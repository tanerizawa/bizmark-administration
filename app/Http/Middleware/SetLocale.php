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
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if locale is in session
        if (Session::has('locale')) {
            $locale = Session::get('locale');
        } 
        // Check if locale is in query string (?lang=en)
        elseif ($request->has('lang')) {
            $locale = $request->get('lang');
            Session::put('locale', $locale);
        }
        // Default to Indonesian
        else {
            $locale = 'id';
            Session::put('locale', $locale);
        }

        // Validate locale
        $availableLocales = ['en', 'id'];
        if (!in_array($locale, $availableLocales)) {
            $locale = 'id';
        }

        // Set application locale
        App::setLocale($locale);

        return $next($request);
    }
}
