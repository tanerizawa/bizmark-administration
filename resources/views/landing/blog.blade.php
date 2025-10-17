<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Artikel & Berita - Bizmark.ID</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --apple-blue: #007AFF;
            --apple-blue-dark: #0051D5;
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
        
        .article-card {
            background: var(--dark-bg-tertiary);
            border-radius: 1rem;
            overflow: hidden;
            border: 1px solid var(--dark-separator);
            transition: all 0.3s ease;
            height: 100%;
            display: flex;
            flex-direction: column;
        }
        
        .article-card:hover {
            transform: translateY(-8px);
            border-color: var(--apple-blue);
            box-shadow: 0 15px 35px rgba(0, 122, 255, 0.2);
        }
        
        .article-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
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
                
                <div class="flex items-center space-x-8">
                    <a href="{{ route('landing') }}" class="hover:text-blue-400 transition">Beranda</a>
                    <a href="{{ route('blog.index') }}" class="text-blue-400">Artikel</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Header -->
    <section class="pt-32 pb-12 px-4" style="background: linear-gradient(135deg, #000000 0%, #1a1a2e 100%);">
        <div class="container mx-auto max-w-7xl">
            <h1 class="text-5xl font-bold mb-4">Artikel & Berita</h1>
            <p class="text-xl text-gray-400">Update terbaru seputar perizinan, regulasi, dan tips bisnis</p>
        </div>
    </section>

    <!-- Search & Filter -->
    <section class="py-8 px-4" style="background: var(--dark-bg-secondary);">
        <div class="container mx-auto max-w-7xl">
            <form action="{{ route('blog.index') }}" method="GET" class="flex flex-col md:flex-row gap-4">
                <!-- Search -->
                <div class="flex-1">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari artikel..." class="w-full px-4 py-3 bg-gray-800 text-white rounded-lg border border-gray-700 focus:border-blue-500 focus:outline-none">
                </div>
                
                <!-- Category Filter -->
                <div>
                    <select name="category" class="w-full md:w-48 px-4 py-3 bg-gray-800 text-white rounded-lg border border-gray-700 focus:border-blue-500 focus:outline-none">
                        <option value="">Semua Kategori</option>
                        @foreach($categories as $key => $label)
                        <option value="{{ $key }}" {{ request('category') == $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                
                <!-- Sort -->
                <div>
                    <select name="sort" class="w-full md:w-48 px-4 py-3 bg-gray-800 text-white rounded-lg border border-gray-700 focus:border-blue-500 focus:outline-none">
                        <option value="published_at" {{ request('sort') == 'published_at' ? 'selected' : '' }}>Terbaru</option>
                        <option value="popular" {{ request('sort') == 'popular' ? 'selected' : '' }}>Terpopuler</option>
                    </select>
                </div>
                
                <button type="submit" class="px-6 py-3 bg-blue-600 hover:bg-blue-700 rounded-lg font-semibold transition">
                    <i class="fas fa-search mr-2"></i>Cari
                </button>
            </form>
        </div>
    </section>

    <!-- Articles Grid -->
    <section class="py-12 px-4">
        <div class="container mx-auto max-w-7xl">
            @if($articles->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                @foreach($articles as $article)
                <div class="article-card">
                    @if($article->featured_image)
                    <img src="{{ Storage::url($article->featured_image) }}" alt="{{ $article->title }}" class="article-image">
                    @else
                    <div class="article-image bg-gradient-to-br from-blue-600 to-purple-600 flex items-center justify-center">
                        <i class="fas fa-newspaper text-white text-5xl"></i>
                    </div>
                    @endif
                    
                    <div class="p-6 flex-1 flex flex-col">
                        <div class="mb-3">
                            <a href="{{ route('blog.category', $article->category) }}" class="inline-block px-3 py-1 bg-blue-500/20 text-blue-400 rounded-full text-xs font-semibold hover:bg-blue-500/30 transition">
                                {{ $article->category_label }}
                            </a>
                            @if($article->is_featured)
                            <span class="inline-block px-3 py-1 bg-orange-500/20 text-orange-400 rounded-full text-xs font-semibold ml-2">
                                <i class="fas fa-star mr-1"></i>Featured
                            </span>
                            @endif
                        </div>
                        
                        <h3 class="text-xl font-bold mb-3 line-clamp-2">
                            <a href="{{ route('blog.article', $article->slug) }}" class="hover:text-blue-400 transition">
                                {{ $article->title }}
                            </a>
                        </h3>
                        
                        <p class="text-gray-400 mb-4 line-clamp-3 text-sm flex-1">
                            {{ $article->excerpt }}
                        </p>
                        
                        <div class="flex items-center justify-between text-sm text-gray-500 pt-4 border-t border-gray-700 mt-auto">
                            <div class="flex items-center gap-4">
                                <span>
                                    <i class="fas fa-calendar mr-1"></i>
                                    {{ $article->published_at->format('d M Y') }}
                                </span>
                                <span>
                                    <i class="fas fa-clock mr-1"></i>
                                    {{ $article->reading_time }} min
                                </span>
                            </div>
                            <span>
                                <i class="fas fa-eye mr-1"></i>
                                {{ number_format($article->views_count) }}
                            </span>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            
            <!-- Pagination -->
            @if($articles->hasPages())
            <div class="mt-12">
                {{ $articles->links() }}
            </div>
            @endif
            @else
            <div class="text-center py-20">
                <i class="fas fa-search text-6xl text-gray-600 mb-4"></i>
                <p class="text-xl text-gray-400">Tidak ada artikel yang ditemukan</p>
            </div>
            @endif
        </div>
    </section>

    <!-- Footer -->
    <footer class="py-8 px-4 border-t border-gray-800">
        <div class="container mx-auto max-w-7xl">
            <div class="text-center text-gray-500">
                <p>&copy; 2025 Bizmark.ID - PT Cangah Pajaratan Mandiri. All rights reserved.</p>
            </div>
        </div>
    </footer>
</body>
</html>
