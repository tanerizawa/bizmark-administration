<?php

namespace Tests\Unit;

use App\Jobs\GeneratePdfJob;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class GeneratePdfJobTest extends TestCase
{
    public function test_job_properties(): void
    {
        $job = new GeneratePdfJob(
            modelType: 'App\Models\Quotation',
            modelId: 1,
            view: 'pdfs.quotation',
            data: ['quotation' => ['number' => 'Q-2026-001']],
        );

        $this->assertSame(3, $job->tries);
        $this->assertSame(120, $job->timeout);
        $this->assertSame([30, 60, 120], $job->backoff);
    }

    public function test_handle_renders_view_and_stores_pdf(): void
    {
        Storage::fake('local');

        // Add the stubs directory to the view finder paths
        view()->getFinder()->prependLocation(__DIR__.'/../stubs');

        $job = new GeneratePdfJob(
            modelType: 'App\Models\Quotation',
            modelId: 42,
            view: 'pdfs.quotation',
            data: [],
            disk: 'local',
        );

        $job->handle();

        // Should have stored a file in pdfs/ directory
        $files = Storage::disk('local')->files('pdfs');
        $this->assertCount(1, $files);
        $this->assertStringContainsString('App-Models-Quotation-42-', $files[0]);
    }

    public function test_handle_logs_on_completion(): void
    {
        Storage::fake('local');

        view()->getFinder()->prependLocation(__DIR__.'/../stubs');

        Log::shouldReceive('info')
            ->once()
            ->with('GeneratePdfJob: starting', \Mockery::on(function ($context) {
                return $context['model_type'] === 'App\Models\Quotation'
                    && $context['model_id'] === 1
                    && $context['view'] === 'pdfs.quotation';
            }));

        Log::shouldReceive('info')
            ->once()
            ->with('GeneratePdfJob: completed', \Mockery::on(function ($context) {
                return str_contains($context['path'] ?? '', 'pdfs/');
            }));

        $job = new GeneratePdfJob(
            modelType: 'App\Models\Quotation',
            modelId: 1,
            view: 'pdfs.quotation',
            data: [],
        );

        $job->handle();
    }

    public function test_handle_logs_error_on_failure(): void
    {
        Storage::fake('local');

        Log::shouldReceive('info')
            ->once()
            ->with('GeneratePdfJob: starting', \Mockery::on(function ($context) {
                return $context['model_type'] === 'App\Models\Quotation'
                    && $context['model_id'] === 1;
            }));

        Log::shouldReceive('error')
            ->once()
            ->with('GeneratePdfJob: failed', \Mockery::on(function ($context) {
                return $context['model_type'] === 'App\Models\Quotation'
                    && $context['model_id'] === 1;
            }));

        $job = new GeneratePdfJob(
            modelType: 'App\Models\Quotation',
            modelId: 1,
            view: 'nonexistent.view',
            data: [],
        );

        try {
            $job->handle();
        } catch (\Exception) {
            // Expected: view doesn't exist
        }
    }

    public function test_failed_logs_permanent_failure(): void
    {
        $job = new GeneratePdfJob(
            modelType: 'App\Models\Quotation',
            modelId: 1,
            view: 'pdfs.quotation',
            data: [],
        );

        Log::shouldReceive('error')
            ->once()
            ->with('GeneratePdfJob: permanently failed', \Mockery::on(function ($context) {
                return $context['model_type'] === 'App\Models\Quotation'
                    && str_contains($context['error'] ?? '', 'Disk full');
            }));

        $job->failed(new \RuntimeException('Disk full'));
    }
}
