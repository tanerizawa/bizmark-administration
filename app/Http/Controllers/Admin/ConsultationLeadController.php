<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\ConsultRequest;
use App\Models\PermitApplication;
use App\Notifications\ClientWelcomeNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ConsultationLeadController extends Controller
{
    /**
     * Display a listing of consultation requests/leads
     */
    public function index(Request $request)
    {
        $query = ConsultRequest::with(['kbli', 'reviewer', 'client'])
            ->orderBy('created_at', 'desc');

        // Filter by status
        if ($request->filled('status')) {
            $query->where('estimate_status', $request->status);
        }

        // Filter by conversion status
        if ($request->filled('converted')) {
            if ($request->converted === 'yes') {
                $query->where('converted_to_client', true);
            } elseif ($request->converted === 'no') {
                $query->where('converted_to_client', false);
            }
        }

        // Filter by contacted status
        if ($request->filled('contacted')) {
            if ($request->contacted === 'yes') {
                $query->where('contacted', true);
            } elseif ($request->contacted === 'no') {
                $query->where('contacted', false);
            }
        }

        // Filter by business size
        if ($request->filled('business_size')) {
            $query->where('business_size', $request->business_size);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('company_name', 'like', "%{$search}%")
                    ->orWhere('location', 'like', "%{$search}%")
                    ->orWhere('kbli_code', 'like', "%{$search}%");
            });
        }

        // Date range filter
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // High value filter (above 10M)
        if ($request->filled('high_value')) {
            $query->whereRaw("CAST(auto_estimate->'cost_summary'->>'grand_total' AS BIGINT) >= ?", [10000000]);
        }

        $consultations = $query->paginate(20);

        // Basic stats for dashboard
        $stats = [
            'total' => ConsultRequest::count(),
            'new' => ConsultRequest::where('estimate_status', 'auto_estimated')->where('contacted', false)->count(),
            'contacted' => ConsultRequest::where('contacted', true)->count(),
            'converted' => ConsultRequest::where('converted_to_client', true)->count(),
            'pending_review' => ConsultRequest::where('estimate_status', 'auto_estimated')->whereNull('reviewed_at')->count(),
            'high_value' => ConsultRequest::whereRaw("CAST(auto_estimate->'cost_summary'->>'grand_total' AS BIGINT) >= ?", [10000000])->count(),
            'this_week' => ConsultRequest::where('created_at', '>=', now()->startOfWeek())->count(),
            'this_month' => ConsultRequest::where('created_at', '>=', now()->startOfMonth())->count(),
        ];

        return view('admin.consultation-leads.index', compact('consultations', 'stats'));
    }

    /**
     * Display the specified consultation request
     */
    public function show(ConsultRequest $consultation)
    {
        $consultation->load(['kbli', 'reviewer', 'client']);

        return view('admin.consultation-leads.show', compact('consultation'));
    }

    /**
     * Update consultation status
     */
    public function updateStatus(Request $request, ConsultRequest $consultation)
    {
        $request->validate([
            'estimate_status' => 'required|in:auto_estimated,reviewed,approved,quoted,rejected',
            'admin_notes' => 'nullable|string|max:2000',
        ]);

        $consultation->update([
            'estimate_status' => $request->estimate_status,
            'admin_notes' => $request->admin_notes,
            'reviewed_by' => in_array($request->estimate_status, ['reviewed', 'approved', 'quoted']) ? auth()->id() : $consultation->reviewed_by,
            'reviewed_at' => in_array($request->estimate_status, ['reviewed', 'approved', 'quoted']) ? now() : $consultation->reviewed_at,
        ]);

        return back()->with('success', 'Status berhasil diupdate');
    }

    /**
     * Mark as contacted
     */
    public function markContacted(Request $request, ConsultRequest $consultation)
    {
        $request->validate([
            'admin_notes' => 'nullable|string|max:2000',
        ]);

        $consultation->markAsContacted();

        if ($request->admin_notes) {
            $consultation->update(['admin_notes' => $request->admin_notes]);
        }

        return back()->with('success', 'Berhasil ditandai sebagai telah dihubungi');
    }

    /**
     * Add admin notes
     */
    public function addNote(Request $request, ConsultRequest $consultation)
    {
        $request->validate([
            'note' => 'required|string|max:2000',
        ]);

        $currentNotes = $consultation->admin_notes ?? [];
        $user = auth()->user();

        $newNote = [
            'note' => $request->note,
            'admin_name' => $user->name,
            'admin_id' => $user->id,
            'created_at' => now()->toISOString(),
        ];

        $currentNotes[] = $newNote;
        $consultation->update(['admin_notes' => $currentNotes]);

        return back()->with('success', 'Catatan berhasil ditambahkan');
    }

    /**
     * Convert consultation to client project
     */
    public function convertToClient(Request $request, ConsultRequest $consultation)
    {
        $request->validate([
            'create_client_account' => 'required|boolean',
            'password' => 'required_if:create_client_account,true|min:8',
            'company_name' => 'nullable|string|max:255',
        ]);

        DB::beginTransaction();
        try {
            // Check if email already exists
            $client = Client::where('email', $consultation->email)->first();

            if (! $client && $request->create_client_account) {
                // Create new client account
                $client = Client::create([
                    'name' => $consultation->name ?: 'Guest User',
                    'email' => $consultation->email,
                    'password' => bcrypt($request->password),
                    'company_name' => $request->company_name ?: $consultation->company_name,
                    'phone' => $consultation->phone,
                    'company_type' => $this->mapBusinessSizeToCompanyType($consultation->business_size),
                    'email_verified_at' => now(), // Auto-verify
                ]);
            }

            if ($client) {
                // Create permit application from consultation
                $application = PermitApplication::create([
                    'application_number' => PermitApplication::generateApplicationNumber(),
                    'client_id' => $client->id,
                    'kbli_code' => $consultation->kbli_code,
                    'business_description' => $consultation->project_description,
                    'business_scale' => $consultation->business_size,
                    'location_province' => $this->extractProvince($consultation->location),
                    'location_city' => $consultation->location,
                    'status' => 'draft',
                    'submission_date' => now(),
                    'notes' => "Converted from consultation request: #{$consultation->id}\n\n".
                              'Original estimate: '.($consultation->auto_estimate['cost_summary']['formatted']['grand_total'] ?? 'N/A')."\n".
                              "Investment level: {$consultation->investment_level}\n".
                              "Business size: {$consultation->business_size}\n\n".
                              "AI Analysis:\n".json_encode($consultation->auto_estimate['ai_analysis'] ?? [], JSON_PRETTY_PRINT),
                ]);

                // Update consultation
                $consultation->convertToClient($client->id);

                if ($request->create_client_account && $request->password) {
                    $client->notify(new ClientWelcomeNotification($client, $request->password));
                }

                DB::commit();

                return redirect()
                    ->route('admin.consultation-leads.show', $consultation)
                    ->with('success', 'Consultation berhasil dikonversi ke client! Client ID: '.$client->id);
            }

            DB::rollBack();

            return back()->with('error', 'Gagal membuat client account');

        } catch (\Exception $e) {
            DB::rollBack();

            return back()->with('error', 'Terjadi kesalahan: '.$e->getMessage());
        }
    }

    /**
     * Export consultations to CSV
     */
    public function export(Request $request)
    {
        $query = ConsultRequest::with('kbli')->orderBy('created_at', 'desc');

        // Apply same filters as index
        if ($request->filled('status')) {
            $query->where('estimate_status', $request->status);
        }
        if ($request->filled('converted')) {
            if ($request->converted === 'yes') {
                $query->where('converted_to_client', true);
            } elseif ($request->converted === 'no') {
                $query->where('converted_to_client', false);
            }
        }
        if ($request->filled('contacted')) {
            if ($request->contacted === 'yes') {
                $query->where('contacted', true);
            } elseif ($request->contacted === 'no') {
                $query->where('contacted', false);
            }
        }
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $consultations = $query->get();

        $filename = 'consultation-leads-'.now()->format('Y-m-d').'.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($consultations) {
            $file = fopen('php://output', 'w');

            // Header row
            fputcsv($file, [
                'ID',
                'Date',
                'Email',
                'Phone',
                'Company',
                'KBLI',
                'Business Description',
                'Business Size',
                'Location',
                'Investment Level',
                'Estimated Cost',
                'Status',
                'Contacted',
                'Converted',
                'Confidence Score',
            ]);

            // Data rows
            foreach ($consultations as $consultation) {
                fputcsv($file, [
                    $consultation->id,
                    $consultation->created_at->format('Y-m-d H:i'),
                    $consultation->email,
                    $consultation->phone,
                    $consultation->company_name,
                    $consultation->kbli_code.' - '.optional($consultation->kbli)->description,
                    $consultation->project_description,
                    $consultation->business_size_label,
                    $consultation->location,
                    $consultation->investment_level_label,
                    $consultation->auto_estimate['cost_summary']['formatted']['grand_total'] ?? '-',
                    $consultation->estimate_status,
                    $consultation->contacted ? 'Yes' : 'No',
                    $consultation->converted_to_client ? 'Yes' : 'No',
                    $consultation->confidence_score ?? '-',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Delete consultation request
     */
    public function destroy(ConsultRequest $consultation)
    {
        $consultation->delete();

        return redirect()
            ->route('admin.consultation-leads.index')
            ->with('success', 'Consultation request berhasil dihapus');
    }

    /**
     * Helper: Map business size to company type
     */
    private function mapBusinessSizeToCompanyType(string $businessSize): string
    {
        return match ($businessSize) {
            'micro' => 'small_business',
            'small' => 'small_business',
            'medium' => 'medium_enterprise',
            'large' => 'large_enterprise',
            default => 'small_business',
        };
    }

    /**
     * Helper: Extract province from location string
     */
    private function extractProvince(string $location): ?string
    {
        // Simple extraction logic - can be improved
        $provinces = [
            'Jakarta' => 'DKI Jakarta',
            'Bandung' => 'Jawa Barat',
            'Surabaya' => 'Jawa Timur',
            'Semarang' => 'Jawa Tengah',
            'Medan' => 'Sumatera Utara',
            // Add more mappings as needed
        ];

        foreach ($provinces as $city => $province) {
            if (stripos($location, $city) !== false) {
                return $province;
            }
        }

        return null; // Could not determine province
    }
}
