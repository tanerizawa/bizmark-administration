<?php

namespace App\Console\Commands\AutoPost;

use App\Models\ArticleTopic;
use App\Models\AutoPostConfig;
use App\Services\ArticleAutoPostService;
use App\Services\TopicGenerationService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ScheduleNightlyBatch extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'autopost:schedule-nightly
                            {--start-date= : Batch start date (YYYY-MM-DD), default: today in config timezone}
                            {--target= : Total posts to schedule in this run, default: posts_per_day}
                            {--overflow-days=7 : Max day spillover when slots are full}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Schedule auto-post batch at midnight with overflow to next day when slots are full';

    /**
     * Execute the console command.
     */
    public function handle(ArticleAutoPostService $service): int
    {
        $config = AutoPostConfig::current();

        if (! $config->is_enabled) {
            $this->warn('⚠️ Auto-posting is disabled. Skipping nightly batch scheduling.');

            return self::SUCCESS;
        }

        $timezone = $config->timezone ?: 'Asia/Jakarta';
        $startDateOption = $this->option('start-date');
        $startDate = $startDateOption
            ? Carbon::parse($startDateOption, $timezone)
            : Carbon::now($timezone)->startOfDay();

        $target = (int) ($this->option('target') ?: $config->posts_per_day);
        $target = max(1, $target);

        $maxOverflowDays = max(0, (int) $this->option('overflow-days'));

        $this->info('🌙 Nightly auto-post batch scheduler started');
        $this->line("   Start date: {$startDate->toDateString()} ({$timezone})");
        $this->line("   Target posts: {$target}");
        $this->line("   Max overflow days: {$maxOverflowDays}");

        // Pre-emptively replenish topic pool so midnight run can complete without manual intervention.
        $availableTopics = ArticleTopic::available()->count();
        if ($availableTopics < $target) {
            $this->warn("⚠️ Topic pool low: {$availableTopics}, replenishing...");
            try {
                $generated = app(TopicGenerationService::class)->replenishIfNeeded($target * 3);
                $this->line("   ✅ Generated {$generated} additional topic(s)");
            } catch (\Throwable $e) {
                $this->warn('   ⚠️ Topic replenishment failed: '.$e->getMessage());
            }
        }

        $result = $service->scheduleRollingBatch($startDate, $target, $maxOverflowDays);

        $this->newLine();
        $this->info('📊 Nightly batch summary');
        $this->line("   Requested: {$result['requested']}");
        $this->line("   Scheduled: {$result['scheduled_count']}");
        $this->line("   Remaining: {$result['remaining']}");
        $this->line("   Days used: {$result['days_used']}");

        if (! empty($result['scheduled'])) {
            $this->newLine();
            $this->info('✅ Scheduled slots:');
            foreach ($result['scheduled'] as $schedule) {
                $this->line("   - #{$schedule->id} at {$schedule->scheduled_at->setTimezone($timezone)->format('Y-m-d H:i')} (topic {$schedule->topic_id})");
            }
        }

        if ($result['remaining'] > 0) {
            $this->warn('⚠️ Some posts could not be scheduled due to limited slots/topics.');
        }

        return self::SUCCESS;
    }
}
