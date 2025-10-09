<?php

namespace Database\Seeders;

use App\Models\PermitType;
use App\Models\Institution;
use Illuminate\Database\Seeder;

class PermitTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get institutions (will create if not exist)
        $dlh = Institution::firstOrCreate(
            ['name' => 'Dinas Lingkungan Hidup'],
            ['type' => 'government', 'address' => '-', 'phone' => '-', 'email' => '-']
        );
        
        $bpn = Institution::firstOrCreate(
            ['name' => 'Badan Pertanahan Nasional (BPN)'],
            ['type' => 'government', 'address' => '-', 'phone' => '-', 'email' => '-']
        );
        
        $dpmptsp = Institution::firstOrCreate(
            ['name' => 'DPMPTSP (Dinas Penanaman Modal dan PTSP)'],
            ['type' => 'government', 'address' => '-', 'phone' => '-', 'email' => '-']
        );
        
        $dpu = Institution::firstOrCreate(
            ['name' => 'Dinas Pekerjaan Umum'],
            ['type' => 'government', 'address' => '-', 'phone' => '-', 'email' => '-']
        );
        
        $dishub = Institution::firstOrCreate(
            ['name' => 'Dinas Perhubungan'],
            ['type' => 'government', 'address' => '-', 'phone' => '-', 'email' => '-']
        );

        $permits = [
            // ENVIRONMENTAL PERMITS
            [
                'name' => 'UKL-UPL',
                'code' => 'UKL_UPL',
                'category' => 'environmental',
                'institution_id' => $dlh->id,
                'avg_processing_days' => 14,
                'description' => 'Upaya Pengelolaan Lingkungan Hidup dan Upaya Pemantauan Lingkungan Hidup',
                'required_documents' => [
                    'Formulir UKL-UPL',
                    'Profil Perusahaan',
                    'Akta Pendirian Perusahaan',
                    'NPWP',
                    'Surat Pernyataan Kesanggupan',
                    'Siteplan',
                    'Dokumen Lingkungan'
                ],
                'estimated_cost_min' => 5000000,
                'estimated_cost_max' => 15000000,
            ],
            [
                'name' => 'AMDAL',
                'code' => 'AMDAL',
                'category' => 'environmental',
                'institution_id' => $dlh->id,
                'avg_processing_days' => 75,
                'description' => 'Analisis Mengenai Dampak Lingkungan',
                'required_documents' => [
                    'KA-ANDAL (Kerangka Acuan)',
                    'ANDAL (Analisis Dampak Lingkungan)',
                    'RKL (Rencana Kelola Lingkungan)',
                    'RPL (Rencana Pemantauan Lingkungan)',
                    'Profil Perusahaan',
                    'Siteplan Detail'
                ],
                'estimated_cost_min' => 50000000,
                'estimated_cost_max' => 200000000,
            ],
            [
                'name' => 'SPPL',
                'code' => 'SPPL',
                'category' => 'environmental',
                'institution_id' => $dlh->id,
                'avg_processing_days' => 7,
                'description' => 'Surat Pernyataan Pengelolaan Lingkungan Hidup',
                'required_documents' => [
                    'Formulir SPPL',
                    'KTP Direktur',
                    'NPWP',
                    'Surat Pernyataan bermaterai'
                ],
                'estimated_cost_min' => 500000,
                'estimated_cost_max' => 2000000,
            ],
            [
                'name' => 'Izin Lingkungan',
                'code' => 'IZIN_LINGKUNGAN',
                'category' => 'environmental',
                'institution_id' => $dlh->id,
                'avg_processing_days' => 30,
                'description' => 'Izin yang diberikan kepada setiap orang yang melakukan usaha dan/atau kegiatan yang wajib AMDAL atau UKL-UPL',
                'required_documents' => [
                    'Dokumen AMDAL atau UKL-UPL yang telah disetujui',
                    'Profil Perusahaan',
                    'Rekomendasi dari instansi terkait'
                ],
                'estimated_cost_min' => 3000000,
                'estimated_cost_max' => 10000000,
            ],

            // LAND & PROPERTY PERMITS
            [
                'name' => 'Pertek BPN (Pemetaan)',
                'code' => 'PERTEK_BPN',
                'category' => 'land',
                'institution_id' => $bpn->id,
                'avg_processing_days' => 14,
                'description' => 'Pertimbangan Teknis Pertanahan dari BPN',
                'required_documents' => [
                    'Surat Permohonan',
                    'Sertifikat Tanah',
                    'Peta Lokasi',
                    'Siteplan',
                    'KTP & NPWP',
                    'Akta Perusahaan'
                ],
                'estimated_cost_min' => 3000000,
                'estimated_cost_max' => 10000000,
            ],
            [
                'name' => 'Sertifikat Tanah',
                'code' => 'SERTIFIKAT_TANAH',
                'category' => 'land',
                'institution_id' => $bpn->id,
                'avg_processing_days' => 60,
                'description' => 'Surat tanda bukti hak atas tanah',
                'required_documents' => [
                    'Surat Ukur',
                    'Surat Keterangan Tanah',
                    'KTP & KK',
                    'SPPT PBB',
                    'Surat Pernyataan Penguasaan Fisik'
                ],
                'estimated_cost_min' => 5000000,
                'estimated_cost_max' => 20000000,
            ],

            // BUILDING PERMITS
            [
                'name' => 'IMB (Izin Mendirikan Bangunan)',
                'code' => 'IMB',
                'category' => 'building',
                'institution_id' => $dpmptsp->id,
                'avg_processing_days' => 30,
                'description' => 'Izin untuk mendirikan bangunan (sudah diganti PBG di beberapa daerah)',
                'required_documents' => [
                    'Gambar Rencana Arsitektur',
                    'Gambar Rencana Struktur',
                    'Gambar Rencana Utilitas',
                    'Sertifikat Tanah',
                    'Siteplan',
                    'Pernyataan Pengawas',
                    'KTP & NPWP'
                ],
                'estimated_cost_min' => 10000000,
                'estimated_cost_max' => 50000000,
            ],
            [
                'name' => 'PBG (Persetujuan Bangunan Gedung)',
                'code' => 'PBG',
                'category' => 'building',
                'institution_id' => $dpmptsp->id,
                'avg_processing_days' => 30,
                'description' => 'Pengganti IMB sesuai UU Cipta Kerja',
                'required_documents' => [
                    'Dokumen Rencana Teknis',
                    'Data Pemilik Bangunan',
                    'Status Hak Tanah',
                    'Persyaratan Administratif'
                ],
                'estimated_cost_min' => 10000000,
                'estimated_cost_max' => 50000000,
            ],
            [
                'name' => 'SLF (Sertifikat Laik Fungsi)',
                'code' => 'SLF',
                'category' => 'building',
                'institution_id' => $dpmptsp->id,
                'avg_processing_days' => 14,
                'description' => 'Sertifikat yang menyatakan bangunan layak fungsi',
                'required_documents' => [
                    'PBG/IMB',
                    'Laporan Hasil Pengawasan',
                    'Hasil Audit Struktur',
                    'Hasil Audit Kebakaran',
                    'As Built Drawing'
                ],
                'estimated_cost_min' => 5000000,
                'estimated_cost_max' => 20000000,
            ],
            [
                'name' => 'Siteplan',
                'code' => 'SITEPLAN',
                'category' => 'building',
                'institution_id' => $dpu->id,
                'avg_processing_days' => 7,
                'description' => 'Rencana tapak/denah lokasi bangunan',
                'required_documents' => [
                    'Sertifikat Tanah',
                    'Gambar Situasi',
                    'Peta Lokasi',
                    'Data Koordinat'
                ],
                'estimated_cost_min' => 2000000,
                'estimated_cost_max' => 8000000,
            ],

            // TRANSPORTATION PERMITS
            [
                'name' => 'Andalalin (Analisis Dampak Lalu Lintas)',
                'code' => 'ANDALALIN',
                'category' => 'transportation',
                'institution_id' => $dishub->id,
                'avg_processing_days' => 30,
                'description' => 'Analisis dampak pembangunan terhadap lalu lintas',
                'required_documents' => [
                    'Dokumen Andalalin',
                    'Survey Lalu Lintas',
                    'Peta Lokasi',
                    'Siteplan',
                    'Data Bangkitan Perjalanan'
                ],
                'estimated_cost_min' => 15000000,
                'estimated_cost_max' => 50000000,
            ],
            [
                'name' => 'Izin Trayek',
                'code' => 'IZIN_TRAYEK',
                'category' => 'transportation',
                'institution_id' => $dishub->id,
                'avg_processing_days' => 21,
                'description' => 'Izin penyelenggaraan angkutan umum pada trayek tertentu',
                'required_documents' => [
                    'Proposal Pengajuan Trayek',
                    'Hasil Survei',
                    'Rekomendasi Organda',
                    'KIR Kendaraan',
                    'STNK & BPKB'
                ],
                'estimated_cost_min' => 5000000,
                'estimated_cost_max' => 15000000,
            ],

            // BUSINESS PERMITS
            [
                'name' => 'NIB (Nomor Induk Berusaha)',
                'code' => 'NIB',
                'category' => 'business',
                'institution_id' => $dpmptsp->id,
                'avg_processing_days' => 1,
                'description' => 'Identitas pelaku usaha yang diterbitkan oleh OSS',
                'required_documents' => [
                    'Akta Pendirian Perusahaan',
                    'KTP Direktur',
                    'NPWP Perusahaan',
                    'Email Aktif'
                ],
                'estimated_cost_min' => 0,
                'estimated_cost_max' => 1000000,
            ],
            [
                'name' => 'TDP (Tanda Daftar Perusahaan)',
                'code' => 'TDP',
                'category' => 'business',
                'institution_id' => $dpmptsp->id,
                'avg_processing_days' => 7,
                'description' => 'Bukti bahwa perusahaan telah melakukan pendaftaran (sudah terintegrasi NIB)',
                'required_documents' => [
                    'Akta Pendirian',
                    'SK Kemenkumham',
                    'NPWP',
                    'KTP Pengurus'
                ],
                'estimated_cost_min' => 500000,
                'estimated_cost_max' => 2000000,
            ],
            [
                'name' => 'SIUP (Surat Izin Usaha Perdagangan)',
                'code' => 'SIUP',
                'category' => 'business',
                'institution_id' => $dpmptsp->id,
                'avg_processing_days' => 7,
                'description' => 'Izin untuk melakukan kegiatan perdagangan (sudah terintegrasi NIB)',
                'required_documents' => [
                    'Akta Pendirian',
                    'NPWP',
                    'Surat Keterangan Domisili',
                    'Pas Foto Direktur'
                ],
                'estimated_cost_min' => 1000000,
                'estimated_cost_max' => 5000000,
            ],
            [
                'name' => 'TDI (Tanda Daftar Industri)',
                'code' => 'TDI',
                'category' => 'business',
                'institution_id' => $dpmptsp->id,
                'avg_processing_days' => 14,
                'description' => 'Tanda bukti pendaftaran bagi perusahaan industri',
                'required_documents' => [
                    'Akta Perusahaan',
                    'NPWP',
                    'Data Produksi',
                    'Data Peralatan',
                    'Data Tenaga Kerja'
                ],
                'estimated_cost_min' => 2000000,
                'estimated_cost_max' => 8000000,
            ],

            // OTHER PERMITS
            [
                'name' => 'Izin HO (Izin Gangguan)',
                'code' => 'IZIN_HO',
                'category' => 'other',
                'institution_id' => $dpmptsp->id,
                'avg_processing_days' => 14,
                'description' => 'Izin tempat usaha yang dapat menimbulkan bahaya, kerugian, atau gangguan',
                'required_documents' => [
                    'Surat Permohonan',
                    'Fotocopy KTP',
                    'Surat Keterangan Domisili',
                    'IMB/PBG',
                    'Persetujuan Tetangga',
                    'Denah Lokasi'
                ],
                'estimated_cost_min' => 2000000,
                'estimated_cost_max' => 10000000,
            ],
            [
                'name' => 'Izin Reklame',
                'code' => 'IZIN_REKLAME',
                'category' => 'other',
                'institution_id' => $dpmptsp->id,
                'avg_processing_days' => 7,
                'description' => 'Izin pemasangan reklame/billboard',
                'required_documents' => [
                    'Surat Permohonan',
                    'Gambar Desain Reklame',
                    'Foto Lokasi',
                    'Surat Persetujuan Pemilik Tanah',
                    'NPWP'
                ],
                'estimated_cost_min' => 1000000,
                'estimated_cost_max' => 5000000,
            ],
            [
                'name' => 'Izin Operasional',
                'code' => 'IZIN_OPERASIONAL',
                'category' => 'business',
                'institution_id' => $dpmptsp->id,
                'avg_processing_days' => 14,
                'description' => 'Izin untuk menjalankan kegiatan usaha',
                'required_documents' => [
                    'NIB',
                    'Sertifikat Standar',
                    'Dokumen Lingkungan',
                    'PBG (jika ada bangunan)',
                    'Izin lainnya sesuai bidang usaha'
                ],
                'estimated_cost_min' => 3000000,
                'estimated_cost_max' => 15000000,
            ],
            [
                'name' => 'Izin Penggunaan Air Tanah',
                'code' => 'IZIN_AIR_TANAH',
                'category' => 'environmental',
                'institution_id' => $dpu->id,
                'avg_processing_days' => 21,
                'description' => 'Izin pengambilan dan pemanfaatan air tanah',
                'required_documents' => [
                    'Surat Permohonan',
                    'Rencana Penggunaan Air',
                    'Data Sumur',
                    'Hasil Uji Lab Air',
                    'Rekomendasi Teknis'
                ],
                'estimated_cost_min' => 3000000,
                'estimated_cost_max' => 10000000,
            ],
        ];

        foreach ($permits as $permit) {
            PermitType::updateOrCreate(
                ['code' => $permit['code']],
                $permit
            );
        }

        $this->command->info('✅ Seeded ' . count($permits) . ' permit types successfully!');
    }
}
