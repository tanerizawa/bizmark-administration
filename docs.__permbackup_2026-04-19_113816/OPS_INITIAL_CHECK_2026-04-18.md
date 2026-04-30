# OPS Initial Check — 2026-04-18

## Ringkasan Status

- Host OS: Debian 13 (trixie), kernel 6.12.x
- Disk root: 197G total / 132G used (70%)
- Memory: 15Gi total / ~7Gi used / ~8.6Gi available
- Konektivitas: DNS & HTTPS keluar (contoh github.com) OK

## Layanan & Port yang Terlihat

- Nginx: listen 80/443
- PHP-FPM: php8.4-fpm (socket: /var/run/php/php8.4-fpm.sock)
- PostgreSQL: listen 127.0.0.1:5432
- Redis: listen 127.0.0.1:6379
- Docker daemon aktif (beberapa container kasm/*, searxng, postgres:16)
- PM2 berjalan (mengelola beberapa app areton/academos)

## Aplikasi bizmark.id

- `GET https://bizmark.id/` (local probe via Host header) → 200
- `GET https://bizmark.id/health` → 200 setelah penambahan endpoint health di aplikasi
- Test suite repo: `./vendor/bin/phpunit` → OK (74 tests, 378 assertions)

## Temuan Kritis (Butuh Perbaikan di Level Sistem)

### 1) `nginx -t` gagal karena include vhost yang hilang

Gejala:

- `nginx -t` mengembalikan error karena file include hilang:
  - `open() "/etc/nginx/sites-enabled/sepetak.org" failed (2: No such file or directory) in /etc/nginx/nginx.conf:66`

Analisis singkat:

- Di `/etc/nginx/nginx.conf` ada `include /etc/nginx/sites-enabled/*;`
- Ada symlink `/etc/nginx/sites-enabled/sepetak.org` yang menunjuk ke `/etc/nginx/sites-available/sepetak.org`, namun targetnya tidak ada.
- Nginx masih berjalan (konfigurasi sudah ter-load sebelumnya), tapi reload/restart berisiko gagal sampai error ini dibetulkan.

Remediasi (jalankan di VPS dengan akses root sebenarnya):

1. Identifikasi broken symlink:
   - `ls -la /etc/nginx/sites-enabled | grep sepetak.org`
2. Opsi A (paling aman): restore file `/etc/nginx/sites-available/sepetak.org` sesuai konfigurasi yang benar.
3. Opsi B: jika domain sudah tidak dipakai, hapus symlink yang rusak:
   - `rm /etc/nginx/sites-enabled/sepetak.org`
4. Verifikasi & reload:
   - `nginx -t`
   - `nginx -s reload` (atau `systemctl reload nginx`)

Catatan:

- Di environment eksekusi ini, filesystem root dipasang read-only sehingga file `/etc/nginx/*` tidak dapat dimodifikasi dari sini.

### 2) `api.bizmark.id` endpoint health mengembalikan 502

Gejala:

- `GET https://api.bizmark.id/health` (local probe via Host header) → 502

Analisis singkat:

- Vhost `/etc/nginx/sites-available/api.bizmark.id` mem-proxy endpoint `/health` dan `/api/*` ke `http://127.0.0.1:8000/...`
- Pada saat pemeriksaan, tidak ada proses yang listen di port 8000 (`curl http://127.0.0.1:8000/health` gagal connect).

Remediasi (jalankan di VPS):

1. Identifikasi service backend yang seharusnya listen di 8000 (systemd/pm2/docker):
   - `ss -lntup | grep ':8000' || true`
   - `ps aux | grep -E '(uvicorn|gunicorn|php artisan serve|node|nest|fastapi)' | head`
   - `docker ps --format 'table {{.Names}}\t{{.Ports}}\t{{.Status}}'`
   - `pm2 ls`
2. Start/restart service backend tersebut, lalu verifikasi:
   - `curl -sS -D - http://127.0.0.1:8000/health | head`
   - `curl -k -sS -D - -H 'Host: api.bizmark.id' https://127.0.0.1/health | head`

## Perubahan Aplikasi (Repo)

- Menambahkan endpoint health: `GET /health` → `{"status":"ok"}`

