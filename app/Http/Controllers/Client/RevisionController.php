<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\PermitApplication;
use App\Models\ApplicationRevision;
use App\Models\ApplicationStatusLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RevisionController extends Controller
{
    /**
     * Show revision details for client to review
     */
    public function show($applicationId, $revisionId)
    {
        $clientId = auth()->guard('client')->id();
        
        $application = PermitApplication::where('client_id', $clientId)
            ->findOrFail($applicationId);
        
        $revision = ApplicationRevision::with(['revisedBy', 'quotationItems.permitType'])
            ->where('application_id', $applicationId)
            ->where('id', $revisionId)
            ->firstOrFail();
        
        // Get original package data for comparison
        $originalPackage = $this->getOriginalPackageData($application);
        
        return view('client.applications.revisions.show', compact(
            'application',
            'revision',
            'originalPackage'
        ));
    }
    
    /**
     * Client approve revision
     */
    public function approve(Request $request, $applicationId, $revisionId)
    {
        $clientId = auth()->guard('client')->id();
        
        $application = PermitApplication::where('client_id', $clientId)
            ->findOrFail($applicationId);
        
        $revision = ApplicationRevision::where('application_id', $applicationId)
            ->where('id', $revisionId)
            ->where('status', 'pending_client_approval')
            ->firstOrFail();
        
        DB::beginTransaction();
        try {
            // Approve revision
            $revision->update([
                'status' => 'approved',
                'client_approved_at' => now(),
            ]);
            
            // Update application with new data
            $application->update([
                'form_data' => array_merge(
                    $application->form_data ?? [],
                    [
                        'selected_permits' => $revision->permits_data,
                        'project_data' => $revision->project_data,
                        'revision_number' => $revision->revision_number,
                    ]
                ),
                'quoted_price' => $revision->total_cost,
                'status' => 'quotation_accepted',
            ]);
            
            // Create status log
            ApplicationStatusLog::create([
                'application_id' => $application->id,
                'from_status' => $application->status,
                'to_status' => 'quotation_accepted',
                'notes' => "Client menyetujui revisi paket #{$revision->revision_number}",
                'changed_by_type' => 'client',
                'changed_by_id' => $clientId,
            ]);
            
            // TODO: Send notification to admin
            // User::role('admin')->each(function($admin) use ($revision) {
            //     $admin->notify(new RevisionApproved($revision));
            // });
            
            DB::commit();
            
            return redirect()
                ->route('client.applications.show', $application->id)
                ->with('success', 'Revisi paket berhasil disetujui. Silakan lanjutkan ke pembayaran.');
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to approve revision: ' . $e->getMessage(), [
                'application_id' => $applicationId,
                'revision_id' => $revisionId,
                'client_id' => $clientId,
            ]);
            
            return back()->with('error', 'Gagal menyetujui revisi: ' . $e->getMessage());
        }
    }
    
    /**
     * Client reject revision
     */
    public function reject(Request $request, $applicationId, $revisionId)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);
        
        $clientId = auth()->guard('client')->id();
        
        $application = PermitApplication::where('client_id', $clientId)
            ->findOrFail($applicationId);
        
        $revision = ApplicationRevision::where('application_id', $applicationId)
            ->where('id', $revisionId)
            ->where('status', 'pending_client_approval')
            ->firstOrFail();
        
        DB::beginTransaction();
        try {
            // Reject revision
            $revision->update([
                'status' => 'rejected',
            ]);
            
            // Create status log
            ApplicationStatusLog::create([
                'application_id' => $application->id,
                'from_status' => $application->status,
                'to_status' => $application->status, // Keep same status
                'notes' => "Client menolak revisi paket #{$revision->revision_number}. Alasan: {$request->rejection_reason}",
                'changed_by_type' => 'client',
                'changed_by_id' => $clientId,
            ]);
            
            // TODO: Send notification to admin
            
            DB::commit();
            
            return redirect()
                ->route('client.applications.show', $application->id)
                ->with('info', 'Revisi paket ditolak. Admin akan diberitahu untuk membuat revisi baru.');
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to reject revision: ' . $e->getMessage());
            
            return back()->with('error', 'Gagal menolak revisi: ' . $e->getMessage());
        }
    }
    
    /**
     * Get original package data
     */
    private function getOriginalPackageData($application)
    {
        // Get the very first approved revision or original submission
        $firstRevision = $application->revisions()
            ->where('status', 'approved')
            ->where('revision_number', 1)
            ->first();
        
        if ($firstRevision) {
            return [
                'permits' => $firstRevision->permits_data,
                'project_data' => $firstRevision->project_data,
                'total_cost' => $firstRevision->total_cost,
            ];
        }
        
        // Return original form_data
        $formData = is_string($application->form_data) 
            ? json_decode($application->form_data, true) 
            : ($application->form_data ?? []);
        
        return [
            'permits' => $formData['selected_permits'] ?? [],
            'project_data' => [
                'location' => $formData['project_location'] ?? '',
                'land_area' => $formData['land_area'] ?? 0,
                'building_area' => $formData['building_area'] ?? 0,
                'investment_value' => $formData['investment_value'] ?? 0,
            ],
            'total_cost' => $application->quoted_price ?? 0,
        ];
    }
}
