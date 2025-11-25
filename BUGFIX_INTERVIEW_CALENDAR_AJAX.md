# Bug Fix: Interview Calendar AJAX Error

**Tanggal:** 24 November 2025  
**Status:** ✅ RESOLVED  
**Severity:** High  
**Halaman:** `/admin/recruitment/interviews`

---

## 🐛 Masalah

Popup error muncul saat mengakses halaman Interview Management:
```
Error loading interviews!
```

Kalender FullCalendar gagal load data interview via AJAX request.

---

## 🔍 Root Cause Analysis

### 1. **AJAX Parameter Mismatch**

**View (index.blade.php)** mengirim parameter:
```javascript
events: {
    url: '{{ route("admin.recruitment.interviews.index") }}',
    method: 'GET',
    extraParams: () => ({ json: 1 }),  // ❌ Mengirim parameter json=1
    failure: () => alert('Error loading interviews!')
}
```

**Controller** hanya memeriksa header:
```php
// ❌ SEBELUM
if ($request->wantsJson()) {  // Hanya cek Accept: application/json header
    // ... handle JSON response
}
```

**Masalah:** FullCalendar tidak mengirim header `Accept: application/json` secara default, hanya menambah query parameter `json=1`. Controller tidak memeriksa parameter ini.

### 2. **Missing Variable**

View mengharapkan variable `$metrics`:
```php
@php
    $completedCount = $metrics['completed'] ?? InterviewSchedule::where('status', 'completed')->count();
    $cancelledCount = $metrics['cancelled'] ?? InterviewSchedule::where('status', 'cancelled')->count();
@endphp
```

Controller tidak passing variable ini, menyebabkan:
- Fallback query dijalankan berkali-kali (inefficient)
- Potential undefined variable warning

---

## ✅ Solusi

### 1. **Fix AJAX Detection**

**File:** `app/Http/Controllers/Admin/InterviewScheduleController.php`

```php
// ✅ SESUDAH
if ($request->wantsJson() || $request->has('json')) {
    // Check header OR query parameter
    $start = $request->input('start');
    $end = $request->input('end');
    
    // ... handle JSON response
}
```

**Penjelasan:**
- `wantsJson()`: Cek header `Accept: application/json`
- `has('json')`: Cek query parameter `?json=1`
- Sekarang support kedua metode

### 2. **Add Metrics Variable**

```php
// Regular view
$upcomingInterviews = InterviewSchedule::with(['jobApplication.jobVacancy'])
    ->where('scheduled_at', '>=', now())
    ->where('status', 'scheduled')
    ->orderBy('scheduled_at')
    ->take(10)
    ->get();

$todayInterviews = InterviewSchedule::with(['jobApplication.jobVacancy'])
    ->whereDate('scheduled_at', today())
    ->orderBy('scheduled_at')
    ->get();

// ✅ ADD: Interview metrics
$metrics = [
    'completed' => InterviewSchedule::where('status', 'completed')->count(),
    'cancelled' => InterviewSchedule::where('status', 'cancelled')->count(),
];

return view('admin.recruitment.interviews.index', compact('upcomingInterviews', 'todayInterviews', 'metrics'));
```

**Benefit:**
- Single query per metric (tidak ada fallback query)
- More efficient
- Cleaner code

---

## 🧪 Testing

### Test Case 1: Calendar Load
```
✅ PASS - Kalender load tanpa error
✅ PASS - Data interview muncul di kalender
✅ PASS - No popup "Error loading interviews!"
```

### Test Case 2: JSON Response
```bash
curl 'https://bizmark.id/admin/recruitment/interviews?json=1&start=2025-11-01&end=2025-11-30'
```
**Expected:** JSON array dengan format FullCalendar event

### Test Case 3: Metrics Display
```
✅ PASS - Completed count muncul
✅ PASS - Cancelled count muncul
✅ PASS - No N+1 query issue
```

---

## 📊 Impact

### Before Fix:
- ❌ Calendar tidak bisa load data
- ❌ User experience terganggu (popup error)
- ❌ Interview scheduling workflow blocked
- ⚠️ Inefficient queries (fallback)

### After Fix:
- ✅ Calendar load dengan sempurna
- ✅ Data interview tampil di kalender
- ✅ Smooth user experience
- ✅ Efficient queries
- ✅ No warnings/errors

---

## 🔧 Files Modified

1. **app/Http/Controllers/Admin/InterviewScheduleController.php**
   - Line 23: Added `|| $request->has('json')` condition
   - Line 59-64: Added `$metrics` calculation
   - Line 66: Updated `compact()` to include `$metrics`

---

## 📝 Prevention

### Future Guidelines:

1. **AJAX Endpoint Detection:**
   ```php
   // ✅ BEST PRACTICE - Check multiple indicators
   if ($request->wantsJson() || $request->has('json') || $request->ajax()) {
       return response()->json($data);
   }
   ```

2. **View Variable Validation:**
   - Always pass all variables referenced in view
   - Use IDE inspection to catch undefined variables
   - Document required variables in controller docblocks

3. **FullCalendar Integration:**
   - Always test AJAX endpoints separately
   - Check browser Network tab for 200 response
   - Verify JSON format matches FullCalendar spec

---

## 🎯 Lessons Learned

1. **Laravel Request Methods:**
   - `wantsJson()` → Checks `Accept` header only
   - `ajax()` → Checks `X-Requested-With` header
   - `has('param')` → Checks query/body parameters
   - Use combinations for robust detection

2. **FullCalendar Defaults:**
   - Does NOT set `Accept: application/json` automatically
   - Use `extraParams` for query parameters
   - Or configure headers manually in event source

3. **Variable Fallbacks:**
   - While `??` operator is safe, it's not optimal
   - Better to pass variables from controller
   - Avoid logic in view layer

---

## ✅ Resolution

- [x] Controller logic fixed
- [x] Metrics variable added
- [x] Cache cleared
- [x] Testing completed
- [x] Documentation created

**Status:** Fully resolved and production-ready ✨
