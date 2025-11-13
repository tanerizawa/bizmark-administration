# 🧪 BANK RECONCILIATION - TESTING GUIDE

**Date:** October 10, 2025  
**Purpose:** Test complete bank reconciliation workflow  
**Status:** Ready to test

---

## 📋 PRE-TEST CHECKLIST

### Prerequisites
- ✅ Migrations run (3 tables created)
- ✅ Models created (BankReconciliation, BankStatementEntry)
- ✅ Controller created (BankReconciliationController)
- ✅ Routes registered (12 routes)
- ✅ Views created (4 views)
- ✅ Navigation menu added
- ✅ Sample CSV created

### Required Data
Before testing, ensure you have:
- [ ] At least 1 active cash account (bank account)
- [ ] Some project payments in the period (Oct 1-20, 2025)
- [ ] Some project expenses in the period (Oct 1-20, 2025)
- [ ] Sample CSV file ready

---

## 🚀 TEST SCENARIO 1: COMPLETE WORKFLOW

### Step 1: Access Reconciliation Page
**Action:**
1. Login to Bizmark system
2. Look at left sidebar
3. Find "Rekonsiliasi Bank" menu (below "Akun Kas")
4. Click it

**Expected Result:**
- ✅ Page loads successfully
- ✅ URL: `/reconciliations`
- ✅ Shows dashboard with 4 statistics cards
- ✅ Shows "Mulai Rekonsiliasi Baru" button
- ✅ Empty state message if no reconciliations exist

**Screenshot Points:**
- Dashboard view
- Statistics cards
- Empty state

---

### Step 2: Start New Reconciliation
**Action:**
1. Click "Mulai Rekonsiliasi Baru" button
2. Fill out the form:
   - **Akun Kas:** Select your bank account (e.g., "Bank BTN")
   - **Tanggal Mulai:** 2025-10-01
   - **Tanggal Selesai:** 2025-10-20
   - **Saldo Awal Bank:** 50000000 (50 million)
   - **Saldo Akhir Bank:** 83943500 (83.9 million)
   - **Upload Bank Statement:** Drag & drop or select CSV file
     - File location: `storage/app/sample_bank_statement_october.csv`
3. Click "Mulai Rekonsiliasi" button

**Expected Result:**
- ✅ Form validation passes
- ✅ CSV file uploads successfully
- ✅ File size check passes (<5MB)
- ✅ File format validated (CSV)
- ✅ Redirect to matching page
- ✅ URL: `/reconciliations/{id}/match`
- ✅ Success message shown
- ✅ Bank entries imported (10 entries from CSV)

**Troubleshooting:**
- **If CSV upload fails:** Check file format, ensure header row exists
- **If validation fails:** Check date range (end_date must be >= start_date)
- **If import fails:** Check CSV column order matches expected format

---

### Step 3: View Matching Interface
**Action:**
1. Observe the matching interface
2. Check left panel (System Transactions)
3. Check right panel (Bank Statement Entries)
4. Check statistics cards at top

**Expected Result:**
- ✅ Page loads with split-screen layout
- ✅ Statistics show:
  - Total Transaksi Bank: 10 (or 9 excluding saldo awal)
  - Sudah Cocok: 0
  - Belum Cocok: 10
  - Match Rate: 0%
- ✅ Left panel shows unreconciled system transactions
  - Grouped by type (Income/Expense)
  - Color-coded (green/red)
- ✅ Right panel shows bank entries
  - All have "Match" button
  - None have "Matched" badge yet
- ✅ "Auto-Match" button visible at top
- ✅ "Selesaikan Rekonsiliasi" button hidden (unmatched > 0)

**Screenshot Points:**
- Full matching interface
- Left panel (system transactions)
- Right panel (bank entries)
- Statistics cards

---

### Step 4: Auto-Match Transactions
**Action:**
1. Click "Auto-Match" button at top
2. Wait for processing

**Expected Result:**
- ✅ Page reloads/refreshes
- ✅ Success message shown (e.g., "Berhasil mencocokkan X transaksi otomatis")
- ✅ Statistics updated:
  - Sudah Cocok: increased
  - Belum Cocok: decreased
  - Match Rate: calculated percentage
- ✅ Matched entries in right panel show:
  - Green background tint
  - "Matched" badge
  - "Unmatch" button
- ✅ System transactions that were matched disappear from left panel

**What Auto-Match Does:**
- Finds transactions with exact date AND exact amount match
- Links bank entry with system transaction
- Marks both as reconciled
- Updates reconciliation_id in both records

**Note:** Auto-match might not find all matches if:
- Dates don't match exactly
- Amounts differ slightly
- Transaction types don't match
- Transactions already reconciled

---

### Step 5: Manual Match Remaining Transactions
**Action:**
1. Find unmatched bank entry in right panel
2. Click "Match" button on that entry
3. Modal opens
4. Fill modal form:
   - **Transaction Type:** Select (Payment/Expense/Invoice Payment)
   - **Transaction ID:** Select from dropdown
   - **Notes:** (Optional) "Matched manually - date difference"
5. Click "Match" button in modal

**Expected Result:**
- ✅ Modal opens correctly
- ✅ Bank entry info displayed in modal header
- ✅ Transaction type dropdown populated
- ✅ Transaction ID dropdown updates based on type selection
- ✅ Submit successful
- ✅ Modal closes
- ✅ Success message shown
- ✅ Bank entry now shows "Matched" badge
- ✅ Statistics updated
- ✅ Matched transaction removed from left panel

**Repeat for all remaining unmatched entries**

**Modal Features:**
- Shows bank entry details (date, amount)
- Dynamic transaction list based on type
- Notes field for explanation
- Cancel button works (closes modal)
- Click outside modal closes it

---

### Step 6: Review Outstanding Items
**Action:**
1. After matching, observe if any bank entries remain unmatched
2. For unmatched items, they could be:
   - Bank fees not recorded in system
   - Interest earned not recorded
   - Timing differences (check not yet cleared)
   - Errors in bank statement or system

**Expected Result:**
- ✅ Unmatched items clearly visible (no "Matched" badge)
- ✅ "Match" button still available
- ✅ Can choose to:
  - Match with existing transaction
  - Leave as outstanding (will show in report)
  - Unmatch if matched incorrectly

---

### Step 7: Complete Reconciliation
**Action:**
1. Once all critical transactions matched (or explained)
2. Click "Selesaikan Rekonsiliasi" button
3. Confirm in dialog

**Expected Result:**
- ✅ Button becomes visible when ready
- ✅ Confirmation dialog appears
- ✅ After confirm, processes completion
- ✅ Calculates:
  - Deposits in transit (unmatched income)
  - Outstanding checks (unmatched expenses)
  - Final adjusted balances
  - Difference between bank & book
- ✅ Status changes to "completed"
- ✅ Redirects to report page
- ✅ Success message shown

---

### Step 8: View Reconciliation Report
**Action:**
1. Observe the reconciliation report
2. Scroll through all sections
3. Check calculations

**Expected Result:**
- ✅ Professional report layout displayed
- ✅ **Section 1: Bank Balance Reconciliation**
  - Closing balance bank: Rp 83,943,500
  - + Deposits in transit: (calculated)
  - - Outstanding checks: (calculated)
  - = Adjusted bank balance: (calculated)
- ✅ **Section 2: Book Balance Reconciliation**
  - Closing balance book: (from system)
  - + Bank credits: (calculated)
  - - Bank charges: (calculated)
  - = Adjusted book balance: (calculated)
- ✅ **Section 3: Difference**
  - Shows difference amount
  - ✅ Green checkmark if = 0 (balanced)
  - ⚠️ Red warning if ≠ 0 (needs investigation)
- ✅ **Section 4: Matching Statistics**
  - Total, Matched, Unmatched counts
  - Match rate percentage
- ✅ **Section 5: Matched Transactions Table**
  - Lists all matched entries
  - Shows date, description, debit, credit, confidence
- ✅ **Section 6: Outstanding Items** (if any)
  - Lists unmatched entries
  - Shows reason/status
- ✅ Signature section shows:
  - Reconciled by: Your name
  - Date: Current date
- ✅ Print button works (opens print dialog)

**Screenshot Points:**
- Full report
- Each section
- Difference analysis (especially if balanced)

---

### Step 9: Test Print Functionality
**Action:**
1. Click "Print" button
2. Browser print dialog opens
3. Check print preview

**Expected Result:**
- ✅ Print dialog opens
- ✅ Print preview looks professional
- ✅ No background colors/gradients (print-friendly)
- ✅ Buttons hidden in print view
- ✅ All content visible
- ✅ Proper page breaks (if multiple pages)

---

### Step 10: Return to List & Verify
**Action:**
1. Click "Kembali ke Daftar" button
2. View reconciliations list

**Expected Result:**
- ✅ Returns to index page
- ✅ New reconciliation visible in table
- ✅ Shows correct data:
  - Date: Oct 10, 2025
  - Account: Selected bank account
  - Period: Oct 1-20, 2025
  - Saldo Buku: (calculated)
  - Saldo Bank: Rp 83,943,500
  - Selisih: (calculated)
  - Status: "Selesai" badge (green)
- ✅ Action buttons:
  - View Report icon (file-alt)
  - No delete button (completed can't be deleted)

---

## 🧪 TEST SCENARIO 2: UNMATCH TRANSACTION

### Step 1: View Completed Reconciliation
**Action:**
1. From list, click on completed reconciliation
2. You're on report page
3. Note: Can't unmatch from report page

**Alternative:**
1. Create another reconciliation (don't complete it)
2. Match some transactions
3. Go to match interface

### Step 2: Unmatch Transaction
**Action:**
1. On match page, find matched bank entry
2. Click "Unmatch" button (below the entry)
3. Confirm in dialog

**Expected Result:**
- ✅ Confirmation dialog appears
- ✅ After confirm:
  - Bank entry "Matched" badge removed
  - "Match" button reappears
  - Transaction appears back in left panel
  - System transaction `is_reconciled` = false
  - Statistics updated
- ✅ Success message shown

---

## 🧪 TEST SCENARIO 3: DELETE RECONCILIATION

### Step 1: Delete In-Progress Reconciliation
**Action:**
1. Create new reconciliation but don't complete it
2. From list, click delete icon (trash)
3. Confirm deletion

**Expected Result:**
- ✅ Confirmation dialog appears
- ✅ After confirm:
  - Reconciliation deleted from database
  - Bank statement file deleted from storage
  - All bank_statement_entries deleted (cascade)
  - Redirects to list
- ✅ Success message shown

### Step 2: Try Delete Completed Reconciliation
**Action:**
1. Try to delete completed reconciliation
2. Click delete icon

**Expected Result:**
- ✅ Error message: "Hanya rekonsiliasi dengan status 'in progress' yang bisa dihapus"
- ✅ Reconciliation NOT deleted
- ✅ Stays on list page

---

## 🧪 TEST SCENARIO 4: FILTERS

### Step 1: Filter by Account
**Action:**
1. On index page, select different account from dropdown
2. Click filter button (or auto-submit)

**Expected Result:**
- ✅ List updates to show only that account's reconciliations
- ✅ URL has query parameter: `?cash_account_id=X`
- ✅ Statistics cards update

### Step 2: Filter by Status
**Action:**
1. Select "Selesai" from status dropdown
2. Submit

**Expected Result:**
- ✅ Shows only completed reconciliations
- ✅ URL has query parameter: `?status=completed`

### Step 3: Filter by Date Range
**Action:**
1. Enter start date: 2025-10-01
2. Enter end date: 2025-10-31
3. Submit

**Expected Result:**
- ✅ Shows reconciliations within date range
- ✅ URL has query parameters

### Step 4: Clear Filters
**Action:**
1. Click X button (clear filters)

**Expected Result:**
- ✅ All filters reset
- ✅ Shows all reconciliations
- ✅ URL clean (no query parameters)

---

## 🧪 TEST SCENARIO 5: EDGE CASES

### Test 1: Upload Invalid File
**Action:**
1. Try to upload .txt file
2. Try to upload .pdf file
3. Try to upload 10MB file

**Expected Result:**
- ✅ Validation error for wrong format
- ✅ Error message shown
- ✅ File not uploaded

### Test 2: Upload CSV with Wrong Columns
**Action:**
1. Create CSV with missing columns
2. Upload it

**Expected Result:**
- ✅ Import fails gracefully
- ✅ Error message shown
- ✅ Or partial import with warnings

### Test 3: Period with No Transactions
**Action:**
1. Create reconciliation for period with no system transactions
2. Upload bank statement

**Expected Result:**
- ✅ Works correctly
- ✅ Left panel shows "no transactions"
- ✅ All bank entries unmatched
- ✅ Can still complete (will show all as outstanding)

### Test 4: Duplicate Reconciliation
**Action:**
1. Create reconciliation for Oct 1-20
2. Try to create another for same period & account

**Expected Result:**
- ✅ Should work (no constraint preventing it)
- ✅ But might want to add warning in future

---

## 📊 EXPECTED RESULTS SUMMARY

### Database Records Created:
- ✅ 1 record in `bank_reconciliations` table
- ✅ 9-10 records in `bank_statement_entries` table (excluding saldo awal)
- ✅ Updated `is_reconciled` in `project_payments` table
- ✅ Updated `is_reconciled` in `project_expenses` table

### Files Created:
- ✅ 1 CSV file in `storage/app/public/bank-statements/` folder

### Status Workflow:
1. ✅ **in_progress** - When created, during matching
2. ✅ **completed** - After clicking "Selesaikan Rekonsiliasi"
3. ✅ **reviewed** - (Future: when manager reviews)
4. ✅ **approved** - (Future: when approved)

---

## 🐛 COMMON ISSUES & SOLUTIONS

### Issue 1: Routes Not Found (404)
**Symptoms:** Clicking menu shows 404  
**Solution:**
```bash
docker compose exec app php artisan route:clear
docker compose exec app php artisan route:cache
```

### Issue 2: View Not Found
**Symptoms:** Error "View [reconciliations.index] not found"  
**Solution:** Check if all 4 view files exist in `resources/views/reconciliations/`

### Issue 3: CSV Import Fails
**Symptoms:** No bank entries imported  
**Solution:**
- Check CSV format (header row required)
- Check column order matches expected
- Check date format (YYYY-MM-DD preferred)
- Check for encoding issues (UTF-8 recommended)

### Issue 4: Auto-Match Finds Nothing
**Symptoms:** Auto-match completes but matches 0 transactions  
**Solution:**
- Check if system transactions exist in the period
- Check if dates match exactly
- Check if amounts match exactly
- Dates in CSV vs database might have timezone issues

### Issue 5: Statistics Don't Update
**Symptoms:** Match rate stays at 0%  
**Solution:** Refresh page (F5)

### Issue 6: Can't Complete Reconciliation
**Symptoms:** Button doesn't appear  
**Solution:**
- Must have unmatched = 0, OR
- Button appears when ready (check controller logic)
- Actually button should appear always, will calculate outstanding

---

## ✅ SUCCESS CRITERIA

### All Tests Pass If:
- ✅ Can create reconciliation
- ✅ CSV imports successfully
- ✅ Can view matching interface
- ✅ Auto-match works (finds at least some matches)
- ✅ Manual match works via modal
- ✅ Can unmatch transactions
- ✅ Can complete reconciliation
- ✅ Report generates correctly
- ✅ Calculations are accurate
- ✅ Can print report
- ✅ Can filter list
- ✅ Can delete in-progress
- ✅ Cannot delete completed
- ✅ Navigation menu works
- ✅ No console errors
- ✅ No 500 errors
- ✅ All buttons functional
- ✅ All forms validate
- ✅ All modals work

---

## 📸 SCREENSHOTS TO CAPTURE

1. **Dashboard** - Empty state
2. **Dashboard** - With reconciliations
3. **Create Form** - Empty
4. **Create Form** - File uploaded
5. **Match Interface** - Initial state
6. **Match Interface** - After auto-match
7. **Match Interface** - Manual match modal
8. **Match Interface** - All matched
9. **Report** - Section 1-2 (balances)
10. **Report** - Section 3 (difference = 0)
11. **Report** - Section 4-5 (statistics & transactions)
12. **Report** - Signatures
13. **Print Preview** - Report

---

## 🎯 NEXT STEPS AFTER TESTING

### If All Tests Pass:
1. ✅ Mark feature as Production Ready
2. ✅ Create user documentation
3. ✅ Record demo video
4. ✅ Train finance team
5. ✅ Deploy to production
6. ✅ Monitor for issues

### If Issues Found:
1. Document all bugs/issues
2. Prioritize by severity
3. Fix critical bugs first
4. Re-test after fixes
5. Iterate until stable

---

## 📞 TESTING SUPPORT

### Need Help?
- Check Laravel logs: `storage/logs/laravel.log`
- Check browser console (F12)
- Check network tab for API errors
- Review this guide again
- Check database records directly

### Report Issues:
Document each issue with:
- Steps to reproduce
- Expected result
- Actual result
- Error messages (if any)
- Screenshots
- Browser/device info

---

**Happy Testing!** 🧪🚀

Remember: This is MVP (Minimum Viable Product). Not everything will be perfect, but it should work end-to-end!

---

**Created:** October 10, 2025  
**Version:** 1.0  
**Status:** Ready for Testing
