<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
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
        $availableLocales = ['en', 'id'];
        
        if (!in_array($locale, $availableLocales)) {
            abort(400, 'Invalid locale');
        }
        
        // Store locale in session
        Session::put('locale', $locale);
        
        // Redirect back to previous page
        return redirect()->back();
    }
}
