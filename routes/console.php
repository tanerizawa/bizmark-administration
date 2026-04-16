<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule interview reminders (sent 24 hours before interview)
Schedule::command('interviews:send-reminders')
    ->dailyAt('09:00')
    ->timezone('Asia/Jakarta')
    ->withoutOverlapping();

// ========================================
// Auto-Post Article Scheduler
// ========================================

// Daily at 23:30 - Replenish topic pool if running low (before scheduling)
// Increased threshold and count for aggressive content velocity
Schedule::command('topics:replenish --threshold=30 --count=50')
    ->dailyAt('23:30')
    ->timezone('Asia/Jakarta')
    ->withoutOverlapping()
    ->onSuccess(function () {
        \Log::info('✅ Topic pool replenishment check completed');
    })
    ->onFailure(function () {
        \Log::error('❌ Topic pool replenishment failed');
    });

// Daily at midnight - Schedule posts for the day
Schedule::command('articles:schedule-daily')
    ->dailyAt('00:01')
    ->timezone('Asia/Jakarta')
    ->withoutOverlapping()
    ->onSuccess(function () {
        \Log::info('✅ Daily article scheduling completed');
    })
    ->onFailure(function () {
        \Log::error('❌ Daily article scheduling failed');
    });

// Every 15 minutes - Check and process pending schedules
Schedule::command('articles:process-pending')
    ->everyFifteenMinutes()
    ->withoutOverlapping()
    ->runInBackground();

// Every hour - Backfill missing featured images via Pexels for newly generated articles
Schedule::command('articles:backfill-images --limit=8')
    ->hourly()
    ->withoutOverlapping()
    ->runInBackground();

// Every 15 minutes - Repair storage/cache permissions to avoid Blade compile permission errors
Schedule::call(function () {
    $directories = [
        storage_path('logs'),
        storage_path('framework'),
        storage_path('framework/views'),
        storage_path('framework/cache'),
        storage_path('framework/sessions'),
        base_path('bootstrap/cache'),
    ];

    foreach ($directories as $dir) {
        if (!is_dir($dir)) {
            continue;
        }

        @chmod($dir, 0775);

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dir, \FilesystemIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($iterator as $item) {
            if ($item->isDir()) {
                @chmod($item->getPathname(), 0775);
            } else {
                @chmod($item->getPathname(), 0664);
            }
        }
    }
})
    ->everyFifteenMinutes()
    ->name('fix-storage-permissions');

// Weekly cleanup - Keep last 90 days of logs
Schedule::command('articles:cleanup-logs')
    ->weekly()
    ->sundays()
    ->at('02:00')
    ->timezone('Asia/Jakarta');

// ========================================
// Shapefile Cleanup
// ========================================

// Every 6 hours - Clean up old generated shapefile ZIP files (older than 24h)
Schedule::command('shapefiles:cleanup --hours=24')
    ->everySixHours()
    ->withoutOverlapping()
    ->onSuccess(function () {
        \Log::info('✅ Shapefile cleanup completed');
    });

// ========================================
// SEO Domination Engine v2 — Automation
// ========================================

// ─── INTELLIGENCE LAYER (data collection) ────────────────────

// Weekly Monday 02:00 - Full SEO intelligence pipeline (keywords → clusters → gaps)
Schedule::command('seo:intelligence --queue-gaps=20 --meta-limit=5')
    ->weekly()
    ->mondays()
    ->at('02:00')
    ->timezone('Asia/Jakarta')
    ->withoutOverlapping()
    ->onSuccess(function () {
        \Log::info('✅ SEO intelligence pipeline completed');
    })
    ->onFailure(function () {
        \Log::error('❌ SEO intelligence pipeline failed');
    });

// Daily 03:00 - Queue top content gaps as article topics (feeds auto-post)
Schedule::command('seo:orchestrate --phase=content --queue-gaps=10 --convert-clusters')
    ->dailyAt('03:00')
    ->timezone('Asia/Jakarta')
    ->withoutOverlapping()
    ->onSuccess(function () {
        \Log::info('✅ Content gap queuing completed');
    });

// Weekly Sunday 04:00 - Competitor SERP analysis
Schedule::command('seo:competitor-analyze --limit=15')
    ->weekly()
    ->sundays()
    ->at('04:00')
    ->timezone('Asia/Jakarta')
    ->withoutOverlapping()
    ->onSuccess(function () {
        \Log::info('✅ Competitor analysis completed');
    });

// Daily 05:00 - Position tracking (track keyword rankings via SearXNG)
Schedule::command('seo:track-positions --limit=50')
    ->dailyAt('05:00')
    ->timezone('Asia/Jakarta')
    ->withoutOverlapping()
    ->onSuccess(function () {
        \Log::info('✅ Position tracking completed');
    });

// Daily 05:30 - Trending topic discovery via SearXNG
Schedule::command('seo:trending-topics')
    ->dailyAt('05:30')
    ->timezone('Asia/Jakarta')
    ->withoutOverlapping()
    ->onSuccess(function () {
        \Log::info('✅ Trending topic discovery completed');
    });

// Weekly Sunday 03:00 - Cleanup expired trending topics
Schedule::command('seo:trending-topics --cleanup')
    ->weekly()
    ->sundays()
    ->at('03:00')
    ->timezone('Asia/Jakarta')
    ->withoutOverlapping();

// Daily 06:15 - Convert high-priority trending topics to article topics
Schedule::command('seo:trending-topics --convert --min-score=60 --limit=3')
    ->dailyAt('06:15')
    ->timezone('Asia/Jakarta')
    ->withoutOverlapping()
    ->onSuccess(function () {
        \Log::info('✅ Trending topics converted to article topics');
    });

// ─── OPTIMIZATION LAYER ─────────────────────────────────────

// Weekly Friday 04:00 - AI meta tag optimization
Schedule::command('seo:optimize-meta --limit=10')
    ->weekly()
    ->fridays()
    ->at('04:00')
    ->timezone('Asia/Jakarta')
    ->withoutOverlapping()
    ->onSuccess(function () {
        \Log::info('✅ Meta optimization completed');
    });

// Weekly Wednesday 05:00 - Create A/B tests for top articles
Schedule::command('seo:meta-ab-test --create --limit=3')
    ->weekly()
    ->wednesdays()
    ->at('05:00')
    ->timezone('Asia/Jakarta')
    ->withoutOverlapping();

// Daily 06:00 - Evaluate running A/B tests
Schedule::command('seo:meta-ab-test --evaluate')
    ->dailyAt('06:00')
    ->timezone('Asia/Jakarta')
    ->withoutOverlapping();

// Weekly Thursday 03:00 - Refresh stale content (>90 days)
Schedule::command('seo:refresh-content --older-than=90 --limit=5')
    ->weekly()
    ->thursdays()
    ->at('03:00')
    ->timezone('Asia/Jakarta')
    ->withoutOverlapping()
    ->onSuccess(function () {
        \Log::info('✅ Content refresh completed');
    });

// Weekly Saturday 04:00 - Bidirectional backlink scan (inject cross-links in existing articles)
Schedule::command('seo:backlink-scan --limit=50')
    ->weekly()
    ->saturdays()
    ->at('04:00')
    ->timezone('Asia/Jakarta')
    ->withoutOverlapping()
    ->onSuccess(function () {
        \Log::info('✅ Backlink scan completed');
    });

// Daily 01:00 - Score all unscored articles
Schedule::command('seo:score-articles --limit=20')
    ->dailyAt('01:00')
    ->timezone('Asia/Jakarta')
    ->withoutOverlapping();

// ─── CONTENT VELOCITY BOOSTER ────────────────────────────────

// Every 4 hours - Emergency topic replenish from intelligence pipeline
// When pool drops critically low, run content gap queuing to keep auto-poster fed
Schedule::call(function () {
    $available = \App\Models\ArticleTopic::where('status', 'pending')->count();
    if ($available < 20) {
        \Log::warning("⚠️ Topic pool critically low ({$available}). Running emergency intelligence replenish...");
        \Illuminate\Support\Facades\Artisan::call('seo:orchestrate', [
            '--phase' => 'content',
            '--convert-clusters' => true,
            '--queue-gaps' => 30,
        ]);
        \Log::info('✅ Emergency topic replenish completed. Pool: ' . \App\Models\ArticleTopic::where('status', 'pending')->count());
    }
})
    ->everyFourHours()
    ->timezone('Asia/Jakarta')
    ->name('emergency-topic-replenish')
    ->withoutOverlapping();

// Twice daily - Score newly published articles for quality tracking
Schedule::command('seo:score-articles --limit=20')
    ->dailyAt('12:00')
    ->timezone('Asia/Jakarta')
    ->name('midday-seo-scoring')
    ->withoutOverlapping();

// ─── DISTRIBUTION LAYER ─────────────────────────────────────

// Daily 15:00 - Regenerate sitemap after new content
Schedule::command('sitemap:generate --ping')
    ->dailyAt('15:00')
    ->timezone('Asia/Jakarta')
    ->withoutOverlapping();

// Daily 15:10 - Submit recent articles to IndexNow (increased for velocity)
Schedule::command('seo:index-now --recent=15')
    ->dailyAt('15:10')
    ->timezone('Asia/Jakarta')
    ->withoutOverlapping();

// Daily 20:00 - Second IndexNow submission for afternoon articles
Schedule::command('seo:index-now --recent=10')
    ->dailyAt('20:00')
    ->timezone('Asia/Jakarta')
    ->name('indexnow-evening')
    ->withoutOverlapping();

// Daily 14:00 - Syndicate to external platforms (Medium, Dev.to, LinkedIn)
// Guard in ContentSyndicationService checks for non-empty tokens before posting
Schedule::command('content:syndicate --limit=3')
    ->dailyAt('14:00')
    ->timezone('Asia/Jakarta')
    ->withoutOverlapping()
    ->onSuccess(function () {
        \Log::info('✅ Content syndication completed');
    });

// Daily 14:30 - Post to social media platforms (Telegram, Twitter, Facebook, LinkedIn, GBP)
// Guard in SocialPostingService checks for non-empty tokens before posting
Schedule::command('content:social-post --limit=3')
    ->dailyAt('14:30')
    ->timezone('Asia/Jakarta')
    ->withoutOverlapping()
    ->onSuccess(function () {
        \Log::info('✅ Social media posting completed');
    });

// Every 30 minutes - Process scheduled social posts
Schedule::command('content:social-post --process-scheduled')
    ->everyThirtyMinutes()
    ->timezone('Asia/Jakarta')
    ->name('process-scheduled-social-posts')
    ->withoutOverlapping();

// ─── REPORTING LAYER ────────────────────────────────────────

// Weekly Monday 07:00 - Generate SEO performance report + email
Schedule::command('seo:weekly-report --email')
    ->weekly()
    ->mondays()
    ->at('07:00')
    ->timezone('Asia/Jakarta')
    ->withoutOverlapping()
    ->onSuccess(function () {
        \Log::info('✅ SEO weekly report generated and emailed');
    });

// Weekly Monday 07:30 - Position tracking summary report
Schedule::command('seo:track-positions --summary')
    ->weekly()
    ->mondays()
    ->at('07:30')
    ->timezone('Asia/Jakarta')
    ->withoutOverlapping()
    ->onSuccess(function () {
        \Log::info('✅ Position tracking summary generated');
    });

// Daily 04:45 - Import Google Search Console data (falls back to simulation when credentials absent)
Schedule::command('seo:gsc-import --days=7')
    ->dailyAt('04:45')
    ->timezone('Asia/Jakarta')
    ->withoutOverlapping()
    ->onSuccess(function () {
        \Log::info('✅ GSC data import completed');
    })
    ->onFailure(function () {
        \Log::error('❌ GSC data import failed');
    });

// Weekly Sunday 04:30 - GSC import + cross-reference AI estimates vs real GSC data (28-day window)
Schedule::command('seo:gsc-import --days=28 --crossref --crossref-days=28')
    ->weekly()
    ->sundays()
    ->at('04:30')
    ->timezone('Asia/Jakarta')
    ->withoutOverlapping()
    ->onSuccess(function () {
        \Log::info('✅ GSC weekly cross-reference completed');
    });

// ─── PHASE 7: E-E-A-T & pSEO OPTIMIZATION ──────────────────

// Weekly Sunday 05:00 - Submit all pSEO pages to IndexNow
Schedule::command('seo:index-now --pseo')
    ->weekly()
    ->sundays()
    ->at('05:00')
    ->timezone('Asia/Jakarta')
    ->name('indexnow-pseo-weekly')
    ->withoutOverlapping();

// ─── PHASE 8: RAG REGULATION SYNC ──────────────────────────

// Weekly Wednesday 04:00 - Sync & warm RAG regulation caches
Schedule::command('rag:sync-regulations')
    ->weekly()
    ->wednesdays()
    ->at('04:00')
    ->timezone('Asia/Jakarta')
    ->withoutOverlapping()
    ->onSuccess(function () {
        \Log::info('✅ RAG regulation sync completed');
    })
    ->onFailure(function () {
        \Log::error('❌ RAG regulation sync failed');
    });
