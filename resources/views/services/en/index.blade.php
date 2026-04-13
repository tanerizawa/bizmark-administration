@extends('landing.layout')

@section('title', 'Our Services - Investment & Compliance Solutions - Bizmark.ID')
@section('meta_description', 'Comprehensive investment and compliance services for foreign investors in Indonesia. BKPM approval, company establishment, work permits, and ongoing support.')
@section('meta_keywords', 'Indonesia investment services, PMA services, foreign investment Indonesia, BKPM approval, company establishment')

@section('structured_data')
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@type": "ItemList",
    "name": "Bizmark.ID Investment Services",
    "description": "Professional investment and compliance services for foreign investors in Indonesia",
    "numberOfItems": {{ count($services) }},
    "itemListElement": [
        @foreach($services as $slug => $svc)
        {
            "@type": "ListItem",
            "position": {{ $loop->iteration }},
            "item": {
                "@type": "Service",
                "name": "{{ $svc['title'] }}",
                "description": "{{ $svc['short_description'] }}",
                "url": "{{ route('services.show.en', $slug) }}",
                "provider": {"@type": "Organization", "name": "PT Cangah Pajaratan Mandiri (Bizmark.ID)"}
            }
        }@if(!$loop->last),@endif
        @endforeach
    ]
}
</script>
@endsection

@section('content')

@php
    $contact = (array) data_get(config('landing_metrics'), 'contact', []);
    $whatsappBase = $contact['whatsapp_link'] ?? '';
    $supportEmail = $contact['email'] ?? '';
    $waHeroText = 'Hello, I need information about your investment services';
    $waHeroHref = $whatsappBase . (str_contains($whatsappBase, '?') ? '&' : '?') . 'text=' . rawurlencode($waHeroText);
@endphp

<!-- Breadcrumb -->
<section class="bg-gray-50 py-6 mt-20">
    <div class="container">
        <nav class="flex items-center text-sm text-gray-600" aria-label="Breadcrumb">
            <a href="{{ route('landing.en') }}" class="hover:text-primary transition">
                <i class="fas fa-home mr-1"></i>Home
            </a>
            <i class="fas fa-chevron-right mx-2 text-gray-400 text-xs"></i>
            <span class="text-gray-900 font-medium">Services</span>
        </nav>
    </div>
</section>

<!-- Hero Section -->
<section class="section bg-gradient-to-br from-blue-900 to-blue-800 text-white">
    <div class="container">
        <div class="max-w-4xl mx-auto text-center" data-aos="fade-up">
            <div class="inline-block px-4 py-2 bg-white/20 backdrop-blur-sm rounded-full text-sm font-semibold mb-6">
                <i class="fas fa-briefcase mr-2"></i>Professional Services
            </div>
            <h1 class="text-4xl md:text-5xl font-bold mb-6">Investment & Compliance Services</h1>
            <p class="text-xl text-blue-100 mb-8">
                Comprehensive solutions for foreign investors establishing and operating businesses in Indonesia
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="#services-grid" class="btn bg-white text-blue-900 hover:bg-blue-50 px-8 py-3">
                    <i class="fas fa-arrow-down mr-2"></i>Explore Services
                </a>
                <a href="{{ $waHeroHref }}" target="_blank" rel="noopener" class="btn bg-green-600 hover:bg-green-700 text-white px-8 py-3">
                    <i class="fab fa-whatsapp mr-2"></i>Free Consultation
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Trust Stats -->
<section class="py-10 bg-gray-50 border-y border-gray-200">
    <div class="container">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center" data-aos="fade-up">
            <div>
                <div class="text-3xl font-black text-gray-900">500+</div>
                <div class="text-sm text-gray-600 mt-1">Successful Investments</div>
            </div>
            <div>
                <div class="text-3xl font-black text-gray-900">15+</div>
                <div class="text-sm text-gray-600 mt-1">Years Experience</div>
            </div>
            <div>
                <div class="text-3xl font-black text-gray-900">98%</div>
                <div class="text-sm text-gray-600 mt-1">Success Rate</div>
            </div>
            <div>
                <div class="text-3xl font-black text-gray-900">200+</div>
                <div class="text-sm text-gray-600 mt-1">Active Clients</div>
            </div>
        </div>
    </div>
</section>

<!-- Services Grid -->
<section id="services-grid" class="section bg-white">
    <div class="container">
        <div class="text-center mb-12" data-aos="fade-up">
            <h2 class="text-3xl md:text-4xl font-bold mb-4 text-gray-900">Our Service Portfolio</h2>
            <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                End-to-end support for your investment journey in Indonesia
            </p>
        </div>
        
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6" data-aos="fade-up">
            @foreach($services as $slug => $service)
            <a href="{{ route('services.show.en', $slug) }}" class="group card hover:shadow-xl hover:border-blue-300 transition-all duration-300 border border-gray-200">
                <div class="flex items-start justify-between mb-4">
                    <div class="w-14 h-14 rounded-xl flex items-center justify-center" style="background: {{ $service['color'] }}15;">
                        <i class="fas {{ $service['icon'] }} text-2xl" style="color: {{ $service['color'] }};"></i>
                    </div>
                    @if(isset($service['featured']) && $service['featured'])
                    <span class="px-3 py-1 bg-yellow-100 text-yellow-800 text-xs font-bold rounded-full">Popular</span>
                    @endif
                </div>
                
                <h3 class="text-lg font-bold mb-2 text-gray-900 group-hover:text-primary transition">
                    {{ $service['title'] }}
                </h3>
                
                <p class="text-gray-600 mb-4 leading-relaxed text-sm">
                    {{ Str::limit($service['short_description'], 120) }}
                </p>
                
                @if(isset($service['pricing']))
                <div class="flex items-center justify-between mb-4 pb-4 border-t border-gray-100 pt-4">
                    <div>
                        <div class="text-xs text-gray-500">Starting From</div>
                        <div class="text-lg font-bold" style="color: {{ $service['color'] }};">{{ $service['pricing']['display'] }}</div>
                    </div>
                    <div class="text-right">
                        <div class="text-xs text-gray-500">Timeline</div>
                        <div class="text-sm font-semibold text-gray-700">{{ $service['duration'] }}</div>
                    </div>
                </div>
                @endif
                
                <span class="inline-flex items-center text-primary font-semibold text-sm group-hover:translate-x-1 transition-transform">
                    Learn More <i class="fas fa-arrow-right ml-2 text-xs"></i>
                </span>
            </a>
            @endforeach
        </div>
    </div>
</section>

<!-- Why Choose Us -->
<section class="section bg-gradient-to-br from-gray-50 to-blue-50">
    <div class="container">
        <div class="text-center mb-12" data-aos="fade-up">
            <h2 class="text-3xl md:text-4xl font-bold mb-4 text-gray-900">Why Choose Bizmark.ID</h2>
            <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                Your trusted partner for navigating Indonesia's business landscape
            </p>
        </div>
        
        <div class="grid md:grid-cols-3 gap-8 max-w-5xl mx-auto" data-aos="fade-up">
            <div class="text-center p-6">
                <div class="w-16 h-16 rounded-full bg-primary text-white flex items-center justify-center mx-auto mb-4 text-2xl">
                    <i class="fas fa-shield-alt"></i>
                </div>
                <h3 class="text-xl font-bold mb-3 text-gray-900">Regulatory Expertise</h3>
                <p class="text-gray-600">15+ years navigating Indonesian investment regulations and BKPM requirements</p>
            </div>
            <div class="text-center p-6">
                <div class="w-16 h-16 rounded-full bg-primary text-white flex items-center justify-center mx-auto mb-4 text-2xl">
                    <i class="fas fa-users"></i>
                </div>
                <h3 class="text-xl font-bold mb-3 text-gray-900">Dedicated Support</h3>
                <p class="text-gray-600">English-speaking consultants providing personalized guidance throughout your journey</p>
            </div>
            <div class="text-center p-6">
                <div class="w-16 h-16 rounded-full bg-primary text-white flex items-center justify-center mx-auto mb-4 text-2xl">
                    <i class="fas fa-chart-line"></i>
                </div>
                <h3 class="text-xl font-bold mb-3 text-gray-900">Proven Track Record</h3>
                <p class="text-gray-600">500+ successful investments with 98% approval rate across all sectors</p>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="section bg-gradient-to-br from-gray-900 to-gray-800 text-white">
    <div class="container">
        <div class="max-w-4xl mx-auto text-center" data-aos="fade-up">
            <h2 class="text-3xl md:text-4xl font-bold mb-6">Ready to Invest in Indonesia?</h2>
            <p class="text-lg md:text-xl mb-8 text-white/80">
                Schedule a free consultation to discuss your investment plans and get a customized roadmap
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ $whatsappBase }}" target="_blank" rel="noopener" class="btn bg-secondary hover:bg-secondary-dark px-8 py-4 text-lg">
                    <i class="fab fa-whatsapp mr-2"></i>WhatsApp Us
                </a>
                <a href="mailto:{{ $supportEmail }}" class="btn bg-white/10 hover:bg-white/20 text-white border border-white/30 px-8 py-4 text-lg">
                    <i class="fas fa-envelope mr-2"></i>Email Inquiry
                </a>
            </div>
        </div>
    </div>
</section>

@endsection
