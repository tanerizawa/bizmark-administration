<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        if (!auth()->check()) {
            // Redirect to landing page alih-alih login page agar admin secret path tetap tersembunyi
            return redirect()->route('landing.id')->with('warning', 'Silakan login terlebih dahulu untuk mengakses halaman ini.');
        }

        if (!auth()->user()->can($permission)) {
            abort(403, 'Anda tidak memiliki akses untuk halaman ini.');
        }

        return $next($request);
    }
}
