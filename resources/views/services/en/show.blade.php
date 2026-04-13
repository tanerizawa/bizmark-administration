@extends('landing.layout')

@section('title', ($service['title'] ?? 'Service') . ' - Bizmark.ID')
@section('meta_description', $service['short_description'] ?? '')
@section('meta_keywords', $service['meta_keywords'] ?? 'Indonesia investment, foreign investment, PMA services')

@section('structured_data')
{{-- BreadcrumbList --}}
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@type": "BreadcrumbList",
    "itemListElement": [
        {"@@type": "ListItem", "position": 1, "name": "Home", "item": "{{ route('landing.en') }}"},
        {"@@type": "ListItem", "position": 2, "name": "Services", "item": "{{ route('services.index.en') }}"},
        {"@@type": "ListItem", "position": 3, "name": "{{ $service['title'] }}"}
    ]
}
</script>
{{-- Service Schema --}}
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@type": "Service",
    "name": "{{ $service['title'] }}",
    "description": "{{ $service['short_description'] }}",
    "provider": {
        "@@type": "Organization",
        "name": "PT Cangah Pajaratan Mandiri (Bizmark.ID)",
        "url": "https://bizmark.id"
    },
    "areaServed": {"@@type": "Country", "name": "Indonesia"},
    @if(isset($service['pricing']))
    "offers": {
        "@@type": "Offer",
        "priceCurrency": "{{ $service['pricing']['currency'] ?? 'USD' }}",
        "price": "{{ $service['pricing']['min'] ?? 0 }}",
        "priceSpecification": {
            "@@type": "PriceSpecification",
            "minPrice": "{{ $service['pricing']['min'] ?? 0 }}",
            "maxPrice": "{{ $service['pricing']['max'] ?? 0 }}",
            "priceCurrency": "{{ $service['pricing']['currency'] ?? 'USD' }}"
        }
    },
    @endif
    "serviceType": "{{ $service['category'] ?? 'Professional Service' }}"
}
</script>
{{-- FAQ Schema --}}
@if(!empty($service['faq']))
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@type": "FAQPage",
    "mainEntity": [
        @foreach($service['faq'] as $faqItem)
        {
            "@@type": "Question",
            "name": "{{ $faqItem['q'] }}",
            "acceptedAnswer": {
                "@@type": "Answer",
                "text": "{{ $faqItem['a'] }}"
            }
        }@if(!$loop->last),@endif
        @endforeach
    ]
}
</script>
@endif
@endsection

@section('content')

@php
    $contact = (array) data_get(config('landing_metrics'), 'contact', []);
    $whatsappBase = $contact['whatsapp_link'] ?? '';
    $supportEmail = $contact['email'] ?? '';
    $phoneRaw = $contact['phone'] ?? '';
    $waHeroText = "Hello, I'm interested in " . ($service['title'] ?? '');
    $waHeroHref = $whatsappBase . (str_contains($whatsappBase, '?') ? '&' : '?') . 'text=' . rawurlencode($waHeroText);
    $mailSubject = ($service['title'] ?? 'Service') . ' Inquiry';
    $mailHref = 'mailto:' . $supportEmail . '?subject=' . rawurlencode($mailSubject);
    $narrative = $service['narrative'] ?? null;
@endphp

<!-- Breadcrumb -->
<section class="bg-gray-50 py-6 mt-20">
    <div class="container">
        <nav class="flex flex-wrap items-center text-sm text-gray-600" aria-label="Breadcrumb">
            <a href="{{ route('landing.en') }}" class="hover:text-primary transition">
                <i class="fas fa-home mr-1"></i>Home
            </a>
            <i class="fas fa-chevron-right mx-2 text-gray-400 text-xs"></i>
            <a href="{{ route('services.index.en') }}" class="hover:text-primary transition">Services</a>
            <i class="fas fa-chevron-right mx-2 text-gray-400 text-xs"></i>
            <span class="text-gray-900 font-medium">{{ $service['title'] }}</span>
        </nav>
    </div>
</section>

<!-- Hero Section -->
<section class="section bg-gradient-to-br from-white via-gray-50 to-white pt-12 pb-16">
    <div class="container">
        <div class="max-w-5xl mx-auto">
            <div class="flex flex-col md:flex-row items-start gap-8" data-aos="fade-up">
                <!-- Icon -->
                <div class="w-20 h-20 md:w-24 md:h-24 rounded-3xl flex items-center justify-center flex-shrink-0 shadow-lg" style="background: linear-gradient(135deg, {{ $service['color'] }}20 0%, {{ $service['color'] }}40 100%);">
                    <i class="fas {{ $service['icon'] }} text-4xl md:text-5xl" style="color: {{ $service['color'] }};"></i>
                </div>
                
                <!-- Content -->
                <div class="flex-1">
                    <span class="inline-flex items-center px-4 py-1.5 rounded-full text-xs font-semibold mb-4" style="background: {{ $service['color'] }}15; color: {{ $service['color'] }};">
                        <i class="fas fa-briefcase mr-1.5"></i>Professional Service
                    </span>
                    <h1 class="text-3xl md:text-4xl lg:text-5xl font-bold mb-4 text-gray-900">
                        {{ $service['title'] }}
                    </h1>
                    <p class="text-lg md:text-xl text-gray-600 leading-relaxed mb-6">
                        {{ $service['short_description'] }}
                    </p>
                    
                    <!-- Pricing & Duration -->
                    @if(isset($service['pricing']))
                    <div class="flex flex-wrap items-center gap-6 mb-6 p-5 bg-white rounded-xl border border-gray-200 shadow-sm">
                        <div>
                            <div class="text-xs text-gray-500 mb-1">Starting From</div>
                            <div class="text-2xl font-bold" style="color: {{ $service['color'] }};">{{ $service['pricing']['display'] }}</div>
                        </div>
                        <div class="h-10 w-px bg-gray-200"></div>
                        <div>
                            <div class="text-xs text-gray-500 mb-1">Processing Time</div>
                            <div class="text-lg font-semibold text-gray-700">{{ $service['duration'] }}</div>
                        </div>
                        <div class="h-10 w-px bg-gray-200"></div>
                        <div>
                            <div class="text-xs text-gray-500 mb-1">Consultation</div>
                            <div class="text-lg font-semibold text-green-600">Free</div>
                        </div>
                    </div>
                    @endif
                    
                    <!-- Quick Actions -->
                    <div class="flex flex-wrap gap-3">
                        <a href="{{ $waHeroHref }}" target="_blank" rel="noopener" class="btn btn-primary">
                            <i class="fab fa-whatsapp mr-2"></i>WhatsApp Consultation
                        </a>
                        <a href="{{ $mailHref }}" class="btn bg-white border-2 border-gray-300 text-gray-700 hover:border-primary hover:text-primary">
                            <i class="fas fa-envelope mr-2"></i>Email Us
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Service Details -->
<section class="section bg-white">
    <div class="container">
        <div class="max-w-5xl mx-auto">
            <div class="grid lg:grid-cols-3 gap-8">
                <!-- Main Content -->
                <div class="lg:col-span-2 space-y-10">
                    
                    <!-- Narrative Introduction -->
                    @if($narrative)
                    <div class="bg-gradient-to-br from-blue-50 to-indigo-50 border-l-4 border-blue-600 rounded-r-xl p-8 shadow-sm" data-aos="fade-up">
                        <div class="flex items-start gap-4 mb-6">
                            <div class="w-12 h-12 bg-blue-600 rounded-lg flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-lightbulb text-white text-xl"></i>
                            </div>
                            <div>
                                <h2 class="text-2xl font-bold text-gray-900 mb-2">Understanding {{ $service['title'] }}</h2>
                                <p class="text-sm text-blue-900 font-medium">Essential insights for foreign investors in Indonesia</p>
                            </div>
                        </div>
                        
                        <div class="prose max-w-none text-gray-700 leading-relaxed space-y-4">
                            <p class="text-lg font-medium text-gray-900">{{ $narrative['intro'] }}</p>
                            
                            @foreach(explode('|', $narrative['body']) as $paragraph)
                            <p>{{ trim($paragraph) }}</p>
                            @endforeach
                            
                            <p><strong>Why this matters for your business:</strong> {{ $narrative['highlight'] }}</p>
                            
                            <p class="bg-white p-4 rounded-lg border-l-4 border-blue-600 italic">{{ $narrative['quote'] }}</p>
                        </div>
                    </div>
                    @endif
                    
                    <!-- Deliverables -->
                    @if(!empty($service['deliverables']))
                    <div data-aos="fade-up">
                        <h2 class="text-2xl font-bold mb-6 text-gray-900 flex items-center">
                            <i class="fas fa-box-open mr-3" style="color: {{ $service['color'] }};"></i>
                            What You'll Receive
                        </h2>
                        <div class="grid sm:grid-cols-2 gap-3">
                            @foreach($service['deliverables'] as $deliverable)
                            <div class="flex items-start gap-3 p-4 bg-gradient-to-r from-green-50 to-white rounded-lg border border-green-100 hover:shadow-md transition">
                                <i class="fas fa-check-double text-green-600 mt-1 flex-shrink-0"></i>
                                <span class="text-gray-700 text-sm leading-relaxed">{{ $deliverable }}</span>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif
                    
                    <!-- Process Steps -->
                    @if(!empty($service['process_steps']))
                    <div data-aos="fade-up">
                        <h2 class="text-2xl font-bold mb-6 text-gray-900 flex items-center">
                            <i class="fas fa-route mr-3" style="color: {{ $service['color'] }};"></i>
                            Our Process
                        </h2>
                        <div class="space-y-4">
                            @foreach($service['process_steps'] as $index => $step)
                            <div class="flex gap-4 p-5 bg-gradient-to-r from-blue-50 to-white rounded-lg border border-blue-100 hover:shadow-md transition">
                                <div class="flex-shrink-0 w-12 h-12 rounded-full bg-gradient-to-br from-blue-900 to-blue-700 text-white flex items-center justify-center font-bold text-lg shadow-lg">
                                    {{ $index + 1 }}
                                </div>
                                <div class="flex-1 pt-2">
                                    <h3 class="font-semibold text-gray-900">{{ $step }}</h3>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif
                    
                    <!-- Requirements -->
                    @if(!empty($service['requirements']))
                    <div data-aos="fade-up">
                        <h2 class="text-2xl font-bold mb-6 text-gray-900 flex items-center">
                            <i class="fas fa-file-alt mr-3 text-yellow-600"></i>
                            Required Documents
                        </h2>
                        <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-6">
                            <ul class="space-y-3">
                                @foreach($service['requirements'] as $requirement)
                                <li class="flex items-start gap-3">
                                    <i class="fas fa-file-alt text-yellow-600 mt-1"></i>
                                    <span class="text-gray-700">{{ $requirement }}</span>
                                </li>
                                @endforeach
                            </ul>
                            <div class="mt-4 p-3 bg-white rounded-lg text-sm text-gray-600">
                                <i class="fas fa-info-circle mr-2 text-blue-600"></i>
                                Detailed requirements will be discussed during your free initial consultation.
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- FAQ Section -->
                    @if(!empty($service['faq']))
                    <div data-aos="fade-up">
                        <h2 class="text-2xl font-bold mb-6 text-gray-900 flex items-center">
                            <i class="fas fa-question-circle mr-3" style="color: {{ $service['color'] }};"></i>
                            Frequently Asked Questions
                        </h2>
                        <div class="space-y-3">
                            @foreach($service['faq'] as $index => $faq)
                            <div class="faq-item group border border-gray-200 rounded-xl overflow-hidden {{ $index === 0 ? 'faq-open' : '' }}">
                                <button class="faq-toggle flex items-center justify-between w-full p-5 text-left hover:bg-gray-50 transition" onclick="this.parentElement.classList.toggle('faq-open')">
                                    <span class="font-semibold text-gray-900 pr-4">{{ $faq['q'] }}</span>
                                    <i class="fas fa-chevron-down text-gray-400 faq-chevron transition-transform flex-shrink-0"></i>
                                </button>
                                <div class="faq-answer px-5 pb-5 text-gray-600 leading-relaxed">
                                    {{ $faq['a'] }}
                                </div>
                            </div>
                            @endforeach
                        </div>
                        <style>
                            .faq-item .faq-answer { display: none; }
                            .faq-item.faq-open .faq-answer { display: block; }
                            .faq-item.faq-open .faq-chevron { transform: rotate(180deg); }
                        </style>
                    </div>
                    @endif

                </div>
                
                <!-- Sidebar -->
                <div class="lg:col-span-1">
                    <div class="sticky top-24 space-y-6">
                        
                        <!-- Contact Card -->
                        <div class="card bg-gradient-to-br from-primary to-primary-dark text-white">
                            <h3 class="text-xl font-bold mb-4">Need Assistance?</h3>
                            <p class="text-white/90 text-sm mb-6">Our investment consultants are ready to help you navigate Indonesia's regulatory landscape.</p>
                            <div class="space-y-3">
                                <a href="{{ $waHeroHref }}" target="_blank" rel="noopener" class="block w-full px-4 py-3 bg-green-600 text-white rounded-lg text-center font-semibold hover:bg-green-700 transition">
                                    <i class="fab fa-whatsapp mr-2"></i>Chat on WhatsApp
                                </a>
                                <a href="{{ $mailHref }}" class="block w-full px-4 py-3 bg-white text-primary rounded-lg text-center font-semibold hover:bg-gray-50 transition">
                                    <i class="fas fa-envelope mr-2"></i>Send Email
                                </a>
                            </div>
                        </div>
                        
                        <!-- Quick Info -->
                        <div class="card border-2" style="border-color: {{ $service['color'] }}22;">
                            <h3 class="font-bold text-gray-900 mb-4">Service Information</h3>
                            <div class="space-y-3 text-sm">
                                @if(isset($service['pricing']))
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Investment:</span>
                                    <span class="font-semibold text-gray-900">{{ $service['pricing']['display'] }}</span>
                                </div>
                                @endif
                                @if(isset($service['duration']))
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Timeline:</span>
                                    <span class="font-semibold text-gray-900">{{ $service['duration'] }}</span>
                                </div>
                                @endif
                                @if(!empty($service['deliverables']))
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Deliverables:</span>
                                    <span class="font-semibold text-gray-900">{{ count($service['deliverables']) }} items</span>
                                </div>
                                @endif
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Market:</span>
                                    <span class="font-semibold text-gray-900">Foreign Investment</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Consultation:</span>
                                    <span class="font-semibold text-green-600">Free</span>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Related Services -->
                        @if(!empty($relatedServices))
                        <div class="card">
                            <h3 class="font-bold text-gray-900 mb-4">Related Services</h3>
                            <div class="space-y-3">
                                @foreach($relatedServices as $relSlug => $related)
                                <a href="{{ route('services.show.en', $relSlug) }}" class="flex items-center gap-3 p-3 rounded-lg hover:bg-gray-50 transition group">
                                    <div class="w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0" style="background: {{ $related['color'] ?? '#1E40AF' }}15;">
                                        <i class="fas {{ $related['icon'] }} text-sm" style="color: {{ $related['color'] ?? '#1E40AF' }};"></i>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="text-sm font-semibold text-gray-900 group-hover:text-primary transition truncate">{{ $related['title'] }}</div>
                                    </div>
                                    <i class="fas fa-chevron-right text-xs text-gray-400 group-hover:text-primary transition"></i>
                                </a>
                                @endforeach
                            </div>
                        </div>
                        @endif
                        
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Trust Strip -->
<section class="py-10 bg-gradient-to-r from-gray-50 to-white border-y border-gray-100">
    <div class="container">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6 max-w-4xl mx-auto text-center" data-aos="fade-up">
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

<!-- Testimonial -->
@if(!empty($testimonial))
<section class="section bg-white">
    <div class="container">
        <div class="max-w-3xl mx-auto" data-aos="fade-up">
            <h2 class="text-2xl md:text-3xl font-bold text-center text-gray-900 mb-8">What Our Clients Say</h2>
            <div class="bg-gradient-to-br from-gray-50 to-white rounded-2xl p-8 border border-gray-100 shadow-sm">
                <div class="flex items-center gap-1 mb-4">
                    @for($i = 0; $i < ($testimonial['rating'] ?? 5); $i++)
                    <i class="fas fa-star text-yellow-400"></i>
                    @endfor
                </div>
                <blockquote class="text-lg text-gray-700 leading-relaxed italic mb-6">
                    "{{ $testimonial['text'] }}"
                </blockquote>
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-full flex items-center justify-center text-white font-bold text-lg" style="background: {{ $service['color'] }};">
                        {{ substr($testimonial['name'], 0, 1) }}
                    </div>
                    <div>
                        <div class="font-semibold text-gray-900">{{ $testimonial['name'] }}</div>
                        <div class="text-sm text-gray-500">{{ $testimonial['position'] }} — {{ $testimonial['company'] }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endif

<!-- CTA Section -->
<section class="section bg-gradient-to-br from-gray-900 to-gray-800 text-white">
    <div class="container">
        <div class="max-w-4xl mx-auto text-center" data-aos="fade-up">
            <h2 class="text-3xl md:text-4xl font-bold mb-6">
                Ready to Start Your Investment Journey?
            </h2>
            <p class="text-lg md:text-xl mb-8 text-white/80">
                Get a free consultation and customized roadmap for your business in Indonesia
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                @php
                    $waCtaText = 'Hello, I need consultation for ' . ($service['title'] ?? '');
                    $waCtaHref = $whatsappBase . (str_contains($whatsappBase, '?') ? '&' : '?') . 'text=' . rawurlencode($waCtaText);
                @endphp
                <a href="{{ $waCtaHref }}" target="_blank" rel="noopener" class="btn bg-secondary hover:bg-secondary-dark px-8 py-4 text-lg">
                    <i class="fab fa-whatsapp mr-2"></i>Free Consultation
                </a>
                <a href="{{ route('services.index.en') }}" class="btn bg-white/10 hover:bg-white/20 text-white border border-white/30 px-8 py-4 text-lg">
                    <i class="fas fa-th-large mr-2"></i>View All Services
                </a>
            </div>
        </div>
    </div>
</section>

@endsection
