@extends('landing.layout')

@section('content')

<!-- Breadcrumbs -->
<section class="pt-24 pb-4 px-4 bg-white">
    <div class="container mx-auto max-w-4xl">
        <nav class="flex items-center space-x-2 text-sm text-gray-600" data-aos="fade-right">
            <a href="/" class="hover:text-primary transition">
                <i class="fas fa-home"></i> Beranda
            </a>
            <span>/</span>
            <a href="{{ route('blog.index') }}" class="hover:text-primary transition">
                Blog
            </a>
            <span>/</span>
            <a href="{{ route('blog.category', $article->category) }}" class="hover:text-primary transition">
                {{ $article->category_label }}
            </a>
            <span>/</span>
            <span class="text-gray-900 truncate max-w-xs font-medium">{{ Str::limit($article->title, 40) }}</span>
        </nav>
    </div>
</section>

<!-- Article Header -->
<section class="py-8 px-4 bg-gradient-to-br from-primary/5 via-purple-500/5 to-secondary/5">
    <div class="container mx-auto max-w-4xl">
        <!-- Back Button -->
        <div class="mb-6" data-aos="fade-up">
            <a href="{{ route('blog.index') }}" class="text-gray-700 hover:text-primary transition font-medium">
                <i class="fas fa-arrow-left mr-2"></i>Kembali ke Blog
            </a>
        </div>
        
        <!-- Category & Date -->
        <div class="flex items-center gap-4 mb-6 flex-wrap" data-aos="fade-up" data-aos-delay="100">
            <a href="{{ route('blog.category', $article->category) }}" class="px-4 py-2 bg-primary text-white rounded-full text-sm font-semibold hover:bg-primary-dark transition">
                {{ $article->category_label }}
            </a>
            <span class="text-gray-700"><i class="far fa-calendar mr-2"></i>{{ $article->published_at->format('d F Y') }}</span>
            <span class="text-gray-700"><i class="far fa-clock mr-2"></i>{{ $article->reading_time }} menit</span>
            <span class="text-gray-700"><i class="far fa-eye mr-2"></i>{{ number_format($article->views_count) }} views</span>
        </div>
        
        <!-- Title -->
        <h1 class="text-4xl md:text-5xl font-bold mb-6 leading-tight text-gray-900" data-aos="fade-up" data-aos-delay="200">
            {{ $article->title }}
        </h1>
        
        <!-- Excerpt -->
        <p class="text-xl text-gray-700 leading-relaxed" data-aos="fade-up" data-aos-delay="300">
            {{ $article->excerpt }}
        </p>
    </div>
</section>

<!-- Featured Image -->
@if($article->featured_image)
<section class="px-4 py-8 bg-white">
    <div class="container mx-auto max-w-4xl">
        <div class="rounded-2xl overflow-hidden shadow-soft-lg" data-aos="zoom-in">
            <img src="{{ Storage::url($article->featured_image) }}" alt="{{ $article->title }}" class="w-full h-auto">
        </div>
    </div>
</section>
@endif

<!-- Article Content -->
<section class="py-12 px-4 bg-white">
    <div class="container mx-auto max-w-4xl">
        <article class="prose prose-lg max-w-none" data-aos="fade-up">
            {!! $article->content !!}
        </article>
        
        <!-- Tags -->
        @if($article->tags && count($article->tags) > 0)
        <div class="mt-12 pt-8 border-t border-gray-200" data-aos="fade-up">
            <h4 class="text-lg font-bold mb-4 text-gray-900">Tags:</h4>
            <div class="flex flex-wrap gap-3">
                @foreach($article->tags as $tag)
                <a href="{{ route('blog.tag', $tag) }}" class="px-4 py-2 bg-gray-100 hover:bg-primary/10 text-gray-700 hover:text-primary rounded-full text-sm font-medium transition">
                    #{{ $tag }}
                </a>
                @endforeach
            </div>
        </div>
        @endif
        
        <!-- Share Buttons -->
        <div class="mt-8 pt-8 border-t border-gray-200" data-aos="fade-up">
            <h4 class="text-lg font-bold mb-4 text-gray-900">Bagikan Artikel:</h4>
            <div class="flex gap-3">
                <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(route('blog.article', $article->slug)) }}" 
                   target="_blank"
                   class="w-12 h-12 bg-blue-600 hover:bg-blue-700 text-white rounded-full flex items-center justify-center transition shadow-soft">
                    <i class="fab fa-facebook-f"></i>
                </a>
                <a href="https://twitter.com/intent/tweet?url={{ urlencode(route('blog.article', $article->slug)) }}&text={{ urlencode($article->title) }}" 
                   target="_blank"
                   class="w-12 h-12 bg-sky-500 hover:bg-sky-600 text-white rounded-full flex items-center justify-center transition shadow-soft">
                    <i class="fab fa-twitter"></i>
                </a>
                <a href="https://www.linkedin.com/shareArticle?mini=true&url={{ urlencode(route('blog.article', $article->slug)) }}&title={{ urlencode($article->title) }}" 
                   target="_blank"
                   class="w-12 h-12 bg-blue-700 hover:bg-blue-800 text-white rounded-full flex items-center justify-center transition shadow-soft">
                    <i class="fab fa-linkedin-in"></i>
                </a>
                <a href="https://wa.me/?text={{ urlencode($article->title . ' - ' . route('blog.article', $article->slug)) }}" 
                   target="_blank"
                   class="w-12 h-12 bg-green-600 hover:bg-green-700 text-white rounded-full flex items-center justify-center transition shadow-soft">
                    <i class="fab fa-whatsapp"></i>
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Related Articles -->
@if($relatedArticles->count() > 0)
<section class="py-20 px-4 bg-gray-50">
    <div class="container mx-auto max-w-6xl">
        <h2 class="text-3xl md:text-4xl font-bold mb-12 text-center text-gray-900" data-aos="fade-up">
            Artikel Terkait
        </h2>
        
        <div class="grid md:grid-cols-3 gap-8">
            @foreach($relatedArticles as $related)
            <article class="bg-white rounded-2xl overflow-hidden shadow-soft hover:shadow-soft-lg transition-shadow" data-aos="fade-up" data-aos-delay="{{ $loop->index * 100 }}">
                <div class="relative h-48 overflow-hidden bg-gray-100">
                    @if($related->featured_image)
                    <img src="{{ Storage::url($related->featured_image) }}" alt="{{ $related->title }}" class="w-full h-full object-cover hover:scale-105 transition-transform duration-300">
                    @else
                    <div class="w-full h-full bg-gradient-to-br from-primary/10 to-secondary/10 flex items-center justify-center">
                        <i class="fas fa-newspaper text-6xl text-gray-300"></i>
                    </div>
                    @endif
                </div>
                
                <div class="p-6">
                    <div class="flex items-center gap-3 mb-3 text-xs text-gray-600">
                        <span><i class="far fa-calendar mr-1"></i>{{ $related->published_at->format('d M Y') }}</span>
                        <span><i class="far fa-clock mr-1"></i>{{ $related->reading_time }} min</span>
                    </div>
                    
                    <h3 class="text-xl font-bold mb-3 leading-tight text-gray-900 hover:text-primary transition">
                        <a href="{{ route('blog.article', $related->slug) }}">{{ $related->title }}</a>
                    </h3>
                    
                    <p class="text-gray-600 text-sm mb-4">
                        {{ Str::limit($related->excerpt, 100) }}
                    </p>
                    
                    <a href="{{ route('blog.article', $related->slug) }}" class="text-primary hover:text-primary-dark font-semibold text-sm inline-flex items-center group">
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
<section class="py-20 px-4 bg-gradient-to-br from-primary/10 via-purple-500/5 to-secondary/10">
    <div class="container mx-auto max-w-4xl">
        <div class="bg-white rounded-3xl p-12 text-center shadow-soft-xl" data-aos="zoom-in">
            <h2 class="text-3xl md:text-4xl font-bold mb-4 text-gray-900">
                Butuh Konsultasi Perizinan?
            </h2>
            <p class="text-xl text-gray-700 mb-8">
                Tim ahli kami siap membantu Anda mengurus perizinan industri dengan cepat dan transparan
            </p>
            
            <div class="flex flex-wrap justify-center gap-4">
                <a href="https://wa.me/6283879602855?text=Halo%20Bizmark.ID%2C%20saya%20ingin%20konsultasi" 
                   target="_blank"
                   class="px-8 py-4 bg-primary hover:bg-primary-dark text-white font-semibold rounded-xl transition shadow-soft inline-flex items-center">
                    <i class="fab fa-whatsapp mr-2 text-xl"></i>
                    Chat via WhatsApp
                </a>
                <a href="{{ route('blog.index') }}" class="px-8 py-4 bg-white hover:bg-gray-50 text-gray-900 font-semibold rounded-xl transition shadow-soft border-2 border-gray-200 inline-flex items-center">
                    <i class="fas fa-newspaper mr-2"></i>
                    Baca Artikel Lainnya
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Custom Prose Styles for Article Content - Light Theme -->
<style>
.prose {
    color: #374151; /* gray-700 */
}
.prose h2 {
    color: #111827; /* gray-900 */
    font-weight: 700;
    font-size: 2rem;
    margin-top: 2rem;
    margin-bottom: 1rem;
}
.prose h3 {
    color: #111827; /* gray-900 */
    font-weight: 600;
    font-size: 1.5rem;
    margin-top: 1.5rem;
    margin-bottom: 0.75rem;
}
.prose h4 {
    color: #1F2937; /* gray-800 */
    font-weight: 600;
    font-size: 1.25rem;
    margin-top: 1.25rem;
    margin-bottom: 0.5rem;
}
.prose p {
    color: #4B5563; /* gray-600 */
    margin-bottom: 1.25rem;
    line-height: 1.8;
}
.prose ul, .prose ol {
    color: #4B5563; /* gray-600 */
    margin-bottom: 1.25rem;
    padding-left: 1.5rem;
}
.prose li {
    margin-bottom: 0.5rem;
}
.prose a {
    color: #0077B5; /* primary */
    text-decoration: underline;
    font-weight: 500;
}
.prose a:hover {
    color: #005582; /* primary-dark */
}
.prose strong {
    color: #111827; /* gray-900 */
    font-weight: 600;
}
.prose em {
    color: #374151; /* gray-700 */
}
.prose blockquote {
    border-left: 4px solid #0077B5; /* primary */
    padding-left: 1rem;
    padding-top: 0.5rem;
    padding-bottom: 0.5rem;
    font-style: italic;
    color: #6B7280; /* gray-500 */
    background: #F9FAFB; /* gray-50 */
    margin: 1.5rem 0;
    border-radius: 0 0.5rem 0.5rem 0;
}
.prose blockquote p {
    color: #6B7280; /* gray-500 */
}
.prose img {
    border-radius: 1rem;
    margin: 1.5rem 0;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
}
.prose code {
    background: #F3F4F6; /* gray-100 */
    color: #DC2626; /* red-600 */
    padding: 0.2rem 0.4rem;
    border-radius: 0.25rem;
    font-size: 0.9em;
    font-weight: 500;
}
.prose pre {
    background: #1F2937; /* gray-800 */
    color: #F9FAFB; /* gray-50 */
    padding: 1rem;
    border-radius: 0.5rem;
    overflow-x: auto;
    margin: 1.5rem 0;
}
.prose pre code {
    background: transparent;
    color: inherit;
    padding: 0;
}
.prose table {
    width: 100%;
    margin: 1.5rem 0;
    border-collapse: collapse;
}
.prose th {
    background: #F3F4F6; /* gray-100 */
    color: #111827; /* gray-900 */
    font-weight: 600;
    padding: 0.75rem;
    text-align: left;
    border: 1px solid #E5E7EB; /* gray-200 */
}
.prose td {
    padding: 0.75rem;
    border: 1px solid #E5E7EB; /* gray-200 */
    color: #4B5563; /* gray-600 */
}
.prose tr:nth-child(even) {
    background: #F9FAFB; /* gray-50 */
}
</style>

@endsection
