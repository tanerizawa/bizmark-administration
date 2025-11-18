# 🚀 PWA Mobile Admin - Production Deploy Guide

## Pre-Deployment Checklist (15 Menit)

### ✅ 1. Test Lokal

```bash
# Clear all caches
php artisan config:clear
php artisan route:clear
php artisan cache:clear
php artisan view:clear

# Test routes
php artisan route:list --path=m
# Expected: 30+ routes

# Run server
php artisan serve

# Test di browser
# Desktop: http://localhost:8000/dashboard
# Mobile: http://localhost:8000/m (use Chrome DevTools mobile mode)
```

### ✅ 2. Code Review

**Files to verify:**
- [ ] `routes/mobile.php` - All routes defined
- [ ] `app/Http/Middleware/DetectMobile.php` - Middleware exists
- [ ] `app/Http/Controllers/Mobile/*` - 7 controllers exist
- [ ] `resources/views/mobile/*` - Views exist
- [ ] `public/sw.js` - Version v2.5.0
- [ ] `public/manifest.json` - Mobile shortcuts added
- [ ] `public/mobile-offline.html` - Offline page exists
- [ ] `bootstrap/app.php` - Routes & middleware registered

**Quick check:**
```bash
# Count files
ls -la app/Http/Controllers/Mobile/ | wc -l
# Expected: 7 controllers + 2 (. and ..) = 9

ls -la resources/views/mobile/ | wc -l
# Expected: Multiple views

# Check service worker version
grep "CACHE_VERSION" public/sw.js
# Expected: v2.5.0
```

---

## Production Deployment (30 Menit)

### Step 1: Backup (5 min)

```bash
# Backup database
php artisan backup:run

# Atau manual backup
mysqldump -u bizmark -p bizmark_db > backup_$(date +%Y%m%d_%H%M%S).sql

# Backup files
tar -czf backup_files_$(date +%Y%m%d_%H%M%S).tar.gz \
  storage/app \
  public/uploads \
  .env
```

### Step 2: Git Commit & Push (5 min)

```bash
# Check status
git status

# Add all files
git add .

# Commit
git commit -m "feat: PWA Mobile Admin - Complete Implementation

- Add mobile routes with /m prefix
- Add DetectMobile middleware for auto-detection
- Add 7 mobile controllers (Dashboard, Project, Approval, Task, Financial, Notification, Profile)
- Add 6 mobile views (dashboard, projects, approvals, tasks)
- Update Service Worker to v2.5.0 with mobile caching
- Add mobile shortcuts to manifest.json
- Add beautiful mobile offline fallback page
- Add comprehensive documentation (9 files)

Features:
- Swipeable approval cards
- Pull-to-refresh dashboard
- Offline support
- PWA installable
- Mobile-first design

Status: Ready for production testing"

# Push to repository
git push origin main
```

### Step 3: Deploy to Server (10 min)

**SSH ke server:**
```bash
ssh user@bizmark.id
# Atau sesuai server setup
```

**Di server:**
```bash
# Navigate to project
cd /var/www/bizmark.id

# Pull latest code
git pull origin main

# Install dependencies
composer install --optimize-autoloader --no-dev

# NPM (if needed)
npm install && npm run build

# Run migrations (jika ada perubahan DB)
php artisan migrate --force

# Clear & cache configs
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# Optimize
php artisan optimize

# Set permissions
chmod -R 775 storage
chmod -R 775 bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

### Step 4: Restart Services (2 min)

```bash
# Restart PHP-FPM
sudo systemctl restart php8.2-fpm
# Atau sesuai versi PHP

# Reload Nginx
sudo systemctl reload nginx

# Check status
sudo systemctl status php8.2-fpm
sudo systemctl status nginx
```

### Step 5: Verify Deployment (8 min)

```bash
# Test endpoint dari server
curl -I https://bizmark.id/m
# Expected: HTTP/2 200

# Check routes
php artisan route:list --path=m | head -n 20

# Check logs
tail -f storage/logs/laravel.log
```

**Test dari browser:**
1. Buka https://bizmark.id/m
2. Check console untuk errors
3. Test navigation
4. Check Service Worker: DevTools → Application → Service Workers
5. Check manifest: DevTools → Application → Manifest

---

## Post-Deployment Testing (20 Menit)

### ✅ Desktop Testing (5 min)

**Chrome DevTools:**
```
1. Buka https://bizmark.id/dashboard
2. F12 → Toggle Device Toolbar (Ctrl+Shift+M)
3. Select device: iPhone 12 Pro
4. Refresh page
5. Expected: Auto-redirect ke /m

Test:
☐ Dashboard loads
☐ Projects list loads
☐ Approvals list loads
☐ Tasks list loads
☐ Navigation works
☐ No console errors
```

### ✅ Real Device Testing (10 min)

**Android (Chrome):**
```
1. Buka https://bizmark.id/m
2. Test swipe gesture di approvals
3. Test pull-to-refresh di dashboard
4. Chrome Menu → "Add to Home Screen"
5. Open dari home screen
6. Enable airplane mode → Test offline
7. Disable airplane mode → Should sync

Checklist:
☐ Page loads fast (<2s)
☐ Bottom navigation works
☐ Swipe gestures work
☐ Pull-to-refresh works
☐ PWA installable
☐ Offline page shows when offline
☐ Online sync works
```

**iOS (Safari):**
```
1. Buka https://bizmark.id/m di Safari
2. Share button → "Add to Home Screen"
3. Open dari home screen (fullscreen)
4. Test safe area (notch)
5. Test gestures

Checklist:
☐ Fullscreen mode works
☐ Safe area correct (no content under notch)
☐ Gestures work
☐ Navigation smooth
```

### ✅ Performance Testing (5 min)

**Lighthouse Audit:**
```
1. Chrome DevTools → Lighthouse tab
2. Select: Mobile + Progressive Web App
3. Click "Generate report"

Target Scores:
☐ Performance: 90+
☐ Accessibility: 90+
☐ Best Practices: 90+
☐ SEO: 90+
☐ PWA: 100

Key Metrics:
☐ First Contentful Paint: <1.5s
☐ Largest Contentful Paint: <2.5s
☐ Cumulative Layout Shift: <0.1
☐ Time to Interactive: <3s
```

---

## Rollback Procedure (If Needed)

```bash
# SSH to server
ssh user@bizmark.id
cd /var/www/bizmark.id

# Git rollback
git log --oneline -n 5
git revert HEAD --no-edit
# Atau specific commit:
# git revert abc1234 --no-edit

# Restore database (if needed)
mysql -u bizmark -p bizmark_db < backup_YYYYMMDD_HHMMSS.sql

# Clear caches
php artisan config:clear
php artisan route:clear
php artisan cache:clear

# Restart services
sudo systemctl restart php8.2-fpm
sudo systemctl reload nginx
```

---

## Monitoring Setup (Optional but Recommended)

### Laravel Telescope (Dev/Staging)

```bash
composer require laravel/telescope --dev
php artisan telescope:install
php artisan migrate
```

### Sentry (Production Error Tracking)

```bash
composer require sentry/sentry-laravel
php artisan sentry:publish --dsn=YOUR_SENTRY_DSN

# Add to .env
SENTRY_LARAVEL_DSN=your-dsn-here
SENTRY_TRACES_SAMPLE_RATE=0.2
```

### Google Analytics (Usage Tracking)

**Add to `mobile/layouts/app.blade.php`:**
```html
<!-- Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=GA_MEASUREMENT_ID"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());
  gtag('config', 'GA_MEASUREMENT_ID');
</script>
```

---

## Security Hardening (IMPORTANT!)

### Add Authorization Checks

**Edit controllers, tambahkan:**
```php
// Example: ProjectController
public function show(Project $project)
{
    $this->authorize('view', $project);
    // ... rest of code
}

// ApprovalController
public function approve($type, $id)
{
    $item = $this->getApprovalItem($type, $id);
    $this->authorize('approve', $item);
    // ... rest of code
}
```

### Add Rate Limiting

**Edit `routes/mobile.php`:**
```php
// Approval endpoints
Route::post('/{type}/{id}/approve', [ApprovalController::class, 'approve'])
    ->middleware('throttle:10,1'); // Max 10 per minute

Route::post('/{type}/{id}/reject', [ApprovalController::class, 'reject'])
    ->middleware('throttle:10,1');

Route::post('/bulk-approve', [ApprovalController::class, 'bulkApprove'])
    ->middleware('throttle:5,1'); // Max 5 per minute for bulk
```

### Add API Rate Limiting

**Create `app/Http/Middleware/MobileApiThrottle.php`:**
```php
<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;

class MobileApiThrottle
{
    public function handle(Request $request, Closure $next)
    {
        $key = 'mobile-api:' . $request->user()->id;
        
        if (RateLimiter::tooManyAttempts($key, 60)) {
            return response()->json([
                'success' => false,
                'message' => 'Too many requests. Please try again later.'
            ], 429);
        }
        
        RateLimiter::hit($key, 60); // 60 requests per minute
        
        return $next($request);
    }
}
```

---

## User Training

### For Admins

**Email Template:**
```
Subject: 🚀 New Feature: Mobile Admin App!

Hi Team,

Kami telah meluncurkan Mobile Admin App untuk Bizmark.ID!

🎯 Fitur Utama:
- ⚡ Approve dalam 5 detik (swipe gesture!)
- 📱 Access dari mana saja
- 🔄 Pull-to-refresh
- 📡 Works offline

🚀 Cara Pakai:
1. Buka bizmark.id di HP
2. Auto-redirect ke mobile version
3. Install PWA: Menu → "Add to Home Screen"

💡 Tips:
- Swipe right untuk approve
- Swipe left untuk reject
- Pull down untuk refresh dashboard

Video tutorial: [link]

Happy managing!
```

### Quick Start Video (Script)

```
0:00 - Intro: "Mobile Admin dalam 60 detik"
0:05 - Buka bizmark.id di mobile
0:10 - Auto-redirect ke /m
0:15 - Tour: Dashboard, Projects, Approvals, Tasks
0:30 - Demo swipe gesture approval
0:40 - Demo pull-to-refresh
0:50 - Demo install PWA
0:60 - Outro: "Start managing on the go!"
```

---

## Success Metrics to Track

### Week 1 (Adoption)
- [ ] Mobile pageviews > 20%
- [ ] PWA installs > 10 users
- [ ] Approval via mobile > 30%
- [ ] No critical bugs

### Week 2-4 (Engagement)
- [ ] Mobile pageviews > 40%
- [ ] Approval time < 1 minute avg
- [ ] Task completion +20%
- [ ] User feedback positive (NPS > 50)

### Month 2-3 (Optimization)
- [ ] Mobile pageviews > 60%
- [ ] Approval time < 30 seconds avg
- [ ] Task completion +30%
- [ ] User feedback excellent (NPS > 70)

### SQL Queries untuk Metrics

```sql
-- Mobile adoption rate
SELECT 
  DATE(created_at) as date,
  COUNT(CASE WHEN path LIKE '/m/%' THEN 1 END) * 100.0 / COUNT(*) as mobile_percentage
FROM page_views
WHERE created_at > NOW() - INTERVAL '7 days'
GROUP BY DATE(created_at)
ORDER BY date;

-- Average approval time
SELECT 
  AVG(EXTRACT(EPOCH FROM (approved_at - created_at))) as avg_seconds
FROM project_expenses
WHERE status = 'approved'
  AND approved_at > NOW() - INTERVAL '7 days';

-- Task completion rate
SELECT 
  COUNT(CASE WHEN status = 'done' THEN 1 END) * 100.0 / COUNT(*) as completion_rate
FROM tasks
WHERE updated_at > NOW() - INTERVAL '7 days';
```

---

## Troubleshooting

### Issue: Routes 404
```bash
php artisan route:clear
php artisan config:clear
php artisan cache:clear
```

### Issue: Service Worker Not Updating
```javascript
// Increment version di public/sw.js
const CACHE_VERSION = 'v2.5.1'; // Change this

// Hard refresh browser
Ctrl + Shift + R (Windows/Linux)
Cmd + Shift + R (Mac)
```

### Issue: Mobile Not Auto-Redirecting
```bash
# Check middleware registered
grep -n "mobile" bootstrap/app.php

# Should see:
# 'mobile' => \App\Http\Middleware\DetectMobile::class,
```

### Issue: PWA Not Installable
```
✓ Check HTTPS enabled
✓ Check manifest.json accessible: /manifest.json
✓ Check service worker registered
✓ Check icons exist and correct size
✓ Hard refresh: Ctrl+Shift+R
```

---

## 🎉 Deployment Complete!

**Final Checklist:**
- [x] Code deployed
- [x] Services restarted
- [x] Desktop test passed
- [x] Mobile test passed
- [x] Performance good
- [ ] Users notified
- [ ] Monitoring active

**Next Steps:**
1. Monitor logs untuk 24 jam pertama
2. Collect user feedback
3. Iterate based on feedback
4. Scale successful features

---

**Deploy Date:** ____________  
**Deployed By:** ____________  
**Server:** production  
**Version:** 1.0.0  
**Status:** ✅ LIVE
