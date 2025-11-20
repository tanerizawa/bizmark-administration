@extends('mobile-landing.layouts.content')

@section('title', 'Layanan Kami - Bizmark.ID')
@section('meta_description', 'Layanan lengkap perizinan industri dan konsultasi lingkungan untuk bisnis Anda')

@section('content')

<!-- Hero Section -->
<section class="magazine-section bg-gradient-to-br from-blue-50 via-white to-purple-50">
    <div class="content-container text-center">
        <span class="inline-block px-4 py-2 bg-primary/10 text-primary rounded-full text-sm font-semibold mb-4">
            <i class="fas fa-briefcase mr-2"></i>Layanan Profesional
        </span>
        <h1 class="headline text-4xl mb-4 text-gray-900">
            Layanan Perizinan & Konsultasi
        </h1>
        <p class="text-base text-gray-600 leading-relaxed mb-8">
            Solusi lengkap untuk semua kebutuhan perizinan industri Anda
        </p>
        
        <!-- Quick Stats -->
        <div class="grid grid-cols-2 gap-4 mt-8">
            <div class="text-center bg-white rounded-2xl p-4 shadow-sm">
                <div class="text-3xl font-bold text-primary mb-1">10+</div>
                <div class="text-xs text-gray-600">Tahun Pengalaman</div>
            </div>
            <div class="text-center bg-white rounded-2xl p-4 shadow-sm">
                <div class="text-3xl font-bold text-green-600 mb-1">500+</div>
                <div class="text-xs text-gray-600">Klien Puas</div>
            </div>
            <div class="text-center bg-white rounded-2xl p-4 shadow-sm">
                <div class="text-3xl font-bold text-yellow-600 mb-1">98%</div>
                <div class="text-xs text-gray-600">Tingkat Sukses</div>
            </div>
            <div class="text-center bg-white rounded-2xl p-4 shadow-sm">
                <div class="text-3xl font-bold text-purple-600 mb-1">8</div>
                <div class="text-xs text-gray-600">Layanan Unggulan</div>
            </div>
        </div>
    </div>
</section>

<!-- Services Grid Section -->
<section class="magazine-section bg-white">
    <div class="content-container">
        <div class="space-y-4">
            @foreach($services as $slug => $service)
            <a href="{{ route('services.show', $slug) }}" 
               class="block bg-white rounded-2xl p-6 shadow-md hover:shadow-xl transition-all border border-gray-100">
                <!-- Icon & Title -->
                <div class="flex items-start gap-4 mb-3">
                    <div class="w-14 h-14 rounded-xl bg-gradient-to-br from-[#0077B5] to-[#005582] flex items-center justify-center text-white text-xl flex-shrink-0">
                        <i class="{{ $service['icon'] }}"></i>
                    </div>
                    <div class="flex-1">
                        <h3 class="font-bold text-lg text-gray-900 mb-1">
                            {{ $service['title'] }}
                        </h3>
                        @if(isset($service['subtitle']))
                        <p class="text-xs text-gray-500">{{ $service['subtitle'] }}</p>
                        @endif
                    </div>
                </div>
                
                <!-- Description -->
                <p class="text-sm text-gray-600 leading-relaxed mb-4">
                    {{ $service['short_description'] }}
                </p>
                
                <!-- Features Grid -->
                @if(isset($service['features']) && count($service['features']) > 0)
                <div class="grid grid-cols-2 gap-2 mb-4">
                    @foreach(array_slice($service['features'], 0, 4) as $feature)
                    <div class="flex items-center gap-2 text-xs text-gray-600">
                        <i class="fas fa-check-circle text-green-500 text-sm"></i>
                        <span>{{ $feature }}</span>
                    </div>
                    @endforeach
                </div>
                @endif
                
                <!-- CTA -->
                <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                    <span class="text-sm font-semibold text-primary">
                        Pelajari Lebih Lanjut
                    </span>
                    <i class="fas fa-arrow-right text-primary"></i>
                </div>
            </a>
            @endforeach
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="magazine-section bg-gradient-to-br from-[#0077B5] to-[#005582] text-white">
    <div class="content-container text-center">
        <div class="w-16 h-16 rounded-full bg-white/10 flex items-center justify-center mx-auto mb-4">
            <i class="fas fa-phone-alt text-3xl"></i>
        </div>
        <h2 class="headline text-3xl mb-3">
            Butuh Konsultasi?
        </h2>
        <p class="text-base text-white/90 mb-6 leading-relaxed">
            Tim ahli kami siap membantu Anda menentukan layanan yang tepat untuk bisnis Anda
        </p>
        
        <div class="space-y-3">
            <a href="https://wa.me/6283879602855?text=Halo%20Bizmark.ID%2C%20saya%20ingin%20konsultasi%20tentang%20perizinan" 
               target="_blank"
               class="block w-full bg-white text-[#0077B5] font-bold py-4 px-6 rounded-xl shadow-lg hover:shadow-2xl transition-all">
                <i class="fab fa-whatsapp mr-2"></i> Chat via WhatsApp
            </a>
            <a href="tel:+6283879602855" 
               class="block w-full bg-white/10 backdrop-blur text-white font-semibold py-4 px-6 rounded-xl border-2 border-white/30">
                <i class="fas fa-phone mr-2"></i> Telepon Langsung
            </a>
        </div>
    </div>
</section>

@endsection
