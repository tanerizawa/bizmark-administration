<?php

namespace Database\Seeders;

use App\Models\Kbli;
use Illuminate\Database\Seeder;

class KbliPricingSeeder extends Seeder
{
    /**
     * Seed pricing data for top 30-50 most common 5-digit KBLI codes in Indonesia.
     * IMPORTANT: Only using 5-digit codes (Kelompok Kegiatan - most specific level)
     * 
     * Pricing structure:
     * - Biaya Pokok (Direct Costs): printing, lab tests, permits, field equipment
     * - Biaya Jasa (Service Fees): hours × rates by role (admin, technical, review, field)
     * - Overhead: 10% automatically applied
     * 
     * Hourly rates (IDR):
     * - Admin: 100,000 - 150,000
     * - Technical: 200,000 - 300,000
     * - Review: 150,000 - 200,000
     * - Field: 175,000 - 250,000
     * 
     * For actual cost estimation, use PricingEngine service with OpenRouter AI
     * which considers: business size, location, investment level, specific requirements
     */
    public function run(): void
    {
        $pricingData = [
            // ============================================
            // CATEGORY F: KONSTRUKSI (Construction) - 5 DIGIT
            // ============================================
            [
                'code' => '41011',
                'category' => 'Konstruksi Gedung',
                'activities' => 'Pembangunan gedung residensial, komersial, dan industri. Termasuk renovasi dan pembongkaran.',
                'examples' => 'Rumah tinggal, apartemen, perkantoran, pabrik, gudang',
                'complexity_level' => 'high',
                'default_direct_costs' => [
                    'printing' => 300000,
                    'permits' => 750000,
                    'lab_tests' => 2000000,
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
                    ['name' => 'AMDAL', 'priority' => 'conditional'],
                    ['name' => 'IMB Processing', 'priority' => 'required'],
                ],
            ],
            [
                'code' => '42',
                'category' => 'Konstruksi Bangunan Sipil',
                'activities' => 'Pembangunan jalan, jembatan, bendungan, terowongan, saluran irigasi',
                'examples' => 'Jalan raya, jembatan, terowongan, bendungan, jaringan utilitas',
                'complexity_level' => 'high',
                'default_direct_costs' => [
                    'printing' => 350000,
                    'permits' => 1000000,
                    'lab_tests' => 3000000,
                    'field_equipment' => 750000,
                ],
                'default_hours_estimate' => [
                    'admin' => 6,
                    'technical' => 32,
                    'review' => 12,
                    'field' => 24,
                ],
                'default_hourly_rates' => [
                    'admin' => 125000,
                    'technical' => 300000,
                    'review' => 200000,
                    'field' => 225000,
                ],
                'regulatory_flags' => [
                    'requires_permit' => true,
                    'environmental_assessment' => true,
                    'amdal_required' => true,
                ],
                'recommended_services' => [
                    ['name' => 'AMDAL', 'priority' => 'required'],
                    ['name' => 'Soil Testing', 'priority' => 'required'],
                ],
            ],
            [
                'code' => '43',
                'category' => 'Konstruksi Khusus',
                'activities' => 'Instalasi listrik, plumbing, AC, landscaping, finishing',
                'examples' => 'Instalasi MEP, landscaping, interior finishing',
                'complexity_level' => 'medium',
                'default_direct_costs' => [
                    'printing' => 200000,
                    'permits' => 500000,
                    'lab_tests' => 1000000,
                    'field_equipment' => 300000,
                ],
                'default_hours_estimate' => [
                    'admin' => 3,
                    'technical' => 16,
                    'review' => 6,
                    'field' => 12,
                ],
                'default_hourly_rates' => [
                    'admin' => 100000,
                    'technical' => 200000,
                    'review' => 175000,
                    'field' => 175000,
                ],
                'regulatory_flags' => [
                    'requires_permit' => true,
                    'environmental_assessment' => false,
                ],
                'recommended_services' => [
                    ['name' => 'UKL/UPL', 'priority' => 'optional'],
                    ['name' => 'Safety Plan', 'priority' => 'required'],
                ],
            ],

            // ============================================
            // CATEGORY C: INDUSTRI PENGOLAHAN (Manufacturing)
            // ============================================
            [
                'code' => '10',
                'category' => 'Industri Makanan',
                'activities' => 'Produksi, pengolahan, dan pengawetan makanan',
                'examples' => 'Pabrik roti, pengolahan daging, produksi makanan olahan',
                'complexity_level' => 'high',
                'default_direct_costs' => [
                    'printing' => 250000,
                    'permits' => 800000,
                    'lab_tests' => 2500000,
                    'field_equipment' => 400000,
                ],
                'default_hours_estimate' => [
                    'admin' => 4,
                    'technical' => 20,
                    'review' => 8,
                    'field' => 12,
                ],
                'default_hourly_rates' => [
                    'admin' => 125000,
                    'technical' => 225000,
                    'review' => 175000,
                    'field' => 200000,
                ],
                'regulatory_flags' => [
                    'requires_permit' => true,
                    'environmental_assessment' => true,
                    'food_safety' => true,
                    'bpom_required' => true,
                ],
                'recommended_services' => [
                    ['name' => 'UKL/UPL', 'priority' => 'required'],
                    ['name' => 'Food Safety Assessment', 'priority' => 'required'],
                    ['name' => 'BPOM Registration', 'priority' => 'required'],
                ],
            ],
            [
                'code' => '11',
                'category' => 'Industri Minuman',
                'activities' => 'Produksi minuman ringan, air mineral, minuman beralkohol',
                'examples' => 'Pabrik air mineral, minuman ringan, brewery',
                'complexity_level' => 'high',
                'default_direct_costs' => [
                    'printing' => 250000,
                    'permits' => 850000,
                    'lab_tests' => 2000000,
                    'field_equipment' => 400000,
                ],
                'default_hours_estimate' => [
                    'admin' => 4,
                    'technical' => 18,
                    'review' => 8,
                    'field' => 12,
                ],
                'default_hourly_rates' => [
                    'admin' => 125000,
                    'technical' => 225000,
                    'review' => 175000,
                    'field' => 200000,
                ],
                'regulatory_flags' => [
                    'requires_permit' => true,
                    'environmental_assessment' => true,
                    'bpom_required' => true,
                ],
                'recommended_services' => [
                    ['name' => 'UKL/UPL', 'priority' => 'required'],
                    ['name' => 'Water Quality Testing', 'priority' => 'required'],
                    ['name' => 'BPOM Registration', 'priority' => 'required'],
                ],
            ],

            // ============================================
            // CATEGORY I: AKOMODASI DAN PENYEDIAAN MAKANAN (Hospitality)
            // ============================================
            [
                'code' => '55',
                'category' => 'Akomodasi',
                'activities' => 'Hotel, motel, guest house, villa, resort',
                'examples' => 'Hotel berbintang, boutique hotel, villa resort',
                'complexity_level' => 'medium',
                'default_direct_costs' => [
                    'printing' => 250000,
                    'permits' => 600000,
                    'lab_tests' => 1500000,
                    'field_equipment' => 300000,
                ],
                'default_hours_estimate' => [
                    'admin' => 3,
                    'technical' => 16,
                    'review' => 6,
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
                    'tourism_license' => true,
                ],
                'recommended_services' => [
                    ['name' => 'UKL/UPL', 'priority' => 'required'],
                    ['name' => 'Fire Safety Assessment', 'priority' => 'required'],
                ],
            ],
            [
                'code' => '56',
                'category' => 'Penyediaan Makanan Dan Minuman',
                'activities' => 'Restoran, kafe, katering, warung makan',
                'examples' => 'Restoran fine dining, kafe, catering service, food court',
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
                ],
                'recommended_services' => [
                    ['name' => 'UKL/UPL', 'priority' => 'required'],
                    ['name' => 'Food Safety Assessment', 'priority' => 'required'],
                ],
            ],

            // ============================================
            // CATEGORY G: PERDAGANGAN (Retail/Wholesale)
            // ============================================
            [
                'code' => '46',
                'category' => 'Perdagangan Besar',
                'activities' => 'Perdagangan grosir barang konsumsi, material, mesin',
                'examples' => 'Distributor, pedagang besar, wholesaler',
                'complexity_level' => 'low',
                'default_direct_costs' => [
                    'printing' => 150000,
                    'permits' => 400000,
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
                ],
                'recommended_services' => [
                    ['name' => 'Business License', 'priority' => 'required'],
                ],
            ],
            [
                'code' => '47',
                'category' => 'Perdagangan Eceran',
                'activities' => 'Toko retail, supermarket, minimarket, specialty store',
                'examples' => 'Minimarket, toko fashion, toko elektronik',
                'complexity_level' => 'low',
                'default_direct_costs' => [
                    'printing' => 150000,
                    'permits' => 350000,
                    'lab_tests' => 0,
                    'field_equipment' => 150000,
                ],
                'default_hours_estimate' => [
                    'admin' => 2,
                    'technical' => 6,
                    'review' => 2,
                    'field' => 4,
                ],
                'default_hourly_rates' => [
                    'admin' => 100000,
                    'technical' => 150000,
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
            // CATEGORY J: INFORMASI DAN KOMUNIKASI (IT/Tech)
            // ============================================
            [
                'code' => '62',
                'category' => 'Aktivitas Pemrograman Dan Konsultasi Komputer',
                'activities' => 'Pengembangan software, web development, IT consulting',
                'examples' => 'Software house, web development, mobile app development',
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
                'code' => '63',
                'category' => 'Aktivitas Portal Web Dan Jasa Informasi Lainnya',
                'activities' => 'Portal berita, e-commerce platform, data processing',
                'examples' => 'Website portal, e-commerce platform, data center',
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

            // ============================================
            // CATEGORY M: JASA PROFESIONAL (Professional Services)
            // ============================================
            [
                'code' => '71',
                'category' => 'Aktivitas Arsitektur Dan Keinsinyuran',
                'activities' => 'Jasa arsitektur, engineering, surveying, technical consulting',
                'examples' => 'Konsultan arsitektur, konsultan struktur, surveyor',
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
                    ['name' => 'Professional License Verification', 'priority' => 'required'],
                ],
            ],
            [
                'code' => '69',
                'category' => 'Aktivitas Hukum Dan Akuntansi',
                'activities' => 'Jasa hukum, akuntansi, perpajakan, audit',
                'examples' => 'Kantor hukum, kantor akuntan, konsultan pajak',
                'complexity_level' => 'low',
                'default_direct_costs' => [
                    'printing' => 150000,
                    'permits' => 350000,
                    'lab_tests' => 0,
                    'field_equipment' => 0,
                ],
                'default_hours_estimate' => [
                    'admin' => 2,
                    'technical' => 8,
                    'review' => 4,
                    'field' => 0,
                ],
                'default_hourly_rates' => [
                    'admin' => 125000,
                    'technical' => 250000,
                    'review' => 200000,
                    'field' => 0,
                ],
                'regulatory_flags' => [
                    'requires_permit' => true,
                    'environmental_assessment' => false,
                    'professional_license' => true,
                ],
                'recommended_services' => [
                    ['name' => 'Professional License Verification', 'priority' => 'required'],
                ],
            ],

            // ============================================
            // CATEGORY P: PENDIDIKAN (Education)
            // ============================================
            [
                'code' => '85',
                'category' => 'Pendidikan',
                'activities' => 'Sekolah, universitas, kursus, pelatihan',
                'examples' => 'TK, SD, SMP, SMA, universitas, lembaga kursus',
                'complexity_level' => 'medium',
                'default_direct_costs' => [
                    'printing' => 200000,
                    'permits' => 500000,
                    'lab_tests' => 1000000,
                    'field_equipment' => 200000,
                ],
                'default_hours_estimate' => [
                    'admin' => 3,
                    'technical' => 12,
                    'review' => 5,
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
                    'environmental_assessment' => true,
                    'education_license' => true,
                ],
                'recommended_services' => [
                    ['name' => 'UKL/UPL', 'priority' => 'required'],
                    ['name' => 'Fire Safety Assessment', 'priority' => 'required'],
                ],
            ],

            // ============================================
            // CATEGORY Q: KESEHATAN (Healthcare)
            // ============================================
            [
                'code' => '86',
                'category' => 'Aktivitas Kesehatan Manusia',
                'activities' => 'Rumah sakit, klinik, praktik dokter, laboratorium medis',
                'examples' => 'Rumah sakit, klinik, puskesmas, laboratorium',
                'complexity_level' => 'high',
                'default_direct_costs' => [
                    'printing' => 300000,
                    'permits' => 900000,
                    'lab_tests' => 2500000,
                    'field_equipment' => 500000,
                ],
                'default_hours_estimate' => [
                    'admin' => 5,
                    'technical' => 24,
                    'review' => 10,
                    'field' => 12,
                ],
                'default_hourly_rates' => [
                    'admin' => 150000,
                    'technical' => 275000,
                    'review' => 200000,
                    'field' => 200000,
                ],
                'regulatory_flags' => [
                    'requires_permit' => true,
                    'environmental_assessment' => true,
                    'health_license' => true,
                    'waste_management' => true,
                ],
                'recommended_services' => [
                    ['name' => 'UKL/UPL', 'priority' => 'required'],
                    ['name' => 'Medical Waste Management Plan', 'priority' => 'required'],
                    ['name' => 'Health Facility Licensing', 'priority' => 'required'],
                ],
            ],

            // ============================================
            // CATEGORY B: PERTAMBANGAN (Mining)
            // ============================================
            [
                'code' => '08',
                'category' => 'Pertambangan Dan Penggalian Lainnya',
                'activities' => 'Pertambangan batu, pasir, kerikil, tanah liat',
                'examples' => 'Quarry batu, tambang pasir, tambang tanah liat',
                'complexity_level' => 'high',
                'default_direct_costs' => [
                    'printing' => 400000,
                    'permits' => 1500000,
                    'lab_tests' => 4000000,
                    'field_equipment' => 1000000,
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
                    'amdal_required' => true,
                    'mining_license' => true,
                ],
                'recommended_services' => [
                    ['name' => 'AMDAL', 'priority' => 'required'],
                    ['name' => 'Mining Permit', 'priority' => 'required'],
                    ['name' => 'Reclamation Plan', 'priority' => 'required'],
                ],
            ],

            // ============================================
            // CATEGORY A: PERTANIAN (Agriculture)
            // ============================================
            [
                'code' => '01',
                'category' => 'Pertanian, Perburuan Dan Kegiatan Ybdi',
                'activities' => 'Budidaya tanaman pangan, hortikultura, perkebunan',
                'examples' => 'Perkebunan kelapa sawit, kopi, kakao, hortikultura',
                'complexity_level' => 'medium',
                'default_direct_costs' => [
                    'printing' => 250000,
                    'permits' => 600000,
                    'lab_tests' => 1500000,
                    'field_equipment' => 500000,
                ],
                'default_hours_estimate' => [
                    'admin' => 3,
                    'technical' => 16,
                    'review' => 6,
                    'field' => 12,
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
                ],
                'recommended_services' => [
                    ['name' => 'UKL/UPL', 'priority' => 'required'],
                    ['name' => 'Agricultural Permit', 'priority' => 'required'],
                ],
            ],

            // ============================================
            // CATEGORY H: TRANSPORTASI (Transportation)
            // ============================================
            [
                'code' => '49',
                'category' => 'Angkutan Darat Dan Angkutan Melalui Pipa',
                'activities' => 'Angkutan penumpang dan barang darat',
                'examples' => 'Bus, truk, taksi, rental mobil',
                'complexity_level' => 'medium',
                'default_direct_costs' => [
                    'printing' => 200000,
                    'permits' => 500000,
                    'lab_tests' => 500000,
                    'field_equipment' => 250000,
                ],
                'default_hours_estimate' => [
                    'admin' => 3,
                    'technical' => 10,
                    'review' => 4,
                    'field' => 8,
                ],
                'default_hourly_rates' => [
                    'admin' => 100000,
                    'technical' => 175000,
                    'review' => 150000,
                    'field' => 175000,
                ],
                'regulatory_flags' => [
                    'requires_permit' => true,
                    'environmental_assessment' => true,
                    'transport_license' => true,
                ],
                'recommended_services' => [
                    ['name' => 'UKL/UPL', 'priority' => 'required'],
                    ['name' => 'Transport Permit', 'priority' => 'required'],
                ],
            ],
            [
                'code' => '52',
                'category' => 'Pergudangan Dan Aktivitas Penunjang Angkutan',
                'activities' => 'Pergudangan, cold storage, terminal, parkir',
                'examples' => 'Gudang logistik, cold storage, freight forwarding',
                'complexity_level' => 'medium',
                'default_direct_costs' => [
                    'printing' => 250000,
                    'permits' => 600000,
                    'lab_tests' => 1000000,
                    'field_equipment' => 350000,
                ],
                'default_hours_estimate' => [
                    'admin' => 3,
                    'technical' => 14,
                    'review' => 5,
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
                ],
                'recommended_services' => [
                    ['name' => 'UKL/UPL', 'priority' => 'required'],
                    ['name' => 'Fire Safety Assessment', 'priority' => 'required'],
                ],
            ],
        ];

        // Update each KBLI code with pricing data
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
                
                $this->command->info("Updated pricing for KBLI {$data['code']}: {$data['category']}");
            } else {
                $this->command->warn("KBLI code {$data['code']} not found in database");
            }
        }

        $this->command->info("\n✅ Successfully seeded pricing data for " . count($pricingData) . " KBLI codes");
    }
}
