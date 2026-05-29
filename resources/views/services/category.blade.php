@extends('landing.layout')

@section('title', $title ?? ($categoryName . ' - Layanan Bizmark.ID'))
@section('meta_description', $meta_description ?? '')

@section('content')
@php
    $isEn = ($locale ?? app()->getLocale()) === 'en';
    $indexRoute = $isEn ? route('services.index.en') : route('services.index.id');
    $showRoute  = $isEn ? 'services.show.en' : 'services.show.id';
@endphp

{{-- Hero / Header --}}
<section class="relative overflow-hidden pt-28 pb-14 bg-[var(--bg-raised)] border-b border-gray-200">
    <div class="container-wide">
        {{-- Breadcrumb --}}
        <nav class="flex items-center gap-2 text-sm mb-6 text-gray-500" aria-label="Breadcrumb">
            <a href="{{ $isEn ? url('/en') : url('/') }}" class="hover:text-amber-700 transition-colors">
                {{ $isEn ? 'Home' : 'Beranda' }}
            </a>
            <i class="fas fa-chevron-right text-[10px] text-gray-400"></i>
            <a href="{{ $indexRoute }}" class="hover:text-amber-700 transition-colors">
                {{ $isEn ? 'Services' : 'Layanan' }}
            </a>
            <i class="fas fa-chevron-right text-[10px] text-gray-400"></i>
            <span class="font-semibold text-gray-800">{{ $categoryName }}</span>
        </nav>

        <div class="flex items-center gap-4 mb-4">
            <span class="eyebrow">{{ $isEn ? 'Category' : 'Kategori' }}</span>
        </div>
        <h1 class="display-md mb-3">
            {{ $isEn ? $categoryName . ' Services' : 'Layanan ' . ucwords(strtolower($categoryName)) }}
        </h1>
        <p class="text-lg leading-relaxed max-w-3xl mb-7" style="color: var(--text-secondary);">
            @if($isEn)
                Browse all {{ count($categoryServices) }} services in the {{ $categoryName }} category. Each service includes full scope, timeline, and pricing transparency.
            @else
                Daftar lengkap {{ count($categoryServices) }} layanan dalam kategori {{ ucwords(strtolower($categoryName)) }}. Setiap layanan mencakup ruang lingkup, durasi proses, dan estimasi biaya secara transparan.
            @endif
        </p>
        <div class="flex flex-wrap gap-3">
            <a href="{{ $waHref }}" target="_blank" rel="noopener noreferrer" class="btn btn-secondary">
                <i class="fab fa-whatsapp"></i>
                {{ $isEn ? 'Free Consultation' : 'Konsultasi Gratis' }}
            </a>
            <a href="{{ $indexRoute }}" class="btn btn-ghost">
                <i class="fas fa-arrow-left"></i>
                {{ $isEn ? 'All Services' : 'Semua Layanan' }}
            </a>
        </div>
    </div>
</section>

{{-- Service Grid --}}
<section class="section">
    <div class="container-wide">
        <div class="grid md:grid-cols-2 xl:grid-cols-3 gap-6">
            @foreach($categoryServices as $slug => $service)
                <article class="premium-card h-full flex flex-col" data-aos="fade-up" data-aos-delay="{{ ($loop->index % 3) * 100 }}">
                    <div class="flex items-start justify-between gap-4 mb-4">
                        <span class="editorial-icon-badge" style="width:3rem;height:3rem;border-radius:.75rem;flex-shrink:0;">
                            <i class="fas {{ $service['icon'] ?? 'fa-layer-group' }} icon-md" aria-hidden="true"></i>
                        </span>
                        @if(!empty($service['badge']))
                            <span class="badge-featured">{{ $service['badge'] }}</span>
                        @endif
                    </div>

                    <h2 class="text-lg font-bold mb-2 card-title line-clamp-2" style="color: var(--text-primary);">
                        {{ $service['title'] ?? '' }}
                    </h2>
                    <p class="text-sm mb-4 line-clamp-3" style="color: var(--text-secondary);">
                        {{ $service['short_description'] ?? '' }}
                    </p>

                    @if(!empty($service['process_time']) || !empty($service['price_range']))
                        <div class="flex flex-wrap gap-2 mb-4">
                            @if(!empty($service['process_time']))
                                <span class="text-xs px-2 py-1 rounded-full" style="background: rgba(15,23,42,.05); color: var(--text-secondary);">
                                    <i class="fas fa-clock mr-1"></i>{{ $service['process_time'] }}
                                </span>
                            @endif
                            @if(!empty($service['price_range']))
                                <span class="text-xs px-2 py-1 rounded-full font-semibold" style="background: var(--accent-glow); color: var(--accent-text);">
                                    <i class="fas fa-tag mr-1"></i>{{ $service['price_range'] }}
                                </span>
                            @endif
                        </div>
                    @endif

                    <div class="mt-auto pt-3 space-y-2" style="border-top: 1px solid var(--border-subtle);">
                        <a href="{{ route($showRoute, $slug) }}" class="link-primary text-sm inline-flex items-center w-full">
                            {{ $isEn ? 'Learn More' : 'Pelajari Lebih Lanjut' }}
                            <i class="fas fa-arrow-right ml-2"></i>
                        </a>
                        <a href="https://wa.me/6283879602855?text={{ rawurlencode(($isEn ? 'Hello, I am interested in ' : 'Halo, saya tertarik layanan ') . ($service['title'] ?? '') . ($isEn ? '. Can you explain the process and pricing?' : '. Bisa jelaskan proses dan biayanya?')) }}"
                           target="_blank" rel="noopener noreferrer"
                           class="btn btn-primary w-full btn-sm">
                            <i class="fab fa-whatsapp"></i>
                            {{ $isEn ? 'Ask Price & Process' : 'Tanya Biaya & Proses' }}
                        </a>
                    </div>
                </article>
            @endforeach
        </div>
    </div>
</section>

{{-- Bottom CTA --}}
<section class="section-sm section-premium">
    <div class="container-wide text-center">
        <h2 class="display-md mb-3" style="color: var(--text-primary);">
            {{ $isEn ? 'Not Sure Which Service Fits Your Business?' : 'Belum Yakin Layanan Mana yang Tepat?' }}
        </h2>
        <p class="mb-7" style="color: var(--text-secondary);">
            {{ $isEn
                ? 'Describe your business profile and we will suggest the right permit priority map.'
                : 'Ceritakan profil usaha Anda, kami bantu susun peta izin prioritas.' }}
        </p>
        <div class="flex flex-wrap justify-center gap-3">
            <a href="{{ $waHref }}" target="_blank" rel="noopener noreferrer" class="btn btn-primary">
                <i class="fab fa-whatsapp"></i> WhatsApp
            </a>
            <a href="{{ $isEn ? route('pma.inquiry.create') : route('landing.service-inquiry.create') }}"
               class="btn btn-ghost">
                <i class="fas fa-robot"></i>
                {{ $isEn ? 'AI Permit Checker' : 'Analisis AI Gratis' }}
            </a>
        </div>
    </div>
</section>
@endsection
