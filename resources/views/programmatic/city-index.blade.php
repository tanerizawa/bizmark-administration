@extends('landing.layout')

@section('title', 'Layanan Perizinan Lingkungan di ' . $city['name'] . ' | Bizmark.ID')
@section('meta_title', 'Layanan Perizinan Lingkungan di ' . $city['name'] . ' - Bizmark.ID')
@section('meta_description', 'Jasa konsultan perizinan lingkungan profesional di ' . $city['name'] . ', ' . $city['province'] . '. Layanan AMDAL, UKL-UPL, Limbah B3, SLO & perizinan industri lainnya.')
@section('meta_keywords', 'perizinan lingkungan ' . $city['name'] . ', konsultan amdal ' . $city['name'] . ', jasa ukl upl ' . $city['name'] . ', perizinan limbah b3 ' . $city['name'])

@section('content')

{{-- Breadcrumb Schema --}}
@php
$breadcrumbSchema = [
    '@context' => 'https://schema.org',
    '@type' => 'BreadcrumbList',
    'itemListElement' => [
        ['@type' => 'ListItem', 'position' => 1, 'name' => 'Beranda', 'item' => config('app.url')],
        ['@type' => 'ListItem', 'position' => 2, 'name' => 'Layanan', 'item' => config('app.url') . '/layanan'],
        ['@type' => 'ListItem', 'position' => 3, 'name' => $city['name'], 'item' => config('app.url') . '/layanan/kota/' . $citySlug],
    ],
];
$contact = (array) data_get(config('landing_metrics'), 'contact', []);
$whatsapp = $contact['whatsapp_link'] ?? 'https://wa.me/6283879602855';
$waText = 'Halo Bizmark, saya tertarik dengan layanan perizinan di ' . ($city['name'] ?? '');
$waLink = $whatsapp . (str_contains($whatsapp, '?') ? '&' : '?') . 'text=' . rawurlencode($waText);
@endphp
<script type="application/ld+json">{!! json_encode($breadcrumbSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>

{{-- Hero with editorial gradient --}}
<section class="relative overflow-hidden" style="min-height: clamp(380px, 50vh, 520px);">
    <div class="absolute inset-0 gradient-hero"></div>
    <div class="absolute inset-0 opacity-[.07]">
        <div class="absolute top-10 right-10 w-80 h-80 rounded-full blur-3xl" style="background: var(--color-accent);"></div>
        <div class="absolute bottom-10 left-10 w-64 h-64 rounded-full blur-3xl" style="background: var(--color-secondary);"></div>
    </div>
    <div class="container-wide relative z-10 flex flex-col justify-end h-full" style="min-height: clamp(380px, 50vh, 520px);">
        <div class="pb-10 pt-32 lg:pt-40 lg:pb-14 max-w-3xl">
            {{-- Breadcrumb --}}
            <nav class="mb-6">
                <ol class="flex items-center gap-2 text-sm" style="color: rgba(255,255,255,.5);">
                    <li><a href="/" class="hover:opacity-80 transition"><i class="fas fa-home text-xs"></i></a></li>
                    <li><i class="fas fa-chevron-right text-[10px]" style="color: rgba(255,255,255,.3);"></i></li>
                    <li><a href="{{ route('services.index.id') }}" class="hover:opacity-80 transition">Layanan</a></li>
                    <li><i class="fas fa-chevron-right text-[10px]" style="color: rgba(255,255,255,.3);"></i></li>
                    <li class="font-medium" style="color: rgba(255,255,255,.9);">{{ $city['name'] }}</li>
                </ol>
            </nav>

            {{-- Badge --}}
            <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full backdrop-blur-sm mb-6" style="background: rgba(255,255,255,.1); border: 1px solid rgba(255,255,255,.15);">
                <i class="fas fa-map-marker-alt text-xs" style="color: var(--color-accent);"></i>
                <span class="text-sm font-semibold" style="color: rgba(255,255,255,.9);">{{ $city['name'] }}, {{ $city['province'] }}</span>
            </div>

            {{-- Headline --}}
            <h1 class="font-black leading-[1.1] mb-5" style="font-size: clamp(2rem,4.5vw,3.5rem); letter-spacing: -0.03em; color: #fff;">
                Layanan Perizinan<br>Lingkungan di {{ $city['name'] }}
            </h1>

            {{-- Description --}}
            <p class="text-lg leading-relaxed max-w-2xl mb-8 font-light" style="color: rgba(255,255,255,.65);">
                {{ $city['description'] }}
            </p>

            {{-- CTA --}}
            <div class="flex flex-wrap gap-4 mb-10">
                <a href="{{ $waLink }}" target="_blank" rel="noopener"
                   class="btn btn-success btn-lg" style="background: var(--color-success); border-radius: var(--radius-full);">
                    <i class="fab fa-whatsapp text-lg"></i>
                    <span>Konsultasi Gratis</span>
                </a>
                <a href="#layanan"
                   class="btn btn-ghost btn-lg" style="border-radius: var(--radius-full);">
                    <span>Lihat Layanan</span>
                    <i class="fas fa-chevron-down text-sm"></i>
                </a>
            </div>

            {{-- Stats --}}
            <div class="flex items-center gap-8 text-sm" style="color: rgba(255,255,255,.7);">
                <div>
                    <span class="text-2xl font-black" style="color: #fff;">{{ $city['population'] ?? '-' }}</span>
                    <br><span class="text-xs uppercase tracking-wider" style="color: rgba(255,255,255,.45);">Populasi</span>
                </div>
                <div class="w-px h-8" style="background: rgba(255,255,255,.15);"></div>
                <div>
                    <span class="text-2xl font-black" style="color: #fff;">{{ count($city['industrial_zones'] ?? []) }}</span>
                    <br><span class="text-xs uppercase tracking-wider" style="color: rgba(255,255,255,.45);">Kawasan Industri</span>
                </div>
                <div class="w-px h-8" style="background: rgba(255,255,255,.15);"></div>
                <div>
                    <span class="text-2xl font-black" style="color: var(--color-secondary);">{{ count($services) }}</span>
                    <br><span class="text-xs uppercase tracking-wider" style="color: rgba(255,255,255,.45);">Layanan</span>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Industrial Zones --}}
@if(count($city['industrial_zones'] ?? []) > 0)
<section class="py-12 md:py-16" style="background: var(--surface-warm);">
    <div class="container-wide">
        <div class="max-w-3xl mx-auto text-center mb-10">
            <div class="section-badge mx-auto">
                <i class="fas fa-industry text-xs" style="color: var(--color-accent);"></i>
                <span>Kawasan Industri</span>
            </div>
            <h2 class="section-title" style="color: var(--text-primary);">Kawasan Industri di {{ $city['name'] }}</h2>
            <p class="section-description" style="color: var(--text-secondary);">Kami melayani perusahaan di seluruh kawasan industri {{ $city['name'] }}, termasuk:</p>
        </div>
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4 max-w-5xl mx-auto">
            @foreach($city['industrial_zones'] ?? [] as $zone)
            <div class="flex items-center gap-3 rounded-xl px-5 py-4 transition hover:shadow-md" style="background: var(--surface); border: 1px solid var(--border-light);">
                <div class="flex-shrink-0 w-9 h-9 rounded-lg flex items-center justify-center" style="background: rgba(14,165,233,.08);">
                    <i class="fas fa-building text-sm" style="color: var(--color-accent);"></i>
                </div>
                <span class="font-medium" style="color: var(--text-primary);">{{ $zone }}</span>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- Services Grid --}}
<section class="py-12 md:py-16" id="layanan" style="background: var(--surface);">
    <div class="container-wide">
        <div class="max-w-3xl mx-auto text-center mb-10">
            <div class="section-badge mx-auto">
                <i class="fas fa-briefcase text-xs" style="color: var(--color-secondary);"></i>
                <span>Layanan Profesional</span>
            </div>
            <h2 class="section-title" style="color: var(--text-primary);">Layanan Kami di {{ $city['name'] }}</h2>
            <p class="section-description" style="color: var(--text-secondary);">Solusi lengkap perizinan industri dan konsultasi lingkungan untuk perusahaan di {{ $city['name'] }}.</p>
        </div>
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6 max-w-5xl mx-auto">
            @foreach($services as $slug => $svc)
            <a href="{{ url('/layanan/' . $slug . '/' . $citySlug) }}"
               class="card block">
                <div class="w-11 h-11 rounded-xl flex items-center justify-center mb-4 transition" style="background: {{ $svc['color'] ?? '#0ea5e9' }}12; color: {{ $svc['color'] ?? '#0ea5e9' }};">
                    <i class="fas {{ $svc['icon'] ?? 'fa-file-alt' }} text-lg"></i>
                </div>
                <h3 class="text-lg font-semibold mb-2 card-title" style="color: var(--text-primary);">{{ $svc['title'] }}</h3>
                <p class="text-sm leading-relaxed line-clamp-3" style="color: var(--text-secondary);">{{ $svc['short_description'] ?? $svc['description'] ?? '' }}</p>
                <div class="mt-4 pt-3 border-t flex items-center justify-between" style="border-color:var(--border-light);">
                    <span class="link-primary text-sm">Selengkapnya <i class="fas fa-arrow-right ml-2"></i></span>
                    <span class="text-xs" style="color:var(--text-tertiary);">di {{ $city['name'] }}</span>
                </div>
            </a>
            @endforeach
        </div>
    </div>
</section>

{{-- Key Regulations --}}
@if(!empty($city['key_regulations']))
<section class="py-12 md:py-16" style="background: var(--surface-cool);">
    <div class="container-wide">
        <div class="max-w-3xl mx-auto text-center mb-10">
            <div class="section-badge mx-auto">
                <i class="fas fa-gavel text-xs" style="color: var(--color-secondary);"></i>
                <span>Regulasi</span>
            </div>
            <h2 class="section-title" style="color: var(--text-primary);">Regulasi Utama di {{ $city['name'] }}</h2>
        </div>
        <div class="max-w-4xl mx-auto">
            @if(is_array($city['key_regulations']))
            <div class="grid sm:grid-cols-2 gap-4">
                @foreach($city['key_regulations'] as $reg)
                <div class="flex items-start gap-3 rounded-xl px-5 py-4" style="background: var(--surface); border: 1px solid var(--border-light);">
                    <i class="fas fa-gavel mt-0.5" style="color: var(--color-secondary);"></i>
                    <span class="text-sm leading-relaxed" style="color: var(--text-primary);">{{ $reg }}</span>
                </div>
                @endforeach
            </div>
            @else
            <div class="flex items-start gap-3 rounded-xl px-5 py-4" style="background: var(--surface); border: 1px solid var(--border-light);">
                <i class="fas fa-gavel mt-0.5" style="color: var(--color-secondary);"></i>
                <span class="text-sm leading-relaxed" style="color: var(--text-primary);">{{ $city['key_regulations'] }}</span>
            </div>
            @endif
        </div>
    </div>
</section>
@endif

{{-- Other Cities --}}
<section class="py-12 md:py-16" style="background: var(--surface);">
    <div class="container-wide">
        <div class="max-w-3xl mx-auto text-center mb-10">
            <div class="section-badge mx-auto">
                <i class="fas fa-map-marked-alt text-xs" style="color: var(--color-accent);"></i>
                <span>Jangkauan Layanan</span>
            </div>
            <h2 class="section-title" style="color: var(--text-primary);">Layanan Kami di Kota Lain</h2>
        </div>
        <div class="flex flex-wrap justify-center gap-3 max-w-4xl mx-auto">
            @foreach($otherCities as $cSlug => $c)
            <a href="{{ url('/layanan/kota/' . $cSlug) }}"
               class="chip">
                <i class="fas fa-map-marker-alt text-xs" style="color: var(--color-accent);"></i> {{ $c['name'] }}
            </a>
            @endforeach
        </div>
    </div>
</section>

{{-- CTA Bottom --}}
<section class="relative py-16 md:py-20 overflow-hidden gradient-hero text-center">
    <div class="absolute inset-0 opacity-[.07]">
        <div class="absolute top-10 right-20 w-64 h-64 rounded-full blur-3xl" style="background: var(--color-accent);"></div>
        <div class="absolute bottom-10 left-20 w-48 h-48 rounded-full blur-3xl" style="background: var(--color-secondary);"></div>
    </div>
    <div class="container-wide relative z-10 max-w-3xl mx-auto">
        <h2 class="font-black mb-4" style="font-size: clamp(1.5rem,3vw,2.25rem); letter-spacing: -0.02em; color: #fff;">Butuh Bantuan Perizinan di {{ $city['name'] }}?</h2>
        <p class="text-lg mb-8 font-light" style="color: rgba(255,255,255,.6);">Konsultasi gratis dengan tim ahli kami. Kami siap membantu kebutuhan perizinan perusahaan Anda.</p>
        <a href="{{ $waLink }}" target="_blank" rel="noopener"
           class="btn btn-success btn-lg" style="background: var(--color-success); border-radius: var(--radius-full);">
            <i class="fab fa-whatsapp text-xl"></i> Hubungi Kami Sekarang
        </a>
    </div>
</section>

@endsection
