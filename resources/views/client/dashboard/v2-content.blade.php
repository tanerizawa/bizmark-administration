{{--
    Dashboard content body — Portal v2.
    Two-column desktop, stacked mobile:
      LEFT  → Pipeline kanban-lite + Quick actions
      RIGHT → Activity feed + Recent documents
--}}
@php
    $profileCompletionValue = (int) ($profileCompletion ?? 0);
    $profileMilestoneReached = $profileCompletionValue >= 75;
    $step1Done = ($profileComplete ?? false) || $profileMilestoneReached;
    $step1Started = $profileCompletionValue > 0;
    $step2Done = $projects->count() > 0;
    $step3Done = $uploadedDocuments > 0;
    $stepsCompleted = (int)$step1Done + (int)$step2Done + (int)$step3Done;
    $stepsTotal = 3;
    $milestonePercent = (int) round(($stepsCompleted / $stepsTotal) * 100);
    $progressPercent = $milestonePercent;
    $step1Status = $step1Done ? 'done' : ($step1Started ? 'in_progress' : 'pending');
    $progressPillTone = $progressPercent >= 67 ? 'success' : ($progressPercent >= 34 ? 'info' : 'neutral');
    $progressBarColor = $progressPercent >= 67 ? 'var(--apple-teal)' : 'var(--client-primary)';

    $onboardingSteps = [
        ['num'=>1,'done'=>$step1Done,'status'=>$step1Status,'enabled'=>true,'title'=>'Lengkapi Profil','desc'=>$profileCompletionValue > 0 ? ($profileMilestoneReached ? $profileCompletionValue . '% data inti akun sudah siap' : $profileCompletionValue . '% data akun sudah terisi') : 'Tambahkan data usaha dan PIC utama','url'=>route('client.profile.edit'),'cta'=>'Buka Profil'],
        ['num'=>2,'done'=>$step2Done,'status'=>$step2Done ? 'done' : ($step1Done ? 'ready' : 'locked'),'enabled'=>$step1Done,'title'=>'Ajukan Permohonan','desc'=>$step1Done ? 'Pilih layanan yang ingin diproses dari katalog' : 'Lengkapi profil terlebih dahulu agar pengajuan tidak tersendat','url'=>$step1Done ? route('client.services.index') : route('client.profile.edit'),'cta'=>$step1Done ? 'Pilih Layanan' : 'Lengkapi Profil'],
        ['num'=>3,'done'=>$step3Done,'status'=>$step3Done ? 'done' : (($step2Done && ($pendingDocuments ?? 0) > 0) ? 'ready' : 'locked'),'enabled'=>$step2Done && ($pendingDocuments ?? 0) > 0,'title'=>'Upload Dokumen','desc'=>($pendingDocuments ?? 0) > 0 ? $pendingDocuments . ' dokumen menunggu unggahan Anda' : 'Dokumen akan aktif saat ada permintaan dari tim Bizmark','url'=>route('client.documents.index'),'cta'=>'Buka Dokumen'],
    ];

    // Pipeline kanban: group projects by stage
    $pipelineColumns = [
        ['key' => 'submitted',    'title' => 'Diajukan',     'icon' => 'fas fa-paper-plane',   'color' => 'var(--apple-blue)',
         'projects' => $projects->filter(fn($p) => $p->status && in_array($p->status->name, ['Draft', 'Diajukan']))->take(3)],
        ['key' => 'review',       'title' => 'Verifikasi',   'icon' => 'fas fa-magnifying-glass-chart', 'color' => 'var(--client-primary)',
         'projects' => $projects->filter(fn($p) => $p->status && in_array($p->status->name, ['Dalam Proses', 'Sedang Diproses']))->take(3)],
        ['key' => 'incomplete',   'title' => 'Perlu Aksi',   'icon' => 'fas fa-circle-exclamation', 'color' => 'var(--apple-orange)',
         'projects' => $projects->filter(fn($p) => $p->status && $p->status->name === 'Dokumen Kurang')->take(3)],
        ['key' => 'completed',    'title' => 'Selesai',      'icon' => 'fas fa-check-circle',  'color' => 'var(--apple-green)',
         'projects' => $projects->filter(fn($p) => $p->status && $p->status->name === 'Selesai')->take(3)],
    ];

    // Activity feed: combine recent documents + tasks
    $activities = collect();
    foreach ($recentDocuments as $doc) {
        $activities->push([
            'icon' => 'fas fa-file-arrow-up',
            'color' => 'var(--client-primary)',
            'text' => 'Dokumen <strong>' . e($doc->document_name ?? $doc->name ?? 'baru') . '</strong> diunggah',
            'time' => $doc->created_at,
            'url' => null,
        ]);
    }
    foreach ($upcomingDeadlines as $task) {
        $activities->push([
            'icon' => 'fas fa-clock',
            'color' => 'var(--apple-orange)',
            'text' => 'Deadline: <strong>' . e($task->title ?? 'Tugas') . '</strong>',
            'time' => $task->due_date,
            'url' => null,
        ]);
    }
    $activities = $activities->sortByDesc('time')->take(8);
@endphp

<div class="px-4 lg:px-8 max-w-[1400px] mx-auto py-6 space-y-6">

    {{-- ─── 1. ONBOARDING STEPPER (collapsible, hidden when 100%) ─── --}}
    @if($stepsCompleted < 3)
    <section class="bg-[var(--surface-elevated)] border border-[var(--border-subtle)] rounded-xl px-5 pt-6 pb-4"
             aria-label="Panduan memulai">
        <div class="flex items-center justify-between gap-4 mb-4">
            <div>
                <span class="portal-eyebrow"><i class="fas fa-rocket text-[9px]" aria-hidden="true"></i> Mulai cepat</span>
                <h2 class="mt-1.5 text-base font-semibold text-[var(--text-primary)] leading-tight">
                    Langkah 1 dari 3: Lengkapi Profil Anda
                </h2>
                <p class="mt-1 text-xs text-[var(--text-secondary)]">
                    {{ $stepsCompleted }}/{{ $stepsTotal }} langkah tuntas
                </p>
            </div>
            <span class="portal-pill portal-pill--{{ $progressPillTone }} portal-pill--with-icon">
                <i class="fas fa-circle-half-stroke text-[10px]" aria-hidden="true"></i>
                {{ $stepsCompleted }}/{{ $stepsTotal }} langkah
            </span>
        </div>

        <div class="h-2 rounded-full bg-[var(--surface-cool)] overflow-hidden">
            <div class="h-full rounded-full bg-[var(--client-primary)] transition-all duration-500"
                 style="width: {{ $progressPercent }}%; background: {{ $progressBarColor }};"></div>
        </div>

        <div class="mt-4 space-y-3">
            @foreach($onboardingSteps as $step)
            <div class="relative flex gap-5 rounded-xl border p-4 transition-colors
                {{ $step['done']
                    ? 'border-[var(--apple-green)]/30 bg-[rgba(52,199,89,0.05)]'
                    : (($step['status'] ?? null) === 'in_progress'
                        ? 'border-[var(--client-primary)]/35 bg-[rgba(10,102,194,0.08)]'
                        : (($step['enabled'] ?? true)
                        ? 'border-[var(--client-primary)]/30 bg-[var(--client-primary-light)]'
                        : 'border-[var(--border-subtle)] bg-[var(--surface-cool)]')) }}
                {{ (($step['num'] ?? 0) === 3 && ($step['status'] ?? null) === 'locked') ? 'opacity-50 cursor-not-allowed' : '' }}">
                @if(!$loop->last)
                <span class="absolute left-[1.35rem] top-12 bottom-[-0.9rem] w-px"
                      style="background: {{ $step['done'] ? 'rgba(52,199,89,0.35)' : 'var(--border-subtle)' }};"></span>
                @endif

                <span class="relative z-[1] mt-0.5 inline-flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-full text-xs font-bold
                    {{ $step['done']
                        ? 'bg-[var(--apple-green)] text-white'
                        : (($step['status'] ?? null) === 'in_progress'
                            ? 'bg-white text-[var(--client-primary)] border-2 border-[var(--client-primary)]'
                            : (($step['enabled'] ?? true)
                            ? 'bg-[var(--client-primary)] text-white'
                            : 'bg-[var(--surface-sunken)] text-[var(--text-tertiary)]')) }}">
                    @if($step['done'])
                        <i class="fas fa-check text-[10px]" aria-hidden="true"></i>
                    @else
                        {{ $step['num'] }}
                    @endif
                </span>

                <div class="min-w-0 flex-1">
                    <div class="flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between">
                        <div>
                            <p class="text-sm font-semibold text-[var(--text-primary)] leading-tight">{{ $step['title'] }}</p>
                            <p class="text-xs text-[var(--text-secondary)] mt-1">{{ $step['desc'] }}</p>
                        </div>

                        @if($step['done'])
                            <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-1 text-xs font-semibold whitespace-nowrap"
                                  style="background: #DCFCE7; color: #166534; border: 1px solid rgba(22, 101, 52, 0.18);">
                                <i class="fas fa-circle-check text-[10px]" aria-hidden="true"></i> Selesai
                            </span>
                        @elseif(($step['status'] ?? null) === 'in_progress')
                            <span class="portal-pill portal-pill--info portal-pill--with-icon whitespace-nowrap">
                                <i class="fas fa-hourglass-half text-[10px]" aria-hidden="true"></i> Dalam progres
                            </span>
                        @elseif($step['enabled'] ?? true)
                            <a href="{{ $step['url'] }}"
                               class="inline-flex items-center gap-1.5 text-xs font-semibold text-[var(--client-primary)] hover:underline whitespace-nowrap">
                                {{ $step['cta'] }} <i class="fas fa-arrow-right text-[9px]" aria-hidden="true"></i>
                            </a>
                        @else
                            <span class="inline-flex items-center gap-1 text-xs text-[var(--text-tertiary)] whitespace-nowrap opacity-60">
                                <i class="fas fa-lock text-[10px]" aria-hidden="true"></i> Menunggu langkah sebelumnya
                            </span>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </section>
    @endif

    {{-- ─── 2. TWO-COLUMN: Pipeline + Activity ─── --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- LEFT (2/3): Pipeline kanban-lite --}}
        <section class="lg:col-span-2 bg-[var(--surface-elevated)] border border-[var(--border-subtle)] rounded-xl"
                 aria-label="Pipeline permohonan">
            <header class="px-5 py-4 border-b border-[var(--border-subtle)]">
                <div class="flex items-center justify-between gap-3">
                    <h2 class="text-base font-semibold text-[var(--text-primary)] leading-tight">Pipeline Permohonan</h2>
                    <a href="{{ route('client.projects.index') }}"
                       class="inline-flex items-center text-xs font-semibold text-[var(--client-primary)] hover:underline">
                        Lihat semua <i class="fas fa-arrow-right ml-1 text-[9px]" aria-hidden="true"></i>
                    </a>
                </div>
                <p class="mt-0.5 text-xs text-[var(--text-secondary)]">{{ $projects->count() }} total · {{ $activeProjects }} aktif</p>
            </header>

            @if($projects->count() === 0)
                <div class="portal-empty-state px-6 py-10 flex flex-col items-center text-center gap-4">
                    <div class="portal-empty-illustration" aria-hidden="true">
                        <span class="portal-empty-illustration__tile"></span>
                        <span class="portal-empty-illustration__tile"></span>
                        <span class="portal-empty-illustration__node"><i class="fas fa-folder-open"></i></span>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-[var(--text-primary)]">Belum ada proyek aktif</p>
                        <p class="text-xs text-[var(--text-secondary)] mt-1 max-w-sm">Semua permohonan yang Anda kirim akan muncul di pipeline ini, lengkap dengan status, progres verifikasi, dan dokumen yang perlu ditindaklanjuti.</p>
                    </div>
                    <a href="{{ route('client.services.index') }}"
                              class="inline-flex min-h-11 items-center justify-center gap-2 rounded-lg border border-[var(--client-primary-hover)]/20 bg-[var(--client-primary)] px-4 py-2.5 text-center text-sm font-semibold leading-none text-white shadow-[0_10px_24px_rgba(10,102,194,0.22)] transition-all hover:bg-[var(--client-primary-hover)] hover:shadow-[0_14px_30px_rgba(10,102,194,0.28)] whitespace-nowrap"
                              style="background: var(--client-primary); color: #ffffff; border-color: rgba(0, 65, 130, 0.2); box-shadow: 0 10px 24px rgba(10, 102, 194, 0.22);">
                        <i class="fas fa-layer-group text-xs" aria-hidden="true" style="color: #ffffff;"></i>
                        <span style="color: #ffffff;">Jelajahi Katalog Izin</span>
                        <i class="fas fa-arrow-right text-xs" aria-hidden="true" style="color: #ffffff;"></i>
                    </a>
                </div>
            @else
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-px bg-[var(--border-subtle)] rounded-b-xl overflow-hidden">
                    @foreach($pipelineColumns as $col)
                        <div class="bg-[var(--surface-elevated)] p-3 min-h-[200px]">
                            <div class="flex items-center gap-2 mb-3">
                                <span class="w-6 h-6 rounded-md inline-flex items-center justify-center text-[10px]"
                                      style="background: {{ $col['color'] }}1a; color: {{ $col['color'] }};">
                                    <i class="{{ $col['icon'] }}" aria-hidden="true"></i>
                                </span>
                                <h3 class="text-xs font-semibold text-[var(--text-primary)]">{{ $col['title'] }}</h3>
                                <span class="ml-auto text-[10px] font-bold tabular-nums text-[var(--text-tertiary)]">
                                    {{ $col['projects']->count() }}
                                </span>
                            </div>
                            <div class="space-y-2">
                                @forelse($col['projects'] as $project)
                                    <a href="{{ route('client.projects.show', $project->id) }}"
                                       class="block p-2.5 rounded-md bg-[var(--surface-cool)] border border-[var(--border-subtle)] hover:border-[var(--client-primary)] hover:bg-[var(--client-primary-light)] transition-colors group">
                                        <p class="text-xs font-semibold text-[var(--text-primary)] line-clamp-2 leading-snug group-hover:text-[var(--client-primary)]">
                                            {{ $project->name ?? 'Proyek #' . $project->id }}
                                        </p>
                                        @if($project->permitApplication?->permitType)
                                            <p class="mt-1 text-[10px] text-[var(--text-tertiary)] truncate">
                                                {{ $project->permitApplication->permitType->name }}
                                            </p>
                                        @endif
                                        <div class="mt-1.5 flex items-center justify-between">
                                            <span class="portal-mono text-[10px]">#{{ $project->id }}</span>
                                            @if($project->updated_at)
                                                <time class="text-[10px] text-[var(--text-tertiary)]">
                                                    {{ $project->updated_at->diffForHumans(null, true) }}
                                                </time>
                                            @endif
                                        </div>
                                    </a>
                                @empty
                                    <p class="text-[10px] text-[var(--text-tertiary)] italic px-1">Kosong</p>
                                @endforelse
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </section>

        {{-- RIGHT (1/3): Activity feed --}}
        <aside class="bg-[var(--surface-elevated)] border border-[var(--border-subtle)] rounded-xl"
               aria-label="Aktivitas terbaru">
            <header class="flex items-center justify-between gap-3 px-5 py-4 border-b border-[var(--border-subtle)]">
                <h2 class="text-base font-semibold text-[var(--text-primary)] leading-tight">Aktivitas</h2>
                <a href="{{ route('client.notifications.index') }}"
                   class="text-xs font-semibold text-[var(--client-primary)] hover:underline">
                    Semua →
                </a>
            </header>

            @if($activities->isEmpty())
                <div class="portal-empty-state px-5 py-10 text-center">
                    <div class="portal-empty-illustration portal-empty-illustration--warm mx-auto mb-3" aria-hidden="true">
                        <span class="portal-empty-illustration__tile"></span>
                        <span class="portal-empty-illustration__tile"></span>
                        <span class="portal-empty-illustration__node"><i class="fas fa-inbox"></i></span>
                    </div>
                    <p class="text-sm font-semibold text-[var(--text-primary)]">Belum ada aktivitas</p>
                    <p class="mt-1 text-xs text-[var(--text-secondary)] max-w-[260px] mx-auto">Riwayat dokumen, catatan tim Bizmark, dan pembaruan progres akan terkumpul di sini setelah Anda memulai permohonan pertama.</p>
                    <a href="{{ route('client.applications.create') }}"
                       class="mt-4 inline-flex items-center gap-2 rounded-lg border border-[var(--client-primary)]/20 bg-[var(--client-primary-light)] px-3.5 py-2 text-xs font-semibold text-[var(--client-primary)] hover:brightness-105 transition-all">
                        <i class="fas fa-plus text-[10px]" aria-hidden="true"></i>
                        Buat permohonan pertama
                    </a>
                </div>
            @else
                <ul class="divide-y divide-[var(--border-subtle)]">
                    @foreach($activities as $act)
                        <li class="px-5 py-3 flex items-start gap-3">
                            <span class="flex-shrink-0 w-7 h-7 rounded-full inline-flex items-center justify-center text-[11px] mt-0.5"
                                  style="background: {{ $act['color'] }}1a; color: {{ $act['color'] }};">
                                <i class="{{ $act['icon'] }}" aria-hidden="true"></i>
                            </span>
                            <div class="min-w-0 flex-1">
                                <p class="text-xs text-[var(--text-primary)] leading-relaxed">
                                    {!! $act['text'] !!}
                                </p>
                                @if($act['time'])
                                    <time class="text-[10px] text-[var(--text-tertiary)] mt-0.5 block">
                                        {{ \Carbon\Carbon::parse($act['time'])->diffForHumans() }}
                                    </time>
                                @endif
                            </div>
                        </li>
                    @endforeach
                </ul>
            @endif
        </aside>

    </div>

    {{-- ─── 3. QUICK ACTIONS ─── --}}
    <section aria-label="Aksi cepat">
        <h2 class="text-base font-semibold text-[var(--text-primary)] mb-3">Aksi Cepat</h2>
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
            @foreach([
                ['icon'=>'fas fa-plus','title'=>$step1Done ? 'Permohonan Baru' : 'Lengkapi Profil','desc'=>$step1Done ? 'Mulai izin baru' : 'Wajib sebelum ajukan izin','url'=>$step1Done ? route('client.applications.create') : route('client.profile.edit'),'color'=>'var(--client-primary)','enabled'=>true,'badge'=>$step1Done ? null : 'Langkah 1'],
                ['icon'=>'fas fa-upload','title'=>'Upload Dokumen','desc'=>($pendingDocuments ?? 0) > 0 ? 'Unggah ' . $pendingDocuments . ' dokumen' : ($step2Done ? 'Menunggu permintaan dokumen dari tim Bizmark' : 'Aktif setelah ada pengajuan pertama'),'url'=>route('client.documents.index'),'color'=>'var(--apple-orange)','enabled'=>$step2Done && ($pendingDocuments ?? 0) > 0,'badge'=>($pendingDocuments ?? 0) > 0 ? $pendingDocuments . ' menunggu' : null],
                ['icon'=>'fas fa-search','title'=>'Cek Status','desc'=>$step2Done ? 'Pantau progres permohonan' : 'Aktif setelah ada pengajuan pertama','url'=>route('client.applications.index'),'color'=>'var(--apple-green)','enabled'=>$step2Done,'badge'=>null],
                ['icon'=>'fas fa-layer-group','title'=>'Katalog','desc'=>'Jelajah layanan izin','url'=>route('client.services.index'),'color'=>'var(--viz-3)','enabled'=>true,'badge'=>null],
            ] as $qa)
            @if($qa['enabled'])
            <a href="{{ $qa['url'] }}"
               class="portal-lift bg-[var(--surface-elevated)] border border-[var(--border-subtle)] rounded-xl p-4 group">
            @else
            <div class="portal-action-card--locked relative rounded-xl border border-[var(--border-subtle)] bg-[var(--surface-cool)] p-4 cursor-not-allowed">
            @endif
                @if(!$qa['enabled'])
                <span class="portal-action-lock" aria-hidden="true">
                    <i class="fas fa-lock text-[9px]"></i>
                </span>
                @endif
                                <span class="w-10 h-10 rounded-lg inline-flex items-center justify-center mb-2.5"
                                            style="background: {{ $qa['enabled'] ? ($qa['color'] . '1a') : 'var(--surface-sunken)' }}; color: {{ $qa['enabled'] ? $qa['color'] : 'var(--text-tertiary)' }};">
                    <i class="{{ $qa['icon'] }} text-sm" aria-hidden="true"></i>
                </span>
                <div class="flex items-start justify-between gap-2">
                                        <p class="text-sm font-semibold {{ $qa['enabled'] ? 'text-[var(--text-primary)]' : 'text-[var(--text-secondary)]' }} leading-tight">{{ $qa['title'] }}</p>
                    @if($qa['badge'])
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold whitespace-nowrap"
                          style="background: var(--surface-sunken); color: var(--text-secondary); border: 1px solid var(--border-subtle);">
                        {{ $qa['badge'] }}
                    </span>
                    @endif
                </div>
                                <p class="mt-0.5 text-xs {{ $qa['enabled'] ? 'text-[var(--text-secondary)]' : 'text-[var(--text-tertiary)]' }}">{{ $qa['desc'] }}</p>
            @if($qa['enabled'])
            </a>
            @else
            </div>
            @endif
            @endforeach
        </div>
    </section>

</div>
