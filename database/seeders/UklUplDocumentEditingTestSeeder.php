<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TestTemplate;
use Carbon\Carbon;

class UklUplDocumentEditingTestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Check if template already exists
        $existingTemplate = TestTemplate::where('title', 'Document Editing Test - UKL/UPL Environmental Specialist')->first();
        
        if ($existingTemplate) {
            $this->command->info('⚠️  Template UKL/UPL sudah ada! Skipping...');
            return;
        }

        // Instruksi untuk kandidat
        $instructions = <<<'EOT'
▸ LATAR BELAKANG

Anda akan menerima dokumen UKL/UPL yang tidak teratur dan belum lengkap. Tugas Anda adalah memperbaiki dokumen tersebut mengikuti standar penyusunan dokumen lingkungan hidup yang berlaku.


▸ TUGAS YANG HARUS DISELESAIKAN

[ 1 ] RAPIHKAN DOKUMEN (25 poin)
    • Perbaiki formatting (font, spacing, alignment)
    • Konsistensi heading hierarchy
    • Tambahkan/perbaiki page numbering
    • Atur margin dan layout profesional

[ 2 ] SESUAIKAN KAIDAH PENYUSUNAN & PENOMORAN (25 poin)
    • Perbaiki penomoran bab/sub-bab (1, 1.1, 1.1.1, dst)
    • Beri nomor pada semua tabel dan gambar
    • Update daftar isi
    • Pastikan konsistensi cross-reference

[ 3 ] LENGKAPI KONTEN YANG KOSONG (30 poin)
    • Isi rumus/formula perhitungan yang hilang
    • Lengkapi data tabel dengan dummy data yang relevan
    • Tambahkan parameter lingkungan standar
    • Isi bagian metodologi yang kosong

[ 4 ] BERIKAN KOMENTAR TEKNIS (15 poin)
    • Gunakan fitur "Comment" di Word
    • Identifikasi kesalahan teknis
    • Beri saran perbaikan
    • Tandai bagian yang kritis/perlu review lebih lanjut

[ 5 ] OVERALL QUALITY (5 poin)
    • Profesionalisme dokumen
    • Kelayakan untuk diserahkan ke klien
    • Compliance dengan standar UKL/UPL


▸ ATURAN PENGERJAAN

▪ DIIZINKAN:
    • Gunakan Microsoft Word atau kompatibel (.docx)
    • Gunakan internet untuk referensi standar/regulasi
    • Simpan dengan nama: UKL_UPL_FIXED_[NAMA_ANDA].docx

▪ TIDAK DIIZINKAN:
    • Konsultasi dengan orang lain

▪ BATAS WAKTU:
    • Upload sebelum waktu habis (2 jam)


▸ STANDAR DOKUMEN

    Font Body          Times New Roman 12pt
    Font Heading       Arial 14-16pt (bold)
    Margin             3cm (top), 3cm (left), 3cm (right), 2cm (bottom)
    Line Spacing       1.5
    Paper Size         A4


▸ PENILAIAN

Dokumen yang diperbaiki akan dinilai berdasarkan 22 kriteria dengan total 100 poin.
Passing score: 70%
EOT;

        // 22 Kriteria Penilaian
        $criteria = [
            // Formatting & Layout (25 poin)
            [
                'id' => 1,
                'category' => 'Formatting & Layout',
                'description' => 'Font consistency (Times New Roman 12pt body, Arial 14-16pt heading)',
                'points' => 5,
                'type' => 'Technical',
            ],
            [
                'id' => 2,
                'category' => 'Formatting & Layout',
                'description' => 'Heading hierarchy (H1 untuk BAB, H2 untuk sub-bab, H3 untuk sub-sub-bab)',
                'points' => 5,
                'type' => 'Technical',
            ],
            [
                'id' => 3,
                'category' => 'Formatting & Layout',
                'description' => 'Spacing & Margin (Line spacing 1.5, margin 3-3-3-2 cm)',
                'points' => 5,
                'type' => 'Technical',
            ],
            [
                'id' => 4,
                'category' => 'Formatting & Layout',
                'description' => 'Page numbering (Romawi untuk bagian awal, angka untuk isi)',
                'points' => 5,
                'type' => 'Technical',
            ],
            [
                'id' => 5,
                'category' => 'Formatting & Layout',
                'description' => 'Header/Footer consistency dengan judul dokumen',
                'points' => 5,
                'type' => 'Technical',
            ],
            
            // Penomoran & Struktur (25 poin)
            [
                'id' => 6,
                'category' => 'Penomoran & Struktur',
                'description' => 'Penomoran BAB (Format: BAB I, BAB II - uppercase Romawi)',
                'points' => 7,
                'type' => 'Technical',
            ],
            [
                'id' => 7,
                'category' => 'Penomoran & Struktur',
                'description' => 'Penomoran sub-bab (Format: 1.1, 1.1.1, 1.1.1.1 maksimal 4 level)',
                'points' => 6,
                'type' => 'Technical',
            ],
            [
                'id' => 8,
                'category' => 'Penomoran & Struktur',
                'description' => 'Penomoran tabel (Format: Tabel 3.1 - BAB.urutan)',
                'points' => 5,
                'type' => 'Technical',
            ],
            [
                'id' => 9,
                'category' => 'Penomoran & Struktur',
                'description' => 'Penomoran gambar (Format: Gambar 2.3 - BAB.urutan)',
                'points' => 4,
                'type' => 'Technical',
            ],
            [
                'id' => 10,
                'category' => 'Penomoran & Struktur',
                'description' => 'Daftar isi accuracy (Semua heading + page number benar)',
                'points' => 3,
                'type' => 'Technical',
            ],
            
            // Content Completion (30 poin)
            [
                'id' => 11,
                'category' => 'Content Completion',
                'description' => 'Rumus perhitungan lingkungan (TSS, BOD, COD, Beban Pencemaran)',
                'points' => 8,
                'type' => 'Technical',
            ],
            [
                'id' => 12,
                'category' => 'Content Completion',
                'description' => 'Lengkapi data tabel kualitas air, udara, kebisingan',
                'points' => 8,
                'type' => 'Technical',
            ],
            [
                'id' => 13,
                'category' => 'Content Completion',
                'description' => 'Parameter standar baku mutu (PP, Perda)',
                'points' => 6,
                'type' => 'Technical',
            ],
            [
                'id' => 14,
                'category' => 'Content Completion',
                'description' => 'Metodologi sampling (Metode pengambilan sampel)',
                'points' => 5,
                'type' => 'Technical',
            ],
            [
                'id' => 15,
                'category' => 'Content Completion',
                'description' => 'Dummy data relevan untuk jenis kegiatan',
                'points' => 3,
                'type' => 'Technical',
            ],
            
            // Technical Review (15 poin)
            [
                'id' => 16,
                'category' => 'Technical Review',
                'description' => 'Identifikasi minimal 5 kesalahan teknis (gunakan Comment di Word)',
                'points' => 5,
                'type' => 'Analysis',
            ],
            [
                'id' => 17,
                'category' => 'Technical Review',
                'description' => 'Berikan saran perbaikan konstruktif',
                'points' => 4,
                'type' => 'Analysis',
            ],
            [
                'id' => 18,
                'category' => 'Technical Review',
                'description' => 'Compliance check - Tandai bagian tidak sesuai regulasi',
                'points' => 3,
                'type' => 'Analysis',
            ],
            [
                'id' => 19,
                'category' => 'Technical Review',
                'description' => 'Flag critical issues yang dapat menggagalkan persetujuan',
                'points' => 3,
                'type' => 'Analysis',
            ],
            
            // Overall Quality (5 poin)
            [
                'id' => 20,
                'category' => 'Overall Quality',
                'description' => 'Profesionalisme dokumen (layak diserahkan ke KLHK/Dinas LH)',
                'points' => 2,
                'type' => 'Quality',
            ],
            [
                'id' => 21,
                'category' => 'Overall Quality',
                'description' => 'Readability - Mudah dibaca dan dipahami',
                'points' => 2,
                'type' => 'Quality',
            ],
            [
                'id' => 22,
                'category' => 'Overall Quality',
                'description' => 'Completeness - Tidak ada bagian yang masih kosong',
                'points' => 1,
                'type' => 'Quality',
            ],
        ];

        // Calculate total points
        $totalPoints = collect($criteria)->sum('points');

        // Create test template
        $template = TestTemplate::create([
            'title' => 'Document Editing Test - UKL/UPL Environmental Specialist',
            'description' => 'Test kemampuan kandidat dalam memperbaiki dan melengkapi dokumen teknis UKL/UPL (Upaya Pengelolaan Lingkungan/Upaya Pemantauan Lingkungan). Kandidat akan mendownload dokumen yang tidak teratur, memperbaikinya sesuai standar, dan mengupload hasil revisi dalam waktu 2 jam.',
            'test_type' => 'document-editing',
            'duration_minutes' => 120,
            'passing_score' => 70,
            'instructions' => $instructions,
            'template_file_path' => null, // Will be updated when user uploads document
            'evaluation_criteria' => [
                'criteria' => $criteria,
                'total_points' => $totalPoints,
            ],
            'is_active' => false, // Set to false until document is uploaded
            'questions_data' => null,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        $this->command->info('✅ Test template UKL/UPL berhasil dibuat!');
        $this->command->info('');
        $this->command->info('📋 Details:');
        $this->command->info("   ID: {$template->id}");
        $this->command->info("   Title: {$template->title}");
        $this->command->info("   Type: {$template->test_type}");
        $this->command->info("   Duration: {$template->duration_minutes} minutes");
        $this->command->info("   Passing Score: {$template->passing_score}%");
        $this->command->info("   Total Criteria: " . count($criteria));
        $this->command->info("   Total Points: {$totalPoints}");
        $this->command->info("   Status: INACTIVE (upload dokumen dulu)");
        $this->command->info('');
        $this->command->info('📝 Next Steps:');
        $this->command->info('   1. Siapkan file: UKL_UPL_Template_Broken.docx');
        $this->command->info('   2. Edit template via admin panel');
        $this->command->info('   3. Upload dokumen template');
        $this->command->info('   4. Aktivkan template (is_active = true)');
        $this->command->info('');
        $this->command->info("🔗 Edit URL: /admin/recruitment/tests/{$template->id}/edit");
    }
}
