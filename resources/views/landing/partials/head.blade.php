<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="X-UA-Compatible" content="IE=edge">

<!-- Primary Meta Tags -->
@php
    $companyName = 'PT Cangah Pajaratan Mandiri';
    $defaultTitle = app()->getLocale() == 'id' 
        ? "Bizmark.ID - Konsultan Perizinan LB3, AMDAL, UKL-UPL Karawang | {$companyName}" 
        : "Bizmark.ID - Environmental Permit Consultant | {$companyName}";
    $defaultMetaTitle = app()->getLocale() == 'id' 
        ? 'Bizmark.ID - Konsultan Perizinan LB3, AMDAL, UKL-UPL Karawang' 
        : 'Bizmark.ID - Environmental Permit Consultant Karawang';
    $defaultDescription = app()->getLocale() == 'id' 
        ? "{$companyName} (Bizmark.ID) - Spesialis perizinan Limbah B3, AMDAL, UKL-UPL untuk industri manufaktur dengan proses yang transparan dan terpercaya." 
        : "{$companyName} (Bizmark.ID) - Environmental permit specialist for B3 Waste, AMDAL, UKL-UPL in the manufacturing industry. Fast, transparent, and trusted.";
    $defaultKeywords = app()->getLocale() == 'id' 
        ? 'konsultan perizinan karawang, jasa perizinan lb3, limbah b3, amdal karawang, ukl upl karawang, perizinan industri manufaktur, konsultan lingkungan' 
        : 'environmental consultant karawang, b3 waste permit, hazardous waste, amdal karawang, environmental permit, manufacturing industry permit';
@endphp
<title>@yield('title', $defaultTitle)</title>
<meta name="title" content="@yield('meta_title', $defaultMetaTitle)">
<meta name="description" content="@yield('meta_description', $defaultDescription)">
<meta name="keywords" content="@yield('meta_keywords', $defaultKeywords)">
<meta name="robots" content="index, follow">
<meta name="language" content="{{ app()->getLocale() }}">
<meta name="author" content="{{ $companyName }} (Bizmark.ID)">
<link rel="canonical" href="{{ url()->current() }}">

<!-- Alternate Languages (Hreflang) -->
<link rel="alternate" hreflang="id" href="{{ url('/') }}?lang=id">
<link rel="alternate" hreflang="en" href="{{ url('/') }}?lang=en">
<link rel="alternate" hreflang="x-default" href="{{ url('/') }}">

<!-- Open Graph / Facebook -->
@php
    $defaultOgTitle = app()->getLocale() == 'id' 
        ? 'Bizmark.ID - Konsultan Perizinan LB3, AMDAL, UKL-UPL Karawang' 
        : 'Bizmark.ID - Environmental Permit Consultant Karawang';
    $defaultOgDescription = app()->getLocale() == 'id' 
        ? 'Spesialis perizinan Limbah B3, AMDAL, UKL-UPL untuk industri manufaktur. Proses cepat, transparan, dan terpercaya.' 
        : 'Environmental permit specialist for B3 Waste, AMDAL, UKL-UPL for manufacturing industry. Fast, transparent and trusted.';
@endphp
<meta property="og:type" content="website">
<meta property="og:url" content="{{ url()->current() }}">
<meta property="og:title" content="@yield('og_title', $defaultOgTitle)">
<meta property="og:description" content="@yield('og_description', $defaultOgDescription)">
<meta property="og:image" content="{{ asset('images/og-image.jpg') }}">
<meta property="og:locale" content="{{ app()->getLocale() == 'id' ? 'id_ID' : 'en_US' }}">
<meta property="og:site_name" content="Bizmark.ID">

<!-- Twitter -->
@php
    $defaultTwitterTitle = app()->getLocale() == 'id' 
        ? 'Bizmark.ID - Konsultan Perizinan LB3, AMDAL, UKL-UPL' 
        : 'Bizmark.ID - Environmental Permit Consultant';
    $defaultTwitterDescription = app()->getLocale() == 'id' 
        ? 'Perizinan industri lebih cepat, transparan, dan terpercaya.' 
        : 'Faster, transparent, and trusted industrial permits.';
@endphp
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:url" content="{{ url()->current() }}">
<meta name="twitter:title" content="@yield('twitter_title', $defaultTwitterTitle)">
<meta name="twitter:description" content="@yield('twitter_description', $defaultTwitterDescription)">
<meta name="twitter:image" content="{{ asset('images/twitter-image.jpg') }}">

<!-- Favicons -->
<link rel="icon" type="image/x-icon" href="/favicon.ico">
<link rel="apple-touch-icon" sizes="180x180" href="/images/icon-192.png">
<meta name="theme-color" content="#0077B5">
<meta name="mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
<meta name="apple-mobile-web-app-title" content="Bizmark.ID">

<!-- PWA Manifest -->
<link rel="manifest" href="/manifest.json">

<!-- Preconnect for external resources -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="preconnect" href="https://cdnjs.cloudflare.com">
<link rel="preconnect" href="https://www.googletagmanager.com">
<link rel="dns-prefetch" href="https://images.pexels.com">

<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />

<!-- Google Fonts - Inter -->
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap">

<!-- Tailwind CSS CDN (Required for landing page custom config) -->
<script src="https://cdn.tailwindcss.com"></script>

<!-- Critical CSS (Inline for LCP) -->
@include('landing.partials.critical-css')

<!-- Structured Data (JSON-LD Schema.org) -->
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@type": "Organization",
    "name": "Bizmark.ID",
    "legalName": "{{ $companyName }}",
    "url": "{{ url('/') }}",
    "logo": "{{ asset('images/logo.png') }}",
    "description": "{{ __('landing.schema_description') }}",
    "address": {
        "@type": "PostalAddress",
        "addressCountry": "ID",
        "addressRegion": "West Java",
        "addressLocality": "Karawang"
    },
    "contactPoint": [{
        "@type": "ContactPoint",
        "telephone": "+62-838-7960-2855",
        "contactType": "customer service",
        "availableLanguage": ["Indonesian", "English"],
        "areaServed": "ID"
    }],
    "sameAs": [
        "https://wa.me/6283879602855"
    ]
}
</script>

<!-- Service Worker Registration - FORCE CLEANUP -->
<script>
// Force update to new cleanup service worker
if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
        // First, unregister all existing service workers
        navigator.serviceWorker.getRegistrations().then(function(registrations) {
            const unregisterPromises = registrations.map(registration => {
                console.log('[Landing] Unregistering old SW:', registration.scope);
                return registration.unregister();
            });
            
            return Promise.all(unregisterPromises);
        }).then(() => {
            console.log('[Landing] All old service workers unregistered');
            
            // Register new cleanup service worker
            return navigator.serviceWorker.register('/sw.js', {
                updateViaCache: 'none', // Don't cache the service worker file
                scope: '/'
            });
        }).then((registration) => {
            console.log('[Landing] Cleanup service worker registered');
            
            // Force immediate activation
            if (registration.waiting) {
                registration.waiting.postMessage({ type: 'SKIP_WAITING' });
            }
            
            // Listen for controller change (new SW activated)
            navigator.serviceWorker.addEventListener('controllerchange', () => {
                console.log('[Landing] New service worker activated, reloading...');
                window.location.reload();
            });
        }).catch((error) => {
            console.error('[Landing] Service worker error:', error);
        });
        
        // Also clear all caches manually
        if ('caches' in window) {
            caches.keys().then(function(names) {
                return Promise.all(
                    names.map(name => {
                        console.log('[Landing] Deleting cache:', name);
                        return caches.delete(name);
                    })
                );
            });
        }
    });
}
</script>
