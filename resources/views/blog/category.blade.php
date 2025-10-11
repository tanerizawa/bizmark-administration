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
            <a href="{{ route('blog.index') }}" class="hover:text-apple-blue transition">
                Blog
            </a>
            <span>/</span>
            <span class="text-white">{{ $categoryLabel }}</span>
        </nav>
    </div>
</section>

<!-- Category Header -->
<section class="py-12 px-4 bg-gradient-to-br from-apple-blue/10 via-purple-500/10 to-apple-green/10">
    <div class="container mx-auto text-center">
        <div class="mb-6" data-aos="fade-up">
            <a href="{{ route('blog.index') }}" class="text-gray-400 hover:text-apple-blue transition">
                <i class="fas fa-arrow-left mr-2"></i>Kembali ke Blog
            </a>
        </div>
        
        <div class="inline-block px-6 py-3 bg-apple-blue/20 backdrop-blur-sm rounded-full mb-6" data-aos="fade-up" data-aos-delay="100">
            <i class="fas fa-folder mr-2 text-apple-blue"></i>
            <span class="text-sm font-semibold">Kategori</span>
        </div>
        
        <h1 class="text-5xl md:text-6xl font-bold mb-6" data-aos="fade-up" data-aos-delay="200">
            {{ $categoryLabel }}
        </h1>
        
        <p class="text-xl text-gray-300 max-w-3xl mx-auto" data-aos="fade-up" data-aos-delay="300">
            @if($category === 'perizinan')
                Panduan lengkap dan informasi terkini seputar perizinan industri di Indonesia
            @elseif($category === 'lingkungan')
                Artikel tentang pengelolaan lingkungan, AMDAL, UKL-UPL, dan keberlanjutan
            @elseif($category === 'regulasi')
                Update regulasi dan kebijakan terbaru yang berdampak pada industri
            @elseif($category === 'tips')
                Tips praktis dan panduan untuk mempermudah proses perizinan Anda
            @else
                Temukan artikel menarik dalam kategori ini
            @endif
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
                        <span class="px-3 py-1 bg-apple-blue/90 backdrop-blur-sm text-white rounded-full text-xs font-semibold">
                            {{ $article->category_label }}
                        </span>
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
            <i class="fas fa-folder-open text-6xl text-gray-700 mb-4"></i>
            <h3 class="text-2xl font-bold mb-2">Belum Ada Artikel</h3>
            <p class="text-gray-400 mb-8">Artikel dalam kategori ini akan segera hadir.</p>
            <a href="{{ route('blog.index') }}" class="btn-primary inline-flex items-center">
                <i class="fas fa-arrow-left mr-2"></i>
                Lihat Semua Artikel
            </a>
        </div>
        @endif
    </div>
</section>

<!-- Other Categories -->
<section class="py-12 px-4 bg-dark-bg-secondary">
    <div class="container mx-auto">
        <h3 class="text-2xl font-bold mb-8 text-center" data-aos="fade-up">
            Jelajahi Kategori Lainnya
        </h3>
        
        <div class="flex flex-wrap justify-center gap-4" data-aos="fade-up" data-aos-delay="100">
            @if($category !== 'perizinan')
            <a href="{{ route('blog.category', 'perizinan') }}" class="px-6 py-3 glass rounded-full hover:bg-apple-blue/20 transition font-medium">
                <i class="fas fa-file-contract mr-2"></i>Perizinan
            </a>
            @endif
            @if($category !== 'lingkungan')
            <a href="{{ route('blog.category', 'lingkungan') }}" class="px-6 py-3 glass rounded-full hover:bg-apple-green/20 transition font-medium">
                <i class="fas fa-leaf mr-2"></i>Lingkungan
            </a>
            @endif
            @if($category !== 'regulasi')
            <a href="{{ route('blog.category', 'regulasi') }}" class="px-6 py-3 glass rounded-full hover:bg-purple-500/20 transition font-medium">
                <i class="fas fa-gavel mr-2"></i>Regulasi
            </a>
            @endif
            @if($category !== 'tips')
            <a href="{{ route('blog.category', 'tips') }}" class="px-6 py-3 glass rounded-full hover:bg-apple-orange/20 transition font-medium">
                <i class="fas fa-lightbulb mr-2"></i>Tips & Panduan
            </a>
            @endif
        </div>
    </div>
</section>

@endsection
