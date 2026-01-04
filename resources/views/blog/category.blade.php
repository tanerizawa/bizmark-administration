@extends('landing.layout')

@section('content')

<!-- Breadcrumbs -->
<section class="pt-24 pb-4 px-4 bg-gray-50 dark:bg-gray-900">
    <div class="container mx-auto">
        <nav class="flex items-center space-x-2 text-sm text-gray-600 dark:text-gray-400" data-aos="fade-right">
            <a href="/" class="hover:text-blue-600 dark:hover:text-blue-400 transition">
                <i class="fas fa-home"></i> {{ __('blog.breadcrumb.home') }}
            </a>
            <span>/</span>
            <a href="{{ route($blogIndexRoute) }}" class="hover:text-blue-600 dark:hover:text-blue-400 transition">
                {{ __('blog.breadcrumb.blog') }}
            </a>
            <span>/</span>
            <span class="text-gray-900 dark:text-white font-medium">{{ $categoryLabel }}</span>
        </nav>
    </div>
</section>

<!-- Category Header -->
<section class="py-12 px-4 bg-gradient-to-br from-blue-50 via-purple-50 to-green-50 dark:from-blue-900/10 dark:via-purple-900/10 dark:to-green-900/10">
    <div class="container mx-auto text-center">
        <div class="mb-6" data-aos="fade-up">
            <a href="{{ route($blogIndexRoute) }}" class="text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 transition inline-flex items-center">
                <i class="fas fa-arrow-left mr-2"></i>{{ __('blog.category_page.back_to_blog') }}
            </a>
        </div>
        
        <div class="inline-block px-6 py-3 bg-blue-100 dark:bg-blue-900/30 backdrop-blur-sm rounded-full mb-6" data-aos="fade-up" data-aos-delay="100">
            <i class="fas fa-folder mr-2 text-blue-600 dark:text-blue-400"></i>
            <span class="text-sm font-semibold text-blue-800 dark:text-blue-300">{{ __('blog.category_page.category_badge') }}</span>
        </div>
        
        <h1 class="text-5xl md:text-6xl font-bold mb-6 text-gray-900 dark:text-white" data-aos="fade-up" data-aos-delay="200">
            {{ $categoryLabel }}
        </h1>
        
        <p class="text-xl text-gray-600 dark:text-gray-300 max-w-3xl mx-auto" data-aos="fade-up" data-aos-delay="300">
            {{ __('blog.category_descriptions.' . $category, ['default' => __('blog.category_descriptions.general')]) }}
        </p>
    </div>
</section>

<!-- Articles Grid -->
<section class="py-20 px-4 bg-white dark:bg-gray-900">
    <div class="container mx-auto">
        @if($articles->count() > 0)
        <!-- Article Count -->
        <div class="mb-8 text-center text-gray-600 dark:text-gray-400" data-aos="fade-up">
            <p>{{ __('blog.category_page.found_articles') }} <span class="text-blue-600 dark:text-blue-400 font-semibold">{{ $articles->total() }}</span> {{ __('blog.category_page.articles') }}</p>
        </div>
        
        <div class="grid md:grid-cols-3 gap-8 mb-12">
            @foreach($articles as $article)
            <article class="card bg-white dark:bg-gray-800 rounded-2xl overflow-hidden shadow-soft hover:shadow-soft-lg transition-all" data-aos="fade-up" data-aos-delay="{{ ($loop->index % 3) * 100 }}">
                <div class="relative h-48 overflow-hidden">
                    @if($article->featured_image)
                    <img src="{{ Storage::url($article->featured_image) }}" alt="{{ $article->title }}" class="w-full h-full object-cover">
                    @else
                    <div class="w-full h-full bg-gradient-to-br from-blue-100 to-green-100 dark:from-blue-900/20 dark:to-green-900/20 flex items-center justify-center">
                        <i class="fas fa-newspaper text-6xl text-gray-400 dark:text-gray-600"></i>
                    </div>
                    @endif
                    
                    <!-- Category Badge -->
                    <div class="absolute top-4 left-4">
                        <span class="px-3 py-1 bg-blue-600 dark:bg-blue-500 backdrop-blur-sm text-white rounded-full text-xs font-semibold">
                            {{ $article->category_label }}
                        </span>
                    </div>
                </div>
                
                <div class="p-6">
                    <div class="flex items-center gap-3 mb-3 text-xs text-gray-500 dark:text-gray-400">
                        <span><i class="far fa-calendar mr-1"></i>{{ $article->published_at->format('d M Y') }}</span>
                        <span><i class="far fa-clock mr-1"></i>{{ $article->reading_time }} {{ __('blog.category_page.reading_time') }}</span>
                        <span><i class="far fa-eye mr-1"></i>{{ number_format($article->views) }}</span>
                    </div>
                    
                    <h3 class="text-xl font-bold mb-3 leading-tight text-gray-900 dark:text-white hover:text-blue-600 dark:hover:text-blue-400 transition">
                        <a href="{{ route($blogArticleRoute, $article->slug) }}">{{ $article->title }}</a>
                    </h3>
                    
                    <p class="text-gray-600 dark:text-gray-400 text-sm mb-4 leading-relaxed">
                        {{ Str::limit($article->excerpt, 120) }}
                    </p>
                    
                    <!-- Tags -->
                    @if($article->tags && count($article->tags) > 0)
                    <div class="flex flex-wrap gap-2 mb-4">
                        @foreach(array_slice($article->tags, 0, 3) as $tag)
                        <a href="{{ route($blogTagRoute, $tag) }}" class="text-xs px-2 py-1 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-full text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 transition">
                            #{{ $tag }}
                        </a>
                        @endforeach
                    </div>
                    @endif
                    
                    <a href="{{ route($blogArticleRoute, $article->slug) }}" class="text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 font-semibold text-sm inline-flex items-center group">
                        {{ __('blog.actions.read_more') }}
                        <i class="fas fa-arrow-right ml-2 group-hover:translate-x-1 transition-transform"></i>
                    </a>
                </div>
            </article>
            @endforeach
        </div>
        
        <!-- Pagination -->
        @if($articles->hasPages())
        <div class="flex justify-center">
            {{ $articles->links() }}
        </div>
        @endif
        @else
        <div class="text-center py-20">
            <i class="fas fa-folder-open text-6xl text-gray-400 dark:text-gray-600 mb-4"></i>
            <h3 class="text-2xl font-bold mb-2 text-gray-900 dark:text-white">{{ __('blog.category_page.no_articles') }}</h3>
            <p class="text-gray-600 dark:text-gray-400 mb-8">{{ __('blog.category_page.no_articles_description') }}</p>
            <a href="{{ route($blogIndexRoute) }}" class="btn btn-primary inline-flex items-center">
                <i class="fas fa-arrow-left mr-2"></i>
                {{ __('blog.category_page.view_all_articles') }}
            </a>
        </div>
        @endif
    </div>
</section>

<!-- Other Categories -->
<section class="py-12 px-4 bg-gray-50 dark:bg-gray-800">
    <div class="container mx-auto">
        <h3 class="text-2xl font-bold mb-8 text-center text-gray-900 dark:text-white" data-aos="fade-up">
            {{ __('blog.category_page.explore_categories') }}
        </h3>
        
        <div class="flex flex-wrap justify-center gap-4" data-aos="fade-up" data-aos-delay="100">
            @if($category !== 'perizinan')
            <a href="{{ route($blogCategoryRoute, 'perizinan') }}" class="px-6 py-3 bg-white dark:bg-gray-700 rounded-full hover:bg-blue-50 dark:hover:bg-blue-900/30 transition font-medium text-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-600 hover:border-blue-300 dark:hover:border-blue-600">
                <i class="fas fa-file-contract mr-2 text-blue-600 dark:text-blue-400"></i>{{ __('blog.category_names.perizinan') }}
            </a>
            @endif
            @if($category !== 'lingkungan')
            <a href="{{ route($blogCategoryRoute, 'lingkungan') }}" class="px-6 py-3 bg-white dark:bg-gray-700 rounded-full hover:bg-green-50 dark:hover:bg-green-900/30 transition font-medium text-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-600 hover:border-green-300 dark:hover:border-green-600">
                <i class="fas fa-leaf mr-2 text-green-600 dark:text-green-400"></i>{{ __('blog.category_names.lingkungan') }}
            </a>
            @endif
            @if($category !== 'regulasi')
            <a href="{{ route($blogCategoryRoute, 'regulasi') }}" class="px-6 py-3 bg-white dark:bg-gray-700 rounded-full hover:bg-purple-50 dark:hover:bg-purple-900/30 transition font-medium text-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-600 hover:border-purple-300 dark:hover:border-purple-600">
                <i class="fas fa-gavel mr-2 text-purple-600 dark:text-purple-400"></i>{{ __('blog.category_names.regulasi') }}
            </a>
            @endif
            @if($category !== 'tips')
            <a href="{{ route($blogCategoryRoute, 'tips') }}" class="px-6 py-3 bg-white dark:bg-gray-700 rounded-full hover:bg-orange-50 dark:hover:bg-orange-900/30 transition font-medium text-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-600 hover:border-orange-300 dark:hover:border-orange-600">
                <i class="fas fa-lightbulb mr-2 text-orange-600 dark:text-orange-400"></i>{{ __('blog.category_names.tips') }}
            </a>
            @endif
        </div>
    </div>
</section>

@endsection
