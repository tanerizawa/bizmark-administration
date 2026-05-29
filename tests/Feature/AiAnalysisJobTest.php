<?php

namespace Tests\Feature;

use App\Jobs\AiAnalysisJob;
use App\Models\Article;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class AiAnalysisJobTest extends TestCase
{
    use RefreshDatabase;

    private Article $article;

    protected function setUp(): void
    {
        parent::setUp();

        $author = User::factory()->create();
        $this->article = Article::create([
            'title' => 'Test SEO Article',
            'slug' => 'test-seo-article',
            'content' => '<p>Test content for AI analysis.</p>',
            'status' => 'published',
            'language' => 'id',
            'author_id' => $author->id,
        ]);
    }

    public function test_handle_article_meta_optimize_with_valid_article(): void
    {
        Log::spy();

        $job = new AiAnalysisJob('article_meta_optimize', [
            'article_id' => $this->article->id,
        ]);

        $job->handle();

        Log::shouldHaveReceived('info')
            ->once()
            ->withArgs(fn ($message) => str_contains($message, 'article_meta_optimize complete'));
    }

    public function test_handle_article_meta_optimize_missing_article_id(): void
    {
        Log::spy();

        $job = new AiAnalysisJob('article_meta_optimize', []);

        $job->handle();

        Log::shouldHaveReceived('warning')
            ->once()
            ->withArgs(fn ($message) => str_contains($message, 'requires article_id'));
    }

    public function test_handle_article_meta_optimize_article_not_found(): void
    {
        Log::spy();

        $job = new AiAnalysisJob('article_meta_optimize', [
            'article_id' => 9999,
        ]);

        $job->handle();

        Log::shouldHaveReceived('info')
            ->once()
            ->withArgs(fn ($message) => str_contains($message, 'article not found'));
    }

    public function test_handle_unknown_task_type(): void
    {
        Log::spy();

        $job = new AiAnalysisJob('unknown_task_type', []);

        $job->handle();

        Log::shouldHaveReceived('error')
            ->once()
            ->withArgs(fn ($message) => str_contains($message, 'unknown task type'));
    }

    public function test_failed_logs_error(): void
    {
        Log::spy();

        $exception = new \Exception('AI API timeout after 3 retries');
        $job = new AiAnalysisJob('article_meta_optimize', ['article_id' => 1]);

        $job->failed($exception);

        Log::shouldHaveReceived('error')
            ->once()
            ->withArgs(fn ($message) => str_contains($message, 'failed after retries'));
    }
}
