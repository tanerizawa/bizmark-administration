@extends('mobile.layouts.app')

@section('title', 'Pengeluaran')

@section('content')
<div class="pb-20">

    {{-- Header --}}
    <div class="bg-gradient-to-br from-[#0A66C2] to-[#004182] rounded-2xl p-6 mb-4 text-white">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h2 class="text-xl font-bold">Pengeluaran</h2>
                <p class="text-sm opacity-90">{{ $stats['all'] ?? 0 }} total transaksi</p>
            </div>
            <div class="w-12 h-12 bg-white/10 rounded-xl flex items-center justify-center">
                <i class="fas fa-receipt text-2xl"></i>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-3">
            <div class="bg-white/10 rounded-lg p-3 border border-white/20">
                <div class="text-xs opacity-75">Bulan Ini</div>
                <div class="text-lg font-bold">Rp {{ number_format($stats['thisMonth'] ?? 0, 0, ',', '.') }}</div>
            </div>
            <div class="bg-white/10 rounded-lg p-3 border border-white/20">
                <div class="text-xs opacity-75">Billable</div>
                <div class="text-lg font-bold">{{ $stats['billable'] ?? 0 }}</div>
            </div>
        </div>
    </div>

    {{-- Status Filter --}}
    <div class="flex gap-2 mb-4 overflow-x-auto pb-2 scrollbar-hide">
        @foreach(['all' => 'Semua', 'pending' => 'Pending', 'approved' => 'Approved', 'rejected' => 'Rejected'] as $key => $label)
        <a href="{{ route('mobile.financial.expenses', ['status' => $key]) }}"
           class="px-4 py-2 rounded-lg text-sm font-medium whitespace-nowrap
           {{ $currentStatus === $key ? 'bg-[#0A66C2] text-white' : 'bg-white border border-gray-200 text-gray-700' }}">
            {{ $label }}
        </a>
        @endforeach
    </div>

    {{-- Expenses List --}}
    <div class="space-y-2">
        @forelse($expenses as $expense)
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <div class="flex items-start gap-3">
                <div class="w-10 h-10 bg-red-50 rounded-xl flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-arrow-up text-red-500 text-sm"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 mb-0.5">
                        <h4 class="text-sm font-semibold text-gray-900 truncate">
                            {{ $expense->description ?? 'Pengeluaran' }}
                        </h4>
                        <span class="text-xs px-2 py-0.5 rounded-full font-medium
                            @if($expense->status === 'approved') bg-green-100 text-green-700
                            @elseif($expense->status === 'pending') bg-amber-100 text-amber-700
                            @elseif($expense->status === 'rejected') bg-red-100 text-red-700
                            @else bg-gray-100 text-gray-700
                            @endif">
                            {{ ucfirst($expense->status ?? 'N/A') }}
                        </span>
                    </div>
                    @if($expense->project)
                    <p class="text-xs text-gray-500">
                        <i class="fas fa-folder text-gray-400 mr-1"></i>{{ $expense->project->name }}
                    </p>
                    @endif
                    @if($expense->category)
                    <p class="text-xs text-gray-400 mt-0.5">
                        <i class="fas fa-tag mr-1"></i>{{ $expense->category->name ?? '-' }}
                    </p>
                    @endif
                    <p class="text-xs text-gray-400 mt-0.5">
                        <i class="fas fa-calendar mr-1"></i>
                        {{ $expense->expense_date ? \Carbon\Carbon::parse($expense->expense_date)->format('d M Y') : '-' }}
                    </p>
                </div>
                <div class="text-right flex-shrink-0">
                    <div class="text-sm font-bold text-gray-900">
                        Rp {{ number_format($expense->amount ?? 0, 0, ',', '.') }}
                    </div>
                    @if($expense->is_billable)
                    <span class="text-xs text-[#0A66C2] font-medium">Billable</span>
                    @endif
                </div>
            </div>
        </div>
        @empty
        <div class="text-center py-16">
            <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-receipt text-gray-300 text-2xl"></i>
            </div>
            <h3 class="text-gray-500 font-medium mb-1">Belum Ada Pengeluaran</h3>
            <p class="text-sm text-gray-400">Catat pengeluaran pertama Anda</p>
        </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    @if($expenses->hasPages())
    <div class="mt-4 px-2">
        {{ $expenses->links('pagination::simple-tailwind') }}
    </div>
    @endif
</div>
@endsection
