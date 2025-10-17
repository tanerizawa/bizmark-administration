{{-- MODERN PROFESSIONAL DESIGN SYSTEM 2025 --}}
{{-- Complete Redesign - Bold, Elegant, Sophisticated --}}

<script>
    tailwind.config = {
        darkMode: 'class',
        theme: {
            extend: {
                colors: {
                    primary: {
                        DEFAULT: '#1E40AF',
                        50: '#EFF6FF',
                        100: '#DBEAFE',
                        600: '#1E40AF',
                        700: '#1E3A8A',
                        800: '#1E3A8A',
                        900: '#172554',
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
        /* Premium Color Palette */
        --primary: #1E40AF;
        --primary-dark: #1E3A8A;
        --primary-darker: #172554;
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
        margin-bottom: 1.5rem;
        letter-spacing: -0.025em;
    }
    
    h1 { font-size: 4rem; font-weight: 900; }      /* 64px HERO */
    h2 { font-size: 3rem; font-weight: 800; }      /* 48px SECTIONS */
    h3 { font-size: 2rem; font-weight: 700; }      /* 32px */
    h4 { font-size: 1.5rem; font-weight: 700; }    /* 24px */
    
    p {
        margin-bottom: 1.5rem;
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
    padding-block: clamp(64px, 14vw, 96px);
}

.section-sm {
    padding-block: clamp(48px, 10vw, 72px);
}
    
@media (max-width: 768px) {
    .section { padding-block: 52px; }
    .section-sm { padding-block: 38px; }
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
    background: white;
    border-radius: 20px;
    padding: 1.75rem;
    box-shadow: none;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    border: 1px solid rgba(17, 24, 39, 0.06);
}

.card:hover {
    transform: translateY(-4px);
    box-shadow: 0 18px 30px -24px rgba(30, 64, 175, 0.35);
    border-color: rgba(30, 64, 175, 0.12);
    }
    
    /* Modern Buttons - Bold & Clear */
.btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.65rem;
    padding: 0.9rem 1.75rem;
    font-size: 1rem;
    font-weight: 600;
    border-radius: 9999px;
    transition: all 0.28s cubic-bezier(0.4, 0, 0.2, 1);
    cursor: pointer;
    border: none;
    text-decoration: none;
}

.btn-primary {
    position: relative;
    background: linear-gradient(135deg, #4F46E5, #2563EB);
    color: white;
    box-shadow: none;
    border: 1px solid rgba(79, 70, 229, 0.6);
}

.btn-primary:hover {
    transform: translateY(-1px);
    box-shadow: 0 12px 20px -16px rgba(79, 70, 229, 0.6);
}

.btn-secondary {
    background: #F6F7FB;
    color: var(--primary);
    border: 1px solid rgba(30, 64, 175, 0.3);
    box-shadow: none;
}

.btn-secondary:hover {
    background: var(--primary);
    color: white;
    transform: translateY(-1px);
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
    
    /* Modern Navbar */
    .navbar {
        position: fixed;
        top: 0;
        width: 100%;
        background: rgba(255, 255, 255, 0.9);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        border-bottom: 1px solid rgba(0, 0, 0, 0.06);
        z-index: 1000;
        transition: all 0.3s ease;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
    }
    
    .navbar.scrolled {
        background: rgba(255, 255, 255, 0.95);
        box-shadow: var(--shadow-soft);
    }
    
    /* Service Cards - Modern Grid */
.service-card {
    position: relative;
    background: white;
    border-radius: 22px;
    padding: 2.25rem;
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
</style>
