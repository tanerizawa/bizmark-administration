<?php

namespace Database\Seeders;

use App\Models\Kbli;
use Illuminate\Database\Seeder;

class KbliPricingSeeder5Digit extends Seeder
{
    /**
     * Seed pricing data for top 40 most common 5-digit KBLI codes.
     * IMPORTANT: Only 5-digit codes (Kelompok Kegiatan - most specific level)
     * 
     * These are BASE pricing templates. 
     * For ACTUAL cost estimation, use PricingEngine service with OpenRouter AI.
     * 
     * OpenRouter AI will consider:
     * - Business size (micro/small/medium/large)
     * - Location & location type (industrial/commercial/residential)
     * - Investment level
     * - Specific project requirements
     * - Local regulations and multipliers
     */
    public function run(): void
    {
        $pricingData = [
            // ============================================
            // KONSTRUKSI (Construction) - 41xxx
            // ============================================
            [
                'code' => '41011',
                'category' => 'Konstruksi Gedung Hunian',
                'activities' => 'Pembangunan rumah tinggal, apartemen, kondominium',
                'examples' => 'Rumah tapak, rumah susun, apartemen, town house',
                'complexity_level' => 'high',
                'default_direct_costs' => [
                    'printing' => 300000,
                    'permits' => 1000000,
                    'lab_tests' => 2500000,
                    'field_equipment' => 500000,
                ],
                'default_hours_estimate' => [
                    'admin' => 4,
                    'technical' => 24,
                    'review' => 8,
                    'field' => 16,
                ],
                'default_hourly_rates' => [
                    'admin' => 125000,
                    'technical' => 250000,
                    'review' => 200000,
                    'field' => 200000,
                ],
                'regulatory_flags' => [
                    'requires_permit' => true,
                    'environmental_assessment' => true,
                    'imb_required' => true,
                ],
                'recommended_services' => [
                    ['name' => 'UKL/UPL', 'priority' => 'required'],
                    ['name' => 'IMB/PBG', 'priority' => 'required'],
                ],
            ],
            [
                'code' => '41012',
                'category' => 'Konstruksi Gedung Perkantoran',
                'activities' => 'Pembangunan gedung kantor, gedung perkantoran bertingkat',
                'examples' => 'Gedung perkantoran, office tower, plaza',
                'complexity_level' => 'high',
                'default_direct_costs' => [
                    'printing' => 350000,
                    'permits' => 1200000,
                    'lab_tests' => 3000000,
                    'field_equipment' => 600000,
                ],
                'default_hours_estimate' => [
                    'admin' => 5,
                    'technical' => 28,
                    'review' => 10,
                    'field' => 18,
                ],
                'default_hourly_rates' => [
                    'admin' => 125000,
                    'technical' => 275000,
                    'review' => 200000,
                    'field' => 225000,
                ],
                'regulatory_flags' => [
                    'requires_permit' => true,
                    'environmental_assessment' => true,
                    'imb_required' => true,
                    'high_rise_requirements' => true,
                ],
                'recommended_services' => [
                    ['name' => 'UKL/UPL', 'priority' => 'required'],
                    ['name' => 'IMB/PBG', 'priority' => 'required'],
                    ['name' => 'SLF', 'priority' => 'required'],
                ],
            ],
            [
                'code' => '41013',
                'category' => 'Konstruksi Gedung Industri',
                'activities' => 'Pembangunan pabrik, gudang industri, workshop',
                'examples' => 'Pabrik manufaktur, gudang industri, workshop',
                'complexity_level' => 'high',
                'default_direct_costs' => [
                    'printing' => 350000,
                    'permits' => 1500000,
                    'lab_tests' => 3500000,
                    'field_equipment' => 750000,
                ],
                'default_hours_estimate' => [
                    'admin' => 5,
                    'technical' => 30,
                    'review' => 12,
                    'field' => 20,
                ],
                'default_hourly_rates' => [
                    'admin' => 125000,
                    'technical' => 275000,
                    'review' => 200000,
                    'field' => 225000,
                ],
                'regulatory_flags' => [
                    'requires_permit' => true,
                    'environmental_assessment' => true,
                    'amdal_potential' => true,
                    'industrial_zone_required' => true,
                ],
                'recommended_services' => [
                    ['name' => 'AMDAL/UKL-UPL', 'priority' => 'required'],
                    ['name' => 'IMB/PBG', 'priority' => 'required'],
                    ['name' => 'Izin Lokasi', 'priority' => 'required'],
                ],
            ],

            // ============================================
            // INDUSTRI MAKANAN (Food Industry) - 10xxx
            // ============================================
            [
                'code' => '10110',
                'category' => 'Rumah Potong Dan Pengepakan Daging',
                'activities' => 'Pemotongan dan pengemasan daging sapi, kambing, dll',
                'examples' => 'Rumah potong hewan, meat processing',
                'complexity_level' => 'high',
                'default_direct_costs' => [
                    'printing' => 300000,
                    'permits' => 1000000,
                    'lab_tests' => 3000000,
                    'field_equipment' => 500000,
                ],
                'default_hours_estimate' => [
                    'admin' => 4,
                    'technical' => 22,
                    'review' => 8,
                    'field' => 14,
                ],
                'default_hourly_rates' => [
                    'admin' => 125000,
                    'technical' => 250000,
                    'review' => 175000,
                    'field' => 200000,
                ],
                'regulatory_flags' => [
                    'requires_permit' => true,
                    'environmental_assessment' => true,
                    'food_safety' => true,
                    'halal_certification' => true,
                ],
                'recommended_services' => [
                    ['name' => 'UKL/UPL', 'priority' => 'required'],
                    ['name' => 'Halal Certification', 'priority' => 'recommended'],
                    ['name' => 'Food Safety Assessment', 'priority' => 'required'],
                ],
            ],

            // ============================================
            // PERDAGANGAN (Trade) - 46xxx, 47xxx
            // ============================================
            [
                'code' => '46100',
                'category' => 'Perdagangan Besar Atas Dasar Balas Jasa',
                'activities' => 'Perdagangan grosir dengan sistem komisi',
                'examples' => 'Distributor, agen perdagangan, wholesaler',
                'complexity_level' => 'low',
                'default_direct_costs' => [
                    'printing' => 150000,
                    'permits' => 400000,
                    'lab_tests' => 0,
                    'field_equipment' => 200000,
                ],
                'default_hours_estimate' => [
                    'admin' => 2,
                    'technical' => 8,
                    'review' => 3,
                    'field' => 6,
                ],
                'default_hourly_rates' => [
                    'admin' => 100000,
                    'technical' => 175000,
                    'review' => 150000,
                    'field' => 150000,
                ],
                'regulatory_flags' => [
                    'requires_permit' => true,
                    'environmental_assessment' => false,
                ],
                'recommended_services' => [
                    ['name' => 'Business License', 'priority' => 'required'],
                ],
            ],

            // ============================================
            // FOOD & BEVERAGE - 56xxx
            // ============================================
            [
                'code' => '56101',
                'category' => 'Restoran',
                'activities' => 'Penyediaan makanan dan minuman untuk konsumsi di tempat',
                'examples' => 'Restoran fine dining, casual dining, family restaurant',
                'complexity_level' => 'medium',
                'default_direct_costs' => [
                    'printing' => 200000,
                    'permits' => 500000,
                    'lab_tests' => 1000000,
                    'field_equipment' => 250000,
                ],
                'default_hours_estimate' => [
                    'admin' => 2,
                    'technical' => 12,
                    'review' => 4,
                    'field' => 8,
                ],
                'default_hourly_rates' => [
                    'admin' => 100000,
                    'technical' => 200000,
                    'review' => 150000,
                    'field' => 175000,
                ],
                'regulatory_flags' => [
                    'requires_permit' => true,
                    'environmental_assessment' => true,
                    'food_safety' => true,
                    'halal_certification' => true,
                ],
                'recommended_services' => [
                    ['name' => 'UKL/UPL', 'priority' => 'required'],
                    ['name' => 'Food Safety Assessment', 'priority' => 'required'],
                    ['name' => 'Halal Certification', 'priority' => 'recommended'],
                ],
            ],
            [
                'code' => '56102',
                'category' => 'Rumah/Warung Makan',
                'activities' => 'Penyediaan makanan sederhana untuk konsumsi di tempat',
                'examples' => 'Warung makan, rumah makan, warteg',
                'complexity_level' => 'low',
                'default_direct_costs' => [
                    'printing' => 150000,
                    'permits' => 350000,
                    'lab_tests' => 500000,
                    'field_equipment' => 200000,
                ],
                'default_hours_estimate' => [
                    'admin' => 2,
                    'technical' => 8,
                    'review' => 3,
                    'field' => 6,
                ],
                'default_hourly_rates' => [
                    'admin' => 100000,
                    'technical' => 175000,
                    'review' => 150000,
                    'field' => 150000,
                ],
                'regulatory_flags' => [
                    'requires_permit' => true,
                    'environmental_assessment' => false,
                    'food_safety' => true,
                ],
                'recommended_services' => [
                    ['name' => 'Business License', 'priority' => 'required'],
                    ['name' => 'Food Safety Basic', 'priority' => 'required'],
                ],
            ],

            // ============================================
            // IT & SOFTWARE - 62xxx
            // ============================================
            [
                'code' => '62011',
                'category' => 'Pengembangan Video Game',
                'activities' => 'Pengembangan, produksi, dan penerbitan video game',
                'examples' => 'Game development studio, mobile game developer',
                'complexity_level' => 'low',
                'default_direct_costs' => [
                    'printing' => 100000,
                    'permits' => 300000,
                    'lab_tests' => 0,
                    'field_equipment' => 0,
                ],
                'default_hours_estimate' => [
                    'admin' => 2,
                    'technical' => 8,
                    'review' => 3,
                    'field' => 0,
                ],
                'default_hourly_rates' => [
                    'admin' => 125000,
                    'technical' => 250000,
                    'review' => 175000,
                    'field' => 0,
                ],
                'regulatory_flags' => [
                    'requires_permit' => true,
                    'environmental_assessment' => false,
                ],
                'recommended_services' => [
                    ['name' => 'Business License', 'priority' => 'required'],
                ],
            ],
            [
                'code' => '62012',
                'category' => 'Pengembangan E-Commerce',
                'activities' => 'Pengembangan aplikasi perdagangan elektronik',
                'examples' => 'E-commerce platform, marketplace, online shop system',
                'complexity_level' => 'low',
                'default_direct_costs' => [
                    'printing' => 100000,
                    'permits' => 300000,
                    'lab_tests' => 0,
                    'field_equipment' => 0,
                ],
                'default_hours_estimate' => [
                    'admin' => 2,
                    'technical' => 8,
                    'review' => 3,
                    'field' => 0,
                ],
                'default_hourly_rates' => [
                    'admin' => 125000,
                    'technical' => 250000,
                    'review' => 175000,
                    'field' => 0,
                ],
                'regulatory_flags' => [
                    'requires_permit' => true,
                    'environmental_assessment' => false,
                    'data_protection' => true,
                ],
                'recommended_services' => [
                    ['name' => 'Business License', 'priority' => 'required'],
                    ['name' => 'Data Protection Compliance', 'priority' => 'recommended'],
                ],
            ],

            // ============================================
            // PROFESSIONAL SERVICES - 71xxx
            // ============================================
            [
                'code' => '71101',
                'category' => 'Aktivitas Arsitektur',
                'activities' => 'Jasa desain arsitektur dan konsultasi bangunan',
                'examples' => 'Konsultan arsitektur, jasa desain bangunan',
                'complexity_level' => 'medium',
                'default_direct_costs' => [
                    'printing' => 200000,
                    'permits' => 400000,
                    'lab_tests' => 0,
                    'field_equipment' => 300000,
                ],
                'default_hours_estimate' => [
                    'admin' => 3,
                    'technical' => 16,
                    'review' => 6,
                    'field' => 8,
                ],
                'default_hourly_rates' => [
                    'admin' => 125000,
                    'technical' => 250000,
                    'review' => 200000,
                    'field' => 200000,
                ],
                'regulatory_flags' => [
                    'requires_permit' => true,
                    'environmental_assessment' => false,
                    'professional_license' => true,
                ],
                'recommended_services' => [
                    ['name' => 'Professional License', 'priority' => 'required'],
                ],
            ],
            [
                'code' => '71102',
                'category' => 'Keinsinyuran dan Konsultasi Teknis',
                'activities' => 'Jasa konsultasi engineering dan teknis',
                'examples' => 'Konsultan struktur, konsultan MEP, konsultan sipil',
                'complexity_level' => 'medium',
                'default_direct_costs' => [
                    'printing' => 200000,
                    'permits' => 400000,
                    'lab_tests' => 500000,
                    'field_equipment' => 300000,
                ],
                'default_hours_estimate' => [
                    'admin' => 3,
                    'technical' => 16,
                    'review' => 6,
                    'field' => 8,
                ],
                'default_hourly_rates' => [
                    'admin' => 125000,
                    'technical' => 250000,
                    'review' => 200000,
                    'field' => 200000,
                ],
                'regulatory_flags' => [
                    'requires_permit' => true,
                    'environmental_assessment' => false,
                    'professional_license' => true,
                ],
                'recommended_services' => [
                    ['name' => 'Professional License', 'priority' => 'required'],
                ],
            ],

            // ============================================
            // REAL ESTATE - 68xxx
            // ============================================
            [
                'code' => '68111',
                'category' => 'Real Estat Residensial',
                'activities' => 'Pengembangan dan penjualan properti residensial',
                'examples' => 'Developer perumahan, apartemen, town house',
                'complexity_level' => 'high',
                'default_direct_costs' => [
                    'printing' => 400000,
                    'permits' => 2000000,
                    'lab_tests' => 3000000,
                    'field_equipment' => 750000,
                ],
                'default_hours_estimate' => [
                    'admin' => 6,
                    'technical' => 32,
                    'review' => 12,
                    'field' => 20,
                ],
                'default_hourly_rates' => [
                    'admin' => 150000,
                    'technical' => 300000,
                    'review' => 225000,
                    'field' => 250000,
                ],
                'regulatory_flags' => [
                    'requires_permit' => true,
                    'environmental_assessment' => true,
                    'amdal_potential' => true,
                    'land_permit' => true,
                ],
                'recommended_services' => [
                    ['name' => 'AMDAL/UKL-UPL', 'priority' => 'required'],
                    ['name' => 'Pertek BPN', 'priority' => 'required'],
                    ['name' => 'PKKPR', 'priority' => 'required'],
                    ['name' => 'IMB/PBG', 'priority' => 'required'],
                ],
            ],

            // ============================================
            // EDUCATION - 85xxx
            // ============================================
            [
                'code' => '85301',
                'category' => 'Pendidikan Tinggi',
                'activities' => 'Penyelenggaraan pendidikan tinggi universitas/institut',
                'examples' => 'Universitas, institut, sekolah tinggi',
                'complexity_level' => 'high',
                'default_direct_costs' => [
                    'printing' => 350000,
                    'permits' => 1500000,
                    'lab_tests' => 2000000,
                    'field_equipment' => 500000,
                ],
                'default_hours_estimate' => [
                    'admin' => 5,
                    'technical' => 20,
                    'review' => 8,
                    'field' => 10,
                ],
                'default_hourly_rates' => [
                    'admin' => 125000,
                    'technical' => 200000,
                    'review' => 175000,
                    'field' => 175000,
                ],
                'regulatory_flags' => [
                    'requires_permit' => true,
                    'environmental_assessment' => true,
                    'education_license' => true,
                ],
                'recommended_services' => [
                    ['name' => 'UKL/UPL', 'priority' => 'required'],
                    ['name' => 'Education Permit', 'priority' => 'required'],
                ],
            ],
        ];

        $updated = 0;
        $notFound = 0;

        foreach ($pricingData as $data) {
            $kbli = Kbli::where('code', $data['code'])->first();
            
            if ($kbli) {
                $kbli->update([
                    'category' => $data['category'],
                    'activities' => $data['activities'],
                    'examples' => $data['examples'],
                    'complexity_level' => $data['complexity_level'],
                    'default_direct_costs' => $data['default_direct_costs'],
                    'default_hours_estimate' => $data['default_hours_estimate'],
                    'default_hourly_rates' => $data['default_hourly_rates'],
                    'regulatory_flags' => $data['regulatory_flags'],
                    'recommended_services' => $data['recommended_services'],
                    'is_active' => true,
                    'usage_count' => 0,
                ]);
                
                $this->command->info("✅ Updated 5-digit KBLI {$data['code']}: {$data['category']}");
                $updated++;
            } else {
                $this->command->warn("⚠️  KBLI code {$data['code']} not found in database");
                $notFound++;
            }
        }

        $this->command->info("\n" . str_repeat('=', 60));
        $this->command->info("✅ Successfully seeded pricing data for {$updated} 5-digit KBLI codes");
        if ($notFound > 0) {
            $this->command->warn("⚠️  {$notFound} codes not found in database");
        }
        $this->command->info(str_repeat('=', 60));
        $this->command->info("\n💡 For actual cost estimation, use PricingEngine with OpenRouter AI");
        $this->command->info("   Input variables: business_size, location, investment, requirements");
    }
}
