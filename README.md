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
- Bootstrap 5 (beberapa view legacy), SCSS
- AOS (animate on scroll), Font Awesome 7

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
├── views/              # 386 blade files across 100 subfolders
├── js/ css/ sass/

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

Dokumen planning aktif di `docs/`:

- `SEO_MASTER_PLAN.md` — SEO domination roadmap
- `SMART_SEO_DOMINATION_PLAN.md` — Strategy companion
- `PHASE_8_MASTER_PLAN.md` — Current phase roadmap
- `ADMIN_DESIGN_COMPACT_PLAN.md` + `ADMIN_PANEL_RESTRUCTURE_PLAN.md`
- `MOBILE_COMPREHENSIVE_ANALYSIS.md`
- `RTRW_INTEGRATION_PLAN.md`

Dokumen historis (dark mode guide, security guide, color/CSS token guide,
RAG integration, business analysis, PWA wireframes, dll.) berada di
`docs/archive/` — tidak di-track git tetapi tetap tersedia lokal untuk referensi.

---

## License

Proprietary — © BizMark.ID. Tidak untuk didistribusikan tanpa izin.
