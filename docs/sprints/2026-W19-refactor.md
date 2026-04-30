# 2026-W19 Refactor Sprint
## Perencanaan: Senin, 4 Mei - Jumat, 8 Mei 2026

> Status: **DRAFT** — lanjutan pasca-W18.

---

## 0) Sprint Meta

- **Sprint Name**: `2026-W19 Refactor Sprint`
- **Owner**: _assign_
- **Referensi:** `docs/PARTIAL_SPLIT_MVP_EXECUTION_PLAYBOOK.md`, `docs/WEEKLY_REFACTOR_SPRINT_TEMPLATE.md`

---

## 1) Carry-over (dari W18)

| ID | Item | Catatan |
|----|------|--------|
| W18-03 | Push `main` ke GitHub | `docs/GIT_PUBLISH_AFTER_REWRITE.md` — hingga selesai, tim lain bisa ambil snapshot dari mesin yang sudah push |

---

## 2) Objective (draft)

1. Setelah **remote `main` sinkron**, pastikan GitHub Actions **Verify** hijau di default branch.
2. Tambah unit test / smoke di area berikutnya (pilih dari backlog produk, tetap tanpa migrasi besar).
3. Audit manual satu modul admin (selain SEO) bila ada PR besar — pola FQCN route.

---

## 3) Backlog (isi saat planning)

| ID | P | Item | Status |
|----|---|------|--------|
| W19-01 | P2 | Unit test `SeoAdminFlashRedirect` (nama route `login` + `blog.article.id`) | DONE |
| W19-02 | P2 | Unit test `SeoReportsController` (snapshot views + generate report weekly/monthly) | DONE |
| W19-03 | P2 | Unit test `SeoRefreshLogsController::runContentRefresh` (mock service, tanpa RefreshDatabase SQLite) | DONE |
| W19-04 | P1 | `InvoiceWorkflowTest` — 10 tests: updateStatus, send, delete, recordPayment (partial + full pay) | DONE |
| W19-05 | P1 | `QuotationWorkflowTest` — 8 tests: client accept/reject, expired, ownership guard | DONE |
| W19-06 | P1 | **Bug fix**: `InvoiceController::destroy()` missing `action` field in `ProjectLog::create()` | DONE |
| W19-07 | P1 | **Bug fix**: `InvoiceController::recordPayment()` CashAccount balance guard bypass (`allowBalanceUpdate`) | DONE |
| W19-08 | P3 | `AutoPostPipelineTest` — config toggle, schedule CRUD, processNow, retry, bulkAction (15 tests) | DONE |
| W19-09 | P2 | CSS: `admin/jobs/create.blade.php` + `edit.blade.php` → 100% Tailwind (0 Bootstrap classes) | DONE |
| W19-10 | P2 | CSS: `admin/email/campaigns/show.blade.php` + `admin/email-accounts/show.blade.php` → 100% Tailwind (Bootstrap modals → Alpine.js x-data) | DONE |
| W19-11 | P3 | `AdminEmailCampaignTest` — draft/scheduled/send/cancel/export/delete flow (15 tests) | DONE |
| W19-12 | P2 | CSS: `admin/email/templates/{show,create,edit}.blade.php` → 100% Tailwind (92 hits → 0, Bootstrap modal → Alpine.js) | DONE |
| W19-13 | P3 | `AdminEmailTemplateTest` — CRUD, category validation, active/inactive toggle (11 tests) | DONE |
| W19-14 | P2 | CSS: `admin/email-accounts/index.blade.php` → 100% Tailwind (34 hits → 0, Bootstrap modal → Alpine.js x-data) | DONE |
| W19-15 | P2 | CSS: `admin/email/settings/index.blade.php` → 100% Tailwind (34 hits → 0) | DONE |
| W19-16 | P2 | CSS: `admin/email/campaigns/send.blade.php` → 100% Tailwind (28 hits → 0, checklist → Alpine.js x-data) | DONE |
| W19-17 | P2 | CSS: `admin/email-accounts/create.blade.php` → 100% Tailwind (27 hits → 0, auto-reply + type toggle → Alpine.js) | DONE |
| W19-18 | P3 | `AdminEmailSubscriberTest` — CRUD, duplicate guard, status validation, ilike skip SQLite (13 tests) | DONE |
| W19-19 | P2 | CSS: `admin/permit-applications/revise.blade.php` → 100% Tailwind (167 hits → 0, Bootstrap Tabs → Alpine.js x-data) | DONE |
| W19-20 | P2 | Observability: `sentry/sentry-laravel` ^4.25 + `laravel/pulse` ^1.7 installed, configs published | DONE |
| W19-21 | P2 | Async Job: `GeneratePdfJob` — queue `documents`, DomPDF/HTML fallback, tries=3, timeout=120s | DONE |
| W19-22 | P2 | Async Job: `SendEmailCampaignJob` — queue `email`, batch 50, WithoutOverlapping, tries=2, timeout=600s | DONE |
| W19-23 | P2 | Queue separation: `worker-email` + `worker-documents` dedicated containers di `docker-compose.yml` | DONE |

**Test count: 167 → 251 (+84). Coverage: ~32% → ~48%. Target 250 TERCAPAI. Semua pending sprint W19 selesai.**

---

## 4) Referensi

- `docs/sprints/2026-W18-refactor.md`
- `docs/GIT_PUBLISH_AFTER_REWRITE.md`
