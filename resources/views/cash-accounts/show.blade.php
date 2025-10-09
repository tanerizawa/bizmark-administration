@extends('layouts.app')

@section('title', 'Mutasi - ' . $cashAccount->account_name)

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 space-y-3 sm:space-y-0">
        <div class="flex items-center">
            <a href="{{ route('cash-accounts.index') }}" class="mr-4" style="color: rgba(10, 132, 255, 0.9);">
                <i class="fas fa-arrow-left text-lg"></i>
            </a>
            <div>
                <h1 class="text-2xl font-bold" style="color: #FFFFFF;">Mutasi Rekening</h1>
                <p class="mt-1 text-sm" style="color: rgba(235, 235, 245, 0.6);">
                    {{ $cashAccount->account_name }}
                    @if($cashAccount->account_number)
                        <span class="ml-2">• {{ $cashAccount->account_number }}</span>
                    @endif
                </p>
            </div>
        </div>
        <div class="flex space-x-2">
            <a href="{{ route('cash-accounts.edit', $cashAccount) }}" 
               class="px-4 py-2 rounded-lg text-sm font-medium transition-colors" 
               style="background: rgba(255, 149, 0, 0.2); color: rgba(255, 149, 0, 1);">
                <i class="fas fa-edit mr-2"></i>Edit
            </a>
            <button onclick="window.print()" class="px-4 py-2 rounded-lg text-sm font-medium transition-colors" style="background: rgba(142, 142, 147, 0.2); color: rgba(235, 235, 245, 0.9);">
                <i class="fas fa-print mr-2"></i>Print
            </button>
            <button onclick="exportToCSV()" class="px-4 py-2 rounded-lg text-sm font-medium transition-colors" style="background: rgba(52, 199, 89, 0.2); color: rgba(52, 199, 89, 1);">
                <i class="fas fa-file-export mr-2"></i>Export CSV
            </button>
        </div>
    </div>

    <!-- Summary Cards -->
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
                <span class="text-sm" style="color: rgba(235, 235, 245, 0.6);">Total Pemasukan</span>
                <i class="fas fa-arrow-down" style="color: rgba(52, 199, 89, 1);"></i>
            </div>
            <p class="text-xl font-bold" style="color: rgba(52, 199, 89, 1);">
                Rp {{ number_format($totalIncome, 0, ',', '.') }}
            </p>
        </div>

        <div class="card-elevated rounded-apple-lg p-4">
            <div class="flex items-center justify-between mb-2">
                <span class="text-sm" style="color: rgba(235, 235, 245, 0.6);">Total Pengeluaran</span>
                <i class="fas fa-arrow-up" style="color: rgba(255, 59, 48, 1);"></i>
            </div>
            <p class="text-xl font-bold" style="color: rgba(255, 59, 48, 1);">
                Rp {{ number_format($totalExpense, 0, ',', '.') }}
            </p>
        </div>

        <div class="card-elevated rounded-apple-lg p-4">
            <div class="flex items-center justify-between mb-2">
                <span class="text-sm" style="color: rgba(235, 235, 245, 0.6);">Selisih</span>
                <i class="fas fa-exchange-alt" style="color: rgba(142, 142, 147, 1);"></i>
            </div>
            <p class="text-xl font-bold" style="color: {{ $netChange >= 0 ? 'rgba(52, 199, 89, 1)' : 'rgba(255, 59, 48, 1)' }};">
                {{ $netChange >= 0 ? '+' : '' }}Rp {{ number_format(abs($netChange), 0, ',', '.') }}
            </p>
        </div>
    </div>

    <!-- Filter Form -->
    <div class="card-elevated rounded-apple-lg p-6 mb-6">
        <form method="GET" action="{{ route('cash-accounts.show', $cashAccount) }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium mb-2" style="color: rgba(235, 235, 245, 0.6);">Jenis Transaksi</label>
                <select name="transaction_type" class="w-full px-4 py-2 rounded-lg text-sm transition-colors" style="background: rgba(44, 44, 46, 1); color: rgba(235, 235, 245, 0.9); border: 1px solid rgba(58, 58, 60, 0.8);">
                    <option value="all" {{ request('transaction_type', 'all') == 'all' ? 'selected' : '' }}>Semua Transaksi</option>
                    <option value="income" {{ request('transaction_type') == 'income' ? 'selected' : '' }}>Pemasukan</option>
                    <option value="expense" {{ request('transaction_type') == 'expense' ? 'selected' : '' }}>Pengeluaran</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium mb-2" style="color: rgba(235, 235, 245, 0.6);">Periode</label>
                <select name="filter_type" id="filterType" class="w-full px-4 py-2 rounded-lg text-sm transition-colors" style="background: rgba(44, 44, 46, 1); color: rgba(235, 235, 245, 0.9); border: 1px solid rgba(58, 58, 60, 0.8);">
                    <option value="month" {{ request('filter_type', 'month') == 'month' ? 'selected' : '' }}>Bulan Ini</option>
                    <option value="quarter" {{ request('filter_type') == 'quarter' ? 'selected' : '' }}>Kuartal Ini</option>
                    <option value="year" {{ request('filter_type') == 'year' ? 'selected' : '' }}>Tahun Ini</option>
                    <option value="custom" {{ request('filter_type') == 'custom' ? 'selected' : '' }}>Custom</option>
                </select>
            </div>

            <div id="customDates" style="display: {{ request('filter_type') == 'custom' ? 'block' : 'none' }};">
                <label class="block text-sm font-medium mb-2" style="color: rgba(235, 235, 245, 0.6);">Dari Tanggal</label>
                <input type="date" name="start_date" value="{{ request('start_date') }}" class="w-full px-4 py-2 rounded-lg text-sm" style="background: rgba(44, 44, 46, 1); color: rgba(235, 235, 245, 0.9); border: 1px solid rgba(58, 58, 60, 0.8);">
            </div>

            <div id="customDatesEnd" style="display: {{ request('filter_type') == 'custom' ? 'block' : 'none' }};">
                <label class="block text-sm font-medium mb-2" style="color: rgba(235, 235, 245, 0.6);">Sampai Tanggal</label>
                <input type="date" name="end_date" value="{{ request('end_date') }}" class="w-full px-4 py-2 rounded-lg text-sm" style="background: rgba(44, 44, 46, 1); color: rgba(235, 235, 245, 0.9); border: 1px solid rgba(58, 58, 60, 0.8);">
            </div>

            <div class="md:col-span-4 flex gap-2">
                <button type="submit" class="px-6 py-2 rounded-lg text-sm font-medium transition-colors" style="background: rgba(10, 132, 255, 1); color: #FFFFFF;">
                    <i class="fas fa-filter mr-2"></i>Filter
                </button>
                <a href="{{ route('cash-accounts.show', $cashAccount) }}" class="px-6 py-2 rounded-lg text-sm font-medium transition-colors" style="background: rgba(44, 44, 46, 1); color: rgba(235, 235, 245, 0.9);">
                    <i class="fas fa-undo mr-2"></i>Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Mutations Table -->
    <div class="card-elevated rounded-apple-lg p-6">
        <h3 class="text-lg font-semibold mb-4" style="color: #FFFFFF;">
            <i class="fas fa-list mr-2 text-apple-blue-dark"></i>Riwayat Mutasi ({{ $mutations->count() }} transaksi)
        </h3>

        @if($mutations && $mutations->count() > 0)
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr style="border-bottom: 1px solid rgba(58, 58, 60, 0.8);">
                        <th class="text-left py-3 px-2 text-sm font-medium" style="color: rgba(235, 235, 245, 0.6);">Tanggal</th>
                        <th class="text-left py-3 px-2 text-sm font-medium" style="color: rgba(235, 235, 245, 0.6);">Deskripsi</th>
                        <th class="text-left py-3 px-2 text-sm font-medium" style="color: rgba(235, 235, 245, 0.6);">Metode</th>
                        <th class="text-right py-3 px-2 text-sm font-medium" style="color: rgba(235, 235, 245, 0.6);">Debit</th>
                        <th class="text-right py-3 px-2 text-sm font-medium" style="color: rgba(235, 235, 245, 0.6);">Kredit</th>
                        <th class="text-right py-3 px-2 text-sm font-medium" style="color: rgba(235, 235, 245, 0.6);">Saldo</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($mutations as $mutation)
                    <tr style="border-bottom: 1px solid rgba(58, 58, 60, 0.4);">
                        <td class="py-3 px-2 text-sm" style="color: rgba(235, 235, 245, 0.9);">
                            {{ \Carbon\Carbon::parse($mutation['date'])->format('d M Y') }}
                        </td>
                        <td class="py-3 px-2 text-sm" style="color: rgba(235, 235, 245, 0.9);">
                            <div>{{ $mutation['description'] }}</div>
                            @if($mutation['reference'])
                            <div class="text-xs mt-1" style="color: rgba(235, 235, 245, 0.5);">{{ $mutation['reference'] }}</div>
                            @endif
                        </td>
                        <td class="py-3 px-2 text-sm">
                            @php
                                $methodConfig = match($mutation['payment_method'] ?? '') {
                                    'bank_transfer' => ['label' => 'Transfer Bank', 'color' => 'rgba(10, 132, 255, 1)', 'bg' => 'rgba(10, 132, 255, 0.2)'],
                                    'cash' => ['label' => 'Tunai', 'color' => 'rgba(52, 199, 89, 1)', 'bg' => 'rgba(52, 199, 89, 0.2)'],
                                    'check' => ['label' => 'Cek', 'color' => 'rgba(255, 149, 0, 1)', 'bg' => 'rgba(255, 149, 0, 0.2)'],
                                    'credit_card' => ['label' => 'Kartu Kredit', 'color' => 'rgba(175, 82, 222, 1)', 'bg' => 'rgba(175, 82, 222, 0.2)'],
                                    'debit_card' => ['label' => 'Kartu Debit', 'color' => 'rgba(94, 92, 230, 1)', 'bg' => 'rgba(94, 92, 230, 0.2)'],
                                    default => ['label' => 'Lainnya', 'color' => 'rgba(142, 142, 147, 1)', 'bg' => 'rgba(142, 142, 147, 0.2)']
                                };
                            @endphp
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full" 
                                  style="background: {{ $methodConfig['bg'] }}; color: {{ $methodConfig['color'] }};">
                                {{ $methodConfig['label'] }}
                            </span>
                        </td>
                        <td class="py-3 px-2 text-sm text-right font-semibold" style="color: {{ $mutation['type'] == 'income' ? 'rgba(52, 199, 89, 1)' : 'rgba(235, 235, 245, 0.3)' }};">
                            {{ $mutation['type'] == 'income' ? 'Rp ' . number_format($mutation['amount'], 0, ',', '.') : '-' }}
                        </td>
                        <td class="py-3 px-2 text-sm text-right font-semibold" style="color: {{ $mutation['type'] == 'expense' ? 'rgba(255, 59, 48, 1)' : 'rgba(235, 235, 245, 0.3)' }};">
                            {{ $mutation['type'] == 'expense' ? 'Rp ' . number_format($mutation['amount'], 0, ',', '.') : '-' }}
                        </td>
                        <td class="py-3 px-2 text-sm text-right font-mono" style="color: rgba(235, 235, 245, 0.8);">
                            Rp {{ number_format($mutation['balance'], 0, ',', '.') }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="text-center py-12">
            <i class="fas fa-inbox text-6xl mb-4" style="color: rgba(235, 235, 245, 0.3);"></i>
            <p class="text-lg font-medium mb-2" style="color: rgba(235, 235, 245, 0.6);">Tidak Ada Transaksi</p>
            <p class="text-sm" style="color: rgba(235, 235, 245, 0.4);">Belum ada transaksi untuk periode yang dipilih</p>
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
