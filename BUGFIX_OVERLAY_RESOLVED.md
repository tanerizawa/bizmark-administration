# Perbaikan Overlay Issues - Landing Page

**Tanggal:** 14 Oktober 2025  
**Status:** ✅ SELESAI  
**Masalah:** Duplikasi konten dan overlay pada landing page

---

## 🐛 Masalah yang Ditemukan

### 1. Duplikasi Blog Section di `why-choose.blade.php`
**Gejala:**
- Blog section muncul 2 kali (overlay)
- Section blog lama (dark theme) masih ada di dalam file why-choose.blade.php
- Section blog baru (light theme) di file blog.blade.php terpisah

**Penyebab:**
- Blog section lama (237 baris) tidak dihapus dari why-choose.blade.php saat migrasi
- File why-choose.blade.php memiliki 329 baris (seharusnya hanya 89 baris)

**Solusi:**
- ✅ Hapus entire "Latest Articles Section - Magazine Style" dari why-choose.blade.php (line 92-329)
- ✅ Gunakan blog.blade.php yang baru sebagai satu-satunya blog section
- ✅ Pastikan index.blade.php hanya include blog.blade.php sekali

**File yang Diperbaiki:**
- `resources/views/landing/sections/why-choose.blade.php` (329 → 89 baris)

---

### 2. Duplikasi Seluruh Layout di `layout.blade.php`
**Gejala:**
- Semua elemen tampil 2 kali (footer, FAB buttons, navbar)
- HTML source menunjukkan duplikasi literal

**Penyebab:**
- File layout.blade.php memiliki duplikasi kode pada setiap baris
- Format: `<tag>content</tag><tag>content</tag>` (setiap line ditulis 2 kali)
- Total 87 baris dengan content duplikat

**Contoh Masalah:**
```blade
<!DOCTYPE html><!DOCTYPE html>
<html lang="{{ app()->getLocale() }}"><html lang="{{ app()->getLocale() }}">
<body><body class="dark">
@include('landing.partials.navbar')    @include('landing.partials.navbar')
```

**Solusi:**
- ✅ Rewrite seluruh file layout.blade.php dengan struktur bersih
- ✅ Hapus semua duplikasi
- ✅ Pastikan hanya 1 instance dari setiap elemen

**File yang Diperbaiki:**
- `resources/views/landing/layout.blade.php` (87 → 44 baris, clean)

---

## ✅ Hasil Perbaikan

### Layout Bersih (`layout.blade.php`)
```blade
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" class="scroll-smooth">
<head>
    @include('landing.partials.head')
    @include('landing.partials.styles')
</head>
<body>
    @include('landing.partials.navbar')
    @include('landing.partials.mobile-menu')
    @include('landing.partials.search-modal')
    
    <main id="main-content">
        @yield('content')
    </main>
    
    <!-- Floating Action Buttons (1x only) -->
    <div class="fab-group">
        <!-- WhatsApp, Phone, Back to Top -->
    </div>
    
    @include('landing.partials.scripts')
</body>
</html>
```

### Struktur Halaman (`index.blade.php`)
```blade
@extends('landing.layout')

@section('content')
    @include('landing.sections.hero')
    @include('landing.sections.services')
    @include('landing.sections.process')
    @include('landing.sections.blog')        <!-- Blog section (1x only) -->
    @include('landing.sections.why-choose')  <!-- Clean, no blog inside -->
    @include('landing.sections.faq')
    @include('landing.sections.contact')
    @include('landing.sections.footer')
@endsection
```

### File Why Choose Bersih
- **Before:** 329 baris (termasuk blog section lama)
- **After:** 89 baris (hanya 6 feature cards)
- **Removed:** 240 baris blog code duplikat

---

## 🎯 Verifikasi

### Checklist Perbaikan
- ✅ Layout.blade.php: Tidak ada duplikasi HTML
- ✅ Why-choose.blade.php: Hanya 6 feature cards, no blog section
- ✅ Blog.blade.php: Section terpisah dan clean
- ✅ Index.blade.php: Include blog section 1x saja
- ✅ FAB Buttons: Tampil 1x (WhatsApp, Phone, Back to Top)
- ✅ Footer: Tampil 1x
- ✅ Navbar: Tampil 1x

### Test Command
```bash
# Check untuk duplikasi
grep -n "@include.*blog" resources/views/landing/index.blade.php
# Should show: Line 15 only

# Check layout line count
wc -l resources/views/landing/layout.blade.php
# Should show: 44 lines

# Check why-choose line count
wc -l resources/views/landing/sections/why-choose.blade.php
# Should show: 89 lines
```

---

## 📝 Lessons Learned

1. **Always check for old code before adding new sections**
   - Blog section lama masih ada di why-choose.blade.php
   - Harus dihapus saat migrasi ke struktur baru

2. **Verify layout file integrity**
   - Layout.blade.php somehow got duplicated
   - Critical file that affects all pages

3. **Use grep to find duplicates**
   ```bash
   grep -rn "@include.*section" resources/views/landing/
   ```

4. **Test after major refactoring**
   - View source HTML to detect duplications
   - Check line counts before/after

---

## 🚀 Status

**Masalah Overlay:** ✅ SELESAI  
**Blog Section:** ✅ Tampil 1x dengan light theme  
**Layout:** ✅ Bersih, no duplications  
**Ready for:** Production deployment

**Files Modified:**
1. `resources/views/landing/layout.blade.php` (cleaned)
2. `resources/views/landing/sections/why-choose.blade.php` (removed old blog)

**No Breaking Changes:** Semua functionality tetap bekerja, hanya menghapus duplikasi.
