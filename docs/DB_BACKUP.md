# Database Backup

## Perintah

- Jalankan backup via Artisan:

```bash
php artisan db:backup full --verify
```

## Output

- Default lokasi backup: `storage/app/backups`
- Format file: `<type>-<YYYYmmdd-HHMMSS>.sql` untuk MySQL/MariaDB, atau `<type>-<YYYYmmdd-HHMMSS>.sqlite` untuk SQLite.

## Lokasi Aman

- Untuk produksi, rekomendasi simpan di luar web root:

```bash
BACKUP_DIR=/var/backups/bizmark php artisan db:backup full --verify
```

## Catatan Operasional

- Pastikan user yang menjalankan backup memiliki akses tulis ke `BACKUP_DIR`.
- Pastikan `mysqldump` tersedia untuk koneksi MySQL/MariaDB.
- Setelah backup dibuat, salin ke lokasi terpisah (offsite) sebelum melakukan migrasi/patch aplikasi.

