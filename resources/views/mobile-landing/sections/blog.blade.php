@php
    use App\Models\Article;
    
    // Get latest 3 published articles
    $articles = Article::where('status', 'published')
        ->orderBy('published_at', 'desc')
        ->take(3)
        ->get();
    
    $metrics = config('landing_metrics');
@endphp

@if($articles->count() > 0)
<!-- BLOG/ARTICLES: Knowledge Hub -->
<section id="blog" class="magazine-section bg-white fade-in-up">
    <!-- Section Header -->
    <div class="mb-8">
        <h2 class="headline text-4xl text-gray-900 mb-3">
            Insight & <span class="text-gradient">Panduan</span>
        </h2>
        <p class="text-gray-600 text-sm max-w-xl leading-relaxed">
            Panduan perizinan, tips compliance, dan update regulasi terbaru 
            untuk membantu bisnis Anda tetap compliant.
        </p>
        
        <!-- Category Quick Links -->
        <div class="mt-6 flex flex-wrap items-center gap-2">
            <a href="{{ route('blog.category', 'perizinan') }}" 
               class="text-xs font-semibold bg-blue-50 text-blue-700 px-4 py-2 rounded-full hover:bg-blue-100 transition-colors">
                #Perizinan
            </a>
            <a href="{{ route('blog.category', 'compliance') }}" 
               class="text-xs font-semibold bg-green-50 text-green-700 px-4 py-2 rounded-full hover:bg-green-100 transition-colors">
                #Compliance
            </a>
            <a href="{{ route('blog.category', 'regulasi') }}" 
               class="text-xs font-semibold bg-blue-50 text-blue-700 px-4 py-2 rounded-full hover:bg-blue-100 transition-colors">
                #Regulasi
            </a>
            <a href="{{ route('blog.index') }}" 
               class="text-xs font-semibold bg-gray-100 text-gray-700 px-4 py-2 rounded-full hover:bg-gray-200 transition-colors">
                Lihat Semua →
            </a>
        </div>
    </div>
    
    <!-- Articles Grid -->
    <div class="space-y-4">
        @foreach($articles as $index => $article)
        @if($index === 0)
        <!-- Featured Article (First/Latest) -->
        <article class="magazine-card">
            @if($article->featured_image)
            <div class="relative h-48 overflow-hidden">
                <img src="{{ Storage::url($article->featured_image) }}" 
                     alt="{{ $article->title }}"
                     class="w-full h-full object-cover">
                <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent"></div>
                <div class="absolute bottom-4 left-4">
                    @if($article->category)
                    <span class="category-tag bg-white/90 backdrop-blur-sm text-gray-900 px-3 py-1.5 rounded-full shadow-lg text-xs font-bold">
                        {{ strtoupper($article->category) }}
                    </span>
                    @endif
                </div>
            </div>
            @else
            <div class="relative h-48 overflow-hidden bg-gradient-to-br from-[#0077B5] to-[#005582]">
                <div class="absolute inset-0 flex items-center justify-center">
                    <i class="fas fa-newspaper text-white text-6xl opacity-20"></i>
                </div>
            </div>
            @endif
            
            <div class="p-6">
                <div class="flex items-center gap-3 mb-3 text-xs text-gray-500">
                    <span class="flex items-center gap-1">
                        <i class="far fa-calendar"></i>
                        {{ $article->published_at?->format('d M Y') ?? $article->created_at->format('d M Y') }}
                    </span>
                    <span class="flex items-center gap-1">
                        <i class="far fa-clock"></i>
                        {{ $article->read_time ?? '5' }} min
                    </span>
                    @if($article->views_count)
                    <span class="flex items-center gap-1">
                        <i class="far fa-eye"></i>
                        {{ $article->views_count }}
                    </span>
                    @endif
                </div>
                
                <h3 class="headline text-xl text-gray-900 mb-2">
                    {{ $article->title }}
                </h3>
                
                <p class="text-sm text-gray-600 mb-4 leading-relaxed line-clamp-3">
                    {{ $article->excerpt ?? strip_tags(substr($article->content, 0, 150)) }}...
                </p>
                
                <a href="{{ route('blog.article', $article->slug) }}" 
                   class="inline-flex items-center gap-2 text-sm font-semibold text-blue-600 hover:text-blue-700 transition-colors">
                    Baca Artikel Lengkap
                    <i class="fas fa-arrow-right text-xs"></i>
                </a>
            </div>
        </article>
        
        @else
        <!-- Regular Article Cards -->
        <article class="flex gap-4 bg-white rounded-2xl p-4 border border-gray-100 shadow-sm hover:shadow-md transition-shadow">
            @if($article->featured_image)
            <div class="flex-shrink-0 w-24 h-24 rounded-xl overflow-hidden bg-gray-100">
                <img src="{{ Storage::url($article->featured_image) }}" 
                     alt="{{ $article->title }}"
                     class="w-full h-full object-cover">
            </div>
            @else
            <div class="flex-shrink-0 w-24 h-24 rounded-xl overflow-hidden bg-gradient-to-br from-gray-200 to-gray-300 flex items-center justify-center">
                <i class="fas fa-file-alt text-gray-400 text-2xl"></i>
            </div>
            @endif
            
            <div class="flex-1 min-w-0">
                @if($article->category)
                <div class="category-tag text-blue-600 text-xs mb-1">
                    {{ strtoupper($article->category) }}
                </div>
                @endif
                
                <h4 class="text-base font-bold text-gray-900 mb-1 line-clamp-2">
                    {{ $article->title }}
                </h4>
                
                <div class="flex items-center gap-3 text-xs text-gray-500 mb-2">
                    <span>{{ $article->published_at?->format('d M Y') ?? $article->created_at->format('d M Y') }}</span>
                    <span>•</span>
                    <span>{{ $article->read_time ?? '5' }} min</span>
                </div>
                
                <a href="{{ route('blog.article', $article->slug) }}" 
                   class="text-xs font-semibold text-blue-600 hover:underline">
                    Baca Selengkapnya →
                </a>
            </div>
        </article>
        @endif
        @endforeach
    </div>
    
    <!-- CTA: View All Articles -->
    <div class="mt-8 text-center">
        <a href="{{ route('blog.index') }}" 
           class="inline-block bg-gradient-to-r from-[#0077B5] to-[#005582] text-white font-semibold px-8 py-4 rounded-xl hover:shadow-lg transition-all">
            <i class="fas fa-th-large mr-2"></i>Lihat Semua Artikel
        </a>
        <p class="text-xs text-gray-500 mt-3">
            Update mingguan tentang perizinan & regulasi bisnis
        </p>
    </div>
</section>
@endif
