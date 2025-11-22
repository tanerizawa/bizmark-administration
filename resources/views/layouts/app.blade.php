<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>@yield('title', 'Dashboard') - Bizmark Permit Management</title>
    
    <!-- External CSS - CDN Only -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Tailwind CSS Browser Build (Local Fallback) -->
    <script src="{{ asset('js/tailwind-browser.js') }}" type="module"></script>
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        /* Apple Design System Variables */
        :root {
            --apple-blue: #007AFF;
            --apple-blue-dark: #0051D5;
            --apple-green: #34C759;
            --apple-orange: #FF9500;
            --apple-red: #FF3B30;
            --apple-purple: #AF52DE;
            --apple-pink: #FF2D55;
            --apple-teal: #5AC8FA;
            --apple-indigo: #5856D6;
            
            /* Dark Mode Colors */
            --dark-bg: #000000;
            --dark-bg-secondary: #1C1C1E;
            --dark-bg-tertiary: #2C2C2E;
            --dark-bg-elevated: rgba(28, 28, 30, 0.9);
            --dark-separator: rgba(84, 84, 88, 0.35);
            --dark-text-primary: #FFFFFF;
            --dark-text-secondary: rgba(235, 235, 245, 0.6);
            --dark-text-tertiary: rgba(235, 235, 245, 0.3);
        }

        * {
            box-sizing: border-box;
        }

        html, body {
            margin: 0;
            padding: 0;
            width: 100%;
            height: 100%;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background-color: var(--dark-bg);
            color: var(--dark-text-primary);
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            overflow: hidden;
        }
        

        
        /* Ensure all text is visible in dark mode */
        h1, h2, h3, h4, h5, h6, p, span, div, label, a, td, th {
            color: var(--dark-text-primary);
        }
        
        small, .text-muted {
            color: var(--dark-text-secondary) !important;
        }

        /* Custom Tailwind Extensions */
        .rounded-apple { border-radius: 10px; }
        .rounded-apple-lg { border-radius: 12px; }
        .rounded-apple-xl { border-radius: 16px; }
        
        .card-elevated {
            background-color: var(--dark-bg-elevated);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid var(--dark-separator);
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.48);
        }
        
        .text-dark-text-primary { color: var(--dark-text-primary); }
        .text-dark-text-secondary { color: var(--dark-text-secondary); }
        .text-dark-text-tertiary { color: var(--dark-text-tertiary); }
        
        .bg-apple-blue { background-color: var(--apple-blue); }
        .bg-apple-green { background-color: var(--apple-green); }
        .bg-apple-orange { background-color: var(--apple-orange); }
        .bg-apple-red { background-color: var(--apple-red); }
        .bg-apple-purple { background-color: var(--apple-purple); }
        
        .text-apple-blue { color: var(--apple-blue); }
        .text-apple-blue-dark { color: var(--apple-blue-dark); }
        .text-apple-green { color: var(--apple-green); }
        .text-apple-orange { color: var(--apple-orange); }
        .text-apple-red { color: var(--apple-red); }
        
        .border-apple-red { border-color: var(--apple-red); }
        .border-apple-green { border-color: var(--apple-green); }
        .border-apple-blue { border-color: var(--apple-blue); }
        
        .btn-primary {
            background: linear-gradient(135deg, var(--apple-blue) 0%, var(--apple-blue-dark) 100%);
            border: none;
            box-shadow: 0 4px 12px rgba(0, 122, 255, 0.4);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0, 122, 255, 0.6);
        }
        
        .hover-lift {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .hover-lift:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.64);
        }
        
        .transition-apple {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* Navbar Styling */
        .navbar {
            background-color: var(--dark-bg-elevated);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-bottom: 1px solid var(--dark-separator);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.48);
        }
        
        .navbar-brand, .nav-link {
            color: var(--dark-text-primary) !important;
        }
        
        .dropdown-menu {
            background-color: var(--dark-bg-tertiary);
            border: 1px solid var(--dark-separator);
            border-radius: 10px;
        }
        
        .dropdown-item {
            color: var(--dark-text-primary);
        }
        
        .dropdown-item:hover {
            background-color: var(--dark-bg-secondary);
            color: var(--apple-blue);
        }
        
        /* Fix Bootstrap vs Tailwind Conflicts */
        button, .btn {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            font-weight: 500;
        }
        
        /* Ensure text is visible and properly styled */
        p, span, div, label, a {
            color: inherit;
        }
        
        /* Button fixes */
        .btn {
            padding: 0.5rem 1rem;
            border-radius: 8px;
            font-size: 0.875rem;
            line-height: 1.5;
        }
        
        .btn-sm, .btn-primary-sm, .btn-secondary-sm {
            padding: 0.375rem 0.75rem;
            font-size: 0.8125rem;
            border-radius: 6px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .btn-primary-sm {
            background: linear-gradient(135deg, var(--apple-blue) 0%, var(--apple-blue-dark) 100%);
            color: #FFFFFF;
            border: none;
            box-shadow: 0 2px 8px rgba(0, 122, 255, 0.3);
        }
        
        .btn-primary-sm:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0, 122, 255, 0.5);
        }
        
        .btn-secondary-sm {
            background: rgba(142, 142, 147, 0.3);
            color: rgba(235, 235, 245, 0.9);
            border: 1px solid rgba(142, 142, 147, 0.5);
        }
        
        .btn-secondary-sm:hover {
            background: rgba(142, 142, 147, 0.4);
            border-color: rgba(142, 142, 147, 0.7);
        }
        
        .btn-lg {
            padding: 0.75rem 1.5rem;
            font-size: 1rem;
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 12px;
        }
        
        ::-webkit-scrollbar-track {
            background: var(--dark-bg-secondary);
        }
        
        ::-webkit-scrollbar-thumb {
            background: var(--dark-separator);
            border-radius: 6px;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: rgba(84, 84, 88, 0.85);
        }

        /* Form Elements - Dark Mode */
        input[type="text"],
        input[type="email"],
        input[type="password"],
        input[type="number"],
        input[type="date"],
        input[type="time"],
        input[type="datetime-local"],
        input[type="search"],
        input[type="tel"],
        input[type="url"],
        textarea,
        select {
            background-color: var(--dark-bg-tertiary) !important;
            border: 1px solid rgba(84, 84, 88, 0.25) !important;
            color: var(--dark-text-primary) !important;
            border-radius: 10px !important;
            padding: 0.5rem 0.75rem !important;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
        }

        input[type="text"]:focus,
        input[type="email"]:focus,
        input[type="password"]:focus,
        input[type="number"]:focus,
        input[type="date"]:focus,
        input[type="time"]:focus,
        input[type="datetime-local"]:focus,
        input[type="search"]:focus,
        input[type="tel"]:focus,
        input[type="url"]:focus,
        textarea:focus,
        select:focus {
            outline: none !important;
            border-color: var(--apple-blue) !important;
            box-shadow: 0 0 0 3px rgba(0, 122, 255, 0.1) !important;
            background-color: var(--dark-bg-secondary) !important;
        }

        input::placeholder,
        textarea::placeholder {
            color: var(--dark-text-tertiary) !important;
        }

        /* Select Dropdown */
        select {
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='rgba(235, 235, 245, 0.6)'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E") !important;
            background-repeat: no-repeat !important;
            background-position: right 0.5rem center !important;
            background-size: 1.5em 1.5em !important;
            padding-right: 2.5rem !important;
            appearance: none !important;
            -webkit-appearance: none !important;
            -moz-appearance: none !important;
        }

        /* Option in Select */
        select option {
            background-color: var(--dark-bg-tertiary);
            color: var(--dark-text-primary);
        }

        /* Checkbox and Radio */
        input[type="checkbox"],
        input[type="radio"] {
            width: 1.25rem !important;
            height: 1.25rem !important;
            background-color: var(--dark-bg-tertiary) !important;
            border: 2px solid var(--dark-separator) !important;
            border-radius: 4px !important;
            cursor: pointer !important;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
        }

        input[type="radio"] {
            border-radius: 50% !important;
        }

        input[type="checkbox"]:checked,
        input[type="radio"]:checked {
            background-color: var(--apple-blue) !important;
            border-color: var(--apple-blue) !important;
        }

        /* Labels */
        label {
            color: var(--dark-text-secondary) !important;
            font-size: 0.875rem !important;
            font-weight: 500 !important;
            margin-bottom: 0.5rem !important;
        }

        /* File Input */
        input[type="file"] {
            color: var(--dark-text-secondary) !important;
        }

        input[type="file"]::file-selector-button {
            background-color: var(--dark-bg-tertiary);
            border: 1px solid var(--dark-separator);
            color: var(--dark-text-primary);
            padding: 0.5rem 1rem;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-right: 1rem;
        }

        input[type="file"]::file-selector-button:hover {
            background-color: var(--dark-bg-secondary);
            border-color: var(--apple-blue);
        }

        /* Buttons */
        button:not(.btn-primary):not(.navbar-toggler) {
            background-color: var(--dark-bg-tertiary);
            border: 1px solid var(--dark-separator);
            color: var(--dark-text-primary);
            border-radius: 10px;
            padding: 0.5rem 1rem;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        button:not(.btn-primary):not(.navbar-toggler):hover {
            background-color: var(--dark-bg-secondary);
            border-color: var(--apple-blue);
            color: var(--dark-text-primary);
        }

        /* Table */
        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }

        thead {
            background-color: var(--dark-bg-secondary);
        }

        thead th {
            padding: 0.75rem 1rem;
            text-align: left;
            font-size: 0.75rem;
            font-weight: 600;
            color: var(--dark-text-secondary);
            text-transform: uppercase;
            letter-spacing: 0.05em;
            border-bottom: 1px solid var(--dark-separator);
        }

        tbody td {
            padding: 0.75rem 1rem;
            border-bottom: 1px solid var(--dark-separator);
            color: var(--dark-text-primary);
        }

        tbody tr {
            transition: background-color 0.2s ease;
        }

        tbody tr:hover {
            background-color: var(--dark-bg-secondary);
        }

        /* Badges */
        .badge {
            padding: 0.25rem 0.75rem;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
        }

        /* Alert/Messages */
        .alert {
            border-radius: 10px;
            border: 1px solid var(--dark-separator);
            padding: 1rem;
            margin-bottom: 1rem;
        }

        .alert-success {
            background-color: rgba(52, 199, 89, 0.1);
            border-color: rgba(52, 199, 89, 0.3);
            color: var(--apple-green);
        }

        .alert-danger {
            background-color: rgba(255, 59, 48, 0.1);
            border-color: rgba(255, 59, 48, 0.3);
            color: var(--apple-red);
        }

        .alert-warning {
            background-color: rgba(255, 149, 0, 0.1);
            border-color: rgba(255, 149, 0, 0.3);
            color: var(--apple-orange);
        }

        .alert-info {
            background-color: rgba(0, 122, 255, 0.1);
            border-color: rgba(0, 122, 255, 0.3);
            color: var(--apple-blue);
        }

        /* Modal/Dialog */
        .modal-content {
            background-color: var(--dark-bg-elevated);
            border: 1px solid var(--dark-separator);
            border-radius: 12px;
        }

        .modal-header,
        .modal-footer {
            border-color: var(--dark-separator);
        }

        .modal-title {
            color: var(--dark-text-primary);
        }

        /* Disabled State */
        input:disabled,
        textarea:disabled,
        select:disabled,
        button:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        /* Focus Visible */
        *:focus-visible {
            outline: 2px solid var(--apple-blue);
            outline-offset: 2px;
        }

        /* Layout Structure */
        .app-shell {
            display: grid !important;
            grid-template-columns: 256px 1fr !important;
            min-height: 100vh !important;
            width: 100% !important;
            background-color: var(--dark-bg) !important;
        }

        .app-sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: 256px;
            height: 100vh;
            background-color: var(--dark-bg-secondary);
            border-right: 1px solid var(--dark-separator);
            z-index: 40;
            display: flex;
            flex-direction: column;
        }

        .sidebar-header {
            padding: 1.25rem 1rem;
            border-bottom: 1px solid var(--dark-separator);
            flex-shrink: 0;
        }

        .sidebar-nav {
            flex: 1;
            overflow-y: auto;
            overflow-x: hidden;
            padding: 1rem;
        }

        .sidebar-nav::-webkit-scrollbar {
            width: 6px;
        }

        .sidebar-nav::-webkit-scrollbar-track {
            background: transparent;
        }

        .sidebar-nav::-webkit-scrollbar-thumb {
            background: var(--dark-separator);
            border-radius: 10px;
        }

        .sidebar-nav::-webkit-scrollbar-thumb:hover {
            background: var(--dark-text-tertiary);
        }

        .sidebar-footer {
            padding: 1rem;
            border-top: 1px solid var(--dark-separator);
            flex-shrink: 0;
        }

        .app-shell {
            position: relative;
            width: 100%;
            height: 100vh;
            overflow: hidden;
            background-color: var(--dark-bg);
        }

        .app-main {
            position: absolute;
            left: 256px;
            top: 0;
            right: 0;
            bottom: 0;
            display: flex;
            flex-direction: column;
            background-color: var(--dark-bg);
            overflow: hidden;
        }

        .app-topbar {
            height: 4rem;
            min-height: 4rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 1.5rem;
            background-color: var(--dark-bg-elevated);
            border-bottom: 1px solid var(--dark-separator);
            flex-shrink: 0;
        }

        .app-content {
            flex: 1;
            overflow-y: auto;
            overflow-x: hidden;
            padding: 1.5rem;
            background-color: var(--dark-bg);
            height: 100%;
        }

        .app-content::-webkit-scrollbar {
            width: 8px;
        }

        .app-content::-webkit-scrollbar-track {
            background: var(--dark-bg);
        }

        .app-content::-webkit-scrollbar-thumb {
            background: var(--dark-separator);
            border-radius: 10px;
        }

        .app-content::-webkit-scrollbar-thumb:hover {
            background: var(--dark-text-tertiary);
        }
        
        /* Navigation Links */
        .nav-section {
            margin-bottom: 1.5rem;
        }

        .nav-section-title {
            padding: 0.5rem 0.75rem;
            font-size: 0.75rem;
            font-weight: 600;
            color: var(--dark-text-tertiary);
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 0.5rem;
        }

        .nav-links {
            display: flex;
            flex-direction: column;
            gap: 0.25rem;
        }

        .nav-link {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0.5rem 0.75rem;
            border-radius: 10px;
            font-size: 0.875rem;
            font-weight: 500;
            color: var(--dark-text-secondary);
            text-decoration: none;
            transition: all 0.2s ease;
        }

        .nav-link:hover {
            background-color: var(--dark-bg-tertiary);
            color: var(--dark-text-primary);
        }

        .nav-link.active {
            background-color: var(--apple-blue);
            color: #FFFFFF;
        }

        .nav-link-content {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .nav-link i {
            width: 1.25rem;
            text-align: center;
            font-size: 0.875rem;
        }

        .nav-badge {
            padding: 0.125rem 0.5rem;
            font-size: 0.75rem;
            font-weight: 600;
            border-radius: 9999px;
        }

        .nav-link.active .nav-badge {
            background-color: #FFFFFF;
            color: var(--apple-blue);
        }

        .nav-link:not(.active) .nav-badge {
            background-color: var(--dark-bg-tertiary);
            color: var(--dark-text-secondary);
        }

        .nav-link:not(.active) .nav-badge.badge-alert {
            background-color: #EF4444;
            color: #FFFFFF;
        }

        .nav-link:not(.active) .nav-badge.badge-warning {
            background-color: #F59E0B;
            color: #FFFFFF;
        }
    </style>
    
    @stack('styles')
</head>
<body>
    @auth
    <!-- Main Layout with Sidebar -->
    @php
        $navCounts = Cache::get('bizmark-perizinan-cache-nav_counts', [
            'projects' => \App\Models\Project::count(),
            'active_projects' => \App\Models\Project::whereHas('status', function($q) {
                $q->whereIn('code', ['KONTRAK', 'PENGUMPULAN_DOK', 'PROSES_DLH', 'PROSES_BPN', 'PROSES_OSS', 'PROSES_NOTARIS', 'MENUNGGU_PERSETUJUAN']);
            })->count(),
            'tasks' => \App\Models\Task::count(),
            'pending_tasks' => \App\Models\Task::where('status', 'pending')->count(),
            'documents' => \App\Models\Document::count(),
            'institutions' => \App\Models\Institution::count(),
            'clients' => \App\Models\Client::count(),
            'permit_types' => \App\Models\PermitType::count(),
            'permit_templates' => \App\Models\PermitTemplate::count(),
        ]);
    @endphp
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
                    </div>
                </div>

                <!-- Permit Management -->
                <div class="nav-section">
                    <div class="nav-section-title">Perizinan</div>
                    <div class="nav-links">
                        <a href="{{ route('admin.permits.index') }}" class="nav-link {{ request()->routeIs('admin.permits.*') || request()->routeIs('admin.permit-dashboard') || request()->routeIs('admin.permit-applications.*') || request()->routeIs('permit-types.*') || request()->routeIs('admin.payments.*') ? 'active' : '' }}">
                            <div class="nav-link-content">
                                <i class="fas fa-file-certificate"></i>
                                <span>Kelola Perizinan</span>
                            </div>
                            @php
                                $submittedCount = \App\Models\PermitApplication::where('status', 'submitted')->count();
                                $underReviewCount = \App\Models\PermitApplication::where('status', 'under_review')->count();
                                $unreadClientNotes = \App\Models\ApplicationNote::where('author_type', 'client')->where('is_read', false)->count();
                                $pendingPayments = \App\Models\Payment::where('payment_method', 'manual')->where('status', 'processing')->count();
                                $totalNotifications = $submittedCount + $underReviewCount + $unreadClientNotes + $pendingPayments;
                            @endphp
                            @if($totalNotifications > 0)
                                <span class="nav-badge badge-alert">{{ $totalNotifications }}</span>
                            @endif
                        </a>
                    </div>
                </div>

                <!-- Recruitment -->
                <div class="nav-section">
                    <div class="nav-section-title">Rekrutmen</div>
                    <div class="nav-links">
                        <a href="{{ route('admin.recruitment.index') }}" class="nav-link {{ request()->routeIs('admin.recruitment.*') || request()->routeIs('admin.jobs.*') || request()->routeIs('admin.applications.*') ? 'active' : '' }}">
                            <div class="nav-link-content">
                                <i class="fas fa-user-tie"></i>
                                <span>Kelola Rekrutmen</span>
                            </div>
                            @php
                                $pendingJobApps = \App\Models\JobApplication::where('status', 'pending')->count();
                            @endphp
                            @if($pendingJobApps > 0)
                                <span class="nav-badge badge-alert">{{ $pendingJobApps }}</span>
                            @endif
                        </a>
                    </div>
                </div>

                <!-- Email Management -->
                <div class="nav-section">
                    <div class="nav-section-title">Email</div>
                    <div class="nav-links">
                        <a href="{{ route('admin.email-management.index') }}" class="nav-link {{ request()->routeIs('admin.email-management.*') || request()->routeIs('admin.inbox.*') || request()->routeIs('admin.campaigns.*') || request()->routeIs('admin.subscribers.*') || request()->routeIs('admin.templates.*') || request()->routeIs('admin.email.settings.*') || request()->routeIs('admin.email-accounts.*') ? 'active' : '' }}">
                            <div class="nav-link-content">
                                <i class="fas fa-envelope"></i>
                                <span>Kelola Email</span>
                            </div>
                            @php
                                $unreadCount = \App\Models\EmailInbox::where('category', 'inbox')->where('is_read', false)->count();
                            @endphp
                            @if($unreadCount > 0)
                                <span class="nav-badge badge-alert">{{ $unreadCount }}</span>
                            @endif
                        </a>
                    </div>
                </div>

                <!-- Master Data -->
                <div class="nav-section">
                    <div class="nav-section-title">Data Master</div>
                    <div class="nav-links">
                        <a href="{{ route('admin.master-data.index') }}" class="nav-link {{ request()->routeIs('admin.master-data.*') || request()->routeIs('cash-accounts.*') || request()->routeIs('admin.settings.kbli.*') || request()->routeIs('reconciliations.*') ? 'active' : '' }}">
                            <div class="nav-link-content">
                                <i class="fas fa-database"></i>
                                <span>Master Data</span>
                            </div>
                            @php
                                $pendingReconciliations = \App\Models\BankReconciliation::where('status', 'pending')->count();
                            @endphp
                            @if($pendingReconciliations > 0)
                                <span class="nav-badge badge-warning">{{ $pendingReconciliations }}</span>
                            @endif
                        </a>
                    </div>
                </div>

                <!-- Content & Media -->
                <div class="nav-section">
                    <div class="nav-section-title">Konten</div>
                    <div class="nav-links">
                        <a href="{{ route('articles.index') }}" class="nav-link {{ request()->routeIs('articles.*') ? 'active' : '' }}">
                            <div class="nav-link-content">
                                <i class="fas fa-newspaper"></i>
                                <span>Artikel & Berita</span>
                            </div>
                        </a>
                    </div>
                </div>
            </nav>

            <!-- User Info Footer -->
            <div class="sidebar-footer">
                <div style="display: flex; align-items: center; gap: 0.75rem;">
                    <div style="width: 2.5rem; height: 2.5rem; border-radius: 50%; background: var(--apple-blue); display: flex; align-items: center; justify-content: center; color: #FFFFFF; font-weight: 600; font-size: 0.875rem; flex-shrink: 0;">
                        {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                    </div>
                    <div style="flex: 1; min-width: 0;">
                        <p style="font-size: 0.875rem; font-weight: 500; color: var(--dark-text-primary); margin: 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ Auth::user()->name }}</p>
                        <p style="font-size: 0.75rem; color: var(--dark-text-secondary); margin: 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ Auth::user()->email }}</p>
                    </div>
                    <form method="POST" action="{{ route('logout') }}" style="margin: 0;">
                        @csrf
                        <button type="submit" style="padding: 0.5rem; color: var(--dark-text-secondary); background: transparent; border: none; cursor: pointer; border-radius: 8px; transition: all 0.2s;" onmouseover="this.style.color='#EF4444'" onmouseout="this.style.color='var(--dark-text-secondary)'">
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
                    <button style="padding: 0.5rem; border-radius: 10px; color: var(--dark-text-secondary); background: transparent; border: none; cursor: pointer;">
                        <i class="fas fa-bell"></i>
                    </button>
                    <button style="padding: 0.5rem; border-radius: 10px; color: var(--dark-text-secondary); background: transparent; border: none; cursor: pointer;">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </header>

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
    
    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
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
    </script>
    
    @stack('scripts')
</body>
</html>
