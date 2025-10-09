<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BestPracticeProjectStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * Update project statuses to follow best practice project management workflow
     * Menghapus status yang spesifik ke instansi (DLH, BPN, OSS, Notaris)
     * Menggantinya dengan status umum yang berlaku untuk semua jenis proyek
     */
    public function run(): void
    {
        // Backup existing project status relationships
        $projectStatuses = DB::table('projects')
            ->select('id', 'status_id')
            ->whereNotNull('status_id')
            ->get();

        // Disable foreign key checks temporarily
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        // Clear existing statuses
        DB::table('project_statuses')->truncate();
        
        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        /**
         * Best Practice Project Management Status Flow:
         * 1. Lead/Inquiry → 2. Penawaran → 3. Negosiasi → 4. Kontrak
         * → 5. Persiapan → 6. Dalam Pengerjaan → 7. Review
         * → 8. Menunggu Persetujuan → 9. Selesai → 10. Ditutup
         * 
         * Status tambahan: Ditunda, Dibatalkan
         */
        
        $statuses = [
            // 1. INQUIRY/LEAD STAGE
            [
                'name' => 'Lead',
                'code' => 'LEAD',
                'color' => '#94A3B8', // Slate
                'description' => 'Calon proyek/klien yang sedang dijajaki, belum ada komitmen resmi',
                'sort_order' => 1,
                'is_active' => true,
                'is_final' => false,
            ],
            
            // 2. PENAWARAN STAGE
            [
                'name' => 'Penawaran',
                'code' => 'PROPOSAL',
                'color' => '#3B82F6', // Blue
                'description' => 'Tahap penawaran proposal dan quotation ke klien',
                'sort_order' => 2,
                'is_active' => true,
                'is_final' => false,
            ],
            
            // 3. NEGOSIASI STAGE
            [
                'name' => 'Negosiasi',
                'code' => 'NEGOTIATION',
                'color' => '#8B5CF6', // Purple
                'description' => 'Negosiasi harga, scope, dan terms dengan klien',
                'sort_order' => 3,
                'is_active' => true,
                'is_final' => false,
            ],
            
            // 4. KONTRAK STAGE
            [
                'name' => 'Kontrak',
                'code' => 'CONTRACT',
                'color' => '#10B981', // Green
                'description' => 'Kontrak telah ditandatangani, proyek dikonfirmasi',
                'sort_order' => 4,
                'is_active' => true,
                'is_final' => false,
            ],
            
            // 5. PERSIAPAN STAGE
            [
                'name' => 'Persiapan',
                'code' => 'PREPARATION',
                'color' => '#F59E0B', // Amber
                'description' => 'Persiapan dokumen, tim, dan resources untuk eksekusi proyek',
                'sort_order' => 5,
                'is_active' => true,
                'is_final' => false,
            ],
            
            // 6. DALAM PENGERJAAN STAGE
            [
                'name' => 'Dalam Pengerjaan',
                'code' => 'IN_PROGRESS',
                'color' => '#0EA5E9', // Sky Blue
                'description' => 'Proyek sedang dalam tahap eksekusi/implementasi aktif',
                'sort_order' => 6,
                'is_active' => true,
                'is_final' => false,
            ],
            
            // 7. REVIEW/QC STAGE
            [
                'name' => 'Review',
                'code' => 'REVIEW',
                'color' => '#EC4899', // Pink
                'description' => 'Tahap review internal dan quality control sebelum submit',
                'sort_order' => 7,
                'is_active' => true,
                'is_final' => false,
            ],
            
            // 8. MENUNGGU PERSETUJUAN
            [
                'name' => 'Menunggu Persetujuan',
                'code' => 'WAITING_APPROVAL',
                'color' => '#F97316', // Orange
                'description' => 'Dokumen/deliverable sudah disubmit, menunggu approval dari klien atau instansi',
                'sort_order' => 8,
                'is_active' => true,
                'is_final' => false,
            ],
            
            // 9. REVISI
            [
                'name' => 'Revisi',
                'code' => 'REVISION',
                'color' => '#EAB308', // Yellow
                'description' => 'Ada feedback/revisi yang harus dikerjakan',
                'sort_order' => 9,
                'is_active' => true,
                'is_final' => false,
            ],
            
            // 10. SELESAI
            [
                'name' => 'Selesai',
                'code' => 'COMPLETED',
                'color' => '#22C55E', // Success Green
                'description' => 'Proyek berhasil diselesaikan, deliverable diterima',
                'sort_order' => 10,
                'is_active' => true,
                'is_final' => true,
            ],
            
            // 11. DITUTUP/CLOSED
            [
                'name' => 'Ditutup',
                'code' => 'CLOSED',
                'color' => '#059669', // Emerald (darker green)
                'description' => 'Proyek selesai dan ditutup secara administratif, invoice lunas',
                'sort_order' => 11,
                'is_active' => true,
                'is_final' => true,
            ],
            
            // SPECIAL STATUS - DITUNDA
            [
                'name' => 'Ditunda',
                'code' => 'ON_HOLD',
                'color' => '#6B7280', // Gray
                'description' => 'Proyek ditunda sementara karena berbagai alasan',
                'sort_order' => 98,
                'is_active' => true,
                'is_final' => false,
            ],
            
            // SPECIAL STATUS - DIBATALKAN
            [
                'name' => 'Dibatalkan',
                'code' => 'CANCELLED',
                'color' => '#EF4444', // Red
                'description' => 'Proyek dibatalkan dan tidak akan dilanjutkan',
                'sort_order' => 99,
                'is_active' => true,
                'is_final' => true,
            ],
        ];

        // Insert new statuses and create mapping
        $statusMapping = [
            1 => 2,   // Penawaran (old) → Penawaran (new ID 2)
            2 => 4,   // Kontrak → Kontrak (new ID 4)
            3 => 5,   // Pengumpulan Dokumen → Persiapan (new ID 5)
            4 => 6,   // Proses di DLH → Dalam Pengerjaan (new ID 6)
            5 => 6,   // Proses di BPN → Dalam Pengerjaan (new ID 6)
            6 => 6,   // Proses di OSS → Dalam Pengerjaan (new ID 6)
            7 => 6,   // Proses di Notaris → Dalam Pengerjaan (new ID 6)
            8 => 8,   // Menunggu Persetujuan → Menunggu Persetujuan (new ID 8)
            9 => 10,  // SK Terbit → Selesai (new ID 10)
            10 => 13, // Dibatalkan → Dibatalkan (new ID 13)
            11 => 12, // Ditunda → Ditunda (new ID 12)
        ];

        foreach ($statuses as $status) {
            DB::table('project_statuses')->insert(array_merge($status, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }

        // Restore project relationships with mapping
        foreach ($projectStatuses as $project) {
            $oldStatusId = $project->status_id;
            
            // Map old status to new status
            // Default to "Dalam Pengerjaan" (ID 6) if no exact match
            $newStatusId = $statusMapping[$oldStatusId] ?? 6;
            
            DB::table('projects')
                ->where('id', $project->id)
                ->update(['status_id' => $newStatusId]);
        }

        $this->command->info('Project statuses updated successfully with best practice workflow!');
        $this->command->info('Total statuses: ' . count($statuses));
        $this->command->info('Old statuses have been mapped to new general statuses.');
    }
}
