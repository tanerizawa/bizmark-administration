<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Client Portal') - Bizmark.id</title>
    
    <!-- External CSS - CDN Only -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <style>
        [x-cloak] { display: none !important; }
    </style>
    
    @stack('styles')
</head>
<body class="bg-gray-50">
    <div x-data="{ sidebarOpen: false }" class="flex h-screen overflow-hidden">
        
        <!-- Sidebar -->
        <aside 
            :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
            class="fixed inset-y-0 left-0 z-50 w-64 bg-gradient-to-b from-purple-700 to-purple-900 text-white transform transition-transform duration-300 ease-in-out lg:translate-x-0 lg:static"
        >
            <!-- Logo -->
            <div class="flex items-center justify-between h-16 px-6 border-b border-purple-600">
                <h1 class="text-2xl font-bold">Bizmark<span class="text-yellow-300">.id</span></h1>
                <button @click="sidebarOpen = false" class="lg:hidden text-white">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <!-- User Info -->
            <div class="p-6 border-b border-purple-600">
                <div class="flex items-center space-x-3">
                    <div class="w-12 h-12 bg-purple-500 rounded-full flex items-center justify-center">
                        <i class="fas fa-user text-xl"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-semibold truncate">{{ auth('client')->user()->name }}</p>
                        <p class="text-sm text-purple-200 truncate">{{ auth('client')->user()->email }}</p>
                    </div>
                </div>
            </div>

            <!-- Navigation -->
            <nav class="flex-1 px-4 py-6 space-y-1 overflow-y-auto">
                <!-- Main Menu -->
                <a href="{{ route('client.dashboard') }}" 
                   class="flex items-center px-4 py-3 rounded-lg transition {{ request()->routeIs('client.dashboard') ? 'bg-purple-600' : 'hover:bg-purple-600' }}">
                    <i class="fas fa-home w-5"></i>
                    <span class="ml-3">Dashboard</span>
                </a>

                <!-- Divider -->
                <div class="px-4 pt-4 pb-2">
                    <p class="text-xs uppercase tracking-wider text-purple-200 font-semibold">Layanan</p>
                </div>

                <a href="{{ route('client.services.index') }}" 
                   class="flex items-center px-4 py-3 rounded-lg transition {{ request()->routeIs('client.services.*') && !request()->routeIs('client.applications.*') ? 'bg-purple-600' : 'hover:bg-purple-600' }}">
                    <i class="fas fa-layer-group w-5"></i>
                    <span class="ml-3">Katalog Izin</span>
                </a>

                <!-- Divider -->
                <div class="px-4 pt-4 pb-2">
                    <p class="text-xs uppercase tracking-wider text-purple-200 font-semibold">Permohonan & Proyek</p>
                </div>

                <a href="{{ route('client.applications.index') }}" 
                   class="flex items-center px-4 py-3 rounded-lg transition {{ request()->routeIs('client.applications.*') ? 'bg-purple-600' : 'hover:bg-purple-600' }}">
                    <i class="fas fa-file-alt w-5"></i>
                    <span class="ml-3">Permohonan Saya</span>
                    @php
                        $draftCount = \App\Models\PermitApplication::where('client_id', auth('client')->id())
                            ->where('status', 'draft')
                            ->count();
                        $submittedCount = \App\Models\PermitApplication::where('client_id', auth('client')->id())
                            ->whereIn('status', ['submitted', 'under_review', 'document_incomplete'])
                            ->count();
                        
                        // Count unread admin notes
                        $unreadAdminNotes = \App\Models\ApplicationNote::whereHas('application', function($q) {
                                $q->where('client_id', auth('client')->id());
                            })
                            ->where('author_type', 'admin')
                            ->where('is_internal', false)
                            ->where('is_read', false)
                            ->count();
                    @endphp
                    @if($draftCount > 0 || $submittedCount > 0 || $unreadAdminNotes > 0)
                        <span class="ml-auto flex items-center gap-1">
                            @if($draftCount > 0)
                            <span class="px-2 py-0.5 bg-gray-500 text-white text-xs rounded-full">{{ $draftCount }}</span>
                            @endif
                            @if($submittedCount > 0)
                            <span class="px-2 py-0.5 bg-blue-500 text-white text-xs rounded-full">{{ $submittedCount }}</span>
                            @endif
                            @if($unreadAdminNotes > 0)
                            <span class="px-2 py-0.5 bg-yellow-500 text-white text-xs rounded-full" title="Pesan baru dari admin">
                                <i class="fas fa-comment text-[10px]"></i> {{ $unreadAdminNotes }}
                            </span>
                            @endif
                        </span>
                    @endif
                </a>

                <a href="{{ route('client.projects.index') }}" 
                   class="flex items-center px-4 py-3 rounded-lg transition {{ request()->routeIs('client.projects.*') ? 'bg-purple-600' : 'hover:bg-purple-600' }}">
                    <i class="fas fa-folder-open w-5"></i>
                    <span class="ml-3">Proyek Aktif</span>
                    @php
                        $activeProjects = \App\Models\Project::where('client_id', auth('client')->id())
                            ->whereHas('status', function($q) {
                                $q->whereNotIn('name', ['Selesai', 'Dibatalkan']);
                            })
                            ->count();
                    @endphp
                    @if($activeProjects > 0)
                        <span class="ml-auto px-2 py-0.5 bg-green-500 text-white text-xs rounded-full">{{ $activeProjects }}</span>
                    @endif
                </a>

                <!-- Divider -->
                <div class="px-4 pt-4 pb-2">
                    <p class="text-xs uppercase tracking-wider text-purple-200 font-semibold">Dokumen & Keuangan</p>
                </div>

                <a href="{{ route('client.documents.index') }}" 
                   class="flex items-center px-4 py-3 rounded-lg transition {{ request()->routeIs('client.documents.*') ? 'bg-purple-600' : 'hover:bg-purple-600' }}">
                    <i class="fas fa-file-download w-5"></i>
                    <span class="ml-3">Dokumen</span>
                </a>

                <!-- Divider -->
                <div class="px-4 pt-4 pb-2">
                    <p class="text-xs uppercase tracking-wider text-purple-200 font-semibold">Akun</p>
                </div>

                <a href="{{ route('client.profile.edit') }}" 
                   class="flex items-center px-4 py-3 rounded-lg transition {{ request()->routeIs('client.profile.*') ? 'bg-purple-600' : 'hover:bg-purple-600' }}">
                    <i class="fas fa-user-circle w-5"></i>
                    <span class="ml-3">Profil Saya</span>
                </a>
            </nav>

            <!-- Logout -->
            <div class="p-4 border-t border-purple-600">
                <form method="POST" action="{{ route('client.logout') }}">
                    @csrf
                    <button type="submit" class="flex items-center w-full px-4 py-3 hover:bg-purple-600 rounded-lg transition">
                        <i class="fas fa-sign-out-alt w-5"></i>
                        <span class="ml-3">Logout</span>
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            
            <!-- Header -->
            <header class="bg-white shadow-sm z-10">
                <div class="flex items-center justify-between h-16 px-6">
                    <button @click="sidebarOpen = !sidebarOpen" class="lg:hidden text-gray-600">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                    
                    <div class="flex-1 flex items-center justify-between">
                        <div>
                            <h2 class="text-2xl font-bold text-gray-800">@yield('page-title', 'Portal Klien')</h2>
                            <p class="text-sm text-gray-500">@yield('page-subtitle', 'Selamat datang kembali, ' . auth('client')->user()->name . '!')</p>
                        </div>
                        
                        <div class="flex items-center space-x-4">
                            <button class="relative text-gray-600 hover:text-purple-600">
                                <i class="fas fa-bell text-xl"></i>
                                <span class="absolute -top-1 -right-1 w-5 h-5 bg-red-500 text-white text-xs rounded-full flex items-center justify-center">0</span>
                            </button>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Content -->
            <main class="flex-1 overflow-y-auto p-6">
                
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
        </div>
    </div>

    @stack('scripts')
</body>
</html>
