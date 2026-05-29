{{-- Compliance Monitor Index — Portal v2 --}}
@php
    $expiringCount = $monitors->where('status', 'expiring_soon')->count();
    $expiredCount  = $monitors->where('status', 'expired')->count();
    $activeCount   = $monitors->where('status', 'active')->count();
    $renewedCount  = $monitors->where('status', 'renewed')->count();
@endphp

{{-- ─── HERO ─── --}}
<section class="portal-hero relative overflow-hidden border-b border-[var(--border-subtle)]"
         aria-label="Compliance Monitor"
         style="background: linear-gradient(135deg, #1e3a5f 0%, #0f2340 100%); color:#fff;">
    <div class="portal-glow-orb portal-glow-orb--tr hidden lg:block" aria-hidden="true"
         style="background: radial-gradient(circle at 50% 50%, rgba(99,102,241,0.25) 0%, transparent 70%);"></div>

    <div class="relative max-w-[1400px] mx-auto px-4 lg:px-8 py-5 lg:py-7">
        <div class="flex items-start justify-between gap-4 mb-5">
            <div>
                <span class="portal-eyebrow" style="background: rgba(255,255,255,0.12); color: rgba(255,255,255,0.9); border-color: rgba(255,255,255,0.2);">
                    <i class="fas fa-shield-halved text-[9px]" aria-hidden="true"></i>
                    Compliance Monitor
                </span>
                <h1 class="mt-2 text-2xl font-bold text-white">Pantau Masa Berlaku Izin</h1>
                <p class="mt-1 text-sm text-white/80">Kami mengingatkan Anda sebelum izin penting berakhir.</p>
            </div>
            <div class="flex items-center gap-2 flex-shrink-0">
                <a href="{{ route('client.compliance-monitor.export') }}"
                   class="inline-flex items-center gap-1.5 px-3 py-2 bg-white/10 hover:bg-white/20 border border-white/20 rounded-lg text-xs font-semibold transition-colors">
                    <i class="fas fa-download text-[10px]" aria-hidden="true"></i>
                    <span class="hidden sm:inline">Export CSV</span>
                </a>
                @if(isset($pushSubscribed) && !$pushSubscribed)
                <button type="button" id="btn-push-subscribe" onclick="subscribePush()"
                        class="inline-flex items-center gap-1.5 px-3 py-2 bg-indigo-500/80 hover:bg-indigo-500 border border-indigo-400/30 rounded-lg text-xs font-semibold transition-colors">
                    <i class="fas fa-bell text-[10px]" aria-hidden="true"></i>
                    <span class="hidden sm:inline">Aktifkan Notifikasi</span>
                </button>
                @endif
            </div>
        </div>

        {{-- Stats --}}
        <div class="portal-stat-strip grid grid-cols-2 lg:grid-cols-4 gap-3">
            <div class="bg-green-400/20 border border-green-300/30 rounded-lg px-4 py-3">
                <p class="text-[11px] uppercase tracking-wider text-green-200/80 font-semibold mb-1">Aktif</p>
                <p class="text-2xl font-bold tabular-nums text-green-200">{{ $activeCount }}</p>
            </div>
            <div class="bg-amber-400/20 border border-amber-300/30 rounded-lg px-4 py-3">
                <div class="flex items-center justify-between mb-1">
                    <p class="text-[11px] uppercase tracking-wider text-amber-200/80 font-semibold">Segera Berakhir</p>
                    @if($expiringCount > 0)<i class="fas fa-triangle-exclamation text-amber-300 text-xs" aria-hidden="true"></i>@endif
                </div>
                <p class="text-2xl font-bold tabular-nums text-amber-200">{{ $expiringCount }}</p>
            </div>
            <div class="bg-red-400/20 border border-red-300/30 rounded-lg px-4 py-3">
                <div class="flex items-center justify-between mb-1">
                    <p class="text-[11px] uppercase tracking-wider text-red-200/80 font-semibold">Expired</p>
                    @if($expiredCount > 0)<i class="fas fa-circle-xmark text-red-300 text-xs" aria-hidden="true"></i>@endif
                </div>
                <p class="text-2xl font-bold tabular-nums text-red-200">{{ $expiredCount }}</p>
            </div>
            <div class="bg-white/10 border border-white/15 rounded-lg px-4 py-3">
                <p class="text-[11px] uppercase tracking-wider text-white/70 font-semibold mb-1">Diperbarui</p>
                <p class="text-2xl font-bold tabular-nums text-white">{{ $renewedCount }}</p>
            </div>
        </div>
    </div>
</section>

{{-- ─── HEATMAP 12 BULAN ─── --}}
@if($monitors->isNotEmpty())
@php
    // Build month buckets for the next 12 months (current month + 11 forward)
    $heatmapMonths = collect();
    for ($i = 0; $i < 12; $i++) {
        $month = now()->startOfMonth()->addMonths($i);
        $key   = $month->format('Y-m');

        $countActive  = $monitors->filter(fn($m) => $m->expires_at && $m->expires_at->format('Y-m') === $key && $m->status === 'active')->count();
        $countExpiring= $monitors->filter(fn($m) => $m->expires_at && $m->expires_at->format('Y-m') === $key && $m->status === 'expiring_soon')->count();
        $countExpired = $monitors->filter(fn($m) => $m->expires_at && $m->expires_at->format('Y-m') === $key && $m->status === 'expired')->count();
        $total        = $countActive + $countExpiring + $countExpired;

        $heatmapMonths->push([
            'key'           => $key,
            'label'         => $month->locale('id')->isoFormat('MMM'),
            'year'          => $month->format('Y'),
            'total'         => $total,
            'active'        => $countActive,
            'expiring'      => $countExpiring,
            'expired'       => $countExpired,
            'isCurrentMonth'=> $month->format('Y-m') === now()->format('Y-m'),
        ]);
    }

    // Also count permits expired in past 3 months (already expired)
    $pastMonths = collect();
    for ($i = -3; $i < 0; $i++) {
        $month = now()->startOfMonth()->addMonths($i);
        $key   = $month->format('Y-m');
        $countExpired = $monitors->filter(fn($m) => $m->expires_at && $m->expires_at->format('Y-m') === $key)->count();
        $pastMonths->push([
            'key'    => $key,
            'label'  => $month->locale('id')->isoFormat('MMM'),
            'year'   => $month->format('Y'),
            'total'  => $countExpired,
            'status' => 'past',
        ]);
    }
@endphp
<div class="border-b border-[var(--border-subtle)]" style="background: var(--surface-elevated);">
    <div class="max-w-[1400px] mx-auto px-4 lg:px-8 py-5">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-sm font-semibold text-[var(--text-primary)] flex items-center gap-2">
                <i class="fas fa-calendar-days text-[var(--client-primary)] text-xs" aria-hidden="true"></i>
                Kalender Kedaluwarsa — 12 Bulan ke Depan
            </h2>
            <div class="flex items-center gap-3 text-[10px] text-[var(--text-tertiary)]">
                <span class="inline-flex items-center gap-1">
                    <span class="w-2.5 h-2.5 rounded-sm inline-block" style="background: rgba(16,185,129,0.6);"></span> Aktif
                </span>
                <span class="inline-flex items-center gap-1">
                    <span class="w-2.5 h-2.5 rounded-sm inline-block" style="background: rgba(245,158,11,0.7);"></span> Segera Berakhir
                </span>
                <span class="inline-flex items-center gap-1">
                    <span class="w-2.5 h-2.5 rounded-sm inline-block" style="background: rgba(239,68,68,0.7);"></span> Expired
                </span>
            </div>
        </div>

        {{-- 12-month grid --}}
        <div class="grid grid-cols-4 sm:grid-cols-6 lg:grid-cols-12 gap-2" role="list" aria-label="Kalender kedaluwarsa 12 bulan">
            @foreach($heatmapMonths as $month)
            @php
                // Determine cell color based on urgency
                if ($month['expired'] > 0) {
                    $cellBg     = 'rgba(239,68,68,' . min(0.9, 0.35 + $month['expired'] * 0.15) . ')';
                    $textColor  = '#fca5a5';
                    $borderColor= 'rgba(239,68,68,0.5)';
                    $iconClass  = 'fas fa-circle-xmark';
                } elseif ($month['expiring'] > 0) {
                    $cellBg     = 'rgba(245,158,11,' . min(0.9, 0.35 + $month['expiring'] * 0.15) . ')';
                    $textColor  = '#fde68a';
                    $borderColor= 'rgba(245,158,11,0.5)';
                    $iconClass  = 'fas fa-triangle-exclamation';
                } elseif ($month['active'] > 0) {
                    $cellBg     = 'rgba(16,185,129,' . min(0.9, 0.25 + $month['active'] * 0.12) . ')';
                    $textColor  = '#6ee7b7';
                    $borderColor= 'rgba(16,185,129,0.4)';
                    $iconClass  = 'fas fa-circle-check';
                } else {
                    $cellBg     = 'var(--surface-cool)';
                    $textColor  = 'var(--text-tertiary)';
                    $borderColor= 'var(--border-subtle)';
                    $iconClass  = null;
                }
            @endphp
            <div role="listitem"
                 class="relative flex flex-col items-center justify-center rounded-lg p-2 min-h-[60px] border text-center transition-transform hover:scale-105 cursor-default"
                 style="background: {{ $cellBg }}; border-color: {{ $borderColor }}; {{ $month['isCurrentMonth'] ? 'box-shadow: 0 0 0 2px var(--client-primary);' : '' }}"
                 title="{{ $month['label'] }} {{ $month['year'] }}: {{ $month['total'] }} izin">

                @if($month['isCurrentMonth'])
                <span class="absolute -top-1.5 left-1/2 -translate-x-1/2 px-1 rounded text-[8px] font-bold text-white"
                      style="background: var(--client-primary);">Bulan ini</span>
                @endif

                <p class="text-[10px] font-bold uppercase tracking-wide" style="color: {{ $textColor }};">{{ $month['label'] }}</p>
                <p class="text-[9px] opacity-60 mt-0.5" style="color: {{ $textColor }};">{{ $month['year'] }}</p>

                @if($month['total'] > 0)
                <p class="text-lg font-bold leading-none mt-1" style="color: {{ $textColor }};">{{ $month['total'] }}</p>
                <p class="text-[8px] opacity-80" style="color: {{ $textColor }};">izin</p>
                @else
                <p class="text-[11px] mt-1 opacity-40" style="color: {{ $textColor }};">—</p>
                @endif
            </div>
            @endforeach
        </div>

        @if($monitors->whereNotNull('expires_at')->isEmpty())
        <p class="text-xs text-center text-[var(--text-tertiary)] mt-3">Tidak ada data tanggal kedaluwarsa yang tersedia.</p>
        @endif
    </div>
</div>
@endif

{{-- ─── MAIN ─── --}}
<div class="max-w-[1400px] mx-auto px-4 lg:px-8 py-6"
     x-data="{ filter: 'all' }">

    {{-- Push notification toast --}}
    <div x-data="{show: false}" x-show="show" x-transition
         x-init="document.addEventListener('push-subscribed', () => { show=true; setTimeout(()=>show=false,4000) })"
         class="mb-4 flex items-center gap-2 px-4 py-2.5 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-xl text-xs text-green-700 dark:text-green-400"
         style="display:none">
        <i class="fas fa-bell text-green-500" aria-hidden="true"></i> Notifikasi izin aktif. Anda akan diberi tahu sebelum izin kedaluwarsa.
    </div>

    {{-- Filter chips --}}
    @if($monitors->isNotEmpty())
    <div class="flex items-center gap-1.5 flex-wrap mb-5" role="group" aria-label="Filter status">
        @foreach([
            ['key'=>'all',           'label'=>'Semua',            'count'=>$monitors->count(), 'cls'=>'bg-[var(--client-primary)] text-white border-[var(--client-primary)]', 'off'=>'bg-[var(--surface-cool)] text-[var(--text-secondary)] border-[var(--border-subtle)]'],
            ['key'=>'active',        'label'=>'Aktif',            'count'=>$activeCount,    'cls'=>'bg-green-600 text-white border-green-600',   'off'=>'bg-[var(--surface-cool)] text-[var(--text-secondary)] border-[var(--border-subtle)] hover:border-green-500'],
            ['key'=>'expiring_soon', 'label'=>'Mendekati Berakhir','count'=>$expiringCount, 'cls'=>'bg-amber-500 text-white border-amber-500',  'off'=>'bg-[var(--surface-cool)] text-[var(--text-secondary)] border-[var(--border-subtle)] hover:border-amber-400'],
            ['key'=>'expired',       'label'=>'Expired',          'count'=>$expiredCount,   'cls'=>'bg-red-500 text-white border-red-500',       'off'=>'bg-[var(--surface-cool)] text-[var(--text-secondary)] border-[var(--border-subtle)] hover:border-red-400'],
        ] as $chip)
        <button type="button" @click="filter = @js($chip['key'])"
                :class="filter === @js($chip['key']) ? @js($chip['cls']) : @js($chip['off'])"
                class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold border rounded-full transition-colors">
            {{ $chip['label'] }}
            @if($chip['count'] > 0)
            <span class="text-[10px] font-bold opacity-75">{{ $chip['count'] }}</span>
            @endif
        </button>
        @endforeach
    </div>
    @endif

    @if($monitors->isEmpty())
    <x-ui.empty-state icon="fas fa-shield-check" title="Belum ada izin dipantau"
        description="Hubungi tim Bizmark untuk mengaktifkan pemantauan masa berlaku izin Anda." />
    @else
    <div class="space-y-4">
        @foreach($monitors as $monitor)
        @php
            $days  = method_exists($monitor, 'daysUntilExpiry') ? $monitor->daysUntilExpiry() : null;
            $pct   = method_exists($monitor, 'progressPercent') ? $monitor->progressPercent() : 0;
            $badgeColor = match($monitor->status) {
                'expiring_soon' => 'text-amber-600 dark:text-amber-400 bg-amber-50 dark:bg-amber-900/20 border-amber-200 dark:border-amber-700/50',
                'expired'       => 'text-red-600 dark:text-red-400 bg-red-50 dark:bg-red-900/20 border-red-200 dark:border-red-700/50',
                'renewed'       => 'text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-900/20 border-indigo-200 dark:border-indigo-700/50',
                default         => 'text-green-600 dark:text-green-400 bg-green-50 dark:bg-green-900/20 border-green-200 dark:border-green-700/50',
            };
            $barColor = match($monitor->status) {
                'expiring_soon' => 'bg-amber-500',
                'expired'       => 'bg-red-500',
                'renewed'       => 'bg-indigo-500',
                default         => 'bg-green-500',
            };
            $statusLabel = match($monitor->status) {
                'active'        => 'Aktif',
                'expiring_soon' => 'Segera Berakhir',
                'expired'       => 'Expired',
                'renewed'       => 'Diperbarui',
                default         => ucfirst($monitor->status),
            };
        @endphp
        <article class="bg-[var(--surface-elevated)] border @if($monitor->status === 'expired') border-red-200 dark:border-red-800/50 @elseif($monitor->status === 'expiring_soon') border-amber-200 dark:border-amber-700/50 @else border-[var(--border-subtle)] @endif rounded-xl p-4 transition-shadow hover:shadow-sm"
                 x-show="filter === 'all' || filter === '{{ $monitor->status }}'">
            <div class="flex items-start justify-between gap-4">
                <div class="min-w-0 flex-1">
                    <div class="flex items-center gap-2 flex-wrap mb-1">
                        <h3 class="text-sm font-bold text-[var(--text-primary)]">{{ $monitor->permit_type }}</h3>
                        <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full border {{ $badgeColor }}">
                            {{ $statusLabel }}
                        </span>
                        @if($monitor->status === 'expiring_soon' && $days !== null)
                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-300">
                            <i class="fas fa-hourglass-half text-[8px]" aria-hidden="true"></i>
                            @if($days <= 7) Kritis: {{ $days }}h @elseif($days <= 14) Waspada: {{ $days }}h @else {{ $days }}h @endif lagi
                        </span>
                        @endif
                    </div>
                    @if($monitor->permit_number)
                    <p class="text-xs text-[var(--text-tertiary)]">No: {{ $monitor->permit_number }}</p>
                    @endif
                    @if($monitor->project)
                    <p class="text-xs text-[var(--text-tertiary)] mt-0.5">
                        <i class="fas fa-folder text-[9px]" aria-hidden="true"></i> {{ $monitor->project->name }}
                    </p>
                    @endif
                </div>
                <div class="text-right flex-shrink-0">
                    @if($monitor->expiry_date)
                    <p class="text-xs font-semibold text-[var(--text-primary)]">
                        {{ \Carbon\Carbon::parse($monitor->expiry_date)->format('d M Y') }}
                    </p>
                    <p class="text-[10px] text-[var(--text-tertiary)]">Tanggal berakhir</p>
                    @endif
                </div>
            </div>

            @if($pct > 0)
            <div class="mt-3">
                <div class="flex items-center justify-between text-[10px] text-[var(--text-tertiary)] mb-1">
                    <span>Masa berlaku</span>
                    <span>{{ $pct }}%</span>
                </div>
                <div class="h-1.5 bg-[var(--surface-cool)] rounded-full overflow-hidden">
                    <div class="{{ $barColor }} h-full rounded-full transition-all duration-500" style="width:{{ $pct }}%"></div>
                </div>
            </div>
            @endif

            @if($monitor->status === 'expired')
            <div class="mt-3 flex items-center gap-2">
                <a href="{{ route('client.applications.create') }}"
                   class="inline-flex items-center gap-1.5 text-xs font-semibold px-3 py-1.5 bg-[var(--client-primary)] text-white rounded-lg hover:brightness-110 transition-all">
                    <i class="fas fa-rotate text-[9px]" aria-hidden="true"></i> Perpanjang Izin
                </a>
            </div>
            @endif
        </article>
        @endforeach
    </div>
    @endif
</div>
