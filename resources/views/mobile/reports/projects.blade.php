@extends('mobile.layouts.app')

@section('title', 'Laporan Proyek')

@section('content')
<div class="pb-20">

    {{-- Header --}}
    <div class="bg-gradient-to-br from-blue-600 to-blue-800 rounded-2xl p-6 mb-4 text-white">
        <div class="flex items-center gap-3 mb-4">
            <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
                <i class="fas fa-folder text-2xl"></i>
            </div>
            <div>
                <h2 class="text-xl font-bold">Laporan Proyek</h2>
                <p class="text-sm opacity-90">Progress & Statistics</p>
            </div>
        </div>
        
        {{-- Quick Stats --}}
        <div class="grid grid-cols-4 gap-2">
            <div class="bg-white/10 rounded-lg p-3 border border-white/20">
                <div class="text-2xl font-bold">{{ $stats['total'] }}</div>
                <div class="text-xs opacity-90">Total</div>
            </div>
            <div class="bg-white/10 rounded-lg p-3 border border-white/20">
                <div class="text-2xl font-bold">{{ $stats['active'] }}</div>
                <div class="text-xs opacity-90">Aktif</div>
            </div>
            <div class="bg-white/10 rounded-lg p-3 border border-white/20">
                <div class="text-2xl font-bold">{{ $stats['completed'] }}</div>
                <div class="text-xs opacity-90">Selesai</div>
            </div>
            <div class="bg-white/10 rounded-lg p-3 border border-white/20">
                <div class="text-2xl font-bold">{{ $stats['cancelled'] }}</div>
                <div class="text-xs opacity-90">Batal</div>
            </div>
        </div>
    </div>

    {{-- Project by Status --}}
    <div class="bg-white rounded-xl p-4 border border-gray-200 mb-4">
        <h3 class="text-sm font-semibold text-gray-900 mb-3">PROYEK BERDASARKAN STATUS</h3>
        <div class="space-y-3">
            @foreach($byStatus as $status)
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-2 h-2 rounded-full bg-[#0077b5]"></div>
                    <span class="text-sm text-gray-900">{{ $status->name }}</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-sm font-semibold text-gray-900">{{ $status->count }}</span>
                    <span class="text-xs text-gray-500">
                        ({{ round(($status->count / $stats['total']) * 100, 1) }}%)
                    </span>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Recent Completions --}}
    <div class="bg-white rounded-xl p-4 border border-gray-200">
        <h3 class="text-sm font-semibold text-gray-900 mb-3">PROYEK SELESAI TERBARU</h3>
        @if($recentCompletions->count() > 0)
        <div class="space-y-2">
            @foreach($recentCompletions as $project)
            <a href="{{ mobile_route('projects.show', $project->id) }}" 
               class="block p-3 bg-gray-50 rounded-lg active:bg-gray-100">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-green-50 rounded-lg flex items-center justify-center">
                        <i class="fas fa-check text-green-600"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h4 class="text-sm font-medium text-gray-900 truncate">{{ $project->name }}</h4>
                        <div class="text-xs text-gray-500 mt-0.5">
                            Selesai {{ $project->updated_at->diffForHumans() }}
                        </div>
                    </div>
                    <i class="fas fa-chevron-right text-gray-400 text-xs"></i>
                </div>
            </a>
            @endforeach
        </div>
        @else
        <div class="text-center py-6 text-gray-400">
            <i class="fas fa-folder-open text-2xl mb-2"></i>
            <p class="text-sm">Belum ada proyek selesai</p>
        </div>
        @endif
    </div>

</div>
@endsection
