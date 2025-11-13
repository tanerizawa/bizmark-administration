@php
    $isLandingPage = request()->routeIs('landing');
    $landingUrl = route('landing');
@endphp

<!-- Mobile Menu -->
<div id="mobileMenu" 
     class="mobile-menu fixed top-0 right-0 w-80 max-w-[85vw] h-full z-[1001] md:hidden"
     role="dialog"
     aria-label="Mobile navigation menu"
     aria-modal="true">
    <div class="p-6 h-full flex flex-col">
        <!-- Close Button -->
        <div class="flex justify-between items-center mb-8">
            <span class="text-xl font-bold text-white" id="mobile-menu-title">Menu</span>
            <button onclick="toggleMobileMenu()" 
                    class="text-white hover:bg-white/10 rounded-lg p-2 transition" 
                    aria-label="Close navigation menu">
                <i class="fas fa-times text-2xl" aria-hidden="true"></i>
            </button>
        </div>
        
        <!-- Navigation Links -->
        <nav class="flex flex-col space-y-1 flex-1" role="navigation" aria-labelledby="mobile-menu-title">
            <a href="{{ $isLandingPage ? '#home' : $landingUrl . '#home' }}" 
               class="text-white hover:text-blue-200 transition px-4 py-3 rounded-lg hover:bg-white/10" 
               onclick="toggleMobileMenu()"
               aria-label="Go to homepage">
                <i class="fas fa-home w-6 inline-block" aria-hidden="true"></i>
                <span>Beranda</span>
            </a>
            <a href="{{ $isLandingPage ? '#services' : $landingUrl . '#services' }}" 
               class="text-white hover:text-blue-200 transition px-4 py-3 rounded-lg hover:bg-white/10" 
               onclick="toggleMobileMenu()"
               aria-label="View our services">
                <i class="fas fa-briefcase w-6 inline-block" aria-hidden="true"></i>
                <span>Layanan</span>
            </a>
            <a href="{{ $isLandingPage ? '#process' : $landingUrl . '#process' }}" 
               class="text-white hover:text-blue-200 transition px-4 py-3 rounded-lg hover:bg-white/10" 
               onclick="toggleMobileMenu()"
               aria-label="View our process">
                <i class="fas fa-tasks w-6 inline-block" aria-hidden="true"></i>
                <span>Proses</span>
            </a>
            <a href="{{ route('blog.index') }}" 
               class="text-white hover:text-blue-200 transition px-4 py-3 rounded-lg hover:bg-white/10" 
               onclick="toggleMobileMenu()"
               aria-label="Read our articles">
                <i class="fas fa-newspaper w-6 inline-block" aria-hidden="true"></i>
                <span>Artikel</span>
            </a>
            <a href="{{ $isLandingPage ? '#about' : $landingUrl . '#about' }}" 
               class="text-white hover:text-blue-200 transition px-4 py-3 rounded-lg hover:bg-white/10" 
               onclick="toggleMobileMenu()"
               aria-label="Learn about us">
                <i class="fas fa-info-circle w-6 inline-block" aria-hidden="true"></i>
                <span>Tentang</span>
            </a>
        
        <!-- CTA Button -->
        <a href="{{ $isLandingPage ? '#contact' : $landingUrl . '#contact' }}" 
           class="btn btn-primary w-full justify-center mt-6 shadow-lg" 
           data-cta="mobile_contact" 
           onclick="toggleMobileMenu()"
           aria-label="Start free consultation">
            <i class="fas fa-comments mr-2" aria-hidden="true"></i>
            Konsultasi Gratis
        </a>
        </nav>
        
        <!-- Footer Info -->
        <div class="pt-6 border-t border-white/20 mt-auto">
            <p class="text-white/80 text-sm mb-2">Hubungi Kami:</p>
            <a href="https://wa.me/6283879602855" 
               class="text-white hover:text-blue-200 flex items-center gap-2 mb-2"
               target="_blank"
               rel="noopener noreferrer"
               aria-label="Contact us on WhatsApp at +62 838-7960-2855">
                <i class="fab fa-whatsapp" aria-hidden="true"></i>
                <span class="text-sm">+62 838-7960-2855</span>
            </a>
            <a href="tel:+6283879602855" 
               class="text-white hover:text-blue-200 flex items-center gap-2"
               aria-label="Call us directly at +62 838-7960-2855">
                <i class="fas fa-phone" aria-hidden="true"></i>
                <span class="text-sm">Telepon Langsung</span>
            </a>
        </div>
    </div>
</div>
