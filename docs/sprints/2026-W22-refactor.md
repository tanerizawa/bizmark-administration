# 2026-W22 Refactor Sprint
## Perencanaan: Senin, 5 Mei - Jumat, 9 Mei 2026

> Status: **PLANNING** — lanjutan pasca-W21. Focus: Sentry DSN production + test coverage 60% + Client portal.

---

## 0) Sprint Meta

- **Sprint Name**: `2026-W22 Refactor Sprint`
- **Focus**: Sentry production, Client Portal tests, AI job tests, target 60% coverage

---

## 1) Carry-over (dari W21)

| ID | Item | Catatan |
|----|------|--------|
| W21-01 | Set `SENTRY_LARAVEL_DSN` di production `.env` | Aksi manual server — ambil DSN dari sentry.io |
| W21-07 | `ClientPortalDashboardTest` | Deferred ke W22 |
| W19-carry | Migration squash (156 files) | HIGH RISK — deferred jangka panjang |

---

## 2) Backlog

| ID | P | Item | Status |
|----|---|------|--------|
| W22-01 | P1 | Set `SENTRY_LARAVEL_DSN` production + verify Sentry receives errors | TODO |
| W22-02 | P1 | `ClientPortalDashboardTest` — project list, invoice view, permit status, doc upload (8+ tests) | TODO |
| W22-03 | P2 | `AiDocumentParaphraseTest` — dispatch ParaphraseDocumentJob + fake queue assert (4 tests) | TODO |
| W22-04 | P2 | `AiAnalysisJobTest` — unit test handle() article_meta_optimize, unknown task, failed() (4 tests) | TODO |
| W22-05 | P2 | `GeneratePdfJobTest` — unit test handle() PDF + HTML fallback, failed() callback (4 tests) | TODO |
| W22-06 | P3 | SEO admin test: keyword cluster index, position tracking, refresh logs (6 tests) | TODO |
| W22-07 | P3 | Docs: update SYSTEM_ARCHITECTURE_AUDIT.md — W22 close + W23 planning | TODO |

---

## 3) Catatan Teknis

### Sentry DSN Setup (W22-01)
```bash
# Di server production:
nano /home/bizmark/bizmark.id/.env
# Tambahkan baris:
SENTRY_LARAVEL_DSN=https://xxx@o0.ingest.sentry.io/xxx
# Restart FPM:
sudo systemctl reload php8.4-fpm
```
Verify: Sentry → Issues → trigger exception test → harus muncul dalam 30 detik.

### Target Coverage W22
- Current: 286 tests, ~55%+ coverage
- Target W22: 310+ tests, ~60% coverage
- Area: Client Portal, AI Jobs (unit), SEO controllers

### AiAnalysisJob Test Pattern
```php
Bus::fake();
AiAnalysisJob::dispatch('article_meta_optimize', ['article_id' => $article->id]);
Bus::assertDispatched(AiAnalysisJob::class, fn($job) =>
    $job->taskType === 'article_meta_optimize' &&
    $job->payload['article_id'] === $article->id
);
```

---

## 4) Referensi

- `docs/sprints/2026-W21-refactor.md`
- `docs/SYSTEM_ARCHITECTURE_AUDIT.md` — section 13 (Sprint W22 Planning)
