@extends('landing.layout')

@php
    $locale = app()->getLocale();
    $blogArticleRoute = $locale === 'en' ? 'blog.article.en' : 'blog.article.id';
    $blogCategoryRoute = $locale === 'en' ? 'blog.category.en' : 'blog.category.id';
    $blogTagRoute = $locale === 'en' ? 'blog.tag.en' : 'blog.tag.id';
@endphp

@section('title', $locale === 'en' ? 'Insights - Bizmark.ID' : 'Wawasan - Bizmark.ID')
@section('meta_description', $locale === 'en'
    ? 'Practical guides and insights about permits, compliance, and business operations in Indonesia.'
    : 'Panduan praktis dan wawasan seputar perizinan, compliance, dan operasional bisnis di Indonesia.')

@section('content')
<section class="relative overflow-hidden pt-28 pb-16" style="background:linear-gradient(135deg,var(--surface-warm) 0%, var(--surface-cool) 100%);">
    <div class="container-wide">
        <span class="section-badge mb-4">{{ $locale === 'en' ? 'Insights' : 'Wawasan' }}</span>
        <h1 class="section-title mb-4">{{ $locale === 'en' ? 'Guides, checklists, and regulatory updates' : 'Panduan, checklist, dan update regulasi' }}</h1>
        <p class="section-description mb-0" style="margin-left:0;">
            {{ $locale === 'en'
                ? 'Explore articles written to help you plan permits, reduce risk, and execute faster.'
                : 'Jelajahi artikel yang membantu Anda merencanakan izin, mengurangi risiko, dan mengeksekusi lebih cepat.' }}
        </p>
    </div>
</section>

<section class="section">
    <div class="container-wide">
        @if($articles->count() === 0)
            <div class="card text-center">
                <h2 class="text-xl font-bold mb-2" style="color:var(--text-primary);">{{ $locale === 'en' ? 'No articles yet' : 'Belum ada artikel' }}</h2>
                <p class="mb-0" style="color:var(--text-secondary);">{{ $locale === 'en' ? 'Please check back soon.' : 'Silakan cek kembali nanti.' }}</p>
            </div>
        @else
            <div class="grid md:grid-cols-2 xl:grid-cols-3 gap-6">
                @foreach($articles as $article)
                    <article class="card p-0 overflow-hidden">
                        <a href="{{ route($blogArticleRoute, $article->slug) }}" class="block">
                            <div class="relative" style="aspect-ratio: 16/9; background:var(--surface-cool);">
                                @if($article->featured_image_url)
                                    <img src="{{ $article->featured_image_url }}" alt="{{ $article->title }}" class="w-full h-full object-cover" loading="lazy" width="1200" height="675">
                                @else
                                    <div class="w-full h-full flex items-center justify-center" style="background:linear-gradient(135deg, rgba(15,23,42,.08), rgba(14,165,233,.12));">
                                        <i class="fas fa-newspaper text-4xl" style="color:rgba(15,23,42,.25);"></i>
                                    </div>
                                @endif
                                <div class="absolute top-3 left-3">
                                    <span class="chip active" style="background:rgba(15,23,42,.75);border-color:transparent;color:#fff;">
                                        {{ $article->category_label }}
                                    </span>
                                </div>
                            </div>
                        </a>

                        <div class="p-6">
                            <div class="flex flex-wrap items-center gap-x-4 gap-y-2 text-xs mb-3" style="color:var(--text-tertiary);">
                                <span><i class="far fa-calendar mr-1"></i>{{ optional($article->published_at)->format('d M Y') }}</span>
                                <span><i class="far fa-clock mr-1"></i>{{ $article->reading_time }} {{ $locale === 'en' ? 'min read' : 'menit' }}</span>
                                <span><i class="far fa-eye mr-1"></i>{{ number_format($article->views) }}</span>
                            </div>

                            <h2 class="text-lg font-bold mb-2" style="color:var(--text-primary);">
                                <a href="{{ route($blogArticleRoute, $article->slug) }}" class="hover:underline" style="text-underline-offset:3px;">
                                    {{ $article->title }}
                                </a>
                            </h2>
                            <p class="text-sm mb-4" style="color:var(--text-secondary);">{{ Str::limit($article->excerpt, 140) }}</p>

                            @if(!empty($article->tags))
                                <div class="flex flex-wrap gap-2 mb-4">
                                    @foreach(array_slice($article->tags, 0, 4) as $tag)
                                        <a href="{{ route($blogTagRoute, $tag) }}" class="chip">#{{ $tag }}</a>
                                    @endforeach
                                </div>
                            @endif

                            <div class="pt-3 border-t" style="border-color:var(--border-light);">
                                <a href="{{ route($blogArticleRoute, $article->slug) }}" class="link-primary text-sm inline-flex items-center">
                                    {{ $locale === 'en' ? 'Read' : 'Baca' }} <i class="fas fa-arrow-right ml-2"></i>
                                </a>
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

