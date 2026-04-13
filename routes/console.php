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
        bootstrap_path('cache'),
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

// Daily 01:00 - Score all unscored articles
Schedule::command('seo:score-articles --limit=20')
    ->dailyAt('01:00')
    ->timezone('Asia/Jakarta')
    ->withoutOverlapping();

// ─── DISTRIBUTION LAYER ─────────────────────────────────────

// Daily 15:00 - Regenerate sitemap after new content
Schedule::command('sitemap:generate --ping')
    ->dailyAt('15:00')
    ->timezone('Asia/Jakarta')
    ->withoutOverlapping();

// Daily 15:10 - Submit recent articles to IndexNow
Schedule::command('seo:index-now --recent=3')
    ->dailyAt('15:10')
    ->timezone('Asia/Jakarta')
    ->withoutOverlapping();

// Daily 14:00 - Syndicate to external platforms
Schedule::command('content:syndicate --limit=3')
    ->dailyAt('14:00')
    ->timezone('Asia/Jakarta')
    ->withoutOverlapping();

// ─── REPORTING LAYER ────────────────────────────────────────

// Weekly Monday 07:00 - Generate SEO performance report
Schedule::command('seo:weekly-report')
    ->weekly()
    ->mondays()
    ->at('07:00')
    ->timezone('Asia/Jakarta')
    ->withoutOverlapping()
    ->onSuccess(function () {
        \Log::info('✅ SEO weekly report generated');
    });
