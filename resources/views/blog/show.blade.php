@extends('landing.layout')

@section('content')

<!-- Breadcrumbs -->
<section class="pt-24 pb-4 px-4 bg-dark-bg">
    <div class="container mx-auto max-w-4xl">
        <nav class="flex items-center space-x-2 text-sm text-gray-400" data-aos="fade-right">
            <a href="/" class="hover:text-apple-blue transition">
                <i class="fas fa-home"></i> Beranda
            </a>
            <span>/</span>
            <a href="{{ route('blog.index') }}" class="hover:text-apple-blue transition">
                Blog
            </a>
            <span>/</span>
            <a href="{{ route('blog.category', $article->category) }}" class="hover:text-apple-blue transition">
                {{ $article->category_label }}
            </a>
            <span>/</span>
            <span class="text-white truncate max-w-xs">{{ Str::limit($article->title, 40) }}</span>
        </nav>
    </div>
</section>

<!-- Article Header -->
<section class="py-8 px-4 bg-gradient-to-br from-apple-blue/10 via-purple-500/10 to-apple-green/10">
    <div class="container mx-auto max-w-4xl">
        <!-- Back Button -->
        <div class="mb-6" data-aos="fade-up">
            <a href="{{ route('blog.index') }}" class="text-gray-400 hover:text-apple-blue transition">
                <i class="fas fa-arrow-left mr-2"></i>Kembali ke Blog
            </a>
        </div>
        
        <!-- Category & Date -->
        <div class="flex items-center gap-4 mb-6" data-aos="fade-up" data-aos-delay="100">
            <a href="{{ route('blog.category', $article->category) }}" class="px-4 py-2 bg-apple-blue/90 backdrop-blur-sm text-white rounded-full text-sm font-semibold hover:bg-apple-blue transition">
                {{ $article->category_label }}
            </a>
            <span class="text-gray-400"><i class="far fa-calendar mr-2"></i>{{ $article->published_at->format('d F Y') }}</span>
            <span class="text-gray-400"><i class="far fa-clock mr-2"></i>{{ $article->reading_time }} menit</span>
            <span class="text-gray-400"><i class="far fa-eye mr-2"></i>{{ number_format($article->views) }} views</span>
        </div>
        
        <!-- Title -->
        <h1 class="text-4xl md:text-5xl font-bold mb-6 leading-tight" data-aos="fade-up" data-aos-delay="200">
            {{ $article->title }}
        </h1>
        
        <!-- Excerpt -->
        <p class="text-xl text-gray-300 leading-relaxed" data-aos="fade-up" data-aos-delay="300">
            {{ $article->excerpt }}
        </p>
    </div>
</section>

<!-- Featured Image -->
@if($article->featured_image)
<section class="px-4 -mt-8">
    <div class="container mx-auto max-w-4xl">
        <div class="rounded-2xl overflow-hidden glass" data-aos="zoom-in">
            <img src="{{ Storage::url($article->featured_image) }}" alt="{{ $article->title }}" class="w-full h-auto">
        </div>
    </div>
</section>
@endif

<!-- Article Content -->
<section class="py-12 px-4 bg-dark-bg">
    <div class="container mx-auto max-w-4xl">
        <article class="prose prose-invert prose-lg max-w-none" data-aos="fade-up">
            {!! $article->content !!}
        </article>
        
        <!-- Tags -->
        @if($article->tags && count($article->tags) > 0)
        <div class="mt-12 pt-8 border-t border-white/10" data-aos="fade-up">
            <h4 class="text-lg font-bold mb-4">Tags:</h4>
            <div class="flex flex-wrap gap-3">
                @foreach($article->tags as $tag)
                <a href="{{ route('blog.tag', $tag) }}" class="px-4 py-2 bg-white/5 hover:bg-apple-blue/20 rounded-full text-sm font-medium transition">
                    #{{ $tag }}
                </a>
                @endforeach
            </div>
        </div>
        @endif
        
        <!-- Share Buttons -->
        <div class="mt-8 pt-8 border-t border-white/10" data-aos="fade-up">
            <h4 class="text-lg font-bold mb-4">Bagikan Artikel:</h4>
            <div class="flex gap-3">
                <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(route('blog.article', $article->slug)) }}" 
                   target="_blank"
                   class="w-12 h-12 bg-blue-600 hover:bg-blue-700 rounded-full flex items-center justify-center transition">
                    <i class="fab fa-facebook-f"></i>
                </a>
                <a href="https://twitter.com/intent/tweet?url={{ urlencode(route('blog.article', $article->slug)) }}&text={{ urlencode($article->title) }}" 
                   target="_blank"
                   class="w-12 h-12 bg-sky-500 hover:bg-sky-600 rounded-full flex items-center justify-center transition">
                    <i class="fab fa-twitter"></i>
                </a>
                <a href="https://www.linkedin.com/shareArticle?mini=true&url={{ urlencode(route('blog.article', $article->slug)) }}&title={{ urlencode($article->title) }}" 
                   target="_blank"
                   class="w-12 h-12 bg-blue-700 hover:bg-blue-800 rounded-full flex items-center justify-center transition">
                    <i class="fab fa-linkedin-in"></i>
                </a>
                <a href="https://wa.me/?text={{ urlencode($article->title . ' - ' . route('blog.article', $article->slug)) }}" 
                   target="_blank"
                   class="w-12 h-12 bg-apple-green hover:bg-green-600 rounded-full flex items-center justify-center transition">
                    <i class="fab fa-whatsapp"></i>
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Related Articles -->
@if($relatedArticles->count() > 0)
<section class="py-20 px-4 bg-dark-bg-secondary">
    <div class="container mx-auto max-w-6xl">
        <h2 class="text-3xl md:text-4xl font-bold mb-12 text-center" data-aos="fade-up">
            Artikel Terkait
        </h2>
        
        <div class="grid md:grid-cols-3 gap-8">
            @foreach($relatedArticles as $related)
            <article class="article-card glass rounded-2xl overflow-hidden" data-aos="fade-up" data-aos-delay="{{ $loop->index * 100 }}">
                <div class="relative h-48 overflow-hidden">
                    @if($related->featured_image)
                    <img src="{{ Storage::url($related->featured_image) }}" alt="{{ $related->title }}" class="w-full h-full object-cover">
                    @else
                    <div class="w-full h-full bg-gradient-to-br from-apple-blue/20 to-apple-green/20 flex items-center justify-center">
                        <i class="fas fa-newspaper text-6xl text-gray-600"></i>
                    </div>
                    @endif
                </div>
                
                <div class="p-6">
                    <div class="flex items-center gap-3 mb-3 text-xs text-gray-400">
                        <span><i class="far fa-calendar mr-1"></i>{{ $related->published_at->format('d M Y') }}</span>
                        <span><i class="far fa-clock mr-1"></i>{{ $related->reading_time }} min</span>
                    </div>
                    
                    <h3 class="text-xl font-bold mb-3 leading-tight hover:text-apple-blue transition">
                        <a href="{{ route('blog.article', $related->slug) }}">{{ $related->title }}</a>
                    </h3>
                    
                    <p class="text-gray-400 text-sm mb-4">
                        {{ Str::limit($related->excerpt, 100) }}
                    </p>
                    
                    <a href="{{ route('blog.article', $related->slug) }}" class="text-apple-blue hover:text-apple-blue-dark font-semibold text-sm inline-flex items-center group">
                        Baca Artikel 
                        <i class="fas fa-arrow-right ml-2 group-hover:translate-x-1 transition-transform"></i>
                    </a>
                </div>
            </article>
            @endforeach
        </div>
    </div>
</section>
@endif

<!-- CTA Section -->
<section class="py-20 px-4 bg-gradient-to-br from-apple-blue/10 via-purple-500/10 to-apple-green/10">
    <div class="container mx-auto max-w-4xl">
        <div class="glass rounded-3xl p-12 text-center" data-aos="zoom-in">
            <h2 class="text-3xl md:text-4xl font-bold mb-4">
                Butuh Konsultasi Perizinan?
            </h2>
            <p class="text-xl text-gray-300 mb-8">
                Tim ahli kami siap membantu Anda mengurus perizinan industri dengan cepat dan transparan
            </p>
            
            <div class="flex flex-wrap justify-center gap-4">
                <a href="https://wa.me/6281382605030?text=Halo%20Bizmark.ID%2C%20saya%20ingin%20konsultasi" 
                   target="_blank"
                   class="btn-primary inline-flex items-center">
                    <i class="fab fa-whatsapp mr-2"></i>
                    Chat via WhatsApp
                </a>
                <a href="{{ route('blog.index') }}" class="btn-secondary inline-flex items-center">
                    <i class="fas fa-newspaper mr-2"></i>
                    Baca Artikel Lainnya
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Custom Prose Styles for Article Content -->
<style>
.prose {
    color: #e5e7eb;
}
.prose h2 {
    color: #fff;
    font-weight: 700;
    font-size: 2rem;
    margin-top: 2rem;
    margin-bottom: 1rem;
}
.prose h3 {
    color: #fff;
    font-weight: 600;
    font-size: 1.5rem;
    margin-top: 1.5rem;
    margin-bottom: 0.75rem;
}
.prose p {
    margin-bottom: 1.25rem;
    line-height: 1.8;
}
.prose ul, .prose ol {
    margin-bottom: 1.25rem;
    padding-left: 1.5rem;
}
.prose li {
    margin-bottom: 0.5rem;
}
.prose a {
    color: #007AFF;
    text-decoration: underline;
}
.prose a:hover {
    color: #0051D5;
}
.prose strong {
    color: #fff;
    font-weight: 600;
}
.prose blockquote {
    border-left: 4px solid #007AFF;
    padding-left: 1rem;
    font-style: italic;
    color: #9ca3af;
    margin: 1.5rem 0;
}
.prose img {
    border-radius: 1rem;
    margin: 1.5rem 0;
}
.prose code {
    background: rgba(255, 255, 255, 0.05);
    padding: 0.2rem 0.4rem;
    border-radius: 0.25rem;
    font-size: 0.9em;
}
.prose pre {
    background: rgba(255, 255, 255, 0.05);
    padding: 1rem;
    border-radius: 0.5rem;
    overflow-x: auto;
    margin: 1.5rem 0;
}
</style>

@endsection
