<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class ConsultantFeeCalculatorService
{
    /**
     * Base consultant fees by permit category (in Rupiah)
     */
    const BASE_FEES = [
        'foundational' => ['min' => 500000, 'max' => 1000000],
        'environmental' => ['min' => 5000000, 'max' => 15000000],
        'technical' => ['min' => 3000000, 'max' => 10000000],
        'operational' => ['min' => 2000000, 'max' => 5000000],
        'sectoral' => ['min' => 2000000, 'max' => 8000000],
    ];

    /**
     * Minimum total consultant fees by project complexity
     */
    const MINIMUM_FEES = [
        'simple' => 2000000,    // 1-3 permits
        'medium' => 5000000,    // 4-8 permits
        'complex' => 10000000,  // 9+ permits
    ];

    /**
     * Major cities with premium pricing
     */
    const PREMIUM_CITIES = [
        'Jakarta', 'DKI Jakarta', 'Jakarta Pusat', 'Jakarta Selatan', 'Jakarta Timur',
        'Jakarta Barat', 'Jakarta Utara', 'Surabaya', 'Bandung', 'Medan', 'Semarang',
        'Tangerang', 'Bekasi', 'Depok', 'Tangerang Selatan', 'South Tangerang'
    ];

    /**
     * Provincial capitals (excluding premium cities)
     */
    const PROVINCIAL_CAPITALS = [
        'Yogyakarta', 'Palembang', 'Makassar', 'Denpasar', 'Padang', 'Bandar Lampung',
        'Malang', 'Balikpapan', 'Pontianak', 'Samarinda', 'Manado', 'Batam',
        'Pekanbaru', 'Banjarmasin', 'Jambi', 'Banda Aceh', 'Mataram', 'Kupang',
        'Kendari', 'Palu', 'Ambon', 'Jayapura', 'Gorontalo', 'Mamuju', 'Sofifi'
    ];

    /**
     * Calculate total consultant fees for a list of permits
     */
    public function calculateTotalConsultantFee(array $permits, array $context = []): array
    {
        $landArea = $context['land_area'] ?? 0;
        $buildingArea = $context['building_area'] ?? 0;
        $investment = $context['investment_value'] ?? 0;
        $location = $context['city'] ?? $context['location_type'] ?? 'unknown';
        $employees = $context['employees'] ?? 0;
        $environmentalImpact = $context['environmental_impact'] ?? 'low';
        $urgency = $context['urgency_level'] ?? 'standard';

        $totalMin = 0;
        $totalMax = 0;
        $permitCount = count($permits);

        // Calculate fees for each permit
        foreach ($permits as &$permit) {
            $category = $permit['category'] ?? 'sectoral';
            $baseFee = $this->getBaseFee($category);

            // Apply complexity multiplier based on project scale
            $complexityMultiplier = $this->getComplexityMultiplier($landArea, $buildingArea, $investment);
            
            // Apply location multiplier
            $locationMultiplier = $this->getLocationMultiplier($location);

            // Apply environmental impact multiplier
            $environmentalMultiplier = $this->getEnvironmentalMultiplier($environmentalImpact, $category);

            // Apply urgency multiplier
            $urgencyMultiplier = $urgency === 'rush' ? 1.5 : 1.0;

            // Calculate final fee for this permit
            $permitFeeMin = $baseFee['min'] * $complexityMultiplier * $locationMultiplier * $environmentalMultiplier * $urgencyMultiplier;
            $permitFeeMax = $baseFee['max'] * $complexityMultiplier * $locationMultiplier * $environmentalMultiplier * $urgencyMultiplier;

            // Add consultant fee to permit data
            $permit['consultant_fee_range'] = [
                'min' => (int) round($permitFeeMin),
                'max' => (int) round($permitFeeMax),
            ];

            $totalMin += $permitFeeMin;
            $totalMax += $permitFeeMax;
        }

        // Ensure minimum fee based on project complexity
        $minimumFee = $this->getMinimumFee($permitCount);
        
        if ($totalMin < $minimumFee) {
            $totalMin = $minimumFee;
        }

        // Apply document preparation costs (10-15% of consultant fees)
        $docPrepMin = $totalMin * 0.10;
        $docPrepMax = $totalMax * 0.15;

        return [
            'permits' => $permits,
            'consultant_fees' => [
                'min' => (int) round($totalMin),
                'max' => (int) round($totalMax),
            ],
            'document_preparation' => [
                'min' => (int) round($docPrepMin),
                'max' => (int) round($docPrepMax),
            ],
            'total_consultant_package' => [
                'min' => (int) round($totalMin + $docPrepMin),
                'max' => (int) round($totalMax + $docPrepMax),
            ],
            'complexity_factor' => $complexityMultiplier,
            'location_factor' => $locationMultiplier,
            'environmental_factor' => $environmentalMultiplier,
            'urgency_factor' => $urgencyMultiplier,
            'minimum_fee_applied' => $totalMin < $minimumFee,
        ];
    }

    /**
     * Get base fee for a permit category
     */
    protected function getBaseFee(string $category): array
    {
        return self::BASE_FEES[$category] ?? self::BASE_FEES['sectoral'];
    }

    /**
     * Calculate complexity multiplier based on project scale
     */
    protected function getComplexityMultiplier(float $landArea, float $buildingArea, float $investment): float
    {
        $area = max($landArea, $buildingArea);
        $investmentInBillions = $investment / 1000000000; // Convert to billions

        // Base multiplier on area
        if ($area >= 5000) {
            $multiplier = 3.0; // Large scale
        } elseif ($area >= 500) {
            $multiplier = 2.0; // Medium scale
        } elseif ($area >= 50) {
            $multiplier = 1.5; // Small scale
        } else {
            $multiplier = 1.0; // Micro scale
        }

        // Adjust based on investment value
        if ($investmentInBillions >= 100) {
            $multiplier *= 1.5; // Very high investment
        } elseif ($investmentInBillions >= 10) {
            $multiplier *= 1.3; // High investment
        } elseif ($investmentInBillions >= 1) {
            $multiplier *= 1.1; // Medium investment
        }

        return min($multiplier, 5.0); // Cap at 5x
    }

    /**
     * Calculate location multiplier
     */
    protected function getLocationMultiplier(string $location): float
    {
        // Check if premium city
        foreach (self::PREMIUM_CITIES as $city) {
            if (stripos($location, $city) !== false) {
                return 1.5;
            }
        }

        // Check if provincial capital
        foreach (self::PROVINCIAL_CAPITALS as $city) {
            if (stripos($location, $city) !== false) {
                return 1.2;
            }
        }

        // Check location type
        if (stripos($location, 'perkotaan') !== false || stripos($location, 'urban') !== false) {
            return 1.0;
        }

        if (stripos($location, 'pedesaan') !== false || stripos($location, 'rural') !== false) {
            return 0.8;
        }

        return 1.0; // Default
    }

    /**
     * Calculate environmental impact multiplier
     */
    protected function getEnvironmentalMultiplier(string $impact, string $category): float
    {
        // Environmental permits get higher multiplier for high impact
        if ($category === 'environmental') {
            return match($impact) {
                'high' => 2.0,
                'medium' => 1.5,
                'low' => 1.2,
                default => 1.0,
            };
        }

        // Other permits get smaller multiplier
        return match($impact) {
            'high' => 1.3,
            'medium' => 1.1,
            'low' => 1.0,
            default => 1.0,
        };
    }

    /**
     * Get minimum fee based on number of permits
     */
    protected function getMinimumFee(int $permitCount): int
    {
        if ($permitCount >= 9) {
            return self::MINIMUM_FEES['complex'];
        } elseif ($permitCount >= 4) {
            return self::MINIMUM_FEES['medium'];
        } else {
            return self::MINIMUM_FEES['simple'];
        }
    }

    /**
     * Calculate government fees from AI recommendations
     */
    public function extractGovernmentFees(array $permits): array
    {
        $totalMin = 0;
        $totalMax = 0;

        foreach ($permits as $permit) {
            if (isset($permit['estimated_cost_range'])) {
                $totalMin += $permit['estimated_cost_range']['min'] ?? 0;
                $totalMax += $permit['estimated_cost_range']['max'] ?? 0;
            }
        }

        return [
            'min' => $totalMin,
            'max' => $totalMax,
        ];
    }

    /**
     * Generate detailed cost breakdown with explanations
     */
    public function generateCostBreakdown(array $permits, array $context = []): array
    {
        $consultantCalc = $this->calculateTotalConsultantFee($permits, $context);
        $governmentFees = $this->extractGovernmentFees($permits);

        $totalMin = $governmentFees['min'] + $consultantCalc['total_consultant_package']['min'];
        $totalMax = $governmentFees['max'] + $consultantCalc['total_consultant_package']['max'];

        return [
            'government_fees' => $governmentFees,
            'consultant_fees' => $consultantCalc['consultant_fees'],
            'document_preparation' => $consultantCalc['document_preparation'],
            'total_consultant_package' => $consultantCalc['total_consultant_package'],
            'grand_total' => [
                'min' => $totalMin,
                'max' => $totalMax,
            ],
            'factors' => [
                'complexity' => $consultantCalc['complexity_factor'],
                'location' => $consultantCalc['location_factor'],
                'environmental' => $consultantCalc['environmental_factor'],
                'urgency' => $consultantCalc['urgency_factor'],
            ],
            'permit_count' => count($permits),
            'minimum_fee_applied' => $consultantCalc['minimum_fee_applied'],
            'permits_with_fees' => $consultantCalc['permits'],
        ];
    }

    /**
     * Format cost breakdown for display
     */
    public function formatCostBreakdown(array $breakdown): array
    {
        return [
            'sections' => [
                [
                    'title' => 'Biaya Pemerintah',
                    'subtitle' => 'Biaya resmi ke instansi pemerintah',
                    'amount' => $breakdown['government_fees'],
                    'icon' => 'fa-landmark',
                    'color' => 'blue',
                ],
                [
                    'title' => 'Jasa Konsultan BizMark',
                    'subtitle' => 'Pengurusan dan konsultasi perizinan',
                    'amount' => $breakdown['consultant_fees'],
                    'icon' => 'fa-user-tie',
                    'color' => 'green',
                ],
                [
                    'title' => 'Persiapan Dokumen',
                    'subtitle' => 'Penyusunan dan legalisasi dokumen',
                    'amount' => $breakdown['document_preparation'],
                    'icon' => 'fa-file-alt',
                    'color' => 'yellow',
                ],
            ],
            'total' => $breakdown['grand_total'],
            'factors' => $breakdown['factors'],
            'notes' => [
                'Estimasi biaya dapat berubah sesuai kondisi lapangan',
                'Jasa konsultan BizMark sudah termasuk pendampingan hingga izin terbit',
                'Biaya pemerintah disesuaikan dengan tarif resmi terbaru',
            ],
        ];
    }
}
