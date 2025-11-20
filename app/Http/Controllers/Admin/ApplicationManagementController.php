<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PermitApplication;
use App\Models\PermitType;
use App\Models\Client;
use App\Models\ApplicationStatusLog;
use App\Models\Project;
use App\Models\ProjectPermit;
use App\Models\ProjectPermitDependency;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ApplicationManagementController extends Controller
{
    /**
     * Display a listing of all permit applications.
     */
    public function index(Request $request)
    {
        $query = PermitApplication::with(['client', 'permitType', 'reviewedBy']);
        
        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        // Filter by permit type
        if ($request->filled('permit_type_id')) {
            $query->where('permit_type_id', $request->permit_type_id);
        }
        
        // Filter by client
        if ($request->filled('client_id')) {
            $query->where('client_id', $request->client_id);
        }
        
        // Search by application number or client name
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('application_number', 'ILIKE', '%' . $search . '%')
                  ->orWhereHas('client', function($q) use ($search) {
                      $q->where('name', 'ILIKE', '%' . $search . '%');
                  });
            });
        }
        
        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('submitted_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('submitted_at', '<=', $request->date_to);
        }
        
        // Sort
        $sortBy = $request->get('sort_by', 'submitted_at');
        $sortOrder = $request->get('sort_order', 'desc');
        
        // Handle null submitted_at for drafts
        if ($sortBy === 'submitted_at') {
            $query->orderByRaw('submitted_at IS NULL, submitted_at ' . $sortOrder);
        } else {
            $query->orderBy($sortBy, $sortOrder);
        }
        
        $applications = $query->paginate(20);
        
        // Get filter options
        $permitTypes = PermitType::orderBy('name')->get();
        $clients = Client::orderBy('name')->get();
        
        // Statistics
        $stats = [
            'total' => PermitApplication::count(),
            'submitted' => PermitApplication::where('status', 'submitted')->count(),
            'under_review' => PermitApplication::where('status', 'under_review')->count(),
            'quoted' => PermitApplication::where('status', 'quoted')->count(),
            'in_progress' => PermitApplication::where('status', 'in_progress')->count(),
            'completed' => PermitApplication::where('status', 'completed')->count(),
        ];
        
        // Status options for filter
        $statusOptions = [
            'draft' => 'Draft',
            'submitted' => 'Submitted',
            'under_review' => 'Under Review',
            'document_incomplete' => 'Document Incomplete',
            'quoted' => 'Quoted',
            'quotation_accepted' => 'Quotation Accepted',
            'quotation_rejected' => 'Quotation Rejected',
            'payment_pending' => 'Payment Pending',
            'payment_verified' => 'Payment Verified',
            'in_progress' => 'In Progress',
            'completed' => 'Completed',
            'cancelled' => 'Cancelled',
        ];
        
        return view('admin.permit-applications.index', compact(
            'applications',
            'permitTypes',
            'clients',
            'stats',
            'statusOptions'
        ));
    }
    
    /**
     * Display the specified application.
     */
    public function show($id)
    {
        $application = PermitApplication::with([
            'client',
            'permitType',
            'documents.uploadedBy',
            'statusLogs.user',
            'reviewedBy',
            'quotation',
            'project'
        ])->findOrFail($id);
        
        // Mark all unread client notes as read
        \App\Models\ApplicationNote::where('application_id', $id)
            ->where('author_type', 'client')
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now()
            ]);
        
        return view('admin.permit-applications.show', compact('application'));
    }
    
    /**
     * Start reviewing an application.
     */
    public function startReview($id)
    {
        $application = PermitApplication::with('client')->findOrFail($id);
        
        // Only submitted applications can be reviewed
        if ($application->status !== 'submitted') {
            return back()->with('error', 'Hanya aplikasi yang sudah disubmit yang bisa direview');
        }
        
        DB::beginTransaction();
        try {
            $previousStatus = $application->status;
            
            $application->update([
                'status' => 'under_review',
                'reviewed_by' => Auth::id(),
                'reviewed_at' => now(),
            ]);
            
            // Log status change
            ApplicationStatusLog::create([
                'application_id' => $application->id,
                'from_status' => 'submitted',
                'to_status' => 'under_review',
                'changed_by_type' => 'user',
                'changed_by_id' => Auth::id(),
                'notes' => 'Review dimulai oleh admin',
            ]);
            
            // Send email notification to client
            if ($application->client && $application->client->email) {
                try {
                    \Mail::to($application->client->email)->send(
                        new \App\Mail\ApplicationStatusChanged(
                            $application,
                            $previousStatus,
                            'under_review',
                            Auth::user(),
                            'Review dimulai oleh admin'
                        )
                    );
                } catch (\Exception $emailException) {
                    \Log::warning('Failed to send review start email', [
                        'application_id' => $application->id,
                        'error' => $emailException->getMessage()
                    ]);
                }
            }
            
            DB::commit();
            
            return redirect()
                ->route('admin.permit-applications.show', $application->id)
                ->with('success', 'Review dimulai. Notifikasi email telah dikirim ke client. Silakan verifikasi dokumen dan buat quotation.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal memulai review: ' . $e->getMessage());
        }
    }
    
    /**
     * Update application status.
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:under_review,document_incomplete,quoted,payment_pending,in_progress,completed,cancelled',
            'notes' => 'nullable|string|max:1000',
        ]);
        
        $application = PermitApplication::with('client')->findOrFail($id);
        $previousStatus = $application->status;
        
        DB::beginTransaction();
        try {
            $application->update([
                'status' => $request->status,
                'admin_notes' => $request->notes,
            ]);
            
            // Log status change
            ApplicationStatusLog::create([
                'application_id' => $application->id,
                'from_status' => $previousStatus,
                'to_status' => $request->status,
                'changed_by_type' => 'user',
                'changed_by_id' => Auth::id(),
                'notes' => $request->notes,
            ]);
            
            // Send email notification to client
            if ($application->client && $application->client->email) {
                try {
                    \Mail::to($application->client->email)->send(
                        new \App\Mail\ApplicationStatusChanged(
                            $application,
                            $previousStatus,
                            $request->status,
                            Auth::user(),
                            $request->notes
                        )
                    );
                } catch (\Exception $emailException) {
                    // Log email failure but don't fail the status update
                    \Log::warning('Failed to send status change email', [
                        'application_id' => $application->id,
                        'client_email' => $application->client->email,
                        'error' => $emailException->getMessage()
                    ]);
                }
            }
            
            DB::commit();
            
            return back()->with('success', 'Status aplikasi berhasil diupdate dan notifikasi email telah dikirim ke client');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal mengupdate status: ' . $e->getMessage());
        }
    }
    
    /**
     * Add admin notes to application.
     */
    public function addNotes(Request $request, $id)
    {
        $request->validate([
            'notes' => 'required|string|max:2000',
        ]);
        
        $application = PermitApplication::findOrFail($id);
        
        $existingNotes = $application->admin_notes;
        $timestamp = now()->format('Y-m-d H:i:s');
        $userName = Auth::user()->name;
        $newNote = "[{$timestamp}] {$userName}: {$request->notes}";
        
        if ($existingNotes) {
            $application->admin_notes = $existingNotes . "\n\n" . $newNote;
        } else {
            $application->admin_notes = $newNote;
        }
        
        $application->save();
        
        return back()->with('success', 'Catatan berhasil ditambahkan');
    }
    
    /**
     * Verify or reject a document.
     */
    public function verifyDocument(Request $request, $applicationId, $documentId)
    {
        $request->validate([
            'action' => 'required|in:approve,reject',
            'notes' => 'nullable|string|max:500',
        ]);
        
        $application = PermitApplication::findOrFail($applicationId);
        $document = $application->documents()->findOrFail($documentId);
        
        $isApproved = $request->action === 'approve';
        
        $document->update([
            'is_verified' => $isApproved,
            'verified_by' => Auth::id(),
            'verified_at' => now(),
            'verification_notes' => $request->notes,
        ]);
        
        $message = $isApproved 
            ? 'Dokumen berhasil diverifikasi' 
            : 'Dokumen ditolak. Client perlu upload ulang.';
        
        return back()->with('success', $message);
    }
    
    /**
     * Bulk verify all documents.
     */
    public function verifyAllDocuments($id)
    {
        $application = PermitApplication::findOrFail($id);
        
        $unverifiedCount = $application->documents()
            ->where('is_verified', false)
            ->count();
        
        if ($unverifiedCount === 0) {
            return back()->with('info', 'Semua dokumen sudah diverifikasi');
        }
        
        $application->documents()
            ->where('is_verified', false)
            ->update([
                'is_verified' => true,
                'verified_by' => Auth::id(),
                'verified_at' => now(),
                'verification_notes' => 'Bulk verification oleh admin',
            ]);
        
        return back()->with('success', "{$unverifiedCount} dokumen berhasil diverifikasi");
    }
    
    /**
     * Mark application as document incomplete.
     */
    public function requestDocumentRevision(Request $request, $id)
    {
        $request->validate([
            'notes' => 'required|string|max:1000',
        ]);
        
        $application = PermitApplication::with('client')->findOrFail($id);
        $previousStatus = $application->status;
        
        DB::beginTransaction();
        try {
            $application->update([
                'status' => 'document_incomplete',
                'admin_notes' => $request->notes,
            ]);
            
            // Log status change
            ApplicationStatusLog::create([
                'application_id' => $application->id,
                'from_status' => $previousStatus,
                'to_status' => 'document_incomplete',
                'changed_by_type' => 'user',
                'changed_by_id' => Auth::id(),
                'notes' => 'Dokumen tidak lengkap: ' . $request->notes,
            ]);
            
            // Send email notification to client
            if ($application->client && $application->client->email) {
                try {
                    \Mail::to($application->client->email)->send(
                        new \App\Mail\ApplicationStatusChanged(
                            $application,
                            $previousStatus,
                            'document_incomplete',
                            Auth::user(),
                            $request->notes
                        )
                    );
                } catch (\Exception $emailException) {
                    \Log::warning('Failed to send document revision email', [
                        'application_id' => $application->id,
                        'error' => $emailException->getMessage()
                    ]);
                }
            }
            
            DB::commit();
            
            return back()->with('success', 'Client diminta untuk melengkapi dokumen. Notifikasi email telah dikirim.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal request revision: ' . $e->getMessage());
        }
    }
    
    /**
     * Convert permit package application to Project with ProjectPermits
     */
    public function convertToProject($id)
    {
        $application = PermitApplication::with(['client'])->findOrFail($id);
        
        // Check if already converted
        if ($application->project_id) {
            return back()->with('error', 'Aplikasi ini sudah dikonversi menjadi proyek');
        }
        
        // Check status - should be payment_verified
        if ($application->status !== 'payment_verified') {
            return back()->with('error', 'Hanya aplikasi dengan status "Payment Verified" yang bisa dikonversi menjadi proyek');
        }
        
        $formData = is_string($application->form_data) 
            ? json_decode($application->form_data, true) 
            : $application->form_data;
        
        $isPackage = isset($formData['package_type']) && $formData['package_type'] === 'multi_permit';
        
        if (!$isPackage) {
            return back()->with('error', 'Hanya aplikasi paket izin yang bisa dikonversi menjadi proyek');
        }
        
        DB::beginTransaction();
        try {
            // Create Project
            $project = \App\Models\Project::create([
                'permit_application_id' => $application->id,
                'client_id' => $application->client_id,
                'project_name' => $formData['project_name'] ?? 'Untitled Project',
                'project_location' => $formData['project_location'] ?? null,
                'land_area' => $formData['land_area'] ?? null,
                'building_area' => $formData['building_area'] ?? null,
                'building_floors' => $formData['building_floors'] ?? null,
                'project_budget' => $formData['investment_value'] ?? 0,
                'target_completion_date' => isset($formData['target_completion_date']) 
                    ? \Carbon\Carbon::parse($formData['target_completion_date']) 
                    : null,
                'description' => $formData['project_description'] ?? null,
                'status' => 'active',
            ]);
            
            // Create ProjectPermits (only for BizMark.ID permits)
            $bizmarkPermits = $formData['bizmark_permits'] ?? [];
            $sequenceOrder = 1;
            $permitIdMap = []; // Map permit names to ProjectPermit IDs
            
            foreach ($bizmarkPermits as $permit) {
                $projectPermit = \App\Models\ProjectPermit::create([
                    'project_id' => $project->id,
                    'permit_type_id' => null, // Custom permit from KBLI
                    'custom_permit_name' => $permit['name'],
                    'sequence_order' => $sequenceOrder++,
                    'is_goal_permit' => false, // Can be updated later
                    'status' => \App\Models\ProjectPermit::STATUS_NOT_STARTED,
                    'estimated_cost' => isset($permit['estimated_cost_min']) && isset($permit['estimated_cost_max'])
                        ? ($permit['estimated_cost_min'] + $permit['estimated_cost_max']) / 2
                        : 0,
                    'estimated_days' => $permit['estimated_days'] ?? null,
                ]);
                
                $permitIdMap[$permit['name']] = $projectPermit->id;
            }
            
            // Create ProjectPermitDependencies based on AI recommendations
            foreach ($bizmarkPermits as $permit) {
                if (!empty($permit['dependencies']) && isset($permitIdMap[$permit['name']])) {
                    foreach ($permit['dependencies'] as $dependencyName) {
                        // Find if dependency is in our BizMark permits
                        if (isset($permitIdMap[$dependencyName])) {
                            \App\Models\ProjectPermitDependency::create([
                                'project_permit_id' => $permitIdMap[$permit['name']],
                                'depends_on_permit_id' => $permitIdMap[$dependencyName],
                                'dependency_type' => 'MANDATORY', // Default to mandatory
                                'can_proceed_without' => false,
                            ]);
                        }
                    }
                }
            }
            
            // Update application with project_id and status
            $application->update([
                'project_id' => $project->id,
                'converted_at' => now(),
                'status' => 'in_progress',
            ]);
            
            // Log status change
            ApplicationStatusLog::create([
                'application_id' => $application->id,
                'from_status' => 'payment_verified',
                'to_status' => 'in_progress',
                'changed_by_type' => 'user',
                'changed_by_id' => Auth::id(),
                'notes' => 'Aplikasi dikonversi menjadi proyek: ' . $project->project_name . ' dengan ' . count($bizmarkPermits) . ' izin',
            ]);
            
            DB::commit();
            
            return redirect()
                ->route('client.projects.show', $project->id)
                ->with('success', 'Berhasil mengkonversi aplikasi menjadi proyek dengan ' . count($bizmarkPermits) . ' izin. Proyek sekarang dapat dikelola.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal konversi ke proyek: ' . $e->getMessage());
        }
    }
}
