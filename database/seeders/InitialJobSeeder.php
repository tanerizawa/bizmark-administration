<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\JobVacancy;
use Carbon\Carbon;

class InitialJobSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        JobVacancy::create([
            'title' => 'Drafter Dokumen Lingkungan & Teknis',
            'slug' => 'drafter-dokumen-lingkungan-teknis',
            'position' => 'Drafter Dokumen Teknis & Lingkungan',
            'description' => 'PT. Cangah Pajaratan Mandiri adalah perusahaan konsultan yang bergerak di bidang perizinan, dokumen lingkungan, dan kajian teknis. Kami mencari individu yang teliti, komunikatif, dan mampu bekerja dalam tim untuk bergabung sebagai Drafter Dokumen Lingkungan & Teknis.

Kami memberikan kesempatan bagi lulusan baru maupun profesional yang ingin mengembangkan karir di bidang penyusunan dokumen teknis dan lingkungan, dengan proyek yang variatif dan menantang.',
            
            'responsibilities' => json_encode([
                'Menyusun dokumen UKL-UPL, Kajian Lingkungan, dan Pertimbangan Teknis',
                'Mengumpulkan data lapangan dan melakukan pengolahan data teknis',
                'Menyusun laporan teknis dengan format yang rapi dan terstruktur',
                'Bekerja sama dengan tim teknis dan analis dalam penyusunan dokumen',
                'Memastikan kualitas format, layout, dan struktur dokumen sesuai standar',
                'Melakukan revisi dokumen berdasarkan feedback klien atau konsultan senior',
            ]),
            
            'qualifications' => json_encode([
                'Pendidikan minimal D3/S1 dari jurusan Teknik Lingkungan, Teknik Sipil, Planologi, atau bidang terkait',
                'Terampil dalam pengolahan dan drafting dokumen teknis',
                'Mampu menggunakan Microsoft Office (Word, Excel, PowerPoint) dengan baik',
                'Diutamakan memiliki pengalaman dalam menyusun dokumen UKL-UPL atau Kajian Teknis',
                'Teliti, komunikatif, dan mampu bekerja sesuai deadline',
                'Mampu bekerja secara individu maupun dalam tim',
                'Fresh graduate dipersilakan melamar',
            ]),
            
            'benefits' => json_encode([
                'Lingkungan kerja profesional dengan tim yang suportif',
                'Proyek yang variatif di bidang teknis dan lingkungan',
                'Kesempatan untuk meningkatkan kompetensi dalam penyusunan dokumen teknis',
                'Pelatihan dan bimbingan dari konsultan senior',
                'Gaji kompetitif sesuai pengalaman',
                'Jenjang karir yang jelas',
            ]),
            
            'employment_type' => 'full-time',
            'location' => 'Jakarta',
            'salary_min' => null, // Akan dibicarakan saat interview
            'salary_max' => null,
            'salary_negotiable' => true,
            'deadline' => Carbon::now()->addMonths(2), // Open untuk 2 bulan
            'status' => 'open',
            'google_form_url' => null, // Bisa diisi nanti kalau ada Google Form backup
            'applications_count' => 0,
        ]);

        $this->command->info('✅ Job vacancy "Drafter Dokumen Lingkungan & Teknis" created successfully!');
    }
}
