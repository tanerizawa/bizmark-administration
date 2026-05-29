@extends('landing.layout')

@section('title', ($service['title'] ?? 'Service') . ' - Bizmark.ID')
@section('meta_description', $service['short_description'] ?? '')
@section('meta_keywords', $service['meta_keywords'] ?? 'Indonesia permits, compliance service')

@if(!empty($service['faq']))
@section('structured_data')
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@type": "FAQPage",
    "mainEntity": [
        @foreach($service['faq'] as $i => $faq)
        {
            "@@type": "Question",
            "name": {{ json_encode($faq['q']) }},
            "acceptedAnswer": { "@@type": "Answer", "text": {{ json_encode($faq['a']) }} }
        }{{ !$loop->last ? ',' : '' }}
        @endforeach
    ]
}
</script>
@endsection
@endif

@section('content')
@php
    $contact = (array) data_get(config('landing_metrics'), 'contact', []);
    $whatsapp = $contact['whatsapp_link'] ?? 'https://wa.me/6283879602855';
    $waText = 'Hello, I would like to discuss: ' . ($service['title'] ?? 'this service');
    $waHref = $whatsapp . (str_contains($whatsapp, '?') ? '&' : '?') . 'text=' . rawurlencode($waText);
@endphp

{{-- ── HERO ─────────────────────────────── --}}
<section class="relative overflow-hidden pt-28 pb-16 bg-[var(--bg-raised)] border-b border-gray-200">
    <div class="container-wide">
        <div class="max-w-4xl">
            <a href="{{ route('services.index.en') }}" class="link-primary text-sm inline-flex items-center gap-2 mb-6">
                <i class="fas fa-arrow-left text-xs"></i>Back to all services
            </a>
            <div class="flex items-start gap-4 mb-5">
                <div class="editorial-icon-badge flex-shrink-0" style="width:3.5rem;height:3.5rem;border-radius:.875rem;">
                    <i class="fas {{ $service['icon'] ?? 'fa-briefcase' }} icon-xl" aria-hidden="true"></i>
                </div>
                <div>
                    <span class="section-badge">{{ $service['category'] ?? 'Service' }}</span>
                    <h1 class="section-title mb-0">{{ $service['title'] }}</h1>
                </div>
            </div>
            <p class="section-description mb-6" style="margin-left:0;">{{ $service['short_description'] }}</p>
            <div class="flex flex-wrap gap-2 mb-7">
                @if(!empty($service['process_time']))
                    <span class="text-xs px-2 py-1 rounded-full bg-gray-100 border border-gray-200 text-gray-600">
                        <i class="fas fa-clock mr-1"></i>{{ $service['process_time'] }}
                    </span>
                @endif
                @if(!empty($service['price_range']))
                    <span class="text-xs px-2 py-1 rounded-full bg-amber-500/10 text-amber-700">
                        <i class="fas fa-tag mr-1"></i>{{ $service['price_range'] }}
                    </span>
                @endif
            </div>
            <div class="flex flex-wrap gap-3">
                <a href="{{ $waHref }}" target="_blank" rel="noopener noreferrer" class="btn btn-primary">
                    <i class="fab fa-whatsapp"></i> WhatsApp Consultation
                </a>
                <a href="{{ route('contact.index') }}" class="btn btn-primary">
                    <i class="fas fa-envelope"></i> Contact Our Team
                </a>
            </div>
        </div>
    </div>
</section>

{{-- ── KEY FEATURES ─────────────────────── --}}
@if(!empty($service['key_features']))
<section class="section-v2">
    <div class="container-wide">
        <div class="max-w-3xl mb-8">
            <span class="eyebrow mb-3">What's Included</span>
            <h2 class="display-md mt-2 mb-3">What this service covers</h2>
        </div>
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($service['key_features'] as $feature)
                <div class="premium-card flex items-start gap-3">
                    <span class="editorial-icon-badge flex-shrink-0 mt-0.5" style="width:2rem;height:2rem;border-radius:.5rem;">
                        <i class="fas fa-check icon-sm" aria-hidden="true"></i>
                    </span>
                    <span class="text-sm leading-relaxed text-gray-600">{{ $feature }}</span>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- ── SUB SERVICES ─────────────────────── --}}
@if(!empty($service['sub_services']))
<section class="section-v2 section-premium">
    <div class="container-wide">
        <div class="max-w-3xl mb-8">
            <span class="eyebrow mb-3">Package Scope</span>
            <h2 class="display-md mt-2 mb-3">What's included in this package</h2>
        </div>
        <div class="grid md:grid-cols-2 xl:grid-cols-3 gap-5">
            @foreach($service['sub_services'] as $subSlug => $subService)
                <article class="premium-card">
                    <h3 class="text-base font-semibold mb-2 text-gray-900 dark:text-white">{{ $subService['title'] }}</h3>
                    <p class="text-sm mb-4 flex-1 text-gray-600">{{ $subService['description'] ?? $subService['short_description'] ?? '' }}</p>
                    <div class="flex flex-wrap gap-2">
                        @if(!empty($subService['duration']))
                            <span class="text-xs px-2 py-1 rounded-full bg-gray-100 text-gray-600 border border-gray-200">{{ $subService['duration'] }}</span>
                        @endif
                        @if(!empty($subService['price']))
                            <span class="text-xs px-2 py-1 rounded-full bg-amber-500/10 text-amber-700">{{ $subService['price'] }}</span>
                        @endif
                    </div>
                </article>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- ── DOCUMENTS REQUIRED ───────────────── --}}
@if(!empty($service['documents_required']))
<section class="section-v2">
    <div class="container-wide">
        <div class="max-w-3xl mb-8">
            <span class="eyebrow mb-3">Preparation</span>
            <h2 class="display-md mt-2 mb-3">Documents you'll need to prepare</h2>
            <p style="color:var(--text-secondary);">Our team will guide you through each item — providing templates and format standards where needed.</p>
        </div>
        <div class="grid md:grid-cols-2 gap-3 max-w-4xl">
            @foreach($service['documents_required'] as $doc)
                <div class="flex items-start gap-3 p-3 rounded-lg bg-[var(--bg-raised)] border border-gray-200">
                    <i class="fas fa-file-alt mt-0.5 flex-shrink-0 text-amber-600"></i>
                    <span class="text-sm text-gray-600">{{ $doc }}</span>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- ── COMPARISON ───────────────────────── --}}
<section class="section-v2 section-premium">
    <div class="container-wide">
        <div class="text-center mb-8 max-w-2xl mx-auto">
            <span class="eyebrow mb-3">Why Bizmark.ID?</span>
            <h2 class="display-md mt-2 mb-3">Without Bizmark vs. With Bizmark</h2>
            <p class="text-gray-600">See the real difference in process speed, outcomes, and risk exposure.</p>
        </div>
        <div class="grid md:grid-cols-2 gap-5 max-w-3xl mx-auto">
            <div class="premium-card border border-red-200/30 bg-red-50/30">
                <div class="flex items-center gap-2 mb-4">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center bg-red-100/50">
                        <i class="fas fa-times text-sm text-red-400"></i>
                    </div>
                    <h3 class="font-bold text-base text-red-400">Without Bizmark</h3>
                </div>
                <ul class="space-y-2.5">
                    @foreach([
                        ['fa-clock', 'Slower process — independent research takes weeks, often with dead ends.'],
                        ['fa-exclamation-triangle', 'Document rejection risk due to incorrect format or missing requirements.'],
                        ['fa-search', 'No status visibility — uncertain when permits will be issued.'],
                        ['fa-money-bill-wave', 'Error costs can reach IDR 50–200M if a wrong step is taken.'],
                    ] as [$ico, $txt])
                    <li class="flex items-start gap-2.5 text-sm text-gray-600">
                        <i class="fas {{ $ico }} mt-0.5 flex-shrink-0 text-red-400"></i>
                        <span>{{ $txt }}</span>
                    </li>
                    @endforeach
                </ul>
            </div>
            <div class="premium-card border border-amber-200/30 bg-amber-50/30">
                <div class="flex items-center gap-2 mb-4">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center bg-amber-100/50">
                        <i class="fas fa-check text-sm text-amber-500"></i>
                    </div>
                    <h3 class="font-bold text-base text-amber-700">With Bizmark.ID</h3>
                </div>
                <ul class="space-y-2.5">
                    @foreach([
                        ['fa-bolt', 'Accelerated process — experienced team knows exact requirements and workflows.'],
                        ['fa-shield-halved', 'Documents prepared to standard — 96% approval rate across all clients.'],
                        ['fa-chart-line', 'Weekly SLA reports — you always know your permit status.'],
                        ['fa-hand-holding-usd', 'Staged payment: 50% upfront, 50% on permit issuance.'],
                    ] as [$ico, $txt])
                    <li class="flex items-start gap-2.5 text-sm text-gray-600">
                        <i class="fas {{ $ico }} mt-0.5 flex-shrink-0 text-amber-500"></i>
                        <span>{{ $txt }}</span>
                    </li>
                    @endforeach
                </ul>
                <div class="mt-4 pt-4 border-t border-amber-200/20">
                    <a href="{{ $waHref }}" target="_blank" rel="noopener noreferrer" class="btn btn-primary w-full btn-sm">
                        <i class="fab fa-whatsapp"></i> Start Free Consultation
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ── FAQ ───────────────────────────────── --}}
@if(!empty($service['faq']))
<section class="section-v2">
    <div class="container-wide">
        <div class="max-w-3xl mb-8">
            <span class="eyebrow mb-3">FAQ</span>
            <h2 class="display-md mt-2 mb-3">Frequently Asked Questions</h2>
        </div>
        <div class="max-w-3xl space-y-3">
            @foreach($service['faq'] as $faq)
                <details class="faq-item">
                    <summary class="faq-toggle">
                        <span>{{ $faq['q'] }}</span>
                        <i class="fas fa-chevron-down" aria-hidden="true"></i>
                    </summary>
                    <div class="faq-content">
                        <div class="faq-content-inner">{{ $faq['a'] }}</div>
                    </div>
                </details>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- ── FINAL CTA ─────────────────────────── --}}
<section class="section-v2-sm">
    <div class="container-wide text-center">
        <span class="blue-rule mx-auto mb-5"></span>
        <h2 class="display-md mb-4">Ready to move your permits forward?</h2>
        <p class="mb-7 max-w-xl mx-auto text-gray-600">Our team guides you from the first consultation through to permit issuance — on schedule, every time.</p>
        <div class="flex flex-wrap justify-center gap-3">
            <a href="{{ $waHref }}" target="_blank" rel="noopener noreferrer" class="btn btn-primary btn-lg">
                <i class="fab fa-whatsapp"></i> WhatsApp Consultation
            </a>
            <a href="{{ route('services.index.en') }}" class="btn btn-ghost btn-lg">
                <i class="fas fa-layer-group"></i> Explore More Services
            </a>
        </div>
        <p class="text-xs mt-5 text-gray-400">
            <i class="fas fa-shield-halved mr-1.5 text-amber-400"></i>
            ISO 9001:2015 · NDA available · 96% on-time delivery · Bilingual EN/ID
        </p>
    </div>
</section>

@endsection

