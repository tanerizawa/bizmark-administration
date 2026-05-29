<!DOCTYPE html>
<html lang="id" x-data x-bind:data-theme="$store.theme ? $store.theme.current : null">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#0a66c2">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="description" content="Portal Klien Bizmark.ID — Kelola permohonan izin usaha, dokumen, dan status proyek Anda.">
    <title>@yield('title', 'Client Portal') - Bizmark.id</title>
    
    <!-- Favicons -->
    <link rel="icon" type="image/png" href="{{ asset('images/pavicon.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('images/pavicon.png') }}">
    
    <!-- Client Portal Assets (Vite — no CDN) -->
    @vite(['resources/css/client.css', 'resources/js/client.js'])

    <!-- Preload critical fonts (FA solid) -->
    @php
        $manifest = json_decode(file_get_contents(public_path('build/manifest.json')), true);
        $faSolidFile = $manifest['node_modules/@fortawesome/fontawesome-free/webfonts/fa-solid-900.woff2']['file'] ?? null;
    @endphp
    @if($faSolidFile)
    <link rel="preload" href="{{ asset('build/' . $faSolidFile) }}" as="font" type="font/woff2" crossorigin="anonymous">
    @endif
    
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

        /* Focus-visible ring for keyboard navigation (WCAG 2.2) */
        :focus-visible {
            outline: 2px solid #0a66c2;
            outline-offset: 2px;
            border-radius: 2px;
        }

        /* Suppress outline for mouse/touch (only show for keyboard) */
        :focus:not(:focus-visible) {
            outline: none;
        }
        
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

        /* Portal v2: hide legacy title block in header — v2 hero provides full context */
        body.portal-v2 .portal-v2-hidden {
            display: none !important;
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
@php
    // Portal redesign v2 feature flag (config/portal_redesign.php)
    $portalV2Master  = (bool) config('portal_redesign.enabled', false);
    $portalV2Routes  = (array) config('portal_redesign.enabled_routes', []);
    $portalV2Allowed = $portalV2Master || in_array(optional(request()->route())->getName(), $portalV2Routes, true);
    $portalLegacy    = config('portal_redesign.allow_legacy_query', true) && request()->boolean('legacy');
    $portalV2        = $portalV2Allowed && ! $portalLegacy;
    $portalCmdk      = $portalV2 && (bool) config('portal_redesign.command_palette', true);
@endphp
<body class="min-h-screen {{ $portalV2 ? 'portal-v2' : '' }}" style="background: var(--surface-cool); color: var(--text-primary);">
    @if($portalV2)
        {{-- Hidden logout form referenced by command palette --}}
        <form id="client-logout-form" method="POST" action="{{ route('client.logout') }}" class="hidden">
            @csrf
        </form>
    @endif
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
                'group' => 'Utama',
            ],
            [
                'label' => 'Katalog Izin',
                'icon' => 'fa-layer-group',
                'route' => route('client.services.index'),
                'active' => request()->routeIs('client.services.*') && !request()->routeIs('client.applications.*'),
                'group' => 'Utama',
            ],
            [
                'label' => 'Permohonan Saya',
                'icon' => 'fa-file-signature',
                'route' => route('client.applications.index'),
                'active' => request()->routeIs('client.applications.*'),
                'badge' => $submittedCount + $draftCount,
                'badge_color' => 'bg-blue-600',
                'group' => 'Utama',
            ],
            [
                'label' => 'Proyek Aktif',
                'icon' => 'fa-briefcase',
                'route' => route('client.projects.index'),
                'active' => request()->routeIs('client.projects.*'),
                'badge' => $activeProjects,
                'badge_color' => 'bg-green-600',
                'group' => 'Utama',
            ],
            [
                'label' => 'Dokumen',
                'icon' => 'fa-folder',
                'route' => route('client.documents.index'),
                'active' => request()->routeIs('client.documents.*'),
                'badge' => $pendingDocuments,
                'badge_color' => 'bg-amber-600',
                'group' => 'Utama',
            ],
            [
                'label' => 'Vault Dokumen',
                'icon' => 'fa-vault',
                'route' => route('client.vault.index'),
                'active' => request()->routeIs('client.vault.*'),
                'group' => 'Alat Lanjutan',
            ],
            [
                'label' => 'OSS Tracker',
                'icon' => 'fa-circle-check',
                'route' => route('client.oss-tracker.index'),
                'active' => request()->routeIs('client.oss-tracker.*'),
                'group' => 'Alat Lanjutan',
            ],
            [
                'label' => 'API Keys',
                'icon' => 'fa-key',
                'route' => route('client.api-keys.index'),
                'active' => request()->routeIs('client.api-keys.*'),
                'group' => 'Alat Lanjutan',
            ],
            [
                'label' => 'Laporan Compliance',
                'icon' => 'fa-file-pdf',
                'route' => route('client.compliance-reports.index'),
                'active' => request()->routeIs('client.compliance-reports.*'),
                'group' => 'Alat Lanjutan',
            ],
            [
                'label' => 'Profil & Akun',
                'icon' => 'fa-id-card',
                'route' => route('client.profile.edit'),
                'active' => request()->routeIs('client.profile.*'),
                'group' => 'Utama',
            ],
        ];
        $navGroups = collect($navItems)->groupBy('group');
    @endphp
    <div x-data="{ sidebarOpen: false, profileOpen: false }" class="flex min-h-screen overflow-hidden">
        
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
                class="absolute top-0 left-0 bottom-0 w-[70%] max-w-xs shadow-2xl overflow-y-auto flex flex-col"
                style="background: var(--surface-elevated); padding-bottom: env(safe-area-inset-bottom); z-index: 10000;"
            >
                <!-- Close Button -->
                <button 
                    @click="profileOpen = false"
                    class="absolute top-4 right-4 w-8 h-8 flex items-center justify-center rounded-full transition-colors z-10"
                    style="background: var(--surface-sunken); color: var(--text-secondary);"
                >
                    <i class="fas fa-times"></i>
                </button>
                
                <!-- User Info Header -->
                <div class="p-6 flex-shrink-0" style="background: var(--client-primary); color: #fff;">
                    <div class="flex items-start gap-4">
                        @if($client->profile_picture && Storage::disk('public')->exists($client->profile_picture))
                        <img src="{{ asset('storage/' . $client->profile_picture) }}" 
                             alt="{{ $client->name }}" 
                             loading="lazy"
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
                       class="flex items-center gap-3 px-6 py-3 transition-colors"
                       style="color: var(--text-primary);"
                       onmouseover="this.style.background='var(--surface-sunken)'" onmouseout="this.style.background=''">
                        <i class="fas fa-id-card w-5 text-center flex-shrink-0" style="color: var(--text-secondary);"></i>
                        <span class="text-sm font-medium flex-1">Profil Saya</span>
                        <i class="fas fa-chevron-right text-xs" style="color: var(--text-tertiary);"></i>
                    </a>
                    
                    <!-- Payments -->
                    <a href="{{ route('client.applications.index', ['status' => 'payment_pending']) }}" 
                       @click="profileOpen = false"
                       class="flex items-center gap-3 px-6 py-3 transition-colors"
                       style="color: var(--text-primary);"
                       onmouseover="this.style.background='var(--surface-sunken)'" onmouseout="this.style.background=''">
                        <i class="fas fa-wallet w-5 text-center flex-shrink-0" style="color: var(--text-secondary);"></i>
                        <span class="text-sm font-medium flex-1">Pembayaran</span>
                        @php
                            $pendingPayments = \App\Models\PermitApplication::where('client_id', $client->id)
                                ->where('status', 'payment_pending')
                                ->count();
                        @endphp
                        @if($pendingPayments > 0)
                        <span class="text-xs font-semibold text-white px-2 py-0.5 rounded-full min-w-[20px] text-center" style="background: var(--apple-red);">
                            {{ $pendingPayments }}
                        </span>
                        @else
                        <i class="fas fa-chevron-right text-xs" style="color: var(--text-tertiary);"></i>
                        @endif
                    </a>
                    
                    <!-- Quotations -->
                    <a href="{{ route('client.applications.index', ['status' => 'quoted']) }}" 
                       @click="profileOpen = false"
                       class="flex items-center gap-3 px-6 py-3 transition-colors"
                       style="color: var(--text-primary);"
                       onmouseover="this.style.background='var(--surface-sunken)'" onmouseout="this.style.background=''">
                        <i class="fas fa-file-invoice-dollar w-5 text-center flex-shrink-0" style="color: var(--text-secondary);"></i>
                        <span class="text-sm font-medium flex-1">Penawaran</span>
                        @php
                            $pendingQuotations = \App\Models\PermitApplication::where('client_id', $client->id)
                                ->where('status', 'quoted')
                                ->count();
                        @endphp
                        @if($pendingQuotations > 0)
                        <span class="text-xs font-semibold text-white px-2 py-0.5 rounded-full min-w-[20px] text-center" style="background: var(--apple-red);">
                            {{ $pendingQuotations }}
                        </span>
                        @else
                        <i class="fas fa-chevron-right text-xs" style="color: var(--text-tertiary);"></i>
                        @endif
                    </a>
                    
                    <!-- Notifications -->
                    <a href="{{ route('client.notifications.index') }}" 
                       @click="profileOpen = false"
                       class="flex items-center gap-3 px-6 py-3 transition-colors"
                       style="color: var(--text-primary);"
                       onmouseover="this.style.background='var(--surface-sunken)'" onmouseout="this.style.background=''">
                        <i class="fas fa-bell w-5 text-center flex-shrink-0" style="color: var(--text-secondary);"></i>
                        <span class="text-sm font-medium flex-1">Notifikasi</span>
                        @if($notificationCount > 0)
                        <span class="text-xs font-semibold text-white px-2 py-0.5 rounded-full min-w-[20px] text-center" style="background: var(--apple-red);">
                            {{ $notificationCount > 9 ? '9+' : $notificationCount }}
                        </span>
                        @else
                        <i class="fas fa-chevron-right text-xs" style="color: var(--text-tertiary);"></i>
                        @endif
                    </a>
                    
                    <!-- Divider -->
                    <div class="my-2 border-t" style="border-color: var(--border-subtle);"></div>
                    
                    <!-- Support -->
                    <a href="{{ config('landing_metrics.contact.whatsapp_link') }}" 
                       target="_blank"
                       rel="noopener noreferrer"
                       @click="profileOpen = false"
                       class="flex items-center gap-3 px-6 py-3 transition-colors"
                       style="color: var(--text-primary);"
                       onmouseover="this.style.background='var(--surface-sunken)'" onmouseout="this.style.background=''">
                        <i class="fab fa-whatsapp w-5 text-center flex-shrink-0" style="color: var(--text-secondary);"></i>
                        <span class="text-sm font-medium flex-1">Bantuan</span>
                        <i class="fas fa-external-link-alt text-xs" style="color: var(--text-tertiary);"></i>
                    </a>
                </div>
                
                <!-- Footer - Logout (Sticky) -->
                <div class="flex-shrink-0 border-t" style="border-color: var(--border-subtle); background: var(--surface-elevated);">
                    <form method="POST" action="{{ route('client.logout') }}">
                        @csrf
                        <button type="submit" 
                                class="w-full flex items-center gap-3 px-6 py-3 transition-colors text-left"
                                style="color: var(--text-primary);"
                                onmouseover="this.style.background='color-mix(in oklab, var(--apple-red) 10%, transparent)'; this.style.color='var(--apple-red)'"
                                onmouseout="this.style.background=''; this.style.color='var(--text-primary)'">
                            <i class="fas fa-sign-out-alt w-5 text-center flex-shrink-0"></i>
                            <span class="text-sm font-medium flex-1">Logout</span>
                            <i class="fas fa-chevron-right text-xs" style="color: var(--text-tertiary);"></i>
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
                             loading="lazy"
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
                    @click="$dispatch('cmdk-open')"
                    class="flex-1 flex items-center gap-2 bg-white/20 hover:bg-white/30 rounded-lg px-3 py-1.5 text-left transition-colors backdrop-blur-sm"
                >
                    <i class="fas fa-search text-white/80 text-sm"></i>
                    <span class="text-sm text-white/90 truncate">Cari proyek, dokumen, KBLI...</span>
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
            class="browser-only fixed inset-y-0 left-0 z-50 w-64 border-r transform transition-transform duration-300 ease-in-out lg:translate-x-0 lg:shadow-none flex flex-col"
            style="background: var(--surface-elevated); border-color: var(--border-subtle); box-shadow: var(--shadow-md);"
        >
            {{-- Logo Header --}}
            <div class="flex items-center justify-between h-16 px-5 flex-shrink-0 border-b"
                 style="border-color: var(--border-subtle);">
                <a href="{{ route('client.dashboard') }}" class="flex items-center gap-2.5 group">
                    <img src="{{ asset('images/logo-bizmark.svg') }}"
                         alt="BizMark Indonesia"
                         class="h-9 w-9 transition-transform group-hover:scale-105">
                    <div class="flex flex-col leading-none">
                        <span class="text-base font-bold" style="color: var(--text-primary);">BizMark</span>
                        <span class="text-[9px] tracking-widest font-medium" style="color: var(--text-tertiary);">INDONESIA</span>
                    </div>
                </a>
                <button @click="sidebarOpen = false"
                        class="lg:hidden w-8 h-8 flex items-center justify-center rounded-lg transition-colors"
                        style="color: var(--text-tertiary);"
                        onmouseover="this.style.background='var(--surface-sunken)'" onmouseout="this.style.background=''">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            {{-- User Profile Card --}}
            <div class="px-4 py-4 flex-shrink-0 border-b" style="border-color: var(--border-subtle);">
                <div class="flex items-center gap-3">
                    @if($client->profile_picture && Storage::disk('public')->exists($client->profile_picture))
                    <img src="{{ asset('storage/' . $client->profile_picture) }}"
                         alt="{{ $client->name }}"
                         loading="lazy"
                         class="w-11 h-11 rounded-xl object-cover flex-shrink-0"
                         style="border: 2px solid var(--client-primary-border);">
                    @else
                    <div class="w-11 h-11 rounded-xl flex items-center justify-center text-base flex-shrink-0"
                         style="background: var(--client-primary-light); color: var(--client-primary); border: 2px solid var(--client-primary-border);">
                        <i class="fas {{ $client->client_type === 'company' ? 'fa-building' : 'fa-user' }}"></i>
                    </div>
                    @endif
                    <div class="min-w-0 flex-1">
                        <p class="font-semibold text-sm truncate" style="color: var(--text-primary);">{{ $client->name }}</p>
                        <p class="text-xs flex items-center gap-1 mt-0.5" style="color: var(--text-tertiary);">
                            <i class="fas {{ $client->client_type === 'company' ? 'fa-building' : 'fa-user' }} text-[10px]"></i>
                            {{ $client->client_type === 'company' ? 'Perusahaan' : 'Perorangan' }}
                        </p>
                    </div>
                    @if($notificationCount > 0)
                    <a href="{{ route('client.notifications.index') }}"
                       class="flex-shrink-0 w-7 h-7 flex items-center justify-center rounded-full text-white text-xs font-bold"
                       style="background: var(--apple-red);"
                       title="{{ $notificationCount }} notifikasi belum dibaca">
                        {{ $notificationCount > 9 ? '9+' : $notificationCount }}
                    </a>
                    @endif
                </div>
            </div>

            {{-- Navigation --}}
            <nav class="flex-1 overflow-y-auto px-3 py-3 space-y-4">
                @foreach($navGroups as $groupLabel => $groupItems)
                <div>
                    <p class="px-3 pb-2 text-[10px] font-semibold uppercase tracking-[0.18em]"
                       style="color: var(--text-tertiary);">{{ $groupLabel }}</p>
                    <div class="space-y-0.5">
                        @foreach($groupItems as $item)
                        @php $isActive = $item['active']; @endphp
                        <a href="{{ $item['route'] }}"
                           class="relative flex items-center px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-150 group"
                           style="{{ $isActive ? 'background: var(--client-primary-light); color: var(--client-primary);' : 'color: var(--text-secondary);' }}"
                           @if(!$isActive)
                           onmouseover="this.style.background='var(--surface-sunken)'; this.style.color='var(--text-primary)'"
                           onmouseout="this.style.background=''; this.style.color='var(--text-secondary)'"
                           @endif
                        >
                            @if($isActive)
                            <span class="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-5 rounded-r-full"
                                  style="background: var(--client-primary);"></span>
                            @endif
                            <i class="fas {{ $item['icon'] }} w-5 text-center flex-shrink-0 text-sm" aria-hidden="true"></i>
                            <span class="ml-2.5 flex-1 truncate">{{ $item['label'] }}</span>
                            @if(!empty($item['badge']) && $item['badge'] > 0)
                            <span class="text-[10px] font-bold text-white px-1.5 py-0.5 rounded-full flex-shrink-0 min-w-[20px] text-center"
                                  style="background: {{ $isActive ? 'var(--client-primary)' : ($item['badge_color'] === 'bg-blue-600' ? 'var(--client-primary)' : ($item['badge_color'] === 'bg-green-600' ? 'var(--apple-green)' : 'var(--apple-orange)')) }};">
                                {{ $item['badge'] > 99 ? '99+' : $item['badge'] }}
                            </span>
                            @endif
                        </a>
                        @endforeach
                    </div>
                </div>
                @endforeach
            </nav>

            {{-- Footer --}}
            <div class="flex-shrink-0 border-t px-3 py-3 space-y-1" style="border-color: var(--border-subtle);">
                {{-- Support link --}}
                @php $supportEmail = data_get(config('landing_metrics'), 'contact.email', 'info@bizmark.id'); @endphp
                <a href="mailto:{{ $supportEmail }}"
                   class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl text-sm transition-all duration-150"
                   style="color: var(--text-tertiary);"
                   onmouseover="this.style.background='var(--surface-sunken)'; this.style.color='var(--client-primary)'"
                   onmouseout="this.style.background=''; this.style.color='var(--text-tertiary)'">
                    <i class="fas fa-headset w-5 text-center text-sm flex-shrink-0" aria-hidden="true"></i>
                    <span class="flex-1 truncate">Bantuan: {{ $supportEmail }}</span>
                </a>

                {{-- Logout --}}
                <form method="POST" action="{{ route('client.logout') }}">
                    @csrf
                    <button type="submit"
                            class="w-full flex items-center gap-2.5 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-150 text-left"
                            style="color: var(--text-tertiary);"
                            onmouseover="this.style.background='color-mix(in oklab, var(--apple-red) 10%, transparent)'; this.style.color='var(--apple-red)'"
                            onmouseout="this.style.background=''; this.style.color='var(--text-tertiary)'">
                        <i class="fas fa-sign-out-alt w-5 text-center text-sm flex-shrink-0" aria-hidden="true"></i>
                        <span>Logout</span>
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden lg:ml-64">
            
            <!-- Desktop/Browser Header (Hidden in PWA Standalone) -->
            <header class="browser-only desktop-header z-10 sticky top-0 border-b"
                    style="background: var(--surface-elevated); border-color: var(--border-subtle); box-shadow: var(--shadow-xs);">
                <div class="flex items-center h-14 px-4 sm:px-6 gap-3">
                    <!-- Hamburger Menu (Mobile Browser Only) -->
                    <button @click="sidebarOpen = !sidebarOpen"
                            class="lg:hidden w-9 h-9 flex items-center justify-center rounded-lg transition-colors"
                            style="color: var(--text-secondary);"
                            onmouseover="this.style.background='var(--surface-sunken)'" onmouseout="this.style.background=''">
                        <i class="fas fa-bars"></i>
                    </button>

                    {{-- Left: Page title (legacy) OR Portal-v2 breadcrumb/CmdK hint --}}
                    <div class="flex-1 min-w-0">
                        {{-- Legacy title block (hidden on portal-v2) --}}
                        <div class="portal-v2-hidden">
                            <h2 class="text-lg font-bold leading-tight truncate" style="color: var(--text-primary);">
                                @yield('page-title', 'Portal Klien')
                            </h2>
                            <div class="hidden md:flex items-center gap-1.5 mt-0.5">
                                @if($draftCount > 0)
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium"
                                      style="background: var(--client-primary-light); color: var(--client-primary);">
                                    <i class="fas fa-pen-to-square mr-1 text-[10px]"></i>Draft: {{ $draftCount }}
                                </span>
                                @endif
                                @if($submittedCount > 0)
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium"
                                      style="background: var(--status-in_progress-bg); color: var(--status-in_progress);">
                                    <i class="fas fa-spinner mr-1 text-[10px]"></i>Proses: {{ $submittedCount }}
                                </span>
                                @endif
                                @if($pendingDocuments > 0)
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium"
                                      style="background: var(--status-paid-bg); color: var(--status-paid);">
                                    <i class="fas fa-file-circle-exclamation mr-1 text-[10px]"></i>Dok Pending: {{ $pendingDocuments }}
                                </span>
                                @endif
                            </div>
                        </div>

                        {{-- Portal v2: Command Palette shortcut hint (desktop only) --}}
                        @if($portalV2)
                        <button
                            x-data="{ isMac: /Mac|iPhone|iPad/.test(navigator.platform || '') }"
                            @click="$dispatch('cmdk-open')"
                            class="hidden lg:flex items-center gap-2 px-3 py-1.5 rounded-lg text-sm transition-all duration-150 group"
                            style="background: var(--surface-sunken); color: var(--text-tertiary); border: 1px solid var(--border-subtle);"
                            onmouseover="this.style.borderColor='var(--client-primary-border)'; this.style.color='var(--text-secondary)'"
                            onmouseout="this.style.borderColor='var(--border-subtle)'; this.style.color='var(--text-tertiary)'"
                            title="Buka Command Palette"
                        >
                            <i class="fas fa-magnifying-glass text-xs"></i>
                            <span>Cari atau tekan tindakan...</span>
                            <span class="ml-2 flex items-center gap-0.5 text-[10px] font-mono"
                                  style="color: var(--text-tertiary);">
                                <kbd class="px-1.5 py-0.5 rounded" style="background: var(--surface-elevated); border: 1px solid var(--border-default);" x-text="isMac ? '⌘' : 'Ctrl'"></kbd>
                                <kbd class="px-1.5 py-0.5 rounded" style="background: var(--surface-elevated); border: 1px solid var(--border-default);">K</kbd>
                            </span>
                        </button>
                        @endif
                    </div>

                    {{-- Right: Actions --}}
                    <div class="flex items-center gap-1.5 sm:gap-2 flex-shrink-0">
                        {{-- Date (desktop only) --}}
                        <div class="hidden xl:flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs"
                             style="background: var(--surface-sunken); color: var(--text-tertiary);">
                            <i class="fas fa-calendar-day" style="color: var(--text-tertiary);"></i>
                            <span>{{ now()->translatedFormat('d F Y') }}</span>
                        </div>

                        {{-- Dark Mode Toggle --}}
                        <button
                            @click="$store.theme && $store.theme.toggle()"
                            class="hidden lg:flex items-center justify-center w-9 h-9 rounded-lg transition-colors active:scale-95"
                            style="color: var(--text-secondary);"
                            onmouseover="this.style.background='var(--surface-sunken)'" onmouseout="this.style.background=''"
                            :aria-label="$store.theme && $store.theme.current === 'dark' ? 'Switch ke Light Mode' : 'Switch ke Dark Mode'"
                            title="Toggle Dark Mode"
                        >
                            <i :class="$store.theme && $store.theme.current === 'dark' ? 'fas fa-sun' : 'fas fa-moon'" class="text-sm"></i>
                        </button>

                        {{-- Notifications --}}
                        <div class="relative" x-data="{ open: false }" @click.outside="open = false" @keydown.escape.window="open = false">
                            <button
                                type="button"
                                aria-haspopup="true"
                                :aria-expanded="open"
                                title="Lihat notifikasi"
                                class="relative flex items-center justify-center w-9 h-9 rounded-lg transition-colors"
                                style="color: var(--text-secondary);"
                                onmouseover="this.style.background='var(--surface-sunken)'; this.style.color='var(--client-primary)'"
                                onmouseout="this.style.background=''; this.style.color='var(--text-secondary)'"
                                @click="open = !open"
                            >
                                <i class="fas fa-bell text-lg"></i>
                                @if($notificationCount > 0)
                                <span class="notification-badge absolute top-1 right-1 bg-red-500 text-white rounded-full font-semibold px-1">
                                    {{ $notificationCount > 9 ? '9+' : $notificationCount }}
                                </span>
                                @endif
                            </button>

                            @include('client.components.notification-dropdown')
                        </div>

                        {{-- Profile Pill --}}
                        <div class="flex items-center gap-2 rounded-full px-3 py-1.5 cursor-default border"
                             style="background: var(--surface-elevated); border-color: var(--border-subtle); box-shadow: var(--shadow-xs);">
                            @if($client->profile_picture && Storage::disk('public')->exists($client->profile_picture))
                            <img src="{{ asset('storage/' . $client->profile_picture) }}"
                                 alt="{{ $client->name }}"
                                 loading="lazy"
                                 class="w-7 h-7 rounded-full object-cover flex-shrink-0">
                            @else
                            <div class="w-7 h-7 rounded-full flex items-center justify-center flex-shrink-0 text-xs"
                                 style="background: var(--client-primary-light); color: var(--client-primary);">
                                <i class="fas {{ $client->client_type === 'company' ? 'fa-building' : 'fa-user' }}"></i>
                            </div>
                            @endif
                            <div class="hidden sm:block text-xs leading-tight max-w-[11rem] xl:max-w-[15rem]">
                                <p class="font-semibold truncate" style="color: var(--text-primary);" title="{{ $client->company_name ?? $client->name }}">
                                    {{ $client->company_name ?? $client->name }}
                                </p>
                                <p class="truncate" style="color: var(--text-tertiary);">
                                    Akun Klien
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Content - LinkedIn Style Full Width -->
            <main class="flex-1 overflow-y-auto" style="background: var(--surface-cool);">
                
                @if (session('success'))
                    <div class="mx-4 sm:mx-6 mt-4 p-4 border-l-4 rounded animate-fade-in"
                         style="background: color-mix(in oklab, var(--apple-green) 12%, var(--surface-elevated)); border-color: var(--apple-green); color: var(--text-primary);">
                        <i class="fas fa-check-circle mr-2" style="color: var(--apple-green);"></i>{{ session('success') }}
                    </div>
                @endif

                @if (session('error'))
                    <div class="mx-4 sm:mx-6 mt-4 p-4 border-l-4 rounded animate-fade-in"
                         style="background: color-mix(in oklab, var(--apple-red) 10%, var(--surface-elevated)); border-color: var(--apple-red); color: var(--text-primary);">
                        <i class="fas fa-exclamation-circle mr-2" style="color: var(--apple-red);"></i>{{ session('error') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="mx-4 sm:mx-6 mt-4 p-4 border-l-4 rounded animate-fade-in"
                         style="background: color-mix(in oklab, var(--apple-red) 10%, var(--surface-elevated)); border-color: var(--apple-red); color: var(--text-primary);">
                        <p class="font-semibold mb-2"><i class="fas fa-exclamation-triangle mr-2" style="color: var(--apple-red);"></i>Terjadi Kesalahan:</p>
                        <ul class="list-disc list-inside text-sm" style="color: var(--text-secondary);">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @yield('content')
                
            </main>
            
            <!-- Mobile Bottom Navigation v2 -->
            <nav id="bottom-nav"
                 class="lg:hidden fixed bottom-0 left-0 right-0 z-50 safe-area-bottom transition-transform duration-300 backdrop-blur-sm border-t"
                 style="background: color-mix(in oklab, var(--surface-elevated) 96%, transparent); border-color: var(--border-subtle);"
                 aria-label="Navigasi utama">
                <div class="grid grid-cols-5 h-14">

                    {{-- Home --}}
                    @php $activeHome = request()->routeIs('client.dashboard'); @endphp
                    <a href="{{ route('client.dashboard') }}"
                       class="relative flex flex-col items-center justify-center gap-0.5 transition-colors"
                       style="color: {{ $activeHome ? 'var(--client-primary)' : 'var(--text-tertiary)' }};">
                        @if($activeHome)
                        <span class="absolute top-0 left-1/2 -translate-x-1/2 w-8 h-0.5 rounded-full" style="background: var(--client-primary);"></span>
                        @endif
                        <i class="fas fa-house text-[18px]" aria-hidden="true"></i>
                        <span class="text-[9px] font-semibold">Home</span>
                    </a>

                    {{-- Layanan --}}
                    @php $activeSvc = request()->routeIs('client.services.*'); @endphp
                    <a href="{{ route('client.services.index') }}"
                       class="relative flex flex-col items-center justify-center gap-0.5 transition-colors"
                       style="color: {{ $activeSvc ? 'var(--client-primary)' : 'var(--text-tertiary)' }};">
                        @if($activeSvc)
                        <span class="absolute top-0 left-1/2 -translate-x-1/2 w-8 h-0.5 rounded-full" style="background: var(--client-primary);"></span>
                        @endif
                        <i class="fas fa-layer-group text-[18px]" aria-hidden="true"></i>
                        <span class="text-[9px] font-semibold">Layanan</span>
                    </a>

                    {{-- FAB: Ajukan Baru (center, elevated) --}}
                    <div class="flex items-center justify-center">
                        <a href="{{ route('client.applications.create') }}"
                           class="flex items-center justify-center w-12 h-12 -mt-5 rounded-full text-white active:scale-95 transition-all duration-150"
                           aria-label="Ajukan permohonan baru"
                           style="background: var(--client-primary); box-shadow: 0 4px 16px color-mix(in oklab, var(--client-primary) 45%, transparent);">
                            <i class="fas fa-plus text-lg" aria-hidden="true"></i>
                        </a>
                    </div>

                    {{-- Proyek --}}
                    @php $activeProj = request()->routeIs('client.projects.*'); @endphp
                    <a href="{{ route('client.projects.index') }}"
                       class="relative flex flex-col items-center justify-center gap-0.5 transition-colors"
                       style="color: {{ $activeProj ? 'var(--client-primary)' : 'var(--text-tertiary)' }};">
                        @if($activeProj)
                        <span class="absolute top-0 left-1/2 -translate-x-1/2 w-8 h-0.5 rounded-full" style="background: var(--client-primary);"></span>
                        @endif
                        <i class="fas fa-briefcase text-[18px]" aria-hidden="true"></i>
                        <span class="text-[9px] font-semibold">Proyek</span>
                        @if($activeProjects > 0)
                        <span class="absolute top-1 right-[18%] min-w-[14px] h-[14px] px-0.5 text-white text-[8px] font-bold rounded-full flex items-center justify-center"
                              style="background: var(--apple-green);">
                            {{ $activeProjects > 9 ? '9+' : $activeProjects }}
                        </span>
                        @endif
                    </a>

                    {{-- Notifikasi (replaces Documents to give unread count visibility on mobile) --}}
                    @php
                        $activeNotif = request()->routeIs('client.notifications.*');
                        $hasUnread   = $notificationCount > 0;
                    @endphp
                    <a href="{{ route('client.notifications.index') }}"
                       class="relative flex flex-col items-center justify-center gap-0.5 transition-colors"
                       style="color: {{ $activeNotif ? 'var(--client-primary)' : 'var(--text-tertiary)' }};">
                        @if($activeNotif)
                        <span class="absolute top-0 left-1/2 -translate-x-1/2 w-8 h-0.5 rounded-full" style="background: var(--client-primary);"></span>
                        @endif
                        <i class="fas fa-bell text-[18px]" aria-hidden="true"></i>
                        <span class="text-[9px] font-semibold">Notifikasi</span>
                        @if($hasUnread)
                        <span class="absolute top-1 right-[18%] min-w-[14px] h-[14px] px-0.5 text-white text-[8px] font-bold rounded-full flex items-center justify-center"
                              style="background: var(--apple-red);">
                            {{ $notificationCount > 9 ? '9+' : $notificationCount }}
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

    {{-- A11y: global aria-live region for toast/status announcements --}}
    <div id="a11y-announcer" role="status" aria-live="polite" aria-atomic="true"
         class="sr-only"></div>
    <div id="a11y-alerter" role="alert" aria-live="assertive" aria-atomic="true"
         class="sr-only"></div>

    {{-- Portal v2: Command Palette (⌘K / Ctrl+K) --}}
    @if($portalCmdk)
        <x-ui.command-palette />
    @endif
</body>
</html>
