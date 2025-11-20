@php
    $services = collect(config('services_data'));
    $metrics = config('landing_metrics');
    
    // Get featured service (OSS)
    $featured = $services->where('featured', true)->first();
    
    // Get other services (non-featured, limit 4 for grid)
    $others = $services->where('featured', '!=', true)->take(4);
@endphp

<!-- FEATURED ARTICLES: Services as Magazine Stories -->
<section id="services" class="magazine-section bg-white fade-in-up">
    <!-- Section Header -->
    <div class="mb-10">
        <h2 class="headline text-4xl text-gray-900 mb-3">
            Layanan <span class="text-gradient">Unggulan</span> Kami
        </h2>
        <p class="text-gray-600 text-sm max-w-xl leading-relaxed">
            Eksplorasi berbagai layanan perizinan yang kami tawarkan dengan 
            jaminan proses cepat, transparan, dan 100% legal.
        </p>
        
        <!-- Quick Stats Bar -->
        <div class="mt-6 flex flex-wrap items-center gap-3">
            <div class="flex items-center gap-2 bg-blue-50 px-4 py-2 rounded-full">
                <i class="fas fa-users text-blue-600"></i>
                <span class="text-sm font-semibold text-blue-900">{{ $metrics['display']['clients_total'] }} Klien</span>
            </div>
            <div class="flex items-center gap-2 bg-green-50 px-4 py-2 rounded-full">
                <i class="fas fa-check-circle text-green-600"></i>
                <span class="text-sm font-semibold text-green-900">{{ $metrics['display']['permits_processed'] }} Izin Selesai</span>
            </div>
            <div class="flex items-center gap-2 bg-orange-50 px-4 py-2 rounded-full">
                <i class="fas fa-clock text-orange-600"></i>
                <span class="text-sm font-semibold text-orange-900">{{ $metrics['display']['process_time'] }}</span>
            </div>
        </div>
    </div>
    
    <!-- Magazine Grid Layout -->
    <div class="space-y-6">
        
        @if($featured)
        <!-- Hero Article (Featured Service) -->
        <article class="magazine-card">
            <div class="relative h-48 overflow-hidden" style="background: linear-gradient(135deg, {{ $featured['color'] }} 0%, {{ $featured['color'] }}dd 100%);">
                <div class="absolute inset-0 flex items-center justify-center">
                    <i class="fas {{ $featured['icon'] }} text-white text-6xl opacity-20"></i>
                </div>
                <div class="absolute top-4 left-4">
                    <span class="category-tag bg-white/30 backdrop-blur-sm text-white px-3 py-1.5 rounded-full shadow-lg">
                        {{ $featured['badge'] ?? 'Paling Populer' }}
                    </span>
                </div>
            </div>
            <div class="p-6">
                <div class="category-tag mb-2" style="color: {{ $featured['color'] }};">
                    PERIZINAN USAHA
                </div>
                <h3 class="headline text-2xl text-gray-900 mb-3">
                    {{ $featured['title'] }}
                </h3>
                <p class="text-gray-600 text-sm mb-4 leading-relaxed">
                    {{ $featured['short_description'] }}
                </p>
                <div class="flex items-center justify-between">
                    <div>
                        @if(isset($featured['price']))
                        <span class="text-xs text-gray-500">Mulai dari</span>
                        <span class="text-2xl font-bold ml-1" style="color: {{ $featured['color'] }};">{{ $featured['price'] }}</span>
                        @endif
                    </div>
                    <div class="flex items-center gap-1 bg-red-50 px-3 py-1 rounded-full">
                        <i class="fas fa-fire text-red-500 text-xs"></i>
                        <span class="text-xs font-bold text-red-700">{{ $featured['badge'] ?? 'Terfavorit' }}</span>
                    </div>
                </div>
                <a href="{{ route('services.show', $featured['slug']) }}" 
                   class="block mt-4 text-center bg-gradient-to-r from-[#0077B5] to-[#005582] text-white font-semibold py-3 px-4 rounded-xl hover:shadow-lg transition-all">
                    <i class="fas fa-arrow-right mr-2"></i>Lihat Detail
                </a>
            </div>
        </article>
        @endif
        
        <!-- Grid of 2 Medium Articles -->
        <div class="grid grid-cols-2 gap-4">
            @foreach($others as $service)
            <!-- Article Card -->
            <article class="magazine-card">
                <div class="relative h-32 overflow-hidden" style="background: linear-gradient(135deg, {{ $service['color'] }} 0%, {{ $service['color'] }}dd 100%);">
                    <div class="absolute inset-0 flex items-center justify-center">
                        <i class="fas {{ $service['icon'] }} text-white text-4xl opacity-30"></i>
                    </div>
                </div>
                <div class="p-4">
                    @if(isset($service['category']))
                    <div class="category-tag mb-1" style="color: {{ $service['color'] }};">
                        {{ $service['category'] }}
                    </div>
                    @endif
                    <h4 class="text-base font-bold text-gray-900 mb-2">
                        {{ $service['title'] }}
                    </h4>
                    <p class="text-xs text-gray-600 mb-3">
                        {{ $service['short_description'] }}
                    </p>
                    <a href="{{ route('services.show', $service['slug']) }}" 
                       class="text-xs font-semibold hover:underline" 
                       style="color: {{ $service['color'] }};">
                        Baca Selengkapnya →
                    </a>
                </div>
            </article>
            @endforeach
        </div>
        
        <!-- Call-to-Action Banner -->
        <div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-[#0077B5] to-[#005582] p-8 text-white text-center">
            <div class="relative z-10">
                <i class="fas fa-headset text-5xl mb-4 opacity-90"></i>
                <h3 class="text-2xl font-bold mb-2">Butuh Layanan Lain?</h3>
                <p class="text-sm mb-6 opacity-90">Kami menyediakan {{ $metrics['permits']['types_available'] }}+ jenis layanan perizinan</p>
                <div class="flex gap-3 justify-center">
                    <a href="{{ route('services.index') }}" 
                       class="bg-white text-blue-600 font-semibold px-6 py-3 rounded-xl hover:shadow-lg transition-all">
                        <i class="fas fa-th-large mr-2"></i>Lihat Semua Layanan
                    </a>
                    <a href="https://wa.me/{{ $metrics['contact']['whatsapp'] }}?text=Halo%20BizMark%2C%20saya%20ingin%20konsultasi%20layanan" 
                       class="bg-green-500 text-white font-semibold px-6 py-3 rounded-xl hover:bg-green-600 transition-all">
                        <i class="fab fa-whatsapp mr-2"></i>Konsultasi
                    </a>
                </div>
            </div>
            
            <!-- Decorative Elements -->
            <div class="absolute -top-10 -right-10 w-40 h-40 bg-white/10 rounded-full blur-3xl"></div>
            <div class="absolute -bottom-10 -left-10 w-40 h-40 bg-white/10 rounded-full blur-3xl"></div>
        </div>
        
    </div>
</section>
                    <h4 class="text-base font-bold text-gray-900 mb-2">
                        PBG & SLF
                    </h4>
                    <p class="text-xs text-gray-600 mb-3">
                        Izin mendirikan bangunan & sertifikat laik fungsi
                    </p>
                </div>
            </article>
        </div>
        
        <!-- Full Width List Articles -->
        <article class="magazine-card bg-gradient-to-r from-orange-50 to-yellow-50">
            <div class="p-6">
                <div class="flex items-start gap-4 mb-4">
                    <div class="bg-orange-100 p-4 rounded-full flex-shrink-0">
                        <i class="fas fa-file-signature text-3xl text-orange-600"></i>
                    </div>
                    <div class="flex-1">
                        <div class="category-tag text-orange-600 mb-1">
                            LEGALITAS
                        </div>
                        <h4 class="text-lg font-bold text-gray-900 mb-1">
                            Pendirian PT, CV, Yayasan
                        </h4>
                        <p class="text-sm text-gray-600">
                            Layanan pendirian badan usaha lengkap dengan akta notaris
                        </p>
                    </div>
                </div>
            </div>
        </article>
        
        <article class="magazine-card bg-gradient-to-r from-red-50 to-pink-50">
            <div class="p-6">
                <div class="flex items-start gap-4 mb-4">
                    <div class="bg-red-100 p-4 rounded-full flex-shrink-0">
                        <i class="fas fa-shield-alt text-3xl text-red-600"></i>
                    </div>
                    <div class="flex-1">
                        <div class="category-tag text-red-600 mb-1">
                            PERIZINAN KHUSUS
                        </div>
                        <h4 class="text-lg font-bold text-gray-900 mb-1">
                            IUJK, SIUP, TDP & Lainnya
                        </h4>
                        <p class="text-sm text-gray-600">
                            Berbagai perizinan khusus sesuai bidang usaha Anda
                        </p>
                    </div>
                </div>
            </div>
        </article>
        
    </div>
    
</section>
