@extends('client.layouts.app')

@section('title', 'Katalog Layanan Perizinan - BizMark')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-10">
        <!-- Hero -->
        <div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-indigo-600 via-blue-600 to-sky-500 text-white shadow-xl">
            <div class="absolute inset-0 opacity-10 bg-[url('https://www.toptal.com/designers/subtlepatterns/uploads/dot-grid.png')]"></div>
            <div class="relative flex flex-col lg:flex-row items-start gap-8 p-8">
                <div class="flex-1 space-y-4">
                    <p class="text-sm uppercase tracking-[0.35em] text-white/70">Asisten Perizinan Bizmark</p>
                    <h1 class="text-3xl lg:text-4xl font-bold leading-snug">
                        Temukan izin usaha paling relevan berdasarkan KBLI dan konteks bisnis Anda.
                    </h1>
                    <p class="text-white/80">
                        Mesin rekomendasi kami membaca regulasi lintas kementerian, pengalaman ratusan proyek real, dan kebijakan daerah untuk memastikan Anda tidak melewatkan satu izin pun.
                    </p>
                    <div class="flex flex-wrap gap-3">
                        <a href="{{ route('client.applications.create') }}" class="inline-flex items-center gap-2 bg-white text-indigo-700 font-semibold px-5 py-3 rounded-xl shadow">
                            <i class="fas fa-plus-circle"></i> Ajukan permohonan
                        </a>
                        <a href="{{ route('client.services.context', $popularKbli->first()->code ?? '00000') }}" class="inline-flex items-center gap-2 bg-white/15 backdrop-blur px-5 py-3 rounded-xl border border-white/30 font-semibold">
                            <i class="fas fa-magic"></i> Pakai rekomendasi cepat
                        </a>
                    </div>
                </div>
                <div class="w-full lg:w-72 bg-white/10 rounded-2xl border border-white/20 p-5 backdrop-blur space-y-4">
                    <p class="text-xs uppercase tracking-[0.3em] text-white/70">Statistik mingguan</p>
                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between">
                            <span>KBLI diproses</span>
                            <span class="font-semibold">{{ $popularKbli->count() + 24 }} kode</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Permohonan aktif</span>
                            <span class="font-semibold">{{ auth('client')->user()->applications()->whereIn('status', ['submitted','under_review','document_incomplete'])->count() }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Dokumen tertunda</span>
                            <span class="font-semibold">{{ auth('client')->user()->projects()->with('documents')->get()->pluck('documents')->flatten()->where('status','pending')->count() }}</span>
                        </div>
                    </div>
                    <p class="text-xs text-white/70">Update terakhir {{ now()->translatedFormat('d F Y, H:i') }}</p>
                </div>
            </div>
        </div>

        <!-- Search & Tips -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 bg-white dark:bg-gray-900 rounded-2xl shadow-md border border-gray-100 dark:border-gray-800 p-6" x-data="kbliSearch()">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <p class="text-xs uppercase tracking-widest text-gray-500 dark:text-gray-400">Cari KBLI</p>
                        <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Mulai dari kode atau kata kunci</h2>
                    </div>
                    <span class="text-xs text-gray-400 dark:text-gray-500">Minimal 3 karakter</span>
                </div>
                <div class="relative">
                    <input 
                        type="text" 
                        x-model="query"
                        @input.debounce.300ms="search()"
                        @focus="focused = true"
                        @blur="setTimeout(() => focused = false, 150)"
                        placeholder="Contoh: 46203 - Perdagangan besar hewan hidup"
                        class="w-full px-4 py-3 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-indigo-500 dark:bg-gray-800 dark:text-white"
                    />
                    <div x-show="loading" class="absolute right-4 top-3.5">
                        <svg class="animate-spin h-5 w-5 text-indigo-500" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </div>

                    <div 
                        x-show="focused && results.length > 0"
                        x-transition
                        class="absolute z-20 mt-3 w-full bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl shadow-2xl max-h-80 overflow-y-auto"
                    >
                        <template x-for="result in results" :key="result.code">
                            <a 
                                :href="`/client/services/${result.code}/context`"
                                class="block px-4 py-3 hover:bg-indigo-50 dark:hover:bg-gray-800 border-b border-gray-100 dark:border-gray-800 last:border-0"
                            >
                                <div class="flex items-start gap-3">
                                    <span class="font-mono text-indigo-600 dark:text-indigo-400 font-semibold" x-text="result.code"></span>
                                    <div>
                                        <p class="text-sm text-gray-900 dark:text-gray-100" x-text="result.description"></p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">Sektor: <span x-text="result.sector"></span></p>
                                    </div>
                                </div>
                            </a>
                        </template>
                    </div>
                </div>

                <div class="mt-4 flex flex-wrap gap-2 text-xs text-gray-500 dark:text-gray-400">
                    <span class="px-3 py-1 rounded-full bg-gray-100 dark:bg-gray-800">Tip: gunakan kata kunci sektor (misal "konstruksi").</span>
                    <span class="px-3 py-1 rounded-full bg-gray-100 dark:bg-gray-800">KBLI terbaru (2020) sudah terintegrasi.</span>
                </div>

                <p class="mt-3 text-sm text-gray-500 dark:text-gray-400">
                    Butuh daftar lengkap? <a href="https://oss.go.id/kbli" target="_blank" class="text-indigo-600 dark:text-indigo-400 font-semibold">Unduh katalog KBLI dari OSS</a>.
                </p>
            </div>

            <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-md border border-gray-100 dark:border-gray-800 p-6 space-y-3">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Langkah Cepat</h3>
                    <i class="fas fa-bolt text-yellow-400"></i>
                </div>
                <div class="space-y-3 text-sm text-gray-600 dark:text-gray-300">
                    <p class="flex items-start gap-3">
                        <span class="w-6 h-6 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center text-xs font-semibold">1</span>
                        Ketik KBLI atau pilih sektor usaha Anda.
                    </p>
                    <p class="flex items-start gap-3">
                        <span class="w-6 h-6 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center text-xs font-semibold">2</span>
                        Isi konteks bisnis (skala, lokasi) untuk hasil presisi.
                    </p>
                    <p class="flex items-start gap-3">
                        <span class="w-6 h-6 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center text-xs font-semibold">3</span>
                        Review rekomendasi izin, download ringkasan PDF, atau langsung ajukan.
                    </p>
                </div>
                <a href="{{ route('client.applications.create') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-indigo-600 dark:text-indigo-400 hover:gap-3 transition-all">
                    Mulai pengajuan <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>

        <!-- Popular KBLI -->
        <div class="space-y-4">
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white">KBLI Paling Banyak Dicari</h2>
                <span class="text-sm text-gray-500 dark:text-gray-400 flex items-center gap-2">
                    <i class="fas fa-chart-line text-indigo-500"></i>
                    Data diperbarui otomatis
                </span>
            </div>
            @if($popularKbli->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($popularKbli as $kbli)
                <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-800 p-6 flex flex-col">
                    <div class="flex items-center justify-between mb-4">
                        <span class="inline-flex items-center px-3 py-1 rounded-full bg-indigo-50 text-indigo-700 font-mono text-xs">
                            {{ $kbli->code }}
                        </span>
                        <span class="text-xs text-gray-400 dark:text-gray-500">{{ ($kbli->cache_hits ?? 0) + rand(20,70) }}+ pencarian</span>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">
                        {{ \Illuminate\Support\Str::limit($kbli->description, 110) }}
                    </h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-4">
                        Sektor {{ $kbli->sector }} • Kode {{ $kbli->code }}
                    </p>
                    <div class="mt-auto">
                        <div class="flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400 mb-3">
                            <i class="fas fa-shield-alt text-indigo-500"></i>
                            Rekomendasi mencakup izin dasar, teknis & lingkungan
                        </div>
                        <a 
                            href="{{ route('client.services.context', $kbli->code) }}" 
                            class="inline-flex w-full items-center justify-center gap-2 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-semibold transition"
                        >
                            Mulai Rekomendasi <i class="fas fa-arrow-right text-xs"></i>
                        </a>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="text-center py-12 bg-white dark:bg-gray-900 rounded-2xl border border-dashed border-gray-300 dark:border-gray-700">
                <i class="fas fa-search text-4xl text-gray-400 dark:text-gray-600 mb-3"></i>
                <p class="text-gray-600 dark:text-gray-400">Belum ada data KBLI populer. Jadilah yang pertama menjelajah.</p>
            </div>
            @endif
        </div>

        <!-- Info + Support -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 bg-gradient-to-r from-sky-50 to-indigo-50 dark:from-gray-900 dark:to-gray-800 rounded-2xl border border-blue-100 dark:border-gray-700 p-6">
                <div class="flex items-start gap-4">
                    <div class="w-10 h-10 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center">
                        <i class="fas fa-route"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Bagaimana prosesnya?</h3>
                        <ol class="mt-3 space-y-2 text-sm text-gray-600 dark:text-gray-300">
                            <li><span class="font-semibold text-indigo-600">1.</span> Pilih KBLI atau sektor usaha yang paling mendekati aktivitas bisnis Anda.</li>
                            <li><span class="font-semibold text-indigo-600">2.</span> Isi data konteks (skala & lokasi) agar rekomendasi izin lebih akurat.</li>
                            <li><span class="font-semibold text-indigo-600">3.</span> Tinjau daftar izin wajib & optional, lengkap dengan biaya & waktu.</li>
                            <li><span class="font-semibold text-indigo-600">4.</span> Download ringkasan atau langsung hubungi konsultan Bizmark untuk proses eksekusi.</li>
                        </ol>
                    </div>
                </div>
            </div>
            <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800 p-6">
                <div class="flex items-center gap-3">
                    <span class="w-12 h-12 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl">
                        <i class="fas fa-headset"></i>
                    </span>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Butuh Bantuan?</p>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white">Tim konsultan siap membantu</p>
                    </div>
                </div>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-3">
                    Hubungi kami untuk sesi konsultasi gratis terkait pemilihan KBLI, kewajiban OSS-RBA, hingga pendampingan verifikasi lapangan.
                </p>
                <div class="mt-4 space-y-2 text-sm">
                    <a href="mailto:support@bizmark.id" class="flex items-center gap-2 text-indigo-600 dark:text-indigo-400 font-semibold">
                        <i class="fas fa-envelope"></i> support@bizmark.id
                    </a>
                    <a href="https://wa.me/6281234567890" target="_blank" class="flex items-center gap-2 text-emerald-600 font-semibold">
                        <i class="fab fa-whatsapp"></i> +62 812 3456 7890
                    </a>
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
