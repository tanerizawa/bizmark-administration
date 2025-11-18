# 🚀 PWA Mobile Admin - Quick Reference

## 📍 URLs

```
Mobile:   https://bizmark.id/m
Desktop:  https://bizmark.id/dashboard

Toggle:
  POST /m/force-desktop  → Switch to desktop
  POST /m/force-mobile   → Switch to mobile
```

## 🗺️ Route Map

```
/m                          → Dashboard
/m/projects                 → Projects list
/m/projects/{id}            → Project detail
/m/projects/search?q=...    → Search projects
/m/approvals                → Approvals dashboard
/m/approvals/pending        → Pending only
/m/approvals/{type}/{id}    → Approval detail
/m/tasks                    → Tasks list (TODO)
/m/financial                → Financial dashboard (TODO)
/m/notifications            → Notifications (TODO)
/m/profile                  → User profile (TODO)
```

## 🎯 Controllers

### DashboardController
```php
index()    → Mobile dashboard dengan 4 metrics
refresh()  → JSON refresh endpoint
sync()     → Offline sync handler
```

### ProjectController
```php
index()        → List + filter + search
show($id)      → Detail dengan tabs
search($q)     → Autocomplete
addNote($id)   → Quick note
updateStatus() → Quick status update
timeline($id)  → Timeline lazy load
quickCreate()  → Create dari bottom sheet
```

### ApprovalController
```php
index()                → Dashboard
pending()              → Pending only
show($type, $id)       → Detail
approve($type, $id)    → Single approve
reject($type, $id)     → Single reject
bulkApprove()          → Bulk approve
bulkReject()           → Bulk reject
```

## 🎨 Views

```
mobile/layouts/app.blade.php           → Layout utama
mobile/dashboard/index.blade.php       → Dashboard
mobile/projects/index.blade.php        → Projects list
mobile/projects/show.blade.php         → Project detail
mobile/approvals/index.blade.php       → Approvals
```

## 🔧 Key Components

### Alpine.js Components

**Dashboard:**
```javascript
dashboardMobile() {
  urgent: { count, label, color, items }
  runway: { months, cash, burn, label, color }
  approvals: { count, label, items }
  tasks: { today, overdue, upcoming, label, color }
  
  refresh()          // Pull-to-refresh
  expand(section)    // Toggle sections
}
```

**Projects:**
```javascript
projectsPage() {
  projects: []
  currentStatus: 'active'
  searchQuery: ''
  
  filterStatus(status)   // Filter by status
  search()               // Debounced search
  loadMore()             // Pagination
}

projectDetail(id) {
  activeTab: 'overview'
  timeline: []
  
  loadTimeline()         // Lazy load
}
```

**Approvals:**
```javascript
approvalsPage() {
  approvals: []
  selectedItems: []
  currentRejectItem: null
  
  toggleSelection(item)  // Bulk select
  quickApprove(item)     // Single approve
  showRejectModal(item)  // Show modal
  confirmReject()        // Confirm reject
  bulkApprove()          // Bulk approve
  bulkReject()           // Bulk reject
}

swipeableCard(item) {
  swipeX: 0
  
  touchStart(e)          // Swipe start
  touchMove(e)           // Swipe move
  touchEnd(e)            // Swipe end (trigger action)
}
```

## 🎨 CSS Classes

### Layout
```css
.mobile-page            → Main container
.safe-top               → Safe area top (iOS)
.safe-bottom            → Safe area bottom (iOS)
.scrollbar-hide         → Hide scrollbar
```

### Cards
```css
.approval-card          → Swipeable card container
.metric-card            → Dashboard metric card
.project-card           → Project list item
```

### Colors
```css
from-blue-500 to-blue-600    → Projects gradient
from-purple-500 to-purple-600 → Approvals gradient
from-green-500 to-green-600   → Financial gradient

bg-green-100 text-green-800   → Success badge
bg-blue-100 text-blue-800     → Info badge
bg-yellow-100 text-yellow-800 → Warning badge
bg-red-100 text-red-800       → Danger badge
```

## 🔐 Authentication

All routes protected:
```php
Route::middleware(['auth', 'mobile'])
```

CSRF token required:
```javascript
headers: {
  'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
}
```

## 📦 Service Worker

```javascript
// Version
const CACHE_VERSION = 'v2.5.0';

// Caches
STATIC_CACHE   → Static assets (manifest, favicon)
DYNAMIC_CACHE  → Client portal pages
IMAGE_CACHE    → Images
MOBILE_CACHE   → Mobile admin pages

// Strategies
/m/*           → networkFirstMobile (aggressive cache)
/client/*      → networkFirstClient
images         → cacheFirstImage
static         → cacheFirstStatic
```

## 🎯 Performance Tips

### Backend
```php
// Cache queries
Cache::remember('key', 120, fn() => Query::get());

// Eager load
->with(['relation1', 'relation2'])

// Select specific fields
->select('id', 'name', 'status')

// Paginate
->paginate(20)
```

### Frontend
```javascript
// Debounce search
@input.debounce.300ms="search()"

// Lazy load
x-show="activeTab === 'timeline'" 
x-init="loadTimeline()"

// Infinite scroll
@scroll.window="loadMore()"
```

## 🐛 Debug

### Check Routes
```bash
php artisan route:list --path=m
```

### Clear Cache
```bash
php artisan config:clear
php artisan route:clear
php artisan cache:clear
```

### Test Mobile Detection
```php
// In controller
dd(request()->header('User-Agent'));
dd(session('force_desktop'));
dd($isMobile);
```

### Service Worker Debug
```javascript
// Console
navigator.serviceWorker.getRegistrations()
  .then(regs => console.log(regs));

// Check cache
caches.keys().then(keys => console.log(keys));
```

## 📱 Testing Devices

### Chrome DevTools
```
F12 → Toggle Device Toolbar (Ctrl+Shift+M)
Devices: iPhone 12 Pro, Pixel 5, iPad Air
Network: Slow 3G, Fast 3G, Offline
```

### Real Devices
```bash
# HTTPS required for PWA
ngrok http 8000

# Or Laravel Valet (Mac/Linux)
valet secure bizmark
```

## 🎨 Customization

### Colors
```php
// resources/views/mobile/layouts/app.blade.php
<div class="bg-gradient-to-br from-YOUR-500 to-YOUR-600">
```

### Bottom Nav
```html
<!-- Add new tab -->
<a href="/m/YOUR-PAGE" class="nav-item">
  <i class="fas fa-YOUR-ICON"></i>
  <span>Label</span>
</a>
```

### Shortcuts
```json
// public/manifest.json
{
  "shortcuts": [
    {
      "name": "Your Feature",
      "url": "/m/your-page",
      "icons": [...]
    }
  ]
}
```

## 📊 Monitoring

### Performance
```javascript
// Lighthouse
Performance: 90+
PWA: 100
Accessibility: 90+
```

### Database
```sql
-- Mobile usage
SELECT COUNT(*) FROM page_views WHERE url LIKE '/m/%';

-- Approval time
SELECT AVG(approved_at - created_at) FROM project_expenses;
```

### Cache
```bash
# Redis
redis-cli
KEYS mobile_*
GET mobile_dashboard_1
```

## 🚨 Common Issues

**Routes 404:**
```bash
php artisan route:clear
```

**Service Worker outdated:**
```javascript
// Increment version
const CACHE_VERSION = 'v2.5.1';
// Hard refresh: Ctrl+Shift+R
```

**Mobile not detected:**
```php
// Check middleware registered
'mobile' => \App\Http\Middleware\DetectMobile::class
```

**PWA not installable:**
```
✓ HTTPS required
✓ manifest.json accessible
✓ Service Worker registered
✓ Icons correct size
```

## 📚 Documentation

```
PWA_MOBILE_ADMIN_COMPREHENSIVE_ANALYSIS.md  → Analysis
WIREFRAMES_AND_FLOWS.md                     → Designs
QUICK_START_GUIDE.md                        → Implementation
DESKTOP_VS_MOBILE_COMPARISON.md             → Comparison
MOBILE_IMPLEMENTATION_COMPLETE.md           → Status
MOBILE_README_ID.md                         → Indonesian guide
MOBILE_CHANGELOG.md                         → Changes
MOBILE_QUICK_REFERENCE.md                   → This file
```

## 🎯 Next Steps

```
✅ Phase 1: Core (Dashboard, Projects, Approvals)
🔄 Phase 2: Complete (Tasks, Financial, Profile)
📅 Phase 3: Enhance (Push, Sync, Analytics)
🚀 Phase 4: Production (Optimize, Deploy, Monitor)
```

---

**Quick Links:**
- Desktop: `/dashboard`
- Mobile: `/m`
- Docs: `/docs/pwa-mobile/`
- Service Worker: `/sw.js`
- Manifest: `/manifest.json`

**Version:** 1.0.0  
**Updated:** 2025-11-18
