<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminAuditLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SecurityDashboardController extends Controller
{
    public function webhookMetrics(Request $request): JsonResponse
    {
        $store = (string) config('email_webhook.cache_store', config('cache.default'));
        $hours = max(1, min(168, (int) $request->query('hours', 24)));
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

        $out = [];
        for ($i = $hours - 1; $i >= 0; $i--) {
            $bucket = now()->subHours($i)->format('YmdH');
            $row = ['bucket' => $bucket];
            foreach ($metrics as $metric) {
                $key = 'email_webhook_metric:' . $metric . ':' . $bucket;
                $row[$metric] = (int) Cache::store($store)->get($key, 0);
            }
            $out[] = $row;
        }

        return response()->json([
            'success' => true,
            'store' => $store,
            'hours' => $hours,
            'data' => $out,
        ]);
    }

    public function auditLogs(Request $request)
    {
        $logs = AdminAuditLog::query()
            ->latest()
            ->paginate(50)
            ->withQueryString();

        return view('admin.security.audit-logs', compact('logs'));
    }
}

