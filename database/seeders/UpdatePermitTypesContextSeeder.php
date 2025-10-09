<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PermitType;
use App\Models\Institution;

class UpdatePermitTypesContextSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * Memperbaiki data jenis izin agar institusi, waktu, dan biaya sesuai konteks
     * berdasarkan regulasi perizinan di Indonesia.
     */
    public function run(): void
    {
        // Get institutions
        $dlh = Institution::where('type', 'DLH')->first();
        $klhk = Institution::where('type', 'KLHK')->first();
        $dpmptsp = Institution::where('type', 'DPMPTSP')->first();
        $pupr = Institution::where('type', 'PUPR')->first();
        $dishub = Institution::where('type', 'DISHUB')->first();
        $bpm = Institution::where('type', 'BPN')->first();
        $oss = Institution::where('type', 'OSS')->first();
        $pemda = Institution::where('type', 'PEMDA')->first();
        $disperindag = Institution::where('type', 'DISPERINDAG')->first();

        // Data mapping yang benar untuk setiap jenis izin
        $permitUpdates = [
            // ENVIRONMENTAL PERMITS
            'AMDAL' => [
                'institution_id' => $klhk->id ?? null,
                'avg_processing_days' => 75, // Sesuai PP 22/2021
                'estimated_cost_min' => 50000000,
                'estimated_cost_max' => 200000000,
                'description' => 'Analisis Mengenai Dampak Lingkungan untuk kegiatan yang berdampak penting terhadap lingkungan. Dikeluarkan oleh Kementerian LHK untuk kegiatan strategis nasional atau lintas provinsi.',
            ],
            'UKL_UPL' => [
                'institution_id' => $dlh->id ?? null,
                'avg_processing_days' => 14, // Sesuai PP 22/2021
                'estimated_cost_min' => 5000000,
                'estimated_cost_max' => 15000000,
                'description' => 'Upaya Pengelolaan Lingkungan dan Upaya Pemantauan Lingkungan untuk kegiatan yang tidak berdampak penting terhadap lingkungan. Dikeluarkan oleh Dinas Lingkungan Hidup Kabupaten/Kota.',
            ],
            'SPPL' => [
                'institution_id' => $dlh->id ?? null,
                'avg_processing_days' => 7,
                'estimated_cost_min' => 500000,
                'estimated_cost_max' => 2000000,
                'description' => 'Surat Pernyataan Kesanggupan Pengelolaan dan Pemantauan Lingkungan untuk usaha mikro dan kecil. Dikeluarkan oleh Dinas Lingkungan Hidup setempat.',
            ],
            'IZIN_LINGKUNGAN' => [
                'institution_id' => $dlh->id ?? null,
                'avg_processing_days' => 30,
                'estimated_cost_min' => 3000000,
                'estimated_cost_max' => 10000000,
                'description' => 'Izin Lingkungan merupakan izin yang diberikan kepada setiap orang yang melakukan usaha dan/atau kegiatan yang wajib memiliki AMDAL atau UKL-UPL.',
            ],
            'IZIN_AIR_TANAH' => [
                'institution_id' => $dlh->id ?? null,
                'avg_processing_days' => 21,
                'estimated_cost_min' => 3000000,
                'estimated_cost_max' => 10000000,
                'description' => 'Izin untuk pengambilan dan pemanfaatan air tanah. Dikeluarkan oleh Dinas Lingkungan Hidup atau instansi terkait di tingkat kabupaten/kota.',
            ],
            'Lab' => [
                'institution_id' => $dlh->id ?? null,
                'avg_processing_days' => 7,
                'estimated_cost_min' => 2000000,
                'estimated_cost_max' => 8000000,
                'description' => 'Uji Laboratorium untuk analisis kualitas air, udara, tanah, dan limbah sesuai standar baku mutu lingkungan.',
            ],
            'PERTEK LB3' => [
                'institution_id' => $dlh->id ?? null,
                'avg_processing_days' => 30,
                'estimated_cost_min' => 10000000,
                'estimated_cost_max' => 30000000,
                'description' => 'Persetujuan Teknis Pengelolaan Limbah Bahan Berbahaya dan Beracun (B3). Diperlukan untuk kegiatan yang menghasilkan, mengumpulkan, mengangkut, atau memanfaatkan limbah B3.',
            ],
            'RINTEK LB3' => [
                'institution_id' => $dlh->id ?? null,
                'avg_processing_days' => 21,
                'estimated_cost_min' => 8000000,
                'estimated_cost_max' => 25000000,
                'description' => 'Rekomendasi Teknis Limbah B3 yang diperlukan sebelum mendapatkan izin pengelolaan limbah B3.',
            ],

            // LAND & SPATIAL PERMITS
            'PERTEK_BPN' => [
                'institution_id' => $bpm->id ?? null,
                'avg_processing_days' => 14,
                'estimated_cost_min' => 3000000,
                'estimated_cost_max' => 10000000,
                'description' => 'Persetujuan Teknis Pemetaan dari Badan Pertanahan Nasional untuk kegiatan pengukuran dan pemetaan tanah.',
            ],
            'SERTIFIKAT_TANAH' => [
                'institution_id' => $bpm->id ?? null,
                'avg_processing_days' => 60,
                'estimated_cost_min' => 5000000,
                'estimated_cost_max' => 20000000,
                'description' => 'Sertifikat Hak Atas Tanah yang dikeluarkan oleh Kantor Pertanahan/BPN sebagai bukti kepemilikan sah atas tanah.',
            ],
            'PKKPR' => [
                'institution_id' => $dpmptsp->id ?? null,
                'avg_processing_days' => 14,
                'estimated_cost_min' => 2000000,
                'estimated_cost_max' => 8000000,
                'description' => 'Persetujuan Kesesuaian Kegiatan Pemanfaatan Ruang. Diperlukan untuk memastikan kegiatan sesuai dengan Rencana Tata Ruang Wilayah (RTRW).',
            ],

            // BUILDING PERMITS
            'IMB' => [
                'institution_id' => $dpmptsp->id ?? null,
                'avg_processing_days' => 30,
                'estimated_cost_min' => 10000000,
                'estimated_cost_max' => 50000000,
                'description' => 'Izin Mendirikan Bangunan (sistem lama). Untuk bangunan yang dibangun sebelum tahun 2020.',
            ],
            'PBG' => [
                'institution_id' => $dpmptsp->id ?? null,
                'avg_processing_days' => 30,
                'estimated_cost_min' => 10000000,
                'estimated_cost_max' => 50000000,
                'description' => 'Persetujuan Bangunan Gedung menggantikan IMB sesuai UU Cipta Kerja. Diproses melalui sistem OSS RBA untuk kemudahan perizinan.',
            ],
            'SLF' => [
                'institution_id' => $dpmptsp->id ?? null,
                'avg_processing_days' => 14,
                'estimated_cost_min' => 5000000,
                'estimated_cost_max' => 20000000,
                'description' => 'Sertifikat Laik Fungsi bangunan gedung yang diterbitkan setelah bangunan selesai dan lulus uji kelayakan teknis.',
            ],
            'SITEPLAN' => [
                'institution_id' => $pupr->id ?? null,
                'avg_processing_days' => 14,
                'estimated_cost_min' => 3000000,
                'estimated_cost_max' => 10000000,
                'description' => 'Persetujuan Siteplan/Rencana Tapak dari Dinas PUPR untuk perencanaan tata letak bangunan dan infrastruktur.',
            ],

            // TRANSPORTATION PERMITS
            'ANDALALIN' => [
                'institution_id' => $dishub->id ?? null,
                'avg_processing_days' => 30,
                'estimated_cost_min' => 15000000,
                'estimated_cost_max' => 50000000,
                'description' => 'Analisis Dampak Lalu Lintas diperlukan untuk pembangunan yang berpotensi menimbulkan dampak lalu lintas. Dikeluarkan oleh Dinas Perhubungan.',
            ],
            'IZIN_TRAYEK' => [
                'institution_id' => $dishub->id ?? null,
                'avg_processing_days' => 21,
                'estimated_cost_min' => 5000000,
                'estimated_cost_max' => 15000000,
                'description' => 'Izin Trayek untuk angkutan umum atau kendaraan operasional perusahaan. Dikeluarkan oleh Dinas Perhubungan.',
            ],

            // BUSINESS PERMITS
            'NIB' => [
                'institution_id' => $oss->id ?? null,
                'avg_processing_days' => 1,
                'estimated_cost_min' => 0,
                'estimated_cost_max' => 500000,
                'description' => 'Nomor Induk Berusaha merupakan identitas pelaku usaha yang diterbitkan melalui sistem OSS. Gratis dan dapat diproses secara online.',
            ],
            'TDP' => [
                'institution_id' => $oss->id ?? null,
                'avg_processing_days' => 3,
                'estimated_cost_min' => 0,
                'estimated_cost_max' => 1000000,
                'description' => 'Tanda Daftar Perusahaan (sudah terintegrasi dalam NIB sejak sistem OSS). Dikeluarkan otomatis melalui sistem OSS.',
            ],
            'SIUP' => [
                'institution_id' => $oss->id ?? null,
                'avg_processing_days' => 3,
                'estimated_cost_min' => 0,
                'estimated_cost_max' => 2000000,
                'description' => 'Surat Izin Usaha Perdagangan (sudah terintegrasi dalam NIB). Diterbitkan otomatis melalui sistem OSS berdasarkan KBLI.',
            ],
            'TDI' => [
                'institution_id' => $oss->id ?? null,
                'avg_processing_days' => 7,
                'estimated_cost_min' => 1000000,
                'estimated_cost_max' => 5000000,
                'description' => 'Tanda Daftar Industri untuk kegiatan industri. Diproses melalui sistem OSS dengan verifikasi dari Dinas Perindustrian.',
            ],
            'IZIN_OPERASIONAL' => [
                'institution_id' => $oss->id ?? null,
                'avg_processing_days' => 14,
                'estimated_cost_min' => 3000000,
                'estimated_cost_max' => 15000000,
                'description' => 'Izin Operasional/Komersial yang diperlukan setelah konstruksi selesai untuk memulai kegiatan usaha.',
            ],

            // OTHER PERMITS
            'IZIN_HO' => [
                'institution_id' => $dpmptsp->id ?? null,
                'avg_processing_days' => 14,
                'estimated_cost_min' => 2000000,
                'estimated_cost_max' => 10000000,
                'description' => 'Izin Gangguan/HO untuk kegiatan usaha yang berpotensi menimbulkan gangguan lingkungan. Diproses melalui DPMPTSP setempat.',
            ],
            'IZIN_REKLAME' => [
                'institution_id' => $dpmptsp->id ?? null,
                'avg_processing_days' => 7,
                'estimated_cost_min' => 1000000,
                'estimated_cost_max' => 5000000,
                'description' => 'Izin pemasangan reklame/papan nama usaha. Dikeluarkan oleh DPMPTSP atau Satpol PP setempat.',
            ],
        ];

        // Update setiap permit type
        foreach ($permitUpdates as $code => $data) {
            $permitType = PermitType::where('code', $code)->first();
            
            if ($permitType) {
                $permitType->update($data);
                $this->command->info("✅ Updated: {$permitType->name}");
            } else {
                $this->command->warn("⚠️ Permit type not found: {$code}");
            }
        }

        $this->command->info("\n🎉 Permit types context update completed!");
        $this->command->info("Total updated: " . count($permitUpdates) . " permit types");
    }
}
