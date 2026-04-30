@php
    $locale = app()->getLocale();
    $landingConfig = config('landing');
    $metrics = config('landing_metrics');
    $contact = (array) data_get($metrics, 'contact', []);
    $whatsappLink = $contact['whatsapp_link'] ?? 'https://wa.me/6283879602855';
    $emailAddress = $contact['email'] ?? 'info@bizmark.id';
    $phoneNumber = $contact['phone'] ?? '+62 838 7960 2855';
    $phoneHref = preg_replace('/\s+/', '', $phoneNumber);

    $processSteps = (array) data_get($landingConfig, 'process_steps', []);
    $testimonials = (array) data_get($landingConfig, 'testimonials', []);
    $faqs = (array) data_get($landingConfig, 'faq', []);

    $primaryCtaRoute = route('landing.service-inquiry.create');
    $servicesIndexRoute = $locale === 'en' ? route('services.index.en') : route('services.index.id');
@endphp

<section class="relative overflow-hidden" aria-labelledby="home-hero-title">
    <div class="absolute inset-0">
        <picture>
            <source srcset="/images/landing/hero-1400.webp" type="image/webp" media="(min-width:1024px)">
            <source srcset="/images/landing/hero-1200.webp" type="image/webp" media="(min-width:640px)">
            <source srcset="/images/landing/hero-800.webp" type="image/webp">
            <img src="/images/landing/hero-1200.jpg" alt="" class="absolute inset-0 w-full h-full object-cover" fetchpriority="high" aria-hidden="true">
        </picture>
        <div class="absolute inset-0 bg-gradient-to-b from-slate-950/45 via-slate-950/70 to-slate-950/95"></div>
    </div>

    <div class="container-wide relative z-10 pt-28 pb-16 md:pt-32 md:pb-24">
        <div class="grid lg:grid-cols-12 gap-10 items-end">
            <div class="lg:col-span-7">
                <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full text-xs font-semibold uppercase tracking-widest mb-6 border border-white/20 bg-white/10 text-white/90 backdrop-blur">
                    <span class="relative flex h-2 w-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full opacity-75" style="background:var(--color-accent);"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2" style="background:var(--color-accent);"></span>
                    </span>
                    {{ __('landing.hero.badge') }}
                </div>

                <h1 id="home-hero-title" class="text-white mb-5" style="font-size:clamp(2.4rem,5vw,4.25rem);font-weight:850;line-height:1.06;letter-spacing:-.04em;">
                    {{ __('landing.hero.title') }}
                </h1>

                <p class="text-lg md:text-xl leading-relaxed mb-8 max-w-2xl text-white/85">
                    {{ __('landing.hero.subtitle') }}
                </p>

                <div class="flex flex-col sm:flex-row gap-3 mb-6">
                    <a href="{{ $primaryCtaRoute }}" class="btn btn-secondary btn-lg">
                        <i class="fas fa-calendar-check"></i>
                        {{ __('landing.hero.cta_primary') }}
                    </a>
                    <a href="{{ $servicesIndexRoute }}" class="btn btn-ghost btn-lg">
                        <i class="fas fa-layer-group"></i>
                        {{ __('landing.hero.cta_secondary') }}
                    </a>
                </div>

                <div class="flex flex-wrap items-center gap-x-6 gap-y-2 text-sm text-white/75">
                    <span class="inline-flex items-center gap-2">
                        <i class="fas fa-shield-halved"></i>
                        {{ __('landing.hero.trust_badge') }}
                    </span>
                    <span class="inline-flex items-center gap-2">
                        <i class="fas fa-certificate"></i>
                        ISO
                    </span>
                    <span class="inline-flex items-center gap-2">
                        <i class="fas fa-map-marked-alt"></i>
                        {{ $locale === 'en' ? 'Nationwide coverage' : 'Cakupan se-Indonesia' }}
                    </span>
                </div>
            </div>

            <div class="lg:col-span-5">
                <div class="rounded-2xl border border-white/15 bg-white/10 backdrop-blur p-6 md:p-7 shadow-xl">
                    <div class="flex items-start justify-between gap-4 mb-5">
                        <div>
                            <div class="text-xs font-semibold uppercase tracking-widest text-white/70 mb-1">
                                {{ $locale === 'en' ? 'Quick start' : 'Mulai cepat' }}
                            </div>
                            <h2 class="text-white text-xl font-bold leading-tight mb-0">
                                {{ $locale === 'en' ? 'Get a clear permit roadmap' : 'Dapatkan peta jalan perizinan' }}
                            </h2>
                        </div>
                        <div class="w-11 h-11 rounded-xl flex items-center justify-center bg-white/10 border border-white/10">
                            <i class="fas fa-compass text-white"></i>
                        </div>
                    </div>

                    <div class="grid gap-3">
                        <a href="{{ route('consultation.index') }}" class="group flex items-start gap-4 rounded-xl bg-white/10 hover:bg-white/15 border border-white/10 px-4 py-4 transition">
                            <div class="w-10 h-10 rounded-lg flex items-center justify-center shrink-0" style="background:rgba(249,115,22,.18);">
                                <i class="fas fa-calculator" style="color:var(--color-secondary);"></i>
                            </div>
                            <div class="min-w-0">
                                <div class="text-white font-semibold leading-snug">
                                    {{ $locale === 'en' ? 'AI Cost Estimate' : 'Estimasi Biaya (AI)' }}
                                </div>
                                <div class="text-sm text-white/70 leading-relaxed">
                                    {{ $locale === 'en' ? 'Pick KBLI, get a fast cost & timeline estimate.' : 'Pilih KBLI, terima estimasi biaya & durasi secara instan.' }}
                                </div>
                            </div>
                            <i class="fas fa-arrow-right text-white/70 mt-1 transition-transform group-hover:translate-x-0.5"></i>
                        </a>

                        <a href="{{ route('polygon.shp.index') }}" class="group flex items-start gap-4 rounded-xl bg-white/10 hover:bg-white/15 border border-white/10 px-4 py-4 transition">
                            <div class="w-10 h-10 rounded-lg flex items-center justify-center shrink-0" style="background:rgba(34,197,94,.18);">
                                <i class="fas fa-draw-polygon" style="color:var(--color-success);"></i>
                            </div>
                            <div class="min-w-0">
                                <div class="text-white font-semibold leading-snug">
                                    Polygon SHP Maker
                                </div>
                                <div class="text-sm text-white/70 leading-relaxed">
                                    {{ $locale === 'en' ? 'Create SHP files for OSS-RBA submissions.' : 'Buat file SHP untuk kebutuhan upload OSS-RBA.' }}
                                </div>
                            </div>
                            <i class="fas fa-arrow-right text-white/70 mt-1 transition-transform group-hover:translate-x-0.5"></i>
                        </a>

                        <a href="{{ $primaryCtaRoute }}" class="group flex items-start gap-4 rounded-xl bg-white/10 hover:bg-white/15 border border-white/10 px-4 py-4 transition">
                            <div class="w-10 h-10 rounded-lg flex items-center justify-center shrink-0" style="background:rgba(14,165,233,.18);">
                                <i class="fas fa-robot" style="color:var(--color-accent);"></i>
                            </div>
                            <div class="min-w-0">
                                <div class="text-white font-semibold leading-snug">
                                    {{ $locale === 'en' ? 'Free Permit Analysis' : 'Analisis Perizinan Gratis' }}
                                </div>
                                <div class="text-sm text-white/70 leading-relaxed">
                                    {{ $locale === 'en' ? 'Tell us your business context and get a recommended permit list.' : 'Ceritakan konteks usaha Anda dan dapatkan rekomendasi izin.' }}
                                </div>
                            </div>
                            <i class="fas fa-arrow-right text-white/70 mt-1 transition-transform group-hover:translate-x-0.5"></i>
                        </a>
                    </div>

                    <div class="mt-5 pt-5 border-t border-white/10 flex flex-col sm:flex-row gap-3">
                        <a href="{{ $whatsappLink }}" target="_blank" rel="noopener noreferrer" class="btn btn-success w-full sm:w-auto">
                            <i class="fab fa-whatsapp"></i> WhatsApp
                        </a>
                        <a href="mailto:{{ $emailAddress }}" class="btn btn-outline-primary w-full sm:w-auto" style="border-color:rgba(255,255,255,.3);color:#fff;">
                            <i class="fas fa-envelope"></i> {{ $locale === 'en' ? 'Email' : 'Email' }}
                        </a>
                        <a href="tel:{{ $phoneHref }}" class="btn btn-outline-primary w-full sm:w-auto" style="border-color:rgba(255,255,255,.3);color:#fff;">
                            <i class="fas fa-phone"></i> {{ $locale === 'en' ? 'Call' : 'Telepon' }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="section-sm" aria-label="{{ $locale === 'en' ? 'Key metrics' : 'Ringkasan metrik' }}" style="background:var(--surface-warm);border-top:1px solid var(--border-light);border-bottom:1px solid var(--border-light);">
    <div class="container-wide">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-8 md:gap-4">
            <div class="text-center">
                <div class="text-4xl md:text-5xl font-extrabold tracking-tight mb-1" style="color:var(--text-primary);">
                    <span class="counter" data-target="138" data-suffix="+">138+</span>
                </div>
                <div class="text-sm font-semibold mb-0.5" style="color:var(--text-primary);">{{ __('landing.stats.clients.label') }}</div>
                <div class="text-xs" style="color:var(--text-tertiary);">{{ __('landing.stats.clients.description') }}</div>
            </div>
            <div class="text-center">
                <div class="text-4xl md:text-5xl font-extrabold tracking-tight mb-1" style="color:var(--text-primary);">
                    <span class="counter" data-target="10" data-suffix="+">10+</span>
                </div>
                <div class="text-sm font-semibold mb-0.5" style="color:var(--text-primary);">{{ __('landing.stats.experience.label') }}</div>
                <div class="text-xs" style="color:var(--text-tertiary);">{{ __('landing.stats.experience.description') }}</div>
            </div>
            <div class="text-center">
                <div class="text-4xl md:text-5xl font-extrabold tracking-tight mb-1" style="color:var(--text-primary);">
                    <span class="counter" data-target="96" data-suffix="%">96%</span>
                </div>
                <div class="text-sm font-semibold mb-0.5" style="color:var(--text-primary);">{{ __('landing.stats.success_rate.label') }}</div>
                <div class="text-xs" style="color:var(--text-tertiary);">{{ __('landing.stats.success_rate.description') }}</div>
            </div>
            <div class="text-center">
                <div class="text-4xl md:text-5xl font-extrabold tracking-tight mb-1" style="color:var(--color-success);">
                    <i class="fas fa-certificate"></i>
                </div>
                <div class="text-sm font-semibold mb-0.5" style="color:var(--text-primary);">{{ __('landing.stats.iso_certified.label') }}</div>
                <div class="text-xs" style="color:var(--text-tertiary);">{{ __('landing.stats.iso_certified.description') }}</div>
            </div>
        </div>
    </div>
</section>

<section id="services" class="section" aria-labelledby="services-heading">
    <div class="container-wide">
        <div class="text-center mb-14">
            <span class="section-badge mb-4">{{ __('landing.services.badge') }}</span>
            <h2 id="services-heading" class="section-title">{{ __('landing.services.title') }}</h2>
            <p class="section-description mx-auto">{{ __('landing.services.subtitle') }}</p>
        </div>

        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($services as $slug => $service)
                @break($loop->index >= 6)
                <article class="card group">
                    <div class="flex items-start justify-between mb-5">
                        <div class="w-14 h-14 rounded-xl flex items-center justify-center" style="background:{{ $service['color'] }}15;">
                            <i class="fas {{ $service['icon'] }} text-2xl" style="color:{{ $service['color'] }};"></i>
                        </div>
                        @if(isset($service['featured']) && $service['featured'])
                            <span class="badge-featured">{{ __('landing.services.popular') }}</span>
                        @endif
                    </div>

                    <h3 class="text-lg font-bold mb-2 card-title" style="color:var(--text-primary);">{{ $service['title'] }}</h3>
                    <p class="text-sm leading-relaxed mb-5" style="color:var(--text-secondary);">{{ $service['short_description'] }}</p>

                    <a href="{{ $locale === 'en' ? route('services.show.en', $slug) : route('services.show.id', $slug) }}" class="link-primary text-sm inline-flex items-center group" aria-label="{{ __('landing.services.learn_more') }} - {{ $service['title'] }}">
                        {{ __('landing.services.learn_more') }}
                        <i class="fas fa-arrow-right ml-2 transition-transform group-hover:translate-x-1"></i>
                    </a>
                </article>
            @endforeach
        </div>

        <div class="text-center mt-10">
            <a href="{{ $servicesIndexRoute }}" class="btn btn-primary">
                <i class="fas fa-th-list mr-2"></i>
                <span>{{ __('landing.services.show_more') }}</span>
            </a>
        </div>
    </div>
</section>

<section class="section-sm" style="background:var(--surface-warm);border-top:1px solid var(--border-light);border-bottom:1px solid var(--border-light);" aria-labelledby="tools-heading">
    <div class="container-wide">
        <div class="text-center mb-10">
            <span class="section-badge mb-3" style="background:linear-gradient(135deg,#10b981,#0d9488);color:#fff;border-color:transparent;">{{ $locale === 'en' ? 'NEW' : 'FITUR BARU' }}</span>
            <h2 id="tools-heading" class="section-title">{{ $locale === 'en' ? 'Free Digital Tools' : 'Alat Digital Gratis' }}</h2>
            <p class="section-description mx-auto">{{ $locale === 'en' ? 'Practical tools to speed up your OSS-RBA preparation.' : 'Tools praktis untuk membantu persiapan OSS-RBA dan perizinan Anda.' }}</p>
        </div>

        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6 max-w-5xl mx-auto">
            <a href="{{ route('polygon.shp.index') }}" class="magazine-card group flex items-start gap-4 p-5 hover:shadow-lg transition-all">
                <div class="flex-shrink-0 w-12 h-12 rounded-xl flex items-center justify-center" style="background:linear-gradient(135deg,#10b981,#0d9488);">
                    <i class="fas fa-draw-polygon text-xl text-white"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 mb-1">
                        <h3 class="text-base font-bold" style="color:var(--text-primary);">Polygon SHP Maker</h3>
                        <span class="px-2 py-0.5 text-[10px] font-bold rounded" style="background:#d1fae5;color:#059669;">{{ $locale === 'en' ? 'FREE' : 'GRATIS' }}</span>
                    </div>
                    <p class="text-sm mb-2" style="color:var(--text-secondary);">{{ $locale === 'en' ? 'Create SHP files with an interactive map for OSS-RBA.' : 'Buat file Shapefile (.shp) untuk upload OSS-RBA dengan peta interaktif.' }}</p>
                    <span class="link-primary text-sm inline-flex items-center group-hover:gap-2 transition-all">
                        {{ $locale === 'en' ? 'Create SHP' : 'Buat SHP' }} <i class="fas fa-arrow-right ml-1"></i>
                    </span>
                </div>
            </a>

            <a href="{{ route('consultation.index') }}" class="magazine-card group flex items-start gap-4 p-5 hover:shadow-lg transition-all">
                <div class="flex-shrink-0 w-12 h-12 rounded-xl flex items-center justify-center" style="background:linear-gradient(135deg,#8b5cf6,#6366f1);">
                    <i class="fas fa-calculator text-xl text-white"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 mb-1">
                        <h3 class="text-base font-bold" style="color:var(--text-primary);">{{ $locale === 'en' ? 'Cost Estimate' : 'Estimasi Biaya' }}</h3>
                        <span class="px-2 py-0.5 text-[10px] font-bold rounded" style="background:#ede9fe;color:#7c3aed;"><i class="fas fa-robot mr-0.5"></i>AI</span>
                    </div>
                    <p class="text-sm mb-2" style="color:var(--text-secondary);">{{ $locale === 'en' ? 'Estimate cost & timeline based on KBLI.' : 'Estimasi biaya perizinan dengan AI. Pilih KBLI, terima hasil instan.' }}</p>
                    <span class="link-primary text-sm inline-flex items-center group-hover:gap-2 transition-all">
                        {{ $locale === 'en' ? 'Estimate now' : 'Hitung Biaya' }} <i class="fas fa-arrow-right ml-1"></i>
                    </span>
                </div>
            </a>

            <a href="{{ route('client.services.index') }}" class="magazine-card group flex items-start gap-4 p-5 hover:shadow-lg transition-all">
                <div class="flex-shrink-0 w-12 h-12 rounded-xl flex items-center justify-center" style="background:linear-gradient(135deg,#0ea5e9,#0284c7);">
                    <i class="fas fa-search-dollar text-xl text-white"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 mb-1">
                        <h3 class="text-base font-bold" style="color:var(--text-primary);">{{ $locale === 'en' ? 'Permit Analysis' : 'Analisis Perizinan' }}</h3>
                        <span class="px-2 py-0.5 text-[10px] font-bold rounded" style="background:#e0f2fe;color:#0284c7;"><i class="fas fa-robot mr-0.5"></i>AI</span>
                    </div>
                    <p class="text-sm mb-2" style="color:var(--text-secondary);">{{ $locale === 'en' ? 'AI helps map permit requirements from 1000+ KBLI services.' : 'AI menganalisis kebutuhan izin usaha Anda dari 1000+ layanan KBLI.' }}</p>
                    <span class="link-primary text-sm inline-flex items-center group-hover:gap-2 transition-all">
                        {{ $locale === 'en' ? 'Analyze' : 'Analisis Sekarang' }} <i class="fas fa-arrow-right ml-1"></i>
                    </span>
                </div>
            </a>
        </div>
    </div>
</section>

<section id="process" class="section" style="background:var(--surface-cool);" aria-labelledby="process-heading">
    <div class="container-wide">
        <div class="text-center mb-14">
            <span class="section-badge mb-4">{{ __('landing.process.badge') }}</span>
            <h2 id="process-heading" class="section-title">{{ __('landing.process.title') }}</h2>
            <p class="section-description mx-auto">{{ __('landing.process.subtitle') }}</p>
        </div>

        <ol class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($processSteps as $step)
                <li class="card">
                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 rounded-xl flex items-center justify-center" style="background:{{ $step['color'] }}1a;">
                            <i class="{{ $step['icon'] }} text-xl" style="color:{{ $step['color'] }};"></i>
                        </div>
                        <div class="min-w-0">
                            <h3 class="text-base font-bold mb-1" style="color:var(--text-primary);">
                                {{ data_get($step, "title.{$locale}") ?? data_get($step, 'title.id') }}
                            </h3>
                            <p class="text-sm leading-relaxed mb-0" style="color:var(--text-secondary);">
                                {{ data_get($step, "body.{$locale}") ?? data_get($step, 'body.id') }}
                            </p>
                        </div>
                    </div>
                </li>
            @endforeach
        </ol>
    </div>
</section>

<section id="about" class="section" aria-labelledby="about-heading">
    <div class="container-wide">
        <div class="grid lg:grid-cols-2 gap-14 items-center">
            <div>
                <span class="section-badge mb-4">{{ __('landing.about.badge') }}</span>
                <h2 id="about-heading" class="section-title mb-5">{{ __('landing.about.title') }}</h2>
                <p class="text-lg leading-relaxed mb-4" style="color:var(--text-secondary);">{{ __('landing.about.description_1') }}</p>
                <p class="leading-relaxed mb-8" style="color:var(--text-secondary);">{{ __('landing.about.description_2') }}</p>

                <div class="grid sm:grid-cols-2 gap-5 mb-8">
                    <div class="flex items-start gap-3">
                        <div class="flex-shrink-0 w-11 h-11 rounded-lg flex items-center justify-center" style="background:rgba(15,23,42,.06);">
                            <i class="fas fa-certificate text-lg" style="color:var(--color-primary);"></i>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold mb-0.5" style="color:var(--text-primary);">{{ __('landing.about.iso_title') }}</h3>
                            <p class="text-xs leading-relaxed mb-0" style="color:var(--text-tertiary);">{{ __('landing.about.iso_desc') }}</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <div class="flex-shrink-0 w-11 h-11 rounded-lg flex items-center justify-center" style="background:rgba(22,163,74,.08);">
                            <i class="fas fa-users text-lg" style="color:var(--color-success);"></i>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold mb-0.5" style="color:var(--text-primary);">{{ __('landing.about.expert_title') }}</h3>
                            <p class="text-xs leading-relaxed mb-0" style="color:var(--text-tertiary);">{{ __('landing.about.expert_desc') }}</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <div class="flex-shrink-0 w-11 h-11 rounded-lg flex items-center justify-center" style="background:rgba(15,23,42,.06);">
                            <i class="fas fa-handshake text-lg" style="color:var(--color-primary);"></i>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold mb-0.5" style="color:var(--text-primary);">{{ __('landing.about.clients_title') }}</h3>
                            <p class="text-xs leading-relaxed mb-0" style="color:var(--text-tertiary);">{{ __('landing.about.clients_desc') }}</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <div class="flex-shrink-0 w-11 h-11 rounded-lg flex items-center justify-center" style="background:rgba(234,179,8,.1);">
                            <i class="fas fa-globe text-lg" style="color:var(--color-warning);"></i>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold mb-0.5" style="color:var(--text-primary);">{{ __('landing.about.nationwide_title') }}</h3>
                            <p class="text-xs leading-relaxed mb-0" style="color:var(--text-tertiary);">{{ __('landing.about.nationwide_desc') }}</p>
                        </div>
                    </div>
                </div>

                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('contact.index') }}" class="btn btn-primary"><i class="fas fa-phone"></i> {{ $locale === 'en' ? 'Contact us' : 'Hubungi Kami' }}</a>
                    <a href="{{ $servicesIndexRoute }}" class="btn btn-outline-primary"><i class="fas fa-layer-group"></i> {{ __('landing.about.cta_services') }}</a>
                </div>
            </div>

            <div class="relative">
                <div class="magazine-img" style="aspect-ratio:4/3;">
                    <picture>
                        <source srcset="/images/landing/hero-modern-1400.webp" type="image/webp">
                        <img src="/images/landing/hero-modern-1400.jpg" alt="{{ __('landing.about.years_title') }}" loading="lazy" width="700" height="525">
                    </picture>
                    <div class="magazine-img-caption">
                        <h3 class="text-xl font-bold mb-1">{{ __('landing.about.years_title') }}</h3>
                        <p class="text-sm opacity-90 mb-0">{{ __('landing.about.years_desc') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@if(count($testimonials) > 0)
<section class="section" style="background:var(--surface-warm);" aria-labelledby="testimonials-heading">
    <div class="container-wide">
        <div class="text-center mb-12">
            <h2 id="testimonials-heading" class="section-title">{{ __('landing.testimonials.title') }}</h2>
            <p class="section-description mx-auto">{{ __('landing.testimonials.subtitle') }}</p>
        </div>

        <div class="grid md:grid-cols-3 gap-6">
            @foreach(array_slice($testimonials, 0, 3) as $t)
                <figure class="card">
                    <div class="flex items-center justify-between mb-4">
                        <div class="text-sm font-semibold" style="color:var(--text-primary);">{{ $t['company'] ?? 'Client' }}</div>
                        <div class="text-amber-400 text-sm" aria-label="{{ ($t['rating'] ?? 5) }} stars">
                            @for($i = 0; $i < ($t['rating'] ?? 5); $i++)
                                <i class="fas fa-star"></i>
                            @endfor
                        </div>
                    </div>
                    <blockquote class="text-sm leading-relaxed mb-5" style="color:var(--text-secondary);">“{{ $t['text'] ?? '' }}”</blockquote>
                    <figcaption class="text-sm" style="color:var(--text-tertiary);">
                        <span class="font-semibold" style="color:var(--text-primary);">{{ $t['name'] ?? '' }}</span>
                        <span> — {{ $t['position'] ?? '' }}</span>
                    </figcaption>
                </figure>
            @endforeach
        </div>
    </div>
</section>
@endif

@if(count($faqs) > 0)
<section id="faq" class="section" aria-labelledby="faq-heading">
    <div class="container-wide">
        <div class="text-center mb-12">
            <h2 id="faq-heading" class="section-title">{{ __('landing.faq.title') }}</h2>
            <p class="section-description mx-auto">{{ __('landing.faq.subtitle') }}</p>
        </div>

        <div class="max-w-3xl mx-auto space-y-3">
            @foreach($faqs as $faq)
                <details class="faq-item">
                    <summary class="faq-toggle">
                        <span>{{ data_get($faq, "question.{$locale}") ?? data_get($faq, 'question.id') }}</span>
                        <i class="fas fa-chevron-down" aria-hidden="true"></i>
                    </summary>
                    <div class="faq-content">
                        <div class="faq-content-inner">
                            {{ data_get($faq, "answer.{$locale}") ?? data_get($faq, 'answer.id') }}
                        </div>
                    </div>
                </details>
            @endforeach
        </div>
    </div>
</section>
@endif

<section class="section" style="background:var(--surface-dark);" aria-labelledby="cta-heading">
    <div class="container-wide text-center">
        <h2 id="cta-heading" class="text-white mb-4" style="font-size:clamp(1.75rem,3.5vw,2.5rem);font-weight:750;letter-spacing:-.03em;">{{ __('landing.cta.title') }}</h2>
        <p class="text-lg mb-10 mx-auto" style="color:rgba(255,255,255,.75);max-width:46ch;">{{ __('landing.cta.subtitle') }}</p>
        <div class="flex flex-col sm:flex-row gap-3 justify-center">
            <a href="{{ $primaryCtaRoute }}" class="btn btn-secondary btn-lg">
                <i class="fas fa-clipboard-list"></i> {{ __('landing.cta.button_primary') }}
            </a>
            <a href="mailto:{{ $emailAddress }}" class="btn btn-ghost btn-lg">
                <i class="fas fa-envelope"></i> {{ __('landing.cta.button_email') }}
            </a>
            <a href="{{ $whatsappLink }}" class="btn btn-success btn-lg" target="_blank" rel="noopener noreferrer">
                <i class="fab fa-whatsapp"></i> {{ __('landing.cta.whatsapp') }}
            </a>
        </div>
    </div>
</section>
