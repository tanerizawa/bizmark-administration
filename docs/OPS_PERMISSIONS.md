# Permission & Ownership (Laravel)

Masalah `file_put_contents(.../storage/framework/views/*.php): Permission denied` terjadi saat user proses web (PHP-FPM) tidak bisa menulis ke `storage/framework/views` (compiled Blade view). Laravel juga butuh write access ke `storage/logs`, `storage/framework/cache`, dan `bootstrap/cache`.

Di codebase ini, compiled views diarahkan ke direktori temp (`/tmp/bizmark-framework-views`) secara default lewat `config/view.php` dan bisa dioverride via `VIEW_COMPILED_PATH`.

## Checklist Direktori Wajib Writable

- `storage/framework/views`
- `storage/framework/cache`
- `storage/framework/sessions`
- `storage/logs`
- `bootstrap/cache`

## Perbaikan Cepat (Recommended)

Gunakan script:

```bash
sudo WEB_USER=www-data WEB_GROUP=www-data bash scripts/fix-laravel-permissions.sh
```

Jika path aplikasi bukan di root repo:

```bash
sudo APP_PATH=/var/www/bizmark.id WEB_USER=www-data WEB_GROUP=www-data bash /var/www/bizmark.id/scripts/fix-laravel-permissions.sh
```

## Alternatif Manual (Tanpa ACL)

```bash
sudo chgrp -R www-data storage bootstrap/cache
sudo find storage bootstrap/cache -type d -exec chmod 2775 {} +
sudo find storage bootstrap/cache -type f -exec chmod 0664 {} +
```

## Monitoring & Deteksi Dini

Aplikasi menyediakan command monitoring:

```bash
php artisan ops:permissions-check
php artisan ops:permissions-check --json
```

Command ini:

- Mengecek direktori runtime kritikal.
- Melakukan write-test (membuat file sementara) untuk memastikan `file_put_contents` benar-benar berhasil.
- Mengembalikan exit code non-zero bila ada yang gagal.

Scheduler sudah menjalankan `ops:permissions-check` setiap 10 menit via `routes/console.php`.

Untuk debug cepat tanpa SSH (jika `INTERNAL_API_KEY` sudah diset), tersedia endpoint internal:

```bash
curl -H "X-Internal-Api-Key: <key>" https://bizmark.id/api/internal/ops/permissions
```

## Catatan Keamanan

- Hindari `chmod -R 777`.
- Prioritaskan group + `g+s` + ACL default agar file baru ikut writable untuk web server tanpa membuka akses dunia.
