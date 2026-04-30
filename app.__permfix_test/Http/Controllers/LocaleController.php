<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class LocaleController extends Controller
{
    /**
     * Change the application locale
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function setLocale(Request $request, string $locale)
    {
        // Validate locale
        $availableLocales = config('app.available_locales', ['id', 'en']);

        if (! in_array($locale, $availableLocales)) {
            abort(400, 'Invalid locale');
        }

        // Store locale in session
        Session::put('locale', $locale);

        // Store market segment based on locale
        $marketSegment = $locale === 'en' ? 'pma' : 'local';
        Session::put('market_segment', $marketSegment);

        $previousUrl = $request->headers->get('referer') ?? url()->previous();
        $parsedPrevious = is_string($previousUrl) ? parse_url($previousUrl) : null;
        $previousPath = is_array($parsedPrevious) ? ($parsedPrevious['path'] ?? '/') : '/';
        $previousQuery = (is_array($parsedPrevious) && ! empty($parsedPrevious['query']))
            ? ('?'.$parsedPrevious['query'])
            : '';

        $previousPath = '/'.ltrim($previousPath, '/');
        $normalizedPath = rtrim($previousPath, '/');
        if ($normalizedPath === '') {
            $normalizedPath = '/';
        }

        $previousIsEnglish = ($normalizedPath === '/en') || str_starts_with($normalizedPath, '/en/');
        $pathWithoutPrefix = $previousIsEnglish
            ? (substr($normalizedPath, 3) ?: '/')
            : $normalizedPath;

        $redirectTo = function (string $path) use ($previousQuery) {
            return redirect()->to($path.$previousQuery);
        };

        // Keep the user on the equivalent page when possible.
        // For pages without a safe 1:1 mapping, fall back to the locale landing.
        if ($locale === 'en') {
            if ($previousIsEnglish) {
                return $redirectTo($normalizedPath);
            }

            // ID -> EN mappings
            if (str_starts_with($normalizedPath, '/blog')) {
                return $redirectTo($normalizedPath === '/' ? '/en' : '/en'.$normalizedPath);
            }

            if ($normalizedPath === '/kebijakan-privasi') {
                return $redirectTo('/en/privacy-policy');
            }

            if ($normalizedPath === '/syarat-ketentuan') {
                return $redirectTo('/en/terms-conditions');
            }

            if (str_starts_with($normalizedPath, '/layanan')) {
                $servicesSuffix = substr($normalizedPath, strlen('/layanan'));
                $servicesSuffix = $servicesSuffix === false ? '' : $servicesSuffix;

                return $redirectTo('/en/services'.$servicesSuffix);
            }

            return redirect()->route('landing.en');
        }

        // locale === 'id'
        if (! $previousIsEnglish) {
            return $redirectTo($normalizedPath);
        }

        // EN -> ID mappings
        if (str_starts_with($pathWithoutPrefix, '/blog')) {
            return $redirectTo($pathWithoutPrefix);
        }

        if ($pathWithoutPrefix === '/privacy-policy') {
            return $redirectTo('/kebijakan-privasi');
        }

        if ($pathWithoutPrefix === '/terms-conditions') {
            return $redirectTo('/syarat-ketentuan');
        }

        if (str_starts_with($pathWithoutPrefix, '/services')) {
            $servicesSuffix = substr($pathWithoutPrefix, strlen('/services'));
            $servicesSuffix = $servicesSuffix === false ? '' : $servicesSuffix;

            return $redirectTo('/layanan'.$servicesSuffix);
        }

        if ($pathWithoutPrefix === '/') {
            return $redirectTo('/');
        }

        return redirect()->route('landing.id');
    }
}
