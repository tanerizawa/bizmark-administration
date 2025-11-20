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

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background-color: var(--dark-bg);
            color: var(--dark-text-primary);
            min-height: 100vh;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            margin: 0 !important;
            padding: 0 !important;
            overflow-x: hidden !important;
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
            background-color: var(--dark-bg-secondary) !important;
            border-right: 1px solid var(--dark-separator) !important;
            height: 100vh !important;
            position: sticky !important;
            top: 0 !important;
            overflow-y: auto !important;
            overflow-x: hidden !important;
            z-index: 10 !important;
            grid-column: 1 !important;
        }

        .app-sidebar .sidebar-inner {
            height: 100% !important;
            display: flex !important;
            flex-direction: column !important;
        }

        .app-main {
            display: flex !important;
            flex-direction: column !important;
            min-width: 0 !important;
            width: 100% !important;
            min-height: 100vh !important;
            background-color: var(--dark-bg) !important;
            grid-column: 2 !important;
        }

        .app-topbar {
            height: 4rem !important;
            min-height: 4rem !important;
            display: flex !important;
            align-items: center !important;
            justify-content: space-between !important;
            padding: 0 1.5rem !important;
            background-color: var(--dark-bg-elevated) !important;
            border-bottom: 1px solid var(--dark-separator) !important;
            flex-shrink: 0 !important;
        }

        .app-content {
            flex: 1 1 auto !important;
            overflow-y: auto !important;
            overflow-x: hidden !important;
            padding: 1.5rem;
            background-color: var(--dark-bg);
        }
        
        /* Content Container Fallback - Tailwind classes replacement */
        .app-content > div[class*="max-w"],
        .app-content > div:first-child {
            max-width: 80rem !important;
            margin-left: auto !important;
            margin-right: auto !important;
        }
        
        .app-content > div[class*="space-y"] {
            display: flex !important;
            flex-direction: column !important;
            gap: 2.5rem !important;
        }
        
        /* Essential Layout Fix Only */
        .app-content > div:first-child {
            max-width: 80rem;
            margin: 0 auto;
        }
        
        /* Navigation Links Critical Fallback - Prevent Layout Breaking */
        .app-sidebar nav > div {
            display: flex !important;
            flex-direction: column !important;
            gap: 0.25rem !important;
        }
        
        .app-sidebar nav a {
            display: flex !important;
            align-items: center !important;
            justify-content: space-between !important;
            padding: 0.5rem 0.75rem !important;
            border-radius: 10px !important;
            font-size: 0.875rem !important;
            font-weight: 500 !important;
            transition: all 0.3s ease !important;
            text-decoration: none !important;
        }
        
        .app-sidebar nav a > div {
            display: flex !important;
            align-items: center !important;
        }
        
        .app-sidebar nav a i {
            width: 1.25rem !important;
            text-align: center !important;
        }
        
        .app-sidebar nav a span {
            margin-left: 0.75rem !important;
        }
        
        .app-sidebar nav a span[class*="px-2"],
        .app-sidebar nav a > span:last-child {
            margin-left: 0 !important;
            padding: 0.125rem 0.5rem !important;
            font-size: 0.75rem !important;
            font-weight: 600 !important;
            border-radius: 9999px !important;
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
    <div class="app-shell" style="display: grid !important; grid-template-columns: 256px 1fr !important; min-height: 100vh !important; width: 100% !important; overflow: hidden !important; background-color: var(--dark-bg) !important;">
        <!-- Sidebar -->
        <aside class="app-sidebar" style="background-color: var(--dark-bg-secondary) !important; border-right: 1px solid var(--dark-separator) !important; height: 100% !important; position: relative !important; z-index: 10 !important;">
            <div class="sidebar-inner" style="height: 100%; display: flex; flex-direction: column;">
                <!-- Logo -->
                <div style="padding: 1rem; border-bottom: 1px solid var(--dark-separator);">
                    <h1 style="font-size: 1.25rem; font-weight: 700; color: var(--dark-text-primary);">
                        <i class="fas fa-shield-alt" style="color: var(--apple-blue); margin-right: 0.5rem;"></i>
                        Bizmark.ID
                    </h1>
                    <p style="font-size: 0.75rem; color: var(--dark-text-secondary); margin-top: 0.25rem;">Permit Management System</p>
                </div>

                <!-- Navigation -->
                <nav style="flex: 1 1 auto; padding: 1rem; overflow-y: auto;">
                    <div style="display: flex; flex-direction: column; gap: 0.25rem;">
                        <a href="{{ route('dashboard') }}" style="display: flex; align-items: center; justify-content: space-between; padding: 0.5rem 0.75rem; border-radius: 10px; font-size: 0.875rem; font-weight: 500; transition: all 0.3s; {{ request()->routeIs('dashboard') ? 'background: var(--apple-blue); color: #FFFFFF;' : 'color: var(--dark-text-secondary);' }}">
                            <div style="display: flex; align-items: center;">
                                <i class="fas fa-home" style="width: 1.25rem;"></i>
                                <span style="margin-left: 0.75rem;">Dashboard</span>
                            </div>
                        </a>
                        
                        <a href="{{ route('projects.index') }}" style="display: flex; align-items: center; justify-content: space-between; padding: 0.5rem 0.75rem; border-radius: 10px; font-size: 0.875rem; font-weight: 500; transition: all 0.3s; {{ request()->routeIs('projects.*') ? 'background: var(--apple-blue); color: #FFFFFF;' : 'color: var(--dark-text-secondary);' }}">
                            <div style="display: flex; align-items: center;">
                                <i class="fas fa-project-diagram" style="width: 1.25rem;"></i>
                                <span style="margin-left: 0.75rem;">Proyek</span>
                            </div>
                            @if(isset($navCounts['projects']) && $navCounts['projects'] > 0)
                                <span style="padding: 0.125rem 0.5rem; font-size: 0.75rem; font-weight: 600; border-radius: 9999px; {{ request()->routeIs('projects.*') ? 'background: #FFFFFF; color: var(--apple-blue);' : 'background: var(--dark-bg-tertiary); color: var(--dark-text-secondary);' }}">
                                    {{ $navCounts['projects'] }}
                                </span>
                            @endif
                        </a>
                        
                        <a href="{{ route('tasks.index') }}" class="flex items-center justify-between px-3 py-2 rounded-apple text-sm font-medium transition-apple {{ request()->routeIs('tasks.*') ? 'bg-apple-blue text-white' : 'text-dark-text-secondary hover:bg-dark-bg-tertiary hover:text-dark-text-primary' }}">
                            <div class="flex items-center">
                                <i class="fas fa-tasks w-5"></i>
                                <span class="ml-3">Tugas</span>
                            </div>
                            @if(isset($navCounts['pending_tasks']) && $navCounts['pending_tasks'] > 0)
                                <span class="px-2 py-0.5 text-xs font-semibold rounded-full {{ request()->routeIs('tasks.*') ? 'bg-white text-apple-blue' : 'bg-apple-orange text-white' }}">
                                    {{ $navCounts['pending_tasks'] }}
                                </span>
                            @endif
                        </a>
                        
                        <a href="{{ route('documents.index') }}" class="flex items-center justify-between px-3 py-2 rounded-apple text-sm font-medium transition-apple {{ request()->routeIs('documents.*') ? 'bg-apple-blue text-white' : 'text-dark-text-secondary hover:bg-dark-bg-tertiary hover:text-dark-text-primary' }}">
                            <div class="flex items-center">
                                <i class="fas fa-file-alt w-5"></i>
                                <span class="ml-3">Dokumen</span>
                            </div>
                            @if(isset($navCounts['documents']) && $navCounts['documents'] > 0)
                                <span class="px-2 py-0.5 text-xs font-semibold rounded-full {{ request()->routeIs('documents.*') ? 'bg-white text-apple-blue' : 'bg-dark-bg-tertiary text-dark-text-secondary' }}">
                                    {{ $navCounts['documents'] }}
                                </span>
                            @endif
                        </a>
                        
                        <a href="{{ route('institutions.index') }}" class="flex items-center justify-between px-3 py-2 rounded-apple text-sm font-medium transition-apple {{ request()->routeIs('institutions.*') ? 'bg-apple-blue text-white' : 'text-dark-text-secondary hover:bg-dark-bg-tertiary hover:text-dark-text-primary' }}">
                            <div class="flex items-center">
                                <i class="fas fa-building w-5"></i>
                                <span class="ml-3">Instansi</span>
                            </div>
                            @if(isset($navCounts['institutions']) && $navCounts['institutions'] > 0)
                                <span class="px-2 py-0.5 text-xs font-semibold rounded-full {{ request()->routeIs('institutions.*') ? 'bg-white text-apple-blue' : 'bg-dark-bg-tertiary text-dark-text-secondary' }}">
                                    {{ $navCounts['institutions'] }}
                                </span>
                            @endif
                        </a>
                        
                        <a href="{{ route('clients.index') }}" class="flex items-center justify-between px-3 py-2 rounded-apple text-sm font-medium transition-apple {{ request()->routeIs('clients.*') ? 'bg-apple-blue text-white' : 'text-dark-text-secondary hover:bg-dark-bg-tertiary hover:text-dark-text-primary' }}">
                            <div class="flex items-center">
                                <i class="fas fa-users w-5"></i>
                                <span class="ml-3">Klien</span>
                            </div>
                            @if(isset($navCounts['clients']) && $navCounts['clients'] > 0)
                                <span class="px-2 py-0.5 text-xs font-semibold rounded-full {{ request()->routeIs('clients.*') ? 'bg-white text-apple-blue' : 'bg-dark-bg-tertiary text-dark-text-secondary' }}">
                                    {{ $navCounts['clients'] }}
                                </span>
                            @endif
                        </a>
                        
                        <a href="{{ route('settings.index') }}" class="flex items-center justify-between px-3 py-2 rounded-apple text-sm font-medium transition-apple {{ request()->routeIs('settings.*') ? 'bg-apple-blue text-white' : 'text-dark-text-secondary hover:bg-dark-bg-tertiary hover:text-dark-text-primary' }}">
                            <div class="flex items-center">
                                <i class="fas fa-cog w-5"></i>
                                <span class="ml-3">Pengaturan</span>
                            </div>
                        </a>
                        
                        <div class="pt-4 mt-4" style="border-top: 1px solid var(--dark-separator);">
                            <p class="px-3 text-xs font-semibold text-dark-text-tertiary uppercase tracking-wider mb-2">Permit Management</p>
                            
                            <a href="{{ route('admin.permit-dashboard') }}" class="flex items-center justify-between px-3 py-2 rounded-apple text-sm font-medium transition-apple {{ request()->routeIs('admin.permit-dashboard') ? 'bg-apple-blue text-white' : 'text-dark-text-secondary hover:bg-dark-bg-tertiary hover:text-dark-text-primary' }}">
                                <div class="flex items-center">
                                    <i class="fas fa-chart-pie w-5"></i>
                                    <span class="ml-3">Dashboard Permit</span>
                                </div>
                            </a>
                            
                            <a href="{{ route('admin.permit-applications.index') }}" class="flex items-center justify-between px-3 py-2 rounded-apple text-sm font-medium transition-apple {{ request()->routeIs('admin.permit-applications.*') ? 'bg-apple-blue text-white' : 'text-dark-text-secondary hover:bg-dark-bg-tertiary hover:text-dark-text-primary' }}">
                                <div class="flex items-center">
                                    <i class="fas fa-file-signature w-5"></i>
                                    <span class="ml-3">Permohonan Izin</span>
                                </div>
                                @php
                                    $submittedCount = \App\Models\PermitApplication::where('status', 'submitted')->count();
                                    $underReviewCount = \App\Models\PermitApplication::where('status', 'under_review')->count();
                                    $totalPending = $submittedCount + $underReviewCount;
                                    
                                    // Count unread client notes
                                    $unreadClientNotes = \App\Models\ApplicationNote::where('author_type', 'client')
                                        ->where('is_read', false)
                                        ->count();
                                @endphp
                                @if($totalPending > 0 || $unreadClientNotes > 0)
                                    <div class="flex items-center gap-1">
                                        @if($totalPending > 0)
                                            <span class="px-2 py-0.5 text-xs font-semibold rounded-full {{ request()->routeIs('admin.permit-applications.*') ? 'bg-white text-apple-blue' : 'bg-yellow-500 text-white' }}">
                                                {{ $totalPending }}
                                            </span>
                                        @endif
                                        @if($unreadClientNotes > 0)
                                            <span class="px-2 py-0.5 text-xs font-semibold rounded-full {{ request()->routeIs('admin.permit-applications.*') ? 'bg-white text-apple-blue' : 'bg-blue-500 text-white' }}" title="Pesan baru dari klien">
                                                <i class="fas fa-comment text-[10px]"></i> {{ $unreadClientNotes }}
                                            </span>
                                        @endif
                                    </div>
                                @endif
                            </a>
                            
                            <a href="{{ route('permit-types.index') }}" class="flex items-center justify-between px-3 py-2 rounded-apple text-sm font-medium transition-apple {{ request()->routeIs('permit-types.*') ? 'bg-apple-blue text-white' : 'text-dark-text-secondary hover:bg-dark-bg-tertiary hover:text-dark-text-primary' }}">
                                <div class="flex items-center">
                                    <i class="fas fa-certificate w-5"></i>
                                    <span class="ml-3">Jenis Izin</span>
                                </div>
                            </a>
                            
                            <a href="{{ route('admin.payments.index') }}" class="flex items-center justify-between px-3 py-2 rounded-apple text-sm font-medium transition-apple {{ request()->routeIs('admin.payments.*') ? 'bg-apple-blue text-white' : 'text-dark-text-secondary hover:bg-dark-bg-tertiary hover:text-dark-text-primary' }}">
                                <div class="flex items-center">
                                    <i class="fas fa-money-check-alt w-5"></i>
                                    <span class="ml-3">Verifikasi Pembayaran</span>
                                </div>
                                @php
                                    $pendingPayments = \App\Models\Payment::where('payment_method', 'manual')->where('status', 'processing')->count();
                                @endphp
                                @if($pendingPayments > 0)
                                    <span class="px-2 py-0.5 text-xs font-semibold rounded-full {{ request()->routeIs('admin.payments.*') ? 'bg-white text-apple-blue' : 'bg-green-500 text-white' }}">
                                        {{ $pendingPayments }}
                                    </span>
                                @endif
                            </a>
                        </div>
                        
                        <div class="pt-4 mt-4" style="border-top: 1px solid var(--dark-separator);">
                            <p class="px-3 text-xs font-semibold text-dark-text-tertiary uppercase tracking-wider mb-2">Recruitment</p>
                            
                            <a href="{{ route('admin.jobs.index') }}" class="flex items-center justify-between px-3 py-2 rounded-apple text-sm font-medium transition-apple {{ request()->routeIs('admin.jobs.*') ? 'bg-apple-blue text-white' : 'text-dark-text-secondary hover:bg-dark-bg-tertiary hover:text-dark-text-primary' }}">
                                <div class="flex items-center">
                                    <i class="fas fa-briefcase w-5"></i>
                                    <span class="ml-3">Lowongan Kerja</span>
                                </div>
                            </a>
                            
                            <a href="{{ route('admin.applications.index') }}" class="flex items-center justify-between px-3 py-2 rounded-apple text-sm font-medium transition-apple {{ request()->routeIs('admin.applications.*') ? 'bg-apple-blue text-white' : 'text-dark-text-secondary hover:bg-dark-bg-tertiary hover:text-dark-text-primary' }}">
                                <div class="flex items-center">
                                    <i class="fas fa-user-tie w-5"></i>
                                    <span class="ml-3">Lamaran Masuk</span>
                                </div>
                                @php
                                    $pendingJobApps = \App\Models\JobApplication::where('status', 'pending')->count();
                                @endphp
                                @if($pendingJobApps > 0)
                                    <span class="px-2 py-0.5 text-xs font-semibold rounded-full {{ request()->routeIs('admin.applications.*') ? 'bg-white text-apple-blue' : 'bg-red-500 text-white' }}">
                                        {{ $pendingJobApps }}
                                    </span>
                                @endif
                            </a>
                        </div>
                        
                        <div class="pt-4 mt-4" style="border-top: 1px solid var(--dark-separator);">
                            <p class="px-3 text-xs font-semibold text-dark-text-tertiary uppercase tracking-wider mb-2">Email Management</p>
                            
                            <a href="{{ route('admin.inbox.index') }}" class="flex items-center justify-between px-3 py-2 rounded-apple text-sm font-medium transition-apple {{ request()->routeIs('admin.inbox.*') ? 'bg-apple-blue text-white' : 'text-dark-text-secondary hover:bg-dark-bg-tertiary hover:text-dark-text-primary' }}">
                                <div class="flex items-center">
                                    <i class="fas fa-inbox w-5"></i>
                                    <span class="ml-3">Inbox</span>
                                </div>
                                @php
                                    $unreadCount = \App\Models\EmailInbox::where('category', 'inbox')->where('is_read', false)->count();
                                @endphp
                                @if($unreadCount > 0)
                                    <span class="px-2 py-0.5 text-xs font-semibold rounded-full {{ request()->routeIs('admin.inbox.*') ? 'bg-white text-apple-blue' : 'bg-red-500 text-white' }}">
                                        {{ $unreadCount }}
                                    </span>
                                @endif
                            </a>
                            
                            <a href="{{ route('admin.campaigns.index') }}" class="flex items-center justify-between px-3 py-2 rounded-apple text-sm font-medium transition-apple {{ request()->routeIs('admin.campaigns.*') ? 'bg-apple-blue text-white' : 'text-dark-text-secondary hover:bg-dark-bg-tertiary hover:text-dark-text-primary' }}">
                                <div class="flex items-center">
                                    <i class="fas fa-paper-plane w-5"></i>
                                    <span class="ml-3">Campaigns</span>
                                </div>
                            </a>
                            
                            <a href="{{ route('admin.subscribers.index') }}" class="flex items-center justify-between px-3 py-2 rounded-apple text-sm font-medium transition-apple {{ request()->routeIs('admin.subscribers.*') ? 'bg-apple-blue text-white' : 'text-dark-text-secondary hover:bg-dark-bg-tertiary hover:text-dark-text-primary' }}">
                                <div class="flex items-center">
                                    <i class="fas fa-users w-5"></i>
                                    <span class="ml-3">Subscribers</span>
                                </div>
                                <span class="px-2 py-0.5 text-xs font-semibold rounded-full {{ request()->routeIs('admin.subscribers.*') ? 'bg-white text-apple-blue' : 'bg-dark-bg-tertiary text-dark-text-secondary' }}">
                                    {{ \App\Models\EmailSubscriber::where('status', 'active')->count() }}
                                </span>
                            </a>
                            
                            <a href="{{ route('admin.templates.index') }}" class="flex items-center justify-between px-3 py-2 rounded-apple text-sm font-medium transition-apple {{ request()->routeIs('admin.templates.*') ? 'bg-apple-blue text-white' : 'text-dark-text-secondary hover:bg-dark-bg-tertiary hover:text-dark-text-primary' }}">
                                <div class="flex items-center">
                                    <i class="fas fa-file-alt w-5"></i>
                                    <span class="ml-3">Templates</span>
                                </div>
                            </a>
                            
                            <a href="{{ route('admin.email.settings.index') }}" class="flex items-center justify-between px-3 py-2 rounded-apple text-sm font-medium transition-apple {{ request()->routeIs('admin.email.settings.*') ? 'bg-apple-blue text-white' : 'text-dark-text-secondary hover:bg-dark-bg-tertiary hover:text-dark-text-primary' }}">
                                <div class="flex items-center">
                                    <i class="fas fa-cog w-5"></i>
                                    <span class="ml-3">Email Settings</span>
                                </div>
                            </a>
                            
                            <a href="{{ route('admin.email-accounts.index') }}" class="flex items-center justify-between px-3 py-2 rounded-apple text-sm font-medium transition-apple {{ request()->routeIs('admin.email-accounts.*') ? 'bg-apple-blue text-white' : 'text-dark-text-secondary hover:bg-dark-bg-tertiary hover:text-dark-text-primary' }}">
                                <div class="flex items-center">
                                    <i class="fas fa-at w-5"></i>
                                    <span class="ml-3">Email Accounts</span>
                                </div>
                                @php
                                    $activeEmailAccounts = \App\Models\EmailAccount::where('is_active', true)->count();
                                @endphp
                                @if($activeEmailAccounts > 0)
                                    <span class="px-2 py-0.5 text-xs font-semibold rounded-full {{ request()->routeIs('admin.email-accounts.*') ? 'bg-white text-apple-blue' : 'bg-white/20 text-white' }}">
                                        {{ $activeEmailAccounts }}
                                    </span>
                                @endif
                            </a>
                        </div>
                        
                        <div class="pt-4 mt-4" style="border-top: 1px solid var(--dark-separator);">
                            <p class="px-3 text-xs font-semibold text-dark-text-tertiary uppercase tracking-wider mb-2">Master Data</p>
                            
                            <a href="{{ route('permit-types.index') }}" class="flex items-center justify-between px-3 py-2 rounded-apple text-sm font-medium transition-apple {{ request()->routeIs('permit-types.*') ? 'bg-apple-blue text-white' : 'text-dark-text-secondary hover:bg-dark-bg-tertiary hover:text-dark-text-primary' }}">
                                <div class="flex items-center">
                                    <i class="fas fa-certificate w-5"></i>
                                    <span class="ml-3">Jenis Izin</span>
                                </div>
                                @if(isset($navCounts['permit_types']) && $navCounts['permit_types'] > 0)
                                    <span class="px-2 py-0.5 text-xs font-semibold rounded-full {{ request()->routeIs('permit-types.*') ? 'bg-white text-apple-blue' : 'bg-dark-bg-tertiary text-dark-text-secondary' }}">
                                        {{ $navCounts['permit_types'] }}
                                    </span>
                                @endif
                            </a>
                            
                            <a href="{{ route('permit-templates.index') }}" class="flex items-center justify-between px-3 py-2 rounded-apple text-sm font-medium transition-apple {{ request()->routeIs('permit-templates.*') ? 'bg-apple-blue text-white' : 'text-dark-text-secondary hover:bg-dark-bg-tertiary hover:text-dark-text-primary' }}">
                                <div class="flex items-center">
                                    <i class="fas fa-clipboard-list w-5"></i>
                                    <span class="ml-3">Template Izin</span>
                                </div>
                                @if(isset($navCounts['permit_templates']) && $navCounts['permit_templates'] > 0)
                                    <span class="px-2 py-0.5 text-xs font-semibold rounded-full {{ request()->routeIs('permit-templates.*') ? 'bg-white text-apple-blue' : 'bg-dark-bg-tertiary text-dark-text-secondary' }}">
                                        {{ $navCounts['permit_templates'] }}
                                    </span>
                                @endif
                            </a>
                            
                            <a href="{{ route('cash-accounts.index') }}" class="flex items-center justify-between px-3 py-2 rounded-apple text-sm font-medium transition-apple {{ request()->routeIs('cash-accounts.*') ? 'bg-apple-blue text-white' : 'text-dark-text-secondary hover:bg-dark-bg-tertiary hover:text-dark-text-primary' }}">
                                <div class="flex items-center">
                                    <i class="fas fa-wallet w-5"></i>
                                    <span class="ml-3">Akun Kas</span>
                                </div>
                            </a>
                            
                            <a href="{{ route('admin.settings.kbli.index') }}" class="flex items-center justify-between px-3 py-2 rounded-apple text-sm font-medium transition-apple {{ request()->routeIs('admin.settings.kbli.*') ? 'bg-apple-blue text-white' : 'text-dark-text-secondary hover:bg-dark-bg-tertiary hover:text-dark-text-primary' }}">
                                <div class="flex items-center">
                                    <i class="fas fa-industry w-5"></i>
                                    <span class="ml-3">Data KBLI</span>
                                </div>
                                @php
                                    $kbliCount = \App\Models\Kbli::count();
                                @endphp
                                @if($kbliCount > 0)
                                    <span class="px-2 py-0.5 text-xs font-semibold rounded-full {{ request()->routeIs('admin.settings.kbli.*') ? 'bg-white text-apple-blue' : 'bg-dark-bg-tertiary text-dark-text-secondary' }}">
                                        {{ $kbliCount }}
                                    </span>
                                @endif
                            </a>
                            
                            <a href="{{ route('reconciliations.index') }}" class="flex items-center justify-between px-3 py-2 rounded-apple text-sm font-medium transition-apple {{ request()->routeIs('reconciliations.*') ? 'bg-apple-blue text-white' : 'text-dark-text-secondary hover:bg-dark-bg-tertiary hover:text-dark-text-primary' }}">
                                <div class="flex items-center">
                                    <i class="fas fa-sync-alt w-5"></i>
                                    <span class="ml-3">Rekonsiliasi Bank</span>
                                </div>
                            </a>
                        </div>
                        
                        <div class="pt-4 mt-4" style="border-top: 1px solid var(--dark-separator);">
                            <p class="px-3 text-xs font-semibold text-dark-text-tertiary uppercase tracking-wider mb-2">Konten & Media</p>
                            
                            <a href="{{ route('articles.index') }}" class="flex items-center justify-between px-3 py-2 rounded-apple text-sm font-medium transition-apple {{ request()->routeIs('articles.*') ? 'bg-apple-blue text-white' : 'text-dark-text-secondary hover:bg-dark-bg-tertiary hover:text-dark-text-primary' }}">
                                <div class="flex items-center">
                                    <i class="fas fa-newspaper w-5"></i>
                                    <span class="ml-3">Artikel & Berita</span>
                                </div>
                            </a>
                        </div>
                    </div>
                </nav>

                <!-- User Info -->
                <div class="p-4" style="border-top: 1px solid var(--dark-separator);">
                    <div class="flex items-center">
                        <div class="w-10 h-10 rounded-full bg-apple-blue flex items-center justify-center text-white font-semibold">
                            {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                        </div>
                        <div class="ml-3 flex-1">
                            <p class="text-sm font-medium text-dark-text-primary">{{ Auth::user()->name }}</p>
                            <p class="text-xs text-dark-text-secondary">{{ Auth::user()->email }}</p>
                        </div>
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="text-dark-text-secondary hover:text-apple-red transition-apple">
                                <i class="fas fa-sign-out-alt"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="app-main" style="display: flex !important; flex-direction: column !important; min-width: 0 !important; overflow: hidden !important; background-color: var(--dark-bg) !important; grid-column: 2 !important;">
            <!-- Top Bar -->
            <header class="app-topbar" style="height: 4rem; min-height: 4rem; display: flex; align-items: center; justify-content: space-between; padding: 0 1.5rem; background-color: var(--dark-bg-elevated); border-bottom: 1px solid var(--dark-separator); flex-shrink: 0;">
                <div>
                    <h2 style="font-size: 1.125rem; font-weight: 600; color: var(--dark-text-primary); margin: 0;">@yield('page-title', 'Dashboard')</h2>
                    <p style="font-size: 0.75rem; color: var(--dark-text-secondary); margin: 0;">{{ now()->format('l, d F Y') }}</p>
                </div>
                <div style="display: flex; align-items: center; gap: 0.75rem;">
                    <button style="padding: 0.5rem; border-radius: 10px; color: var(--dark-text-secondary); background: transparent; border: none; cursor: pointer;">
                        <i class="fas fa-bell"></i>
                    </button>
                    <button style="padding: 0.5rem; border-radius: 10px; color: var(--dark-text-secondary); background: transparent; border: none; cursor: pointer;">
                        <i class="fas fa-cog"></i>
                    </button>
                </div>
            </header>

            <!-- Content Area -->
            <main class="app-content" style="flex: 1 1 auto; overflow-y: auto; overflow-x: hidden; padding: 1.5rem; background-color: var(--dark-bg);">
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
