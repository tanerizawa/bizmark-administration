@php
    $isLandingPage = request()->routeIs('landing');
    $landingUrl = route('landing');
@endphp

<!-- Mobile Menu -->
<div id="mobileMenu" class="mobile-menu fixed top-0 right-0 w-64 h-full bg-dark-bg-secondary z-[1001] md:hidden">
    <div class="p-6">
        <button onclick="toggleMobileMenu()" class="text-white mb-8">
            <i class="fas fa-times text-2xl"></i>
        </button>
        <div class="flex flex-col space-y-4">
            <a href="{{ $isLandingPage ? '#home' : $landingUrl . '#home' }}" class="text-white hover:text-apple-blue transition" onclick="toggleMobileMenu()">Beranda</a>
            <a href="{{ $isLandingPage ? '#services' : $landingUrl . '#services' }}" class="text-white hover:text-apple-blue transition" onclick="toggleMobileMenu()">Layanan</a>
            <a href="{{ $isLandingPage ? '#process' : $landingUrl . '#process' }}" class="text-white hover:text-apple-blue transition" onclick="toggleMobileMenu()">Proses</a>
            <a href="{{ route('blog.index') }}" class="text-white hover:text-apple-blue transition" onclick="toggleMobileMenu()">Artikel</a>
            <a href="{{ $isLandingPage ? '#about' : $landingUrl . '#about' }}" class="text-white hover:text-apple-blue transition" onclick="toggleMobileMenu()">Tentang</a>
            <a href="{{ $isLandingPage ? '#contact' : $landingUrl . '#contact' }}" class="btn btn-primary text-center mt-4" onclick="toggleMobileMenu()">Konsultasi Gratis</a>
        </div>
    </div>
</div>
