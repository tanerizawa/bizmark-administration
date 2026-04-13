<?php

namespace App\Services;

use App\Models\Article;
use App\Models\ContentSyndication;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ContentSyndicationService
{
    protected OpenRouterService $ai;

    public function __construct(OpenRouterService $ai)
    {
        $this->ai = $ai;
    }

    /**
     * Syndicate article to all enabled platforms
     */
    public function syndicateArticle(Article $article): array
    {
        $results = [];
        $canonicalUrl = config('app.url') . '/blog/' . $article->slug;

        // Medium
        if (config('services.medium.token')) {
            $results['medium'] = $this->syndicateToMedium($article, $canonicalUrl);
        }

        // Dev.to
        if (config('services.devto.api_key')) {
            $results['devto'] = $this->syndicateToDevTo($article, $canonicalUrl);
        }

        // LinkedIn Articles  
        if (config('services.linkedin.access_token')) {
            $results['linkedin'] = $this->syndicateToLinkedIn($article, $canonicalUrl);
        }

        // If no external APIs configured, create syndication records for manual posting
        if (empty($results)) {
            $results = $this->createManualSyndicationRecords($article);
        }

        return $results;
    }

    /**
     * Syndicate batch of recent articles
     */
    public function syndicateBatch(int $limit = 5): array
    {
        $articles = Article::where('status', 'published')
            ->whereDoesntHave('syndications')
            ->where('published_at', '>=', now()->subDays(30))
            ->orderBy('views_count', 'desc')
            ->limit($limit)
            ->get();

        $results = [];
        foreach ($articles as $article) {
            $results[$article->id] = $this->syndicateArticle($article);
        }

        return $results;
    }

    /**
     * Generate syndication-optimized summary using AI
     */
    public function generateSyndicationSummary(Article $article, string $platform): string
    {
        $maxLength = match ($platform) {
            'medium' => 5000,
            'linkedin' => 1300,
            'devto' => 5000,
            default => 2000,
        };

        $content = Str::limit(strip_tags($article->content), 3000);
        $canonicalUrl = config('app.url') . '/blog/' . $article->slug;

        $prompt = <<<PROMPT
Tulis ulang artikel berikut untuk platform {$platform} dalam Bahasa Indonesia.
Max {$maxLength} karakter. Harus original (bukan copy-paste).

Judul: {$article->title}
Konten: {$content}

Rules:
- Tulis sebagai ringkasan/insight, BUKAN copy artikel
- Akhiri dengan "📖 Artikel lengkap: {$canonicalUrl}" sebagai canonical reference
- {$platform} format: gunakan heading, bullet points
- Tambahkan opening hook yang menarik
- Jangan sertakan metadata/JSON, hanya teks artikel

Respond with the article text only (no JSON wrapping).
PROMPT;

        $response = $this->ai->chat([
            ['role' => 'system', 'content' => "Kamu adalah content writer profesional untuk platform {$platform}."],
            ['role' => 'user', 'content' => $prompt],
        ], [
            'model' => 'google/gemini-2.5-flash',
            'temperature' => 0.6,
            'max_tokens' => 2000,
        ]);

        if ($response['success']) {
            return $response['content'];
        }

        // Fallback: simple summary
        $excerpt = $article->excerpt ?: Str::limit(strip_tags($article->content), 500);
        return "## {$article->title}\n\n{$excerpt}\n\n📖 Artikel lengkap: {$canonicalUrl}";
    }

    /**
     * Syndicate to Medium via API
     */
    protected function syndicateToMedium(Article $article, string $canonicalUrl): array
    {
        $token = config('services.medium.token');
        $record = $this->getOrCreateRecord($article, 'medium');

        try {
            // Get user ID first
            $userResponse = Http::withToken($token)
                ->get('https://api.medium.com/v1/me');

            if (!$userResponse->successful()) {
                return $this->markFailed($record, 'Failed to get Medium user: ' . $userResponse->status());
            }

            $userId = $userResponse->json('data.id');
            $content = $this->generateSyndicationSummary($article, 'medium');

            $response = Http::withToken($token)
                ->post("https://api.medium.com/v1/users/{$userId}/posts", [
                    'title' => $article->title,
                    'contentFormat' => 'markdown',
                    'content' => $content,
                    'canonicalUrl' => $canonicalUrl,
                    'tags' => array_slice($article->tags ?? ['perizinan', 'bisnis', 'lingkungan'], 0, 5),
                    'publishStatus' => 'public',
                ]);

            if ($response->successful()) {
                $data = $response->json('data');
                return $this->markPublished($record, $data['url'] ?? '');
            }

            return $this->markFailed($record, 'Medium API error: ' . $response->status());
        } catch (\Exception $e) {
            return $this->markFailed($record, $e->getMessage());
        }
    }

    /**
     * Syndicate to Dev.to via API
     */
    protected function syndicateToDevTo(Article $article, string $canonicalUrl): array
    {
        $apiKey = config('services.devto.api_key');
        $record = $this->getOrCreateRecord($article, 'devto');

        try {
            $content = $this->generateSyndicationSummary($article, 'devto');

            $response = Http::withHeaders(['api-key' => $apiKey])
                ->post('https://dev.to/api/articles', [
                    'article' => [
                        'title' => $article->title,
                        'body_markdown' => $content,
                        'published' => true,
                        'canonical_url' => $canonicalUrl,
                        'tags' => array_slice(
                            array_map(fn ($t) => Str::slug($t, ''), $article->tags ?? ['perizinan', 'bisnis']),
                            0, 4
                        ),
                    ],
                ]);

            if ($response->successful()) {
                $data = $response->json();
                return $this->markPublished($record, $data['url'] ?? '');
            }

            return $this->markFailed($record, 'Dev.to API error: ' . $response->status());
        } catch (\Exception $e) {
            return $this->markFailed($record, $e->getMessage());
        }
    }

    /**
     * Syndicate to LinkedIn Articles
     */
    protected function syndicateToLinkedIn(Article $article, string $canonicalUrl): array
    {
        $token = config('services.linkedin.access_token');
        $orgId = config('services.linkedin.organization_id');
        $record = $this->getOrCreateRecord($article, 'linkedin');

        try {
            $summary = Str::limit(strip_tags($article->content), 700);

            $response = Http::withToken($token)
                ->post('https://api.linkedin.com/v2/ugcPosts', [
                    'author' => "urn:li:organization:{$orgId}",
                    'lifecycleState' => 'PUBLISHED',
                    'specificContent' => [
                        'com.linkedin.ugc.ShareContent' => [
                            'shareCommentary' => ['text' => "📄 {$article->title}\n\n{$summary}"],
                            'shareMediaCategory' => 'ARTICLE',
                            'media' => [[
                                'status' => 'READY',
                                'originalUrl' => $canonicalUrl,
                                'title' => ['text' => $article->title],
                                'description' => ['text' => $article->excerpt ?: Str::limit(strip_tags($article->content), 200)],
                            ]],
                        ],
                    ],
                    'visibility' => ['com.linkedin.ugc.MemberNetworkVisibility' => 'PUBLIC'],
                ]);

            if ($response->successful()) {
                return $this->markPublished($record, $canonicalUrl);
            }

            return $this->markFailed($record, 'LinkedIn API error: ' . $response->status());
        } catch (\Exception $e) {
            return $this->markFailed($record, $e->getMessage());
        }
    }

    /**
     * Create manual syndication records when no APIs configured
     */
    protected function createManualSyndicationRecords(Article $article): array
    {
        $platforms = ['medium', 'linkedin', 'devto'];
        $results = [];

        foreach ($platforms as $platform) {
            $existing = ContentSyndication::where('article_id', $article->id)
                ->where('platform', $platform)
                ->first();

            if (!$existing) {
                ContentSyndication::create([
                    'article_id' => $article->id,
                    'platform' => $platform,
                    'status' => 'pending',
                    'metrics' => ['auto_content' => true],
                ]);
                $results[$platform] = ['status' => 'pending', 'message' => 'Queued for manual syndication'];
            }
        }

        return $results;
    }

    /**
     * Get or create syndication record
     */
    protected function getOrCreateRecord(Article $article, string $platform): ContentSyndication
    {
        return ContentSyndication::firstOrCreate(
            ['article_id' => $article->id, 'platform' => $platform],
            ['status' => 'pending']
        );
    }

    protected function markPublished(ContentSyndication $record, string $url): array
    {
        $record->update([
            'status' => 'published',
            'platform_url' => $url,
            'published_at' => now(),
        ]);

        Log::info('Content syndicated', [
            'article' => $record->article_id,
            'platform' => $record->platform,
            'url' => $url,
        ]);

        return ['status' => 'published', 'url' => $url];
    }

    protected function markFailed(ContentSyndication $record, string $error): array
    {
        $record->update([
            'status' => 'failed',
            'error_message' => Str::limit($error, 255),
        ]);

        Log::warning('Content syndication failed', [
            'article' => $record->article_id,
            'platform' => $record->platform,
            'error' => $error,
        ]);

        return ['status' => 'failed', 'error' => $error];
    }
}
