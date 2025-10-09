# Feature: Delete Invoice with Validation

## 📋 Overview

Fitur untuk menghapus invoice dengan validasi ketat untuk menjaga integritas data keuangan.

---

## ✨ Features

### **1. Smart Delete Button**

Tombol hapus **hanya muncul** untuk invoice dengan status:
- ✅ `draft` - Invoice yang belum dikirim
- ✅ `cancelled` - Invoice yang sudah dibatalkan

Tombol **TIDAK muncul** untuk:
- ❌ `sent` - Invoice sudah dikirim ke client
- ❌ `partial` - Invoice sedang dalam proses pembayaran
- ❌ `paid` - Invoice sudah lunas
- ❌ `overdue` - Invoice terlambat bayar

### **2. Multi-Level Validation**

Backend melakukan validasi berlapis:

#### **Validation 1: Check Payments**
```php
if ($invoice->payments()->exists()) {
    return error('Tidak dapat menghapus invoice yang sudah memiliki pembayaran');
}
```
**Alasan**: Invoice yang sudah ada pembayaran tidak boleh dihapus untuk menjaga audit trail.

#### **Validation 2: Check Status**
```php
if ($invoice->status !== 'draft' && $invoice->status !== 'cancelled') {
    return error('Hanya invoice Draft atau Cancelled yang dapat dihapus');
}
```
**Alasan**: Invoice yang sudah sent/paid harus mengikuti proper cancellation flow.

### **3. Cascade Delete**

Saat invoice dihapus, data terkait juga dihapus:
- ✅ Invoice Items (line items)
- ✅ Payment Schedules (jadwal termin)
- ✅ Invoice file (soft delete)

**Yang TIDAK dihapus**:
- ❌ Project (parent tetap ada)
- ❌ Payments (jika ada, delete akan diblock)

### **4. Activity Logging**

Setiap penghapusan tercatat di project log:
```php
ProjectLog::create([
    'project_id' => $projectId,
    'user_id' => auth()->id(),
    'description' => "Invoice INV-202510-0001 dihapus",
]);
```

### **5. User-Friendly UI**

**Confirmation Dialog:**
```
⚠️ HAPUS INVOICE

Apakah Anda yakin ingin menghapus invoice ini?

⚠️ Perhatian:
• Hanya invoice Draft atau Cancelled yang dapat dihapus
• Invoice yang sudah ada pembayaran tidak dapat dihapus
• Data invoice item dan jadwal pembayaran akan ikut terhapus

Proses ini TIDAK DAPAT dibatalkan!
```

**Loading Indicator:**
- Blue toast: "Menghapus invoice..."

**Success Message:**
- Green toast: "✅ Invoice berhasil dihapus"
- Auto reload after 1 second

**Error Handling:**
- Red alert with detailed error message

---

## 🔄 User Flow

### **Scenario 1: Delete Draft Invoice (Success)**

```
1. User clicks 🗑️ button on draft invoice
2. Confirmation dialog appears
3. User clicks OK
4. Loading toast: "Menghapus invoice..."
5. Backend validates:
   ✅ Status = draft
   ✅ No payments
6. Delete invoice + items + schedules
7. Success toast: "✅ Invoice berhasil dihapus"
8. Page reload → invoice gone
```

### **Scenario 2: Try Delete Paid Invoice (Blocked - UI Level)**

```
1. User views paid invoice
2. 🗑️ button NOT VISIBLE
3. Cannot delete (prevented at UI level)
```

### **Scenario 3: Try Delete Invoice with Payment (Blocked - Backend)**

```
1. Admin somehow bypasses UI (e.g., API call)
2. Clicks delete
3. Confirmation appears
4. User clicks OK
5. Backend validates:
   ❌ Has payments!
6. Error alert:
   "❌ Tidak dapat menghapus invoice yang sudah memiliki pembayaran.
    Hapus pembayaran terlebih dahulu atau gunakan fitur Batalkan Invoice."
```

### **Scenario 4: Try Delete Sent Invoice (Blocked - Backend)**

```
1. Invoice status = 'sent'
2. Button visible (if manually changed)
3. User tries to delete
4. Backend validates:
   ❌ Status != draft && != cancelled
5. Error alert:
   "❌ Hanya invoice dengan status Draft atau Cancelled yang dapat dihapus.
    Invoice dengan status lain harus dibatalkan terlebih dahulu."
```

---

## 💾 Database Impact

### **Tables Affected:**

1. **invoices** (soft delete)
   ```sql
   UPDATE invoices SET deleted_at = NOW() WHERE id = ?
   ```

2. **invoice_items** (hard delete)
   ```sql
   DELETE FROM invoice_items WHERE invoice_id = ?
   ```

3. **payment_schedules** (hard delete)
   ```sql
   DELETE FROM payment_schedules WHERE invoice_id = ?
   ```

4. **project_logs** (insert)
   ```sql
   INSERT INTO project_logs (project_id, user_id, description, created_at)
   VALUES (?, ?, 'Invoice INV-XXX dihapus', NOW())
   ```

### **Data NOT Affected:**

- ❌ `projects` (parent remain intact)
- ❌ `project_payments` (protected by validation)
- ❌ `clients` (no impact)

---

## 🎯 Business Rules

### **When to Use Delete vs Cancel**

| Situation | Action | Reason |
|-----------|--------|--------|
| Invoice baru dibuat, belum dikirim | **DELETE** ✅ | Draft dapat dihapus tanpa jejak |
| Invoice salah input, belum ada payment | **DELETE** ✅ | Cancelled invoice bisa dihapus |
| Invoice sudah dikirim ke client | **CANCEL** ⚠️ | Perlu audit trail |
| Invoice sedang dibayar (partial) | **CANCEL** ⚠️ | Ada transaksi terkait |
| Invoice sudah lunas | **REFUND + CANCEL** ⚠️ | Buat credit note |

### **Best Practices**

1. ✅ **Delete draft**: OK untuk kesalahan input sebelum dikirim
2. ✅ **Cancel sent**: Gunakan flow cancellation jika sudah dikirim
3. ❌ **Never delete paid**: Buat credit note untuk refund
4. ✅ **Log everything**: Setiap delete tercatat di project log

---

## 🔒 Security

### **Authorization** (TODO - Enhancement)

Current: Basic authentication check via `auth()->id()`

Recommended enhancement:
```php
// In controller
if (!auth()->user()->can('delete', $invoice)) {
    abort(403, 'Unauthorized to delete this invoice');
}

// In Policy
public function delete(User $user, Invoice $invoice)
{
    // Only owner or admin can delete
    return $user->id === $invoice->project->user_id 
        || $user->hasRole('admin');
}
```

### **Audit Trail**

All deletes logged in `project_logs`:
- Who: `user_id`
- What: `description`
- When: `created_at`
- Where: `project_id`

---

## 🧪 Testing

### **Manual Test Cases**

**Test 1: Delete Draft Invoice**
```
✅ Create new invoice (status: draft)
✅ Click delete button
✅ Confirm dialog
✅ Verify: Invoice deleted
✅ Verify: Items deleted
✅ Verify: Schedules deleted
✅ Verify: Log created
```

**Test 2: Cannot Delete with Payment**
```
✅ Create invoice (status: draft)
✅ Add payment to invoice
✅ Try to delete
❌ Expect: Error message
✅ Verify: Invoice still exists
```

**Test 3: Button Visibility**
```
✅ Draft invoice → Button visible
✅ Cancelled invoice → Button visible
❌ Sent invoice → Button hidden
❌ Paid invoice → Button hidden
❌ Partial invoice → Button hidden
```

**Test 4: Activity Logging**
```
✅ Delete invoice
✅ Check project_logs table
✅ Verify entry exists with correct description
✅ Verify user_id = current user
```

### **Edge Cases**

1. ✅ Delete invoice with 0 items (allowed)
2. ✅ Delete invoice with 0 schedules (allowed)
3. ❌ Delete invoice with payment (blocked)
4. ❌ Delete paid invoice (button hidden + backend block)
5. ✅ Concurrent delete (Laravel transaction handling)

---

## 📁 Files Modified

### **Backend**

**File**: `app/Http/Controllers/FinancialController.php`

**Method**: `destroyInvoice(Invoice $invoice)`

**Changes**:
- Added payment validation
- Added status validation
- Added cascade delete for items and schedules
- Added activity logging
- Added proper error handling
- Changed messages to Indonesian

### **Frontend**

**File**: `resources/views/projects/partials/financial-modals.blade.php`

**Function**: `deleteInvoice(invoiceId)`

**Changes**:
- Enhanced confirmation dialog with detailed warnings
- Added loading indicator (blue toast)
- Added success message (green toast with auto reload)
- Improved error handling with detailed messages
- Better UX with toast notifications

**File**: `resources/views/projects/partials/financial-tab.blade.php`

**Changes**:
- Updated delete button condition: `draft || cancelled`
- Updated tooltip: "Hapus Invoice"

### **Routes**

**File**: `routes/web.php`

**Route**: `DELETE /invoices/{invoice}` ✅ Already exists

---

## 🚀 Usage

### **Via UI**

1. Go to Project Detail → Financial Tab
2. Find invoice with status "Draft" or "Cancelled"
3. Click 🗑️ trash icon in Actions column
4. Read confirmation dialog carefully
5. Click OK to delete
6. Wait for success message
7. Page will reload automatically

### **Via API** (for integrations)

```bash
# Delete invoice
curl -X DELETE https://bizmark.id/invoices/123 \
  -H "X-CSRF-TOKEN: your-token" \
  -H "Accept: application/json"

# Success Response (200)
{
  "success": true,
  "message": "Invoice berhasil dihapus"
}

# Error Response - Has Payment (422)
{
  "success": false,
  "message": "Tidak dapat menghapus invoice yang sudah memiliki pembayaran..."
}

# Error Response - Wrong Status (422)
{
  "success": false,
  "message": "Hanya invoice dengan status Draft atau Cancelled yang dapat dihapus..."
}
```

---

## 🔮 Future Enhancements

### **1. Soft Delete Recovery**

Allow admins to restore deleted invoices:
```php
Route::post('invoices/{id}/restore', [FinancialController::class, 'restoreInvoice']);
```

### **2. Bulk Delete**

Delete multiple draft invoices at once:
```javascript
function deleteBulkInvoices(invoiceIds) {
    // Delete multiple invoices in one go
}
```

### **3. Archive Instead of Delete**

For better audit trail:
```php
$invoice->status = 'archived';
$invoice->save();
```

### **4. Export Deleted Invoices**

Before deletion, auto-export to PDF for records:
```php
// Generate PDF backup before delete
$pdf = $invoice->generatePDF();
Storage::disk('backups')->put("deleted_invoices/{$invoice->invoice_number}.pdf", $pdf);
```

### **5. Approval Workflow**

Require admin approval for deleting certain invoices:
```php
if ($invoice->total_amount > 100000000) {
    // Require manager approval
    DeletionRequest::create([...]);
}
```

---

## ⚠️ Important Notes

### **DO's:**
✅ Always check invoice status before delete  
✅ Verify no payments exist  
✅ Log all delete operations  
✅ Show clear warnings to users  
✅ Use soft delete for invoices  

### **DON'Ts:**
❌ Never delete paid invoices  
❌ Don't allow delete if has payments  
❌ Don't skip validation checks  
❌ Don't forget to cascade delete items/schedules  
❌ Don't delete without confirmation  

---

## 📊 Statistics

**Before Feature:**
- ❌ No delete functionality
- ❌ Draft invoices accumulate
- ❌ No way to clean up mistakes

**After Feature:**
- ✅ Clean delete for draft/cancelled invoices
- ✅ Protected delete for paid invoices
- ✅ Audit trail maintained
- ✅ Better data hygiene

---

## ✅ Completion Checklist

- [x] Backend validation (payment check)
- [x] Backend validation (status check)
- [x] Cascade delete (items, schedules)
- [x] Activity logging
- [x] Error handling
- [x] UI confirmation dialog
- [x] Loading indicator
- [x] Success feedback
- [x] Button visibility logic
- [x] Indonesian messages
- [x] Route exists
- [x] Documentation created
- [x] Cache cleared

**Status**: ✅ **PRODUCTION READY**

---

**Date**: October 3, 2025  
**Feature**: Delete Invoice with Validation  
**Author**: GitHub Copilot  
**Project**: Bizmark.id Financial Management System
