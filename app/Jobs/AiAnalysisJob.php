<?php

namespace App\Jobs;

use App\Models\Article;
use App\Services\SmartMetaOptimizerService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

/**
 * Generic async wrapper for AI-powered analysis tasks via OpenRouter.
 * Supports: article_meta_optimize, custom chat tasks.
 * Queue: ai — dedicated worker keeps AI latency separate from default queue.
 */
class AiAnalysisJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public int $timeout = 180;

    public array $backoff = [30, 60, 120];

    public bool $deleteWhenMissingModels = true;

    public function __construct(
        public readonly string $taskType,
        public readonly array $payload = []
    ) {
        $this->onQueue('ai');
    }

    public function handle(): void
    {
        match ($this->taskType) {
            'article_meta_optimize' => $this->handleArticleMetaOptimize(),
            default => $this->handleUnknownTask(),
        };
    }

    private function handleArticleMetaOptimize(): void
    {
        $articleId = $this->payload['article_id'] ?? null;

        if (! $articleId) {
            Log::warning('AiAnalysisJob: article_meta_optimize requires article_id', $this->payload);

            return;
        }

        $article = Article::find($articleId);

        if (! $article) {
            Log::info('AiAnalysisJob: article not found, skipping', ['article_id' => $articleId]);

            return;
        }

        $service = app(SmartMetaOptimizerService::class);
        $result = $service->optimizeArticle($article);

        Log::info('AiAnalysisJob: article_meta_optimize complete', [
            'article_id' => $articleId,
            'status' => $result['status'] ?? 'unknown',
            'changes' => array_keys($result['changes'] ?? []),
        ]);
    }

    private function handleUnknownTask(): void
    {
        Log::error('AiAnalysisJob: unknown task type', ['task' => $this->taskType]);
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('AiAnalysisJob: failed after retries', [
            'task' => $this->taskType,
            'payload' => $this->payload,
            'error' => $exception->getMessage(),
        ]);
    }
}
