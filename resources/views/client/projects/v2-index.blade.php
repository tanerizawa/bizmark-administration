{{-- Projects List — Portal v2 --}}
@php
    $totalProjects   = $stats['total'] ?? 0;
    $activeProjects  = $stats['active'] ?? 0;
    $completedProjects = $stats['completed'] ?? 0;
    $totalValue      = $stats['total_value'] ?? 0;
    $sortBy          = request('sort_by', 'created_at');
    $sortDir         = request('sort_order', 'desc');
    $currentStatus   = request('status', '');
    $currentSearch   = request('search', '');
@endphp

{{-- ─── HERO ─── --}}
<section class="portal-hero relative overflow-hidden border-b border-[var(--border-subtle)]"
         style="background: linear-gradient(135deg, var(--client-primary) 0%, color-mix(in oklab, var(--client-primary) 75%, #001a40) 100%); color:#fff;"
         aria-label="Proyek saya">
    <div class="portal-glow-orb portal-glow-orb--tr hidden lg:block" aria-hidden="true"
         style="background: radial-gradient(circle at 50% 50%, rgba(255,255,255,0.18) 0%, transparent 70%); --tr-x: 100px; --tr-y: -80px;"></div>
    <svg class="portal-archipelago absolute inset-y-0 right-0 h-full opacity-[0.06] pointer-events-none hidden lg:block"
         viewBox="0 0 800 300" preserveAspectRatio="xMidYMid slice" aria-hidden="true">
        <path fill="currentColor" d="M0,150 Q100,80 200,140 T400,150 T600,135 T800,150 V300 H0 Z"/>
    </svg>

    <div class="relative max-w-[1400px] mx-auto px-4 lg:px-8 py-6 lg:py-9">
        <div class="flex items-start justify-between gap-6 mb-6">
            <div>
                <span class="portal-eyebrow" style="background: rgba(255,255,255,0.15); color: rgba(255,255,255,0.9); border-color: rgba(255,255,255,0.2);">
                    <i class="fas fa-briefcase text-[9px]" aria-hidden="true"></i>
                    Manajemen Proyek
                </span>
                <h1 class="mt-2 text-2xl lg:text-[28px] font-bold leading-tight">
                    {{ $totalProjects }} Proyek
                    <span class="text-white/70 font-normal">terdaftar</span>
                </h1>
                <p class="mt-1.5 text-sm text-white/80 max-w-xl">Pantau progres semua proyek perizinan aktif dan selesai.</p>
            </div>
            <a href="{{ route('client.applications.index') }}"
               class="hidden lg:inline-flex items-center gap-2 bg-white text-[var(--client-primary)] font-semibold text-sm px-4 py-2.5 rounded-lg shadow-sm hover:shadow-md transition-shadow flex-shrink-0">
                <i class="fas fa-folder-open text-xs" aria-hidden="true"></i> Lihat Permohonan
            </a>
        </div>

        <div class="portal-stat-strip grid grid-cols-2 lg:grid-cols-4 gap-3">
            @foreach([
                ['label'=>'Total Proyek',    'value'=>$totalProjects,   'sub'=>'Semua status',   'icon'=>'fa-briefcase'],
                ['label'=>'Aktif',           'value'=>$activeProjects,  'sub'=>'Sedang berjalan','icon'=>'fa-spinner'],
                ['label'=>'Selesai',         'value'=>$completedProjects,'sub'=>'Izin terbit',   'icon'=>'fa-check-circle'],
                ['label'=>'Total Nilai',     'value'=>'Rp '.number_format($totalValue/1000000,0).'M','sub'=>'Investasi','icon'=>'fa-money-bill'],
            ] as $s)
            <div class="bg-white/10 backdrop-blur border border-white/15 rounded-lg px-4 py-3 lg:py-4">
                <div class="flex items-center justify-between mb-1.5">
                    <p class="text-[11px] uppercase tracking-wider text-white/70 font-semibold">{{ $s['label'] }}</p>
                    <i class="fas {{ $s['icon'] }} text-white/40 text-xs" aria-hidden="true"></i>
                </div>
                <p class="text-2xl lg:text-3xl font-bold tabular-nums leading-none">{{ $s['value'] }}</p>
                <p class="text-[11px] text-white/60 mt-1">{{ $s['sub'] }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ─── FILTER BAR ─── --}}
<div class="sticky top-0 z-20 bg-[var(--surface-elevated)]/95 backdrop-blur border-b border-[var(--border-subtle)]"
     x-data="{
         search: @js($currentSearch),
         status: @js($currentStatus),
         sort: @js($sortBy.'_'.$sortDir),
         view: localStorage.getItem('projects_view') || 'grid',
         setView(v) { this.view = v; localStorage.setItem('projects_view', v); },
         submit() {
             const url = new URL(window.location.href);
             this.search ? url.searchParams.set('search', this.search) : url.searchParams.delete('search');
             this.status ? url.searchParams.set('status', this.status) : url.searchParams.delete('status');
             const [sb,sd] = this.sort.split('_last_');
             url.searchParams.set('sort_by', sb);
             url.searchParams.set('sort_order', sd || 'desc');
             url.searchParams.delete('page');
             window.location.href = url.toString();
         }
     }" id="projects-filter-bar">
    <div class="max-w-[1400px] mx-auto px-4 lg:px-8 py-3 flex items-center gap-2">
        <div class="relative flex-1">
            <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-[var(--text-tertiary)] text-xs" aria-hidden="true"></i>
            <input type="text" x-model="search" @keydown.enter.prevent="submit()"
                   placeholder="Cari nama proyek, nomor…"
                   class="w-full pl-9 pr-4 py-2 text-sm bg-[var(--surface-cool)] border border-[var(--border-subtle)] rounded-lg focus:ring-2 focus:ring-[var(--client-primary)] focus:border-[var(--client-primary)] text-[var(--text-primary)] placeholder-[var(--text-tertiary)]">
        </div>
        @if($statuses->count())
        <div class="flex gap-1.5 overflow-x-auto scrollbar-hide">
            <button type="button" @click="status=''; submit()"
                    class="flex-shrink-0 inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold transition-colors
                        {{ !$currentStatus ? 'bg-[var(--client-primary)] text-white shadow-sm' : 'bg-[var(--surface-cool)] text-[var(--text-secondary)] border border-[var(--border-subtle)]' }}">
                Semua
            </button>
            @foreach($statuses as $status)
            <button type="button" @click="status=@js($status->name); submit()"
                    class="flex-shrink-0 inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold transition-colors
                        {{ $currentStatus === $status->name ? 'bg-[var(--client-primary)] text-white shadow-sm' : 'bg-[var(--surface-cool)] text-[var(--text-secondary)] border border-[var(--border-subtle)]' }}">
                {{ $status->name }}
            </button>
            @endforeach
        </div>
        @endif

        {{-- View toggle: Grid / Kanban --}}
        <div class="hidden sm:flex items-center gap-1 flex-shrink-0 p-0.5 rounded-lg border border-[var(--border-subtle)]"
             style="background: var(--surface-cool);"
             role="group" aria-label="Tampilan">
            <button type="button"
                    @click="setView('grid')"
                    :class="view === 'grid' ? 'bg-[var(--surface-elevated)] text-[var(--client-primary)] shadow-sm' : 'text-[var(--text-tertiary)] hover:text-[var(--text-secondary)]'"
                    class="inline-flex items-center gap-1 px-2.5 py-1.5 text-xs font-semibold rounded-md transition-all"
                    aria-label="Tampilan grid">
                <i class="fas fa-th-large text-[10px]" aria-hidden="true"></i>
                <span class="hidden md:inline">Grid</span>
            </button>
            <button type="button"
                    @click="setView('kanban')"
                    :class="view === 'kanban' ? 'bg-[var(--surface-elevated)] text-[var(--client-primary)] shadow-sm' : 'text-[var(--text-tertiary)] hover:text-[var(--text-secondary)]'"
                    class="inline-flex items-center gap-1 px-2.5 py-1.5 text-xs font-semibold rounded-md transition-all"
                    aria-label="Tampilan kanban">
                <i class="fas fa-columns text-[10px]" aria-hidden="true"></i>
                <span class="hidden md:inline">Kanban</span>
            </button>
        </div>
    </div>
</div>

{{-- ─── LIST / KANBAN ─── --}}
<div class="max-w-[1400px] mx-auto px-4 lg:px-8 py-6"
     x-data="{ get view() { return document.getElementById('projects-filter-bar')?._x_dataStack?.[0]?.view || localStorage.getItem('projects_view') || 'grid' } }">

@if($projects->count() === 0)
    <x-ui.empty-state
        icon="fas fa-briefcase"
        title="{{ $currentSearch || $currentStatus ? 'Tidak ada hasil' : 'Belum ada proyek' }}"
        description="{{ $currentSearch || $currentStatus ? 'Coba ubah filter.' : 'Proyek akan muncul setelah permohonan izin Anda diproses.' }}"
    />
@else

    {{-- ── GRID VIEW ── --}}
    <div x-show="view !== 'kanban'" x-cloak class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-4">
        @foreach($projects as $project)
        @php
            $progress = $project->progress_percentage ?? 0;
            $statusColor = $project->status->color ?? '#0a66c2';
            $progressColor = $progress >= 100 ? 'var(--apple-green)' : ($progress >= 50 ? 'var(--client-primary)' : 'var(--apple-orange)');
        @endphp
        <a href="{{ route('client.projects.show', $project->id) }}"
           class="portal-lift bg-[var(--surface-elevated)] border border-[var(--border-subtle)] rounded-xl overflow-hidden group block">

            {{-- Card header (status colour band) --}}
            <div class="h-1.5 w-full" style="background: {{ $statusColor }};"></div>

            <div class="p-4 lg:p-5 space-y-3">
                {{-- Status + permit type --}}
                <div class="flex items-start justify-between gap-2">
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold"
                          style="background: {{ $statusColor }}1a; color: {{ $statusColor }};">
                        <span class="w-1.5 h-1.5 rounded-full inline-block" style="background: {{ $statusColor }};"></span>
                        {{ $project->status?->name ?? 'N/A' }}
                    </span>
                    <span class="portal-mono text-[10px] text-[var(--text-tertiary)]">#{{ $project->id }}</span>
                </div>

                {{-- Name --}}
                <h3 class="text-base font-semibold text-[var(--text-primary)] leading-snug group-hover:text-[var(--client-primary)] transition-colors line-clamp-2">
                    {{ $project->name }}
                </h3>

                @if($project->permitApplication?->permitType)
                <p class="text-xs text-[var(--text-secondary)] line-clamp-1">
                    <i class="fas fa-file-alt text-[10px] mr-1 text-[var(--text-tertiary)]" aria-hidden="true"></i>
                    {{ $project->permitApplication->permitType->name }}
                </p>
                @endif

                {{-- Progress bar --}}
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <span class="text-[10px] text-[var(--text-tertiary)] font-medium">Progress</span>
                        <span class="text-[10px] font-bold tabular-nums" style="color: {{ $progressColor }}">{{ $progress }}%</span>
                    </div>
                    <div class="h-1.5 rounded-full bg-[var(--surface-sunken)] overflow-hidden">
                        <div class="h-full rounded-full transition-all duration-500" style="width: {{ $progress }}%; background: {{ $progressColor }};"></div>
                    </div>
                </div>

                {{-- Meta --}}
                <div class="flex items-center justify-between gap-2 pt-1 border-t border-[var(--border-subtle)] text-xs text-[var(--text-tertiary)]">
                    <div class="flex items-center gap-3">
                        @if($project->deadline)
                        <span class="inline-flex items-center gap-1 {{ now()->gt($project->deadline) ? 'text-[var(--apple-red)]' : '' }}">
                            <i class="fas fa-flag text-[9px]" aria-hidden="true"></i>
                            {{ $project->deadline->format('d M Y') }}
                        </span>
                        @endif
                        @if($project->tasks_count ?? $project->tasks?->count())
                        <span class="inline-flex items-center gap-1">
                            <i class="fas fa-tasks text-[9px]" aria-hidden="true"></i>
                            {{ $project->tasks?->count() ?? 0 }}
                        </span>
                        @endif
                    </div>
                    @if($project->contract_value)
                    <span class="font-semibold text-[var(--text-primary)]">Rp {{ number_format($project->contract_value / 1000000, 1) }}M</span>
                    @endif
                </div>
            </div>
        </a>
        @endforeach
    </div>

    {{-- ── KANBAN VIEW ── --}}
    @php
        // Group projects by status for kanban (use getCollection for paginator)
        $projectItems = $projects instanceof \Illuminate\Pagination\AbstractPaginator
            ? $projects->getCollection()
            : collect($projects);

        $kanbanColumns = $statuses->map(function($statusObj) use ($projectItems) {
            return [
                'status'   => $statusObj,
                'projects' => $projectItems->where('status_id', $statusObj->id)->values(),
            ];
        })->filter(fn($col) => $col['projects']->isNotEmpty());
    @endphp
    <div x-show="view === 'kanban'" x-cloak
         class="flex gap-4 overflow-x-auto pb-4 -mx-4 px-4 lg:-mx-8 lg:px-8"
         style="min-height: 400px;">

        @foreach($kanbanColumns as $col)
        @php
            $colColor = $col['status']->color ?? '#0a66c2';
        @endphp
        <div class="flex-shrink-0 w-72 flex flex-col">
            {{-- Column header --}}
            <div class="flex items-center justify-between px-3 py-2.5 rounded-t-xl mb-0 border-b-2"
                 style="background: {{ $colColor }}18; border-color: {{ $colColor }}; border-top: 1px solid {{ $colColor }}40; border-left: 1px solid {{ $colColor }}40; border-right: 1px solid {{ $colColor }}40;">
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full inline-block" style="background: {{ $colColor }};"></span>
                    <h3 class="text-xs font-bold text-[var(--text-primary)]">{{ $col['status']->name }}</h3>
                </div>
                <span class="inline-flex items-center justify-center w-5 h-5 rounded-full text-[10px] font-bold text-white"
                      style="background: {{ $colColor }};">{{ $col['projects']->count() }}</span>
            </div>

            {{-- Column cards --}}
            <div class="flex-1 space-y-2.5 p-3 rounded-b-xl border"
                 style="background: var(--surface-cool); border-color: {{ $colColor }}40; border-top: none; min-height: 200px;">
                @foreach($col['projects'] as $project)
                @php
                    $progress = $project->progress_percentage ?? 0;
                    $progressColor = $progress >= 100 ? 'var(--apple-green)' : ($progress >= 50 ? 'var(--client-primary)' : 'var(--apple-orange)');
                @endphp
                <a href="{{ route('client.projects.show', $project->id) }}"
                   class="block rounded-xl border border-[var(--border-subtle)] p-3 transition-all hover:shadow-md hover:-translate-y-0.5 group"
                   style="background: var(--surface-elevated);">
                    <h4 class="text-sm font-semibold text-[var(--text-primary)] group-hover:text-[var(--client-primary)] transition-colors line-clamp-2 mb-2 leading-snug">
                        {{ $project->name }}
                    </h4>

                    @if($project->permitApplication?->permitType)
                    <p class="text-[10px] text-[var(--text-tertiary)] mb-2 line-clamp-1">
                        <i class="fas fa-file-alt text-[9px] mr-1" aria-hidden="true"></i>
                        {{ $project->permitApplication->permitType->name }}
                    </p>
                    @endif

                    {{-- Progress --}}
                    <div class="mb-2">
                        <div class="h-1 rounded-full bg-[var(--surface-sunken)] overflow-hidden">
                            <div class="h-full rounded-full" style="width: {{ $progress }}%; background: {{ $progressColor }};"></div>
                        </div>
                    </div>

                    <div class="flex items-center justify-between text-[10px] text-[var(--text-tertiary)]">
                        @if($project->deadline)
                        <span class="{{ now()->gt($project->deadline) ? 'text-[var(--apple-red)]' : '' }}">
                            <i class="fas fa-flag text-[8px]" aria-hidden="true"></i>
                            {{ $project->deadline->format('d M') }}
                        </span>
                        @else
                        <span></span>
                        @endif
                        <span class="font-semibold tabular-nums" style="color: {{ $progressColor }}">{{ $progress }}%</span>
                    </div>
                </a>
                @endforeach

                @if($col['projects']->isEmpty())
                <div class="text-center py-8 text-[var(--text-tertiary)] text-xs opacity-60">
                    <i class="fas fa-inbox text-lg mb-2 block" aria-hidden="true"></i>
                    Kosong
                </div>
                @endif
            </div>
        </div>
        @endforeach

        {{-- Empty state for kanban --}}
        @if($kanbanColumns->isEmpty())
        <div class="flex-1 flex items-center justify-center">
            <x-ui.empty-state icon="fas fa-columns" title="Tidak ada kolom status" description="Tidak ada proyek yang cocok dengan filter ini." />
        </div>
        @endif
    </div>

@endif
</div>
