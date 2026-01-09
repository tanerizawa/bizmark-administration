<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    @php
        $currentLocale = app()->getLocale();
        $isEnglish = $currentLocale === 'en';
    @endphp
    
    <title>{{ config('app.name') }} - {{ $isEnglish ? 'Trusted Business Licensing Solutions' : 'Solusi Perizinan Terpercaya' }}</title>
    
    <!-- SEO Meta Tags -->
    <meta name="description" content="{{ $isEnglish ? 'Trusted business licensing services for OSS, AMDAL, PBG, SLF, and more. Fast, transparent, and 100% legal.' : 'Layanan perizinan usaha terpercaya untuk OSS, AMDAL, PBG, SLF, dan lainnya. Proses cepat, transparan, dan 100% legal.' }}">
    <meta name="keywords" content="OSS, AMDAL, PBG, SLF, {{ $isEnglish ? 'Business Licensing, NIB, Business License, Licensing Consultant' : 'Perizinan Usaha, NIB, Izin Usaha, Konsultan Perizinan' }}">
    <meta name="author" content="Bizmark.ID">
    
    <!-- Open Graph Meta Tags -->
    <meta property="og:title" content="Bizmark.ID - {{ $isEnglish ? 'Trusted Licensing Solutions' : 'Solusi Perizinan Terpercaya' }}">
    <meta property="og:description" content="{{ $isEnglish ? 'Professional business licensing services with fast and transparent processes' : 'Layanan perizinan usaha profesional dengan proses cepat dan transparan' }}">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:image" content="{{ asset('images/og-image.jpg') }}">
    <meta property="og:locale" content="{{ $isEnglish ? 'en_US' : 'id_ID' }}">
    
    <!-- PWA Meta Tags -->
    <meta name="theme-color" content="#0077B5">
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/icons/icon-192x192.png') }}">
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Alpine.js (for interactive components) -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- Custom Styles from Sections -->
    @stack('styles')
    
    <!-- Magazine Custom Styles -->
    <style>
        /* Magazine Design System */
        :root {
            /* Fonts */
            --font-display: 'Playfair Display', serif;
            --font-body: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            
            /* LinkedIn Blue Palette + Gold Accent */
            --color-ink: #111827;          /* Dark gray-black for text */
            --color-paper: #FFFFFF;         /* Pure white */
            --color-primary: #0077B5;       /* LinkedIn Blue - Primary */
            --color-primary-dark: #005582;  /* LinkedIn Blue Dark */
            --color-primary-darker: #003d5c; /* LinkedIn Blue Darker */
            --color-primary-light: #0099E5; /* LinkedIn Blue Light */
            --color-gold: #F2CD49;          /* Logo Gold/Yellow */
            --color-gold-dark: #D4AF37;     /* Darker Gold */
            --color-success: #10B981;       /* Green for WhatsApp/Success */
            --color-muted: #6B7280;         /* Gray for muted text */
            --color-border: #E5E7EB;        /* Light gray borders */
            
            /* Spacing */
            --space-xs: 8px;
            --space-sm: 16px;
            --space-md: 24px;
            --space-lg: 32px;
            --space-xl: 48px;
            --space-2xl: 64px;
            --space-3xl: 80px;
            
            /* Shadows */
            --shadow-sm: 0 1px 3px rgba(0,0,0,0.05);
            --shadow-md: 0 4px 6px rgba(0,0,0,0.08);
            --shadow-lg: 0 10px 20px rgba(0,0,0,0.12);
            --shadow-blue: 0 10px 25px rgba(0, 119, 181, 0.2);
            --shadow-gold: 0 10px 25px rgba(242, 205, 73, 0.2);
        }
        
        /* Typography */
        body {
            font-family: var(--font-body);
            color: var(--color-paper);
            background: #000000;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 1rem;
        }
            -moz-osx-font-smoothing: grayscale;
        }
        
        .headline {
            font-family: var(--font-display);
            font-weight: 900;
            line-height: 1.1;
            letter-spacing: -0.02em;
        }
        
        .deck {
            font-family: var(--font-body);
            font-weight: 400;
            line-height: 1.6;
        }
        
        .category-tag {
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.1em;
            text-transform: uppercase;
        }
        
        /* Magazine Section */
        .magazine-section {
            padding: var(--space-lg) var(--space-md);
            max-width: 1200px;
            margin: 0 auto;
        }
        
        /* Animations */
        .fade-in-up {
            opacity: 0;
            transform: translateY(30px);
            transition: opacity 0.6s ease, transform 0.6s ease;
        }
        
        @keyframes bounce-slow {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }
        
        .animate-bounce-slow {
            animation: bounce-slow 2s ease-in-out infinite;
        }
        
        .fade-in-up.visible {
            opacity: 1;
            transform: translateY(0);
        }
        
        /* Parallax */
        .parallax-bg {
            transition: transform 0.1s ease-out;
        }
        
        /* Touch Feedback */
        .touchable:active {
            opacity: 0.8;
            transform: scale(0.98);
        }
        
        /* Magazine Card */
        .magazine-card {
            background: var(--color-paper);
            border-radius: 20px;
            overflow: hidden;
            box-shadow: var(--shadow-md);
            transition: all 0.3s ease;
        }
        
        .magazine-card:hover {
            box-shadow: var(--shadow-lg);
            transform: translateY(-4px);
        }
        
        .magazine-card-image img {
            transition: transform 0.5s ease;
        }
        
        .magazine-card:hover .magazine-card-image img {
            transform: scale(1.05);
        }
        
        /* Gradient Text */
        .text-gradient {
            background: linear-gradient(135deg, #0077B5 0%, #005582 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        /* Smooth Scroll */
        html {
            scroll-behavior: smooth;
        }
        
        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }
        
        ::-webkit-scrollbar-track {
            background: #F3F4F6;
        }
        
        ::-webkit-scrollbar-thumb {
            background: #9CA3AF;
            border-radius: 4px;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: #6B7280;
        }
        
        /* Pulse Animation for CTA */
        @keyframes pulse-slow {
            0%, 100% {
                box-shadow: 0 20px 25px -5px rgba(0, 119, 181, 0.4), 0 10px 10px -5px rgba(0, 85, 130, 0.3);
            }
            50% {
                box-shadow: 0 25px 30px -5px rgba(0, 119, 181, 0.6), 0 15px 15px -5px rgba(0, 85, 130, 0.5);
            }
        }
        
        .animate-pulse-slow {
            animation: pulse-slow 2s ease-in-out infinite;
        }
        
        .shadow-3xl {
            box-shadow: 0 30px 40px -10px rgba(0, 0, 0, 0.3);
        }
    </style>
    
    @stack('styles')
    
    <!-- Mobile Navbar Styles -->
    <style>
        .navbar {
            position: fixed;
            top: 0;
            width: 100%;
            background: rgba(0, 0, 0, 0.95);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(84, 84, 88, 0.35);
            z-index: 1000;
            transition: all 0.3s ease;
        }
        
        .navbar.scrolled {
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
        }
        
        .mobile-menu {
            display: none;
            position: fixed;
            top: 70px;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.98);
            backdrop-filter: blur(20px);
            z-index: 999;
            overflow-y: auto;
        }
        
        .mobile-menu.active {
            display: block;
        }
        
        .mobile-menu a {
            color: rgba(235, 235, 245, 0.6);
            transition: color 0.2s ease;
        }
        
        .mobile-menu a:hover {
            color: #007AFF;
        }
    </style>
</head>
<body class="bg-white overflow-x-hidden">
    
    <!-- Navigation Bar -->
    <nav class="navbar">
        <div class="container mx-auto px-4 py-4">
            <div class="flex justify-between items-center">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-700 rounded-lg flex items-center justify-center">
                        <i class="fas fa-shield-alt text-white text-xl"></i>
                    </div>
                    <span class="text-xl font-bold text-white">Bizmark<span class="text-yellow-400">.ID</span></span>
                </div>
                
                <div class="hidden md:flex items-center space-x-8 text-white">
                    <a href="#home" class="hover:text-blue-400 transition">{{ $isEnglish ? 'Home' : 'Beranda' }}</a>
                    <a href="#services" class="hover:text-blue-400 transition">{{ $isEnglish ? 'Services' : 'Layanan' }}</a>
                    <a href="#why-us" class="hover:text-blue-400 transition">{{ $isEnglish ? 'Why Us' : 'Keunggulan' }}</a>
                    <a href="#contact" class="hover:text-blue-400 transition">{{ $isEnglish ? 'Contact' : 'Kontak' }}</a>
                    <a href="{{ route('login') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 rounded-lg transition text-white">
                        <i class="fas fa-sign-in-alt"></i>
                        <span>Login</span>
                    </a>
                </div>
                
                <button class="md:hidden text-2xl text-white" onclick="toggleMobileMenu()">
                    <i class="fas fa-bars"></i>
                </button>
            </div>
        </div>
    </nav>
    
    <div id="mobileMenu" class="mobile-menu">
        <div class="container mx-auto px-4 py-6">
            <a href="#home" class="block py-3 text-lg font-medium" onclick="toggleMobileMenu()">
                <i class="fas fa-home mr-3"></i>{{ $isEnglish ? 'Home' : 'Beranda' }}
            </a>
            <a href="#services" class="block py-3 text-lg font-medium" onclick="toggleMobileMenu()">
                <i class="fas fa-certificate mr-3"></i>{{ $isEnglish ? 'Services' : 'Layanan' }}
            </a>
            <a href="#why-us" class="block py-3 text-lg font-medium" onclick="toggleMobileMenu()">
                <i class="fas fa-award mr-3"></i>{{ $isEnglish ? 'Why Us' : 'Keunggulan' }}
            </a>
            <a href="#testimonials" class="block py-3 text-lg font-medium" onclick="toggleMobileMenu()">
                <i class="fas fa-star mr-3"></i>{{ $isEnglish ? 'Testimonials' : 'Testimoni' }}
            </a>
            <a href="#faq" class="block py-3 text-lg font-medium" onclick="toggleMobileMenu()">
                <i class="fas fa-question-circle mr-3"></i>FAQ</a>
            <a href="#contact" class="block py-3 text-lg font-medium" onclick="toggleMobileMenu()">
                <i class="fas fa-envelope mr-3"></i>{{ $isEnglish ? 'Contact' : 'Kontak' }}
            </a>
            <div class="mt-6 pt-6 border-t border-gray-800">
                <a href="{{ route('login') }}" class="block py-3 px-6 bg-blue-600 hover:bg-blue-700 rounded-lg text-center text-white transition font-semibold">
                    <i class="fas fa-sign-in-alt mr-2"></i>Login
                </a>
            </div>
        </div>
    </div>
    
    <!-- Magazine Content -->
    <main id="magazine-content" class="pt-16">
        @yield('content')
    </main>
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- Magazine JavaScript -->
    <script>
        // Intersection Observer for Fade-in Animations
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };
        
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                }
            });
        }, observerOptions);
        
        // Observe all fade-in elements
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('.fade-in-up').forEach(el => {
                observer.observe(el);
            });
        });
        
        // Parallax Effect for Hero
        let ticking = false;
        window.addEventListener('scroll', () => {
            if (!ticking) {
                window.requestAnimationFrame(() => {
                    const scrolled = window.pageYOffset;
                    const parallaxBg = document.querySelector('.parallax-bg');
                    
                    if (parallaxBg && scrolled < window.innerHeight) {
                        parallaxBg.style.transform = `translateY(${scrolled * 0.5}px)`;
                    }
                    
                    ticking = false;
                });
                ticking = true;
            }
        });
        
        // Smooth Scroll for Anchor Links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                const href = this.getAttribute('href');
                if (href !== '#') {
                    e.preventDefault();
                    const target = document.querySelector(href);
                    if (target) {
                        target.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                    }
                }
            });
        });
        
        // Screen Width Detection (Auto-redirect disabled to prevent infinite loop)
        // Users can manually switch using the toggle in header
        function updateScreenWidth() {
            const width = window.innerWidth;
            
            // Send width to server for analytics
            fetch('/api/set-screen-width', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ width: width })
            }).catch(err => console.log('Screen width update failed:', err));
        }
        
        // Update on load
        updateScreenWidth();
        
        // Update on resize (debounced)
        let resizeTimeout;
        window.addEventListener('resize', () => {
            clearTimeout(resizeTimeout);
            resizeTimeout = setTimeout(updateScreenWidth, 1000);
        });
        
        // Suppress Cloudflare Insights errors (optional analytics)
        window.addEventListener('error', function(e) {
            if (e.filename && e.filename.includes('cloudflareinsights.com')) {
                e.preventDefault();
                console.log('Cloudflare Insights blocked or unavailable (optional analytics)');
            }
        }, true);
        
        // Mobile Menu Toggle
        function toggleMobileMenu() {
            const menu = document.getElementById('mobileMenu');
            menu.classList.toggle('active');
        }
        
        // Navbar Scroll Effect
        window.addEventListener('scroll', () => {
            const navbar = document.querySelector('.navbar');
            if (window.scrollY > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });
    </script>
    
    @stack('scripts')
</body>
</html>
