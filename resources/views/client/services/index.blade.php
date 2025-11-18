@extends('client.layouts.app')

@section('title', 'Katalog Layanan Perizinan - BizMark')

@section('content')
<!-- Mobile Hero - LinkedIn Style -->
<div class="lg:hidden bg-[#0a66c2] text-white border-b border-gray-200 dark:border-gray-700">
    <div class="p-6">
        <div class="mb-4">
            <p class="text-xs text-white/70 uppercase tracking-wider mb-1">Katalog Layanan</p>
            <h1 class="text-xl font-bold mb-2">Cari Layanan Perizinan Anda</h1>
            <p class="text-sm text-white/90">Temukan izin usaha yang tepat dari 120+ layanan tersedia</p>
        </div>
        
        <!-- Compact Stats Grid -->
        <div class="grid grid-cols-3 gap-2 mb-4">
            <div class="bg-white/10 backdrop-blur px-3 py-2.5 text-center">
                <i class="fas fa-certificate text-white/70 text-xs mb-1"></i>
                <p class="text-lg font-bold">120+</p>
                <p class="text-[10px] text-white/70">Layanan</p>
            </div>
            <div class="bg-white/10 backdrop-blur px-3 py-2.5 text-center">
                <i class="fas fa-layer-group text-white/70 text-xs mb-1"></i>
                <p class="text-lg font-bold">15</p>
                <p class="text-[10px] text-white/70">Kategori</p>
            </div>
            <div class="bg-white/10 backdrop-blur px-3 py-2.5 text-center">
                <i class="fas fa-bolt text-white/70 text-xs mb-1"></i>
                <p class="text-lg font-bold">7-14</p>
                <p class="text-[10px] text-white/70">Hari</p>
            </div>
        </div>
        
        <!-- Quick Actions -->
        <div class="grid grid-cols-2 gap-2">
            <a href="#search-section" class="flex items-center justify-center gap-2 px-4 py-3 bg-white/10 backdrop-blur border border-white/30 text-white font-semibold rounded-lg hover:bg-white/20 active:scale-95 transition-all">
                <i class="fas fa-search text-sm"></i>
                <span class="text-sm">Cari KBLI</span>
            </a>
            <a href="#support-section" class="flex items-center justify-center gap-2 px-4 py-3 bg-white text-[#0a66c2] font-semibold rounded-lg hover:bg-white/95 active:scale-95 transition-all">
                <i class="fas fa-headset text-sm"></i>
                <span class="text-sm">Konsultasi</span>
            </a>
        </div>
    </div>
</div>

<!-- Desktop Hero - LinkedIn Style -->
<div class="hidden lg:block bg-[#0a66c2] text-white border-b border-gray-200 dark:border-gray-700">
    <div class="px-6 lg:px-8 py-6">
        <div class="flex items-start justify-between gap-6">
            <!-- Left: Info & CTA -->
            <div class="flex-1">
                <div class="inline-flex items-center gap-2 bg-white/20 backdrop-blur px-3 py-1.5 rounded-full text-xs font-semibold mb-3">
                    <i class="fas fa-sparkles"></i>
                    <span>AI-Powered Recommendation</span>
                </div>
                
                <h1 class="text-2xl lg:text-3xl font-bold mb-3">
                    Temukan Layanan Perizinan yang Tepat untuk Bisnis Anda
                </h1>
                <p class="text-base text-white/90 mb-6 max-w-2xl">
                    Sistem cerdas kami menganalisis kebutuhan bisnis Anda dan merekomendasikan izin yang wajib, lengkap dengan estimasi biaya dan waktu proses.
                </p>
                
                <!-- CTA Buttons -->
                <div class="flex items-center gap-3">
                    <a href="{{ route('client.applications.index') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-white text-[#0a66c2] font-semibold rounded-lg hover:bg-white/95 active:scale-95 transition-all">
                        <i class="fas fa-list"></i>
                        <span>Permohonan Saya</span>
                    </a>
                    <a href="#support-section" class="inline-flex items-center gap-2 px-6 py-3 bg-white/10 backdrop-blur border border-white/30 text-white font-semibold rounded-lg hover:bg-white/20 active:scale-95 transition-all">
                        <i class="fas fa-headset"></i>
                        <span>Konsultasi Gratis</span>
                    </a>
                </div>
            </div>
            
            <!-- Right: Stats Grid -->
            <div class="grid grid-cols-3 gap-4">
                <div class="bg-white/10 backdrop-blur border border-white/20 px-5 py-4 min-w-[140px]">
                    <div class="flex items-center justify-between mb-2">
                        <p class="text-xs uppercase tracking-wider text-white/70">Layanan</p>
                        <i class="fas fa-certificate text-white/50"></i>
                    </div>
                    <p class="text-3xl font-bold">120+</p>
                    <p class="text-xs text-white/70 mt-1">Jenis perizinan</p>
                </div>
                
                <div class="bg-white/10 backdrop-blur border border-white/20 px-5 py-4">
                    <div class="flex items-center justify-between mb-2">
                        <p class="text-xs uppercase tracking-wider text-white/70">Kategori</p>
                        <i class="fas fa-layer-group text-white/50"></i>
                    </div>
                    <p class="text-3xl font-bold">15</p>
                    <p class="text-xs text-white/70 mt-1">Sektor usaha</p>
                </div>
                
                <div class="bg-white/10 backdrop-blur border border-white/20 px-5 py-4">
                    <div class="flex items-center justify-between mb-2">
                        <p class="text-xs uppercase tracking-wider text-white/70">Proses</p>
                        <i class="fas fa-bolt text-white/50"></i>
                    </div>
                    <p class="text-3xl font-bold">7-14</p>
                    <p class="text-xs text-white/70 mt-1">Hari kerja</p>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="space-y-1">

    <!-- Search Section -->
    <div id="search-section" class="bg-white dark:bg-gray-800 border-y border-gray-200 dark:border-gray-700">
        <div class="px-4 lg:px-6 py-4 border-b border-gray-100 dark:border-gray-700">
            <h2 class="text-sm font-bold text-gray-900 dark:text-white flex items-center">
                <i class="fas fa-search text-[#0a66c2] mr-2 text-base"></i>
                Pencarian KBLI
            </h2>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Masukkan KBLI atau kata kunci usaha (min. 3 karakter)</p>
        </div>
        
        <div class="px-4 lg:px-6 py-4" x-data="kbliSearch()">
            <div class="relative">
                <input 
                    type="text" 
                    x-model="query"
                    @input.debounce.300ms="search()"
                    @focus="focused = true"
                    @blur="setTimeout(() => focused = false, 150)"
                    placeholder="Cari jenis usaha anda disini.."
                    class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0a66c2] dark:bg-gray-700 dark:text-white text-sm"
                />
                <div x-show="loading" class="absolute right-4 top-3.5">
                    <svg class="animate-spin h-5 w-5 text-[#0a66c2]" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </div>

                <div 
                    x-show="focused && results.length > 0"
                    x-transition
                    class="absolute z-20 mt-2 w-full bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-2xl max-h-80 overflow-y-auto"
                >
                    <template x-for="result in results" :key="result.code">
                        <a 
                            :href="`/client/services/${result.code}/context`"
                            class="block px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-700/50 border-b border-gray-100 dark:border-gray-700 last:border-0 transition-colors"
                        >
                            <div class="flex items-start gap-3">
                                <span class="font-mono text-[#0a66c2] font-semibold text-sm flex-shrink-0" x-text="result.code"></span>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm text-gray-900 dark:text-white font-medium" x-text="result.description"></p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Sektor: <span x-text="result.sector"></span></p>
                                </div>
                            </div>
                        </a>
                    </template>
                </div>
            </div>

            <div class="mt-4 space-y-2">
                <p class="text-sm text-gray-600 dark:text-gray-400 flex items-start gap-2">
                    <i class="fas fa-lightbulb text-[#0a66c2] mt-0.5 flex-shrink-0"></i>
                    <span>Gunakan kata kunci sektor (contoh: "konstruksi", "perdagangan") atau kode KBLI langsung</span>
                </p>
                <p class="text-sm text-gray-600 dark:text-gray-400 flex items-start gap-2">
                    <i class="fas fa-download text-[#0a66c2] mt-0.5 flex-shrink-0"></i>
                    <span>Butuh referensi lengkap? <a href="https://oss.go.id/kbli" target="_blank" class="text-[#0a66c2] font-semibold hover:underline">Unduh katalog resmi KBLI</a></span>
                </p>
            </div>
        </div>
    </div>

    <!-- Cara Penggunaan -->
    <div class="bg-white dark:bg-gray-800 border-y border-gray-200 dark:border-gray-700">
        <div class="px-4 lg:px-6 py-4 border-b border-gray-100 dark:border-gray-700">
            <h2 class="text-sm font-bold text-gray-900 dark:text-white flex items-center">
                <i class="fas fa-route text-[#0a66c2] mr-2 text-base"></i>
                Cara Penggunaan
            </h2>
        </div>
        <div class="px-4 lg:px-6 py-4">
            <ol class="space-y-3">
                <li class="flex items-start gap-3 py-2 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors -mx-2 px-2 rounded">
                    <span class="w-6 h-6 rounded-full bg-[#0a66c2] text-white flex items-center justify-center text-xs font-bold flex-shrink-0">1</span>
                    <span class="text-sm text-gray-700 dark:text-gray-300 pt-0.5">Cari KBLI atau sektor usaha di kolom pencarian</span>
                </li>
                <li class="flex items-start gap-3 py-2 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors -mx-2 px-2 rounded">
                    <span class="w-6 h-6 rounded-full bg-[#0a66c2] text-white flex items-center justify-center text-xs font-bold flex-shrink-0">2</span>
                    <span class="text-sm text-gray-700 dark:text-gray-300 pt-0.5">Isi data konteks bisnis untuk rekomendasi akurat</span>
                </li>
                <li class="flex items-start gap-3 py-2 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors -mx-2 px-2 rounded">
                    <span class="w-6 h-6 rounded-full bg-[#0a66c2] text-white flex items-center justify-center text-xs font-bold flex-shrink-0">3</span>
                    <span class="text-sm text-gray-700 dark:text-gray-300 pt-0.5">Review daftar izin wajib dengan estimasi biaya</span>
                </li>
                <li class="flex items-start gap-3 py-2 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors -mx-2 px-2 rounded">
                    <span class="w-6 h-6 rounded-full bg-[#0a66c2] text-white flex items-center justify-center text-xs font-bold flex-shrink-0">4</span>
                    <span class="text-sm text-gray-700 dark:text-gray-300 pt-0.5">Download atau hubungi konsultan untuk bantuan</span>
                </li>
            </ol>
        </div>
    </div>

    <!-- Support Section -->
    <div id="support-section" class="bg-white dark:bg-gray-800 border-y border-gray-200 dark:border-gray-700">
        <div class="px-4 lg:px-6 py-4 border-b border-gray-100 dark:border-gray-700">
            <h2 class="text-sm font-bold text-gray-900 dark:text-white flex items-center">
                <i class="fas fa-headset text-[#0a66c2] mr-2 text-base"></i>
                Butuh Bantuan?
            </h2>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Tim konsultan siap membantu Anda</p>
        </div>
        <div class="px-4 lg:px-6 py-4">
            <p class="text-sm text-gray-600 dark:text-gray-300 mb-4">
                Dapatkan sesi konsultasi gratis untuk pemilihan KBLI, kewajiban OSS-RBA, hingga pendampingan verifikasi lapangan.
            </p>
            
            <div class="space-y-3">
                <a href="mailto:cs@bizmark.id" class="flex items-center gap-3 px-4 py-3 bg-gray-50 dark:bg-gray-700/50 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors group">
                    <div class="w-10 h-10 rounded-lg bg-[#0a66c2]/10 flex items-center justify-center group-hover:bg-[#0a66c2]/20 transition-colors flex-shrink-0">
                        <i class="fas fa-envelope text-[#0a66c2]"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs text-gray-500 dark:text-gray-400">Email</p>
                        <p class="text-sm font-semibold text-gray-900 dark:text-white">cs@bizmark.id</p>
                    </div>
                    <i class="fas fa-arrow-right text-gray-400 group-hover:text-[#0a66c2] transition-colors"></i>
                </a>
                
                <a href="https://wa.me/62838796028550" target="_blank" class="flex items-center gap-3 px-4 py-3 bg-emerald-50 dark:bg-emerald-900/20 hover:bg-emerald-100 dark:hover:bg-emerald-900/30 rounded-lg transition-colors group">
                    <div class="w-10 h-10 rounded-lg bg-emerald-100 dark:bg-emerald-900/40 flex items-center justify-center group-hover:bg-emerald-200 dark:group-hover:bg-emerald-900/60 transition-colors flex-shrink-0">
                        <i class="fab fa-whatsapp text-emerald-600 dark:text-emerald-400 text-lg"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs text-emerald-600 dark:text-emerald-400">WhatsApp</p>
                        <p class="text-sm font-semibold text-emerald-700 dark:text-emerald-300">+62 838 7960 2855</p>
                    </div>
                    <i class="fas fa-arrow-right text-emerald-400 group-hover:text-emerald-600 transition-colors"></i>
                </a>
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
