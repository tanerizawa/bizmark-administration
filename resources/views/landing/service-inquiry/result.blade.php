<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Hasil Analisis - {{ $inquiry->inquiry_number }} - Bizmark.ID</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        linkedin: {
                            50: '#e7f3f8',
                            100: '#cce7f1',
                            200: '#99cfe3',
                            500: '#0077B5',
                            600: '#005582',
                            700: '#004161',
                            900: '#001820',
                        },
                        gold: {
                            400: '#F2CD49',
                        }
                    }
                }
            }
        }
    </script>
    
    <style>
        [x-cloak] { display: none !important; }
        
        .pulse-loader {
            animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }
        
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: .5; }
        }
        
        .spinner {
            border: 3px solid rgba(0, 119, 181, 0.1);
            border-top: 3px solid #0077B5;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        .fade-in {
            animation: fadeIn 0.5s ease-in;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body class="bg-gradient-to-br from-linkedin-50 to-white min-h-screen">
    <!-- Header -->
    <header class="bg-white shadow-sm sticky top-0 z-50">
        <div class="max-w-5xl mx-auto px-4 py-4 flex items-center justify-between">
            <a href="/" class="flex items-center space-x-2">
                <div class="w-10 h-10 bg-gradient-to-br from-linkedin-500 to-linkedin-600 rounded-lg flex items-center justify-center shadow-md">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                </div>
                <div>
                    <span class="text-xl font-bold text-linkedin-700">Bizmark</span>
                    <span class="text-xl font-bold text-gold-400">.ID</span>
                </div>
            </a>
            <a href="/" class="text-sm text-gray-600 hover:text-linkedin-500 transition">
                Kembali ke Beranda
            </a>
        </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-5xl mx-auto px-4 py-8 pb-20" x-data="resultPage('{{ $inquiry->inquiry_number }}')" x-init="init()">
        
        <!-- Processing State -->
        <div x-show="status === 'processing'" x-cloak class="text-center py-12">
            <div class="inline-block mb-6">
                <div class="spinner"></div>
            </div>
            <h1 class="text-2xl md:text-3xl font-bold text-gray-900 mb-3">
                🤖 AI Sedang Menganalisis...
            </h1>
            <p class="text-gray-600 text-lg mb-6">
                Mohon tunggu sebentar, kami sedang memproses data Anda
            </p>
            <div class="max-w-md mx-auto bg-white rounded-xl p-6 shadow-lg">
                <div class="flex items-center justify-between text-sm text-gray-600 mb-3">
                    <span>Progress</span>
                    <span class="font-semibold" x-text="pollCount + ' detik'"></span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-gradient-to-r from-linkedin-500 to-linkedin-600 h-2 rounded-full transition-all duration-1000"
                         :style="`width: ${Math.min((pollCount / 30) * 100, 90)}%`"></div>
                </div>
                <p class="text-xs text-gray-500 mt-3">Biasanya membutuhkan 10-30 detik</p>
            </div>
        </div>

        <!-- Results -->
        <div x-show="status === 'completed'" x-cloak class="fade-in">
            <!-- Success Badge -->
            <div class="text-center mb-8">
                <div class="inline-block bg-green-100 text-green-700 px-6 py-3 rounded-full text-sm font-semibold mb-4">
                    ✅ Analisis Selesai!
                </div>
                <h1 class="text-2xl md:text-3xl font-bold text-gray-900 mb-2">
                    Hasil Analisis Perizinan
                </h1>
                <p class="text-gray-600">
                    Untuk: <span class="font-semibold">{{ $inquiry->company_name }}</span>
                </p>
                <p class="text-sm text-gray-500">
                    Inquiry: {{ $inquiry->inquiry_number }} • {{ $inquiry->created_at->format('d M Y, H:i') }}
                </p>
            </div>

            <!-- Summary Card -->
            <div class="bg-gradient-to-br from-linkedin-500 to-linkedin-600 rounded-2xl p-6 md:p-8 text-white shadow-xl mb-6">
                <h2 class="text-xl font-bold mb-4">📊 Ringkasan</h2>
                <div class="grid md:grid-cols-3 gap-6">
                    <div>
                        <div class="text-linkedin-100 text-sm mb-1">Total Estimasi Biaya</div>
                        <div class="text-2xl font-bold" x-text="analysis ? formatCurrency(analysis.total_estimated_cost.min) + ' - ' + formatCurrency(analysis.total_estimated_cost.max) : '-'"></div>
                    </div>
                    <div>
                        <div class="text-linkedin-100 text-sm mb-1">Timeline Estimasi</div>
                        <div class="text-2xl font-bold" x-text="analysis?.total_estimated_timeline || '-'"></div>
                    </div>
                    <div>
                        <div class="text-linkedin-100 text-sm mb-1">Tingkat Kompleksitas</div>
                        <div class="text-2xl font-bold">
                            <span x-text="analysis?.complexity_score || '-'"></span>
                            <span class="text-lg">/10</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recommended Permits -->
            <div class="bg-white rounded-2xl shadow-xl p-6 md:p-8 mb-6">
                <h2 class="text-xl font-bold text-gray-900 mb-4">🎯 Izin yang Direkomendasikan</h2>
                <div class="space-y-4">
                    <template x-for="(permit, index) in analysis?.recommended_permits || []" :key="index">
                        <div class="border-l-4 rounded-lg p-4 transition hover:shadow-md"
                             :class="{
                                 'border-red-500 bg-red-50': permit.priority === 'critical',
                                 'border-orange-500 bg-orange-50': permit.priority === 'high',
                                 'border-blue-500 bg-blue-50': permit.priority === 'medium'
                             }">
                            <div class="flex items-start justify-between mb-2">
                                <div class="flex-1">
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="text-lg font-bold text-gray-900" x-text="(index + 1) + '. ' + permit.name"></span>
                                        <span class="text-xs font-semibold px-2 py-1 rounded-full"
                                              :class="{
                                                  'bg-red-200 text-red-800': permit.priority === 'critical',
                                                  'bg-orange-200 text-orange-800': permit.priority === 'high',
                                                  'bg-blue-200 text-blue-800': permit.priority === 'medium'
                                              }"
                                              x-text="permit.priority === 'critical' ? 'WAJIB' : (permit.priority === 'high' ? 'PENTING' : 'PERLU')"></span>
                                    </div>
                                    <p class="text-sm text-gray-600 mb-2" x-text="permit.description"></p>
                                    <div class="flex flex-wrap gap-4 text-xs text-gray-500">
                                        <span>⏱️ <span x-text="permit.estimated_timeline"></span></span>
                                        <span>💰 <span x-text="permit.estimated_cost_range"></span></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Risk Factors -->
            <div class="bg-white rounded-2xl shadow-xl p-6 md:p-8 mb-6">
                <h2 class="text-xl font-bold text-gray-900 mb-4">⚠️ Faktor Risiko & Perhatian</h2>
                <ul class="space-y-3">
                    <template x-for="(risk, index) in analysis?.risk_factors || []" :key="index">
                        <li class="flex items-start">
                            <span class="text-orange-500 mr-3 text-lg">⚠️</span>
                            <span class="text-gray-700" x-text="risk"></span>
                        </li>
                    </template>
                </ul>
            </div>

            <!-- Next Steps -->
            <div class="bg-white rounded-2xl shadow-xl p-6 md:p-8 mb-6">
                <h2 class="text-xl font-bold text-gray-900 mb-4">📌 Langkah Selanjutnya</h2>
                <ol class="space-y-3">
                    <template x-for="(step, index) in analysis?.next_steps || []" :key="index">
                        <li class="flex items-start">
                            <span class="flex items-center justify-center w-6 h-6 bg-linkedin-500 text-white rounded-full text-sm font-bold mr-3 flex-shrink-0" x-text="index + 1"></span>
                            <span class="text-gray-700 pt-0.5" x-text="step"></span>
                        </li>
                    </template>
                </ol>
            </div>

            <!-- Limitations Notice -->
            <div class="bg-yellow-50 border-l-4 border-yellow-400 rounded-lg p-6 mb-6">
                <div class="flex items-start">
                    <svg class="w-6 h-6 text-yellow-600 mr-3 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                    </svg>
                    <div class="text-sm text-gray-700">
                        <p class="font-semibold mb-1">Catatan Penting:</p>
                        <p x-text="analysis?.limitations"></p>
                    </div>
                </div>
            </div>

            <!-- CTA Section -->
            <div class="bg-gradient-to-r from-linkedin-600 to-linkedin-700 rounded-2xl p-8 text-white text-center shadow-2xl mb-6">
                <h2 class="text-2xl font-bold mb-3">🚀 Siap Memulai Proses Perizinan?</h2>
                <p class="text-linkedin-100 mb-6 max-w-2xl mx-auto">
                    Daftar sekarang untuk mendapatkan analisis lengkap dengan dokumen checklist detail, 
                    timeline breakdown, pendampingan konsultan bersertifikat, dan monitoring real-time.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
                    <a href="{{ route('client.register') }}" 
                       class="inline-block px-8 py-4 bg-gold-400 hover:bg-gold-500 text-gray-900 font-bold rounded-lg shadow-lg transform hover:-translate-y-1 transition">
                        ✨ Daftar Portal Lengkap
                    </a>
                    <a href="https://wa.me/6283879602855?text=Halo, saya tertarik dengan layanan perizinan. Inquiry: {{ $inquiry->inquiry_number }}" 
                       target="_blank"
                       class="inline-block px-8 py-4 bg-white/10 hover:bg-white/20 backdrop-blur-sm border-2 border-white/30 text-white font-semibold rounded-lg transition">
                        💬 Chat via WhatsApp
                    </a>
                </div>
            </div>

            <!-- Benefits Grid -->
            <div class="grid md:grid-cols-3 gap-6 mb-8">
                <div class="bg-white rounded-xl p-6 shadow-md text-center">
                    <div class="text-4xl mb-3">📋</div>
                    <h3 class="font-bold text-gray-900 mb-2">Dokumen Checklist</h3>
                    <p class="text-sm text-gray-600">Panduan lengkap dokumen yang diperlukan per izin</p>
                </div>
                <div class="bg-white rounded-xl p-6 shadow-md text-center">
                    <div class="text-4xl mb-3">👨‍💼</div>
                    <h3 class="font-bold text-gray-900 mb-2">Konsultan Bersertifikat</h3>
                    <p class="text-sm text-gray-600">Pendampingan dari ahli perizinan profesional</p>
                </div>
                <div class="bg-white rounded-xl p-6 shadow-md text-center">
                    <div class="text-4xl mb-3">📊</div>
                    <h3 class="font-bold text-gray-900 mb-2">Monitoring Real-Time</h3>
                    <p class="text-sm text-gray-600">Pantau progress aplikasi Anda 24/7 di portal</p>
                </div>
            </div>

            <!-- Share & Download -->
            <div class="text-center">
                <p class="text-sm text-gray-600 mb-3">Hasil analisis sudah dikirim ke email Anda: <span class="font-semibold">{{ $inquiry->email }}</span></p>
                <div class="flex justify-center gap-3">
                    <button @click="window.print()" class="px-6 py-2 bg-white border-2 border-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-50 transition">
                        📥 Cetak/Simpan PDF
                    </button>
                </div>
            </div>
        </div>

        <!-- Error State -->
        <div x-show="status === 'error'" x-cloak class="text-center py-12">
            <div class="text-6xl mb-4">😔</div>
            <h1 class="text-2xl font-bold text-gray-900 mb-3">
                Terjadi Kesalahan
            </h1>
            <p class="text-gray-600 mb-6" x-text="errorMessage"></p>
            <a href="{{ route('landing.service-inquiry.create') }}" 
               class="inline-block px-6 py-3 bg-linkedin-500 hover:bg-linkedin-600 text-white font-semibold rounded-lg transition">
                Coba Lagi
            </a>
        </div>

    </main>

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <script>
        function resultPage(inquiryNumber) {
            return {
                status: 'processing', // processing, completed, error
                analysis: null,
                pollCount: 0,
                pollInterval: null,
                errorMessage: '',

                init() {
                    this.checkStatus();
                },

                async checkStatus() {
                    try {
                        const response = await fetch(`/konsultasi-gratis/api/status/${inquiryNumber}`);
                        const data = await response.json();

                        if (data.success && data.status === 'completed') {
                            this.status = 'completed';
                            this.analysis = data.analysis;
                            if (this.pollInterval) {
                                clearInterval(this.pollInterval);
                            }
                        } else if (data.status === 'processing') {
                            // Keep polling
                            if (!this.pollInterval) {
                                this.pollInterval = setInterval(() => {
                                    this.pollCount++;
                                    this.checkStatus();
                                }, 2000); // Poll every 2 seconds
                            }
                        } else {
                            this.status = 'error';
                            this.errorMessage = data.message || 'Gagal memuat hasil analisis';
                        }
                    } catch (error) {
                        console.error('Poll error:', error);
                        this.status = 'error';
                        this.errorMessage = 'Terjadi kesalahan jaringan';
                    }
                },

                formatCurrency(value) {
                    if (!value) return 'Rp 0';
                    return 'Rp ' + new Intl.NumberFormat('id-ID').format(value);
                }
            }
        }
    </script>
</body>
</html>
