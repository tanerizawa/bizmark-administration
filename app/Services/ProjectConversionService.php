<?php

namespace App\Services;

use App\Models\PermitApplication;
use App\Models\Project;
use App\Models\ProjectStatus;
use App\Models\Client;
use App\Models\Quotation;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProjectConversionService
{
    /**
     * Convert a PermitApplication to a Project after payment verification
     *
     * @param PermitApplication $application
     * @return Project
     * @throws \Exception
     */
    public function convertToProject(PermitApplication $application): Project
    {
        try {
            DB::beginTransaction();

            // Validation: Application must be payment_verified
            if ($application->status !== 'payment_verified') {
                throw new \Exception("Application must be payment_verified before conversion. Current status: {$application->status}");
            }

            // Check if already converted
            if ($application->project_id) {
                throw new \Exception("Application already converted to project ID: {$application->project_id}");
            }

            // Get quotation
            $quotation = $application->quotation;
            if (!$quotation) {
                throw new \Exception("No quotation found for application: {$application->application_number}");
            }

            // Get client
            $client = $application->client;
            if (!$client) {
                throw new \Exception("No client found for application: {$application->application_number}");
            }

            // Get "Planning" or first active project status
            // Priority: Kontrak > Pengumpulan Dokumen > First Active
            $projectStatus = ProjectStatus::where('name', 'LIKE', '%Kontrak%')
                ->orWhere('name', 'LIKE', '%Contract%')
                ->orWhere('code', 'contract')
                ->active()
                ->first();

            if (!$projectStatus) {
                $projectStatus = ProjectStatus::where('name', 'LIKE', '%Pengumpulan Dokumen%')
                    ->orWhere('code', 'document_collection')
                    ->active()
                    ->first();
            }

            if (!$projectStatus) {
                $projectStatus = ProjectStatus::active()->ordered()->first();
            }

            // Create Project
            $project = Project::create([
                'name' => "{$application->permitType->name} - {$client->name}",
                'description' => "Project created from permit application {$application->application_number}",
                'client_id' => $client->id,
                'permit_application_id' => $application->id,
                'client_name' => $client->name,
                'client_contact' => $client->email,
                'client_address' => $client->address ?? '-',
                'status_id' => $projectStatus?->id,
                'start_date' => now(),
                'deadline' => now()->addDays($application->permitType->processing_days ?? 30),
                'progress_percentage' => 0,
                'notes' => "Auto-generated from permit application system\n\nOriginal Application: {$application->application_number}\nPermit Type: {$application->permitType->name}",
                
                // Financial data from quotation
                'contract_value' => $quotation->total_amount,
                'down_payment' => $quotation->down_payment_amount,
                'payment_received' => 0, // Will be updated by payment events
                'total_expenses' => 0,
                'profit_margin' => 0,
                'payment_terms' => "DP: {$quotation->down_payment_percentage}%, Remaining: " . (100 - $quotation->down_payment_percentage) . "%",
                'payment_status' => 'partial', // DP received, remaining outstanding
            ]);

            // Update PermitApplication
            $application->update([
                'project_id' => $project->id,
                'status' => 'converted_to_project',
                'converted_at' => now(),
            ]);

            // Create status log
            $application->statusLogs()->create([
                'from_status' => 'payment_verified',
                'to_status' => 'converted_to_project',
                'notes' => "Converted to project: {$project->name}",
                'changed_by_type' => 'system',
                'changed_by_id' => null,
            ]);

            // Log conversion
            Log::info("PermitApplication converted to Project", [
                'application_id' => $application->id,
                'application_number' => $application->application_number,
                'project_id' => $project->id,
                'project_name' => $project->name,
                'client_id' => $client->id,
                'contract_value' => $quotation->total_amount,
            ]);

            DB::commit();

            return $project;

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error("Failed to convert PermitApplication to Project", [
                'application_id' => $application->id,
                'application_number' => $application->application_number ?? null,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            throw $e;
        }
    }

    /**
     * Check if an application can be converted
     *
     * @param PermitApplication $application
     * @return bool
     */
    public function canConvert(PermitApplication $application): bool
    {
        return $application->status === 'payment_verified' 
            && $application->project_id === null
            && $application->quotation !== null;
    }

    /**
     * Get conversion eligibility status with reason
     *
     * @param PermitApplication $application
     * @return array
     */
    public function getConversionStatus(PermitApplication $application): array
    {
        if ($application->project_id) {
            return [
                'eligible' => false,
                'reason' => 'Already converted to project',
                'project_id' => $application->project_id,
            ];
        }

        if ($application->status !== 'payment_verified') {
            return [
                'eligible' => false,
                'reason' => "Payment not verified. Current status: {$application->status}",
            ];
        }

        if (!$application->quotation) {
            return [
                'eligible' => false,
                'reason' => 'No quotation found',
            ];
        }

        return [
            'eligible' => true,
            'reason' => 'Ready for conversion',
        ];
    }
}
