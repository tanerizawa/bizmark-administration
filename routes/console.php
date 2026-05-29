<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
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
// P1 — Regulatory Compliance Monitor
// ========================================
Schedule::command('permits:check-expiry')
    ->dailyAt('08:00')
    ->timezone('Asia/Jakarta')
    ->withoutOverlapping()
    ->onSuccess(function () {
        Log::info('✅ Permit expiry check completed');
    })
    ->onFailure(function () {
        Log::error('❌ Permit expiry check failed');
    });

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
        Log::info('✅ Topic pool replenishment check completed');
    })
    ->onFailure(function () {
        Log::error('❌ Topic pool replenishment failed');
    });

// Daily at midnight - Schedule posts for the day
Schedule::command('articles:schedule-daily')
    ->dailyAt('00:01')
    ->timezone('Asia/Jakarta')
    ->withoutOverlapping()
    ->onSuccess(function () {
        Log::info('✅ Daily article scheduling completed');
    })
    ->onFailure(function () {
        Log::error('❌ Daily article scheduling failed');
    });

// Every 15 minutes - Check and process pending schedules
Schedule::command('articles:process-pending')
    ->everyFifteenMinutes()
    ->withoutOverlapping()
    ->runInBackground();

// Every 15 minutes - Process overdue autopost schedules
Schedule::command('autopost:process-overdue --limit=10')
    ->everyFifteenMinutes()
    ->withoutOverlapping()
    ->runInBackground();

// Every 30 minutes - Health check and auto-fix stuck schedules
Schedule::command('articles:health-check --fix')
    ->everyThirtyMinutes()
    ->withoutOverlapping();

// Daily midnight - Generate standalone daily batch with overflow handling
Schedule::command('autopost:schedule-nightly --overflow-days=7')
    ->dailyAt('00:00')
    ->timezone('Asia/Jakarta')
    ->withoutOverlapping();

// Every hour - Backfill missing featured images via Pexels for newly generated articles
Schedule::command('articles:backfill-images --limit=8')
    ->hourly()
    ->withoutOverlapping()
    ->runInBackground();

// Weekly cleanup - Keep last 90 days of logs
Schedule::command('articles:cleanup-logs')
    ->weekly()
    ->sundays()
    ->at('02:00')
    ->timezone('Asia/Jakarta');

// Monthly model pruning
Schedule::command('model:prune')
    ->monthly()
    ->at('02:00');

// Hourly stale cache tags cleanup
Schedule::command('cache:prune-stale-tags')
    ->hourly();

// Every 10 minutes - Runtime permissions check (storage/* + bootstrap/cache)
Schedule::command('ops:permissions-check --create')
    ->everyTenMinutes()
    ->withoutOverlapping()
    ->onFailure(function () {
        Log::error('❌ Runtime permissions check failed (storage/bootstrap/cache not writable)');
    });

// Hourly email webhook security metrics report
Schedule::command('email-webhook:metrics-report --hours=1')
    ->hourly()
    ->withoutOverlapping();

// ========================================
// Database Backup
// ========================================

// Daily backup at 01:00 with verification
Schedule::command('db:backup daily --verify')
    ->dailyAt('01:00')
    ->timezone('Asia/Jakarta')
    ->withoutOverlapping();

// Weekly full backup on Sunday 01:30
Schedule::command('db:backup full --verify')
    ->weeklyOn(0, '01:30')
    ->timezone('Asia/Jakarta')
    ->withoutOverlapping();

// Daily integrity check for latest backup at 06:00
Schedule::exec('bash '.base_path('scripts/db-backup.sh').' verify')
    ->dailyAt('06:00')
    ->timezone('Asia/Jakarta')
    ->withoutOverlapping();

// ========================================
// Shapefile Cleanup
// ========================================

// Every 6 hours - Clean up old generated shapefile ZIP files (older than 24h)
Schedule::command('shapefiles:cleanup --hours=24')
    ->everySixHours()
    ->withoutOverlapping()
    ->onSuccess(function () {
        Log::info('✅ Shapefile cleanup completed');
    });

// Every 6 hours - Database health monitor
Schedule::command('db:monitor --alert')
    ->everySixHours()
    ->withoutOverlapping();

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
        Log::info('✅ SEO intelligence pipeline completed');
    })
    ->onFailure(function () {
        Log::error('❌ SEO intelligence pipeline failed');
    });

// Daily 03:00 - Queue top content gaps as article topics (feeds auto-post)
Schedule::command('seo:orchestrate --phase=content --queue-gaps=10 --convert-clusters')
    ->dailyAt('03:00')
    ->timezone('Asia/Jakarta')
    ->withoutOverlapping()
    ->onSuccess(function () {
        Log::info('✅ Content gap queuing completed');
    });

// Weekly Sunday 04:00 - Competitor SERP analysis
Schedule::command('seo:competitor-analyze --limit=15')
    ->weekly()
    ->sundays()
    ->at('04:00')
    ->timezone('Asia/Jakarta')
    ->withoutOverlapping()
    ->onSuccess(function () {
        Log::info('✅ Competitor analysis completed');
    });

// Daily 05:00 - Position tracking (track keyword rankings via SearXNG)
Schedule::command('seo:track-positions --limit=50')
    ->dailyAt('05:00')
    ->timezone('Asia/Jakarta')
    ->withoutOverlapping()
    ->onSuccess(function () {
        Log::info('✅ Position tracking completed');
    });

// Daily 05:30 - Trending topic discovery via SearXNG
Schedule::command('seo:trending-topics')
    ->dailyAt('05:30')
    ->timezone('Asia/Jakarta')
    ->withoutOverlapping()
    ->onSuccess(function () {
        Log::info('✅ Trending topic discovery completed');
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
        Log::info('✅ Trending topics converted to article topics');
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
        Log::info('✅ Meta optimization completed');
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
        Log::info('✅ Content refresh completed');
    });

// Weekly Saturday 04:00 - Bidirectional backlink scan (inject cross-links in existing articles)
Schedule::command('seo:backlink-scan --limit=50')
    ->weekly()
    ->saturdays()
    ->at('04:00')
    ->timezone('Asia/Jakarta')
    ->withoutOverlapping()
    ->onSuccess(function () {
        Log::info('✅ Backlink scan completed');
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
        Log::warning("⚠️ Topic pool critically low ({$available}). Running emergency intelligence replenish...");
        \Illuminate\Support\Facades\Artisan::call('seo:orchestrate', [
            '--phase' => 'content',
            '--convert-clusters' => true,
            '--queue-gaps' => 30,
        ]);
        Log::info('✅ Emergency topic replenish completed. Pool: '.\App\Models\ArticleTopic::where('status', 'pending')->count());
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
        Log::info('✅ Content syndication completed');
    });

// Daily 07:00 - Full distribution pipeline (legacy command)
Schedule::command('seo:distribute --all --limit=3')
    ->dailyAt('07:00')
    ->timezone('Asia/Jakarta')
    ->withoutOverlapping();

// Daily 11:00 - Generate social captions for recent content
Schedule::command('content:social-captions --limit=2')
    ->dailyAt('11:00')
    ->timezone('Asia/Jakarta')
    ->withoutOverlapping();

// Daily 14:30 - Post to social media platforms (Telegram, Twitter, Facebook, LinkedIn, GBP)
// Guard in SocialPostingService checks for non-empty tokens before posting
Schedule::command('content:social-post --limit=3')
    ->dailyAt('14:30')
    ->timezone('Asia/Jakarta')
    ->withoutOverlapping()
    ->onSuccess(function () {
        Log::info('✅ Social media posting completed');
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
        Log::info('✅ SEO weekly report generated and emailed');
    });

// Daily 23:55 - Snapshot article views for trend tracking
Schedule::command('seo:snapshot-views')
    ->dailyAt('23:55')
    ->timezone('Asia/Jakarta')
    ->withoutOverlapping();

// Weekly Monday 07:30 - Position tracking summary report
Schedule::command('seo:track-positions --summary')
    ->weekly()
    ->mondays()
    ->at('07:30')
    ->timezone('Asia/Jakarta')
    ->withoutOverlapping()
    ->onSuccess(function () {
        Log::info('✅ Position tracking summary generated');
    });

// Daily 04:45 - Import Google Search Console data (falls back to simulation when credentials absent)
Schedule::command('seo:gsc-import --days=7')
    ->dailyAt('04:45')
    ->timezone('Asia/Jakarta')
    ->withoutOverlapping()
    ->onSuccess(function () {
        Log::info('✅ GSC data import completed');
    })
    ->onFailure(function () {
        Log::error('❌ GSC data import failed');
    });

// Weekly Sunday 04:30 - GSC import + cross-reference AI estimates vs real GSC data (28-day window)
Schedule::command('seo:gsc-import --days=28 --crossref --crossref-days=28')
    ->weekly()
    ->sundays()
    ->at('04:30')
    ->timezone('Asia/Jakarta')
    ->withoutOverlapping()
    ->onSuccess(function () {
        Log::info('✅ GSC weekly cross-reference completed');
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
        Log::info('✅ RAG regulation sync completed');
    })
    ->onFailure(function () {
        Log::error('❌ RAG regulation sync failed');
    });

// ─── P5: KBLI SEMANTIC SEARCH — Re-index new/unembedded KBLIs ───
// Daily Sunday 03:00 — hanya proses KBLI yang belum ter-embed
Schedule::command('kbli:index-embeddings')
    ->weekly()
    ->sundays()
    ->at('03:00')
    ->timezone('Asia/Jakarta')
    ->withoutOverlapping()
    ->onSuccess(function () {
        Log::info('✅ KBLI embedding indexing completed');
    })
    ->onFailure(function () {
        Log::error('❌ KBLI embedding indexing failed');
    });

// ─── P4: OSS-RBA STATUS TRACKER — Daily status check ───
// Daily 09:00 WIB — cek status semua permit yang belum dicek hari ini
Schedule::command('oss:check-status')
    ->dailyAt('09:00')
    ->timezone('Asia/Jakarta')
    ->withoutOverlapping()
    ->onSuccess(function () {
        Log::info('✅ OSS permit status check completed');
    })
    ->onFailure(function () {
        Log::error('❌ OSS permit status check failed');
    });

// ─── P7: AI REGULATORY CHANGE DETECTOR — Weekly crawl ───
// Weekly Monday 07:00 WIB — crawl JDIH + OSS sources, AI analyze, notify clients
Schedule::command('regulatory:crawl')
    ->weekly()
    ->mondays()
    ->at('07:00')
    ->timezone('Asia/Jakarta')
    ->withoutOverlapping()
    ->onSuccess(function () {
        Log::info('✅ Regulatory change crawl completed');
    })
    ->onFailure(function () {
        Log::error('❌ Regulatory change crawl failed');
    });
