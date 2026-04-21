<?php

namespace App\Modules\ContentSeo\Controllers\Admin\Seo;

use App\Modules\ContentSeo\Controllers\Admin\Concerns\SeoAdminFlashRedirect;
use App\Http\Controllers\Controller;
use App\Models\KeywordPositionHistory;
use App\Modules\ContentSeo\Services\CompetitiveIntelligenceService;
use Illuminate\Http\Request;

class SeoPositionsController extends Controller
{
    use SeoAdminFlashRedirect;

    // ─────────────────────────────────────────────────────────────────
    // POSITION TRACKING (Phase 5: Intelligence)
    // ─────────────────────────────────────────────────────────────────

    /**
     * Position tracking dashboard.
     */
    public function positions(Request $request, CompetitiveIntelligenceService $intelligence)
    {
        $period = $request->get('period', '7days');
        $days = match ($period) {
            '7days' => 7,
            '30days' => 30,
            '90days' => 90,
            default => 7,
        };

        // Get summary stats
        $summary = $intelligence->getPositionTrackingSummary();

        // Get latest positions per keyword
        $latestPositions = KeywordPositionHistory::latestPerKeyword()
            ->orderBy('position')
            ->orderByDesc('tracked_at')
            ->paginate(25);

        // Get big movers (significant changes)
        $bigMovers = KeywordPositionHistory::lastDays($days)
            ->significantChanges(5)
            ->orderByDesc('position_change')
            ->limit(10)
            ->get();

        // Position tier distribution
        $tierDistribution = KeywordPositionHistory::latestPerKeyword()
            ->selectRaw("
                CASE 
                    WHEN position <= 3 THEN 'top3'
                    WHEN position <= 10 THEN 'page1'
                    WHEN position <= 20 THEN 'page2'
                    WHEN position <= 30 THEN 'page3'
                    WHEN position IS NOT NULL THEN 'beyond'
                    ELSE 'notranking'
                END as tier,
                COUNT(*) as cnt
            ")
            ->groupBy('tier')
            ->pluck('cnt', 'tier')
            ->toArray();

        // Recent tracking count
        $recentTracking = [
            'today' => KeywordPositionHistory::today()->count(),
            'yesterday' => KeywordPositionHistory::whereDate('tracked_at', now()->subDay())->count(),
            'last_week' => KeywordPositionHistory::lastDays(7)->count(),
        ];

        // Trend chart data (average position over time)
        $trendData = KeywordPositionHistory::lastDays($days)
            ->whereNotNull('position')
            ->selectRaw('DATE(tracked_at) as date, AVG(position) as avg_position, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->map(fn($r) => [
                'date' => $r->date,
                'avg_position' => round($r->avg_position, 1),
                'count' => $r->count,
            ])
            ->toArray();

        return view('admin.seo.positions', compact(
            'summary', 'latestPositions', 'bigMovers', 'tierDistribution',
            'recentTracking', 'trendData', 'period', 'days'
        ));
    }

    /**
     * Position trend for a specific keyword.
     */
    public function positionTrend(string $keyword, CompetitiveIntelligenceService $intelligence)
    {
        $keyword = urldecode($keyword);
        $trend = $intelligence->getKeywordTrend($keyword, 30);

        return view('admin.seo.position-trend', compact('keyword', 'trend'));
    }

    /**
     * Trigger position tracking manually.
     */
    public function trackPositions(Request $request, CompetitiveIntelligenceService $intelligence)
    {
        $keyword = $request->get('keyword');
        $limit = (int) $request->get('limit', 20);

        if ($keyword) {
            // Track single keyword
            $result = $intelligence->trackPosition($keyword);
            
            if ($result) {
                return $this->seoBackFlash('success', "Position tracked for '{$keyword}': #{$result->position}");
            }
            
            return $this->seoBackFlash('error', "Failed to track position for '{$keyword}'");
        }

        // Batch tracking
        $results = $intelligence->trackAllPositions($limit);

        return $this->seoBackFlash(
            'success',
            "Tracked {$results['tracked']} keywords. " .
            "Skipped {$results['skipped']}. " .
            "Created {$results['alerts_created']} alerts."
        );
    }
}

