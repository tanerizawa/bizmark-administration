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
                        '💡 AI kami menganalisis ribuan regulasi untuk memberikan rekomendasi terbaik',
                        '🚀 Proses perizinan yang tepat dapat menghemat waktu hingga 60%',
                        '📊 Kami telah membantu ribuan UKM dalam proses perizinan',
                        '⚡ Rekomendasi AI disesuaikan dengan skala dan lokasi usaha Anda',
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
                                AI kami telah menganalisis {{ count($recommendation->recommended_permits ?? []) }} jenis izin yang relevan untuk bisnis Anda
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
                class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6 mb-6"
                x-show="show"
                x-transition:enter="transition ease-out duration-500"
                x-transition:enter-start="opacity-0 transform -translate-y-4"
                x-transition:enter-end="opacity-100 transform translate-y-0"
            >
            <div class="flex items-start justify-between">
                <div>
                    <span class="inline-block px-3 py-1 bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 text-xs font-mono font-semibold rounded mb-2">
                        {{ $kbli->code }}
                    </span>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-1">
                        {{ $kbli->description }}
                    </h1>
                    <div class="text-sm text-gray-600 dark:text-gray-400">
                        Sektor {{ $kbli->sector }} • Kode: {{ $kbli->code }}
                    </div>
                </div>
                <div class="text-right">
                    <div class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium
                        {{ $recommendation->confidence_score >= 0.8 ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200' }}">
                        <i class="fas fa-check-circle mr-1"></i>
                        Confidence: {{ number_format($recommendation->confidence_score * 100, 0) }}%
                    </div>
                    <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                        AI Model: {{ $recommendation->ai_model }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <div 
                class="bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-800/20 rounded-lg p-6 border border-blue-200 dark:border-blue-800"
                x-show="show"
                x-transition:enter="transition ease-out duration-500 delay-300"
                x-transition:enter-start="opacity-0 transform translate-y-4"
                x-transition:enter-end="opacity-100 transform translate-y-0"
            >
                <div class="flex items-center justify-between mb-2">
                    <i class="fas fa-file-alt text-2xl text-blue-600 dark:text-blue-400"></i>
                    <span class="text-xs text-blue-600 dark:text-blue-400 font-medium">TOTAL IZIN</span>
                </div>
                <div class="text-3xl font-bold text-blue-900 dark:text-blue-100">
                    {{ $recommendation->mandatory_permits_count }}
                </div>
                <div class="text-sm text-blue-700 dark:text-blue-300 mt-1">Izin Wajib</div>
            </div>

            <div 
                class="bg-gradient-to-br from-green-50 to-green-100 dark:from-green-900/20 dark:to-green-800/20 rounded-lg p-6 border border-green-200 dark:border-green-800"
                x-show="show"
                x-transition:enter="transition ease-out duration-500 delay-400"
                x-transition:enter-start="opacity-0 transform translate-y-4"
                x-transition:enter-end="opacity-100 transform translate-y-0"
            >
                <div class="flex items-center justify-between mb-2">
                    <i class="fas fa-money-bill-wave text-2xl text-green-600 dark:text-green-400"></i>
                    <span class="text-xs text-green-600 dark:text-green-400 font-medium">ESTIMASI BIAYA</span>
                </div>
                <div class="text-2xl font-bold text-green-900 dark:text-green-100">
                    Rp {{ number_format($recommendation->total_cost_range['min'] ?? 0, 0, ',', '.') }}
                </div>
                <div class="text-sm text-green-700 dark:text-green-300 mt-1">
                    s/d Rp {{ number_format($recommendation->total_cost_range['max'] ?? 0, 0, ',', '.') }}
                </div>
            </div>

            <div 
                class="bg-gradient-to-br from-purple-50 to-purple-100 dark:from-purple-900/20 dark:to-purple-800/20 rounded-lg p-6 border border-purple-200 dark:border-purple-800"
                x-show="show"
                x-transition:enter="transition ease-out duration-500 delay-500"
                x-transition:enter-start="opacity-0 transform translate-y-4"
                x-transition:enter-end="opacity-100 transform translate-y-0"
            >
                <div class="flex items-center justify-between mb-2">
                    <i class="fas fa-clock text-2xl text-purple-600 dark:text-purple-400"></i>
                    <span class="text-xs text-purple-600 dark:text-purple-400 font-medium">WAKTU PROSES</span>
                </div>
                <div class="text-3xl font-bold text-purple-900 dark:text-purple-100">
                    {{ $recommendation->estimated_timeline['total_days'] ?? '?' }}
                </div>
                <div class="text-sm text-purple-700 dark:text-purple-300 mt-1">Hari Kerja</div>
            </div>
        </div>

        <!-- Risk Assessment -->
        @if(!empty($recommendation->risk_assessment))
        <div class="mb-6 bg-orange-50 dark:bg-orange-900/20 border border-orange-200 dark:border-orange-800 rounded-lg p-6">
            <div class="flex items-start">
                <i class="fas fa-exclamation-triangle text-orange-600 dark:text-orange-400 text-xl mr-3 mt-1"></i>
                <div class="flex-1">
                    <h3 class="font-semibold text-orange-900 dark:text-orange-200 mb-2">Penilaian Risiko</h3>
                    <div class="text-sm text-orange-800 dark:text-orange-300 space-y-1">
                        @if(isset($recommendation->risk_assessment['level']))
                        <p><strong>Level Risiko:</strong> {{ ucfirst($recommendation->risk_assessment['level']) }}</p>
                        @endif
                        @if(isset($recommendation->risk_assessment['notes']))
                        <p>{{ $recommendation->risk_assessment['notes'] }}</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Mandatory Permits -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 mb-6">
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white flex items-center">
                    <i class="fas fa-star text-yellow-500 mr-2"></i>
                    Izin Wajib
                </h2>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                    Perizinan yang harus dipenuhi untuk menjalankan usaha secara legal
                </p>
            </div>

            <div class="divide-y divide-gray-200 dark:border-gray-700">
                @forelse($recommendation->recommended_permits as $index => $permit)
                @if(($permit['type'] ?? '') === 'mandatory')
                <div class="p-6">
                    <div class="flex items-start">
                        <div class="flex-shrink-0 w-10 h-10 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center mr-4">
                            <span class="text-blue-600 dark:text-blue-400 font-bold">{{ $loop->iteration }}</span>
                        </div>
                        <div class="flex-1">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">
                                {{ $permit['name'] }}
                            </h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">
                                {{ $permit['description'] ?? 'Tidak ada deskripsi' }}
                            </p>
                            
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                                <div>
                                    <span class="text-gray-500 dark:text-gray-400">Penerbit:</span>
                                    <span class="ml-2 font-medium text-gray-900 dark:text-white">{{ $permit['issuing_authority'] ?? 'N/A' }}</span>
                                </div>
                                <div>
                                    <span class="text-gray-500 dark:text-gray-400">Biaya:</span>
                                    <span class="ml-2 font-medium text-gray-900 dark:text-white">
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
                                <div>
                                    <span class="text-gray-500 dark:text-gray-400">Waktu Proses:</span>
                                    <span class="ml-2 font-medium text-gray-900 dark:text-white">
                                        {{ isset($permit['estimated_days']) ? $permit['estimated_days'] . ' hari' : ($permit['processing_time'] ?? 'N/A') }}
                                    </span>
                                </div>
                            </div>

                            @if(!empty($permit['requirements']))
                            <div class="mt-3">
                                <button 
                                    onclick="toggleRequirements('permit-{{ $index }}')" 
                                    class="text-sm text-blue-600 dark:text-blue-400 hover:underline"
                                >
                                    <i class="fas fa-chevron-down mr-1" id="icon-permit-{{ $index }}"></i>
                                    Lihat Persyaratan ({{ count($permit['requirements']) }})
                                </button>
                                <ul id="permit-{{ $index }}" class="hidden mt-2 ml-4 space-y-1 text-sm text-gray-700 dark:text-gray-300 list-disc list-inside">
                                    @foreach($permit['requirements'] as $req)
                                    <li>{{ $req }}</li>
                                    @endforeach
                                </ul>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                @endif
                @empty
                <div class="p-6 text-center text-gray-500 dark:text-gray-400">
                    Tidak ada izin wajib teridentifikasi
                </div>
                @endforelse
            </div>
        </div>

        <!-- Required Documents -->
        @if(!empty($recommendation->required_documents))
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 mb-6">
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white flex items-center">
                    <i class="fas fa-folder-open mr-2 text-blue-600"></i>
                    Dokumen yang Dibutuhkan
                </h2>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    @foreach($recommendation->required_documents as $doc)
                    <div class="flex items-start p-4 bg-gray-50 dark:bg-gray-700 rounded-lg border border-gray-200 dark:border-gray-600">
                        <i class="fas fa-file-alt text-blue-600 dark:text-blue-400 mt-1 mr-3"></i>
                        <div class="flex-1">
                            <h4 class="font-semibold text-gray-900 dark:text-white text-sm mb-1">
                                {{ is_array($doc) ? ($doc['name'] ?? 'Dokumen') : $doc }}
                            </h4>
                            @if(is_array($doc) && isset($doc['notes']))
                            <p class="text-xs text-gray-600 dark:text-gray-400 mb-1">{{ $doc['notes'] }}</p>
                            @endif
                            @if(is_array($doc))
                            <div class="flex items-center gap-2 text-xs">
                                @if(isset($doc['type']))
                                <span class="px-2 py-0.5 bg-blue-100 dark:bg-blue-900 text-blue-700 dark:text-blue-300 rounded">
                                    {{ ucfirst($doc['type']) }}
                                </span>
                                @endif
                                @if(isset($doc['format']))
                                <span class="px-2 py-0.5 bg-gray-200 dark:bg-gray-600 text-gray-700 dark:text-gray-300 rounded">
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
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 mb-6">
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white flex items-center">
                    <i class="fas fa-calendar-alt mr-2 text-purple-600"></i>
                    Timeline Proses Perizinan
                </h2>
            </div>
            <div class="p-6">
                <!-- Timeline Summary -->
                <div class="bg-purple-50 dark:bg-purple-900/20 rounded-lg p-4 mb-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Estimasi Waktu Total</p>
                            <p class="text-2xl font-bold text-purple-900 dark:text-purple-100">
                                {{ $recommendation->estimated_timeline['minimum_days'] ?? 'N/A' }} - 
                                {{ $recommendation->estimated_timeline['maximum_days'] ?? 'N/A' }} Hari
                            </p>
                        </div>
                        <i class="fas fa-clock text-4xl text-purple-400"></i>
                    </div>
                </div>

                <!-- Critical Path -->
                @if(!empty($recommendation->estimated_timeline['critical_path']))
                <div>
                    <h3 class="font-semibold text-gray-900 dark:text-white mb-3">Jalur Kritis Perizinan:</h3>
                    <div class="space-y-3">
                        @foreach($recommendation->estimated_timeline['critical_path'] as $index => $step)
                        <div class="flex items-start">
                            <div class="flex-shrink-0 w-8 h-8 bg-purple-100 dark:bg-purple-900 rounded-full flex items-center justify-center mr-4 mt-1">
                                <span class="text-purple-600 dark:text-purple-400 font-bold text-sm">{{ $index + 1 }}</span>
                            </div>
                            <div class="flex-1">
                                <h4 class="font-medium text-gray-900 dark:text-white">{{ $step }}</h4>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
        </div>
        @endif

        <!-- Action Buttons -->
        <div class="flex flex-wrap gap-4 justify-center">
            <button 
                onclick="window.print()" 
                class="px-6 py-3 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-colors inline-flex items-center"
            >
                <i class="fas fa-download mr-2"></i>
                Download PDF
            </button>
            
            <a 
                href="{{ route('client.contact') }}" 
                class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors inline-flex items-center"
            >
                <i class="fas fa-comments mr-2"></i>
                Konsultasi dengan Ahli
            </a>
            
            <a 
                href="{{ route('client.applications.create', ['kbli_code' => $kbli->code]) }}" 
                class="px-6 py-3 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors inline-flex items-center"
            >
                <i class="fas fa-paper-plane mr-2"></i>
                Ajukan Permohonan
            </a>
        </div>

        <!-- Footer Note -->
        <div class="mt-8 text-center text-sm text-gray-500 dark:text-gray-400">
            <i class="fas fa-info-circle mr-1"></i>
            Rekomendasi ini dihasilkan oleh AI berdasarkan data KBLI dan regulasi terkini.
            Untuk kepastian hukum, silakan konsultasikan dengan ahli perizinan kami.
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

