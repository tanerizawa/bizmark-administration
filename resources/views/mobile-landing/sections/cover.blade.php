@php
    $currentLocale = app()->getLocale();
    $isEnglish = $currentLocale === 'en';
@endphp

<!-- Hero Section - Desktop Style Adapted for Mobile -->
<section id="home" class="hero-gradient pt-32 pb-20 px-4 min-h-screen flex items-center" role="banner">
    <div class="container mx-auto text-center max-w-5xl relative z-10">
        <!-- Logo -->
        <div class="logo-container mb-8" aria-label="{{ $isEnglish ? 'Bizmark.ID Logo' : 'Logo Bizmark.ID' }}">
            <img src="{{ asset('images/logo-bizmark.svg') }}" 
                 alt="{{ $isEnglish ? 'BizMark Indonesia - Licensing Consultant' : 'BizMark Indonesia - Konsultan Perizinan' }}" 
                 class="w-24 h-24 mx-auto filter drop-shadow-lg">
        </div>
        
        <!-- Main Headline -->
        <h1 class="text-3xl sm:text-4xl md:text-5xl font-extrabold mb-6 leading-tight text-white">
            {{ $isEnglish ? 'Business Licensing' : 'Solusi' }} 
            <span class="text-gradient bg-clip-text text-transparent bg-gradient-to-r from-blue-400 to-green-400">
                {{ $isEnglish ? 'Management Solutions' : 'Manajemen Perizinan' }}
            </span>
            <br>{{ $isEnglish ? '& Trusted Business Consultant' : '& Konsultan Bisnis Terpercaya' }}
        </h1>
        
        <!-- Subtitle -->
        <p class="text-base sm:text-lg md:text-xl mb-8 max-w-3xl mx-auto px-4 text-gray-300">
            {{ $isEnglish 
                ? 'Administrative digitalization, professional OSS, AMDAL, UKL-UPL, PBG, SLF licensing services, and business consulting for modern companies in Indonesia. Fast, transparent, and trusted processes.' 
                : 'Digitalisasi administrasi, layanan perizinan profesional OSS, AMDAL, UKL-UPL, PBG, SLF, dan konsultasi bisnis untuk perusahaan modern di Indonesia. Proses cepat, transparan, dan terpercaya.'
            }}
        </p>
        
        <!-- CTA Buttons -->
        <div class="flex flex-col sm:flex-row justify-center items-center gap-4 px-4">
            <button @click="window.dispatchEvent(new CustomEvent('open-ai-estimator'))" 
                    class="btn-primary w-full sm:w-auto"
                    aria-label="{{ $isEnglish ? 'Get cost estimate with AI' : 'Dapatkan estimasi biaya perizinan dengan AI' }}">
                <i class="fas fa-calculator mr-2" aria-hidden="true"></i>
                {{ __('mobile.hero.cta_primary') }}
            </button>
            
            <a href="#services" 
               class="btn-secondary w-full sm:w-auto" 
               aria-label="{{ $isEnglish ? 'Learn more about our services' : 'Pelajari lebih lanjut tentang layanan kami' }}">
                <i class="fas fa-info-circle mr-2" aria-hidden="true"></i>
                {{ __('mobile.hero.cta_tertiary') }}
            </a>
        </div>
    </div>
</section>

<style>
    .hero-gradient {
        background: linear-gradient(135deg, #000000 0%, #1a1a2e 50%, #16213e 100%);
        position: relative;
        overflow: hidden;
    }
    
    .hero-gradient::before {
        content: '';
        position: absolute;
        width: 500px;
        height: 500px;
        background: radial-gradient(circle, rgba(0, 122, 255, 0.15) 0%, transparent 70%);
        top: -250px;
        right: -250px;
        animation: float 20s infinite ease-in-out;
    }
    
    @keyframes float {
        0%, 100% { transform: translate(0, 0) rotate(0deg); }
        50% { transform: translate(50px, 50px) rotate(180deg); }
    }
    
    .btn-primary {
        background: linear-gradient(135deg, #007AFF 0%, #0051D5 100%);
        color: #fff;
        border-radius: 0.75rem;
        padding: 1rem 2rem;
        font-weight: 600;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
        border: none;
        cursor: pointer;
        min-height: 48px;
        -webkit-tap-highlight-color: transparent;
    }
    
    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 30px rgba(0, 122, 255, 0.4);
    }
    
    .btn-primary:active {
        transform: scale(0.98);
    }
    
    .btn-secondary {
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(10px);
        color: #fff;
        border: 2px solid rgba(255, 255, 255, 0.2);
        border-radius: 0.75rem;
        padding: 1rem 2rem;
        font-weight: 600;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
        min-height: 48px;
        -webkit-tap-highlight-color: transparent;
    }
    
    .btn-secondary:hover {
        background: rgba(255, 255, 255, 0.2);
        border-color: rgba(255, 255, 255, 0.4);
    }
    
    .btn-secondary:active {
        transform: scale(0.98);
    }
</style>
