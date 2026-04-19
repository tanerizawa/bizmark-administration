@extends('landing.layout')

@section('title', 'Our Services - Bizmark.ID')
@section('meta_description', 'Integrated permit and compliance services in Indonesia, from planning to approval.')

@section('content')
@php
    $contact = (array) data_get(config('landing_metrics'), 'contact', []);
    $whatsapp = $contact['whatsapp_link'] ?? 'https://wa.me/6283879602855';
    $waText = 'Hello, I want to discuss permit services for my business';
    $waHref = $whatsapp . (str_contains($whatsapp, '?') ? '&' : '?') . 'text=' . rawurlencode($waText);
    $groupedServices = collect($services)->groupBy(fn($service) => $service['category'] ?? 'Other');
@endphp

<section class="relative overflow-hidden pt-28 pb-16" style="background:linear-gradient(135deg,var(--surface-warm) 0%, var(--surface-cool) 100%);">
    <div class="container-wide">
        <span class="section-badge mb-4">Services</span>
        <h1 class="section-title mb-4">One Consistent Workflow for Business Permits</h1>
        <p class="section-description mb-8" style="margin-left:0;">Explore all services in one integrated system and continue seamlessly from landing to detail to contact.</p>
        <div class="flex flex-wrap gap-3">
            <a href="{{ $waHref }}" class="btn btn-secondary"><i class="fab fa-whatsapp"></i> Free Consultation</a>
            <a href="{{ route('contact.index') }}" class="btn btn-outline-primary"><i class="fas fa-envelope"></i> Contact Team</a>
        </div>
    </div>
</section>

<section class="section">
    <div class="container-wide">
        @foreach($groupedServices as $categoryName => $items)
            <div class="mb-14 last:mb-0">
                <div class="mb-6">
                    <span class="section-badge">{{ $categoryName }}</span>
                    <h2 class="text-2xl font-bold mb-1" style="color:var(--text-primary);">{{ $categoryName }}</h2>
                    <p class="text-sm mb-0" style="color:var(--text-secondary);">{{ count($items) }} services available</p>
                </div>

                <div class="grid md:grid-cols-2 xl:grid-cols-3 gap-6">
                    @foreach($items as $slug => $service)
                        <article class="card h-full flex flex-col">
                            <div class="flex items-start justify-between gap-4 mb-4">
                                <div class="w-12 h-12 rounded-lg flex items-center justify-center" style="background:{{ $service['color'] ?? '#0f172a' }}20;">
                                    <i class="fas {{ $service['icon'] ?? 'fa-layer-group' }}" style="color:{{ $service['color'] ?? '#0f172a' }};"></i>
                                </div>
                                @if(!empty($service['badge']))
                                    <span class="badge-featured">{{ $service['badge'] }}</span>
                                @endif
                            </div>

                            <h3 class="text-lg font-bold mb-2 card-title" style="color:var(--text-primary);">{{ $service['title'] }}</h3>
                            <p class="text-sm mb-4" style="color:var(--text-secondary);">{{ $service['short_description'] }}</p>

                            @if(!empty($service['duration']) || !empty($service['pricing']['display']))
                                <div class="flex flex-wrap gap-2 mb-4">
                                    @if(!empty($service['duration']))
                                        <span class="text-xs px-2 py-1 rounded-full" style="background:var(--surface-cool);color:var(--text-secondary);"><i class="fas fa-clock mr-1"></i>{{ $service['duration'] }}</span>
                                    @endif
                                    @if(!empty($service['pricing']['display']))
                                        <span class="text-xs px-2 py-1 rounded-full" style="background:rgba(22,163,74,.12);color:var(--color-success);"><i class="fas fa-tag mr-1"></i>{{ $service['pricing']['display'] }}</span>
                                    @endif
                                </div>
                            @endif

                            <div class="mt-auto pt-3 border-t" style="border-color:var(--border-light);">
                                <a href="{{ route('services.show.en', $slug) }}" class="link-primary text-sm inline-flex items-center">
                                    Learn More <i class="fas fa-arrow-right ml-2"></i>
                                </a>
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>
</section>

<section class="section-sm" style="background:var(--surface-dark);">
    <div class="container-wide text-center">
        <h2 class="text-white mb-3" style="font-size:clamp(1.5rem,3vw,2.1rem);font-weight:750;">Need Help Selecting the Right Service?</h2>
        <p class="mb-7" style="color:rgba(255,255,255,.74);">Share your business profile and we will suggest the best permit priority map.</p>
        <div class="flex flex-wrap justify-center gap-3">
            <a href="{{ $waHref }}" class="btn btn-success"><i class="fab fa-whatsapp"></i> WhatsApp</a>
            <a href="{{ route('landing.service-inquiry.create') }}" class="btn btn-secondary"><i class="fas fa-robot"></i> AI Permit Analysis</a>
        </div>
    </div>
</section>
@endsection
