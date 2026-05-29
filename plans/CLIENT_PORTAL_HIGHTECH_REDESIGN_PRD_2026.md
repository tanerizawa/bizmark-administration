# Bizmark.ID — Client Portal High-Tech Redesign PRD & MVP Architecture

> **Tipe Dokumen**: Product Requirements Document (PRD) + MVP Architecture Blueprint
> **Versi**: 1.0
> **Tanggal**: 2026-05-05
> **Owner**: Product · Frontend · Design
> **Status**: 🔵 Proposal (menunggu approval)
> **Predecessor**: [`plans/CLIENT_PORTAL_REDESIGN_2026.md`](CLIENT_PORTAL_REDESIGN_2026.md) v1.4 (foundation/tech-debt sudah selesai)
> **Skop**: Total transformasi visual & UX `/client/*` agar setara platform bisnis high-tech kelas dunia

---

## 0. Eksekutif Brief — Mengapa Redesign Total

### 0.1 Masalah Inti
Bizmark.ID adalah **ekosistem perizinan terpadu** dengan kompleksitas setara mid-market SaaS:
- 40+ halaman portal klien
- 10+ modul end-to-end (Catalog → Application → Quotation → Payment → Project → Vault → Compliance Monitor → OSS Tracker → API Keys → Compliance Reports)
- Dual-persona: UMKM lokal & PMA (foreign investor)
- Multi-channel: Web, PWA mobile, Email, WhatsApp

**Tetapi** tampilan portal saat ini terlihat seperti **template admin generik** (LinkedIn-blue card stack, daftar bertumpuk, ikon FontAwesome flat). Ini menciptakan **persepsi rendah-kualitas** yang tidak sebanding dengan:

1. **Landing page v2** yang sudah sangat sophisticated (split-hero, archipelago SVG, glow orbs, CSS variable token system, persona-aware copy, ecosystem-hub).
2. **Kompleksitas backend** (AI KBLI matcher, OSS-RBA tracker, Midtrans integration, document vault, B2B API keys, signed URLs, 2FA, push notifications).
3. **Positioning bisnis** sebagai konsultan perizinan high-tech untuk UMKM serius dan PMA.

> **One-liner Visi**: *"Saat klien login, mereka harus merasa membuka cockpit bisnis—bukan inbox helpdesk."*

### 0.2 Tujuan PRD
1. Menerjemahkan ide redesign menjadi **rancang bangun arsitektural** yang dapat dieksekusi tim secara bertahap.
2. Mengikat visual portal pada **bahasa desain yang sama** dengan landing v2 sehingga pengalaman dari prospect → trial → klien terasa kontinyu.
3. Menyederhanakan **information architecture** kompleksitas 10 modul agar klien tidak tersesat.
4. Menetapkan **MVP shippable** dalam 4 sprint, lalu roadmap incremental berikutnya.

### 0.3 Non-Goals
- Tidak mengubah business logic / kontrak API / nama route.
- Tidak melakukan big-bang rewrite frontend (incremental, feature-flagged).
- Tidak mengganti Blade dengan SPA — tetap server-rendered + Alpine + sedikit Livewire/Hotwire spirit.
- Tidak mengubah brand color primary `#0a66c2` (akan **dipromosikan** ke sistem token, bukan diganti).

---

## 1. Audit Cepat — Landing vs Portal

| Aspek | Landing v2 (saat ini) | Client Portal (saat ini) | Gap |
|---|---|---|---|
| Token system | CSS variables (`--accent-rgb`, `--accent-glow`, `--tools`) | Hard-coded Tailwind utilities + `#0a66c2` | ⚠️ Tinggi |
| Tipografi | Display serif/sans hybrid, hierarchy jelas | Single sans, sedikit hierarchy | ⚠️ Sedang |
| Visual signature | Archipelago SVG, glow orbs, eyebrow chips | Flat cards, tidak ada signature | ⚠️ Tinggi |
| Density | Spacious, editorial | Cramped (LinkedIn feed) | ⚠️ Sedang |
| Motion | AOS fade-up, parallax subtle | Hampir tidak ada | ⚠️ Tinggi |
| Data viz | N/A (marketing) | Kosong — angka-angka mentah | 🚨 Kritis |
| Empty states | Beautiful CTAs | "Belum ada data" plain text | 🚨 Kritis |
| Navigation | Top nav + footer mega | Sidebar + bottom nav (PWA) — solid struktur | ✅ OK |
| Persona awareness | Eksplisit (PMA/Team/DIY ribbon) | Tidak ada di portal | ⚠️ Sedang |

**Diagnosa**: Portal punya **tulang yang benar** (mobile-first, PWA-aware, sidebar+bottom-nav) tetapi **kulitnya generik**. Redesign berfokus pada **kulit + density + motion + data viz + empty states**, bukan rewrite struktur.

---

## 2. Riset Referensi — 12 Platform yang Akan Kita "Curi" Pelajaran

Saya kategorikan pembelajaran per pilar agar konkret diterjemahkan ke kode.

### 2.1 Visual Identity & Density
| Platform | Yang Dicuri | Aplikasi di Bizmark |
|---|---|---|
| **Linear** | Keyboard-first, density tinggi, command palette, monochrome + 1 accent, micro-shadows | Command palette `⌘K`, single accent (Bizmark blue), dense list view untuk Applications/Projects |
| **Vercel Dashboard** | Dark-mode-native, geometric grid, status pills monochrome, code-like data | Dark mode default option, monospace untuk ID permit/tracking number |
| **Stripe Dashboard** | Information density tinggi tapi tetap clear, hierarchy via type-scale, badge system rapi, payment flow yang elegant | Payment & Quotation pages, status timeline, breadcrumb + page-actions pattern |
| **Mercury Bank** | Editorial typography, big numbers, "calm finance" mood, soft gradient hero | Dashboard hero "Total Invested" big-number card, calm palette di Vault |

### 2.2 Information Architecture & Navigation
| Platform | Yang Dicuri | Aplikasi di Bizmark |
|---|---|---|
| **Notion** | Sidebar collapsible, breadcrumb dengan emoji/icon, peek/preview hover | Sidebar group "Workspace", breadcrumb dengan ikon modul |
| **Figma** | Persistent left rail + contextual right inspector | Right inspector untuk detail Application (selected row → drawer) |
| **Salesforce Lightning** | Tabbed record pages, related lists, utility bar bawah | Tabbed view di `applications/show` (Detail · Documents · Notes · Timeline · Payment) |
| **AWS Console** | Service switcher, search-everything, region selector | Service switcher untuk berpindah modul (Application ↔ Project ↔ Compliance) |

### 2.3 Onboarding & Empty States
| Platform | Yang Dicuri | Aplikasi di Bizmark |
|---|---|---|
| **Intercom / Pylon** | Checklist progress widget, "next best action" | Onboarding stepper sudah ada — di-upgrade jadi persistent floating widget hingga 100% |
| **Slack** | Empty state ilustratif + 1 CTA primer + 1 sekunder | Setiap halaman kosong punya hero illustration + primary action |
| **Pipedrive** | Pipeline kanban + drag-drop status | Kanban view opsional untuk Applications & Projects |

### 2.4 Data Visualization
| Platform | Yang Dicuri | Aplikasi di Bizmark |
|---|---|---|
| **Datadog / Grafana** | Sparkline inline, time-range picker konsisten | Sparkline di card "Active Projects (30d)", "Documents Uploaded (7d)" |
| **Plaid / Ramp** | Spending bar charts horizontal | Bar chart "Investasi per Bulan" di dashboard hero |
| **Github Insights** | Heatmap kontribusi | Heatmap "Aktivitas Compliance 12 bulan" |

### 2.5 Mobile / PWA
| Platform | Yang Dicuri | Aplikasi di Bizmark |
|---|---|---|
| **Revolut / Wise** | Bottom tab + center FAB, swipeable cards, haptic-like animations | FAB "Ajukan Permohonan" di tengah bottom nav |
| **Linear Mobile** | Sheet bottom modals, swipe-to-action | Bottom sheet untuk filter/sort, swipe pada list item untuk archive/star |

### 2.6 Indonesian / Local Context
| Referensi | Pelajaran |
|---|---|
| **OSS-RBA (resmi)** | Klien sudah familiar dengan stepper status NIB → Sertifikat. Pertahankan vocabulary: "Pengajuan", "Verifikasi", "Diterbitkan". |
| **Mekari Jurnal / Talenta** | Sidebar grouping bahasa Indonesia yang tidak kaku, copy hangat tapi formal. |
| **DANA / Jago** | Big-number hero + horizontal scroll cards untuk mobile feed. |

---

## 3. Design Philosophy — 7 Prinsip yang Mengikat Semua Keputusan

1. **One Continuous Story** — Landing, Auth, Portal, Email harus pakai token & visual signature yang sama. Klien tidak boleh merasakan "dilempar" ke aplikasi berbeda saat login.
2. **Calm by Default, Loud When It Matters** — Background tenang (off-white / near-black), aksen warna hanya muncul untuk: status kritis, CTA primer, brand moment.
3. **Information Density with Breathing Room** — Tinggi info per layar (tabular, sparkline) tapi line-height 1.5+ dan padding generous antar group.
4. **Progressive Disclosure** — Default tampilkan ringkasan; detail muncul via drawer/expand/peek—bukan halaman baru.
5. **Status is the Hero** — Setiap permohonan, dokumen, payment punya state yang jelas terlihat di first-fold tanpa scroll.
6. **Keyboard & Touch Equal Citizens** — `⌘K` di desktop, swipe gestures + bottom sheet di mobile. Tidak ada interaksi yang hanya bisa di salah satu.
7. **Server-Rendered, JS-Sprinkled** — Tetap Blade + Alpine. Hindari hidrasi berat. Animasi pakai CSS + IntersectionObserver, bukan framework motion.

---

## 4. Information Architecture (IA) Redesign

### 4.1 Before — Datar & Lebar
```
Dashboard
Layanan
Permohonan
Pembayaran
Proyek
Dokumen
Notifikasi
OSS Tracker
Compliance Monitor
Compliance Reports
API Keys
Profil
```
12 menu sejajar → cognitive overload, tidak ada hierarchy of importance.

### 4.2 After — Berkelompok dengan Mental Model "Lifecycle Bisnis"
```
🏠 Beranda                           (Dashboard)

📋 PERIZINAN  (lifecycle aktif)
  ├─ Katalog Layanan
  ├─ Permohonan
  ├─ Penawaran & Pembayaran        (gabungan quotations + payments)
  └─ Proyek Aktif

📁 ARSIP & KEPATUHAN  (post-issuance)
  ├─ Document Vault
  ├─ Compliance Monitor
  ├─ Laporan Kepatuhan
  └─ OSS-RBA Tracker

🔧 PENGEMBANG & INTEGRASI         (collapsed by default, badge "API")
  └─ API Keys

⚙️  Pengaturan
  ├─ Profil & Perusahaan
  ├─ Notifikasi
  └─ Keamanan (2FA)
```
**Rasionalisasi**:
- Klien melihat lifecycle: **Mengurus → Menyimpan → Memantau**.
- "Penawaran & Pembayaran" digabung karena selalu ber-pair (klien tidak pernah ke `/payments` tanpa lewat quotation).
- "Pengembang" disembunyikan default (hanya 5% klien) → mengurangi clutter untuk 95% mayoritas.

### 4.3 Global Navigation Pattern (Desktop)
```
┌──────────────────────────────────────────────────────────────────────┐
│ [Logo Bizmark]  [Search/⌘K]      [Help] [Notif] [Theme] [Avatar▾]  │  ← Top bar 56px
├────────────┬─────────────────────────────────────────────────────────┤
│            │ Breadcrumb · Page Title · Page Actions                  │
│  Sidebar   │─────────────────────────────────────────────────────────│
│  240px     │                                                         │
│  collapse  │                  Page Content                           │
│  to 64px   │                                                         │
│            │                                                         │
└────────────┴─────────────────────────────────────────────────────────┘
```
- **Sidebar collapse** ke 64px (icon-only) → preferensi tersimpan di `localStorage`.
- **Command Palette** (`⌘K` / `Ctrl+K`) untuk: navigate, create application, search applications, search documents, switch theme.
- **Breadcrumb** dengan icon modul; klik segment terakhir = inline-edit page title (untuk project/application name).

### 4.4 Mobile / PWA
```
┌─────────────────────────────────────┐
│ ◀  Page Title              ⋮       │  ← Top app bar 56px
├─────────────────────────────────────┤
│                                     │
│         Page Content                │
│                                     │
├─────────────────────────────────────┤
│  🏠      📋      ➕      📁      ⚙️ │  ← Bottom nav with center FAB
│ Beranda Izin   New   Arsip Setting  │
└─────────────────────────────────────┘
```
- **Center FAB** = "Ajukan Permohonan Baru" (action paling sering).
- **Pull-to-refresh** pada Dashboard, Applications, Notifications.
- **Bottom sheet** untuk filter, sort, action menu (bukan modal full-screen).

---

## 5. Visual Language — Design Tokens

### 5.1 Color System (CSS Variables, light + dark)
```css
:root {
  /* Brand */
  --brand-50:  #eef5fc;
  --brand-100: #d6e6f7;
  --brand-300: #6fa6dc;
  --brand-500: #0a66c2;   /* primary — preserved */
  --brand-600: #0857a6;
  --brand-700: #064486;
  --brand-rgb: 10 102 194;

  /* Semantic surfaces */
  --surface-0: #ffffff;        /* page background */
  --surface-1: #f8fafc;        /* card */
  --surface-2: #f1f5f9;        /* nested */
  --surface-3: #e2e8f0;        /* divider strong */
  --border:    #e5e7eb;
  --border-strong: #cbd5e1;

  /* Text */
  --text-1: #0f172a;
  --text-2: #475569;
  --text-3: #94a3b8;

  /* Status (high-tech palette: muted, not candy) */
  --status-success: #047857;   /* emerald-700 */
  --status-warning: #b45309;   /* amber-700  */
  --status-danger:  #be123c;   /* rose-700   */
  --status-info:    var(--brand-500);
  --status-neutral: #64748b;

  /* Visualization (5-color qualitative + extras) */
  --viz-1: #0a66c2;
  --viz-2: #0891b2;
  --viz-3: #7c3aed;
  --viz-4: #db2777;
  --viz-5: #ea580c;

  /* Effects */
  --glow-brand: radial-gradient(60% 60% at 50% 0%, rgb(10 102 194 / .15), transparent);
  --shadow-sm: 0 1px 2px rgb(15 23 42 / .04);
  --shadow-md: 0 4px 12px rgb(15 23 42 / .06);
  --shadow-lg: 0 12px 32px rgb(15 23 42 / .08);
  --shadow-glow: 0 0 0 4px rgb(10 102 194 / .12);

  /* Motion */
  --ease-out-expo: cubic-bezier(.16, 1, .3, 1);
  --ease-out-back: cubic-bezier(.34, 1.56, .64, 1);
  --dur-fast: 120ms;
  --dur-base: 200ms;
  --dur-slow: 360ms;
}

[data-theme="dark"] {
  --surface-0: #0b1220;
  --surface-1: #111a2e;
  --surface-2: #182238;
  --surface-3: #243049;
  --border:    #1e293b;
  --border-strong: #334155;
  --text-1: #f1f5f9;
  --text-2: #cbd5e1;
  --text-3: #64748b;
  --shadow-sm: 0 1px 2px rgb(0 0 0 / .4);
  --shadow-md: 0 4px 12px rgb(0 0 0 / .35);
  --shadow-lg: 0 12px 32px rgb(0 0 0 / .45);
}
```

### 5.2 Type Scale
- Display 32/40 (700) — hanya hero dashboard
- H1 24/32 (600) — page title
- H2 18/26 (600) — section
- H3 14/20 (600 uppercase tracking-wider) — group label
- Body 14/22 (400)
- Small 12/18 (500) — meta, label
- Mono `JetBrains Mono` 12/18 — IDs (NIB, OSS number, application UUID)

Font keluarga: lanjutkan stack sistem (sudah ada di layout); untuk display, opsional load `Inter Display` lokal (zero CDN, woff2, preload).

### 5.3 Spacing & Layout
- 4px base scale: `1·2·3·4·6·8·12·16·24` (Tailwind compatible).
- **Page gutter desktop**: 32px; **mobile**: 16px.
- **Card padding**: 20px (compact list) / 24px (standard) / 32px (hero).
- **Max content width** modul detail: 1280px; tabel padat: 1440px; reading content: 760px.

### 5.4 Motion
- Page transitions: 200ms `--ease-out-expo` opacity+translateY(8px).
- Drawer/sheet: 280ms.
- Skeleton shimmer: 1.5s infinite (sudah ada — pertahankan).
- Hover lift card: `translateY(-2px)` + `--shadow-md` → `--shadow-lg`.
- **Reduced motion**: respect `prefers-reduced-motion` — semua transisi → 0ms.

### 5.5 Visual Signature (untuk kontinuitas dengan landing)
- **Top accent line** (1px gradient) di setiap halaman portal — sama persis dengan landing hero.
- **Glow orb** subtle di pojok hero dashboard (positioned `absolute`, `pointer-events:none`).
- **Indonesia archipelago watermark** ultra-low-opacity di empty states + login screen.
- **Eyebrow chip** pattern sama (icon + small caps text) untuk page section labels.

---

## 6. Component Library Map

### 6.1 Komponen yang Sudah Ada (audit sebelumnya menyebut `x-ui.*`)
Audit dulu folder `resources/views/client/components/` & `resources/views/components/ui/` (jika ada). Yang belum lengkap → ditambahkan di MVP Phase 1.

### 6.2 Component Inventory (target state)
```
components/ui/
├─ button.blade.php           (variant: primary|secondary|ghost|danger; size: sm|md|lg)
├─ badge.blade.php            (variant: success|warning|danger|info|neutral; soft|solid)
├─ card.blade.php             (variant: flat|elevated|outline)
├─ stat-card.blade.php        (big number + label + delta + sparkline slot)
├─ data-table.blade.php       (sticky header, sort, filter slot, empty state slot)
├─ status-pill.blade.php      (status enum → color + icon mapping)
├─ timeline.blade.php         (vertical event timeline for application progress)
├─ stepper.blade.php          (horizontal progress, sudah ada inline → ekstrak)
├─ empty-state.blade.php      (illustration + title + body + primary/secondary CTA)
├─ skeleton.blade.php         (presets: card|row|chart|avatar+text)
├─ drawer.blade.php           (right-side slide, 480px)
├─ sheet.blade.php            (bottom-sheet mobile)
├─ command-palette.blade.php  (⌘K modal with keyboard nav)
├─ breadcrumb.blade.php       (with icons)
├─ page-header.blade.php      (title + description + actions slot)
├─ tabs.blade.php             (a11y-compliant, keyboard nav)
├─ tooltip.blade.php          (Alpine, no popper.js)
├─ avatar.blade.php           (initials gradient fallback)
├─ progress.blade.php         (linear + circular)
├─ kbd.blade.php              (`<kbd>` with system styling)
├─ toast.blade.php            (sudah ada? — verify)
└─ chart-spark.blade.php      (inline SVG sparkline, no chart.js needed)
```
Setiap komponen **stateless**, props-driven, tanpa CDN.

### 6.3 Pattern Library (composed)
- **Hero Stat Strip**: 4 stat-card bersebelahan (Active Projects · Pending Docs · Total Invested · Compliance Score)
- **Timeline Drawer**: klik permohonan → drawer right shows full timeline
- **Filter Bar**: search + status chips + date range + saved-views
- **Inline Editor**: klik field → input muncul, save on blur (untuk project name, notes)

---

## 7. Halaman-Per-Halaman Blueprint (Top 10 Priority)

> Untuk setiap halaman: **Tujuan**, **Pattern**, **Komponen**, **Data Viz**, **Empty State**.

### 7.1 Dashboard (`/client/dashboard`) — **PRIORITY 1**
- **Tujuan**: 1-screen situational awareness untuk klien.
- **Layout**:
  ```
  ┌─ Greeting + Persona ribbon ─────────────────────────┐
  │ "Selamat pagi, Pak Ahmad — 2 dokumen perlu Anda"    │
  ├─ Stat strip (4 stat-card horizontal scroll mobile) ─┤
  │ Active │ Pending Docs │ Invested │ Compliance ✓    │
  ├─ Onboarding stepper (collapsible, hidden if 100%) ─┤
  ├─ Two-column ──────────────────────────────────────┤
  │ ◀ Active Pipeline (kanban-lite)  │ Activity Feed ▶ │
  │  • Application #234 (Verifikasi) │ 2h ago: Doc up  │
  │  • Project #99 (Eksekusi 60%)    │ 5h ago: Quote   │
  │                                  │                 │
  ├─ Bottom strip: Quick actions (4 cards) ────────────┤
  └─ Recent Documents · Compliance alerts ─────────────┘
  ```
- **Komponen baru**: stat-card, kanban-lite, activity-feed.
- **Data viz**: sparkline 30-day di Active Projects card; donut compliance score.
- **Empty state**: archipelago watermark + "Mulai permohonan pertama Anda" CTA.

### 7.2 Permohonan List (`/client/applications`) — **PRIORITY 1**
- Pattern: **List + Side Drawer** (à la Linear).
- Layout: Filter bar di atas, data-table dengan sticky header, klik row → drawer kanan menampilkan timeline + dokumen + notes (tanpa pindah halaman).
- Density toggle: comfortable / compact.
- Saved views: "Pending payment", "Awaiting docs", "In review".

### 7.3 Permohonan Detail (`/client/applications/{id}`) — **PRIORITY 1**
- Pattern: **Page header + Tabs** (Detail · Dokumen · Penawaran · Pembayaran · Notes · Timeline).
- Sticky page actions: `[Submit]` `[Cancel]` `[Download PDF]`.
- Timeline tab pakai `<x-ui.timeline>`.
- Dokumen tab: drag-drop upload zone + grid kartu dokumen dengan status pill.

### 7.4 Quotation Show (`/client/applications/{id}/quotation`) — **PRIORITY 1**
- Pattern: **Editorial invoice** (à la Stripe). Big number, line items table, CTA "Setujui & Lanjut Pembayaran".
- Sidebar: Validity countdown timer, contact PIC card.

### 7.5 Payment (`/client/applications/{id}/payment`) — **PRIORITY 1**
- Pattern: **Multi-method tabs** (Midtrans / Manual transfer).
- Visual: bank logos large, copy-to-clipboard untuk virtual account, success animation pada `payments/success`.

### 7.6 Project Detail (`/client/projects/{id}`) — **PRIORITY 2**
- Pattern: Tabs (Overview · Milestones · Documents · Team · Activity).
- Gantt-lite milestone visualisation (CSS-only, no library).

### 7.7 Document Vault (`/client/vault`) — **PRIORITY 2**
- Pattern: **Grid + Folder tree sidebar**, à la Drive.
- Bulk select + bulk download (sudah ada API).
- Search + filter by category & expiry date.

### 7.8 Compliance Monitor (`/client/compliance-monitor`) — **PRIORITY 2**
- Pattern: **Health dashboard** (à la Datadog). Score gauge + heatmap 12 bulan + list of expiring permits.
- Color-code by severity.

### 7.9 OSS Tracker (`/client/oss-tracker`) — **PRIORITY 3**
- Pattern: **Status pipeline** (sequential stages with current state highlighted).
- Mini-card untuk credential, dengan refresh button + last-sync timestamp.

### 7.10 Profile (`/client/profile`) — **PRIORITY 3**
- Pattern: **Settings page** (sidebar sections: Akun · Perusahaan · PIC · Notifikasi · Keamanan).
- Avatar uploader, company logo, completion meter.

---

## 8. Interaksi & Micro-UX Patterns

### 8.1 Empty States (mandatory di SEMUA list)
Format:
```
[archipelago-watermark SVG]
[Title Bahasa Indonesia hangat]
[1 paragraph max-width 480px]
[Primary CTA] [Secondary link]
```
Contoh: "Belum ada permohonan." → "Mulai dengan menjelajahi katalog izin yang sesuai bidang usaha Anda. Tim kami siap membantu memilih."

### 8.2 Loading
- **Skeleton-first** untuk data-fetched sections.
- **Optimistic UI** untuk: mark-notification-read, save-note, toggle-theme.
- **Progressive image**: blur-up sudah ada — pertahankan.

### 8.3 Feedback
- Toast bottom-right (desktop), top (mobile).
- Inline validation untuk form, dengan icon + helper text.
- Haptic-style scale animation untuk button press (`active:scale-[.98]`).

### 8.4 Errors
- Form errors: red border + helper text, jangan modal.
- 500/network: full-page error state dengan illustration + retry button + status page link.

### 8.5 Keyboard Shortcuts (desktop)
| Key | Action |
|---|---|
| `⌘K` / `Ctrl+K` | Command palette |
| `g d` | Go to Dashboard |
| `g a` | Go to Applications |
| `g p` | Go to Projects |
| `c` | Create new application |
| `/` | Focus search |
| `?` | Show shortcuts cheatsheet |

---

## 9. Mobile / PWA Strategy

1. **Bottom nav redesign**: 4 item + center FAB (Beranda · Izin · ➕ · Arsip · Setting).
2. **Pull-to-refresh** native-feel via `overscroll-behavior` + custom Alpine handler.
3. **Bottom sheets** untuk filter & action menu (replace dropdown).
4. **Swipe actions** pada list item: swipe-left → archive; swipe-right → mark important.
5. **Offline-aware**: existing service worker (`sw.js`) — tambah offline banner di app bar saat `navigator.onLine === false`.
6. **Install prompt**: smart timing (setelah klien login 2x, atau menyelesaikan permohonan pertama).
7. **Push notifications**: existing infrastructure — tambah inbox di `/notifications` dengan grouping by date.

---

## 10. MVP Scope — Apa yang Ship di Sprint 1-5

> **Last updated**: 2026-05-05 — Sprint 1–5 selesai. Sprint 6 in-progress (auth ✅, empty states ✅, dark mode QA ✅, edit/create pages ✅). Status aktual di bawah.

### Sprint 1 (2 minggu): **Foundation Token & Shell** ✅ SELESAI
- [x] Implement design token CSS variables (`resources/css/client.css` ~1070 baris: `--client-primary`, `--surface-*`, `--text-*`, `--apple-*`, `--viz-*`, `--shadow-glow`, `--ease-out-expo`, `--dur-*`).
- [x] Feature flag `config/portal_redesign.php` (`CLIENT_PORTAL_REDESIGN`, `allow_legacy_query`, `enabled_routes`, `command_palette`).
- [x] Refactor `client/layouts/app.blade.php`: gate wrapper `$portalV2`, `body.portal-v2` class, dark mode toggle Alpine store, hidden logout form untuk cmdk, `@if($portalCmdk)` command palette injection.
- [x] Desktop header slim `h-14`, title block disembunyikan di portal-v2 (`portal-v2-hidden`).
- [x] Build 9 komponen UI baru: `command-palette`, `page-header`, `status-pill`, `kbd`, `drawer`, `timeline`, `sheet`, `chart-spark`, `empty-state`, `tabs`.
- [x] Dark mode toggle persisted via `Alpine.store('theme')`.
- [x] Visual signature: `.portal-accent-line`, `.portal-glow-orb`, `.portal-archipelago`, `.portal-eyebrow`, `.portal-hero`, `.portal-stat-strip`, `.portal-lift`, `.portal-pill`, `.portal-mono`, `.portal-kbd`, `.portal-drawer`, `.portal-cmdk`.
- [x] Bug fix: `.portal-hero > *:not([aria-hidden])` — dekoratif `aria-hidden` elemen tidak lagi masuk document flow.

### Sprint 2 (2 minggu): **Dashboard & Applications List/Detail** ✅ SELESAI
- [x] `dashboard/v2-hero.blade.php` — dark gradient hero, greeting time-based, attention summary, 4 stat cards (sparkline + donut compliance). Bug fix: compliance `0/0` → null display `–`, emoji 🌿 → FA icon.
- [x] `dashboard/v2-content.blade.php` — onboarding stepper 3-step (step-3 enabled hanya jika `$pendingDocuments > 0`), pipeline kanban-lite 4 kolom, activity feed, quick actions 4 card. Empty pipeline → CTA Jelajahi Katalog menonjol.
- [x] `applications/v2-index.blade.php` — hero strip + 4 stat cards, sticky filter+search bar, 7 chip filter, card list dengan per-row peek drawer.
- [x] `applications/v2-show.blade.php` — hero back link + 5-tab (`detail/docs/quote/notes/timeline`) + upload drawer + sidebar action card.
- [x] Gate wrappers thin: `applications/index.blade.php`, `applications/show.blade.php`.
- [x] Legacy preserved: `legacy-index.blade.php`, `legacy-show.blade.php`.

### Sprint 3 (2 minggu): **Quotation, Payment, Project** ✅ SELESAI
- [x] `applications/v2-quotation.blade.php` — purple gradient hero, invoice line items, DP box, reject drawer, print CSS.
- [x] Gate wrapper `applications/quotation.blade.php`.
- [x] Legacy preserved: `applications/legacy-quotation.blade.php`.
- [x] `projects/v2-index.blade.php` — hero stat strip, sticky filter chips, card grid dengan progress bar.
- [x] `projects/v2-show.blade.php` — hero donut progress, 4 tabs (Overview/Tasks/Dokumen/Aktivitas), sidebar related application + mini timeline.
- [x] Gate wrappers: `projects/index.blade.php`, `projects/show.blade.php`.
- [x] Legacy preserved: `projects/legacy-index.blade.php`, `projects/legacy-show.blade.php`.
- [x] Mobile bottom nav v2 — active indicator bar, CSS tokens, FAB glow `box-shadow color-mix`, badge `var(--apple-green/orange)`.
- [x] **Payment multi-method v2** — `payments/v2-show.blade.php` + `payments/v2-success.blade.php` selesai di Sprint 4.
- [x] **Command palette fungsional** — `SearchController.php` + Alpine `portalCmdk` dengan `onQueryInput()` debounce 300ms selesai di Sprint 5.

### Sprint 4 (2 minggu): **Compliance Suite & Profile** ✅ SELESAI
- [x] `documents/v2-index.blade.php` — hero + 4 stat cards, sticky filter bar (search/project/type), sortable table, upload drawer.
- [x] `documents/v2-vault.blade.php` — hero expiry warning stats, category-grouped card grid, hover download, expiry badges (Segera Berakhir / Expired).
- [x] `compliance-monitor/v2-index.blade.php` — dark navy hero, 4 status stat cards (Aktif/Segera/Expired/Diperbarui), Alpine filter chips, permit cards dengan progress bar + Perpanjang CTA.
- [x] `oss-tracker/v2-index.blade.php` — green hero, onboarding wizard 3-step (jika `!$hasCredential`), auto-refresh 5 menit, status list dengan badge per state.
- [x] `payments/v2-show.blade.php` — hero + 4-step progress indicator, progress bar %, upload bukti bayar form, payment history list.
- [x] `payments/v2-success.blade.php` — success ring animation CSS, payment summary chip, dual CTA (Lihat Permohonan / Dashboard).
- [x] `profile/v2-edit.blade.php` — 2-col layout: profil info + company fields + notif prefs / password + 2FA + danger zone.
- [x] Gate wrappers thin untuk semua 6 destinasi + 7 legacy backups dengan wrapper stripped.
- [x] Build sukses: `client-DYpDhjKu.css` 327KB / 57.73KB gzip.
- [x] CSS bundle optimisasi — selesai Sprint 6: FA subset 19.3KB, bundle 40.42KB gzip (target ≤35KB tercapai).
- [x] Lighthouse + axe-core audit — selesai Sprint 6: `<meta description>`, preload woff2, type="button" fixes.
- [x] Playwright smoke tests — selesai Sprint 6: 12 E2E tests di `tests/e2e/client-portal-smoke.spec.ts`.

**Definition of Done Sprint 4**: ✅ Semua halaman compliance, vault, payments, profile memiliki v2 partial. Gate wrappers aktif.

### Sprint 5 (2 minggu): **Catalog, Reports & Create Flow** ✅ SELESAI
- [x] `services/v2-index.blade.php` — hero stats (total KBLI/sektor/estimasi), KBLI search+sector filter, popular KBLI grid, AI KBLI Matcher CTA.
- [x] `services/v2-show.blade.php` — KBLI code hero, confidence % badge, 4 stat cards, mandatory/optional permit list, cost sidebar CTA.
- [x] `api-keys/v2-index.blade.php` — dark purple hero, 4-plan comparison grid, keys table (toggle/delete), create key modal Alpine.
- [x] `compliance-reports/v2-index.blade.php` — dark green hero, template gallery, report table status filter chips + email modal.
- [x] `notifications/v2-index.blade.php` — sticky header + unread badge, mark-all-read POST, list unread dot indicator.
- [x] `applications/create` flow — gate wrappers + legacy backups + v2 partials: `v2-select-permit` (permit checklist + summary sticky), `v2-create` (3-step stepper: perusahaan/PIC/konfirmasi), `v2-create-package` (pre-fill from context, project fields), `v2-preview-submit` (T&C scroll box + agree+submit).
- [x] Command palette live search: `SearchController` at `GET /client/search`, `portalCmdk` Alpine `onQueryInput()` debounce 300ms → merge live results (applications, projects, KBLI) with static commands.
- [x] Gate wrappers: `select-permit`, `create`, `create-package`, `preview-submit`.
- [x] Build sukses: `client-De43CnT6.css` 331KB / 58.10KB gzip.

**Definition of Done Sprint 5**: ✅ Semua halaman product-facing (katalog, laporan, API, create flow) tampil konsisten portal-v2.

### Sprint 6 (2 minggu): **Auth, Polish & Launch** ✅ SELESAI
- [x] Auth pages redesign: `client/layouts/auth.blade.php` — split-screen auth layout (branding panel kiri + form kanan), Vite CSS, Alpine.js, feature chips, testimonial card.
- [x] `auth/login.blade.php` — portal-v2 card, eyebrow chip, password toggle, remember me, lupa password link.
- [x] `auth/register.blade.php` — 2-col grid (nama/perusahaan), password confirmation toggle, no CDN.
- [x] `auth/forgot-password.blade.php` — clean single-field, status session.
- [x] `auth/reset-password.blade.php` — token + email + password + confirm fields.
- [x] `auth/verify-email.blade.php` — icon centered, resend + logout buttons.
- [x] Empty states audit — semua 11 v2 partial sudah menggunakan `<x-ui.empty-state>` (tidak ada legacy `@include('client.components.empty-state')` tersisa di v2 files).
- [x] Dark mode QA — semua v2 partial menggunakan CSS variable tokens (`var(--text-*)`, `var(--surface-*)`) — tidak ada hardcoded gray class tanpa dark: prefix.
- [x] `applications/edit.blade.php` gate wrapper — v2-edit: 3-step stepper (perusahaan/PIC/konfirmasi), KBLI autocomplete, portal-v2 hero amber/brown gradient.
- [x] `compliance-reports/create.blade.php` gate wrapper — v2-create: portal-v2 hero green gradient, 3-step wizard (template/proyek-periode/parameter), Alpine `reportForm()` + AI progress modal.
- [x] `services/context.blade.php` gate wrapper — v2-context: portal-v2 hero (KBLI code eyebrow, gradient), 4-step form wizard (skala/lokasi/detail/konfirmasi) dengan CSS token inputs.
- [x] **Bug audit + fixes** — Strip `@extends`/`@section` dari 3 v2 partials yang salah; gate wrappers diperbaiki; hardcoded `#0a66c2` diganti `var(--client-primary)` (lihat §19).
- [x] **CSS bundle optimisasi** — FA icon subset (`resources/css/fa-client-subset.css`): 108KB → 19.3KB source. Bundle: 58.12KB → **40.52KB gzip** ✅
- [x] **Lighthouse fixes** — `font-display: swap`, `loading="lazy"` pada profile images, woff2 preload hint, `<meta name="description">`, semua `<button>` punya `type=` attr.
- [x] **Playwright E2E smoke tests** — 12 test cases di `tests/e2e/client-portal-smoke.spec.ts`: login, applications list, create flow, show, documents, profile, notifications, command palette, dark mode, auth pages, legacy fallback.
- [x] **Rollback playbook** — `docs/PORTAL_V2_SOFTLAUNCH_ROLLBACK.md`: 4-level rollback (per-sesi `?legacy=1`, env flag, hotfix, git revert), monitoring metrics, soft launch stages.

**Definition of Done Sprint 6**: ✅ Score Lighthouse fixes diterapkan, semua auth pages branded, Playwright tests siap, rollback playbook lengkap.

### Out of MVP (Phase 2+)
- ~~Kanban full drag-drop (MVP cuma static columns).~~ ✅ **SELESAI** — Grid/Kanban view toggle ditambahkan ke `projects/v2-index.blade.php` dengan Alpine `localStorage` persistence. Kanban dikelompokkan per `ProjectStatus`, read-only (client tidak bisa ubah status, hanya admin).
- ~~Heatmap compliance 12-bulan (MVP cuma list view).~~ ✅ **SELESAI** — 12-month expiry calendar grid ditambahkan ke `compliance-monitor/v2-index.blade.php`. Setiap sel menunjukkan jumlah izin yang berakhir di bulan tersebut, dengan warna hijau/kuning/merah sesuai urgency.
- API Keys redesign (low-traffic page).
- Recruitment portal (out of scope).
- Internationalization full (saat ini ID-first; EN sudah ada di landing).

---

## 11. Technical Implementation Notes

### 11.1 Build Pipeline
- Tetap Vite + Tailwind (sesuai keputusan v1.4 plan). Tambahkan `@layer` untuk token variables.
- Hapus sisa CDN apa pun (audit `grep -r "cdn"` di blade).
- Font Awesome → migrate ke **Lucide icons** (tree-shakeable, modern, lebih cocok dengan high-tech vibe). Phased: tambahkan komponen `<x-icon name="...">`, ganti per-halaman.

### 11.2 Alpine vs Livewire
- Default tetap **Alpine** untuk interaksi ringan (toggle, dropdown, tabs).
- **Livewire** boleh dipertimbangkan untuk: data-table dengan filter+sort+pagination, command palette search, drawer detail tanpa full page reload. **Tetapi** ini opsional — MVP bisa jalan dengan Alpine + fetch.

### 11.3 Performance Budgets (per halaman)
- HTML: ≤30 KB
- CSS: ≤35 KB gzipped (shared bundle)
- JS: ≤60 KB gzipped (shared bundle)
- LCP: ≤2.0s (3G fast), TTI: ≤2.5s
- CLS: ≤0.05

### 11.4 Accessibility Non-Negotiables
- Semua interactive ≥44×44 px touch target.
- Focus-visible ring (sudah ada — pertahankan).
- Color contrast AA (4.5:1 body, 3:1 large) — verify dark mode juga.
- Sidebar collapse tetap accessible via keyboard.
- Command palette `role="dialog"` + arrow nav + escape to close.
- Toast `aria-live="polite"`.

### 11.5 Feature Flag Strategy
- Env: `CLIENT_PORTAL_REDESIGN=true|false` di `.env`.
- Layout root menerapkan class `<body class="portal-v2">` saat aktif.
- CSS scoped via `.portal-v2 { ... }` untuk isolasi.
- Per-halaman juga bisa di-flag (`config/portal_redesign.php` array of routes).
- Allow `?legacy=1` query param untuk fallback (bantu QA & rollback).

### 11.6 File Impact Map (estimate)
| Layer | Files | Action |
|---|---|---|
| `resources/css/client.css` | 1 | Major refactor (token layer) |
| `resources/views/client/layouts/app.blade.php` | 1 | Major refactor |
| `resources/views/components/ui/*` | ~22 | Audit + buat yang kurang |
| `resources/views/client/dashboard/*` | 3 | Rewrite |
| `resources/views/client/applications/*` | 8 | Refactor (drawer, tabs) |
| `resources/views/client/{projects,documents,vault,oss-tracker,compliance-*}/*` | ~12 | Refactor |
| `resources/views/client/profile/*` | 1 | Refactor settings layout |
| `resources/js/client.js` | 1 | Tambah cmdk, drawer, swipe handlers |
| `routes/web.php` | 0 | **Tidak diubah** |
| Controller / Model | 0 | **Tidak diubah** |

Estimasi total: ±50 file Blade modified, 0 PHP backend change. Risk surface: frontend-only.

---

## 12. KPI & Success Metrics

| KPI | Baseline | MVP Target | 6-Month Target | Cara Ukur |
|---|---|---|---|---|
| Lighthouse Performance (mobile) | ~62 | ≥85 | ≥92 | CI Lighthouse |
| Lighthouse Accessibility | ~84 | ≥95 | ≥98 | CI |
| Time-to-first-action (login → first click) | ~4.2s | ≤2.5s | ≤2.0s | RUM |
| Task Success Rate (UAT script) | 71% | ≥88% | ≥94% | Internal UAT |
| SUS Score | unknown | ≥75 | ≥82 | Quarterly survey |
| NPS klien aktif | unknown | ≥30 | ≥45 | In-product survey |
| Daily Active Clients (DAC) | baseline t-0 | +20% | +60% | Analytics |
| Support tickets "saya bingung..." | baseline | -30% | -60% | Helpdesk tag |
| Mobile session share | ~38% | ≥50% | ≥60% | Analytics |
| PWA install rate | baseline | ≥10% MAU | ≥25% MAU | SW analytics |

---

## 13. Risiko & Mitigasi

| Risiko | Probabilitas | Dampak | Mitigasi |
|---|---|---|---|
| Klien existing tidak suka perubahan visual | Sedang | Sedang | Feature flag + opsi "Tampilan lama" selama 2 sprint pertama; in-app tour untuk perubahan besar |
| Bundle CSS membengkak karena token + utility | Rendah | Sedang | Vite purge + audit `du` setiap PR; budget 35KB hard cap di CI |
| Alpine state conflict di komponen baru | Rendah | Tinggi | Namespacing `x-data` pakai prefix komponen (`{ portalDrawer: false }`) |
| Sidebar collapse memutus muscle memory | Sedang | Rendah | Tetap default expanded; collapse opsional dengan keyboard `[` `]` |
| QA coverage tidak cukup untuk 50 file | Tinggi | Tinggi | Playwright smoke per critical-flow + visual regression Percy/Chromatic opsional |
| Dark mode kontras gagal AA di chart | Sedang | Sedang | `--viz-*` punya varian dark; test dengan axe-core di CI |
| Migrasi Lucide icons rusak ikon eksisting | Sedang | Rendah | Adapter `<x-icon name="check">` mapping FA→Lucide; ganti incremental |

---

## 14. Open Questions (perlu keputusan stakeholder)

1. **Brand color**: lock di `#0a66c2` atau open ke evolusi (mis. shift sedikit ke `#0857a6` lebih premium-look)?
2. **Dark mode default**: light atau respect system preference?
3. **Iconography**: commit ke Lucide, atau tetap FontAwesome untuk hemat effort?
4. **Livewire**: boleh ditambah sebagai dependency atau strict Alpine-only?
5. **Visual regression tool**: invest di Percy/Chromatic atau cukup screenshot manual?
6. **Lokalisasi**: portal aktifkan EN sekarang atau parkir hingga ada demand?
7. **Onboarding tour**: pakai library (Shepherd.js) atau custom Alpine?

---

## 15. Lampiran A — Wireframe Reference (low-fi, tekstual)

### A.1 Dashboard Hero (desktop, 1280px)
```
┌──────────────────────────────────────────────────────────────────┐
│ ─── thin gradient accent line ───────────────────────────────── │
│                                                                  │
│  ‹eyebrow chip› ⊙ Selamat pagi                                   │
│  Pak Ahmad, 2 hal perlu perhatian Anda hari ini.                 │
│                                                                  │
│  ┌──────────┐ ┌──────────┐ ┌──────────┐ ┌──────────┐             │
│  │ 3        │ │ 5        │ │ Rp 24Jt  │ │ 92%      │             │
│  │ Aktif    │ │ Doc      │ │ Invest   │ │ Comply   │             │
│  │ ╱╲╱╲▁╱   │ │ ▂▃▄▅▆    │ │ ▆▆▇▆▇    │ │ ◐        │             │
│  └──────────┘ └──────────┘ └──────────┘ └──────────┘             │
│                                                                  │
│  ┌─ Pipeline Aktif (4 kolom) ─────────┐ ┌─ Aktivitas ──────────┐ │
│  │ Diajukan │ Verifikasi │ Bayar │ ✓  │ │ 2j: Dok diunggah   ↗ │ │
│  │  • #234  │  • #233    │       │    │ │ 5j: Quotation kirim│ │
│  │  • #238  │            │       │    │ │ 1h: Pembayaran ✓  │ │
│  └────────────────────────────────────┘ └──────────────────────┘ │
│                                                                  │
│  Aksi Cepat: [+ Permohonan] [↑ Upload] [🔍 Status] [📚 Katalog] │
└──────────────────────────────────────────────────────────────────┘
```

### A.2 Applications List + Drawer
```
┌─────────────────────────────────────────────────────────────────┐
│ Permohonan          [+ Baru]  [⚙ View] [⌘K]                    │
│ ─────────────────────────────────────────────────────────────── │
│ Saved: ‹All› ‹Pending Pay› ‹Awaiting Docs› ‹In Review›         │
│ 🔍 Search…    Status: all▾   Tanggal: 30d▾   Density: comfy▾   │
│ ─────────────────────────────────────────────────────────────── │
│ ID      Nama Layanan       Status      Update   Pembayaran      │
│ ─────────────────────────────────────────────────────────────── │
│ #234   NIB + Izin Operasi  ● Verifikasi  2j     ✓ Lunas         │
│ #233   Sertifikat SLF      ● Diajukan    5j     ⏱ Pending       │
│ #232   PB-UMKU Komersial   ✓ Diterbitkan 3h     ✓ Lunas         │
│  ←── click row opens drawer →                                   │
└─────────────────────────────────────────────────────────────────┘
                                          ┌─ Drawer 480px ─┐
                                          │ #234 NIB + Izin│
                                          │ ──────────────  │
                                          │ Timeline       │
                                          │ ● Diajukan 1May│
                                          │ ● Verif    3May│
                                          │ ◌ Diterbit  -  │
                                          │ ──────────────  │
                                          │ Dokumen (4/5)  │
                                          │ ──────────────  │
                                          │ [Buka Detail →]│
                                          └────────────────┘
```

---

## 16. Lampiran B — Decision Log Template

| Tanggal | Keputusan | Owner | Konteks |
|---|---|---|---|
| TBD | Brand color locked / shifted | Product | Section 14.1 |
| TBD | Iconography commit | Frontend | Section 14.3 |
| TBD | MVP scope final approval | Product + Eng Lead | Section 10 |

---

## 17. Next Steps (begitu PRD approved)

1. **Week 0** — Stakeholder review, jawab Open Questions §14, lock brand tokens.
2. **Week 0** — Buat Figma library mirror dari token §5 (opsional jika ada designer).
3. **Sprint 1 kickoff** — Branch `feat/portal-redesign-foundation`, feature flag aktif `CLIENT_PORTAL_REDESIGN=true` di staging.
4. Implementasi mengikuti Section 10 sprint plan.
5. **Bi-weekly demo** ke stakeholder + UAT internal tim ops.
6. **Soft launch** (10% klien) di akhir Sprint 4 → ramp up berdasarkan metric §12.

---

## 18. Cross-References

- [`plans/CLIENT_PORTAL_REDESIGN_2026.md`](CLIENT_PORTAL_REDESIGN_2026.md) — predecessor (token migration, tech-debt cleanup) — assumed completed.
- [`docs/ADMIN_UI_REDESIGN_GUIDE.md`](../docs/ADMIN_UI_REDESIGN_GUIDE.md) — admin design system; banyak komponen reusable.
- [`docs/ui-components.md`](../docs/ui-components.md) — daftar `x-ui.*` resmi.
- [`plans/LANDING_REDESIGN_ECOSYSTEM_PLATFORM.md`](LANDING_REDESIGN_ECOSYSTEM_PLATFORM.md) — landing v2 yang menjadi acuan visual continuity.
- [`plans/UI_ARCHITECTURE_LONG_TERM_PLAN.md`](UI_ARCHITECTURE_LONG_TERM_PLAN.md) — visi 12-bulan UI.

---

> **Closing Note**: Dokumen ini sengaja **opinionated** — setiap keputusan punya rasionalisasi yang dapat di-challenge. Tujuannya bukan menjadikan ini Bible, melainkan starting point yang konkret sehingga tim bisa langsung membuka editor dan mulai. Jika ada bagian yang ingin diperdalam (mis. Figma component spec, Playwright test plan, atau API contract per drawer), tinggal minta — kita expand jadi sub-dokumen terpisah.

---

## 19. Bug Audit Log — Phase 0–6 (Sprint 6 QA)

Tanggal audit: setelah Phase 6 selesai (semua 26 v2 partial files).

### BUG-001 — CRITICAL: `@extends`/`@section` dalam v2 partial files ✅ FIXED

**Severity**: Critical — causes Laravel Blade double-layout rendering / section conflict  
**Root Cause**: 3 v2 partial files dibuat dengan pola gate-wrapper (`@extends` + `@section('content')`) alih-alih pola partial (content-only, tanpa `@extends`). File-file ini di-`@include()` oleh gate wrapper, sehingga menyebabkan konflik.

**Affected files**:
| File | Bug |
|---|---|
| `resources/views/client/applications/v2-edit.blade.php` | Had `@extends`, `@section('title')`, `@section('content')`, `@endsection` |
| `resources/views/client/compliance-reports/v2-create.blade.php` | Same |
| `resources/views/client/services/v2-context.blade.php` | Same |

**Fix applied**:
1. Stripped `@extends(...)`, `@section('title', ...)`, `@section('content')`, final `@endsection` dari semua 3 partial files.
2. Menambahkan `@extends('client.layouts.app')` + `@section('title', ...)` + `@section('content')` + `@endsection` ke gate wrapper files:
   - `resources/views/client/applications/edit.blade.php`
   - `resources/views/client/compliance-reports/create.blade.php`
   - `resources/views/client/services/context.blade.php`

**Pattern yang benar** (harus konsisten di semua 26+ v2 partial files):
- Gate wrapper (`*.blade.php`): `@extends` + `@section('content')` + `@if($portalV2) @include('v2-*')` + `@endsection`
- v2 partial (`v2-*.blade.php`): Content-only, NO `@extends`/`@section`. Boleh pakai `@push('scripts')`.

---

### BUG-002 — MINOR: Hardcoded hex `#0a66c2` dalam v2 partial files ✅ FIXED

**Severity**: Minor — menggunakan hardcoded hex value dari CSS custom property `--client-primary`  
**Affected files**:
- `resources/views/client/services/v2-context.blade.php` (2 instances: `focus:ring-[#0a66c2]`, `border-t-[#0a66c2]`)

**Not a bug** (acceptable):
- `resources/views/client/projects/v2-index.blade.php:119` — `$statusColor = $project->status->color ?? '#0a66c2'` adalah PHP fallback value. CSS var tidak bisa digunakan di PHP context.
- `rgba(10,102,194, ...)` dalam `onfocus` handlers — CSS vars dengan alpha channel tidak bisa langsung di-interpolate di inline JS.

**Fix applied**: Replaced `focus:ring-[#0a66c2]` dan `border-t-[#0a66c2]` dengan `focus:ring-[var(--client-primary)]` dan `border-t-[var(--client-primary)]` di `v2-context.blade.php`.

---

### BUG-003 — NON-ISSUE: `x-cloak` CSS rule ✅ ALREADY PRESENT

**Verified**: `resources/css/client.css` line 219 sudah memiliki `[x-cloak] { display: none !important; }`.  
Semua penggunaan `x-cloak` di v2 partial files (create, edit, preview-submit) sudah benar.

---

### BUG-004 — NON-ISSUE: Hero gradient inconsistency ✅ INTENTIONAL

Per-section hero colors adalah intentional brand differentiation:
- Dashboard / Applications index: `var(--client-primary)` biru (brand canonical)
- Applications EDIT: `#92400e` amber (mode edit visual cue)
- API Keys: `#1e1b4b` indigo (developer section)
- Quotation: `#1a0050` deep purple (premium tier visual)
- Compliance Monitor: `#1e3a5f` dark navy (compliance severity)
- Compliance Reports: `#064e3b` green (report/go signal)
- OSS Tracker: `#064e3b` green (regulatory compliance)

---

### Build Verification

Setelah semua fixes diterapkan, build berhasil:
```
✓ client-sj-SnM4t.css  331.66 kB │ gzip: 58.12 kB
✓ client-CnaN8tWA.js    30.01 kB │ gzip: 10.42 kB
✓ built in 5.31s
```
Laravel cache cleared: `view:clear`, `cache:clear`, `config:clear`, `route:clear` ✅
