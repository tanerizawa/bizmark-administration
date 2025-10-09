<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Document;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Carbon\Carbon;

class DocumentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $projects = Project::all();
        $tasks = Task::all();
        $users = User::all();

        $documents = [
            [
                'title' => 'Dokumen IMB - Gambar Teknik',
                'file_path' => 'documents/sample_gambar_teknik.pdf',
                'file_name' => 'Gambar_Teknik_IMB_2024.pdf',
                'file_size' => '2480000',
                'mime_type' => 'application/pdf',
                'category' => 'teknis',
                'description' => 'Gambar teknik dan site plan untuk pengajuan IMB kawasan komersial. Termasuk denah, tampak, potongan, dan detail struktur.',
                'status' => 'draft',
                'version' => '1.0',
                'project_id' => $projects->where('name', 'Perizinan IMB Gedung Perkantoran')->first()?->id,
                'task_id' => $tasks->where('title', 'Persiapan Dokumen Teknis')->first()?->id,
                'uploaded_by' => $users->first()?->id,
                'created_at' => Carbon::now()->subDays(15),
                'updated_at' => Carbon::now()->subDays(15),
            ],
            [
                'title' => 'Dokumen UKL-UPL - Analisis Dampak',
                'file_path' => 'documents/sample_ukl_upl.pdf',
                'file_name' => 'Analisis_Dampak_Lingkungan.pdf',
                'file_size' => '1850000',
                'mime_type' => 'application/pdf',
                'category' => 'lingkungan',
                'description' => 'Dokumen analisis dampak lingkungan untuk izin UKL-UPL pembangunan pabrik. Mencakup analisis kualitas air, udara, dan pengelolaan limbah.',
                'status' => 'review',
                'version' => '2.1',
                'project_id' => $projects->where('name', 'Perizinan UKL-UPL Pabrik Tekstil')->first()?->id,
                'task_id' => $tasks->where('title', 'Penyusunan Dokumen UKL-UPL')->first()?->id,
                'uploaded_by' => $users->first()?->id,
                'created_at' => Carbon::now()->subDays(20),
                'updated_at' => Carbon::now()->subDays(5),
            ],
            [
                'title' => 'Survei Lalu Lintas - Andalalin',
                'file_path' => 'documents/sample_survei_lalin.xlsx',
                'file_name' => 'Data_Survei_Lalu_Lintas.xlsx',
                'file_size' => '680000',
                'mime_type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'category' => 'transportasi',
                'description' => 'Data hasil survei lalu lintas untuk analisis dampak lalin (Andalalin). Berisi counting kendaraan, waktu tempuh, dan analisis kapasitas jalan.',
                'status' => 'approved',
                'version' => '1.0',
                'project_id' => $projects->where('name', 'Andalalin Mall Shopping Center')->first()?->id,
                'task_id' => $tasks->where('title', 'Survei Lalu Lintas')->first()?->id,
                'uploaded_by' => $users->first()?->id,
                'created_at' => Carbon::now()->subDays(30),
                'updated_at' => Carbon::now()->subDays(10),
            ],
            [
                'title' => 'Peta Lokasi dan Aksesibilitas',
                'file_path' => 'documents/sample_peta_lokasi.jpg',
                'file_name' => 'Peta_Lokasi_Proyek.jpg',
                'file_size' => '1200000',
                'mime_type' => 'image/jpeg',
                'category' => 'teknis',
                'description' => 'Peta lokasi proyek dengan detail aksesibilitas, infrastruktur pendukung, dan radius dampak pembangunan.',
                'status' => 'draft',
                'version' => '1.0',
                'project_id' => $projects->first()?->id,
                'task_id' => $tasks->where('title', 'Survei Lokasi')->first()?->id,
                'uploaded_by' => $users->first()?->id,
                'created_at' => Carbon::now()->subDays(8),
                'updated_at' => Carbon::now()->subDays(3),
            ],
            [
                'title' => 'Surat Permohonan Resmi',
                'file_path' => 'documents/sample_surat_permohonan.docx',
                'file_name' => 'Surat_Permohonan_IMB.docx',
                'file_size' => '95000',
                'mime_type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'category' => 'administrasi',
                'description' => 'Surat permohonan resmi untuk pengajuan IMB yang telah ditandatangani direktur dan bermaterai. Format sesuai template instansi terkait.',
                'status' => 'approved',
                'version' => '1.0',
                'project_id' => $projects->where('name', 'Perizinan IMB Gedung Perkantoran')->first()?->id,
                'task_id' => $tasks->where('title', 'Pengajuan ke Instansi')->first()?->id,
                'uploaded_by' => $users->first()?->id,
                'created_at' => Carbon::now()->subDays(25),
                'updated_at' => Carbon::now()->subDays(18),
            ],
            [
                'title' => 'Laporan Monitoring Lingkungan',
                'file_path' => 'documents/sample_monitoring_lingkungan.pdf',
                'file_name' => 'Laporan_Monitoring_Q1_2024.pdf',
                'file_size' => '3200000',
                'mime_type' => 'application/pdf',
                'category' => 'lingkungan',
                'description' => 'Laporan monitoring dampak lingkungan kuartal 1 tahun 2024. Mencakup pemantauan kualitas air, udara, kebisingan, dan pengelolaan limbah.',
                'status' => 'review',
                'version' => '1.2',
                'project_id' => $projects->where('name', 'Perizinan UKL-UPL Pabrik Tekstil')->first()?->id,
                'task_id' => $tasks->where('title', 'Monitoring Implementasi')->first()?->id,
                'uploaded_by' => $users->first()?->id,
                'created_at' => Carbon::now()->subDays(12),
                'updated_at' => Carbon::now()->subDays(2),
            ]
        ];

        foreach ($documents as $document) {
            Document::create($document);
        }
    }
}
