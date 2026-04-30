<?php

namespace App\Console\Commands;

use App\Models\Article;
use App\Models\ContentSyndication;
use App\Models\EmailSubscriber;
use App\Models\SocialPost;
use App\Models\User;
use App\Services\ContentSyndicationService;
use App\Services\SocialCaptionService;
use App\Services\SocialPostingService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class DistributionEngineCommand extends Command
{
    protected $signature = 'seo:distribute
        {--syndicate : Run content syndication}
        {--captions : Generate social captions}
        {--social : Post to social media platforms}
        {--push : Send push notification for latest article}
        {--all : Run all distribution tasks}
        {--limit=5 : Max articles per task}';

    protected $description = 'SEO Distribution Engine — syndicate, post social, notify, and amplify content';

    public function handle(
        ContentSyndicationService $syndicationService,
        SocialCaptionService $captionService,
        SocialPostingService $socialPostingService
    ): int {
        $this->info('🚀 SEO Distribution Engine');
        $this->info('━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->newLine();

        $this->showStats();

        $runAll = $this->option('all');
        $limit = (int) $this->option('limit');

        // 1. Content Syndication
        if ($runAll || $this->option('syndicate')) {
            $this->newLine();
            $this->info('📤 Content Syndication');
            $results = $syndicationService->syndicateBatch($limit);
            $count = count($results);
            $this->line("   Syndicated {$count} articles to external platforms");
            foreach ($results as $articleId => $platforms) {
                $article = Article::find($articleId);
                $statuses = collect($platforms)->pluck('status')->countBy();
                $this->line("   📄 {$article?->title}: ".$statuses->map(fn ($c, $s) => "{$s}({$c})")->implode(', '));
            }
        }

        // 2. Social Captions
        if ($runAll || $this->option('captions')) {
            $this->newLine();
            $this->info('📱 Social Caption Generation');
            $articles = Article::where('status', 'published')
                ->orderBy('published_at', 'desc')
                ->limit($limit * 3)
                ->get()
                ->filter(fn ($a) => ! Cache::has("social_captions:{$a->id}"))
                ->take($limit);

            $generated = 0;
            foreach ($articles as $article) {
                $captions = $captionService->generateAll($article);
                Cache::put("social_captions:{$article->id}", $captions, now()->addDays(7));
                $generated++;
                $this->line("   ✅ {$article->title} — ".count($captions).' platform captions');
            }
            $this->line("   Generated captions for {$generated} articles");
        }

        // 3. Social Media Posting
        if ($runAll || $this->option('social')) {
            $this->newLine();
            $this->info('📱 Social Media Posting');

            $articles = Article::where('status', 'published')
                ->whereNotNull('published_at')
                ->where('published_at', '>=', now()->subDays(30))
                ->whereDoesntHave('socialPosts', fn ($q) => $q->where('status', 'posted'))
                ->orderByDesc('published_at')
                ->limit($limit)
                ->get();

            $totalPosted = 0;
            $totalPending = 0;

            foreach ($articles as $article) {
                $results = $socialPostingService->postToAll($article);
                $posted = collect($results)->where('status', 'posted')->count();
                $pending = collect($results)->where('status', 'pending')->count();
                $totalPosted += $posted;
                $totalPending += $pending;

                $platformStatus = collect($results)->map(fn ($r) => $r['status'])->implode(', ');
                $this->line("   📄 {$article->title}: {$platformStatus}");
            }

            $this->line("   Posted: {$totalPosted} | Pending: {$totalPending}");

            // Also process any scheduled posts that are due
            $scheduled = $socialPostingService->processScheduledPosts();
            if ($scheduled > 0) {
                $this->line("   ⏰ Processed {$scheduled} scheduled posts");
            }
        }

        // 4. Push Notification (latest unpushed)
        if ($runAll || $this->option('push')) {
            $this->newLine();
            $this->info('🔔 Push Notifications');
            $subscribers = User::whereHas('pushSubscriptions')->count();
            $this->line("   Push subscribers: {$subscribers}");

            $latest = Article::where('status', 'published')
                ->orderBy('published_at', 'desc')
                ->first();

            if ($latest && $subscribers > 0) {
                $cacheKey = "push_sent:{$latest->id}";
                if (! Cache::has($cacheKey)) {
                    $users = User::whereHas('pushSubscriptions')->get();
                    \Illuminate\Support\Facades\Notification::send(
                        $users,
                        new \App\Notifications\NewArticleNotification($latest)
                    );
                    Cache::put($cacheKey, true, now()->addDays(1));
                    $this->line("   ✅ Push sent: {$latest->title} → {$subscribers} subscribers");
                } else {
                    $this->line("   ⏭️  Latest article already pushed: {$latest->title}");
                }
            }
        }

        $this->newLine();
        $this->info('✅ Distribution Engine complete');

        return self::SUCCESS;
    }

    protected function showStats(): void
    {
        $this->info('📊 Distribution Status');
        $this->table(
            ['Metric', 'Value'],
            [
                ['Published Articles', Article::where('status', 'published')->count()],
                ['Syndicated', ContentSyndication::where('status', 'published')->count()],
                ['Pending Syndication', ContentSyndication::where('status', 'pending')->count()],
                ['Failed Syndication', ContentSyndication::where('status', 'failed')->count()],
                ['Social Posts (Posted)', SocialPost::where('status', 'posted')->count()],
                ['Social Posts (Pending)', SocialPost::where('status', 'pending')->count()],
                ['Social Posts (Scheduled)', SocialPost::where('status', 'scheduled')->count()],
                ['Push Subscribers', DB::table('push_subscriptions')->count()],
                ['Email Subscribers', EmailSubscriber::active()->count()],
                ['Cached Social Captions', $this->countCachedCaptions()],
            ]
        );
    }

    protected function countCachedCaptions(): int
    {
        // Count articles with cached captions
        $count = 0;
        Article::where('status', 'published')
            ->orderBy('published_at', 'desc')
            ->limit(50)
            ->get()
            ->each(function ($article) use (&$count) {
                if (Cache::has("social_captions:{$article->id}")) {
                    $count++;
                }
            });

        return $count;
    }
}
