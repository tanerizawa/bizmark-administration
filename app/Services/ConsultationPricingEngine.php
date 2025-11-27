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
        
        // Validate location type
        if (!in_array($validated['location_type'], ['industrial', 'commercial', 'residential', 'rural'])) {
            throw new \InvalidArgumentException('Invalid location type');
        }
        
        return $validated;
    }

    /**
     * Get base estimate from KBLI template
     */
    protected function getBaseEstimate(Kbli $kbli, array $params): array
    {
        $directCosts = $kbli->default_direct_costs ?? [
            'printing' => 200000,
            'permits' => 500000,
            'lab_tests' => 1000000,
            'field_equipment' => 300000,
        ];
        
        $hoursEstimate = $kbli->default_hours_estimate ?? [
            'admin' => 2,
            'technical' => 12,
            'review' => 4,
            'field' => 8,
        ];
        
        $hourlyRates = $kbli->default_hourly_rates ?? [
            'admin' => 100000,
            'technical' => 200000,
            'review' => 150000,
            'field' => 175000,
        ];
        
        // Calculate service fees
        $serviceFees = [];
        $totalServiceHours = 0;
        $totalServiceCost = 0;
        
        foreach ($hoursEstimate as $role => $hours) {
            $rate = $hourlyRates[$role] ?? 150000;
            $cost = $hours * $rate;
            $serviceFees[$role] = [
                'hours' => $hours,
                'rate' => $rate,
                'cost' => $cost,
            ];
            $totalServiceHours += $hours;
            $totalServiceCost += $cost;
        }
        
        // Calculate direct costs total
        $totalDirectCosts = array_sum($directCosts);
        
        // Calculate subtotal
        $subtotal = $totalDirectCosts + $totalServiceCost;
        
        // Apply overhead (10%)
        $overhead = $subtotal * 0.10;
        
        // Grand total
        $grandTotal = $subtotal + $overhead;
        
        return [
            'biaya_pokok' => [
                'breakdown' => $directCosts,
                'total' => $totalDirectCosts,
            ],
            'biaya_jasa' => [
                'breakdown' => $serviceFees,
                'total_hours' => $totalServiceHours,
                'total' => $totalServiceCost,
            ],
            'overhead' => [
                'percentage' => 10,
                'amount' => $overhead,
            ],
            'subtotal' => $subtotal,
            'grand_total' => (int) round($grandTotal, -4), // Round to nearest 10k
        ];
    }

    /**
     * Get business size multiplier
     */
    protected function getBusinessSizeMultiplier(string $size): float
    {
        return match($size) {
            'micro' => 1.0,
            'small' => 1.3,
            'medium' => 1.8,
            'large' => 2.5,
            default => 1.0,
        };
    }

    /**
     * Get location multiplier
     */
    protected function getLocationMultiplier(string $locationType): float
    {
        return match($locationType) {
            'industrial' => 1.2,  // More permits required
            'commercial' => 1.0,   // Standard
            'residential' => 0.9,  // Simpler requirements
            'rural' => 0.8,        // Less complex
            default => 1.0,
        };
    }

    /**
     * Apply multipliers to base estimate
     */
    protected function applyMultipliers(array $base, float $sizeMultiplier, float $locationMultiplier): array
    {
        $combined = $sizeMultiplier * $locationMultiplier;
        
        $adjusted = $base;
        
        // Apply multiplier to service fees (biaya jasa)
        foreach ($adjusted['biaya_jasa']['breakdown'] as $role => &$data) {
            $data['hours'] = round($data['hours'] * $combined, 1);
            $data['cost'] = (int) ($data['hours'] * $data['rate']);
        }
        
        // Recalculate totals
        $adjusted['biaya_jasa']['total_hours'] = array_sum(array_column($adjusted['biaya_jasa']['breakdown'], 'hours'));
        $adjusted['biaya_jasa']['total'] = array_sum(array_column($adjusted['biaya_jasa']['breakdown'], 'cost'));
        
        // Apply multiplier to some direct costs (field equipment, permits)
        $adjusted['biaya_pokok']['breakdown']['permits'] = (int) ($base['biaya_pokok']['breakdown']['permits'] * $combined);
        $adjusted['biaya_pokok']['breakdown']['field_equipment'] = (int) ($base['biaya_pokok']['breakdown']['field_equipment'] * $combined);
        $adjusted['biaya_pokok']['total'] = array_sum($adjusted['biaya_pokok']['breakdown']);
        
        // Recalculate totals
        $adjusted['subtotal'] = $adjusted['biaya_pokok']['total'] + $adjusted['biaya_jasa']['total'];
        $adjusted['overhead']['amount'] = (int) ($adjusted['subtotal'] * 0.10);
        $adjusted['grand_total'] = (int) round($adjusted['subtotal'] + $adjusted['overhead']['amount'], -4);
        
        $adjusted['multipliers_applied'] = [
            'business_size' => $sizeMultiplier,
            'location' => $locationMultiplier,
            'combined' => $combined,
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
            $finalCosts['biaya_jasa']['total'] = (int) ($adjusted['biaya_jasa']['total'] * $factor);
            $finalCosts['subtotal'] = $finalCosts['biaya_pokok']['total'] + $finalCosts['biaya_jasa']['total'];
            $finalCosts['overhead']['amount'] = (int) ($finalCosts['subtotal'] * 0.10);
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
                'biaya_pokok' => $finalCosts['biaya_pokok'],
                'biaya_jasa' => $finalCosts['biaya_jasa'],
                'overhead' => $finalCosts['overhead'],
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
                'This is an AI-enhanced estimate based on multiple factors',
                'Actual costs may vary depending on document completeness and specific requirements',
                'Biaya Pokok = Direct costs (printing, permits, lab tests, field equipment)',
                'Biaya Jasa = Service fees (consultant hours × hourly rates)',
                'Overhead includes administrative costs and project management (10%)',
                'For detailed breakdown and official quotation, please register in our client portal',
            ],
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
        
        return [
            'kbli' => [
                'code' => $params['kbli_code'] ?? 'unknown',
                'description' => 'Data not available',
                'category' => 'Unknown',
                'complexity_level' => 'medium',
            ],
            'cost_summary' => [
                'grand_total' => 5000000, // Default 5 juta
                'cost_range' => [
                    'min' => 3000000,
                    'max' => 8000000,
                    'currency' => 'IDR',
                ],
                'formatted' => [
                    'grand_total' => 'Rp 5.000.000',
                    'range' => 'Rp 3.000.000 - Rp 8.000.000',
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
