<?php

namespace Tests\Unit;

use App\Jobs\AiAnalysisJob;
use App\Models\Article;
use App\Services\SmartMetaOptimizerService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class AiAnalysisJobTest extends TestCase
{
    use RefreshDatabase;

    public function test_job_properties(): void
    {
        $job = new AiAnalysisJob('article_meta_optimize', ['article_id' => 1]);

        $this->assertSame(3, $job->tries);
        $this->assertSame(180, $job->timeout);
        $this->assertSame([30, 60, 120], $job->backoff);
        $this->assertTrue($job->deleteWhenMissingModels);
    }

    public function test_handle_article_meta_optimize_calls_service(): void
    {
        $user = \App\Models\User::factory()->create();
        $article = Article::create([
            'title' => 'Panduan AMDAL 2026',
            'slug' => 'panduan-amdal-2026',
            'content' => 'Konten artikel.',
            'status' => 'published',
            'language' => 'id',
            'author_id' => $user->id,
        ]);

        $mockService = $this->createMock(SmartMetaOptimizerService::class);
        $mockService->expects($this->once())
            ->method('optimizeArticle')
            ->with($this->callback(fn ($arg) => $arg->id === $article->id))
            ->willReturn(['status' => 'success', 'changes' => ['meta_title']]);

        $this->app->instance(SmartMetaOptimizerService::class, $mockService);

        $job = new AiAnalysisJob('article_meta_optimize', ['article_id' => $article->id]);
        $job->handle();
    }

    public function test_handle_skips_gracefully_when_article_id_missing(): void
    {
        Log::shouldReceive('warning')
            ->once()
            ->with('AiAnalysisJob: article_meta_optimize requires article_id', []);

        $mockService = $this->createMock(SmartMetaOptimizerService::class);
        $mockService->expects($this->never())->method('optimizeArticle');
        $this->app->instance(SmartMetaOptimizerService::class, $mockService);

        $job = new AiAnalysisJob('article_meta_optimize', []);
        $job->handle();
    }

    public function test_handle_skips_gracefully_when_article_not_found(): void
    {
        Log::shouldReceive('info')
            ->once()
            ->with('AiAnalysisJob: article not found, skipping', ['article_id' => 99999]);

        $mockService = $this->createMock(SmartMetaOptimizerService::class);
        $mockService->expects($this->never())->method('optimizeArticle');
        $this->app->instance(SmartMetaOptimizerService::class, $mockService);

        $job = new AiAnalysisJob('article_meta_optimize', ['article_id' => 99999]);
        $job->handle();
    }

    public function test_handle_unknown_task_logs_error(): void
    {
        Log::shouldReceive('error')
            ->once()
            ->with('AiAnalysisJob: unknown task type', ['task' => 'nonexistent_task']);

        $job = new AiAnalysisJob('nonexistent_task');
        $job->handle();
    }

    public function test_failed_logs_error(): void
    {
        Log::shouldReceive('error')
            ->once()
            ->with('AiAnalysisJob: failed after retries', \Mockery::on(function ($context) {
                return $context['task'] === 'article_meta_optimize'
                    && $context['error'] === 'Connection timeout';
            }));

        $job = new AiAnalysisJob('article_meta_optimize', ['article_id' => 1]);
        $job->failed(new \RuntimeException('Connection timeout'));
    }
}
