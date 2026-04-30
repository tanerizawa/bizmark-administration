@extends('landing.layout')

@php
    $locale = $article->language ?? app()->getLocale();
    $blogIndexRoute = $locale === 'en' ? 'blog.index.en' : 'blog.index.id';
    $blogArticleRoute = $locale === 'en' ? 'blog.article.en' : 'blog.article.id';
    $blogCategoryRoute = $locale === 'en' ? 'blog.category.en' : 'blog.category.id';
    $blogTagRoute = $locale === 'en' ? 'blog.tag.en' : 'blog.tag.id';
@endphp

@section('title', $article->meta_title ?: $article->title)
@section('meta_description', $article->meta_description ?: $article->excerpt)
@section('meta_keywords', $article->meta_keywords ?: '')
@section('og_title', $article->title)
@section('og_description', $article->excerpt)
@section('og_image', $article->featured_image_url ?: asset('images/og-image-id.jpg'))

@section('content')
<section class="relative overflow-hidden pt-28 pb-12" style="background:linear-gradient(135deg,var(--surface-warm) 0%, var(--surface-cool) 100%);">
    <div class="container-wide">
        <div class="max-w-4xl">
            <nav class="text-sm mb-5" style="color:var(--text-tertiary);">
                <a href="{{ route($blogIndexRoute) }}" class="link-primary">{{ $locale === 'en' ? 'Insights' : 'Wawasan' }}</a>
                <span class="mx-2">/</span>
                <a href="{{ route($blogCategoryRoute, $article->category) }}" class="link-primary">{{ $article->category_label }}</a>
            </nav>

            <h1 class="text-[clamp(1.75rem,4vw,3rem)] font-extrabold tracking-tight mb-4" style="color:var(--text-primary);line-height:1.12;">
                {{ $article->title }}
            </h1>

            <div class="flex flex-wrap items-center gap-x-4 gap-y-2 text-sm mb-6" style="color:var(--text-secondary);">
                <span><i class="far fa-calendar mr-1" style="color:var(--text-tertiary);"></i>{{ optional($article->published_at)->format('d M Y') }}</span>
                <span><i class="far fa-clock mr-1" style="color:var(--text-tertiary);"></i>{{ $article->reading_time }} {{ $locale === 'en' ? 'min read' : 'menit' }}</span>
                <span><i class="far fa-eye mr-1" style="color:var(--text-tertiary);"></i>{{ number_format($article->views) }}</span>
                @if($article->author)
                    <span><i class="far fa-user mr-1" style="color:var(--text-tertiary);"></i>{{ $article->author->name }}</span>
                @endif
            </div>
        </div>
    </div>
</section>

<section class="section pt-0">
    <div class="container-wide">
        <div class="grid lg:grid-cols-12 gap-10 items-start">
            <article class="lg:col-span-8">
                @if($article->featured_image_url)
                    <div class="card p-0 overflow-hidden mb-6">
                        <img src="{{ $article->featured_image_url }}" alt="{{ $article->title }}" class="w-full h-auto" loading="lazy" width="1200" height="675">
                    </div>
                @endif

                <div class="card">
                    <div class="content-prose">
                        {!! $article->content !!}
                    </div>

                    @if(!empty($article->tags))
                        <div class="mt-8 pt-6 border-t" style="border-color:var(--border-light);">
                            <div class="flex flex-wrap gap-2">
                                @foreach($article->tags as $tag)
                                    <a href="{{ route($blogTagRoute, $tag) }}" class="chip">#{{ $tag }}</a>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </article>

            <aside class="lg:col-span-4">
                <div class="card mb-6">
                    <div class="text-xs font-semibold uppercase tracking-widest mb-2" style="color:var(--text-tertiary);">{{ $locale === 'en' ? 'Next step' : 'Langkah berikutnya' }}</div>
                    <div class="text-base font-bold mb-3" style="color:var(--text-primary);">{{ $locale === 'en' ? 'Need a permit roadmap?' : 'Butuh peta jalan perizinan?' }}</div>
                    <div class="text-sm mb-5" style="color:var(--text-secondary);">
                        {{ $locale === 'en' ? 'Share your business context and we will recommend the required permits and priorities.' : 'Ceritakan konteks usaha Anda dan kami rekomendasikan izin yang dibutuhkan serta prioritasnya.' }}
                    </div>
                    <a href="{{ route('landing.service-inquiry.create') }}" class="btn btn-secondary w-full">
                        <i class="fas fa-robot"></i> {{ $locale === 'en' ? 'Free Permit Analysis' : 'Analisis Perizinan Gratis' }}
                    </a>
                    <a href="{{ route('contact.index') }}" class="btn btn-outline-primary w-full mt-3">
                        <i class="fas fa-envelope"></i> {{ $locale === 'en' ? 'Contact team' : 'Hubungi tim' }}
                    </a>
                </div>

                @if($relatedArticles->count() > 0)
                    <div class="card">
                        <div class="text-sm font-bold mb-4" style="color:var(--text-primary);">{{ $locale === 'en' ? 'Related articles' : 'Artikel terkait' }}</div>
                        <div class="space-y-4">
                            @foreach($relatedArticles as $ra)
                                <a href="{{ route($blogArticleRoute, $ra->slug) }}" class="block rounded-xl p-4 hover:shadow-md transition" style="border:1px solid var(--border-light);background:var(--surface);">
                                    <div class="text-xs mb-1" style="color:var(--text-tertiary);">{{ optional($ra->published_at)->format('d M Y') }}</div>
                                    <div class="text-sm font-semibold" style="color:var(--text-primary);">{{ $ra->title }}</div>
                                    <div class="text-xs mt-2" style="color:var(--text-secondary);">{{ Str::limit($ra->excerpt, 100) }}</div>
                                </a>
                            @endforeach
                        </div>
                        <div class="mt-5 pt-4 border-t" style="border-color:var(--border-light);">
                            <a href="{{ route($blogIndexRoute) }}" class="link-primary text-sm inline-flex items-center">{{ $locale === 'en' ? 'View all' : 'Lihat semua' }} <i class="fas fa-arrow-right ml-2"></i></a>
                        </div>
                    </div>
                @endif
            </aside>
        </div>
    </div>
</section>
@endsection

