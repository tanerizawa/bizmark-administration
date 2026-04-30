@extends('landing.layout')

@section('title', $seo['title'])
@section('meta_title', $seo['title'])
@section('meta_description', $seo['description'])
@section('meta_keywords', $seo['keywords'])

@section('content')
@php
    $contact = (array) data_get(config('landing_metrics'), 'contact', []);
    $whatsapp = $contact['whatsapp_link'] ?? 'https://wa.me/6283879602855';
    $waText = 'Halo, saya ingin konsultasi terkait ' . ($cluster->pillar_title ?? '');
    $waHref = $whatsapp . (str_contains($whatsapp, '?') ? '&' : '?') . 'text=' . rawurlencode($waText);
    $serviceHref = !empty($cluster->service_slug) ? route('services.show.id', $cluster->service_slug) : null;
@endphp

<section class="relative overflow-hidden pt-28 pb-16" style="background:linear-gradient(135deg,var(--surface-warm) 0%, var(--surface-cool) 100%);">
    <div class="container-wide">
        <span class="section-badge mb-4">Panduan</span>
        <h1 class="section-title mb-4">{{ $cluster->pillar_title }}</h1>
        <p class="section-description mb-8" style="margin-left:0;">{{ $cluster->pillar_description }}</p>
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('pillar.index') }}" class="btn btn-outline-primary"><i class="fas fa-arrow-left"></i> Semua Panduan</a>
            @if($serviceHref)
                <a href="{{ $serviceHref }}" class="btn btn-primary"><i class="fas {{ $service['icon'] ?? 'fa-layer-group' }}"></i> Lihat Layanan</a>
            @endif
        </div>
    </div>
</section>

<section class="section-sm" style="background:var(--surface-secondary);">
    <div class="container-wide">
        <div class="card">
            <div class="flex items-center justify-between flex-wrap gap-3 mb-4">
                <h2 class="text-lg font-bold mb-0" style="color:var(--text-primary);"><i class="fas fa-list-ol mr-2"></i>Daftar Isi</h2>
                <div class="flex flex-wrap gap-2">
                    <span class="chip"><i class="fas fa-check-circle"></i> Artikel tersedia</span>
                    <span class="chip"><i class="fas fa-clock"></i> Segera hadir</span>
                </div>
            </div>
            <div class="grid sm:grid-cols-2 gap-2">
                @foreach($subtopics as $i => $subtopic)
                    <a href="#subtopic-{{ $i }}" class="chip">
                        <span style="font-variant-numeric: tabular-nums;">{{ $i + 1 }}.</span>
                        <span>{{ $subtopic['title'] }}</span>
                        <span class="ml-auto" style="color:var(--text-tertiary);">
                            <i class="fas {{ $subtopic['has_article'] ? 'fa-check' : 'fa-minus' }}"></i>
                        </span>
                    </a>
                @endforeach
            </div>
        </div>
    </div>
</section>

<section class="section">
    <div class="container-wide space-y-6">
        @foreach($subtopics as $i => $subtopic)
            <div id="subtopic-{{ $i }}" class="scroll-mt-24">
                <article class="card">
                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 rounded-xl flex items-center justify-center" style="background:{{ $subtopic['has_article'] ? 'rgba(22,163,74,.12)' : 'rgba(148,163,184,.18)' }};color:{{ $subtopic['has_article'] ? 'var(--color-success)' : 'var(--text-tertiary)' }};font-weight:800;">
                            {{ $i + 1 }}
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="flex flex-wrap items-center gap-2 mb-2">
                                <span class="chip">{{ ucfirst($subtopic['priority']) }}</span>
                                <span class="chip">{{ ucfirst($subtopic['type']) }}</span>
                            </div>
                            <h3 class="text-lg font-bold mb-2" style="color:var(--text-primary);">{{ $subtopic['title'] }}</h3>
                            @if($subtopic['has_article'])
                                <p class="text-sm mb-3" style="color:var(--text-secondary);">{{ $subtopic['article']->excerpt }}</p>
                                <a href="{{ $subtopic['article']->getUrl() }}" class="link-primary text-sm inline-flex items-center">Baca selengkapnya <i class="fas fa-arrow-right ml-2"></i></a>
                            @else
                                <p class="text-sm mb-0" style="color:var(--text-tertiary);">Artikel untuk topik ini sedang dalam tahap persiapan.</p>
                            @endif
                        </div>
                    </div>
                </article>
            </div>
        @endforeach
    </div>
</section>

{{-- Related Articles --}}
@if($articles->count() > 0)
<section class="section-sm" style="background:var(--surface-secondary);">
    <div class="container-wide">
        <h2 class="section-title mb-6" style="font-size:clamp(1.35rem,2.4vw,1.8rem);">Artikel Terkait</h2>
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($articles->take(6) as $article)
                <a href="{{ $article->getUrl() }}" class="card overflow-hidden p-0">
                    @if($article->featured_image)
                        <img src="{{ $article->featured_image }}" alt="{{ $article->title }}" class="w-full" style="height:160px;object-fit:cover;" loading="lazy">
                    @endif
                    <div style="padding:1.25rem;">
                        <h3 class="text-base font-bold mb-2 card-title" style="color:var(--text-primary);">{{ $article->title }}</h3>
                        <p class="text-sm mb-0" style="color:var(--text-secondary);">{{ $article->excerpt }}</p>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- Keywords Cloud --}}
@if($keywordClusters->count() > 0)
<section class="section-sm">
    <div class="container-wide">
        <h2 class="section-title mb-6" style="font-size:clamp(1.35rem,2.4vw,1.8rem);">Kata Kunci Terkait</h2>
        <div class="flex flex-wrap gap-2">
            @foreach($keywordClusters->flatMap(fn($kc) => $kc->long_tail_keywords ?? $kc->keywords ?? [])->unique()->take(30) as $keyword)
                <span class="chip">{{ $keyword }}</span>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- Related Pillars --}}
@if($relatedClusters->count() > 0)
<section class="section-sm" style="background:var(--surface-secondary);">
    <div class="container-wide">
        <h2 class="section-title mb-6" style="font-size:clamp(1.35rem,2.4vw,1.8rem);">Panduan Lainnya</h2>
        <div class="grid sm:grid-cols-2 gap-6">
            @foreach($relatedClusters as $rc)
                <a href="{{ url('/panduan/' . $rc->pillar_slug) }}" class="card flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center" style="background:rgba(15,23,42,.06);color:var(--text-primary);">
                        <i class="fas fa-book"></i>
                    </div>
                    <div class="min-w-0">
                        <h3 class="text-base font-bold mb-1" style="color:var(--text-primary);">{{ $rc->pillar_title }}</h3>
                        <p class="text-sm mb-0" style="color:var(--text-secondary);">{{ count($rc->subtopics ?? []) }} subtopik</p>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
</section>
@endif

<section class="section-sm" style="background:var(--surface-dark);">
    <div class="container-wide text-center">
        <h2 class="text-white mb-3" style="font-size:clamp(1.5rem,3vw,2.1rem);font-weight:750;">Butuh Bantuan Pengurusan?</h2>
        <p class="mb-7" style="color:rgba(255,255,255,.74);">Ceritakan kebutuhan Anda, tim kami bantu susun langkah dan dokumen yang diperlukan.</p>
        <div class="flex flex-wrap justify-center gap-3">
            <a href="{{ $waHref }}" target="_blank" rel="noopener" class="btn btn-success"><i class="fab fa-whatsapp"></i> Konsultasi via WhatsApp</a>
            <a href="{{ route('contact.index') }}" class="btn btn-secondary"><i class="fas fa-envelope"></i> Hubungi Tim</a>
        </div>
    </div>
</section>

{{-- Schemas --}}
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@type": "BreadcrumbList",
    "itemListElement": [
        {"@@type": "ListItem", "position": 1, "name": "Home", "item": "{{ url('/') }}"},
        {"@@type": "ListItem", "position": 2, "name": "Panduan", "item": "{{ url('/panduan') }}"},
        {"@@type": "ListItem", "position": 3, "name": "{{ $cluster->pillar_title }}"}
    ]
}
</script>

<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@type": "Article",
    "headline": "{{ $cluster->pillar_title }}",
    "description": "{{ $cluster->pillar_description }}",
    "url": "{{ url('/panduan/' . $cluster->pillar_slug) }}",
    "dateModified": "{{ $cluster->updated_at->toIso8601String() }}",
    "publisher": {
        "@@type": "Organization",
        "name": "Bizmark.ID",
        "url": "{{ url('/') }}"
    }
}
</script>

@if($faqs->count() > 0)
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@type": "FAQPage",
    "mainEntity": [
        @foreach($faqs as $i => $faq)
        {
            "@@type": "Question",
            "name": "{{ $faq['title'] }}",
            "acceptedAnswer": {
                "@@type": "Answer",
                "text": "{{ $faq['has_article'] ? $faq['article']->excerpt : 'Panduan lengkap segera tersedia di Bizmark.ID' }}"
            }
        }{{ $i < $faqs->count() - 1 ? ',' : '' }}
        @endforeach
    ]
}
</script>
@endif
@endsection
