# Quick Reference: CSP & Accessibility Best Practices

## 🚨 DO's and DON'Ts

### ❌ DON'T: Inline Event Handlers
```html
<!-- BAD: Violates CSP -->
<button onclick="doSomething()">Click</button>
<div onclick="alert('Hi')">Click me</div>
```

### ✅ DO: Event Delegation
```html
<!-- GOOD: CSP-compliant -->
<button type="button" data-action="do-something">Click</button>

<script>
document.addEventListener('click', function(e) {
  const btn = e.target.closest('[data-action="do-something"]');
  if (btn) doSomething();
});
</script>
```

---

### ❌ DON'T: Query DB in View
```php
<!-- BAD: N+1 queries, slow -->
{{ \App\Models\Project::count() }}
{{ \App\Models\Task::where('status', 'done')->count() }}
```

### ✅ DO: View Composer + Cache
```php
// View Composer
$data = Cache::remember('key', 300, function() {
    return ['count' => Project::count()];
});

// View
{{ $data['count'] ?? 0 }}
```

---

### ❌ DON'T: Decorative Icons without aria-hidden
```html
<!-- BAD: Screen reader will announce "star icon" -->
<i class="fas fa-star"></i>
```

### ✅ DO: Hide Decorative Icons
```html
<!-- GOOD: Screen reader skips this -->
<i class="fas fa-star" aria-hidden="true"></i>
```

---

### ❌ DON'T: Buttons without Type
```html
<!-- BAD: Defaults to type="submit" in forms -->
<button>Close</button>
```

### ✅ DO: Explicit Button Type
```html
<!-- GOOD: Explicit type -->
<button type="button">Close</button>
<button type="submit">Save</button>
```

---

### ❌ DON'T: Missing ARIA Labels
```html
<!-- BAD: Screen reader can't describe purpose -->
<button><i class="fas fa-times"></i></button>
<input type="text" placeholder="Search">
```

### ✅ DO: Proper ARIA Labels
```html
<!-- GOOD: Accessible to screen readers -->
<button aria-label="Close dialog">
  <i class="fas fa-times" aria-hidden="true"></i>
</button>
<input type="text" aria-label="Search content" placeholder="Search">
```

---

### ❌ DON'T: Auto-hide Critical Messages
```html
<!-- BAD: Error disappears before user reads -->
<div role="alert" class="error auto-hide">Critical error!</div>
```

### ✅ DO: Only Auto-hide Success
```html
<!-- GOOD: Success auto-hides, errors don't -->
<div role="alert" class="success" data-autohide="true">Saved!</div>
<div role="alert" class="error">Fix these errors.</div>
```

---

## 🎨 Accessibility Patterns

### Pattern 1: Close Button
```html
<button type="button" 
        data-dismiss="alert" 
        class="close-btn"
        aria-label="Close notification">
  <i class="fas fa-times" aria-hidden="true"></i>
</button>
```

### Pattern 2: Icon Button
```html
<button type="button" aria-label="Add to favorites">
  <i class="fas fa-heart" aria-hidden="true"></i>
  <span class="sr-only">Add to favorites</span>
</button>
```

### Pattern 3: Search Input
```html
<div role="search">
  <label for="search" class="sr-only">Search</label>
  <input id="search" 
         type="text" 
         aria-label="Search content"
         placeholder="Type to search...">
</div>
```

### Pattern 4: Loading State
```html
<button type="button" aria-busy="true" disabled>
  <i class="fas fa-spinner fa-spin" aria-hidden="true"></i>
  <span>Loading...</span>
  <span class="sr-only">Please wait, processing your request</span>
</button>
```

### Pattern 5: Badge/Count
```html
<a href="/notifications" aria-label="Notifications">
  <i class="fas fa-bell" aria-hidden="true"></i>
  <span class="badge" aria-label="5 unread notifications">5</span>
</a>
```

---

## 🔒 CSP Quick Rules

### script-src Directive
```html
<!-- Development: Allow CDN -->
script-src 'self' 'unsafe-eval' https://cdn.tailwindcss.com;

<!-- Production: No CDN, use nonce -->
script-src 'self' 'nonce-{random}';
```

### Common Domains to Whitelist
```html
<!-- Fonts -->
font-src 'self' https://fonts.gstatic.com https://cdnjs.cloudflare.com;

<!-- Styles -->
style-src 'self' 'unsafe-inline' https://fonts.googleapis.com;

<!-- Images -->
img-src 'self' data: blob: https://your-cdn.com;

<!-- Scripts -->
script-src 'self' https://cdn.jsdelivr.net https://cdn.tailwindcss.com;
```

---

## ⚡ Performance Tips

### 1. Cache Expensive Queries
```php
// BAD: Query every request
$count = Project::count();

// GOOD: Cache 5 minutes
$count = Cache::remember('project_count', 300, fn() => Project::count());
```

### 2. Eager Loading
```php
// BAD: N+1 queries
$projects = Project::all();
foreach ($projects as $project) {
    echo $project->institution->name; // Query per project!
}

// GOOD: Eager load
$projects = Project::with('institution')->get();
foreach ($projects as $project) {
    echo $project->institution->name; // No extra queries
}
```

### 3. Chunk Large Datasets
```php
// BAD: Load all into memory
Project::all()->each(function($project) { /* ... */ });

// GOOD: Process in chunks
Project::chunk(100, function($projects) {
    foreach ($projects as $project) { /* ... */ }
});
```

---

## 🧪 Testing Checklist

### Accessibility Testing
```bash
# Tools
- WAVE Browser Extension
- axe DevTools
- Lighthouse (Chrome DevTools)
- Screen Reader (NVDA/JAWS/VoiceOver)

# Manual Tests
□ Tab through all interactive elements
□ Test with screen reader
□ Check color contrast (4.5:1 minimum)
□ Verify focus indicators visible
□ Test keyboard-only navigation
```

### CSP Testing
```bash
# Browser Console
□ No CSP violation errors
□ All scripts execute successfully
□ All styles load correctly
□ All images display

# Tools
- Mozilla Observatory
- CSP Evaluator (Google)
- SecurityHeaders.com
```

### Performance Testing
```bash
# Tools
- Laravel Debugbar
- Laravel Telescope
- Chrome DevTools Performance
- WebPageTest.org

# Metrics to Check
□ DB queries < 20 per page
□ Page load time < 200ms
□ Cache hit rate > 80%
□ Memory usage stable
```

---

## 🐛 Common Mistakes & Fixes

### Mistake 1: Forgot aria-hidden on icons
```html
<!-- Before -->
<i class="fas fa-check"></i> Completed

<!-- After -->
<i class="fas fa-check" aria-hidden="true"></i> Completed
```

### Mistake 2: Missing button type
```html
<!-- Before (bad in forms) -->
<button onclick="close()">Close</button>

<!-- After -->
<button type="button" data-action="close">Close</button>
```

### Mistake 3: Inline styles (CSP violation)
```html
<!-- Before (violates CSP) -->
<div style="color: red;">Error</div>

<!-- After -->
<div class="text-red-600">Error</div>
```

### Mistake 4: Cache not invalidating
```php
// Before: Cache never clears
Cache::rememberForever('data', fn() => Model::all());

// After: Clear cache on model events
class ModelObserver {
    public function created($model) {
        Cache::forget('data');
    }
}
```

---

## 📚 Resources

### Official Documentation
- [Laravel View Composers](https://laravel.com/docs/10.x/views#view-composers)
- [Laravel Cache](https://laravel.com/docs/10.x/cache)
- [WCAG 2.1 Guidelines](https://www.w3.org/WAI/WCAG21/quickref/)
- [MDN ARIA](https://developer.mozilla.org/en-US/docs/Web/Accessibility/ARIA)
- [CSP Reference](https://content-security-policy.com/)

### Tools
- [WAVE Extension](https://wave.webaim.org/extension/)
- [axe DevTools](https://www.deque.com/axe/devtools/)
- [CSP Evaluator](https://csp-evaluator.withgoogle.com/)
- [SecurityHeaders.com](https://securityheaders.com/)

### Cheat Sheets
- [ARIA Roles](https://www.w3.org/TR/wai-aria-1.1/#role_definitions)
- [CSP Directives](https://content-security-policy.com/reference/)
- [Laravel Best Practices](https://github.com/alexeymezenin/laravel-best-practices)

---

**Remember:**
- 🔒 Security first
- ♿ Accessibility matters
- ⚡ Performance optimization
- 📖 Document everything
- 🧪 Test thoroughly

**Quick Help:**
```bash
# Clear all caches
php artisan cache:clear
php artisan config:clear
php artisan view:clear

# Check CSP in browser
Console → Check for "Content Security Policy" errors

# Test accessibility
Install WAVE extension → Run audit
```
