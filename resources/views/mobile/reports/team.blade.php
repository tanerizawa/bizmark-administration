@extends('mobile.layouts.app')

@section('title', 'Laporan Tim')

@section('content')
<div class="pb-20">

    {{-- Header --}}
    <div class="bg-gradient-to-br from-purple-600 to-purple-800 rounded-2xl p-6 mb-4 text-white">
        <div class="flex items-center gap-3 mb-4">
            <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
                <i class="fas fa-users text-2xl"></i>
            </div>
            <div>
                <h2 class="text-xl font-bold">Laporan Tim</h2>
                <p class="text-sm opacity-90">Performance & Productivity</p>
            </div>
        </div>
        
        {{-- Quick Stats --}}
        <div class="grid grid-cols-3 gap-2">
            <div class="bg-white/10 rounded-lg p-3 border border-white/20">
                <div class="text-2xl font-bold">{{ $stats['total'] }}</div>
                <div class="text-xs opacity-90">Total Anggota</div>
            </div>
            <div class="bg-white/10 rounded-lg p-3 border border-white/20">
                <div class="text-2xl font-bold">{{ $stats['active'] }}</div>
                <div class="text-xs opacity-90">Aktif</div>
            </div>
            <div class="bg-white/10 rounded-lg p-3 border border-white/20">
                <div class="text-2xl font-bold">{{ $stats['on_project'] }}</div>
                <div class="text-xs opacity-90">Di Proyek</div>
            </div>
        </div>
    </div>

    {{-- Team Performance --}}
    <div class="bg-white rounded-xl p-4 border border-gray-200">
        <h3 class="text-sm font-semibold text-gray-900 mb-3">PERFORMA TIM</h3>
        @if($userPerformance->count() > 0)
        <div class="space-y-3">
            @foreach($userPerformance as $user)
            <div class="border-b border-gray-100 last:border-0 pb-3 last:pb-0">
                <div class="flex items-center gap-3 mb-2">
                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-[#0077b5] to-[#004d6d] flex items-center justify-center text-white font-bold text-sm">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                    <div class="flex-1">
                        <h4 class="text-sm font-medium text-gray-900">{{ $user->name }}</h4>
                        @if($user->role)
                        <div class="text-xs text-gray-500">{{ $user->role->display_name ?? $user->role->name }}</div>
                        @else
                        <div class="text-xs text-gray-500">Staff</div>
                        @endif
                    </div>
                </div>
                
                <div class="pl-0 ml-0">
                    <div class="grid grid-cols-3 gap-2 text-center bg-gray-50 rounded-lg p-2">
                        <div>
                            <div class="text-sm font-bold text-gray-900">{{ $user->total_tasks ?? 0 }}</div>
                            <div class="text-xs text-gray-500">Total Tasks</div>
                        </div>
                        <div>
                            <div class="text-sm font-bold text-green-600">{{ $user->completed_tasks ?? 0 }}</div>
                            <div class="text-xs text-gray-500">Completed</div>
                        </div>
                        <div>
                            @php
                                $total = $user->total_tasks ?? 0;
                                $completed = $user->completed_tasks ?? 0;
                                $rate = $total > 0 ? round(($completed / $total) * 100) : 0;
                            @endphp
                            <div class="text-sm font-bold text-[#0077b5]">{{ $rate }}%</div>
                            <div class="text-xs text-gray-500">Rate</div>
                        </div>
                    </div>
                    
                    {{-- Progress Bar --}}
                    @if(($user->total_tasks ?? 0) > 0)
                    <div class="mt-2 h-2 bg-gray-100 rounded-full overflow-hidden">
                        @php
                            $percentage = (($user->completed_tasks ?? 0) / $user->total_tasks) * 100;
                        @endphp
                        <div class="h-full bg-gradient-to-r from-[#0077b5] to-[#00a0dc]" 
                             style="width: {{ $percentage }}%"></div>
                    </div>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="text-center py-6 text-gray-400">
            <i class="fas fa-users text-2xl mb-2"></i>
            <p class="text-sm">Belum ada data performa</p>
        </div>
        @endif
    </div>

</div>
@endsection
