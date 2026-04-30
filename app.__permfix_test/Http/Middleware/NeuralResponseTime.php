<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

/**
 * NeuralResponseTime Middleware
 *
 * Monitor dan classify response times berdasarkan neural processing thresholds.
 * Optimal response time: <300ms (immediate neural response)
 *
 * Response Time Classifications:
 * - <100ms: Instant (feels seamless)
 * - <300ms: Immediate (optimal)
 * - <1000ms: Acceptable (noticeable)
 * - >1000ms: Slow (requires indicator)
 *
 * @author Bizmark.ID Development Team
 *
 * @version 1.0.0
 */
class NeuralResponseTime
{
    /**
     * Response time thresholds (milliseconds)
     */
    const INSTANT = 100;

    const IMMEDIATE = 300;

    const ACCEPTABLE = 1000;

    const SLOW = 3000;

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Start timing
        $startTime = microtime(true);

        // Process request
        $response = $next($request);

        // Calculate response time
        $endTime = microtime(true);
        $responseTime = ($endTime - $startTime) * 1000; // Convert to milliseconds

        // Classify response time
        $classification = $this->classifyResponseTime($responseTime);

        // Add headers
        $response->headers->set('X-Neural-Response-Time', round($responseTime, 2).'ms');
        $response->headers->set('X-Neural-Classification', $classification['label']);
        $response->headers->set('X-Neural-Status', $classification['status']);

        // Log slow responses
        if ($this->shouldLogResponse($responseTime)) {
            $this->logSlowResponse($request, $responseTime, $classification);
        }

        // Add performance hints to response
        if ($request->wantsJson()) {
            $this->addPerformanceMetrics($response, $responseTime, $classification);
        }

        return $response;
    }

    /**
     * Classify response time berdasarkan neural thresholds
     *
     * @param  float  $responseTime  Response time in milliseconds
     * @return array Classification details
     */
    protected function classifyResponseTime(float $responseTime): array
    {
        if ($responseTime < self::INSTANT) {
            return [
                'label' => 'instant',
                'status' => 'excellent',
                'color' => 'success',
                'description' => 'Seamless experience',
                'recommendation' => 'Maintain current performance',
            ];
        }

        if ($responseTime < self::IMMEDIATE) {
            return [
                'label' => 'immediate',
                'status' => 'optimal',
                'color' => 'success',
                'description' => 'Optimal neural response',
                'recommendation' => 'Good performance',
            ];
        }

        if ($responseTime < self::ACCEPTABLE) {
            return [
                'label' => 'acceptable',
                'status' => 'warning',
                'color' => 'warning',
                'description' => 'Noticeable delay',
                'recommendation' => 'Consider optimization',
            ];
        }

        if ($responseTime < self::SLOW) {
            return [
                'label' => 'slow',
                'status' => 'poor',
                'color' => 'danger',
                'description' => 'Requires loading indicator',
                'recommendation' => 'Optimize immediately',
            ];
        }

        return [
            'label' => 'very_slow',
            'status' => 'critical',
            'color' => 'danger',
            'description' => 'Critical performance issue',
            'recommendation' => 'URGENT: Major optimization needed',
        ];
    }

    /**
     * Determine if response should be logged
     */
    protected function shouldLogResponse(float $responseTime): bool
    {
        // Check if monitoring enabled
        if (! Config::get('neuroscience.response_time.enable_monitoring', true)) {
            return false;
        }

        // Check if slow response logging enabled
        if (! Config::get('neuroscience.response_time.log_slow_responses', true)) {
            return false;
        }

        // Get threshold from config
        $threshold = Config::get('neuroscience.response_time.log_threshold', self::IMMEDIATE);

        return $responseTime > $threshold;
    }

    /**
     * Log slow response for analysis
     */
    protected function logSlowResponse(Request $request, float $responseTime, array $classification): void
    {
        $logData = [
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'route' => $request->route() ? $request->route()->getName() : 'unknown',
            'response_time_ms' => round($responseTime, 2),
            'classification' => $classification['label'],
            'status' => $classification['status'],
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'timestamp' => now()->toIso8601String(),
        ];

        // Log dengan level sesuai severity
        if ($classification['status'] === 'critical') {
            Log::critical('Neural Response Time: Critical Performance Issue', $logData);
        } elseif ($classification['status'] === 'poor') {
            Log::warning('Neural Response Time: Slow Response', $logData);
        } else {
            Log::info('Neural Response Time: Acceptable Delay', $logData);
        }
    }

    /**
     * Add performance metrics to JSON response
     */
    protected function addPerformanceMetrics(Response $response, float $responseTime, array $classification): void
    {
        $content = $response->getContent();

        if (! $content) {
            return;
        }

        $data = json_decode($content, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return;
        }

        // Add performance metadata
        $data['_performance'] = [
            'response_time_ms' => round($responseTime, 2),
            'classification' => $classification['label'],
            'status' => $classification['status'],
            'threshold' => [
                'instant' => self::INSTANT,
                'immediate' => self::IMMEDIATE,
                'acceptable' => self::ACCEPTABLE,
            ],
        ];

        $response->setContent(json_encode($data));
    }

    /**
     * Terminate middleware
     *
     * Can be used for additional analytics/monitoring after response sent
     */
    public function terminate(Request $request, Response $response): void
    {
        // Extract response time from header
        $responseTimeHeader = $response->headers->get('X-Neural-Response-Time');

        if (! $responseTimeHeader) {
            return;
        }

        $responseTime = (float) str_replace('ms', '', $responseTimeHeader);

        // Track metrics (could send to analytics service)
        $this->trackPerformanceMetrics($request, $responseTime);
    }

    /**
     * Track performance metrics for analytics
     */
    protected function trackPerformanceMetrics(Request $request, float $responseTime): void
    {
        // Check if analytics tracking enabled
        if (! Config::get('neuroscience.analytics.track_attention', true)) {
            return;
        }

        // Example: Send to analytics service
        // This could be Google Analytics, Mixpanel, custom analytics, etc.

        try {
            // Store metrics in cache for aggregation
            $cacheKey = 'neural_metrics:'.date('Y-m-d-H');
            $metrics = cache()->get($cacheKey, []);

            $metrics[] = [
                'route' => $request->route() ? $request->route()->getName() : 'unknown',
                'response_time' => $responseTime,
                'timestamp' => now()->timestamp,
            ];

            // Keep last 1000 metrics per hour
            if (count($metrics) > 1000) {
                $metrics = array_slice($metrics, -1000);
            }

            cache()->put($cacheKey, $metrics, now()->addHours(24));

        } catch (\Exception $e) {
            // Silent fail - don't break request for analytics
            Log::debug('Failed to track neural metrics: '.$e->getMessage());
        }
    }

    /**
     * Get performance statistics for a route
     *
     * @param  int  $hours  Number of hours to analyze
     */
    public static function getRouteStatistics(string $routeName, int $hours = 24): array
    {
        $statistics = [
            'route' => $routeName,
            'total_requests' => 0,
            'average_response_time' => 0,
            'min_response_time' => 0,
            'max_response_time' => 0,
            'instant_count' => 0,
            'immediate_count' => 0,
            'acceptable_count' => 0,
            'slow_count' => 0,
            'percentiles' => [],
        ];

        $allMetrics = [];

        // Collect metrics from last N hours
        for ($i = 0; $i < $hours; $i++) {
            $hour = now()->subHours($i)->format('Y-m-d-H');
            $cacheKey = 'neural_metrics:'.$hour;
            $hourMetrics = cache()->get($cacheKey, []);

            // Filter by route
            $routeMetrics = array_filter($hourMetrics, function ($metric) use ($routeName) {
                return ($metric['route'] ?? '') === $routeName;
            });

            $allMetrics = array_merge($allMetrics, $routeMetrics);
        }

        if (empty($allMetrics)) {
            return $statistics;
        }

        // Calculate statistics
        $responseTimes = array_column($allMetrics, 'response_time');
        sort($responseTimes);

        $statistics['total_requests'] = count($responseTimes);
        $statistics['average_response_time'] = round(array_sum($responseTimes) / count($responseTimes), 2);
        $statistics['min_response_time'] = round(min($responseTimes), 2);
        $statistics['max_response_time'] = round(max($responseTimes), 2);

        // Count by classification
        foreach ($responseTimes as $time) {
            if ($time < self::INSTANT) {
                $statistics['instant_count']++;
            } elseif ($time < self::IMMEDIATE) {
                $statistics['immediate_count']++;
            } elseif ($time < self::ACCEPTABLE) {
                $statistics['acceptable_count']++;
            } else {
                $statistics['slow_count']++;
            }
        }

        // Calculate percentiles
        $statistics['percentiles'] = [
            'p50' => round(self::percentile($responseTimes, 50), 2),
            'p75' => round(self::percentile($responseTimes, 75), 2),
            'p90' => round(self::percentile($responseTimes, 90), 2),
            'p95' => round(self::percentile($responseTimes, 95), 2),
            'p99' => round(self::percentile($responseTimes, 99), 2),
        ];

        return $statistics;
    }

    /**
     * Calculate percentile value
     *
     * @param  array  $values  Sorted array of values
     * @param  float  $percentile  Percentile (0-100)
     */
    protected static function percentile(array $values, float $percentile): float
    {
        $count = count($values);
        $index = ($percentile / 100) * ($count - 1);

        $lower = floor($index);
        $upper = ceil($index);

        if ($lower == $upper) {
            return $values[$lower];
        }

        $weight = $index - $lower;

        return $values[$lower] * (1 - $weight) + $values[$upper] * $weight;
    }
}
