@extends('landing.layout')

@section('content')

<!-- Breadcrumbs -->
<section class="pt-24 pb-4 px-4 bg-dark-bg">
    <div class="container mx-auto">
        <nav class="flex items-center space-x-2 text-sm text-gray-400" data-aos="fade-right">
            <a href="/" class="hover:text-apple-blue transition">
                <i class="fas fa-home"></i> Beranda
            </a>
            <span>/</span>
            <span class="text-white">Blog & Artikel</span>
        </nav>
    </div>
</section>

<!-- Blog Header -->
<section class="py-12 px-4 bg-gradient-to-br from-apple-blue/10 via-purple-500/10 to-apple-green/10">
    <div class="container mx-auto text-center">
        <h1 class="text-5xl md:text-6xl font-bold mb-6" data-aos="fade-up">
            Blog & Artikel
        </h1>
        <p class="text-xl text-gray-300 max-w-3xl mx-auto" data-aos="fade-up" data-aos-delay="100">
            Temukan informasi terkini seputar perizinan industri, regulasi lingkungan, dan insight bisnis
        </p>
    </div>
</section>

<!-- Articles Grid -->
<section class="py-20 px-4 bg-dark-bg">
    <div class="container mx-auto">
        @if($articles->count() > 0)
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
                        @foreach(array_slice($article->tags, 0, 3) as $tag)
                        <a href="{{ route('blog.tag', $tag) }}" class="text-xs px-2 py-1 bg-white/5 hover:bg-white/10 rounded-full text-gray-400 hover:text-apple-blue transition">
                            #{{ $tag }}
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
            <i class="fas fa-newspaper text-6xl text-gray-700 mb-4"></i>
            <h3 class="text-2xl font-bold mb-2">Belum Ada Artikel</h3>
            <p class="text-gray-400">Artikel akan segera hadir. Stay tuned!</p>
        </div>
        @endif
    </div>
</section>

<!-- Categories & Tags Sidebar (Optional Enhancement) -->
<section class="py-12 px-4 bg-dark-bg-secondary">
    <div class="container mx-auto">
        <div class="grid md:grid-cols-2 gap-8">
            <!-- Popular Categories -->
            <div class="glass rounded-2xl p-8" data-aos="fade-right">
                <h3 class="text-2xl font-bold mb-6 flex items-center">
                    <i class="fas fa-folder mr-3 text-apple-blue"></i>
                    Kategori
                </h3>
                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('blog.category', 'perizinan') }}" class="px-4 py-2 bg-white/5 hover:bg-apple-blue/20 rounded-full text-sm font-medium transition">
                        Perizinan
                    </a>
                    <a href="{{ route('blog.category', 'lingkungan') }}" class="px-4 py-2 bg-white/5 hover:bg-apple-green/20 rounded-full text-sm font-medium transition">
                        Lingkungan
                    </a>
                    <a href="{{ route('blog.category', 'regulasi') }}" class="px-4 py-2 bg-white/5 hover:bg-purple-500/20 rounded-full text-sm font-medium transition">
                        Regulasi
                    </a>
                    <a href="{{ route('blog.category', 'tips') }}" class="px-4 py-2 bg-white/5 hover:bg-apple-orange/20 rounded-full text-sm font-medium transition">
                        Tips & Panduan
                    </a>
                </div>
            </div>
            
            <!-- CTA Box -->
            <div class="glass rounded-2xl p-8 bg-gradient-to-br from-apple-blue/10 to-apple-green/10" data-aos="fade-left">
                <h3 class="text-2xl font-bold mb-4">
                    Butuh Bantuan Perizinan?
                </h3>
                <p class="text-gray-300 mb-6">
                    Konsultasikan kebutuhan perizinan industri Anda dengan tim ahli kami sekarang juga.
                </p>
                <a href="https://wa.me/6281382605030?text=Halo%20Bizmark.ID%2C%20saya%20ingin%20konsultasi" 
                   target="_blank"
                   class="btn-primary inline-flex items-center">
                    <i class="fab fa-whatsapp mr-2"></i>
                    Konsultasi Gratis
                </a>
            </div>
        </div>
    </div>
</section>

@endsection
