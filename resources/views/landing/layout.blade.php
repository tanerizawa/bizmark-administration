<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" class="scroll-smooth" style="margin:0;padding:0;">
<head>
    @include('landing.partials.head')
    @include('landing.partials.styles-modern')
    @yield('structured_data')
    @stack('styles')
</head>
<body class="font-sans antialiased bg-white text-gray-900 min-h-screen flex flex-col" style="margin:0;padding:0;">

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
    
    <!-- Floating Action Buttons -->
    <div class="fab-group fixed bottom-6 right-6 flex flex-col gap-3 z-[999]">
        <a href="{{ $whatsappLink }}?text=Halo%20PT%20Cangah%20Pajaratan%20Mandiri%2C%20saya%20ingin%20konsultasi%20tentang%20perizinan" 
           target="_blank" 
           class="fab fab-whatsapp"
           title="Chat WhatsApp"
           aria-label="Chat via WhatsApp"
           data-cta="fab_whatsapp"
           onclick="trackEvent('CTA', 'click', 'fab_whatsapp')">
            <i class="fab fa-whatsapp"></i>
        </a>
        <a href="tel:{{ str_replace(' ', '', $phoneNumber) }}" 
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
    @stack('scripts')
    
    <!-- Tawk.to Live Chat Widget - DISABLED (Placeholder configuration) -->
    <!-- To enable: Replace YOUR_PROPERTY_ID/YOUR_WIDGET_ID with actual values -->
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
    -->
</body>
</html>
