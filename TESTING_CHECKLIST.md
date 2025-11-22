# 🧪 TESTING CHECKLIST - PHASE 3 & 4

## Testing Date: January 11, 2025
## Tester: Pre-Production Review
## Status: IN PROGRESS

---

## 1. SECURITY HEADERS TEST ✅

### Test Method:
```bash
# Test security headers
curl -I http://localhost/ 2>&1 | grep -E "X-|Content-Security|Strict-Transport"
```

### Expected Headers:
- [x] X-Content-Type-Options: nosniff
- [x] X-Frame-Options: SAMEORIGIN
- [x] X-XSS-Protection: 1; mode=block
- [x] Referrer-Policy: strict-origin-when-cross-origin
- [x] Permissions-Policy: geolocation=(), microphone=(), camera=()
- [x] Content-Security-Policy: (comprehensive policy)
- [x] Strict-Transport-Security (production only)

### Status: ✅ PASS / ⚠️ WARNING / ❌ FAIL

**Notes:**
- HSTS akan aktif hanya di production
- CSP policy sudah comprehensive
- All security headers present

---

## 2. PRIVACY POLICY PAGE TEST

### Test Cases:

#### A. Page Accessibility
- [ ] URL /privacy-policy accessible
- [ ] No 404 or 500 errors
- [ ] Page loads within 2 seconds

#### B. Indonesian Content
- [ ] Header "Kebijakan Privasi" displays
- [ ] Last updated date shows
- [ ] All 10 sections present:
  1. [ ] Informasi yang Kami Kumpulkan
  2. [ ] Penggunaan Cookie
  3. [ ] Bagaimana Kami Menggunakan Informasi Anda
  4. [ ] Berbagi Informasi
  5. [ ] Keamanan Data
  6. [ ] Hak Anda (6 boxes)
  7. [ ] Google Analytics
  8. [ ] Penyimpanan Data
  9. [ ] Perubahan Kebijakan
  10. [ ] Hubungi Kami (contact box)

#### C. English Content (Switch Language First)
- [ ] Header "Privacy Policy" displays
- [ ] All 10 sections in English
- [ ] Contact information correct

#### D. Design & UX
- [ ] Glassmorphism effects visible
- [ ] AOS animations working
- [ ] Back to home button works
- [ ] Responsive on mobile
- [ ] All links clickable

#### E. Integration
- [ ] Link from cookie banner works
- [ ] Route registered correctly
- [ ] No console errors

### Test Commands:
```bash
# Test route
curl -I http://localhost/privacy-policy

# Check for errors
docker compose logs app | grep -i error | tail -20
```

### Status: ⏳ PENDING / ✅ PASS / ❌ FAIL

**Notes:**

---

## 3. COOKIE CONSENT BANNER TEST

### Test Cases:

#### A. First Visit Behavior
- [ ] Banner appears on first visit
- [ ] Slide-up animation smooth
- [ ] Cookie emoji visible (🍪)
- [ ] "Pelajari lebih lanjut" link works
- [ ] Accept button visible
- [ ] Reject button visible

#### B. Accept Flow
- [ ] Click "Terima" button
- [ ] Banner slides down
- [ ] localStorage set to 'accepted'
- [ ] Google Analytics loads (check console)
- [ ] Banner doesn't appear on reload

**Test in Browser Console:**
```javascript
// Check consent status
localStorage.getItem('cookieConsent')
// Expected: 'accepted'

// Check if GA loaded
console.log(typeof gtag)
// Expected: 'function'
```

#### C. Reject Flow
- [ ] Clear localStorage first
- [ ] Click "Tolak" button
- [ ] Banner slides down
- [ ] localStorage set to 'rejected'
- [ ] Google Analytics NOT loaded
- [ ] Banner doesn't appear on reload

**Reset Test:**
```javascript
// Reset for testing
localStorage.removeItem('cookieConsent');
location.reload();
```

#### D. Privacy Policy Link
- [ ] "Pelajari lebih lanjut" link clickable
- [ ] Opens /privacy-policy page
- [ ] Returns to homepage after clicking back

### Status: ⏳ PENDING / ✅ PASS / ❌ FAIL

**Notes:**

---

## 4. GOOGLE ANALYTICS INTEGRATION TEST

### Test Cases:

#### A. Cookie Consent Integration
- [ ] GA NOT loaded on page load (before consent)
- [ ] GA loads after clicking "Terima"
- [ ] GA does NOT load after clicking "Tolak"
- [ ] Console log: "Google Analytics loaded"

#### B. GA4 Script Loading
- [ ] Check Network tab for gtag/js request
- [ ] Request includes correct Measurement ID
- [ ] Script loads from googletagmanager.com
- [ ] No CORS errors

**Browser Console Check:**
```javascript
// Check if dataLayer exists
console.log(window.dataLayer);
// Should be array if GA loaded

// Check gtag function
console.log(typeof gtag);
// Should be 'function' if GA loaded
```

#### C. Custom Event Tracking
Test each event type:

1. **WhatsApp Click Event**
   - [ ] Click WhatsApp button/link
   - [ ] Console log shows event tracked
   - [ ] Category: "Engagement"
   - [ ] Action: "whatsapp_click"

2. **Language Switch Event**
   - [ ] Switch to English
   - [ ] Console log shows event tracked
   - [ ] Category: "Settings"
   - [ ] Action: "language_switch"
   - [ ] Label: "EN"

3. **Search Submit Event**
   - [ ] Submit search form (404 page or blog)
   - [ ] Console log shows event tracked
   - [ ] Category: "Search"
   - [ ] Action: "search_submit"

4. **CTA Button Click Event**
   - [ ] Click any .btn-primary or .btn-secondary
   - [ ] Console log shows event tracked
   - [ ] Category: "CTA"
   - [ ] Action: "button_click"

5. **Phone Click Event**
   - [ ] Click phone number link
   - [ ] Console log shows event tracked
   - [ ] Category: "Engagement"
   - [ ] Action: "phone_click"

**Manual Event Test:**
```javascript
// Test trackEvent function
trackEvent('Test', 'manual_test', 'Testing');
// Check console for confirmation
```

#### D. Anonymous IP & Secure Cookies
- [ ] GA config includes 'anonymize_ip': true
- [ ] Cookie flags: 'SameSite=None;Secure'
- [ ] Check in code (layout.blade.php)

### Status: ⏳ PENDING / ✅ PASS / ❌ FAIL

**Notes:**
- Replace 'G-XXXXXXXXXX' with real GA4 Measurement ID before production
- Test in GA4 Real-Time reports after deployment

---

## 5. LANGUAGE SWITCHER TEST (Phase 3)

### Test Cases:

#### A. Desktop Dropdown
- [ ] Globe icon visible in navbar
- [ ] Current locale displays (ID/EN)
- [ ] Dropdown opens on click
- [ ] Checkmark on active language
- [ ] Smooth transitions
- [ ] Click away closes dropdown

#### B. Mobile Toggle
- [ ] Open hamburger menu
- [ ] Language section visible at bottom
- [ ] ID/EN buttons present
- [ ] Active button has blue background
- [ ] Touch-friendly sizing

#### C. Language Persistence
- [ ] Switch to English
- [ ] Page reloads with English content
- [ ] Navigate to another page → still English
- [ ] Close browser → reopen → still English
- [ ] Check session storage works

#### D. Translation Quality
- [ ] All nav items translated
- [ ] Hero section translated
- [ ] Services section translated
- [ ] Footer translated
- [ ] No missing translation keys

**Test in Browser:**
```javascript
// Check current locale
console.log(document.documentElement.lang);
// Should be 'id' or 'en'

// Check session
// (requires backend check)
```

### Status: ⏳ PENDING / ✅ PASS / ❌ FAIL

**Notes:**

---

## 6. LOADING STATES TEST (Phase 3)

### Test Cases:

#### A. Loading Screen
- [ ] Black screen appears on page load
- [ ] Blue spinner visible and rotating
- [ ] Auto-hides after ~500ms
- [ ] Smooth fade-out transition
- [ ] No flash of content

**Performance Check:**
```javascript
// Check loading time
window.addEventListener('load', function() {
    console.log('Page loaded in:', performance.now(), 'ms');
});
```

#### B. Skeleton Classes (if used)
- [ ] .skeleton class renders correctly
- [ ] Shimmer animation smooth
- [ ] .skeleton-text height correct
- [ ] .skeleton-title height correct
- [ ] .skeleton-image height correct

#### C. Browser Compatibility
- [ ] Works in Chrome
- [ ] Works in Firefox
- [ ] Works in Safari
- [ ] Works in Edge
- [ ] Works on mobile browsers

### Status: ⏳ PENDING / ✅ PASS / ❌ FAIL

**Notes:**

---

## 7. CUSTOM 404 PAGE TEST (Phase 3)

### Test Cases:

#### A. Page Accessibility
- [ ] Visit non-existent URL (/test-404)
- [ ] Custom 404 page displays
- [ ] No default Laravel error page

#### B. Design Elements
- [ ] Floating icon animation smooth
- [ ] Giant "404" gradient text visible
- [ ] Error message in Indonesian
- [ ] Background blur effects visible
- [ ] Glassmorphism design present

#### C. Search Functionality
- [ ] Search box visible
- [ ] Placeholder text present
- [ ] Can type in search box
- [ ] Submit redirects to /blog?search=query
- [ ] Query parameter passed correctly

#### D. CTA Buttons
- [ ] "Kembali ke Beranda" button works (→ /)
- [ ] "Baca Artikel" button works (→ /blog)
- [ ] WhatsApp button works (→ WhatsApp Web)
- [ ] All buttons hover effects work

#### E. Popular Pages Grid
- [ ] 4 cards display correctly
- [ ] Services card links to /#services
- [ ] Process card links to /#process
- [ ] About card links to /#about
- [ ] Blog card links to /blog
- [ ] All icons visible
- [ ] Hover effects work

#### F. Responsive Design
- [ ] Desktop layout (3-4 columns)
- [ ] Tablet layout (2 columns)
- [ ] Mobile layout (1 column)
- [ ] All elements readable

**Test URLs:**
```bash
curl -I http://localhost/this-does-not-exist
# Should return 404 with custom page
```

### Status: ⏳ PENDING / ✅ PASS / ❌ FAIL

**Notes:**

---

## 8. LIVE CHAT WIDGET TEST (Phase 3)

### Test Cases:

#### A. Widget Visibility
- [ ] WhatsApp widget visible bottom-right
- [ ] Green color (#25D366) correct
- [ ] Pulse animation on icon
- [ ] Always visible on scroll

#### B. Desktop Version
- [ ] Icon + text visible
- [ ] "Chat with Us" text displays
- [ ] "We're online!" subtitle displays
- [ ] Hover: scales up (1.05x)
- [ ] Hover: green glow shadow appears

#### C. Mobile Version
- [ ] Icon-only mode (text hidden)
- [ ] Touch-friendly size
- [ ] Positioned correctly
- [ ] Doesn't overlap other elements

#### D. Functionality
- [ ] Click opens WhatsApp Web in new tab
- [ ] Pre-filled message correct:
      "Halo Bizmark.ID, saya ingin berkonsultasi"
- [ ] Phone number correct: +6283879602855
- [ ] target="_blank" works
- [ ] No console errors

#### E. Z-Index & Positioning
- [ ] Above content (z-index 998)
- [ ] Below cookie banner (z-index 999)
- [ ] Doesn't interfere with FAB buttons
- [ ] Visible on all pages

### Status: ⏳ PENDING / ✅ PASS / ❌ FAIL

**Notes:**

---

## 9. SEO OPTIMIZATION TEST (Phase 4)

### Test Cases:

#### A. Meta Tags
- [ ] View page source
- [ ] `<title>` tag present and dynamic
- [ ] meta description present
- [ ] meta keywords present
- [ ] Language attribute on <html> tag
- [ ] Canonical URL correct
- [ ] No duplicate meta tags

**Check in Browser:**
```javascript
// View meta tags
document.querySelectorAll('meta').forEach(m => {
    console.log(m.getAttribute('name'), ':', m.getAttribute('content'));
});
```

#### B. Open Graph Tags
- [ ] og:type = "website"
- [ ] og:url = current URL
- [ ] og:title present
- [ ] og:description present
- [ ] og:image present
- [ ] og:locale correct (id_ID or en_US)
- [ ] og:site_name = "Bizmark.ID"

**Test with:**
- Facebook Sharing Debugger: https://developers.facebook.com/tools/debug/

#### C. Twitter Card Tags
- [ ] twitter:card = "summary_large_image"
- [ ] twitter:url present
- [ ] twitter:title present
- [ ] twitter:description present
- [ ] twitter:image present

**Test with:**
- Twitter Card Validator: https://cards-dev.twitter.com/validator

#### D. Hreflang Tags
- [ ] hreflang="id" present
- [ ] hreflang="en" present
- [ ] hreflang="x-default" present
- [ ] All point to correct URLs

#### E. Structured Data (JSON-LD)
- [ ] Organization schema present
- [ ] Service schema present
- [ ] WebSite schema with SearchAction present
- [ ] No JSON syntax errors

**Test with:**
```bash
# View structured data
curl http://localhost/ | grep -A 50 'application/ld+json'
```

**Validate with:**
- Google Rich Results Test: https://search.google.com/test/rich-results
- Schema Validator: https://validator.schema.org

#### F. XML Sitemap
- [ ] /sitemap.xml accessible
- [ ] Valid XML format
- [ ] Homepage included (priority 1.0)
- [ ] Blog index included (priority 0.9)
- [ ] Privacy policy included (priority 0.5)
- [ ] Published articles included (priority 0.8)
- [ ] lastmod dates present
- [ ] changefreq present

**Test:**
```bash
curl http://localhost/sitemap.xml
```

**Validate with:**
- XML Sitemap Validator: https://www.xml-sitemaps.com/validate-xml-sitemap.html

#### G. Robots.txt
- [ ] /robots.txt accessible
- [ ] User-agent: * present
- [ ] Allow: / present
- [ ] Disallow: /admin present
- [ ] Disallow: /dashboard present
- [ ] Disallow: /login present
- [ ] Disallow: /api present
- [ ] Sitemap URL present

**Test:**
```bash
curl http://localhost/robots.txt
```

### Status: ⏳ PENDING / ✅ PASS / ❌ FAIL

**Notes:**

---

## 10. RESPONSIVE DESIGN TEST

### Test Cases:

#### A. Desktop (≥1024px)
- [ ] Full navbar visible
- [ ] Language dropdown works
- [ ] All sections display correctly
- [ ] Images load properly
- [ ] No horizontal scroll

#### B. Tablet (768px - 1023px)
- [ ] Layout adjusts appropriately
- [ ] Touch-friendly elements
- [ ] Images responsive
- [ ] Navigation accessible

#### C. Mobile (< 768px)
- [ ] Hamburger menu works
- [ ] Mobile language toggle works
- [ ] Content stacks vertically
- [ ] Text readable (no tiny fonts)
- [ ] Buttons touch-friendly (min 44px)
- [ ] WhatsApp widget icon-only
- [ ] No horizontal scroll

#### D. Device Testing
- [ ] iPhone (Safari)
- [ ] Android (Chrome)
- [ ] iPad
- [ ] Desktop Chrome
- [ ] Desktop Firefox
- [ ] Desktop Safari

**Test with:**
- Chrome DevTools (F12 → Device Toolbar)
- Responsive Design Checker: https://responsivedesignchecker.com

### Status: ⏳ PENDING / ✅ PASS / ❌ FAIL

**Notes:**

---

## 11. PERFORMANCE TEST

### Test Cases:

#### A. Page Load Speed
- [ ] First Contentful Paint < 1.5s
- [ ] Largest Contentful Paint < 2.5s
- [ ] Time to Interactive < 3.5s
- [ ] Total page load < 3s
- [ ] No render-blocking resources

**Test with:**
```bash
# Check load time
curl -w "@curl-format.txt" -o /dev/null -s http://localhost/
```

**Test Tools:**
- PageSpeed Insights: https://pagespeed.web.dev
- GTmetrix: https://gtmetrix.com
- WebPageTest: https://www.webpagetest.org

#### B. Lighthouse Scores
- [ ] Performance: > 90
- [ ] Accessibility: > 90
- [ ] Best Practices: > 90
- [ ] SEO: > 90

#### C. Image Optimization
- [ ] Images lazy-loaded
- [ ] Proper image formats
- [ ] Correct dimensions
- [ ] No oversized images

#### D. Resource Optimization
- [ ] CSS minified (production)
- [ ] JS minified (production)
- [ ] No unused CSS/JS
- [ ] Fonts optimized
- [ ] Gzip compression enabled

### Status: ⏳ PENDING / ✅ PASS / ❌ FAIL

**Notes:**

---

## 12. BROWSER CONSOLE TEST

### Test Cases:

#### A. JavaScript Errors
- [ ] No errors in console
- [ ] No warnings (except expected)
- [ ] All functions defined
- [ ] No undefined variables

#### B. Network Requests
- [ ] All requests successful (200)
- [ ] No 404 errors
- [ ] No CORS errors
- [ ] No mixed content warnings

#### C. Console Logs (Development)
- [ ] "Cookies accepted" when accepted
- [ ] "Cookies rejected" when rejected
- [ ] "Google Analytics loaded" when GA loads
- [ ] Event tracking logs visible
- [ ] No unexpected errors

**Test:**
```javascript
// Open console (F12)
// Navigate through site
// Check for red errors
// Check network tab for failures
```

### Status: ⏳ PENDING / ✅ PASS / ❌ FAIL

**Notes:**

---

## 13. CROSS-BROWSER COMPATIBILITY

### Test Cases:

#### A. Google Chrome (Latest)
- [ ] All features work
- [ ] Animations smooth
- [ ] No visual glitches

#### B. Firefox (Latest)
- [ ] All features work
- [ ] Animations smooth
- [ ] No visual glitches

#### C. Safari (Latest)
- [ ] All features work
- [ ] Animations smooth
- [ ] WebKit-specific issues fixed

#### D. Edge (Latest)
- [ ] All features work
- [ ] Animations smooth
- [ ] No visual glitches

#### E. Mobile Browsers
- [ ] Chrome Mobile
- [ ] Safari iOS
- [ ] Samsung Internet
- [ ] Firefox Mobile

### Status: ⏳ PENDING / ✅ PASS / ❌ FAIL

**Notes:**

---

## 14. ACCESSIBILITY TEST

### Test Cases:

#### A. Keyboard Navigation
- [ ] Tab through all elements
- [ ] All links accessible
- [ ] All buttons accessible
- [ ] No keyboard traps
- [ ] Focus indicators visible

#### B. Screen Reader
- [ ] Alt text on images
- [ ] ARIA labels present
- [ ] Heading hierarchy correct
- [ ] Form labels present
- [ ] Meaningful link text

#### C. Color Contrast
- [ ] Text readable on backgrounds
- [ ] WCAG AA compliance
- [ ] No color-only information

#### D. Forms
- [ ] Labels associated with inputs
- [ ] Error messages clear
- [ ] Required fields marked
- [ ] Validation accessible

**Test Tools:**
- WAVE: https://wave.webaim.org
- axe DevTools: Browser extension
- Lighthouse Accessibility Audit

### Status: ⏳ PENDING / ✅ PASS / ❌ FAIL

**Notes:**

---

## 15. DATABASE & BACKEND TEST

### Test Cases:

#### A. Routes
- [ ] All routes registered correctly
- [ ] No route conflicts
- [ ] Middleware applied correctly
- [ ] No 500 errors

**Test:**
```bash
docker compose exec app php artisan route:list | grep locale
docker compose exec app php artisan route:list | grep sitemap
docker compose exec app php artisan route:list | grep privacy
```

#### B. Controllers
- [ ] LocaleController works
- [ ] SitemapController works
- [ ] All methods return expected results
- [ ] No exceptions thrown

#### C. Middleware
- [ ] SetLocale middleware active
- [ ] SecurityHeaders middleware active
- [ ] Session middleware working
- [ ] CSRF protection active

#### D. Database Queries
- [ ] Articles fetch correctly for sitemap
- [ ] No N+1 query problems
- [ ] Proper eager loading
- [ ] Query performance acceptable

**Test:**
```bash
# Enable query logging in local
# Check logs for slow queries
docker compose logs app | grep -i "slow query"
```

### Status: ⏳ PENDING / ✅ PASS / ❌ FAIL

**Notes:**

---

## 16. INTEGRATION TEST (End-to-End)

### User Journey Test:

#### Journey 1: First-Time Visitor
1. [ ] Visit homepage
2. [ ] See loading screen
3. [ ] Cookie banner appears
4. [ ] Click "Pelajari lebih lanjut"
5. [ ] Read privacy policy
6. [ ] Click back
7. [ ] Click "Terima" on banner
8. [ ] Banner disappears
9. [ ] GA loads (check console)
10. [ ] Switch to English
11. [ ] Content changes to English
12. [ ] Click WhatsApp chat
13. [ ] Opens WhatsApp Web
14. [ ] Return to site
15. [ ] Navigate to blog
16. [ ] Search for article
17. [ ] Event tracked (check console)

#### Journey 2: Returning Visitor
1. [ ] Visit homepage
2. [ ] No cookie banner (already accepted)
3. [ ] Language preference preserved
4. [ ] GA already active
5. [ ] All features work

#### Journey 3: Error Handling
1. [ ] Visit invalid URL
2. [ ] See custom 404 page
3. [ ] Search from 404 page
4. [ ] Redirects to blog with search
5. [ ] Click "Kembali ke Beranda"
6. [ ] Returns to homepage

### Status: ⏳ PENDING / ✅ PASS / ❌ FAIL

**Notes:**

---

## 17. SECURITY TEST

### Test Cases:

#### A. XSS Protection
- [ ] Security headers present
- [ ] Input sanitization working
- [ ] No script injection possible
- [ ] CSP blocks unauthorized scripts

**Test:**
```javascript
// Try injecting script in search
<script>alert('XSS')</script>
// Should be escaped/blocked
```

#### B. CSRF Protection
- [ ] Laravel CSRF tokens present
- [ ] Forms protected
- [ ] API requests protected

#### C. HTTPS Enforcement
- [ ] HTTP redirects to HTTPS (production)
- [ ] Secure cookies set
- [ ] HSTS header present (production)

#### D. SQL Injection
- [ ] Eloquent ORM prevents injection
- [ ] No raw queries vulnerable
- [ ] Input validation present

### Status: ⏳ PENDING / ✅ PASS / ❌ FAIL

**Notes:**

---

## 18. FINAL PRE-DEPLOYMENT CHECKLIST

### Code Quality:
- [ ] No console.log in production code
- [ ] No TODO comments unresolved
- [ ] No commented-out code
- [ ] Proper error handling
- [ ] Code formatted consistently

### Configuration:
- [ ] .env.example updated
- [ ] GA4 Measurement ID placeholder noted
- [ ] All secrets in .env not .git
- [ ] Production values documented

### Documentation:
- [ ] README updated
- [ ] PHASE_3_COMPLETE.md created
- [ ] PHASE_4_COMPLETE.md created
- [ ] Testing checklist created
- [ ] Deployment guide created

### Caches:
- [ ] Route cache cleared
- [ ] View cache cleared
- [ ] Config cache cleared
- [ ] Application cache cleared

### Git:
- [ ] All changes committed
- [ ] Meaningful commit messages
- [ ] No sensitive data in commits
- [ ] Branch up to date

### Deployment Prep:
- [ ] Database backups ready
- [ ] Rollback plan documented
- [ ] Monitoring tools ready
- [ ] Support contacts notified

---

## TEST EXECUTION LOG

### Tester: __________________
### Date: __________________
### Environment: Local / Staging / Production

### Issues Found:
1. 
2. 
3. 

### Critical Blockers:
- [ ] None
- [ ] Issue #: ______

### Minor Issues:
- [ ] None
- [ ] Issue #: ______

### Recommendations:
1. 
2. 
3. 

---

## SIGN-OFF

### Developer: __________________ Date: __________
### QA Tester: __________________ Date: __________
### Project Manager: ____________ Date: __________

---

## OVERALL STATUS

- [ ] ✅ ALL TESTS PASSED - Ready for Production
- [ ] ⚠️ MINOR ISSUES - Can deploy with notes
- [ ] ❌ CRITICAL ISSUES - DO NOT DEPLOY

**Final Notes:**



**Approved for Production:** YES / NO

---

*Testing Checklist Version: 1.0*  
*Last Updated: January 11, 2025*  
*Project: Bizmark.ID Landing Page*  
*Phases Tested: 3 & 4*
