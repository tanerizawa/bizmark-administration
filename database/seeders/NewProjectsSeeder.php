<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Project;
use App\Models\Institution;
use Carbon\Carbon;

class NewProjectsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get main institution (DPMPTSP as default)
        $dpmptsp = Institution::where('name', 'DPMPTSP Kabupaten Karawang')->first();
        $dlh = Institution::where('name', 'LIKE', '%Lingkungan Hidup%')->first();
        $klhk = Institution::where('name', 'LIKE', '%Kementerian Lingkungan%')->first();
        
        $projects = [
            [
                'name' => 'Pemanfaatan Limbah B3 - PT RAS',
                'description' => 'Pemanfaatan Limbah B3 untuk PT Rindu Alam Sejahtera',
                'client_name' => 'PT RINDU ALAM SEJAHTERA',
                'client_contact' => '021-8988-XXXX',
                'client_address' => 'Kawasan Industri Karawang, Jawa Barat',
                'status_id' => 1, // lead
                'institution_id' => $klhk ? $klhk->id : $dpmptsp->id,
                'start_date' => Carbon::now(),
                'deadline' => null,
                'budget' => 0,
                'contract_value' => 0,
                'notes' => 'Proyek pemanfaatan limbah B3. Status: Lead - belum ada komitmen.',
            ],
            [
                'name' => 'TPS Limbah B3 - PT RAS',
                'description' => 'Pembangunan Tempat Penyimpanan Sementara (TPS) Limbah B3 dengan 8 izin terkait',
                'client_name' => 'PT RINDU ALAM SEJAHTERA',
                'client_contact' => '021-8988-XXXX',
                'client_address' => 'Kawasan Industri Karawang, Jawa Barat',
                'status_id' => 2, // quotation
                'institution_id' => $dlh ? $dlh->id : $dpmptsp->id,
                'start_date' => Carbon::now(),
                'deadline' => Carbon::create(2025, 12, 1),
                'budget' => 0,
                'contract_value' => 0,
                'notes' => 'Pembangunan TPS Limbah B3 dengan 8 izin terkait. Target deadline: 01 Des 2025. Status: Penawaran telah dikirim.',
            ],
            [
                'name' => 'Sistem Informasi Administrasi - Nusantara Group',
                'description' => 'Pengembangan sistem informasi administrasi untuk Nusantara Group',
                'client_name' => 'NUSANTARA GROUP',
                'client_contact' => '021-5XXX-XXXX',
                'client_address' => 'Jakarta Selatan, DKI Jakarta',
                'status_id' => 4, // in_progress
                'institution_id' => Institution::where('name', 'LIKE', '%Team IT%')->first()?->id ?? $dpmptsp->id,
                'start_date' => Carbon::now()->subDays(30),
                'deadline' => Carbon::create(2025, 12, 31),
                'budget' => 0,
                'contract_value' => 0,
                'notes' => 'Pengembangan sistem informasi administrasi. Status: Dalam Pengerjaan.',
            ],
            [
                'name' => 'UKL-UPL (Negosiasi) - PT MCM',
                'description' => 'UKL-UPL untuk PT Mega Corporindo Mandiri, saat ini dalam tahap negosiasi',
                'client_name' => 'PT MEGA CORPORINDO MANDIRI',
                'client_contact' => '021-8XXX-XXXX',
                'client_address' => 'Kawasan Industri MM2100, Bekasi, Jawa Barat',
                'status_id' => 3, // negotiation
                'institution_id' => $dlh ? $dlh->id : $dpmptsp->id,
                'start_date' => Carbon::now(),
                'deadline' => Carbon::create(2025, 10, 31),
                'budget' => 0,
                'contract_value' => 0,
                'notes' => 'UKL-UPL dalam tahap negosiasi harga dan scope. Target: 31 Okt 2025.',
            ],
            [
                'name' => 'UKL-UPL + Uji Lab - PT Maulida',
                'description' => 'UKL-UPL termasuk pengujian laboratorium lingkungan',
                'client_name' => 'PT MAULIDA',
                'client_contact' => '0267-XXXX-XXXX',
                'client_address' => 'Karawang, Jawa Barat',
                'status_id' => 5, // preparation
                'institution_id' => $dlh ? $dlh->id : $dpmptsp->id,
                'start_date' => Carbon::now(),
                'deadline' => Carbon::create(2025, 12, 19),
                'budget' => 0,
                'contract_value' => 0,
                'notes' => 'UKL-UPL dengan pengujian laboratorium. Status: Persiapan dokumen awal.',
            ],
            [
                'name' => 'UKL-UPL Pabrik Industri - PT Asiacon',
                'description' => 'UKL-UPL untuk pabrik industri skala besar dengan budget Rp 180 juta',
                'client_name' => 'PT ASIACON',
                'client_contact' => '021-8XXX-XXXX',
                'client_address' => 'Cikarang, Bekasi, Jawa Barat',
                'status_id' => 5, // preparation
                'institution_id' => $dlh ? $dlh->id : $dpmptsp->id,
                'start_date' => Carbon::now(),
                'deadline' => Carbon::create(2025, 12, 5),
                'budget' => 180000000, // Rp 180 juta
                'contract_value' => 180000000,
                'notes' => 'UKL-UPL pabrik industri skala besar. Budget: Rp 180.000.000. Target: 05 Des 2025.',
            ],
            [
                'name' => 'UKL-UPL Pembangunan Perumahan - PT PJL',
                'description' => 'UKL-UPL untuk pembangunan pabrik baru termasuk Pertek BPN, Siteplan, dan PBG',
                'client_name' => 'PT PUTRA JAYA LAKSANA',
                'client_contact' => '0267-XXXX-XXXX',
                'client_address' => 'Karawang, Jawa Barat',
                'status_id' => 7, // revision
                'institution_id' => $dlh ? $dlh->id : $dpmptsp->id,
                'start_date' => Carbon::now()->subDays(20),
                'deadline' => Carbon::create(2025, 10, 24),
                'budget' => 0,
                'contract_value' => 0,
                'notes' => 'UKL-UPL pembangunan pabrik + Pertek BPN + Siteplan + PBG. Status: Revisi dari instansi. Deadline mendesak: 24 Okt 2025.',
            ],
            [
                'name' => 'Perpanjangan Kartu Pengawasan - PT RAS',
                'description' => 'Perpanjangan Kartu Pengawasan (KPS) untuk fasilitas PT Rindu Alam Sejahtera',
                'client_name' => 'PT RINDU ALAM SEJAHTERA',
                'client_contact' => '021-8988-XXXX',
                'client_address' => 'Kawasan Industri Karawang, Jawa Barat',
                'status_id' => 6, // waiting_approval
                'institution_id' => $dlh ? $dlh->id : $dpmptsp->id,
                'start_date' => Carbon::now()->subDays(10),
                'deadline' => Carbon::create(2025, 10, 17),
                'budget' => 0,
                'contract_value' => 0,
                'notes' => 'Perpanjangan Kartu Pengawasan (KPS). Status: Menunggu Persetujuan dari instansi. Target: 17 Okt 2025.',
            ],
        ];

        foreach ($projects as $projectData) {
            Project::create($projectData);
            $this->command->info("✅ Created project: {$projectData['name']}");
        }

        $this->command->info("\n🎉 Successfully created 8 new projects!");
    }
}
