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
    $totalSubtopics = count($subtopics ?? []);
    $availableArticles = collect($subtopics ?? [])->where('has_article', true)->count();
@endphp

{{-- Reading-progress strip --}}
<div id="pillar-progress" aria-hidden="true"
     style="position:fixed;top:0;left:0;height:3px;width:0;background:linear-gradient(90deg, var(--accent), var(--accent-soft));z-index:60;transition:width .15s ease-out;"></div>

{{-- HERO --}}
<section class="section-v2-sm bg-[var(--bg-raised)] border-b border-gray-200">
    <div class="container-wide">
        <div class="max-w-4xl">
            <span class="eyebrow mb-4">Panduan Lengkap</span>
            <h1 class="display-lg mt-2 mb-5">{{ $cluster->pillar_title }}</h1>
            <p class="text-lg leading-relaxed max-w-3xl text-gray-600 mb-6">{{ $cluster->pillar_description }}</p>
            <div class="flex flex-wrap items-center gap-4 text-sm text-gray-500 mb-6">
                <span class="inline-flex items-center gap-1.5">
                    <i class="fas fa-list-ol" style="color: var(--accent);"></i>
                    <strong>{{ $totalSubtopics }}</strong> subtopik
                </span>
                <span class="inline-flex items-center gap-1.5">
                    <i class="fas fa-book-open" style="color: var(--accent);"></i>
                    <strong>{{ $availableArticles }}</strong> artikel tersedia
                </span>
                <span class="inline-flex items-center gap-1.5">
                    <i class="fas fa-clock" style="color: var(--accent);"></i>
                    Diperbarui berkala
                </span>
            </div>
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('pillar.index') }}" class="btn btn-ghost btn-sm"><i class="fas fa-arrow-left text-xs"></i> Semua Panduan</a>
                @if($serviceHref)
                    <a href="{{ $serviceHref }}" class="btn btn-gold btn-sm"><i class="fas {{ $service['icon'] ?? 'fa-layer-group' }} text-xs"></i> Lihat Layanan</a>
                @endif
            </div>
        </div>
    </div>
</section>

{{-- MAGAZINE LAYOUT: sticky sidebar TOC + content --}}
<section class="section-v2">
    <div class="container-wide">
        <div class="grid lg:grid-cols-12 gap-8 items-start">

            {{-- Sticky sidebar (desktop) / accordion (mobile) --}}
            <aside class="lg:col-span-4 lg:sticky lg:top-24 order-1">
                <div class="premium-card">
                    <div class="flex items-center justify-between flex-wrap gap-2 mb-4">
                        <h2 class="font-display font-bold text-base mb-0 inline-flex items-center gap-2">
                            <i class="fas fa-list-ol" style="color: var(--accent);"></i>
                            Daftar Isi
                        </h2>
                        <span class="text-[10px] font-bold uppercase tracking-[.15em]" style="color: var(--accent-text);">
                            {{ $availableArticles }}/{{ $totalSubtopics }}
                        </span>
                    </div>
                    <nav class="space-y-1 text-sm" aria-label="Daftar isi panduan">
                        @foreach($subtopics as $i => $subtopic)
                            <a href="#subtopic-{{ $i }}"
                               class="flex items-start gap-2.5 p-2 rounded-md transition-colors hover:bg-amber-500/10"
                               style="color: {{ $subtopic['has_article'] ? 'var(--text-primary)' : 'var(--text-tertiary)' }};">
                                <span class="flex-shrink-0 inline-flex items-center justify-center w-5 h-5 rounded text-[10px] font-bold" style="background: {{ $subtopic['has_article'] ? 'var(--accent-glow)' : 'rgba(0,0,0,.04)' }}; color: {{ $subtopic['has_article'] ? 'var(--accent-text)' : 'var(--text-tertiary)' }};">
                                    {{ $i + 1 }}
                                </span>
                                <span class="flex-1 leading-snug">{{ $subtopic['title'] }}</span>
                                @if($subtopic['has_article'])
                                    <i class="fas fa-check text-[10px] mt-1.5" style="color: var(--accent);" aria-label="Tersedia"></i>
                                @endif
                            </a>
                        @endforeach
                    </nav>
                </div>

                @if($serviceHref)
                <div class="premium-card mt-5">
                    <p class="text-[10px] font-bold uppercase tracking-[.15em] mb-2" style="color: var(--accent-text);">
                        <i class="fas fa-handshake mr-1"></i> Bantuan Profesional
                    </p>
                    <h3 class="font-display font-bold text-base mb-2">Mau diurus tim kami?</h3>
                    <p class="text-sm text-gray-600 mb-4">Konsultasi gratis &amp; estimasi biaya dalam 24 jam.</p>
                    <a href="{{ $waHref }}" target="_blank" rel="noopener" class="btn btn-gold btn-sm w-full justify-center">
                        <i class="fab fa-whatsapp"></i> Konsultasi WhatsApp
                    </a>
                </div>
                @endif
            </aside>

            {{-- Article body --}}
            <div class="lg:col-span-8 order-2 space-y-6">
                @foreach($subtopics as $i => $subtopic)
                    <article id="subtopic-{{ $i }}" class="premium-card scroll-mt-24">
                        <div class="flex items-start gap-4">
                            <div class="flex-shrink-0 w-12 h-12 rounded-xl flex items-center justify-center font-extrabold"
                                 style="background: {{ $subtopic['has_article'] ? 'var(--accent-glow)' : 'rgba(0,0,0,.04)' }}; color: {{ $subtopic['has_article'] ? 'var(--accent-text)' : 'var(--text-tertiary)' }};">
                                {{ $i + 1 }}
                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="flex flex-wrap items-center gap-2 mb-2">
                                    <span class="text-[10px] font-bold uppercase tracking-[.15em] px-2 py-0.5 rounded" style="background: rgba(0,0,0,.05); color: var(--text-muted);">{{ ucfirst($subtopic['priority']) }}</span>
                                    <span class="text-[10px] font-bold uppercase tracking-[.15em] px-2 py-0.5 rounded" style="background: rgba(0,0,0,.05); color: var(--text-muted);">{{ ucfirst($subtopic['type']) }}</span>
                                </div>
                                <h3 class="font-display font-bold text-xl mb-2">{{ $subtopic['title'] }}</h3>
                                @if($subtopic['has_article'])
                                    <p class="text-base leading-relaxed mb-3 text-gray-600">{{ $subtopic['article']->excerpt }}</p>
                                    <a href="{{ $subtopic['article']->getUrl() }}" class="inline-flex items-center gap-1.5 text-sm font-semibold" style="color: var(--accent-text);">
                                        Baca selengkapnya
                                        <i class="fas fa-arrow-right text-xs"></i>
                                    </a>
                                @else
                                    <p class="text-sm mb-0 text-gray-500"><i class="fas fa-clock mr-1"></i> Artikel sedang dalam tahap persiapan.</p>
                                @endif
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
        </div>
    </div>
</section>

{{-- Related Articles --}}
@if($articles->count() > 0)
<section class="section-v2-sm section-premium">
    <div class="container-wide">
        <span class="eyebrow mb-3">Bacaan Terkait</span>
        <h2 class="display-md mt-2 mb-8">Artikel pendukung</h2>
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($articles->take(6) as $article)
                <a href="{{ $article->getUrl() }}" class="premium-card overflow-hidden p-0 group transition-transform hover:-translate-y-0.5">
                    @if($article->featured_image)
                        <img src="{{ $article->featured_image }}" alt="{{ $article->title }}" class="w-full" style="height:160px;object-fit:cover;" loading="lazy">
                    @endif
                    <div style="padding:1.25rem;">
                        <h3 class="text-base font-bold mb-2 group-hover:underline" style="color:var(--text-primary);">{{ $article->title }}</h3>
                        <p class="text-sm mb-0 text-gray-600">{{ $article->excerpt }}</p>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- Keywords Cloud --}}
@if($keywordClusters->count() > 0)
<section class="section-v2-sm">
    <div class="container-wide">
        <span class="eyebrow mb-3">Pencarian Terkait</span>
        <h2 class="display-md mt-2 mb-6">Kata kunci yang sering dicari</h2>
        <div class="flex flex-wrap gap-2">
            @foreach($keywordClusters->flatMap(fn($kc) => $kc->long_tail_keywords ?? $kc->keywords ?? [])->unique()->take(30) as $keyword)
                <span class="text-xs px-3 py-1.5 rounded-full" style="background: rgba(0,0,0,.04); color: var(--text-muted);">{{ $keyword }}</span>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- Related Pillars --}}
@if($relatedClusters->count() > 0)
<section class="section-v2-sm section-premium">
    <div class="container-wide">
        <span class="eyebrow mb-3">Eksplorasi Lanjutan</span>
        <h2 class="display-md mt-2 mb-8">Panduan lainnya</h2>
        <div class="grid sm:grid-cols-2 gap-5">
            @foreach($relatedClusters as $rc)
                <a href="{{ url('/panduan/' . $rc->pillar_slug) }}" class="premium-card flex items-center gap-4 group transition-transform hover:-translate-y-0.5">
                    <div class="flex-shrink-0 w-12 h-12 rounded-xl flex items-center justify-center" style="background: var(--accent-glow); color: var(--accent-text);">
                        <i class="fas fa-book"></i>
                    </div>
                    <div class="min-w-0">
                        <h3 class="text-base font-bold mb-1 group-hover:underline" style="color:var(--text-primary);">{{ $rc->pillar_title }}</h3>
                        <p class="text-sm mb-0 text-gray-500">{{ count($rc->subtopics ?? []) }} subtopik</p>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- CTA --}}
<section class="section-v2 section-premium">
    <div class="container-wide">
        <div class="max-w-3xl mx-auto text-center">
            <span class="gold-rule"></span>
            <h2 class="display-md mb-4">Butuh bantuan pengurusan?</h2>
            <p class="text-lg leading-relaxed mb-8 text-gray-600">Ceritakan kebutuhan Anda, tim kami bantu susun langkah dan dokumen yang diperlukan.</p>
            <div class="flex flex-wrap items-center justify-center gap-3">
                <a href="{{ $waHref }}" target="_blank" rel="noopener" class="btn btn-gold btn-lg"><i class="fab fa-whatsapp"></i> Konsultasi via WhatsApp</a>
                <a href="{{ route('contact.index') }}" class="btn btn-ghost btn-lg"><i class="fas fa-envelope"></i> Hubungi Tim</a>
            </div>
        </div>
    </div>
</section>

<script>
// Reading progress
(function () {
    var bar = document.getElementById('pillar-progress');
    if (!bar) return;
    function update() {
        var doc = document.documentElement;
        var scrolled = (doc.scrollTop || document.body.scrollTop);
        var max = (doc.scrollHeight - doc.clientHeight) || 1;
        var pct = Math.max(0, Math.min(100, (scrolled / max) * 100));
        bar.style.width = pct + '%';
    }
    window.addEventListener('scroll', update, { passive: true });
    update();
})();
</script>

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
