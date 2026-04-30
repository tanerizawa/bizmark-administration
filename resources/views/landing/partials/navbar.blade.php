@php
    $currentLocale = app()->getLocale();
    $landingUrl = $currentLocale === 'en' ? route('landing.en') : route('landing.id');
    $servicesUrl = $currentLocale === 'en' ? route('services.index.en') : route('services.index.id');
    $processUrl = $currentLocale === 'en' ? route('process.en') : route('process.id');
    $aboutUrl = $currentLocale === 'en' ? route('about.en') : route('about.id');
    $blogUrl = $currentLocale === 'en' ? route('blog.index.en') : route('blog.index.id');
@endphp

<nav class="app-navbar fixed top-0 left-0 right-0 z-50"
     role="navigation"
     aria-label="{{ $currentLocale === 'id' ? 'Navigasi utama' : 'Main navigation' }}"
     x-data="{ serviceMenu: false, toolsMenu: false, localeMenu: false, closeTimer: null }">
    <div class="container-wide">
        <div class="app-navbar-inner">
            <a href="{{ $landingUrl }}" class="brand-mark" aria-label="Bizmark.ID">
                <i class="fas fa-certificate"></i>
                <span>Bizmark.ID</span>
            </a>

            <div class="hidden md:flex items-center gap-1">
                <a href="{{ $landingUrl }}" class="nav-link {{ request()->routeIs('landing.*') ? 'active' : '' }}">{{ __('landing.nav.home') }}</a>

                {{-- Mega-dropdown Layanan (WAI-ARIA Navigation Pattern) --}}
                <div class="relative"
                     @mouseenter="clearTimeout(closeTimer); serviceMenu = true"
                     @mouseleave="closeTimer = setTimeout(() => serviceMenu = false, 180)">
                    <button type="button"
                        id="services-menu-btn"
                        class="nav-link {{ request()->routeIs('services.*') ? 'active' : '' }} inline-flex items-center gap-1.5"
                        :aria-expanded="serviceMenu"
                        aria-haspopup="true"
                        aria-controls="servicesMegaMenu"
                        @click="serviceMenu = !serviceMenu">
                        {{ __('landing.nav.services') }}
                        <i class="fas fa-chevron-down text-[10px]"
                           aria-hidden="true"
                           :class="{ 'rotate-180': serviceMenu }"></i>
                    </button>
                    <div id="servicesMegaMenu"
                        x-show="serviceMenu"
                        x-cloak
                        @mouseenter="clearTimeout(closeTimer)"
                        @mouseleave="closeTimer = setTimeout(() => serviceMenu = false, 180)"
                        @click.outside="serviceMenu = false"
                        @keydown.escape.window="serviceMenu = false"
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 translate-y-2"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100 translate-y-0"
                        x-transition:leave-end="opacity-0 translate-y-2"
                        class="nav-dropdown"
                        style="left:0;width:min(580px,calc(100vw - 2rem));max-height:calc(100vh - 100px);overflow-y:auto;"
                        role="menu"
                        aria-labelledby="services-menu-btn">
                        @php
                        $navServices = [
                            ['icon' => 'fa-recycle',    'color' => 'var(--accent)', 'label' => $currentLocale === 'en' ? 'Waste & B3'       : 'Limbah & B3',         'desc' => $currentLocale === 'en' ? 'B3 waste permits, TPS'    : 'Izin limbah B3, TPS',         'slug' => 'perizinan-lb3'],
                            ['icon' => 'fa-leaf',       'color' => 'var(--accent)', 'label' => $currentLocale === 'en' ? 'Environment'       : 'Lingkungan',          'desc' => $currentLocale === 'en' ? 'AMDAL, UKL-UPL, SPPL'     : 'AMDAL, UKL-UPL, SPPL',        'slug' => 'amdal'],
                            ['icon' => 'fa-building',   'color' => 'var(--accent)', 'label' => $currentLocale === 'en' ? 'Building Permits'  : 'Perizinan Gedung',    'desc' => $currentLocale === 'en' ? 'PBG, SLF, IMB'            : 'PBG, SLF, dan IMB',           'slug' => 'pbg-slf'],
                            ['icon' => 'fa-id-card',    'color' => 'var(--accent)', 'label' => $currentLocale === 'en' ? 'Business License'  : 'Izin Usaha',          'desc' => $currentLocale === 'en' ? 'NIB, OSS-RBA, SIUP'        : 'NIB, OSS-RBA, SIUP',          'slug' => 'oss-nib'],
                            ['icon' => 'fa-globe-asia', 'color' => 'var(--accent)', 'label' => $currentLocale === 'en' ? 'PMA / Foreign Inv' : 'PMA / Investasi Asing','desc' => $currentLocale === 'en' ? 'BKPM, sectoral permits'    : 'BKPM, izin sektoral',          'slug' => 'pma-investasi-asing'],
                            ['icon' => 'fa-industry',   'color' => 'var(--accent)', 'label' => $currentLocale === 'en' ? 'Operational'       : 'Operasional',         'desc' => $currentLocale === 'en' ? 'Sector-specific permits'   : 'Izin operasional sektoral',    'slug' => 'izin-operasional'],
                        ];
                        @endphp
                        <div class="p-5">
                            <p class="text-[10px] font-bold uppercase tracking-[.15em] mb-3 text-gray-500">
                                {{ $currentLocale === 'en' ? 'Services' : 'Layanan' }}
                            </p>
                            <div class="grid grid-cols-2 gap-1.5 mb-4">
                                @foreach($navServices as $idx => $ns)
                                <a href="{{ $currentLocale === 'en' ? route('services.show.en', $ns['slug']) : route('services.show.id', $ns['slug']) }}"
                                   role="menuitem"
                                   tabindex="0"
                                   class="nav-menu-feature flex items-start gap-3 p-3 rounded-xl transition-colors group no-underline">
                                    <div class="flex-shrink-0 w-9 h-9 rounded-lg flex items-center justify-center"
                                         style="background:{{ $ns['color'] }}18;">
                                        <i class="fas {{ $ns['icon'] }} text-sm" style="color:{{ $ns['color'] }};" aria-hidden="true"></i>
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-sm font-semibold leading-tight text-gray-100">{{ $ns['label'] }}</p>
                                        <p class="text-xs leading-snug mt-0.5 text-gray-500">{{ $ns['desc'] }}</p>
                                    </div>
                                </a>
                                @endforeach
                            </div>
                            <div class="pt-3 border-t border-white/10 flex items-center justify-between">
                                <a href="{{ $servicesUrl }}" role="menuitem" class="link-primary text-sm font-semibold inline-flex items-center gap-1.5">
                                    {{ $currentLocale === 'en' ? 'View all services' : 'Lihat semua layanan' }}
                                    <i class="fas fa-arrow-right text-xs" aria-hidden="true"></i>
                                </a>
                                <a href="{{ route('landing.service-inquiry.create') }}" role="menuitem" class="btn btn-secondary btn-sm">
                                    <i class="fas fa-robot" aria-hidden="true"></i>
                                    {{ $currentLocale === 'en' ? 'AI Analysis' : 'Analisis AI' }}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <a href="{{ $processUrl }}" class="nav-link {{ request()->routeIs('process.*') ? 'active' : '' }}">{{ __('landing.nav.process') }}</a>
                <a href="{{ $aboutUrl }}" class="nav-link {{ request()->routeIs('about.*') ? 'active' : '' }}">{{ __('landing.nav.about') }}</a>
                <a href="{{ $blogUrl }}" class="nav-link {{ request()->routeIs('blog.*') ? 'active' : '' }}">{{ __('landing.nav.blog') }}</a>
            </div>

            <div class="hidden md:flex items-center gap-2">
                <div class="relative"
                     x-data
                     @click.outside="toolsMenu = false"
                     @keydown.escape.window="toolsMenu = false">
                    <button type="button"
                            class="nav-control-btn"
                            :aria-expanded="toolsMenu"
                            aria-haspopup="true"
                            @click="toolsMenu = !toolsMenu">
                        <i class="fas fa-tools"></i>
                        <span>{{ $currentLocale === 'en' ? 'Tools' : 'Alat Bantu' }}</span>
                        <i class="fas fa-chevron-down text-[10px]"></i>
                    </button>
                    <div id="toolsMenu"
                         x-show="toolsMenu"
                         x-cloak
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 translate-y-2"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 translate-y-0"
                         x-transition:leave-end="opacity-0 translate-y-2"
                         class="nav-dropdown"
                         role="menu">
                        <a href="{{ route('polygon.shp.index') }}" role="menuitem" class="nav-dropdown-item">
                            <i class="fas fa-draw-polygon text-emerald-500"></i>
                            <span>Polygon SHP Maker</span>
                        </a>
                        <a href="{{ route('calculator.index') }}" role="menuitem" class="nav-dropdown-item">
                            <i class="fas fa-calculator text-violet-500"></i>
                            <span>{{ $currentLocale === 'en' ? 'Permit Cost Calculator' : 'Kalkulator Perizinan' }}</span>
                        </a>
                        <a href="{{ route('consultation.index') }}" role="menuitem" class="nav-dropdown-item">
                            <i class="fas fa-comments-dollar text-sky-500"></i>
                            <span>{{ $currentLocale === 'en' ? 'Cost Estimator' : 'Estimasi Biaya' }}</span>
                        </a>
                    </div>
                </div>

                <div class="relative"
                     x-data
                     @click.outside="localeMenu = false"
                     @keydown.escape.window="localeMenu = false">
                    <button type="button"
                            class="nav-control-btn"
                            :aria-expanded="localeMenu"
                            aria-haspopup="true"
                            @click="localeMenu = !localeMenu">
                        <span>{{ $currentLocale === 'en' ? '🇬🇧 EN' : '🇮🇩 ID' }}</span>
                        <i class="fas fa-chevron-down text-[10px]"></i>
                    </button>
                    <div id="localeDropdown"
                         x-show="localeMenu"
                         x-cloak
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 translate-y-2"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 translate-y-0"
                         x-transition:leave-end="opacity-0 translate-y-2"
                         class="nav-dropdown"
                         role="menu">
                        <a href="{{ route('locale.set', 'id') }}" role="menuitem" class="nav-dropdown-item {{ $currentLocale === 'id' ? 'active' : '' }}">
                            <span>🇮🇩 Indonesia</span>
                        </a>
                        <a href="{{ route('locale.set', 'en') }}" role="menuitem" class="nav-dropdown-item {{ $currentLocale === 'en' ? 'active' : '' }}">
                            <span>🇬🇧 English</span>
                        </a>
                    </div>
                </div>

                {{-- Dark Mode Toggle --}}
                <button type="button"
                    id="themeToggle"
                    class="nav-control-btn"
                    @click="toggleTheme()"
                    aria-label="{{ $currentLocale === 'id' ? 'Ganti tema gelap/terang' : 'Toggle dark/light mode' }}"
                    title="{{ $currentLocale === 'id' ? 'Ganti tema' : 'Toggle theme' }}">
                    <i id="themeIcon" class="fas fa-moon"></i>
                </button>

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
                @click="$dispatch('open-mobile-menu')"
                aria-label="{{ $currentLocale === 'id' ? 'Buka menu navigasi' : 'Open navigation menu' }}">
                <i class="fas fa-bars"></i>
            </button>
        </div>
    </div>
</nav>
