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

---

## 4) Referensi

- `docs/sprints/2026-W18-refactor.md`
- `docs/GIT_PUBLISH_AFTER_REWRITE.md`
