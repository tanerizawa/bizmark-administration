<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tes Kadaluarsa - {{ config('app.name') }}</title>
    
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
        
        .warning-pulse {
            animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }
        
        @keyframes pulse {
            0%, 100% {
                opacity: 1;
            }
            50% {
                opacity: .7;
            }
        }
        
        .card-shadow {
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        }
    </style>
</head>
<body class="flex items-center justify-center p-4">
    <div class="max-w-2xl w-full">
        <!-- Expired Card -->
        <div class="bg-white rounded-3xl shadow-2xl overflow-hidden card-shadow">
            <!-- Header with Warning Icon -->
            <div class="bg-gradient-to-r from-orange-400 to-red-500 p-8 text-center">
                <div class="warning-pulse">
                    <div class="w-24 h-24 bg-white rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="bi bi-hourglass-bottom text-orange-500 text-6xl"></i>
                    </div>
                </div>
                <h1 class="text-3xl font-bold text-white mb-2">Waktu Tes Telah Habis</h1>
                <p class="text-orange-100 text-lg">
                    Maaf, batas waktu untuk mengerjakan tes ini sudah berakhir
                </p>
            </div>

            <!-- Content -->
            <div class="p-8 space-y-6">
                <!-- Test Info -->
                <div class="bg-gray-50 rounded-2xl p-6 space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-gray-600 font-medium">Nama Tes:</span>
                        <span class="text-gray-900 font-semibold">{{ $testSession->testTemplate->title }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-gray-600 font-medium">Kandidat:</span>
                        <span class="text-gray-900 font-semibold">{{ $testSession->jobApplication->full_name }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-gray-600 font-medium">Posisi:</span>
                        <span class="text-gray-900 font-semibold">{{ $testSession->jobApplication?->jobVacancy?->title ?? 'Position' }}</span>
                    </div>
                    <div class="flex items-center justify-between border-t border-gray-200 pt-3 mt-3">
                        <span class="text-gray-600 font-medium">Batas Waktu:</span>
                        <span class="text-red-600 font-semibold">{{ $testSession->expires_at->format('d M Y, H:i') }} WIB</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-gray-600 font-medium">Kadaluarsa Sejak:</span>
                        <span class="text-gray-500 font-semibold">{{ $testSession->expires_at->diffForHumans() }}</span>
                    </div>
                </div>

                <!-- Status Alert -->
                <div class="bg-red-50 border-2 border-red-200 rounded-2xl p-6">
                    <div class="flex items-start gap-4">
                        <i class="bi bi-x-circle-fill text-red-500 text-4xl flex-shrink-0"></i>
                        <div class="flex-1">
                            <h3 class="text-red-900 font-semibold text-lg mb-2">Akses Tes Tidak Tersedia</h3>
                            <p class="text-red-700 text-sm leading-relaxed mb-3">
                                Tes ini telah melewati batas waktu pengerjaan yang ditetapkan. Sistem secara otomatis menutup akses setelah deadline terlewati untuk menjaga integritas proses rekrutmen.
                            </p>
                            
                            @if($testSession->status === 'started')
                                <div class="bg-red-100 rounded-lg p-3 mt-3">
                                    <p class="text-red-800 text-sm font-medium mb-1">
                                        <i class="bi bi-info-circle mr-1"></i> Status: Belum Selesai
                                    </p>
                                    <p class="text-red-700 text-xs">
                                        Tes dimulai tetapi tidak diselesaikan sebelum batas waktu.
                                    </p>
                                </div>
                            @else
                                <div class="bg-orange-100 rounded-lg p-3 mt-3">
                                    <p class="text-orange-800 text-sm font-medium mb-1">
                                        <i class="bi bi-info-circle mr-1"></i> Status: Tidak Dimulai
                                    </p>
                                    <p class="text-orange-700 text-xs">
                                        Tes tidak dimulai sebelum batas waktu berakhir.
                                    </p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- What to Do -->
                <div class="bg-blue-50 rounded-2xl p-6">
                    <h3 class="text-gray-900 font-semibold text-lg mb-4 flex items-center gap-2">
                        <i class="bi bi-question-circle text-blue-600 text-2xl"></i>
                        Apa yang Harus Saya Lakukan?
                    </h3>
                    
                    <div class="space-y-4">
                        <div class="flex items-start gap-3">
                            <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center flex-shrink-0 text-white font-bold">
                                1
                            </div>
                            <div>
                                <h4 class="text-gray-900 font-semibold mb-1">Hubungi Tim HR</h4>
                                <p class="text-gray-700 text-sm">
                                    Segera hubungi tim HR kami untuk menjelaskan situasi Anda. Mereka akan meninjau kasus Anda dan menentukan langkah selanjutnya.
                                </p>
                            </div>
                        </div>

                        <div class="flex items-start gap-3">
                            <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center flex-shrink-0 text-white font-bold">
                                2
                            </div>
                            <div>
                                <h4 class="text-gray-900 font-semibold mb-1">Kemungkinan Penjadwalan Ulang</h4>
                                <p class="text-gray-700 text-sm">
                                    Tergantung pada kebijakan perusahaan dan alasan keterlambatan, tim HR mungkin dapat memberikan kesempatan untuk mengerjakan tes lagi.
                                </p>
                            </div>
                        </div>

                        <div class="flex items-start gap-3">
                            <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center flex-shrink-0 text-white font-bold">
                                3
                            </div>
                            <div>
                                <h4 class="text-gray-900 font-semibold mb-1">Jelaskan Kendala Teknis</h4>
                                <p class="text-gray-700 text-sm">
                                    Jika Anda mengalami masalah teknis (koneksi internet, dll), segera laporkan kepada tim HR dengan bukti yang relevan.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Contact Information -->
                <div class="bg-gradient-to-r from-purple-50 to-blue-50 rounded-2xl p-6">
                    <h3 class="text-gray-900 font-semibold text-lg mb-4 flex items-center gap-2">
                        <i class="bi bi-telephone-fill text-purple-600"></i>
                        Informasi Kontak
                    </h3>
                    
                    <p class="text-gray-700 text-sm mb-4">
                        Silakan hubungi tim rekrutmen kami melalui salah satu channel berikut:
                    </p>

                    <div class="space-y-3">
                        <a href="mailto:recruitment@bizmark.id" class="flex items-center gap-3 p-3 bg-white rounded-xl hover:shadow-md transition-all">
                            <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                                <i class="bi bi-envelope-fill text-blue-600 text-xl"></i>
                            </div>
                            <div>
                                <p class="text-gray-900 font-semibold">Email</p>
                                <p class="text-blue-600 text-sm">recruitment@bizmark.id</p>
                            </div>
                        </a>

                        <a href="https://wa.me/6281234567890" target="_blank" class="flex items-center gap-3 p-3 bg-white rounded-xl hover:shadow-md transition-all">
                            <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                                <i class="bi bi-whatsapp text-green-600 text-xl"></i>
                            </div>
                            <div>
                                <p class="text-gray-900 font-semibold">WhatsApp</p>
                                <p class="text-green-600 text-sm">+62 812-3456-7890</p>
                            </div>
                        </a>

                        <a href="tel:+622112345678" class="flex items-center gap-3 p-3 bg-white rounded-xl hover:shadow-md transition-all">
                            <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center">
                                <i class="bi bi-telephone-fill text-purple-600 text-xl"></i>
                            </div>
                            <div>
                                <p class="text-gray-900 font-semibold">Telepon</p>
                                <p class="text-purple-600 text-sm">021-1234-5678</p>
                            </div>
                        </a>
                    </div>

                    <div class="mt-4 p-3 bg-white rounded-xl border-2 border-blue-200">
                        <p class="text-xs text-gray-600 mb-1">
                            <i class="bi bi-clock text-blue-600"></i> <strong>Jam Operasional:</strong>
                        </p>
                        <p class="text-xs text-gray-700">
                            Senin - Jumat: 09:00 - 17:00 WIB<br>
                            Sabtu - Minggu: Libur
                        </p>
                    </div>
                </div>

                <!-- Important Notes -->
                <div class="border-l-4 border-yellow-400 bg-yellow-50 p-4 rounded-r-xl">
                    <div class="flex items-start gap-3">
                        <i class="bi bi-exclamation-triangle-fill text-yellow-600 text-xl"></i>
                        <div>
                            <h4 class="text-yellow-900 font-semibold mb-1">Catatan Penting:</h4>
                            <ul class="text-yellow-800 text-sm space-y-1 list-disc list-inside">
                                <li>Keputusan penjadwalan ulang sepenuhnya berada di tangan tim HR</li>
                                <li>Tidak semua kasus akan mendapatkan kesempatan ulang</li>
                                <li>Pastikan untuk menghubungi dalam 24 jam untuk respons lebih cepat</li>
                                <li>Simpan screenshot halaman ini sebagai referensi</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row gap-3 pt-4">
                    <a href="mailto:recruitment@bizmark.id?subject=Tes%20Kadaluarsa%20-%20{{ urlencode($testSession->testTemplate->title) }}&body=Saya%20ingin%20melaporkan%20bahwa%20saya%20tidak%20dapat%20menyelesaikan%20tes%20karena..." 
                       class="flex-1 bg-gradient-to-r from-blue-600 to-purple-600 text-white font-semibold py-3 px-6 rounded-xl hover:from-blue-700 hover:to-purple-700 transition-all duration-300 transform hover:scale-105 text-center">
                        <i class="bi bi-envelope mr-2"></i>Kirim Email ke HR
                    </a>
                    <button onclick="window.close()" class="flex-1 bg-gray-200 text-gray-700 font-semibold py-3 px-6 rounded-xl hover:bg-gray-300 transition-all duration-300">
                        <i class="bi bi-x-lg mr-2"></i>Tutup Halaman
                    </button>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="text-center mt-6 text-white">
            <p class="text-sm opacity-90">
                &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
            </p>
        </div>
    </div>

    <script>
        // Prevent back button
        history.pushState(null, null, location.href);
        window.onpopstate = function () {
            history.go(1);
        };
    </script>
</body>
</html>
