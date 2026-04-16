@php
    $isLandingPage = request()->routeIs('landing') || request()->routeIs('landing.id') || request()->routeIs('landing.en');
    $landingUrl = app()->getLocale() === 'en' ? route('landing.en') : route('landing.id');
    $blogUrl = app()->getLocale() === 'en' ? route('blog.index.en') : route('blog.index.id');
    $landingMetrics = config('landing_metrics');
    $contact = $landingMetrics['contact'] ?? [];
    $phoneNumber = $contact['phone'] ?? '+62 838 7960 2855';
    $whatsappNumber = $contact['whatsapp'] ?? '6283879602855';
    $whatsappLink = $contact['whatsapp_link'] ?? ('https://wa.me/' . $whatsappNumber);
    $emailAddress = $contact['email'] ?? 'info@bizmark.id';
@endphp

<!-- Mobile Menu -->
<div id="mobileMenu" 
     class="fixed inset-0 z-[60] hidden"
     role="dialog"
     aria-label="Mobile navigation menu"
     aria-modal="true">
    <!-- Backdrop (animated opacity) -->
    <div class="mobile-menu-backdrop fixed inset-0 bg-black/50 backdrop-blur-sm" 
         onclick="toggleMobileMenu()"
         style="opacity: 0; transition: opacity 0.3s ease;"></div>
    
    <!-- Menu Panel (animated slide-in from right) -->
    <div class="mobile-menu-panel fixed top-0 right-0 w-80 max-w-[85vw] h-full gradient-primary shadow-2xl"
         style="transform: translateX(100%); transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);">
        <div class="p-6 h-full flex flex-col overflow-y-auto overscroll-contain -webkit-overflow-scrolling-touch">
            <!-- Close Button -->
            <div class="flex justify-between items-center mb-8">
                <span class="text-xl font-bold text-white" id="mobile-menu-title">Menu</span>
                <button onclick="toggleMobileMenu()" 
                        class="text-white hover:bg-white/10 rounded-lg p-3 -mr-1 transition active:scale-95 min-w-[44px] min-h-[44px] flex items-center justify-center" 
                        aria-label="{{ app()->getLocale() === 'id' ? 'Tutup menu navigasi' : 'Close navigation menu' }}">
                    <i class="fas fa-times text-2xl" aria-hidden="true"></i>
                </button>
            </div>
            
            <!-- Navigation Links (min 44px touch targets) -->
            <nav class="flex flex-col space-y-1 flex-1" role="navigation" aria-labelledby="mobile-menu-title">
                <a href="{{ $isLandingPage ? '#home' : $landingUrl . '#home' }}" 
                   class="text-white/95 hover:text-white transition px-4 py-3.5 rounded-lg hover:bg-white/10 active:bg-white/20 flex items-center min-h-[44px]" 
                   onclick="toggleMobileMenu()">
                    <i class="fas fa-home w-6 inline-block text-white/70" aria-hidden="true"></i>
                    <span class="ml-1">{{ __('landing.nav.home') }}</span>
                </a>
                <a href="{{ $isLandingPage ? '#services' : $landingUrl . '#services' }}" 
                   class="text-white transition px-4 py-3.5 rounded-lg hover:bg-white/10 active:bg-white/20 flex items-center min-h-[44px]" 
                   onclick="toggleMobileMenu()">
                    <i class="fas fa-briefcase w-6 inline-block text-white/70" aria-hidden="true"></i>
                    <span class="ml-1">{{ __('landing.nav.services') }}</span>
                </a>
                <a href="{{ $isLandingPage ? '#process' : $landingUrl . '#process' }}" 
                   class="text-white transition px-4 py-3.5 rounded-lg hover:bg-white/10 active:bg-white/20 flex items-center min-h-[44px]" 
                   onclick="toggleMobileMenu()">
                    <i class="fas fa-tasks w-6 inline-block text-white/70" aria-hidden="true"></i>
                    <span class="ml-1">{{ __('landing.nav.process') }}</span>
                </a>
                <a href="{{ $isLandingPage ? '#about' : $landingUrl . '#about' }}" 
                   class="text-white transition px-4 py-3.5 rounded-lg hover:bg-white/10 active:bg-white/20 flex items-center min-h-[44px]" 
                   onclick="toggleMobileMenu()">
                    <i class="fas fa-info-circle w-6 inline-block text-white/70" aria-hidden="true"></i>
                    <span class="ml-1">{{ __('landing.nav.about') }}</span>
                </a>
                <a href="{{ $blogUrl }}" 
                   class="text-white transition px-4 py-3.5 rounded-lg hover:bg-white/10 active:bg-white/20 flex items-center min-h-[44px]" 
                   onclick="toggleMobileMenu()">
                    <i class="fas fa-newspaper w-6 inline-block text-white/70" aria-hidden="true"></i>
                    <span class="ml-1">{{ __('landing.nav.blog') }}</span>
                </a>
                
                <!-- Permohonan Section (Highlighted) -->
                <div class="border-t border-white/20 my-2"></div>
                <a href="{{ route('permohonan.index') }}" 
                   class="text-white transition px-4 py-3.5 rounded-lg bg-gradient-to-r from-orange-500/30 to-amber-500/30 hover:from-orange-500/40 hover:to-amber-500/40 active:from-orange-500/50 active:to-amber-500/50 flex items-center min-h-[44px] border border-orange-400/30" 
                   onclick="toggleMobileMenu()">
                    <i class="fas fa-file-invoice-dollar w-6 inline-block text-orange-300" aria-hidden="true"></i>
                    <span class="ml-1 font-semibold">Permohonan Biaya</span>
                    <span class="ml-auto flex h-2 w-2">
                        <span class="animate-ping absolute inline-flex h-2 w-2 rounded-full bg-orange-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-orange-400"></span>
                    </span>
                </a>
                
                <!-- Tools Section -->
                <div class="border-t border-white/20 my-2"></div>
                <p class="px-4 py-1 text-white/50 text-xs uppercase tracking-wider font-semibold">Tools</p>
                <a href="{{ route('polygon.shp.index') }}" 
                   class="text-white transition px-4 py-3.5 rounded-lg hover:bg-white/10 active:bg-white/20 flex items-center min-h-[44px]" 
                   onclick="toggleMobileMenu()">
                    <i class="fas fa-draw-polygon w-6 inline-block text-emerald-300" aria-hidden="true"></i>
                    <span class="ml-1">Polygon SHP Maker</span>
                </a>
                <a href="{{ route('calculator.index') }}" 
                   class="text-white transition px-4 py-3.5 rounded-lg hover:bg-white/10 active:bg-white/20 flex items-center min-h-[44px]" 
                   onclick="toggleMobileMenu()">
                    <i class="fas fa-calculator w-6 inline-block text-purple-300" aria-hidden="true"></i>
                    <span class="ml-1">Kalkulator Perizinan</span>
                </a>
                <a href="{{ route('consultation.index') }}" 
                   class="text-white transition px-4 py-3.5 rounded-lg hover:bg-white/10 active:bg-white/20 flex items-center min-h-[44px]" 
                   onclick="toggleMobileMenu()">
                    <i class="fas fa-comments-dollar w-6 inline-block text-blue-300" aria-hidden="true"></i>
                    <span class="ml-1">Estimasi Biaya</span>
                </a>
                
                <!-- Divider -->
                <div class="border-t border-white/20 my-4"></div>
                
                <!-- Locale Switcher for Mobile -->
                <div class="px-4 py-3">
                    <p class="text-white/60 text-xs uppercase tracking-wider font-semibold mb-3">{{ __('landing.footer.language') }}</p>
                    <div class="flex gap-2">
                        <a href="{{ route('locale.set', 'id') }}" 
                           class="flex-1 flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg transition min-h-[44px] {{ app()->getLocale() === 'id' ? 'bg-white font-semibold text-[color:var(--color-primary)]' : 'bg-white/10 text-white hover:bg-white/20 active:bg-white/30' }}">
                            <span>🇮🇩</span>
                            <span class="text-sm">Indonesia</span>
                        </a>
                        <a href="{{ route('locale.set', 'en') }}" 
                           class="flex-1 flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg transition min-h-[44px] {{ app()->getLocale() === 'en' ? 'bg-white font-semibold text-[color:var(--color-primary)]' : 'bg-white/10 text-white hover:bg-white/20 active:bg-white/30' }}">
                            <span>🇬🇧</span>
                            <span class="text-sm">English</span>
                        </a>
                    </div>
                </div>
                
                <!-- CTA Button -->
                @if(auth('client')->check())
                    <a href="{{ route('client.dashboard') }}" 
                       class="mx-4 mt-4 block text-center px-6 py-3.5 bg-white hover:bg-white/95 active:bg-white/90 rounded-lg font-bold transition text-[color:var(--color-primary)] min-h-[44px] active:scale-[0.98]" 
                       onclick="toggleMobileMenu()">
                        <i class="fas fa-user-circle mr-1"></i> {{ auth('client')->user()->name }}
                    </a>
                    <form method="POST" action="{{ route('client.logout') }}" class="mx-4 mt-2">
                        @csrf
                        <button type="submit" class="w-full text-center px-6 py-2.5 bg-white/20 hover:bg-white/30 rounded-lg font-medium transition text-white text-sm min-h-[44px]">
                            <i class="fas fa-sign-out-alt mr-1"></i> Logout
                        </button>
                    </form>
                @elseif(auth('web')->check())
                    <a href="/dashboard" 
                       class="mx-4 mt-4 block text-center px-6 py-3.5 bg-white hover:bg-white/95 active:bg-white/90 rounded-lg font-bold transition text-[color:var(--color-primary)] min-h-[44px] active:scale-[0.98]" 
                       onclick="toggleMobileMenu()">
                        <i class="fas fa-tachometer-alt mr-1"></i> Dashboard
                    </a>
                @else
                    <a href="/login" 
                       class="mx-4 mt-4 block text-center px-6 py-3.5 bg-white hover:bg-white/95 active:bg-white/90 rounded-lg font-bold transition text-[color:var(--color-primary)] min-h-[44px] active:scale-[0.98]" 
                       onclick="toggleMobileMenu()">
                        <i class="fas fa-sign-in-alt mr-1"></i> {{ app()->getLocale() === 'id' ? 'Login / Daftar' : 'Login / Sign Up' }}
                    </a>
                @endif
            </nav>
            
            <!-- Footer Info -->
            <div class="pt-6 border-t border-white/20 mt-auto">
                <p class="text-white/80 text-sm mb-3">{{ __('landing.footer.contact_us') }}:</p>
                <a href="{{ $whatsappLink }}" 
                   class="text-white flex items-center gap-3 mb-2 py-2 min-h-[44px] active:opacity-80"
                   target="_blank"
                   rel="noopener noreferrer">
                    <i class="fab fa-whatsapp text-lg" aria-hidden="true"></i>
                    <span class="text-sm">{{ $phoneNumber }}</span>
                </a>
                <a href="mailto:{{ $emailAddress }}" 
                   class="text-white flex items-center gap-3 py-2 min-h-[44px] active:opacity-80">
                    <i class="fas fa-envelope text-lg" aria-hidden="true"></i>
                    <span class="text-sm">{{ $emailAddress }}</span>
                </a>
            </div>
        </div>
    </div>
</div>

<style>
/* Mobile menu animation states */
#mobileMenu .mobile-menu-backdrop.active {
    opacity: 1 !important;
}
#mobileMenu .mobile-menu-panel.active {
    transform: translateX(0) !important;
}
/* Smooth scroll momentum on iOS */
#mobileMenu .mobile-menu-panel > div {
    -webkit-overflow-scrolling: touch;
}
</style>
