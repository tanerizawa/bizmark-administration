@extends('beta-tester.layouts.app')

@section('title', 'Program Beta Tester')

@section('content')
    <!-- Hero Section -->
    <section class="hero-gradient py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <p class="inline-flex items-center gap-2 px-4 py-2 rounded-full text-sm font-semibold mb-4"
                   style="background: rgba(0, 122, 255, 0.12); color: var(--apple-blue);">
                    <i class="fas fa-shield-alt"></i>
                    PT Cangah Pajaratan Mandiri • RegTech Platform
                </p>
                <h1 class="text-4xl md:text-5xl lg:text-6xl font-extrabold mb-6 leading-tight">
                    Program <span style="background: linear-gradient(135deg, var(--apple-blue) 0%, var(--apple-green) 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">Beta Tester</span> Bizmark.ID
                </h1>
                <p class="text-xl md:text-2xl mb-8 max-w-3xl mx-auto" style="color: var(--light-text-secondary);">
                    Uji aplikasi perizinan terintegrasi Bizmark.ID selama 1 bulan, laporkan temuan melalui GitLab,
                    dan bantu kami menyiapkan platform RegTech berbasis AI sebelum peluncuran luas.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="{{ route('beta-tester.register') }}" class="btn-primary">
                        <i class="fas fa-user-plus"></i>
                        <span>Daftar Sebagai Beta Tester</span>
                    </a>
                    <a href="#info" class="btn-secondary">
                        <i class="fas fa-info-circle"></i>
                        <span>Pelajari Program</span>
                    </a>
                </div>
                <div class="flex flex-wrap justify-center gap-4 mt-8 text-sm font-semibold" style="color: var(--light-text-secondary);">
                    <span class="inline-flex items-center gap-2">
                        <i class="fas fa-clock text-blue-500"></i> Durasi 1 Bulan
                    </span>
                    <span class="inline-flex items-center gap-2">
                        <i class="fas fa-file-signature text-green-500"></i> Pakta Integritas & NDA Digital
                    </span>
                    <span class="inline-flex items-center gap-2">
                        <i class="fab fa-gitlab text-orange-500"></i> Pelaporan via GitLab
                    </span>
                </div>
            </div>
        </div>
    </section>

    <!-- Overview & Benefits -->
    <section id="info" class="py-16" style="background: var(--light-bg);">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-12">
            <div class="grid lg:grid-cols-2 gap-8">
                <div class="card p-8">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-700 rounded-lg flex items-center justify-center">
                            <i class="fas fa-building text-white text-xl"></i>
                        </div>
                        <h2 class="text-2xl font-bold">Profil Bizmark.ID</h2>
                    </div>
                    <p class="text-lg mb-4" style="color: var(--light-text-secondary);">
                        Bizmark.ID adalah platform layanan perizinan digital (RegTech) yang dikembangkan oleh PT Cangah
                        Pajaratan Mandiri. Sistem ini mendigitalisasi proses OSS/RBA, perizinan lingkungan, dan dokumen operasional
                        lain dengan dukungan pakar hukum, regulasi, dan tenaga ahli bersertifikat.
                    </p>
                    <ul class="space-y-3" style="color: var(--light-text-secondary);">
                        <li class="flex gap-3">
                            <i class="fas fa-check text-green-500 mt-1"></i>
                            Digitalisasi alur perizinan, pengelolaan dokumen, dan pemantauan status secara real time.
                        </li>
                        <li class="flex gap-3">
                            <i class="fas fa-check text-green-500 mt-1"></i>
                            Fondasi tahap I berbasis web yang akan dikembangkan ke aplikasi mobile Android/iOS.
                        </li>
                        <li class="flex gap-3">
                            <i class="fas fa-check text-green-500 mt-1"></i>
                            Rencana integrasi kecerdasan buatan untuk analisis kebutuhan perizinan yang tetap diawasi tenaga ahli.
                        </li>
                    </ul>
                </div>
                <div class="card p-8">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-green-700 rounded-lg flex items-center justify-center">
                            <i class="fas fa-bullseye text-white text-xl"></i>
                        </div>
                        <h2 class="text-2xl font-bold">Maksud Program</h2>
                    </div>
                    <p class="text-lg mb-4" style="color: var(--light-text-secondary);">
                        Program beta tester disusun berdasarkan Terms of Reference (ToR) resmi untuk memastikan
                        kesiapan modul landing page, client panel, dan admin panel simulatif sebelum memasuki tahap produksi.
                    </p>
                    <ul class="space-y-3" style="color: var(--light-text-secondary);">
                        <li class="flex gap-3">
                            <i class="fas fa-check text-green-500 mt-1"></i>
                            Mengidentifikasi bug, ketidaksesuaian alur, dan kebutuhan UX/UI.
                        </li>
                        <li class="flex gap-3">
                            <i class="fas fa-check text-green-500 mt-1"></i>
                            Menguji kelogisan proses perizinan end-to-end menggunakan data simulasi.
                        </li>
                        <li class="flex gap-3">
                            <i class="fas fa-check text-green-500 mt-1"></i>
                            Menyusun dokumentasi issue terstruktur di GitLab agar siap untuk fase pengembangan berikutnya.
                        </li>
                    </ul>
                </div>
            </div>

            <div class="grid md:grid-cols-3 gap-8" id="benefits">
                <div class="card p-6 text-center">
                    <div class="w-16 h-16 bg-gradient-to-br from-purple-500 to-purple-700 rounded-xl flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-graduation-cap text-white text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-2">Sertifikat & Pengalaman</h3>
                    <p style="color: var(--light-text-secondary);">
                        Sertifikat resmi dan pengalaman praktik QA di startup RegTech.
                    </p>
                </div>
                <div class="card p-6 text-center">
                    <div class="w-16 h-16 bg-gradient-to-br from-green-500 to-green-700 rounded-xl flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-hand-holding-usd text-white text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-2">Biaya Operasional</h3>
                    <p style="color: var(--light-text-secondary);">
                        Uang saku/kompensasi yang diberikan sebagai bentuk apresiasi partisipasi.
                    </p>
                </div>
                <div class="card p-6 text-center">
                    <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-blue-700 rounded-xl flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-network-wired text-white text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-2">Akses Sistem Lengkap</h3>
                    <p style="color: var(--light-text-secondary);">
                        Eksplor landing page, client panel, admin panel simulatif, dan repositori GitLab.
                    </p>
                </div>
            </div>

            <div class="card p-8">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-12 h-12 bg-gradient-to-br from-orange-500 to-orange-700 rounded-lg flex items-center justify-center">
                        <i class="fas fa-user-check text-white text-xl"></i>
                    </div>
                    <h2 class="text-3xl font-bold">Kriteria Peserta</h2>
                </div>
                <div class="grid md:grid-cols-2 gap-6">
                    <ul class="space-y-3" style="color: var(--light-text-secondary);">
                        <li class="flex items-start gap-3">
                            <div class="w-6 h-6 bg-green-500 rounded-full flex items-center justify-center flex-shrink-0 mt-1">
                                <i class="fas fa-check text-white text-xs"></i>
                            </div>
                            Mahasiswa aktif (D3/S1) dengan ketertarikan pada TI, SI, RPL, atau UX/UI.
                        </li>
                        <li class="flex items-start gap-3">
                            <div class="w-6 h-6 bg-green-500 rounded-full flex items-center justify-center flex-shrink-0 mt-1">
                                <i class="fas fa-check text-white text-xs"></i>
                            </div>
                            Memiliki perangkat & koneksi internet memadai serta akun GitLab (atau bersedia membuat).
                        </li>
                        <li class="flex items-start gap-3">
                            <div class="w-6 h-6 bg-green-500 rounded-full flex items-center justify-center flex-shrink-0 mt-1">
                                <i class="fas fa-check text-white text-xs"></i>
                            </div>
                            Bersedia mengikuti seluruh jadwal orientasi, pengujian, dan retesting selama ±1 bulan.
                        </li>
                    </ul>
                    <ul class="space-y-3" style="color: var(--light-text-secondary);">
                        <li class="flex items-start gap-3">
                            <div class="w-6 h-6 bg-green-500 rounded-full flex items-center justify-center flex-shrink-0 mt-1">
                                <i class="fas fa-check text-white text-xs"></i>
                            </div>
                            Berkomitmen menjaga kerahasiaan data simulasi dan menandatangani Pakta Integritas & NDA.
                        </li>
                        <li class="flex items-start gap-3">
                            <div class="w-6 h-6 bg-green-500 rounded-full flex items-center justify-center flex-shrink-0 mt-1">
                                <i class="fas fa-check text-white text-xs"></i>
                            </div>
                            Mampu menyusun laporan bug/UX improvement secara objektif sesuai template GitLab.
                        </li>
                        <li class="flex items-start gap-3">
                            <div class="w-6 h-6 bg-green-500 rounded-full flex items-center justify-center flex-shrink-0 mt-1">
                                <i class="fas fa-check text-white text-xs"></i>
                            </div>
                            Mengikuti program atas nama pribadi (tidak membentuk hubungan kerja).
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- Scope Section -->
    <section class="py-16" style="background: var(--light-bg-secondary);">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6 mb-12">
                <div>
                    <p class="text-sm uppercase tracking-widest text-blue-500 font-semibold mb-2">Ruang Lingkup Pengujian</p>
                    <h2 class="text-3xl font-bold" style="color: var(--light-text-primary);">Apa yang akan Anda uji?</h2>
                    <p class="mt-3" style="color: var(--light-text-secondary);">
                        Berdasarkan ToR, pengujian dibagi menjadi beberapa fokus utama berikut.
                    </p>
                </div>
                <a href="{{ route('beta-tester.register') }}" class="btn-secondary">
                    <i class="fas fa-arrow-right"></i>
                    <span>Ikuti Program</span>
                </a>
            </div>

            <div class="grid md:grid-cols-3 gap-8">
                <div class="card p-6 h-full">
                    <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-700 rounded-lg flex items-center justify-center mb-4">
                        <i class="fas fa-tasks text-white"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-3">Pengujian Fungsional</h3>
                    <p style="color: var(--light-text-secondary);">
                        Uji proses registrasi/login, pengisian form perizinan simulatif, unggah dokumen, dan validasi input.
                        Laporkan perilaku yang tidak sesuai melalui template Bug Report.
                    </p>
                </div>
                <div class="card p-6 h-full">
                    <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-green-700 rounded-lg flex items-center justify-center mb-4">
                        <i class="fas fa-project-diagram text-white"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-3">Pengujian Alur Proses</h3>
                    <p style="color: var(--light-text-secondary);">
                        Evaluasi kelogisan urutan langkah, relevansi data, serta pesan error. Pastikan traceability data
                        sesuai prakarsa Bizmark.ID sebagai platform perizinan terintegrasi.
                    </p>
                </div>
                <div class="card p-6 h-full">
                    <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-purple-700 rounded-lg flex items-center justify-center mb-4">
                        <i class="fas fa-icons text-white"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-3">UX/UI & Aksesibilitas</h3>
                    <p style="color: var(--light-text-secondary);">
                        Berikan UX Feedback terkait tampilan, istilah, ukuran teks, struktur menu, dan kenyamanan penggunaan desktop/mobile.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Responsibilities -->
    <section class="py-16" style="background: var(--light-bg);">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-700 rounded-lg flex items-center justify-center">
                    <i class="fas fa-balance-scale text-white text-xl"></i>
                </div>
                <h2 class="text-3xl font-bold">Hak & Tanggung Jawab</h2>
            </div>
            <div class="grid lg:grid-cols-2 gap-8">
                <div class="card p-6">
                    <h3 class="text-xl font-bold mb-3">Bizmark.ID / PT Cangah Pajaratan Mandiri</h3>
                    <ul class="space-y-3" style="color: var(--light-text-secondary);">
                        <li class="flex gap-3"><i class="fas fa-check text-green-500 mt-1"></i>Menyediakan akses aplikasi, akun GitLab, dan dokumen resmi (ToR, Pakta Integritas, NDA).</li>
                        <li class="flex gap-3"><i class="fas fa-check text-green-500 mt-1"></i>Meninjau dan menindaklanjuti issue yang dilaporkan, termasuk penjadwalan retesting.</li>
                        <li class="flex gap-3"><i class="fas fa-check text-green-500 mt-1"></i>Memberikan sertifikat dan biaya operasional bagi peserta yang memenuhi ketentuan.</li>
                    </ul>
                </div>
                <div class="card p-6">
                    <h3 class="text-xl font-bold mb-3">Peserta Beta Tester</h3>
                    <ul class="space-y-3" style="color: var(--light-text-secondary);">
                        <li class="flex gap-3"><i class="fas fa-check text-green-500 mt-1"></i>Menandatangani Pakta Integritas & NDA, menjaga kerahasiaan data simulasi.</li>
                        <li class="flex gap-3"><i class="fas fa-check text-green-500 mt-1"></i>Mengikuti jadwal orientasi, pengujian, dan retesting; menggunakan template issue resmi.</li>
                        <li class="flex gap-3"><i class="fas fa-check text-green-500 mt-1"></i>Melaporkan temuan secara objektif, tidak memanipulasi data, dan menjaga etika testing.</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- Documents -->
    <section class="py-16" style="background: var(--light-bg-secondary);">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center gap-3 mb-12">
                <div class="w-12 h-12 bg-gradient-to-br from-orange-500 to-orange-700 rounded-lg flex items-center justify-center">
                    <i class="fas fa-file-contract text-white text-xl"></i>
                </div>
                <h2 class="text-3xl font-bold">Dokumen & Komitmen Program</h2>
            </div>
            <div class="grid md:grid-cols-3 gap-8">
                <div class="card p-6 h-full">
                    <h3 class="text-xl font-bold mb-2">Pakta Integritas</h3>
                    <p style="color: var(--light-text-secondary);">
                        Menegaskan kejujuran, tanggung jawab, dan larangan menyalahgunakan akses aplikasi.
                        Pelanggaran dapat mengakibatkan pencabutan hak dan tindakan hukum.
                    </p>
                </div>
                <div class="card p-6 h-full">
                    <h3 class="text-xl font-bold mb-2">Perjanjian Kerahasiaan (NDA)</h3>
                    <p style="color: var(--light-text-secondary);">
                        Mengatur kewajiban menjaga informasi rahasia hingga 3 tahun setelah program berakhir,
                        termasuk pembatasan akses teknis dan hak kekayaan intelektual.
                    </p>
                </div>
                <div class="card p-6 h-full">
                    <h3 class="text-xl font-bold mb-2">Terms of Reference (ToR)</h3>
                    <p style="color: var(--light-text-secondary);">
                        Dokumen rujukan utama mengenai tujuan, metodologi, jadwal mingguan, ruang lingkup, dan peran setiap pihak.
                        Disediakan dalam dashboard peserta untuk dibaca dan ditandatangani secara digital.
                    </p>
                </div>
            </div>
            <p class="mt-6 text-sm" style="color: var(--light-text-secondary);">
                Semua dokumen dapat dibaca melalui dashboard peserta dalam format digital, lengkap dengan tanda tangan elektronik dan catatan hash SHA-256.
            </p>
        </div>
    </section>

    <!-- GitLab Workflow -->
    <section class="py-16" style="background: var(--light-bg);">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-purple-700 rounded-lg flex items-center justify-center">
                    <i class="fab fa-gitlab text-white text-xl"></i>
                </div>
                <h2 class="text-3xl font-bold">Alur Pelaporan di GitLab</h2>
            </div>
            <div class="grid lg:grid-cols-3 gap-6">
                <div class="card p-6">
                    <h3 class="font-bold text-lg mb-2">1. Pilih Template</h3>
                    <p style="color: var(--light-text-secondary);">
                        Gunakan template <strong>Bug Report</strong>, <strong>UX Feedback</strong>, atau <strong>Improvement Suggestion</strong>
                        yang tersedia di repositori <em>bizmark-beta-testing</em>.
                    </p>
                </div>
                <div class="card p-6">
                    <h3 class="font-bold text-lg mb-2">2. Dokumentasikan Temuan</h3>
                    <p style="color: var(--light-text-secondary);">
                        Sertakan URL, perangkat, langkah reproduksi, expected vs actual result, dan lampiran screenshot/video.
                        Tandai tingkat keparahan sesuai template.
                    </p>
                </div>
                <div class="card p-6">
                    <h3 class="font-bold text-lg mb-2">3. Verifikasi & Retesting</h3>
                    <p style="color: var(--light-text-secondary);">
                        Tim Bizmark.ID melakukan triage. Setelah perbaikan, peserta melakukan retesting dan menutup issue
                        dengan status <em>verified</em>.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Timeline -->
    <section class="py-16" style="background: var(--light-bg-secondary);">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center gap-3 mb-8">
                <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-700 rounded-lg flex items-center justify-center">
                    <i class="fas fa-calendar-alt text-white text-xl"></i>
                </div>
                <h2 class="text-3xl font-bold">Timeline Program (4 Minggu)</h2>
            </div>
            <div class="space-y-6">
                <div class="card p-6 flex gap-4 items-start">
                    <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-blue-700 rounded-xl flex items-center justify-center">
                        <span class="text-2xl font-bold text-white">1</span>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold mb-2">Minggu 1 – Orientasi & Onboarding</h3>
                        <p style="color: var(--light-text-secondary);">
                            Penjelasan profil Bizmark.ID, ruang lingkup program, akses GitLab, penandatanganan Pakta Integritas dan NDA,
                            serta pengujian awal aksesibilitas.
                        </p>
                    </div>
                </div>
                <div class="card p-6 flex gap-4 items-start">
                    <div class="w-16 h-16 bg-gradient-to-br from-green-500 to-green-700 rounded-xl flex items-center justify-center">
                        <span class="text-2xl font-bold text-white">2</span>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold mb-2">Minggu 2 – Functional Testing</h3>
                        <p style="color: var(--light-text-secondary);">
                            Fokus pada form pengajuan, upload dokumen, validasi, konsistensi data, serta penyusunan Bug Report lengkap.
                        </p>
                    </div>
                </div>
                <div class="card p-6 flex gap-4 items-start">
                    <div class="w-16 h-16 bg-gradient-to-br from-purple-500 to-purple-700 rounded-xl flex items-center justify-center">
                        <span class="text-2xl font-bold text-white">3</span>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold mb-2">Minggu 3 – UX/UI & Flow Review</h3>
                        <p style="color: var(--light-text-secondary);">
                            Penilaian kemudahan penggunaan, istilah, tata letak menu, serta penyampaian UX Feedback dan Improvement Suggestion.
                        </p>
                    </div>
                </div>
                <div class="card p-6 flex gap-4 items-start">
                    <div class="w-16 h-16 bg-gradient-to-br from-orange-500 to-orange-700 rounded-xl flex items-center justify-center">
                        <span class="text-2xl font-bold text-white">4</span>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold mb-2">Minggu 4 – Retesting & Penutupan</h3>
                        <p style="color: var(--light-text-secondary);">
                            Verifikasi perbaikan pada GitLab, rekapitulasi issue, dan penyerahan sertifikat serta biaya operasional.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Registration Process -->
    <section class="py-16" style="background: var(--light-bg);">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="card p-8">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-12 h-12 bg-gradient-to-br from-orange-500 to-orange-700 rounded-lg flex items-center justify-center">
                        <i class="fas fa-clipboard-list text-white text-xl"></i>
                    </div>
                    <h2 class="text-3xl font-bold">Proses Pendaftaran</h2>
                </div>
                <div class="grid md:grid-cols-4 gap-6">
                    <div class="text-center">
                        <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-blue-700 rounded-full flex items-center justify-center mx-auto mb-3">
                            <span class="text-2xl font-bold text-white">1</span>
                        </div>
                        <h4 class="font-bold mb-2">Isi Form</h4>
                        <p class="text-sm" style="color: var(--light-text-secondary);">Lengkapi data diri, akademik, kontak, dan motivasi.</p>
                    </div>
                    <div class="text-center">
                        <div class="w-16 h-16 bg-gradient-to-br from-green-500 to-green-700 rounded-full flex items-center justify-center mx-auto mb-3">
                            <span class="text-2xl font-bold text-white">2</span>
                        </div>
                        <h4 class="font-bold mb-2">Verifikasi Awal</h4>
                        <p class="text-sm" style="color: var(--light-text-secondary);">Tim melakukan screening dan mengirim akses dashboard.</p>
                    </div>
                    <div class="text-center">
                        <div class="w-16 h-16 bg-gradient-to-br from-purple-500 to-purple-700 rounded-full flex items-center justify-center mx-auto mb-3">
                            <span class="text-2xl font-bold text-white">3</span>
                        </div>
                        <h4 class="font-bold mb-2">Tanda Tangan Digital</h4>
                        <p class="text-sm" style="color: var(--light-text-secondary);">Pakta Integritas & NDA ditandatangani langsung melalui sistem.</p>
                    </div>
                    <div class="text-center">
                        <div class="w-16 h-16 bg-gradient-to-br from-orange-500 to-orange-700 rounded-full flex items-center justify-center mx-auto mb-3">
                            <span class="text-2xl font-bold text-white">4</span>
                        </div>
                        <h4 class="font-bold mb-2">Mulai Testing</h4>
                        <p class="text-sm" style="color: var(--light-text-secondary);">Dapatkan akses GitLab, jadwal, dan panduan lengkap.</p>
                    </div>
                </div>
            </div>

            <!-- CTA -->
            <div class="mt-12 text-center space-y-4">
                <a href="{{ route('beta-tester.register') }}" class="btn-primary text-lg">
                    <i class="fas fa-rocket"></i>
                    <span>Daftar Sebagai Beta Tester Sekarang</span>
                </a>
                <p style="color: var(--light-text-secondary);">
                    Punya pertanyaan? Hubungi tim kami melalui email resmi yang tertera pada dashboard setelah Anda terdaftar.
                </p>
            </div>
        </div>
    </section>
@endsection
