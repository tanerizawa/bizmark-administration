<?php

namespace App\Console\Commands;

use App\Jobs\GenerateAutoPostArticle;
use App\Models\AutoPostSchedule;
use Illuminate\Console\Command;

class ProcessPendingArticles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'articles:process-pending {--force : Force process even outside time window}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process pending article schedules and dispatch generation jobs';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 Checking for pending article schedules...');

        $windowMinutes = $this->option('force') ? 1440 : 15; // 24 hours if forced, 15 min otherwise

        // Get schedules due for processing
        $schedules = AutoPostSchedule::where('status', 'pending')
            ->where('scheduled_at', '<=', now()->addMinutes($windowMinutes))
            ->where('scheduled_at', '>=', now()->subMinutes($windowMinutes))
            ->orderBy('scheduled_at')
            ->get();

        if ($schedules->isEmpty()) {
            $this->info('✅ No pending schedules found.');

            return 0;
        }

        $this->info("📋 Found {$schedules->count()} schedule(s) to process.");
        $this->newLine();

        $dispatched = 0;
        $skipped = 0;

        foreach ($schedules as $schedule) {
            try {
                $topic = $schedule->topic;

                if (! $topic) {
                    $this->warn("⚠️  Schedule #{$schedule->id}: Topic not found");
                    $schedule->markAsFailed('Topic not found');
                    $skipped++;

                    continue;
                }

                $this->line("📄 Processing schedule #{$schedule->id}");
                $this->line("   Topic: {$topic->title}");
                $this->line("   Scheduled: {$schedule->scheduled_at->format('Y-m-d H:i:s')}");

                // Dispatch to queue
                GenerateAutoPostArticle::dispatch($schedule);

                $this->info('   ✅ Dispatched to queue');
                $dispatched++;

            } catch (\Exception $e) {
                $this->error("   ❌ Failed: {$e->getMessage()}");
                $skipped++;
            }

            $this->newLine();
        }

        $this->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->info('📊 Summary:');
        $this->line("   Dispatched: {$dispatched}");
        $this->line("   Skipped: {$skipped}");
        $this->line("   Total: {$schedules->count()}");
        $this->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');

        return 0;
    }
}
