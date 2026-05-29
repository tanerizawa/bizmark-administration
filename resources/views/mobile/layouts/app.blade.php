{{--
    Mobile App Layout
    Path: resources/views/mobile/layouts/app.blade.php
    
    Features:
    - PWA-ready structure
    - Bottom navigation
    - Native-like header
    - Safe area support (notch)
    - Offline indicator
--}}

<!DOCTYPE html>
<html lang="id" class="mobile-ui">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    {{-- PWA Meta Tags --}}
    <meta name="theme-color" content="#0A66C2">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="Bizmark Admin">
    
    {{-- Manifest --}}
    <link rel="manifest" href="/manifest.json">
    
    {{-- Icons --}}
    <link rel="icon" type="image/png" href="/images/pavicon.png">
    <link rel="apple-touch-icon" href="/images/pavicon.png">
    
    <title>@yield('title', 'Dashboard') - Bizmark Admin</title>
    
    {{-- Tailwind CSS CDN (for development - use compiled in production) --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        // Suppress Tailwind CDN warning in console (we know it's for development)
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'bizmark': {
                            DEFAULT: '#0A66C2',
                            dark: '#004182',
                            light: '#E7F3F8'
                        }
                    }
                }
            }
        }
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    {{-- Alpine.js --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    {{-- IndexedDB Manager for Offline Sync --}}
    <script src="/js/pwa/indexeddb.js"></script>
    
    <style>
        * {
            -webkit-tap-highlight-color: transparent;
            -webkit-touch-callout: none;
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
        
        body {
            margin: 0;
            padding: 0;
            min-height: 100vh;
            background: #F3F4F6;
            overscroll-behavior-y: contain;
            padding-bottom: env(safe-area-inset-bottom);
        }
        
        /* Fixed Header with safe area - Bizmark Brand Color */
        .mobile-header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 50;
            background: linear-gradient(135deg, #0A66C2 0%, #004182 100%);
            height: calc(56px + env(safe-area-inset-top));
            padding-top: env(safe-area-inset-top);
            box-shadow: 0 2px 8px rgba(10, 102, 194, 0.15);
            transition: transform 0.3s ease;
        }
        
        /* Content area padding */
        .mobile-content {
            padding-top: calc(56px + env(safe-area-inset-top));
            padding-bottom: calc(64px + env(safe-area-inset-bottom));
            min-height: 100vh;
        }
        
        /* Bottom Navigation with safe area */
        .mobile-bottom-nav {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            z-index: 50;
            background: white;
            border-top: 1px solid #caccce;
            height: calc(64px + env(safe-area-inset-bottom));
            padding-bottom: env(safe-area-inset-bottom);
            box-shadow: 0 -2px 8px rgba(0, 0, 0, 0.05);
        }
        
        /* Smooth transitions */
        .transition-smooth {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        /* Loading skeleton animation */
        @keyframes shimmer {
            0% { background-position: -200% 0; }
            100% { background-position: 200% 0; }
        }
        
        .skeleton {
            background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
            background-size: 200% 100%;
            animation: shimmer 1.5s infinite;
        }
        
        /* Pull to refresh indicator */
        .refresh-indicator {
            position: fixed;
            top: calc(56px + env(safe-area-inset-top) - 40px);
            left: 50%;
            transform: translateX(-50%);
            width: 40px;
            height: 40px;
            background: white;
            border-radius: 50%;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: opacity 0.3s ease;
            z-index: 60;
        }
        
        .refresh-indicator.show {
            opacity: 1;
        }
        
        /* Bottom sheet */
        .bottom-sheet {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: white;
            border-radius: 20px 20px 0 0;
            padding: 20px;
            padding-bottom: calc(20px + env(safe-area-inset-bottom));
            transform: translateY(100%);
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            z-index: 60;
        }
        
        .bottom-sheet.show {
            transform: translateY(0);
        }
        
        /* Haptic feedback simulation */
        .haptic-feedback {
            animation: haptic 0.1s ease;
        }
        
        @keyframes haptic {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-2px); }
            75% { transform: translateX(2px); }
        }
        
        /* Alpine.js x-cloak */
        [x-cloak] {
            display: none !important;
        }
        
        /* Toast animations */
        @keyframes fade-in {
            from {
                opacity: 0;
                transform: translate(-50%, 20px);
            }
            to {
                opacity: 1;
                transform: translate(-50%, 0);
            }
        }
        
        @keyframes fade-out {
            from {
                opacity: 1;
                transform: translate(-50%, 0);
            }
            to {
                opacity: 0;
                transform: translate(-50%, -20px);
            }
        }
        
        .animate-fade-in {
            animation: fade-in 0.3s ease-out;
        }
        
        .animate-fade-out {
            animation: fade-out 0.3s ease-in;
        }
    </style>
    
    @stack('styles')
</head>
<body>
    
    {{-- Pull to Refresh Indicator --}}
    <div id="refreshIndicator" class="refresh-indicator">
        <i class="fas fa-sync-alt" style="color: rgb(0, 119, 181);"></i>
    </div>

    {{-- Offline Indicator Banner --}}
    <div id="offlineIndicator" class="fixed top-0 left-0 right-0 z-[100] bg-amber-500 text-white text-center py-2 text-sm font-medium transform -translate-y-full transition-transform duration-300" style="padding-top: env(safe-area-inset-top);">
        <i class="fas fa-wifi-slash mr-2"></i>Anda sedang offline
    </div>

    {{-- Sync Pending Indicator --}}
    <div id="syncIndicator" class="hidden fixed top-[calc(56px+env(safe-area-inset-top)+4px)] right-4 z-50 bg-blue-500 text-white px-3 py-1 rounded-full text-xs font-medium shadow-lg">
        <i class="fas fa-sync fa-spin mr-1"></i><span id="syncText">Syncing...</span>
    </div>

    {{-- Fixed Header --}}
    <header class="mobile-header">
        <div class="h-14 px-4 flex items-center justify-between">
            {{-- Left: Menu or Back --}}
            @if(request()->routeIs('mobile.dashboard'))
                {{-- Dashboard: Show logo/icon instead of back button --}}
                <div class="p-2 -ml-2">
                    <div class="w-8 h-8 bg-white/10 rounded-lg flex items-center justify-center">
                        <i class="fas fa-briefcase text-white text-sm"></i>
                    </div>
                </div>
            @else
                {{-- Other pages: Show back button --}}
                <button onclick="history.back()" class="p-2 -ml-2 text-white hover:bg-white/10 rounded-full transition-smooth">
                    <i class="fas fa-arrow-left text-xl"></i>
                </button>
            @endif
            
            {{-- Center: Title --}}
            <h1 class="text-lg font-bold text-white flex-1 text-center px-4 truncate">
                @yield('title', 'Dashboard')
            </h1>
            
            {{-- Right: Actions --}}
            <div class="flex items-center gap-2">
                @yield('header-actions')
                
                <button onclick="showSettings()" 
                        class="p-2 -mr-2 text-white hover:bg-white/10 rounded-full transition-smooth">
                    <i class="fas fa-cog text-xl"></i>
                </button>
            </div>
        </div>
    </header>

    {{-- Main Content --}}
    <main class="mobile-content">
        <div class="px-3 py-4">
            @yield('content')
        </div>
    </main>

    {{-- Bottom Navigation - LinkedIn Style (5 items with center FAB) --}}
    <nav id="bottom-nav" class="mobile-bottom-nav transition-transform duration-300 ease-out">
        <div class="grid grid-cols-5 h-14">
            
            {{-- Home --}}
            <a href="{{ mobile_route('dashboard') }}" 
               class="flex flex-col items-center justify-center gap-0.5
                      {{ request()->routeIs('mobile.dashboard') ? 'text-[#0A66C2]' : 'text-gray-600' }} 
                      hover:text-[#0A66C2] transition-colors">
                <i class="fas fa-house text-xl"></i>
                <span class="text-[9px] font-medium">Home</span>
            </a>
            
            {{-- Tasks --}}
            <a href="{{ mobile_route('tasks.index') }}" 
               class="flex flex-col items-center justify-center gap-0.5 relative
                      {{ request()->routeIs('mobile.tasks*') ? 'text-[#0A66C2]' : 'text-gray-600' }} 
                      hover:text-[#0A66C2] transition-colors">
                <i class="fas fa-circle-check text-xl"></i>
                <span class="text-[9px] font-medium">Tasks</span>
                @if(isset($myTasksCount) && $myTasksCount > 0)
                    <span class="absolute top-0.5 right-[28%] w-4 h-4 bg-red-500 text-white text-[9px] rounded-full 
                                 flex items-center justify-center font-bold">
                        {{ $myTasksCount > 9 ? '9' : $myTasksCount }}
                    </span>
                @endif
            </a>
            
            {{-- Quick Add (Center - Elevated FAB style) --}}
            <div class="flex items-center justify-center">
                <button onclick="showQuickAdd()" 
                        class="flex items-center justify-center w-12 h-12 -mt-5 rounded-full 
                               bg-[#0A66C2] hover:bg-[#004182] text-white shadow-lg hover:shadow-xl 
                               transition-all active:scale-95">
                    <i class="fas fa-plus text-xl"></i>
                </button>
            </div>
            
            {{-- Notifications --}}
            <a href="{{ mobile_route('notifications.index') }}" 
               class="flex flex-col items-center justify-center gap-0.5 relative
                      {{ request()->routeIs('mobile.notifications*') ? 'text-[#0A66C2]' : 'text-gray-600' }} 
                      hover:text-[#0A66C2] transition-colors">
                <i class="fas fa-bell text-xl"></i>
                <span class="text-[9px] font-medium">Notif</span>
                @if(isset($unreadNotifCount) && $unreadNotifCount > 0)
                    <span class="absolute top-0.5 right-[28%] w-4 h-4 bg-amber-500 text-white text-[9px] rounded-full 
                                 flex items-center justify-center font-bold">
                        {{ $unreadNotifCount > 9 ? '9' : $unreadNotifCount }}
                    </span>
                @endif
            </a>
            
            {{-- Menu --}}
            <button onclick="showMenu()" 
                    class="flex flex-col items-center justify-center gap-0.5 text-gray-600 
                           hover:text-[#0A66C2] transition-colors">
                <i class="fas fa-bars text-xl"></i>
                <span class="text-[9px] font-medium">Menu</span>
            </button>
            
        </div>
    </nav>

    {{-- Quick Add Bottom Sheet --}}
    <div id="quickAddSheet" class="fixed inset-0 bg-black/50 hidden z-50" onclick="hideQuickAdd()">
        <div class="bottom-sheet" onclick="event.stopPropagation()">
            <div class="w-12 h-1 bg-gray-300 rounded-full mx-auto mb-3"></div>
            
            <h3 class="text-base font-bold text-gray-900 mb-3">Tambah Baru</h3>
            
            <div class="space-y-2">
                {{-- Input Uang Masuk --}}
                <button onclick="showFinancialInput('income')" 
                        class="block w-full p-3 bg-white border border-gray-200 rounded-lg hover:border-gray-300 transition-colors text-left">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 bg-gray-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-arrow-down text-gray-700 text-sm"></i>
                        </div>
                        <div class="flex-1">
                            <div class="font-medium text-gray-900 text-sm">Uang Masuk</div>
                            <div class="text-xs text-gray-500">Pembayaran, invoice</div>
                        </div>
                    </div>
                </button>
                
                {{-- Input Uang Keluar --}}
                <button onclick="showFinancialInput('expense')" 
                        class="block w-full p-3 bg-white border border-gray-200 rounded-lg hover:border-gray-300 transition-colors text-left">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 bg-gray-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-arrow-up text-gray-700 text-sm"></i>
                        </div>
                        <div class="flex-1">
                            <div class="font-medium text-gray-900 text-sm">Uang Keluar</div>
                            <div class="text-xs text-gray-500">Operasional, gaji</div>
                        </div>
                    </div>
                </button>
                
                {{-- Quick Task --}}
                <button onclick="showTaskInput()" 
                        class="block w-full p-3 bg-white border border-gray-200 rounded-lg hover:border-gray-300 transition-colors text-left">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 bg-gray-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-circle-check text-gray-700 text-sm"></i>
                        </div>
                        <div class="flex-1">
                            <div class="font-medium text-gray-900 text-sm">Task Baru</div>
                            <div class="text-xs text-gray-500">Buat task cepat</div>
                        </div>
                    </div>
                </button>
                
                {{-- Upload Dokumen --}}
                <a href="{{ mobile_route('documents.upload') }}" 
                   class="block p-3 bg-white border border-gray-200 rounded-lg hover:border-gray-300 transition-colors">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 bg-gray-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-camera text-gray-700 text-sm"></i>
                        </div>
                        <div class="flex-1">
                            <div class="font-medium text-gray-900 text-sm">Upload Dokumen</div>
                            <div class="text-xs text-gray-500">Foto atau file</div>
                        </div>
                    </div>
                </a>
                
                {{-- Proyek Baru --}}
                <a href="{{ mobile_route('projects.create') }}" 
                   class="block p-3 bg-white border border-gray-200 rounded-lg hover:border-gray-300 transition-colors">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 bg-gray-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-folder-plus text-gray-700 text-sm"></i>
                        </div>
                        <div class="flex-1">
                            <div class="font-medium text-gray-900 text-sm">Proyek Baru</div>
                            <div class="text-xs text-gray-500">Tambah proyek</div>
                        </div>
                    </div>
                </a>
            </div>
            
            <button onclick="hideQuickAdd()" 
                    class="w-full mt-4 py-3 text-gray-600 font-medium hover:bg-gray-50 rounded-lg transition-colors">
                Batal
            </button>
        </div>
    </div>

    {{-- Settings Bottom Sheet --}}
    <div id="settingsSheet" class="fixed inset-0 bg-black/50 hidden z-50" onclick="hideSettings()">
        <div class="bottom-sheet" onclick="event.stopPropagation()">
            <div class="w-12 h-1 bg-gray-300 rounded-full mx-auto mb-3"></div>
            
            <h3 class="text-base font-bold text-gray-900 mb-3">Pengaturan</h3>
            
            <div class="space-y-2">
                {{-- Profile --}}
                <a href="{{ mobile_route('profile.show') }}" 
                   class="block p-3 bg-white border border-gray-200 rounded-lg hover:border-gray-300 transition-colors">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 bg-[#E7F3F8] rounded-lg flex items-center justify-center">
                            <i class="fas fa-user text-[#0A66C2] text-sm"></i>
                        </div>
                        <div class="flex-1">
                            <div class="font-medium text-gray-900 text-sm">Profile Saya</div>
                            <div class="text-xs text-gray-500">{{ auth()->user()->name }}</div>
                        </div>
                        <i class="fas fa-chevron-right text-gray-400 text-xs"></i>
                    </div>
                </a>
                
                {{-- Preferences --}}
                <a href="{{ mobile_route('settings.preferences') }}" 
                   class="block p-3 bg-white border border-gray-200 rounded-lg hover:border-gray-300 transition-colors">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 bg-gray-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-sliders text-gray-700 text-sm"></i>
                        </div>
                        <div class="flex-1">
                            <div class="font-medium text-gray-900 text-sm">Preferensi</div>
                            <div class="text-xs text-gray-500">Notifikasi, bahasa, tema</div>
                        </div>
                        <i class="fas fa-chevron-right text-gray-400 text-xs"></i>
                    </div>
                </a>
                
                {{-- About --}}
                <a href="{{ mobile_route('settings.about') }}" 
                   class="block p-3 bg-white border border-gray-200 rounded-lg hover:border-gray-300 transition-colors">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 bg-gray-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-info-circle text-gray-700 text-sm"></i>
                        </div>
                        <div class="flex-1">
                            <div class="font-medium text-gray-900 text-sm">Tentang Aplikasi</div>
                            <div class="text-xs text-gray-500">Versi, bantuan, privacy</div>
                        </div>
                        <i class="fas fa-chevron-right text-gray-400 text-xs"></i>
                    </div>
                </a>
                
                {{-- Logout --}}
                <form method="POST" action="{{ route('logout') }}" class="mt-4">
                    @csrf
                    <button type="submit" 
                            class="block w-full p-3 bg-red-50 border border-red-200 rounded-lg hover:bg-red-100 transition-colors text-left">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 bg-red-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-sign-out-alt text-red-600 text-sm"></i>
                            </div>
                            <div class="flex-1">
                                <div class="font-medium text-red-600 text-sm">Keluar</div>
                            </div>
                        </div>
                    </button>
                </form>
            </div>
            
            <button onclick="hideSettings()" 
                    class="w-full mt-4 py-3 text-gray-600 font-medium hover:bg-gray-50 rounded-lg transition-colors">
                Tutup
            </button>
        </div>
    </div>

    {{-- Menu Bottom Sheet --}}
    <div id="menuSheet" class="fixed inset-0 bg-black/50 hidden z-50" onclick="hideMenu()">
        <div class="bottom-sheet" onclick="event.stopPropagation()">
            <div class="w-12 h-1 bg-gray-300 rounded-full mx-auto mb-3"></div>
            
            <h3 class="text-base font-bold text-gray-900 mb-3">Menu</h3>
            
            <div class="space-y-2 max-h-96 overflow-y-auto">
                {{-- Dashboard --}}
                <a href="{{ mobile_route('dashboard') }}" 
                   class="block p-3 bg-white border border-gray-200 rounded-lg hover:border-gray-300 transition-colors">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 bg-[#E7F3F8] rounded-lg flex items-center justify-center">
                            <i class="fas fa-house text-[#0A66C2] text-sm"></i>
                        </div>
                        <div class="flex-1">
                            <div class="font-medium text-gray-900 text-sm">Dashboard</div>
                        </div>
                        <i class="fas fa-chevron-right text-gray-400 text-xs"></i>
                    </div>
                </a>
                
                {{-- Projects --}}
                <a href="{{ mobile_route('projects.index') }}" 
                   class="block p-3 bg-white border border-gray-200 rounded-lg hover:border-gray-300 transition-colors">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 bg-gray-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-folder text-gray-700 text-sm"></i>
                        </div>
                        <div class="flex-1">
                            <div class="font-medium text-gray-900 text-sm">Proyek</div>
                        </div>
                        <i class="fas fa-chevron-right text-gray-400 text-xs"></i>
                    </div>
                </a>
                
                {{-- Clients --}}
                <a href="{{ mobile_route('clients.index') }}" 
                   class="block p-3 bg-white border border-gray-200 rounded-lg hover:border-gray-300 transition-colors">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 bg-gray-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-building text-gray-700 text-sm"></i>
                        </div>
                        <div class="flex-1">
                            <div class="font-medium text-gray-900 text-sm">Klien</div>
                        </div>
                        <i class="fas fa-chevron-right text-gray-400 text-xs"></i>
                    </div>
                </a>
                
                {{-- Tasks --}}
                <a href="{{ mobile_route('tasks.index') }}" 
                   class="block p-3 bg-white border border-gray-200 rounded-lg hover:border-gray-300 transition-colors">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 bg-gray-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-circle-check text-gray-700 text-sm"></i>
                        </div>
                        <div class="flex-1">
                            <div class="font-medium text-gray-900 text-sm">Tasks</div>
                        </div>
                        <i class="fas fa-chevron-right text-gray-400 text-xs"></i>
                    </div>
                </a>
                
                {{-- Financial --}}
                <a href="{{ mobile_route('financial.index') }}" 
                   class="block p-3 bg-white border border-gray-200 rounded-lg hover:border-gray-300 transition-colors">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 bg-gray-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-wallet text-gray-700 text-sm"></i>
                        </div>
                        <div class="flex-1">
                            <div class="font-medium text-gray-900 text-sm">Keuangan</div>
                        </div>
                        <i class="fas fa-chevron-right text-gray-400 text-xs"></i>
                    </div>
                </a>
                
                {{-- Documents --}}
                <a href="{{ mobile_route('documents.index') }}" 
                   class="block p-3 bg-white border border-gray-200 rounded-lg hover:border-gray-300 transition-colors">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 bg-gray-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-file text-gray-700 text-sm"></i>
                        </div>
                        <div class="flex-1">
                            <div class="font-medium text-gray-900 text-sm">Dokumen</div>
                        </div>
                        <i class="fas fa-chevron-right text-gray-400 text-xs"></i>
                    </div>
                </a>
                
                {{-- Reports --}}
                <a href="{{ mobile_route('reports.index') }}" 
                   class="block p-3 bg-white border border-gray-200 rounded-lg hover:border-gray-300 transition-colors">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 bg-gray-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-chart-line text-gray-700 text-sm"></i>
                        </div>
                        <div class="flex-1">
                            <div class="font-medium text-gray-900 text-sm">Laporan</div>
                        </div>
                        <i class="fas fa-chevron-right text-gray-400 text-xs"></i>
                    </div>
                </a>
                
                {{-- Team --}}
                <a href="{{ mobile_route('team.index') }}" 
                   class="block p-3 bg-white border border-gray-200 rounded-lg hover:border-gray-300 transition-colors">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 bg-gray-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-users text-gray-700 text-sm"></i>
                        </div>
                        <div class="flex-1">
                            <div class="font-medium text-gray-900 text-sm">Tim</div>
                        </div>
                        <i class="fas fa-chevron-right text-gray-400 text-xs"></i>
                    </div>
                </a>
            </div>
            
            <button onclick="hideMenu()" 
                    class="w-full mt-4 py-3 text-gray-600 font-medium hover:bg-gray-50 rounded-lg transition-colors">
                Tutup
            </button>
        </div>
    </div>

    {{-- Install PWA Prompt - Android & Generic --}}
    <div id="installPrompt" class="fixed bottom-20 left-4 right-4 bg-white rounded-2xl shadow-xl p-4 hidden z-50 transform transition-transform duration-300 scale-95 opacity-0">
        <div class="flex items-start gap-3">
            <div class="w-12 h-12 bg-[#E7F3F8] rounded-xl flex items-center justify-center flex-shrink-0">
                <i class="fas fa-mobile-alt text-[#0A66C2] text-xl"></i>
            </div>
            <div class="flex-1">
                <h4 class="font-bold text-gray-900 mb-1">Install Bizmark Admin</h4>
                <p class="text-sm text-gray-600 mb-3">Tambah ke home screen untuk akses cepat</p>
                <div class="flex gap-2">
                    <button onclick="installPWA()" 
                            class="px-4 py-2 bg-[#0A66C2] text-white rounded-lg text-sm font-medium 
                                   hover:bg-[#004182] transition-colors">
                        Install
                    </button>
                    <button onclick="dismissInstallPrompt()" 
                            class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm font-medium 
                                   hover:bg-gray-200 transition-colors">
                        Nanti
                    </button>
                </div>
            </div>
            <button onclick="dismissInstallPrompt()" 
                    class="text-gray-400 hover:text-gray-600 -mt-1 -mr-1">
                <i class="fas fa-times"></i>
            </button>
        </div>
    </div>

    {{-- Install PWA Prompt - iOS Safari --}}
    <div id="iosInstallPrompt" class="fixed bottom-20 left-4 right-4 bg-white rounded-2xl shadow-xl p-4 hidden z-50 transform transition-transform duration-300 scale-95 opacity-0">
        <div class="flex items-start gap-3">
            <div class="w-12 h-12 bg-[#E7F3F8] rounded-xl flex items-center justify-center flex-shrink-0">
                <i class="fab fa-apple text-gray-800 text-2xl"></i>
            </div>
            <div class="flex-1">
                <h4 class="font-bold text-gray-900 mb-1">Install Bizmark Admin</h4>
                <p class="text-sm text-gray-600 mb-3">Untuk pengalaman terbaik di iPhone:</p>
                <div class="space-y-2 text-sm text-gray-700">
                    <div class="flex items-center gap-2">
                        <span class="w-5 h-5 bg-gray-100 rounded flex items-center justify-center text-xs font-medium">1</span>
                        <span>Tap <i class="fas fa-share-from-square text-[#007AFF]"></i> di Safari</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="w-5 h-5 bg-gray-100 rounded flex items-center justify-center text-xs font-medium">2</span>
                        <span>Scroll & tap <strong>"Add to Home Screen"</strong></span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="w-5 h-5 bg-gray-100 rounded flex items-center justify-center text-xs font-medium">3</span>
                        <span>Tap <strong>"Add"</strong></span>
                    </div>
                </div>
                <button onclick="dismissIOSInstallPrompt()" 
                        class="mt-3 w-full py-2 bg-gray-100 text-gray-700 rounded-lg text-sm font-medium 
                               hover:bg-gray-200 transition-colors">
                    Mengerti
                </button>
            </div>
            <button onclick="dismissIOSInstallPrompt()" 
                    class="text-gray-400 hover:text-gray-600 -mt-1 -mr-1">
                <i class="fas fa-times"></i>
            </button>
        </div>
    </div>

    {{-- Offline/Sync Indicator --}}
    <div id="syncIndicator" class="fixed top-[calc(56px+env(safe-area-inset-top)+8px)] right-4 bg-amber-500 text-white px-3 py-1.5 rounded-full text-xs font-medium shadow-lg hidden z-50 flex items-center gap-2">
        <i class="fas fa-sync fa-spin"></i>
        <span id="syncText">Syncing...</span>
    </div>

    {{-- Scripts --}}
    <script>
        // Service Worker Registration (PWA Mobile)
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('/sw-mobile.js')
                .then(registration => {
                    console.log('✅ Mobile Service Worker registered:', registration.scope);

                    const activateWaitingWorker = () => {
                        if (registration.waiting) {
                            registration.waiting.postMessage({ type: 'SKIP_WAITING' });
                        }
                    };

                    registration.update().catch(() => {});
                    activateWaitingWorker();

                    registration.addEventListener('updatefound', () => {
                        const newWorker = registration.installing;
                        if (!newWorker) return;

                        newWorker.addEventListener('statechange', () => {
                            if (newWorker.state === 'installed') {
                                activateWaitingWorker();
                            }
                        });
                    });
                })
                .catch(error => {
                    console.log('❌ Service Worker registration failed:', error);
                });
            
            // Listen for SW controller change (after update)
            navigator.serviceWorker.addEventListener('controllerchange', () => {
                window.location.reload();
            });
        }

        // PWA Install Prompt - Enhanced with iOS support and 7-day cool-off
        let deferredPrompt;
        const INSTALL_COOLOFF_DAYS = 7;
        
        // Detect iOS Safari
        const isIOS = /iPad|iPhone|iPod/.test(navigator.userAgent) && !window.MSStream;
        const isInStandaloneMode = window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone === true;
        const isSafari = /^((?!chrome|android).)*safari/i.test(navigator.userAgent);
        
        // Check if install prompt should be shown
        function shouldShowInstallPrompt() {
            if (isInStandaloneMode) return false; // Already installed
            
            const dismissedTime = localStorage.getItem('installPromptDismissed');
            if (dismissedTime) {
                const daysSinceDismissed = (Date.now() - parseInt(dismissedTime)) / (1000 * 60 * 60 * 24);
                if (daysSinceDismissed < INSTALL_COOLOFF_DAYS) {
                    return false;
                }
            }
            return true;
        }
        
        // Show appropriate install prompt
        function showAppropriateInstallPrompt() {
            if (!shouldShowInstallPrompt()) return;
            
            // Delay showing prompt
            setTimeout(() => {
                if (isIOS && isSafari) {
                    showIOSInstallPrompt();
                } else if (deferredPrompt) {
                    showInstallPrompt();
                }
            }, 3000);
        }
        
        // Android/Chrome install flow
        window.addEventListener('beforeinstallprompt', (e) => {
            e.preventDefault();
            deferredPrompt = e;
            showAppropriateInstallPrompt();
        });
        
        function showInstallPrompt() {
            const prompt = document.getElementById('installPrompt');
            prompt.classList.remove('hidden');
            setTimeout(() => {
                prompt.classList.remove('scale-95', 'opacity-0');
                prompt.classList.add('scale-100', 'opacity-100');
            }, 10);
        }

        function installPWA() {
            if (deferredPrompt) {
                deferredPrompt.prompt();
                deferredPrompt.userChoice.then((choiceResult) => {
                    if (choiceResult.outcome === 'accepted') {
                        console.log('✅ User accepted PWA install');
                        localStorage.setItem('pwaInstalled', 'true');
                    }
                    deferredPrompt = null;
                    dismissInstallPrompt();
                });
            }
        }

        function dismissInstallPrompt() {
            const prompt = document.getElementById('installPrompt');
            prompt.classList.add('scale-95', 'opacity-0');
            prompt.classList.remove('scale-100', 'opacity-100');
            setTimeout(() => {
                prompt.classList.add('hidden');
            }, 300);
            localStorage.setItem('installPromptDismissed', Date.now().toString());
        }
        
        // iOS Safari install instructions
        function showIOSInstallPrompt() {
            const prompt = document.getElementById('iosInstallPrompt');
            prompt.classList.remove('hidden');
            setTimeout(() => {
                prompt.classList.remove('scale-95', 'opacity-0');
                prompt.classList.add('scale-100', 'opacity-100');
            }, 10);
        }
        
        function dismissIOSInstallPrompt() {
            const prompt = document.getElementById('iosInstallPrompt');
            prompt.classList.add('scale-95', 'opacity-0');
            prompt.classList.remove('scale-100', 'opacity-100');
            setTimeout(() => {
                prompt.classList.add('hidden');
            }, 300);
            localStorage.setItem('installPromptDismissed', Date.now().toString());
        }
        
        // Check for iOS on page load
        if (isIOS && isSafari && !isInStandaloneMode && shouldShowInstallPrompt()) {
            setTimeout(showIOSInstallPrompt, 3000);
        }

        // Quick Add Bottom Sheet
        function showQuickAdd() {
            const sheet = document.getElementById('quickAddSheet');
            sheet.classList.remove('hidden');
            setTimeout(() => {
                sheet.querySelector('.bottom-sheet').classList.add('show');
            }, 10);
        }

        function hideQuickAdd() {
            const sheet = document.getElementById('quickAddSheet');
            sheet.querySelector('.bottom-sheet').classList.remove('show');
            setTimeout(() => {
                sheet.classList.add('hidden');
            }, 300);
        }

        // Financial Input Handler
        function showFinancialInput(type) {
            hideQuickAdd();
            // Redirect to financial quick input page with type parameter
            window.location.href = '{{ mobile_route("financial.quick-input") }}?type=' + type;
        }

        // Task Input Handler
        function showTaskInput() {
            hideQuickAdd();
            // Redirect to tasks page and trigger modal
            window.location.href = '{{ mobile_route("tasks.index") }}?openQuickAdd=1';
        }

        // Menu Handler (replaces Profile)
        function showMenu() {
            const sheet = document.getElementById('menuSheet');
            sheet.classList.remove('hidden');
            setTimeout(() => {
                sheet.querySelector('.bottom-sheet').classList.add('show');
            }, 10);
        }

        function hideMenu() {
            const sheet = document.getElementById('menuSheet');
            sheet.querySelector('.bottom-sheet').classList.remove('show');
            setTimeout(() => {
                sheet.classList.add('hidden');
            }, 300);
        }

        // Settings Handler
        function showSettings() {
            const sheet = document.getElementById('settingsSheet');
            sheet.classList.remove('hidden');
            setTimeout(() => {
                sheet.querySelector('.bottom-sheet').classList.add('show');
            }, 10);
        }

        function hideSettings() {
            const sheet = document.getElementById('settingsSheet');
            sheet.querySelector('.bottom-sheet').classList.remove('show');
            setTimeout(() => {
                sheet.classList.add('hidden');
            }, 300);
        }

        // Pull to Refresh - Best Practice Implementation
        let pullStartY = 0;
        let pullEndY = 0;
        let pulling = false;
        let isRefreshing = false;

        document.addEventListener('touchstart', (e) => {
            if (window.scrollY === 0 && !isRefreshing) {
                pullStartY = e.touches[0].pageY;
                pulling = true;
            }
        }, { passive: true });

        document.addEventListener('touchmove', (e) => {
            if (!pulling || isRefreshing) return;
            pullEndY = e.touches[0].pageY;
            const pullDistance = pullEndY - pullStartY;

            const indicator = document.getElementById('refreshIndicator');
            if (pullDistance > 80) {
                indicator.classList.add('show');
                const rotation = Math.min(pullDistance * 2, 360);
                indicator.querySelector('i').style.transform = `rotate(${rotation}deg)`;
            }
        }, { passive: true });

        document.addEventListener('touchend', () => {
            if (!pulling || isRefreshing) return;
            const pullDistance = pullEndY - pullStartY;
            const indicator = document.getElementById('refreshIndicator');

            if (pullDistance > 80) {
                // Trigger refresh
                refreshPage();
            } else {
                indicator.classList.remove('show');
            }
            
            pulling = false;
        });

        async function refreshPage() {
            if (isRefreshing) return;
            isRefreshing = true;
            
            const indicator = document.getElementById('refreshIndicator');
            const icon = indicator.querySelector('i');
            
            // Show spinning animation
            icon.classList.add('fa-spin');
            icon.style.transform = '';
            
            try {
                // Check if we're actually online first
                if (!navigator.onLine) {
                    throw new Error('No internet connection');
                }
                
                // Clear service worker cache for this page
                if ('caches' in window) {
                    const cacheNames = await caches.keys();
                    for (const cacheName of cacheNames) {
                        const cache = await caches.open(cacheName);
                        await cache.delete(window.location.href);
                    }
                }
                
                // Fetch fresh data with cache busting
                const timestamp = new Date().getTime();
                const url = new URL(window.location.href);
                url.searchParams.set('_refresh', timestamp);
                
                const response = await fetch(url.toString(), {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Cache-Control': 'no-cache, no-store, must-revalidate',
                        'Pragma': 'no-cache',
                        'Expires': '0'
                    },
                    cache: 'no-store'
                });
                
                if (!response.ok) {
                    throw new Error('Failed to fetch fresh data');
                }
                
                // Success - reload with cache bypass
                setTimeout(() => {
                    window.location.reload(true);
                }, 500);
                
            } catch (error) {
                console.error('Refresh failed:', error);
                
                // Just stop the indicator, don't show error notification
                icon.classList.remove('fa-spin');
                indicator.classList.remove('show');
                isRefreshing = false;
            }
        }

        // Haptic Feedback
        function triggerHaptic(type = 'medium') {
            if ('vibrate' in navigator) {
                const patterns = {
                    light: 10,
                    medium: 20,
                    heavy: 30
                };
                navigator.vibrate(patterns[type] || 20);
            }
        }

        // Safe Area Inset Detection
        function checkSafeArea() {
            const root = document.documentElement;
            const hasNotch = getComputedStyle(root).getPropertyValue('padding-top');
            
            if (hasNotch && parseFloat(hasNotch) > 0) {
                root.classList.add('has-notch');
            }
        }

        checkSafeArea();

        // Auto-hide header and bottom nav on scroll (LinkedIn-style)
        let lastScrollY = window.scrollY;
        let scrollTimeout;
        
        function handleScroll() {
            const header = document.querySelector('.mobile-header');
            const bottomNav = document.getElementById('bottom-nav');
            const currentScrollY = window.scrollY;
            
            if (!header || !bottomNav) return;
            
            // Clear existing timeout
            clearTimeout(scrollTimeout);
            
            // Add delay before processing (debounce)
            scrollTimeout = setTimeout(() => {
                if (currentScrollY > lastScrollY && currentScrollY > 100) {
                    // Scrolling DOWN - hide header and bottom nav
                    header.style.transform = 'translateY(-100%)';
                    bottomNav.style.transform = 'translateY(100%)';
                } else {
                    // Scrolling UP - show header and bottom nav
                    header.style.transform = 'translateY(0)';
                    bottomNav.style.transform = 'translateY(0)';
                }
                
                lastScrollY = currentScrollY;
            }, 10);
        }
        
        // Attach scroll listener with passive flag for better performance
        window.addEventListener('scroll', handleScroll, { passive: true });
        
        // Ensure nav is visible on page load
        window.addEventListener('load', () => {
            const header = document.querySelector('.mobile-header');
            const bottomNav = document.getElementById('bottom-nav');
            if (header) header.style.transform = 'translateY(0)';
            if (bottomNav) bottomNav.style.transform = 'translateY(0)';
        });

        // Standalone Mode Detection
        if (window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone === true) {
            document.documentElement.classList.add('pwa-mode');
            console.log('🚀 Running in PWA mode');
        }
        
        // Screen Width Detection for Responsive Routing
        (function() {
            function updateScreenWidth() {
                const width = window.innerWidth;
                fetch('/api/set-screen-width', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ width: width })
                }).catch(err => console.log('Screen width update failed:', err));
            }
            
            // Update on load
            updateScreenWidth();
            
            // Update on resize (debounced)
            let resizeTimer;
            window.addEventListener('resize', function() {
                clearTimeout(resizeTimer);
                resizeTimer = setTimeout(function() {
                    updateScreenWidth();
                    // Auto-refresh if crossing mobile/desktop threshold
                    const currentWidth = window.innerWidth;
                    const wasMobile = sessionStorage.getItem('wasMobile') === 'true';
                    const isMobileNow = currentWidth < 768;
                    
                    if (wasMobile !== isMobileNow) {
                        sessionStorage.setItem('wasMobile', isMobileNow);
                        // Refresh page to apply new layout
                        setTimeout(() => window.location.reload(), 500);
                    }
                }, 500);
            });
            
            // Store initial state
            sessionStorage.setItem('wasMobile', (window.innerWidth < 768).toString());
        })();
        
        // ============= Offline/Background Sync Integration =============
        
        // Handle service worker sync messages
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.addEventListener('message', (event) => {
                if (event.data.type === 'SYNC_SUCCESS') {
                    showToast('Data berhasil disinkronkan', 'success');
                    updateSyncIndicator();
                }
            });
        }
        
        // Check and update sync indicator
        async function updateSyncIndicator() {
            if (typeof BizmarkDB !== 'undefined') {
                const count = await BizmarkDB.getPendingRequestsCount();
                const indicator = document.getElementById('syncIndicator');
                const text = document.getElementById('syncText');
                
                if (count > 0) {
                    indicator.classList.remove('hidden');
                    text.textContent = `${count} pending`;
                } else {
                    indicator.classList.add('hidden');
                }
            }
        }
        
        // Queue a request for offline sync
        async function queueOfflineRequest(url, method, body, type = 'form') {
            if (typeof BizmarkDB === 'undefined') {
                console.error('BizmarkDB not loaded');
                return false;
            }
            
            try {
                await BizmarkDB.addPendingRequest(url, method, body, type);
                
                // Request background sync if supported
                if ('serviceWorker' in navigator && 'SyncManager' in window) {
                    const registration = await navigator.serviceWorker.ready;
                    await registration.sync.register('sync-pending-data');
                }
                
                updateSyncIndicator();
                showToast('Data disimpan offline, akan sync otomatis', 'info');
                return true;
            } catch (error) {
                console.error('Failed to queue request:', error);
                return false;
            }
        }
        
        // Save form as draft
        async function saveFormDraft(formType, formData) {
            if (typeof BizmarkDB !== 'undefined') {
                try {
                    await BizmarkDB.saveFormDraft(formType, formData);
                    showToast('Draft tersimpan', 'info');
                } catch (error) {
                    console.error('Failed to save draft:', error);
                }
            }
        }
        
        // Check for pending sync on load
        document.addEventListener('DOMContentLoaded', updateSyncIndicator);
        
        // Offline indicator toggle
        function updateOfflineIndicator(isOnline) {
            const indicator = document.getElementById('offlineIndicator');
            if (indicator) {
                if (isOnline) {
                    indicator.style.transform = 'translateY(-100%)';
                } else {
                    indicator.style.transform = 'translateY(0)';
                }
            }
        }
        
        // Check initial online status
        document.addEventListener('DOMContentLoaded', () => {
            updateOfflineIndicator(navigator.onLine);
        });
        
        // Attempt sync when coming back online
        window.addEventListener('online', async () => {
            updateOfflineIndicator(true);
            showToast('Kembali online', 'success');
            
            if ('serviceWorker' in navigator && 'SyncManager' in window) {
                const registration = await navigator.serviceWorker.ready;
                await registration.sync.register('sync-pending-data');
            }
        });
        
        window.addEventListener('offline', () => {
            updateOfflineIndicator(false);
            showToast('Anda sedang offline', 'warning');
        });
        
        // Toast notification helper
        function showToast(message, type = 'info') {
            const colors = {
                success: 'bg-green-500',
                error: 'bg-red-500',
                warning: 'bg-amber-500',
                info: 'bg-blue-500'
            };
            
            const icons = {
                success: 'fa-check-circle',
                error: 'fa-exclamation-circle',
                warning: 'fa-exclamation-triangle',
                info: 'fa-info-circle'
            };
            
            const toast = document.createElement('div');
            toast.className = `fixed bottom-24 left-1/2 -translate-x-1/2 ${colors[type]} text-white px-4 py-2 rounded-full shadow-lg z-50 flex items-center gap-2 text-sm font-medium animate-fade-in`;
            toast.innerHTML = `<i class="fas ${icons[type]}"></i> ${message}`;
            
            document.body.appendChild(toast);
            
            setTimeout(() => {
                toast.classList.remove('animate-fade-in');
                toast.classList.add('animate-fade-out');
                setTimeout(() => toast.remove(), 300);
            }, 3000);
        }
    </script>

    @stack('scripts')
</body>
</html>
