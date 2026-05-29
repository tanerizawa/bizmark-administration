# Tailwind CSS v4 — Usage Rules
> **Last Updated:** 2026-05-03 | Tailwind CSS v4 via `@tailwindcss/vite` plugin (no tailwind.config.js)

---

## Import Architecture

### WAJIB: `@import 'tailwindcss'` (bukan @tailwind directives)
```css
/* ✅ WAJIB — Tailwind v4 syntax */
@import 'tailwindcss';
@import './design-tokens.css';
@import '@fortawesome/fontawesome-free/css/fontawesome.css';
@import '@fortawesome/fontawesome-free/css/regular.css';
@import '@fortawesome/fontawesome-free/css/solid.css';
@import '@fortawesome/fontawesome-free/css/brands.css';

/* ❌ DILARANG — Tailwind v3 syntax */
@tailwind base;
@tailwind components;
@tailwind utilities;
```

### WAJIB: `@source` untuk manual content scanning
```css
@source '../../vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php';
@source '../../storage/framework/views/*.php';
@source '../**/*.blade.php';
@source '../**/*.js';
```

### WAJIB: `@theme` untuk font (bukan tailwind.config.js)
```css
@theme {
    --font-sans: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}
```

### Custom dark variant (sudah terkonfigurasi di admin.css)
```css
/* Admin: dark mode via data-theme="dark" attribute di <html> */
@custom-variant dark (&:where([data-theme="dark"], [data-theme="dark"] *));
```

---

## Penggunaan di Admin Panel

### Tailwind HANYA untuk Layout di Admin

```blade
{{-- ✅ BOLEH — layout, spacing, grid --}}
<div class="space-y-4">
<div class="flex items-center justify-between gap-3">
<div class="grid grid-cols-3 gap-4">
<div class="overflow-x-auto">
<div class="flex-1 min-w-0 truncate">

{{-- ❌ DILARANG — warna dan background di admin --}}
<div class="bg-gray-800 text-white dark:bg-gray-900">
<span class="text-blue-500 border-gray-700">
<div class="bg-yellow-500/20 text-yellow-400">
```

### Warna di Admin: WAJIB pakai CSS vars + inline style

```blade
{{-- ❌ DILARANG --}}
<div class="bg-blue-600 text-white rounded-xl">

{{-- ✅ WAJIB --}}
<div style="background:var(--apple-blue);color:#fff;border-radius:10px">
```

---

## Penggunaan di Landing Page

### Tailwind utilities + semantic design tokens

```blade
{{-- ✅ BENAR — landing page, light+dark mode via prefers-color-scheme --}}
<div class="bg-surface text-primary border border-default rounded-xl p-6">
<button class="bg-primary text-white px-6 py-3 rounded-full hover:bg-primary-dark">
<p class="text-secondary text-sm">Deskripsi</p>

{{-- ✅ dark: prefix BOLEH di landing page --}}
<div class="bg-white dark:bg-gray-900 text-gray-900 dark:text-white">
```

---

## Design Tokens Usage

### Semantic tokens dari design-tokens.css

| Token | Value (Light) | Value (Dark) |
|-------|--------------|-------------|
| `--color-surface` | `#FDFBF8` | `#1C1C1E` |
| `--color-surface-elevated` | `#FFFFFF` | `#2C2C2E` |
| `--color-text-primary` | `#1A1410` | `#FFFFFF` |
| `--color-text-secondary` | `#6B5D52` | `rgba(235,235,245,.6)` |
| `--color-border` | `#E5E7EB` | `rgba(84,84,88,.35)` |

### DILARANG: Hardcoded hex/rgb
```css
/* ❌ DILARANG */
.my-text { color: #ff4444; }
.my-bg { background: #1c1c1e; }

/* ✅ WAJIB */
.my-text { color: var(--apple-red); }
.my-bg { background: var(--dark-bg-secondary); }
```

---

## Dark Mode Setup

### Admin Panel (data-theme="dark")
```css
/* Didefinisikan di design-tokens.css — jangan edit langsung admin.css */
[data-theme="dark"] {
    --color-surface: #1C1C1E;
    --color-text-primary: #FFFFFF;
    --color-border: rgba(84, 84, 88, 0.35);
}
```

**Catatan:** `dark:` Tailwind variant di admin bekerja via `@custom-variant dark` yang dibind ke `[data-theme="dark"]`. Tapi untuk color values di admin, **tetap gunakan inline style + CSS vars** karena lebih reliabel dan konsisten.

### Landing Page (prefers-color-scheme)
```css
@media (prefers-color-scheme: dark) {
    :root:not([data-theme="light"]) {
        --color-surface: #1C1C1E;
        --color-text-primary: #E5E5E5;
        --color-border: rgba(84, 84, 88, 0.35);
    }
}
```

---

## CSS File Organization

```
resources/css/
├── design-tokens.css       ← [WAJIB] Single source of truth — layer 1/2/3 tokens
├── admin.css               ← Admin panel: imports tailwindcss + FA + tokens
├── app.css                 ← General app CSS
├── landing.css             ← Landing page styles
└── landing-theme.css       ← Landing theme overrides
```

### Urutan import yang BENAR di admin.css
```css
@import './design-tokens.css';   /* 1. tokens dulu */
@import 'tailwindcss';           /* 2. tailwind */
@import '@fortawesome/...';      /* 3. FA icons */
/* kemudian custom component CSS */
```

---

## Class Order Convention (Tailwind)

Urutan yang disarankan:
1. Layout: `flex grid grid-cols-* gap-*`
2. Sizing: `w-* h-* min-w-* max-w-*`
3. Spacing: `p-* px-* py-* m-* space-*`
4. Typography: `text-* font-* leading-*`
5. Display/Overflow: `overflow-* truncate hidden`
6. Misc: `cursor-* select-* pointer-events-*`

---

## Vite Configuration

```js
// vite.config.js — TIDAK pakai postcss plugin
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        tailwindcss(),   // ← Tailwind v4 Vite plugin, bukan postcss
        laravel({ input: ['resources/css/admin.css', ...] })
    ]
});
```

**Tidak ada `tailwind.config.js`** — semua konfigurasi via `@theme {}` di CSS.
