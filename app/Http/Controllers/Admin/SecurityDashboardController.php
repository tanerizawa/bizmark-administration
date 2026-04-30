<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminAuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SecurityDashboardController extends Controller
{
    public function webhookMetrics(Request $request)
    {
        $store = (string) config('email_webhook.cache_store', config('cache.default'));
        $hours = max(1, min(168, (int) $request->query('hours', 24)));
        $metricKeys = [
            'missing_headers' => 'Missing Headers',
            'invalid_timestamp' => 'Invalid Timestamp',
            'timestamp_out_of_window' => 'Timestamp Out of Window',
            'invalid_nonce' => 'Invalid Nonce',
            'nonce_reused' => 'Nonce Reused',
            'cache_store_failure' => 'Cache Store Failure',
            'cache_latency_high' => 'Cache Latency High',
            'cache_get_failure' => 'Cache Get Failure',
            'cache_add_failure' => 'Cache Add Failure',
            'cache_put_failure' => 'Cache Put Failure',
        ];

        $rows = [];
        $totals = array_fill_keys(array_keys($metricKeys), 0);

        for ($i = $hours - 1; $i >= 0; $i--) {
            $bucket = now()->subHours($i)->format('YmdH');
            $label = now()->subHours($i)->format('d/m H:00');
            $row = ['bucket' => $bucket, 'label' => $label];
            foreach (array_keys($metricKeys) as $metric) {
                $key = 'email_webhook_metric:'.$metric.':'.$bucket;
                $val = (int) Cache::store($store)->get($key, 0);
                $row[$metric] = $val;
                $totals[$metric] += $val;
            }
            $rows[] = $row;
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'store' => $store,
                'hours' => $hours,
                'data' => $rows,
            ]);
        }

        return view('admin.security.webhook-metrics', compact('rows', 'totals', 'metricKeys', 'store', 'hours'));
    }

    public function auditLogs(Request $request)
    {
        $query = AdminAuditLog::with('user')->latest();

        if ($request->filled('event')) {
            $query->where('event', $request->event);
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        $logs = $query->paginate(50)->withQueryString();

        $events = AdminAuditLog::select('event')->distinct()->orderBy('event')->pluck('event');

        return view('admin.security.audit-logs', compact('logs', 'events'));
    }
}
