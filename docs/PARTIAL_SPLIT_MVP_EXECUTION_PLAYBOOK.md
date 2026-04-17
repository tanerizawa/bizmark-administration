# Partial/Split/MVP Execution Playbook
## Panduan Sistematis Refactor Bertahap Bizmark.ID

---

## 1) Tujuan Dokumen

Dokumen ini menjadi kerangka kerja standar untuk:

1. Mengeksekusi perubahan besar secara **bertahap (partial)**.
2. Memecah modul besar menjadi unit kecil (**split by domain**).
3. Menjaga delivery cepat lewat **MVP per batch**, tanpa menunggu perfect-state.
4. Menurunkan risiko regresi melalui verifikasi dan rollback plan yang jelas.

Fokusnya bukan sekadar “bersih kode”, tetapi menjaga:
- kontinuitas bisnis,
- stabilitas produksi,
- keamanan,
- dan kecepatan iterasi.

---

## 2) Prinsip Utama

### A. Partial-First
Jangan refactor masif sekaligus. Potong menjadi batch kecil yang bisa diverifikasi.

### B. Split by Domain, not by File Size
Pemecahan dilakukan berdasarkan tanggung jawab bisnis (alerts, financial, SEO, routing), bukan semata jumlah baris.

### C. MVP per Batch
Setiap batch harus menghasilkan nilai pakai nyata, meski belum final 100%.

### D. Contract-First
Output/shape API, route names, dan behavior existing harus tetap kompatibel selama transisi.

### E. Safety Rails Wajib
Setiap batch wajib punya:
- Definition of Done,
- verifikasi minimal,
- rollback path.

---

## 3) Baseline Kondisi Saat Ini (Ringkas)

Batch yang sudah terealisasi:

1. **Security hardening**
   - pembersihan kredensial plain text,
   - rotasi admin secret path via config,
   - pembersihan jejak path lama.

2. **Route modularization**
   - `routes/web.php` disederhanakan,
   - admin routes dipisah ke `routes/web_admin.php` + `routes/admin/*`.

3. **Controller/service splitting**
   - `SeoAnalyticsController` dipecah per domain.
   - `FinancialController` dipecah per domain.
   - `FreeAIAnalysisService` dipecah sebagian.
   - `DashboardDataService` sekarang sudah jadi orchestrator tipis dengan domain service:
     - `DashboardAlertService`
     - `DashboardFinancialService`
     - `DashboardOperationalService`

Implikasi: fondasi refactor sudah benar, tahap berikutnya adalah standarisasi eksekusi dan coverage verifikasi.

---

## 4) Arsitektur Eksekusi: 3 Layer

### Layer 1 — Orchestrator (tipis)
Contoh: `DashboardDataService`  
Tugas:
- menyusun output akhir,
- delegasi ke domain services,
- tanpa query/logic kompleks.

### Layer 2 — Domain Services
Contoh:
- `DashboardAlertService`
- `DashboardFinancialService`
- `DashboardOperationalService`

Tugas:
- business query + mapping per domain,
- isolated dan testable.

### Layer 3 — Interface Adapters
Contoh:
- controller,
- route partial,
- view model mapping.

Tugas:
- terima input/response,
- minim logic domain.

---

## 5) Framework Prioritas Pengerjaan

Gunakan urutan prioritas berikut:

1. **P0 Security & Data Integrity**
   - credential leaks,
   - secret exposure,
   - destructive scripts tanpa guard.

2. **P1 Runtime Stability**
   - route integrity,
   - service resolution/container,
   - fatal-error hotspots.

3. **P2 High-Complexity Modules**
   - file besar + tanggung jawab campur.

4. **P3 Maintainability Improvements**
   - naming consistency,
   - import cleanup,
   - docs & test debt.

---

## 6) Template MVP per Batch

Setiap batch harus mengikuti format ini:

### Batch Header
- **Nama**: singkat, domain-specific.
- **Scope**: file/area yang disentuh.
- **Out of Scope**: yang sengaja tidak disentuh.

### Deliverables
- [ ] pemecahan kode/domain
- [ ] tidak mengubah kontrak eksternal
- [ ] verifikasi runtime
- [ ] update dokumentasi (jika ada perubahan pola)

### Definition of Done
- [ ] `php -l` semua file berubah
- [ ] `php artisan route:list --except-vendor` sukses
- [ ] dependency injection resolve (tinker smoke)
- [ ] linter errors baru = 0
- [ ] rollback path jelas

### Rollback Plan
- revert commit batch tunggal (atomic commit),
- tidak ada migration irreversible di batch yang sama.

---

## 7) SOP Eksekusi Teknis (Langkah Operasional)

1. **Audit singkat**
   - identifikasi method/domain yang akan diekstrak.
2. **Extract service baru**
   - copy logic + dependency minimal.
3. **Wire orchestrator**
   - constructor injection + delegasi.
4. **Delete old logic**
   - hapus duplikasi setelah verifikasi.
5. **Verification gate**
   - syntax, route list, DI smoke, lints.
6. **Atomic commit**
   - 1 batch = 1 commit tematik.

---

## 8) Checklist Risiko & Mitigasi

### Risiko: route break karena file include
Mitigasi:
- setiap partial route tetap diawali `<?php`,
- hindari ketergantungan implicit `use` lintas file.

### Risiko: contract output berubah
Mitigasi:
- keep array keys dan struktur output identik,
- tambah contract test/snapshot.

### Risiko: regressions tersembunyi
Mitigasi:
- batch kecil,
- smoke checks wajib tiap batch,
- commit atomik untuk revert cepat.

### Risiko: query performance turun
Mitigasi:
- jangan ubah semantics query saat extraction,
- optimasi dilakukan sebagai batch terpisah.

---

## 9) Rencana Batch Rekomendasi (Next)

### Batch A — Contract Tests Dashboard (P1)
Goal:
- menambahkan test shape output `DashboardDataService::build()`.

Deliverables:
- test keys wajib: `criticalAlerts`, `cashFlowStatus`, `pendingApprovals`, `cashFlowSummary`, `receivablesAging`, `budgetStatus`, `thisWeek`, `projectStatusDistribution`, `recentActivities`, `ragMetrics`.
- validasi type-level minimum per key.

### Batch B — SEO Domain Consistency (P2)
Goal:
- standarisasi naming + response style antar `Admin/Seo/*Controller`.

Deliverables:
- helper/trait common redirect messaging (opsional),
- pengurangan duplicate snippets.

### Batch C — AutoPost Domain Hardening (P2)
Goal:
- pastikan helper extraction saat ini punya boundaries jelas.

Deliverables:
- validasi dependency graph,
- dokumentasi flow `ArticleAutoPostService` -> helper services.

### Batch D — Route Governance (P3)
Goal:
- tetapkan aturan penempatan route per file domain.

Deliverables:
- mini guideline `routes/admin/*.php`,
- checklist saat menambah endpoint baru.

---

## 10) Standar Dokumentasi per Batch

Setiap batch harus menghasilkan catatan singkat:

1. **Apa yang dipecah**
2. **Kenapa dipecah**
3. **Apa yang tidak diubah**
4. **Cara verifikasi**
5. **Komit terkait**

Format ini menjaga jejak teknis tetap mudah diaudit.

---

## 11) KPI Keberhasilan Refactor

Gunakan indikator ini untuk menilai progres:

1. Jumlah file monolitik berkurang.
2. Ukuran orchestrator turun, domain service naik (sehat).
3. Route integrity stabil (`route:list` sukses konsisten).
4. Warning test framework (deprecated metadata, dll.) turun ke nol.
5. Lead time perubahan fitur menurun (lebih cepat karena modul lebih kecil).

---

## 12) Penutup

Strategi terbaik untuk codebase aktif adalah:

**“Refactor kecil, sering, terukur, dan bisa di-rollback.”**

Dokumen ini dijadikan baseline eksekusi agar semua perubahan besar tetap:
- sistematis,
- aman,
- dan cepat dikirim ke produksi tanpa chaos.

---

## 13) Indeks dokumen terkait

- [docs/README.md](README.md) — indeks playbook, template sprint, governance route admin, arsip sprint.
- [docs/ROUTE_ADMIN_GOVERNANCE.md](ROUTE_ADMIN_GOVERNANCE.md) — konvensi `routes/admin/*.php`.
- [docs/WEEKLY_REFACTOR_SPRINT_TEMPLATE.md](WEEKLY_REFACTOR_SPRINT_TEMPLATE.md) — template sprint mingguan.
- [docs/sprints/](sprints/) — laporan sprint per minggu (`2026-W16` … `2026-W19-refactor.md`).
- [docs/SECURITY_GIT_HISTORY_SCRUB.md](SECURITY_GIT_HISTORY_SCRUB.md) — opsi pembersihan string sensitif di histori Git.
- [docs/GIT_PUBLISH_AFTER_REWRITE.md](GIT_PUBLISH_AFTER_REWRITE.md) — menerbitkan `main` / semua ref setelah histori divergen.

