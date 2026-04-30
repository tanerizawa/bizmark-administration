# Admin Panel CSS Separation Plan

## Root Cause Analysis

### The Problem
Since migrating from CDN Tailwind v3 to Vite-bundled Tailwind v4 (BUG-11/12), the admin panel's dark theme is broken. The display looks like a light theme with dark overlays — "tidak seperti ketika masih memaki CDN".

### Why Previous Fixes Didn't Work

1. **CSS Cascade Layer Conflict**: Tailwind v4 outputs all utility classes inside `@layer utilities {}`. Inline `<style>` blocks are **unlayered**. Per CSS cascade rules, **unlayered styles always beat layered styles**, regardless of specificity or source order. This means:
   - `h1 { color: var(--dark-text-primary); }` (inline, unlayered) beats `text-green-400` (Tailwind, layered)
   - Any Tailwind text/bg utility class on `h1-h6/p/label/td/th` elements is silently overridden

2. **Neuroscience Light Theme Still Loaded**: `app.css` imports `neuroscience-variables.css` which defines light-themed CSS variables (`--color-bg-primary: #FDFBF8` cream background). The inline `<style>` block tries to override these, but since BOTH are unlayered, the source order works correctly — BUT:
   - The `app.css` has hardcoded light values (`.card { background-color: #fff; }`) that cannot be fixed with CSS variable overrides
   - Every admin page load fights against the light theme, creating unpredictable rendering

3. **Shared CSS File**: `app.css` is shared between admin layout (~146 views) and landing blog pages. The neuroscience light theme was designed for landing pages, not the admin panel.

## Solution: Separate Admin CSS Entry Point

### Architecture

```
resources/css/
  ├── app.css            ← Kept for landing/blog pages (with neuroscience light theme)
  ├── admin.css          ← NEW: For admin panel only (dark theme)
  ├── landing.css
  ├── landing-theme.css
  ├── inquiry-form.css
  └── neuroscience-variables.css ← Only imported by app.css for landing pages
```

### Implementation Steps

#### Step 1: Create `resources/css/admin.css`

```
@import 'tailwindcss';
@import '@fortawesome/fontawesome-free/css/fontawesome.css';
@import '@fortawesome/fontawesome-free/css/regular.css';
@import '@fortawesome/fontawesome-free/css/solid.css';
@import '@fortawesome/fontawesome-free/css/brands.css';

@source '../../vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php';
@source '../../storage/framework/views/*.php';
@source '../**/*.blade.php';
@source '../**/*.js';

@theme {
    --font-sans: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}

/* === DARK THEME (Apple HIG-inspired) === */

:root {
    /* Apple Design System Colors */
    --apple-blue: #007AFF;
    --apple-blue-dark: #0051D5;
    --apple-green: #34C759;
    --apple-orange: #FF9500;
    --apple-red: #FF3B30;
    --apple-purple: #AF52DE;
    --apple-pink: #FF2D55;
    --apple-teal: #5AC8FA;
    --apple-indigo: #5856D6;
    
    /* Dark Mode Colors */
    --dark-bg: #000000;
    --dark-bg-secondary: #1C1C1E;
    --dark-bg-tertiary: #2C2C2E;
    --dark-bg-elevated: rgba(28, 28, 30, 0.9);
    --dark-separator: rgba(84, 84, 88, 0.35);
    --dark-text-primary: #FFFFFF;
    --dark-text-secondary: rgba(235, 235, 245, 0.6);
    --dark-text-tertiary: rgba(235, 235, 245, 0.3);
    
    /* Shadows */
    --shadow-soft: 0 2px 15px rgba(0, 0, 0, 0.5);
    --shadow-soft-lg: 0 10px 40px rgba(0, 0, 0, 0.6);
    --shadow-soft-xl: 0 20px 50px rgba(0, 0, 0, 0.7);
}

/* Global Styles */
* { box-sizing: border-box; margin: 0; padding: 0; }

html, body {
    margin: 0; padding: 0; width: 100%; height: 100%;
}

body {
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    background-color: var(--dark-bg);
    color: var(--dark-text-primary);
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
    overflow-x: hidden;
}

/* Text elements (span and div excluded to allow Tailwind utilities) */
h1, h2, h3, h4, h5, h6, p, label, td, th {
    color: var(--dark-text-primary);
}

/* Cards */
.card-elevated, .card {
    background-color: var(--dark-bg-elevated);
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
    border: 1px solid var(--dark-separator);
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.48);
}

/* Links */
a { color: var(--apple-blue); text-decoration: none; }
a:hover { color: #4DA3FF; }

/* Buttons */
.btn {
    background: var(--dark-bg-tertiary) !important;
    color: var(--dark-text-primary) !important;
    border: 1px solid var(--dark-separator) !important;
}
.btn-primary {
    background: var(--apple-blue) !important;
    color: #fff !important;
    border: none !important;
}
.btn-secondary {
    background: var(--dark-bg-secondary) !important;
    color: var(--apple-blue) !important;
    border: 1px solid var(--dark-separator) !important;
}

/* Form Elements */
input[type="text"], input[type="email"], input[type="password"],
input[type="number"], input[type="date"], input[type="time"],
input[type="search"], textarea, select {
    border-color: var(--dark-separator);
}
input:focus, textarea:focus, select:focus {
    border-color: var(--apple-blue);
    box-shadow: 0 0 0 3px rgba(0, 122, 255, 0.15);
}

/* ... plus all admin utility classes currently in the inline <style> block ... */
```

> **Key**: Since `admin.css` is loaded BEFORE the inline `<style>` in the Blade layout, rules in `admin.css` will cascade correctly. The inline `<style>` should be removed entirely, or kept only for truly dynamic values. Tailwind utilities in `admin.css` are still in `@layer utilities`, but since there's NO conflicting light theme, everything works harmoniously.

#### Step 2: Register in `vite.config.js`

```js
input: [
    'resources/css/app.css',
    'resources/css/admin.css',     // ← ADD
    'resources/css/landing.css',
    'resources/css/inquiry-form.css',
    'resources/css/landing-theme.css',
    'resources/js/app.js',
],
```

#### Step 3: Update `resources/views/layouts/app.blade.php`

Change:
```blade
@vite(['resources/css/app.css', 'resources/js/app.js'])
```
To:
```blade
@vite(['resources/css/admin.css', 'resources/js/app.js'])
```

Then **remove the entire inline `<style>` block** (lines 22-... ~1700 lines). All those classes move to `admin.css`.

#### Step 4: Rebuild

```bash
npm run build
```

#### Step 5: Verify

- Check `/admin/cash-accounts` in browser (hard refresh: Ctrl+Shift+R)
- All admin pages should now render with proper dark theme
- Landing pages (`/`, `/blog`) should still render with neuroscience light theme

## Files Modified

| File | Action | Description |
|------|--------|-------------|
| `resources/css/admin.css` | **CREATE** | New admin-only CSS with dark theme |
| `vite.config.js` | **EDIT** | Add `resources/css/admin.css` to input array |
| `resources/views/layouts/app.blade.php` | **EDIT** | Change `@vite()` to load `admin.css`; remove inline `<style>` block |
| `public/build/` | **REGENERATE** | `npm run build` creates new CSS asset |

## Risks & Mitigation

| Risk | Mitigation |
|------|-----------|
| Missing admin classes that were only in inline `<style>` | Comprehensive audit of `<style>` block; all classes copied to `admin.css` |
| CSP blocks inline styles? | CSP already allows `unsafe-inline` for styles, so existing inline styles in views still work |
| Landing pages lose dark theme variables | `app.css` still has neuroscience variables; landing pages use `landing-theme.css` which has its own theme |
| Vite build error | Run `npm run build` and fix any import/syntax issues |
