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

            <!-- KBLI Header -->
            <div 
                class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4 mb-4"
                x-show="show"
                x-transition:enter="transition ease-out duration-500"
                x-transition:enter-start="opacity-0 transform -translate-y-4"
                x-transition:enter-end="opacity-100 transform translate-y-0"
            >
            <div class="flex items-start justify-between gap-4">
                <div class="flex-1">
                    <span class="inline-block px-2.5 py-1 bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 text-xs font-mono font-semibold rounded mb-2">
                        {{ $kbli->code }}
                    </span>
                    <h1 class="text-xl font-bold text-gray-900 dark:text-white mb-1">
                        {{ $kbli->description }}
                    </h1>
                    <div class="text-xs text-gray-600 dark:text-gray-400">
                        Sektor {{ $kbli->sector }} • Kode: {{ $kbli->code }}
                    </div>
                    @if($kbli->notes)
                    <div class="mt-3 p-3 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg">
                        <div class="flex items-start gap-2">
                            <i class="fas fa-info-circle text-blue-600 dark:text-blue-400 text-xs mt-0.5 flex-shrink-0"></i>
                            <p class="text-xs text-gray-700 dark:text-gray-300 leading-relaxed">
                                {{ $kbli->notes }}
                            </p>
                        </div>
                    </div>
                    @endif
                </div>
                <div class="text-right flex-shrink-0">
                    <div class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium
                        {{ $recommendation->confidence_score >= 0.8 ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200' }}">
                        <i class="fas fa-check-circle mr-1"></i>
                        {{ number_format($recommendation->confidence_score * 100, 0) }}%
                    </div>
                    <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                        Akurasi Data
                    </div>
                </div>
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
            <div 
                class="bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-800/20 rounded-lg p-4 border border-blue-200 dark:border-blue-800"
                x-show="show"
                x-transition:enter="transition ease-out duration-500 delay-300"
                x-transition:enter-start="opacity-0 transform translate-y-4"
                x-transition:enter-end="opacity-100 transform translate-y-0"
            >
                <div class="flex items-center justify-between mb-1">
                    <i class="fas fa-file-alt text-xl text-blue-600 dark:text-blue-400"></i>
                    <span class="text-xs text-blue-600 dark:text-blue-400 font-medium">TOTAL</span>
                </div>
                <div class="text-2xl font-bold text-blue-900 dark:text-blue-100">
                    {{ $recommendation->mandatory_permits_count }}
                </div>
                <div class="text-xs text-blue-700 dark:text-blue-300">Izin Wajib</div>
            </div>

            <div 
                class="bg-gradient-to-br from-green-50 to-green-100 dark:from-green-900/20 dark:to-green-800/20 rounded-lg p-4 border border-green-200 dark:border-green-800"
                x-show="show"
                x-transition:enter="transition ease-out duration-500 delay-400"
                x-transition:enter-start="opacity-0 transform translate-y-4"
                x-transition:enter-end="opacity-100 transform translate-y-0"
            >
                <div class="flex items-center justify-between mb-1">
                    <i class="fas fa-money-bill-wave text-xl text-green-600 dark:text-green-400"></i>
                    <span class="text-xs text-green-600 dark:text-green-400 font-medium">ESTIMASI</span>
                </div>
                <div class="text-lg font-bold text-green-900 dark:text-green-100">
                    Rp {{ number_format($recommendation->total_cost_range['min'] ?? 0, 0, ',', '.') }}
                </div>
                <div class="text-xs text-green-700 dark:text-green-300">
                    s/d Rp {{ number_format($recommendation->total_cost_range['max'] ?? 0, 0, ',', '.') }}
                </div>
            </div>

            <div 
                class="bg-gradient-to-br from-purple-50 to-purple-100 dark:from-purple-900/20 dark:to-purple-800/20 rounded-lg p-4 border border-purple-200 dark:border-purple-800"
                x-show="show"
                x-transition:enter="transition ease-out duration-500 delay-500"
                x-transition:enter-start="opacity-0 transform translate-y-4"
                x-transition:enter-end="opacity-100 transform translate-y-0"
            >
                <div class="flex items-center justify-between mb-1">
                    <i class="fas fa-clock text-xl text-purple-600 dark:text-purple-400"></i>
                    <span class="text-xs text-purple-600 dark:text-purple-400 font-medium">DURASI</span>
                </div>
                <div class="text-2xl font-bold text-purple-900 dark:text-purple-100">
                    {{ $recommendation->estimated_timeline['total_days'] ?? '?' }}
                </div>
                <div class="text-xs text-purple-700 dark:text-purple-300">Hari Kerja</div>
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
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 mb-4">
            <div class="p-4 border-b border-gray-200 dark:border-gray-700 bg-{{ $info['color'] }}-50 dark:bg-{{ $info['color'] }}-900/20">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-{{ $info['color'] }}-100 dark:bg-{{ $info['color'] }}-900 rounded-lg flex items-center justify-center">
                            <i class="fas {{ $info['icon'] }} text-{{ $info['color'] }}-600 dark:text-{{ $info['color'] }}-400 text-lg"></i>
                        </div>
                        <div>
                            <h2 class="text-base font-bold text-gray-900 dark:text-white flex items-center gap-2">
                                {{ $info['title'] }}
                                <span class="px-2 py-0.5 bg-{{ $info['color'] }}-600 text-white text-xs rounded-full">
                                    {{ $totalCount }}
                                </span>
                            </h2>
                            <p class="text-xs text-gray-600 dark:text-gray-400 mt-0.5">
                                {{ $info['description'] }}
                            </p>
                        </div>
                    </div>
                    @if($mandatoryCount > 0)
                    <span class="inline-flex items-center px-2.5 py-1 bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200 text-xs font-semibold rounded">
                        <i class="fas fa-star text-yellow-500 mr-1"></i>
                        {{ $mandatoryCount }} Wajib
                    </span>
                    @endif
                </div>
            </div>

            <div class="divide-y divide-gray-200 dark:divide-gray-700">
                @foreach($permits as $index => $permit)
                <div class="p-4">
                    <div class="flex items-start gap-3">
                        <div class="flex-shrink-0 w-8 h-8 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center">
                            <span class="text-blue-600 dark:text-blue-400 font-bold text-sm">{{ $loop->iteration }}</span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-start justify-between gap-2 mb-1">
                                <h3 class="text-base font-semibold text-gray-900 dark:text-white">
                                    {{ $permit['name'] }}
                                </h3>
                                @if(($permit['type'] ?? '') === 'mandatory')
                                <span class="inline-flex items-center px-2 py-0.5 bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200 text-xs font-semibold rounded flex-shrink-0">
                                    <i class="fas fa-star text-yellow-500 mr-1"></i>
                                    WAJIB
                                </span>
                                @elseif(($permit['type'] ?? '') === 'recommended')
                                <span class="inline-flex items-center px-2 py-0.5 bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200 text-xs font-semibold rounded flex-shrink-0">
                                    <i class="fas fa-info-circle mr-1"></i>
                                    REKOMEN
                                </span>
                                @elseif(($permit['type'] ?? '') === 'conditional')
                                <span class="inline-flex items-center px-2 py-0.5 bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 text-xs font-semibold rounded flex-shrink-0">
                                    <i class="fas fa-question-circle mr-1"></i>
                                    KONDISI
                                </span>
                                @endif
                            </div>
                            <p class="text-xs text-gray-600 dark:text-gray-400 mb-2 leading-relaxed">
                                {{ $permit['description'] ?? 'Tidak ada deskripsi' }}
                            </p>
                            
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-3 text-xs mb-2">
                                <div class="flex items-center gap-1">
                                    <span class="text-gray-500 dark:text-gray-400">Penerbit:</span>
                                    <span class="font-medium text-gray-900 dark:text-white truncate">{{ $permit['issuing_authority'] ?? 'N/A' }}</span>
                                </div>
                                <div class="flex items-center gap-1">
                                    <span class="text-gray-500 dark:text-gray-400">Biaya:</span>
                                    <span class="font-medium text-gray-900 dark:text-white">
                                        @if(isset($permit['estimated_cost_range']))
                                            @if(($permit['estimated_cost_range']['min'] ?? 0) == 0 && ($permit['estimated_cost_range']['max'] ?? 0) == 0)
                                                Gratis
                                            @else
                                                Rp {{ number_format($permit['estimated_cost_range']['min'] ?? 0, 0, ',', '.') }}
                                                @if(($permit['estimated_cost_range']['max'] ?? 0) > ($permit['estimated_cost_range']['min'] ?? 0))
                                                    - Rp {{ number_format($permit['estimated_cost_range']['max'], 0, ',', '.') }}
                                                @endif
                                            @endif
                                        @else
                                            N/A
                                        @endif
                                    </span>
                                </div>
                                <div class="flex items-center gap-1">
                                    <span class="text-gray-500 dark:text-gray-400">Durasi:</span>
                                    <span class="font-medium text-gray-900 dark:text-white">{{ $permit['estimated_days'] ?? 'N/A' }} hari</span>
                                </div>
                            </div>

                            <!-- Prerequisites & Dependencies -->
                            @if(!empty($permit['prerequisites']) || !empty($permit['triggers_next']))
                            <div class="mt-3 pt-3 border-t border-gray-200 dark:border-gray-700 space-y-2">
                                @if(!empty($permit['prerequisites']))
                                <div class="flex items-start gap-2">
                                    <i class="fas fa-arrow-left text-blue-600 dark:text-blue-400 text-xs mt-1"></i>
                                    <div class="flex-1">
                                        <span class="text-xs font-semibold text-gray-700 dark:text-gray-300">Perlu:</span>
                                        <div class="flex flex-wrap gap-1.5 mt-1">
                                            @foreach($permit['prerequisites'] as $prereq)
                                            <span class="inline-flex items-center px-2 py-0.5 bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 text-xs rounded">
                                                {{ $prereq }}
                                            </span>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                                @endif

                                @if(!empty($permit['triggers_next']))
                                <div class="flex items-start gap-2">
                                    <i class="fas fa-arrow-right text-green-600 dark:text-green-400 text-xs mt-1"></i>
                                    <div class="flex-1">
                                        <span class="text-xs font-semibold text-gray-700 dark:text-gray-300">Buka akses:</span>
                                        <div class="flex flex-wrap gap-1.5 mt-1">
                                            @foreach($permit['triggers_next'] as $next)
                                            <span class="inline-flex items-center px-2 py-0.5 bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200 text-xs rounded">
                                                {{ $next }}
                                            </span>
                                            @endforeach
                                        </div>
                                    </div>
                                    </div>
                                </div>
                                @endif
                            </div>
                            @endif

                            @if(!empty($permit['requirements']))
                            <div class="mt-2">
                                <button 
                                    onclick="toggleRequirements('permit-{{ $loop->parent->index }}-{{ $loop->index }}')" 
                                    class="text-xs text-blue-600 dark:text-blue-400 hover:underline flex items-center gap-1"
                                >
                                    <i class="fas fa-chevron-down text-xs" id="icon-permit-{{ $loop->parent->index }}-{{ $loop->index }}"></i>
                                    Persyaratan ({{ count($permit['requirements']) }})
                                </button>
                                <ul id="permit-{{ $loop->parent->index }}-{{ $loop->index }}" class="hidden mt-2 ml-4 space-y-1 text-xs text-gray-700 dark:text-gray-300 list-disc list-inside">
                                    @foreach($permit['requirements'] as $req)
                                    <li>{{ $req }}</li>
                                    @endforeach
                                </ul>
                            </div>
                            @endif

                            @if(!empty($permit['legal_basis']) || !empty($permit['renewal_period']))
                            <div class="mt-2 pt-2 border-t border-gray-200 dark:border-gray-700 space-y-1">
                                @if(!empty($permit['legal_basis']))
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    <i class="fas fa-gavel mr-1"></i>
                                    <strong>Dasar:</strong> {{ $permit['legal_basis'] }}
                                </p>
                                @endif
                                @if(!empty($permit['renewal_period']))
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    <i class="fas fa-redo mr-1"></i>
                                    <strong>Perpanjangan:</strong> {{ $permit['renewal_period'] }}
                                </p>
                                @endif
                            </div>
                            @endif
                        </div>
                    </div>
                @endforeach
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
        <div class="flex flex-wrap gap-3 justify-center mt-6">
            <button 
                onclick="window.print()" 
                class="px-5 py-2.5 bg-gray-600 hover:bg-gray-700 text-white text-sm rounded-lg transition-colors inline-flex items-center gap-2"
            >
                <i class="fas fa-download"></i>
                Download PDF
            </button>
            
            <a 
                href="{{ route('client.applications.create', ['kbli_code' => $kbli->code]) }}" 
                class="px-6 py-3 bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 text-white font-semibold text-sm rounded-lg transition-all shadow-lg hover:shadow-xl inline-flex items-center gap-2"
            >
                <i class="fas fa-paper-plane"></i>
                Ajukan Permohonan / Konsultasi
            </a>
        </div>

        <!-- Footer Note -->
        <div class="mt-6 text-center text-xs text-gray-500 dark:text-gray-400">
            <i class="fas fa-shield-alt mr-1"></i>
            Data perhitungan berdasarkan regulasi terkini dan pengalaman proyek perizinan yang telah kami tangani. Untuk kepastian hukum, konsultasikan dengan tim ahli kami.
        </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
function toggleRequirements(id) {
    const element = document.getElementById(id);
    const icon = document.getElementById('icon-' + id);
    
    if (element.classList.contains('hidden')) {
        element.classList.remove('hidden');
        icon.classList.remove('fa-chevron-down');
        icon.classList.add('fa-chevron-up');
    } else {
        element.classList.add('hidden');
        icon.classList.remove('fa-chevron-up');
        icon.classList.add('fa-chevron-down');
    }
}
</script>
@endpush
@endsection

