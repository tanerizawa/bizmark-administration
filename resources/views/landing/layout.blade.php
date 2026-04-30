<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" class="scroll-smooth">
<script>
// --- Dark/Light Mode Initialization ---
// Must run synchronously before paint to prevent flash
(function() {
    var theme = localStorage.getItem('bizmark_theme');
    if (!theme) {
        theme = window.matchMedia('(prefers-color-scheme: light)').matches ? 'light' : 'dark';
    }
    if (theme === 'light') {
        document.documentElement.setAttribute('data-theme', 'light');
    }
})();
</script>
<head>
    @include('landing.partials.head')
    @vite('resources/css/landing-theme.css')
    @vite('resources/js/app.js')
    @yield('structured_data')
    @stack('styles')
</head>
<body class="font-sans antialiased min-h-screen flex flex-col">

@php
    $landingMetrics = config('landing_metrics');
    $contact = $landingMetrics['contact'] ?? [];
    $whatsappNumber = $contact['whatsapp'] ?? '6283879602855';
    $whatsappLink = $contact['whatsapp_link'] ?? ('https://wa.me/' . $whatsappNumber);
    $phoneNumber = $contact['phone'] ?? '+62 838 7960 2855';
@endphp

<!-- Skip to main content link for accessibility -->
<a href="#main-content" class="skip-link">Lewati ke konten utama</a>

    @include('landing.partials.navbar')
    @include('landing.partials.mobile-menu')
    
    <!-- Main Content -->
    <main id="main-content" class="flex-1">
        @yield('content')
    </main>
    
    @include('landing.partials.footer')
    
    <!-- Scroll-to-top button -->
    <button type="button"
            id="backToTop"
            class="fab fab-back-to-top"
            aria-label="{{ app()->getLocale() === 'id' ? 'Kembali ke atas' : 'Back to top' }}"
            x-data
            @click="window.scrollTo({top:0,behavior:'smooth'}); if(window.trackEvent) trackEvent('Navigation','scroll_to_top','fab');">
        <i class="fas fa-arrow-up" aria-hidden="true"></i>
    </button>
    
    @include('landing.partials.scripts')
    
    {{-- AOS.js Scroll Animations --}}
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        var aosConfig = {
            duration: 800,
            easing: 'ease-out-cubic',
            once: true,
            disable: window.matchMedia('(prefers-reduced-motion: reduce)').matches
        };
        if (typeof AOS !== 'undefined') {
            AOS.init(aosConfig);
        }
        // Re-init on theme change (for light/dark mode transitions)
        window.addEventListener('themeChanged', function() {
            setTimeout(function() { if (typeof AOS !== 'undefined') AOS.refresh(); }, 300);
        });
    });
    </script>
    @stack('scripts')
    
    {{-- ============================================================
         Tawk.to Live Chat Widget — DISABLED (Placeholder Config)
         ============================================================
         This is the single highest-impact conversion improvement
         available at zero code complexity (just configuration).

         TO ENABLE:
         1. Sign up / log in at https://dashboard.tawk.to
         2. Create a new property or use existing one
         3. Go to Admin → Widget Code and copy your property/widget ID
         4. Replace YOUR_PROPERTY_ID / YOUR_WIDGET_ID below
         5. Uncomment the <script> block
         6. Run: php artisan view:clear && npm run build
         7. Verify chat widget appears on landing pages

         ALTERNATIVES (if Tawk.to is not desired):
         - Tidio (tidio.com) — modern interface with AI chatbot
         - Crisp (crisp.chat) — clean B2B-focused interface
         - Both offer similar free tiers
         ============================================================ --}}
    <!--
    <script type="text/javascript">
    var Tawk_API=Tawk_API||{}, Tawk_LoadStart=new Date();
    (function(){
        var s1=document.createElement("script"),s0=document.getElementsByTagName("script")[0];
        s1.async=true;
        s1.src='https://embed.tawk.to/YOUR_PROPERTY_ID/YOUR_WIDGET_ID';
        s1.charset='UTF-8';
        s1.setAttribute('crossorigin','*');
        s0.parentNode.insertBefore(s1,s0);

        Tawk_API.onChatStarted = function(){
            if (typeof gtag !== 'undefined') {
                gtag('event', 'chat_started', {
                    'event_category': 'Engagement',
                    'event_label': 'Tawk.to Chat'
                });
            }
        };
    })();
    </script>
    -->
</body>
</html>
