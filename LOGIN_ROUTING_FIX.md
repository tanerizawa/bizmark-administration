# Fix Login Routing - Landing Page ke Portal Klien

## Masalah
Login di landing page mengarah ke login admin panel (`/__REDACTED_LEGACY_ADMIN_SEGMENT__`) padahal seharusnya mengarah ke login portal klien (`/client/login`). Login admin panel adalah internal dan rahasia.

## Solusi

### 1. Struktur Routing yang Benar
```php
// Admin Panel Login (Internal & Rahasia)
Route::get('/__REDACTED_LEGACY_ADMIN_SEGMENT__', [LoginController::class, 'showLoginForm'])->name('login');

// Client Portal Login (Publik)
Route::get('/client/login', [ClientAuthController::class, 'showLoginForm'])->name('client.login');

// Redirect /login ke homepage untuk keamanan
Route::get('/login', function () {
    return redirect('/');
});
```

### 2. File yang Diperbaiki

#### `/resources/views/landing.blade.php`
**Sebelum:**
```html
<li><a href="/__REDACTED_LEGACY_ADMIN_SEGMENT__" class="hover:text-blue-400 transition">Portal Klien</a></li>
```

**Sesudah:**
```html
<li><a href="{{ route('client.login') }}" class="hover:text-blue-400 transition">Portal Klien</a></li>
```

### 3. File yang Sudah Benar
- `/resources/views/landing/partials/navbar.blade.php` ✅
- `/resources/views/landing/partials/mobile-menu.blade.php` ✅

## URL Access Points

### Admin Panel (Internal Staff Only)
- Login: `https://bizmark.id/__REDACTED_LEGACY_ADMIN_SEGMENT__` (hidden path)
- Setelah login: `/dashboard` (admin dashboard)

### Client Portal (Public Access)
- Login: `https://bizmark.id/client/login`
- Register: `https://bizmark.id/client/register`
- Setelah login: `/client/dashboard`

### Public Routes
- Landing: `https://bizmark.id/`
- Blog: `https://bizmark.id/blog`
- Karir: `https://bizmark.id/karir`
- Layanan: `https://bizmark.id/layanan`

## Keamanan
- Path `/__REDACTED_LEGACY_ADMIN_SEGMENT__` tidak dipublikasikan di landing page
- Path `/login` redirect ke homepage untuk menyembunyikan admin login
- Hanya staff internal yang tahu path `/__REDACTED_LEGACY_ADMIN_SEGMENT__`
- Client menggunakan `/client/login` yang jelas dan mudah diakses

## Testing Checklist
- [ ] Akses landing page dan klik link "Portal Klien" di footer → harus ke `/client/login`
- [ ] Klik tombol "Portal Klien" di navbar → harus ke `/client/login`
- [ ] Akses `/__REDACTED_LEGACY_ADMIN_SEGMENT__` → harus masuk ke admin login (untuk internal staff)
- [ ] Akses `/client/login` → harus masuk ke client login
- [ ] Akses `/login` → harus redirect ke landing page

## Status
✅ **SELESAI** - Routing sudah diperbaiki dan aman
