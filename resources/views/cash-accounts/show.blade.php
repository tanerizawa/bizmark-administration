@extends('layouts.app')

@section('title', $cashAccount->account_name)

@section('content')
<div class="max-w-6xl mx-auto">
    <!-- Header -->
    <div class="flex justify-between items-start mb-6">
        <div class="flex items-center">
            <a href="{{ route('cash-accounts.index') }}" class="text-apple-blue-dark hover:text-apple-blue mr-4">
                <i class="fas fa-arrow-left text-lg"></i>
            </a>
            <div>
                <h1 class="text-3xl font-bold" style="color: #FFFFFF;">{{ $cashAccount->account_name }}</h1>
                <p class="mt-1" style="color: rgba(235, 235, 245, 0.6);">Detail Akun Kas & Bank</p>
            </div>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('cash-accounts.edit', $cashAccount) }}" 
               class="px-4 py-2 rounded-lg font-medium transition-colors" style="background: rgba(255, 149, 0, 0.9); color: #FFFFFF;">
                <i class="fas fa-edit mr-2"></i>Edit
            </a>
        </div>
    </div>

    <!-- Account Overview -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="card-elevated rounded-apple-lg p-4">
            <div class="flex items-center justify-between mb-2">
                <span class="text-sm" style="color: rgba(235, 235, 245, 0.6);">Saldo Saat Ini</span>
                <i class="fas fa-wallet text-apple-blue-dark"></i>
            </div>
            <p class="text-2xl font-bold" style="color: #FFFFFF;">
                {{ $cashAccount->formatted_balance }}
            </p>
        </div>

        <div class="card-elevated rounded-apple-lg p-4">
            <div class="flex items-center justify-between mb-2">
                <span class="text-sm" style="color: rgba(235, 235, 245, 0.6);">Saldo Awal</span>
                <i class="fas fa-calendar-alt" style="color: rgba(142, 142, 147, 1);"></i>
            </div>
            <p class="text-xl font-bold" style="color: rgba(235, 235, 245, 0.9);">
                Rp {{ number_format($cashAccount->initial_balance, 0, ',', '.') }}
            </p>
        </div>

        <div class="card-elevated rounded-apple-lg p-4">
            <div class="flex items-center justify-between mb-2">
                <span class="text-sm" style="color: rgba(235, 235, 245, 0.6);">Total Pemasukan</span>
                <i class="fas fa-arrow-down" style="color: rgba(52, 199, 89, 1);"></i>
            </div>
            <p class="text-xl font-bold" style="color: rgba(52, 199, 89, 1);">
                Rp {{ number_format($cashAccount->payments()->sum('amount'), 0, ',', '.') }}
            </p>
        </div>

        <div class="card-elevated rounded-apple-lg p-4">
            <div class="flex items-center justify-between mb-2">
                <span class="text-sm" style="color: rgba(235, 235, 245, 0.6);">Total Pengeluaran</span>
                <i class="fas fa-arrow-up" style="color: rgba(255, 59, 48, 1);"></i>
            </div>
            <p class="text-xl font-bold" style="color: rgba(255, 59, 48, 1);">
                Rp {{ number_format($cashAccount->expenses()->sum('amount'), 0, ',', '.') }}
            </p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Transaction History -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Payments -->
            <div class="card-elevated rounded-apple-lg p-6">
                <h3 class="text-lg font-semibold mb-4" style="color: #FFFFFF;">
                    <i class="fas fa-arrow-down mr-2" style="color: rgba(52, 199, 89, 1);"></i>Pemasukan ({{ $cashAccount->payments()->count() }})
                </h3>

                @if($cashAccount->payments && $cashAccount->payments->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr style="border-bottom: 1px solid rgba(58, 58, 60, 0.8);">
                                <th class="text-left py-3 px-2 text-sm font-medium" style="color: rgba(235, 235, 245, 0.6);">Tanggal</th>
                                <th class="text-left py-3 px-2 text-sm font-medium" style="color: rgba(235, 235, 245, 0.6);">Proyek</th>
                                <th class="text-left py-3 px-2 text-sm font-medium" style="color: rgba(235, 235, 245, 0.6);">Tipe</th>
                                <th class="text-right py-3 px-2 text-sm font-medium" style="color: rgba(235, 235, 245, 0.6);">Jumlah</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($cashAccount->payments as $payment)
                            <tr style="border-bottom: 1px solid rgba(58, 58, 60, 0.4);">
                                <td class="py-3 px-2 text-sm" style="color: rgba(235, 235, 245, 0.9);">
                                    {{ $payment->payment_date->format('d M Y') }}
                                </td>
                                <td class="py-3 px-2 text-sm" style="color: rgba(235, 235, 245, 0.9);">
                                    @if($payment->project)
                                    <a href="{{ route('projects.show', $payment->project) }}" class="text-apple-blue-dark hover:text-apple-blue">
                                        {{ Str::limit($payment->project->name, 30) }}
                                    </a>
                                    @else
                                    <span style="color: rgba(235, 235, 245, 0.4);">-</span>
                                    @endif
                                </td>
                                <td class="py-3 px-2 text-sm" style="color: rgba(235, 235, 245, 0.7);">
                                    {{ $payment->payment_type_name }}
                                </td>
                                <td class="py-3 px-2 text-sm text-right font-semibold" style="color: rgba(52, 199, 89, 1);">
                                    +{{ $payment->formatted_amount }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-8">
                    <i class="fas fa-inbox text-4xl mb-3" style="color: rgba(235, 235, 245, 0.3);"></i>
                    <p style="color: rgba(235, 235, 245, 0.6);">Belum ada pemasukan</p>
                </div>
                @endif
            </div>

            <!-- Expenses -->
            <div class="card-elevated rounded-apple-lg p-6">
                <h3 class="text-lg font-semibold mb-4" style="color: #FFFFFF;">
                    <i class="fas fa-arrow-up mr-2" style="color: rgba(255, 59, 48, 1);"></i>Pengeluaran ({{ $cashAccount->expenses()->count() }})
                </h3>

                @if($cashAccount->expenses && $cashAccount->expenses->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr style="border-bottom: 1px solid rgba(58, 58, 60, 0.8);">
                                <th class="text-left py-3 px-2 text-sm font-medium" style="color: rgba(235, 235, 245, 0.6);">Tanggal</th>
                                <th class="text-left py-3 px-2 text-sm font-medium" style="color: rgba(235, 235, 245, 0.6);">Proyek</th>
                                <th class="text-left py-3 px-2 text-sm font-medium" style="color: rgba(235, 235, 245, 0.6);">Kategori</th>
                                <th class="text-right py-3 px-2 text-sm font-medium" style="color: rgba(235, 235, 245, 0.6);">Jumlah</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($cashAccount->expenses as $expense)
                            <tr style="border-bottom: 1px solid rgba(58, 58, 60, 0.4);">
                                <td class="py-3 px-2 text-sm" style="color: rgba(235, 235, 245, 0.9);">
                                    {{ $expense->expense_date->format('d M Y') }}
                                </td>
                                <td class="py-3 px-2 text-sm" style="color: rgba(235, 235, 245, 0.9);">
                                    @if($expense->project)
                                    <a href="{{ route('projects.show', $expense->project) }}" class="text-apple-blue-dark hover:text-apple-blue">
                                        {{ Str::limit($expense->project->name, 30) }}
                                    </a>
                                    @else
                                    <span style="color: rgba(235, 235, 245, 0.4);">General</span>
                                    @endif
                                </td>
                                <td class="py-3 px-2 text-sm" style="color: rgba(235, 235, 245, 0.7);">
                                    {{ $expense->category_name }}
                                </td>
                                <td class="py-3 px-2 text-sm text-right font-semibold" style="color: rgba(255, 59, 48, 1);">
                                    -{{ $expense->formatted_amount }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-8">
                    <i class="fas fa-inbox text-4xl mb-3" style="color: rgba(235, 235, 245, 0.3);"></i>
                    <p style="color: rgba(235, 235, 245, 0.6);">Belum ada pengeluaran</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Account Information Sidebar -->
        <div class="space-y-6">
            <!-- Account Details -->
            <div class="card-elevated rounded-apple-lg p-6">
                <h3 class="text-lg font-semibold mb-4" style="color: #FFFFFF;">
                    <i class="fas fa-info-circle mr-2 text-apple-blue-dark"></i>Informasi Akun
                </h3>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium mb-1" style="color: rgba(235, 235, 245, 0.6);">Tipe Akun</label>
                        @php
                            $typeConfig = match($cashAccount->account_type) {
                                'bank' => ['label' => 'Bank', 'color' => 'rgba(10, 132, 255, 1)', 'bg' => 'rgba(10, 132, 255, 0.2)'],
                                'cash' => ['label' => 'Kas Tunai', 'color' => 'rgba(52, 199, 89, 1)', 'bg' => 'rgba(52, 199, 89, 0.2)'],
                                'receivable' => ['label' => 'Piutang', 'color' => 'rgba(255, 149, 0, 1)', 'bg' => 'rgba(255, 149, 0, 0.2)'],
                                'payable' => ['label' => 'Hutang', 'color' => 'rgba(255, 59, 48, 1)', 'bg' => 'rgba(255, 59, 48, 0.2)'],
                                default => ['label' => 'Lainnya', 'color' => 'rgba(142, 142, 147, 1)', 'bg' => 'rgba(142, 142, 147, 0.2)']
                            };
                        @endphp
                        <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full" 
                              style="background: {{ $typeConfig['bg'] }}; color: {{ $typeConfig['color'] }};">
                            {{ $typeConfig['label'] }}
                        </span>
                    </div>

                    @if($cashAccount->bank_name)
                    <div>
                        <label class="block text-sm font-medium mb-1" style="color: rgba(235, 235, 245, 0.6);">Nama Bank</label>
                        <p class="font-medium" style="color: rgba(235, 235, 245, 0.9);">{{ $cashAccount->bank_name }}</p>
                    </div>
                    @endif

                    @if($cashAccount->account_number)
                    <div>
                        <label class="block text-sm font-medium mb-1" style="color: rgba(235, 235, 245, 0.6);">Nomor Rekening</label>
                        <p class="font-mono" style="color: rgba(235, 235, 245, 0.9);">{{ $cashAccount->account_number }}</p>
                    </div>
                    @endif

                    <div>
                        <label class="block text-sm font-medium mb-1" style="color: rgba(235, 235, 245, 0.6);">Status</label>
                        @if($cashAccount->is_active)
                            <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full" 
                                  style="background: rgba(52, 199, 89, 0.2); color: rgba(52, 199, 89, 1);">
                                Aktif
                            </span>
                        @else
                            <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full" 
                                  style="background: rgba(142, 142, 147, 0.2); color: rgba(142, 142, 147, 1);">
                                Nonaktif
                            </span>
                        @endif
                    </div>

                    @if($cashAccount->notes)
                    <div>
                        <label class="block text-sm font-medium mb-1" style="color: rgba(235, 235, 245, 0.6);">Catatan</label>
                        <p class="text-sm whitespace-pre-line" style="color: rgba(235, 235, 245, 0.8);">{{ $cashAccount->notes }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Statistics -->
            <div class="card-elevated rounded-apple-lg p-6">
                <h3 class="text-lg font-semibold mb-4" style="color: #FFFFFF;">
                    <i class="fas fa-chart-bar mr-2 text-apple-blue-dark"></i>Statistik
                </h3>
                <div class="space-y-4">
                    <div class="flex justify-between items-center">
                        <span class="text-sm" style="color: rgba(235, 235, 245, 0.6);">Total Transaksi</span>
                        <span class="font-semibold" style="color: rgba(235, 235, 245, 0.9);">
                            {{ $cashAccount->payments()->count() + $cashAccount->expenses()->count() }}
                        </span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm" style="color: rgba(235, 235, 245, 0.6);">Pemasukan</span>
                        <span class="font-semibold" style="color: rgba(52, 199, 89, 1);">
                            {{ $cashAccount->payments()->count() }} transaksi
                        </span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm" style="color: rgba(235, 235, 245, 0.6);">Pengeluaran</span>
                        <span class="font-semibold" style="color: rgba(255, 59, 48, 1);">
                            {{ $cashAccount->expenses()->count() }} transaksi
                        </span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm" style="color: rgba(235, 235, 245, 0.6);">Dibuat</span>
                        <span class="text-sm" style="color: rgba(235, 235, 245, 0.6);">{{ $cashAccount->created_at->format('d M Y') }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm" style="color: rgba(235, 235, 245, 0.6);">Update Terakhir</span>
                        <span class="text-sm" style="color: rgba(235, 235, 245, 0.6);">{{ $cashAccount->updated_at->diffForHumans() }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
