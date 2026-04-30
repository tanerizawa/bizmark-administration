@php
    $locale = app()->getLocale();
    $landingUrl = $locale === 'en' ? route('landing.en') : route('landing.id');
    $servicesUrl = $locale === 'en' ? route('services.index.en') : route('services.index.id');
    $processUrl = $locale === 'en' ? route('process.en') : route('process.id');
    $aboutUrl = $locale === 'en' ? route('about.en') : route('about.id');
    $blogUrl = $locale === 'en' ? route('blog.index.en') : route('blog.index.id');
    $landingMetrics = config('landing_metrics');
    $contact = $landingMetrics['contact'] ?? [];
    $phoneNumber = $contact['phone'] ?? '+62 838 7960 2855';
    $whatsappNumber = $contact['whatsapp'] ?? '6283879602855';
    $whatsappLink = $contact['whatsapp_link'] ?? ('https://wa.me/' . $whatsappNumber);
    $emailAddress = $contact['email'] ?? 'info@bizmark.id';
@endphp

<div id="mobileMenu" 
     class="fixed inset-0 z-[60] hidden"
     role="dialog"
     aria-label="Mobile navigation menu"
     aria-modal="true">
    <div class="mobile-menu-backdrop fixed inset-0 bg-black/50 backdrop-blur-sm" 
         onclick="toggleMobileMenu()"
         style="opacity: 0; transition: opacity 0.3s ease;"></div>

    <div class="mobile-menu-panel fixed top-0 right-0 w-80 max-w-[88vw] h-full shadow-2xl"
         style="transform: translateX(100%); transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);">
        <div class="p-6 h-full flex flex-col overflow-y-auto overscroll-contain -webkit-overflow-scrolling-touch" style="background:var(--surface-dark);">
            <div class="flex justify-between items-center mb-7">
                <div id="mobile-menu-title" class="text-base font-semibold tracking-wide uppercase text-white/80">{{ $locale === 'id' ? 'Navigasi' : 'Navigation' }}</div>
                <button onclick="toggleMobileMenu()" 
                        class="text-white/90 hover:bg-white/10 rounded-lg p-2.5 -mr-1 transition active:scale-95 min-w-[44px] min-h-[44px] flex items-center justify-center" 
                        aria-label="{{ $locale === 'id' ? 'Tutup menu navigasi' : 'Close navigation menu' }}">
                    <i class="fas fa-times text-lg" aria-hidden="true"></i>
                </button>
            </div>

            <nav class="flex flex-col space-y-1 flex-1" role="navigation" aria-labelledby="mobile-menu-title">
                <a href="{{ $landingUrl }}" 
                   class="text-white/95 hover:text-white transition px-4 py-3.5 rounded-lg hover:bg-white/10 active:bg-white/20 flex items-center min-h-[44px]"
                   onclick="toggleMobileMenu()">
                    <i class="fas fa-home w-6 inline-block text-white/70" aria-hidden="true"></i>
                    <span class="ml-1">{{ __('landing.nav.home') }}</span>
                </a>
                <a href="{{ $servicesUrl }}"
                   class="text-white transition px-4 py-3.5 rounded-lg hover:bg-white/10 active:bg-white/20 flex items-center min-h-[44px]" 
                   onclick="toggleMobileMenu()">
                    <i class="fas fa-briefcase w-6 inline-block text-white/70" aria-hidden="true"></i>
                    <span class="ml-1">{{ __('landing.nav.services') }}</span>
                </a>
                <a href="{{ $processUrl }}"
                   class="text-white transition px-4 py-3.5 rounded-lg hover:bg-white/10 active:bg-white/20 flex items-center min-h-[44px]" 
                   onclick="toggleMobileMenu()">
                    <i class="fas fa-tasks w-6 inline-block text-white/70" aria-hidden="true"></i>
                    <span class="ml-1">{{ __('landing.nav.process') }}</span>
                </a>
                <a href="{{ $aboutUrl }}"
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

                <div class="border-t border-white/15 my-2"></div>
                <a href="{{ route('permohonan.index') }}"
                   class="text-white transition px-4 py-3.5 rounded-lg bg-orange-500/20 hover:bg-orange-500/30 active:bg-orange-500/35 flex items-center min-h-[44px] border border-orange-300/35"
                   onclick="toggleMobileMenu()">
                    <i class="fas fa-file-invoice-dollar w-6 inline-block text-orange-300" aria-hidden="true"></i>
                    <span class="ml-1 font-semibold">Permohonan</span>
                </a>

                <div class="border-t border-white/15 my-2"></div>
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

                <div class="border-t border-white/15 my-4"></div>
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
                        <i class="fas fa-sign-in-alt mr-1"></i> {{ $locale === 'id' ? 'Login / Daftar' : 'Login / Sign Up' }}
                    </a>
                @endif
            </nav>

            <div class="pt-6 border-t border-white/15 mt-auto">
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
