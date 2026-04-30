<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class EnsureEmailWebhookReplayProtection
{
    public function handle(Request $request, Closure $next): Response
    {
        $enabled = (bool) config('email_webhook.replay_protection_enabled', app()->environment('production'));
        if (! $enabled) {
            return $next($request);
        }

        $timestampHeader = (string) config('email_webhook.timestamp_header', 'X-Timestamp');
        $nonceHeader = (string) config('email_webhook.nonce_header', 'X-Nonce');
        $providedTimestamp = (string) $request->header($timestampHeader, '');
        $nonce = (string) $request->header($nonceHeader, '');

        if ($providedTimestamp === '' || $nonce === '') {
            $this->incrementMetric('missing_headers');

            return response()->json([
                'success' => false,
                'error' => 'Missing replay protection headers',
            ], 401);
        }

        if (! ctype_digit($providedTimestamp)) {
            $this->incrementMetric('invalid_timestamp');

            return response()->json([
                'success' => false,
                'error' => 'Invalid timestamp',
            ], 401);
        }

        $timestamp = (int) $providedTimestamp;
        $now = now()->getTimestamp();
        $maxAge = (int) config('email_webhook.max_age_seconds', 300);
        $maxFutureSkew = (int) config('email_webhook.max_future_skew_seconds', 30);

        if (($now - $timestamp) > $maxAge || ($timestamp - $now) > $maxFutureSkew) {
            $this->incrementMetric('timestamp_out_of_window');
            Log::warning('Email webhook replay protection rejected request: timestamp out of window', [
                'ip' => $request->ip(),
                'timestamp' => $timestamp,
                'now' => $now,
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Timestamp out of window',
            ], 401);
        }

        if (strlen($nonce) < 16 || strlen($nonce) > 128) {
            $this->incrementMetric('invalid_nonce');

            return response()->json([
                'success' => false,
                'error' => 'Invalid nonce',
            ], 401);
        }

        $store = (string) config('email_webhook.cache_store', config('cache.default'));
        $nonceTtl = (int) config('email_webhook.nonce_ttl_seconds', 86400);
        $responseTtl = (int) config('email_webhook.response_cache_ttl_seconds', $nonceTtl);

        $nonceKey = 'email_webhook_nonce:'.$nonce;
        $responseKey = 'email_webhook_response:'.$nonce;

        $cache = null;
        $fallbackToArray = false;
        $cacheStart = microtime(true);
        try {
            $cache = Cache::store($store);
            $cache->get('_email_webhook_cache_healthcheck');
        } catch (Throwable $e) {
            $fallbackToArray = true;
            $this->incrementMetric('cache_store_failure');
            Log::critical('Email webhook replay protection cache store failure', [
                'store' => $store,
                'error' => $e->getMessage(),
            ]);
            $cache = Cache::store('array');
        } finally {
            $latencyMs = (microtime(true) - $cacheStart) * 1000;
            $latencyThreshold = (int) config('email_webhook.cache_latency_warn_ms', 50);
            if ($latencyMs >= $latencyThreshold && ! $fallbackToArray) {
                $this->incrementMetric('cache_latency_high');
                Log::warning('Email webhook cache latency high', [
                    'store' => $store,
                    'latency_ms' => (int) $latencyMs,
                ]);
            }
        }

        $cachedResponse = null;
        try {
            $cachedResponse = $cache->get($responseKey);
        } catch (Throwable $e) {
            $this->incrementMetric('cache_get_failure');
            $cachedResponse = null;
        }
        if (is_array($cachedResponse) && isset($cachedResponse['status'], $cachedResponse['body'], $cachedResponse['content_type'])) {
            return response($cachedResponse['body'], (int) $cachedResponse['status'])
                ->header('Content-Type', (string) $cachedResponse['content_type']);
        }

        try {
            $stored = $cache->add($nonceKey, 1, $nonceTtl);
        } catch (Throwable $e) {
            $this->incrementMetric('cache_add_failure');
            Log::critical('Email webhook replay protection cache add failure', [
                'store' => $store,
                'error' => $e->getMessage(),
            ]);
            $stored = true;
        }
        if (! $stored) {
            $this->incrementMetric('nonce_reused');

            return response()->json([
                'success' => true,
                'message' => 'Already processed',
            ], 200);
        }

        $response = $next($request);

        try {
            $cache->put($responseKey, [
                'status' => $response->getStatusCode(),
                'content_type' => $response->headers->get('Content-Type', 'application/json'),
                'body' => $response->getContent(),
            ], $responseTtl);
        } catch (Throwable $e) {
            $this->incrementMetric('cache_put_failure');
        }

        return $response;
    }

    private function incrementMetric(string $metric): void
    {
        $store = (string) config('email_webhook.cache_store', config('cache.default'));
        $key = 'email_webhook_metric:'.$metric.':'.now()->format('YmdH');

        try {
            Cache::store($store)->increment($key);
            Cache::store($store)->put($key, Cache::store($store)->get($key), now()->addDays(2));
        } catch (Throwable $e) {
            try {
                Cache::store('array')->increment($key);
            } catch (Throwable $e2) {
            }
        }
    }
}
