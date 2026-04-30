# Audit Sistem Bizmark.id (Laravel) — 2026-04-18

Dokumen ini berisi hasil audit statik (berbasis pembacaan kode & konfigurasi repo) untuk memahami arsitektur Bizmark.id, mengidentifikasi bug/risiko, serta menyusun rencana perbaikan dan peningkatan.

## Ruang lingkup & asumsi

- Audit dilakukan pada repo: `/home/bizmark/bizmark.id`.
- Metode: inspeksi kode (routes, controllers, services, middleware, scheduler, konfigurasi, views). Tidak melakukan perubahan fungsional/implementasi pada kode aplikasi, dan tidak menjalankan aplikasi di lingkungan produksi.
- Temuan “bug” dibagi menjadi:
  - **Bug terkonfirmasi secara statik**: kesalahan namespace, file referensi yang tidak ada, potensi fatal error yang jelas.
  - **Risiko/defect potensial**: bergantung pada konfigurasi runtime atau skenario tertentu (mis. integrasi eksternal).

## Ringkasan eksekutif

- Aplikasi adalah monolit Laravel 12 dengan beberapa domain besar dalam 1 codebase: Operasional (admin), Client Portal (permit/quotation/payment/documents), SEO/Content engine (autopost, SEO scheduler), Recruitment, serta tool spasial (RTRW/shape).
- Dua risiko keamanan paling kritikal:
  - **Stored XSS dari inbound email webhook** yang dirender mentah di admin.
  - **Payment callback Midtrans tanpa verifikasi signature eksplisit** (rawan spoofing status pembayaran jika SDK tidak memverifikasi sumber dengan ketat).
- Beberapa bug/defect statik berdampak tinggi:
  - Scheduled task autopost **ter-skip permanen** karena `class_exists` salah namespace.
  - Scheduler memanggil **script backup yang tidak ada** (`scripts/db-backup.sh`).
  - Duplikasi schedule (Kernel + routes/console) + pekerjaan berat `chmod` rekursif tiap 15 menit (berpotensi mengganggu performa server).
  - API memakai middleware `auth:sanctum` tapi paket Sanctum tidak terpasang (akan memicu error bila route tersebut diakses).
- Performa: terdapat beberapa “hotspot” yang bisa langsung terasa di produksi (filesystem I/O berulang, query terlalu lebar, caching menggunakan DB).

## Arsitektur sistem saat ini

### Stack & komponen utama

- Framework: Laravel 12 ([composer.json](file:///home/bizmark/bizmark.id/composer.json#L8-L23)).
- PHP: ^8.2 ([composer.json](file:///home/bizmark/bizmark.id/composer.json#L8-L12)).
- Queue: default `database` ([queue.php](file:///home/bizmark/bizmark.id/config/queue.php#L16-L44)).
- Payment gateway: Midtrans ([config/midtrans.php](file:///home/bizmark/bizmark.id/config/midtrans.php)).
- Web push: `laravel-notification-channels/webpush` ([composer.json](file:///home/bizmark/bizmark.id/composer.json#L12-L16)).
- Tool spasial: `gasparesganga/php-shapefile` + API internal.
- SEO/AI: banyak service internal (OpenRouter, SearXNG, GSC, dsb.) via [services.php](file:///home/bizmark/bizmark.id/config/services.php).

### Bootstrap, routing, dan middleware pipeline

- Entry point: [public/index.php](file:///home/bizmark/bizmark.id/public/index.php) → [bootstrap/app.php](file:///home/bizmark/bizmark.id/bootstrap/app.php).
- Routing didefinisikan via `Application::configure()->withRouting(...)` di [bootstrap/app.php](file:///home/bizmark/bizmark.id/bootstrap/app.php#L8-L18):
  - `routes/web.php`, `routes/api.php`, `routes/console.php`, `routes/mobile.php`, health `/up`.
- Middleware global `web` di-append di [bootstrap/app.php](file:///home/bizmark/bizmark.id/bootstrap/app.php#L19-L28):
  - `RequestId`, `NeuralResponseTime`, `SecurityHeaders`, `DeviceDetection`, `DetectMobile`, `LogReconciliationRequests`.
- Alias middleware (role/permission/locale/email-access) di [bootstrap/app.php](file:///home/bizmark/bizmark.id/bootstrap/app.php#L30-L36).
- CSRF exception: wildcard `webhook/*` di [bootstrap/app.php](file:///home/bizmark/bizmark.id/bootstrap/app.php#L38-L41).

### Domain utama (modul bisnis)

- **Admin/Operasional**: dashboard, projects, tasks, documents, clients, institutions, finance, SEO, autopost, recruitment (routes admin: [routes/web_admin.php](file:///home/bizmark/bizmark.id/routes/web_admin.php)).
- **Client Portal**: register/login, aplikasi perizinan, quotation, pembayaran (Midtrans/manual), dokumen, profil (routes client: [web.php](file:///home/bizmark/bizmark.id/routes/web.php#L247-L342)).
- **SEO & Content Engine**: artikel, topic, autopost schedule, SEO reporting, indexing, syndication (commands di [app/Console/Commands](file:///home/bizmark/bizmark.id/app/Console/Commands)).
- **Recruitment**: job vacancy, pipeline kandidat, test, interview (routes di [web.php](file:///home/bizmark/bizmark.id/routes/web.php#L535-L621)).
- **Spasial / RTRW / Shapefile**: endpoint API untuk shapefile & RTRW proxy (routes: [api.php](file:///home/bizmark/bizmark.id/routes/api.php)).
- **Inbound Email**: webhook inbound email (Cloudflare worker) → inbox internal (controller: [EmailWebhookController.php](file:///home/bizmark/bizmark.id/app/Http/Controllers/EmailWebhookController.php)).

### Auth & akses kontrol

- Multi-guard:
  - `web` (admin/user) dan `client` (portal client) di [auth.php](file:///home/bizmark/bizmark.id/config/auth.php#L38-L77).
- Unified login: [UnifiedLoginController.php](file:///home/bizmark/bizmark.id/app/Http/Controllers/Auth/UnifiedLoginController.php).
- Role/permission: middleware custom `CheckRole`, `CheckPermission` (lihat folder [Middleware](file:///home/bizmark/bizmark.id/app/Http/Middleware)).

### Background processing

- Scheduler utama di [Kernel.php](file:///home/bizmark/bizmark.id/app/Console/Kernel.php) (SEO, autopost, backup/monitoring).
- Jobs queue di [app/Jobs](file:///home/bizmark/bizmark.id/app/Jobs) (AI/document/analysis).
- Event/listener untuk distribusi konten & social posting (folder [Events](file:///home/bizmark/bizmark.id/app/Events) dan [Listeners](file:///home/bizmark/bizmark.id/app/Listeners)).

## Daftar bug & risiko (dengan prioritas)

Prioritas:
- **P0**: security/data integrity, fatal error, payment/accounting, atau sistem inti tidak berjalan.
- **P1**: mengganggu operasional utama, performa signifikan, bug sering muncul.
- **P2**: edge case, maintainability, debt yang cepat memburuk.
- **P3**: kosmetik/cleanup.

### P0 — wajib ditangani segera

1) **Stored XSS dari inbound email webhook (admin RCE di browser)**
- Dampak: takeover session admin, aksi admin tanpa izin, kebocoran data.
- Lokasi:
  - Penyimpanan HTML mentah: [EmailWebhookController.php](file:///home/bizmark/bizmark.id/app/Http/Controllers/EmailWebhookController.php#L86-L118)
  - Rendering `{!! !!}`: [reply.blade.php](file:///home/bizmark/bizmark.id/resources/views/admin/email/inbox/reply.blade.php#L49-L56)
- Akar masalah: input pihak eksternal (email HTML) dirender tanpa sanitasi.

2) **Midtrans callback tanpa verifikasi signature eksplisit**
- Dampak: spoofing callback → status pembayaran salah (success tanpa bayar).
- Lokasi: route [web.php](file:///home/bizmark/bizmark.id/routes/web.php#L516-L519), handler [PaymentCallbackController.php](file:///home/bizmark/bizmark.id/app/Http/Controllers/Api/PaymentCallbackController.php#L24-L91).

3) **Scheduled task autopost “overdue” ter-skip permanen (namespace salah)**
- Dampak: backlog schedule tidak diproses; autopost engine tidak stabil.
- Lokasi:
  - Pengecekan salah: [Kernel.php](file:///home/bizmark/bizmark.id/app/Console/Kernel.php#L58-L65)
  - Namespace benar: [ProcessOverdueSchedules.php](file:///home/bizmark/bizmark.id/app/Console/Commands/AutoPost/ProcessOverdueSchedules.php#L3-L10)

4) **Endpoint API memakai `auth:sanctum` tapi Sanctum tidak terpasang**
- Dampak: akses endpoint menyebabkan error middleware/guard (runtime failure).
- Lokasi: [api.php](file:///home/bizmark/bizmark.id/routes/api.php#L28-L32), dependensi: [composer.json](file:///home/bizmark/bizmark.id/composer.json#L8-L23).

### P1 — penting, berdampak besar pada performa/operasional

5) **Scheduler memanggil `scripts/db-backup.sh` yang tidak ada**
- Dampak: backup terjadwal gagal; false sense of safety; alarm/log noise.
- Lokasi panggilan: [Kernel.php](file:///home/bizmark/bizmark.id/app/Console/Kernel.php#L178-L205). Direktori scripts: [scripts](file:///home/bizmark/bizmark.id/scripts).

6) **Duplikasi scheduler di Kernel dan routes/console (risiko eksekusi dobel)**
- Dampak: double-run, konflik, beban dobel, side effect tak terprediksi.
- Lokasi: [Kernel.php](file:///home/bizmark/bizmark.id/app/Console/Kernel.php) dan [console.php](file:///home/bizmark/bizmark.id/routes/console.php).

7) **Pekerjaan berat: recursive chmod tiap 15 menit**
- Dampak: IO disk tinggi, latency meningkat, mengganggu queue worker.
- Lokasi: [Kernel.php](file:///home/bizmark/bizmark.id/app/Console/Kernel.php#L25-L56), duplikat di [console.php](file:///home/bizmark/bizmark.id/routes/console.php#L58-L92).

8) **Query mobile tasks berpotensi mengambil semua task (tanpa filter user pada list)**
- Dampak: beban DB besar, kebocoran data lintas user (jika view tidak menyaring).
- Lokasi: [Mobile/TaskController.php](file:///home/bizmark/bizmark.id/app/Http/Controllers/Mobile/TaskController.php#L17-L50).

9) **Open redirect pada unified login (path “//evil.com” dianggap aman)**
- Dampak: phishing/credential theft, reputasi buruk.
- Lokasi: [UnifiedLoginController.php](file:///home/bizmark/bizmark.id/app/Http/Controllers/Auth/UnifiedLoginController.php#L29-L36) + [isSafeRedirect](file:///home/bizmark/bizmark.id/app/Http/Controllers/Auth/UnifiedLoginController.php#L119-L133).

10) **Upload bukti transfer disimpan ke disk public**
- Dampak: kebocoran dokumen sensitif bila URL tertebak/terekspos.
- Lokasi: [PaymentController.php](file:///home/bizmark/bizmark.id/app/Http/Controllers/Client/PaymentController.php#L176-L233).

### P2 — perbaiki untuk stabilitas & maintainability

11) **Potensi null deref pada notifikasi email assignment**
- Dampak: webhook email gagal (500) jika ada assignment orphan.
- Lokasi: [EmailWebhookController.php](file:///home/bizmark/bizmark.id/app/Http/Controllers/EmailWebhookController.php#L229-L240).

12) **Potensi N+1 di command autopost overdue**
- Dampak: banyak query saat backlog besar.
- Lokasi: [ProcessOverdueSchedules.php](file:///home/bizmark/bizmark.id/app/Console/Commands/AutoPost/ProcessOverdueSchedules.php#L35-L46), akses relasi [ProcessOverdueSchedules.php](file:///home/bizmark/bizmark.id/app/Console/Commands/AutoPost/ProcessOverdueSchedules.php#L60-L65).

13) **Filesystem I/O per artikel di blog listing (`Storage::exists` di accessor)**
- Dampak: latensi tinggi pada listing blog.
- Lokasi: [Article.php](file:///home/bizmark/bizmark.id/app/Models/Article.php#L171-L186) + pemakaian di [blog/index.blade.php](file:///home/bizmark/bizmark.id/resources/views/blog/index.blade.php#L74-L83).

14) **Logging request penuh/trace di webhook email**
- Dampak: PII exposure di log; biaya storage log tinggi.
- Lokasi: [EmailWebhookController.php](file:///home/bizmark/bizmark.id/app/Http/Controllers/EmailWebhookController.php#L41-L45), [EmailWebhookController.php](file:///home/bizmark/bizmark.id/app/Http/Controllers/EmailWebhookController.php#L158-L169).

15) **Callback Midtrans mencetak trace error penuh**
- Dampak: info disclosure di log.
- Lokasi: [PaymentCallbackController.php](file:///home/bizmark/bizmark.id/app/Http/Controllers/Api/PaymentCallbackController.php#L82-L90).

## Rencana perbaikan terperinci (per item prioritas)

### P0-1: Mitigasi XSS email webhook

- Implementasi:
  - Sanitasi `html` sebelum disimpan atau sebelum dirender (allowlist tag/atribut).
  - Ganti rendering raw menjadi output yang aman (minimal: render sanitized HTML).
  - Audit semua penggunaan `{!! !!}` untuk memastikan hanya untuk data trusted.
- Pengujian:
  - Buat payload HTML berbahaya (script/onerror) → pastikan tidak dieksekusi di admin.
  - Pastikan formatting email “normal” tetap terbaca.
- Output:
  - Tidak ada eksekusi JS dari konten inbound email.

### P0-2: Hardening Midtrans callback

- Implementasi:
  - Pindahkan callback ke `routes/api.php` (stateless) atau pastikan CSRF tidak menjadi blocker tanpa melebar ke wildcard.
  - Tambahkan verifikasi signature/token sesuai spesifikasi Midtrans sebelum memproses status.
  - Tambahkan idempotency (jika callback berulang) dan rate limiting khusus endpoint callback.
- Pengujian:
  - Simulasi callback invalid signature → 401/403.
  - Callback valid signature → status payment berubah sesuai state machine.
  - Callback duplikat → tidak mengubah state secara salah, tidak double side-effect.

### P0-3: Repair scheduler autopost overdue (namespace)

- Implementasi:
  - Perbaiki reference `class_exists` sesuai namespace sebenarnya, atau hilangkan guard tersebut bila tidak perlu.
- Pengujian:
  - Jalankan scheduler/command di env dev → pastikan schedule dieksekusi.

### P0-4: Perbaiki autentikasi API `auth:sanctum`

Opsi yang valid:
- Tambah Sanctum sebagai dependency + konfigurasi lengkap, atau
- Hapus/ubah middleware endpoint ke mekanisme auth yang memang dipakai (mis. session/admin-only), atau
- Jadikan endpoint internal (protect via secret header / IP allowlist) bila cocok untuk use-case.

### P1: Backup script & konsolidasi scheduler

- Implementasi:
  - Pastikan backup dijalankan via command artisan yang ada (mis. `DatabaseBackup`) atau sediakan script `scripts/db-backup.sh` yang benar.
  - Pilih satu sumber schedule: **Kernel** (rekomendasi) dan minimalkan definisi schedule di `routes/console.php`.
- Pengujian:
  - Dry run backup di staging; verifikasi output artefak + retensi.
  - Verifikasi tidak ada job dobel ketika cron menjalankan scheduler.

### P1: Matikan/ubah “chmod rekursif” periodik

- Implementasi:
  - Pindahkan ke provisioning/deploy step.
  - Jika tetap diperlukan, batasi scope dan frekuensi + conditional check.
- Pengujian:
  - Monitoring I/O dan waktu eksekusi scheduler sebelum/after.

### P1: Mobile tasks query

- Implementasi:
  - Pastikan list tasks terfilter sesuai user/team dan kebijakan akses.
  - Tambahkan pagination dan index DB bila perlu.
- Pengujian:
  - User A tidak dapat melihat tasks user B.

### P1: Open redirect unified login

- Implementasi:
  - Perketat validasi redirect: tolak `//`, tolak scheme (`http:`), dan hanya izinkan path relatif.
- Pengujian:
  - `redirect=//evil.com` ditolak.
  - Redirect internal valid tetap berjalan.

### P1: Penyimpanan bukti transfer

- Implementasi:
  - Pindahkan storage ke disk private; sediakan endpoint download terproteksi.
  - Simpan metadata original name; file name random.
- Pengujian:
  - File tidak bisa diakses tanpa auth.

## Rekomendasi peningkatan performa

- Cache store: ganti dari database ke Redis untuk traffic tinggi (lihat [cache.php](file:///home/bizmark/bizmark.id/config/cache.php#L18-L78)).
- Kurangi overhead middleware `NeuralResponseTime` (terutama jika cache=database) (lihat [NeuralResponseTime.php](file:///home/bizmark/bizmark.id/app/Http/Middleware/NeuralResponseTime.php#L236-L289)).
- Hilangkan `Storage::exists` per item pada listing blog (lihat [Article.php](file:///home/bizmark/bizmark.id/app/Models/Article.php#L171-L186)).
- Pindahkan agregasi dashboard dari “group di PHP” ke query agregasi SQL (contoh: [DashboardOperationalService.php](file:///home/bizmark/bizmark.id/app/Services/Dashboard/DashboardOperationalService.php#L76-L113)).

## Rekomendasi peningkatan keamanan

- Sanitasi HTML untuk semua input eksternal yang dirender di admin (email, content syndication, dsb.).
- Hardening callback eksternal (Midtrans, webhook lain) dengan signature verification + replay protection + rate limit.
- Minimalkan logging data sensitif (hindari `request->all()` dan `trace` di production).
- Review CSP di [SecurityHeaders.php](file:///home/bizmark/bizmark.id/app/Http/Middleware/SecurityHeaders.php#L35-L54): kurangi `unsafe-inline`/`unsafe-eval` jika memungkinkan; minimalkan domain allowlist; gunakan nonce/hashed scripts.
- Pindahkan semua upload sensitif dari disk `public` ke `private` dan akses via controller + authorization.

## Roadmap implementasi (estimasi waktu & resource)

Estimasi di bawah adalah baseline untuk tim kecil; dapat berubah tergantung ketersediaan staging, coverage test, dan kompleksitas data produksi.

### Fase 0 — Stabilization & Security Hotfix (± 3–7 hari kerja)

- Resource: 1 backend engineer + 1 QA (paruh waktu) + 1 DevOps (paruh waktu).
- Output:
  - Patch XSS email.
  - Signature verification Midtrans callback + endpoint dipindah/di-hardening.
  - Fix namespace scheduler autopost.
  - Putuskan solusi untuk `auth:sanctum` (install atau ganti).

### Fase 1 — Operasional & Performa (± 1–2 minggu)

- Resource: 1–2 backend engineer + 1 QA.
- Output:
  - Konsolidasi scheduler (Kernel vs routes/console).
  - Hapus/ubah chmod rekursif periodik.
  - Optimasi hotspot (mobile tasks filter + pagination, blog image accessor, dashboard aggregations).
  - Pindahkan upload bukti transfer ke private storage + secure download.

### Fase 2 — Hardening & Maintainability (± 2–4 minggu)

- Resource: 2 backend engineer + QA + ops.
- Output:
  - Tambah test coverage (feature test untuk payment, webhook, auth multi-guard).
  - Standardisasi pattern: validasi request (FormRequest), service boundaries, error handling.
  - Observability: logging yang terstruktur + metric untuk scheduler/queue.

### Fase 3 — Evolusi Produk (berjalan)

- Resource: sesuai prioritas bisnis.
- Output:
  - Modularisasi routes/feature flags, pemecahan domain (mis. SEO engine) jika perlu.
  - Redis + queue worker tuning + caching strategy end-to-end.

## Langkah audit lanjutan (untuk menemukan bug yang tidak tampak secara statik)

- Jalankan test suite (feature/integration) untuk flow kritikal: login admin/client, create application→quotation→payment, callback Midtrans, upload dokumen, inbound email.
- Tambahkan static analysis dan baseline: PHPStan/Psalm, Laravel Pint, serta aturan khusus untuk `{!! !!}` dan penggunaan `Storage::disk('public')` pada data sensitif.
- Aktifkan profiling/observability di staging: slow query log, queue latency, scheduler runtime, error rate per endpoint.
- Audit konfigurasi produksi: storage permissions, cron scheduler, worker queue concurrency, cache driver (Redis), rate limiting store, dan rotasi log.
