<?php

namespace App\Jobs;

use App\Models\Article;
use App\Models\ArticleTopic;
use App\Models\AutoPostLog;
use App\Models\AutoPostSchedule;
use App\Services\ArticleAutoPostService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class GenerateAutoPostArticle implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Cache schema check to avoid repeated metadata queries in one job run.
     */
    private ?bool $hasArticleIdColumn = null;

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
     * Delete the job if its models no longer exist.
     */
    public $deleteWhenMissingModels = true;

    /**
     * The schedule instance.
     */
    protected AutoPostSchedule $schedule;

    /**
     * Create a new job instance.
     */
    public function __construct(AutoPostSchedule $schedule)
    {
        $this->schedule = $schedule->withoutRelations();
        $this->onQueue('default');
    }

    /**
     * Get the middleware the job should pass through.
     * Prevents duplicate job processing for same schedule
     */
    public function middleware(): array
    {
        return [
            (new WithoutOverlapping($this->schedule->id))
                ->releaseAfter(180) // Release lock after 3 minutes if stuck
                ->expireAfter(600), // Expire lock after 10 minutes
        ];
    }

    /**
     * Determine the time at which the job should timeout.
     */
    public function retryUntil(): \DateTime
    {
        return now()->addHours(2); // Give up after 2 hours total
    }

    /**
     * Safe logging helper that won't crash the job
     */
    private function safeLog(string $level, string $message, array $context = []): void
    {
        try {
            Log::$level($message, $context);
        } catch (\Throwable $e) {
            // Fallback to error_log if Laravel logging fails
            error_log("[{$level}] {$message} ".json_encode($context));
        }
    }

    /**
     * Detect whether schedules table supports article linkage.
     */
    private function scheduleHasArticleIdColumn(): bool
    {
        if ($this->hasArticleIdColumn === null) {
            try {
                $this->hasArticleIdColumn = Schema::hasColumn('auto_post_schedules', 'article_id');
            } catch (\Throwable $e) {
                $this->hasArticleIdColumn = false;
            }
        }

        return $this->hasArticleIdColumn;
    }

    /**
     * Resolve an existing article that already belongs to this schedule.
     */
    private function resolveExistingArticleId(?AutoPostSchedule $schedule = null): ?int
    {
        $schedule = $schedule ?? $this->schedule;
        if (! $schedule) {
            return null;
        }

        // 1) Direct linkage on schedule (if column exists)
        if ($this->scheduleHasArticleIdColumn() && ! empty($schedule->article_id)) {
            if (Article::find($schedule->article_id)) {
                return (int) $schedule->article_id;
            }
        }

        // 2) Linkage on topic (source of truth for published topics)
        $topic = ArticleTopic::withTrashed()->find($schedule->topic_id);
        if ($topic && ! empty($topic->article_id) && Article::find($topic->article_id)) {
            return (int) $topic->article_id;
        }

        // 3) Fallback from logs
        $createLog = AutoPostLog::where('schedule_id', $schedule->id)
            ->whereIn('event', ['article_created', 'article_published'])
            ->orderByDesc('created_at')
            ->first();

        if ($createLog) {
            $candidateId = $createLog->article_id
                ?? data_get($createLog->context, 'article_id');

            if (! empty($candidateId) && Article::find($candidateId)) {
                return (int) $candidateId;
            }
        }

        return null;
    }

    /**
     * Finalize schedule as completed in an idempotent way.
     */
    private function finalizeAsCompleted(AutoPostSchedule $schedule, int $articleId, string $source): void
    {
        $payload = [
            'status' => 'completed',
            'completed_at' => $schedule->completed_at ?? now(),
            'error_message' => null,
            'updated_at' => now(),
        ];

        if ($this->scheduleHasArticleIdColumn()) {
            $payload['article_id'] = $articleId;
        }

        DB::table('auto_post_schedules')
            ->where('id', $schedule->id)
            ->update($payload);

        $this->safeLog('warning', '🔁 Reconciled schedule to completed from existing article link', [
            'schedule_id' => $schedule->id,
            'article_id' => $articleId,
            'source' => $source,
        ]);
    }

    /**
     * Execute the job.
     */
    public function handle(ArticleAutoPostService $service): void
    {
        $this->safeLog('info', '🔄 Queue job started', [
            'job_id' => $this->job?->getJobId() ?? 'manual',
            'schedule_id' => $this->schedule->id,
            'attempt' => $this->attempts(),
        ]);

        // Wrap entire job in try-catch for crash protection
        try {
            // Refresh schedule from database to get latest status
            $this->schedule = AutoPostSchedule::find($this->schedule->id);

            if (! $this->schedule) {
                $this->safeLog('warning', '⚠️  Schedule not found, skipping', [
                    'schedule_id' => $this->schedule->id ?? 'unknown',
                ]);

                return;
            }

            // Check if already completed or failed permanently
            if (in_array($this->schedule->status, ['completed', 'failed'])) {
                $this->safeLog('warning', '⚠️  Schedule already completed or failed', [
                    'schedule_id' => $this->schedule->id,
                    'status' => $this->schedule->status,
                ]);

                return;
            }

            // Reconcile historical inconsistency: article exists but schedule is not completed yet.
            $existingArticleId = $this->resolveExistingArticleId($this->schedule);
            if ($existingArticleId) {
                $this->finalizeAsCompleted($this->schedule, $existingArticleId, 'pre-flight');

                return;
            }

            // If already has article_id, just mark as completed
            if ($this->scheduleHasArticleIdColumn() && $this->schedule->article_id) {
                $this->safeLog('info', '✅ Schedule already has article, marking completed', [
                    'schedule_id' => $this->schedule->id,
                    'article_id' => $this->schedule->article_id,
                ]);
                $this->schedule->update([
                    'status' => 'completed',
                    'completed_at' => now(),
                ]);

                return;
            }

            // Handle stuck "processing" schedules - allow retry if stuck > 2 minutes
            if ($this->schedule->status === 'processing') {
                $stuckMinutes = $this->schedule->updated_at->diffInMinutes(now());

                if ($stuckMinutes < 2) {
                    $this->safeLog('warning', '⚠️  Schedule being processed by another worker', [
                        'schedule_id' => $this->schedule->id,
                        'stuck_minutes' => $stuckMinutes,
                    ]);

                    return;
                }

                $this->safeLog('info', '🔄 Recovering stuck processing schedule', [
                    'schedule_id' => $this->schedule->id,
                    'stuck_minutes' => $stuckMinutes,
                ]);
            }

            // Mark as processing with database lock to prevent race conditions
            $lockQuery = DB::table('auto_post_schedules')
                ->where('id', $this->schedule->id)
                ->whereIn('status', ['pending', 'processing']); // Allow both pending AND stuck processing

            if ($this->scheduleHasArticleIdColumn()) {
                // Safety: don't process if article already exists
                $lockQuery->whereNull('article_id');
            }

            $updated = $lockQuery->update([
                'status' => 'processing',
                'started_at' => now(),
                'attempts' => DB::raw('attempts + 1'),
                'updated_at' => now(),
            ]);

            if (! $updated) {
                $this->safeLog('warning', '⚠️  Failed to lock schedule', [
                    'schedule_id' => $this->schedule->id,
                ]);

                return;
            }

            // Refresh to get updated values
            $this->schedule->refresh();

            $this->safeLog('info', '✅ Schedule locked for processing', [
                'schedule_id' => $this->schedule->id,
                'started_at' => $this->schedule->started_at,
            ]);

            $article = null;

            try {
                // Generate and publish article
                $this->safeLog('info', '🚀 Calling ArticleAutoPostService->executeScheduledPost()', [
                    'schedule_id' => $this->schedule->id,
                ]);

                $article = $service->executeScheduledPost($this->schedule);

                // ⚠️ CRITICAL: Immediately save article_id AND mark completed in single operation
                // This ensures atomic completion - either both succeed or we can detect partial completion
                $startedAt = $this->schedule->started_at ?? now();
                $generationTime = (int) $startedAt->diffInSeconds(now()); // Cast to int for PostgreSQL

                $completePayload = [
                    'status' => 'completed',
                    'completed_at' => now(),
                    'generation_time_seconds' => $generationTime,
                    'error_message' => null,
                    'updated_at' => now(),
                ];

                if ($this->scheduleHasArticleIdColumn()) {
                    $completePayload['article_id'] = $article->id;
                }

                DB::table('auto_post_schedules')
                    ->where('id', $this->schedule->id)
                    ->update($completePayload);

                $this->safeLog('info', '✅ Article created and schedule completed', [
                    'schedule_id' => $this->schedule->id,
                    'article_id' => $article->id,
                    'generation_time' => $generationTime.'s',
                ]);

            } catch (\Exception $e) {
                if (str_contains(strtolower($e->getMessage()), 'handled by automatic reassignment')) {
                    $this->safeLog('warning', '🔁 Schedule processing redirected after duplicate topic reassignment', [
                        'schedule_id' => $this->schedule->id,
                        'message' => $e->getMessage(),
                    ]);

                    return;
                }

                // If article was created but linking failed, schedule will be fixed by fix-stuck command
                $this->safeLog('error', '❌ Article generation or linking failed', [
                    'schedule_id' => $this->schedule->id,
                    'article_created' => $article !== null,
                    'article_id' => $article?->id,
                    'error' => $e->getMessage(),
                ]);

                // Only mark as failed if article wasn't created
                if (! $article) {
                    $this->schedule->markAsFailed($e->getMessage());
                }

                throw $e;
            }

            // Job completed successfully - logging only (won't fail job if logging fails)
            $this->safeLog('info', '🎉 Queue job finished', [
                'job_id' => $this->job?->getJobId() ?? 'unknown',
                'schedule_id' => $this->schedule->id,
            ]);

        } catch (\Exception $e) {
            $this->safeLog('error', '❌ Queue job failed', [
                'job_id' => $this->job?->getJobId() ?? 'unknown',
                'schedule_id' => $this->schedule->id,
                'attempt' => $this->attempts(),
                'error' => $e->getMessage(),
            ]);

            // CRITICAL: Rollback status if job crashed before article creation
            $this->rollbackOnFailure($e);

            // Rethrow to trigger retry mechanism
            throw $e;
        }
    }

    /**
     * Rollback schedule status on failure
     */
    private function rollbackOnFailure(\Exception $e): void
    {
        try {
            $schedule = AutoPostSchedule::find($this->schedule->id);

            // If article already exists, always finalize to completed (never rollback to pending).
            $existingArticleId = $this->resolveExistingArticleId($schedule);
            if ($schedule && $existingArticleId) {
                $this->finalizeAsCompleted($schedule, $existingArticleId, 'rollback');

                return;
            }

            $noArticleLinked = $this->scheduleHasArticleIdColumn() ? ! $schedule->article_id : true;

            if ($schedule && $schedule->status === 'processing' && $noArticleLinked) {
                $this->safeLog('warning', '🔄 Rolling back stuck processing status', [
                    'schedule_id' => $schedule->id,
                ]);

                $schedule->update([
                    'status' => 'pending',
                    'started_at' => null,
                    'error_message' => substr('Rollback: '.$e->getMessage(), 0, 500),
                ]);

                // Clear topic scheduling for retry
                if ($schedule->topic) {
                    $schedule->topic->clearScheduling();
                }
            }
        } catch (\Exception $rollbackError) {
            $this->safeLog('error', '❌ Failed to rollback schedule status', [
                'schedule_id' => $this->schedule->id,
                'error' => $rollbackError->getMessage(),
            ]);
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        $this->safeLog('error', '💀 Queue job failed permanently (max retries reached)', [
            'schedule_id' => $this->schedule->id,
            'error' => $exception->getMessage(),
        ]);

        try {
            // Update schedule with final failure status
            $schedule = AutoPostSchedule::find($this->schedule->id);

            // Last-resort reconciliation: if article exists, do not mark failed.
            $existingArticleId = $this->resolveExistingArticleId($schedule);
            if ($schedule && $existingArticleId) {
                $this->finalizeAsCompleted($schedule, $existingArticleId, 'failed-handler');

                return;
            }

            if ($schedule) {
                $schedule->update([
                    'status' => 'failed',
                    'error_message' => "Max retries reached: {$exception->getMessage()}",
                ]);

                // Clear topic scheduling so it can be retried later
                if ($schedule->topic) {
                    $schedule->topic->clearScheduling();
                }
            }

            AutoPostLog::logError(
                'job_failed_permanently',
                'Article generation job failed after maximum retries',
                [
                    'schedule_id' => $this->schedule->id,
                    'error' => $exception->getMessage(),
                    'exception_class' => get_class($exception),
                    'attempts' => $this->attempts(),
                ]
            );
        } catch (\Throwable $e) {
            error_log('Failed to update schedule on job failure: '.$e->getMessage());
        }
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
            'schedule:'.$this->schedule->id,
            'topic:'.$this->schedule->topic_id,
        ];
    }
}
