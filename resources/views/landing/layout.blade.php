<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" class="scroll-smooth">
<head>
    @include('landing.partials.head')
    @include('landing.partials.styles-modern')
</head>
<body>
    @include('landing.partials.navbar')
    @include('landing.partials.mobile-menu')
    
    <!-- Main Content -->
    <main id="main-content">
        @yield('content')
    </main>
    
    <!-- Floating Action Buttons -->
    <div class="fab-group">
        <a href="https://wa.me/6281382605030?text=Halo%20PT%20Cangah%20Pajaratan%20Mandiri%2C%20saya%20ingin%20konsultasi%20tentang%20perizinan" 
           target="_blank" 
           class="fab fab-whatsapp"
           title="Chat WhatsApp"
           aria-label="Chat via WhatsApp">
            <i class="fab fa-whatsapp"></i>
        </a>
        <a href="tel:+6281382605030" 
           class="fab fab-phone"
           title="Telepon Kami"
           aria-label="Hubungi via telepon">
            <i class="fas fa-phone-alt"></i>
        </a>
        <button onclick="window.scrollTo({top: 0, behavior: 'smooth'})" 
                class="fab fab-back-to-top"
                id="backToTop"
                title="Kembali ke Atas"
                aria-label="Kembali ke atas halaman">
            <i class="fas fa-arrow-up"></i>
        </button>
    </div>
    
    @include('landing.partials.scripts')
</body>
</html>
