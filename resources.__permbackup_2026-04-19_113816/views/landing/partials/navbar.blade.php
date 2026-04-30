@php
    $currentLocale = app()->getLocale();
    $landingUrl = $currentLocale === 'en' ? route('landing.en') : route('landing.id');
    $servicesUrl = $currentLocale === 'en' ? route('services.index.en') : route('services.index.id');
    $processUrl = $currentLocale === 'en' ? route('process.en') : route('process.id');
    $aboutUrl = $currentLocale === 'en' ? route('about.en') : route('about.id');
    $blogUrl = $currentLocale === 'en' ? route('blog.index.en') : route('blog.index.id');
@endphp

<nav class="app-navbar fixed top-0 left-0 right-0 z-50" role="navigation" aria-label="{{ $currentLocale === 'id' ? 'Navigasi utama' : 'Main navigation' }}">
    <div class="container-wide">
        <div class="app-navbar-inner">
            <a href="{{ $landingUrl }}" class="brand-mark" aria-label="Bizmark.ID">
                <i class="fas fa-certificate"></i>
                <span>Bizmark.ID</span>
            </a>

            <div class="hidden md:flex items-center gap-1">
                <a href="{{ $landingUrl }}" class="nav-link {{ request()->routeIs('landing.*') ? 'active' : '' }}">{{ __('landing.nav.home') }}</a>
                <a href="{{ $servicesUrl }}" class="nav-link {{ request()->routeIs('services.*') ? 'active' : '' }}">{{ __('landing.nav.services') }}</a>
                <a href="{{ $processUrl }}" class="nav-link {{ request()->routeIs('process.*') ? 'active' : '' }}">{{ __('landing.nav.process') }}</a>
                <a href="{{ $aboutUrl }}" class="nav-link {{ request()->routeIs('about.*') ? 'active' : '' }}">{{ __('landing.nav.about') }}</a>
                <a href="{{ $blogUrl }}" class="nav-link {{ request()->routeIs('blog.*') ? 'active' : '' }}">{{ __('landing.nav.blog') }}</a>
            </div>

            <div class="hidden md:flex items-center gap-2">
                <div class="relative" id="toolsDropdown">
                    <button type="button" class="nav-control-btn" aria-expanded="false" aria-haspopup="true" data-dropdown-trigger="toolsMenu">
                        <i class="fas fa-tools"></i>
                        <span>Tools</span>
                        <i class="fas fa-chevron-down text-[10px]"></i>
                    </button>
                    <div id="toolsMenu" class="nav-dropdown hidden" role="menu">
                        <a href="{{ route('polygon.shp.index') }}" role="menuitem" class="nav-dropdown-item">
                            <i class="fas fa-draw-polygon text-emerald-500"></i>
                            <span>Polygon SHP Maker</span>
                        </a>
                        <a href="{{ route('calculator.index') }}" role="menuitem" class="nav-dropdown-item">
                            <i class="fas fa-calculator text-violet-500"></i>
                            <span>Kalkulator Perizinan</span>
                        </a>
                        <a href="{{ route('consultation.index') }}" role="menuitem" class="nav-dropdown-item">
                            <i class="fas fa-comments-dollar text-sky-500"></i>
                            <span>Estimasi Biaya</span>
                        </a>
                    </div>
                </div>

                <div class="relative" id="localeSwitcher">
                    <button type="button" class="nav-control-btn" aria-expanded="false" aria-haspopup="true" data-dropdown-trigger="localeDropdown">
                        <span>{{ $currentLocale === 'en' ? '🇬🇧 EN' : '🇮🇩 ID' }}</span>
                        <i class="fas fa-chevron-down text-[10px]"></i>
                    </button>
                    <div id="localeDropdown" class="nav-dropdown hidden" role="menu">
                        <a href="{{ route('locale.set', 'id') }}" role="menuitem" class="nav-dropdown-item {{ $currentLocale === 'id' ? 'active' : '' }}">
                            <span>🇮🇩 Indonesia</span>
                        </a>
                        <a href="{{ route('locale.set', 'en') }}" role="menuitem" class="nav-dropdown-item {{ $currentLocale === 'en' ? 'active' : '' }}">
                            <span>🇬🇧 English</span>
                        </a>
                    </div>
                </div>

                <a href="{{ route('permohonan.index') }}" class="btn btn-outline-primary btn-sm">
                    <i class="fas fa-file-invoice-dollar"></i>
                    <span>Permohonan</span>
                </a>

                @if(auth('client')->check())
                    <a href="{{ route('client.dashboard') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-user-circle"></i>
                        <span>{{ $currentLocale === 'id' ? 'Dashboard' : 'Dashboard' }}</span>
                    </a>
                @elseif(auth('web')->check())
                    <a href="/dashboard" class="btn btn-primary btn-sm">
                        <i class="fas fa-tachometer-alt"></i>
                        <span>Dashboard</span>
                    </a>
                @else
                    <a href="/login" class="btn btn-primary btn-sm">
                        <i class="fas fa-sign-in-alt"></i>
                        <span>{{ $currentLocale === 'id' ? 'Login / Daftar' : 'Login / Sign Up' }}</span>
                    </a>
                @endif
            </div>

            <button
                class="md:hidden nav-control-btn"
                onclick="toggleMobileMenu()"
                id="mobile-menu-button"
                aria-label="{{ $currentLocale === 'id' ? 'Buka menu navigasi' : 'Open navigation menu' }}"
                aria-expanded="false"
                aria-controls="mobileMenu">
                <i class="fas fa-bars"></i>
            </button>
        </div>
    </div>
</nav>
