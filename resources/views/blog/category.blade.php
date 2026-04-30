@extends('landing.layout')

@php
    $locale = app()->getLocale();
    $isEn = $locale === 'en';
    $total = $articles->total();
@endphp

@section('title', $categoryLabel . ' — Bizmark.ID')
@section('meta_description', $categoryLabel . ' — ' . ($isEn ? 'Articles, case studies, and guides' : 'Artikel, studi kasus, dan panduan'))

@section('structured_data')
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@type": "CollectionPage",
    "name": @json($categoryLabel . ' — Bizmark.ID'),
    "url": "{{ url()->current() }}",
    "isPartOf": {
        "@@type": "Blog",
        "name": "Bizmark.ID",
        "url": "{{ route($blogIndexRoute) }}"
    }
}
</script>
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@type": "BreadcrumbList",
    "itemListElement": [
        { "@@type": "ListItem", "position": 1, "name": "{{ $isEn ? 'Home' : 'Beranda' }}", "item": "{{ url('/') }}" },
        { "@@type": "ListItem", "position": 2, "name": "Blog", "item": "{{ route($blogIndexRoute) }}" },
        { "@@type": "ListItem", "position": 3, "name": @json($categoryLabel), "item": "{{ url()->current() }}" }
    ]
}
</script>
@endsection

@section('content')

{{-- HERO --}}
<section class="section-v2" style="background: linear-gradient(180deg, #fff 0%, var(--surface-premium) 100%);">
    <div class="container-wide">
        <nav aria-label="Breadcrumb" class="text-xs mb-6" style="color: var(--text-tertiary);">
            <a href="{{ url('/') }}" style="color: var(--text-secondary);">{{ $isEn ? 'Home' : 'Beranda' }}</a>
            <span class="mx-2">/</span>
            <a href="{{ route($blogIndexRoute) }}" style="color: var(--text-secondary);">Blog</a>
            <span class="mx-2">/</span>
            <span>{{ $categoryLabel }}</span>
        </nav>

        <div class="max-w-4xl">
            <span class="eyebrow mb-6">{{ $isEn ? 'Category' : 'Kategori' }}</span>
            <h1 class="display-xl mt-2 mb-4" style="color: var(--text-primary);">
                {{ $categoryLabel }}
            </h1>
            <p class="text-xl leading-relaxed max-w-3xl" style="color: var(--text-secondary);">
                {{ __('blog.category_descriptions.' . $category, ['default' => __('blog.category_descriptions.general')]) }}
            </p>
            @if($total > 0)
                <div class="mt-6 text-sm" style="color: var(--text-tertiary);">
                    <i class="fas fa-folder-open mr-2" style="color: var(--color-gold);"></i>
                    <strong style="color: var(--text-primary);">{{ number_format($total) }}</strong>
                    {{ $isEn ? 'articles in this category' : 'artikel dalam kategori ini' }}
                </div>
            @endif
        </div>
    </div>
</section>

{{-- BACK LINK --}}
<section class="section-v2-sm border-y" style="background: #fff; border-color: var(--border-subtle);">
    <div class="container-wide">
        <a href="{{ route($blogIndexRoute) }}" class="link-primary text-sm inline-flex items-center gap-2 font-semibold">
            <i class="fas fa-arrow-left text-xs flex-shrink-0 leading-none" aria-hidden="true"></i>
            {{ $isEn ? 'Back to all articles' : __('blog.category_page.back_to_blog') }}
        </a>
    </div>
</section>

{{-- ARTICLES GRID --}}
<section class="section-v2">
    <div class="container-wide">
        @if($articles->count() === 0)
            <div class="text-center py-16">
                <div class="inline-flex items-center justify-center w-20 h-20 rounded-full mb-6"
                     style="background: var(--surface-cool); color: var(--text-tertiary);">
                    <i class="fas fa-newspaper text-3xl"></i>
                </div>
                <h2 class="font-display text-2xl font-bold mb-2" style="color: var(--text-primary);">
                    {{ __('blog.category_page.no_articles') }}
                </h2>
                <p style="color: var(--text-secondary);">
                    {{ __('blog.category_page.no_articles_description') }}
                </p>
            </div>
        @else
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8 grid-equal">
                @foreach($articles as $article)
                    <a href="{{ route($blogArticleRoute, $article->slug) }}" class="article-card">
                        <div class="article-image">
                            @if($article->featured_image_url)
                                <img src="{{ $article->featured_image_url }}" alt="{{ $article->title }}"
                                     loading="lazy" width="600" height="375">
                            @else
                                <div class="w-full h-full flex items-center justify-center bg-ink-gradient">
                                    <i class="fas fa-newspaper text-3xl text-white/40"></i>
                                </div>
                            @endif
                        </div>
                        <div class="flex flex-col flex-1">
                            <div class="article-meta mb-2">
                                <span class="article-cat">{{ $article->category_label }}</span>
                                <span>·</span>
                                <time datetime="{{ optional($article->published_at)->toIso8601String() }}">
                                    {{ optional($article->published_at)->translatedFormat('d M Y') }}
                                </time>
                            </div>
                            <h2 class="article-title mb-3">{{ $article->title }}</h2>
                            @if($article->excerpt)
                                <p class="article-excerpt flex-1">{{ \Illuminate\Support\Str::limit($article->excerpt, 140) }}</p>
                            @endif
                            @if($article->reading_time)
                                <div class="text-xs mt-4 pt-4 border-t" style="color: var(--text-tertiary); border-color: var(--border-subtle);">
                                    <i class="far fa-clock mr-1"></i>
                                    {{ $article->reading_time }} {{ $isEn ? 'min read' : __('blog.category_page.reading_time') }}
                                </div>
                            @endif
                        </div>
                    </a>
                @endforeach
            </div>

            @if($articles->hasPages())
                <div class="mt-12">
                    {{ $articles->links() }}
                </div>
            @endif
        @endif
    </div>
</section>

{{-- EXPLORE OTHER CATEGORIES --}}
<section class="section-v2-sm section-premium">
    <div class="container-wide text-center">
        <h2 class="font-display text-xl font-bold mb-5" style="color: var(--text-primary);">
            {{ __('blog.category_page.explore_categories') }}
        </h2>
        <div class="flex flex-wrap justify-center gap-2">
            @foreach(['perizinan','lingkungan','regulasi','tips'] as $c)
                @continue($c === $category)
                <a href="{{ route($blogCategoryRoute, $c) }}" class="cert-badge">
                    <i class="fas fa-folder text-[10px]"></i>
                    {{ __('blog.category_names.' . $c) }}
                </a>
            @endforeach
        </div>
    </div>
</section>

@endsection
