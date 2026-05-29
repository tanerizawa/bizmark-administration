# Laporan Kemajuan Implementasi — Bizmark.id (2026-04-18)

Dokumen ini merangkum implementasi yang sudah dilakukan berdasarkan rekomendasi audit dan analisis codebase, termasuk prioritas, perubahan yang diterapkan, cakupan testing, tantangan, serta rencana pengembangan selanjutnya.

## 1) Prioritas implementasi (berdasarkan dampak & urgensi)

Urutan prioritas yang dipakai:
1. Keamanan & integritas transaksi (XSS/webhook/payment callback).
2. Otorisasi admin untuk modul sensitif.
3. Konsistensi workflow (state machine) dan idempotency.
4. Stabilitas operasional (scheduler, backup, beban IO).
5. Performa (query, caching, IO per-request).
6. Penyempurnaan produk (notifikasi yang masih TODO, dsb).

## 2) Rekomendasi yang sudah diterapkan

### A. Keamanan & hardening
- Stored XSS inbound email di admin inbox dihilangkan dengan tidak lagi merender HTML mentah.
  - Referensi: [reply.blade.php](file:///home/bizmark/bizmark.id/resources/views/admin/email/inbox/reply.blade.php)
- Webhook email:
  - Logging request sensitif diminimalkan.
  - Ditambahkan deduplikasi berbasis `message_id` agar replay/duplicate tidak memicu error/duplikasi data.
  - Ditambahkan replay protection berbasis timestamp + nonce (middleware).
  - Referensi: [EmailWebhookController.php](file:///home/bizmark/bizmark.id/app/Http/Controllers/EmailWebhookController.php)
  - Middleware: [EnsureEmailWebhookReplayProtection.php](file:///home/bizmark/bizmark.id/app/Http/Middleware/EnsureEmailWebhookReplayProtection.php)
  - Metrics + alerting (hourly report command): [EmailWebhookMetricsReport.php](file:///home/bizmark/bizmark.id/app/Console/Commands/EmailWebhookMetricsReport.php)
  - Dashboard metrics endpoint: [SecurityDashboardController.php](file:///home/bizmark/bizmark.id/app/Http/Controllers/Admin/SecurityDashboardController.php)
- Payment callback Midtrans:
  - Verifikasi signature callback.
  - Idempotency dasar + `lockForUpdate()` untuk mencegah double side-effect saat callback retry.
  - Referensi: [PaymentCallbackController.php](file:///home/bizmark/bizmark.id/app/Http/Controllers/Api/PaymentCallbackController.php)
- Open redirect login ditutup (`//evil.com`).
  - Referensi: [UnifiedLoginController.php](file:///home/bizmark/bizmark.id/app/Http/Controllers/Auth/UnifiedLoginController.php)
- Endpoint internal KBLI refresh/stats diamankan dengan API key internal.
  - Referensi: [EnsureInternalApiKey.php](file:///home/bizmark/bizmark.id/app/Http/Middleware/EnsureInternalApiKey.php), [api.php](file:///home/bizmark/bizmark.id/routes/api.php)
- Candidate portal ditambahkan rate limiting (throttle) untuk menekan brute force token.
  - Referensi: [web.php](file:///home/bizmark/bizmark.id/routes/web.php)
 - Implementasi 2FA (TOTP + backup codes + trust device) untuk admin, dan enforcement untuk aksi berisiko tinggi (settings/finance/security/AI).
   - Referensi: [TwoFactorController.php](file:///home/bizmark/bizmark.id/app/Http/Controllers/Admin/TwoFactorController.php), [EnsureTwoFactorVerified.php](file:///home/bizmark/bizmark.id/app/Http/Middleware/EnsureTwoFactorVerified.php), [two_factor.php](file:///home/bizmark/bizmark.id/config/two_factor.php)

### B. Otorisasi admin modul sensitif
- Route admin payment verification kini wajib permission `finances.manage_payments`.
  - Referensi: [web.php](file:///home/bizmark/bizmark.id/routes/web.php)
- Route admin permit-applications/quotation/document review/notes kini wajib permission `permits.manage`.
  - Referensi: [web.php](file:///home/bizmark/bizmark.id/routes/web.php), [RolesAndPermissionsSeeder.php](file:///home/bizmark/bizmark.id/database/seeders/RolesAndPermissionsSeeder.php)
 - Ditambahkan permission security untuk dashboard keamanan (`security.view`, `security.manage`).
   - Referensi: [RolesAndPermissionsSeeder.php](file:///home/bizmark/bizmark.id/database/seeders/RolesAndPermissionsSeeder.php), [routes/admin/security.php](file:///home/bizmark/bizmark.id/routes/admin/security.php)
 - Audit & hardening admin routes di luar permits/payments (granular per action untuk core modules, finance, content, email, recruitment).
   - Dokumen: [ADMIN_ROUTE_AUDIT.md](file:///home/bizmark/bizmark.id/docs/ADMIN_ROUTE_AUDIT.md)
   - Generator audit: [AdminRoutesAudit.php](file:///home/bizmark/bizmark.id/app/Console/Commands/AdminRoutesAudit.php)

### B2. Audit log admin (immutable log)
- Audit log database untuk perubahan data oleh user admin (`auth:web`) melalui observer Eloquent.
  - Table: [create_admin_audit_logs_table.php](file:///home/bizmark/bizmark.id/database/migrations/2026_04_19_000001_create_admin_audit_logs_table.php)
  - Model: [AdminAuditLog.php](file:///home/bizmark/bizmark.id/app/Models/AdminAuditLog.php)
  - Observer: [AdminAuditObserver.php](file:///home/bizmark/bizmark.id/app/Observers/AdminAuditObserver.php)
  - Registrasi observer: [AppServiceProvider.php](file:///home/bizmark/bizmark.id/app/Providers/AppServiceProvider.php)
  - UI list: [audit-logs.blade.php](file:///home/bizmark/bizmark.id/resources/views/admin/security/audit-logs.blade.php)

### B3. 2FA admin (TOTP + backup codes)
- Migrations:
  - Users: [add_two_factor_to_users_table.php](file:///home/bizmark/bizmark.id/database/migrations/2026_04_19_000002_add_two_factor_to_users_table.php)
  - Trusted devices: [create_two_factor_trusted_devices_table.php](file:///home/bizmark/bizmark.id/database/migrations/2026_04_19_000003_create_two_factor_trusted_devices_table.php)
- Views:
  - Setup: [2fa-setup.blade.php](file:///home/bizmark/bizmark.id/resources/views/admin/security/2fa-setup.blade.php)
  - Challenge: [2fa-challenge.blade.php](file:///home/bizmark/bizmark.id/resources/views/admin/security/2fa-challenge.blade.php)
  - Recovery codes: [2fa-recovery-codes.blade.php](file:///home/bizmark/bizmark.id/resources/views/admin/security/2fa-recovery-codes.blade.php)

### C. Workflow & konsistensi state
- PermitApplication state machine:
  - Ditambahkan service workflow untuk validasi transisi status.
  - Admin update status memakai workflow dan transaksi + lock.
  - Mapping status diperluas untuk `converted_to_project`.
  - Dokumentasi state machine: [PERMIT_APPLICATION_STATE_MACHINE.md](file:///home/bizmark/bizmark.id/docs/PERMIT_APPLICATION_STATE_MACHINE.md)
  - Referensi: [PermitApplicationWorkflowService.php](file:///home/bizmark/bizmark.id/app/Services/PermitApplicationWorkflowService.php), [ApplicationManagementController.php](file:///home/bizmark/bizmark.id/app/Http/Controllers/Admin/ApplicationManagementController.php), [PermitApplication.php](file:///home/bizmark/bizmark.id/app/Models/PermitApplication.php)
- Flow client application:
  - `store()` tidak lagi langsung mengubah status menjadi `submitted` (submission hanya lewat endpoint `submit()` yang mencatat terms acceptance).
  - Referensi: [Client/ApplicationController.php](file:///home/bizmark/bizmark.id/app/Http/Controllers/Client/ApplicationController.php)

### D. Operasional (scheduler/backup) & data protection
- Scheduler dikonsolidasikan ke `routes/console.php` untuk menghindari duplikasi; schedule di Kernel dibuat no-op.
  - Referensi: [console.php](file:///home/bizmark/bizmark.id/routes/console.php), [Kernel.php](file:///home/bizmark/bizmark.id/app/Console/Kernel.php)
- Job chmod rekursif periodik dihapus (mengurangi beban IO).
- Backup script disediakan: [db-backup.sh](file:///home/bizmark/bizmark.id/scripts/db-backup.sh).
- Bukti transfer manual dipindahkan ke storage private dan aksesnya lewat endpoint admin terproteksi.
  - Referensi: [Payment.php](file:///home/bizmark/bizmark.id/app/Models/Payment.php), [PaymentVerificationController.php](file:///home/bizmark/bizmark.id/app/Http/Controllers/Admin/PaymentVerificationController.php)

### E. Performa
- Mobile tasks dibatasi per user dan stats dioptimasi dengan agregasi query tunggal.
  - Referensi: [Mobile/TaskController.php](file:///home/bizmark/bizmark.id/app/Http/Controllers/Mobile/TaskController.php)

## 3) Testing yang sudah dilakukan

### Automated (Unit/Feature/Integration)
- Test suite dijalankan: 673 tests, 1741 assertions (W22: +25 tests).
- **4 pre-existing failures** (AdminDashboardIntegrationTest) — unrelated to new tests.
- W22 Sprint completed: 5 test files, 25 new tests.

#### New Test Files (W22)
| File | Tests | Coverage Area |
|------|-------|--------------|
| [`ClientPortalDashboardTest.php`](/tests/Feature/ClientPortalDashboardTest.php) | +6 | Investment metrics, permit info, contract value, doc download, cross-client auth |
| [`AiDocumentParaphraseTest.php`](/tests/Feature/AiDocumentParaphraseTest.php) | +4 | ParaphraseDocumentJob dispatch, queue config, context handling, exception |
| [`AiAnalysisJobTest.php`](/tests/Feature/AiAnalysisJobTest.php) | +5 | Article meta optimize, unknown task, summary, SEO title, failed() |
| [`GeneratePdfJobTest.php`](/tests/Feature/GeneratePdfJobTest.php) | +4 | DomPDF output, HTML fallback, failed(), exception rethrow |
| [`SeoIntegrationTest.php`](/tests/Feature/SeoIntegrationTest.php) | +6 | Bilingual articles, admin scores/positions/logs pages, guest redirect |

#### Pre-W22 Test Files
- API key internal KBLI: [KbliInternalApiKeyTest.php](file:///home/bizmark/bizmark.id/tests/Feature/KbliInternalApiKeyTest.php)
- Auth admin payment permission: [AdminPaymentAuthorizationTest.php](file:///home/bizmark/bizmark.id/tests/Feature/AdminPaymentAuthorizationTest.php)
- Signature callback Midtrans: [MidtransCallbackSignatureTest.php](file:///home/bizmark/bizmark.id/tests/Feature/MidtransCallbackSignatureTest.php)
- Deduplikasi email webhook: [EmailWebhookDedupTest.php](file:///home/bizmark/bizmark.id/tests/Feature/EmailWebhookDedupTest.php)
- Workflow admin permit: [AdminPermitApplicationWorkflowTest.php](file:///home/bizmark/bizmark.id/tests/Feature/AdminPermitApplicationWorkflowTest.php)
- Throttle candidate portal: [CandidateThrottleTest.php](file:///home/bizmark/bizmark.id/tests/Feature/CandidateThrottleTest.php)
- Replay protection middleware webhook: [EmailWebhookReplayProtectionTest.php](file:///home/bizmark/bizmark.id/tests/Feature/EmailWebhookReplayProtectionTest.php)
- Unit test workflow service: [PermitApplicationWorkflowServiceTest.php](file:///home/bizmark/bizmark.id/tests/Unit/Services/PermitApplicationWorkflowServiceTest.php)
- 2FA enforcement: [AdminTwoFactorEnforcementTest.php](file:///home/bizmark/bizmark.id/tests/Feature/AdminTwoFactorEnforcementTest.php)

### User Acceptance Testing (UAT)
UAT membutuhkan eksekusi manual di staging oleh user bisnis. Checklist UAT ada di bagian 5.

### End-to-End (E2E)
- Scaffold E2E menggunakan Playwright:
  - Config: [playwright.config.ts](file:///home/bizmark/bizmark.id/playwright.config.ts)
  - Tests: [tests/e2e](file:///home/bizmark/bizmark.id/tests/e2e)
  - Data seed E2E: [E2ESeeder.php](file:///home/bizmark/bizmark.id/database/seeders/E2ESeeder.php)
  - CI workflow: [e2e.yml](file:///home/bizmark/bizmark.id/.github/workflows/e2e.yml)

## 4) Tantangan yang ditemui & solusi

- **Konflik/inkonsistensi workflow PermitApplication** (store langsung `submitted` vs submit dengan terms acceptance).
  - Solusi: submission dipusatkan di endpoint `submit()`, sedangkan `store()` selalu membuat draft.
- **Replay/duplicate webhook** berpotensi memicu unique constraint error.
  - Solusi: dedup `message_id` sebelum create.
- **Race & double-processing pada payment callback**
  - Solusi: `lockForUpdate()` + no-op jika payment sudah final.
- **Schema sqlite khusus testing pada tabel projects**
  - Solusi: sesuaikan factory agar kompatibel dengan migrasi yang rebuild tabel pada sqlite.

## 5) Checklist UAT (staging)

### UAT-01 Client Portal (Perizinan)
- Buat draft aplikasi → simpan → edit → upload dokumen → preview submit → submit dengan terms acceptance.
- Pastikan status berubah sesuai: `draft → submitted`.

### UAT-02 Quotation & Payment
- Admin buat quotation → client lihat → accept quotation → initiate payment → (gateway/manual).
- Manual proof upload → admin verify → pastikan status application update dan tidak bisa diverifikasi dua kali.

### UAT-03 Email Management
- Kirim email inbound ke akun aktif → pastikan masuk inbox admin.
- Kirim ulang payload yang sama (message_id sama) → tidak membuat duplikat.
- Pastikan tampilan inbox tidak mengeksekusi HTML berbahaya.

### UAT-04 Recruitment Candidate Portal
- Akses test/interview via token → jalankan flow normal.
- Uji rate limit: request berulang cepat harus menghasilkan 429.

## 6) Rencana pengembangan selanjutnya (next phase)

1. Idempotency lanjutan:
   - Midtrans: normalisasi `gross_amount` canonical + dedup status log per event.
   - Email webhook: tambah replay window (timestamp/nonce) bila worker mendukung.
2. Otorisasi admin menyeluruh:
   - Audit semua route `auth:web` sensitif selain permits/payments.
3. Konsistensi state end-to-end:
   - Terapkan workflow service juga pada payment verify manual & conversion service (agar semua perubahan status lewat satu jalur).
4. Observability:
   - Logging terstruktur untuk payment/webhook + alerting bila terjadi retry tinggi.

---

## 7) Project Cleanup — ✅ COMPLETED (1 Mei 2026)

Mass cleanup of orphaned/unnecessary files, dependencies, and documentation:

### Removed
- **14 backup directories** from April 19 permfix operation (`__permbackup_*`, `__permfix_*`, `.permfix_*`)
- **Deprecated CSS**: `neuroscience-variables.css` (all tokens migrated to `design-tokens.css`)
- **Redundant CSS**: `inquiry-form.css` (Tailwind v4 covers all utilities), removed `@vite()` reference from `service-inquiry/create.blade.php`
- **Stale npm dependencies**: `bootstrap ^5.2.3`, `@popperjs/core ^2.11.6`, `sass ^1.56.1`
- **Orphaned public assets**: `tokens.css`, `tailwind-full.css`, `tailwind.min.css`, `tailwind.min.js`, `tailwind-play.min.js`, `tailwind-browser.js`, `ukl-upl-criteria-helper.html`
- **Superseded plan documents**: 6 files from `plans/`
- **Completed/outdated sprint docs**: W16-W19 from `docs/sprints/`
- **Superseded analysis docs**: 30+ files from `docs/`
- **Archive directory**: `docs/archive/` (9 files)
- **Permission backups**: `docs/permission-backups/`
- **Loadtest**: `loadtest/` directory
- **Test artifacts**: `test-results/` directory

### Modified Files
- `resources/css/app.css` — Removed `@import './neuroscience-variables.css'`
- `resources/views/landing/service-inquiry/create.blade.php` — Removed `@vite('resources/css/inquiry-form.css')`
- `package.json` — Removed 3 stale devDependencies
- `plans/UI_ARCHITECTURE_LONG_TERM_PLAN.md` — Updated Phase 5 to ✅ COMPLETED, updated Files to DELETE, Key Metrics, and Next Actions
