<?php

namespace Database\Seeders;

use App\Models\Project;
use Illuminate\Database\Seeder;

class ImproveProjectNamingSeeder extends Seeder
{
    /**
     * Memperbaiki penamaan proyek yang kurang relevan dan informatif.
     * 
     * MASALAH:
     * 1. Banyak proyek dengan nama "Pekerjaan..." (redundant)
     * 2. 4 proyek dengan nama identik "Pekerjaan UKL UPL" (tidak distinguishable)
     * 3. Nama client di judul (tidak perlu, sudah ada client_id)
     * 4. Tidak spesifik tentang jenis kegiatan
     * 
     * POLA PENAMAAN BARU:
     * [Jenis Izin/Kegiatan] [Detail Spesifik] - [Client Short Name]
     * 
     * Contoh:
     * - "UKL-UPL Pabrik Industri - PT Asiacon"
     * - "TPS Limbah B3 - PT RAS"
     * - "Perpanjangan Kartu Pengawasan - PT RAS"
     */
    public function run(): void
    {
        $updates = [
            // Project 40: Kartu Pengawasan
            [
                'id' => 40,
                'old_name' => 'Pekerjaan Kartu Pengawasan PT RAS',
                'new_name' => 'Perpanjangan Kartu Pengawasan - PT RAS',
                'reason' => 'Hapus kata "Pekerjaan", client sudah ada di field terpisah',
                'new_description' => 'Perpanjangan Kartu Pengawasan (KPS) untuk fasilitas PT Rindu Alam Sejahtera',
            ],

            // Project 41: UKL-UPL dengan Pembangunan (has Pertek, Siteplan, PBG)
            [
                'id' => 41,
                'old_name' => 'Pekerjaan UKL UPL',
                'new_name' => 'UKL-UPL Pembangunan Pabrik - PT PJL',
                'reason' => 'Spesifik tentang pembangunan, singkat PT Putra Jaya Laksana',
                'new_description' => 'UKL-UPL untuk pembangunan pabrik baru termasuk Pertek BPN, Siteplan, dan PBG',
            ],

            // Project 42: UKL-UPL Budget Terbesar (180jt)
            [
                'id' => 42,
                'old_name' => 'Pekerjaan UKL UPL',
                'new_name' => 'UKL-UPL Pabrik Industri - PT Asiacon',
                'reason' => 'Budget terbesar, proyek industri skala besar',
                'new_description' => 'UKL-UPL untuk pabrik industri skala besar dengan budget Rp 180 juta',
            ],

            // Project 43: UKL-UPL + Uji Lab
            [
                'id' => 43,
                'old_name' => 'Pekerjaan UKL UPL',
                'new_name' => 'UKL-UPL + Uji Lab - PT Maulida',
                'reason' => 'Highlight uji lab sebagai pembeda',
                'new_description' => 'UKL-UPL termasuk pengujian laboratorium lingkungan',
            ],

            // Project 44: UKL-UPL dalam Negosiasi
            [
                'id' => 44,
                'old_name' => 'Pekerjaan UKL UPL',
                'new_name' => 'UKL-UPL (Negosiasi) - PT MCM',
                'reason' => 'Status negosiasi, PT Mega Corporindo Mandiri',
                'new_description' => 'UKL-UPL untuk PT Mega Corporindo Mandiri, saat ini dalam tahap negosiasi',
            ],

            // Project 45: Sistem Informasi
            [
                'id' => 45,
                'old_name' => 'Pengembangan Sistem Informasi Nusantara Group',
                'new_name' => 'Sistem Informasi Administrasi - Nusantara Group',
                'reason' => 'Lebih spesifik tentang jenis sistem (administrasi)',
                'new_description' => 'Pengembangan sistem informasi administrasi untuk Nusantara Group',
            ],

            // Project 46: Penyimpanan Limbah B3 (Budget terbesar 269.5jt, 8 permits!)
            [
                'id' => 46,
                'old_name' => 'Pekerjaan Penyimpanan Limbah B3',
                'new_name' => 'TPS Limbah B3 - PT RAS',
                'reason' => 'TPS = Tempat Penyimpanan Sementara (istilah teknis), hapus "Pekerjaan"',
                'new_description' => 'Pembangunan Tempat Penyimpanan Sementara (TPS) Limbah B3 dengan 8 izin terkait',
            ],

            // Project 47: Pemanfaatan Limbah B3
            [
                'id' => 47,
                'old_name' => 'Pekerjaan Pemanfaatan Limbah B3 PT RAS',
                'new_name' => 'Pemanfaatan Limbah B3 - PT RAS',
                'reason' => 'Hapus "Pekerjaan" dan client dari nama (redundant)',
                'new_description' => 'Pemanfaatan Limbah B3 untuk PT Rindu Alam Sejahtera',
            ],
        ];

        echo "\n🔧 MEMPERBAIKI PENAMAAN PROYEK\n";
        echo "================================\n\n";

        foreach ($updates as $update) {
            $project = Project::find($update['id']);
            
            if (!$project) {
                echo "⚠️  Project ID {$update['id']} tidak ditemukan, skip...\n\n";
                continue;
            }

            echo "📋 Project ID: {$update['id']}\n";
            echo "   Client: {$project->client->name}\n";
            echo "   Budget: Rp " . number_format($project->budget ?? 0, 0, ',', '.') . "\n";
            echo "   Permits: {$project->permits()->count()} izin\n";
            echo "\n";
            echo "   ❌ BEFORE: \"{$update['old_name']}\"\n";
            echo "   ✅ AFTER:  \"{$update['new_name']}\"\n";
            echo "   💡 Reason: {$update['reason']}\n";
            echo "\n";
            
            // Update project
            $project->update([
                'name' => $update['new_name'],
                'description' => $update['new_description'],
            ]);
            
            echo "   ✓ Updated successfully!\n";
            echo "   ---\n\n";
        }

        echo "\n";
        echo "🎉 SELESAI! Total {$this->countOf($updates)} proyek telah diperbaiki\n";
        echo "\n";
        echo "📊 SUMMARY PERUBAHAN:\n";
        echo "   • Removed \"Pekerjaan\" prefix (5 projects)\n";
        echo "   • Distinguished 4 identical \"UKL UPL\" projects\n";
        echo "   • Added client short names for clarity\n";
        echo "   • Used technical terms (TPS, KPS)\n";
        echo "   • Updated descriptions for context\n";
        echo "\n";
        echo "🔍 VERIFIKASI:\n";
        echo "   Silakan cek di /projects untuk melihat nama baru\n";
        echo "   Nama sekarang lebih informatif dan distinguishable!\n";
        echo "\n";
    }

    /**
     * Count array items
     */
    private function countOf(array $items): int
    {
        return count($items);
    }
}
