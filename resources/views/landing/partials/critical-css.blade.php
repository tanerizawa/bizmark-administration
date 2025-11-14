<!-- Critical CSS - Above the Fold (Inline for LCP) -->
<style>
/* Reset & Base */
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
html{scroll-behavior:smooth}
body{font-family:'Inter',-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;-webkit-font-smoothing:antialiased;-moz-osx-font-smoothing:grayscale;color:#0f172a;line-height:1.6}

/* Container */
.container-wide{max-width:1440px;margin:0 auto;padding-left:clamp(24px,5vw,56px);padding-right:clamp(24px,5vw,56px)}

/* Hero Section - Critical Styles */
#home{position:relative;overflow:hidden;background:linear-gradient(180deg,#fff,#fff,#f8fafc);padding-top:8rem;padding-bottom:7rem}
@media(min-width:1024px){#home{padding-top:10rem;padding-bottom:8rem}}

/* Pills */
.pill{display:inline-flex;align-items:center;gap:.55rem;padding:.45rem 1.4rem;border-radius:9999px;font-size:.75rem;font-weight:600;letter-spacing:.35em;text-transform:uppercase}
.pill-brand{background:rgba(30,64,175,.08);color:#1e40af;border:1px solid rgba(30,64,175,.25)}

/* Typography */
h1{font-size:2.25rem;font-weight:600;line-height:1.2;color:#0f172a}
@media(min-width:1024px){h1{font-size:3.4rem}}

/* Buttons */
.btn{display:inline-flex;align-items:center;justify-content:center;gap:.65rem;padding:.85rem 1.9rem;min-height:48px;min-width:120px;font-size:.95rem;font-weight:600;border-radius:9999px;text-transform:uppercase;letter-spacing:.3em;transition:all .28s cubic-bezier(.4,0,.2,1);cursor:pointer;border:none;text-decoration:none}
.btn-primary{background:linear-gradient(135deg,#1e40af,#4338ca);color:#fff;border:1px solid rgba(30,64,175,.65);box-shadow:0 12px 28px -18px rgba(30,64,175,.8)}
.btn-secondary{background:#f7f8fc;color:#1e40af;border:1px solid rgba(30,64,175,.25)}

/* Metric Cards */
.metric-card{background:#fff;border:1px solid rgba(148,163,184,.2);border-radius:1.25rem;padding:1.25rem;text-align:center}
.metric-value{font-size:2rem;font-weight:700;color:#1e40af;margin-bottom:.25rem}
.metric-label{font-size:.75rem;text-transform:uppercase;letter-spacing:.35em;color:#64748b}

/* Loading State - Prevent Flash */
body{opacity:0;animation:fadeIn .3s ease-in forwards}
@keyframes fadeIn{to{opacity:1}}
</style>
