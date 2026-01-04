<!-- Footer -->
<footer class="bg-gray-900 text-gray-300 py-12">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid md:grid-cols-4 gap-8 mb-8">
            <div>
                <div class="text-xl font-bold text-white mb-4">
                    <i class="fas fa-certificate text-blue-400 mr-2"></i>Bizmark.ID
                </div>
                <p class="text-sm">{{ __('landing.footer.tagline') }}</p>
                <div class="mt-4 flex gap-3">
                    <a href="https://www.linkedin.com/company/bizmark-id" target="_blank" class="text-gray-400 hover:text-blue-400 transition" aria-label="LinkedIn">
                        <i class="fab fa-linkedin text-xl"></i>
                    </a>
                    <a href="https://www.facebook.com/bizmark.id" target="_blank" class="text-gray-400 hover:text-blue-400 transition" aria-label="Facebook">
                        <i class="fab fa-facebook text-xl"></i>
                    </a>
                    <a href="https://www.instagram.com/bizmark.id" target="_blank" class="text-gray-400 hover:text-pink-400 transition" aria-label="Instagram">
                        <i class="fab fa-instagram text-xl"></i>
                    </a>
                </div>
            </div>
            
            <div>
                <h4 class="text-white font-semibold mb-4">{{ __('landing.footer.navigation') }}</h4>
                <ul class="space-y-2 text-sm">
                    @php
                        $landingRoute = app()->getLocale() === 'en' ? route('landing.en') : route('landing.id');
                        $blogRoute = app()->getLocale() === 'en' ? route('blog.index.en') : route('blog.index.id');
                        $careerRoute = route('career.index');
                    @endphp
                    <li><a href="{{ $landingRoute }}#services" class="hover:text-white transition">{{ __('landing.footer.navigation') }}</a></li>
                    <li><a href="{{ $landingRoute }}#process" class="hover:text-white transition">{{ __('landing.nav.process') }}</a></li>
                    <li><a href="{{ $landingRoute }}#about" class="hover:text-white transition">{{ __('landing.nav.about') }}</a></li>
                    <li><a href="{{ $blogRoute }}" class="hover:text-white transition">{{ __('landing.nav.blog') }}</a></li>
                    <li><a href="{{ $careerRoute }}" class="hover:text-white transition">Karir</a></li>
                </ul>
            </div>
            
            <div>
                <h4 class="text-white font-semibold mb-4">{{ __('landing.footer.contact_us') }}</h4>
                <ul class="space-y-2 text-sm">
                    <li>
                        <a href="mailto:cs@bizmark.id" class="hover:text-white transition">
                            <i class="fas fa-envelope mr-2"></i>cs@bizmark.id
                        </a>
                    </li>
                    <li>
                        <a href="tel:+6283879602855" class="hover:text-white transition">
                            <i class="fas fa-phone mr-2"></i>+62 838 7960 2855
                        </a>
                    </li>
                    <li>
                        <a href="https://wa.me/6283879602855" target="_blank" class="hover:text-white transition">
                            <i class="fab fa-whatsapp mr-2"></i>WhatsApp
                        </a>
                    </li>
                    <li><i class="fas fa-map-marker-alt mr-2"></i>Karawang, Jawa Barat</li>
                </ul>
            </div>
            
            <div>
                <h4 class="text-white font-semibold mb-4">{{ __('landing.footer.legal') }}</h4>
                <ul class="space-y-2 text-sm">
                    <li><a href="{{ route('privacy.policy.id') }}" class="hover:text-white transition">{{ __('landing.footer.privacy_policy') }}</a></li>
                    <li><a href="{{ route('terms.conditions.id') }}" class="hover:text-white transition">{{ __('landing.footer.terms_conditions') }}</a></li>
                </ul>
                <h4 class="text-white font-semibold mb-4 mt-6">Bahasa</h4>
                <ul class="space-y-2 text-sm">
                    <li><a href="{{ route('locale.set', 'id') }}" class="hover:text-white transition {{ app()->getLocale() == 'id' ? 'text-blue-400 font-semibold' : '' }}">🇮🇩 Indonesia</a></li>
                    <li><a href="{{ route('locale.set', 'en') }}" class="hover:text-white transition {{ app()->getLocale() == 'en' ? 'text-blue-400 font-semibold' : '' }}">🇬🇧 English</a></li>
                </ul>
            </div>
        </div>
        
        <div class="border-t border-gray-800 pt-8 text-center text-sm">
            <p>&copy; {{ date('Y') }} Bizmark.ID. {{ __('landing.footer.copyright') }}</p>
            <p class="mt-2 text-gray-500 text-xs">PT Cangah Pajaratan Mandiri - Konsultan Perizinan & Bisnis Terpercaya</p>
        </div>
    </div>
</footer>
