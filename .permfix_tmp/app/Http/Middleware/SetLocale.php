<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Handle an incoming request.
     * 
     * Priority: Route Param > Session > Browser > Default
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string|null  $locale
     */
    public function handle(Request $request, Closure $next, $locale = null): Response
    {
        // Available locales
        $availableLocales = config('app.available_locales', ['id', 'en']);
        
        // Priority 1: Explicit locale from route parameter or query
        if ($locale && in_array($locale, $availableLocales)) {
            App::setLocale($locale);
            Session::put('locale', $locale);
            
            // Store market segment based on locale
            $marketSegment = $locale === 'en' ? 'pma' : 'local';
            Session::put('market_segment', $marketSegment);
            
            return $next($request);
        }
        
        // Check query string (?lang=en)
        if ($request->has('lang') && in_array($request->get('lang'), $availableLocales)) {
            $queryLocale = $request->get('lang');
            App::setLocale($queryLocale);
            Session::put('locale', $queryLocale);
            Session::put('market_segment', $queryLocale === 'en' ? 'pma' : 'local');
            return $next($request);
        }
        
        // Priority 2: Session locale (user previously selected)
        if (Session::has('locale')) {
            $sessionLocale = Session::get('locale');
            if (in_array($sessionLocale, $availableLocales)) {
                App::setLocale($sessionLocale);
                return $next($request);
            }
        }
        
        // Priority 3: Browser Accept-Language header
        $browserLang = $request->getPreferredLanguage($availableLocales);
        
        // Priority 4: Fallback to default locale
        $selectedLocale = $browserLang ?? config('app.fallback_locale', 'id');
        
        // Validate locale
        if (!in_array($selectedLocale, $availableLocales)) {
            $selectedLocale = 'id';
        }
        
        App::setLocale($selectedLocale);
        Session::put('locale', $selectedLocale);
        
        // Store market segment
        $marketSegment = $selectedLocale === 'en' ? 'pma' : 'local';
        Session::put('market_segment', $marketSegment);
        
        return $next($request);
    }
}
