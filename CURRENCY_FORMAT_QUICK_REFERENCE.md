# Currency Format System - Quick Reference Card

**Format Standard:** `1,234.56` (comma = thousand separator, period = decimal)

---

## 🚀 Quick Start

### 1. Load Helper (di layouts/app.blade.php)
```html
<script src="{{ asset('js/currency-helper.js') }}"></script>
```

### 2. Setup Currency Input
```html
<!-- HTML -->
<input type="text" id="amount_display" placeholder="0.00">
<input type="hidden" name="amount" id="amount">

<!-- JavaScript -->
<script>
CurrencyHelper.setupInput('amount_display', 'amount');
</script>
```

### 3. Display Currency (Blade)
```php
<!-- Using helper -->
{{ \App\Helpers\CurrencyHelper::format($amount) }}
<!-- Output: Rp 1,234.56 -->

<!-- Or with custom format -->
{{ \App\Helpers\CurrencyHelper::format($amount, 2, false) }}
<!-- Output: 1,234.56 (without Rp) -->
```

---

## 📖 JavaScript API

```javascript
// Format number to currency string
CurrencyHelper.format(1234.56)  // "1,234.56"

// Parse currency string to number
CurrencyHelper.parse("1,234.56")  // 1234.56

// Setup dual input system
CurrencyHelper.setupInput('display_id', 'hidden_id', {
    decimals: 2,
    allowNegative: false,
    maxValue: 9999999999,
    onUpdate: (value, formatted) => { /* callback */ }
});

// Validate currency input
CurrencyHelper.validate(100, {
    min: 0,
    max: 10000,
    allowZero: false
});  // {valid: true, error: null}

// Validate entire form
CurrencyHelper.validateForm(formElement, [
    {
        displayId: 'amount_display',
        hiddenId: 'amount',
        fieldName: 'Amount',
        required: true,
        validate: { min: 0.01 }
    }
]);

// Display currency in element
CurrencyHelper.display('element_id', 1234.56, {
    prefix: 'Rp ',
    decimals: 2,
    showSign: false
});

// Normalize various formats
CurrencyHelper.normalize('1.234,56')  // 1234.56 (ID format)
CurrencyHelper.normalize('1,234.56')  // 1234.56 (INT format)
```

---

## 📖 PHP API

```php
use App\Helpers\CurrencyHelper;

// Format for display
CurrencyHelper::format(1234.56)  // "Rp 1,234.56"
CurrencyHelper::format(1234.56, 2, false)  // "1,234.56"

// Compact format
CurrencyHelper::formatCompact(1234567)  // "Rp 1.2M"
CurrencyHelper::formatCompact(45000)    // "Rp 45.0K"

// Parse string to float
CurrencyHelper::parse("1,234.56")  // 1234.56
CurrencyHelper::parse("Rp 1.234,56")  // 1234.56

// Normalize (handle both formats)
CurrencyHelper::normalize("1.234,56")  // 1234.56
CurrencyHelper::normalize("1,234.56")  // 1234.56

// Validate with rules
$validation = CurrencyHelper::validate(100, [
    'min' => 0,
    'max' => 10000,
    'allow_zero' => false,
    'allow_negative' => false
]);
// ['valid' => true, 'error' => null]

// Format percentage
CurrencyHelper::formatPercentage(25, 100)  // "25.0%"

// Format difference with sign
CurrencyHelper::formatDifference(150, 100)  // "+Rp 50.00"
CurrencyHelper::formatDifference(150, 100, true)  // "+50.0%"

// Array operations
CurrencyHelper::sum([100, 200, 300])  // 600.0
CurrencyHelper::average([100, 200, 300])  // 200.0
CurrencyHelper::formatArray([100, 200])  // ["Rp 100.00", "Rp 200.00"]

// To array (for API)
CurrencyHelper::toArray(1234.56)
// [
//     'raw' => 1234.56,
//     'formatted' => '1,234.56',
//     'display' => 'Rp 1,234.56',
//     'compact' => 'Rp 1.2K'
// ]
```

---

## 🎨 Common Patterns

### Pattern 1: Input Form with Validation
```html
<form id="myForm" onsubmit="return handleSubmit(event)">
    <input type="text" id="amount_display" placeholder="0.00">
    <input type="hidden" name="amount" id="amount" required>
    <button type="submit">Submit</button>
</form>

<script>
// Setup input
CurrencyHelper.setupInput('amount_display', 'amount');

// Handle submit
function handleSubmit(e) {
    e.preventDefault();
    
    const validation = CurrencyHelper.validateForm(e.target, [
        {
            displayId: 'amount_display',
            hiddenId: 'amount',
            fieldName: 'Amount',
            required: true,
            validate: { min: 0.01, allowZero: false }
        }
    ]);
    
    if (!validation.valid) {
        alert('Error:\n' + validation.errors.join('\n'));
        return false;
    }
    
    // Form is valid, submit
    e.target.submit();
}
</script>
```

### Pattern 2: Multiple Currency Inputs
```javascript
// Setup multiple at once
CurrencyHelper.setupMultiple([
    {displayId: 'income_display', hiddenId: 'income'},
    {displayId: 'expense_display', hiddenId: 'expense'},
    {displayId: 'budget_display', hiddenId: 'budget', options: {maxValue: 1000000}}
]);
```

### Pattern 3: Dynamic Display Update
```javascript
// Calculate and display total
function updateTotal() {
    const income = CurrencyHelper.parse(document.getElementById('income_display').value);
    const expense = CurrencyHelper.parse(document.getElementById('expense_display').value);
    const total = income - expense;
    
    CurrencyHelper.display('total_display', total, {
        prefix: 'Rp ',
        showSign: true
    });
}
```

### Pattern 4: Blade Template Display
```php
<!-- Financial Summary Card -->
<div class="card">
    <h3>Total Budget</h3>
    <p class="amount">
        {{ \App\Helpers\CurrencyHelper::format($totalBudget) }}
    </p>
    <p class="percentage">
        {{ \App\Helpers\CurrencyHelper::formatPercentage($spent, $totalBudget) }} spent
    </p>
</div>
```

---

## 🧪 Testing Checklist

### Input Testing
- [ ] Type "1234" → Display shows "1,234.00"
- [ ] Type "1234.5" → Display shows "1,234.50"
- [ ] Type "1234.567" → Display shows "1,234.56" (truncated)
- [ ] Type "0" → Validation fails (if required)
- [ ] Paste "1,234.56" → Formatted correctly
- [ ] Mobile keyboard shows numeric keyboard

### Display Testing
- [ ] All amounts show format 1,234.56
- [ ] No mixing of Indonesian format (1.234,56)
- [ ] Negative amounts show correctly
- [ ] Large numbers (millions) formatted correctly
- [ ] Zero shows as "0.00"

### Form Submission Testing
- [ ] Hidden input has correct numeric value
- [ ] Server receives raw number (1234.56)
- [ ] Database stores correct value
- [ ] Edit form pre-fills with formatted value
- [ ] Validation works correctly

---

## ⚠️ Common Mistakes

### ❌ DON'T DO THIS:
```html
<!-- Wrong: Using type="number" -->
<input type="number" name="amount" step="0.01">

<!-- Wrong: No hidden input -->
<input type="text" name="amount" id="amount_display">

<!-- Wrong: Indonesian format in number_format -->
{{ number_format($amount, 0, ',', '.') }}  <!-- 1.234.567 -->

<!-- Wrong: Using id-ID locale -->
amount.toLocaleString('id-ID')  <!-- 1.234,56 -->
```

### ✅ DO THIS INSTEAD:
```html
<!-- Correct: Dual input system -->
<input type="text" id="amount_display" placeholder="0.00">
<input type="hidden" name="amount" id="amount">
<script>
CurrencyHelper.setupInput('amount_display', 'amount');
</script>

<!-- Correct: International format -->
{{ \App\Helpers\CurrencyHelper::format($amount) }}  <!-- Rp 1,234.56 -->

<!-- Correct: en-US locale -->
amount.toLocaleString('en-US', {minimumFractionDigits: 2})  <!-- 1,234.56 -->
```

---

## 🔧 Troubleshooting

### Problem: Input not formatting
**Solution:** Check if CurrencyHelper is loaded:
```javascript
console.log(typeof CurrencyHelper);  // Should be "object"
```

### Problem: Hidden input empty on submit
**Solution:** Ensure setupInput is called after DOM loads:
```javascript
document.addEventListener('DOMContentLoaded', function() {
    CurrencyHelper.setupInput('display_id', 'hidden_id');
});
```

### Problem: Validation not working
**Solution:** Update hidden input before validation:
```javascript
// In form submit handler
if (displayInput.value) {
    hiddenInput.value = CurrencyHelper.parse(displayInput.value);
}
```

### Problem: Display shows NaN
**Solution:** Check for valid numeric value:
```javascript
const value = parseFloat(rawValue);
if (isNaN(value)) {
    // Handle invalid input
}
```

---

## 📚 Documentation Files

- **CURRENCY_FORMAT_ANALYSIS.md** - Detailed analysis (70 items to fix)
- **CURRENCY_FORMAT_IMPLEMENTATION.md** - Step-by-step guide
- **CURRENCY_FORMAT_SUMMARY.md** - Executive summary
- **test_currency_format_system.html** - Interactive test suite

---

## 🎯 Best Practices

1. **Always use dual input system** for currency inputs
2. **Always use CurrencyHelper.format()** for display
3. **Never mix formats** (stay with 1,234.56)
4. **Validate on both client and server** side
5. **Use helper functions** instead of custom implementations
6. **Test on mobile** devices for keyboard behavior
7. **Handle edge cases** (zero, negative, very large numbers)
8. **Document currency fields** in code comments

---

**Last Updated:** 22 November 2025  
**Version:** 1.0
