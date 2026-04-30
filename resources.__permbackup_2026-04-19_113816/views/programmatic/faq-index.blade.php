@extends('landing.layout')

@section('title', 'FAQ Perizinan Lingkungan & Industri | Bizmark.ID')
@section('meta_title', 'FAQ Perizinan Lingkungan & Industri - Pertanyaan Umum | Bizmark.ID')
@section('meta_description', 'Kumpulan pertanyaan dan jawaban seputar perizinan lingkungan, AMDAL, UKL-UPL, Limbah B3, dan perizinan industri lainnya.')
@section('meta_keywords', 'faq perizinan, pertanyaan perizinan lingkungan, tanya jawab amdal, faq limbah b3, pertanyaan ukl upl')

@section('content')
@php
    $contact = (array) data_get(config('landing_metrics'), 'contact', []);
    $whatsapp = $contact['whatsapp_link'] ?? 'https://wa.me/6283879602855';
    $waText = 'Halo Bizmark, saya memiliki pertanyaan tentang perizinan';
    $waHref = $whatsapp . (str_contains($whatsapp, '?') ? '&' : '?') . 'text=' . rawurlencode($waText);
@endphp

<section class="relative overflow-hidden pt-28 pb-16" style="background:linear-gradient(135deg,var(--surface-warm) 0%, var(--surface-cool) 100%);">
    <div class="container-wide text-center">
        <span class="section-badge mb-4">FAQ</span>
        <h1 class="section-title mb-4">Pusat Bantuan &amp; FAQ</h1>
        <p class="section-description mb-8">Temukan jawaban untuk pertanyaan umum seputar perizinan lingkungan dan industri di Indonesia.</p>
        <a href="{{ route('contact.index') }}" class="btn btn-outline-primary"><i class="fas fa-envelope"></i> Hubungi Tim</a>
    </div>
</section>

<section class="section">
    <div class="container-wide">
        <div class="grid sm:grid-cols-2 gap-6">
            @foreach($topics as $slug => $topic)
                <a href="{{ url('/faq/' . $topic['slug']) }}" class="card">
                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 rounded-xl flex items-center justify-center" style="background:rgba(14,165,233,.12);color:var(--color-accent);">
                            <i class="fas {{ $topic['icon'] ?? 'fa-circle-question' }}"></i>
                        </div>
                        <div class="min-w-0 flex-1">
                            <h2 class="text-lg font-bold mb-2 card-title" style="color:var(--text-primary);">{{ $topic['title'] }}</h2>
                            <p class="text-sm mb-3" style="color:var(--text-secondary);">{{ $topic['description'] }}</p>
                            <div class="flex items-center justify-between gap-4">
                                <span class="text-xs" style="color:var(--text-tertiary);">{{ $topic['faq_count'] }} artikel terkait</span>
                                <span class="link-primary text-sm">Lihat FAQ <i class="fas fa-arrow-right ml-2"></i></span>
                            </div>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
</section>

<section class="section-sm" style="background:var(--surface-dark);">
    <div class="container-wide text-center">
        <h2 class="text-white mb-3" style="font-size:clamp(1.5rem,3vw,2.1rem);font-weight:750;">Tidak Menemukan Jawaban?</h2>
        <p class="mb-7" style="color:rgba(255,255,255,.74);">Tanya langsung, tim kami siap membantu.</p>
        <div class="flex flex-wrap justify-center gap-3">
            <a href="{{ $waHref }}" target="_blank" rel="noopener" class="btn btn-success"><i class="fab fa-whatsapp"></i> Tanya via WhatsApp</a>
            <a href="{{ route('contact.index') }}" class="btn btn-secondary"><i class="fas fa-envelope"></i> Hubungi Tim</a>
        </div>
    </div>
</section>

@endsection
