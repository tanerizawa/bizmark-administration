<!-- Critical CSS - Above the Fold -->
<style>
@layer base {
*,*::before,*::after{box-sizing:border-box}
html{scroll-behavior:smooth;font-size:16px;background:var(--bg-base,#0a0f1e)}
body{font-family:'Inter',-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif;-webkit-font-smoothing:antialiased;-moz-osx-font-smoothing:grayscale;background:var(--bg-base,#0a0f1e);color:var(--text-primary,#f1f5f9);line-height:1.65;overflow-x:hidden;min-height:100vh}
.container-wide{max-width:1280px;margin:0 auto;padding-left:clamp(16px,4vw,40px);padding-right:clamp(16px,4vw,40px)}
.container{max-width:1100px;margin:0 auto;padding-left:clamp(16px,4vw,40px);padding-right:clamp(16px,4vw,40px)}
#main-content{position:relative;overflow:hidden}
h1,h2,h3,h4,h5,h6{font-family:'Fraunces','Playfair Display',Georgia,serif;font-weight:700;line-height:1.15;letter-spacing:-0.025em;color:var(--text-primary,#f1f5f9)}
h1{font-size:clamp(2.25rem,5vw,3.75rem);font-weight:800;line-height:1.06;letter-spacing:-0.035em}
h2{font-size:clamp(1.75rem,3vw,2.5rem);line-height:1.15;letter-spacing:-0.025em}
.btn{display:inline-flex;align-items:center;justify-content:center;gap:.5rem;padding:.625rem 1.5rem;font-size:.9375rem;font-weight:600;border-radius:10px;transition:all .2s;cursor:pointer;border:1px solid transparent;text-decoration:none;line-height:1.4;font-family:'Inter',sans-serif}
.btn-primary{background:#3b82f6;color:#fff;box-shadow:0 2px 8px rgba(59,130,246,.35)}
.btn-secondary{background:#3b82f6;color:#fff}
.btn-ghost{background:rgba(255,255,255,.08);color:var(--text-primary,#f1f5f9);border:1px solid var(--border-light,rgba(255,255,255,.12));backdrop-filter:blur(4px)}
.btn-lg{padding:.875rem 2rem;font-size:1rem}
.metric-card{background:var(--bg-raised,#0f1629);border:1px solid var(--border-subtle,rgba(255,255,255,.07));border-radius:12px;padding:1.25rem;text-align:center}
.skip-link{position:absolute;top:-100%;left:50%;transform:translateX(-50%);z-index:9999;padding:.75rem 1.5rem;background:#3b82f6;color:#fff;border-radius:8px;font-weight:600;transition:top .2s}
.skip-link:focus{top:1rem}
.app-navbar{background:var(--navbar-bg,rgba(10,15,30,.85));border-bottom:1px solid var(--border-subtle,rgba(255,255,255,.07));backdrop-filter:blur(16px)}
}
.animate-fade-in{opacity:0;animation:fadeIn .6s ease forwards}
@keyframes fadeIn{to{opacity:1}}
.delay-100{animation-delay:.1s}.delay-200{animation-delay:.2s}.delay-300{animation-delay:.3s}
</style>
