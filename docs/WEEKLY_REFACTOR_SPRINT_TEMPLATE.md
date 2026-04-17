# Weekly Refactor Sprint Template
## Template Eksekusi Mingguan (Partial/Split/MVP)

> Gunakan template ini setiap awal minggu untuk menjaga refactor tetap terstruktur, terukur, dan aman.
>
> **Indeks dokumen:** [README.md](README.md) (playbook, governance route admin, arsip sprint). Publikasi Git setelah rewrite: [GIT_PUBLISH_AFTER_REWRITE.md](GIT_PUBLISH_AFTER_REWRITE.md).

---

## 0) Sprint Meta

- **Sprint Name**: `YYYY-WW Refactor Sprint`
- **Periode**: `Senin, DD MMM - Jumat, DD MMM YYYY`
- **Owner**: `Nama PIC`
- **Contributors**: `Nama tim`
- **Repo/Branch**: `main` / `feature/refactor-...`

---

## 1) Objective Sprint (Maksimal 3)

Contoh:

1. Menurunkan kompleksitas modul dashboard agar controller/service jadi orchestration-only.
2. Menjaga route integrity 100% (no broken route, no naming regressions).
3. Menutup warning/debt test framework tanpa mengubah behavior bisnis.

---

## 2) Scope Definition

### In Scope
- [ ] Area/Modul 1
- [ ] Area/Modul 2
- [ ] Area/Modul 3

### Out of Scope
- [ ] Item yang sengaja tidak disentuh
- [ ] Item yang butuh RFC/decision terpisah

---

## 3) Backlog Prioritas (P0-P3)

| ID | Prioritas | Item | Impact | Effort | Status |
|----|-----------|------|--------|--------|--------|
| R-01 | P0 | ... | High | M | TODO |
| R-02 | P1 | ... | High | S | TODO |
| R-03 | P2 | ... | Medium | M | TODO |
| R-04 | P3 | ... | Low | S | TODO |

Keterangan:
- **Impact**: nilai bisnis/stabilitas
- **Effort**: S (<= 0.5 hari), M (<= 1 hari), L (> 1 hari)

---

## 4) Batch Plan (MVP per Batch)

### Batch 1
- **Goal**:
- **Files**:
- **Expected Output Contract**: (tetap/sedikit berubah)
- **Risiko**:
- **Rollback**:

### Batch 2
- **Goal**:
- **Files**:
- **Expected Output Contract**:
- **Risiko**:
- **Rollback**:

### Batch 3
- **Goal**:
- **Files**:
- **Expected Output Contract**:
- **Risiko**:
- **Rollback**:

---

## 5) Definition of Done Gate (Wajib per Batch)

Checklist wajib sebelum merge:

- [ ] Scope batch tidak melebar (no hidden scope creep)
- [ ] `php -l` semua file berubah lulus
- [ ] `php artisan route:list --except-vendor` sukses
- [ ] Dependency injection resolve untuk service/controller baru
- [ ] Linter error baru = 0
- [ ] Tidak ada hardcoded secret/path sensitif baru
- [ ] Commit message tematik dan atomic
- [ ] Catatan perubahan ditulis (what/why/how to verify)

---

## 6) Daily Execution Board

### Senin
- Planned:
- Done:
- Blocker:

### Selasa
- Planned:
- Done:
- Blocker:

### Rabu
- Planned:
- Done:
- Blocker:

### Kamis
- Planned:
- Done:
- Blocker:

### Jumat
- Planned:
- Done:
- Blocker:

---

## 7) Risk & Decision Log

| Tanggal | Isu | Dampak | Keputusan | Owner |
|---------|-----|--------|-----------|-------|
| YYYY-MM-DD | ... | ... | ... | ... |

Gunakan log ini untuk keputusan penting:
- split route vs tetap monolith,
- contract compatibility decisions,
- defer item karena risiko tinggi.

---

## 8) Verification Matrix

| Area | Verifikasi | Tool/Cmd | Hasil |
|------|------------|----------|-------|
| Routes | Integrity | `php artisan route:list --except-vendor` | PASS/FAIL |
| Services | DI resolution | `php artisan tinker --execute=\"app(...)\"` | PASS/FAIL |
| Syntax | PHP lint | `php -l <file>` | PASS/FAIL |
| Lint | Static checks | ReadLints/CI | PASS/FAIL |
| Security | Secret/path leak scan | `rg` pattern sweep | PASS/FAIL |

---

## 9) Merge Readiness Checklist

- [ ] Semua batch sesuai DoD
- [ ] Tidak ada file backup/log sensitif ikut track
- [ ] Dokumentasi update
- [ ] Re-run verification matrix final
- [ ] Working tree clean

---

## 10) Sprint Retrospective (Jumat)

### What Went Well
- 

### What Was Painful
- 

### Action Items Minggu Depan
1. 
2. 
3. 

---

## 11) Ready-to-Copy Mini Plan (Opsional Cepat)

```md
## Sprint Objective
1) ...
2) ...

## Batch Today
- Batch: ...
- Files: ...
- DoD:
  - php -l
  - route:list
  - DI resolve
  - lint clean

## End of Day Notes
- Done:
- Risk:
- Next:
```

---

## 12) Penggunaan yang Disarankan

1. Duplikasi file ini setiap awal minggu:
   - `docs/sprints/YYYY-WW-refactor.md`
2. Isi Section 0-4 di awal minggu.
3. Update Section 6 harian.
4. Update Section 7 setiap ada keputusan penting.
5. Tutup minggu dengan Section 10.

Dengan format ini, refactor tetap cepat, tidak liar, dan mudah diaudit.

