# Roo Code Rules — Bizmark Design System
> **Last Updated:** 2026-05-03 | Stack: Tailwind CSS v4 · Alpine.js v3.15.1 · Font Awesome v7.1.0 · Chart.js v4.4 · Vite v7

---

## Rule Files Index

| # | File | Purpose | Applied When |
|---|------|---------|-------------|
| 1 | [`global-design-system.md`](global-design-system.md) | **BACA PERTAMA.** Aturan global: admin=inline style+CSS vars, landing=Tailwind utilities, dark mode dua konteks, anti-patterns terlarang | **Semua mode** |
| 2 | [`tailwind-css-usage.md`](tailwind-css-usage.md) | Tailwind v4 setup (via @tailwindcss/vite), `@import`, `@theme`, `@source`, perbedaan admin vs landing | **Saat menulis CSS atau Tailwind classes** |
| 3 | [`alpine-js-patterns.md`](alpine-js-patterns.md) | Alpine.js v3.15.1: directives, magic properties, Alpine.store, Alpine.data, common patterns (modal, tabs, toast, loading, confirm delete) | **Saat menulis interaktivitas frontend** |
| 4 | [`blade-component-api.md`](blade-component-api.md) | Blade component API: kapan pakai `x-ui.*` vs direct HTML, named props, slot system, `$attributes->merge()`, dark theme di components | **Saat membuat/mengedit Blade components** |
| 5 | [`livewire-integration.md`](livewire-integration.md) | Livewire + Alpine: `wire:*`, `$wire`, loading states, `$wire.entangle()`, `$dispatch` toast, table pattern | **Saat menggunakan Livewire** |

---

## Ringkasan Aturan Paling Kritis

### Admin Panel
```
✅ style="background:var(--dark-bg-secondary);color:var(--dark-text-primary)"
✅ color-mix(in srgb, var(--apple-blue) 15%, transparent)
✅ Tailwind hanya untuk: flex grid gap-* space-* overflow-* p-* rounded-*
❌ dark:bg-gray-800   dark:text-white   bg-blue-500/20   text-yellow-400
❌ var(--neuro-*)     style="color:#hex"
```

### Library Loading (Vite/npm — bukan CDN)
```
Alpine.js   → import Alpine from 'alpinejs'         (resources/js/app.js)
FA v7       → @import '@fortawesome/...'            (resources/css/admin.css)
Chart.js    → import { Chart } from 'chart.js'      (resources/js/charts.js)
```

### Versi Library Aktif
| Library | Versi |
|---------|-------|
| Tailwind CSS | v4.x (via @tailwindcss/vite) |
| Alpine.js | v3.15.1 |
| @alpinejs/collapse | v3.15.1 |
| Font Awesome Free | v7.1.0 |
| Chart.js | v4.4.x |
| Vite | v7.x |
| Laravel | v11 |

---

## How to Use

1. **Pertama baca** `global-design-system.md` — aturan paling fundamental, ada dua konteks (admin vs landing)
2. **Styling di admin** → inline `style=""` + CSS vars, Tailwind hanya layout
3. **Styling di landing** → Tailwind utilities + `dark:` prefix + semantic tokens
4. **Membuat component** → ikuti `blade-component-api.md`
5. **Interaktivitas** → ikuti `alpine-js-patterns.md`
6. **Livewire** → ikuti `livewire-integration.md`

---

## Enforcement

Rules ini di-inject ke context setiap kali Roo Code atau AI agent aktif.

```blade
{{-- ❌ Akan ditolak --}}
<div style="padding: 1rem; color: #333;">
<div class="dark:bg-gray-800 bg-yellow-500/20">
<button onclick="doSomething()">
style="color:var(--neuro-primary)"

{{-- ✅ Yang benar --}}
<div style="padding:16px;color:var(--dark-text-primary)">
<div style="background:color-mix(in srgb,var(--apple-yellow) 15%,transparent)">
<button @click="doSomething()">
style="color:var(--apple-blue)"
```
