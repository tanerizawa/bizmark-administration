@extends('client.layouts.app')

@section('title', 'Katalog Layanan Perizinan - BizMark')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Hero Section -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">
                Katalog Layanan Perizinan Usaha
            </h1>
            <p class="text-gray-600 dark:text-gray-400">
                Temukan izin usaha yang Anda butuhkan berdasarkan kode KBLI (Klasifikasi Baku Lapangan Usaha Indonesia). 
                Sistem kami akan memberikan rekomendasi perizinan yang dipersonalisasi berdasarkan regulasi terkini.
            </p>
        </div>

        <!-- KBLI Search -->
        <div class="mb-8 bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
            <label for="kbli-search" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                Cari KBLI Anda
            </label>
            <div class="relative" x-data="kbliSearch()">
                <input 
                    type="text" 
                    id="kbli-search"
                    x-model="query"
                    @input.debounce.300ms="search()"
                    @focus="focused = true"
                    @blur="setTimeout(() => focused = false, 200)"
                    placeholder="Ketik kode atau deskripsi KBLI (contoh: 46311 atau perdagangan besar)"
                    class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                />
                
                <!-- Search Results Dropdown -->
                <div 
                    x-show="focused && results.length > 0"
                    x-transition
                    class="absolute z-10 w-full mt-2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-lg max-h-96 overflow-y-auto"
                >
                    <template x-for="result in results" :key="result.code">
                        <a 
                            :href="`/client/services/${result.code}/context`"
                            class="block px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-700 border-b border-gray-100 dark:border-gray-700 last:border-0"
                        >
                            <div class="flex items-start">
                                <span class="font-mono font-semibold text-blue-600 dark:text-blue-400 mr-3" x-text="result.code"></span>
                                <span class="text-gray-700 dark:text-gray-300 flex-1" x-text="result.description"></span>
                            </div>
                            <div class="text-xs text-gray-500 dark:text-gray-400 mt-1 ml-16">
                                <span>Sektor: </span><span x-text="result.sector"></span>
                            </div>
                        </a>
                    </template>
                </div>

                <!-- Loading State -->
                <div 
                    x-show="loading" 
                    class="absolute right-3 top-3"
                >
                    <svg class="animate-spin h-5 w-5 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </div>
            </div>

            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                Tidak menemukan KBLI Anda? 
                <a href="https://oss.go.id/kbli" target="_blank" class="text-blue-600 dark:text-blue-400 hover:underline">
                    Lihat daftar lengkap KBLI
                </a>
            </p>
        </div>

        <!-- Sector Filters -->
        <div class="mb-8">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Pilih Berdasarkan Sektor</h2>
            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-3">
                @foreach($sectors as $sector)
                <a 
                    href="{{ route('client.services.index', ['sector' => $sector->sector]) }}"
                    class="px-4 py-3 text-center border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-blue-50 dark:hover:bg-gray-700 hover:border-blue-500 transition-colors
                        {{ request('sector') == $sector->sector ? 'bg-blue-50 dark:bg-gray-700 border-blue-500' : 'bg-white dark:bg-gray-800' }}"
                >
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $sector->sector }}</span>
                </a>
                @endforeach
            </div>
            @if(request('sector'))
            <div class="mt-3">
                <a href="{{ route('client.services.index') }}" class="text-sm text-blue-600 dark:text-blue-400 hover:underline">
                    ← Tampilkan semua sektor
                </a>
            </div>
            @endif
        </div>

        <!-- Popular KBLI -->
        <div>
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">KBLI Populer</h2>
                <span class="text-sm text-gray-500 dark:text-gray-400">
                    <i class="fas fa-fire text-orange-500 mr-1"></i>
                    Paling banyak dicari
                </span>
            </div>

            @if($popularKbli->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($popularKbli as $kbli)
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6 hover:shadow-md transition-shadow">
                    <div class="flex items-start justify-between mb-3">
                        <span class="inline-block px-3 py-1 bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 text-xs font-mono font-semibold rounded">
                            {{ $kbli->code }}
                        </span>
                        <span class="text-xs text-gray-500 dark:text-gray-400">
                            {{ $kbli->cache_hits ?? 0 }}+ pencarian
                        </span>
                    </div>
                    
                    <h3 class="text-base font-medium text-gray-900 dark:text-white mb-2">
                        {{ Str::limit($kbli->description, 100) }}
                    </h3>
                    
                    <div class="text-xs text-gray-500 dark:text-gray-400 mb-4">
                        <span class="inline-block">Sektor {{ $kbli->sector }}</span>
                        <span class="mx-2">•</span>
                        <span class="inline-block">Kode: {{ $kbli->code }}</span>
                    </div>

                    <a 
                        href="{{ route('client.services.context', $kbli->code) }}" 
                        class="block w-full text-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors"
                    >
                        Mulai Rekomendasi
                    </a>
                </div>
                @endforeach
            </div>
            @else
            <div class="text-center py-12 bg-gray-50 dark:bg-gray-800 rounded-lg">
                <i class="fas fa-search text-4xl text-gray-400 dark:text-gray-600 mb-4"></i>
                <p class="text-gray-600 dark:text-gray-400">Belum ada data KBLI populer. Mulai pencarian pertama Anda!</p>
            </div>
            @endif
        </div>

        <!-- Info Banner -->
        <div class="mt-8 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
            <div class="flex items-start">
                <i class="fas fa-info-circle text-blue-600 dark:text-blue-400 mt-0.5 mr-3"></i>
                <div class="text-sm text-blue-800 dark:text-blue-300">
                    <strong class="font-semibold">Cara Kerja:</strong>
                    <ol class="list-decimal list-inside mt-2 space-y-1">
                        <li>Pilih kode KBLI yang sesuai dengan usaha Anda</li>
                        <li>Masukkan informasi bisnis (skala & lokasi) untuk rekomendasi lebih akurat</li>
                        <li>Sistem kami akan menganalisis dan memberikan daftar izin yang dibutuhkan</li>
                        <li>Download ringkasan atau langsung ajukan permohonan izin</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function kbliSearch() {
    return {
        query: '',
        results: [],
        loading: false,
        focused: false,
        
        async search() {
            if (this.query.length < 3) {
                this.results = [];
                return;
            }
            
            this.loading = true;
            
            try {
                const response = await fetch(`/api/kbli/search?q=${encodeURIComponent(this.query)}`);
                const data = await response.json();
                this.results = data.data || [];
            } catch (error) {
                console.error('KBLI search error:', error);
                this.results = [];
            } finally {
                this.loading = false;
            }
        }
    }
}
</script>
@endpush
@endsection

