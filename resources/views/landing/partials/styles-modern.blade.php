{{-- MODERN PROFESSIONAL DESIGN SYSTEM 2025 --}}
{{-- Complete Redesign - Bold, Elegant, Sophisticated --}}

<script>
    tailwind.config = {
        darkMode: 'class',
        theme: {
            extend: {
                colors: {
                    primary: {
                        DEFAULT: '#0077B5',
                        50: '#e7f5ff',
                        100: '#d0ebff',
                        600: '#0077B5',
                        700: '#005582',
                        800: '#004868',
                        900: '#003d5c',
                    },
                    secondary: {
                        DEFAULT: '#F97316',
                        600: '#EA580C',
                        700: '#C2410C',
                    },
                },
                fontFamily: {
                    sans: ['Inter', 'system-ui', 'sans-serif'],
                },
                fontSize: {
                    'hero': ['4rem', { lineHeight: '1.1', fontWeight: '900', letterSpacing: '-0.02em' }],
                    'display': ['3rem', { lineHeight: '1.2', fontWeight: '800', letterSpacing: '-0.02em' }],
                },
                spacing: {
                    '18': '4.5rem',
                    '30': '7.5rem',
                    '36': '9rem',
                },
                borderRadius: {
                    '2xl': '1rem',
                    '3xl': '1.5rem',
                    '4xl': '2rem',
                },
                boxShadow: {
                    'soft': '0 2px 15px 0 rgba(0, 0, 0, 0.05)',
                    'soft-lg': '0 10px 40px 0 rgba(0, 0, 0, 0.08)',
                    'soft-xl': '0 20px 50px 0 rgba(0, 0, 0, 0.12)',
                },
            }
        }
    }
</script>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap');
    
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }
    
    :root {
        /* Premium Color Palette - LinkedIn Official Blue */
        --primary: #0077B5;
        --primary-dark: #005582;
        --primary-darker: #003d5c;
        --secondary: #F97316;
        --secondary-dark: #EA580C;
        --accent: #10B981;
        
        /* Modern Grays */
        --gray-50: #F9FAFB;
        --gray-100: #F3F4F6;
        --gray-200: #E5E7EB;
        --gray-600: #4B5563;
        --gray-700: #374151;
        --gray-800: #1F2937;
        --gray-900: #111827;
        
        /* Spacing - Generous for modern look */
        --section-y: 120px;
        --section-y-sm: 80px;
        
        /* Shadows - Soft & elevated */
        --shadow-soft: 0 2px 15px rgba(0, 0, 0, 0.05);
        --shadow-soft-lg: 0 10px 40px rgba(0, 0, 0, 0.08);
        --shadow-soft-xl: 0 20px 50px rgba(0, 0, 0, 0.12);
    }
    
body {
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
    background: #FFFFFF;
    color: var(--gray-900);
    font-size: 17px;
    line-height: 1.7;
    font-weight: 400;
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
    overflow-x: hidden;
}
    
    /* =================================
       MODERN TYPOGRAPHY SYSTEM
       Large, bold, confident
    ================================= */
    
    h1, h2, h3, h4, h5, h6 {
        font-weight: 800;
        line-height: 1.2;
        color: var(--gray-900);
        margin-bottom: 1rem;
        letter-spacing: -0.025em;
    }
    
    h1 { font-size: 4rem; font-weight: 900; }      /* 64px HERO */
    h2 { font-size: 3rem; font-weight: 800; }      /* 48px SECTIONS */
    h3 { font-size: 2rem; font-weight: 700; }      /* 32px */
    h4 { font-size: 1.5rem; font-weight: 700; }    /* 24px */
    
    p {
        margin-bottom: 1rem;
        color: var(--gray-600);
        line-height: 1.7;
        font-size: 1.125rem;
    }
    
    .text-lead {
        font-size: 1.375rem;
        line-height: 1.6;
        color: var(--gray-700);
    }
    
    @media (max-width: 1024px) {
        h1 { font-size: 3rem; }
        h2 { font-size: 2.5rem; }
    }
    
    @media (max-width: 768px) {
        h1 { font-size: 2.5rem; }
        h2 { font-size: 2rem; }
        p { font-size: 1rem; }
    }
    
    /* =================================
       MODERN LAYOUT SYSTEM
    ================================= */
    
    .container,
    .container-wide {
        width: 100%;
        margin: 0 auto;
        padding-inline: clamp(24px, 5vw, 56px);
    }
    
    .container {
        max-width: 1150px;
    }
    
    .container-narrow {
        max-width: 780px;
    }
    
    .container-wide {
        max-width: 1280px;
    }
    
    @media (min-width: 1536px) {
        .container {
            padding-left: 48px;
            padding-right: 48px;
        }
    }
    
.section {
    padding-block: clamp(40px, 8vw, 64px);
}

.section-sm {
    padding-block: clamp(32px, 6vw, 52px);
}
    
@media (max-width: 768px) {
    .section { padding-block: 40px; }
    .section-sm { padding-block: 32px; }
}
    
    /* =================================
       MODERN UI COMPONENTS
    ================================= */
    
    /* Section Headers - Bold & Clear */
    .section-label {
        display: inline-block;
        font-size: 0.875rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: var(--primary);
        background: linear-gradient(135deg, rgba(30, 64, 175, 0.1), rgba(249, 115, 22, 0.1));
        padding: 0.5rem 1.25rem;
        border-radius: 9999px;
        margin-bottom: 1rem;
    }
    
.section-title {
    font-size: clamp(2.25rem, 3vw, 2.75rem);
    font-weight: 800;
    line-height: 1.2;
    letter-spacing: -0.02em;
    color: var(--gray-900);
    margin-bottom: 1.25rem;
}

.section-description {
    font-size: clamp(1.05rem, 2vw, 1.2rem);
    line-height: 1.65;
    color: var(--gray-600);
    max-width: 52ch;
    margin-left: auto;
    margin-right: auto;
}
    
    /* Modern Cards - Elevated & Soft */
.card {
    /* Use background-color so Tailwind gradient utilities (background-image) still apply */
    background-color: white;
    border-radius: 20px;
    padding: 1.5rem 1.5rem;
    box-shadow: none;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    border: 1px solid rgba(17, 24, 39, 0.06);
}

.card:hover {
    transform: translateY(-4px);
    box-shadow: 0 18px 30px -24px rgba(0, 119, 181, 0.35);
    border-color: rgba(0, 119, 181, 0.12);
    }
    
    /* Modern Buttons - Bold & Clear */
.btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.65rem;
    padding: 0.85rem 1.9rem;
    min-height: 48px; /* Touch-friendly (WCAG 2.5.5) */
    min-width: 120px; /* Prevent narrow buttons */
    font-size: 0.95rem;
    font-weight: 600;
    border-radius: 9999px;
    text-transform: uppercase;
    letter-spacing: 0.3em;
    transition: all 0.28s cubic-bezier(0.4, 0, 0.2, 1);
    cursor: pointer;
    border: none;
    text-decoration: none;
}

.btn-primary {
    background: linear-gradient(135deg, #0077B5, #005582);
    color: white;
    border: 1px solid rgba(0, 119, 181, 0.65);
    box-shadow: 0 12px 28px -18px rgba(0, 119, 181, 0.8);
}

.btn-primary:hover {
    transform: translateY(-1px);
    box-shadow: 0 18px 32px -18px rgba(0, 85, 130, 0.9);
}

.btn-secondary {
    background: #F7F8FC;
    color: var(--primary);
    border: 1px solid rgba(30, 64, 175, 0.25);
}

.btn-secondary:hover {
    background: rgba(30, 64, 175, 0.08);
    color: var(--primary);
    transform: translateY(-1px);
}

.btn-outline {
    background: transparent;
    color: var(--gray-900);
    border: 1px solid rgba(17, 24, 39, 0.4);
}

.btn-outline:hover {
    background: var(--gray-900);
    color: white;
}
    
    /* Modern Gradients */
    .gradient-primary {
        background: linear-gradient(135deg, var(--primary) 0%, var(--primary-darker) 100%);
    }
    
    .gradient-secondary {
        background: linear-gradient(135deg, var(--secondary) 0%, var(--secondary-dark) 100%);
    }
    
    .gradient-mesh {
        background: 
            radial-gradient(at 0% 0%, rgba(30, 64, 175, 0.1) 0px, transparent 50%),
            radial-gradient(at 100% 100%, rgba(249, 115, 22, 0.1) 0px, transparent 50%);
    }
    
    /* Glassmorphism Effect */
    .glass {
        background: rgba(255, 255, 255, 0.8);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.3);
    }

    .pill {
        display: inline-flex;
        align-items: center;
        gap: 0.55rem;
        padding: 0.45rem 1.4rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 600;
        letter-spacing: 0.35em;
        text-transform: uppercase;
    }

    .pill-brand {
        background: rgba(30, 64, 175, 0.08);
        color: var(--primary);
        border: 1px solid rgba(30, 64, 175, 0.25);
    }

    .pill-neutral {
        background: rgba(148, 163, 184, 0.12);
        color: var(--gray-500);
        border: 1px solid rgba(148, 163, 184, 0.35);
    }

    .metric-card {
        display: flex;
        flex-direction: column;
        gap: 0.2rem;
    }

    .metric-value {
        font-size: 2.5rem;
        font-weight: 700;
        color: var(--gray-900);
        letter-spacing: -0.05em;
    }

    .metric-label {
        font-size: 0.65rem;
        letter-spacing: 0.35em;
        text-transform: uppercase;
        color: rgba(71, 85, 105, 0.9);
    }

    .icon-ring {
        width: 3.25rem;
        height: 3.25rem;
        border-radius: 9999px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: var(--icon-sheen, rgba(0, 119, 181, 0.1));
        border: 1px solid var(--icon-border, rgba(0, 119, 181, 0.12));
        color: var(--icon-color, var(--primary));
        transition: all 0.3s ease;
    }

    .icon-ring i {
        color: inherit;
        font-size: 1.15rem;
    }

    .icon-ring:hover {
        transform: translateY(-2px);
        background: linear-gradient(135deg, #0077B5, #005582);
        color: white;
        border-color: #0077B5;
        box-shadow: 0 8px 20px rgba(0, 119, 181, 0.3);
    }

    .client-logo-card {
        animation: fadeInUp 0.6s ease-out backwards;
    }

    .client-logo-card .monogram {
        font-size: 2rem;
        font-weight: 700;
        letter-spacing: 0.16em;
        color: rgb(148, 163, 184);
        transition: color 0.3s ease;
    }

    .client-logo-card:hover .monogram {
        color: var(--primary);
    }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Stats */
    .stat-card {
        animation: fadeInScale 0.6s ease-out both;
    }

    .stat-surface {
        position: relative;
        padding: 2.25rem;
        border-radius: 1.75rem;
        background: rgba(255, 255, 255, 0.92);
        border: 1px solid rgba(15, 23, 42, 0.06);
        backdrop-filter: blur(12px);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        overflow: hidden;
    }

    .stat-card:hover .stat-surface {
        transform: translateY(-6px);
        box-shadow: 0 25px 60px -35px rgba(15, 23, 42, 0.35);
        border-color: rgba(15, 23, 42, 0.12);
    }

    .stat-surface::after {
        content: '';
        position: absolute;
        inset: 0;
        background: radial-gradient(circle at 80% 0%, var(--stat-accent-soft, rgba(30, 64, 175, 0.16)), transparent 60%);
        pointer-events: none;
    }

    .stat-icon {
        width: 64px;
        height: 64px;
        border-radius: 1.5rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #0077B5, #005582);
        box-shadow: 0 10px 25px rgba(0, 119, 181, 0.25);
        position: relative;
        z-index: 1;
    }

    .stat-value {
        font-size: clamp(2.75rem, 4vw, 3.5rem);
        font-weight: 800;
        color: var(--stat-accent, var(--primary));
        display: flex;
        align-items: baseline;
        justify-content: center;
        gap: 0.3rem;
        margin-bottom: 0.75rem;
        position: relative;
        z-index: 1;
    }

    .counter-animation {
        display: inline-block;
        font-variant-numeric: tabular-nums;
    }

    .stat-suffix {
        font-size: 1.25rem;
        font-weight: 700;
        color: inherit;
    }

    .stat-label {
        font-size: 1.05rem;
        font-weight: 600;
        color: var(--gray-900);
        margin-bottom: 0.35rem;
    }

    .stat-description {
        color: var(--gray-600);
        font-size: 0.95rem;
        margin: 0;
    }

    @keyframes fadeInScale {
        from {
            opacity: 0;
            transform: scale(0.9);
        }
        to {
            opacity: 1;
            transform: scale(1);
        }
    }

    /* Testimonials */
    .testimonial-slide {
        animation: testimonialFade 0.6s ease-out both;
    }

    .testimonial-card {
        position: relative;
        padding: clamp(2rem, 4vw, 2.75rem);
        border-radius: 2rem;
        background: rgba(255, 255, 255, 0.95);
        border: 1px solid rgba(30, 64, 175, 0.12);
        box-shadow: 0 25px 65px -35px rgba(15, 23, 42, 0.35);
        overflow: hidden;
        transition: transform 0.3s ease, border-color 0.3s ease;
    }

    .testimonial-card::after {
        content: '';
        position: absolute;
        inset: 0;
        background: radial-gradient(circle at 85% 15%, rgba(30, 64, 175, 0.08), transparent 45%);
        pointer-events: none;
    }

    .testimonial-card:hover {
        transform: translateY(-6px);
        border-color: rgba(30, 64, 175, 0.35);
    }

    .testimonial-quote {
        position: absolute;
        top: 1.5rem;
        right: 1.5rem;
        font-size: 6rem;
        color: rgba(30, 64, 175, 0.08);
        line-height: 1;
        z-index: 0;
    }

    .testimonial-avatar {
        width: 3.5rem;
        height: 3.5rem;
        border-radius: 9999px;
        background: linear-gradient(135deg, #0077B5, #005582);
        color: white;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        box-shadow: 0 10px 20px rgba(0, 119, 181, 0.3);
    }

    .testimonial-verified {
        position: absolute;
        bottom: -4px;
        right: -4px;
        width: 22px;
        height: 22px;
        border-radius: 9999px;
        background: #10B981;
        border: 2px solid white;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .carousel-nav {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        width: 44px;
        height: 44px;
        border-radius: 9999px;
        border: 1px solid rgba(15, 23, 42, 0.12);
        background: white;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--gray-700);
        transition: all 0.3s ease;
        box-shadow: 0 10px 25px rgba(15, 23, 42, 0.12);
    }

    .carousel-nav.left {
        left: -1rem;
    }

    .carousel-nav.right {
        right: -1rem;
    }

    .carousel-nav:hover {
        background: linear-gradient(135deg, #0077B5, #005582);
        color: white;
        border-color: #0077B5;
        transform: translateY(-50%) scale(1.05);
    }

    .testimonial-dot {
        width: 12px;
        height: 12px;
        border-radius: 9999px;
        background: rgba(148, 163, 184, 0.6);
        border: none;
        transition: all 0.3s ease;
    }

    .testimonial-dot.active {
        width: 32px;
        background: var(--primary);
    }

    @media (max-width: 640px) {
        .carousel-nav.left {
            left: 0;
        }

        .carousel-nav.right {
            right: 0;
        }
    }

    @keyframes testimonialFade {
        from { opacity: 0; }
        to { opacity: 1; }
    }

    /* FAQ */
    .faq-item {
        border: 1px solid rgba(15, 23, 42, 0.08);
        border-radius: 1.5rem;
        background: white;
        box-shadow: 0 15px 35px -30px rgba(15, 23, 42, 0.45);
        transition: border-color 0.25s ease, box-shadow 0.25s ease;
    }

    .faq-item-open {
        border-color: rgba(30, 64, 175, 0.55);
        box-shadow: 0 25px 60px -35px rgba(30, 64, 175, 0.35);
    }

    .faq-trigger {
        width: 100%;
        text-align: left;
        padding: 1.5rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        font-weight: 600;
        background: transparent;
        border: none;
        cursor: pointer;
    }

    .faq-question {
        font-size: 1.05rem;
        color: var(--gray-900);
    }

    .faq-icon {
        transition: transform 0.3s ease, color 0.3s ease;
        color: var(--primary);
    }

    .faq-item-open .faq-icon {
        transform: rotate(180deg);
        color: var(--primary-dark);
    }

    .faq-content {
        padding: 0 1.5rem 1.5rem;
        color: var(--gray-600);
        border-top: 1px solid rgba(15, 23, 42, 0.08);
    }

    .faq-content p {
        margin-top: 1rem;
        margin-bottom: 0;
    }
    
    /* Modern Navbar */
    .navbar {
        position: fixed;
        top: 0;
        width: 100%;
        background: linear-gradient(135deg, #0077B5 0%, #005582 100%);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        border-bottom: 1px solid rgba(255, 255, 255, 0.15);
        z-index: 1000;
        transition: background 0.5s cubic-bezier(0.4, 0, 0.2, 1), 
                    box-shadow 0.4s cubic-bezier(0.4, 0, 0.2, 1),
                    border-color 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }
    
    .navbar.scrolled {
        background: linear-gradient(135deg, #005582 0%, #003d5c 100%);
        box-shadow: 0 6px 16px rgba(0, 0, 0, 0.25);
        border-bottom: 1px solid rgba(255, 255, 255, 0.25);
    }
    
    /* Service Cards - Modern Grid */
.service-card {
    position: relative;
    background: white;
    border-radius: 20px;
    padding: 1.5rem;
    box-shadow: none;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    overflow: hidden;
    border: 1px solid rgba(17, 24, 39, 0.06);
}
    
    .service-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, var(--primary), var(--secondary));
        opacity: 0;
        transition: opacity 0.4s ease;
    }
    
.service-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 18px 30px -24px rgba(30, 64, 175, 0.35);
    border-color: rgba(30, 64, 175, 0.12);
}
    
    .service-card:hover::before {
        opacity: 1;
    }
    
.service-icon {
    width: 56px;
    height: 56px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, rgba(30, 64, 175, 0.1), rgba(249, 115, 22, 0.1));
    border-radius: 18px;
    margin-bottom: 1.25rem;
    font-size: 1.75rem;
    color: var(--primary);
    transition: all 0.4s ease;
}
    
    .service-card:hover .service-icon {
        transform: scale(1.1) rotate(5deg);
        background: linear-gradient(135deg, var(--primary), var(--secondary));
        color: white;
        box-shadow: 0 8px 20px rgba(30, 64, 175, 0.3);
    }
    
    /* Underline Link Animation */
    .underline-link {
        position: relative;
        display: inline-block;
    }
    
    .underline-link::after {
        content: '';
        position: absolute;
        bottom: -2px;
        left: 0;
        width: 100%;
        height: 2px;
        background: currentColor;
        transform: scaleX(0);
        transform-origin: right;
        transition: transform 0.3s ease;
    }
    
    .underline-link:hover::after {
        transform: scaleX(1);
        transform-origin: left;
    }
    
    /* Utility Classes */
    .text-gradient {
        background: linear-gradient(135deg, var(--primary), var(--secondary));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }
    
    /* Smooth Scrolling */
    html {
        scroll-behavior: smooth;
    }
    
    /* Selection Color */
    ::selection {
        background: var(--primary);
        color: white;
    }
    
    /* Focus Styles */
    *:focus-visible {
        outline: 2px solid var(--primary);
        outline-offset: 2px;
    }
    
    /* Mobile Menu Styles */
    .mobile-menu {
        transform: translateX(100%);
        transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: -4px 0 24px rgba(0, 0, 0, 0.15);
        background: linear-gradient(135deg, #0077B5 0%, #005582 100%);
        overflow-y: auto;
    }
    
    .mobile-menu.active {
        transform: translateX(0);
    }
    
    body.mobile-menu-open {
        overflow: hidden;
    }
    
    body.mobile-menu-open::before {
        content: '';
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.5);
        z-index: 1000;
        animation: fadeIn 0.3s ease;
        backdrop-filter: blur(2px);
    }
    
    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
    
    /* Mobile Menu Links */
    .mobile-menu a {
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    /* Safe text wrapping inside cards/sections to prevent overflow */
    .card { 
        overflow-wrap: anywhere; 
        word-break: break-word; 
        hyphens: auto; 
    }
    .card .flex > * { min-width: 0; }
    .prose { 
        overflow-wrap: anywhere; 
        word-break: break-word; 
        hyphens: auto; 
    }
    .card img, .card svg { max-width: 100%; height: auto; }

    /* Ensure headings respect white text contexts (e.g., dark gradient sections/cards) */
    .text-white h1,
    .text-white h2,
    .text-white h3,
    .text-white h4,
    .text-white h5,
    .text-white h6 { color: #ffffff; }
    
    .mobile-menu a:not(.btn):hover {
        transform: translateX(4px);
    }
    
    .mobile-menu .btn:hover {
        transform: scale(1.02);
    }
    
    /* Hamburger Menu Animation */
    .hamburger-menu {
        position: relative;
        width: 24px;
        height: 20px;
        cursor: pointer;
    }
    
    /* Floating Action Buttons */
    .fab-group {
        position: fixed;
        bottom: 24px;
        right: 24px;
        display: flex;
        flex-direction: column;
        gap: 12px;
        z-index: 999;
    }
    
    .fab {
        width: 56px;
        height: 56px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        text-decoration: none;
        color: white;
        border: none;
        cursor: pointer;
    }
    
    .fab:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.25);
    }
    
    .fab-whatsapp {
        background: linear-gradient(135deg, #25D366 0%, #128C7E 100%);
    }
    
    .fab-phone {
        background: linear-gradient(135deg, #0077B5 0%, #005582 100%);
    }
    
    .fab-back-to-top {
        background: linear-gradient(135deg, #6B7280 0%, #374151 100%);
        opacity: 0;
        visibility: hidden;
        transition: all 0.3s ease;
    }
    
    .fab-back-to-top.show {
        opacity: 1;
        visibility: visible;
    }
    
    /* Mobile Responsiveness */
    @media (max-width: 768px) {
        .fab-group {
            bottom: 16px;
            right: 16px;
        }
        
        .fab {
            width: 48px;
            height: 48px;
        }
        
        .fab i {
            font-size: 18px;
        }
    }
    
    /* Footer Specific Styles */
    footer {
        width: 100%;
        max-width: 100vw;
        overflow-x: hidden;
    }
    
    footer * {
        word-wrap: break-word;
        overflow-wrap: break-word;
    }
    
    footer .container {
        width: 100%;
        padding-left: 1rem;
        padding-right: 1rem;
    }
    
    @media (min-width: 768px) {
        footer .container {
            padding-left: 2rem;
            padding-right: 2rem;
        }
    }
    
    footer input,
    footer button {
        max-width: 100%;
    }
    
    /* Mobile Performance Optimizations */
    @media (max-width: 768px) {
        /* Reduce animation complexity on mobile */
        * {
            animation-duration: 0.5s !important; /* Faster animations */
        }
        
        /* Simplify hover effects on touch devices */
        .btn:hover,
        .card:hover,
        .service-card:hover {
            transform: none !important; /* Disable hover transforms */
        }
        
        /* Optimize font rendering */
        body {
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            text-rendering: optimizeSpeed; /* Faster text rendering */
        }
        
        /* Reduce blur effects */
        .glass {
            backdrop-filter: blur(8px); /* Reduce from 20px */
            -webkit-backdrop-filter: blur(8px);
        }
    }
    
    /* Respect user's motion preferences */
    @media (prefers-reduced-motion: reduce) {
        *,
        *::before,
        *::after {
            animation-duration: 0.01ms !important;
            animation-iteration-count: 1 !important;
            transition-duration: 0.01ms !important;
            scroll-behavior: auto !important;
        }
    }
</style>
