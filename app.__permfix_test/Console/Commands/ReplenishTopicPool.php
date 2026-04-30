<?php

namespace App\Console\Commands;

use App\Services\TopicGenerationService;
use Illuminate\Console\Command;

class ReplenishTopicPool extends Command
{
    protected $signature = 'topics:replenish 
                            {--count=10 : Number of topics to generate}
                            {--threshold=5 : Minimum available topics before auto-replenish}
                            {--force : Force generation even if pool is above threshold}';

    protected $description = 'Auto-generate new article topics via AI when pool is running low';

    public function handle(TopicGenerationService $service): int
    {
        $count = (int) $this->option('count');
        $threshold = (int) $this->option('threshold');
        $force = $this->option('force');

        $available = \App\Models\ArticleTopic::available()->count();
        $this->info("📊 Current pool: {$available} available topics (threshold: {$threshold})");

        if (! $force && $available >= $threshold) {
            $this->info('✅ Pool is healthy, no replenishment needed.');

            return 0;
        }

        $this->info("🤖 Generating {$count} new topics via AI...");
        $this->newLine();

        try {
            $created = $force
                ? $service->generateTopics($count)
                : $service->replenishIfNeeded($threshold);

            if ($created > 0) {
                $newAvailable = \App\Models\ArticleTopic::available()->count();
                $this->info("✅ Successfully generated {$created} new topics.");
                $this->info("📊 Pool now: {$newAvailable} available topics.");
            } else {
                $this->warn('⚠️ No new topics were generated. AI response might have returned duplicates.');
            }

            return 0;
        } catch (\Exception $e) {
            $this->error("❌ Failed: {$e->getMessage()}");

            return 1;
        }
    }
}
