{{-- Documents Vault — Portal v2 --}}
@php
    $catLabels = [
        'izin_utama'        => 'Izin Utama',
        'dokumen_pendukung' => 'Dokumen Pendukung',
        'laporan'           => 'Laporan',
        'sertifikat'        => 'Sertifikat',
        'lainnya'           => 'Lainnya',
    ];
    $catIcons = [
        'izin_utama'        => 'fa-building-columns',
        'dokumen_pendukung' => 'fa-file-lines',
        'laporan'           => 'fa-chart-bar',
        'sertifikat'        => 'fa-award',
        'lainnya'           => 'fa-folder',
    ];
    $allDocs = $documents instanceof \Illuminate\Pagination\LengthAwarePaginator
        ? $documents->getCollection()
        : collect($documents);
    $byCat = $allDocs->groupBy(fn($d) => $d->vault_category ?? 'lainnya');
    $totalDocs   = $stats['total']    ?? $allDocs->count();
    $expiringCount = $stats['expiring'] ?? 0;
    $expiredCount  = $stats['expired']  ?? 0;
    $currentCat  = request('category', 'all');
    $currentSearch = request('search', '');
@endphp

{{-- ─── HERO ─── --}}
<section class="portal-hero relative overflow-hidden border-b border-[var(--border-subtle)]"
         style="background: linear-gradient(135deg, var(--client-primary) 0%, color-mix(in oklab, var(--client-primary) 65%, #001020) 100%); color:#fff;"
         aria-label="Vault dokumen">
    <div class="portal-glow-orb portal-glow-orb--tr hidden lg:block" aria-hidden="true"
         style="background: radial-gradient(circle at 50% 50%, rgba(255,255,255,0.16) 0%, transparent 70%);"></div>

    <div class="relative max-w-[1400px] mx-auto px-4 lg:px-8 py-5 lg:py-7">
        <span class="portal-eyebrow" style="background: rgba(255,255,255,0.15); color: rgba(255,255,255,0.9); border-color: rgba(255,255,255,0.2);">
            <i class="fas fa-folder-open text-[9px]" aria-hidden="true"></i>
            Vault Dokumen
        </span>
        <h1 class="mt-2 text-2xl font-bold text-white">Semua Dokumen Resmi</h1>
        <p class="mt-1 text-sm text-white/80">Berkas proyek tersimpan aman — cari, filter, dan unduh kapan saja.</p>

        <div class="portal-stat-strip grid grid-cols-3 gap-3 mt-5">
            <div class="bg-white/10 backdrop-blur border border-white/15 rounded-lg px-4 py-3">
                <p class="text-[11px] uppercase tracking-wider text-white/70 font-semibold mb-1">Total Dokumen</p>
                <p class="text-2xl font-bold tabular-nums text-white">{{ $totalDocs }}</p>
                <p class="text-[11px] text-white/60 mt-0.5">Semua kategori</p>
            </div>
            <div class="bg-amber-500/20 border border-amber-400/30 rounded-lg px-4 py-3">
                <p class="text-[11px] uppercase tracking-wider text-amber-200/80 font-semibold mb-1">Segera Berakhir</p>
                <p class="text-2xl font-bold tabular-nums text-amber-200">{{ $expiringCount }}</p>
                <p class="text-[11px] text-amber-200/60 mt-0.5">Dalam 30 hari</p>
            </div>
            <div class="bg-red-500/20 border border-red-400/30 rounded-lg px-4 py-3">
                <p class="text-[11px] uppercase tracking-wider text-red-200/80 font-semibold mb-1">Sudah Berakhir</p>
                <p class="text-2xl font-bold tabular-nums text-red-200">{{ $expiredCount }}</p>
                <p class="text-[11px] text-red-200/60 mt-0.5">Perlu diperbaharui</p>
            </div>
        </div>
    </div>
</section>

{{-- ─── FILTER BAR ─── --}}
<div class="sticky top-0 z-20 bg-[var(--surface-elevated)]/95 backdrop-blur border-b border-[var(--border-subtle)]"
     x-data="{
         query: @js($currentSearch),
         cat: @js($currentCat),
         go() {
             const u = new URL(window.location.href);
             this.query ? u.searchParams.set('search', this.query) : u.searchParams.delete('search');
             this.cat !== 'all' ? u.searchParams.set('category', this.cat) : u.searchParams.delete('category');
             u.searchParams.delete('page');
             window.location.href = u.toString();
         }
     }">
    <div class="max-w-[1400px] mx-auto px-4 lg:px-8 py-3 flex flex-wrap items-center gap-2">
        <div class="relative flex-1 min-w-[180px]">
            <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-[var(--text-tertiary)] text-xs" aria-hidden="true"></i>
            <input type="text" x-model="query" @keydown.enter.prevent="go()"
                   placeholder="Cari dokumen…"
                   class="w-full pl-9 pr-4 py-2 text-sm bg-[var(--surface-cool)] border border-[var(--border-subtle)] rounded-lg focus:ring-2 focus:ring-[var(--client-primary)] text-[var(--text-primary)] placeholder-[var(--text-tertiary)]">
        </div>
        <div class="flex items-center gap-1.5 flex-wrap">
            @foreach(array_merge(['all' => 'Semua'], $catLabels) as $key => $label)
            <button type="button" @click="cat = @js($key); go()"
                    :class="cat === @js($key) ? 'bg-[var(--client-primary)] text-white border-[var(--client-primary)]' : 'bg-[var(--surface-cool)] text-[var(--text-secondary)] border-[var(--border-subtle)]'"
                    class="px-3 py-1.5 text-xs font-semibold border rounded-full transition-colors">
                {{ $label }}
            </button>
            @endforeach
        </div>
    </div>
</div>

{{-- ─── CATEGORIES ─── --}}
<div class="max-w-[1400px] mx-auto px-4 lg:px-8 py-6 space-y-8">
    @if($allDocs->isEmpty())
    <x-ui.empty-state icon="fas fa-folder-open" title="Vault kosong"
        description="Belum ada dokumen tersimpan. Upload berkas perizinan dari halaman Dokumen."
        :action="['label' => 'Lihat Dokumen', 'url' => route('client.documents.index')]" />
    @else
    @foreach(($catLabels + ['lainnya' => 'Lainnya']) as $catKey => $catName)
    @php $docsInCat = $byCat->get($catKey, collect()); @endphp
    @if($docsInCat->isNotEmpty())
    <section aria-label="{{ $catName }}">
        <div class="flex items-center gap-2.5 mb-4">
            <div class="w-8 h-8 rounded-lg bg-[var(--surface-cool)] border border-[var(--border-subtle)] flex items-center justify-center">
                <i class="fas {{ $catIcons[$catKey] ?? 'fa-folder' }} text-[var(--client-primary)] text-sm" aria-hidden="true"></i>
            </div>
            <div>
                <h2 class="text-base font-bold text-[var(--text-primary)]">{{ $catName }}</h2>
                <p class="text-xs text-[var(--text-tertiary)]">{{ $docsInCat->count() }} dokumen</p>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($docsInCat as $doc)
            @php
                $isExpired  = $doc->expiry_date && now()->gt($doc->expiry_date);
                $isExpiring = !$isExpired && $doc->expiry_date && now()->diffInDays($doc->expiry_date, false) <= 30;
                $fileIcon   = str_contains($doc->mime_type ?? '', 'pdf') ? 'fa-file-pdf text-red-500'
                            : (str_contains($doc->mime_type ?? '', 'image') ? 'fa-file-image text-blue-500'
                            : (str_contains($doc->mime_type ?? '', 'word') ? 'fa-file-word text-blue-700' : 'fa-file text-[var(--text-tertiary)]'));
            @endphp
            <article class="group bg-[var(--surface-elevated)] border @if($isExpired) border-red-300 dark:border-red-800/60 @elseif($isExpiring) border-amber-300 dark:border-amber-700/60 @else border-[var(--border-subtle)] @endif rounded-xl p-4 transition-shadow hover:shadow-md">
                <div class="flex items-start justify-between gap-3 mb-3">
                    <i class="fas {{ $fileIcon }} text-xl flex-shrink-0 mt-0.5" aria-hidden="true"></i>
                    @if($isExpired)
                    <span class="text-[10px] font-bold px-2 py-0.5 bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400 rounded-full">EXPIRED</span>
                    @elseif($isExpiring)
                    <span class="text-[10px] font-bold px-2 py-0.5 bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400 rounded-full">SEGERA BERAKHIR</span>
                    @endif
                </div>
                <p class="text-sm font-semibold text-[var(--text-primary)] truncate mb-1">
                    {{ $doc->document_name ?? $doc->file_name ?? 'Dokumen' }}
                </p>
                <p class="text-xs text-[var(--text-tertiary)] mb-3">{{ $doc->created_at->format('d M Y') }}</p>
                @if($doc->expiry_date)
                <p class="text-xs {{ $isExpired ? 'text-red-500' : ($isExpiring ? 'text-amber-500' : 'text-[var(--text-tertiary)]') }} mb-3">
                    <i class="fas fa-calendar-xmark text-[9px]" aria-hidden="true"></i>
                    Berakhir {{ $doc->expiry_date->format('d M Y') }}
                </p>
                @endif
                @if($doc->file_path)
                <a href="{{ Storage::url($doc->file_path) }}" target="_blank" rel="noopener"
                   class="inline-flex items-center gap-1.5 text-xs font-semibold text-[var(--client-primary)] hover:underline opacity-0 group-hover:opacity-100 transition-opacity">
                    <i class="fas fa-download text-[10px]" aria-hidden="true"></i> Unduh
                </a>
                @endif
            </article>
            @endforeach
        </div>
    </section>
    @endif
    @endforeach
    @endif
</div>
