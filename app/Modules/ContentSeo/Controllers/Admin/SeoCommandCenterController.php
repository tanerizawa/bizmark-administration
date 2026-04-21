<?php

namespace App\Modules\ContentSeo\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\SeoScore;
use App\Models\CompetitorAnalysis;
use App\Models\MetaAbTest;
use App\Models\SearchConsoleData;
use App\Models\ContentRefreshLog;
use App\Models\SeoReport;
use App\Services\SeoReportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SeoCommandCenterController extends Controller
{
    /**
     * Display the unified SEO Command Center dashboard with tabs.
     */
    public function index(Request $request, SeoReportService $reportService)
    {
        $activeTab = $request->get('tab', 'overview');
        $period = $request->get('period', '30days');
        
        $days = match ($period) {
            '7days' => 7,
            '30days' => 30,
            '90days' => 90,
            default => 30,
        };
        
        // Overview stats (cached)
        $stats = Cache::remember("seo_command_stats_{$period}", 300, function () use ($days) {
            $publishedCount = Article::where('status', 'published')->count();
            $totalViews = Article::where('status', 'published')->sum('views_count');
            $avgViews = $publishedCount > 0 ? round($totalViews / $publishedCount) : 0;
            $avgSeoScore = round(SeoScore::avg('total_score') ?? 0, 1);
            
            return [
                'published_count' => $publishedCount,
                'total_views' => $totalViews,
                'avg_views' => $avgViews,
                'avg_seo_score' => $avgSeoScore,
            ];
        });
        
        // Module counts for badges
        $moduleCounts = [
            'low_scores' => SeoScore::where('total_score', '<', 60)->count(),
            'competitors' => CompetitorAnalysis::count(),
            'active_tests' => MetaAbTest::where('status', 'running')->count(),
            'search_console' => SearchConsoleData::count(),
            'pending_refresh' => ContentRefreshLog::where('status', 'pending')->count(),
            'reports' => SeoReport::count(),
        ];
        
        // Score distribution for quick stats
        $scoreDistribution = SeoScore::selectRaw("
            CASE
                WHEN total_score >= 80 THEN 'excellent'
                WHEN total_score >= 60 THEN 'good'
                WHEN total_score >= 40 THEN 'average'
                ELSE 'poor'
            END as grade_group,
            COUNT(*) as cnt
        ")->groupBy('grade_group')->pluck('cnt', 'grade_group')->toArray();
        
        // Top articles needing attention
        $lowScoreArticles = SeoScore::with('article:id,title,slug')
            ->where('total_score', '<', 60)
            ->orderBy('total_score', 'asc')
            ->limit(5)
            ->get();
        
        // View trends
        $viewTrends = $reportService->getSiteTrends($days);
        
        return view('admin.seo.command-center', compact(
            'activeTab',
            'period',
            'stats',
            'moduleCounts',
            'scoreDistribution',
            'lowScoreArticles',
            'viewTrends'
        ));
    }
}
