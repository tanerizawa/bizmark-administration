<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Our Services - Investment & Compliance Solutions - Bizmark.ID</title>
    <meta name="description" content="Comprehensive investment and compliance services for foreign investors in Indonesia. BKPM approval, company establishment, work permits, and ongoing support.">
    <meta name="keywords" content="Indonesia investment services, PMA services, foreign investment Indonesia, BKPM approval, company establishment">
    
    <link rel="canonical" href="https://bizmark.id/en/services">
    <link rel="alternate" hreflang="id" href="https://bizmark.id/id/services">
    <link rel="alternate" hreflang="en" href="https://bizmark.id/en/services">
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="font-sans antialiased bg-white text-gray-900">

<!-- Navbar -->
<nav class="fixed top-0 left-0 right-0 z-50 bg-white border-b border-gray-200 shadow-sm">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
            <a href="{{ route('landing.en') }}" class="text-xl font-bold text-blue-900">
                <i class="fas fa-certificate text-blue-600 mr-2"></i>Bizmark.ID
            </a>
            <div class="flex items-center gap-6">
                <a href="{{ route('landing.en') }}#services" class="hidden md:inline text-sm text-gray-600 hover:text-blue-600 transition">Services</a>
                <a href="{{ route('landing.en') }}#about" class="hidden md:inline text-sm text-gray-600 hover:text-blue-600 transition">About</a>
                <x-locale-switcher />
                <a href="{{ route('landing.en') }}" class="px-4 py-2 bg-blue-900 text-white rounded-lg text-sm font-semibold hover:bg-blue-800 transition">
                    <i class="fas fa-home mr-2"></i>Home
                </a>
            </div>
        </div>
    </div>
</nav>

<!-- Hero Section -->
<section class="pt-24 pb-16 bg-gradient-to-br from-blue-900 to-blue-800 text-white">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="max-w-4xl mx-auto text-center">
            <div class="inline-block px-4 py-2 bg-white/20 backdrop-blur-sm rounded-full text-sm font-semibold mb-6">
                <i class="fas fa-briefcase mr-2"></i>Professional Services
            </div>
            <h1 class="text-4xl md:text-5xl font-bold mb-6">Investment & Compliance Services</h1>
            <p class="text-xl text-blue-100 mb-8">
                Comprehensive solutions for foreign investors establishing and operating businesses in Indonesia
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="#services-grid" class="px-8 py-3 bg-white text-blue-900 rounded-lg font-semibold hover:bg-blue-50 transition">
                    <i class="fas fa-arrow-down mr-2"></i>Explore Services
                </a>
                <a href="https://wa.me/6283879602855?text=Hello,%20I%20need%20information%20about%20your%20services" 
                   class="px-8 py-3 bg-green-600 text-white rounded-lg font-semibold hover:bg-green-700 transition">
                    <i class="fab fa-whatsapp mr-2"></i>Free Consultation
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Stats -->
<section class="py-12 bg-gray-50 border-y border-gray-200">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
            <div>
                <div class="text-4xl font-bold text-blue-900 mb-2">500+</div>
                <div class="text-sm text-gray-600">Successful Investments</div>
            </div>
            <div>
                <div class="text-4xl font-bold text-blue-900 mb-2">15+</div>
                <div class="text-sm text-gray-600">Years Experience</div>
            </div>
            <div>
                <div class="text-4xl font-bold text-blue-900 mb-2">98%</div>
                <div class="text-sm text-gray-600">Success Rate</div>
            </div>
            <div>
                <div class="text-4xl font-bold text-blue-900 mb-2">ISO</div>
                <div class="text-sm text-gray-600">Certified Partner</div>
            </div>
        </div>
    </div>
</section>

<!-- Services Grid -->
<section id="services-grid" class="py-20 bg-white">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-3xl md:text-4xl font-bold mb-4 text-gray-900">Our Service Portfolio</h2>
            <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                End-to-end support for your investment journey in Indonesia
            </p>
        </div>
        
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($services as $slug => $service)
            <div class="group bg-white rounded-2xl border border-gray-200 hover:border-blue-300 hover:shadow-xl transition-all duration-300 p-6">
                <div class="flex items-start justify-between mb-4">
                    <div class="w-16 h-16 rounded-xl flex items-center justify-center" style="background: {{ $service['color'] }}20;">
                        <i class="fas {{ $service['icon'] }} text-3xl" style="color: {{ $service['color'] }};"></i>
                    </div>
                    @if(isset($service['featured']) && $service['featured'])
                    <span class="px-3 py-1 bg-yellow-100 text-yellow-800 text-xs font-bold rounded-full">
                        Popular
                    </span>
                    @endif
                </div>
                
                <h3 class="text-xl font-bold mb-3 text-gray-900 group-hover:text-blue-900 transition">
                    {{ $service['title'] }}
                </h3>
                
                <p class="text-gray-600 mb-4 leading-relaxed text-sm">
                    {{ $service['short_description'] }}
                </p>
                
                @if(isset($service['pricing']))
                <div class="flex items-center justify-between mb-4 pb-4 border-b border-gray-100">
                    <div>
                        <div class="text-xs text-gray-500">Starting From</div>
                        <div class="text-lg font-bold text-blue-900">{{ $service['pricing']['display'] }}</div>
                    </div>
                    <div class="text-right">
                        <div class="text-xs text-gray-500">Timeline</div>
                        <div class="text-sm font-semibold text-gray-700">{{ $service['duration'] }}</div>
                    </div>
                </div>
                @endif
                
                <a href="{{ route('services.show.en', $slug) }}" 
                   class="inline-flex items-center text-blue-900 font-semibold hover:text-blue-700 transition group">
                    Learn More
                    <i class="fas fa-arrow-right ml-2 group-hover:translate-x-1 transition-transform"></i>
                </a>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- Why Choose Us -->
<section class="py-20 bg-gradient-to-br from-gray-50 to-blue-50">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-3xl md:text-4xl font-bold mb-4 text-gray-900">Why Choose Bizmark.ID</h2>
            <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                Your trusted partner for navigating Indonesia's business landscape
            </p>
        </div>
        
        <div class="grid md:grid-cols-3 gap-8 max-w-5xl mx-auto">
            <div class="text-center p-6">
                <div class="w-16 h-16 rounded-full bg-blue-900 text-white flex items-center justify-center mx-auto mb-4 text-2xl">
                    <i class="fas fa-shield-alt"></i>
                </div>
                <h3 class="text-xl font-bold mb-3 text-gray-900">Regulatory Expertise</h3>
                <p class="text-gray-600">15+ years navigating Indonesian investment regulations and BKPM requirements</p>
            </div>
            
            <div class="text-center p-6">
                <div class="w-16 h-16 rounded-full bg-blue-900 text-white flex items-center justify-center mx-auto mb-4 text-2xl">
                    <i class="fas fa-users"></i>
                </div>
                <h3 class="text-xl font-bold mb-3 text-gray-900">Dedicated Support</h3>
                <p class="text-gray-600">English-speaking consultants providing personalized guidance throughout your journey</p>
            </div>
            
            <div class="text-center p-6">
                <div class="w-16 h-16 rounded-full bg-blue-900 text-white flex items-center justify-center mx-auto mb-4 text-2xl">
                    <i class="fas fa-chart-line"></i>
                </div>
                <h3 class="text-xl font-bold mb-3 text-gray-900">Proven Track Record</h3>
                <p class="text-gray-600">500+ successful investments with 98% approval rate across all sectors</p>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="py-20 bg-gradient-to-br from-blue-900 to-blue-800 text-white">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-3xl md:text-4xl font-bold mb-4">Ready to Invest in Indonesia?</h2>
        <p class="text-xl text-blue-100 mb-8 max-w-2xl mx-auto">
            Schedule a free consultation to discuss your investment plans and get a customized roadmap
        </p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="https://wa.me/6283879602855" 
               class="px-8 py-4 bg-green-600 text-white rounded-lg font-bold hover:bg-green-700 transition">
                <i class="fab fa-whatsapp mr-2"></i>WhatsApp Us
            </a>
            <a href="mailto:cs@bizmark.id" 
               class="px-8 py-4 bg-white text-blue-900 rounded-lg font-bold hover:bg-blue-50 transition">
                <i class="fas fa-envelope mr-2"></i>Email Inquiry
            </a>
        </div>
    </div>
</section>

<!-- Footer -->
<footer class="bg-gray-900 text-gray-300 py-12">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid md:grid-cols-4 gap-8 mb-8">
            <div>
                <div class="text-xl font-bold text-white mb-4">
                    <i class="fas fa-certificate text-blue-400 mr-2"></i>Bizmark.ID
                </div>
                <p class="text-sm">Your trusted partner for investment and compliance services in Indonesia</p>
            </div>
            
            <div>
                <h4 class="text-white font-semibold mb-4">Services</h4>
                <ul class="space-y-2 text-sm">
                    <li><a href="{{ route('services.index.en') }}" class="hover:text-white transition">All Services</a></li>
                    <li><a href="{{ route('landing.en') }}#process" class="hover:text-white transition">Our Process</a></li>
                </ul>
            </div>
            
            <div>
                <h4 class="text-white font-semibold mb-4">Contact</h4>
                <ul class="space-y-2 text-sm">
                    <li><i class="fas fa-envelope mr-2"></i>cs@bizmark.id</li>
                    <li><i class="fas fa-phone mr-2"></i>+62 838 7960 2855</li>
                    <li><i class="fas fa-map-marker-alt mr-2"></i>Karawang, Jawa Barat</li>
                </ul>
            </div>
            
            <div>
                <h4 class="text-white font-semibold mb-4">Language</h4>
                <div class="flex gap-2">
                    <a href="{{ route('locale.set', 'en') }}" class="px-4 py-2 bg-white text-blue-900 rounded-lg text-sm font-semibold">
                        🇬🇧 English
                    </a>
                    <a href="{{ route('locale.set', 'id') }}" class="px-4 py-2 bg-gray-800 text-white rounded-lg text-sm hover:bg-gray-700 transition">
                        🇮🇩 Indonesia
                    </a>
                </div>
            </div>
        </div>
        
        <div class="border-t border-gray-800 pt-8 text-center text-sm">
            <p>&copy; {{ date('Y') }} Bizmark.ID. All rights reserved.</p>
        </div>
    </div>
</footer>

</body>
</html>
