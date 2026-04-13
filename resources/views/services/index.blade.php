@extends('landing.layout')

@section('title', $title ?? 'Layanan Kami - Bizmark.ID')
@section('meta_description', $meta_description ?? 'Layanan lengkap perizinan industri dan konsultasi lingkungan')

@section('structured_data')
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@type": "OfferCatalog",
    "name": "Layanan Perizinan Bizmark.ID",
    "description": "Layanan lengkap perizinan industri dan konsultasi lingkungan",
    "numberOfItems": {{ count($services) }},
    "provider": {
        "@@type": "Organization",
        "name": "Bizmark.ID",
        "url": "{{ url('/') }}",
        "telephone": "+62 838 7960 2855",
        "address": {"@@type": "PostalAddress", "addressLocality": "Karawang", "addressCountry": "ID"}
    },
    "itemListElement": [
        @foreach($services as $slug => $svc)
        {
            "@type": "OfferCatalog",
            "name": "{{ $svc['title'] }}",
            "description": "{{ $svc['short_description'] }}",
            "url": "{{ route($locale === 'en' ? 'services.show.en' : 'services.show.id', $slug) }}",
            "itemOffered": {
                "@type": "Service",
                "name": "{{ $svc['title'] }}",
                "description": "{{ $svc['short_description'] }}",
                "provider": {"@type": "Organization", "name": "Bizmark.ID"}
                @if(!empty($svc['price_range'])),
                "offers": {"@type": "Offer", "price": "{{ $svc['price_range'] }}", "priceCurrency": "IDR"}
                @endif
            }
        }@if(!$loop->last),@endif
        @endforeach
    ]
}
</script>
@if(!empty($serviceFaqs))
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@type": "FAQPage",
    "mainEntity": [
        @foreach($serviceFaqs as $fIndex => $faq)
        {
            "@type": "Question",
            "name": "{{ $faq['q'] }}",
            "acceptedAnswer": {"@type": "Answer", "text": "{{ $faq['a'] }}"}
        }@if(!$loop->last),@endif
        @endforeach
    ]
}
</script>
@endif
@endsection

@section('content')

@php
    $contact = (array) data_get(config('landing_metrics'), 'contact', []);
    $whatsappLink = $contact['whatsapp_link'] ?? '';
    $phoneRaw = $contact['phone'] ?? '';
    $phoneHref = $phoneRaw !== '' ? ('tel:' . preg_replace('/\s+/', '', $phoneRaw)) : '';
    $totalSubServices = collect($services)->sum(fn($s) => count($s['sub_services'] ?? []));

    $categoryMap = [
        'LINGKUNGAN' => ['label' => 'Lingkungan', 'icon' => 'fa-leaf', 'color' => '#059669', 'desc' => 'Perizinan & konsultasi pengelolaan dampak lingkungan hidup', 'summary' => 'Membantu perusahaan memenuhi regulasi lingkungan hidup — dari AMDAL, UKL-UPL, pengelolaan limbah B3, hingga konsultasi berkelanjutan untuk operasional yang ramah lingkungan.'],
        'PERIZINAN USAHA' => ['label' => 'Perizinan Usaha', 'icon' => 'fa-file-signature', 'color' => '#F59E0B', 'desc' => 'Pengurusan legalitas dan izin dasar usaha', 'summary' => 'Pengurusan NIB melalui sistem OSS, perizinan berusaha berbasis risiko, dan legalitas dasar usaha agar bisnis Anda beroperasi secara legal dan tercatat resmi di sistem pemerintah.'],
        'BANGUNAN' => ['label' => 'Bangunan', 'icon' => 'fa-building', 'color' => '#3B82F6', 'desc' => 'Perizinan bangunan gedung dan kelayakan fungsi', 'summary' => 'Pengurusan Persetujuan Bangunan Gedung (PBG) dan Sertifikat Laik Fungsi (SLF) — memastikan bangunan memenuhi standar keamanan, konstruksi, dan kelayakan fungsi sesuai regulasi.'],
        'INDUSTRI' => ['label' => 'Industri', 'icon' => 'fa-industry', 'color' => '#8B5CF6', 'desc' => 'Izin operasional untuk kegiatan industri manufaktur', 'summary' => 'Perizinan operasional industri mencakup izin produksi, penggunaan peralatan, dan kepatuhan standar keamanan pabrik untuk fasilitas industri yang sesuai regulasi.'],
        'TEKNOLOGI' => ['label' => 'Teknologi', 'icon' => 'fa-microchip', 'color' => '#6366F1', 'desc' => 'Sistem monitoring digital & pemantauan real-time', 'summary' => 'Solusi monitoring digital terintegrasi untuk pemantauan lingkungan, emisi, dan kepatuhan regulasi secara real-time dengan data akurat dan laporan otomatis.'],
        'K3' => ['label' => 'K3', 'icon' => 'fa-hard-hat', 'color' => '#EF4444', 'desc' => 'Keselamatan dan kesehatan kerja', 'summary' => 'Pengurusan izin K3 meliputi sertifikasi peralatan, audit keselamatan kerja, dan implementasi Sistem Manajemen K3 untuk tempat kerja yang aman dan sesuai standar.'],
    ];
    $grouped = collect($services)->groupBy(fn($s) => $s['category'] ?? 'LAINNYA', preserveKeys: true);

    // Map per-service colors to neuroscience palette
    $colorMap = [
        '#0077B5' => '#5B8DBE',
        '#F97316' => '#E8956F',
        '#10B981' => '#7CB342',
    ];

    // Client list for social proof
    $clients = config('landing.clients', []);

    // Testimonials mapped to categories
    $testimonials = config('landing.testimonials', []);
    $categoryTestimonial = [
        'LINGKUNGAN' => $testimonials[2] ?? null,  // HSE Manager - LB3
        'PERIZINAN USAHA' => null,
        'BANGUNAN' => $testimonials[0] ?? null,     // Project Manager - PBG
        'INDUSTRI' => $testimonials[1] ?? null,     // Operational Manager
        'TEKNOLOGI' => $testimonials[4] ?? null,    // HRD Manager - Portal
        'K3' => null,
    ];

    // FAQ for this page
    $serviceFaqs = [
        ['q' => 'Apa perbedaan AMDAL dan UKL-UPL?', 'a' => 'AMDAL wajib untuk kegiatan berdampak besar & penting (industri besar, pertambangan, infrastruktur besar). UKL-UPL untuk kegiatan berdampak tidak signifikan (usaha menengah). AMDAL lebih kompleks, melibatkan komisi penilai dan pelibatan masyarakat, dengan biaya mulai Rp 150 juta dan proses 6-12 bulan. UKL-UPL lebih sederhana dengan biaya mulai Rp 15 juta dan proses 30-60 hari.'],
        ['q' => 'Berapa biaya pengurusan izin lingkungan?', 'a' => 'Biaya bervariasi: SPPL gratis-Rp 5 juta, UKL-UPL Rp 15-75 juta, AMDAL Rp 150 juta-1 miliar+, izin limbah B3 Rp 25 juta ke atas. Untuk estimasi tepat sesuai kebutuhan spesifik, hubungi kami untuk konsultasi gratis.'],
        ['q' => 'Dokumen apa saja yang perlu disiapkan?', 'a' => 'Dokumen umum: KTP/NPWP Direktur, Akta Pendirian, SK Kemenkumham, NPWP Perusahaan, dan NIB. Dokumen tambahan tergantung jenis izin: denah lokasi untuk PBG, neraca limbah B3, atau data kapasitas produksi. Tim kami membantu menyiapkan seluruh kebutuhan.'],
        ['q' => 'Berapa lama proses pengurusan perizinan?', 'a' => 'OSS/NIB: 1-3 hari. UKL-UPL: 30-60 hari. PBG/SLF: 30-60 hari. AMDAL: 6-12 bulan. Izin limbah B3: 30-240 hari tergantung jenis izin. Kami memberikan estimasi akurat setelah konsultasi awal.'],
        ['q' => 'Bagaimana skema pembayaran?', 'a' => 'Pembayaran bertahap: 50% saat kick-off sebagai DP dan 50% ketika izin terbit. Untuk proyek besar, skema dapat disesuaikan. Kami memberikan estimasi biaya detail dan transparan di awal.'],
        ['q' => 'Apakah ada jaminan jika perizinan tidak berhasil?', 'a' => 'Jika kegagalan berasal dari sisi kami, biaya dikembalikan sebagian sesuai kesepakatan awal. Tim bersertifikat dan pengalaman lebih dari 10 tahun memastikan proses berjalan optimal.'],
        ['q' => 'Apakah melayani klien di luar Jawa Barat?', 'a' => 'Ya. Kantor pusat di Karawang, namun kami memiliki jaringan konsultan di berbagai provinsi dan dapat mengurus izin di seluruh Indonesia. Konsultasi awal dapat dilakukan secara daring.'],
        ['q' => 'Apa itu OSS-RBA dan tingkat risiko?', 'a' => 'OSS-RBA (Online Single Submission berbasis Risiko) mengklasifikasikan usaha menjadi 4 tingkat: Rendah (cukup NIB), Menengah Rendah (NIB + Sertifikat Standar), Menengah Tinggi (NIB + Sertifikat Standar terverifikasi), dan Tinggi (NIB + Izin lengkap). Kami membantu menentukan dan mengurus sesuai tingkat risiko Anda.'],
    ];
@endphp

<!-- =============================================
     HERO — Services Directory 
     ============================================= -->
<section class="relative overflow-hidden" style="min-height: clamp(380px, 50vh, 520px);">
    <div class="absolute inset-0">
        <img src="/images/landing/hero-modern-1400.webp" alt="Layanan perizinan industri" class="w-full h-full object-cover" loading="eager">
        <div class="absolute inset-0" style="background: linear-gradient(to top, #0f172a, rgba(15,23,42,.7), rgba(15,23,42,.4));"></div>
    </div>
    <div class="container-wide relative z-10 flex flex-col justify-end h-full" style="min-height: clamp(380px, 50vh, 520px);">
        <div class="pb-10 pt-32 lg:pt-40 lg:pb-14 max-w-3xl" data-aos="fade-up" data-aos-duration="800">
            {{-- Breadcrumb --}}
            <nav class="mb-6">
                <ol class="flex items-center gap-2 text-sm" style="color: rgba(255,255,255,.5);">
                    <li><a href="{{ route('landing.id') }}" class="hover:opacity-80 transition"><i class="fas fa-home text-xs"></i></a></li>
                    <li><i class="fas fa-chevron-right text-[10px]" style="color: rgba(255,255,255,.3);"></i></li>
                    <li class="font-medium" style="color: rgba(255,255,255,.9);">Layanan</li>
                </ol>
            </nav>

            {{-- Badge --}}
            <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full backdrop-blur-sm mb-6" style="background: rgba(255,255,255,.1); border: 1px solid rgba(255,255,255,.15);">
                <span class="flex h-2 w-2 rounded-full animate-pulse" style="background: #E8956F;"></span>
                <span class="text-sm font-semibold" style="color: rgba(255,255,255,.9);">Layanan Profesional</span>
            </div>

            {{-- Headline --}}
            <h1 class="font-black leading-[1.1] mb-5" style="font-size: clamp(2rem,4.5vw,3.5rem); letter-spacing: -0.03em; color: #fff;">
                Layanan Perizinan &<br>Konsultasi Lingkungan
            </h1>

            {{-- Description --}}
            <p class="text-lg leading-relaxed max-w-2xl mb-8 font-light" style="color: rgba(255,255,255,.6);">
                Solusi lengkap perizinan industri dari konsultasi awal hingga izin terbit — satu mitra terpercaya.
            </p>

            {{-- CTA Buttons --}}
            <div class="flex flex-wrap gap-4 mb-10">
                @php
                    $waHeroText = 'Halo, saya tertarik dengan layanan perizinan Bizmark.ID';
                    $waHeroHref = $whatsappLink . (str_contains($whatsappLink, '?') ? '&' : '?') . 'text=' . rawurlencode($waHeroText);
                @endphp
                <a href="{{ $waHeroHref }}" target="_blank" rel="noopener" 
                   class="inline-flex items-center gap-3 px-8 py-4 rounded-full font-semibold transition-all duration-300 hover:translate-y-[-2px]" style="background: linear-gradient(90deg, #5B8DBE, #3A5D82); color: #fff;">
                    <i class="fab fa-whatsapp text-lg"></i>
                    <span>Konsultasi Gratis</span>
                </a>
                <a href="#layanan" 
                   class="inline-flex items-center gap-3 px-8 py-4 rounded-full font-semibold backdrop-blur-sm transition-all duration-300" style="background: rgba(255,255,255,.1); color: #fff; border: 1px solid rgba(255,255,255,.2);">
                    <span>Lihat Layanan</span>
                    <i class="fas fa-chevron-down text-sm"></i>
                </a>
            </div>

            {{-- Stats (animated counters) --}}
            <div class="flex items-center gap-8 text-sm" style="color: rgba(255,255,255,.7);">
                <div><span class="text-2xl font-black counter" data-target="10" data-suffix="+" style="color: #fff;">0+</span><br><span class="text-xs uppercase tracking-wider" style="color: rgba(255,255,255,.5);">Tahun</span></div>
                <div class="w-px h-8" style="background: rgba(255,255,255,.15);"></div>
                <div><span class="text-2xl font-black counter" data-target="500" data-suffix="+" style="color: #fff;">0+</span><br><span class="text-xs uppercase tracking-wider" style="color: rgba(255,255,255,.5);">Proyek</span></div>
                <div class="w-px h-8" style="background: rgba(255,255,255,.15);"></div>
                <div><span class="text-2xl font-black counter" data-target="98" data-suffix="%" style="color: #7CB342;">0%</span><br><span class="text-xs uppercase tracking-wider" style="color: rgba(255,255,255,.5);">Sukses</span></div>
                <div class="w-px h-8" style="background: rgba(255,255,255,.15);"></div>
                <div><span class="text-2xl font-black counter" data-target="{{ count($services) }}" data-suffix="" style="color: #E8956F;">0</span><br><span class="text-xs uppercase tracking-wider" style="color: rgba(255,255,255,.5);">Layanan</span></div>
            </div>
        </div>
    </div>
</section>

<!-- =============================================
     SOCIAL PROOF STRIP
     ============================================= -->
<div class="py-5 overflow-hidden" style="background: #fff; border-bottom: 1px solid rgba(91,141,190,.08);">
    <div class="container-wide">
        <div class="flex items-center gap-6 lg:gap-8">
            <span class="shrink-0 text-xs font-semibold uppercase tracking-wider" style="color: #9B8B7E;">Dipercaya oleh</span>
            <div class="flex items-center gap-4 overflow-x-auto scrollbar-hide">
                @foreach($clients as $client)
                <div class="client-logo shrink-0 px-4 py-2 rounded-lg text-xs font-semibold whitespace-nowrap" style="background: rgba(91,141,190,.05); color: #6B5D52; border: 1px solid rgba(91,141,190,.08);">
                    {{ $client }}
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<!-- =============================================
     STICKY CATEGORY NAV
     ============================================= -->
<nav class="sticky top-[64px] z-30 backdrop-blur-md" style="background: rgba(255,255,255,.95); border-bottom: 1px solid rgba(91,141,190,.1); box-shadow: 0 2px 8px rgba(0,0,0,.04);" id="layanan">
    <div class="container-wide">
        <div class="flex items-center gap-1.5 overflow-x-auto py-3 scrollbar-hide -mx-1 px-1">
            <button onclick="filterCategory('all')" class="cat-tab cat-tab-active shrink-0 px-4 py-2 rounded-full text-sm font-semibold transition-all duration-300" data-cat="all">
                Semua <span class="ml-1 text-xs opacity-60">({{ count($services) }})</span>
            </button>
            @foreach($categoryMap as $catKey => $cat)
                @if($grouped->has($catKey))
                <button onclick="filterCategory('{{ Str::slug($catKey) }}')" class="cat-tab shrink-0 px-4 py-2 rounded-full text-sm font-semibold transition-all duration-300" data-cat="{{ Str::slug($catKey) }}">
                    <i class="fas {{ $cat['icon'] }} mr-1.5 text-xs" style="color: {{ $cat['color'] }};"></i>{{ $cat['label'] }}
                    <span class="ml-1 text-xs opacity-60">({{ $grouped[$catKey]->count() }})</span>
                </button>
                @endif
            @endforeach
        </div>
    </div>
</nav>

<!-- =============================================
     ALL SERVICES — By Category (Magazine Layout)
     ============================================= -->
@php $catIndex = 0; @endphp
@foreach($categoryMap as $catKey => $catMeta)
    @if($grouped->has($catKey))
    @php
        $catIndex++;
        $catServices = $grouped[$catKey];
        $isSingle = $catServices->count() === 1;
        $isDark = $catIndex % 2 === 0;
        $lightBg = match($catKey) {
            'LINGKUNGAN' => 'background: linear-gradient(135deg, #F0F4ED, rgba(232,245,233,.4))',
            'BANGUNAN' => 'background: linear-gradient(135deg, rgba(239,246,255,.8), rgba(219,234,254,.3))',
            'TEKNOLOGI' => 'background: linear-gradient(135deg, rgba(238,242,255,.7), rgba(224,231,255,.3))',
            default => 'background: #FDFBF8',
        };
    @endphp
    <section class="service-category-section py-16 lg:py-20 {{ $isDark ? 'dove' : '' }}" @if(!$isDark) style="{{ $lightBg }}" @endif data-category-section="{{ Str::slug($catKey) }}">
        <div class="container-wide">
            {{-- Category Section Header --}}
            <div class="max-w-3xl mx-auto text-center mb-12 space-y-4" data-aos="fade-up" data-aos-duration="800">
                <div class="sec-badge inline-flex items-center gap-2 px-4 py-2 rounded-full" style="background: rgba(255,255,255,.8); border: 1px solid {{ $catMeta['color'] }}30; box-shadow: 0 2px 15px rgba(0,0,0,.05);">
                    <i class="fas {{ $catMeta['icon'] }} text-xs" style="color: {{ $catMeta['color'] }};"></i>
                    <span class="text-sm font-semibold" style="color: {{ $catMeta['color'] }};">{{ $catMeta['label'] }}</span>
                    <span class="sec-count text-xs">&middot; {{ $catServices->count() }} layanan</span>
                </div>
                <h2 class="sec-title text-3xl lg:text-4xl font-black leading-tight" style="letter-spacing: -0.02em;">
                    {{ $catMeta['desc'] }}
                </h2>
                @if(!empty($catMeta['summary']) && !$isSingle)
                <p class="sec-summary text-base leading-relaxed font-light max-w-2xl mx-auto" style="color: #6B5D52;">
                    {{ $catMeta['summary'] }}
                </p>
                @endif
            </div>

            @if($isSingle)
            {{-- ========== Single Service: Horizontal Layout with Context Panel ========== --}}
            @php
                $service = $catServices->first();
                $serviceSlug = $catServices->keys()->first();
                $mappedColor = $colorMap[$service['color'] ?? ''] ?? ($service['color'] ?? '#5B8DBE');
            @endphp
            <div class="grid lg:grid-cols-5 gap-8 items-start">
                {{-- Service Card (3/5 width) --}}
                <div class="lg:col-span-3">
                    <a href="{{ route($locale === 'en' ? 'services.show.en' : 'services.show.id', $service['slug']) }}"
                       class="svc-card service-card group relative rounded-2xl p-8 lg:p-10 flex flex-col no-underline h-full"
                       style="border: 1px solid {{ $catMeta['color'] }}20;"
                       data-category="{{ Str::slug($catKey) }}"
                       data-aos="fade-right"
                       data-aos-duration="800">

                        {{-- Top Accent Bar --}}
                        <div class="absolute top-0 left-0 right-0 h-1.5 rounded-t-2xl" style="background: linear-gradient(90deg, {{ $catMeta['color'] }}, {{ $catMeta['color'] }}80);"></div>

                        {{-- Icon + Title row --}}
                        <div class="flex items-start gap-5 mb-6">
                            <div class="icon-ring w-16 h-16 rounded-2xl flex items-center justify-center shrink-0" style="background: linear-gradient(135deg, {{ $mappedColor }}20, {{ $mappedColor }}08); border: 1px solid {{ $mappedColor }}25;">
                                <i class="fas {{ $service['icon'] }} text-3xl" style="color: {{ $mappedColor }};"></i>
                            </div>
                            <div>
                                <div class="flex items-center gap-2 mb-1">
                                    <h3 class="card-title text-2xl font-bold">
                                        {{ $service['title'] }}
                                    </h3>
                                    @if(!empty($service['badge']))
                                    <span class="card-badge text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-full" style="background: rgba(232,149,111,.15); color: #E8956F;">{{ $service['badge'] }}</span>
                                    @endif
                                </div>
                                <span class="card-cat text-xs font-medium uppercase tracking-wider">{{ $catMeta['label'] }}</span>
                            </div>
                        </div>

                        {{-- Full description --}}
                        <p class="card-desc text-[15px] leading-relaxed mb-6">
                            {{ $service['short_description'] }}
                        </p>

                        {{-- Sub-services tags --}}
                        @if(!empty($service['sub_services']))
                        <div class="flex flex-wrap gap-2 mb-6">
                            @foreach(array_slice(array_keys($service['sub_services']), 0, 4) as $subKey)
                            <span class="card-tag text-[11px] px-3 py-1.5 rounded-full font-medium" style="background: {{ $catMeta['color'] }}0D; border: 1px solid {{ $catMeta['color'] }}1A; color: #6B5D52;">{{ $service['sub_services'][$subKey]['title'] }}</span>
                            @endforeach
                            @if(count($service['sub_services']) > 4)
                            <span class="card-tag-more text-[11px] px-3 py-1.5 rounded-full" style="background: rgba(0,0,0,.03); color: #9B8B7E;">+{{ count($service['sub_services']) - 4 }} lainnya</span>
                            @endif
                        </div>
                        @endif

                        {{-- Pricing & Time indicators --}}
                        @if(!empty($service['price_range']) || !empty($service['process_time']))
                        <div class="flex items-center gap-4 mb-6">
                            @if(!empty($service['price_range']))
                            <span class="card-price inline-flex items-center gap-1.5 text-xs font-semibold px-3 py-1.5 rounded-full" style="background: rgba(124,179,66,.1); color: #7CB342; border: 1px solid rgba(124,179,66,.15);">
                                <i class="fas fa-tag text-[10px]"></i>{{ $service['price_range'] }}
                            </span>
                            @endif
                            @if(!empty($service['process_time']))
                            <span class="card-time inline-flex items-center gap-1.5 text-xs font-semibold px-3 py-1.5 rounded-full" style="background: rgba(91,141,190,.1); color: #5B8DBE; border: 1px solid rgba(91,141,190,.15);">
                                <i class="fas fa-clock text-[10px]"></i>{{ $service['process_time'] }}
                            </span>
                            @endif
                        </div>
                        @endif

                        {{-- Footer --}}
                        <div class="card-footer pt-6 border-t flex items-center justify-between mt-auto">
                            @if(!empty($service['sub_services']))
                            <span class="card-meta text-xs">
                                <i class="fas fa-layer-group mr-1" style="color: {{ $mappedColor }};"></i>{{ count($service['sub_services']) }} sub-layanan
                            </span>
                            @else
                            <span></span>
                            @endif
                            <span class="inline-flex items-center gap-2 font-semibold text-sm group-hover:gap-3 transition-all duration-300" style="color: {{ $catMeta['color'] }};">
                                <span>Pelajari Lebih Lanjut</span>
                                <i class="fas fa-arrow-right text-xs"></i>
                            </span>
                        </div>
                    </a>
                </div>

                {{-- Context Panel (2/5 width) --}}
                <div class="lg:col-span-2" data-aos="fade-left" data-aos-delay="150" data-aos-duration="800">
                    <div class="ctx-panel rounded-2xl p-8 h-full space-y-6" style="border: 1px solid {{ $catMeta['color'] }}20;">
                        {{-- Category Overview --}}
                        <div>
                            <div class="flex items-center gap-2.5 mb-3">
                                <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background: {{ $catMeta['color'] }}15;">
                                    <i class="fas {{ $catMeta['icon'] }} text-sm" style="color: {{ $catMeta['color'] }};"></i>
                                </div>
                                <h3 class="panel-title font-bold text-base">Tentang Layanan</h3>
                            </div>
                            <p class="panel-body text-sm leading-relaxed">{{ $catMeta['summary'] ?? $catMeta['desc'] }}</p>
                        </div>

                        {{-- Full Sub-services List --}}
                        @if(!empty($service['sub_services']))
                        <div>
                            <h4 class="panel-meta text-xs font-semibold uppercase tracking-wider mb-3">Sub-Layanan Tersedia</h4>
                            <ul class="space-y-2.5">
                                @foreach($service['sub_services'] as $subService)
                                <li class="flex items-start gap-2.5 text-sm panel-body">
                                    <i class="fas fa-check-circle text-xs mt-1 shrink-0" style="color: {{ $catMeta['color'] }};"></i>
                                    <span>{{ $subService['title'] }}</span>
                                </li>
                                @endforeach
                            </ul>
                        </div>
                        @endif

                        {{-- Quick CTA --}}
                        <div class="panel-divider pt-4" style="border-top: 1px solid {{ $catMeta['color'] }}15;">
                            @php
                                $waCatText = 'Halo, saya ingin konsultasi tentang ' . $service['title'];
                                $waCatHref = $whatsappLink . (str_contains($whatsappLink, '?') ? '&' : '?') . 'text=' . rawurlencode($waCatText);
                            @endphp
                            <a href="{{ $waCatHref }}" target="_blank" rel="noopener"
                               class="inline-flex items-center gap-2 text-sm font-semibold transition-all duration-300 hover:gap-3" style="color: {{ $catMeta['color'] }};">
                                <i class="fab fa-whatsapp"></i>
                                <span>Konsultasi {{ $catMeta['label'] }}</span>
                                <i class="fas fa-arrow-right text-xs"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            @else
            {{-- ========== Multiple Services: Grid Layout ========== --}}
            <div class="grid md:grid-cols-2 {{ $catServices->count() >= 6 ? 'xl:grid-cols-3' : '' }} gap-8">
                @foreach($catServices as $slug => $service)
                @php $mappedColor = $colorMap[$service['color'] ?? ''] ?? ($service['color'] ?? '#5B8DBE'); @endphp
                <a href="{{ route($locale === 'en' ? 'services.show.en' : 'services.show.id', $service['slug']) }}"
                   class="svc-card service-card group relative rounded-2xl p-8 flex flex-col no-underline"
                   style="border: 1px solid {{ $catMeta['color'] }}15;"
                   data-category="{{ Str::slug($catKey) }}"
                   data-aos="fade-up"
                   data-aos-delay="{{ $loop->index * 80 }}"
                   data-aos-duration="800">

                    {{-- Top Border Accent (hover) --}}
                    <div class="absolute top-0 left-0 right-0 h-1 rounded-t-2xl opacity-0 group-hover:opacity-100 transition-opacity duration-300" style="background: linear-gradient(90deg, {{ $catMeta['color'] }}, {{ $catMeta['color'] }}80);"></div>

                    {{-- Icon --}}
                    <div class="icon-ring w-14 h-14 rounded-full flex items-center justify-center mb-6" style="background: linear-gradient(135deg, {{ $mappedColor }}20, {{ $mappedColor }}08); border: 1px solid {{ $mappedColor }}25;">
                        <i class="fas {{ $service['icon'] }} text-2xl" style="color: {{ $mappedColor }};"></i>
                    </div>

                    {{-- Content --}}
                    <div class="space-y-3 mb-6 flex-1">
                        <div class="flex items-center gap-2">
                            <h3 class="card-title text-xl font-bold">
                                {{ $service['title'] }}
                            </h3>
                            @if(!empty($service['badge']))
                            <span class="card-badge text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-full" style="background: rgba(232,149,111,.15); color: #E8956F;">{{ $service['badge'] }}</span>
                            @endif
                        </div>
                        <p class="card-desc text-sm leading-relaxed">
                            {{ Str::limit($service['short_description'], 140) }}
                        </p>
                    </div>

                    {{-- Sub-services preview --}}
                    @if(!empty($service['sub_services']))
                    <div class="flex flex-wrap gap-2 mb-6">
                        @foreach(array_slice(array_keys($service['sub_services']), 0, 3) as $subKey)
                        <span class="card-tag text-[11px] px-2.5 py-1 rounded-full" style="background: {{ $catMeta['color'] }}0D; border: 1px solid {{ $catMeta['color'] }}15; color: #6B5D52;">{{ Str::limit($service['sub_services'][$subKey]['title'], 25) }}</span>
                        @endforeach
                        @if(count($service['sub_services']) > 3)
                        <span class="card-tag-more text-[11px] px-2.5 py-1 rounded-full" style="background: rgba(0,0,0,.03); color: #9B8B7E;">+{{ count($service['sub_services']) - 3 }} lainnya</span>
                        @endif
                    </div>
                    @endif

                    {{-- Pricing & Time indicators --}}
                    @if(!empty($service['price_range']) || !empty($service['process_time']))
                    <div class="flex items-center gap-3 mb-6 flex-wrap">
                        @if(!empty($service['price_range']))
                        <span class="card-price inline-flex items-center gap-1.5 text-[11px] font-semibold px-2.5 py-1 rounded-full" style="background: rgba(124,179,66,.1); color: #7CB342; border: 1px solid rgba(124,179,66,.15);">
                            <i class="fas fa-tag text-[9px]"></i>{{ $service['price_range'] }}
                        </span>
                        @endif
                        @if(!empty($service['process_time']))
                        <span class="card-time inline-flex items-center gap-1.5 text-[11px] font-semibold px-2.5 py-1 rounded-full" style="background: rgba(91,141,190,.1); color: #5B8DBE; border: 1px solid rgba(91,141,190,.15);">
                            <i class="fas fa-clock text-[9px]"></i>{{ $service['process_time'] }}
                        </span>
                        @endif
                    </div>
                    @endif

                    {{-- Footer --}}
                    <div class="card-footer pt-6 border-t flex items-center justify-between mt-auto">
                        @if(!empty($service['sub_services']))
                        <span class="card-meta text-xs">
                            <i class="fas fa-layer-group mr-1" style="color: {{ $mappedColor }};"></i>{{ count($service['sub_services']) }} sub-layanan
                        </span>
                        @else
                        <span></span>
                        @endif
                        <span class="inline-flex items-center gap-2 font-semibold text-sm group-hover:gap-3 transition-all duration-300" style="color: {{ $catMeta['color'] }};">
                            <span>Pelajari Lebih Lanjut</span>
                            <i class="fas fa-arrow-right text-xs"></i>
                        </span>
                    </div>
                </a>
                @endforeach
            </div>

            {{-- Category Testimonial --}}
            @if(!empty($categoryTestimonial[$catKey]))
            @php $catTestimonial = $categoryTestimonial[$catKey]; @endphp
            <div class="cat-testimonial mt-12 max-w-2xl mx-auto text-center" data-aos="fade-up" data-aos-duration="800">
                <div class="testimonial-card rounded-2xl p-8" style="border: 1px solid {{ $catMeta['color'] }}15;">
                    <i class="fas fa-quote-left text-2xl mb-4 block" style="color: {{ $catMeta['color'] }}30;"></i>
                    <p class="testimonial-text text-base leading-relaxed font-light mb-5 italic" style="color: #6B5D52;">
                        "{{ $catTestimonial['text'] ?? $catTestimonial['content'] ?? '' }}"
                    </p>
                    <div class="flex items-center justify-center gap-3">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center text-sm font-bold" style="background: {{ $catMeta['color'] }}15; color: {{ $catMeta['color'] }};">
                            {{ strtoupper(substr($catTestimonial['name'] ?? 'K', 0, 1)) }}
                        </div>
                        <div class="text-left">
                            <div class="testimonial-name text-sm font-semibold" style="color: #1A1410;">{{ $catTestimonial['name'] ?? '' }}</div>
                            <div class="testimonial-role text-xs" style="color: #9B8B7E;">{{ $catTestimonial['role'] ?? $catTestimonial['position'] ?? '' }}</div>
                        </div>
                    </div>
                </div>
            </div>
            @endif
            @endif
        </div>
    </section>
    @endif
@endforeach

<!-- =============================================
     COMPARISON TABLE — AMDAL vs UKL-UPL vs SPPL
     ============================================= -->
<section class="py-16 lg:py-24" style="background: #fff;">
    <div class="container-wide">
        <div class="max-w-3xl mx-auto text-center mb-12 space-y-4" data-aos="fade-up" data-aos-duration="800">
            <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full" style="background: rgba(91,141,190,.1); border: 1px solid rgba(91,141,190,.2);">
                <i class="fas fa-balance-scale text-xs" style="color: #5B8DBE;"></i>
                <span class="text-sm font-semibold" style="color: #5B8DBE;">Perbandingan</span>
            </div>
            <h2 class="text-3xl lg:text-4xl font-black leading-tight" style="letter-spacing: -0.02em; color: #1A1410;">
                AMDAL vs UKL-UPL vs SPPL
            </h2>
            <p class="text-base leading-relaxed font-light max-w-2xl mx-auto" style="color: #6B5D52;">
                Pahami perbedaan utama dokumen lingkungan agar Anda memilih yang tepat sesuai skala usaha.
            </p>
        </div>

        <div class="overflow-x-auto rounded-2xl" style="border: 1px solid rgba(91,141,190,.1);" data-aos="fade-up" data-aos-delay="100" data-aos-duration="800">
            <table class="comparison-table w-full text-sm" style="min-width: 640px;">
                <thead>
                    <tr style="background: linear-gradient(135deg, #1E1B18, #28231E);">
                        <th class="px-6 py-4 text-left font-semibold text-xs uppercase tracking-wider" style="color: rgba(255,255,255,.6); width: 22%;">Aspek</th>
                        <th class="px-6 py-4 text-center font-semibold" style="color: #E8956F;">AMDAL</th>
                        <th class="px-6 py-4 text-center font-semibold" style="color: #5B8DBE;">UKL-UPL</th>
                        <th class="px-6 py-4 text-center font-semibold" style="color: #7CB342;">SPPL</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $compRows = [
                            ['label' => 'Skala Usaha', 'amdal' => 'Besar & berdampak penting', 'ukl' => 'Menengah, dampak tidak signifikan', 'sppl' => 'Kecil, dampak minimal'],
                            ['label' => 'Estimasi Biaya', 'amdal' => 'Rp 150 Jt – 1 M+', 'ukl' => 'Rp 15 – 75 Jt', 'sppl' => 'Gratis – Rp 5 Jt'],
                            ['label' => 'Durasi Proses', 'amdal' => '6 – 12 Bulan', 'ukl' => '30 – 60 Hari', 'sppl' => '1 – 7 Hari'],
                            ['label' => 'Kompleksitas', 'amdal' => 'Sangat Tinggi', 'ukl' => 'Sedang', 'sppl' => 'Rendah'],
                            ['label' => 'Dokumen', 'amdal' => 'KA, ANDAL, RKL-RPL', 'ukl' => 'Formulir UKL-UPL', 'sppl' => 'Surat Pernyataan'],
                            ['label' => 'Pelibatan Masyarakat', 'amdal' => 'Wajib (konsultasi publik)', 'ukl' => 'Tidak wajib', 'sppl' => 'Tidak diperlukan'],
                            ['label' => 'Contoh Kegiatan', 'amdal' => 'Pabrik besar, pertambangan, PLTU', 'ukl' => 'Pabrik menengah, hotel, RS', 'sppl' => 'Toko, restoran kecil, bengkel'],
                        ];
                    @endphp
                    @foreach($compRows as $ri => $row)
                    <tr style="background: {{ $ri % 2 === 0 ? '#FDFBF8' : '#fff' }}; border-bottom: 1px solid rgba(91,141,190,.06);">
                        <td class="px-6 py-4 font-semibold" style="color: #1A1410;">{{ $row['label'] }}</td>
                        <td class="px-6 py-4 text-center" style="color: #6B5D52;">{{ $row['amdal'] }}</td>
                        <td class="px-6 py-4 text-center" style="color: #6B5D52;">{{ $row['ukl'] }}</td>
                        <td class="px-6 py-4 text-center" style="color: #6B5D52;">{{ $row['sppl'] }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="text-center mt-8" data-aos="fade-up" data-aos-delay="200">
            @php
                $waCompText = 'Halo, saya ingin konsultasi tentang jenis dokumen lingkungan yang tepat untuk usaha saya';
                $waCompHref = $whatsappLink . (str_contains($whatsappLink, '?') ? '&' : '?') . 'text=' . rawurlencode($waCompText);
            @endphp
            <a href="{{ $waCompHref }}" target="_blank" rel="noopener"
               class="inline-flex items-center gap-2 px-6 py-3 rounded-full font-semibold text-sm transition-all duration-300 hover:translate-y-[-2px]" style="background: rgba(91,141,190,.1); color: #5B8DBE; border: 1px solid rgba(91,141,190,.2);">
                <i class="fab fa-whatsapp"></i>
                <span>Tidak yakin? Konsultasi gratis</span>
            </a>
        </div>
    </div>
</section>

<!-- =============================================
     PROCESS — Timeline Steps (Landing Pattern)
     ============================================= -->
<section class="py-16 lg:py-24" style="background: linear-gradient(135deg, #FDFBF8, rgba(245,240,235,.6));">
    <div class="container-wide">
        {{-- Section Header --}}
        <div class="max-w-3xl mx-auto text-center mb-16 space-y-6" data-aos="fade-up" data-aos-duration="800">
            <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full" style="background: rgba(91,141,190,.1); border: 1px solid rgba(91,141,190,.2);">
                <span class="text-sm font-semibold" style="color: #5B8DBE;">Cara Kerja Kami</span>
            </div>
            <h2 class="text-4xl lg:text-5xl font-black leading-tight" style="letter-spacing: -0.02em; color: #1A1410;">
                Proses Perizinan yang Mudah & Transparan
            </h2>
            <p class="text-lg leading-relaxed font-light max-w-2xl mx-auto" style="color: #6B5D52;">
                Empat langkah terstruktur memastikan setiap izin ditangani dengan jelas, terukur, dan transparan.
            </p>
        </div>

        {{-- Process Grid --}}
        <div class="grid lg:grid-cols-2 gap-8">
            @php
                $steps = [
                    ['num' => 1, 'icon' => 'fa-comments', 'title' => 'Konsultasi & Asesmen', 'desc' => 'Diskusi kebutuhan perizinan, analisis regulasi, penilaian kelayakan, dan identifikasi dokumen yang diperlukan.', 'items' => ['Analisis kebutuhan perizinan', 'Pemetaan regulasi terkait', 'Estimasi biaya & timeline'], 'color' => '#E8956F'],
                    ['num' => 2, 'icon' => 'fa-file-alt', 'title' => 'Penyusunan Dokumen', 'desc' => 'Tim ahli menyusun seluruh dokumen teknis dan administratif secara profesional sesuai standar instansi.', 'items' => ['Dokumen teknis lengkap', 'Validasi administratif', 'Quality assurance internal'], 'color' => '#5B8DBE'],
                    ['num' => 3, 'icon' => 'fa-paper-plane', 'title' => 'Pengajuan & Koordinasi', 'desc' => 'Koordinasi langsung dengan instansi pemerintah terkait untuk pengajuan dan tindak lanjut perizinan.', 'items' => ['Pengajuan ke instansi', 'Koordinasi antar lembaga', 'Monitoring progres real-time'], 'color' => '#8B5CF6'],
                    ['num' => 4, 'icon' => 'fa-check-double', 'title' => 'Izin Terbit & Pendampingan', 'desc' => 'Pendampingan hingga izin terbit, dokumentasi serah terima, dan dukungan pasca-penerbitan.', 'items' => ['Verifikasi izin terbit', 'Dokumentasi lengkap', 'Dukungan pasca-izin'], 'color' => '#7CB342'],
                ];
            @endphp
            @foreach($steps as $index => $step)
            <article class="group relative rounded-2xl p-8 transition-all duration-300 hover:translate-y-[-4px]"
                     style="background: #fff; border: 1px solid rgba(91,141,190,.1);"
                     data-aos="fade-up" 
                     data-aos-delay="{{ $index * 100 }}"
                     data-aos-duration="800">
                {{-- Top Accent --}}
                <div class="absolute top-0 left-0 right-0 h-1 rounded-t-2xl opacity-0 group-hover:opacity-100 transition-opacity duration-300" style="background: linear-gradient(90deg, #5B8DBE, #E8956F);"></div>
                
                {{-- Step Header --}}
                <div class="flex items-start gap-5 mb-5">
                    <div class="w-12 h-12 rounded-full flex items-center justify-center font-bold text-lg shrink-0" style="background: linear-gradient(135deg, {{ $step['color'] }}, {{ $step['color'] }}B3); color: #fff; box-shadow: 0 4px 12px rgba(0,0,0,.1);">
                        {{ $step['num'] }}
                    </div>
                    <div class="w-14 h-14 rounded-full flex items-center justify-center shrink-0 transition-all duration-300" style="background: linear-gradient(135deg, {{ $step['color'] }}26, {{ $step['color'] }}0D); border: 1px solid {{ $step['color'] }}33;">
                        <i class="fas {{ $step['icon'] }} text-2xl" style="color: {{ $step['color'] }};"></i>
                    </div>
                </div>
                
                {{-- Content --}}
                <h3 class="text-xl font-bold mb-3 transition-colors duration-300" style="color: #1A1410;">
                    {{ $step['title'] }}
                </h3>
                <p class="text-sm leading-relaxed mb-5" style="color: #6B5D52;">
                    {{ $step['desc'] }}
                </p>
                
                {{-- Checklist --}}
                <ul class="space-y-2">
                    @foreach($step['items'] as $item)
                    <li class="flex items-center gap-2 text-sm" style="color: #6B5D52;">
                        <i class="fas fa-check-circle text-xs shrink-0" style="color: #7CB342;"></i>
                        {{ $item }}
                    </li>
                    @endforeach
                </ul>
            </article>
            @endforeach
        </div>
    </div>
</section>

<!-- =============================================
     WHY CHOOSE US — Dark Dove Section
     ============================================= -->
<section class="dove py-16 lg:py-24">
    <div class="container-wide">
        {{-- Section Header --}}
        <div class="max-w-3xl mx-auto text-center mb-12 space-y-6" data-aos="fade-up" data-aos-duration="800">
            <div class="sec-badge inline-flex items-center gap-2 px-4 py-2 rounded-full">
                <span class="text-sm font-semibold" style="color: #5B8DBE;">Mengapa Kami</span>
            </div>
            <h2 class="sec-title text-4xl lg:text-5xl font-black leading-tight" style="letter-spacing: -0.02em;">
                Mitra Terpercaya untuk Perizinan Anda
            </h2>
            <p class="panel-body text-lg leading-relaxed font-light max-w-2xl mx-auto" style="color: rgba(255,255,255,.6);">
                Lebih dari sekadar pengurusan dokumen — kami adalah perpanjangan tim compliance Anda.
            </p>
        </div>

        {{-- Trust Grid --}}
        <div class="grid md:grid-cols-2 xl:grid-cols-4 gap-8">
            @php
                $proofs = [
                    ['icon' => 'fa-certificate', 'color' => '#5B8DBE', 'title' => 'Tim Bersertifikat', 'desc' => 'Konsultan tersertifikasi dengan keahlian multidisiplin — dari analisis lingkungan hingga K3.'],
                    ['icon' => 'fa-bolt', 'color' => '#E8956F', 'title' => 'Proses Cepat', 'desc' => 'Hubungan institusional kuat dan pemahaman prosedur mendalam untuk pengurusan lebih cepat.'],
                    ['icon' => 'fa-eye', 'color' => '#7CB342', 'title' => 'Transparansi Penuh', 'desc' => 'Estimasi biaya detail di awal, update progress berkala, dan monitoring proyek.'],
                    ['icon' => 'fa-shield-alt', 'color' => '#8B5CF6', 'title' => 'Garansi Pendampingan', 'desc' => 'Dari konsultasi hingga izin terbit termasuk revisi dan dukungan pasca-penerbitan.'],
                ];
            @endphp
            @foreach($proofs as $index => $proof)
            <article class="trust-card group relative rounded-2xl p-8 transition-all duration-300 text-center"
                     data-aos="fade-up" data-aos-delay="{{ $index * 80 }}" data-aos-duration="800">
                <div class="absolute top-0 left-0 right-0 h-1 rounded-t-2xl opacity-0 group-hover:opacity-100 transition-opacity duration-300" style="background: linear-gradient(90deg, #5B8DBE, #E8956F);"></div>
                <div class="icon-ring w-14 h-14 rounded-full flex items-center justify-center mx-auto mb-6" style="background: linear-gradient(135deg, {{ $proof['color'] }}30, {{ $proof['color'] }}15); border: 1px solid {{ $proof['color'] }}40;">
                    <i class="fas {{ $proof['icon'] }} text-2xl" style="color: {{ $proof['color'] }};"></i>
                </div>
                <h3 class="trust-title text-xl font-bold mb-3">{{ $proof['title'] }}</h3>
                <p class="trust-desc text-sm leading-relaxed">{{ $proof['desc'] }}</p>
            </article>
            @endforeach
        </div>
    </div>
</section>

<!-- =============================================
     CTA — Full-Width Dark CTA
     ============================================= -->
<section class="py-16 lg:py-24" style="background: linear-gradient(135deg, #FDFBF8, rgba(245,240,235,.6));">
    <div class="container-wide">
        <div class="cta-dark rounded-2xl p-10 lg:p-16 relative overflow-hidden" data-aos="fade-up" data-aos-duration="800">
            {{-- Subtle decorative glow --}}
            <div class="absolute -top-24 -right-24 w-64 h-64 rounded-full opacity-10" style="background: radial-gradient(circle, #5B8DBE, transparent);"></div>
            <div class="absolute -bottom-16 -left-16 w-48 h-48 rounded-full opacity-10" style="background: radial-gradient(circle, #E8956F, transparent);"></div>
            <div class="grid lg:grid-cols-[1.2fr_0.8fr] gap-10 items-center relative z-10">
                {{-- Left: Content --}}
                <div>
                    <span class="cta-label text-xs font-semibold uppercase tracking-wider mb-3 block">Jangan Tunda Lagi</span>
                    <h2 class="cta-title text-3xl lg:text-4xl font-black mb-4 leading-tight" style="letter-spacing: -0.02em;">
                        Regulasi Berubah — Pastikan Izin Anda Terbit Tepat Waktu
                    </h2>
                    <p class="cta-desc text-lg leading-relaxed font-light mb-8">
                        Perubahan regulasi sering terjadi tanpa peringatan. Semakin cepat Anda memulai, semakin kecil risiko keterlambatan dan sanksi. Konsultasi gratis — tanpa kewajiban.
                    </p>
                    <div class="flex flex-wrap gap-4">
                        @php
                            $waCtaText = 'Halo, saya ingin konsultasi perizinan untuk bisnis saya';
                            $waCtaHref = $whatsappLink . (str_contains($whatsappLink, '?') ? '&' : '?') . 'text=' . rawurlencode($waCtaText);
                        @endphp
                        <a href="{{ $waCtaHref }}" target="_blank" rel="noopener" 
                           class="cta-btn-primary inline-flex items-center gap-3 px-8 py-4 rounded-full font-semibold transition-all duration-300 hover:translate-y-[-2px]">
                            <i class="fab fa-whatsapp text-lg"></i>
                            <span>Konsultasi via WhatsApp</span>
                        </a>
                        <a href="{{ $phoneHref }}" 
                           class="cta-btn-secondary inline-flex items-center gap-3 px-8 py-4 rounded-full font-semibold transition-all duration-300">
                            <i class="fas fa-phone text-sm"></i>
                            <span>{{ $phoneRaw }}</span>
                        </a>
                    </div>
                </div>

                {{-- Right: Benefits --}}
                <div class="cta-panel rounded-2xl p-8">
                    <h3 class="cta-panel-title font-bold mb-5 text-sm uppercase tracking-wider">Apa yang Anda Dapatkan</h3>
                    <ul class="space-y-4">
                        @php
                            $benefits = [
                                'Konsultasi awal & analisis kebutuhan gratis',
                                'Estimasi biaya & timeline transparan',
                                'Pendampingan end-to-end hingga izin terbit',
                                'Dukungan pasca-penerbitan izin',
                            ];
                        @endphp
                        @foreach($benefits as $benefit)
                        <li class="cta-panel-item flex items-start gap-3 text-sm">
                            <i class="fas fa-check-circle mt-0.5 shrink-0" style="color: #7CB342;"></i>
                            <span>{{ $benefit }}</span>
                        </li>
                        @endforeach
                    </ul>
                    <div class="cta-panel-divider mt-6 pt-5 flex items-center gap-3">
                        <div class="flex -space-x-2">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-semibold" style="background: linear-gradient(135deg, #5B8DBE, #3A5D82); border: 2px solid #28231E; color: #fff;">B</div>
                            <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-semibold" style="background: linear-gradient(135deg, #E8956F, #C96535); border: 2px solid #28231E; color: #fff;">M</div>
                        </div>
                        <span class="cta-panel-note text-xs">Respon dalam <strong>&lt; 30 menit</strong> pada jam kerja</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- =============================================
     JANGKAUAN WILAYAH — Collapsible Province Grid
     ============================================= -->
@php
    $seoCities = config('programmatic_seo.cities', []);
    $cityByProvince = collect($seoCities)->groupBy('province')->sortKeys();
    $provinceOrder = ['Jawa Barat', 'DKI Jakarta', 'Banten', 'Jawa Tengah', 'DI Yogyakarta', 'Jawa Timur'];
    $sortedProvinces = collect($provinceOrder)
        ->filter(fn($p) => $cityByProvince->has($p))
        ->merge($cityByProvince->keys()->diff($provinceOrder)->sort())
        ->unique();
@endphp
@if($seoCities)
<section class="py-8 lg:py-10" style="background: linear-gradient(135deg, #FDFBF8, rgba(245,240,235,.6));" id="wilayah">
    <div class="container-wide">
        <div class="flex items-center gap-3 mb-4" data-aos="fade-up" data-aos-duration="800">
            <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full" style="background: rgba(91,141,190,.1); border: 1px solid rgba(91,141,190,.2);">
                <i class="fas fa-map-marked-alt text-xs" style="color: #5B8DBE;"></i>
                <span class="text-xs font-semibold" style="color: #5B8DBE;">{{ count($seoCities) }} Kota</span>
            </div>
            <h2 class="text-xl lg:text-2xl font-black" style="letter-spacing: -0.02em; color: #1A1410;">Jangkauan Wilayah Layanan</h2>
        </div>

        <div data-aos="fade-up" data-aos-delay="100" data-aos-duration="800">
            {{-- Province pills — fill left to right --}}
            <div class="flex flex-wrap gap-1.5">
                @foreach($sortedProvinces as $province)
                @php $provCities = $cityByProvince[$province]; @endphp
                <button onclick="toggleWilayah(this, '{{ Str::slug($province) }}')" class="wil-btn inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-sm font-semibold transition-all duration-200 cursor-pointer" style="background: #fff; color: #1A1410; border: 1px solid rgba(91,141,190,.12);" aria-expanded="false" onmouseover="if(!this.classList.contains('wil-active')){this.style.borderColor='rgba(91,141,190,.3)';this.style.background='rgba(91,141,190,.04)'}" onmouseout="if(!this.classList.contains('wil-active')){this.style.borderColor='rgba(91,141,190,.12)';this.style.background='#fff'}">
                    <span>{{ $province }}</span>
                    <span class="text-[11px] font-normal" style="color: #5B8DBE;">{{ count($provCities) }}</span>
                    <i class="fas fa-chevron-down text-[9px] transition-transform duration-200 wil-chev" style="color: rgba(91,141,190,.35);"></i>
                </button>
                @endforeach
            </div>

            {{-- Expandable city panel (dark strip) --}}
            <div id="wil-panel" class="overflow-hidden transition-all duration-300" style="max-height: 0;">
                @foreach($sortedProvinces as $province)
                @php $provCities = $cityByProvince[$province]; @endphp
                <div id="wil-{{ Str::slug($province) }}" class="wil-cities hidden">
                    <div class="mt-2 rounded-xl px-4 py-3" style="background: #1E1B18;">
                        <div class="flex items-center gap-2 mb-2">
                            <i class="fas fa-map-marker-alt text-xs" style="color: #E8956F;"></i>
                            <span class="text-xs font-bold uppercase tracking-wider" style="color: #E8956F;">{{ $province }}</span>
                            <span class="text-[11px]" style="color: rgba(255,255,255,.4);">— {{ count($provCities) }} kota</span>
                        </div>
                        <div class="flex flex-wrap gap-1.5">
                            @foreach($provCities as $city)
                            <a href="{{ url('/layanan/kota/' . $city['slug']) }}" class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium transition-all duration-200" style="background: rgba(255,255,255,.08); color: rgba(255,255,255,.85); border: 1px solid rgba(255,255,255,.1);" onmouseover="this.style.background='rgba(232,149,111,.2)';this.style.borderColor='rgba(232,149,111,.4)';this.style.color='#E8956F'" onmouseout="this.style.background='rgba(255,255,255,.08)';this.style.borderColor='rgba(255,255,255,.1)';this.style.color='rgba(255,255,255,.85)'">
                                {{ $city['name'] }}
                            </a>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</section>
<script>
function toggleWilayah(btn, slug) {
    var panel = document.getElementById('wil-panel');
    var target = document.getElementById('wil-' + slug);
    var wasOpen = btn.getAttribute('aria-expanded') === 'true';
    document.querySelectorAll('.wil-btn').forEach(function(b) {
        b.setAttribute('aria-expanded', 'false');
        b.classList.remove('wil-active');
        b.style.background = '#fff';
        b.style.color = '#1A1410';
        b.style.borderColor = 'rgba(91,141,190,.12)';
        b.querySelector('.wil-chev').style.transform = '';
        b.querySelector('.wil-chev').style.color = 'rgba(91,141,190,.35)';
    });
    document.querySelectorAll('.wil-cities').forEach(function(c) { c.classList.add('hidden'); });
    if (!wasOpen) {
        target.classList.remove('hidden');
        btn.setAttribute('aria-expanded', 'true');
        btn.classList.add('wil-active');
        btn.style.background = '#1E1B18';
        btn.style.borderColor = '#1E1B18';
        btn.style.color = '#E8956F';
        btn.querySelector('.wil-chev').style.transform = 'rotate(180deg)';
        btn.querySelector('.wil-chev').style.color = '#E8956F';
        panel.style.maxHeight = target.scrollHeight + 16 + 'px';
    } else {
        panel.style.maxHeight = '0';
    }
}
</script>
@endif

<!-- =============================================
     FAQ — Accordion Section
     ============================================= -->
@if(!empty($serviceFaqs))
<section class="dove py-16 lg:py-24" style="background: linear-gradient(135deg, #1E1B18, #28231E);">
    <div class="container-wide">
        <div class="max-w-3xl mx-auto text-center mb-12 space-y-4" data-aos="fade-up" data-aos-duration="800">
            <div class="sec-badge inline-flex items-center gap-2 px-4 py-2 rounded-full" style="background: rgba(255,255,255,.08); border: 1px solid rgba(255,255,255,.12);">
                <i class="fas fa-question-circle text-xs" style="color: #E8956F;"></i>
                <span class="text-sm font-semibold" style="color: #E8956F;">FAQ</span>
            </div>
            <h2 class="sec-title text-3xl lg:text-4xl font-black leading-tight" style="letter-spacing: -0.02em; color: #fff;">
                Pertanyaan yang Sering Diajukan
            </h2>
            <p class="panel-body text-base leading-relaxed font-light max-w-2xl mx-auto" style="color: rgba(255,255,255,.6);">
                Jawaban atas pertanyaan umum seputar perizinan dan layanan kami.
            </p>
        </div>

        <div class="max-w-3xl mx-auto space-y-3" data-aos="fade-up" data-aos-delay="100" data-aos-duration="800">
            @foreach($serviceFaqs as $faqIdx => $faq)
            <div class="faq-item rounded-xl overflow-hidden" style="background: rgba(255,255,255,.04); border: 1px solid rgba(255,255,255,.08);">
                <button onclick="toggleFaq(this)" class="faq-trigger w-full flex items-center justify-between px-6 py-5 text-left transition-all duration-300" style="background: rgba(255,255,255,.04);" aria-expanded="false">
                    <span class="faq-question text-sm font-semibold pr-4" style="color: rgba(255,255,255,.9);">{{ $faq['q'] }}</span>
                    <i class="fas fa-chevron-down text-xs shrink-0 faq-chevron transition-transform duration-300" style="color: rgba(255,255,255,.4);"></i>
                </button>
                <div class="faq-answer overflow-hidden transition-all duration-300" style="max-height: 0; background: rgba(255,255,255,.02);">
                    <p class="px-6 pb-5 pt-0 text-sm leading-relaxed" style="color: rgba(255,255,255,.6);">{{ $faq['a'] }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif

<!-- ============================================
     STYLES & SCRIPTS
     ============================================ -->
<style>
    /* Scrollbar hide */
    .scrollbar-hide::-webkit-scrollbar { display: none; }
    .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }

    /* Category tabs */
    .cat-tab {
        background: transparent;
        color: #6B5D52;
        border: 1px solid transparent;
        cursor: pointer;
        white-space: nowrap;
    }
    .cat-tab:hover { background: #5B8DBE10; color: #1A1410; border-color: #5B8DBE20; }
    .cat-tab-active {
        background: linear-gradient(135deg, #5B8DBE, #3A5D82) !important;
        color: #fff !important;
        border-color: transparent !important;
    }

    /* Filter hidden states */
    .service-card[data-hidden="true"],
    .service-category-section[data-hidden="true"] { display: none; }
    a.service-card { text-decoration: none; color: inherit; }

    /* ==========================================
       Light Theme — Base text colors (always)
       ========================================== */
    .sec-title { color: #1A1410; }
    .sec-count { color: #9B8B7E; }
    .card-title { color: #1A1410; transition: color .3s; }
    .card-desc { color: #6B5D52; }
    .card-meta { color: #9B8B7E; }
    .card-cat  { color: #9B8B7E; }
    .card-footer { border-color: rgba(91,141,190,.1); }
    .panel-title { color: #1A1410; }
    .panel-body  { color: #6B5D52; }
    .panel-meta  { color: #9B8B7E; }

    /* Service card — light mode */
    .svc-card {
        background: #fff;
        transition: all .3s ease;
    }
    .svc-card:hover { box-shadow: 0 10px 40px rgba(0,0,0,.08); }

    /* Context panel — light mode */
    .ctx-panel { background: rgba(255,255,255,.8); backdrop-filter: blur(8px); }

    /* ==========================================
       DOVE (Dark Magazine) Theme
       Applied via .dove class on section
       ========================================== */
    .dove {
        background: linear-gradient(135deg, #1E1B18, #28231E);
    }

    /* Heading & text overrides */
    .dove .sec-badge { background: rgba(255,255,255,.08) !important; border: 1px solid rgba(255,255,255,.12) !important; box-shadow: none !important; }
    .dove .sec-title { color: #fff; }
    .dove .sec-count { color: rgba(255,255,255,.45); }

    /* Cards */
    .dove .svc-card {
        background: rgba(255,255,255,.05) !important;
        border: 1px solid rgba(255,255,255,.08) !important;
    }
    .dove .svc-card:hover {
        background: rgba(255,255,255,.10) !important;
        border-color: rgba(255,255,255,.16) !important;
        box-shadow: 0 10px 40px rgba(0,0,0,.3) !important;
    }
    .dove .card-title { color: #fff; }
    .dove .card-desc { color: rgba(255,255,255,.65); }
    .dove .card-meta { color: rgba(255,255,255,.45); }
    .dove .card-cat  { color: rgba(255,255,255,.45); }
    .dove .card-tag {
        background: rgba(255,255,255,.08) !important;
        border-color: rgba(255,255,255,.1) !important;
        color: rgba(255,255,255,.6) !important;
    }
    .dove .card-tag-more {
        background: rgba(255,255,255,.04) !important;
        color: rgba(255,255,255,.35) !important;
    }
    .dove .card-footer { border-color: rgba(255,255,255,.08) !important; }
    .dove .card-badge { background: rgba(232,149,111,.25) !important; }

    /* Context panel */
    .dove .ctx-panel {
        background: rgba(255,255,255,.04) !important;
        border: 1px solid rgba(255,255,255,.08) !important;
        backdrop-filter: none;
    }
    .dove .panel-title { color: #fff; }
    .dove .panel-body  { color: rgba(255,255,255,.65); }
    .dove .panel-meta  { color: rgba(255,255,255,.45); }
    .dove .panel-divider { border-color: rgba(255,255,255,.08) !important; }

    /* Trust / proof cards in dove */
    .dove .trust-card {
        background: rgba(255,255,255,.05);
        border: 1px solid rgba(255,255,255,.08);
    }
    .dove .trust-card:hover {
        background: rgba(255,255,255,.10);
        border-color: rgba(255,255,255,.16);
    }
    .dove .trust-title { color: #fff; transition: color .3s; }
    .dove .trust-desc  { color: rgba(255,255,255,.6); }

    /* CTA dark card */
    .cta-dark {
        background: linear-gradient(135deg, #1E1B18, #28231E);
        border: 1px solid rgba(255,255,255,.06);
        position: relative;
        overflow: hidden;
    }
    .cta-dark .cta-label { color: rgba(255,255,255,.45); }
    .cta-dark .cta-title { color: #fff; }
    .cta-dark .cta-desc  { color: rgba(255,255,255,.6); }
    .cta-dark .cta-panel {
        background: rgba(255,255,255,.05);
        border: 1px solid rgba(255,255,255,.08);
    }
    .cta-dark .cta-panel-title { color: #fff; }
    .cta-dark .cta-panel-item { color: rgba(255,255,255,.65); }
    .cta-dark .cta-panel-divider { border-color: rgba(255,255,255,.08); }
    .cta-dark .cta-panel-note { color: rgba(255,255,255,.45); }
    .cta-dark .cta-panel-note strong { color: rgba(255,255,255,.8); }
    .cta-dark .cta-btn-secondary {
        background: rgba(255,255,255,.08);
        color: #fff;
        border: 2px solid rgba(255,255,255,.15);
    }
    .cta-dark .cta-btn-secondary:hover {
        background: rgba(255,255,255,.14);
        border-color: rgba(255,255,255,.3);
    }
    .cta-dark .cta-btn-primary {
        background: linear-gradient(90deg, #5B8DBE, #3A5D82);
        color: #fff;
        box-shadow: 0 4px 16px rgba(91,141,190,.3);
    }
    .cta-dark .cta-btn-primary:hover {
        box-shadow: 0 8px 28px rgba(91,141,190,.45);
    }

    /* Trust cards — light mode defaults */
    .trust-card {
        background: rgba(255,255,255,.06);
        border: 1px solid rgba(255,255,255,.08);
        transition: all .3s ease;
    }
    .trust-card:hover {
        transform: translateY(-4px);
    }
    .trust-title { color: #1A1410; transition: color .3s; }
    .trust-desc  { color: #6B5D52; }

    /* Icon container glow on dark */
    .dove .icon-ring {
        box-shadow: 0 0 20px rgba(0,0,0,.2);
    }

    /* ==========================================
       PRICING & TIME INDICATORS
       ========================================== */
    .card-price, .card-time {
        transition: all .3s ease;
    }
    .dove .card-price {
        background: rgba(124,179,66,.15) !important;
        border-color: rgba(124,179,66,.25) !important;
        color: #7CB342 !important;
    }
    .dove .card-time {
        background: rgba(91,141,190,.15) !important;
        border-color: rgba(91,141,190,.25) !important;
        color: #5B8DBE !important;
    }

    /* ==========================================
       COMPARISON TABLE
       ========================================== */
    .comparison-table th, .comparison-table td {
        vertical-align: top;
    }
    .comparison-table th { color: #fff; }
    .comparison-table tbody tr { transition: background .2s; }
    .comparison-table tbody tr:hover { background: rgba(91,141,190,.04) !important; }

    /* ==========================================
       FAQ ACCORDION
       ========================================== */
    .faq-item { transition: all .3s ease; }
    .faq-item:hover { border-color: rgba(255,255,255,.15) !important; }
    .faq-answer { transition: max-height .3s ease; }

    /* ==========================================
       CATEGORY TESTIMONIALS
       ========================================== */
    .testimonial-card {
        background: rgba(255,255,255,.6);
        backdrop-filter: blur(6px);
        transition: all .3s ease;
    }
    .testimonial-card:hover {
        box-shadow: 0 8px 30px rgba(0,0,0,.06);
    }
    .dove .testimonial-card {
        background: rgba(255,255,255,.04) !important;
        border-color: rgba(255,255,255,.08) !important;
    }
    .dove .testimonial-text { color: rgba(255,255,255,.65) !important; }
    .dove .testimonial-name { color: #fff !important; }
    .dove .testimonial-role { color: rgba(255,255,255,.45) !important; }

    /* ==========================================
       CATEGORY SUMMARY
       ========================================== */
    .dove .sec-summary { color: rgba(255,255,255,.55) !important; }

    /* ==========================================
       CLIENT LOGO STRIP
       ========================================== */
    .client-logo {
        transition: all .3s ease;
    }
    .client-logo:hover {
        background: rgba(91,141,190,.12) !important;
        border-color: rgba(91,141,190,.2) !important;
        color: #1A1410 !important;
    }

    /* ==========================================
       CARD HOVER MICROINTERACTIONS
       ========================================== */
    .svc-card .icon-ring {
        transition: transform .35s ease, box-shadow .35s ease;
    }
    .svc-card:hover .icon-ring {
        transform: scale(1.08);
        box-shadow: 0 6px 20px rgba(0,0,0,.08);
    }
    .svc-card .card-tag {
        transition: all .25s ease;
        transform: translateX(0);
    }
    .svc-card:hover .card-tag {
        transform: translateX(2px);
    }
    .svc-card:hover .card-title {
        color: #5B8DBE;
    }
    .dove .svc-card:hover .card-title {
        color: #E8956F !important;
    }
</style>

<script>
function filterCategory(cat) {
    document.querySelectorAll('.cat-tab').forEach(t => {
        t.classList.toggle('cat-tab-active', t.dataset.cat === cat);
    });
    document.querySelectorAll('.service-category-section').forEach(s => {
        if (cat === 'all') {
            s.removeAttribute('data-hidden');
        } else {
            if (s.dataset.categorySection !== cat) {
                s.setAttribute('data-hidden', 'true');
            } else {
                s.removeAttribute('data-hidden');
            }
        }
    });
}

/* FAQ Accordion Toggle */
function toggleFaq(btn) {
    const item = btn.closest('.faq-item');
    const answer = item.querySelector('.faq-answer');
    const chevron = item.querySelector('.faq-chevron');
    const isOpen = btn.getAttribute('aria-expanded') === 'true';

    // Close all others
    document.querySelectorAll('.faq-item').forEach(other => {
        if (other !== item) {
            other.querySelector('.faq-trigger').setAttribute('aria-expanded', 'false');
            other.querySelector('.faq-answer').style.maxHeight = '0';
            other.querySelector('.faq-chevron').style.transform = 'rotate(0deg)';
            other.querySelector('.faq-trigger').style.background = 'rgba(255,255,255,.04)';
        }
    });

    if (isOpen) {
        btn.setAttribute('aria-expanded', 'false');
        answer.style.maxHeight = '0';
        chevron.style.transform = 'rotate(0deg)';
        btn.style.background = 'rgba(255,255,255,.04)';
    } else {
        btn.setAttribute('aria-expanded', 'true');
        answer.style.maxHeight = answer.scrollHeight + 'px';
        chevron.style.transform = 'rotate(180deg)';
        btn.style.background = 'rgba(255,255,255,.08)';
    }
}

/* Animated Counters */
(function() {
    const counters = document.querySelectorAll('.counter');
    if (!counters.length) return;
    
    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(function(entry) {
            if (entry.isIntersecting) {
                const el = entry.target;
                const target = parseInt(el.dataset.target, 10);
                const suffix = el.dataset.suffix || '';
                const duration = 2000;
                const start = performance.now();
                
                function update(now) {
                    const elapsed = now - start;
                    const progress = Math.min(elapsed / duration, 1);
                    const eased = 1 - Math.pow(1 - progress, 3);
                    const current = Math.round(eased * target);
                    el.textContent = current + suffix;
                    if (progress < 1) requestAnimationFrame(update);
                }
                requestAnimationFrame(update);
                observer.unobserve(el);
            }
        });
    }, { threshold: 0.5 });

    counters.forEach(function(c) { observer.observe(c); });
})();

/* Mobile Sticky CTA — show after scrolling past hero */
(function() {
    const sticky = document.getElementById('mobile-sticky-cta');
    if (!sticky) return;
    
    const showAfter = 500;
    let visible = false;
    
    window.addEventListener('scroll', function() {
        const scrolled = window.scrollY > showAfter;
        if (scrolled && !visible) {
            sticky.style.transform = 'translateY(0)';
            visible = true;
        } else if (!scrolled && visible) {
            sticky.style.transform = 'translateY(100%)';
            visible = false;
        }
    }, { passive: true });
})();
</script>

<!-- Mobile Sticky CTA -->
<div id="mobile-sticky-cta" class="fixed bottom-0 left-0 right-0 z-50 lg:hidden" style="background: linear-gradient(135deg, #1E1B18, #28231E); border-top: 1px solid rgba(91,141,190,.2); padding: 12px 16px; transform: translateY(100%); transition: transform .3s ease; box-shadow: 0 -4px 20px rgba(0,0,0,.2);">
    <div class="flex items-center gap-3">
        <div class="flex-1 min-w-0">
            <div class="text-xs font-semibold truncate" style="color: rgba(255,255,255,.9);">Butuh bantuan perizinan?</div>
            <div class="text-[11px]" style="color: rgba(255,255,255,.5);">Konsultasi gratis &middot; Respon cepat</div>
        </div>
        @php
            $waStickyText = 'Halo, saya butuh konsultasi perizinan';
            $waStickyHref = $whatsappLink . (str_contains($whatsappLink, '?') ? '&' : '?') . 'text=' . rawurlencode($waStickyText);
        @endphp
        <a href="{{ $waStickyHref }}" target="_blank" rel="noopener"
           class="shrink-0 inline-flex items-center gap-2 px-5 py-2.5 rounded-full font-semibold text-sm transition-all duration-300" style="background: linear-gradient(90deg, #25D366, #128C7E); color: #fff;">
            <i class="fab fa-whatsapp"></i>
            <span>WhatsApp</span>
        </a>
    </div>
</div>

@endsection
