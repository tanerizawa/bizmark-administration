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
3. **Sinkron GitHub (manual):** saat PAT + branch protection siap — force-push histori + komunikasi re-sync (carry-over W17-05).

---

## 2) Scope

### In Scope

- [x] Dokumentasi carry-over W17 (README + sprint W17/W18).
- [x] Workflow CI `.github/workflows/verify.yml` + target `make verify`.
- [ ] Audit cepat `routes/admin` setelah merge dari kontributor lain (regresi FQCN / `<?php`) — ulangi saat ada PR besar.

### Out of Scope

- [ ] Perubahan schema DB / migrasi besar tanpa RFC.
- [ ] Deploy otomatis ke production dari agen.

---

## 3) Backlog

| ID | P | Item | Effort | Status |
|----|---|------|--------|--------|
| W18-01 | P2 | GitHub Actions `verify.yml` + `make verify` | M | DONE |
| W18-02 | P3 | Unit test tambahan (modul non-kritis) | M | TODO |
| W18-03 | P0 | Selesaikan W17-05 manual (push remote) bila blokir rilis | S | TODO |

---

## 4) Referensi

- `docs/sprints/2026-W17-refactor.md`
- `docs/SECURITY_GIT_HISTORY_SCRUB.md`
- `docs/PARTIAL_SPLIT_MVP_EXECUTION_PLAYBOOK.md`
