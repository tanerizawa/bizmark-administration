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

{{-- Breadcrumbs --}}
<section class="pt-24 pb-4 px-4 bg-white border-b border-gray-100">
    <div class="container mx-auto max-w-6xl">
        <nav class="flex flex-wrap items-center gap-2 text-sm text-gray-500">
            <a href="/" class="hover:text-primary transition"><i class="fas fa-home"></i></a>
            <span class="text-gray-300">/</span>
            <a href="{{ url('/faq') }}" class="hover:text-primary transition">FAQ</a>
            <span class="text-gray-300">/</span>
            <span class="text-gray-900 font-medium">{{ $topic['title'] }}</span>
        </nav>
    </div>
</section>

{{-- Hero --}}
<section class="py-12 md:py-16 bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 text-white">
    <div class="container mx-auto max-w-4xl px-4 text-center">
        <div class="w-14 h-14 rounded-xl bg-sky-500/20 text-sky-300 flex items-center justify-center mx-auto mb-4">
            <i class="fas {{ $topic['icon'] }} text-2xl"></i>
        </div>
        <h1 class="text-3xl md:text-4xl font-extrabold mb-4">FAQ: {{ $topic['title'] }}</h1>
        <p class="text-lg text-gray-300 max-w-2xl mx-auto">{{ $topic['description'] }}</p>
    </div>
</section>

{{-- FAQ List --}}
<section class="py-12 md:py-16 bg-white">
    <div class="container mx-auto max-w-4xl px-4">
        @if(count($faqs) > 0)
        <div class="space-y-3">
            @foreach($faqs as $i => $faq)
            <details class="group bg-white rounded-xl border border-gray-200 overflow-hidden" {{ $i < 3 ? 'open' : '' }}>
                <summary class="flex items-center justify-between p-5 cursor-pointer hover:bg-gray-50 transition">
                    <h2 class="font-semibold text-gray-900 pr-4 text-base">{{ $faq['question'] }}</h2>
                    <i class="fas fa-chevron-down text-gray-400 group-open:rotate-180 transition-transform flex-shrink-0"></i>
                </summary>
                <div class="px-5 pb-5 border-t border-gray-100 pt-4">
                    <p class="text-gray-600 leading-relaxed mb-3">{{ $faq['answer'] }}</p>
                    @if(!empty($faq['source_article']))
                    <a href="{{ $faq['source_article']['url'] }}" class="inline-flex items-center text-sm text-sky-600 hover:text-sky-700 font-medium">
                        <i class="fas fa-book-open mr-1.5"></i> Baca selengkapnya: {{ Str::limit($faq['source_article']['title'], 60) }}
                    </a>
                    @endif
                </div>
            </details>
            @endforeach
        </div>
        @else
        <div class="text-center py-12">
            <i class="fas fa-question-circle text-gray-300 text-4xl mb-4"></i>
            <p class="text-gray-500">Belum ada FAQ untuk topik ini. Hubungi kami untuk bertanya langsung.</p>
        </div>
        @endif
    </div>
</section>

{{-- Other Topics --}}
<section class="py-12 md:py-16 bg-gray-50">
    <div class="container mx-auto max-w-6xl px-4">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">Topik FAQ Lainnya</h2>
        <div class="grid sm:grid-cols-3 gap-4">
            @foreach($otherTopics as $slug => $other)
            <a href="{{ url('/faq/' . $other['slug']) }}"
               class="block bg-white rounded-xl border border-gray-200 p-5 hover:border-sky-300 hover:shadow-md transition">
                <div class="flex items-center gap-3 mb-2">
                    <i class="fas {{ $other['icon'] }} text-sky-500"></i>
                    <h3 class="font-semibold text-gray-900">{{ $other['title'] }}</h3>
                </div>
                <p class="text-sm text-gray-500">{{ Str::limit($other['description'], 80) }}</p>
            </a>
            @endforeach
        </div>
    </div>
</section>

{{-- CTA --}}
<section class="py-16 bg-gradient-to-r from-slate-900 to-slate-800 text-white text-center">
    <div class="container mx-auto max-w-3xl px-4">
        <h2 class="text-2xl md:text-3xl font-bold mb-4">Punya Pertanyaan Lain?</h2>
        <p class="text-gray-300 mb-8">Konsultasi gratis dengan tim ahli kami. Kami siap membantu menjawab semua pertanyaan seputar perizinan.</p>
        <a href="https://wa.me/6283879602855?text={{ urlencode('Halo Bizmark, saya memiliki pertanyaan tentang ' . $topic['title']) }}" target="_blank" rel="noopener"
           class="inline-flex items-center px-8 py-4 bg-green-600 hover:bg-green-700 text-white text-lg font-semibold rounded-xl transition shadow-lg shadow-green-500/20">
            <i class="fab fa-whatsapp mr-3 text-xl"></i> Tanya Langsung
        </a>
    </div>
</section>

@endsection
