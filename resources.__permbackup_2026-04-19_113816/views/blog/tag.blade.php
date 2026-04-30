@extends('landing.layout')

@php
    $locale = app()->getLocale();
    $blogIndexRoute = $locale === 'en' ? 'blog.index.en' : 'blog.index.id';
    $blogArticleRoute = $locale === 'en' ? 'blog.article.en' : 'blog.article.id';
    $blogTagRoute = $locale === 'en' ? 'blog.tag.en' : 'blog.tag.id';
@endphp

@section('title', '#' . $tag . ' - Bizmark.ID')
@section('meta_description', ($locale === 'en' ? 'Articles tagged' : 'Artikel dengan tag') . ' #' . $tag)

@section('content')
<section class="relative overflow-hidden pt-28 pb-16" style="background:linear-gradient(135deg,var(--surface-warm) 0%, var(--surface-cool) 100%);">
    <div class="container-wide">
        <a href="{{ route($blogIndexRoute) }}" class="link-primary text-sm inline-flex items-center mb-5"><i class="fas fa-arrow-left mr-2"></i>{{ $locale === 'en' ? 'Back to insights' : 'Kembali ke blog' }}</a>
        <span class="section-badge mb-4">{{ $locale === 'en' ? 'Tag' : 'Tag' }}</span>
        <h1 class="section-title mb-3">#{{ $tag }}</h1>
        <p class="section-description mb-0" style="margin-left:0;">{{ $locale === 'en' ? 'Browse articles that mention this topic.' : 'Jelajahi artikel yang membahas topik ini.' }}</p>
    </div>
</section>

<section class="section">
    <div class="container-wide">
        @if($articles->count() === 0)
            <div class="card text-center">
                <h2 class="text-xl font-bold mb-2" style="color:var(--text-primary);">{{ $locale === 'en' ? 'No articles found' : 'Tidak ada artikel' }}</h2>
                <p class="mb-0" style="color:var(--text-secondary);">{{ $locale === 'en' ? 'Try another tag.' : 'Coba tag lain.' }}</p>
            </div>
        @else
            <div class="grid md:grid-cols-2 xl:grid-cols-3 gap-6">
                @foreach($articles as $article)
                    <article class="card p-0 overflow-hidden">
                        <a href="{{ route($blogArticleRoute, $article->slug) }}" class="block">
                            <div class="relative" style="aspect-ratio:16/9;background:var(--surface-cool);">
                                @if($article->featured_image_url)
                                    <img src="{{ $article->featured_image_url }}" alt="{{ $article->title }}" class="w-full h-full object-cover" loading="lazy" width="1200" height="675">
                                @else
                                    <div class="w-full h-full flex items-center justify-center" style="background:linear-gradient(135deg, rgba(15,23,42,.08), rgba(14,165,233,.12));">
                                        <i class="fas fa-newspaper text-4xl" style="color:rgba(15,23,42,.25);"></i>
                                    </div>
                                @endif
                            </div>
                        </a>
                        <div class="p-6">
                            <div class="flex flex-wrap items-center gap-x-4 gap-y-2 text-xs mb-3" style="color:var(--text-tertiary);">
                                <span><i class="far fa-calendar mr-1"></i>{{ optional($article->published_at)->format('d M Y') }}</span>
                                <span><i class="far fa-clock mr-1"></i>{{ $article->reading_time }} {{ $locale === 'en' ? 'min read' : 'menit' }}</span>
                            </div>
                            <h2 class="text-lg font-bold mb-2" style="color:var(--text-primary);">
                                <a href="{{ route($blogArticleRoute, $article->slug) }}" class="hover:underline" style="text-underline-offset:3px;">{{ $article->title }}</a>
                            </h2>
                            <p class="text-sm mb-4" style="color:var(--text-secondary);">{{ Str::limit($article->excerpt, 140) }}</p>
                            <div class="flex flex-wrap gap-2 mb-4">
                                <a href="{{ route($blogTagRoute, $tag) }}" class="chip active">#{{ $tag }}</a>
                                <span class="chip" style="pointer-events:none;">{{ $article->category_label }}</span>
                            </div>
                            <div class="pt-3 border-t" style="border-color:var(--border-light);">
                                <a href="{{ route($blogArticleRoute, $article->slug) }}" class="link-primary text-sm inline-flex items-center">{{ $locale === 'en' ? 'Read' : 'Baca' }} <i class="fas fa-arrow-right ml-2"></i></a>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>

            @if($articles->hasPages())
                <div class="mt-10">
                    {{ $articles->links() }}
                </div>
            @endif
        @endif
    </div>
</section>
@endsection

