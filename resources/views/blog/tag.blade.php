@extends('landing.layout')

@section('content')

<!-- Tag Header -->
<section class="py-20 px-4 bg-gradient-to-br from-apple-blue/10 via-purple-500/10 to-apple-green/10">
    <div class="container mx-auto text-center">
        <div class="mb-6" data-aos="fade-up">
            <a href="{{ route('blog.index') }}" class="text-gray-400 hover:text-apple-blue transition">
                <i class="fas fa-arrow-left mr-2"></i>Kembali ke Blog
            </a>
        </div>
        
        <div class="inline-block px-6 py-3 bg-apple-blue/20 backdrop-blur-sm rounded-full mb-6" data-aos="fade-up" data-aos-delay="100">
            <i class="fas fa-tag mr-2 text-apple-blue"></i>
            <span class="text-sm font-semibold">Tag</span>
        </div>
        
        <h1 class="text-5xl md:text-6xl font-bold mb-6" data-aos="fade-up" data-aos-delay="200">
            #{{ $tag }}
        </h1>
        
        <p class="text-xl text-gray-300 max-w-3xl mx-auto" data-aos="fade-up" data-aos-delay="300">
            Artikel yang ditandai dengan <span class="text-apple-blue">#{{ $tag }}</span>
        </p>
    </div>
</section>

<!-- Articles Grid -->
<section class="py-20 px-4 bg-dark-bg">
    <div class="container mx-auto">
        @if($articles->count() > 0)
        <!-- Article Count -->
        <div class="mb-8 text-center text-gray-400" data-aos="fade-up">
            <p>Ditemukan <span class="text-apple-blue font-semibold">{{ $articles->total() }}</span> artikel</p>
        </div>
        
        <div class="grid md:grid-cols-3 gap-8 mb-12">
            @foreach($articles as $article)
            <article class="article-card glass rounded-2xl overflow-hidden" data-aos="fade-up" data-aos-delay="{{ ($loop->index % 3) * 100 }}">
                <div class="relative h-48 overflow-hidden">
                    @if($article->featured_image)
                    <img src="{{ Storage::url($article->featured_image) }}" alt="{{ $article->title }}" class="w-full h-full object-cover">
                    @else
                    <div class="w-full h-full bg-gradient-to-br from-apple-blue/20 to-apple-green/20 flex items-center justify-center">
                        <i class="fas fa-newspaper text-6xl text-gray-600"></i>
                    </div>
                    @endif
                    
                    <!-- Category Badge -->
                    <div class="absolute top-4 left-4">
                        <a href="{{ route('blog.category', $article->category) }}" class="px-3 py-1 bg-apple-blue/90 backdrop-blur-sm text-white rounded-full text-xs font-semibold hover:bg-apple-blue transition">
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
                    
                    <h3 class="text-xl font-bold mb-3 leading-tight hover:text-apple-blue transition">
                        <a href="{{ route('blog.article', $article->slug) }}">{{ $article->title }}</a>
                    </h3>
                    
                    <p class="text-gray-400 text-sm mb-4 leading-relaxed">
                        {{ Str::limit($article->excerpt, 120) }}
                    </p>
                    
                    <!-- Tags -->
                    @if($article->tags && count($article->tags) > 0)
                    <div class="flex flex-wrap gap-2 mb-4">
                        @foreach(array_slice($article->tags, 0, 3) as $articleTag)
                        <a href="{{ route('blog.tag', $articleTag) }}" class="text-xs px-2 py-1 {{ $articleTag === $tag ? 'bg-apple-blue/20 text-apple-blue' : 'bg-white/5 text-gray-400' }} hover:bg-white/10 rounded-full hover:text-apple-blue transition">
                            #{{ $articleTag }}
                        </a>
                        @endforeach
                    </div>
                    @endif
                    
                    <a href="{{ route('blog.article', $article->slug) }}" class="text-apple-blue hover:text-apple-blue-dark font-semibold text-sm inline-flex items-center group">
                        Baca Selengkapnya 
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
            <i class="fas fa-tag text-6xl text-gray-700 mb-4"></i>
            <h3 class="text-2xl font-bold mb-2">Belum Ada Artikel</h3>
            <p class="text-gray-400 mb-8">Artikel dengan tag ini akan segera hadir.</p>
            <a href="{{ route('blog.index') }}" class="btn-primary inline-flex items-center">
                <i class="fas fa-arrow-left mr-2"></i>
                Lihat Semua Artikel
            </a>
        </div>
        @endif
    </div>
</section>

<!-- Popular Tags -->
<section class="py-12 px-4 bg-dark-bg-secondary">
    <div class="container mx-auto">
        <h3 class="text-2xl font-bold mb-8 text-center" data-aos="fade-up">
            Tag Populer Lainnya
        </h3>
        
        <div class="flex flex-wrap justify-center gap-3" data-aos="fade-up" data-aos-delay="100">
            @php
            $popularTags = ['amdal', 'ukl-upl', 'lb3', 'oss', 'pbg', 'slf', 'perizinan-industri', 'lingkungan-hidup', 'regulasi-2025', 'tips-perizinan'];
            @endphp
            
            @foreach($popularTags as $popularTag)
                @if($popularTag !== $tag)
                <a href="{{ route('blog.tag', $popularTag) }}" class="px-4 py-2 glass rounded-full hover:bg-apple-blue/20 transition text-sm font-medium">
                    #{{ $popularTag }}
                </a>
                @endif
            @endforeach
        </div>
    </div>
</section>

@endsection
