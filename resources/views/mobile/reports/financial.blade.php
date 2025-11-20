@extends('mobile.layouts.app')

@section('title', 'Laporan Keuangan')

@section('content')
<div class="pb-20">

    {{-- Header --}}
    <div class="bg-gradient-to-br from-green-600 to-green-800 rounded-2xl p-6 mb-4 text-white">
        <div class="flex items-center gap-3 mb-4">
            <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
                <i class="fas fa-coins text-2xl"></i>
            </div>
            <div>
                <h2 class="text-xl font-bold">Laporan Keuangan</h2>
                <p class="text-sm opacity-90">Cash Flow & P&L Statement</p>
            </div>
        </div>
        
        {{-- Net Cash Flow --}}
        <div class="bg-white/10 rounded-xl p-4 border border-white/20">
            <div class="text-sm opacity-90 mb-1">Net Cash Flow (3 Bulan)</div>
            <div class="text-3xl font-bold">
                Rp {{ number_format($cashFlow['net'], 0, ',', '.') }}
            </div>
            <div class="text-xs mt-2 opacity-75">
                Income: Rp {{ number_format($cashFlow['income'], 0, ',', '.') }} | 
                Expenses: Rp {{ number_format($cashFlow['expenses'], 0, ',', '.') }}
            </div>
        </div>
    </div>

    {{-- Summary Cards --}}
    <div class="grid grid-cols-2 gap-3 mb-4">
        <div class="bg-white rounded-xl p-4 border border-gray-200">
            <div class="flex items-center gap-2 mb-2">
                <div class="w-8 h-8 bg-green-50 rounded-lg flex items-center justify-center">
                    <i class="fas fa-arrow-down text-green-600 text-sm"></i>
                </div>
                <div class="text-xs text-gray-600">Total Income</div>
            </div>
            <div class="text-xl font-bold text-gray-900">
                Rp {{ number_format($cashFlow['income'], 0, ',', '.') }}
            </div>
        </div>
        
        <div class="bg-white rounded-xl p-4 border border-gray-200">
            <div class="flex items-center gap-2 mb-2">
                <div class="w-8 h-8 bg-red-50 rounded-lg flex items-center justify-center">
                    <i class="fas fa-arrow-up text-red-600 text-sm"></i>
                </div>
                <div class="text-xs text-gray-600">Total Expenses</div>
            </div>
            <div class="text-xl font-bold text-gray-900">
                Rp {{ number_format($cashFlow['expenses'], 0, ',', '.') }}
            </div>
        </div>
    </div>

    {{-- Receivables --}}
    <div class="bg-white rounded-xl p-4 border border-gray-200 mb-4">
        <h3 class="text-sm font-semibold text-gray-900 mb-3">PIUTANG</h3>
        <div class="space-y-3">
            <div class="flex justify-between items-center">
                <span class="text-sm text-gray-600">Total Piutang</span>
                <span class="text-lg font-bold text-gray-900">
                    Rp {{ number_format($receivables['total'], 0, ',', '.') }}
                </span>
            </div>
            <div class="flex justify-between items-center">
                <span class="text-sm text-gray-600">Jatuh Tempo</span>
                <span class="text-lg font-bold text-red-600">
                    Rp {{ number_format($receivables['overdue'], 0, ',', '.') }}
                </span>
            </div>
        </div>
    </div>

    {{-- Monthly Breakdown --}}
    <div class="bg-white rounded-xl p-4 border border-gray-200 mb-4">
        <h3 class="text-sm font-semibold text-gray-900 mb-3">RINCIAN BULANAN</h3>
        <div class="space-y-3">
            @foreach($monthly as $month)
            <div class="border-b border-gray-100 last:border-0 pb-3 last:pb-0">
                <div class="text-sm font-medium text-gray-900 mb-2">{{ $month['month'] }}</div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <div class="text-xs text-gray-500">Income</div>
                        <div class="text-sm font-semibold text-green-600">
                            Rp {{ number_format($month['income'], 0, ',', '.') }}
                        </div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-500">Expenses</div>
                        <div class="text-sm font-semibold text-red-600">
                            Rp {{ number_format($month['expenses'], 0, ',', '.') }}
                        </div>
                    </div>
                </div>
                <div class="mt-2">
                    <div class="text-xs text-gray-500">Net</div>
                    <div class="text-sm font-semibold {{ $month['income'] - $month['expenses'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                        Rp {{ number_format($month['income'] - $month['expenses'], 0, ',', '.') }}
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Actions --}}
    <div class="grid grid-cols-2 gap-3">
        <a href="{{ mobile_route('financial.receivables') }}" 
           class="flex items-center justify-center gap-2 bg-white border border-gray-200 rounded-xl p-4 active:bg-gray-50">
            <i class="fas fa-file-invoice text-[#0077b5]"></i>
            <span class="text-sm font-medium text-gray-900">Lihat Piutang</span>
        </a>
        <a href="{{ mobile_route('financial.expenses') }}" 
           class="flex items-center justify-center gap-2 bg-white border border-gray-200 rounded-xl p-4 active:bg-gray-50">
            <i class="fas fa-receipt text-[#0077b5]"></i>
            <span class="text-sm font-medium text-gray-900">Lihat Expenses</span>
        </a>
    </div>

</div>
@endsection
