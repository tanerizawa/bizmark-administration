{{-- Create Package — Portal v2 --}}
@php
    $ctx = $businessContext ?? [];
    $hasContext = !empty($ctx);
    $prefilledLocation = '';
    if (!empty($ctx['district']))  $prefilledLocation .= $ctx['district'] . ', ';
    if (!empty($ctx['city']))      $prefilledLocation .= $ctx['city'] . ', ';
    if (!empty($ctx['province']))  $prefilledLocation .= $ctx['province'];
    $prefilledLocation = rtrim($prefilledLocation, ', ');
    $prefillLandArea     = old('land_area', $ctx['land_area'] ?? '');
    $prefillBuildingArea = old('building_area', $ctx['building_area'] ?? '');
    $prefillFloors       = old('building_floors', $ctx['number_of_floors'] ?? '');
    $prefillInvestment   = old('investment_value', $ctx['investment_value'] ?? '');
    $prefillLocation     = old('project_location', $prefilledLocation);
    $scaleLabels    = ['mikro'=>'Usaha Mikro','kecil'=>'Usaha Kecil','menengah'=>'Usaha Menengah','besar'=>'Usaha Besar'];
    $impactLabels   = ['low'=>'Rendah','medium'=>'Menengah','high'=>'Tinggi'];
    $locationLabels = ['perkotaan'=>'Area Perkotaan','pedesaan'=>'Area Pedesaan','kawasan_industri'=>'Kawasan Industri'];
    $permitCount = count(session('permit_selection.permits', []));
@endphp

{{-- ─── HERO ─── --}}
<section class="portal-hero relative overflow-hidden border-b border-[var(--border-subtle)]"
         style="background: linear-gradient(135deg, var(--client-primary) 0%, color-mix(in oklab, var(--client-primary) 55%, #001020) 100%); color:#fff;"
         aria-label="Detail Proyek">
    <div class="portal-glow-orb portal-glow-orb--tr hidden lg:block" aria-hidden="true"
         style="background: radial-gradient(circle at 50% 50%, rgba(255,255,255,0.16) 0%, transparent 70%);"></div>
    <div class="relative max-w-[1400px] mx-auto px-4 lg:px-8 py-5 lg:py-7">
        <nav class="flex items-center gap-1.5 text-[11px] text-white/60 mb-3">
            <a href="{{ route('client.services.index') }}" class="hover:text-white transition-colors">Katalog</a>
            <i class="fas fa-chevron-right text-[8px]" aria-hidden="true"></i>
            <a href="{{ route('client.services.show', session('permit_selection.kbli_code')) }}" class="hover:text-white transition-colors">Pilih Izin</a>
            <i class="fas fa-chevron-right text-[8px]" aria-hidden="true"></i>
            <span class="text-white/90">Detail Proyek</span>
        </nav>
        <span class="portal-eyebrow" style="background: rgba(255,255,255,0.15); color: rgba(255,255,255,0.9); border-color: rgba(255,255,255,0.2);">
            <i class="fas fa-layer-group text-[9px]" aria-hidden="true"></i>
            Paket {{ $permitCount }} Izin
        </span>
        <h1 class="mt-2 text-xl font-bold text-white">Informasi Proyek</h1>
        <p class="mt-1 text-sm text-white/80">Lengkapi detail proyek untuk memproses {{ $permitCount }} izin yang dipilih.</p>
    </div>
</section>

{{-- ─── MAIN ─── --}}
<div class="max-w-[860px] mx-auto px-4 lg:px-8 py-6 space-y-5">

    {{-- Auto-fill banner --}}
    @if($hasContext)
    <div class="flex items-start gap-3 bg-[var(--client-primary)]/5 border border-[var(--client-primary)]/20 rounded-xl p-4">
        <div class="w-8 h-8 rounded-full bg-[var(--client-primary)]/10 flex items-center justify-center flex-shrink-0 mt-0.5">
            <i class="fas fa-magic text-[var(--client-primary)] text-sm" aria-hidden="true"></i>
        </div>
        <div>
            <p class="text-sm font-semibold text-[var(--text-primary)]">Data otomatis terisi dari analisis sebelumnya</p>
            <p class="text-xs text-[var(--text-secondary)] mt-0.5">Beberapa kolom telah diisi. Anda dapat mengubahnya jika perlu.</p>
            <div class="flex flex-wrap gap-1.5 mt-2">
                @if(!empty($ctx['business_scale']))
                <span class="text-[10px] font-semibold px-2 py-0.5 bg-[var(--surface-cool)] border border-[var(--border-subtle)] rounded-full text-[var(--text-secondary)]">
                    <i class="fas fa-chart-bar text-[var(--client-primary)] mr-1" aria-hidden="true"></i>
                    {{ $scaleLabels[$ctx['business_scale']] ?? $ctx['business_scale'] }}
                </span>
                @endif
                @if(!empty($ctx['location_category']))
                <span class="text-[10px] font-semibold px-2 py-0.5 bg-[var(--surface-cool)] border border-[var(--border-subtle)] rounded-full text-[var(--text-secondary)]">
                    <i class="fas fa-map-marker-alt text-[var(--client-primary)] mr-1" aria-hidden="true"></i>
                    {{ $locationLabels[$ctx['location_category']] ?? $ctx['location_category'] }}
                </span>
                @endif
                @if(!empty($ctx['number_of_employees']))
                <span class="text-[10px] font-semibold px-2 py-0.5 bg-[var(--surface-cool)] border border-[var(--border-subtle)] rounded-full text-[var(--text-secondary)]">
                    <i class="fas fa-users text-[var(--client-primary)] mr-1" aria-hidden="true"></i>
                    {{ $ctx['number_of_employees'] }} Karyawan
                </span>
                @endif
            </div>
        </div>
    </div>
    @endif

    {{-- Errors --}}
    @if($errors->any())
    <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl px-4 py-3 text-sm text-red-700 dark:text-red-300">
        <i class="fas fa-triangle-exclamation mr-2" aria-hidden="true"></i>{{ $errors->first() }}
    </div>
    @endif

    <form action="{{ route('client.applications.store-package') }}" method="POST" enctype="multipart/form-data">
        @csrf

        {{-- Project Info --}}
        <div class="bg-[var(--surface-elevated)] border border-[var(--border-subtle)] rounded-xl p-5 space-y-4">
            <h2 class="text-sm font-bold text-[var(--text-primary)] flex items-center gap-2">
                <i class="fas fa-building text-[var(--client-primary)] text-sm" aria-hidden="true"></i>
                Detail Proyek
            </h2>
            <div>
                <label class="block text-xs font-semibold text-[var(--text-primary)] mb-1.5">Nama Proyek <span class="text-[var(--apple-red)]">*</span></label>
                <input type="text" name="project_name" required
                       value="{{ old('project_name', $client->company_name ?? '') }}"
                       placeholder="Mis. Pembangunan Ruko di Jakarta Selatan"
                       class="w-full px-3 py-2.5 bg-[var(--surface-cool)] border border-[var(--border-subtle)] rounded-lg text-sm text-[var(--text-primary)] focus:ring-2 focus:ring-[var(--client-primary)]">
            </div>
            <div>
                <label class="block text-xs font-semibold text-[var(--text-primary)] mb-1.5">Lokasi Proyek <span class="text-[var(--apple-red)]">*</span></label>
                <textarea name="project_location" required rows="2"
                          placeholder="Kecamatan, Kota/Kabupaten, Provinsi"
                          class="w-full px-3 py-2.5 bg-[var(--surface-cool)] border border-[var(--border-subtle)] rounded-lg text-sm text-[var(--text-primary)] focus:ring-2 focus:ring-[var(--client-primary)] resize-none">{{ $prefillLocation }}</textarea>
            </div>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-[var(--text-primary)] mb-1.5">Luas Tanah (m²)</label>
                    <input type="number" name="land_area" min="0" step="0.01"
                           value="{{ $prefillLandArea }}" placeholder="500"
                           class="w-full px-3 py-2.5 bg-[var(--surface-cool)] border border-[var(--border-subtle)] rounded-lg text-sm text-[var(--text-primary)] focus:ring-2 focus:ring-[var(--client-primary)]">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-[var(--text-primary)] mb-1.5">Luas Bangunan (m²)</label>
                    <input type="number" name="building_area" min="0" step="0.01"
                           value="{{ $prefillBuildingArea }}" placeholder="300"
                           class="w-full px-3 py-2.5 bg-[var(--surface-cool)] border border-[var(--border-subtle)] rounded-lg text-sm text-[var(--text-primary)] focus:ring-2 focus:ring-[var(--client-primary)]">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-[var(--text-primary)] mb-1.5">Jumlah Lantai</label>
                    <input type="number" name="building_floors" min="1" step="1"
                           value="{{ $prefillFloors }}" placeholder="2"
                           class="w-full px-3 py-2.5 bg-[var(--surface-cool)] border border-[var(--border-subtle)] rounded-lg text-sm text-[var(--text-primary)] focus:ring-2 focus:ring-[var(--client-primary)]">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-[var(--text-primary)] mb-1.5">Nilai Investasi (Rp)</label>
                    <input type="number" name="investment_value" min="0" step="1000000"
                           value="{{ $prefillInvestment }}" placeholder="1000000000"
                           class="w-full px-3 py-2.5 bg-[var(--surface-cool)] border border-[var(--border-subtle)] rounded-lg text-sm text-[var(--text-primary)] focus:ring-2 focus:ring-[var(--client-primary)]">
                </div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-[var(--text-primary)] mb-1.5">Target Selesai</label>
                    <input type="date" name="target_completion_date"
                           value="{{ old('target_completion_date') }}"
                           class="w-full px-3 py-2.5 bg-[var(--surface-cool)] border border-[var(--border-subtle)] rounded-lg text-sm text-[var(--text-primary)] focus:ring-2 focus:ring-[var(--client-primary)]">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-[var(--text-primary)] mb-1.5">Dokumen Pendukung (opsional)</label>
                    <input type="file" name="supporting_documents[]" multiple accept=".pdf,.jpg,.png"
                           class="w-full text-xs text-[var(--text-secondary)] file:mr-3 file:py-2 file:px-3 file:border-0 file:bg-[var(--client-primary)] file:text-white file:text-xs file:font-semibold file:rounded-lg file:cursor-pointer cursor-pointer">
                </div>
            </div>
            <div>
                <label class="block text-xs font-semibold text-[var(--text-primary)] mb-1.5">Deskripsi Proyek</label>
                <textarea name="project_description" rows="3"
                          placeholder="Deskripsi singkat tentang proyek dan kebutuhan perizinan…"
                          class="w-full px-3 py-2.5 bg-[var(--surface-cool)] border border-[var(--border-subtle)] rounded-lg text-sm text-[var(--text-primary)] focus:ring-2 focus:ring-[var(--client-primary)] resize-none">{{ old('project_description') }}</textarea>
            </div>
        </div>

        {{-- Actions --}}
        <div class="flex items-center justify-between mt-5">
            <a href="{{ route('client.services.show', session('permit_selection.kbli_code')) }}"
               class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-semibold text-[var(--text-secondary)] bg-[var(--surface-cool)] border border-[var(--border-subtle)] rounded-xl hover:border-[var(--client-primary)]/40 transition-colors">
                <i class="fas fa-arrow-left text-xs" aria-hidden="true"></i> Kembali
            </a>
            <button type="submit"
                    class="inline-flex items-center gap-2 px-5 py-2.5 bg-[var(--client-primary)] text-white text-sm font-semibold rounded-xl hover:brightness-110 transition-all">
                <i class="fas fa-paper-plane text-xs" aria-hidden="true"></i>
                Ajukan {{ $permitCount }} Izin
            </button>
        </div>
    </form>
</div>
