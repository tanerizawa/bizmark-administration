<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DeviceDetection
{
    /**
     * Handle an incoming request.
     * Auto-detect device and force appropriate view
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Skip for authenticated users (they have their own mobile dashboard)
        if ($request->user()) {
            return $next($request);
        }

        // Skip for API and AJAX requests
        if ($request->is('api/*') || $request->ajax() || $request->wantsJson()) {
            return $next($request);
        }

        // Skip for assets and specific paths
        $skipPaths = [
            'storage/*',
            'css/*',
            'js/*',
            'images/*',
            'livewire/*',
            'api/*',
            'sitemap.xml',
            'robots.txt',
            'login*',
            'register*',
            'dashboard*',
            'admin*',
            'client/*'
        ];

        foreach ($skipPaths as $path) {
            if ($request->is($path)) {
                return $next($request);
            }
        }

        // Handle manual preference: ?mobile=1 → redirect to mobile landing
        if ($request->has('mobile') && $request->query('mobile') === '1') {
            $request->session()->put('force_device', 'mobile');
            $locale = $this->determineLocale($request);
            return redirect()->route($locale === 'en' ? 'mobile.landing.en' : 'mobile.landing.id');
        }

        // Handle manual preference: ?desktop=1 → redirect to desktop landing
        if ($request->has('desktop') && $request->query('desktop') === '1') {
            $request->session()->put('force_device', 'desktop');
            $locale = $this->determineLocale($request);
            return redirect()->route($locale === 'en' ? 'landing.en' : 'landing.id');
        }

        // Share device detection info to views (without redirecting)
        $isMobile = $this->detectMobileDevice($request);
        view()->share('isDeviceMobile', $isMobile);

        // Landing pages (/, /en) are fully responsive — NO auto-redirect.
        // Mobile landing (/m/landing, /m/en/landing) is still accessible
        // via ?mobile=1 or direct URL for users who prefer the mobile version.
        // No force redirect in either direction.

        return $next($request);
    }

    /**
     * Detect if device is mobile using multiple methods (best practice)
     *
     * @param Request $request
     * @return bool
     */
    private function detectMobileDevice(Request $request): bool
    {
        // Method 1: Check cookie preference (highest priority)
        if ($request->cookie('force_device')) {
            return $request->cookie('force_device') === 'mobile';
        }

        // Method 2: Check session preference
        if ($request->session()->has('force_device')) {
            return $request->session()->get('force_device') === 'mobile';
        }

        // Method 3: Check screen width from session (set by JavaScript)
        $screenWidth = $request->session()->get('screen_width', 0);
        if ($screenWidth > 0) {
            return $screenWidth < 768; // Tailwind md breakpoint
        }

        // Method 4: User-Agent detection (fallback)
        return $this->isMobileUserAgent($request->header('User-Agent'));
    }

    /**
     * Check if User-Agent indicates mobile device
     * Uses comprehensive mobile detection patterns
     *
     * @param string|null $userAgent
     * @return bool
     */
    private function isMobileUserAgent(?string $userAgent): bool
    {
        if (empty($userAgent)) {
            return false;
        }

        // Mobile indicators (comprehensive list)
        $mobileKeywords = [
            'Mobile', 'Android', 'iPhone', 'iPad', 'iPod',
            'BlackBerry', 'IEMobile', 'Opera Mini', 'Opera Mobi',
            'Windows Phone', 'webOS', 'Symbian', 'Kindle',
            'Palm', 'Tablet', 'Silk', 'PlayBook'
        ];

        foreach ($mobileKeywords as $keyword) {
            if (stripos($userAgent, $keyword) !== false) {
                // Exclude iPad when explicitly testing for tablet
                if ($keyword === 'iPad' && stripos($userAgent, 'Macintosh') !== false) {
                    // Some browsers on iPad report as Macintosh, handle carefully
                    if (stripos($userAgent, 'Touch') !== false) {
                        return true;
                    }
                }
                return true;
            }
        }

        // Check for mobile screen size indicators
        if (preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i', $userAgent)) {
            return true;
        }

        return false;
    }

    /**
     * Determine locale from request
     * Checks route name and path to determine if it's English or Indonesian
     *
     * @param Request $request
     * @return string
     */
    private function determineLocale(Request $request): string
    {
        // Check route name first
        $routeName = $request->route()?->getName();
        if ($routeName && str_contains($routeName, '.en')) {
            return 'en';
        }

        // Check path for /en prefix
        if ($request->is('en') || $request->is('en/*')) {
            return 'en';
        }

        // Check app locale if already set
        $currentLocale = app()->getLocale();
        if ($currentLocale && $currentLocale !== 'en') {
            return $currentLocale;
        }

        // Default to Indonesian
        return 'id';
    }
}
