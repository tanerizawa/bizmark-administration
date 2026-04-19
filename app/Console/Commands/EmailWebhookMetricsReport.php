<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Throwable;

class EmailWebhookMetricsReport extends Command
{
    protected $signature = 'email-webhook:metrics-report {--hours=1}';
    protected $description = 'Summarize email webhook replay protection metrics';

    public function handle(): int
    {
        $store = (string) config('email_webhook.cache_store', config('cache.default'));
        $hours = max(1, min(168, (int) $this->option('hours')));

        $metrics = [
            'missing_headers',
            'invalid_timestamp',
            'timestamp_out_of_window',
            'invalid_nonce',
            'nonce_reused',
            'cache_store_failure',
            'cache_latency_high',
            'cache_get_failure',
            'cache_add_failure',
            'cache_put_failure',
        ];

        $summary = array_fill_keys($metrics, 0);

        try {
            $cache = Cache::store($store);
            for ($i = 0; $i < $hours; $i++) {
                $bucket = now()->subHours($i)->format('YmdH');
                foreach ($metrics as $metric) {
                    $key = 'email_webhook_metric:' . $metric . ':' . $bucket;
                    $summary[$metric] += (int) $cache->get($key, 0);
                }
            }
        } catch (Throwable $e) {
            $this->error('Failed to read metrics from cache store: ' . $e->getMessage());
            Log::critical('Email webhook metrics report failed', ['store' => $store, 'error' => $e->getMessage()]);
            return self::FAILURE;
        }

        $this->info('Email webhook metrics (last ' . $hours . ' hour(s))');
        foreach ($summary as $metric => $value) {
            $this->line($metric . ': ' . $value);
        }

        $warnThreshold = (int) config('email_webhook.alert_threshold_per_hour', 10);
        $alerts = [];
        foreach (['timestamp_out_of_window', 'invalid_timestamp', 'invalid_nonce', 'cache_store_failure'] as $metric) {
            if (($summary[$metric] ?? 0) >= $warnThreshold) {
                $alerts[$metric] = $summary[$metric];
            }
        }

        if ($alerts !== []) {
            Log::warning('Email webhook security metrics exceeded threshold', [
                'hours' => $hours,
                'threshold' => $warnThreshold,
                'alerts' => $alerts,
            ]);
        }

        return self::SUCCESS;
    }
}

