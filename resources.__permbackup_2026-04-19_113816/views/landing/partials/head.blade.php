<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="csrf-token" content="{{ csrf_token() }}">

<!-- Primary Meta Tags -->
@php
    $companyName = 'PT Cangah Pajaratan Mandiri';
    $landingMetrics = config('landing_metrics');
    $contact = $landingMetrics['contact'] ?? [];
    $schemaPhone = $contact['phone'] ?? '+62 838 7960 2855';
    $schemaPhone = preg_replace('/\s+/', '', $schemaPhone);
    $schemaWhatsapp = $contact['whatsapp_link'] ?? 'https://wa.me/6283879602855';
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

<!-- RSS Feed Auto-Discovery -->
<link rel="alternate" type="application/rss+xml" title="Bizmark.ID Blog RSS" href="{{ route('feed.rss') }}">
<link rel="alternate" type="application/atom+xml" title="Bizmark.ID Blog Atom" href="{{ route('feed.atom') }}">

<!-- Alternate Languages (Hreflang) -->
<link rel="alternate" hreflang="id" href="{{ url('/') }}">
<link rel="alternate" hreflang="en" href="{{ url('/en') }}">
<link rel="alternate" hreflang="x-default" href="{{ url('/') }}">

<!-- WebSite Schema + Sitelinks Searchbox -->
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@type": "WebSite",
    "name": "Bizmark.ID",
    "alternateName": "{{ $companyName }}",
    "url": "{{ url('/') }}",
    "inLanguage": ["id", "en"],
    "potentialAction": {
        "@@type": "SearchAction",
        "target": {
            "@@type": "EntryPoint",
            "urlTemplate": "{{ url('/blog') }}?q={search_term_string}"
        },
        "query-input": "required name=search_term_string"
    }
}
</script>

<!-- Open Graph / Facebook -->
@php
    $defaultOgTitle = app()->getLocale() == 'id' 
        ? 'Bizmark.ID - Konsultan Perizinan LB3, AMDAL, UKL-UPL Karawang' 
        : 'Bizmark.ID - Environmental Permit Consultant Karawang';
    $defaultOgDescription = app()->getLocale() == 'id' 
        ? 'Spesialis perizinan Limbah B3, AMDAL, UKL-UPL untuk industri manufaktur. Proses cepat, transparan, dan terpercaya.' 
        : 'Environmental permit specialist for B3 Waste, AMDAL, UKL-UPL for manufacturing industry. Fast, transparent and trusted.';
@endphp
<meta property="og:type" content="@yield('og_type', 'website')">
<meta property="og:url" content="{{ url()->current() }}">
<meta property="og:title" content="@yield('og_title', $defaultOgTitle)">
<meta property="og:description" content="@yield('og_description', $defaultOgDescription)">
@php
    $ogImage = app()->getLocale() == 'id' ? asset('images/og-image-id.jpg') : asset('images/og-image-en.jpg');
@endphp
<meta property="og:image" content="@yield('og_image', $ogImage)">
@hasSection('article_published_time')
<meta property="article:published_time" content="@yield('article_published_time')">
<meta property="article:modified_time" content="@yield('article_modified_time')">
<meta property="article:section" content="@yield('article_section')">
@endif
<meta property="og:image:width" content="1200">
<meta property="og:image:height" content="630">
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
<meta name="twitter:image" content="{{ $ogImage }}">

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
<link rel="dns-prefetch" href="https://www.googletagmanager.com">
<!-- Font Awesome (low priority, not render-critical) -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" media="print" onload="this.media='all'" />

<!-- Google Fonts - Inter -->
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap">

<!-- Tailwind CSS (compiled) -->
@vite('resources/css/landing.css')

<!-- Critical CSS (Inline for LCP) -->
@include('landing.partials.critical-css')

<!-- Structured Data (JSON-LD Schema.org) -->
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@type": "Organization",
    "name": "Bizmark.ID",
    "legalName": "{{ $companyName }}",
    "url": "{{ url('/') }}",
    "logo": "{{ asset('images/logo.png') }}",
    "description": "{{ __('landing.schema_description') }}",
    "address": {
        "@@type": "PostalAddress",
        "addressCountry": "ID",
        "addressRegion": "West Java",
        "addressLocality": "Karawang"
    },
    "contactPoint": [{
        "@@type": "ContactPoint",
        "telephone": "{{ $schemaPhone }}",
        "contactType": "customer service",
        "availableLanguage": ["Indonesian", "English"],
        "areaServed": "ID"
    }],
    "sameAs": [
        "{{ $schemaWhatsapp }}"
    ]
}
</script>

<!-- Page-specific head content -->
@stack('head')

<!-- Google Analytics 4 -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-DT71N7BSW9" fetchpriority="low"></script>
<script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());
    gtag('config', 'G-DT71N7BSW9');
</script>

<!-- Service Worker Registration -->
<script>
if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
        navigator.serviceWorker.register('/sw.js', { scope: '/' })
            .then((registration) => {
                registration.addEventListener('updatefound', () => {
                    const newWorker = registration.installing;
                    if (newWorker) {
                        newWorker.addEventListener('statechange', () => {
                            if (newWorker.state === 'activated' && navigator.serviceWorker.controller) {
                                // New SW activated, optionally notify user
                            }
                        });
                    }
                });
            })
            .catch((error) => {
                console.error('[SW] Registration failed:', error);
            });
    });
}
</script>
