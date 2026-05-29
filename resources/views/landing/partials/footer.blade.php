@php
    $landingMetrics = config('landing_metrics');
    $contact = $landingMetrics['contact'] ?? [];
    $phoneNumber = $contact['phone'] ?? '+62 838 7960 2855';
    $whatsappNumber = $contact['whatsapp'] ?? '6283879602855';
    $whatsappLink = $contact['whatsapp_link'] ?? ('https://wa.me/' . $whatsappNumber);
    $emailAddress = $contact['email'] ?? 'info@bizmark.id';
    $footerCities = config('programmatic_seo.cities', []);
    $featuredCities = collect($footerCities)->take(20);
    $locale = app()->getLocale();
    $landingRoute = $locale === 'en' ? route('landing.en') : route('landing.id');
    $servicesRoute = $locale === 'en' ? route('services.index.en') : route('services.index.id');
    $processRoute = $locale === 'en' ? route('process.en') : route('process.id');
    $aboutRoute = $locale === 'en' ? route('about.en') : route('about.id');
    $blogRoute = $locale === 'en' ? route('blog.index.en') : route('blog.index.id');
    $clientsActive = $landingMetrics['stats']['clients_active_label'] ?? '138+';
    $privacyRoute = $locale === 'en' ? route('privacy.policy.en') : route('privacy.policy.id');
    $termsRoute = $locale === 'en' ? route('terms.conditions.en') : route('terms.conditions.id');
@endphp

<footer class="site-footer mt-auto">
    <div class="container-wide py-14">
        <div class="grid gap-8 grid-cols-2 md:grid-cols-3 lg:grid-cols-12">
            <div class="col-span-2 md:col-span-3 lg:col-span-3">
                <a href="{{ $landingRoute }}" class="brand-mark mb-4 inline-flex items-center gap-2" aria-label="Bizmark.ID">
                    <img src="{{ asset('images/logo-mark.svg') }}" alt="" class="h-8 w-auto" loading="lazy">
                    <span class="font-bold text-xl tracking-tight">Bizmark.ID</span>
                </a>
                <p class="footer-note mb-6">{{ __('landing.footer.tagline') }}</p>
                <div class="flex gap-3">
                    <a href="https://www.linkedin.com/company/bizmark-id" target="_blank" rel="noopener noreferrer" class="footer-social" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                    <a href="https://www.facebook.com/bizmark.id" target="_blank" rel="noopener noreferrer" class="footer-social" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                    <a href="https://www.instagram.com/bizmark.id" target="_blank" rel="noopener noreferrer" class="footer-social" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                </div>
            </div>

            <div class="lg:col-span-2">
                <h4 class="footer-title">{{ __('landing.footer.navigation') }}</h4>
                <ul class="footer-list">
                    <li><a href="{{ $landingRoute }}">{{ __('landing.nav.home') }}</a></li>
                    <li><a href="{{ $servicesRoute }}">{{ __('landing.nav.services') }}</a></li>
                    <li><a href="{{ $processRoute }}">{{ __('landing.nav.process') }}</a></li>
                    <li><a href="{{ $locale === 'en' ? route('pricing.en') : route('pricing.id') }}">{{ $locale === 'en' ? 'Pricing' : 'Harga' }}</a></li>
                    <li><a href="{{ $aboutRoute }}">{{ __('landing.nav.about') }}</a></li>
                    <li><a href="{{ $blogRoute }}">{{ __('landing.nav.blog') }}</a></li>
                    <li><a href="{{ $locale === 'en' ? route('status.en') : route('status.id') }}">{{ $locale === 'en' ? 'System Status' : 'Status Sistem' }}</a></li>
                </ul>
            </div>

            <div class="lg:col-span-2">
                <h4 class="footer-title">Platform</h4>
                <ul class="footer-list">
                    <li><a href="{{ route('landing.service-inquiry.create') }}">{{ $locale === 'en' ? 'AI Permit Checker' : 'Cek Perizinan AI' }}</a></li>
                    <li><a href="{{ route('permohonan.index') }}">Permohonan</a></li>
                    <li><a href="{{ route('login') }}">{{ $locale === 'en' ? 'Client Portal' : 'Portal Klien' }}</a></li>
                </ul>
            </div>

            <div class="lg:col-span-3 md:col-span-2">
                <h4 class="footer-title">{{ __('landing.footer.contact_us') }}</h4>
                <ul class="footer-list">
                    <li><a href="mailto:{{ $emailAddress }}"><i class="fas fa-envelope mr-2"></i>{{ $emailAddress }}</a></li>
                    <li><a href="tel:{{ str_replace(' ', '', $phoneNumber) }}"><i class="fas fa-phone mr-2"></i>{{ $phoneNumber }}</a></li>
                    <li><a href="{{ $whatsappLink }}" target="_blank" rel="noopener noreferrer"><i class="fab fa-whatsapp mr-2"></i>{{ __('landing.footer.whatsapp') }}</a></li>
                    <li><span><i class="fas fa-map-marker-alt mr-2"></i>{{ __('landing.footer.location') }}</span></li>
                </ul>
            </div>

            <div class="lg:col-span-2">
                <h4 class="footer-title">{{ __('landing.footer.legal') }}</h4>
                <ul class="footer-list mb-6">
                    <li><a href="{{ $privacyRoute }}">{{ __('landing.footer.privacy_policy') }}</a></li>
                    <li><a href="{{ $termsRoute }}">{{ __('landing.footer.terms_conditions') }}</a></li>
                </ul>
                <h4 class="footer-title">{{ __('landing.footer.language') }}</h4>
                <ul class="footer-list">
                    <li><a href="{{ route('locale.set', 'id') }}" class="{{ $locale === 'id' ? 'active' : '' }}">🇮🇩 Indonesia</a></li>
                    <li><a href="{{ route('locale.set', 'en') }}" class="{{ $locale === 'en' ? 'active' : '' }}">🇬🇧 English</a></li>
                </ul>
            </div>
        </div>

        @if($featuredCities->count() > 0)
            <div class="mt-10 pt-8 border-t border-gray-200">
                <div class="footer-title mb-3">{{ $locale === 'en' ? 'Service Coverage' : 'Jangkauan Layanan' }}</div>
                <div class="footer-city-wrap">
                    @foreach($featuredCities as $city)
                        <a href="{{ url('/layanan/kota/' . $city['slug']) }}" class="footer-city-link">{{ $city['name'] }}</a>
                    @endforeach
                </div>
            </div>
        @endif

        <div class="mt-10 pt-8 border-t border-gray-200">
            @php
                $hasIso = !empty(config('landing_metrics.certifications.iso'));
                $partners = [
                    ['label' => 'OSS-RBA',    'sub' => $locale === 'en' ? 'Online Single Submission' : 'Online Single Submission'],
                    ['label' => 'BKPM',       'sub' => $locale === 'en' ? 'Investment Coordinating Board' : 'Badan Koordinasi Penanaman Modal'],
                    ['label' => 'KBLI 2020',  'sub' => $locale === 'en' ? 'Business Classification' : 'Klasifikasi Baku Lapangan Usaha'],
                    ['label' => 'KLHK / DLH', 'sub' => $locale === 'en' ? 'Environmental Authority' : 'Kementerian/Dinas Lingkungan Hidup'],
                    ['label' => 'Kemenkumham','sub' => $locale === 'en' ? 'Law & Human Rights' : 'Hukum & Hak Asasi Manusia'],
                    ['label' => 'DJP',        'sub' => $locale === 'en' ? 'Tax Directorate' : 'Direktorat Jenderal Pajak'],
                ];
            @endphp

            {{-- Partner / Integration Strip — platform context --}}
            <div class="mb-8">
                <div class="text-center mb-4">
                    <span class="eyebrow" style="color: var(--text-muted);">{{ $locale === 'en' ? 'Connected to official systems' : 'Terhubung dengan sistem resmi' }}</span>
                </div>
                <div class="flex flex-wrap items-center justify-center gap-2 max-w-4xl mx-auto">
                    @foreach($partners as $p)
                        <span class="partner-chip" title="{{ $p['sub'] }}">
                            <i class="fas fa-link text-[10px]" aria-hidden="true"></i>
                            <span>{{ $p['label'] }}</span>
                        </span>
                    @endforeach
                </div>
            </div>

            <div class="flex flex-wrap items-center justify-center gap-3 mb-6">
                <span class="trust-badge"><i class="fas fa-shield-halved" style="color:var(--accent);"></i> {{ $locale === 'en' ? 'Data Protected' : 'Data Terlindungi' }}</span>
                <span class="trust-badge"><i class="fas fa-map-marked-alt" style="color:var(--accent);"></i> {{ $locale === 'en' ? 'Nationwide Coverage' : 'Cakupan Se-Indonesia' }}</span>
                <span class="trust-badge"><i class="fas fa-globe" style="color:var(--accent);"></i> Bilingual ID / EN</span>
                <span class="trust-badge"><i class="fas fa-award" style="color:var(--accent);"></i> {{ config('landing_metrics.stats.experience_label', '12+ Tahun') }} {{ $locale === 'en' ? 'experience' : 'pengalaman' }}</span>
            </div>
            <div class="text-sm text-center text-gray-500">
                <p class="mb-1">{{ __('landing.footer.copyright', ['year' => date('Y')]) }}</p>
                <p class="mb-0">{{ __('landing.footer.tagline') }}</p>
            </div>
        </div>
    </div>
</footer>
