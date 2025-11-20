@extends('mobile-landing.layouts.content')

@section('title', $service['title'] . ' - Bizmark.ID')
@section('meta_description', $service['short_description'])

@section('content')

<!-- Hero Section -->
<section class="magazine-section bg-gradient-to-br from-blue-50 via-white to-purple-50">
    <div class="content-container">
        <!-- Back Button -->
        <a href="{{ route('services.index') }}" class="inline-flex items-center gap-2 text-sm text-gray-600 mb-6">
            <i class="fas fa-arrow-left"></i>
            <span>Kembali ke Layanan</span>
        </a>
        
        <!-- Icon & Title -->
        <div class="flex items-start gap-4 mb-6">
            <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-[#0077B5] to-[#005582] flex items-center justify-center text-white text-2xl flex-shrink-0 shadow-lg">
                <i class="{{ $service['icon'] }}"></i>
            </div>
            <div class="flex-1">
                <span class="inline-block px-3 py-1 bg-primary/10 text-primary rounded-full text-xs font-semibold mb-2">
                    Layanan Profesional
                </span>
                <h1 class="headline text-3xl text-gray-900 mb-2">
                    {{ $service['title'] }}
                </h1>
                @if(isset($service['subtitle']))
                <p class="text-sm text-gray-500">{{ $service['subtitle'] }}</p>
                @endif
            </div>
        </div>
        
        <!-- Description -->
        <p class="text-base text-gray-600 leading-relaxed mb-6">
            {{ $service['short_description'] }}
        </p>
        
        <!-- Quick Actions -->
        <div class="space-y-3 mb-6">
            <a href="https://wa.me/6283879602855?text=Halo%20Bizmark.ID%2C%20saya%20ingin%20konsultasi%20tentang%20{{ urlencode($service['title']) }}" 
               target="_blank"
               class="btn-primary flex items-center justify-center gap-2">
                <i class="fab fa-whatsapp text-xl"></i>
                <span>Konsultasi via WhatsApp</span>
            </a>
            <a href="tel:+6283879602855" 
               class="block text-center bg-white border-2 border-[#0077B5] text-[#0077B5] font-semibold py-3.5 px-6 rounded-xl">
                <i class="fas fa-phone mr-2"></i> Telepon Langsung
            </a>
        </div>
    </div>
</section>

<!-- Features Section -->
@if(isset($service['features']) && count($service['features']) > 0)
<section class="magazine-section bg-white">
    <div class="content-container">
        <h2 class="headline text-2xl text-gray-900 mb-4">
            <i class="fas fa-check-circle text-green-500 mr-2"></i>
            Fitur Layanan
        </h2>
        <div class="space-y-3">
            @foreach($service['features'] as $feature)
            <div class="flex items-start gap-3 bg-gray-50 p-4 rounded-xl">
                <i class="fas fa-check text-[#0077B5] mt-1 flex-shrink-0"></i>
                <span class="text-sm text-gray-700">{{ $feature }}</span>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif

<!-- Benefits Section -->
@if(isset($service['benefits']) && count($service['benefits']) > 0)
<section class="magazine-section bg-gradient-to-br from-blue-50 to-purple-50">
    <div class="content-container">
        <h2 class="headline text-2xl text-gray-900 mb-4">
            <i class="fas fa-star text-yellow-400 mr-2"></i>
            Keuntungan
        </h2>
        <div class="grid grid-cols-1 gap-4">
            @foreach($service['benefits'] as $benefit)
            <div class="bg-white rounded-xl p-5 shadow-sm">
                <div class="flex items-start gap-3">
                    <div class="w-8 h-8 rounded-full bg-gradient-to-br from-[#0077B5] to-[#005582] flex items-center justify-center text-white text-sm flex-shrink-0">
                        <i class="fas fa-plus"></i>
                    </div>
                    <p class="text-sm text-gray-700 flex-1">{{ $benefit }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif

<!-- Process Section -->
@if(isset($service['process']) && count($service['process']) > 0)
<section class="magazine-section bg-white">
    <div class="content-container">
        <h2 class="headline text-2xl text-gray-900 mb-4">
            <i class="fas fa-tasks text-[#0077B5] mr-2"></i>
            Proses Pengerjaan
        </h2>
        <div class="space-y-4">
            @foreach($service['process'] as $index => $step)
            <div class="flex gap-4">
                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-[#0077B5] to-[#005582] flex items-center justify-center text-white font-bold flex-shrink-0">
                    {{ $index + 1 }}
                </div>
                <div class="flex-1 pt-2">
                    <h3 class="font-semibold text-gray-900 mb-1">{{ $step['title'] ?? $step }}</h3>
                    @if(is_array($step) && isset($step['description']))
                    <p class="text-sm text-gray-600">{{ $step['description'] }}</p>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif

<!-- Documents Section -->
@if(isset($service['documents']) && count($service['documents']) > 0)
<section class="magazine-section bg-gray-50">
    <div class="content-container">
        <h2 class="headline text-2xl text-gray-900 mb-4">
            <i class="fas fa-file-alt text-[#0077B5] mr-2"></i>
            Dokumen yang Dibutuhkan
        </h2>
        <div class="bg-white rounded-2xl p-5 shadow-sm">
            <ul class="space-y-3">
                @foreach($service['documents'] as $document)
                <li class="flex items-start gap-3 text-sm text-gray-700">
                    <i class="fas fa-file text-[#0077B5] mt-1 flex-shrink-0"></i>
                    <span>{{ $document }}</span>
                </li>
                @endforeach
            </ul>
        </div>
    </div>
</section>
@endif

<!-- Related Services -->
@if(count($relatedServices) > 0)
<section class="magazine-section bg-white">
    <div class="content-container">
        <h2 class="headline text-2xl text-gray-900 mb-6">
            Layanan Lainnya
        </h2>
        <div class="space-y-4">
            @foreach($relatedServices as $slug => $related)
            <a href="{{ route('services.show', $slug) }}" 
               class="block bg-gradient-to-br from-gray-50 to-white rounded-xl p-5 shadow-sm hover:shadow-md transition-all border border-gray-100">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-[#0077B5] to-[#005582] flex items-center justify-center text-white flex-shrink-0">
                        <i class="{{ $related['icon'] }}"></i>
                    </div>
                    <div class="flex-1">
                        <h3 class="font-bold text-gray-900 mb-1">{{ $related['title'] }}</h3>
                        <p class="text-xs text-gray-500 line-clamp-2">{{ $related['short_description'] }}</p>
                    </div>
                    <i class="fas fa-arrow-right text-[#0077B5]"></i>
                </div>
            </a>
            @endforeach
        </div>
    </div>
</section>
@endif

<!-- CTA Section -->
<section class="magazine-section bg-gradient-to-br from-[#0077B5] to-[#005582] text-white">
    <div class="content-container text-center">
        <div class="w-16 h-16 rounded-full bg-white/10 flex items-center justify-center mx-auto mb-4">
            <i class="fas fa-headset text-3xl"></i>
        </div>
        <h2 class="headline text-3xl mb-3">
            Siap Memulai?
        </h2>
        <p class="text-base text-white/90 mb-6 leading-relaxed">
            Konsultasikan kebutuhan {{ $service['title'] }} Anda dengan tim ahli kami
        </p>
        
        <a href="https://wa.me/6283879602855?text=Halo%20Bizmark.ID%2C%20saya%20ingin%20konsultasi%20tentang%20{{ urlencode($service['title']) }}" 
           target="_blank"
           class="inline-block w-full bg-white text-[#0077B5] font-bold py-4 px-6 rounded-xl shadow-lg hover:shadow-2xl transition-all">
            <i class="fab fa-whatsapp mr-2"></i> Konsultasi Gratis Sekarang
        </a>
    </div>
</section>

@endsection
