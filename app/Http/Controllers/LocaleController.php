<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;

class LocaleController extends Controller
{
    /**
     * Change the application locale
     *
     * @param string $locale
     * @return \Illuminate\Http\RedirectResponse
     */
    public function setLocale($locale)
    {
        // Validate locale
        $availableLocales = config('app.available_locales', ['id', 'en']);
        
        if (!in_array($locale, $availableLocales)) {
            abort(400, 'Invalid locale');
        }
        
        // Store locale in session
        Session::put('locale', $locale);
        
        // Store market segment based on locale
        $marketSegment = $locale === 'en' ? 'pma' : 'local';
        Session::put('market_segment', $marketSegment);
        
        // Redirect to locale-specific landing page
        // Indonesian uses root /, English uses /en
        if ($locale === 'en') {
            return redirect()->route('landing.en');
        } else {
            return redirect()->route('landing.id'); // Root for Indonesian
        }
    }
}
