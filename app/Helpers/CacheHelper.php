<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Cache;

class CacheHelper
{
    /**
     * Clear all navigation-related caches
     *
     * @return void
     */
    public static function clearNavigationCache(): void
    {
        Cache::forget('bizmark_nav_counts');
        Cache::forget('bizmark_permit_notifications');
        Cache::forget('bizmark_other_notifications');
    }

    /**
     * Clear specific cache keys
     *
     * @param string|array $keys
     * @return void
     */
    public static function clearCache($keys): void
    {
        $keys = is_array($keys) ? $keys : [$keys];
        
        foreach ($keys as $key) {
            Cache::forget($key);
        }
    }
}
