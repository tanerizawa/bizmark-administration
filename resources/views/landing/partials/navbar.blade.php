@php
    $isLandingPage = request()->routeIs('landing');
    $landingUrl = route('landing');
@endphp

<!-- Navigation -->
<nav class="navbar">
    <div class="container-wide">
        <div class="flex items-center justify-between h-20">
            <!-- Logo -->
            <div class="flex items-center">
                <a href="{{ $isLandingPage ? '#home' : $landingUrl . '#home' }}" class="text-2xl font-bold bg-gradient-to-r from-primary to-secondary bg-clip-text text-transparent hover:opacity-80 transition">
                    Bizmark.ID
                </a>
            </div>
            
            <!-- Desktop Menu -->
            <div class="hidden md:flex items-center space-x-8">
                <a href="{{ $isLandingPage ? '#home' : $landingUrl . '#home' }}" class="text-text-secondary hover:text-primary transition font-medium">Beranda</a>
                <a href="{{ $isLandingPage ? '#services' : $landingUrl . '#services' }}" class="text-text-secondary hover:text-primary transition font-medium">Layanan</a>
                <a href="{{ $isLandingPage ? '#process' : $landingUrl . '#process' }}" class="text-text-secondary hover:text-primary transition font-medium">Proses</a>
                <a href="{{ route('blog.index') }}" class="text-text-secondary hover:text-primary transition font-medium">Artikel</a>
                <a href="{{ $isLandingPage ? '#about' : $landingUrl . '#about' }}" class="text-text-secondary hover:text-primary transition font-medium">Tentang</a>
                <a href="{{ $isLandingPage ? '#contact' : $landingUrl . '#contact' }}" class="btn btn-primary">Konsultasi Gratis</a>
            </div>
            
            <!-- Mobile Menu Buttons -->
            <div class="md:hidden flex items-center space-x-4">
                <button class="text-text-primary" onclick="toggleMobileMenu()" aria-label="Menu">
                    <i class="fas fa-bars text-2xl"></i>
                </button>
            </div>
        </div>
    </div>
</nav>
