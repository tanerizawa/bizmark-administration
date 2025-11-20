@extends('mobile.layouts.app')

@section('title', 'Keuangan')

@section('content')
<div class="pb-20">
    
    {{-- Cash Balance Card --}}
    <div class="bg-gradient-to-br from-[#0077b5] to-[#004d6d] rounded-2xl p-6 mb-4 text-white shadow-lg">
        <div class="text-sm opacity-90 mb-2">Saldo Kas</div>
        <div class="text-3xl font-bold mb-1">Rp {{ number_format($cashBalance['total'] ?? 0, 0, ',', '.') }}</div>
        <div class="text-sm opacity-75">Runway: {{ $runway['months'] ?? 0 }} bulan</div>
    </div>

    {{-- Monthly Stats --}}
    <div class="grid grid-cols-2 gap-3 mb-4">
        <div class="bg-white rounded-xl p-4 border border-gray-200">
            <div class="flex items-center gap-2 mb-2">
                <div class="w-8 h-8 bg-green-50 rounded-lg flex items-center justify-center">
                    <i class="fas fa-arrow-down text-green-600 text-sm"></i>
                </div>
                <div class="text-xs text-gray-600 font-medium">Pemasukan</div>
            </div>
            <div class="text-xl font-bold text-gray-900">
                Rp {{ number_format($thisMonth['income'] ?? 0, 0, ',', '.') }}
            </div>
            <div class="text-xs text-gray-500 mt-1">Bulan ini</div>
        </div>

        <div class="bg-white rounded-xl p-4 border border-gray-200">
            <div class="flex items-center gap-2 mb-2">
                <div class="w-8 h-8 bg-red-50 rounded-lg flex items-center justify-center">
                    <i class="fas fa-arrow-up text-red-600 text-sm"></i>
                </div>
                <div class="text-xs text-gray-600 font-medium">Pengeluaran</div>
            </div>
            <div class="text-xl font-bold text-gray-900">
                Rp {{ number_format($thisMonth['expenses'] ?? 0, 0, ',', '.') }}
            </div>
            <div class="text-xs text-gray-500 mt-1">Bulan ini</div>
        </div>
    </div>

    {{-- Pending Receivables --}}
    @if($pendingReceivables && $pendingReceivables->count() > 0)
    @php
        $totalPending = $pendingReceivables->sum('amount');
    @endphp
    <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 mb-4">
        <div class="flex items-start gap-3">
            <div class="w-10 h-10 bg-amber-100 rounded-lg flex items-center justify-center flex-shrink-0">
                <i class="fas fa-clock text-amber-600"></i>
            </div>
            <div class="flex-1">
                <div class="font-semibold text-gray-900 mb-1">Piutang Pending</div>
                <div class="text-2xl font-bold text-amber-600 mb-1">
                    Rp {{ number_format($totalPending, 0, ',', '.') }}
                </div>
                <div class="text-sm text-gray-600">{{ $pendingReceivables->count() }} invoice menunggu pembayaran</div>
            </div>
        </div>
    </div>
    @endif

    {{-- Quick Actions --}}
    <div class="mb-4">
        <h3 class="text-sm font-semibold text-gray-900 mb-3 px-1">Aksi Cepat</h3>
        <div class="grid grid-cols-2 gap-3">
            <a href="{{ mobile_route('financial.quick-input', ['type' => 'income']) }}" 
               class="bg-white border border-gray-200 rounded-xl p-4 active:scale-95 transition-transform">
                <div class="w-10 h-10 bg-green-50 rounded-lg flex items-center justify-center mb-3">
                    <i class="fas fa-plus-circle text-green-600"></i>
                </div>
                <div class="font-medium text-gray-900 text-sm">Input Pemasukan</div>
                <div class="text-xs text-gray-500 mt-1">Pembayaran & invoice</div>
            </a>

            <a href="{{ mobile_route('financial.quick-input', ['type' => 'expense']) }}" 
               class="bg-white border border-gray-200 rounded-xl p-4 active:scale-95 transition-transform">
                <div class="w-10 h-10 bg-red-50 rounded-lg flex items-center justify-center mb-3">
                    <i class="fas fa-minus-circle text-red-600"></i>
                </div>
                <div class="font-medium text-gray-900 text-sm">Input Pengeluaran</div>
                <div class="text-xs text-gray-500 mt-1">Operasional & kasbon</div>
            </a>
        </div>
    </div>

    {{-- Recent Transactions --}}
    <div>
        <div class="flex items-center justify-between mb-3 px-1">
            <h3 class="text-sm font-semibold text-gray-900">Transaksi Terakhir</h3>
            <a href="{{ mobile_route('financial.cash-flow') }}" class="text-xs text-[#0077b5] font-medium">
                Lihat Semua →
            </a>
        </div>

        <div class="space-y-2">
            @forelse($recentTransactions as $transaction)
            <div class="bg-white rounded-xl p-4 border border-gray-200">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <div class="flex items-center gap-2 mb-1">
                            <div class="w-8 h-8 {{ $transaction['type'] === 'income' ? 'bg-green-50' : 'bg-red-50' }} rounded-lg flex items-center justify-center">
                                <i class="fas {{ $transaction['type'] === 'income' ? 'fa-arrow-down text-green-600' : 'fa-arrow-up text-red-600' }} text-sm"></i>
                            </div>
                            <div>
                                <div class="font-medium text-gray-900 text-sm">{{ $transaction['description'] }}</div>
                                <div class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($transaction['date'])->format('d M Y') }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="text-right">
                        <div class="font-bold {{ $transaction['type'] === 'income' ? 'text-green-600' : 'text-red-600' }}">
                            {{ $transaction['type'] === 'income' ? '+' : '-' }}Rp {{ number_format($transaction['amount'], 0, ',', '.') }}
                        </div>
                        @if($transaction['project_name'])
                        <div class="text-xs text-gray-500 mt-0.5">{{ $transaction['project_name'] }}</div>
                        @endif
                    </div>
                </div>
            </div>
            @empty
            <div class="text-center py-8 text-gray-500">
                <i class="fas fa-inbox text-3xl mb-2 opacity-50"></i>
                <div class="text-sm">Belum ada transaksi</div>
            </div>
            @endforelse
        </div>
    </div>

</div>
@endsection
