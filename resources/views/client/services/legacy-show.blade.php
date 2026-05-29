@php
    // ── Contact config ──
    $contact = (array) data_get(config('landing_metrics'), 'contact', []);
    $supportWhatsapp = $contact['whatsapp_link'] ?? 'https://wa.me/6283879602855';

    // ── Scale & Location Labels ──
    $scaleOptions = [
        'mikro'    => ['label' => 'Usaha Mikro',    'summary' => 'Aset ≤ Rp 50 juta atau omzet ≤ Rp 300 juta/tahun'],
        'kecil'    => ['label' => 'Usaha Kecil',    'summary' => 'Aset Rp 50–500 juta atau omzet Rp 300 jt – 2,5 M/tahun'],
        'menengah' => ['label' => 'Usaha Menengah', 'summary' => 'Aset Rp 500 jt – 10 M atau omzet Rp 2,5 – 50 M/tahun'],
        'besar'    => ['label' => 'Usaha Besar',    'summary' => 'Aset > Rp 10 miliar atau omzet > Rp 50 miliar/tahun'],
    ];
    $locationOptions = [
        'perkotaan'        => ['label' => 'Area Perkotaan',    'summary' => 'Regulasi zonasi dan kepadatan tinggi'],
        'pedesaan'         => ['label' => 'Area Pedesaan',     'summary' => 'Proses fleksibel dengan keterlibatan pemda'],
        'kawasan_industri' => ['label' => 'Kawasan Industri',  'summary' => 'Koordinasi pengelola kawasan & kementerian teknis'],
    ];
    $scaleInfo    = $scaleOptions[$businessScale] ?? ['label' => 'Umum', 'summary' => 'Rekomendasi umum tanpa konteks skala'];
    $locationInfo = $locationOptions[$locationType] ?? ['label' => 'Tidak ditentukan', 'summary' => 'Lokasi default'];

    // ── Recommendation metrics (safe access) ──
    $allPermits       = $recommendation->recommended_permits ?? [];
    $totalPermits     = count($allPermits);
    $mandatoryTotal   = collect($allPermits)->where('type', 'mandatory')->count();
    $recommendedTotal = collect($allPermits)->where('type', 'recommended')->count();
    $conditionalTotal = $totalPermits - $mandatoryTotal - $recommendedTotal;
    $confidencePercent = max(5, min(100, round(($recommendation->confidence_score ?? 0) * 100)));

    // ── Cost range (handle both government_fee & estimated_cost_range keys) ──
    $costRangeMin = 0;
    $costRangeMax = 0;
    foreach ($allPermits as $p) {
        $fee = $p['estimated_cost_range'] ?? $p['government_fee'] ?? null;
        $costRangeMin += (int) ($fee['min'] ?? 0);
        $costRangeMax += (int) ($fee['max'] ?? 0);
    }

    // ── Timeline (handle both object keys and summary string) ──
    $timeline        = $recommendation->estimated_timeline ?? [];
    $timelineMin     = $timeline['minimum_days'] ?? null;
    $timelineMax     = $timeline['maximum_days'] ?? null;
    $timelineSummary = $timeline['summary'] ?? null;
    $criticalPath    = $timeline['critical_path'] ?? [];
    if (!$timelineMin && $timelineSummary && preg_match('/(\d+)\s*[-–]\s*(\d+)/', $timelineSummary, $tm)) {
        $timelineMin = (int) $tm[1];
        $timelineMax = (int) $tm[2];
    }

    // ── Complexity level (full Tailwind classes — NO interpolation) ──
    if ($mandatoryTotal <= 2) {
        $complexityLevel = 'Rendah';
        $complexityCopy  = 'Kebutuhan izin relatif sederhana dan dapat ditangani cepat.';
    } elseif ($mandatoryTotal <= 5) {
        $complexityLevel = 'Menengah';
        $complexityCopy  = 'Beberapa izin prioritas perlu dipantau secara bertahap.';
    } else {
        $complexityLevel = 'Tinggi';
        $complexityCopy  = 'Perlu orkestrasi ketat dan koordinasi lintas instansi.';
    }
    $complexityBarClass   = match($complexityLevel) { 'Rendah' => 'bg-green-500', 'Menengah' => 'bg-yellow-500', default => 'bg-red-500' };
    $complexityBadgeClass = match($complexityLevel) {
        'Rendah'   => 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300',
        'Menengah' => 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-300',
        default    => 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300',
    };
    $complexityWidth = match($complexityLevel) { 'Rendah' => '33%', 'Menengah' => '66%', default => '100%' };

    // ── Risk Assessment ──
    $riskAssessment = $recommendation->risk_assessment ?? [];
    $riskLevel      = $riskAssessment['level'] ?? null;
    $riskFactors    = $riskAssessment['factors'] ?? [];
    $riskMitigation = $riskAssessment['mitigation'] ?? [];
    $riskPitfalls   = $riskAssessment['common_pitfalls'] ?? [];
    $riskNotes      = $riskAssessment['notes'] ?? null;

    // ── AI Metadata ──
    $aiModel        = $recommendation->ai_model ?? null;
    $cacheHits      = $recommendation->cache_hits ?? 0;
    $aiVersion      = null;

    // ── Additional Notes (JSON-encoded from OpenRouterService) ──
    $additionalNotesRaw = $recommendation->additional_notes ?? null;
    $additionalNotes = null;
    $nextSteps       = [];
    $limitations     = null;
    $extraRiskFactors = [];
    if ($additionalNotesRaw) {
        $decoded = is_string($additionalNotesRaw) ? json_decode($additionalNotesRaw, true) : (is_array($additionalNotesRaw) ? $additionalNotesRaw : null);
        if (is_array($decoded)) {
            $nextSteps        = $decoded['next_steps'] ?? [];
            $limitations      = $decoded['limitations'] ?? null;
            $extraRiskFactors = $decoded['risk_factors'] ?? [];
            $aiVersion        = $decoded['version'] ?? null;
            // If there are other text notes, keep them
            unset($decoded['next_steps'], $decoded['limitations'], $decoded['risk_factors'], $decoded['version']);
            $remaining = array_filter($decoded);
            $additionalNotes = !empty($remaining) ? implode("\n", array_map(fn($v) => is_string($v) ? $v : json_encode($v), $remaining)) : null;
        } else {
            $additionalNotes = $additionalNotesRaw;
        }
    }

    // Merge extra risk factors if main ones empty
    if (empty($riskFactors) && !empty($extraRiskFactors)) {
        $riskFactors = $extraRiskFactors;
    }

    // ── Risk Classification Label ──
    $riskClassification = null;
    $riskClassBadge = '';
    if ($riskLevel) {
        $riskClassification = match($riskLevel) {
            'low' => 'Risiko Rendah',
            'medium' => 'Risiko Menengah',
            'high' => 'Risiko Tinggi',
            default => ucfirst($riskLevel),
        };
        $riskClassBadge = match($riskLevel) {
            'low' => 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300',
            'medium' => 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-300',
            'high' => 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300',
            default => 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400',
        };
    }

    // ── Consultant Cost Totals (for metrics when formattedCosts unavailable) ──
    $consultRangeMin = 0;
    $consultRangeMax = 0;
    foreach ($allPermits as $p) {
        $cfee = $p['consultant_fee_range'] ?? $p['consultant_fee'] ?? null;
        $consultRangeMin += (int) ($cfee['min'] ?? 0);
        $consultRangeMax += (int) ($cfee['max'] ?? 0);
    }
    $grandTotalMin = $costRangeMin + $consultRangeMin;
    $grandTotalMax = $costRangeMax + $consultRangeMax;

    // ── Permit category info ──
    $categoryInfo = [
        'foundational'  => ['title' => 'Izin Dasar & Legalitas',    'icon' => 'fa-building',     'desc' => 'Fondasi hukum pendirian dan operasional usaha'],
        'environmental' => ['title' => 'Izin Lingkungan',           'icon' => 'fa-leaf',         'desc' => 'Dampak lingkungan dan pengelolaan lingkungan hidup'],
        'technical'     => ['title' => 'Izin Teknis',               'icon' => 'fa-tools',        'desc' => 'Bangunan, lahan, dan infrastruktur teknis'],
        'operational'   => ['title' => 'Izin Operasional',          'icon' => 'fa-cogs',         'desc' => 'Kegiatan operasional dan produksi usaha'],
        'sectoral'      => ['title' => 'Izin Khusus Sektoral',      'icon' => 'fa-certificate',  'desc' => 'Izin spesifik untuk sektor usaha ini'],
        'other'         => ['title' => 'Izin Lainnya',              'icon' => 'fa-file-alt',     'desc' => 'Izin tambahan yang mungkin diperlukan'],
    ];
@endphp

{{-- ============================================================ --}}
{{-- MOBILE HERO                                                  --}}
{{-- ============================================================ --}}
<div class="lg:hidden bg-[#0a66c2] text-white px-4 py-5 border-y border-[#0a66c2]" role="banner">
    {{-- Back --}}
    <a href="{{ route('client.services.index') }}" class="inline-flex items-center text-white/80 hover:text-white text-sm mb-3 min-h-[44px] active:scale-95 transition-all" aria-label="Kembali ke katalog">
        <i class="fas fa-arrow-left mr-2" aria-hidden="true"></i> Kembali
    </a>

    {{-- KBLI Badge + Title --}}
    <div class="flex items-start gap-3 mb-4">
        <span class="flex-shrink-0 px-2.5 py-1 bg-white/15 backdrop-blur border border-white/30 text-xs font-mono font-bold rounded">{{ $kbli->code }}</span>
        <div class="flex-1 min-w-0">
            <h1 class="text-lg font-bold leading-tight">{{ $kbli->description }}</h1>
            <p class="text-sm text-white/80 mt-0.5">Sektor {{ $kbli->sector }}</p>
        </div>
    </div>

    @if($recommendation)
    {{-- Confidence bar --}}
    <div class="mb-4">
        <div class="flex items-center justify-between text-xs text-white/70 mb-1">
            <span>Akurasi Rekomendasi</span>
            <span>{{ $confidencePercent }}%</span>
        </div>
        <div class="h-1.5 bg-white/20 rounded-full overflow-hidden">
            <div class="h-full bg-white rounded-full transition-all" style="width: {{ $confidencePercent }}%"></div>
        </div>
    </div>

    {{-- Stats 3-col --}}
    <div class="grid grid-cols-3 gap-2 mb-4" role="list" aria-label="Ringkasan rekomendasi">
        <div class="bg-white/10 backdrop-blur border border-white/20 px-3 py-2.5 text-center" role="listitem">
            <p class="text-lg font-bold leading-tight">{{ $mandatoryTotal }}</p>
            <p class="text-[10px] text-white/70 leading-tight">Izin Wajib</p>
        </div>
        <div class="bg-white/10 backdrop-blur border border-white/20 px-3 py-2.5 text-center" role="listitem">
            <p class="text-lg font-bold leading-tight">{{ $totalPermits }}</p>
            <p class="text-[10px] text-white/70 leading-tight">Total Izin</p>
        </div>
        <div class="bg-white/10 backdrop-blur border border-white/20 px-3 py-2.5 text-center" role="listitem">
            <p class="text-lg font-bold leading-tight">{{ $timelineMin ?? '?' }}–{{ $timelineMax ?? '?' }}</p>
            <p class="text-[10px] text-white/70 leading-tight">Hari Kerja</p>
        </div>
    </div>
    @endif

    {{-- CTA --}}
    <div class="grid grid-cols-2 gap-2">
        <a href="{{ route('client.services.context', $kbli->code) }}"
           class="flex items-center justify-center gap-2 px-4 py-3 bg-white/10 backdrop-blur border border-white/30 text-white font-semibold rounded-lg min-h-[44px] active:scale-95 transition-all text-sm">
            <i class="fas fa-edit" aria-hidden="true"></i> Ubah Konteks
        </a>
        <a href="{{ route('client.applications.create', ['kbli_code' => $kbli->code]) }}"
           class="flex items-center justify-center gap-2 px-4 py-3 bg-white text-[#0a66c2] font-semibold rounded-lg min-h-[44px] active:scale-95 transition-all text-sm">
            <i class="fas fa-paper-plane" aria-hidden="true"></i> Ajukan
        </a>
    </div>
</div>

{{-- ============================================================ --}}
{{-- DESKTOP HERO                                                 --}}
{{-- ============================================================ --}}
<div class="hidden lg:block bg-[#0a66c2] border-y border-[#0a66c2] text-white" role="banner">
    <div class="max-w-6xl mx-auto px-6 lg:px-8 py-8">
        {{-- Back --}}
        <a href="{{ route('client.services.index') }}" class="inline-flex items-center text-white/80 hover:text-white text-sm mb-5 transition-colors" aria-label="Kembali ke katalog">
            <i class="fas fa-arrow-left mr-2" aria-hidden="true"></i> Kembali ke Katalog
        </a>

        <div class="flex items-start justify-between gap-8">
            {{-- Left: KBLI Info --}}
            <div class="flex-1">
                <div class="flex items-center gap-3 mb-3">
                    <span class="inline-flex items-center px-3 py-1 bg-white/15 backdrop-blur border border-white/30 text-xs font-mono font-bold rounded uppercase tracking-widest">
                        KBLI {{ $kbli->code }}
                    </span>
                    <span class="text-sm text-white/70">Sektor {{ $kbli->sector }}</span>
                </div>

                <h1 class="text-2xl lg:text-3xl font-bold leading-tight mb-2">{{ $kbli->description }}</h1>

                @if($kbli->notes)
                <p class="text-sm text-white/80 leading-relaxed mb-4 max-w-2xl">{{ $kbli->notes }}</p>
                @endif

                @if($recommendation)
                {{-- Confidence bar --}}
                <div class="max-w-sm mb-5">
                    <div class="flex items-center justify-between text-xs text-white/70 mb-1">
                        <span>Akurasi Rekomendasi</span>
                        <span>{{ $confidencePercent }}% yakin</span>
                    </div>
                    <div class="h-2 bg-white/20 rounded-full overflow-hidden">
                        <div class="h-full bg-white rounded-full transition-all" style="width: {{ $confidencePercent }}%"></div>
                    </div>
                </div>

                {{-- CTA --}}
                <div class="flex items-center gap-3">
                    <a href="{{ route('client.applications.create', ['kbli_code' => $kbli->code]) }}"
                       class="inline-flex items-center gap-2 px-5 py-3 bg-white text-[#0a66c2] font-semibold hover:shadow-lg active:scale-95 transition-all">
                        <i class="fas fa-paper-plane" aria-hidden="true"></i> Ajukan Permohonan
                    </a>
                    <a href="{{ route('client.services.downloadSummary', $kbli->code) }}"
                       class="inline-flex items-center gap-2 px-5 py-3 bg-white/10 backdrop-blur border border-white/30 font-semibold hover:bg-white/20 active:scale-95 transition-all">
                        <i class="fas fa-file-download" aria-hidden="true"></i> Download PDF
                    </a>
                </div>
                @endif
            </div>

            {{-- Right: Business Context --}}
            <div class="w-72 flex-shrink-0 space-y-3">
                <div class="bg-white/10 backdrop-blur border border-white/20 p-4">
                    <p class="text-xs uppercase tracking-widest text-white/60 mb-3">Konteks Bisnis</p>
                    <div class="space-y-3">
                        <div class="bg-white/5 rounded-lg p-3">
                            <p class="text-[11px] uppercase tracking-wide text-white/50">Skala Usaha</p>
                            <p class="text-sm font-semibold mt-0.5">{{ $scaleInfo['label'] }}</p>
                            <p class="text-xs text-white/60 leading-relaxed mt-0.5">{{ $scaleInfo['summary'] }}</p>
                        </div>
                        <div class="bg-white/5 rounded-lg p-3">
                            <p class="text-[11px] uppercase tracking-wide text-white/50">Lokasi Operasional</p>
                            <p class="text-sm font-semibold mt-0.5">{{ $locationInfo['label'] }}</p>
                            <p class="text-xs text-white/60 leading-relaxed mt-0.5">{{ $locationInfo['summary'] }}</p>
                        </div>
                    </div>
                    <a href="{{ route('client.services.context', $kbli->code) }}"
                       class="mt-3 inline-flex items-center gap-2 text-xs font-semibold text-white/80 hover:text-white transition-colors">
                        <i class="fas fa-edit text-xs" aria-hidden="true"></i> Perbarui Konteks Bisnis
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ============================================================ --}}
{{-- CONTENT                                                      --}}
{{-- ============================================================ --}}
<div class="space-y-1 lg:mt-1">

    {{-- ── Error Alert ── --}}
    @if(session('error'))
    <section class="bg-red-50 dark:bg-red-900/20 border-y border-red-200 dark:border-red-800" aria-label="Pesan error">
        <div class="px-4 lg:px-5 py-4 flex items-start gap-3">
            <i class="fas fa-exclamation-circle text-red-600 dark:text-red-400 mt-0.5 flex-shrink-0" aria-hidden="true"></i>
            <div>
                <p class="text-sm font-semibold text-red-800 dark:text-red-300">Terjadi Kesalahan</p>
                <p class="text-sm text-red-700 dark:text-red-300 mt-0.5">{{ session('error') }}</p>
            </div>
        </div>
    </section>
    @endif

    {{-- ── Success Alert ── --}}
    @if(session('success'))
    <section class="bg-green-50 dark:bg-green-900/20 border-y border-green-200 dark:border-green-800"
             x-data="{ show: true }" x-show="show" x-transition aria-label="Pesan sukses">
        <div class="px-4 lg:px-5 py-4 flex items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <i class="fas fa-check-circle text-green-600 dark:text-green-400 flex-shrink-0" aria-hidden="true"></i>
                <p class="text-sm text-green-800 dark:text-green-300">{{ session('success') }}</p>
            </div>
            <button @click="show = false" class="text-green-400 hover:text-green-600 dark:hover:text-green-300 min-h-[44px] min-w-[44px] flex items-center justify-center" aria-label="Tutup notifikasi">
                <i class="fas fa-times" aria-hidden="true"></i>
            </button>
        </div>
    </section>
    @endif

    @if(!$recommendation)
    {{-- ============================================================ --}}
    {{-- LOADING STATE (defensive — controller normally redirects)    --}}
    {{-- ============================================================ --}}
    <section class="bg-white dark:bg-gray-800 border-y border-gray-200 dark:border-gray-700" aria-label="Memuat rekomendasi">
        <div class="px-4 lg:px-5 py-12 text-center">
            <div class="relative inline-block mb-6">
                <div class="w-16 h-16 border-4 border-[#0a66c2]/20 border-t-[#0a66c2] rounded-full animate-spin"></div>
            </div>
            <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Menganalisis Kebutuhan Perizinan</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">Sistem sedang menganalisis regulasi terkini…</p>
            <div class="max-w-xs mx-auto">
                <div class="h-1.5 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
                    <div class="h-full bg-[#0a66c2] animate-[progress_2s_ease-in-out_infinite]"></div>
                </div>
            </div>
        </div>
    </section>
    <style>
        @keyframes progress { 0%{transform:translateX(-100%) scaleX(.3)} 50%{transform:translateX(50%) scaleX(.6)} 100%{transform:translateX(200%) scaleX(.3)} }
    </style>

    @else
    {{-- ============================================================ --}}
    {{-- RECOMMENDATION CONTENT                                      --}}
    {{-- ============================================================ --}}

    {{-- ── 1. Key Metrics (4-grid) ── --}}
    <section class="bg-white dark:bg-gray-800 border-y border-gray-200 dark:border-gray-700" aria-label="Ringkasan metrik">
        <div class="p-4 lg:p-5 border-b border-gray-100 dark:border-gray-700">
            <h2 class="text-base lg:text-lg font-semibold text-gray-900 dark:text-white leading-tight flex items-center gap-2">
                <i class="fas fa-chart-bar text-[#0a66c2]" aria-hidden="true"></i>
                Ringkasan Rekomendasi
            </h2>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Analisis {{ $totalPermits }} izin untuk KBLI {{ $kbli->code }}</p>
        </div>
        <div class="px-4 lg:px-5 py-4">
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
                {{-- Mandatory --}}
                <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                    <div class="flex items-center justify-between mb-2">
                        <p class="text-xs text-gray-500 dark:text-gray-400">Izin Wajib</p>
                        <i class="fas fa-exclamation-circle text-red-500" aria-hidden="true"></i>
                    </div>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ $mandatoryTotal }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">dari {{ $totalPermits }} rekomendasi</p>
                    <div class="mt-3 flex flex-wrap gap-1">
                        @if($mandatoryTotal > 0)
                        <span class="px-2 py-0.5 rounded-full bg-red-100 dark:bg-red-900/40 text-red-700 dark:text-red-300 text-[10px] font-semibold uppercase">Segera</span>
                        @endif
                        @if($recommendedTotal > 0)
                        <span class="px-2 py-0.5 rounded-full bg-blue-100 dark:bg-blue-900/40 text-blue-700 dark:text-blue-300 text-[10px] font-semibold uppercase">{{ $recommendedTotal }} Anjuran</span>
                        @endif
                        @if($conditionalTotal > 0)
                        <span class="px-2 py-0.5 rounded-full bg-amber-100 dark:bg-amber-900/40 text-amber-700 dark:text-amber-300 text-[10px] font-semibold uppercase">{{ $conditionalTotal }} Bersyarat</span>
                        @endif
                    </div>
                </div>

                {{-- Cost --}}
                <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                    <div class="flex items-center justify-between mb-2">
                        <p class="text-xs text-gray-500 dark:text-gray-400">Total Estimasi Biaya</p>
                        <i class="fas fa-coins text-[#0a66c2]" aria-hidden="true"></i>
                    </div>
                    @if($grandTotalMin == 0 && $grandTotalMax == 0)
                    <p class="text-2xl font-bold text-green-600 dark:text-green-400">Gratis</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Pemerintah + Konsultan</p>
                    @else
                    <p class="text-xl font-bold text-gray-900 dark:text-white">Rp {{ number_format($grandTotalMin, 0, ',', '.') }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">hingga Rp {{ number_format($grandTotalMax, 0, ',', '.') }}</p>
                    @endif
                    <div class="mt-2 space-y-0.5">
                        <p class="text-[10px] text-gray-400 dark:text-gray-500 leading-snug">
                            <i class="fas fa-landmark mr-0.5" aria-hidden="true"></i>
                            Pemerintah: Rp {{ number_format($costRangeMin, 0, ',', '.') }} – {{ number_format($costRangeMax, 0, ',', '.') }}
                        </p>
                        <p class="text-[10px] text-[#0a66c2] dark:text-blue-400 leading-snug">
                            <i class="fas fa-user-tie mr-0.5" aria-hidden="true"></i>
                            Konsultan: Rp {{ number_format($consultRangeMin, 0, ',', '.') }} – {{ number_format($consultRangeMax, 0, ',', '.') }}
                        </p>
                    </div>
                </div>

                {{-- Duration --}}
                <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                    <div class="flex items-center justify-between mb-2">
                        <p class="text-xs text-gray-500 dark:text-gray-400">Estimasi Durasi</p>
                        <i class="fas fa-clock text-[#0a66c2]" aria-hidden="true"></i>
                    </div>
                    <div class="flex items-baseline gap-1">
                        <span class="text-3xl font-bold text-gray-900 dark:text-white">{{ $timelineMin ?? '?' }}</span>
                        <span class="text-sm text-gray-500 dark:text-gray-400">–</span>
                        <span class="text-3xl font-bold text-gray-900 dark:text-white">{{ $timelineMax ?? '?' }}</span>
                    </div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">hari kerja</p>
                    <p class="text-[10px] text-gray-400 dark:text-gray-500 mt-2 leading-snug">
                        Tergantung kelengkapan dokumen
                    </p>
                </div>

                {{-- Complexity --}}
                <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                    <div class="flex items-center justify-between mb-2">
                        <p class="text-xs text-gray-500 dark:text-gray-400">Kompleksitas</p>
                        <i class="fas fa-sitemap text-[#0a66c2]" aria-hidden="true"></i>
                    </div>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $complexityLevel }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 leading-snug">{{ $complexityCopy }}</p>
                    <div class="mt-3">
                        <div class="h-2 bg-gray-100 dark:bg-gray-700 rounded-full overflow-hidden">
                            <div class="h-full {{ $complexityBarClass }} rounded-full transition-all" style="width: {{ $complexityWidth }}"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ── 2. Disclaimer ── --}}
    <section class="bg-blue-50/50 dark:bg-[#0a66c2]/5 border-y border-blue-200/70 dark:border-[#0a66c2]/30" aria-label="Informasi penting">
        <div class="px-4 lg:px-5 py-4 flex items-start gap-3">
            <i class="fas fa-info-circle text-[#0a66c2] mt-0.5 flex-shrink-0" aria-hidden="true"></i>
            <div class="flex-1 text-xs text-gray-700 dark:text-gray-300 space-y-1.5">
                <p class="font-bold text-sm text-gray-900 dark:text-white mb-2">Penting untuk Diketahui</p>
                <p class="flex items-start gap-2">
                    <i class="fas fa-database text-[#0a66c2] mt-0.5 flex-shrink-0" aria-hidden="true"></i>
                    <span><strong>Sumber Data:</strong> Perhitungan otomatis berdasarkan regulasi terkini, database perizinan nasional, dan ratusan studi kasus proyek perizinan.</span>
                </p>
                <p class="flex items-start gap-2">
                    <i class="fas fa-calculator text-[#0a66c2] mt-0.5 flex-shrink-0" aria-hidden="true"></i>
                    <span><strong>Biaya Aktual:</strong> Estimasi akan dihitung ulang sesuai kompleksitas, luas area, zonasi, dan jenis kegiatan usaha Anda.</span>
                </p>
                <p class="flex items-start gap-2">
                    <i class="fas fa-map-marked-alt text-[#0a66c2] mt-0.5 flex-shrink-0" aria-hidden="true"></i>
                    <span><strong>Variasi Regional:</strong> Persyaratan dan prosedur dapat berbeda antar daerah sesuai regulasi pemerintah daerah setempat.</span>
                </p>
                <p class="flex items-start gap-2">
                    <i class="fas fa-gavel text-[#0a66c2] mt-0.5 flex-shrink-0" aria-hidden="true"></i>
                    <span><strong>Kepastian Hukum:</strong> Untuk analisis biaya aktual, <a href="{{ route('client.applications.create', ['kbli_code' => $kbli->code]) }}" class="text-[#0a66c2] dark:text-blue-400 font-semibold hover:underline">ajukan permohonan</a> — tim kami merespons dalam 1×24 jam.</span>
                </p>
            </div>
        </div>
    </section>

    {{-- ── 3. Enhanced Cost Breakdown (if context data provided) ── --}}
    @if(isset($formattedCosts) && $formattedCosts)
    <section class="bg-white dark:bg-gray-800 border-y border-gray-200 dark:border-gray-700" aria-label="Rincian biaya">
        <div class="p-4 lg:p-5 border-b border-gray-100 dark:border-gray-700">
            <h2 class="text-base lg:text-lg font-semibold text-gray-900 dark:text-white leading-tight flex items-center gap-2">
                <i class="fas fa-calculator text-[#0a66c2]" aria-hidden="true"></i>
                Rincian Biaya Lengkap
            </h2>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Berdasarkan data konteks proyek Anda</p>
        </div>
        <div class="px-4 lg:px-5 py-4">
            {{-- Cost sections grid --}}
            <div class="grid md:grid-cols-3 gap-3 mb-4">
                @foreach($formattedCosts['sections'] as $section)
                <div class="bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 rounded-lg p-4">
                    <div class="flex items-center gap-2 mb-2">
                        <i class="fas {{ $section['icon'] }} text-[#0a66c2]" aria-hidden="true"></i>
                        <h3 class="text-sm font-semibold text-gray-900 dark:text-white">{{ $section['title'] }}</h3>
                    </div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-2">{{ $section['subtitle'] }}</p>
                    <p class="text-xl font-bold text-gray-900 dark:text-white">
                        Rp {{ number_format($section['amount']['min'], 0, ',', '.') }}
                    </p>
                    @if(($section['amount']['max'] ?? 0) > ($section['amount']['min'] ?? 0))
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                        hingga Rp {{ number_format($section['amount']['max'], 0, ',', '.') }}
                    </p>
                    @endif
                </div>
                @endforeach
            </div>

            {{-- Total --}}
            <div class="bg-[#0a66c2] text-white rounded-lg p-4 flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-semibold">Total Estimasi Investasi</h3>
                    <p class="text-xs text-white/70 mt-0.5">Biaya pemerintah + Jasa konsultan + Persiapan dokumen</p>
                </div>
                <div class="text-right">
                    <p class="text-2xl font-bold">Rp {{ number_format($formattedCosts['total']['min'], 0, ',', '.') }}</p>
                    @if(($formattedCosts['total']['max'] ?? 0) > ($formattedCosts['total']['min'] ?? 0))
                    <p class="text-xs text-white/80 mt-0.5">hingga Rp {{ number_format($formattedCosts['total']['max'], 0, ',', '.') }}</p>
                    @endif
                </div>
            </div>

            {{-- Complexity factors --}}
            @if(isset($costBreakdown['factors']))
            <div class="mt-4 grid grid-cols-2 md:grid-cols-4 gap-2">
                @foreach(['complexity' => 'Kompleksitas', 'location' => 'Lokasi', 'environmental' => 'Lingkungan', 'urgency' => 'Urgensi'] as $fKey => $fLabel)
                @if(isset($costBreakdown['factors'][$fKey]))
                <div class="text-center bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 rounded-lg p-2">
                    <p class="text-[10px] text-gray-500 dark:text-gray-400 uppercase">{{ $fLabel }}</p>
                    <p class="text-lg font-bold text-gray-900 dark:text-white">{{ number_format($costBreakdown['factors'][$fKey], 1) }}×</p>
                </div>
                @endif
                @endforeach
            </div>
            @endif

            {{-- Notes --}}
            @if(!empty($formattedCosts['notes']))
            <div class="mt-4 bg-amber-50 dark:bg-amber-900/10 border border-amber-200 dark:border-amber-800/30 rounded-lg p-3">
                <p class="text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">
                    <i class="fas fa-info-circle text-amber-500 mr-1" aria-hidden="true"></i> Catatan:
                </p>
                <ul class="text-xs text-gray-600 dark:text-gray-400 space-y-0.5">
                    @foreach($formattedCosts['notes'] as $note)
                    <li class="flex items-start gap-1.5"><span class="text-amber-400 mt-0.5">•</span> {{ $note }}</li>
                    @endforeach
                </ul>
            </div>
            @endif
        </div>
    </section>
    @endif

    {{-- ── 4. Risk Assessment ── --}}
    @if(!empty($riskAssessment))
    <section class="bg-white dark:bg-gray-800 border-y border-gray-200 dark:border-gray-700" aria-label="Penilaian risiko" x-data="{ expanded: true }">
        <div class="p-4 lg:p-5 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
            <div>
                <h2 class="text-base lg:text-lg font-semibold text-gray-900 dark:text-white leading-tight flex items-center gap-2">
                    <i class="fas fa-shield-alt text-amber-500" aria-hidden="true"></i>
                    Penilaian Risiko
                </h2>
                @if($riskClassification)
                <div class="flex items-center gap-2 mt-1">
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 {{ $riskClassBadge }} text-[10px] font-bold rounded-full uppercase tracking-wider">
                        <i class="fas fa-shield-alt" aria-hidden="true"></i>
                        {{ $riskClassification }}
                    </span>
                </div>
                @endif
            </div>
            @if(count($riskFactors) + count($riskMitigation) + count($riskPitfalls) > 0)
            <button @click="expanded = !expanded"
                    class="text-sm font-medium text-[#0a66c2] dark:text-blue-400 px-3 py-2 min-h-[44px] flex items-center active:scale-95 transition-transform">
                <span x-text="expanded ? 'Tutup' : 'Detail'"></span>
                <i class="fas fa-chevron-down ml-2 text-xs transition-transform" :class="expanded ? 'rotate-180' : ''" aria-hidden="true"></i>
            </button>
            @endif
        </div>
        <div class="px-4 lg:px-5 py-4">
            @if($riskNotes)
            <p class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed mb-3">{{ $riskNotes }}</p>
            @endif

            <div x-show="expanded" x-transition x-cloak class="space-y-4">
                {{-- Risk Factors --}}
                @if(count($riskFactors) > 0)
                <div>
                    <h3 class="text-xs font-bold text-gray-900 dark:text-white uppercase tracking-wider mb-2">Faktor Risiko</h3>
                    <ul class="space-y-1.5">
                        @foreach($riskFactors as $factor)
                        <li class="flex items-start gap-2 text-sm text-gray-700 dark:text-gray-300">
                            <i class="fas fa-exclamation-triangle text-amber-500 mt-0.5 flex-shrink-0 text-xs" aria-hidden="true"></i>
                            <span>{{ is_array($factor) ? ($factor['description'] ?? json_encode($factor)) : $factor }}</span>
                        </li>
                        @endforeach
                    </ul>
                </div>
                @endif

                {{-- Mitigation --}}
                @if(count($riskMitigation) > 0)
                <div>
                    <h3 class="text-xs font-bold text-gray-900 dark:text-white uppercase tracking-wider mb-2">Langkah Mitigasi</h3>
                    <ul class="space-y-1.5">
                        @foreach($riskMitigation as $mitigation)
                        <li class="flex items-start gap-2 text-sm text-gray-700 dark:text-gray-300">
                            <i class="fas fa-check-circle text-green-500 mt-0.5 flex-shrink-0 text-xs" aria-hidden="true"></i>
                            <span>{{ is_array($mitigation) ? ($mitigation['description'] ?? json_encode($mitigation)) : $mitigation }}</span>
                        </li>
                        @endforeach
                    </ul>
                </div>
                @endif

                {{-- Common Pitfalls --}}
                @if(count($riskPitfalls) > 0)
                <div>
                    <h3 class="text-xs font-bold text-gray-900 dark:text-white uppercase tracking-wider mb-2">Kesalahan Umum yang Perlu Dihindari</h3>
                    <ul class="space-y-1.5">
                        @foreach($riskPitfalls as $pitfall)
                        <li class="flex items-start gap-2 text-sm text-gray-700 dark:text-gray-300">
                            <i class="fas fa-ban text-red-400 mt-0.5 flex-shrink-0 text-xs" aria-hidden="true"></i>
                            <span>{{ is_array($pitfall) ? ($pitfall['description'] ?? json_encode($pitfall)) : $pitfall }}</span>
                        </li>
                        @endforeach
                    </ul>
                </div>
                @endif
            </div>
        </div>
    </section>
    @endif

    {{-- ── 5. Permits by Category ── --}}
    @if($totalPermits > 0)
    @php
        $permitsByCategory = collect($allPermits)->groupBy(fn($p) => $p['category'] ?? 'other');
    @endphp

    @foreach($permitsByCategory as $category => $categoryPermits)
    @php
        $catInfo = $categoryInfo[$category] ?? $categoryInfo['other'];
        $catMandatory = collect($categoryPermits)->where('type', 'mandatory')->count();
        $catTotal = count($categoryPermits);
    @endphp
    <section class="bg-white dark:bg-gray-800 border-y border-gray-200 dark:border-gray-700"
             aria-label="Kategori: {{ $catInfo['title'] }}"
             x-data="{ open: {{ $loop->first ? 'true' : 'false' }} }">
        {{-- Category header (clickable) --}}
        <button type="button" @click="open = !open"
                class="w-full p-4 lg:p-5 border-b border-gray-100 dark:border-gray-700 flex items-start justify-between gap-3 text-left hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
            <div class="flex items-start gap-3">
                <span class="w-10 h-10 rounded-lg bg-[#0a66c2]/10 dark:bg-[#0a66c2]/20 flex items-center justify-center flex-shrink-0">
                    <i class="fas {{ $catInfo['icon'] }} text-[#0a66c2]" aria-hidden="true"></i>
                </span>
                <div>
                    <h2 class="text-base font-bold text-gray-900 dark:text-white flex items-center flex-wrap gap-2">
                        {{ $catInfo['title'] }}
                        <span class="px-2 py-0.5 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 text-xs rounded-full font-semibold">{{ $catTotal }} izin</span>
                    </h2>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ $catInfo['desc'] }}</p>
                </div>
            </div>
            <div class="flex items-center gap-3 flex-shrink-0">
                @if($catMandatory > 0)
                <span class="hidden sm:inline-flex items-center px-2 py-1 bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300 text-xs font-semibold rounded-lg">
                    <i class="fas fa-exclamation-circle mr-1" aria-hidden="true"></i>{{ $catMandatory }} Wajib
                </span>
                @endif
                <i class="fas text-sm text-gray-400 dark:text-gray-500 transition-transform" :class="open ? 'fa-chevron-up' : 'fa-chevron-down'" aria-hidden="true"></i>
            </div>
        </button>

        {{-- Permits list --}}
        <div x-show="open" x-transition x-cloak>
            @foreach($categoryPermits as $pIdx => $permit)
            @php
                $pType     = $permit['type'] ?? 'optional';
                $pName     = $permit['name'] ?? 'Izin Tidak Diketahui';
                $pDesc     = $permit['description'] ?? null;
                $pCode     = $permit['code'] ?? null;
                $pAuthority = $permit['issuing_authority'] ?? null;
                $pLegal    = $permit['legal_basis'] ?? null;
                $pPriority = $permit['priority'] ?? null;
                $pOrder    = $permit['order'] ?? null;
                $pTotalCost = $permit['total_cost_range'] ?? null;

                // Cost: handle both key structures
                $pGovFee      = $permit['estimated_cost_range'] ?? $permit['government_fee'] ?? null;
                $pGovMin      = (int) ($pGovFee['min'] ?? 0);
                $pGovMax      = (int) ($pGovFee['max'] ?? 0);
                $pGovNote     = $pGovFee['note'] ?? null;
                $pConsultFee  = $permit['consultant_fee_range'] ?? $permit['consultant_fee'] ?? null;
                $pConsultMin  = (int) ($pConsultFee['min'] ?? 0);
                $pConsultMax  = (int) ($pConsultFee['max'] ?? 0);
                $pConsultNote = is_array($pConsultFee) ? ($pConsultFee['note'] ?? null) : null;

                // Timeline: handle both key names
                $pDuration = $permit['estimated_processing_time'] ?? $permit['estimated_timeline'] ?? null;

                // Dependencies & unlocks
                $pPrerequisites = $permit['prerequisites'] ?? $permit['dependencies'] ?? [];
                $pTriggersNext  = $permit['triggers_next'] ?? [];

                // Optional extended fields
                $pRenewal       = $permit['renewal_period'] ?? null;
                $pTimelineNotes = $permit['timeline_notes'] ?? null;
                $pDigitalReqs   = $permit['digital_requirements'] ?? [];
                $pRequirements  = $permit['requirements'] ?? [];

                // Type badge styling
                $typeBadge = match($pType) {
                    'mandatory'   => ['label' => 'WAJIB',      'icon' => 'fa-exclamation-circle', 'class' => 'bg-red-100 dark:bg-red-900/40 text-red-700 dark:text-red-300'],
                    'recommended' => ['label' => 'ANJURAN',    'icon' => 'fa-thumbs-up',          'class' => 'bg-blue-100 dark:bg-blue-900/40 text-blue-700 dark:text-blue-300'],
                    'conditional' => ['label' => 'BERSYARAT',  'icon' => 'fa-question-circle',    'class' => 'bg-amber-100 dark:bg-amber-900/40 text-amber-700 dark:text-amber-300'],
                    default       => ['label' => 'OPSIONAL',   'icon' => 'fa-circle',             'class' => 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400'],
                };

                // Priority badge styling
                $priorityBadge = match($pPriority) {
                    'critical' => ['label' => 'Kritis',   'class' => 'bg-red-500 text-white'],
                    'high'     => ['label' => 'Tinggi',   'class' => 'bg-orange-100 dark:bg-orange-900/40 text-orange-700 dark:text-orange-300'],
                    'medium'   => ['label' => 'Sedang',   'class' => 'bg-yellow-100 dark:bg-yellow-900/40 text-yellow-700 dark:text-yellow-300'],
                    'low'      => ['label' => 'Rendah',   'class' => 'bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400'],
                    default    => null,
                };
            @endphp
            <div class="px-4 lg:px-5 py-4 border-b border-gray-100 dark:border-gray-700 last:border-b-0" x-data="{ showDetail: false }">
                {{-- Permit header --}}
                <div class="flex items-start gap-3 mb-3">
                    <span class="flex-shrink-0 w-8 h-8 {{ $pPriority === 'critical' ? 'bg-red-500 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300' }} rounded-full flex items-center justify-center text-sm font-bold">
                        {{ $pOrder ?? $loop->iteration }}
                    </span>
                    <div class="flex-1 min-w-0">
                        <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-2">
                            <div>
                                <h3 class="text-sm font-bold text-gray-900 dark:text-white leading-tight">{{ $pName }}</h3>
                                @if($pCode)
                                <p class="text-[10px] font-mono text-gray-400 dark:text-gray-500 mt-0.5">{{ $pCode }}</p>
                                @endif
                            </div>
                            <div class="flex items-center gap-1.5 self-start flex-wrap">
                                @if($priorityBadge)
                                <span class="inline-flex items-center px-2 py-0.5 {{ $priorityBadge['class'] }} text-[10px] font-bold rounded-full uppercase tracking-wider">
                                    {{ $priorityBadge['label'] }}
                                </span>
                                @endif
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 {{ $typeBadge['class'] }} text-[10px] font-bold rounded-full uppercase tracking-wider">
                                    <i class="fas {{ $typeBadge['icon'] }}" aria-hidden="true"></i>
                                    {{ $typeBadge['label'] }}
                                </span>
                            </div>
                        </div>
                        @if($pDesc)
                        <p class="text-xs text-gray-600 dark:text-gray-400 mt-1.5 leading-relaxed">{{ $pDesc }}</p>
                        @endif
                    </div>
                </div>

                {{-- Key info grid (4 cols on lg) --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-2 ml-11 mb-3">
                    {{-- Issuing authority --}}
                    <div class="bg-gray-50 dark:bg-gray-700/50 border border-gray-100 dark:border-gray-600 rounded-lg p-3">
                        <p class="text-[10px] text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Penerbit</p>
                        <p class="text-xs font-semibold text-gray-900 dark:text-white leading-snug">{{ $pAuthority ?? 'N/A' }}</p>
                    </div>
                    {{-- Government cost --}}
                    <div class="bg-gray-50 dark:bg-gray-700/50 border border-gray-100 dark:border-gray-600 rounded-lg p-3">
                        <p class="text-[10px] text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">
                            <i class="fas fa-landmark mr-0.5" aria-hidden="true"></i> Biaya Pemerintah
                        </p>
                        @if($pGovMin == 0 && $pGovMax == 0)
                        <p class="text-xs font-semibold text-green-600 dark:text-green-400">Gratis</p>
                        @else
                        <p class="text-xs font-semibold text-gray-900 dark:text-white">
                            Rp {{ number_format($pGovMin, 0, ',', '.') }}
                            @if($pGovMax > $pGovMin)
                            <span class="font-normal text-gray-500">– {{ number_format($pGovMax, 0, ',', '.') }}</span>
                            @endif
                        </p>
                        @endif
                        @if($pGovNote)
                        <p class="text-[10px] text-gray-400 dark:text-gray-500 mt-0.5 italic">{{ $pGovNote }}</p>
                        @endif
                    </div>
                    {{-- Consultant cost --}}
                    <div class="bg-gray-50 dark:bg-gray-700/50 border border-gray-100 dark:border-gray-600 rounded-lg p-3">
                        <p class="text-[10px] text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">
                            <i class="fas fa-user-tie mr-0.5" aria-hidden="true"></i> Jasa Konsultan
                        </p>
                        @if($pConsultMin == 0 && $pConsultMax == 0)
                        <p class="text-xs font-semibold text-gray-600 dark:text-gray-400">N/A</p>
                        @else
                        <p class="text-xs font-semibold text-[#0a66c2] dark:text-blue-400">
                            Rp {{ number_format($pConsultMin, 0, ',', '.') }}
                            @if($pConsultMax > $pConsultMin)
                            <span class="font-normal">– {{ number_format($pConsultMax, 0, ',', '.') }}</span>
                            @endif
                        </p>
                        @endif
                        @if($pConsultNote)
                        <p class="text-[10px] text-gray-400 dark:text-gray-500 mt-0.5 italic">{{ $pConsultNote }}</p>
                        @endif
                    </div>
                    {{-- Processing time --}}
                    <div class="bg-gray-50 dark:bg-gray-700/50 border border-gray-100 dark:border-gray-600 rounded-lg p-3">
                        <p class="text-[10px] text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">
                            <i class="fas fa-clock mr-0.5" aria-hidden="true"></i> Durasi Proses
                        </p>
                        <p class="text-xs font-semibold text-gray-900 dark:text-white">{{ $pDuration ?? 'N/A' }}</p>
                        @if($pTimelineNotes)
                        <p class="text-[10px] text-gray-400 dark:text-gray-500 mt-0.5">{{ $pTimelineNotes }}</p>
                        @endif
                    </div>
                </div>

                {{-- Total cost per permit (if available) --}}
                @if($pTotalCost)
                <div class="ml-11 mb-3">
                    <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-[#0a66c2]/5 dark:bg-[#0a66c2]/10 border border-[#0a66c2]/20 rounded-lg">
                        <i class="fas fa-coins text-[#0a66c2] text-xs" aria-hidden="true"></i>
                        <p class="text-xs font-semibold text-gray-900 dark:text-white">Total: {{ $pTotalCost }}</p>
                    </div>
                </div>
                @endif

                {{-- Legal basis + renewal (prominent) --}}
                @if($pLegal || $pRenewal)
                <div class="ml-11 mb-3 grid grid-cols-1 sm:grid-cols-2 gap-2">
                    @if($pLegal)
                    <div class="flex items-start gap-2 text-xs text-gray-600 dark:text-gray-400 bg-gray-50 dark:bg-gray-700/50 border border-gray-100 dark:border-gray-600 rounded-lg p-2.5">
                        <i class="fas fa-gavel text-[#0a66c2] mt-0.5 flex-shrink-0" aria-hidden="true"></i>
                        <div>
                            <p class="text-[10px] text-gray-500 dark:text-gray-400 uppercase tracking-wider">Dasar Hukum</p>
                            <p class="text-xs font-medium text-gray-900 dark:text-white mt-0.5">{{ $pLegal }}</p>
                        </div>
                    </div>
                    @endif
                    @if($pRenewal)
                    <div class="flex items-start gap-2 text-xs text-gray-600 dark:text-gray-400 bg-gray-50 dark:bg-gray-700/50 border border-gray-100 dark:border-gray-600 rounded-lg p-2.5">
                        <i class="fas fa-redo text-[#0a66c2] mt-0.5 flex-shrink-0" aria-hidden="true"></i>
                        <div>
                            <p class="text-[10px] text-gray-500 dark:text-gray-400 uppercase tracking-wider">Perpanjangan</p>
                            <p class="text-xs font-medium text-gray-900 dark:text-white mt-0.5">{{ $pRenewal }}</p>
                        </div>
                    </div>
                    @endif
                </div>
                @endif

                {{-- Tags: dependencies, triggers, digital --}}
                <div class="ml-11 flex flex-wrap gap-1.5 mb-2">
                    @if(count($pPrerequisites) > 0)
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 text-[10px] font-semibold uppercase">
                        <i class="fas fa-link" aria-hidden="true"></i> {{ count($pPrerequisites) }} Prasyarat
                    </span>
                    @endif
                    @if(count($pTriggersNext) > 0)
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 text-[10px] font-semibold uppercase">
                        <i class="fas fa-unlock" aria-hidden="true"></i> Membuka {{ count($pTriggersNext) }} Izin
                    </span>
                    @endif
                    @if(count($pDigitalReqs) > 0)
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 text-[10px] font-semibold uppercase">
                        <i class="fas fa-laptop-code" aria-hidden="true"></i> Digital Ready
                    </span>
                    @endif
                </div>

                {{-- Expandable detail --}}
                @if(count($pPrerequisites) + count($pTriggersNext) + count($pDigitalReqs) + count($pRequirements) > 0)
                <div class="ml-11">
                    <button type="button" @click="showDetail = !showDetail"
                            class="text-xs text-[#0a66c2] dark:text-blue-400 font-semibold inline-flex items-center gap-1.5 hover:text-[#004182] dark:hover:text-blue-300 transition-colors py-1 min-h-[44px]">
                        <i class="fas" :class="showDetail ? 'fa-minus-circle' : 'fa-plus-circle'" aria-hidden="true"></i>
                        <span x-text="showDetail ? 'Sembunyikan Detail' : 'Lihat Detail Lengkap'"></span>
                    </button>

                    <div x-show="showDetail" x-transition x-cloak class="mt-2 space-y-3 pb-1">
                        {{-- Prerequisites --}}
                        @if(count($pPrerequisites) > 0)
                        <div>
                            <p class="text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">
                                <i class="fas fa-link text-[#0a66c2] mr-1" aria-hidden="true"></i>
                                Harus diselesaikan terlebih dahulu:
                            </p>
                            <div class="flex flex-wrap gap-1.5">
                                @foreach($pPrerequisites as $prereq)
                                <span class="inline-flex items-center px-2.5 py-1 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-700 text-amber-800 dark:text-amber-300 text-xs rounded-lg">
                                    <i class="fas fa-arrow-right text-amber-400 mr-1 text-[10px]" aria-hidden="true"></i>
                                    {{ $prereq }}
                                </span>
                                @endforeach
                            </div>
                        </div>
                        @endif

                        {{-- Triggers next --}}
                        @if(count($pTriggersNext) > 0)
                        <div>
                            <p class="text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">
                                <i class="fas fa-unlock text-green-500 mr-1" aria-hidden="true"></i>
                                Setelah selesai, Anda dapat mengajukan:
                            </p>
                            <div class="flex flex-wrap gap-1.5">
                                @foreach($pTriggersNext as $next)
                                <span class="inline-flex items-center px-2.5 py-1 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-700 text-green-800 dark:text-green-300 text-xs rounded-lg">
                                    <i class="fas fa-check text-green-400 mr-1 text-[10px]" aria-hidden="true"></i>
                                    {{ $next }}
                                </span>
                                @endforeach
                            </div>
                        </div>
                        @endif

                        {{-- Digital Requirements --}}
                        @if(count($pDigitalReqs) > 0)
                        <div>
                            <p class="text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">
                                <i class="fas fa-laptop-code text-[#0a66c2] mr-1" aria-hidden="true"></i>
                                Kebutuhan Digital:
                            </p>
                            <div class="flex flex-wrap gap-1.5">
                                @foreach($pDigitalReqs as $dReq)
                                <span class="inline-flex items-center px-2.5 py-1 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 text-xs rounded-lg">{{ $dReq }}</span>
                                @endforeach
                            </div>
                        </div>
                        @endif

                        {{-- Per-permit requirements (if available) --}}
                        @if(count($pRequirements) > 0)
                        <div>
                            <p class="text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">
                                <i class="fas fa-clipboard-list text-[#0a66c2] mr-1" aria-hidden="true"></i>
                                Persyaratan ({{ count($pRequirements) }}):
                            </p>
                            <ul class="ml-4 space-y-1 text-xs text-gray-700 dark:text-gray-300 list-disc list-outside">
                                @foreach($pRequirements as $req)
                                <li>{{ $req }}</li>
                                @endforeach
                            </ul>
                        </div>
                        @endif
                    </div>
                </div>
                @endif
            </div>
            @endforeach
        </div>
    </section>
    @endforeach

    @else
    {{-- No permits --}}
    <section class="bg-white dark:bg-gray-800 border-y border-gray-200 dark:border-gray-700" aria-label="Tidak ada rekomendasi">
        <div class="px-4 lg:px-5 py-8 text-center">
            <div class="w-14 h-14 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center mx-auto mb-3">
                <i class="fas fa-search text-gray-400 dark:text-gray-500 text-xl" aria-hidden="true"></i>
            </div>
            <p class="text-sm font-semibold text-gray-700 dark:text-gray-300">Belum Ada Rekomendasi</p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Tidak ada izin yang ditemukan untuk KBLI ini. Hubungi tim kami untuk analisis manual.</p>
        </div>
    </section>
    @endif

    {{-- ── 6. Required Documents ── --}}
    @if(!empty($recommendation->required_documents))
    <section class="bg-white dark:bg-gray-800 border-y border-gray-200 dark:border-gray-700" aria-label="Dokumen yang dibutuhkan">
        <div class="p-4 lg:p-5 border-b border-gray-100 dark:border-gray-700">
            <h2 class="text-base lg:text-lg font-semibold text-gray-900 dark:text-white leading-tight flex items-center gap-2">
                <i class="fas fa-folder-open text-[#0a66c2]" aria-hidden="true"></i>
                Dokumen yang Dibutuhkan
            </h2>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">{{ count($recommendation->required_documents) }} dokumen untuk seluruh proses perizinan</p>
        </div>
        <div class="px-4 lg:px-5 py-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                @foreach($recommendation->required_documents as $dIdx => $doc)
                @php
                    $docName   = is_array($doc) ? ($doc['name'] ?? 'Dokumen') : $doc;
                    $docNotes  = is_array($doc) ? ($doc['notes'] ?? null) : null;
                    $docType   = is_array($doc) ? ($doc['type'] ?? null) : null;
                    $docFormat = is_array($doc) ? ($doc['format'] ?? null) : null;
                @endphp
                <div class="flex items-start gap-3 p-3 bg-gray-50 dark:bg-gray-700/50 border border-gray-100 dark:border-gray-600 rounded-lg">
                    <span class="flex-shrink-0 w-7 h-7 bg-[#0a66c2]/10 dark:bg-[#0a66c2]/20 rounded flex items-center justify-center">
                        <i class="fas fa-file-alt text-[#0a66c2] text-xs" aria-hidden="true"></i>
                    </span>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-semibold text-gray-900 dark:text-white leading-tight">
                            {{ $dIdx + 1 }}. {{ $docName }}
                        </p>
                        @if($docNotes)
                        <p class="text-[10px] text-gray-500 dark:text-gray-400 mt-0.5 leading-relaxed">{{ $docNotes }}</p>
                        @endif
                        @if($docType || $docFormat)
                        <div class="flex items-center gap-1.5 mt-1">
                            @if($docType)
                            <span class="px-1.5 py-0.5 bg-[#0a66c2]/10 text-[#0a66c2] text-[10px] font-medium rounded">{{ ucfirst($docType) }}</span>
                            @endif
                            @if($docFormat)
                            <span class="px-1.5 py-0.5 bg-gray-200 dark:bg-gray-600 text-gray-700 dark:text-gray-300 text-[10px] font-medium rounded">{{ strtoupper($docFormat) }}</span>
                            @endif
                        </div>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    {{-- ── 7. Timeline ── --}}
    @if(!empty($recommendation->estimated_timeline))
    <section class="bg-white dark:bg-gray-800 border-y border-gray-200 dark:border-gray-700" aria-label="Timeline proses">
        <div class="p-4 lg:p-5 border-b border-gray-100 dark:border-gray-700">
            <h2 class="text-base lg:text-lg font-semibold text-gray-900 dark:text-white leading-tight flex items-center gap-2">
                <i class="fas fa-calendar-alt text-[#0a66c2]" aria-hidden="true"></i>
                Timeline Proses
            </h2>
        </div>
        <div class="px-4 lg:px-5 py-4">
            {{-- Timeline summary --}}
            <div class="bg-[#0a66c2]/5 dark:bg-[#0a66c2]/10 border border-[#0a66c2]/20 rounded-lg p-4 mb-4 flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-0.5">Estimasi Total</p>
                    <p class="text-xl font-bold text-gray-900 dark:text-white">
                        @if($timelineMin && $timelineMax)
                            {{ $timelineMin }} – {{ $timelineMax }} Hari Kerja
                        @elseif($timelineSummary)
                            {{ $timelineSummary }}
                        @else
                            Hubungi konsultan
                        @endif
                    </p>
                </div>
                <i class="fas fa-clock text-3xl text-[#0a66c2]/50" aria-hidden="true"></i>
            </div>

            {{-- Critical path --}}
            @if(count($criticalPath) > 0)
            <div>
                <h3 class="text-xs font-bold text-gray-900 dark:text-white uppercase tracking-wider mb-3">Jalur Kritis</h3>
                <div class="space-y-0" role="list">
                    @foreach($criticalPath as $cpIdx => $step)
                    <div class="flex items-start gap-3 relative" role="listitem">
                        {{-- Vertical line --}}
                        @if(!$loop->last)
                        <div class="absolute left-3 top-7 w-0.5 h-full bg-[#0a66c2]/20"></div>
                        @endif
                        <span class="relative z-10 flex-shrink-0 w-6 h-6 bg-[#0a66c2] rounded-full flex items-center justify-center">
                            <span class="text-white font-bold text-[10px]">{{ $cpIdx + 1 }}</span>
                        </span>
                        <p class="flex-1 text-sm text-gray-800 dark:text-gray-200 pb-4 leading-relaxed">
                            {{ is_array($step) ? ($step['description'] ?? json_encode($step)) : $step }}
                        </p>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </section>
    @endif

    {{-- ── 8. Next Steps (from AI analysis) ── --}}
    @if(!empty($nextSteps))
    <section class="bg-white dark:bg-gray-800 border-y border-gray-200 dark:border-gray-700" aria-label="Langkah-langkah selanjutnya">
        <div class="p-4 lg:p-5 border-b border-gray-100 dark:border-gray-700">
            <h2 class="text-base lg:text-lg font-semibold text-gray-900 dark:text-white leading-tight flex items-center gap-2">
                <i class="fas fa-list-ol text-[#0a66c2]" aria-hidden="true"></i>
                Langkah-Langkah yang Direkomendasikan
            </h2>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Urutan tindakan berdasarkan analisis AI</p>
        </div>
        <div class="px-4 lg:px-5 py-4">
            <div class="space-y-0" role="list">
                @foreach($nextSteps as $nsIdx => $step)
                <div class="flex items-start gap-3 relative" role="listitem">
                    @if(!$loop->last)
                    <div class="absolute left-3 top-7 w-0.5 h-full bg-[#0a66c2]/20"></div>
                    @endif
                    <span class="relative z-10 flex-shrink-0 w-6 h-6 bg-[#0a66c2] rounded-full flex items-center justify-center">
                        <span class="text-white font-bold text-[10px]">{{ $nsIdx + 1 }}</span>
                    </span>
                    <p class="flex-1 text-sm text-gray-800 dark:text-gray-200 pb-4 leading-relaxed">
                        {{ is_array($step) ? ($step['description'] ?? json_encode($step)) : $step }}
                    </p>
                </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    {{-- ── 9. Additional Notes (from AI) ── --}}
    @if($additionalNotes)
    <section class="bg-white dark:bg-gray-800 border-y border-gray-200 dark:border-gray-700" aria-label="Catatan tambahan">
        <div class="p-4 lg:p-5 border-b border-gray-100 dark:border-gray-700">
            <h2 class="text-base lg:text-lg font-semibold text-gray-900 dark:text-white leading-tight flex items-center gap-2">
                <i class="fas fa-sticky-note text-[#0a66c2]" aria-hidden="true"></i>
                Catatan Tambahan
            </h2>
        </div>
        <div class="px-4 lg:px-5 py-4">
            <p class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed whitespace-pre-line">{{ $additionalNotes }}</p>
        </div>
    </section>
    @endif

    {{-- ── 10. Limitations / Disclaimer ── --}}
    @if($limitations)
    <section class="bg-blue-50/50 dark:bg-blue-900/10 border-y border-blue-200/50 dark:border-blue-800/30" aria-label="Catatan analisis">
        <div class="px-4 lg:px-5 py-4 flex items-start gap-3">
            <i class="fas fa-info-circle text-[#0a66c2] mt-0.5 flex-shrink-0" aria-hidden="true"></i>
            <div>
                <p class="text-xs font-bold text-gray-900 dark:text-white mb-1">Catatan Penting</p>
                <p class="text-xs text-gray-700 dark:text-gray-300 leading-relaxed">{{ $limitations }}</p>
            </div>
        </div>
    </section>
    @endif

    {{-- ── 11. Action Buttons ── --}}
    <section class="bg-white dark:bg-gray-800 border-y border-gray-200 dark:border-gray-700" aria-label="Ajukan permohonan">
        <div class="p-4 lg:p-5 border-b border-gray-100 dark:border-gray-700">
            <h2 class="text-base lg:text-lg font-semibold text-gray-900 dark:text-white leading-tight flex items-center gap-2">
                <i class="fas fa-arrow-circle-right text-[#0a66c2]" aria-hidden="true"></i>
                Siap Memulai?
            </h2>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Butuh pendampingan penuh sampai izin terbit?</p>
        </div>
        <div class="px-4 lg:px-5 py-5">
            {{-- Risk classification badge --}}
            @if($riskClassification)
            <div class="mb-4 flex items-center gap-2">
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 {{ $riskClassBadge }} text-xs font-semibold rounded-lg">
                    <i class="fas fa-shield-alt" aria-hidden="true"></i>
                    Klasifikasi: {{ $riskClassification }}
                </span>
                @if($complexityLevel)
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 {{ $complexityBadgeClass }} text-xs font-semibold rounded-lg">
                    <i class="fas fa-sitemap" aria-hidden="true"></i>
                    Kompleksitas: {{ $complexityLevel }}
                </span>
                @endif
            </div>
            @endif

            <p class="text-sm text-gray-600 dark:text-gray-300 mb-5 leading-relaxed">
                Konsultan BizMark siap membantu penyusunan dokumen, koordinasi instansi, dan monitoring progres melalui dashboard klien.
            </p>

            <div class="flex flex-col sm:flex-row gap-3">
                <a href="{{ route('client.services.downloadSummary', $kbli->code) }}"
                   class="flex-1 sm:flex-none px-5 py-3 bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200 text-sm font-semibold rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition inline-flex items-center justify-center gap-2 min-h-[44px] active:scale-95">
                    <i class="fas fa-file-download" aria-hidden="true"></i>
                    Download Ringkasan PDF
                </a>
                <a href="{{ route('client.applications.create', ['kbli_code' => $kbli->code]) }}"
                   class="flex-1 sm:flex-none px-6 py-3 bg-[#0a66c2] hover:bg-[#004182] text-white font-semibold text-sm rounded-lg transition-all shadow-sm hover:shadow-md inline-flex items-center justify-center gap-2 min-h-[44px] active:scale-95">
                    <i class="fas fa-paper-plane" aria-hidden="true"></i>
                    Ajukan Permohonan / Konsultasi
                </a>
                <a href="{{ $supportWhatsapp }}" target="_blank" rel="noopener noreferrer"
                   class="flex-1 sm:flex-none px-5 py-3 bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-300 text-sm font-semibold rounded-lg hover:bg-emerald-100 dark:hover:bg-emerald-900/30 transition inline-flex items-center justify-center gap-2 min-h-[44px] active:scale-95 border border-emerald-200 dark:border-emerald-800">
                    <i class="fab fa-whatsapp" aria-hidden="true"></i>
                    WhatsApp
                </a>
            </div>
        </div>
    </section>

    {{-- ── 12. AI Transparency Footer ── --}}
    <section class="bg-gray-50 dark:bg-gray-900/50 border-y border-gray-200 dark:border-gray-700" aria-label="Metadata analisis">
        <div class="px-4 lg:px-5 py-3 flex flex-wrap items-center justify-center gap-x-4 gap-y-1 text-[10px] text-gray-400 dark:text-gray-500">
            <span>
                <i class="fas fa-gavel mr-1" aria-hidden="true"></i>
                Regulasi 2026 (UU 6/2023)
            </span>
            @if($aiModel)
            <span>
                <i class="fas fa-robot mr-1" aria-hidden="true"></i>
                {{ $aiModel }}
            </span>
            @endif
            <span>
                <i class="fas fa-bullseye mr-1" aria-hidden="true"></i>
                Akurasi: {{ $confidencePercent }}%
            </span>
            @if($riskClassification)
            <span>
                <i class="fas fa-shield-alt mr-1" aria-hidden="true"></i>
                {{ $riskClassification }}
            </span>
            @endif
            @if($cacheHits > 0)
            <span>
                <i class="fas fa-bolt mr-1" aria-hidden="true"></i>
                Cache hit: {{ $cacheHits }}×
            </span>
            @endif
            <span>
                <i class="fas fa-calendar mr-1" aria-hidden="true"></i>
                {{ now()->locale('id')->isoFormat('D MMM YYYY HH:mm') }}
            </span>
        </div>
    </section>

    @endif {{-- end @if($recommendation) --}}

</div>
