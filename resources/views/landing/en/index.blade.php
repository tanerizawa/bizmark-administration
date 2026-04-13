<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    
    <!-- Primary Meta Tags -->
    <title>{{ __('landing.meta.title') }}</title>
    <meta name="title" content="{{ __('landing.meta.title') }}">
    <meta name="description" content="{{ __('landing.meta.description') }}">
    <meta name="keywords" content="{{ __('landing.meta.keywords') }}">
    <meta name="robots" content="index, follow">
    <meta name="author" content="Bizmark.ID">
    <meta name="language" content="English">
    <link rel="canonical" href="https://bizmark.id/en">
    
    <!-- Hreflang Tags for SEO -->
    <link rel="alternate" hreflang="id" href="https://bizmark.id/">
    <link rel="alternate" hreflang="en" href="https://bizmark.id/en">
    <link rel="alternate" hreflang="x-default" href="https://bizmark.id/">
    
    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://bizmark.id/en">
    <meta property="og:title" content="{{ __('landing.meta.og_title') }}">
    <meta property="og:description" content="{{ __('landing.meta.og_description') }}">
    <meta property="og:image" content="https://bizmark.id/images/og-image-en.jpg">
    <meta property="og:locale" content="en_US">
    <meta property="og:locale:alternate" content="id_ID">
    
    <!-- Twitter -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:url" content="https://bizmark.id/en">
    <meta name="twitter:title" content="{{ __('landing.meta.title') }}">
    <meta name="twitter:description" content="{{ __('landing.meta.description') }}">
    <meta name="twitter:image" content="https://bizmark.id/images/og-image-en.jpg">
    
    <!-- Schema.org Structured Data for SEO -->
    <script type="application/ld+json">
    {
        "@@context": "https://schema.org",
        "@type": "LocalBusiness",
        "@id": "https://bizmark.id/#organization",
        "name": "Bizmark.ID - PT Cangah Pajaratan Mandiri",
        "alternateName": "Bizmark Indonesia",
        "description": "Trusted business permit consultant in Indonesia. Specialists in AMDAL, UKL-UPL, environmental permits, and PMA/PT establishment.",
        "url": "https://bizmark.id",
        "logo": "https://bizmark.id/images/logo.png",
        "image": "https://bizmark.id/images/og-image-en.jpg",
        "telephone": "{{ preg_replace('/\\s+/', '', data_get(config('landing_metrics'), 'contact.phone', '+62 838 7960 2855')) }}",
        "email": "{{ data_get(config('landing_metrics'), 'contact.email', 'info@bizmark.id') }}",
        "address": {
            "@type": "PostalAddress",
            "addressLocality": "Karawang",
            "addressRegion": "Jawa Barat",
            "addressCountry": "ID"
        },
        "geo": {
            "@type": "GeoCoordinates",
            "latitude": "-6.3227",
            "longitude": "107.3376"
        },
        "openingHoursSpecification": {
            "@type": "OpeningHoursSpecification",
            "dayOfWeek": ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday"],
            "opens": "08:00",
            "closes": "17:00"
        },
        "priceRange": "$$",
        "currenciesAccepted": "IDR",
        "paymentAccepted": "Cash, Bank Transfer",
        "areaServed": {
            "@type": "Country",
            "name": "Indonesia"
        },
        "sameAs": [
            "https://www.linkedin.com/company/bizmark-id",
            "https://www.facebook.com/bizmark.id",
            "https://www.instagram.com/bizmark.id"
        ],
        "hasOfferCatalog": {
            "@type": "OfferCatalog",
            "name": "Business Permit Services",
            "itemListElement": [
                {
                    "@type": "Offer",
                    "itemOffered": {
                        "@type": "Service",
                        "name": "PMA Company Establishment",
                        "description": "Foreign investment company establishment services in Indonesia"
                    }
                },
                {
                    "@type": "Offer",
                    "itemOffered": {
                        "@type": "Service",
                        "name": "AMDAL Document",
                        "description": "Environmental Impact Assessment document preparation"
                    }
                },
                {
                    "@type": "Offer",
                    "itemOffered": {
                        "@type": "Service",
                        "name": "UKL-UPL",
                        "description": "Environmental Management and Monitoring document preparation"
                    }
                }
            ]
        }
    }
    </script>
    
    <!-- BreadcrumbList Schema -->
    <script type="application/ld+json">
    {
        "@@context": "https://schema.org",
        "@type": "BreadcrumbList",
        "itemListElement": [
            {
                "@type": "ListItem",
                "position": 1,
                "name": "Home",
                "item": "https://bizmark.id/en"
            }
        ]
    }
    </script>
    
    <!-- Favicons -->
    <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">
    
    <!-- Performance: DNS Prefetch & Preconnect -->
    <link rel="dns-prefetch" href="//cdnjs.cloudflare.com">
    <link rel="preconnect" href="https://cdnjs.cloudflare.com" crossorigin>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://www.googletagmanager.com">
    
    <!-- Performance: Preload Critical Resources -->
    <link rel="preload" as="image" href="/images/landing/hero-1200.jpg">
    
    <!-- Tailwind CSS (compiled) -->
    @vite('resources/css/landing.css')
    
    <!-- Google Fonts: Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- FontAwesome (async) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" media="print" onload="this.media='all'">
    <noscript><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"></noscript>
    
    @include('landing.partials.critical-css')
    @include('landing.partials.styles-modern')
</head>
<body class="font-sans antialiased bg-white text-gray-900">

<!-- Skip to main content link for accessibility -->
<a href="#main-content" class="skip-link">Skip to main content</a>

@include('landing.partials.navbar')
@include('landing.partials.mobile-menu')

<!-- HERO — Magazine Editorial -->
<section id="main-content" class="relative min-h-[90vh] flex items-end overflow-hidden" style="padding-top:5rem;">
    <picture>
        <source srcset="/images/landing/hero-1400.webp" type="image/webp" media="(min-width:1024px)">
        <source srcset="/images/landing/hero-1200.webp" type="image/webp" media="(min-width:640px)">
        <source srcset="/images/landing/hero-800.webp" type="image/webp">
        <img src="/images/landing/hero-1200.jpg" alt="" class="absolute inset-0 w-full h-full object-cover" loading="eager" aria-hidden="true">
    </picture>
    <div class="absolute inset-0" style="background:linear-gradient(180deg,rgba(15,23,42,.35) 0%,rgba(15,23,42,.75) 55%,rgba(15,23,42,.95) 100%);"></div>

    <div class="container-wide relative z-10 pb-16 md:pb-24">
        <div class="max-w-3xl">
            <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full text-xs font-bold uppercase tracking-widest mb-6 border" style="background:rgba(255,255,255,.08);border-color:rgba(255,255,255,.18);color:rgba(255,255,255,.9);backdrop-filter:blur(4px);">
                <span class="relative flex h-2 w-2">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full opacity-75" style="background:var(--apple-green);"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2" style="background:var(--apple-green);"></span>
                </span>
                {{ __('landing.hero.badge') }}
            </div>

            <h1 class="text-white mb-6" style="font-size:clamp(2.5rem,5.5vw,4.25rem);font-weight:800;line-height:1.08;letter-spacing:-.04em;">
                {{ __('landing.hero.title') }}
            </h1>

            <p class="text-lg md:text-xl leading-relaxed mb-10" style="color:rgba(255,255,255,.88);max-width:42ch;">
                {{ __('landing.hero.subtitle') }}
            </p>

            <div class="flex flex-col sm:flex-row gap-3 mb-8">
                <a href="{{ route('pma.inquiry.create') }}" class="btn btn-secondary btn-lg">
                    <i class="fas fa-calendar-check"></i>
                    {{ __('landing.hero.cta_primary') }}
                </a>
                <a href="#services" class="btn btn-ghost btn-lg">
                    <i class="fas fa-arrow-down"></i>
                    {{ __('landing.hero.cta_secondary') }}
                </a>
            </div>

            <p class="text-sm" style="color:rgba(255,255,255,.7);">
                <i class="fas fa-shield-halved mr-1.5"></i>{{ __('landing.hero.trust_badge') }}
            </p>
        </div>
    </div>
</section>

<!-- STATS — Editorial Numbers Strip -->
<section id="stats" class="section-sm" style="background:var(--surface-warm);border-top:1px solid var(--border-light);border-bottom:1px solid var(--border-light);">
    <div class="container-wide">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-8 md:gap-4">
            <div class="text-center animate-fade-in">
                <div class="text-4xl md:text-5xl font-extrabold tracking-tight mb-1" style="color:var(--color-primary);">
                    <span class="counter" data-target="138" data-suffix="+">0</span>
                </div>
                <div class="text-sm font-semibold mb-0.5" style="color:var(--text-primary);">{{ __('landing.stats.clients.label') }}</div>
                <div class="text-xs" style="color:var(--text-tertiary);">{{ __('landing.stats.clients.description') }}</div>
            </div>
            <div class="text-center animate-fade-in delay-100">
                <div class="text-4xl md:text-5xl font-extrabold tracking-tight mb-1" style="color:var(--color-primary);">
                    <span class="counter" data-target="10" data-suffix="+">0</span>
                </div>
                <div class="text-sm font-semibold mb-0.5" style="color:var(--text-primary);">{{ __('landing.stats.experience.label') }}</div>
                <div class="text-xs" style="color:var(--text-tertiary);">{{ __('landing.stats.experience.description') }}</div>
            </div>
            <div class="text-center animate-fade-in delay-200">
                <div class="text-4xl md:text-5xl font-extrabold tracking-tight mb-1" style="color:var(--color-primary);">
                    <span class="counter" data-target="96" data-suffix="%">0</span>
                </div>
                <div class="text-sm font-semibold mb-0.5" style="color:var(--text-primary);">{{ __('landing.stats.success_rate.label') }}</div>
                <div class="text-xs" style="color:var(--text-tertiary);">{{ __('landing.stats.success_rate.description') }}</div>
            </div>
            <div class="text-center animate-fade-in delay-300">
                <div class="text-4xl md:text-5xl font-extrabold tracking-tight mb-1" style="color:var(--color-success);">
                    <i class="fas fa-certificate"></i>
                </div>
                <div class="text-sm font-semibold mb-0.5" style="color:var(--text-primary);">{{ __('landing.stats.iso_certified.label') }}</div>
                <div class="text-xs" style="color:var(--text-tertiary);">{{ __('landing.stats.iso_certified.description') }}</div>
            </div>
        </div>
    </div>
</section>

<!-- SERVICES — Magazine Card Grid -->
<section id="services" class="section" aria-labelledby="services-heading">
    <div class="container-wide">
        <div class="text-center mb-14 animate-fade-in">
            <span class="section-badge mb-4">{{ __('landing.services.badge') }}</span>
            <h2 id="services-heading" class="section-title">{{ __('landing.services.title') }}</h2>
            <p class="section-description mx-auto">{{ __('landing.services.subtitle') }}</p>
        </div>

        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6" id="services-grid">
            @foreach($services as $slug => $service)
            <article class="group card animate-fade-in{{ $loop->index >= 6 ? ' service-card-extra hidden' : '' }}">
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

                @if(isset($service['pricing']))
                <div class="flex items-center justify-between mb-5 pb-4" style="border-bottom:1px solid var(--border-light);">
                    <div>
                        <div class="text-xs" style="color:var(--text-tertiary);">{{ __('landing.services.starting_from') }}</div>
                        <div class="text-base font-bold" style="color:var(--color-primary);">{{ $service['pricing']['display'] }}</div>
                    </div>
                    <div class="text-right">
                        <div class="text-xs" style="color:var(--text-tertiary);">{{ __('landing.services.duration') }}</div>
                        <div class="text-sm font-semibold" style="color:var(--text-primary);">{{ $service['duration'] }}</div>
                    </div>
                </div>
                @endif

                <a href="{{ route('services.show.en', $slug) }}" class="link-primary text-sm inline-flex items-center group" aria-label="{{ __('landing.services.learn_more') }} - {{ $service['title'] }}">
                    {{ __('landing.services.learn_more') }}
                    <i class="fas fa-arrow-right ml-2 transition-transform group-hover:translate-x-1"></i>
                </a>
            </article>
            @endforeach
        </div>

        <div class="text-center mt-10">
            <a href="{{ route('services.index.en') }}" class="btn btn-primary">
                <i class="fas fa-th-list mr-2"></i>
                <span>{{ __('landing.services.show_more') }}</span>
            </a>
        </div>
    </div>
</section>

<!-- PROCESS — Editorial Timeline -->
<section id="process" class="section" style="background:var(--surface-warm);">
    <div class="container-wide">
        <div class="grid lg:grid-cols-5 gap-12 items-start">
            <div class="lg:col-span-2 lg:sticky lg:top-28 animate-fade-in">
                <span class="section-badge mb-4">{{ __('landing.process.badge') }}</span>
                <h2 class="section-title mb-4">{{ __('landing.process.title') }}</h2>
                <p class="section-description mb-8" style="margin:0;">{{ __('landing.process.subtitle') }}</p>
                <div class="magazine-img hidden lg:block" style="aspect-ratio:4/3;">
                    <picture>
                        <source srcset="/images/landing/process-1200.webp" type="image/webp">
                        <img src="/images/landing/process-1200.jpg" alt="Business permit process" loading="lazy" width="600" height="450">
                    </picture>
                </div>
            </div>

            <div class="lg:col-span-3 space-y-0">
                @foreach(['discovery', 'roadmap', 'preparation', 'liaison', 'monitoring', 'support'] as $index => $step)
                <div class="flex gap-5 animate-fade-in {{ $loop->last ? '' : 'pb-8' }}" style="{{ $loop->last ? '' : 'border-bottom:1px solid var(--border-light);margin-bottom:2rem;' }}">
                    <div class="flex-shrink-0">
                        <div class="w-11 h-11 rounded-full flex items-center justify-center text-sm font-bold text-white" style="background:var(--color-primary);">{{ $index + 1 }}</div>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h3 class="text-lg font-bold mb-1.5" style="color:var(--text-primary);">{{ __("investment.process.{$step}.title") }}</h3>
                        <p class="text-sm leading-relaxed mb-3" style="color:var(--text-secondary);">{{ __("investment.process.{$step}.description") }}</p>
                        <div class="grid sm:grid-cols-2 gap-1.5">
                            @foreach(__("investment.process.{$step}.deliverables") as $deliverable)
                            <div class="flex items-start gap-2 text-sm" style="color:var(--text-secondary);">
                                <i class="fas fa-check mt-0.5 text-xs" style="color:var(--color-success);"></i>
                                <span>{{ $deliverable }}</span>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</section>

<!-- ABOUT — Editorial Feature -->
<section id="about" class="section">
    <div class="container-wide">
        <div class="grid lg:grid-cols-2 gap-14 items-center">
            <div class="animate-fade-in">
                <span class="section-badge mb-4">{{ __('landing.about.badge') }}</span>
                <h2 class="section-title mb-5">{{ __('landing.about.title') }}</h2>
                <p class="text-lg leading-relaxed mb-4" style="color:var(--text-secondary);">{{ __('landing.about.description_1') }}</p>
                <p class="leading-relaxed mb-8" style="color:var(--text-secondary);">{{ __('landing.about.description_2') }}</p>

                <div class="grid sm:grid-cols-2 gap-5 mb-8">
                    <div class="flex items-start gap-3">
                        <div class="flex-shrink-0 w-11 h-11 rounded-lg flex items-center justify-center" style="background:rgba(15,23,42,.06);">
                            <i class="fas fa-certificate text-lg" style="color:var(--color-primary);"></i>
                        </div>
                        <div>
                            <h4 class="text-sm font-bold mb-0.5" style="color:var(--text-primary);">{{ __('landing.about.iso_title') }}</h4>
                            <p class="text-xs leading-relaxed" style="color:var(--text-tertiary);">{{ __('landing.about.iso_desc') }}</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <div class="flex-shrink-0 w-11 h-11 rounded-lg flex items-center justify-center" style="background:rgba(22,163,74,.08);">
                            <i class="fas fa-users text-lg" style="color:var(--color-success);"></i>
                        </div>
                        <div>
                            <h4 class="text-sm font-bold mb-0.5" style="color:var(--text-primary);">{{ __('landing.about.expert_title') }}</h4>
                            <p class="text-xs leading-relaxed" style="color:var(--text-tertiary);">{{ __('landing.about.expert_desc') }}</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <div class="flex-shrink-0 w-11 h-11 rounded-lg flex items-center justify-center" style="background:rgba(15,23,42,.06);">
                            <i class="fas fa-handshake text-lg" style="color:var(--color-primary);"></i>
                        </div>
                        <div>
                            <h4 class="text-sm font-bold mb-0.5" style="color:var(--text-primary);">{{ __('landing.about.clients_title') }}</h4>
                            <p class="text-xs leading-relaxed" style="color:var(--text-tertiary);">{{ __('landing.about.clients_desc') }}</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <div class="flex-shrink-0 w-11 h-11 rounded-lg flex items-center justify-center" style="background:rgba(234,179,8,.1);">
                            <i class="fas fa-globe text-lg" style="color:var(--color-warning);"></i>
                        </div>
                        <div>
                            <h4 class="text-sm font-bold mb-0.5" style="color:var(--text-primary);">{{ __('landing.about.nationwide_title') }}</h4>
                            <p class="text-xs leading-relaxed" style="color:var(--text-tertiary);">{{ __('landing.about.nationwide_desc') }}</p>
                        </div>
                    </div>
                </div>

                <div class="flex flex-wrap gap-3">
                    <a href="#contact" class="btn btn-primary"><i class="fas fa-phone"></i> {{ __('landing.about.cta_consult') }}</a>
                    <a href="#services" class="btn btn-outline-primary"><i class="fas fa-arrow-down"></i> {{ __('landing.about.cta_services') }}</a>
                </div>
            </div>

            <div class="relative about-image-wrapper animate-fade-in delay-200">
                <div class="magazine-img" style="aspect-ratio:4/3;">
                    <picture>
                        <source srcset="/images/landing/hero-modern-1400.webp" type="image/webp">
                        <img src="/images/landing/hero-modern-1400.jpg" alt="{{ __('landing.about.years_title') }}" loading="lazy" width="700" height="525">
                    </picture>
                    <div class="magazine-img-caption">
                        <h3 class="text-xl font-bold mb-1">{{ __('landing.about.years_title') }}</h3>
                        <p class="text-sm opacity-90">{{ __('landing.about.years_desc') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA — Premium Dark -->
<section id="contact" class="section" style="background:var(--surface-dark);">
    <div class="container-wide text-center">
        <h2 class="text-white mb-4" style="font-size:clamp(1.75rem,3.5vw,2.5rem);font-weight:700;letter-spacing:-.03em;">{{ __('landing.cta.title') }}</h2>
        <p class="text-lg mb-10 mx-auto" style="color:rgba(255,255,255,.75);max-width:46ch;">{{ __('landing.cta.subtitle') }}</p>
        <div class="flex flex-col sm:flex-row gap-3 justify-center">
            <a href="{{ route('pma.inquiry.create') }}" class="btn btn-secondary btn-lg">
                <i class="fas fa-clipboard-list"></i> {{ __('landing.cta.button_primary') }}
            </a>
            <a href="mailto:{{ data_get(config('landing_metrics'), 'contact.email', 'info@bizmark.id') }}" class="btn btn-ghost btn-lg">
                <i class="fas fa-envelope"></i> {{ __('landing.cta.button_email') }}
            </a>
            <a href="{{ data_get(config('landing_metrics'), 'contact.whatsapp_link', 'https://wa.me/6283879602855') }}" class="btn btn-success btn-lg">
                <i class="fab fa-whatsapp"></i> {{ __('landing.cta.whatsapp') }}
            </a>
        </div>
    </div>
</section>

<!-- BLOG — Magazine Articles -->
@if(isset($latestArticles) && $latestArticles->count() > 0)
<section id="blog" class="section" style="background:var(--surface-warm);">
    <div class="container-wide">
        <div class="text-center mb-12 animate-fade-in">
            <span class="section-badge mb-4">{{ __('landing.blog.badge') }}</span>
            <h2 class="section-title">{{ __('landing.blog.title') }}</h2>
            <p class="section-description mx-auto">{{ __('landing.blog.subtitle') }}</p>
        </div>

        <div class="grid md:grid-cols-3 gap-6">
            @foreach($latestArticles as $article)
            <article class="card group animate-fade-in">
                <div class="flex items-center gap-2 mb-4">
                    <span class="text-xs font-bold uppercase tracking-wider" style="color:var(--color-accent);">{{ $article->category ?? __('landing.blog.badge') }}</span>
                    <span style="color:var(--border-medium);">&middot;</span>
                    <span class="text-xs" style="color:var(--text-tertiary);">{{ $article->published_at?->diffForHumans() }}</span>
                </div>
                <h3 class="text-base font-bold mb-2 card-title" style="color:var(--text-primary);line-height:1.35;">{{ Str::limit($article->title, 60) }}</h3>
                <p class="text-sm mb-5 leading-relaxed" style="color:var(--text-secondary);">{{ Str::limit(strip_tags($article->content), 120) }}</p>
                @php
                    $blogRoute = app()->getLocale() === 'en' ? route('blog.article.en', $article->slug) : route('blog.article.id', $article->slug);
                @endphp
                <a href="{{ $blogRoute }}" class="link-primary text-sm inline-flex items-center group" aria-label="{{ __('landing.blog.read_more') }} - {{ $article->title }}">
                    {{ __('landing.blog.read_more') }}
                    <i class="fas fa-arrow-right ml-2 transition-transform group-hover:translate-x-1"></i>
                </a>
            </article>
            @endforeach
        </div>

        <div class="text-center mt-10">
            @php $allBlogRoute = app()->getLocale() === 'en' ? route('blog.index.en') : route('blog.index.id'); @endphp
            <a href="{{ $allBlogRoute }}" class="btn btn-outline-primary">
                <i class="fas fa-newspaper mr-2"></i> {{ __('landing.blog.view_all') }}
            </a>
        </div>
    </div>
</section>
@endif

@include('landing.partials.footer')

<!-- Sticky Mobile CTA -->
@php
    $mobileCTARoute = app()->getLocale() === 'en' ? route('pma.inquiry.create') : route('landing.service-inquiry.create');
    $mobileWhatsappLink = data_get(config('landing_metrics'), 'contact.whatsapp_link', 'https://wa.me/6283879602855');
@endphp
<div id="sticky-mobile-cta" class="fixed bottom-0 left-0 right-0 z-40 md:hidden transform translate-y-full transition-transform duration-300" style="background:rgba(255,255,255,.97);backdrop-filter:blur(12px);border-top:1px solid var(--border-light);box-shadow:0 -4px 20px rgba(0,0,0,.06);padding-bottom:env(safe-area-inset-bottom,0px);">
    <div class="flex gap-2 p-3">
        <a href="{{ $mobileCTARoute }}" class="flex-1 btn btn-primary text-sm py-3">
            <i class="fas fa-clipboard-list"></i> {{ __('landing.cta.button_primary') }}
        </a>
        <a href="{{ $mobileWhatsappLink }}" class="btn btn-success text-sm py-3 px-4" target="_blank" rel="noopener noreferrer">
            <i class="fab fa-whatsapp text-lg"></i>
        </a>
    </div>
</div>

@include('landing.partials.scripts')

<script>
// Services Show More Toggle
function toggleServices() {
    const extras = document.querySelectorAll('.service-card-extra');
    const icon = document.getElementById('services-toggle-icon');
    const text = document.getElementById('services-toggle-text');
    const isHidden = extras[0]?.classList.contains('hidden');

    extras.forEach(card => {
        if (isHidden) { card.classList.remove('hidden'); card.classList.add('visible'); }
        else { card.classList.add('hidden'); card.classList.remove('visible'); }
    });

    if (text) text.textContent = isHidden ? '{{ __("landing.services.show_less") }}' : '{{ __("landing.services.show_more") }}';
    if (icon) { icon.classList.toggle('fa-chevron-down', !isHidden); icon.classList.toggle('fa-chevron-up', isHidden); }
}
window.toggleServices = toggleServices;

// Scroll Animation Observer + Counter + Sticky CTA
document.addEventListener('DOMContentLoaded', function() {
    const animatedElements = document.querySelectorAll('.animate-fade-in');
    if (animatedElements.length > 0) {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => { if (entry.isIntersecting) entry.target.classList.add('visible'); });
        }, { threshold: 0.1, rootMargin: '0px 0px -50px 0px' });
        animatedElements.forEach(el => observer.observe(el));
    }

    const sections = document.querySelectorAll('section[id]');
    const navLinks = document.querySelectorAll('.nav-link');
    function updateActiveNav() {
        const scrollY = window.scrollY + 100;
        sections.forEach(section => {
            const sectionTop = section.offsetTop;
            const sectionHeight = section.offsetHeight;
            const sectionId = section.getAttribute('id');
            if (scrollY >= sectionTop && scrollY < sectionTop + sectionHeight) {
                navLinks.forEach(link => {
                    link.classList.remove('active');
                    if (link.getAttribute('href') === '#' + sectionId) link.classList.add('active');
                });
            }
        });
    }
    window.addEventListener('scroll', updateActiveNav);
    updateActiveNav();

    const stickyCTA = document.getElementById('sticky-mobile-cta');
    if (stickyCTA) {
        window.addEventListener('scroll', function() {
            if (window.scrollY > 600) stickyCTA.classList.remove('translate-y-full');
            else stickyCTA.classList.add('translate-y-full');
        }, { passive: true });
    }

    const counters = document.querySelectorAll('.counter');
    let counterObserverTriggered = false;
    counters.forEach(counter => {
        const target = counter.getAttribute('data-target');
        const suffix = counter.getAttribute('data-suffix') || '';
        counter.textContent = target + suffix;
    });

    function animateCounter(counter) {
        const target = parseInt(counter.getAttribute('data-target'));
        const suffix = counter.getAttribute('data-suffix') || '';
        const duration = 1500; const steps = 40; const stepTime = duration / steps; const increment = target / steps;
        let current = 0;
        counter.textContent = '0' + suffix;
        const timer = setInterval(() => {
            current += increment;
            if (current >= target) { counter.textContent = target + suffix; clearInterval(timer); }
            else { counter.textContent = Math.floor(current) + suffix; }
        }, stepTime);
    }

    const counterObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting && !counterObserverTriggered) {
                counterObserverTriggered = true;
                counters.forEach(counter => animateCounter(counter));
            }
        });
    }, { threshold: 0.3 });
    const statsSection = document.getElementById('stats');
    if (statsSection) counterObserver.observe(statsSection);
});
</script>

    <!-- Google Analytics 4 -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-DT71N7BSW9"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', 'G-DT71N7BSW9');
    </script>
</body>
</html>
