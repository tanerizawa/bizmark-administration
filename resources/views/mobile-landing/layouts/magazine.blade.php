<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>{{ config('app.name') }} - Solusi Perizinan Terpercaya</title>
    
    <!-- SEO Meta Tags -->
    <meta name="description" content="Layanan perizinan usaha terpercaya untuk OSS, AMDAL, PBG, SLF, dan lainnya. Proses cepat, transparan, dan 100% legal.">
    <meta name="keywords" content="OSS, AMDAL, PBG, SLF, Perizinan Usaha, NIB, Izin Usaha, Konsultan Perizinan">
    <meta name="author" content="Bizmark.ID">
    
    <!-- Open Graph Meta Tags -->
    <meta property="og:title" content="Bizmark.ID - Solusi Perizinan Terpercaya">
    <meta property="og:description" content="Layanan perizinan usaha profesional dengan proses cepat dan transparan">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:image" content="{{ asset('images/og-image.jpg') }}">
    
    <!-- PWA Meta Tags -->
    <meta name="theme-color" content="#1E40AF">
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
    
    <!-- Magazine Custom Styles -->
    <style>
        /* Magazine Design System */
        :root {
            /* Fonts */
            --font-display: 'Playfair Display', serif;
            --font-body: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            
            /* Colors */
            --color-ink: #111827;
            --color-paper: #FFFFFF;
            --color-primary: #1E40AF;
            --color-gold: #F59E0B;
            --color-crimson: #DC2626;
            --color-emerald: #059669;
            --color-purple: #7C3AED;
            
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
        }
        
        /* Typography */
        body {
            font-family: var(--font-body);
            color: var(--color-ink);
            -webkit-font-smoothing: antialiased;
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
            padding: var(--space-2xl) var(--space-md);
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
</head>
<body class="bg-white overflow-x-hidden">
    
    <!-- Magazine Content -->
    <main id="magazine-content">
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
        
        // Screen Width Detection with Auto-Redirect
        let lastScreenWidth = window.innerWidth;
        const MOBILE_BREAKPOINT = 768;
        
        function updateScreenWidth() {
            const width = window.innerWidth;
            
            // Send width to server
            fetch('/api/set-screen-width', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ width: width })
            }).catch(err => console.log('Screen width update failed:', err));
            
            // Check if crossed breakpoint threshold
            const wasDesktop = lastScreenWidth >= MOBILE_BREAKPOINT;
            const isDesktop = width >= MOBILE_BREAKPOINT;
            
            // If switched from mobile to desktop, redirect to desktop version
            if (!wasDesktop && isDesktop) {
                console.log('Switched to desktop view, redirecting...');
                setTimeout(() => {
                    window.location.href = '/?desktop=1';
                }, 500);
            }
            
            lastScreenWidth = width;
        }
        
        // Update on load
        updateScreenWidth();
        
        // Debounced resize handler (only redirect after user stops resizing)
        let resizeTimeout;
        window.addEventListener('resize', () => {
            clearTimeout(resizeTimeout);
            resizeTimeout = setTimeout(updateScreenWidth, 1000); // Wait 1 second after resize stops
        });
    </script>
    
    @stack('scripts')
</body>
</html>
