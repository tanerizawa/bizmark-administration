# Roo Code Rules — Bizmark Design System

## Rule Files Index

| # | File | Purpose | Applied When |
|---|------|---------|-------------|
| 1 | [`global-design-system.md`](/roo/rules/global-design-system.md) | Global UI/UX principles: zero inline styles, zero hardcoded colors, zero inline JS, component library mandate, dark mode, accessibility | **All modes** — Setiap perubahan UI |
| 2 | [`blade-component-api.md`](/roo/rules/blade-component-api.md) | Component API design: named parameters, slot system, attributes forwarding, Alpine.js integration, testing checklist | **Code mode** — Saat membuat/mengedit Blade components |
| 3 | [`tailwind-css-usage.md`](/roo/rules/tailwind-css-usage.md) | CSS architecture: Tailwind v4 syntax, design tokens, dark mode selectors, class order, micro-interactions, performance | **All modes** — Saat menulis CSS atau Tailwind classes |
| 4 | [`alpine-js-patterns.md`](/roo/rules/alpine-js-patterns.md) | Alpine.js interaction patterns: zero inline JS, component data, $dispatch, common patterns (modal, tabs, dropdown, toast, form) | **All modes** — Saat menulis interaktivitas frontend |
| 5 | [`livewire-integration.md`](/roo/rules/livewire-integration.md) | Livewire + Blade component patterns: wire:click, wire:model, loading states, $dispatch toast, modal/form/table integration, accessibility | **All modes** — Saat membuat Livewire components atau menulis interaksi backend-driven |

## How to Use

1. **Pertama baca** `global-design-system.md` — ini adalah aturan paling fundamental
2. **Saat membuat component baru** — ikuti `blade-component-api.md`
3. **Saat menulis CSS atau styling** — ikuti `tailwind-css-usage.md`
4. **Saat menambah interaktivitas** — ikuti `alpine-js-patterns.md`
5. **Saat menggunakan Livewire** — ikuti `livewire-integration.md` untuk pattern wire:click, wire:model, loading states, dan integrasi modal/form/table

## Enforcement

Rules ini di-inject ke system prompt setiap kali Roo Code aktif. Jika ada kode yang melanggar aturan:

```blade
<!-- ❌ Akan ditolak oleh Roo Code -->
<div style="padding: 1rem;">
<button onclick="doSomething()">
<span style="color: #ff4444;">

<!-- ✅ Yang akan dihasilkan sebagai gantinya -->
<div class="p-4">
<button @click="doSomething()">
<x-ui.badge variant="danger">
```
