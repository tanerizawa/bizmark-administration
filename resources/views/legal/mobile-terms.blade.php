@extends('mobile-landing.layouts.content')

@section('title', 'Syarat & Ketentuan - Bizmark.ID')
@section('meta_description', 'Syarat dan ketentuan penggunaan layanan PT Cangah Pajaratan Mandiri (Bizmark.ID)')

@section('content')

<!-- Hero Section -->
<section class="magazine-section bg-gradient-to-br from-blue-50 to-white">
    <div class="content-container text-center">
        <div class="w-16 h-16 rounded-full bg-gradient-to-br from-[#0077B5] to-[#005582] flex items-center justify-center mx-auto mb-4 text-white text-2xl">
            <i class="fas fa-file-contract"></i>
        </div>
        <h1 class="headline text-3xl text-gray-900 mb-3">
            Syarat & Ketentuan
        </h1>
        <p class="text-sm text-gray-600 mb-2">
            PT Cangah Pajaratan Mandiri (Bizmark.ID)
        </p>
        <p class="text-xs text-gray-500">
            Terakhir diperbarui: {{ now()->translatedFormat('d F Y') }}
        </p>
    </div>
</section>

<!-- Content Section -->
<section class="magazine-section bg-white">
    <div class="content-container">
        <div class="prose prose-sm max-w-none">
            
            <div class="bg-blue-50 border-l-4 border-[#0077B5] p-4 rounded-r-xl mb-6">
                <p class="text-sm text-gray-700 leading-relaxed">
                    Dengan menggunakan layanan Bizmark.ID, Anda setuju untuk terikat dengan syarat dan ketentuan berikut. 
                    Harap membaca dengan seksama sebelum menggunakan layanan kami.
                </p>
            </div>

            <h2 class="text-xl font-bold text-gray-900 mb-3 mt-6">1. Definisi</h2>
            <ul class="space-y-2 mb-6">
                <li class="flex items-start gap-2 text-sm text-gray-700">
                    <i class="fas fa-circle text-[#0077B5] text-xs mt-1.5 flex-shrink-0"></i>
                    <span><strong>"Kami"</strong> merujuk pada PT Cangah Pajaratan Mandiri (Bizmark.ID)</span>
                </li>
                <li class="flex items-start gap-2 text-sm text-gray-700">
                    <i class="fas fa-circle text-[#0077B5] text-xs mt-1.5 flex-shrink-0"></i>
                    <span><strong>"Anda"</strong> merujuk pada pengguna layanan kami</span>
                </li>
                <li class="flex items-start gap-2 text-sm text-gray-700">
                    <i class="fas fa-circle text-[#0077B5] text-xs mt-1.5 flex-shrink-0"></i>
                    <span><strong>"Layanan"</strong> merujuk pada semua jasa konsultasi dan perizinan yang kami tawarkan</span>
                </li>
            </ul>

            <h2 class="text-xl font-bold text-gray-900 mb-3 mt-6">2. Penggunaan Layanan</h2>
            <p class="text-sm text-gray-700 leading-relaxed mb-4">
                Dengan menggunakan layanan kami, Anda setuju untuk:
            </p>
            <ul class="space-y-2 mb-6">
                <li class="flex items-start gap-2 text-sm text-gray-700">
                    <i class="fas fa-check text-green-500 mt-1 flex-shrink-0"></i>
                    <span>Memberikan informasi yang akurat dan lengkap</span>
                </li>
                <li class="flex items-start gap-2 text-sm text-gray-700">
                    <i class="fas fa-check text-green-500 mt-1 flex-shrink-0"></i>
                    <span>Mematuhi semua peraturan dan hukum yang berlaku</span>
                </li>
                <li class="flex items-start gap-2 text-sm text-gray-700">
                    <i class="fas fa-check text-green-500 mt-1 flex-shrink-0"></i>
                    <span>Bertanggung jawab atas keakuratan dokumen yang diserahkan</span>
                </li>
                <li class="flex items-start gap-2 text-sm text-gray-700">
                    <i class="fas fa-check text-green-500 mt-1 flex-shrink-0"></i>
                    <span>Membayar biaya layanan sesuai kesepakatan</span>
                </li>
            </ul>

            <h2 class="text-xl font-bold text-gray-900 mb-3 mt-6">3. Lingkup Layanan</h2>
            <p class="text-sm text-gray-700 leading-relaxed mb-4">
                Kami menyediakan layanan konsultasi dan pengurusan perizinan, termasuk namun tidak terbatas pada:
            </p>
            <ul class="space-y-2 mb-6">
                <li class="flex items-start gap-2 text-sm text-gray-700">
                    <i class="fas fa-briefcase text-[#0077B5] mt-1 flex-shrink-0"></i>
                    <span>Perizinan Limbah B3</span>
                </li>
                <li class="flex items-start gap-2 text-sm text-gray-700">
                    <i class="fas fa-briefcase text-[#0077B5] mt-1 flex-shrink-0"></i>
                    <span>AMDAL, UKL-UPL, SPPL</span>
                </li>
                <li class="flex items-start gap-2 text-sm text-gray-700">
                    <i class="fas fa-briefcase text-[#0077B5] mt-1 flex-shrink-0"></i>
                    <span>OSS & NIB</span>
                </li>
                <li class="flex items-start gap-2 text-sm text-gray-700">
                    <i class="fas fa-briefcase text-[#0077B5] mt-1 flex-shrink-0"></i>
                    <span>PBG & SLF</span>
                </li>
                <li class="flex items-start gap-2 text-sm text-gray-700">
                    <i class="fas fa-briefcase text-[#0077B5] mt-1 flex-shrink-0"></i>
                    <span>Konsultasi lingkungan dan perizinan lainnya</span>
                </li>
            </ul>

            <h2 class="text-xl font-bold text-gray-900 mb-3 mt-6">4. Biaya dan Pembayaran</h2>
            <p class="text-sm text-gray-700 leading-relaxed mb-4">
                Ketentuan biaya dan pembayaran:
            </p>
            <ul class="space-y-2 mb-6">
                <li class="flex items-start gap-2 text-sm text-gray-700">
                    <i class="fas fa-money-bill-wave text-green-600 mt-1 flex-shrink-0"></i>
                    <span>Biaya layanan akan diinformasikan dalam proposal/penawaran</span>
                </li>
                <li class="flex items-start gap-2 text-sm text-gray-700">
                    <i class="fas fa-money-bill-wave text-green-600 mt-1 flex-shrink-0"></i>
                    <span>Pembayaran dilakukan sesuai termin yang disepakati</span>
                </li>
                <li class="flex items-start gap-2 text-sm text-gray-700">
                    <i class="fas fa-money-bill-wave text-green-600 mt-1 flex-shrink-0"></i>
                    <span>Keterlambatan pembayaran dapat mempengaruhi proses pengurusan</span>
                </li>
            </ul>

            <h2 class="text-xl font-bold text-gray-900 mb-3 mt-6">5. Tanggung Jawab</h2>
            <p class="text-sm text-gray-700 leading-relaxed mb-4">
                <strong>Tanggung Jawab Kami:</strong>
            </p>
            <ul class="space-y-2 mb-4">
                <li class="flex items-start gap-2 text-sm text-gray-700">
                    <i class="fas fa-shield-alt text-[#0077B5] mt-1 flex-shrink-0"></i>
                    <span>Memberikan layanan profesional sesuai standar industri</span>
                </li>
                <li class="flex items-start gap-2 text-sm text-gray-700">
                    <i class="fas fa-shield-alt text-[#0077B5] mt-1 flex-shrink-0"></i>
                    <span>Menjaga kerahasiaan data dan informasi klien</span>
                </li>
                <li class="flex items-start gap-2 text-sm text-gray-700">
                    <i class="fas fa-shield-alt text-[#0077B5] mt-1 flex-shrink-0"></i>
                    <span>Memproses perizinan sesuai prosedur yang berlaku</span>
                </li>
            </ul>
            
            <p class="text-sm text-gray-700 leading-relaxed mb-4">
                <strong>Tanggung Jawab Anda:</strong>
            </p>
            <ul class="space-y-2 mb-6">
                <li class="flex items-start gap-2 text-sm text-gray-700">
                    <i class="fas fa-user-check text-[#0077B5] mt-1 flex-shrink-0"></i>
                    <span>Menyediakan dokumen yang diperlukan tepat waktu</span>
                </li>
                <li class="flex items-start gap-2 text-sm text-gray-700">
                    <i class="fas fa-user-check text-[#0077B5] mt-1 flex-shrink-0"></i>
                    <span>Memastikan kebenaran informasi yang diberikan</span>
                </li>
                <li class="flex items-start gap-2 text-sm text-gray-700">
                    <i class="fas fa-user-check text-[#0077B5] mt-1 flex-shrink-0"></i>
                    <span>Melakukan pembayaran sesuai kesepakatan</span>
                </li>
            </ul>

            <h2 class="text-xl font-bold text-gray-900 mb-3 mt-6">6. Pembatalan dan Pengembalian Dana</h2>
            <p class="text-sm text-gray-700 leading-relaxed mb-6">
                Pembatalan layanan harus diajukan secara tertulis. Pengembalian dana (jika ada) akan diproses sesuai 
                dengan tahapan pekerjaan yang telah diselesaikan dan biaya yang telah dikeluarkan.
            </p>

            <h2 class="text-xl font-bold text-gray-900 mb-3 mt-6">7. Kerahasiaan</h2>
            <p class="text-sm text-gray-700 leading-relaxed mb-6">
                Kami berkomitmen untuk menjaga kerahasiaan semua informasi yang diberikan oleh klien. 
                Informasi hanya akan digunakan untuk keperluan pengurusan perizinan dan tidak akan dibagikan 
                kepada pihak ketiga tanpa persetujuan tertulis dari Anda.
            </p>

            <h2 class="text-xl font-bold text-gray-900 mb-3 mt-6">8. Hukum yang Berlaku</h2>
            <p class="text-sm text-gray-700 leading-relaxed mb-6">
                Syarat dan ketentuan ini diatur dan ditafsirkan sesuai dengan hukum Republik Indonesia. 
                Setiap perselisihan akan diselesaikan melalui musyawarah atau melalui jalur hukum yang berlaku.
            </p>

            <h2 class="text-xl font-bold text-gray-900 mb-3 mt-6">9. Perubahan Syarat & Ketentuan</h2>
            <p class="text-sm text-gray-700 leading-relaxed mb-6">
                Kami berhak untuk mengubah syarat dan ketentuan ini sewaktu-waktu. Perubahan akan berlaku 
                efektif setelah dipublikasikan di website kami. Penggunaan layanan setelah perubahan berarti 
                Anda menyetujui syarat dan ketentuan yang telah diperbarui.
            </p>

        </div>
    </div>
</section>

<!-- Contact Section -->
<section class="magazine-section bg-gradient-to-br from-gray-50 to-white">
    <div class="content-container">
        <div class="bg-white rounded-2xl p-6 shadow-lg border-2 border-[#0077B5]">
            <div class="text-center mb-4">
                <i class="fas fa-question-circle text-4xl text-[#0077B5] mb-3"></i>
                <h3 class="text-xl font-bold text-gray-900 mb-2">Butuh Klarifikasi?</h3>
                <p class="text-sm text-gray-600">
                    Hubungi kami untuk penjelasan lebih lanjut mengenai syarat dan ketentuan:
                </p>
            </div>
            <div class="space-y-3">
                <a href="mailto:cs@bizmark.id" class="flex items-center gap-3 bg-gray-50 p-4 rounded-xl">
                    <i class="fas fa-envelope text-[#0077B5] text-xl"></i>
                    <div>
                        <div class="text-xs text-gray-500">Email</div>
                        <div class="font-semibold text-gray-900">cs@bizmark.id</div>
                    </div>
                </a>
                <a href="tel:+6283879602855" class="flex items-center gap-3 bg-gray-50 p-4 rounded-xl">
                    <i class="fas fa-phone text-[#0077B5] text-xl"></i>
                    <div>
                        <div class="text-xs text-gray-500">Telepon</div>
                        <div class="font-semibold text-gray-900">+62 838 7960 2855</div>
                    </div>
                </a>
            </div>
        </div>
    </div>
</section>

@endsection
