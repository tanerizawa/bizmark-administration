<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PermitTemplate;
use App\Models\PermitTemplateItem;
use App\Models\PermitTemplateDependency;
use App\Models\PermitType;

class ImprovedPermitTemplatesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * Membuat template izin yang lebih baik berdasarkan:
     * 1. Best practice perizinan di Indonesia
     * 2. Logika sistem dengan dependencies yang benar
     * 3. Kebutuhan aktual dari project yang ada
     * 4. Alur yang realistis dan sesuai regulasi
     */
    public function run(): void
    {
        // Clear existing templates (optional - uncomment if needed)
        // PermitTemplateDependency::truncate();
        // PermitTemplateItem::truncate();
        // PermitTemplate::truncate();

        $this->command->info("🚀 Creating improved permit templates...\n");

        // Get permit types
        $permitTypes = PermitType::pluck('id', 'code')->toArray();

        // ==========================================
        // 1. TEMPLATE: UKL-UPL PABRIK/INDUSTRI LENGKAP
        // ==========================================
        $this->command->info("📋 Creating: UKL-UPL Pabrik/Industri Lengkap");
        
        $templateUKLIndustri = PermitTemplate::create([
            'name' => 'UKL-UPL Pabrik/Industri Lengkap',
            'description' => 'Template lengkap untuk pembangunan pabrik atau industri dengan luas > 1 hektar. Meliputi proses perizinan dari persiapan lahan hingga izin lingkungan siap operasi.',
            'use_case' => 'Pembangunan pabrik manufaktur, industri pengolahan, warehouse besar, atau fasilitas industri lainnya',
            'category' => 'industrial',
            'is_public' => true,
            'usage_count' => 0,
        ]);

        $items = [];
        
        // 1. Sertifikat Tanah (Prasyarat Pertama)
        $items['sertifikat'] = PermitTemplateItem::create([
            'template_id' => $templateUKLIndustri->id,
            'permit_type_id' => $permitTypes['SERTIFIKAT_TANAH'] ?? null,
            'sequence_order' => 1,
            'is_goal_permit' => false,
            'estimated_days' => 60,
            'estimated_cost' => 10000000,
            'notes' => 'Wajib memiliki sertifikat tanah terlebih dahulu sebagai bukti kepemilikan legal',
        ]);

        // 2. Pertek BPN (Pemetaan)
        $items['pertek'] = PermitTemplateItem::create([
            'template_id' => $templateUKLIndustri->id,
            'permit_type_id' => $permitTypes['PERTEK_BPN'] ?? null,
            'sequence_order' => 2,
            'is_goal_permit' => false,
            'estimated_days' => 14,
            'estimated_cost' => 5000000,
            'notes' => 'Persetujuan teknis pemetaan dari BPN untuk validasi batas tanah',
        ]);

        // 3. PKKPR (Kesesuaian Tata Ruang)
        $items['pkkpr'] = PermitTemplateItem::create([
            'template_id' => $templateUKLIndustri->id,
            'permit_type_id' => $permitTypes['PKKPR'] ?? null,
            'sequence_order' => 3,
            'is_goal_permit' => false,
            'estimated_days' => 14,
            'estimated_cost' => 5000000,
            'notes' => 'Memastikan lokasi sesuai dengan RTRW (Rencana Tata Ruang Wilayah)',
        ]);

        // 4. Siteplan
        $items['siteplan'] = PermitTemplateItem::create([
            'template_id' => $templateUKLIndustri->id,
            'permit_type_id' => $permitTypes['SITEPLAN'] ?? null,
            'sequence_order' => 4,
            'is_goal_permit' => false,
            'estimated_days' => 14,
            'estimated_cost' => 6000000,
            'notes' => 'Persetujuan rencana tapak dari Dinas PUPR',
        ]);

        // 5. Andalalin
        $items['andalalin'] = PermitTemplateItem::create([
            'template_id' => $templateUKLIndustri->id,
            'permit_type_id' => $permitTypes['ANDALALIN'] ?? null,
            'sequence_order' => 5,
            'is_goal_permit' => false,
            'estimated_days' => 30,
            'estimated_cost' => 30000000,
            'notes' => 'Analisis dampak lalu lintas untuk kegiatan yang berdampak pada traffic',
        ]);

        // 6. PBG (Persetujuan Bangunan Gedung)
        $items['pbg'] = PermitTemplateItem::create([
            'template_id' => $templateUKLIndustri->id,
            'permit_type_id' => $permitTypes['PBG'] ?? null,
            'sequence_order' => 6,
            'is_goal_permit' => false,
            'estimated_days' => 30,
            'estimated_cost' => 30000000,
            'notes' => 'Persetujuan untuk memulai pembangunan fisik',
        ]);

        // 7. SLF (Sertifikat Laik Fungsi)
        $items['slf'] = PermitTemplateItem::create([
            'template_id' => $templateUKLIndustri->id,
            'permit_type_id' => $permitTypes['SLF'] ?? null,
            'sequence_order' => 7,
            'is_goal_permit' => false,
            'estimated_days' => 14,
            'estimated_cost' => 12000000,
            'notes' => 'Sertifikat kelayakan bangunan setelah konstruksi selesai',
        ]);

        // 8. UKL-UPL (GOAL - Izin Lingkungan)
        $items['ukl'] = PermitTemplateItem::create([
            'template_id' => $templateUKLIndustri->id,
            'permit_type_id' => $permitTypes['UKL_UPL'] ?? null,
            'sequence_order' => 8,
            'is_goal_permit' => true,
            'estimated_days' => 14,
            'estimated_cost' => 10000000,
            'notes' => 'TUJUAN AKHIR: Izin lingkungan untuk operasional pabrik',
        ]);

        // 9. Izin Operasional
        $items['operasional'] = PermitTemplateItem::create([
            'template_id' => $templateUKLIndustri->id,
            'permit_type_id' => $permitTypes['IZIN_OPERASIONAL'] ?? null,
            'sequence_order' => 9,
            'is_goal_permit' => false,
            'estimated_days' => 14,
            'estimated_cost' => 8000000,
            'notes' => 'Izin untuk mulai operasional komersial',
        ]);

        // Set Dependencies (Logical Flow)
        PermitTemplateDependency::create([
            'template_id' => $templateUKLIndustri->id,
            'permit_item_id' => $items['pertek']->id,
            'depends_on_item_id' => $items['sertifikat']->id,
            'dependency_type' => 'MANDATORY',
        ]);

        PermitTemplateDependency::create([
            'template_id' => $templateUKLIndustri->id,
            'permit_item_id' => $items['pkkpr']->id,
            'depends_on_item_id' => $items['sertifikat']->id,
            'dependency_type' => 'MANDATORY',
        ]);

        PermitTemplateDependency::create([
            'template_id' => $templateUKLIndustri->id,
            'permit_item_id' => $items['siteplan']->id,
            'depends_on_item_id' => $items['pkkpr']->id,
            'dependency_type' => 'MANDATORY',
        ]);

        PermitTemplateDependency::create([
            'template_id' => $templateUKLIndustri->id,
            'permit_item_id' => $items['pbg']->id,
            'depends_on_item_id' => $items['siteplan']->id,
            'dependency_type' => 'MANDATORY',
        ]);

        PermitTemplateDependency::create([
            'template_id' => $templateUKLIndustri->id,
            'permit_item_id' => $items['pbg']->id,
            'depends_on_item_id' => $items['andalalin']->id,
            'dependency_type' => 'OPTIONAL', // Opsional, bisa paralel
        ]);

        PermitTemplateDependency::create([
            'template_id' => $templateUKLIndustri->id,
            'permit_item_id' => $items['slf']->id,
            'depends_on_item_id' => $items['pbg']->id,
            'dependency_type' => 'MANDATORY',
        ]);

        PermitTemplateDependency::create([
            'template_id' => $templateUKLIndustri->id,
            'permit_item_id' => $items['ukl']->id,
            'depends_on_item_id' => $items['slf']->id,
            'dependency_type' => 'MANDATORY',
        ]);

        PermitTemplateDependency::create([
            'template_id' => $templateUKLIndustri->id,
            'permit_item_id' => $items['operasional']->id,
            'depends_on_item_id' => $items['ukl']->id,
            'dependency_type' => 'MANDATORY',
        ]);

        $this->command->info("✅ Template created with " . count($items) . " permits and dependencies\n");

        // ==========================================
        // 2. TEMPLATE: AMDAL PROYEK STRATEGIS
        // ==========================================
        $this->command->info("📋 Creating: AMDAL Proyek Strategis");
        
        $templateAMDAL = PermitTemplate::create([
            'name' => 'AMDAL Proyek Strategis Nasional',
            'description' => 'Template untuk proyek besar yang berdampak penting terhadap lingkungan seperti pembangkit listrik, pabrik kimia, atau proyek infrastruktur strategis nasional.',
            'use_case' => 'Proyek dengan dampak lingkungan signifikan, wajib AMDAL sesuai PP 22/2021',
            'category' => 'strategic',
            'is_public' => true,
            'usage_count' => 0,
        ]);

        $aItems = [];
        
        $aItems['sertifikat'] = PermitTemplateItem::create([
            'template_id' => $templateAMDAL->id,
            'permit_type_id' => $permitTypes['SERTIFIKAT_TANAH'] ?? null,
            'sequence_order' => 1,
            'is_goal_permit' => false,
            'estimated_days' => 60,
            'estimated_cost' => 15000000,
        ]);

        $aItems['pkkpr'] = PermitTemplateItem::create([
            'template_id' => $templateAMDAL->id,
            'permit_type_id' => $permitTypes['PKKPR'] ?? null,
            'sequence_order' => 2,
            'is_goal_permit' => false,
            'estimated_days' => 14,
            'estimated_cost' => 8000000,
        ]);

        $aItems['amdal'] = PermitTemplateItem::create([
            'template_id' => $templateAMDAL->id,
            'permit_type_id' => $permitTypes['AMDAL'] ?? null,
            'sequence_order' => 3,
            'is_goal_permit' => true,
            'estimated_days' => 75,
            'estimated_cost' => 125000000,
            'notes' => 'TUJUAN AKHIR: AMDAL untuk proyek strategis',
        ]);

        $aItems['izin_lh'] = PermitTemplateItem::create([
            'template_id' => $templateAMDAL->id,
            'permit_type_id' => $permitTypes['IZIN_LINGKUNGAN'] ?? null,
            'sequence_order' => 4,
            'is_goal_permit' => false,
            'estimated_days' => 30,
            'estimated_cost' => 8000000,
        ]);

        $aItems['pbg'] = PermitTemplateItem::create([
            'template_id' => $templateAMDAL->id,
            'permit_type_id' => $permitTypes['PBG'] ?? null,
            'sequence_order' => 5,
            'is_goal_permit' => false,
            'estimated_days' => 30,
            'estimated_cost' => 40000000,
        ]);

        $aItems['slf'] = PermitTemplateItem::create([
            'template_id' => $templateAMDAL->id,
            'permit_type_id' => $permitTypes['SLF'] ?? null,
            'sequence_order' => 6,
            'is_goal_permit' => false,
            'estimated_days' => 14,
            'estimated_cost' => 15000000,
        ]);

        $aItems['operasional'] = PermitTemplateItem::create([
            'template_id' => $templateAMDAL->id,
            'permit_type_id' => $permitTypes['IZIN_OPERASIONAL'] ?? null,
            'sequence_order' => 7,
            'is_goal_permit' => false,
            'estimated_days' => 14,
            'estimated_cost' => 12000000,
        ]);

        // Dependencies
        PermitTemplateDependency::create([
            'template_id' => $templateAMDAL->id,
            'permit_item_id' => $aItems['pkkpr']->id,
            'depends_on_item_id' => $aItems['sertifikat']->id,
            'dependency_type' => 'MANDATORY',
        ]);

        PermitTemplateDependency::create([
            'template_id' => $templateAMDAL->id,
            'permit_item_id' => $aItems['amdal']->id,
            'depends_on_item_id' => $aItems['pkkpr']->id,
            'dependency_type' => 'MANDATORY',
        ]);

        PermitTemplateDependency::create([
            'template_id' => $templateAMDAL->id,
            'permit_item_id' => $aItems['izin_lh']->id,
            'depends_on_item_id' => $aItems['amdal']->id,
            'dependency_type' => 'MANDATORY',
        ]);

        PermitTemplateDependency::create([
            'template_id' => $templateAMDAL->id,
            'permit_item_id' => $aItems['pbg']->id,
            'depends_on_item_id' => $aItems['izin_lh']->id,
            'dependency_type' => 'MANDATORY',
        ]);

        PermitTemplateDependency::create([
            'template_id' => $templateAMDAL->id,
            'permit_item_id' => $aItems['slf']->id,
            'depends_on_item_id' => $aItems['pbg']->id,
            'dependency_type' => 'MANDATORY',
        ]);

        PermitTemplateDependency::create([
            'template_id' => $templateAMDAL->id,
            'permit_item_id' => $aItems['operasional']->id,
            'depends_on_item_id' => $aItems['slf']->id,
            'dependency_type' => 'MANDATORY',
        ]);

        $this->command->info("✅ Template created with " . count($aItems) . " permits\n");

        // ==========================================
        // 3. TEMPLATE: STARTUP BISNIS LENGKAP
        // ==========================================
        $this->command->info("📋 Creating: Startup Bisnis Lengkap");
        
        $templateStartup = PermitTemplate::create([
            'name' => 'Startup Bisnis Lengkap (NIB + Office)',
            'description' => 'Template untuk memulai bisnis baru dengan kantor fisik. Meliputi legalitas perusahaan hingga izin operasional kantor.',
            'use_case' => 'Startup, UMKM yang berkembang, atau bisnis baru dengan kantor fisik',
            'category' => 'business',
            'is_public' => true,
            'usage_count' => 0,
        ]);

        $sItems = [];
        
        $sItems['nib'] = PermitTemplateItem::create([
            'template_id' => $templateStartup->id,
            'permit_type_id' => $permitTypes['NIB'] ?? null,
            'sequence_order' => 1,
            'is_goal_permit' => false,
            'estimated_days' => 1,
            'estimated_cost' => 500000,
            'notes' => 'NIB adalah identitas usaha, wajib paling awal',
        ]);

        $sItems['tdi'] = PermitTemplateItem::create([
            'template_id' => $templateStartup->id,
            'permit_type_id' => $permitTypes['TDI'] ?? null,
            'sequence_order' => 2,
            'is_goal_permit' => false,
            'estimated_days' => 7,
            'estimated_cost' => 3000000,
            'notes' => 'Jika bisnis berhubungan dengan industri/manufaktur',
        ]);

        $sItems['pbg'] = PermitTemplateItem::create([
            'template_id' => $templateStartup->id,
            'permit_type_id' => $permitTypes['PBG'] ?? null,
            'sequence_order' => 3,
            'is_goal_permit' => false,
            'estimated_days' => 30,
            'estimated_cost' => 15000000,
            'notes' => 'Jika ada renovasi atau pembangunan kantor',
        ]);

        $sItems['slf'] = PermitTemplateItem::create([
            'template_id' => $templateStartup->id,
            'permit_type_id' => $permitTypes['SLF'] ?? null,
            'sequence_order' => 4,
            'is_goal_permit' => false,
            'estimated_days' => 14,
            'estimated_cost' => 8000000,
        ]);

        $sItems['sppl'] = PermitTemplateItem::create([
            'template_id' => $templateStartup->id,
            'permit_type_id' => $permitTypes['SPPL'] ?? null,
            'sequence_order' => 5,
            'is_goal_permit' => false,
            'estimated_days' => 7,
            'estimated_cost' => 1000000,
            'notes' => 'Untuk UMKM, cukup SPPL (pernyataan pengelolaan lingkungan)',
        ]);

        $sItems['operasional'] = PermitTemplateItem::create([
            'template_id' => $templateStartup->id,
            'permit_type_id' => $permitTypes['IZIN_OPERASIONAL'] ?? null,
            'sequence_order' => 6,
            'is_goal_permit' => true,
            'estimated_days' => 14,
            'estimated_cost' => 5000000,
            'notes' => 'TUJUAN AKHIR: Izin untuk mulai beroperasi',
        ]);

        // Dependencies
        PermitTemplateDependency::create([
            'template_id' => $templateStartup->id,
            'permit_item_id' => $sItems['tdi']->id,
            'depends_on_item_id' => $sItems['nib']->id,
            'dependency_type' => 'MANDATORY',
        ]);

        PermitTemplateDependency::create([
            'template_id' => $templateStartup->id,
            'permit_item_id' => $sItems['slf']->id,
            'depends_on_item_id' => $sItems['pbg']->id,
            'dependency_type' => 'MANDATORY',
        ]);

        PermitTemplateDependency::create([
            'template_id' => $templateStartup->id,
            'permit_item_id' => $sItems['operasional']->id,
            'depends_on_item_id' => $sItems['slf']->id,
            'dependency_type' => 'MANDATORY',
        ]);

        PermitTemplateDependency::create([
            'template_id' => $templateStartup->id,
            'permit_item_id' => $sItems['operasional']->id,
            'depends_on_item_id' => $sItems['sppl']->id,
            'dependency_type' => 'MANDATORY',
        ]);

        $this->command->info("✅ Template created with " . count($sItems) . " permits\n");

        // ==========================================
        // 4. TEMPLATE: BANGUNAN KOMERSIAL (MALL/HOTEL)
        // ==========================================
        $this->command->info("📋 Creating: Bangunan Komersial");
        
        $templateKomersial = PermitTemplate::create([
            'name' => 'Bangunan Komersial (Mall/Hotel/Apartemen)',
            'description' => 'Template untuk pembangunan gedung komersial seperti mall, hotel, apartemen, atau perkantoran tinggi.',
            'use_case' => 'Proyek properti komersial, mixed-use building, high-rise',
            'category' => 'commercial',
            'is_public' => true,
            'usage_count' => 0,
        ]);

        $kItems = [];
        
        $kItems['sertifikat'] = PermitTemplateItem::create([
            'template_id' => $templateKomersial->id,
            'permit_type_id' => $permitTypes['SERTIFIKAT_TANAH'] ?? null,
            'sequence_order' => 1,
            'is_goal_permit' => false,
            'estimated_days' => 60,
            'estimated_cost' => 12000000,
        ]);

        $kItems['pkkpr'] = PermitTemplateItem::create([
            'template_id' => $templateKomersial->id,
            'permit_type_id' => $permitTypes['PKKPR'] ?? null,
            'sequence_order' => 2,
            'is_goal_permit' => false,
            'estimated_days' => 14,
            'estimated_cost' => 6000000,
        ]);

        $kItems['siteplan'] = PermitTemplateItem::create([
            'template_id' => $templateKomersial->id,
            'permit_type_id' => $permitTypes['SITEPLAN'] ?? null,
            'sequence_order' => 3,
            'is_goal_permit' => false,
            'estimated_days' => 14,
            'estimated_cost' => 8000000,
        ]);

        $kItems['andalalin'] = PermitTemplateItem::create([
            'template_id' => $templateKomersial->id,
            'permit_type_id' => $permitTypes['ANDALALIN'] ?? null,
            'sequence_order' => 4,
            'is_goal_permit' => false,
            'estimated_days' => 30,
            'estimated_cost' => 35000000,
        ]);

        $kItems['ukl'] = PermitTemplateItem::create([
            'template_id' => $templateKomersial->id,
            'permit_type_id' => $permitTypes['UKL_UPL'] ?? null,
            'sequence_order' => 5,
            'is_goal_permit' => false,
            'estimated_days' => 14,
            'estimated_cost' => 10000000,
        ]);

        $kItems['pbg'] = PermitTemplateItem::create([
            'template_id' => $templateKomersial->id,
            'permit_type_id' => $permitTypes['PBG'] ?? null,
            'sequence_order' => 6,
            'is_goal_permit' => false,
            'estimated_days' => 30,
            'estimated_cost' => 35000000,
        ]);

        $kItems['slf'] = PermitTemplateItem::create([
            'template_id' => $templateKomersial->id,
            'permit_type_id' => $permitTypes['SLF'] ?? null,
            'sequence_order' => 7,
            'is_goal_permit' => true,
            'estimated_days' => 14,
            'estimated_cost' => 15000000,
            'notes' => 'TUJUAN AKHIR: SLF untuk izin operasional gedung',
        ]);

        // Dependencies
        PermitTemplateDependency::create([
            'template_id' => $templateKomersial->id,
            'permit_item_id' => $kItems['pkkpr']->id,
            'depends_on_item_id' => $kItems['sertifikat']->id,
            'dependency_type' => 'MANDATORY',
        ]);

        PermitTemplateDependency::create([
            'template_id' => $templateKomersial->id,
            'permit_item_id' => $kItems['siteplan']->id,
            'depends_on_item_id' => $kItems['pkkpr']->id,
            'dependency_type' => 'MANDATORY',
        ]);

        PermitTemplateDependency::create([
            'template_id' => $templateKomersial->id,
            'permit_item_id' => $kItems['pbg']->id,
            'depends_on_item_id' => $kItems['siteplan']->id,
            'dependency_type' => 'MANDATORY',
        ]);

        PermitTemplateDependency::create([
            'template_id' => $templateKomersial->id,
            'permit_item_id' => $kItems['pbg']->id,
            'depends_on_item_id' => $kItems['andalalin']->id,
            'dependency_type' => 'OPTIONAL',
        ]);

        PermitTemplateDependency::create([
            'template_id' => $templateKomersial->id,
            'permit_item_id' => $kItems['pbg']->id,
            'depends_on_item_id' => $kItems['ukl']->id,
            'dependency_type' => 'MANDATORY',
        ]);

        PermitTemplateDependency::create([
            'template_id' => $templateKomersial->id,
            'permit_item_id' => $kItems['slf']->id,
            'depends_on_item_id' => $kItems['pbg']->id,
            'dependency_type' => 'MANDATORY',
        ]);

        $this->command->info("✅ Template created with " . count($kItems) . " permits\n");

        $this->command->info("\n🎉 All improved templates created successfully!");
        $this->command->info("Total: 4 comprehensive templates with proper dependencies");
    }
}
