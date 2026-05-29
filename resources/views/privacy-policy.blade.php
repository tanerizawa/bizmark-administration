@extends('landing.layout')

@section('title', app()->getLocale() === 'en' ? 'Privacy Policy - Bizmark.ID' : 'Kebijakan Privasi - Bizmark.ID')
@section('meta_description', app()->getLocale() === 'en' ? 'Privacy policy information and where to find the latest version.' : 'Informasi kebijakan privasi dan lokasi versi terbaru.')

@section('content')
@php
    $isEnglish = app()->getLocale() === 'en';
    $targetUrl = $isEnglish ? route('privacy.policy.en') : route('privacy.policy.id');
@endphp

<section class="relative overflow-hidden pt-28 pb-16" style="background: var(--bg-raised); border-bottom: 1px solid var(--border-subtle);">
    <div class="container-wide">
        <span class="section-badge mb-4">Legal</span>
        <h1 class="section-title mb-4">{{ $isEnglish ? 'Privacy Policy' : 'Kebijakan Privasi' }}</h1>
        <p class="section-description" style="margin-left:0;">
            {{ $isEnglish ? 'This page has moved to our unified legal layout.' : 'Halaman ini sudah dipindahkan ke layout legal yang terintegrasi.' }}
        </p>
        <div class="flex flex-wrap gap-3">
            <a href="{{ $targetUrl }}" class="btn btn-primary"><i class="fas fa-arrow-right"></i> {{ $isEnglish ? 'Open Privacy Policy' : 'Buka Kebijakan Privasi' }}</a>
            <a href="{{ $isEnglish ? route('landing.en') : route('landing.id') }}" class="btn btn-outline-primary"><i class="fas fa-home"></i> {{ $isEnglish ? 'Back to Home' : 'Kembali ke Beranda' }}</a>
        </div>
    </div>
</section>
@endsection
