<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" class="scroll-smooth">
<head>
    @include('landing.partials.head')
    @include('landing.partials.styles-modern')
</head>
<body>
    @include('landing.partials.navbar')
    @include('landing.partials.mobile-menu')
    
    <!-- Main Content -->
    <main id="main-content">
        @yield('content')
    </main>
    
    <!-- Floating Action Buttons -->
    <div class="fab-group">
        <a href="https://wa.me/6283879602855?text=Halo%20PT%20Cangah%20Pajaratan%20Mandiri%2C%20saya%20ingin%20konsultasi%20tentang%20perizinan" 
           target="_blank" 
           class="fab fab-whatsapp"
           title="Chat WhatsApp"
           aria-label="Chat via WhatsApp"
           data-cta="fab_whatsapp"
           onclick="trackEvent('CTA', 'click', 'fab_whatsapp')">
            <i class="fab fa-whatsapp"></i>
        </a>
        <a href="tel:+6283879602855" 
           class="fab fab-phone"
           title="Telepon Kami"
           aria-label="Hubungi via telepon"
           data-cta="fab_phone"
           onclick="trackEvent('CTA', 'click', 'fab_phone')">
            <i class="fas fa-phone-alt"></i>
        </a>
        <button onclick="window.scrollTo({top: 0, behavior: 'smooth'}); trackEvent('Navigation', 'scroll_to_top', 'fab');" 
                class="fab fab-back-to-top"
                id="backToTop"
                title="Kembali ke Atas"
                aria-label="Kembali ke atas halaman">
            <i class="fas fa-arrow-up"></i>
        </button>
    </div>
    
    @include('landing.partials.scripts')
    
    <!-- Tawk.to Live Chat Widget -->
    <script type="text/javascript">
    var Tawk_API=Tawk_API||{}, Tawk_LoadStart=new Date();
    (function(){
        var s1=document.createElement("script"),s0=document.getElementsByTagName("script")[0];
        s1.async=true;
        s1.src='https://embed.tawk.to/YOUR_PROPERTY_ID/YOUR_WIDGET_ID';
        s1.charset='UTF-8';
        s1.setAttribute('crossorigin','*');
        s0.parentNode.insertBefore(s1,s0);
        
        // Track chat events
        Tawk_API.onLoad = function(){
            console.log('Tawk.to chat loaded');
        };
        
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
</body>
</html>
