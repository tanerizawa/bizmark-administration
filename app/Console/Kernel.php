<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // ========================================
        // 📝 AUTO-POST ARTICLE MANAGEMENT
        // ========================================

        // Health check and auto-fix stuck schedules (Every 30 minutes)
        $schedule->command('articles:health-check --fix')
            ->everyThirtyMinutes()
            ->withoutOverlapping()
            ->emailOutputOnFailure('cs@bizmark.id');

        // Fix storage/cache permissions to prevent permission denied errors (Every 15 minutes)
        $schedule->call(function () {
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
        })->everyFifteenMinutes()->name('fix-storage-permissions');

        // Process overdue schedules (Every 15 minutes)
        $schedule->command('autopost:process-overdue --limit=10')
            ->everyFifteenMinutes()
            ->withoutOverlapping()
            ->skip(function () {
                // Skip if command doesn't exist yet
                return !class_exists(\App\Console\Commands\ProcessOverdueSchedules::class);
            });

        // Generate standalone daily batch at midnight with automatic overflow to next day.
        $schedule->command('autopost:schedule-nightly --overflow-days=7')
            ->dailyAt('00:00')
            ->timezone('Asia/Jakarta')
            ->withoutOverlapping()
            ->emailOutputOnFailure('cs@bizmark.id');

        // Fix stuck processing schedules (Every 5 minutes) - DEPRECATED, use health-check instead
        // $schedule->command('autopost:fix-stuck --timeout=10')
        //     ->everyFiveMinutes()
        //     ->withoutOverlapping();

        // ========================================
        // 🧹 MAINTENANCE TASKS
        // ========================================

        // Clean old logs (Every month)
        $schedule->command('model:prune')
            ->monthly()
            ->at('02:00');

        // Cache cleanup
        $schedule->command('cache:prune-stale-tags')
            ->hourly();

        // ========================================
        // 🔍 SEO DOMINATION ENGINE
        // ========================================

        // Submit recent articles to IndexNow (Daily at 6 AM)
        $schedule->command('seo:index-now --recent=1')
            ->dailyAt('06:00')
            ->withoutOverlapping();

        // Full IndexNow re-submission (Weekly Sunday at 3 AM)
        $schedule->command('seo:index-now --all')
            ->weeklyOn(0, '03:00')
            ->withoutOverlapping();

        // Content Refresh: AI-refresh 2 stale articles daily at 4 AM
        $schedule->command('seo:refresh-content --older-than=90 --limit=2')
            ->dailyAt('04:00')
            ->withoutOverlapping();

        // SEO Intelligence: Full pipeline weekly (keywords + clusters + gaps + meta)
        $schedule->command('seo:intelligence --queue-gaps=3 --meta-limit=5')
            ->weeklyOn(1, '02:00')
            ->withoutOverlapping();

        // Meta optimization: Daily optimize 3 articles at 5 AM
        $schedule->command('seo:optimize-meta --limit=3')
            ->dailyAt('05:00')
            ->withoutOverlapping();

        // ========================================
        // 📤 DISTRIBUTION ENGINE
        // ========================================

        // Full distribution: syndicate + captions + push (Daily at 7 AM)
        $schedule->command('seo:distribute --all --limit=3')
            ->dailyAt('07:00')
            ->withoutOverlapping();

        // Social captions for new articles (Daily at 11 AM)
        $schedule->command('content:social-captions --limit=2')
            ->dailyAt('11:00')
            ->withoutOverlapping();

        // ========================================
        // 📊 SEO COMMAND CENTER
        // ========================================

        // Daily view snapshot for trend tracking (Every night at 23:55)
        $schedule->command('seo:snapshot-views')
            ->dailyAt('23:55')
            ->withoutOverlapping();

        // Daily SEO scoring for 10 articles (at 3 AM)
        $schedule->command('seo:score-articles --limit=10')
            ->dailyAt('03:00')
            ->withoutOverlapping();

        // Weekly SEO report with email (Every Sunday at 20:00)
        $schedule->command('seo:weekly-report --email')
            ->weeklyOn(0, '20:00')
            ->withoutOverlapping();

        // Monthly SEO report (First of month at 20:00)
        $schedule->command('seo:weekly-report --monthly --email')
            ->monthlyOn(1, '20:00')
            ->withoutOverlapping();

        // Competitor analysis (Monday 04:00)
        $schedule->command('seo:competitor-analyze --limit=5')
            ->weeklyOn(1, '04:00')
            ->withoutOverlapping();

        // Meta A/B tests: create + evaluate (Tuesday & Friday 05:00)
        $schedule->command('seo:meta-ab-test --all --limit=3')
            ->twiceWeekly(2, 5, '05:00')
            ->withoutOverlapping();

        // GSC data import (daily 02:00)
        $schedule->command('seo:gsc-import --days=3')
            ->dailyAt('02:00')
            ->withoutOverlapping();

        // ========================================
        // 💾 DATABASE BACKUP & PROTECTION
        // ========================================

        // Daily database backup at 1 AM (before other maintenance tasks)
        $schedule->exec('bash ' . base_path('scripts/db-backup.sh') . ' backup')
            ->dailyAt('01:00')
            ->timezone('Asia/Jakarta')
            ->withoutOverlapping()
            ->onSuccess(function () {
                \Illuminate\Support\Facades\Log::info('Daily database backup completed successfully');
            })
            ->onFailure(function () {
                \Illuminate\Support\Facades\Log::error('Daily database backup FAILED!');
            })
            ->emailOutputOnFailure('cs@bizmark.id');

        // Weekly full backup on Sunday at 1:30 AM
        $schedule->exec('bash ' . base_path('scripts/db-backup.sh') . ' full')
            ->weeklyOn(0, '01:30')
            ->timezone('Asia/Jakarta')
            ->withoutOverlapping()
            ->emailOutputOnFailure('info@bizmark.id');

        // Verify last backup integrity daily at 6 AM
        $schedule->exec('bash ' . base_path('scripts/db-backup.sh') . ' verify')
            ->dailyAt('06:00')
            ->timezone('Asia/Jakarta')
            ->onFailure(function () {
                \Illuminate\Support\Facades\Log::critical('Database backup verification FAILED!');
            });

        // Database health check every 6 hours
        $schedule->command('db:monitor --alert')
            ->everySixHours()
            ->withoutOverlapping();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
