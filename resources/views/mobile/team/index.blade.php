@extends('mobile.layouts.app')

@section('title', 'Tim')

@section('content')
<div class="pb-20">
    
    {{-- Header --}}
    <div class="bg-gradient-to-br from-[#0077b5] to-[#004d6d] rounded-2xl p-6 mb-4 text-white">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h2 class="text-xl font-bold">Tim</h2>
                <p class="text-sm opacity-90">{{ $stats['total'] ?? 0 }} anggota</p>
            </div>
            <div class="w-12 h-12 bg-white/10 rounded-xl flex items-center justify-center">
                <i class="fas fa-users text-2xl"></i>
            </div>
        </div>
        
        {{-- Stats --}}
        <div class="grid grid-cols-3 gap-2">
            <div class="bg-white/10 rounded-lg p-3 border border-white/20">
                <div class="text-2xl font-bold">{{ $stats['active'] ?? 0 }}</div>
                <div class="text-xs opacity-90">Aktif</div>
            </div>
            <div class="bg-white/10 rounded-lg p-3 border border-white/20">
                <div class="text-2xl font-bold">{{ $stats['on_project'] ?? 0 }}</div>
                <div class="text-xs opacity-90">Di Proyek</div>
            </div>
            <div class="bg-white/10 rounded-lg p-3 border border-white/20">
                <div class="text-2xl font-bold">{{ $stats['available'] ?? 0 }}</div>
                <div class="text-xs opacity-90">Available</div>
            </div>
        </div>
    </div>

    {{-- Search --}}
    <div class="relative mb-4">
        <input type="text" 
               placeholder="Cari anggota tim..." 
               class="w-full h-12 pl-12 pr-4 rounded-xl border border-gray-200 text-gray-900 placeholder-gray-400">
        <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
    </div>

    {{-- Role Filters --}}
    <div class="flex gap-2 overflow-x-auto pb-2 mb-4 scrollbar-hide">
        <button class="px-4 py-2 bg-[#0077b5] text-white rounded-lg text-sm font-medium whitespace-nowrap">
            Semua
        </button>
        <button class="px-4 py-2 bg-white text-gray-700 rounded-lg text-sm font-medium border border-gray-200 whitespace-nowrap">
            Admin
        </button>
        <button class="px-4 py-2 bg-white text-gray-700 rounded-lg text-sm font-medium border border-gray-200 whitespace-nowrap">
            Project Manager
        </button>
        <button class="px-4 py-2 bg-white text-gray-700 rounded-lg text-sm font-medium border border-gray-200 whitespace-nowrap">
            Staff
        </button>
    </div>

    {{-- Team Members List --}}
    @if(isset($users) && $users->count() > 0)
        <div class="space-y-3">
            @foreach($users as $user)
            <a href="#" onclick="alert('Detail anggota coming soon'); return false;" 
               class="block bg-white rounded-xl p-4 border border-gray-200 active:bg-gray-50">
                <div class="flex items-center gap-3">
                    {{-- Avatar --}}
                    @if($user->avatar_url ?? false)
                    <img src="{{ $user->avatar_url }}" 
                         alt="{{ $user->name }}"
                         class="w-14 h-14 rounded-full object-cover">
                    @else
                    <div class="w-14 h-14 rounded-full bg-gradient-to-br from-[#0077b5] to-[#004d6d] flex items-center justify-center text-white font-bold text-lg">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                    @endif
                    
                    {{-- Info --}}
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 mb-0.5">
                            <h4 class="font-semibold text-gray-900 truncate">{{ $user->name }}</h4>
                            @if(($user->is_active ?? true))
                            <span class="flex-shrink-0 px-2 py-0.5 bg-green-50 text-green-700 text-xs font-medium rounded">
                                Aktif
                            </span>
                            @else
                            <span class="flex-shrink-0 px-2 py-0.5 bg-gray-100 text-gray-600 text-xs font-medium rounded">
                                Nonaktif
                            </span>
                            @endif
                        </div>
                        
                        {{-- Role --}}
                        <div class="flex items-center gap-1 text-xs text-gray-600 mb-2">
                            <i class="fas fa-user-tag"></i>
                            @if($user->role)
                            <span>{{ $user->role->display_name ?? $user->role->name }}</span>
                            @else
                            <span>Staff</span>
                            @endif
                        </div>
                        
                        {{-- Contact --}}
                        <div class="space-y-1">
                            @if($user->email ?? false)
                            <div class="flex items-center gap-2 text-xs text-gray-500">
                                <i class="fas fa-envelope w-3.5"></i>
                                <span class="truncate">{{ $user->email }}</span>
                            </div>
                            @endif
                            @if($user->phone ?? false)
                            <div class="flex items-center gap-2 text-xs text-gray-500">
                                <i class="fas fa-phone w-3.5"></i>
                                <span>{{ $user->phone }}</span>
                            </div>
                            @endif
                        </div>
                        
                        {{-- Projects --}}
                        @if(isset($user->active_projects_count))
                        <div class="flex items-center gap-1 text-xs text-[#0077b5] font-medium mt-2">
                            <i class="fas fa-folder"></i>
                            <span>{{ $user->active_projects_count }} proyek aktif</span>
                        </div>
                        @endif
                    </div>
                    
                    <i class="fas fa-chevron-right text-gray-400"></i>
                </div>
            </a>
            @endforeach
        </div>

        {{-- Pagination --}}
        @if($users->hasPages())
        <div class="mt-6 flex justify-center">
            {{ $users->links() }}
        </div>
        @endif

    @else
        {{-- Empty State --}}
        <div class="bg-white rounded-xl p-8 text-center border border-gray-200">
            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
                <i class="fas fa-users text-gray-400 text-2xl"></i>
            </div>
            <h3 class="text-gray-900 font-semibold mb-1">Belum ada anggota tim</h3>
            <p class="text-sm text-gray-500">Tambahkan anggota tim untuk memulai</p>
        </div>
    @endif

</div>
@endsection
