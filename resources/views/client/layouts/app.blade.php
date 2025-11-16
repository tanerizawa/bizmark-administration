<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#007AFF">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <title>@yield('title', 'Client Portal') - Bizmark.id</title>
    
    <!-- External CSS - CDN Only -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <style>
        [x-cloak] { display: none !important; }
        
        /* Touch optimization */
        * {
            -webkit-tap-highlight-color: transparent;
            -webkit-touch-callout: none;
        }
        
        /* iOS viewport height fix */
        :root {
            --vh: 1vh;
        }
        
        body {
            min-height: 100vh;
            min-height: calc(var(--vh, 1vh) * 100);
        }
        
        /* Smooth scrolling */
        html {
            scroll-behavior: smooth;
        }
        
        /* Better touch feedback */
        button, a {
            -webkit-tap-highlight-color: transparent;
        }
        
        button:active, a:active {
            opacity: 0.8;
        }
        
        /* Image lazy loading blur-up effect */
        img[loading="lazy"] {
            opacity: 0;
            transition: opacity 0.3s ease-in-out;
        }
        
        img[loading="lazy"].loaded {
            opacity: 1;
        }
        
        img[loading="lazy"]:not(.loaded) {
            background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
            background-size: 200% 100%;
            animation: shimmer 1.5s infinite;
        }
        
        /* Loading skeleton */
        .skeleton {
            background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
            background-size: 200% 100%;
            animation: shimmer 1.5s infinite;
            border-radius: 0.5rem;
        }
        
        .skeleton-text {
            height: 1rem;
            margin-bottom: 0.5rem;
        }
        
        .skeleton-title {
            height: 1.5rem;
            width: 60%;
            margin-bottom: 1rem;
        }
        
        .skeleton-card {
            height: 120px;
        }
        
        .skeleton-avatar {
            width: 3rem;
            height: 3rem;
            border-radius: 50%;
        }
        
        @keyframes shimmer {
            0% { background-position: -200% 0; }
            100% { background-position: 200% 0; }
        }
        
        /* PWA + mobile visibility helpers */
        .pwa-only {
            display: none !important;
        }
        
        .browser-only {
            display: block;
        }
        
        html.pwa-mode .pwa-only,
        html.mobile-ui .pwa-only {
            display: flex !important;
        }
        
        html.pwa-mode .browser-only,
        html.mobile-ui .browser-only,
        html.pwa-mode .desktop-header,
        html.mobile-ui .desktop-header,
        html.pwa-mode aside.browser-only,
        html.mobile-ui aside.browser-only {
            display: none !important;
        }
        
        html.pwa-mode .lg\:ml-64,
        html.mobile-ui .lg\:ml-64 {
            margin-left: 0 !important;
        }
        
        /* Fallback when JS cannot detect standalone mode */
        @media (display-mode: standalone) {
            .pwa-only {
                display: flex !important;
            }
            
            .browser-only,
            .desktop-header,
            aside.browser-only {
                display: none !important;
            }
            
            .lg\:ml-64 {
                margin-left: 0 !important;
            }
        }
        
        /* Mobile + PWA specific layout */
        html.pwa-mode .pwa-header,
        html.mobile-ui .pwa-header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 50;
            background: white;
            border-bottom: 1px solid #e5e7eb;
            height: 56px;
            display: flex;
            align-items: center;
            padding-left: calc(1rem + env(safe-area-inset-left));
            padding-right: calc(1rem + env(safe-area-inset-right));
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        }
        
        html.pwa-mode body,
        html.mobile-ui body {
            padding-top: 56px !important;
            padding-bottom: 65px !important;
        }
        
        @media (display-mode: standalone) {
            .pwa-header {
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                z-index: 50;
                background: white;
                border-bottom: 1px solid #e5e7eb;
                height: 56px;
                display: flex;
                align-items: center;
                padding-left: calc(1rem + env(safe-area-inset-left));
                padding-right: calc(1rem + env(safe-area-inset-right));
                box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            }
            
            body {
                padding-top: 56px !important;
                padding-bottom: 65px !important;
            }
        }
        
        html.pwa-mode .bottom-nav-text,
        html.mobile-ui .bottom-nav-text {
            display: none;
        }
        
        html.pwa-mode .bottom-nav-icon,
        html.mobile-ui .bottom-nav-icon {
            font-size: 1.5rem;
        }
        
        html.pwa-mode .center-action-btn,
        html.mobile-ui .center-action-btn {
            width: 3.5rem;
            height: 3.5rem;
            margin-top: -1.75rem;
            box-shadow: 0 4px 12px rgba(99, 102, 241, 0.4);
        }
        
        html.pwa-mode .pwa-header *,
        html.mobile-ui .pwa-header * {
            transition: all 0.2s ease;
        }
        
        html.pwa-mode .pwa-header button,
        html.mobile-ui .pwa-header button,
        html.pwa-mode .pwa-header a,
        html.mobile-ui .pwa-header a {
            min-width: 44px;
            min-height: 44px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
        
        html.pwa-mode body,
        html.mobile-ui body {
            padding-left: env(safe-area-inset-left);
            padding-right: env(safe-area-inset-right);
        }
        
        .floating-panel {
            max-width: 20rem;
        }
        
        html.mobile-ui .floating-panel {
            width: auto;
            left: 1rem !important;
            right: 1rem !important;
            max-width: none;
        }
        
        .floating-panel--notifications {
            max-height: 24rem;
        }
        
        html.mobile-ui .floating-panel--notifications {
            max-height: calc(80vh - 3rem);
        }
        
        .notification-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 1.25rem;
            height: 1.25rem;
            font-size: 0.65rem;
            line-height: 1;
        }
        
        /* Mobile browser fallback when JS is disabled */
        @media (max-width: 1023px) {
            .desktop-header {
                display: none !important;
            }
            
            aside.browser-only {
                display: none !important;
            }
            
            .lg\:ml-64 {
                margin-left: 0 !important;
            }
            
            .pwa-only {
                display: flex !important;
            }
            
            body {
                padding-top: 56px;
                padding-bottom: 65px;
            }
        }
    </style>
    
    @stack('styles')
    
    <script>
        // Detect standalone/mobile mode ASAP to avoid flicker
        (function() {
            const docEl = document.documentElement;
            const standaloneMedia = window.matchMedia('(display-mode: standalone)');
            const mobileMedia = window.matchMedia('(max-width: 1023px)');
            let previousStandalone = null;
            
            const detectStandalone = () => standaloneMedia.matches || window.navigator.standalone === true;
            
            const applyModeClasses = () => {
                const standalone = detectStandalone();
                const prefersMobileUI = standalone || (mobileMedia && mobileMedia.matches);
                
                docEl.classList.toggle('pwa-mode', standalone);
                docEl.classList.toggle('browser-mode', !standalone);
                docEl.classList.toggle('mobile-ui', prefersMobileUI);
                
                if (previousStandalone !== standalone) {
                    console.log(standalone ? '✅ PWA Mode Detected' : '🌐 Browser Mode Detected');
                    previousStandalone = standalone;
                }
            };
            
            const registerMediaListener = (media) => {
                if (!media) return;
                if (typeof media.addEventListener === 'function') {
                    media.addEventListener('change', applyModeClasses);
                } else if (typeof media.addListener === 'function') {
                    media.addListener(applyModeClasses);
                }
            };
            
            registerMediaListener(standaloneMedia);
            registerMediaListener(mobileMedia);
            window.addEventListener('pageshow', applyModeClasses);
            window.addEventListener('appinstalled', applyModeClasses);
            applyModeClasses();
            
            window.__IS_STANDALONE__ = detectStandalone;
        })();
        
        // Fix viewport height on mobile
        function setVh() {
            const vh = window.innerHeight * 0.01;
            document.documentElement.style.setProperty('--vh', `${vh}px`);
        }
        setVh();
        window.addEventListener('resize', setVh);
        window.addEventListener('orientationchange', setVh);
        
        // Force refresh service worker for PWA updates
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.getRegistrations().then(function(registrations) {
                for(let registration of registrations) {
                    registration.update();
                }
            });
        }
    </script>
</head>
<body class="bg-gray-100 min-h-screen text-gray-900">
    @php
        $client = auth('client')->user();
        $draftCount = \App\Models\PermitApplication::where('client_id', $client->id)
            ->where('status', 'draft')
            ->count();
        $submittedCount = \App\Models\PermitApplication::where('client_id', $client->id)
            ->whereIn('status', ['submitted', 'under_review', 'document_incomplete'])
            ->count();
        $unreadAdminNotes = \App\Models\ApplicationNote::whereHas('application', function($query) use ($client) {
                $query->where('client_id', $client->id);
            })
            ->where('author_type', 'admin')
            ->where('is_internal', false)
            ->where('is_read', false)
            ->count();
        $activeProjects = \App\Models\Project::where('client_id', $client->id)
            ->whereHas('status', function($q) {
                $q->whereNotIn('name', ['Selesai', 'Dibatalkan']);
            })
            ->count();
        $pendingDocuments = \App\Models\ApplicationDocument::whereHas('application', function($query) use ($client) {
                $query->where('client_id', $client->id);
            })
            ->where('status', 'pending')
            ->count();
        $recentNotifications = \App\Models\ApplicationNote::with(['application:id,application_number,status'])
            ->whereHas('application', function($query) use ($client) {
                $query->where('client_id', $client->id);
            })
            ->visibleToClient()
            ->byAdmin()
            ->latest()
            ->limit(5)
            ->get();
        $notificationCount = $unreadAdminNotes;

        $navItems = [
            [
                'label' => 'Dashboard',
                'icon' => 'fa-house',
                'route' => route('client.dashboard'),
                'active' => request()->routeIs('client.dashboard'),
            ],
            [
                'label' => 'Katalog Izin',
                'icon' => 'fa-layer-group',
                'route' => route('client.services.index'),
                'active' => request()->routeIs('client.services.*') && !request()->routeIs('client.applications.*'),
            ],
            [
                'label' => 'Permohonan Saya',
                'icon' => 'fa-file-signature',
                'route' => route('client.applications.index'),
                'active' => request()->routeIs('client.applications.*'),
                'badge' => $submittedCount + $draftCount,
                'badge_color' => 'bg-indigo-600'
            ],
            [
                'label' => 'Proyek Aktif',
                'icon' => 'fa-diagram-project',
                'route' => route('client.projects.index'),
                'active' => request()->routeIs('client.projects.*'),
                'badge' => $activeProjects,
                'badge_color' => 'bg-green-600'
            ],
            [
                'label' => 'Dokumen',
                'icon' => 'fa-folder-open',
                'route' => route('client.documents.index'),
                'active' => request()->routeIs('client.documents.*'),
                'badge' => $pendingDocuments,
                'badge_color' => 'bg-amber-600'
            ],
            [
                'label' => 'Profil & Akun',
                'icon' => 'fa-user-circle',
                'route' => route('client.profile.edit'),
                'active' => request()->routeIs('client.profile.*'),
            ],
        ];
    @endphp
    <div x-data="{ sidebarOpen: false }" class="flex min-h-screen overflow-hidden">
        <!-- PWA-Only Minimal Header (App-like) -->
        <header class="pwa-only pwa-header">
            <div class="flex items-center justify-between w-full">
                <!-- Left: Brand -->
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 bg-gradient-to-br from-indigo-600 to-blue-600 rounded-lg flex items-center justify-center shadow-sm">
                        <i class="fas fa-building text-white text-sm"></i>
                    </div>
                    <h1 class="text-base font-bold text-gray-800">
                        Bizmark<span class="text-indigo-600">.id</span>
                    </h1>
                </div>
                
                <!-- Right: Action Icons -->
                <div class="flex items-center gap-4">
                    <!-- Notifications Link -->
                    <a 
                        href="{{ route('client.notifications.index') }}"
                        class="relative text-gray-600 hover:text-indigo-600 transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500/70 rounded-full"
                        aria-label="Lihat notifikasi"
                    >
                        <i class="fas fa-bell text-xl"></i>
                        @if($notificationCount > 0)
                        <span class="notification-badge absolute -top-1 -right-1 bg-red-500 text-white rounded-full font-semibold shadow-sm px-1.5">
                            {{ $notificationCount > 9 ? '9+' : $notificationCount }}
                        </span>
                        @endif
                    </a>
                    
                    <!-- Profile Link -->
                    <a 
                        href="{{ route('client.profile.edit') }}"
                        class="flex items-center gap-2 text-gray-600 hover:text-indigo-600 transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500/70 rounded-full"
                        aria-label="Buka profil"
                    >
                        <div class="w-8 h-8 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center text-sm font-semibold">
                            {{ strtoupper(substr($client->name, 0, 1)) }}
                        </div>
                    </a>
                </div>
            </div>
        </header>
        
        <div 
            class="fixed inset-0 bg-black/40 z-40 lg:hidden"
            x-show="sidebarOpen"
            x-transition.opacity
            @click="sidebarOpen = false"
            x-cloak
        ></div>
        
        <!-- Sidebar (Browser Mode) - Hidden in PWA standalone -->
        <aside 
            :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
            class="browser-only fixed inset-y-0 left-0 z-50 w-64 bg-white border-r border-gray-200 shadow-lg transform transition-transform duration-300 ease-in-out lg:translate-x-0 lg:shadow-none"
        >
            <div class="flex items-center justify-between h-16 px-6 border-b border-gray-100">
                <h1 class="text-xl font-bold text-gray-800">Bizmark<span class="text-indigo-600">.id</span></h1>
                <button @click="sidebarOpen = false" class="lg:hidden text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <div class="px-6 py-5 border-b border-gray-100">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-2xl bg-indigo-100 text-indigo-600 flex items-center justify-center text-lg font-semibold">
                        {{ strtoupper(substr($client->name, 0, 1)) }}
                    </div>
                    <div class="min-w-0">
                        <p class="font-semibold text-gray-900 truncate">{{ $client->name }}</p>
                        <p class="text-sm text-gray-500 truncate">{{ $client->email }}</p>
                    </div>
                </div>
                <p class="mt-4 text-xs text-gray-500 leading-relaxed">
                    Akses cepat ke navigasi utama. Detail status, notifikasi, dan statistik tampil di header halaman.
                </p>
            </div>

            <nav class="flex-1 overflow-y-auto px-4 py-6 space-y-1">
                @foreach($navItems as $item)
                    <a 
                        href="{{ $item['route'] }}" 
                        class="flex items-center px-4 py-3 rounded-lg text-sm font-medium transition {{ $item['active'] ? 'bg-indigo-50 text-indigo-700 border border-indigo-100' : 'text-gray-600 hover:bg-gray-50' }}"
                    >
                        <i class="fas {{ $item['icon'] }} w-5"></i>
                        <span class="ml-3 flex-1">{{ $item['label'] }}</span>
                        @if(!empty($item['badge']))
                            <span class="text-xs font-semibold text-white px-2 py-0.5 rounded-full {{ $item['badge_color'] ?? 'bg-gray-500' }}">
                                {{ $item['badge'] }}
                            </span>
                        @endif
                    </a>
                @endforeach
            </nav>

            <div class="p-4 border-t border-gray-100 space-y-3">
                <div class="text-xs text-gray-500">
                    <p class="font-semibold text-gray-600 mb-1">Butuh Bantuan?</p>
                    <a href="mailto:support@bizmark.id" class="inline-flex items-center gap-2 text-indigo-600 font-semibold">
                        <i class="fas fa-headset"></i> support@bizmark.id
                    </a>
                </div>
                <form method="POST" action="{{ route('client.logout') }}">
                    @csrf
                    <button type="submit" class="flex items-center w-full px-4 py-3 text-sm font-semibold text-gray-600 hover:bg-gray-50 rounded-lg transition">
                        <i class="fas fa-sign-out-alt w-5"></i>
                        <span class="ml-3">Logout</span>
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden lg:ml-64">
            
            <!-- Desktop/Browser Header (Hidden in PWA Standalone) -->
            <header class="browser-only desktop-header bg-white shadow-sm z-10 sticky top-0">
                <div class="flex items-center justify-between h-20 px-4 sm:px-6">
                    <!-- Hamburger Menu (Mobile Browser Only) -->
                    <button @click="sidebarOpen = !sidebarOpen" class="lg:hidden text-gray-600 hover:text-indigo-600 transition-colors">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                    
                    <div class="flex-1 flex items-center justify-between gap-4 lg:gap-0">
                        <div class="space-y-0.5">
                            <h2 class="text-xl sm:text-2xl font-bold text-gray-800 leading-tight">@yield('page-title', 'Portal Klien')</h2>
                            <p class="text-sm text-gray-500 hidden sm:block">@yield('page-subtitle', 'Selamat datang kembali, ' . $client->name . '!')</p>
                            <div class="hidden md:flex items-center gap-2">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs bg-indigo-50 text-indigo-700">
                                    Draft: {{ $draftCount }}
                                </span>
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs bg-emerald-50 text-emerald-700">
                                    Sedang Diproses: {{ $submittedCount }}
                                </span>
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs bg-amber-50 text-amber-700">
                                    Dokumen Pending: {{ $pendingDocuments }}
                                </span>
                            </div>
                        </div>
                        
                        <div class="flex items-center space-x-3 sm:space-x-4">
                            <div class="hidden lg:flex items-center bg-gray-50 px-3 py-2 rounded-lg text-sm text-gray-600">
                                <i class="fas fa-calendar-day mr-2 text-gray-400"></i>
                                <span>{{ now()->translatedFormat('l, d F Y') }}</span>
                            </div>
                            <div class="relative" x-data="{ open: false }" @click.outside="open = false" @keydown.escape.window="open = false">
                                <button 
                                    type="button"
                                    aria-haspopup="true"
                                    :aria-expanded="open"
                                    title="Lihat notifikasi"
                                    class="relative text-gray-600 hover:text-indigo-600 focus:outline-none"
                                    @click="open = !open"
                                >
                                    <i class="fas fa-bell text-xl"></i>
                                    @if($notificationCount > 0)
                                    <span class="notification-badge absolute -top-1 -right-1 bg-red-500 text-white rounded-full font-semibold px-1.5">
                                        {{ $notificationCount > 9 ? '9+' : $notificationCount }}
                                    </span>
                                    @endif
                                </button>
                                
                                @include('client.components.notification-dropdown')
                            </div>
                            <div class="flex items-center gap-2 bg-white border border-gray-200 rounded-full px-3 py-1.5 shadow-sm">
                                <div class="w-8 h-8 rounded-full bg-indigo-100 text-indigo-700 flex items-center justify-center font-semibold">
                                    {{ strtoupper(substr($client->name, 0, 1)) }}
                                </div>
                                <div class="hidden sm:block text-xs leading-tight">
                                    <p class="font-semibold text-gray-700">{{ \Illuminate\Support\Str::limit($client->company_name ?? $client->name, 18) }}</p>
                                    <p class="text-gray-400">Client Premium</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Content -->
            <main class="flex-1 overflow-y-auto p-4 sm:p-6">
                
                @if (session('success'))
                    <div class="mb-6 p-4 bg-green-100 border-l-4 border-green-500 text-green-700 rounded animate-fade-in">
                        <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
                    </div>
                @endif

                @if (session('error'))
                    <div class="mb-6 p-4 bg-red-100 border-l-4 border-red-500 text-red-700 rounded animate-fade-in">
                        <i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="mb-6 p-4 bg-red-100 border-l-4 border-red-500 text-red-700 rounded animate-fade-in">
                        <p class="font-semibold mb-2"><i class="fas fa-exclamation-triangle mr-2"></i>Terjadi Kesalahan:</p>
                        <ul class="list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @yield('content')
                
            </main>
            
            <!-- Mobile Bottom Navigation -->
            <nav class="lg:hidden fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 z-50 safe-area-bottom" aria-label="Navigasi utama mobile">
                <div class="grid grid-cols-5 h-16">
                    <!-- Dashboard -->
                    <a href="{{ route('client.dashboard') }}" 
                       class="flex flex-col items-center justify-center gap-1 {{ request()->routeIs('client.dashboard') ? 'text-indigo-600' : 'text-gray-500' }} transition-colors">
                        <i class="fas fa-home text-xl bottom-nav-icon"></i>
                        <span class="text-[10px] font-medium bottom-nav-text">Home</span>
                    </a>
                    
                    <!-- Proyek Aktif -->
                    <a href="{{ route('client.projects.index') }}" 
                       class="flex flex-col items-center justify-center gap-1 relative {{ request()->routeIs('client.projects.*') ? 'text-indigo-600' : 'text-gray-500' }} transition-colors">
                        <i class="fas fa-diagram-project text-xl bottom-nav-icon"></i>
                        <span class="text-[10px] font-medium bottom-nav-text">Proyek</span>
                        @if($activeProjects > 0)
                        <span class="absolute top-1 right-2 inline-flex min-w-[1.15rem] h-5 bg-green-500 text-white text-[10px] rounded-full items-center justify-center font-semibold px-1.5 leading-none shadow-sm">
                            {{ $activeProjects > 9 ? '9+' : $activeProjects }}
                        </span>
                        @endif
                    </a>
                    
                    <!-- Ajukan (Center - Elevated) -->
                    <a href="{{ route('client.applications.create') }}" 
                       class="flex flex-col items-center justify-center relative -top-4">
                        <div class="w-14 h-14 rounded-full bg-gradient-to-r from-indigo-600 to-blue-600 flex items-center justify-center shadow-lg hover:shadow-xl transition-shadow center-action-btn">
                            <i class="fas fa-plus text-white text-2xl"></i>
                        </div>
                        <span class="text-[10px] font-medium text-gray-500 mt-1">Ajukan</span>
                    </a>
                    
                    <!-- Permohonan/Izin -->
                    <a href="{{ route('client.applications.index') }}" 
                       class="flex flex-col items-center justify-center gap-1 relative {{ request()->routeIs('client.applications.*') ? 'text-indigo-600' : 'text-gray-500' }} transition-colors">
                        <i class="fas fa-file-signature text-xl bottom-nav-icon"></i>
                        <span class="text-[10px] font-medium bottom-nav-text">Izin</span>
                        @if($submittedCount + $draftCount > 0)
                        <span class="absolute top-1 right-2 inline-flex min-w-[1.15rem] h-5 bg-indigo-500 text-white text-[10px] rounded-full items-center justify-center font-semibold px-1.5 leading-none shadow-sm">
                            {{ $submittedCount + $draftCount > 9 ? '9+' : $submittedCount + $draftCount }}
                        </span>
                        @endif
                    </a>
                    
                    <!-- Dokumen -->
                    <a href="{{ route('client.documents.index') }}" 
                       class="flex flex-col items-center justify-center gap-1 relative {{ request()->routeIs('client.documents.*') ? 'text-indigo-600' : 'text-gray-500' }} transition-colors">
                        <i class="fas fa-folder-open text-xl bottom-nav-icon"></i>
                        <span class="text-[10px] font-medium bottom-nav-text">Dokumen</span>
                        @if($pendingDocuments > 0)
                        <span class="absolute top-1 right-2 inline-flex min-w-[1.15rem] h-5 bg-amber-500 text-white text-[10px] rounded-full items-center justify-center font-semibold px-1.5 leading-none shadow-sm">
                            {{ $pendingDocuments > 9 ? '9+' : $pendingDocuments }}
                        </span>
                        @endif
                    </a>
                </div>
            </nav>
        </div>
    </div>

    @stack('scripts')
    
    <!-- Push Notification JavaScript -->
    <script>
        // VAPID Public Key from Laravel config
        const VAPID_PUBLIC_KEY = '{{ config('webpush.vapid.public_key') }}';
        
        // Helper function to convert base64 to Uint8Array
        function urlBase64ToUint8Array(base64String) {
            const padding = '='.repeat((4 - base64String.length % 4) % 4);
            const base64 = (base64String + padding)
                .replace(/\-/g, '+')
                .replace(/_/g, '/');
            
            const rawData = window.atob(base64);
            const outputArray = new Uint8Array(rawData.length);
            
            for (let i = 0; i < rawData.length; ++i) {
                outputArray[i] = rawData.charCodeAt(i);
            }
            return outputArray;
        }
        
        // Check if PWA is installed (standalone mode)
        function isPWA() {
            if (typeof window.__IS_STANDALONE__ === 'function') {
                return window.__IS_STANDALONE__();
            }
            
            if (window.navigator.standalone === true) {
                return true;
            }
            
            return window.matchMedia('(display-mode: standalone)').matches;
        }
        
        // Subscribe to push notifications
        async function subscribeToPushNotifications() {
            try {
                // Check if browser supports notifications
                if (!('Notification' in window)) {
                    console.log('This browser does not support notifications');
                    return false;
                }
                
                // Check if service worker is supported
                if (!('serviceWorker' in navigator)) {
                    console.log('Service worker not supported');
                    return false;
                }
                
                // Check current permission
                let permission = Notification.permission;
                
                // If permission is default, request it
                if (permission === 'default') {
                    permission = await Notification.requestPermission();
                }
                
                // If permission denied, exit
                if (permission !== 'granted') {
                    console.log('Notification permission denied');
                    return false;
                }
                
                // Get service worker registration
                const registration = await navigator.serviceWorker.ready;
                
                // Check if already subscribed
                let subscription = await registration.pushManager.getSubscription();
                
                if (!subscription) {
                    // Subscribe to push notifications
                    subscription = await registration.pushManager.subscribe({
                        userVisibleOnly: true,
                        applicationServerKey: urlBase64ToUint8Array(VAPID_PUBLIC_KEY)
                    });
                }
                
                // Send subscription to server
                const response = await fetch('/api/client/push/subscribe', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(subscription.toJSON())
                });
                
                const result = await response.json();
                
                if (result.success) {
                    console.log('Successfully subscribed to push notifications');
                    localStorage.setItem('push-subscribed', 'true');
                    return true;
                } else {
                    console.error('Failed to subscribe:', result.message);
                    return false;
                }
                
            } catch (error) {
                console.error('Error subscribing to push notifications:', error);
                return false;
            }
        }
        
        // Auto-subscribe when PWA is installed (only once)
        if (isPWA() && !localStorage.getItem('push-subscribed')) {
            // Delay subscription to not interrupt initial load
            setTimeout(() => {
                subscribeToPushNotifications();
            }, 2000);
        }
        
        // Expose function globally for manual subscription
        window.subscribeToPushNotifications = subscribeToPushNotifications;
        window.isPWA = isPWA;
        
        // Add body class if PWA mode
        if (isPWA()) {
            document.body.classList.add('pwa-mode');
        } else {
            document.body.classList.add('browser-mode');
        }
    </script>
    
    <script>
        // Universal lazy loading for images
        if ('IntersectionObserver' in window) {
            const imageObserver = new IntersectionObserver((entries, observer) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const img = entry.target;
                        if (img.dataset.src) {
                            img.src = img.dataset.src;
                            img.removeAttribute('data-src');
                        }
                        if (img.dataset.srcset) {
                            img.srcset = img.dataset.srcset;
                            img.removeAttribute('data-srcset');
                        }
                        img.classList.add('loaded');
                        observer.unobserve(img);
                    }
                });
            }, {
                rootMargin: '50px 0px',
                threshold: 0.01
            });
            
            // Observe all lazy images
            document.querySelectorAll('img[loading="lazy"]').forEach(img => {
                imageObserver.observe(img);
            });
        }
    </script>
    
    <style>
        /* iOS Safe Area Support */
        .safe-area-bottom {
            padding-bottom: env(safe-area-inset-bottom);
        }
        
        /* Adjust main content padding for bottom nav on mobile */
        @media (max-width: 1023px) {
            main {
                padding-bottom: calc(4rem + env(safe-area-inset-bottom)) !important;
            }
        }
        
        /* Smooth transitions */
        nav a {
            transition: all 0.2s ease;
        }
        
        /* Active state feedback */
        nav a:active {
            transform: scale(0.95);
        }
    </style>
</body>
</html>
