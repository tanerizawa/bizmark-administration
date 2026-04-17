<?php

namespace Tests\Unit\Support;

use App\Support\SeoDashboardCache;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class SeoDashboardCacheTest extends TestCase
{
    public function test_forget_stats_clears_three_keys(): void
    {
        foreach (['seo_dashboard_stats_7days', 'seo_dashboard_stats_30days', 'seo_dashboard_stats_90days'] as $key) {
            Cache::put($key, ['stub' => true], 3600);
        }

        SeoDashboardCache::forgetStatsCaches();

        foreach (['seo_dashboard_stats_7days', 'seo_dashboard_stats_30days', 'seo_dashboard_stats_90days'] as $key) {
            $this->assertNull(Cache::get($key), "Expected cache key {$key} to be cleared");
        }
    }
}
