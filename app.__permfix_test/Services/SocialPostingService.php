<?php

namespace App\Services;

use App\Models\Article;
use App\Models\SocialPost;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class SocialPostingService
{
    protected SocialCaptionService $captionService;

    public function __construct(SocialCaptionService $captionService)
    {
        $this->captionService = $captionService;
    }

    /**
     * Post article to all configured social platforms
     */
    public function postToAll(Article $article): array
    {
        $results = [];
        $platforms = $this->getActivePlatforms();

        if (empty($platforms)) {
            Log::info('No social platforms configured, skipping pending record creation', [
                'article' => $article->id,
            ]);

            return [];
        }

        // Generate captions for all platforms
        $captions = $this->captionService->generateAll($article);

        foreach ($platforms as $platform) {
            // Skip if already posted
            if ($this->alreadyPosted($article, $platform)) {
                $results[$platform] = ['status' => 'skipped', 'reason' => 'already_posted'];

                continue;
            }

            $caption = $captions[$platform] ?? $this->getDefaultCaption($article, $platform);
            $results[$platform] = $this->postToPlatform($article, $platform, $caption);
        }

        return $results;
    }

    /**
     * Post to a specific platform
     */
    public function postToPlatform(Article $article, string $platform, string $caption): array
    {
        $method = 'postTo'.ucfirst($platform);

        if (! method_exists($this, $method)) {
            return $this->createRecord($article, $platform, $caption, 'pending', 'Platform posting not implemented');
        }

        try {
            $result = $this->{$method}($article, $caption);

            return $result;
        } catch (\Exception $e) {
            Log::error("Social posting to {$platform} failed", [
                'article' => $article->id,
                'error' => $e->getMessage(),
            ]);

            return $this->createRecord($article, $platform, $caption, 'failed', $e->getMessage());
        }
    }

    /**
     * Process scheduled posts that are due
     */
    public function processScheduledPosts(): int
    {
        $posts = SocialPost::scheduled()->with('article')->get();
        $processed = 0;

        foreach ($posts as $post) {
            if (! $post->article) {
                $post->update(['status' => 'failed', 'error_message' => 'Article deleted']);

                continue;
            }

            $result = $this->postToPlatform($post->article, $post->platform, $post->caption);

            if (($result['status'] ?? '') === 'posted') {
                $post->update([
                    'status' => 'posted',
                    'posted_at' => now(),
                    'platform_post_id' => $result['platform_post_id'] ?? null,
                    'platform_url' => $result['platform_url'] ?? null,
                ]);
                $processed++;
            }
        }

        return $processed;
    }

    /**
     * Schedule posts for an article with optimal timing
     */
    public function scheduleForArticle(Article $article): array
    {
        $platforms = $this->getActivePlatforms();
        $captions = $this->captionService->generateAll($article);
        $scheduled = [];

        foreach ($platforms as $platform) {
            if ($this->alreadyPosted($article, $platform)) {
                continue;
            }

            $caption = $captions[$platform] ?? $this->getDefaultCaption($article, $platform);
            $scheduledTime = $this->captionService->getNextOptimalTime($platform);

            SocialPost::create([
                'article_id' => $article->id,
                'platform' => $platform,
                'caption' => $caption,
                'status' => 'scheduled',
                'scheduled_for' => $scheduledTime,
            ]);

            $scheduled[$platform] = $scheduledTime;
        }

        return $scheduled;
    }

    // ── Platform Implementations ─────────────────────────────

    /**
     * Post to Telegram channel via Bot API
     */
    protected function postToTelegram(Article $article, string $caption): array
    {
        $botToken = config('services.telegram.bot_token');
        $channelId = config('services.telegram.channel_id');

        if (! $botToken || ! $channelId) {
            return $this->createRecord($article, 'telegram', $caption, 'pending', 'Telegram bot token or channel ID not configured');
        }

        $url = "https://api.telegram.org/bot{$botToken}/sendMessage";

        $response = Http::post($url, [
            'chat_id' => $channelId,
            'text' => $caption,
            'parse_mode' => 'HTML',
            'disable_web_page_preview' => false,
        ]);

        if ($response->successful() && $response->json('ok')) {
            $messageId = $response->json('result.message_id');
            $chatId = ltrim((string) $channelId, '@');

            return $this->createRecord($article, 'telegram', $caption, 'posted', null, [
                'platform_post_id' => (string) $messageId,
                'platform_url' => "https://t.me/{$chatId}/{$messageId}",
            ]);
        }

        $error = $response->json('description', 'Unknown Telegram API error');

        return $this->createRecord($article, 'telegram', $caption, 'failed', $error);
    }

    /**
     * Post to LinkedIn company page
     */
    protected function postToLinkedin(Article $article, string $caption): array
    {
        $accessToken = config('services.linkedin.access_token');
        $orgId = config('services.linkedin.organization_id');

        if (! $accessToken || ! $orgId) {
            return $this->createRecord($article, 'linkedin', $caption, 'pending', 'LinkedIn credentials not configured');
        }

        $articleUrl = config('app.url').'/blog/'.$article->slug;

        $response = Http::withToken($accessToken)
            ->post('https://api.linkedin.com/v2/ugcPosts', [
                'author' => "urn:li:organization:{$orgId}",
                'lifecycleState' => 'PUBLISHED',
                'specificContent' => [
                    'com.linkedin.ugc.ShareContent' => [
                        'shareCommentary' => ['text' => $caption],
                        'shareMediaCategory' => 'ARTICLE',
                        'media' => [[
                            'status' => 'READY',
                            'originalUrl' => $articleUrl,
                            'title' => ['text' => $article->title],
                            'description' => ['text' => Str::limit(strip_tags($article->content), 200)],
                        ]],
                    ],
                ],
                'visibility' => [
                    'com.linkedin.ugc.MemberNetworkVisibility' => 'PUBLIC',
                ],
            ]);

        if ($response->successful()) {
            $postId = $response->json('id', '');

            return $this->createRecord($article, 'linkedin', $caption, 'posted', null, [
                'platform_post_id' => $postId,
                'platform_url' => "https://www.linkedin.com/feed/update/{$postId}",
            ]);
        }

        return $this->createRecord($article, 'linkedin', $caption, 'failed', $response->body());
    }

    /**
     * Post to Twitter/X via API v2
     */
    protected function postToTwitter(Article $article, string $caption): array
    {
        $apiKey = config('services.twitter.api_key');
        $apiSecret = config('services.twitter.api_secret');
        $accessToken = config('services.twitter.access_token');
        $accessTokenSecret = config('services.twitter.access_token_secret');

        if (! $accessToken || ! $apiKey || ! $apiSecret || ! $accessTokenSecret) {
            return $this->createRecord($article, 'twitter', $caption, 'pending', 'Twitter API credentials not configured');
        }

        $url = 'https://api.twitter.com/2/tweets';
        $payload = ['text' => Str::limit($caption, 280)];

        // Twitter v2 accepts JSON bodies. OAuth 1.0a signature for JSON requests
        // should include only OAuth params + query string, not JSON body fields.
        $oauth = $this->buildOAuthHeader($url, 'POST', [], $apiKey, $apiSecret, $accessToken, $accessTokenSecret);

        $response = Http::withHeaders([
            'Authorization' => $oauth,
            'Content-Type' => 'application/json',
        ])
            ->asJson()
            ->post($url, $payload);

        if ($response->successful()) {
            $tweetId = $response->json('data.id');

            return $this->createRecord($article, 'twitter', $caption, 'posted', null, [
                'platform_post_id' => $tweetId,
                'platform_url' => "https://twitter.com/i/web/status/{$tweetId}",
            ]);
        }

        if ($this->isTwitterCreditBlocked($response->json())) {
            return $this->createRecord($article, 'twitter', $caption, 'skipped', 'Twitter credits depleted; retry after account recharge');
        }

        return $this->createRecord($article, 'twitter', $caption, 'failed', $response->body());
    }

    /**
     * Post to Facebook page
     */
    protected function postToFacebook(Article $article, string $caption): array
    {
        $pageToken = config('services.facebook.page_access_token');
        $pageId = config('services.facebook.page_id');

        if (! $pageToken || ! $pageId) {
            return $this->createRecord($article, 'facebook', $caption, 'pending', 'Facebook page credentials not configured');
        }

        $articleUrl = config('app.url').'/blog/'.$article->slug;

        $response = Http::post("https://graph.facebook.com/v19.0/{$pageId}/feed", [
            'message' => $caption,
            'link' => $articleUrl,
            'access_token' => $pageToken,
        ]);

        if ($response->successful() && $response->json('id')) {
            $postId = $response->json('id');

            return $this->createRecord($article, 'facebook', $caption, 'posted', null, [
                'platform_post_id' => $postId,
                'platform_url' => "https://www.facebook.com/{$postId}",
            ]);
        }

        return $this->createRecord($article, 'facebook', $caption, 'failed', $response->json('error.message', $response->body()));
    }

    /**
     * Get a fresh GBP access token via OAuth2 refresh token flow.
     * Token is cached for 55 minutes to avoid unnecessary refreshes.
     */
    protected function getGbpAccessToken(): ?string
    {
        $refreshToken = config('services.gbp.refresh_token');
        if (! $refreshToken) {
            return null;
        }

        return Cache::remember('gbp_access_token', 55 * 60, function () use ($refreshToken) {
            $response = Http::asForm()->post('https://oauth2.googleapis.com/token', [
                'client_id' => config('services.google.client_id'),
                'client_secret' => config('services.google.client_secret'),
                'refresh_token' => $refreshToken,
                'grant_type' => 'refresh_token',
            ]);

            if ($response->successful()) {
                return $response->json('access_token');
            }

            \Log::warning('GBP token refresh failed', ['status' => $response->status(), 'body' => $response->body()]);

            return null;
        });
    }

    /**
     * Post to Google Business Profile
     */
    protected function postToGbp(Article $article, string $caption): array
    {
        $accessToken = $this->getGbpAccessToken();
        $locationId = config('services.gbp.location_id');

        if (! $accessToken || ! $locationId) {
            return $this->createRecord($article, 'gbp', $caption, 'pending', 'GBP credentials not configured');
        }

        $articleUrl = config('app.url').'/blog/'.$article->slug;

        // Google Business Profile Local Posts API
        // Requires "My Business Business Information API" enabled in Google Cloud Console
        $response = Http::withToken($accessToken)
            ->post("https://mybusiness.googleapis.com/v4/accounts/-/locations/{$locationId}/localPosts", [
                'topicType' => 'STANDARD',
                'languageCode' => 'id',
                'summary' => Str::limit($caption, 1500),
                'callToAction' => [
                    'actionType' => 'LEARN_MORE',
                    'url' => $articleUrl,
                ],
            ]);

        if ($response->successful()) {
            $postName = $response->json('name', '');

            return $this->createRecord($article, 'gbp', $caption, 'posted', null, [
                'platform_post_id' => $postName,
            ]);
        }

        if ($this->isGbpTemporarilyBlocked($response->json())) {
            return $this->createRecord($article, 'gbp', $caption, 'skipped', 'GBP temporarily unavailable; retry after Google service/quota is restored');
        }

        return $this->createRecord($article, 'gbp', $caption, 'failed', $response->body());
    }

    // ── Helpers ──────────────────────────────────────────────

    protected function getActivePlatforms(): array
    {
        $platforms = [];
        $freeOnly = (bool) config('services.social_posting.free_only', true);

        if (config('services.telegram.bot_token') && config('services.telegram.channel_id')) {
            $platforms[] = 'telegram';
        }
        if (config('services.linkedin.access_token') && config('services.linkedin.organization_id')) {
            $platforms[] = 'linkedin';
        }
        if (! $freeOnly && config('services.twitter.access_token') && config('services.twitter.api_key')) {
            $platforms[] = 'twitter';
        }
        if (config('services.facebook.page_access_token') && config('services.facebook.page_id')) {
            $platforms[] = 'facebook';
        }
        if (! $freeOnly && config('services.gbp.refresh_token') && config('services.gbp.location_id')) {
            $platforms[] = 'gbp';
        }

        return $platforms;
    }

    protected function alreadyPosted(Article $article, string $platform): bool
    {
        return SocialPost::where('article_id', $article->id)
            ->where('platform', $platform)
            ->whereIn('status', ['posted', 'scheduled'])
            ->exists();
    }

    protected function createPendingRecords(Article $article): array
    {
        $captions = $this->captionService->generateAll($article);
        $results = [];

        foreach (['telegram', 'linkedin', 'twitter', 'facebook', 'gbp'] as $platform) {
            if ($this->alreadyPosted($article, $platform)) {
                $results[$platform] = ['status' => 'skipped'];

                continue;
            }

            $caption = $captions[$platform] ?? $this->getDefaultCaption($article, $platform);
            $results[$platform] = $this->createRecord($article, $platform, $caption, 'pending', 'API credentials not configured');
        }

        return $results;
    }

    protected function createRecord(Article $article, string $platform, string $caption, string $status, ?string $error = null, array $extra = []): array
    {
        $post = SocialPost::updateOrCreate(
            ['article_id' => $article->id, 'platform' => $platform],
            array_filter([
                'caption' => $caption,
                'status' => $status,
                'posted_at' => $status === 'posted' ? now() : null,
                'platform_post_id' => $extra['platform_post_id'] ?? null,
                'platform_url' => $extra['platform_url'] ?? null,
                'error_message' => $error,
            ], fn ($v) => $v !== null)
        );

        return [
            'status' => $status,
            'post_id' => $post->id,
            'platform_post_id' => $extra['platform_post_id'] ?? null,
            'platform_url' => $extra['platform_url'] ?? null,
            'error' => $error,
        ];
    }

    protected function isTwitterCreditBlocked(array $payload): bool
    {
        return ($payload['title'] ?? null) === 'CreditsDepleted';
    }

    protected function isGbpTemporarilyBlocked(array $payload): bool
    {
        $error = $payload['error'] ?? [];
        $status = $error['status'] ?? null;
        $message = strtolower((string) ($error['message'] ?? ''));

        if ($status === 'RESOURCE_EXHAUSTED') {
            return true;
        }

        if ($status === 'PERMISSION_DENIED' && str_contains($message, 'disabled')) {
            return true;
        }

        return str_contains($message, 'quota');
    }

    protected function getDefaultCaption(Article $article, string $platform): string
    {
        $url = config('app.url').'/blog/'.$article->slug;
        $title = $article->title;
        $excerpt = Str::limit(strip_tags($article->content), 200);

        return match ($platform) {
            'telegram' => "<b>{$title}</b>\n\n{$excerpt}\n\n🔗 {$url}",
            'twitter' => Str::limit("{$title}\n\n{$excerpt}\n\n{$url}", 280),
            'gbp' => "{$title}\n\n{$excerpt}\n\nBaca selengkapnya di bizmark.id",
            default => "{$title}\n\n{$excerpt}\n\n🔗 {$url}",
        };
    }

    /**
     * Build OAuth 1.0a header for Twitter API
     */
    protected function buildOAuthHeader(string $url, string $method, array $params, string $consumerKey, string $consumerSecret, string $token, string $tokenSecret): string
    {
        $oauth = [
            'oauth_consumer_key' => $consumerKey,
            'oauth_nonce' => Str::random(32),
            'oauth_signature_method' => 'HMAC-SHA1',
            'oauth_timestamp' => (string) time(),
            'oauth_token' => $token,
            'oauth_version' => '1.0',
        ];

        $base = array_merge($oauth, $params);
        ksort($base);
        $baseString = strtoupper($method).'&'.rawurlencode($url).'&'.rawurlencode(http_build_query($base, '', '&', PHP_QUERY_RFC3986));
        $signingKey = rawurlencode($consumerSecret).'&'.rawurlencode($tokenSecret);
        $oauth['oauth_signature'] = base64_encode(hash_hmac('sha1', $baseString, $signingKey, true));

        $parts = [];
        foreach ($oauth as $k => $v) {
            $parts[] = rawurlencode($k).'="'.rawurlencode($v).'"';
        }

        return 'OAuth '.implode(', ', $parts);
    }

    /**
     * Get posting summary for an article
     */
    public function getSummary(Article $article): array
    {
        $posts = SocialPost::where('article_id', $article->id)->get();

        return [
            'article_id' => $article->id,
            'title' => $article->title,
            'total_posts' => $posts->count(),
            'posted' => $posts->where('status', 'posted')->count(),
            'pending' => $posts->where('status', 'pending')->count(),
            'failed' => $posts->where('status', 'failed')->count(),
            'platforms' => $posts->mapWithKeys(fn ($p) => [$p->platform => $p->status])->toArray(),
        ];
    }
}
