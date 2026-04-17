# 2026-W17 Refactor Sprint
## Perencanaan: Senin, 20 Apr - Jumat, 24 Apr 2026

> Status: **DRAFT KERJA** — isi harian saat sprint berjalan.

---

## 0) Sprint Meta

- **Sprint Name**: `2026-W17 Refactor Sprint`
- **Owner**: _assign_
- **Contributors**: _assign_
- **Referensi proses**: `docs/PARTIAL_SPLIT_MVP_EXECUTION_PLAYBOOK.md`, `docs/WEEKLY_REFACTOR_SPRINT_TEMPLATE.md`

---

## 1) Objective Sprint (maks 3)

1. **Kontrak dashboard**: uji otomatis shape `DashboardDataService::build()` (tanpa DB produksi).
2. **Konsistensi SEO admin**: trait `SeoAdminFlashRedirect` + helper cache `SeoDashboardCache` (selesai).
3. **Governance route**: semua PR route admin mengikuti `docs/ROUTE_ADMIN_GOVERNANCE.md`.

---

## 2) Scope

### In Scope

- [x] Test kontrak `DashboardDataService` (PHPUnit murni + mock).
- [x] Review duplikasi ringan di `Admin/Seo/*` + service terkait — invalidasi cache KPI dipusatkan di `App\Support\SeoDashboardCache`.
- [x] Dokumentasi indeks + sprint link (baseline W16/W17).

### Out of Scope

- [ ] Squash migration / schema besar.
- [ ] Optimasi query agresif tanpa baseline metrik.

---

## 3) Backlog

| ID | P | Item | Effort | Status |
|----|---|------|--------|--------|
| W17-01 | P1 | Contract test `DashboardDataService::build()` | S | DONE |
| W17-02 | P2 | SEO: pusatkan `Cache::forget` KPI dashboard | S | DONE |
| W17-03 | P3 | Scan `routes/admin` — semua partial diawali `<?php` | S | DONE |
| W17-04 | P2 | Runbook + skrip audit pembersihan histori Git; trait redirect SEO | M | DONE |

---

## 4) Batch Plan

### Batch 1 — Contract test

- **Deliverable**: `tests/Unit/Services/Dashboard/DashboardDataServiceContractTest.php`
- **DoD**: `vendor/bin/phpunit …ContractTest.php` hijau tanpa `Tests\TestCase` (hindari guard `bizmark_db`).

### Batch 2 — SEO (minimal)

- **Deliverable**: `App\Support\SeoDashboardCache::forgetStatsCaches()` — dipakai `SeoScoresController`, `SeoReportsController`, `SeoFixService`.
- **DoD**: tidak ada perubahan perilaku; hanya satu sumber kebenaran untuk tiga kunci cache.

### Batch 3 — SEO redirect + keamanan operasional

- **Deliverable**: `SeoAdminFlashRedirect` di semua controller `Admin/Seo/*`; `docs/SECURITY_GIT_HISTORY_SCRUB.md`; `scripts/git-audit-sensitive-paths.sh`; fallback `DB_USERNAME` di `scripts/db-backup.sh` selaras `db-restore.sh` (`bizmark`).
- **DoD**: `php -l` + `route:list`; audit script jalan tanpa error.

---

## 5) Daily Board

### Senin–Jumat

_Plan / Done / Blocker — isi saat sprint._

---

## 6) Risk Log

| Tanggal | Isu | Keputusan |
|---------|-----|-----------|
| | | |

---

## 7) Action dari W16

- [x] Dokumentasi playbook + template + indeks + governance.
- [x] Contract test dashboard (W17-01).
- [x] SEO dedupe cache KPI (W17-02).
