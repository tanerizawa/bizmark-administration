<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Task;
use App\Models\Project;
use App\Models\User;
use App\Models\Institution;

class TaskSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $projects = Project::all();
        $users = User::all();
        $institutions = Institution::all();

        $tasks = [
            [
                'project_id' => $projects->first()->id,
                'title' => 'Persiapan Dokumen Persyaratan',
                'description' => 'Mengumpulkan dan mempersiapkan semua dokumen persyaratan untuk pengajuan IMB',
                'sop_notes' => "1. Surat permohonan\n2. KTP pemohon\n3. Surat kepemilikan tanah\n4. Gambar arsitektur\n5. Perhitungan struktur",
                'assigned_user_id' => $users->random()->id,
                'due_date' => now()->addDays(7),
                'status' => 'todo',
                'priority' => 'high',
                'institution_id' => $institutions->random()->id,
                'estimated_hours' => 16,
                'sort_order' => 1,
            ],
            [
                'project_id' => $projects->first()->id,
                'title' => 'Konsultasi dengan Arsitek',
                'description' => 'Melakukan konsultasi teknis dengan arsitek untuk finalisasi desain',
                'assigned_user_id' => $users->random()->id,
                'due_date' => now()->addDays(5),
                'status' => 'in_progress',
                'priority' => 'normal',
                'estimated_hours' => 8,
                'started_at' => now()->subDays(2),
                'sort_order' => 2,
            ],
            [
                'project_id' => $projects->skip(1)->first()->id,
                'title' => 'Analisis Dampak Lingkungan',
                'description' => 'Melakukan analisis dampak lingkungan untuk proses UKL-UPL',
                'sop_notes' => "1. Survey lokasi\n2. Analisis dampak air\n3. Analisis dampak udara\n4. Rencana pengelolaan\n5. Rencana pemantauan",
                'assigned_user_id' => $users->random()->id,
                'due_date' => now()->addDays(14),
                'status' => 'todo',
                'priority' => 'urgent',
                'institution_id' => $institutions->random()->id,
                'estimated_hours' => 40,
                'sort_order' => 1,
            ],
            [
                'project_id' => $projects->skip(1)->first()->id,
                'title' => 'Pengajuan ke DLHK',
                'description' => 'Pengajuan dokumen UKL-UPL ke Dinas Lingkungan Hidup dan Kehutanan',
                'assigned_user_id' => $users->random()->id,
                'due_date' => now()->addDays(21),
                'status' => 'blocked',
                'priority' => 'high',
                'institution_id' => $institutions->where('name', 'LIKE', '%Lingkungan%')->first()?->id ?? $institutions->random()->id,
                'estimated_hours' => 4,
                'sort_order' => 2,
            ],
            [
                'project_id' => $projects->skip(2)->first()->id,
                'title' => 'Studi Lalu Lintas',
                'description' => 'Melakukan studi analisis dampak lalu lintas untuk pembangunan mall',
                'sop_notes' => "1. Survey volume lalu lintas\n2. Analisis kapasitas jalan\n3. Prediksi dampak\n4. Rencana mitigasi\n5. Rekomendasi",
                'assigned_user_id' => $users->random()->id,
                'due_date' => now()->addDays(10),
                'status' => 'in_progress',
                'priority' => 'high',
                'estimated_hours' => 32,
                'started_at' => now()->subDays(3),
                'actual_hours' => 12,
                'sort_order' => 1,
            ],
            [
                'project_id' => $projects->skip(2)->first()->id,
                'title' => 'Koordinasi dengan Dishub',
                'description' => 'Koordinasi dan konsultasi dengan Dinas Perhubungan terkait analisis lalu lintas',
                'assigned_user_id' => $users->random()->id,
                'due_date' => now()->addDays(15),
                'status' => 'todo',
                'priority' => 'normal',
                'institution_id' => $institutions->where('name', 'LIKE', '%Perhubungan%')->first()?->id ?? $institutions->random()->id,
                'estimated_hours' => 6,
                'sort_order' => 2,
            ],
            [
                'project_id' => $projects->first()->id,
                'title' => 'Pengajuan ke DPMPTSP',
                'description' => 'Pengajuan berkas IMB ke Dinas Penanaman Modal dan Pelayanan Terpadu Satu Pintu',
                'assigned_user_id' => $users->random()->id,
                'due_date' => now()->subDays(2), // Overdue task
                'status' => 'todo',
                'priority' => 'urgent',
                'institution_id' => $institutions->where('name', 'LIKE', '%DPMPTSP%')->first()?->id ?? $institutions->random()->id,
                'estimated_hours' => 3,
                'sort_order' => 3,
            ],
            [
                'project_id' => $projects->skip(1)->first()->id,
                'title' => 'Finalisasi Dokumen UKL-UPL',
                'description' => 'Finalisasi dan review dokumen UKL-UPL sebelum pengajuan',
                'assigned_user_id' => $users->random()->id,
                'due_date' => now()->addDays(3),
                'status' => 'done',
                'priority' => 'normal',
                'estimated_hours' => 6,
                'actual_hours' => 5,
                'started_at' => now()->subDays(5),
                'completed_at' => now()->subDays(1),
                'completion_notes' => 'Dokumen telah selesai direview dan siap untuk pengajuan. Semua persyaratan teknis telah dipenuhi.',
                'sort_order' => 3,
            ],
        ];

        foreach ($tasks as $taskData) {
            Task::create($taskData);
        }

        $this->command->info('Task seeder completed successfully!');
    }
}
