<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AutoPostLog;
use App\Models\AutoPostSchedule;
use App\Models\ArticleTopic;
use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AutoPostAnalyticsController extends Controller
{
    public function index(Request $request)
    {
        $period = $request->get('period', '7days');
        
        [$startDate, $endDate] = $this->getPeriodDates($period);
        
        // Overview stats
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
        
        // Success rate
        $totalProcessed = $stats['completed_schedules'] + $stats['failed_schedules'];
        $stats['success_rate'] = $totalProcessed > 0 
            ? round(($stats['completed_schedules'] / $totalProcessed) * 100, 1)
            : 0;
        
        // Daily generation chart
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
        
        // Category distribution
        $categoryDistribution = Article::select('category', DB::raw('COUNT(*) as count'))
            ->where('created_at', '>=', $startDate)
            ->where('created_at', '<=', $endDate)
            ->where('source_type', 'auto-generated')
            ->groupBy('category')
            ->get();
        
        // Recent logs
        $recentLogs = AutoPostLog::with(['schedule.topic'])
            ->where('created_at', '>=', $startDate)
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get();
        
        // Performance metrics
        $performanceMetrics = $this->calculatePerformanceMetrics($startDate, $endDate);
        
        return view('admin.auto-post.analytics', compact(
            'stats',
            'dailyGeneration',
            'categoryDistribution',
            'recentLogs',
            'performanceMetrics',
            'period'
        ));
    }
    
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
    
    private function calculatePerformanceMetrics($startDate, $endDate): array
    {
        $logs = AutoPostLog::whereBetween('created_at', [$startDate, $endDate])->get();
        
        $totalGenerations = $logs->where('event', 'generation_started')->count();
        $successGenerations = $logs->where('event', 'article_created')->count();
        $failedGenerations = $logs->where('event', 'generation_failed')->count();
        
        $qualityChecksFailed = $logs->where('event', 'quality_check_failed')->count();
        $duplicatesDetected = $logs->where('event', 'duplicate_detected')->count();
        
        return [
            'total_attempts' => $totalGenerations,
            'successful' => $successGenerations,
            'failed' => $failedGenerations,
            'quality_issues' => $qualityChecksFailed,
            'duplicates' => $duplicatesDetected,
            'success_percentage' => $totalGenerations > 0 
                ? round(($successGenerations / $totalGenerations) * 100, 1)
                : 0,
        ];
    }
}
