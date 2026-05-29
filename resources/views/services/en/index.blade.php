@extends('landing.layout')

@section('title', 'Our Services - Bizmark.ID')
@section('meta_description', 'Integrated permit and compliance services in Indonesia, from planning to approval.')

@section('content')
@php
    $contact = data_get(config('landing_metrics'), 'contact', []);
    $whatsapp = $contact['whatsapp_link'] ?? 'https://wa.me/6283879602855';
    $waText = 'Hello, I want to discuss permit services for my business';
    $waHref = $whatsapp . (str_contains($whatsapp, '?') ? '&' : '?') . 'text=' . rawurlencode($waText);
@endphp

<section class="relative overflow-hidden pt-28 pb-16 bg-[var(--bg-raised)] border-b border-gray-200">
    <div class="container-wide">
        <span class="section-badge mb-4">Services</span>
        <h1 class="section-title mb-4">End-to-End Permit Services for Your Business</h1>
        <p class="section-description mb-8" style="margin-left:0;">Browse all our permit services, explore the full scope and requirements for each, and connect with our team when you are ready to start.</p>
        <div class="flex flex-wrap gap-3">
            <a href="{{ $waHref }}" class="btn btn-secondary"><i class="fab fa-whatsapp"></i> Free Consultation — We Respond Fast</a>
            <a href="{{ route('pma.inquiry.create') }}" class="btn btn-ghost"><i class="fas fa-robot"></i> Check My Permits via AI</a>
        </div>
    </div>
</section>

{{-- Sticky category anchor nav --}}
<div class="sticky top-16 z-30 bg-white border-b border-gray-200 shadow-sm">
    <div class="container-wide overflow-x-auto">
        <div class="flex items-center gap-1 py-2 min-w-max">
            @foreach($groupedServices as $categoryName => $_)
            <a href="#cat-{{ Str::slug($categoryName) }}"
               class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold text-gray-600 hover:text-amber-700 hover:bg-amber-50 border border-transparent hover:border-amber-200 transition-all duration-200 whitespace-nowrap">
                {{ $categoryName }}
            </a>
            @endforeach
        </div>
    </div>
</div>

<section class="section">
    <div class="container-wide">
        @foreach($groupedServices as $categoryName => $items)
            @php
                $totalInCat = count($items);
                $displayItems = $totalInCat > 3 ? array_slice($items, 0, 3, true) : $items;
                $catSlug = \Str::slug($categoryName);
            @endphp
            <div id="cat-{{ $catSlug }}" class="mb-14 last:mb-0 scroll-mt-24">
                <div class="flex items-center gap-3 mb-6">
                    <span class="section-badge">{{ $categoryName }}</span>
                    <span class="text-sm text-gray-500">{{ $totalInCat }} services available</span>
                    <h2 class="sr-only">{{ $categoryName }}</h2>
                </div>

                <div class="grid md:grid-cols-2 xl:grid-cols-3 gap-6">
                    @foreach($displayItems as $slug => $service)
                        <article class="card h-full flex flex-col">
                            <div class="flex items-start justify-between gap-4 mb-4">
                                <span class="editorial-icon-badge w-12 h-12 !rounded-xl flex-shrink-0">
                                    <i class="fas {{ $service['icon'] ?? 'fa-layer-group' }} icon-md" aria-hidden="true"></i>
                                </span>
                                @if(!empty($service['badge']))
                                    <span class="badge-featured">{{ $service['badge'] }}</span>
                                @endif
                            </div>

                            <h3 class="text-lg font-bold mb-2 card-title line-clamp-2 text-gray-900">{{ $service['title'] ?? '' }}</h3>
                            <p class="text-sm mb-4 line-clamp-3 text-gray-600">{{ $service['short_description'] ?? '' }}</p>

                            @if(!empty($service['duration']) || !empty($service['pricing']['display']))
                                <div class="flex flex-wrap gap-2 mb-4">
                                    @if(!empty($service['duration']))
                                        <span class="text-xs px-2 py-1 rounded-full bg-gray-100 text-gray-600"><i class="fas fa-clock mr-1"></i>{{ $service['duration'] }}</span>
                                    @endif
                                    @if(!empty($service['pricing']['display']))
                                        <span class="text-xs px-2 py-1 rounded-full bg-amber-500/10 text-amber-700"><i class="fas fa-tag mr-1"></i>{{ $service['pricing']['display'] }}</span>
                                    @endif
                                </div>
                            @endif

                            <div class="mt-auto pt-3 border-t border-gray-200">
                                <a href="{{ route('services.show.en', $slug) }}" class="link-primary text-sm inline-flex items-center">
                                    Learn More <i class="fas fa-arrow-right ml-2"></i>
                                </a>
                            </div>
                        </article>
                    @endforeach

                    {{-- "See All" card — only when category > 3 --}}
                    @if($totalInCat > 3)
                        <a href="{{ route('services.category.en', $catSlug) }}"
                           class="card h-full flex flex-col items-center justify-center text-center border-dashed hover:border-amber-400 hover:bg-amber-50/30 transition-all duration-200 group min-h-[220px]">
                            <span class="editorial-icon-badge mb-4 group-hover:scale-110 transition-transform" style="width:3rem;height:3rem;border-radius:.75rem;">
                                <i class="fas fa-layer-group icon-md" aria-hidden="true"></i>
                            </span>
                            <p class="text-sm font-semibold mb-1 text-gray-900">
                                +{{ $totalInCat - 3 }} More Services
                            </p>
                            <p class="text-xs mb-3 text-gray-500">in {{ $categoryName }}</p>
                            <span class="inline-flex items-center gap-1.5 text-xs font-semibold px-3 py-1.5 rounded-full bg-amber-500/10 text-amber-700">
                                See All <i class="fas fa-arrow-right ml-1"></i>
                            </span>
                        </a>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
</section>

<section class="section-sm section-premium">
    <div class="container-wide text-center">
        <h2 class="text-gray-900 mb-3" style="font-size:clamp(1.5rem,3vw,2.1rem);font-weight:750;">Need Help Selecting the Right Service?</h2>
        <p class="mb-7 text-gray-600">Share your business profile and we will suggest the best permit priority map.</p>
        <div class="flex flex-wrap justify-center gap-3">
            <a href="{{ $waHref }}" class="btn btn-primary"><i class="fab fa-whatsapp"></i> WhatsApp — Get a Quote</a>
            <a href="{{ route('pma.inquiry.create') }}" class="btn btn-ghost"><i class="fas fa-robot"></i> AI Permit Checker</a>
        </div>
    </div>
</section>
@endsection
