@extends('landing.layout')

@section('title', $pageData['meta_title'])
@section('meta_title', $pageData['meta_title'])
@section('meta_description', $pageData['meta_description'])
@section('meta_keywords', $pageData['meta_keywords'])
@section('og_title', $pageData['meta_title'])
@section('og_description', $pageData['meta_description'])

@section('content')

{{-- Breadcrumb Schema --}}
@php
$breadcrumbSchema = [
    '@context' => 'https://schema.org',
    '@type' => 'BreadcrumbList',
    'itemListElement' => [
        ['@type' => 'ListItem', 'position' => 1, 'name' => 'Beranda', 'item' => config('app.url')],
        ['@type' => 'ListItem', 'position' => 2, 'name' => 'Layanan', 'item' => config('app.url') . '/layanan'],
        ['@type' => 'ListItem', 'position' => 3, 'name' => 'Perbandingan', 'item' => config('app.url') . '/layanan/perbandingan'],
        ['@type' => 'ListItem', 'position' => 4, 'name' => $serviceA['title'] . ' vs ' . $serviceB['title'], 'item' => config('app.url') . '/layanan/perbandingan/' . $comparisonSlug],
    ],
];
@endphp
<script type="application/ld+json">{!! json_encode($breadcrumbSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>

{{-- FAQ Schema --}}
@php
$faqSchema = [
    '@context' => 'https://schema.org',
    '@type' => 'FAQPage',
    'mainEntity' => collect($pageData['faqs'])->map(fn($f) => [
        '@type' => 'Question',
        'name' => $f['question'],
        'acceptedAnswer' => ['@type' => 'Answer', 'text' => $f['answer']],
    ])->toArray(),
];
@endphp
<script type="application/ld+json">{!! json_encode($faqSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>

{{-- Breadcrumbs --}}
<section class="pt-24 pb-4 px-4 bg-white border-b border-gray-100">
    <div class="container mx-auto max-w-6xl">
        <nav class="flex flex-wrap items-center gap-2 text-sm text-gray-500">
            <a href="/" class="hover:text-primary transition"><i class="fas fa-home"></i></a>
            <span class="text-gray-300">/</span>
            <a href="{{ route('services.index.id') }}" class="hover:text-primary transition">Layanan</a>
            <span class="text-gray-300">/</span>
            <a href="{{ url('/layanan/perbandingan') }}" class="hover:text-primary transition">Perbandingan</a>
            <span class="text-gray-300">/</span>
            <span class="text-gray-900 font-medium">{{ $serviceA['title'] }} vs {{ $serviceB['title'] }}</span>
        </nav>
    </div>
</section>

{{-- Hero --}}
<section class="relative bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 text-white py-16 md:py-20 overflow-hidden">
    <div class="absolute inset-0 opacity-10">
        <div class="absolute top-0 right-0 w-96 h-96 bg-sky-500 rounded-full blur-3xl"></div>
        <div class="absolute bottom-0 left-0 w-96 h-96 bg-orange-500 rounded-full blur-3xl"></div>
    </div>
    <div class="container mx-auto max-w-4xl px-4 relative z-10 text-center">
        <div class="flex items-center justify-center gap-4 mb-6">
            <div class="w-14 h-14 rounded-xl flex items-center justify-center text-white text-xl" style="background-color: {{ $serviceA['color'] ?? '#0ea5e9' }}">
                <i class="fas {{ $serviceA['icon'] ?? 'fa-file-alt' }}"></i>
            </div>
            <span class="text-2xl font-bold text-gray-400">VS</span>
            <div class="w-14 h-14 rounded-xl flex items-center justify-center text-white text-xl" style="background-color: {{ $serviceB['color'] ?? '#f97316' }}">
                <i class="fas {{ $serviceB['icon'] ?? 'fa-file-alt' }}"></i>
            </div>
        </div>
        <h1 class="text-3xl md:text-4xl lg:text-5xl font-extrabold leading-tight mb-4">{{ $pageData['h1'] }}</h1>
        <p class="text-lg text-gray-300 max-w-2xl mx-auto">{{ $pageData['intro'] }}</p>
    </div>
</section>

{{-- Quick Summary Cards --}}
<section class="py-12 md:py-16 bg-white">
    <div class="container mx-auto max-w-6xl px-4">
        <div class="grid md:grid-cols-2 gap-6 mb-12">
            {{-- Service A --}}
            <div class="bg-white rounded-2xl border-2 p-6" style="border-color: {{ $serviceA['color'] ?? '#0ea5e9' }}20">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 rounded-lg flex items-center justify-center text-white" style="background-color: {{ $serviceA['color'] ?? '#0ea5e9' }}">
                        <i class="fas {{ $serviceA['icon'] ?? 'fa-file-alt' }}"></i>
                    </div>
                    <h2 class="text-xl font-bold text-gray-900">{{ $serviceA['title'] }}</h2>
                </div>
                <p class="text-gray-600 text-sm leading-relaxed mb-4">{{ $serviceA['long_description'] ?? $serviceA['short_description'] ?? '' }}</p>
                <a href="{{ route('services.show.id', $slugA) }}" class="text-sm font-medium text-sky-600 hover:text-sky-700">
                    Detail Layanan <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>

            {{-- Service B --}}
            <div class="bg-white rounded-2xl border-2 p-6" style="border-color: {{ $serviceB['color'] ?? '#f97316' }}20">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 rounded-lg flex items-center justify-center text-white" style="background-color: {{ $serviceB['color'] ?? '#f97316' }}">
                        <i class="fas {{ $serviceB['icon'] ?? 'fa-file-alt' }}"></i>
                    </div>
                    <h2 class="text-xl font-bold text-gray-900">{{ $serviceB['title'] }}</h2>
                </div>
                <p class="text-gray-600 text-sm leading-relaxed mb-4">{{ $serviceB['long_description'] ?? $serviceB['short_description'] ?? '' }}</p>
                <a href="{{ route('services.show.id', $slugB) }}" class="text-sm font-medium text-sky-600 hover:text-sky-700">
                    Detail Layanan <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>
        </div>

        {{-- Comparison Table --}}
        <h2 class="text-2xl font-bold text-gray-900 mb-6 text-center">Tabel Perbandingan</h2>
        <div class="overflow-x-auto mb-12">
            <table class="w-full border-collapse rounded-xl overflow-hidden">
                <thead>
                    <tr class="bg-slate-900 text-white">
                        <th class="px-6 py-4 text-left text-sm font-semibold">Aspek</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold">{{ $serviceA['title'] }}</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold">{{ $serviceB['title'] }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pageData['comparison_table'] as $i => $row)
                    <tr class="{{ $i % 2 === 0 ? 'bg-gray-50' : 'bg-white' }}">
                        <td class="px-6 py-4 font-medium text-gray-900 text-sm">{{ $row['aspect'] }}</td>
                        <td class="px-6 py-4 text-gray-600 text-sm">{{ $row['a'] }}</td>
                        <td class="px-6 py-4 text-gray-600 text-sm">{{ $row['b'] }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- When to Choose --}}
        <div class="grid md:grid-cols-2 gap-6 mb-12">
            <div class="bg-sky-50 rounded-2xl p-6 border border-sky-100">
                <h3 class="text-lg font-bold text-gray-900 mb-3">
                    <i class="fas fa-check-circle text-sky-600 mr-2"></i> Pilih {{ $serviceA['title'] }} Jika:
                </h3>
                <ul class="space-y-2 text-sm text-gray-700">
                    <li class="flex items-start gap-2"><i class="fas fa-chevron-right text-sky-500 mt-1 text-xs"></i> Usaha Anda memerlukan {{ strtolower($serviceA['short_description'] ?? 'perizinan khusus') }}</li>
                    <li class="flex items-start gap-2"><i class="fas fa-chevron-right text-sky-500 mt-1 text-xs"></i> Kategori usaha termasuk {{ strtolower($serviceA['category'] ?? 'perizinan') }}</li>
                    <li class="flex items-start gap-2"><i class="fas fa-chevron-right text-sky-500 mt-1 text-xs"></i> Anda membutuhkan dokumen resmi untuk operasional</li>
                </ul>
            </div>
            <div class="bg-orange-50 rounded-2xl p-6 border border-orange-100">
                <h3 class="text-lg font-bold text-gray-900 mb-3">
                    <i class="fas fa-check-circle text-orange-600 mr-2"></i> Pilih {{ $serviceB['title'] }} Jika:
                </h3>
                <ul class="space-y-2 text-sm text-gray-700">
                    <li class="flex items-start gap-2"><i class="fas fa-chevron-right text-orange-500 mt-1 text-xs"></i> Usaha Anda memerlukan {{ strtolower($serviceB['short_description'] ?? 'perizinan khusus') }}</li>
                    <li class="flex items-start gap-2"><i class="fas fa-chevron-right text-orange-500 mt-1 text-xs"></i> Kategori usaha termasuk {{ strtolower($serviceB['category'] ?? 'perizinan') }}</li>
                    <li class="flex items-start gap-2"><i class="fas fa-chevron-right text-orange-500 mt-1 text-xs"></i> Anda membutuhkan dokumen resmi untuk operasional</li>
                </ul>
            </div>
        </div>
    </div>
</section>

{{-- FAQ --}}
<section class="py-12 md:py-16 bg-gray-50">
    <div class="container mx-auto max-w-4xl px-4">
        <h2 class="text-2xl font-bold text-gray-900 text-center mb-8">FAQ: {{ $serviceA['title'] }} vs {{ $serviceB['title'] }}</h2>
        <div class="space-y-3">
            @foreach($pageData['faqs'] as $i => $faq)
            <details class="group bg-white rounded-xl border border-gray-200 overflow-hidden" {{ $i === 0 ? 'open' : '' }}>
                <summary class="flex items-center justify-between p-5 cursor-pointer hover:bg-gray-50 transition">
                    <h3 class="font-semibold text-gray-900 pr-4">{{ $faq['question'] }}</h3>
                    <i class="fas fa-chevron-down text-gray-400 group-open:rotate-180 transition-transform flex-shrink-0"></i>
                </summary>
                <div class="px-5 pb-5 text-gray-600 leading-relaxed border-t border-gray-100 pt-4">
                    {{ $faq['answer'] }}
                </div>
            </details>
            @endforeach
        </div>
    </div>
</section>

{{-- Related Articles --}}
@if($relatedArticles->count() > 0)
<section class="py-12 md:py-16 bg-white">
    <div class="container mx-auto max-w-6xl px-4">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">Artikel Terkait</h2>
        <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-5">
            @foreach($relatedArticles as $article)
            <a href="{{ route('blog.article.id', $article->slug) }}" class="group block bg-white rounded-xl border border-gray-200 overflow-hidden hover:shadow-md transition">
                @if($article->featured_image)
                <div class="aspect-video overflow-hidden">
                    <img src="{{ asset('storage/' . $article->featured_image) }}" alt="{{ $article->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" loading="lazy">
                </div>
                @endif
                <div class="p-4">
                    <h3 class="font-semibold text-gray-900 text-sm leading-snug line-clamp-2 group-hover:text-primary transition">{{ $article->title }}</h3>
                    <p class="text-xs text-gray-500 mt-2">{{ $article->reading_time }} menit baca</p>
                </div>
            </a>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- Other Comparisons --}}
@if($otherComparisons->count() > 0)
<section class="py-12 md:py-16 bg-gray-50">
    <div class="container mx-auto max-w-6xl px-4">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">Perbandingan Lainnya</h2>
        <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4">
            @foreach($otherComparisons as $slug => $comp)
            <a href="{{ url('/layanan/perbandingan/' . $slug) }}"
               class="block bg-white rounded-xl border border-gray-200 p-4 hover:border-sky-300 hover:shadow-md transition text-center">
                <div class="flex items-center justify-center gap-2 mb-2">
                    <i class="fas {{ $comp['a']['icon'] ?? 'fa-file-alt' }} text-gray-500"></i>
                    <span class="text-xs font-bold text-gray-400">VS</span>
                    <i class="fas {{ $comp['b']['icon'] ?? 'fa-file-alt' }} text-gray-500"></i>
                </div>
                <p class="text-sm font-medium text-gray-900">{{ $comp['a']['title'] }} vs {{ $comp['b']['title'] }}</p>
            </a>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- CTA --}}
<section class="py-16 bg-gradient-to-r from-slate-900 to-slate-800 text-white text-center">
    <div class="container mx-auto max-w-3xl px-4">
        <h2 class="text-2xl md:text-3xl font-bold mb-4">Butuh Bantuan Memilih?</h2>
        <p class="text-gray-300 mb-8">Tim ahli kami siap membantu menentukan perizinan yang tepat untuk usaha Anda. Konsultasi gratis!</p>
        <a href="https://wa.me/6283879602855?text={{ urlencode('Halo Bizmark, saya ingin konsultasi tentang perbedaan ' . $serviceA['title'] . ' dan ' . $serviceB['title']) }}" target="_blank" rel="noopener"
           class="inline-flex items-center px-8 py-4 bg-green-600 hover:bg-green-700 text-white text-lg font-semibold rounded-xl transition shadow-lg shadow-green-500/20">
            <i class="fab fa-whatsapp mr-3 text-xl"></i> Konsultasi Gratis
        </a>
    </div>
</section>

@endsection
