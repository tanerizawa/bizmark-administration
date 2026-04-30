@extends('landing.layout')

@php
    $locale = app()->getLocale();
    $isEn = $locale === 'en';
    $blogIndexRoute    = $isEn ? 'blog.index.en'    : 'blog.index.id';
    $blogArticleRoute  = $isEn ? 'blog.article.en'  : 'blog.article.id';
    $blogTagRoute      = $isEn ? 'blog.tag.en'      : 'blog.tag.id';
    $total = $articles->total();
@endphp

@section('title', '#' . $tag . ' — Bizmark.ID')
@section('meta_description', ($isEn ? 'Articles tagged ' : 'Artikel tag ') . $tag)

@section('structured_data')
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@type": "BreadcrumbList",
    "itemListElement": [
        { "@@type": "ListItem", "position": 1, "name": "{{ $isEn ? 'Home' : 'Beranda' }}", "item": "{{ url('/') }}" },
        { "@@type": "ListItem", "position": 2, "name": "Blog", "item": "{{ route($blogIndexRoute) }}" },
        { "@@type": "ListItem", "position": 3, "name": @json('#' . $tag), "item": "{{ url()->current() }}" }
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
            <span>#{{ $tag }}</span>
        </nav>

        <div class="max-w-4xl">
            <span class="eyebrow mb-6">
                <i class="fas fa-hashtag mr-1 text-[10px]"></i>
                {{ $isEn ? 'Tag' : 'Tag' }}
            </span>
            <h1 class="display-xl mt-2 mb-4" style="color: var(--text-primary);">
                #{{ $tag }}
            </h1>
            <p class="text-lg leading-relaxed max-w-3xl" style="color: var(--text-secondary);">
                {{ $isEn ? 'All articles tagged' : 'Semua artikel dengan tag' }} <strong style="color: var(--text-primary);">#{{ $tag }}</strong>.
            </p>
            @if($total > 0)
                <div class="mt-6 text-sm" style="color: var(--text-tertiary);">
                    <i class="fas fa-newspaper mr-2" style="color: var(--color-gold);"></i>
                    <strong style="color: var(--text-primary);">{{ number_format($total) }}</strong>
                    {{ $isEn ? 'matching articles' : 'artikel cocok' }}
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
            {{ $isEn ? 'Back to all articles' : 'Kembali ke semua artikel' }}
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
                    <i class="fas fa-hashtag text-3xl"></i>
                </div>
                <h2 class="font-display text-2xl font-bold mb-2" style="color: var(--text-primary);">
                    {{ $isEn ? 'No articles found' : 'Artikel tidak ditemukan' }}
                </h2>
                <p style="color: var(--text-secondary);">
                    {{ $isEn ? 'No articles match this tag yet.' : 'Belum ada artikel dengan tag ini.' }}
                </p>
            </div>
        @else
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8 grid-equal">
                @foreach($articles as $article)
                    <a href="{{ route($blogArticleRoute, $article->slug) }}" class="article-card">
                        <div class="article-image">
                            @if($article->featured_image_url ?? $article->featured_image)
                                <img src="{{ $article->featured_image_url ?? asset('storage/' . $article->featured_image) }}"
                                     alt="{{ $article->title }}" loading="lazy" width="600" height="375">
                            @else
                                <div class="w-full h-full flex items-center justify-center bg-ink-gradient">
                                    <i class="fas fa-newspaper text-3xl text-white/40"></i>
                                </div>
                            @endif
                        </div>
                        <div class="flex flex-col flex-1">
                            <div class="article-meta mb-2">
                                <span class="article-cat">{{ $article->category_label ?? $article->category }}</span>
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
                                    {{ $article->reading_time }} {{ $isEn ? 'min read' : 'menit baca' }}
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

@endsection
