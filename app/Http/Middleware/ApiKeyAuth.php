<?php

namespace App\Http\Middleware;

use App\Models\ApiKey;
use App\Models\ApiUsageLog;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * P8 — Authenticate and rate-limit B2B API requests via ApiKey.
 *
 * Expects: Authorization: Bearer {api_key}
 */
class ApiKeyAuth
{
    public function handle(Request $request, Closure $next): Response
    {
        $bearer = $request->bearerToken();

        if (empty($bearer)) {
            return response()->json(['error' => 'API key required. Pass Authorization: Bearer {key}.'], 401);
        }

        $apiKey = ApiKey::where('key', $bearer)
            ->where('is_active', true)
            ->first();

        if (! $apiKey) {
            return response()->json(['error' => 'Invalid or inactive API key.'], 401);
        }

        // Reset monthly quota if past reset date
        if ($apiKey->usage_reset_at && now()->gt($apiKey->usage_reset_at)) {
            $apiKey->update([
                'usage_this_month' => 0,
                'usage_reset_at' => now()->addMonth(),
            ]);
            $apiKey->refresh();
        }

        if (! $apiKey->hasQuotaRemaining()) {
            return response()->json([
                'error' => 'Monthly quota exceeded.',
                'plan' => $apiKey->plan,
                'limit' => $apiKey->monthly_limit,
                'used' => $apiKey->usage_this_month,
                'upgrade_url' => url('/client/api-keys'),
            ], 429);
        }

        // Check endpoint restriction
        if ($apiKey->allowed_endpoints !== null) {
            $endpoint = $request->path();
            $allowed = $apiKey->allowed_endpoints;

            if (! collect($allowed)->contains(fn ($e) => str_starts_with($endpoint, ltrim($e, '/')))) {
                return response()->json(['error' => 'This endpoint is not included in your plan.'], 403);
            }
        }

        // Attach to request for controllers to use
        $request->attributes->set('api_key', $apiKey);

        $startTime = microtime(true);
        $response = $next($request);
        $latency = (microtime(true) - $startTime) * 1000;

        // Log usage (async-safe: fire and forget)
        $apiKey->incrementUsage();
        ApiUsageLog::create([
            'api_key_id' => $apiKey->id,
            'endpoint' => $request->path(),
            'status_code' => $response->getStatusCode(),
            'latency_ms' => round($latency, 2),
            'date' => now()->toDateString(),
        ]);

        return $response->header('X-RateLimit-Limit', $apiKey->monthly_limit)
            ->header('X-RateLimit-Remaining', max(0, $apiKey->monthly_limit - $apiKey->usage_this_month))
            ->header('X-API-Plan', $apiKey->plan);
    }
}
