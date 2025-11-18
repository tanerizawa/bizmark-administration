@extends('client.layouts.app')

@section('title', 'Rekomendasi Perizinan - BizMark')

@section('content')
<div class="space-y-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Back Button -->
        <a href="{{ route('client.services.index') }}" class="inline-flex items-center text-[#0a66c2] hover:text-[#004182] transition-colors">
            <i class="fas fa-arrow-left mr-2"></i>
            Kembali ke Katalog
        </a>

        @if(session('error'))
        <div class="mt-6 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-2xl p-4">
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
        <!-- Professional Loading Animation -->
        <div class="mt-6 bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="p-12 text-center">
                <!-- Simple Professional Spinner -->
                <div class="relative inline-block mb-6">
                    <div class="w-20 h-20 border-4 border-[#0a66c2]/20 border-t-[#0a66c2] rounded-full animate-spin"></div>
                </div>

                <!-- Title -->
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-3">
                    Menganalisis Kebutuhan Perizinan
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
                                    :class="index <= currentStep ? 'bg-[#0a66c2] text-white scale-110' : 'bg-gray-200 dark:bg-gray-700 text-gray-500'"
                                >
                                    <i class="fas fa-check text-xs" x-show="index < currentStep"></i>
                                    <span x-show="index >= currentStep" x-text="index + 1"></span>
                                </div>
                                <div 
                                    x-show="index < steps.length - 1" 
                                    class="w-16 h-1 mx-2 transition-all duration-500"
                                    :class="index < currentStep ? 'bg-[#0a66c2]' : 'bg-gray-200 dark:bg-gray-700'"
                                ></div>
                            </div>
                        </template>
                    </div>
                    <p class="text-sm text-gray-600 dark:text-gray-400 font-medium transition-all duration-500" x-text="steps[currentStep]"></p>
                </div>

                <!-- Info Text -->
                <div class="bg-[#0a66c2]/5 rounded-xl p-4 max-w-lg mx-auto">
                    <p class="text-sm text-gray-700 dark:text-gray-300">
                        Sistem kami menganalisis regulasi terkini untuk memberikan rekomendasi terbaik
                    </p>
                </div>

                <!-- Animated Progress Bar -->
                <div class="mt-6 max-w-md mx-auto">
                    <div class="h-1.5 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
                        <div class="h-full bg-[#0a66c2] animate-progress-bar"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Animation CSS -->
        <style>
            @keyframes progress-bar {
                0% { transform: translateX(-100%) scaleX(0.3); }
                50% { transform: translateX(50%) scaleX(0.6); }
                100% { transform: translateX(200%) scaleX(0.3); }
            }
            .animate-progress-bar {
                animation: progress-bar 2s ease-in-out infinite;
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
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 transform translate-y-2"
                x-transition:enter-end="opacity-100 transform translate-y-0"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="bg-white dark:bg-gray-800 border-l-4 border-[#0a66c2] rounded-xl shadow-sm p-4 mb-6"
            >
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-[#0a66c2]/10 rounded-full flex items-center justify-center">
                            <i class="fas fa-check-circle text-lg text-[#0a66c2]"></i>
                        </div>
                        <div>
                            <h3 class="text-base font-semibold text-gray-900 dark:text-white">Rekomendasi Berhasil Dibuat</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                Sistem menganalisis {{ count($recommendation->recommended_permits ?? []) }} jenis izin berdasarkan regulasi terkini
                            </p>
                        </div>
                    </div>
                    <button 
                        @click="showSuccess = false"
                        class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors"
                    >
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
            </div>

            <!-- KBLI Hero -->
            <div 
                class="relative overflow-hidden rounded-2xl bg-[#0a66c2] text-white shadow-sm mb-6"
                x-show="show"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 transform -translate-y-4"
                x-transition:enter-end="opacity-100 transform translate-y-0"
            >
                <div class="relative flex flex-col lg:flex-row gap-8 p-6">
                    <div class="flex-1">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-white/10 border border-white/30 uppercase tracking-widest">
                            KBLI {{ $kbli->code }}
                        </span>
                        <h1 class="text-2xl lg:text-3xl font-bold mt-4 leading-snug">
                            {{ $kbli->description }}
                        </h1>
                        <p class="text-sm text-white/80 mt-2">Sektor {{ $kbli->sector }} • Rekomendasi berbasis regulasi terbaru</p>
                        @if($kbli->notes)
                        <div class="mt-4 bg-white/10 border border-white/20 rounded-xl p-4 text-sm text-white/90 leading-relaxed">
                            <p>{{ $kbli->notes }}</p>
                        </div>
                        @endif
                        <div class="mt-6 space-y-3">
                            <div>
                                <p class="text-[11px] uppercase tracking-[0.3em] text-white/70">Akurasi Rekomendasi</p>
                                <div class="mt-2 h-2 bg-white/20 rounded-full overflow-hidden">
                                    <div class="h-full bg-white rounded-full" style="width: {{ $confidencePercent }}%;"></div>
                                </div>
                                <div class="flex items-center justify-between text-xs text-white/80 mt-1">
                                    <span>{{ $confidencePercent }}% yakin</span>
                                    <span>Pembaruan otomatis</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="w-full lg:w-72 space-y-4">
                        <div class="bg-white/10 border border-white/20 rounded-xl p-4 backdrop-blur">
                            <p class="text-xs uppercase tracking-widest text-white/70">Konteks Bisnis</p>
                            <div class="mt-3 space-y-3">
                                <div class="rounded-lg bg-white/5 p-3">
                                    <p class="text-[11px] uppercase tracking-wide text-white/60">Skala Usaha</p>
                                    <p class="text-base font-semibold">{{ $scaleInfo['label'] }}</p>
                                    <p class="text-xs text-white/70 leading-relaxed">{{ $scaleInfo['summary'] }}</p>
                                </div>
                                <div class="rounded-lg bg-white/5 p-3">
                                    <p class="text-[11px] uppercase tracking-wide text-white/60">Lokasi Operasional</p>
                                    <p class="text-base font-semibold">{{ $locationInfo['label'] }}</p>
                                    <p class="text-xs text-white/70 leading-relaxed">{{ $locationInfo['summary'] }}</p>
                                </div>
                            </div>
                            <div class="mt-4 flex flex-wrap gap-2">
                                @if($businessScale)
                                    <span class="inline-flex items-center px-3 py-1 text-xs font-semibold rounded-full bg-white/20 border border-white/30">
                                        <i class="fas fa-chart-line mr-1"></i>
                                        Skala: {{ ucfirst($businessScale) }}
                                    </span>
                                @endif
                                @if($locationType)
                                    <span class="inline-flex items-center px-3 py-1 text-xs font-semibold rounded-full bg-white/20 border border-white/30">
                                        <i class="fas fa-map-marker-alt mr-1"></i>
                                        Lokasi: {{ str_replace('_', ' ', ucfirst($locationType)) }}
                                    </span>
                                @endif
                                @if(!$businessScale && !$locationType)
                                    <span class="inline-flex items-center px-3 py-1 text-xs font-semibold rounded-full bg-white/10 border border-white/20">
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
                            <p class="text-white/70 mt-1">
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
                        <i class="fas fa-layer-group text-[#0a66c2]"></i>
                    </div>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2">
                        {{ $mandatoryCount }}
                    </p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Izin wajib dari total {{ $totalPermits }} rekomendasi</p>
                    <div class="mt-4 flex flex-wrap gap-2 text-[11px] uppercase">
                        <span class="px-2 py-1 rounded-full bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-300 font-semibold">Segera Dipenuhi</span>
                        <span class="px-2 py-1 rounded-full bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300 font-semibold">Pantau Status</span>
                    </div>
                </div>

                <div 
                    class="relative overflow-hidden rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-4"
                    x-show="show"
                    x-transition:enter="transition ease-out duration-300 delay-100"
                    x-transition:enter-start="opacity-0 transform translate-y-4"
                    x-transition:enter-end="opacity-100 transform translate-y-0"
                >
                    <div class="flex items-center justify-between text-xs text-gray-600 dark:text-gray-400">
                        <span>Biaya Resmi Pemerintah</span>
                        <i class="fas fa-landmark"></i>
                    </div>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-3">Mulai dari</p>
                    <p class="text-2xl font-semibold text-gray-900 dark:text-white">
                        @if($costRangeMin == 0 && $costRangeMax == 0)
                            <span class="text-green-600 dark:text-green-400">Gratis</span>
                        @else
                            Rp {{ number_format($costRangeMin, 0, ',', '.') }}
                        @endif
                    </p>
                    @if($costRangeMin > 0 || $costRangeMax > 0)
                    <p class="text-xs text-gray-600 dark:text-gray-400">
                        hingga Rp {{ number_format($costRangeMax, 0, ',', '.') }}
                    </p>
                    @endif
                    <div class="mt-2 pt-2 border-t border-gray-200 dark:border-gray-700">
                        <p class="text-[11px] text-gray-500 dark:text-gray-400">
                            <i class="fas fa-info-circle mr-1"></i>
                            @if($costRangeMin == 0 && $costRangeMax == 0)
                                Biaya pemerintah gratis. <strong>Biaya jasa konsultan BizMark</strong> akan ditampilkan terpisah (minimal Rp 2 juta).
                            @else
                                Biaya ini adalah PNBP/retribusi resmi ke pemerintah. <strong>Biaya jasa konsultan BizMark</strong> dihitung terpisah berdasarkan kompleksitas.
                            @endif
                        </p>
                    </div>
                </div>

                <div 
                    class="relative overflow-hidden rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-4"
                    x-show="show"
                    x-transition:enter="transition ease-out duration-300 delay-150"
                    x-transition:enter-start="opacity-0 transform translate-y-4"
                    x-transition:enter-end="opacity-100 transform translate-y-0"
                >
                    <div class="flex items-center justify-between text-xs text-gray-600 dark:text-gray-400">
                        <span>Estimasi Durasi</span>
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="mt-3 flex items-baseline gap-2 text-gray-900 dark:text-white">
                        <span class="text-3xl font-bold">
                            {{ $recommendation->estimated_timeline['minimum_days'] ?? '?' }}
                        </span>
                        <span class="text-sm font-medium">hari</span>
                    </div>
                    <p class="text-xs text-gray-600 dark:text-gray-400">
                        hingga {{ $recommendation->estimated_timeline['maximum_days'] ?? '?' }} hari kerja
                    </p>
                    <p class="text-[11px] text-gray-500 dark:text-gray-400 mt-2">
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
        <div class="mb-4 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-xl p-4">
            <div class="flex items-start gap-3">
                <i class="fas fa-exclamation-triangle text-amber-600 dark:text-amber-400 text-lg mt-0.5"></i>
                <div class="flex-1">
                    <h3 class="font-semibold text-gray-900 dark:text-white mb-1 text-sm">Penilaian Risiko</h3>
                    <div class="text-xs text-gray-700 dark:text-gray-300 space-y-1">
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
        <div class="mb-4 bg-[#0a66c2]/5 dark:bg-[#0a66c2]/10 border-l-4 border-[#0a66c2] rounded-r-xl p-4 shadow-sm">
            <div class="flex items-start gap-3">
                <i class="fas fa-info-circle text-[#0a66c2] text-lg flex-shrink-0 mt-0.5"></i>
                <div class="flex-1">
                    <h3 class="font-bold text-gray-900 dark:text-white mb-2 text-sm">Penting untuk Diketahui</h3>
                    <div class="space-y-1.5 text-xs text-gray-700 dark:text-gray-300">
                        <p class="flex items-start gap-2">
                            <i class="fas fa-database text-[#0a66c2] text-xs mt-0.5 flex-shrink-0"></i>
                            <span><strong>Sumber Data:</strong> Hasil perhitungan otomatis berdasarkan regulasi terkini, database perizinan nasional, dan ratusan studi kasus proyek perizinan yang telah kami tangani.</span>
                        </p>
                        <p class="flex items-start gap-2">
                            <i class="fas fa-calculator text-[#0a66c2] text-xs mt-0.5 flex-shrink-0"></i>
                            <span><strong>Biaya Aktual:</strong> Estimasi biaya akan dihitung ulang secara detail sesuai kompleksitas pekerjaan, luas area, zonasi, dan kegiatan usaha yang akan Anda ajukan.</span>
                        </p>
                        <p class="flex items-start gap-2">
                            <i class="fas fa-map-marked-alt text-[#0a66c2] text-xs mt-0.5 flex-shrink-0"></i>
                            <span><strong>Variasi Regional:</strong> Persyaratan dan prosedur dapat berbeda antar daerah sesuai regulasi pemerintah daerah setempat.</span>
                        </p>
                        <p class="flex items-start gap-2">
                            <i class="fas fa-gavel text-[#0a66c2] text-xs mt-0.5 flex-shrink-0"></i>
                            <span><strong>Kepastian Hukum:</strong> Untuk konsultasi gratis atau analisis biaya aktual, silakan <a href="{{ route('client.applications.create', ['kbli_code' => $kbli->code]) }}" class="text-[#0a66c2] underline font-semibold hover:text-[#004182] transition">ajukan permohonan</a> dan tim konsultan kami akan merespons dalam 1x24 jam.</span>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Enhanced Cost Breakdown (if context data provided) -->
        @if(isset($formattedCosts) && $formattedCosts)
        <div class="space-y-6 mb-8">
            <div class="bg-gradient-to-br from-[#0a66c2] to-[#004182] rounded-2xl shadow-lg p-6 text-white">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
                        <i class="fas fa-calculator text-2xl"></i>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold">Rincian Biaya Lengkap</h2>
                        <p class="text-sm text-white/80">Berdasarkan data konteks proyek Anda</p>
                    </div>
                </div>

                <div class="grid md:grid-cols-3 gap-4">
                    @foreach($formattedCosts['sections'] as $section)
                    <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4">
                        <div class="flex items-center gap-2 mb-3">
                            <i class="fas {{ $section['icon'] }} text-lg"></i>
                            <h3 class="font-semibold">{{ $section['title'] }}</h3>
                        </div>
                        <p class="text-xs text-white/70 mb-2">{{ $section['subtitle'] }}</p>
                        <p class="text-2xl font-bold">
                            Rp {{ number_format($section['amount']['min'], 0, ',', '.') }}
                        </p>
                        @if($section['amount']['max'] > $section['amount']['min'])
                        <p class="text-sm text-white/80">
                            - Rp {{ number_format($section['amount']['max'], 0, ',', '.') }}
                        </p>
                        @endif
                    </div>
                    @endforeach
                </div>

                <div class="mt-6 pt-6 border-t border-white/20">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-semibold">Total Estimasi Investasi</h3>
                            <p class="text-sm text-white/80">Biaya pemerintah + Jasa konsultan + Persiapan dokumen</p>
                        </div>
                        <div class="text-right">
                            <p class="text-3xl font-bold">
                                Rp {{ number_format($formattedCosts['total']['min'], 0, ',', '.') }}
                            </p>
                            @if($formattedCosts['total']['max'] > $formattedCosts['total']['min'])
                            <p class="text-sm text-white/90">
                                hingga Rp {{ number_format($formattedCosts['total']['max'], 0, ',', '.') }}
                            </p>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Complexity Factors -->
                @if(isset($costBreakdown['factors']))
                <div class="mt-4 pt-4 border-t border-white/20">
                    <p class="text-sm font-semibold mb-2">Faktor Perhitungan:</p>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                        <div class="text-center bg-white/10 rounded-lg p-2">
                            <p class="text-xs text-white/70">Kompleksitas</p>
                            <p class="text-lg font-bold">{{ number_format($costBreakdown['factors']['complexity'], 1) }}x</p>
                        </div>
                        <div class="text-center bg-white/10 rounded-lg p-2">
                            <p class="text-xs text-white/70">Lokasi</p>
                            <p class="text-lg font-bold">{{ number_format($costBreakdown['factors']['location'], 1) }}x</p>
                        </div>
                        <div class="text-center bg-white/10 rounded-lg p-2">
                            <p class="text-xs text-white/70">Lingkungan</p>
                            <p class="text-lg font-bold">{{ number_format($costBreakdown['factors']['environmental'], 1) }}x</p>
                        </div>
                        <div class="text-center bg-white/10 rounded-lg p-2">
                            <p class="text-xs text-white/70">Urgensi</p>
                            <p class="text-lg font-bold">{{ number_format($costBreakdown['factors']['urgency'], 1) }}x</p>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Notes -->
                <div class="mt-4 bg-white/10 rounded-lg p-3">
                    <p class="text-xs font-semibold mb-2"><i class="fas fa-info-circle mr-1"></i> Catatan Penting:</p>
                    <ul class="text-xs space-y-1 text-white/90">
                        @foreach($formattedCosts['notes'] as $note)
                        <li>• {{ $note }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
        @endif

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
            class="bg-white dark:bg-gray-900 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-800 mb-4 overflow-hidden"
        >
            <button 
                type="button"
                @click="open = !open"
                class="w-full flex items-start justify-between gap-4 p-4 bg-gray-50 dark:bg-gray-800/50 border-b border-gray-200 dark:border-gray-700 text-left hover:bg-gray-100 dark:hover:bg-gray-800 transition"
            >
                <div class="flex items-start gap-3">
                    <div class="w-12 h-12 bg-[#0a66c2]/10 rounded-xl flex items-center justify-center">
                        <i class="fas {{ $info['icon'] }} text-[#0a66c2] text-lg"></i>
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-gray-900 dark:text-white flex items-center flex-wrap gap-2">
                            {{ $info['title'] }}
                            <span class="px-2 py-0.5 bg-gray-600 dark:bg-gray-700 text-white text-xs rounded-full">{{ $totalCount }} izin</span>
                        </h2>
                        <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">{{ $info['description'] }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    @if($mandatoryCount > 0)
                        <span class="inline-flex items-center px-2.5 py-1 bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200 text-xs font-semibold rounded-lg">
                            <i class="fas fa-exclamation-circle mr-1"></i>
                            {{ $mandatoryCount }} Wajib
                        </span>
                    @endif
                    <i class="fas text-sm text-gray-600 dark:text-gray-400" :class="open ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
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
                        'mandatory' => 'fa-exclamation-circle',
                        'recommended' => 'fa-info-circle',
                        'conditional' => 'fa-question-circle',
                    ];
                    $typeColor = $typeColors[$type] ?? 'gray';
                    $typeLabel = $typeLabels[$type] ?? 'OPSIONAL';
                    $typeIcon = $typeIcons[$type] ?? 'fa-circle';
                @endphp
                <div class="p-4" x-data="{ showRequirements: false }">
                    <div class="flex items-start gap-3">
                        <div class="flex-shrink-0 w-8 h-8 bg-gray-100 dark:bg-gray-800 rounded-full flex items-center justify-center">
                            <span class="text-gray-700 dark:text-gray-300 font-bold text-sm">{{ $loop->iteration }}</span>
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
                                @php
                                    $badgeClass = match($type) {
                                        'mandatory' => 'bg-red-100 dark:bg-red-900/40 text-red-800 dark:text-red-200',
                                        'recommended' => 'bg-[#0a66c2]/10 text-[#0a66c2]',
                                        'conditional' => 'bg-amber-100 dark:bg-amber-900/40 text-amber-800 dark:text-amber-200',
                                        default => 'bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300'
                                    };
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-1 {{ $badgeClass }} text-xs font-semibold rounded-full self-start">
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
                                    <p class="text-gray-500 dark:text-gray-400 mb-1">
                                        <i class="fas fa-landmark text-xs mr-1"></i>
                                        Biaya Pemerintah
                                    </p>
                                    <p class="font-semibold text-gray-900 dark:text-white">
                                        @if(isset($permit['estimated_cost_range']))
                                            @if(($permit['estimated_cost_range']['min'] ?? 0) == 0 && ($permit['estimated_cost_range']['max'] ?? 0) == 0)
                                                <span class="text-green-600 dark:text-green-400">Gratis</span>
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
                                    @if(isset($permit['consultant_fee_range']))
                                    <p class="text-[10px] text-[#0a66c2] dark:text-blue-400 mt-1">
                                        <i class="fas fa-user-tie text-xs"></i>
                                        +Konsultan: Rp {{ number_format($permit['consultant_fee_range']['min'] ?? 0, 0, ',', '.') }}
                                    </p>
                                    @endif
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
                                    <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-300">
                                        <i class="fas fa-link"></i> Ada Ketergantungan
                                    </span>
                                @endif
                                @if(!empty($permit['triggers_next']))
                                    <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-300">
                                        <i class="fas fa-unlock"></i> Membuka Izin Lain
                                    </span>
                                @endif
                            </div>

                            @if(!empty($permit['digital_requirements']))
                            <div class="mt-3">
                                <p class="text-xs font-semibold text-gray-600 dark:text-gray-300 mb-1">Kebutuhan Digital:</p>
                                <div class="flex flex-wrap gap-1.5">
                                    @foreach($permit['digital_requirements'] as $req)
                                    <span class="inline-flex items-center px-2 py-0.5 bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 text-xs rounded-lg">
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
                                    <span class="inline-flex items-center px-2 py-0.5 bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 text-xs rounded-lg">
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
                                    <span class="inline-flex items-center px-2 py-0.5 bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 text-xs rounded-lg">
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
                                    class="text-xs text-[#0a66c2] dark:text-[#0a66c2] font-semibold inline-flex items-center gap-2 hover:text-[#004182] transition"
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
            <div class="bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-4">
                <p class="text-gray-700 dark:text-gray-300 text-sm">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    Belum ada rekomendasi izin yang tersedia untuk KBLI ini.
                </p>
            </div>
        @endif

        <!-- Required Documents -->
        @if(!empty($recommendation->required_documents))
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 mb-4">
            <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-base font-bold text-gray-900 dark:text-white flex items-center gap-2">
                    <i class="fas fa-folder-open text-[#0a66c2] text-lg"></i>
                    Dokumen yang Dibutuhkan
                </h2>
            </div>
            <div class="p-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    @foreach($recommendation->required_documents as $doc)
                    <div class="flex items-start p-3 bg-gray-50 dark:bg-gray-700 rounded-xl border border-gray-200 dark:border-gray-600">
                        <i class="fas fa-file-alt text-[#0a66c2] dark:text-[#0a66c2] text-sm mt-0.5 mr-2 flex-shrink-0"></i>
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
                                <span class="px-1.5 py-0.5 bg-[#0a66c2]/10 text-[#0a66c2] rounded-lg text-xs font-medium">
                                    {{ ucfirst($doc['type']) }}
                                </span>
                                @endif
                                @if(isset($doc['format']))
                                <span class="px-1.5 py-0.5 bg-gray-200 dark:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-lg text-xs font-medium">
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
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 mb-4">
            <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-base font-bold text-gray-900 dark:text-white flex items-center gap-2">
                    <i class="fas fa-calendar-alt text-[#0a66c2] text-lg"></i>
                    Timeline Proses
                </h2>
            </div>
            <div class="p-4">
                <!-- Timeline Summary -->
                <div class="bg-[#0a66c2]/5 rounded-xl p-3 mb-3">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs text-gray-600 dark:text-gray-400 mb-0.5">Estimasi Total</p>
                            <p class="text-lg font-bold text-gray-900 dark:text-white">
                                {{ $recommendation->estimated_timeline['minimum_days'] ?? 'N/A' }} - 
                                {{ $recommendation->estimated_timeline['maximum_days'] ?? 'N/A' }} Hari
                            </p>
                        </div>
                        <i class="fas fa-clock text-3xl text-[#0a66c2]"></i>
                    </div>
                </div>

                <!-- Critical Path -->
                @if(!empty($recommendation->estimated_timeline['critical_path']))
                <div>
                    <h3 class="font-semibold text-gray-900 dark:text-white mb-2 text-sm">Jalur Kritis:</h3>
                    <div class="space-y-2">
                        @foreach($recommendation->estimated_timeline['critical_path'] as $index => $step)
                        <div class="flex items-start gap-3">
                            <div class="flex-shrink-0 w-6 h-6 bg-[#0a66c2]/10 rounded-full flex items-center justify-center">
                                <span class="text-[#0a66c2] font-bold text-xs">{{ $index + 1 }}</span>
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
                    <a 
                        href="{{ route('client.services.downloadSummary', $kbli->code) }}" 
                        class="flex-1 sm:flex-none px-5 py-3 bg-gray-100 dark:bg-gray-800 text-gray-800 dark:text-gray-200 text-sm font-semibold rounded-xl hover:bg-gray-200 dark:hover:bg-gray-700 transition inline-flex items-center justify-center gap-2"
                    >
                        <i class="fas fa-file-download"></i>
                        Download Ringkasan PDF
                    </a>
                    <a 
                        href="{{ route('client.applications.create', ['kbli_code' => $kbli->code]) }}" 
                        class="flex-1 sm:flex-none px-6 py-3 bg-[#0a66c2] hover:bg-[#004182] text-white font-semibold text-sm rounded-xl transition-all shadow-sm hover:shadow-md inline-flex items-center justify-center gap-2"
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
