<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Interview Expired - {{ config('app.name') }}</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
        }
        
        .card-shadow {
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        }
        
        .fade-in {
            animation: fadeIn 0.5s ease-out;
        }
        
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>
<body class="flex items-center justify-center p-4">
    <div class="max-w-2xl w-full fade-in">
        <!-- Expired Card -->
        <div class="bg-white rounded-3xl shadow-2xl overflow-hidden card-shadow">
            <!-- Header -->
            <div class="bg-gradient-to-r from-gray-600 to-gray-800 p-8 text-center">
                <div class="w-24 h-24 bg-white rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="bi bi-calendar-x text-gray-600 text-6xl"></i>
                </div>
                <h1 class="text-3xl font-bold text-white mb-2">Link Interview Sudah Kadaluarsa</h1>
                <p class="text-gray-200 text-lg">
                    Maaf, akses ke halaman interview ini sudah tidak tersedia
                </p>
            </div>

            <!-- Content -->
            <div class="p-8 space-y-6">
                <!-- Interview Info -->
                <div class="bg-gray-50 rounded-2xl p-6 space-y-3">
                    <h3 class="text-gray-900 font-semibold mb-3 flex items-center gap-2">
                        <i class="bi bi-info-circle text-blue-600"></i>
                        Informasi Interview
                    </h3>
                    
                    @isset($interview)
                    <div class="space-y-2 text-sm">
                        <div class="flex items-center justify-between">
                            <span class="text-gray-600 font-medium">Kandidat:</span>
                            <span class="text-gray-900 font-semibold">{{ $interview->jobApplication->full_name }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-600 font-medium">Posisi:</span>
                            <span class="text-gray-900 font-semibold">{{ $interview->jobApplication?->jobVacancy?->title ?? 'Position' }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-600 font-medium">Jadwal Interview:</span>
                            <span class="text-gray-900 font-semibold">{{ $interview->scheduled_at->format('d M Y, H:i') }} WIB</span>
                        </div>
                        <div class="flex items-center justify-between border-t border-gray-200 pt-2 mt-2">
                            <span class="text-gray-600 font-medium">Status:</span>
                            <span class="px-3 py-1 bg-red-100 text-red-700 rounded-full text-xs font-semibold">
                                Kadaluarsa
                            </span>
                        </div>
                    </div>
                    @else
                    <p class="text-gray-600 text-sm">
                        Link interview yang Anda akses sudah tidak valid atau sudah kadaluarsa.
                    </p>
                    @endisset
                </div>

                <!-- Why Expired -->
                <div class="bg-orange-50 border-2 border-orange-200 rounded-2xl p-6">
                    <h3 class="text-orange-900 font-semibold text-lg mb-3 flex items-center gap-2">
                        <i class="bi bi-question-circle-fill text-orange-600"></i>
                        Mengapa Link Kadaluarsa?
                    </h3>
                    <div class="space-y-3 text-sm text-orange-800">
                        <div class="flex items-start gap-2">
                            <i class="bi bi-check-circle-fill text-orange-500 mt-1"></i>
                            <p><strong>Interview sudah selesai:</strong> Link otomatis tidak aktif setelah 7 hari dari jadwal interview</p>
                        </div>
                        <div class="flex items-start gap-2">
                            <i class="bi bi-check-circle-fill text-orange-500 mt-1"></i>
                            <p><strong>Keamanan data:</strong> Link dibatasi waktunya untuk melindungi privasi kandidat</p>
                        </div>
                        <div class="flex items-start gap-2">
                            <i class="bi bi-check-circle-fill text-orange-500 mt-1"></i>
                            <p><strong>Status sudah berubah:</strong> Interview mungkin sudah dibatalkan atau dijadwalkan ulang</p>
                        </div>
                    </div>
                </div>

                <!-- What to Do -->
                <div class="bg-blue-50 rounded-2xl p-6">
                    <h3 class="text-gray-900 font-semibold text-lg mb-4 flex items-center gap-2">
                        <i class="bi bi-lightbulb-fill text-yellow-500 text-2xl"></i>
                        Apa yang Harus Dilakukan?
                    </h3>
                    
                    <div class="space-y-4">
                        <div class="bg-white rounded-xl p-4">
                            <div class="flex items-start gap-3">
                                <div class="w-10 h-10 bg-blue-600 rounded-full flex items-center justify-center flex-shrink-0 text-white font-bold">
                                    1
                                </div>
                                <div>
                                    <h4 class="text-gray-900 font-semibold mb-1">Cek Email Terbaru</h4>
                                    <p class="text-gray-700 text-sm">
                                        Periksa inbox email Anda untuk update terbaru mengenai status rekrutmen. Jangan lupa cek folder spam/junk.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white rounded-xl p-4">
                            <div class="flex items-start gap-3">
                                <div class="w-10 h-10 bg-blue-600 rounded-full flex items-center justify-center flex-shrink-0 text-white font-bold">
                                    2
                                </div>
                                <div>
                                    <h4 class="text-gray-900 font-semibold mb-1">Hubungi Tim HR</h4>
                                    <p class="text-gray-700 text-sm">
                                        Jika Anda merasa seharusnya masih bisa mengakses interview, segera hubungi tim HR untuk klarifikasi.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white rounded-xl p-4">
                            <div class="flex items-start gap-3">
                                <div class="w-10 h-10 bg-blue-600 rounded-full flex items-center justify-center flex-shrink-0 text-white font-bold">
                                    3
                                </div>
                                <div>
                                    <h4 class="text-gray-900 font-semibold mb-1">Tunggu Pemberitahuan Selanjutnya</h4>
                                    <p class="text-gray-700 text-sm">
                                        Jika interview sudah selesai, tim HR akan menghubungi Anda untuk tahap selanjutnya dalam 3-5 hari kerja.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Contact Information -->
                <div class="bg-gradient-to-r from-purple-50 to-blue-50 rounded-2xl p-6">
                    <h3 class="text-gray-900 font-semibold text-lg mb-4 flex items-center gap-2">
                        <i class="bi bi-headset text-purple-600"></i>
                        Butuh Bantuan?
                    </h3>
                    
                    <p class="text-gray-700 text-sm mb-4">
                        Hubungi tim rekrutmen kami untuk informasi lebih lanjut:
                    </p>

                    <div class="space-y-3">
                        <a href="mailto:recruitment@bizmark.id?subject=Pertanyaan%20Interview%20Expired" class="flex items-center gap-3 p-3 bg-white rounded-xl hover:shadow-md transition-all group">
                            <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center group-hover:bg-blue-200 transition-colors">
                                <i class="bi bi-envelope-fill text-blue-600 text-xl"></i>
                            </div>
                            <div class="flex-1">
                                <p class="text-gray-900 font-semibold group-hover:text-blue-600 transition-colors">Email</p>
                                <p class="text-blue-600 text-sm">recruitment@bizmark.id</p>
                            </div>
                            <i class="bi bi-arrow-right text-gray-400 group-hover:text-blue-600 transition-colors"></i>
                        </a>

                        <a href="https://wa.me/6281234567890?text=Halo,%20saya%20ingin%20menanyakan%20tentang%20interview%20yang%20kadaluarsa" target="_blank" class="flex items-center gap-3 p-3 bg-white rounded-xl hover:shadow-md transition-all group">
                            <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center group-hover:bg-green-200 transition-colors">
                                <i class="bi bi-whatsapp text-green-600 text-xl"></i>
                            </div>
                            <div class="flex-1">
                                <p class="text-gray-900 font-semibold group-hover:text-green-600 transition-colors">WhatsApp</p>
                                <p class="text-green-600 text-sm">+62 812-3456-7890</p>
                            </div>
                            <i class="bi bi-arrow-right text-gray-400 group-hover:text-green-600 transition-colors"></i>
                        </a>

                        <a href="tel:+622112345678" class="flex items-center gap-3 p-3 bg-white rounded-xl hover:shadow-md transition-all group">
                            <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center group-hover:bg-purple-200 transition-colors">
                                <i class="bi bi-telephone-fill text-purple-600 text-xl"></i>
                            </div>
                            <div class="flex-1">
                                <p class="text-gray-900 font-semibold group-hover:text-purple-600 transition-colors">Telepon</p>
                                <p class="text-purple-600 text-sm">021-1234-5678</p>
                            </div>
                            <i class="bi bi-arrow-right text-gray-400 group-hover:text-purple-600 transition-colors"></i>
                        </a>
                    </div>

                    <div class="mt-4 p-4 bg-white rounded-xl border border-blue-200">
                        <div class="flex items-start gap-3">
                            <i class="bi bi-clock-fill text-blue-600 text-xl"></i>
                            <div>
                                <p class="text-gray-900 font-semibold text-sm mb-1">Jam Operasional</p>
                                <p class="text-gray-600 text-xs">
                                    Senin - Jumat: 09:00 - 17:00 WIB<br>
                                    Sabtu: 09:00 - 13:00 WIB<br>
                                    Minggu & Hari Libur: Tutup
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tips -->
                <div class="border-l-4 border-blue-400 bg-blue-50 p-4 rounded-r-xl">
                    <div class="flex items-start gap-3">
                        <i class="bi bi-lightbulb-fill text-blue-600 text-xl"></i>
                        <div>
                            <h4 class="text-blue-900 font-semibold mb-2">Tips untuk Kandidat:</h4>
                            <ul class="text-blue-800 text-sm space-y-1">
                                <li class="flex items-start gap-2">
                                    <i class="bi bi-check2 text-blue-600 mt-0.5"></i>
                                    <span>Selalu simpan email invitation sebagai referensi</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <i class="bi bi-check2 text-blue-600 mt-0.5"></i>
                                    <span>Akses link interview jauh-jauh hari sebelum jadwal untuk test koneksi</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <i class="bi bi-check2 text-blue-600 mt-0.5"></i>
                                    <span>Bookmark link interview agar tidak hilang</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <i class="bi bi-check2 text-blue-600 mt-0.5"></i>
                                    <span>Hubungi HR segera jika ada kendala teknis</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row gap-3 pt-4">
                    <a href="mailto:recruitment@bizmark.id" class="flex-1 bg-gradient-to-r from-blue-600 to-purple-600 text-white font-semibold py-3 px-6 rounded-xl hover:from-blue-700 hover:to-purple-700 transition-all duration-300 transform hover:scale-105 text-center">
                        <i class="bi bi-envelope mr-2"></i>Hubungi HR via Email
                    </a>
                    <button onclick="window.close()" class="flex-1 bg-gray-200 text-gray-700 font-semibold py-3 px-6 rounded-xl hover:bg-gray-300 transition-all duration-300 text-center">
                        <i class="bi bi-x-lg mr-2"></i>Tutup Halaman
                    </button>
                </div>

                <!-- Additional Info -->
                <div class="text-center pt-4 border-t border-gray-200">
                    <p class="text-gray-500 text-xs">
                        Jika Anda merasa ini adalah kesalahan sistem, mohon hubungi support teknis kami dengan menyertakan screenshot halaman ini.
                    </p>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="text-center mt-6 text-white">
            <p class="text-sm opacity-90">
                &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
            </p>
            <p class="text-xs opacity-75 mt-1">
                Recruitment Management System
            </p>
        </div>
    </div>

    <script>
        // Prevent back button
        history.pushState(null, null, location.href);
        window.onpopstate = function () {
            history.go(1);
        };

        // Optional: Auto-close after 5 minutes
        setTimeout(() => {
            if (confirm('Halaman ini akan ditutup otomatis. Tutup sekarang?')) {
                window.close();
            }
        }, 300000); // 5 minutes
    </script>
</body>
</html>
