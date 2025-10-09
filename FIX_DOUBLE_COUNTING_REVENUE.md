# Fix: Double Counting Revenue & Chart Data

**Date:** 2025-10-03  
**Issue:** Card "Diterima" dan Chart "Pemasukan vs Pengeluaran" menampilkan data ganda karena double counting payment

---

## 🐛 **Root Cause**

Setelah menambahkan fitur linking payment ke invoice via `invoice_id`, terjadi double counting karena:

1. **Payment tercatat di `project_payments`** → `sum(amount)` = 90jt
2. **Payment juga update `invoice->paid_amount`** via model events → `sum(paid_amount)` = 90jt
3. **Kedua angka dijumlahkan** → Total = 180jt ❌

### **Masalah Ditemukan di:**

#### **1. ProjectController - Total Received Card**
```php
// ❌ SALAH (before)
$manualPayments = $project->payments()->sum('amount');      // 90jt
$invoicePayments = $project->invoices()->sum('paid_amount'); // 90jt
$totalReceived = $manualPayments + $invoicePayments;        // 180jt ⚠️
```

#### **2. ProjectController - Monthly Chart**
```php
// ❌ SALAH (before)
$manualPayments = $project->payments()
    ->whereMonth('payment_date', $month)
    ->sum('amount');

$invoicePayments = $project->invoices()
    ->whereMonth('invoice_date', $month)
    ->sum('paid_amount');

$monthIncome = $manualPayments + $invoicePayments; // ⚠️ Double!
```

#### **3. FinancialController - Monthly Chart**
```php
// ❌ SALAH (before) - Menggunakan invoice_date, bukan payment_date
$monthIncome = $project->invoices()
    ->whereMonth('invoice_date', $month)
    ->sum('paid_amount');
```

---

## ✅ **Solution Applied**

### **1. Fix Total Received Calculation**

**File:** `app/Http/Controllers/ProjectController.php`

```php
// ✅ BENAR (after)
// Calculate from invoice paid_amount (already includes linked payments)
$invoicePayments = $project->invoices()->sum('paid_amount');

// Add manual payments NOT linked to any invoice (for backward compatibility)
$manualPaymentsNotLinked = $project->payments()
    ->whereNull('invoice_id')
    ->sum('amount');

$totalReceived = $invoicePayments + $manualPaymentsNotLinked;
```

**Logika:**
- Hitung dari `invoice->paid_amount` (sudah otomatis terupdate via model events)
- Tambahkan payment yang `invoice_id = NULL` (jika ada payment manual legacy)
- Hindari double counting payment yang sudah di-link ke invoice

---

### **2. Fix Monthly Chart - ProjectController**

**File:** `app/Http/Controllers/ProjectController.php` → `getMonthlyFinancialData()`

```php
// ✅ BENAR (after)
// Calculate income from ALL payments in this month
// (both invoice-linked and manual payments)
$monthIncome = $project->payments()
    ->whereMonth('payment_date', $date->month)
    ->whereYear('payment_date', $date->year)
    ->sum('amount');
```

**Logika:**
- Hitung langsung dari `project_payments` berdasarkan `payment_date`
- Semua payment dihitung sekali saja (baik yang linked maupun tidak)
- Chart mencerminkan kapan uang **benar-benar diterima**

---

### **3. Fix Monthly Chart - FinancialController**

**File:** `app/Http/Controllers/FinancialController.php` → `getMonthlyFinancialData()`

```php
// ✅ BENAR (after)
// Calculate income from actual payment dates, not invoice dates
$monthIncome = $project->payments()
    ->whereMonth('payment_date', $date->month)
    ->whereYear('payment_date', $date->year)
    ->sum('amount');
```

**Mengapa pakai `payment_date` bukan `invoice_date`?**
- Invoice date = kapan invoice dibuat/dikirim
- Payment date = kapan uang benar-benar diterima
- Chart harus menampilkan **cashflow aktual**, bukan tanggal invoice

---

## 📊 **Verification Results**

### **Project #42 - Before Fix:**
```
Nilai Kontrak: Rp 180.000.000
Diterima: Rp 180.000.000 (100% terbayar) ❌
Pengeluaran: Rp 18.777.000
Profit: Rp 161.223.000 (89.6% margin) ❌
```

### **Project #42 - After Fix:**
```
Nilai Kontrak: Rp 180.000.000
Diterima: Rp 90.000.000 (50% terbayar) ✅
Pengeluaran: Rp 18.777.000
Profit: Rp 71.223.000 (79.1% margin) ✅
```

### **Chart Data - After Fix:**
```
Oct 2025:
  Pemasukan: Rp 90.000.000 ✅
  Pengeluaran: Rp 0

Sep 2025:
  Pemasukan: Rp 0
  Pengeluaran: Rp 18.777.000 ✅
```

---

## 🔄 **Impact on Existing Data**

### **Scenario 1: Payment Linked to Invoice (New System)**
```
1 Payment (90jt) → Linked to Invoice #33
✅ Counted once in totalReceived
✅ Chart shows payment on payment_date
```

### **Scenario 2: Manual Payment Without Invoice (Legacy)**
```
1 Payment (50jt) → invoice_id = NULL
✅ Counted in manualPaymentsNotLinked
✅ Chart shows payment on payment_date
```

### **Scenario 3: Multiple Payments for 1 Invoice**
```
Invoice #33: Total 100jt
- Payment #1: 30jt (DP) → linked
- Payment #2: 70jt (Pelunasan) → linked

Invoice->paid_amount = 100jt (auto-updated)
✅ totalReceived = 100jt (no double counting)
✅ Chart shows 30jt + 70jt on respective payment_dates
```

---

## 🎯 **Testing Checklist**

After refresh browser, verify:

- [ ] Card "Diterima" shows correct amount (not doubled)
- [ ] Card "Profit" calculated correctly (received - expenses)
- [ ] Chart displays income on actual payment dates
- [ ] Chart income matches sum of all payments
- [ ] No double counting for invoice-linked payments
- [ ] Legacy manual payments (if any) still counted

---

## 📝 **Related Files Modified**

1. `app/Http/Controllers/ProjectController.php`
   - Method: `show()` - Line ~145-155
   - Method: `getMonthlyFinancialData()` - Line ~287-295

2. `app/Http/Controllers/FinancialController.php`
   - Method: `getMonthlyFinancialData()` - Line ~453-461

---

## 🔗 **Related Fixes**

- [FINANCIAL_INVOICE_FIX.md](FINANCIAL_INVOICE_FIX.md) - Invoice-Payment linking architecture
- [FIX_PAYMENT_METHOD_ENUM.md](FIX_PAYMENT_METHOD_ENUM.md) - ENUM mismatch fix
- [FEATURE_DELETE_INVOICE.md](FEATURE_DELETE_INVOICE.md) - Delete invoice with validation

---

**Status:** ✅ Fixed  
**Tested:** Project #42  
**Cache Cleared:** `php artisan optimize:clear`
