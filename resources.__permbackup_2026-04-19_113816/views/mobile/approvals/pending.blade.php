@extends('mobile.layouts.app')

@section('title', 'Pending Approvals')

@section('content')
<div class="pb-20">

    {{-- Header --}}
    <div class="bg-gradient-to-br from-[#0A66C2] to-[#004182] rounded-2xl p-6 mb-4 text-white">
        <div class="flex items-center justify-between mb-2">
            <div>
                <h2 class="text-xl font-bold">Pending Approvals</h2>
                <p class="text-sm opacity-90">{{ $approvals->count() }} item menunggu persetujuan</p>
            </div>
            <div class="w-12 h-12 bg-white/10 rounded-xl flex items-center justify-center">
                <i class="fas fa-clock text-2xl"></i>
            </div>
        </div>
    </div>

    {{-- Approval List --}}
    <div class="space-y-2">
        @forelse($approvals as $approval)
        <a href="{{ $approval['url'] ?? '#' }}"
           class="block bg-white rounded-xl border border-gray-200 p-4 active:scale-[0.98] transition-transform">
            <div class="flex items-start gap-3">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0
                    @if($approval['color'] === 'purple') bg-purple-50
                    @elseif($approval['color'] === 'green') bg-green-50
                    @else bg-blue-50
                    @endif">
                    <i class="fas fa-{{ $approval['icon'] ?? 'file' }}
                        @if($approval['color'] === 'purple') text-purple-600
                        @elseif($approval['color'] === 'green') text-green-600
                        @else text-blue-600
                        @endif"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 mb-1">
                        <h4 class="text-sm font-semibold text-gray-900 truncate">{{ $approval['title'] }}</h4>
                        <span class="text-xs px-2 py-0.5 rounded-full font-medium bg-amber-100 text-amber-700">Pending</span>
                    </div>
                    <p class="text-xs text-gray-500">{{ $approval['subtitle'] ?? '-' }}</p>
                    @if(isset($approval['amount']))
                    <p class="text-sm font-bold text-gray-900 mt-1">
                        Rp {{ number_format($approval['amount'], 0, ',', '.') }}
                    </p>
                    @endif
                    @if(isset($approval['date']))
                    <p class="text-xs text-gray-400 mt-1">
                        <i class="fas fa-calendar mr-1"></i>{{ \Carbon\Carbon::parse($approval['date'])->format('d M Y') }}
                    </p>
                    @endif
                </div>
                <i class="fas fa-chevron-right text-gray-300 text-xs mt-2"></i>
            </div>
        </a>
        @empty
        <div class="text-center py-16">
            <div class="w-16 h-16 bg-green-50 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-check-circle text-green-400 text-2xl"></i>
            </div>
            <h3 class="text-gray-500 font-medium mb-1">Tidak Ada Pending</h3>
            <p class="text-sm text-gray-400">Semua item sudah diproses</p>
        </div>
        @endforelse
    </div>
</div>
@endsection
