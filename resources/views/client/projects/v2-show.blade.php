{{-- Project Detail — Portal v2 (tabs: Overview / Tasks / Dokumen / Aktivitas) --}}
@php
    $progress = $stats['progress'] ?? 0;
    $progressColor = $progress >= 100 ? 'var(--apple-green)' : ($progress >= 50 ? 'var(--client-primary)' : 'var(--apple-orange)');
    $statusColor = $project->status?->color ?? 'var(--client-primary)';

    $pendingBalance = $stats['pending_payments'] ?? 0;
    $completedTasks = $stats['completed_tasks'] ?? 0;
    $totalTasks = $stats['total_tasks'] ?? 0;

    // Timeline items from logs
    $timelineItems = [];
    foreach ($recentActivities as $log) {
        $timelineItems[] = [
            'title' => $log->description ?? 'Aktivitas proyek',
            'subtitle' => ($log->user?->name ?? 'Tim') . ' · ' . $log->created_at->diffForHumans(),
            'status' => 'completed',
            'icon' => 'fa-circle-check',
        ];
    }

    $tabs = [
        ['id' => 'overview',  'label' => 'Overview',   'icon' => 'fas fa-chart-pie'],
        ['id' => 'tasks',     'label' => 'Tasks',      'icon' => 'fas fa-list-check', 'badge' => $totalTasks ?: null],
        ['id' => 'docs',      'label' => 'Dokumen',    'icon' => 'fas fa-paperclip',  'badge' => $stats['total_documents'] ?: null],
        ['id' => 'activity',  'label' => 'Aktivitas',  'icon' => 'fas fa-clock-rotate-left'],
    ];
@endphp

{{-- ─── HERO ─── --}}
<section class="portal-hero relative overflow-hidden border-b border-[var(--border-subtle)]"
         style="background: linear-gradient(135deg, {{ $statusColor }} 0%, color-mix(in oklab, {{ $statusColor }} 70%, #000) 100%); color:#fff;"
         aria-label="Detail proyek">
    <div class="portal-glow-orb portal-glow-orb--tr hidden lg:block" aria-hidden="true"
         style="background: radial-gradient(circle at 50% 50%, rgba(255,255,255,0.2) 0%, transparent 70%); --tr-x: 80px; --tr-y: -80px;"></div>

    <div class="relative max-w-[1400px] mx-auto px-4 lg:px-8 py-5 lg:py-7">
        <a href="{{ route('client.projects.index') }}"
           class="inline-flex items-center gap-2 text-white/70 hover:text-white text-xs font-medium mb-4 transition-colors">
            <i class="fas fa-arrow-left text-[10px]" aria-hidden="true"></i>
            Daftar Proyek
        </a>

        <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-5">
            <div class="min-w-0">
                <span class="portal-eyebrow" style="background: rgba(255,255,255,0.15); color: rgba(255,255,255,0.9); border-color: rgba(255,255,255,0.2);">
                    <i class="fas fa-briefcase text-[9px]" aria-hidden="true"></i>
                    Proyek
                </span>
                <h1 class="mt-2 text-xl lg:text-2xl font-bold leading-snug">{{ $project->name }}</h1>
                @if($project->description)
                <p class="mt-1 text-sm text-white/80 max-w-2xl line-clamp-2">{{ $project->description }}</p>
                @endif
                <div class="mt-3 flex flex-wrap items-center gap-2">
                    <span class="inline-flex items-center gap-1.5 bg-white/15 backdrop-blur border border-white/20 px-3 py-1 rounded-full text-xs font-semibold">
                        {{ $project->status?->name ?? 'Aktif' }}
                    </span>
                    @if($project->institution)
                    <span class="text-xs text-white/70">
                        <i class="fas fa-building text-[10px] mr-1" aria-hidden="true"></i>
                        {{ $project->institution->name }}
                    </span>
                    @endif
                    @if($project->deadline)
                    <span class="text-xs {{ now()->gt($project->deadline) ? 'text-[var(--apple-red-light)]' : 'text-white/70' }}">
                        <i class="fas fa-flag text-[10px] mr-1" aria-hidden="true"></i>
                        Deadline {{ $project->deadline->format('d M Y') }}
                    </span>
                    @endif
                </div>
            </div>

            {{-- Quick stats --}}
            <div class="grid grid-cols-3 gap-2 lg:gap-3 flex-shrink-0">
                <div class="bg-white/10 backdrop-blur border border-white/15 rounded-lg px-3 py-2.5 lg:min-w-[110px]">
                    <p class="text-[10px] uppercase tracking-wider text-white/70 font-semibold">Progress</p>
                    <p class="text-xl lg:text-2xl font-bold tabular-nums">{{ $progress }}%</p>
                    <div class="h-1 rounded-full bg-white/20 mt-1.5 overflow-hidden">
                        <div class="h-full rounded-full bg-white/80 transition-all" style="width: {{ $progress }}%"></div>
                    </div>
                </div>
                <div class="bg-white/10 backdrop-blur border border-white/15 rounded-lg px-3 py-2.5 lg:min-w-[110px]">
                    <p class="text-[10px] uppercase tracking-wider text-white/70 font-semibold">Task</p>
                    <p class="text-xl lg:text-2xl font-bold tabular-nums">{{ $completedTasks }}<span class="text-sm text-white/60">/{{ $totalTasks }}</span></p>
                    <p class="text-[10px] text-white/60 mt-0.5">Selesai</p>
                </div>
                <div class="bg-white/10 backdrop-blur border border-white/15 rounded-lg px-3 py-2.5 lg:min-w-[110px]">
                    @if($project->contract_value)
                    <p class="text-[10px] uppercase tracking-wider text-white/70 font-semibold">Nilai</p>
                    <p class="text-lg lg:text-xl font-bold tabular-nums">Rp {{ number_format($project->contract_value / 1000000, 1) }}M</p>
                    @if($pendingBalance > 0)
                    <p class="text-[10px] text-white/60 mt-0.5">Sisa Rp {{ number_format($pendingBalance / 1000000, 1) }}M</p>
                    @else
                    <p class="text-[10px] text-[var(--apple-green)] mt-0.5">Lunas</p>
                    @endif
                    @else
                    <p class="text-[10px] uppercase tracking-wider text-white/70 font-semibold">Dokumen</p>
                    <p class="text-xl lg:text-2xl font-bold tabular-nums">{{ $stats['total_documents'] ?? 0 }}</p>
                    <p class="text-[10px] text-white/60 mt-0.5">Berkas</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ─── BODY ─── --}}
<div class="max-w-[1400px] mx-auto px-4 lg:px-8 py-6">
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- MAIN TABS --}}
    <div class="lg:col-span-2">
        <div class="bg-[var(--surface-elevated)] border border-[var(--border-subtle)] rounded-xl">
            <div class="px-4 lg:px-6 pt-4">
                <x-ui.tabs :tabs="$tabs" defaultTab="overview">

                    {{-- TAB: OVERVIEW --}}
                    <div x-show="activeTab === 'overview'" x-transition.opacity class="space-y-5 pt-2 pb-5">

                        {{-- Progress breakdown --}}
                        <section>
                            <span class="portal-eyebrow"><i class="fas fa-chart-pie text-[9px]" aria-hidden="true"></i> Progres Proyek</span>
                            <div class="mt-3">
                                <div class="flex items-center justify-between mb-1.5">
                                    <span class="text-sm font-semibold text-[var(--text-primary)]">{{ $progress }}% selesai</span>
                                    <span class="text-xs text-[var(--text-secondary)]">{{ $completedTasks }}/{{ $totalTasks }} task</span>
                                </div>
                                <div class="h-3 rounded-full bg-[var(--surface-sunken)] overflow-hidden">
                                    <div class="h-full rounded-full transition-all duration-700"
                                         style="width: {{ $progress }}%; background: {{ $progressColor }};"></div>
                                </div>
                            </div>
                        </section>

                        {{-- Info grid --}}
                        <section class="border-t border-[var(--border-subtle)] pt-5">
                            <span class="portal-eyebrow"><i class="fas fa-info text-[9px]" aria-hidden="true"></i> Informasi Proyek</span>
                            <dl class="mt-3 grid grid-cols-2 gap-x-4 gap-y-3 text-sm">
                                <div>
                                    <dt class="text-[10px] uppercase tracking-wider text-[var(--text-tertiary)] font-semibold">Status</dt>
                                    <dd class="mt-0.5">
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold"
                                              style="background: {{ $statusColor }}1a; color: {{ $statusColor }};">
                                            {{ $project->status?->name ?? 'Aktif' }}
                                        </span>
                                    </dd>
                                </div>
                                @if($project->institution)
                                <div>
                                    <dt class="text-[10px] uppercase tracking-wider text-[var(--text-tertiary)] font-semibold">Instansi</dt>
                                    <dd class="text-[var(--text-primary)] mt-0.5">{{ $project->institution->name }}</dd>
                                </div>
                                @endif
                                @if($project->start_date)
                                <div>
                                    <dt class="text-[10px] uppercase tracking-wider text-[var(--text-tertiary)] font-semibold">Tanggal Mulai</dt>
                                    <dd class="text-[var(--text-primary)] mt-0.5">{{ $project->start_date->format('d M Y') }}</dd>
                                </div>
                                @endif
                                @if($project->deadline)
                                <div>
                                    <dt class="text-[10px] uppercase tracking-wider text-[var(--text-tertiary)] font-semibold">Deadline</dt>
                                    <dd class="mt-0.5 {{ now()->gt($project->deadline) ? 'text-[var(--apple-red)] font-semibold' : 'text-[var(--text-primary)]' }}">
                                        {{ $project->deadline->format('d M Y') }}
                                        @if(now()->gt($project->deadline))
                                        <span class="text-[10px]">(melewati batas)</span>
                                        @endif
                                    </dd>
                                </div>
                                @endif
                                @if($project->contract_value)
                                <div>
                                    <dt class="text-[10px] uppercase tracking-wider text-[var(--text-tertiary)] font-semibold">Nilai Kontrak</dt>
                                    <dd class="portal-mono text-[var(--text-primary)] font-semibold mt-0.5">Rp {{ number_format($project->contract_value, 0, ',', '.') }}</dd>
                                </div>
                                <div>
                                    <dt class="text-[10px] uppercase tracking-wider text-[var(--text-tertiary)] font-semibold">Status Bayar</dt>
                                    <dd class="mt-0.5">
                                        @if($pendingBalance <= 0)
                                        <span class="portal-pill portal-pill--success portal-pill--with-icon">
                                            <i class="fas fa-check text-[9px]" aria-hidden="true"></i> Lunas
                                        </span>
                                        @else
                                        <span class="portal-pill portal-pill--warning">Rp {{ number_format($pendingBalance / 1000000, 1) }}M sisa</span>
                                        @endif
                                    </dd>
                                </div>
                                @endif
                            </dl>
                        </section>

                        {{-- Permits --}}
                        @if($project->permits && $project->permits->count() > 0)
                        <section class="border-t border-[var(--border-subtle)] pt-5">
                            <span class="portal-eyebrow"><i class="fas fa-certificate text-[9px]" aria-hidden="true"></i> Izin Diterbitkan</span>
                            <ul class="mt-3 space-y-2">
                                @foreach($project->permits as $permit)
                                <li class="flex items-center justify-between gap-3 rounded-md border border-[var(--border-subtle)] bg-[var(--surface-cool)] px-3 py-2">
                                    <div>
                                        <p class="text-sm font-semibold text-[var(--text-primary)]">{{ $permit->permitType?->name ?? 'Izin' }}</p>
                                        @if($permit->permit_number)
                                        <p class="portal-mono text-xs text-[var(--text-tertiary)] mt-0.5">{{ $permit->permit_number }}</p>
                                        @endif
                                    </div>
                                    @if($permit->issue_date)
                                    <span class="text-xs text-[var(--text-secondary)]">{{ \Carbon\Carbon::parse($permit->issue_date)->format('d M Y') }}</span>
                                    @endif
                                </li>
                                @endforeach
                            </ul>
                        </section>
                        @endif
                    </div>

                    {{-- TAB: TASKS --}}
                    <div x-show="activeTab === 'tasks'" x-transition.opacity class="pt-2 pb-5">
                        @if($project->tasks->count() === 0)
                        <x-ui.empty-state icon="fas fa-list-check" size="sm" title="Belum ada task" />
                        @else
                        <ul class="divide-y divide-[var(--border-subtle)] border border-[var(--border-subtle)] rounded-lg overflow-hidden">
                            @foreach($project->tasks->sortBy('due_date') as $task)
                            @php
                                $taskDone = $task->status === 'completed' || $task->completed_at !== null;
                                $taskOverdue = !$taskDone && $task->due_date && now()->gt($task->due_date);
                            @endphp
                            <li class="flex items-start gap-3 px-4 py-3 bg-[var(--surface-elevated)] hover:bg-[var(--surface-cool)] transition-colors">
                                <span class="flex-shrink-0 w-5 h-5 rounded-full border-2 mt-0.5 inline-flex items-center justify-center
                                    {{ $taskDone ? 'border-[var(--apple-green)] bg-[var(--apple-green)]' : 'border-[var(--border-subtle)]' }}">
                                    @if($taskDone)
                                    <i class="fas fa-check text-white text-[8px]" aria-hidden="true"></i>
                                    @endif
                                </span>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-semibold text-[var(--text-primary)] {{ $taskDone ? 'line-through text-[var(--text-tertiary)]' : '' }}">
                                        {{ $task->title }}
                                    </p>
                                    <div class="flex flex-wrap items-center gap-x-3 gap-y-0.5 mt-1">
                                        @if($task->assignedUser)
                                        <span class="text-[10px] text-[var(--text-tertiary)]">
                                            <i class="fas fa-user text-[9px] mr-0.5" aria-hidden="true"></i>{{ $task->assignedUser->name }}
                                        </span>
                                        @endif
                                        @if($task->due_date)
                                        <span class="text-[10px] {{ $taskOverdue ? 'text-[var(--apple-red)] font-semibold' : 'text-[var(--text-tertiary)]' }}">
                                            <i class="fas fa-calendar text-[9px] mr-0.5" aria-hidden="true"></i>
                                            {{ $task->due_date->format('d M Y') }}
                                            {{ $taskOverdue ? '(terlambat)' : '' }}
                                        </span>
                                        @endif
                                    </div>
                                </div>
                                @if(!$taskDone)
                                <span class="flex-shrink-0 portal-pill portal-pill--{{ $taskOverdue ? 'danger' : 'info' }} text-[10px]">
                                    {{ $taskOverdue ? 'Terlambat' : 'Aktif' }}
                                </span>
                                @endif
                            </li>
                            @endforeach
                        </ul>
                        @endif
                    </div>

                    {{-- TAB: DOCS --}}
                    <div x-show="activeTab === 'docs'" x-transition.opacity class="pt-2 pb-5">
                        @if($project->documents->count() === 0)
                        <x-ui.empty-state icon="fas fa-file-arrow-up" size="sm" title="Belum ada dokumen" />
                        @else
                        <ul class="divide-y divide-[var(--border-subtle)] border border-[var(--border-subtle)] rounded-lg overflow-hidden">
                            @foreach($project->documents as $doc)
                            @php
                                $fileIcon = str_contains($doc->mime_type ?? '', 'pdf') ? 'fa-file-pdf'
                                          : (str_contains($doc->mime_type ?? '', 'image') ? 'fa-file-image' : 'fa-file');
                            @endphp
                            <li class="flex items-center gap-3 px-4 py-3 bg-[var(--surface-elevated)] hover:bg-[var(--surface-cool)] transition-colors">
                                <i class="fas {{ $fileIcon }} text-base text-[var(--text-tertiary)] flex-shrink-0" aria-hidden="true"></i>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-semibold text-[var(--text-primary)] truncate">{{ $doc->document_type ?? $doc->file_name }}</p>
                                    <p class="text-[10px] text-[var(--text-tertiary)] mt-0.5">{{ $doc->created_at->format('d M Y') }}</p>
                                </div>
                            </li>
                            @endforeach
                        </ul>
                        @endif
                    </div>

                    {{-- TAB: ACTIVITY --}}
                    <div x-show="activeTab === 'activity'" x-transition.opacity class="pt-2 pb-5">
                        @if(count($timelineItems) === 0)
                        <x-ui.empty-state icon="fas fa-clock-rotate-left" size="sm" title="Belum ada aktivitas" />
                        @else
                        <x-ui.timeline :items="$timelineItems" />
                        @endif
                    </div>
                </x-ui.tabs>
            </div>
        </div>
    </div>

    {{-- SIDEBAR --}}
    <aside class="lg:col-span-1 space-y-4">

        {{-- Progress card --}}
        <div class="bg-[var(--surface-elevated)] border border-[var(--border-subtle)] rounded-xl px-4 py-4">
            <span class="portal-eyebrow"><i class="fas fa-bullseye text-[9px]" aria-hidden="true"></i> Progres</span>
            <div class="mt-3 flex items-center justify-center">
                <div class="relative w-24 h-24">
                    <svg class="w-full h-full -rotate-90" viewBox="0 0 36 36" aria-hidden="true">
                        <circle cx="18" cy="18" r="15.9" fill="none" stroke="var(--surface-sunken)" stroke-width="3"/>
                        <circle cx="18" cy="18" r="15.9" fill="none" stroke="{{ $progressColor }}" stroke-width="3"
                                stroke-dasharray="{{ $progress }} {{ 100 - $progress }}"
                                stroke-linecap="round"/>
                    </svg>
                    <div class="absolute inset-0 flex flex-col items-center justify-center">
                        <span class="text-xl font-bold tabular-nums" style="color: {{ $progressColor }}">{{ $progress }}%</span>
                    </div>
                </div>
            </div>
            <div class="mt-3 grid grid-cols-2 gap-2 text-center">
                <div>
                    <p class="text-lg font-bold text-[var(--text-primary)] tabular-nums">{{ $completedTasks }}</p>
                    <p class="text-[10px] text-[var(--text-tertiary)]">Task selesai</p>
                </div>
                <div>
                    <p class="text-lg font-bold text-[var(--text-primary)] tabular-nums">{{ $totalTasks - $completedTasks }}</p>
                    <p class="text-[10px] text-[var(--text-tertiary)]">Tersisa</p>
                </div>
            </div>
        </div>

        {{-- Related application --}}
        @if($project->permitApplication)
        <div class="bg-[var(--surface-elevated)] border border-[var(--border-subtle)] rounded-xl px-4 py-4">
            <span class="portal-eyebrow"><i class="fas fa-link text-[9px]" aria-hidden="true"></i> Permohonan Asal</span>
            <div class="mt-2">
                <p class="portal-mono text-sm font-semibold text-[var(--text-primary)]">{{ $project->permitApplication->application_number }}</p>
                @if($project->permitApplication->permitType)
                <p class="text-xs text-[var(--text-secondary)] mt-0.5">{{ $project->permitApplication->permitType->name }}</p>
                @endif
                <a href="{{ route('client.applications.show', $project->permitApplication->id) }}"
                   class="mt-2 inline-flex items-center gap-1.5 text-xs font-semibold text-[var(--client-primary)] hover:underline">
                    Lihat permohonan <i class="fas fa-arrow-right text-[9px]" aria-hidden="true"></i>
                </a>
            </div>
        </div>
        @endif

        {{-- Mini timeline --}}
        @if(count($timelineItems) > 0)
        <div class="bg-[var(--surface-elevated)] border border-[var(--border-subtle)] rounded-xl px-4 py-4 hidden lg:block">
            <span class="portal-eyebrow"><i class="fas fa-clock-rotate-left text-[9px]" aria-hidden="true"></i> Aktivitas Terbaru</span>
            <div class="mt-3">
                <x-ui.timeline :items="array_slice($timelineItems, 0, 4)" />
            </div>
        </div>
        @endif
    </aside>
</div>
</div>
