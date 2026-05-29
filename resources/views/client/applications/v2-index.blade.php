{{-- Applications List — Portal v2 (high-tech business platform aesthetic) --}}
@php
    $totalApplications = $applications->total();
    $actionNeeded     = ($statusCounts['document_incomplete'] ?? 0) + ($statusCounts['payment_pending'] ?? 0);
    $waitingResponse  = ($statusCounts['quoted'] ?? 0);
    $completed        = ($statusCounts['completed'] ?? 0);
    $activeProcessing = ($statusCounts['submitted'] ?? 0) + ($statusCounts['under_review'] ?? 0)
                      + ($statusCounts['in_progress'] ?? 0) + ($statusCounts['payment_verified'] ?? 0);

    // Saved views (filter chips)
    $filterChips = [
        ''                    => ['label' => 'Semua',         'icon' => 'fa-layer-group'],
        'document_incomplete' => ['label' => 'Perlu Dok',     'icon' => 'fa-circle-exclamation'],
        'payment_pending'     => ['label' => 'Bayar',         'icon' => 'fa-credit-card'],
        'quoted'              => ['label' => 'Penawaran',     'icon' => 'fa-file-invoice-dollar'],
        'submitted'           => ['label' => 'Diajukan',      'icon' => 'fa-paper-plane'],
        'under_review'        => ['label' => 'Review',        'icon' => 'fa-magnifying-glass-chart'],
        'in_progress'         => ['label' => 'Proses',        'icon' => 'fa-spinner'],
        'completed'           => ['label' => 'Selesai',       'icon' => 'fa-check-circle'],
    ];
    $currentStatus = request('status', '');
    $currentSearch = request('search', '');
@endphp

{{-- ─── HERO STRIP ─── --}}
<section class="portal-hero relative overflow-hidden border-b border-[var(--border-subtle)]"
         style="background: linear-gradient(135deg, var(--client-primary) 0%, color-mix(in oklab, var(--client-primary) 80%, #003a6b) 100%); color:#fff;"
         aria-label="Manajemen permohonan">

    {{-- Glow orb desktop only --}}
    <div class="portal-glow-orb portal-glow-orb--tr hidden lg:block" aria-hidden="true"
         style="background: radial-gradient(circle at 50% 50%, rgba(255,255,255,0.18) 0%, transparent 70%); --tr-x: 100px; --tr-y: -100px;"></div>

    {{-- Archipelago watermark --}}
    <svg class="portal-archipelago absolute inset-y-0 right-0 h-full opacity-[0.06] pointer-events-none hidden lg:block"
         viewBox="0 0 800 300" preserveAspectRatio="xMidYMid slice" aria-hidden="true">
        <path fill="currentColor" d="M0,150 Q100,80 200,140 T400,150 T600,135 T800,150 V300 H0 Z"/>
    </svg>

    <div class="relative max-w-[1400px] mx-auto px-4 lg:px-8 py-6 lg:py-9">
        <div class="flex items-start justify-between gap-6 mb-6">
            <div class="min-w-0">
                <span class="portal-eyebrow" style="background: rgba(255,255,255,0.15); color: rgba(255,255,255,0.9); border-color: rgba(255,255,255,0.2);">
                    <i class="fas fa-folder-tree text-[9px]" aria-hidden="true"></i>
                    Manajemen Permohonan
                </span>
                <h1 class="mt-2 text-2xl lg:text-[28px] font-bold leading-tight">
                    {{ $totalApplications }} Permohonan
                    <span class="text-white/70 font-normal">terdaftar</span>
                </h1>
                <p class="mt-1.5 text-sm text-white/80 leading-relaxed max-w-xl">
                    Pantau progres, lengkapi dokumen, dan kelola pembayaran dalam satu tempat.
                </p>
            </div>

            <div class="hidden lg:flex gap-2 flex-shrink-0">
                <a href="{{ route('client.services.index') }}"
                   class="inline-flex items-center gap-2 bg-white text-[var(--client-primary)] font-semibold text-sm px-4 py-2.5 rounded-lg shadow-sm hover:shadow-md transition-shadow">
                    <i class="fas fa-plus text-xs" aria-hidden="true"></i> Ajukan Izin Baru
                </a>
                <a href="{{ route('client.documents.index') }}"
                   class="inline-flex items-center gap-2 bg-white/10 hover:bg-white/15 backdrop-blur border border-white/20 text-white font-semibold text-sm px-4 py-2.5 rounded-lg transition-colors">
                    <i class="fas fa-paperclip text-xs" aria-hidden="true"></i> Kelola Dokumen
                </a>
            </div>
        </div>

        {{-- Stat strip --}}
        <div class="portal-stat-strip grid grid-cols-2 lg:grid-cols-4 gap-3 lg:gap-4">
            @foreach([
                ['label'=>'Butuh Tindakan',  'value'=>$actionNeeded,     'desc'=>'Dokumen & Pembayaran', 'icon'=>'fa-circle-exclamation', 'tone'=>'warning'],
                ['label'=>'Aktif Diproses',  'value'=>$activeProcessing, 'desc'=>'Sedang Ditangani',     'icon'=>'fa-spinner',            'tone'=>'info'],
                ['label'=>'Menunggu Respon', 'value'=>$waitingResponse,  'desc'=>'Review Penawaran',     'icon'=>'fa-clock',              'tone'=>'neutral'],
                ['label'=>'Selesai',         'value'=>$completed,        'desc'=>'Izin Terbit',          'icon'=>'fa-check-circle',       'tone'=>'success'],
            ] as $stat)
            <div class="bg-white/10 backdrop-blur border border-white/15 rounded-lg px-4 py-3 lg:py-4">
                <div class="flex items-center justify-between mb-1.5">
                    <p class="text-[11px] uppercase tracking-wider text-white/70 font-semibold leading-tight">{{ $stat['label'] }}</p>
                    <i class="fas {{ $stat['icon'] }} text-white/40 text-xs" aria-hidden="true"></i>
                </div>
                <p class="text-2xl lg:text-3xl font-bold tabular-nums leading-none">{{ $stat['value'] }}</p>
                <p class="text-[11px] text-white/60 mt-1">{{ $stat['desc'] }}</p>
            </div>
            @endforeach
        </div>

        {{-- Mobile actions --}}
        <div class="flex gap-2 mt-5 lg:hidden">
            <a href="{{ route('client.services.index') }}" class="flex-1 inline-flex items-center justify-center gap-2 bg-white text-[var(--client-primary)] font-semibold text-sm px-4 py-3 rounded-lg">
                <i class="fas fa-plus text-xs" aria-hidden="true"></i> Ajukan
            </a>
            <a href="{{ route('client.documents.index') }}" class="flex-1 inline-flex items-center justify-center gap-2 bg-white/10 backdrop-blur border border-white/20 text-white font-semibold text-sm px-4 py-3 rounded-lg">
                <i class="fas fa-paperclip text-xs" aria-hidden="true"></i> Dokumen
            </a>
        </div>
    </div>
</section>

{{-- ─── FILTER + SEARCH BAR ─── --}}
<div class="sticky top-0 z-20 bg-[var(--surface-elevated)]/95 backdrop-blur border-b border-[var(--border-subtle)]"
     x-data="{
         search: @js($currentSearch),
         status: @js($currentStatus),
         submit() {
             const url = new URL(window.location.href);
             this.search ? url.searchParams.set('search', this.search) : url.searchParams.delete('search');
             this.status ? url.searchParams.set('status', this.status) : url.searchParams.delete('status');
             url.searchParams.delete('page');
             window.location.href = url.toString();
         }
     }">
    <div class="max-w-[1400px] mx-auto px-4 lg:px-8 py-3 space-y-3">

        {{-- Search + view toggle --}}
        <div class="flex items-center gap-2">
            <div class="relative flex-1">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-[var(--text-tertiary)] text-xs" aria-hidden="true"></i>
                <input type="text" x-model="search"
                       @keydown.enter.prevent="submit()"
                       placeholder="Cari nomor permohonan, jenis izin, KBLI…"
                       class="w-full pl-9 pr-9 py-2 text-sm bg-[var(--surface-cool)] border border-[var(--border-subtle)] rounded-lg focus:ring-2 focus:ring-[var(--client-primary)] focus:border-[var(--client-primary)] text-[var(--text-primary)] placeholder-[var(--text-tertiary)]"
                       aria-label="Cari permohonan">
                <template x-if="search.length > 0">
                    <button type="button" @click="search=''; submit()" class="absolute right-3 top-1/2 -translate-y-1/2 text-[var(--text-tertiary)] hover:text-[var(--text-primary)]" aria-label="Hapus pencarian">
                        <i class="fas fa-times text-xs"></i>
                    </button>
                </template>
            </div>
            <button type="button" @click="$dispatch('cmdk-open')"
                    class="hidden lg:inline-flex items-center gap-2 px-3 py-2 bg-[var(--surface-cool)] border border-[var(--border-subtle)] rounded-lg text-xs font-medium text-[var(--text-secondary)] hover:bg-[var(--client-primary-light)] hover:border-[var(--client-primary)] hover:text-[var(--client-primary)] transition-colors">
                <i class="fas fa-bolt text-[10px]" aria-hidden="true"></i> Quick Nav
                <x-ui.kbd :keys="['⌘','K']" />
            </button>
        </div>

        {{-- Filter chips --}}
        <div class="flex gap-1.5 overflow-x-auto scrollbar-hide -mx-1 px-1 pb-0.5">
            @foreach($filterChips as $chipStatus => $chip)
                @php
                    $isActive = $currentStatus === $chipStatus;
                    $count = $chipStatus ? ($statusCounts[$chipStatus] ?? 0) : $totalApplications;
                @endphp
                <button type="button" @click="status=@js($chipStatus); submit()"
                        class="flex-shrink-0 inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold transition-colors
                            {{ $isActive
                                ? 'bg-[var(--client-primary)] text-white shadow-sm'
                                : 'bg-[var(--surface-cool)] text-[var(--text-secondary)] hover:bg-[var(--client-primary-light)] hover:text-[var(--client-primary)] border border-[var(--border-subtle)]' }}"
                        aria-pressed="{{ $isActive ? 'true' : 'false' }}">
                    <i class="fas {{ $chip['icon'] }} text-[10px]" aria-hidden="true"></i>
                    {{ $chip['label'] }}
                    @if($count > 0)
                        <span class="inline-flex items-center justify-center min-w-[18px] h-[18px] px-1 rounded-full text-[10px] font-bold tabular-nums
                            {{ $isActive ? 'bg-white/25 text-white' : 'bg-[var(--surface-elevated)] text-[var(--text-tertiary)]' }}">
                            {{ $count }}
                        </span>
                    @endif
                </button>
            @endforeach
        </div>
    </div>
</div>

{{-- ─── LIST ─── --}}
<div class="max-w-[1400px] mx-auto px-4 lg:px-8 py-6">

@if($applications->count() === 0)
    <x-ui.empty-state
        icon="fas fa-inbox"
        title="{{ $currentSearch || $currentStatus ? 'Tidak ada hasil' : 'Belum ada permohonan' }}"
        description="{{ $currentSearch || $currentStatus
            ? 'Coba ubah filter atau kata kunci pencarian.'
            : 'Mulailah dengan menjelajahi katalog perizinan dan ajukan izin pertama Anda.' }}"
        :action="$currentSearch || $currentStatus
            ? null
            : ['label' => 'Jelajahi Katalog', 'url' => route('client.services.index'), 'icon' => 'fas fa-arrow-right']"
    />
@else
    <div class="bg-[var(--surface-elevated)] border border-[var(--border-subtle)] rounded-xl overflow-hidden divide-y divide-[var(--border-subtle)]">
        @foreach($applications as $application)
            @php
                $formData = is_string($application->form_data) ? json_decode($application->form_data, true) : $application->form_data;
                $formData = $formData ?? [];
                $isPackage = ($formData['package_type'] ?? null) === 'multi_permit';

                $permitName = 'Permohonan Izin';
                if ($application->permitType) {
                    $permitName = $application->permitType->name;
                } elseif ($isPackage) {
                    $totalPermits = ($formData['permits_by_service']['bizmark'] ?? 0) + ($formData['permits_by_service']['owned'] ?? 0);
                    $permitName = ($formData['project_name'] ?? 'Paket Izin') . ' (' . $totalPermits . ' Perizinan)';
                } elseif (!empty($formData['permit_name'])) {
                    $permitName = $formData['permit_name'];
                } elseif ($application->kbli_description) {
                    $permitName = 'Perizinan ' . $application->kbli_description;
                }

                $location = (!empty($formData['city']) && !empty($formData['province']))
                    ? $formData['city'].', '.$formData['province']
                    : ($formData['province'] ?? null);
                $businessScale = !empty($formData['business_scale']) ? ucfirst($formData['business_scale']) : null;
                $landArea = !empty($formData['land_area']) ? number_format($formData['land_area'], 0, ',', '.').' m²' : null;

                $daysAgo = (int) $application->created_at->diffInDays(now());
                $relTime = $daysAgo === 0 ? 'Hari ini' : ($daysAgo === 1 ? 'Kemarin' : $daysAgo.' hari lalu');
            @endphp

            <article class="group portal-lift hover:bg-[var(--surface-cool)] transition-colors">
                <a href="{{ route('client.applications.show', $application->id) }}" class="block px-4 lg:px-6 py-4 lg:py-5">
                    <div class="flex items-start gap-4">
                        <div class="flex-1 min-w-0 space-y-2.5">

                            {{-- Status row --}}
                            <div class="flex flex-wrap items-center gap-2">
                                <x-ui.status-pill :status="$application->status" />
                                <span class="portal-mono text-[11px] text-[var(--text-tertiary)]">
                                    {{ $application->application_number }}
                                </span>
                                <span class="text-[11px] text-[var(--text-tertiary)]">·</span>
                                <span class="text-[11px] text-[var(--text-tertiary)]">{{ $relTime }}</span>
                                @if($isPackage)
                                <span class="portal-pill portal-pill--info portal-pill--with-icon ml-auto lg:ml-0">
                                    <i class="fas fa-box text-[9px]" aria-hidden="true"></i> Paket
                                </span>
                                @endif
                            </div>

                            {{-- Title --}}
                            <h3 class="text-[15px] lg:text-base font-semibold text-[var(--text-primary)] leading-snug group-hover:text-[var(--client-primary)] transition-colors">
                                {{ $permitName }}
                            </h3>

                            @if($application->kbli_code && $application->kbli_description)
                            <p class="text-xs text-[var(--text-secondary)] leading-relaxed">
                                <span class="portal-mono font-semibold">KBLI {{ $application->kbli_code }}</span>
                                <span class="mx-1">·</span>{{ Str::limit($application->kbli_description, 80) }}
                            </p>
                            @endif

                            {{-- Meta strip --}}
                            @if($location || $businessScale || $landArea || $application->documents->count() > 0 || $application->quoted_price)
                            <div class="flex flex-wrap items-center gap-x-4 gap-y-1.5 text-xs text-[var(--text-secondary)]">
                                @if($location)
                                <span class="inline-flex items-center gap-1.5">
                                    <i class="fas fa-location-dot text-[10px] text-[var(--text-tertiary)]" aria-hidden="true"></i>{{ $location }}
                                </span>
                                @endif
                                @if($businessScale)
                                <span class="inline-flex items-center gap-1.5">
                                    <i class="fas fa-building text-[10px] text-[var(--text-tertiary)]" aria-hidden="true"></i>{{ $businessScale }}
                                </span>
                                @endif
                                @if($landArea)
                                <span class="inline-flex items-center gap-1.5">
                                    <i class="fas fa-ruler-combined text-[10px] text-[var(--text-tertiary)]" aria-hidden="true"></i>{{ $landArea }}
                                </span>
                                @endif
                                <span class="inline-flex items-center gap-1.5">
                                    <i class="fas fa-paperclip text-[10px] text-[var(--text-tertiary)]" aria-hidden="true"></i>
                                    {{ $application->documents->count() }} dokumen
                                </span>
                                @if($application->quoted_price)
                                <span class="inline-flex items-center gap-1.5 font-semibold text-[var(--text-primary)]">
                                    <i class="fas fa-money-bill text-[10px] text-[var(--apple-green)]" aria-hidden="true"></i>
                                    Rp {{ number_format($application->quoted_price, 0, ',', '.') }}
                                </span>
                                @endif
                            </div>
                            @endif
                        </div>

                        <div class="flex-shrink-0 flex items-center gap-2 mt-1">
                            <button type="button"
                                    @click.prevent.stop="$dispatch('drawer-open', { name: 'app-peek-{{ $application->id }}' })"
                                    class="hidden lg:inline-flex items-center justify-center w-8 h-8 rounded-md border border-[var(--border-subtle)] text-[var(--text-tertiary)] hover:text-[var(--client-primary)] hover:border-[var(--client-primary)] transition-colors"
                                    title="Peek detail" aria-label="Peek detail">
                                <i class="fas fa-eye text-xs" aria-hidden="true"></i>
                            </button>
                            <i class="fas fa-chevron-right text-[var(--text-tertiary)] text-xs hidden sm:block" aria-hidden="true"></i>
                        </div>
                    </div>
                </a>
            </article>

            {{-- Peek drawer (desktop) --}}
            <x-ui.drawer name="app-peek-{{ $application->id }}" size="md" :title="$application->application_number">
                <div class="space-y-4">
                    <div>
                        <span class="portal-eyebrow"><i class="fas fa-circle-info text-[9px]" aria-hidden="true"></i> Ringkasan</span>
                        <h3 class="mt-2 text-base font-semibold text-[var(--text-primary)] leading-snug">{{ $permitName }}</h3>
                        <div class="mt-2 flex items-center gap-2">
                            <x-ui.status-pill :status="$application->status" />
                            <span class="text-[11px] text-[var(--text-tertiary)]">{{ $relTime }}</span>
                        </div>
                    </div>

                    @if($application->kbli_code)
                    <div class="rounded-lg bg-[var(--surface-cool)] border border-[var(--border-subtle)] px-3 py-2.5">
                        <p class="text-[10px] uppercase tracking-wider text-[var(--text-tertiary)] font-semibold">KBLI</p>
                        <p class="portal-mono text-sm font-semibold text-[var(--text-primary)] mt-0.5">{{ $application->kbli_code }}</p>
                        @if($application->kbli_description)
                        <p class="text-xs text-[var(--text-secondary)] mt-1">{{ $application->kbli_description }}</p>
                        @endif
                    </div>
                    @endif

                    <dl class="grid grid-cols-2 gap-3 text-xs">
                        @if($location)
                        <div>
                            <dt class="text-[10px] uppercase tracking-wider text-[var(--text-tertiary)] font-semibold">Lokasi</dt>
                            <dd class="mt-0.5 text-[var(--text-primary)]">{{ $location }}</dd>
                        </div>
                        @endif
                        @if($businessScale)
                        <div>
                            <dt class="text-[10px] uppercase tracking-wider text-[var(--text-tertiary)] font-semibold">Skala</dt>
                            <dd class="mt-0.5 text-[var(--text-primary)]">{{ $businessScale }}</dd>
                        </div>
                        @endif
                        <div>
                            <dt class="text-[10px] uppercase tracking-wider text-[var(--text-tertiary)] font-semibold">Dokumen</dt>
                            <dd class="mt-0.5 text-[var(--text-primary)]">{{ $application->documents->count() }} berkas</dd>
                        </div>
                        @if($application->quoted_price)
                        <div>
                            <dt class="text-[10px] uppercase tracking-wider text-[var(--text-tertiary)] font-semibold">Penawaran</dt>
                            <dd class="mt-0.5 text-[var(--text-primary)] font-semibold">Rp {{ number_format($application->quoted_price, 0, ',', '.') }}</dd>
                        </div>
                        @endif
                        <div>
                            <dt class="text-[10px] uppercase tracking-wider text-[var(--text-tertiary)] font-semibold">Dibuat</dt>
                            <dd class="mt-0.5 text-[var(--text-primary)]">{{ $application->created_at->format('d M Y') }}</dd>
                        </div>
                        @if($application->submitted_at)
                        <div>
                            <dt class="text-[10px] uppercase tracking-wider text-[var(--text-tertiary)] font-semibold">Diajukan</dt>
                            <dd class="mt-0.5 text-[var(--text-primary)]">{{ $application->submitted_at->format('d M Y') }}</dd>
                        </div>
                        @endif
                    </dl>
                </div>

                <x-slot:footer>
                    <a href="{{ route('client.applications.show', $application->id) }}"
                       class="inline-flex items-center justify-center gap-2 px-4 py-2 bg-[var(--client-primary)] text-white text-sm font-semibold rounded-md hover:opacity-90 transition-opacity">
                        Buka detail <i class="fas fa-arrow-right text-[10px]" aria-hidden="true"></i>
                    </a>
                </x-slot:footer>
            </x-ui.drawer>
        @endforeach
    </div>

    {{-- Pagination --}}
    @if($applications->hasPages())
    <div class="mt-6">
        {{ $applications->links() }}
    </div>
    @endif
@endif

</div>
