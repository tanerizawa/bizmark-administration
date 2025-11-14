<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\PermitType;
use App\Models\PermitApplication;
use App\Models\ApplicationDocument;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Notification;
use App\Notifications\ApplicationSubmittedNotification;
use App\Notifications\NewApplicationNotification;

class ApplicationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:client');
    }

    /**
     * Display a listing of client's applications.
     */
    public function index(Request $request)
    {
        $query = PermitApplication::where('client_id', auth('client')->id())
            ->with(['permitType', 'documents']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Search
        if ($request->filled('search')) {
            $query->where('application_number', 'ilike', '%' . $request->search . '%');
        }

        $applications = $query->latest('created_at')->paginate(10)->withQueryString();

        // Get status counts for filter badges
        $statusCounts = PermitApplication::where('client_id', auth('client')->id())
            ->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status');

        return view('client.applications.index', compact('applications', 'statusCounts'));
    }

    /**
     * Show the form for creating a new application.
     */
    public function create(Request $request)
    {
        $permitTypeId = $request->get('permit_type');
        
        if (!$permitTypeId) {
            return redirect()->route('client.services.index')
                ->with('error', 'Silakan pilih jenis izin terlebih dahulu.');
        }

        $permitType = PermitType::findOrFail($permitTypeId);

        // Check if there's a draft application for this permit type
        $draft = PermitApplication::where('client_id', auth('client')->id())
            ->where('permit_type_id', $permitTypeId)
            ->where('status', 'draft')
            ->first();

        return view('client.applications.create', compact('permitType', 'draft'));
    }

    /**
     * Store a newly created application (or save draft).
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'permit_type_id' => 'required|exists:permit_types,id',
            'form_data' => 'required|array',
            'save_as_draft' => 'boolean',
            'kbli_code' => 'nullable|string|max:10',
            'kbli_description' => 'nullable|string',
            'kbli_category' => 'nullable|string',
        ]);

        $client = auth('client')->user();
        $isDraft = $request->boolean('save_as_draft');

        DB::beginTransaction();
        try {
            $application = PermitApplication::create([
                'client_id' => $client->id,
                'permit_type_id' => $validated['permit_type_id'],
                'form_data' => $validated['form_data'],
                'status' => $isDraft ? 'draft' : 'submitted',
                'submitted_at' => $isDraft ? null : now(),
                'kbli_code' => $validated['kbli_code'] ?? null,
                'kbli_description' => $validated['kbli_description'] ?? null,
                'kbli_category' => $validated['kbli_category'] ?? null,
            ]);

            DB::commit();

            if ($isDraft) {
                return redirect()->route('client.applications.index')
                    ->with('success', 'Draft permohonan berhasil disimpan. Anda dapat melanjutkan nanti.');
            }

            // Send notifications
            $client->notify(new ApplicationSubmittedNotification($application));
            
            // Notify all admins
            $admins = User::where('guard', 'web')->get();
            Notification::send($admins, new NewApplicationNotification($application));

            return redirect()->route('client.applications.show', $application->id)
                ->with('success', 'Permohonan berhasil diajukan! Silakan upload dokumen pendukung.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified application.
     */
    public function show($id)
    {
        $application = PermitApplication::where('client_id', auth('client')->id())
            ->with(['permitType', 'documents', 'quotations.payments', 'statusLogs'])
            ->findOrFail($id);

        // Check for pending payment
        $pendingPayment = null;
        if ($application->quotation) {
            $pendingPayment = \App\Models\Payment::where('quotation_id', $application->quotation->id)
                ->where('status', 'processing')
                ->latest()
                ->first();
        }

        // Mark all unread admin notes as read
        \App\Models\ApplicationNote::where('application_id', $id)
            ->where('author_type', 'admin')
            ->where('is_internal', false)
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now()
            ]);

        return view('client.applications.show', compact('application', 'pendingPayment'));
    }

    /**
     * Show the form for editing the specified application.
     */
    public function edit($id)
    {
        $application = PermitApplication::where('client_id', auth('client')->id())
            ->with('permitType')
            ->findOrFail($id);

        if (!$application->canBeEdited()) {
            return redirect()->route('client.applications.show', $id)
                ->with('error', 'Permohonan ini tidak dapat diubah.');
        }

        $permitType = $application->permitType;

        return view('client.applications.edit', compact('application', 'permitType'));
    }

    /**
     * Update the specified application.
     */
    public function update(Request $request, $id)
    {
        $application = PermitApplication::where('client_id', auth('client')->id())
            ->findOrFail($id);

        if (!$application->canBeEdited()) {
            return redirect()->route('client.applications.show', $id)
                ->with('error', 'Permohonan ini tidak dapat diubah.');
        }

        $validated = $request->validate([
            'form_data' => 'required|array',
            'save_as_draft' => 'boolean',
        ]);

        $isDraft = $request->boolean('save_as_draft');

        $application->update([
            'form_data' => $validated['form_data'],
            'status' => $isDraft ? 'draft' : 'submitted',
            'submitted_at' => $isDraft ? $application->submitted_at : now(),
        ]);

        if ($isDraft) {
            return redirect()->route('client.applications.index')
                ->with('success', 'Perubahan berhasil disimpan sebagai draft.');
        }

        return redirect()->route('client.applications.show', $id)
            ->with('success', 'Permohonan berhasil diperbarui dan diajukan!');
    }

    /**
     * Upload document for application.
     */
    public function uploadDocument(Request $request, $id)
    {
        $application = PermitApplication::where('client_id', auth('client')->id())
            ->findOrFail($id);

        $validated = $request->validate([
            'document_type' => 'required|string|max:100',
            'file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120', // 5MB
            'notes' => 'nullable|string|max:500',
        ]);

        try {
            $file = $request->file('file');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('applications/' . $application->id, $filename, 'private');

            ApplicationDocument::create([
                'application_id' => $application->id,
                'document_type' => $validated['document_type'],
                'file_path' => $path,
                'file_name' => $file->getClientOriginalName(),
                'file_size' => $file->getSize(),
                'mime_type' => $file->getMimeType(),
                'notes' => $validated['notes'] ?? null,
            ]);

            return back()->with('success', 'Dokumen berhasil diupload.');

        } catch (\Exception $e) {
            return back()->with('error', 'Gagal mengupload dokumen: ' . $e->getMessage());
        }
    }

    /**
     * Delete uploaded document.
     */
    public function deleteDocument($applicationId, $documentId)
    {
        $application = PermitApplication::where('client_id', auth('client')->id())
            ->findOrFail($applicationId);

        $document = ApplicationDocument::where('application_id', $application->id)
            ->findOrFail($documentId);

        if ($document->is_verified) {
            return back()->with('error', 'Dokumen yang sudah diverifikasi tidak dapat dihapus.');
        }

        try {
            Storage::disk('private')->delete($document->file_path);
            $document->delete();

            return back()->with('success', 'Dokumen berhasil dihapus.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus dokumen: ' . $e->getMessage());
        }
    }

    /**
     * Submit application after documents uploaded.
     */
    public function submit($id)
    {
        $application = PermitApplication::where('client_id', auth('client')->id())
            ->with('documents')
            ->findOrFail($id);

        if (!$application->canBeSubmitted()) {
            return back()->with('error', 'Permohonan tidak dapat diajukan saat ini.');
        }

        if ($application->documents->isEmpty()) {
            return back()->with('error', 'Silakan upload minimal satu dokumen sebelum mengajukan permohonan.');
        }

        $application->update([
            'status' => 'submitted',
            'submitted_at' => now(),
        ]);

        // Log status change
        $application->statusLogs()->create([
            'from_status' => 'draft',
            'to_status' => 'submitted',
            'notes' => 'Permohonan diajukan oleh klien',
            'changed_by_type' => 'App\Models\Client',
            'changed_by_id' => auth('client')->id(),
        ]);

        return redirect()->route('client.applications.show', $id)
            ->with('success', 'Permohonan berhasil diajukan! Tim kami akan segera meninjaunya.');
    }

    /**
     * Cancel application.
     */
    public function cancel($id)
    {
        $application = PermitApplication::where('client_id', auth('client')->id())
            ->findOrFail($id);

        if (!$application->canBeCancelled()) {
            return back()->with('error', 'Permohonan ini tidak dapat dibatalkan.');
        }

        $oldStatus = $application->status;

        $application->update([
            'status' => 'cancelled',
        ]);

        // Log status change
        $application->statusLogs()->create([
            'from_status' => $oldStatus,
            'to_status' => 'cancelled',
            'notes' => 'Permohonan dibatalkan oleh klien',
            'changed_by_type' => 'App\Models\Client',
            'changed_by_id' => auth('client')->id(),
        ]);

        return redirect()->route('client.applications.index')
            ->with('success', 'Permohonan berhasil dibatalkan.');
    }
}
