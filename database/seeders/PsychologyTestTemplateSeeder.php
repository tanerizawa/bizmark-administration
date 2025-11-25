<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TestTemplate;
use Carbon\Carbon;

class PsychologyTestTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Check if template already exists
        $existingTemplate = TestTemplate::where('title', 'Tes Psikologi Komprehensif')->first();
        
        if ($existingTemplate) {
            $this->command->info('⚠️  Template Psikologi sudah ada! Skipping...');
            return;
        }

        $instructions = <<<'EOT'
▸ PETUNJUK UMUM

Tes ini dirancang untuk mengukur berbagai aspek psikologis termasuk kepribadian, kemampuan kognitif, dan kecenderungan perilaku Anda. Tidak ada jawaban yang benar atau salah - jawablah sejujur mungkin sesuai dengan kondisi Anda yang sebenarnya.


▸ CARA MENGERJAKAN

[ 1 ] BAGIAN KEPRIBADIAN
    • Baca setiap pernyataan dengan seksama
    • Pilih skala yang paling sesuai dengan diri Anda
    • Jangan terlalu lama memikirkan satu pertanyaan
    • Jawab berdasarkan kondisi Anda saat ini, bukan kondisi ideal

[ 2 ] BAGIAN KOGNITIF
    • Kerjakan soal sesuai urutan
    • Perhatikan pola dan hubungan antar elemen
    • Gunakan logika dan analisis untuk menjawab
    • Tidak ada pengurangan nilai untuk jawaban salah

[ 3 ] BAGIAN SITUASIONAL
    • Bayangkan Anda berada dalam situasi yang dijelaskan
    • Pilih respon yang paling mungkin Anda lakukan
    • Pertimbangkan konteks profesional


▸ SKALA PENILAIAN

▪ KEPRIBADIAN (Skala 1-5):
    1 = Sangat Tidak Setuju
    2 = Tidak Setuju
    3 = Netral
    4 = Setuju
    5 = Sangat Setuju

▪ SITUASIONAL:
    • Pilih 1 jawaban yang paling sesuai
    • Tidak ada jawaban "benar" mutlak
    • Pertimbangkan nilai dan budaya organisasi


▸ ATURAN PENGERJAAN

▪ DIIZINKAN:
    • Mengerjakan dalam kondisi tenang
    • Membaca ulang pertanyaan
    • Melewati pertanyaan sulit (kembali lagi nanti)

▪ TIDAK DIIZINKAN:
    • Diskusi dengan orang lain
    • Menggunakan bantuan eksternal
    • Memberikan jawaban random/asal

▪ BATAS WAKTU:
    • Total waktu: 45 menit
    • Pastikan semua pertanyaan terjawab


▸ DIMENSI YANG DIUKUR

Tes ini mengukur 5 dimensi utama kepribadian (Big Five):
• Extraversion (Ekstraversi)
• Agreeableness (Keramahan)
• Conscientiousness (Kehati-hatian)
• Neuroticism (Neurotisisme)
• Openness to Experience (Keterbukaan)


▸ TIPS MENGERJAKAN

• Jawab sejujur-jujurnya, bukan jawaban yang "terdengar baik"
• Jangan terlalu lama memikirkan satu pertanyaan (maksimal 30 detik)
• Jika ragu, pilih jawaban yang paling mendekati kondisi Anda
• Konsistensi jawaban Anda akan mempengaruhi validitas hasil
• Pastikan koneksi internet stabil untuk menghindari kehilangan data

Selamat mengerjakan! Hasil tes akan digunakan untuk proses seleksi dan penempatan posisi yang sesuai dengan profil kepribadian Anda.
EOT;

        // 50 Pertanyaan Psikologi yang Comprehensive
        $questions = [
            // SECTION 1: EXTRAVERSION (10 pertanyaan)
            [
                'question_text' => 'Saya merasa berenergi ketika berada di tengah keramaian dan bertemu banyak orang.',
                'question_type' => 'rating',
                'options' => ['1 - Sangat Tidak Setuju', '2 - Tidak Setuju', '3 - Netral', '4 - Setuju', '5 - Sangat Setuju'],
                'correct_answer' => null, // No correct answer for psychology test
                'points' => 2,
                'category' => 'Extraversion',
            ],
            [
                'question_text' => 'Saya lebih suka bekerja sendiri daripada dalam tim yang besar.',
                'question_type' => 'rating',
                'options' => ['1 - Sangat Tidak Setuju', '2 - Tidak Setuju', '3 - Netral', '4 - Setuju', '5 - Sangat Setuju'],
                'correct_answer' => null,
                'points' => 2,
                'category' => 'Extraversion (Reverse)',
            ],
            [
                'question_text' => 'Saya mudah memulai percakapan dengan orang yang baru saya kenal.',
                'question_type' => 'rating',
                'options' => ['1 - Sangat Tidak Setuju', '2 - Tidak Setuju', '3 - Netral', '4 - Setuju', '5 - Sangat Setuju'],
                'correct_answer' => null,
                'points' => 2,
                'category' => 'Extraversion',
            ],
            [
                'question_text' => 'Saya merasa lelah setelah menghadiri acara sosial yang ramai.',
                'question_type' => 'rating',
                'options' => ['1 - Sangat Tidak Setuju', '2 - Tidak Setuju', '3 - Netral', '4 - Setuju', '5 - Sangat Setuju'],
                'correct_answer' => null,
                'points' => 2,
                'category' => 'Extraversion (Reverse)',
            ],
            [
                'question_text' => 'Saya senang menjadi pusat perhatian dalam suatu kelompok.',
                'question_type' => 'rating',
                'options' => ['1 - Sangat Tidak Setuju', '2 - Tidak Setuju', '3 - Netral', '4 - Setuju', '5 - Sangat Setuju'],
                'correct_answer' => null,
                'points' => 2,
                'category' => 'Extraversion',
            ],
            [
                'question_text' => 'Saya memerlukan waktu sendirian untuk mengisi ulang energi saya.',
                'question_type' => 'rating',
                'options' => ['1 - Sangat Tidak Setuju', '2 - Tidak Setuju', '3 - Netral', '4 - Setuju', '5 - Sangat Setuju'],
                'correct_answer' => null,
                'points' => 2,
                'category' => 'Extraversion (Reverse)',
            ],
            [
                'question_text' => 'Saya aktif berbicara dalam rapat atau diskusi kelompok.',
                'question_type' => 'rating',
                'options' => ['1 - Sangat Tidak Setuju', '2 - Tidak Setuju', '3 - Netral', '4 - Setuju', '5 - Sangat Setuju'],
                'correct_answer' => null,
                'points' => 2,
                'category' => 'Extraversion',
            ],
            [
                'question_text' => 'Saya merasa nyaman bekerja di ruangan yang tenang dan sepi.',
                'question_type' => 'rating',
                'options' => ['1 - Sangat Tidak Setuju', '2 - Tidak Setuju', '3 - Netral', '4 - Setuju', '5 - Sangat Setuju'],
                'correct_answer' => null,
                'points' => 2,
                'category' => 'Extraversion (Reverse)',
            ],
            [
                'question_text' => 'Saya sering mengajak orang lain untuk berkumpul atau melakukan aktivitas bersama.',
                'question_type' => 'rating',
                'options' => ['1 - Sangat Tidak Setuju', '2 - Tidak Setuju', '3 - Netral', '4 - Setuju', '5 - Sangat Setuju'],
                'correct_answer' => null,
                'points' => 2,
                'category' => 'Extraversion',
            ],
            [
                'question_text' => 'Saya lebih memilih komunikasi tertulis (email, chat) daripada tatap muka.',
                'question_type' => 'rating',
                'options' => ['1 - Sangat Tidak Setuju', '2 - Tidak Setuju', '3 - Netral', '4 - Setuju', '5 - Sangat Setuju'],
                'correct_answer' => null,
                'points' => 2,
                'category' => 'Extraversion (Reverse)',
            ],

            // SECTION 2: AGREEABLENESS (10 pertanyaan)
            [
                'question_text' => 'Saya selalu berusaha membantu orang lain yang membutuhkan, meskipun saya sedang sibuk.',
                'question_type' => 'rating',
                'options' => ['1 - Sangat Tidak Setuju', '2 - Tidak Setuju', '3 - Netral', '4 - Setuju', '5 - Sangat Setuju'],
                'correct_answer' => null,
                'points' => 2,
                'category' => 'Agreeableness',
            ],
            [
                'question_text' => 'Saya cenderung skeptis terhadap niat baik orang lain.',
                'question_type' => 'rating',
                'options' => ['1 - Sangat Tidak Setuju', '2 - Tidak Setuju', '3 - Netral', '4 - Setuju', '5 - Sangat Setuju'],
                'correct_answer' => null,
                'points' => 2,
                'category' => 'Agreeableness (Reverse)',
            ],
            [
                'question_text' => 'Saya mudah berempati dengan perasaan dan situasi orang lain.',
                'question_type' => 'rating',
                'options' => ['1 - Sangat Tidak Setuju', '2 - Tidak Setuju', '3 - Netral', '4 - Setuju', '5 - Sangat Setuju'],
                'correct_answer' => null,
                'points' => 2,
                'category' => 'Agreeableness',
            ],
            [
                'question_text' => 'Saya tidak segan mengkritik orang lain jika mereka salah, tanpa mempertimbangkan perasaan mereka.',
                'question_type' => 'rating',
                'options' => ['1 - Sangat Tidak Setuju', '2 - Tidak Setuju', '3 - Netral', '4 - Setuju', '5 - Sangat Setuju'],
                'correct_answer' => null,
                'points' => 2,
                'category' => 'Agreeableness (Reverse)',
            ],
            [
                'question_text' => 'Saya berusaha mencari solusi yang menguntungkan semua pihak dalam sebuah konflik.',
                'question_type' => 'rating',
                'options' => ['1 - Sangat Tidak Setuju', '2 - Tidak Setuju', '3 - Netral', '4 - Setuju', '5 - Sangat Setuju'],
                'correct_answer' => null,
                'points' => 2,
                'category' => 'Agreeableness',
            ],
            [
                'question_text' => 'Saya percaya bahwa setiap orang harus memperjuangkan kepentingannya sendiri terlebih dahulu.',
                'question_type' => 'rating',
                'options' => ['1 - Sangat Tidak Setuju', '2 - Tidak Setuju', '3 - Netral', '4 - Setuju', '5 - Sangat Setuju'],
                'correct_answer' => null,
                'points' => 2,
                'category' => 'Agreeableness (Reverse)',
            ],
            [
                'question_text' => 'Saya dapat dengan mudah memaafkan kesalahan orang lain.',
                'question_type' => 'rating',
                'options' => ['1 - Sangat Tidak Setuju', '2 - Tidak Setuju', '3 - Netral', '4 - Setuju', '5 - Sangat Setuju'],
                'correct_answer' => null,
                'points' => 2,
                'category' => 'Agreeableness',
            ],
            [
                'question_text' => 'Saya sering merasa kesal dengan perilaku orang lain dan sulit melupakannya.',
                'question_type' => 'rating',
                'options' => ['1 - Sangat Tidak Setuju', '2 - Tidak Setuju', '3 - Netral', '4 - Setuju', '5 - Sangat Setuju'],
                'correct_answer' => null,
                'points' => 2,
                'category' => 'Agreeableness (Reverse)',
            ],
            [
                'question_text' => 'Saya menghargai pendapat orang lain meskipun berbeda dengan pendapat saya.',
                'question_type' => 'rating',
                'options' => ['1 - Sangat Tidak Setuju', '2 - Tidak Setuju', '3 - Netral', '4 - Setuju', '5 - Sangat Setuju'],
                'correct_answer' => null,
                'points' => 2,
                'category' => 'Agreeableness',
            ],
            [
                'question_text' => 'Saya cenderung mendebat orang yang memiliki pandangan berbeda dari saya.',
                'question_type' => 'rating',
                'options' => ['1 - Sangat Tidak Setuju', '2 - Tidak Setuju', '3 - Netral', '4 - Setuju', '5 - Sangat Setuju'],
                'correct_answer' => null,
                'points' => 2,
                'category' => 'Agreeableness (Reverse)',
            ],

            // SECTION 3: CONSCIENTIOUSNESS (10 pertanyaan)
            [
                'question_text' => 'Saya selalu membuat rencana detail sebelum memulai pekerjaan atau proyek.',
                'question_type' => 'rating',
                'options' => ['1 - Sangat Tidak Setuju', '2 - Tidak Setuju', '3 - Netral', '4 - Setuju', '5 - Sangat Setuju'],
                'correct_answer' => null,
                'points' => 2,
                'category' => 'Conscientiousness',
            ],
            [
                'question_text' => 'Saya sering menunda-nunda pekerjaan hingga mendekati deadline.',
                'question_type' => 'rating',
                'options' => ['1 - Sangat Tidak Setuju', '2 - Tidak Setuju', '3 - Netral', '4 - Setuju', '5 - Sangat Setuju'],
                'correct_answer' => null,
                'points' => 2,
                'category' => 'Conscientiousness (Reverse)',
            ],
            [
                'question_text' => 'Saya teliti dalam memeriksa kembali pekerjaan saya sebelum menyelesaikannya.',
                'question_type' => 'rating',
                'options' => ['1 - Sangat Tidak Setuju', '2 - Tidak Setuju', '3 - Netral', '4 - Setuju', '5 - Sangat Setuju'],
                'correct_answer' => null,
                'points' => 2,
                'category' => 'Conscientiousness',
            ],
            [
                'question_text' => 'Ruang kerja saya biasanya berantakan dan tidak terorganisir.',
                'question_type' => 'rating',
                'options' => ['1 - Sangat Tidak Setuju', '2 - Tidak Setuju', '3 - Netral', '4 - Setuju', '5 - Sangat Setuju'],
                'correct_answer' => null,
                'points' => 2,
                'category' => 'Conscientiousness (Reverse)',
            ],
            [
                'question_text' => 'Saya dapat diandalkan untuk menyelesaikan tugas tepat waktu.',
                'question_type' => 'rating',
                'options' => ['1 - Sangat Tidak Setuju', '2 - Tidak Setuju', '3 - Netral', '4 - Setuju', '5 - Sangat Setuju'],
                'correct_answer' => null,
                'points' => 2,
                'category' => 'Conscientiousness',
            ],
            [
                'question_text' => 'Saya lebih suka bekerja secara spontan daripada mengikuti jadwal yang ketat.',
                'question_type' => 'rating',
                'options' => ['1 - Sangat Tidak Setuju', '2 - Tidak Setuju', '3 - Netral', '4 - Setuju', '5 - Sangat Setuju'],
                'correct_answer' => null,
                'points' => 2,
                'category' => 'Conscientiousness (Reverse)',
            ],
            [
                'question_text' => 'Saya menetapkan tujuan jangka panjang dan bekerja sistematis untuk mencapainya.',
                'question_type' => 'rating',
                'options' => ['1 - Sangat Tidak Setuju', '2 - Tidak Setuju', '3 - Netral', '4 - Setuju', '5 - Sangat Setuju'],
                'correct_answer' => null,
                'points' => 2,
                'category' => 'Conscientiousness',
            ],
            [
                'question_text' => 'Saya sering lupa janji atau komitmen yang telah saya buat.',
                'question_type' => 'rating',
                'options' => ['1 - Sangat Tidak Setuju', '2 - Tidak Setuju', '3 - Netral', '4 - Setuju', '5 - Sangat Setuju'],
                'correct_answer' => null,
                'points' => 2,
                'category' => 'Conscientiousness (Reverse)',
            ],
            [
                'question_text' => 'Saya berusaha keras untuk mencapai standar kualitas yang tinggi dalam pekerjaan saya.',
                'question_type' => 'rating',
                'options' => ['1 - Sangat Tidak Setuju', '2 - Tidak Setuju', '3 - Netral', '4 - Setuju', '5 - Sangat Setuju'],
                'correct_answer' => null,
                'points' => 2,
                'category' => 'Conscientiousness',
            ],
            [
                'question_text' => 'Saya merasa tidak masalah jika pekerjaan saya "cukup baik" tanpa harus sempurna.',
                'question_type' => 'rating',
                'options' => ['1 - Sangat Tidak Setuju', '2 - Tidak Setuju', '3 - Netral', '4 - Setuju', '5 - Sangat Setuju'],
                'correct_answer' => null,
                'points' => 2,
                'category' => 'Conscientiousness (Reverse)',
            ],

            // SECTION 4: NEUROTICISM / EMOTIONAL STABILITY (10 pertanyaan)
            [
                'question_text' => 'Saya sering merasa cemas atau khawatir tentang banyak hal.',
                'question_type' => 'rating',
                'options' => ['1 - Sangat Tidak Setuju', '2 - Tidak Setuju', '3 - Netral', '4 - Setuju', '5 - Sangat Setuju'],
                'correct_answer' => null,
                'points' => 2,
                'category' => 'Neuroticism',
            ],
            [
                'question_text' => 'Saya tetap tenang dalam situasi yang menekan atau penuh tekanan.',
                'question_type' => 'rating',
                'options' => ['1 - Sangat Tidak Setuju', '2 - Tidak Setuju', '3 - Netral', '4 - Setuju', '5 - Sangat Setuju'],
                'correct_answer' => null,
                'points' => 2,
                'category' => 'Emotional Stability',
            ],
            [
                'question_text' => 'Mood saya mudah berubah-ubah dari waktu ke waktu.',
                'question_type' => 'rating',
                'options' => ['1 - Sangat Tidak Setuju', '2 - Tidak Setuju', '3 - Netral', '4 - Setuju', '5 - Sangat Setuju'],
                'correct_answer' => null,
                'points' => 2,
                'category' => 'Neuroticism',
            ],
            [
                'question_text' => 'Saya dapat mengelola emosi saya dengan baik dalam situasi sulit.',
                'question_type' => 'rating',
                'options' => ['1 - Sangat Tidak Setuju', '2 - Tidak Setuju', '3 - Netral', '4 - Setuju', '5 - Sangat Setuju'],
                'correct_answer' => null,
                'points' => 2,
                'category' => 'Emotional Stability',
            ],
            [
                'question_text' => 'Saya sering merasa stres dan overwhelmed dengan tanggung jawab saya.',
                'question_type' => 'rating',
                'options' => ['1 - Sangat Tidak Setuju', '2 - Tidak Setuju', '3 - Netral', '4 - Setuju', '5 - Sangat Setuju'],
                'correct_answer' => null,
                'points' => 2,
                'category' => 'Neuroticism',
            ],
            [
                'question_text' => 'Saya jarang merasa khawatir atau gelisah tentang masa depan.',
                'question_type' => 'rating',
                'options' => ['1 - Sangat Tidak Setuju', '2 - Tidak Setuju', '3 - Netral', '4 - Setuju', '5 - Sangat Setuju'],
                'correct_answer' => null,
                'points' => 2,
                'category' => 'Emotional Stability',
            ],
            [
                'question_text' => 'Saya mudah tersinggung atau marah karena hal-hal kecil.',
                'question_type' => 'rating',
                'options' => ['1 - Sangat Tidak Setuju', '2 - Tidak Setuju', '3 - Netral', '4 - Setuju', '5 - Sangat Setuju'],
                'correct_answer' => null,
                'points' => 2,
                'category' => 'Neuroticism',
            ],
            [
                'question_text' => 'Saya cepat pulih dari kekecewaan atau kegagalan.',
                'question_type' => 'rating',
                'options' => ['1 - Sangat Tidak Setuju', '2 - Tidak Setuju', '3 - Netral', '4 - Setuju', '5 - Sangat Setuju'],
                'correct_answer' => null,
                'points' => 2,
                'category' => 'Emotional Stability',
            ],
            [
                'question_text' => 'Saya sering merasa tidak aman atau meragukan kemampuan diri sendiri.',
                'question_type' => 'rating',
                'options' => ['1 - Sangat Tidak Setuju', '2 - Tidak Setuju', '3 - Netral', '4 - Setuju', '5 - Sangat Setuju'],
                'correct_answer' => null,
                'points' => 2,
                'category' => 'Neuroticism',
            ],
            [
                'question_text' => 'Saya memiliki pandangan positif tentang hidup dan masa depan saya.',
                'question_type' => 'rating',
                'options' => ['1 - Sangat Tidak Setuju', '2 - Tidak Setuju', '3 - Netral', '4 - Setuju', '5 - Sangat Setuju'],
                'correct_answer' => null,
                'points' => 2,
                'category' => 'Emotional Stability',
            ],

            // SECTION 5: OPENNESS TO EXPERIENCE (10 pertanyaan)
            [
                'question_text' => 'Saya senang mencoba hal-hal baru dan pengalaman yang belum pernah saya lakukan.',
                'question_type' => 'rating',
                'options' => ['1 - Sangat Tidak Setuju', '2 - Tidak Setuju', '3 - Netral', '4 - Setuju', '5 - Sangat Setuju'],
                'correct_answer' => null,
                'points' => 2,
                'category' => 'Openness',
            ],
            [
                'question_text' => 'Saya lebih suka cara kerja yang sudah terbukti daripada mencoba metode baru.',
                'question_type' => 'rating',
                'options' => ['1 - Sangat Tidak Setuju', '2 - Tidak Setuju', '3 - Netral', '4 - Setuju', '5 - Sangat Setuju'],
                'correct_answer' => null,
                'points' => 2,
                'category' => 'Openness (Reverse)',
            ],
            [
                'question_text' => 'Saya memiliki imajinasi yang kuat dan sering memikirkan ide-ide kreatif.',
                'question_type' => 'rating',
                'options' => ['1 - Sangat Tidak Setuju', '2 - Tidak Setuju', '3 - Netral', '4 - Setuju', '5 - Sangat Setuju'],
                'correct_answer' => null,
                'points' => 2,
                'category' => 'Openness',
            ],
            [
                'question_text' => 'Saya adalah orang yang praktis dan tidak terlalu tertarik dengan konsep abstrak.',
                'question_type' => 'rating',
                'options' => ['1 - Sangat Tidak Setuju', '2 - Tidak Setuju', '3 - Netral', '4 - Setuju', '5 - Sangat Setuju'],
                'correct_answer' => null,
                'points' => 2,
                'category' => 'Openness (Reverse)',
            ],
            [
                'question_text' => 'Saya tertarik mempelajari budaya, seni, dan cara hidup yang berbeda.',
                'question_type' => 'rating',
                'options' => ['1 - Sangat Tidak Setuju', '2 - Tidak Setuju', '3 - Netral', '4 - Setuju', '5 - Sangat Setuju'],
                'correct_answer' => null,
                'points' => 2,
                'category' => 'Openness',
            ],
            [
                'question_text' => 'Saya merasa nyaman dengan rutinitas yang sama setiap hari.',
                'question_type' => 'rating',
                'options' => ['1 - Sangat Tidak Setuju', '2 - Tidak Setuju', '3 - Netral', '4 - Setuju', '5 - Sangat Setuju'],
                'correct_answer' => null,
                'points' => 2,
                'category' => 'Openness (Reverse)',
            ],
            [
                'question_text' => 'Saya suka berdiskusi tentang topik filosofis dan teoritis.',
                'question_type' => 'rating',
                'options' => ['1 - Sangat Tidak Setuju', '2 - Tidak Setuju', '3 - Netral', '4 - Setuju', '5 - Sangat Setuju'],
                'correct_answer' => null,
                'points' => 2,
                'category' => 'Openness',
            ],
            [
                'question_text' => 'Saya jarang tertarik dengan ide atau konsep yang tidak memiliki aplikasi praktis.',
                'question_type' => 'rating',
                'options' => ['1 - Sangat Tidak Setuju', '2 - Tidak Setuju', '3 - Netral', '4 - Setuju', '5 - Sangat Setuju'],
                'correct_answer' => null,
                'points' => 2,
                'category' => 'Openness (Reverse)',
            ],
            [
                'question_text' => 'Saya terbuka terhadap feedback dan perspektif yang menantang cara berpikir saya.',
                'question_type' => 'rating',
                'options' => ['1 - Sangat Tidak Setuju', '2 - Tidak Setuju', '3 - Netral', '4 - Setuju', '5 - Sangat Setuju'],
                'correct_answer' => null,
                'points' => 2,
                'category' => 'Openness',
            ],
            [
                'question_text' => 'Saya skeptis terhadap perubahan dan inovasi yang belum terbukti.',
                'question_type' => 'rating',
                'options' => ['1 - Sangat Tidak Setuju', '2 - Tidak Setuju', '3 - Netral', '4 - Setuju', '5 - Sangat Setuju'],
                'correct_answer' => null,
                'points' => 2,
                'category' => 'Openness (Reverse)',
            ],
        ];

        // Create test template
        $template = TestTemplate::create([
            'title' => 'Tes Psikologi Komprehensif',
            'test_type' => 'psychology',
            'description' => 'Tes psikologi berbasis Big Five Personality Traits untuk mengukur 5 dimensi utama kepribadian: Extraversion, Agreeableness, Conscientiousness, Neuroticism/Emotional Stability, dan Openness to Experience. Terdiri dari 50 pertanyaan dengan skala Likert 1-5.',
            'duration_minutes' => 45,
            'passing_score' => 0, // No passing score for psychology test
            'instructions' => $instructions,
            'questions_data' => $questions,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->command->info('✅ Template Tes Psikologi berhasil dibuat!');
        $this->command->info('   ID: ' . $template->id);
        $this->command->info('   Total Pertanyaan: ' . count($questions));
        $this->command->info('   Dimensi: Big Five (Extraversion, Agreeableness, Conscientiousness, Neuroticism, Openness)');
    }
}
