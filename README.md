# BizMark.ID

Sistem manajemen bisnis terintegrasi untuk konsultan perizinan Indonesia.
Mengelola siklus lengkap proyek perizinan, finansial, SDM, legal, konten SEO,
dan layanan konsultasi berbasis AI dalam satu platform.

[![Laravel](https://img.shields.io/badge/Laravel-12.x-FF2D20.svg?logo=laravel)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.2+-777BB4.svg?logo=php)](https://php.net)
[![PostgreSQL](https://img.shields.io/badge/PostgreSQL-14+-336791.svg?logo=postgresql)](https://postgresql.org)
[![License](https://img.shields.io/badge/license-Proprietary-red.svg)](#license)

---

## Daftar Isi

- [Ringkasan](#ringkasan)
- [Modul Utama](#modul-utama)
- [Tech Stack](#tech-stack)
- [Struktur Proyek](#struktur-proyek)
- [Instalasi](#instalasi)
- [Development](#development)
- [Testing](#testing)
- [Deployment](#deployment)
- [Dokumentasi Tambahan](#dokumentasi-tambahan)

---

## Ringkasan

BizMark.ID adalah platform internal yang digunakan untuk operasi perusahaan
konsultan perizinan. Platform ini mencakup:

- **Manajemen Proyek & Perizinan** end-to-end (Project → Permit → Document).
- **Pipeline keuangan** (Quotation, Invoice, Payment, Bank Reconciliation).
- **HR & Recruitment** (Vacancy, Application, Interview, Test).
- **Content automation + SEO domination** (Article generation, Topic Cluster,
  Keyword Position tracking, Meta A/B test, IndexNow, Sitemap).
- **Email suite** (Inbox, Campaign, Template, Subscriber).
- **Landing page publik** dengan PWA mobile experience.
- **AI & RAG** untuk rekomendasi KBLI, cost estimator, dan konten.

### Skala Kodebase

| Area | Jumlah |
| --- | --- |
| Controllers | 129+ |
| Models | 93+ |
| Services | 49+ |
| Console Commands | 42+ |
| Views (Blade) | 386+ |
| Migrations | 150+ |

---

## Modul Utama

### Perizinan & Proyek
- `PermitType`, `PermitTemplate`, `PermitApplication`, `ProjectPermit`
- `Kbli`, `KbliPermitRecommendation`, integrasi `PerizinanAIService`
- Shapefile & RTRW integration (`ShapefileService`, RTRW proxy)

### Finansial
- `Quotation`, `Invoice`, `Payment`, `PaymentSchedule`
- `BankReconciliation`, `BankStatementEntry`, `CashAccount`
- `ProjectExpense`, `ExpenseCategory`, `TaxRate`, Midtrans gateway

### HR & Recruitment
- `JobVacancy`, `JobApplication`, `RecruitmentStage`
- `InterviewSchedule`, `InterviewFeedback`
- `TestTemplate`, `TestSession`, `TestAnswer`, `TechnicalTestSubmission`

### Content & SEO
- `Article`, `ArticleTopic`, `TopicCluster`, `KeywordCluster`
- `ArticleAutoPostService`, `TopicGenerationService`
- `SearchConsoleService`, `GoogleIndexingService`, `IndexNowService`
- `SchemaMarkupService`, `SitemapGeneratorService`
- `SeoScoringService`, `SeoFixService`, `MetaAbTestService`
- `CompetitiveIntelligenceService`, `ContentGapService`

### Email
- `EmailAccount`, `EmailInbox`, `EmailCampaign`, `EmailTemplate`
- `EmailSubscriber`, `EmailAssignment`, `EmailLog`
- IMAP/MIME parsing via `EmailMimeParser`

### AI
- `OpenRouterService` (multi-model via OpenRouter)
- `FreeAIAnalysisService`, `NeuroscienceService`
- `PerizinanAIService` (RAG atas regulasi)
- `ConsultationPricingEngine`, `ConsultantFeeCalculatorService`
- `SocialCaptionService`, `AttentionAnalyzer`

### Mobile / PWA
- Routes terpisah di `routes/mobile.php`
- Service worker di `public/sw.js`
- Push notifications via `laravel-notification-channels/webpush`
- Fallback: `public/offline.html`, `public/mobile-offline.html`

---

## Tech Stack

### Backend
- **Laravel 12** (PHP 8.2+)
- **PostgreSQL 14+**
- **Queue worker** via Supervisor
- DomPDF, PhpWord, PhpSpreadsheet, FastExcel
- Guzzle, smalot/pdfparser, gasparesganga/php-shapefile
- Midtrans PHP SDK, Spatie iCalendar, WebPush channel

### Frontend
- **Vite 7** + **Tailwind CSS 4**
- **Alpine.js 3** (+ collapse plugin)
- **Blade Component Library** — 27 `x-ui.*` components di [`resources/views/components/ui/`](resources/views/components/ui/)
- **Design Tokens (CSS Custom Properties)** — 3-layer token architecture di [`resources/css/design-tokens.css`](resources/css/design-tokens.css)
- Bootstrap 5 (beberapa view legacy), SCSS
- AOS (animate on scroll), Font Awesome 7

### Blade Component Library (`x-ui.*`)

27 komponen UI di [`resources/views/components/ui/`](resources/views/components/ui/), dipanggil dengan prefix `x-ui.*`:

**Tier 1 — Core:**
| Component | File | Deskripsi |
|-----------|------|-----------|
| [`x-ui.button`](resources/views/components/ui/button.blade.php) | `button.blade.php` | 5 variants, 3 sizes, link mode, loading state |
| [`x-ui.badge`](resources/views/components/ui/badge.blade.php) | `badge.blade.php` | 5 color variants, pill/dot, dot indicator |
| [`x-ui.card`](resources/views/components/ui/card.blade.php) | `card.blade.php` | 3 variants, 4 padding sizes, header/footer slots |
| [`x-ui.input`](resources/views/components/ui/input.blade.php) | `input.blade.php` | 7 types, leading/trailing icon, error state, helper text |
| [`x-ui.select`](resources/views/components/ui/select.blade.php) | `select.blade.php` | Native select + Alpine.js searchable variant |
| [`x-ui.textarea`](resources/views/components/ui/textarea.blade.php) | `textarea.blade.php` | Character counter via Alpine.js, resize control |
| [`x-ui.checkbox`](resources/views/components/ui/checkbox.blade.php) | `checkbox.blade.php` | 3 sizes, indeterminate state, description text |
| [`x-ui.toggle`](resources/views/components/ui/toggle.blade.php) | `toggle.blade.php` | Alpine.js switch, 4 color variants, 3 sizes |
| [`x-ui.alert`](resources/views/components/ui/alert.blade.php) | `alert.blade.php` | 4 variants, dismissible with Alpine.js, icon |
| [`x-ui.stat-card`](resources/views/components/ui/stat-card.blade.php) | `stat-card.blade.php` | Trend indicator, 5 color variants, icon |

**Tier 2 — Interactive (Alpine.js):**
| Component | File | Deskripsi |
|-----------|------|-----------|
| [`x-ui.modal`](resources/views/components/ui/modal.blade.php) | `modal.blade.php` | Alpine.js + x-teleport, 5 sizes, backdrop |
| [`x-ui.dropdown`](resources/views/components/ui/dropdown.blade.php) | `dropdown.blade.php` | Alpine.js, left/right align, transition |
| [`x-ui.tabs`](resources/views/components/ui/tabs.blade.php) | `tabs.blade.php` | Alpine.js, underline/pills variants, badge support |
| [`x-ui.table`](resources/views/components/ui/table.blade.php) | `table.blade.php` | Scoped slots per column, sortable, striped, hoverable |
| [`x-ui.pagination`](resources/views/components/ui/pagination.blade.php) | `pagination.blade.php` | Laravel Paginator, simple/full variants |
| [`x-ui.toast`](resources/views/components/ui/toast.blade.php) | `toast.blade.php` | Global notification via `$dispatch('toast', {...})` |
| [`x-ui.progress`](resources/views/components/ui/progress.blade.php) | `progress.blade.php` | Determinate + indeterminate, 5 color variants |
| [`x-ui.skeleton`](resources/views/components/ui/skeleton.blade.php) | `skeleton.blade.php` | text/circle/rect/card/table variants |

**Tier 3 — Layout & Navigation:**
| Component | File | Deskripsi |
|-----------|------|-----------|
| [`x-ui.breadcrumb`](resources/views/components/ui/breadcrumb.blade.php) | `breadcrumb.blade.php` | Chevron/slash/dot separators, home icon |
| [`x-ui.avatar`](resources/views/components/ui/avatar.blade.php) | `avatar.blade.php` | Image/initials/fallback, 5 sizes, status dot |
| [`x-ui.empty-state`](resources/views/components/ui/empty-state.blade.php) | `empty-state.blade.php` | Icon + title + description + action button |
| [`x-ui.radio-group`](resources/views/components/ui/radio-group.blade.php) | `radio-group.blade.php` | Default + card variants, option descriptions |
| [`x-ui.file-upload`](resources/views/components/ui/file-upload.blade.php) | `file-upload.blade.php` | Alpine.js drag & drop, accept/maxSize/maxFiles |
| [`x-ui.tooltip`](resources/views/components/ui/tooltip.blade.php) | `tooltip.blade.php` | 4 positions, configurable delay, arrow indicator |
| [`x-ui.accordion`](resources/views/components/ui/accordion.blade.php) | `accordion.blade.php` | Alpine.js + x-collapse, single/multiple modes |

### Design Tokens

3-layer CSS Custom Properties architecture di [`resources/css/design-tokens.css`](resources/css/design-tokens.css):

| Layer | Tujuan | Contoh |
|-------|--------|--------|
| Layer 1: Base | Raw brand values | `--color-primary: #5B8DBE` |
| Layer 2: Semantic | Context-mapped tokens | `--color-surface: var(--color-gray-50)` |
| Layer 3: Component | Component overrides | `--btn-primary-bg: var(--color-primary)` |

Dark mode: Admin menggunakan `[data-theme="dark"]` selector, Landing menggunakan `@media (prefers-color-scheme: dark)`.

### Dev Tools
- Laravel Pint (code style)
- PHPUnit 11
- Laravel Pail (log streaming)
- Sail / Docker (lihat `docker-compose.yml`)

---

## Struktur Proyek

```
app/
├── Console/Commands/   # 42+ artisan commands (SEO, backups, monitoring)
├── Http/Controllers/
│   ├── Admin/          # Dashboard admin
│   ├── Api/            # API endpoints (KBLI, Consultation, RTRW, Shapefile)
│   ├── Auth/           # Login terpisah (termasuk unified admin path)
│   ├── Landing/        # Landing page publik
│   └── Mobile/         # Endpoint PWA mobile
├── Models/             # 93 Eloquent models
├── Services/           # 49 domain services
├── Jobs/ Listeners/ Events/ Observers/ Policies/ Mail/ Notifications/
└── Helpers/MobileHelper.php

database/
├── migrations/         # 150+ migrations (start 2025-10)
├── seeders/
└── factories/

resources/
├── views/
│   ├── components/
│   │   └── ui/         # 27 Blade components (x-ui.* library)
│   ├── admin/          # Admin panel views
│   ├── landing/        # Landing page views
│   ├── layouts/        # Layout templates
│   └── ...             # 386+ blade files across 100 subfolders
├── css/
│   ├── design-tokens.css   # [NEW] 3-layer token architecture
│   ├── app.css              # Global styles + imports
│   ├── admin.css            # Admin overrides
│   ├── landing.css          # Landing overrides
│   └── landing-theme.css    # Legacy (to be deprecated)
├── js/ sass/

routes/
├── web.php             # 1.098 lines
├── console.php         # 390 lines (scheduled jobs)
├── mobile.php          # 220 lines
└── api.php             # 51 lines

docs/                   # Active planning docs (in-progress phases)
docs/archive/           # Historical / completed docs (gitignored)

storage/
├── app/                # Uploads, shapefiles, KBLI data
├── backups/            # DB backups (via scripts/db-backup.sh)
└── logs/

scripts/
├── db-backup.sh
└── db-restore.sh
```

---

## Instalasi

### Prasyarat
- PHP **8.2+** dengan ekstensi: `pgsql`, `gd`, `mbstring`, `xml`, `zip`, `bcmath`, `imap`
- **PostgreSQL 14+**
- **Node.js 18+** & npm
- Composer 2
- Supervisor (production, untuk queue worker)

### Setup

```bash
git clone <repo-url> bizmark.id
cd bizmark.id

composer install
npm install

cp .env.example .env
php artisan key:generate

# Konfigurasi database di .env, lalu:
php artisan migrate --seed

npm run build         # atau `npm run dev` untuk development
```

### Environment Variables Penting

Minimal yang perlu diisi di `.env`:

```env
APP_URL=http://localhost:8081

DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=bizmark_db
DB_USERNAME=...
DB_PASSWORD=...

# AI / Content
OPENROUTER_API_KEY=
PEXELS_API_KEY=
GOOGLE_SEARCH_CONSOLE_CREDENTIALS=

# Email (contoh Brevo)
MAIL_MAILER=smtp
MAIL_HOST=smtp-relay.brevo.com
MAIL_PORT=587
MAIL_USERNAME=...
MAIL_PASSWORD=...

# Midtrans (opsional)
MIDTRANS_SERVER_KEY=
MIDTRANS_CLIENT_KEY=
MIDTRANS_IS_PRODUCTION=false
```

---

## Development

Menjalankan stack dev (server + queue + logs + vite) sekaligus:

```bash
composer dev
```

Atau secara terpisah:

```bash
php artisan serve
php artisan queue:listen
php artisan pail        # log streaming
npm run dev
```

Perintah artisan yang sering dipakai:

```bash
# Content & SEO
php artisan articles:auto-post
php artisan seo:position-tracking
php artisan seo:trending-topics
php artisan seo:backlink-scan
php artisan indexnow:submit
php artisan topic-cluster:run
php artisan meta:ab-test
php artisan search-console:import

# Database
php artisan db:backup
php artisan db:monitor
php artisan migrate:fresh --safe
```

---

## Testing

```bash
composer test
# atau
php artisan test
```

Test suite ada di `tests/Feature/` dan `tests/Unit/` (PHPUnit 11).
Test environment menggunakan `.env.testing`.

---

## Deployment

### Docker

```bash
docker compose up -d
```

Konfigurasi di `docker-compose.yml` + `docker/nginx/`, `docker/searxng/`.

### Manual (server tradisional)

1. Deploy code (`git pull` / rsync)
2. `composer install --no-dev --optimize-autoloader`
3. `npm run build`
4. `php artisan migrate --force`
5. `php artisan config:cache route:cache view:cache event:cache`
6. Restart queue worker (`supervisorctl restart bizmark-worker:*`)
7. Reload PHP-FPM / Nginx

### Database Backup

Backup otomatis tersedia di `scripts/db-backup.sh` dan bisa dijalankan via cron atau
`php artisan db:backup`. Restore: `scripts/db-restore.sh`.

---

## Dokumentasi Tambahan

### Design System & UI

| Dokumen | Lokasi | Deskripsi |
|---------|--------|-----------|
| Design System V2 (Landing) | [`docs/DESIGN_SYSTEM.md`](docs/DESIGN_SYSTEM.md) | Landing page design tokens — Fraunces/Inter, navy/gold palette |
| UI Architecture Plan | [`plans/UI_ARCHITECTURE_LONG_TERM_PLAN.md`](plans/UI_ARCHITECTURE_LONG_TERM_PLAN.md) | 7-phase roadmap: token unification → component library → migration |
| Global Design Rules | [`.roo/rules/global-design-system.md`](.roo/rules/global-design-system.md) | Zero inline styles, zero hardcoded colors, component library mandate |
| Blade Component API | [`.roo/rules/blade-component-api.md`](.roo/rules/blade-component-api.md) | Component naming, props, slots, attributes forwarding |
| Tailwind CSS Usage | [`.roo/rules/tailwind-css-usage.md`](.roo/rules/tailwind-css-usage.md) | Tailwind v4 syntax, class order convention, micro-interactions |
| Alpine.js Patterns | [`.roo/rules/alpine-js-patterns.md`](.roo/rules/alpine-js-patterns.md) | Zero inline JS, component data, Pines UI integration |

### Planning & Roadmap

| Dokumen | Deskripsi |
|---------|-----------|
| `SEO_MASTER_PLAN.md` | SEO domination roadmap |
| `SMART_SEO_DOMINATION_PLAN.md` | Strategy companion |
| `PHASE_8_MASTER_PLAN.md` | Current phase roadmap |
| `ADMIN_DESIGN_COMPACT_PLAN.md` + `ADMIN_PANEL_RESTRUCTURE_PLAN.md` | Admin panel redesign |
| `MOBILE_COMPREHENSIVE_ANALYSIS.md` | Mobile / PWA analysis |
| `RTRW_INTEGRATION_PLAN.md` | RTRW spatial data integration |

Dokumen historis (dark mode guide, security guide, color/CSS token guide,
RAG integration, business analysis, PWA wireframes, dll.) berada di
`docs/archive/` — tidak di-track git tetapi tetap tersedia lokal untuk referensi.

---

## License

Proprietary — © BizMark.ID. Tidak untuk didistribusikan tanpa izin.
