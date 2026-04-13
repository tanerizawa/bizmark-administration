@extends('mobile-landing.layouts.content')

@section('title', 'Layanan Kami - Bizmark.ID')
@section('meta_description', 'Layanan lengkap perizinan industri dan konsultasi lingkungan untuk bisnis Anda')

@section('content')

@php
    $contact = (array) data_get(config('landing_metrics'), 'contact', []);
    $whatsappBase = $contact['whatsapp_link'] ?? '';
    $phoneRaw = $contact['phone'] ?? '';
    $phoneHref = $phoneRaw !== '' ? ('tel:' . preg_replace('/\s+/', '', $phoneRaw)) : '';
    $waText = 'Halo Bizmark.ID, saya ingin konsultasi tentang perizinan';
    $whatsappHref = $whatsappBase . (str_contains($whatsappBase, '?') ? '&' : '?') . 'text=' . rawurlencode($waText);
@endphp

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
            <a href="{{ route($locale === 'en' ? 'services.show.en' : 'services.show.id', $slug) }}" 
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
            <a href="{{ $whatsappHref }}" 
               target="_blank"
               rel="noopener"
               class="block w-full bg-white text-[#0077B5] font-bold py-4 px-6 rounded-xl shadow-lg hover:shadow-2xl transition-all">
                <i class="fab fa-whatsapp mr-2"></i> Chat via WhatsApp
            </a>
            <a href="{{ $phoneHref }}" 
               class="block w-full bg-white/10 backdrop-blur text-white font-semibold py-4 px-6 rounded-xl border-2 border-white/30">
                <i class="fas fa-phone mr-2"></i> Telepon Langsung
            </a>
        </div>
    </div>
</section>

{{-- Mobile City Coverage (Collapsible) --}}
@php
    $mobileCities = config('programmatic_seo.cities', []);
    if (count($mobileCities) > 0) {
        $mByProv = collect($mobileCities)->groupBy('province')->sortKeys();
        $mProvOrder = ['Jawa Barat', 'DKI Jakarta', 'Banten', 'Jawa Tengah', 'DI Yogyakarta', 'Jawa Timur'];
        $mSortedProvs = collect($mProvOrder)
            ->filter(fn($p) => $mByProv->has($p))
            ->merge($mByProv->keys()->diff($mProvOrder)->sort())
            ->unique();
    }
@endphp
@if(count($mobileCities) > 0)
<section class="py-5 bg-gray-50">
    <div class="content-container">
        <div class="flex items-center gap-2 mb-3">
            <i class="fas fa-map-marked-alt text-xs text-blue-600"></i>
            <span class="text-sm font-bold text-gray-900">Jangkauan Wilayah</span>
            <span class="text-xs text-gray-500">({{ count($mobileCities) }} kota)</span>
        </div>
        <div class="flex flex-wrap gap-1">
            @foreach($mSortedProvs as $mp)
            @php $mpCities = $mByProv[$mp]; @endphp
            <button onclick="toggleMProv(this,'{{ Str::slug($mp) }}')" class="mprov-btn inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-xs font-semibold bg-white border border-gray-200 text-gray-800 transition-all duration-200" aria-expanded="false">
                {{ $mp }} <span class="text-[10px] font-normal text-blue-500">{{ count($mpCities) }}</span>
                <i class="fas fa-chevron-down text-[8px] text-gray-400 transition-transform duration-200 mprov-chev"></i>
            </button>
            @endforeach
        </div>
        <div id="mprov-panel" class="overflow-hidden transition-all duration-300" style="max-height: 0;">
            @foreach($mSortedProvs as $mp)
            @php $mpCities = $mByProv[$mp]; @endphp
            <div id="mprov-{{ Str::slug($mp) }}" class="mprov-cities hidden">
                <div class="mt-1.5 rounded-lg px-3 py-2.5" style="background: #1E1B18;">
                    <div class="flex items-center gap-1.5 mb-1.5">
                        <i class="fas fa-map-marker-alt text-[10px]" style="color: #E8956F;"></i>
                        <span class="text-[11px] font-bold uppercase tracking-wider" style="color: #E8956F;">{{ $mp }}</span>
                    </div>
                    <div class="flex flex-wrap gap-1">
                        @foreach($mpCities as $mc)
                        <a href="{{ url('/layanan/kota/' . $mc['slug']) }}" class="px-2.5 py-1 rounded-full text-xs font-medium transition-colors duration-200" style="background: rgba(255,255,255,.08); color: rgba(255,255,255,.85); border: 1px solid rgba(255,255,255,.1);">
                            {{ $mc['name'] }}
                        </a>
                        @endforeach
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
<script>
function toggleMProv(btn, slug) {
    var panel = document.getElementById('mprov-panel');
    var target = document.getElementById('mprov-' + slug);
    var wasOpen = btn.getAttribute('aria-expanded') === 'true';
    document.querySelectorAll('.mprov-btn').forEach(function(b) {
        b.setAttribute('aria-expanded', 'false');
        b.style.background = '';
        b.style.color = '';
        b.style.borderColor = '';
        b.querySelector('.mprov-chev').style.transform = '';
        b.querySelector('.mprov-chev').style.color = '';
    });
    document.querySelectorAll('.mprov-cities').forEach(function(c) { c.classList.add('hidden'); });
    if (!wasOpen) {
        target.classList.remove('hidden');
        btn.setAttribute('aria-expanded', 'true');
        btn.style.background = '#1E1B18';
        btn.style.borderColor = '#1E1B18';
        btn.style.color = '#E8956F';
        btn.querySelector('.mprov-chev').style.transform = 'rotate(180deg)';
        btn.querySelector('.mprov-chev').style.color = '#E8956F';
        panel.style.maxHeight = target.scrollHeight + 12 + 'px';
    } else {
        panel.style.maxHeight = '0';
    }
}
</script>
@endif

@endsection
