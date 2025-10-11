@extends('layouts.app')

@section('title', 'Matching Transaksi')
@section('page-title', 'Matching Transaksi Rekonsiliasi')

@section('content')
<div class="max-w-full">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 space-y-3 sm:space-y-0">
        <div>
            <h2 class="text-xl font-bold mb-1" style="color: rgba(235, 235, 245, 0.9);">
                {{ $reconciliation->cashAccount->account_name }}
            </h2>
            <p class="text-sm" style="color: rgba(235, 235, 245, 0.6);">
                Periode: {{ $reconciliation->start_date->format('d M Y') }} - {{ $reconciliation->end_date->format('d M Y') }}
            </p>
        </div>
        <div class="flex items-center space-x-3">
            <a href="{{ route('reconciliations.index') }}" 
               class="px-4 py-2 rounded-apple text-sm font-medium transition-all"
               style="background: rgba(255, 255, 255, 0.05); color: rgba(235, 235, 245, 0.9);">
                <i class="fas fa-arrow-left mr-2"></i> Kembali
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="rounded-apple p-4" style="background: rgba(255, 255, 255, 0.05);">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs" style="color: rgba(235, 235, 245, 0.6);">Total Transaksi Bank</span>
                <i class="fas fa-university" style="color: rgba(0, 122, 255, 0.6);"></i>
            </div>
            <p class="text-2xl font-bold" style="color: rgba(235, 235, 245, 0.9);">{{ $stats['total'] }}</p>
        </div>

        <div class="rounded-apple p-4" style="background: rgba(52, 199, 89, 0.1);">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs" style="color: rgba(52, 199, 89, 0.8);">Sudah Cocok</span>
                <i class="fas fa-check-circle" style="color: rgba(52, 199, 89, 1);"></i>
            </div>
            <p class="text-2xl font-bold" style="color: rgba(52, 199, 89, 1);">{{ $stats['matched'] }}</p>
        </div>

        <div class="rounded-apple p-4" style="background: rgba(255, 159, 10, 0.1);">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs" style="color: rgba(255, 159, 10, 0.8);">Belum Cocok</span>
                <i class="fas fa-exclamation-triangle" style="color: rgba(255, 159, 10, 1);"></i>
            </div>
            <p class="text-2xl font-bold" style="color: rgba(255, 159, 10, 1);">{{ $stats['unmatched'] }}</p>
        </div>

        <div class="rounded-apple p-4" style="background: rgba(0, 122, 255, 0.1);">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs" style="color: rgba(0, 122, 255, 0.8);">Match Rate</span>
                <i class="fas fa-percentage" style="color: rgba(0, 122, 255, 1);"></i>
            </div>
            <p class="text-2xl font-bold" style="color: rgba(0, 122, 255, 1);">{{ number_format($stats['match_rate'], 1) }}%</p>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center space-x-3">
            <form action="{{ route('reconciliations.auto-match', $reconciliation) }}" method="POST" class="inline">
                @csrf
                <button type="submit" 
                        class="px-4 py-2 rounded-apple text-sm font-medium transition-all hover:opacity-90"
                        style="background: linear-gradient(135deg, rgba(52, 199, 89, 1) 0%, rgba(48, 209, 88, 1) 100%); color: white;">
                    <i class="fas fa-magic mr-2"></i> Auto-Match
                </button>
            </form>
        </div>

        @if($stats['unmatched'] == 0)
        <form action="{{ route('reconciliations.complete', $reconciliation) }}" method="POST" 
              onsubmit="return confirm('Yakin ingin menyelesaikan rekonsiliasi ini?');">
            @csrf
            <button type="submit" 
                    class="px-4 py-2 rounded-apple text-sm font-medium transition-all hover:opacity-90"
                    style="background: linear-gradient(135deg, rgba(0, 122, 255, 1) 0%, rgba(10, 132, 255, 1) 100%); color: white;">
                <i class="fas fa-check-double mr-2"></i> Selesaikan Rekonsiliasi
            </button>
        </form>
        @endif
    </div>

    <!-- Matching Interface -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Left Panel: System Transactions -->
        <div>
            <div class="rounded-apple p-4 mb-4" style="background: rgba(255, 255, 255, 0.05);">
                <h3 class="text-sm font-bold mb-2" style="color: rgba(235, 235, 245, 0.9);">
                    <i class="fas fa-desktop mr-2"></i> Transaksi Sistem (Belum Rekonsiliasi)
                </h3>
                <p class="text-xs" style="color: rgba(235, 235, 245, 0.6);">
                    {{ $systemPayments->count() + $systemExpenses->count() + $systemInvoicePayments->count() }} transaksi
                </p>
            </div>

            <div class="space-y-3 max-h-[600px] overflow-y-auto pr-2">
                <!-- Income Transactions -->
                @if($systemPayments->count() > 0 || $systemInvoicePayments->count() > 0)
                <div class="mb-4">
                    <h4 class="text-xs font-medium mb-2 px-2" style="color: rgba(52, 199, 89, 0.8);">
                        <i class="fas fa-arrow-down mr-1"></i> PEMASUKAN
                    </h4>
                    
                    @foreach($systemPayments as $payment)
                    <div class="rounded-apple p-3 mb-2 cursor-pointer hover:ring-2 hover:ring-blue-500 transition-all"
                         style="background: rgba(52, 199, 89, 0.05);"
                         data-type="payment"
                         data-id="{{ $payment->id }}"
                         data-date="{{ $payment->payment_date->format('Y-m-d') }}"
                         data-amount="{{ $payment->amount }}">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="flex items-center mb-1">
                                    <span class="text-xs font-medium" style="color: rgba(235, 235, 245, 0.9);">
                                        {{ $payment->payment_date->format('d M Y') }}
                                    </span>
                                    <span class="mx-2" style="color: rgba(235, 235, 245, 0.3);">•</span>
                                    <span class="text-xs" style="color: rgba(235, 235, 245, 0.6);">
                                        {{ $payment->payment_type }}
                                    </span>
                                </div>
                                <p class="text-xs mb-1" style="color: rgba(235, 235, 245, 0.7);">
                                    {{ Str::limit($payment->description ?? 'Payment', 50) }}
                                </p>
                                @if($payment->reference_number)
                                <p class="text-xs" style="color: rgba(235, 235, 245, 0.5);">
                                    Ref: {{ $payment->reference_number }}
                                </p>
                                @endif
                            </div>
                            <div class="text-right ml-3">
                                <p class="text-sm font-bold" style="color: rgba(52, 199, 89, 1);">
                                    +Rp {{ number_format($payment->amount, 0, ',', '.') }}
                                </p>
                            </div>
                        </div>
                    </div>
                    @endforeach

                    @foreach($systemInvoicePayments as $invoicePayment)
                    <div class="rounded-apple p-3 mb-2 cursor-pointer hover:ring-2 hover:ring-blue-500 transition-all"
                         style="background: rgba(52, 199, 89, 0.05);"
                         data-type="invoice_payment"
                         data-id="{{ $invoicePayment->id }}"
                         data-date="{{ $invoicePayment->paid_date->format('Y-m-d') }}"
                         data-amount="{{ $invoicePayment->amount }}">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="flex items-center mb-1">
                                    <span class="text-xs font-medium" style="color: rgba(235, 235, 245, 0.9);">
                                        {{ $invoicePayment->paid_date->format('d M Y') }}
                                    </span>
                                    <span class="mx-2" style="color: rgba(235, 235, 245, 0.3);">•</span>
                                    <span class="text-xs" style="color: rgba(235, 235, 245, 0.6);">
                                        Invoice Payment
                                    </span>
                                </div>
                                <p class="text-xs mb-1" style="color: rgba(235, 235, 245, 0.7);">
                                    Schedule #{{ $invoicePayment->id }}
                                </p>
                                @if($invoicePayment->reference_number)
                                <p class="text-xs" style="color: rgba(235, 235, 245, 0.5);">
                                    Ref: {{ $invoicePayment->reference_number }}
                                </p>
                                @endif
                            </div>
                            <div class="text-right ml-3">
                                <p class="text-sm font-bold" style="color: rgba(52, 199, 89, 1);">
                                    +Rp {{ number_format($invoicePayment->amount, 0, ',', '.') }}
                                </p>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif

                <!-- Expense Transactions -->
                @if($systemExpenses->count() > 0)
                <div class="mb-4">
                    <h4 class="text-xs font-medium mb-2 px-2" style="color: rgba(255, 69, 58, 0.8);">
                        <i class="fas fa-arrow-up mr-1"></i> PENGELUARAN
                    </h4>
                    
                    @foreach($systemExpenses as $expense)
                    <div class="rounded-apple p-3 mb-2 cursor-pointer hover:ring-2 hover:ring-blue-500 transition-all"
                         style="background: rgba(255, 69, 58, 0.05);"
                         data-type="expense"
                         data-id="{{ $expense->id }}"
                         data-date="{{ $expense->expense_date->format('Y-m-d') }}"
                         data-amount="{{ $expense->amount }}">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="flex items-center mb-1">
                                    <span class="text-xs font-medium" style="color: rgba(235, 235, 245, 0.9);">
                                        {{ $expense->expense_date->format('d M Y') }}
                                    </span>
                                    <span class="mx-2" style="color: rgba(235, 235, 245, 0.3);">•</span>
                                    <span class="text-xs" style="color: rgba(235, 235, 245, 0.6);">
                                        {{ $expense->category }}
                                    </span>
                                </div>
                                <p class="text-xs mb-1" style="color: rgba(235, 235, 245, 0.7);">
                                    {{ Str::limit($expense->description ?? 'Expense', 50) }}
                                </p>
                                @if($expense->vendor_name)
                                <p class="text-xs" style="color: rgba(235, 235, 245, 0.5);">
                                    Vendor: {{ $expense->vendor_name }}
                                </p>
                                @endif
                            </div>
                            <div class="text-right ml-3">
                                <p class="text-sm font-bold" style="color: rgba(255, 69, 58, 1);">
                                    -Rp {{ number_format($expense->amount, 0, ',', '.') }}
                                </p>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif

                @if($systemPayments->count() == 0 && $systemExpenses->count() == 0 && $systemInvoicePayments->count() == 0)
                <div class="text-center py-8" style="color: rgba(235, 235, 245, 0.5);">
                    <i class="fas fa-check-circle text-4xl mb-3" style="color: rgba(52, 199, 89, 0.5);"></i>
                    <p class="text-sm">Semua transaksi sudah direkonsiliasi!</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Right Panel: Bank Statement Entries -->
        <div>
            <div class="rounded-apple p-4 mb-4" style="background: rgba(255, 255, 255, 0.05);">
                <h3 class="text-sm font-bold mb-2" style="color: rgba(235, 235, 245, 0.9);">
                    <i class="fas fa-university mr-2"></i> Bank Statement
                </h3>
                <p class="text-xs" style="color: rgba(235, 235, 245, 0.6);">
                    {{ $reconciliation->bankStatementEntries->count() }} transaksi
                </p>
            </div>

            <div class="space-y-3 max-h-[600px] overflow-y-auto pr-2">
                @foreach($reconciliation->bankStatementEntries->sortBy('transaction_date') as $entry)
                <div class="rounded-apple p-3 relative"
                     style="background: {{ $entry->is_matched ? 'rgba(52, 199, 89, 0.1)' : 'rgba(255, 255, 255, 0.05)' }};"
                     id="bank-entry-{{ $entry->id }}">
                    
                    @if($entry->is_matched)
                    <!-- Matched Badge -->
                    <div class="absolute top-2 right-2">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium"
                              style="background: rgba(52, 199, 89, 0.2); color: rgba(52, 199, 89, 1);">
                            <i class="fas fa-check mr-1"></i> Matched
                        </span>
                    </div>
                    @else
                    <!-- Match Button -->
                    <button onclick="openMatchModal({{ $entry->id }}, '{{ $entry->transaction_date->format('Y-m-d') }}', {{ $entry->credit_amount > 0 ? $entry->credit_amount : $entry->debit_amount }}, '{{ $entry->credit_amount > 0 ? 'income' : 'expense' }}')"
                            class="absolute top-2 right-2 px-2 py-1 rounded text-xs font-medium transition-all hover:opacity-90"
                            style="background: rgba(0, 122, 255, 0.2); color: rgba(0, 122, 255, 1);">
                        <i class="fas fa-link mr-1"></i> Match
                    </button>
                    @endif

                    <div class="flex items-start justify-between pr-20">
                        <div class="flex-1">
                            <div class="flex items-center mb-1">
                                <span class="text-xs font-medium" style="color: rgba(235, 235, 245, 0.9);">
                                    {{ $entry->transaction_date->format('d M Y') }}
                                </span>
                                @if($entry->is_matched)
                                <span class="mx-2" style="color: rgba(235, 235, 245, 0.3);">•</span>
                                <span class="text-xs" style="color: rgba(52, 199, 89, 0.8);">
                                    {{ ucfirst($entry->match_confidence ?? 'matched') }}
                                </span>
                                @endif
                            </div>
                            <p class="text-xs mb-1" style="color: rgba(235, 235, 245, 0.7);">
                                {{ Str::limit($entry->description, 50) }}
                            </p>
                            @if($entry->reference_number)
                            <p class="text-xs" style="color: rgba(235, 235, 245, 0.5);">
                                Ref: {{ $entry->reference_number }}
                            </p>
                            @endif
                            @if($entry->is_matched && $entry->match_notes)
                            <p class="text-xs mt-1" style="color: rgba(235, 235, 245, 0.5);">
                                <i class="fas fa-sticky-note mr-1"></i> {{ $entry->match_notes }}
                            </p>
                            @endif
                        </div>
                        <div class="text-right">
                            @if($entry->credit_amount > 0)
                            <p class="text-sm font-bold" style="color: rgba(52, 199, 89, 1);">
                                +Rp {{ number_format($entry->credit_amount, 0, ',', '.') }}
                            </p>
                            @else
                            <p class="text-sm font-bold" style="color: rgba(255, 69, 58, 1);">
                                -Rp {{ number_format($entry->debit_amount, 0, ',', '.') }}
                            </p>
                            @endif
                        </div>
                    </div>

                    @if($entry->is_matched)
                    <!-- Unmatch Button -->
                    <form action="{{ route('reconciliations.unmatch', $reconciliation) }}" method="POST" class="mt-2"
                          onsubmit="return confirm('Yakin ingin membatalkan matching ini?');">
                        @csrf
                        <input type="hidden" name="bank_entry_id" value="{{ $entry->id }}">
                        <button type="submit" class="text-xs hover:underline" style="color: rgba(255, 69, 58, 0.8);">
                            <i class="fas fa-unlink mr-1"></i> Unmatch
                        </button>
                    </form>
                    @endif
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<!-- Manual Match Modal -->
<div id="matchModal" class="fixed inset-0 z-50 hidden items-center justify-center p-4" style="background: rgba(0, 0, 0, 0.7);">
    <div class="rounded-apple p-6 max-w-2xl w-full max-h-[80vh] overflow-y-auto" style="background: rgba(30, 30, 30, 1);">
        <h3 class="text-lg font-bold mb-4" style="color: rgba(235, 235, 245, 0.9);">
            Manual Match Transaksi
        </h3>

        <form action="{{ route('reconciliations.manual-match', $reconciliation) }}" method="POST" id="matchForm">
            @csrf
            <input type="hidden" name="bank_entry_id" id="modal_bank_entry_id">

            <div class="mb-4 p-3 rounded-apple" style="background: rgba(0, 122, 255, 0.1);">
                <p class="text-xs mb-1" style="color: rgba(0, 122, 255, 0.8);">Bank Statement Entry:</p>
                <p class="text-sm font-medium" style="color: rgba(235, 235, 245, 0.9);" id="modal_bank_info"></p>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium mb-2" style="color: rgba(235, 235, 245, 0.9);">
                    Pilih Transaksi Sistem <span class="text-red-500">*</span>
                </label>
                <select name="transaction_type" id="modal_transaction_type" required
                        class="w-full px-3 py-2 rounded-apple text-sm border-none focus:outline-none focus:ring-2"
                        style="background: rgba(255, 255, 255, 0.05); color: rgba(235, 235, 245, 0.9);"
                        onchange="updateTransactionList()">
                    <option value="">Pilih tipe transaksi</option>
                    <option value="payment">Payment</option>
                    <option value="expense">Expense</option>
                    <option value="invoice_payment">Invoice Payment</option>
                </select>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium mb-2" style="color: rgba(235, 235, 245, 0.9);">
                    Transaction ID <span class="text-red-500">*</span>
                </label>
                <select name="transaction_id" id="modal_transaction_id" required
                        class="w-full px-3 py-2 rounded-apple text-sm border-none focus:outline-none focus:ring-2"
                        style="background: rgba(255, 255, 255, 0.05); color: rgba(235, 235, 245, 0.9);">
                    <option value="">Pilih transaksi</option>
                </select>
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium mb-2" style="color: rgba(235, 235, 245, 0.9);">
                    Catatan (Optional)
                </label>
                <textarea name="notes" rows="3"
                          class="w-full px-3 py-2 rounded-apple text-sm border-none focus:outline-none focus:ring-2"
                          style="background: rgba(255, 255, 255, 0.05); color: rgba(235, 235, 245, 0.9);"
                          placeholder="Catatan tambahan untuk matching ini..."></textarea>
            </div>

            <div class="flex items-center justify-end space-x-3">
                <button type="button" onclick="closeMatchModal()"
                        class="px-4 py-2 rounded-apple text-sm font-medium transition-all"
                        style="background: rgba(255, 255, 255, 0.05); color: rgba(235, 235, 245, 0.9);">
                    Batal
                </button>
                <button type="submit"
                        class="px-4 py-2 rounded-apple text-sm font-medium transition-all hover:opacity-90"
                        style="background: linear-gradient(135deg, rgba(0, 122, 255, 1) 0%, rgba(10, 132, 255, 1) 100%); color: white;">
                    <i class="fas fa-check mr-2"></i> Match
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// Store transaction data for modal
const systemPayments = @json($systemPayments);
const systemExpenses = @json($systemExpenses);
const systemInvoicePayments = @json($systemInvoicePayments);

function openMatchModal(bankEntryId, date, amount, type) {
    document.getElementById('modal_bank_entry_id').value = bankEntryId;
    document.getElementById('modal_bank_info').textContent = `${date} - Rp ${amount.toLocaleString('id-ID')} (${type})`;
    
    // Pre-select transaction type based on bank entry type
    const transactionTypeSelect = document.getElementById('modal_transaction_type');
    if (type === 'income') {
        transactionTypeSelect.value = 'payment';
    } else {
        transactionTypeSelect.value = 'expense';
    }
    
    updateTransactionList();
    
    document.getElementById('matchModal').classList.remove('hidden');
    document.getElementById('matchModal').classList.add('flex');
}

function closeMatchModal() {
    document.getElementById('matchModal').classList.add('hidden');
    document.getElementById('matchModal').classList.remove('flex');
    document.getElementById('matchForm').reset();
}

function updateTransactionList() {
    const type = document.getElementById('modal_transaction_type').value;
    const select = document.getElementById('modal_transaction_id');
    
    select.innerHTML = '<option value="">Pilih transaksi</option>';
    
    let transactions = [];
    if (type === 'payment') {
        transactions = systemPayments;
    } else if (type === 'expense') {
        transactions = systemExpenses;
    } else if (type === 'invoice_payment') {
        transactions = systemInvoicePayments;
    }
    
    transactions.forEach(trans => {
        const option = document.createElement('option');
        option.value = trans.id;
        
        let date, amount, description;
        if (type === 'payment') {
            date = trans.payment_date;
            amount = trans.amount;
            description = trans.description || 'Payment';
        } else if (type === 'expense') {
            date = trans.expense_date;
            amount = trans.amount;
            description = trans.description || 'Expense';
        } else if (type === 'invoice_payment') {
            date = trans.paid_date;
            amount = trans.amount;
            description = `Invoice Payment #${trans.id}`;
        }
        
        option.textContent = `${date} - Rp ${parseFloat(amount).toLocaleString('id-ID')} - ${description.substring(0, 40)}`;
        select.appendChild(option);
    });
}

// Close modal when clicking outside
document.getElementById('matchModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeMatchModal();
    }
});
</script>

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
