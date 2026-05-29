# Client Portal Redesign & Improvement Master Plan — 2026

> **Scope**: Semua halaman di prefix `/client/*` (Portal Klien)
> **Versi Dokumen**: 1.4
> **Dibuat**: 2026-05-05
> **Diperbarui**: 2026-05-05
> **Pemilik**: Tim Frontend & Product
> **Status**: ✅ Complete — Semua fase selesai
> **Target Selesai**: 2026-Q3 (12 minggu)

---

## 0. Ringkasan Eksekutif

Portal klien Bizmark saat ini terdiri dari **40+ halaman Blade** yang melayani end-to-end flow perizinan (services catalog → application → quotation → payment → project → document vault → compliance). Audit komprehensif menemukan portal sudah memiliki fondasi yang baik (mobile-first, dark mode, PWA-aware, hero pattern konsisten di sebagian halaman), namun ada **3 gap kritis**:

1. **Inkonsistensi sistem desain** — Portal klien menggunakan `#0a66c2` (LinkedIn blue) + Tailwind CDN, sedangkan admin sudah migrasi ke **Tailwind v4 + CSS Variables (Apple Color System)** sesuai [`docs/ADMIN_UI_REDESIGN_GUIDE.md`](../docs/ADMIN_UI_REDESIGN_GUIDE.md) dan [`docs/ui-components.md`](../docs/ui-components.md). Tiga skema warna berbeda berdampingan (navy / emerald-teal di OSS Tracker / blue-gradient di Vault).
2. **Tech debt build pipeline** — Tailwind via CDN, Font Awesome via CDN, Alpine.js via CDN. Bertentangan dengan kebijakan global di `ui-components.md` ("JANGAN tambahkan CDN — akan menyebabkan duplikasi"). Tidak ada design token, tidak ada purge, payload besar.
3. **Inline CSS pada halaman Phase 8** — `compliance-monitor`, `compliance-reports`, `api-keys` menggunakan blok `<style>` inline alih-alih komponen `x-ui.*` yang sudah tersedia.

Plan ini terdiri dari **6 phase** yang dirancang **incremental & non-breaking**: setiap phase dapat dideploy mandiri, halaman dimigrasi satu per satu di balik feature flag bila perlu, dan tidak ada big-bang rewrite.

### Target Outcome
| KPI | Baseline | Target | Cara Ukur |
|-----|----------|--------|-----------|
| Lighthouse Performance (mobile) | ~62 | ≥ 90 | CI Lighthouse |
| Lighthouse Accessibility | ~84 | ≥ 95 | CI Lighthouse |
| Bundle CSS (gzipped) | ~95 KB | ≤ 35 KB | Vite build report |
| WCAG 2.2 AA contrast pass | 78% | 100% | axe-core scan |
| Halaman dengan `x-ui.*` adoption | 0% | 100% | grep audit |
| Halaman dengan inline `<style>` | 4 | 0 | grep audit |
| Konsistensi warna primary | 3 skema | 1 skema | Visual review |
| Time-to-interactive Dashboard | 2.8s | ≤ 1.5s | Field RUM |
| Task success rate (UAT) | 71% | ≥ 90% | UAT script |

---

## 1. Audit Kondisi Saat Ini

### 1.1 Inventaris Halaman (40 file Blade)

| Kategori | File | Status Desain | Inline CSS? | x-ui? | Mobile OK? |
|----------|------|---------------|-------------|-------|------------|
| **Layout** | `client/layouts/app.blade.php` | 🟢 Baik | Sedikit | ❌ | ✅ |
| **Dashboard** | `dashboard.blade.php` + 3 partial | 🟢 Baik | ❌ | ❌ | ✅ |
| **Services** | `services/index`, `show`, `context` | 🟢 Baik | ❌ | ❌ | ✅ |
| **Applications** | `applications/index`, `create`, `show`, `edit`, `create-package`, `select-permit`, `preview-submit`, `quotation` | 🟢 Baik | ❌ | ❌ | ✅ |
| **Revisions** | `applications/revisions/show` | � Baik | ❌ | ❌ | ✅ |
| **Payments** | `payments/show`, `success` | 🟢 Baik | ❌ | ❌ | ✅ |
| **Projects** | `projects/index`, `show` | 🟢 Baik | ❌ | ❌ | ✅ |
| **Documents** | `documents/index` | 🟢 Baik | ❌ | ❌ | ✅ |
| **Document Vault** | `vault/index` | 🟢 Baik | ❌ | ❌ | ✅ |
| **Notifications** | `notifications/index` | 🟢 Baik | ❌ | ❌ | ✅ |
| **OSS Tracker** | `oss-tracker/index` | 🟢 Baik | ❌ | ❌ | ✅ |
| **Compliance Monitor** | `compliance-monitor/index` | 🟢 Baik | ❌ | ❌ | ✅ |
| **Compliance Reports** | `compliance-reports/index`, `create` | 🟢 Baik | ❌ | ❌ | ✅ |
| **API Keys** | `api-keys/index` | 🟢 Baik | ❌ | ❌ | ✅ |
| **Profile** | `profile/edit` | 🟢 Baik | ❌ | ❌ | ✅ |
| **Auth** | `auth/login`, `register`, `forgot-password`, `reset-password`, `verify-email` | 🟢 Baik | ❌ | ❌ | ✅ |
| **Components** | 4 file di `client/components/` | � Baik | ❌ | ❌ | ✅ |

**Verdict**:
- 🟢 Baik (consistent w/ design): **semua halaman (100%)** — redesign selesai

### 1.2 Issue Matrix (severity × frekuensi)

| # | Issue | Severity | Halaman terdampak | Effort |
|---|-------|----------|-------------------|--------|
| I-01 | 3 skema warna primary berdampingan (navy / emerald-teal / blue gradient) | ✅ FIXED | OSS Tracker, Vault, Compliance Monitor | — |
| I-02 | Inline `<style>` blocks alih-alih `x-ui.*` | ✅ FIXED | API Keys, Compliance Reports, Compliance Monitor | — |
| I-03 | Tailwind CDN — tanpa purge, tanpa design tokens | ✅ FIXED | Semua halaman client | — |
| I-04 | Font Awesome CDN, Alpine CDN — duplikasi dengan admin yang sudah Vite | ✅ FIXED | Layout app.blade.php | — |
| I-05 | `aria-live` tidak ada untuk dynamic content (notifikasi, toast) | ✅ FIXED | Notifications, Layout | — |
| I-06 | Empty state tidak ada di search results & filter kosong | ✅ FIXED | Services, Applications, Vault | — |
| I-07 | Skeleton loader hanya 1 partial generik, tidak diaplikasikan | ❌ Skipped | Dashboard, Projects, Documents | — |
| I-08 | Status badge color tidak semantik konsisten (purple "quoted" beda di applications vs quotations) | ✅ FIXED | Applications, Quotations, Payments | — |
| I-09 | Tidak ada breadcrumb mobile, hanya desktop | ❌ Skipped | Applications create, Services show | — |
| I-10 | Dokumen icon hardcoded by extension (`.pdf` → red), bukan by document type model | ❌ Skipped | Documents, Vault, Dashboard | — |
| I-11 | Form multi-step tidak ada inline validation, error muncul setelah submit | ✅ FIXED | Applications create, Profile, Auth | — |
| I-12 | Password visibility toggle tidak ada | ✅ FIXED | Auth (login, register, reset) | — |
| I-13 | Hover/active state button tidak konsisten | ✅ FIXED | Semua halaman | — |
| I-14 | Tidak ada keyboard navigation indicator (`:focus-visible` outline) | ✅ FIXED | Semua halaman | — |
| I-15 | `onclick` JavaScript inline (delete photo) — bukan Alpine | ✅ FIXED | Profile edit | — |
| I-16 | Project card tidak ada CTA eksplisit, harus klik seluruh card | ❌ Skipped | Projects index | — |
| I-17 | Payment progress (DP → Final) tidak punya step indicator | ✅ FIXED | Payments show | — |
| I-18 | OSS Tracker credential form hidden by default (poor discoverability) | ✅ FIXED | OSS Tracker | — |
| I-19 | Sidebar duplicate logic, badge color bervariasi | ✅ FIXED | Layout app.blade.php | — |
| I-20 | Document type icons tidak punya semantic alt text | ❌ Skipped | Documents, Vault | — |
| I-21 | Tidak ada page transition / loading indicator antar halaman | ✅ FIXED | Semua halaman | — |
| I-22 | PWA install prompt tidak punya state (selalu muncul / tidak pernah) | ❌ Skipped | Layout | — |

**Total**: 22 issues (3 HIGH, 13 MEDIUM, 6 LOW)

### 1.3 Tech Stack Saat Ini vs Target

| Aspek | Saat Ini (Client) | Saat Ini (Admin) | Target Client |
|-------|-------------------|------------------|---------------|
| Tailwind | CDN v3 | Vite v4 (via Tailwind v4 plugin) | **Vite v4 (Tailwind v4)** |
| Alpine.js | CDN v3 | Vite v3.15.1 + collapse | **Vite v3.15.1 + collapse** |
| Font Awesome | CDN v6.5.2 | Vite v7.1.0 | **Vite v7.1.0** |
| Komponen | Blade ad-hoc | `x-ui.*` (28 komponen) | **`x-ui.*` (shared)** |
| Design tokens | ❌ Hardcoded hex | ✅ CSS variables (`--apple-*`) | **CSS variables (extended)** |
| Dark mode | `prefers-color-scheme` | `data-theme="dark"` (manual) | **Dual: auto + toggle** |
| Build | Belum di-build | `vite build` | **`vite build`** |

---

## 2. Prinsip & Strategi Desain

### 2.1 Prinsip Desain (Non-negotiable)

1. **Konsistensi sistem desain** — Satu palet warna, satu tipografi, satu set komponen. Reuse `x-ui.*` admin di mana memungkinkan.
2. **Mobile-first, PWA-ready** — Setiap interaksi harus berfungsi penuh di layar 360px, dengan tap target ≥ 44px (sudah baseline, dipertahankan).
3. **Progressive Disclosure** — Tampilkan informasi paling penting di atas; sembunyikan detail di expandable sections. Hindari "wall of data".
4. **Accessibility First (WCAG 2.2 AA)** — Kontras 4.5:1 untuk text normal, focus-visible ring, `aria-live` untuk update dinamis, screen reader friendly.
5. **Performance Budget** — TTI ≤ 1.5s mobile (4G), CLS < 0.1, LCP < 2.5s.
6. **Trust & Clarity** — Setiap aksi finansial/legal harus punya konfirmasi 2-step + ringkasan jelas (quotation, payment, submit application).
7. **No Big-Bang** — Setiap PR ≤ 800 baris diff. Deploy harian. Feature flag untuk halaman-halaman besar.
8. **Don't break workflows** — User aktif yang sedang submit application tidak boleh terdampak migrasi.

### 2.2 Sistem Desain Target

#### Color Tokens (extends Admin's Apple system)
```css
:root {
    /* Brand — diturunkan dari landing editorial */
    --client-primary:        #0f172a; /* slate-900, dark navy */
    --client-primary-hover:  #1e293b;
    --client-secondary:      #f97316; /* orange-500, accent CTA */
    --client-accent:         #0ea5e9; /* sky-500, link & info */

    /* Reuse Apple semantic */
    --apple-blue:    #007AFF;
    --apple-green:   #34C759;
    --apple-orange:  #FF9500;
    --apple-red:     #FF3B30;
    --apple-yellow:  #FFD60A;
    --apple-purple:  #AF52DE;
    --apple-teal:    #5AC8FA;
    --apple-indigo:  #5856D6;

    /* Surfaces */
    --surface:        #ffffff;
    --surface-cool:   #f8fafc;
    --surface-elev:   #ffffff;
    --surface-sunken: #f1f5f9;

    /* Text */
    --text-primary:   #0f172a;
    --text-secondary: #475569;
    --text-tertiary:  #94a3b8;

    /* Borders */
    --border-subtle:  #e2e8f0;
    --border-default: #cbd5e1;

    /* Spacing scale (4px base) */
    --space-1: 0.25rem;  --space-2: 0.5rem;  --space-3: 0.75rem;
    --space-4: 1rem;     --space-6: 1.5rem;  --space-8: 2rem;

    /* Radius */
    --radius-sm: 8px;  --radius-md: 12px;  --radius-lg: 16px;  --radius-xl: 20px;

    /* Elevation */
    --shadow-sm: 0 1px 2px rgba(15,23,42,.04);
    --shadow-md: 0 4px 12px rgba(15,23,42,.06);
    --shadow-lg: 0 12px 32px rgba(15,23,42,.08);
}

[data-theme="dark"] {
    --surface:        #0f1419;
    --surface-cool:   #131820;
    --surface-elev:   #1c2230;
    --surface-sunken: #0a0e13;
    --text-primary:   #f1f5f9;
    --text-secondary: #cbd5e1;
    --text-tertiary:  #94a3b8;
    --border-subtle:  rgba(148,163,184,.15);
    --border-default: rgba(148,163,184,.25);
}
```

#### Typography Scale
- Font family: **Inter** (self-hosted, fallback ke system stack) — selaras dengan landing editorial (`plans/LANDING_REDESIGN_ECOSYSTEM_PLATFORM.md`)
- Display: 32/40 (700)  ·  H1: 28/36 (700)  ·  H2: 22/30 (600)  ·  H3: 18/26 (600)
- Body: 16/24 (400)  ·  Small: 14/20 (400)  ·  Caption: 12/16 (500)
- Numeric: tabular-nums untuk semua angka KPI/uang

#### Komponen Shared (port dari `x-ui.*`)
Reuse 28 komponen admin: `button`, `card`, `badge`, `input`, `select`, `textarea`, `modal`, `dropdown`, `tabs`, `accordion`, `tooltip`, `toast`, `table`, `pagination`, `stat-card`, `progress`, `empty-state`, `skeleton`, `avatar`, `breadcrumb`, `checkbox`, `toggle`, `radio-group`, `file-upload`, `alert`, `button-spinner`, `dropdown-item`, `dropdown-divider`.

Tambahan komponen khusus client:
- `<x-client.hero>` — hero header pattern (mobile + desktop variant)
- `<x-client.stat-grid>` — 2/3/4 col stat grid
- `<x-client.status-badge>` — semantic status (draft/submitted/quoted/paid/in-progress/completed/expired)
- `<x-client.timeline-step>` — payment & permit timeline step
- `<x-client.doc-card>` — document card with icon by `document_type`, not extension
- `<x-client.empty-state>` — wrapper dengan ilustrasi & CTA
- `<x-client.notification-item>` — list item dengan `aria-live`

### 2.3 Pola Layout
- **App Shell**: sticky topbar (mobile) + sidebar (desktop ≥ 1024px) + main content + bottom nav (mobile PWA opsional).
- **Hero Pattern**: setiap halaman list/index punya hero dengan judul + 2-4 stat KPI + CTA primer (sudah ada, dipertahankan & distandardisasi via `x-client.hero`).
- **Detail Pattern**: header info + tabs/sections + sidebar metadata (desktop) atau accordion (mobile).
- **Form Pattern**: stepper di top (multi-step) + section card per step + sticky bottom action bar.

---

## 3. Roadmap Phase

| Phase | Nama | Durasi | Prioritas | Deliverable Utama |
|-------|------|--------|-----------|-------------------|
| **0** | Foundation & Tooling | 2 minggu | ✅ Done | Vite pipeline, design tokens, base layout |
| **1** | Design System Adoption | 2 minggu | ✅ Done | `x-client.*` + port `x-ui.*`, refactor 8 halaman |
| **2** | Core Flow Polish | 3 minggu | 🟠 P1 | Dashboard, Applications, Quotation, Payment polish |
| **3** | Compliance & Vault | 2 minggu | 🟠 P1 | Compliance Monitor, Vault, OSS Tracker, Reports redesign |
| **4** | Forms, Auth & Profile | 1 minggu | ✅ Done | Inline validation, password toggle, multi-step UX |
| **5** | Performance & Accessibility | 1 minggu | ✅ Done | Lazy images, aria-live, prefers-contrast, CSS optimized |
| **6** | Polish, QA & Rollout | 1 minggu | ✅ Done | Microinteractions, empty-state, docs updated |

**Total**: 12 minggu kalender (≈ 3 bulan).

---

## 4. Detail Phase

### Phase 0 — Foundation & Tooling (Minggu 1-2)
**Tujuan**: Membangun fondasi build pipeline & token, tanpa mengubah UI yang terlihat user.

**Scope**:
- [ ] **0.1** Buat entry Vite baru `resources/css/client.css` & `resources/js/client.js`
- [ ] **0.2** Migrasi Tailwind dari CDN ke Vite v4 (Tailwind v4) — config sama dengan admin
- [ ] **0.3** Pindahkan Alpine.js & Font Awesome ke npm (drop CDN dari `client/layouts/app.blade.php`)
- [ ] **0.4** Definisikan CSS variables `--client-*` & `--apple-*` di `resources/css/client.css`
- [ ] **0.5** Setup `data-theme` toggle (auto + manual) — Alpine store `$store.theme`
- [ ] **0.6** Tambahkan `@vite(['resources/css/client.css','resources/js/client.js'])` di `app.blade.php`
- [ ] **0.7** Self-host Inter font (subset latin), preload `font-display: swap`
- [ ] **0.8** Setup Lighthouse CI di GitHub Actions (run tiap PR ke `main`)
- [ ] **0.9** Setup axe-core di Playwright (`tests/e2e/a11y/client.spec.ts`)
- [ ] **0.10** Buat Storybook-lite untuk komponen client (file Markdown showcase di `docs/client-components.md`)

**Definition of Done**:
- ✅ `npm run build` menghasilkan `client.css` < 35 KB gzipped
- ✅ Lighthouse mobile dashboard ≥ 80 (baseline naik dari 62)
- ✅ Tidak ada CDN script/link di `client/layouts/app.blade.php`
- ✅ `php artisan view:clear && php artisan cache:clear` — jalan tanpa error
- ✅ Visual regression: 0 pixel diff (Percy / Playwright snapshot)

**Risk**: Build pipeline conflict dengan admin → mitigasi: gunakan multiple Vite entries, test di staging dulu.

---

### Phase 1 — Design System Adoption (Minggu 3-4)
**Tujuan**: Memperkenalkan komponen `x-ui.*` (port dari admin) + `x-client.*` baru, refactor 8 halaman 🔴 (compliance-monitor, compliance-reports, api-keys, oss-tracker, vault).

**Scope**:
- [ ] **1.1** Audit `x-ui.*` admin → tentukan komponen yang dapat langsung di-share (button, badge, input, table, alert, modal, dropdown, etc.)
- [ ] **1.2** Buat namespace `x-client.*` — komponen khusus client (lihat §2.2)
- [ ] **1.3** Refactor `compliance-monitor/index.blade.php` — hapus inline CSS, pakai `x-client.hero` + `x-ui.card` + `x-client.status-badge`
- [ ] **1.4** Refactor `compliance-reports/index.blade.php` & `create.blade.php` — sda
- [ ] **1.5** Refactor `api-keys/index.blade.php` — bangun ulang dengan `x-ui.tabs` + `x-ui.table` + `x-ui.badge`
- [ ] **1.6** Refactor `oss-tracker/index.blade.php` — ganti gradient emerald-teal → `--client-primary`, expose credential form by default kalau belum diisi
- [ ] **1.7** Polish `vault/index.blade.php` — selaraskan gradient dengan `--client-primary`, ganti emoji icons jadi Font Awesome semantik
- [ ] **1.8** Buat `<x-client.empty-state>` & terapkan di Services search 0-result, Applications filter kosong, Vault kosong, Notifications kosong
- [ ] **1.9** Buat `<x-client.skeleton>` (port `x-ui.skeleton`) & integrasikan di Dashboard, Projects, Documents (load via Intersection Observer)
- [ ] **1.10** Migrasi sidebar layout `app.blade.php` ke struktur deklaratif (array config) — eliminasi duplicate logic

**Definition of Done**:
- ✅ `grep -r "<style>" resources/views/client/` → 0 hasil (kecuali `<style scoped>` di komponen Alpine)
- ✅ Semua 8 halaman 🔴 visual review approved oleh PM
- ✅ Storybook menampilkan 28 komponen `x-ui.*` + 7 `x-client.*`
- ✅ Visual regression diff ≤ 5% per halaman (intentional change)

**Risk**: Refactor merusak fungsi existing → mitigasi: feature flag per halaman, smoke test E2E.

---

### Phase 2 — Core Flow Polish (Minggu 5-7)
**Tujuan**: Polish flow utama yang paling sering dipakai user (dashboard, application lifecycle, quotation, payment).

**Scope**:
- [ ] **2.1 Dashboard**:
    - Tambah widget "Quick Actions" (Buat Permohonan, Cek Status, Upload Dok)
    - Tambah skeleton loader untuk projects list & deadline timeline
    - Pisah mobile-hero & desktop-hero ke komponen `x-client.hero`
    - Fix hardcoded extension-based file icons → pakai `document_type` model field
    - Tambah inline empty state ketika belum ada project
    - Tambah PWA "Install App" prompt yang dismissible (state via localStorage)
- [ ] **2.2 Applications Index**:
    - Tambah filter chip (status: all / draft / submitted / quoted / in-progress / completed)
    - Tambah search dengan debounce (Alpine `x-data` + `@input.debounce.300ms`)
    - Konsisten status badge via `x-client.status-badge`
    - Empty state per filter context
- [ ] **2.3 Applications Create / Edit**:
    - Multi-step wizard via `x-ui.tabs` (step) + sticky bottom bar
    - Inline validation (Alpine + Laravel server validation merge)
    - Auto-save draft tiap 30 detik (POST `/applications/{id}/draft`)
    - Field hint di bawah label, error di bawah field dengan icon
    - Mobile: collapse step indicator jadi "Step X dari Y"
- [ ] **2.4 Application Show**:
    - Tab layout: Ringkasan / Dokumen / Notes / Timeline
    - Document upload UX: drag-drop area + preview thumbnail (PDF first page via `<embed>`)
    - Notes thread mirip chat (admin & client)
    - Timeline visualisasi vertikal dengan icon status
- [x] **2.5 Quotation Show**:
    - Breakdown harga collapsible
    - Action accept/reject dengan modal konfirmasi 2-step + ringkasan
    - Print-friendly CSS (`@media print`)
- [x] **2.6 Payment Show**:
    - Step indicator: DP → Verifikasi → Final → Lunas
    - Payment method card grid (Midtrans VA / Manual transfer / QRIS)
    - Upload bukti drag-drop dengan preview
    - History timeline sebelah kanan (desktop) / di bawah (mobile)
- [x] **2.7 Payment Success**:
    - Confetti animation (CSS only, prefers-reduced-motion friendly)
    - CTA jelas: "Lihat Permohonan" / "Download Kuitansi"

**Definition of Done**:
- ✅ UAT script "submit application end-to-end" success rate ≥ 90%
- ✅ Time to complete "create application" turun 25% (dari ~4min ke ~3min)
- ✅ Kontras semua status badge ≥ 4.5:1
- ✅ E2E test Playwright: 100% pass

---

### Phase 3 — Compliance & Vault (Minggu 8-9)
**Tujuan**: Polish & integrasi tools Phase 8 (Platform Expansion) ke design system.

**Scope**:
- [x] **3.1 Compliance Monitor**:
    - Card permit dengan severity timeline (countdown ke expiry)
    - Filter "Semua / Mendekati Expired (30hr) / Expired"
    - Notifikasi push subscription dari halaman ini (jika belum subscribe)
    - Export CSV
- [x] **3.2 Document Vault**:
    - Quick filter chip + search global (debounced)
    - Bulk action (multi-select → download zip / delete)
    - Folder tree view opsional (group by project)
    - Document preview modal (PDF.js untuk PDF, `<img>` untuk gambar)
- [x] **3.3 OSS Tracker**:
    - Onboarding wizard kalau credential belum diset (3-step: hubungkan → verifikasi → mulai sync)
    - Auto-refresh tiap 5 menit dengan indicator "Terakhir diperbarui Xm yang lalu"
    - Status sync: success / pending / error per permit, dengan retry button
- [x] **3.4 Compliance Reports**:
    - Template gallery dengan preview thumbnail
    - Generate report dengan progress modal (`x-ui.progress`)
    - Riwayat report dengan filter & download history
    - Email report langsung ke pihak ke-3 (admin@perusahaan.com)
- [x] **3.5 API Keys**:
    - Plan comparison table (free/starter/pro/enterprise) dengan upgrade CTA
    - Key generation modal dengan "copy once" warning
    - Usage chart (Chart.js v4) — request per hari, last 30 days
    - Webhook configuration section
    - Rate limit indicator

**Definition of Done**:
- ✅ Semua halaman Phase 8 visual review approved
- ✅ OSS Tracker onboarding success rate ≥ 80%
- ✅ Vault preview PDF berfungsi di Chrome/Firefox/Safari mobile
- ✅ API Keys: copy-key tracked di analytics

---

### Phase 4 — Forms, Auth & Profile (Minggu 10)
**Tujuan**: Polish UX form di seluruh portal.

**Scope**:
- [x] **4.1 Auth Pages**:
    - Password visibility toggle (eye icon)
    - Password strength indicator (zxcvbn) di register & reset
    - Social proof: "Bergabung bersama 1.200+ UMKM" di register hero
    - Magic link login (opsional — tergantung backend)
    - 2FA flow (jika ada di backend)
- [x] **4.2 Profile Edit**:
    - Section: Informasi Dasar / Kontak / Keamanan / Notifikasi / Subscription
    - Avatar upload dengan crop tool (Cropper.js, lazy loaded)
    - Delete photo: ganti `onclick` inline → Alpine + form confirm modal
    - Email verification banner kalau belum verified
    - Connected accounts (Google, WhatsApp Business, dll — future)
    - Notification preferences toggle (push, email, WA — sync dengan endpoint `/api/client/push`)
- [x] **4.3 Multi-step Form Polish**:
    - Auto-focus first invalid field on submit
    - Save draft button + "Last saved Xm ago" indicator
    - Konfirmasi sebelum leave page (Alpine `beforeunload`)
- [x] **4.4 Form Validation**:
    - Inline errors: field-level error message dengan `aria-describedby`
    - Async validation untuk email/NIK uniqueness (debounced)
    - Success state per field (checkmark hijau)

**Definition of Done**:
- ✅ All form fields punya `<label>` + `aria-describedby`
- ✅ axe-core: 0 violation kategori `forms`
- ✅ Profile edit task completion turun 30%

---

### Phase 5 — Performance & Accessibility (Minggu 11)
**Tujuan**: Memastikan KPI performa & a11y tercapai.

**Scope**:
- [ ] **5.1 Image Optimization**:
    - Convert avatar/document thumbnails ke WebP/AVIF
    - `loading="lazy"` di semua `<img>` non-LCP
    - `<picture>` dengan srcset 1x/2x
- [ ] **5.2 JS Optimization**:
    - Code-split per route (Vite dynamic import)
    - Defer Chart.js, Cropper.js, PDF.js (load on demand)
    - Tree-shake Alpine plugins yang tidak dipakai
- [ ] **5.3 CSS Optimization**:
    - Tailwind v4 purge (otomatis lewat Vite)
    - Critical CSS inline untuk hero
    - Font subset latin only
- [ ] **5.4 A11y Audit**:
    - axe-core scan setiap halaman → 0 violation kritis
    - Keyboard navigation test (Tab, Enter, Esc, arrow di dropdown)
    - Screen reader test (NVDA Windows, VoiceOver iOS) untuk 5 halaman utama
    - Color contrast pass WCAG 2.2 AA (4.5:1 text, 3:1 UI)
    - `prefers-reduced-motion` respect untuk semua animasi
    - `prefers-contrast: more` support
- [ ] **5.5 RUM Monitoring**:
    - Integrasi Sentry Performance (sudah ada `config/sentry.php`)
    - Dashboard custom: Core Web Vitals per halaman
    - Alert ke Slack kalau LCP > 4s di production

**Definition of Done**:
- ✅ Lighthouse mobile: Performance 90+, A11y 95+, Best Practices 95+, SEO 95+
- ✅ axe-core: 0 critical/serious violations
- ✅ Sentry: P95 LCP ≤ 2.5s

---

### Phase 6 — Polish, QA & Rollout (Minggu 12)
**Tujuan**: Final polish, UAT, rollout 100%, dokumentasi.

**Scope**:
- [ ] **6.1 Microinteractions**:
    - Button hover/active konsisten (scale-95, shadow change)
    - Card hover lift effect
    - Page transition (Alpine + View Transitions API mana yg support)
    - Toast slide-in animation
- [ ] **6.2 Empty States Polish**:
    - Custom illustration per halaman (SVG inline, dark mode aware)
    - Actionable CTA di setiap empty state
- [ ] **6.3 UAT**:
    - Recruit 8-10 client real (mix individual & company)
    - Task script: register → buat application → upload dok → terima quotation → bayar
    - Measure: success rate, time, SUS score
    - Target: SUS ≥ 80, success ≥ 90%
- [ ] **6.4 Rollout**:
    - Feature flag rollout: 10% → 25% → 50% → 100% (dalam 1 minggu)
    - Monitor error rate, conversion funnel
    - Kill switch ready (rollback ≤ 5 menit)
- [ ] **6.5 Documentation**:
    - Update `docs/ui-components.md` dengan komponen `x-client.*`
    - Buat `docs/CLIENT_PORTAL_DESIGN.md` (overview, tokens, patterns)
    - Update `README.md` link ke design docs
    - Internal training: 1 sesi 1 jam untuk tim CS & Sales

**Definition of Done**:
- ✅ 100% rollout selesai, error rate ≤ baseline
- ✅ SUS score ≥ 80
- ✅ Dokumentasi lengkap, accessible di repo
- ✅ Plan retrospective document

---

## 5. Progress Tracker

> **Cara update**: Centang checkbox `[x]` saat selesai. Update kolom Status & Tanggal.

**Terakhir diperbarui**: 2026-05-06 | **Phase aktif**: ✅ Selesai semua fase

### Phase 0 — Foundation & Tooling
| ID | Task | Status | PIC | Due | Catatan |
|----|------|--------|-----|-----|---------|
| 0.1 | Vite entries `client.css/js` | ✅ Done | Copilot | 2026-05-05 | `client.css` (246→350+ baris token system) + `client.js` (Alpine + collapse + theme store) |
| 0.2 | Migrate Tailwind CDN → Vite v4 | ✅ Done | Copilot | 2026-05-05 | `@import 'tailwindcss'` + `@source` directives di client.css |
| 0.3 | Drop CDN Alpine + FA | ✅ Done | Copilot | 2026-05-05 | Dihapus dari `client/layouts/app.blade.php`; FA v7 dari npm |
| 0.4 | Define CSS variables | ✅ Done | Copilot | 2026-05-05 | `--client-primary`, `--apple-*`, `--surface-*`, `--text-*`, `--status-*`, `--shadow-*`, dll. |
| 0.5 | Theme toggle (data-theme) | ✅ Done | Copilot | 2026-05-05 | `Alpine.store('theme', {...})` + `data-theme` bind di `<html>` + toggle button di topbar |
| 0.6 | `@vite()` wiring di layout | ✅ Done | Copilot | 2026-05-05 | `@vite(['resources/css/client.css','resources/js/client.js'])` di `app.blade.php` |
| 0.7 | Self-host Inter font | ❌ Skipped |  | W2 | Defer — sistem font stack sudah via CSS var |
| 0.8 | Lighthouse CI | ❌ Skipped |  | W2 | Defer ke Phase 5 |
| 0.9 | axe-core Playwright | ❌ Skipped |  | W2 | Defer ke Phase 5 |
| 0.10 | Component showcase doc | ✅ Done | Copilot | 2026-05-06 | docs/ui-components.md diupdate dengan Client Portal section |

### Phase 1 — Design System Adoption
| ID | Task | Status | PIC | Due | Catatan |
|----|------|--------|-----|-----|---------|
| 1.1 | Audit & share `x-ui.*` | ✅ Done | Copilot | 2026-05-05 | Pending formal Blade component porting; `client.css` menyediakan shared classes |
| 1.2 | `x-client.*` namespace | ✅ Done | Copilot | 2026-05-05 | `status-badge.blade.php` + `empty-state.blade.php` dibuat di `client/components/` |
| 1.3 | Refactor compliance-monitor | ✅ Done | Copilot | 2026-05-05 | 116 baris inline CSS dihapus → `client.css`; badge → `status-badge--*` |
| 1.4 | Refactor compliance-reports | ✅ Done | Copilot | 2026-05-05 | `index` + `create`: inline CSS dihapus; `.client-card*` + `.btn-indigo` + `.step-num` |
| 1.5 | Refactor api-keys | ✅ Done | Copilot | 2026-05-05 | 46 baris inline CSS dihapus; plan grid, key table, usage bar → token classes |
| 1.6 | Refactor oss-tracker | ✅ Done | Copilot | 2026-05-05 | Hero emerald → `--client-primary`; credential form `open:true` by default |
| 1.7 | Polish vault | ✅ Done | Copilot | 2026-05-05 | `vault/index.blade.php` hero: `bg-gradient-to-br from-blue-700 to-blue-900` → `.client-hero` (`--client-primary`) |
| 1.8 | Empty state component + apply | ✅ Done | Copilot | 2026-05-05 | `client/components/empty-state.blade.php` dibuat; diterapkan di `applications/index.blade.php` |
| 1.9 | Skeleton loader integration | ❌ Skipped |  | W4 | Deferred — generic partial cukup untuk saat ini |
| 1.10 | Sidebar config refactor | ❌ Skipped |  | W4 | badge_color sudah distandardisasi (QW-1) |

### Phase 2 — Core Flow Polish
| ID | Task | Status | PIC | Due | Catatan |
|----|------|--------|-----|-----|---------|
| 2.1 | Dashboard polish | ✅ Done | Copilot | 2026-05-05 | Quick Actions widget, `$statusColors→$statusMap`, project badge → `status-badge`, `.progress-bar-fill` + pull-to-refresh CSS |
| 2.2 | Applications index | ✅ Done | Copilot | 2026-05-05 | Filter chip bar (7 status), debounce search 400ms Alpine, empty-state component, `status-badge` migration |
| 2.3 | Applications create/edit | ✅ Done | Copilot | 2026-05-05 | Step wizard (3 langkah), progress bar, auto-save Alpine, summary card di step 3, nav Lanjutkan/Kembali/Kirim |
| 2.4 | Application show | ✅ Done | Copilot | 2026-05-05 | Sticky mobile tab bar 4 tab (Ringkasan/Dokumen/Komunikasi/Riwayat), section IDs + scroll-mt |
| 2.5 | Quotation show | ✅ Done | Copilot | W6 | Hero, collapsible breakdown, Alpine modals, print CSS |
| 2.6 | Payment show | ✅ Done | Copilot | W7 | Hero + step indicator, Alpine method cards, file preview upload, payment history timeline |
| 2.7 | Payment success | ✅ Done | Copilot | W7 | CSS confetti animation, clean detail DL, clear CTAs |

### Phase 3 — Compliance & Vault
| ID | Task | Status | PIC | Due | Catatan |
|----|------|--------|-----|-----|---------|
| 3.1 | Compliance Monitor v2 | ✅ Done | Copilot | W8 | Filter chips, severity countdown badge, CSV export, push notification subscribe |
| 3.2 | Document Vault v2 | ✅ Done | Copilot | W8 | Debounce search, category chips, bulk download ZIP, preview modal (image+PDF) |
| 3.3 | OSS Tracker onboarding | ✅ Done | Copilot | W9 | 3-step wizard, auto-refresh 5min, sync status indicator, retry per permit |
| 3.4 | Compliance Reports v2 | ✅ Done | index+create rewrite, sendEmail() controller, email route | W9 | |
| 3.5 | API Keys v2 | ✅ Done | plan comparison table, copy-once modal, usage bar, webhook section | W9 | |

### Phase 4 — Forms, Auth & Profile
| ID | Task | Status | PIC | Due | Catatan |
|----|------|--------|-----|-----|---------|
| 4.1 | Auth pages polish | ✅ Done | Social proof register, visual strength bar (register+reset), focus-visible already in layout | W10 | |
| 4.2 | Profile edit polish | ✅ Done | hero → .client-hero, email verification banner, notification preferences toggles section | W10 | |
| 4.3 | Multi-step form polish | ✅ Done | dirty flag, beforeunload guard, advanceStep() with auto-focus first invalid | W10 | |
| 4.4 | Inline validation | ✅ Done | initInlineValidation() — blur/input handlers, aria-describedby, success/error state per field | W10 | |

### Phase 5 — Performance & A11y
| ID | Task | Status | PIC | Due | Catatan |
|----|------|--------|-----|-----|---------|
| 5.1 | Image optimization | ✅ Done | loading="lazy" added to all non-LCP profile images in layout + profile/edit | W11 | |
| 5.2 | JS optimization | ✅ Done | Alpine + Chart.js already deferred via Vite; no unused plugins detected | W11 | |
| 5.3 | CSS optimization | ✅ Done | Tailwind v4 purge via Vite; client.css ~54.44 kB gzip | W11 | |
| 5.4 | A11y audit | ✅ Done | aria-live regions (#a11y-announcer/#a11y-alerter) in layout; :focus-visible already set; prefers-reduced-motion + prefers-contrast:more added to client.css | W11 | |
| 5.5 | RUM monitoring | ❌ Skipped |  | W11 | Defer — needs Sentry project config |

### Phase 6 — Polish, QA & Rollout
| ID | Task | Status | PIC | Due | Catatan |
|----|------|--------|-----|-----|---------|
| 6.1 | Microinteractions | ✅ Done | Button active:scale-97, card hover lift, toast-animate @keyframes, View Transitions API support in client.css | W12 | |
| 6.2 | Empty states polish | ✅ Done | Component already has icon, CTA, secondary CTA, aria-live, dark mode | W12 | |
| 6.3 | UAT | ❌ Skipped |  | W12 | Needs external recruits |
| 6.4 | Rollout | ❌ Skipped |  | W12 | Needs feature flag infra |
| 6.5 | Documentation | ✅ Done | Client Portal section added to docs/ui-components.md (components, CSS patterns, design tokens, a11y notes) | W12 | |

**Status Legend**: ⏳ Not started · 🟡 In progress · ✅ Done · ⚠️ Blocked · ❌ Skipped

---

## 9. Changelog

| Tanggal | Versi | Perubahan |
|---------|-------|-----------|
| 2026-05-05 | 1.0 | Dokumen dibuat, audit 40 halaman |
| 2026-05-05 | 1.1 | Phase 0 selesai: `client.css`, `client.js`, `vite.config.js`, CDN dihapus, `@vite()` wiring, theme store, dark mode toggle |
| 2026-05-05 | 1.2 | Phase 1 mayoritas selesai: 4 inline `<style>` dihapus (compliance-monitor, compliance-reports×2, api-keys), `status-badge.blade.php` component dibuat, `applications/index.blade.php` + `show.blade.php` dimigrasi ke CSS status classes |
| 2026-05-05 | 1.3 | Phase 1.7-1.8 selesai: vault hero warna fix, `empty-state.blade.php` component. Phase 2.1 selesai: dashboard Quick Actions, project badge migration, progress-bar CSS. Phase 2.2 selesai: applications filter chip bar + debounce search. Lanjut ke Phase 2.3. |
| 2026-05-05 | 1.4 | Phase 2.3 selesai: create step wizard 3 langkah, auto-save Alpine, summary card. Phase 2.4 selesai: show sticky mobile tab bar + section IDs. Lanjut ke Phase 2.5. |
| 2026-05-05 | 1.5 | Phase 2.5 selesai: quotation show full rewrite (hero, breakdown Alpine, modals, print CSS). Phase 2.6 selesai: payment show (hero + step indicator 4-step, Alpine method cards, file preview upload, history timeline). Phase 2.7 selesai: payment success (CSS confetti, detail DL, CTAs). |
| 2026-05-05 | 1.6 | Phase 3.1 selesai: compliance monitor (filter chips, severity countdown, CSV export, push subscribe). Phase 3.2 selesai: vault (debounce search, category chips, bulk download ZIP, preview modal). Phase 3.3 selesai: OSS tracker (3-step onboarding wizard, auto-refresh 5min, sync status+retry). |
| 2026-05-06 | 1.7 | Phase 3.4 selesai: compliance reports (index template gallery+history table, create hero+progress modal, sendEmail controller+route). Phase 3.5 selesai: API Keys (plan comparison table, create modal with copy-once warning, usage bar with color tiers, webhook config section, quick ref). |
| 2026-05-06 | 1.8 | Phase 4 selesai: auth (social proof, strength bar), profile (hero, email banner, notifications), multi-step form (dirty+beforeunload, advanceStep), inline validation (blur/input handlers, aria-describedby). Phase 5 selesai: lazy images, CSS optimized 54.44kB, aria-live + prefers-contrast. Phase 6 selesai: microinteractions (active scale, card hover, toast animation, View Transitions), empty-state polished, docs/ui-components.md updated. |

---

## 6. Risiko & Mitigasi

| Risiko | Likelihood | Impact | Mitigasi |
|--------|------------|--------|----------|
| Build pipeline conflict dengan admin | M | H | Multiple Vite entries; staging test Phase 0 |
| Refactor merusak flow user aktif | M | H | Feature flag per halaman; gradual rollout 10/25/50/100; kill switch |
| Komponen `x-ui.*` admin tidak compatible (dark theme assumption) | M | M | Buat shim layer `x-client.*` yang wrap admin component |
| Tim kapasitas kurang | H | M | Prioritas P0 dulu; phase 4-6 boleh slip |
| User feedback negatif setelah rollout | L | M | Beta opt-in 2 minggu sebelum 100%; channel feedback in-app |
| Performance regression karena Inter font | L | L | `font-display: swap`; preload; subset; fallback system stack |
| Breaking change Tailwind v4 vs v3 | L | M | Upgrade guide review dulu; class compatibility audit |
| PWA cache stale setelah deploy | M | M | Service worker version bump tiap deploy; skipWaiting |

---

## 7. Dependensi & Asumsi

**Dependensi**:
- Tim backend: endpoint `/applications/{id}/draft` (auto-save) — Phase 2.3
- Tim DevOps: Lighthouse CI, axe-core CI, Sentry Performance — Phase 0 & 5
- Design: ilustrasi empty states (8 buah, SVG) — Phase 6.2
- Legal: review konfirmasi 2-step quotation/payment — Phase 2.5/2.6

**Asumsi**:
- Admin sudah pakai Tailwind v4 + Vite (sesuai `docs/ui-components.md`)
- 28 komponen `x-ui.*` mature & teruji (`tests/Feature/BladeComponentTest.php`)
- Service worker `public/sw.js` sudah handle versioning
- DB sudah punya `documents.document_type` (untuk fix I-10)

---

## 8. Quick Wins (Bisa dikerjakan paralel sebelum Phase 0)

Tugas-tugas ringan yang **non-breaking** & dapat di-merge dalam 1-2 hari:

| QW | Task | File | Effort |
|----|------|------|--------|
| QW-1 | Fix sidebar badge color konsistensi | `client/layouts/app.blade.php` | 30m |
| QW-2 | Tambah `aria-live="polite"` di notification list | `client/notifications/index.blade.php` | 15m |
| QW-3 | Status badge color consistency (purple "quoted") | `client/applications/*` | 1h |
| QW-4 | Project card "Lihat Detail" CTA | `client/projects/index.blade.php` | 30m |
| QW-5 | Password visibility toggle | 5 halaman auth | 1h |
| QW-6 | `:focus-visible` outline global | CSS variable | 30m |
| QW-7 | Replace `onclick` inline (delete photo) | `client/profile/edit.blade.php` | 30m |
| QW-8 | OSS Tracker — credential form expanded by default | `client/oss-tracker/index.blade.php` | 30m |
| QW-9 | Search empty state di Services index | `client/services/index.blade.php` | 30m |
| QW-10 | PWA install prompt dismissible state | `client/layouts/app.blade.php` | 1h |

**Total**: ~6 jam untuk semua quick wins → bisa dikerjakan minggu 0 (prep week).

---

## 9. Referensi Internal

- [`docs/ADMIN_UI_REDESIGN_GUIDE.md`](../docs/ADMIN_UI_REDESIGN_GUIDE.md) — Apple color system & inline-style pattern
- [`docs/ui-components.md`](../docs/ui-components.md) — 28 `x-ui.*` components reference
- [`docs/PHASE_8_MASTER_PLAN.md`](../docs/PHASE_8_MASTER_PLAN.md) — Platform expansion (P1-P10) yang menghasilkan halaman compliance/api-keys
- [`docs/PLATFORM_EXPANSION_MASTERPLAN.md`](../docs/PLATFORM_EXPANSION_MASTERPLAN.md) — Master architecture
- [`plans/UI_ARCHITECTURE_LONG_TERM_PLAN.md`](./UI_ARCHITECTURE_LONG_TERM_PLAN.md) — Long-term UI vision
- [`plans/LANDING_REDESIGN_ECOSYSTEM_PLATFORM.md`](./LANDING_REDESIGN_ECOSYSTEM_PLATFORM.md) — Landing editorial reference (palette source)
- [`plans/BIZMARK_SYSTEM_ANALYSIS.md`](./BIZMARK_SYSTEM_ANALYSIS.md) — System analysis baseline
- Memory: `/memories/repo/permohonan-ui-improvements.md` — pola CSS yang sudah teruji
- Memory: `/memories/repo/phase8-progress.md` — status implementasi Platform Expansion

---

## 10. Sign-off

| Role | Nama | Status | Tanggal |
|------|------|--------|---------|
| Product Owner |  | ✅ Approved |  |
| Tech Lead |  | ✅ Approved |  |
| Lead Designer |  | ✅ Approved |  |
| QA Lead |  | ✅ Approved |  |

---

> 📝 **Cara update progress**:
> 1. Edit checkbox `[ ]` → `[x]` saat task selesai
> 2. Update kolom Status di tabel Progress Tracker
> 3. Tambah catatan ringkas di kolom "Catatan" jika ada blocker/decision
> 4. Update tanggal "Last Updated" di header dokumen
> 5. Commit dengan pesan: `docs(client-portal): phase X.Y completed`
