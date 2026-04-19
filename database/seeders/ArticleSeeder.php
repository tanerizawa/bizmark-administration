<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\User;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class ArticleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::first(); // Get first user as author

        if (!$user) {
            $this->command->error('No users found! Please create a user first.');
            return;
        }

        $articles = [
            [
                'title' => 'Pentingnya Dokumen LB3 dalam Pengelolaan Limbah Bahan Berbahaya dan Beracun',
                'excerpt' => 'Dokumen LB3 (Limbah Bahan Berbahaya dan Beracun) merupakan dokumen penting yang wajib dimiliki oleh setiap perusahaan yang menghasilkan limbah B3.',
                'content' => '<h2>Apa itu LB3?</h2>
                <p>LB3 atau Limbah Bahan Berbahaya dan Beracun adalah sisa suatu usaha dan/atau kegiatan yang mengandung bahan berbahaya dan/atau beracun yang karena sifat dan/atau konsentrasinya dan/atau jumlahnya, baik secara langsung maupun tidak langsung, dapat mencemarkan dan/atau merusak lingkungan hidup, dan/atau dapat membahayakan lingkungan hidup, kesehatan, kelangsungan hidup manusia serta makhluk hidup lain.</p>
                
                <h2>Mengapa Dokumen LB3 Penting?</h2>
                <p>Dokumen LB3 memiliki beberapa fungsi penting:</p>
                <ul>
                    <li>Sebagai bukti kepatuhan terhadap peraturan perundang-undangan lingkungan hidup</li>
                    <li>Memastikan pengelolaan limbah B3 dilakukan dengan benar dan aman</li>
                    <li>Melindungi lingkungan dari pencemaran limbah B3</li>
                    <li>Mencegah sanksi hukum dan administratif dari pemerintah</li>
                </ul>
                
                <h2>Peraturan yang Mengatur LB3</h2>
                <p>Pengelolaan LB3 di Indonesia diatur dalam berbagai peraturan, antara lain:</p>
                <ul>
                    <li>PP No. 101 Tahun 2014 tentang Pengelolaan Limbah B3</li>
                    <li>PP No. 22 Tahun 2021 tentang Penyelenggaraan Perlindungan dan Pengelolaan Lingkungan Hidup</li>
                    <li>Permen LHK No. 6 Tahun 2021 tentang Tata Cara dan Persyaratan Pengelolaan Limbah B3</li>
                </ul>
                
                <h2>Layanan PT CANGAH PAJARATAN MANDIRI</h2>
                <p>PT CANGAH PAJARATAN MANDIRI siap membantu perusahaan Anda dalam pengurusan dokumen LB3, mulai dari konsultasi, penyusunan dokumen, hingga pendampingan proses perizinan.</p>',
                'category' => 'tips',
                'tags' => ['lb3', 'limbah b3', 'perizinan', 'lingkungan'],
                'status' => 'published',
                'published_at' => Carbon::now()->subDays(10),
                'is_featured' => true,
                'author_id' => $user->id,
                'meta_title' => 'Pentingnya Dokumen LB3 dalam Pengelolaan Limbah B3',
                'meta_description' => 'Pelajari pentingnya dokumen LB3 dalam pengelolaan limbah bahan berbahaya dan beracun serta peraturan yang mengaturnya.',
                'meta_keywords' => 'lb3, limbah b3, pengelolaan limbah, perizinan lingkungan',
            ],
            [
                'title' => 'Panduan Lengkap AMDAL: Analisis Mengenai Dampak Lingkungan',
                'excerpt' => 'AMDAL adalah kajian mengenai dampak penting suatu usaha dan/atau kegiatan yang direncanakan pada lingkungan hidup yang diperlukan bagi proses pengambilan keputusan.',
                'content' => '<h2>Apa itu AMDAL?</h2>
                <p>AMDAL (Analisis Mengenai Dampak Lingkungan) adalah kajian mengenai dampak penting suatu usaha dan/atau kegiatan yang direncanakan pada lingkungan hidup yang diperlukan bagi proses pengambilan keputusan tentang penyelenggaraan usaha dan/atau kegiatan.</p>
                
                <h2>Siapa yang Wajib Membuat AMDAL?</h2>
                <p>Setiap usaha dan/atau kegiatan yang berdampak penting terhadap lingkungan hidup wajib memiliki AMDAL. Jenis usaha yang wajib AMDAL diatur dalam Permen LHK No. 4 Tahun 2021 tentang Daftar Usaha dan/atau Kegiatan yang Wajib Memiliki AMDAL.</p>
                
                <h2>Dokumen dalam AMDAL</h2>
                <p>AMDAL terdiri dari beberapa dokumen:</p>
                <ol>
                    <li><strong>Kerangka Acuan (KA)</strong> - Ruang lingkup kajian AMDAL</li>
                    <li><strong>Analisis Dampak Lingkungan (ANDAL)</strong> - Telaahan mendalam dampak penting</li>
                    <li><strong>Rencana Pengelolaan Lingkungan (RKL)</strong> - Upaya penanganan dampak</li>
                    <li><strong>Rencana Pemantauan Lingkungan (RPL)</strong> - Upaya pemantauan dampak</li>
                </ol>
                
                <h2>Proses Penyusunan AMDAL</h2>
                <p>Proses penyusunan AMDAL meliputi beberapa tahap yang harus dilalui dengan cermat dan sesuai regulasi yang berlaku. Tim kami memiliki pengalaman dalam membantu perusahaan menyusun dokumen AMDAL yang berkualitas.</p>',
                'category' => 'tips',
                'tags' => ['amdal', 'lingkungan', 'perizinan', 'dampak lingkungan'],
                'status' => 'published',
                'published_at' => Carbon::now()->subDays(7),
                'is_featured' => true,
                'author_id' => $user->id,
                'meta_title' => 'Panduan Lengkap AMDAL - Analisis Mengenai Dampak Lingkungan',
                'meta_description' => 'Panduan lengkap tentang AMDAL, siapa yang wajib membuat, dokumen yang diperlukan, dan proses penyusunannya.',
                'meta_keywords' => 'amdal, analisis dampak lingkungan, perizinan, lingkungan hidup',
            ],
            [
                'title' => 'Peraturan Terbaru: Permen LHK No. 4 Tahun 2021 tentang Daftar Usaha Wajib AMDAL',
                'excerpt' => 'Pemerintah menerbitkan peraturan terbaru mengenai daftar usaha dan/atau kegiatan yang wajib memiliki AMDAL.',
                'content' => '<h2>Perubahan Signifikan</h2>
                <p>Permen LHK No. 4 Tahun 2021 menggantikan Permen LHK No. P.38/Menlhk/Setjen/Kum.1/7/2019 tentang Jenis Rencana Usaha dan/atau Kegiatan yang Wajib Memiliki AMDAL.</p>
                
                <h2>Poin-Poin Penting</h2>
                <ul>
                    <li>Penyederhanaan daftar usaha wajib AMDAL</li>
                    <li>Integrasi dengan sistem OSS (Online Single Submission)</li>
                    <li>Percepatan proses perizinan lingkungan</li>
                    <li>Penguatan peran konsultan lingkungan bersertifikat</li>
                </ul>
                
                <h2>Dampak bagi Industri</h2>
                <p>Peraturan ini membawa dampak positif bagi industri dalam hal efisiensi waktu dan biaya dalam pengurusan izin lingkungan. Namun, tetap memperhatikan aspek perlindungan lingkungan hidup.</p>',
                'category' => 'regulation',
                'tags' => ['regulasi', 'amdal', 'permen lhk', 'perizinan'],
                'status' => 'published',
                'published_at' => Carbon::now()->subDays(5),
                'is_featured' => false,
                'author_id' => $user->id,
                'meta_title' => 'Permen LHK No. 4 Tahun 2021 tentang Daftar Usaha Wajib AMDAL',
                'meta_description' => 'Penjelasan lengkap tentang Permen LHK No. 4 Tahun 2021 yang mengatur daftar usaha wajib AMDAL.',
                'meta_keywords' => 'permen lhk, amdal, regulasi lingkungan, perizinan',
            ],
            [
                'title' => 'Studi Kasus: Pengurusan Izin LB3 untuk Industri Manufaktur di Karawang',
                'excerpt' => 'Pengalaman kami dalam membantu perusahaan manufaktur di Karawang mendapatkan izin pengelolaan limbah B3.',
                'content' => '<h2>Latar Belakang Proyek</h2>
                <p>Sebuah perusahaan manufaktur elektronik di Karawang membutuhkan izin pengelolaan limbah B3 untuk operasional pabrik barunya. Perusahaan ini menghasilkan limbah B3 berupa solder waste, chemical waste, dan oil waste.</p>
                
                <h2>Tantangan yang Dihadapi</h2>
                <ul>
                    <li>Deadline ketat dari investor</li>
                    <li>Kompleksitas jenis limbah B3 yang dihasilkan</li>
                    <li>Koordinasi dengan berbagai instansi pemerintah</li>
                    <li>Penyiapan fasilitas TPS limbah B3</li>
                </ul>
                
                <h2>Solusi yang Kami Berikan</h2>
                <p>Tim PT CANGAH PAJARATAN MANDIRI melakukan pendekatan komprehensif:</p>
                <ol>
                    <li>Survey lapangan dan analisis jenis limbah</li>
                    <li>Penyusunan dokumen teknis dan administratif</li>
                    <li>Konsultasi dengan DLHK Jawa Barat</li>
                    <li>Pendampingan verifikasi lapangan</li>
                    <li>Finalisasi dan penerbitan izin</li>
                </ol>
                
                <h2>Hasil</h2>
                <p>Izin berhasil terbit dalam waktu 45 hari kerja, lebih cepat dari estimasi awal 60 hari. Klien sangat puas dengan layanan kami dan operasional pabrik dapat dimulai sesuai jadwal.</p>',
                'category' => 'case-study',
                'tags' => ['studi kasus', 'lb3', 'karawang', 'manufaktur'],
                'status' => 'published',
                'published_at' => Carbon::now()->subDays(3),
                'is_featured' => true,
                'author_id' => $user->id,
                'meta_title' => 'Studi Kasus Pengurusan Izin LB3 Industri Manufaktur Karawang',
                'meta_description' => 'Studi kasus sukses pengurusan izin LB3 untuk industri manufaktur di Karawang oleh PT CANGAH PAJARATAN MANDIRI.',
                'meta_keywords' => 'studi kasus, lb3, karawang, industri manufaktur, perizinan',
            ],
            [
                'title' => 'PT CANGAH PAJARATAN MANDIRI Raih Penghargaan Best Environmental Consultant 2024',
                'excerpt' => 'Kami dengan bangga mengumumkan bahwa PT CANGAH PAJARATAN MANDIRI telah meraih penghargaan sebagai Best Environmental Consultant 2024.',
                'content' => '<h2>Penghargaan Bergengsi</h2>
                <p>PT CANGAH PAJARATAN MANDIRI dengan bangga mengumumkan telah meraih penghargaan sebagai Best Environmental Consultant 2024 dari Asosiasi Konsultan Lingkungan Indonesia.</p>
                
                <h2>Kriteria Penilaian</h2>
                <p>Penghargaan ini diberikan berdasarkan beberapa kriteria:</p>
                <ul>
                    <li>Jumlah proyek yang berhasil diselesaikan</li>
                    <li>Tingkat kepuasan klien</li>
                    <li>Inovasi dalam layanan konsultasi</li>
                    <li>Kontribusi terhadap pelestarian lingkungan</li>
                    <li>Profesionalisme dan integritas</li>
                </ul>
                
                <h2>Komitmen Kami</h2>
                <p>Penghargaan ini menjadi motivasi bagi kami untuk terus memberikan layanan terbaik kepada klien. Kami akan terus berinovasi dan meningkatkan kualitas layanan kami.</p>
                
                <h2>Terima Kasih</h2>
                <p>Kami mengucapkan terima kasih kepada seluruh klien yang telah mempercayakan kebutuhan konsultasi lingkungan mereka kepada kami. Kepercayaan Anda adalah aset berharga bagi kami.</p>',
                'category' => 'news',
                'tags' => ['berita', 'penghargaan', 'achievement'],
                'status' => 'published',
                'published_at' => Carbon::now()->subDay(),
                'is_featured' => false,
                'author_id' => $user->id,
                'meta_title' => 'PT CANGAH PAJARATAN MANDIRI Raih Best Environmental Consultant 2024',
                'meta_description' => 'PT CANGAH PAJARATAN MANDIRI meraih penghargaan Best Environmental Consultant 2024 dari Asosiasi Konsultan Lingkungan Indonesia.',
                'meta_keywords' => 'penghargaan, konsultan lingkungan, berita, achievement',
            ],
        ];

        foreach ($articles as $articleData) {
            Article::create($articleData);
        }

        $this->command->info('Sample articles created successfully!');
    }
}

