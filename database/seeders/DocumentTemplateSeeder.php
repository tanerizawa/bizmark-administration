<?php

namespace Database\Seeders;

use App\Models\DocumentTemplate;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class DocumentTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get first admin user
        $admin = User::first();

        if (!$admin) {
            $this->command->warn('No users found. Please run UserSeeder first.');
            return;
        }

        // Create templates directory if not exists
        $templatesPath = storage_path('app/templates');
        if (!File::exists($templatesPath)) {
            File::makeDirectory($templatesPath, 0755, true);
        }

        // Sample template 1: Pertek BPN
        $pertekTemplate = $templatesPath . '/sample_pertek_bpn.txt';
        if (!File::exists($pertekTemplate)) {
            File::put($pertekTemplate, $this->getSamplePertekContent());
        }

        DocumentTemplate::create([
            'name' => 'Template Permohonan Pertek BPN',
            'permit_type' => 'pertek_bpn',
            'description' => 'Template surat permohonan pertimbangan teknis pertanahan ke BPN. Cocok untuk perizinan yang memerlukan analisa pertanahan.',
            'file_name' => 'sample_pertek_bpn.txt',
            'file_path' => 'templates/sample_pertek_bpn.txt',
            'file_size' => File::size($pertekTemplate),
            'mime_type' => 'text/plain',
            'page_count' => 5,
            'required_fields' => [
                'project_name',
                'client_name',
                'location',
                'land_area',
                'land_certificate',
                'pic_name',
            ],
            'is_active' => true,
            'created_by' => $admin->id,
        ]);

        // Sample template 2: UKL-UPL
        $uklTemplate = $templatesPath . '/sample_ukl_upl.txt';
        if (!File::exists($uklTemplate)) {
            File::put($uklTemplate, $this->getSampleUKLContent());
        }

        DocumentTemplate::create([
            'name' => 'Template Dokumen UKL-UPL',
            'permit_type' => 'ukl_upl',
            'description' => 'Template dokumen Upaya Pengelolaan Lingkungan dan Upaya Pemantauan Lingkungan. Untuk kegiatan yang berdampak lingkungan kecil-menengah.',
            'file_name' => 'sample_ukl_upl.txt',
            'file_path' => 'templates/sample_ukl_upl.txt',
            'file_size' => File::size($uklTemplate),
            'mime_type' => 'text/plain',
            'page_count' => 15,
            'required_fields' => [
                'project_name',
                'client_name',
                'location',
                'business_type',
                'land_area',
                'building_area',
            ],
            'is_active' => true,
            'created_by' => $admin->id,
        ]);

        // Sample template 3: IMB (Izin Mendirikan Bangunan)
        $imbTemplate = $templatesPath . '/sample_imb.txt';
        if (!File::exists($imbTemplate)) {
            File::put($imbTemplate, $this->getSampleIMBContent());
        }

        DocumentTemplate::create([
            'name' => 'Template Permohonan IMB',
            'permit_type' => 'imb',
            'description' => 'Template surat permohonan Izin Mendirikan Bangunan (IMB) atau PBG. Untuk pembangunan gedung/bangunan.',
            'file_name' => 'sample_imb.txt',
            'file_path' => 'templates/sample_imb.txt',
            'file_size' => File::size($imbTemplate),
            'mime_type' => 'text/plain',
            'page_count' => 8,
            'required_fields' => [
                'project_name',
                'client_name',
                'location',
                'building_area',
                'land_certificate',
            ],
            'is_active' => true,
            'created_by' => $admin->id,
        ]);

        $this->command->info('Document templates seeded successfully!');
    }

    protected function getSamplePertekContent(): string
    {
        return "SURAT PERMOHONAN PERTIMBANGAN TEKNIS PERTANAHAN (PERTEK)\n\nKepada Yth. Kepala Kantor Pertanahan [Lokasi] di Tempat\n\nDengan hormat,\n\nYang bertanda tangan di bawah ini:\nNama: [Nama Client]\nAlamat: [Alamat Client]\n\nMengajukan permohonan Pertek untuk:\nLokasi: [Lokasi Proyek]\nLuas: [Luas Tanah] m²\nSertifikat: [Nomor Sertifikat]\n\nDemikian permohonan ini kami sampaikan. Terima kasih.\n\n[Lokasi], [Tanggal]\n([Nama PIC])";
    }

    protected function getSampleUKLContent(): string
    {
        return "DOKUMEN UKL-UPL\n\nPemrakarsa: [Nama Client]\nKegiatan: [Nama Proyek]\nLokasi: [Lokasi Proyek]\nJenis: [Jenis Usaha]\nLuas: [Luas Tanah] m²\n\nBAB I - PENDAHULUAN\nDokumen ini menerangkan upaya pengelolaan dan pemantauan lingkungan.\n\nBAB II - DESKRIPSI KEGIATAN\n[Detail proyek]\n\nBAB III - DAMPAK LINGKUNGAN\n[Dampak yang mungkin timbul]\n\nBAB IV - PENGELOLAAN\n[Upaya pengelolaan]\n\n[Lokasi], [Tanggal]\n([Nama PIC])";
    }

    protected function getSampleIMBContent(): string
    {
        return "SURAT PERMOHONAN IMB\n\nKepada Yth. Kepala DPMPTSP [Lokasi] di Tempat\n\nDengan hormat,\n\nPemohon: [Nama Client]\nAlamat: [Alamat Client]\n\nDATA BANGUNAN:\nNama: [Nama Proyek]\nLokasi: [Lokasi Proyek]\nLuas Tanah: [Luas Tanah] m²\nLuas Bangunan: [Luas Bangunan] m²\nSertifikat: [Nomor Sertifikat]\n\nDemikian permohonan IMB ini kami sampaikan.\n\n[Lokasi], [Tanggal]\n([Nama PIC])";
    }
}

