<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- SEO Meta Tags -->
    <title>{{ $article->meta_title ?: $article->title }} - Bizmark.ID</title>
    <meta name="description" content="{{ $article->meta_description ?: $article->excerpt }}">
    <meta name="keywords" content="{{ $article->meta_keywords }}">
    <meta name="author" content="{{ $article->author->name }}">
    
    <!-- Open Graph -->
    <meta property="og:title" content="{{ $article->title }}">
    <meta property="og:description" content="{{ $article->excerpt }}">
    <meta property="og:image" content="{{ $article->featured_image ? Storage::url($article->featured_image) : asset('images/default-og.jpg') }}">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:type" content="article">
    
    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $article->title }}">
    <meta name="twitter:description" content="{{ $article->excerpt }}">
    <meta name="twitter:image" content="{{ $article->featured_image ? Storage::url($article->featured_image) : asset('images/default-og.jpg') }}">
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --apple-blue: #007AFF;
            --dark-bg: #000000;
            --dark-bg-secondary: #1C1C1E;
            --dark-bg-tertiary: #2C2C2E;
            --dark-separator: rgba(84, 84, 88, 0.35);
        }
        
        body {
            background: var(--dark-bg);
            color: white;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }
        
        .navbar {
            position: fixed;
            top: 0;
            width: 100%;
            background: rgba(28, 28, 30, 0.9);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid var(--dark-separator);
            z-index: 1000;
        }
        
        .prose {
            color: white;
            max-width: none;
        }
        
        .prose h2 {
            font-size: 1.875rem;
            font-weight: 700;
            margin-top: 2rem;
            margin-bottom: 1rem;
            color: white;
        }
        
        .prose h3 {
            font-size: 1.5rem;
            font-weight: 700;
            margin-top: 1.5rem;
            margin-bottom: 0.75rem;
            color: white;
        }
        
        .prose p {
            margin-bottom: 1.25rem;
            line-height: 1.75;
            color: rgba(235, 235, 245, 0.8);
        }
        
        .prose ul, .prose ol {
            margin-left: 1.5rem;
            margin-bottom: 1.25rem;
            color: rgba(235, 235, 245, 0.8);
        }
        
        .prose li {
            margin-bottom: 0.5rem;
        }
        
        .prose a {
            color: var(--apple-blue);
            text-decoration: underline;
        }
        
        .prose a:hover {
            color: #0051D5;
        }
        
        .prose img {
            border-radius: 0.75rem;
            margin: 2rem 0;
            max-width: 100%;
        }
        
        .prose strong {
            font-weight: 700;
            color: white;
        }
        
        .prose blockquote {
            border-left: 4px solid var(--apple-blue);
            padding-left: 1rem;
            margin: 1.5rem 0;
            font-style: italic;
            color: rgba(235, 235, 245, 0.6);
        }
        
        .prose code {
            background: var(--dark-bg-tertiary);
            padding: 0.2rem 0.4rem;
            border-radius: 0.25rem;
            font-family: 'Courier New', monospace;
            font-size: 0.875rem;
        }
        
        .prose pre {
            background: var(--dark-bg-tertiary);
            padding: 1rem;
            border-radius: 0.5rem;
            overflow-x: auto;
            margin: 1.5rem 0;
        }
        
        .prose pre code {
            background: transparent;
            padding: 0;
        }
        
        .article-card {
            background: var(--dark-bg-tertiary);
            border-radius: 1rem;
            overflow: hidden;
            border: 1px solid var(--dark-separator);
            transition: all 0.3s ease;
        }
        
        .article-card:hover {
            transform: translateY(-4px);
            border-color: var(--apple-blue);
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar">
        <div class="container mx-auto px-4 py-4">
            <div class="flex justify-between items-center">
                <a href="{{ route('landing') }}" class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-700 rounded-lg flex items-center justify-center">
                        <i class="fas fa-shield-alt text-white text-xl"></i>
                    </div>
                    <span class="text-xl font-bold">Bizmark.ID</span>
                </a>
                
                <div class="flex items-center space-x-6">
                    <a href="{{ route('landing') }}" class="hover:text-blue-400 transition">Beranda</a>
                    <a href="{{ route('blog.index') }}" class="hover:text-blue-400 transition">Artikel</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Article Header -->
    <section class="pt-32 pb-12 px-4" style="background: linear-gradient(135deg, #000000 0%, #1a1a2e 100%);">
        <div class="container mx-auto max-w-4xl">
            <!-- Breadcrumb -->
            <div class="mb-6 text-sm text-gray-400">
                <a href="{{ route('landing') }}" class="hover:text-blue-400">Beranda</a>
                <span class="mx-2">/</span>
                <a href="{{ route('blog.index') }}" class="hover:text-blue-400">Artikel</a>
                <span class="mx-2">/</span>
                <a href="{{ route('blog.category', $article->category) }}" class="hover:text-blue-400">{{ $article->category_label }}</a>
            </div>
            
            <!-- Category Badge -->
            <div class="mb-4">
                <a href="{{ route('blog.category', $article->category) }}" class="inline-block px-4 py-2 bg-blue-500/20 text-blue-400 rounded-full text-sm font-semibold hover:bg-blue-500/30 transition">
                    {{ $article->category_label }}
                </a>
                @if($article->is_featured)
                <span class="inline-block px-4 py-2 bg-orange-500/20 text-orange-400 rounded-full text-sm font-semibold ml-2">
                    <i class="fas fa-star mr-1"></i>Featured
                </span>
                @endif
            </div>
            
            <!-- Title -->
            <h1 class="text-4xl md:text-5xl font-bold mb-6 leading-tight">{{ $article->title }}</h1>
            
            <!-- Meta Info -->
            <div class="flex flex-wrap items-center gap-6 text-gray-400 mb-6">
                <div class="flex items-center gap-2">
                    <div class="w-10 h-10 bg-blue-600 rounded-full flex items-center justify-center font-semibold">
                        {{ strtoupper(substr($article->author->name, 0, 2)) }}
                    </div>
                    <span>{{ $article->author->name }}</span>
                </div>
                <div class="flex items-center gap-2">
                    <i class="fas fa-calendar"></i>
                    <span>{{ $article->published_at->format('d F Y') }}</span>
                </div>
                <div class="flex items-center gap-2">
                    <i class="fas fa-clock"></i>
                    <span>{{ $article->reading_time }} menit baca</span>
                </div>
                <div class="flex items-center gap-2">
                    <i class="fas fa-eye"></i>
                    <span>{{ number_format($article->views_count) }} views</span>
                </div>
            </div>
            
            <!-- Excerpt -->
            @if($article->excerpt)
            <p class="text-xl text-gray-300 border-l-4 border-blue-500 pl-6 italic">
                {{ $article->excerpt }}
            </p>
            @endif
        </div>
    </section>

    <!-- Featured Image -->
    @if($article->featured_image)
    <section class="py-8 px-4" style="background: var(--dark-bg-secondary);">
        <div class="container mx-auto max-w-4xl">
            <img src="{{ Storage::url($article->featured_image) }}" alt="{{ $article->title }}" class="w-full rounded-2xl shadow-2xl">
        </div>
    </section>
    @endif

    <!-- Article Content -->
    <section class="py-12 px-4">
        <div class="container mx-auto max-w-4xl">
            <div class="prose prose-lg">
                {!! $article->content !!}
            </div>
            
            <!-- Tags -->
            @if($article->tags && count($article->tags) > 0)
            <div class="mt-12 pt-8 border-t border-gray-800">
                <h4 class="text-lg font-semibold mb-4">Tags:</h4>
                <div class="flex flex-wrap gap-2">
                    @foreach($article->tags as $tag)
                    <a href="{{ route('blog.tag', $tag) }}" class="px-4 py-2 bg-blue-500/20 text-blue-400 rounded-full text-sm hover:bg-blue-500/30 transition">
                        #{{ $tag }}
                    </a>
                    @endforeach
                </div>
            </div>
            @endif
            
            <!-- Share Buttons -->
            <div class="mt-8 pt-8 border-t border-gray-800">
                <h4 class="text-lg font-semibold mb-4">Bagikan Artikel:</h4>
                <div class="flex gap-4">
                    <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}" target="_blank" class="px-6 py-3 bg-blue-600 hover:bg-blue-700 rounded-lg transition">
                        <i class="fab fa-facebook-f mr-2"></i>Facebook
                    </a>
                    <a href="https://twitter.com/intent/tweet?url={{ urlencode(url()->current()) }}&text={{ urlencode($article->title) }}" target="_blank" class="px-6 py-3 bg-sky-500 hover:bg-sky-600 rounded-lg transition">
                        <i class="fab fa-twitter mr-2"></i>Twitter
                    </a>
                    <a href="https://wa.me/?text={{ urlencode($article->title . ' ' . url()->current()) }}" target="_blank" class="px-6 py-3 bg-green-600 hover:bg-green-700 rounded-lg transition">
                        <i class="fab fa-whatsapp mr-2"></i>WhatsApp
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Related Articles -->
    @if($relatedArticles->count() > 0)
    <section class="py-12 px-4" style="background: var(--dark-bg-secondary);">
        <div class="container mx-auto max-w-7xl">
            <h3 class="text-3xl font-bold mb-8">Artikel Terkait</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                @foreach($relatedArticles as $related)
                <div class="article-card">
                    @if($related->featured_image)
                    <img src="{{ Storage::url($related->featured_image) }}" alt="{{ $related->title }}" class="w-full h-48 object-cover">
                    @else
                    <div class="w-full h-48 bg-gradient-to-br from-blue-600 to-purple-600 flex items-center justify-center">
                        <i class="fas fa-newspaper text-white text-4xl"></i>
                    </div>
                    @endif
                    
                    <div class="p-6">
                        <div class="mb-3">
                            <span class="inline-block px-3 py-1 bg-blue-500/20 text-blue-400 rounded-full text-xs font-semibold">
                                {{ $related->category_label }}
                            </span>
                        </div>
                        
                        <h4 class="text-lg font-bold mb-2 line-clamp-2">
                            <a href="{{ route('blog.article', $related->slug) }}" class="hover:text-blue-400 transition">
                                {{ $related->title }}
                            </a>
                        </h4>
                        
                        <p class="text-gray-400 text-sm mb-4 line-clamp-2">
                            {{ $related->excerpt }}
                        </p>
                        
                        <div class="text-sm text-gray-500">
                            <i class="fas fa-calendar mr-1"></i>
                            {{ $related->published_at->format('d M Y') }}
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    <!-- CTA Section -->
    <section class="py-12 px-4">
        <div class="container mx-auto max-w-4xl">
            <div class="bg-gradient-to-r from-blue-600 to-purple-600 rounded-2xl p-8 md:p-12 text-center">
                <h3 class="text-3xl font-bold mb-4">Butuh Konsultasi Perizinan?</h3>
                <p class="text-xl mb-6">Hubungi kami untuk konsultasi gratis mengenai kebutuhan perizinan bisnis Anda</p>
                <a href="https://wa.me/6281382605030" target="_blank" class="inline-block px-8 py-4 bg-white text-blue-600 font-bold rounded-lg hover:bg-gray-100 transition">
                    <i class="fab fa-whatsapp mr-2"></i>Chat via WhatsApp
                </a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="py-8 px-4 border-t border-gray-800">
        <div class="container mx-auto max-w-7xl">
            <div class="text-center text-gray-500">
                <p>&copy; 2025 Bizmark.ID - PT Timur Cakrawala Konsultan. All rights reserved.</p>
            </div>
        </div>
    </footer>
</body>
</html>
