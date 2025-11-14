@extends('layouts.client')

@section('title', 'Konteks Bisnis - BizMark')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-3xl">
    <!-- Back Button -->
    <a href="{{ route('services.index') }}" class="inline-flex items-center text-blue-600 dark:text-blue-400 hover:underline mb-6">
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
                Informasi tambahan ini akan membantu AI memberikan rekomendasi perizinan yang lebih akurat dan relevan dengan kondisi bisnis Anda.
            </p>
        </div>

        <form action="{{ route('services.show', $kbli->code) }}" method="GET" class="space-y-6">
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
                    href="{{ route('services.show', $kbli->code) }}" 
                    class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors"
                >
                    Lewati (Rekomendasi Umum)
                </a>
                
                <button 
                    type="submit" 
                    class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors inline-flex items-center"
                >
                    Dapatkan Rekomendasi
                    <i class="fas fa-arrow-right ml-2"></i>
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
</div>
@endsection
