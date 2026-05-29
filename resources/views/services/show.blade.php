@extends('landing.layout')

@section('title', $title ?? ($service['title'] . ' - Bizmark.ID'))
@section('meta_description', $meta_description ?? $service['short_description'])
@section('meta_keywords', $service['meta_keywords'] ?? '')

@if(!empty($service['faq']))
@section('structured_data')
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@type": "FAQPage",
    "mainEntity": [
        @foreach($service['faq'] as $i => $faq)
        {
            "@@type": "Question",
            "name": {{ json_encode($faq['q']) }},
            "acceptedAnswer": {
                "@@type": "Answer",
                "text": {{ json_encode($faq['a']) }}
            }
        }{{ !$loop->last ? ',' : '' }}
        @endforeach
    ]
}
</script>
@endsection
@endif

@section('content')
@php
    $contact = (array) data_get(config('landing_metrics'), 'contact', []);
    $whatsapp = $contact['whatsapp_link'] ?? 'https://wa.me/6283879602855';
    $waText = 'Halo, saya ingin konsultasi layanan ' . ($service['title'] ?? '');
    $waHref = $whatsapp . (str_contains($whatsapp, '?') ? '&' : '?') . 'text=' . rawurlencode($waText);
    $serviceSlug = request()->route('slug') ?? ($service['slug'] ?? '');
    $partial = $serviceSlug ? 'services.partials.' . $serviceSlug : null;
    // 2-track CTA: DIY portal (AI checker prefilled) vs Didampingi tim (WhatsApp)
    $aiCheckerHref = route('landing.service-inquiry.create', ['q' => $service['title'] ?? '']);
@endphp

<section class="relative overflow-hidden pt-28 pb-16" style="background: var(--bg-raised); border-bottom: 1px solid var(--border-subtle);">
    <div class="container-wide">
        <div class="max-w-4xl">
            <a href="{{ route('services.index.id') }}" class="link-primary text-sm inline-flex items-center mb-5"><i class="fas fa-arrow-left mr-2"></i>Kembali ke semua layanan</a>
            <div class="flex items-start gap-4 mb-5">
                <div class="editorial-icon-badge" style="width:3.5rem;height:3.5rem;border-radius:.875rem;">
                    <i class="fas {{ $service['icon'] ?? 'fa-briefcase' }} icon-xl" aria-hidden="true"></i>
                </div>
                <div>
                    <span class="eyebrow">{{ $service['category'] ?? 'Layanan' }}</span>
                    <h1 class="display-lg mt-1 mb-0">{{ $service['title'] }}</h1>
                </div>
            </div>
            <p class="text-lg leading-relaxed mb-6 max-w-3xl" style="color: var(--text-secondary);">{{ $service['short_description'] }}</p>
            <div class="flex flex-wrap gap-2 mb-7">
                @if(!empty($service['process_time']))
                    <span class="text-xs px-2 py-1 rounded-full" style="background: rgba(15,23,42,.05); border: 1px solid var(--border-subtle); color: var(--text-secondary);"><i class="fas fa-clock mr-1"></i>{{ $service['process_time'] }}</span>
                @endif
                @if(!empty($service['price_range']))
                    <span class="text-xs px-2 py-1 rounded-full" style="background: var(--accent-glow); color: var(--accent-text);"><i class="fas fa-tag mr-1"></i>{{ $service['price_range'] }}</span>
                @endif
            </div>

            {{-- 2-Track CTA: DIY (emerald) vs With Team (gold) --}}
            <div class="grid sm:grid-cols-2 gap-3 max-w-2xl">
                <a href="{{ $aiCheckerHref }}" class="premium-card hover:no-underline" style="border-color: rgba(var(--tools-rgb),.25); background: var(--tools-glow);">
                    <div class="flex items-start gap-3">
                        <div class="editorial-icon-badge is-tools is-circle flex-shrink-0">
                            <i class="fas fa-robot text-sm" aria-hidden="true"></i>
                        </div>
                        <div>
                            <div class="text-[10px] font-bold uppercase tracking-[.14em] mb-1" style="color: var(--tools);">Pakai sendiri · Gratis</div>
                            <div class="font-semibold text-sm mb-1" style="color: var(--text-primary);">Cek dengan AI dulu</div>
                            <div class="text-xs" style="color: var(--text-secondary);">Lihat persyaratan & estimasi tanpa daftar</div>
                        </div>
                    </div>
                </a>
                <a href="{{ $waHref }}" target="_blank" rel="noopener noreferrer" class="premium-card hover:no-underline" style="border-color: rgba(var(--accent-rgb),.25); background: var(--accent-glow);">
                    <div class="flex items-start gap-3">
                        <div class="editorial-icon-badge is-circle flex-shrink-0">
                            <i class="fab fa-whatsapp text-sm" aria-hidden="true"></i>
                        </div>
                        <div>
                            <div class="text-[10px] font-bold uppercase tracking-[.14em] mb-1" style="color: var(--accent-text);">Didampingi tim</div>
                            <div class="font-semibold text-sm mb-1" style="color: var(--text-primary);">Konsultasi via WhatsApp</div>
                            <div class="text-xs" style="color: var(--text-secondary);">Tim ahli mendampingi end-to-end</div>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </div>
</section>

@if(!empty($service['sub_services']))
<section class="section">
    <div class="container-wide">
        <h2 class="display-md mb-3">Cakupan Sub-Layanan</h2>
        <p class="text-base mb-8 max-w-2xl" style="color: var(--text-secondary);">Rincian layanan yang termasuk pada paket ini.</p>
        <div class="grid md:grid-cols-2 xl:grid-cols-3 gap-6">
            @foreach($service['sub_services'] as $subSlug => $subService)
                <article class="premium-card">
                    <h3 class="text-base font-semibold mb-2" style="color: var(--text-primary);">{{ $subService['title'] }}</h3>
                    <p class="text-sm mb-4" style="color: var(--text-secondary);">{{ $subService['description'] ?? $subService['short_description'] ?? '' }}</p>
                    <div class="flex flex-wrap gap-2">
                        @if(!empty($subService['duration']))
                            <span class="text-xs px-2 py-1 rounded-full" style="background: rgba(15,23,42,.05); color: var(--text-secondary);">{{ $subService['duration'] }}</span>
                        @endif
                        @if(!empty($subService['price']))
                            <span class="text-xs px-2 py-1 rounded-full" style="background: var(--accent-glow); color: var(--accent-text);">{{ $subService['price'] }}</span>
                        @endif
                    </div>
                </article>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- Comparison: Tanpa vs Dengan Bizmark --}}
<section class="section-sm section-premium" style="border-top: 1px solid var(--border-subtle); border-bottom: 1px solid var(--border-subtle);">
    <div class="container-wide">
        <div class="text-center mb-8">
            <span class="eyebrow mb-3">Mengapa Bizmark.ID?</span>
            <h2 class="display-md mt-1 mb-2">Tanpa Bizmark vs Dengan Bizmark</h2>
            <p class="text-sm" style="color: var(--text-secondary);">Lihat perbedaan nyata dalam proses, waktu, dan risiko pengurusan izin.</p>
        </div>
        <div class="grid md:grid-cols-2 gap-5 max-w-3xl mx-auto">
            {{-- Tanpa Bizmark --}}
            <div class="premium-card border border-red-200/30 bg-red-50/30">
                <div class="flex items-center gap-2 mb-4">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center bg-red-100">
                        <i class="fas fa-times text-sm text-red-400"></i>
                    </div>
                    <h3 class="font-bold text-base text-red-400">Tanpa Bizmark</h3>
                </div>
                <ul class="space-y-2.5">
                    @php
                    $withoutItems = [
                        ['icon' => 'fa-clock', 'text' => 'Proses lebih lama — penelitian mandiri memakan waktu berminggu-minggu'],
                        ['icon' => 'fa-exclamation-triangle', 'text' => 'Risiko penolakan dokumen karena format atau persyaratan tidak sesuai'],
                        ['icon' => 'fa-search', 'text' => 'Tidak ada visibilitas status — tidak tahu kapan izin terbit'],
                        ['icon' => 'fa-money-bill-wave', 'text' => 'Biaya kesalahan bisa Rp 50–200 juta jika ada salah langkah'],
                    ];
                    @endphp
                    @foreach($withoutItems as $item)
                    <li class="flex items-start gap-2.5 text-sm text-gray-600">
                        <i class="fas {{ $item['icon'] }} mt-0.5 shrink-0 text-red-400"></i>
                        <span>{{ $item['text'] }}</span>
                    </li>
                    @endforeach
                </ul>
            </div>
            {{-- Dengan Bizmark --}}
            <div class="premium-card" style="border: 1px solid rgba(var(--accent-rgb),.25); background: var(--accent-glow);">
                <div class="flex items-center gap-2 mb-4">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center" style="background: rgba(var(--accent-rgb),.18);">
                        <i class="fas fa-check text-sm" style="color: var(--accent-text);"></i>
                    </div>
                    <h3 class="font-bold text-base" style="color: var(--accent-text);">Dengan Bizmark.ID</h3>
                </div>
                <ul class="space-y-2.5">
                    @php
                    $withItems = [
                        ['icon' => 'fa-bolt', 'text' => 'Proses dipercepat — tim berpengalaman tahu persis alur dan persyaratan'],
                        ['icon' => 'fa-shield-halved', 'text' => 'Dokumen disiapkan sesuai standar — tingkat keberhasilan 96%'],
                        ['icon' => 'fa-chart-line', 'text' => 'Laporan SLA mingguan — Anda tahu status izin setiap saat'],
                        ['icon' => 'fa-hand-holding-usd', 'text' => 'Pembayaran bertahap: 50% DP, 50% saat izin terbit'],
                    ];
                    @endphp
                    @foreach($withItems as $item)
                    <li class="flex items-start gap-2.5 text-sm" style="color: var(--text-secondary);">
                        <i class="fas {{ $item['icon'] }} mt-0.5 shrink-0" style="color: var(--accent-text);"></i>
                        <span>{{ $item['text'] }}</span>
                    </li>
                    @endforeach
                </ul>
                <div class="mt-4 pt-4" style="border-top: 1px solid rgba(var(--accent-rgb),.18);">
                    <a href="{{ $waHref }}" target="_blank" rel="noopener noreferrer" class="btn btn-primary w-full btn-sm">
                        <i class="fab fa-whatsapp"></i> Mulai Konsultasi Gratis
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

@if($partial && View::exists($partial))
<section class="section" style="background:var(--surface-warm);">
    <div class="container-wide">
        <div class="premium-card">
            @include($partial)
        </div>
    </div>
</section>
@endif

@if(!empty($service['faq']))
<section class="section">
    <div class="container-wide">
        <h2 class="display-md mb-3">Pertanyaan Umum</h2>
        <p class="text-base mb-8 max-w-2xl" style="color: var(--text-secondary);">Jawaban cepat untuk pertanyaan yang paling sering muncul.</p>
        <div class="max-w-3xl space-y-3">
            @foreach($service['faq'] as $faq)
                <details class="faq-item">
                    <summary class="faq-toggle">
                        <span>{{ $faq['q'] }}</span>
                        <i class="fas fa-chevron-down" aria-hidden="true"></i>
                    </summary>
                    <div class="faq-content">
                        <div class="faq-content-inner">{{ $faq['a'] }}</div>
                    </div>
                </details>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- Final 2-Track CTA --}}
<section class="section-sm section-premium">
    <div class="container-wide">
        <div class="text-center mb-8 max-w-2xl mx-auto">
            <h2 class="display-md mb-3">Mulai dari mana saja Anda nyaman.</h2>
            <p style="color: var(--text-secondary);">Pilih DIY gratis pakai AI, atau langsung didampingi tim ahli kami.</p>
        </div>
        <div class="grid sm:grid-cols-2 gap-4 max-w-3xl mx-auto">
            <a href="{{ $aiCheckerHref }}" class="premium-card text-center hover:no-underline" style="border-color: rgba(var(--tools-rgb),.25); background: var(--tools-glow);">
                <div class="editorial-icon-badge is-tools is-circle mx-auto mb-3" style="width:3rem;height:3rem;">
                    <i class="fas fa-robot icon-lg" aria-hidden="true"></i>
                </div>
                <div class="text-[10px] font-bold uppercase tracking-[.14em] mb-2" style="color: var(--tools);">Gratis · Tanpa daftar</div>
                <h3 class="font-bold text-base mb-2" style="color: var(--text-primary);">Coba dengan AI dulu</h3>
                <p class="text-sm" style="color: var(--text-secondary);">Cek persyaratan & estimasi biaya untuk {{ $service['title'] }}.</p>
            </a>
            <a href="{{ $waHref }}" target="_blank" rel="noopener noreferrer" class="premium-card text-center hover:no-underline" style="border-color: rgba(var(--accent-rgb),.25); background: var(--accent-glow);">
                <div class="editorial-icon-badge is-circle mx-auto mb-3" style="width:3rem;height:3rem;">
                    <i class="fab fa-whatsapp icon-lg" aria-hidden="true"></i>
                </div>
                <div class="text-[10px] font-bold uppercase tracking-[.14em] mb-2" style="color: var(--accent-text);">Didampingi tim</div>
                <h3 class="font-bold text-base mb-2" style="color: var(--text-primary);">Konsultasi langsung</h3>
                <p class="text-sm" style="color: var(--text-secondary);">Tim ahli kami mendampingi dari awal hingga izin terbit.</p>
            </a>
        </div>
    </div>
</section>
@endsection
