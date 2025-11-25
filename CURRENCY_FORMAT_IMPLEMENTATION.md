# Implementasi Format Keuangan Standar - Step by Step Guide

**Format Target:** `1,234.56` (International - comma ribuan, period desimal)  
**Timeline:** 3 minggu  
**Status:** 📋 Planning Phase

---

## 📦 Setup Awal

### 1. Load Helper Files

#### A. JavaScript Helper
Tambahkan di `resources/views/layouts/app.blade.php` **SEBELUM** closing `</body>`:

```html
<!-- Currency Helper - MUST load before other scripts -->
<script src="{{ asset('js/currency-helper.js') }}"></script>
```

#### B. PHP Helper - Register Service Provider

**File:** `app/Providers/AppServiceProvider.php`

```php
public function boot()
{
    // Register Currency Helper as Global Blade Helper
    Blade::directive('currency', function ($expression) {
        return "<?php echo \App\Helpers\CurrencyHelper::format($expression); ?>";
    });
    
    Blade::directive('currencyRaw', function ($expression) {
        return "<?php echo \App\Helpers\CurrencyHelper::format($expression, 2, false); ?>";
    });
}
```

Setelah itu jalankan:
```bash
php artisan config:clear
php artisan view:clear
```

---

## 🎯 PHASE 1: Critical Input Forms (Week 1)

### Task 1.1: General Transactions - Income Form

**File:** `resources/views/cash-accounts/tabs/general-transactions.blade.php`

**Line 461 - CURRENT:**
```html
<input type="number" class="form-control form-control-apple" 
       id="income_amount" name="amount" step="0.01" min="0.01" required>
```

**REPLACE WITH:**
```html
<input type="text" class="form-control form-control-apple" 
       id="income_amount_display" 
       placeholder="0.00"
       inputmode="decimal">
<input type="hidden" name="amount" id="income_amount">
```

**ADD JavaScript (sebelum closing modal/section):**
```javascript
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Setup currency input for income
    CurrencyHelper.setupInput('income_amount_display', 'income_amount', {
        decimals: 2,
        maxValue: 9999999999.99
    });
});
</script>
```

**ADD Form Validation:**
```javascript
// Find form submit handler
document.getElementById('incomeForm').addEventListener('submit', function(e) {
    const validation = CurrencyHelper.validateForm(this, [
        {
            displayId: 'income_amount_display',
            hiddenId: 'income_amount',
            fieldName: 'Jumlah Pemasukan',
            required: true,
            validate: { min: 0.01, allowZero: false }
        }
    ]);
    
    if (!validation.valid) {
        e.preventDefault();
        alert('Error:\n' + validation.errors.join('\n'));
        return false;
    }
});
```

**Testing:**
- [ ] Input: "1234" → Display: "1,234.00"
- [ ] Input: "1234.5" → Display: "1,234.50"
- [ ] Input: "0" → Validation error
- [ ] Submit form → Hidden input has raw value (1234.50)

---

### Task 1.2: General Transactions - Expense Form

**File:** `resources/views/cash-accounts/tabs/general-transactions.blade.php`

**Line 531 - CURRENT:**
```html
<input type="number" class="form-control form-control-apple" 
       id="expense_amount" name="amount" step="0.01" min="0.01" required>
```

**REPLACE WITH:**
```html
<input type="text" class="form-control form-control-apple" 
       id="expense_amount_display" 
       placeholder="0.00"
       inputmode="decimal">
<input type="hidden" name="amount" id="expense_amount">
```

**ADD JavaScript:**
```javascript
<script>
document.addEventListener('DOMContentLoaded', function() {
    CurrencyHelper.setupInput('expense_amount_display', 'expense_amount', {
        decimals: 2,
        maxValue: 9999999999.99
    });
});
</script>
```

**Testing:** Same as Task 1.1

---

### Task 1.3: Project Financial Modals - Payment Amount

**File:** `resources/views/projects/partials/financial-modals.blade.php`

**Line 191 - CURRENT:**
```html
<input type="number" name="amount" step="0.01" min="0.01" required
       class="input-dark w-full px-4 py-2.5 rounded-lg"
       placeholder="0.00">
```

**REPLACE WITH:**
```html
<input type="text" 
       id="invoice_payment_amount_display"
       class="input-dark w-full px-4 py-2.5 rounded-lg"
       placeholder="0.00"
       inputmode="decimal">
<input type="hidden" name="amount" id="invoice_payment_amount">
```

**ADD JavaScript (in existing submitInvoicePayment function):**
```javascript
// Add at the beginning of modal functions section
document.addEventListener('DOMContentLoaded', function() {
    CurrencyHelper.setupInput('invoice_payment_amount_display', 'invoice_payment_amount');
});

// Update submitInvoicePayment function
function submitInvoicePayment(event) {
    event.preventDefault();
    
    // Ensure hidden input is updated
    const displayInput = document.getElementById('invoice_payment_amount_display');
    const hiddenInput = document.getElementById('invoice_payment_amount');
    if (displayInput && displayInput.value) {
        hiddenInput.value = CurrencyHelper.parse(displayInput.value);
    }
    
    // Rest of the function...
}
```

---

### Task 1.4: Project Financial Modals - Direct Income

**File:** `resources/views/projects/partials/financial-modals.blade.php`

**Line 277 - Replace similar to above:**
```html
<input type="text" 
       id="direct_income_amount_display"
       class="input-dark w-full px-4 py-2.5 rounded-lg"
       placeholder="0.00"
       inputmode="decimal">
<input type="hidden" name="amount" id="direct_income_amount">

<script>
CurrencyHelper.setupInput('direct_income_amount_display', 'direct_income_amount');
</script>
```

---

### Task 1.5: Project Financial Modals - Direct Expense

**File:** `resources/views/projects/partials/financial-modals.blade.php`

**Line 338:**
```html
<input type="text" 
       id="direct_expense_amount_display"
       class="input-dark w-full px-4 py-2.5 rounded-lg"
       placeholder="0.00"
       inputmode="decimal">
<input type="hidden" name="amount" id="direct_expense_amount">

<script>
CurrencyHelper.setupInput('direct_expense_amount_display', 'direct_expense_amount');
</script>
```

---

### Task 1.6: Invoice Item - Unit Price

**File:** `resources/views/projects/partials/financial-modals.blade.php`

**Line 537 (dynamic form):**

**FIND:**
```html
<input type="number" name="items[${invoiceItemCount}][unit_price]" 
       step="0.01" min="0" required>
```

**REPLACE WITH:**
```html
<input type="text" 
       id="invoice_item_price_${invoiceItemCount}_display"
       class="form-control"
       placeholder="0.00"
       inputmode="decimal">
<input type="hidden" 
       name="items[${invoiceItemCount}][unit_price]" 
       id="invoice_item_price_${invoiceItemCount}">
```

**UPDATE addInvoiceItem() function:**
```javascript
function addInvoiceItem() {
    invoiceItemCount++;
    
    // ... existing row HTML ...
    
    // Setup currency input for the new row
    CurrencyHelper.setupInput(
        `invoice_item_price_${invoiceItemCount}_display`,
        `invoice_item_price_${invoiceItemCount}`,
        {
            decimals: 2,
            onUpdate: function(value) {
                calculateInvoiceTotal(); // Recalculate when price changes
            }
        }
    );
}
```

---

### Task 1.7: Project Show/Edit - Payment Amount

**File:** `resources/views/projects/show.blade.php`

**Line 1018:**
```html
<input type="text" 
       id="payment_amount_display"
       class="input-dark w-full px-4 py-2 rounded-lg" 
       placeholder="0.00"
       inputmode="decimal">
<input type="hidden" name="amount" id="payment_amount">

<script>
CurrencyHelper.setupInput('payment_amount_display', 'payment_amount');
</script>
```

---

### Task 1.8: Project Edit - Budget & Actual Cost

**File:** `resources/views/projects/edit.blade.php`

**Line 227 (Budget):**
```html
<input type="text" 
       id="budget_display" 
       class="form-control"
       placeholder="0.00"
       inputmode="decimal"
       value="{{ old('budget', \App\Helpers\CurrencyHelper::format($project->budget, 2, false)) }}">
<input type="hidden" 
       id="budget" 
       name="budget" 
       value="{{ old('budget', $project->budget) }}">
```

**Line 242 (Actual Cost):**
```html
<input type="text" 
       id="actual_cost_display"
       class="form-control"
       placeholder="0.00"
       inputmode="decimal"
       value="{{ old('actual_cost', \App\Helpers\CurrencyHelper::format($project->actual_cost, 2, false)) }}">
<input type="hidden" 
       id="actual_cost" 
       name="actual_cost" 
       value="{{ old('actual_cost', $project->actual_cost) }}">
```

**ADD JavaScript:**
```javascript
<script>
document.addEventListener('DOMContentLoaded', function() {
    CurrencyHelper.setupMultiple([
        {displayId: 'budget_display', hiddenId: 'budget'},
        {displayId: 'actual_cost_display', hiddenId: 'actual_cost'}
    ]);
});
</script>
```

---

### ✅ Phase 1 Checklist

- [ ] Task 1.1: General Transactions Income ✓
- [ ] Task 1.2: General Transactions Expense ✓
- [ ] Task 1.3: Payment Amount ✓
- [ ] Task 1.4: Direct Income ✓
- [ ] Task 1.5: Direct Expense ✓
- [ ] Task 1.6: Invoice Unit Price ✓
- [ ] Task 1.7: Project Payment ✓
- [ ] Task 1.8: Budget & Cost ✓
- [ ] Test all forms end-to-end
- [ ] Verify database values are correct

**Estimasi:** 8 jam kerja  
**Completion Target:** End of Week 1

---

## 🎨 PHASE 2: Display Format PHP (Week 2)

### Global Replace Strategy

Gunakan Find & Replace di VS Code:

**Find (Regex):**
```regex
number_format\(([^,]+),\s*0,\s*',',\s*'\.'\)
```

**Replace:**
```php
number_format($1, 2, '.', ',')
```

atau lebih baik, gunakan helper:

**Replace:**
```php
\App\Helpers\CurrencyHelper::format($1, 2, false)
```

### Task 2.1: Projects Show Page

**File:** `resources/views/projects/show.blade.php`

**Lines to fix:** 218, 223, 327, 623, 628, 636, 645, 914, 918, 922, 1001

**OLD:**
```php
Rp {{ number_format($totalBudget, 0, ',', '.') }}
```

**NEW:**
```php
{{ \App\Helpers\CurrencyHelper::format($totalBudget) }}
```

atau dengan Blade directive:

**NEW (setelah register directive):**
```php
@currency($totalBudget)
```

### Task 2.2: Financial Tab

**File:** `resources/views/projects/partials/financial-tab.blade.php`

Replace semua 12 instances dengan helper.

### Task 2.3: Reconciliation Pages

**Files:**
- `reconciliations/show.blade.php` (13 instances)
- `reconciliations/index.blade.php` (3 instances)
- `reconciliations/match.blade.php` (5 instances)

Replace all dengan Currency Helper.

### Task 2.4: Cash Accounts

**Files:**
- `cash-accounts/tabs/general-transactions.blade.php` (5 instances)
- `cash-accounts/tabs/transactions.blade.php` (5 instances)

Replace all.

### ✅ Phase 2 Checklist

- [ ] Projects Show (11 fixes)
- [ ] Financial Tab (12 fixes)
- [ ] Reconciliation Show (13 fixes)
- [ ] Reconciliation Index (3 fixes)
- [ ] Reconciliation Match (5 fixes)
- [ ] General Transactions (5 fixes)
- [ ] Transactions Tab (5 fixes)
- [ ] Visual verification semua pages
- [ ] Screenshot before/after

**Estimasi:** 4 jam kerja  
**Completion Target:** Mid Week 2

---

## 🔧 PHASE 3: JavaScript Display (Week 2)

### Task 3.1: Financial Modals JavaScript

**File:** `resources/views/projects/partials/financial-modals.blade.php`

**Lines:** 496, 668, 766, 1190

**FIND:**
```javascript
toLocaleString('id-ID')
```

**REPLACE:**
```javascript
toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })
```

atau gunakan helper:

**REPLACE:**
```javascript
CurrencyHelper.format(value)
```

### Task 3.2: Reconciliation Match

**File:** `resources/views/reconciliations/match.blade.php`

**Lines:** 405, 461

Replace dengan CurrencyHelper.format()

### ✅ Phase 3 Checklist

- [ ] Financial Modals (4 fixes)
- [ ] Reconciliation Match (2 fixes)
- [ ] Browser console testing
- [ ] Mobile testing

**Estimasi:** 2 jam kerja  
**Completion Target:** End of Week 2

---

## 🧪 PHASE 4: Testing & Validation (Week 3)

### Test Scenarios

#### 1. Input Testing
```
Test Case 1: Small Amount
Input: 100
Expected Display: 100.00
Expected Hidden: 100.00
Expected DB: 100.00

Test Case 2: Medium Amount with Decimals
Input: 1234.56
Expected Display: 1,234.56
Expected Hidden: 1234.56
Expected DB: 1234.56

Test Case 3: Large Amount
Input: 42485447.23
Expected Display: 42,485,447.23
Expected Hidden: 42485447.23
Expected DB: 42485447.23

Test Case 4: Zero (Should Fail Validation)
Input: 0
Expected: Validation error

Test Case 5: Negative (Should Fail if not allowed)
Input: -100
Expected: Validation error or accepted based on config
```

#### 2. Display Testing
- [ ] All currency displays show format 1,234.56
- [ ] No mix of Indonesian format (1.234,56)
- [ ] Consistency across all pages
- [ ] Mobile responsive

#### 3. Integration Testing
- [ ] Submit form → Check database value
- [ ] Edit existing → Pre-fill with correct format
- [ ] Calculate totals → Correct math
- [ ] Reports/exports → Correct format

#### 4. Browser Testing
- [ ] Chrome
- [ ] Firefox
- [ ] Safari
- [ ] Mobile Chrome
- [ ] Mobile Safari

### Testing Script

Create: `test_currency_format_system.php`

```php
<?php
require __DIR__.'/vendor/autoload.php';

use App\Helpers\CurrencyHelper;

echo "=== CURRENCY FORMAT SYSTEM TEST ===\n\n";

// Test 1: Format
$tests = [
    1234.56 => 'Rp 1,234.56',
    42485447.23 => 'Rp 42,485,447.23',
    0 => 'Rp 0.00',
    -100 => 'Rp -100.00'
];

echo "Test 1: Format\n";
foreach ($tests as $input => $expected) {
    $result = CurrencyHelper::format($input);
    $status = $result === $expected ? '✓' : '✗';
    echo "  $status Input: $input → Output: $result (Expected: $expected)\n";
}

// Test 2: Parse
echo "\nTest 2: Parse\n";
$parseTests = [
    '1,234.56' => 1234.56,
    '1.234,56' => 1234.56, // Indonesian format
    'Rp 42,485,447.23' => 42485447.23
];

foreach ($parseTests as $input => $expected) {
    $result = CurrencyHelper::parse($input);
    $status = $result === $expected ? '✓' : '✗';
    echo "  $status Input: $input → Output: $result (Expected: $expected)\n";
}

// Test 3: Validation
echo "\nTest 3: Validation\n";
$validation = CurrencyHelper::validate(0, ['allow_zero' => false]);
echo "  " . ($validation['valid'] ? '✗' : '✓') . " Zero should fail: " . $validation['error'] . "\n";

$validation = CurrencyHelper::validate(100, ['min' => 0]);
echo "  " . ($validation['valid'] ? '✓' : '✗') . " Valid amount: 100\n";

echo "\n=== ALL TESTS COMPLETE ===\n";
```

Run:
```bash
php test_currency_format_system.php
```

### ✅ Phase 4 Checklist

- [ ] Unit tests pass
- [ ] Integration tests pass
- [ ] Manual testing complete
- [ ] Mobile testing complete
- [ ] User acceptance testing
- [ ] Performance check (no slowdown)
- [ ] Documentation updated

**Estimasi:** 4 jam kerja  
**Completion Target:** End of Week 3

---

## 📊 Progress Tracking

| Phase | Status | Progress | Completion Date |
|-------|--------|----------|-----------------|
| Setup | 🟡 In Progress | 0% | - |
| Phase 1: Input Forms | ⚪ Not Started | 0/8 | - |
| Phase 2: Display PHP | ⚪ Not Started | 0/54 | - |
| Phase 3: JavaScript | ⚪ Not Started | 0/6 | - |
| Phase 4: Testing | ⚪ Not Started | 0% | - |

**Overall Progress:** 0% (0/70 tasks)

---

## 🚀 Quick Start Command

```bash
# 1. Verify helpers exist
ls -la app/Helpers/CurrencyHelper.php
ls -la public/js/currency-helper.js

# 2. Clear caches
php artisan config:clear
php artisan view:clear
php artisan cache:clear

# 3. Run test
php test_currency_format_system.php

# 4. Start with Task 1.1
# Edit: resources/views/cash-accounts/tabs/general-transactions.blade.php
```

---

**Last Updated:** 22 November 2025  
**Next Review:** After Phase 1 completion
