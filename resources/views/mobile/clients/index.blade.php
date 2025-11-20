@extends('mobile.layouts.app')

@section('title', 'Klien')

@section('content')
<div class="pb-20">
    
    {{-- Header --}}
    <div class="bg-gradient-to-br from-[#0077b5] to-[#004d6d] rounded-2xl p-6 mb-4 text-white">
        <div class="flex items-center justify-between mb-3">
            <div>
                <h2 class="text-xl font-bold">Klien</h2>
                <p class="text-sm opacity-90">Daftar klien & proyek</p>
            </div>
            <div class="w-12 h-12 bg-white/10 rounded-xl flex items-center justify-center">
                <i class="fas fa-building text-2xl"></i>
            </div>
        </div>
        
        {{-- Search --}}
        <div class="relative">
            <input type="text" 
                   placeholder="Cari klien..." 
                   class="w-full px-4 py-2.5 pl-10 bg-white/10 border border-white/20 rounded-lg text-white placeholder-white/60 focus:outline-none focus:ring-2 focus:ring-white/30">
            <i class="fas fa-search absolute left-3 top-3.5 text-white/60"></i>
        </div>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-3 gap-3 mb-4">
        <div class="bg-white rounded-xl p-4 border border-gray-200 text-center">
            <div class="text-2xl font-bold text-[#0077b5] mb-1">{{ $stats['total'] }}</div>
            <div class="text-xs text-gray-600">Total</div>
        </div>
        <div class="bg-white rounded-xl p-4 border border-gray-200 text-center">
            <div class="text-2xl font-bold text-green-600 mb-1">{{ $stats['active'] }}</div>
            <div class="text-xs text-gray-600">Aktif</div>
        </div>
        <div class="bg-white rounded-xl p-4 border border-gray-200 text-center">
            <div class="text-2xl font-bold text-gray-600 mb-1">{{ $stats['inactive'] }}</div>
            <div class="text-xs text-gray-600">Tidak Aktif</div>
        </div>
    </div>

    {{-- Client List --}}
    <div class="space-y-2">
        @forelse($clients as $client)
        <div class="bg-white rounded-xl p-4 border border-gray-200">
            <div class="flex items-start justify-between mb-3">
                <div class="flex-1">
                    <div class="flex items-center gap-2 mb-1">
                        <div class="w-10 h-10 bg-gradient-to-br from-[#0077b5] to-[#004d6d] rounded-lg flex items-center justify-center text-white font-bold">
                            {{ strtoupper(substr($client->name, 0, 1)) }}
                        </div>
                        <div class="flex-1">
                            <h3 class="font-semibold text-gray-900">{{ $client->name }}</h3>
                            @if($client->company_name)
                            <div class="text-xs text-gray-500">{{ $client->company_name }}</div>
                            @endif
                        </div>
                    </div>
                </div>
                <span class="px-2 py-1 rounded-full text-xs font-medium {{ $client->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600' }}">
                    {{ $client->status === 'active' ? 'Aktif' : 'Nonaktif' }}
                </span>
            </div>
            
            <div class="space-y-2 text-sm">
                @if($client->email)
                <div class="flex items-center gap-2 text-gray-600">
                    <i class="fas fa-envelope text-xs w-4"></i>
                    <span>{{ $client->email }}</span>
                </div>
                @endif
                @if($client->phone)
                <div class="flex items-center gap-2 text-gray-600">
                    <i class="fas fa-phone text-xs w-4"></i>
                    <span>{{ $client->phone }}</span>
                </div>
                @endif
                <div class="flex items-center gap-2 text-gray-600">
                    <i class="fas fa-folder text-xs w-4"></i>
                    <span>{{ $client->projects_count }} Proyek</span>
                </div>
            </div>
            
            <div class="mt-3 pt-3 border-t border-gray-100">
                <a href="{{ mobile_route('clients.show', $client->id) }}" 
                   class="text-[#0077b5] text-sm font-medium inline-flex items-center gap-1">
                    Lihat Detail
                    <i class="fas fa-chevron-right text-xs"></i>
                </a>
            </div>
        </div>
        @empty
        <div class="text-center py-12">
            <div class="w-20 h-20 bg-gray-100 rounded-full mx-auto mb-4 flex items-center justify-center">
                <i class="fas fa-building text-3xl text-gray-400"></i>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Belum Ada Klien</h3>
            <p class="text-sm text-gray-600 mb-4">Tambahkan klien pertama Anda</p>
        </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    @if($clients->hasPages())
    <div class="mt-4">
        {{ $clients->links() }}
    </div>
    @endif

</div>
@endsection
