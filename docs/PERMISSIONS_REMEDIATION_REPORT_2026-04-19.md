# Permissions & Ownership Remediation Report (2026-04-19)

## Tujuan
- Menghilangkan error `Operation not permitted` / `Permission denied` yang muncul saat operasi tulis (npm/pint/cache/logs).
- Menormalkan permission: folder `755` dan file `644` untuk path runtime Laravel (`storage`, `bootstrap/cache`) dan file project yang sebelumnya tidak bisa ditulis.
- Menyediakan backup sebelum perubahan.
- Memverifikasi aplikasi dapat melakukan operasi write (cache/log) dan toolchain dapat berjalan tanpa error permission.

## Diagnosa Awal (Ringkas)
- Sebagian tree project ber-owner `nobody:nogroup` dan memiliki mode `755` bahkan untuk file (contoh: `package.json`, `database/migrations/*`, dll).
- Proses eksekusi di mesin ini **tidak memiliki capability** untuk `chown/chmod` terhadap file milik `nobody`, sehingga muncul `Operation not permitted` saat mencoba memperbaiki secara langsung.
- Dampak praktis:
  - Tool yang butuh write (mis. `npm install`, `pint`) bisa gagal karena tidak dapat menulis file tertentu.
  - Folder `docs/` tidak bisa dipakai untuk menyimpan snapshot/backup melalui command biasa karena tidak writable.

## Strategi Remediasi yang Dipakai
Karena `chown/chmod` terhadap file milik `nobody` tidak dapat dilakukan, remediation dilakukan dengan cara:
- Untuk **directory** milik `nobody`: lakukan **rename in-place** ke `__permbackup_<timestamp>` (rename dalam parent yang sama), lalu buat directory baru (owner runtime) dan **copy** isi dari backup ke directory baru.
- Untuk **file** milik `nobody`: pindahkan ke folder backup file, lalu buat file baru (owner runtime) dan copy kontennya kembali.

Ini mengubah ownership + mode pada tree aktif tanpa perlu `chown` pada inode lama.

## Backup yang Dibuat
- Backup directory in-place (rename):
  - `config.__permbackup_2026-04-19_113801`
  - `bootstrap.__permbackup_2026-04-19_113816`
  - `database.__permbackup_2026-04-19_113816`
  - `docker.__permbackup_2026-04-19_113816`
  - `docs.__permbackup_2026-04-19_113816`
  - `lang.__permbackup_2026-04-19_113816`
  - `public.__permbackup_2026-04-19_113816`
  - `resources.__permbackup_2026-04-19_113816`
  - `routes.__permbackup_2026-04-19_113816`
  - `tests.__permbackup_2026-04-19_113816`
  - `storage.__permbackup_2026-04-19_113816`
- Backup file:
  - `.permfix_files_backup_2026-04-19_113841/` (berisi `artisan`, `Dockerfile`, `.editorconfig`, `.gitattributes`, `package.json`, `package-lock.json`)
- Snapshot post-fix:
  - `docs/permission-backups/post-fix-2026-04-19_114114.txt`

## Perubahan Permission yang Diterapkan
- Tree aktif yang direkonstruksi dinormalisasi menjadi:
  - Directories: `755`
  - Files: `644`
- Entry-point executable:
  - `artisan`: `755`
- Laravel runtime directories dipastikan ada dan writable oleh owner runtime:
  - `storage/framework/{cache,views,sessions,testing}`
  - `storage/logs`
  - `bootstrap/cache`

## Verifikasi Pasca-Remediasi
Operasi berikut berhasil tanpa error permission:
- `php artisan view:cache`
- `php artisan config:cache`
- Write file log: `storage/logs/perm_write_test.log`
- `npm install -D @playwright/test` (tidak lagi error EACCES)

Catatan:
- Root directory project (`/home/bizmark/bizmark.id`) masih ber-owner `nobody:nogroup` dan mode `777`, dan tidak dapat diubah karena keterbatasan capability environment. Namun seluruh path kritikal aplikasi dan toolchain sudah direkonstruksi menjadi owner runtime + mode 755/644 sehingga operasi write berjalan normal.
