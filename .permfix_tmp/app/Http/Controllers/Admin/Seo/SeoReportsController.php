<?php

namespace App\Http\Controllers\Admin\Seo;

use App\Http\Controllers\Admin\Seo\Concerns\SeoAdminFlashRedirect;
use App\Http\Controllers\Controller;
use App\Models\SeoReport;
use App\Services\SeoReportService;
use App\Support\SeoDashboardCache;
use Illuminate\Http\Request;

class SeoReportsController extends Controller
{
    use SeoAdminFlashRedirect;

    /**
     * Reports list
     */
    public function reports()
    {
        $reports = SeoReport::orderBy('created_at', 'desc')->paginate(20);
        return view('admin.seo.reports', compact('reports'));
    }

    /**
     * Report detail
     */
    public function reportDetail(int $reportId)
    {
        $report = SeoReport::findOrFail($reportId);
        return view('admin.seo.report-detail', compact('report'));
    }

    /**
     * Snapshot daily views via web (replaces: php artisan seo:snapshot-views)
     */
    public function runSnapshotViews(SeoReportService $reportService)
    {
        $count = $reportService->snapshotDailyViews();

        SeoDashboardCache::forgetStatsCaches();

        return $this->seoBackFlash('success', "📸 View snapshot selesai: {$count} artikel terekam.");
    }

    /**
     * Generate weekly report via web (replaces: php artisan seo:weekly-report)
     */
    public function runGenerateReport(Request $request, SeoReportService $reportService)
    {
        $type = $request->get('type', 'weekly');

        $report = $type === 'monthly'
            ? $reportService->generateMonthlyReport()
            : $reportService->generateWeeklyReport();

        $period = ucfirst($report->period);
        $msg = "📋 {$period} report berhasil di-generate ({$report->period_start->format('d M')} — {$report->period_end->format('d M Y')}).";

        return $this->seoRouteFlash('admin.seo.reports', 'success', $msg);
    }
}

