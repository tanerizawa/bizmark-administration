@extends('client.layouts.app')

@section('title', 'Katalog Layanan Perizinan')

@section('content')
@php
    $contact = (array) data_get(config('landing_metrics'), 'contact', []);
    $supportEmail = $contact['email'] ?? 'info@bizmark.id';
    $supportWhatsapp = $contact['whatsapp_link'] ?? 'https://wa.me/6283879602855';
    $supportPhone = $contact['phone'] ?? '+62 838 7960 2855';
@endphp

{{-- ============================================================ --}}
{{-- MOBILE HERO HEADER                                           --}}
{{-- ============================================================ --}}
<div class="lg:hidden bg-[#0a66c2] text-white px-4 py-5 border-y border-[#0a66c2]" role="banner">
    <div class="mb-4">
        <p class="text-xs text-white/70 uppercase tracking-wider leading-tight">Katalog Layanan</p>
        <h1 class="text-lg font-bold mt-1 leading-tight">Cari Layanan Perizinan Anda</h1>
        <p class="text-sm text-white/90 mt-1 leading-normal">Temukan izin usaha yang tepat dari {{ number_format($totalKbli) }}+ layanan</p>
    </div>

    {{-- Stats 3-col --}}
    <div class="grid grid-cols-3 gap-2 mb-4" role="list" aria-label="Statistik layanan">
        <div class="bg-white/10 backdrop-blur border border-white/20 px-3 py-2.5 text-center" role="listitem">
            <i class="fas fa-certificate text-white/50 text-xs" aria-hidden="true"></i>
            <p class="text-lg font-bold leading-tight mt-0.5">{{ number_format($totalKbli) }}+</p>
            <p class="text-[10px] text-white/70 leading-tight">Layanan</p>
        </div>
        <div class="bg-white/10 backdrop-blur border border-white/20 px-3 py-2.5 text-center" role="listitem">
            <i class="fas fa-layer-group text-white/50 text-xs" aria-hidden="true"></i>
            <p class="text-lg font-bold leading-tight mt-0.5">{{ $totalSectors }}</p>
            <p class="text-[10px] text-white/70 leading-tight">Sektor</p>
        </div>
        <div class="bg-white/10 backdrop-blur border border-white/20 px-3 py-2.5 text-center" role="listitem">
            <i class="fas fa-bolt text-white/50 text-xs" aria-hidden="true"></i>
            <p class="text-lg font-bold leading-tight mt-0.5">7-14</p>
            <p class="text-[10px] text-white/70 leading-tight">Hari Kerja</p>
        </div>
    </div>

    {{-- Quick Actions --}}
    <div class="grid grid-cols-2 gap-2">
        <a href="#search-section"
           class="flex items-center justify-center gap-2 px-4 py-3 bg-white/10 backdrop-blur border border-white/30 text-white font-semibold rounded-lg min-h-[44px] active:scale-95 transition-all"
           aria-label="Cari kode KBLI">
            <i class="fas fa-search text-sm" aria-hidden="true"></i>
            <span class="text-sm">Cari KBLI</span>
        </a>
        <a href="#support-section"
           class="flex items-center justify-center gap-2 px-4 py-3 bg-white text-[#0a66c2] font-semibold rounded-lg min-h-[44px] active:scale-95 transition-all"
           aria-label="Konsultasi gratis">
            <i class="fas fa-headset text-sm" aria-hidden="true"></i>
            <span class="text-sm">Konsultasi</span>
        </a>
    </div>
</div>

{{-- ============================================================ --}}
{{-- DESKTOP HERO HEADER                                          --}}
{{-- ============================================================ --}}
<div class="hidden lg:block bg-[#0a66c2] border-y border-[#0a66c2] text-white" role="banner">
    <div class="max-w-6xl mx-auto px-6 lg:px-8 py-8">
        <div class="flex items-start justify-between gap-8">
            {{-- Left: Info & CTA --}}
            <div class="flex-1">
                <div class="inline-flex items-center gap-2 bg-white/15 backdrop-blur border border-white/20 px-3 py-1.5 rounded-full text-xs font-semibold mb-3">
                    <i class="fas fa-wand-magic-sparkles text-xs" aria-hidden="true"></i>
                    <span>AI-Powered Recommendation</span>
                </div>

                <h1 class="text-2xl lg:text-3xl font-bold leading-tight mb-3">
                    Temukan Layanan Perizinan yang Tepat untuk Bisnis Anda
                </h1>
                <p class="text-base text-white/90 leading-normal mb-6 max-w-2xl">
                    Sistem cerdas kami menganalisis kebutuhan bisnis Anda dan merekomendasikan izin yang wajib, lengkap dengan estimasi biaya dan waktu proses.
                </p>

                {{-- CTA Buttons --}}
                <div class="flex items-center gap-3">
                    <a href="{{ route('client.applications.index') }}"
                       class="inline-flex items-center gap-2 px-5 py-3 bg-white text-[#0a66c2] font-semibold hover:shadow-lg active:scale-95 transition-all">
                        <i class="fas fa-list" aria-hidden="true"></i>
                        <span>Permohonan Saya</span>
                    </a>
                    <a href="#support-section"
                       class="inline-flex items-center gap-2 px-5 py-3 bg-white/10 backdrop-blur border border-white/30 font-semibold hover:bg-white/20 active:scale-95 transition-all">
                        <i class="fas fa-headset" aria-hidden="true"></i>
                        <span>Konsultasi Gratis</span>
                    </a>
                </div>
            </div>

            {{-- Right: Stats Grid --}}
            <div class="grid grid-cols-3 gap-4 flex-shrink-0" role="list" aria-label="Statistik layanan">
                <div class="bg-white/10 backdrop-blur border border-white/20 px-5 py-4 min-w-[140px]" role="listitem">
                    <div class="flex items-center justify-between mb-2">
                        <p class="text-xs uppercase tracking-wider text-white/70 leading-tight">Layanan</p>
                        <i class="fas fa-certificate text-white/50" aria-hidden="true"></i>
                    </div>
                    <p class="text-3xl font-bold leading-tight">{{ number_format($totalKbli) }}+</p>
                    <p class="text-xs text-white/60 mt-1">Jenis perizinan</p>
                </div>
                <div class="bg-white/10 backdrop-blur border border-white/20 px-5 py-4" role="listitem">
                    <div class="flex items-center justify-between mb-2">
                        <p class="text-xs uppercase tracking-wider text-white/70 leading-tight">Sektor</p>
                        <i class="fas fa-layer-group text-white/50" aria-hidden="true"></i>
                    </div>
                    <p class="text-3xl font-bold leading-tight">{{ $totalSectors }}</p>
                    <p class="text-xs text-white/60 mt-1">Sektor usaha</p>
                </div>
                <div class="bg-white/10 backdrop-blur border border-white/20 px-5 py-4" role="listitem">
                    <div class="flex items-center justify-between mb-2">
                        <p class="text-xs uppercase tracking-wider text-white/70 leading-tight">Proses</p>
                        <i class="fas fa-bolt text-white/50" aria-hidden="true"></i>
                    </div>
                    <p class="text-3xl font-bold leading-tight">7-14</p>
                    <p class="text-xs text-white/60 mt-1">Hari kerja</p>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ============================================================ --}}
{{-- CONTENT CARDS                                                --}}
{{-- ============================================================ --}}
<div class="space-y-1 lg:mt-1">

    {{-- ───── KBLI SEARCH ───── --}}
    <section id="search-section" class="bg-white dark:bg-gray-800 border-y border-gray-200 dark:border-gray-700" aria-label="Pencarian KBLI">
        <div class="p-4 lg:p-5 border-b border-gray-100 dark:border-gray-700">
            <h2 class="text-base lg:text-lg font-semibold text-gray-900 dark:text-white leading-tight flex items-center gap-2">
                <i class="fas fa-search text-[#0a66c2]" aria-hidden="true"></i>
                Pencarian KBLI
            </h2>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5 leading-normal">Masukkan KBLI atau kata kunci usaha (min. 3 karakter)</p>
        </div>

        <div class="px-4 lg:px-5 py-4" x-data="kbliSearch()">
            <div class="relative">
                <div class="relative">
                    <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 dark:text-gray-500 text-sm" aria-hidden="true"></i>
                    <input
                        type="text"
                        x-model="query"
                        @input.debounce.300ms="search()"
                        @focus="focused = true"
                        @click="focused = true"
                        @keydown.escape="focused = false"
                        placeholder="Cari jenis usaha anda disini.. (contoh: konstruksi, perdagangan, 47111)"
                        class="w-full pl-11 pr-12 py-3.5 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0a66c2] focus:border-[#0a66c2] dark:bg-gray-700 dark:text-white text-sm transition-colors"
                        aria-label="Cari KBLI atau jenis usaha"
                        autocomplete="off"
                    />
                    {{-- Loading spinner --}}
                    <div x-show="loading" class="absolute right-4 top-1/2 -translate-y-1/2" x-cloak>
                        <svg class="animate-spin h-5 w-5 text-[#0a66c2]" viewBox="0 0 24 24" aria-hidden="true">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </div>
                    {{-- Clear button --}}
                    <button x-show="query.length > 0 && !loading" @click="query = ''; results = []; noResults = false; errorMsg = ''"
                            class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors"
                            aria-label="Hapus pencarian" x-cloak>
                        <i class="fas fa-times-circle" aria-hidden="true"></i>
                    </button>
                </div>

                {{-- Search Results Dropdown --}}
                <div
                    x-show="focused && (results.length > 0 || noResults || errorMsg)"
                    @click.outside="focused = false"
                    x-transition:enter="transition ease-out duration-150"
                    x-transition:enter-start="opacity-0 -translate-y-1"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    x-transition:leave="transition ease-in duration-100"
                    x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    class="absolute z-20 mt-2 w-full bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-2xl max-h-80 overflow-y-auto"
                    x-cloak
                >
                    {{-- Results count header --}}
                    <template x-if="results.length > 0">
                        <div class="px-4 py-2 bg-gray-50 dark:bg-gray-700/50 border-b border-gray-100 dark:border-gray-700 sticky top-0">
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                <span x-text="results.length"></span> hasil ditemukan
                            </p>
                        </div>
                    </template>

                    {{-- Result items --}}
                    <template x-for="result in results" :key="result.code">
                        <a
                            :href="`/client/services/${result.code}/context`"
                            class="flex items-start gap-3 px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-700/50 active:bg-gray-100 dark:active:bg-gray-700 border-b border-gray-100 dark:border-gray-700 last:border-0 transition-colors"
                        >
                            <span class="flex-shrink-0 inline-flex items-center justify-center w-10 h-10 rounded-lg bg-blue-50 dark:bg-blue-900/30 text-[#0a66c2] dark:text-blue-400">
                                <span class="font-mono text-xs font-bold" x-text="result.code"></span>
                            </span>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm text-gray-900 dark:text-white font-medium leading-tight" x-text="result.description"></p>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5 leading-normal">
                                    Sektor: <span x-text="result.sector" class="capitalize"></span>
                                </p>
                            </div>
                            <i class="fas fa-chevron-right text-gray-300 dark:text-gray-600 text-xs mt-1 flex-shrink-0" aria-hidden="true"></i>
                        </a>
                    </template>

                    {{-- No results state --}}
                    <template x-if="noResults && !errorMsg">
                        <div class="px-4 py-8 text-center">
                            <div class="w-12 h-12 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center mx-auto mb-3">
                                <i class="fas fa-search text-gray-400 dark:text-gray-500" aria-hidden="true"></i>
                            </div>
                            <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tidak ditemukan</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Coba kata kunci lain atau kode KBLI 5 digit</p>
                        </div>
                    </template>

                    {{-- Error state --}}
                    <template x-if="errorMsg">
                        <div class="px-4 py-6 text-center">
                            <div class="w-12 h-12 rounded-full bg-red-50 dark:bg-red-900/20 flex items-center justify-center mx-auto mb-3">
                                <i class="fas fa-exclamation-triangle text-red-500 dark:text-red-400" aria-hidden="true"></i>
                            </div>
                            <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-1" x-text="errorMsg"></p>
                            <button @click="search()" class="text-xs text-[#0a66c2] dark:text-blue-400 font-medium hover:underline mt-1">Coba lagi</button>
                        </div>
                    </template>
                </div>
            </div>

            {{-- Search tips --}}
            <div class="mt-4 space-y-2">
                <p class="text-sm text-gray-600 dark:text-gray-400 flex items-start gap-2">
                    <i class="fas fa-lightbulb text-[#0a66c2] mt-0.5 flex-shrink-0" aria-hidden="true"></i>
                    <span>Gunakan kata kunci sektor (contoh: "konstruksi", "perdagangan") atau kode KBLI 5 digit</span>
                </p>
                <p class="text-sm text-gray-600 dark:text-gray-400 flex items-start gap-2">
                    <i class="fas fa-download text-[#0a66c2] mt-0.5 flex-shrink-0" aria-hidden="true"></i>
                    <span>Butuh referensi? <a href="https://oss.go.id/informasi/kbli-kbli" target="_blank" rel="noopener noreferrer" class="text-[#0a66c2] dark:text-blue-400 font-semibold hover:underline">Katalog KBLI resmi OSS</a></span>
                </p>
            </div>
        </div>
    </section>

    {{-- ───── POPULAR KBLI ───── --}}
    @if($popularKbli->count() > 0)
    <section class="bg-white dark:bg-gray-800 border-y border-gray-200 dark:border-gray-700" aria-label="KBLI populer">
        <div class="p-4 lg:p-5 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
            <div>
                <h2 class="text-base lg:text-lg font-semibold text-gray-900 dark:text-white leading-tight">KBLI Populer</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5 leading-normal">Layanan yang sering dicari klien</p>
            </div>
        </div>
        <div>
            @foreach($popularKbli as $kbli)
            <a href="{{ route('client.services.context', $kbli->code) }}"
               class="flex items-center gap-3 px-4 lg:px-5 py-3.5 border-b border-gray-100 dark:border-gray-700 last:border-b-0 hover:bg-gray-50 dark:hover:bg-gray-700/50 active:bg-gray-100 dark:active:bg-gray-700 transition-colors group">
                <span class="w-10 h-10 rounded-lg bg-blue-50 dark:bg-blue-900/30 flex items-center justify-center flex-shrink-0">
                    <span class="font-mono text-xs font-bold text-[#0a66c2] dark:text-blue-400">{{ $kbli->code }}</span>
                </span>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-gray-900 dark:text-white leading-tight truncate group-hover:text-[#0a66c2] dark:group-hover:text-blue-400 transition-colors">{{ $kbli->description }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5 leading-normal capitalize">Sektor {{ $kbli->sector }}</p>
                </div>
                @if($kbli->cache_hits > 0)
                <span class="text-xs text-gray-400 dark:text-gray-500 flex-shrink-0 hidden sm:block">
                    <i class="fas fa-fire text-orange-400 mr-1" aria-hidden="true"></i>{{ $kbli->cache_hits }}x
                </span>
                @endif
                <i class="fas fa-chevron-right text-gray-300 dark:text-gray-600 text-xs flex-shrink-0" aria-hidden="true"></i>
            </a>
            @endforeach
        </div>
    </section>
    @endif

    {{-- ───── BROWSE BY SECTOR ───── --}}
    @if($sectors->count() > 0)
    <section class="bg-white dark:bg-gray-800 border-y border-gray-200 dark:border-gray-700" aria-label="Jelajahi per sektor" x-data="{ showAll: false }">
        <div class="p-4 lg:p-5 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
            <div>
                <h2 class="text-base lg:text-lg font-semibold text-gray-900 dark:text-white leading-tight">Jelajahi Per Sektor</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5 leading-normal">{{ $totalSectors }} sektor usaha tersedia</p>
            </div>
            @if($sectors->count() > 8)
            <button @click="showAll = !showAll"
                    class="text-sm font-medium text-[#0a66c2] hover:text-[#004182] dark:text-blue-400 dark:hover:text-blue-300 px-3 py-2 min-h-[44px] flex items-center active:scale-95 transition-transform">
                <span x-text="showAll ? 'Lebih sedikit' : 'Lihat semua'"></span>
                <i class="fas fa-chevron-down ml-2 text-xs transition-transform" :class="showAll ? 'rotate-180' : ''" aria-hidden="true"></i>
            </button>
            @endif
        </div>
        <div class="px-4 lg:px-5 py-4">
            <div class="flex flex-wrap gap-2">
                @foreach($sectors as $index => $sector)
                <a href="#search-section"
                   @click="$nextTick(() => { const el = document.querySelector('[x-data=\'kbliSearch()\'] input'); if(el) { el.value = '{{ $sector->sector }}'; el.dispatchEvent(new Event('input')); el.focus(); } })"
                   class="inline-flex items-center gap-2 px-3 py-2 rounded-lg border border-gray-200 dark:border-gray-600 hover:border-[#0a66c2] dark:hover:border-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/20 text-sm text-gray-700 dark:text-gray-300 hover:text-[#0a66c2] dark:hover:text-blue-400 transition-all active:scale-95 {{ $index >= 8 ? '' : '' }}"
                   x-show="{{ $index < 8 ? 'true' : 'showAll' }}"
                   x-transition>
                    <i class="fas fa-tag text-xs text-gray-400 dark:text-gray-500" aria-hidden="true"></i>
                    <span class="capitalize">{{ $sector->sector }}</span>
                    <span class="text-xs text-gray-400 dark:text-gray-500 font-mono">({{ $sector->total_kbli }})</span>
                </a>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    {{-- ───── HOW TO USE ───── --}}
    <section class="bg-white dark:bg-gray-800 border-y border-gray-200 dark:border-gray-700" aria-label="Cara penggunaan">
        <div class="p-4 lg:p-5 border-b border-gray-100 dark:border-gray-700">
            <h2 class="text-base lg:text-lg font-semibold text-gray-900 dark:text-white leading-tight flex items-center gap-2">
                <i class="fas fa-route text-[#0a66c2]" aria-hidden="true"></i>
                Cara Penggunaan
            </h2>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5 leading-normal">4 langkah mudah mendapatkan rekomendasi perizinan</p>
        </div>
        <div class="px-4 lg:px-5 py-4">
            <ol class="space-y-1" role="list">
                <li class="flex items-start gap-3 px-2 py-3 hover:bg-gray-50 dark:hover:bg-gray-700/50 rounded-lg transition-colors" role="listitem">
                    <span class="w-8 h-8 rounded-full bg-[#0a66c2] text-white flex items-center justify-center text-xs font-bold flex-shrink-0" aria-hidden="true">1</span>
                    <div class="flex-1 pt-0.5">
                        <p class="text-sm font-semibold text-gray-900 dark:text-white leading-tight">Cari KBLI</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5 leading-normal">Masukkan kode KBLI atau kata kunci usaha di kolom pencarian</p>
                    </div>
                </li>
                <li class="flex items-start gap-3 px-2 py-3 hover:bg-gray-50 dark:hover:bg-gray-700/50 rounded-lg transition-colors" role="listitem">
                    <span class="w-8 h-8 rounded-full bg-[#0a66c2] text-white flex items-center justify-center text-xs font-bold flex-shrink-0" aria-hidden="true">2</span>
                    <div class="flex-1 pt-0.5">
                        <p class="text-sm font-semibold text-gray-900 dark:text-white leading-tight">Isi Data Konteks</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5 leading-normal">Lengkapi informasi bisnis untuk rekomendasi yang lebih akurat</p>
                    </div>
                </li>
                <li class="flex items-start gap-3 px-2 py-3 hover:bg-gray-50 dark:hover:bg-gray-700/50 rounded-lg transition-colors" role="listitem">
                    <span class="w-8 h-8 rounded-full bg-[#0a66c2] text-white flex items-center justify-center text-xs font-bold flex-shrink-0" aria-hidden="true">3</span>
                    <div class="flex-1 pt-0.5">
                        <p class="text-sm font-semibold text-gray-900 dark:text-white leading-tight">Review Rekomendasi</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5 leading-normal">Lihat daftar izin wajib lengkap dengan estimasi biaya dan waktu</p>
                    </div>
                </li>
                <li class="flex items-start gap-3 px-2 py-3 hover:bg-gray-50 dark:hover:bg-gray-700/50 rounded-lg transition-colors" role="listitem">
                    <span class="w-8 h-8 rounded-full bg-[#0a66c2] text-white flex items-center justify-center text-xs font-bold flex-shrink-0" aria-hidden="true">4</span>
                    <div class="flex-1 pt-0.5">
                        <p class="text-sm font-semibold text-gray-900 dark:text-white leading-tight">Download atau Ajukan</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5 leading-normal">Unduh PDF ringkasan atau langsung ajukan permohonan ke tim kami</p>
                    </div>
                </li>
            </ol>
        </div>
    </section>

    {{-- ───── SUPPORT / CONSULTATION ───── --}}
    <section id="support-section" class="bg-white dark:bg-gray-800 border-y border-gray-200 dark:border-gray-700" aria-label="Bantuan dan konsultasi">
        <div class="p-4 lg:p-5 border-b border-gray-100 dark:border-gray-700">
            <h2 class="text-base lg:text-lg font-semibold text-gray-900 dark:text-white leading-tight flex items-center gap-2">
                <i class="fas fa-headset text-[#0a66c2]" aria-hidden="true"></i>
                Butuh Bantuan?
            </h2>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5 leading-normal">Tim konsultan siap membantu Anda</p>
        </div>
        <div class="px-4 lg:px-5 py-4">
            <p class="text-sm text-gray-600 dark:text-gray-300 mb-4 leading-normal">
                Dapatkan sesi konsultasi gratis untuk pemilihan KBLI, kewajiban OSS-RBA, hingga pendampingan verifikasi lapangan.
            </p>

            <div class="space-y-2">
                <a href="mailto:{{ $supportEmail }}"
                   class="flex items-center gap-3 px-4 py-3 bg-gray-50 dark:bg-gray-700/50 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors group">
                    <span class="w-10 h-10 rounded-lg bg-[#0a66c2]/10 dark:bg-[#0a66c2]/20 flex items-center justify-center group-hover:bg-[#0a66c2]/20 dark:group-hover:bg-[#0a66c2]/30 transition-colors flex-shrink-0">
                        <i class="fas fa-envelope text-[#0a66c2]" aria-hidden="true"></i>
                    </span>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs text-gray-500 dark:text-gray-400 leading-tight">Email</p>
                        <p class="text-sm font-semibold text-gray-900 dark:text-white leading-tight mt-0.5">{{ $supportEmail }}</p>
                    </div>
                    <i class="fas fa-arrow-right text-gray-400 group-hover:text-[#0a66c2] transition-colors text-xs" aria-hidden="true"></i>
                </a>

                <a href="{{ $supportWhatsapp }}" target="_blank" rel="noopener noreferrer"
                   class="flex items-center gap-3 px-4 py-3 bg-emerald-50 dark:bg-emerald-900/20 hover:bg-emerald-100 dark:hover:bg-emerald-900/30 rounded-lg transition-colors group">
                    <span class="w-10 h-10 rounded-lg bg-emerald-100 dark:bg-emerald-900/40 flex items-center justify-center group-hover:bg-emerald-200 dark:group-hover:bg-emerald-900/60 transition-colors flex-shrink-0">
                        <i class="fab fa-whatsapp text-emerald-600 dark:text-emerald-400 text-lg" aria-hidden="true"></i>
                    </span>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs text-emerald-600 dark:text-emerald-400 leading-tight">WhatsApp</p>
                        <p class="text-sm font-semibold text-emerald-700 dark:text-emerald-300 leading-tight mt-0.5">{{ $supportPhone }}</p>
                    </div>
                    <i class="fas fa-arrow-right text-emerald-400 group-hover:text-emerald-600 transition-colors text-xs" aria-hidden="true"></i>
                </a>
            </div>
        </div>
    </section>

</div>

@push('scripts')
<script>
function kbliSearch() {
    return {
        query: '',
        results: [],
        loading: false,
        focused: false,
        noResults: false,
        errorMsg: '',

        async search() {
            this.errorMsg = '';
            this.noResults = false;

            if (this.query.length < 3) {
                this.results = [];
                return;
            }

            this.loading = true;

            try {
                const response = await fetch(`/api/kbli/search?q=${encodeURIComponent(this.query)}`);

                if (!response.ok) {
                    throw new Error(response.status === 429 ? 'Terlalu banyak pencarian. Tunggu sebentar.' : 'Gagal memuat data.');
                }

                const data = await response.json();
                this.results = data.data || [];
                this.noResults = this.results.length === 0;
            } catch (error) {
                console.error('KBLI search error:', error);
                this.results = [];
                this.noResults = false;
                this.errorMsg = error.message || 'Terjadi kesalahan saat mencari. Silakan coba lagi.';
            } finally {
                this.loading = false;
            }
        }
    }
}
</script>
@endpush
@endsection
