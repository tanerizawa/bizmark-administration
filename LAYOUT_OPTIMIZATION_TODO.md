# Layout Optimization - TODO List

## ✅ SUDAH DIIMPLEMENTASIKAN (22 Nov 2025)

### Phase 1: Critical Bug Fixes
**Problem:** `body { overflow: hidden }` menyebabkan halaman login/register tidak bisa scroll
**Solution:** 
- Changed to `overflow-x: hidden; overflow-y: auto;`
- Added conditional `.authenticated` class to only lock overflow for logged-in users
- Guest pages can now scroll properly

### Phase 2: CSS Cleanup
**Problem:** Two `.app-shell` declarations causing confusion
**Solution:** Merged into single declaration

### Phase 3: Query Optimization
**Problem:** `active_projects` query executed but never used
**Solution:** Removed from `$navCounts` array

### Phase 4: Basic Mobile Responsiveness
**Problem:** Fixed 256px sidebar causes layout issues on mobile
**Solution:** Added media query for < 768px to hide sidebar by default

### Phase 5: VIEW COMPOSER IMPLEMENTATION ✅ **COMPLETED!**
**Problem:** 15-20 database queries running on every page load from sidebar
**Solution Implemented:**

#### ✅ Created NavigationComposer
- File: `app/View/Composers/NavigationComposer.php`
- Uses `Cache::remember()` properly (not `Cache::get()`)
- Cache TTL: 5 minutes for nav counts, 1 minute for notifications
- Separates data into 3 categories: `$navCounts`, `$permitNotifications`, `$otherNotifications`

#### ✅ Registered in AppServiceProvider
- Updated: `app/Providers/AppServiceProvider.php`
- Composer attached to `layouts.app` view

#### ✅ Removed ALL Inline Queries from Layout
- Removed `@php` blocks with Eloquent queries
- Now uses cached data from View Composer
- Clean, maintainable blade template

#### ✅ Created Cache Helper
- File: `app/Helpers/CacheHelper.php`
- Method: `clearNavigationCache()` to invalidate all nav caches
- Ready for use in controllers/observers when data changes

#### ✅ Created Artisan Command
- Command: `php artisan cache:refresh-navigation`
- Clears navigation cache on demand
- Useful for admin maintenance or after bulk data changes

---

## 🔴 REMAINING TASKS
**Current Problem:**
```php
// Di layout, query dijalankan setiap page load:
$navCounts = Cache::get('bizmark-perizinan-cache-nav_counts', [
    'projects' => \App\Models\Project::count(),
    // ... 7+ queries lainnya
]);
```

**Issue:** 
- `Cache::get()` dengan default array **TIDAK** menyimpan ke cache
- Semua query tetap jalan setiap request
- Bisa lambat saat data besar

**Solution yang harus diimplementasikan:**

#### a) Buat View Composer
File: `app/View/Composers/NavigationComposer.php`

```php
<?php

namespace App\View\Composers;

use Illuminate\View\View;
use Illuminate\Support\Facades\Cache;
use App\Models\{
    Project, Task, Document, Institution, Client, 
    PermitType, PermitTemplate, PermitApplication, 
    ApplicationNote, Payment, JobApplication, 
    EmailInbox, BankReconciliation
};

class NavigationComposer
{
    public function compose(View $view)
    {
        $navCounts = Cache::remember('nav_counts', 300, function () {
            return [
                'projects' => Project::count(),
                'tasks' => Task::count(),
                'pending_tasks' => Task::where('status', 'pending')->count(),
                'documents' => Document::count(),
                'institutions' => Institution::count(),
                'clients' => Client::count(),
                'permit_types' => PermitType::count(),
                'permit_templates' => PermitTemplate::count(),
            ];
        });

        $permitNotifications = Cache::remember('permit_notifications', 60, function () {
            return [
                'submitted' => PermitApplication::where('status', 'submitted')->count(),
                'under_review' => PermitApplication::where('status', 'under_review')->count(),
                'unread_notes' => ApplicationNote::where('author_type', 'client')
                    ->where('is_read', false)->count(),
                'pending_payments' => Payment::where('payment_method', 'manual')
                    ->where('status', 'processing')->count(),
            ];
        });

        $otherNotifications = Cache::remember('other_notifications', 60, function () {
            return [
                'pending_job_apps' => JobApplication::where('status', 'pending')->count(),
                'unread_emails' => EmailInbox::where('category', 'inbox')
                    ->where('is_read', false)->count(),
                'pending_reconciliations' => BankReconciliation::where('status', 'pending')->count(),
            ];
        });

        $view->with([
            'navCounts' => $navCounts,
            'permitNotifications' => $permitNotifications,
            'otherNotifications' => $otherNotifications,
        ]);
    }
}
```

#### b) Register View Composer
File: `app/Providers/ViewServiceProvider.php` (atau di `AppServiceProvider`)

```php
use Illuminate\Support\Facades\View;
use App\View\Composers\NavigationComposer;

public function boot()
{
    View::composer('layouts.app', NavigationComposer::class);
}
```

#### c) Update Layout
Hapus semua `@php` query di `layouts/app.blade.php`, ganti dengan:

```blade
{{-- navCounts, permitNotifications, otherNotifications sudah tersedia dari View Composer --}}
```

**Benefits:**
- Query hanya dijalankan 1x per 5 menit (300s) atau 1 menit (60s)
- Jauh lebih cepat karena pakai cache yang benar
- Lebih maintainable, logic terpisah dari view

---

### 2. Validasi/Fix Route `/api/set-screen-width`

**Current Code:**
```javascript
fetch('/api/set-screen-width', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
    },
    body: JSON.stringify({ width: width })
})
```

**TODO:**
1. Cek apakah route ini ada di `routes/web.php` atau `routes/api.php`
2. Jika tidak ada, ada 2 pilihan:
   - **Hapus** JS code ini (lebih simple)
   - **Buat** route-nya jika memang perlu

**Jika ingin implement:**
```php
// routes/web.php
Route::post('/api/set-screen-width', function (Request $request) {
    session(['screen_width' => $request->width]);
    return response()->json(['success' => true]);
})->middleware('auth');
```

---

## 🟡 RECOMMENDED - SANGAT DISARANKAN

### 3. Improve Mobile Menu
**Current:** Sidebar hidden di mobile dengan CSS saja
**Perlu:** Tambahkan toggle button untuk buka/tutup sidebar

**Implementation:**
1. Tambahkan hamburger button di `.app-topbar` untuk mobile
2. Tambahkan JavaScript untuk toggle class `.mobile-open`
3. Tambahkan backdrop/overlay saat sidebar terbuka

---

### 4. Optimize Inline Queries di Sidebar
**Problem:** Masih ada query inline di sidebar:

```php
@php
    $submittedCount = \App\Models\PermitApplication::where('status', 'submitted')->count();
    $underReviewCount = \App\Models\PermitApplication::where('status', 'under_review')->count();
    // dst...
@endphp
```

**Solution:** Pindahkan semua ke View Composer (lihat point #1)

---

## 🟢 NICE TO HAVE - Opsional

### 5. Struktur HTML Konsisten
**Current:** Beberapa nav-section tidak konsisten struktur HTML-nya
**Fix:** Bungkus semua dengan struktur yang sama:

```blade
<div class="nav-section">
    <div class="nav-section-title">Title</div>
    <div class="nav-links">...</div>
</div>
```

---

## TESTING CHECKLIST

Setelah implementasi View Composer:

- [ ] Test di desktop (sidebar muncul normal)
- [ ] Test di mobile (sidebar hidden, bisa scroll)
- [ ] Test halaman login/register (bisa scroll penuh)
- [ ] Test semua badge notification (angka masih update)
- [ ] Monitor query performance di Laravel Debugbar
- [ ] Clear cache: `php artisan cache:clear`
- [ ] Clear view cache: `php artisan view:clear`

---

## ESTIMASI IMPACT

**Sebelum (current):**
- ~15-20 database queries per page load (dari sidebar)
- Loading time: ~200-500ms (tergantung data size)

**Sesudah (dengan caching):**
- ~0-2 database queries per page load (cache hit)
- Loading time: ~50-100ms
- Cache refresh setiap 1-5 menit

**Performance Gain:** ~70-80% faster page load

---

## NOTES

- Cache key menggunakan format simple: `nav_counts`, `permit_notifications`, dll
- TTL (Time To Live) diset 60-300 detik (1-5 menit)
- Bisa disesuaikan sesuai kebutuhan real-time data
- Untuk data yang perlu real-time (misal unread messages), bisa pakai polling/pusher

