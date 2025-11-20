@extends('mobile.layouts.app')

@section('title', 'Laporan Klien')

@section('content')
<div class="pb-20">

    {{-- Header --}}
    <div class="bg-gradient-to-br from-orange-600 to-orange-800 rounded-2xl p-6 mb-4 text-white">
        <div class="flex items-center gap-3 mb-4">
            <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
                <i class="fas fa-building text-2xl"></i>
            </div>
            <div>
                <h2 class="text-xl font-bold">Laporan Klien</h2>
                <p class="text-sm opacity-90">Client Statistics</p>
            </div>
        </div>
        
        {{-- Quick Stats --}}
        <div class="grid grid-cols-3 gap-2">
            <div class="bg-white/10 rounded-lg p-3 border border-white/20">
                <div class="text-2xl font-bold">{{ $stats['total'] }}</div>
                <div class="text-xs opacity-90">Total Klien</div>
            </div>
            <div class="bg-white/10 rounded-lg p-3 border border-white/20">
                <div class="text-2xl font-bold">{{ $stats['active'] }}</div>
                <div class="text-xs opacity-90">Aktif</div>
            </div>
            <div class="bg-white/10 rounded-lg p-3 border border-white/20">
                <div class="text-2xl font-bold">{{ $stats['inactive'] }}</div>
                <div class="text-xs opacity-90">Nonaktif</div>
            </div>
        </div>
    </div>

    {{-- Top Clients --}}
    <div class="bg-white rounded-xl p-4 border border-gray-200">
        <h3 class="text-sm font-semibold text-gray-900 mb-3">TOP KLIEN (By Project Count)</h3>
        @if($topClients->count() > 0)
        <div class="space-y-2">
            @foreach($topClients as $index => $client)
            <a href="{{ mobile_route('clients.show', $client->id) }}" 
               class="block p-3 bg-gray-50 rounded-lg active:bg-gray-100">
                <div class="flex items-center gap-3">
                    {{-- Rank Badge --}}
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center font-bold text-sm
                        {{ $index === 0 ? 'bg-yellow-100 text-yellow-700' : '' }}
                        {{ $index === 1 ? 'bg-gray-200 text-gray-700' : '' }}
                        {{ $index === 2 ? 'bg-orange-100 text-orange-700' : '' }}
                        {{ $index > 2 ? 'bg-gray-100 text-gray-600' : '' }}">
                        #{{ $index + 1 }}
                    </div>
                    
                    {{-- Client Info --}}
                    <div class="flex-1 min-w-0">
                        <h4 class="text-sm font-medium text-gray-900 truncate">{{ $client->name }}</h4>
                        @if($client->company_name)
                        <div class="text-xs text-gray-500 truncate">{{ $client->company_name }}</div>
                        @endif
                    </div>
                    
                    {{-- Project Count --}}
                    <div class="text-right">
                        <div class="text-lg font-bold text-[#0077b5]">{{ $client->projects_count }}</div>
                        <div class="text-xs text-gray-500">proyek</div>
                    </div>
                    
                    <i class="fas fa-chevron-right text-gray-400 text-xs"></i>
                </div>
            </a>
            @endforeach
        </div>
        @else
        <div class="text-center py-6 text-gray-400">
            <i class="fas fa-building text-2xl mb-2"></i>
            <p class="text-sm">Belum ada data klien</p>
        </div>
        @endif
    </div>

</div>
@endsection
