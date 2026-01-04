@php
    $isLandingPage = request()->routeIs('landing') || request()->routeIs('landing.id') || request()->routeIs('landing.en');
    $landingUrl = app()->getLocale() === 'en' ? route('landing.en') : route('landing.id');
    $blogUrl = app()->getLocale() === 'en' ? route('blog.index.en') : route('blog.index.id');
@endphp

<!-- Mobile Menu -->
<div id="mobileMenu" 
     class="fixed inset-0 z-[60] hidden"
     role="dialog"
     aria-label="Mobile navigation menu"
     aria-modal="true">
    <!-- Backdrop -->
    <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" onclick="toggleMobileMenu()"></div>
    
    <!-- Menu Panel -->
    <div class="fixed top-0 right-0 w-80 max-w-[85vw] h-full bg-gradient-to-br from-blue-900 to-blue-800 shadow-2xl">
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
                    <span>{{ __('landing.nav.home') }}</span>
                </a>
                <a href="{{ $isLandingPage ? '#services' : $landingUrl . '#services' }}" 
                   class="text-white hover:text-blue-200 transition px-4 py-3 rounded-lg hover:bg-white/10" 
                   onclick="toggleMobileMenu()"
                   aria-label="View our services">
                    <i class="fas fa-briefcase w-6 inline-block" aria-hidden="true"></i>
                    <span>{{ __('landing.nav.services') }}</span>
                </a>
                <a href="{{ $isLandingPage ? '#process' : $landingUrl . '#process' }}" 
                   class="text-white hover:text-blue-200 transition px-4 py-3 rounded-lg hover:bg-white/10" 
                   onclick="toggleMobileMenu()"
                   aria-label="View our process">
                    <i class="fas fa-tasks w-6 inline-block" aria-hidden="true"></i>
                    <span>{{ __('landing.nav.process') }}</span>
                </a>
                <a href="{{ $isLandingPage ? '#about' : $landingUrl . '#about' }}" 
                   class="text-white hover:text-blue-200 transition px-4 py-3 rounded-lg hover:bg-white/10" 
                   onclick="toggleMobileMenu()"
                   aria-label="Learn about us">
                    <i class="fas fa-info-circle w-6 inline-block" aria-hidden="true"></i>
                    <span>{{ __('landing.nav.about') }}</span>
                </a>
                <a href="{{ $blogUrl }}" 
                   class="text-white hover:text-blue-200 transition px-4 py-3 rounded-lg hover:bg-white/10" 
                   onclick="toggleMobileMenu()"
                   aria-label="Read our articles">
                    <i class="fas fa-newspaper w-6 inline-block" aria-hidden="true"></i>
                    <span>{{ __('landing.nav.blog') }}</span>
                </a>
                
                <!-- Divider -->
                <div class="border-t border-white/20 my-4"></div>
                
                <!-- Locale Switcher for Mobile -->
                <div class="px-4 py-3">
                    <p class="text-white/60 text-xs uppercase tracking-wider font-semibold mb-3">Language / Bahasa</p>
                    <div class="flex gap-2">
                        <a href="{{ route('locale.set', 'id') }}" 
                           class="flex-1 flex items-center justify-center gap-2 px-4 py-2 rounded-lg transition {{ app()->getLocale() === 'id' ? 'bg-white text-blue-900 font-semibold' : 'bg-white/10 text-white hover:bg-white/20' }}">
                            <span>🇮🇩</span>
                            <span class="text-sm">Indonesia</span>
                        </a>
                        <a href="{{ route('locale.set', 'en') }}" 
                           class="flex-1 flex items-center justify-center gap-2 px-4 py-2 rounded-lg transition {{ app()->getLocale() === 'en' ? 'bg-white text-blue-900 font-semibold' : 'bg-white/10 text-white hover:bg-white/20' }}">
                            <span>🇬🇧</span>
                            <span class="text-sm">English</span>
                        </a>
                    </div>
                </div>
                
                <!-- CTA Button -->
                <a href="{{ $isLandingPage ? '#contact' : $landingUrl . '#contact' }}" 
                   class="mx-4 mt-4 block text-center px-6 py-3 bg-white text-blue-900 rounded-lg font-bold hover:bg-blue-50 transition" 
                   onclick="toggleMobileMenu()"
                   aria-label="Get started">
                    {{ __('landing.nav.get_started') }}
                </a>
            </nav>
            
            <!-- Footer Info -->
            <div class="pt-6 border-t border-white/20 mt-auto">
                <p class="text-white/80 text-sm mb-2">{{ __('landing.footer.contact_us') }}:</p>
                <a href="https://wa.me/6283879602855" 
                   class="text-white hover:text-blue-200 flex items-center gap-2 mb-2"
                   target="_blank"
                   rel="noopener noreferrer"
                   aria-label="Contact us on WhatsApp">
                    <i class="fab fa-whatsapp" aria-hidden="true"></i>
                    <span class="text-sm">+62 838 7960 2855</span>
                </a>
                <a href="mailto:cs@bizmark.id" 
                   class="text-white hover:text-blue-200 flex items-center gap-2"
                   aria-label="Email us">
                    <i class="fas fa-envelope" aria-hidden="true"></i>
                    <span class="text-sm">cs@bizmark.id</span>
                </a>
            </div>
        </div>
    </div>
</div>
