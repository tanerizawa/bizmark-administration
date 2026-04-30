<?php

namespace App\Support;

use Illuminate\Support\Facades\Cache;

/**
 * Kunci cache statistik dashboard SEO (7/30/90 hari) dipakai di beberapa controller & SeoFixService.
 */
final class SeoDashboardCache
{
    public static function forgetStatsCaches(): void
    {
        Cache::forget('seo_dashboard_stats_7days');
        Cache::forget('seo_dashboard_stats_30days');
        Cache::forget('seo_dashboard_stats_90days');
    }
}
