# 2026-W16 Refactor Sprint
## Periode: Senin, 13 Apr - Jumat, 17 Apr 2026

---

## 0) Sprint Meta

- **Sprint Name**: `2026-W16 Refactor Sprint`
- **Owner**: Engineering
- **Contributors**: Platform + Product Engineering
- **Branch Strategy**: Trunk-based (`main`) with atomic commits

---

## 1) Objective Sprint

1. Menstabilkan fondasi refactor dengan pemecahan domain besar (SEO, dashboard, routes).
2. Menutup celah keamanan residual (legacy secret-path references, credential hygiene).
3. Menstandarkan eksekusi refactor lewat playbook + template sprint.

---

## 2) Scope Definition

### In Scope
- Split controller/service berisiko rendah dengan contract output tetap.
- Modularisasi route admin agar lebih maintainable.
- Security hygiene di config/script/docs.
- Dokumentasi SOP refactor.

### Out of Scope
- Perubahan behavior bisnis inti.
- Migrasi database besar/squash migration.
- Optimasi performa agresif yang mengubah query semantics.

---

## 3) Backlog Prioritas (Delivered)

| ID | Prioritas | Item | Impact | Effort | Status |
|----|-----------|------|--------|--------|--------|
| R-01 | P0 | Security cleanup (`/__REDACTED_LEGACY_ADMIN_SEGMENT__`, credential leak prevention) | High | S | DONE |
| R-02 | P1 | Split SEO analytics monolith | High | L | DONE |
| R-03 | P1 | Modularize admin routes | High | M | DONE |
| R-04 | P1 | Split dashboard by domain services | High | M | DONE |
| R-05 | P2 | PHPUnit metadata deprecation cleanup | Medium | S | DONE |
| R-06 | P3 | Refactor execution playbook + sprint template docs | Medium | S | DONE |

---

## 4) Batch Summary (Executed)

### Batch A — Security Hardening
- **Scope**: auth config, robots/sitemap handling, restore script, docs hygiene.
- **Result**: secret path lama tidak lagi tersebar di code/docs/script.
- **Verification**: route list + lint + grep sweep.

### Batch B — SEO Controller Split
- **Scope**: `SeoAnalyticsController` -> `Admin/Seo/*Controller`.
- **Result**: domain terpisah (scores/reports/competitors/ab tests/search console/etc).
- **Verification**: route mapping intact, no runtime break.

### Batch C — Routes Modularization
- **Scope**: `web.php` + `web_admin.php` + `routes/admin/*.php`.
- **Result**: admin routes terpisah domain, entrypoint lebih kecil.
- **Verification**: `php artisan route:list --except-vendor` sukses (`Showing [688] routes`).

### Batch D — Dashboard Domain Split
- **Scope**: `DashboardDataService` jadi orchestrator tipis.
- **Result**:
  - `DashboardAlertService`
  - `DashboardFinancialService`
  - `DashboardOperationalService`
- **Verification**: DI resolve sukses + lint bersih.

### Batch E — Process Standardization
- **Scope**: docs playbook + template.
- **Result**:
  - `docs/PARTIAL_SPLIT_MVP_EXECUTION_PLAYBOOK.md`
  - `docs/WEEKLY_REFACTOR_SPRINT_TEMPLATE.md`

---

## 5) Definition of Done Gate (Status)

- [x] Scope batch terjaga (no hidden scope creep)
- [x] Syntax check (`php -l`) file berubah lulus
- [x] Route integrity check lulus (`Showing [688] routes`)
- [x] DI resolution untuk service baru lulus
- [x] Linter error baru = 0
- [x] Tidak ada hardcoded secret/path sensitif residual yang diketahui
- [x] Atomic commits dibuat per tema
- [x] Dokumentasi proses refactor tersedia

---

## 6) Verification Matrix (Final)

| Area | Verifikasi | Hasil |
|------|------------|-------|
| Routes | `php artisan route:list --except-vendor` | PASS |
| Services DI | `php artisan tinker --execute=\"app(...)\"` | PASS |
| Syntax | `php -l` on changed files | PASS |
| Lint | diagnostics changed files | PASS |
| Security sweep | string/path leak scan | PASS |

---

## 7) Risk & Decision Log

| Tanggal | Isu | Dampak | Keputusan |
|---------|-----|--------|-----------|
| 2026-04-17 | Split route partial menyebabkan class resolution error | High | Gunakan FQCN langsung di partial route files |
| 2026-04-17 | `robots.txt` sempat membocorkan admin secret path | High | Hilangkan publikasi secret path dari robots/sitemap logic |
| 2026-04-17 | Test run diblok safety guard (`bizmark_db`) | Medium | Pertahankan guard, gunakan verifikasi non-destructive |

---

## 8) Merge Readiness Checklist

- [x] Semua batch selesai dan tervalidasi
- [x] Working tree bersih setelah commit
- [x] Dokumentasi diperbarui
- [x] Tidak ada file backup sensitif ikut track

---

## 9) Retrospective

### What Went Well
- Refactor besar bisa dikirim bertahap tanpa downtime.
- Safety checks (route list, lint, DI) efektif mencegah regressions.
- Atomic commit membantu rollback readiness.

### What Was Painful
- Route partialization rentan error namespace/import saat awal split.
- Test suite penuh tidak bisa dijalankan pada konfigurasi DB unsafe.

### Action Items Minggu Depan (2026-W17)
1. Tambah contract tests untuk `DashboardDataService::build()` (shape/keys).
2. Lanjut standardisasi naming dan common helper di `Admin/Seo/*Controller`.
3. Buat guideline route governance final untuk `routes/admin/*.php`.

---

## 10) Referensi Dokumen

- `docs/PARTIAL_SPLIT_MVP_EXECUTION_PLAYBOOK.md`
- `docs/WEEKLY_REFACTOR_SPRINT_TEMPLATE.md`

