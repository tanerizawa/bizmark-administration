<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PermitApplication;
use App\Models\ApplicationRevision;
use App\Models\QuotationItem;
use App\Models\ApplicationLocationDetail;
use App\Models\ApplicationLegalityDocument;
use App\Models\PermitType;
use App\Models\ApplicationStatusLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PackageRevisionController extends Controller
{
    /**
     * Show form untuk revisi paket
     */
    public function create($applicationId)
    {
        $application = PermitApplication::with([
            'client', 
            'revisions',
            'locationDetail',
            'legalityDocuments',
            'quotationItems'
        ])->findOrFail($applicationId);
        
        // Get current package data
        $currentPackage = $this->getCurrentPackageData($application);
        
        // Get available permit types
        $permitTypes = PermitType::where('is_active', true)
            ->orderBy('name')
            ->get();
        
        // Get previous revisions
        $revisions = $application->revisions()
            ->with('revisedBy')
            ->orderBy('revision_number', 'desc')
            ->get();
        
        return view('admin.permit-applications.revise', compact(
            'application',
            'currentPackage',
            'permitTypes',
            'revisions'
        ));
    }
    
    /**
     * Store revision baru
     */
    public function store(Request $request, $applicationId)
    {
        $validated = $request->validate([
            'revision_type' => 'required|in:technical_adjustment,client_request,cost_update,document_incomplete',
            'revision_reason' => 'required|string|max:1000',
            'permits' => 'required|array|min:1',
            'permits.*.permit_type_id' => 'required|exists:permit_types,id',
            'permits.*.service_type' => 'required|in:bizmark,owned,self',
            'permits.*.unit_price' => 'required|numeric|min:0',
            'permits.*.estimated_days' => 'required|integer|min:1',
            
            // Location details (optional)
            'location.province' => 'nullable|string',
            'location.city_regency' => 'nullable|string',
            'location.district' => 'nullable|string',
            'location.sub_district' => 'nullable|string',
            'location.full_address' => 'nullable|string',
            'location.postal_code' => 'nullable|string',
            'location.latitude' => 'nullable|numeric',
            'location.longitude' => 'nullable|numeric',
            'location.zone_type' => 'nullable|in:industrial,commercial,residential,mixed,special_economic_zone',
            'location.land_status' => 'nullable|in:HGB,HGU,Hak_Milik,Girik,Sewa,Other',
            'location.land_certificate_number' => 'nullable|string',
            
            // Project data
            'land_area' => 'nullable|numeric',
            'building_area' => 'nullable|numeric',
            'investment_value' => 'nullable|numeric',
            
            // Legality documents (optional)
            'legality_documents' => 'nullable|array',
            'legality_documents.*.category' => 'required_with:legality_documents|in:land_ownership,company_legal,existing_permits,power_of_attorney,technical,other',
            'legality_documents.*.name' => 'required_with:legality_documents|string',
            'legality_documents.*.is_available' => 'nullable|boolean',
            'legality_documents.*.number' => 'nullable|string',
            'legality_documents.*.issued_date' => 'nullable|date',
            'legality_documents.*.notes' => 'nullable|string',
        ]);
        
        $application = PermitApplication::findOrFail($applicationId);
        
        DB::beginTransaction();
        try {
            // Create revision
            $revisionNumber = $application->revisions()->max('revision_number') + 1;
            
            // Calculate total cost
            $totalCost = collect($validated['permits'])->sum('unit_price');
            
            $revision = ApplicationRevision::create([
                'application_id' => $application->id,
                'revision_number' => $revisionNumber,
                'revision_type' => $validated['revision_type'],
                'revision_reason' => $validated['revision_reason'],
                'revised_by_id' => auth()->id(),
                'permits_data' => $validated['permits'],
                'project_data' => [
                    'location' => $validated['location'] ?? [],
                    'land_area' => $request->land_area,
                    'building_area' => $request->building_area,
                    'investment_value' => $request->investment_value,
                ],
                'total_cost' => $totalCost,
                'status' => 'pending_client_approval',
            ]);
            
            // Create quotation items for this revision
            foreach ($validated['permits'] as $permit) {
                $permitType = PermitType::find($permit['permit_type_id']);
                
                QuotationItem::create([
                    'application_id' => $application->id,
                    'revision_id' => $revision->id,
                    'item_type' => 'permit',
                    'permit_type_id' => $permit['permit_type_id'],
                    'item_name' => $permitType->name,
                    'description' => $permitType->description,
                    'unit_price' => $permit['unit_price'],
                    'quantity' => 1,
                    'subtotal' => $permit['unit_price'],
                    'service_type' => $permit['service_type'],
                    'estimated_days' => $permit['estimated_days'],
                ]);
            }
            
            // Update location details if provided
            if (!empty($validated['location'])) {
                $this->updateLocationDetails($application->id, $validated['location']);
            }
            
            // Update legality documents if provided
            if (!empty($validated['legality_documents'])) {
                $this->updateLegalityDocuments($application->id, $validated['legality_documents']);
            }
            
            // Create status log
            ApplicationStatusLog::create([
                'application_id' => $application->id,
                'from_status' => $application->status,
                'to_status' => $application->status, // Keep same status
                'notes' => "Revisi paket #{$revisionNumber} dibuat oleh " . auth()->user()->name,
                'changed_by_type' => 'user',
                'changed_by_id' => auth()->id(),
            ]);
            
            // Send notification to client
            // TODO: Implement PackageRevisionCreated notification
            // $application->client->notify(new PackageRevisionCreated($revision));
            
            DB::commit();
            
            return redirect()
                ->route('admin.permit-applications.show', $application->id)
                ->with('success', 'Revisi paket berhasil dibuat dan menunggu persetujuan client');
                
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create package revision: ' . $e->getMessage(), [
                'application_id' => $applicationId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return back()
                ->withInput()
                ->with('error', 'Gagal membuat revisi: ' . $e->getMessage());
        }
    }
    
    /**
     * Show revision details
     */
    public function show($applicationId, $revisionId)
    {
        $application = PermitApplication::with('client')->findOrFail($applicationId);
        
        $revision = ApplicationRevision::with(['revisedBy', 'quotationItems.permitType'])
            ->where('application_id', $applicationId)
            ->where('id', $revisionId)
            ->firstOrFail();
        
        // Get current package data for comparison
        $currentPackage = $this->getCurrentPackageData($application);
        
        return view('admin.permit-applications.revision-detail', compact(
            'application',
            'revision',
            'currentPackage'
        ));
    }
    
    /**
     * Get current package data (latest approved revision or original)
     */
    private function getCurrentPackageData($application)
    {
        // Get latest approved revision
        $latestRevision = $application->revisions()
            ->where('status', 'approved')
            ->latest()
            ->first();
        
        if ($latestRevision) {
            return [
                'permits' => $latestRevision->permits_data,
                'project_data' => $latestRevision->project_data,
                'total_cost' => $latestRevision->total_cost,
                'source' => 'revision',
                'revision_number' => $latestRevision->revision_number,
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
            'source' => 'original',
        ];
    }
    
    /**
     * Update location details
     */
    private function updateLocationDetails($applicationId, $locationData)
    {
        ApplicationLocationDetail::updateOrCreate(
            ['application_id' => $applicationId],
            $locationData
        );
    }
    
    /**
     * Update legality documents
     */
    private function updateLegalityDocuments($applicationId, $documents)
    {
        foreach ($documents as $doc) {
            // Skip if document name is empty
            if (empty($doc['name'])) {
                continue;
            }
            
            ApplicationLegalityDocument::updateOrCreate(
                [
                    'application_id' => $applicationId,
                    'document_category' => $doc['category'],
                    'document_name' => $doc['name'],
                ],
                [
                    'is_available' => $doc['is_available'] ?? false,
                    'document_number' => $doc['number'] ?? null,
                    'issued_date' => !empty($doc['issued_date']) ? $doc['issued_date'] : null,
                    'notes' => $doc['notes'] ?? null,
                ]
            );
        }
    }
}
