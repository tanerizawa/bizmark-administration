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
        // 🤖 AUTOMATED BACKLINK MANAGEMENT
        // ========================================

        // Daily Backlink Health Check (Every morning at 8 AM)
        $schedule->command('backlink:monitor --limit=50')
            ->dailyAt('08:00')
            ->withoutOverlapping()
            ->emailOutputOnFailure('cs@bizmark.id');

        // Weekly Backlink Crawler (Every Monday at 9 AM)
        $schedule->command('backlink:crawl --all --limit=25')
            ->weeklyOn(1, '09:00')
            ->withoutOverlapping()
            ->emailOutputOnFailure('cs@bizmark.id');

        // Daily AI-Powered Outreach (Monday-Friday at 10 AM)
        $schedule->command('backlink:outreach --ai --priority=high --limit=5 --type=initial')
            ->weekdays()
            ->at('10:00')
            ->withoutOverlapping()
            ->emailOutputOnFailure('cs@bizmark.id');

        // Weekly Follow-up Emails (Every Wednesday at 10 AM)
        $schedule->command('backlink:outreach --ai --type=follow_up --limit=10')
            ->weeklyOn(3, '10:00')
            ->withoutOverlapping()
            ->emailOutputOnFailure('cs@bizmark.id');

        // Monthly Content Syndication (First day of month at 11 AM)
        $schedule->command('content:syndicate --limit=5')
            ->monthlyOn(1, '11:00')
            ->withoutOverlapping()
            ->emailOutputOnFailure('cs@bizmark.id');

        // ========================================
        // 📊 ANALYTICS & REPORTING
        // ========================================

        // Weekly Backlink Report (Every Sunday at 18:00)
        $schedule->call(function () {
            \Illuminate\Support\Facades\Log::info('Weekly backlink report generated', [
                'total_targets' => \App\Models\BacklinkTarget::count(),
                'active_backlinks' => \App\Models\Backlink::where('status', 'active')->count(),
                'contacted_targets' => \App\Models\BacklinkTarget::where('status', 'contacted')->count(),
                'response_rate' => \App\Models\BacklinkOutreach::where('status', 'replied')->count(),
            ]);
        })->weeklyOn(0, '18:00');

        // ========================================
        // 📝 AUTO-POST ARTICLE MANAGEMENT
        // ========================================

        // Process overdue schedules (Every 15 minutes)
        $schedule->command('autopost:process-overdue --limit=10')
            ->everyFifteenMinutes()
            ->withoutOverlapping();

        // Fix stuck processing schedules (Every 5 minutes)
        $schedule->command('autopost:fix-stuck --timeout=10')
            ->everyFiveMinutes()
            ->withoutOverlapping();

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
