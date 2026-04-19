@extends('landing.layout')

@section('title', 'Perbandingan Layanan Perizinan | Bizmark.ID')
@section('meta_title', 'Perbandingan Layanan Perizinan Lingkungan & Industri 2026 | Bizmark.ID')
@section('meta_description', 'Panduan perbandingan lengkap berbagai jenis perizinan: AMDAL vs UKL-UPL, Limbah B3 vs AMDAL, dan lainnya. Pahami perbedaan sebelum mengurus izin.')
@section('meta_keywords', 'perbandingan perizinan, perbedaan amdal ukl-upl, perbedaan perizinan, panduan memilih izin')

@section('content')
@php
    $contact = (array) data_get(config('landing_metrics'), 'contact', []);
    $whatsapp = $contact['whatsapp_link'] ?? 'https://wa.me/6283879602855';
    $waText = 'Halo Bizmark, saya ingin konsultasi tentang perizinan yang tepat untuk usaha saya';
    $waHref = $whatsapp . (str_contains($whatsapp, '?') ? '&' : '?') . 'text=' . rawurlencode($waText);
@endphp

<section class="relative overflow-hidden pt-28 pb-16" style="background:linear-gradient(135deg,var(--surface-warm) 0%, var(--surface-cool) 100%);">
    <div class="container-wide">
        <span class="section-badge mb-4">Perbandingan</span>
        <h1 class="section-title mb-4">Perbandingan Layanan Perizinan</h1>
        <p class="section-description mb-8" style="margin-left:0;">Pahami perbedaan antara berbagai jenis perizinan lingkungan dan industri sebelum Anda memulai proses pengurusan.</p>
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('services.index.id') }}" class="btn btn-outline-primary"><i class="fas fa-layer-group"></i> Semua Layanan</a>
            <a href="{{ route('faq.index') }}" class="btn btn-outline-primary"><i class="fas fa-circle-question"></i> FAQ</a>
        </div>
    </div>
</section>

<section class="section">
    <div class="container-wide">
        <div class="grid sm:grid-cols-2 gap-6">
            @foreach($pairs as $slug => $pair)
                <a href="{{ url('/layanan/perbandingan/' . $slug) }}" class="card">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-10 h-10 rounded-lg flex items-center justify-center text-white text-sm font-bold" style="background-color: {{ $pair['a']['color'] ?? '#0f172a' }}">
                            <i class="fas {{ $pair['a']['icon'] ?? 'fa-file-alt' }}"></i>
                        </div>
                        <span class="text-sm" style="color:var(--text-tertiary);font-weight:800;letter-spacing:.08em;">VS</span>
                        <div class="w-10 h-10 rounded-lg flex items-center justify-center text-white text-sm font-bold" style="background-color: {{ $pair['b']['color'] ?? '#0f172a' }}">
                            <i class="fas {{ $pair['b']['icon'] ?? 'fa-file-alt' }}"></i>
                        </div>
                    </div>
                    <h2 class="text-lg font-bold mb-2 card-title" style="color:var(--text-primary);">{{ $pair['a']['title'] }} vs {{ $pair['b']['title'] }}</h2>
                    <p class="text-sm mb-0" style="color:var(--text-secondary);">Perbedaan, persyaratan, dan kapan Anda membutuhkan masing-masing layanan.</p>
                    <div class="mt-4">
                        <span class="link-primary text-sm">Baca Perbandingan <i class="fas fa-arrow-right ml-2"></i></span>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
</section>

<section class="section-sm" style="background:var(--surface-dark);">
    <div class="container-wide text-center">
        <h2 class="text-white mb-3" style="font-size:clamp(1.5rem,3vw,2.1rem);font-weight:750;">Masih Bingung Memilih?</h2>
        <p class="mb-7" style="color:rgba(255,255,255,.74);">Konsultasi gratis untuk menentukan perizinan yang tepat untuk usaha Anda.</p>
        <div class="flex flex-wrap justify-center gap-3">
            <a href="{{ $waHref }}" target="_blank" rel="noopener" class="btn btn-success"><i class="fab fa-whatsapp"></i> Konsultasi via WhatsApp</a>
            <a href="{{ route('contact.index') }}" class="btn btn-secondary"><i class="fas fa-envelope"></i> Hubungi Tim</a>
        </div>
    </div>
</section>

@endsection
