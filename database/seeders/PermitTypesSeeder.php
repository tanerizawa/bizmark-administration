<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PermitType;
use App\Models\Institution;

class PermitTypesSeeder extends Seeder
{
    public function run(): void
    {
        $institution = Institution::first();
        
        $permitTypes = [
            [
                'name' => 'Surat Izin Usaha Perdagangan (SIUP)',
                'code' => 'SIUP',
                'category' => 'business',
                'institution_id' => $institution?->id,
                'avg_processing_days' => 30,
                'description' => 'Izin untuk menjalankan kegiatan usaha perdagangan barang dan jasa',
                'required_documents' => [
                    'KTP Direktur/Pemilik',
                    'NPWP Perusahaan',
                    'Akta Pendirian Perusahaan',
                    'SK Kemenkumham',
                    'Surat Domisili Usaha',
                    'Pas Foto Direktur 4x6 (2 lembar)',
                ],
                'estimated_cost_min' => 3000000,
                'estimated_cost_max' => 5000000,
                'is_active' => true,
            ],
            [
                'name' => 'Tanda Daftar Perusahaan (TDP)',
                'code' => 'TDP',
                'category' => 'business',
                'institution_id' => $institution?->id,
                'avg_processing_days' => 14,
                'description' => 'Pendaftaran perusahaan pada daftar perusahaan yang wajib dilakukan',
                'required_documents' => [
                    'KTP Pemilik/Direktur',
                    'NPWP Perusahaan',
                    'Akta Pendirian',
                    'SK Kemenkumham',
                    'SIUP',
                ],
                'estimated_cost_min' => 2000000,
                'estimated_cost_max' => 3500000,
                'is_active' => true,
            ],
            [
                'name' => 'Nomor Pokok Wajib Pajak (NPWP)',
                'code' => 'NPWP',
                'category' => 'business',
                'institution_id' => $institution?->id,
                'avg_processing_days' => 7,
                'description' => 'Nomor identitas wajib pajak yang diberikan oleh Direktorat Jenderal Pajak',
                'required_documents' => [
                    'KTP',
                    'Kartu Keluarga',
                    'Surat Keterangan Usaha',
                ],
                'estimated_cost_min' => 500000,
                'estimated_cost_max' => 1000000,
                'is_active' => true,
            ],
            [
                'name' => 'Izin Mendirikan Bangunan (IMB)',
                'code' => 'IMB',
                'category' => 'building',
                'institution_id' => $institution?->id,
                'avg_processing_days' => 45,
                'description' => 'Izin untuk mendirikan bangunan sesuai tata ruang dan persyaratan teknis',
                'required_documents' => [
                    'Fotokopi KTP Pemohon',
                    'Fotokopi Sertifikat Tanah',
                    'Gambar Rencana Bangunan',
                    'Surat Pernyataan Tidak Keberatan dari Tetangga',
                    'SPPT PBB Tahun Berjalan',
                ],
                'estimated_cost_min' => 5000000,
                'estimated_cost_max' => 15000000,
                'is_active' => true,
            ],
            [
                'name' => 'Sertifikat Laik Fungsi (SLF)',
                'code' => 'SLF',
                'category' => 'building',
                'institution_id' => $institution?->id,
                'avg_processing_days' => 21,
                'description' => 'Sertifikat yang menyatakan bahwa bangunan gedung layak fungsi',
                'required_documents' => [
                    'IMB',
                    'Gambar As Built Drawing',
                    'Laporan Hasil Pengawasan Konstruksi',
                    'Berita Acara Serah Terima Pekerjaan',
                ],
                'estimated_cost_min' => 3000000,
                'estimated_cost_max' => 7000000,
                'is_active' => true,
            ],
            [
                'name' => 'Surat Izin Usaha Industri (SIUI)',
                'code' => 'SIUI',
                'category' => 'business',
                'institution_id' => $institution?->id,
                'avg_processing_days' => 30,
                'description' => 'Izin untuk melakukan kegiatan usaha industri',
                'required_documents' => [
                    'KTP Direktur',
                    'NPWP Perusahaan',
                    'Akta Pendirian',
                    'SK Kemenkumham',
                    'Surat Domisili',
                    'Izin Gangguan (HO)',
                ],
                'estimated_cost_min' => 4000000,
                'estimated_cost_max' => 8000000,
                'is_active' => true,
            ],
            [
                'name' => 'Izin Gangguan (HO)',
                'code' => 'HO',
                'category' => 'business',
                'institution_id' => $institution?->id,
                'avg_processing_days' => 21,
                'description' => 'Izin tempat usaha yang tidak menimbulkan gangguan lingkungan',
                'required_documents' => [
                    'KTP Pemilik Usaha',
                    'Surat Keterangan Domisili',
                    'Denah Lokasi Usaha',
                    'Pas Foto 4x6',
                    'NPWP',
                ],
                'estimated_cost_min' => 2500000,
                'estimated_cost_max' => 4500000,
                'is_active' => true,
            ],
            [
                'name' => 'Izin Edar Produk (MD/ML)',
                'code' => 'MD',
                'category' => 'business',
                'institution_id' => $institution?->id,
                'avg_processing_days' => 60,
                'description' => 'Izin edar untuk produk makanan dan minuman dari BPOM',
                'required_documents' => [
                    'Akta Pendirian Perusahaan',
                    'NPWP',
                    'Sertifikat Halal (jika diperlukan)',
                    'Hasil Uji Lab Produk',
                    'Desain Label Kemasan',
                ],
                'estimated_cost_min' => 8000000,
                'estimated_cost_max' => 15000000,
                'is_active' => true,
            ],
            [
                'name' => 'Sertifikat Halal',
                'code' => 'HALAL',
                'category' => 'other',
                'institution_id' => $institution?->id,
                'avg_processing_days' => 90,
                'description' => 'Sertifikat yang menyatakan produk halal sesuai syariat Islam',
                'required_documents' => [
                    'Akta Pendirian',
                    'NPWP',
                    'Daftar Produk',
                    'Daftar Bahan Baku dan Supplier',
                    'Sistem Jaminan Halal',
                ],
                'estimated_cost_min' => 10000000,
                'estimated_cost_max' => 20000000,
                'is_active' => true,
            ],
            [
                'name' => 'NIB (Nomor Induk Berusaha)',
                'code' => 'NIB',
                'category' => 'business',
                'institution_id' => $institution?->id,
                'avg_processing_days' => 1,
                'description' => 'Identitas pelaku usaha yang diterbitkan oleh OSS',
                'required_documents' => [
                    'KTP Direktur',
                    'Email Aktif',
                    'Nomor Telepon',
                    'Akta Pendirian (untuk PT/CV)',
                ],
                'estimated_cost_min' => 500000,
                'estimated_cost_max' => 1500000,
                'is_active' => true,
            ],
            [
                'name' => 'Izin Lingkungan (AMDAL/UKL-UPL)',
                'code' => 'AMDAL',
                'category' => 'environmental',
                'institution_id' => $institution?->id,
                'avg_processing_days' => 60,
                'description' => 'Izin untuk kegiatan yang berdampak lingkungan',
                'required_documents' => [
                    'Akta Pendirian',
                    'Dokumen AMDAL/UKL-UPL',
                    'Denah Lokasi',
                    'Surat Pernyataan Kesanggupan',
                ],
                'estimated_cost_min' => 15000000,
                'estimated_cost_max' => 50000000,
                'is_active' => true,
            ],
            [
                'name' => 'Izin Lokasi',
                'code' => 'IZIN_LOKASI',
                'category' => 'land',
                'institution_id' => $institution?->id,
                'avg_processing_days' => 30,
                'description' => 'Izin penggunaan lahan untuk kegiatan usaha',
                'required_documents' => [
                    'KTP Pemohon',
                    'Sertifikat Tanah',
                    'Peta Lokasi',
                    'Surat Pernyataan Kesanggupan',
                ],
                'estimated_cost_min' => 5000000,
                'estimated_cost_max' => 10000000,
                'is_active' => true,
            ],
            [
                'name' => 'Izin Trayek',
                'code' => 'TRAYEK',
                'category' => 'transportation',
                'institution_id' => $institution?->id,
                'avg_processing_days' => 21,
                'description' => 'Izin untuk angkutan umum dalam trayek tertentu',
                'required_documents' => [
                    'KTP Pemilik',
                    'STNK Kendaraan',
                    'KIR',
                    'Surat Pernyataan Kesanggupan',
                ],
                'estimated_cost_min' => 2000000,
                'estimated_cost_max' => 5000000,
                'is_active' => true,
            ],
        ];

        foreach ($permitTypes as $permitType) {
            PermitType::updateOrCreate(
                ['code' => $permitType['code']],
                $permitType
            );
        }

        $this->command->info('✓ Seeded ' . count($permitTypes) . ' permit types');
    }
}
