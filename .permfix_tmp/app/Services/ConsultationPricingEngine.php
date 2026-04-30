<?php

namespace App\Services;

use App\Models\Kbli;
use Illuminate\Support\Facades\Log;

/**
 * ConsultationPricingEngine
 * 
 * Advanced pricing engine that uses OpenRouter AI for accurate cost estimation.
 * Integrates with existing OpenRouterService for permit analysis.
 * 
 * Input Variables:
 * - kbli_code (5-digit): Business classification
 * - business_size: micro, small, medium, large
 * - location: Province/city name
 * - location_type: industrial, commercial, residential, rural
 * - investment_level: under_100m, 100m_500m, 500m_2b, over_2b
 * - employee_count: Number of employees
 * - project_description: Detailed business description
 * - deliverables_requested: Array of requested services
 */
class ConsultationPricingEngine
{
    protected OpenRouterService $openRouterService;
    
    public function __construct(OpenRouterService $openRouterService)
    {
        $this->openRouterService = $openRouterService;
    }

    /**
     * Calculate comprehensive cost estimate using AI
     * 
     * @param array $params Input parameters for estimation
     * @return array Detailed cost breakdown with AI analysis
     */
    public function calculateEstimate(array $params): array
    {
        $startTime = microtime(true);
        
        try {
            // Validate input
            $validated = $this->validateInput($params);
            
            // Get KBLI data (5-digit only)
            $kbli = Kbli::whereRaw('LENGTH(code) = 5')
                        ->where('code', $validated['kbli_code'])
                        ->where('is_active', true)
                        ->first();
            
            if (!$kbli) {
                return $this->fallbackEstimate($validated, 'KBLI code not found or not 5-digit');
            }
            
            // Get base estimate from KBLI template
            $baseEstimate = $this->getBaseEstimate($kbli, $validated);
            
            // Apply business size multiplier
            $sizeMultiplier = $this->getBusinessSizeMultiplier($validated['business_size']);
            $locationMultiplier = $this->getLocationMultiplier($validated['location_type']);
            
            // Calculate adjusted costs
            $adjustedEstimate = $this->applyMultipliers(
                $baseEstimate,
                $sizeMultiplier,
                $locationMultiplier
            );
            
            // Use OpenRouter AI for detailed permit analysis and cost refinement
            $aiAnalysis = $this->getAIEnhancedAnalysis($kbli, $validated, $adjustedEstimate);
            
            // Merge all data
            $finalEstimate = $this->mergeFinalEstimate(
                $kbli,
                $baseEstimate,
                $adjustedEstimate,
                $aiAnalysis,
                $validated
            );
            
            $finalEstimate['processing_time_ms'] = (int) ((microtime(true) - $startTime) * 1000);
            $finalEstimate['generated_at'] = now()->toIso8601String();
            $finalEstimate['confidence_score'] = $this->calculateConfidenceScore($kbli, $aiAnalysis);
            
            Log::info('ConsultationPricingEngine: Estimate calculated', [
                'kbli_code' => $validated['kbli_code'],
                'total_cost' => $finalEstimate['cost_summary']['grand_total'],
                'processing_time' => $finalEstimate['processing_time_ms'],
            ]);
            
            return $finalEstimate;
            
        } catch (\Exception $e) {
            Log::error('ConsultationPricingEngine: Calculation failed', [
                'error' => $e->getMessage(),
                'params' => $params,
            ]);
            
            return $this->fallbackEstimate($params, $e->getMessage());
        }
    }

    /**
     * Validate and normalize input parameters
     */
    protected function validateInput(array $params): array
    {
        // Required fields
        $validated = [
            'kbli_code' => $params['kbli_code'] ?? null,
            'business_size' => $params['business_size'] ?? 'small',
            'location' => $params['location'] ?? 'Jakarta',
            'location_type' => $params['location_type'] ?? 'commercial',
            'investment_level' => $params['investment_level'] ?? 'under_100m',
            'employee_count' => $params['employee_count'] ?? 5,
            'project_description' => $params['project_description'] ?? '',
            'deliverables_requested' => $params['deliverables_requested'] ?? [],
        ];
        
        // Validate KBLI code (must be 5 digits)
        if (!$validated['kbli_code'] || !preg_match('/^\d{5}$/', $validated['kbli_code'])) {
            throw new \InvalidArgumentException('KBLI code must be exactly 5 digits');
        }
        
        // Validate business size
        if (!in_array($validated['business_size'], ['micro', 'small', 'medium', 'large'])) {
            throw new \InvalidArgumentException('Invalid business size');
        }
        
        // Map location type to internal calculation values
        $locationTypeMap = [
            'commercial' => 'commercial',
            'industrial' => 'industrial',
            'residential' => 'residential',
            'mixed_use' => 'commercial',        // Mixed-use treated as commercial
            'special_economic' => 'industrial', // KEK treated as industrial
            'rural_agricultural' => 'rural',   // Rural/agricultural
            'tourism' => 'commercial',          // Tourism as commercial
            'educational' => 'residential',     // Educational as residential-like
        ];
        
        $originalLocationType = $validated['location_type'];
        if (isset($locationTypeMap[$originalLocationType])) {
            $validated['location_type'] = $locationTypeMap[$originalLocationType];
        } else {
            throw new \InvalidArgumentException('Invalid location type: ' . $originalLocationType);
        }
        
        return $validated;
    }

    /**
     * Get base estimate from investment level and KBLI complexity
     * 
     * New approach: Calculate as 8-12% of investment value
     */
    protected function getBaseEstimate(Kbli $kbli, array $params): array
    {
        // Get investment value in IDR
        $investmentValue = $this->getInvestmentValue($params['investment_level']);
        
        // Calculate license fee percentage based on complexity
        $licensePercentage = $this->getLicensePercentage($kbli, $params);
        
        // Base calculation: percentage of investment
        $totalLicenseCost = (int) round($investmentValue * $licensePercentage / 100, -4);
        
        // Ensure minimum cost based on business size
        $minCost = $this->getMinimumCost($params['business_size'], $kbli->complexity_level);
        $totalLicenseCost = max($totalLicenseCost, $minCost);
        
        // Break down into components
        $breakdown = $this->calculateCostBreakdown($totalLicenseCost, $kbli, $params);
        
        return $breakdown;
    }
    
    /**
     * Get investment value from level
     */
    protected function getInvestmentValue(string $level): int
    {
        return match($level) {
            'under_100m' => 75_000_000,      // Use 75M as mid-point
            '100m_500m' => 300_000_000,      // Use 300M as mid-point  
            '500m_2b' => 1_250_000_000,      // Use 1.25B as mid-point
            '2b_10b' => 6_000_000_000,       // Use 6B as mid-point
            '10b_50b' => 30_000_000_000,     // Use 30B as mid-point
            'above_50b' => 100_000_000_000,  // Use 100B as conservative estimate
            default => 300_000_000,          // Default 300M
        };
    }
    
    /**
     * Get license percentage based on complexity and business type
     */
    protected function getLicensePercentage(Kbli $kbli, array $params): float
    {
        // Base percentage range: 8-12%
        $basePercentage = match($kbli->complexity_level) {
            'low' => 8.0,      // Simple businesses
            'medium' => 10.0,  // Standard businesses 
            'high' => 12.0,    // Complex businesses (Real Estate, Manufacturing, etc)
            default => 10.0,
        };
        
        // Adjust by business size (larger businesses need more complex licensing)
        $sizeAdjustment = match($params['business_size']) {
            'micro' => -1.0,   // 7-11%
            'small' => 0.0,    // 8-12% 
            'medium' => +1.0,  // 9-13%
            'large' => +2.0,   // 10-14%
            default => 0.0,
        };
        
        // Adjust by location type  
        $locationAdjustment = match($params['location_type']) {
            'industrial' => +1.5,    // More complex regulations
            'commercial' => 0.0,     // Standard
            'residential' => -0.5,   // Simpler
            'rural' => -1.0,         // Much simpler
            default => 0.0,
        };
        
        // Entity type adjustment (business structure complexity)
        $entityAdjustment = 0.0;
        if (isset($params['entity_type'])) {
            $entityAdjustment = match($params['entity_type']) {
                'individual' => -1.0,        // Simplest
                'cv', 'firma' => -0.5,       // Simple partnerships
                'pt' => 0.0,                 // Standard corporation
                'pt_pma' => +2.0,            // Foreign investment - more complex
                'persero', 'perum', 'bumn' => +1.0,  // State enterprises
                'koperasi' => -0.5,          // Cooperative - simpler
                'yayasan', 'perkumpulan' => 0.0,     // Non-profit
                'foreign_rep' => +1.5,       // Foreign representation
                default => 0.0,
            };
        }
        
        // Business nature adjustment
        $natureAdjustment = 0.0;
        if (isset($params['business_nature'])) {
            $natureAdjustment = match($params['business_nature']) {
                'local_market' => 0.0,           // Standard
                'export_oriented' => +1.0,       // More permits needed
                'import_dependent' => +1.5,      // Import licenses
                'b2b_services' => -0.5,          // Usually simpler
                'b2c_retail' => 0.0,             // Standard retail
                'online_marketplace' => -0.5,    // Digital business
                'franchise' => +0.5,             // Brand compliance
                'government_contractor' => +2.0, // Strict requirements
                'high_risk' => +3.0,             // Mining, chemical, etc
                default => 0.0,
            };
        }
        
        $finalPercentage = $basePercentage + $sizeAdjustment + $locationAdjustment + $entityAdjustment + $natureAdjustment;
        
        // Cap between 6% and 18% (expanded range for complex cases)
        return max(6.0, min(18.0, $finalPercentage));
    }
    
    /**
     * Get minimum cost based on business size and complexity
     * Uses AI settings for minimum grand total
     */
    protected function getMinimumCost(string $businessSize, string $complexity): int
    {
        $minimumGrandTotal = (int) AISettingService::get('pricing.minimum_grand_total', 3000000);
        
        $baseCosts = [
            'low' => ['micro' => $minimumGrandTotal, 'small' => 5_000_000, 'medium' => 8_000_000, 'large' => 15_000_000],
            'medium' => ['micro' => 5_000_000, 'small' => 8_000_000, 'medium' => 12_000_000, 'large' => 20_000_000],
            'high' => ['micro' => 8_000_000, 'small' => 15_000_000, 'medium' => 25_000_000, 'large' => 40_000_000],
        ];
        
        return $baseCosts[$complexity][$businessSize] ?? 8_000_000;
    }
    
    /**
     * Break down total cost into components
     */
    protected function calculateCostBreakdown(int $totalCost, Kbli $kbli, array $params): array
    {
        // Allocate percentages
        $biayaPemerintah = (int) ($totalCost * 0.25);    // 25% government fees
        $biayaKonsultan = (int) ($totalCost * 0.55);     // 55% consulting fees 
        $overhead = (int) ($totalCost * 0.20);           // 20% overhead & admin
        
        // Breakdown government fees
        $governmentBreakdown = [
            'izin_usaha' => (int) ($biayaPemerintah * 0.4),      // 40% business permits
            'izin_teknis' => (int) ($biayaPemerintah * 0.35),    // 35% technical permits
            'izin_lingkungan' => (int) ($biayaPemerintah * 0.15), // 15% environmental
            'retribusi_daerah' => (int) ($biayaPemerintah * 0.1),  // 10% local fees
        ];
        
        // Breakdown consulting fees based on typical hours
        $complexity = $kbli->complexity_level;
        $totalHours = match($complexity) {
            'low' => 20,     // Simple: 20 hours
            'medium' => 35,  // Standard: 35 hours
            'high' => 60,    // Complex: 60 hours  
            default => 35,
        };
        
        // Adjust hours by business size
        $sizeMultiplier = match($params['business_size']) {
            'micro' => 0.8,
            'small' => 1.0,
            'medium' => 1.4,
            'large' => 1.8,
            default => 1.0,
        };
        
        $adjustedHours = (int) ($totalHours * $sizeMultiplier);
        $averageRate = $adjustedHours > 0 ? (int) ($biayaKonsultan / $adjustedHours) : 200000;
        
        $consultingBreakdown = [
            'konsultasi_awal' => [
                'hours' => max(2, (int) ($adjustedHours * 0.1)),
                'rate' => 250000,
                'cost' => (int) ($biayaKonsultan * 0.1),
            ],
            'persiapan_dokumen' => [
                'hours' => max(6, (int) ($adjustedHours * 0.4)),
                'rate' => $averageRate,
                'cost' => (int) ($biayaKonsultan * 0.4),
            ],
            'pengajuan_izin' => [
                'hours' => max(4, (int) ($adjustedHours * 0.3)),
                'rate' => $averageRate,
                'cost' => (int) ($biayaKonsultan * 0.3),
            ],
            'monitoring_followup' => [
                'hours' => max(2, (int) ($adjustedHours * 0.2)),
                'rate' => 150000,
                'cost' => (int) ($biayaKonsultan * 0.2),
            ],
        ];
        
        $overheadPercentage = (float) AISettingService::get('pricing.overhead_percentage', 20);
        
        return [
            'biaya_pemerintah' => [
                'breakdown' => $governmentBreakdown,
                'total' => array_sum($governmentBreakdown),
            ],
            'biaya_konsultan' => [
                'breakdown' => $consultingBreakdown,
                'total_hours' => $adjustedHours,
                'total' => array_sum(array_column($consultingBreakdown, 'cost')),
            ],
            'overhead' => [
                'percentage' => $overheadPercentage,
                'amount' => $overhead,
                'description' => 'Admin, koordinasi, dan manajemen project'
            ],
            'subtotal' => $biayaPemerintah + $biayaKonsultan,
            'grand_total' => $totalCost,
            'investment_percentage' => $this->getLicensePercentage($kbli, $params),
            'investment_value' => $this->getInvestmentValue($params['investment_level']),
        ];
    }

    /**
     * Get business size multiplier
     */
    /**
     * Get business size multiplier from AI settings (dynamic)
     */
    protected function getBusinessSizeMultiplier(string $size): float
    {
        $key = "pricing.size_multiplier.{$size}";
        $default = match($size) {
            'micro' => 1.0,
            'small' => 1.3,
            'medium' => 1.8,
            'large' => 2.5,
            default => 1.0,
        };
        
        return (float) AISettingService::get($key, $default);
    }

    /**
     * Get location multiplier from AI settings (dynamic)
     */
    protected function getLocationMultiplier(string $locationType): float
    {
        $key = "pricing.location_multiplier.{$locationType}";
        $default = match($locationType) {
            'industrial' => 1.2,  // More permits required
            'commercial' => 1.0,   // Standard
            'residential' => 0.9,  // Simpler requirements
            'rural' => 0.8,        // Less complex
            default => 1.0,
        };
        
        return (float) AISettingService::get($key, $default);
    }

    /**
     * Apply location and complexity adjustments to investment-based estimate
     * 
     * Note: Business size already factored into investment percentage calculation
     */
    protected function applyMultipliers(array $base, float $sizeMultiplier, float $locationMultiplier): array
    {
        // In the new system, business size is already factored in during base calculation
        // Only apply location multiplier for regional variations
        
        $locationAdjustment = $locationMultiplier;
        
        $adjusted = $base;
        
        // Apply location adjustment to consulting fees only (government fees are fixed)
        if (isset($adjusted['biaya_konsultan']['breakdown'])) {
            foreach ($adjusted['biaya_konsultan']['breakdown'] as $key => &$data) {
                if (isset($data['cost'])) {
                    $data['cost'] = (int) ($data['cost'] * $locationAdjustment);
                }
                if (isset($data['hours'])) {
                    $data['hours'] = round($data['hours'] * $locationAdjustment, 1);
                }
            }
            
            // Recalculate consulting total
            $adjusted['biaya_konsultan']['total'] = array_sum(array_column($adjusted['biaya_konsultan']['breakdown'], 'cost'));
            $adjusted['biaya_konsultan']['total_hours'] = array_sum(array_column($adjusted['biaya_konsultan']['breakdown'], 'hours'));
        }
        
        // Recalculate totals
        $adjusted['subtotal'] = ($adjusted['biaya_pemerintah']['total'] ?? 0) + ($adjusted['biaya_konsultan']['total'] ?? 0);
        $adjusted['overhead']['amount'] = (int) ($adjusted['subtotal'] * ($adjusted['overhead']['percentage'] / 100));
        $adjusted['grand_total'] = (int) round($adjusted['subtotal'] + $adjusted['overhead']['amount'], -4);
        
        $adjusted['multipliers_applied'] = [
            'business_size' => $sizeMultiplier,      // Already applied in base calculation
            'location' => $locationMultiplier,       // Applied to consulting fees
            'combined' => $locationAdjustment,       // Effective multiplier
            'note' => 'Business size integrated in investment percentage calculation',
        ];
        
        return $adjusted;
    }

    /**
     * Get AI-enhanced analysis using OpenRouter
     */
    protected function getAIEnhancedAnalysis(Kbli $kbli, array $params, array $adjustedEstimate): ?array
    {
        try {
            // Use existing OpenRouterService for permit analysis
            $aiResult = $this->openRouterService->generatePermitRecommendations(
                $kbli->code,
                $kbli->description,
                $kbli->sector ?? '',
                $params['business_size'],
                $params['location_type'],
                null  // client_id not available in public consultation
            );
            
            if ($aiResult) {
                // Add cost refinement based on AI recommendations
                $aiCostAdjustment = $this->calculateAICostAdjustment($aiResult, $adjustedEstimate);
                
                return [
                    'permits' => $aiResult['recommended_permits'] ?? [],
                    'documents' => $aiResult['required_documents'] ?? [],
                    'risk_assessment' => $aiResult['risk_assessment'] ?? null,
                    'timeline' => $aiResult['estimated_timeline'] ?? null,
                    'cost_adjustment' => $aiCostAdjustment,
                    'ai_model' => $aiResult['ai_model'] ?? 'unknown',
                    'confidence' => $aiResult['confidence_score'] ?? 0.5,
                ];
            }
            
            return null;
            
        } catch (\Exception $e) {
            Log::warning('AI analysis failed, using base estimate', ['error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Calculate cost adjustment based on AI recommendations
     */
    protected function calculateAICostAdjustment(?array $aiResult, array $baseEstimate): array
    {
        if (!$aiResult || !isset($aiResult['recommended_permits'])) {
            return ['adjustment_factor' => 1.0, 'reason' => 'No AI data'];
        }
        
        $permits = $aiResult['recommended_permits'];
        $mandatoryCount = count(array_filter($permits, fn($p) => ($p['type'] ?? '') === 'mandatory'));
        $highComplexity = isset($aiResult['risk_assessment']['level']) && $aiResult['risk_assessment']['level'] === 'high';
        
        $adjustmentFactor = 1.0;
        $reasons = [];
        
        // More mandatory permits = higher cost
        if ($mandatoryCount > 8) {
            $adjustmentFactor += 0.2;
            $reasons[] = "Many mandatory permits ({$mandatoryCount})";
        } elseif ($mandatoryCount > 5) {
            $adjustmentFactor += 0.1;
            $reasons[] = "Multiple mandatory permits ({$mandatoryCount})";
        }
        
        // High complexity = higher cost
        if ($highComplexity) {
            $adjustmentFactor += 0.15;
            $reasons[] = "High complexity business";
        }
        
        return [
            'adjustment_factor' => $adjustmentFactor,
            'mandatory_permits_count' => $mandatoryCount,
            'reasons' => $reasons,
        ];
    }

    /**
     * Merge all estimates into final result
     */
    protected function mergeFinalEstimate(Kbli $kbli, array $base, array $adjusted, ?array $ai, array $params): array
    {
        // Apply AI cost adjustment if available
        $finalCosts = $adjusted;
        if ($ai && isset($ai['cost_adjustment']['adjustment_factor'])) {
            $factor = $ai['cost_adjustment']['adjustment_factor'];
            
            // Apply AI adjustment to consulting fees only
            if (isset($finalCosts['biaya_konsultan']['total'])) {
                $finalCosts['biaya_konsultan']['total'] = (int) ($adjusted['biaya_konsultan']['total'] * $factor);
                
                // Proportionally adjust breakdown
                foreach ($finalCosts['biaya_konsultan']['breakdown'] as $key => &$data) {
                    if (isset($data['cost'])) {
                        $data['cost'] = (int) ($data['cost'] * $factor);
                    }
                }
            }
            
            // Recalculate totals
            $finalCosts['subtotal'] = ($finalCosts['biaya_pemerintah']['total'] ?? 0) + ($finalCosts['biaya_konsultan']['total'] ?? 0);
            $finalCosts['overhead']['amount'] = (int) ($finalCosts['subtotal'] * ($finalCosts['overhead']['percentage'] / 100));
            $finalCosts['grand_total'] = (int) round($finalCosts['subtotal'] + $finalCosts['overhead']['amount'], -4);
        }
        
        // Calculate cost range (±15%)
        $minCost = (int) round($finalCosts['grand_total'] * 0.85, -4);
        $maxCost = (int) round($finalCosts['grand_total'] * 1.15, -4);
        
        return [
            'kbli' => [
                'code' => $kbli->code,
                'description' => $kbli->description,
                'category' => $kbli->category,
                'complexity_level' => $kbli->complexity_level,
            ],
            'input_parameters' => [
                'business_size' => $params['business_size'],
                'location' => $params['location'],
                'location_type' => $params['location_type'],
                'investment_level' => $params['investment_level'],
                'employee_count' => $params['employee_count'],
            ],
            'cost_breakdown' => [
                'biaya_pemerintah' => $finalCosts['biaya_pemerintah'] ?? [],
                'biaya_konsultan' => $finalCosts['biaya_konsultan'] ?? [],
                'overhead' => $finalCosts['overhead'] ?? [],
            ],
            'cost_summary' => [
                'subtotal' => $finalCosts['subtotal'],
                'overhead' => $finalCosts['overhead']['amount'],
                'grand_total' => $finalCosts['grand_total'],
                'cost_range' => [
                    'min' => $minCost,
                    'max' => $maxCost,
                    'currency' => 'IDR',
                ],
                'formatted' => [
                    'subtotal' => 'Rp ' . number_format($finalCosts['subtotal'], 0, ',', '.'),
                    'grand_total' => 'Rp ' . number_format($finalCosts['grand_total'], 0, ',', '.'),
                    'range' => 'Rp ' . number_format($minCost, 0, ',', '.') . ' - Rp ' . number_format($maxCost, 0, ',', '.'),
                ],
            ],
            'multipliers' => $adjusted['multipliers_applied'] ?? [],
            'ai_analysis' => $ai ? [
                'permits' => $ai['permits'] ?? [],
                'documents' => $ai['documents'] ?? [],
                'risk_assessment' => $ai['risk_assessment'] ?? null,
                'timeline' => $ai['timeline'] ?? null,
                'cost_adjustment' => $ai['cost_adjustment'] ?? null,
                'model_used' => $ai['ai_model'] ?? null,
            ] : null,
            'estimate_notes' => [
                'Estimasi ini dihitung berdasarkan ' . round($finalCosts['investment_percentage'] ?? 10, 1) . '% dari nilai investasi (' . 'Rp ' . number_format($finalCosts['investment_value'] ?? 0, 0, ',', '.') . ')',
                'Biaya aktual dapat bervariasi tergantung kelengkapan dokumen dan persyaratan khusus',
                'Biaya Pemerintah = PNBP, retribusi daerah, dan biaya resmi lainnya (25%)',
                'Biaya Konsultan = Jasa konsultasi, persiapan dokumen, dan pengurusan (55%)', 
                'Overhead = Administrasi, koordinasi, dan manajemen project (20%)',
                'Untuk breakdown detail dan penawaran resmi, silakan daftar di client portal kami',
                'Estimasi ini telah disesuaikan dengan kompleksitas usaha dan lokasi bisnis Anda',
            ],
            'investment_value' => $finalCosts['investment_value'] ?? 0,
            'investment_percentage' => $finalCosts['investment_percentage'] ?? 10,
        ];
    }

    /**
     * Calculate confidence score
     */
    protected function calculateConfidenceScore(Kbli $kbli, ?array $aiAnalysis): float
    {
        $score = 0.5; // Base score
        
        // KBLI has pricing data
        if ($kbli->default_direct_costs && $kbli->default_hours_estimate) {
            $score += 0.2;
        }
        
        // AI analysis available
        if ($aiAnalysis) {
            $score += 0.15;
            
            // AI has high confidence
            if (($aiAnalysis['confidence'] ?? 0) > 0.7) {
                $score += 0.1;
            }
        }
        
        // KBLI has been used before
        if ($kbli->usage_count > 0) {
            $score += min(0.05 * log10($kbli->usage_count + 1), 0.15);
        }
        
        return min(1.0, round($score, 2));
    }

    /**
     * Fallback estimate when AI or data not available
     */
    protected function fallbackEstimate(array $params, string $reason): array
    {
        Log::warning('Using fallback estimate', ['reason' => $reason, 'params' => $params]);
        
        $minimumGrandTotal = (int) AISettingService::get('pricing.minimum_grand_total', 3000000);
        $defaultTotal = $minimumGrandTotal + 2000000; // Add 2M for safer estimate
        
        return [
            'kbli' => [
                'code' => $params['kbli_code'] ?? 'unknown',
                'description' => 'Data not available',
                'category' => 'Unknown',
                'complexity_level' => 'medium',
            ],
            'cost_summary' => [
                'grand_total' => $defaultTotal,
                'cost_range' => [
                    'min' => $minimumGrandTotal,
                    'max' => $defaultTotal + 3000000,
                    'currency' => 'IDR',
                ],
                'formatted' => [
                    'grand_total' => 'Rp ' . number_format($defaultTotal, 0, ',', '.'),
                    'range' => 'Rp ' . number_format($minimumGrandTotal, 0, ',', '.') . ' - Rp ' . number_format($defaultTotal + 3000000, 0, ',', '.'),
                ],
            ],
            'estimate_notes' => [
                'This is a fallback estimate due to: ' . $reason,
                'Please contact us for detailed consultation and accurate quotation',
                'Register in our client portal for full service access',
            ],
            'confidence_score' => 0.3,
            'fallback_reason' => $reason,
            'generated_at' => now()->toIso8601String(),
        ];
    }
}
