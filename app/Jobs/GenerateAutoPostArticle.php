<?php

namespace App\Jobs;

use App\Models\AutoPostSchedule;
use App\Services\ArticleAutoPostService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GenerateAutoPostArticle implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     */
    public $tries = 3;

    /**
     * The maximum number of seconds the job can run before timing out.
     */
    public $timeout = 300; // 5 minutes

    /**
     * The number of seconds to wait before retrying the job.
     */
    public $backoff = [60, 300, 900]; // 1min, 5min, 15min

    /**
     * The schedule instance.
     */
    protected AutoPostSchedule $schedule;

    /**
     * Create a new job instance.
     */
    public function __construct(AutoPostSchedule $schedule)
    {
        $this->schedule = $schedule;
        $this->onQueue('default'); // Use default queue
    }

    /**
     * Execute the job.
     */
    public function handle(ArticleAutoPostService $service): void
    {
        \Log::info('🔄 Queue job started', [
            'job_id' => $this->job?->getJobId() ?? 'manual',
            'schedule_id' => $this->schedule->id,
            'attempt' => $this->attempts(),
        ]);

        try {
            // Check if already processed
            if (!$this->schedule->isPending()) {
                \Log::warning('⚠️  Schedule already processed', [
                    'schedule_id' => $this->schedule->id,
                    'status' => $this->schedule->status,
                ]);
                return;
            }

            // Mark as processing
            $this->schedule->markAsProcessing();

            // Generate and publish article
            $article = $service->executeScheduledPost($this->schedule);

            // Mark as completed
            $generationTime = now()->diffInSeconds($this->schedule->started_at);
            $this->schedule->markAsCompleted($generationTime);

            \Log::info('✅ Queue job completed successfully', [
                'job_id' => $this->job->getJobId(),
                'schedule_id' => $this->schedule->id,
                'article_id' => $article->id,
                'generation_time' => $generationTime . 's',
            ]);

        } catch (\Exception $e) {
            \Log::error('❌ Queue job failed', [
                'job_id' => $this->job->getJobId(),
                'schedule_id' => $this->schedule->id,
                'attempt' => $this->attempts(),
                'error' => $e->getMessage(),
            ]);

            // Mark as failed
            $this->schedule->markAsFailed($e->getMessage());

            // Rethrow to trigger retry mechanism
            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        \Log::error('💀 Queue job failed permanently (max retries reached)', [
            'schedule_id' => $this->schedule->id,
            'error' => $exception->getMessage(),
        ]);

        // Update schedule with final failure status
        $this->schedule->update([
            'status' => 'failed',
            'error_message' => "Max retries reached: {$exception->getMessage()}",
        ]);

        \App\Models\AutoPostLog::logError(
            'job_failed_permanently',
            'Article generation job failed after maximum retries',
            [
                'schedule_id' => $this->schedule->id,
                'error' => $exception->getMessage(),
            ]
        );
    }

    /**
     * Calculate the number of seconds to wait before retrying the job.
     */
    public function backoff(): array
    {
        return $this->backoff;
    }

    /**
     * Get the tags that should be assigned to the job.
     */
    public function tags(): array
    {
        return [
            'auto-post',
            'schedule:' . $this->schedule->id,
            'topic:' . $this->schedule->topic_id,
        ];
    }
}
