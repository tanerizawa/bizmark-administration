<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    
    <title>@yield('title', 'Program Beta Tester') - Bizmark.ID</title>
    <meta name="description" content="Program Beta Tester Bizmark.ID - Bergabunglah dalam pengembangan sistem manajemen bisnis terdepan">
    <meta name="robots" content="noindex, nofollow">
    
    <!-- Favicons -->
    <link rel="icon" type="image/png" href="{{ asset('images/pavicon.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('images/pavicon.png') }}">
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <style>
        :root {
            --apple-blue: #007AFF;
            --apple-blue-dark: #0051D5;
            --apple-green: #34C759;
            --light-bg: #FFFFFF;
            --light-bg-secondary: #F5F5F7;
            --light-bg-tertiary: #FAFAFA;
            --light-bg-elevated: rgba(255, 255, 255, 0.95);
            --light-separator: rgba(0, 0, 0, 0.1);
            --light-text-primary: #1D1D1F;
            --light-text-secondary: #86868B;
            --dark-bg: #080F1D;
            --dark-bg-secondary: #0F172A;
            --dark-bg-tertiary: #1E293B;
            --dark-separator: rgba(255, 255, 255, 0.12);
            --dark-text-primary: #F3F4F6;
            --dark-text-secondary: #A7B0C2;
        }
        
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body {
            background: var(--light-bg-secondary);
            color: var(--light-text-primary);
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            overflow-x: hidden;
            min-height: 100vh;
        }
        
        html { 
            scroll-behavior: smooth;
            overflow-x: hidden;
        }
        
        .navbar {
            position: fixed;
            top: 0;
            width: 100%;
            background: var(--light-bg-elevated);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid var(--light-separator);
            z-index: 1000;
            transition: all 0.3s ease;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        }
        
        .hero-gradient {
            background: linear-gradient(135deg, #F5F5F7 0%, #E8E8ED 50%, #D1D1D6 100%);
            position: relative;
            overflow: hidden;
        }
        
        .card {
            background: var(--light-bg);
            border: 1px solid var(--light-separator);
            border-radius: 12px;
            transition: all 0.3s ease;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
        }
        
        .card:hover {
            border-color: var(--apple-blue);
            box-shadow: 0 8px 24px rgba(0, 122, 255, 0.15);
        }
        
        .btn-primary {
            background: linear-gradient(135deg, var(--apple-blue) 0%, var(--apple-blue-dark) 100%);
            color: white;
            padding: 12px 32px;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(0, 122, 255, 0.4);
        }
        
        .btn-secondary {
            background: var(--light-bg);
            color: var(--light-text-primary);
            padding: 12px 32px;
            border-radius: 10px;
            font-weight: 600;
            border: 1px solid var(--light-separator);
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        
        .btn-secondary:hover {
            border-color: var(--apple-blue);
            color: var(--apple-blue);
            box-shadow: 0 4px 12px rgba(0, 122, 255, 0.15);
        }
        
        .mobile-menu {
            display: none;
            position: fixed;
            top: 65px;
            left: 0;
            right: 0;
            background: var(--light-bg-elevated);
            backdrop-filter: blur(20px);
            padding: 20px;
            border-bottom: 1px solid var(--light-separator);
            z-index: 999;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        }
        
        .mobile-menu.active { display: block; }
        
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }
        
        @media (max-width: 768px) {
            .navbar { padding: 12px 0; }
        }
    </style>
    
    @yield('styles')
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar">
        <div class="container mx-auto px-4 py-4">
            <div class="flex justify-between items-center">
                <a href="{{ route('beta-tester.index') }}" class="flex items-center space-x-2">
                    <img src="{{ asset('images/logo.png') }}" alt="Bizmark.ID Logo" class="h-7 w-auto">
                    <div class="text-xs font-semibold" style="color: var(--light-text-secondary);">Beta Tester</div>
                </a>
                
                <div class="hidden md:flex items-center space-x-6">
                    <a href="{{ route('beta-tester.index') }}" class="hover:text-blue-600 transition" style="color: var(--light-text-primary);">
                        <i class="fas fa-home mr-2"></i>Beranda
                    </a>
                    <a href="{{ route('beta-tester.register') }}" class="hover:text-blue-600 transition" style="color: var(--light-text-primary);">
                        <i class="fas fa-user-plus mr-2"></i>Daftar
                    </a>
                    <a href="/" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 rounded-lg transition text-white">
                        <i class="fas fa-arrow-left mr-2"></i>Kembali ke Website
                    </a>
                </div>
                
                <button class="md:hidden text-2xl" onclick="toggleMobileMenu()">
                    <i class="fas fa-bars"></i>
                </button>
            </div>
        </div>
    </nav>
    
    <div id="mobileMenu" class="mobile-menu">
        <a href="{{ route('beta-tester.index') }}" class="block py-2 hover:text-blue-600" style="color: var(--light-text-primary);">
            <i class="fas fa-home mr-2"></i>Beranda
        </a>
        <a href="{{ route('beta-tester.register') }}" class="block py-2 hover:text-blue-600" style="color: var(--light-text-primary);">
            <i class="fas fa-user-plus mr-2"></i>Daftar
        </a>
        <a href="/" class="block py-3 mt-2 bg-blue-600 hover:bg-blue-700 rounded-lg text-center text-white transition">
            <i class="fas fa-arrow-left mr-2"></i>Kembali ke Website
        </a>
    </div>

    <!-- Main Content -->
    <main class="pt-20">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="py-12 px-4 mt-16" style="background: var(--light-bg); border-top: 1px solid var(--light-separator);">
        <div class="container mx-auto max-w-6xl">
            <div class="grid md:grid-cols-4 gap-8 mb-8">
                <div>
                    <div class="flex items-center space-x-3 mb-4">
                        <img src="{{ asset('images/logo.png') }}" alt="Bizmark.ID" class="h-7 w-auto">
                    </div>
                    <p class="text-sm" style="color: var(--light-text-secondary);">
                        Platform manajemen bisnis terpadu untuk solusi perizinan dan administrasi perusahaan modern.
                    </p>
                </div>
                
                <div>
                    <h3 class="font-bold mb-4" style="color: var(--light-text-primary);">Program</h3>
                    <ul class="space-y-2" style="color: var(--light-text-secondary);">
                        <li><a href="{{ route('beta-tester.index') }}" class="hover:text-blue-600 transition text-sm">Tentang Program</a></li>
                        <li><a href="{{ route('beta-tester.register') }}" class="hover:text-blue-600 transition text-sm">Pendaftaran</a></li>
                        <li><a href="{{ route('beta-tester.index') }}#timeline" class="hover:text-blue-600 transition text-sm">Timeline</a></li>
                        <li><a href="{{ route('beta-tester.index') }}#benefits" class="hover:text-blue-600 transition text-sm">Benefit</a></li>
                    </ul>
                </div>
                
                <div>
                    <h3 class="font-bold mb-4" style="color: var(--light-text-primary);">Perusahaan</h3>
                    <ul class="space-y-2" style="color: var(--light-text-secondary);">
                        <li><a href="/#about" class="hover:text-blue-600 transition text-sm">Tentang Kami</a></li>
                        <li><a href="/#services" class="hover:text-blue-600 transition text-sm">Layanan</a></li>
                        <li><a href="/#contact" class="hover:text-blue-600 transition text-sm">Kontak</a></li>
                        <li><a href="{{ route('login') }}" class="hover:text-blue-600 transition text-sm">Login</a></li>
                    </ul>
                </div>
                
                <div>
                    <h3 class="font-bold mb-4" style="color: var(--light-text-primary);">Hubungi Kami</h3>
                    <ul class="space-y-3" style="color: var(--light-text-secondary);">
                        <li class="flex items-start text-sm">
                            <i class="fas fa-envelope mr-2 mt-1"></i>
                            <a href="mailto:cs@bizmark.id" class="hover:text-blue-600">cs@bizmark.id</a>
                        </li>
                        <li class="flex items-start text-sm">
                            <i class="fas fa-phone mr-2 mt-1"></i>
                            <a href="tel:+6283879602855" class="hover:text-blue-600">+62 838 7960 2855</a>
                        </li>
                    </ul>
                    <div class="mt-4">
                        <h4 class="font-bold mb-3 text-sm">Ikuti Kami</h4>
                        <div class="flex space-x-3">
                            <a href="https://facebook.com/bizmark.id" target="_blank" rel="noopener" aria-label="Facebook" class="w-9 h-9 bg-blue-500 rounded-lg flex items-center justify-center hover:bg-blue-600 transition">
                                <i class="fab fa-facebook-f text-white"></i>
                            </a>
                            <a href="https://instagram.com/bizmark.id" target="_blank" rel="noopener" aria-label="Instagram" class="w-9 h-9 bg-pink-600 rounded-lg flex items-center justify-center hover:bg-pink-700 transition">
                                <i class="fab fa-instagram text-white"></i>
                            </a>
                            <a href="https://linkedin.com/company/bizmark-id" target="_blank" rel="noopener" aria-label="LinkedIn" class="w-9 h-9 bg-blue-700 rounded-lg flex items-center justify-center hover:bg-blue-800 transition">
                                <i class="fab fa-linkedin-in text-white"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="border-t pt-6" style="border-color: var(--light-separator);">
                <div class="text-center text-sm" style="color: var(--light-text-secondary);">
                    <p>&copy; 2025 Bizmark.ID - PT Cangah Pajaratan Mandiri. All rights reserved.</p>
                </div>
            </div>
        </div>
    </footer>

    <!-- WhatsApp Floating Button -->
    <a href="https://wa.me/6283879602855?text=Halo%20Bizmark.ID,%20saya%20ingin%20bertanya%20tentang%20Program%20Beta%20Tester" 
       target="_blank" 
       rel="noopener"
       aria-label="Chat WhatsApp"
       class="fixed bottom-6 right-6 w-14 h-14 bg-green-500 rounded-full flex items-center justify-center shadow-lg hover:bg-green-600 transition-all hover:scale-110 z-50"
       style="animation: pulse 2s infinite;">
        <i class="fab fa-whatsapp text-white text-2xl"></i>
    </a>

    <script>
        function toggleMobileMenu() {
            const menu = document.getElementById('mobileMenu');
            menu.classList.toggle('active');
        }
        
        // Close mobile menu when clicking outside
        document.addEventListener('click', function(event) {
            const menu = document.getElementById('mobileMenu');
            const button = event.target.closest('button');
            
            if (!menu.contains(event.target) && !button) {
                menu.classList.remove('active');
            }
        });
    </script>
    
    @yield('scripts')
</body>
</html>
