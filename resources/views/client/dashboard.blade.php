<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Dashboard Client - Bizmark.id</title>
    
    <!-- External CSS - CDN Only -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        [x-cloak] { display: none !important; }
    </style>
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
                        <p class="font-semibold truncate">{{ $client->name }}</p>
                        <p class="text-sm text-purple-200 truncate">{{ $client->email }}</p>
                    </div>
                </div>
            </div>

            <!-- Navigation -->
            <nav class="flex-1 px-4 py-6 space-y-2">
                <a href="{{ route('client.dashboard') }}" class="flex items-center px-4 py-3 bg-purple-600 rounded-lg">
                    <i class="fas fa-home w-5"></i>
                    <span class="ml-3">Dashboard</span>
                </a>
                <a href="#" class="flex items-center px-4 py-3 hover:bg-purple-600 rounded-lg transition">
                    <i class="fas fa-folder w-5"></i>
                    <span class="ml-3">Proyek Saya</span>
                </a>
                <a href="#" class="flex items-center px-4 py-3 hover:bg-purple-600 rounded-lg transition">
                    <i class="fas fa-file-alt w-5"></i>
                    <span class="ml-3">Dokumen</span>
                </a>
                <a href="#" class="flex items-center px-4 py-3 hover:bg-purple-600 rounded-lg transition">
                    <i class="fas fa-credit-card w-5"></i>
                    <span class="ml-3">Pembayaran</span>
                </a>
                <a href="#" class="flex items-center px-4 py-3 hover:bg-purple-600 rounded-lg transition">
                    <i class="fas fa-user-circle w-5"></i>
                    <span class="ml-3">Profil</span>
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
                            <h2 class="text-2xl font-bold text-gray-800">Dashboard</h2>
                            <p class="text-sm text-gray-500">Selamat datang kembali, {{ $client->name }}!</p>
                        </div>
                        
                        <div class="flex items-center space-x-4">
                            <button class="relative text-gray-600 hover:text-purple-600">
                                <i class="fas fa-bell text-xl"></i>
                                <span class="absolute -top-1 -right-1 w-5 h-5 bg-red-500 text-white text-xs rounded-full flex items-center justify-center">3</span>
                            </button>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Content -->
            <main class="flex-1 overflow-y-auto p-6">
                
                @if (session('success'))
                    <div class="mb-6 p-4 bg-green-100 border-l-4 border-green-500 text-green-700 rounded">
                        <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
                    </div>
                @endif

                <!-- Metrics Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <!-- Active Projects -->
                    <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-blue-500">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-600 mb-1">Proyek Aktif</p>
                                <p class="text-3xl font-bold text-gray-800">{{ $activeProjects }}</p>
                            </div>
                            <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-folder-open text-blue-600 text-xl"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Completed Projects -->
                    <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-green-500">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-600 mb-1">Proyek Selesai</p>
                                <p class="text-3xl font-bold text-gray-800">{{ $completedProjects }}</p>
                            </div>
                            <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-check-circle text-green-600 text-xl"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Total Investment -->
                    <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-purple-500">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-600 mb-1">Total Investasi</p>
                                <p class="text-3xl font-bold text-gray-800">Rp {{ number_format($totalInvested, 0, ',', '.') }}</p>
                            </div>
                            <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-wallet text-purple-600 text-xl"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Upcoming Deadlines -->
                    <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-orange-500">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-600 mb-1">Deadline Dekat</p>
                                <p class="text-3xl font-bold text-gray-800">{{ $upcomingDeadlines->count() }}</p>
                            </div>
                            <div class="w-12 h-12 bg-orange-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-clock text-orange-600 text-xl"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    
                    <!-- Active Projects -->
                    <div class="bg-white rounded-xl shadow-sm">
                        <div class="p-6 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-800">Proyek Aktif</h3>
                        </div>
                        <div class="p-6">
                            @forelse($projects->take(5) as $project)
                                <div class="flex items-center justify-between py-3 border-b border-gray-100 last:border-0">
                                    <div class="flex-1">
                                        <p class="font-medium text-gray-800">{{ $project->name }}</p>
                                        <p class="text-sm text-gray-500">{{ $project->permitType->name ?? 'N/A' }}</p>
                                    </div>
                                    <span class="px-3 py-1 text-xs font-semibold rounded-full {{ $project->status && $project->status->name === 'Selesai' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800' }}">
                                        {{ $project->status->name ?? 'N/A' }}
                                    </span>
                                </div>
                            @empty
                                <p class="text-gray-500 text-center py-8">Belum ada proyek</p>
                            @endforelse
                        </div>
                    </div>

                    <!-- Recent Documents -->
                    <div class="bg-white rounded-xl shadow-sm">
                        <div class="p-6 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-800">Dokumen Terbaru</h3>
                        </div>
                        <div class="p-6">
                            @forelse($recentDocuments as $document)
                                <div class="flex items-center justify-between py-3 border-b border-gray-100 last:border-0">
                                    <div class="flex items-center space-x-3 flex-1">
                                        <i class="fas fa-file-pdf text-red-500"></i>
                                        <div class="flex-1 min-w-0">
                                            <p class="font-medium text-gray-800 truncate">{{ $document->document_name }}</p>
                                            <p class="text-sm text-gray-500">{{ $document->created_at->diffForHumans() }}</p>
                                        </div>
                                    </div>
                                    <a href="{{ Storage::url($document->file_path) }}" target="_blank" class="text-purple-600 hover:text-purple-800">
                                        <i class="fas fa-download"></i>
                                    </a>
                                </div>
                            @empty
                                <p class="text-gray-500 text-center py-8">Belum ada dokumen</p>
                            @endforelse
                        </div>
                    </div>

                    <!-- Upcoming Deadlines -->
                    <div class="bg-white rounded-xl shadow-sm lg:col-span-2">
                        <div class="p-6 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-800">Deadline Mendatang (7 Hari)</h3>
                        </div>
                        <div class="p-6">
                            @forelse($upcomingDeadlines as $task)
                                <div class="flex items-center justify-between py-3 border-b border-gray-100 last:border-0">
                                    <div class="flex-1">
                                        <p class="font-medium text-gray-800">{{ $task->name }}</p>
                                        <p class="text-sm text-gray-500">{{ $task->project->name ?? 'N/A' }}</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-sm font-semibold text-orange-600">
                                            {{ $task->due_date->diffForHumans() }}
                                        </p>
                                        <p class="text-xs text-gray-500">{{ $task->due_date->format('d M Y') }}</p>
                                    </div>
                                </div>
                            @empty
                                <p class="text-gray-500 text-center py-8">Tidak ada deadline dalam 7 hari ke depan</p>
                            @endforelse
                        </div>
                    </div>

                </div>

            </main>

        </div>

    </div>

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- Tawk.to Live Chat Widget -->
    <script type="text/javascript">
    var Tawk_API=Tawk_API||{}, Tawk_LoadStart=new Date();
    (function(){
        var s1=document.createElement("script"),s0=document.getElementsByTagName("script")[0];
        s1.async=true;
        s1.src='https://embed.tawk.to/YOUR_PROPERTY_ID/YOUR_WIDGET_ID';
        s1.charset='UTF-8';
        s1.setAttribute('crossorigin','*');
        s0.parentNode.insertBefore(s1,s0);
        
        // Set visitor info
        Tawk_API.onLoad = function(){
            Tawk_API.setAttributes({
                'name' : '{{ $client->name }}',
                'email' : '{{ $client->email }}',
                'hash' : '{{ hash_hmac("sha256", $client->email, config("services.tawk.api_key", "")) }}'
            }, function(error){});
        };
    })();
    </script>
</body>
</html>
