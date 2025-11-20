@php
    $metrics = config('landing_metrics');
@endphp

<!-- COVER PAGE: Magazine-Style Hero -->
<section class="magazine-cover relative h-screen overflow-hidden">
    <!-- Parallax Background -->
    <div class="parallax-bg absolute inset-0 -top-12 -bottom-12">
        <!-- Gradient Background (no image needed) -->
        <div class="w-full h-full bg-gradient-to-br from-[#0077B5] via-[#005582] to-[#003d5c]"></div>
        <!-- Pattern Overlay -->
        <div class="absolute inset-0 opacity-10" style="background-image: url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'none\' fill-rule=\'evenodd\'%3E%3Cg fill=\'%23ffffff\' fill-opacity=\'1\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');"></div>
        <!-- Dark Gradient Overlay -->
        <div class="absolute inset-0 bg-gradient-to-b from-black/40 via-black/20 to-black/60"></div>
    </div>
    
    <!-- Content -->
    <div class="relative z-10 h-full flex flex-col">
        <!-- Minimal Header -->
        <div class="flex items-center justify-between p-6">
            <div class="text-white font-bold text-xl flex items-center gap-2">
                <i class="fas fa-building text-yellow-400"></i>
                <span>Bizmark<span class="text-yellow-400">.ID</span></span>
            </div>
            <button onclick="toggleMobileMenu()" class="text-white text-2xl">
                <i class="fas fa-bars"></i>
            </button>
        </div>
        
        <!-- Cover Story Content -->
        <div class="flex-1 flex flex-col justify-end p-6 pb-12">
            <!-- Issue Tag -->
            <div class="mb-2">
                <span class="category-tag bg-white/20 backdrop-blur-md text-white px-3 py-1.5 rounded-full text-xs">
                    Edisi Spesial 2025
                </span>
            </div>
            
            <!-- Main Headline -->
            <h1 class="headline text-white text-4xl mb-3 leading-tight">
                Solusi <span class="text-yellow-400">Perizinan</span><br>
                untuk Bisnis<br>
                Masa Depan
            </h1>
            
            <!-- Deck (Subtitle) -->
            <p class="deck text-white text-base mb-4 max-w-md leading-relaxed opacity-90">
                Dari OSS hingga AMDAL, kami hadirkan layanan perizinan yang cepat,
                transparan, dan terpercaya untuk pertumbuhan bisnis Anda.
            </p>
            
            <!-- Quick Stats -->
            <div class="flex items-center gap-4 text-white text-xs mb-4 opacity-90 flex-wrap">
                <div class="flex items-center gap-1.5">
                    <i class="fas fa-building"></i>
                    <span><strong>{{ $metrics['projects']['completed'] }}</strong> Project Selesai</span>
                </div>
                <span class="text-white/40">•</span>
                <div class="flex items-center gap-1.5">
                    <i class="fas fa-check-circle"></i>
                    <span><strong>{{ $metrics['display']['sla_rate'] }}</strong> SLA Tepat</span>
                </div>
                <span class="text-white/40">•</span>
                <div class="flex items-center gap-1.5">
                    <i class="fas fa-map-marked-alt"></i>
                    <span><strong>{{ $metrics['coverage']['provinces'] }}</strong> Provinsi</span>
                </div>
            </div>
            
            <!-- Byline -->
            <div class="flex items-center gap-2 text-white text-sm mb-6 opacity-75">
                <i class="fas fa-award"></i>
                <span>Dipercaya {{ $metrics['display']['clients_total'] }} Perusahaan di Indonesia</span>
            </div>
            
            <!-- Primary CTA -->
            <div class="space-y-3 mb-6">
                <a href="https://wa.me/{{ $metrics['contact']['whatsapp'] }}?text=Halo%20Bizmark.ID,%20saya%20ingin%20konsultasi%20perizinan" 
                   class="block w-full bg-gradient-to-r from-green-600 to-green-700 text-white font-bold text-lg py-4 px-8 rounded-2xl shadow-2xl hover:shadow-3xl active:scale-95 transition-all duration-200"
                   onclick="trackEvent('CTA', 'click', 'hero_whatsapp_mobile')">
                    <div class="flex items-center justify-center gap-3">
                        <i class="fab fa-whatsapp text-2xl"></i>
                        <span>Konsultasi Gratis</span>
                        <i class="fas fa-arrow-right"></i>
                    </div>
                </a>
                <div class="flex items-center gap-2">
                    <a href="#services" 
                       class="flex-1 bg-white/10 backdrop-blur-sm border-2 border-white/30 text-white font-semibold text-sm py-3 px-4 rounded-xl hover:bg-white/20 active:scale-95 transition-all duration-200 text-center"
                       onclick="trackEvent('CTA', 'click', 'hero_services_mobile')">
                        <i class="fas fa-list mr-2"></i>Lihat Layanan
                    </a>
                    <a href="{{ route('login') }}" 
                       class="flex-1 bg-white/10 backdrop-blur-sm border-2 border-white/30 text-white font-semibold text-sm py-3 px-4 rounded-xl hover:bg-white/20 active:scale-95 transition-all duration-200 text-center">
                        <i class="fas fa-sign-in-alt mr-2"></i>Masuk
                    </a>
                </div>
                <p class="text-center text-white text-xs opacity-80">
                    <i class="fas fa-phone mr-1"></i> {{ $metrics['contact']['phone_display'] }}
                </p>
            </div>
            
            <!-- Trust Badges -->
            <div class="grid grid-cols-2 gap-2 mb-6 max-w-md mx-auto">
                @foreach($metrics['trust_badges'] as $badge)
                <div class="flex items-center gap-2 bg-white/10 backdrop-blur-sm rounded-lg px-2.5 py-2">
                    <i class="fas {{ $badge['icon'] }} text-{{ $badge['color'] }}-400 text-base"></i>
                    <span class="text-white text-xs font-medium">{{ $badge['label'] }}</span>
                </div>
                @endforeach
            </div>
            
            <!-- Scroll Indicator -->
            <div class="scroll-indicator flex flex-col items-center text-white opacity-60 animate-bounce">
                <i class="fas fa-chevron-down text-xl mb-1"></i>
                <p class="text-xs">Jelajahi Lebih Lanjut</p>
            </div>
        </div>
    </div>
</section>

<!-- Mobile Menu (Hidden by default) -->
<div id="mobileMenu" class="fixed inset-0 z-50 bg-gray-900 transform translate-x-full transition-transform duration-300">
    <div class="flex flex-col h-full">
        <!-- Menu Header -->
        <div class="flex items-center justify-between p-6 border-b border-gray-800">
            <span class="text-white font-bold text-xl">Menu Utama</span>
            <button onclick="toggleMobileMenu()" class="text-white text-2xl">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <!-- Menu Items -->
        <nav class="flex-1 p-6 overflow-y-auto">
            <a href="{{ route('services.index') }}" class="block text-white text-lg py-4 border-b border-gray-800">
                <i class="fas fa-certificate mr-3"></i> Layanan
            </a>
            <a href="#why-us" onclick="toggleMobileMenu()" class="block text-white text-lg py-4 border-b border-gray-800">
                <i class="fas fa-star mr-3"></i> Mengapa Kami
            </a>
            <a href="{{ route('blog.index') }}" class="block text-white text-lg py-4 border-b border-gray-800">
                <i class="fas fa-newspaper mr-3"></i> Artikel
            </a>
            <a href="{{ route('career.index') }}" class="block text-white text-lg py-4 border-b border-gray-800">
                <i class="fas fa-briefcase mr-3"></i> Karir
            </a>
            <a href="#faq" onclick="toggleMobileMenu()" class="block text-white text-lg py-4 border-b border-gray-800">
                <i class="fas fa-question-circle mr-3"></i> FAQ
            </a>
            <a href="#contact" onclick="toggleMobileMenu()" class="block text-white text-lg py-4 border-b border-gray-800">
                <i class="fas fa-envelope mr-3"></i> Kontak
            </a>
            
            <!-- Legal & Info -->
            <div class="mt-6 pt-6 border-t border-gray-800">
                <p class="text-gray-400 text-xs mb-3 uppercase tracking-wider">Informasi</p>
                <a href="{{ route('privacy.policy') }}" class="block text-gray-300 text-sm py-3 border-b border-gray-800">
                    <i class="fas fa-shield-alt mr-3 text-gray-500"></i> Kebijakan Privasi
                </a>
                <a href="{{ route('terms.conditions') }}" class="block text-gray-300 text-sm py-3 border-b border-gray-800">
                    <i class="fas fa-file-contract mr-3 text-gray-500"></i> Syarat & Ketentuan
                </a>
                <a href="{{ route('sitemap') }}" class="block text-gray-300 text-sm py-3">
                    <i class="fas fa-sitemap mr-3 text-gray-500"></i> Sitemap
                </a>
            </div>
        </nav>
        
        <!-- Menu Footer -->
        <div class="p-6 border-t border-gray-800">
            <a href="{{ route('login') }}" class="flex items-center justify-center gap-2 bg-white/10 text-white font-semibold py-4 px-6 rounded-xl border border-white/20">
                <i class="fas fa-sign-in-alt text-xl"></i>
                <span>Daftar / Masuk</span>
            </a>
        </div>
    </div>
</div>

<script>
function toggleMobileMenu() {
    const menu = document.getElementById('mobileMenu');
    menu.classList.toggle('translate-x-full');
}
</script>
