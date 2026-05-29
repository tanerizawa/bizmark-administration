{{-- Services Catalog Index — Portal v2 --}}
@php
    $currentSearch  = request('search', '');
    $currentQuery   = request('q', '');
    $initialCatalogItems = $catalogKbli->getCollection()
        ->map(fn ($kbli) => [
            'code' => $kbli->code,
            'description' => $kbli->description,
            'sector' => $kbli->sector,
            'teaser' => $kbli->activities ?: $kbli->category,
            'url' => route('client.services.context', $kbli->code),
        ])
        ->values();
@endphp

{{-- ─── HERO ─── --}}
<section class="portal-hero relative overflow-hidden border-b border-[var(--border-subtle)]"
         style="background: linear-gradient(145deg, color-mix(in oklab, var(--primary-blue, var(--client-primary)) 78%, #06111d) 0%, color-mix(in oklab, var(--client-primary) 58%, #081724) 58%, #081420 100%); color:#fff;"
         aria-label="Katalog Layanan Perizinan">
    <div class="portal-glow-orb portal-glow-orb--tr hidden lg:block" aria-hidden="true"
         style="background: radial-gradient(circle at 50% 50%, rgba(255,255,255,0.18) 0%, transparent 70%);"></div>

    <div class="relative max-w-[1400px] mx-auto px-4 lg:px-8 py-5 lg:py-7">
        <span class="portal-eyebrow" style="background: rgba(255,255,255,0.15); color: rgba(255,255,255,0.9); border-color: rgba(255,255,255,0.2);">
            <i class="fas fa-wand-magic-sparkles text-[9px]" aria-hidden="true"></i>
            AI-Powered Recommendation
        </span>
        <div class="mt-3 flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
            <div class="flex-1">
                <h1 class="text-2xl font-bold text-white leading-tight">Temukan Layanan Perizinan yang Tepat</h1>
                <p class="mt-2 max-w-xl text-sm leading-6 text-white/84">
                    {{ number_format($totalKbli) }}+ jenis izin dari {{ $totalSectors }} sektor. Sistem AI kami mencocokkan izin yang wajib untuk bisnis Anda.
                </p>
            </div>
            <div class="flex items-center gap-3 flex-shrink-0">
                <a href="{{ route('client.applications.index') }}"
                   class="inline-flex min-h-11 items-center justify-center gap-2 rounded-lg border border-white/35 bg-white px-4 py-2.5 text-sm font-semibold leading-none shadow-[0_10px_28px_rgba(4,16,29,0.18)] transition-all active:scale-[0.98] hover:bg-slate-50 whitespace-nowrap"
                   style="background:#ffffff; color:var(--client-primary-hover); border-color:rgba(255,255,255,0.4); box-shadow:0 10px 28px rgba(4,16,29,0.18);">
                    <i class="fas fa-list text-xs" aria-hidden="true" style="color:var(--client-primary-hover);"></i>
                    <span style="color:var(--client-primary-hover);">Permohonan Saya</span>
                </a>
                <a href="#support-section"
                   class="inline-flex items-center gap-2 px-4 py-2.5 bg-white/10 border border-white/25 text-white text-sm font-semibold rounded-lg hover:bg-white/20 transition-colors">
                    <i class="fas fa-headset text-xs" aria-hidden="true"></i> Konsultasi
                </a>
            </div>
        </div>

        {{-- Stat strip --}}
        <div class="mt-4 grid grid-cols-3 gap-3 lg:max-w-2xl">
            <div class="rounded-lg border border-white/15 bg-white/8 px-4 py-2.5 text-center backdrop-blur">
                <p class="text-xl font-bold tabular-nums text-white">{{ number_format($totalKbli) }}+</p>
                <p class="text-[11px] text-white/70 mt-0.5">Jenis Perizinan</p>
            </div>
            <div class="rounded-lg border border-white/15 bg-white/8 px-4 py-2.5 text-center backdrop-blur">
                <p class="text-xl font-bold tabular-nums text-white">{{ $totalSectors }}</p>
                <p class="text-[11px] text-white/70 mt-0.5">Sektor Usaha</p>
            </div>
            <div class="rounded-lg border border-white/15 bg-white/8 px-4 py-2.5 text-center backdrop-blur">
                <p class="text-xl font-bold text-white">7–14</p>
                <p class="text-[11px] text-white/70 mt-0.5">Hari Kerja</p>
            </div>
        </div>
    </div>
</section>

{{-- ─── KBLI SEARCH + CATALOG (Alpine scope wrapper) ─── --}}
<div
    x-data="kbliSearch"
    x-init="() => { query = @js($currentQuery ?: $currentSearch); if (query.trim().length >= 3) search(); }"
>
{{-- ─── KBLI SEARCH BAR ─── --}}
<div id="search-section" class="bg-[var(--surface-elevated)] border-b border-[var(--border-subtle)]">
    <div class="max-w-[1400px] mx-auto px-4 lg:px-8 py-4">
        <div class="rounded-2xl border border-[var(--border-subtle)] bg-[var(--surface-elevated)] p-3 shadow-[0_10px_30px_rgba(15,23,42,0.05)]">
            <div class="mb-2 flex items-center justify-between gap-3 px-1">
                <p class="text-xs font-semibold uppercase tracking-[0.14em] text-[var(--text-tertiary)]">Pencarian katalog</p>
                <span class="hidden sm:inline text-[11px] text-[var(--text-tertiary)]">Cari berdasarkan nama layanan, KBLI, atau aktivitas usaha</span>
            </div>
            <div class="flex flex-col items-stretch gap-3 lg:flex-row lg:items-center">
            <div class="relative flex-1">
                <i class="fas fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-[var(--text-tertiary)] text-xs" aria-hidden="true"></i>
                <input type="text" x-model="query" @input.debounce.300ms="search()"
                       placeholder="Cari nama layanan atau kode KBLI (mis. 47711, Restoran)…"
                       class="w-full rounded-xl border border-[var(--border-subtle)] bg-[var(--surface-cool)] py-3 pl-10 pr-4 text-sm text-[var(--text-primary)] placeholder-[var(--text-tertiary)] focus:ring-2 focus:ring-[var(--action-blue,var(--client-primary))]">
            </div>
            <div class="inline-flex min-h-[48px] items-center justify-center gap-2 rounded-xl border border-[var(--border-subtle)] bg-[var(--surface-cool)] px-4 text-xs font-semibold text-[var(--text-secondary)]">
                <i class="fas" :class="loading ? 'fa-spinner fa-spin' : 'fa-database'" aria-hidden="true"></i>
                <span x-text="loading ? 'Memuat KBLI…' : 'Autofetch KBLI aktif'"></span>
            </div>
            </div>
        </div>
    </div>
</div>

{{-- ─── CATALOG RESULTS ─── --}}
@if(isset($catalogKbli))
<section class="max-w-[1400px] mx-auto px-4 lg:px-8 pb-8">

    {{-- Section header --}}
    <div class="mb-4 flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <h2 class="flex items-center gap-2 text-sm font-bold text-[var(--text-primary)]">
                <i class="fas fa-compass text-[var(--client-primary)] text-xs" aria-hidden="true"></i>
                <span x-show="query.length >= 3">Hasil pencarian katalog</span>
                <span x-show="query.length < 3">Jelajah layanan lengkap</span>
            </h2>
            <p class="mt-1 text-xs text-[var(--text-secondary)]">
                <span x-show="loading">Sedang mengambil layanan terbaru dari database KBLI…</span>
                <span x-show="!loading && query.length >= 3"
                      x-text="results.length > 0 ? `Menampilkan ${results.length} layanan KBLI yang relevan.` : ''"></span>
                <span x-show="!loading && query.length < 3">Menampilkan {{ $catalogKbli->total() }} layanan KBLI.</span>
            </p>
        </div>
        <button type="button" @click="query = ''; results = []; noResults = false;"
                x-show="query.length > 0"
                class="inline-flex items-center gap-2 text-xs font-semibold text-[var(--client-primary)] hover:underline">
            Reset pencarian <i class="fas fa-rotate-left text-[10px]" aria-hidden="true"></i>
        </button>
    </div>

    {{-- Error --}}
    <div x-show="errorMsg"
         class="mb-4 rounded-xl border border-[rgba(255,59,48,0.18)] bg-[rgba(255,59,48,0.06)] px-4 py-3 text-sm text-[var(--apple-red)]"
         x-text="errorMsg"></div>

    {{-- Type-more hint (1–2 chars typed) --}}
    <div x-show="query.length > 0 && query.length < 3"
         class="mb-4 rounded-xl border border-[var(--border-subtle)] bg-[var(--surface-cool)] px-4 py-3 text-sm text-[var(--text-secondary)] text-center">
        <i class="fas fa-keyboard mr-2 text-xs" aria-hidden="true"></i>
        Ketik <span x-text="3 - query.length"></span> karakter lagi untuk mulai mencari…
    </div>

    {{-- ── LIVE RESULTS (Alpine — visible when query ≥ 3) ── --}}
    <div x-show="query.length >= 3" style="display:none">
        {{-- Loading skeleton --}}
        <div x-show="loading" class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-3">
            <template x-for="i in [1,2,3,4,5,6]" :key="i">
                <div class="h-36 animate-pulse rounded-2xl bg-[var(--surface-cool)]"></div>
            </template>
        </div>

        {{-- Results grid --}}
        <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-3"
             x-show="!loading && results.length > 0">
            <template x-for="item in results" :key="item.code">
                <a :href="`/client/services/${item.code}/context`"
                   class="group rounded-2xl border border-[var(--border-subtle)] bg-[var(--surface-elevated)] p-5 transition-all hover:-translate-y-0.5 hover:border-[var(--client-primary)]/35 hover:shadow-[0_16px_40px_rgba(15,23,42,0.08)]">
                    <div class="mb-3 flex items-start justify-between gap-3">
                        <div>
                            <span class="portal-mono text-[11px] text-[var(--client-primary)]" x-text="`KBLI ${item.code}`"></span>
                            <p class="mt-1 text-[10px] uppercase tracking-[0.14em] text-[var(--text-tertiary)]"
                               x-show="item.sector" x-text="item.sector"></p>
                        </div>
                        <span class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-[var(--client-primary-light)] text-[var(--client-primary)] transition-all group-hover:bg-[var(--client-primary)] group-hover:text-white">
                            <i class="fas fa-arrow-right text-xs" aria-hidden="true"></i>
                        </span>
                    </div>
                    <p class="text-base font-semibold leading-snug text-[var(--text-primary)] line-clamp-2"
                       x-text="item.description || `Layanan untuk KBLI ${item.code}`"></p>
                    <p class="mt-2 text-sm leading-6 text-[var(--text-secondary)] line-clamp-3"
                       x-show="item.activities || item.notes"
                       x-text="item.activities || item.notes || ''"></p>
                </a>
            </template>
        </div>

        {{-- No results --}}
        <div class="portal-empty-state rounded-2xl border border-[var(--border-subtle)] bg-[var(--surface-elevated)] px-6 py-10 text-center"
             x-show="!loading && noResults">
            <div class="portal-empty-illustration mx-auto mb-4" aria-hidden="true">
                <span class="portal-empty-illustration__tile"></span>
                <span class="portal-empty-illustration__tile"></span>
                <span class="portal-empty-illustration__node"><i class="fas fa-compass-drafting"></i></span>
            </div>
            <p class="text-sm font-semibold text-[var(--text-primary)]">Belum ada layanan yang cocok</p>
            <p class="mx-auto mt-1 max-w-md text-xs leading-6 text-[var(--text-secondary)]">Coba ganti kata kunci atau gunakan konsultasi gratis agar tim Bizmark membantu memetakan izin yang relevan untuk bisnis Anda.</p>
        </div>
    </div>

    {{-- ── STATIC PHP CATALOG + PAGINATOR (visible when query < 3) ── --}}
    <div x-show="query.length < 3">
        <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-3">
            @foreach($catalogKbli as $kbli)
            @php
                $kTitle  = trim((string) ($kbli->description ?? ''));
                $kTeaser = trim((string) ($kbli->activities ?? $kbli->category ?? ''));
            @endphp
            <a href="{{ route('client.services.context', $kbli->code) }}"
               class="group rounded-2xl border border-[var(--border-subtle)] bg-[var(--surface-elevated)] p-5 transition-all hover:-translate-y-0.5 hover:border-[var(--client-primary)]/35 hover:shadow-[0_16px_40px_rgba(15,23,42,0.08)]">
                <div class="mb-3 flex items-start justify-between gap-3">
                    <div>
                        <span class="portal-mono text-[11px] text-[var(--client-primary)]">KBLI {{ $kbli->code }}</span>
                        @if($kbli->sector)
                        <p class="mt-1 text-[10px] uppercase tracking-[0.14em] text-[var(--text-tertiary)]">{{ $kbli->sector }}</p>
                        @endif
                    </div>
                    <span class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-[var(--client-primary-light)] text-[var(--client-primary)] transition-all group-hover:bg-[var(--client-primary)] group-hover:text-white">
                        <i class="fas fa-arrow-right text-xs" aria-hidden="true"></i>
                    </span>
                </div>
                <p class="text-base font-semibold leading-snug text-[var(--text-primary)] line-clamp-2">
                    {{ $kTitle ?: 'Layanan untuk KBLI ' . $kbli->code }}
                </p>
                @if($kTeaser !== '')
                <p class="mt-2 text-sm leading-6 text-[var(--text-secondary)] line-clamp-3">
                    {{ \Illuminate\Support\Str::limit($kTeaser, 120) }}
                </p>
                @endif
            </a>
            @endforeach
        </div>
        <div class="mt-6">
            {{ $catalogKbli->links() }}
        </div>
    </div>

</section>
@endif
</div>{{-- end Alpine scope wrapper --}}

{{-- ─── AI KBLI MATCHER CTA ─── --}}
<section id="support-section" class="max-w-[1400px] mx-auto px-4 lg:px-8 pb-10">
    <div class="bg-gradient-to-r from-[var(--client-primary)] to-[color-mix(in_oklab,var(--client-primary)_70%,#0d1b2a)] rounded-2xl p-6 lg:p-8 text-white flex flex-col lg:flex-row items-center justify-between gap-6">
        <div>
            <p class="text-xs uppercase tracking-widest text-white/70 font-semibold mb-2">Tidak tahu harus mulai dari mana?</p>
            <h3 class="text-xl font-bold mb-1">Konsultasi Gratis dengan Tim Bizmark</h3>
            <p class="text-sm text-white/80">Ceritakan bisnis Anda — kami rekomendasikan izin yang tepat dalam 1×24 jam.</p>
        </div>
        @php
            $contact = (array) data_get(config('landing_metrics'), 'contact', []);
            $waLink  = $contact['whatsapp_link'] ?? 'https://wa.me/6283879602855';
        @endphp
        <div class="flex items-center gap-3 flex-shrink-0">
            <a href="{{ $waLink }}" target="_blank" rel="noopener"
               class="inline-flex items-center gap-2 px-5 py-3 bg-white text-[var(--client-primary)] font-semibold text-sm rounded-lg hover:shadow-lg transition-shadow">
                <i class="fab fa-whatsapp text-green-600" aria-hidden="true"></i> Chat WhatsApp
            </a>
        </div>
    </div>
</section>
