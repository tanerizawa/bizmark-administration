<?php

namespace App\Console\Commands\AutoPost;

use App\Jobs\GenerateAutoPostArticle;
use App\Models\AutoPostSchedule;
use Illuminate\Console\Command;

class ProcessOverdueSchedules extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'autopost:process-overdue {--limit=10 : Maximum number of schedules to process} {--force : Process even if time window exceeded}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process pending schedules that have passed their scheduled time';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $limit = (int) $this->option('limit');
        $force = $this->option('force');

        $this->info('Checking for overdue pending schedules...');

        // Get overdue schedules within reasonable time window (last 7 days unless forced)
        $query = AutoPostSchedule::where('status', 'pending')
            ->where('scheduled_at', '<=', now())
            ->orderBy('scheduled_at', 'asc');

        if (! $force) {
            // Don't process schedules older than 7 days unless forced
            $query->where('scheduled_at', '>=', now()->subDays(7));
        }

        $overdue = $query->limit($limit)->get();

        if ($overdue->isEmpty()) {
            $this->info('No overdue schedules found.');

            return 0;
        }

        $this->info("Found {$overdue->count()} overdue schedule(s)");
        $this->newLine();

        $bar = $this->output->createProgressBar($overdue->count());
        $bar->start();

        $dispatched = 0;

        foreach ($overdue as $schedule) {
            $this->newLine();
            $topicTitle = $schedule->topic?->title ?? "[Topic unavailable #{$schedule->topic_id}]";
            $this->line("Schedule #{$schedule->id}: {$topicTitle}");
            $this->line("  Scheduled: {$schedule->scheduled_at->format('Y-m-d H:i')} ({$schedule->scheduled_at->diffForHumans()})");

            try {
                // Dispatch to queue
                GenerateAutoPostArticle::dispatch($schedule)->onQueue('default');

                $this->info('  ✓ Dispatched to queue');
                $dispatched++;

                // Small delay to prevent overwhelming the queue
                usleep(100000); // 0.1 second

            } catch (\Exception $e) {
                $this->error("  ✗ Failed: {$e->getMessage()}");
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        $this->info('Summary:');
        $this->info("  Total overdue: {$overdue->count()}");
        $this->info("  Successfully dispatched: {$dispatched}");
        $this->info('  Queue worker will process them shortly...');

        return 0;
    }
}
