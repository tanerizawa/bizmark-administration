@extends('landing.layout')

@section('title', $title ?? $subService['title'] . ' - Bizmark.ID')
@section('meta_description', $meta_description ?? $subService['short_description'])
@section('meta_keywords', $subService['meta_keywords'] ?? $parentService['meta_keywords'] ?? '')

@section('structured_data')
{{-- BreadcrumbList --}}
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@type": "BreadcrumbList",
    "itemListElement": [
        {"@@type": "ListItem", "position": 1, "name": "Beranda", "item": "{{ route('landing.id') }}"},
        {"@@type": "ListItem", "position": 2, "name": "Layanan", "item": "{{ route('services.index.id') }}"},
        {"@@type": "ListItem", "position": 3, "name": "{{ $parentService['title'] }}", "item": "{{ route('services.show.id', $parentSlug) }}"},
        {"@@type": "ListItem", "position": 4, "name": "{{ $subService['title'] }}"}
    ]
}
</script>
{{-- Service Schema --}}
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@type": "Service",
    "name": "{{ $subService['title'] }}",
    "description": "{{ $subService['short_description'] }}",
    "provider": {
        "@@type": "Organization",
        "name": "PT Cangah Pajaratan Mandiri (Bizmark.ID)",
        "url": "https://bizmark.id"
    },
    "areaServed": {"@@type": "Country", "name": "Indonesia"},
    "isRelatedTo": {
        "@@type": "Service",
        "name": "{{ $parentService['title'] }}",
        "url": "{{ route('services.show.id', $parentSlug) }}"
    }
}
</script>
{{-- FAQ Schema --}}
@if(!empty($parentService['faq']))
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@type": "FAQPage",
    "mainEntity": [
        @foreach($parentService['faq'] as $faqItem)
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
    $phoneRaw = $contact['phone'] ?? '';
    $phoneHref = $phoneRaw !== '' ? ('tel:' . preg_replace('/\s+/', '', $phoneRaw)) : '';
    $supportEmail = $contact['email'] ?? '';
    $waText = 'Halo, saya tertarik dengan layanan ' . ($subService['title'] ?? '') . ' (' . ($parentService['title'] ?? '') . ')';
    $waHref = $whatsappBase . (str_contains($whatsappBase, '?') ? '&' : '?') . 'text=' . rawurlencode($waText);
    $parentColor = $parentService['color'] ?? '#1E40AF';
    $subIcon = $subService['icon'] ?? $parentService['icon'] ?? 'fa-concierge-bell';
    $routePrefix = $locale === 'en' ? 'services.' : 'services.';
@endphp

<!-- Breadcrumb -->
<section class="bg-gray-50 py-6 mt-20">
    <div class="container">
        <nav class="flex flex-wrap items-center text-sm text-gray-600" aria-label="Breadcrumb">
            <a href="{{ route('landing.id') }}" class="hover:text-primary transition">
                <i class="fas fa-home mr-1"></i>Beranda
            </a>
            <i class="fas fa-chevron-right mx-2 text-gray-400 text-xs"></i>
            <a href="{{ route('services.index.id') }}" class="hover:text-primary transition">Layanan</a>
            <i class="fas fa-chevron-right mx-2 text-gray-400 text-xs"></i>
            <a href="{{ route('services.show.id', $parentSlug) }}" class="hover:text-primary transition">{{ $parentService['title'] }}</a>
            <i class="fas fa-chevron-right mx-2 text-gray-400 text-xs"></i>
            <span class="text-gray-900 font-medium">{{ $subService['title'] }}</span>
        </nav>
    </div>
</section>

<!-- Hero Section -->
<section class="section bg-gradient-to-br from-white via-gray-50 to-white pt-12 pb-16">
    <div class="container">
        <div class="max-w-5xl mx-auto">
            <div class="flex flex-col md:flex-row items-start gap-8" data-aos="fade-up">
                <!-- Icon -->
                <div class="w-20 h-20 md:w-24 md:h-24 rounded-3xl flex items-center justify-center flex-shrink-0 shadow-lg" style="background: linear-gradient(135deg, {{ $parentColor }}20 0%, {{ $parentColor }}40 100%);">
                    <i class="fas {{ $subIcon }} text-4xl md:text-5xl" style="color: {{ $parentColor }};"></i>
                </div>
                
                <!-- Content -->
                <div class="flex-1">
                    <div class="flex flex-wrap items-center gap-2 mb-4">
                        <a href="{{ route('services.show.id', $parentSlug) }}" class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold transition hover:opacity-80" style="background: {{ $parentColor }}15; color: {{ $parentColor }};">
                            <i class="fas {{ $parentService['icon'] ?? 'fa-tag' }} mr-1.5"></i>
                            {{ $parentService['title'] }}
                        </a>
                        @if(!empty($subService['duration']))
                        <span class="inline-flex items-center px-3 py-1 bg-blue-50 text-blue-700 rounded-full text-xs font-semibold">
                            <i class="fas fa-clock mr-1.5"></i>{{ $subService['duration'] }}
                        </span>
                        @endif
                    </div>
                    
                    <h1 class="text-3xl md:text-4xl lg:text-5xl font-bold mb-4 text-gray-900">
                        {{ $subService['title'] }}
                    </h1>
                    <p class="text-lg md:text-xl text-gray-600 leading-relaxed mb-6">
                        {{ $subService['short_description'] }}
                    </p>
                    
                    <!-- Quick Actions -->
                    <div class="flex flex-wrap gap-3">
                        <a href="{{ $waHref }}" target="_blank" rel="noopener" class="btn btn-primary">
                            <i class="fab fa-whatsapp mr-2"></i>
                            Konsultasi Gratis
                        </a>
                        <a href="{{ $phoneHref }}" class="btn bg-white border-2 border-gray-300 text-gray-700 hover:border-primary hover:text-primary">
                            <i class="fas fa-phone mr-2"></i>
                            Telepon Kami
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Main Content -->
<section class="section bg-white">
    <div class="container">
        <div class="max-w-5xl mx-auto">
            <div class="grid lg:grid-cols-3 gap-8">
                <!-- Main Content Column -->
                <div class="lg:col-span-2 space-y-10">
                    
                    <!-- Long Description -->
                    @if(!empty($subService['long_description']))
                    <div class="prose prose-lg max-w-none" data-aos="fade-up">
                        <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-4 flex items-center">
                            <i class="fas fa-info-circle mr-3" style="color: {{ $parentColor }};"></i>
                            Tentang {{ $subService['title'] }}
                        </h2>
                        <div class="text-gray-600 leading-relaxed space-y-4">
                            <p>{{ $subService['long_description'] }}</p>
                        </div>
                    </div>
                    @endif

                    <!-- Process Steps -->
                    @if(!empty($subService['process_steps']))
                    <div data-aos="fade-up">
                        <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-6 flex items-center">
                            <i class="fas fa-route mr-3" style="color: {{ $parentColor }};"></i>
                            Proses Pengurusan
                        </h2>
                        <div class="space-y-4">
                            @foreach($subService['process_steps'] as $index => $step)
                            <div class="flex gap-4 p-5 bg-gradient-to-r from-gray-50 to-white rounded-xl border border-gray-100 hover:shadow-md transition">
                                <div class="flex-shrink-0 w-12 h-12 rounded-full flex items-center justify-center font-bold text-lg text-white shadow-lg" style="background: linear-gradient(135deg, {{ $parentColor }}, {{ $parentColor }}CC);">
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
                    @if(!empty($subService['requirements']))
                    <div data-aos="fade-up">
                        <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-6 flex items-center">
                            <i class="fas fa-file-alt mr-3 text-yellow-600"></i>
                            Persyaratan & Dokumen
                        </h2>
                        <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-6">
                            <ul class="space-y-3">
                                @foreach($subService['requirements'] as $req)
                                <li class="flex items-start gap-3">
                                    <i class="fas fa-check-circle mt-0.5 text-yellow-600"></i>
                                    <span class="text-gray-700">{{ $req }}</span>
                                </li>
                                @endforeach
                            </ul>
                            <div class="mt-4 p-3 bg-white rounded-lg text-sm text-gray-600">
                                <i class="fas fa-info-circle mr-2 text-blue-600"></i>
                                Persyaratan detail akan dibahas saat konsultasi awal bersama tim kami.
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- FAQ Section -->
                    @if(!empty($parentService['faq']))
                    <div data-aos="fade-up">
                        <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-6 flex items-center">
                            <i class="fas fa-question-circle mr-3" style="color: {{ $parentColor }};"></i>
                            Pertanyaan Umum (FAQ)
                        </h2>
                        <div class="space-y-4">
                            @foreach($parentService['faq'] as $faq)
                            <details class="group bg-white rounded-xl border border-gray-200 overflow-hidden">
                                <summary class="flex items-center justify-between p-5 cursor-pointer hover:bg-gray-50 transition">
                                    <span class="font-semibold text-gray-900 pr-4">{{ $faq['q'] }}</span>
                                    <i class="fas fa-chevron-down text-gray-400 group-open:rotate-180 transition-transform flex-shrink-0"></i>
                                </summary>
                                <div class="px-5 pb-5 text-gray-600 leading-relaxed border-t border-gray-100 pt-4">
                                    {{ $faq['a'] }}
                                </div>
                            </details>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <!-- Related Sub-Services (Siblings) -->
                    @if(count($relatedSubs) > 0)
                    <div data-aos="fade-up">
                        <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-6 flex items-center">
                            <i class="fas fa-th-list mr-3" style="color: {{ $parentColor }};"></i>
                            Layanan {{ $parentService['title'] }} Lainnya
                        </h2>
                        <div class="grid sm:grid-cols-2 gap-4">
                            @foreach($relatedSubs as $sibSlug => $sibling)
                            <a href="{{ route('services.sub.id', [$parentSlug, $sibSlug]) }}" class="flex items-start gap-4 p-4 rounded-xl border border-gray-200 hover:border-primary/30 hover:shadow-md transition group">
                                <div class="w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0" style="background: {{ $parentColor }}15;">
                                    <i class="fas {{ $sibling['icon'] ?? $parentService['icon'] }} text-sm" style="color: {{ $parentColor }};"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="font-semibold text-gray-900 group-hover:text-primary transition text-sm">{{ $sibling['title'] }}</div>
                                    <p class="text-xs text-gray-500 mt-1 line-clamp-2">{{ $sibling['short_description'] }}</p>
                                </div>
                                <i class="fas fa-chevron-right text-xs text-gray-400 group-hover:text-primary transition mt-1"></i>
                            </a>
                            @endforeach
                        </div>
                    </div>
                    @endif

                </div>
                
                <!-- Sidebar -->
                <div class="lg:col-span-1">
                    <div class="sticky top-24 space-y-6">
                        
                        <!-- Quick Info Card -->
                        <div class="card border-2" style="border-color: {{ $parentColor }}22;">
                            <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                                <i class="fas fa-clipboard-list mr-2" style="color: {{ $parentColor }};"></i>
                                Ringkasan
                            </h3>
                            <ul class="space-y-3 text-sm text-gray-700">
                                @if(!empty($subService['duration']))
                                <li class="flex items-start gap-3">
                                    <i class="fas fa-clock mt-0.5" style="color: {{ $parentColor }};"></i>
                                    <div>
                                        <span class="text-xs text-gray-500 block">Estimasi Waktu</span>
                                        <span class="font-semibold">{{ $subService['duration'] }}</span>
                                    </div>
                                </li>
                                @endif
                                @if(!empty($parentService['price']))
                                <li class="flex items-start gap-3">
                                    <i class="fas fa-tag mt-0.5 text-green-600"></i>
                                    <div>
                                        <span class="text-xs text-gray-500 block">Harga Mulai</span>
                                        <span class="font-semibold">{{ $parentService['price'] }}</span>
                                    </div>
                                </li>
                                @endif
                                @if(!empty($subService['process_steps']))
                                <li class="flex items-start gap-3">
                                    <i class="fas fa-list-ol mt-0.5" style="color: {{ $parentColor }};"></i>
                                    <div>
                                        <span class="text-xs text-gray-500 block">Tahapan Proses</span>
                                        <span class="font-semibold">{{ count($subService['process_steps']) }} langkah</span>
                                    </div>
                                </li>
                                @endif
                                @if(!empty($subService['requirements']))
                                <li class="flex items-start gap-3">
                                    <i class="fas fa-file-alt mt-0.5" style="color: {{ $parentColor }};"></i>
                                    <div>
                                        <span class="text-xs text-gray-500 block">Dokumen Dibutuhkan</span>
                                        <span class="font-semibold">{{ count($subService['requirements']) }} dokumen</span>
                                    </div>
                                </li>
                                @endif
                                <li class="flex items-start gap-3">
                                    <i class="fas fa-shield-alt mt-0.5 text-green-600"></i>
                                    <div>
                                        <span class="text-xs text-gray-500 block">Garansi</span>
                                        <span class="font-semibold">Proses Transparan</span>
                                    </div>
                                </li>
                            </ul>
                            <a href="{{ $waHref }}" target="_blank" rel="noopener" class="btn btn-primary w-full justify-center mt-5">
                                <i class="fab fa-whatsapp mr-2"></i>
                                Konsultasi Gratis
                            </a>
                        </div>

                        <!-- Contact Card -->
                        <div class="card bg-gradient-to-br from-primary to-primary-dark text-white">
                            <h3 class="text-lg font-bold mb-3 flex items-center">
                                <i class="fas fa-headset mr-2"></i>
                                Butuh Bantuan?
                            </h3>
                            <p class="text-white/90 text-sm mb-4">Tim kami siap membantu Anda</p>
                            <div class="space-y-3">
                                <a href="{{ $whatsappBase }}" target="_blank" rel="noopener" class="flex items-center text-white hover:text-white/80 transition">
                                    <i class="fab fa-whatsapp text-lg mr-3"></i>
                                    <div>
                                        <div class="text-xs text-white/70">WhatsApp</div>
                                        <div class="font-semibold text-sm">{{ $phoneRaw }}</div>
                                    </div>
                                </a>
                                <a href="{{ $phoneHref }}" class="flex items-center text-white hover:text-white/80 transition">
                                    <i class="fas fa-phone text-lg mr-3"></i>
                                    <div>
                                        <div class="text-xs text-white/70">Telepon</div>
                                        <div class="font-semibold text-sm">{{ $phoneRaw }}</div>
                                    </div>
                                </a>
                                <a href="mailto:{{ $supportEmail }}" class="flex items-center text-white hover:text-white/80 transition">
                                    <i class="fas fa-envelope text-lg mr-3"></i>
                                    <div>
                                        <div class="text-xs text-white/70">Email</div>
                                        <div class="font-semibold text-sm">{{ $supportEmail }}</div>
                                    </div>
                                </a>
                            </div>
                        </div>

                        <!-- Parent Service Link -->
                        <a href="{{ route('services.show.id', $parentSlug) }}" class="card flex items-center gap-4 hover:shadow-lg transition group">
                            <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0" style="background: {{ $parentColor }}15;">
                                <i class="fas {{ $parentService['icon'] }} text-xl" style="color: {{ $parentColor }};"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="text-xs text-gray-500">Kembali ke</div>
                                <div class="font-semibold text-gray-900 group-hover:text-primary transition truncate">{{ $parentService['title'] }}</div>
                            </div>
                            <i class="fas fa-arrow-right text-gray-400 group-hover:text-primary transition"></i>
                        </a>

                        <!-- Other Main Services -->
                        @if(count($relatedServices) > 0)
                        <div class="card">
                            <h3 class="text-lg font-bold mb-4 text-gray-900">Layanan Lainnya</h3>
                            <div class="space-y-3">
                                @foreach($relatedServices as $relSlug => $related)
                                <a href="{{ route('services.show.id', $relSlug) }}" class="flex items-center gap-3 p-3 rounded-lg hover:bg-gray-50 transition group">
                                    <div class="w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0" style="background: {{ $related['color'] ?? '#1E40AF' }}20;">
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
<section class="py-8 bg-gray-50 border-y border-gray-100">
    <div class="container">
        <div class="flex flex-wrap justify-center gap-8 md:gap-12 text-center" data-aos="fade-up">
            <div>
                <div class="text-2xl font-black text-gray-900">10+</div>
                <div class="text-xs text-gray-500">Tahun Pengalaman</div>
            </div>
            <div>
                <div class="text-2xl font-black text-gray-900">500+</div>
                <div class="text-xs text-gray-500">Proyek Selesai</div>
            </div>
            <div>
                <div class="text-2xl font-black text-gray-900">98%</div>
                <div class="text-xs text-gray-500">Tingkat Keberhasilan</div>
            </div>
            <div>
                <div class="text-2xl font-black text-gray-900">200+</div>
                <div class="text-xs text-gray-500">Klien Aktif</div>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="section bg-gradient-to-br from-gray-900 to-gray-800 text-white">
    <div class="container">
        <div class="max-w-4xl mx-auto text-center" data-aos="fade-up">
            <div class="inline-flex items-center px-4 py-1.5 rounded-full text-sm font-semibold mb-6" style="background: {{ $parentColor }}30; color: {{ $parentColor }};">
                <i class="fas fa-bolt mr-2"></i>
                Konsultasi Awal Gratis
            </div>
            <h2 class="text-3xl md:text-4xl font-bold mb-4">
                Jangan Tunda Pengurusan {{ $subService['title'] }}
            </h2>
            <p class="text-lg md:text-xl mb-8 text-white/80">
                Regulasi terus diperketat — pastikan bisnis Anda patuh sebelum tenggat. Hubungi kami sekarang.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ $waHref }}" target="_blank" rel="noopener" class="btn bg-secondary hover:bg-secondary-dark px-8 py-4 text-lg">
                    <i class="fab fa-whatsapp mr-2"></i>
                    Konsultasi Sekarang
                </a>
                <a href="{{ route('services.show.id', $parentSlug) }}" class="btn bg-white/10 hover:bg-white/20 text-white border border-white/30 px-8 py-4 text-lg">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Kembali ke {{ $parentService['title'] }}
                </a>
            </div>
        </div>
    </div>
</section>

@endsection