<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    
    <title>@yield('title', 'Bizmark.ID - Solusi Perizinan Usaha')</title>
    <meta name="description" content="@yield('meta_description', 'Solusi perizinan usaha terpercaya untuk pertumbuhan bisnis Anda')">
    
    <!-- Open Graph -->
    <meta property="og:title" content="@yield('title', 'Bizmark.ID')">
    <meta property="og:description" content="@yield('meta_description', 'Solusi perizinan usaha terpercaya')">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    
    <!-- Favicons -->
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Playfair+Display:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Custom CSS for Magazine Style -->
    <style>
        :root {
            --color-primary: #0077B5;
            --color-primary-dark: #005582;
            --color-accent: #00A0DC;
        }
        
        * {
            -webkit-tap-highlight-color: transparent;
        }
        
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            overflow-x: hidden;
        }
        
        .headline {
            font-family: 'Playfair Display', Georgia, serif;
            font-weight: 700;
            line-height: 1.2;
            letter-spacing: -0.02em;
        }
        
        .text-gradient {
            background: linear-gradient(135deg, var(--color-primary) 0%, var(--color-accent) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .bg-primary {
            background-color: var(--color-primary);
        }
        
        .bg-primary-dark {
            background-color: var(--color-primary-dark);
        }
        
        .text-primary {
            color: var(--color-primary);
        }
        
        .border-primary {
            border-color: var(--color-primary);
        }
        
        .magazine-section {
            padding: 2rem 1.5rem;
        }
        
        /* LinkedIn Blue themed buttons */
        .btn-primary {
            background: linear-gradient(135deg, var(--color-primary) 0%, var(--color-primary-dark) 100%);
            color: white;
            font-weight: 600;
            padding: 0.875rem 1.75rem;
            border-radius: 0.75rem;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(0, 119, 181, 0.3);
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0, 119, 181, 0.4);
        }
        
        .btn-primary:active {
            transform: translateY(0);
        }
        
        /* Smooth scroll */
        html {
            scroll-behavior: smooth;
        }
        
        /* Content max width */
        .content-container {
            max-width: 768px;
            margin: 0 auto;
        }
    </style>
    
    @stack('styles')
</head>
<body class="bg-white overflow-x-hidden">
    
    <!-- Mobile Header -->
    <header class="fixed top-0 left-0 right-0 z-40 bg-white/95 backdrop-blur-sm border-b border-gray-200">
        <div class="flex items-center justify-between px-6 h-16">
            <!-- Logo -->
            <a href="{{ route('landing') }}" class="flex items-center gap-2">
                <i class="fas fa-building text-yellow-400 text-xl"></i>
                <span class="font-bold text-gray-900 text-lg">
                    Bizmark<span class="text-yellow-400">.ID</span>
                </span>
            </a>
            
            <!-- Menu Button -->
            <button onclick="toggleMobileMenu()" class="p-2 text-gray-700 hover:text-primary transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                </svg>
            </button>
        </div>
    </header>
    
    <!-- Main Content -->
    <main class="pt-16">
        @yield('content')
    </main>
    
    <!-- Mobile Footer -->
    @include('mobile-landing.sections.footer')
    
    <!-- Mobile Menu -->
    <div id="mobileMenu" class="fixed inset-0 z-50 bg-gray-900 transform translate-x-full transition-transform duration-300">
        <div class="flex flex-col h-full">
            <!-- Menu Header -->
            <div class="flex items-center justify-between p-6 border-b border-gray-800">
                <span class="text-white font-bold text-xl">Menu Utama</span>
                <button onclick="toggleMobileMenu()" class="text-white text-2xl">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <!-- Menu Items -->
            <nav class="flex-1 p-6 overflow-y-auto">
                <a href="{{ route('landing') }}" class="block text-white text-lg py-4 border-b border-gray-800">
                    <i class="fas fa-home mr-3"></i> Beranda
                </a>
                <a href="{{ route('services.index') }}" class="block text-white text-lg py-4 border-b border-gray-800">
                    <i class="fas fa-certificate mr-3"></i> Layanan
                </a>
                <a href="{{ route('blog.index') }}" class="block text-white text-lg py-4 border-b border-gray-800">
                    <i class="fas fa-newspaper mr-3"></i> Artikel
                </a>
                <a href="{{ route('career.index') }}" class="block text-white text-lg py-4 border-b border-gray-800">
                    <i class="fas fa-briefcase mr-3"></i> Karir
                </a>
                <a href="{{ route('landing') }}#faq" class="block text-white text-lg py-4 border-b border-gray-800">
                    <i class="fas fa-question-circle mr-3"></i> FAQ
                </a>
                <a href="{{ route('landing') }}#contact" class="block text-white text-lg py-4 border-b border-gray-800">
                    <i class="fas fa-envelope mr-3"></i> Kontak
                </a>
                
                <!-- Legal & Info -->
                <div class="mt-6 pt-6 border-t border-gray-800">
                    <p class="text-gray-400 text-xs mb-3 uppercase tracking-wider">Informasi</p>
                    <a href="{{ route('privacy.policy') }}" class="block text-gray-300 text-sm py-3 border-b border-gray-800">
                        <i class="fas fa-shield-alt mr-3 text-gray-500"></i> Kebijakan Privasi
                    </a>
                    <a href="{{ route('terms.conditions') }}" class="block text-gray-300 text-sm py-3 border-b border-gray-800">
                        <i class="fas fa-file-contract mr-3 text-gray-500"></i> Syarat & Ketentuan
                    </a>
                    <a href="{{ route('sitemap') }}" class="block text-gray-300 text-sm py-3">
                        <i class="fas fa-sitemap mr-3 text-gray-500"></i> Sitemap
                    </a>
                </div>
            </nav>
            
            <!-- Menu Footer -->
            <div class="p-6 border-t border-gray-800">
                <a href="{{ route('login') }}" class="flex items-center justify-center gap-2 bg-white/10 text-white font-semibold py-4 px-6 rounded-xl border border-white/20">
                    <i class="fas fa-sign-in-alt text-xl"></i>
                    <span>Daftar / Masuk</span>
                </a>
            </div>
        </div>
    </div>
    
    <!-- Scripts -->
    <script>
        function toggleMobileMenu() {
            const menu = document.getElementById('mobileMenu');
            menu.classList.toggle('translate-x-full');
        }
        
        // Close menu when clicking outside
        document.getElementById('mobileMenu')?.addEventListener('click', function(e) {
            if (e.target === this) {
                toggleMobileMenu();
            }
        });
    </script>
    
    @stack('scripts')
</body>
</html>
