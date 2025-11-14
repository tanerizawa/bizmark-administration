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
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {}
            }
        }
    </script>
    
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
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background-color: var(--dark-bg);
            color: var(--dark-text-primary);
            min-height: 100vh;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
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
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        <aside class="w-64 flex-shrink-0" style="background-color: var(--dark-bg-secondary); border-right: 1px solid var(--dark-separator);">
            <div class="h-full flex flex-col">
                <!-- Logo -->
                <div class="p-4" style="border-bottom: 1px solid var(--dark-separator);">
                    <h1 class="text-xl font-bold text-dark-text-primary">
                        <i class="fas fa-shield-alt text-apple-blue mr-2"></i>
                        Bizmark.ID
                    </h1>
                    <p class="text-xs text-dark-text-secondary mt-1">Permit Management System</p>
                </div>

                <!-- Navigation -->
                <nav class="flex-1 p-4 overflow-y-auto">
                    <div class="space-y-1">
                        <a href="{{ route('dashboard') }}" class="flex items-center justify-between px-3 py-2 rounded-apple text-sm font-medium transition-apple {{ request()->routeIs('dashboard') ? 'bg-apple-blue text-white' : 'text-dark-text-secondary hover:bg-dark-bg-tertiary hover:text-dark-text-primary' }}">
                            <div class="flex items-center">
                                <i class="fas fa-home w-5"></i>
                                <span class="ml-3">Dashboard</span>
                            </div>
                        </a>
                        
                        <a href="{{ route('projects.index') }}" class="flex items-center justify-between px-3 py-2 rounded-apple text-sm font-medium transition-apple {{ request()->routeIs('projects.*') ? 'bg-apple-blue text-white' : 'text-dark-text-secondary hover:bg-dark-bg-tertiary hover:text-dark-text-primary' }}">
                            <div class="flex items-center">
                                <i class="fas fa-project-diagram w-5"></i>
                                <span class="ml-3">Proyek</span>
                            </div>
                            @if(isset($navCounts['projects']) && $navCounts['projects'] > 0)
                                <span class="px-2 py-0.5 text-xs font-semibold rounded-full {{ request()->routeIs('projects.*') ? 'bg-white text-apple-blue' : 'bg-dark-bg-tertiary text-dark-text-secondary' }}">
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
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Top Bar -->
            <header class="h-16 flex items-center justify-between px-6" style="background-color: var(--dark-bg-elevated); border-bottom: 1px solid var(--dark-separator);">
                <div>
                    <h2 class="text-lg font-semibold text-dark-text-primary">@yield('page-title', 'Dashboard')</h2>
                    <p class="text-xs text-dark-text-secondary">{{ now()->format('l, d F Y') }}</p>
                </div>
                <div class="flex items-center space-x-3">
                    <button class="p-2 rounded-apple text-dark-text-secondary hover:text-dark-text-primary hover:bg-dark-bg-tertiary transition-apple">
                        <i class="fas fa-bell"></i>
                    </button>
                    <button class="p-2 rounded-apple text-dark-text-secondary hover:text-dark-text-primary hover:bg-dark-bg-tertiary transition-apple">
                        <i class="fas fa-cog"></i>
                    </button>
                </div>
            </header>

            <!-- Content Area -->
            <main class="flex-1 overflow-y-auto p-6">
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
    
    @stack('scripts')
</body>
</html>
