<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $projects = [
            [
                'name' => 'Perizinan IMB Gedung Perkantoran',
                'description' => 'Pengurusan Izin Mendirikan Bangunan untuk gedung perkantoran 8 lantai di kawasan bisnis Jakarta Selatan',
                'client_name' => 'PT Mega Prima Development',
                'client_contact' => '021-7654321 / mega.prima@email.com',
                'client_address' => 'Jl. Sudirman No. 123, Jakarta Selatan',
                'status_id' => 2, // In Progress
                'institution_id' => 1, // Dinas Tata Ruang dan Cipta Karya
                'start_date' => '2025-09-15',
                'deadline' => '2025-12-15',
                'progress_percentage' => 65,
                'budget' => 250000000,
                'actual_cost' => 150000000,
                'notes' => 'Dokumen teknis sudah diserahkan. Menunggu verifikasi lapangan dari dinas.',
            ],
            [
                'name' => 'Perizinan UKL-UPL Pabrik Tekstil',
                'description' => 'Pengurusan perizinan lingkungan UKL-UPL untuk pabrik tekstil dengan kapasitas produksi 500 ton/bulan',
                'client_name' => 'CV Tekstil Nusantara',
                'client_contact' => '0251-8765432 / tekstil.nusantara@email.com',
                'client_address' => 'Kawasan Industri Cibitung, Bekasi',
                'status_id' => 3, // Review
                'institution_id' => 2, // Dinas Lingkungan Hidup
                'start_date' => '2025-08-01',
                'deadline' => '2025-11-30',
                'progress_percentage' => 80,
                'budget' => 175000000,
                'actual_cost' => 140000000,
                'notes' => 'Dokumen UKL-UPL telah diserahkan. Sedang dalam tahap review teknis.',
            ],
            [
                'name' => 'Andalalin Mall Shopping Center',
                'description' => 'Analisis Dampak Lalu Lintas untuk pembangunan mall dengan luas 15.000 m2',
                'client_name' => 'PT Metropolitan Shopping',
                'client_contact' => '021-9876543 / metro.shopping@email.com',
                'client_address' => 'Jl. Raya Bogor KM 25, Depok',
                'status_id' => 1, // Planning
                'institution_id' => 3, // Dinas Perhubungan
                'start_date' => '2025-10-01',
                'deadline' => '2026-01-31',
                'progress_percentage' => 25,
                'budget' => 300000000,
                'actual_cost' => 75000000,
                'notes' => 'Survey traffic counting sudah dimulai. Koordinasi dengan Dishub untuk data sekunder.',
            ],
            [
                'name' => 'Sertifikat Halal Produk Makanan',
                'description' => 'Pengurusan sertifikat halal untuk 15 varian produk makanan olahan',
                'client_name' => 'UD Berkah Mandiri',
                'client_contact' => '0274-654321 / berkah.mandiri@email.com',
                'client_address' => 'Jl. Malioboro No. 45, Yogyakarta',
                'status_id' => 4, // Completed
                'institution_id' => 4, // BPOM
                'start_date' => '2025-07-01',
                'deadline' => '2025-09-30',
                'progress_percentage' => 100,
                'budget' => 85000000,
                'actual_cost' => 80000000,
                'notes' => 'Sertifikat halal telah diterbitkan untuk semua produk. Proses selesai tepat waktu.',
            ],
            [
                'name' => 'Perizinan OSS Startup Teknologi',
                'description' => 'Pengurusan izin usaha melalui Online Single Submission untuk perusahaan teknologi finansial',
                'client_name' => 'PT Digital Inovasi Indonesia',
                'client_contact' => '021-5432167 / digital.inovasi@email.com',
                'client_address' => 'BSD Green Office Park, Tangerang Selatan',
                'status_id' => 2, // In Progress
                'institution_id' => 5, // BKPM
                'start_date' => '2025-09-20',
                'deadline' => '2025-11-20',
                'progress_percentage' => 40,
                'budget' => 120000000,
                'actual_cost' => 48000000,
                'notes' => 'Berkas NIB sudah disubmit. Menunggu persetujuan dari kementerian terkait.',
            ],
            [
                'name' => 'KKPR Kawasan Industri',
                'description' => 'Kajian Keselamatan dan Kesehatan Kerja serta Perlindungan Radiasi untuk kawasan industri kimia',
                'client_name' => 'PT Industri Kimia Sejahtera',
                'client_contact' => '021-7891234 / kimia.sejahtera@email.com',
                'client_address' => 'Kawasan Industri Cilegon, Banten',
                'status_id' => 5, // On Hold
                'institution_id' => 1, // Dinas Tata Ruang dan Cipta Karya
                'start_date' => '2025-08-15',
                'deadline' => '2025-12-31',
                'progress_percentage' => 30,
                'budget' => 400000000,
                'actual_cost' => 120000000,
                'notes' => 'Proses ditunda karena menunggu hasil audit safety dari konsultan internasional.',
            ]
        ];

        foreach ($projects as $project) {
            \App\Models\Project::create($project);
        }

        // Create project logs for some projects
        $projectsWithLogs = \App\Models\Project::take(3)->get();
        
        foreach ($projectsWithLogs as $project) {
            \App\Models\ProjectLog::create([
                'project_id' => $project->id,
                'action' => 'created',
                'description' => "Proyek '{$project->name}' berhasil dibuat",
                'new_values' => $project->toArray(),
                'created_at' => $project->created_at,
            ]);

            \App\Models\ProjectLog::create([
                'project_id' => $project->id,
                'action' => 'status_changed',
                'description' => "Status proyek diubah menjadi '{$project->status->name}'",
                'new_values' => ['status_id' => $project->status_id],
                'created_at' => $project->created_at->addDays(1),
            ]);
        }
    }
}
