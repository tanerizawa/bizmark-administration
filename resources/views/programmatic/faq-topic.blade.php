@extends('landing.layout')

@section('title', 'FAQ ' . $topic['title'] . ' ' . $year . ' | Bizmark.ID')
@section('meta_title', 'FAQ ' . $topic['title'] . ' ' . $year . ' - Pertanyaan & Jawaban | Bizmark.ID')
@section('meta_description', $topic['description'])
@section('meta_keywords', 'faq ' . strtolower($topic['title']) . ', pertanyaan ' . strtolower($topic['title']) . ', tanya jawab ' . strtolower($topic['title']))

@section('content')

{{-- FAQ Schema --}}
@if(count($faqs) >= 2)
@php
$faqSchema = [
    '@context' => 'https://schema.org',
    '@type' => 'FAQPage',
    'mainEntity' => collect($faqs)->map(fn($f) => [
        '@type' => 'Question',
        'name' => $f['question'],
        'acceptedAnswer' => ['@type' => 'Answer', 'text' => $f['answer']],
    ])->toArray(),
];
@endphp
<script type="application/ld+json">{!! json_encode($faqSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
@endif

{{-- Breadcrumb Schema --}}
@php
$breadcrumbSchema = [
    '@context' => 'https://schema.org',
    '@type' => 'BreadcrumbList',
    'itemListElement' => [
        ['@type' => 'ListItem', 'position' => 1, 'name' => 'Beranda', 'item' => config('app.url')],
        ['@type' => 'ListItem', 'position' => 2, 'name' => 'FAQ', 'item' => config('app.url') . '/faq'],
        ['@type' => 'ListItem', 'position' => 3, 'name' => $topic['title'], 'item' => config('app.url') . '/faq/' . $topicSlug],
    ],
];
@endphp
<script type="application/ld+json">{!! json_encode($breadcrumbSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
@php
    $contact = (array) data_get(config('landing_metrics'), 'contact', []);
    $whatsapp = $contact['whatsapp_link'] ?? 'https://wa.me/6283879602855';
    $waText = 'Halo Bizmark, saya memiliki pertanyaan tentang ' . ($topic['title'] ?? '');
    $waHref = $whatsapp . (str_contains($whatsapp, '?') ? '&' : '?') . 'text=' . rawurlencode($waText);
@endphp

<section class="relative overflow-hidden pt-28 pb-16" style="background:linear-gradient(135deg,var(--surface-warm) 0%, var(--surface-cool) 100%);">
    <div class="container-wide">
        <a href="{{ route('faq.index') }}" class="link-primary text-sm inline-flex items-center mb-5"><i class="fas fa-arrow-left mr-2"></i>Kembali ke FAQ</a>
        <div class="max-w-4xl">
            <span class="section-badge mb-4">FAQ</span>
            <h1 class="section-title mb-4">FAQ: {{ $topic['title'] }}</h1>
            <p class="section-description" style="margin-left:0;">{{ $topic['description'] }}</p>
        </div>
    </div>
</section>

<section class="section">
    <div class="container-wide">
        @if(count($faqs) > 0)
            <div class="max-w-4xl space-y-3">
                @foreach($faqs as $i => $faq)
                    <details class="faq-item" {{ $i < 3 ? 'open' : '' }}>
                        <summary class="faq-toggle">
                            <span>{{ $faq['question'] }}</span>
                            <i class="fas fa-chevron-down" aria-hidden="true"></i>
                        </summary>
                        <div class="faq-content">
                            <div class="faq-content-inner content-prose">
                                <p>{{ $faq['answer'] }}</p>
                                @if(!empty($faq['source_article']))
                                    <p><a href="{{ $faq['source_article']['url'] }}">Baca selengkapnya: {{ Str::limit($faq['source_article']['title'], 60) }}</a></p>
                                @endif
                            </div>
                        </div>
                    </details>
                @endforeach
            </div>
        @else
            <div class="card text-center">
                <h2 class="text-lg font-bold mb-2" style="color:var(--text-primary);">Belum ada FAQ untuk topik ini</h2>
                <p class="mb-0" style="color:var(--text-secondary);">Hubungi kami untuk bertanya langsung.</p>
            </div>
        @endif
    </div>
</section>

<section class="section-sm" style="background:var(--surface-secondary);">
    <div class="container-wide">
        <h2 class="section-title mb-6" style="font-size:clamp(1.35rem,2.4vw,1.8rem);">Topik FAQ Lainnya</h2>
        <div class="grid sm:grid-cols-3 gap-6">
            @foreach($otherTopics as $slug => $other)
                <a href="{{ url('/faq/' . $other['slug']) }}" class="card">
                    <div class="flex items-center gap-3 mb-2">
                        <span class="w-10 h-10 rounded-xl flex items-center justify-center" style="background:rgba(14,165,233,.12);color:var(--color-accent);">
                            <i class="fas {{ $other['icon'] ?? 'fa-circle-question' }}"></i>
                        </span>
                        <h3 class="text-base font-bold mb-0" style="color:var(--text-primary);">{{ $other['title'] }}</h3>
                    </div>
                    <p class="text-sm mb-0" style="color:var(--text-secondary);">{{ Str::limit($other['description'], 96) }}</p>
                </a>
            @endforeach
        </div>
    </div>
</section>

<section class="section-sm" style="background:var(--surface-dark);">
    <div class="container-wide text-center">
        <h2 class="text-white mb-3" style="font-size:clamp(1.5rem,3vw,2.1rem);font-weight:750;">Punya Pertanyaan Lain?</h2>
        <p class="mb-7" style="color:rgba(255,255,255,.74);">Konsultasi gratis dengan tim ahli kami.</p>
        <div class="flex flex-wrap justify-center gap-3">
            <a href="{{ $waHref }}" target="_blank" rel="noopener" class="btn btn-success"><i class="fab fa-whatsapp"></i> Tanya via WhatsApp</a>
            <a href="{{ route('contact.index') }}" class="btn btn-secondary"><i class="fas fa-envelope"></i> Hubungi Tim</a>
        </div>
    </div>
</section>

@endsection
