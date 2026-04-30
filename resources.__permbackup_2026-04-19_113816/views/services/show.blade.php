@extends('landing.layout')

@section('title', $title ?? ($service['title'] . ' - Bizmark.ID'))
@section('meta_description', $meta_description ?? $service['short_description'])
@section('meta_keywords', $service['meta_keywords'] ?? '')

@section('content')
@php
    $contact = (array) data_get(config('landing_metrics'), 'contact', []);
    $whatsapp = $contact['whatsapp_link'] ?? 'https://wa.me/6283879602855';
    $waText = 'Halo, saya ingin konsultasi layanan ' . ($service['title'] ?? '');
    $waHref = $whatsapp . (str_contains($whatsapp, '?') ? '&' : '?') . 'text=' . rawurlencode($waText);
    $serviceSlug = request()->route('slug') ?? ($service['slug'] ?? '');
    $partial = $serviceSlug ? 'services.partials.' . $serviceSlug : null;
@endphp

<section class="relative overflow-hidden pt-28 pb-16" style="background:linear-gradient(135deg,var(--surface-warm) 0%, var(--surface-cool) 100%);">
    <div class="container-wide">
        <div class="max-w-4xl">
            <a href="{{ route('services.index.id') }}" class="link-primary text-sm inline-flex items-center mb-5"><i class="fas fa-arrow-left mr-2"></i>Kembali ke semua layanan</a>
            <div class="flex items-start gap-4 mb-5">
                <div class="w-14 h-14 rounded-xl flex items-center justify-center" style="background:{{ $service['color'] ?? '#0f172a' }}20;">
                    <i class="fas {{ $service['icon'] ?? 'fa-briefcase' }}" style="color:{{ $service['color'] ?? '#0f172a' }};"></i>
                </div>
                <div>
                    <span class="section-badge">{{ $service['category'] ?? 'Layanan' }}</span>
                    <h1 class="section-title mb-0">{{ $service['title'] }}</h1>
                </div>
            </div>
            <p class="section-description mb-6" style="margin-left:0;">{{ $service['short_description'] }}</p>
            <div class="flex flex-wrap gap-2 mb-7">
                @if(!empty($service['process_time']))
                    <span class="text-xs px-2 py-1 rounded-full" style="background:var(--surface);border:1px solid var(--border-light);color:var(--text-secondary);"><i class="fas fa-clock mr-1"></i>{{ $service['process_time'] }}</span>
                @endif
                @if(!empty($service['price_range']))
                    <span class="text-xs px-2 py-1 rounded-full" style="background:rgba(22,163,74,.12);color:var(--color-success);"><i class="fas fa-tag mr-1"></i>{{ $service['price_range'] }}</span>
                @endif
            </div>
            <div class="flex flex-wrap gap-3">
                <a href="{{ $waHref }}" class="btn btn-success"><i class="fab fa-whatsapp"></i> Konsultasi via WhatsApp</a>
                <a href="{{ route('contact.index') }}" class="btn btn-primary"><i class="fas fa-envelope"></i> Hubungi Tim</a>
            </div>
        </div>
    </div>
</section>

@if(!empty($service['sub_services']))
<section class="section">
    <div class="container-wide">
        <h2 class="section-title mb-3">Cakupan Sub-Layanan</h2>
        <p class="section-description mb-8" style="margin-left:0;">Rincian layanan yang termasuk pada paket ini.</p>
        <div class="grid md:grid-cols-2 xl:grid-cols-3 gap-6">
            @foreach($service['sub_services'] as $subSlug => $subService)
                <article class="card">
                    <h3 class="text-base font-semibold mb-2" style="color:var(--text-primary);">{{ $subService['title'] }}</h3>
                    <p class="text-sm mb-4" style="color:var(--text-secondary);">{{ $subService['description'] ?? $subService['short_description'] ?? '' }}</p>
                    <div class="flex flex-wrap gap-2">
                        @if(!empty($subService['duration']))
                            <span class="text-xs px-2 py-1 rounded-full" style="background:var(--surface-cool);color:var(--text-secondary);">{{ $subService['duration'] }}</span>
                        @endif
                        @if(!empty($subService['price']))
                            <span class="text-xs px-2 py-1 rounded-full" style="background:rgba(22,163,74,.12);color:var(--color-success);">{{ $subService['price'] }}</span>
                        @endif
                    </div>
                </article>
            @endforeach
        </div>
    </div>
</section>
@endif

@if($partial && View::exists($partial))
<section class="section" style="background:var(--surface-warm);">
    <div class="container-wide">
        <div class="card">
            @include($partial)
        </div>
    </div>
</section>
@endif

@if(!empty($service['faq']))
<section class="section">
    <div class="container-wide">
        <h2 class="section-title mb-3">Pertanyaan Umum</h2>
        <p class="section-description mb-8" style="margin-left:0;">Jawaban cepat untuk pertanyaan yang paling sering muncul.</p>
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

<section class="section-sm" style="background:var(--surface-dark);">
    <div class="container-wide text-center">
        <h2 class="text-white mb-3" style="font-size:clamp(1.5rem,3vw,2.1rem);font-weight:750;">Lanjutkan ke Tahap Eksekusi</h2>
        <p class="mb-7" style="color:rgba(255,255,255,.74);">Tim kami siap membantu dari asesmen sampai izin terbit.</p>
        <div class="flex flex-wrap justify-center gap-3">
            <a href="{{ route('contact.index') }}" class="btn btn-secondary"><i class="fas fa-paper-plane"></i> Kirim Brief</a>
            <a href="{{ route('services.index.id') }}" class="btn btn-ghost"><i class="fas fa-layer-group"></i> Layanan Lainnya</a>
        </div>
    </div>
</section>
@endsection
