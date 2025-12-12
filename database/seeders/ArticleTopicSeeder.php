<?php

namespace Database\Seeders;

use App\Models\ArticleTopic;
use Illuminate\Database\Seeder;

class ArticleTopicSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('📝 Seeding Article Topics...');

        $topics = [
            // Tips & Panduan (40%)
            [
                'title' => 'Cara Mengurus IMB Rumah Tinggal: Panduan Lengkap 2025',
                'description' => 'Panduan step-by-step mengurus Izin Mendirikan Bangunan untuk rumah tinggal, syarat dokumen, biaya, dan timeline.',
                'category' => 'tips',
                'keywords' => ['IMB', 'izin mendirikan bangunan', 'rumah tinggal', 'perizinan bangunan'],
                'tags' => ['IMB', 'Perizinan', 'Rumah Tinggal'],
                'priority' => 100,
            ],
            [
                'title' => 'Syarat dan Cara Mengajukan SPPL untuk Usaha Kecil',
                'description' => 'Penjelasan lengkap tentang Surat Pernyataan Kesanggupan Pengelolaan Lingkungan untuk UMKM.',
                'category' => 'tips',
                'keywords' => ['SPPL', 'izin lingkungan', 'UMKM', 'usaha kecil'],
                'tags' => ['SPPL', 'Lingkungan', 'UMKM'],
                'priority' => 95,
            ],
            [
                'title' => '5 Kesalahan Umum Saat Mengurus NIB dan Cara Menghindarinya',
                'description' => 'Hindari kesalahan fatal saat mengurus Nomor Induk Berusaha melalui OSS.',
                'category' => 'tips',
                'keywords' => ['NIB', 'OSS', 'nomor induk berusaha', 'kesalahan umum'],
                'tags' => ['NIB', 'OSS', 'Bisnis'],
                'priority' => 90,
            ],
            [
                'title' => 'Panduan Lengkap Mengurus SLF Bangunan Gedung',
                'description' => 'Cara mengurus Sertifikat Laik Fungsi bangunan gedung, persyaratan, dan prosedur lengkap.',
                'category' => 'tips',
                'keywords' => ['SLF', 'sertifikat laik fungsi', 'bangunan gedung'],
                'tags' => ['SLF', 'Bangunan', 'Perizinan'],
                'priority' => 85,
            ],
            
            // Regulation (25%)
            [
                'title' => 'Perubahan Regulasi Perizinan 2025: Apa yang Perlu Anda Ketahui',
                'description' => 'Update terbaru peraturan perizinan usaha dan dampaknya untuk pemilik bisnis.',
                'category' => 'regulation',
                'keywords' => ['regulasi 2025', 'peraturan perizinan', 'update peraturan'],
                'tags' => ['Regulasi', 'Update', '2025'],
                'priority' => 80,
            ],
            [
                'title' => 'Memahami UU Cipta Kerja: Dampak pada Perizinan Usaha',
                'description' => 'Analisis mendalam tentang perubahan perizinan usaha pasca UU Cipta Kerja.',
                'category' => 'regulation',
                'keywords' => ['UU Cipta Kerja', 'omnibus law', 'perizinan usaha'],
                'tags' => ['UU Cipta Kerja', 'Regulasi'],
                'priority' => 75,
            ],
            [
                'title' => 'Perbedaan UKL-UPL dan AMDAL: Kapan Harus Menggunakan Mana?',
                'description' => 'Penjelasan detail kriteria dan perbedaan dokumen lingkungan UKL-UPL vs AMDAL.',
                'category' => 'regulation',
                'keywords' => ['UKL-UPL', 'AMDAL', 'dokumen lingkungan', 'perbedaan'],
                'tags' => ['UKL-UPL', 'AMDAL', 'Lingkungan'],
                'priority' => 70,
            ],
            
            // General (20%)
            [
                'title' => 'Mengapa Perizinan Usaha Penting untuk Keberlangsungan Bisnis',
                'description' => 'Manfaat memiliki izin usaha lengkap dan risiko operasional tanpa izin.',
                'category' => 'general',
                'keywords' => ['perizinan usaha', 'pentingnya izin', 'legalitas bisnis'],
                'tags' => ['Perizinan', 'Bisnis', 'Legalitas'],
                'priority' => 65,
            ],
            [
                'title' => 'Konsultan Perizinan: Kapan Anda Membutuhkannya?',
                'description' => 'Panduan memilih jasa konsultan perizinan dan kapan waktu yang tepat menggunakannya.',
                'category' => 'general',
                'keywords' => ['konsultan perizinan', 'jasa perizinan', 'kapan butuh konsultan'],
                'tags' => ['Konsultan', 'Perizinan'],
                'priority' => 60,
            ],
            [
                'title' => 'Timeline Proses Perizinan: Berapa Lama Setiap Izin Diproses?',
                'description' => 'Estimasi waktu pemrosesan berbagai jenis izin usaha dan dokumen lingkungan.',
                'category' => 'general',
                'keywords' => ['timeline perizinan', 'lama proses izin', 'durasi perizinan'],
                'tags' => ['Timeline', 'Proses', 'Perizinan'],
                'priority' => 55,
            ],
            
            // Case Study (10%)
            [
                'title' => 'Studi Kasus: Bagaimana PT ABC Mengurus AMDAL dalam 6 Bulan',
                'description' => 'Pengalaman nyata klien mengurus AMDAL untuk pabrik manufaktur.',
                'category' => 'case-study',
                'keywords' => ['studi kasus', 'AMDAL', 'pengalaman klien'],
                'tags' => ['Case Study', 'AMDAL', 'Success Story'],
                'priority' => 50,
            ],
            [
                'title' => 'Success Story: Dari Izin Prinsip hingga Operasional dalam 3 Bulan',
                'description' => 'Kisah sukses klien menyelesaikan seluruh perizinan usaha dengan efisien.',
                'category' => 'case-study',
                'keywords' => ['success story', 'izin prinsip', 'perizinan cepat'],
                'tags' => ['Success Story', 'Perizinan'],
                'priority' => 45,
            ],
            
            // News (5%)
            [
                'title' => 'Pemerintah Luncurkan Sistem OSS RBA: Apa yang Berubah?',
                'description' => 'Berita terbaru tentang implementasi OSS berbasis risiko dan dampaknya.',
                'category' => 'news',
                'keywords' => ['OSS RBA', 'berita perizinan', 'sistem baru'],
                'tags' => ['News', 'OSS', 'Update'],
                'priority' => 40,
            ],
            [
                'title' => 'Insentif Perizinan untuk Investor Asing di 2025',
                'description' => 'Kemudahan perizinan dan insentif baru untuk penanaman modal asing.',
                'category' => 'news',
                'keywords' => ['investor asing', 'insentif perizinan', 'PMA'],
                'tags' => ['News', 'Investasi', 'Insentif'],
                'priority' => 35,
            ],
            
            // Additional topics for variety
            [
                'title' => 'Checklist Dokumen Wajib untuk Perizinan Industri Manufaktur',
                'description' => 'Daftar lengkap dokumen yang dibutuhkan untuk mengurus izin industri manufaktur.',
                'category' => 'tips',
                'keywords' => ['checklist dokumen', 'industri manufaktur', 'izin industri'],
                'tags' => ['Checklist', 'Manufaktur', 'Dokumen'],
                'priority' => 30,
            ],
            [
                'title' => 'Biaya Pengurusan Perizinan: Breakdown Lengkap 2025',
                'description' => 'Rincian biaya resmi dan estimasi untuk berbagai jenis izin usaha.',
                'category' => 'general',
                'keywords' => ['biaya perizinan', 'tarif izin', 'harga perizinan'],
                'tags' => ['Biaya', 'Tarif', 'Perizinan'],
                'priority' => 25,
            ],
        ];

        foreach ($topics as $topicData) {
            ArticleTopic::create($topicData);
        }

        $count = count($topics);
        $this->command->info("✅ Created {$count} article topics!");
        $this->command->line('');
        $this->command->info('📊 Topic Distribution:');
        $this->command->line('   Tips & Panduan: 4 topics (40%)');
        $this->command->line('   Regulation: 3 topics (25%)');
        $this->command->line('   General: 3 topics (20%)');
        $this->command->line('   Case Study: 2 topics (10%)');
        $this->command->line('   News: 2 topics (5%)');
        $this->command->line('   Additional: 2 topics');
        $this->command->line('');
        $this->command->info('💡 Topics ready for auto-posting!');
    }
}
