@extends('landing.layout')

@section('title', 'Syarat & Ketentuan - Bizmark.ID')
@section('description', 'Syarat dan Ketentuan penggunaan layanan konsultasi perizinan PT Cangah Pajaratan Mandiri (Bizmark.ID).')

@section('content')

{{-- Hero Section --}}
<section class="relative bg-gradient-to-b from-gray-50 to-white pt-32 pb-16 lg:pt-40 lg:pb-20">
    <div class="absolute inset-0 opacity-[0.03]" style="background-image: url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'none\' fill-rule=\'evenodd\'%3E%3Cg fill=\'%231E40AF\' fill-opacity=\'1\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');"></div>
    
    <div class="container-wide relative z-10">
        <div class="max-w-4xl mx-auto text-center" data-aos="fade-up">
            {{-- Badge --}}
            <div class="inline-flex items-center gap-2 px-4 py-2 bg-orange-50 rounded-full mb-6 border border-orange-100">
                <svg class="w-4 h-4 text-orange-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1.323l3.954 1.582 1.599-.8a1 1 0 01.894 1.79l-1.233.616 1.738 5.42a1 1 0 01-.285 1.05A3.989 3.989 0 0115 15a3.989 3.989 0 01-2.667-1.019 1 1 0 01-.285-1.05l1.738-5.42-1.233-.617a1 1 0 01.894-1.788l1.599.799L11 4.323V3a1 1 0 011-1zm-5 8.274l-.818 2.552c-.25.78-.19 1.582.154 2.261.344.68.952 1.198 1.664 1.422.713.224 1.44.196 2.037-.083.598-.28 1.072-.813 1.32-1.593.246-.78.19-1.582-.154-2.261-.344-.68-.952-1.198-1.664-1.422-.712-.224-1.44-.196-2.037.083-.598.28-1.072.813-1.32 1.593z" clip-rule="evenodd"/>
                </svg>
                <span class="text-sm font-semibold text-orange-600">Legal</span>
            </div>
            
            <h1 class="text-4xl lg:text-5xl font-bold text-gray-900 leading-tight mb-6">
                Syarat & Ketentuan
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
                        Selamat datang di Bizmark.ID. Syarat dan Ketentuan ini mengatur penggunaan layanan konsultasi perizinan industri yang disediakan oleh PT Cangah Pajaratan Mandiri ("Bizmark.ID", "kami", "kita", atau "milik kami"). Dengan menggunakan layanan kami, Anda ("Klien", "Anda", atau "milik Anda") menyetujui untuk terikat dengan syarat dan ketentuan berikut.
                    </p>
                    <p class="text-gray-700 leading-relaxed mt-4">
                        Harap membaca syarat dan ketentuan ini dengan seksama sebelum menggunakan layanan kami. Jika Anda tidak setuju dengan bagian mana pun dari syarat ini, mohon untuk tidak menggunakan layanan kami.
                    </p>
                </div>

                {{-- Penerimaan Syarat --}}
                <div class="mb-12">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">1. Penerimaan Syarat</h2>
                    <p class="text-gray-700 leading-relaxed mb-4">
                        Dengan mengakses website kami, menghubungi kami, atau menggunakan layanan kami, Anda mengakui bahwa:
                    </p>
                    <ul class="list-disc list-inside space-y-2 text-gray-700 ml-4">
                        <li>Anda telah membaca, memahami, dan menyetujui Syarat & Ketentuan ini</li>
                        <li>Anda memiliki kapasitas hukum untuk mengikatkan diri dalam perjanjian ini</li>
                        <li>Anda mewakili entitas bisnis yang sah dengan dokumen legal yang valid</li>
                        <li>Informasi yang Anda berikan adalah akurat dan lengkap</li>
                        <li>Anda akan mematuhi seluruh ketentuan yang berlaku</li>
                    </ul>
                </div>

                {{-- Definisi Layanan --}}
                <div class="mb-12">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">2. Definisi Layanan</h2>
                    <p class="text-gray-700 leading-relaxed mb-4">
                        Bizmark.ID menyediakan layanan konsultasi dan pengurusan perizinan industri, termasuk namun tidak terbatas pada:
                    </p>
                    <ul class="list-disc list-inside space-y-2 text-gray-700 ml-4">
                        <li><strong>AMDAL & UKL-UPL:</strong> Dokumen analisis dampak lingkungan dan pengelolaan lingkungan</li>
                        <li><strong>Izin Lingkungan:</strong> Persetujuan lingkungan untuk kegiatan usaha</li>
                        <li><strong>PROPER:</strong> Program penilaian peringkat kinerja perusahaan dalam pengelolaan lingkungan</li>
                        <li><strong>Izin Berusaha (OSS):</strong> Perizinan berusaha berbasis risiko melalui Online Single Submission</li>
                        <li><strong>IPAL & SLO:</strong> Instalasi pengolahan air limbah dan surat layak operasi</li>
                        <li><strong>IMB & SLF:</strong> Izin mendirikan bangunan dan sertifikat laik fungsi</li>
                        <li><strong>Sertifikasi K3:</strong> Sertifikasi kesehatan dan keselamatan kerja</li>
                        <li><strong>Konsultasi HSE:</strong> Health, Safety, and Environment consultation</li>
                    </ul>
                </div>

                {{-- Kewajiban Klien --}}
                <div class="mb-12">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">3. Kewajiban Klien</h2>
                    
                    <h3 class="text-xl font-semibold text-gray-900 mb-3">3.1 Penyediaan Dokumen</h3>
                    <p class="text-gray-700 leading-relaxed mb-4">Klien wajib menyediakan:</p>
                    <ul class="list-disc list-inside space-y-2 text-gray-700 ml-4">
                        <li>Dokumen perusahaan lengkap dan terkini (Akta, SK Kemenkumham, NPWP, dll)</li>
                        <li>Data teknis fasilitas/proyek yang akurat dan detail</li>
                        <li>Informasi lingkungan dan operasional yang dibutuhkan</li>
                        <li>Surat kuasa dan dokumen pendukung lainnya</li>
                        <li>Akses lokasi untuk survey dan inspeksi lapangan</li>
                    </ul>

                    <h3 class="text-xl font-semibold text-gray-900 mb-3 mt-6">3.2 Keakuratan Informasi</h3>
                    <p class="text-gray-700 leading-relaxed">
                        Klien menjamin bahwa seluruh informasi dan dokumen yang diberikan adalah benar, akurat, dan tidak menyesatkan. Klien bertanggung jawab penuh atas kebenaran data dan konsekuensi hukum yang timbul dari informasi yang salah atau tidak lengkap.
                    </p>

                    <h3 class="text-xl font-semibold text-gray-900 mb-3 mt-6">3.3 Kerja Sama</h3>
                    <p class="text-gray-700 leading-relaxed">
                        Klien wajib memberikan kerja sama yang baik, termasuk merespons permintaan informasi, menyediakan akses lokasi, dan memfasilitasi koordinasi dengan pihak terkait dalam waktu yang wajar.
                    </p>
                </div>

                {{-- Ketentuan Pembayaran --}}
                <div class="mb-12">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">4. Ketentuan Pembayaran</h2>
                    
                    <h3 class="text-xl font-semibold text-gray-900 mb-3">4.1 Struktur Biaya</h3>
                    <p class="text-gray-700 leading-relaxed mb-4">
                        Biaya layanan akan diinformasikan melalui penawaran resmi (quotation) yang mencakup:
                    </p>
                    <ul class="list-disc list-inside space-y-2 text-gray-700 ml-4">
                        <li>Biaya konsultasi dan pengurusan dokumen</li>
                        <li>Biaya survey lapangan dan analisis teknis</li>
                        <li>Biaya administrasi pemerintah (retribusi, PNBP)</li>
                        <li>Biaya lain-lain yang terkait dengan proyek</li>
                    </ul>

                    <h3 class="text-xl font-semibold text-gray-900 mb-3 mt-6">4.2 Skema Pembayaran</h3>
                    <p class="text-gray-700 leading-relaxed mb-4">
                        Pembayaran umumnya dilakukan secara bertahap:
                    </p>
                    <ul class="list-disc list-inside space-y-2 text-gray-700 ml-4">
                        <li><strong>Down Payment (DP):</strong> 30-50% dari nilai kontrak saat penandatanganan</li>
                        <li><strong>Progress Payment:</strong> 30-40% saat tahap tertentu selesai (misal: draft dokumen)</li>
                        <li><strong>Final Payment:</strong> 20-30% saat izin terbit atau dokumen final diserahkan</li>
                    </ul>
                    <p class="text-gray-700 leading-relaxed mt-4">
                        Skema pembayaran dapat disesuaikan berdasarkan kesepakatan dalam kontrak kerja sama.
                    </p>

                    <h3 class="text-xl font-semibold text-gray-900 mb-3 mt-6">4.3 Keterlambatan Pembayaran</h3>
                    <p class="text-gray-700 leading-relaxed">
                        Keterlambatan pembayaran dapat mengakibatkan penundaan proses dan/atau dikenakan denda sesuai kesepakatan. Kami berhak menghentikan layanan jika pembayaran tertunggak lebih dari 30 hari.
                    </p>

                    <h3 class="text-xl font-semibold text-gray-900 mb-3 mt-6">4.4 Biaya Tambahan</h3>
                    <p class="text-gray-700 leading-relaxed">
                        Biaya tambahan di luar quotation awal (seperti revisi dokumen mayor, perubahan scope, atau biaya pemerintah yang tidak terduga) akan diinformasikan dan disetujui terlebih dahulu oleh Klien.
                    </p>
                </div>

                {{-- Jangka Waktu & Penyelesaian --}}
                <div class="mb-12">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">5. Jangka Waktu dan Penyelesaian</h2>
                    
                    <h3 class="text-xl font-semibold text-gray-900 mb-3">5.1 Timeline Proyek</h3>
                    <p class="text-gray-700 leading-relaxed">
                        Estimasi waktu penyelesaian akan ditetapkan dalam kontrak kerja sama berdasarkan kompleksitas proyek, kelengkapan dokumen, dan proses di instansi terkait. Timeline bersifat estimasi dan dapat berubah karena faktor di luar kendali kami.
                    </p>

                    <h3 class="text-xl font-semibold text-gray-900 mb-3 mt-6">5.2 Keterlambatan di Luar Kendali</h3>
                    <p class="text-gray-700 leading-relaxed mb-4">
                        Kami tidak bertanggung jawab atas keterlambatan yang disebabkan oleh:
                    </p>
                    <ul class="list-disc list-inside space-y-2 text-gray-700 ml-4">
                        <li>Keterlambatan Klien dalam menyediakan dokumen atau informasi</li>
                        <li>Proses review dan persetujuan di instansi pemerintah</li>
                        <li>Perubahan regulasi atau kebijakan pemerintah</li>
                        <li>Force majeure (bencana alam, pandemi, kerusuhan, dll)</li>
                        <li>Revisi mayor yang diminta Klien atau instansi terkait</li>
                    </ul>
                </div>

                {{-- Batasan Tanggung Jawab --}}
                <div class="mb-12">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">6. Batasan Tanggung Jawab</h2>
                    
                    <h3 class="text-xl font-semibold text-gray-900 mb-3">6.1 Ruang Lingkup Tanggung Jawab</h3>
                    <p class="text-gray-700 leading-relaxed mb-4">
                        Bizmark.ID bertanggung jawab untuk:
                    </p>
                    <ul class="list-disc list-inside space-y-2 text-gray-700 ml-4">
                        <li>Menyusun dokumen perizinan sesuai standar dan regulasi yang berlaku</li>
                        <li>Melakukan koordinasi dengan instansi pemerintah terkait</li>
                        <li>Memberikan update berkala tentang progress pengerjaan</li>
                        <li>Menjaga kerahasiaan informasi Klien</li>
                    </ul>

                    <h3 class="text-xl font-semibold text-gray-900 mb-3 mt-6">6.2 Batasan</h3>
                    <p class="text-gray-700 leading-relaxed mb-4">
                        Kami TIDAK bertanggung jawab atas:
                    </p>
                    <ul class="list-disc list-inside space-y-2 text-gray-700 ml-4">
                        <li>Penolakan izin oleh instansi pemerintah karena kondisi objektif lokasi/proyek</li>
                        <li>Kerugian finansial atau operasional Klien akibat proses perizinan yang lama</li>
                        <li>Perubahan kebijakan atau regulasi pemerintah selama proses berjalan</li>
                        <li>Informasi yang salah atau tidak lengkap yang diberikan oleh Klien</li>
                        <li>Tindakan atau kelalaian pihak ketiga (instansi pemerintah, konsultan lain, dll)</li>
                        <li>Kondisi force majeure yang menghambat proses</li>
                    </ul>

                    <h3 class="text-xl font-semibold text-gray-900 mb-3 mt-6">6.3 Batas Ganti Rugi</h3>
                    <p class="text-gray-700 leading-relaxed">
                        Dalam hal terjadi klaim, total tanggung jawab kami terbatas pada nilai kontrak layanan yang telah dibayarkan. Kami tidak bertanggung jawab atas kerugian tidak langsung, kerugian konsekuensial, atau kehilangan profit.
                    </p>
                </div>

                {{-- Kerahasiaan --}}
                <div class="mb-12">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">7. Kerahasiaan</h2>
                    <p class="text-gray-700 leading-relaxed mb-4">
                        Kedua belah pihak sepakat untuk:
                    </p>
                    <ul class="list-disc list-inside space-y-2 text-gray-700 ml-4">
                        <li>Menjaga kerahasiaan informasi bisnis, teknis, dan pribadi yang diperoleh selama kerja sama</li>
                        <li>Tidak mengungkapkan informasi rahasia kepada pihak ketiga tanpa persetujuan tertulis</li>
                        <li>Menggunakan informasi hanya untuk tujuan pelaksanaan layanan</li>
                        <li>Mengembalikan atau menghancurkan informasi rahasia setelah kerja sama berakhir</li>
                    </ul>
                    <p class="text-gray-700 leading-relaxed mt-4">
                        Kewajiban kerahasiaan tidak berlaku untuk informasi yang: (a) sudah menjadi domain publik, (b) diwajibkan diungkapkan oleh hukum, atau (c) dikembangkan secara independen.
                    </p>
                </div>

                {{-- Hak Kekayaan Intelektual --}}
                <div class="mb-12">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">8. Hak Kekayaan Intelektual</h2>
                    <p class="text-gray-700 leading-relaxed mb-4">
                        Kepemilikan hak kekayaan intelektual diatur sebagai berikut:
                    </p>
                    <ul class="list-disc list-inside space-y-2 text-gray-700 ml-4">
                        <li><strong>Dokumen Proyek:</strong> Dokumen perizinan yang disusun untuk Klien menjadi milik Klien setelah pembayaran lunas</li>
                        <li><strong>Template & Metodologi:</strong> Template, metodologi, dan know-how yang kami gunakan tetap menjadi milik Bizmark.ID</li>
                        <li><strong>Data Klien:</strong> Data dan informasi yang diberikan Klien tetap menjadi milik Klien</li>
                        <li><strong>Portfolio:</strong> Kami berhak mencantumkan proyek Klien dalam portfolio kami (tanpa data sensitif) kecuali ada kesepakatan lain</li>
                    </ul>
                </div>

                {{-- Penghentian Layanan --}}
                <div class="mb-12">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">9. Penghentian Layanan</h2>
                    
                    <h3 class="text-xl font-semibold text-gray-900 mb-3">9.1 Penghentian oleh Klien</h3>
                    <p class="text-gray-700 leading-relaxed">
                        Klien dapat menghentikan layanan dengan pemberitahuan tertulis minimal 7 hari sebelumnya. Klien tetap wajib membayar biaya untuk pekerjaan yang telah diselesaikan dan biaya yang sudah dikeluarkan.
                    </p>

                    <h3 class="text-xl font-semibold text-gray-900 mb-3 mt-6">9.2 Penghentian oleh Bizmark.ID</h3>
                    <p class="text-gray-700 leading-relaxed mb-4">
                        Kami berhak menghentikan layanan jika:
                    </p>
                    <ul class="list-disc list-inside space-y-2 text-gray-700 ml-4">
                        <li>Klien melanggar syarat dan ketentuan ini</li>
                        <li>Terjadi keterlambatan pembayaran lebih dari 30 hari</li>
                        <li>Klien tidak memberikan dokumen/informasi yang diperlukan setelah 3x reminder</li>
                        <li>Ditemukan informasi palsu atau menyesatkan dari Klien</li>
                        <li>Terjadi force majeure yang berkepanjangan</li>
                    </ul>
                </div>

                {{-- Penyelesaian Sengketa --}}
                <div class="mb-12">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">10. Penyelesaian Sengketa</h2>
                    <p class="text-gray-700 leading-relaxed mb-4">
                        Dalam hal terjadi perselisihan atau sengketa:
                    </p>
                    <ol class="list-decimal list-inside space-y-2 text-gray-700 ml-4">
                        <li>Kedua belah pihak akan berupaya menyelesaikan secara musyawarah dalam waktu 30 hari</li>
                        <li>Jika musyawarah gagal, sengketa akan diselesaikan melalui mediasi</li>
                        <li>Jika mediasi tidak berhasil, sengketa akan diselesaikan melalui arbitrase atau pengadilan</li>
                        <li>Hukum yang berlaku adalah hukum Republik Indonesia</li>
                        <li>Domisili hukum yang dipilih adalah Pengadilan Negeri Karawang</li>
                    </ol>
                </div>

                {{-- Perubahan Syarat --}}
                <div class="mb-12">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">11. Perubahan Syarat & Ketentuan</h2>
                    <p class="text-gray-700 leading-relaxed">
                        Kami berhak mengubah atau memperbarui Syarat & Ketentuan ini dari waktu ke waktu. Perubahan akan diposting di website dengan tanggal "Terakhir diperbarui" yang baru. Untuk proyek yang sedang berjalan, syarat yang berlaku adalah syarat saat kontrak ditandatangani. Penggunaan layanan baru setelah perubahan berarti Anda menerima syarat yang diperbarui.
                    </p>
                </div>

                {{-- Lain-lain --}}
                <div class="mb-12">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">12. Ketentuan Lain-lain</h2>
                    
                    <h3 class="text-xl font-semibold text-gray-900 mb-3">12.1 Keseluruhan Perjanjian</h3>
                    <p class="text-gray-700 leading-relaxed">
                        Syarat & Ketentuan ini, bersama dengan kontrak kerja sama dan dokumen terkait lainnya, merupakan keseluruhan perjanjian antara para pihak dan menggantikan semua perjanjian sebelumnya.
                    </p>

                    <h3 class="text-xl font-semibold text-gray-900 mb-3 mt-6">12.2 Keterpisahan</h3>
                    <p class="text-gray-700 leading-relaxed">
                        Jika ada ketentuan yang dinyatakan tidak sah atau tidak dapat dilaksanakan, ketentuan lainnya tetap berlaku sepenuhnya.
                    </p>

                    <h3 class="text-xl font-semibold text-gray-900 mb-3 mt-6">12.3 Pengabaian</h3>
                    <p class="text-gray-700 leading-relaxed">
                        Kegagalan kami untuk menegakkan suatu hak atau ketentuan tidak dianggap sebagai pengabaian hak atau ketentuan tersebut.
                    </p>

                    <h3 class="text-xl font-semibold text-gray-900 mb-3 mt-6">12.4 Pengalihan</h3>
                    <p class="text-gray-700 leading-relaxed">
                        Klien tidak dapat mengalihkan hak atau kewajiban berdasarkan perjanjian ini tanpa persetujuan tertulis dari kami. Kami dapat mengalihkan hak dan kewajiban kami kepada pihak ketiga.
                    </p>
                </div>

                {{-- Kontak --}}
                <div class="bg-orange-50 rounded-xl p-8 border border-orange-100">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">Hubungi Kami</h2>
                    <p class="text-gray-700 leading-relaxed mb-6">
                        Jika Anda memiliki pertanyaan tentang Syarat & Ketentuan ini atau ingin mendiskusikan kerja sama, silakan hubungi kami:
                    </p>
                    <div class="space-y-3">
                        <div class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-orange-600 flex-shrink-0 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                            <div>
                                <p class="text-sm text-gray-600">Email</p>
                                <a href="mailto:headoffice.cpm@gmail.com" class="text-orange-600 hover:text-orange-700 font-semibold">headoffice.cpm@gmail.com</a>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-orange-600 flex-shrink-0 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                            </svg>
                            <div>
                                <p class="text-sm text-gray-600">Telepon</p>
                                <a href="tel:+6281382605030" class="text-orange-600 hover:text-orange-700 font-semibold">+62 813 8260 5030</a>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-orange-600 flex-shrink-0 mt-1" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.890-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                            </svg>
                            <div>
                                <p class="text-sm text-gray-600">WhatsApp</p>
                                <a href="https://wa.me/6281382605030" target="_blank" class="text-orange-600 hover:text-orange-700 font-semibold">+62 813 8260 5030</a>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-orange-600 flex-shrink-0 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                Siap Memulai Kerja Sama?
            </h2>
            <p class="text-lg text-gray-600 mb-8">
                Konsultasikan kebutuhan perizinan Anda dengan tim ahli kami
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="https://wa.me/6281382605030" target="_blank" class="inline-flex items-center justify-center px-8 py-4 bg-orange-600 text-white font-semibold rounded-lg hover:bg-orange-700 transition-all duration-300 shadow-lg hover:shadow-xl">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.890-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                    </svg>
                    Hubungi via WhatsApp
                </a>
                <a href="{{ route('landing') }}" class="inline-flex items-center justify-center px-8 py-4 bg-white text-gray-700 font-semibold rounded-lg border-2 border-gray-300 hover:border-orange-600 hover:text-orange-600 transition-all duration-300">
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
