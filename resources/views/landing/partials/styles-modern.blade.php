{{-- @deprecated CSS moved to resources/css/landing-theme.css --}}
{{-- Keep this file temporarily as reference, will be removed after testing --}}
{{-- DARK TECH STARTUP DESIGN SYSTEM --}}
{{-- Deep Navy + Electric Blue + Fraunces/Inter --}}
<style>
@layer base {

/* ======================================
   DARK TECH STARTUP — Design Tokens
   Deep Navy #0a0f1e + Electric Blue #3b82f6
====================================== */
:root {
    /* === Surfaces === */
    --bg-base:    #0a0f1e;
    --bg-raised:  #0f1629;
    --bg-overlay: #131c35;

    /* === Accent === */
    --accent:      #3b82f6;
    --accent-dark: #2563eb;
    --accent-soft: #60a5fa;
    --accent-dim:  rgba(59,130,246,.55);
    --accent-glow: rgba(59,130,246,.12);
    --accent-rgb:  59, 130, 246;

    /* === Text === */
    --text-primary:   #f1f5f9;
    --text-secondary: #94a3b8;
    --text-muted:     #475569;
    --text-inverse:   #0a0f1e;

    /* === Borders === */
    --border-subtle: rgba(255,255,255,.07);
    --border-light:  rgba(255,255,255,.07);
    --border-medium: rgba(255,255,255,.12);
    --border-strong: rgba(255,255,255,.22);

    /* === Status === */
    --color-success: #22c55e;
    --color-danger:  #ef4444;
    --color-warning: #f59e0b;

    /* === Legacy compat (referenced by existing blade inline styles) === */
    --color-primary:      #1e3a8a;
    --color-primary-dark: #050810;
    --color-primary-light:#1e3a8a;
    --color-secondary:    #3b82f6;
    --color-accent:       #3b82f6;
    --color-gold:         #f59e0b;
    --color-gold-light:   #fbbf24;
    --surface:            #0a0f1e;
    --surface-primary:    #0a0f1e;
    --surface-secondary:  #0f1629;
    --surface-cool:       #0f1629;
    --surface-warm:       #0f1629;
    --surface-panel:      #131c35;
    --surface-panel-strong: #1a2545;
    --surface-dark:       #050810;
    --surface-premium:    #0f1629;
    --navbar-bg:          rgba(10,15,30,.85);
    --surface-ink:        #050810;
    --text-tertiary:      #475569;
    --text-ink:           #f1f5f9;
    --text-inverse:       #0a0f1e;
    --color-primary-rgb:  10, 15, 30;
    --primary: #0a0f1e;
    --primary-dark: #050810;
    --gray-900: #0a0f1e;

    /* === Fonts === */
    --font-display: 'Fraunces', 'Playfair Display', Georgia, serif;
    --font-sans:    'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;

    /* === Radii === */
    --radius-sm:   4px;
    --radius-md:   8px;
    --radius-lg:   10px;
    --radius-xl:   12px;
    --radius-2xl:  16px;
    --radius-full: 9999px;

    /* === Shadows === */
    --shadow-sm:   0 1px 4px rgba(0,0,0,.4);
    --shadow-md:   0 4px 16px rgba(0,0,0,.5);
    --shadow-lg:   0 8px 32px rgba(0,0,0,.55);
    --shadow-xl:   0 16px 48px rgba(0,0,0,.6);
    --shadow-ring: 0 0 0 3px rgba(59,130,246,.25);
    --shadow-blue: 0 0 0 1px rgba(59,130,246,.35), 0 4px 24px rgba(59,130,246,.12);
}

/* ======================================
   LIGHT MODE OVERRIDES
   Triggered by data-theme="light" on <html>
====================================== */
html[data-theme="light"] {
    /* === Surfaces (inverted) === */
    --bg-base:         #ffffff;
    --bg-raised:       #f8fafc;
    --bg-overlay:      #f1f5f9;

    /* === Accent (keep same brand blue but softer) === */
    --accent:          #2563eb;
    --accent-dark:     #1d4ed8;
    --accent-soft:     #3b82f6;
    --accent-dim:      rgba(37,99,235,.45);
    --accent-glow:     rgba(37,99,235,.08);
    --accent-rgb:      37, 99, 235;

    /* === Text (inverted) === */
    --text-primary:    #0f172a;
    --text-secondary:  #475569;
    --text-muted:      #94a3b8;
    --text-inverse:    #f1f5f9;

    /* === Borders (lighter) === */
    --border-subtle:   rgba(0,0,0,.07);
    --border-light:    rgba(0,0,0,.07);
    --border-medium:   rgba(0,0,0,.12);
    --border-strong:   rgba(0,0,0,.18);

    /* === Legacy compat === */
    --color-primary:        #1e40af;
    --color-primary-dark:   #1e3a8a;
    --color-primary-light:  #3b82f6;
    --color-gold:           #d97706;
    --color-gold-light:     #f59e0b;
    --surface:              #ffffff;
    --surface-primary:      #ffffff;
    --surface-secondary:    #f8fafc;
    --surface-cool:         #f1f5f9;
    --surface-warm:         #fefce8;
    --surface-panel:        #f8fafc;
    --surface-panel-strong: #e2e8f0;
    --surface-dark:         #f1f5f9;
    --surface-premium:      #f8fafc;
    --navbar-bg:            rgba(255,255,255,.90);
    --surface-ink:          #0f172a;
    --text-tertiary:        #94a3b8;
    --text-ink:             #0f172a;
    --color-primary-rgb:    255, 255, 255;
    --primary:              #ffffff;
    --primary-dark:         #f1f5f9;
    --gray-900:             #0f172a;

    /* === Shadows (lighter for light mode) === */
    --shadow-sm:   0 1px 4px rgba(0,0,0,.06);
    --shadow-md:   0 4px 16px rgba(0,0,0,.08);
    --shadow-lg:   0 8px 32px rgba(0,0,0,.10);
    --shadow-xl:   0 16px 48px rgba(0,0,0,.12);
    --shadow-ring: 0 0 0 3px rgba(37,99,235,.20);
    --shadow-blue: 0 0 0 1px rgba(37,99,235,.25), 0 4px 24px rgba(37,99,235,.08);
}

/* ======================================
   TYPOGRAPHY UTILITIES
====================================== */
.font-display { font-family: var(--font-display); font-feature-settings: 'ss01','ss02'; }
.display-xl { font-family: var(--font-display); font-weight: 700; font-size: clamp(2.75rem, 6vw, 4.5rem); line-height: 1.02; letter-spacing: -0.035em; color: var(--text-primary); }
.display-lg { font-family: var(--font-display); font-weight: 700; font-size: clamp(1.875rem, 3.5vw, 2.75rem); line-height: 1.08; letter-spacing: -0.025em; color: var(--text-primary); }
.display-md { font-family: var(--font-display); font-weight: 700; font-size: clamp(1.5rem, 2.5vw, 2rem); line-height: 1.12; letter-spacing: -0.02em; color: var(--text-primary); }

/* EYEBROW */
.eyebrow {
    font-family: var(--font-sans);
    font-size: .6875rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .14em;
    color: var(--accent-soft);
    display: inline-block;
}

/* BODY */
body {
    font-family: var(--font-sans);
    background: var(--bg-base);
    color: var(--text-primary);
    font-size: 1rem;
    line-height: 1.65;
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
    overflow-x: hidden;
    min-height: 100vh;
}
h1,h2,h3,h4,h5,h6 { font-family: var(--font-display); font-weight: 700; line-height: 1.15; letter-spacing: -0.025em; color: var(--text-primary); }
h1 { font-size: clamp(2.25rem,5vw,3.75rem); font-weight: 800; line-height: 1.06; letter-spacing: -0.035em; }
h2 { font-size: clamp(1.75rem,3vw,2.5rem); }
h3 { font-size: 1.125rem; font-weight: 600; }
p  { line-height: 1.7; margin-bottom: .875rem; color: var(--text-secondary); }
a  { color: inherit; }

/* LAYOUT */
.container,.container-wide { width: 100%; margin: 0 auto; padding-inline: clamp(1rem,4vw,2.5rem); }
.container      { max-width: 1100px; }
.container-wide { max-width: 1280px; }
.section     { padding: clamp(2rem,4vw,3rem) 0; }
.section-sm  { padding: clamp(1.25rem,2.5vw,2rem) 0; }
.section-v2     { padding-block: clamp(2rem,4.5vw,3.5rem); }
.section-v2-sm  { padding-block: clamp(1.25rem,2.5vw,2rem); }
.section-premium  { background: var(--bg-raised); }
.section-ink      { background: var(--bg-base); }
.section-surface-dark { background: var(--bg-base); }
.section-divider  { border-top: 1px solid var(--border-subtle); }

/* SECTION HEADER UTILITIES */
.section-badge,.section-label {
    display: inline-flex; align-items: center; gap: .375rem;
    font-size: .6875rem; font-weight: 700; text-transform: uppercase; letter-spacing: .14em;
    color: var(--accent-soft); padding: .3rem .875rem;
    background: rgba(59,130,246,.08); border-radius: var(--radius-full);
    border: 1px solid rgba(59,130,246,.15); margin-bottom: .875rem;
}
.section-title {
    font-family: var(--font-display); font-size: clamp(1.625rem,3vw,2.25rem);
    font-weight: 700; line-height: 1.15; letter-spacing: -0.025em;
    color: var(--text-primary); margin-bottom: .875rem;
}
.section-description { font-size: 1.0625rem; line-height: 1.7; color: var(--text-secondary); max-width: 52ch; margin: 0 auto; }

/* CARD */
.premium-card {
    background: var(--bg-raised); border: 1px solid var(--border-subtle);
    border-radius: var(--radius-xl); padding: 1.375rem 1.5rem;
    transition: border-color .25s,box-shadow .25s,transform .25s; position: relative;
}
.premium-card:hover { border-color: rgba(59,130,246,.25); transform: translateY(-2px); }
.premium-card.is-featured { border-color: rgba(59,130,246,.35); box-shadow: 0 0 0 1px rgba(59,130,246,.2),0 8px 32px rgba(59,130,246,.08); }
.premium-card.is-featured::after {
    content: ''; position: absolute; top: 0; left: 0; right: 0; height: 2px;
    background: linear-gradient(90deg,var(--accent),#60a5fa);
    border-radius: var(--radius-xl) var(--radius-xl) 0 0;
}
.card { background: var(--bg-raised); border: 1px solid var(--border-subtle); border-radius: var(--radius-xl); padding: 1.375rem 1.5rem; transition: border-color .25s,box-shadow .25s,transform .25s; }
.card:hover { border-color: rgba(59,130,246,.25); transform: translateY(-2px); }
.card-title { transition: color .2s; }
.card:hover .card-title { color: var(--accent); }
.magazine-card { background: var(--bg-raised); border: 1px solid var(--border-subtle); border-radius: var(--radius-xl); transition: border-color .25s,box-shadow .25s; }
.magazine-card:hover { border-color: rgba(59,130,246,.25); }

/* ICON BADGE */
.editorial-icon-badge {
    width: 2.625rem; height: 2.625rem; border-radius: .75rem;
    display: inline-flex; align-items: center; justify-content: center;
    background: rgba(59,130,246,.08); border: 1px solid rgba(59,130,246,.14);
    flex-shrink: 0; color: var(--accent-soft);
}
.editorial-icon-badge.is-circle { border-radius: 999px; }
.editorial-icon-badge.is-dark   { background: rgba(255,255,255,.06); border-color: rgba(255,255,255,.1); }

/* ICON SIZES */
.icon-xl { font-size: 1.375rem; line-height: 1; display: inline-block; }
.icon-lg { font-size: 1.25rem;  line-height: 1; display: inline-block; }
.icon-md { font-size: 1.0625rem;line-height: 1; display: inline-block; }
.icon-sm { font-size: .875rem;  line-height: 1; display: inline-block; }

/* STATUS BADGE */
.status-badge { display: inline-flex; align-items: center; gap: .5rem; font-size: .6875rem; font-weight: 700; text-transform: uppercase; letter-spacing: .12em; }
.status-badge .status-dot { width: 1.75rem; height: 1.75rem; border-radius: 999px; display: inline-flex; align-items: center; justify-content: center; flex-shrink: 0; }
.status-badge.is-danger  { color: var(--color-danger); }
.status-badge.is-danger  .status-dot { background: rgba(239,68,68,.12); color: var(--color-danger); }
.status-badge.is-success { color: var(--color-success); }
.status-badge.is-success .status-dot { background: rgba(34,197,94,.12); color: var(--color-success); }

/* SUBTLE LINK CARD */
.subtle-link-card { display: flex; align-items: center; gap: .75rem; padding: .75rem 1rem; border-radius: var(--radius-lg); border: 1px solid transparent; text-decoration: none; color: inherit; background: transparent; transition: background .2s,border-color .2s; }
.subtle-link-card:hover { background: var(--bg-overlay); border-color: var(--border-subtle); }

/* ACCENT RULE (decorative line) */
.gold-rule, .blue-rule { display: inline-block; width: 36px; height: 2px; background: var(--accent); margin-bottom: 1.25rem; }

/* METRIC STACK */
.metric-stack { display: grid; gap: .25rem; }
.metric-stack .metric-value { font-family: var(--font-display); font-size: 1.625rem; font-weight: 700; line-height: 1; color: var(--text-primary); letter-spacing: -0.03em; }
.metric-stack .metric-label  { font-size: .875rem; line-height: 1.5; color: var(--text-secondary); }

/* TIMELINE STEP */
.timeline-step-card { height: 100%; display: flex; flex-direction: column; gap: .875rem; padding: 1.25rem; }
.timeline-step-top  { display: flex; align-items: center; justify-content: space-between; gap: .75rem; }
.timeline-step-num {
    display: inline-flex; align-items: center; justify-content: center;
    width: 2.5rem; height: 2.5rem; border-radius: 999px;
    border: 1px solid rgba(59,130,246,.3); background: rgba(59,130,246,.08);
    color: var(--accent); font-family: var(--font-sans); font-size: .8125rem; font-weight: 700;
}
.timeline-step-pill {
    display: inline-flex; align-items: center; justify-content: center;
    padding: .3rem .625rem; border-radius: 999px;
    background: rgba(59,130,246,.08); color: var(--accent-soft);
    font-size: .625rem; font-weight: 700; letter-spacing: .1em; text-transform: uppercase;
}

/* QUOTE CARD */
.quote-card { position: relative; border-radius: var(--radius-xl); padding: 1.5rem; background: var(--bg-raised); border: 1px solid var(--border-subtle); transition: border-color .25s,box-shadow .25s; }
.quote-card:hover { border-color: rgba(59,130,246,.25); }

/* TOOL CARD */
.tool-card { position: relative; display: flex; flex-direction: column; gap: .875rem; padding: 1.375rem; background: var(--bg-raised); border: 1px solid var(--border-subtle); border-radius: var(--radius-xl); text-decoration: none; color: inherit; overflow: hidden; transition: border-color .25s,box-shadow .25s,transform .25s; }
.tool-card:hover { border-color: rgba(59,130,246,.3); transform: translateY(-2px); }
.tool-card .tool-title { font-family: var(--font-display); font-weight: 700; font-size: 1rem; color: var(--text-primary); }
.tool-card .tool-desc  { font-size: .875rem; color: var(--text-secondary); line-height: 1.6; }
.tool-card .tool-stat  { font-family: var(--font-sans); font-size: .625rem; font-weight: 700; text-transform: uppercase; letter-spacing: .1em; color: var(--text-secondary); background: rgba(255,255,255,.06); border: 1px solid var(--border-subtle); border-radius: var(--radius-full); padding: 2px 8px; white-space: nowrap; flex-shrink: 0; align-self: flex-start; }
.tool-card .tool-cta   { margin-top: auto; font-size: .875rem; font-weight: 600; color: var(--text-secondary); display: inline-flex; align-items: center; gap: .375rem; transition: color .2s; }
.tool-card:hover .tool-cta { color: var(--accent); }

/* ARTICLE CARDS */
.article-card { display: flex; flex-direction: column; gap: .875rem; text-decoration: none; color: inherit; transition: transform .25s; }
.article-card:hover { transform: translateY(-2px); }
.article-card .article-image { aspect-ratio: 16/10; overflow: hidden; border-radius: var(--radius-lg); background: var(--bg-overlay); }
.article-card .article-image img { width: 100%; height: 100%; object-fit: cover; transition: transform .5s; }
.article-card:hover .article-image img { transform: scale(1.04); }
.article-card.featured .article-image { aspect-ratio: 16/9; }
.article-card .article-meta { display: flex; align-items: center; gap: .625rem; font-size: .75rem; color: var(--text-muted); font-weight: 500; }
.article-card .article-cat { display: inline-flex; padding: .2rem .5rem; border-radius: var(--radius-full); background: rgba(59,130,246,.08); color: var(--accent-soft); font-weight: 700; font-size: .6rem; text-transform: uppercase; letter-spacing: .1em; }
.article-card .article-title { font-family: var(--font-display); font-weight: 700; line-height: 1.25; color: var(--text-primary); font-size: 1.125rem; letter-spacing: -0.015em; transition: color .2s; }
.article-card:hover .article-title { color: var(--accent-soft); }
.article-card.featured .article-title { font-size: clamp(1.25rem,2.5vw,1.75rem); }
.article-card .article-excerpt { font-size: .9375rem; line-height: 1.6; color: var(--text-secondary); }

/* ARTICLE LIST (text-only right column) */
.article-list-item { display: flex; flex-direction: column; padding: .875rem 0; text-decoration: none; color: inherit; border-color: var(--border-subtle); transition: opacity .2s; }
.article-list-item:first-child { padding-top: 0; }
.article-list-item:last-child  { padding-bottom: 0; }
.article-list-item:hover { opacity: .8; }
.article-list-item:hover .article-list-title { color: var(--accent-soft); }
.article-list-item .article-cat { display: inline-flex; padding: .15rem .45rem; border-radius: var(--radius-full); background: rgba(59,130,246,.08); color: var(--accent-soft); font-family: var(--font-sans); font-weight: 700; font-size: .6rem; text-transform: uppercase; letter-spacing: .1em; }
.article-list-title { font-family: var(--font-display); font-weight: 700; font-size: .9375rem; line-height: 1.35; color: var(--text-primary); letter-spacing: -0.01em; transition: color .2s; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; margin-bottom: .2rem; }
.article-list-meta { font-family: var(--font-sans); font-size: .75rem; color: var(--text-muted); }

/* SECONDARY ARTICLE */
.article-card-secondary { display: flex; flex-direction: column; gap: .75rem; text-decoration: none; color: inherit; border-radius: var(--radius-lg); overflow: hidden; background: var(--bg-raised); border: 1px solid var(--border-subtle); transition: border-color .25s,box-shadow .25s; }
.article-card-secondary:hover { border-color: var(--border-medium); box-shadow: var(--shadow-blue); }
.article-card-secondary .article-body { padding: .875rem 1rem 1rem; }
.article-card-secondary .article-title { font-family: var(--font-display); font-weight: 700; font-size: .9375rem; line-height: 1.4; color: var(--text-primary); letter-spacing: -0.01em; margin: .25rem 0 .375rem; }

/* ARTICLE ROW */
.article-card-row { display: grid; grid-template-columns: 120px 1fr; gap: .875rem; align-items: start; text-decoration: none; color: inherit; transition: opacity .2s; }
.article-card-row:hover { opacity: .8; }
.article-card-row .article-image { aspect-ratio: 4/3; overflow: hidden; border-radius: var(--radius-md); background: var(--bg-overlay); }
.article-card-row .article-image img { width: 100%; height: 100%; object-fit: cover; }
.article-card-row .article-title { font-family: var(--font-display); font-weight: 700; line-height: 1.3; color: var(--text-primary); font-size: .9375rem; letter-spacing: -0.01em; margin: .25rem 0 .35rem; }
.article-card-row .article-row-excerpt { font-size: .8125rem; line-height: 1.55; color: var(--text-secondary); }

/* STAT CLUSTER */
.stat-cluster { display: flex; flex-wrap: wrap; gap: 1.75rem; align-items: flex-start; }
.stat-cluster .stat-item { min-width: 0; }
.stat-cluster .stat-value { font-family: var(--font-display); font-weight: 800; font-size: clamp(1.5rem,2.5vw,2rem); line-height: 1; color: var(--text-primary); letter-spacing: -0.02em; }
.stat-cluster .stat-label { font-size: .8125rem; color: var(--text-secondary); margin-top: .2rem; font-weight: 500; }
.stat-item { display: flex; flex-direction: column; gap: .2rem; }
.stat-value { font-family: var(--font-display); font-size: clamp(1.5rem,2.5vw,2rem); font-weight: 800; line-height: 1; color: var(--text-primary); letter-spacing: -0.03em; }
.stat-value.on-dark { color: var(--text-primary); }
.stat-label { font-size: .8125rem; font-weight: 500; color: var(--text-secondary); line-height: 1.4; max-width: 12ch; }
.stat-label.on-dark { color: var(--text-secondary); }

/* LOGO ROW */
.logo-row { display: grid; grid-template-columns: repeat(auto-fit,minmax(110px,1fr)); gap: .75rem; align-items: center; }
.logo-mono { display: flex; align-items: center; justify-content: center; padding: .75rem .875rem; border-radius: var(--radius-lg); border: 1px solid var(--border-subtle); background: var(--bg-raised); transition: border-color .2s,box-shadow .2s; }
.logo-mono:hover { border-color: rgba(59,130,246,.3); box-shadow: 0 0 0 1px rgba(59,130,246,.15); }
.logo-mono span { font-family: var(--font-sans); font-size: .6875rem; font-weight: 700; letter-spacing: .06em; color: var(--text-secondary); text-align: center; line-height: 1.3; text-transform: uppercase; }

/* CERT BADGE */
.cert-badge { display: inline-flex; align-items: center; gap: .5rem; padding: .4rem .875rem; border-radius: 999px; border: 1px solid var(--border-medium); background: var(--bg-raised); font-size: .75rem; font-weight: 600; color: var(--text-secondary); letter-spacing: .03em; }
.cert-badge i { color: var(--accent); }

/* TRUST BADGE */
.trust-badge { display: inline-flex; align-items: center; gap: .5rem; padding: .375rem .875rem; border-radius: var(--radius-full); border: 1px solid var(--border-medium); background: rgba(255,255,255,.06); color: var(--text-secondary); font-size: .75rem; font-weight: 600; }

/* FORM */
.form-input-dark { flex: 1; height: 48px; padding: 0 1rem; border-radius: var(--radius-lg); background: rgba(255,255,255,.06); border: 1px solid var(--border-medium); color: var(--text-primary); font-size: .9375rem; font-family: inherit; transition: border-color .2s,background .2s; }
.form-input-dark::placeholder { color: var(--text-muted); }
.form-input-dark:focus { outline: none; border-color: var(--accent); background: rgba(255,255,255,.09); box-shadow: 0 0 0 3px rgba(59,130,246,.18); }
.form-row-v2 { display: flex; flex-wrap: wrap; gap: .625rem; align-items: stretch; }
.form-row-v2 .form-input-dark { flex: 1; min-width: 200px; }
.form-row-v2 .btn { height: 48px; padding-inline: 1.375rem; flex-shrink: 0; }
@media (max-width: 640px) {
    .form-row-v2 { flex-direction: column; }
    .form-row-v2 .btn { width: 100%; }
    .form-row-v2 .form-input-dark { min-width: 0; }
}

/* BUTTONS */
.btn { display: inline-flex; align-items: center; justify-content: center; gap: .5rem; padding: .625rem 1.5rem; font-size: .9375rem; font-weight: 600; border-radius: var(--radius-lg); transition: all .2s; cursor: pointer; border: 1px solid transparent; text-decoration: none; line-height: 1.4; white-space: nowrap; font-family: var(--font-sans); }
.btn i,.btn .fas,.btn .fab,.btn .far,.btn .fa-brands,.btn .fa-solid { display: inline-grid; place-items: center; width: 1em; height: 1em; line-height: 1; flex-shrink: 0; }
.btn-primary { background: var(--accent); color: #fff; box-shadow: 0 2px 8px rgba(59,130,246,.35); }
.btn-primary:hover { background: var(--accent-dark); box-shadow: 0 4px 16px rgba(59,130,246,.45); transform: translateY(-1px); }
.btn-secondary { background: var(--accent); color: #fff; }
.btn-secondary:hover { background: var(--accent-dark); transform: translateY(-1px); }
.btn-ghost { background: rgba(255,255,255,.08); color: var(--text-primary); border: 1px solid var(--border-medium); backdrop-filter: blur(4px); }
.btn-ghost:hover { background: rgba(255,255,255,.14); border-color: var(--border-strong); }
.btn-ghost-on-dark { background: rgba(255,255,255,.08); color: var(--text-primary); border: 1px solid var(--border-medium); }
.btn-ghost-on-dark:hover { background: rgba(255,255,255,.14); border-color: var(--border-strong); }
.btn-outline-primary { background: transparent; color: var(--text-secondary); border: 1px solid var(--border-medium); }
.btn-outline-primary:hover { border-color: rgba(59,130,246,.5); color: var(--accent); background: rgba(59,130,246,.06); }
.btn-success { background: var(--color-success); color: #fff; }
.btn-success:hover { background: #16a34a; transform: translateY(-1px); }
.btn-gold { background: var(--accent); color: #fff; font-weight: 700; box-shadow: 0 4px 16px rgba(59,130,246,.35); }
.btn-gold:hover { background: var(--accent-dark); box-shadow: 0 8px 24px rgba(59,130,246,.45); transform: translateY(-1px); }
.btn-lg { padding: .875rem 2rem; font-size: 1rem; }
.btn-sm { padding: .4375rem 1.125rem; font-size: .8125rem; min-height: 40px; }

/* PILL / CHIP */
.pill,.chip { display: inline-flex; align-items: center; gap: .375rem; font-size: .6875rem; font-weight: 700; text-transform: uppercase; letter-spacing: .1em; padding: .3rem .75rem; border-radius: var(--radius-full); border: 1px solid var(--border-subtle); background: var(--bg-raised); color: var(--text-secondary); text-decoration: none; transition: all .2s; }
.pill-brand,.pill-neutral { background: rgba(59,130,246,.07); border-color: rgba(59,130,246,.15); color: var(--accent-soft); }
.chip:hover { background: var(--bg-overlay); border-color: var(--border-medium); color: var(--text-primary); }
.chip.active { background: rgba(59,130,246,.12); border-color: rgba(59,130,246,.35); color: #93c5fd; }

/* LINK */
.link-primary { color: var(--text-secondary); font-weight: 600; text-decoration: none; transition: color .2s; position: relative; }
.link-primary:hover { color: var(--accent); }
.link-primary::after { content: ''; position: absolute; bottom: -2px; left: 0; width: 0; height: 1.5px; background: var(--accent); transition: width .3s; }
.link-primary:hover::after { width: 100%; }

/* DIVIDER */
.editorial-divider { display: flex; align-items: center; gap: 1rem; margin: 1.5rem 0; }
.editorial-divider::before,.editorial-divider::after { content: ''; flex: 1; height: 1px; background: var(--border-subtle); }
.editorial-divider span { font-size: .6875rem; font-weight: 700; text-transform: uppercase; letter-spacing: .12em; color: var(--text-muted); white-space: nowrap; }

/* MAGAZINE IMG */
.magazine-img { border-radius: var(--radius-xl); overflow: hidden; position: relative; }
.magazine-img img { width: 100%; height: 100%; object-fit: cover; transition: transform .5s; }
.magazine-img:hover img { transform: scale(1.03); }
.magazine-img-caption { position: absolute; bottom: 0; left: 0; right: 0; background: linear-gradient(to top,rgba(0,0,0,.7),transparent); padding: 2rem 1.25rem 1rem; color: #fff; }

/* BADGE FEATURED */
.badge-featured { display: inline-flex; align-items: center; font-size: .625rem; font-weight: 700; text-transform: uppercase; letter-spacing: .06em; padding: .25rem .75rem; border-radius: var(--radius-full); background: rgba(59,130,246,.08); color: var(--accent-soft); border: 1px solid rgba(59,130,246,.18); }

/* FAQ */
.faq-item { border: 1px solid var(--border-subtle); border-radius: var(--radius-lg); overflow: hidden; transition: all .25s; background: var(--bg-raised); }
.faq-item:hover { border-color: var(--border-medium); }
.faq-item.active,.faq-item[open] { border-color: rgba(59,130,246,.35); }
.faq-toggle { width: 100%; display: flex; align-items: center; justify-content: space-between; gap: 1rem; padding: 1.125rem 1.375rem; background: none; border: none; cursor: pointer; text-align: left; font-size: .9375rem; font-weight: 600; color: var(--text-primary); line-height: 1.4; transition: background .2s; list-style: none; font-family: var(--font-sans); }
.faq-toggle::-webkit-details-marker { display: none; }
.faq-toggle:hover { background: var(--bg-overlay); }
.faq-toggle i { flex-shrink: 0; font-size: .75rem; color: var(--text-muted); transition: transform .3s,color .2s; }
.faq-item.active .faq-toggle i,.faq-item[open] .faq-toggle i { transform: rotate(180deg); color: var(--accent); }
.faq-content { max-height: 0; overflow: hidden; transition: max-height .35s ease; }
.faq-item.active .faq-content,.faq-item[open] .faq-content { max-height: 600px; }
.faq-content-inner { padding: 0 1.375rem 1.125rem; color: var(--text-secondary); line-height: 1.75; font-size: .9375rem; }

/* NAVBAR */
.app-navbar { background: rgba(10,15,30,.85); border-bottom: 1px solid var(--border-subtle); backdrop-filter: blur(16px); -webkit-backdrop-filter: blur(16px); }
.app-navbar-inner { min-height: 64px; display: flex; align-items: center; justify-content: space-between; gap: 1rem; }
.brand-mark { display: inline-flex; align-items: center; gap: .6rem; font-size: 1.5rem; font-weight: 900; letter-spacing: -0.04em; color: var(--text-primary); text-decoration: none; font-family: var(--font-sans); }
.brand-mark i { font-size: 1.1rem; color: var(--accent); }
.site-footer .brand-mark { color: var(--text-primary); }
.site-footer .brand-mark i { color: var(--accent); }
.brand-mark span { font-size: clamp(1.2rem,2vw,1.4rem); font-family: var(--font-sans); font-weight: 900; }
.nav-link { font-family: var(--font-sans); font-size: .875rem; font-weight: 500; letter-spacing: .01em; color: var(--text-secondary); text-decoration: none; transition: color .2s,background .2s; padding: .45rem .75rem; border-radius: var(--radius-md); position: relative; }
.nav-link:hover { color: var(--text-primary); background: rgba(255,255,255,.06); }
.nav-link.active { color: var(--accent); font-weight: 600; }
a.nav-link::after { content: ''; position: absolute; bottom: 2px; left: 50%; transform: translateX(-50%); width: 0; height: 2px; background: var(--accent); border-radius: 1px; transition: width .3s; }
a.nav-link:hover::after,a.nav-link.active::after { width: 60%; }
.nav-control-btn { min-height: 36px; min-width: 36px; display: inline-flex; align-items: center; justify-content: center; gap: .45rem; padding: .4rem .7rem; border: 1px solid var(--border-medium); border-radius: var(--radius-lg); background: rgba(255,255,255,.05); color: var(--text-secondary); font-size: .8125rem; font-weight: 600; transition: all .2s; }
.nav-control-btn:hover { border-color: var(--border-strong); color: var(--text-primary); background: rgba(255,255,255,.1); }
.nav-dropdown { position: absolute; right: 0; top: calc(100% + .5rem); min-width: 220px; padding: .5rem; border-radius: var(--radius-lg); border: 1px solid var(--border-medium); background: var(--bg-overlay); box-shadow: var(--shadow-lg); }
.nav-dropdown-item { display: flex; align-items: center; gap: .6rem; padding: .55rem .65rem; border-radius: var(--radius-md); text-decoration: none; color: var(--text-secondary); font-size: .875rem; font-weight: 500; }
.nav-dropdown-item:hover,.nav-dropdown-item.active { background: rgba(255,255,255,.07); color: var(--text-primary); }
.nav-menu-feature { border: 1px solid transparent; }
.nav-menu-feature:hover,.nav-menu-feature:focus-visible { background: var(--bg-overlay); border-color: var(--border-subtle); }
#mobileMenu .mobile-menu-backdrop.active { opacity: 1 !important; }
#mobileMenu .mobile-menu-panel.active { transform: translateX(0) !important; }
#mobileMenu .mobile-menu-panel > div { -webkit-overflow-scrolling: touch; }
#servicesMegaMenu::before { content: ''; position: absolute; top: -8px; left: 0; right: 0; height: 8px; }

/* FOOTER */
.site-footer { background: var(--bg-base); border-top: 1px solid var(--border-subtle); color: var(--text-secondary); }
.footer-title { font-size: .6875rem; font-weight: 700; text-transform: uppercase; letter-spacing: .1em; color: var(--text-secondary); margin-bottom: .75rem; }
.footer-note { color: var(--text-secondary); max-width: 34ch; line-height: 1.7; font-size: .9375rem; }
.footer-list { list-style: none; margin: 0; padding: 0; display: grid; gap: .4rem; }
.footer-list a,.footer-list span { color: var(--text-secondary); text-decoration: none; font-size: .9rem; transition: color .2s; }
.footer-list i,.footer-list .fas,.footer-list .fab,.footer-list .far { display: inline-grid; place-items: center; width: 1rem; height: 1rem; line-height: 1; vertical-align: middle; }
.footer-list a:hover,.footer-list a.active { color: var(--text-primary); }
.footer-social { width: 34px; height: 34px; border-radius: 999px; display: inline-flex; align-items: center; justify-content: center; border: 1px solid var(--border-medium); color: var(--text-secondary); text-decoration: none; transition: all .2s; font-size: .875rem; }
.footer-social i { line-height: 1; display: block; width: 1em; height: 1em; text-align: center; }
.footer-social:hover { color: var(--text-primary); border-color: var(--border-strong); background: rgba(255,255,255,.07); }
.footer-city-wrap { display: flex; flex-wrap: wrap; gap: .4rem; }
.footer-city-link { text-decoration: none; color: var(--text-muted); font-size: .75rem; border: 1px solid var(--border-subtle); border-radius: 999px; padding: .2rem .5rem; transition: all .2s; }
.footer-city-link:hover { color: var(--text-secondary); border-color: var(--border-medium); }

/* BG UTILITIES */
.bg-ink-gradient { background: var(--bg-base); color: var(--text-primary); }
.bg-ink-gradient-soft { background: var(--bg-raised); color: var(--text-primary); }
.gradient-hero { background: var(--bg-base); }
.gradient-primary { background: var(--bg-raised); }

/* GRID */
.grid-equal { align-items: stretch; }
.grid-equal > * { height: 100%; }

/* COUNTER */
.counter { font-variant-numeric: tabular-nums; }

/* MONOGRAM / CLIENT LOGOS */
.monogram { font-size: .875rem; font-weight: 800; letter-spacing: .05em; color: var(--text-secondary); line-height: 1; }
.client-logo-card { transition: all .25s; }

/* PROCESS GRID */
@media (min-width: 768px) and (max-width: 1023px) {
    .process-step-grid > li:nth-child(odd) { border-right: 1px solid var(--border-subtle); padding-right: 1.5rem; }
    .process-step-grid > li:nth-child(1),.process-step-grid > li:nth-child(2) { padding-bottom: 1.5rem; border-bottom: 1px solid var(--border-subtle); }
}

/* ACCESSIBILITY */
*:focus-visible { outline: 2px solid var(--accent); outline-offset: 3px; border-radius: 4px; }
:focus:not(:focus-visible) { outline: none; }
.skip-link { position: absolute; top: -100%; left: 50%; transform: translateX(-50%); z-index: 9999; padding: .75rem 1.5rem; background: var(--accent); color: #fff; border-radius: var(--radius-md); font-weight: 600; transition: top .2s; }
.skip-link:focus { top: 1rem; }

/* AOS POLYFILL */
[data-aos] { opacity: 1 !important; transform: none !important; transition: none !important; }

/* ANIMATIONS */
.animate-fade-in { opacity: 0; animation: fadeIn .6s ease forwards; }
@keyframes fadeIn { to { opacity: 1; } }
.delay-100 { animation-delay: .1s; }
.delay-200 { animation-delay: .2s; }
.delay-300 { animation-delay: .3s; }
.hover-lift { transition: transform .3s,box-shadow .3s; }
.hover-lift:hover { transform: translateY(-3px); box-shadow: var(--shadow-lg); }

/* SCROLL TO TOP */
#backToTop { position: fixed; right: 1.5rem; bottom: 2rem; z-index: 50; }
.fab-back-to-top { display: flex; align-items: center; justify-content: center; background: var(--bg-raised); color: var(--text-secondary); border: 1px solid var(--border-medium); border-radius: 50%; width: 42px; height: 42px; font-size: 1rem; cursor: pointer; box-shadow: var(--shadow-md); opacity: 0; visibility: hidden; transform: translateY(8px); transition: opacity .25s,visibility .25s,transform .25s,background .2s,color .2s; }
.fab-back-to-top.show { opacity: 1; visibility: visible; transform: translateY(0); }
.fab-back-to-top:hover { background: var(--accent); color: #fff; border-color: var(--accent); }
.fab-back-to-top i { line-height: 1; pointer-events: none; }
.btn i,.btn .fas,.btn .fab,.btn .far,a.inline-flex i,.fab-group a i { line-height: 1; display: inline-block; }
@media (max-width: 640px) { #backToTop { right: 1rem; bottom: 4.5rem; } .fab-back-to-top { width: 38px; height: 38px; font-size: .875rem; } }

/* MOBILE CTA */
#sticky-mobile-cta { backdrop-filter: blur(12px); background: rgba(10,15,30,.95) !important; }

/* COOKIE */
.cookie-banner { border-radius: var(--radius-xl) var(--radius-xl) 0 0; border-top: 1px solid var(--border-subtle); }

/* CONTENT PROSE */
.content-prose { color: var(--text-secondary); font-size: 1rem; line-height: 1.9; }
.content-prose > * + * { margin-top: 1rem; }
.content-prose h2,.content-prose h3,.content-prose h4 { color: var(--text-primary); line-height: 1.25; letter-spacing: -0.02em; margin-top: 2rem; margin-bottom: .75rem; }
.content-prose h2 { font-size: 1.5rem; font-weight: 800; }
.content-prose h3 { font-size: 1.25rem; font-weight: 700; }
.content-prose h4 { font-size: 1.05rem; font-weight: 700; }
.content-prose a { color: var(--accent); text-decoration: underline; text-underline-offset: 3px; }
.content-prose a:hover { color: #60a5fa; }
.content-prose ul,.content-prose ol { padding-left: 1.25rem; }
.content-prose li { margin: .35rem 0; }
.content-prose blockquote { margin: 1.5rem 0; padding: 1rem 1.25rem; border-left: 3px solid var(--accent); background: var(--bg-raised); color: var(--text-secondary); border-radius: var(--radius-lg); }
.content-prose img { max-width: 100%; height: auto; border-radius: var(--radius-lg); border: 1px solid var(--border-subtle); }
.content-prose hr { border: 0; border-top: 1px solid var(--border-subtle); margin: 2rem 0; }
.content-prose code { background: var(--bg-raised); padding: .1rem .35rem; border-radius: .35rem; color: #93c5fd; font-size: .875em; }
.content-prose pre { background: var(--bg-raised); color: var(--text-primary); padding: 1rem; border-radius: var(--radius-lg); overflow: auto; border: 1px solid var(--border-subtle); }
.content-prose table { width: 100%; border-collapse: collapse; }
.content-prose th,.content-prose td { border: 1px solid var(--border-subtle); padding: .6rem .75rem; vertical-align: top; }
.content-prose th { background: var(--bg-overlay); color: var(--text-primary); font-weight: 700; }

/* PAGINATION */
.pagination { display: flex; justify-content: center; }
.pagination .page-link,.pagination a,.pagination span { display: inline-flex; align-items: center; justify-content: center; min-width: 38px; min-height: 38px; padding: .35rem .65rem; border-radius: 999px; border: 1px solid var(--border-subtle); background: var(--bg-raised); color: var(--text-secondary); text-decoration: none; margin: 0 .2rem; }
.pagination .active span,.pagination .active .page-link { background: rgba(59,130,246,.12); border-color: rgba(59,130,246,.3); color: var(--accent-soft); font-weight: 700; }
.pagination a:hover { background: var(--bg-overlay); border-color: var(--border-medium); color: var(--text-primary); }

/* ALPINE CLOAK */
[x-cloak] { display: none !important; }

/* RESPONSIVE */
@media (max-width: 768px) {
    .section { padding: 2rem 0; }
    .section-v2 { padding-block: 2rem; }
    .section-v2-sm { padding-block: 1.25rem; }
    .btn-lg { padding: .75rem 1.5rem; font-size: .9375rem; }
    .premium-card,.card { padding: 1.125rem; }
    .tool-card { padding: 1.125rem; }
    .stat-cluster { gap: 1.25rem; }
    .logo-row { grid-template-columns: repeat(auto-fit,minmax(90px,1fr)); gap: .625rem; }
    h1 { font-size: 2.125rem; }
    h2 { font-size: 1.625rem; }
}

/* PRINT */
@media print {
    nav,footer,#sticky-mobile-cta,.btn,.cookie-banner { display: none !important; }
    .section { padding: 1rem 0; }
    body { font-size: 12pt; color: #000; background: #fff; }
}

/* SECTION ANCHOR */
section[id] { scroll-margin-top: 80px; }

/* REDUCE MOTION */
@media (prefers-reduced-motion: reduce) {
    *,*::before,*::after { animation-duration: .01ms !important; animation-iteration-count: 1 !important; transition-duration: .01ms !important; scroll-behavior: auto !important; }
}


} /* end @layer base */

/* ============================================================
   MICRO-INTERACTIONS & ENHANCED UX  — UNLAYERED
   All rules here are outside @layer, giving them higher cascade
   priority than Tailwind utilities for consistent behaviour.
   ============================================================ */

/* --- Scroll progress indicator --- */
#scroll-progress {
    position: fixed; top: 0; left: 0; z-index: 9999;
    height: 2px; width: 0;
    background: linear-gradient(90deg, var(--accent) 0%, var(--accent-soft) 100%);
    pointer-events: none;
    transition: width .1s linear;
}

/* --- Navbar: CSS-driven smooth transition --- */
.app-navbar { transition: background .35s ease, box-shadow .35s ease, border-color .35s ease; }
.app-navbar.is-scrolled {
    background: rgba(10,15,30,.97) !important;
    box-shadow: 0 4px 24px rgba(0,0,0,.45);
    border-bottom-color: rgba(59,130,246,.12);
}

/* --- Card hover: add shadow elevation + border (existing translateY kept) --- */
.card:hover         { box-shadow: var(--shadow-blue); }
.premium-card:hover { box-shadow: var(--shadow-blue); }
.quote-card:hover   { box-shadow: var(--shadow-blue); }
.tool-card:hover    { box-shadow: var(--shadow-blue); }

/* --- Editorial icon badge: spring-physics hover scale --- */
.editorial-icon-badge {
    transition: transform .3s cubic-bezier(0.34,1.56,0.64,1);
}
.card:hover .editorial-icon-badge,
a:hover > .editorial-icon-badge,
.subtle-link-card:hover .editorial-icon-badge { transform: scale(1.1); }

/* --- Arrow & chevron icons: slide right on interactive hover --- */
.fa-arrow-right, .fa-arrow-up-right, .fa-external-link-alt {
    display: inline-block;
    transition: transform .2s ease;
}
.btn:hover .fa-arrow-right,
.btn:hover .fa-arrow-up-right,
.link-primary:hover .fa-arrow-right,
.card:hover .fa-arrow-right,
.subtle-link-card:hover .fa-arrow-right,
.tool-card:hover .fa-arrow-right,
a:hover .fa-arrow-up-right { transform: translateX(3px); }

/* --- Buttons: active press-down + ghost/outline lift --- */
.btn:active { transform: translateY(1px) scale(.98) !important; box-shadow: none !important; }
.btn-ghost:hover,
.btn-ghost-on-dark:hover { transform: translateY(-1px); }
.btn-outline-primary:hover { transform: translateY(-1px); }

/* --- FAQ: smoother cubic-bezier easing --- */
.faq-content { transition: max-height .4s cubic-bezier(0.4,0,0.2,1) !important; }
.faq-toggle { transition: background .2s, color .2s; }

/* --- Focus-visible rings (keyboard / accessibility) --- */
:focus-visible {
    outline: 2px solid var(--accent);
    outline-offset: 3px;
    border-radius: 4px;
}
.btn:focus-visible   { outline-offset: 2px; }
a:focus-visible      { border-radius: 3px; }
input:focus-visible, select:focus-visible, textarea:focus-visible {
    outline: 2px solid var(--accent);
    outline-offset: 0;
    border-color: var(--accent) !important;
}

/* --- Brand mark: subtle opacity on hover --- */
.brand-mark { transition: opacity .2s; }
.brand-mark:hover { opacity: .82; }

/* --- Page section entrance animation --- */
@keyframes section-enter {
    from { opacity: 0; transform: translateY(10px); }
    to   { opacity: 1; transform: translateY(0); }
}
main > section:first-of-type {
    animation: section-enter .5s cubic-bezier(0.4,0,0.2,1) both;
}

/* --- Subtle link underline grow (for inline text links) --- */
.link-inline {
    text-decoration: underline;
    text-decoration-color: transparent;
    text-underline-offset: 3px;
    transition: text-decoration-color .2s;
    color: var(--accent-soft);
}
.link-inline:hover { text-decoration-color: var(--accent-soft); }

/* --- Cert badge hover --- */
.cert-badge {
    transition: border-color .2s, background .2s, transform .2s;
}
.cert-badge:hover {
    border-color: rgba(59,130,246,.35);
    background: rgba(59,130,246,.06);
    transform: translateY(-1px);
}

/* ============================================================
   DARK THEME LEGACY ADAPTER  — UNLAYERED (beats @layer utilities)
   Unlayered styles have higher cascade priority than ALL layered
   styles, so these override Tailwind's @layer utilities without
   needing excessive !important.
   
   @deprecated — This adapter converts V1 (light-themed) Tailwind
   class-based sections to dark theme. As V2 sections are now the
   standard, this block should be removed once all V1 sections
   (legacy/contact, legacy/services, legacy/navbar, etc.) are
   fully retired.
   ============================================================ */

/* --- Tailwind gray text → dark-theme equivalents --- */
body .text-gray-900, body .text-gray-800 { color: var(--text-primary) !important; }
body .text-gray-700                       { color: rgba(241,245,249,.88) !important; }
body .text-gray-600                       { color: var(--text-secondary) !important; }
body .text-gray-500, body .text-gray-400  { color: var(--text-muted) !important; }

/* --- Tailwind light surface classes → dark overlay --- */
body [class~="bg-white"]      { background-color: var(--bg-overlay) !important; }
body [class~="bg-red-50"],
body [class~="bg-orange-50"],
body [class~="bg-blue-50"],
body [class~="bg-green-50"],
body [class~="bg-green-800"],
body [class~="bg-green-900"],
body [class~="bg-yellow-50"],
body [class~="bg-gray-50"],
body [class~="bg-gray-100"]   { background-color: var(--bg-overlay) !important; }

/* --- Light border colours → subtle dark borders --- */
body [class~="border-gray-200"], body [class~="border-gray-300"] { border-color: var(--border-medium) !important; }
body [class~="hover:border-green-300"]:hover, body [class~="hover:border-green-400"]:hover { border-color: rgba(59,130,246,.4) !important; }

/* --- Semantic green/coloured text → adapt for dark bg --- */
body [class~="text-green-700"], body [class~="text-green-800"] { color: #4ade80 !important; }
body [class~="text-green-600"] { color: #86efac !important; }
body [class~="text-blue-600"], body [class~="text-blue-700"]   { color: var(--accent-soft) !important; }
body [class~="text-red-600"]   { color: #f87171 !important; }

/* --- --color-primary is dark navy — remap text to accent --- */
body [style*="color: var(--color-primary)"],
body [style*="color:var(--color-primary)"] { color: var(--accent-soft) !important; }
/* Also remap rgba(15,23,42,.06) icon bg that is nearly invisible on dark --- */
body [style*="rgba(15,23,42,.06)"] { background: rgba(59,130,246,.12) !important; }

/* --- Strip Tailwind light gradient fills inside cards --- */
body .from-red-50,
body .from-orange-50,
body .from-blue-50,
body .from-green-50  {
    --tw-gradient-from: var(--bg-overlay) !important;
    --tw-gradient-stops: var(--bg-overlay), var(--bg-overlay) !important;
    background-image: none !important;
    background-color: var(--bg-overlay) !important;
}

/* --- Override INLINE style gradient backgrounds in legacy partials ---
   [style*="linear-gradient"] matches elements with inline gradient.
   Only applied within .card context to avoid breaking hero section gradients. */
.card [style*="linear-gradient(135deg"],
.card [style*="linear-gradient(to "],
.premium-card [style*="linear-gradient(135deg"] {
    background: var(--bg-overlay) !important;
    background-image: none !important;
}

/* --- Coloured icon container badges → dark semantic tints --- */
body .bg-red-600   { background-color: rgba(239,68,68,.15) !important; color: #f87171 !important; }
body .bg-blue-600  { background-color: rgba(59,130,246,.15) !important; color: var(--accent-soft) !important; }
body .bg-green-600 { background-color: rgba(34,197,94,.15) !important; color: #4ade80 !important; }

/* --- Light border colours → subtle dark border --- */
body [class~="border-red-100"], body [class~="border-red-200"],
body [class~="border-orange-100"], body [class~="border-orange-200"] {
    border-color: var(--border-medium) !important;
}

/* --- Tailwind prose plugin → dark text on dark bg --- */
body .prose                { color: var(--text-secondary) !important; }
body .prose h1, body .prose h2, body .prose h3,
body .prose h4, body .prose h5, body .prose h6 { color: var(--text-primary) !important; }
body .prose strong         { color: var(--text-primary) !important; }
body .prose a              { color: var(--accent-soft) !important; }
body .prose code           { background: var(--bg-overlay) !important; color: #93c5fd !important; }
body .prose blockquote     {
    border-left-color: var(--accent) !important;
    background: var(--bg-raised) !important;
    color: var(--text-secondary) !important;
}

/* ============================================================
   EXCLUSIONS — restore light theme for .inquiry-page (service
   inquiry form) which is intentionally light-themed.
   ============================================================ */
.inquiry-page [class~="bg-white"],
.inquiry-page .bg-white        { background-color: #ffffff !important; }

.inquiry-page [class~="text-gray-900"],
.inquiry-page .text-gray-900   { color: #111827 !important; }

.inquiry-page [class~="text-gray-600"],
.inquiry-page .text-gray-600   { color: #475569 !important; }

.inquiry-page [class~="text-gray-500"],
.inquiry-page .text-gray-500   { color: #6b7280 !important; }

.inquiry-page [class~="border-gray-200"],
.inquiry-page .border-gray-200 { border-color: #e5e7eb !important; }

.inquiry-page [class~="bg-gray-50"],
.inquiry-page .bg-gray-50      { background-color: #f9fafb !important; }

.inquiry-page [class~="bg-primary-50"],
.inquiry-page .bg-primary-50   { background-color: #f0f4ff !important; }

.inquiry-page [class~="bg-primary-100"],
.inquiry-page .bg-primary-100  { background-color: #dbeafe !important; }

/* --- S1 fix: hover effects for social-icon, pdf-download-btn, print-btn --- */
.social-icon          { opacity: 1; transition: opacity 0.2s ease; }
.social-icon:hover    { opacity: 0.8; }

.pdf-download-btn:hover { background: rgba(14, 165, 233, 0.15) !important; }
.print-btn:hover        { color: var(--text-secondary) !important; }

</style>
