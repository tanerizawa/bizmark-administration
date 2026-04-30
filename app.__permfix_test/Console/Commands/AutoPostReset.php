<?php

namespace App\Console\Commands;

use App\Jobs\GenerateAutoPostArticle;
use App\Models\AutoPostSchedule;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class AutoPostReset extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'articles:reset 
                            {--stuck : Reset only stuck processing schedules}
                            {--failed : Reset only failed schedules}
                            {--all : Reset all non-completed schedules}
                            {--dispatch : Also dispatch jobs to queue after reset}
                            {--id= : Reset specific schedule ID}';

    /**
     * The console command description.
     */
    protected $description = 'Reset stuck or failed auto-post schedules for retry';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔄 AUTO-POST SCHEDULE RESET');
        $this->info('═══════════════════════════════════════════');
        $this->newLine();

        // Get schedules based on options
        $query = AutoPostSchedule::with('topic');

        if ($this->option('id')) {
            $query->where('id', $this->option('id'));
        } elseif ($this->option('stuck')) {
            $query->where('status', 'processing');
        } elseif ($this->option('failed')) {
            $query->where('status', 'failed');
        } elseif ($this->option('all')) {
            $query->whereIn('status', ['pending', 'processing', 'failed']);
        } else {
            // Default: stuck processing only (most common issue)
            $query->where('status', 'processing');
        }

        $schedules = $query->get();

        if ($schedules->isEmpty()) {
            $this->info('✅ No schedules found matching criteria');

            return 0;
        }

        $this->warn("Found {$schedules->count()} schedule(s) to reset:");
        $this->newLine();

        // Display schedules
        $headers = ['ID', 'Topic', 'Status', 'Scheduled', 'Started', 'Attempts'];
        $rows = $schedules->map(function ($s) {
            return [
                $s->id,
                \Str::limit($s->topic?->title ?? 'N/A', 40),
                $s->status,
                $s->scheduled_at?->format('Y-m-d H:i') ?? 'N/A',
                $s->started_at?->format('Y-m-d H:i') ?? '-',
                $s->attempts,
            ];
        })->toArray();

        $this->table($headers, $rows);
        $this->newLine();

        if (! $this->confirm('Reset these schedules to pending status?')) {
            $this->info('Operation cancelled');

            return 0;
        }

        // Clear failed jobs first
        $this->info('🧹 Clearing failed jobs from database...');
        $clearedJobs = DB::table('failed_jobs')
            ->where('payload', 'like', '%GenerateAutoPostArticle%')
            ->delete();
        $this->line("   Cleared {$clearedJobs} failed jobs");

        // Reset schedules
        $resetCount = 0;
        $dispatchCount = 0;

        foreach ($schedules as $schedule) {
            // Check if article already exists (from partial completion)
            if ($schedule->article_id) {
                $this->warn("   #{$schedule->id} - Article already exists (ID: {$schedule->article_id}), marking as completed");
                $schedule->update([
                    'status' => 'completed',
                    'completed_at' => now(),
                ]);

                continue;
            }

            // Reset schedule
            $schedule->update([
                'status' => 'pending',
                'started_at' => null,
                'completed_at' => null,
                'error_message' => 'Reset by artisan command at '.now()->format('Y-m-d H:i:s'),
                'attempts' => 0, // Reset attempts for fresh start
            ]);

            // Clear topic scheduling status
            if ($schedule->topic) {
                $schedule->topic->update([
                    'is_scheduled' => true,
                    'scheduled_for' => $schedule->scheduled_at,
                    'processing_status' => null,
                ]);
            }

            $this->line("   ✅ #{$schedule->id} - Reset to pending");
            $resetCount++;

            // Optionally dispatch job immediately
            if ($this->option('dispatch')) {
                // Add small delay between dispatches to prevent overwhelming
                GenerateAutoPostArticle::dispatch($schedule)
                    ->delay(now()->addSeconds($dispatchCount * 5)); // 5 second spacing
                $this->line('      📤 Job dispatched (delay: '.($dispatchCount * 5).'s)');
                $dispatchCount++;
            }
        }

        $this->newLine();
        $this->info('═══════════════════════════════════════════');
        $this->info("✅ Reset {$resetCount} schedule(s)");

        if ($dispatchCount > 0) {
            $this->info("📤 Dispatched {$dispatchCount} job(s) to queue");
            $this->newLine();
            $this->comment('Monitor queue with: php artisan queue:listen --tries=3');
        } else {
            $this->newLine();
            $this->comment('Jobs will be processed by scheduler at their scheduled time.');
            $this->comment('To process immediately, run: php artisan articles:reset --stuck --dispatch');
        }

        return 0;
    }
}
