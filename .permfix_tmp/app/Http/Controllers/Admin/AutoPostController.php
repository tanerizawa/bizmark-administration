<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AutoPostConfig;
use App\Models\AutoPostLog;
use App\Models\AutoPostSchedule;
use App\Models\ArticleTopic;
use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class AutoPostController extends Controller
{
    /**
     * Display the unified auto-post dashboard with tabs.
     */
    public function index(Request $request)
    {
        $activeTab = $request->get('tab', 'config');
        
        // Config data
        $config = AutoPostConfig::current();
        
        // Analytics/Overview data
        $period = $request->get('period', '7days');
        [$startDate, $endDate] = $this->getPeriodDates($period);
        
        $analytics = $this->getAnalyticsData($startDate, $endDate, $period);
        
        // Topics data
        $topicsData = $this->getTopicsData($request);
        
        // Schedules data
        $schedulesData = $this->getSchedulesData($request);
        
        return view('admin.auto-post.index', array_merge([
            'activeTab' => $activeTab,
            'config' => $config,
            'period' => $period,
        ], $analytics, $topicsData, $schedulesData));
    }
    
    /**
     * Get analytics data for overview.
     */
    private function getAnalyticsData($startDate, $endDate, $period): array
    {
        $stats = [
            'total_articles' => Article::where('created_at', '>=', $startDate)
                ->where('created_at', '<=', $endDate)
                ->where('source_type', 'auto-generated')
                ->count(),
            
            'total_topics' => ArticleTopic::count(),
            'available_topics' => ArticleTopic::where('status', 'pending')->whereNull('scheduled_for')->count(),
            'used_topics' => ArticleTopic::where('status', 'published')->count(),
            
            'pending_schedules' => AutoPostSchedule::where('status', 'pending')->count(),
            'completed_schedules' => AutoPostSchedule::where('status', 'completed')
                ->where('completed_at', '>=', $startDate)
                ->count(),
            'failed_schedules' => AutoPostSchedule::where('status', 'failed')
                ->where('completed_at', '>=', $startDate)
                ->count(),
        ];
        
        $totalProcessed = $stats['completed_schedules'] + $stats['failed_schedules'];
        $stats['success_rate'] = $totalProcessed > 0 
            ? round(($stats['completed_schedules'] / $totalProcessed) * 100, 1)
            : 0;
        
        $dailyGeneration = AutoPostSchedule::select(
                DB::raw('DATE(completed_at) as date'),
                DB::raw('COUNT(*) as count'),
                DB::raw("SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as success"),
                DB::raw("SUM(CASE WHEN status = 'failed' THEN 1 ELSE 0 END) as failed")
            )
            ->whereNotNull('completed_at')
            ->where('completed_at', '>=', $startDate)
            ->where('completed_at', '<=', $endDate)
            ->groupBy(DB::raw('DATE(completed_at)'))
            ->orderBy('date')
            ->get();
        
        $categoryDistribution = Article::select('category', DB::raw('COUNT(*) as count'))
            ->where('created_at', '>=', $startDate)
            ->where('created_at', '<=', $endDate)
            ->where('source_type', 'auto-generated')
            ->groupBy('category')
            ->get();
        
        $performanceMetrics = $this->calculatePerformanceMetrics($startDate, $endDate);
        
        return [
            'stats' => $stats,
            'dailyGeneration' => $dailyGeneration,
            'categoryDistribution' => $categoryDistribution,
            'performanceMetrics' => $performanceMetrics,
        ];
    }
    
    /**
     * Get topics data for topics tab.
     */
    private function getTopicsData(Request $request): array
    {
        $query = ArticleTopic::query();
        
        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'ILIKE', "%{$search}%")
                    ->orWhere('description', 'ILIKE', "%{$search}%")
                    ->orWhereRaw("COALESCE(keywords::text, '') ILIKE ?", ["%{$search}%"]);
            });
        }
        
        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }
        
        if ($category = $request->get('category')) {
            $query->where('category', $category);
        }

        if ($market = $request->get('market')) {
            $query->where('target_market', $market);
        }
        
        $topics = $query->orderBy('created_at', 'desc')->paginate(20);
        
        $topicStats = [
            'total' => ArticleTopic::count(),
            'available' => ArticleTopic::where('status', 'pending')->whereNull('scheduled_for')->count(),
            'scheduled' => ArticleTopic::whereNotNull('scheduled_for')->whereIn('status', ['pending', 'scheduled'])->count(),
            'used' => ArticleTopic::where('status', 'published')->count(),
            'local' => ArticleTopic::where('target_market', 'local')->count(),
            'pma' => ArticleTopic::where('target_market', 'pma')->count(),
            'both' => ArticleTopic::where('target_market', 'both')->count(),
        ];
        
        $categories = ArticleTopic::distinct('category')->pluck('category')->filter();
        $markets = collect(['local', 'pma', 'both']);
        
        return [
            'topics' => $topics,
            'topicStats' => $topicStats,
            'categories' => $categories,
            'markets' => $markets,
        ];
    }
    
    /**
     * Get schedules data for schedules tab.
     */
    private function getSchedulesData(Request $request): array
    {
        $query = AutoPostSchedule::with([
            'topic' => function ($q) {
                $q->withTrashed();
            },
            'article',
        ]);
        $scheduleDateColumn = $this->getScheduleDateColumn();

        if ($search = $request->get('schedule_search')) {
            $query->whereHas('topic', function ($q) use ($search) {
                $q->withTrashed()->where('title', 'ILIKE', "%{$search}%");
            });
        }

        if ($dateFrom = $request->get('schedule_date_from')) {
            $query->whereDate($scheduleDateColumn, '>=', $dateFrom);
        }

        if ($dateTo = $request->get('schedule_date_to')) {
            $query->whereDate($scheduleDateColumn, '<=', $dateTo);
        }
        
        if ($status = $request->get('schedule_status')) {
            $query->where('status', $status);
        }
        
        $schedules = $query->orderBy($scheduleDateColumn, 'desc')->paginate(20);
        
        $scheduleStats = [
            'pending' => AutoPostSchedule::where('status', 'pending')->count(),
            'processing' => AutoPostSchedule::where('status', 'processing')->count(),
            'completed' => AutoPostSchedule::where('status', 'completed')->count(),
            'failed' => AutoPostSchedule::where('status', 'failed')->count(),
        ];
        
        return [
            'schedules' => $schedules,
            'scheduleStats' => $scheduleStats,
            'scheduleDateColumn' => $scheduleDateColumn,
        ];
    }
    
    /**
     * Get period dates for analytics.
     */
    private function getPeriodDates($period): array
    {
        $endDate = Carbon::now();
        
        switch ($period) {
            case '24hours':
                $startDate = Carbon::now()->subDay();
                break;
            case '7days':
                $startDate = Carbon::now()->subDays(7);
                break;
            case '30days':
                $startDate = Carbon::now()->subDays(30);
                break;
            case '90days':
                $startDate = Carbon::now()->subDays(90);
                break;
            default:
                $startDate = Carbon::now()->subDays(7);
        }
        
        return [$startDate, $endDate];
    }
    
    /**
     * Calculate performance metrics.
     */
    private function calculatePerformanceMetrics($startDate, $endDate): array
    {
        $scheduleDateColumn = $this->getScheduleDateColumn();

        return [
            'total_attempts' => AutoPostSchedule::whereBetween($scheduleDateColumn, [$startDate, $endDate])->count(),
            'successful' => AutoPostSchedule::where('status', 'completed')
                ->whereBetween('completed_at', [$startDate, $endDate])
                ->count(),
            'failed' => AutoPostSchedule::where('status', 'failed')
                ->whereBetween('completed_at', [$startDate, $endDate])
                ->count(),
            'quality_issues' => AutoPostLog::where('level', 'warning')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->count(),
            'duplicates' => AutoPostLog::where('message', 'like', '%duplicate%')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->count(),
        ];
    }

    /**
     * Resolve schedule date column safely across schema variations.
     */
    private function getScheduleDateColumn(): string
    {
        if (Schema::hasColumn('auto_post_schedules', 'scheduled_at')) {
            return 'scheduled_at';
        }

        return 'scheduled_for';
    }
}
