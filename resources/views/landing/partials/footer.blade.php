<!-- Footer -->
@php
    $landingMetrics = config('landing_metrics');
    $contact = $landingMetrics['contact'] ?? [];
    $phoneNumber = $contact['phone'] ?? '+62 838 7960 2855';
    $whatsappNumber = $contact['whatsapp'] ?? '6283879602855';
    $whatsappLink = $contact['whatsapp_link'] ?? ('https://wa.me/' . $whatsappNumber);
    $emailAddress = $contact['email'] ?? 'info@bizmark.id';
@endphp
<footer class="bg-gray-900 text-gray-300 py-8 mt-auto">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Top row: Jangkauan Layanan cities --}}
        @php
            $footerCities = config('programmatic_seo.cities', []);
            $footerCityByProvince = collect($footerCities)->groupBy('province')->sortKeys();
            $footerProvinceOrder = ['Jawa Barat', 'DKI Jakarta', 'Banten', 'Jawa Tengah', 'DI Yogyakarta', 'Jawa Timur', 'Sumatera Utara', 'Sumatera Selatan', 'Kepulauan Riau', 'Kalimantan Timur', 'Sulawesi Selatan', 'Bali'];
            $footerSorted = collect($footerProvinceOrder)
                ->filter(fn($p) => $footerCityByProvince->has($p))
                ->merge($footerCityByProvince->keys()->diff($footerProvinceOrder)->sort())
                ->unique();
        @endphp
        @if(count($footerCities) > 0)
        <div class="mb-6 pb-6 border-b border-gray-800">
            <div class="flex items-center gap-2 mb-3">
                <i class="fas fa-map-marked-alt text-xs" style="color: var(--color-accent, #0ea5e9);"></i>
                <span class="text-xs font-semibold text-white uppercase tracking-wider">Jangkauan Layanan</span>
            </div>
            <div class="flex flex-wrap gap-x-1.5 gap-y-1 text-xs">
                @foreach($footerSorted as $pIdx => $province)
                @php $fCities = $footerCityByProvince[$province]; @endphp
                @if($pIdx > 0)<span class="text-gray-700">·</span>@endif
                @foreach($fCities as $fcIdx => $fc)
                <a href="{{ url('/layanan/kota/' . $fc['slug']) }}" class="text-gray-400 hover:text-white transition">{{ $fc['name'] }}</a>@if(!$loop->last)<span class="text-gray-700">,</span>@endif
                @endforeach
                @endforeach
            </div>
        </div>
        @endif

        <div class="grid md:grid-cols-4 gap-8 mb-8">
            <div>
                <div class="text-xl font-bold text-white mb-4">
                    <i class="fas fa-certificate mr-2 text-[color:var(--color-primary)]"></i>Bizmark.ID
                </div>
                <p class="text-sm text-gray-300">{{ __('landing.footer.tagline') }}</p>
                <div class="mt-4 flex gap-4">
                    <a href="https://www.linkedin.com/company/bizmark-id" target="_blank" rel="noopener noreferrer" class="text-gray-400 hover:text-[color:var(--color-primary-light)] transition" aria-label="LinkedIn">
                        <i class="fab fa-linkedin text-2xl"></i>
                    </a>
                    <a href="https://www.facebook.com/bizmark.id" target="_blank" rel="noopener noreferrer" class="text-gray-400 hover:text-[color:var(--color-primary-light)] transition" aria-label="Facebook">
                        <i class="fab fa-facebook text-2xl"></i>
                    </a>
                    <a href="https://www.instagram.com/bizmark.id" target="_blank" rel="noopener noreferrer" class="text-gray-400 hover:text-[color:var(--color-primary-light)] transition" aria-label="Instagram">
                        <i class="fab fa-instagram text-2xl"></i>
                    </a>
                </div>
            </div>
            
            <div>
                <h4 class="text-white font-semibold mb-4">{{ __('landing.footer.navigation') }}</h4>
                <ul class="space-y-1 text-sm">
                    @php
                        $landingRoute = app()->getLocale() === 'en' ? route('landing.en') : route('landing.id');
                        $blogRoute = app()->getLocale() === 'en' ? route('blog.index.en') : route('blog.index.id');
                        $careerRoute = route('career.index');
                    @endphp
                    <li><a href="{{ app()->getLocale() === 'en' ? route('services.index.en') : route('services.index.id') }}" class="text-gray-300 hover:text-white transition inline-block py-1.5 min-h-[44px] flex items-center">{{ __('landing.nav.services') }}</a></li>
                    <li><a href="{{ $landingRoute }}#process" class="text-gray-300 hover:text-white transition inline-block py-1.5 min-h-[44px] flex items-center">{{ __('landing.nav.process') }}</a></li>
                    <li><a href="{{ $landingRoute }}#about" class="text-gray-300 hover:text-white transition inline-block py-1.5 min-h-[44px] flex items-center">{{ __('landing.nav.about') }}</a></li>
                    <li><a href="{{ $blogRoute }}" class="text-gray-300 hover:text-white transition inline-block py-1.5 min-h-[44px] flex items-center">{{ __('landing.nav.blog') }}</a></li>
                    <li><a href="{{ $careerRoute }}" class="text-gray-300 hover:text-white transition inline-block py-1.5 min-h-[44px] flex items-center">{{ __('landing.footer.careers') }}</a></li>
                </ul>
            </div>
            
            <div>
                <h4 class="text-white font-semibold mb-4">{{ __('landing.footer.contact_us') }}</h4>
                <ul class="space-y-2 text-sm">
                    <li>
                        <a href="mailto:{{ $emailAddress }}" class="text-gray-300 hover:text-white transition">
                            <i class="fas fa-envelope mr-2"></i>{{ $emailAddress }}
                        </a>
                    </li>
                    <li>
                        <a href="tel:{{ str_replace(' ', '', $phoneNumber) }}" class="text-gray-300 hover:text-white transition">
                            <i class="fas fa-phone mr-2"></i>{{ $phoneNumber }}
                        </a>
                    </li>
                    <li>
                        <a href="{{ $whatsappLink }}" target="_blank" rel="noopener noreferrer" class="text-gray-300 hover:text-white transition inline-block py-1.5">
                            <i class="fab fa-whatsapp mr-2"></i>{{ __('landing.footer.whatsapp') }}
                        </a>
                    </li>
                    <li class="text-gray-300 py-1.5"><i class="fas fa-map-marker-alt mr-2"></i>{{ __('landing.footer.location') }}</li>
                </ul>
            </div>
            
            <div>
                <h4 class="text-white font-semibold mb-4">{{ __('landing.footer.legal') }}</h4>
                <ul class="space-y-2 text-sm">
                    @php
                        $privacyRoute = app()->getLocale() === 'en' ? route('privacy.policy.en') : route('privacy.policy.id');
                        $termsRoute = app()->getLocale() === 'en' ? route('terms.conditions.en') : route('terms.conditions.id');
                    @endphp
                    <li><a href="{{ $privacyRoute }}" class="text-gray-300 hover:text-white transition">{{ __('landing.footer.privacy_policy') }}</a></li>
                    <li><a href="{{ $termsRoute }}" class="text-gray-300 hover:text-white transition">{{ __('landing.footer.terms_conditions') }}</a></li>
                </ul>
                <h4 class="text-white font-semibold mb-4 mt-6">{{ __('landing.footer.language') }}</h4>
                <ul class="space-y-2 text-sm">
                    <li><a href="{{ route('locale.set', 'id') }}" class="hover:text-white transition {{ app()->getLocale() == 'id' ? 'font-semibold text-[color:var(--color-primary-light)]' : 'text-gray-300' }}">🇮🇩 Indonesia</a></li>
                    <li><a href="{{ route('locale.set', 'en') }}" class="hover:text-white transition {{ app()->getLocale() == 'en' ? 'font-semibold text-[color:var(--color-primary-light)]' : 'text-gray-300' }}">🇬🇧 English</a></li>
                </ul>
            </div>
        </div>
        
        <div class="border-t border-gray-800 pt-6 pb-0 text-center text-sm text-gray-400">
            <p class="mb-1">{{ __('landing.footer.copyright', ['year' => date('Y')]) }}</p>
            <p class="text-gray-400 text-xs mb-0">{{ __('landing.footer.tagline') }}</p>
        </div>
    </div>
</footer>
