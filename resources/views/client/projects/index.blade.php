@extends('client.layouts.app')

@section('title', 'Proyek Saya')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Proyek Saya</h1>
            <p class="text-gray-600 mt-1">Kelola dan monitor semua proyek perizinan Anda</p>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Total Projects -->
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Total Proyek</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">{{ $stats['total'] }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-folder text-blue-600 text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Active Projects -->
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Proyek Aktif</p>
                    <p class="text-3xl font-bold text-green-600 mt-2">{{ $stats['active'] }}</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-clock text-green-600 text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Completed Projects -->
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Selesai</p>
                    <p class="text-3xl font-bold text-purple-600 mt-2">{{ $stats['completed'] }}</p>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-check-circle text-purple-600 text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Total Value -->
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Total Nilai Kontrak</p>
                    <p class="text-2xl font-bold text-gray-900 mt-2">Rp {{ number_format($stats['total_value'], 0, ',', '.') }}</p>
                </div>
                <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-dollar-sign text-yellow-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters & Search -->
    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
        <form method="GET" action="{{ route('client.projects.index') }}" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Search -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-search mr-2"></i>Cari Proyek
                    </label>
                    <input 
                        type="text" 
                        name="search" 
                        value="{{ request('search') }}"
                        placeholder="Nama proyek..."
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                    >
                </div>

                <!-- Status Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-filter mr-2"></i>Status
                    </label>
                    <select 
                        name="status" 
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                    >
                        <option value="">Semua Status</option>
                        @foreach($statuses as $status)
                            <option value="{{ $status->id }}" {{ request('status') == $status->id ? 'selected' : '' }}>
                                {{ $status->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Sort -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-sort mr-2"></i>Urutkan
                    </label>
                    <select 
                        name="sort_by" 
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                    >
                        <option value="created_at" {{ request('sort_by') == 'created_at' ? 'selected' : '' }}>Terbaru</option>
                        <option value="deadline" {{ request('sort_by') == 'deadline' ? 'selected' : '' }}>Deadline</option>
                        <option value="name" {{ request('sort_by') == 'name' ? 'selected' : '' }}>Nama A-Z</option>
                        <option value="progress_percentage" {{ request('sort_by') == 'progress_percentage' ? 'selected' : '' }}>Progress</option>
                    </select>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <button 
                    type="submit" 
                    class="px-6 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition"
                >
                    <i class="fas fa-search mr-2"></i>Filter
                </button>
                <a 
                    href="{{ route('client.projects.index') }}" 
                    class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition"
                >
                    <i class="fas fa-redo mr-2"></i>Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Projects Grid -->
    @if($projects->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($projects as $project)
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-lg transition">
                    <!-- Header -->
                    <div class="p-6 bg-gradient-to-r from-purple-50 to-purple-100">
                        <div class="flex items-start justify-between mb-3">
                            <h3 class="text-lg font-bold text-gray-900 line-clamp-2">
                                {{ $project->name }}
                            </h3>
                            <span class="px-3 py-1 text-xs font-semibold rounded-full whitespace-nowrap ml-2" 
                                  style="background-color: {{ $project->status->color }}20; color: {{ $project->status->color }}">
                                {{ $project->status->name }}
                            </span>
                        </div>
                        
                        @if($project->institution)
                            <p class="text-sm text-gray-600">
                                <i class="fas fa-building mr-1"></i>{{ $project->institution->name }}
                            </p>
                        @endif
                    </div>

                    <!-- Body -->
                    <div class="p-6 space-y-4">
                        <!-- Progress Bar -->
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-sm font-medium text-gray-700">Progress</span>
                                <span class="text-sm font-bold text-purple-600">{{ $project->progress_percentage ?? 0 }}%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div 
                                    class="bg-gradient-to-r from-purple-500 to-purple-600 h-2 rounded-full transition-all duration-300" 
                                    style="width: {{ $project->progress_percentage ?? 0 }}%"
                                ></div>
                            </div>
                        </div>

                        <!-- Info Grid -->
                        <div class="grid grid-cols-2 gap-4 pt-4 border-t border-gray-100">
                            <div>
                                <p class="text-xs text-gray-500 mb-1">Mulai</p>
                                <p class="text-sm font-medium text-gray-900">
                                    {{ $project->start_date ? $project->start_date->format('d M Y') : '-' }}
                                </p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 mb-1">Deadline</p>
                                <p class="text-sm font-medium text-gray-900">
                                    {{ $project->deadline ? $project->deadline->format('d M Y') : '-' }}
                                </p>
                            </div>
                        </div>

                        <!-- Contract Value -->
                        @if($project->contract_value)
                            <div class="pt-4 border-t border-gray-100">
                                <p class="text-xs text-gray-500 mb-1">Nilai Kontrak</p>
                                <p class="text-lg font-bold text-gray-900">
                                    Rp {{ number_format($project->contract_value, 0, ',', '.') }}
                                </p>
                            </div>
                        @endif
                    </div>

                    <!-- Footer -->
                    <div class="px-6 pb-6">
                        <a 
                            href="{{ route('client.projects.show', $project->id) }}" 
                            class="block w-full text-center px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition"
                        >
                            <i class="fas fa-eye mr-2"></i>Lihat Detail
                        </a>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
            {{ $projects->links() }}
        </div>
    @else
        <!-- Empty State -->
        <div class="bg-white rounded-xl shadow-sm p-12 border border-gray-100 text-center">
            <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-folder-open text-gray-400 text-4xl"></i>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-2">Tidak Ada Proyek</h3>
            <p class="text-gray-600 mb-6">
                @if(request('search') || request('status'))
                    Tidak ada proyek yang sesuai dengan filter Anda.
                @else
                    Belum ada proyek yang terdaftar untuk akun Anda.
                @endif
            </p>
            @if(request('search') || request('status'))
                <a 
                    href="{{ route('client.projects.index') }}" 
                    class="inline-block px-6 py-3 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition"
                >
                    <i class="fas fa-redo mr-2"></i>Reset Filter
                </a>
            @endif
        </div>
    @endif
</div>
@endsection
