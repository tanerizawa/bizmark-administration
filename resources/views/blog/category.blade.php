@extends('landing.layout')

@section('content')

<!-- Breadcrumbs -->
<section class="pt-24 pb-4 px-4 bg-gray-50 border-b border-gray-100">
    <div class="container mx-auto">
        <nav class="flex items-center space-x-2 text-sm text-gray-500">
            <a href="/" class="text-gray-600 hover:text-primary transition">
                <i class="fas fa-home mr-1"></i>{{ __('blog.breadcrumb.home') }}
            </a>
            <span class="text-gray-300">/</span>
            <a href="{{ route($blogIndexRoute) }}" class="text-gray-600 hover:text-primary transition">
                {{ __('blog.breadcrumb.blog') }}
            </a>
            <span class="text-gray-300">/</span>
            <span class="text-gray-900 font-medium">{{ $categoryLabel }}</span>
        </nav>
    </div>
</section>

<!-- Category Header -->
<section class="py-14 px-4 bg-white">
    <div class="container mx-auto text-center">
        <div class="mb-5">
            <a href="{{ route($blogIndexRoute) }}" class="text-gray-500 hover:text-primary transition inline-flex items-center text-sm font-medium">
                <i class="fas fa-arrow-left mr-2"></i>{{ __('blog.category_page.back_to_blog') }}
            </a>
        </div>
        
        <span class="inline-flex items-center gap-2 px-4 py-1.5 bg-primary/5 rounded-full text-xs font-bold uppercase tracking-widest text-primary mb-5">
            <i class="fas fa-folder"></i> {{ __('blog.category_page.category_badge') }}
        </span>
        
        <h1 class="text-4xl md:text-5xl font-bold mb-4 text-gray-900">
            {{ $categoryLabel }}
        </h1>
        
        <p class="text-lg text-gray-600 max-w-2xl mx-auto">
            {{ __('blog.category_descriptions.' . $category, ['default' => __('blog.category_descriptions.general')]) }}
        </p>
    </div>
</section>

<!-- Articles Grid -->
<section class="py-14 px-4 bg-gray-50">
    <div class="container mx-auto">
        @if($articles->count() > 0)
        <div class="mb-8 text-center text-gray-500 text-sm">
            <p>{{ __('blog.category_page.found_articles') }} <span class="text-primary font-semibold">{{ $articles->total() }}</span> {{ __('blog.category_page.articles') }}</p>
        </div>
        
        <div class="grid md:grid-cols-2 xl:grid-cols-3 gap-6 mb-10">
            @foreach($articles as $article)
            <article class="group bg-white rounded-2xl overflow-hidden shadow-soft hover:shadow-lg transition-all duration-300">
                <div class="relative h-48 overflow-hidden">
                    @if($article->featured_image)
                    <img src="{{ Storage::url($article->featured_image) }}" alt="{{ $article->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" loading="lazy">
                    @else
                    <div class="w-full h-full bg-gradient-to-br from-primary/10 to-secondary/10 flex items-center justify-center">
                        <i class="fas fa-newspaper text-4xl text-gray-200"></i>
                    </div>
                    @endif
                    <div class="absolute top-3 left-3">
                        <span class="px-3 py-1 rounded-full bg-primary text-white text-xs font-semibold">
                            {{ $article->category_label }}
                        </span>
                    </div>
                </div>
                
                <div class="p-6">
                    <div class="flex items-center gap-3 mb-3 text-xs text-gray-400">
                        <span><i class="far fa-calendar mr-1"></i>{{ $article->published_at->format('d M Y') }}</span>
                        <span><i class="far fa-clock mr-1"></i>{{ $article->reading_time }} {{ __('blog.category_page.reading_time') }}</span>
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
                        @foreach(array_slice($article->tags, 0, 3) as $tag)
                        <a href="{{ route($blogTagRoute, $tag) }}" class="text-xs px-2.5 py-1 bg-gray-100 hover:bg-primary/10 rounded-full text-gray-500 hover:text-primary transition">
                            #{{ $tag }}
                        </a>
                        @endforeach
                    </div>
                    @endif
                    
                    <a href="{{ route($blogArticleRoute, $article->slug) }}" class="text-primary font-semibold text-sm inline-flex items-center group/link">
                        {{ __('blog.actions.read_more') }}
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
                <i class="fas fa-folder-open text-2xl"></i>
            </div>
            <h3 class="text-2xl font-bold mb-2 text-gray-900">{{ __('blog.category_page.no_articles') }}</h3>
            <p class="text-gray-500 mb-6">{{ __('blog.category_page.no_articles_description') }}</p>
            <a href="{{ route($blogIndexRoute) }}" class="btn btn-primary inline-flex items-center">
                <i class="fas fa-arrow-left mr-2"></i>
                {{ __('blog.category_page.view_all_articles') }}
            </a>
        </div>
        @endif
    </div>
</section>

<!-- Other Categories -->
<section class="py-12 px-4 bg-white border-t border-gray-100">
    <div class="container mx-auto">
        <h3 class="text-xl font-bold mb-6 text-center text-gray-900">
            {{ __('blog.category_page.explore_categories') }}
        </h3>
        
        <div class="flex flex-wrap justify-center gap-3">
            @if($category !== 'perizinan')
            <a href="{{ route($blogCategoryRoute, 'perizinan') }}" class="px-5 py-2.5 bg-gray-100 hover:bg-primary/10 rounded-full transition font-medium text-gray-700 hover:text-primary text-sm">
                <i class="fas fa-file-contract mr-2 text-primary"></i>{{ __('blog.category_names.perizinan') }}
            </a>
            @endif
            @if($category !== 'lingkungan')
            <a href="{{ route($blogCategoryRoute, 'lingkungan') }}" class="px-5 py-2.5 bg-gray-100 hover:bg-primary/10 rounded-full transition font-medium text-gray-700 hover:text-primary text-sm">
                <i class="fas fa-leaf mr-2 text-green-600"></i>{{ __('blog.category_names.lingkungan') }}
            </a>
            @endif
            @if($category !== 'regulasi')
            <a href="{{ route($blogCategoryRoute, 'regulasi') }}" class="px-5 py-2.5 bg-gray-100 hover:bg-primary/10 rounded-full transition font-medium text-gray-700 hover:text-primary text-sm">
                <i class="fas fa-gavel mr-2 text-purple-600"></i>{{ __('blog.category_names.regulasi') }}
            </a>
            @endif
            @if($category !== 'tips')
            <a href="{{ route($blogCategoryRoute, 'tips') }}" class="px-5 py-2.5 bg-gray-100 hover:bg-primary/10 rounded-full transition font-medium text-gray-700 hover:text-primary text-sm">
                <i class="fas fa-lightbulb mr-2 text-secondary"></i>{{ __('blog.category_names.tips') }}
            </a>
            @endif
        </div>
    </div>
</section>

@endsection
