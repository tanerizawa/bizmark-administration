{{--
    Dashboard hero — Portal v2 (high-tech redesign).
    Uses design tokens, visual signature (glow orb, accent line, archipelago).
    Gated by config('portal_redesign.enabled').
--}}
@php
    $now = now();
    $hour = (int) $now->format('H');
    $greeting = $hour < 11 ? 'Selamat pagi' : ($hour < 15 ? 'Selamat siang' : ($hour < 18 ? 'Selamat sore' : 'Selamat malam'));
    $firstName = \Illuminate\Support\Str::of($client->name ?? '')->before(' ')->title();
    $profileCompletionValue = (int) ($profileCompletion ?? 0);
    $heroProgressStyle = $profileCompletionValue >= 75
        ? 'background: rgba(16, 185, 129, 0.16); color: rgba(236, 253, 245, 0.98); border-color: rgba(16, 185, 129, 0.35);'
        : 'background: rgba(255,255,255,0.12); color: rgba(255,255,255,0.98); border-color: rgba(255,255,255,0.22);';

    // Compute "what needs attention"
    $attention = collect();
    if (($pendingDocuments ?? 0) > 0) {
        $attention->push("{$pendingDocuments} dokumen menunggu Anda upload");
    }
    if (($upcomingDeadlines ?? collect())->count() > 0) {
        $attention->push($upcomingDeadlines->count() . ' permohonan dengan deadline 7 hari');
    }
    if (isset($unreadAdminNotes) && $unreadAdminNotes > 0) {
        $attention->push("{$unreadAdminNotes} pesan baru dari tim Bizmark");
    }

    // Compliance: null when no documents requested (avoid 0/0 undefined state)
    $complianceScore = $totalDocuments > 0 ? (int) $documentCompletion : null;

    $heroPrimaryAction = ! ($profileComplete ?? false)
        ? [
            'url' => route('client.profile.edit'),
            'label' => 'Lengkapi Profil',
            'icon' => 'fa-id-card',
            'note' => $profileCompletionValue . '% profil terisi',
        ]
        : ($projects->count() === 0
            ? [
                'url' => route('client.services.index'),
                'label' => 'Jelajahi Katalog',
                'icon' => 'fa-layer-group',
                'note' => 'Pilih izin pertama Anda',
            ]
            : (($pendingDocuments ?? 0) > 0
                ? [
                    'url' => route('client.documents.index'),
                    'label' => 'Upload Dokumen',
                    'icon' => 'fa-upload',
                    'note' => $pendingDocuments . ' dokumen menunggu',
                ]
                : [
                    'url' => route('client.applications.create'),
                    'label' => 'Ajukan Permohonan',
                    'icon' => 'fa-plus',
                    'note' => 'Mulai permohonan baru',
                ]));
@endphp

<section class="portal-hero portal-accent-line border-b border-[var(--border-subtle)]"
         style="background: linear-gradient(145deg, color-mix(in oklab, var(--client-primary) 82%, #041425) 0%, color-mix(in oklab, var(--client-primary) 52%, #02101d) 58%, #051523 100%); color: #fff;"
         aria-labelledby="dashboard-hero-title">

    {{-- Decorative glow orb (desktop only) --}}
    <div class="portal-glow-orb portal-glow-orb--tr hidden lg:block" aria-hidden="true"></div>

    {{-- Archipelago watermark --}}
    <svg class="portal-archipelago hidden md:block" viewBox="0 0 1000 360" preserveAspectRatio="xMidYMid meet" aria-hidden="true">
        <g fill="none" stroke="currentColor" stroke-width="1" stroke-linejoin="round" stroke-linecap="round">
            <path d="M70,200 Q90,150 130,120 Q175,100 210,130 Q230,165 215,200 Q200,235 165,250 Q120,260 90,235 Z"/>
            <path d="M260,235 Q320,225 390,238 Q420,245 415,258 Q380,268 320,262 Q280,258 258,250 Z"/>
            <path d="M380,110 Q430,95 480,110 Q510,140 500,180 Q480,210 440,210 Q400,200 380,170 Q370,140 380,110 Z"/>
            <path d="M580,130 Q610,115 625,140 Q620,165 610,180 Q625,200 615,230 Q600,245 585,230 Q580,205 590,185 Q575,165 580,130 Z"/>
            <path d="M740,150 Q800,135 870,150 Q920,170 920,205 Q900,230 850,230 Q790,225 750,210 Q725,185 740,150 Z"/>
        </g>
    </svg>

    <div class="px-4 lg:px-8 py-6 lg:py-8 max-w-[1400px] mx-auto">

        {{-- ── Greeting + Eyebrow + CTA ── --}}
        <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div class="min-w-0 flex-1">
                <span class="portal-eyebrow mb-2.5" style="background: rgba(255,255,255,0.15); color: rgba(255,255,255,0.95); border-color: rgba(255,255,255,0.25);">
                    <i class="fas fa-circle-check text-[9px]" aria-hidden="true"></i>
                    Dashboard · {{ $now->translatedFormat('l, j F Y') }}
                </span>

                <h1 id="dashboard-hero-title"
                    class="text-2xl lg:text-3xl font-bold tracking-tight text-white leading-tight">
                    {{ $greeting }}{{ $firstName ? ', ' . e($firstName) : '' }}.
                </h1>

                @if($attention->isNotEmpty())
                    <p class="mt-2 max-w-2xl text-sm font-normal leading-6 text-white/80">
                        Hari ini: {{ $attention->implode(' · ') }}.
                    </p>
                @else
                    <p class="mt-2 max-w-2xl text-sm font-normal leading-6 text-white/80">
                        Semua terkendali. Gunakan dashboard ini untuk memulai izin baru, memantau progres, dan menindaklanjuti dokumen tanpa berpindah menu.
                    </p>
                @endif
            </div>

            <div class="flex flex-col items-stretch gap-2 w-full sm:flex-row sm:items-center sm:w-auto lg:justify-end lg:flex-shrink-0">
                <span class="inline-flex items-center justify-center gap-2 rounded-lg border px-3 py-2 text-xs font-semibold whitespace-nowrap backdrop-blur"
                      style="{{ $heroProgressStyle }}">
                    <i class="fas fa-compass text-[10px]" aria-hidden="true"></i>
                    {{ $heroPrimaryAction['note'] }}
                </span>
                <a href="{{ $heroPrimaryAction['url'] }}"
                         class="inline-flex min-h-11 items-center justify-center gap-2 rounded-lg border border-white/35 bg-white px-4 py-2.5 text-center text-sm font-semibold leading-none text-[var(--client-primary-hover)] shadow-[0_10px_28px_rgba(4,16,29,0.18)] transition-all active:scale-[0.98] hover:bg-slate-50 hover:text-[var(--client-primary-hover)] whitespace-nowrap"
                         style="background: #ffffff; color: var(--client-primary-hover); border-color: rgba(255, 255, 255, 0.4); box-shadow: 0 10px 28px rgba(4, 16, 29, 0.18);">
                    <i class="fas {{ $heroPrimaryAction['icon'] }} text-xs" aria-hidden="true" style="color: var(--client-primary-hover);"></i>
                    <span style="color: var(--client-primary-hover);">{{ $heroPrimaryAction['label'] }}</span>
                </a>
            </div>
        </div>

        {{-- ── Stat Strip (4 cards, mobile horizontal scroll) ── --}}
        <div class="portal-stat-strip mt-6">

            {{-- Active Projects --}}
            <div class="portal-lift bg-white/10 backdrop-blur border border-white/15 rounded-xl px-5 py-4">
                <div class="flex items-start justify-between gap-3">
                    <div class="min-w-0">
                        <p class="text-[11px] uppercase tracking-wider font-semibold text-white/70">Proyek Aktif</p>
                        @if(($activeProjects ?? 0) > 0)
                        <p class="mt-1.5 text-3xl font-bold tabular-nums text-white leading-none">
                            {{ $activeProjects ?? 0 }}
                        </p>
                        <p class="mt-1.5 text-xs text-white/70">
                            {{ $projects->count() > 0 ? 'dari ' . $projects->count() . ' total' : 'Belum ada proyek dimulai' }}
                        </p>
                        @else
                        <a href="{{ route('client.applications.create') }}"
                           class="mt-1.5 inline-flex items-center gap-1.5 rounded-md border border-white/25 bg-white/10 px-3 py-1.5 text-xs font-semibold text-white/95 transition-all hover:bg-white/18 hover:border-white/40">
                            <i class="fas fa-plus text-[10px]" aria-hidden="true"></i>
                            Buat Proyek
                        </a>
                        <div class="portal-stat-placeholder mt-3" aria-hidden="true">
                            <span></span><span></span><span></span>
                        </div>
                        <p class="mt-2 text-xs text-white/75">Ajukan proyek pertama Anda untuk menghidupkan ringkasan ini.</p>
                        @endif
                    </div>
                    <span class="flex h-10 w-10 flex-shrink-0 items-center justify-center self-start rounded-xl bg-white/15 shadow-[inset_0_1px_0_rgba(255,255,255,0.08)]">
                        <i class="fas fa-diagram-project text-sm leading-none text-white" aria-hidden="true"></i>
                    </span>
                </div>
            </div>

            {{-- Completed --}}
            <div class="portal-lift bg-white/10 backdrop-blur border border-white/15 rounded-xl px-5 py-4">
                <div class="flex items-start justify-between gap-3">
                    <div class="min-w-0">
                        <p class="text-[11px] uppercase tracking-wider font-semibold text-white/70">Selesai</p>
                        @if(($completedProjects ?? 0) > 0)
                        <p class="mt-1.5 text-3xl font-bold tabular-nums text-white leading-none">
                            {{ $completedProjects ?? 0 }}
                        </p>
                        <p class="mt-1.5 text-xs text-white/70">
                            {{ $projects->count() > 0 ? round(($completedProjects / $projects->count()) * 100) : 0 }}% sukses
                        </p>
                        @else
                        <p class="mt-1.5 text-lg font-semibold text-white leading-none">Belum ada capaian</p>
                        <div class="portal-stat-placeholder mt-3" aria-hidden="true">
                            <span></span><span></span><span></span>
                        </div>
                        <p class="mt-2 text-xs text-white/75">Penyelesaian proyek pertama akan langsung dirangkum di kartu ini.</p>
                        @endif
                    </div>
                    <span class="flex h-10 w-10 flex-shrink-0 items-center justify-center self-start rounded-xl bg-white/15 shadow-[inset_0_1px_0_rgba(255,255,255,0.08)]">
                        <i class="fas fa-check text-sm leading-none text-white" aria-hidden="true"></i>
                    </span>
                </div>
            </div>

            {{-- Investment --}}
            <div class="portal-lift bg-white/10 backdrop-blur border border-white/15 rounded-xl px-5 py-4">
                <div class="flex items-start justify-between gap-3">
                    <div class="min-w-0">
                        <p class="text-[11px] uppercase tracking-wider font-semibold text-white/70">Investasi</p>
                        <p class="mt-1.5 text-2xl font-bold tabular-nums text-white leading-none truncate">
                            {{ $investDisplay }}
                        </p>
                        <p class="mt-1.5 text-xs text-white/70">
                            {{ ($totalInvested ?? 0) > 0 ? 'Total nilai kontrak' : 'Nilai akan muncul setelah proyek berjalan' }}
                        </p>
                    </div>
                    <span class="flex h-10 w-10 flex-shrink-0 items-center justify-center self-start rounded-xl bg-white/15 shadow-[inset_0_1px_0_rgba(255,255,255,0.08)]">
                        <i class="fas fa-money-bill-wave text-sm leading-none text-white" aria-hidden="true"></i>
                    </span>
                </div>
            </div>

            {{-- Compliance --}}
            <div class="portal-lift bg-white/10 backdrop-blur border border-white/15 rounded-xl px-5 py-4">
                <div class="flex items-start justify-between gap-3">
                    <div class="min-w-0">
                        <p class="text-[11px] uppercase tracking-wider font-semibold text-white/70">Kelengkapan</p>
                        @if($complianceScore !== null)
                        <p class="mt-1.5 text-3xl font-bold tabular-nums text-white leading-none">
                            {{ $complianceScore }}<span class="text-base text-white/60">%</span>
                        </p>
                        <p class="mt-1.5 text-xs text-white/70">
                            {{ $uploadedDocuments ?? 0 }}/{{ $totalDocuments }} dokumen
                        </p>
                        @else
                        <p class="mt-1.5 text-lg font-semibold text-white leading-none">Belum dimulai</p>
                        <p class="mt-1.5 text-xs text-white/70">Persentase muncul saat ada dokumen yang diminta</p>
                        @endif
                    </div>
                    {{-- Donut indicator --}}
                    @php
                        $r = 14; $c = 2 * pi() * $r;
                        $scoreForDonut = $complianceScore ?? 0;
                        $offset = $c - ($scoreForDonut / 100) * $c;
                        $donutColor = $complianceScore === null ? 'var(--border-subtle)'
                            : ($complianceScore >= 80 ? 'var(--apple-green)' : ($complianceScore >= 50 ? 'var(--apple-orange)' : 'var(--apple-red)'));
                    @endphp
                    <svg class="flex-shrink-0" width="40" height="40" viewBox="0 0 40 40" aria-hidden="true">
                        <circle cx="20" cy="20" r="{{ $r }}" fill="none" stroke="rgba(255,255,255,0.2)" stroke-width="3"/>
                        @if($complianceScore !== null)
                        <circle cx="20" cy="20" r="{{ $r }}" fill="none" stroke="{{ $donutColor }}" stroke-width="3"
                                stroke-dasharray="{{ $c }}" stroke-dashoffset="{{ $offset }}"
                                transform="rotate(-90 20 20)" stroke-linecap="round"/>
                        @endif
                    </svg>
                </div>
            </div>

        </div>

    </div>
</section>
