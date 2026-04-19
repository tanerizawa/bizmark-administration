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
@php
    $contact = (array) data_get(config('landing_metrics'), 'contact', []);
    $whatsapp = $contact['whatsapp_link'] ?? 'https://wa.me/6283879602855';
    $waText = 'Halo Bizmark, saya ingin konsultasi tentang perbedaan ' . ($serviceA['title'] ?? '') . ' dan ' . ($serviceB['title'] ?? '');
    $waHref = $whatsapp . (str_contains($whatsapp, '?') ? '&' : '?') . 'text=' . rawurlencode($waText);
@endphp

<section class="relative overflow-hidden pt-28 pb-16" style="background:linear-gradient(135deg,var(--surface-warm) 0%, var(--surface-cool) 100%);">
    <div class="container-wide text-center">
        <a href="{{ url('/layanan/perbandingan') }}" class="link-primary text-sm inline-flex items-center mb-5"><i class="fas fa-arrow-left mr-2"></i>Kembali ke Perbandingan</a>
        <div class="flex items-center justify-center gap-4 mb-6">
            <div class="w-14 h-14 rounded-xl flex items-center justify-center text-white text-xl" style="background-color: {{ $serviceA['color'] ?? '#0f172a' }}">
                <i class="fas {{ $serviceA['icon'] ?? 'fa-file-alt' }}"></i>
            </div>
            <span class="text-sm" style="color:var(--text-tertiary);font-weight:900;letter-spacing:.12em;">VS</span>
            <div class="w-14 h-14 rounded-xl flex items-center justify-center text-white text-xl" style="background-color: {{ $serviceB['color'] ?? '#0f172a' }}">
                <i class="fas {{ $serviceB['icon'] ?? 'fa-file-alt' }}"></i>
            </div>
        </div>
        <h1 class="section-title mb-4">{{ $pageData['h1'] }}</h1>
        <p class="section-description" style="margin-left:auto;margin-right:auto;">{{ $pageData['intro'] }}</p>
    </div>
</section>

<section class="section">
    <div class="container-wide">
        <div class="grid md:grid-cols-2 gap-6 mb-10">
            <div class="card" style="border-color: {{ $serviceA['color'] ?? '#0f172a' }}35;">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 rounded-lg flex items-center justify-center text-white" style="background-color: {{ $serviceA['color'] ?? '#0f172a' }}">
                        <i class="fas {{ $serviceA['icon'] ?? 'fa-file-alt' }}"></i>
                    </div>
                    <h2 class="text-xl font-bold mb-0" style="color:var(--text-primary);">{{ $serviceA['title'] }}</h2>
                </div>
                <p class="text-sm mb-4" style="color:var(--text-secondary);">{{ $serviceA['long_description'] ?? $serviceA['short_description'] ?? '' }}</p>
                <a href="{{ route('services.show.id', $slugA) }}" class="link-primary text-sm inline-flex items-center">Detail Layanan <i class="fas fa-arrow-right ml-2"></i></a>
            </div>

            <div class="card" style="border-color: {{ $serviceB['color'] ?? '#0f172a' }}35;">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 rounded-lg flex items-center justify-center text-white" style="background-color: {{ $serviceB['color'] ?? '#0f172a' }}">
                        <i class="fas {{ $serviceB['icon'] ?? 'fa-file-alt' }}"></i>
                    </div>
                    <h2 class="text-xl font-bold mb-0" style="color:var(--text-primary);">{{ $serviceB['title'] }}</h2>
                </div>
                <p class="text-sm mb-4" style="color:var(--text-secondary);">{{ $serviceB['long_description'] ?? $serviceB['short_description'] ?? '' }}</p>
                <a href="{{ route('services.show.id', $slugB) }}" class="link-primary text-sm inline-flex items-center">Detail Layanan <i class="fas fa-arrow-right ml-2"></i></a>
            </div>
        </div>

        <div class="card mb-10">
            <h2 class="text-xl font-bold mb-4" style="color:var(--text-primary);">Tabel Perbandingan</h2>
            <div class="content-prose">
                <div style="overflow:auto;">
                    <table>
                        <thead>
                            <tr>
                                <th>Aspek</th>
                                <th>{{ $serviceA['title'] }}</th>
                                <th>{{ $serviceB['title'] }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pageData['comparison_table'] as $row)
                                <tr>
                                    <td><strong>{{ $row['aspect'] }}</strong></td>
                                    <td>{{ $row['a'] }}</td>
                                    <td>{{ $row['b'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="grid md:grid-cols-2 gap-6 mb-10">
            <div class="card">
                <h3 class="text-lg font-bold mb-3" style="color:var(--text-primary);">Pilih {{ $serviceA['title'] }} Jika:</h3>
                <div class="content-prose">
                    <ul>
                        <li>Usaha Anda memerlukan {{ strtolower($serviceA['short_description'] ?? 'perizinan khusus') }}</li>
                        <li>Kategori usaha termasuk {{ strtolower($serviceA['category'] ?? 'perizinan') }}</li>
                        <li>Anda membutuhkan dokumen resmi untuk operasional</li>
                    </ul>
                </div>
            </div>
            <div class="card">
                <h3 class="text-lg font-bold mb-3" style="color:var(--text-primary);">Pilih {{ $serviceB['title'] }} Jika:</h3>
                <div class="content-prose">
                    <ul>
                        <li>Usaha Anda memerlukan {{ strtolower($serviceB['short_description'] ?? 'perizinan khusus') }}</li>
                        <li>Kategori usaha termasuk {{ strtolower($serviceB['category'] ?? 'perizinan') }}</li>
                        <li>Anda membutuhkan dokumen resmi untuk operasional</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="section-sm" style="background:var(--surface-secondary);">
    <div class="container-wide">
        <h2 class="section-title mb-6" style="font-size:clamp(1.35rem,2.4vw,1.8rem);">FAQ: {{ $serviceA['title'] }} vs {{ $serviceB['title'] }}</h2>
        <div class="max-w-4xl space-y-3">
            @foreach($pageData['faqs'] as $i => $faq)
                <details class="faq-item" {{ $i === 0 ? 'open' : '' }}>
                    <summary class="faq-toggle">
                        <span>{{ $faq['question'] }}</span>
                        <i class="fas fa-chevron-down" aria-hidden="true"></i>
                    </summary>
                    <div class="faq-content">
                        <div class="faq-content-inner">{{ $faq['answer'] }}</div>
                    </div>
                </details>
            @endforeach
        </div>
    </div>
</section>

{{-- Related Articles --}}
@if($relatedArticles->count() > 0)
<section class="section-sm">
    <div class="container-wide">
        <h2 class="section-title mb-6" style="font-size:clamp(1.35rem,2.4vw,1.8rem);">Artikel Terkait</h2>
        <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($relatedArticles as $article)
                <a href="{{ route('blog.article.id', $article->slug) }}" class="card overflow-hidden p-0">
                    @if($article->featured_image)
                        <img src="{{ asset('storage/' . $article->featured_image) }}" alt="{{ $article->title }}" class="w-full" style="height:160px;object-fit:cover;" loading="lazy">
                    @endif
                    <div style="padding:1.25rem;">
                        <h3 class="text-sm font-bold mb-2 card-title" style="color:var(--text-primary);">{{ $article->title }}</h3>
                        <p class="text-xs mb-0" style="color:var(--text-tertiary);">{{ $article->reading_time }} menit baca</p>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- Other Comparisons --}}
@if($otherComparisons->count() > 0)
<section class="section-sm" style="background:var(--surface-secondary);">
    <div class="container-wide">
        <h2 class="section-title mb-6" style="font-size:clamp(1.35rem,2.4vw,1.8rem);">Perbandingan Lainnya</h2>
        <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($otherComparisons as $slug => $comp)
                <a href="{{ url('/layanan/perbandingan/' . $slug) }}" class="card text-center">
                    <div class="flex items-center justify-center gap-2 mb-3">
                        <i class="fas {{ $comp['a']['icon'] ?? 'fa-file-alt' }}" style="color:var(--text-tertiary);"></i>
                        <span class="text-xs" style="color:var(--text-tertiary);font-weight:800;letter-spacing:.08em;">VS</span>
                        <i class="fas {{ $comp['b']['icon'] ?? 'fa-file-alt' }}" style="color:var(--text-tertiary);"></i>
                    </div>
                    <p class="text-sm font-semibold mb-0" style="color:var(--text-primary);">{{ $comp['a']['title'] }} vs {{ $comp['b']['title'] }}</p>
                </a>
            @endforeach
        </div>
    </div>
</section>
@endif

<section class="section-sm" style="background:var(--surface-dark);">
    <div class="container-wide text-center">
        <h2 class="text-white mb-3" style="font-size:clamp(1.5rem,3vw,2.1rem);font-weight:750;">Butuh Bantuan Memilih?</h2>
        <p class="mb-7" style="color:rgba(255,255,255,.74);">Tim ahli kami siap membantu menentukan perizinan yang tepat untuk usaha Anda.</p>
        <div class="flex flex-wrap justify-center gap-3">
            <a href="{{ $waHref }}" target="_blank" rel="noopener" class="btn btn-success"><i class="fab fa-whatsapp"></i> Konsultasi via WhatsApp</a>
            <a href="{{ route('contact.index') }}" class="btn btn-secondary"><i class="fas fa-envelope"></i> Hubungi Tim</a>
        </div>
    </div>
</section>

@endsection
