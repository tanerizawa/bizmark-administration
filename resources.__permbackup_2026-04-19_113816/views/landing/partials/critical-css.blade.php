<!-- Critical CSS - Magazine Editorial Design (Above the Fold) -->
<style>
@layer base {
*,*::before,*::after{box-sizing:border-box}
html{scroll-behavior:smooth;font-size:16px}
body{font-family:'Inter',-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;-webkit-font-smoothing:antialiased;-moz-osx-font-smoothing:grayscale;color:#1a1a2e;line-height:1.7;overflow-x:hidden}
.container-wide{max-width:1320px;margin:0 auto;padding-left:clamp(20px,5vw,48px);padding-right:clamp(20px,5vw,48px)}
.container{max-width:1080px;margin:0 auto;padding-left:clamp(20px,5vw,48px);padding-right:clamp(20px,5vw,48px)}
#main-content{position:relative;overflow:hidden}
.gradient-hero{background:linear-gradient(160deg,#0f172a 0%,#1e3a5f 50%,#0c4a6e 100%)}
.gradient-primary{background:linear-gradient(135deg,#0f172a 0%,#1e3a5f 100%)}
h1{font-size:clamp(2.25rem,5vw,3.75rem);font-weight:800;line-height:1.12;letter-spacing:-0.035em}
h2{font-size:clamp(1.875rem,3.5vw,2.75rem);font-weight:700;line-height:1.2;letter-spacing:-0.025em}
.btn{display:inline-flex;align-items:center;justify-content:center;gap:.5rem;padding:.75rem 1.75rem;font-size:.9375rem;font-weight:600;border-radius:.625rem;transition:all .25s ease;cursor:pointer;border:none;text-decoration:none;line-height:1.4}
.btn-primary{background:#0f172a;color:#fff;box-shadow:0 1px 3px rgba(0,0,0,.12)}
.btn-secondary{background:#f97316;color:#fff}
.btn-ghost{background:rgba(255,255,255,.12);color:#fff;border:1px solid rgba(255,255,255,.25);backdrop-filter:blur(4px)}
.btn-lg{padding:1rem 2rem;font-size:1rem}
.metric-card{background:#fff;border:1px solid #e2e8f0;border-radius:.75rem;padding:1.25rem;text-align:center}
.skip-link{position:absolute;top:-100%;left:50%;transform:translateX(-50%);z-index:9999;padding:.75rem 1.5rem;background:#0f172a;color:#fff;border-radius:.5rem;font-weight:600;transition:top .2s}
.skip-link:focus{top:1rem}
}
.animate-fade-in{opacity:0;animation:fadeIn .6s ease forwards}
@keyframes fadeIn{to{opacity:1}}
.delay-100{animation-delay:.1s}.delay-200{animation-delay:.2s}.delay-300{animation-delay:.3s}
</style>
