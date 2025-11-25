<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LogReconciliationRequests
{
    public function handle(Request $request, Closure $next)
    {
        if ($request->is('reconciliations') || $request->is('reconciliations/*')) {
            Log::info("=== RECONCILIATION REQUEST ===", [
                'method' => $request->method(),
                'url' => $request->fullUrl(),
                'has_file' => $request->hasFile('bank_statement'),
                'file_size' => $request->hasFile('bank_statement') ? $request->file('bank_statement')->getSize() : 0,
                'all_inputs' => $request->except(['_token', 'bank_statement']),
                'ip' => $request->ip(),
                'user_id' => auth()->id(),
            ]);
        }

        $response = $next($request);

        if ($request->is('reconciliations') || $request->is('reconciliations/*')) {
            Log::info("=== RECONCILIATION RESPONSE ===", [
                'status' => $response->getStatusCode(),
                'redirect_to' => $response->isRedirect() ? $response->headers->get('Location') : null,
            ]);
        }

        return $response;
    }
}
