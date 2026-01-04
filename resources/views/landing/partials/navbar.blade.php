@php
    $isLandingPage = request()->routeIs('landing') || request()->routeIs('landing.id') || request()->routeIs('landing.en');
    $landingUrl = app()->getLocale() === 'en' ? route('landing.en') : route('landing.id');
    $blogUrl = app()->getLocale() === 'en' ? route('blog.index.en') : route('blog.index.id');
@endphp

<!-- Navigation -->
<nav class="fixed top-0 left-0 right-0 z-50 bg-white/95 backdrop-blur-sm border-b border-gray-200 shadow-sm" role="navigation" aria-label="Main navigation">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
            <!-- Logo -->
            <div class="flex items-center">
                <a href="{{ $landingUrl }}" class="text-xl font-bold text-blue-900" aria-label="Bizmark.ID - Go to homepage">
                    <i class="fas fa-certificate text-blue-600 mr-2"></i>
                    Bizmark.ID
                </a>
            </div>
            
            <!-- Desktop Navigation -->
            <div class="hidden md:flex items-center space-x-6">
                <a href="{{ $isLandingPage ? '#home' : $landingUrl . '#home' }}" class="text-gray-700 hover:text-blue-600 transition">{{ __('landing.nav.home') }}</a>
                <a href="{{ $isLandingPage ? '#services' : $landingUrl . '#services' }}" class="text-gray-700 hover:text-blue-600 transition">{{ __('landing.nav.services') }}</a>
                <a href="{{ $isLandingPage ? '#process' : $landingUrl . '#process' }}" class="text-gray-700 hover:text-blue-600 transition">{{ __('landing.nav.process') }}</a>
                <a href="{{ $isLandingPage ? '#about' : $landingUrl . '#about' }}" class="text-gray-700 hover:text-blue-600 transition">{{ __('landing.nav.about') }}</a>
                <a href="{{ $blogUrl }}" class="text-gray-700 hover:text-blue-600 transition">{{ __('landing.nav.blog') }}</a>
                
                <!-- Locale Switcher -->
                <x-locale-switcher />
                
                <a href="{{ $isLandingPage ? '#contact' : $landingUrl . '#contact' }}" class="px-6 py-2 bg-blue-900 text-white rounded-lg hover:bg-blue-800 transition font-semibold">
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
