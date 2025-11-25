<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#0a66c2">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <title>@yield('title', 'Client Portal') - Bizmark.id</title>
    
    <!-- Favicons -->
    <link rel="icon" type="image/png" href="{{ asset('images/pavicon.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('images/pavicon.png') }}">
    
    <!-- External CSS - CDN Only -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <style>
        /* LinkedIn-style font stack */
        * {
            font-family: -apple-system, system-ui, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', 'Fira Sans', Ubuntu, Oxygen, 'Oxygen Sans', Cantarell, 'Droid Sans', Arial, sans-serif;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }
        
        /* Remove default underline from all links */
        a {
            text-decoration: none;
        }
        
        /* Only show underline on hover for links with hover:underline class */
        a.hover\:underline:hover {
            text-decoration: underline;
        }
        
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
            background: #0a66c2;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            height: 56px;
            display: flex;
            align-items: center;
            padding-left: calc(1rem + env(safe-area-inset-left));
            padding-right: calc(1rem + env(safe-area-inset-right));
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
        }
        
        html.pwa-mode body,
        html.mobile-ui body {
            padding-top: 56px !important;
            padding-bottom: 56px !important;
        }
        
        @media (display-mode: standalone) {
            .pwa-header {
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                z-index: 50;
                background: #0a66c2;
                border-bottom: 1px solid rgba(255, 255, 255, 0.1);
                height: 56px;
                display: flex;
                align-items: center;
                padding-left: calc(1rem + env(safe-area-inset-left));
                padding-right: calc(1rem + env(safe-area-inset-right));
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
            }
            
            body {
                padding-top: 56px !important;
                padding-bottom: 56px !important;
            }
        }
        
        html.pwa-mode .bottom-nav-text,
        html.mobile-ui .bottom-nav-text {
            display: none;
        }
        
        html.pwa-mode .pwa-header *,
        html.mobile-ui .pwa-header * {
            transition: all 0.2s ease;
            color: white;
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
            color: white;
        }
        
        html.pwa-mode .pwa-header button:hover,
        html.mobile-ui .pwa-header button:hover,
        html.pwa-mode .pwa-header a:hover,
        html.mobile-ui .pwa-header a:hover {
            background-color: rgba(255, 255, 255, 0.1);
            border-radius: 0.5rem;
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
                padding-bottom: 56px;
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
    </script>
    
    <!-- PWA Update Handler -->
    <script src="/js/pwa-update-handler.js"></script>
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
                'badge_color' => 'bg-[#0a66c2]'
            ],
            [
                'label' => 'Proyek Aktif',
                'icon' => 'fa-briefcase',
                'route' => route('client.projects.index'),
                'active' => request()->routeIs('client.projects.*'),
                'badge' => $activeProjects,
                'badge_color' => 'bg-green-600'
            ],
            [
                'label' => 'Dokumen',
                'icon' => 'fa-folder',
                'route' => route('client.documents.index'),
                'active' => request()->routeIs('client.documents.*'),
                'badge' => $pendingDocuments,
                'badge_color' => 'bg-amber-600'
            ],
            [
                'label' => 'Profil & Akun',
                'icon' => 'fa-id-card',
                'route' => route('client.profile.edit'),
                'active' => request()->routeIs('client.profile.*'),
            ],
        ];
    @endphp
    <div x-data="{ sidebarOpen: false, searchOpen: false, searchQuery: '', profileOpen: false }" class="flex min-h-screen overflow-hidden">
        
        <!-- Profile Dropdown Menu (LinkedIn-style - Full Height Slide from Left) - PORTAL LEVEL -->
        <div 
            x-show="profileOpen"
            @click.self="profileOpen = false"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 bg-black/50 z-[100]"
            style="z-index: 9999;"
            x-cloak
        >
            <div 
                x-show="profileOpen"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="-translate-x-full"
                x-transition:enter-end="translate-x-0"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="translate-x-0"
                x-transition:leave-end="-translate-x-full"
                class="absolute top-0 left-0 bottom-0 w-[70%] max-w-xs bg-white shadow-2xl overflow-y-auto flex flex-col"
                style="padding-bottom: env(safe-area-inset-bottom); z-index: 10000;"
            >
                <!-- Close Button -->
                <button 
                    @click="profileOpen = false"
                    class="absolute top-4 right-4 w-8 h-8 flex items-center justify-center rounded-full bg-gray-100 hover:bg-gray-200 text-gray-600 transition-colors z-10"
                >
                    <i class="fas fa-times"></i>
                </button>
                
                <!-- User Info Header -->
                <div class="p-6 bg-[#0a66c2] text-white flex-shrink-0">
                    <div class="flex items-start gap-4">
                        @if($client->profile_picture && Storage::disk('public')->exists($client->profile_picture))
                        <img src="{{ asset('storage/' . $client->profile_picture) }}" 
                             alt="{{ $client->name }}" 
                             class="w-16 h-16 rounded-full object-cover border-2 border-white/20 flex-shrink-0">
                        @else
                        <div class="w-16 h-16 rounded-full bg-white/20 text-white flex items-center justify-center text-2xl border-2 border-white/20 flex-shrink-0">
                            <i class="fas {{ $client->client_type === 'company' ? 'fa-building' : 'fa-user' }}"></i>
                        </div>
                        @endif
                        <div class="flex-1 min-w-0 pt-1">
                            <p class="font-semibold text-base text-white truncate">{{ $client->name }}</p>
                            <p class="text-sm text-white/70 truncate">{{ $client->email }}</p>
                            <p class="text-xs text-white/60 mt-1">
                                <i class="fas {{ $client->client_type === 'company' ? 'fa-building' : 'fa-user' }} mr-1"></i>
                                {{ $client->client_type === 'company' ? 'Perusahaan' : 'Perorangan' }}
                            </p>
                        </div>
                    </div>
                </div>
                
                <!-- Menu Items - Scrollable -->
                <div class="flex-1 overflow-y-auto py-2">
                    <!-- Profile -->
                    <a href="{{ route('client.profile.edit') }}" 
                       @click="profileOpen = false"
                       class="flex items-center gap-3 px-6 py-3 hover:bg-gray-50 active:bg-gray-100 transition-colors">
                        <i class="fas fa-id-card text-gray-600 w-5 text-center"></i>
                        <span class="text-sm font-medium text-gray-900 flex-1">Profil Saya</span>
                        <i class="fas fa-chevron-right text-gray-400 text-xs"></i>
                    </a>
                    
                    <!-- Payments -->
                    <a href="{{ route('client.applications.index', ['status' => 'payment_pending']) }}" 
                       @click="profileOpen = false"
                       class="flex items-center gap-3 px-6 py-3 hover:bg-gray-50 active:bg-gray-100 transition-colors">
                        <i class="fas fa-wallet text-gray-600 w-5 text-center"></i>
                        <span class="text-sm font-medium text-gray-900 flex-1">Pembayaran</span>
                        @php
                            $pendingPayments = \App\Models\PermitApplication::where('client_id', $client->id)
                                ->where('status', 'payment_pending')
                                ->count();
                        @endphp
                        @if($pendingPayments > 0)
                        <span class="text-xs font-semibold text-white px-2 py-0.5 rounded-full bg-red-600 min-w-[20px] text-center">
                            {{ $pendingPayments }}
                        </span>
                        @else
                        <i class="fas fa-chevron-right text-gray-400 text-xs"></i>
                        @endif
                    </a>
                    
                    <!-- Quotations -->
                    <a href="{{ route('client.applications.index', ['status' => 'quoted']) }}" 
                       @click="profileOpen = false"
                       class="flex items-center gap-3 px-6 py-3 hover:bg-gray-50 active:bg-gray-100 transition-colors">
                        <i class="fas fa-file-invoice-dollar text-gray-600 w-5 text-center"></i>
                        <span class="text-sm font-medium text-gray-900 flex-1">Penawaran</span>
                        @php
                            $pendingQuotations = \App\Models\PermitApplication::where('client_id', $client->id)
                                ->where('status', 'quoted')
                                ->count();
                        @endphp
                        @if($pendingQuotations > 0)
                        <span class="text-xs font-semibold text-white px-2 py-0.5 rounded-full bg-red-600 min-w-[20px] text-center">
                            {{ $pendingQuotations }}
                        </span>
                        @else
                        <i class="fas fa-chevron-right text-gray-400 text-xs"></i>
                        @endif
                    </a>
                    
                    <!-- Notifications -->
                    <a href="{{ route('client.notifications.index') }}" 
                       @click="profileOpen = false"
                       class="flex items-center gap-3 px-6 py-3 hover:bg-gray-50 active:bg-gray-100 transition-colors">
                        <i class="fas fa-bell text-gray-600 w-5 text-center"></i>
                        <span class="text-sm font-medium text-gray-900 flex-1">Notifikasi</span>
                        @if($notificationCount > 0)
                        <span class="text-xs font-semibold text-white px-2 py-0.5 rounded-full bg-red-600 min-w-[20px] text-center">
                            {{ $notificationCount > 9 ? '9+' : $notificationCount }}
                        </span>
                        @else
                        <i class="fas fa-chevron-right text-gray-400 text-xs"></i>
                        @endif
                    </a>
                    
                    <!-- Divider -->
                    <div class="my-2 border-t border-gray-200"></div>
                    
                    <!-- Support -->
                    <a href="https://wa.me/6283879602855" 
                       target="_blank"
                       @click="profileOpen = false"
                       class="flex items-center gap-3 px-6 py-3 hover:bg-gray-50 active:bg-gray-100 transition-colors">
                        <i class="fab fa-whatsapp text-gray-600 w-5 text-center"></i>
                        <span class="text-sm font-medium text-gray-900 flex-1">Bantuan</span>
                        <i class="fas fa-external-link-alt text-gray-400 text-xs"></i>
                    </a>
                </div>
                
                <!-- Footer - Logout (Sticky) -->
                <div class="flex-shrink-0 border-t border-gray-200 bg-white">
                    <form method="POST" action="{{ route('client.logout') }}">
                        @csrf
                        <button type="submit" 
                                class="w-full flex items-center gap-3 px-6 py-3 hover:bg-gray-50 active:bg-gray-100 transition-colors">
                            <i class="fas fa-sign-out-alt text-gray-600 w-5 text-center"></i>
                            <span class="text-sm font-medium text-gray-900 flex-1 text-left">Logout</span>
                            <i class="fas fa-chevron-right text-gray-400 text-xs"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- PWA-Only Minimal Header (LinkedIn-style - Profile left, Search center, Notif right) -->
        <header id="pwa-header" class="pwa-only pwa-header transition-transform duration-300">
            <div class="flex items-center justify-between w-full gap-2">
                <!-- Left: Profile Photo with Dropdown -->
                <div class="relative flex-shrink-0">
                    <button 
                        @click="profileOpen = !profileOpen"
                        class="focus:outline-none"
                        aria-label="Menu Profil"
                        aria-expanded="false"
                        :aria-expanded="profileOpen"
                    >
                        @if($client->profile_picture && Storage::disk('public')->exists($client->profile_picture))
                        <img src="{{ asset('storage/' . $client->profile_picture) }}" 
                             alt="{{ $client->name }}" 
                             class="w-8 h-8 rounded-full object-cover border-2 transition-all"
                             :class="profileOpen ? 'border-white' : 'border-white/70'">
                        @else
                        <div class="w-8 h-8 rounded-full flex items-center justify-center border-2 transition-all"
                             :class="profileOpen ? 'border-white bg-white/90 text-[#0a66c2]' : 'border-white/70 bg-white/20 text-white'">
                            <i class="fas {{ $client->client_type === 'company' ? 'fa-building' : 'fa-user' }} text-sm"></i>
                        </div>
                        @endif
                    </button>
                </div>
                
                <!-- Center: Search Bar -->
                <button 
                    @click="searchOpen = true"
                    class="flex-1 flex items-center gap-2 bg-white/20 hover:bg-white/30 rounded-lg px-3 py-1.5 text-left transition-colors backdrop-blur-sm"
                >
                    <i class="fas fa-search text-white/80 text-sm"></i>
                    <span class="text-sm text-white/90 truncate">Cari...</span>
                </button>
                
                <!-- Right: Notifications (Quick Access) -->
                <a 
                    href="{{ route('client.notifications.index') }}"
                    class="relative text-white hover:text-white/80 transition-colors p-1.5 rounded-lg hover:bg-white/20 flex-shrink-0"
                    aria-label="Notifikasi"
                >
                    <i class="fas fa-bell text-lg"></i>
                    @if($notificationCount > 0)
                    <span class="absolute top-0 right-0 w-4 h-4 bg-red-500 text-white text-[8px] rounded-full flex items-center justify-center font-bold shadow-lg">
                        {{ $notificationCount > 9 ? '9' : $notificationCount }}
                    </span>
                    @endif
                </a>
            </div>
        </header>
        
        <!-- Search Overlay (Full Screen) -->
        <div 
            x-show="searchOpen" 
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 bg-black/50 z-[60] lg:hidden"
            @click="searchOpen = false"
            x-cloak
        ></div>
        
        <div 
            x-show="searchOpen"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 translate-y-4"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 translate-y-4"
            class="fixed inset-x-0 top-0 bg-white z-[70] lg:hidden shadow-lg"
            @click.away="searchOpen = false"
            x-cloak
        >
            <!-- Search Header -->
            <div class="flex items-center gap-3 p-4 border-b border-gray-200">
                <button @click="searchOpen = false" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-arrow-left text-lg"></i>
                </button>
                <div class="flex-1 relative">
                    <input 
                        type="search"
                        x-model="searchQuery"
                        placeholder="Cari proyek, dokumen, izin..."
                        class="w-full bg-gray-100 rounded-lg px-4 py-2 pr-10 text-sm focus:outline-none focus:ring-2 focus:ring-[#0a66c2]"
                        autofocus
                        @keyup.escape="searchOpen = false"
                    >
                    <button 
                        x-show="searchQuery.length > 0"
                        @click="searchQuery = ''"
                        class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600"
                    >
                        <i class="fas fa-times text-sm"></i>
                    </button>
                </div>
            </div>
            
            <!-- Search Results -->
            <div class="overflow-y-auto" style="max-height: calc(100vh - 120px);">
                <!-- Recent Searches -->
                <div x-show="searchQuery.length === 0" class="p-4">
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">Pencarian Terkini</p>
                    <div class="space-y-2">
                        <a href="#" class="flex items-center gap-3 p-2 rounded-lg hover:bg-gray-50">
                            <i class="fas fa-clock-rotate-left text-gray-400 text-sm"></i>
                            <span class="text-sm text-gray-700">Proyek Konstruksi</span>
                        </a>
                        <a href="#" class="flex items-center gap-3 p-2 rounded-lg hover:bg-gray-50">
                            <i class="fas fa-clock-rotate-left text-gray-400 text-sm"></i>
                            <span class="text-sm text-gray-700">IMB Jakarta</span>
                        </a>
                    </div>
                </div>
                
                <!-- Search Results (when typing) -->
                <div x-show="searchQuery.length > 0" class="divide-y divide-gray-100">
                    <!-- Projects -->
                    <div class="p-4">
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Proyek</p>
                        <a href="#" class="flex items-start gap-3 p-2 rounded-lg hover:bg-gray-50">
                            <div class="w-10 h-10 rounded-lg bg-[#0a66c2]/10 text-[#0a66c2] flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-diagram-project"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900 truncate">Proyek Konstruksi Gedung</p>
                                <p class="text-xs text-gray-500">Dalam Proses • 5 dokumen</p>
                            </div>
                        </a>
                    </div>
                    
                    <!-- Documents -->
                    <div class="p-4">
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Dokumen</p>
                        <a href="#" class="flex items-start gap-3 p-2 rounded-lg hover:bg-gray-50">
                            <div class="w-10 h-10 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-file-pdf"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900 truncate">KTP Direktur.pdf</p>
                                <p class="text-xs text-gray-500">Diupload 2 hari lalu</p>
                            </div>
                        </a>
                    </div>
                    
                    <!-- No Results -->
                    <div x-show="false" class="p-8 text-center">
                        <i class="fas fa-search text-gray-300 text-4xl mb-3"></i>
                        <p class="text-sm text-gray-500">Tidak ada hasil untuk "<span x-text="searchQuery"></span>"</p>
                    </div>
                </div>
            </div>
        </div>
        
        <div 
            class="fixed inset-0 bg-black/40 z-40 lg:hidden"
            x-show="sidebarOpen"
            x-transition.opacity
            @click="sidebarOpen = false"
            x-cloak
        >
        </div>
        
        <!-- Sidebar (Browser Mode) - Hidden in PWA standalone -->
        <aside 
            :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
            class="browser-only fixed inset-y-0 left-0 z-50 w-64 bg-white border-r border-gray-200 shadow-lg transform transition-transform duration-300 ease-in-out lg:translate-x-0 lg:shadow-none"
        >
            <div class="flex items-center justify-between h-16 px-6 border-b border-gray-100">
                <!-- Logo BizMark Official -->
                <a href="{{ route('client.dashboard') }}" class="flex items-center gap-2 group">
                    <img src="{{ asset('images/logo-bizmark.svg') }}" 
                         alt="BizMark Indonesia" 
                         class="h-10 w-10 transition-transform group-hover:scale-105">
                    <div class="flex flex-col">
                        <span class="text-lg font-bold text-gray-800 leading-tight">BizMark</span>
                        <span class="text-[9px] text-gray-500 tracking-wider leading-tight">INDONESIA</span>
                    </div>
                </a>
                <button @click="sidebarOpen = false" class="lg:hidden text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <div class="px-6 py-5 border-b border-gray-100">
                <div class="flex items-center gap-3">
                    @if($client->profile_picture && Storage::disk('public')->exists($client->profile_picture))
                    <img src="{{ asset('storage/' . $client->profile_picture) }}" 
                         alt="{{ $client->name }}" 
                         class="w-12 h-12 rounded-2xl object-cover border-2 border-[#0a66c2]/20">
                    @else
                    <div class="w-12 h-12 rounded-2xl bg-[#0a66c2]/10 text-[#0a66c2] flex items-center justify-center text-lg">
                        <i class="fas {{ $client->client_type === 'company' ? 'fa-building' : 'fa-user' }}"></i>
                    </div>
                    @endif
                    <div class="min-w-0">
                        <p class="font-semibold text-gray-900 truncate">{{ $client->name }}</p>
                        <p class="text-sm text-gray-500 truncate flex items-center gap-1">
                            <i class="fas {{ $client->client_type === 'company' ? 'fa-building' : 'fa-user' }} text-xs"></i>
                            {{ $client->client_type === 'company' ? 'Perusahaan' : 'Perorangan' }}
                        </p>
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
                        class="flex items-center px-4 py-3 rounded-lg text-sm font-medium transition {{ $item['active'] ? 'bg-[#0a66c2]/10 text-[#0a66c2] border border-[#0a66c2]/20' : 'text-gray-600 hover:bg-gray-50' }}"
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
                    <a href="mailto:cs@bizmark.id" class="inline-flex items-center gap-2 text-[#0a66c2] font-semibold hover:text-[#004182]">
                        <i class="fas fa-headset"></i> cs@bizmark.id
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
                    <button @click="sidebarOpen = !sidebarOpen" class="lg:hidden text-gray-600 hover:text-[#0a66c2] transition-colors">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                    
                    <div class="flex-1 flex items-center justify-between gap-4 lg:gap-0">
                        <div class="space-y-0.5">
                            <h2 class="text-xl sm:text-2xl font-bold text-gray-800 leading-tight">@yield('page-title', 'Portal Klien')</h2>
                            <p class="text-sm text-gray-500 hidden sm:block">@yield('page-subtitle', 'Selamat datang kembali, ' . $client->name . '!')</p>
                            <div class="hidden md:flex items-center gap-2">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs bg-[#0a66c2]/10 text-[#0a66c2]">
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
                                    class="relative text-gray-600 hover:text-[#0a66c2] focus:outline-none"
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
                                @if($client->profile_picture && Storage::disk('public')->exists($client->profile_picture))
                                <img src="{{ asset('storage/' . $client->profile_picture) }}" 
                                     alt="{{ $client->name }}" 
                                     class="w-8 h-8 rounded-full object-cover">
                                @else
                                <div class="w-8 h-8 rounded-full bg-[#0a66c2]/10 text-[#0a66c2] flex items-center justify-center">
                                    <i class="fas {{ $client->client_type === 'company' ? 'fa-building' : 'fa-user' }}"></i>
                                </div>
                                @endif
                                <div class="hidden sm:block text-xs leading-tight">
                                    <p class="font-semibold text-gray-700">{{ \Illuminate\Support\Str::limit($client->company_name ?? $client->name, 18) }}</p>
                                    <p class="text-gray-400">
                                        <i class="fas {{ $client->client_type === 'company' ? 'fa-building' : 'fa-user' }} text-[10px] mr-1"></i>
                                        {{ $client->client_type === 'company' ? 'Perusahaan' : 'Perorangan' }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Content - LinkedIn Style Full Width -->
            <main class="flex-1 overflow-y-auto bg-gray-50 dark:bg-gray-900">
                
                @if (session('success'))
                    <div class="mx-4 sm:mx-6 mt-4 p-4 bg-green-100 dark:bg-green-900/30 border-l-4 border-green-500 text-green-700 dark:text-green-400 rounded animate-fade-in">
                        <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
                    </div>
                @endif

                @if (session('error'))
                    <div class="mx-4 sm:mx-6 mt-4 p-4 bg-red-100 dark:bg-red-900/30 border-l-4 border-red-500 text-red-700 dark:text-red-400 rounded animate-fade-in">
                        <i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="mx-4 sm:mx-6 mt-4 p-4 bg-red-100 dark:bg-red-900/30 border-l-4 border-red-500 text-red-700 dark:text-red-400 rounded animate-fade-in">
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
            
            <!-- Mobile Bottom Navigation (LinkedIn/Instagram-style - 5 items with center action) -->
            <nav id="bottom-nav" class="lg:hidden fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 z-50 safe-area-bottom transition-transform duration-300" aria-label="Navigasi utama">
                <div class="grid grid-cols-5 h-14">
                    <!-- Home -->
                    <a href="{{ route('client.dashboard') }}" 
                       class="flex flex-col items-center justify-center gap-0.5 {{ request()->routeIs('client.dashboard') ? 'text-[#0a66c2]' : 'text-gray-600' }} hover:text-[#0a66c2] transition-colors">
                        <i class="fas fa-house text-xl"></i>
                        <span class="text-[9px] font-medium">Home</span>
                    </a>
                    
                    <!-- Layanan/Katalog -->
                    <a href="{{ route('client.services.index') }}" 
                       class="flex flex-col items-center justify-center gap-0.5 {{ request()->routeIs('client.services.*') ? 'text-[#0a66c2]' : 'text-gray-600' }} hover:text-[#0a66c2] transition-colors">
                        <i class="fas fa-layer-group text-xl"></i>
                        <span class="text-[9px] font-medium">Layanan</span>
                    </a>
                    
                    <!-- Ajukan (Center - Elevated FAB style) -->
                    <div class="flex items-center justify-center">
                        <a href="{{ route('client.applications.create') }}" 
                           class="flex items-center justify-center w-12 h-12 -mt-5 rounded-full bg-[#0a66c2] hover:bg-[#004182] text-white shadow-lg hover:shadow-xl transition-all active:scale-95">
                            <i class="fas fa-plus text-xl"></i>
                        </a>
                    </div>
                    
                    <!-- Proyek -->
                    <a href="{{ route('client.projects.index') }}" 
                       class="flex flex-col items-center justify-center gap-0.5 relative {{ request()->routeIs('client.projects.*') ? 'text-[#0a66c2]' : 'text-gray-600' }} hover:text-[#0a66c2] transition-colors">
                        <i class="fas fa-briefcase text-xl"></i>
                        <span class="text-[9px] font-medium">Proyek</span>
                        @if($activeProjects > 0)
                        <span class="absolute top-0.5 right-[28%] w-4 h-4 bg-green-500 text-white text-[9px] rounded-full flex items-center justify-center font-bold">
                            {{ $activeProjects > 9 ? '9' : $activeProjects }}
                        </span>
                        @endif
                    </a>
                    
                    <!-- Dokumen -->
                    <a href="{{ route('client.documents.index') }}" 
                       class="flex flex-col items-center justify-center gap-0.5 relative {{ request()->routeIs('client.documents.*') ? 'text-[#0a66c2]' : 'text-gray-600' }} hover:text-[#0a66c2] transition-colors">
                        <i class="fas fa-folder text-xl"></i>
                        <span class="text-[9px] font-medium">Dokumen</span>
                        @if($pendingDocuments > 0)
                        <span class="absolute top-0.5 right-[28%] w-4 h-4 bg-amber-500 text-white text-[9px] rounded-full flex items-center justify-center font-bold">
                            {{ $pendingDocuments > 9 ? '9' : $pendingDocuments }}
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
    
    <!-- PWA Install Prompt Handler -->
    <script>
        (function() {
            let deferredPrompt;
            let installPromptShown = localStorage.getItem('pwa-install-prompt-shown');
            let installDismissed = localStorage.getItem('pwa-install-dismissed');
            
            console.log('[PWA Install] Initializing...');
            console.log('[PWA Install] Is PWA?', isPWA());
            console.log('[PWA Install] Prompt shown before?', installPromptShown);
            console.log('[PWA Install] Dismissed?', installDismissed);
            
            // Capture the beforeinstallprompt event
            window.addEventListener('beforeinstallprompt', (e) => {
                console.log('[PWA Install] beforeinstallprompt event fired');
                
                // Prevent the mini-infobar from appearing on mobile
                e.preventDefault();
                
                // Stash the event so it can be triggered later
                deferredPrompt = e;
                
                // Check if app is not installed and prompt hasn't been dismissed
                if (!isPWA() && !installDismissed) {
                    // Show install prompt after a short delay (better UX)
                    setTimeout(() => {
                        showInstallPrompt();
                    }, 3000); // 3 seconds delay
                }
            });
            
            // Show custom install prompt
            function showInstallPrompt() {
                if (!deferredPrompt) {
                    console.log('[PWA Install] No deferred prompt available');
                    return;
                }
                
                console.log('[PWA Install] Showing install prompt');
                
                // Create custom install banner
                const banner = document.createElement('div');
                banner.id = 'pwa-install-banner';
                banner.className = 'fixed bottom-20 left-4 right-4 bg-[#0a66c2] text-white rounded-lg shadow-2xl p-4 z-50 animate-slide-up';
                banner.style.maxWidth = '500px';
                banner.style.margin = '0 auto';
                
                banner.innerHTML = `
                    <div class="flex items-start gap-3">
                        <div class="flex-shrink-0 w-12 h-12 bg-white rounded-lg flex items-center justify-center">
                            <i class="fas fa-download text-[#0a66c2] text-xl"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h4 class="font-bold text-white mb-1">Install Aplikasi Bizmark</h4>
                            <p class="text-sm text-blue-100 mb-3">
                                Akses lebih cepat dan notifikasi real-time langsung di perangkat Anda!
                            </p>
                            <div class="flex gap-2">
                                <button id="pwa-install-btn" class="px-4 py-2 bg-white text-[#0a66c2] font-semibold rounded-lg hover:bg-gray-50 transition text-sm">
                                    <i class="fas fa-download mr-1"></i>Install Sekarang
                                </button>
                                <button id="pwa-dismiss-btn" class="px-4 py-2 bg-[#004182] text-white font-medium rounded-lg hover:bg-[#003366] transition text-sm">
                                    Nanti
                                </button>
                            </div>
                        </div>
                        <button id="pwa-close-btn" class="flex-shrink-0 text-white hover:text-blue-100 transition">
                            <i class="fas fa-times text-lg"></i>
                        </button>
                    </div>
                `;
                
                document.body.appendChild(banner);
                
                // Add animation styles if not exist
                if (!document.getElementById('pwa-install-styles')) {
                    const style = document.createElement('style');
                    style.id = 'pwa-install-styles';
                    style.textContent = `
                        @keyframes slide-up {
                            from {
                                transform: translateY(100px);
                                opacity: 0;
                            }
                            to {
                                transform: translateY(0);
                                opacity: 1;
                            }
                        }
                        .animate-slide-up {
                            animation: slide-up 0.3s ease-out;
                        }
                    `;
                    document.head.appendChild(style);
                }
                
                // Handle install button click
                document.getElementById('pwa-install-btn').addEventListener('click', async () => {
                    console.log('[PWA Install] User clicked install');
                    
                    if (!deferredPrompt) {
                        console.log('[PWA Install] No deferred prompt available');
                        return;
                    }
                    
                    // Show the install prompt
                    deferredPrompt.prompt();
                    
                    // Wait for the user to respond to the prompt
                    const { outcome } = await deferredPrompt.userChoice;
                    
                    console.log('[PWA Install] User choice:', outcome);
                    
                    if (outcome === 'accepted') {
                        console.log('[PWA Install] User accepted the install prompt');
                        localStorage.setItem('pwa-installed', 'true');
                    } else {
                        console.log('[PWA Install] User dismissed the install prompt');
                        localStorage.setItem('pwa-install-dismissed', 'true');
                    }
                    
                    // Mark that prompt was shown
                    localStorage.setItem('pwa-install-prompt-shown', 'true');
                    
                    // Clear the deferred prompt
                    deferredPrompt = null;
                    
                    // Remove banner
                    banner.remove();
                });
                
                // Handle dismiss button
                document.getElementById('pwa-dismiss-btn').addEventListener('click', () => {
                    console.log('[PWA Install] User clicked dismiss (later)');
                    localStorage.setItem('pwa-install-prompt-shown', 'true');
                    banner.remove();
                    
                    // Show again after 1 day
                    setTimeout(() => {
                        localStorage.removeItem('pwa-install-prompt-shown');
                    }, 24 * 60 * 60 * 1000);
                });
                
                // Handle close button
                document.getElementById('pwa-close-btn').addEventListener('click', () => {
                    console.log('[PWA Install] User closed banner');
                    localStorage.setItem('pwa-install-dismissed', 'true');
                    localStorage.setItem('pwa-install-prompt-shown', 'true');
                    banner.remove();
                });
            }
            
            // Detect when app is installed
            window.addEventListener('appinstalled', (e) => {
                console.log('[PWA Install] App was installed successfully');
                localStorage.setItem('pwa-installed', 'true');
                localStorage.removeItem('pwa-install-dismissed');
                
                // Remove banner if still visible
                const banner = document.getElementById('pwa-install-banner');
                if (banner) {
                    banner.remove();
                }
                
                // Show success message
                if (typeof window.showToast === 'function') {
                    window.showToast('✅ Aplikasi berhasil diinstall!', 'success');
                }
            });
            
            // Manual trigger function (can be called from UI button)
            window.triggerPWAInstall = function() {
                console.log('[PWA Install] Manual trigger called');
                
                if (isPWA()) {
                    alert('Aplikasi sudah terinstall!');
                    return;
                }
                
                if (deferredPrompt) {
                    showInstallPrompt();
                } else {
                    console.log('[PWA Install] No install prompt available');
                    alert('Install prompt tidak tersedia. Pastikan Anda menggunakan browser yang mendukung PWA (Chrome/Edge) dan belum menginstall aplikasi.');
                }
            };
            
            // Expose install status checker
            window.checkPWAInstallStatus = function() {
                const isInstalled = isPWA();
                const canInstall = !!deferredPrompt;
                
                console.log('[PWA Install] Status check:');
                console.log('- Is installed:', isInstalled);
                console.log('- Can install:', canInstall);
                console.log('- Has deferred prompt:', !!deferredPrompt);
                
                return {
                    isInstalled,
                    canInstall,
                    hasPrompt: !!deferredPrompt
                };
            };
            
            // Debug: Force show prompt (untuk testing)
            window.forceShowPWAInstall = function() {
                console.log('[PWA Install] Force showing install prompt');
                localStorage.removeItem('pwa-install-prompt-shown');
                localStorage.removeItem('pwa-install-dismissed');
                
                if (deferredPrompt) {
                    showInstallPrompt();
                } else {
                    console.log('[PWA Install] No deferred prompt available yet. Reload page and try again.');
                }
            };
            
        })();
    </script>
    
    <script>
        // Auto-hide header and bottom nav on scroll (LinkedIn-style) - INSTANT RESPONSE
        (function() {
            if (window.innerWidth >= 1024) return; // Only for mobile
            
            const header = document.getElementById('pwa-header');
            const bottomNav = document.getElementById('bottom-nav');
            
            if (!header || !bottomNav) return;
            
            let lastScrollTop = 0;
            let ticking = false;
            
            function updateNavigation(currentScroll) {
                if (currentScroll > lastScrollTop && currentScroll > 50) {
                    // Scrolling DOWN - hide header and bottom nav
                    header.style.transform = 'translateY(-100%)';
                    bottomNav.style.transform = 'translateY(100%)';
                } else if (currentScroll < lastScrollTop) {
                    // Scrolling UP - show header and bottom nav
                    header.style.transform = 'translateY(0)';
                    bottomNav.style.transform = 'translateY(0)';
                }
                
                lastScrollTop = currentScroll <= 0 ? 0 : currentScroll;
                ticking = false;
            }
            
            window.addEventListener('scroll', function() {
                const currentScroll = window.pageYOffset || document.documentElement.scrollTop;
                
                if (!ticking) {
                    window.requestAnimationFrame(function() {
                        updateNavigation(currentScroll);
                    });
                    ticking = true;
                }
            }, { passive: true });
            
            // Always show on page load
            header.style.transform = 'translateY(0)';
            bottomNav.style.transform = 'translateY(0)';
        })();
        
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
                padding-bottom: calc(3.5rem + env(safe-area-inset-bottom)) !important;
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
