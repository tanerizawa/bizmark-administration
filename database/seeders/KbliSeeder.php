<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Kbli;

class KbliSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * Comprehensive KBLI data with realistic pricing for Indonesian businesses
     */
    public function run(): void
    {
        $kblis = [
            // CONSTRUCTION & REAL ESTATE
            [
                'code' => '41001',
                'title' => 'Konstruksi Gedung Tempat Tinggal',
                'category' => 'Konstruksi',
                'description' => 'Pembangunan rumah tinggal, apartemen, dan hunian residensial',
                'activities' => 'Pembangunan rumah, renovasi, gedung apartemen',
                'complexity_level' => 'medium',
                'default_direct_costs' => [
                    'site_survey' => 500000,
                    'soil_test' => 2000000,
                    'printing_plotting' => 300000,
                    'permit_admin' => 500000,
                    'environmental_docs' => 1000000,
                ],
                'default_hours_estimate' => [
                    'admin' => 4,
                    'technical' => 24,
                    'review' => 6,
                    'site_visit' => 8,
                ],
                'default_hourly_rates' => [
                    'admin' => 100000,
                    'technical' => 250000,
                    'review' => 200000,
                    'site_visit' => 150000,
                ],
                'regulatory_flags' => ['imb_required', 'ukl_upl_required', 'site_plan_required'],
                'recommended_services' => [
                    ['name' => 'UKL/UPL', 'priority' => 'high'],
                    ['name' => 'IMB Support', 'priority' => 'high'],
                    ['name' => 'Site Plan', 'priority' => 'medium'],
                ],
                'examples' => 'Perumahan 10 unit, Apartemen 5 lantai, Renovasi rumah tinggal',
            ],
            [
                'code' => '41002',
                'title' => 'Konstruksi Gedung Bukan Tempat Tinggal',
                'category' => 'Konstruksi',
                'description' => 'Pembangunan perkantoran, pabrik, gudang, dan bangunan komersial',
                'activities' => 'Gedung kantor, pabrik, gudang, pusat perbelanjaan',
                'complexity_level' => 'high',
                'default_direct_costs' => [
                    'site_survey' => 1000000,
                    'soil_test' => 3000000,
                    'environmental_assessment' => 5000000,
                    'printing_plotting' => 500000,
                    'permit_admin' => 1000000,
                    'fire_safety_docs' => 2000000,
                ],
                'default_hours_estimate' => [
                    'admin' => 8,
                    'technical' => 40,
                    'review' => 12,
                    'site_visit' => 16,
                ],
                'default_hourly_rates' => [
                    'admin' => 100000,
                    'technical' => 300000,
                    'review' => 250000,
                    'site_visit' => 150000,
                ],
                'regulatory_flags' => ['imb_required', 'amdal_possible', 'fire_safety_required', 'structural_approval'],
                'recommended_services' => [
                    ['name' => 'AMDAL/UKL-UPL', 'priority' => 'high'],
                    ['name' => 'IMB Support', 'priority' => 'high'],
                    ['name' => 'Fire Safety Certificate', 'priority' => 'high'],
                    ['name' => 'Structural Analysis', 'priority' => 'medium'],
                ],
                'examples' => 'Gedung perkantoran 10 lantai, Pabrik manufaktur, Mall 3 lantai',
            ],
            
            // FOOD & BEVERAGE
            [
                'code' => '10720',
                'title' => 'Industri Roti dan Kue',
                'category' => 'Manufaktur Makanan',
                'description' => 'Produksi roti, kue, pastry dan produk bakery',
                'activities' => 'Produksi roti, kue kering, cake, pastry',
                'complexity_level' => 'medium',
                'default_direct_costs' => [
                    'lab_test_product' => 2500000,
                    'halal_cert_support' => 3000000,
                    'bpom_docs' => 1500000,
                    'printing' => 200000,
                    'facility_inspection' => 1000000,
                ],
                'default_hours_estimate' => [
                    'admin' => 6,
                    'technical' => 20,
                    'review' => 8,
                    'site_visit' => 4,
                ],
                'default_hourly_rates' => [
                    'admin' => 100000,
                    'technical' => 200000,
                    'review' => 180000,
                    'site_visit' => 150000,
                ],
                'regulatory_flags' => ['pirt_required', 'halal_cert', 'bpom_registration', 'food_safety'],
                'recommended_services' => [
                    ['name' => 'PIRT/MD BPOM', 'priority' => 'high'],
                    ['name' => 'Sertifikasi Halal', 'priority' => 'high'],
                    ['name' => 'Lab Testing', 'priority' => 'medium'],
                    ['name' => 'GMP/HACCP Support', 'priority' => 'low'],
                ],
                'examples' => 'Toko roti kecil, Pabrik kue kering, Bakery & Cafe',
            ],
            [
                'code' => '56101',
                'title' => 'Restoran',
                'category' => 'Makanan & Minuman',
                'description' => 'Jasa penyediaan makanan di restoran, rumah makan, warung',
                'activities' => 'Restoran, cafe, rumah makan, food court',
                'complexity_level' => 'low',
                'default_direct_costs' => [
                    'health_cert' => 500000,
                    'fire_safety_check' => 1000000,
                    'waste_management_plan' => 800000,
                    'printing' => 150000,
                ],
                'default_hours_estimate' => [
                    'admin' => 3,
                    'technical' => 12,
                    'review' => 4,
                    'site_visit' => 4,
                ],
                'default_hourly_rates' => [
                    'admin' => 100000,
                    'technical' => 180000,
                    'review' => 150000,
                    'site_visit' => 120000,
                ],
                'regulatory_flags' => ['health_permit', 'ukl_upl_required', 'fire_safety'],
                'recommended_services' => [
                    ['name' => 'Izin Usaha & Kesehatan', 'priority' => 'high'],
                    ['name' => 'UKL/UPL', 'priority' => 'medium'],
                    ['name' => 'Fire Safety', 'priority' => 'medium'],
                ],
                'examples' => 'Restoran 50 kursi, Cafe, Warung makan, Food court tenant',
            ],
            
            // HOSPITALITY
            [
                'code' => '55101',
                'title' => 'Hotel Bintang',
                'category' => 'Perhotelan',
                'description' => 'Jasa penginapan hotel berbintang dengan fasilitas lengkap',
                'activities' => 'Hotel bintang 1-5, resort, boutique hotel',
                'complexity_level' => 'high',
                'default_direct_costs' => [
                    'environmental_assessment' => 8000000,
                    'fire_safety_system' => 5000000,
                    'waste_water_analysis' => 3000000,
                    'air_quality_test' => 2000000,
                    'permit_admin' => 2000000,
                    'printing_plotting' => 500000,
                ],
                'default_hours_estimate' => [
                    'admin' => 12,
                    'technical' => 48,
                    'review' => 16,
                    'site_visit' => 16,
                ],
                'default_hourly_rates' => [
                    'admin' => 120000,
                    'technical' => 300000,
                    'review' => 250000,
                    'site_visit' => 180000,
                ],
                'regulatory_flags' => ['amdal_required', 'tourism_license', 'fire_safety_required', 'waste_management', 'water_treatment'],
                'recommended_services' => [
                    ['name' => 'AMDAL', 'priority' => 'high'],
                    ['name' => 'Tourism License Support', 'priority' => 'high'],
                    ['name' => 'Fire Safety System', 'priority' => 'high'],
                    ['name' => 'Waste Water Treatment', 'priority' => 'high'],
                    ['name' => 'Energy Efficiency', 'priority' => 'low'],
                ],
                'examples' => 'Hotel bintang 3-5, Resort 100 kamar, Boutique hotel 30 kamar',
            ],
            
            // MANUFACTURING
            [
                'code' => '13111',
                'title' => 'Industri Tekstil',
                'category' => 'Manufaktur',
                'description' => 'Pemintalan, pertenunan, dan finishing tekstil',
                'activities' => 'Produksi benang, kain tenun, finishing tekstil',
                'complexity_level' => 'high',
                'default_direct_costs' => [
                    'environmental_assessment' => 10000000,
                    'waste_water_analysis' => 5000000,
                    'air_emission_test' => 4000000,
                    'noise_test' => 1500000,
                    'permit_admin' => 2000000,
                    'chemical_safety_docs' => 3000000,
                ],
                'default_hours_estimate' => [
                    'admin' => 10,
                    'technical' => 60,
                    'review' => 20,
                    'site_visit' => 16,
                ],
                'default_hourly_rates' => [
                    'admin' => 120000,
                    'technical' => 350000,
                    'review' => 280000,
                    'site_visit' => 180000,
                ],
                'regulatory_flags' => ['amdal_required', 'waste_water_permit', 'air_emission_permit', 'chemical_management', 'industrial_permit'],
                'recommended_services' => [
                    ['name' => 'AMDAL', 'priority' => 'high'],
                    ['name' => 'Izin Lingkungan', 'priority' => 'high'],
                    ['name' => 'Waste Water Treatment Design', 'priority' => 'high'],
                    ['name' => 'Air Pollution Control', 'priority' => 'high'],
                    ['name' => 'ISO 14001 Support', 'priority' => 'low'],
                ],
                'examples' => 'Pabrik tekstil 1000 ton/tahun, Finishing tekstil, Garmen besar',
            ],
            [
                'code' => '24201',
                'title' => 'Industri Besi dan Baja Dasar',
                'category' => 'Manufaktur Logam',
                'description' => 'Produksi besi dan baja, peleburan logam',
                'activities' => 'Peleburan besi, produksi baja, casting logam',
                'complexity_level' => 'high',
                'default_direct_costs' => [
                    'environmental_assessment' => 15000000,
                    'air_emission_test' => 6000000,
                    'noise_vibration_test' => 2500000,
                    'waste_analysis' => 4000000,
                    'soil_water_test' => 3000000,
                    'permit_admin' => 3000000,
                ],
                'default_hours_estimate' => [
                    'admin' => 16,
                    'technical' => 80,
                    'review' => 24,
                    'site_visit' => 24,
                ],
                'default_hourly_rates' => [
                    'admin' => 150000,
                    'technical' => 400000,
                    'review' => 320000,
                    'site_visit' => 200000,
                ],
                'regulatory_flags' => ['amdal_required', 'air_emission_permit', 'hazardous_waste', 'water_permit', 'mining_related'],
                'recommended_services' => [
                    ['name' => 'AMDAL', 'priority' => 'high'],
                    ['name' => 'Izin Lingkungan', 'priority' => 'high'],
                    ['name' => 'Air Pollution Control', 'priority' => 'high'],
                    ['name' => 'Hazardous Waste Management', 'priority' => 'high'],
                    ['name' => 'PROPER Rating Support', 'priority' => 'medium'],
                ],
                'examples' => 'Pabrik baja, Foundry, Steel mill',
            ],
            
            // RETAIL & TRADING
            [
                'code' => '47111',
                'title' => 'Perdagangan Eceran Berbagai Macam Barang (Toserba)',
                'category' => 'Perdagangan',
                'description' => 'Toko swalayan, supermarket, hypermarket',
                'activities' => 'Supermarket, minimarket, hypermarket, toserba',
                'complexity_level' => 'medium',
                'default_direct_costs' => [
                    'fire_safety_check' => 2000000,
                    'waste_management_plan' => 1000000,
                    'health_safety_docs' => 800000,
                    'printing' => 200000,
                ],
                'default_hours_estimate' => [
                    'admin' => 5,
                    'technical' => 16,
                    'review' => 6,
                    'site_visit' => 8,
                ],
                'default_hourly_rates' => [
                    'admin' => 100000,
                    'technical' => 200000,
                    'review' => 180000,
                    'site_visit' => 120000,
                ],
                'regulatory_flags' => ['trading_license', 'ukl_upl_required', 'fire_safety', 'food_retail_if_applicable'],
                'recommended_services' => [
                    ['name' => 'Izin Usaha Perdagangan', 'priority' => 'high'],
                    ['name' => 'UKL/UPL', 'priority' => 'medium'],
                    ['name' => 'Fire Safety Certificate', 'priority' => 'high'],
                ],
                'examples' => 'Supermarket 500m2, Minimarket, Hypermarket 2000m2',
            ],
            
            // HEALTHCARE
            [
                'code' => '86101',
                'title' => 'Rumah Sakit',
                'category' => 'Kesehatan',
                'description' => 'Jasa pelayanan rumah sakit umum dan khusus',
                'activities' => 'RS umum, RS khusus, klinik besar, poliklinik',
                'complexity_level' => 'high',
                'default_direct_costs' => [
                    'environmental_assessment' => 12000000,
                    'medical_waste_system' => 8000000,
                    'waste_water_treatment' => 6000000,
                    'air_quality_test' => 3000000,
                    'radiation_safety' => 5000000,
                    'fire_safety_system' => 4000000,
                    'permit_admin' => 3000000,
                ],
                'default_hours_estimate' => [
                    'admin' => 20,
                    'technical' => 80,
                    'review' => 24,
                    'site_visit' => 24,
                ],
                'default_hourly_rates' => [
                    'admin' => 150000,
                    'technical' => 350000,
                    'review' => 280000,
                    'site_visit' => 200000,
                ],
                'regulatory_flags' => ['amdal_required', 'medical_waste_permit', 'radiation_permit', 'health_ministry_license', 'fire_safety_required'],
                'recommended_services' => [
                    ['name' => 'AMDAL', 'priority' => 'high'],
                    ['name' => 'Medical Waste Management', 'priority' => 'high'],
                    ['name' => 'Radiation Safety', 'priority' => 'high'],
                    ['name' => 'Waste Water Treatment', 'priority' => 'high'],
                    ['name' => 'Hospital Accreditation Support', 'priority' => 'medium'],
                ],
                'examples' => 'RS Umum 100 bed, RS Khusus, Klinik Utama',
            ],
            
            // MINING & QUARRYING
            [
                'code' => '08101',
                'title' => 'Pertambangan Batu, Pasir dan Tanah Liat',
                'category' => 'Pertambangan',
                'description' => 'Penggalian batu, pasir, kerikil, tanah liat untuk konstruksi',
                'activities' => 'Quarry batu, tambang pasir, galian tanah',
                'complexity_level' => 'high',
                'default_direct_costs' => [
                    'environmental_assessment' => 20000000,
                    'geotechnical_survey' => 8000000,
                    'water_impact_study' => 5000000,
                    'biodiversity_study' => 6000000,
                    'reclamation_plan' => 10000000,
                    'air_dust_monitoring' => 3000000,
                    'permit_admin' => 5000000,
                ],
                'default_hours_estimate' => [
                    'admin' => 24,
                    'technical' => 120,
                    'review' => 40,
                    'site_visit' => 40,
                ],
                'default_hourly_rates' => [
                    'admin' => 150000,
                    'technical' => 450000,
                    'review' => 350000,
                    'site_visit' => 250000,
                ],
                'regulatory_flags' => ['amdal_required', 'mining_permit', 'reclamation_plan', 'community_consent', 'environmental_bond'],
                'recommended_services' => [
                    ['name' => 'AMDAL', 'priority' => 'high'],
                    ['name' => 'Izin Pertambangan', 'priority' => 'high'],
                    ['name' => 'Reclamation & Closure Plan', 'priority' => 'high'],
                    ['name' => 'Community Development Plan', 'priority' => 'high'],
                    ['name' => 'Environmental Monitoring', 'priority' => 'medium'],
                ],
                'examples' => 'Quarry batu 10 Ha, Tambang pasir, Galian C',
            ],
            
            // WASTE MANAGEMENT
            [
                'code' => '38111',
                'title' => 'Pengumpulan Limbah Non B3',
                'category' => 'Pengelolaan Limbah',
                'description' => 'Pengumpulan, pengangkutan limbah domestik dan komersial non-B3',
                'activities' => 'TPS, TPA, pengangkutan sampah, bank sampah',
                'complexity_level' => 'high',
                'default_direct_costs' => [
                    'environmental_assessment' => 15000000,
                    'leachate_analysis' => 4000000,
                    'air_emission_test' => 3000000,
                    'groundwater_test' => 3500000,
                    'waste_composition_study' => 2500000,
                    'permit_admin' => 3000000,
                ],
                'default_hours_estimate' => [
                    'admin' => 16,
                    'technical' => 80,
                    'review' => 24,
                    'site_visit' => 32,
                ],
                'default_hourly_rates' => [
                    'admin' => 120000,
                    'technical' => 350000,
                    'review' => 280000,
                    'site_visit' => 200000,
                ],
                'regulatory_flags' => ['amdal_required', 'waste_management_license', 'environmental_permit', 'operational_monitoring'],
                'recommended_services' => [
                    ['name' => 'AMDAL/UKL-UPL', 'priority' => 'high'],
                    ['name' => 'Waste Management License', 'priority' => 'high'],
                    ['name' => 'Operational Plan', 'priority' => 'high'],
                    ['name' => 'Environmental Monitoring Program', 'priority' => 'high'],
                ],
                'examples' => 'TPA Regional, TPS 3R, Pengangkutan sampah kota',
            ],
            [
                'code' => '38211',
                'title' => 'Pengolahan dan Pembuangan Limbah B3',
                'category' => 'Limbah B3',
                'description' => 'Pengolahan, penyimpanan, dan pembuangan limbah B3',
                'activities' => 'Incinerator B3, landfill B3, pengolahan limbah medis',
                'complexity_level' => 'high',
                'default_direct_costs' => [
                    'environmental_assessment' => 25000000,
                    'b3_characterization' => 8000000,
                    'toxicity_test' => 6000000,
                    'groundwater_monitoring' => 5000000,
                    'air_emission_test' => 5000000,
                    'safety_audit' => 4000000,
                    'permit_admin' => 5000000,
                ],
                'default_hours_estimate' => [
                    'admin' => 30,
                    'technical' => 150,
                    'review' => 50,
                    'site_visit' => 40,
                ],
                'default_hourly_rates' => [
                    'admin' => 180000,
                    'technical' => 500000,
                    'review' => 400000,
                    'site_visit' => 280000,
                ],
                'regulatory_flags' => ['amdal_required', 'b3_license', 'environmental_ministry_approval', 'emergency_response_plan', 'insurance_required'],
                'recommended_services' => [
                    ['name' => 'AMDAL', 'priority' => 'high'],
                    ['name' => 'B3 Management License', 'priority' => 'high'],
                    ['name' => 'Emergency Response Plan', 'priority' => 'high'],
                    ['name' => 'Safety Management System', 'priority' => 'high'],
                    ['name' => 'ISO 14001 & OHSAS 18001', 'priority' => 'medium'],
                ],
                'examples' => 'Incinerator limbah medis, TPA B3, Pengolahan limbah industri',
            ],
            
            // ENERGY & UTILITIES
            [
                'code' => '35111',
                'title' => 'Pembangkit Listrik',
                'category' => 'Energi',
                'description' => 'Pembangkit listrik tenaga termal, hidro, diesel, dll',
                'activities' => 'PLTU, PLTD, PLTA, PLTMH, Solar PV',
                'complexity_level' => 'high',
                'default_direct_costs' => [
                    'environmental_assessment' => 30000000,
                    'air_emission_modeling' => 8000000,
                    'water_impact_study' => 6000000,
                    'noise_vibration_study' => 4000000,
                    'social_impact_assessment' => 10000000,
                    'grid_connection_study' => 5000000,
                    'permit_admin' => 8000000,
                ],
                'default_hours_estimate' => [
                    'admin' => 40,
                    'technical' => 200,
                    'review' => 60,
                    'site_visit' => 48,
                ],
                'default_hourly_rates' => [
                    'admin' => 200000,
                    'technical' => 550000,
                    'review' => 450000,
                    'site_visit' => 300000,
                ],
                'regulatory_flags' => ['amdal_required', 'esdm_license', 'environmental_permit', 'grid_connection_approval', 'community_consent'],
                'recommended_services' => [
                    ['name' => 'AMDAL', 'priority' => 'high'],
                    ['name' => 'Power Generation License', 'priority' => 'high'],
                    ['name' => 'Environmental Permit', 'priority' => 'high'],
                    ['name' => 'Community Consultation', 'priority' => 'high'],
                    ['name' => 'Carbon Credits Support', 'priority' => 'low'],
                ],
                'examples' => 'PLTU 50 MW, PLTA 10 MW, Solar Farm 5 MWp',
            ],
            
            // AGRICULTURE
            [
                'code' => '01111',
                'title' => 'Pertanian Padi',
                'category' => 'Pertanian',
                'description' => 'Budidaya padi sawah dan padi gogo',
                'activities' => 'Sawah padi, pertanian padi organik',
                'complexity_level' => 'low',
                'default_direct_costs' => [
                    'soil_test' => 1500000,
                    'water_quality_test' => 1000000,
                    'organic_certification' => 2000000,
                    'printing' => 150000,
                ],
                'default_hours_estimate' => [
                    'admin' => 3,
                    'technical' => 12,
                    'review' => 4,
                    'site_visit' => 8,
                ],
                'default_hourly_rates' => [
                    'admin' => 80000,
                    'technical' => 150000,
                    'review' => 120000,
                    'site_visit' => 100000,
                ],
                'regulatory_flags' => ['land_use_permit', 'water_use_permit', 'organic_cert_optional'],
                'recommended_services' => [
                    ['name' => 'Land Use Permit', 'priority' => 'medium'],
                    ['name' => 'Organic Certification', 'priority' => 'low'],
                    ['name' => 'Good Agricultural Practice', 'priority' => 'low'],
                ],
                'examples' => 'Sawah 5 Ha, Pertanian organik, Kelompok tani',
            ],
            
            // PROFESSIONAL SERVICES
            [
                'code' => '71101',
                'title' => 'Jasa Arsitektur',
                'category' => 'Jasa Profesional',
                'description' => 'Jasa konsultasi dan desain arsitektur',
                'activities' => 'Desain bangunan, konsultasi arsitektur, site planning',
                'complexity_level' => 'low',
                'default_direct_costs' => [
                    'printing_plotting' => 500000,
                    'software_license' => 1000000,
                    'site_survey_tools' => 500000,
                ],
                'default_hours_estimate' => [
                    'admin' => 4,
                    'technical' => 16,
                    'review' => 6,
                ],
                'default_hourly_rates' => [
                    'admin' => 100000,
                    'technical' => 250000,
                    'review' => 200000,
                ],
                'regulatory_flags' => ['professional_license', 'business_permit'],
                'recommended_services' => [
                    ['name' => 'Business License', 'priority' => 'high'],
                    ['name' => 'Professional Insurance', 'priority' => 'medium'],
                ],
                'examples' => 'Kantor arsitektur, Konsultan desain, Arsitek independen',
            ],
            
            // TRANSPORTATION
            [
                'code' => '49311',
                'title' => 'Angkutan Darat untuk Penumpang',
                'category' => 'Transportasi',
                'description' => 'Jasa angkutan penumpang dengan bus, taksi, shuttle',
                'activities' => 'Bus AKAP, taksi, travel, shuttle',
                'complexity_level' => 'medium',
                'default_direct_costs' => [
                    'vehicle_emission_test' => 500000,
                    'safety_inspection' => 1000000,
                    'insurance_admin' => 800000,
                    'permit_admin' => 1000000,
                ],
                'default_hours_estimate' => [
                    'admin' => 6,
                    'technical' => 16,
                    'review' => 6,
                ],
                'default_hourly_rates' => [
                    'admin' => 100000,
                    'technical' => 180000,
                    'review' => 150000,
                ],
                'regulatory_flags' => ['transportation_license', 'vehicle_registration', 'route_permit', 'safety_standards'],
                'recommended_services' => [
                    ['name' => 'Transportation License', 'priority' => 'high'],
                    ['name' => 'Fleet Management Support', 'priority' => 'medium'],
                    ['name' => 'Safety Management System', 'priority' => 'medium'],
                ],
                'examples' => 'Bus AKAP 10 unit, Taksi online, Travel antar kota',
            ],
            
            // EDUCATION
            [
                'code' => '85301',
                'title' => 'Pendidikan Menengah Atas',
                'category' => 'Pendidikan',
                'description' => 'Sekolah menengah atas, SMK, madrasah aliyah',
                'activities' => 'SMA, SMK, MA, sekolah internasional',
                'complexity_level' => 'medium',
                'default_direct_costs' => [
                    'fire_safety_inspection' => 2000000,
                    'health_sanitation_check' => 1000000,
                    'building_safety_audit' => 1500000,
                    'accreditation_support' => 3000000,
                    'permit_admin' => 1000000,
                ],
                'default_hours_estimate' => [
                    'admin' => 8,
                    'technical' => 24,
                    'review' => 8,
                    'site_visit' => 8,
                ],
                'default_hourly_rates' => [
                    'admin' => 100000,
                    'technical' => 200000,
                    'review' => 180000,
                    'site_visit' => 120000,
                ],
                'regulatory_flags' => ['education_permit', 'building_safety', 'fire_safety', 'accreditation'],
                'recommended_services' => [
                    ['name' => 'School Operational Permit', 'priority' => 'high'],
                    ['name' => 'Building Safety Certificate', 'priority' => 'high'],
                    ['name' => 'Accreditation Support', 'priority' => 'medium'],
                    ['name' => 'UKL/UPL', 'priority' => 'medium'],
                ],
                'examples' => 'SMA swasta 500 siswa, SMK, Sekolah internasional',
            ],
            
            // PLANTATION
            [
                'code' => '01220',
                'title' => 'Perkebunan Kelapa Sawit',
                'category' => 'Perkebunan',
                'description' => 'Budidaya dan pengolahan kelapa sawit',
                'activities' => 'Perkebunan sawit, pabrik CPO',
                'complexity_level' => 'high',
                'default_direct_costs' => [
                    'environmental_assessment' => 50000000,
                    'hcv_hcs_study' => 30000000,
                    'social_impact_assessment' => 15000000,
                    'soil_land_survey' => 10000000,
                    'biodiversity_study' => 12000000,
                    'community_consent' => 8000000,
                    'permit_admin' => 10000000,
                ],
                'default_hours_estimate' => [
                    'admin' => 60,
                    'technical' => 300,
                    'review' => 80,
                    'site_visit' => 80,
                ],
                'default_hourly_rates' => [
                    'admin' => 150000,
                    'technical' => 500000,
                    'review' => 400000,
                    'site_visit' => 280000,
                ],
                'regulatory_flags' => ['amdal_required', 'plantation_permit', 'hcv_hcs_required', 'ispo_rspo', 'community_consent'],
                'recommended_services' => [
                    ['name' => 'AMDAL', 'priority' => 'high'],
                    ['name' => 'HCV-HCS Assessment', 'priority' => 'high'],
                    ['name' => 'Plantation License (IUP)', 'priority' => 'high'],
                    ['name' => 'ISPO/RSPO Certification', 'priority' => 'high'],
                    ['name' => 'Community Development', 'priority' => 'high'],
                ],
                'examples' => 'Perkebunan 5000 Ha, Pabrik CPO, Plasma smallholder',
            ],
        ];

        foreach ($kblis as $kbli) {
            Kbli::updateOrCreate(
                ['code' => $kbli['code']],
                $kbli
            );
        }
        
        $this->command->info('✅ Seeded ' . count($kblis) . ' KBLI entries');
    }
}
