@extends('landing.layout')

@section('title', $pageData['meta_title'])
@section('meta_title', $pageData['meta_title'])
@section('meta_description', $pageData['meta_description'])
@section('meta_keywords', $pageData['meta_keywords'])
@section('og_title', $pageData['meta_title'])
@section('og_description', $pageData['meta_description'])

@section('content')

{{-- Service Schema --}}
<script type="application/ld+json">{!! json_encode($pageData['schema_service'], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>

{{-- Breadcrumb Schema --}}
@php
$breadcrumbSchema = [
    '@context' => 'https://schema.org',
    '@type' => 'BreadcrumbList',
    'itemListElement' => [
        ['@type' => 'ListItem', 'position' => 1, 'name' => 'Beranda', 'item' => config('app.url')],
        ['@type' => 'ListItem', 'position' => 2, 'name' => 'Layanan', 'item' => config('app.url') . '/layanan'],
        ['@type' => 'ListItem', 'position' => 3, 'name' => $service['title'], 'item' => config('app.url') . '/layanan/' . $serviceSlug],
        ['@type' => 'ListItem', 'position' => 4, 'name' => $city['name'], 'item' => config('app.url') . '/layanan/' . $serviceSlug . '/' . $citySlug],
    ],
];
$svcColor = $service['color'] ?? '#0ea5e9';
@endphp
<script type="application/ld+json">{!! json_encode($breadcrumbSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>

{{-- FAQ Schema --}}
@if(count($pageData['faqs']) >= 2)
@php
$faqSchema = [
    '@context' => 'https://schema.org',
    '@type' => 'FAQPage',
    'mainEntity' => collect($pageData['faqs'])->map(fn($f) => [
        '@type' => 'Question',
        'name' => $f['question'],
        'acceptedAnswer' => ['@type' => 'Answer', 'text' => $f['answer']],
    ])->toArray(),
];
@endphp
<script type="application/ld+json">{!! json_encode($faqSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
@endif

{{-- Hero --}}
<section class="relative overflow-hidden bg-[var(--bg-raised)] border-b border-gray-200" style="min-height: clamp(380px, 50vh, 520px);">
    <div class="container-wide relative z-10 flex flex-col justify-end h-full" style="min-height: clamp(380px, 50vh, 520px);">
        <div class="pb-10 pt-32 lg:pt-40 lg:pb-14">
            <div class="flex flex-col lg:flex-row items-end lg:items-end gap-10">
                <div class="lg:w-2/3">
                    {{-- Breadcrumb --}}
                    <nav class="mb-6">
                        <ol class="flex items-center gap-2 text-sm text-gray-400">
                            <li><a href="/" class="hover:text-gray-600 transition"><i class="fas fa-home text-xs"></i></a></li>
                            <li><i class="fas fa-chevron-right text-[10px] text-gray-300"></i></li>
                            <li><a href="{{ route('services.index.id') }}" class="hover:text-gray-600 transition text-gray-500">Layanan</a></li>
                            <li><i class="fas fa-chevron-right text-[10px] text-gray-300"></i></li>
                            <li><a href="{{ route('services.show.id', $serviceSlug) }}" class="hover:text-gray-600 transition text-gray-500">{{ $service['title'] }}</a></li>
                            <li><i class="fas fa-chevron-right text-[10px] text-gray-300"></i></li>
                            <li class="font-medium text-gray-900">{{ $city['name'] }}</li>
                        </ol>
                    </nav>

                    {{-- Badges --}}
                    <div class="flex items-center gap-3 mb-6">
                        <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-gray-100 border border-gray-200">
                            <i class="fas fa-map-marker-alt text-xs text-amber-500"></i>
                            <span class="text-sm font-semibold text-gray-900">{{ $city['name'] }}, {{ $city['province'] }}</span>
                        </div>
                        <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full" style="background: {{ $svcColor }}12; border: 1px solid {{ $svcColor }}25;">
                            <i class="fas {{ $service['icon'] ?? 'fa-file-alt' }} text-xs" style="color: {{ $svcColor }};"></i>
                            <span class="text-sm font-semibold text-gray-900">{{ $service['category'] ?? 'PERIZINAN' }}</span>
                        </div>
                    </div>

                    {{-- Headline --}}
                    <h1 class="font-black leading-[1.1] mb-5 text-gray-900" style="font-size: clamp(2rem,4.5vw,3.5rem); letter-spacing: -0.03em;">
                        {{ $pageData['h1'] }}
                    </h1>

                    {{-- Description --}}
                    <p class="text-lg leading-relaxed max-w-2xl mb-8 font-light text-gray-600">
                        {{ $pageData['intro'] }}
                    </p>

                    {{-- CTA Buttons --}}
                    <div class="flex flex-wrap gap-4">
                        <a href="{{ $pageData['cta_whatsapp'] }}" target="_blank" rel="noopener"
                           class="btn btn-gold btn-lg" style="border-radius: var(--radius-full);">
                            <i class="fab fa-whatsapp text-lg"></i> Konsultasi Gratis
                        </a>
                        <a href="{{ route('services.show.id', $serviceSlug) }}"
                           class="btn btn-ghost btn-lg" style="border-radius: var(--radius-full);">
                            <i class="fas fa-info-circle"></i> Detail Layanan
                        </a>
                    </div>
                </div>

                {{-- Advantage Card --}}
                <div class="lg:w-1/3 hidden lg:block">
                    <div class="rounded-2xl p-6 bg-white border border-gray-200 shadow-sm">
                        <h3 class="text-lg font-bold mb-4 text-gray-900">Keunggulan Kami</h3>
                        <ul class="space-y-3 text-sm text-gray-600">
                            <li class="flex items-start gap-3"><i class="fas fa-check-circle mt-0.5 text-amber-500"></i> Tim berpengalaman di {{ $city['name'] }}</li>
                            <li class="flex items-start gap-3"><i class="fas fa-check-circle mt-0.5 text-amber-500"></i> Proses transparan & terpantau</li>
                            <li class="flex items-start gap-3"><i class="fas fa-check-circle mt-0.5 text-amber-500"></i> Koordinasi langsung ke {{ $pageData['government_office'] ?: 'dinas terkait' }}</li>
                            <li class="flex items-start gap-3"><i class="fas fa-check-circle mt-0.5 text-amber-500"></i> Garansi kelengkapan dokumen</li>
                            <li class="flex items-start gap-3"><i class="fas fa-check-circle mt-0.5 text-amber-500"></i> Konsultasi awal gratis</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Service Detail Content --}}
<section class="py-12 md:py-16" style="background: var(--surface);">
    <div class="container-wide">
        <div class="grid lg:grid-cols-3 gap-10">
            <div class="lg:col-span-2">
                <h2 class="text-2xl font-bold mb-4" style="color: var(--text-primary);">Tentang {{ $service['title'] }} di {{ $city['name'] }}</h2>
                <p class="leading-relaxed mb-8" style="color: var(--text-secondary);">{{ $service['long_description'] }}</p>

                @if($pageData['zones_text'])
                <h3 class="text-xl font-semibold mb-3" style="color: var(--text-primary);">Kawasan Industri yang Kami Layani</h3>
                <p class="mb-4" style="color: var(--text-secondary);">{{ $pageData['zones_text'] }}</p>
                <div class="grid sm:grid-cols-2 gap-3 mb-8">
                    @foreach($city['industrial_zones'] ?? [] as $zone)
                    <div class="flex items-center gap-3 text-sm rounded-xl px-4 py-3" style="background: var(--surface-cool); border: 1px solid var(--border-light);">
                        <div class="flex-shrink-0 w-8 h-8 rounded-lg flex items-center justify-center" style="background: {{ $svcColor }}12;">
                            <i class="fas fa-industry text-xs" style="color: {{ $svcColor }};"></i>
                        </div>
                        <span style="color: var(--text-primary); font-weight: 500;">{{ $zone }}</span>
                    </div>
                    @endforeach
                </div>
                @endif

                <h3 class="text-xl font-semibold mb-3" style="color: var(--text-primary);">Sektor Industri di {{ $city['name'] }}</h3>
                <p class="mb-4" style="color: var(--text-secondary);">{{ $pageData['industries_text'] }}</p>
                <div class="flex flex-wrap gap-2 mb-10">
                    @foreach($city['industries'] ?? [] as $industry)
                    <span class="px-3 py-1.5 text-sm rounded-full font-medium" style="background: {{ $svcColor }}10; color: {{ $svcColor }}; border: 1px solid {{ $svcColor }}25;">{{ ucfirst($industry) }}</span>
                    @endforeach
                </div>

                {{-- Process Steps --}}
                <h3 class="text-xl font-semibold mb-5" style="color: var(--text-primary);">Proses Pengurusan {{ $service['title'] }}</h3>
                <div class="space-y-4 mb-8">
                    @php
                    $steps = [
                        ['icon' => 'fa-headset', 'title' => 'Konsultasi Awal', 'desc' => 'Konsultasi gratis untuk memahami kebutuhan perizinan Anda di ' . $city['name']],
                        ['icon' => 'fa-file-signature', 'title' => 'Persiapan Dokumen', 'desc' => 'Tim kami menyiapkan dan menyusun seluruh dokumen yang diperlukan'],
                        ['icon' => 'fa-paper-plane', 'title' => 'Pengajuan', 'desc' => 'Pengajuan resmi ke ' . ($pageData['government_office'] ?: 'instansi terkait')],
                        ['icon' => 'fa-search', 'title' => 'Monitoring', 'desc' => 'Pemantauan progres dan koordinasi intensif untuk mempercepat proses'],
                        ['icon' => 'fa-check-double', 'title' => 'Penerbitan Izin', 'desc' => 'Izin diterbitkan dan siap untuk operasional perusahaan Anda'],
                    ];
                    @endphp
                    @foreach($steps as $i => $step)
                    <div class="flex items-start gap-4 p-5 rounded-xl transition" style="background: var(--surface-cool); border: 1px solid var(--border-light);">
                        <div class="flex-shrink-0 w-10 h-10 rounded-full flex items-center justify-center text-sm font-bold text-white" style="background: {{ $svcColor }};">{{ $i + 1 }}</div>
                        <div>
                            <h4 class="font-semibold mb-1" style="color: var(--text-primary);">{{ $step['title'] }}</h4>
                            <p class="text-sm" style="color: var(--text-secondary);">{{ $step['desc'] }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Sidebar --}}
            <div class="lg:col-span-1">
                {{-- CTA Box --}}
                <div class="rounded-2xl p-6 mb-6 sticky top-24" style="background: var(--surface-cool); border: 1px solid var(--border-light);">
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center mb-4" style="background: {{ $svcColor }}12; color: {{ $svcColor }};">
                        <i class="fas {{ $service['icon'] ?? 'fa-file-alt' }} text-xl"></i>
                    </div>
                    <h3 class="text-lg font-bold mb-2 text-gray-900">Butuh {{ $service['title'] }}?</h3>
                    <p class="text-sm mb-5 text-gray-600">Hubungi kami sekarang untuk konsultasi gratis tentang {{ $service['title'] }} di {{ $city['name'] }}.</p>
                    <a href="{{ $pageData['cta_whatsapp'] }}" target="_blank" rel="noopener"
                       class="btn btn-gold mb-3" style="border-radius: var(--radius-lg); width: 100%;">
                        <i class="fab fa-whatsapp"></i> WhatsApp Kami
                    </a>
                    <a href="{{ route('contact.index') }}" class="btn btn-outline-primary" style="border-radius: var(--radius-lg); width: 100%;">
                        <i class="fas fa-envelope"></i> Formulir Kontak
                    </a>

                    {{-- Related Services --}}
                    <div class="mt-6 pt-6" style="border-top: 1px solid var(--border-light);">
                        <h4 class="text-sm font-bold uppercase tracking-wider mb-4" style="color: var(--text-tertiary);">Layanan Lain di {{ $city['name'] }}</h4>
                        <ul class="space-y-3">
                            @foreach($relatedServices as $slug => $rs)
                            <li>
                                <a href="{{ url('/layanan/' . $slug . '/' . $citySlug) }}"
                                   class="link-primary text-sm flex items-center gap-3">
                                    <i class="fas {{ $rs['icon'] ?? 'fa-file-alt' }}" style="color: {{ $rs['color'] ?? '#0ea5e9' }};"></i>
                                    <span>{{ $rs['title'] }}</span>
                                </a>
                            </li>
                            @endforeach
                        </ul>
                    </div>

                    {{-- Other Cities --}}
                    <div class="mt-6 pt-6" style="border-top: 1px solid var(--border-light);">
                        <h4 class="text-sm font-bold uppercase tracking-wider mb-4" style="color: var(--text-tertiary);">{{ $service['title'] }} di Kota Lain</h4>
                        <div class="flex flex-wrap gap-2">
                            @foreach($otherCities as $cSlug => $c)
                            <a href="{{ url('/layanan/' . $serviceSlug . '/' . $cSlug) }}"
                               class="chip">
                                {{ $c['name'] }}
                            </a>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- FAQ Section --}}
@if(count($pageData['faqs']) > 0)
<section class="py-12 md:py-16" style="background: var(--surface-cool);">
    <div class="container-wide max-w-4xl mx-auto">
        <div class="text-center mb-10">
            <div class="eyebrow mx-auto">
                <i class="fas fa-question-circle text-xs" style="color: var(--color-accent);"></i>
                <span>FAQ</span>
            </div>
            <h2 class="display-lg" style="color: var(--text-primary);">FAQ: {{ $service['title'] }} di {{ $city['name'] }}</h2>
        </div>
        <div class="space-y-3">
            @foreach($pageData['faqs'] as $i => $faq)
                <details class="faq-item" {{ $i === 0 ? 'open' : '' }}>
                    <summary class="faq-toggle">
                        <span>{{ $faq['question'] }}</span>
                        <i class="fas fa-chevron-down" aria-hidden="true"></i>
                    </summary>
                    <div class="faq-content">
                        <div class="faq-content-inner">{{ $faq['answer'] }}</div>
                    </div>
                </details>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- Related Articles --}}
@if($relatedArticles->count() > 0)
<section class="py-12 md:py-16" style="background: var(--surface);">
    <div class="container-wide">
        <div class="text-center mb-10">
            <div class="eyebrow mx-auto">
                <i class="fas fa-newspaper text-xs" style="color: var(--color-secondary);"></i>
                <span>Artikel</span>
            </div>
            <h2 class="display-lg" style="color: var(--text-primary);">Artikel Terkait {{ $service['title'] }}</h2>
        </div>
        <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-5 max-w-5xl mx-auto">
            @foreach($relatedArticles as $article)
            <a href="{{ route('blog.article.id', $article->slug) }}" class="group card block overflow-hidden" style="padding: 0;">
                @if($article->featured_image)
                <div class="aspect-video overflow-hidden">
                    <img src="{{ asset('storage/' . $article->featured_image) }}" alt="{{ $article->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" loading="lazy">
                </div>
                @endif
                <div class="p-4">
                    <h3 class="font-semibold text-sm leading-snug line-clamp-2 card-title" style="color: var(--text-primary);">{{ $article->title }}</h3>
                    <p class="text-xs mt-2" style="color: var(--text-tertiary);">{{ $article->reading_time }} menit baca</p>
                </div>
            </a>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- CTA Bottom --}}
<section class="section-premium text-center">
    <div class="container-wide max-w-3xl mx-auto">
        <h2 class="font-black mb-4 text-gray-900" style="font-size: clamp(1.5rem,3vw,2.25rem); letter-spacing: -0.02em;">Siap Mengurus {{ $service['title'] }} di {{ $city['name'] }}?</h2>
        <p class="text-lg mb-8 font-light text-gray-600">Konsultasi gratis dengan tim ahli kami. Proses cepat, transparan, dan terpercaya.</p>
        <a href="{{ $pageData['cta_whatsapp'] }}" target="_blank" rel="noopener"
           class="btn btn-gold btn-lg" style="border-radius: var(--radius-full);">
            <i class="fab fa-whatsapp text-xl"></i> Hubungi Kami Sekarang
        </a>
    </div>
</section>

@endsection
