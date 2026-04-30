# Tailwind CSS Usage Rules

## Import Architecture

### WAJIB: Gunakan Tailwind CSS v4 `@import` syntax
```css
/* ✅ WAJIB */
@import 'tailwindcss';
@import './design-tokens.css';
@import '@fortawesome/fontawesome-free/css/fontawesome.css';

/* ❌ DILARANG */
@tailwind base;
@tailwind components;
@tailwind utilities;
```

### WAJIB: Gunakan `@source` untuk purge paths
```css
@source '../../vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php';
@source '../../storage/framework/views/*.php';
@source '../**/*.blade.php';
@source '../**/*.js';
```

### WAJIB: Gunakan `@theme` untuk custom values
```css
@theme {
    --font-sans: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    --color-primary: #5B8DBE;
    --color-primary-dark: #3A5D82;
}
```

## Design Tokens Usage

### Gunakan CSS variables untuk cross-file consistency
```css
/* ✅ WAJIB - Define di design-tokens.css */
:root {
    --color-primary: #5B8DBE;
    --spacing-card: 1.5rem;
}

/* ✅ WAJIB - Referensi di Tailwind @theme */
@theme {
    --color-primary: var(--color-primary);
}

/* ✅ WAJIB - Gunakan di Blade via Tailwind classes */
<div class="bg-primary text-white p-card">
```

### DILARANG: Hardcoded values
```css
/* ❌ DILARANG */
.text-red { color: #ff4444; }
.custom-blue { color: #1a1a2e; }
.btn-red { background: #dc2626; }

/* ✅ WAJIB */
.text-danger { color: var(--color-error); }
.bg-primary-dark { background: var(--color-primary-darker); }
```

## Dark Mode

### WAJIB: Setiap komponen support dark mode
```blade
<div class="bg-white dark:bg-gray-800 
            text-gray-900 dark:text-white
            border border-gray-200 dark:border-gray-700">
```

### Admin Panel: Gunakan `data-theme="dark"` selector
```css
/* admin.css */
[data-theme="dark"] {
    --color-surface: #1C1C1E;
    --color-text-primary: #FFFFFF;
    --color-border: rgba(84, 84, 88, 0.35);
}
```

### Landing Page: Gunakan `prefers-color-scheme`
```css
@media (prefers-color-scheme: dark) {
    :root {
        --color-surface: #1C1C1E;
        --color-text-primary: #E5E5E5;
    }
}
```

## CSS File Organization

### File structure
```
resources/css/
├── design-tokens.css     # [WAJIB] Single source of truth
├── app.css               # [WAJIB] Main entry, imports + @theme
├── admin.css             # [OPSIONAL] Admin-specific overrides only
├── landing.css           # [OPSIONAL] Landing-specific overrides only
├── inquiry-form.css      # [OPSIONAL] Standalone jika diperlukan
```

### DILARANG: Buat component CSS files
```css
/* ❌ DILARANG - Component styling di file CSS terpisah */
resources/css/components/
├── button.css
├── card.css
├── modal.css

/* ✅ WAJIB - Component styling via Tailwind di Blade component saja */
resources/views/components/ui/
├── button.blade.php      <!-- Tailwind classes HERE -->
├── card.blade.php        <!-- Tailwind classes HERE -->
```

## Class Order Convention

Gunakan urutan berikut untuk Tailwind classes:

```blade
<div class="
    <!-- 1. Layout & Display -->
    flex items-center justify-between
    
    <!-- 2. Spacing -->
    p-4 gap-3
    
    <!-- 3. Sizing -->
    w-full h-auto
    
    <!-- 4. Typography -->
    text-sm font-medium text-gray-900
    
    <!-- 5. Background & Borders -->
    bg-white rounded-2xl border border-gray-200
    
    <!-- 6. Shadows -->
    shadow-sm
    
    <!-- 7. Transitions & Animations -->
    transition-all duration-200
    
    <!-- 8. Dark Mode -->
    dark:bg-gray-800 dark:text-white dark:border-gray-700
    
    <!-- 9. Responsive -->
    md:flex-row lg:p-6
    
    <!-- 10. States -->
    hover:shadow-md focus:ring-2 focus:ring-primary
">
```

## Micro-Interactions

### Gunakan Tailwind transition utilities
```blade
<!-- ✅ WAJIB -->
<button class="transition-all duration-200 hover:scale-105 hover:shadow-md">

<!-- ❌ DILARANG - Inline CSS -->
<button style="transition: all 0.2s;">
```

### Gunakan Tailwind animation utilities
```blade
<div class="animate-fade-in">
<div class="animate-slide-up">
```

## Performance Rules

1. **Jangan gunakan `@apply` berlebihan** — lebih baik utility classes langsung
2. **Jangan override Tailwind utilities** — gunakan `@theme` untuk kustomisasi
3. **Gunakan Tailwind CSS v4 `@layer`** untuk custom CSS:
   ```css
   @layer utilities {
       .scrollbar-hide { scrollbar-width: none; }
   }
   ```
4. **Hindari `!important`** — gunakan CSS specificity yang tepat
