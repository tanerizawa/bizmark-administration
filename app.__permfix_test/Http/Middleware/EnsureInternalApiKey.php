<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureInternalApiKey
{
    public function handle(Request $request, Closure $next): Response
    {
        $configuredKey = (string) config('app.internal_api_key', env('INTERNAL_API_KEY', ''));
        $providedKey = (string) $request->header('X-Internal-Api-Key', '');

        if ($configuredKey === '' || $providedKey === '') {
            abort(401, 'Unauthorized');
        }

        if (! hash_equals($configuredKey, $providedKey)) {
            abort(403, 'Forbidden');
        }

        return $next($request);
    }
}
