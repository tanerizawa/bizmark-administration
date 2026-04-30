@php
    $locale = $locale ?? app()->getLocale();
    $metrics = config('landing_metrics');
    $contact = (array) data_get($metrics, 'contact', []);
    $whatsappLink = $contact['whatsapp_link'] ?? 'https://wa.me/6283879602855';
    $phoneNumber = $contact['phone'] ?? '+62 838 7960 2855';
    $phoneHref = preg_replace('/\s+/', '', $phoneNumber);
    $primaryCtaRoute = route('landing.service-inquiry.create');
    $servicesIndexRoute = $locale === 'en' ? route('services.index.en') : route('services.index.id');
    $isEn = $locale === 'en';
@endphp

{{-- ────────────────────────────────────────────────
     HERO — Dark Tech Startup, Center-Aligned Single Column
     ──────────────────────────────────────────────── --}}
<section class="relative overflow-hidden section-v2" aria-labelledby="hero-title">

    {{-- Very subtle top glow --}}
    <div aria-hidden="true" class="absolute inset-x-0 top-0 h-px pointer-events-none"
         style="background: linear-gradient(90deg, transparent, rgba(59,130,246,.3), transparent);"></div>

    <div class="container relative text-center">

        {{-- Eyebrow --}}
        <div class="flex justify-center mb-5" data-aos="fade-up" data-aos-duration="600">
            <span class="eyebrow">{{ $isEn ? 'Permit Consultancy Since 2014' : 'Konsultan Perizinan Sejak 2014' }}</span>
        </div>

        {{-- Headline --}}
        <h1 id="hero-title" class="display-xl mx-auto mb-5 max-w-[780px]" data-aos="fade-up" data-aos-duration="700" data-aos-delay="100">
            @if($isEn)
                Navigate Indonesia's permit landscape
                <span class="text-blue-400">with precision.</span>
            @else
                Urus perizinan usaha Indonesia
                <span class="text-blue-400">dengan presisi.</span>
            @endif
        </h1>

        {{-- Lead paragraph --}}
        <p class="text-lg leading-relaxed mx-auto mb-8 max-w-[580px] text-gray-400"
           data-aos="fade-up" data-aos-duration="600" data-aos-delay="200">
            {{ $isEn
                ? 'AMDAL, UKL-UPL, B3 waste, PBG, SLF, NIB — one team, one SLA. Trusted by 138+ corporations across manufacturing, infrastructure, and energy.'
                : 'AMDAL, UKL-UPL, limbah B3, PBG, SLF, NIB — satu tim, satu SLA. Dipercaya 138+ korporasi manufaktur, infrastruktur, dan energi.' }}
        </p>

        {{-- CTAs --}}
        <div class="flex flex-wrap items-center justify-center gap-3 mb-10" data-aos="fade-up" data-aos-duration="600" data-aos-delay="300">
            <a href="{{ $primaryCtaRoute }}" class="btn btn-primary btn-lg"
               x-data
               @click="if(window.trackEvent) trackEvent('CTA','click','hero_primary')">
                <i class="fas fa-robot" aria-hidden="true"></i>
                <span>{{ $isEn ? 'Free AI Permit Check' : 'Cek Perizinan AI — Gratis' }}</span>
            </a>
            <a href="{{ $servicesIndexRoute }}" class="btn btn-ghost btn-lg"
               x-data
               @click="if(window.trackEvent) trackEvent('CTA','click','hero_secondary')">
                <span>{{ $isEn ? 'Explore Services' : 'Lihat Semua Layanan' }}</span>
                <i class="fas fa-arrow-right text-sm" aria-hidden="true"></i>
            </a>
        </div>

        {{-- Stat row --}}
        <div class="inline-flex flex-wrap items-center justify-center gap-6 pt-7 border-t border-white/10" data-aos="fade-up" data-aos-duration="600" data-aos-delay="400">
            <div class="flex flex-col items-center gap-0.5">
                <span class="stat-value">138<span class="text-blue-400 text-[.7em]">+</span></span>
                <span class="stat-label text-center">{{ $isEn ? 'Corporate clients' : 'Klien Korporat' }}</span>
            </div>
            <div class="w-px h-8 bg-white/10"></div>
            <div class="flex flex-col items-center gap-0.5">
                <span class="stat-value">96%</span>
                <span class="stat-label text-center">{{ $isEn ? 'On-time delivery' : 'Izin selesai tepat waktu' }}</span>
            </div>
            <div class="w-px h-8 bg-white/10"></div>
            <div class="flex flex-col items-center gap-0.5">
                <span class="stat-value">10<span class="text-[.65em] text-blue-400">+ yr</span></span>
                <span class="stat-label text-center">{{ $isEn ? 'Years of experience' : 'Tahun pengalaman' }}</span>
            </div>
            <div class="w-px h-8 bg-white/10"></div>
            <div class="flex flex-col items-center gap-0.5">
                <span class="stat-value text-blue-400"><i class="fas fa-certificate" aria-hidden="true"></i></span>
                <span class="stat-label text-center">ISO 9001:2015</span>
            </div>
        </div>

    </div>
</section>
