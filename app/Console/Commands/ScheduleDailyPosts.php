<?php

namespace App\Console\Commands;

use App\Models\AutoPostConfig;
use App\Services\ArticleAutoPostService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ScheduleDailyPosts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'articles:schedule-daily {date? : Date to schedule (YYYY-MM-DD, default: today)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Schedule article posts for a specific date';

    /**
     * Execute the console command.
     */
    public function handle(ArticleAutoPostService $service)
    {
        // Parse date argument
        $dateString = $this->argument('date');
        $date = $dateString ? Carbon::parse($dateString) : now();

        $this->info("📅 Scheduling posts for: {$date->toDateString()}");
        $this->newLine();

        // Check if enabled
        $config = AutoPostConfig::current();

        if (!$config->is_enabled) {
            $this->warn('⚠️  Auto-posting is DISABLED in configuration.');
            $this->line('   Enable it from admin panel: /admin/auto-post/config');
            return 1;
        }

        $this->info("⚙️  Configuration:");
        $this->line("   Posts per day: {$config->posts_per_day}");
        $this->line("   Post times: " . implode(', ', $config->post_times));
        $this->line("   AI Model: {$config->ai_model}");
        $this->newLine();

        // Schedule posts
        try {
            $schedules = $service->scheduleNextBatch($date);

            if (empty($schedules)) {
                $this->warn('⚠️  No posts scheduled. Possible reasons:');
                $this->line('   - All time slots already scheduled');
                $this->line('   - No available topics in pool');
                return 0;
            }

            $this->info("✅ Successfully scheduled {count($schedules)} post(s):");
            $this->newLine();

            foreach ($schedules as $schedule) {
                $topic = $schedule->topic;
                $this->line("📄 Schedule #{$schedule->id}");
                $this->line("   Topic: {$topic->title}");
                $this->line("   Category: {$topic->category}");
                $this->line("   Time: {$schedule->scheduled_at->format('H:i')}");
                $this->newLine();
            }

            $this->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
            $this->info("📊 Scheduled: " . count($schedules) . " post(s)");
            $this->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');

            return 0;

        } catch (\Exception $e) {
            $this->error("❌ Scheduling failed: {$e->getMessage()}");
            return 1;
        }
    }
}
