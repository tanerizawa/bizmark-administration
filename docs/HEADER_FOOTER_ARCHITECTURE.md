# Header & Footer Architecture

## 📋 Single Source of Truth

Untuk memastikan konsistensi di seluruh aplikasi, header dan footer menggunakan **partial components** sebagai single source of truth.

## 🏗️ Struktur

### Header (Navbar)
```
resources/views/landing/partials/navbar.blade.php
```

**Digunakan oleh:**
- ✅ Halaman Landing Indonesia (`landing/id/index.blade.php`)
- ✅ Halaman Landing English (`landing/en/index.blade.php`)
- ✅ Halaman Blog (`blog/index.blade.php` via `landing/layout.blade.php`)
- ✅ Semua halaman yang menggunakan `landing/layout.blade.php`

**Fitur:**
- White professional navbar dengan LinkedIn blue branding (`#0A66C2`)
- Responsive dengan mobile menu
- Active state detection untuk blog pages
- Automatic anchor links untuk landing pages
- Locale switcher integration
- Accessibility compliant (ARIA labels, keyboard navigation)

### Mobile Menu
```
resources/views/landing/partials/mobile-menu.blade.php
```

**Fitur:**
- Neuroscience soft gradient background
- Touch-friendly interface
- Locale switcher for mobile
- Contact information
- Smooth animations

### Footer
```
resources/views/landing/partials/footer.blade.php
```

**Digunakan oleh:**
- ✅ Halaman Landing Indonesia
- ✅ Halaman Landing English
- ✅ Halaman Blog
- ✅ Semua halaman yang menggunakan `landing/layout.blade.php`

**Fitur:**
- Dark theme (`bg-gray-900`)
- 4-column grid layout
- Social media links
- Navigation links
- Contact information
- Legal links (Privacy Policy, Terms)
- Locale switcher

## 📝 Cara Penggunaan

### Untuk Halaman Standalone (seperti landing pages)

```blade
<!DOCTYPE html>
<html lang="id">
<head>
    <!-- Your head content -->
    @include('landing.partials.head')
    @include('landing.partials.styles-modern')
</head>
<body class="font-sans antialiased bg-white text-gray-900">

<!-- Skip to main content link for accessibility -->
<a href="#main-content" class="skip-link">Lewati ke konten utama</a>

@include('landing.partials.navbar')
@include('landing.partials.mobile-menu')

<!-- Your content here -->

@include('landing.partials.footer')

@include('landing.partials.scripts')
</body>
</html>
```

### Untuk Halaman yang Menggunakan Layout

```blade
@extends('landing.layout')

@section('content')
    <!-- Your content here -->
@endsection
```

Layout (`landing/layout.blade.php`) sudah include:
- ✅ Navbar
- ✅ Mobile Menu
- ✅ Footer
- ✅ Scripts
- ✅ Skip link

## 🎨 Design System

### Color Variables
```css
--color-primary: #0A66C2;        /* LinkedIn Blue */
--color-primary-dark: #004182;   /* Darker Blue */
--color-primary-light: #378FE9;  /* Lighter Blue */
--text-primary: #000000;         /* Primary Text */
--text-secondary: #666666;       /* Secondary Text */
--surface: #FFFFFF;              /* Background */
```

### Component Classes
- `.nav-link` - Navigation links dengan hover effect
- `.btn` - Base button styles
- `.btn-primary` - Primary action button
- `.btn-secondary` - Secondary button
- `.btn-ghost` - Transparent button
- `.skip-link` - Accessibility skip link

## ⚠️ PENTING: Jangan Edit Inline!

**❌ JANGAN:**
- Edit navbar/footer langsung di file halaman individual
- Copy-paste navbar/footer code
- Buat variant navbar/footer berbeda

**✅ LAKUKAN:**
- Edit file partial di `resources/views/landing/partials/`
- Perubahan akan otomatis tereflect di semua halaman
- Maintain single source of truth

## 🔄 Update Process

Jika perlu mengubah header/footer:

1. Edit file partial yang sesuai:
   - `navbar.blade.php` untuk header
   - `footer.blade.php` untuk footer
   - `mobile-menu.blade.php` untuk mobile menu

2. Clear cache:
   ```bash
   php artisan optimize:clear
   ```

3. Test di semua halaman:
   - Landing ID: https://bizmark.id/
   - Landing EN: https://bizmark.id/en
   - Blog: https://bizmark.id/blog

## 📊 Benefits

✅ **Konsistensi**: Semua halaman memiliki header/footer yang identik
✅ **Maintainability**: Update sekali, berubah di semua halaman
✅ **DRY Principle**: Don't Repeat Yourself
✅ **Faster Development**: Tidak perlu copy-paste code
✅ **Easier Testing**: Satu component untuk di-test
✅ **Better SEO**: Struktur konsisten di seluruh site

## 🏆 Best Practices

1. **Selalu gunakan partial** - Jangan pernah hardcode navbar/footer
2. **Test responsive** - Check mobile dan desktop view
3. **Accessibility first** - Maintain ARIA labels dan keyboard navigation
4. **Clear cache** - Setelah update partial, clear cache Laravel
5. **Check translations** - Pastikan semua i18n keys tersedia

---

**Last Updated:** January 13, 2026  
**Maintained by:** Development Team
