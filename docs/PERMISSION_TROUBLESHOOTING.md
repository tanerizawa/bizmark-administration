# Permission Denied Troubleshooting

Gejala:

- `file_put_contents(.../storage/framework/views/*.php): Permission denied`

Ini hampir selalu berarti user proses web (PHP-FPM) tidak memiliki hak tulis ke direktori compiled views.

## Penyebab Paling Umum

1. Ownership/permission salah setelah deploy (file dibuat oleh user deploy, web user tidak punya akses).
2. Deploy model symlink/atomic release: `storage` tidak di-share atau permission tidak diterapkan ke target symlink.
3. PHP-FPM berjalan sebagai user yang berbeda dari yang diasumsikan (bukan `www-data`).
4. Filesystem read-only / quota penuh.
5. SELinux/AppArmor menolak write meskipun permission terlihat benar.
6. `VIEW_COMPILED_PATH` diarahkan ke lokasi yang tidak writable.

## Langkah Audit di VPS

Jalankan command ini dan simpan output:

```bash
php artisan ops:permissions-check --json || true
```

Lalu cek user PHP-FPM:

```bash
ps aux | egrep 'php-fpm|php8\.' | head
```

Audit permission end-to-end (penting: sampai root path):

```bash
namei -l /home/bizmark/bizmark.id/storage/framework/views
ls -ld /home/bizmark/bizmark.id/storage /home/bizmark/bizmark.id/storage/framework /home/bizmark/bizmark.id/storage/framework/views
```

Jika memakai ACL:

```bash
getfacl -p /home/bizmark/bizmark.id/storage/framework/views | head -n 80
```

Jika SELinux aktif:

```bash
sestatus || true
sudo ausearch -m avc -ts recent | tail -n 50 || true
```

## Perbaikan Standar (Laravel)

Gunakan:

```bash
sudo WEB_USER=www-data WEB_GROUP=www-data bash scripts/fix-laravel-permissions.sh
php artisan ops:permissions-check
```

Jika web user bukan `www-data`, set `WEB_USER` sesuai hasil audit PHP-FPM.

## Deploy Ulang (Fail-Fast)

Gunakan script deploy:

```bash
sudo APP_PATH=/home/bizmark/bizmark.id WEB_USER=www-data WEB_GROUP=www-data bash scripts/deploy-vps.sh
```

