{{-- Financial Tab - Extra Compact Design --}}

<!-- Budget Overview Cards - Compact with Full Numbers -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-2.5 mb-3">
    <!-- Total Budget -->
    <div class="data-block">
        <div class="flex items-center justify-between mb-1">
            <span class="text-xs font-medium" style="color: rgba(235, 235, 245, 0.6);">Nilai Kontrak</span>
            <i class="fas fa-wallet text-xs text-apple-blue-dark"></i>
        </div>
        <p class="text-sm font-bold mb-0.5 leading-tight" style="color: #FFFFFF;">
            Rp {{ number_format($totalBudget, 0, ',', '.') }}
        </p>
        <p class="text-xs" style="color: rgba(235, 235, 245, 0.5);">Total kontrak</p>
    </div>

    <!-- Total Received -->
    <div class="data-block">
        <div class="flex items-center justify-between mb-1">
            <span class="text-xs font-medium" style="color: rgba(235, 235, 245, 0.6);">Diterima</span>
            <i class="fas fa-hand-holding-usd text-xs" style="color: rgba(52, 199, 89, 1);"></i>
        </div>
        <p class="text-sm font-bold mb-0.5 leading-tight" style="color: rgba(52, 199, 89, 1);">
            Rp {{ number_format($totalReceived, 0, ',', '.') }}
        </p>
        <p class="text-xs" style="color: rgba(235, 235, 245, 0.5);">
            {{ $totalBudget > 0 ? number_format(($totalReceived / $totalBudget) * 100, 1) : 0 }}% terbayar
        </p>
    </div>

    <!-- Total Expenses -->
    <div class="data-block">
        <div class="flex items-center justify-between mb-1">
            <span class="text-xs font-medium" style="color: rgba(235, 235, 245, 0.6);">Pengeluaran</span>
            <i class="fas fa-shopping-cart text-xs" style="color: rgba(255, 59, 48, 1);"></i>
        </div>
        <p class="text-sm font-bold mb-0.5 leading-tight" style="color: rgba(255, 59, 48, 1);">
            Rp {{ number_format($totalExpenses, 0, ',', '.') }}
        </p>
        <p class="text-xs" style="color: rgba(235, 235, 245, 0.5);">
            {{ $totalBudget > 0 ? number_format(($totalExpenses / $totalBudget) * 100, 1) : 0 }}% dari kontrak
        </p>
    </div>

    <!-- Profit Margin -->
    <div class="data-block">
        <div class="flex items-center justify-between mb-1">
            <span class="text-xs font-medium" style="color: rgba(235, 235, 245, 0.6);">Profit</span>
            <i class="fas fa-chart-line text-xs" style="color: rgba(0, 122, 255, 1);"></i>
        </div>
        <p class="text-sm font-bold mb-0.5 leading-tight" style="color: {{ $profitMargin >= 0 ? 'rgba(52, 199, 89, 1)' : 'rgba(255, 59, 48, 1)' }};">
            {{ $profitMargin < 0 ? '-' : '' }}Rp {{ number_format(abs($profitMargin), 0, ',', '.') }}
        </p>
        <p class="text-xs" style="color: rgba(235, 235, 245, 0.5);">
            {{ $totalReceived > 0 ? number_format(($profitMargin / $totalReceived) * 100, 1) : 0 }}% margin
        </p>
    </div>
</div>

<!-- Secondary Metrics - Compact -->
<div class="grid grid-cols-1 md:grid-cols-2 gap-2.5 mb-3">
    <div class="data-block">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs mb-0.5" style="color: rgba(235, 235, 245, 0.6);">Total Invoice</p>
                <p class="text-sm font-bold" style="color: #FFFFFF;">Rp {{ number_format($totalInvoiced, 0, ',', '.') }}</p>
            </div>
            <i class="fas fa-file-invoice text-lg" style="color: rgba(0, 122, 255, 0.6);"></i>
        </div>
    </div>

    <div class="data-block">
        <div class="flex items-center justify-between">
            <div class="flex-1">
                <p class="text-xs mb-0.5" style="color: rgba(235, 235, 245, 0.6);">Kasbon Belum Lunas</p>
                <p class="text-sm font-bold" style="color: rgba(255, 149, 0, 1);">Rp {{ number_format($receivableOutstanding, 0, ',', '.') }}</p>
                @php
                    $kasbonCount = $project->expenses()
                        ->where('is_receivable', true)
                        ->whereIn('receivable_status', ['pending', 'partial'])
                        ->count();
                @endphp
                @if($kasbonCount > 0)
                <p class="text-xs mt-0.5" style="color: rgba(255, 149, 0, 0.7);">{{ $kasbonCount }} kasbon aktif</p>
                @endif
            </div>
            <i class="fas fa-hand-holding-usd text-lg" style="color: rgba(255, 149, 0, 0.6);"></i>
        </div>
    </div>
</div>

<!-- Financial Chart - Compact -->
<div class="page-card space-y-2 mb-3">
    <div class="flex items-center justify-between mb-2">
        <h3 class="text-sm font-semibold" style="color: #FFFFFF;">
            <i class="fas fa-chart-bar mr-2 text-apple-blue-dark"></i>Pemasukan vs Pengeluaran (6 Bulan)
        </h3>
    </div>
    <div class="relative" style="height: 250px;">
        <canvas id="financialChart"></canvas>
    </div>
</div>

<!-- Invoices Section - Compact -->
<div class="page-card space-y-2 mb-3">
    <div class="flex justify-between items-center mb-2">
        <h3 class="text-sm font-semibold" style="color: #FFFFFF;">
            <i class="fas fa-file-invoice mr-2 text-apple-blue-dark"></i>Daftar Invoice
        </h3>
        <button onclick="openInvoiceModal()" 
                class="btn-primary-sm" 
                style="background: rgba(0, 122, 255, 0.9); color: #FFFFFF;">
            <i class="fas fa-plus mr-1"></i>Tambah Invoice
        </button>
    </div>

    @if($project->invoices && $project->invoices->count() > 0)
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr style="border-bottom: 1px solid rgba(58, 58, 60, 0.8);">
                    <th class="text-left py-2 px-2 text-xs font-medium" style="color: rgba(235, 235, 245, 0.6);">No. Invoice</th>
                    <th class="text-left py-2 px-2 text-xs font-medium" style="color: rgba(235, 235, 245, 0.6);">Tanggal</th>
                    <th class="text-left py-2 px-2 text-xs font-medium" style="color: rgba(235, 235, 245, 0.6);">Jatuh Tempo</th>
                    <th class="text-right py-2 px-2 text-xs font-medium" style="color: rgba(235, 235, 245, 0.6);">Total</th>
                    <th class="text-right py-2 px-2 text-xs font-medium" style="color: rgba(235, 235, 245, 0.6);">Terbayar</th>
                    <th class="text-center py-2 px-2 text-xs font-medium" style="color: rgba(235, 235, 245, 0.6);">Status</th>
                    <th class="text-center py-2 px-2 text-xs font-medium" style="color: rgba(235, 235, 245, 0.6);">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($project->invoices as $invoice)
                <tr style="border-bottom: 1px solid rgba(58, 58, 60, 0.4);">
                    <td class="py-1.5 px-2 text-xs font-mono" style="color: rgba(0, 122, 255, 1);">
                        {{ $invoice->invoice_number }}
                    </td>
                    <td class="py-1.5 px-2 text-xs" style="color: rgba(235, 235, 245, 0.9);">
                        {{ $invoice->invoice_date->format('d M Y') }}
                    </td>
                    <td class="py-1.5 px-2 text-xs" style="color: rgba(235, 235, 245, 0.9);">
                        {{ $invoice->due_date->format('d M Y') }}
                    </td>
                    <td class="py-1.5 px-2 text-xs text-right font-semibold" style="color: #FFFFFF;">
                        Rp {{ number_format($invoice->total_amount, 0, ',', '.') }}
                    </td>
                    <td class="py-1.5 px-2 text-xs text-right font-semibold" style="color: rgba(52, 199, 89, 1);">
                        Rp {{ number_format($invoice->paid_amount, 0, ',', '.') }}
                    </td>
                    <td class="py-1.5 px-2 text-center">
                        @php
                            $badge = $invoice->status_badge;
                            $colors = [
                                'draft' => 'background: rgba(142, 142, 147, 0.3); color: rgba(142, 142, 147, 1);',
                                'sent' => 'background: rgba(0, 122, 255, 0.3); color: rgba(0, 122, 255, 1);',
                                'partial' => 'background: rgba(255, 149, 0, 0.3); color: rgba(255, 149, 0, 1);',
                                'paid' => 'background: rgba(52, 199, 89, 0.3); color: rgba(52, 199, 89, 1);',
                                'overdue' => 'background: rgba(255, 59, 48, 0.3); color: rgba(255, 59, 48, 1);',
                                'cancelled' => 'background: rgba(142, 142, 147, 0.3); color: rgba(142, 142, 147, 1);',
                            ];
                        @endphp
                        <span class="inline-flex px-2 py-0.5 text-xs font-semibold rounded-full" style="{{ $colors[$invoice->status] ?? $colors['draft'] }}">
                            {{ $badge['label'] }}
                        </span>
                    </td>
                    <td class="py-1.5 px-2 text-center">
                        <div class="flex items-center justify-center space-x-2">
                            @if($invoice->status !== 'paid' && $invoice->status !== 'cancelled')
                            <button onclick="openPaymentModal({{ $invoice->id }}, '{{ $invoice->invoice_number }}', {{ $invoice->remaining_amount }})" 
                                    class="text-xs hover:opacity-75" style="color: rgba(52, 199, 89, 1);" title="Catat Pembayaran">
                                <i class="fas fa-dollar-sign"></i>
                            </button>
                            @endif
                            <a href="{{ route('invoices.download-pdf', $invoice) }}" target="_blank"
                               class="text-xs hover:opacity-75" style="color: rgba(255, 149, 0, 1);" title="Download PDF">
                                <i class="fas fa-file-pdf"></i>
                            </a>
                            <button onclick="viewInvoice({{ $invoice->id }})" 
                                    class="text-xs hover:opacity-75" style="color: rgba(0, 122, 255, 1);" title="Lihat Detail">
                                <i class="fas fa-eye"></i>
                            </button>
                            @if($invoice->status === 'draft' || $invoice->status === 'cancelled')
                            <button onclick="deleteInvoice({{ $invoice->id }})" 
                                    class="text-xs hover:opacity-75" style="color: rgba(255, 59, 48, 1);" title="Hapus Invoice">
                                <i class="fas fa-trash"></i>
                            </button>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @else
    <div class="text-center py-8">
        <i class="fas fa-file-invoice text-3xl mb-2" style="color: rgba(235, 235, 245, 0.3);"></i>
        <p class="text-sm mb-1" style="color: rgba(235, 235, 245, 0.6);">Belum ada invoice</p>
        <p class="text-xs mb-3" style="color: rgba(235, 235, 245, 0.4);">Buat invoice pertama untuk melacak pembayaran</p>
        <button onclick="openInvoiceModal()" 
                class="text-xs px-4 py-2 rounded-lg font-medium transition-colors" 
                style="background: rgba(0, 122, 255, 0.9); color: #FFFFFF;">
            <i class="fas fa-plus mr-1"></i>Buat Invoice Pertama
        </button>
    </div>
    @endif
</div>

<!-- Direct Income Section - Pemasukan Langsung (Non-Invoice) -->
<div class="page-card space-y-2 mb-3">
    <div class="flex justify-between items-center mb-2">
        <div class="flex items-center gap-2">
            <h3 class="text-sm font-semibold" style="color: #FFFFFF;">
                <i class="fas fa-hand-holding-usd mr-2" style="color: rgba(52, 199, 89, 1);"></i>Pemasukan Langsung
            </h3>
            @if($totalDirectIncome > 0)
            <span class="text-xs px-2 py-1 rounded-lg font-semibold" style="background: rgba(52, 199, 89, 0.2); color: rgba(52, 199, 89, 1);">
                Total: Rp {{ number_format($totalDirectIncome, 0, ',', '.') }}
            </span>
            @endif
        </div>
        <button onclick="openDirectIncomeModal()" 
                class="btn-primary-sm" 
                style="background: rgba(52, 199, 89, 0.9); color: #FFFFFF; border-color: transparent;">
            <i class="fas fa-plus mr-1"></i>Tambah Pemasukan
        </button>
    </div>

    <div class="text-xs mb-2 px-3 py-2 rounded-lg" style="background: rgba(52, 199, 89, 0.1); border-left: 3px solid rgba(52, 199, 89, 1); color: rgba(235, 235, 245, 0.8);">
        <i class="fas fa-info-circle mr-1" style="color: rgba(52, 199, 89, 1);"></i>
        <strong>Pemasukan Langsung</strong> adalah pembayaran yang diterima tanpa invoice formal (contoh: uang muka, donasi, hibah, atau pembayaran cash langsung).
    </div>

    @if($directIncomes && $directIncomes->count() > 0)
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr style="border-bottom: 1px solid rgba(58, 58, 60, 0.8);">
                    <th class="text-left py-2 px-2 text-xs font-medium" style="color: rgba(235, 235, 245, 0.6);">Tanggal</th>
                    <th class="text-left py-2 px-2 text-xs font-medium" style="color: rgba(235, 235, 245, 0.6);">Deskripsi</th>
                    <th class="text-left py-2 px-2 text-xs font-medium" style="color: rgba(235, 235, 245, 0.6);">Metode Pembayaran</th>
                    <th class="text-left py-2 px-2 text-xs font-medium" style="color: rgba(235, 235, 245, 0.6);">Rekening/Kas</th>
                    <th class="text-right py-2 px-2 text-xs font-medium" style="color: rgba(235, 235, 245, 0.6);">Jumlah</th>
                    <th class="text-left py-2 px-2 text-xs font-medium" style="color: rgba(235, 235, 245, 0.6);">Dicatat Oleh</th>
                    <th class="text-center py-2 px-2 text-xs font-medium" style="color: rgba(235, 235, 245, 0.6);">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($directIncomes as $income)
                <tr style="border-bottom: 1px solid rgba(58, 58, 60, 0.4);">
                    <td class="py-1.5 px-2 text-xs" style="color: rgba(235, 235, 245, 0.9);">
                        {{ \Carbon\Carbon::parse($income->payment_date)->format('d M Y') }}
                    </td>
                    <td class="py-1.5 px-2 text-xs" style="color: rgba(235, 235, 245, 0.9);">
                        {{ $income->description }}
                        @if($income->reference_number)
                        <p class="text-xs mt-0.5" style="color: rgba(235, 235, 245, 0.5);">
                            Ref: {{ $income->reference_number }}
                        </p>
                        @endif
                    </td>
                    <td class="py-1.5 px-2 text-xs" style="color: rgba(235, 235, 245, 0.9);">
                        @php
                            $methodLabels = [
                                'bank_transfer' => 'Transfer Bank',
                                'cash' => 'Kas Tunai',
                                'check' => 'Cek',
                                'giro' => 'Giro',
                                'other' => 'Lainnya'
                            ];
                            $methodLabel = $methodLabels[$income->payment_method] ?? ucfirst($income->payment_method);
                        @endphp
                        <span class="inline-flex items-center">
                            <i class="fas fa-{{ $income->payment_method === 'cash' ? 'money-bill-wave' : ($income->payment_method === 'bank_transfer' ? 'university' : 'credit-card') }} mr-1.5" 
                               style="color: rgba(235, 235, 245, 0.4);"></i>
                            {{ $methodLabel }}
                        </span>
                    </td>
                    <td class="py-1.5 px-2 text-xs" style="color: rgba(235, 235, 245, 0.9);">
                        @if($income->bankAccount)
                            {{ $income->bankAccount->account_name }}
                            @if($income->bankAccount->account_number)
                            <span class="text-xs" style="color: rgba(235, 235, 245, 0.5);">
                                ({{ $income->bankAccount->account_number }})
                            </span>
                            @endif
                        @else
                            <span style="color: rgba(235, 235, 245, 0.5);">-</span>
                        @endif
                    </td>
                    <td class="py-1.5 px-2 text-xs text-right font-bold" style="color: rgba(52, 199, 89, 1);">
                        Rp {{ number_format($income->amount, 0, ',', '.') }}
                    </td>
                    <td class="py-1.5 px-2 text-xs" style="color: rgba(235, 235, 245, 0.7);">
                        {{ $income->createdBy ? $income->createdBy->name : '-' }}
                        <p class="text-xs mt-0.5" style="color: rgba(235, 235, 245, 0.4);">
                            {{ $income->created_at->format('d M Y H:i') }}
                        </p>
                    </td>
                    <td class="py-1.5 px-2 text-center">
                        <div class="flex items-center justify-center space-x-2">
                            <button onclick="editDirectIncome({{ $income->id }})" 
                                    class="text-xs hover:opacity-75" style="color: rgba(0, 122, 255, 1);" title="Edit">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button onclick="deleteDirectIncome({{ $income->id }})" 
                                    class="text-xs hover:opacity-75" style="color: rgba(255, 59, 48, 1);" title="Hapus">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @else
    <div class="text-center py-8">
        <i class="fas fa-hand-holding-usd text-3xl mb-2" style="color: rgba(52, 199, 89, 0.3);"></i>
        <p class="text-sm mb-1" style="color: rgba(235, 235, 245, 0.6);">Belum ada pemasukan langsung</p>
        <p class="text-xs mb-3" style="color: rgba(235, 235, 245, 0.4);">Catat pemasukan yang tidak terkait dengan invoice</p>
        <button onclick="openDirectIncomeModal()" 
                class="text-xs px-4 py-2 rounded-lg font-medium transition-colors" 
                style="background: rgba(52, 199, 89, 0.9); color: #FFFFFF;">
            <i class="fas fa-plus mr-1"></i>Tambah Pemasukan Pertama
        </button>
    </div>
    @endif
</div>

<!-- Unified Expenses Section with Smart Filters -->
<div class="page-card space-y-2">
    <div class="flex justify-between items-center mb-2">
        <h3 class="text-sm font-semibold" style="color: #FFFFFF;">
            <i class="fas fa-receipt mr-2 text-apple-blue-dark"></i>Pengeluaran & Kasbon
        </h3>
        <button onclick="openExpenseModal()" 
                class="text-xs px-2.5 py-1.5 rounded-lg font-medium transition-colors" 
                style="background: rgba(255, 59, 48, 0.9); color: #FFFFFF;">
            <i class="fas fa-plus mr-1"></i>Tambah
        </button>
    </div>

    @php
        // Calculate summary data for filters
        // Semua pengeluaran adalah tanggung jawab perusahaan (diambil dari kas/bank masuk)
        // Kecuali: kasbon/piutang internal yang harus dikembalikan oleh karyawan
        $allExpenses = $project->expenses;
        $kasbonExpenses = $allExpenses->where('is_receivable', true)->where('receivable_status', '!=', 'paid');
        $regularExpenses = $allExpenses->where('is_receivable', false);
        
        $totalAll = $allExpenses->sum('amount');
        $totalKasbon = $kasbonExpenses->sum(function($e) { return $e->amount - $e->receivable_paid_amount; });
        $totalRegular = $regularExpenses->sum('amount');
        
        $countAll = $allExpenses->count();
        $countKasbon = $kasbonExpenses->count();
        $countRegular = $regularExpenses->count();
    @endphp

    @if($countAll > 0)
    <!-- Compact Filter Tabs -->
    <div class="flex gap-2 mb-2 overflow-x-auto">
        <button onclick="filterExpenses('all')" 
                class="filter-tab px-3 py-1.5 rounded-lg text-xs font-medium transition-all whitespace-nowrap"
                style="background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 255, 255, 0.1); color: rgba(235, 235, 245, 0.9);">
            <i class="fas fa-list mr-1.5" style="color: rgba(235, 235, 245, 0.4);"></i>Semua ({{ $countAll }})
        </button>
        <button onclick="filterExpenses('kasbon')" 
                class="filter-tab px-3 py-1.5 rounded-lg text-xs font-medium transition-all whitespace-nowrap"
                style="background: rgba(255, 204, 0, 0.15); border: 1px solid rgba(255, 204, 0, 0.3); color: rgba(255, 204, 0, 1);">
            <i class="fas fa-money-bill-wave mr-1.5" style="color: rgba(255, 204, 0, 0.6);"></i>Kasbon/Piutang Internal ({{ $countKasbon }})
        </button>
        <button onclick="filterExpenses('regular')" 
                class="filter-tab px-3 py-1.5 rounded-lg text-xs font-medium transition-all whitespace-nowrap"
                style="background: rgba(10, 132, 255, 0.15); border: 1px solid rgba(10, 132, 255, 0.3); color: rgba(10, 132, 255, 1);">
            <i class="fas fa-file-invoice mr-1.5" style="color: rgba(10, 132, 255, 0.6);"></i>Pengeluaran Operasional ({{ $countRegular }})
        </button>
    </div>

    <!-- Unified Expenses Table -->
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr style="border-bottom: 1px solid rgba(58, 58, 60, 0.8);">
                    <th class="text-left py-2 px-2 text-xs font-medium" style="color: rgba(235, 235, 245, 0.6);">Tanggal</th>
                    <th class="text-left py-2 px-2 text-xs font-medium" style="color: rgba(235, 235, 245, 0.6);">Deskripsi</th>
                    <th class="text-left py-2 px-2 text-xs font-medium" style="color: rgba(235, 235, 245, 0.6);">Kategori</th>
                    <th class="text-center py-2 px-2 text-xs font-medium" style="color: rgba(235, 235, 245, 0.6);">Tipe</th>
                    <th class="text-right py-2 px-2 text-xs font-medium" style="color: rgba(235, 235, 245, 0.6);">Jumlah</th>
                    <th class="text-right py-2 px-2 text-xs font-medium" style="color: rgba(235, 235, 245, 0.6);">Dibayar</th>
                    <th class="text-center py-2 px-2 text-xs font-medium" style="color: rgba(235, 235, 245, 0.6);">Status</th>
                    <th class="text-center py-2 px-2 text-xs font-medium" style="color: rgba(235, 235, 245, 0.6);">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($allExpenses->sortByDesc('expense_date') as $expense)
                @php
                    // Determine expense type for filtering
                    // Hanya 2 tipe: kasbon (piutang internal) atau regular (operasional)
                    $expenseType = 'regular';
                    if ($expense->is_receivable && $expense->receivable_status != 'paid') {
                        $expenseType = 'kasbon';
                    }
                    
                    $remaining = $expense->amount - $expense->receivable_paid_amount;
                @endphp
                <tr class="expense-row" data-type="{{ $expenseType }}" style="border-bottom: 1px solid rgba(58, 58, 60, 0.4);">
                    <td class="py-1.5 px-2 text-xs" style="color: rgba(235, 235, 245, 0.9);">
                        {{ $expense->expense_date->format('d M Y') }}
                    </td>
                    <td class="py-1.5 px-2 text-xs" style="color: rgba(235, 235, 245, 0.9);">
                        {{ $expense->description }}
                        @if($expense->is_receivable && $expense->receivable_status != 'paid')
                        <p class="text-xs mt-0.5" style="color: rgba(235, 235, 245, 0.5);">
                            Dari: {{ $expense->receivable_from ?? '-' }}
                        </p>
                        @endif
                        @if($expense->notes)
                        <p class="text-xs mt-0.5" style="color: rgba(235, 235, 245, 0.5);">{{ Str::limit($expense->notes, 50) }}</p>
                        @endif
                    </td>
                    <td class="py-1.5 px-2 text-xs" style="color: rgba(235, 235, 245, 0.9);">
                        <span class="inline-flex items-center">
                            <i class="fas fa-{{ $expense->category_icon }} mr-1.5" style="color: rgba(235, 235, 245, 0.4);"></i>
                            <span>{{ $expense->category_name }}</span>
                        </span>
                    </td>
                    <td class="py-1.5 px-2 text-center">
                        @if($expense->is_receivable && $expense->receivable_status != 'paid')
                        <span class="inline-flex items-center px-2 py-0.5 text-xs rounded-full" style="background: rgba(255, 204, 0, 0.2); color: rgba(255, 204, 0, 1);">
                            <i class="fas fa-money-bill-wave mr-1" style="color: rgba(255, 204, 0, 0.6);"></i> Kasbon
                        </span>
                        @else
                        <span class="inline-flex items-center px-2 py-0.5 text-xs rounded-full" style="background: rgba(10, 132, 255, 0.2); color: rgba(10, 132, 255, 1);">
                            <i class="fas fa-file-invoice mr-1" style="color: rgba(10, 132, 255, 0.6);"></i> Operasional
                        </span>
                        @endif
                    </td>
                    <td class="py-1.5 px-2 text-xs text-right font-semibold" style="color: rgba(255, 59, 48, 1);">
                        Rp {{ number_format($expense->amount, 0, ',', '.') }}
                    </td>
                    <td class="py-1.5 px-2 text-xs text-right" style="color: rgba(52, 199, 89, 1);">
                        @if($expense->is_receivable && $expense->receivable_paid_amount > 0)
                        Rp {{ number_format($expense->receivable_paid_amount, 0, ',', '.') }}
                        @else
                        -
                        @endif
                    </td>
                    <td class="py-1.5 px-2 text-center">
                        @if($expense->is_receivable && $expense->receivable_status != 'paid')
                            @if($expense->receivable_status == 'pending')
                            <span class="inline-flex px-2 py-0.5 text-xs rounded-full" style="background: rgba(255, 69, 58, 0.2); color: rgba(255, 69, 58, 1);">
                                Belum Bayar
                            </span>
                            @elseif($expense->receivable_status == 'partial')
                            <span class="inline-flex px-2 py-0.5 text-xs rounded-full" style="background: rgba(255, 204, 0, 0.2); color: rgba(255, 204, 0, 1);">
                                Sebagian
                            </span>
                            @endif
                        @else
                        -
                        @endif
                    </td>
                    <td class="py-1.5 px-2 text-center">
                        <div class="flex justify-center space-x-1">
                            @if($expense->is_receivable && $expense->receivable_status != 'paid')
                            <button onclick="recordReceivablePayment({{ $expense->id }}, {{ $remaining }})" 
                                    class="text-xs px-2 py-1 rounded hover:opacity-75" 
                                    style="background: rgba(52, 199, 89, 0.2); color: rgba(52, 199, 89, 1);"
                                    title="Catat Pembayaran">
                                <i class="fas fa-money-bill"></i>
                            </button>
                            @endif
                            <button onclick="editExpense({{ $expense->id }})" 
                                    class="text-xs px-2 py-1 rounded hover:opacity-75" 
                                    style="background: rgba(10, 132, 255, 0.2); color: rgba(10, 132, 255, 1);"
                                    title="Edit">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button onclick="deleteExpense({{ $expense->id }})" 
                                    class="text-xs px-2 py-1 rounded hover:opacity-75" 
                                    style="background: rgba(255, 69, 58, 0.2); color: rgba(255, 69, 58, 1);"
                                    title="Hapus">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @else
    <div class="text-center py-6">
        <i class="fas fa-inbox text-3xl mb-2" style="color: rgba(235, 235, 245, 0.3);"></i>
        <p class="text-xs" style="color: rgba(235, 235, 245, 0.6);">Belum ada pengeluaran tercatat</p>
    </div>
    @endif
</div>



{{-- Modals will be added in the parent view --}}

<script>
// Financial Chart - Compact Version
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('financialChart');
    if (ctx) {
        const chartData = @json($monthlyData);
        
        console.log('=== FINANCIAL CHART DATA ===');
        console.log('Labels:', chartData.labels);
        console.log('Income:', chartData.income);
        console.log('Expenses:', chartData.expenses);
        
        // Debug individual values
        console.log('Income array length:', chartData.income.length);
        console.log('Income values:', chartData.income.map((v, i) => `${chartData.labels[i]}: ${v}`));
        
        // Check if there's any income
        const totalIncome = chartData.income.reduce((sum, val) => sum + val, 0);
        console.log('Total Income:', totalIncome);
        
        if (totalIncome === 0) {
            console.warn('⚠️ No income data! All values are zero.');
        } else {
            console.log('✅ Income data found:', totalIncome);
        }
        
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: chartData.labels,
                datasets: [
                    {
                        label: 'Pemasukan',
                        data: chartData.income,
                        backgroundColor: 'rgba(52, 199, 89, 0.8)',
                        borderColor: 'rgba(52, 199, 89, 1)',
                        borderWidth: 2,
                        borderRadius: 4,
                    },
                    {
                        label: 'Pengeluaran',
                        data: chartData.expenses,
                        backgroundColor: 'rgba(255, 59, 48, 0.8)',
                        borderColor: 'rgba(255, 59, 48, 1)',
                        borderWidth: 2,
                        borderRadius: 4,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        labels: {
                            color: 'rgba(235, 235, 245, 0.9)',
                            font: {
                                family: '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial',
                                size: 11
                            },
                            padding: 10,
                            usePointStyle: true,
                            pointStyle: 'circle',
                            boxWidth: 6,
                            boxHeight: 6
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(28, 28, 30, 0.95)',
                        titleColor: 'rgba(235, 235, 245, 0.9)',
                        bodyColor: 'rgba(235, 235, 245, 0.8)',
                        borderColor: 'rgba(58, 58, 60, 0.8)',
                        borderWidth: 1,
                        padding: 8,
                        displayColors: true,
                        titleFont: {
                            size: 11
                        },
                        bodyFont: {
                            size: 11
                        },
                        callbacks: {
                            label: function(context) {
                                return context.dataset.label + ': Rp ' + 
                                       new Intl.NumberFormat('id-ID').format(context.parsed.y);
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(58, 58, 60, 0.4)',
                            drawBorder: false
                        },
                        ticks: {
                            color: 'rgba(235, 235, 245, 0.6)',
                            font: {
                                family: '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto',
                                size: 10
                            },
                            callback: function(value) {
                                if (value >= 1000000) {
                                    return 'Rp ' + (value / 1000000).toFixed(0) + 'M';
                                } else if (value >= 1000) {
                                    return 'Rp ' + (value / 1000).toFixed(0) + 'K';
                                }
                                return 'Rp ' + value;
                            }
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            color: 'rgba(235, 235, 245, 0.6)',
                            font: {
                                family: '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto',
                                size: 10
                            }
                        }
                    }
                }
            }
        });
    }
});

// Filter expenses by type (kasbon vs operasional)
function filterExpenses(type) {
    const rows = document.querySelectorAll('.expense-row');
    const tabs = document.querySelectorAll('.filter-tab');
    
    // Update active state on tabs
    tabs.forEach(tab => {
        const tabType = tab.getAttribute('onclick').match(/filterExpenses\('(\w+)'\)/)[1];
        if (tabType === type) {
            // Active state
            tab.style.opacity = '1';
            tab.style.transform = 'scale(1.05)';
            if (tabType === 'all') {
                tab.style.background = 'rgba(255, 255, 255, 0.15)';
                tab.style.borderColor = 'rgba(255, 255, 255, 0.4)';
            } else if (tabType === 'kasbon') {
                tab.style.background = 'rgba(255, 204, 0, 0.3)';
                tab.style.borderColor = 'rgba(255, 204, 0, 0.6)';
            } else if (tabType === 'regular') {
                tab.style.background = 'rgba(10, 132, 255, 0.3)';
                tab.style.borderColor = 'rgba(10, 132, 255, 0.6)';
            }
        } else {
            // Inactive state
            tab.style.opacity = '0.7';
            tab.style.transform = 'scale(1)';
            if (tabType === 'all') {
                tab.style.background = 'rgba(255, 255, 255, 0.05)';
                tab.style.borderColor = 'rgba(255, 255, 255, 0.1)';
            } else if (tabType === 'kasbon') {
                tab.style.background = 'rgba(255, 204, 0, 0.15)';
                tab.style.borderColor = 'rgba(255, 204, 0, 0.3)';
            } else if (tabType === 'regular') {
                tab.style.background = 'rgba(10, 132, 255, 0.15)';
                tab.style.borderColor = 'rgba(10, 132, 255, 0.3)';
            }
        }
    });
    
    // Show/hide rows based on type
    rows.forEach(row => {
        if (type === 'all' || row.dataset.type === type) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}

// Direct Income Functions
function editDirectIncome(id) {
    // Open modal in edit mode
    window.openDirectIncomeModal(id);
}

function deleteDirectIncome(id) {
    if (!confirm('Apakah Anda yakin ingin menghapus pemasukan ini?')) {
        return;
    }
    
    fetch(`/projects/{{ $project->id }}/direct-income/${id}`, {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Pemasukan berhasil dihapus', 'success');
            setTimeout(() => location.reload(), 1000);
        } else {
            showNotification(data.message || 'Gagal menghapus pemasukan', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Terjadi kesalahan saat menghapus pemasukan', 'error');
    });
}

</script>
