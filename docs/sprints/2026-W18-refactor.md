# 2026-W18 Refactor Sprint
## Perencanaan: Senin, 27 Apr - Jumat, 1 Mei 2026

> Status: **DRAFT** — lanjutan pasca-W17 (push GitHub manual tidak wajib untuk mulai batch di bawah).

---

## 0) Sprint Meta

- **Sprint Name**: `2026-W18 Refactor Sprint`
- **Owner**: _assign_
- **Contributors**: _assign_
- **Catatan produksi:** `bizmark.id` production — batch di bawah hanya kode/CI/docs di repo; deploy terpisah dengan prosedur rilis tim.

---

## 1) Objective Sprint (maks 3)

1. **Stabilitas CI:** `make verify` / `./scripts/verify-post-scrub-local.sh` lokal + workflow GitHub Actions `verify.yml` (sudah).
2. **Uji tambahan aman:** tambah/rapikan unit test di area berikutnya (prioritas rendah) tanpa mengubah perilaku produksi.
3. **Sinkron GitHub (manual):** `docs/GIT_PUBLISH_AFTER_REWRITE.md` — PAT/SSH + `--force-with-lease`; carry-over W17-05 / W18-03.

---

## 2) Scope

### In Scope

- [x] Dokumentasi carry-over W17 (README + sprint W17/W18).
- [x] Workflow CI `.github/workflows/verify.yml` + target `make verify`.
- [x] Cek otomatis baris pertama `<?php` pada `routes/admin/*.php` (`verify-admin-route-partials.sh`, dipanggil dari `verify-post-scrub-local.sh`). Audit FQCN tetap manual saat review PR.

### Out of Scope

- [ ] Perubahan schema DB / migrasi besar tanpa RFC.
- [ ] Deploy otomatis ke production dari agen.

---

## 3) Backlog

| ID | P | Item | Effort | Status |
|----|---|------|--------|--------|
| W18-01 | P2 | GitHub Actions `verify.yml` + `make verify` | M | DONE |
| W18-02 | P3 | Unit test `SeoDashboardCache` + skrip `verify-admin-route-partials.sh` di alur verify | S | DONE |
| W18-03 | P0 | Publikasi `main` ke GitHub (operator + PAT/SSH) | S | **MANUAL** — ikuti `docs/GIT_PUBLISH_AFTER_REWRITE.md` |

---

## 4) Referensi

- `docs/sprints/2026-W17-refactor.md`
- `docs/SECURITY_GIT_HISTORY_SCRUB.md`
- `docs/PARTIAL_SPLIT_MVP_EXECUTION_PLAYBOOK.md`
