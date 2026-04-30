@extends('layouts.app')

@section('title', 'Mutasi - ' . $cashAccount->account_name)

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 space-y-3 sm:space-y-0">
        <div class="flex items-center">
            <a href="{{ route('cash-accounts.index') }}" class="mr-4 text-apple-blue/90">
                <i class="fas fa-arrow-left text-lg"></i>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-white">Mutasi Rekening</h1>
                <p class="mt-1 text-sm text-dark-text-secondary">
                    {{ $cashAccount->account_name }}
                    @if($cashAccount->account_number)
                        <span class="ml-2">• {{ $cashAccount->account_number }}</span>
                    @endif
                </p>
            </div>
        </div>
        <div class="flex space-x-2">
            <a href="{{ route('cash-accounts.edit', $cashAccount) }}"
               class="px-4 py-2 rounded-lg text-sm font-medium transition-colors bg-apple-orange/20 text-apple-orange">
                <i class="fas fa-edit mr-2"></i>Edit
            </a>
            <button onclick="window.print()" class="px-4 py-2 rounded-lg text-sm font-medium transition-colors bg-white/10 text-dark-text-primary/90">
                <i class="fas fa-print mr-2"></i>Print
            </button>
            <button onclick="exportToCSV()" class="px-4 py-2 rounded-lg text-sm font-medium transition-colors bg-apple-green/20 text-apple-green">
                <i class="fas fa-file-export mr-2"></i>Export CSV
            </button>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="card-elevated rounded-apple-lg p-4">
            <div class="flex items-center justify-between mb-2">
                <span class="text-sm text-dark-text-secondary">Saldo Saat Ini</span>
                <i class="fas fa-wallet text-apple-blue-dark"></i>
            </div>
            <p class="text-2xl font-bold text-white">
                {{ $cashAccount->formatted_balance }}
            </p>
        </div>

        <div class="card-elevated rounded-apple-lg p-4">
            <div class="flex items-center justify-between mb-2">
                <span class="text-sm text-dark-text-secondary">Total Pemasukan</span>
                <i class="fas fa-arrow-down text-apple-green"></i>
            </div>
            <p class="text-xl font-bold text-apple-green">
                Rp {{ number_format($totalIncome, 0, ',', '.') }}
            </p>
        </div>

        <div class="card-elevated rounded-apple-lg p-4">
            <div class="flex items-center justify-between mb-2">
                <span class="text-sm text-dark-text-secondary">Total Pengeluaran</span>
                <i class="fas fa-arrow-up text-apple-red"></i>
            </div>
            <p class="text-xl font-bold text-apple-red">
                Rp {{ number_format($totalExpense, 0, ',', '.') }}
            </p>
        </div>

        <div class="card-elevated rounded-apple-lg p-4">
            <div class="flex items-center justify-between mb-2">
                <span class="text-sm text-dark-text-secondary">Selisih</span>
                <i class="fas fa-exchange-alt text-dark-text-tertiary"></i>
            </div>
            <p class="text-xl font-bold {{ $netChange >= 0 ? 'text-apple-green' : 'text-apple-red' }}">
                {{ $netChange >= 0 ? '+' : '' }}Rp {{ number_format(abs($netChange), 0, ',', '.') }}
            </p>
        </div>
    </div>

    <!-- Filter Form -->
    <div class="card-elevated rounded-apple-lg p-6 mb-6">
        <form method="GET" action="{{ route('cash-accounts.show', $cashAccount) }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium mb-2 text-dark-text-secondary">Jenis Transaksi</label>
                <select name="transaction_type" class="w-full px-4 py-2 rounded-lg text-sm transition-colors bg-dark-bg-tertiary text-dark-text-primary border border-white/10">
                    <option value="all" {{ request('transaction_type', 'all') == 'all' ? 'selected' : '' }}>Semua Transaksi</option>
                    <option value="income" {{ request('transaction_type') == 'income' ? 'selected' : '' }}>Pemasukan</option>
                    <option value="expense" {{ request('transaction_type') == 'expense' ? 'selected' : '' }}>Pengeluaran</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium mb-2 text-dark-text-secondary">Periode</label>
                <select name="filter_type" id="filterType" class="w-full px-4 py-2 rounded-lg text-sm transition-colors bg-dark-bg-tertiary text-dark-text-primary border border-white/10">
                    <option value="month" {{ request('filter_type', 'month') == 'month' ? 'selected' : '' }}>Bulan Ini</option>
                    <option value="quarter" {{ request('filter_type') == 'quarter' ? 'selected' : '' }}>Kuartal Ini</option>
                    <option value="year" {{ request('filter_type') == 'year' ? 'selected' : '' }}>Tahun Ini</option>
                    <option value="custom" {{ request('filter_type') == 'custom' ? 'selected' : '' }}>Custom</option>
                </select>
            </div>

            <div id="customDates" style="display: {{ request('filter_type') == 'custom' ? 'block' : 'none' }};">
                <label class="block text-sm font-medium mb-2 text-dark-text-secondary">Dari Tanggal</label>
                <input type="date" name="start_date" value="{{ request('start_date') }}" class="w-full px-4 py-2 rounded-lg text-sm bg-dark-bg-tertiary text-dark-text-primary border border-white/10">
            </div>

            <div id="customDatesEnd" style="display: {{ request('filter_type') == 'custom' ? 'block' : 'none' }};">
                <label class="block text-sm font-medium mb-2 text-dark-text-secondary">Sampai Tanggal</label>
                <input type="date" name="end_date" value="{{ request('end_date') }}" class="w-full px-4 py-2 rounded-lg text-sm bg-dark-bg-tertiary text-dark-text-primary border border-white/10">
            </div>

            <div class="md:col-span-4 flex gap-2">
                <button type="submit" class="px-6 py-2 rounded-lg text-sm font-medium transition-colors bg-apple-blue text-white">
                    <i class="fas fa-filter mr-2"></i>Filter
                </button>
                <a href="{{ route('cash-accounts.show', $cashAccount) }}" class="px-6 py-2 rounded-lg text-sm font-medium transition-colors bg-dark-bg-tertiary text-dark-text-primary">
                    <i class="fas fa-undo mr-2"></i>Reset
                </a>
            </div>
        </form>
    </div>

    @php
        $totalUnassigned = ($unassignedInvoicePayments ?? 0) + ($unassignedExpenses ?? 0);
    @endphp

    @if($totalUnassigned > 0)
    <div class="mb-6 p-4 rounded-apple-lg bg-apple-orange/15 border border-apple-orange/30">
        <div class="flex items-start space-x-3">
            <i class="fas fa-exclamation-triangle mt-1 text-apple-orange"></i>
            <div>
                <p class="text-sm font-semibold text-apple-orange">Transaksi Tidak Terasosiasi</p>
                <p class="text-xs mt-1 text-dark-text-primary/70">
                    Terdapat <strong>{{ $totalUnassigned }}</strong> transaksi yang belum ditetapkan ke akun kas manapun dalam periode ini.
                    @if(($unassignedInvoicePayments ?? 0) > 0)
                        <br>• Pembayaran Invoice tidak terasosiasi: <strong>{{ number_format($unassignedInvoicePayments, 0, ',', '.') }} transaksi</strong> (Rp {{ number_format($unassignedInvoiceTotal ?? 0, 0, ',', '.') }})
                    @endif
                    @if(($unassignedExpenses ?? 0) > 0)
                        <br>• Pengeluaran tidak terasosiasi: <strong>{{ number_format($unassignedExpenses, 0, ',', '.') }} transaksi</strong> (Rp {{ number_format($unassignedExpenseTotal ?? 0, 0, ',', '.') }})
                    @endif
                </p>
                <p class="text-xs mt-2 text-dark-text-tertiary/80">
                    <i class="fas fa-info-circle mr-1"></i>Transaksi ini tidak mempengaruhi saldo rekening manapun.
                    Edit transaksi dan tetapkan akun kas untuk melacaknya.
                </p>
            </div>
        </div>
    </div>
    @endif

    <!-- Mutations Table -->
    <div class="card-elevated rounded-apple-lg p-6">
        <h3 class="text-lg font-semibold mb-4 text-white">
            <i class="fas fa-list mr-2 text-apple-blue-dark"></i>Riwayat Mutasi ({{ $mutations->count() }} transaksi)
        </h3>

        @if($mutations && $mutations->count() > 0)
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-white/10">
                        <th class="text-left py-3 px-2 text-sm font-medium text-dark-text-secondary">Tanggal</th>
                        <th class="text-left py-3 px-2 text-sm font-medium text-dark-text-secondary">Deskripsi</th>
                        <th class="text-left py-3 px-2 text-sm font-medium text-dark-text-secondary">Metode</th>
                        <th class="text-right py-3 px-2 text-sm font-medium text-dark-text-secondary">Debit</th>
                        <th class="text-right py-3 px-2 text-sm font-medium text-dark-text-secondary">Kredit</th>
                        <th class="text-right py-3 px-2 text-sm font-medium text-dark-text-secondary">Saldo</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($mutations as $mutation)
                    <tr class="border-b border-white/5">
                        <td class="py-3 px-2 text-sm text-dark-text-primary">
                            {{ \Carbon\Carbon::parse($mutation['date'])->format('d M Y') }}
                        </td>
                        <td class="py-3 px-2 text-sm text-dark-text-primary">
                            <div>{{ $mutation['description'] }}</div>
                            @if($mutation['reference'])
                            <div class="text-xs mt-1 text-dark-text-tertiary/80">{{ $mutation['reference'] }}</div>
                            @endif
                        </td>
                        <td class="py-3 px-2 text-sm">
                            @php
                                $methodConfig = match($mutation['payment_method'] ?? '') {
                                    'bank_transfer' => ['label' => 'Transfer Bank', 'class' => 'bg-apple-blue/20 text-apple-blue'],
                                    'cash' => ['label' => 'Tunai', 'class' => 'bg-apple-green/20 text-apple-green'],
                                    'check' => ['label' => 'Cek', 'class' => 'bg-apple-orange/20 text-apple-orange'],
                                    'credit_card' => ['label' => 'Kartu Kredit', 'class' => 'bg-purple-500/20 text-purple-400'],
                                    'debit_card' => ['label' => 'Kartu Debit', 'class' => 'bg-indigo-500/20 text-indigo-400'],
                                    default => ['label' => 'Lainnya', 'class' => 'bg-dark-text-tertiary/20 text-dark-text-tertiary']
                                };
                            @endphp
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $methodConfig['class'] }}">
                                {{ $methodConfig['label'] }}
                            </span>
                        </td>
                        <td class="py-3 px-2 text-sm text-right font-semibold {{ $mutation['type'] == 'income' ? 'text-apple-green' : 'text-dark-text-tertiary/30' }}">
                            {{ $mutation['type'] == 'income' ? 'Rp ' . number_format($mutation['amount'], 0, ',', '.') : '-' }}
                        </td>
                        <td class="py-3 px-2 text-sm text-right font-semibold {{ $mutation['type'] == 'expense' ? 'text-apple-red' : 'text-dark-text-tertiary/30' }}">
                            {{ $mutation['type'] == 'expense' ? 'Rp ' . number_format($mutation['amount'], 0, ',', '.') : '-' }}
                        </td>
                        <td class="py-3 px-2 text-sm text-right font-mono text-dark-text-primary/80">
                            Rp {{ number_format($mutation['balance'], 0, ',', '.') }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="text-center py-12">
            <i class="fas fa-inbox text-6xl mb-4 text-dark-text-tertiary/30"></i>
            <p class="text-lg font-medium mb-2 text-dark-text-secondary">Tidak Ada Transaksi</p>
            <p class="text-sm text-dark-text-tertiary/60">Belum ada transaksi untuk periode yang dipilih</p>
        </div>
        @endif
    </div>

    <script>
        // Toggle custom date fields
        document.getElementById('filterType').addEventListener('change', function() {
            const customDates = document.getElementById('customDates');
            const customDatesEnd = document.getElementById('customDatesEnd');
            if (this.value === 'custom') {
                customDates.style.display = 'block';
                customDatesEnd.style.display = 'block';
            } else {
                customDates.style.display = 'none';
                customDatesEnd.style.display = 'none';
            }
        });

        // Export to CSV
        function exportToCSV() {
            const cashAccountName = '{{ $cashAccount->account_name }}';
            const startDate = '{{ $startDate }}';
            const endDate = '{{ $endDate }}';
            
            let csv = 'Tanggal,Deskripsi,Referensi,Metode,Debit,Kredit,Saldo\n';
            
            @foreach($mutations as $mutation)
            csv += '"{{ \Carbon\Carbon::parse($mutation['date'])->format('d M Y') }}",';
            csv += '"{{ str_replace('"', '""', $mutation['description']) }}",';
            csv += '"{{ str_replace('"', '""', $mutation['reference'] ?? '') }}",';
            csv += '"{{ $mutation['payment_method'] ?? '' }}",';
            csv += '{{ $mutation['type'] == 'income' ? $mutation['amount'] : '0' }},';
            csv += '{{ $mutation['type'] == 'expense' ? $mutation['amount'] : '0' }},';
            csv += '{{ $mutation['balance'] }}\n';
            @endforeach
            
            const blob = new Blob([csv], { type: 'text/csv' });
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `mutasi_${cashAccountName}_${startDate}_${endDate}.csv`;
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            window.URL.revokeObjectURL(url);
        }

        // Print function
        window.onbeforeprint = function() {
            document.body.classList.add('printing');
        };
        
        window.onafterprint = function() {
            document.body.classList.remove('printing');
        };
    </script>

    <style>
        @media print {
            body.printing * {
                visibility: hidden;
            }
            body.printing .card-elevated {
                visibility: visible;
                position: absolute;
                left: 0;
                top: 0;
            }
        }
    </style>
</div>
@endsection
