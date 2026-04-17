<?php

namespace App\Http\Controllers;

use App\Services\Dashboard\DashboardDataService;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    public function __construct(private DashboardDataService $dashboardData) {}

    /**
     * Dashboard index with caching for performance
     * Cache duration: 5 minutes
     */
    public function index()
    {
        $cacheKey = 'dashboard_data_'.auth()->id();
        $cacheDuration = 5;

        $data = Cache::remember($cacheKey, $cacheDuration * 60, fn () => $this->dashboardData->build());

        \Log::info('Dashboard data loaded', [
            'user_id' => auth()->id(),
            'critical_alerts_count' => $data['criticalAlerts']['total_urgent'] ?? 0,
            'data_keys' => array_keys($data),
        ]);

        return view('dashboard', $data);
    }

    /**
     * Clear dashboard cache manually (useful after data updates)
     */
    public function clearCache()
    {
        $cacheKey = 'dashboard_data_'.auth()->id();
        Cache::forget($cacheKey);

        return redirect()->route('dashboard')->with('success', 'Dashboard cache cleared!');
    }
}
