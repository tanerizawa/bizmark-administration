<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\PermitExpiryMonitor;
use App\Models\Project;
use Illuminate\Http\Request;

class ComplianceMonitorController extends Controller
{
    public function index(Request $request)
    {
        $query = PermitExpiryMonitor::with(['client', 'project'])
            ->orderBy('expires_at');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('client_id')) {
            $query->where('client_id', $request->client_id);
        }

        $monitors = $query->paginate(30)->withQueryString();

        $stats = [
            'active' => PermitExpiryMonitor::where('status', 'active')->count(),
            'expiring_soon' => PermitExpiryMonitor::where('status', 'expiring_soon')->count(),
            'expired' => PermitExpiryMonitor::where('status', 'expired')->count(),
            'total' => PermitExpiryMonitor::count(),
        ];

        $clients = Client::orderBy('name')->get(['id', 'name', 'company_name']);
        $projects = Project::orderBy('name')->get(['id', 'name', 'client_id']);

        return view('admin.compliance-monitor.index', compact('monitors', 'stats', 'clients', 'projects'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_id' => ['required', 'exists:projects,id'],
            'project_permit_id' => ['nullable', 'exists:project_permits,id'],
            'client_id' => ['required', 'exists:clients,id'],
            'permit_type' => ['required', 'string', 'max:200'],
            'permit_number' => ['nullable', 'string', 'max:100'],
            'issued_at' => ['nullable', 'date'],
            'expires_at' => ['required', 'date', 'after:today'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        PermitExpiryMonitor::create($validated);

        return back()->with('success', 'Monitor izin berhasil ditambahkan.');
    }

    public function update(Request $request, PermitExpiryMonitor $monitor)
    {
        $validated = $request->validate([
            'permit_type' => ['sometimes', 'string', 'max:200'],
            'permit_number' => ['nullable', 'string', 'max:100'],
            'issued_at' => ['nullable', 'date'],
            'expires_at' => ['sometimes', 'date'],
            'status' => ['sometimes', 'in:active,expiring_soon,expired,renewed'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        // Reset notification flags when permit is renewed or expires_at updated
        if (isset($validated['status']) && $validated['status'] === 'renewed') {
            $validated['notified_90'] = false;
            $validated['notified_30'] = false;
            $validated['notified_7'] = false;
        }

        $monitor->update($validated);

        return back()->with('success', 'Monitor izin berhasil diperbarui.');
    }

    public function destroy(PermitExpiryMonitor $monitor)
    {
        $monitor->delete();

        return back()->with('success', 'Monitor izin dihapus.');
    }
}
