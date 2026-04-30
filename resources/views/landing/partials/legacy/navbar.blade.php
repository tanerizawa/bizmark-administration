    <!-- ============================================
         NAVIGATION - MILLER'S LAW OPTIMIZATION
         Neuroscience Principles:
         - 6 nav items (within 7±2 optimal range)
         - Neural priority attributes for attention
         - Visual hierarchy: Primary CTA stands out
         - Reduced cognitive load
         ============================================ -->
    <nav class="navbar" role="navigation" aria-label="Main navigation">
        <div class="container mx-auto px-4 py-4">
            <div class="flex justify-between items-center">
                <!-- Brand: Highest attention priority -->
                <a href="#home" class="flex items-center space-x-3 group" data-neural-priority="highest" aria-label="Bizmark.ID Home">
                    <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-700 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform">
                        <i class="fas fa-shield-alt text-white text-xl"></i>
                    </div>
                    <span class="text-xl font-bold">Bizmark.ID</span>
                </a>
                
                <!-- Desktop Navigation: Miller's Law (6 items) -->
                <div class="hidden md:flex items-center" style="gap: var(--spacing-lg);">
                    <a href="#services" 
                       class="transition-colors font-medium" 
                       style="color: inherit;"
                       onmouseover="this.style.color='var(--color-primary)'"
                       onmouseout="this.style.color='inherit'"
                       data-neural-priority="high"
                       aria-label="Lihat layanan perizinan">
                        Layanan
                    </a>
                    <a href="#digital-tools" 
                       class="transition-colors font-medium inline-flex items-center gap-1" 
                       style="color: inherit;"
                       onmouseover="this.style.color='var(--color-primary)'"
                       onmouseout="this.style.color='inherit'"
                       data-neural-priority="high"
                       aria-label="Alat digital gratis">
                        <span>Tools</span>
                        <span class="px-1.5 py-0.5 text-[10px] font-bold bg-green-500 text-white rounded">BARU</span>
                    </a>
                    <a href="#why-us" 
                       class="transition-colors font-medium" 
                       style="color: inherit;"
                       onmouseover="this.style.color='var(--color-primary)'"
                       onmouseout="this.style.color='inherit'"
                       data-neural-priority="medium"
                       aria-label="Keunggulan Bizmark">
                        Keunggulan
                    </a>
                    <a href="{{ route('about.id') }}" 
                       class="transition-colors font-medium" 
                       style="color: inherit;"
                       onmouseover="this.style.color='var(--color-primary)'"
                       onmouseout="this.style.color='inherit'"
                       data-neural-priority="medium"
                       aria-label="Tentang Bizmark">
                        Tentang
                    </a>
                    <a href="#contact" 
                       class="transition-colors font-medium" 
                       style="color: inherit;"
                       onmouseover="this.style.color='var(--color-primary)'"
                       onmouseout="this.style.color='inherit'"
                       data-neural-priority="medium"
                       aria-label="Hubungi kami">
                        Kontak
                    </a>
                    
                    <!-- Primary CTA: Visual hierarchy emphasis -->
                    <a href="{{ route('login') }}" 
                       class="inline-flex items-center gap-2 font-bold transition-all"
                       style="padding: var(--spacing-sm) var(--spacing-lg); 
                              background: var(--gradient-primary); 
                              border-radius: var(--radius-md);
                              box-shadow: var(--shadow-sm);"
                       data-neural-priority="highest"
                       aria-label="Login ke dashboard">
                        <i class="fas fa-sign-in-alt" aria-hidden="true"></i>
                        <span>Login</span>
                    </a>
                </div>
                
                <!-- Mobile Menu Toggle -->
                <button class="md:hidden text-2xl p-2 rounded-lg transition" 
                        style="background-color: transparent;"
                        onmouseover="this.style.backgroundColor='rgba(255,255,255,0.1)'"
                        onmouseout="this.style.backgroundColor='transparent'"
                        onclick="toggleMobileMenu()" 
                        aria-label="Toggle mobile menu"
                        aria-expanded="false"
                        aria-controls="mobileMenu">
                    <i class="fas fa-bars"></i>
                </button>
            </div>
        </div>
    </nav>
    
    <!-- Mobile Menu: Progressive Disclosure -->
    <div id="mobileMenu" class="mobile-menu" role="menu" aria-label="Mobile navigation">
        <a href="{{ app()->getLocale() === 'en' ? route('services.index.en') : route('services.index.id') }}" class="block py-3 transition font-medium" style="color: inherit;" onmouseover="this.style.color='var(--color-primary)'" onmouseout="this.style.color='inherit'" role="menuitem" data-neural-priority="high">
            <i class="fas fa-briefcase mr-2" style="color: var(--color-primary);"></i>Layanan
        </a>
        <a href="#digital-tools" class="block py-3 transition font-medium flex items-center" style="color: inherit;" onmouseover="this.style.color='var(--color-primary)'" onmouseout="this.style.color='inherit'" role="menuitem" data-neural-priority="high">
            <i class="fas fa-tools mr-2" style="color: var(--color-primary);"></i>
            <span>Alat Digital</span>
            <span class="ml-2 px-2 py-0.5 text-[10px] font-bold bg-green-500 text-white rounded">BARU</span>
        </a>
        <a href="#why-us" class="block py-3 transition font-medium" style="color: inherit;" onmouseover="this.style.color='var(--color-primary)'" onmouseout="this.style.color='inherit'" role="menuitem" data-neural-priority="medium">
            <i class="fas fa-trophy mr-2" style="color: var(--color-primary);"></i>Keunggulan
        </a>
        <a href="{{ route('about.id') }}" class="block py-3 transition font-medium" style="color: inherit;" onmouseover="this.style.color='var(--color-primary)'" onmouseout="this.style.color='inherit'" role="menuitem" data-neural-priority="medium">
            <i class="fas fa-info-circle mr-2" style="color: var(--color-primary);"></i>Tentang
        </a>
        <a href="#contact" class="block py-3 transition font-medium" style="color: inherit;" onmouseover="this.style.color='var(--color-primary)'" onmouseout="this.style.color='inherit'" role="menuitem" data-neural-priority="medium">
            <i class="fas fa-envelope mr-2" style="color: var(--color-primary);"></i>Kontak
        </a>
        
        <!-- Primary CTA in mobile -->
        <a href="{{ route('login') }}" 
           class="block py-3 mt-4 font-bold text-center text-white rounded-lg transition"
           style="background: var(--gradient-primary); box-shadow: var(--shadow-md);"
           role="menuitem" 
           data-neural-priority="highest">
            <i class="fas fa-sign-in-alt mr-2"></i>Login
        </a>
    </div>
