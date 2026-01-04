<!DOCTYPE html>
<html lang="id">
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
    <meta name="language" content="Indonesian">
    <link rel="canonical" href="https://bizmark.id/">
    
    <!-- Hreflang Tags for SEO -->
    <link rel="alternate" hreflang="id" href="https://bizmark.id/">
    <link rel="alternate" hreflang="en" href="https://bizmark.id/en">
    <link rel="alternate" hreflang="x-default" href="https://bizmark.id/">
    
    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://bizmark.id/">
    <meta property="og:title" content="{{ __('landing.meta.og_title') }}">
    <meta property="og:description" content="{{ __('landing.meta.og_description') }}">
    <meta property="og:image" content="https://bizmark.id/images/og-image-id.jpg">
    <meta property="og:locale" content="id_ID">
    <meta property="og:locale:alternate" content="en_US">
    
    <!-- Twitter -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:url" content="https://bizmark.id/">
    <meta name="twitter:title" content="{{ __('landing.meta.title') }}">
    <meta name="twitter:description" content="{{ __('landing.meta.description') }}">
    
    <!-- Schema.org Structured Data for SEO -->
    <script type="application/ld+json">
    {
        "@@context": "https://schema.org",
        "@@type": "LocalBusiness",
        "@@id": "https://bizmark.id/#organization",
        "name": "Bizmark.ID - PT Cangah Pajaratan Mandiri",
        "alternateName": "Bizmark Indonesia",
        "description": "Konsultan perizinan bisnis terpercaya di Indonesia. Spesialis AMDAL, UKL-UPL, izin lingkungan, dan pendirian PMA/PT.",
        "url": "https://bizmark.id",
        "logo": "https://bizmark.id/images/logo.png",
        "image": "https://bizmark.id/images/og-image-id.jpg",
        "telephone": "+62-838-7960-2855",
        "email": "cs@bizmark.id",
        "address": {
            "@@type": "PostalAddress",
            "addressLocality": "Karawang",
            "addressRegion": "Jawa Barat",
            "addressCountry": "ID"
        },
        "geo": {
            "@@type": "GeoCoordinates",
            "latitude": "-6.3227",
            "longitude": "107.3376"
        },
        "openingHoursSpecification": {
            "@@type": "OpeningHoursSpecification",
            "dayOfWeek": ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday"],
            "opens": "08:00",
            "closes": "17:00"
        },
        "priceRange": "$$",
        "currenciesAccepted": "IDR",
        "paymentAccepted": "Cash, Bank Transfer",
        "areaServed": {
            "@@type": "Country",
            "name": "Indonesia"
        },
        "sameAs": [
            "https://www.linkedin.com/company/bizmark-id",
            "https://www.facebook.com/bizmark.id",
            "https://www.instagram.com/bizmark.id"
        ],
        "aggregateRating": {
            "@@type": "AggregateRating",
            "ratingValue": "4.9",
            "reviewCount": "127",
            "bestRating": "5",
            "worstRating": "1"
        },
        "hasOfferCatalog": {
            "@@type": "OfferCatalog",
            "name": "Layanan Perizinan Bisnis",
            "itemListElement": [
                {
                    "@@type": "Offer",
                    "itemOffered": {
                        "@@type": "Service",
                        "name": "Pendirian PT PMA",
                        "description": "Layanan pendirian perusahaan penanaman modal asing di Indonesia"
                    }
                },
                {
                    "@@type": "Offer",
                    "itemOffered": {
                        "@@type": "Service",
                        "name": "Dokumen AMDAL",
                        "description": "Penyusunan dokumen Analisis Mengenai Dampak Lingkungan"
                    }
                },
                {
                    "@@type": "Offer",
                    "itemOffered": {
                        "@@type": "Service",
                        "name": "UKL-UPL",
                        "description": "Penyusunan dokumen Upaya Pengelolaan dan Pemantauan Lingkungan"
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
        "@@type": "BreadcrumbList",
        "itemListElement": [
            {
                "@@type": "ListItem",
                "position": 1,
                "name": "Beranda",
                "item": "https://bizmark.id/"
            }
        ]
    }
    </script>
    
    <!-- Favicons -->
    <link rel="icon" type="image/png" href="{{ asset('images/pavicon.png') }}">
    
    <!-- Performance: DNS Prefetch & Preconnect -->
    <link rel="dns-prefetch" href="//cdn.tailwindcss.com">
    <link rel="dns-prefetch" href="//cdnjs.cloudflare.com">
    <link rel="dns-prefetch" href="//cdn.jsdelivr.net">
    <link rel="preconnect" href="https://cdn.tailwindcss.com" crossorigin>
    <link rel="preconnect" href="https://cdnjs.cloudflare.com" crossorigin>
    <link rel="preconnect" href="https://cdn.jsdelivr.net" crossorigin>
    
    <!-- Performance: Preload Critical Resources -->
    <link rel="preload" as="style" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- FontAwesome with display=swap for better performance -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" media="print" onload="this.media='all'">
    <noscript><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"></noscript>
    
    <!-- Alpine.js for interactive components -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <style>
        :root {
            /* Color System */
            --primary: #1E40AF;
            --primary-dark: #1E3A8A;
            --primary-light: #3B82F6;
            --secondary: #0891B2;
            --accent: #DC2626;
            
            /* Semantic Colors */
            --surface: #FFFFFF;
            --surface-secondary: #F9FAFB;
            --surface-tertiary: #F3F4F6;
            --text-primary: #111827;
            --text-secondary: #4B5563;
            --text-tertiary: #9CA3AF;
            --success: #059669;
            --warning: #D97706;
            --error: #DC2626;
            
            /* Spacing Scale (8px base) */
            --space-1: 0.25rem;
            --space-2: 0.5rem;
            --space-3: 0.75rem;
            --space-4: 1rem;
            --space-5: 1.5rem;
            --space-6: 2rem;
            --space-8: 3rem;
            --space-10: 4rem;
            --space-12: 5rem;
            --space-16: 6rem;
            
            /* Border Radius */
            --radius-sm: 0.25rem;
            --radius-md: 0.5rem;
            --radius-lg: 0.75rem;
            --radius-xl: 1rem;
            --radius-2xl: 1.5rem;
            --radius-full: 9999px;
            
            /* Shadows */
            --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
            --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
            --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
            --shadow-xl: 0 20px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1);
        }
        
        .gradient-primary {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
        }
        
        .gradient-hero {
            background: linear-gradient(135deg, rgba(30, 64, 175, 0.97) 0%, rgba(30, 58, 138, 0.95) 100%);
        }
        
        /* Focus Visible States (Accessibility) */
        :focus-visible {
            outline: 2px solid var(--primary);
            outline-offset: 2px;
            border-radius: var(--radius-sm);
        }
        
        a:focus-visible,
        button:focus-visible {
            outline: 2px solid var(--primary);
            outline-offset: 2px;
        }
        
        :focus:not(:focus-visible) {
            outline: none;
        }
        
        /* Skip Link Styles */
        .skip-link {
            position: absolute;
            top: -100%;
            left: 50%;
            transform: translateX(-50%);
            z-index: 9999;
            padding: 0.75rem 1.5rem;
            background: var(--primary);
            color: white;
            font-weight: 600;
            border-radius: var(--radius-lg);
            text-decoration: none;
            transition: top 0.2s ease;
        }
        
        .skip-link:focus {
            top: 1rem;
        }
        
        /* Button Component System */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 0.875rem 1.75rem;
            font-weight: 600;
            font-size: 1rem;
            border-radius: var(--radius-lg);
            transition: all 0.2s ease;
            cursor: pointer;
            text-decoration: none;
        }
        
        .btn:hover {
            transform: translateY(-1px);
        }
        
        .btn:active {
            transform: translateY(0);
        }
        
        .btn-primary {
            background: var(--primary);
            color: white;
            box-shadow: var(--shadow-md);
        }
        
        .btn-primary:hover {
            background: var(--primary-dark);
            box-shadow: var(--shadow-lg);
        }
        
        .btn-secondary {
            background: white;
            color: var(--primary);
            box-shadow: var(--shadow-md);
        }
        
        .btn-secondary:hover {
            background: var(--surface-secondary);
            box-shadow: var(--shadow-lg);
        }
        
        .btn-ghost {
            background: transparent;
            color: white;
            border: 2px solid rgba(255, 255, 255, 0.3);
            backdrop-filter: blur(4px);
        }
        
        .btn-ghost:hover {
            background: rgba(255, 255, 255, 0.1);
            border-color: rgba(255, 255, 255, 0.5);
        }
        
        .btn-success {
            background: var(--success);
            color: white;
        }
        
        .btn-success:hover {
            background: #047857;
        }
        
        .btn-lg {
            padding: 1rem 2rem;
            font-size: 1.125rem;
        }
        
        .btn-sm {
            padding: 0.5rem 1rem;
            font-size: 0.875rem;
        }
        
        /* Nav Link Styles */
        .nav-link {
            position: relative;
            padding: 0.5rem 0.75rem;
            color: var(--text-secondary);
            font-weight: 500;
            transition: color 0.2s ease;
            border-radius: var(--radius-md);
        }
        
        .nav-link:hover {
            color: var(--primary);
            background: rgba(30, 64, 175, 0.05);
        }
        
        .nav-link.active {
            color: var(--primary);
            font-weight: 600;
        }
        
        .nav-link.active::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0.75rem;
            right: 0.75rem;
            height: 2px;
            background: var(--primary);
            border-radius: var(--radius-full);
        }
        
        /* Card Component */
        .card {
            background: var(--surface);
            border: 1px solid var(--surface-tertiary);
            border-radius: var(--radius-xl);
            padding: var(--space-6);
            transition: all 0.3s ease;
        }
        
        .card:hover {
            border-color: var(--primary-light);
            box-shadow: var(--shadow-xl);
            transform: translateY(-4px);
        }
        
        /* Prefers Reduced Motion */
        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after {
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: 0.01ms !important;
                scroll-behavior: auto !important;
            }
            
            .btn:hover,
            .card:hover {
                transform: none;
            }
        }
        
        /* Scroll Animation Classes */
        .animate-fade-in {
            opacity: 0;
            transform: translateY(20px);
            transition: opacity 0.6s ease, transform 0.6s ease;
        }
        
        .animate-fade-in.visible {
            opacity: 1;
            transform: translateY(0);
        }
        
        .delay-100 { transition-delay: 100ms; }
        .delay-200 { transition-delay: 200ms; }
        .delay-300 { transition-delay: 300ms; }
        .delay-400 { transition-delay: 400ms; }
    </style>
</head>
<body class="font-sans antialiased bg-white text-gray-900">

<!-- Skip to main content link for accessibility -->
<a href="#main-content" class="skip-link">Lewati ke konten utama</a>

<!-- Navbar -->
<nav class="fixed top-0 left-0 right-0 z-50 bg-white/95 backdrop-blur-sm border-b border-gray-200 shadow-sm" role="navigation" aria-label="Navigasi utama">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
            <!-- Logo -->
            <div class="flex items-center">
                <a href="{{ route('landing.id') }}" class="text-xl font-bold text-blue-900">
                    <i class="fas fa-certificate text-blue-600 mr-2"></i>
                    Bizmark.ID
                </a>
            </div>
            
            <!-- Desktop Navigation -->
            <div class="hidden md:flex items-center space-x-1">
                <a href="#main-content" class="nav-link">{{ __('landing.nav.home') }}</a>
                <a href="#services" class="nav-link">{{ __('landing.nav.services') }}</a>
                <a href="#process" class="nav-link">{{ __('landing.nav.process') }}</a>
                <a href="#about" class="nav-link">{{ __('landing.nav.about') }}</a>
                <a href="{{ route('blog.index.id') }}" class="nav-link">{{ __('landing.nav.blog') }}</a>
                
                <!-- Locale Switcher -->
                <x-locale-switcher />
                
                <a href="#contact" class="btn btn-primary">
                    {{ __('landing.nav.get_started') }}
                </a>
            </div>
            
            <!-- Mobile Menu Button -->
            <div class="md:hidden flex items-center">
                <button class="text-gray-700 hover:text-blue-600 p-2 rounded-lg hover:bg-gray-100 transition" 
                        onclick="toggleMobileMenu()" 
                        id="mobile-menu-button"
                        aria-label="Open navigation menu" 
                        aria-expanded="false"
                        aria-controls="mobile-menu">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
            </div>
        </div>
    </div>
</nav>

@include('landing.partials.mobile-menu')

<!-- Hero Section -->
<section id="main-content" class="pt-20 gradient-hero text-white min-h-screen flex items-center relative overflow-hidden">
    <!-- Hero Background Decorations -->
    <div class="absolute inset-0 overflow-hidden pointer-events-none" aria-hidden="true">
        <!-- Gradient Orbs -->
        <div class="absolute top-1/4 -left-32 w-96 h-96 bg-blue-400/20 rounded-full blur-3xl animate-pulse"></div>
        <div class="absolute bottom-1/4 -right-32 w-96 h-96 bg-cyan-400/20 rounded-full blur-3xl animate-pulse" style="animation-delay: 1s;"></div>
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[800px] h-[800px] bg-blue-500/10 rounded-full blur-3xl"></div>
        
        <!-- Grid Pattern -->
        <div class="absolute inset-0 opacity-[0.03]" style="background-image: radial-gradient(circle, white 1px, transparent 1px); background-size: 40px 40px;"></div>
        
        <!-- Floating Elements -->
        <div class="absolute top-20 left-10 w-4 h-4 bg-white/20 rounded-full animate-bounce" style="animation-duration: 3s;"></div>
        <div class="absolute top-40 right-20 w-3 h-3 bg-cyan-300/30 rounded-full animate-bounce" style="animation-duration: 2.5s; animation-delay: 0.5s;"></div>
        <div class="absolute bottom-40 left-1/4 w-2 h-2 bg-white/30 rounded-full animate-bounce" style="animation-duration: 4s; animation-delay: 1s;"></div>
    </div>
    
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-20 relative z-10">
        <div class="max-w-4xl mx-auto text-center">
            <!-- Badge with pulse indicator -->
            <div class="inline-flex items-center gap-2 px-4 py-2 bg-white/15 backdrop-blur-sm rounded-full text-sm font-semibold mb-8 border border-white/20">
                <span class="relative flex h-2 w-2">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2 bg-green-400"></span>
                </span>
                {{ __('landing.hero.badge') }}
            </div>
            
            <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold mb-6 leading-tight tracking-tight">
                {{ __('landing.hero.title') }}
            </h1>
            
            <p class="text-xl md:text-2xl mb-8 text-blue-100 leading-relaxed">
                {{ __('landing.hero.subtitle') }}
            </p>
            
            <p class="text-lg mb-10 text-blue-50">
                {{ __('landing.hero.description') }}
            </p>
            
            <div class="flex flex-col sm:flex-row gap-4 justify-center mb-12">
                <a href="{{ route('pma.inquiry.create') }}" class="btn btn-secondary btn-lg">
                    <i class="fas fa-calendar-check"></i>
                    {{ __('landing.hero.cta_primary') }}
                </a>
                <a href="#services" class="btn btn-ghost btn-lg">
                    <i class="fas fa-arrow-down"></i>
                    {{ __('landing.hero.cta_secondary') }}
                </a>
            </div>
            
            <div class="text-sm text-blue-200">
                <i class="fas fa-check-circle mr-2"></i>{{ __('landing.hero.trust_badge') }}
            </div>
        </div>
    </div>
</section>

<!-- Stats Section -->
<section id="stats" class="py-16 bg-gray-50 border-y border-gray-200">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
            <!-- Clients -->
            <div class="text-center animate-fade-in">
                <div class="text-4xl md:text-5xl font-bold text-blue-900 mb-2">
                    <span class="counter" data-target="500" data-suffix="+">0</span>
                </div>
                <div class="text-sm md:text-base font-semibold text-gray-900 mb-1">
                    {{ __('landing.stats.clients.label') }}
                </div>
                <div class="text-xs md:text-sm text-gray-600">
                    {{ __('landing.stats.clients.description') }}
                </div>
            </div>
            
            <!-- Experience -->
            <div class="text-center animate-fade-in delay-100">
                <div class="text-4xl md:text-5xl font-bold text-blue-900 mb-2">
                    <span class="counter" data-target="15" data-suffix="+">0</span>
                </div>
                <div class="text-sm md:text-base font-semibold text-gray-900 mb-1">
                    {{ __('landing.stats.experience.label') }}
                </div>
                <div class="text-xs md:text-sm text-gray-600">
                    {{ __('landing.stats.experience.description') }}
                </div>
            </div>
            
            <!-- Success Rate -->
            <div class="text-center animate-fade-in delay-200">
                <div class="text-4xl md:text-5xl font-bold text-blue-900 mb-2">
                    <span class="counter" data-target="98" data-suffix="%">0</span>
                </div>
                <div class="text-sm md:text-base font-semibold text-gray-900 mb-1">
                    {{ __('landing.stats.success_rate.label') }}
                </div>
                <div class="text-xs md:text-sm text-gray-600">
                    {{ __('landing.stats.success_rate.description') }}
                </div>
            </div>
            
            <!-- ISO Certified -->
            <div class="text-center animate-fade-in delay-300">
                <div class="text-4xl md:text-5xl font-bold text-blue-900 mb-2">
                    <i class="fas fa-certificate text-green-600"></i>
                </div>
                <div class="text-sm md:text-base font-semibold text-gray-900 mb-1">
                    {{ __('landing.stats.iso_certified.label') }}
                </div>
                <div class="text-xs md:text-sm text-gray-600">
                    {{ __('landing.stats.iso_certified.description') }}
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Services Section -->
<section id="services" class="py-20 bg-white" aria-labelledby="services-heading">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16 animate-fade-in">
            <div class="inline-block px-4 py-2 bg-blue-100 text-blue-900 rounded-full text-sm font-semibold mb-4">
                {{ __('landing.services.badge') }}
            </div>
            <h2 id="services-heading" class="text-3xl md:text-4xl lg:text-5xl font-bold mb-6 text-gray-900">
                {{ __('landing.services.title') }}
            </h2>
            <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                {{ __('landing.services.subtitle') }}
            </p>
        </div>
        
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach($services as $slug => $service)
            <div class="group card animate-fade-in delay-{{ ($loop->index % 3 + 1) * 100 }}">
                <div class="flex items-start justify-between mb-4">
                    <div class="w-16 h-16 rounded-xl flex items-center justify-center" style="background: {{ $service['color'] }}20;">
                        <i class="fas {{ $service['icon'] }} text-3xl" style="color: {{ $service['color'] }};"></i>
                    </div>
                    @if(isset($service['featured']) && $service['featured'])
                    <span class="px-3 py-1 bg-yellow-100 text-yellow-800 text-xs font-bold rounded-full">
                        Popular
                    </span>
                    @endif
                </div>
                
                <h3 class="text-xl font-bold mb-3 text-gray-900 group-hover:text-blue-900 transition">
                    {{ $service['title'] }}
                </h3>
                
                <p class="text-gray-600 mb-4 leading-relaxed">
                    {{ $service['short_description'] }}
                </p>
                
                @if(isset($service['pricing']))
                <div class="flex items-center justify-between mb-4 pb-4 border-b border-gray-100">
                    <div>
                        <div class="text-sm text-gray-500">{{ __('landing.services.starting_from') }}</div>
                        <div class="text-lg font-bold text-blue-900">{{ $service['pricing']['display'] }}</div>
                    </div>
                    <div class="text-right">
                        <div class="text-sm text-gray-500">{{ __('landing.services.duration') }}</div>
                        <div class="text-sm font-semibold text-gray-700">{{ $service['duration'] }}</div>
                    </div>
                </div>
                @endif
                
                <a href="{{ route('services.show.id', $slug) }}" class="inline-flex items-center text-blue-900 font-semibold hover:text-blue-700 transition group">
                    {{ __('landing.services.learn_more') }}
                    <i class="fas fa-arrow-right ml-2 group-hover:translate-x-1 transition-transform"></i>
                </a>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- Process Section -->
<section id="process" class="py-20 bg-gradient-to-br from-gray-50 to-blue-50">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16 animate-fade-in">
            <div class="inline-block px-4 py-2 bg-blue-100 text-blue-900 rounded-full text-sm font-semibold mb-4">
                {{ __('landing.process.badge') }}
            </div>
            <h2 class="text-3xl md:text-4xl lg:text-5xl font-bold mb-6 text-gray-900">
                {{ __('landing.process.title') }}
            </h2>
            <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                {{ __('landing.process.subtitle') }}
            </p>
        </div>
        
        <div class="max-w-5xl mx-auto">
            @foreach(['discovery', 'roadmap', 'preparation', 'liaison', 'monitoring', 'support'] as $index => $step)
            <div class="flex gap-6 mb-8 animate-fade-in {{ $loop->last ? '' : 'pb-8 border-b border-gray-200' }}">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 rounded-full gradient-primary text-white flex items-center justify-center font-bold text-lg">
                        {{ $index + 1 }}
                    </div>
                </div>
                <div class="flex-1">
                    <h3 class="text-xl font-bold mb-2 text-gray-900">
                        {{ __("investment.process.{$step}.title") }}
                    </h3>
                    <p class="text-gray-600 mb-4">
                        {{ __("investment.process.{$step}.description") }}
                    </p>
                    <div class="grid sm:grid-cols-2 gap-2">
                        @foreach(__("investment.process.{$step}.deliverables") as $deliverable)
                        <div class="flex items-start gap-2 text-sm text-gray-700">
                            <i class="fas fa-check text-green-600 mt-1"></i>
                            <span>{{ $deliverable }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- About Section -->
<section id="about" class="py-20 bg-white">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid lg:grid-cols-2 gap-12 items-center">
            <div class="animate-fade-in">
                <div class="inline-block px-4 py-2 bg-blue-100 text-blue-900 rounded-full text-sm font-semibold mb-4">
                    Tentang Kami
                </div>
                <h2 class="text-3xl md:text-4xl font-bold mb-6 text-gray-900">
                    Mitra Terpercaya untuk Kesuksesan Bisnis Anda
                </h2>
                <p class="text-lg text-gray-600 mb-6">
                    PT Cangah Pajaratan Mandiri (Bizmark.ID) telah melayani pelaku usaha Indonesia selama lebih dari 15 tahun. Kami spesialis dalam membantu Anda menavigasi kompleksitas regulasi perizinan di Indonesia untuk memastikan bisnis Anda berjalan lancar dan sesuai ketentuan.
                </p>
                <p class="text-gray-600 mb-6">
                    Tim konsultan bersertifikat kami memiliki pengalaman luas dalam menangani berbagai jenis perizinan untuk berbagai industri di seluruh Indonesia. Kami bekerja sama dengan instansi pemerintah termasuk Kementerian Lingkungan Hidup, BKPM, dan pemerintah daerah untuk memastikan kepatuhan dan kesiapan operasional Anda.
                </p>
                
                <div class="grid sm:grid-cols-2 gap-6 mb-8">
                    <div class="flex items-start gap-3">
                        <div class="flex-shrink-0 w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-certificate text-blue-600 text-xl"></i>
                        </div>
                        <div>
                            <h4 class="font-bold text-gray-900 mb-1">Tersertifikasi ISO</h4>
                            <p class="text-sm text-gray-600">Sistem manajemen mutu tersertifikasi</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <div class="flex-shrink-0 w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-users text-green-600 text-xl"></i>
                        </div>
                        <div>
                            <h4 class="font-bold text-gray-900 mb-1">Tim Ahli</h4>
                            <p class="text-sm text-gray-600">Konsultan lingkungan bersertifikat</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <div class="flex-shrink-0 w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-handshake text-purple-600 text-xl"></i>
                        </div>
                        <div>
                            <h4 class="font-bold text-gray-900 mb-1">500+ Klien</h4>
                            <p class="text-sm text-gray-600">Dipercaya perusahaan Indonesia</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <div class="flex-shrink-0 w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-globe text-orange-600 text-xl"></i>
                        </div>
                        <div>
                            <h4 class="font-bold text-gray-900 mb-1">Se-Indonesia</h4>
                            <p class="text-sm text-gray-600">Layanan di seluruh Indonesia</p>
                        </div>
                    </div>
                </div>

                <div class="flex flex-wrap gap-4">
                    <a href="#contact" class="btn btn-primary">
                        <i class="fas fa-phone"></i>
                        Konsultasi Gratis
                    </a>
                    <a href="#services" class="px-6 py-3 border-2 border-blue-900 text-blue-900 rounded-lg font-semibold hover:bg-blue-50 transition inline-flex items-center gap-2">
                        <i class="fas fa-arrow-down"></i>
                        Layanan Kami
                    </a>
                </div>
            </div>

            <div class="relative">
                <div class="aspect-[4/3] bg-gradient-to-br from-blue-100 to-blue-50 rounded-2xl overflow-hidden">
                    <div class="absolute inset-0 flex items-center justify-center">
                        <div class="text-center p-8">
                            <i class="fas fa-building text-8xl text-blue-600 opacity-20 mb-4"></i>
                            <h3 class="text-2xl font-bold text-blue-900 mb-2">15+ Tahun</h3>
                            <p class="text-blue-700">Melayani Pengusaha Indonesia</p>
                        </div>
                    </div>
                </div>
                
                <!-- Stats Overlay -->
                <div class="absolute -bottom-6 -left-6 bg-white p-6 rounded-xl shadow-xl border border-gray-100">
                    <div class="text-4xl font-bold text-blue-900">98%</div>
                    <div class="text-sm text-gray-600">Tingkat Keberhasilan</div>
                </div>
                
                <div class="absolute -top-6 -right-6 bg-white p-6 rounded-xl shadow-xl border border-gray-100">
                    <div class="text-4xl font-bold text-green-600">500+</div>
                    <div class="text-sm text-gray-600">Perusahaan Terlayani</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section id="contact" class="py-20 gradient-primary text-white">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-3xl md:text-4xl font-bold mb-6">
            {{ __('landing.cta.title') }}
        </h2>
        <p class="text-xl mb-10 text-blue-100">
            {{ __('landing.cta.subtitle') }}
        </p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="{{ route('pma.inquiry.create') }}" class="btn btn-secondary btn-lg">
                <i class="fas fa-clipboard-list"></i>
                Mulai Konsultasi Gratis
            </a>
            <a href="mailto:cs@bizmark.id" class="btn btn-ghost btn-lg">
                <i class="fas fa-envelope"></i>
                Email Kami
            </a>
            <a href="https://wa.me/6283879602855" class="btn btn-success btn-lg">
                <i class="fab fa-whatsapp"></i>
                WhatsApp
            </a>
        </div>
    </div>
</section>

@include('landing.partials.footer')

<script>
// Mobile Menu Toggle
function toggleMobileMenu() {
    const menu = document.getElementById('mobileMenu');
    const menuButton = document.getElementById('mobile-menu-button');
    const isHidden = menu.classList.contains('hidden');
    
    if (isHidden) {
        menu.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        if (menuButton) {
            menuButton.setAttribute('aria-expanded', 'true');
        }
    } else {
        menu.classList.add('hidden');
        document.body.style.overflow = '';
        if (menuButton) {
            menuButton.setAttribute('aria-expanded', 'false');
        }
    }
}

// Smooth scroll for anchor links
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        const href = this.getAttribute('href');
        if (href !== '#') {
            e.preventDefault();
            const target = document.querySelector(href);
            if (target) {
                target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                // Close mobile menu if open
                const menu = document.getElementById('mobileMenu');
                if (menu && !menu.classList.contains('hidden')) {
                    toggleMobileMenu();
                }
            }
        }
    });
});

// Scroll Animation Observer
document.addEventListener('DOMContentLoaded', function() {
    const animatedElements = document.querySelectorAll('.animate-fade-in');
    
    if (animatedElements.length > 0) {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                }
            });
        }, {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        });
        
        animatedElements.forEach(el => observer.observe(el));
    }
    
    // Active nav link on scroll
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
                    if (link.getAttribute('href') === '#' + sectionId) {
                        link.classList.add('active');
                    }
                });
            }
        });
    }
    
    window.addEventListener('scroll', updateActiveNav);
    updateActiveNav();
    
    // Counter Animation
    const counters = document.querySelectorAll('.counter');
    let counterObserverTriggered = false;
    
    function animateCounter(counter) {
        const target = parseInt(counter.getAttribute('data-target'));
        const suffix = counter.getAttribute('data-suffix') || '';
        const duration = 2000; // 2 seconds
        const steps = 60;
        const stepTime = duration / steps;
        const increment = target / steps;
        let current = 0;
        
        const timer = setInterval(() => {
            current += increment;
            if (current >= target) {
                counter.textContent = target + suffix;
                clearInterval(timer);
            } else {
                counter.textContent = Math.floor(current) + suffix;
            }
        }, stepTime);
    }
    
    const counterObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting && !counterObserverTriggered) {
                counterObserverTriggered = true;
                counters.forEach(counter => animateCounter(counter));
            }
        });
    }, { threshold: 0.5 });
    
    const statsSection = document.getElementById('stats');
    if (statsSection) counterObserver.observe(statsSection);
});
</script>

</body>
</html>
