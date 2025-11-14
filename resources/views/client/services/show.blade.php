@extends('client.layouts.app')

@section('title', 'Rekomendasi Perizinan - BizMark')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Back Button -->
        <a href="{{ route('client.services.index') }}" class="inline-flex items-center text-blue-600 dark:text-blue-400 hover:underline mb-6">
            <i class="fas fa-arrow-left mr-2"></i>
            Kembali ke Katalog
        </a>

        @if(session('error'))
        <div class="mb-6 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4">
            <div class="flex items-start">
                <i class="fas fa-exclamation-circle text-red-600 dark:text-red-400 mt-0.5 mr-3"></i>
                <div>
                    <strong class="font-semibold text-red-800 dark:text-red-300">Error:</strong>
                    <p class="text-red-700 dark:text-red-300 mt-1">{{ session('error') }}</p>
                    <p class="text-sm text-red-600 dark:text-red-400 mt-2">
                        Silakan hubungi administrator atau coba lagi nanti.
                    </p>
                </div>
            </div>
        </div>
        @endif

        @if(!$recommendation)
        <!-- Advanced Loading Animation -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="p-12 text-center">
                <!-- Animated Icon Container -->
                <div class="relative inline-block mb-6">
                    <!-- Rotating Rings -->
                    <div class="absolute inset-0 flex items-center justify-center">
                        <div class="w-32 h-32 border-4 border-blue-200 dark:border-blue-900 rounded-full animate-spin-slow"></div>
                    </div>
                    <div class="absolute inset-0 flex items-center justify-center">
                        <div class="w-24 h-24 border-4 border-purple-200 dark:border-purple-900 rounded-full animate-spin-reverse"></div>
                    </div>
                    
                    <!-- Center Icon -->
                    <div class="relative w-32 h-32 flex items-center justify-center">
                        <i class="fas fa-brain text-5xl text-blue-600 dark:text-blue-400 animate-pulse"></i>
                    </div>
                </div>

                <!-- Title with typing effect -->
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-3">
                    <span id="loadingText">Menganalisis Kebutuhan Perizinan</span>
                    <span class="animate-pulse">...</span>
                </h2>
                
                <!-- Progress Steps -->
                <div class="max-w-md mx-auto mb-6" x-data="{ 
                    steps: ['Memproses KBLI', 'Menganalisis Regulasi', 'Menyusun Rekomendasi'],
                    currentStep: 0,
                    init() {
                        setInterval(() => {
                            this.currentStep = (this.currentStep + 1) % this.steps.length;
                        }, 3000);
                    }
                }">
                    <div class="flex justify-between items-center mb-2">
                        <template x-for="(step, index) in steps" :key="index">
                            <div class="flex items-center">
                                <div 
                                    class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-semibold transition-all duration-500"
                                    :class="index <= currentStep ? 'bg-blue-600 text-white scale-110' : 'bg-gray-200 dark:bg-gray-700 text-gray-500'"
                                >
                                    <span x-show="index < currentStep">✓</span>
                                    <span x-show="index >= currentStep" x-text="index + 1"></span>
                                </div>
                                <div 
                                    x-show="index < steps.length - 1" 
                                    class="w-16 h-1 mx-2 transition-all duration-500"
                                    :class="index < currentStep ? 'bg-blue-600' : 'bg-gray-200 dark:bg-gray-700'"
                                ></div>
                            </div>
                        </template>
                    </div>
                    <p class="text-sm text-gray-600 dark:text-gray-400 font-medium transition-all duration-500" x-text="steps[currentStep]"></p>
                </div>

                <!-- Fun Facts Carousel -->
                <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4 max-w-lg mx-auto" x-data="{
                    facts: [
                        '💡 Sistem kami menganalisis ribuan regulasi untuk memberikan rekomendasi terbaik',
                        '🚀 Proses perizinan yang tepat dapat menghemat waktu hingga 60%',
                        '📊 Kami telah membantu ribuan UKM dalam proses perizinan',
                        '⚡ Rekomendasi disesuaikan dengan skala dan lokasi usaha Anda',
                        '🎯 Tingkat akurasi rekomendasi kami mencapai 95%'
                    ],
                    currentFact: 0,
                    init() {
                        setInterval(() => {
                            this.currentFact = (this.currentFact + 1) % this.facts.length;
                        }, 4000);
                    }
                }">
                    <p class="text-sm text-gray-700 dark:text-gray-300 transition-all duration-500" x-text="facts[currentFact]"></p>
                </div>

                <!-- Animated Progress Bar -->
                <div class="mt-6 max-w-md mx-auto">
                    <div class="h-2 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
                        <div class="h-full bg-gradient-to-r from-blue-600 via-purple-600 to-blue-600 animate-progress-bar"></div>
                    </div>
                </div>
            </div>

            <!-- Bottom Decorative Wave -->
            <div class="h-2 bg-gradient-to-r from-blue-600 via-purple-600 to-pink-600 animate-gradient-x"></div>
        </div>

        <!-- Custom Animations CSS -->
        <style>
            @keyframes spin-slow {
                from { transform: rotate(0deg); }
                to { transform: rotate(360deg); }
            }
            @keyframes spin-reverse {
                from { transform: rotate(360deg); }
                to { transform: rotate(0deg); }
            }
            @keyframes progress-bar {
                0% { transform: translateX(-100%); }
                100% { transform: translateX(100%); }
            }
            @keyframes gradient-x {
                0%, 100% { background-position: 0% 50%; }
                50% { background-position: 100% 50%; }
            }
            .animate-spin-slow {
                animation: spin-slow 3s linear infinite;
            }
            .animate-spin-reverse {
                animation: spin-reverse 4s linear infinite;
            }
            .animate-progress-bar {
                animation: progress-bar 2s ease-in-out infinite;
            }
            .animate-gradient-x {
                background-size: 200% 200%;
                animation: gradient-x 3s ease infinite;
            }
        </style>
        @else
        @php
            $scaleOptions = [
                'mikro' => [
                    'label' => 'Usaha Mikro',
                    'summary' => 'Aset ≤ Rp 50 juta atau omzet ≤ Rp 300 juta/tahun'
                ],
                'kecil' => [
                    'label' => 'Usaha Kecil',
                    'summary' => 'Aset Rp 50-500 juta atau omzet Rp 300 juta - Rp 2,5 miliar/tahun'
                ],
                'menengah' => [
                    'label' => 'Usaha Menengah',
                    'summary' => 'Aset Rp 500 juta - Rp 10 miliar atau omzet Rp 2,5 - 50 miliar/tahun'
                ],
                'besar' => [
                    'label' => 'Usaha Besar',
                    'summary' => 'Aset > Rp 10 miliar atau omzet > Rp 50 miliar/tahun'
                ],
            ];
            $locationOptions = [
                'perkotaan' => [
                    'label' => 'Area Perkotaan',
                    'summary' => 'Lokasi dengan regulasi zonasi dan kepadatan tinggi'
                ],
                'pedesaan' => [
                    'label' => 'Area Pedesaan',
                    'summary' => 'Proses cenderung fleksibel dengan keterlibatan pemda'
                ],
                'kawasan_industri' => [
                    'label' => 'Kawasan Industri',
                    'summary' => 'Membutuhkan koordinasi pengelola kawasan dan kementerian teknis'
                ],
            ];
            $scaleInfo = $scaleOptions[$businessScale] ?? [
                'label' => 'Belum dipilih',
                'summary' => 'Gunakan rekomendasi umum untuk melihat keseluruhan izin'
            ];
            $locationInfo = $locationOptions[$locationType] ?? [
                'label' => 'Tidak ditentukan',
                'summary' => 'Lokasi default digunakan hingga Anda memperbaruinya'
            ];
            $confidencePercent = max(5, min(100, round(($recommendation->confidence_score ?? 0) * 100)));
            $totalPermits = count($recommendation->recommended_permits ?? []);
            $mandatoryCount = $recommendation->mandatory_permits_count ?? 0;
            if ($mandatoryCount <= 2) {
                $complexityLevel = 'Rendah';
                $complexityCopy = 'Kebutuhan izin relatif sederhana dan dapat ditangani cepat.';
                $complexityColor = 'green';
            } elseif ($mandatoryCount <= 5) {
                $complexityLevel = 'Menengah';
                $complexityCopy = 'Ada beberapa izin prioritas yang perlu dipantau bertahap.';
                $complexityColor = 'yellow';
            } else {
                $complexityLevel = 'Tinggi';
                $complexityCopy = 'Perlu orkestrasi ketat dan koordinasi lintas instansi.';
                $complexityColor = 'red';
            }
            $costRangeMin = $recommendation->total_cost_range['min'] ?? 0;
            $costRangeMax = $recommendation->total_cost_range['max'] ?? 0;
        @endphp
        <!-- Success Entry Animation -->
        <div x-data="{ show: false, showSuccess: true }" x-init="setTimeout(() => show = true, 100)">
            
            <!-- Success Notification Banner -->
            <div 
                x-show="showSuccess"
                x-transition:enter="transition ease-out duration-500 delay-200"
                x-transition:enter-start="opacity-0 transform -translate-y-4"
                x-transition:enter-end="opacity-100 transform translate-y-0"
                x-transition:leave="transition ease-in duration-300"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="bg-gradient-to-r from-green-500 to-emerald-600 text-white rounded-lg shadow-lg p-4 mb-6 relative overflow-hidden"
            >
                <!-- Animated Background -->
                <div class="absolute inset-0 bg-white opacity-10">
                    <div class="absolute inset-0 animate-pulse"></div>
                </div>
                
                <div class="relative flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="bg-white bg-opacity-20 rounded-full p-3 mr-4">
                            <i class="fas fa-check-circle text-2xl"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-lg mb-1">✨ Rekomendasi Berhasil Dibuat!</h3>
                            <p class="text-sm text-green-50">
                                Sistem kami telah menganalisis {{ count($recommendation->recommended_permits ?? []) }} jenis izin berdasarkan data regulasi terkini dan pengalaman proyek perizinan yang telah kami tangani
                            </p>
                        </div>
                    </div>
                    <button 
                        @click="showSuccess = false"
                        class="text-white hover:text-green-100 transition-colors"
                    >
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
            </div>

            <!-- KBLI Hero -->
            <div 
                class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-slate-900 via-blue-900 to-emerald-900 text-white border border-white/10 shadow-xl mb-6"
                x-show="show"
                x-transition:enter="transition ease-out duration-500"
                x-transition:enter-start="opacity-0 transform -translate-y-4"
                x-transition:enter-end="opacity-100 transform translate-y-0"
            >
                <div class="absolute inset-0 opacity-40">
                    <div class="absolute -left-10 -top-10 w-64 h-64 bg-blue-500 rounded-full blur-3xl"></div>
                    <div class="absolute -right-12 top-6 w-52 h-52 bg-emerald-400 rounded-full blur-3xl"></div>
                    <div class="absolute bottom-0 left-1/2 -translate-x-1/2 w-96 h-96 bg-slate-900/40 rounded-full blur-3xl"></div>
                </div>
                <div class="relative flex flex-col lg:flex-row gap-8 p-6">
                    <div class="flex-1">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-white/10 border border-white/30 uppercase tracking-widest">
                            KBLI {{ $kbli->code }}
                        </span>
                        <h1 class="text-2xl lg:text-3xl font-bold mt-4 leading-snug">
                            {{ $kbli->description }}
                        </h1>
                        <p class="text-sm text-blue-100/80 mt-2">Sektor {{ $kbli->sector }} • Rekomendasi berbasis regulasi terbaru</p>
                        @if($kbli->notes)
                        <div class="mt-4 bg-white/10 border border-white/20 rounded-xl p-4 text-sm text-blue-50/90 leading-relaxed">
                            <p>{{ $kbli->notes }}</p>
                        </div>
                        @endif
                        <div class="mt-6 space-y-3">
                            <div>
                                <p class="text-[11px] uppercase tracking-[0.3em] text-blue-200">Akurasi Rekomendasi</p>
                                <div class="mt-2 h-2 bg-white/20 rounded-full overflow-hidden">
                                    <div class="h-full bg-gradient-to-r from-emerald-300 via-blue-300 to-cyan-300 rounded-full" style="width: {{ $confidencePercent }}%;"></div>
                                </div>
                                <div class="flex items-center justify-between text-xs text-blue-100 mt-1">
                                    <span>{{ $confidencePercent }}% yakin</span>
                                    <span>Pembaruan otomatis</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="w-full lg:w-72 space-y-4">
                        <div class="bg-white/10 border border-white/20 rounded-xl p-4 backdrop-blur">
                            <p class="text-xs uppercase tracking-widest text-blue-100">Konteks Bisnis</p>
                            <div class="mt-3 space-y-3">
                                <div class="rounded-lg bg-white/5 p-3">
                                    <p class="text-[11px] uppercase tracking-wide text-blue-100/70">Skala Usaha</p>
                                    <p class="text-base font-semibold">{{ $scaleInfo['label'] }}</p>
                                    <p class="text-xs text-blue-100/80 leading-relaxed">{{ $scaleInfo['summary'] }}</p>
                                </div>
                                <div class="rounded-lg bg-white/5 p-3">
                                    <p class="text-[11px] uppercase tracking-wide text-blue-100/70">Lokasi Operasional</p>
                                    <p class="text-base font-semibold">{{ $locationInfo['label'] }}</p>
                                    <p class="text-xs text-blue-100/80 leading-relaxed">{{ $locationInfo['summary'] }}</p>
                                </div>
                            </div>
                            <div class="mt-4 flex flex-wrap gap-2">
                                @if($businessScale)
                                    <span class="inline-flex items-center px-3 py-1 text-xs font-semibold rounded-full bg-emerald-300/20 border border-emerald-200/40">
                                        <i class="fas fa-chart-line mr-1 text-emerald-200"></i>
                                        Skala: {{ ucfirst($businessScale) }}
                                    </span>
                                @endif
                                @if($locationType)
                                    <span class="inline-flex items-center px-3 py-1 text-xs font-semibold rounded-full bg-cyan-300/20 border border-cyan-200/40">
                                        <i class="fas fa-map-marker-alt mr-1 text-cyan-200"></i>
                                        Lokasi: {{ str_replace('_', ' ', ucfirst($locationType)) }}
                                    </span>
                                @endif
                                @if(!$businessScale && !$locationType)
                                    <span class="inline-flex items-center px-3 py-1 text-xs font-semibold rounded-full bg-white/5 border border-white/20 text-blue-50">
                                        Menggunakan rekomendasi umum
                                    </span>
                                @endif
                            </div>
                            <a 
                                href="{{ route('client.services.context', $kbli->code) }}"
                                class="mt-4 inline-flex items-center gap-2 text-sm font-semibold text-white/90 hover:text-white transition-colors"
                            >
                                <i class="fas fa-edit text-sm"></i>
                                Perbarui Konteks Bisnis
                            </a>
                        </div>
                        <div class="bg-black/30 border border-white/10 rounded-xl p-4 text-sm leading-relaxed">
                            <p class="font-semibold text-white">Ringkasan Singkat</p>
                            <p class="text-blue-100/80 mt-1">
                                Sistem kami menyiapkan {{ $totalPermits }} rekomendasi izin dengan prioritas menyesuaikan profil usaha Anda.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recommendation Highlights -->
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4 mb-6">
                <div 
                    class="relative overflow-hidden rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 p-4"
                    x-show="show"
                    x-transition:enter="transition ease-out duration-500 delay-200"
                    x-transition:enter-start="opacity-0 transform translate-y-4"
                    x-transition:enter-end="opacity-100 transform translate-y-0"
                >
                    <div class="flex items-center justify-between text-xs text-gray-500 dark:text-gray-400">
                        <span>Prioritas Utama</span>
                        <i class="fas fa-layer-group text-blue-500"></i>
                    </div>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2">
                        {{ $mandatoryCount }}
                    </p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Izin wajib dari total {{ $totalPermits }} rekomendasi</p>
                    <div class="mt-4 flex flex-wrap gap-2 text-[11px] uppercase">
                        <span class="px-2 py-1 rounded-full bg-blue-50 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300 font-semibold">Segera Dipenuhi</span>
                        <span class="px-2 py-1 rounded-full bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300 font-semibold">Pantau Status</span>
                    </div>
                </div>

                <div 
                    class="relative overflow-hidden rounded-xl border border-emerald-100 dark:border-emerald-800 bg-gradient-to-br from-emerald-50 to-green-100 dark:from-emerald-900/20 dark:to-green-900/30 p-4"
                    x-show="show"
                    x-transition:enter="transition ease-out duration-500 delay-250"
                    x-transition:enter-start="opacity-0 transform translate-y-4"
                    x-transition:enter-end="opacity-100 transform translate-y-0"
                >
                    <div class="flex items-center justify-between text-xs text-emerald-800 dark:text-emerald-200">
                        <span>Estimasi Investasi</span>
                        <i class="fas fa-money-bill-wave"></i>
                    </div>
                    <p class="text-sm text-emerald-900 dark:text-emerald-100 mt-3">Mulai dari</p>
                    <p class="text-2xl font-semibold text-emerald-900 dark:text-white">Rp {{ number_format($costRangeMin, 0, ',', '.') }}</p>
                    <p class="text-xs text-emerald-900/80 dark:text-emerald-200/80">
                        hingga Rp {{ number_format($costRangeMax, 0, ',', '.') }}
                    </p>
                    <p class="text-[11px] text-emerald-900/70 dark:text-emerald-100/80 mt-2">
                        Akan dikalkulasi ulang berdasarkan detail zona & luas usaha.
                    </p>
                </div>

                <div 
                    class="relative overflow-hidden rounded-xl border border-purple-100 dark:border-purple-800 bg-gradient-to-br from-purple-50 to-indigo-100 dark:from-purple-900/20 dark:to-indigo-900/20 p-4"
                    x-show="show"
                    x-transition:enter="transition ease-out duration-500 delay-300"
                    x-transition:enter-start="opacity-0 transform translate-y-4"
                    x-transition:enter-end="opacity-100 transform translate-y-0"
                >
                    <div class="flex items-center justify-between text-xs text-purple-800 dark:text-purple-200">
                        <span>Estimasi Durasi</span>
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="mt-3 flex items-baseline gap-2 text-purple-900 dark:text-white">
                        <span class="text-3xl font-bold">
                            {{ $recommendation->estimated_timeline['minimum_days'] ?? '?' }}
                        </span>
                        <span class="text-sm font-medium">hari</span>
                    </div>
                    <p class="text-xs text-purple-800/80 dark:text-purple-200/80">
                        hingga {{ $recommendation->estimated_timeline['maximum_days'] ?? '?' }} hari kerja
                    </p>
                    <p class="text-[11px] text-purple-900/70 dark:text-purple-100/80 mt-2">
                        Estimasi tergantung kelengkapan dokumen & jadwal inspeksi.
                    </p>
                </div>

                <div 
                    class="relative overflow-hidden rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 p-4"
                    x-show="show"
                    x-transition:enter="transition ease-out duration-500 delay-350"
                    x-transition:enter-start="opacity-0 transform translate-y-4"
                    x-transition:enter-end="opacity-100 transform translate-y-0"
                >
                    <div class="flex items-center justify-between text-xs text-gray-500 dark:text-gray-400">
                        <span>Kompleksitas</span>
                        <i class="fas fa-sitemap text-{{ $complexityColor }}-500"></i>
                    </div>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white mt-2">
                        {{ $complexityLevel }}
                    </p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 leading-relaxed">{{ $complexityCopy }}</p>
                    <div class="mt-4">
                        <div class="h-2 bg-gray-100 dark:bg-gray-800 rounded-full overflow-hidden">
                            @php
                                $complexityWidth = $complexityLevel === 'Rendah' ? '33%' : ($complexityLevel === 'Menengah' ? '66%' : '100%');
                            @endphp
                            <div class="h-full bg-{{ $complexityColor }}-500 rounded-full" style="width: {{ $complexityWidth }};"></div>
                        </div>
                        <p class="text-[11px] text-gray-400 dark:text-gray-500 mt-1">Semakin tinggi berarti perlu koordinasi ekstra</p>
                    </div>
                </div>
            </div>

        <!-- Risk Assessment -->
        @if(!empty($recommendation->risk_assessment))
        <div class="mb-4 bg-orange-50 dark:bg-orange-900/20 border border-orange-200 dark:border-orange-800 rounded-lg p-4">
            <div class="flex items-start gap-3">
                <i class="fas fa-exclamation-triangle text-orange-600 dark:text-orange-400 text-lg mt-0.5"></i>
                <div class="flex-1">
                    <h3 class="font-semibold text-orange-900 dark:text-orange-200 mb-1 text-sm">Penilaian Risiko</h3>
                    <div class="text-xs text-orange-800 dark:text-orange-300 space-y-1">
                        @if(isset($recommendation->risk_assessment['level']))
                        <p><strong>Level:</strong> {{ ucfirst($recommendation->risk_assessment['level']) }}</p>
                        @endif
                        @if(isset($recommendation->risk_assessment['notes']))
                        <p>{{ $recommendation->risk_assessment['notes'] }}</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Important Disclaimer -->
        <div class="mb-4 bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 border-l-4 border-blue-600 dark:border-blue-400 rounded-r-lg p-4 shadow-sm">
            <div class="flex items-start gap-3">
                <i class="fas fa-info-circle text-blue-600 dark:text-blue-400 text-lg flex-shrink-0 mt-0.5"></i>
                <div class="flex-1">
                    <h3 class="font-bold text-blue-900 dark:text-blue-100 mb-2 text-sm">📌 Penting untuk Diketahui</h3>
                    <div class="space-y-1.5 text-xs text-gray-700 dark:text-gray-300">
                        <p class="flex items-start gap-2">
                            <i class="fas fa-database text-blue-600 dark:text-blue-400 text-xs mt-0.5 flex-shrink-0"></i>
                            <span><strong>Sumber Data:</strong> Hasil perhitungan otomatis berdasarkan regulasi terkini, database perizinan nasional, dan ratusan studi kasus proyek perizinan yang telah kami tangani.</span>
                        </p>
                        <p class="flex items-start gap-2">
                            <i class="fas fa-calculator text-blue-600 dark:text-blue-400 text-xs mt-0.5 flex-shrink-0"></i>
                            <span><strong>Biaya Aktual:</strong> Estimasi biaya akan dihitung ulang secara detail sesuai kompleksitas pekerjaan, luas area, zonasi, dan kegiatan usaha yang akan Anda ajukan.</span>
                        </p>
                        <p class="flex items-start gap-2">
                            <i class="fas fa-map-marked-alt text-blue-600 dark:text-blue-400 text-xs mt-0.5 flex-shrink-0"></i>
                            <span><strong>Variasi Regional:</strong> Persyaratan dan prosedur dapat berbeda antar daerah sesuai regulasi pemerintah daerah setempat.</span>
                        </p>
                        <p class="flex items-start gap-2">
                            <i class="fas fa-gavel text-blue-600 dark:text-blue-400 text-xs mt-0.5 flex-shrink-0"></i>
                            <span><strong>Kepastian Hukum:</strong> Untuk konsultasi gratis atau analisis biaya aktual, silakan <a href="{{ route('client.applications.create', ['kbli_code' => $kbli->code]) }}" class="text-green-600 dark:text-green-400 underline font-semibold hover:text-green-800 dark:hover:text-green-200 transition">ajukan permohonan</a> dan tim konsultan kami akan merespons dalam 1x24 jam.</span>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Permits by Category -->
        @if(!empty($recommendation->recommended_permits) && count($recommendation->recommended_permits) > 0)
        @php
            $permitsByCategory = collect($recommendation->recommended_permits)
                ->groupBy(function($permit) {
                    return $permit['category'] ?? 'other';
                });
            
            $categoryInfo = [
                'foundational' => [
                    'title' => 'Izin Dasar & Legalitas',
                    'icon' => 'fa-building',
                    'color' => 'blue',
                    'description' => 'Izin fundamental yang menjadi dasar pendirian dan operasional usaha'
                ],
                'environmental' => [
                    'title' => 'Izin Lingkungan',
                    'icon' => 'fa-leaf',
                    'color' => 'green',
                    'description' => 'Izin terkait dampak lingkungan dan pengelolaan lingkungan hidup'
                ],
                'technical' => [
                    'title' => 'Izin Teknis',
                    'icon' => 'fa-tools',
                    'color' => 'orange',
                    'description' => 'Izin teknis terkait bangunan, lahan, dan infrastruktur'
                ],
                'operational' => [
                    'title' => 'Izin Operasional',
                    'icon' => 'fa-cogs',
                    'color' => 'purple',
                    'description' => 'Izin untuk menjalankan kegiatan operasional usaha'
                ],
                'sectoral' => [
                    'title' => 'Izin Khusus Sektoral',
                    'icon' => 'fa-certificate',
                    'color' => 'indigo',
                    'description' => 'Izin spesifik yang diperlukan untuk sektor usaha tertentu'
                ],
                'other' => [
                    'title' => 'Izin Lainnya',
                    'icon' => 'fa-file-alt',
                    'color' => 'gray',
                    'description' => 'Izin tambahan yang mungkin diperlukan'
                ]
            ];
        @endphp

        @foreach($permitsByCategory as $category => $permits)
            @php
                $info = $categoryInfo[$category] ?? $categoryInfo['other'];
                $mandatoryCount = collect($permits)->where('type', 'mandatory')->count();
                $totalCount = count($permits);
            @endphp

        <!-- Category Section -->
        <div 
            x-data="{ open: {{ $loop->first ? 'true' : 'false' }} }" 
            class="bg-white dark:bg-gray-900 rounded-xl shadow-sm border border-gray-200 dark:border-gray-800 mb-4 overflow-hidden"
        >
            <button 
                type="button"
                @click="open = !open"
                class="w-full flex items-start justify-between gap-4 p-4 bg-{{ $info['color'] }}-50 dark:bg-{{ $info['color'] }}-900/20 border-b border-gray-200 dark:border-gray-800 text-left"
            >
                <div class="flex items-start gap-3">
                    <div class="w-12 h-12 bg-{{ $info['color'] }}-100 dark:bg-{{ $info['color'] }}-900 rounded-xl flex items-center justify-center">
                        <i class="fas {{ $info['icon'] }} text-{{ $info['color'] }}-600 dark:text-{{ $info['color'] }}-300 text-lg"></i>
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-gray-900 dark:text-white flex items-center flex-wrap gap-2">
                            {{ $info['title'] }}
                            <span class="px-2 py-0.5 bg-{{ $info['color'] }}-600 text-white text-xs rounded-full">{{ $totalCount }} izin</span>
                        </h2>
                        <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">{{ $info['description'] }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    @if($mandatoryCount > 0)
                        <span class="inline-flex items-center px-2.5 py-1 bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200 text-xs font-semibold rounded">
                            <i class="fas fa-star text-yellow-500 mr-1"></i>
                            {{ $mandatoryCount }} Wajib
                        </span>
                    @endif
                    <i class="fas text-sm text-{{ $info['color'] }}-600 dark:text-{{ $info['color'] }}-300" :class="open ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
                </div>
            </button>

            <div 
                x-show="open" 
                x-transition 
                class="divide-y divide-gray-200 dark:divide-gray-800"
                style="display: none;"
            >
                @foreach($permits as $index => $permit)
                @php
                    $type = $permit['type'] ?? null;
                    $typeColors = [
                        'mandatory' => 'red',
                        'recommended' => 'yellow',
                        'conditional' => 'blue',
                    ];
                    $typeLabels = [
                        'mandatory' => 'WAJIB',
                        'recommended' => 'REKOMENDASI',
                        'conditional' => 'BERSYARAT',
                    ];
                    $typeIcons = [
                        'mandatory' => 'fa-star',
                        'recommended' => 'fa-info-circle',
                        'conditional' => 'fa-question-circle',
                    ];
                    $typeColor = $typeColors[$type] ?? 'gray';
                    $typeLabel = $typeLabels[$type] ?? 'OPSIONAL';
                    $typeIcon = $typeIcons[$type] ?? 'fa-dot-circle';
                @endphp
                <div class="p-4" x-data="{ showRequirements: false }">
                    <div class="flex items-start gap-3">
                        <div class="flex-shrink-0 w-8 h-8 bg-{{ $info['color'] }}-100 dark:bg-{{ $info['color'] }}-900 rounded-full flex items-center justify-center">
                            <span class="text-{{ $info['color'] }}-600 dark:text-{{ $info['color'] }}-300 font-bold text-sm">{{ $loop->iteration }}</span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-2 mb-2">
                                <div>
                                    <h3 class="text-base font-semibold text-gray-900 dark:text-white">
                                        {{ $permit['name'] }}
                                    </h3>
                                    <p class="text-xs text-gray-600 dark:text-gray-400 mt-0.5 leading-relaxed">
                                        {{ $permit['description'] ?? 'Tidak ada deskripsi' }}
                                    </p>
                                </div>
                                <span class="inline-flex items-center px-2.5 py-1 bg-{{ $typeColor }}-100 dark:bg-{{ $typeColor }}-900 text-{{ $typeColor }}-800 dark:text-{{ $typeColor }}-200 text-xs font-semibold rounded-full self-start">
                                    <i class="fas {{ $typeIcon }} mr-1"></i>
                                    {{ $typeLabel }}
                                </span>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs mb-3">
                                <div class="rounded-lg bg-gray-50 dark:bg-gray-800/80 p-3 border border-gray-100 dark:border-gray-700">
                                    <p class="text-gray-500 dark:text-gray-400">Penerbit</p>
                                    <p class="font-semibold text-gray-900 dark:text-white">{{ $permit['issuing_authority'] ?? 'N/A' }}</p>
                                </div>
                                <div class="rounded-lg bg-gray-50 dark:bg-gray-800/80 p-3 border border-gray-100 dark:border-gray-700">
                                    <p class="text-gray-500 dark:text-gray-400">Estimasi Biaya</p>
                                    <p class="font-semibold text-gray-900 dark:text-white">
                                        @if(isset($permit['estimated_cost_range']))
                                            @if(($permit['estimated_cost_range']['min'] ?? 0) == 0 && ($permit['estimated_cost_range']['max'] ?? 0) == 0)
                                                Gratis
                                            @else
                                                Rp {{ number_format($permit['estimated_cost_range']['min'] ?? 0, 0, ',', '.') }}
                                                @if(($permit['estimated_cost_range']['max'] ?? 0) > ($permit['estimated_cost_range']['min'] ?? 0))
                                                    - Rp {{ number_format($permit['estimated_cost_range']['max'] ?? 0, 0, ',', '.') }}
                                                @endif
                                            @endif
                                        @else
                                            N/A
                                        @endif
                                    </p>
                                </div>
                                <div class="rounded-lg bg-gray-50 dark:bg-gray-800/80 p-3 border border-gray-100 dark:border-gray-700">
                                    <p class="text-gray-500 dark:text-gray-400">Durasi</p>
                                    <p class="font-semibold text-gray-900 dark:text-white">{{ $permit['estimated_processing_time'] ?? 'N/A' }}</p>
                                </div>
                            </div>

                            <div class="flex flex-wrap gap-2 text-[11px] uppercase tracking-wide">
                                @if(!empty($permit['digital_requirements']))
                                    <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-200">
                                        <i class="fas fa-laptop-code"></i> Digital Ready
                                    </span>
                                @endif
                                @if(!empty($permit['dependencies']))
                                    <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-200">
                                        <i class="fas fa-link"></i> Ada Ketergantungan
                                    </span>
                                @endif
                                @if(!empty($permit['triggers_next']))
                                    <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-200">
                                        <i class="fas fa-unlock"></i> Membuka Izin Lain
                                    </span>
                                @endif
                            </div>

                            @if(!empty($permit['digital_requirements']))
                            <div class="mt-3">
                                <p class="text-xs font-semibold text-gray-600 dark:text-gray-300 mb-1">Kebutuhan Digital:</p>
                                <div class="flex flex-wrap gap-1.5">
                                    @foreach($permit['digital_requirements'] as $req)
                                    <span class="inline-flex items-center px-2 py-0.5 bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 text-xs rounded">
                                        {{ $req }}
                                    </span>
                                    @endforeach
                                </div>
                            </div>
                            @endif

                            @if(!empty($permit['dependencies']))
                            <div class="mt-3">
                                <p class="text-xs font-semibold text-gray-600 dark:text-gray-300 mb-1">Harus selesai terlebih dahulu:</p>
                                <div class="flex flex-wrap gap-1.5">
                                    @foreach($permit['dependencies'] as $dep)
                                    <span class="inline-flex items-center px-2 py-0.5 bg-yellow-50 dark:bg-yellow-900/20 text-yellow-800 dark:text-yellow-200 text-xs rounded">
                                        {{ $dep }}
                                    </span>
                                    @endforeach
                                </div>
                            </div>
                            @endif

                            @if(!empty($permit['triggers_next']))
                            <div class="mt-3">
                                <p class="text-xs font-semibold text-gray-600 dark:text-gray-300 mb-1">Setelah selesai Anda bisa mengajukan:</p>
                                <div class="flex flex-wrap gap-1.5">
                                    @foreach($permit['triggers_next'] as $next)
                                    <span class="inline-flex items-center px-2 py-0.5 bg-green-50 dark:bg-green-900/20 text-green-800 dark:text-green-200 text-xs rounded">
                                        {{ $next }}
                                    </span>
                                    @endforeach
                                </div>
                            </div>
                            @endif

                            @if(!empty($permit['timeline_notes']))
                            <div class="mt-3">
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    <i class="fas fa-stream mr-1"></i>
                                    {{ $permit['timeline_notes'] }}
                                </p>
                            </div>
                            @endif

                            @if(!empty($permit['requirements']))
                            <div class="mt-4 border-t border-dashed border-gray-200 dark:border-gray-700 pt-3">
                                <button 
                                    type="button"
                                    @click="showRequirements = !showRequirements"
                                    class="text-xs text-blue-600 dark:text-blue-400 font-semibold inline-flex items-center gap-2"
                                >
                                    <i class="fas" :class="showRequirements ? 'fa-minus-circle' : 'fa-plus-circle'"></i>
                                    Persyaratan ({{ count($permit['requirements']) }})
                                </button>
                                <ul 
                                    class="mt-2 ml-4 space-y-1 text-xs text-gray-700 dark:text-gray-300 list-disc list-outside"
                                    x-show="showRequirements"
                                    x-transition
                                    style="display: none;"
                                >
                                    @foreach($permit['requirements'] as $req)
                                    <li>{{ $req }}</li>
                                    @endforeach
                                </ul>
                            </div>
                            @endif

                            @if(!empty($permit['legal_basis']) || !empty($permit['renewal_period']))
                            <div class="mt-3 grid grid-cols-1 sm:grid-cols-2 gap-2 text-xs">
                                @if(!empty($permit['legal_basis']))
                                <p class="flex items-start gap-2 text-gray-500 dark:text-gray-400">
                                    <i class="fas fa-gavel mt-0.5"></i>
                                    <span><strong>Dasar:</strong> {{ $permit['legal_basis'] }}</span>
                                </p>
                                @endif
                                @if(!empty($permit['renewal_period']))
                                <p class="flex items-start gap-2 text-gray-500 dark:text-gray-400">
                                    <i class="fas fa-redo mt-0.5"></i>
                                    <span><strong>Perpanjangan:</strong> {{ $permit['renewal_period'] }}</span>
                                </p>
                                @endif
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endforeach
        @else
            <!-- No permits found -->
            <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4">
                <p class="text-yellow-800 dark:text-yellow-200 text-sm">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    Belum ada rekomendasi izin yang tersedia untuk KBLI ini.
                </p>
            </div>
        @endif

        <!-- Required Documents -->
        @if(!empty($recommendation->required_documents))
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 mb-4">
            <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-base font-bold text-gray-900 dark:text-white flex items-center gap-2">
                    <i class="fas fa-folder-open text-blue-600 text-lg"></i>
                    Dokumen yang Dibutuhkan
                </h2>
            </div>
            <div class="p-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    @foreach($recommendation->required_documents as $doc)
                    <div class="flex items-start p-3 bg-gray-50 dark:bg-gray-700 rounded-lg border border-gray-200 dark:border-gray-600">
                        <i class="fas fa-file-alt text-blue-600 dark:text-blue-400 text-sm mt-0.5 mr-2 flex-shrink-0"></i>
                        <div class="flex-1 min-w-0">
                            <h4 class="font-semibold text-gray-900 dark:text-white text-xs mb-0.5">
                                {{ is_array($doc) ? ($doc['name'] ?? 'Dokumen') : $doc }}
                            </h4>
                            @if(is_array($doc) && isset($doc['notes']))
                            <p class="text-xs text-gray-600 dark:text-gray-400 mb-1 leading-relaxed">{{ $doc['notes'] }}</p>
                            @endif
                            @if(is_array($doc))
                            <div class="flex items-center gap-1.5 text-xs">
                                @if(isset($doc['type']))
                                <span class="px-1.5 py-0.5 bg-blue-100 dark:bg-blue-900 text-blue-700 dark:text-blue-300 rounded text-xs">
                                    {{ ucfirst($doc['type']) }}
                                </span>
                                @endif
                                @if(isset($doc['format']))
                                <span class="px-1.5 py-0.5 bg-gray-200 dark:bg-gray-600 text-gray-700 dark:text-gray-300 rounded text-xs">
                                    {{ strtoupper($doc['format']) }}
                                </span>
                                @endif
                            </div>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        <!-- Timeline -->
        @if(!empty($recommendation->estimated_timeline))
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 mb-4">
            <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-base font-bold text-gray-900 dark:text-white flex items-center gap-2">
                    <i class="fas fa-calendar-alt text-purple-600 text-lg"></i>
                    Timeline Proses
                </h2>
            </div>
            <div class="p-4">
                <!-- Timeline Summary -->
                <div class="bg-purple-50 dark:bg-purple-900/20 rounded-lg p-3 mb-3">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs text-gray-600 dark:text-gray-400 mb-0.5">Estimasi Total</p>
                            <p class="text-lg font-bold text-purple-900 dark:text-purple-100">
                                {{ $recommendation->estimated_timeline['minimum_days'] ?? 'N/A' }} - 
                                {{ $recommendation->estimated_timeline['maximum_days'] ?? 'N/A' }} Hari
                            </p>
                        </div>
                        <i class="fas fa-clock text-3xl text-purple-400"></i>
                    </div>
                </div>

                <!-- Critical Path -->
                @if(!empty($recommendation->estimated_timeline['critical_path']))
                <div>
                    <h3 class="font-semibold text-gray-900 dark:text-white mb-2 text-sm">Jalur Kritis:</h3>
                    <div class="space-y-2">
                        @foreach($recommendation->estimated_timeline['critical_path'] as $index => $step)
                        <div class="flex items-start gap-3">
                            <div class="flex-shrink-0 w-6 h-6 bg-purple-100 dark:bg-purple-900 rounded-full flex items-center justify-center">
                                <span class="text-purple-600 dark:text-purple-400 font-bold text-xs">{{ $index + 1 }}</span>
                            </div>
                            <p class="flex-1 text-xs text-gray-900 dark:text-white">{{ $step }}</p>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
        </div>
        @endif

        <!-- Action Buttons -->
        <div class="mt-10">
            <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-2xl shadow-sm p-6 flex flex-col lg:flex-row items-start lg:items-center gap-6">
                <div class="flex-1">
                    <p class="text-xs uppercase tracking-[0.3em] text-gray-500 dark:text-gray-400">Langkah Berikutnya</p>
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white mt-2">Butuh pendampingan penuh sampai izin terbit?</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-2 leading-relaxed">
                        Konsultan BizMark siap membantu penyusunan dokumen, koordinasi instansi, dan monitoring progres melalui dashboard klien.
                    </p>
                </div>
                <div class="flex flex-col sm:flex-row gap-3 w-full lg:w-auto">
                    <button 
                        onclick="window.print()" 
                        class="flex-1 sm:flex-none px-5 py-3 bg-gray-100 dark:bg-gray-800 text-gray-800 dark:text-gray-200 text-sm font-semibold rounded-xl hover:bg-gray-200 dark:hover:bg-gray-700 transition inline-flex items-center justify-center gap-2"
                    >
                        <i class="fas fa-file-download"></i>
                        Download Ringkasan
                    </button>
                    
                    <a 
                        href="{{ route('client.applications.create', ['kbli_code' => $kbli->code]) }}" 
                        class="flex-1 sm:flex-none px-6 py-3 bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 text-white font-semibold text-sm rounded-xl transition-all shadow-lg hover:shadow-xl inline-flex items-center justify-center gap-2"
                    >
                        <i class="fas fa-paper-plane"></i>
                        Ajukan Permohonan / Konsultasi
                    </a>
                </div>
            </div>
        </div>

        <!-- Footer Note -->
        <div class="mt-6 text-center text-xs text-gray-500 dark:text-gray-400">
            <i class="fas fa-shield-alt mr-1"></i>
            Data perhitungan berdasarkan regulasi terkini dan pengalaman proyek perizinan yang telah kami tangani. Untuk kepastian hukum, konsultasikan dengan tim ahli kami.
        </div>
        @endif
    </div>
</div>

@endsection
