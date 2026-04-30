# Bizmark Design System — Global Rules

## Source of Truth

Semua komponen UI HARUS menggunakan Blade components dari `resources/views/components/ui/`.
Dilarang keras menulis HTML UI element langsung di Blade view.

## Design Token Hierarchy

```
design-tokens.css (Layer 1: Base)
├── --color-primary: #5B8DBE         (serene blue)
├── --color-secondary: #E8956F       (warm coral)
├── --color-accent: #7CB342          (healing green)
└── ...

design-tokens.css (Layer 2: Semantic)
├── --color-surface
├── --color-text-primary
├── --color-border
└── ...

design-tokens.css (Layer 3: Component)
├── --btn-primary-bg: var(--color-primary)
├── --card-padding: var(--spacing-lg)
└── ...
```

## Strict Rules

### 1. Zero Inline Styles
```blade
<!-- ❌ DILARANG -->
<div style="padding: 1rem; color: #333;">

<!-- ✅ WAJIB -->
<div class="p-4 text-gray-900 dark:text-white">
```

### 2. Zero Inline JavaScript Event Handlers
```blade
<!-- ❌ DILARANG -->
<div onmouseover="showMenu()" onmouseout="hideMenu()">

<!-- ✅ WAJIB - Gunakan Alpine.js -->
<div x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false">
```

### 3. Zero Hardcoded Colors
```blade
<!-- ❌ DILARANG -->
<span class="text-[#ff4444]">
<div class="bg-[#1a1a2e]">

<!-- ✅ WAJIB - Gunakan design tokens -->
<x-ui.badge variant="danger">
<div class="bg-surface-dark">
```

### 4. Always Use Component Library
```blade
<!-- ❌ DILARANG -->
<div class="bg-white rounded-2xl shadow-md p-6">
    <h3>{{ $title }}</h3>
    <p>{{ $value }}</p>
</div>

<!-- ✅ WAJIB -->
<x-ui.stat-card :label="$title" :value="$value" variant="elevated" />
```

### 5. Component Props Must Use Named Parameters
```blade
<!-- ❌ DILARANG -->
<x-ui.button primary large>Submit</x-ui.button>

<!-- ✅ WAJIB -->
<x-ui.button variant="primary" size="lg">Submit</x-ui.button>
```

### 6. Dark Mode Support Wajib
```blade
<!-- WAJIB - Semua komponen harus support dark: prefix -->
<div class="bg-white dark:bg-gray-800 text-gray-900 dark:text-white">
```

### 7. Accessibility Wajib
```blade
<!-- WAJIB -->
<button aria-label="Close" role="button">
<div role="dialog" aria-modal="true" aria-labelledby="modal-title">
```

## Design Tokens Reference

Gunakan semantic tokens berikut — DILARANG menggunakan nilai raw:

| Token | Value | Usage |
|-------|-------|-------|
| `bg-primary` | Primary blue bg | Buttons, links, active states |
| `bg-surface` | Card/container bg | Landing page cards |
| `bg-surface-dark` | Dark card bg | Admin panel cards |
| `text-primary` | Main text color | Body, heading |
| `text-secondary` | Muted text | Labels, descriptions |
| `border-default` | Border color | Card borders, dividers |
