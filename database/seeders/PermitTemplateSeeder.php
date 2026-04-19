<?php

namespace Database\Seeders;

use App\Models\PermitType;
use App\Models\PermitTemplate;
use App\Models\PermitTemplateItem;
use App\Models\PermitTemplateDependency;
use Illuminate\Database\Seeder;

class PermitTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get permit types
        $uklUpl = PermitType::where('code', 'UKL_UPL')->first();
        $pertekBpn = PermitType::where('code', 'PERTEK_BPN')->first();
        $siteplan = PermitType::where('code', 'SITEPLAN')->first();
        $pbg = PermitType::where('code', 'PBG')->first();
        $andalalin = PermitType::where('code', 'ANDALALIN')->first();
        $izinLingkungan = PermitType::where('code', 'IZIN_LINGKUNGAN')->first();
        $nib = PermitType::where('code', 'NIB')->first();
        $izinOperasional = PermitType::where('code', 'IZIN_OPERASIONAL')->first();
        $slf = PermitType::where('code', 'SLF')->first();

        // Template 1: UKL-UPL untuk Pabrik/Industri
        $template1 = PermitTemplate::create([
            'name' => 'UKL-UPL Pabrik/Industri',
            'description' => 'Template lengkap untuk pengurusan UKL-UPL pabrik atau industri dengan luas lahan > 1 hektar',
            'use_case' => 'Cocok untuk: Pabrik kelapa sawit, pabrik pengolahan, industri manufaktur menengah-besar',
            'category' => 'environmental',
            'is_public' => true,
        ]);

        // Items untuk Template 1
        $t1_pertek = PermitTemplateItem::create([
            'template_id' => $template1->id,
            'permit_type_id' => $pertekBpn->id,
            'sequence_order' => 1,
            'estimated_days' => 14,
            'estimated_cost' => 5000000,
            'notes' => 'Harus diselesaikan pertama sebelum izin lainnya',
        ]);

        $t1_siteplan = PermitTemplateItem::create([
            'template_id' => $template1->id,
            'permit_type_id' => $siteplan->id,
            'sequence_order' => 2,
            'estimated_days' => 7,
            'estimated_cost' => 3000000,
            'notes' => 'Butuh hasil Pertek BPN',
        ]);

        $t1_pbg = PermitTemplateItem::create([
            'template_id' => $template1->id,
            'permit_type_id' => $pbg->id,
            'sequence_order' => 3,
            'estimated_days' => 30,
            'estimated_cost' => 25000000,
            'notes' => 'Bisa parallel dengan Andalalin',
        ]);

        $t1_andalalin = PermitTemplateItem::create([
            'template_id' => $template1->id,
            'permit_type_id' => $andalalin->id,
            'sequence_order' => 4,
            'estimated_days' => 30,
            'estimated_cost' => 30000000,
            'notes' => 'Bisa parallel dengan PBG',
        ]);

        $t1_uklupl = PermitTemplateItem::create([
            'template_id' => $template1->id,
            'permit_type_id' => $uklUpl->id,
            'sequence_order' => 5,
            'is_goal_permit' => true,
            'estimated_days' => 14,
            'estimated_cost' => 10000000,
            'notes' => 'Izin target akhir',
        ]);

        // Dependencies untuk Template 1
        PermitTemplateDependency::create([
            'template_id' => $template1->id,
            'permit_item_id' => $t1_siteplan->id,
            'depends_on_item_id' => $t1_pertek->id,
            'dependency_type' => 'MANDATORY',
        ]);

        PermitTemplateDependency::create([
            'template_id' => $template1->id,
            'permit_item_id' => $t1_pbg->id,
            'depends_on_item_id' => $t1_siteplan->id,
            'dependency_type' => 'MANDATORY',
        ]);

        PermitTemplateDependency::create([
            'template_id' => $template1->id,
            'permit_item_id' => $t1_andalalin->id,
            'depends_on_item_id' => $t1_siteplan->id,
            'dependency_type' => 'MANDATORY',
        ]);

        PermitTemplateDependency::create([
            'template_id' => $template1->id,
            'permit_item_id' => $t1_uklupl->id,
            'depends_on_item_id' => $t1_pbg->id,
            'dependency_type' => 'MANDATORY',
        ]);

        PermitTemplateDependency::create([
            'template_id' => $template1->id,
            'permit_item_id' => $t1_uklupl->id,
            'depends_on_item_id' => $t1_andalalin->id,
            'dependency_type' => 'MANDATORY',
        ]);

        // Template 2: UKL-UPL Sederhana (tanpa Pertek BPN)
        $template2 = PermitTemplate::create([
            'name' => 'UKL-UPL Bangunan Komersial',
            'description' => 'Template untuk bangunan komersial seperti apartemen, hotel, atau perkantoran',
            'use_case' => 'Cocok untuk: Apartemen, hotel, mall, gedung perkantoran di area perkotaan',
            'category' => 'environmental',
            'is_public' => true,
        ]);

        // Items untuk Template 2
        $t2_pbg = PermitTemplateItem::create([
            'template_id' => $template2->id,
            'permit_type_id' => $pbg->id,
            'sequence_order' => 1,
            'estimated_days' => 30,
            'estimated_cost' => 20000000,
        ]);

        $t2_andalalin = PermitTemplateItem::create([
            'template_id' => $template2->id,
            'permit_type_id' => $andalalin->id,
            'sequence_order' => 2,
            'estimated_days' => 30,
            'estimated_cost' => 25000000,
        ]);

        $t2_uklupl = PermitTemplateItem::create([
            'template_id' => $template2->id,
            'permit_type_id' => $uklUpl->id,
            'sequence_order' => 3,
            'is_goal_permit' => true,
            'estimated_days' => 14,
            'estimated_cost' => 8000000,
        ]);

        // Dependencies untuk Template 2
        PermitTemplateDependency::create([
            'template_id' => $template2->id,
            'permit_item_id' => $t2_uklupl->id,
            'depends_on_item_id' => $t2_pbg->id,
            'dependency_type' => 'MANDATORY',
        ]);

        PermitTemplateDependency::create([
            'template_id' => $template2->id,
            'permit_item_id' => $t2_uklupl->id,
            'depends_on_item_id' => $t2_andalalin->id,
            'dependency_type' => 'OPTIONAL',
            'notes' => 'Andalalin opsional tergantung skala proyek',
        ]);

        // Template 3: Izin Operasional Bisnis Lengkap
        $template3 = PermitTemplate::create([
            'name' => 'Izin Operasional Bisnis Lengkap',
            'description' => 'Template lengkap untuk memulai operasional bisnis dari NIB sampai izin operasional',
            'use_case' => 'Cocok untuk: Startup bisnis, perusahaan baru, atau ekspansi bisnis yang memerlukan izin lengkap',
            'category' => 'business',
            'is_public' => true,
        ]);

        // Items untuk Template 3
        $t3_nib = PermitTemplateItem::create([
            'template_id' => $template3->id,
            'permit_type_id' => $nib->id,
            'sequence_order' => 1,
            'estimated_days' => 1,
            'estimated_cost' => 500000,
            'notes' => 'Harus pertama kali diurus',
        ]);

        $t3_pbg = PermitTemplateItem::create([
            'template_id' => $template3->id,
            'permit_type_id' => $pbg->id,
            'sequence_order' => 2,
            'estimated_days' => 30,
            'estimated_cost' => 15000000,
        ]);

        $t3_slf = PermitTemplateItem::create([
            'template_id' => $template3->id,
            'permit_type_id' => $slf->id,
            'sequence_order' => 3,
            'estimated_days' => 14,
            'estimated_cost' => 8000000,
        ]);

        $t3_izinLingkungan = PermitTemplateItem::create([
            'template_id' => $template3->id,
            'permit_type_id' => $izinLingkungan->id,
            'sequence_order' => 4,
            'estimated_days' => 30,
            'estimated_cost' => 5000000,
        ]);

        $t3_operasional = PermitTemplateItem::create([
            'template_id' => $template3->id,
            'permit_type_id' => $izinOperasional->id,
            'sequence_order' => 5,
            'is_goal_permit' => true,
            'estimated_days' => 14,
            'estimated_cost' => 7000000,
            'notes' => 'Izin final untuk mulai operasional',
        ]);

        // Dependencies untuk Template 3
        PermitTemplateDependency::create([
            'template_id' => $template3->id,
            'permit_item_id' => $t3_pbg->id,
            'depends_on_item_id' => $t3_nib->id,
            'dependency_type' => 'MANDATORY',
        ]);

        PermitTemplateDependency::create([
            'template_id' => $template3->id,
            'permit_item_id' => $t3_slf->id,
            'depends_on_item_id' => $t3_pbg->id,
            'dependency_type' => 'MANDATORY',
        ]);

        PermitTemplateDependency::create([
            'template_id' => $template3->id,
            'permit_item_id' => $t3_izinLingkungan->id,
            'depends_on_item_id' => $t3_nib->id,
            'dependency_type' => 'MANDATORY',
        ]);

        PermitTemplateDependency::create([
            'template_id' => $template3->id,
            'permit_item_id' => $t3_operasional->id,
            'depends_on_item_id' => $t3_slf->id,
            'dependency_type' => 'MANDATORY',
        ]);

        PermitTemplateDependency::create([
            'template_id' => $template3->id,
            'permit_item_id' => $t3_operasional->id,
            'depends_on_item_id' => $t3_izinLingkungan->id,
            'dependency_type' => 'MANDATORY',
        ]);

        $this->command->info('✅ Seeded 3 permit templates with items and dependencies!');
        $this->command->info('   - Template 1: UKL-UPL Pabrik/Industri (5 permits, 5 dependencies)');
        $this->command->info('   - Template 2: UKL-UPL Bangunan Komersial (3 permits, 2 dependencies)');
        $this->command->info('   - Template 3: Izin Operasional Bisnis Lengkap (5 permits, 5 dependencies)');
    }
}
