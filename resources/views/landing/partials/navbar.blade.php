@php
    $isLandingPage = request()->routeIs('landing') || request()->routeIs('landing.id') || request()->routeIs('landing.en');
    $isBlogPage = request()->routeIs('blog.*');
    $landingUrl = app()->getLocale() === 'en' ? route('landing.en') : route('landing.id');
    $blogUrl = app()->getLocale() === 'en' ? route('blog.index.en') : route('blog.index.id');
@endphp

<!-- Navbar -->
<nav class="fixed top-0 left-0 right-0 z-50 bg-white/95 backdrop-blur-sm border-b border-gray-200 shadow-sm rounded-none" role="navigation" aria-label="{{ app()->getLocale() === 'id' ? 'Navigasi utama' : 'Main navigation' }}">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
            <!-- Logo -->
            <div class="flex items-center">
                <a href="{{ $landingUrl }}" class="text-xl font-bold text-[color:var(--text-primary)]">
                    <i class="fas fa-certificate mr-2 text-[color:var(--color-primary)]"></i>
                    Bizmark.ID
                </a>
            </div>
            
            <!-- Desktop Navigation -->
            <div class="hidden md:flex items-center space-x-2 lg:space-x-3">
                <a href="{{ $landingUrl }}" class="nav-link">{{ __('landing.nav.home') }}</a>
                <a href="{{ app()->getLocale() === 'en' ? route('services.index.en') : route('services.index.id') }}" class="nav-link">{{ __('landing.nav.services') }}</a>
                <a href="{{ app()->getLocale() === 'en' ? route('process.en') : route('process.id') }}" class="nav-link">{{ __('landing.nav.process') }}</a>
                <a href="{{ app()->getLocale() === 'en' ? route('about.en') : route('about.id') }}" class="nav-link">{{ __('landing.nav.about') }}</a>
                <a href="{{ $blogUrl }}" class="nav-link{{ $isBlogPage ? ' active' : '' }}">{{ __('landing.nav.blog') }}</a>
                
                <!-- Permohonan Button (Prominent CTA) -->
                <a href="{{ route('permohonan.index') }}" class="nav-link relative group">
                    <span class="flex items-center gap-1.5">
                        <i class="fas fa-file-invoice-dollar text-xs text-orange-500"></i>
                        <span>Permohonan</span>
                    </span>
                    <span class="absolute -top-1 -right-1 flex h-2 w-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-orange-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-orange-500"></span>
                    </span>
                </a>
                
                <!-- Tools Dropdown -->
                <div class="relative inline-block" id="toolsDropdown">
                    <button type="button"
                            onclick="document.getElementById('toolsMenu').classList.toggle('hidden')"
                            class="nav-link inline-flex items-center gap-1"
                            aria-expanded="false"
                            aria-haspopup="true">
                        <i class="fas fa-tools text-xs opacity-70"></i>
                        Tools
                        <svg class="w-3 h-3 opacity-60" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    <div id="toolsMenu"
                         class="hidden absolute right-0 z-50 w-56 mt-2 origin-top-right bg-white border border-gray-200 rounded-lg shadow-lg ring-1 ring-black/5"
                         role="menu">
                        <div class="py-1">
                            <a href="{{ route('polygon.shp.index') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition-colors" role="menuitem">
                                <i class="fas fa-draw-polygon text-emerald-500 w-4 text-center"></i>
                                <div>
                                    <div class="font-medium">Polygon SHP Maker</div>
                                    <div class="text-xs text-gray-400">Buat file SHP untuk OSS</div>
                                </div>
                            </a>
                            <a href="{{ route('calculator.index') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition-colors" role="menuitem">
                                <i class="fas fa-calculator text-purple-500 w-4 text-center"></i>
                                <div>
                                    <div class="font-medium">Kalkulator Perizinan</div>
                                    <div class="text-xs text-gray-400">Estimasi biaya & waktu</div>
                                </div>
                            </a>
                            <a href="{{ route('consultation.index') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition-colors" role="menuitem">
                                <i class="fas fa-comments-dollar text-blue-500 w-4 text-center"></i>
                                <div>
                                    <div class="font-medium">Estimasi Biaya</div>
                                    <div class="text-xs text-gray-400">Konsultasi gratis</div>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
                
                <!-- Locale Switcher (vanilla JS - no Alpine dependency) -->
                @php $currentLocale = app()->getLocale(); @endphp
                <div class="relative inline-block text-left" id="localeSwitcher">
                    <button type="button" 
                            onclick="document.getElementById('localeDropdown').classList.toggle('hidden')"
                            class="inline-flex items-center gap-2 px-3 py-2 text-sm font-medium rounded-lg border transition-colors text-[color:var(--text-secondary)] border-gray-200 hover:bg-gray-50 hover:border-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-1 focus:ring-[color:var(--color-primary)]"
                            aria-expanded="false" 
                            aria-haspopup="true"
                            aria-label="{{ $currentLocale === 'id' ? 'Ganti bahasa' : 'Change language' }}">
                        <span class="text-base">{{ $currentLocale === 'en' ? '🇬🇧' : '🇮🇩' }}</span>
                        <span class="hidden sm:inline">{{ $currentLocale === 'en' ? 'EN' : 'ID' }}</span>
                        <svg class="w-3.5 h-3.5 opacity-60" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div id="localeDropdown" 
                         class="hidden absolute right-0 z-50 w-48 mt-2 origin-top-right bg-white border border-gray-200 rounded-lg shadow-lg ring-1 ring-black/5"
                         role="menu">
                        <div class="py-1">
                            <a href="{{ route('locale.set', 'id') }}" 
                               class="flex items-center gap-3 px-4 py-2.5 text-sm transition-colors {{ $currentLocale === 'id' ? 'bg-blue-50 text-[color:var(--color-primary)] font-semibold' : 'text-gray-700 hover:bg-gray-50' }}"
                               role="menuitem">
                                <span class="text-lg">🇮🇩</span>
                                <span>Bahasa Indonesia</span>
                                @if($currentLocale === 'id')
                                    <i class="fas fa-check ml-auto text-[color:var(--color-primary)] text-xs"></i>
                                @endif
                            </a>
                            <a href="{{ route('locale.set', 'en') }}" 
                               class="flex items-center gap-3 px-4 py-2.5 text-sm transition-colors {{ $currentLocale === 'en' ? 'bg-blue-50 text-[color:var(--color-primary)] font-semibold' : 'text-gray-700 hover:bg-gray-50' }}"
                               role="menuitem">
                                <span class="text-lg">🇬🇧</span>
                                <span>English</span>
                                @if($currentLocale === 'en')
                                    <i class="fas fa-check ml-auto text-[color:var(--color-primary)] text-xs"></i>
                                @endif
                            </a>
                        </div>
                    </div>
                </div>
                
                @if(auth('client')->check())
                    <div class="relative inline-block" id="profileDropdown">
                        <button type="button"
                                onclick="document.getElementById('profileMenu').classList.toggle('hidden')"
                                class="inline-flex items-center gap-2 px-3 py-2 text-sm font-medium rounded-lg border transition-colors border-[color:var(--color-primary)] text-[color:var(--color-primary)] hover:bg-blue-50 focus:outline-none focus:ring-2 focus:ring-offset-1 focus:ring-[color:var(--color-primary)]"
                                aria-expanded="false" aria-haspopup="true">
                            <i class="fas fa-user-circle"></i>
                            <span class="max-w-[100px] truncate">{{ auth('client')->user()->name }}</span>
                            <svg class="w-3 h-3 opacity-60" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        <div id="profileMenu"
                             class="hidden absolute right-0 z-50 w-48 mt-2 origin-top-right bg-white border border-gray-200 rounded-lg shadow-lg ring-1 ring-black/5"
                             role="menu">
                            <div class="py-1">
                                <div class="px-4 py-2 border-b border-gray-100">
                                    <p class="text-xs font-semibold text-gray-800 truncate">{{ auth('client')->user()->name }}</p>
                                    <p class="text-[10px] text-gray-500 truncate">{{ auth('client')->user()->email }}</p>
                                </div>
                                <a href="{{ route('client.dashboard') }}" class="flex items-center gap-2 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition-colors" role="menuitem">
                                    <i class="fas fa-tachometer-alt w-4 text-center text-gray-400"></i> Dashboard
                                </a>
                                <form method="POST" action="{{ route('client.logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full flex items-center gap-2 px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 transition-colors" role="menuitem">
                                        <i class="fas fa-sign-out-alt w-4 text-center"></i> Logout
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @elseif(auth('web')->check())
                    <a href="/dashboard" class="btn btn-primary">
                        <i class="fas fa-tachometer-alt mr-1"></i> Dashboard
                    </a>
                @else
                    <a href="/login" class="btn btn-primary">
                        <i class="fas fa-sign-in-alt mr-1"></i>
                        {{ app()->getLocale() === 'id' ? 'Login / Daftar' : 'Login / Sign Up' }}
                    </a>
                @endif
            </div>
            
            <!-- Mobile Menu Button -->
            <div class="md:hidden flex items-center">
                <button class="p-2.5 rounded-lg transition text-[color:var(--text-secondary)] hover:bg-orange-50 hover:text-[color:var(--color-secondary)] active:bg-orange-100 active:scale-95 min-w-[44px] min-h-[44px] flex items-center justify-center" 
                        onclick="toggleMobileMenu()" 
                        id="mobile-menu-button"
                        aria-label="{{ app()->getLocale() === 'id' ? 'Buka menu navigasi' : 'Open navigation menu' }}" 
                        aria-expanded="false"
                        aria-controls="mobileMenu">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
            </div>
        </div>
    </div>
</nav>
