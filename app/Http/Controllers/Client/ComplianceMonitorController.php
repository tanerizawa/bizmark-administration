<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\PermitExpiryMonitor;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ComplianceMonitorController extends Controller
{
    public function index()
    {
        $client = Auth::guard('client')->user();

        $monitors = PermitExpiryMonitor::where('client_id', $client->id)
            ->orderBy('expires_at')
            ->with('project')
            ->get();

        $stats = [
            'active' => $monitors->where('status', 'active')->count(),
            'expiring_soon' => $monitors->where('status', 'expiring_soon')->count(),
            'expired' => $monitors->where('status', 'expired')->count(),
            'renewed' => $monitors->where('status', 'renewed')->count(),
        ];

        $pushSubscribed = false; // TODO: query push_subscriptions table per client

        return view('client.compliance-monitor.index', compact('monitors', 'stats', 'pushSubscribed'));
    }

    public function export(): StreamedResponse
    {
        $client = Auth::guard('client')->user();
        $monitors = PermitExpiryMonitor::where('client_id', $client->id)
            ->orderBy('expires_at')
            ->with('project')
            ->get();

        $filename = 'compliance-monitor-'.now()->format('Y-m-d').'.csv';

        return response()->streamDownload(function () use ($monitors) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Jenis Izin', 'Nomor Izin', 'Proyek', 'Status', 'Tanggal Expire', 'Sisa Hari']);
            foreach ($monitors as $m) {
                fputcsv($out, [
                    $m->permit_type,
                    $m->permit_number ?? '-',
                    $m->project?->name ?? '-',
                    $m->status,
                    $m->expires_at->format('d/m/Y'),
                    $m->daysUntilExpiry(),
                ]);
            }
            fclose($out);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}
