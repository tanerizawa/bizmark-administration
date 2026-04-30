@extends('landing.layout')

@php
    $locale = app()->getLocale();
    $isEn = $locale === 'en';

    $blogIndexRoute    = $isEn ? 'blog.index.en'    : 'blog.index.id';
    $blogArticleRoute  = $isEn ? 'blog.article.en'  : 'blog.article.id';
    $blogCategoryRoute = $isEn ? 'blog.category.en' : 'blog.category.id';
    $blogTagRoute      = $isEn ? 'blog.tag.en'      : 'blog.tag.id';

    $categoryLabels = \App\Helpers\BlogHelper::categoryLabels($isEn);
    $categoryLabel = $categoryLabels[$article->category] ?? \Illuminate\Support\Str::title(str_replace('-', ' ', (string)$article->category));

    $heroImage = $article->featured_image
        ? (\Illuminate\Support\Str::startsWith($article->featured_image, ['http','/']) ? $article->featured_image : asset('storage/' . $article->featured_image))
        : null;

    $primaryCtaRoute = route('landing.service-inquiry.create');
    $contact = (array) data_get(config('landing_metrics'), 'contact', []);
    $whatsappLink = $contact['whatsapp_link'] ?? 'https://wa.me/6283879602855';

    $authorName = optional($article->author)->name ?? 'Bizmark.ID Team';
    $publishedIso = optional($article->published_at)->toIso8601String();
    $modifiedIso  = optional($article->updated_at)->toIso8601String();
@endphp

@section('title', $article->meta_title ?? $article->title)
@section('meta_title', $article->meta_title ?? $article->title)
@section('meta_description', $article->meta_description ?? \Illuminate\Support\Str::limit(strip_tags((string) $article->excerpt ?? strip_tags((string) $article->content)), 155))
@if($article->meta_keywords)
    @section('meta_keywords', is_array($article->meta_keywords) ? implode(', ', $article->meta_keywords) : $article->meta_keywords)
@endif
@section('og_title', $article->title)
@section('og_description', \Illuminate\Support\Str::limit(strip_tags((string) $article->excerpt ?? strip_tags((string) $article->content)), 200))
@if($heroImage)
    @section('og_image', $heroImage)
@endif

@section('structured_data')
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@type": "Article",
    "mainEntityOfPage": {
        "@@type": "WebPage",
        "@id": "{{ url()->current() }}"
    },
    "headline": @json($article->title),
    "description": @json(\Illuminate\Support\Str::limit(strip_tags((string) $article->excerpt ?? strip_tags((string) $article->content)), 200)),
    @if($heroImage)"image": @json($heroImage),@endif
    "datePublished": "{{ $publishedIso }}",
    "dateModified": "{{ $modifiedIso }}",
    "author": {
        "@@type": "Person",
        "name": @json($authorName)
    },
    "publisher": {
        "@@type": "Organization",
        "name": "Bizmark.ID",
        "logo": {
            "@@type": "ImageObject",
            "url": "{{ asset('images/logo.png') }}"
        }
    },
    "articleSection": @json($categoryLabel)
}
</script>
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@type": "BreadcrumbList",
    "itemListElement": [
        { "@@type": "ListItem", "position": 1, "name": "{{ $isEn ? 'Home' : 'Beranda' }}", "item": "{{ url('/') }}" },
        { "@@type": "ListItem", "position": 2, "name": "Blog", "item": "{{ route($blogIndexRoute) }}" },
        { "@@type": "ListItem", "position": 3, "name": @json($categoryLabel), "item": "{{ route($blogCategoryRoute, $article->category) }}" },
        { "@@type": "ListItem", "position": 4, "name": @json($article->title), "item": "{{ url()->current() }}" }
    ]
}
</script>
@endsection

@section('content')

{{-- Reading progress bar --}}
<div id="reading-progress" aria-hidden="true"
     style="position: fixed; top: 0; left: 0; height: 3px; width: 0%; background: linear-gradient(90deg, var(--color-gold), var(--color-gold-light)); z-index: 60; transition: width .1s linear;"></div>

{{-- HERO --}}
<section class="section-v2" style="background: linear-gradient(180deg, #fff 0%, var(--surface-premium) 100%); padding-bottom: 2rem;">
    <div class="container-wide">
        <nav aria-label="Breadcrumb" class="text-xs mb-8 flex flex-wrap items-center gap-x-2" style="color: var(--text-tertiary);">
            <a href="{{ url('/') }}" class="hover:text-primary transition" style="color: var(--text-secondary);">
                {{ $isEn ? 'Home' : 'Beranda' }}
            </a>
            <span>/</span>
            <a href="{{ route($blogIndexRoute) }}" class="hover:text-primary transition" style="color: var(--text-secondary);">Blog</a>
            <span>/</span>
            <a href="{{ route($blogCategoryRoute, $article->category) }}" class="hover:text-primary transition" style="color: var(--text-secondary);">
                {{ $categoryLabel }}
            </a>
            <span>/</span>
            <span class="truncate max-w-[240px]">{{ $article->title }}</span>
        </nav>

        <div class="max-w-4xl">
            <a href="{{ route($blogCategoryRoute, $article->category) }}"
               class="article-cat mb-6 inline-flex"
               style="padding: .25rem .75rem; background: var(--color-primary); color: #fff; border-radius: var(--radius-full); font-size: .6875rem; font-weight: 700; letter-spacing: .1em; text-transform: uppercase;">
                {{ $categoryLabel }}
            </a>

            <h1 class="display-lg mt-4 mb-6" style="color: var(--text-primary);">
                {{ $article->title }}
            </h1>

            @if($article->excerpt)
                <p class="text-xl leading-relaxed max-w-3xl mb-8" style="color: var(--text-secondary);">
                    {{ $article->excerpt }}
                </p>
            @endif

            {{-- Article meta strip --}}
            <div class="flex flex-wrap items-center gap-x-6 gap-y-2 text-sm pt-6 border-t" style="color: var(--text-tertiary); border-color: var(--border-subtle);">
                <span class="inline-flex items-center gap-2">
                    <i class="fas fa-user-tie" style="color: var(--color-gold);"></i>
                    <span style="color: var(--text-primary); font-weight: 600;">{{ $authorName }}</span>
                </span>
                <span class="inline-flex items-center gap-2">
                    <i class="far fa-calendar" style="color: var(--color-gold);"></i>
                    <time datetime="{{ $publishedIso }}">{{ optional($article->published_at)->translatedFormat('d F Y') }}</time>
                </span>
                @if($article->reading_time)
                    <span class="inline-flex items-center gap-2">
                        <i class="far fa-clock" style="color: var(--color-gold);"></i>
                        {{ $article->reading_time }} {{ $isEn ? 'min read' : 'menit baca' }}
                    </span>
                @endif
                @if($article->views_count)
                    <span class="inline-flex items-center gap-2">
                        <i class="far fa-eye" style="color: var(--color-gold);"></i>
                        {{ number_format($article->views_count) }} {{ $isEn ? 'views' : 'dibaca' }}
                    </span>
                @endif
            </div>
        </div>
    </div>
</section>

{{-- HERO IMAGE --}}
@if($heroImage)
    <div class="container-wide">
        <div class="rounded-3xl overflow-hidden shadow-lg" style="aspect-ratio: 21/9; background: var(--surface-cool);">
            <img src="{{ $heroImage }}" alt="{{ $article->title }}"
                 class="w-full h-full object-cover"
                 loading="eager" fetchpriority="high" width="1400" height="600">
        </div>
    </div>
@endif

{{-- ARTICLE BODY --}}
<section class="section-v2" style="padding-top: 3rem;">
    <div class="container-wide">
        <div class="grid lg:grid-cols-12 gap-10">

            {{-- Main content (8 cols) --}}
            <article class="lg:col-span-8">
                <div class="article-prose" id="article-body">
                    {{-- SAFE: sanitized via HtmlSanitizer to prevent XSS --}}
                    {!! \App\Support\HtmlSanitizer::clean($article->content) !!}
                </div>

                {{-- Tags --}}
                @if(!empty($article->tags))
                    <div class="mt-10 pt-8 border-t" style="border-color: var(--border-subtle);">
                        <div class="text-[11px] font-bold uppercase tracking-[.15em] mb-3" style="color: var(--text-tertiary);">
                            {{ $isEn ? 'Tagged' : 'Tag' }}
                        </div>
                        <div class="flex flex-wrap gap-2">
                            @foreach((array) $article->tags as $tag)
                                <a href="{{ route($blogTagRoute, $tag) }}" class="cert-badge">
                                    <i class="fas fa-hashtag text-[10px]"></i>{{ $tag }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Share --}}
                <div class="mt-8 pt-8 border-t flex flex-wrap items-center gap-3" style="border-color: var(--border-subtle);">
                    <span class="text-sm font-semibold" style="color: var(--text-primary);">
                        {{ $isEn ? 'Share:' : 'Bagikan:' }}
                    </span>
                    @php
                        $shareUrl = urlencode(url()->current());
                        $shareText = urlencode($article->title);
                    @endphp
                    <a href="https://wa.me/?text={{ $shareText }}%20{{ $shareUrl }}" target="_blank" rel="noopener noreferrer"
                       class="cert-badge" aria-label="WhatsApp">
                        <i class="fab fa-whatsapp text-base flex-shrink-0 leading-none" style="color: #25d366;" aria-hidden="true"></i> WhatsApp
                    </a>
                    <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ $shareUrl }}" target="_blank" rel="noopener noreferrer"
                       class="cert-badge" aria-label="LinkedIn">
                        <i class="fab fa-linkedin text-base flex-shrink-0 leading-none" style="color: #0a66c2;" aria-hidden="true"></i> LinkedIn
                    </a>
                    <a href="https://twitter.com/intent/tweet?text={{ $shareText }}&url={{ $shareUrl }}" target="_blank" rel="noopener noreferrer"
                       class="cert-badge" aria-label="X/Twitter">
                        <i class="fab fa-x-twitter text-base flex-shrink-0 leading-none" aria-hidden="true"></i> X
                    </a>
                    <a href="mailto:?subject={{ $shareText }}&body={{ $shareUrl }}" class="cert-badge" aria-label="Email">
                        <i class="far fa-envelope text-base flex-shrink-0 leading-none" aria-hidden="true"></i> Email
                    </a>
                </div>
            </article>

            {{-- Sidebar (4 cols) --}}
            <aside class="lg:col-span-4">
                <div class="sticky top-24 space-y-5">

                    {{-- CTA card --}}
                    <div class="rounded-2xl p-6 bg-ink-gradient relative overflow-hidden">
                        <div aria-hidden="true" class="absolute -top-6 -right-6 w-28 h-28 opacity-25 pointer-events-none"
                             style="background: radial-gradient(circle, var(--color-gold) 0%, transparent 65%);"></div>
                        <div class="relative">
                            <div class="text-[10px] font-bold uppercase tracking-[.2em] mb-2" style="color: var(--color-gold-light);">
                                {{ $isEn ? 'Need help?' : 'Butuh bantuan?' }}
                            </div>
                            <h3 class="font-display font-bold text-xl mb-2 text-white leading-tight">
                                {{ $isEn ? 'Get your permit roadmap — free' : 'Dapatkan peta jalan perizinan Anda — gratis' }}
                            </h3>
                            <p class="text-sm mb-5 leading-relaxed" style="color: rgba(255,255,255,.72);">
                                {{ $isEn
                                    ? 'Free AI analysis — 1,000+ KBLI mapped instantly.'
                                    : 'Analisis AI gratis — 1.000+ KBLI dipetakan instan.' }}
                            </p>
                            <a href="{{ $primaryCtaRoute }}" class="btn btn-gold w-full justify-center mb-3">
                                <i class="fas fa-robot text-base flex-shrink-0 leading-none" aria-hidden="true"></i>
                                <span>{{ $isEn ? 'Start Free Permit Check' : 'Cek Perizinan Gratis' }}</span>
                            </a>
                            <a href="{{ $whatsappLink }}" target="_blank" rel="noopener noreferrer"
                               class="btn btn-ghost-on-dark w-full justify-center">
                                <i class="fab fa-whatsapp text-base flex-shrink-0 leading-none" aria-hidden="true"></i>
                                <span>{{ $isEn ? 'Chat WhatsApp' : 'Chat WhatsApp' }}</span>
                            </a>
                        </div>
                    </div>

                    {{-- Table of contents (auto-generated client-side) --}}
                    <nav id="toc-container" class="premium-card" aria-label="Table of contents" style="display: none;">
                        <div class="text-[11px] font-bold uppercase tracking-[.15em] mb-3" style="color: var(--color-gold);">
                            <i class="fas fa-list-ul mr-1.5"></i>
                            {{ $isEn ? 'In this article' : 'Dalam artikel ini' }}
                        </div>
                        <ol id="toc-list" class="space-y-2 text-sm" style="list-style: none; padding-left: 0;"></ol>
                    </nav>

                    {{-- Author mini-card --}}
                    <div class="premium-card">
                        <div class="flex items-center gap-4">
                            <div class="w-14 h-14 rounded-full flex items-center justify-center flex-shrink-0"
                                 style="background: rgba(59,130,246,.12); color: var(--accent-soft);">
                                <i class="fas fa-user-tie text-xl"></i>
                            </div>
                            <div class="min-w-0">
                                <div class="text-xs" style="color: var(--text-tertiary);">{{ $isEn ? 'Written by' : 'Ditulis oleh' }}</div>
                                <div class="font-semibold text-base truncate" style="color: var(--text-primary);">{{ $authorName }}</div>
                                <div class="text-xs" style="color: var(--text-secondary);">Bizmark.ID</div>
                            </div>
                        </div>
                    </div>
                </div>
            </aside>

        </div>
    </div>
</section>

{{-- RELATED ARTICLES --}}
@if($relatedArticles->count() > 0)
<section class="section-v2 section-premium">
    <div class="container-wide">
        <div class="flex items-end justify-between mb-10 gap-6 flex-wrap">
            <div>
                <span class="eyebrow mb-3">{{ $isEn ? 'Keep reading' : 'Baca juga' }}</span>
                <h2 class="display-md mt-2" style="color: var(--text-primary);">
                    {{ $isEn ? 'More on ' : 'Lainnya tentang ' }}{{ $categoryLabel }}
                </h2>
            </div>
            <a href="{{ route($blogCategoryRoute, $article->category) }}" class="btn btn-outline-primary btn-sm">
                <span>{{ $isEn ? 'View all' : 'Lihat semua' }}</span>
                <i class="fas fa-arrow-right text-xs flex-shrink-0 leading-none" aria-hidden="true"></i>
            </a>
        </div>

        <div class="grid md:grid-cols-3 gap-8 grid-equal">
            @foreach($relatedArticles as $related)
                <a href="{{ route($blogArticleRoute, $related->slug) }}" class="article-card">
                    <div class="article-image">
                        @if($related->featured_image)
                            <img src="{{ \Illuminate\Support\Str::startsWith($related->featured_image, ['http','/']) ? $related->featured_image : asset('storage/' . $related->featured_image) }}"
                                 alt="{{ $related->title }}"
                                 loading="lazy" width="400" height="250">
                        @else
                            <div class="w-full h-full flex items-center justify-center bg-ink-gradient">
                                <i class="fas fa-newspaper text-2xl text-white/40"></i>
                            </div>
                        @endif
                    </div>
                    <div class="flex flex-col flex-1">
                        <div class="article-meta mb-2">
                            <span class="article-cat">{{ $categoryLabels[$related->category] ?? $related->category }}</span>
                        </div>
                        <h3 class="article-title mb-2" style="font-size: 1.125rem;">{{ $related->title }}</h3>
                        @if($related->reading_time)
                            <div class="text-xs mt-auto pt-3" style="color: var(--text-tertiary);">
                                <i class="far fa-clock mr-1"></i>
                                {{ $related->reading_time }} {{ $isEn ? 'min read' : 'menit baca' }}
                            </div>
                        @endif
                    </div>
                </a>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- Newsletter --}}
@include('landing.sections.v2.newsletter', ['locale' => $locale])

{{-- Article prose styles --}}
<style>
.article-prose {
    font-size: 1.0625rem;
    line-height: 1.75;
    color: var(--text-primary);
    max-width: 72ch;
}
.article-prose > * + * { margin-top: 1.25rem; }
.article-prose h2 {
    font-family: var(--font-display);
    font-weight: 700;
    font-size: clamp(1.5rem, 2.5vw, 1.875rem);
    line-height: 1.25;
    margin-top: 3rem;
    margin-bottom: .75rem;
    color: var(--text-primary);
    letter-spacing: -0.015em;
}
.article-prose h3 {
    font-family: var(--font-display);
    font-weight: 700;
    font-size: 1.375rem;
    line-height: 1.3;
    margin-top: 2.5rem;
    margin-bottom: .5rem;
    color: var(--text-primary);
}
.article-prose h4 {
    font-weight: 700;
    font-size: 1.125rem;
    margin-top: 2rem;
    margin-bottom: .5rem;
    color: var(--text-primary);
}
.article-prose p { color: var(--text-secondary); }
.article-prose a {
    color: var(--accent-soft);
    text-decoration: underline;
    text-decoration-color: var(--color-gold);
    text-underline-offset: 3px;
    text-decoration-thickness: 2px;
    font-weight: 500;
}
.article-prose a:hover { color: var(--color-gold); }
.article-prose strong { color: var(--text-primary); font-weight: 700; }
.article-prose ul, .article-prose ol { padding-left: 1.5rem; color: var(--text-secondary); }
.article-prose ul > li, .article-prose ol > li { margin-top: .5rem; line-height: 1.7; }
.article-prose ul > li::marker { color: var(--color-gold); }
.article-prose ol > li::marker { color: var(--color-gold); font-weight: 700; }
.article-prose blockquote {
    border-left: 3px solid var(--color-gold);
    padding: .75rem 0 .75rem 1.5rem;
    margin: 2rem 0;
    font-family: var(--font-display);
    font-size: 1.25rem;
    font-style: italic;
    color: var(--text-primary);
    line-height: 1.5;
}
.article-prose img {
    max-width: 100%;
    height: auto;
    border-radius: var(--radius-xl);
    margin: 2rem 0;
}
.article-prose figure { margin: 2rem 0; }
.article-prose figcaption { font-size: .875rem; color: var(--text-tertiary); text-align: center; margin-top: .5rem; }
.article-prose pre {
    background: var(--surface-ink);
    color: #e2e8f0;
    padding: 1.25rem;
    border-radius: var(--radius-lg);
    overflow-x: auto;
    font-size: .875rem;
    line-height: 1.6;
}
.article-prose code {
    background: var(--surface-cool);
    padding: .125rem .375rem;
    border-radius: var(--radius-sm);
    font-size: .875em;
    font-family: 'JetBrains Mono', ui-monospace, monospace;
}
.article-prose pre code { background: transparent; padding: 0; }
.article-prose hr {
    border: none;
    height: 1px;
    background: var(--border-medium);
    margin: 3rem 0;
}
.article-prose table {
    width: 100%;
    border-collapse: collapse;
    margin: 2rem 0;
    font-size: .9375rem;
}
.article-prose table th, .article-prose table td {
    padding: .75rem 1rem;
    text-align: left;
    border-bottom: 1px solid var(--border-subtle);
}
.article-prose table th {
    background: var(--surface-premium);
    font-weight: 700;
    color: var(--text-primary);
}

/* TOC link styles */
#toc-list a {
    display: block;
    padding: .375rem .625rem;
    border-radius: var(--radius-md);
    color: var(--text-secondary);
    text-decoration: none;
    transition: background .2s ease, color .2s ease;
    font-size: .875rem;
    line-height: 1.4;
}
#toc-list a:hover { background: var(--surface-cool); color: var(--color-primary); }
#toc-list .toc-h3 { padding-left: 1.5rem; font-size: .8125rem; }
</style>

{{-- Reading progress + TOC generator --}}
<script>
(function() {
    // Reading progress
    var bar = document.getElementById('reading-progress');
    var body = document.getElementById('article-body');
    function update() {
        if (!bar || !body) return;
        var rect = body.getBoundingClientRect();
        var total = rect.height - window.innerHeight;
        var scrolled = Math.min(Math.max(-rect.top, 0), total);
        var pct = total > 0 ? (scrolled / total) * 100 : 0;
        bar.style.width = pct + '%';
    }
    window.addEventListener('scroll', update, { passive: true });
    window.addEventListener('resize', update, { passive: true });
    update();

    // Auto-generate TOC from h2/h3
    var container = document.getElementById('toc-container');
    var list = document.getElementById('toc-list');
    if (!container || !list || !body) return;
    var headings = body.querySelectorAll('h2, h3');
    if (headings.length < 2) return;

    headings.forEach(function(h, i) {
        if (!h.id) {
            h.id = 'section-' + i + '-' + (h.textContent || '').toLowerCase().replace(/[^a-z0-9]+/g, '-').slice(0, 40);
        }
        var li = document.createElement('li');
        var a = document.createElement('a');
        a.href = '#' + h.id;
        a.textContent = h.textContent;
        if (h.tagName === 'H3') a.className = 'toc-h3';
        li.appendChild(a);
        list.appendChild(li);
    });
    container.style.display = '';
})();
</script>

@endsection
