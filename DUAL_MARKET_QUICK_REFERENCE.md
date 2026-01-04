# 🚀 DUAL MARKET - QUICK REFERENCE GUIDE

## 🎯 Overview

Bizmark.ID now supports **two distinct markets**:
- 🇮🇩 **Local Market**: Indonesian UMKM (pricing in IDR)
- 🇬🇧 **PMA Market**: Foreign investors (pricing in USD)

---

## 📍 URL Structure

### Base URLs:
```
Indonesian:  https://bizmark.id/id
English:     https://bizmark.id/en
```

### Routes Pattern:
```
/{locale}/               → Landing page
/{locale}/services       → Services list
/{locale}/services/{slug} → Service detail
/{locale}/blog           → Blog index
/{locale}/blog/{slug}    → Blog post
```

---

## 🔧 How It Works

### 1. Automatic Market Detection

When user visits `/en`:
1. **SetLocale middleware** detects route parameter
2. Sets `locale = 'en'` and `market_segment = 'pma'`
3. Stores in session for persistence
4. Controller loads appropriate config and view

### 2. Manual Language Switch

User clicks locale switcher:
1. Redirects to `/locale/{locale}`
2. LocaleController updates session
3. Redirects back to appropriate locale route

---

## 💻 Development Guide

### Adding New Content

#### 1. Translations
```php
// Add to lang/en/filename.php
return [
    'key' => 'English translation',
];

// Add to lang/id/filename.php  
return [
    'key' => 'Terjemahan Indonesia',
];
```

#### 2. Using Translations in Blade
```blade
{{ __('filename.key') }}
{{ __('landing.hero.title') }}
{{ __('investment.process.discovery.title') }}
```

#### 3. Locale-Aware Controllers
```php
public function index()
{
    $locale = app()->getLocale();
    $marketSegment = session('market_segment', 'local');
    
    // Load appropriate config
    $services = $marketSegment === 'pma' 
        ? config('services_pma') 
        : config('services_data');
    
    return view("page.{$locale}.index", compact('services'));
}
```

#### 4. Creating Locale-Specific Views

**Option A: Separate Views** (Recommended for major pages)
```
resources/views/
  ├── landing/
  │   ├── index.blade.php      (Indonesian)
  │   └── en/
  │       └── index.blade.php  (English)
```

**Option B: Dynamic Views** (For shared layouts)
```blade
@if(app()->getLocale() === 'en')
    <h1>{{ __('landing.hero.title') }}</h1>
@else
    <h1>{{ __('landing.hero.title') }}</h1>
@endif
```

---

## 🗺️ File Structure

### Configuration
```
config/
  ├── services_data.php  → Local market (9 services, IDR)
  └── services_pma.php   → PMA market (8 services, USD)
```

### Translations
```
lang/
  ├── en/
  │   ├── landing.php      → 1,100+ lines
  │   ├── investment.php   → 600+ lines
  │   └── ...
  └── id/
      └── ...
```

### Views
```
resources/views/
  ├── components/
  │   └── locale-switcher.blade.php  → Language selector
  ├── landing/
  │   ├── index.blade.php            → Indonesian landing
  │   └── en/
  │       └── index.blade.php        → English landing
  └── partials/
      ├── navbar.blade.php           → With locale switcher
      └── mobile-menu.blade.php      → With locale buttons
```

---

## 🧪 Testing Commands

### Quick Test
```bash
# Run comprehensive test suite
./test-dual-market.sh
```

### Manual Tests
```bash
# Check locale config
php artisan tinker
> app()->getLocale()
> config('app.available_locales')

# Check services count
> count(config('services_data'))  // 9 (local)
> count(config('services_pma'))   // 8 (PMA)

# Test translations
> __('landing.meta.title', [], 'en')
> __('landing.meta.title', [], 'id')

# Check routes
php artisan route:list --name=landing
```

---

## 🎨 UI Components

### Locale Switcher (Desktop)
```blade
<x-locale-switcher />
```

Features:
- Dropdown with flags (🇮🇩 🇬🇧)
- Active state indicator
- Market segment display
- Alpine.js powered

### Locale Switcher (Mobile)
Already integrated in `mobile-menu.blade.php`:
- Button-style language selection
- Active state highlighting
- No dropdown needed

---

## 📊 Config Loading Logic

### In Controllers:
```php
// Get current market segment
$marketSegment = session('market_segment', 'local');

// Load appropriate config
$services = $marketSegment === 'pma' 
    ? config('services_pma')   // Foreign investors
    : config('services_data'); // Local businesses

// Pass to view
return view($view, compact('services', 'locale', 'marketSegment'));
```

### In Views:
```blade
@foreach($services as $slug => $service)
    <h3>{{ $service['title'] }}</h3>
    <p>{{ $service['short_description'] }}</p>
    
    @if(isset($service['pricing']))
        <div>{{ $service['pricing']['display'] }}</div>
    @endif
@endforeach
```

---

## 🔗 Route Helpers

### Generating Locale-Aware URLs:
```blade
<!-- Current locale -->
<a href="{{ route('services.index.' . app()->getLocale()) }}">

<!-- Specific locale -->
<a href="{{ route('landing.en') }}">
<a href="{{ route('landing.id') }}">

<!-- Switch locale -->
<a href="{{ route('locale.switch', 'en') }}">
<a href="{{ route('locale.switch', 'id') }}">
```

---

## 🌐 SEO Implementation

### Hreflang Tags (Add to head)
```blade
<link rel="alternate" hreflang="id" href="https://bizmark.id/id">
<link rel="alternate" hreflang="en" href="https://bizmark.id/en">
<link rel="alternate" hreflang="x-default" href="https://bizmark.id">
```

### Canonical URLs
```blade
<link rel="canonical" href="https://bizmark.id/{{ app()->getLocale() }}">
```

### Open Graph
```blade
<meta property="og:locale" content="{{ app()->getLocale() === 'id' ? 'id_ID' : 'en_US' }}">
<meta property="og:locale:alternate" content="{{ app()->getLocale() === 'id' ? 'en_US' : 'id_ID' }}">
```

---

## 🔍 Debugging

### Check Current State:
```blade
<!-- In Blade views -->
<div class="debug">
    Locale: {{ app()->getLocale() }}<br>
    Market: {{ session('market_segment') }}<br>
    Config: {{ $marketSegment === 'pma' ? 'PMA' : 'Local' }}
</div>
```

### Common Issues:

**Issue**: Locale not switching
- **Check**: Session storage enabled
- **Fix**: Clear cache `php artisan cache:clear`

**Issue**: Wrong config loaded
- **Check**: Market segment in session
- **Fix**: Clear session or switch locale manually

**Issue**: Translations not showing
- **Check**: File exists in `lang/{locale}/`
- **Fix**: Create translation file or fallback to default

---

## 📝 Best Practices

### 1. Always Use Translation Keys
```blade
<!-- ❌ Bad -->
<h1>Welcome to Bizmark.ID</h1>

<!-- ✅ Good -->
<h1>{{ __('landing.hero.title') }}</h1>
```

### 2. Cache Locale-Aware Data
```php
// ❌ Bad
$articles = Article::published()->get();

// ✅ Good
$articles = cache()->remember("articles.{$locale}", 600, function () use ($locale) {
    return Article::published()->where('language', $locale)->get();
});
```

### 3. Pass Locale to Views
```php
// ✅ Good practice
return view($view, [
    'locale' => app()->getLocale(),
    'marketSegment' => session('market_segment'),
    // ... other data
]);
```

### 4. Use Named Routes
```blade
<!-- ❌ Bad -->
<a href="/en/services">

<!-- ✅ Good -->
<a href="{{ route('services.index.en') }}">
```

---

## 🚦 Status Indicators

### How to Check System Status:

1. **Locale Detection**: Visit `/en` → Should show English content
2. **Config Loading**: Check service pricing → USD for EN, IDR for ID
3. **Translation**: Check page titles → Different per locale
4. **Routing**: Click nav links → Should preserve locale
5. **Switcher**: Click locale switcher → Should update all content

---

## 📈 Performance Tips

### 1. Cache Translations (Production)
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 2. Eager Load Relations
```php
$articles = Article::with('author', 'category')
    ->where('language', $locale)
    ->get();
```

### 3. Use CDN for Static Assets
```blade
<link rel="stylesheet" href="{{ asset('css/app.css') }}">
<!-- Will use CDN in production -->
```

---

## 🔒 Security Checklist

- [x] Locale whitelist validation
- [x] Session encryption enabled
- [x] CSRF protection on locale switch
- [x] XSS prevention in translations
- [x] SQL injection prevention (Eloquent)

---

## 📞 Support

### Questions?
- **Technical**: Review [DUAL_MARKET_PHASE1_COMPLETE.md](DUAL_MARKET_PHASE1_COMPLETE.md)
- **Content**: Check translation files in `lang/{locale}/`
- **Testing**: Run `./test-dual-market.sh`

### Need Help?
Contact development team with:
1. Current locale (ID/EN)
2. Market segment (Local/PMA)
3. Expected vs actual behavior
4. Screenshots or error messages

---

## 🎯 Quick Commands Cheat Sheet

```bash
# Testing
./test-dual-market.sh              # Full test suite
php artisan route:list --name=landing  # Check routes
php artisan tinker                 # Interactive testing

# Cache Management
php artisan cache:clear            # Clear all cache
php artisan config:clear           # Clear config cache
php artisan view:clear             # Clear view cache

# Development
php artisan serve                  # Start dev server
php artisan route:list             # List all routes
php artisan tinker                 # REPL for testing
```

---

**Last Updated**: Phase 1 Completion  
**Version**: 1.0  
**Status**: ✅ Production Ready  

---

*For detailed implementation documentation, see [DUAL_MARKET_PHASE1_COMPLETE.md](DUAL_MARKET_PHASE1_COMPLETE.md)*
