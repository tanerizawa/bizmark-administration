@extends('layouts.app')

@section('title', 'Rekonsiliasi Bank')
@section('page-title', 'Rekonsiliasi Bank')

@section('content')
<div class="max-w-full">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 space-y-3 sm:space-y-0">
        <div>
            <p class="text-sm" style="color: rgba(235, 235, 245, 0.6);">Rekonsiliasi bank statement dengan transaksi sistem</p>
        </div>
        <div class="flex items-center space-x-3">
            <a href="{{ route('reconciliations.create') }}" 
               class="inline-flex items-center px-4 py-2 rounded-apple text-sm font-medium shadow-sm transition-all hover:opacity-90"
               style="background: linear-gradient(135deg, rgba(0, 122, 255, 1) 0%, rgba(10, 132, 255, 1) 100%); color: white;">
                <i class="fas fa-plus mr-2"></i>
                Mulai Rekonsiliasi Baru
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <!-- Total Reconciliations -->
        <div class="rounded-apple p-5 shadow-sm" style="background: rgba(255, 255, 255, 0.05); backdrop-filter: blur(10px);">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-medium" style="color: rgba(235, 235, 245, 0.6);">Total Rekonsiliasi</span>
                <i class="fas fa-file-alt" style="color: rgba(0, 122, 255, 0.6);"></i>
            </div>
            <p class="text-2xl font-bold" style="color: rgba(235, 235, 245, 0.9);">
                {{ $reconciliations->total() }}
            </p>
        </div>

        <!-- Completed -->
        <div class="rounded-apple p-5 shadow-sm" style="background: rgba(255, 255, 255, 0.05); backdrop-filter: blur(10px);">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-medium" style="color: rgba(235, 235, 245, 0.6);">Selesai</span>
                <i class="fas fa-check-circle" style="color: rgba(52, 199, 89, 0.6);"></i>
            </div>
            <p class="text-2xl font-bold" style="color: rgba(52, 199, 89, 1);">
                {{ $reconciliations->where('status', 'completed')->count() }}
            </p>
        </div>

        <!-- In Progress -->
        <div class="rounded-apple p-5 shadow-sm" style="background: rgba(255, 255, 255, 0.05); backdrop-filter: blur(10px);">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-medium" style="color: rgba(235, 235, 245, 0.6);">Sedang Proses</span>
                <i class="fas fa-hourglass-half" style="color: rgba(255, 159, 10, 0.6);"></i>
            </div>
            <p class="text-2xl font-bold" style="color: rgba(255, 159, 10, 1);">
                {{ $reconciliations->where('status', 'in_progress')->count() }}
            </p>
        </div>

        <!-- Balanced -->
        <div class="rounded-apple p-5 shadow-sm" style="background: rgba(255, 255, 255, 0.05); backdrop-filter: blur(10px);">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-medium" style="color: rgba(235, 235, 245, 0.6);">Seimbang (Diff = 0)</span>
                <i class="fas fa-balance-scale" style="color: rgba(52, 199, 89, 0.6);"></i>
            </div>
            <p class="text-2xl font-bold" style="color: rgba(52, 199, 89, 1);">
                {{ $reconciliations->filter(fn($r) => $r->difference == 0)->count() }}
            </p>
        </div>
    </div>

    <!-- Filters -->
    <div class="rounded-apple p-4 mb-6" style="background: rgba(255, 255, 255, 0.05); backdrop-filter: blur(10px);">
        <form method="GET" action="{{ route('reconciliations.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- Cash Account Filter -->
            <div>
                <label class="block text-xs font-medium mb-2" style="color: rgba(235, 235, 245, 0.6);">Akun Kas</label>
                <select name="cash_account_id" 
                        class="w-full px-3 py-2 rounded-apple text-sm border-none focus:outline-none focus:ring-2"
                        style="background: rgba(255, 255, 255, 0.05); color: rgba(235, 235, 245, 0.9); backdrop-filter: blur(10px);">
                    <option value="">Semua Akun</option>
                    @foreach($cashAccounts as $account)
                        <option value="{{ $account->id }}" {{ request('cash_account_id') == $account->id ? 'selected' : '' }}>
                            {{ $account->account_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Status Filter -->
            <div>
                <label class="block text-xs font-medium mb-2" style="color: rgba(235, 235, 245, 0.6);">Status</label>
                <select name="status" 
                        class="w-full px-3 py-2 rounded-apple text-sm border-none focus:outline-none focus:ring-2"
                        style="background: rgba(255, 255, 255, 0.05); color: rgba(235, 235, 245, 0.9); backdrop-filter: blur(10px);">
                    <option value="">Semua Status</option>
                    <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>Sedang Proses</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Selesai</option>
                    <option value="reviewed" {{ request('status') == 'reviewed' ? 'selected' : '' }}>Direview</option>
                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Disetujui</option>
                </select>
            </div>

            <!-- Date From -->
            <div>
                <label class="block text-xs font-medium mb-2" style="color: rgba(235, 235, 245, 0.6);">Dari Tanggal</label>
                <input type="date" name="start_date" value="{{ request('start_date') }}"
                       class="w-full px-3 py-2 rounded-apple text-sm border-none focus:outline-none focus:ring-2"
                       style="background: rgba(255, 255, 255, 0.05); color: rgba(235, 235, 245, 0.9); backdrop-filter: blur(10px);">
            </div>

            <!-- Date To -->
            <div>
                <label class="block text-xs font-medium mb-2" style="color: rgba(235, 235, 245, 0.6);">Sampai Tanggal</label>
                <div class="flex space-x-2">
                    <input type="date" name="end_date" value="{{ request('end_date') }}"
                           class="flex-1 px-3 py-2 rounded-apple text-sm border-none focus:outline-none focus:ring-2"
                           style="background: rgba(255, 255, 255, 0.05); color: rgba(235, 235, 245, 0.9); backdrop-filter: blur(10px);">
                    <button type="submit" 
                            class="px-4 py-2 rounded-apple text-sm font-medium transition-all hover:opacity-90"
                            style="background: rgba(0, 122, 255, 0.2); color: rgba(0, 122, 255, 1);">
                        <i class="fas fa-filter"></i>
                    </button>
                    <a href="{{ route('reconciliations.index') }}" 
                       class="px-4 py-2 rounded-apple text-sm font-medium transition-all hover:opacity-90"
                       style="background: rgba(255, 255, 255, 0.05); color: rgba(235, 235, 245, 0.6);">
                        <i class="fas fa-times"></i>
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- Reconciliations Table -->
    <div class="rounded-apple overflow-hidden shadow-sm" style="background: rgba(255, 255, 255, 0.05); backdrop-filter: blur(10px);">
        <table class="w-full">
            <thead>
                <tr style="background: rgba(255, 255, 255, 0.02); border-bottom: 1px solid rgba(235, 235, 245, 0.1);">
                    <th class="px-4 py-3 text-left text-xs font-medium" style="color: rgba(235, 235, 245, 0.6);">Tanggal</th>
                    <th class="px-4 py-3 text-left text-xs font-medium" style="color: rgba(235, 235, 245, 0.6);">Akun Kas</th>
                    <th class="px-4 py-3 text-left text-xs font-medium" style="color: rgba(235, 235, 245, 0.6);">Periode</th>
                    <th class="px-4 py-3 text-right text-xs font-medium" style="color: rgba(235, 235, 245, 0.6);">Saldo Buku</th>
                    <th class="px-4 py-3 text-right text-xs font-medium" style="color: rgba(235, 235, 245, 0.6);">Saldo Bank</th>
                    <th class="px-4 py-3 text-right text-xs font-medium" style="color: rgba(235, 235, 245, 0.6);">Selisih</th>
                    <th class="px-4 py-3 text-center text-xs font-medium" style="color: rgba(235, 235, 245, 0.6);">Status</th>
                    <th class="px-4 py-3 text-center text-xs font-medium" style="color: rgba(235, 235, 245, 0.6);">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($reconciliations as $reconciliation)
                <tr style="border-bottom: 1px solid rgba(235, 235, 245, 0.05);" class="hover:bg-white/5 transition-colors">
                    <td class="px-4 py-3 text-sm" style="color: rgba(235, 235, 245, 0.9);">
                        {{ $reconciliation->reconciliation_date->format('d M Y') }}
                    </td>
                    <td class="px-4 py-3 text-sm" style="color: rgba(235, 235, 245, 0.9);">
                        <div class="font-medium">{{ $reconciliation->cashAccount->account_name }}</div>
                        <div class="text-xs" style="color: rgba(235, 235, 245, 0.6);">{{ $reconciliation->cashAccount->bank_name }}</div>
                    </td>
                    <td class="px-4 py-3 text-sm" style="color: rgba(235, 235, 245, 0.9);">
                        {{ $reconciliation->start_date->format('d M') }} - {{ $reconciliation->end_date->format('d M Y') }}
                    </td>
                    <td class="px-4 py-3 text-sm text-right font-medium" style="color: rgba(235, 235, 245, 0.9);">
                        Rp {{ number_format($reconciliation->closing_balance_book, 0, ',', '.') }}
                    </td>
                    <td class="px-4 py-3 text-sm text-right font-medium" style="color: rgba(235, 235, 245, 0.9);">
                        Rp {{ number_format($reconciliation->closing_balance_bank, 0, ',', '.') }}
                    </td>
                    <td class="px-4 py-3 text-sm text-right font-bold {{ $reconciliation->difference == 0 ? 'text-green-500' : 'text-red-500' }}">
                        Rp {{ number_format(abs($reconciliation->difference), 0, ',', '.') }}
                    </td>
                    <td class="px-4 py-3 text-center">
                        @if($reconciliation->status === 'in_progress')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium" style="background: rgba(255, 159, 10, 0.2); color: rgba(255, 159, 10, 1);">
                                <i class="fas fa-hourglass-half mr-1"></i> Proses
                            </span>
                        @elseif($reconciliation->status === 'completed')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium" style="background: rgba(52, 199, 89, 0.2); color: rgba(52, 199, 89, 1);">
                                <i class="fas fa-check-circle mr-1"></i> Selesai
                            </span>
                        @elseif($reconciliation->status === 'reviewed')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium" style="background: rgba(0, 122, 255, 0.2); color: rgba(0, 122, 255, 1);">
                                <i class="fas fa-eye mr-1"></i> Review
                            </span>
                        @elseif($reconciliation->status === 'approved')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium" style="background: rgba(52, 199, 89, 0.2); color: rgba(52, 199, 89, 1);">
                                <i class="fas fa-check-double mr-1"></i> Disetujui
                            </span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-center">
                        <div class="flex items-center justify-center space-x-2">
                            @if($reconciliation->status === 'in_progress')
                                <a href="{{ route('reconciliations.match', $reconciliation) }}" 
                                   class="text-blue-500 hover:text-blue-600 text-sm" title="Lanjutkan Matching">
                                    <i class="fas fa-exchange-alt"></i>
                                </a>
                            @else
                                <a href="{{ route('reconciliations.show', $reconciliation) }}" 
                                   class="text-blue-500 hover:text-blue-600 text-sm" title="Lihat Report">
                                    <i class="fas fa-file-alt"></i>
                                </a>
                            @endif
                            @if($reconciliation->status === 'in_progress')
                                <form action="{{ route('reconciliations.destroy', $reconciliation) }}" method="POST" class="inline"
                                      onsubmit="return confirm('Yakin ingin menghapus rekonsiliasi ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:text-red-600 text-sm" title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-4 py-8 text-center" style="color: rgba(235, 235, 245, 0.6);">
                        <i class="fas fa-inbox text-3xl mb-2"></i>
                        <p>Belum ada rekonsiliasi bank</p>
                        <a href="{{ route('reconciliations.create') }}" class="text-blue-500 hover:underline text-sm mt-2 inline-block">
                            Mulai rekonsiliasi pertama →
                        </a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($reconciliations->hasPages())
    <div class="mt-4">
        {{ $reconciliations->links() }}
    </div>
    @endif
</div>

@if(session('success'))
<div class="fixed bottom-4 right-4 bg-green-500 text-white px-6 py-3 rounded-apple shadow-lg z-50 animate-fade-in">
    <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
</div>
@endif

@if(session('error'))
<div class="fixed bottom-4 right-4 bg-red-500 text-white px-6 py-3 rounded-apple shadow-lg z-50 animate-fade-in">
    <i class="fas fa-exclamation-circle mr-2"></i> {{ session('error') }}
</div>
@endif
@endsection
