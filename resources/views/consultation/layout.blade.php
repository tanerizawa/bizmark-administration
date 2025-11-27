<!DOCTYPE html>
<html lang="id" x-data="{ darkMode: false }" :class="{ 'dark': darkMode }">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- SEO Meta Tags -->
    <title>@yield('meta_title', 'Bizmark.ID - Konsultan Perizinan')</title>
    <meta name="description" content="@yield('meta_description', 'Konsultan perizinan industri terpercaya di Indonesia')">
    <meta name="keywords" content="@yield('meta_keywords', 'perizinan, konsultan, AMDAL, UKL-UPL')">
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <style>
        [x-cloak] { display: none !important; }
        body { font-family: 'Inter', sans-serif; }
    </style>
    
    @stack('styles')
</head>
<body class="antialiased">
    <!-- Simple Navigation -->
    <nav class="fixed top-0 left-0 right-0 z-50 bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-700 shadow-sm">
        <div class="container mx-auto px-4">
            <div class="flex items-center justify-between h-16">
                <!-- Logo -->
                <a href="{{ route('landing') }}" class="flex items-center gap-2">
                    <i class="fas fa-building text-blue-600 text-xl"></i>
                    <span class="text-xl font-bold text-gray-900 dark:text-white">
                        Bizmark<span class="text-blue-600">.ID</span>
                    </span>
                </a>
                
                <!-- Navigation Links -->
                <div class="hidden md:flex items-center gap-6">
                    <a href="{{ route('landing') }}" class="text-gray-600 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 transition">
                        <i class="fas fa-home mr-2"></i>Beranda
                    </a>
                    <a href="{{ route('blog.index') }}" class="text-gray-600 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 transition">
                        <i class="fas fa-newspaper mr-2"></i>Artikel
                    </a>
                    <a href="{{ route('contact.index') }}" class="text-gray-600 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 transition">
                        <i class="fas fa-envelope mr-2"></i>Kontak
                    </a>
                </div>
                
                <!-- Dark Mode Toggle -->
                <button @click="darkMode = !darkMode" class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition">
                    <i class="fas fa-moon text-gray-600 dark:text-gray-300" x-show="!darkMode"></i>
                    <i class="fas fa-sun text-yellow-500" x-show="darkMode" x-cloak></i>
                </button>
            </div>
        </div>
    </nav>
    
    <!-- Main Content -->
    <main>
        @yield('content')
    </main>
    
    <!-- Simple Footer -->
    <footer class="bg-gray-900 text-white py-8">
        <div class="container mx-auto px-4">
            <div class="flex flex-col md:flex-row items-center justify-between gap-4">
                <div class="text-center md:text-left">
                    <p class="font-bold mb-1">PT Cangah Pajaratan Mandiri</p>
                    <p class="text-sm text-gray-400">Konsultan Perizinan Industri Terpercaya</p>
                </div>
                <div class="flex items-center gap-4">
                    <a href="https://wa.me/6283879602855" target="_blank" rel="noopener noreferrer" class="text-gray-400 hover:text-white transition">
                        <i class="fab fa-whatsapp text-xl"></i>
                    </a>
                    <a href="tel:+6283879602855" class="text-gray-400 hover:text-white transition">
                        <i class="fas fa-phone text-xl"></i>
                    </a>
                    <a href="mailto:info@bizmark.id" class="text-gray-400 hover:text-white transition">
                        <i class="fas fa-envelope text-xl"></i>
                    </a>
                </div>
            </div>
            <div class="text-center mt-6 pt-6 border-t border-gray-800">
                <p class="text-sm text-gray-400">
                    &copy; {{ date('Y') }} Bizmark.ID. All rights reserved.
                </p>
            </div>
        </div>
    </footer>
    
    @stack('scripts')
</body>
</html>
