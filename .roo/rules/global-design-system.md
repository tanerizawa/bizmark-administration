# Bizmark Design System — Global Rules
> **Last Updated:** 2026-05-03 | Stack: Tailwind CSS v4 · Alpine.js v3.15.1 · Font Awesome v7.1.0 · Chart.js v4.4 · Vite v7

---

## Aturan Fundamental — Dua Konteks Berbeda

App ini punya dua konteks styling yang **berbeda aturannya**:

| Konteks | Styling Utama | Dark Mode | Lokasi |
|---------|--------------|-----------|--------|
| **Admin Panel** | Inline `style=""` + CSS vars | `data-theme="dark"` di `<html>` | `resources/views/admin/**` |
| **Landing Page** | Tailwind utility classes + semantic tokens | `prefers-color-scheme` | `resources/views/landing/**` |

---

## Admin Panel — Aturan Wajib

### 1. Inline `style=""` + CSS Variables untuk Warna

Admin panel WAJIB menggunakan inline `style=""` dengan CSS variables untuk semua warna, background, dan border.
Tailwind `dark:` classes **DILARANG** untuk color values di admin — tidak reliabel di dark theme.

```blade
{{-- ✅ WAJIB — inline style dengan CSS vars --}}
<div style="background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:14px;padding:16px">

{{-- ✅ WAJIB — tinted background dengan color-mix() --}}
<span style="background:color-mix(in srgb,var(--apple-blue) 15%,transparent);color:var(--apple-blue);padding:3px 10px;border-radius:20px">

{{-- ❌ DILARANG di admin --}}
<div class="dark:bg-gray-800 dark:text-white bg-yellow-500/20 text-yellow-400">
```

### 2. Tailwind Hanya untuk Layout di Admin

Tailwind utilities boleh digunakan di admin **hanya** untuk layout:

```blade
{{-- ✅ BOLEH — layout utilities --}}
<div class="space-y-4">
<div class="flex items-center justify-between gap-4">
<div class="grid grid-cols-3 gap-3">
<div class="overflow-hidden overflow-x-auto">

{{-- ❌ JANGAN untuk warna/bg di admin --}}
<div class="bg-blue-500 text-white border-gray-700">
```

### 3. CSS Variables yang Selalu Tersedia di Admin

Didefinisikan di `resources/css/admin.css` + `resources/css/design-tokens.css`:

#### Apple Accent Colors
```css
--apple-blue:   #007AFF   /* Primary action, link, active */
--apple-green:  #34C759   /* Success, converted, positif */
--apple-orange: #FF9500   /* Warning, pending */
--apple-red:    #FF3B30   /* Danger, error, delete */
--apple-yellow: #FFD60A   /* Info ringan, starred */
--apple-purple: #AF52DE   /* Premium, special */
--apple-teal:   #5AC8FA   /* Info, processing */
--apple-indigo: #5856D6   /* Secondary blue */
--apple-pink:   #FF2D55   /* Highlight */
```

#### Dark Surfaces
```css
--dark-bg:            #000000            /* Background overlay */
--dark-bg-secondary:  #1C1C1E            /* Card utama, container */
--dark-bg-tertiary:   #2C2C2E            /* Input, badge, thead */
--dark-bg-elevated:   rgba(28,28,30,.9)  /* Modal, dropdown, floating */
--dark-separator:     rgba(84,84,88,.35) /* Border semua container */
```

#### Text
```css
--dark-text-primary:   #FFFFFF                  /* Judul, nilai utama */
--dark-text-secondary: rgba(235,235,245,.6)     /* Label, deskripsi */
--dark-text-tertiary:  rgba(235,235,245,.3)     /* Placeholder */
```

#### Shadows
```css
--shadow-soft:    0 2px 15px rgba(0,0,0,.5)
--shadow-soft-lg: 0 10px 40px rgba(0,0,0,.6)
--shadow-soft-xl: 0 20px 50px rgba(0,0,0,.7)
```

#### Transitions
```css
--transition-fast: 200ms cubic-bezier(.4,0,.2,1)
--transition-base: 300ms cubic-bezier(.4,0,.2,1)
--transition-slow: 400ms cubic-bezier(.4,0,.2,1)
```

### 4. Pola `color-mix()` untuk Tinted Backgrounds

```blade
{{-- Badge tint 15% --}}
<span style="background:color-mix(in srgb,var(--apple-blue) 15%,transparent);color:var(--apple-blue)">

{{-- Gradient card thumbnail --}}
<div style="background:linear-gradient(135deg,color-mix(in srgb,var(--apple-red) 25%,var(--dark-bg-secondary)),var(--dark-bg-secondary))">

{{-- Dynamic Blade expression --}}
<span style="background:{{ $ok ? 'var(--apple-green)' : 'color-mix(in srgb,var(--dark-bg-tertiary) 100%,transparent)' }};color:{{ $ok ? '#fff' : 'var(--dark-text-secondary)' }}">
```

### 5. Hover Effects

Simple hover: `onmouseover`/`onmouseout` diizinkan:

```blade
{{-- ✅ Acceptable untuk simple opacity hover --}}
<button onmouseover="this.style.opacity='.8'" onmouseout="this.style.opacity='1'">
<a onmouseover="this.style.color='var(--dark-text-primary)'" onmouseout="this.style.color='var(--dark-text-secondary)'">

{{-- ✅ LEBIH BAIK — Alpine untuk state kompleks --}}
<div x-data="{ hover: false }" @mouseenter="hover=true" @mouseleave="hover=false"
     :style="hover ? 'background:var(--dark-bg-tertiary)' : ''">
```

### 6. Global CSS Classes yang Valid di Admin

Didefinisikan di `resources/css/admin.css` — boleh dipakai langsung di class="":

```
card-elevated          → card dengan shadow elevation
rounded-apple          → border-radius: 10px
rounded-apple-lg       → border-radius: 14px
rounded-apple-xl       → border-radius: 18px
input-apple            → input field Apple-style dark
transition-apple       → transition cubic-bezier 300ms
bg-apple-blue          → background: var(--apple-blue)
bg-apple-green         → background: var(--apple-green)
bg-apple-red           → background: var(--apple-red)
bg-apple-orange        → background: var(--apple-orange)
bg-apple-purple        → background: var(--apple-purple)
text-apple-blue        → color: var(--apple-blue)
```

### 7. `x-cloak` Wajib

```blade
{{-- CSS ini ada di admin layout — pastikan tidak dihapus --}}
<style>[x-cloak]{display:none!important}</style>

{{-- Wajib dipakai di semua modal/dropdown Alpine --}}
<div x-cloak x-show="open" x-transition>...</div>
```

---

## Landing Page — Aturan

### 8. Tailwind Semantic Classes + design-tokens.css

```blade
{{-- ✅ WAJIB — semantic tokens --}}
<div class="bg-surface text-primary border border-default rounded-lg p-6">

{{-- ✅ Dark mode via Tailwind custom-variant --}}
<div class="bg-white dark:bg-gray-900 text-gray-900 dark:text-white">
```

---

## Aturan Bersama (Admin + Landing)

### 9. Zero Hardcoded Hex/RGB Values

```blade
{{-- ❌ DILARANG --}}
<span style="color:#ff4444">
<div style="background:#1c1c1e">
<span class="text-[#007AFF]">

{{-- ✅ WAJIB --}}
<span style="color:var(--apple-red)">
<div style="background:var(--dark-bg-secondary)">
```

### 10. Zero Neuro/Legacy CSS Vars

Variabel `--neuro-*` tidak terdefinisi di mana pun. Selalu ganti:

```
--neuro-primary  → var(--apple-blue)
--neuro-warning  → var(--apple-yellow)
--neuro-success  → var(--apple-green)
--neuro-danger   → var(--apple-red)
```

### 11. Interaktivitas: Alpine.js First (Bukan onclick/onsubmit)

```blade
{{-- ❌ DILARANG --}}
<button onclick="doSomething()">
<form onsubmit="return validate()">
<select onchange="location.href=this.value">

{{-- ✅ WAJIB --}}
<button @click="doSomething()">
<form @submit.prevent="validate()">
<select x-data @change="window.location.href = $event.target.value">
```

### 12. Accessibility Minimal

```blade
<button aria-label="Hapus item">
<div role="dialog" aria-modal="true" aria-labelledby="modal-title">
<div role="alert" aria-live="polite">
```

---

## Anti-Patterns Terlarang

```blade
{{-- ❌ Tailwind dark: untuk warna di admin --}}
class="dark:bg-gray-800 dark:text-white dark:border-gray-700"

{{-- ❌ Tailwind opacity/slash modifier --}}
class="bg-blue-500/20 text-yellow-400/80"

{{-- ❌ Tailwind arbitrary values untuk warna --}}
class="bg-[#007AFF] text-[rgba(235,235,245,0.6)]"

{{-- ❌ Neuro legacy vars --}}
style="color:var(--neuro-primary);background:var(--neuro-surface)"

{{-- ❌ Hardcoded hex di style --}}
style="color:#ff4444;background:#1c1c1e"
```
