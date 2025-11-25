<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tes Selesai - {{ config('app.name') }}</title>
    
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
        
        .success-animation {
            animation: scaleIn 0.5s ease-out;
        }
        
        @keyframes scaleIn {
            0% {
                transform: scale(0);
                opacity: 0;
            }
            50% {
                transform: scale(1.1);
            }
            100% {
                transform: scale(1);
                opacity: 1;
            }
        }
        
        .card-shadow {
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        }
        
        .confetti {
            position: fixed;
            width: 10px;
            height: 10px;
            background: #ffd700;
            position: absolute;
            animation: confetti-fall 3s linear infinite;
        }
        
        @keyframes confetti-fall {
            to {
                transform: translateY(100vh) rotate(360deg);
                opacity: 0;
            }
        }
    </style>
</head>
<body class="flex items-center justify-center p-4">
    <!-- Confetti Effect (optional) -->
    <div id="confetti-container"></div>

    <div class="max-w-2xl w-full">
        <!-- Success Card -->
        <div class="bg-white rounded-3xl shadow-2xl overflow-hidden card-shadow">
            <!-- Header with Success Icon -->
            <div class="bg-gradient-to-r from-green-400 to-green-600 p-8 text-center">
                <div class="success-animation">
                    <div class="w-24 h-24 bg-white rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="bi bi-check-circle-fill text-green-500 text-6xl"></i>
                    </div>
                </div>
                <h1 class="text-3xl font-bold text-white mb-2">Tes Berhasil Diselesaikan!</h1>
                <p class="text-green-100 text-lg">
                    Terima kasih telah menyelesaikan tes dengan baik
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
                    <div class="flex items-center justify-between">
                        <span class="text-gray-600 font-medium">Waktu Selesai:</span>
                        <span class="text-gray-900 font-semibold">{{ $testSession->completed_at->format('d M Y, H:i') }} WIB</span>
                    </div>
                </div>

                <!-- Score (if auto-graded) -->
                @if($testSession->score !== null)
                <div class="bg-gradient-to-r from-blue-50 to-purple-50 rounded-2xl p-6 text-center">
                    <p class="text-gray-600 mb-2">Skor Anda:</p>
                    <div class="flex items-center justify-center gap-2">
                        <span class="text-5xl font-bold bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">
                            {{ number_format($testSession->score, 1) }}
                        </span>
                        <span class="text-2xl text-gray-500">/ 100</span>
                    </div>
                    
                    @if($testSession->score >= $testSession->testTemplate->passing_score)
                        <div class="mt-4 bg-green-100 border border-green-300 rounded-xl p-3">
                            <i class="bi bi-trophy-fill text-green-600 text-2xl"></i>
                            <p class="text-green-700 font-semibold mt-1">Selamat! Anda Lulus Tes</p>
                            <p class="text-green-600 text-sm">
                                Nilai passing: {{ $testSession->testTemplate->passing_score }}%
                            </p>
                        </div>
                    @else
                        <div class="mt-4 bg-yellow-100 border border-yellow-300 rounded-xl p-3">
                            <i class="bi bi-info-circle-fill text-yellow-600 text-2xl"></i>
                            <p class="text-yellow-700 font-semibold mt-1">Terima Kasih Atas Partisipasi Anda</p>
                            <p class="text-yellow-600 text-sm">
                                Nilai passing: {{ $testSession->testTemplate->passing_score }}%
                            </p>
                        </div>
                    @endif
                </div>
                @else
                <!-- For Essay/Manual Grading -->
                <div class="bg-blue-50 border border-blue-200 rounded-2xl p-6">
                    <div class="flex items-start gap-3">
                        <i class="bi bi-hourglass-split text-blue-600 text-3xl"></i>
                        <div>
                            <h3 class="text-blue-900 font-semibold text-lg mb-1">Jawaban Sedang Diproses</h3>
                            <p class="text-blue-700 text-sm leading-relaxed">
                                Tes Anda berisi soal essay yang memerlukan penilaian manual oleh tim HR kami. 
                                Hasil akan diinformasikan melalui email dalam 2-3 hari kerja.
                            </p>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Statistics -->
                <div class="grid grid-cols-3 gap-4">
                    <div class="bg-gray-50 rounded-xl p-4 text-center">
                        <i class="bi bi-list-check text-gray-400 text-2xl mb-2"></i>
                        <p class="text-2xl font-bold text-gray-900">
                            {{ $testSession->testAnswers->count() }} / {{ $testSession->testTemplate->total_questions ?? 0 }}
                        </p>
                        <p class="text-sm text-gray-600">Soal Dijawab</p>
                    </div>
                    <div class="bg-gray-50 rounded-xl p-4 text-center">
                        <i class="bi bi-clock text-gray-400 text-2xl mb-2"></i>
                        <p class="text-2xl font-bold text-gray-900">
                            @if($testSession->started_at && $testSession->completed_at)
                                {{ $testSession->started_at->diffInMinutes($testSession->completed_at) }}
                            @elseif($testSession->time_taken_minutes)
                                {{ $testSession->time_taken_minutes }}
                            @else
                                {{ $testSession->testTemplate->duration_minutes }}
                            @endif
                        </p>
                        <p class="text-sm text-gray-600">Menit Digunakan</p>
                    </div>
                    <div class="bg-gray-50 rounded-xl p-4 text-center">
                        <i class="bi bi-window text-gray-400 text-2xl mb-2"></i>
                        <p class="text-2xl font-bold text-gray-900">{{ $testSession->tab_switches ?? 0 }}</p>
                        <p class="text-sm text-gray-600">Tab Switch</p>
                    </div>
                </div>

                <!-- Next Steps -->
                <div class="bg-gradient-to-r from-purple-50 to-blue-50 rounded-2xl p-6">
                    <h3 class="text-gray-900 font-semibold text-lg mb-3 flex items-center gap-2">
                        <i class="bi bi-lightbulb text-yellow-500"></i>
                        Langkah Selanjutnya
                    </h3>
                    <ul class="space-y-2 text-gray-700">
                        <li class="flex items-start gap-2">
                            <i class="bi bi-check-circle-fill text-green-500 mt-1"></i>
                            <span>Tim HR kami akan meninjau hasil tes Anda</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <i class="bi bi-check-circle-fill text-green-500 mt-1"></i>
                            <span>Anda akan dihubungi melalui email untuk tahap selanjutnya</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <i class="bi bi-check-circle-fill text-green-500 mt-1"></i>
                            <span>Proses review biasanya memakan waktu 3-5 hari kerja</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <i class="bi bi-check-circle-fill text-green-500 mt-1"></i>
                            <span>Pastikan untuk mengecek email (termasuk folder spam) secara berkala</span>
                        </li>
                    </ul>
                </div>

                <!-- Contact Info -->
                <div class="border-t border-gray-200 pt-6">
                    <h3 class="text-gray-900 font-semibold mb-3">Butuh Bantuan?</h3>
                    <p class="text-gray-600 text-sm mb-3">
                        Jika ada pertanyaan atau kendala, jangan ragu untuk menghubungi tim HR kami:
                    </p>
                    <div class="flex flex-col gap-2 text-sm">
                        <a href="mailto:cs@bizmark.id" class="text-blue-600 hover:text-blue-700 flex items-center gap-2">
                            <i class="bi bi-envelope"></i>
                            cs@bizmark.id
                        </a>
                        <a href="https://wa.me/6283879602855" class="text-green-600 hover:text-green-700 flex items-center gap-2">
                            <i class="bi bi-whatsapp"></i>
                            +62 838 7960 2855
                        </a>
                    </div>
                </div>

                <!-- Close Button -->
                <div class="text-center pt-4">
                    <button onclick="window.close()" class="bg-gradient-to-r from-purple-600 to-blue-600 text-white font-semibold py-3 px-8 rounded-xl hover:from-purple-700 hover:to-blue-700 transition-all duration-300 transform hover:scale-105">
                        <i class="bi bi-x-lg mr-2"></i>Tutup Halaman
                    </button>
                    <p class="text-gray-500 text-xs mt-3">
                        Anda dapat menutup halaman ini dengan aman
                    </p>
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
        // Optional: Generate confetti effect
        function createConfetti() {
            const container = document.getElementById('confetti-container');
            const colors = ['#ffd700', '#ff6b6b', '#4ecdc4', '#45b7d1', '#f7b731'];
            
            for (let i = 0; i < 50; i++) {
                const confetti = document.createElement('div');
                confetti.className = 'confetti';
                confetti.style.left = Math.random() * 100 + 'vw';
                confetti.style.backgroundColor = colors[Math.floor(Math.random() * colors.length)];
                confetti.style.animationDelay = Math.random() * 3 + 's';
                confetti.style.animationDuration = (Math.random() * 2 + 2) + 's';
                container.appendChild(confetti);
                
                setTimeout(() => confetti.remove(), 3000);
            }
        }

        // Show confetti if passed
        @if($testSession->score !== null && $testSession->score >= $testSession->testTemplate->passing_score)
            createConfetti();
        @endif

        // Prevent back button
        history.pushState(null, null, location.href);
        window.onpopstate = function () {
            history.go(1);
        };
    </script>
</body>
</html>
