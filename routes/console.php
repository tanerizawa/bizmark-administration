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

// Weekly cleanup - Keep last 90 days of logs
Schedule::command('articles:cleanup-logs')
    ->weekly()
    ->sundays()
    ->at('02:00')
    ->timezone('Asia/Jakarta');
