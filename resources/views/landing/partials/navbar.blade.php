@php
    $isLandingPage = request()->routeIs('landing');
    $landingUrl = route('landing');
@endphp

<!-- Navigation -->
<nav class="navbar bg-[#0077B5] shadow-md" role="navigation" aria-label="Main navigation">
    <div class="container-wide h-20 flex items-center justify-between">
        <a href="{{ $isLandingPage ? '#home' : $landingUrl . '#home' }}" 
           class="flex items-center gap-2"
           aria-label="Bizmark.ID - Go to homepage">
            <div class="flex items-center gap-1">
                <i class="fas fa-building text-yellow-400 text-2xl"></i>
                <span class="text-2xl font-bold text-white">
                    Bizmark<span class="text-yellow-400">.ID</span>
                </span>
            </div>
        </a>

        <div class="hidden lg:flex items-center gap-6 text-xs font-semibold uppercase tracking-[0.35em] text-blue-100" role="menubar">
            <a href="{{ $isLandingPage ? '#home' : $landingUrl . '#home' }}" class="nav-link-white" role="menuitem">Beranda</a>
            <a href="{{ $isLandingPage ? '#services' : $landingUrl . '#services' }}" class="nav-link-white" role="menuitem">Layanan</a>
            <a href="{{ $isLandingPage ? '#process' : $landingUrl . '#process' }}" class="nav-link-white" role="menuitem">Proses</a>
            <a href="{{ route('blog.index') }}" class="nav-link-white" role="menuitem">Artikel</a>
            <a href="{{ route('career.index') }}" class="nav-link-white" role="menuitem">Karir</a>
            <a href="{{ $isLandingPage ? '#about' : $landingUrl . '#about' }}" class="nav-link-white" role="menuitem">Tentang</a>
            <a href="{{ route('contact.index') }}" class="nav-link-white" role="menuitem">Kontak</a>
        </div>

        <div class="hidden lg:flex items-center gap-4">
            <div class="text-right text-xs uppercase tracking-[0.35em] text-blue-100">
                <span aria-label="Hotline telephone number">Hotline</span>
                <p class="text-base font-semibold tracking-normal text-white">
                    <a href="tel:+6283879602855" 
                       class="hover:text-yellow-400 transition"
                       aria-label="Call us at +62 838 7960 2855">
                        +62 838 7960 2855
                    </a>
                </p>
            </div>
            
            <div class="h-8 w-px bg-blue-400"></div>
            
            <!-- Unified Login Button -->
            <a href="{{ route('login') }}" 
               class="flex items-center gap-2 text-sm font-medium bg-white hover:bg-yellow-400 text-[#0077B5] px-4 py-2.5 rounded-lg transition shadow-sm"
               aria-label="Login Portal">
                <i class="fas fa-sign-in-alt"></i>
                <span>Login</span>
            </a>
        </div>

        <div class="lg:hidden flex items-center">
            <button class="text-white hover:text-yellow-400 p-2 rounded-lg hover:bg-blue-700 transition" 
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
