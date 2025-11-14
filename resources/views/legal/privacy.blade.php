@extends('landing.layout')

@section('title', 'Kebijakan Privasi - Bizmark.ID')
@section('description', 'Kebijakan Privasi PT Cangah Pajaratan Mandiri tentang pengumpulan, penggunaan, dan perlindungan data pribadi Anda.')

@section('content')

{{-- Hero Section --}}
<section class="relative bg-gradient-to-b from-gray-50 to-white pt-32 pb-16 lg:pt-40 lg:pb-20">
    <div class="absolute inset-0 opacity-[0.03]" style="background-image: url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'none\' fill-rule=\'evenodd\'%3E%3Cg fill=\'%231E40AF\' fill-opacity=\'1\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');"></div>
    
    <div class="container-wide relative z-10">
        <div class="max-w-4xl mx-auto text-center" data-aos="fade-up">
            {{-- Badge --}}
            <div class="inline-flex items-center gap-2 px-4 py-2 bg-blue-50 rounded-full mb-6 border border-blue-100">
                <svg class="w-4 h-4 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                <span class="text-sm font-semibold text-blue-600">Legal</span>
            </div>
            
            <h1 class="text-4xl lg:text-5xl font-bold text-gray-900 leading-tight mb-6">
                Kebijakan Privasi
            </h1>
            
            <p class="text-lg text-gray-600 leading-relaxed mb-4">
                PT Cangah Pajaratan Mandiri (Bizmark.ID)
            </p>
            
            <p class="text-sm text-gray-500">
                Terakhir diperbarui: {{ now()->translatedFormat('d F Y') }}
            </p>
        </div>
    </div>
</section>

{{-- Content Section --}}
<section class="py-16 lg:py-20 bg-white">
    <div class="container-wide">
        <div class="max-w-4xl mx-auto">
            <div class="prose prose-lg max-w-none" data-aos="fade-up">
                
                {{-- Pendahuluan --}}
                <div class="mb-12">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">Pendahuluan</h2>
                    <p class="text-gray-700 leading-relaxed">
                        PT Cangah Pajaratan Mandiri ("Bizmark.ID", "kami", "kita", atau "milik kami") berkomitmen untuk melindungi privasi dan keamanan informasi pribadi Anda. Kebijakan Privasi ini menjelaskan bagaimana kami mengumpulkan, menggunakan, mengungkapkan, dan melindungi informasi yang Anda berikan saat menggunakan layanan konsultasi perizinan industri kami.
                    </p>
                    <p class="text-gray-700 leading-relaxed mt-4">
                        Dengan menggunakan layanan kami, Anda menyetujui pengumpulan dan penggunaan informasi sesuai dengan kebijakan ini. Jika Anda tidak setuju dengan kebijakan ini, mohon untuk tidak menggunakan layanan kami.
                    </p>
                </div>

                {{-- Informasi yang Kami Kumpulkan --}}
                <div class="mb-12">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">Informasi yang Kami Kumpulkan</h2>
                    
                    <h3 class="text-xl font-semibold text-gray-900 mb-3">1. Informasi yang Anda Berikan</h3>
                    <p class="text-gray-700 leading-relaxed mb-4">
                        Kami mengumpulkan informasi yang Anda berikan secara langsung kepada kami, termasuk:
                    </p>
                    <ul class="list-disc list-inside space-y-2 text-gray-700 ml-4">
                        <li><strong>Data Identitas:</strong> Nama lengkap, alamat, nomor telepon, alamat email, nomor KTP/NPWP</li>
                        <li><strong>Data Perusahaan:</strong> Nama perusahaan, alamat kantor, bidang usaha, struktur organisasi, dokumen perusahaan (akta, SK, SIUP, dll)</li>
                        <li><strong>Data Perizinan:</strong> Informasi terkait jenis izin yang diajukan, lokasi proyek, data teknis fasilitas, dokumen lingkungan</li>
                        <li><strong>Data Komunikasi:</strong> Isi percakapan via email, WhatsApp, telepon, atau formulir kontak</li>
                        <li><strong>Data Transaksi:</strong> Riwayat pembayaran, invoice, kontrak layanan</li>
                    </ul>

                    <h3 class="text-xl font-semibold text-gray-900 mb-3 mt-6">2. Informasi yang Dikumpulkan Secara Otomatis</h3>
                    <p class="text-gray-700 leading-relaxed mb-4">
                        Saat Anda mengakses website kami, kami dapat mengumpulkan informasi teknis secara otomatis:
                    </p>
                    <ul class="list-disc list-inside space-y-2 text-gray-700 ml-4">
                        <li>Alamat IP dan lokasi geografis</li>
                        <li>Jenis browser dan sistem operasi</li>
                        <li>Halaman yang dikunjungi dan durasi kunjungan</li>
                        <li>Sumber referral (dari mana Anda menemukan kami)</li>
                        <li>Cookies dan teknologi pelacakan serupa</li>
                    </ul>
                </div>

                {{-- Penggunaan Informasi --}}
                <div class="mb-12">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">Bagaimana Kami Menggunakan Informasi Anda</h2>
                    <p class="text-gray-700 leading-relaxed mb-4">
                        Kami menggunakan informasi yang dikumpulkan untuk tujuan berikut:
                    </p>
                    <ul class="list-disc list-inside space-y-2 text-gray-700 ml-4">
                        <li><strong>Penyediaan Layanan:</strong> Memproses pengajuan perizinan, menyiapkan dokumen, berkomunikasi dengan instansi pemerintah</li>
                        <li><strong>Komunikasi:</strong> Mengirimkan update progress, notifikasi penting, konfirmasi pembayaran, laporan berkala</li>
                        <li><strong>Administrasi:</strong> Mengelola akun Anda, memproses pembayaran, mengirim invoice dan dokumen kontrak</li>
                        <li><strong>Peningkatan Layanan:</strong> Menganalisis penggunaan website, meningkatkan kualitas layanan, mengembangkan fitur baru</li>
                        <li><strong>Kepatuhan Hukum:</strong> Memenuhi kewajiban hukum, merespons permintaan otoritas, melindungi hak dan keamanan</li>
                        <li><strong>Pemasaran:</strong> Mengirimkan informasi tentang layanan baru, penawaran khusus (dengan persetujuan Anda)</li>
                    </ul>
                </div>

                {{-- Pembagian Informasi --}}
                <div class="mb-12">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">Pembagian Informasi dengan Pihak Ketiga</h2>
                    <p class="text-gray-700 leading-relaxed mb-4">
                        Kami dapat membagikan informasi Anda kepada:
                    </p>
                    <ul class="list-disc list-inside space-y-2 text-gray-700 ml-4">
                        <li><strong>Instansi Pemerintah:</strong> Kementerian, dinas, dan lembaga terkait untuk proses perizinan</li>
                        <li><strong>Mitra Konsultan:</strong> Konsultan ahli (AMDAL, HSE, teknik) yang membantu dalam proyek Anda</li>
                        <li><strong>Penyedia Layanan:</strong> Penyedia layanan IT, cloud storage, payment gateway yang mendukung operasional kami</li>
                        <li><strong>Auditor & Legal:</strong> Auditor, konsultan hukum, atau penasihat profesional untuk keperluan compliance</li>
                        <li><strong>Penegak Hukum:</strong> Saat diwajibkan oleh hukum, perintah pengadilan, atau proses hukum lainnya</li>
                    </ul>
                    <p class="text-gray-700 leading-relaxed mt-4">
                        Kami memastikan pihak ketiga terikat kewajiban kerahasiaan dan hanya menggunakan informasi sesuai instruksi kami.
                    </p>
                </div>

                {{-- Keamanan Data --}}
                <div class="mb-12">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">Keamanan Data</h2>
                    <p class="text-gray-700 leading-relaxed mb-4">
                        Kami menerapkan langkah-langkah keamanan teknis dan organisasi untuk melindungi informasi Anda:
                    </p>
                    <ul class="list-disc list-inside space-y-2 text-gray-700 ml-4">
                        <li>Enkripsi data saat transmisi (SSL/TLS)</li>
                        <li>Akses terbatas hanya untuk karyawan yang berwenang</li>
                        <li>Sistem backup dan disaster recovery</li>
                        <li>Pemantauan aktivitas mencurigakan secara berkala</li>
                        <li>Perjanjian kerahasiaan dengan karyawan dan mitra</li>
                    </ul>
                    <p class="text-gray-700 leading-relaxed mt-4">
                        Namun, tidak ada metode transmisi melalui internet atau penyimpanan elektronik yang 100% aman. Kami akan terus berupaya melindungi informasi Anda tetapi tidak dapat menjamin keamanan absolut.
                    </p>
                </div>

                {{-- Hak Anda --}}
                <div class="mb-12">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">Hak Anda</h2>
                    <p class="text-gray-700 leading-relaxed mb-4">
                        Sesuai dengan peraturan perlindungan data yang berlaku, Anda memiliki hak untuk:
                    </p>
                    <ul class="list-disc list-inside space-y-2 text-gray-700 ml-4">
                        <li><strong>Akses:</strong> Meminta salinan informasi pribadi yang kami simpan tentang Anda</li>
                        <li><strong>Koreksi:</strong> Meminta pembaruan atau perbaikan informasi yang tidak akurat</li>
                        <li><strong>Penghapusan:</strong> Meminta penghapusan informasi Anda (dengan pengecualian tertentu)</li>
                        <li><strong>Pembatasan:</strong> Meminta pembatasan pemrosesan informasi Anda</li>
                        <li><strong>Portabilitas:</strong> Meminta transfer data Anda ke penyedia layanan lain</li>
                        <li><strong>Keberatan:</strong> Menolak pemrosesan informasi untuk tujuan tertentu</li>
                        <li><strong>Penarikan Persetujuan:</strong> Menarik persetujuan yang telah diberikan sebelumnya</li>
                    </ul>
                    <p class="text-gray-700 leading-relaxed mt-4">
                        Untuk menggunakan hak Anda, silakan hubungi kami di <a href="mailto:headoffice.cpm@gmail.com" class="text-blue-600 hover:text-blue-700 font-semibold">headoffice.cpm@gmail.com</a>
                    </p>
                </div>

                {{-- Penyimpanan Data --}}
                <div class="mb-12">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">Penyimpanan dan Retensi Data</h2>
                    <p class="text-gray-700 leading-relaxed">
                        Kami menyimpan informasi pribadi Anda selama diperlukan untuk:
                    </p>
                    <ul class="list-disc list-inside space-y-2 text-gray-700 ml-4 mt-4">
                        <li>Memberikan layanan yang Anda minta</li>
                        <li>Memenuhi kewajiban hukum dan peraturan (minimal 5 tahun untuk dokumen perizinan)</li>
                        <li>Menyelesaikan sengketa dan menegakkan perjanjian</li>
                    </ul>
                    <p class="text-gray-700 leading-relaxed mt-4">
                        Setelah periode retensi berakhir, kami akan menghapus atau menganonimkan informasi Anda secara aman.
                    </p>
                </div>

                {{-- Cookies --}}
                <div class="mb-12">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">Cookies dan Teknologi Pelacakan</h2>
                    <p class="text-gray-700 leading-relaxed mb-4">
                        Website kami menggunakan cookies dan teknologi serupa untuk:
                    </p>
                    <ul class="list-disc list-inside space-y-2 text-gray-700 ml-4">
                        <li>Mengingat preferensi dan pengaturan Anda</li>
                        <li>Menganalisis traffic dan perilaku pengguna</li>
                        <li>Meningkatkan performa dan keamanan website</li>
                        <li>Menyediakan konten dan iklan yang relevan</li>
                    </ul>
                    <p class="text-gray-700 leading-relaxed mt-4">
                        Anda dapat mengatur browser untuk menolak cookies atau memberikan notifikasi saat cookies dikirim. Namun, beberapa fitur website mungkin tidak berfungsi dengan baik tanpa cookies.
                    </p>
                </div>

                {{-- Tautan Pihak Ketiga --}}
                <div class="mb-12">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">Tautan ke Website Pihak Ketiga</h2>
                    <p class="text-gray-700 leading-relaxed">
                        Website kami dapat berisi tautan ke website pihak ketiga. Kami tidak bertanggung jawab atas praktik privasi atau konten website tersebut. Kami menyarankan Anda untuk membaca kebijakan privasi setiap website yang Anda kunjungi.
                    </p>
                </div>

                {{-- Perubahan Kebijakan --}}
                <div class="mb-12">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">Perubahan Kebijakan Privasi</h2>
                    <p class="text-gray-700 leading-relaxed">
                        Kami dapat memperbarui Kebijakan Privasi ini dari waktu ke waktu. Perubahan akan diposting di halaman ini dengan tanggal "Terakhir diperbarui" yang baru. Kami akan memberitahu Anda tentang perubahan material melalui email atau notifikasi di website. Penggunaan layanan kami setelah perubahan berarti Anda menerima kebijakan yang diperbarui.
                    </p>
                </div>

                {{-- Anak-anak --}}
                <div class="mb-12">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">Privasi Anak-anak</h2>
                    <p class="text-gray-700 leading-relaxed">
                        Layanan kami tidak ditujukan untuk individu berusia di bawah 18 tahun. Kami tidak secara sengaja mengumpulkan informasi pribadi dari anak-anak. Jika Anda mengetahui bahwa seorang anak telah memberikan informasi pribadi kepada kami, silakan hubungi kami agar kami dapat mengambil tindakan yang diperlukan.
                    </p>
                </div>

                {{-- Kontak --}}
                <div class="bg-blue-50 rounded-xl p-8 border border-blue-100">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">Hubungi Kami</h2>
                    <p class="text-gray-700 leading-relaxed mb-6">
                        Jika Anda memiliki pertanyaan, kekhawatiran, atau ingin menggunakan hak Anda terkait kebijakan privasi ini, silakan hubungi kami:
                    </p>
                    <div class="space-y-3">
                        <div class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-blue-600 flex-shrink-0 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                            <div>
                                <p class="text-sm text-gray-600">Email</p>
                                <a href="mailto:headoffice.cpm@gmail.com" class="text-blue-600 hover:text-blue-700 font-semibold">headoffice.cpm@gmail.com</a>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-blue-600 flex-shrink-0 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                            </svg>
                            <div>
                                <p class="text-sm text-gray-600">Telepon</p>
                                <a href="tel:+6281382605030" class="text-blue-600 hover:text-blue-700 font-semibold">+62 813 8260 5030</a>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-blue-600 flex-shrink-0 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            <div>
                                <p class="text-sm text-gray-600">Alamat</p>
                                <p class="text-gray-900 font-semibold">PT Cangah Pajaratan Mandiri</p>
                                <p class="text-gray-700">Karawang, Jawa Barat 41361<br>Indonesia</p>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</section>

{{-- CTA Section --}}
<section class="py-16 bg-gray-50">
    <div class="container-wide">
        <div class="max-w-4xl mx-auto text-center" data-aos="fade-up">
            <h2 class="text-2xl lg:text-3xl font-bold text-gray-900 mb-4">
                Ada Pertanyaan tentang Privasi?
            </h2>
            <p class="text-lg text-gray-600 mb-8">
                Tim kami siap membantu menjawab pertanyaan Anda tentang bagaimana kami melindungi data Anda
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="mailto:headoffice.cpm@gmail.com" class="inline-flex items-center justify-center px-8 py-4 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-all duration-300 shadow-lg hover:shadow-xl">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                    Kirim Email
                </a>
                <a href="{{ route('landing') }}" class="inline-flex items-center justify-center px-8 py-4 bg-white text-gray-700 font-semibold rounded-lg border-2 border-gray-300 hover:border-blue-600 hover:text-blue-600 transition-all duration-300">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    Kembali ke Beranda
                </a>
            </div>
        </div>
    </div>
</section>

@include('landing.sections.footer')

@endsection
