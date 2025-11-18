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
        // Cek jika user force desktop mode via session
        if (session('force_desktop')) {
            // Jika user akses mobile route tapi force desktop, redirect ke desktop
            if ($request->is('m/*')) {
                return redirect('/dashboard');
            }
            return $next($request);
        }
        
        // Detect mobile device
        $isMobile = $this->isMobileDevice($request);
        
        // Share mobile detection ke semua views
        view()->share('isMobile', $isMobile);
        
        // Jika mobile device akses desktop route (bukan /m/*), redirect ke mobile
        if ($isMobile && !$request->is('m/*') && $request->is('dashboard*')) {
            return redirect()->route('mobile.dashboard');
        }
        
        // Jika desktop device akses mobile route, redirect ke desktop
        if (!$isMobile && $request->is('m/*') && !session('force_mobile')) {
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
        
        // Mobile patterns
        $mobilePatterns = [
            'Mobile', 'Android', 'iPhone', 'iPad', 'iPod',
            'BlackBerry', 'IEMobile', 'Opera Mini', 'Opera Mobi'
        ];
        
        foreach ($mobilePatterns as $pattern) {
            if (stripos($userAgent, $pattern) !== false) {
                return true;
            }
        }
        
        // Check screen width from previous visits (if available)
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
