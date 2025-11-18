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
    <meta name="theme-color" content="#0a66c2">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="Bizmark Admin">
    
    {{-- Manifest --}}
    <link rel="manifest" href="/manifest.json">
    
    {{-- Icons --}}
    <link rel="icon" type="image/png" sizes="192x192" href="/icons/icon-192x192.png">
    <link rel="apple-touch-icon" href="/icons/icon-192x192.png">
    
    <title>@yield('title', 'Dashboard') - Bizmark Admin</title>
    
    {{-- Tailwind CSS CDN (for development - use compiled in production) --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    {{-- Alpine.js --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <style>
        * {
            -webkit-tap-highlight-color: transparent;
            -webkit-touch-callout: none;
            font-family: -apple-system, system-ui, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', 'Fira Sans', Ubuntu, Oxygen, 'Oxygen Sans', Cantarell, 'Droid Sans', Arial, sans-serif;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }
        
        body {
            margin: 0;
            padding: 0;
            min-height: 100vh;
            background: #F3F4F6;
            overscroll-behavior-y: contain;
            padding-bottom: env(safe-area-inset-bottom);
        }
        
        /* Fixed Header with safe area - LinkedIn style */
        .mobile-header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 50;
            background: linear-gradient(135deg, #0a66c2 0%, #004182 100%);
            height: calc(56px + env(safe-area-inset-top));
            padding-top: env(safe-area-inset-top);
            box-shadow: 0 2px 8px rgba(10, 102, 194, 0.2);
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
            border-top: 1px solid #E5E7EB;
            height: calc(64px + env(safe-area-inset-bottom));
            padding-bottom: env(safe-area-inset-bottom);
            box-shadow: 0 -2px 8px rgba(0, 0, 0, 0.05);
        }
        
        /* Smooth transitions */
        .transition-smooth {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        /* Offline indicator */
        .offline-indicator {
            position: fixed;
            top: calc(56px + env(safe-area-inset-top));
            left: 0;
            right: 0;
            background: #EF4444;
            color: white;
            padding: 8px;
            text-align: center;
            font-size: 12px;
            z-index: 40;
            transform: translateY(-100%);
            transition: transform 0.3s ease;
        }
        
        .offline-indicator.show {
            transform: translateY(0);
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
    </style>
    
    @stack('styles')
</head>
<body>
    
    {{-- Offline Indicator --}}
    <div id="offlineIndicator" class="offline-indicator">
        <i class="fas fa-wifi-slash mr-2"></i>
        <span>Anda sedang offline. Data akan disinkronkan saat online kembali.</span>
    </div>
    
    {{-- Pull to Refresh Indicator --}}
    <div id="refreshIndicator" class="refresh-indicator">
        <i class="fas fa-sync-alt text-blue-600"></i>
    </div>

    {{-- Fixed Header --}}
    <header class="mobile-header">
        <div class="h-14 px-4 flex items-center justify-between">
            {{-- Left: Menu or Back --}}
            <button onclick="history.back()" class="p-2 -ml-2 text-white hover:bg-white/10 rounded-full transition-smooth">
                <i class="fas fa-arrow-left text-xl"></i>
            </button>
            
            {{-- Center: Title --}}
            <h1 class="text-lg font-bold text-white flex-1 text-center px-4 truncate">
                @yield('title', 'Dashboard')
            </h1>
            
            {{-- Right: Actions --}}
            <div class="flex items-center gap-2">
                @yield('header-actions')
                
                <button onclick="toggleSettings()" 
                        class="p-2 -mr-2 text-white hover:bg-white/10 rounded-full transition-smooth">
                    <i class="fas fa-cog text-xl"></i>
                </button>
            </div>
        </div>
    </header>

    {{-- Main Content --}}
    <main class="mobile-content">
        <div class="px-4 py-6">
            @yield('content')
        </div>
    </main>

    {{-- Bottom Navigation - LinkedIn Style (5 items with center FAB) --}}
    <nav id="bottom-nav" class="mobile-bottom-nav">
        <div class="grid grid-cols-5 h-14">
            
            {{-- Home --}}
            <a href="{{ mobile_route('dashboard') }}" 
               class="flex flex-col items-center justify-center gap-0.5
                      {{ request()->routeIs('mobile.dashboard') ? 'text-[#0a66c2]' : 'text-gray-600' }} 
                      hover:text-[#0a66c2] transition-colors">
                <i class="fas fa-house text-xl"></i>
                <span class="text-[9px] font-medium">Home</span>
            </a>
            
            {{-- Tasks --}}
            <a href="{{ mobile_route('tasks.index') }}" 
               class="flex flex-col items-center justify-center gap-0.5 relative
                      {{ request()->routeIs('mobile.tasks*') ? 'text-[#0a66c2]' : 'text-gray-600' }} 
                      hover:text-[#0a66c2] transition-colors">
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
                               bg-[#0a66c2] hover:bg-[#004182] text-white shadow-lg hover:shadow-xl 
                               transition-all active:scale-95">
                    <i class="fas fa-plus text-xl"></i>
                </button>
            </div>
            
            {{-- Notifications --}}
            <a href="{{ mobile_route('notifications.index') }}" 
               class="flex flex-col items-center justify-center gap-0.5 relative
                      {{ request()->routeIs('mobile.notifications*') ? 'text-[#0a66c2]' : 'text-gray-600' }} 
                      hover:text-[#0a66c2] transition-colors">
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
                           hover:text-[#0a66c2] transition-colors">
                <i class="fas fa-bars text-xl"></i>
                <span class="text-[9px] font-medium">Menu</span>
            </button>
            
        </div>
    </nav>

    {{-- Quick Add Bottom Sheet --}}
    <div id="quickAddSheet" class="fixed inset-0 bg-black/50 hidden z-50" onclick="hideQuickAdd()">
        <div class="bottom-sheet" onclick="event.stopPropagation()">
            <div class="w-12 h-1 bg-gray-300 rounded-full mx-auto mb-4"></div>
            
            <h3 class="text-lg font-bold text-gray-900 mb-4">Tambah Baru</h3>
            
            <div class="space-y-2">
                {{-- Input Uang Masuk --}}
                <button onclick="showFinancialInput('income')" 
                        class="block w-full p-4 bg-green-50 rounded-xl hover:bg-green-100 transition-colors text-left">
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center mr-3">
                            <i class="fas fa-arrow-down text-green-600"></i>
                        </div>
                        <div>
                            <div class="font-medium text-gray-900">Input Uang Masuk</div>
                            <div class="text-xs text-gray-600">Pembayaran, invoice, dll</div>
                        </div>
                    </div>
                </button>
                
                {{-- Input Uang Keluar --}}
                <button onclick="showFinancialInput('expense')" 
                        class="block w-full p-4 bg-red-50 rounded-xl hover:bg-red-100 transition-colors text-left">
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center mr-3">
                            <i class="fas fa-arrow-up text-red-600"></i>
                        </div>
                        <div>
                            <div class="font-medium text-gray-900">Input Uang Keluar</div>
                            <div class="text-xs text-gray-600">Operasional, gaji, dll</div>
                        </div>
                    </div>
                </button>
                
                {{-- Quick Task --}}
                <button onclick="showTaskInput()" 
                        class="block w-full p-4 bg-blue-50 rounded-xl hover:bg-blue-100 transition-colors text-left">
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                            <i class="fas fa-circle-check text-blue-600"></i>
                        </div>
                        <div>
                            <div class="font-medium text-gray-900">Quick Task</div>
                            <div class="text-xs text-gray-600">Buat task cepat</div>
                        </div>
                    </div>
                </button>
                
                {{-- Upload Dokumen --}}
                <a href="{{ mobile_route('documents.upload') }}" 
                   class="block p-4 bg-purple-50 rounded-xl hover:bg-purple-100 transition-colors">
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center mr-3">
                            <i class="fas fa-camera text-purple-600"></i>
                        </div>
                        <div>
                            <div class="font-medium text-gray-900">Upload Dokumen</div>
                            <div class="text-xs text-gray-600">Ambil foto atau pilih file</div>
                        </div>
                    </div>
                </a>
                
                {{-- Proyek Baru --}}
                <a href="{{ mobile_route('projects.create') }}" 
                   class="block p-4 bg-indigo-50 rounded-xl hover:bg-indigo-100 transition-colors">
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-indigo-100 rounded-full flex items-center justify-center mr-3">
                            <i class="fas fa-folder-plus text-indigo-600"></i>
                        </div>
                        <div>
                            <div class="font-medium text-gray-900">Proyek Baru</div>
                            <div class="text-xs text-gray-600">Tambah proyek perizinan</div>
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

    {{-- Install PWA Prompt --}}
    <div id="installPrompt" class="fixed bottom-20 left-4 right-4 bg-white rounded-2xl shadow-xl p-4 hidden z-50">
        <div class="flex items-start gap-3">
            <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center flex-shrink-0">
                <i class="fas fa-mobile-alt text-blue-600 text-xl"></i>
            </div>
            <div class="flex-1">
                <h4 class="font-bold text-gray-900 mb-1">Install Bizmark Admin</h4>
                <p class="text-sm text-gray-600 mb-3">Tambah ke home screen untuk akses cepat</p>
                <div class="flex gap-2">
                    <button onclick="installPWA()" 
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium 
                                   hover:bg-blue-700 transition-colors">
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

    {{-- Scripts --}}
    <script>
        // Service Worker Registration
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('/sw.js')
                .then(registration => {
                    console.log('✅ Service Worker registered:', registration);
                })
                .catch(error => {
                    console.log('❌ Service Worker registration failed:', error);
                });
        }

        // Offline/Online Detection
        function updateOnlineStatus() {
            const offlineIndicator = document.getElementById('offlineIndicator');
            if (navigator.onLine) {
                offlineIndicator.classList.remove('show');
            } else {
                offlineIndicator.classList.add('show');
            }
        }

        window.addEventListener('online', updateOnlineStatus);
        window.addEventListener('offline', updateOnlineStatus);
        updateOnlineStatus();

        // PWA Install Prompt
        let deferredPrompt;

        window.addEventListener('beforeinstallprompt', (e) => {
            e.preventDefault();
            deferredPrompt = e;
            
            // Show custom install prompt after 3 seconds
            setTimeout(() => {
                document.getElementById('installPrompt').classList.remove('hidden');
            }, 3000);
        });

        function installPWA() {
            if (deferredPrompt) {
                deferredPrompt.prompt();
                deferredPrompt.userChoice.then((choiceResult) => {
                    if (choiceResult.outcome === 'accepted') {
                        console.log('✅ User accepted PWA install');
                    }
                    deferredPrompt = null;
                    dismissInstallPrompt();
                });
            }
        }

        function dismissInstallPrompt() {
            document.getElementById('installPrompt').classList.add('hidden');
            localStorage.setItem('installPromptDismissed', Date.now());
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
            // Redirect to quick task creation page
            window.location.href = '{{ mobile_route("tasks.create") }}';
        }

        // Menu Handler (replaces Profile)
        function showMenu() {
            // Implementation for menu overlay
            console.log('Menu clicked');
            // TODO: Show menu overlay with Profile, Settings, Logout
        }

        // Settings Menu
        function toggleSettings() {
            // Implementation for settings menu
            console.log('Settings clicked');
        }

        // Pull to Refresh
        let pullStartY = 0;
        let pullEndY = 0;
        let pulling = false;

        document.addEventListener('touchstart', (e) => {
            if (window.scrollY === 0) {
                pullStartY = e.touches[0].pageY;
                pulling = true;
            }
        });

        document.addEventListener('touchmove', (e) => {
            if (!pulling) return;
            pullEndY = e.touches[0].pageY;
            const pullDistance = pullEndY - pullStartY;

            const indicator = document.getElementById('refreshIndicator');
            if (pullDistance > 80) {
                indicator.classList.add('show');
                indicator.querySelector('i').style.transform = `rotate(${pullDistance}deg)`;
            }
        });

        document.addEventListener('touchend', () => {
            if (!pulling) return;
            const pullDistance = pullEndY - pullStartY;
            const indicator = document.getElementById('refreshIndicator');

            if (pullDistance > 80) {
                // Trigger refresh
                refreshPage();
            }
            
            indicator.classList.remove('show');
            pulling = false;
        });

        function refreshPage() {
            // Add spinner animation
            const indicator = document.getElementById('refreshIndicator');
            indicator.querySelector('i').classList.add('fa-spin');

            // Fetch fresh data
            fetch(window.location.href, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.text())
            .then(() => {
                setTimeout(() => {
                    window.location.reload();
                }, 500);
            });
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
    </script>

    @stack('scripts')
</body>
</html>
