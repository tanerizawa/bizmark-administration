# Dokumentasi Teknis Bizmark.ID

Indeks ringkas untuk navigasi dokumen refactor, sprint, dan panduan operasional.

---

## Refactor & eksekusi bertahap

| Dokumen | Deskripsi |
|---------|-----------|
| [PARTIAL_SPLIT_MVP_EXECUTION_PLAYBOOK.md](PARTIAL_SPLIT_MVP_EXECUTION_PLAYBOOK.md) | Playbook partial/split/MVP: prioritas, SOP batch, DoD, risiko, KPI. |
| [WEEKLY_REFACTOR_SPRINT_TEMPLATE.md](WEEKLY_REFACTOR_SPRINT_TEMPLATE.md) | Template sprint mingguan (objective, backlog, daily board, retro). |
| [ROUTE_ADMIN_GOVERNANCE.md](ROUTE_ADMIN_GOVERNANCE.md) | Aturan penempatan dan konvensi `routes/admin/*.php`. |
| [SECURITY_GIT_HISTORY_SCRUB.md](SECURITY_GIT_HISTORY_SCRUB.md) | Runbook audit/pembersihan string sensitif di histori Git (`git filter-repo`). |
| [GIT_PUBLISH_AFTER_REWRITE.md](GIT_PUBLISH_AFTER_REWRITE.md) | Push `main` / semua ref setelah histori divergen (PAT, SSH, branch protection). |

---

## Sprint (arsip per minggu)

| Sprint | Status |
|--------|--------|
| [sprints/2026-W16-refactor.md](sprints/2026-W16-refactor.md) | Laporan minggu selesai (delivered batches + retro). |
| [sprints/2026-W17-refactor.md](sprints/2026-W17-refactor.md) | W17 selesai di lokal; **W17-05 push GitHub = manual** (tim). |
| [sprints/2026-W18-refactor.md](sprints/2026-W18-refactor.md) | W18 draft: CI/verify, uji tambahan, carry-over push. |

**Cara pakai:** duplikasi `WEEKLY_REFACTOR_SPRINT_TEMPLATE.md` ke `docs/sprints/YYYY-WW-refactor.md` setiap awal minggu.

**CI lokal / GitHub:** `make verify` atau workflow **Verify** (`.github/workflows/verify.yml`) — termasuk cek `routes/admin/*.php` baris pertama `<?php` dan PHPUnit `tests/Unit`.

**Kode terkait SEO admin (ringkas):**

- Invalidasi cache KPI dashboard: `app/Support/SeoDashboardCache.php`.
- Redirect + flash: trait `app/Http/Controllers/Admin/Seo/Concerns/SeoAdminFlashRedirect.php` (dipakai semua controller di folder itu).
- Audit / verifikasi pasca-scrub: `scripts/git-audit-sensitive-paths.sh`, `scripts/verify-post-scrub-local.sh` — runbook: `docs/SECURITY_GIT_HISTORY_SCRUB.md` §4–§4 butir 6 (push ditolak “fetch first” = histori divergen; **jangan** `git pull` sembarangan). Push: `scripts/git-force-push-with-github-token.sh` atau `scripts/git-push-main-force-with-lease.sh` (set `CONFIRM_FORCE_PUSH_MAIN=YES`).

---

## Rencana produk / fitur (referensi)

Dokumen lain di folder ini (`ADMIN_PANEL_*`, `SEO_*`, `PHASE_*`, dll.) adalah rencana domain; jadikan referensi, bukan checklist sprint harian kecuali dipilih masuk backlog sprint.

---

## Uji kontrak dashboard

Ujian shape output `DashboardDataService::build()` tanpa koneksi DB:

- `tests/Unit/Services/Dashboard/DashboardDataServiceContractTest.php` (extends `PHPUnit\Framework\TestCase`, bukan `Tests\TestCase`).

Jalankan:

```bash
vendor/bin/phpunit tests/Unit/Services/Dashboard/DashboardDataServiceContractTest.php
```
