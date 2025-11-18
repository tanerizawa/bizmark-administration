<?php

if (!function_exists('mobile_route')) {
    /**
     * Generate mobile route URL with fallback
     * Best practice: graceful degradation jika route tidak ada
     * 
     * @param string $name Route name (dengan atau tanpa prefix mobile.)
     * @param mixed $parameters Route parameters
     * @param string|null $fallback Fallback route jika route utama tidak ada
     * @return string
     */
    function mobile_route(string $name, $parameters = [], ?string $fallback = null): string
    {
        // Add mobile. prefix if not present
        if (!str_starts_with($name, 'mobile.')) {
            $name = 'mobile.' . $name;
        }

        // Try primary route
        if (Route::has($name)) {
            return route($name, $parameters);
        }

        // Try with .index suffix
        if (Route::has($name . '.index')) {
            return route($name . '.index', $parameters);
        }

        // Try with .show suffix
        if (Route::has($name . '.show')) {
            return route($name . '.show', $parameters);
        }

        // Try fallback route
        if ($fallback && Route::has($fallback)) {
            return route($fallback, $parameters);
        }

        // Default fallback ke mobile dashboard
        if (Route::has('mobile.dashboard')) {
            return route('mobile.dashboard');
        }

        // Last resort: return /m
        return url('/m');
    }
}

if (!function_exists('mobile_route_exists')) {
    /**
     * Check if mobile route exists
     * 
     * @param string $name Route name
     * @return bool
     */
    function mobile_route_exists(string $name): bool
    {
        if (!str_starts_with($name, 'mobile.')) {
            $name = 'mobile.' . $name;
        }

        return Route::has($name) 
            || Route::has($name . '.index') 
            || Route::has($name . '.show');
    }
}

if (!function_exists('mobile_api_url')) {
    /**
     * Generate mobile API endpoint URL
     * Best practice: API-first approach
     * 
     * @param string $endpoint API endpoint path
     * @return string
     */
    function mobile_api_url(string $endpoint): string
    {
        return url('/api/mobile/' . ltrim($endpoint, '/'));
    }
}
