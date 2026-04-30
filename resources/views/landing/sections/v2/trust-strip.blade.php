@php
    $locale = $locale ?? app()->getLocale();
    $isEn = $locale === 'en';
    $clients = config('landing.clients', []);
    $featuredClients = collect($clients)->take(8);
@endphp

{{-- ────────────────────────────────────────────────
     TRUST STRIP — Client logos + certifications
     Early social proof (Cialdini principle)
──────────────────────────────────────────────── --}}
<section class="section-v2-sm border-y bg-[var(--bg-raised)] border-white/10"
         aria-label="{{ $isEn ? 'Trusted by' : 'Dipercaya oleh' }}">
    <div class="container-wide">
        <div class="text-center mb-6">
            <span class="eyebrow">{{ $isEn ? 'Trusted By' : 'Dipercaya Oleh' }}</span>
        </div>

        @if($featuredClients->count() > 0)
        <div class="logo-row mb-8">
            @foreach($featuredClients as $clientName)
                <div class="logo-mono" title="{{ $clientName }}">
                    <span>{{ \Illuminate\Support\Str::limit($clientName, 24) }}</span>
                </div>
            @endforeach
        </div>
        @endif

        <div class="flex flex-wrap items-center justify-center gap-3">
            <span class="cert-badge">
                <i class="fas fa-certificate"></i>
                ISO 9001:2015
            </span>
            <span class="cert-badge">
                <i class="fas fa-shield-halved"></i>
                {{ $isEn ? 'OSS-RBA Partner' : 'Mitra OSS-RBA' }}
            </span>
            <span class="cert-badge">
                <i class="fas fa-globe"></i>
                {{ $isEn ? 'Bilingual ID / EN' : 'Bilingual ID / EN' }}
            </span>
            <span class="cert-badge">
                <i class="fas fa-map-marked-alt"></i>
                {{ $isEn ? 'Nationwide coverage' : 'Cakupan se-Indonesia' }}
            </span>
        </div>
    </div>
</section>
