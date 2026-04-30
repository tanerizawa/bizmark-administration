<?php

namespace App\Http\Controllers;

use App\Services\Dashboard\DashboardDataService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    public function __construct(private DashboardDataService $dashboardData) {}

    /**
     * Dashboard index with caching for performance
     * Cache duration: 5 minutes
     *
     * FIX (BUG-02): Use cache tags so we can invalidate all dashboard caches at once
     * when underlying data changes (projects, payments, expenses, etc.)
     */
    public function index()
    {
        $cacheKey = 'dashboard_data_'.auth()->id();
        $cacheDuration = 5;

        $data = Cache::tags(['dashboard'])->remember($cacheKey, $cacheDuration * 60, fn () => $this->dashboardData->build());

        Log::info('Dashboard data loaded', [
            'user_id' => auth()->id(),
            'critical_alerts_count' => $data['criticalAlerts']['total_urgent'] ?? 0,
            'data_keys' => array_keys($data),
        ]);

        return view('dashboard', $data);
    }

    /**
     * Clear dashboard cache for ALL users (not just current user)
     * Call this after any data update that affects dashboard KPIs
     *
     * FIX (BUG-02): Using cache tags to flush all dashboard caches at once
     * instead of only clearing the current user's cache
     */
    public function clearCache()
    {
        Cache::tags(['dashboard'])->flush();

        return redirect()->route('dashboard')->with('success', 'Semua cache dashboard berhasil dibersihkan!');
    }
}
