<?php

namespace App\Services;

use App\Models\Project;
use Illuminate\Support\Facades\Log;

class ProjectContextBuilder
{
    /**
     * Build context array from project for AI paraphrasing
     */
    public function buildContext(Project $project, array $additionalData = []): array
    {
        try {
            $context = [
                // Basic project info
                'project_name' => $project->name,
                // Derive a human-friendly code if not present
                'project_code' => $project->code ?? ('PRJ-' . str_pad((string) $project->id, 5, '0', STR_PAD_LEFT)),
                'project_description' => $project->description,
                'project_status' => $project->status,
                
                // Client info (Bizmark uses client_name directly in projects table)
                'client_name' => $project->client_name ?? 'N/A',
                'client_address' => $project->client_address ?? 'N/A',
                'client_phone' => $project->client_contact ?? 'N/A',
                
                // Location info (use client_address as fallback for location)
                'location' => $additionalData['location'] ?? $project->client_address ?? 'N/A',
                'province' => $additionalData['province'] ?? 'N/A',
                'regency' => $additionalData['regency'] ?? 'N/A',
                'district' => $additionalData['district'] ?? 'N/A',
                
                // Dates
                'start_date' => $project->start_date?->format('d F Y') ?? 'N/A',
                'end_date' => $project->deadline?->format('d F Y') ?? 'N/A',
                'duration' => $this->calculateDuration($project),
                
                // Financial
                'budget' => $this->formatCurrency($project->budget),
                'total_paid' => $this->formatCurrency($project->actual_cost),
                
                // Project specifics
                'land_area' => $additionalData['land_area'] ?? 'N/A',
                'building_area' => $additionalData['building_area'] ?? 'N/A',
                'land_certificate' => $additionalData['land_certificate'] ?? 'N/A',
                'business_type' => $additionalData['business_type'] ?? 'N/A',
                
                // Permit related
                'permit_type' => $additionalData['permit_type'] ?? 'N/A',
                'institution' => $additionalData['institution'] ?? 'N/A',
                
                // Contact person (use additional_context or N/A)
                'pic_name' => $additionalData['pic_name'] ?? $project->client_name ?? 'N/A',
                'pic_phone' => $additionalData['pic_phone'] ?? $project->client_contact ?? 'N/A',
                'pic_email' => $additionalData['pic_email'] ?? 'N/A',
            ];

            // Merge additional data (will override defaults)
            if (!empty($additionalData)) {
                $context = array_merge($context, $additionalData);
            }

            // Remove null values
            $context = array_filter($context, function($value) {
                return $value !== null;
            });

            return $context;

        } catch (\Exception $e) {
            Log::error('Failed to build project context: ' . $e->getMessage());
            
            return [
                'project_name' => $project->name ?? 'Unknown Project',
                'error' => 'Failed to build complete context',
            ];
        }
    }

    /**
     * Calculate project duration in human-readable format
     */
    protected function calculateDuration(Project $project): string
    {
        if (!$project->start_date || !$project->deadline) {
            return 'N/A';
        }

        $days = $project->start_date->diffInDays($project->deadline);
        
        if ($days < 30) {
            return $days . ' hari';
        } elseif ($days < 365) {
            $months = round($days / 30);
            return $months . ' bulan';
        } else {
            $years = round($days / 365, 1);
            return $years . ' tahun';
        }
    }

    /**
     * Format currency to Rupiah
     */
    protected function formatCurrency(?float $amount): string
    {
        if ($amount === null) {
            return 'N/A';
        }

        return 'Rp ' . number_format($amount, 0, ',', '.');
    }

    /**
     * Extract required fields from template
     */
    public function extractRequiredFields(array $templateRequiredFields, array $projectData): array
    {
        $extracted = [];

        foreach ($templateRequiredFields as $field) {
            $extracted[$field] = $projectData[$field] ?? 'N/A';
        }

        return $extracted;
    }

    /**
     * Validate that all required fields are present
     */
    public function validateRequiredFields(array $requiredFields, array $context): array
    {
        $missing = [];

        foreach ($requiredFields as $field) {
            if (!isset($context[$field]) || $context[$field] === 'N/A' || empty($context[$field])) {
                $missing[] = $field;
            }
        }

        return [
            'valid' => empty($missing),
            'missing_fields' => $missing,
        ];
    }

    /**
     * Build context summary for display
     */
    public function buildContextSummary(array $context): string
    {
        $summary = "KONTEKS PROYEK:\n";
        $summary .= "====================\n\n";

        foreach ($context as $key => $value) {
            $label = ucwords(str_replace('_', ' ', $key));
            $summary .= "{$label}: {$value}\n";
        }

        return $summary;
    }

    /**
     * Get default required fields for permit types
     */
    public function getDefaultRequiredFields(string $permitType): array
    {
        $defaults = [
            'pertek_bpn' => [
                'project_name',
                'client_name',
                'location',
                'land_area',
                'land_certificate',
                'pic_name',
            ],
            'ukl_upl' => [
                'project_name',
                'client_name',
                'location',
                'business_type',
                'land_area',
                'building_area',
            ],
            'amdal' => [
                'project_name',
                'client_name',
                'location',
                'business_type',
                'land_area',
                'environmental_impact',
            ],
            'imb' => [
                'project_name',
                'client_name',
                'location',
                'building_area',
                'building_function',
                'land_certificate',
            ],
        ];

        return $defaults[$permitType] ?? [
            'project_name',
            'client_name',
            'location',
        ];
    }
}
