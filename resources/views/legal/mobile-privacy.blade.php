@extends('mobile-landing.layouts.content')

@section('title', 'Kebijakan Privasi - Bizmark.ID')
@section('meta_description', 'Kebijakan Privasi PT Cangah Pajaratan Mandiri tentang perlindungan data pribadi Anda')

@section('content')

<!-- Hero Section -->
<section class="magazine-section bg-gradient-to-br from-blue-50 to-white">
    <div class="content-container text-center">
        <div class="w-16 h-16 rounded-full bg-gradient-to-br from-[#0077B5] to-[#005582] flex items-center justify-center mx-auto mb-4 text-white text-2xl">
            <i class="fas fa-shield-alt"></i>
        </div>
        <h1 class="headline text-3xl text-gray-900 mb-3">
            Kebijakan Privasi
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
                    Kami di Bizmark.ID berkomitmen untuk melindungi privasi dan keamanan data pribadi Anda. 
                    Kebijakan ini menjelaskan bagaimana kami mengumpulkan, menggunakan, dan melindungi informasi Anda.
                </p>
            </div>

            <h2 class="text-xl font-bold text-gray-900 mb-3 mt-6">1. Informasi yang Kami Kumpulkan</h2>
            <p class="text-sm text-gray-700 leading-relaxed mb-4">
                Kami mengumpulkan informasi yang Anda berikan secara langsung saat menggunakan layanan kami:
            </p>
            <ul class="space-y-2 mb-6">
                <li class="flex items-start gap-2 text-sm text-gray-700">
                    <i class="fas fa-check text-[#0077B5] mt-1 flex-shrink-0"></i>
                    <span>Data identitas (nama, alamat, nomor telepon, email)</span>
                </li>
                <li class="flex items-start gap-2 text-sm text-gray-700">
                    <i class="fas fa-check text-[#0077B5] mt-1 flex-shrink-0"></i>
                    <span>Informasi perusahaan (nama, bidang usaha, dokumen legal)</span>
                </li>
                <li class="flex items-start gap-2 text-sm text-gray-700">
                    <i class="fas fa-check text-[#0077B5] mt-1 flex-shrink-0"></i>
                    <span>Data transaksi dan komunikasi dengan kami</span>
                </li>
            </ul>

            <h2 class="text-xl font-bold text-gray-900 mb-3 mt-6">2. Penggunaan Informasi</h2>
            <p class="text-sm text-gray-700 leading-relaxed mb-4">
                Informasi yang kami kumpulkan digunakan untuk:
            </p>
            <ul class="space-y-2 mb-6">
                <li class="flex items-start gap-2 text-sm text-gray-700">
                    <i class="fas fa-check text-green-500 mt-1 flex-shrink-0"></i>
                    <span>Memproses pengajuan perizinan Anda</span>
                </li>
                <li class="flex items-start gap-2 text-sm text-gray-700">
                    <i class="fas fa-check text-green-500 mt-1 flex-shrink-0"></i>
                    <span>Berkomunikasi mengenai layanan dan update</span>
                </li>
                <li class="flex items-start gap-2 text-sm text-gray-700">
                    <i class="fas fa-check text-green-500 mt-1 flex-shrink-0"></i>
                    <span>Meningkatkan kualitas layanan kami</span>
                </li>
                <li class="flex items-start gap-2 text-sm text-gray-700">
                    <i class="fas fa-check text-green-500 mt-1 flex-shrink-0"></i>
                    <span>Mematuhi persyaratan hukum dan regulasi</span>
                </li>
            </ul>

            <h2 class="text-xl font-bold text-gray-900 mb-3 mt-6">3. Keamanan Data</h2>
            <p class="text-sm text-gray-700 leading-relaxed mb-6">
                Kami menerapkan langkah-langkah keamanan teknis dan organisasi yang sesuai untuk melindungi data pribadi Anda 
                dari akses tidak sah, kehilangan, atau penyalahgunaan. Semua data disimpan dengan enkripsi dan hanya dapat diakses 
                oleh personel yang berwenang.
            </p>

            <h2 class="text-xl font-bold text-gray-900 mb-3 mt-6">4. Hak Anda</h2>
            <p class="text-sm text-gray-700 leading-relaxed mb-4">
                Anda memiliki hak untuk:
            </p>
            <ul class="space-y-2 mb-6">
                <li class="flex items-start gap-2 text-sm text-gray-700">
                    <i class="fas fa-user-check text-[#0077B5] mt-1 flex-shrink-0"></i>
                    <span>Mengakses data pribadi Anda</span>
                </li>
                <li class="flex items-start gap-2 text-sm text-gray-700">
                    <i class="fas fa-edit text-[#0077B5] mt-1 flex-shrink-0"></i>
                    <span>Memperbaiki data yang tidak akurat</span>
                </li>
                <li class="flex items-start gap-2 text-sm text-gray-700">
                    <i class="fas fa-trash text-[#0077B5] mt-1 flex-shrink-0"></i>
                    <span>Meminta penghapusan data Anda</span>
                </li>
                <li class="flex items-start gap-2 text-sm text-gray-700">
                    <i class="fas fa-ban text-[#0077B5] mt-1 flex-shrink-0"></i>
                    <span>Menolak pemrosesan data tertentu</span>
                </li>
            </ul>

            <h2 class="text-xl font-bold text-gray-900 mb-3 mt-6">5. Cookie dan Teknologi Serupa</h2>
            <p class="text-sm text-gray-700 leading-relaxed mb-6">
                Website kami menggunakan cookie untuk meningkatkan pengalaman pengguna, menganalisis trafik, 
                dan personalisasi konten. Anda dapat mengelola preferensi cookie melalui pengaturan browser Anda.
            </p>

            <h2 class="text-xl font-bold text-gray-900 mb-3 mt-6">6. Perubahan Kebijakan</h2>
            <p class="text-sm text-gray-700 leading-relaxed mb-6">
                Kami dapat memperbarui kebijakan privasi ini dari waktu ke waktu. Perubahan akan diumumkan 
                melalui website kami dan berlaku efektif setelah dipublikasikan.
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
                <h3 class="text-xl font-bold text-gray-900 mb-2">Ada Pertanyaan?</h3>
                <p class="text-sm text-gray-600">
                    Jika Anda memiliki pertanyaan tentang kebijakan privasi kami, silakan hubungi:
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
