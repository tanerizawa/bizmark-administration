<?php

namespace App\Modules\ContentSeo\Controllers\Admin\Concerns;

use Illuminate\Http\RedirectResponse;

/**
 * Pola redirect + flash konsisten untuk modul admin SEO.
 */
trait SeoAdminFlashRedirect
{
    protected function seoRouteFlash(string $route, string $level, string $message, mixed $routeParameters = null): RedirectResponse
    {
        if ($routeParameters === null) {
            return redirect()->route($route)->with($level, $message);
        }

        return redirect()->route($route, $routeParameters)->with($level, $message);
    }

    protected function seoBackFlash(string $level, string $message): RedirectResponse
    {
        return back()->with($level, $message);
    }
}
