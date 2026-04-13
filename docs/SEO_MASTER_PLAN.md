# BIZMARK.ID — SEO MASTER PLAN
## Strategi Komprehensif untuk Mendominasi Halaman 1 Google (Tanpa Iklan)

**Versi:** 2.0  
**Tanggal:** Juli 2026  
**Target:** Rank #1 untuk semua keyword izin lingkungan & perizinan usaha di Indonesia  
**Metode:** 100% Organik — Zero Paid Ads

---

## DAFTAR ISI

1. [Executive Summary](#1-executive-summary)
2. [Audit Kondisi Saat Ini](#2-audit-kondisi-saat-ini)
3. [Arsitektur SEO Engine](#3-arsitektur-seo-engine)
4. [Phase 1: Content Velocity — Ledakan Konten](#4-phase-1-content-velocity)
5. [Phase 2: Technical SEO Hardening](#5-phase-2-technical-seo-hardening)
6. [Phase 3: Topic Authority & Cluster Strategy](#6-phase-3-topic-authority)
7. [Phase 4: Distribution & Syndication](#7-phase-4-distribution--syndication)
8. [Phase 5: Competitive Intelligence & Monitoring](#8-phase-5-competitive-intelligence)
9. [Phase 6: Advanced Optimization](#9-phase-6-advanced-optimization)
10. [Open-Source Engine Stack](#10-open-source-engine-stack)
11. [Keyword Universe](#11-keyword-universe)
12. [KPI & Target Metrics](#12-kpi--target-metrics)
13. [Scheduled Automation Map](#13-scheduled-automation-map)
14. [Implementation Checklist](#14-implementation-checklist)

---

## 1. EXECUTIVE SUMMARY

### Situasi Kritis
Bizmark.id memiliki **47 service classes** dan **30+ artisan commands** untuk SEO automation, tetapi hanya menghasilkan **5 artikel** yang terpublikasi. Ini seperti memiliki pabrik canggih yang hanya memproduksi 5 unit produk.

### Masalah Utama
| Problem | Data | Impact |
|---------|------|--------|
| Content volume sangat rendah | 5 artikel dari target 500+ | Tidak mungkin rank #1 dengan 5 halaman konten |
| Data pipeline kosong | topic_clusters=0, keyword_clusters=0, content_gaps=0 | Service AI tidak punya data untuk diproses |
| Service dormant | 6 dari 10 core services tidak punya cron trigger | ContentGap, ContentRefresh, Syndication, CompetitiveIntel semua "tidur" |
| Tidak ada posisi tracking | Tidak ada data historical ranking | Tidak bisa ukur apakah SEO berhasil |
| Sitemap tidak lengkap | Tidak include service pages + hreflang | Search engine tidak discover semua konten |

### Solusi: 6-Phase SEO Domination Plan

```
Phase 1 (Minggu 1-4)    → CONTENT VELOCITY: 5 artikel → 200+ artikel
Phase 2 (Minggu 2-6)    → TECHNICAL SEO: Fix sitemap, schema, Core Web Vitals
Phase 3 (Minggu 4-8)    → TOPIC AUTHORITY: Bangun 15+ topic clusters
Phase 4 (Minggu 6-10)   → DISTRIBUTION: Syndication ke Medium, LinkedIn, Dev.to
Phase 5 (Minggu 8-12)   → INTELLIGENCE: Automated competitor tracking & response
Phase 6 (Minggu 10-16)  → ADVANCED: A/B testing, position tracking, content refresh
```

### Proyeksi Hasil
| Metric | Saat Ini | Target 3 Bulan | Target 6 Bulan |
|--------|----------|----------------|----------------|
| Artikel terpublikasi | 5 | 200+ | 500+ |
| Keyword di halaman 1 | ~2 | 50+ | 150+ |
| Organic traffic/bulan | <500 | 5,000+ | 25,000+ |
| Topic clusters | 0 | 15 | 30+ |
| Domain Authority (est) | 5-10 | 20+ | 35+ |
| Indexed pages | ~558 | 800+ | 1,500+ |

---

## 2. AUDIT KONDISI SAAT INI

### 2.1 Inventaris Infrastruktur

#### Service Classes (47 total)
```
ACTIVE (production):
├── ArticleAutoPostService.php    ← Core auto-post pipeline
├── ArticleGenerationService.php  ← AI content generation (OpenRouter/Gemini)
├── ArticleQualityService.php     ← Quality validation
├── InternalLinkService.php       ← Link injection
├── SeoScoringService.php         ← 10-factor scoring (0-100)
├── SeoFixService.php             ← Auto-fix SEO issues
├── PexelsService.php             ← Featured images
├── SitemapGeneratorService.php   ← XML sitemap
├── SchemaMarkupService.php       ← JSON-LD structured data
├── IndexNowService.php           ← IndexNow protocol (Bing/Yandex)
└── SearxngSearchService.php      ← Real SERP data (self-hosted)

SEMI-ACTIVE (exist but under-utilized):
├── TopicClusterService.php       ← Generates clusters, subtopics stay as JSON
├── KeywordResearchService.php    ← AI-estimated volume (no real data)
├── CompetitiveIntelligenceService.php ← Has SearXNG but no cron
├── SmartMetaOptimizerService.php ← AI meta optimization exists
├── ContentRefreshService.php     ← Refresh logic exists, no trigger
└── GoogleIndexingService.php     ← Needs service account

DORMANT (built but never activated):
├── ContentGapService.php         ← Full gap→topic pipeline, zero data
├── ContentSyndicationService.php ← Medium/Dev.to/LinkedIn, no API tokens
├── MetaAbTestService.php         ← Z-test A/B framework, never triggered
└── SocialCaptionService.php      ← Social media captions, no platform
```

#### Artisan Commands (30+)
```
SCHEDULED (active cron):
├── topics:replenish         → Daily 23:30 (refill topic pool)
├── articles:schedule-daily  → Daily 00:01 (schedule posts)
├── articles:process-pending → Every 15 min (generate & publish)
├── articles:backfill-images → Hourly (Pexels images)
└── articles:cleanup-logs    → Weekly (purge old logs)

NOT SCHEDULED (exists but dormant):
├── seo:keyword-research     → Generate keyword clusters
├── seo:topic-cluster        → Generate topic clusters
├── seo:content-gap          → Analyze content gaps
├── seo:content-refresh      → Refresh stale articles
├── seo:competitor-analyze   → Competitor SERP analysis
├── seo:content-syndicate    → Distribute to platforms
├── seo:meta-optimize        → AI meta tag optimization
├── seo:meta-ab-test         → A/B test meta tags
├── seo:intelligence         → Full SEO intelligence run
├── seo:weekly-report        → Generate weekly SEO report
├── seo:score-articles       → Batch SEO scoring
├── seo:social-captions      → Generate social captions
├── seo:distribution-engine  → Full distribution pipeline
├── indexnow:submit          → Submit URLs to IndexNow
├── generate:sitemap         → Regenerate sitemap
└── validate:sitemap         → Validate sitemap structure
```

#### Database Tables (content pipeline)
```
TABLE                    COUNT    STATUS
articles                 5        ⚠️ KRITIS — minimal content
article_topics           35       ✅ Topic pool exists
topic_clusters           0        ❌ Empty — clusters never generated
keyword_clusters         0        ❌ Empty — keywords never researched
content_gaps             0        ❌ Empty — gaps never analyzed
seo_scores               5        ⚠️ Only 5 articles scored
seo_reports              1        ⚠️ Only 1 report
competitor_analyses      5        ⚠️ Limited analysis
content_syndications     0        ❌ Never syndicated
content_refresh_logs     0        ❌ Never refreshed
meta_ab_tests            0        ❌ Never A/B tested
```

### 2.2 Diagnosis: Mengapa Rank Rendah

**Root Cause: Content Starvation**

Google membutuhkan **sinyal topical authority** untuk meranking sebuah domain. Dengan hanya 5 artikel, bizmark.id tidak bisa membuktikan expertise di bidang perizinan lingkungan.

Kompetitor utama (izin.co.id, jasaamdal.com, konsultanlingkungan.co.id) umumnya memiliki:
- 100-500+ halaman konten
- Topic clusters terstruktur (AMDAL → subtopik → FAQ → studi kasus)
- Backlink dari media/portal bisnis
- Update konten regular (freshness signal)

Bizmark.id memiliki **infrastruktur superior** (47 services, AI generation, SearXNG) tetapi **data pipeline-nya kosong**. Solusinya bukan membangun ulang — cukup **mengaktifkan** dan **mengorkestrasi** service yang sudah ada.

---

## 3. ARSITEKTUR SEO ENGINE

### 3.1 Pipeline Architecture

```
┌─────────────────────────────────────────────────────────────────┐
│                    SEO DOMINATION ENGINE v2                       │
├─────────────────────────────────────────────────────────────────┤
│                                                                   │
│  LAYER 1: INTELLIGENCE (Data Collection)                          │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐          │
│  │ Keyword       │  │ Competitor   │  │ SearXNG      │          │
│  │ Research      │  │ Intelligence │  │ Search       │          │
│  │ Service       │  │ Service      │  │ Service      │          │
│  └──────┬───────┘  └──────┬───────┘  └──────┬───────┘          │
│         │                  │                  │                   │
│  LAYER 2: STRATEGY (Analysis & Planning)                         │
│  ┌──────▼───────┐  ┌──────▼───────┐  ┌──────▼───────┐          │
│  │ Topic        │  │ Content      │  │ Content      │          │
│  │ Cluster      │  │ Gap          │  │ Refresh      │          │
│  │ Service      │  │ Service      │  │ Service      │          │
│  └──────┬───────┘  └──────┬───────┘  └──────┬───────┘          │
│         │                  │                  │                   │
│  LAYER 3: PRODUCTION (Content Creation)                          │
│  ┌──────▼──────────────────▼──────────────────▼───────┐          │
│  │              ArticleAutoPostService                 │          │
│  │  ┌─────────┐ ┌──────────┐ ┌─────────┐ ┌────────┐  │          │
│  │  │ Topic   │ │ AI       │ │ Quality │ │ SEO    │  │          │
│  │  │ Select  │ │ Generate │ │ Check   │ │ Score  │  │          │
│  │  └────┬────┘ └────┬─────┘ └────┬────┘ └───┬────┘  │          │
│  └───────┼───────────┼────────────┼───────────┼───────┘          │
│          │           │            │           │                   │
│  LAYER 4: OPTIMIZATION (Post-Publish)                            │
│  ┌───────▼───────────▼────────────▼───────────▼───────┐          │
│  │ Internal │ Schema  │ Smart     │ Meta A/B │ SEO   │          │
│  │ Links    │ Markup  │ Meta Opt  │ Test     │ Fix   │          │
│  └──────┬──────────┬──────────┬──────────┬──────────┬─┘          │
│         │          │          │          │          │             │
│  LAYER 5: DISTRIBUTION (Reach Amplification)                     │
│  ┌──────▼──────────▼──────────▼──────────▼──────────▼─┐          │
│  │ IndexNow │ Google  │ Sitemap │ Content │ Social  │          │
│  │ Submit   │ Index   │ Update  │ Syndic. │ Caption │          │
│  └────────────────────────────────────────────────────┘          │
│                                                                   │
│  LAYER 6: MONITORING (Performance Tracking)                      │
│  ┌────────────────────────────────────────────────────┐          │
│  │ SEO Score │ Weekly  │ Position │ Competitor │     │          │
│  │ Tracking  │ Report  │ History  │ Alerts     │     │          │
│  └────────────────────────────────────────────────────┘          │
│                                                                   │
└─────────────────────────────────────────────────────────────────┘
```

### 3.2 Data Flow

```
KeywordResearch → KeywordCluster records
                        ↓
TopicCluster → TopicCluster records (subtopics)
                        ↓
ContentGap → ContentGap records → queueTopGaps() → ArticleTopic records
                                                            ↓
topics:replenish → [tambah topic jika pool < threshold]     ↓
                                                            ↓
articles:schedule-daily → AutoPostSchedule records  ←───────┘
                                ↓
articles:process-pending → ArticleAutoPostService.executeScheduledPost()
                                ↓
                    ┌───────────┴───────────┐
                    ↓                       ↓
            Published Article         SEO Compliance
                    ↓                       ↓
            ┌───────┴──────────┐    SeoScore record
            ↓                  ↓
    IndexNow submit    Content Syndication
            ↓                  ↓
    Search engines    Medium / Dev.to / LinkedIn
```

---

## 4. PHASE 1: CONTENT VELOCITY — Ledakan Konten
**Timeline: Minggu 1-4 | Priority: CRITICAL**

### 4.1 Masalah
- Hanya 5 artikel dari target 500+
- Topic pool hanya 35 topics (dan 0 keyword clusters untuk membuat topic baru)
- Auto-post berjalan tapi pipeline data-nya kelaparan

### 4.2 Target
- **200+ artikel** dalam 30 hari pertama
- **5-8 artikel/hari** secara otomatis
- Cover semua 15+ jenis layanan izin dengan minimal 10 artikel/layanan

### 4.3 Strategi Konten

#### A. Content Pillars (15 Jenis Layanan)
Setiap layanan harus memiliki cluster konten lengkap:

```
LAYANAN: AMDAL (Analisis Mengenai Dampak Lingkungan)
├── Pillar: "Panduan Lengkap AMDAL 2026" (3000+ kata)
├── Subtopik:
│   ├── "Persyaratan Dokumen AMDAL: Checklist Lengkap"
│   ├── "Biaya Pengurusan AMDAL: Breakdown Detail"
│   ├── "Berapa Lama Proses AMDAL? Timeline Realistis"
│   ├── "Perbedaan AMDAL, UKL-UPL, dan SPPL"
│   ├── "Studi Kasus: AMDAL untuk Proyek Konstruksi"
│   ├── "Tenaga Ahli AMDAL: Kualifikasi & Sertifikasi"
│   ├── "Revisi AMDAL: Kapan & Bagaimana"
│   ├── "AMDAL untuk Industri Manufaktur"
│   ├── "FAQ AMDAL: 20 Pertanyaan Paling Sering Ditanyakan"
│   └── "Template Kerangka Acuan AMDAL"
├── Studi Kasus: 3-5 real/anonymized case studies
└── FAQ Terstruktur: 10-20 Q&A (Schema FAQPage)
```

#### B. Keyword Intent Mapping
```
INFORMATIONAL (awareness, top-of-funnel):
├── "apa itu amdal" → artikel edukasi
├── "perbedaan amdal dan ukl-upl" → artikel perbandingan
├── "syarat dokumen izin lingkungan" → checklist artikel
└── "regulasi limbah b3 terbaru 2026" → artikel update

COMMERCIAL (investigation, mid-funnel):
├── "jasa pengurusan amdal" → landing page + artikel
├── "konsultan izin lingkungan terpercaya" → testimonial + case study
├── "biaya pengurusan izin lb3" → artikel harga
└── "review jasa konsultan oss" → perbandingan

TRANSACTIONAL (decision, bottom-of-funnel):
├── "konsultasi gratis izin lingkungan" → CTA page
├── "pengurusan amdal jakarta" → local SEO page
├── "jasa amdal murah bersertifikat" → landing page
└── "formulir pengajuan izin lb3" → lead magnet

NAVIGATIONAL:
├── "bizmark izin lingkungan" → brand search
├── "bizmark.id kontak" → footer/contact
└── "bizmark konsultan amdal" → homepage
```

#### C. Konten Prioritas Tinggi (Minggu 1-2)

| # | Judul Target | Keyword Utama | Volume Est | Difficulty |
|---|---|---|---|---|
| 1 | Panduan Lengkap AMDAL 2026 | amdal | Tinggi | Tinggi |
| 2 | Panduan Lengkap UKL-UPL 2026 | ukl-upl | Tinggi | Sedang |
| 3 | Cara Mengurus Izin Limbah B3 | izin limbah b3 | Tinggi | Sedang |
| 4 | Perbedaan AMDAL, UKL-UPL, SPPL | perbedaan amdal ukl-upl | Tinggi | Rendah |
| 5 | OSS NIB: Panduan Lengkap 2026 | oss nib | Sangat Tinggi | Tinggi |
| 6 | Biaya Pengurusan AMDAL | biaya amdal | Tinggi | Rendah |
| 7 | Studi Kasus: Izin Lingkungan Ditolak | studi kasus izin lingkungan | Sedang | Rendah |
| 8 | IPAL Industri: Panduan Perizinan | ipal industri | Sedang | Rendah |
| 9 | Izin Pengolahan Limbah B3 | izin pengolahan limbah b3 | Sedang | Sedang |
| 10 | FAQ Izin Lingkungan Indonesia | faq izin lingkungan | Sedang | Rendah |
| 11 | SLO Boiler: Persyaratan & Prosedur | slo boiler | Sedang | Rendah |
| 12 | Pertek Lingkungan Hidup 2026 | pertek lingkungan | Sedang | Rendah |
| 13 | Izin Penyimpanan Limbah B3 (TPS) | tps limbah b3 | Sedang | Rendah |
| 14 | PROPER KLHK: Panduan Peringkat | proper klhk | Sedang | Rendah |
| 15 | Izin Usaha Jasa Konstruksi (IUJK) | iujk | Tinggi | Sedang |
| 16 | Panduan Izin Lingkungan untuk UMKM | izin lingkungan umkm | Sedang | Rendah |
| 17 | Dokumen Lingkungan untuk Investasi | dokumen lingkungan investasi | Sedang | Rendah |
| 18 | Jasa Konsultan AMDAL Terpercaya | jasa konsultan amdal | Tinggi | Tinggi |
| 19 | Audit Lingkungan Hidup Wajib | audit lingkungan | Sedang | Rendah |
| 20 | Izin Pembuangan Limbah Cair (IPLC) | iplc | Sedang | Rendah |

### 4.4 Implementasi Teknis

#### A. Activate Keyword Research Pipeline
```bash
# Step 1: Generate keyword clusters untuk semua layanan
php artisan seo:keyword-research --all --language=id

# Step 2: Generate topic clusters
php artisan seo:topic-cluster --language=id

# Step 3: Analyze content gaps
php artisan seo:content-gap --language=id

# Step 4: Queue gap topics for auto-posting  
php artisan seo:content-gap --queue-top=50
```

#### B. Boost Auto-Post Capacity
Current schedule: ~2-3 posts/day. Target: 5-8 posts/day.

Perubahan di `AutoPostConfig`:
- Tambah time slots: 06:00, 08:00, 10:00, 12:00, 14:00, 16:00, 18:00, 20:00
- Set `posts_per_day = 8`
- Ensure topic pool > 100 topics

#### C. New Scheduled Tasks Needed
```php
// routes/console.php - TAMBAHAN
Schedule::command('seo:keyword-research --all')->weekly()->mondays()->at('02:00');
Schedule::command('seo:topic-cluster')->weekly()->tuesdays()->at('02:00');
Schedule::command('seo:content-gap --queue-top=20')->daily()->at('03:00');
Schedule::command('topics:replenish --threshold=30 --count=50')->daily()->at('22:00');
```

---

## 5. PHASE 2: TECHNICAL SEO HARDENING
**Timeline: Minggu 2-6 | Priority: HIGH**

### 5.1 Sitemap Enhancement

#### Masalah Saat Ini:
- Sitemap tidak include halaman layanan (`/layanan/{slug}`)
- Tidak ada image sitemap
- Hreflang sudah ada di sitemap tapi perlu validasi
- Tidak auto-regenerate saat artikel baru terbit
- Static pages menggunakan `now()` sebagai lastmod

#### Fix Required:
1. Tambahkan semua 15+ halaman layanan ke sitemap
2. Tambahkan image entries untuk artikel dengan featured image
3. Implement sitemap index (split articles/services/static)
4. Auto-regenerate sitemap setelah artikel publish
5. Submit sitemap ke Google Search Console & Bing setelah regenerate

### 5.2 Schema Markup Enhancement

#### Status Saat Ini:
- ✅ `Article` schema
- ✅ `FAQPage` schema (auto-detect dari heading)
- ✅ `HowTo` schema (untuk panduan)
- ✅ `BreadcrumbList` schema

#### Yang Perlu Ditambah:
1. **`LocalBusiness`** schema di homepage dan contact
2. **`Service`** schema di setiap halaman layanan
3. **`Organization`** schema di footer/about
4. **`Review/AggregateRating`** schema dari testimonial
5. **`ProfessionalService`** schema
6. **`WebPage`/`WebSite`** schema di homepage
7. **Sitelinks searchbox** schema

### 5.3 Core Web Vitals

Target Google Core Web Vitals (CWV):
| Metric | Target | Action |
|--------|--------|--------|
| LCP (Largest Contentful Paint) | < 2.5s | Optimize images (WebP), lazy load, CDN caching |
| INP (Interaction to Next Paint) | < 200ms | Minimize JS bundles, defer non-critical scripts |
| CLS (Cumulative Layout Shift) | < 0.1 | Set explicit image dimensions, avoid layout shifts |

### 5.4 Meta Tags Optimization

#### Automated Meta Optimization Pipeline:
```
SmartMetaOptimizerService → optimizeBatch(limit=5)
                                    ↓
                    AI generates optimized meta
                                    ↓
                    IndexNow submits updated URLs
                                    ↓
                    MetaAbTestService → createTest()
                                    ↓
                    After 7 days → evaluateTests()
                                    ↓
                    Winner applied automatically
```

### 5.5 Internal Linking Strategy

#### Current Gap:
- Links hanya ONE-WAY (artikel baru → artikel lama)
- Artikel lama tidak pernah diupdate dengan link ke artikel baru
- Tidak ada "hub page" linking strategy

#### Fix: Bidirectional Link Injection
Setiap kali artikel baru dipublish:
1. Inject links dari artikel baru ke artikel lama (sudah ada)
2. **BARU**: Scan semua artikel lama yang relevan, inject link ke artikel baru
3. **BARU**: Update "Baca juga" section di artikel lama

### 5.6 Robots.txt Optimization

#### Status Saat Ini:
- ✅ Googlebot, Bingbot, Yandex, DuckDuckBot allowed
- ✅ AI bots blocked (ChatGPT, Claude, etc.)
- ⚠️ AhrefsBot dan SemrushBot diblokir — ini tidak mempengaruhi ranking tapi blokir free analysis tools

#### Rekomendasi:
- Biarkan AhrefsBot/SemrushBot diblokir (mereka hanya tools, bukan search engine)
- Tambahkan `Crawl-delay: 1` untuk bot non-prioritas
- Pastikan sitemap URL di robots.txt sudah benar

---

## 6. PHASE 3: TOPIC AUTHORITY & CLUSTER STRATEGY
**Timeline: Minggu 4-8 | Priority: HIGH**

### 6.1 Topic Cluster Architecture

Setiap layanan = 1 Topic Cluster dengan struktur:

```
                    ┌──────────────────┐
                    │   PILLAR PAGE    │
                    │ "Panduan Lengkap │
                    │   AMDAL 2026"   │
                    │  (3000+ kata)    │
                    └────────┬─────────┘
                             │
         ┌───────────┬───────┼───────┬───────────┐
         ↓           ↓       ↓       ↓           ↓
    ┌─────────┐ ┌─────────┐ ┌────┐ ┌─────────┐ ┌─────────┐
    │Persyar- │ │ Biaya   │ │FAQ │ │Studi    │ │Timeline │
    │atan     │ │ Detail  │ │    │ │Kasus    │ │Proses   │
    │Dokumen  │ │ AMDAL   │ │    │ │AMDAL    │ │AMDAL    │
    └─────────┘ └─────────┘ └────┘ └─────────┘ └─────────┘
         ↕           ↕       ↕       ↕           ↕
    (saling link satu sama lain dalam cluster)
```

### 6.2 Target 15 Topic Clusters

| # | Cluster (Pillar) | Target Subtopics | Est. Articles |
|---|---|---|---|
| 1 | AMDAL | 12 | 15 |
| 2 | UKL-UPL | 10 | 12 |
| 3 | Limbah B3 (Perizinan) | 12 | 15 |
| 4 | OSS NIB | 10 | 12 |
| 5 | Izin Lingkungan Umum | 8 | 10 |
| 6 | IPAL & Pengolahan Air Limbah | 8 | 10 |
| 7 | SLO (Sertifikat Laik Operasi) | 6 | 8 |
| 8 | Pertek Lingkungan | 6 | 8 |
| 9 | PROPER KLHK | 6 | 8 |
| 10 | IMB / PBG (Persetujuan Bangunan) | 8 | 10 |
| 11 | Izin Usaha Jasa Konstruksi | 6 | 8 |
| 12 | Audit Lingkungan | 6 | 8 |
| 13 | SPPL | 4 | 6 |
| 14 | Izin Pembuangan Limbah | 6 | 8 |
| 15 | Dokumen Lingkungan untuk Investasi | 4 | 6 |
| **TOTAL** | | **112** | **144+** |

### 6.3 Konten Pendukung (Cross-Cluster)

Selain cluster per layanan, buat konten cross-cutting:

| Jenis | Contoh | Target |
|-------|--------|--------|
| Perbandingan | "AMDAL vs UKL-UPL vs SPPL" | 10 artikel |
| Regional | "Izin Lingkungan Jakarta/Jawa Barat/Jawa Timur" | 10 artikel |
| Industri | "Izin Lingkungan untuk Manufaktur/Pertambangan/Konstruksi" | 10 artikel |
| Regulasi | "Peraturan Terbaru KLHK 2026" | 5 artikel |
| Case Study | Anonymized success stories | 10 artikel |
| FAQ Compilation | "50 FAQ Izin Lingkungan" | 5 artikel |
| **TOTAL** | | **50 artikel** |

### 6.4 Implementasi TopicCluster → ArticleTopic Pipeline

**Gap Kritis**: TopicClusterService generates subtopics as JSON but never converts them to ArticleTopic records.

**Fix**: Buat bridge method yang:
1. Parse `TopicCluster.subtopics` JSON
2. Check apakah subtopik sudah ada sebagai ArticleTopic
3. Create ArticleTopic untuk yang belum ada
4. Set priority berdasarkan search volume estimate

---

## 7. PHASE 4: DISTRIBUTION & SYNDICATION
**Timeline: Minggu 6-10 | Priority: MEDIUM**

### 7.1 Content Distribution Channels

```
TIER 1: Search Engines (Primary)
├── Google → IndexNow + Sitemap submission + Search Console
├── Bing → IndexNow native support
├── Yandex → IndexNow support
└── DuckDuckGo → Bing index sharing

TIER 2: Content Platforms (Syndication)
├── Medium → Full article with canonical URL
├── LinkedIn Articles → Professional network reach
├── Dev.to → Tech/professional community (untuk topik teknis)
└── Kompas.com/Bisnis.com Forum → Indonesia business community

TIER 3: Social Signals
├── LinkedIn (company page) → Article summaries + link
├── Twitter/X → Thread summaries + link
├── Facebook → Business page posts
└── WhatsApp Business → Broadcast to leads

TIER 4: Community & Forum
├── Kaskus → Relevant sub-forums
├── Reddit → r/indonesia, r/business
├── Quora → Answer questions with article links
└── Google Business Profile → Regular posts
```

### 7.2 Syndication Rules

1. **Always include canonical URL** pointing back to bizmark.id
2. **Wait 48-72 hours** after publish before syndicating (let Google index original first)
3. **Rewrite for platform** — don't copy-paste exact content
4. **Include CTA** back to bizmark.id
5. **Track referral traffic** per platform

### 7.3 Activation Steps

```bash
# 1. Configure API tokens di .env
MEDIUM_TOKEN=xxx
DEVTO_API_KEY=xxx
LINKEDIN_ACCESS_TOKEN=xxx
LINKEDIN_ORGANIZATION_ID=xxx

# 2. Schedule syndication
php artisan seo:content-syndicate --limit=5

# 3. Add to cron
# Schedule::command('seo:content-syndicate --limit=3')->daily()->at('14:00');
```

### 7.4 Google Business Profile

**Gratis dan sangat powerful untuk local SEO:**
1. Claim & verify Google Business Profile
2. Post artikel baru setiap minggu
3. Collect client reviews (target: 20+ reviews, 4.5+ rating)
4. Update business info regularly
5. Add photos of office/team/work

---

## 8. PHASE 5: COMPETITIVE INTELLIGENCE & MONITORING
**Timeline: Minggu 8-12 | Priority: MEDIUM**

### 8.1 Competitor Tracking

#### Target Competitors:
1. Konsultan AMDAL nasional (jasaamdal.com, dll)
2. Portal perizinan (izin.co.id, dll)
3. Konsultan lingkungan (konsultanlingkungan.co.id, dll)
4. Law firms dengan layanan perizinan
5. Platform OSS-related

#### Monitoring Points:
| What to Track | How | Frequency |
|---|---|---|
| SERP position untuk target keywords | SearXNG + CompetitiveIntelligenceService | Weekly |
| Competitor new content | SearXNG site:competitor.com | Weekly |
| Content gap vs competitors | ContentGapService | Biweekly |
| Competitor backlink profile | AI analysis via SearXNG | Monthly |
| SERP feature ownership (featured snippets) | SearXNG + manual check | Weekly |

### 8.2 Automated Response System

Ketika competitor publish konten baru di topik kita:
1. **Detect** via CompetitiveIntelligenceService weekly scan
2. **Analyze** konten competitor (word count, structure, keywords)
3. **Generate** konten yang lebih baik (10x content strategy)
4. **Queue** for auto-post priority
5. **Alert** admin via notification

### 8.3 Position Tracking Database

**New table needed: `keyword_position_history`**
```sql
CREATE TABLE keyword_position_history (
    id BIGSERIAL PRIMARY KEY,
    keyword VARCHAR(255) NOT NULL,
    position INT,
    url VARCHAR(500),
    competitor_url VARCHAR(500),
    data_source VARCHAR(50), -- searxng, google, manual
    checked_at TIMESTAMP NOT NULL,
    created_at TIMESTAMP DEFAULT NOW()
);

CREATE INDEX idx_kph_keyword ON keyword_position_history(keyword);
CREATE INDEX idx_kph_checked ON keyword_position_history(checked_at);
```

### 8.4 Implementation

```bash
# Schedule competitive intelligence
php artisan seo:competitor-analyze --top=20

# Add to cron (weekly)
# Schedule::command('seo:competitor-analyze --top=20')->weekly()->sundays()->at('04:00');
```

---

## 9. PHASE 6: ADVANCED OPTIMIZATION
**Timeline: Minggu 10-16 | Priority: MEDIUM-LOW**

### 9.1 Meta A/B Testing

Framework sudah built — `MetaAbTestService` dengan z-test confidence calculation.

#### Activation:
```bash
# Auto-create tests for top articles
php artisan seo:meta-ab-test --create --limit=5

# Evaluate after 7+ days
php artisan seo:meta-ab-test --evaluate

# Schedule
# Schedule::command('seo:meta-ab-test --create --limit=3')->weekly()->wednesdays()->at('05:00');
# Schedule::command('seo:meta-ab-test --evaluate')->daily()->at('06:00');
```

### 9.2 Content Refresh Strategy

#### Trigger Conditions:
- Artikel > 90 hari tanpa update
- Ranking drop > 5 posisi
- Perubahan regulasi yang relevan
- Year reference outdated

#### Refresh Actions:
1. Update meta title/description dengan tahun terbaru
2. Inject "Update YYYY" section dengan info terbaru
3. Update year references di body
4. Re-submit ke IndexNow
5. Re-score via SeoScoringService

```bash
# Schedule content refresh
# Schedule::command('seo:content-refresh --days=90 --limit=5')->weekly()->thursdays()->at('03:00');
```

### 9.3 Smart Meta Optimization

AI-powered meta optimization untuk artikel dengan CTR rendah atau meta score rendah:

```bash
# Optimize weak meta tags
php artisan seo:meta-optimize --limit=10

# Schedule
# Schedule::command('seo:meta-optimize --limit=5')->weekly()->fridays()->at('04:00');
```

### 9.4 SEO Reporting

Weekly automated report covering:
- New articles published
- Keyword position changes
- Content gap status
- Competitor movements
- SEO score distribution
- Syndication performance

```bash
# Generate weekly report
php artisan seo:weekly-report

# Schedule
# Schedule::command('seo:weekly-report')->weekly()->mondays()->at('07:00');
```

---

## 10. OPEN-SOURCE ENGINE STACK

### 10.1 Already Integrated

| Tool | Purpose | Status |
|------|---------|--------|
| **SearXNG** (Docker) | Metasearch engine — free unlimited SERP data | ✅ Active |
| **OpenRouter + Gemini 2.5 Flash** | AI content generation, keyword research, meta optimization | ✅ Active |
| **Pexels API** | Free stock photos for featured images | ✅ Active |
| **IndexNow Protocol** | Instant URL submission to Bing/Yandex | ✅ Active |

### 10.2 Recommended Additions

| Tool | Purpose | Priority | Integration |
|------|---------|----------|-------------|
| **Umami Analytics** | Privacy-focused web analytics (self-hosted) | HIGH | Replace/complement GA4 |
| **Plausible Analytics** | Lightweight analytics (optional) | MEDIUM | Alternative to Umami |
| **Matomo** | Full-featured analytics (self-hosted) | LOW | Heavy alternative |
| **Google Search Console API** | Real keyword positions, clicks, impressions | HIGH | PHP Google API Client |
| **lighthouse-ci** | Automated CWV testing | MEDIUM | GitHub Actions/Cron |
| **spatie/laravel-sitemap** | Enhanced sitemap generation | LOW | Already have custom |
| **Meilisearch** | Full-text search untuk internal site search | MEDIUM | Laravel Scout |

### 10.3 Google Search Console Integration (HIGH PRIORITY)

**Mengapa Penting:**
- Data keyword ranking REAL (bukan AI estimate)
- Click-through rate per keyword
- Impression data
- Index coverage issues
- Core Web Vitals data

**How:**
```
SearchConsoleService (sudah ada) → Import real data → Update keyword_clusters 
→ Validate AI estimates → Better content gap analysis
```

### 10.4 Self-Hosted Search Intelligence Stack

```
┌─────────────────────────────────────────────┐
│           BIZMARK SEARCH INTEL STACK          │
├─────────────────────────────────────────────┤
│                                               │
│  ┌──────────┐  ┌──────────┐  ┌──────────┐  │
│  │ SearXNG  │  │ Google   │  │ IndexNow │  │
│  │ (Docker) │  │ Search   │  │ Protocol │  │
│  │ SERP     │  │ Console  │  │ Submit   │  │
│  │ Data     │  │ API      │  │ URLs     │  │
│  └────┬─────┘  └────┬─────┘  └────┬─────┘  │
│       │              │              │         │
│       └──────────┬───┘              │         │
│                  ↓                  │         │
│  ┌──────────────────────────┐      │         │
│  │   OpenRouter / Gemini    │      │         │
│  │   AI Analysis Layer      │      │         │
│  │   - Content Generation   │      │         │
│  │   - Keyword Research     │      │         │
│  │   - Competitor Analysis  │──────┘         │
│  │   - Meta Optimization    │                │
│  └──────────────────────────┘                │
│                                               │
└─────────────────────────────────────────────┘
```

---

## 11. KEYWORD UNIVERSE

### 11.1 Primary Keywords (Target Rank #1)

| Keyword | Category | Search Intent | Current Status |
|---------|----------|---------------|----------------|
| jasa amdal | AMDAL | Transactional | No dedicated article |
| jasa konsultan amdal | AMDAL | Transactional | 1 article |
| pengurusan amdal | AMDAL | Transactional | No article |
| biaya amdal | AMDAL | Commercial | No article |
| syarat amdal | AMDAL | Informational | No article |
| jasa ukl upl | UKL-UPL | Transactional | No article |
| pengurusan ukl upl | UKL-UPL | Transactional | No article |
| biaya ukl upl | UKL-UPL | Commercial | No article |
| izin limbah b3 | Limbah B3 | Transactional | No article |
| jasa pengurusan limbah b3 | Limbah B3 | Transactional | No article |
| izin pengolahan limbah b3 | Limbah B3 | Transactional | No article |
| izin penyimpanan limbah b3 | Limbah B3 | Transactional | No article |
| oss nib | OSS | Informational | 1 article |
| cara daftar oss | OSS | Informational | No article |
| izin usaha online | OSS | Informational | 1 article |
| pertek lingkungan | Pertek | Transactional | No article |
| slo boiler | SLO | Transactional | No article |
| slo genset | SLO | Transactional | No article |
| ipal industri | IPAL | Commercial | No article |
| proper klhk | PROPER | Informational | No article |

### 11.2 Long-tail Keywords (Quick Wins)

| Keyword (long-tail) | Volume Est | Difficulty | Priority |
|---|---|---|---|
| cara mengurus amdal untuk pabrik | Rendah | Sangat Rendah | Quick Win |
| persyaratan izin limbah b3 2026 | Rendah | Sangat Rendah | Quick Win |
| perbedaan amdal dan ukl upl dan sppl | Sedang | Rendah | Quick Win |
| berapa lama proses amdal | Rendah | Sangat Rendah | Quick Win |
| dokumen yang diperlukan untuk ukl upl | Rendah | Sangat Rendah | Quick Win |
| cara perpanjang izin limbah b3 | Rendah | Sangat Rendah | Quick Win |
| konsultan lingkungan hidup jakarta | Rendah | Rendah | Quick Win |
| jasa pengurusan izin lingkungan surabaya | Rendah | Rendah | Quick Win |
| template kerangka acuan amdal | Rendah | Sangat Rendah | Quick Win |
| syarat menjadi konsultan amdal | Rendah | Sangat Rendah | Quick Win |
| studi kasus amdal perumahan | Rendah | Sangat Rendah | Quick Win |
| checklist dokumen izin lingkungan | Rendah | Sangat Rendah | Quick Win |

### 11.3 Semantic Keyword Groups

```
GROUP: AMDAL Core
├── amdal, analisis mengenai dampak lingkungan
├── andal, rencana pengelolaan lingkungan, rencana pemantauan lingkungan
├── rpplh, dplh, delh, klhs
├── kerangka acuan (ka), amdal ka
└── komisi penilai amdal

GROUP: Limbah B3
├── limbah bahan berbahaya beracun, limbah b3
├── izin tps limbah b3, izin pengumpulan limbah b3
├── izin pengangkutan limbah b3, izin pengolahan limbah b3
├── manifest limbah b3, neraca limbah b3
└── proper klhk, pengelolaan limbah b3

GROUP: OSS/Perizinan Digital
├── oss, online single submission, nib, nomor induk berusaha
├── iumk, izin usaha mikro kecil, siup
├── iui, izin usaha industri, tdi
├── pbg, persetujuan bangunan gedung, imb
└── sertifikat standar, surat izin usaha

GROUP: Izin Lingkungan
├── izin lingkungan, persetujuan lingkungan
├── ukl upl, sppl, amdal
├── pertek, persetujuan teknis lingkungan
├── izin pembuangan air limbah, iplc
└── izin emisi, baku mutu lingkungan
```

---

## 12. KPI & TARGET METRICS

### 12.1 Content Metrics

| KPI | Baseline | Month 1 | Month 3 | Month 6 |
|-----|----------|---------|---------|---------|
| Total articles | 5 | 50+ | 200+ | 500+ |
| Articles/week | <1 | 30-40 | 25-35 | 20-30 |
| Avg word count | ~1500 | 1500+ | 2000+ | 2000+ |
| Avg SEO score | ~70 | 75+ | 80+ | 85+ |
| Topic clusters | 0 | 5 | 15 | 30+ |
| Keyword clusters | 0 | 15 | 50+ | 100+ |

### 12.2 Search Performance

| KPI | Baseline | Month 1 | Month 3 | Month 6 |
|-----|----------|---------|---------|---------|
| Indexed pages | ~558 | 650+ | 1000+ | 1500+ |
| Keywords page 1 | ~2 | 15+ | 50+ | 150+ |
| Featured snippets | 0 | 2+ | 10+ | 25+ |
| Organic traffic/mo | <500 | 2000+ | 10,000+ | 25,000+ |
| Avg CTR | Unknown | 3%+ | 5%+ | 7%+ |
| Avg position | Unknown | <30 | <15 | <10 |

### 12.3 Technical SEO

| KPI | Baseline | Target |
|-----|----------|--------|
| Core Web Vitals pass | Unknown | 100% Good |
| Schema types | 4 | 8+ |
| Internal links/article | 3-5 | 5-8 |
| Sitemap accuracy | Partial | 100% coverage |
| Index coverage errors | Unknown | 0 |
| Mobile-friendly score | Unknown | 95+ |

### 12.4 Authority Metrics

| KPI | Baseline | Month 3 | Month 6 |
|-----|----------|---------|---------|
| Domain Authority (est) | 5-10 | 20+ | 35+ |
| Referring domains | <10 | 30+ | 80+ |
| Content syndication reach | 0 | 3 platforms | 5 platforms |
| Google Business reviews | 0 | 10+ | 25+ |
| Social shares/article | 0 | 5+ | 15+ |

---

## 13. SCHEDULED AUTOMATION MAP

### 13.1 Complete Cron Schedule

```
# ═══════════════════════════════════════════════════════════════
# BIZMARK SEO AUTOMATION SCHEDULE (routes/console.php)
# ═══════════════════════════════════════════════════════════════

# ─── EXISTING (KEEP) ──────────────────────────────────────────
Daily 23:30   topics:replenish --threshold=30 --count=50
Daily 00:01   articles:schedule-daily
Every 15min   articles:process-pending
Hourly        articles:backfill-images --limit=8
Weekly Sun    articles:cleanup-logs
Every 15min   fix-storage-permissions
Every 6hr     shapefiles:cleanup --hours=24
Daily 09:00   interviews:send-reminders

# ─── NEW: INTELLIGENCE LAYER ─────────────────────────────────
Weekly Mon 02:00   seo:keyword-research --all --language=id
Weekly Tue 02:00   seo:topic-cluster --language=id
Daily 03:00        seo:content-gap --queue-top=20
Weekly Sun 04:00   seo:competitor-analyze --top=20

# ─── NEW: OPTIMIZATION LAYER ─────────────────────────────────
Weekly Fri 04:00   seo:meta-optimize --limit=10
Weekly Wed 05:00   seo:meta-ab-test --create --limit=3
Daily 06:00        seo:meta-ab-test --evaluate
Weekly Thu 03:00   seo:content-refresh --days=90 --limit=5

# ─── NEW: DISTRIBUTION LAYER ─────────────────────────────────
Daily 14:00        seo:content-syndicate --limit=3
Daily 15:00        generate:sitemap
Daily 15:05        indexnow:submit --recent=10

# ─── NEW: REPORTING LAYER ────────────────────────────────────
Weekly Mon 07:00   seo:weekly-report
Daily 01:00        seo:score-articles --limit=20
```

### 13.2 Daily Automation Flow

```
00:01  articles:schedule-daily          → Queue hari ini
01:00  seo:score-articles              → Score artikel terbaru
02:00  (depends on day) intelligence   → Data collection
03:00  seo:content-gap --queue-top=20  → Feed topic pool
03:00  (Thu) content-refresh           → Refresh stale content
04:00  (Fri) meta-optimize             → Fix weak meta tags
05:00  (Wed) meta-ab-test --create     → Create new tests
06:00  meta-ab-test --evaluate         → Evaluate running tests
09:00  interviews:send-reminders       → Business operations
14:00  seo:content-syndicate           → Distribute content
15:00  generate:sitemap                → Update sitemap
15:05  indexnow:submit                 → Submit new URLs
22:00  topics:replenish                → Refill topic pool
23:30  (existing) topics:replenish     → Additional refill check

Every 15min: articles:process-pending  → Generate & publish
Hourly:      articles:backfill-images  → Fill missing images
```

---

## 14. IMPLEMENTATION CHECKLIST

### Phase 1: Content Velocity (Minggu 1-4)
- [ ] Run `seo:keyword-research --all` untuk generate keyword clusters
- [ ] Run `seo:topic-cluster --language=id` untuk generate topic clusters
- [ ] Buat bridge: TopicCluster subtopics → ArticleTopic converter
- [ ] Run `seo:content-gap --queue-top=50` untuk feed topic pool
- [ ] Update AutoPostConfig: 8 posts/day, more time slots
- [ ] Update topics:replenish threshold ke 30, count ke 50
- [ ] Add content-gap ke daily cron (03:00)
- [ ] Verify auto-post pipeline berjalan lancar
- [ ] Monitor: 50+ artikel di akhir minggu 4
- [ ] Quality check: avg SEO score > 75

### Phase 2: Technical SEO (Minggu 2-6)
- [ ] Enhance SitemapGeneratorService: add service pages
- [ ] Implement sitemap auto-regenerate on article publish
- [ ] Add LocalBusiness + Service schema to service pages
- [ ] Add Organization schema to homepage/about
- [ ] Implement bidirectional internal linking
- [ ] Audit dan fix Core Web Vitals issues
- [ ] Setup Google Search Console verification (jika belum)
- [ ] Submit sitemap ke Google Search Console
- [ ] Add sitemap generation to daily cron
- [ ] Add IndexNow submission to daily cron

### Phase 3: Topic Authority (Minggu 4-8)
- [ ] Generate 15 complete topic clusters
- [ ] Create pillar pages for top 5 clusters
- [ ] Ensure cross-linking within each cluster
- [ ] Create comparison articles (cross-cluster)
- [ ] Create regional SEO pages (Jakarta, Surabaya, etc.)
- [ ] Create industry-specific pages
- [ ] Verify all cluster subtopics have articles
- [ ] Monitor: 15 clusters, 100+ linked articles

### Phase 4: Distribution (Minggu 6-10)
- [ ] Setup Medium account + API token
- [ ] Setup Dev.to account + API key
- [ ] Setup LinkedIn company page + access token
- [ ] Configure ContentSyndicationService API tokens
- [ ] Add syndication to daily cron (14:00)
- [ ] Claim Google Business Profile
- [ ] Start collecting client reviews on GBP
- [ ] Setup social media posting schedule
- [ ] Monitor: 3 active syndication platforms

### Phase 5: Intelligence (Minggu 8-12)
- [ ] Create keyword_position_history table
- [ ] Implement position tracking in CompetitiveIntelligenceService
- [ ] Add competitor tracking to weekly cron
- [ ] Setup alert system for ranking drops
- [ ] Setup Google Search Console API integration
- [ ] Import real keyword data from Search Console
- [ ] Cross-reference AI estimates vs real data
- [ ] Create admin dashboard for position tracking
- [ ] Monitor: weekly position reports

### Phase 6: Advanced (Minggu 10-16)
- [ ] Activate MetaAbTestService cron
- [ ] Activate ContentRefreshService cron
- [ ] Activate SmartMetaOptimizerService cron
- [ ] Implement SEO weekly report email
- [ ] Fine-tune auto-post quality thresholds
- [ ] Implement content performance tracking
- [ ] A/B test: different content formats (listicle vs guide vs case study)
- [ ] Implement trending topic detection via SearXNG
- [ ] Optimize: target 85+ avg SEO score
- [ ] Review & iterate based on 3-month data

---

## APPENDIX A: SERVICE ARCHITECTURE REFERENCE

### Complete Service Dependency Map

```
ArticleAutoPostService
├── ArticleGenerationService (OpenRouter/Gemini)
├── InternalLinkService
├── TopicSimilarityService
├── ArticleQualityService
├── PexelsService
├── SeoScoringService
└── SeoFixService

KeywordResearchService
└── OpenRouterService

TopicClusterService
└── OpenRouterService

ContentGapService
└── OpenRouterService

CompetitiveIntelligenceService
├── OpenRouterService
├── GoogleSearchService
└── SearxngSearchService

ContentSyndicationService
└── OpenRouterService

ContentRefreshService
├── OpenRouterService
└── IndexNowService

SmartMetaOptimizerService
├── OpenRouterService
└── IndexNowService

MetaAbTestService
└── OpenRouterService

GoogleIndexingService
└── Google API Client

IndexNowService
└── HTTP Client

SearxngSearchService
└── HTTP Client (Docker: bizmark_searxng:8080)

SchemaMarkupService
└── (standalone)

SeoScoringService
└── (standalone)

SitemapGeneratorService
└── (standalone)
```

### Database Schema Summary

```
articles              → Content storage (title, body, meta, slug, etc.)
article_topics        → Topic pool for auto-generation
auto_post_configs     → Auto-post settings (posts/day, time slots)
auto_post_schedules   → Scheduled post queue
auto_post_logs        → Execution logs
topic_clusters        → Pillar topics with subtopics JSON
keyword_clusters      → Keyword groups with volume/difficulty
content_gaps          → Uncovered keyword opportunities
seo_scores            → 10-factor SEO scores per article
seo_reports           → Aggregate SEO reports
competitor_analyses   → SERP competitor data
content_syndications  → Cross-platform distribution log
content_refresh_logs  → Content update history
meta_ab_tests         → A/B test variants and results
```

---

## APPENDIX B: OPEN-SOURCE TOOLS DATABASE

| Tool | Type | License | Use Case | URL |
|------|------|---------|----------|-----|
| SearXNG | Metasearch Engine | AGPL-3.0 | SERP data, keyword research | github.com/searxng/searxng |
| Umami | Web Analytics | MIT | Traffic tracking | github.com/umami-software/umami |
| Plausible | Web Analytics | AGPL-3.0 | Lightweight analytics | github.com/plausible/analytics |
| Matomo | Web Analytics | GPL-3.0 | Full analytics suite | github.com/matomo-org/matomo |
| SEO Panel | SEO Dashboard | GPL-3.0 | Multi-site SEO management | github.com/seopanel/Seo-Panel |
| Meilisearch | Search Engine | MIT | Internal site search | github.com/meilisearch/meilisearch |
| Linkding | Bookmark Manager | MIT | Backlink tracking | github.com/sissbruecker/linkding |
| Lighthouse CI | Performance Testing | Apache-2.0 | Core Web Vitals testing | github.com/GoogleChrome/lighthouse-ci |

---

## APPENDIX C: CONTENT TEMPLATES

### Template 1: Pillar Page (3000+ kata)
```
# [Jenis Izin]: Panduan Lengkap [Tahun]

## Apa itu [Jenis Izin]?
(Definisi, dasar hukum, siapa yang wajib)

## Mengapa [Jenis Izin] Penting?
(5 alasan, konsekuensi tidak punya)

## Persyaratan Dokumen
(Checklist detail, tips persiapan)

## Proses Pengurusan
(Step-by-step, timeline, biaya estimasi)

## FAQ [Jenis Izin]
(10-20 Q&A terstruktur — Schema FAQPage)

## Studi Kasus
(2-3 real examples)

## Tips dari Ahli Bizmark
(Expert insights, common mistakes)

## Hubungi Bizmark untuk Konsultasi Gratis
(CTA: WhatsApp, form, phone)
```

### Template 2: Comparison Article
```
# [Izin A] vs [Izin B]: Mana yang Anda Butuhkan?

## Tabel Perbandingan Cepat
(Side-by-side comparison table)

## Apa itu [Izin A]?
## Apa itu [Izin B]?
## Perbedaan Utama
## Kapan Memilih [Izin A]
## Kapan Memilih [Izin B]
## FAQ
## Konsultasi Gratis Bizmark
```

### Template 3: Case Study
```
# Studi Kasus: [Klien/Industri] Berhasil Mendapat [Izin] dalam [X Hari]

## Latar Belakang
## Tantangan yang Dihadapi
## Solusi Bizmark
## Hasil & Timeline
## Pelajaran untuk Anda
## Hubungi Kami
```

### Template 4: FAQ Compilation
```
# [X] Pertanyaan Paling Sering Ditanyakan tentang [Topik]

## Pertanyaan Umum
(Q1-Q10 dengan jawaban detail)

## Pertanyaan Teknis
(Q11-Q20)

## Pertanyaan Biaya & Waktu
(Q21-Q30)

## Masih Ada Pertanyaan?
(CTA ke konsultasi)
```

---

*Dokumen ini adalah living document yang harus di-review dan update setiap bulan berdasarkan data performa aktual.*

**Next Action**: Implementasi Phase 1 — jalankan Pipeline Data dan Content Velocity Engine.
