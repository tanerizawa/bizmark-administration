# Analisis Codebase Bizmark.id — Business Use Case (BU), Unfinished Tasks, Edge Case, dan Test Plan (2026-04-18)

Dokumen ini menginventarisasi Business Use Case (BU) yang tercermin di codebase, mengidentifikasi task yang belum terdokumentasi/unfinished, dan memetakan potensi error serta edge case. Di bagian akhir terdapat rencana testing komprehensif (unit, integration, end-to-end) mencakup positive flow, negative flow, dan boundary conditions.

## 1) Ringkasan scope & metodologi

- Scope: seluruh repo Laravel di `/home/bizmark/bizmark.id` (routes, controllers, models, middleware, services, jobs, commands, views, config, tests).
- Metode:
  - Pemetaan BU dari entrypoint routing dan controller/view yang dipanggil.
  - Pencarian unfinished task dari TODO/FIXME/DEPRECATED/stub/log-only endpoints/commands.
  - Audit edge case dari pola umum kegagalan: otorisasi, null-relations, idempotency, state machine, concurrency, replay, upload/storage.
  - Review test suite yang sudah ada untuk mengukur coverage & gap.

## 2) Katalog Business Use Case (BU)

Format ringkas per BU:
- **Aktor**: peran utama (public, client, admin, candidate).
- **Tujuan**: outcome bisnis yang ingin dicapai.
- **Entrypoint**: rute & controller utama.
- **Data inti**: model/entitas yang dominan.
- **Dependensi eksternal**: integrasi/layanan luar.

### BU-01 — Marketing Site & SEO (Public)
- Aktor: pengunjung publik.
- Tujuan: akuisisi lead, traffic SEO, konsumsi konten.
- Entrypoint:
  - Sitemap/robots: [routes/web.php](file:///home/bizmark/bizmark.id/routes/web.php)
  - Blog/article: [PublicArticleController](file:///home/bizmark/bizmark.id/app/Http/Controllers/PublicArticleController.php)
  - Programmatic SEO: [ProgrammaticSeoController](file:///home/bizmark/bizmark.id/app/Http/Controllers/ProgrammaticSeoController.php)
- Data inti: `Article`, `ArticleTopic`, `SeoReport`, `SeoScore`, `TrendingTopic`, dsb. (lihat [app/Models](file:///home/bizmark/bizmark.id/app/Models)).
- Dependensi eksternal: Google Analytics/Tag Manager (CSP), IndexNow, Search Console, SearXNG, OpenRouter (lihat [config/services.php](file:///home/bizmark/bizmark.id/config/services.php)).

### BU-02 — Admin Operasional (Backoffice)
- Aktor: admin/user internal (guard `web`).
- Tujuan: mengelola proyek, task, dokumen, klien, institusi, operasional harian.
- Entrypoint:
  - Admin mount: [routes/web.php](file:///home/bizmark/bizmark.id/routes/web.php) → [routes/web_admin.php](file:///home/bizmark/bizmark.id/routes/web_admin.php).
  - Core admin routes: [routes/admin/core.php](file:///home/bizmark/bizmark.id/routes/admin/core.php).
- Data inti: `Project`, `Task`, `Document`, `Client`, `Institution`, `Permission`, `Role`.
- Dependensi eksternal: opsional (export Excel/PDF, webpush).

### BU-03 — Client Portal (Perizinan end-to-end)
- Aktor: klien (guard `client`).
- Tujuan: registrasi/login, ajukan aplikasi perizinan, lihat quotation, lakukan pembayaran, unggah dokumen, monitor status.
- Entrypoint:
  - Client routes: [routes/web.php](file:///home/bizmark/bizmark.id/routes/web.php#L246-L342).
  - Controllers: [Client/ApplicationController](file:///home/bizmark/bizmark.id/app/Http/Controllers/Client/ApplicationController.php), [Client/ClientQuotationController](file:///home/bizmark/bizmark.id/app/Http/Controllers/Client/ClientQuotationController.php), [Client/PaymentController](file:///home/bizmark/bizmark.id/app/Http/Controllers/Client/PaymentController.php).
- Data inti: `PermitApplication`, `Quotation`, `Payment`, `ApplicationDocument`, `ApplicationStatusLog`.
- Dependensi eksternal: Midtrans (Snap + callback).

### BU-04 — Payment Gateway + Manual Verification
- Aktor: client, admin finance/ops.
- Tujuan:
  - Gateway: generate Snap token, proses callback, update status, konversi ke project.
  - Manual: upload bukti transfer, admin verify/reject, notifikasi.
- Entrypoint:
  - Client payment: [routes/web.php](file:///home/bizmark/bizmark.id/routes/web.php#L305-L310)
  - Admin verifikasi: [routes/web.php](file:///home/bizmark/bizmark.id/routes/web.php#L460-L470)
  - Callback: [routes/api.php](file:///home/bizmark/bizmark.id/routes/api.php#L35-L38)
- Data inti: `Payment`, `Quotation`, `PermitApplication`, `ApplicationStatusLog`.
- Dependensi eksternal: Midtrans.

### BU-05 — Email System (Inbox/Campaign/Webhook)
- Aktor: admin, sistem webhook (Cloudflare worker).
- Tujuan: menerima inbound email menjadi tiket/inbox internal, assignment handler, campaign/email outbound.
- Entrypoint:
  - Webhook inbound: [EmailWebhookController](file:///home/bizmark/bizmark.id/app/Http/Controllers/EmailWebhookController.php)
  - Admin email management: [routes/admin/communications_seo_ai.php](file:///home/bizmark/bizmark.id/routes/admin/communications_seo_ai.php)
- Data inti: `EmailInbox`, `EmailAccount`, `EmailAssignment`, `EmailCampaign`, `EmailTemplate`.
- Dependensi eksternal: email worker + provider outbound (stub/unfinished).

### BU-06 — SEO/Autopost Engine (Admin + Scheduler + Queue)
- Aktor: admin konten, scheduler, queue worker.
- Tujuan: generate artikel otomatis, scoring, distribusi, indexing, laporan.
- Entrypoint:
  - Scheduler: [routes/console.php](file:///home/bizmark/bizmark.id/routes/console.php)
  - Jobs: [GenerateAutoPostArticle](file:///home/bizmark/bizmark.id/app/Jobs/GenerateAutoPostArticle.php)
  - Admin UI: [Admin/AutoPostController](file:///home/bizmark/bizmark.id/app/Http/Controllers/Admin/AutoPostController.php)
- Data inti: `AutoPostSchedule`, `AutoPostLog`, `Article`, `ArticleTopic`.
- Dependensi eksternal: OpenRouter, Pexels, IndexNow, social platform tokens.

### BU-07 — Recruitment (Job Posting → Applicant → Interview/Test)
- Aktor: publik (pelamar), admin HR, kandidat (portal interview/test).
- Tujuan: publik melamar, admin kelola pipeline, kandidat isi interview/test via link/token.
- Entrypoint:
  - Career publik: [routes/web.php](file:///home/bizmark/bizmark.id/routes/web.php)
  - Admin recruitment: [routes/web.php](file:///home/bizmark/bizmark.id/routes/web.php#L533-L585)
  - Candidate portal: [routes/web.php](file:///home/bizmark/bizmark.id/routes/web.php#L587-L619)
- Data inti: `JobVacancy`, `JobApplication`, `InterviewSchedule`, `TestSession`, `TestTemplate`, `TechnicalTestSubmission`.
- Dependensi eksternal: email/notification (sebagian unfinished).

### BU-08 — Shapefile/Polygon Tool + RTRW Proxy
- Aktor: publik (tool), client (hasil analisis), sistem.
- Tujuan: generate/olah shapefile, export PDF, analisis zonasi RTRW.
- Entrypoint:
  - API shapefile: [routes/api.php](file:///home/bizmark/bizmark.id/routes/api.php#L40-L47)
  - API RTRW: [routes/api.php](file:///home/bizmark/bizmark.id/routes/api.php#L49-L57)
- Data inti: `ShapefileProject`, file ZIP/PDF output.
- Dependensi eksternal: endpoint RTRW/GISTARU.

## 3) Unfinished / Undocumented Tasks (prioritas)

Catatan: “unfinished task” = TODO/FIXME/stub/log-only/fitur setengah jadi atau tooling debug yang belum tercatat sebagai backlog resmi.

### P0 — berdampak langsung ke revenue/keamanan/operasional
- **Notifikasi/payment workflow belum lengkap**: callback pembayaran belum mengirim email/notifikasi terarah untuk semua pihak.
  - Lokasi: [PaymentCallbackController.php](file:///home/bizmark/bizmark.id/app/Http/Controllers/Api/PaymentCallbackController.php)
  - Dampak bisnis: klien tidak mendapat kepastian; admin terlambat follow-up; dispute meningkat.
  - Rekomendasi: definisikan event `PaymentSucceeded`/`PaymentFailed` + listener queued untuk notifikasi idempotent.
  - Contoh kode (sketsa):

```php
// app/Events/PaymentSucceeded.php
final class PaymentSucceeded { public function __construct(public Payment $payment) {} }

// app/Listeners/SendPaymentNotifications.php (implements ShouldQueue)
public function handle(PaymentSucceeded $event): void {
    $payment = $event->payment->fresh(['client','quotation.application']);
    $payment->client->notify(new PaymentVerifiedNotification($payment, null));
}
```

### P1 — operasional & kualitas layanan
- **Email webhook auto-reply dan notifikasi assignment masih log-only/TODO**
  - Lokasi: [EmailWebhookController.php](file:///home/bizmark/bizmark.id/app/Http/Controllers/EmailWebhookController.php)
  - Dampak bisnis: SLA support tidak terjaga, missed ticket, audit trail lemah.
  - Rekomendasi: implement outbound mail provider + notification (webpush/email) dan dedup berdasarkan `message_id`.

- **Recruitment: beberapa notifikasi belum diaktifkan**
  - Lokasi: [JobApplicationController.php](file:///home/bizmark/bizmark.id/app/Http/Controllers/JobApplicationController.php), [Candidate/InterviewController.php](file:///home/bizmark/bizmark.id/app/Http/Controllers/Candidate/InterviewController.php)
  - Dampak bisnis: HR kehilangan visibilitas kandidat dan reschedule.
  - Rekomendasi: event-driven notifications + throttling + audit log.

- **Permit template update belum diimplementasi**
  - Lokasi: [PermitTemplateController.php](file:///home/bizmark/bizmark.id/app/Http/Controllers/PermitTemplateController.php)
  - Dampak: admin tidak bisa maintenance template perizinan.
  - Rekomendasi: selesaikan CRUD + tests untuk perubahan template dan dependency.

### P2 — debug/ops hygiene
- **Endpoint debug/test masih ada** (`/test-push`, webhook test endpoint, oauth placeholder).
  - Lokasi: [routes/web.php](file:///home/bizmark/bizmark.id/routes/web.php)
  - Dampak: attack surface bertambah, kebingungan operasional.
  - Rekomendasi: guard dengan `APP_ENV` + permission khusus, atau hapus setelah tidak dipakai.

## 4) Potensi error & edge case yang belum tertangani (dan rekomendasi)

### 4.0 Register temuan (ringkas, untuk tracking)

| ID | BU | Prioritas | Masalah | Dampak bisnis | Rekomendasi | Referensi | Test yang wajib ada |
|---|---|---:|---|---|---|---|---|
| F-01 | BU-05 | P0 | Webhook email raw HTML (stored XSS) | takeover admin/session, kebocoran data | sanitasi/escape HTML; dedup message_id | Email webhook + admin inbox views | Feature: payload XSS tidak dieksekusi; output aman |
| F-02 | BU-04 | P0 | Midtrans callback spoof/replay/idempotency | status payment salah, dispute | signature + idempotency + lockForUpdate | PaymentCallbackController | Feature: invalid signature 403; duplicate callback no-op |
| F-03 | BU-02/04 | P0 | Route admin sensitif hanya `auth:web` | privilege escalation internal | pasang `permission:*`/policy | routes/web.php admin group | Feature: user tanpa permission → 403 |
| F-04 | BU-04 | P1 | Race `payment_number` + format inkonsisten | collision nomor, audit kacau | satu sumber generator + unique index | Payment model vs Client PaymentController | Unit: generator; Feature: concurrent create (simulasi) |
| F-05 | BU-03/04 | P1 | Status application/payment bisa “loncat” | workflow inkonsisten | state machine + validasi transisi | Application controllers/services | Unit: transisi; Feature: invalid transition 422 |
| F-06 | BU-03 | P1 | Client bisa membuat banyak payment pending | double invoice, kebingungan klien | enforce 1 active payment per quotation | Client PaymentController | Feature: create second payment ditolak |
| F-07 | BU-05 | P1 | Email webhook replay (tanpa timestamp/nonce) | duplikasi tiket/inbox | nonce/timestamp window + unique message_id | EmailWebhookController | Feature: duplicate message_id tidak duplikat |
| F-08 | BU-04 | P1 | Proof file sensitif lewat public storage | kebocoran PII/dokumen | private disk + secure streaming | PaymentController + admin payment view | Feature: unauthorized proof 403 |
| F-09 | BU-07 | P1 | Candidate portal tanpa throttle | brute force token | throttle + lockout | routes/web.php candidate | Feature: throttle bekerja |
| F-10 | BU-06 | P1 | Scheduler multi-server tanpa onOneServer | double-run lintas server | onOneServer + shared cache lock | routes/console.php schedule | Integration: lock store config; observability |
| F-11 | BU-03/04 | P2 | Null relations (quotation/application/permitType) | 500 pada flow kritikal | guard null + fallback response | payment success/callback/convert | Feature: missing relation handled |
| F-12 | BU-03 | P2 | `form_data` tanpa schema whitelist | data liar masuk DB | FormRequest + schema validation | ApplicationController | Unit: validator; Feature: invalid keys 422 |

### 4.1 Otorisasi & privilege escalation
- Masalah: beberapa route sensitif admin hanya memakai `auth:web` tanpa permission/role yang ketat.
- Dampak bisnis: user internal non-admin bisa melakukan aksi finansial/operasional yang tidak semestinya.
- Lokasi: [routes/web.php](file:///home/bizmark/bizmark.id/routes/web.php#L404-L489).
- Rekomendasi:
  - Terapkan `permission:*` pada semua route admin sensitif (payments verify/reject, permit-applications, document approvals).
  - Tambahkan test otorisasi (403 untuk user tanpa permission).
  - Contoh:

```php
Route::middleware(['auth:web', 'permission:finances.manage_payments'])
  ->group(function () {
      Route::post('payments/{id}/verify', ...);
      Route::post('payments/{id}/reject', ...);
  });
```

### 4.2 State machine application/payment yang tidak konsisten
- Masalah: flow status aplikasi dapat dilompati/bypass, dan transisi status tidak di-enforce.
- Dampak bisnis: aplikasi stuck/inkonsisten, reporting salah, dispute pembayaran.
- Lokasi contoh:
  - create/submit mismatch: [Client/ApplicationController.php](file:///home/bizmark/bizmark.id/app/Http/Controllers/Client/ApplicationController.php)
  - update status bebas: [ApplicationManagementController.php](file:///home/bizmark/bizmark.id/app/Http/Controllers/Admin/ApplicationManagementController.php)
- Rekomendasi:
  - Formalisasi state machine (allowed transitions).
  - Validasi transisi di service layer + test transisi.
  - Contoh guard transisi:

```php
public function transition(PermitApplication $app, string $to): void {
  $allowed = [
    'draft' => ['submitted'],
    'submitted' => ['quotation_issued','rejected'],
    'quotation_accepted' => ['payment_pending'],
    'payment_verified' => ['processing'],
  ];
  $from = $app->status;
  abort_unless(in_array($to, $allowed[$from] ?? [], true), 422, 'Invalid status transition');
  $app->update(['status' => $to]);
}
```

### 4.3 Idempotency & replay: Midtrans callback + Email webhook
- Masalah: callback/webhook bisa dikirim ulang (retry/replay) dan memicu side-effect ganda.
- Dampak bisnis: double log, konversi ganda, status tidak akurat, noise notifikasi.
- Lokasi:
  - Midtrans callback: [PaymentCallbackController.php](file:///home/bizmark/bizmark.id/app/Http/Controllers/Api/PaymentCallbackController.php)
  - Email webhook: [EmailWebhookController.php](file:///home/bizmark/bizmark.id/app/Http/Controllers/EmailWebhookController.php)
- Rekomendasi:
  - Idempotency key per `order_id`/`message_id` (unique constraint + upsert).
  - Simpan “last_processed_at/status” dan skip jika sudah final.
  - Contoh (Midtrans):

```php
$payment = Payment::where('payment_number', $orderId)->lockForUpdate()->firstOrFail();
if (in_array($payment->status, ['success','failed'], true)) {
  return response()->json(['message' => 'Already finalized']);
}
```

### 4.4 Concurrency: nomor pembayaran & verifikasi ganda
- Masalah: generate payment number tanpa lock → collision; verifikasi admin tanpa lock → TOCTOU.
- Dampak bisnis: data integrity dan audit trail kacau.
- Lokasi:
  - payment number format ganda: [Payment.php](file:///home/bizmark/bizmark.id/app/Models/Payment.php), [Client/PaymentController.php](file:///home/bizmark/bizmark.id/app/Http/Controllers/Client/PaymentController.php)
  - verifikasi: [PaymentVerificationController.php](file:///home/bizmark/bizmark.id/app/Http/Controllers/Admin/PaymentVerificationController.php)
- Rekomendasi:
  - Standarkan format nomor pembayaran (1 sumber di model).
  - Tambahkan unique index `payment_number`.
  - Gunakan transaksi + `lockForUpdate()` untuk verify/reject.

### 4.5 Null relations (fatal error)
- Masalah: beberapa flow mengasumsikan relasi selalu ada (`quotation`, `application`, `permitType`).
- Dampak bisnis: 500 pada flow kritikal (callback/payment success).
- Rekomendasi: `->with()` + validasi `exists` + fallback messaging, serta tests null-relation.

## 5) Rencana testing komprehensif

### 5.1 Kondisi saat ini
- Sudah ada test suite PHPUnit dengan `RefreshDatabase` + SQLite memory untuk testing: [phpunit.xml](file:///home/bizmark/bizmark.id/phpunit.xml), [TestCase.php](file:///home/bizmark/bizmark.id/tests/TestCase.php).
- Existing tests (contoh):
  - Shapefile/RTRW: [tests/Feature/ShapefileApiTest.php](file:///home/bizmark/bizmark.id/tests/Feature/ShapefileApiTest.php), [RtrwApiTest.php](file:///home/bizmark/bizmark.id/tests/Feature/RtrwApiTest.php)
  - Service inquiry: [ServiceInquiryControllerTest.php](file:///home/bizmark/bizmark.id/tests/Feature/ServiceInquiryControllerTest.php)
  - SEO admin: [tests/Unit/Http/Controllers/Admin/Seo](file:///home/bizmark/bizmark.id/tests/Unit/Http/Controllers/Admin/Seo)
- Gap utama: payment end-to-end, email webhook, client portal flow perizinan, recruitment candidate portal, otorisasi admin sensitif, idempotency.

### 5.2 Strategi layer test

**Unit Test (cepat, deterministic)**
- Target:
  - Pure services (formatter/engine), helper, state machine, signature validator.
  - Model accessors/mutators, “policy” method, guard functions.
- Contoh kandidat:
  - Validasi signature callback Midtrans (expected hash).
  - Resolver lokasi file proof (private/public fallback).
  - State transition validator untuk `PermitApplication`.

**Integration/Feature Test (HTTP + DB)**
- Target:
  - Endpoint controllers utama dengan DB in-memory.
  - Otorisasi per role/permission.
  - Workflow lintas model (create application → quotation → payment status).
- Fokus: happy path + negative + boundary.

**End-to-End Test (UI)**
- Karena belum ada Dusk/Playwright di repo, rekomendasi 2 opsi:
  - **Laravel Dusk** (PHP): cocok untuk flow admin/client portal.
  - **Playwright** (Node): cocok untuk UI testing modern + cross-browser.
- Minimum E2E yang penting:
  - Client login → create application → lihat quotation → upload manual proof → admin verify → client lihat status.
  - Admin login → inbox email → lihat email → reply.

### 5.3 Matriks skenario test per BU (positive/negative/boundary)

#### BU-03 Client Portal (Perizinan)
- Positive:
  - Client register → verify email → login.
  - Create draft application → submit → status berubah sesuai aturan.
- Negative:
  - Submit application bukan milik client → 404/403.
  - Submit application status bukan draft → 422.
- Boundary:
  - `form_data` empty/large (max payload), unicode, nested array.
  - Upload dokumen: file max size & mime edge (pdf/jpg/png).

#### BU-04 Payment
- Positive:
  - Gateway initiate → payment record dibuat → callback valid signature settlement → payment success.
  - Manual: upload bukti → admin verify → application status log tercipta.
- Negative:
  - Callback invalid signature → 403.
  - Admin verify payment status bukan `processing` → ditolak.
  - Proof akses tanpa auth/permission → 403.
- Boundary:
  - Callback duplicate (idempotency) → tidak menambah log/side effect.
  - Concurrency: dua payment dibuat bersamaan → payment_number tetap unik.

#### BU-05 Email
- Positive:
  - Webhook authorized → inbox record tersimpan → body_text tersedia.
- Negative:
  - Signature invalid / header hilang → 401/403.
  - `from/subject` kosong → 400.
- Boundary:
  - Payload besar (html panjang), attachments array kosong/berisi banyak item.
  - Replay message_id → tidak membuat duplikat (butuh constraint/test).

#### BU-07 Recruitment
- Positive:
  - Applicant apply → record created.
  - Candidate interview/test via token valid.
- Negative:
  - Token invalid/expired → 403/410.
  - Brute force rate limit (throttle).
- Boundary:
  - Submit test empty/partial; upload file besar; time window close.

#### BU-08 Shapefile/RTRW
- Positive:
  - Generate shapefile → calculate → pdf.
- Negative:
  - Invalid geometry → 422.
  - RTRW proxy timeout → 502/504 handling.
- Boundary:
  - File zip besar; concurrency generate.

### 5.4 Contoh kode test (Feature)

Catatan: saat ini repo belum terlihat memiliki `PaymentFactory`/`TaskFactory` di `database/factories`. Untuk membuat test ringkas dan stabil, rekomendasi menambahkan factory tersebut, atau buat record minimal via `Model::create()` di test dengan field wajib.

**(A) Midtrans callback invalid signature → 403**

```php
public function test_midtrans_callback_rejects_invalid_signature(): void
{
    $payload = [
        'order_id' => 'PAY-2026-001',
        'status_code' => '200',
        'gross_amount' => '10000.00',
        'signature_key' => 'invalid',
    ];

    $this->postJson('/api/payment/callback', $payload)->assertStatus(403);
}
```

**(B) Admin proof route harus auth**

```php
public function test_payment_proof_requires_auth(): void
{
    $payment = Payment::create([
        'payment_number' => 'PAY-2026-001',
        'payable_type' => \App\Models\PermitApplication::class,
        'payable_id' => 1,
        'client_id' => 1,
        'quotation_id' => 1,
        'amount' => 10000,
        'payment_type' => 'down_payment',
        'payment_method' => 'manual',
        'status' => 'processing',
        'transfer_proof_path' => 'payment-proofs/x.pdf',
    ]);
    $this->get("/admin/payments/{$payment->id}/proof")->assertRedirect('/login');
}
```

**(C) Mobile task detail tidak boleh akses task user lain**

```php
public function test_mobile_task_show_forbidden_for_other_user(): void
{
    $userA = User::factory()->create();
    $userB = User::factory()->create();
    $task = Task::create([
        'title' => 'Tugas Uji',
        'status' => 'todo',
        'assigned_user_id' => $userB->id,
        'due_date' => now()->addDay(),
    ]);

    $this->actingAs($userA)->get("/m/tasks/{$task->id}")->assertStatus(403);
}
```

## 6) Rekomendasi backlog terstruktur (ringkas)

- Security/Access:
  - Audit semua route `auth:web` yang sensitif → pasang `permission:*`.
  - Tambahkan throttle untuk candidate portal.
- Data integrity:
  - Standarisasi `payment_number`, unique index, row locking verify/reject.
  - Idempotency untuk callback/webhook.
- Product completeness:
  - Implement notifikasi (payment, quotation, revision, recruitment).
  - Finalisasi email outbound provider.
- Testing:
  - Tambah test suite untuk payment & webhook + otorisasi + concurrency.
