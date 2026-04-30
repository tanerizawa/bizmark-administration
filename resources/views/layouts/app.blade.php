<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>@yield('title', 'Dashboard') - Bizmark Permit Management</title>
    
    <!-- FIX (BUG-11/12): Replaced CDN dependencies with local Vite build.
         Font Awesome, Chart.js and Alpine.js are now bundled via npm/Vite.
         Google Fonts preconnect kept for performance; font-display:swap prevents blocking. -->
    
    <!-- Google Fonts Preconnect -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Vite Bundled Assets (CSS + JS) -->
    @vite(['resources/css/admin.css', 'resources/js/app.js'])
    
    @stack('styles')
</head>
<body class="@auth authenticated @endauth">
    @auth
    <!-- Main Layout with Sidebar -->
    {{-- Navigation data ($navCounts, $permitNotifications, $otherNotifications) provided by NavigationComposer --}}
    <div class="app-shell">
        <!-- Fixed Sidebar -->
        <aside class="app-sidebar">
            <!-- Logo Header -->
            <div class="sidebar-header">
                <h1 style="font-size: 1.25rem; font-weight: 700; color: var(--dark-text-primary); margin: 0; display: flex; align-items: center;">
                    <i class="fas fa-shield-alt" style="color: var(--apple-blue); margin-right: 0.5rem;"></i>
                    Bizmark.ID
                </h1>
                <p style="font-size: 0.75rem; color: var(--dark-text-secondary); margin: 0.25rem 0 0 0;">Admin Portal</p>
            </div>

            <!-- Scrollable Navigation -->
            <nav class="sidebar-nav">
                <!-- Main Navigation -->
                <div class="nav-section">
                    <div class="nav-links">
                        <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                            <div class="nav-link-content">
                                <i class="fas fa-home"></i>
                                <span>Dashboard</span>
                            </div>
                        </a>
                        
                        <a href="{{ route('projects.index') }}" class="nav-link {{ request()->routeIs('projects.*') ? 'active' : '' }}">
                            <div class="nav-link-content">
                                <i class="fas fa-project-diagram"></i>
                                <span>Proyek</span>
                            </div>
                            @if(isset($navCounts['projects']) && $navCounts['projects'] > 0)
                                <span class="nav-badge">{{ $navCounts['projects'] }}</span>
                            @endif
                        </a>
                        
                        <a href="{{ route('tasks.index') }}" class="nav-link {{ request()->routeIs('tasks.*') ? 'active' : '' }}">
                            <div class="nav-link-content">
                                <i class="fas fa-tasks"></i>
                                <span>Tugas</span>
                            </div>
                            @if(isset($navCounts['pending_tasks']) && $navCounts['pending_tasks'] > 0)
                                <span class="nav-badge badge-alert">{{ $navCounts['pending_tasks'] }}</span>
                            @endif
                        </a>
                        
                        <a href="{{ route('documents.index') }}" class="nav-link {{ request()->routeIs('documents.*') ? 'active' : '' }}">
                            <div class="nav-link-content">
                                <i class="fas fa-file-alt"></i>
                                <span>Dokumen</span>
                            </div>
                            @if(isset($navCounts['documents']) && $navCounts['documents'] > 0)
                                <span class="nav-badge">{{ $navCounts['documents'] }}</span>
                            @endif
                        </a>
                        
                        <a href="{{ route('institutions.index') }}" class="nav-link {{ request()->routeIs('institutions.*') ? 'active' : '' }}">
                            <div class="nav-link-content">
                                <i class="fas fa-building"></i>
                                <span>Instansi</span>
                            </div>
                            @if(isset($navCounts['institutions']) && $navCounts['institutions'] > 0)
                                <span class="nav-badge">{{ $navCounts['institutions'] }}</span>
                            @endif
                        </a>
                        
                        <a href="{{ route('clients.index') }}" class="nav-link {{ request()->routeIs('clients.*') ? 'active' : '' }}">
                            <div class="nav-link-content">
                                <i class="fas fa-users"></i>
                                <span>Klien</span>
                            </div>
                            @if(isset($navCounts['clients']) && $navCounts['clients'] > 0)
                                <span class="nav-badge">{{ $navCounts['clients'] }}</span>
                            @endif
                        </a>
                        
                        <a href="{{ route('settings.index') }}" class="nav-link {{ request()->routeIs('settings.*') ? 'active' : '' }}">
                            <div class="nav-link-content">
                                <i class="fas fa-cog"></i>
                                <span>Pengaturan</span>
                            </div>
                        </a>
                        
                        @if(auth()->user()->hasRole('admin'))
                        <a href="{{ route('admin.ai-settings.index') }}" class="nav-link {{ request()->routeIs('admin.ai-settings.*') ? 'active' : '' }}">
                            <div class="nav-link-content">
                                <i class="fas fa-brain"></i>
                                <span>AI Settings</span>
                            </div>
                            <span class="badge badge-sm bg-gradient-info ms-2" style="font-size: 0.65rem;">NEW</span>
                        </a>
                        @endif
                    </div>
                </div>

                <!-- Lead Management -->
                <div class="nav-section">
                    <div class="nav-section-title">Lead Management</div>
                    <div class="nav-links">
                        <a href="{{ route('admin.leads.index') }}" class="nav-link {{ request()->routeIs('admin.leads.*') || request()->routeIs('admin.service-inquiries.*') || request()->routeIs('admin.consultation-leads.*') ? 'active' : '' }}">
                            <div class="nav-link-content">
                                <i class="fas fa-user-tag"></i>
                                <span>Kelola Lead</span>
                            </div>
                        </a>
                    </div>
                </div>

                <!-- Permit Management -->
                <div class="nav-section">
                    <div class="nav-section-title">Human Resource</div>
                    <div class="nav-links">
                        <a href="{{ route('admin.permits.index') }}" class="nav-link {{ request()->routeIs('admin.permits.*') || request()->routeIs('admin.permit-dashboard') || request()->routeIs('admin.permit-applications.*') || request()->routeIs('permit-types.*') || request()->routeIs('admin.payments.*') || request()->routeIs('admin.master-data.*') || request()->routeIs('admin.settings.kbli.*') ? 'active' : '' }}">
                            <div class="nav-link-content">
                                <i class="fas fa-briefcase"></i>
                                <span>Kelola Perizinan</span>
                            </div>
                            @if($permitNotifications['total'] > 0)
                                <span class="nav-badge badge-alert">{{ $permitNotifications['total'] }}</span>
                            @endif
                        </a>
                    </div>
                </div>

                <!-- Recruitment -->
                <div class="nav-section">
                    <div class="nav-section-title">Recruitment</div>
                    <div class="nav-links">
                        <a href="{{ route('admin.recruitment.index') }}" class="nav-link {{ request()->routeIs('admin.recruitment.*') || request()->routeIs('admin.jobs.*') || request()->routeIs('admin.applications.*') ? 'active' : '' }}">
                            <div class="nav-link-content">
                                <i class="fas fa-user-tie"></i>
                                <span>Kelola Rekrutmen</span>
                            </div>
                            @if($otherNotifications['pending_job_apps'] > 0)
                                <span class="nav-badge badge-alert">{{ $otherNotifications['pending_job_apps'] }}</span>
                            @endif
                        </a>
                    </div>
                </div>

                <!-- Email Management -->
                <div class="nav-section">
                    <div class="nav-section-title">Komunikasi</div>
                    <div class="nav-links">
                        <a href="{{ route('admin.email-management.index') }}" class="nav-link {{ request()->routeIs('admin.email-management.*') || request()->routeIs('admin.inbox.*') || request()->routeIs('admin.campaigns.*') || request()->routeIs('admin.subscribers.*') || request()->routeIs('admin.templates.*') || request()->routeIs('admin.email.settings.*') || request()->routeIs('admin.email-accounts.*') ? 'active' : '' }}">
                            <div class="nav-link-content">
                                <i class="fas fa-envelope"></i>
                                <span>Kelola Email</span>
                            </div>
                            @if($otherNotifications['unread_emails'] > 0)
                                <span class="nav-badge badge-alert">{{ $otherNotifications['unread_emails'] }}</span>
                            @endif
                        </a>
                    </div>
                </div>

                <!-- Financial Management -->
                <div class="nav-section">
                    <div class="nav-section-title">Keuangan</div>
                    <div class="nav-links">
                        <a href="{{ route('cash-accounts.index') }}" class="nav-link {{ request()->routeIs('cash-accounts.*') || request()->routeIs('reconciliations.*') ? 'active' : '' }}">
                            <div class="nav-link-content">
                                <i class="fas fa-wallet"></i>
                                <span>Akun Kas & Bank</span>
                            </div>
                            @if($otherNotifications['pending_reconciliations'] > 0)
                                <span class="nav-badge badge-warning">{{ $otherNotifications['pending_reconciliations'] }}</span>
                            @endif
                        </a>
                    </div>
                </div>
        
                <!-- Content & Media -->
                <div class="nav-section">
                    <div class="nav-section-title">Konten</div>
                    <div class="nav-links">
                        <a href="{{ route('articles.index') }}" class="nav-link {{ request()->routeIs('articles.*') && !request()->routeIs('auto-post.*') ? 'active' : '' }}">
                            <div class="nav-link-content">
                                <i class="fas fa-newspaper"></i>
                                <span>Artikel & Berita</span>
                            </div>
                        </a>
                        
                        @can('content.manage')
                        <!-- Auto-Post AI -->
                        <a href="{{ route('auto-post.index') }}" class="nav-link {{ request()->routeIs('auto-post.*') ? 'active' : '' }}">
                            <div class="nav-link-content">
                                <i class="fas fa-robot"></i>
                                <span>Auto-Post AI</span>
                            </div>
                        </a>
                        @endcan

                        <!-- SEO Command Center -->
                        <a href="{{ route('admin.seo.command-center') }}" class="nav-link {{ request()->routeIs('admin.seo.*') ? 'active' : '' }}">
                            <div class="nav-link-content">
                                <i class="fas fa-search-dollar"></i>
                                <span>SEO Command</span>
                            </div>
                        </a>
                    </div>
                </div>
            </nav>

            <!-- User Info Footer -->
            <div class="sidebar-footer">
                <div style="display: flex; align-items: center; gap: 0.75rem;">
                    <a href="{{ route('admin.profile.edit') }}" style="display: flex; align-items: center; gap: 0.75rem; flex: 1; min-width: 0; text-decoration: none; border-radius: 10px; padding: 0.25rem; transition: background-color 0.2s;" onmouseover="this.style.backgroundColor='var(--dark-bg-tertiary)'" onmouseout="this.style.backgroundColor='transparent'">
                        <div style="width: 2.5rem; height: 2.5rem; border-radius: 50%; background: var(--apple-blue); display: flex; align-items: center; justify-content: center; color: #FFFFFF; font-weight: 600; font-size: 0.875rem; flex-shrink: 0;">
                            {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                        </div>
                        <div style="flex: 1; min-width: 0;">
                            <p style="font-size: 0.875rem; font-weight: 500; color: var(--dark-text-primary); margin: 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ Auth::user()->name }}</p>
                            <p style="font-size: 0.75rem; color: var(--dark-text-secondary); margin: 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ Auth::user()->email }}</p>
                        </div>
                    </a>
                    <form method="POST" action="{{ route('logout') }}" style="margin: 0;">
                        @csrf
                        <button type="submit" style="padding: 0.5rem; color: var(--dark-text-secondary); background: transparent; border: none; cursor: pointer; border-radius: 8px; transition: all 0.2s;" onmouseover="this.style.color='#EF4444'" onmouseout="this.style.color='var(--dark-text-secondary)'" title="Logout">
                            <i class="fas fa-sign-out-alt"></i>
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <!-- Main Content Area -->
        <div class="app-main">
            <!-- Top Bar -->
            <header class="app-topbar">
                <div>
                    <h2 style="font-size: 1rem; font-weight: 600; color: var(--dark-text-primary); margin: 0;">@yield('page-title', 'Dashboard')</h2>
                    <p style="font-size: 0.75rem; color: var(--dark-text-secondary); margin: 0;">{{ now()->format('l, d F Y') }}</p>
                </div>
                <div style="display: flex; align-items: center; gap: 0.75rem;">
                    <!-- Notifications Button -->
                    <div style="position: relative;">
                        <a href="{{ route('admin.notifications') }}" 
                           style="position: relative; padding: 0.5rem; border-radius: 10px; color: var(--dark-text-secondary); background: transparent; border: none; cursor: pointer; display: inline-flex; align-items: center; justify-content: center; text-decoration: none; transition: all 0.2s;"
                           onmouseover="this.style.backgroundColor='var(--dark-bg-tertiary)'" 
                           onmouseout="this.style.backgroundColor='transparent'"
                           title="Notifikasi">
                            <i class="fas fa-bell"></i>
                            @if(($permitNotifications['total'] ?? 0) + ($otherNotifications['unread_emails'] ?? 0) > 0)
                                <span style="position: absolute; top: 0.25rem; right: 0.25rem; width: 0.5rem; height: 0.5rem; background: var(--apple-red); border-radius: 50%; border: 2px solid var(--dark-bg-elevated);"></span>
                            @endif
                        </a>
                    </div>

                    <!-- Search Button (Toggle) -->
                    <button onclick="toggleSearch()" 
                            style="padding: 0.5rem; border-radius: 10px; color: var(--dark-text-secondary); background: transparent; border: none; cursor: pointer; transition: all 0.2s;"
                            onmouseover="this.style.backgroundColor='var(--dark-bg-tertiary)'" 
                            onmouseout="this.style.backgroundColor='transparent'"
                            title="Pencarian">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </header>

            <!-- Global Search Overlay (Hidden by default) -->
            <div id="searchOverlay" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.7); z-index: 9999; backdrop-filter: blur(8px);" onclick="toggleSearch()">
                <div style="max-width: 600px; margin: 6rem auto; padding: 0 1.5rem;">
                    <div style="background: var(--dark-bg-elevated); border-radius: 16px; padding: 1.5rem; box-shadow: 0 20px 60px rgba(0,0,0,0.6);" onclick="event.stopPropagation()">
                        <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1.5rem;">
                            <i class="fas fa-search" style="color: var(--dark-text-tertiary);"></i>
                            <input type="text" 
                                   id="globalSearchInput"
                                   placeholder="Cari proyek, dokumen, klien..." 
                                   style="flex: 1; background: transparent; border: none; outline: none; color: var(--dark-text-primary); font-size: 1.25rem;"
                                   autofocus>
                            <button onclick="toggleSearch()" style="padding: 0.5rem; color: var(--dark-text-tertiary); background: transparent; border: none; cursor: pointer;">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        <div id="searchResults" style="max-height: 400px; overflow-y: auto;">
                            <p style="text-align: center; color: var(--dark-text-tertiary); padding: 2rem;">
                                Mulai ketik untuk mencari...
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Scrollable Content -->
            <main class="app-content">
                @yield('content')
            </main>
        </div>
    </div>
    @else
    <!-- Guest Layout (Login, etc.) -->
    <div id="app">
        @yield('content')
    </div>
    @endauth
    
    
    <!-- Currency Formatter & Terbilang -->
    <script src="{{ asset('js/currency-terbilang.js') }}"></script>
    
    <!-- Screen Width Detection for Responsive Routing -->
    <script>
        (function() {
            function updateScreenWidth() {
                const width = window.innerWidth;
                fetch('/api/set-screen-width', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
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

        // Global Search Toggle
        function toggleSearch() {
            const overlay = document.getElementById('searchOverlay');
            const input = document.getElementById('globalSearchInput');
            
            if (overlay.style.display === 'none') {
                overlay.style.display = 'block';
                setTimeout(() => input.focus(), 100);
            } else {
                overlay.style.display = 'none';
                input.value = '';
                document.getElementById('searchResults').innerHTML = '<p style="text-align: center; color: var(--dark-text-tertiary); padding: 2rem;">Mulai ketik untuk mencari...</p>';
            }
        }

        // Keyboard shortcut: Ctrl+K or Cmd+K to open search
        document.addEventListener('keydown', function(e) {
            if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
                e.preventDefault();
                toggleSearch();
            }
            // ESC to close
            if (e.key === 'Escape') {
                const overlay = document.getElementById('searchOverlay');
                if (overlay && overlay.style.display !== 'none') {
                    toggleSearch();
                }
            }
        });

        // Simple Search Implementation (can be enhanced with AJAX)
        @auth
        let searchTimeout;
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('globalSearchInput');
            if (searchInput) {
                searchInput.addEventListener('input', function(e) {
                    clearTimeout(searchTimeout);
                    const query = e.target.value.trim();
                    
                    if (query.length < 2) {
                        document.getElementById('searchResults').innerHTML = '<p style="text-align: center; color: var(--dark-text-tertiary); padding: 2rem;">Mulai ketik untuk mencari...</p>';
                        return;
                    }
                    
                    searchTimeout = setTimeout(() => {
                        // Show loading
                        document.getElementById('searchResults').innerHTML = '<p style="text-align: center; color: var(--dark-text-tertiary); padding: 2rem;"><i class="fas fa-spinner fa-spin mr-2"></i>Mencari...</p>';
                        
                        // Fetch search results from API
                        fetch(`{{ route('admin.search') }}?q=${encodeURIComponent(query)}`, {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json'
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            displaySearchResults(data);
                        })
                        .catch(error => {
                            console.error('Search error:', error);
                            document.getElementById('searchResults').innerHTML = '<p style="text-align: center; color: var(--apple-red); padding: 2rem;">Terjadi kesalahan saat mencari</p>';
                        });
                    }, 300);
                });
            }
        });

        // Display search results
        function displaySearchResults(data) {
            const resultsContainer = document.getElementById('searchResults');
            
            if (data.total === 0) {
                resultsContainer.innerHTML = `
                    <div style="padding: 2rem; text-align: center;">
                        <i class="fas fa-search" style="font-size: 2rem; color: var(--dark-text-tertiary); margin-bottom: 1rem;"></i>
                        <p style="color: var(--dark-text-secondary);">Tidak ada hasil untuk "${data.query}"</p>
                    </div>
                `;
                return;
            }

            let html = `<div style="padding: 0.5rem;">`;
            html += `<p style="color: var(--dark-text-tertiary); font-size: 0.75rem; text-transform: uppercase; margin-bottom: 1rem; padding: 0 0.5rem;">${data.total} hasil ditemukan</p>`;

            // Helper function to render result item
            const renderItem = (item) => `
                <a href="${item.url}" 
                   style="display: flex; align-items: center; gap: 1rem; padding: 0.75rem; border-radius: 10px; text-decoration: none; color: var(--dark-text-primary); transition: all 0.2s; margin-bottom: 0.5rem;"
                   onmouseover="this.style.backgroundColor='var(--dark-bg-tertiary)'" 
                   onmouseout="this.style.backgroundColor='transparent'">
                    <div style="width: 2.5rem; height: 2.5rem; border-radius: 8px; background: var(--${item.color}); display: flex; align-items: center; justify-content: center; color: white;">
                        <i class="fas ${item.icon}"></i>
                    </div>
                    <div style="flex: 1; min-width: 0;">
                        <div style="font-weight: 500; margin-bottom: 0.25rem; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">${item.title}</div>
                        <div style="font-size: 0.75rem; color: var(--dark-text-tertiary); overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">${item.subtitle}</div>
                    </div>
                    <div style="font-size: 0.75rem; color: var(--dark-text-tertiary); padding: 0.25rem 0.5rem; background: var(--dark-bg-tertiary); border-radius: 6px;">${item.type}</div>
                </a>
            `;

            // Render each category
            const categories = [
                { key: 'projects', label: 'Proyek' },
                { key: 'tasks', label: 'Task' },
                { key: 'documents', label: 'Dokumen' },
                { key: 'clients', label: 'Klien' },
                { key: 'institutions', label: 'Instansi' },
                { key: 'permits', label: 'Perizinan' }
            ];

            categories.forEach(category => {
                const items = data.results[category.key];
                if (items && items.length > 0) {
                    html += `<div style="margin-bottom: 1.5rem;">`;
                    html += `<p style="color: var(--dark-text-tertiary); font-size: 0.75rem; text-transform: uppercase; margin-bottom: 0.5rem; padding: 0 0.5rem;">${category.label}</p>`;
                    items.forEach(item => {
                        html += renderItem(item);
                    });
                    html += `</div>`;
                }
            });

            html += `</div>`;
            resultsContainer.innerHTML = html;
        }
        @endauth
    </script>
    
    <!-- Submenu Toggle Function -->
    <script>
        function toggleSubmenu(button) {
            const submenu = button.parentElement;
            const content = submenu.querySelector('.nav-submenu-content');
            const icon = button.querySelector('.submenu-icon');
            
            // Toggle display
            if (content.style.display === 'none' || content.style.display === '') {
                content.style.display = 'block';
                icon.style.transform = 'rotate(180deg)';
                submenu.classList.add('active');
            } else {
                content.style.display = 'none';
                icon.style.transform = 'rotate(0deg)';
                if (!button.parentElement.querySelector('.nav-sublink.active')) {
                    submenu.classList.remove('active');
                }
            }
        }
        
        // Auto-expand active submenu on page load
        document.addEventListener('DOMContentLoaded', function() {
            const activeSubmenus = document.querySelectorAll('.nav-submenu.active');
            activeSubmenus.forEach(submenu => {
                const content = submenu.querySelector('.nav-submenu-content');
                const icon = submenu.querySelector('.submenu-icon');
                if (content && icon) {
                    content.style.display = 'block';
                    icon.style.transform = 'rotate(180deg)';
                }
            });
        });
    </script>
    
    <!-- Currency Helper - MUST load before other scripts -->
    <script src="{{ asset('js/currency-helper.js') }}"></script>
    
    @stack('scripts')
</body>
</html>
