@php
    $isLandingPage = request()->routeIs('landing') || request()->routeIs('landing.id') || request()->routeIs('landing.en');
    $landingUrl = app()->getLocale() === 'en' ? route('landing.en') : route('landing.id');
    $blogUrl = app()->getLocale() === 'en' ? route('blog.index.en') : route('blog.index.id');
@endphp

<!-- Navigation - Neuroscience-Based Design -->
<nav id="navbar" 
     class="navbar fixed top-0 left-0 right-0 z-50 transition-all duration-300" 
     role="navigation" 
     aria-label="Main navigation">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
            <!-- Logo -->
            <div class="flex items-center">
                <a href="{{ $landingUrl }}" 
                   class="text-xl font-bold text-white hover:text-blue-100 transition-colors duration-300" 
                   aria-label="Bizmark.ID - Go to homepage">
                    <i class="fas fa-certificate mr-2"></i>
                    Bizmark.ID
                </a>
            </div>
            
            <!-- Desktop Navigation -->
            <div class="hidden md:flex items-center space-x-8">
                <a href="{{ $isLandingPage ? '#home' : $landingUrl . '#home' }}" 
                   class="nav-link text-white/90 hover:text-white transition-colors duration-300 relative group"
                   data-section="home">
                    {{ __('landing.nav.home') }}
                    <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-white/50 group-hover:w-full transition-all duration-300"></span>
                </a>
                <a href="{{ $isLandingPage ? '#services' : $landingUrl . '#services' }}" 
                   class="nav-link text-white/90 hover:text-white transition-colors duration-300 relative group"
                   data-section="services">
                    {{ __('landing.nav.services') }}
                    <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-white/50 group-hover:w-full transition-all duration-300"></span>
                </a>
                <a href="{{ $isLandingPage ? '#process' : $landingUrl . '#process' }}" 
                   class="nav-link text-white/90 hover:text-white transition-colors duration-300 relative group"
                   data-section="process">
                    {{ __('landing.nav.process') }}
                    <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-white/50 group-hover:w-full transition-all duration-300"></span>
                </a>
                <a href="{{ $isLandingPage ? '#about' : $landingUrl . '#about' }}" 
                   class="nav-link text-white/90 hover:text-white transition-colors duration-300 relative group"
                   data-section="about">
                    {{ __('landing.nav.about') }}
                    <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-white/50 group-hover:w-full transition-all duration-300"></span>
                </a>
                <a href="{{ $blogUrl }}" 
                   class="nav-link text-white/90 hover:text-white transition-colors duration-300 relative group">
                    {{ __('landing.nav.blog') }}
                    <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-white/50 group-hover:w-full transition-all duration-300"></span>
                </a>
                
                <!-- Locale Switcher -->
                <x-locale-switcher />
                
                <a href="{{ $isLandingPage ? '#contact' : $landingUrl . '#contact' }}" 
                   class="px-6 py-2 bg-white/20 text-white rounded-full hover:bg-white/30 transition-all duration-300 font-semibold border border-white/30 hover:border-white/50">
                    {{ __('landing.nav.get_started') }}
                </a>
            </div>
            
            <!-- Mobile Menu Button -->
            <div class="md:hidden flex items-center">
                <button class="text-white hover:bg-white/10 p-2 rounded-lg transition-colors duration-300" 
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
