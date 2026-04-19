<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PermitType;

class CalculatorController extends Controller
{
    /**
     * Show the permit calculator tool.
     */
    public function index()
    {
        $permitTypes = PermitType::orderBy('name')->get();
        
        $industries = [
            'Manufaktur',
            'Perdagangan',
            'Jasa',
            'Konstruksi',
            'F&B (Food & Beverage)',
            'Teknologi',
            'Kesehatan',
            'Pendidikan',
            'Properti',
            'Transportasi',
            'Pariwisata',
            'Lainnya',
        ];
        
        $cities = [
            'Jakarta',
            'Bogor',
            'Depok',
            'Tangerang',
            'Bekasi',
            'Bandung',
            'Surabaya',
            'Semarang',
            'Yogyakarta',
            'Lainnya',
        ];
        
        return view('tools.calculator', compact('permitTypes', 'industries', 'cities'));
    }

    /**
     * Calculate permit requirements and cost estimation.
     */
    public function calculate(Request $request)
    {
        $validated = $request->validate([
            'industry' => 'required|string',
            'permit_type_id' => 'required|exists:permit_types,id',
            'city' => 'required|string',
            'company_size' => 'required|in:small,medium,large',
            'urgency' => 'required|in:normal,fast,express',
        ]);

        $permitType = PermitType::findOrFail($validated['permit_type_id']);
        
        // Base cost calculation
        $baseCost = $this->getBaseCost($validated['company_size']);
        
        // Urgency multiplier
        $urgencyMultiplier = match($validated['urgency']) {
            'fast' => 1.5,
            'express' => 2.0,
            default => 1.0,
        };
        
        // City complexity multiplier
        $cityMultiplier = match($validated['city']) {
            'Jakarta' => 1.3,
            'Surabaya' => 1.2,
            'Bandung' => 1.1,
            default => 1.0,
        };
        
        $estimatedCost = $baseCost * $urgencyMultiplier * $cityMultiplier;
        
        // Timeline estimation (in working days)
        $baseTimeline = match($validated['company_size']) {
            'small' => 30,
            'medium' => 45,
            'large' => 60,
        };
        
        $estimatedTimeline = match($validated['urgency']) {
            'fast' => $baseTimeline * 0.7,
            'express' => $baseTimeline * 0.5,
            default => $baseTimeline,
        };
        
        // Required documents checklist
        $documents = $this->getRequiredDocuments($validated['industry'], $permitType);
        
        return response()->json([
            'success' => true,
            'data' => [
                'permit_type' => $permitType->name,
                'industry' => $validated['industry'],
                'city' => $validated['city'],
                'estimated_cost' => number_format($estimatedCost, 0, ',', '.'),
                'estimated_timeline' => ceil($estimatedTimeline),
                'urgency' => $validated['urgency'],
                'documents' => $documents,
                'complexity' => $this->getComplexityLevel($validated),
            ]
        ]);
    }

    /**
     * Get base cost based on company size.
     */
    private function getBaseCost($size)
    {
        return match($size) {
            'small' => 5000000,
            'medium' => 10000000,
            'large' => 20000000,
        };
    }

    /**
     * Get required documents based on industry and permit type.
     */
    private function getRequiredDocuments($industry, $permitType)
    {
        $baseDocuments = [
            'KTP Direktur/Pemilik',
            'NPWP Perusahaan',
            'Akta Pendirian Perusahaan',
            'SK Kemenkumham',
            'NIB (Nomor Induk Berusaha)',
        ];
        
        // Add industry-specific documents
        $industryDocs = match($industry) {
            'F&B (Food & Beverage)' => ['Sertifikat Halal (jika perlu)', 'Hasil Lab Pangan'],
            'Kesehatan' => ['Izin Praktik', 'Sertifikat Kompetensi'],
            'Konstruksi' => ['SBU Konstruksi', 'Izin Lokasi'],
            default => [],
        };
        
        return array_merge($baseDocuments, $industryDocs);
    }

    /**
     * Get complexity level.
     */
    private function getComplexityLevel($data)
    {
        $score = 0;
        
        // Company size complexity
        $score += match($data['company_size']) {
            'large' => 3,
            'medium' => 2,
            'small' => 1,
        };
        
        // City complexity
        $score += match($data['city']) {
            'Jakarta' => 3,
            'Surabaya', 'Bandung' => 2,
            default => 1,
        };
        
        // Urgency complexity
        $score += match($data['urgency']) {
            'express' => 3,
            'fast' => 2,
            'normal' => 1,
        };
        
        return match(true) {
            $score >= 7 => 'Tinggi',
            $score >= 5 => 'Sedang',
            default => 'Rendah',
        };
    }
}
