@extends('landing.layout')

@section('content')
@php
    $blogIndexRoute = app()->getLocale() === 'en' ? 'blog.index.en' : 'blog.index.id';
    $blogCategoryRoute = app()->getLocale() === 'en' ? 'blog.category.en' : 'blog.category.id';
    $blogArticleRoute = app()->getLocale() === 'en' ? 'blog.article.en' : 'blog.article.id';
    $blogTagRoute = app()->getLocale() === 'en' ? 'blog.tag.en' : 'blog.tag.id';
@endphp

<!-- Breadcrumbs -->
<section class="pt-24 pb-4 px-4 bg-gray-50 border-b border-gray-100">
    <div class="container mx-auto">
        <nav class="flex items-center space-x-2 text-sm text-gray-500">
            <a href="/" class="text-gray-600 hover:text-primary transition">
                <i class="fas fa-home mr-1"></i>Beranda
            </a>
            <span class="text-gray-300">/</span>
            <a href="{{ route($blogIndexRoute) }}" class="text-gray-600 hover:text-primary transition">
                Blog
            </a>
            <span class="text-gray-300">/</span>
            <span class="text-gray-900 font-medium">#{{ $tag }}</span>
        </nav>
    </div>
</section>

<!-- Tag Header -->
<section class="py-14 px-4 bg-white">
    <div class="container mx-auto text-center">
        <div class="mb-5">
            <a href="{{ route($blogIndexRoute) }}" class="text-gray-500 hover:text-primary transition inline-flex items-center text-sm font-medium">
                <i class="fas fa-arrow-left mr-2"></i>Kembali ke Blog
            </a>
        </div>

        <span class="inline-flex items-center gap-2 px-4 py-1.5 bg-primary/5 rounded-full text-xs font-bold uppercase tracking-widest text-primary mb-5">
            <i class="fas fa-tag"></i> Tag
        </span>

        <h1 class="text-4xl md:text-5xl font-bold mb-4 text-gray-900">
            #{{ $tag }}
        </h1>

        <p class="text-lg text-gray-600 max-w-2xl mx-auto">
            Artikel yang ditandai dengan <span class="text-primary font-semibold">#{{ $tag }}</span>
        </p>
    </div>
</section>

<!-- Articles Grid -->
<section class="py-14 px-4 bg-gray-50">
    <div class="container mx-auto">
        @if($articles->count() > 0)
        <div class="mb-8 text-center text-gray-500 text-sm">
            <p>Ditemukan <span class="text-primary font-semibold">{{ $articles->total() }}</span> artikel</p>
        </div>

        <div class="grid md:grid-cols-2 xl:grid-cols-3 gap-6 mb-10">
            @foreach($articles as $article)
            <article class="group bg-white rounded-2xl overflow-hidden shadow-soft hover:shadow-lg transition-all duration-300">
                <div class="relative h-48 overflow-hidden">
                    @if($article->featured_image_url)
                    <img src="{{ $article->featured_image_url }}" alt="{{ $article->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" width="1200" height="630" loading="lazy">
                    @else
                    <div class="w-full h-full bg-gradient-to-br from-primary/10 to-secondary/10 flex items-center justify-center">
                        <i class="fas fa-newspaper text-4xl text-gray-200"></i>
                    </div>
                    @endif
                    <div class="absolute top-3 left-3">
                        <a href="{{ route($blogCategoryRoute, $article->category) }}" class="px-3 py-1 rounded-full bg-white/90 text-xs font-semibold tracking-wide uppercase text-gray-800">
                            {{ $article->category_label }}
                        </a>
                    </div>
                </div>

                <div class="p-6">
                    <div class="flex items-center gap-3 mb-3 text-xs text-gray-400">
                        <span><i class="far fa-calendar mr-1"></i>{{ $article->published_at->format('d M Y') }}</span>
                        <span><i class="far fa-clock mr-1"></i>{{ $article->reading_time }} min</span>
                        <span><i class="far fa-eye mr-1"></i>{{ number_format($article->views) }}</span>
                    </div>

                    <h3 class="text-lg font-bold mb-2 leading-snug text-gray-900 group-hover:text-primary transition">
                        <a href="{{ route($blogArticleRoute, $article->slug) }}">{{ $article->title }}</a>
                    </h3>

                    <p class="text-gray-600 text-sm mb-3 line-clamp-2">
                        {{ Str::limit($article->excerpt, 120) }}
                    </p>

                    @if($article->tags && count($article->tags) > 0)
                    <div class="flex flex-wrap gap-2 mb-4">
                        @foreach(array_slice($article->tags, 0, 3) as $articleTag)
                        <a href="{{ route($blogTagRoute, $articleTag) }}" class="text-xs px-2.5 py-1 rounded-full transition {{ $articleTag === $tag ? 'bg-primary/10 text-primary font-semibold' : 'bg-gray-100 text-gray-500 hover:bg-primary/10 hover:text-primary' }}">
                            #{{ $articleTag }}
                        </a>
                        @endforeach
                    </div>
                    @endif

                    <a href="{{ route($blogArticleRoute, $article->slug) }}" class="text-primary font-semibold text-sm inline-flex items-center group/link">
                        Baca Selengkapnya
                        <i class="fas fa-arrow-right ml-2 group-hover/link:translate-x-1 transition-transform"></i>
                    </a>
                </div>
            </article>
            @endforeach
        </div>

        @if($articles->hasPages())
        <div class="flex justify-center">
            {{ $articles->links() }}
        </div>
        @endif
        @else
        <div class="text-center py-20">
            <div class="w-16 h-16 mx-auto rounded-2xl bg-white flex items-center justify-center text-primary shadow-soft mb-4">
                <i class="fas fa-tag text-2xl"></i>
            </div>
            <h3 class="text-2xl font-bold mb-2 text-gray-900">Belum Ada Artikel</h3>
            <p class="text-gray-500 mb-6">Artikel dengan tag ini akan segera hadir.</p>
            <a href="{{ route($blogIndexRoute) }}" class="btn btn-primary inline-flex items-center">
                <i class="fas fa-arrow-left mr-2"></i>
                Lihat Semua Artikel
            </a>
        </div>
        @endif
    </div>
</section>

<!-- Popular Tags -->
<section class="py-12 px-4 bg-white border-t border-gray-100">
    <div class="container mx-auto">
        <h3 class="text-xl font-bold mb-6 text-center text-gray-900">
            Tag Populer Lainnya
        </h3>

        <div class="flex flex-wrap justify-center gap-2">
            @php
            $popularTags = ['amdal', 'ukl-upl', 'lb3', 'oss', 'pbg', 'slf', 'perizinan-industri', 'lingkungan-hidup', 'regulasi-2025', 'tips-perizinan'];
            @endphp

            @foreach($popularTags as $popularTag)
                @if($popularTag !== $tag)
                <a href="{{ route($blogTagRoute, $popularTag) }}" class="px-4 py-2 bg-gray-100 hover:bg-primary/10 text-gray-600 hover:text-primary rounded-full text-sm font-medium transition">
                    #{{ $popularTag }}
                </a>
                @endif
            @endforeach
        </div>
    </div>
</section>

@endsection
