# 2026-W21 Refactor Sprint
## Perencanaan: Senin, 28 Apr - Jumat, 2 Mei 2026

> Status: **COMPLETE** ✅ — kecuali W21-01 (Sentry DSN production — aksi manual server) dan W21-07 (carry-over ke W22)

---

## 0) Sprint Meta

- **Sprint Name**: `2026-W21 Refactor Sprint`
- **Focus**: Observability (Sentry DSN produksi), test coverage push ke 60%, CI/CD phpunit fix, AiAnalysisJob

---

## 1) Carry-over (dari W20)

| ID | Item | Catatan |
|----|------|--------|
| W20-14 | Set `SENTRY_LARAVEL_DSN` di production `.env` | Aksi manual — login ke server, ambil DSN dari sentry.io dashboard |
| W19-carry | Migration squash | HIGH RISK — deferred jangka panjang (156 migrations) |

---

## 2) Backlog

| ID | P | Item | Status |
|----|---|------|--------|
| W21-01 | P1 | Set `SENTRY_LARAVEL_DSN` di production `.env` + verify error tracking | PENDING — aksi manual server |
| W21-02 | P1 | CI/CD: tambah `config:clear && route:clear` sebelum PHPUnit di `ci.yml` | DONE |
| W21-03 | P1 | `AdminJobApplicationWorkflowTest` — 11 tests (index, status, filter, delete, 403 guard) | DONE |
| W21-04 | P2 | `AdminPermitApplicationWorkflowTest` — 12 tests (startReview, updateStatus, addNotes, 403) | DONE |
| W21-05 | P2 | `AiAnalysisJob` — queue `ai`, article_meta_optimize, tries=3, timeout=180s | DONE |
| W21-06 | P2 | Laravel Pulse: `/pulse` route aktif, recorders on | DONE |
| W21-07 | P3 | `ClientPortalDashboardTest` — carry-over ke W22 | CARRY-OVER |
| W21-08 | P3 | Docs: SYSTEM_ARCHITECTURE_AUDIT.md W21 close + W22 planning | DONE |

---

## 3) Catatan Teknis

### phpunit.xml Fix (dari W20 close)
PHPUnit 11 tidak lagi menggunakan `putenv()` untuk `<env>` elements by default.
Fix: tambah `force="true"` pada env vars kritis di `phpunit.xml`.
Pre-condition sebelum test: `php artisan config:clear && php artisan route:clear`.

### Target Coverage W21 — HASIL
- Start: 264 tests, ~50% coverage
- End: 286 tests, ~55%+ coverage (+22 tests)
- Target W22: 310+ tests, ~60% coverage

**Test baru W21:**
- `AdminJobApplicationWorkflowTest` — 11 tests: index (403, redirect), show (404), update-status (reviewed/interview/rejected/accepted/invalid), filter, delete
- `AdminPermitApplicationWorkflowTest` — 12 tests: index (403, redirect), show (404), startReview (valid/invalid state), updateStatus (document_incomplete/cancelled/invalid), addNotes

### Sentry DSN Setup
```bash
# Di server production:
nano /home/bizmark/bizmark.id/.env
# Tambah:
SENTRY_LARAVEL_DSN=https://xxx@o0.ingest.sentry.io/xxx
# Restart FPM:
sudo systemctl reload php8.4-fpm
```

Verify: Sentry → Issues → harus ada error baru setelah deploy.

---

## 4) Referensi

- `docs/sprints/2026-W20-refactor.md`
- `docs/SYSTEM_ARCHITECTURE_AUDIT.md` — section 13 (Sprint W21 Planning)
- `docs/OBSERVABILITY_PLAN.md`
