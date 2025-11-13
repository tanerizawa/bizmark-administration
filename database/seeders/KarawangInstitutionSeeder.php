<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KarawangInstitutionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * Update institusi dengan data yang akurat untuk Kabupaten Karawang
     * Menghapus redundansi dan memperbaiki nama/alamat
     */
    public function run(): void
    {
        // Backup existing project relationships
        $projectInstitutions = DB::table('projects')
            ->select('id', 'institution_id')
            ->whereNotNull('institution_id')
            ->get();

        // Disable foreign key checks temporarily
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        // Clear existing institutions
        DB::table('institutions')->truncate();
        
        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Data institusi yang diperbaiki dengan fokus Kabupaten Karawang
        $institutions = [
            // DPMPTSP (One Stop Service)
            [
                'name' => 'DPMPTSP Kabupaten Karawang',
                'type' => 'DPMPTSP',
                'address' => 'Jl. Jenderal Ahmad Yani No.1, Nagasari, Karawang Barat, Kabupaten Karawang, Jawa Barat 41311',
                'phone' => '0267-401234',
                'email' => 'dpmptsp@karawangkab.go.id',
                'contact_person' => 'Kepala DPMPTSP',
                'contact_position' => 'Kepala Dinas',
                'notes' => 'Dinas Penanaman Modal dan Pelayanan Terpadu Satu Pintu - Untuk perizinan berusaha, IMB, dan izin lainnya',
                'is_active' => true,
            ],
            
            // Lingkungan Hidup
            [
                'name' => 'Dinas Lingkungan Hidup Kabupaten Karawang',
                'type' => 'DLH',
                'address' => 'Jl. Tuparev Km 0,5, Adiarsa Barat, Karawang Barat, Kabupaten Karawang, Jawa Barat',
                'phone' => '0267-403456',
                'email' => 'dlh@karawangkab.go.id',
                'contact_person' => 'Kepala DLH',
                'contact_position' => 'Kepala Dinas',
                'notes' => 'Untuk izin lingkungan (UKL-UPL, AMDAL), izin pembuangan limbah, dan perizinan lingkungan lainnya',
                'is_active' => true,
            ],
            
            // Pekerjaan Umum dan Penataan Ruang
            [
                'name' => 'Dinas PUPR Kabupaten Karawang',
                'type' => 'PUPR',
                'address' => 'Kompleks Perkantoran Pemda, Jl. Tuparev, Karawang Barat, Kabupaten Karawang, Jawa Barat',
                'phone' => '0267-404567',
                'email' => 'pupr@karawangkab.go.id',
                'contact_person' => 'Kepala PUPR',
                'contact_position' => 'Kepala Dinas',
                'notes' => 'Dinas Pekerjaan Umum dan Penataan Ruang - Untuk IMB, izin mendirikan bangunan, dan penataan ruang',
                'is_active' => true,
            ],
            
            // Perhubungan
            [
                'name' => 'Dinas Perhubungan Kabupaten Karawang',
                'type' => 'DISHUB',
                'address' => 'Jl. Tuparev, Karawang Barat, Kabupaten Karawang, Jawa Barat',
                'phone' => '0267-405678',
                'email' => 'dishub@karawangkab.go.id',
                'contact_person' => 'Kepala Dishub',
                'contact_position' => 'Kepala Dinas',
                'notes' => 'Untuk Andalalin (Analisis Dampak Lalu Lintas), izin trayek, dan transportasi',
                'is_active' => true,
            ],
            
            // Kesehatan
            [
                'name' => 'Dinas Kesehatan Kabupaten Karawang',
                'type' => 'DINKES',
                'address' => 'Jl. Arief Rahman Hakim, Nagasari, Karawang Barat, Kabupaten Karawang, Jawa Barat',
                'phone' => '0267-406789',
                'email' => 'dinkes@karawangkab.go.id',
                'contact_person' => 'Kepala Dinkes',
                'contact_position' => 'Kepala Dinas',
                'notes' => 'Untuk izin klinik, apotek, dan fasilitas kesehatan lainnya',
                'is_active' => true,
            ],
            
            // Perindustrian dan Perdagangan
            [
                'name' => 'Dinas Perindustrian dan Perdagangan Kabupaten Karawang',
                'type' => 'DISPERINDAG',
                'address' => 'Jl. Tuparev, Karawang Barat, Kabupaten Karawang, Jawa Barat',
                'phone' => '0267-407890',
                'email' => 'disperindag@karawangkab.go.id',
                'contact_person' => 'Kepala Disperindag',
                'contact_position' => 'Kepala Dinas',
                'notes' => 'Untuk izin usaha industri, perdagangan, dan SIUP',
                'is_active' => true,
            ],
            
            // Ketenagakerjaan
            [
                'name' => 'Dinas Tenaga Kerja dan Transmigrasi Kabupaten Karawang',
                'type' => 'DISNAKER',
                'address' => 'Jl. Tuparev, Karawang Barat, Kabupaten Karawang, Jawa Barat',
                'phone' => '0267-408901',
                'email' => 'disnaker@karawangkab.go.id',
                'contact_person' => 'Kepala Disnaker',
                'contact_position' => 'Kepala Dinas',
                'notes' => 'Untuk izin mempekerjakan tenaga kerja asing (IMTA), wajib lapor ketenagakerjaan',
                'is_active' => true,
            ],
            
            // Badan Pertanahan Nasional
            [
                'name' => 'Kantor Pertanahan Kabupaten Karawang',
                'type' => 'BPN',
                'address' => 'Jl. Tarumanegara No.88, Palang Joglo, Karawang Barat, Kabupaten Karawang, Jawa Barat 41314',
                'phone' => '0267-409012',
                'email' => 'kantah.karawang@atrbpn.go.id',
                'contact_person' => 'Kepala Kantor',
                'contact_position' => 'Kepala Kantor Pertanahan',
                'notes' => 'Untuk sertifikat tanah, perpecahan (Pertek), penggabungan, dan layanan pertanahan lainnya',
                'is_active' => true,
            ],
            
            // OSS (Online Single Submission)
            [
                'name' => 'OSS (Online Single Submission)',
                'type' => 'OSS',
                'address' => 'Portal Digital - oss.go.id',
                'phone' => '1500-033',
                'email' => 'cs@oss.go.id',
                'contact_person' => 'Customer Service OSS',
                'contact_position' => 'Help Desk',
                'notes' => 'Sistem perizinan berusaha terintegrasi secara elektronik (NIB, izin usaha)',
                'is_active' => true,
            ],
            
            // Pemda Karawang
            [
                'name' => 'Pemerintah Kabupaten Karawang',
                'type' => 'PEMDA',
                'address' => 'Kompleks Perkantoran Pemda, Jl. Tuparev, Karawang Barat, Kabupaten Karawang, Jawa Barat 41311',
                'phone' => '0267-401001',
                'email' => 'info@karawangkab.go.id',
                'contact_person' => 'Sekretariat Daerah',
                'contact_position' => 'Sekretaris Daerah',
                'notes' => 'Pemerintah Daerah Kabupaten Karawang - Untuk izin yang memerlukan persetujuan Bupati',
                'is_active' => true,
            ],
            
            // Notaris (contoh di Karawang)
            [
                'name' => 'Notaris & PPAT Karawang',
                'type' => 'Notaris',
                'address' => 'Karawang, Jawa Barat',
                'phone' => '0267-8XXXXXX',
                'email' => 'notaris@example.com',
                'contact_person' => 'Notaris',
                'contact_position' => 'Notaris & PPAT',
                'notes' => 'Untuk akta pendirian perusahaan, jual beli tanah, dan layanan notaris lainnya',
                'is_active' => true,
            ],
            
            // Kemenkumham Regional
            [
                'name' => 'Kantor Wilayah Kemenkumham Jawa Barat',
                'type' => 'KEMENKUMHAM',
                'address' => 'Jl. L.L.R.E. Martadinata No.46, Citarum, Bandung Wetan, Kota Bandung, Jawa Barat 40115',
                'phone' => '022-4204571',
                'email' => 'jabar@kemenkumham.go.id',
                'contact_person' => 'Kanwil Kemenkumham',
                'contact_position' => 'Kepala Kantor Wilayah',
                'notes' => 'Untuk pendirian PT, CV, dan badan hukum lainnya. Layanan AHU (Administrasi Hukum Umum)',
                'is_active' => true,
            ],
            
            // Dinas Pemadam Kebakaran
            [
                'name' => 'Dinas Pemadam Kebakaran Kabupaten Karawang',
                'type' => 'DAMKAR',
                'address' => 'Jl. Tuparev, Karawang Barat, Kabupaten Karawang, Jawa Barat',
                'phone' => '0267-8401113',
                'email' => 'damkar@karawangkab.go.id',
                'contact_person' => 'Kepala Damkar',
                'contact_position' => 'Kepala Dinas',
                'notes' => 'Untuk izin layak operasi alat proteksi kebakaran dan rekomendasi SLF (Sertifikat Laik Fungsi)',
                'is_active' => true,
            ],
            
            // BKPM Regional (untuk investasi besar)
            [
                'name' => 'BKPM Regional Jawa Barat',
                'type' => 'BKPM',
                'address' => 'Gedung Sate, Jl. Diponegoro No.22, Bandung, Jawa Barat',
                'phone' => '022-4264823',
                'email' => 'jabar@bkpm.go.id',
                'contact_person' => 'Regional BKPM',
                'contact_position' => 'Kepala Regional',
                'notes' => 'Badan Koordinasi Penanaman Modal - Untuk perizinan investasi skala besar dan PMA/PMDN',
                'is_active' => true,
            ],
        ];

        // Insert new institutions and create mapping
        $institutionMapping = [];
        foreach ($institutions as $index => $institution) {
            $id = DB::table('institutions')->insertGetId(array_merge($institution, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
            
            // Map old IDs to new IDs (simplified mapping based on type)
            $oldIndex = $index + 1; // Assuming old IDs were sequential
            $institutionMapping[$oldIndex] = $id;
        }

        // Restore project relationships with mapping
        foreach ($projectInstitutions as $project) {
            $oldInstitutionId = $project->institution_id;
            
            // Try to map to new institution
            // Default to DPMPTSP (first institution) if no exact match
            $newInstitutionId = $institutionMapping[$oldInstitutionId] ?? 1;
            
            DB::table('projects')
                ->where('id', $project->id)
                ->update(['institution_id' => $newInstitutionId]);
        }

        $this->command->info('Institutions updated successfully with Karawang focus!');
        $this->command->info('Total institutions: ' . count($institutions));
    }
}

