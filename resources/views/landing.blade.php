{{-- ============================================================
     🗂️  ARCHIVED — V0 (Legacy) Full-Page Template
     ============================================================
     This template is NOT used by any active route.
     
     Active entry points:
       - landing.id.index  →  landing/pages/home.blade.php  (V2)
       - landing.en.index  →  landing/pages/home.blade.php  (V2)
     
     Kept for reference only. All legacy partials imported below
     are superseded by V2 sections in landing/sections/v2/.
     
     To remove:
       1. Run: grep -r "legacy\." resources/views/*.blade.php
       2. Confirm no files reference these partials
       3. Archive the entire landing/partials/legacy/ directory
       4. Delete this file
     ============================================================ --}}
@include('landing.partials.legacy.head')
<body>
    @include('landing.partials.legacy.navbar')

    @include('landing.partials.legacy.hero')

    @include('landing.partials.legacy.about')

    @include('landing.partials.legacy.services')

    @include('landing.partials.legacy.digital-tools')

    @include('landing.partials.legacy.why-us')

    @include('landing.partials.legacy.testimonials')

    @include('landing.partials.legacy.faq')

    @include('landing.partials.legacy.cta')

    @include('landing.partials.legacy.contact')

    @include('landing.partials.legacy.footer')

    @include('landing.partials.legacy.scripts')
