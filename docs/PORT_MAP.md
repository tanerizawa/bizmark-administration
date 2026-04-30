# Port & Service Map — Server Bizmark.ID
> Update: 23 April 2026 — WAJIB dicek sebelum membuka browser preview atau menjalankan server development baru

---

## Peta Domain → Port (Nginx Proxy)

| Domain | Port Backend | Stack | Keterangan |
|--------|-------------|-------|------------|
| `bizmark.id` | php-fpm unix socket | PHP 8.4 FPM | Laravel langsung via FPM, **bukan proxy** |
| `api.bizmark.id` | `localhost:8080` | Docker `bizmark-backend` | Agentsai backend API |
| `beta.bizmark.id` | `localhost:3001` | Docker `bizmark-frontend` | Agentsai frontend Next.js |
| `academos.or.id` | `localhost:3001` | Docker `bizmark-frontend` | Same container, multi-domain |
| `wts.co.id` | `127.0.0.1:3002` | Next.js (host process) | WTS app |
| `areton.id` | `127.0.0.1:3003` | (app lain) | Areton frontend |
| `admin.areton.id` | `127.0.0.1:3005` | (app lain) | Areton admin |
| `api.areton.id` | `127.0.0.1:4000` | (app lain) | Areton API |
| `api-scanner.areton.id` | `127.0.0.1:8091` | (app lain) | Scanner API |
| `hadez.us` | `127.0.0.1:5000` | Gunicorn (Python) | Hadez app |
| `browser.hadez.us` | `127.0.0.1:7000` | Docker `scanner-web` | Scanner web |

---

## Semua Port yang TERPAKAI (JANGAN DIGUNAKAN)

| Port | Status | Dipakai oleh | Akses |
|------|--------|-------------|-------|
| **22** | 🔴 TERPAKAI | SSH | public |
| **25** | 🔴 TERPAKAI | exim4 (mail) | lokal |
| **80** | 🔴 TERPAKAI | nginx | public |
| **443** | 🔴 TERPAKAI | nginx SSL | public |
| **3000** | 🔴 TERPAKAI | `sepetak-waha` (Docker WhatsApp) | `127.0.0.1` only |
| **3001** | 🔴 TERPAKAI | `bizmark-frontend` Docker (Agentsai / academos.or.id / beta.bizmark.id) | public |
| **3002** | 🔴 TERPAKAI | Next.js WTS (wts.co.id) | public via nginx |
| **3003** | 🔴 TERPAKAI | Areton frontend | via nginx |
| **3005** | 🔴 TERPAKAI | Areton admin | via nginx |
| **4000** | 🔴 TERPAKAI | Areton API | via nginx |
| **5000** | 🔴 TERPAKAI | Gunicorn/Python (hadez.us) | public via nginx |
| **5355** | 🔴 TERPAKAI | systemd-resolve mDNS | lokal |
| **5432** | 🔴 TERPAKAI | PostgreSQL host | lokal |
| **5433** | 🔴 TERPAKAI | Docker `wts-db-1` PostgreSQL | public |
| **6379** | 🔴 TERPAKAI | Redis host | lokal |
| **7000** | 🔴 TERPAKAI | Docker `scanner-web` (browser.hadez.us) | public |
| **8080** | 🔴 TERPAKAI | Docker `bizmark-backend` (api.bizmark.id) | public |
| **8088** | 🔴 TERPAKAI | Superset (jika aktif) | cek |
| **8091** | 🔴 TERPAKAI | Scanner API (api-scanner.areton.id) | via nginx |
| **8888** | 🔴 TERPAKAI | Docker `bizmark_searxng` | `127.0.0.1` only |
| **8889** | 🔴 TERPAKAI | Docker `scanner-searxng` | public |
| **9000** | 🔴 TERPAKAI | Docker `bizmark-minio` S3 API | public |
| **9001** | 🔴 TERPAKAI | Docker `bizmark-minio` Console | public |
| **18789** | 🔴 TERPAKAI | openclaw-gateway | lokal |
| **18791** | 🔴 TERPAKAI | nginx upstream | lokal |
| **28813** | 🔴 TERPAKAI | **`php artisan serve` sepetak.org** (PID 404028 + 1010460) | `127.0.0.1` only |
| **43039** | 🔴 TERPAKAI | language_server (Windsurf) | lokal |
| **43471** | 🔴 TERPAKAI | openclaw-gateway | lokal |
| **46487** | 🔴 TERPAKAI | node (Windsurf) | lokal |
| **46525** | 🔴 TERPAKAI | language_server (Windsurf) | lokal |
| **65112** | 🔴 TERPAKAI | node (Windsurf) | lokal |

---

## Port AMAN untuk Development Baru

Port ini belum terpakai (per audit 23 April 2026):

| Port | Rekomendasi |
|------|-------------|
| **8100** | Dev server alternatif #1 |
| **8200** | Dev server alternatif #2 |
| **8300** | Dev server alternatif #3 |
| **9100** | Dev server alternatif #4 |
| **9200** | Dev server alternatif #5 |

---

## Catatan Penting

### bizmark.id Production
- **Production**: nginx → php-fpm socket langsung (`unix:/var/run/php/php8.4-fpm.sock`) — **tidak pakai port**, aman dari konflik
- **Dev server bizmark.id**: TIDAK ADA `artisan serve` yang berjalan — akses dev langsung via FPM/nginx atau jalankan di port aman baru
- Port **28813** terpakai oleh **sepetak.org** (2 proses: PID 404028 + 1010460) — JANGAN dipakai
- Port **3000** terpakai oleh **sepetak-waha** (Docker WhatsApp API sepetak.org)
- **Browser preview Windsurf** (`33091`) adalah Windsurf proxy internal yang meneruskan ke port lain. Konfirmasi selalu ke mana proxy diteruskan sebelum test.

### Windsurf Browser Preview
- Port `33091` adalah proxy Windsurf — selalu cek ke mana ia meneruskan
- Jika preview menunjukkan app lain (sepetak.org, dll), artinya Windsurf salah mendeteksi app yang sedang berjalan
- Selalu gunakan URL langsung (`https://bizmark.id` production, atau port aman baru jika dev server dijalankan) untuk verifikasi

### Cara cek port sebelum pakai
```bash
ss -tlnp | grep :<PORT>
# atau
lsof -i :<PORT>
```
