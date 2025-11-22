# 🧪 Testing Guide - Permit Management Restructure

## 📋 Pre-Testing Checklist

### ✅ Environment Preparation
- [x] Code deployed to testing environment
- [x] Database migrations up to date
- [x] Caches cleared (`view:clear`, `route:clear`, `config:clear`)
- [x] No syntax errors in PHP files
- [x] Blade templates compile successfully

---

## 🎯 Test Scenarios

### 1. Basic Navigation Tests

#### Test 1.1: Access Main Permit Management Page
**Steps:**
1. Log in as admin user
2. Look at sidebar navigation
3. Find "Manajemen Perizinan" menu item
4. Click on it

**Expected Results:**
- ✅ Page loads successfully at `/admin/permits`
- ✅ Dashboard tab is active by default
- ✅ URL shows `?tab=dashboard` or no tab parameter
- ✅ Hero section shows 4 summary stat cards
- ✅ Tab navigation displays 4 tabs
- ✅ Dashboard content is visible

#### Test 1.2: Switch Between Tabs
**Steps:**
1. From dashboard tab, click "Permohonan Izin" tab
2. Wait for content to load
3. Check URL
4. Click "Jenis Izin" tab
5. Check URL
6. Click "Verifikasi Pembayaran" tab
7. Check URL
8. Click "Dashboard" tab to return

**Expected Results:**
- ✅ Tab switches instantly without page reload
- ✅ URL updates: `?tab=applications`, `?tab=types`, `?tab=payments`, `?tab=dashboard`
- ✅ Active tab has blue background and white text
- ✅ Content changes for each tab
- ✅ No JavaScript errors in console

#### Test 1.3: Browser Navigation
**Steps:**
1. Click through all 4 tabs sequentially
2. Press browser back button
3. Press browser forward button
4. Bookmark the page while on "Permohonan Izin" tab
5. Navigate away and return using bookmark

**Expected Results:**
- ✅ Back button switches to previous tab
- ✅ Forward button switches to next tab
- ✅ Bookmark opens directly to "Permohonan Izin" tab
- ✅ No page reload during navigation

#### Test 1.4: Direct Tab Access
**Steps:**
1. Navigate to `/admin/permits?tab=applications` directly
2. Navigate to `/admin/permits?tab=types` directly
3. Navigate to `/admin/permits?tab=payments` directly
4. Navigate to `/admin/permits?tab=invalid` (invalid tab)

**Expected Results:**
- ✅ Applications tab loads directly
- ✅ Types tab loads directly
- ✅ Payments tab loads directly
- ✅ Invalid tab falls back to dashboard (or shows error)

---

### 2. Notification Badge Tests

#### Test 2.1: Sidebar Badge
**Steps:**
1. Check current badge number on "Manajemen Perizinan" menu
2. Create a new permit application with status "submitted"
3. Refresh sidebar (or reload page)
4. Check badge number

**Expected Results:**
- ✅ Badge shows total of: submitted + under_review applications + unread notes + pending payments
- ✅ Badge increases by 1 after new application
- ✅ Badge uses correct colors (red when inactive, white when active)

#### Test 2.2: Applications Tab Badge
**Steps:**
1. Navigate to permit management page
2. Check badge on "Permohonan Izin" tab
3. Note the count
4. Go to applications and change one from "submitted" to "completed"
5. Return to permit management page

**Expected Results:**
- ✅ Badge shows count of submitted + under_review + unread client notes
- ✅ Badge decreases when application status changes
- ✅ Badge hidden when count is 0

#### Test 2.3: Payments Tab Badge
**Steps:**
1. Navigate to permit management page
2. Check badge on "Verifikasi Pembayaran" tab
3. Note the count
4. Verify one manual payment
5. Return to permit management page

**Expected Results:**
- ✅ Badge shows count of manual payments with status "processing"
- ✅ Badge decreases by 1 after verification
- ✅ Badge hidden when count is 0

---

### 3. Dashboard Tab Tests

#### Test 3.1: Focus Cards
**Steps:**
1. Navigate to dashboard tab
2. Check three focus cards

**Expected Results:**
- ✅ "Antrian Tinjauan" shows count of pending applications
- ✅ "Jalur Penawaran" shows count needing quotation
- ✅ "Pantauan Keuangan" shows count of pending payments
- ✅ All numbers are accurate

#### Test 3.2: Status Distribution
**Steps:**
1. Check status distribution section
2. Verify progress bars
3. Compare with actual application counts

**Expected Results:**
- ✅ Shows breakdown by status (Submitted, Under Review, Quoted, etc.)
- ✅ Progress bars show correct percentages
- ✅ Colors match status badges throughout app

#### Test 3.3: Recent Applications
**Steps:**
1. Check recent applications table
2. Verify it shows latest 10 applications
3. Click "Lihat Detail" on one application

**Expected Results:**
- ✅ Table shows 10 most recent applications
- ✅ Data is accurate (number, client, type, status, date)
- ✅ "Lihat Detail" opens application detail page
- ✅ Status badges have correct colors

---

### 4. Applications Tab Tests

#### Test 4.1: Applications List
**Steps:**
1. Navigate to "Permohonan Izin" tab
2. Verify table displays applications
3. Check all columns are present

**Expected Results:**
- ✅ Table shows: Nomor, Klien, Jenis Izin, Status, Tanggal, Aksi
- ✅ Data loads correctly
- ✅ Pagination shows if more than 20 applications
- ✅ Empty state shows if no applications

#### Test 4.2: Search Functionality
**Steps:**
1. Enter application number in search box
2. Wait for auto-submit (or click filter)
3. Verify results
4. Clear search
5. Enter client name and search

**Expected Results:**
- ✅ Form auto-submits after typing (with debounce)
- ✅ Results filtered by application number
- ✅ Clear button resets search
- ✅ Results filtered by client name
- ✅ Tab parameter preserved in URL

#### Test 4.3: Status Filter
**Steps:**
1. Select "Submitted" from status dropdown
2. Verify results
3. Select "Under Review"
4. Verify results
5. Select "Semua Status" to reset

**Expected Results:**
- ✅ Dropdown auto-submits on change
- ✅ Shows only submitted applications
- ✅ Shows only under_review applications
- ✅ Shows all when reset
- ✅ Tab maintained in URL

#### Test 4.4: Permit Type Filter
**Steps:**
1. Select a specific permit type from dropdown
2. Verify results
3. Select "Semua Jenis"

**Expected Results:**
- ✅ Shows only applications of selected type
- ✅ All applications shown when reset
- ✅ Filter works with search and status filters

#### Test 4.5: Combined Filters
**Steps:**
1. Enter search term
2. Select status
3. Select permit type
4. Click filter button
5. Check URL parameters

**Expected Results:**
- ✅ All filters applied simultaneously
- ✅ URL contains: `?tab=applications&search=...&status=...&permit_type=...`
- ✅ Results accurate
- ✅ Clear button resets all filters

#### Test 4.6: Pagination
**Steps:**
1. If pagination available, click page 2
2. Check URL
3. Click back to page 1

**Expected Results:**
- ✅ Shows next 20 applications
- ✅ URL includes: `?tab=applications&page=2`
- ✅ Tab state maintained
- ✅ Filters maintained across pages

#### Test 4.7: View Application Detail
**Steps:**
1. Click "Lihat" button on any application
2. View details
3. Click browser back button

**Expected Results:**
- ✅ Opens application detail page
- ✅ Shows full application information
- ✅ Back button returns to applications tab
- ✅ Filters and page number preserved

---

### 5. Types Tab Tests

#### Test 5.1: Stat Cards
**Steps:**
1. Navigate to "Jenis Izin" tab
2. Check four stat cards

**Expected Results:**
- ✅ "Total Jenis" shows count of all permit types
- ✅ "Aktif" shows count of active types (green)
- ✅ "Total Permohonan" shows count of applications
- ✅ "Harga Rata-rata" shows average price in rupiah

#### Test 5.2: Types Table
**Steps:**
1. Verify table displays all permit types
2. Check columns

**Expected Results:**
- ✅ Table shows: Nama, Harga Dasar, Permohonan, Status, Aksi
- ✅ Prices formatted correctly (Rp 1.000.000)
- ✅ Application count accurate
- ✅ Status badges show "Aktif" (green) or "Nonaktif" (gray)

#### Test 5.3: Search Types
**Steps:**
1. Enter permit type name in search
2. Wait for auto-submit
3. Verify results
4. Clear search

**Expected Results:**
- ✅ Results filtered by name
- ✅ Partial matches work
- ✅ Clear button resets
- ✅ Tab maintained

#### Test 5.4: Active/Inactive Filter
**Steps:**
1. Select "Aktif" from dropdown
2. Verify only active types shown
3. Select "Nonaktif"
4. Verify only inactive types shown
5. Select "Semua" to reset

**Expected Results:**
- ✅ Filter works correctly
- ✅ Auto-submits on change
- ✅ Tab maintained

#### Test 5.5: View Type Detail
**Steps:**
1. Click "Lihat" (eye icon) on a permit type
2. View details
3. Return using back button

**Expected Results:**
- ✅ Opens permit type detail page
- ✅ Shows full type information
- ✅ Back returns to types tab

#### Test 5.6: Edit Type
**Steps:**
1. Click "Edit" (pencil icon) on a permit type
2. Make changes
3. Save or cancel

**Expected Results:**
- ✅ Opens edit form
- ✅ Changes save successfully
- ✅ Returns to types tab (or edit was in modal/same tab)

#### Test 5.7: Create New Type
**Steps:**
1. Click "Tambah Jenis Izin" button
2. Fill form
3. Submit

**Expected Results:**
- ✅ Opens create form
- ✅ New type created successfully
- ✅ Returns to types tab with new type visible

#### Test 5.8: Types Pagination
**Steps:**
1. If available, test pagination
2. Navigate pages
3. Check tab state

**Expected Results:**
- ✅ Pagination works
- ✅ Tab maintained across pages
- ✅ Filters maintained

---

### 6. Payments Tab Tests

#### Test 6.1: Stat Cards
**Steps:**
1. Navigate to "Verifikasi Pembayaran" tab
2. Check four stat cards

**Expected Results:**
- ✅ "Total Pembayaran" shows all payment count
- ✅ "Pending" shows processing payments (orange)
- ✅ "Terverifikasi" shows verified count (green)
- ✅ "Total Nilai" shows sum in millions (Rp X.XM)

#### Test 6.2: Payments Table
**Steps:**
1. Verify table displays payments
2. Check all columns

**Expected Results:**
- ✅ Table shows: Referensi, Permohonan, Klien, Jumlah, Status, Tanggal, Aksi
- ✅ Reference numbers correct
- ✅ Application numbers linked
- ✅ Amounts formatted properly
- ✅ Status badges colored: orange (processing), green (verified), red (failed)

#### Test 6.3: Search Payments
**Steps:**
1. Enter payment reference in search
2. Verify results
3. Clear and search by application number

**Expected Results:**
- ✅ Filters by reference
- ✅ Filters by application number
- ✅ Tab maintained

#### Test 6.4: Status Filter
**Steps:**
1. Select "Sedang Diproses"
2. Verify results
3. Select "Terverifikasi"
4. Select "Gagal"
5. Reset to "Semua Status"

**Expected Results:**
- ✅ Each filter works correctly
- ✅ Auto-submits
- ✅ Tab maintained

#### Test 6.5: Payment Method Filter
**Steps:**
1. Select "Transfer Manual"
2. Verify results
3. Select "Midtrans"
4. Reset

**Expected Results:**
- ✅ Filters by method
- ✅ Accurate results
- ✅ Tab maintained

#### Test 6.6: Combined Payment Filters
**Steps:**
1. Apply search + status + method filters
2. Verify results

**Expected Results:**
- ✅ All filters work together
- ✅ URL parameters correct
- ✅ Accurate results

#### Test 6.7: View Application
**Steps:**
1. Click eye icon (Lihat Permohonan)
2. View application detail
3. Return

**Expected Results:**
- ✅ Opens related application
- ✅ Correct application shown
- ✅ Back returns to payments tab

#### Test 6.8: View Payment Proof
**Steps:**
1. Click image icon (Lihat Bukti) on payment with proof
2. View image

**Expected Results:**
- ✅ Opens proof image in new tab
- ✅ Image displays correctly
- ✅ Original tab maintains state

#### Test 6.9: Payments Pagination
**Steps:**
1. Navigate pages if available
2. Check state preservation

**Expected Results:**
- ✅ Pagination works
- ✅ Tab maintained
- ✅ Filters maintained

---

### 7. Backward Compatibility Tests

#### Test 7.1: Old Dashboard Route
**Steps:**
1. Navigate to `/admin/permit-dashboard`
2. Check redirect

**Expected Results:**
- ✅ Redirects to `/admin/permits?tab=dashboard`
- ✅ Dashboard tab is active
- ✅ Content displays correctly

#### Test 7.2: Old Applications Route
**Steps:**
1. Navigate to `/admin/permit-applications`
2. Check if it still works OR redirects

**Expected Results:**
- ✅ Either works as before (old page)
- ✅ OR redirects to `/admin/permits?tab=applications`
- ✅ Applications list displays

#### Test 7.3: Application Detail Links
**Steps:**
1. Find links to `/admin/permit-applications/123` throughout app
2. Click them
3. Verify they work

**Expected Results:**
- ✅ Detail pages still accessible
- ✅ All data displays correctly
- ✅ Actions (approve, reject, etc.) still work

#### Test 7.4: Types Routes
**Steps:**
1. Navigate to `/permit-types`
2. Check if old route works

**Expected Results:**
- ✅ Either old page works
- ✅ OR redirects to tabbed interface
- ✅ Types list accessible

#### Test 7.5: Payments Routes
**Steps:**
1. Navigate to `/admin/payments`
2. Check functionality

**Expected Results:**
- ✅ Either old page works
- ✅ OR redirects to tabbed interface
- ✅ Payments accessible

---

### 8. Mobile Responsive Tests

#### Test 8.1: Tab Navigation on Mobile
**Steps:**
1. Open on mobile device (or resize browser < 768px)
2. Check tab navigation
3. Scroll tabs horizontally

**Expected Results:**
- ✅ Tab buttons scroll horizontally
- ✅ `overflow-x-auto` works
- ✅ No layout breaks
- ✅ Tabs remain clickable

#### Test 8.2: Tables on Mobile
**Steps:**
1. Open each tab on mobile
2. Check table layouts

**Expected Results:**
- ✅ Tables scroll horizontally
- ✅ Data readable
- ✅ Action buttons accessible
- ✅ No content cut off

#### Test 8.3: Stat Cards on Mobile
**Steps:**
1. Check stat cards on each tab

**Expected Results:**
- ✅ Cards stack vertically
- ✅ Proper spacing
- ✅ Text remains readable
- ✅ No overflow

#### Test 8.4: Filters on Mobile
**Steps:**
1. Test filter forms on mobile
2. Check dropdowns and inputs

**Expected Results:**
- ✅ Forms usable on touch devices
- ✅ Dropdowns open correctly
- ✅ Inputs accessible
- ✅ Buttons tappable

---

### 9. Performance Tests

#### Test 9.1: Page Load Time
**Steps:**
1. Open browser DevTools (Network tab)
2. Navigate to permit management
3. Record load time
4. Switch between tabs

**Expected Results:**
- ✅ Initial page load < 2 seconds
- ✅ Tab switches instant (< 100ms)
- ✅ No unnecessary AJAX calls
- ✅ Assets cached properly

#### Test 9.2: Database Queries
**Steps:**
1. Enable query logging or use debugbar
2. Load each tab
3. Count queries

**Expected Results:**
- ✅ Dashboard: < 15 queries
- ✅ Applications: < 10 queries (+ pagination)
- ✅ Types: < 8 queries (+ pagination)
- ✅ Payments: < 10 queries (+ pagination)
- ✅ No N+1 query problems

#### Test 9.3: Large Dataset Performance
**Steps:**
1. Test with 1000+ applications
2. Check pagination
3. Test filters

**Expected Results:**
- ✅ Pagination handles large dataset
- ✅ Filters remain fast
- ✅ No timeout errors
- ✅ Memory usage reasonable

---

### 10. Edge Case Tests

#### Test 10.1: Empty States
**Steps:**
1. Test each tab with no data
2. Delete all applications, types, or payments

**Expected Results:**
- ✅ Dashboard shows zero counts
- ✅ Applications tab shows empty state message
- ✅ Types tab shows "Tambah Jenis Izin Pertama" CTA
- ✅ Payments tab shows empty message

#### Test 10.2: Single Record
**Steps:**
1. Test with only one application, type, payment

**Expected Results:**
- ✅ Dashboard calculates percentages correctly
- ✅ Tables display single row
- ✅ No pagination shown
- ✅ Filters work

#### Test 10.3: Invalid Tab Parameter
**Steps:**
1. Navigate to `/admin/permits?tab=invalid`
2. Navigate to `/admin/permits?tab=`

**Expected Results:**
- ✅ Falls back to dashboard
- ✅ OR shows 404/error
- ✅ No fatal errors

#### Test 10.4: Missing Permissions
**Steps:**
1. Test with user having limited permissions
2. Check access

**Expected Results:**
- ✅ Permission checks enforced
- ✅ Unauthorized actions blocked
- ✅ Appropriate error messages

#### Test 10.5: Concurrent Filters
**Steps:**
1. Apply multiple filters quickly
2. Clear filters rapidly
3. Paginate while filtering

**Expected Results:**
- ✅ No race conditions
- ✅ Correct results displayed
- ✅ No errors

---

## 🐛 Bug Reporting Template

If you encounter issues, report using this template:

```markdown
### Bug Report

**Tab:** [Dashboard | Applications | Types | Payments]
**Issue:** [Brief description]

**Steps to Reproduce:**
1. 
2. 
3. 

**Expected Behavior:**


**Actual Behavior:**


**Screenshots:**


**Environment:**
- Browser: 
- Device: 
- User Role: 

**Additional Context:**

```

---

## ✅ Test Summary Template

After completing tests, fill this out:

```markdown
### Test Execution Summary

**Date:** {{ date }}
**Tester:** {{ name }}
**Environment:** [Development | Staging | Production]

#### Test Results
- Total Scenarios: 60
- Passed: ___
- Failed: ___
- Skipped: ___

#### Critical Issues Found:
1. 
2. 

#### Minor Issues Found:
1. 
2. 

#### Notes:


#### Sign-Off:
- [ ] All critical tests passed
- [ ] Performance acceptable
- [ ] Mobile responsive
- [ ] Ready for production

**Approved By:** _______________
**Date:** _______________
```

---

## 🎯 Testing Priority

### High Priority (Must Pass)
- Basic navigation between tabs
- Notification badges accuracy
- Search and filter functionality
- Backward compatibility with old routes
- Application detail access

### Medium Priority (Should Pass)
- Performance metrics
- Mobile responsiveness
- Pagination
- Empty states
- Combined filters

### Low Priority (Nice to Have)
- Edge cases
- Concurrent operations
- Large dataset handling

---

**Note:** This comprehensive testing guide covers all aspects of the permit management restructure. Execute tests systematically and document any issues found.
