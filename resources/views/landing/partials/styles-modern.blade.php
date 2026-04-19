{{-- MAGAZINE EDITORIAL DESIGN SYSTEM --}}
{{-- Elegant, typographic, visual-first --}}
<style>
@layer base {

/* ======================================
   COLOR PALETTE - Editorial Magazine
   Sophisticated dark navy + warm accents
====================================== */
:root {
    --color-primary: #0f172a;
    --color-primary-dark: #020617;
    --color-primary-light: #1e3a5f;
    --color-secondary: #f97316;
    --color-accent: #0ea5e9;
    --color-success: #16a34a;
    --color-warning: #eab308;
    --apple-green: #22c55e;
    --color-primary-rgb: 15, 23, 42;

    --surface: #ffffff;
    --surface-primary: #ffffff;
    --surface-secondary: #f8fafc;
    --surface-warm: #faf8f5;
    --surface-cool: #f8fafc;
    --surface-dark: #0f172a;
    --text-primary: #0f172a;
    --text-secondary: #475569;
    --text-tertiary: #94a3b8;
    --text-inverse: #f8fafc;
    --border-light: #e2e8f0;
    --border-medium: #cbd5e1;
    --border-strong: #94a3b8;

    --radius-sm: 0.375rem;
    --radius-md: 0.5rem;
    --radius-lg: 0.75rem;
    --radius-xl: 1rem;
    --radius-2xl: 1.25rem;
    --radius-full: 9999px;

    --shadow-sm: 0 1px 2px rgba(0,0,0,.05);
    --shadow-md: 0 4px 12px rgba(0,0,0,.06);
    --shadow-lg: 0 8px 30px rgba(0,0,0,.08);
    --shadow-xl: 0 16px 48px rgba(0,0,0,.1);
    --shadow-ring: 0 0 0 4px rgba(14,165,233,.12);

    /* Legacy compat */
    --primary: #0f172a;
    --primary-dark: #020617;
    --primary-light: rgba(15,23,42,.06);
    --secondary: #f97316;
    --gray-900: #0f172a;
}

/* ======================================
   TYPOGRAPHY - Editorial Magazine
   Large serifs for headlines, clean sans for body
====================================== */
body {
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
    background: var(--surface);
    color: var(--text-primary);
    font-size: 1rem;
    line-height: 1.7;
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
    overflow-x: hidden;
    min-height: 100vh;
}

h1, h2, h3, h4, h5, h6 {
    font-weight: 700;
    line-height: 1.2;
    letter-spacing: -0.025em;
}

h1 { font-size: clamp(2.5rem, 5vw, 4rem); font-weight: 800; line-height: 1.1; letter-spacing: -0.035em; }
h2 { font-size: clamp(2rem, 3.5vw, 2.75rem); }
h3 { font-size: clamp(1.25rem, 2vw, 1.5rem); }

p { line-height: 1.75; margin-bottom: 1rem; }

/* ======================================
   LAYOUT
====================================== */
.container, .container-wide {
    width: 100%;
    margin: 0 auto;
    padding-inline: clamp(20px, 5vw, 48px);
}
.container { max-width: 1080px; }
.container-wide { max-width: 1320px; }

.section { padding: clamp(3rem, 8vw, 5.5rem) 0; }
.section-sm { padding: clamp(2rem, 5vw, 3.5rem) 0; }

/* ======================================
   SECTION HEADERS - Magazine Style
====================================== */
.section-badge, .section-label {
    display: inline-flex;
    align-items: center;
    gap: .375rem;
    font-size: .6875rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .12em;
    color: var(--text-tertiary);
    padding: .375rem 1rem;
    background: var(--surface-cool);
    border-radius: var(--radius-full);
    border: 1px solid var(--border-light);
    margin-bottom: 1rem;
}

.section-title {
    font-size: clamp(2rem, 3.5vw, 2.75rem);
    font-weight: 700;
    line-height: 1.15;
    letter-spacing: -0.03em;
    color: inherit;
    margin-bottom: 1rem;
}

.section-description {
    font-size: 1.125rem;
    line-height: 1.75;
    color: inherit;
    max-width: 48ch;
    margin: 0 auto;
}

/* ======================================
   GRADIENT BACKGROUNDS
====================================== */
.gradient-hero {
    background: linear-gradient(160deg, #0f172a 0%, #1e3a5f 40%, #0c4a6e 100%);
}

.gradient-primary {
    background: linear-gradient(135deg, #0f172a 0%, #1e3a5f 100%);
}

/* ======================================
   BUTTONS - Clean, minimal, editorial
====================================== */
.btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: .5rem;
    padding: .75rem 1.75rem;
    font-size: .9375rem;
    font-weight: 600;
    border-radius: var(--radius-lg);
    transition: all .25s ease;
    cursor: pointer;
    border: none;
    text-decoration: none;
    line-height: 1.4;
    white-space: nowrap;
}

.btn-primary {
    background: var(--color-primary);
    color: #fff;
    box-shadow: var(--shadow-sm);
}
.btn-primary:hover {
    background: var(--color-primary-light);
    box-shadow: var(--shadow-md);
    transform: translateY(-1px);
}

.btn-secondary {
    background: var(--color-secondary);
    color: #fff;
}
.btn-secondary:hover {
    background: #ea580c;
    transform: translateY(-1px);
}

.btn-ghost {
    background: rgba(255,255,255,.1);
    color: #fff;
    border: 1px solid rgba(255,255,255,.25);
    backdrop-filter: blur(4px);
}
.btn-ghost:hover {
    background: rgba(255,255,255,.2);
    border-color: rgba(255,255,255,.4);
}

.btn-outline-primary {
    background: transparent;
    color: var(--text-primary);
    border: 1.5px solid var(--border-medium);
}
.btn-outline-primary:hover {
    border-color: var(--text-primary);
    background: var(--surface-cool);
}

.btn-success {
    background: var(--color-success);
    color: #fff;
}
.btn-success:hover { background: #15803d; }

.btn-lg { padding: .9375rem 2.25rem; font-size: 1rem; }
.btn-sm { padding: .5rem 1.25rem; font-size: .8125rem; }

/* ======================================
   CARDS - Clean editorial card
====================================== */
.card {
    background-color: var(--surface);
    border: 1px solid var(--border-light);
    border-radius: var(--radius-xl);
    padding: 1.75rem;
    transition: all .3s ease;
    position: relative;
}

.card:hover {
    border-color: var(--border-medium);
    box-shadow: var(--shadow-lg);
    transform: translateY(-2px);
}

.card-title {
    transition: color .2s ease;
}
.card:hover .card-title {
    color: var(--color-primary-light);
}

.magazine-card {
    background-color: var(--surface);
    border: 1px solid var(--border-light);
    border-radius: var(--radius-xl);
    transition: all .3s ease;
}
.magazine-card:hover {
    border-color: var(--border-medium);
    box-shadow: var(--shadow-lg);
    transform: translateY(-2px);
}

/* ======================================
   MAGAZINE IMAGE STYLES
====================================== */
.magazine-img {
    border-radius: var(--radius-xl);
    overflow: hidden;
    position: relative;
}

.magazine-img img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform .6s ease;
}

.magazine-img:hover img {
    transform: scale(1.03);
}

.magazine-img-caption {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    background: linear-gradient(to top, rgba(0,0,0,.65) 0%, transparent 100%);
    padding: 2rem 1.5rem 1rem;
    color: #fff;
}

/* ======================================
   EDITORIAL DIVIDERS
====================================== */
.editorial-divider {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin: 2rem 0;
}
.editorial-divider::before,
.editorial-divider::after {
    content: '';
    flex: 1;
    height: 1px;
    background: var(--border-light);
}
.editorial-divider span {
    font-size: .6875rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .12em;
    color: var(--text-tertiary);
    white-space: nowrap;
}

/* ======================================
   FEATURE BADGES
====================================== */
.badge-featured {
    display: inline-flex;
    align-items: center;
    font-size: .625rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .06em;
    padding: .25rem .75rem;
    border-radius: var(--radius-full);
    background: #fef3c7;
    color: #92400e;
    border: 1px solid #fde68a;
}

/* ======================================
   LINKS
====================================== */
.link-primary {
    color: var(--text-primary);
    font-weight: 600;
    text-decoration: none;
    transition: all .2s ease;
    position: relative;
}
.link-primary:hover {
    color: var(--color-accent);
}
.link-primary::after {
    content: '';
    position: absolute;
    bottom: -2px;
    left: 0;
    width: 0;
    height: 1.5px;
    background: var(--color-accent);
    transition: width .3s ease;
}
.link-primary:hover::after {
    width: 100%;
}

/* ======================================
   FAQ ACCORDION
====================================== */
.faq-item {
    border: 1px solid var(--border-light);
    border-radius: var(--radius-lg);
    overflow: hidden;
    transition: all .3s ease;
    background: var(--surface);
}
.faq-item:hover {
    border-color: var(--border-medium);
}
.faq-item.active,
.faq-item[open] {
    border-color: var(--color-accent);
    box-shadow: 0 0 0 1px var(--color-accent);
}

.faq-toggle {
    width: 100%;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
    padding: 1.25rem 1.5rem;
    background: none;
    border: none;
    cursor: pointer;
    text-align: left;
    font-size: 1rem;
    font-weight: 600;
    color: var(--text-primary);
    line-height: 1.4;
    transition: background .2s;
}
.faq-toggle { list-style: none; }
.faq-toggle::-webkit-details-marker { display: none; }
.faq-toggle:hover { background: var(--surface-cool); }

.faq-toggle i {
    flex-shrink: 0;
    font-size: .75rem;
    color: var(--text-tertiary);
    transition: transform .3s ease;
}
.faq-item.active .faq-toggle i,
.faq-item[open] .faq-toggle i { transform: rotate(180deg); color: var(--color-accent); }

.faq-content {
    max-height: 0;
    overflow: hidden;
    transition: max-height .35s ease, padding .35s ease;
}
.faq-item.active .faq-content,
.faq-item[open] .faq-content {
    max-height: 500px;
}
.faq-content-inner {
    padding: 0 1.5rem 1.25rem;
    color: var(--text-secondary);
    line-height: 1.75;
    font-size: .9375rem;
}

/* ======================================
   STATS/COUNTER
====================================== */
.counter { font-variant-numeric: tabular-nums; }

/* ======================================
   NAVBAR / MENUS
====================================== */
.app-navbar {
    background: rgba(255,255,255,.9);
    border-bottom: 1px solid var(--border-light);
    backdrop-filter: blur(12px);
}
.app-navbar-inner {
    min-height: 72px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
}
.brand-mark {
    display: inline-flex;
    align-items: center;
    gap: .65rem;
    font-size: 1.75rem;
    font-weight: 900;
    letter-spacing: -0.04em;
    color: var(--text-primary);
    text-decoration: none;
}
.brand-mark i {
    font-size: 1.15rem;
    color: var(--color-primary);
}
.brand-mark span {
    font-size: clamp(1.55rem, 2vw, 1.85rem);
}
.nav-link {
    font-size: .875rem;
    font-weight: 500;
    letter-spacing: .01em;
    color: var(--text-secondary);
    text-decoration: none;
    transition: color .25s ease, background .25s ease;
    padding: .5rem .875rem;
    border-radius: .375rem;
    position: relative;
}
.nav-link:hover {
    color: var(--color-secondary);
    background: rgba(249, 115, 22, .06);
}
.nav-link.active {
    color: var(--color-secondary);
    font-weight: 600;
}
.nav-link::after {
    content: '';
    position: absolute;
    bottom: 2px;
    left: 50%;
    transform: translateX(-50%);
    width: 0;
    height: 2px;
    background: var(--color-secondary);
    border-radius: 1px;
    transition: width .3s ease;
}
.nav-link:hover::after, .nav-link.active::after {
    width: 60%;
}
.nav-control-btn {
    min-height: 40px;
    min-width: 40px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: .45rem;
    padding: .5rem .75rem;
    border: 1px solid var(--border-light);
    border-radius: var(--radius-lg);
    background: #fff;
    color: var(--text-secondary);
    font-size: .8125rem;
    font-weight: 600;
    transition: all .2s ease;
}
.nav-control-btn:hover {
    border-color: var(--border-medium);
    color: var(--text-primary);
    background: var(--surface-cool);
}
.nav-dropdown {
    position: absolute;
    right: 0;
    top: calc(100% + .45rem);
    min-width: 220px;
    padding: .5rem;
    border-radius: var(--radius-lg);
    border: 1px solid var(--border-light);
    background: #fff;
    box-shadow: var(--shadow-lg);
}
.nav-dropdown-item {
    display: flex;
    align-items: center;
    gap: .6rem;
    padding: .55rem .65rem;
    border-radius: .5rem;
    text-decoration: none;
    color: var(--text-secondary);
    font-size: .875rem;
    font-weight: 500;
}
.nav-dropdown-item:hover,
.nav-dropdown-item.active {
    background: var(--surface-cool);
    color: var(--text-primary);
}

/* Mobile menu animation states */
#mobileMenu .mobile-menu-backdrop.active { opacity: 1 !important; }
#mobileMenu .mobile-menu-panel.active { transform: translateX(0) !important; }
#mobileMenu .mobile-menu-panel > div { -webkit-overflow-scrolling: touch; }

/* ======================================
   FOOTER
====================================== */
.site-footer {
    background: linear-gradient(135deg, #0b1223 0%, #0f172a 55%, #121d34 100%);
    color: #e2e8f0;
}
.footer-title {
    font-size: .75rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .09em;
    color: rgba(255,255,255,.65);
    margin-bottom: .8rem;
}
.footer-note {
    color: rgba(255,255,255,.72);
    max-width: 36ch;
    line-height: 1.7;
}
.footer-list {
    list-style: none;
    margin: 0;
    padding: 0;
    display: grid;
    gap: .45rem;
}
.footer-list a, .footer-list span {
    color: rgba(255,255,255,.72);
    text-decoration: none;
    font-size: .92rem;
    transition: color .2s ease;
}
.footer-list a:hover, .footer-list a.active {
    color: #fff;
}
.footer-social {
    width: 36px;
    height: 36px;
    border-radius: 999px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border: 1px solid rgba(255,255,255,.18);
    color: rgba(255,255,255,.8);
    text-decoration: none;
    transition: all .2s ease;
}
.footer-social:hover {
    color: #fff;
    border-color: rgba(255,255,255,.35);
    background: rgba(255,255,255,.08);
}
.footer-city-wrap {
    display: flex;
    flex-wrap: wrap;
    gap: .45rem;
}
.footer-city-link {
    text-decoration: none;
    color: rgba(255,255,255,.72);
    font-size: .75rem;
    border: 1px solid rgba(255,255,255,.12);
    border-radius: 999px;
    padding: .25rem .55rem;
    transition: all .2s ease;
}
.footer-city-link:hover {
    color: #fff;
    border-color: rgba(255,255,255,.35);
}

/* ======================================
   ACCESSIBILITY & FOCUS
====================================== */
:focus-visible {
    outline: 2px solid var(--color-accent);
    outline-offset: 2px;
    border-radius: var(--radius-sm);
}
a:focus-visible, button:focus-visible {
    outline: 2px solid var(--color-accent);
    outline-offset: 2px;
}
:focus:not(:focus-visible) { outline: none; }

.skip-link {
    position: absolute;
    top: -100%;
    left: 50%;
    transform: translateX(-50%);
    z-index: 9999;
    padding: .75rem 1.5rem;
    background: var(--color-primary);
    color: #fff;
    border-radius: var(--radius-md);
    font-weight: 600;
    transition: top .2s;
}
.skip-link:focus { top: 1rem; }

/* ======================================
   ANIMATIONS
====================================== */
.animate-fade-in {
    opacity: 0;
    animation: fadeIn .6s ease forwards;
}
@keyframes fadeIn { to { opacity: 1; } }
.delay-100 { animation-delay: .1s; }
.delay-200 { animation-delay: .2s; }
.delay-300 { animation-delay: .3s; }

/* Smooth hover transitions */
.hover-lift { transition: transform .3s ease, box-shadow .3s ease; }
.hover-lift:hover { transform: translateY(-3px); box-shadow: var(--shadow-lg); }

/* ======================================
   RESPONSIVE
====================================== */
@media (max-width: 768px) {
    .section { padding: 3rem 0; }
    .btn-lg { padding: .875rem 1.5rem; font-size: .9375rem; }
    .card { padding: 1.25rem; }
    h1 { font-size: 2.25rem; }
    h2 { font-size: 1.75rem; }
}

/* ======================================
   MOBILE CTA
====================================== */
#sticky-mobile-cta {
    backdrop-filter: blur(12px);
    background: rgba(255,255,255,.95) !important;
}

/* ======================================
   COOKIE CONSENT
====================================== */
.cookie-banner {
    border-radius: var(--radius-xl) var(--radius-xl) 0 0;
    border-top: 1px solid var(--border-light);
}

.content-prose {
    color: var(--text-secondary);
    font-size: 1rem;
    line-height: 1.9;
}
.content-prose > * + * { margin-top: 1rem; }
.content-prose h2, .content-prose h3, .content-prose h4 {
    color: var(--text-primary);
    line-height: 1.25;
    letter-spacing: -0.02em;
    margin-top: 2rem;
    margin-bottom: .75rem;
}
.content-prose h2 { font-size: 1.5rem; font-weight: 800; }
.content-prose h3 { font-size: 1.25rem; font-weight: 750; }
.content-prose h4 { font-size: 1.05rem; font-weight: 700; }
.content-prose a { color: var(--color-accent); text-decoration: underline; text-underline-offset: 3px; }
.content-prose a:hover { color: #0284c7; }
.content-prose ul, .content-prose ol { padding-left: 1.25rem; }
.content-prose li { margin: .35rem 0; }
.content-prose blockquote {
    margin: 1.5rem 0;
    padding: 1rem 1.25rem;
    border-left: 4px solid var(--color-accent);
    background: var(--surface-cool);
    color: var(--text-secondary);
    border-radius: var(--radius-lg);
}
.content-prose img { max-width: 100%; height: auto; border-radius: var(--radius-lg); border: 1px solid var(--border-light); }
.content-prose hr { border: 0; border-top: 1px solid var(--border-light); margin: 2rem 0; }
.content-prose code { background: rgba(15,23,42,.06); padding: .1rem .35rem; border-radius: .35rem; }
.content-prose pre { background: var(--surface-dark); color: var(--text-inverse); padding: 1rem; border-radius: var(--radius-lg); overflow: auto; }
.content-prose table { width: 100%; border-collapse: collapse; }
.content-prose th, .content-prose td { border: 1px solid var(--border-light); padding: .6rem .75rem; vertical-align: top; }
.content-prose th { background: var(--surface-cool); color: var(--text-primary); font-weight: 700; }

.chip {
    display: inline-flex;
    align-items: center;
    gap: .4rem;
    padding: .4rem .75rem;
    border-radius: 999px;
    border: 1px solid var(--border-light);
    background: var(--surface);
    color: var(--text-secondary);
    font-size: .8125rem;
    font-weight: 600;
    text-decoration: none;
    transition: all .2s ease;
}
.chip:hover { background: var(--surface-cool); border-color: var(--border-medium); color: var(--text-primary); }
.chip.active { background: rgba(14,165,233,.12); border-color: rgba(14,165,233,.35); color: var(--text-primary); }

.pagination { display: flex; justify-content: center; }
.pagination .page-link, .pagination a, .pagination span {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 40px;
    min-height: 40px;
    padding: .35rem .65rem;
    border-radius: 999px;
    border: 1px solid var(--border-light);
    background: var(--surface);
    color: var(--text-secondary);
    text-decoration: none;
    margin: 0 .2rem;
}
.pagination .active span, .pagination .active .page-link {
    background: rgba(15,23,42,.08);
    border-color: rgba(15,23,42,.18);
    color: var(--text-primary);
    font-weight: 700;
}
.pagination a:hover { background: var(--surface-cool); border-color: var(--border-medium); color: var(--text-primary); }

/* ======================================
   PRINT
====================================== */
@media print {
    nav, footer, #sticky-mobile-cta, .btn, .cookie-banner { display: none !important; }
    .section { padding: 1rem 0; }
    body { font-size: 12pt; color: #000; }
}

} /* end @layer base */

</style>
