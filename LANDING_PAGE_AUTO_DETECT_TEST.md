# Landing Page Auto-Detect Testing Guide

## 🎯 Overview

Sistem auto-detection telah diimplementasikan untuk landing page Bizmark.ID. Landing page akan otomatis mendeteksi device (desktop/mobile) dan menampilkan versi yang sesuai.

## 📋 Implementasi Summary

### 1. Route Auto-Detection (`/`)
**File**: `routes/web.php`

Route utama `/` sekarang memiliki logic auto-detection:
- **Screen Width Detection** (Priority 1): Menggunakan session `screen_width` yang diset oleh JavaScript
- **User Agent Detection** (Fallback): Deteksi mobile devices dari User-Agent header
- **Breakpoint**: 768px (standard Tailwind `md` breakpoint)

**Behavior**:
```
Width < 768px  → Redirect to /m/landing (Magazine Mobile)
Width >= 768px → Show desktop landing page
```

**Manual Override**:
- `/?mobile=1` → Force mobile version
- `/?desktop=1` → Force desktop version

### 2. Desktop Landing Auto-Redirect
**File**: `resources/views/landing.blade.php` (Line ~1415)

Added JavaScript:
```javascript
// Monitor screen width changes
// If resize from desktop to mobile (cross 768px threshold)
// → Auto-redirect to /m/landing after 1 second
```

**Features**:
- Screen width tracking via `/api/set-screen-width`
- 1-second debounce to prevent accidental redirects during resize
- Only redirects when crossing the 768px threshold
- Console logging for debugging

### 3. Mobile Landing Auto-Redirect
**File**: `resources/views/mobile-landing/layouts/magazine.blade.php` (Line ~295)

Added JavaScript:
```javascript
// Monitor screen width changes
// If resize from mobile to desktop (cross 768px threshold)
// → Auto-redirect to /?desktop=1 after 1 second
```

**Features**:
- Screen width tracking via `/api/set-screen-width`
- 1-second debounce
- Only redirects when crossing the 768px threshold
- Console logging for debugging

### 4. Screen Width API
**File**: `routes/web.php` (Line ~73)

Existing API endpoint:
```
POST /api/set-screen-width
Body: { width: 1024 }
→ Stores width in session
```

Used by both desktop and mobile landing pages to track actual screen dimensions.

## 🧪 Testing Checklist

### Test 1: Initial Access (Mobile Device)
**Setup**: Use mobile device or Chrome DevTools mobile emulation

1. ✅ Open browser on mobile (width < 768px)
2. ✅ Navigate to `http://localhost:8000/`
3. **Expected**: Auto-redirect to `/m/landing` (Magazine layout)
4. **Verify**: 
   - URL changes to `/m/landing`
   - Magazine-style mobile layout appears
   - All 8 sections visible (Cover, Stats, Services, Why Us, Testimonials, FAQ, Contact, Footer)
   - Sticky action bar at bottom

**Commands**:
```bash
# Test mobile viewport
curl -A "Mozilla/5.0 (iPhone; CPU iPhone OS 16_0 like Mac OS X) AppleWebKit/605.1.15" http://localhost:8000/ -I
# Expected: HTTP/1.1 302 Found
# Location: /m/landing
```

### Test 2: Initial Access (Desktop)
**Setup**: Use desktop browser (width >= 768px)

1. ✅ Open browser on desktop
2. ✅ Navigate to `http://localhost:8000/`
3. **Expected**: Show desktop landing page (existing design)
4. **Verify**:
   - URL stays at `/`
   - Desktop layout with full-width design
   - All desktop sections visible

**Commands**:
```bash
# Test desktop viewport
curl -A "Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/120.0.0.0" http://localhost:8000/ -I
# Expected: HTTP/1.1 200 OK
# (No redirect)
```

### Test 3: Responsive Resize (Desktop → Mobile)
**Setup**: Start on desktop, resize to mobile

1. ✅ Open `http://localhost:8000/` on desktop (width >= 768px)
2. ✅ Verify desktop landing page loads
3. ✅ Open browser DevTools
4. ✅ Resize browser window to < 768px width (e.g., 375px)
5. ✅ Wait 1 second
6. **Expected**: Auto-redirect to `/m/landing`
7. **Verify Console**: "Switched to mobile view, redirecting..."

**Chrome DevTools Steps**:
```
1. F12 → Open DevTools
2. Ctrl+Shift+M → Toggle device toolbar
3. Select "iPhone 14 Pro" or "Responsive" with width < 768px
4. Observe redirect after 1 second
```

### Test 4: Responsive Resize (Mobile → Desktop)
**Setup**: Start on mobile, resize to desktop

1. ✅ Open `http://localhost:8000/m/landing` on mobile
2. ✅ Verify magazine mobile layout loads
3. ✅ Open browser DevTools
4. ✅ Resize browser window to >= 768px width (e.g., 1024px)
5. ✅ Wait 1 second
6. **Expected**: Auto-redirect to `/?desktop=1`
7. **Verify Console**: "Switched to desktop view, redirecting..."

**Chrome DevTools Steps**:
```
1. Start at 375px width (mobile)
2. Drag viewport handle to 1024px
3. Observe redirect after 1 second
```

### Test 5: Manual Override (Force Mobile)
**Test**: Force mobile version on desktop

1. ✅ Navigate to `http://localhost:8000/?mobile=1`
2. **Expected**: Redirect to `/m/landing` regardless of screen width
3. **Verify**: Magazine mobile layout appears even on desktop browser

### Test 6: Manual Override (Force Desktop)
**Test**: Force desktop version on mobile

1. ✅ Navigate to `http://localhost:8000/?desktop=1`
2. **Expected**: Show desktop landing page regardless of screen width
3. **Verify**: Desktop layout appears even on mobile browser

### Test 7: Direct Mobile Access
**Test**: Direct access to mobile URL

1. ✅ Navigate to `http://localhost:8000/m/landing` on any device
2. **Expected**: Always show magazine mobile layout
3. **Verify**: No redirect, stays at `/m/landing`

### Test 8: Screen Width Session Persistence
**Test**: Verify session tracks width across pages

1. ✅ Open `/` on mobile → Redirects to `/m/landing`
2. ✅ Click "Portal Login" → Goes to `/login`
3. ✅ Check DevTools Application → Session Storage
4. **Expected**: Session contains `screen_width` value
5. ✅ Click back → Should remember it's mobile

### Test 9: Multiple Resize Events
**Test**: Rapid resize shouldn't cause multiple redirects

1. ✅ Start on desktop landing (`/`)
2. ✅ Quickly resize window: 1024px → 500px → 800px → 600px
3. **Expected**: Only ONE redirect after resizing stops
4. **Verify**: No redirect loop or flashing

**Technical**:
- 1-second debounce prevents multiple triggers
- `lastScreenWidth` tracking prevents unnecessary checks

### Test 10: Cross-Browser Testing

**Browsers to Test**:
- ✅ Chrome (Desktop + Mobile)
- ✅ Firefox (Desktop + Mobile)
- ✅ Safari (Desktop + Mobile iOS)
- ✅ Edge (Desktop)

**Test Each**:
1. Initial mobile access → `/m/landing`
2. Initial desktop access → `/`
3. Resize desktop → mobile
4. Resize mobile → desktop

## 🎨 Visual Testing

### Desktop Landing (`/`)
**Expected Elements**:
- Full-width hero section
- Service cards grid
- Blog/article integration
- Contact CTA sections
- Footer with multiple columns

### Mobile Landing (`/m/landing`)
**Expected Elements**:
- Full-screen magazine cover with parallax
- Stats infographic (5 cards)
- Featured articles (services)
- Photo essay (why us)
- Pull quotes (testimonials)
- Accordion FAQ
- Contact spread
- Footer
- Sticky action bar (WhatsApp + Phone + Login)

## 📊 Performance Verification

### Check JavaScript Console
**Desktop Landing**:
```
✅ "Screen width: 1024"
✅ "Screen width updated successfully"
(If resized to mobile):
✅ "Switched to mobile view, redirecting..."
```

**Mobile Landing**:
```
✅ "Screen width: 375"
✅ "Screen width updated successfully"
(If resized to desktop):
✅ "Switched to desktop view, redirecting..."
```

### Check Network Tab
**API Calls**:
```
POST /api/set-screen-width
Request: { width: 1024 }
Response: { success: true, width: 1024 }
Status: 200 OK
```

### Check Server Logs
```bash
# Monitor real-time logs
php artisan serve --host=0.0.0.0 --port=8000

# Watch for:
[INFO] GET / → 302 Redirect to /m/landing (mobile detected)
[INFO] GET / → 200 OK (desktop detected)
[INFO] POST /api/set-screen-width → 200 OK
```

## 🐛 Troubleshooting

### Issue 1: No Auto-Redirect on Resize
**Symptoms**: Window resized but no redirect happens

**Debug Steps**:
1. Open Console (F12)
2. Check for JavaScript errors
3. Verify `screen_width` in session:
   ```javascript
   console.log(document.cookie);
   ```
4. Check if CSRF token exists:
   ```javascript
   console.log(document.querySelector('meta[name="csrf-token"]'));
   ```

**Solution**:
- Ensure `<meta name="csrf-token">` exists in both layouts
- Check CSRF middleware is enabled
- Verify `/api/set-screen-width` route exists

### Issue 2: Redirect Loop
**Symptoms**: Page keeps redirecting between `/` and `/m/landing`

**Debug Steps**:
1. Check Console for repeated redirect messages
2. Verify breakpoint is 768px (not fluctuating)
3. Check if viewport meta tag is correct

**Solution**:
- Clear browser cache and cookies
- Check viewport meta tag: `<meta name="viewport" content="width=device-width, initial-scale=1.0">`
- Verify no conflicting JavaScript

### Issue 3: Session Not Persisting
**Symptoms**: Screen width not saved across page loads

**Debug Steps**:
1. Check Laravel session driver (`.env`):
   ```
   SESSION_DRIVER=file  # or cookie, database
   ```
2. Verify session middleware in `app/Http/Kernel.php`
3. Check file permissions on `storage/framework/sessions/`

**Solution**:
```bash
# Fix permissions
chmod -R 775 storage/
chown -R www-data:www-data storage/

# Clear sessions
php artisan session:clear
```

### Issue 4: Desktop Shows Mobile Layout
**Symptoms**: Desktop browser (width > 768px) shows mobile magazine layout

**Debug Steps**:
1. Check actual window width:
   ```javascript
   console.log(window.innerWidth);
   ```
2. Check if manual `?mobile=1` parameter is in URL
3. Verify route logic in `routes/web.php`

**Solution**:
- Navigate to `/?desktop=1` to reset
- Check if `session('prefer_mobile')` is set
- Clear session: `php artisan session:clear`

## ✅ Acceptance Criteria

All tests PASS when:

1. ✅ Mobile devices (width < 768px) accessing `/` are redirected to `/m/landing`
2. ✅ Desktop devices (width >= 768px) accessing `/` see desktop landing page
3. ✅ Resizing from desktop to mobile triggers auto-redirect to `/m/landing` after 1 second
4. ✅ Resizing from mobile to desktop triggers auto-redirect to `/?desktop=1` after 1 second
5. ✅ Manual override `?mobile=1` and `?desktop=1` work correctly
6. ✅ No redirect loops occur
7. ✅ Screen width session persists across page navigations
8. ✅ Both layouts render correctly with all content
9. ✅ No JavaScript console errors
10. ✅ API endpoint `/api/set-screen-width` responds successfully

## 🚀 Testing Commands

```bash
# Start Laravel server
php artisan serve --host=0.0.0.0 --port=8000

# Test in browser:
# Desktop: http://localhost:8000/
# Mobile: http://localhost:8000/?mobile=1
# Direct Mobile: http://localhost:8000/m/landing

# cURL tests:
# Mobile User-Agent
curl -L -A "Mozilla/5.0 (iPhone)" http://localhost:8000/

# Desktop User-Agent
curl -L -A "Mozilla/5.0 (Windows NT 10.0)" http://localhost:8000/

# Check routes
php artisan route:list | grep landing

# Monitor logs
tail -f storage/logs/laravel.log
```

## 📝 Test Results Log

**Date**: _____________________
**Tester**: ___________________
**Environment**: _______________

| Test # | Test Case | Status | Notes |
|--------|-----------|--------|-------|
| 1 | Initial Mobile Access | ⬜ | |
| 2 | Initial Desktop Access | ⬜ | |
| 3 | Resize Desktop→Mobile | ⬜ | |
| 4 | Resize Mobile→Desktop | ⬜ | |
| 5 | Manual Override Mobile | ⬜ | |
| 6 | Manual Override Desktop | ⬜ | |
| 7 | Direct Mobile Access | ⬜ | |
| 8 | Session Persistence | ⬜ | |
| 9 | Multiple Resize Events | ⬜ | |
| 10 | Cross-Browser Testing | ⬜ | |

**Overall Result**: ⬜ PASS / ⬜ FAIL

**Issues Found**:
_______________________________________
_______________________________________
_______________________________________

## 🎓 Next Steps After Testing

1. **If All Tests Pass**:
   - ✅ Deploy to staging environment
   - ✅ Conduct user acceptance testing (UAT)
   - ✅ Monitor analytics for redirect patterns
   - ✅ Optimize images for mobile landing page

2. **If Issues Found**:
   - 🔧 Document issues in detail
   - 🔧 Fix critical issues first (redirects, layout)
   - 🔧 Re-test after fixes
   - 🔧 Update this document with solutions

3. **Performance Optimization**:
   - 📊 Measure page load times (desktop vs mobile)
   - 📊 Check API response times
   - 📊 Optimize redirect logic if needed
   - 📊 Implement caching strategies

4. **Analytics Setup**:
   - 📈 Track redirect events (mobile → desktop, desktop → mobile)
   - 📈 Monitor user journey (landing → login → dashboard)
   - 📈 Measure conversion rates by device type
   - 📈 A/B test landing page variants

---

**Last Updated**: January 2025
**Status**: Ready for Testing ✅
**Server**: Running on port 8000
**Route**: Active (`GET /` → `landing` | `GET /m/landing` → `mobile.landing`)
