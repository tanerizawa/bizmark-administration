<?php

namespace App\Http\Controllers\Admin\Seo;

use App\Http\Controllers\Controller;
use App\Models\SeoReport;
use App\Services\SeoReportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SeoReportsController extends Controller
{

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

        Cache::forget('seo_dashboard_stats_7days');
        Cache::forget('seo_dashboard_stats_30days');
        Cache::forget('seo_dashboard_stats_90days');

        return redirect()->back()->with('success', "📸 View snapshot selesai: {$count} artikel terekam.");
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

        return redirect()->route('admin.seo.reports')->with('success', $msg);
    }
}

