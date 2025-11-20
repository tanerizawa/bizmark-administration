<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DetectMobile
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Cek query parameter ?desktop=1 untuk force desktop mode
        if ($request->query('desktop') === '1') {
            session(['force_desktop' => true]);
            session()->forget('prefer_mobile');
        }
        
        // Cek query parameter ?mobile=1 untuk force mobile mode
        if ($request->query('mobile') === '1') {
            session()->forget('force_desktop');
            session(['prefer_mobile' => true]);
        }
        
        // Cek jika user force desktop mode via session
        if (session('force_desktop')) {
            // Jika user akses mobile route tapi force desktop, redirect ke desktop
            if ($request->is('m/*') || $request->is('m')) {
                return redirect('/dashboard');
            }
            return $next($request);
        }
        
        // PRIORITY: Check client-side screen width detection (lebih akurat)
        $screenWidth = session('screen_width', 0);
        $isMobileByScreen = $screenWidth > 0 && $screenWidth < 768;
        
        // Detect mobile device by user agent
        $isMobileByUA = $this->isMobileDevice($request);
        
        // Final decision: Mobile if EITHER screen is small OR user agent is mobile
        // BUT respect user preference if they manually chose
        $isMobile = $isMobileByScreen || $isMobileByUA;
        
        // If screen width indicates desktop (>= 768px), override mobile detection
        if ($screenWidth >= 768) {
            $isMobile = false;
        }
        
        // Share mobile detection ke semua views
        view()->share('isMobile', $isMobile);
        
        // Skip redirect logic for public mobile landing page
        if ($request->is('m/landing')) {
            return $next($request);
        }
        
        // Jika mobile device akses desktop route (bukan /m/*), redirect ke mobile
        // KECUALI jika ada prefer_mobile session (user manually switched)
        if ($isMobile && !session('prefer_mobile') && !$request->is('m/*') && !$request->is('m') && $request->is('dashboard*')) {
            return redirect()->route('mobile.dashboard');
        }
        
        // Jika desktop device (screen >= 768) akses mobile route, redirect ke desktop
        // KECUALI jika user manually prefer mobile OR accessing public mobile landing
        if (!$isMobile && !session('prefer_mobile') && ($request->is('m/*') || $request->is('m')) && !$request->is('m/landing')) {
            return redirect('/dashboard');
        }
        
        return $next($request);
    }
    
    /**
     * Detect if request comes from mobile device
     */
    private function isMobileDevice(Request $request): bool
    {
        $userAgent = $request->header('User-Agent');
        
        // PRIORITY 1: Check mobile device patterns FIRST
        // Android, iOS, dan mobile devices lainnya
        $mobilePatterns = [
            'Android', 'iPhone', 'iPad', 'iPod',
            'BlackBerry', 'IEMobile', 'Opera Mini', 'Opera Mobi',
            'Mobile Safari', 'Windows Phone', 'webOS', 'Mobile'
        ];
        
        foreach ($mobilePatterns as $pattern) {
            if (stripos($userAgent, $pattern) !== false) {
                // Double check: Jika ada "Android" atau "iPhone", pasti mobile
                if (stripos($userAgent, 'Android') !== false || 
                    stripos($userAgent, 'iPhone') !== false ||
                    stripos($userAgent, 'iPad') !== false) {
                    return true;
                }
                // Untuk pattern lain, cek apakah ada "Mobile" keyword
                if (stripos($userAgent, 'Mobile') !== false) {
                    return true;
                }
            }
        }
        
        // PRIORITY 2: Check desktop patterns (to exclude false positives)
        // HANYA jika tidak match mobile patterns di atas
        $desktopPatterns = [
            'Windows NT', 'Macintosh', 'X11; Linux x86_64', 'X11; Ubuntu'
        ];
        
        foreach ($desktopPatterns as $pattern) {
            if (stripos($userAgent, $pattern) !== false) {
                // Jika ada Windows NT atau Macintosh TANPA "Mobile", pasti desktop
                if ((stripos($userAgent, 'Windows NT') !== false || 
                     stripos($userAgent, 'Macintosh') !== false) &&
                    stripos($userAgent, 'Mobile') === false &&
                    stripos($userAgent, 'Android') === false) {
                    return false; // Desktop OS confirmed
                }
            }
        }
        
        // PRIORITY 3: Check screen width from previous visits (if available)
        if (session('screen_width') && session('screen_width') < 768) {
            return true;
        }
        
        return false;
    }
    
    /**
     * Store screen width for better mobile detection
     * Call this via JS: fetch('/api/set-screen-width', {method: 'POST', body: {width: window.innerWidth}})
     */
    public static function setScreenWidth(Request $request)
    {
        $width = $request->input('width');
        if ($width) {
            session(['screen_width' => $width]);
        }
        return response()->json(['success' => true]);
    }
}
