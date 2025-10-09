@extends('layouts.app')

@section('title', 'Akun Kas & Bank')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold" style="color: #FFFFFF;">Akun Kas & Bank</h1>
            <p class="mt-1" style="color: rgba(235, 235, 245, 0.6);">Kelola rekening bank dan kas perusahaan</p>
        </div>
        <a href="{{ route('cash-accounts.create') }}" 
           class="px-4 py-2 rounded-lg font-medium transition-colors" style="background: rgba(52, 199, 89, 0.9); color: #FFFFFF;">
            <i class="fas fa-plus mr-2"></i>Tambah Akun
        </a>
    </div>

    <!-- Success Message -->
    @if(session('success'))
    <div class="mb-6 px-4 py-3 rounded-apple-lg" style="background: rgba(52, 199, 89, 0.2); border-left: 4px solid rgba(52, 199, 89, 1);">
        <p style="color: rgba(52, 199, 89, 1);">
            <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
        </p>
    </div>
    @endif

    @if(session('error'))
    <div class="mb-6 px-4 py-3 rounded-apple-lg" style="background: rgba(255, 59, 48, 0.2); border-left: 4px solid rgba(255, 59, 48, 1);">
        <p style="color: rgba(255, 59, 48, 1);">
            <i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}
        </p>
    </div>
    @endif

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        @php
            $totalBalance = $accounts->sum('current_balance');
            $bankAccounts = $accounts->where('account_type', 'bank')->sum('current_balance');
            $cashAccounts = $accounts->where('account_type', 'cash')->sum('current_balance');
            $receivables = $accounts->where('account_type', 'receivable')->sum('current_balance');
        @endphp

        <div class="card-elevated rounded-apple-lg p-4">
            <div class="flex items-center justify-between mb-2">
                <span class="text-sm" style="color: rgba(235, 235, 245, 0.6);">Total Saldo</span>
                <i class="fas fa-wallet text-apple-blue-dark"></i>
            </div>
            <p class="text-xl font-bold" style="color: #FFFFFF;">
                Rp {{ number_format($totalBalance, 0, ',', '.') }}
            </p>
        </div>

        <div class="card-elevated rounded-apple-lg p-4">
            <div class="flex items-center justify-between mb-2">
                <span class="text-sm" style="color: rgba(235, 235, 245, 0.6);">Bank</span>
                <i class="fas fa-university" style="color: rgba(10, 132, 255, 1);"></i>
            </div>
            <p class="text-xl font-bold" style="color: rgba(10, 132, 255, 1);">
                Rp {{ number_format($bankAccounts, 0, ',', '.') }}
            </p>
        </div>

        <div class="card-elevated rounded-apple-lg p-4">
            <div class="flex items-center justify-between mb-2">
                <span class="text-sm" style="color: rgba(235, 235, 245, 0.6);">Kas Tunai</span>
                <i class="fas fa-money-bill-wave" style="color: rgba(52, 199, 89, 1);"></i>
            </div>
            <p class="text-xl font-bold" style="color: rgba(52, 199, 89, 1);">
                Rp {{ number_format($cashAccounts, 0, ',', '.') }}
            </p>
        </div>

        <div class="card-elevated rounded-apple-lg p-4">
            <div class="flex items-center justify-between mb-2">
                <span class="text-sm" style="color: rgba(235, 235, 245, 0.6);">Piutang</span>
                <i class="fas fa-clock" style="color: rgba(255, 149, 0, 1);"></i>
            </div>
            <p class="text-xl font-bold" style="color: rgba(255, 149, 0, 1);">
                Rp {{ number_format($receivables, 0, ',', '.') }}
            </p>
        </div>
    </div>

    <!-- Accounts List -->
    <div class="card-elevated rounded-apple-lg p-6">
        <h3 class="text-lg font-semibold mb-4" style="color: #FFFFFF;">
            <i class="fas fa-list mr-2 text-apple-blue-dark"></i>Daftar Akun ({{ $accounts->count() }})
        </h3>

        @if($accounts->count() > 0)
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr style="border-bottom: 1px solid rgba(58, 58, 60, 0.8);">
                        <th class="text-left py-3 px-2 text-sm font-medium" style="color: rgba(235, 235, 245, 0.6);">Nama Akun</th>
                        <th class="text-left py-3 px-2 text-sm font-medium" style="color: rgba(235, 235, 245, 0.6);">Tipe</th>
                        <th class="text-left py-3 px-2 text-sm font-medium" style="color: rgba(235, 235, 245, 0.6);">Bank/No. Rekening</th>
                        <th class="text-right py-3 px-2 text-sm font-medium" style="color: rgba(235, 235, 245, 0.6);">Saldo Awal</th>
                        <th class="text-right py-3 px-2 text-sm font-medium" style="color: rgba(235, 235, 245, 0.6);">Saldo Saat Ini</th>
                        <th class="text-center py-3 px-2 text-sm font-medium" style="color: rgba(235, 235, 245, 0.6);">Status</th>
                        <th class="text-center py-3 px-2 text-sm font-medium" style="color: rgba(235, 235, 245, 0.6);">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($accounts as $account)
                    <tr style="border-bottom: 1px solid rgba(58, 58, 60, 0.4);">
                        <td class="py-3 px-2">
                            <p class="font-medium" style="color: rgba(235, 235, 245, 0.9);">{{ $account->account_name }}</p>
                            @if($account->notes)
                            <p class="text-xs mt-1" style="color: rgba(235, 235, 245, 0.5);">{{ Str::limit($account->notes, 50) }}</p>
                            @endif
                        </td>
                        <td class="py-3 px-2">
                            @php
                                $typeConfig = match($account->account_type) {
                                    'bank' => ['label' => 'Bank', 'color' => 'rgba(10, 132, 255, 1)', 'bg' => 'rgba(10, 132, 255, 0.2)'],
                                    'cash' => ['label' => 'Kas', 'color' => 'rgba(52, 199, 89, 1)', 'bg' => 'rgba(52, 199, 89, 0.2)'],
                                    'receivable' => ['label' => 'Piutang', 'color' => 'rgba(255, 149, 0, 1)', 'bg' => 'rgba(255, 149, 0, 0.2)'],
                                    'payable' => ['label' => 'Hutang', 'color' => 'rgba(255, 59, 48, 1)', 'bg' => 'rgba(255, 59, 48, 0.2)'],
                                    default => ['label' => 'Lain', 'color' => 'rgba(142, 142, 147, 1)', 'bg' => 'rgba(142, 142, 147, 0.2)']
                                };
                            @endphp
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full" 
                                  style="background: {{ $typeConfig['bg'] }}; color: {{ $typeConfig['color'] }};">
                                {{ $typeConfig['label'] }}
                            </span>
                        </td>
                        <td class="py-3 px-2 text-sm" style="color: rgba(235, 235, 245, 0.9);">
                            @if($account->bank_name)
                                <p>{{ $account->bank_name }}</p>
                                @if($account->account_number)
                                <p class="text-xs" style="color: rgba(235, 235, 245, 0.6);">{{ $account->account_number }}</p>
                                @endif
                            @else
                                <span style="color: rgba(235, 235, 245, 0.4);">-</span>
                            @endif
                        </td>
                        <td class="py-3 px-2 text-sm text-right" style="color: rgba(235, 235, 245, 0.7);">
                            Rp {{ number_format($account->initial_balance, 0, ',', '.') }}
                        </td>
                        <td class="py-3 px-2 text-sm text-right font-semibold" style="color: #FFFFFF;">
                            Rp {{ number_format($account->current_balance, 0, ',', '.') }}
                        </td>
                        <td class="py-3 px-2 text-center">
                            @if($account->is_active)
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full" 
                                      style="background: rgba(52, 199, 89, 0.2); color: rgba(52, 199, 89, 1);">
                                    Aktif
                                </span>
                            @else
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full" 
                                      style="background: rgba(142, 142, 147, 0.2); color: rgba(142, 142, 147, 1);">
                                    Nonaktif
                                </span>
                            @endif
                        </td>
                        <td class="py-3 px-2 text-center">
                            <div class="flex items-center justify-center space-x-2">
                                <a href="{{ route('cash-accounts.show', $account) }}" 
                                   class="text-apple-blue-dark hover:text-apple-blue" title="Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('cash-accounts.edit', $account) }}" 
                                   class="hover:opacity-80" style="color: rgba(255, 149, 0, 1);" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('cash-accounts.destroy', $account) }}" method="POST" 
                                      class="inline" onsubmit="return confirm('Hapus akun {{ $account->account_name }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" style="color: rgba(255, 59, 48, 1);" 
                                            class="hover:opacity-80" title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="text-center py-12">
            <i class="fas fa-wallet text-5xl mb-4" style="color: rgba(235, 235, 245, 0.3);"></i>
            <p class="text-lg mb-2" style="color: rgba(235, 235, 245, 0.6);">Belum ada akun kas</p>
            <p class="text-sm mb-4" style="color: rgba(235, 235, 245, 0.4);">Tambahkan akun bank atau kas untuk mulai tracking keuangan</p>
            <a href="{{ route('cash-accounts.create') }}" 
               class="inline-block px-6 py-3 rounded-lg font-medium transition-colors" 
               style="background: rgba(52, 199, 89, 0.9); color: #FFFFFF;">
                <i class="fas fa-plus mr-2"></i>Tambah Akun Pertama
            </a>
        </div>
        @endif
    </div>
</div>
@endsection
