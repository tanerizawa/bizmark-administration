@extends('client.layouts.app')

@section('title', 'Konteks Bisnis - BizMark')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-3xl">
    <!-- Back Button -->
    <a href="{{ route('client.services.index') }}" class="inline-flex items-center text-blue-600 dark:text-blue-400 hover:underline mb-6">
        <i class="fas fa-arrow-left mr-2"></i>
        Kembali ke Katalog
    </a>

    <!-- KBLI Info Card -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6 mb-6">
        <div class="flex items-start">
            <div class="flex-shrink-0 mr-4">
                <div class="w-16 h-16 bg-blue-100 dark:bg-blue-900 rounded-lg flex items-center justify-center">
                    <i class="fas fa-briefcase text-2xl text-blue-600 dark:text-blue-400"></i>
                </div>
            </div>
            <div class="flex-1">
                <span class="inline-block px-3 py-1 bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 text-xs font-mono font-semibold rounded mb-2">
                    {{ $kbli->code }}
                </span>
                <h1 class="text-xl font-bold text-gray-900 dark:text-white mb-1">
                    {{ $kbli->description }}
                </h1>
                <div class="text-sm text-gray-600 dark:text-gray-400">
                    Sektor {{ $kbli->sector }} • Kode: {{ $kbli->code }}
                </div>
            </div>
        </div>
    </div>

    <!-- Context Form -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
        <div class="mb-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">
                Konteks Bisnis (Opsional)
            </h2>
            <p class="text-sm text-gray-600 dark:text-gray-400">
                Informasi tambahan ini akan membantu sistem kami memberikan rekomendasi perizinan yang lebih akurat dan relevan dengan kondisi bisnis Anda.
            </p>
        </div>

        <form 
            action="{{ route('client.services.show', $kbli->code) }}" 
            method="GET" 
            class="space-y-6"
            x-data="{ 
                isSubmitting: false,
                submitForm(e) {
                    // Validate at least one field is selected
                    const formData = new FormData(e.target);
                    if (!formData.get('scale') && !formData.get('location')) {
                        alert('Mohon pilih minimal salah satu opsi (Skala Usaha atau Lokasi)');
                        return false;
                    }
                    this.isSubmitting = true;
                    return true;
                }
            }"
            @submit="submitForm($event)"
        >
            <!-- Business Scale -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                    Skala Usaha
                </label>
                <div class="space-y-2">
                    <label class="flex items-center p-4 border border-gray-300 dark:border-gray-600 rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                        <input type="radio" name="scale" value="mikro" class="mr-3 text-blue-600 focus:ring-blue-500">
                        <div>
                            <div class="font-medium text-gray-900 dark:text-white">Usaha Mikro</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">Aset ≤ Rp 50 juta atau Omzet ≤ Rp 300 juta/tahun</div>
                        </div>
                    </label>
                    
                    <label class="flex items-center p-4 border border-gray-300 dark:border-gray-600 rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                        <input type="radio" name="scale" value="kecil" class="mr-3 text-blue-600 focus:ring-blue-500">
                        <div>
                            <div class="font-medium text-gray-900 dark:text-white">Usaha Kecil</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">Aset Rp 50 juta - Rp 500 juta atau Omzet Rp 300 juta - Rp 2,5 miliar/tahun</div>
                        </div>
                    </label>
                    
                    <label class="flex items-center p-4 border border-gray-300 dark:border-gray-600 rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                        <input type="radio" name="scale" value="menengah" class="mr-3 text-blue-600 focus:ring-blue-500">
                        <div>
                            <div class="font-medium text-gray-900 dark:text-white">Usaha Menengah</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">Aset Rp 500 juta - Rp 10 miliar atau Omzet Rp 2,5 miliar - Rp 50 miliar/tahun</div>
                        </div>
                    </label>
                    
                    <label class="flex items-center p-4 border border-gray-300 dark:border-gray-600 rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                        <input type="radio" name="scale" value="besar" class="mr-3 text-blue-600 focus:ring-blue-500">
                        <div>
                            <div class="font-medium text-gray-900 dark:text-white">Usaha Besar</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">Aset > Rp 10 miliar atau Omzet > Rp 50 miliar/tahun</div>
                        </div>
                    </label>
                </div>
            </div>

            <!-- Location Type -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                    Lokasi Usaha
                </label>
                <div class="space-y-2">
                    <label class="flex items-center p-4 border border-gray-300 dark:border-gray-600 rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                        <input type="radio" name="location" value="perkotaan" class="mr-3 text-blue-600 focus:ring-blue-500">
                        <div>
                            <div class="font-medium text-gray-900 dark:text-white">Perkotaan</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">Area komersial, pusat kota, zona bisnis</div>
                        </div>
                    </label>
                    
                    <label class="flex items-center p-4 border border-gray-300 dark:border-gray-600 rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                        <input type="radio" name="location" value="pedesaan" class="mr-3 text-blue-600 focus:ring-blue-500">
                        <div>
                            <div class="font-medium text-gray-900 dark:text-white">Pedesaan</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">Area pertanian, perkebunan, desa</div>
                        </div>
                    </label>
                    
                    <label class="flex items-center p-4 border border-gray-300 dark:border-gray-600 rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                        <input type="radio" name="location" value="kawasan_industri" class="mr-3 text-blue-600 focus:ring-blue-500">
                        <div>
                            <div class="font-medium text-gray-900 dark:text-white">Kawasan Industri</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">Area pabrik, gudang, manufaktur</div>
                        </div>
                    </label>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center justify-between pt-4 border-t border-gray-200 dark:border-gray-700">
                <a 
                    href="{{ route('client.services.show', $kbli->code) }}" 
                    class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors"
                    :class="{ 'pointer-events-none opacity-50': isSubmitting }"
                >
                    Lewati (Rekomendasi Umum)
                </a>
                
                <button 
                    type="submit" 
                    class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-all inline-flex items-center disabled:opacity-50 disabled:cursor-not-allowed"
                    :disabled="isSubmitting"
                >
                    <span x-show="!isSubmitting">
                        Dapatkan Rekomendasi
                        <i class="fas fa-arrow-right ml-2"></i>
                    </span>
                    <span x-show="isSubmitting" class="inline-flex items-center">
                        <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Memproses...
                    </span>
                </button>
            </div>
        </form>
    </div>

    <!-- Info -->
    <div class="mt-6 flex items-start text-sm text-gray-600 dark:text-gray-400">
        <i class="fas fa-shield-alt text-blue-600 dark:text-blue-400 mr-2 mt-0.5"></i>
        <p>
            Data bisnis Anda akan disimpan dengan aman dan hanya digunakan untuk memberikan rekomendasi perizinan yang relevan.
        </p>
    </div>

    <!-- Full-Screen Loading Overlay -->
    <div 
        x-show="isSubmitting"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        class="fixed inset-0 bg-gray-900 bg-opacity-95 z-50 flex items-center justify-center"
        style="display: none;"
    >
        <div class="text-center">
            <!-- Animated Logo/Icon -->
            <div class="relative inline-block mb-8">
                <div class="absolute inset-0 flex items-center justify-center">
                    <div class="w-32 h-32 border-4 border-blue-500 border-opacity-30 rounded-full animate-ping"></div>
                </div>
                <div class="absolute inset-0 flex items-center justify-center">
                    <div class="w-40 h-40 border-4 border-purple-500 border-opacity-20 rounded-full animate-pulse"></div>
                </div>
                <div class="relative w-40 h-40 flex items-center justify-center">
                    <i class="fas fa-magic text-6xl text-white animate-bounce"></i>
                </div>
            </div>

            <!-- Loading Text -->
            <h3 class="text-3xl font-bold text-white mb-4">
                Sedang Memproses Analisis ✨
            </h3>
            
            <!-- Animated Steps -->
            <div class="max-w-md mx-auto space-y-3 mb-6" x-data="{ 
                steps: [
                    '🔍 Menganalisis KBLI Anda...',
                    '📚 Mempelajari regulasi terkait...',
                    '⚙️ Sistem sedang memproses...',
                    '📝 Menyusun rekomendasi...',
                    '✅ Hampir selesai...'
                ],
                currentStep: 0,
                init() {
                    const interval = setInterval(() => {
                        if (this.currentStep < this.steps.length - 1) {
                            this.currentStep++;
                        }
                    }, 2500);
                }
            }">
                <template x-for="(step, index) in steps" :key="index">
                    <div 
                        x-show="index === currentStep"
                        x-transition:enter="transition ease-out duration-500"
                        x-transition:enter-start="opacity-0 transform translate-y-4"
                        x-transition:enter-end="opacity-100 transform translate-y-0"
                        class="text-lg text-gray-300"
                        x-text="step"
                    ></div>
                </template>
            </div>

            <!-- Progress Bar -->
            <div class="max-w-md mx-auto">
                <div class="h-2 bg-gray-800 rounded-full overflow-hidden">
                    <div class="h-full bg-gradient-to-r from-blue-500 via-purple-500 to-pink-500 animate-progress-infinite"></div>
                </div>
                <p class="text-sm text-gray-400 mt-3">
                    Mohon tunggu, proses ini membutuhkan 15-30 detik
                </p>
            </div>
        </div>
    </div>

    <style>
        @keyframes progress-infinite {
            0% { transform: translateX(-100%) scaleX(0.3); }
            50% { transform: translateX(50%) scaleX(0.6); }
            100% { transform: translateX(200%) scaleX(0.3); }
        }
        .animate-progress-infinite {
            animation: progress-infinite 2s ease-in-out infinite;
        }
    </style>
</div>
@endsection
