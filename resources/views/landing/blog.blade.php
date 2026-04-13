<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Artikel & Berita - Bizmark.ID</title>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Breadcrumb Schema -->
    <script type="application/ld+json">
    {
        "@@context": "https://schema.org",
        "@type": "BreadcrumbList",
        "itemListElement": [
            {
                "@type": "ListItem",
                "position": 1,
                "name": "Beranda",
                "item": "{{ route('landing.id') }}"
            },
            {
                "@type": "ListItem",
                "position": 2,
                "name": "Artikel",
                "item": "{{ route('blog.index.id') }}"
            }
        ]
    }
    </script>
    
    <style>
        :root {
            /* Professional LinkedIn-inspired blue */
            --color-primary: #0A66C2;
            --color-primary-dark: #004182;
            --color-primary-light: #378FE9;
            
            /* Professional grays */
            --text-primary: #000000;
            --text-secondary: #666666;
            --text-tertiary: #999999;
            
            /* Surface colors */
            --surface: #FFFFFF;
            --surface-secondary: #F8F9FA;
            --surface-tertiary: #E5E5E5;
            
            /* Spacing scale */
            --space-1: 0.25rem;
            --space-2: 0.5rem;
            --space-3: 0.75rem;
            --space-4: 1rem;
            --space-5: 1.5rem;
            --space-6: 2rem;
            
            /* Border radius */
            --radius-sm: 0.375rem;
            --radius-md: 0.5rem;
            --radius-lg: 0.75rem;
            --radius-xl: 1rem;
            --radius-full: 9999px;
            
            /* Shadows */
            --shadow-sm: 0 1px 2px rgba(0, 0, 0, 0.05);
            --shadow-md: 0 4px 6px rgba(0, 0, 0, 0.07);
            --shadow-lg: 0 10px 15px rgba(0, 0, 0, 0.1);
            --shadow-xl: 0 20px 25px rgba(0, 0, 0, 0.1);
        }
        
        body {
            background: white;
            color: var(--text-primary);
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
        }
        
        /* Button Styles */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 0.875rem 1.75rem;
            font-weight: 600;
            font-size: 1rem;
            border-radius: var(--radius-lg);
            transition: all 0.2s ease;
            cursor: pointer;
            text-decoration: none;
        }
        
        .btn:hover {
            transform: translateY(-1px);
        }
        
        .btn:active {
            transform: translateY(0);
        }
        
        .btn-primary {
            background: var(--color-primary);
            color: white;
            box-shadow: var(--shadow-md);
        }
        
        .btn-primary:hover {
            background: var(--color-primary-dark);
            box-shadow: var(--shadow-lg);
        }
        
        /* Nav Link Styles */
        .nav-link {
            position: relative;
            padding: 0.5rem 0.75rem;
            color: var(--text-secondary);
            font-weight: 500;
            transition: color 0.2s ease;
            border-radius: var(--radius-md);
            text-decoration: none;
        }
        
        .nav-link:hover {
            color: var(--color-primary);
            background: rgba(10, 102, 194, 0.05);
        }
        
        .nav-link.active {
            color: var(--color-primary);
            font-weight: 600;
        }
        
        .nav-link.active::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0.75rem;
            right: 0.75rem;
            height: 2px;
            background: var(--color-primary);
            border-radius: var(--radius-full);
        }
        
        /* Article Card Styles */
        .article-card {
            background: white;
            border-radius: var(--radius-xl);
            overflow: hidden;
            border: 1px solid var(--surface-tertiary);
            transition: all 0.3s ease;
            height: 100%;
            display: flex;
            flex-direction: column;
        }
        
        .article-card:hover {
            transform: translateY(-8px);
            border-color: var(--color-primary-light);
            box-shadow: var(--shadow-xl);
        }
        
        .article-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }
    </style>
</head>
<body class="font-sans antialiased bg-white text-gray-900" style="min-height: 100vh; display: flex; flex-direction: column;">

<!-- Skip to main content link for accessibility -->
<a href="#main-content" style="position: fixed; top: -100px; left: 50%; transform: translateX(-50%); z-index: 9999; padding: 0.75rem 1.5rem; background: var(--color-primary); color: white; font-weight: 600; border-radius: var(--radius-lg); text-decoration: none; transition: top 0.2s ease;" onfocus="this.style.top='1rem'" onblur="this.style.top='-100px'">Lewati ke konten utama</a>

<!-- Navbar -->
<nav class="fixed top-0 left-0 right-0 z-50 bg-white/95 backdrop-blur-sm border-b border-gray-200 shadow-sm" role="navigation" aria-label="Navigasi utama">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
            <!-- Logo -->
            <div class="flex items-center">
                <a href="{{ route('landing.id') }}" class="text-xl font-bold" style="color: var(--text-primary);">
                    <i class="fas fa-certificate mr-2" style="color: var(--color-primary);"></i>
                    Bizmark.ID
                </a>
            </div>
            
            <!-- Desktop Navigation -->
            <div class="hidden md:flex items-center space-x-1">
                <a href="{{ route('landing.id') }}#main-content" class="nav-link">{{ __('landing.nav.home') }}</a>
                <a href="{{ route('landing.id') }}#services" class="nav-link">{{ __('landing.nav.services') }}</a>
                <a href="{{ route('landing.id') }}#process" class="nav-link">{{ __('landing.nav.process') }}</a>
                <a href="{{ route('landing.id') }}#about" class="nav-link">{{ __('landing.nav.about') }}</a>
                <a href="{{ route('blog.index.id') }}" class="nav-link active">{{ __('landing.nav.blog') }}</a>
                
                <!-- Locale Switcher -->
                <x-locale-switcher />
                
                <a href="{{ route('landing.id') }}#contact" class="btn btn-primary">
                    {{ __('landing.nav.get_started') }}
                </a>
            </div>
            
            <!-- Mobile Menu Button -->
            <div class="md:hidden flex items-center">
                <button class="text-gray-700 p-2 rounded-lg hover:bg-gray-100 transition" 
                        style="color: var(--text-secondary);"
                        onmouseover="this.style.color='var(--color-primary)'"
                        onmouseout="this.style.color='var(--text-secondary)'"
                        onclick="toggleMobileMenu()" 
                        id="mobile-menu-button"
                        aria-label="Open navigation menu" 
                        aria-expanded="false"
                        aria-controls="mobile-menu">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
            </div>
        </div>
    </div>
</nav>

@include('landing.partials.mobile-menu')

<!-- Main Content -->
<div style="flex: 1; display: flex; flex-direction: column;">
    <!-- Header -->
    <section id="main-content" class="pt-32 pb-12 px-4" style="background: linear-gradient(135deg, var(--color-primary) 0%, var(--color-primary-dark) 100%);">
        <div class="container mx-auto max-w-7xl text-white">
            <h1 class="text-5xl font-bold mb-4">Artikel & Berita</h1>
            <p class="text-xl" style="color: rgba(255, 255, 255, 0.9);">Update terbaru seputar perizinan, regulasi, dan tips bisnis</p>
        </div>
    </section>

    <!-- Search & Filter -->
    <section class="py-8 px-4" style="background: var(--surface-secondary);">
        <div class="container mx-auto max-w-7xl">
            <form action="{{ route('blog.index.id') }}" method="GET" class="flex flex-col md:flex-row gap-4">
                <!-- Search -->
                <div class="flex-1">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari artikel..." class="w-full px-4 py-3 bg-white text-gray-900 rounded-lg border border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 focus:outline-none transition">
                </div>
                
                <!-- Category Filter -->
                <div>
                    <select name="category" class="w-full md:w-48 px-4 py-3 bg-white text-gray-900 rounded-lg border border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 focus:outline-none transition">
                        <option value="">Semua Kategori</option>
                        @foreach($categories as $key => $label)
                        <option value="{{ $key }}" {{ request('category') == $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                
                <!-- Sort -->
                <div>
                    <select name="sort" class="w-full md:w-48 px-4 py-3 bg-white text-gray-900 rounded-lg border border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 focus:outline-none transition">
                        <option value="published_at" {{ request('sort') == 'published_at' ? 'selected' : '' }}>Terbaru</option>
                        <option value="popular" {{ request('sort') == 'popular' ? 'selected' : '' }}>Terpopuler</option>
                    </select>
                </div>
                
                <button type="submit" class="px-6 py-3 rounded-lg font-semibold transition" style="background: var(--color-primary); color: white;" onmouseover="this.style.background='var(--color-primary-dark)'" onmouseout="this.style.background='var(--color-primary)'">
                    <i class="fas fa-search mr-2"></i>Cari
                </button>
            </form>
        </div>
    </section>

    <!-- Articles Grid -->
    <section class="py-12 px-4 bg-white">
        <div class="container mx-auto max-w-7xl">
            @if($articles->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                @foreach($articles as $article)
                <div class="article-card">
                    @if($article->featured_image)
                    <img src="{{ Storage::url($article->featured_image) }}" alt="{{ $article->title }}" class="article-image">
                    @else
                    <div class="article-image flex items-center justify-center" style="background: linear-gradient(135deg, var(--color-primary) 0%, var(--color-primary-light) 100%);">
                        <i class="fas fa-newspaper text-white text-5xl"></i>
                    </div>
                    @endif
                    
                    <div class="p-6 flex-1 flex flex-col">
                        <div class="mb-3">
                            <a href="{{ route('blog.category', $article->category) }}" class="inline-block px-3 py-1 rounded-full text-xs font-semibold transition" style="background: rgba(10, 102, 194, 0.1); color: var(--color-primary);" onmouseover="this.style.background='rgba(10, 102, 194, 0.2)'" onmouseout="this.style.background='rgba(10, 102, 194, 0.1)'">
                                {{ $article->category_label }}
                            </a>
                            @if($article->is_featured)
                            <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold ml-2" style="background: rgba(255, 149, 0, 0.1); color: #FF9500;">
                                <i class="fas fa-star mr-1"></i>Featured
                            </span>
                            @endif
                        </div>
                        
                        <h3 class="text-xl font-bold mb-3 line-clamp-2" style="color: var(--text-primary);">
                            <a href="{{ route('blog.article', $article->slug) }}" class="transition" style="color: var(--text-primary);" onmouseover="this.style.color='var(--color-primary)'" onmouseout="this.style.color='var(--text-primary)'">
                                {{ $article->title }}
                            </a>
                        </h3>
                        
                        <p class="mb-4 line-clamp-3 text-sm flex-1" style="color: var(--text-secondary);">
                            {{ $article->excerpt }}
                        </p>
                        
                        <div class="flex items-center justify-between text-sm pt-4 border-t mt-auto" style="color: var(--text-tertiary); border-color: var(--surface-tertiary);">
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
                <i class="fas fa-search text-6xl mb-4" style="color: var(--text-tertiary);"></i>
                <p class="text-xl" style="color: var(--text-secondary);">Tidak ada artikel yang ditemukan</p>
            </div>
            @endif
        </div>
    </section>
</div>

    <!-- Footer -->
    <footer class="py-8 px-4 border-t" style="background: var(--surface-secondary); border-color: var(--surface-tertiary); margin-top: auto;">
        <div class="container mx-auto max-w-7xl">
            <div class="text-center" style="color: var(--text-tertiary);">
                <p>&copy; 2025 Bizmark.ID - PT Cangah Pajaratan Mandiri. All rights reserved.</p>
            </div>
        </div>
    </footer>
    
    <script>
        function toggleMobileMenu() {
            const menu = document.getElementById('mobileMenu');
            const button = document.getElementById('mobile-menu-button');
            const isHidden = menu.classList.contains('hidden');
            
            if (isHidden) {
                menu.classList.remove('hidden');
                button.setAttribute('aria-expanded', 'true');
                document.body.style.overflow = 'hidden';
            } else {
                menu.classList.add('hidden');
                button.setAttribute('aria-expanded', 'false');
                document.body.style.overflow = '';
            }
        }
    </script>
</body>
</html>
