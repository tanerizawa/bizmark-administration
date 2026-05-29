{{-- Services Show (Rekomendasi Perizinan) — Portal v2 RAB Edition --}}
@php
    use Illuminate\Support\Str;

    $contact          = (array) data_get(config('landing_metrics'), 'contact', []);
    $supportWhatsapp  = $contact['whatsapp_link'] ?? 'https://wa.me/6283879602855';

    // ─── Helper closures ─────────────────────────────────────────────────────
    $fmtRp = function (int $val): string {
        if ($val <= 0) return 'Gratis';
        if ($val >= 1_000_000_000) return 'Rp ' . number_format($val / 1_000_000_000, 1) . 'M';
        if ($val >= 1_000_000)     return 'Rp ' . number_format($val / 1_000_000, 1) . 'jt';
        return 'Rp ' . number_format($val);
    };
    $fmtRange = function (int $min, int $max) use ($fmtRp): string {
        if ($min <= 0 && $max <= 0) return 'Gratis';
        if ($min === $max) return $fmtRp($min);
        return $fmtRp($min) . ' – ' . $fmtRp($max);
    };

    // ─── Context labels ───────────────────────────────────────────────────────
    $scaleLabels   = ['mikro' => 'Usaha Mikro', 'kecil' => 'Usaha Kecil', 'menengah' => 'Usaha Menengah', 'besar' => 'Usaha Besar'];
    $locationLabels = ['perkotaan' => 'Area Perkotaan', 'pedesaan' => 'Area Pedesaan', 'kawasan_industri' => 'Kawasan Industri'];
    $scaleLabel    = $scaleLabels[$businessScale ?? ''] ?? 'Umum';
    $locationLabel = $locationLabels[$locationType ?? ''] ?? 'Lokasi Umum';

    // ─── Permit data ──────────────────────────────────────────────────────────
    $allPermits    = collect($recommendation->recommended_permits ?? []);
    $totalPermits  = $allPermits->count();
    $confidencePct = max(5, min(100, round(($recommendation->confidence_score ?? 0) * 100)));

    // Phase metadata
    $phases = [
        'foundational' => ['n' => 1, 'label' => 'Tahap 1 — Legalitas Dasar',          'short' => 'Legalitas',   'color' => 'sky',    'icon' => 'fa-id-card-alt'],
        'environmental'=> ['n' => 2, 'label' => 'Tahap 2 — Tata Ruang & Lingkungan',  'short' => 'Lingkungan',  'color' => 'emerald','icon' => 'fa-leaf'],
        'technical'    => ['n' => 3, 'label' => 'Tahap 3 — Teknis & Bangunan',        'short' => 'Teknis',      'color' => 'amber',  'icon' => 'fa-hard-hat'],
        'operational'  => ['n' => 4, 'label' => 'Tahap 4 — Operasional & Standar',    'short' => 'Operasional', 'color' => 'violet', 'icon' => 'fa-cog'],
        'sectoral'     => ['n' => 5, 'label' => 'Tahap 5 — Izin Sektoral Khusus',     'short' => 'Sektoral',    'color' => 'rose',   'icon' => 'fa-star'],
    ];
    $catOrder = ['foundational', 'environmental', 'technical', 'operational', 'sectoral'];

    // Group permits by category preserving phase order
    $grouped = [];
    foreach ($catOrder as $cat) {
        $perms = $allPermits->filter(fn($p) => ($p['category'] ?? 'operational') === $cat)->values();
        if ($perms->isNotEmpty()) $grouped[$cat] = $perms;
    }
    // Any uncategorized goes into operational
    $uncategorized = $allPermits->filter(fn($p) => !in_array($p['category'] ?? '', $catOrder))->values();
    if ($uncategorized->isNotEmpty()) {
        $grouped['operational'] = (isset($grouped['operational']) ? $grouped['operational'] : collect())->merge($uncategorized)->values();
    }

    // ─── Cost totals ──────────────────────────────────────────────────────────
    $aiCost      = $recommendation->total_estimated_cost ?? null;
    $totalGovMin = (int) ($aiCost['government_fees']['min'] ?? $allPermits->sum(fn($p) => is_array($p['government_fee'] ?? null) ? ($p['government_fee']['min'] ?? 0) : 0));
    $totalGovMax = (int) ($aiCost['government_fees']['max'] ?? $allPermits->sum(fn($p) => is_array($p['government_fee'] ?? null) ? ($p['government_fee']['max'] ?? 0) : 0));
    $totalConMin = (int) ($aiCost['consultant_fees']['min'] ?? $allPermits->sum(fn($p) => is_array($p['consultant_fee'] ?? null) ? ($p['consultant_fee']['min'] ?? 0) : 0));
    $totalConMax = (int) ($aiCost['consultant_fees']['max'] ?? $allPermits->sum(fn($p) => is_array($p['consultant_fee'] ?? null) ? ($p['consultant_fee']['max'] ?? 0) : 0));
    $grandMin    = (int) ($aiCost['grand_total']['min'] ?? ($totalGovMin + $totalConMin));
    $grandMax    = (int) ($aiCost['grand_total']['max'] ?? ($totalGovMax + $totalConMax));
    $docPrepMin  = (int) ($costBreakdown['document_preparation']['min'] ?? 0);
    $docPrepMax  = (int) ($costBreakdown['document_preparation']['max'] ?? 0);

    // ─── Timeline ─────────────────────────────────────────────────────────────
    $tlData    = $recommendation->estimated_timeline ?? null;
    $tlMin     = is_array($tlData) ? ($tlData['minimum_days'] ?? null) : null;
    $tlMax     = is_array($tlData) ? ($tlData['maximum_days'] ?? null) : null;
    $tlSummary = is_array($tlData)
        ? ($tlData['summary'] ?? ($recommendation->total_estimated_timeline ?? '–'))
        : ($recommendation->total_estimated_timeline ?? '–');
    $critPath  = is_array($tlData) ? ($tlData['critical_path'] ?? []) : [];

    // ─── Risk & Docs ──────────────────────────────────────────────────────────
    $risk           = $recommendation->risk_assessment ?? null;
    $riskLevel      = $risk['level'] ?? 'medium';
    $riskFactors    = $risk['factors'] ?? ($recommendation->risk_factors ?? []);
    $riskMitigation = $risk['mitigation'] ?? [];
    $commonPitfalls = $risk['common_pitfalls'] ?? [];
    $requiredDocs   = $recommendation->required_documents ?? [];
    $nextSteps      = $recommendation->next_steps ?? [];
    $limitations    = $recommendation->limitations ?? '';
    $riskClass      = $recommendation->risk_classification ?? 'menengah_rendah';
    $complexityScore = $recommendation->complexity_score ?? 5;

    $riskColors = ['low' => 'green', 'medium' => 'amber', 'high' => 'red'];
    $riskColor  = $riskColors[$riskLevel] ?? 'amber';
    $riskLabels = ['low' => 'Risiko Rendah', 'medium' => 'Risiko Menengah', 'high' => 'Risiko Tinggi'];
    $riskLabel  = $riskLabels[$riskLevel] ?? 'Risiko Menengah';
@endphp

<div x-data="{ tab: 'alur', expanded: {} }">

{{-- ─── HERO ─── --}}
<section class="portal-hero relative overflow-hidden border-b border-[var(--border-subtle)]"
         style="background: linear-gradient(135deg, var(--client-primary) 0%, color-mix(in oklab, var(--client-primary) 65%, #001020) 100%); color:#fff;"
         aria-label="Rekomendasi izin KBLI {{ $kbli->code }}">
    <div class="portal-glow-orb portal-glow-orb--tr hidden lg:block" aria-hidden="true"
         style="background: radial-gradient(circle at 50% 50%, rgba(255,255,255,0.18) 0%, transparent 70%);"></div>

    <div class="relative max-w-[1400px] mx-auto px-4 lg:px-8 py-5 lg:py-7">
        <a href="{{ route('client.services.index') }}"
           class="inline-flex items-center gap-1.5 text-white/70 hover:text-white text-xs mb-3 transition-colors">
            <i class="fas fa-arrow-left text-[9px]" aria-hidden="true"></i> Kembali ke Katalog
        </a>

        <div class="flex flex-col lg:flex-row lg:items-start justify-between gap-5">
            <div class="flex-1">
                <div class="flex items-center gap-2 flex-wrap mb-2">
                    <span class="portal-mono text-sm font-bold text-white/90">KBLI {{ $kbli->code }}</span>
                    <span class="portal-eyebrow" style="background: rgba(255,255,255,0.15); color: rgba(255,255,255,0.9); border-color: rgba(255,255,255,0.2);">
                        <i class="fas fa-robot text-[9px]" aria-hidden="true"></i> Analisis AI
                    </span>
                    @if($riskClass)
                    <span class="portal-eyebrow" style="background: rgba(255,255,255,0.12); color: rgba(255,255,255,0.85); border-color: rgba(255,255,255,0.2);">
                        {{ strtoupper(str_replace('_', ' ', $riskClass)) }}
                    </span>
                    @endif
                </div>
                <h1 class="text-xl font-bold text-white leading-tight mb-1">{{ $kbli->name }}</h1>
                <p class="text-sm text-white/70">{{ $kbli->description ?? '' }}</p>
            </div>

            <div class="bg-white/10 backdrop-blur border border-white/15 rounded-xl px-4 py-3 text-center flex-shrink-0 min-w-[100px]">
                <p class="text-3xl font-bold tabular-nums text-white">{{ $confidencePct }}%</p>
                <p class="text-[11px] text-white/70 mt-0.5">Akurasi AI</p>
            </div>
        </div>

        {{-- Context chips --}}
        <div class="flex items-center gap-2 mt-4 flex-wrap">
            <span class="inline-flex items-center gap-1.5 text-xs font-semibold px-3 py-1.5 bg-white/15 border border-white/20 rounded-full text-white">
                <i class="fas fa-building text-[9px]" aria-hidden="true"></i> {{ $scaleLabel }}
            </span>
            <span class="inline-flex items-center gap-1.5 text-xs font-semibold px-3 py-1.5 bg-white/15 border border-white/20 rounded-full text-white">
                <i class="fas fa-location-dot text-[9px]" aria-hidden="true"></i> {{ $locationLabel }}
            </span>
            <span class="inline-flex items-center gap-1.5 text-xs font-semibold px-3 py-1.5 bg-white/15 border border-white/20 rounded-full text-white">
                <i class="fas fa-file-signature text-[9px]" aria-hidden="true"></i> {{ $totalPermits }} Izin Direkomendasikan
            </span>
        </div>

        {{-- Stat strip --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mt-4">
            <div class="bg-white/10 backdrop-blur border border-white/15 rounded-lg px-4 py-3 text-center">
                <p class="text-2xl font-bold text-white tabular-nums">{{ $totalPermits }}</p>
                <p class="text-[11px] text-white/70 mt-0.5">Total Izin</p>
            </div>
            <div class="bg-white/10 backdrop-blur border border-white/15 rounded-lg px-4 py-3 text-center">
                <p class="text-2xl font-bold text-white tabular-nums">{{ count($grouped) }}</p>
                <p class="text-[11px] text-white/70 mt-0.5">Tahap Proses</p>
            </div>
            @if($tlMin && $tlMax)
            <div class="bg-white/10 backdrop-blur border border-white/15 rounded-lg px-4 py-3 text-center">
                <p class="text-xl font-bold text-white tabular-nums">{{ $tlMin }}–{{ $tlMax }}</p>
                <p class="text-[11px] text-white/70 mt-0.5">Hari Kerja</p>
            </div>
            @elseif($grandMin > 0)
            <div class="bg-white/10 backdrop-blur border border-white/15 rounded-lg px-4 py-3 text-center">
                <p class="text-xl font-bold text-white">{{ $fmtRp($grandMin) }}</p>
                <p class="text-[11px] text-white/70 mt-0.5">Est. Minimum</p>
            </div>
            @endif
            <div class="bg-white/10 backdrop-blur border border-white/15 rounded-lg px-4 py-3 text-center">
                <p class="text-2xl font-bold text-white tabular-nums">{{ number_format($complexityScore, 1) }}</p>
                <p class="text-[11px] text-white/70 mt-0.5">Skor Kompleksitas</p>
            </div>
        </div>
    </div>
</section>

{{-- ─── TAB BAR ─── --}}
<div class="sticky top-0 z-20 border-b border-[var(--border-subtle)] shadow-sm" style="background: var(--surface-base, #fff);">
    <div class="max-w-[1400px] mx-auto px-4 lg:px-8">
        <nav class="flex gap-0 overflow-x-auto" aria-label="Tab navigasi rekomendasi">
            <button @click="tab='alur'"
                    :class="tab==='alur' ? 'border-b-2 text-[var(--client-primary)]' : 'text-[var(--text-tertiary)] hover:text-[var(--text-secondary)] hover:bg-[var(--surface-cool)]'"
                    class="px-5 py-3.5 text-sm font-semibold whitespace-nowrap transition-all flex items-center gap-1.5"
                    :style="tab==='alur' ? 'border-color: var(--client-primary); background: color-mix(in oklab, var(--client-primary) 8%, transparent)' : ''">
                <i class="fas fa-sitemap text-xs" aria-hidden="true"></i> Alur Perizinan
            </button>
            <button @click="tab='rab'"
                    :class="tab==='rab' ? 'border-b-2 text-[var(--client-primary)]' : 'text-[var(--text-tertiary)] hover:text-[var(--text-secondary)] hover:bg-[var(--surface-cool)]'"
                    class="px-5 py-3.5 text-sm font-semibold whitespace-nowrap transition-all flex items-center gap-1.5"
                    :style="tab==='rab' ? 'border-color: var(--client-primary); background: color-mix(in oklab, var(--client-primary) 8%, transparent)' : ''">
                <i class="fas fa-table text-xs" aria-hidden="true"></i> RAB Biaya
            </button>
            <button @click="tab='docs'"
                    :class="tab==='docs' ? 'border-b-2 text-[var(--client-primary)]' : 'text-[var(--text-tertiary)] hover:text-[var(--text-secondary)] hover:bg-[var(--surface-cool)]'"
                    class="px-5 py-3.5 text-sm font-semibold whitespace-nowrap transition-all flex items-center gap-1.5"
                    :style="tab==='docs' ? 'border-color: var(--client-primary); background: color-mix(in oklab, var(--client-primary) 8%, transparent)' : ''">
                <i class="fas fa-file-alt text-xs" aria-hidden="true"></i> Dokumen & Risiko
                @if(count($requiredDocs) > 0)
                <span class="inline-flex items-center justify-center w-4 h-4 text-[9px] font-bold rounded-full text-white"
                      style="background: var(--client-primary)">{{ count($requiredDocs) }}</span>
                @endif
            </button>
        </nav>
    </div>
</div>

{{-- ─── CONTENT ─── --}}
<div class="max-w-[1400px] mx-auto px-4 lg:px-8 py-6 grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">

    {{-- ── MAIN (2/3) ── --}}
    <div class="lg:col-span-2 min-w-0">

        {{-- ╔════════════════╗ --}}
        {{-- ║ TAB: Alur       ║ --}}
        {{-- ╚════════════════╝ --}}
        <div x-show="tab==='alur'" x-transition.opacity>
            @php $globalOrder = 0; @endphp

            @foreach($grouped as $cat => $perms)
            @php $ph = $phases[$cat] ?? $phases['operational']; @endphp

            <section class="mb-7">
                {{-- Phase header --}}
                <div class="flex items-center gap-2.5 mb-3">
                    <span class="w-7 h-7 rounded-full flex items-center justify-center flex-shrink-0"
                          style="background: color-mix(in oklab, var(--client-primary) 12%, transparent)">
                        <i class="fas {{ $ph['icon'] }} text-[10px]" style="color: var(--client-primary)" aria-hidden="true"></i>
                    </span>
                    <div>
                        <h2 class="text-sm font-bold text-[var(--text-primary)]">{{ $ph['label'] }}</h2>
                    </div>
                    <span class="ml-auto text-[10px] font-mono text-[var(--text-tertiary)] bg-[var(--surface-cool)] px-2 py-0.5 rounded-full">
                        {{ $perms->count() }} izin
                    </span>
                </div>

                <div class="space-y-2.5">
                    @foreach($perms as $pIdx => $permit)
                    @php
                        $globalOrder++;
                        $gf       = $permit['government_fee'] ?? null;
                        $cf       = $permit['consultant_fee'] ?? null;
                        $govMin   = is_array($gf) ? ($gf['min'] ?? 0) : 0;
                        $govMax   = is_array($gf) ? ($gf['max'] ?? 0) : 0;
                        $conMin   = is_array($cf) ? ($cf['min'] ?? 0) : 0;
                        $conMax   = is_array($cf) ? ($cf['max'] ?? 0) : 0;
                        $pTotMin  = $govMin + $conMin;
                        $pTotMax  = $govMax + $conMax;
                        $prereqs  = is_array($permit['prerequisites'] ?? null) ? $permit['prerequisites'] : [];
                        $triggers = is_array($permit['triggers_next'] ?? null)  ? $permit['triggers_next'] : [];
                        $docs     = is_array($permit['documents_required'] ?? null) ? $permit['documents_required'] : [];
                        $priority = $permit['priority'] ?? 'medium';
                        $isCrit   = $priority === 'critical';
                        $itemKey  = $cat . '_' . $pIdx;
                    @endphp

                    <article class="bg-[var(--surface-elevated)] border border-[var(--border-subtle)] rounded-xl overflow-hidden">
                        {{-- Clickable row --}}
                        <button @click="expanded['{{ $itemKey }}'] = !expanded['{{ $itemKey }}']"
                                :aria-expanded="!!expanded['{{ $itemKey }}']"
                                class="w-full text-left px-4 py-3.5 flex items-start gap-3 hover:bg-[var(--surface-cool)] transition-colors">

                            {{-- Order badge --}}
                            <span class="w-6 h-6 rounded-full text-[11px] font-bold flex items-center justify-center flex-shrink-0 mt-0.5 text-white"
                                  style="background: var(--client-primary); opacity: {{ $isCrit ? '1' : '0.65' }}">
                                {{ $permit['order'] ?? $globalOrder }}
                            </span>

                            <div class="flex-1 min-w-0">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="min-w-0">
                                        <h3 class="text-sm font-bold text-[var(--text-primary)] leading-tight">{{ $permit['name'] ?? 'Izin' }}</h3>
                                        <p class="text-[11px] text-[var(--text-tertiary)] mt-0.5">
                                            @if($permit['issuing_authority'] ?? false)
                                            <span>{{ $permit['issuing_authority'] }}</span>
                                            @endif
                                            @if($permit['estimated_timeline'] ?? false)
                                            <span class="mx-1">·</span>
                                            <i class="fas fa-clock text-[9px]" aria-hidden="true"></i>
                                            {{ $permit['estimated_timeline'] }}
                                            @endif
                                        </p>
                                    </div>
                                    <div class="text-right flex-shrink-0">
                                        @if($pTotMin > 0 || $pTotMax > 0)
                                        <p class="text-xs font-bold text-[var(--text-primary)] font-mono whitespace-nowrap">{{ $fmtRange($pTotMin, $pTotMax) }}</p>
                                        <p class="text-[10px] text-[var(--text-tertiary)]">total izin ini</p>
                                        @else
                                        <p class="text-xs font-semibold text-emerald-600 dark:text-emerald-400">Gratis</p>
                                        @endif
                                    </div>
                                </div>

                                {{-- Prerequisite chips --}}
                                @if(count($prereqs) > 0)
                                <div class="flex flex-wrap gap-1 mt-2">
                                    @foreach($prereqs as $pre)
                                    <span class="inline-flex items-center gap-1 text-[10px] px-2 py-0.5 bg-orange-50 dark:bg-orange-900/20 text-orange-700 dark:text-orange-400 border border-orange-200 dark:border-orange-700/40 rounded-full">
                                        <i class="fas fa-chevron-right text-[7px]" aria-hidden="true"></i>Setelah: {{ Str::limit($pre, 35) }}
                                    </span>
                                    @endforeach
                                </div>
                                @endif
                            </div>

                            <i class="fas fa-chevron-down text-[10px] text-[var(--text-tertiary)] flex-shrink-0 mt-1.5 transition-transform duration-200"
                               :class="expanded['{{ $itemKey }}'] ? 'rotate-180' : ''" aria-hidden="true"></i>
                        </button>

                        {{-- Expanded detail --}}
                        <div x-show="!!expanded['{{ $itemKey }}']"
                             x-transition:enter="transition ease-out duration-150"
                             x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                             class="border-t border-[var(--border-subtle)] bg-[var(--surface-cool)] px-4 py-4 space-y-3.5">

                            @if($permit['description'] ?? false)
                            <p class="text-xs text-[var(--text-secondary)] leading-relaxed">{{ $permit['description'] }}</p>
                            @endif

                            {{-- Fee breakdown grid --}}
                            <div class="grid grid-cols-2 gap-2.5 text-xs">
                                @if($permit['legal_basis'] ?? false)
                                <div class="col-span-2 bg-[var(--surface-elevated)] rounded-lg px-3 py-2">
                                    <p class="text-[10px] font-semibold text-[var(--text-tertiary)] uppercase tracking-wide mb-0.5">Dasar Hukum</p>
                                    <p class="text-[var(--text-secondary)]">{{ $permit['legal_basis'] }}</p>
                                </div>
                                @endif
                                <div class="bg-[var(--surface-elevated)] rounded-lg px-3 py-2">
                                    <p class="text-[10px] font-semibold text-[var(--text-tertiary)] uppercase tracking-wide mb-0.5">Biaya PNBP</p>
                                    <p class="font-semibold text-[var(--text-primary)] font-mono">{{ $fmtRange($govMin, $govMax) }}</p>
                                    @if(is_array($gf) && ($gf['note'] ?? false))
                                    <p class="text-[10px] text-[var(--text-tertiary)] mt-0.5">{{ $gf['note'] }}</p>
                                    @endif
                                </div>
                                <div class="bg-[var(--surface-elevated)] rounded-lg px-3 py-2">
                                    <p class="text-[10px] font-semibold text-[var(--text-tertiary)] uppercase tracking-wide mb-0.5">Jasa Konsultan</p>
                                    <p class="font-semibold text-[var(--text-primary)] font-mono">{{ $fmtRange($conMin, $conMax) }}</p>
                                    @if(is_array($cf) && ($cf['note'] ?? false))
                                    <p class="text-[10px] text-[var(--text-tertiary)] mt-0.5">{{ $cf['note'] }}</p>
                                    @endif
                                </div>
                                <div class="col-span-2 rounded-lg px-3 py-2 flex items-center justify-between"
                                     style="background: color-mix(in oklab, var(--client-primary) 8%, transparent); border: 1px solid color-mix(in oklab, var(--client-primary) 20%, transparent)">
                                    <span class="font-semibold text-[var(--text-secondary)]">Total Izin Ini</span>
                                    <span class="font-bold font-mono" style="color: var(--client-primary)">{{ $fmtRange($pTotMin, $pTotMax) }}</span>
                                </div>
                            </div>

                            {{-- Documents required for this permit --}}
                            @if(count($docs) > 0)
                            <div>
                                <p class="text-[10px] font-semibold text-[var(--text-tertiary)] uppercase tracking-wide mb-1.5">Dokumen Diperlukan</p>
                                <ul class="space-y-1">
                                    @foreach($docs as $d)
                                    <li class="flex items-start gap-2 text-[11px] text-[var(--text-secondary)]">
                                        <i class="fas fa-file-circle-check text-[9px] mt-0.5 flex-shrink-0" style="color: var(--client-primary)" aria-hidden="true"></i>
                                        {{ $d }}
                                    </li>
                                    @endforeach
                                </ul>
                            </div>
                            @endif

                            {{-- Triggers next --}}
                            @if(count($triggers) > 0)
                            <div>
                                <p class="text-[10px] font-semibold text-[var(--text-tertiary)] uppercase tracking-wide mb-1.5">
                                    Setelah ini selesai → dapat mengurus:
                                </p>
                                <div class="flex flex-wrap gap-1.5">
                                    @foreach($triggers as $trig)
                                    <span class="inline-flex items-center gap-1 text-[10px] px-2 py-0.5 bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-700/40 rounded-full">
                                        <i class="fas fa-arrow-right text-[7px]" aria-hidden="true"></i>{{ $trig }}
                                    </span>
                                    @endforeach
                                </div>
                            </div>
                            @endif
                        </div>
                    </article>
                    @endforeach
                </div>
            </section>
            @endforeach

            @if($limitations)
            <div class="mt-2 p-4 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-700/40 rounded-xl flex gap-3 text-xs text-amber-800 dark:text-amber-300">
                <i class="fas fa-info-circle mt-0.5 flex-shrink-0" aria-hidden="true"></i>
                <p>{{ $limitations }}</p>
            </div>
            @endif
        </div>{{-- /tab alur --}}


        {{-- ╔════════════════╗ --}}
        {{-- ║ TAB: RAB Biaya  ║ --}}
        {{-- ╚════════════════╝ --}}
        <div x-show="tab==='rab'" x-transition.opacity>
            <div class="bg-[var(--surface-elevated)] border border-[var(--border-subtle)] rounded-xl overflow-hidden">
                <div class="px-5 py-4 border-b border-[var(--border-subtle)]">
                    <h2 class="text-sm font-bold text-[var(--text-primary)] flex items-center gap-2">
                        <i class="fas fa-table text-xs" style="color: var(--client-primary)" aria-hidden="true"></i>
                        Rencana Anggaran Biaya (RAB) Perizinan
                    </h2>
                    <p class="text-xs text-[var(--text-secondary)] mt-0.5">
                        KBLI {{ $kbli->code }} — {{ $kbli->name }}
                    </p>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-xs" aria-label="RAB Perizinan">
                        <thead>
                            <tr class="bg-[var(--surface-cool)] text-[var(--text-tertiary)] text-[11px] border-b border-[var(--border-subtle)]">
                                <th class="text-left px-4 py-2.5 font-semibold w-8">#</th>
                                <th class="text-left px-4 py-2.5 font-semibold min-w-[200px]">Nama Izin</th>
                                <th class="text-left px-4 py-2.5 font-semibold min-w-[130px]">Instansi</th>
                                <th class="text-right px-4 py-2.5 font-semibold whitespace-nowrap">Biaya PNBP</th>
                                <th class="text-right px-4 py-2.5 font-semibold whitespace-nowrap">Jasa Konsultan</th>
                                <th class="text-right px-4 py-2.5 font-semibold whitespace-nowrap">Total Estimasi</th>
                                <th class="text-left px-4 py-2.5 font-semibold whitespace-nowrap">Waktu</th>
                            </tr>
                        </thead>
                        <tbody>
                        @php
                            $rabRow  = 0;
                            $secAlph = range('A', 'Z');
                            $secI    = 0;
                        @endphp

                        @foreach($grouped as $cat => $perms)
                        @php
                            $ph         = $phases[$cat] ?? $phases['operational'];
                            $secL       = $secAlph[$secI] ?? chr(65 + $secI);
                            $secI++;
                            $sGovMin = $sGovMax = $sConMin = $sConMax = 0;
                        @endphp

                        {{-- Phase section row --}}
                        <tr class="bg-[var(--surface-cool)]" style="border-top: 2px solid var(--border-subtle)">
                            <td colspan="7" class="px-4 py-2 font-bold text-[11px] uppercase tracking-wide" style="color: var(--client-primary)">
                                <i class="fas {{ $ph['icon'] }} mr-1.5 opacity-70" aria-hidden="true"></i>
                                {{ $secL }}. {{ $ph['label'] }}
                            </td>
                        </tr>

                        @foreach($perms as $permit)
                        @php
                            $rabRow++;
                            $gf      = $permit['government_fee'] ?? null;
                            $cf      = $permit['consultant_fee'] ?? null;
                            $govMin  = is_array($gf) ? ($gf['min'] ?? 0) : 0;
                            $govMax  = is_array($gf) ? ($gf['max'] ?? 0) : 0;
                            $conMin  = is_array($cf) ? ($cf['min'] ?? 0) : 0;
                            $conMax  = is_array($cf) ? ($cf['max'] ?? 0) : 0;
                            $pMin    = $govMin + $conMin;
                            $pMax    = $govMax + $conMax;
                            $sGovMin += $govMin; $sGovMax += $govMax;
                            $sConMin += $conMin; $sConMax += $conMax;
                            $prereqs = is_array($permit['prerequisites'] ?? null) ? $permit['prerequisites'] : [];
                        @endphp
                        <tr class="border-b border-[var(--border-subtle)] hover:bg-[var(--surface-cool)] transition-colors">
                            <td class="px-4 py-3 text-[var(--text-tertiary)] font-mono text-[11px] align-top">{{ $rabRow }}</td>
                            <td class="px-4 py-3 align-top">
                                <p class="font-semibold text-[var(--text-primary)] leading-tight">{{ $permit['name'] ?? '–' }}</p>
                                @if($permit['legal_basis'] ?? false)
                                <p class="text-[10px] text-[var(--text-tertiary)] mt-0.5">{{ $permit['legal_basis'] }}</p>
                                @endif
                                @if(count($prereqs) > 0)
                                <div class="flex flex-wrap gap-1 mt-1">
                                    @foreach(array_slice($prereqs, 0, 2) as $pre)
                                    <span class="text-[9px] px-1.5 py-0.5 bg-orange-50 dark:bg-orange-900/20 text-orange-600 dark:text-orange-400 border border-orange-200 dark:border-orange-700/40 rounded">
                                        ↑ {{ Str::limit($pre, 30) }}
                                    </span>
                                    @endforeach
                                    @if(count($prereqs) > 2)
                                    <span class="text-[9px] px-1.5 py-0.5 bg-[var(--surface-cool)] text-[var(--text-tertiary)] rounded">+{{ count($prereqs) - 2 }} lagi</span>
                                    @endif
                                </div>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-[var(--text-secondary)] text-[11px] align-top">
                                {{ Str::limit($permit['issuing_authority'] ?? '–', 25) }}
                            </td>
                            <td class="px-4 py-3 text-right font-mono text-[11px] whitespace-nowrap align-top">
                                @if($govMin <= 0 && $govMax <= 0)
                                <span class="text-emerald-600 dark:text-emerald-400 font-semibold">Gratis</span>
                                @else
                                <span class="text-[var(--text-secondary)]">{{ $fmtRange($govMin, $govMax) }}</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right font-mono text-[11px] text-[var(--text-secondary)] whitespace-nowrap align-top">
                                {{ $fmtRange($conMin, $conMax) }}
                            </td>
                            <td class="px-4 py-3 text-right font-mono text-xs font-bold text-[var(--text-primary)] whitespace-nowrap align-top">
                                {{ $fmtRange($pMin, $pMax) }}
                            </td>
                            <td class="px-4 py-3 text-[var(--text-secondary)] text-[11px] whitespace-nowrap align-top">
                                {{ $permit['estimated_timeline'] ?? '–' }}
                            </td>
                        </tr>
                        @endforeach

                        {{-- Subtotal row --}}
                        @php $sTotMin = $sGovMin + $sConMin; $sTotMax = $sGovMax + $sConMax; @endphp
                        <tr class="bg-[var(--surface-cool)]" style="border-bottom: 2px solid var(--border-subtle)">
                            <td colspan="3" class="px-4 py-2.5 text-right text-[11px] font-bold text-[var(--text-secondary)]">
                                Subtotal {{ $secL }}
                            </td>
                            <td class="px-4 py-2.5 text-right font-bold text-xs text-[var(--text-primary)] font-mono whitespace-nowrap">{{ $fmtRange($sGovMin, $sGovMax) }}</td>
                            <td class="px-4 py-2.5 text-right font-bold text-xs text-[var(--text-primary)] font-mono whitespace-nowrap">{{ $fmtRange($sConMin, $sConMax) }}</td>
                            <td class="px-4 py-2.5 text-right font-bold text-sm font-mono whitespace-nowrap" style="color: var(--client-primary)">{{ $fmtRange($sTotMin, $sTotMax) }}</td>
                            <td></td>
                        </tr>
                        @endforeach

                        {{-- Doc prep row --}}
                        @if($docPrepMin > 0 || $docPrepMax > 0)
                        <tr class="border-b border-[var(--border-subtle)]">
                            <td class="px-4 py-3 text-[var(--text-tertiary)] font-mono text-[11px] align-top">+</td>
                            <td colspan="4" class="px-4 py-3 align-top">
                                <p class="font-semibold text-[var(--text-primary)] text-xs">Biaya Persiapan Dokumen</p>
                                <p class="text-[10px] text-[var(--text-tertiary)]">Penyusunan, legalisasi, penerjemahan dokumen</p>
                            </td>
                            <td class="px-4 py-3 text-right font-bold text-xs text-[var(--text-primary)] font-mono whitespace-nowrap align-top">
                                {{ $fmtRange($docPrepMin, $docPrepMax) }}
                            </td>
                            <td></td>
                        </tr>
                        @endif

                        {{-- Grand total --}}
                        <tr style="background: var(--client-primary); color: #fff;">
                            <td colspan="3" class="px-4 py-4 font-bold text-sm">GRAND TOTAL</td>
                            <td class="px-4 py-4 text-right font-bold text-sm font-mono whitespace-nowrap">{{ $fmtRange($totalGovMin, $totalGovMax) }}</td>
                            <td class="px-4 py-4 text-right font-bold text-sm font-mono whitespace-nowrap">{{ $fmtRange($totalConMin, $totalConMax) }}</td>
                            <td class="px-4 py-4 text-right font-bold text-base font-mono whitespace-nowrap">{{ $fmtRange($grandMin, $grandMax) }}</td>
                            <td class="px-4 py-4 text-sm font-mono whitespace-nowrap opacity-90">
                                @if($tlMin && $tlMax){{ $tlMin }}–{{ $tlMax }} hk @endif
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>

                <div class="px-5 py-3 border-t border-[var(--border-subtle)] bg-[var(--surface-cool)]">
                    <p class="text-[10px] text-[var(--text-tertiary)]">
                        * Estimasi per Mei 2026. Biaya PNBP = tarif resmi instansi pemerintah (PP/Permen PNBP/retribusi daerah).
                        Jasa Konsultan = biaya pendampingan BizMark hingga izin terbit. hk = hari kerja.
                        Angka dapat berubah sesuai Perda setempat dan kondisi lapangan.
                    </p>
                </div>
            </div>
        </div>{{-- /tab rab --}}


        {{-- ╔════════════════════╗ --}}
        {{-- ║ TAB: Dok & Risiko  ║ --}}
        {{-- ╚════════════════════╝ --}}
        <div x-show="tab==='docs'" x-transition.opacity class="space-y-5">

            @if(count($requiredDocs) > 0)
            <section class="bg-[var(--surface-elevated)] border border-[var(--border-subtle)] rounded-xl overflow-hidden">
                <div class="px-5 py-4 border-b border-[var(--border-subtle)] flex items-center justify-between">
                    <h2 class="text-sm font-bold text-[var(--text-primary)] flex items-center gap-2">
                        <i class="fas fa-folder-open text-xs" style="color: var(--client-primary)" aria-hidden="true"></i>
                        Dokumen yang Perlu Disiapkan
                    </h2>
                    <span class="text-[10px] font-mono text-[var(--text-tertiary)] bg-[var(--surface-cool)] px-2 py-0.5 rounded-full">{{ count($requiredDocs) }} dokumen</span>
                </div>
                <ul class="divide-y divide-[var(--border-subtle)]">
                    @foreach($requiredDocs as $doc)
                    <li class="flex items-start gap-3 px-5 py-3 text-xs">
                        <span class="w-4.5 h-4.5 rounded border-2 border-[var(--border-subtle)] flex-shrink-0 mt-0.5"></span>
                        <span class="text-[var(--text-secondary)]">{{ $doc }}</span>
                    </li>
                    @endforeach
                </ul>
            </section>
            @endif

            @if(count($nextSteps) > 0)
            <section class="bg-[var(--surface-elevated)] border border-[var(--border-subtle)] rounded-xl overflow-hidden">
                <div class="px-5 py-4 border-b border-[var(--border-subtle)]">
                    <h2 class="text-sm font-bold text-[var(--text-primary)] flex items-center gap-2">
                        <i class="fas fa-list-check text-xs" style="color: var(--client-primary)" aria-hidden="true"></i>
                        Langkah Selanjutnya
                    </h2>
                </div>
                <ol class="divide-y divide-[var(--border-subtle)]">
                    @foreach($nextSteps as $si => $step)
                    <li class="flex items-start gap-3 px-5 py-3 text-xs">
                        <span class="w-5 h-5 rounded-full text-[10px] font-bold flex items-center justify-center flex-shrink-0 mt-0.5 text-white"
                              style="background: var(--client-primary)">{{ $si + 1 }}</span>
                        <span class="text-[var(--text-secondary)]">{{ $step }}</span>
                    </li>
                    @endforeach
                </ol>
            </section>
            @endif

            @if(count($riskFactors) > 0 || count($riskMitigation) > 0 || count($commonPitfalls) > 0)
            <section class="bg-[var(--surface-elevated)] border border-[var(--border-subtle)] rounded-xl overflow-hidden">
                <div class="px-5 py-4 border-b border-[var(--border-subtle)] flex items-center justify-between">
                    <h2 class="text-sm font-bold text-[var(--text-primary)] flex items-center gap-2">
                        <i class="fas fa-shield-alt text-xs
                            @if($riskColor==='green') text-green-500
                            @elseif($riskColor==='red') text-red-500
                            @else text-amber-500 @endif"
                           aria-hidden="true"></i>
                        Penilaian Risiko
                    </h2>
                    <span class="px-2.5 py-1 text-[10px] font-bold rounded-full border
                        @if($riskColor==='green') bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 border-green-200 dark:border-green-700/40
                        @elseif($riskColor==='red') bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400 border-red-200 dark:border-red-700/40
                        @else bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400 border-amber-200 dark:border-amber-700/40 @endif">
                        {{ $riskLabel }}
                    </span>
                </div>
                <div class="p-5 space-y-4">
                    @if(count($riskFactors) > 0)
                    <div>
                        <h3 class="text-xs font-bold text-[var(--text-primary)] mb-2">Faktor Risiko</h3>
                        <ul class="space-y-1.5">
                            @foreach($riskFactors as $f)
                            <li class="flex items-start gap-2 text-xs text-[var(--text-secondary)]">
                                <i class="fas fa-exclamation-triangle text-[9px] mt-0.5 flex-shrink-0
                                    @if($riskColor==='green') text-green-500 @elseif($riskColor==='red') text-red-500 @else text-amber-500 @endif"
                                   aria-hidden="true"></i>
                                {{ $f }}
                            </li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    @if(count($riskMitigation) > 0)
                    <div>
                        <h3 class="text-xs font-bold text-[var(--text-primary)] mb-2">Langkah Mitigasi</h3>
                        <ul class="space-y-1.5">
                            @foreach($riskMitigation as $m)
                            <li class="flex items-start gap-2 text-xs text-[var(--text-secondary)]">
                                <i class="fas fa-check-circle text-emerald-500 text-[9px] mt-0.5 flex-shrink-0" aria-hidden="true"></i>
                                {{ $m }}
                            </li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    @if(count($commonPitfalls) > 0)
                    <div>
                        <h3 class="text-xs font-bold text-[var(--text-primary)] mb-2">Kesalahan Umum yang Perlu Dihindari</h3>
                        <ul class="space-y-1.5">
                            @foreach($commonPitfalls as $cp)
                            <li class="flex items-start gap-2 text-xs text-[var(--text-secondary)]">
                                <i class="fas fa-times-circle text-red-500 text-[9px] mt-0.5 flex-shrink-0" aria-hidden="true"></i>
                                {{ $cp }}
                            </li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                </div>
            </section>
            @endif

            @if($limitations)
            <div class="p-4 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-700/40 rounded-xl flex gap-3 text-xs text-amber-800 dark:text-amber-300">
                <i class="fas fa-info-circle mt-0.5 flex-shrink-0" aria-hidden="true"></i>
                <p>{{ $limitations }}</p>
            </div>
            @endif
        </div>{{-- /tab docs --}}

    </div>{{-- /main --}}


    {{-- ── SIDEBAR ── --}}
    <aside class="space-y-4 lg:sticky lg:top-16 lg:self-start">

        {{-- RAB Summary --}}
        <section class="bg-[var(--surface-elevated)] border border-[var(--border-subtle)] rounded-xl overflow-hidden">
            <div class="px-5 py-4 border-b border-[var(--border-subtle)]">
                <h2 class="text-sm font-bold text-[var(--text-primary)] flex items-center gap-2">
                    <i class="fas fa-calculator text-xs" style="color: var(--client-primary)" aria-hidden="true"></i>
                    Ringkasan Biaya
                </h2>
            </div>
            <div class="px-5 py-4 space-y-2.5 text-xs">
                <div class="flex items-center justify-between gap-2">
                    <span class="text-[var(--text-secondary)] flex items-center gap-1.5">
                        <i class="fas fa-landmark text-blue-500 text-[9px]" aria-hidden="true"></i> Biaya PNBP
                    </span>
                    <span class="font-semibold text-[var(--text-primary)] font-mono">{{ $fmtRange($totalGovMin, $totalGovMax) }}</span>
                </div>
                <div class="flex items-center justify-between gap-2">
                    <span class="text-[var(--text-secondary)] flex items-center gap-1.5">
                        <i class="fas fa-user-tie text-green-500 text-[9px]" aria-hidden="true"></i> Jasa Konsultan
                    </span>
                    <span class="font-semibold text-[var(--text-primary)] font-mono">{{ $fmtRange($totalConMin, $totalConMax) }}</span>
                </div>
                @if($docPrepMin > 0 || $docPrepMax > 0)
                <div class="flex items-center justify-between gap-2">
                    <span class="text-[var(--text-secondary)] flex items-center gap-1.5">
                        <i class="fas fa-file-alt text-amber-500 text-[9px]" aria-hidden="true"></i> Persiapan Dokumen
                    </span>
                    <span class="font-semibold text-[var(--text-primary)] font-mono">{{ $fmtRange($docPrepMin, $docPrepMax) }}</span>
                </div>
                @endif
                <div class="border-t border-[var(--border-subtle)] pt-2.5 flex items-center justify-between gap-2">
                    <span class="font-bold text-[var(--text-primary)]">TOTAL ESTIMASI</span>
                    <span class="font-bold text-sm font-mono" style="color: var(--client-primary)">{{ $fmtRange($grandMin, $grandMax) }}</span>
                </div>
                <p class="text-[10px] text-[var(--text-tertiary)]">Estimasi Mei 2026 · Termasuk jasa BizMark</p>
            </div>
        </section>

        {{-- Timeline --}}
        @if($tlMin || $tlMax || count($critPath) > 0)
        <section class="bg-[var(--surface-elevated)] border border-[var(--border-subtle)] rounded-xl overflow-hidden">
            <div class="px-5 py-4 border-b border-[var(--border-subtle)]">
                <h2 class="text-sm font-bold text-[var(--text-primary)] flex items-center gap-2">
                    <i class="fas fa-calendar-alt text-xs" style="color: var(--client-primary)" aria-hidden="true"></i>
                    Estimasi Timeline
                </h2>
            </div>
            <div class="px-5 py-4">
                @if($tlMin && $tlMax)
                <div class="flex items-center gap-2 mb-3">
                    <div class="flex-1 text-center bg-[var(--surface-cool)] rounded-lg py-2.5">
                        <p class="text-xl font-bold text-[var(--text-primary)] font-mono tabular-nums">{{ $tlMin }}</p>
                        <p class="text-[10px] text-[var(--text-tertiary)]">hari min.</p>
                    </div>
                    <i class="fas fa-arrow-right text-[var(--text-tertiary)] text-xs flex-shrink-0" aria-hidden="true"></i>
                    <div class="flex-1 text-center bg-[var(--surface-cool)] rounded-lg py-2.5">
                        <p class="text-xl font-bold text-[var(--text-primary)] font-mono tabular-nums">{{ $tlMax }}</p>
                        <p class="text-[10px] text-[var(--text-tertiary)]">hari maks.</p>
                    </div>
                </div>
                @elseif($tlSummary && $tlSummary !== '–')
                <p class="text-sm font-bold text-[var(--text-primary)] mb-3">{{ $tlSummary }}</p>
                @endif

                @if(count($critPath) > 0)
                <div class="space-y-1.5">
                    <p class="text-[10px] font-semibold text-[var(--text-tertiary)] uppercase tracking-wide mb-1">Jalur Kritis</p>
                    @foreach($critPath as $cp)
                    <div class="flex items-start gap-2 text-xs">
                        <span class="w-1.5 h-1.5 rounded-full flex-shrink-0 mt-1.5" style="background: var(--client-primary)"></span>
                        <span class="text-[var(--text-secondary)]">{{ $cp }}</span>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
        </section>
        @endif

        {{-- Risk + complexity --}}
        <div class="flex items-center gap-3 px-4 py-3.5 bg-[var(--surface-elevated)] border border-[var(--border-subtle)] rounded-xl">
            <div class="w-9 h-9 rounded-full flex items-center justify-center flex-shrink-0
                @if($riskColor==='green') bg-green-100 dark:bg-green-900/30
                @elseif($riskColor==='red') bg-red-100 dark:bg-red-900/30
                @else bg-amber-100 dark:bg-amber-900/30 @endif">
                <i class="fas fa-shield-alt text-sm
                    @if($riskColor==='green') text-green-600 dark:text-green-400
                    @elseif($riskColor==='red') text-red-600 dark:text-red-400
                    @else text-amber-600 dark:text-amber-400 @endif"
                   aria-hidden="true"></i>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-xs font-bold text-[var(--text-primary)]">{{ $riskLabel }}</p>
                <p class="text-[10px] text-[var(--text-tertiary)] truncate">{{ str_replace('_', ' ', strtoupper($riskClass)) }}</p>
            </div>
            <div class="text-right flex-shrink-0">
                <p class="text-sm font-bold text-[var(--text-primary)]">{{ number_format($complexityScore, 1) }}<span class="text-[10px] font-normal text-[var(--text-tertiary)]">/10</span></p>
                <p class="text-[10px] text-[var(--text-tertiary)]">Kompleksitas</p>
            </div>
        </div>

        {{-- CTA --}}
        <section class="bg-[var(--surface-elevated)] border border-[var(--border-subtle)] rounded-xl p-5 text-center">
            <div class="w-11 h-11 rounded-full flex items-center justify-center mx-auto mb-3"
                 style="background: color-mix(in oklab, var(--client-primary) 12%, transparent)">
                <i class="fas fa-rocket" style="color: var(--client-primary)" aria-hidden="true"></i>
            </div>
            <h3 class="text-sm font-bold text-[var(--text-primary)] mb-1">Siap Memulai?</h3>
            <p class="text-xs text-[var(--text-secondary)] mb-4">Tim BizMark siap memandu proses perizinan Anda dari awal hingga izin terbit.</p>
            <a href="{{ route('client.applications.create', ['kbli' => $kbli->code]) }}"
               class="w-full inline-flex items-center justify-center gap-2 px-4 py-3 text-white text-sm font-semibold rounded-lg hover:brightness-110 transition-all mb-2"
               style="background: var(--client-primary)">
                <i class="fas fa-plus text-xs" aria-hidden="true"></i> Buat Permohonan
            </a>
            <a href="{{ $supportWhatsapp }}" target="_blank" rel="noopener noreferrer"
               class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-[var(--surface-cool)] border border-[var(--border-subtle)] text-[var(--text-primary)] text-sm font-semibold rounded-lg hover:brightness-105 transition-colors">
                <i class="fab fa-whatsapp text-green-500 text-xs" aria-hidden="true"></i> Tanya via WhatsApp
            </a>
        </section>

        <a href="{{ route('client.services.context', $kbli->code) }}"
           class="block text-center text-xs font-semibold text-[var(--text-tertiary)] hover:text-[var(--text-primary)] transition-colors py-1">
            <i class="fas fa-rotate text-[10px]" aria-hidden="true"></i> Ubah Skala / Lokasi Usaha
        </a>

    </aside>

</div>{{-- /grid --}}
</div>{{-- /x-data --}}
