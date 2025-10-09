<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProjectStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $statuses = [
            [
                'name' => 'Penawaran',
                'code' => 'PENAWARAN',
                'description' => 'Tahap penawaran ke client',
                'color' => '#3B82F6',
                'sort_order' => 1,
                'is_final' => false,
            ],
            [
                'name' => 'Kontrak',
                'code' => 'KONTRAK', 
                'description' => 'Kontrak telah ditandatangani',
                'color' => '#10B981',
                'sort_order' => 2,
                'is_final' => false,
            ],
            [
                'name' => 'Pengumpulan Dokumen',
                'code' => 'PENGUMPULAN_DOK',
                'description' => 'Pengumpulan dan penyusunan dokumen',
                'color' => '#F59E0B',
                'sort_order' => 3,
                'is_final' => false,
            ],
            [
                'name' => 'Proses di DLH',
                'code' => 'PROSES_DLH',
                'description' => 'Dokumen sedang diproses di Dinas Lingkungan Hidup',
                'color' => '#8B5CF6',
                'sort_order' => 4,
                'is_final' => false,
            ],
            [
                'name' => 'Proses di BPN',
                'code' => 'PROSES_BPN',
                'description' => 'Dokumen sedang diproses di BPN',
                'color' => '#EC4899',
                'sort_order' => 5,
                'is_final' => false,
            ],
            [
                'name' => 'Proses di OSS',
                'code' => 'PROSES_OSS',
                'description' => 'Dokumen sedang diproses di OSS',
                'color' => '#06B6D4',
                'sort_order' => 6,
                'is_final' => false,
            ],
            [
                'name' => 'Proses di Notaris',
                'code' => 'PROSES_NOTARIS',
                'description' => 'Dokumen sedang diproses di Notaris',
                'color' => '#84CC16',
                'sort_order' => 7,
                'is_final' => false,
            ],
            [
                'name' => 'Menunggu Persetujuan',
                'code' => 'MENUNGGU_PERSETUJUAN',
                'description' => 'Menunggu persetujuan dari instansi terkait',
                'color' => '#F97316',
                'sort_order' => 8,
                'is_final' => false,
            ],
            [
                'name' => 'SK Terbit',
                'code' => 'SK_TERBIT',
                'description' => 'Surat Keputusan izin telah terbit',
                'color' => '#22C55E',
                'sort_order' => 9,
                'is_final' => true,
            ],
            [
                'name' => 'Dibatalkan',
                'code' => 'DIBATALKAN',
                'description' => 'Proyek dibatalkan',
                'color' => '#EF4444',
                'sort_order' => 10,
                'is_final' => true,
            ],
            [
                'name' => 'Ditunda',
                'code' => 'DITUNDA',
                'description' => 'Proyek ditunda sementara',
                'color' => '#6B7280',
                'sort_order' => 11,
                'is_final' => false,
            ]
        ];

        foreach ($statuses as $status) {
            \DB::table('project_statuses')->insert(array_merge($status, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}
