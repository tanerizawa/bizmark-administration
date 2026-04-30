<?php

namespace Tests\Unit\Http\Controllers\Admin\Seo;

use App\Models\SeoReport;
use App\Modules\ContentSeo\Controllers\Admin\Seo\SeoReportsController;
use App\Services\SeoReportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class SeoReportsControllerTest extends TestCase
{
    public function test_run_snapshot_views_clears_stats_cache_and_redirects_with_flash(): void
    {
        foreach (['seo_dashboard_stats_7days', 'seo_dashboard_stats_30days', 'seo_dashboard_stats_90days'] as $key) {
            Cache::put($key, ['stub' => true], 3600);
        }

        $service = $this->createMock(SeoReportService::class);
        $service->expects($this->once())->method('snapshotDailyViews')->willReturn(7);

        $controller = new SeoReportsController;
        $response = $controller->runSnapshotViews($service);

        $this->assertSame(302, $response->getStatusCode());
        foreach (['seo_dashboard_stats_7days', 'seo_dashboard_stats_30days', 'seo_dashboard_stats_90days'] as $key) {
            $this->assertNull(Cache::get($key), "Expected cache key {$key} cleared");
        }

        $success = $response->getSession()?->get('success');
        $this->assertIsString($success);
        $this->assertStringContainsString('7', $success);
        $this->assertStringContainsString('snapshot', strtolower($success));
    }

    public function test_run_generate_report_defaults_to_weekly_when_type_missing(): void
    {
        $report = new SeoReport([
            'period' => 'weekly',
            'period_start' => now()->subWeek(),
            'period_end' => now(),
        ]);

        $service = $this->createMock(SeoReportService::class);
        $service->expects($this->once())->method('generateWeeklyReport')->willReturn($report);
        $service->expects($this->never())->method('generateMonthlyReport');

        $request = Request::create('/admin/seo/run/generate-report', 'POST', []);

        $controller = new SeoReportsController;
        $response = $controller->runGenerateReport($request, $service);

        $this->assertSame(302, $response->getStatusCode());
        $this->assertSame(route('admin.seo.reports'), $response->headers->get('Location'));

        $success = $response->getSession()?->get('success');
        $this->assertIsString($success);
        $this->assertStringContainsString('Weekly', $success);
    }

    public function test_run_generate_report_monthly_when_type_is_monthly(): void
    {
        $report = new SeoReport([
            'period' => 'monthly',
            'period_start' => now()->subMonth(),
            'period_end' => now(),
        ]);

        $service = $this->createMock(SeoReportService::class);
        $service->expects($this->once())->method('generateMonthlyReport')->willReturn($report);
        $service->expects($this->never())->method('generateWeeklyReport');

        $request = Request::create('/admin/seo/run/generate-report', 'POST', ['type' => 'monthly']);

        $controller = new SeoReportsController;
        $response = $controller->runGenerateReport($request, $service);

        $this->assertSame(302, $response->getStatusCode());
        $this->assertSame(route('admin.seo.reports'), $response->headers->get('Location'));

        $success = $response->getSession()?->get('success');
        $this->assertIsString($success);
        $this->assertStringContainsString('Monthly', $success);
    }
}
