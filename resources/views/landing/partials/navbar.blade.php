@php
    $isLandingPage = request()->routeIs('landing');
    $landingUrl = route('landing');
@endphp

<!-- Navigation -->
<nav class="navbar" role="navigation" aria-label="Main navigation">
    <div class="container-wide h-20 flex items-center justify-between">
        <a href="{{ $isLandingPage ? '#home' : $landingUrl . '#home' }}" 
           class="flex flex-col leading-tight"
           aria-label="Bizmark.ID Permit Suite - Go to homepage">
            <span class="text-xs font-semibold uppercase tracking-[0.4em] text-slate-400">Bizmark.ID</span>
            <span class="text-xl font-semibold text-slate-900">Permit Suite</span>
        </a>

        <div class="hidden lg:flex items-center gap-8 text-xs font-semibold uppercase tracking-[0.35em] text-slate-400" role="menubar">
            <a href="{{ $isLandingPage ? '#home' : $landingUrl . '#home' }}" class="nav-link" role="menuitem">Beranda</a>
            <a href="{{ $isLandingPage ? '#services' : $landingUrl . '#services' }}" class="nav-link" role="menuitem">Layanan</a>
            <a href="{{ $isLandingPage ? '#process' : $landingUrl . '#process' }}" class="nav-link" role="menuitem">Proses</a>
            <a href="{{ route('blog.index') }}" class="nav-link" role="menuitem">Artikel</a>
            <a href="{{ route('career.index') }}" class="nav-link" role="menuitem">Karir</a>
            <a href="{{ $isLandingPage ? '#about' : $landingUrl . '#about' }}" class="nav-link" role="menuitem">Tentang</a>
        </div>

        <div class="hidden lg:flex items-center gap-4">
            <div class="text-right text-xs uppercase tracking-[0.35em] text-slate-400">
                <span aria-label="Hotline telephone number">Hotline</span>
                <p class="text-base font-semibold tracking-normal text-slate-900">
                    <a href="tel:+6283879602855" 
                       class="hover:text-blue-600 transition"
                       aria-label="Call us at +62 838 7960 2855">
                        +62 838 7960 2855
                    </a>
                </p>
            </div>
            
            <div class="h-8 w-px bg-gray-300"></div>
            
            <!-- Client Portal Button - Single Login/Register -->
            <a href="{{ route('client.login') }}" 
               class="flex items-center gap-2 text-sm font-medium bg-blue-600 hover:bg-blue-700 text-white px-4 py-2.5 rounded-lg transition shadow-sm"
               aria-label="Login atau Registrasi Portal Klien">
                <i class="fas fa-sign-in-alt"></i>
                <span>Login / Registrasi</span>
            </a>
        </div>

        <div class="lg:hidden flex items-center">
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
</nav>
