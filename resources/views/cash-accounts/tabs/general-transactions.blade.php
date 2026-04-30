{{-- Keuangan Umum (Non-Project Transactions) --}}
<div>
    <div class="mb-3 flex justify-between items-center">
        <div>
            <h3 class="text-base font-semibold text-white">
                <i class="fas fa-briefcase mr-2 text-dark-text-tertiary/40"></i>
                Keuangan Umum Perusahaan
            </h3>
            <p class="text-xs mt-0.5 text-dark-text-tertiary/80">
                Pemasukan dan pengeluaran yang tidak terkait dengan proyek tertentu
            </p>
        </div>
        <div class="flex gap-2">
            <button onclick="openGeneralIncomeModal()"
                    class="btn-apple-primary text-sm">
                <i class="fas fa-plus-circle mr-2"></i>
                Pemasukan Umum
            </button>
            <button onclick="openGeneralExpenseModal()"
                    class="btn-secondary-sm">
                <i class="fas fa-minus-circle mr-2"></i>
                Pengeluaran Umum
            </button>
        </div>
    </div>

    {{-- Summary Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-3 mb-4">
        <div class="p-3 rounded-apple bg-apple-green/8">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs mb-0.5 text-dark-text-tertiary/80">Total Pemasukan Umum</p>
                    <p class="text-lg font-bold text-apple-green">
                        @php
                            $generalIncome = $generalTransactions['income'] ?? collect();
                            $totalIncome = $generalIncome->sum('amount');
                        @endphp
                        Rp {{ number_format($totalIncome) }}
                    </p>
                </div>
                <div class="h-10 w-10 flex items-center justify-center rounded-full bg-apple-green/15">
                    <i class="fas fa-arrow-down text-sm text-apple-green/90"></i>
                </div>
            </div>
        </div>

        <div class="p-3 rounded-apple bg-apple-red/8">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs mb-0.5 text-dark-text-tertiary/80">Total Pengeluaran Umum</p>
                    <p class="text-lg font-bold text-apple-red">
                        @php
                            $generalExpenses = $generalTransactions['expenses'] ?? collect();
                            $totalExpenses = $generalExpenses->sum('amount');
                        @endphp
                        Rp {{ number_format($totalExpenses) }}
                    </p>
                </div>
                <div class="h-10 w-10 flex items-center justify-center rounded-full bg-apple-red/15">
                    <i class="fas fa-arrow-up text-sm text-apple-red/90"></i>
                </div>
            </div>
        </div>

        <div class="p-3 rounded-apple bg-apple-blue/8">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs mb-0.5 text-dark-text-tertiary/80">Selisih</p>
                    @php $netGeneral = $totalIncome - $totalExpenses; @endphp
                    <p class="text-lg font-bold {{ $netGeneral >= 0 ? 'text-apple-green' : 'text-apple-red' }}">
                        {{ $netGeneral >= 0 ? '+' : '' }}Rp {{ number_format($netGeneral) }}
                    </p>
                </div>
                <div class="h-10 w-10 flex items-center justify-center rounded-full bg-apple-blue/15">
                    <i class="fas fa-balance-scale text-sm text-apple-blue/90"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Transactions List --}}
    @if($generalIncome->count() > 0 || $generalExpenses->count() > 0)
        <div class="space-y-4">
            {{-- General Income Section --}}
            @if($generalIncome->count() > 0)
            <div class="card-elevated rounded-apple-lg p-4">
                <h4 class="text-sm font-semibold mb-3 text-dark-text-primary/90">
                    <i class="fas fa-plus-circle mr-2 text-apple-green/80"></i>
                    Pemasukan Umum ({{ $generalIncome->count() }})
                </h4>
                <div class="space-y-2">
                    @foreach($generalIncome as $income)
                    <div class="p-3 rounded-apple bg-apple-green/[0.05] border-l-[3px] border-l-apple-green/50">
                        <div class="flex justify-between items-start">
                            <div class="flex-1">
                                <div class="flex items-center gap-2">
                                    <span class="text-xs font-medium text-dark-text-primary/90">
                                        {{ \Carbon\Carbon::parse($income->payment_date)->isoFormat('D MMM Y') }}
                                    </span>
                                    <span class="px-2 py-0.5 text-xs rounded-full bg-apple-green/15 text-apple-green">
                                        {{ ucfirst($income->payment_method) }}
                                    </span>
                                </div>
                                <p class="text-sm mt-1 text-dark-text-primary/85">
                                    {{ $income->description ?? 'Pemasukan Umum' }}
                                </p>
                                @if($income->bankAccount)
                                <p class="text-xs mt-1 text-dark-text-tertiary/80">
                                    <i class="fas fa-university text-xs mr-1"></i>
                                    {{ $income->bankAccount->account_name }}
                                </p>
                                @endif
                            </div>
                            <div class="text-right">
                                <div class="text-sm font-bold text-apple-green">
                                    +Rp {{ number_format($income->amount) }}
                                </div>
                                <div class="flex gap-1 mt-2">
                                    <button onclick="editGeneralIncome({{ $income->id }})"
                                            class="text-xs px-2 py-1 rounded hover:bg-white/10 text-dark-text-secondary">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button onclick="deleteGeneralIncome({{ $income->id }})"
                                            class="text-xs px-2 py-1 rounded hover:bg-red-500/20 text-apple-red/80">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- General Expenses Section --}}
            @if($generalExpenses->count() > 0)
            <div class="card-elevated rounded-apple-lg p-4">
                <h4 class="text-sm font-semibold mb-3 text-dark-text-primary/90">
                    <i class="fas fa-minus-circle mr-2 text-apple-red/80"></i>
                    Pengeluaran Umum ({{ $generalExpenses->count() }})
                </h4>
                <div class="space-y-2">
                    @foreach($generalExpenses as $expense)
                    <div class="p-3 rounded-apple bg-apple-red/[0.05] border-l-[3px] border-l-apple-red/50">
                        <div class="flex justify-between items-start">
                            <div class="flex-1">
                                <div class="flex items-center gap-2">
                                    <span class="text-xs font-medium text-dark-text-primary/90">
                                        {{ \Carbon\Carbon::parse($expense->expense_date)->isoFormat('D MMM Y') }}
                                    </span>
                                    <span class="px-2 py-0.5 text-xs rounded-full bg-apple-orange/15 text-apple-orange">
                                        {{ $expense->category_name }}
                                    </span>
                                </div>
                                <p class="text-sm mt-1 text-dark-text-primary/85">
                                    {{ $expense->description ?? $expense->vendor_name ?? 'Pengeluaran Umum' }}
                                </p>
                                @if($expense->bankAccount)
                                <p class="text-xs mt-1 text-dark-text-tertiary/80">
                                    <i class="fas fa-university text-xs mr-1"></i>
                                    {{ $expense->bankAccount->account_name }}
                                </p>
                                @endif
                            </div>
                            <div class="text-right">
                                <div class="text-sm font-bold text-apple-red">
                                    -Rp {{ number_format($expense->amount) }}
                                </div>
                                <div class="flex gap-1 mt-2">
                                    <button onclick="editGeneralExpense({{ $expense->id }})"
                                            class="text-xs px-2 py-1 rounded hover:bg-white/10 text-dark-text-secondary">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button onclick="deleteGeneralExpense({{ $expense->id }})"
                                            class="text-xs px-2 py-1 rounded hover:bg-red-500/20 text-apple-red/80">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    @else
        <div class="card-elevated rounded-apple-lg p-8 text-center">
            <div class="flex flex-col items-center justify-center">
                <i class="fas fa-briefcase text-4xl mb-3 text-dark-text-tertiary/25"></i>
                <p class="text-sm font-medium mb-1 text-dark-text-tertiary/80">
                    Belum ada transaksi keuangan umum
                </p>
                <p class="text-xs text-dark-text-tertiary/60">
                    Klik tombol di atas untuk menambahkan pemasukan atau pengeluaran umum
                </p>
            </div>
        </div>
    @endif
</div>

{{-- JavaScript Functions --}}
<script>
let editingIncomeId = null, editingExpenseId = null;

function openGeneralIncomeModalAlpine() {
    window.__generalIncomeOpen = true;
    document.getElementById('generalIncomeAlpineRoot').__x.$data.open = true;
}
function closeGeneralIncomeModalAlpine() {
    document.getElementById('generalIncomeAlpineRoot').__x.$data.open = false;
}
function openGeneralExpenseModalAlpine() {
    document.getElementById('generalExpenseAlpineRoot').__x.$data.open = true;
}
function closeGeneralExpenseModalAlpine() {
    document.getElementById('generalExpenseAlpineRoot').__x.$data.open = false;
}

function openGeneralIncomeModal() {
    editingIncomeId = null;
    document.getElementById('generalIncomeForm').reset();
    document.getElementById('incomeModalTitle').textContent = 'Tambah Pemasukan Umum';
    document.getElementById('incomeSubmitBtn').textContent = 'Simpan Pemasukan';
    document.getElementById('generalIncomeAlpineRoot').__x.$data.open = true;
}

function openGeneralExpenseModal() {
    editingExpenseId = null;
    document.getElementById('generalExpenseForm').reset();
    document.getElementById('expenseModalTitle').textContent = 'Tambah Pengeluaran Umum';
    document.getElementById('expenseSubmitBtn').textContent = 'Simpan Pengeluaran';
    document.getElementById('generalExpenseAlpineRoot').__x.$data.open = true;
}

function editGeneralIncome(id) {
    editingIncomeId = id;
    document.getElementById('incomeModalTitle').textContent = 'Edit Pemasukan Umum';
    document.getElementById('incomeSubmitBtn').textContent = 'Update Pemasukan';
    
    // Fetch income data
    fetch(`/general-transactions/income/${id}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const income = data.data;
                document.getElementById('income_payment_date').value = income.payment_date;
                document.getElementById('income_amount').value = income.amount;
                document.getElementById('income_payment_method').value = income.payment_method;
                document.getElementById('income_bank_account_id').value = income.bank_account_id;
                document.getElementById('income_description').value = income.description || '';
                document.getElementById('income_reference_number').value = income.reference_number || '';
            } else {
                Swal.fire('Error', 'Gagal memuat data pemasukan', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire('Error', 'Terjadi kesalahan saat memuat data', 'error');
        });
    document.getElementById('generalIncomeAlpineRoot').__x.$data.open = true;
}

function deleteGeneralIncome(id) {
    Swal.fire({
        title: 'Hapus Pemasukan Umum?',
        text: 'Data yang dihapus tidak dapat dikembalikan',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`/general-transactions/income/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire('Berhasil!', data.message, 'success').then(() => location.reload());
                } else {
                    Swal.fire('Error', data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire('Error', 'Terjadi kesalahan saat menghapus data', 'error');
            });
        }
    });
}

function editGeneralExpense(id) {
    editingExpenseId = id;
    document.getElementById('expenseModalTitle').textContent = 'Edit Pengeluaran Umum';
    document.getElementById('expenseSubmitBtn').textContent = 'Update Pengeluaran';
    
    // Fetch expense data
    fetch(`/general-transactions/expense/${id}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const expense = data.data;
                document.getElementById('expense_expense_date').value = expense.expense_date;
                document.getElementById('expense_amount').value = expense.amount;
                document.getElementById('expense_category').value = expense.category;
                document.getElementById('expense_payment_method').value = expense.payment_method;
                document.getElementById('expense_bank_account_id').value = expense.bank_account_id;
                document.getElementById('expense_vendor_name').value = expense.vendor_name || '';
                document.getElementById('expense_description').value = expense.description || '';
            } else {
                Swal.fire('Error', 'Gagal memuat data pengeluaran', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire('Error', 'Terjadi kesalahan saat memuat data', 'error');
        });
    document.getElementById('generalExpenseAlpineRoot').__x.$data.open = true;
}

function deleteGeneralExpense(id) {
    Swal.fire({
        title: 'Hapus Pengeluaran Umum?',
        text: 'Data yang dihapus tidak dapat dikembalikan',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`/general-transactions/expense/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire('Berhasil!', data.message, 'success').then(() => location.reload());
                } else {
                    Swal.fire('Error', data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire('Error', 'Terjadi kesalahan saat menghapus data', 'error');
            });
        }
    });
}

function submitGeneralIncome() {
    const form = document.getElementById('generalIncomeForm');
    const formData = new FormData(form);
    const url = editingIncomeId ? `/general-transactions/income/${editingIncomeId}` : '/general-transactions/income';
    const method = editingIncomeId ? 'PUT' : 'POST';
    const data = {};
    formData.forEach((value, key) => data[key] = value);
    fetch(url, {
        method: method,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Content-Type': 'application/json',
            'Accept': 'application/json',
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.getElementById('generalIncomeAlpineRoot').__x.$data.open = false;
            Swal.fire('Berhasil!', data.message, 'success').then(() => location.reload());
        } else {
            Swal.fire('Error', data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire('Error', 'Terjadi kesalahan saat menyimpan data', 'error');
    });
}

function submitGeneralExpense() {
    const form = document.getElementById('generalExpenseForm');
    const formData = new FormData(form);
    const url = editingExpenseId ? `/general-transactions/expense/${editingExpenseId}` : '/general-transactions/expense';
    const method = editingExpenseId ? 'PUT' : 'POST';
    const data = {};
    formData.forEach((value, key) => data[key] = value);
    fetch(url, {
        method: method,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Content-Type': 'application/json',
            'Accept': 'application/json',
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.getElementById('generalExpenseAlpineRoot').__x.$data.open = false;
            Swal.fire('Berhasil!', data.message, 'success').then(() => location.reload());
        } else {
            Swal.fire('Error', data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire('Error', 'Terjadi kesalahan saat menyimpan data', 'error');
    });
}
</script>

{{-- General Income Modal (Alpine.js) --}}
<div id="generalIncomeAlpineRoot" x-data="{ open: false }">
    <div x-show="open" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4"
         @keydown.escape.window="open = false">
        <div class="absolute inset-0 bg-black/60" @click="open = false"></div>
        <div class="relative rounded-apple-lg shadow-2xl w-full max-w-md bg-[#1C1C1E] border border-white/10">
            <div class="flex items-center justify-between px-5 py-4 border-b border-white/[0.08]">
                <h5 id="incomeModalTitle" class="text-white font-semibold">Tambah Pemasukan Umum</h5>
                <button @click="open = false" class="text-dark-text-tertiary/80 hover:text-white transition">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="p-5">
                <form id="generalIncomeForm" onsubmit="event.preventDefault(); submitGeneralIncome();" class="space-y-3">
                    @php $inputCls = 'w-full px-3 py-2 text-sm rounded-lg focus:outline-none bg-dark-bg-tertiary border border-white/10 text-white'; @endphp
                    <div>
                        <label class="block text-xs mb-1 text-dark-text-primary/70">Tanggal Pemasukan</label>
                        <input type="date" class="{{ $inputCls }}" id="income_payment_date" name="payment_date" required>
                    </div>
                    <div>
                        <label class="block text-xs mb-1 text-dark-text-primary/70">Jumlah (Rp)</label>
                        <input type="text" class="{{ $inputCls }}" id="income_amount_display" placeholder="0.00" inputmode="decimal">
                        <input type="hidden" name="amount" id="income_amount">
                    </div>
                    <div>
                        <label class="block text-xs mb-1 text-dark-text-primary/70">Metode Pembayaran</label>
                        <select class="{{ $inputCls }}" id="income_payment_method" name="payment_method" required>
                            <option value="">Pilih Metode</option>
                            <option value="transfer">Transfer Bank</option>
                            <option value="cash">Tunai</option>
                            <option value="check">Cek</option>
                            <option value="other">Lainnya</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs mb-1 text-dark-text-primary/70">Akun Kas/Bank</label>
                        <select class="{{ $inputCls }}" id="income_bank_account_id" name="bank_account_id" required>
                            <option value="">Pilih Akun</option>
                            @foreach($cashAccountsList as $account)
                            <option value="{{ $account->id }}">{{ $account->account_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs mb-1 text-dark-text-primary/70">Nomor Referensi (Opsional)</label>
                        <input type="text" class="{{ $inputCls }}" id="income_reference_number" name="reference_number" placeholder="No. Bukti/Referensi">
                    </div>
                    <div>
                        <label class="block text-xs mb-1 text-dark-text-primary/70">Keterangan</label>
                        <textarea class="{{ $inputCls }}" id="income_description" name="description" rows="3" placeholder="Deskripsi pemasukan umum..."></textarea>
                    </div>
                </form>
            </div>
            <div class="flex justify-end gap-2 px-5 py-4 border-t border-white/[0.08]">
                <button type="button" @click="open = false"
                        class="px-4 py-2 text-sm rounded-lg transition bg-dark-bg-tertiary text-dark-text-primary/70 border border-white/10">Batal</button>
                <button type="button" id="incomeSubmitBtn" onclick="submitGeneralIncome()"
                        class="btn-apple-primary text-sm">Simpan Pemasukan</button>
            </div>
        </div>
    </div>
</div>

{{-- General Expense Modal (Alpine.js) --}}
<div id="generalExpenseAlpineRoot" x-data="{ open: false }">
    <div x-show="open" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4"
         @keydown.escape.window="open = false">
        <div class="absolute inset-0 bg-black/60" @click="open = false"></div>
        <div class="relative rounded-apple-lg shadow-2xl w-full max-w-md bg-[#1C1C1E] border border-white/10">
            <div class="flex items-center justify-between px-5 py-4 border-b border-white/[0.08]">
                <h5 id="expenseModalTitle" class="text-white font-semibold">Tambah Pengeluaran Umum</h5>
                <button @click="open = false" class="text-dark-text-tertiary/80 hover:text-white transition">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="p-5">
                <form id="generalExpenseForm" onsubmit="event.preventDefault(); submitGeneralExpense();" class="space-y-3">
                    @php $inputCls = 'w-full px-3 py-2 text-sm rounded-lg focus:outline-none bg-dark-bg-tertiary border border-white/10 text-white'; @endphp
                    <div>
                        <label class="block text-xs mb-1 text-dark-text-primary/70">Tanggal Pengeluaran</label>
                        <input type="date" class="{{ $inputCls }}" id="expense_expense_date" name="expense_date" required>
                    </div>
                    <div>
                        <label class="block text-xs mb-1 text-dark-text-primary/70">Jumlah (Rp)</label>
                        <input type="text" class="{{ $inputCls }}" id="expense_amount_display" placeholder="0.00" inputmode="decimal">
                        <input type="hidden" name="amount" id="expense_amount">
                    </div>
                    <div>
                        <label class="block text-xs mb-1 text-dark-text-primary/70">Kategori</label>
                        <select class="{{ $inputCls }}" id="expense_category" name="category" required>
                            <option value="">Pilih Kategori</option>
                            @foreach($expenseCategories as $groupName => $items)
                                <optgroup label="{{ $groupName }}">
                                    @foreach($items as $category)
                                        <option value="{{ $category['value'] }}">{{ $category['icon'] }} {{ $category['label'] }}</option>
                                    @endforeach
                                </optgroup>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs mb-1 text-dark-text-primary/70">Metode Pembayaran</label>
                        <select class="{{ $inputCls }}" id="expense_payment_method" name="payment_method" required>
                            <option value="">Pilih Metode</option>
                            <option value="transfer">Transfer Bank</option>
                            <option value="cash">Tunai</option>
                            <option value="check">Cek</option>
                            <option value="other">Lainnya</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs mb-1 text-dark-text-primary/70">Akun Kas/Bank</label>
                        <select class="{{ $inputCls }}" id="expense_bank_account_id" name="bank_account_id" required>
                            <option value="">Pilih Akun</option>
                            @foreach($cashAccountsList as $account)
                            <option value="{{ $account->id }}">{{ $account->account_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs mb-1 text-dark-text-primary/70">Vendor/Penerima (Opsional)</label>
                        <input type="text" class="{{ $inputCls }}" id="expense_vendor_name" name="vendor_name" placeholder="Nama vendor atau penerima">
                    </div>
                    <div>
                        <label class="block text-xs mb-1 text-dark-text-primary/70">Keterangan</label>
                        <textarea class="{{ $inputCls }}" id="expense_description" name="description" rows="3" placeholder="Deskripsi pengeluaran umum..."></textarea>
                    </div>
                </form>
            </div>
            <div class="flex justify-end gap-2 px-5 py-4 border-t border-white/[0.08]">
                <button type="button" @click="open = false"
                        class="px-4 py-2 text-sm rounded-lg transition bg-dark-bg-tertiary text-dark-text-primary/70 border border-white/10">Batal</button>
                <button type="button" id="expenseSubmitBtn" onclick="submitGeneralExpense()"
                        class="btn-apple-primary text-sm">Simpan Pengeluaran</button>
            </div>
        </div>
    </div>
</div>

<script>
// Setup Currency Inputs for General Transactions
(function() {
    'use strict';
    
    // Wait for DOM and CurrencyHelper
    function initCurrencyInputs() {
        if (typeof CurrencyHelper === 'undefined') {
            console.warn('⏳ CurrencyHelper not yet loaded, retrying...');
            setTimeout(initCurrencyInputs, 100);
            return;
        }
        
        try {
            // Setup Income Amount Input
            CurrencyHelper.setupInput('income_amount_display', 'income_amount', {
                decimals: 2,
                maxValue: 9999999999.99,
                allowNegative: false
            });
            
            // Setup Expense Amount Input
            CurrencyHelper.setupInput('expense_amount_display', 'expense_amount', {
                decimals: 2,
                maxValue: 9999999999.99,
                allowNegative: false
            });
            
            console.log('✅ Currency inputs initialized for General Transactions');
        } catch (error) {
            console.error('❌ Error initializing currency inputs:', error);
        }
    }
    
    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initCurrencyInputs);
    } else {
        initCurrencyInputs();
    }
})();

// Override existing submit functions to handle currency parsing
(function() {
    // Store original functions if they exist
    const originalSubmitIncome = window.submitGeneralIncome;
    const originalSubmitExpense = window.submitGeneralExpense;
    
    // Enhanced Income Submit
    window.submitGeneralIncome = function() {
        const displayInput = document.getElementById('income_amount_display');
        const hiddenInput = document.getElementById('income_amount');
        
        if (displayInput && hiddenInput && typeof CurrencyHelper !== 'undefined') {
            const parsedValue = CurrencyHelper.parse(displayInput.value);
            hiddenInput.value = parsedValue;
            
            console.log('💰 Income Submit:', {
                display: displayInput.value,
                parsed: parsedValue,
                hidden: hiddenInput.value
            });
            
            // Validate
            if (!parsedValue || parsedValue <= 0) {
                alert('❌ Jumlah pemasukan harus diisi dengan benar!');
                displayInput.focus();
                return false;
            }
        }
        
        // Call original function if exists
        if (typeof originalSubmitIncome === 'function') {
            return originalSubmitIncome.apply(this, arguments);
        }
    };
    
    // Enhanced Expense Submit
    window.submitGeneralExpense = function() {
        const displayInput = document.getElementById('expense_amount_display');
        const hiddenInput = document.getElementById('expense_amount');
        
        if (displayInput && hiddenInput && typeof CurrencyHelper !== 'undefined') {
            const parsedValue = CurrencyHelper.parse(displayInput.value);
            hiddenInput.value = parsedValue;
            
            console.log('💸 Expense Submit:', {
                display: displayInput.value,
                parsed: parsedValue,
                hidden: hiddenInput.value
            });
            
            // Validate
            if (!parsedValue || parsedValue <= 0) {
                alert('❌ Jumlah pengeluaran harus diisi dengan benar!');
                displayInput.focus();
                return false;
            }
        }
        
        // Call original function if exists
        if (typeof originalSubmitExpense === 'function') {
            return originalSubmitExpense.apply(this, arguments);
        }
    };
})();
</script>
