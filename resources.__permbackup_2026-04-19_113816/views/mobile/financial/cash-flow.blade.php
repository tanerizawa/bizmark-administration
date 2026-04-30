@extends('mobile.layouts.app')

@section('title', 'Cash Flow')

@section('content')
<div class="pb-20">

    {{-- Summary Card --}}
    <div class="bg-gradient-to-br from-[#0A66C2] to-[#004182] rounded-2xl p-6 mb-4 text-white">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h2 class="text-xl font-bold">Cash Flow</h2>
                <p class="text-sm opacity-90">Periode: {{ ucfirst($period) }}</p>
            </div>
            <div class="w-12 h-12 bg-white/10 rounded-xl flex items-center justify-center">
                <i class="fas fa-chart-line text-2xl"></i>
            </div>
        </div>

        <div class="text-center mb-4">
            <div class="text-sm opacity-75">Net Cash Flow</div>
            <div class="text-3xl font-bold {{ $netCashFlow >= 0 ? '' : 'text-red-200' }}">
                Rp {{ number_format($netCashFlow, 0, ',', '.') }}
            </div>
        </div>

        <div class="grid grid-cols-2 gap-3">
            <div class="bg-white/10 rounded-lg p-3 border border-white/20">
                <div class="flex items-center gap-2 mb-1">
                    <i class="fas fa-arrow-down text-green-300 text-xs"></i>
                    <span class="text-xs opacity-90">Pemasukan</span>
                </div>
                <div class="text-lg font-bold">Rp {{ number_format($totalIncome, 0, ',', '.') }}</div>
            </div>
            <div class="bg-white/10 rounded-lg p-3 border border-white/20">
                <div class="flex items-center gap-2 mb-1">
                    <i class="fas fa-arrow-up text-red-300 text-xs"></i>
                    <span class="text-xs opacity-90">Pengeluaran</span>
                </div>
                <div class="text-lg font-bold">Rp {{ number_format($totalExpenses, 0, ',', '.') }}</div>
            </div>
        </div>
    </div>

    {{-- Period Filter --}}
    <div class="flex gap-2 mb-4 overflow-x-auto pb-2 scrollbar-hide">
        @foreach(['week' => 'Minggu', 'month' => 'Bulan', 'quarter' => 'Kuartal', 'year' => 'Tahun'] as $key => $label)
        <a href="{{ route('mobile.financial.cash-flow', ['period' => $key]) }}"
           class="px-4 py-2 rounded-lg text-sm font-medium whitespace-nowrap
           {{ $period === $key ? 'bg-[#0A66C2] text-white' : 'bg-white border border-gray-200 text-gray-700' }}">
            {{ $label }}
        </a>
        @endforeach
    </div>

    {{-- Daily Breakdown --}}
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="px-4 py-3 border-b border-gray-100">
            <h3 class="text-sm font-bold text-gray-900">Detail Harian</h3>
        </div>
        <div class="divide-y divide-gray-100">
            @forelse($dailyFlow as $day)
            <div class="p-4">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-medium text-gray-900">
                        {{ \Carbon\Carbon::parse($day->date)->format('d M Y') }}
                    </span>
                    <span class="text-sm font-bold {{ $day->daily_net >= 0 ? 'text-green-600' : 'text-red-600' }}">
                        {{ $day->daily_net >= 0 ? '+' : '' }}Rp {{ number_format($day->daily_net, 0, ',', '.') }}
                    </span>
                </div>
                <div class="flex items-center gap-4 text-xs text-gray-500">
                    <span class="flex items-center gap-1">
                        <i class="fas fa-arrow-down text-green-500"></i>
                        Rp {{ number_format($day->daily_income, 0, ',', '.') }}
                    </span>
                    <span class="flex items-center gap-1">
                        <i class="fas fa-arrow-up text-red-500"></i>
                        Rp {{ number_format($day->daily_expense, 0, ',', '.') }}
                    </span>
                </div>
            </div>
            @empty
            <div class="p-8 text-center text-sm text-gray-400">
                <i class="fas fa-chart-bar text-3xl text-gray-300 mb-2 block"></i>
                Belum ada transaksi di periode ini
            </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
