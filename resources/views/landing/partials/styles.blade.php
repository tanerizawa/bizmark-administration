<script>
    tailwind.config = {
        darkMode: 'class',
        theme: {
            extend: {
                colors: {
                    // Modern Professional Color Palette 2025
                    'primary': {
                        DEFAULT: '#1E40AF',  // Deep Blue
                        '50': '#EFF6FF',
                        '100': '#DBEAFE',
                        '200': '#BFDBFE',
                        '300': '#93C5FD',
                        '400': '#60A5FA',
                        '500': '#3B82F6',
                        '600': '#1E40AF',
                        '700': '#1E3A8A',
                        '800': '#1E3A8A',
                        '900': '#1E3A8A',
                        'dark': '#1E3A8A',
                        'darker': '#172554',
                    },
                    'secondary': {
                        DEFAULT: '#F97316',  // Vibrant Orange
                        '50': '#FFF7ED',
                        '100': '#FFEDD5',
                        '200': '#FED7AA',
                        '300': '#FDBA74',
                        '400': '#FB923C',
                        '500': '#F97316',
                        '600': '#EA580C',
                        '700': '#C2410C',
                        '800': '#9A3412',
                        '900': '#7C2D12',
                        'dark': '#EA580C',
                    },
                    'accent': '#10B981',  // Fresh Green
                    'accent-dark': '#059669',
                }
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
        /* Modern Color System */
        --primary: #1E40AF;
        --primary-dark: #1E3A8A;
        --primary-darker: #172554;
        --secondary: #F97316;
        --secondary-dark: #EA580C;
        --accent: #10B981;
        
        /* Neutral Scale */
        --gray-50: #F9FAFB;
        --gray-100: #F3F4F6;
        --gray-200: #E5E7EB;
        --gray-300: #D1D5DB;
        --gray-400: #9CA3AF;
        --gray-500: #6B7280;
        --gray-600: #4B5563;
        --gray-700: #374151;
        --gray-800: #1F2937;
        --gray-900: #111827;
        
        /* Spacing System */
        --section-spacing: 120px;
        --section-spacing-sm: 80px;
        --container-padding: 24px;
        
        /* Border Radius */
        --radius-sm: 12px;
        --radius-md: 16px;
        --radius-lg: 24px;
        --radius-xl: 32px;
        
        /* Shadows - Elevated Design */
        --shadow-sm: 0 2px 4px rgba(0, 0, 0, 0.04);
        --shadow-md: 0 4px 12px rgba(0, 0, 0, 0.08);
        --shadow-lg: 0 8px 24px rgba(0, 0, 0, 0.12);
        --shadow-xl: 0 12px 32px rgba(0, 0, 0, 0.16);
        --shadow-2xl: 0 20px 48px rgba(0, 0, 0, 0.20);
    }
    
    body {
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        background: #FFFFFF;
        color: var(--gray-900);
        overflow-x: hidden;
        font-size: 18px;
        line-height: 1.7;
        -webkit-font-smoothing: antialiased;
        -moz-osx-font-smoothing: grayscale;
        font-weight: 400;
    }
    
    /* Modern Typography System - Bold & Confident */
    h1, h2, h3, h4, h5, h6 {
        font-weight: 800;
        line-height: 1.2;
        color: var(--gray-900);
        margin-bottom: 1.5rem;
        letter-spacing: -0.025em;
    }
    
    h1 { 
        font-size: 4rem;      /* 64px - BOLD HERO */
        font-weight: 900;
    }
    h2 { 
        font-size: 3rem;      /* 48px - SECTION HEADERS */
        font-weight: 800;
    }
    h3 { 
        font-size: 2rem;      /* 32px */
        font-weight: 700;
    }
    h4 { 
        font-size: 1.5rem;    /* 24px */
        font-weight: 700;
    }
    h5 { 
        font-size: 1.25rem;   /* 20px */
        font-weight: 600;
    }
    h6 { 
        font-size: 1.125rem;  /* 18px */
        font-weight: 600;
    }
    
    p {
        margin-bottom: 1.5rem;
        color: var(--gray-600);
        line-height: 1.7;
        font-size: 1.125rem;  /* 18px - More readable */
    }
    
    .lead {
        font-size: 1.375rem;  /* 22px */
        line-height: 1.6;
        color: var(--gray-700);
        font-weight: 400;
    }
    
    a {
        color: var(--primary);
        text-decoration: none;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    a:hover {
        color: var(--primary-dark);
    }
    
    /* Responsive Typography */
    @media (max-width: 1024px) {
        h1 { font-size: 3rem; }    /* 48px */
        h2 { font-size: 2.5rem; }  /* 40px */
        h3 { font-size: 1.75rem; } /* 28px */
    }
    
    @media (max-width: 768px) {
        body { font-size: 16px; }
        h1 { font-size: 2.5rem; }  /* 40px */
        h2 { font-size: 2rem; }    /* 32px */
        h3 { font-size: 1.5rem; }  /* 24px */
        p { font-size: 1rem; }     /* 16px */
        .lead { font-size: 1.125rem; } /* 18px */
    }
    
    /* Navbar Styling - Light Magazine Style */
    .navbar {
        position: fixed;
        top: 0;
        width: 100%;
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        border-bottom: 1px solid #E5E7EB;
        z-index: 1000;
        transition: all 0.3s ease;
        box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.05);
    }
    
    .navbar.scrolled {
        background: rgba(255, 255, 255, 0.98);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        border-bottom-color: #D1D5DB;
    }
    
    /* Container Widths for Magazine Layout - More Compact */
    .container-narrow {
        max-width: 680px;
        margin: 0 auto;
        padding: 0 1.25rem;
    }
    
    .container-wide {
        max-width: 1140px;
        margin: 0 auto;
        padding: 0 1.25rem;
    }
    
    .container-full {
        max-width: 1320px;
        margin: 0 auto;
        padding: 0 1.25rem;
    }
    
    /* Section Spacing - More Compact & Elegant */
    .section {
        padding: 4rem 0;
    }
    
    .section-lg {
        padding: 5rem 0;
    }
    
    @media (max-width: 768px) {
        .section { padding: 2.5rem 0; }
        .section-lg { padding: 3.5rem 0; }
    }
    
    /* Hero Section - Magazine Style */
    .hero-gradient {
        background: linear-gradient(135deg, #F5F7FA 0%, #FFFFFF 50%, #F9FAFB 100%);
        position: relative;
        overflow: hidden;
        border-bottom: 1px solid #E5E7EB;
    }
    
    .hero-gradient::before {
        content: '';
        position: absolute;
        width: 600px;
        height: 600px;
        background: radial-gradient(circle, rgba(0, 102, 204, 0.08) 0%, transparent 70%);
        top: -200px;
        right: -200px;
        animation: float 20s infinite ease-in-out;
    }
    
    .hero-gradient::after {
        content: '';
        position: absolute;
        width: 500px;
        height: 500px;
        background: radial-gradient(circle, rgba(52, 199, 89, 0.06) 0%, transparent 70%);
        bottom: -150px;
        left: -150px;
        animation: float 15s infinite ease-in-out reverse;
    }
    
    @keyframes float {
        0%, 100% { transform: translate(0, 0) rotate(0deg); opacity: 1; }
        50% { transform: translate(50px, 50px) rotate(90deg); opacity: 0.8; }
    }
    
    /* Card Styles - Elegant Magazine Cards */
    .card {
        background: #FFFFFF;
        border-radius: 16px;
        border: 1px solid #E5E7EB;
        padding: 1.75rem;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.08), 0 1px 2px 0 rgba(0, 0, 0, 0.02);
    }
    
    .card:hover {
        transform: translateY(-6px);
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        border-color: #0066CC;
    }
    
    .card-image {
        border-radius: 12px;
        overflow: hidden;
        margin-bottom: 1.25rem;
    }
    
    .card-category {
        display: inline-block;
        font-size: 0.8125rem;
        font-weight: 700;
        color: #0066CC;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        margin-bottom: 0.625rem;
    }
    
    .card-title {
        font-size: 1.375rem;
        font-weight: 700;
        color: #1A1A1A;
        margin-bottom: 0.875rem;
        line-height: 1.25;
    }
    
    .card-excerpt {
        color: #6B7280;
        line-height: 1.5;
        margin-bottom: 1.25rem;
        font-size: 0.9375rem;
    }
    
    .card-meta {
        display: flex;
        align-items: center;
        gap: 0.875rem;
        font-size: 0.8125rem;
        color: #9CA3AF;
    }
    
    /* Button Styles - Elegant & Modern */
    .btn-primary {
        background: linear-gradient(135deg, #0066CC 0%, #0052A3 100%);
        color: #FFFFFF;
        padding: 0.875rem 2rem;
        border-radius: 10px;
        font-weight: 600;
        font-size: 0.9375rem;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        display: inline-block;
        text-decoration: none;
        border: none;
        box-shadow: 0 4px 6px -1px rgba(0, 102, 204, 0.2), 0 2px 4px -1px rgba(0, 102, 204, 0.1);
        cursor: pointer;
    }
    
    .btn-primary:hover {
        background: linear-gradient(135deg, #0052A3 0%, #003d7a 100%);
        transform: translateY(-2px);
        box-shadow: 0 10px 15px -3px rgba(0, 102, 204, 0.3), 0 4px 6px -2px rgba(0, 102, 204, 0.15);
    }
    
    .btn-secondary {
        background: #FFFFFF;
        color: #0066CC;
        padding: 0.875rem 2rem;
        border-radius: 10px;
        font-weight: 600;
        font-size: 0.9375rem;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        display: inline-block;
        text-decoration: none;
        border: 2px solid #0066CC;
        cursor: pointer;
        box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.08);
    }
    
    .btn-secondary:hover {
        background: #0066CC;
        color: #FFFFFF;
        transform: translateY(-2px);
        box-shadow: 0 10px 15px -3px rgba(0, 102, 204, 0.3);
    }
    
    .btn-accent {
        background: linear-gradient(135deg, #FF6B35 0%, #E85A28 100%);
        color: #FFFFFF;
        padding: 0.875rem 2rem;
        border-radius: 10px;
        font-weight: 600;
        font-size: 0.9375rem;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        display: inline-block;
        text-decoration: none;
        border: none;
        box-shadow: 0 4px 6px -1px rgba(255, 107, 53, 0.2);
        cursor: pointer;
    }
    
    .btn-accent:hover {
        background: linear-gradient(135deg, #E85A28 0%, #d4491c 100%);
        transform: translateY(-2px);
        box-shadow: 0 10px 15px -3px rgba(255, 107, 53, 0.3);
    }
    
    /* Link Styles - Elegant */
    .link-primary {
        color: #0066CC;
        font-weight: 600;
        text-decoration: none;
        position: relative;
        display: inline-block;
        transition: color 0.3s ease;
    }
    
    .link-primary::after {
        content: '';
        position: absolute;
        bottom: -2px;
        left: 0;
        width: 100%;
        height: 2px;
        background: #0066CC;
        transform: scaleX(0);
        transform-origin: right;
        transition: transform 0.3s ease;
    }
    
    .link-primary:hover {
        color: #0052A3;
    }
    
    .link-primary:hover::after {
        transform: scaleX(1);
        transform-origin: left;
    }
        cursor: pointer;
        box-shadow: 0 4px 12px rgba(0, 102, 204, 0.25);
    }
    
    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(0, 102, 204, 0.35);
        background: linear-gradient(135deg, #0052A3 0%, #003D7A 100%);
    }
    
    .btn-secondary {
        background: #FFFFFF;
        color: #0066CC;
        padding: 1rem 2.5rem;
        border-radius: 8px;
        font-weight: 600;
        font-size: 1rem;
        transition: all 0.3s ease;
        display: inline-block;
        text-decoration: none;
        border: 2px solid #0066CC;
        cursor: pointer;
    }
    
    .btn-secondary:hover {
        background: #0066CC;
        color: #FFFFFF;
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(0, 102, 204, 0.25);
    }
    
    .btn-accent {
        background: linear-gradient(135deg, #FF6B35 0%, #E85A28 100%);
        color: #FFFFFF;
        padding: 1rem 2.5rem;
        border-radius: 8px;
        font-weight: 600;
        font-size: 1rem;
        transition: all 0.3s ease;
        display: inline-block;
        text-decoration: none;
        border: none;
        cursor: pointer;
        box-shadow: 0 4px 12px rgba(255, 107, 53, 0.25);
    }
    
    .btn-accent:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(255, 107, 53, 0.35);
    }
    
    /* Link Styles */
    .link-primary {
        color: #0066CC;
        font-weight: 600;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.2s ease;
    }
    
    .link-primary:hover {
        color: #0052A3;
        gap: 0.75rem;
    }
    
    .link-primary::after {
        content: '→';
        transition: transform 0.2s ease;
    }
    
    .link-primary:hover::after {
        transform: translateX(4px);
    }
    
    /* Stats/Counter Styles */
    .counter {
        font-size: 3rem;
        font-weight: 800;
        background: linear-gradient(135deg, #0066CC 0%, #34C759 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        line-height: 1;
    }
    
    /* Service Card - Compact & Elegant */
    .service-card {
        background: #FFFFFF;
        border-radius: 16px;
        border: 1px solid #E5E7EB;
        padding: 2rem;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
        box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.08), 0 1px 2px 0 rgba(0, 0, 0, 0.02);
    }
    
    .service-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 3px;
        background: linear-gradient(90deg, #0066CC, #34C759);
        transform: scaleX(0);
        transform-origin: left;
        transition: transform 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    .service-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        border-color: #0066CC;
    }
    
    .service-card:hover::before {
        transform: scaleX(1);
    }
    
    .service-icon {
        width: 56px;
        height: 56px;
        background: linear-gradient(135deg, #F0F7FF 0%, #E6F4F1 100%);
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.75rem;
        color: #0066CC;
        margin-bottom: 1.25rem;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    .service-card:hover .service-icon {
        transform: scale(1.15) rotate(5deg);
        background: linear-gradient(135deg, #0066CC 0%, #34C759 100%);
        color: #FFFFFF;
        box-shadow: 0 8px 16px -4px rgba(0, 102, 204, 0.4);
    }
    
    /* Floating Action Buttons - Light Theme */
    .fab-group {
        position: fixed;
        right: 2rem;
        bottom: 2rem;
        display: flex;
        flex-direction: column;
        gap: 1rem;
        z-index: 998;
    }
    
    .fab {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        color: #fff;
        cursor: pointer;
        z-index: 998;
        transition: all 0.3s ease;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        text-decoration: none;
    }
    
    .fab:hover {
        transform: scale(1.15);
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.25);
    }
    
    .fab-whatsapp {
        background: linear-gradient(135deg, #25D366 0%, #128C7E 100%);
        animation: pulse 2s infinite;
    }
    
    .fab-phone {
        background: linear-gradient(135deg, #0066CC 0%, #0052A3 100%);
    }
    
    .fab-back-to-top {
        background: #FFFFFF;
        border: 2px solid #E5E7EB;
        color: #1A1A1A;
        opacity: 0;
        visibility: hidden;
        transform: translateY(20px);
        transition: all 0.3s ease;
    }
    
    .fab-back-to-top.show {
        opacity: 1;
        visibility: visible;
        transform: translateY(0);
    }
    
    .fab-back-to-top:hover {
        background: #0066CC;
        border-color: #0066CC;
        color: #FFFFFF;
    }
    
    /* Pulse Animation */
    @keyframes pulse {
        0%, 100% { transform: scale(1); box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15); }
        50% { transform: scale(1.05); box-shadow: 0 6px 20px rgba(37, 211, 102, 0.4); }
    }
    
    /* Mobile FAB Adjustments */
    @media (max-width: 768px) {
        .fab-group {
            right: 1rem;
            bottom: 1rem;
            gap: 0.75rem;
        }
        
        .fab {
            width: 56px;
            height: 56px;
            font-size: 1.25rem;
        }
    }
        }
        
        .fab {
            width: 50px;
            height: 50px;
            font-size: 1.25rem;
        }
    }
    
    /* Hide FAB when mobile menu is open */
    body.mobile-menu-open .fab-group {
        display: none;
    }
    
    /* Article Card Hover Effect */
    .article-card {
        transition: all 0.3s ease;
        overflow: hidden;
    }
    
    .article-card:hover {
        transform: translateY(-10px);
    }
    
    .article-card img {
        transition: transform 0.5s ease;
    }
    
    .article-card:hover img {
        transform: scale(1.1);
    }
    
    /* Testimonial Card */
    .testimonial-card {
        min-height: 300px;
    }
    
    /* Process Timeline */
    .timeline-step {
        position: relative;
    }
    
    .timeline-step::after {
        content: '';
        position: absolute;
        top: 50%;
        left: 100%;
        width: 100%;
        height: 2px;
        background: linear-gradient(90deg, #007AFF, transparent);
    }
    
    .timeline-step:last-child::after {
        display: none;
    }
    
    /* Smooth Scroll */
    html {
        scroll-behavior: smooth;
    }
    
    /* Loading Animation */
    .loading {
        border: 3px solid rgba(255, 255, 255, 0.1);
        border-top: 3px solid #007AFF;
        border-radius: 50%;
        width: 40px;
        height: 40px;
        animation: spin 1s linear infinite;
    }
    
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    
    /* Mobile Menu */
    .mobile-menu {
        transform: translateX(100%);
        transition: transform 0.3s ease;
    }
    
    .mobile-menu.active {
        transform: translateX(0);
    }
    
    /* Loading Screen */
    #loading-screen {
        position: fixed;
        inset: 0;
        background: #000000;
        z-index: 9999;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: opacity 0.5s ease, visibility 0.5s ease;
    }
    
    #loading-screen.hidden {
        opacity: 0;
        visibility: hidden;
    }
    
    .loader {
        width: 48px;
        height: 48px;
        border: 3px solid rgba(0, 122, 255, 0.2);
        border-top-color: #007AFF;
        border-radius: 50%;
        animation: spin 0.8s linear infinite;
    }
    
    /* Skeleton Loading */
    .skeleton {
        background: linear-gradient(90deg, 
            rgba(255,255,255,0.05) 25%, 
            rgba(255,255,255,0.1) 50%, 
            rgba(255,255,255,0.05) 75%
        );
        background-size: 200% 100%;
        animation: skeleton-loading 1.5s ease-in-out infinite;
        border-radius: 8px;
    }
    
    @keyframes skeleton-loading {
        0% { background-position: 200% 0; }
        100% { background-position: -200% 0; }
    }
    
    .skeleton-text {
        height: 1rem;
        margin-bottom: 0.5rem;
    }
    
    .skeleton-title {
        height: 2rem;
        width: 70%;
        margin-bottom: 1rem;
    }
    
    .skeleton-image {
        height: 200px;
        width: 100%;
    }
    
    /* Responsive Utilities */
    @media (max-width: 768px) {
        .counter {
            font-size: 2rem;
        }
        
        .hero-gradient::before,
        .hero-gradient::after {
            display: none;
        }
    }
</style>
