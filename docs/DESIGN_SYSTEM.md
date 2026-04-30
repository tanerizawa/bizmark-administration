# Bizmark.ID Design System — V2 (Editorial Premium B2B)

> Last updated: 2026-04-30
> Applies to: Landing pages + Admin panel (see §10 for admin Blade component library)

This document describes the **unified design language** used across all landing and content pages after the V2 redesign. Admin panel design is documented separately in [Section 10](#10-admin-blade-component-library) below.

---

## 1. Design Principles

| Principle | Application |
|---|---|
| **Editorial Premium** | Large display serif typography, generous whitespace, crisp 8pt grid, clear hierarchy (display / headline / title / body). |
| **Symmetry & Precision** | Strict 12-column grid, optical alignment, vertical rhythm in multiples of 8px. |
| **Conversion Psychology** | Cialdini flow: Social Proof → Authority → Scarcity → Reciprocity → Commitment. |
| **F-Pattern Reading** | H1 top-left, secondary info to the right, primary CTA in the F-path. |
| **Fitts's Law** | Primary CTAs min 48×48px, comfortable click areas, spacing between CTAs. |
| **Hick's Law** | Max 3 choices per decision point (segmentation MSME / Corp / PMA). |
| **Loss Aversion** | "Pain → Solution" section with visual transformation divider. |
| **Trust Transparency** | Real numbers (138+, 96%, 10+) — no vague claims. |

---

## 2. Design Tokens

### 2.1 Typography

```css
--font-display: 'Fraunces', Georgia, serif;   /* Display / editorial headlines */
--font-body:    'Inter', system-ui, sans-serif; /* Body + UI */
```

**Scale** (fluid, responsive via `clamp()`):

| Class | Font | Size | Use case |
|---|---|---|---|
| `.display-xl` | Fraunces 800 | `clamp(2.75rem, 5vw, 4.5rem)` | Hero H1 |
| `.display-lg` | Fraunces 700 | `clamp(2rem, 3.5vw, 3rem)` | H2 section |
| `.display-md` | Fraunces 700 | `clamp(1.5rem, 2.5vw, 2.25rem)` | H2 sub-section / newsletter |
| `.eyebrow`    | Inter 700 uppercase | `.75rem` with `.15em` tracking | Section label above headline |

**Body:** `1rem` (16px) with `line-height: 1.7`. Max `75ch` for readability.

### 2.2 Color Palette

```css
/* Primary scale — dark navy (authoritative) */
--color-primary:         #0f172a;
--color-primary-light:   #1e293b;

/* Accent (orange, action) */
--color-secondary:       #f97316;

/* Premium accent — gold */
--color-gold:            #b8860b;
--color-gold-light:      #d4a017;

/* Surfaces */
--surface-premium:       #faf8f3;  /* warm off-white — editorial sections */
--surface-cool:          #f1f5f9;  /* cool grey — cards, inputs */
--surface-ink:           #0a0e1a;  /* darkest — CTA sections */
--surface-warm:          #fffbf5;

/* Semantic */
--color-success:         #16a34a;
--color-danger:          #dc2626;
--color-info:            #0ea5e9;

/* Text */
--text-primary:          #0f172a;
--text-secondary:        #475569;
--text-tertiary:         #64748b;
--text-muted:            #94a3b8;

/* Borders */
--border-subtle:         #e2e8f0;
--border-medium:         #cbd5e1;
--border-light:          #f1f5f9;
```

**Rules:**
- **Navy is authority**, gold is premium accent — use gold sparingly (eyebrow color, rule dividers, CTA button, stat highlights).
- **Dark surfaces** ALWAYS use `.bg-ink-gradient` or `.bg-ink-gradient-soft` (no hardcoded hex chains like `#0a0e1a, #0f172a, #1e293b`).
- **Orange** (`--color-secondary`) is legacy — keep for existing accents but new work prefers gold.

### 2.3 Spacing (strict 8pt)

```
4 / 8 / 12 / 16 / 24 / 32 / 48 / 64 / 96 / 128
```

Section padding presets:

```css
.section-v2      { padding: clamp(4rem, 7vw, 7rem) 0; }
.section-v2-sm   { padding: clamp(2.5rem, 5vw, 4rem) 0; }
```

### 2.4 Radius, Shadow

```css
--radius-sm:   .375rem;
--radius-md:   .5rem;
--radius-lg:   .75rem;
--radius-xl:   1rem;
--radius-2xl:  1.25rem;
--radius-3xl:  1.5rem;
--radius-full: 9999px;
```

Shadows: `shadow-sm / shadow-md / shadow-lg / shadow-xl` only. **No** neomorphism or heavy glass effects.

---

## 3. Components

### 3.1 Buttons

| Class | Use case |
|---|---|
| `btn btn-primary` | Solid navy — default primary action on light |
| `btn btn-outline-primary` | Outline navy — secondary on light |
| `btn btn-gold` | Gold — **premium primary** (hero, final CTA, newsletter) |
| `btn btn-ghost-on-dark` | Transparent w/ border — secondary on dark |
| `btn btn-lg` | Large size modifier |
| `btn btn-sm` | Small size modifier |

```html
<a href="..." class="btn btn-gold btn-lg">
    <i class="fas fa-robot"></i>
    <span>Start Free Analysis</span>
</a>
```

### 3.2 Cards

| Class | Description |
|---|---|
| `.premium-card` | White card with `border-subtle`, `radius-2xl`, hover `translateY(-2px)` + shadow |
| `.premium-card.is-featured` | Adds gold top accent line |
| `.tool-card` | Ecosystem hub tools — uses `--_tool-color` custom property |
| `.article-card` | Vertical article card (image on top) |
| `.article-card-row` | Horizontal article card (image 140px left, text right) |

### 3.3 Layout helpers

| Class | Purpose |
|---|---|
| `.container-wide` | Max-width container with horizontal padding |
| `.section-v2` / `.section-v2-sm` | Section vertical padding |
| `.section-premium` | Uses `--surface-premium` background |
| `.section-ink` | Uses dark gradient (`.bg-ink-gradient`) for CTA sections |
| `.bg-ink-gradient` | Dark gradient: `#0a0e1a → #0f172a → #1a2540` |
| `.bg-ink-gradient-soft` | Softer dark variant: `#0f172a → #1e293b` |
| `.grid-equal` | `grid-auto-rows: 1fr` for equal-height cells |

### 3.4 Typography helpers

| Class | Purpose |
|---|---|
| `.eyebrow` | Small uppercase label above headline (gold option available) |
| `.gold-rule` | Short gold horizontal rule (editorial section opener) |
| `.display-xl` / `.display-lg` / `.display-md` | Fraunces display headlines |
| `.link-primary` | Inline navy link with gold underline on hover |

### 3.5 Forms

| Class | Purpose |
|---|---|
| `.form-input-dark` | Input with dark bg (for dark sections) — h=52px, matches `btn` height |
| `.form-row-v2` | Flex row aligning input + button, stacks on mobile |

```html
<form action="..." method="POST" class="form-row-v2">
    <input type="email" class="form-input-dark" ...>
    <button class="btn btn-gold">Subscribe</button>
</form>
```

### 3.6 Badges

- `.cert-badge` — pill with icon + label (ISO, certifications, category chips)
- `.article-cat` — small uppercase category pill (in article cards)

### 3.7 Stat cluster

`.stat-cluster` with `.stat-item` (value + label) — responsive flex with `clamp()` gap.

### 3.8 Timeline

`.timeline-v2` with `.timeline-node` — vertical timeline with gold-dot nodes (used on `/tentang`).

---

## 4. Section Architecture (Home)

Home page (`resources/views/landing/pages/home.blade.php`) is an orchestrator that includes 12 section partials from `resources/views/landing/sections/v2/`:

1. `hero.blade.php` — Editorial split hero (7/5 cols) with proof card
2. `trust-strip.blade.php` — Client logos + certification badges
3. `segmentation.blade.php` — 3 audience paths (MSME / Corp / PMA)
4. `ecosystem-hub.blade.php` — **Flagship:** 4 free tools (2×2) + 4 secondary links
5. `services.blade.php` — 6 core services (3×2 grid, equal height)
6. `pain-solution.blade.php` — Problem → gold-arrow-divider → Bizmark solution
7. `process.blade.php` — 4-step horizontal timeline
8. `case-studies.blade.php` — 3 testimonials on dark gradient
9. `articles.blade.php` — **Pulls `$latestArticles` from DB** (1 featured + 2 row), emits `ItemList` schema
10. `faq.blade.php` — Top 6 FAQs with `FAQPage` schema
11. `newsletter.blade.php` — Lead magnet on dark gradient with `/subscribe` route
12. `final-cta.blade.php` — Dark section with gold rule + 2 CTAs

### Blog pages

Files: `resources/views/landing/blog.blade.php`, `landing/category.blade.php`, `landing/tag.blade.php`

- `blog.blade.php` — Featured article + grid (3-col) with pagination
- `category.blade.php` — Category listing with breadcrumb + CollectionPage schema
- `tag.blade.php` — Tag listing with breadcrumb

> **Note:** Blog article detail (`resources/views/landing/blog/show.blade.php`) is an 8-col article + 4-col sticky sidebar (CTA + TOC + author); reading progress bar; auto-generated TOC from H2/H3. This file is **not part of the landing page** and is maintained separately.

### Service Inquiry pages

Files: `resources/views/landing/service-inquiry/create.blade.php`, `result.blade.php`, `not-found.blade.php`

- `create.blade.php` — Multi-step form (browser, location, confirm, email) with progress indicator
- `result.blade.php` — **Standalone page** (CDN Alpine.js, not Vite-bundled) — displays KBLI analysis, permit recommendations, risk factors, required docs
- `not-found.blade.php` — **Standalone page** (CDN Alpine.js, not Vite-bundled) — 404-style result with alternative suggestions
---

## 5. SEO & Schema

Every page emits `@@section('structured_data')` with:

| Page type | Schemas |
|---|---|
| Home | `LocalBusiness` + `ItemList` (articles) + `FAQPage` |
| Article | `Article` + `BreadcrumbList` |
| Blog index | `Blog` + `BreadcrumbList` |
| Category | `CollectionPage` + `BreadcrumbList` |
| Tag | `BreadcrumbList` |
| Service | `Service` + `BreadcrumbList` |

Sitemap is split into 4 files: `sitemap-static.xml` (73 URLs), `sitemap-services.xml` (43), `sitemap-articles.xml` (134), `sitemap-cities.xml` — all linked via `sitemap.xml` index.

Regenerate: `php artisan sitemap:generate`
Validate: `php artisan sitemap:validate`

---

## 6. Accessibility

- All interactive elements have visible `:focus-visible` states
- ARIA labels on icon-only buttons; `aria-labelledby` on sections with IDs
- `prefers-reduced-motion` disables animations globally
- `section[id] { scroll-margin-top: 96px; }` prevents sticky-nav overlap on anchor jumps
- Images have `alt` text; decorative use `aria-hidden="true"`

---

## 7. File Locations

```
resources/views/
├── landing/
│   ├── layout.blade.php                # Master layout (Alpine.js via Vite)
│   ├── blog.blade.php                  # Blog index (paginated)
│   ├── category.blade.php              # Blog category listing
│   ├── tag.blade.php                   # Blog tag listing
│   ├── blog/
│   │   └── show.blade.php              # [EXCLUDED] Blog article detail (standalone)
│   ├── service-inquiry/
│   │   ├── create.blade.php            # Multi-step form (extends layout)
│   │   ├── result.blade.php            # [CDN Alpine] KBLI analysis result
│   │   └── not-found.blade.php         # [CDN Alpine] 404 result page
│   ├── partials/
│   │   ├── head.blade.php              # Meta tags, Vite bundles, schema
│   │   ├── navbar.blade.php            # Alpine.js dropdowns + $dispatch
│   │   ├── mobile-menu.blade.php       # Alpine.js x-data mobile panel
│   │   ├── search-modal.blade.php      # Alpine.js x-data search modal
│   │   ├── footer.blade.php            # Landing footer (4-col)
│   │   └── scripts.blade.php           # Minimal JS (tracking, AOS init)
│   ├── pages/
│   │   ├── home.blade.php              # Orchestrator (12 @include)
│   │   ├── about.blade.php             # About page
│   │   └── process.blade.php           # Process page
│   └── sections/
│       └── v2/
│           ├── hero.blade.php
│           ├── trust-strip.blade.php
│           ├── segmentation.blade.php
│           ├── ecosystem-hub.blade.php
│           ├── services.blade.php
│           ├── pain-solution.blade.php
│           ├── process.blade.php
│           ├── case-studies.blade.php
│           ├── articles.blade.php
│           ├── faq.blade.php
│           ├── newsletter.blade.php
│           └── final-cta.blade.php
├── components/ui/                      # 27 x-ui.* Blade components (admin)
│   └── *.blade.php
```

---

## 8. Do & Don't

✅ **DO**
- Use utility classes (`btn-gold`, `bg-ink-gradient`, `form-input-dark`) instead of inline styles
- Pair navy (authority) with gold (premium) — never overuse gold
- Stick to 8pt spacing and `clamp()` fluid typography
- Emit relevant Schema.org markup on every content page

❌ **DON'T**
- Don't hardcode hex colors for dark surfaces (`#0a0e1a 0%, #0f172a 60%…`) — use `.bg-ink-gradient`
- Don't mix button styles within one CTA group (choose one primary, one secondary)
- Don't use bright accent colors (pure red/green/yellow) as backgrounds — use as icon colors only
- Don't add bespoke section partials without extracting reusable utilities if the pattern repeats

---

## 9. Rollback

Backups of the original design (pre-V2):
- Section partials: saved in `.bak3` siblings where applicable
- `about.blade.php` / `process.blade.php`: `/tmp/about-v1.blade.php.backup`, `/tmp/process-v1.blade.php.backup`

Git history per commit in `landing-redesign-comprehensive` plan (C1→C6) for granular revert.

---

## 11. Landing Page Migration — Inline Style/JS Cleanup

> Completed: 2026-04-30
> Scope: All 27 landing page files (layout, 6 partials, 12 v2 sections, 3 standalone pages, 3 blog pages, 2 about/process pages)
> Status: **100% COMPLETE**

### 11.1 Migration Summary

All landing page files have been audited and migrated to eliminate inline styles and inline JavaScript event handlers, replacing them with Tailwind CSS utility classes and Alpine.js directives respectively.

| Violation Type | Before | After |
|----------------|--------|-------|
| `style="..."` attributes | 47 instances across 27 files | **0** (replaced with Tailwind classes or arbitrary values) |
| `onclick="..."` handlers | 34 instances | **0** (replaced with Alpine `@click`, `$dispatch`, or native JS) |
| `onmouseover`/`onmouseout` | 4 instances | **0** (replaced with Alpine `@mouseenter`/`@mouseleave`) |
| `onchange="..."` | 2 instances | **0** (replaced with Alpine `@change` or `x-model`) |
| Inline `<style>` blocks | 5 files (faq, scripts, result, not-found) | Minimized — only `@keyframes`, `@media print`, `@media (prefers-reduced-motion: reduce)` retained |
| Inline `<script>` blocks | 8 files (layout, scripts, navbar, result, not-found, index, category, tag) | Minimized — only vanilla JS for polling, tracking, page reloads retained |

### 11.2 Alpine.js Migration Patterns Used

| Pattern | Old (inline JS) | New (Alpine.js) |
|---------|-----------------|-----------------|
| Mobile menu toggle | `onclick="toggleMobileMenu()"` + DOM querySelector | `@click="$dispatch('open-mobile-menu')"` + `x-data`, `x-show`, `x-transition` |
| Search modal | `onclick="toggleSearch()"` | `@click="$dispatch('open-search')"` + `@open-search.window` |
| Navbar dropdowns | `onclick="this...classList.toggle('hidden')"` | `x-data="{ open: false }"`, `@click="open = !open"`, `x-show` |
| FAQ accordion | DOM-based toggle with `hidden` class | Alpine `x-data`, `@click`, `x-show` with `x-collapse` |
| Locale selector | `onclick="document.getElementById('X').classList.toggle('hidden')"` | `x-data`, `@click`, `x-show`, `@click.outside` |
| Click-outside close | `document.addEventListener('click', function(e) { ... })` | `@click.outside="open = false"` |
| Keyboard escape | `document.addEventListener('keydown', ...)` | `@keydown.escape.window="open = false"` |
| Cross-component event | `window.dispatchEvent(...)` | `$dispatch('event-name', { ... })` + `@event-name.window="handler"` |
| Print trigger | `onclick="window.print()"` | `@click="window.print()"` |
| Loading states | Manual class toggling | Alpine `x-data` with `loading` boolean, `x-show` for spinner |

### 11.3 Inline Style to Tailwind Conversion Patterns

| Old Inline Style | Tailwind Equivalent |
|------------------|---------------------|
| `style="line-height:1.15"` | `leading-[1.15]` |
| `style="background: linear-gradient(135deg, #0A66C2, #00A0DC); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;"` | `bg-gradient-to-r from-[#0A66C2] to-[#00A0DC] bg-clip-text text-transparent` |
| `style="background: linear-gradient(135deg, #0A66C2, #004182);"` | `bg-gradient-to-r from-[#0A66C2] to-[#004182]` |
| `style="animation-delay: 0ms"` | `[animation-delay:0ms]` |
| `style="display:none"` | `class="hidden"` |
| `style="min-height: 300px"` | `min-h-[300px]` |

### 11.4 CSS Class to Tailwind Arbitrary Value Conversion

For pages using CDN Tailwind (standalone pages like `result.blade.php`, `not-found.blade.php`), CSS animation classes were replaced with Tailwind arbitrary values to minimize the `<style>` block:

| Old CSS Class | Tailwind Arbitrary Value |
|---------------|--------------------------|
| `.card-hover` (`translateY(-4px)` + shadow on hover) | `transition-all duration-300 hover:-translate-y-1 hover:shadow-[0_20px_25px_-5px_rgba(0,0,0,0.1),0_10px_10px_-5px_rgba(0,0,0,0.04)]` |
| `.btn-lift` (`translateY(-1px)` on hover) | `transition-all duration-300 hover:-translate-y-0.5 hover:shadow-[0_10px_25px_rgba(10,102,194,0.25)]` |
| `.animate-fade-in` (`opacity:0; animation: fadeIn 0.6s ease forwards`) | `animate-[fadeIn_0.6s_ease_forwards]` |
| `.stagger-item` (`opacity:0; animation: slideUp 0.5s ease forwards`) | `animate-[fadeIn_0.6s_ease_forwards] [animation-delay:...ms]` |
| `.fab-pulse` (`animation: fabPulse 2s ease-in-out infinite`) | `animate-[fabPulse_2s_ease-in-out_infinite]` |
| `.delay-100` through `.delay-300` | `[animation-delay:100ms]` through `[animation-delay:300ms]` |

### 11.5 Files Migrated

**Layout and Partials (7 files):**
- `layout.blade.php` — Removed CDN Alpine.js (now Vite-bundled), removed back-to-top inline JS, converted inline styles
- `head.blade.php` — No inline styles/JS
- `navbar.blade.php` — Converted all dropdown `onclick` to Alpine `x-data` + `@click`; removed inline styles
- `mobile-menu.blade.php` — Converted from DOM-based toggle to Alpine `x-data` + `$dispatch`
- `search-modal.blade.php` — Converted from DOM-based toggle to Alpine `x-data` + `$dispatch`
- `footer.blade.php` — Converted inline styles to Tailwind
- `scripts.blade.php` — Removed dropdown system JS (now Alpine), removed FAQ accordion JS

**Section Partials (12 files):**
- `hero.blade.php` — Converted inline styles to Tailwind, inline `onclick` to `@click`
- `services.blade.php` — Converted inline styles to Tailwind
- `pain-solution.blade.php` — Converted inline styles to Tailwind
- `process.blade.php` — Converted inline styles to Tailwind
- `trust-strip.blade.php` — Converted inline styles to Tailwind
- `segmentation.blade.php` — Converted inline styles to Tailwind
- `ecosystem-hub.blade.php` — Converted inline styles to Tailwind
- `case-studies.blade.php` — Converted inline styles to Tailwind
- `faq.blade.php` — Converted inline styles + removed inline `<style>` block (replaced with Alpine `x-collapse`)
- `articles.blade.php` — Converted inline styles to Tailwind
- `newsletter.blade.php` — Converted inline styles to Tailwind
- `final-cta.blade.php` — Converted inline styles to Tailwind

**Standalone Pages (3 files):**
- `about.blade.php`, `process.blade.php` — Converted inline styles to Tailwind

**Blog Pages (3 files):**
- `blog.blade.php`, `category.blade.php`, `tag.blade.php` — Converted inline styles to Tailwind, removed inline `<script>` blocks

**Service Inquiry Pages (3 files):**
- `create.blade.php` — Converted 3 inline styles (gradient text, progress bar, animation delays)
- `result.blade.php` — **Major rewrite** (911 lines): Added CDN Alpine.js, converted locale dropdown + mobile menu from DOM to Alpine, removed inline `<script>` block, converted all CSS animation classes to Tailwind arbitrary values, minimized `<style>` block
- `not-found.blade.php` — **Full rewrite** (256 lines): Added CDN Alpine.js, converted locale dropdown + mobile menu to Alpine, removed inline `<script>` block, minimized `<style>` block

### 11.6 Excluded Files

The following file is explicitly **excluded** from the landing page scope and retains its inline styles:

- `resources/views/landing/blog/show.blade.php` — Blog article detail page (separate feature, not part of landing page per product decision)

### 11.7 Verification

- **Build verification**: Run `npm run build` — must pass without errors
- **Visual regression**: All converted pages display correctly with same visual appearance
- **Accessibility**: All Alpine components retain proper `role`, `aria-*`, and keyboard navigation
- **Dark mode**: All converted components maintain `dark:` class support

---

## 10. Admin Blade Component Library

> Added: 2026-04-30
> Status: Phase 0 (Design Tokens) + Phase 1 (27 Components) — **COMPLETED**
> Reference plan: [`plans/UI_ARCHITECTURE_LONG_TERM_PLAN.md`](/plans/UI_ARCHITECTURE_LONG_TERM_PLAN.md)

The admin panel UI is built on a **Custom Blade Component Library** with `x-ui.*` prefix, powered by a unified 3-layer design token system.

### 10.1 Design Tokens (Cross-Cutting)

A single source of truth at [`resources/css/design-tokens.css`](/resources/css/design-tokens.css) replaces the previous 3 conflicting CSS variable systems (`admin.css`, `neuroscience-variables.css`, `landing-theme.css`).

| Layer | Description | Example |
|-------|-------------|---------|
| Layer 1: Base | Raw brand values (serene blue `#5B8DBE`, warm coral `#E8956F`, healing green `#7CB342`) | `--color-primary: #5B8DBE` |
| Layer 2: Semantic | Context-mapped tokens for surfaces, text, borders, shadows | `--color-surface`, `--color-text-primary` |
| Layer 3: Component | Per-component override tokens | `--btn-primary-bg`, `--card-padding` |

Dark mode:
- **Admin**: `[data-theme="dark"]` selector on `<html>` — toggleable via user preference
- **Landing**: `@media (prefers-color-scheme: dark)` — follows OS preference

### 10.2 Component Prefix

```blade
{{-- All admin UI components use x-ui.* prefix --}}
<x-ui.button variant="primary">Submit</x-ui.button>
<x-ui.card variant="elevated">Content</x-ui.card>
<x-ui.table :columns="$cols" :rows="$data" />
```

### 10.3 Complete Component Inventory (27 components)

**Tier 1 — Core (Static):**

| Component | File | Variants/Features |
|-----------|------|-------------------|
| [`x-ui.button`](/resources/views/components/ui/button.blade.php) | `button.blade.php` | 5 variants (primary/secondary/outline/ghost/danger), 3 sizes (sm/md/lg), link mode (`href`), loading state |
| [`x-ui.badge`](/resources/views/components/ui/badge.blade.php) | `badge.blade.php` | 5 color variants, pill shape, dot indicator |
| [`x-ui.card`](/resources/views/components/ui/card.blade.php) | `card.blade.php` | 3 variants (elevated/bordered/flat), 4 padding sizes, header/footer slots |
| [`x-ui.input`](/resources/views/components/ui/input.blade.php) | `input.blade.php` | 7 types, leading/trailing icons, error state, helper text |
| [`x-ui.select`](/resources/views/components/ui/select.blade.php) | `select.blade.php` | Native select + Alpine.js searchable variant with live filtering |
| [`x-ui.textarea`](/resources/views/components/ui/textarea.blade.php) | `textarea.blade.php` | Character counter via Alpine.js, resize control, maxLength |
| [`x-ui.checkbox`](/resources/views/components/ui/checkbox.blade.php) | `checkbox.blade.php` | 3 sizes, indeterminate state via `$refs`, description text |
| [`x-ui.toggle`](/resources/views/components/ui/toggle.blade.php) | `toggle.blade.php` | Alpine.js switch, 4 color variants, 3 sizes, keyboard accessible |
| [`x-ui.alert`](/resources/views/components/ui/alert.blade.php) | `alert.blade.php` | 4 variants (success/warning/danger/info), dismissible with Alpine.js |
| [`x-ui.stat-card`](/resources/views/components/ui/stat-card.blade.php) | `stat-card.blade.php` | Trend indicator, 5 color variants, icon support |

**Tier 2 — Interactive (Alpine.js-powered):**

| Component | File | Alpine.js Features |
|-----------|------|-------------------|
| [`x-ui.modal`](/resources/views/components/ui/modal.blade.php) | `modal.blade.php` | `x-teleport`, `x-transition`, 5 sizes, backdrop with blur, Escape to close |
| [`x-ui.dropdown`](/resources/views/components/ui/dropdown.blade.php) | `dropdown.blade.php` | `@click.outside`, `@keydown.escape`, transition, left/right align |
| [`x-ui.tabs`](/resources/views/components/ui/tabs.blade.php) | `tabs.blade.php` | `x-id` for aria bindings, underline/pills variants, badge support |
| [`x-ui.table`](/resources/views/components/ui/table.blade.php) | `table.blade.php` | Scoped slots per column, sortable headers, striped/hoverable variants |
| [`x-ui.pagination`](/resources/views/components/ui/pagination.blade.php) | `pagination.blade.php` | Laravel Paginator integration, simple/full variants, showing info |
| [`x-ui.toast`](/resources/views/components/ui/toast.blade.php) | `toast.blade.php` | Global `$dispatch('toast', {...})` system, 4 types (success/error/warning/info), 6 positions |
| [`x-ui.progress`](/resources/views/components/ui/progress.blade.php) | `progress.blade.php` | Determinate + indeterminate modes, 5 color variants, label positioning |
| [`x-ui.skeleton`](/resources/views/components/ui/skeleton.blade.php) | `skeleton.blade.php` | 5 variants (text/circle/rect/card/table), animate-pulse |

**Tier 3 — Layout & Navigation:**

| Component | File | Features |
|-----------|------|----------|
| [`x-ui.breadcrumb`](/resources/views/components/ui/breadcrumb.blade.php) | `breadcrumb.blade.php` | 3 separator styles (chevron/slash/dot), home icon, `aria-current="page"` |
| [`x-ui.avatar`](/resources/views/components/ui/avatar.blade.php) | `avatar.blade.php` | Image/initials/fallback icon, 5 sizes, 3 shapes, status dot (online/offline/away/busy) |
| [`x-ui.empty-state`](/resources/views/components/ui/empty-state.blade.php) | `empty-state.blade.php` | Custom or default icon, title, description, action button via `x-ui.button` |
| [`x-ui.radio-group`](/resources/views/components/ui/radio-group.blade.php) | `radio-group.blade.php` | Default inline + card variant, option descriptions, per-option disabled |
| [`x-ui.file-upload`](/resources/views/components/ui/file-upload.blade.php) | `file-upload.blade.php` | Alpine.js drag & drop (`@dragover`/`@drop`), accept/maxSize/maxFiles, file preview |
| [`x-ui.tooltip`](/resources/views/components/ui/tooltip.blade.php) | `tooltip.blade.php` | 4 positions, configurable delay, CSS arrow, hover/focus triggers |
| [`x-ui.accordion`](/resources/views/components/ui/accordion.blade.php) | `accordion.blade.php` | Alpine.js `x-collapse`, single/multiple modes, 3 variants (default/bordered/ghost) |
| [`x-ui.dropdown-item`](/resources/views/components/ui/dropdown-item.blade.php) | `dropdown-item.blade.php` | Link or form-based (POST/PUT/DELETE), icon, danger variant |
| [`x-ui.dropdown-divider`](/resources/views/components/ui/dropdown-divider.blade.php) | `dropdown-divider.blade.php` | Simple border divider for dropdown menus |

### 10.4 Migration Path from Landing CSS Classes to Blade Components

When migrating landing page sections to use the Blade component library, use this mapping:

| Old CSS Class | Equivalent Blade Component |
|---------------|---------------------------|
| `.btn-gold` | `<x-ui.button variant="primary" class="bg-gold">` |
| `.btn-primary` | `<x-ui.button variant="primary">` |
| `.btn-outline-primary` | `<x-ui.button variant="outline">` |
| `.premium-card` | `<x-ui.card variant="elevated">` |
| `.article-card` | `<x-ui.card variant="bordered">` |
| `.cert-badge` | `<x-ui.badge variant="info">` |
| `.stat-cluster .stat-item` | Grid of `<x-ui.stat-card>` |
| `.form-input-dark` | `<x-ui.input>` with dark mode classes |

> **Note:** Landing page CSS utility classes (`.btn-gold`, `.bg-ink-gradient`, `.display-xl`) remain valid for the landing page due to its unique editorial/premium identity. The Blade component library is primarily for admin panel and form-heavy pages.

### 10.5 Design Rules for Components

All components adhere to these strict rules (enforced via Roo Code rules):

1. **Zero inline styles** — all styling via Tailwind utility classes
2. **Zero inline JS event handlers** — all interactivity via Alpine.js directives
3. **Zero hardcoded colors** — all colors via CSS custom properties (`var(--color-*)`)
4. **Named parameters required** — always use `variant="primary"`, never positional
5. **Dark mode required** — every component has `dark:` variants
6. **Accessibility required** — proper `role`, `aria-*`, keyboard navigation
7. **Component-forwarded attributes** — via `$attributes->merge()`

### 10.6 File Locations

```
resources/
├── views/components/ui/           # 27 Blade components (*.blade.php)
├── css/
│   ├── design-tokens.css          # [NEW] 3-layer token architecture
│   ├── app.css                    # Imports design-tokens + global
│   ├── admin.css                  # Admin overrides (minimal)
│   └── landing.css                # Landing overrides (minimal)
.roo/rules/
├── global-design-system.md        # Design fundamentals
├── blade-component-api.md         # Component API conventions
├── tailwind-css-usage.md          # Tailwind v4 usage rules
└── alpine-js-patterns.md          # Alpine.js interaction patterns
```
