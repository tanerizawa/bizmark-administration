{{-- Invoice Creation Modal --}}
<div id="invoiceModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50" style="display: none;">
    <div class="rounded-apple-xl p-6 max-w-4xl w-full mx-4 max-h-[90vh] overflow-y-auto" style="background: rgba(28, 28, 30, 0.98);">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold" style="color: #FFFFFF;">
                <i class="fas fa-file-invoice mr-2 text-apple-blue-dark"></i>Create Invoice
            </h2>
            <button onclick="closeInvoiceModal()" class="text-2xl" style="color: rgba(235, 235, 245, 0.6);">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <form id="invoiceForm" onsubmit="submitInvoice(event)">
            <!-- Invoice Header -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <div>
                    <label class="block text-sm font-medium mb-2" style="color: rgba(235, 235, 245, 0.8);">Invoice Date<span class="text-red-500">*</span></label>
                    <input type="date" name="invoice_date" required
                           class="input-dark w-full px-4 py-2.5 rounded-lg"
                           value="{{ date('Y-m-d') }}">
                </div>

                <div>
                    <label class="block text-sm font-medium mb-2" style="color: rgba(235, 235, 245, 0.8);">Due Date<span class="text-red-500">*</span></label>
                    <input type="date" name="due_date" required
                           class="input-dark w-full px-4 py-2.5 rounded-lg"
                           value="{{ date('Y-m-d', strtotime('+30 days')) }}">
                </div>

                <div>
                    <label class="block text-sm font-medium mb-2" style="color: rgba(235, 235, 245, 0.8);">Client Name</label>
                    <input type="text" name="client_name"
                           class="input-dark w-full px-4 py-2.5 rounded-lg"
                           value="{{ $project->client_name }}"
                           placeholder="Client name">
                </div>

                <div>
                    <label class="block text-sm font-medium mb-2" style="color: rgba(235, 235, 245, 0.8);">Tax Rate (%)<span class="text-red-500">*</span></label>
                    <input type="number" name="tax_rate" step="0.01" min="0" max="100" required
                           class="input-dark w-full px-4 py-2.5 rounded-lg"
                           value="11" placeholder="11.00">
                </div>
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium mb-2" style="color: rgba(235, 235, 245, 0.8);">Client Address</label>
                <textarea name="client_address" rows="2"
                          class="input-dark w-full px-4 py-2.5 rounded-lg"
                          placeholder="Client address">{{ $project->client_address }}</textarea>
            </div>

            <!-- Invoice Items -->
            <div class="mb-6">
                <div class="flex justify-between items-center mb-3">
                    <label class="block text-sm font-medium" style="color: rgba(235, 235, 245, 0.8);">Invoice Items<span class="text-red-500">*</span></label>
                    <button type="button" onclick="addInvoiceItem()"
                            class="px-3 py-1 rounded-lg text-sm font-medium transition-colors"
                            style="background: rgba(0, 122, 255, 0.2); color: rgba(0, 122, 255, 1);">
                        <i class="fas fa-plus mr-1"></i>Add Item
                    </button>
                </div>

                <div id="invoiceItems" class="space-y-3">
                    <!-- Items will be added here by JavaScript -->
                </div>
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium mb-2" style="color: rgba(235, 235, 245, 0.8);">Notes</label>
                <textarea name="notes" rows="3"
                          class="input-dark w-full px-4 py-2.5 rounded-lg"
                          placeholder="Additional notes for this invoice"></textarea>
            </div>

            <!-- Invoice Summary -->
            <div class="rounded-lg p-4 mb-6" style="background: rgba(58, 58, 60, 0.5);">
                <div class="space-y-2">
                    <div class="flex justify-between">
                        <span style="color: rgba(235, 235, 245, 0.6);">Subtotal:</span>
                        <span id="invoiceSubtotal" class="font-semibold" style="color: #FFFFFF;">Rp 0</span>
                    </div>
                    <div class="flex justify-between">
                        <span style="color: rgba(235, 235, 245, 0.6);">Tax (<span id="taxRateDisplay">11</span>%):</span>
                        <span id="invoiceTax" class="font-semibold" style="color: #FFFFFF;">Rp 0</span>
                    </div>
                    <div class="flex justify-between text-lg pt-2" style="border-top: 1px solid rgba(58, 58, 60, 0.8);">
                        <span class="font-bold" style="color: #FFFFFF;">Total:</span>
                        <span id="invoiceTotal" class="font-bold" style="color: rgba(0, 122, 255, 1);">Rp 0</span>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex justify-end space-x-3">
                <button type="button" onclick="closeInvoiceModal()"
                        class="px-6 py-2.5 rounded-lg font-medium transition-colors"
                        style="background: rgba(58, 58, 60, 0.8); color: rgba(235, 235, 245, 0.8);">
                    Cancel
                </button>
                <button type="submit"
                        class="px-6 py-2.5 rounded-lg font-medium transition-colors"
                        style="background: rgba(0, 122, 255, 0.9); color: #FFFFFF;">
                    <i class="fas fa-save mr-2"></i>Create Invoice
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Payment Recording Modal --}}
<div id="invoicePaymentModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50" style="display: none;">
    <div class="rounded-apple-xl p-6 max-w-md w-full mx-4" style="background: rgba(28, 28, 30, 0.98);">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl font-bold" style="color: #FFFFFF;">
                <i class="fas fa-dollar-sign mr-2 text-green-500"></i>Record Payment
            </h2>
            <button onclick="closeInvoicePaymentModal()" class="text-2xl" style="color: rgba(235, 235, 245, 0.6);">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <form id="invoicePaymentForm" onsubmit="submitInvoicePayment(event)">
            <input type="hidden" id="payment_invoice_id" name="invoice_id">

            <div class="mb-4 p-3 rounded-lg" style="background: rgba(58, 58, 60, 0.5);">
                <div class="flex justify-between mb-1">
                    <span class="text-sm" style="color: rgba(235, 235, 245, 0.6);">Invoice:</span>
                    <span id="payment_invoice_number" class="text-sm font-mono" style="color: rgba(0, 122, 255, 1);"></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm" style="color: rgba(235, 235, 245, 0.6);">Remaining:</span>
                    <span id="payment_remaining" class="text-sm font-bold" style="color: rgba(255, 149, 0, 1);"></span>
                </div>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium mb-2" style="color: rgba(235, 235, 245, 0.8);">Payment Amount<span class="text-red-500">*</span></label>
                <input type="number" name="amount" step="0.01" min="0.01" required
                       class="input-dark w-full px-4 py-2.5 rounded-lg"
                       placeholder="0.00">
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium mb-2" style="color: rgba(235, 235, 245, 0.8);">Payment Date<span class="text-red-500">*</span></label>
                <input type="date" name="payment_date" required
                       class="input-dark w-full px-4 py-2.5 rounded-lg"
                       value="{{ date('Y-m-d') }}">
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium mb-2" style="color: rgba(235, 235, 245, 0.8);">Payment Method<span class="text-red-500">*</span></label>
                <select name="payment_method" id="payment_method" required onchange="updateCashAccountInfo()"
                        class="input-dark w-full px-4 py-2.5 rounded-lg">
                    <option value="">Pilih metode pembayaran...</option>
                    <option value="bank_transfer">Transfer Bank</option>
                    <option value="cash">Tunai</option>
                </select>
            </div>

            <div id="cash_account_info" class="mb-4 p-3 rounded-lg hidden" style="background: rgba(52, 199, 89, 0.1); border: 1px solid rgba(52, 199, 89, 0.3);">
                <div class="flex items-start">
                    <i class="fas fa-info-circle mt-1 mr-2" style="color: rgba(52, 199, 89, 1);"></i>
                    <div class="text-sm" style="color: rgba(235, 235, 245, 0.8);">
                        <div class="font-medium mb-1" style="color: rgba(52, 199, 89, 1);">Akun Kas Terpilih:</div>
                        <div id="selected_account_name" class="font-mono"></div>
                        <div id="selected_account_balance" class="text-xs mt-1" style="color: rgba(235, 235, 245, 0.6);"></div>
                    </div>
                </div>
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium mb-2" style="color: rgba(235, 235, 245, 0.8);">Reference Number</label>
                <input type="text" name="reference_number"
                       class="input-dark w-full px-4 py-2.5 rounded-lg"
                       placeholder="Transaction reference">
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium mb-2" style="color: rgba(235, 235, 245, 0.8);">Notes</label>
                <textarea name="notes" rows="2"
                          class="input-dark w-full px-4 py-2.5 rounded-lg"
                          placeholder="Payment notes"></textarea>
            </div>

            <div class="flex justify-end space-x-3">
                <button type="button" onclick="closeInvoicePaymentModal()"
                        class="px-6 py-2.5 rounded-lg font-medium transition-colors"
                        style="background: rgba(58, 58, 60, 0.8); color: rgba(235, 235, 245, 0.8);">
                    Cancel
                </button>
                <button type="submit"
                        class="px-6 py-2.5 rounded-lg font-medium transition-colors"
                        style="background: rgba(52, 199, 89, 0.9); color: #FFFFFF;">
                    <i class="fas fa-check mr-2"></i>Record Payment
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Payment Schedule Modal --}}
<div id="scheduleModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50" style="display: none;">
    <div class="rounded-apple-xl p-6 max-w-md w-full mx-4" style="background: rgba(28, 28, 30, 0.98);">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl font-bold" style="color: #FFFFFF;">
                <i class="fas fa-calendar-check mr-2 text-yellow-500"></i>Add Payment Schedule
            </h2>
            <button onclick="closeScheduleModal()" class="text-2xl" style="color: rgba(235, 235, 245, 0.6);">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <form id="scheduleForm" onsubmit="submitSchedule(event)">
            <div class="mb-4">
                <label class="block text-sm font-medium mb-2" style="color: rgba(235, 235, 245, 0.8);">Description<span class="text-red-500">*</span></label>
                <input type="text" name="description" required
                       class="input-dark w-full px-4 py-2.5 rounded-lg"
                       placeholder="e.g., First Payment, Second Installment">
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium mb-2" style="color: rgba(235, 235, 245, 0.8);">Amount<span class="text-red-500">*</span></label>
                <input type="number" name="amount" step="0.01" min="0.01" required
                       class="input-dark w-full px-4 py-2.5 rounded-lg"
                       placeholder="0.00">
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium mb-2" style="color: rgba(235, 235, 245, 0.8);">Due Date<span class="text-red-500">*</span></label>
                <input type="date" name="due_date" required
                       class="input-dark w-full px-4 py-2.5 rounded-lg">
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium mb-2" style="color: rgba(235, 235, 245, 0.8);">Notes</label>
                <textarea name="notes" rows="2"
                          class="input-dark w-full px-4 py-2.5 rounded-lg"
                          placeholder="Additional notes"></textarea>
            </div>

            <div class="flex justify-end space-x-3">
                <button type="button" onclick="closeScheduleModal()"
                        class="px-6 py-2.5 rounded-lg font-medium transition-colors"
                        style="background: rgba(58, 58, 60, 0.8); color: rgba(235, 235, 245, 0.8);">
                    Cancel
                </button>
                <button type="submit"
                        class="px-6 py-2.5 rounded-lg font-medium transition-colors"
                        style="background: rgba(255, 204, 0, 0.9); color: #1C1C1E;">
                    <i class="fas fa-plus mr-2"></i>Add Schedule
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Expense Modal --}}
<div id="financialExpenseModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 overflow-y-auto" style="display: none;">
    <div class="rounded-apple-xl p-6 max-w-md w-full mx-4 my-8" style="background: rgba(28, 28, 30, 0.98);">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl font-bold" style="color: #FFFFFF;">
                <i class="fas fa-receipt mr-2" style="color: rgba(255, 59, 48, 1);"></i>Add Expense
            </h2>
            <button onclick="closeExpenseModal()" class="text-2xl" style="color: rgba(235, 235, 245, 0.6);">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <form id="financialExpenseForm" 
              action="{{ route('projects.financial-expenses.store', $project) }}"
              method="POST"
              onsubmit="handleExpenseSubmit(event)" 
              enctype="multipart/form-data">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium mb-2" style="color: rgba(235, 235, 245, 0.8);">Description<span class="text-red-500">*</span></label>
                <input type="text" name="description" required
                       class="input-dark w-full px-4 py-2.5 rounded-lg"
                       placeholder="Expense description">
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium mb-2" style="color: rgba(235, 235, 245, 0.8);">Amount<span class="text-red-500">*</span></label>
                <input type="number" name="amount" step="0.01" min="0.01" required
                       class="input-dark w-full px-4 py-2.5 rounded-lg"
                       placeholder="0.00">
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium mb-2" style="color: rgba(235, 235, 245, 0.8);">Expense Date<span class="text-red-500">*</span></label>
                <input type="date" name="expense_date" required
                       class="input-dark w-full px-4 py-2.5 rounded-lg"
                       value="{{ date('Y-m-d') }}">
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium mb-2" style="color: rgba(235, 235, 245, 0.8);">Category<span class="text-red-500">*</span></label>
                <select name="category" required
                        class="input-dark w-full px-4 py-2.5 rounded-lg">
                    <option value="">Select category...</option>
                    <option value="vendor">Vendor/Subkon</option>
                    <option value="laboratory">Laboratory</option>
                    <option value="survey">Survey</option>
                    <option value="travel">Travel</option>
                    <option value="operational">Operational</option>
                    <option value="tax">Tax</option>
                    <option value="other">Other</option>
                </select>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium mb-2" style="color: rgba(235, 235, 245, 0.8);">Vendor Name</label>
                <input type="text" name="vendor_name" maxlength="255"
                       class="input-dark w-full px-4 py-2.5 rounded-lg"
                       placeholder="Vendor/supplier name">
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium mb-2" style="color: rgba(235, 235, 245, 0.8);">Payment Method<span class="text-red-500">*</span></label>
                <select name="payment_method" required
                        class="input-dark w-full px-4 py-2.5 rounded-lg">
                    <option value="">Select method...</option>
                    <option value="transfer">Bank Transfer</option>
                    <option value="cash">Cash</option>
                    <option value="check">Check</option>
                    <option value="other">Other</option>
                </select>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium mb-2" style="color: rgba(235, 235, 245, 0.8);">Bank/Cash Account</label>
                <select name="bank_account_id"
                        class="input-dark w-full px-4 py-2.5 rounded-lg">
                    <option value="">Select account...</option>
                    @foreach(\App\Models\CashAccount::active()->get() as $account)
                    <option value="{{ $account->id }}">{{ $account->account_name }} - {{ $account->formatted_balance }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium mb-2" style="color: rgba(235, 235, 245, 0.8);">Receipt File</label>
                
                <!-- Current file display -->
                <div id="currentReceiptFile" class="hidden mb-3 p-3 rounded-lg" style="background: rgba(58, 58, 60, 0.5);">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-2">
                            <i class="fas fa-file-alt" style="color: rgba(255, 204, 0, 0.9);"></i>
                            <span class="text-sm" style="color: rgba(235, 235, 245, 0.8);" id="currentFileName">file.pdf</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <a href="#" id="viewReceiptBtn" target="_blank"
                               class="text-sm px-3 py-1 rounded transition-colors"
                               style="background: rgba(0, 122, 255, 0.15); color: rgba(0, 122, 255, 1);">
                                <i class="fas fa-eye mr-1"></i>Lihat
                            </a>
                            <button type="button" onclick="deleteReceiptFile()"
                                    class="text-sm px-3 py-1 rounded transition-colors"
                                    style="background: rgba(255, 59, 48, 0.15); color: rgba(255, 59, 48, 1);">
                                <i class="fas fa-trash mr-1"></i>Hapus
                            </button>
                        </div>
                    </div>
                </div>
                
                <input type="file" name="receipt_file" accept=".pdf,.jpg,.jpeg,.png"
                       class="input-dark w-full px-4 py-2.5 rounded-lg" id="receiptFileInput">
                <small style="color: rgba(235, 235, 245, 0.6);">PDF, JPG, PNG (max 5MB)</small>
            </div>

            <div class="flex justify-end space-x-3">
                <button type="button" onclick="closeExpenseModal()"
                        class="px-6 py-2.5 rounded-lg font-medium transition-colors"
                        style="background: rgba(58, 58, 60, 0.8); color: rgba(235, 235, 245, 0.8);">
                    Cancel
                </button>
                <button type="submit"
                        class="px-6 py-2.5 rounded-lg font-medium transition-colors"
                        style="background: rgba(255, 59, 48, 0.9); color: #FFFFFF;">
                    <i class="fas fa-plus mr-2"></i>Add Expense
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// Cash Accounts Data (fetched from backend)
let cashAccounts = [];

// Fetch cash accounts on page load
document.addEventListener('DOMContentLoaded', function() {
    fetchCashAccounts();
});

function fetchCashAccounts() {
    fetch('/api/cash-accounts/active')
        .then(response => response.json())
        .then(data => {
            cashAccounts = data;
            console.log('Cash accounts loaded:', cashAccounts);
        })
        .catch(error => {
            console.error('Error fetching cash accounts:', error);
        });
}

function updateCashAccountInfo() {
    const paymentMethod = document.getElementById('payment_method').value;
    const infoDiv = document.getElementById('cash_account_info');
    const accountNameDiv = document.getElementById('selected_account_name');
    const accountBalanceDiv = document.getElementById('selected_account_balance');
    
    if (!paymentMethod) {
        infoDiv.classList.add('hidden');
        return;
    }
    
    // Find appropriate cash account
    let accountType = paymentMethod === 'bank_transfer' ? 'bank' : 'cash';
    let account = cashAccounts.find(acc => acc.account_type === accountType && acc.is_active);
    
    if (account) {
        accountNameDiv.textContent = account.account_name + (account.bank_name ? ' - ' + account.bank_name : '');
        accountBalanceDiv.textContent = 'Saldo saat ini: Rp ' + Number(account.current_balance).toLocaleString('id-ID');
        infoDiv.classList.remove('hidden');
    } else {
        accountNameDiv.textContent = 'Tidak ada akun ' + (accountType === 'bank' ? 'bank' : 'kas tunai') + ' aktif';
        accountBalanceDiv.textContent = '';
        infoDiv.classList.remove('hidden');
    }
}

// Invoice Modal Functions
let invoiceItemCount = 0;

function openInvoiceModal() {
    document.getElementById('invoiceModal').style.display = 'flex';
    document.getElementById('invoiceItems').innerHTML = '';
    invoiceItemCount = 0;
    addInvoiceItem(); // Add first item by default
}

function closeInvoiceModal() {
    document.getElementById('invoiceModal').style.display = 'none';
    document.getElementById('invoiceForm').reset();
}

function addInvoiceItem() {
    invoiceItemCount++;
    const itemsContainer = document.getElementById('invoiceItems');
    const itemHtml = `
        <div class="invoice-item rounded-lg p-3" style="background: rgba(58, 58, 60, 0.5);" data-item-id="${invoiceItemCount}">
            <div class="grid grid-cols-12 gap-2">
                <div class="col-span-5">
                    <input type="text" name="items[${invoiceItemCount}][description]" required
                           class="input-dark w-full px-3 py-2 rounded-lg text-sm"
                           placeholder="Description">
                </div>
                <div class="col-span-2">
                    <input type="number" name="items[${invoiceItemCount}][quantity]" min="1" value="1" required
                           class="item-quantity input-dark w-full px-3 py-2 rounded-lg text-sm"
                           placeholder="Qty" onchange="calculateInvoiceTotal()">
                </div>
                <div class="col-span-3">
                    <input type="number" name="items[${invoiceItemCount}][unit_price]" step="0.01" min="0" required
                           class="item-price input-dark w-full px-3 py-2 rounded-lg text-sm"
                           placeholder="Unit Price" onchange="calculateInvoiceTotal()">
                </div>
                <div class="col-span-2 flex items-center justify-end">
                    <button type="button" onclick="removeInvoiceItem(${invoiceItemCount})"
                            class="text-sm" style="color: rgba(255, 59, 48, 1);">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        </div>
    `;
    itemsContainer.insertAdjacentHTML('beforeend', itemHtml);
    calculateInvoiceTotal();
}

function removeInvoiceItem(itemId) {
    const item = document.querySelector(`[data-item-id="${itemId}"]`);
    if (item) {
        item.remove();
        calculateInvoiceTotal();
    }
}

function calculateInvoiceTotal() {
    let subtotal = 0;
    document.querySelectorAll('.invoice-item').forEach(item => {
        const qty = parseFloat(item.querySelector('.item-quantity').value) || 0;
        const price = parseFloat(item.querySelector('.item-price').value) || 0;
        subtotal += qty * price;
    });

    const taxRate = parseFloat(document.querySelector('input[name="tax_rate"]').value) || 0;
    const tax = (subtotal * taxRate) / 100;
    const total = subtotal + tax;

    document.getElementById('invoiceSubtotal').textContent = 'Rp ' + formatNumber(subtotal);
    document.getElementById('invoiceTax').textContent = 'Rp ' + formatNumber(tax);
    document.getElementById('invoiceTotal').textContent = 'Rp ' + formatNumber(total);
    document.getElementById('taxRateDisplay').textContent = taxRate.toFixed(2);
}

// Watch tax rate changes
document.addEventListener('DOMContentLoaded', function() {
    const taxInput = document.querySelector('input[name="tax_rate"]');
    if (taxInput) {
        taxInput.addEventListener('input', calculateInvoiceTotal);
    }
});

function submitInvoice(event) {
    event.preventDefault();
    const form = event.target;
    const formData = new FormData(form);
    
    // Convert FormData to JSON
    const data = {};
    const items = [];
    
    formData.forEach((value, key) => {
        if (key.startsWith('items[')) {
            const match = key.match(/items\[(\d+)\]\[(\w+)\]/);
            if (match) {
                const index = match[1];
                const field = match[2];
                if (!items[index]) items[index] = {};
                items[index][field] = value;
            }
        } else {
            data[key] = value;
        }
    });
    
    data.items = Object.values(items).filter(item => item.description);
    
    fetch('{{ route("projects.invoices.store", $project) }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            closeInvoiceModal();
            location.reload();
        } else {
            alert(result.message || 'Failed to create invoice');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while creating the invoice');
    });
}

// Payment Modal Functions
function openPaymentModal(invoiceId = null, invoiceNumber = null, remaining = null) {
    console.log('=== OPENING PAYMENT MODAL ===');
    console.log('Invoice ID:', invoiceId);
    console.log('Invoice Number:', invoiceNumber);
    console.log('Remaining:', remaining);
    
    const modal = document.getElementById('invoicePaymentModal');
    console.log('Modal found:', modal ? 'YES' : 'NO');
    
    const invoiceIdInput = document.getElementById('payment_invoice_id');
    console.log('Input found:', invoiceIdInput ? 'YES' : 'NO');
    console.log('Input current value:', invoiceIdInput ? invoiceIdInput.value : 'N/A');
    
    const invoiceNumberDisplay = document.getElementById('payment_invoice_number');
    const remainingDisplay = document.getElementById('payment_remaining');
    const amountInput = document.querySelector('#invoicePaymentForm input[name="amount"]');
    
    if (invoiceId && invoiceIdInput) {
        // Set invoice ID (hidden input)
        invoiceIdInput.value = invoiceId;
        console.log('Set invoice_id input to:', invoiceId);
        console.log('Input value after set:', invoiceIdInput.value);
        
        // Set invoice number display
        if (invoiceNumberDisplay && invoiceNumber) {
            invoiceNumberDisplay.textContent = invoiceNumber;
        }
        
        // Set remaining amount display and input
        if (remaining) {
            if (remainingDisplay) {
                remainingDisplay.textContent = 'Rp ' + remaining.toLocaleString('id-ID');
            }
            if (amountInput) {
                amountInput.value = remaining;
                amountInput.max = remaining;
            }
        }
    } else {
        console.warn('Missing invoiceId or invoiceIdInput!');
        // Reset form
        if (invoiceIdInput) invoiceIdInput.value = '';
        if (invoiceNumberDisplay) invoiceNumberDisplay.textContent = '';
        if (remainingDisplay) remainingDisplay.textContent = '';
        if (amountInput) amountInput.value = '';
    }
    
    modal.classList.remove('hidden');
    modal.style.display = 'flex';
    console.log('Modal opened');
}

function closeInvoicePaymentModal() {
    const modal = document.getElementById('invoicePaymentModal');
    modal.classList.add('hidden');
    modal.style.display = 'none';
    const form = modal.querySelector('form');
    if (form) {
        form.reset();
    }
}

function submitInvoicePayment(event) {
    event.preventDefault();
    console.log('=== SUBMIT INVOICE PAYMENT ===');
    
    const form = event.target;
    console.log('Form found:', form ? 'YES' : 'NO');
    
    const invoiceIdInput = document.getElementById('payment_invoice_id');
    console.log('Invoice ID Input found:', invoiceIdInput ? 'YES' : 'NO');
    console.log('Invoice ID Input value:', invoiceIdInput ? invoiceIdInput.value : 'N/A');
    
    // Get invoice_id directly from input element (more reliable than FormData for hidden inputs)
    const invoiceId = invoiceIdInput ? invoiceIdInput.value : null;
    
    console.log('Invoice ID (direct):', invoiceId);
    
    if (!invoiceId || invoiceId === '') {
        alert('Invoice ID tidak ditemukan! Silakan tutup modal dan coba lagi.');
        console.error('Missing invoice_id');
        return;
    }
    
    // Build FormData
    const formData = new FormData(form);
    
    // Force set invoice_id in FormData (ensure it's included)
    formData.set('invoice_id', invoiceId);
    
    console.log('FormData invoice_id:', formData.get('invoice_id'));
    console.log('Amount:', formData.get('amount'));
    console.log('Payment Date:', formData.get('payment_date'));
    console.log('Payment Method:', formData.get('payment_method'));
    
    // Show loading
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Memproses...';
    
    const url = `/invoices/${invoiceId}/payment`;
    console.log('POST URL:', url);
    
    fetch(url, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: formData
    })
    .then(response => {
        console.log('Response status:', response.status);
        return response.json().then(data => ({
            status: response.status,
            ok: response.ok,
            data: data
        }));
    })
    .then(result => {
        console.log('Response data:', result.data);
        if (result.ok && result.data.success) {
            let message = result.data.message || 'Pembayaran berhasil dicatat!';
            
            // Add cash account info to success message
            if (result.data.cash_account && result.data.new_balance) {
                message += '\n\nAkun: ' + result.data.cash_account;
                message += '\nSaldo baru: Rp ' + Number(result.data.new_balance).toLocaleString('id-ID');
            }
            
            alert(message);
            closeInvoicePaymentModal();
            window.location.hash = 'financial';
            location.reload();
        } else {
            let errorMsg = result.data.message || 'Failed to record payment';
            if (result.data.errors) {
                errorMsg += '\n\nValidation Errors:\n';
                Object.keys(result.data.errors).forEach(key => {
                    errorMsg += `- ${key}: ${result.data.errors[key].join(', ')}\n`;
                });
            }
            alert(errorMsg);
            console.error('Payment error:', result.data);
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        }
    })
    .catch(error => {
        console.error('Fetch error:', error);
        alert('An error occurred while recording the payment: ' + error.message);
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
    });
}

// Schedule Modal Functions
function openScheduleModal() {
    document.getElementById('scheduleModal').style.display = 'flex';
}

function closeScheduleModal() {
    document.getElementById('scheduleModal').style.display = 'none';
    document.getElementById('scheduleForm').reset();
}

function submitSchedule(event) {
    event.preventDefault();
    const form = event.target;
    const formData = new FormData(form);
    
    fetch('{{ route("projects.payment-schedules.store", $project) }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: formData
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            closeScheduleModal();
            location.reload();
        } else {
            alert(result.message || 'Failed to create schedule');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while creating the schedule');
    });
}

function markSchedulePaid(scheduleId) {
    const method = prompt('Enter payment method:');
    if (!method) return;
    
    const reference = prompt('Enter reference number (optional):');
    
    fetch(`/payment-schedules/${scheduleId}/paid`, {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            payment_method: method,
            reference_number: reference
        })
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            location.reload();
        } else {
            alert(result.message || 'Failed to mark as paid');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred');
    });
}

// Expense Modal Functions
function openExpenseModal() {
    const modal = document.getElementById('expenseModal');
    modal.classList.remove('hidden');
    modal.style.display = 'flex';
}

function closeExpenseModal() {
    const modal = document.getElementById('expenseModal');
    modal.classList.add('hidden');
    modal.style.display = 'none';
    
    // Reset form
    const form = document.querySelector('#expenseModal form') || document.getElementById('financialExpenseForm');
    if (form) {
        form.reset();
        
        // Reset form action to create mode
        form.action = '{{ route("projects.financial-expenses.store", $project) }}';
        
        // Remove method spoofing
        const methodInput = form.querySelector('input[name="_method"]');
        if (methodInput) {
            methodInput.remove();
        }
        
        // Reset modal title
        const modalTitle = document.querySelector('#expenseModal h3');
        if (modalTitle) {
            modalTitle.innerHTML = '<i class="fas fa-shopping-cart mr-2 text-apple-blue-dark"></i>Tambah Pengeluaran';
        }
        
        // Reset button text
        const submitBtn = form.querySelector('button[type="submit"]');
        if (submitBtn) {
            submitBtn.innerHTML = '<i class="fas fa-save mr-2"></i>Simpan Pengeluaran';
        }
        
        // Hide receivable fields
        const receivableFields = document.getElementById('receivableFields');
        if (receivableFields) {
            receivableFields.style.display = 'none';
        }
        
        // Hide current receipt file display
        const currentFileDiv = document.getElementById('currentReceiptFile');
        if (currentFileDiv) {
            currentFileDiv.classList.add('hidden');
        }
        
        // Re-enable all fields (in case they were disabled during edit)
        const amountField = form.querySelector('input[name="amount"]');
        const dateField = form.querySelector('input[name="expense_date"]');
        const categoryField = form.querySelector('select[name="category"]');
        
        if (amountField) {
            amountField.readOnly = false;
            amountField.style.opacity = '1';
            amountField.style.cursor = 'text';
            amountField.title = '';
        }
        
        if (dateField) {
            dateField.readOnly = false;
            dateField.style.opacity = '1';
            dateField.style.cursor = 'text';
            dateField.title = '';
        }
        
        if (categoryField) {
            categoryField.disabled = false;
            categoryField.style.opacity = '1';
            categoryField.style.cursor = 'pointer';
            categoryField.title = '';
        }
    }
}

function deleteReceiptFile() {
    if (!confirm('Yakin ingin menghapus file bukti ini?')) {
        return;
    }
    
    const currentFileDiv = document.getElementById('currentReceiptFile');
    const expenseId = currentFileDiv.dataset.expenseId;
    
    if (!expenseId) {
        alert('Error: Expense ID tidak ditemukan');
        return;
    }
    
    fetch(`/financial-expenses/${expenseId}/delete-receipt`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        const contentType = response.headers.get('content-type');
        if (contentType && contentType.includes('application/json')) {
            return response.json();
        } else {
            throw new Error('Response bukan JSON');
        }
    })
    .then(result => {
        if (result.success) {
            // Hide the file display
            currentFileDiv.classList.add('hidden');
            // Clear the file input
            document.getElementById('receiptFileInput').value = '';
            alert('File berhasil dihapus');
        } else {
            alert(result.message || 'Gagal menghapus file');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan saat menghapus file');
    });
}

function handleExpenseSubmit(event) {
    event.preventDefault();
    const form = event.target;
    const formData = new FormData(form);
    
    // Show loading
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalBtnText = submitBtn.innerHTML;
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Menyimpan...';
    
    // Determine if this is create or update
    const method = formData.get('_method') || 'POST';
    const url = form.action;
    
    fetch(url, {
        method: method === 'PATCH' ? 'POST' : 'POST', // Use POST for both, Laravel will handle _method
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: formData
    })
    .then(response => {
        // Parse JSON regardless of status
        return response.json().then(data => ({
            status: response.status,
            ok: response.ok,
            data: data
        }));
    })
    .then(result => {
        if (result.ok && result.data.success) {
            // Set hash to financial tab before reload
            window.location.hash = 'financial';
            location.reload();
        } else {
            // Handle validation errors
            let errorMessage = result.data.message || 'Gagal menyimpan pengeluaran';
            
            if (result.data.errors) {
                // Show validation errors
                errorMessage += '\n\nValidation Errors:\n';
                Object.keys(result.data.errors).forEach(key => {
                    errorMessage += `- ${key}: ${result.data.errors[key].join(', ')}\n`;
                });
            }
            
            alert(errorMessage);
            console.error('Validation errors:', result.data);
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalBtnText;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan saat menyimpan. Silakan coba lagi atau hubungi administrator.\n\nError: ' + error.message);
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalBtnText;
    });
    
    return false;
}

function submitExpense(event) {
    event.preventDefault();
    const form = event.target;
    const formData = new FormData(form);
    
    fetch('{{ route("projects.financial-expenses.store", $project) }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: formData
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            closeExpenseModal();
            location.reload();
        } else {
            alert(result.message || 'Failed to add expense');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while adding the expense');
    });
}

// Delete Functions
function deleteInvoice(invoiceId) {
    if (!confirm('⚠️ HAPUS INVOICE\n\nApakah Anda yakin ingin menghapus invoice ini?\n\n⚠️ Perhatian:\n• Hanya invoice Draft atau Cancelled yang dapat dihapus\n• Invoice yang sudah ada pembayaran tidak dapat dihapus\n• Data invoice item dan jadwal pembayaran akan ikut terhapus\n\nProses ini TIDAK DAPAT dibatalkan!')) {
        return;
    }
    
    const loadingMsg = document.createElement('div');
    loadingMsg.innerHTML = '<div style="position: fixed; top: 20px; right: 20px; background: rgba(0,122,255,0.9); color: white; padding: 15px 20px; border-radius: 8px; z-index: 9999;">Menghapus invoice...</div>';
    document.body.appendChild(loadingMsg);
    
    fetch(`/invoices/${invoiceId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
        }
    })
    .then(response => {
        if (!response.ok) {
            return response.json().then(err => Promise.reject(err));
        }
        return response.json();
    })
    .then(result => {
        document.body.removeChild(loadingMsg);
        if (result.success) {
            // Show success message
            const successMsg = document.createElement('div');
            successMsg.innerHTML = '<div style="position: fixed; top: 20px; right: 20px; background: rgba(52,199,89,0.9); color: white; padding: 15px 20px; border-radius: 8px; z-index: 9999;">✅ ' + result.message + '</div>';
            document.body.appendChild(successMsg);
            
            // Reload after 1 second
            setTimeout(() => {
                location.reload();
            }, 1000);
        } else {
            alert('❌ Gagal menghapus invoice:\n\n' + (result.message || 'Terjadi kesalahan'));
        }
    })
    .catch(error => {
        document.body.removeChild(loadingMsg);
        console.error('Error:', error);
        alert('❌ GAGAL MENGHAPUS INVOICE\n\n' + (error.message || 'Terjadi kesalahan pada server'));
    });
}

function deleteSchedule(scheduleId) {
    if (!confirm('Are you sure you want to delete this schedule?')) return;
    
    fetch(`/payment-schedules/${scheduleId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            location.reload();
        } else {
            alert(result.message || 'Failed to delete schedule');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred');
    });
}

function deleteExpense(expenseId) {
    if (!confirm('Yakin ingin menghapus pengeluaran ini?')) return;
    
    fetch(`/financial-expenses/${expenseId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            // Reload dengan hash untuk stay di tab financial
            window.location.hash = 'financial';
            location.reload();
        } else {
            alert(result.message || 'Gagal menghapus pengeluaran');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan');
    });
}

// Receivables Functions
function openReceivableModal() {
    // For now, just open expense modal with is_receivable pre-checked
    openExpenseModal();
    setTimeout(() => {
        const receivableCheckbox = document.querySelector('input[name="is_receivable"]');
        if (receivableCheckbox) {
            receivableCheckbox.checked = true;
            // Show receivable fields
            toggleReceivableFields();
        }
    }, 100);
}

function recordReceivablePayment(expenseId, remainingAmount) {
    const paymentAmount = prompt(`Masukkan jumlah yang dibayar (Sisa: Rp ${remainingAmount.toLocaleString('id-ID')}):`, remainingAmount);
    
    if (paymentAmount === null) return; // User cancelled
    
    const amount = parseFloat(paymentAmount);
    if (isNaN(amount) || amount <= 0) {
        alert('Jumlah tidak valid!');
        return;
    }
    
    if (amount > remainingAmount) {
        alert('Jumlah pembayaran melebihi sisa piutang!');
        return;
    }
    
    const notes = prompt('Catatan pembayaran (optional):');
    
    fetch(`/financial-expenses/${expenseId}/record-payment`, {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            payment_amount: amount,
            payment_notes: notes
        })
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            alert(result.message);
            window.location.hash = 'financial';
            location.reload();
        } else {
            alert(result.message || 'Gagal mencatat pembayaran');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan');
    });
}

function markAsInvoiced(expenseId) {
    if (!confirm('Tandai pengeluaran ini sudah ditagihkan ke klien?\n\nCatatan: Item ini akan dipindahkan dari daftar piutang.')) return;
    
    // For now, just add a note. In future, this could create an invoice automatically
    const invoiceNote = prompt('Nomor invoice (optional):');
    
    fetch(`/financial-expenses/${expenseId}/mark-invoiced`, {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            invoice_note: invoiceNote
        })
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            window.location.hash = 'financial';
            location.reload();
        } else {
            alert(result.message || 'Gagal menandai sebagai ditagihkan');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan');
    });
}

function toggleReceivableFields() {
    const isReceivable = document.querySelector('input[name="is_receivable"]')?.checked;
    const receivableFields = document.getElementById('receivableFields');
    if (receivableFields) {
        receivableFields.style.display = isReceivable ? 'block' : 'none';
    }
}

function editExpense(expenseId) {
    // Fetch expense data
    fetch(`/financial-expenses/${expenseId}`)
        .then(response => response.json())
        .then(expense => {
            // ============================================
            // VALIDATION: Check if expense can be edited
            // ============================================
            
            // 1. Block jika sudah ditagihkan ke client
            if (expense.is_billable && expense.invoiced) {
                alert('⚠️ TIDAK DAPAT DIUBAH\n\n' +
                      'Pengeluaran ini sudah ditagihkan ke client dan tidak dapat diubah.\n\n' +
                      'Untuk koreksi:\n' +
                      '• Hubungi admin, atau\n' +
                      '• Buat entry pengeluaran baru');
                return;
            }
            
            // 2. Block jika kasbon sudah ada payment
            if (expense.is_receivable && 
                expense.receivable_status && 
                expense.receivable_status !== 'pending') {
                alert('⚠️ TIDAK DAPAT DIUBAH\n\n' +
                      'Kasbon ini sudah ada pembayaran (status: ' + expense.receivable_status + ') dan tidak dapat diubah.\n\n' +
                      'Untuk koreksi:\n' +
                      '• Selesaikan pembayaran kasbon terlebih dahulu\n' +
                      '• Atau buat entry koreksi baru');
                return;
            }
            
            // 3. Check usia expense (audit window 7 hari)
            const expenseDate = new Date(expense.expense_date);
            const today = new Date();
            const daysDiff = Math.floor((today - expenseDate) / (1000 * 60 * 60 * 24));
            const isOld = daysDiff > 7;
            
            if (isOld) {
                const confirmEdit = confirm('⚠️ PERINGATAN: Edit Data Lama\n\n' +
                      'Pengeluaran ini sudah berusia ' + daysDiff + ' hari (> 7 hari).\n' +
                      'Mengubah data lama dapat mempengaruhi audit trail.\n\n' +
                      'Apakah Anda yakin ingin melanjutkan?');
                if (!confirmEdit) {
                    return;
                }
            }
            
            // ============================================
            // POPULATE FORM
            // ============================================
            
            // Get form element
            const form = document.getElementById('financialExpenseForm');
            
            if (!form) {
                console.error('Form not found!');
                alert('Error: Form tidak ditemukan');
                return;
            }
            
            // Populate form with expense data
            const dateField = form.querySelector('input[name="expense_date"]');
            const amountField = form.querySelector('input[name="amount"]');
            const categoryField = form.querySelector('select[name="category"]');
            
            dateField.value = expense.expense_date;
            amountField.value = expense.amount;
            categoryField.value = expense.category;
            form.querySelector('input[name="vendor_name"]').value = expense.vendor_name || '';
            form.querySelector('select[name="payment_method"]').value = expense.payment_method;
            form.querySelector('select[name="bank_account_id"]').value = expense.bank_account_id || '';
            
            const descField = form.querySelector('textarea[name="description"]') || form.querySelector('input[name="description"]');
            if (descField) descField.value = expense.description || '';
            
            // ============================================
            // RESTRICT CRITICAL FIELDS if expense is old or has receivable implications
            // ============================================
            
            const shouldRestrictCriticalFields = isOld || 
                                                 (expense.is_receivable && expense.receivable_status === 'pending');
            
            if (shouldRestrictCriticalFields) {
                // Disable critical fields
                amountField.readOnly = true;
                amountField.style.opacity = '0.6';
                amountField.style.cursor = 'not-allowed';
                amountField.title = 'Amount tidak dapat diubah untuk pengeluaran yang sudah lama atau sudah terhubung dengan invoice/kasbon';
                
                dateField.readOnly = true;
                dateField.style.opacity = '0.6';
                dateField.style.cursor = 'not-allowed';
                dateField.title = 'Tanggal tidak dapat diubah untuk pengeluaran yang sudah lama atau sudah terhubung dengan kasbon';
                
                categoryField.disabled = true;
                categoryField.style.opacity = '0.6';
                categoryField.style.cursor = 'not-allowed';
                categoryField.title = 'Kategori tidak dapat diubah untuk pengeluaran yang sudah lama atau sudah terhubung dengan kasbon';
                
                // Show info message
                setTimeout(() => {
                    alert('ℹ️ INFORMASI\n\n' +
                          'Field Amount, Tanggal, dan Kategori tidak dapat diubah karena:\n' +
                          (isOld ? '• Pengeluaran sudah berusia > 7 hari\n' : '') +
                          (expense.is_receivable ? '• Pengeluaran ini adalah kasbon/piutang internal\n' : '') +
                          '\nYang dapat diubah:\n' +
                          '✓ Deskripsi/Catatan\n' +
                          '✓ Vendor\n' +
                          '✓ Metode Pembayaran\n' +
                          '✓ Akun Bank\n' +
                          '✓ File Bukti');
                }, 300);
            } else {
                // Enable all fields (new expense, can edit freely)
                amountField.readOnly = false;
                amountField.style.opacity = '1';
                amountField.style.cursor = 'text';
                amountField.title = '';
                
                dateField.readOnly = false;
                dateField.style.opacity = '1';
                dateField.style.cursor = 'text';
                dateField.title = '';
                
                categoryField.disabled = false;
                categoryField.style.opacity = '1';
                categoryField.style.cursor = 'pointer';
                categoryField.title = '';
            }
            
            const receivableCheckbox = form.querySelector('input[name="is_receivable"]');
            if (receivableCheckbox) {
                receivableCheckbox.checked = expense.is_receivable;
                toggleReceivableFields();
                
                if (expense.is_receivable) {
                    form.querySelector('input[name="receivable_from"]').value = expense.receivable_from || '';
                    form.querySelector('select[name="receivable_status"]').value = expense.receivable_status || 'pending';
                    form.querySelector('textarea[name="receivable_notes"]').value = expense.receivable_notes || '';
                }
            }
            
            // Show current receipt file if exists
            const currentFileDiv = document.getElementById('currentReceiptFile');
            const currentFileName = document.getElementById('currentFileName');
            const viewReceiptBtn = document.getElementById('viewReceiptBtn');
            
            if (expense.receipt_file) {
                // Store expense ID for delete function
                currentFileDiv.dataset.expenseId = expenseId;
                
                // Extract filename from path
                const fileName = expense.receipt_file.split('/').pop();
                currentFileName.textContent = fileName;
                
                // Set view link
                viewReceiptBtn.href = `/storage/${expense.receipt_file}`;
                
                // Show the file display
                currentFileDiv.classList.remove('hidden');
            } else {
                currentFileDiv.classList.add('hidden');
            }
            
            // Change form action to update
            form.action = `/financial-expenses/${expenseId}`;
            
            // Add method spoofing for PATCH
            let methodInput = form.querySelector('input[name="_method"]');
            if (!methodInput) {
                methodInput = document.createElement('input');
                methodInput.type = 'hidden';
                methodInput.name = '_method';
                form.appendChild(methodInput);
            }
            methodInput.value = 'PATCH';
            
            // Change modal title
            const modalTitle = document.querySelector('#expenseModal h3');
            if (modalTitle) {
                modalTitle.innerHTML = '<i class="fas fa-edit mr-2 text-apple-blue-dark"></i>Edit Pengeluaran';
            }
            
            // Change button text
            const submitBtn = form.querySelector('button[type="submit"]');
            submitBtn.innerHTML = '<i class="fas fa-save mr-2"></i>Update Pengeluaran';
            
            // Open modal
            openExpenseModal();
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Gagal memuat data pengeluaran');
        });
}

function editReceivable(expenseId) {
    editExpense(expenseId);
}

function deleteReceivable(expenseId) {
    if (!confirm('Hapus piutang/kasbon ini?\n\nCatatan: Pengeluaran akan tetap tercatat, hanya status kasbon yang dihapus.')) return;
    
    // Don't delete the expense, just remove receivable flag
    fetch(`/financial-expenses/${expenseId}/remove-receivable`, {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            alert(result.message);
            window.location.hash = 'financial';
            location.reload();
        } else {
            alert(result.message || 'Gagal menghapus piutang');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan');
    });
}

function viewInvoice(invoiceId) {
    window.location.href = '/invoices/' + invoiceId;
}

// Helper function
function formatNumber(num) {
    return new Intl.NumberFormat('id-ID').format(Math.round(num));
}
</script>
