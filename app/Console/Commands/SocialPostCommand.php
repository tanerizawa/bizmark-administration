<?php

namespace App\Console\Commands;

use App\Models\Article;
use App\Models\SocialPost;
use App\Services\SocialCaptionService;
use App\Services\SocialPostingService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SocialPostCommand extends Command
{
    protected $signature = 'content:social-post
        {--article= : Post specific article by ID}
        {--limit=3 : Number of articles to post}
        {--platform= : Post to specific platform only}
        {--schedule : Schedule posts at optimal times instead of posting immediately}
        {--process-scheduled : Process scheduled posts that are due}';

    protected $description = 'Post articles to social media platforms (Telegram, LinkedIn, Twitter, Facebook, GBP)';

    public function handle(SocialPostingService $service): int
    {
        try {
            if ($this->option('process-scheduled')) {
                return $this->processScheduled($service);
            }

            if ($articleId = $this->option('article')) {
                $article = Article::find($articleId);
                if (! $article) {
                    $this->error("Article #{$articleId} not found.");

                    return 1;
                }

                return $this->postArticle($service, $article);
            }

            return $this->postBatch($service);
        } catch (\Exception $e) {
            Log::error('Social post command failed: '.$e->getMessage());
            $this->error('Social posting failed: '.$e->getMessage());

            return 1;
        }
    }

    protected function postArticle(SocialPostingService $service, Article $article): int
    {
        $this->info("📱 Posting: {$article->title}");

        if ($this->option('schedule')) {
            $scheduled = $service->scheduleForArticle($article);
            foreach ($scheduled as $platform => $time) {
                $this->line("  ⏰ {$platform}: scheduled for {$time}");
            }
            $this->info('Scheduled '.count($scheduled).' posts.');

            return 0;
        }

        $platform = $this->option('platform');
        if ($platform) {
            $caption = app(SocialCaptionService::class)->generateFor($article, $platform);
            $result = $service->postToPlatform($article, $platform, $caption ?? '');
            $this->outputResult($platform, $result);

            return 0;
        }

        $results = $service->postToAll($article);
        foreach ($results as $platform => $result) {
            $this->outputResult($platform, $result);
        }

        $posted = collect($results)->where('status', 'posted')->count();
        $pending = collect($results)->where('status', 'pending')->count();
        $this->newLine();
        $this->info("✅ Posted: {$posted} | ⏳ Pending: {$pending}");

        return 0;
    }

    protected function postBatch(SocialPostingService $service): int
    {
        $limit = (int) $this->option('limit');
        $platform = $this->option('platform');

        // Get recent articles not yet posted to any social platform
        $articles = Article::where('status', 'published')
            ->whereNotNull('published_at')
            ->where('published_at', '>=', now()->subDays(30))
            ->whereDoesntHave('socialPosts', function ($q) {
                $q->where('status', 'posted');
            })
            ->orderByDesc('published_at')
            ->limit($limit)
            ->get();

        if ($articles->isEmpty()) {
            $this->info('No articles need social posting.');

            return 0;
        }

        $this->info("📱 Social posting {$articles->count()} articles...");
        $this->newLine();

        $totalPosted = 0;
        $totalPending = 0;

        foreach ($articles as $article) {
            $this->info("→ {$article->title}");

            if ($this->option('schedule')) {
                $scheduled = $service->scheduleForArticle($article);
                foreach ($scheduled as $platform => $time) {
                    $this->line("  ⏰ {$platform}: {$time}");
                }

                continue;
            }

            if ($platform) {
                $caption = app(SocialCaptionService::class)->generateFor($article, $platform);
                $results = [
                    $platform => $service->postToPlatform($article, $platform, $caption ?? ''),
                ];
            } else {
                $results = $service->postToAll($article);
            }

            foreach ($results as $platform => $result) {
                $this->outputResult($platform, $result);
            }

            $totalPosted += collect($results)->where('status', 'posted')->count();
            $totalPending += collect($results)->where('status', 'pending')->count();
            $this->newLine();
        }

        $this->info("🎯 Total — Posted: {$totalPosted} | Pending: {$totalPending}");

        return 0;
    }

    protected function processScheduled(SocialPostingService $service): int
    {
        $this->info('Processing scheduled social posts...');

        $due = SocialPost::scheduled()->count();
        if ($due === 0) {
            $this->info('No scheduled posts due.');

            return 0;
        }

        $processed = $service->processScheduledPosts();
        $this->info("✅ Processed {$processed}/{$due} scheduled posts.");

        return 0;
    }

    protected function outputResult(string $platform, array $result): void
    {
        $status = $result['status'] ?? 'unknown';
        $icon = match ($status) {
            'posted' => '✅',
            'pending' => '⏳',
            'failed' => '❌',
            'skipped' => '⏭️',
            default => '❓',
        };

        $extra = '';
        if ($url = ($result['platform_url'] ?? null)) {
            $extra = " → {$url}";
        }
        if ($error = ($result['error'] ?? null)) {
            $extra = " ({$error})";
        }

        $this->line("  {$icon} {$platform}: {$status}{$extra}");
    }
}
