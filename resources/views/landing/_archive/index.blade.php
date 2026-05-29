{{-- ============================================================
     🗂️  ARCHIVED — V1 (Legacy) Landing Page Template
     ============================================================
     This layout is no longer used by any active route.
     
     Active entry points:
       - landing.id.index  →  landing/pages/home.blade.php  (V2)
       - landing.en.index  →  landing/pages/home.blade.php  (V2)
     
     All V2 section partials live in resources/views/landing/sections/v2/
     
     Kept for reference only. To remove:
       1. Confirm no routes reference this view
       2. Archive: mv to landing/_archive/index.blade.php
       3. Remove V1 partials in landing/sections/ that are unused
     ============================================================ --}}
@extends('landing.layout')

@section('content')

{{-- Hero Section - Professional & Clean --}}
@include('landing.sections.hero')

{{-- Services Section --}}
@include('landing.sections.services')

{{-- Process Section --}}
@include('landing.sections.process')

{{-- Social Proof: Clients + Testimonials (Merged) --}}
@include('landing.sections.social-proof')

{{-- Blog/Articles Carousel - ENHANCED --}}
@include('landing.sections.blog')

{{-- Why Choose Section --}}
@include('landing.sections.why-choose')

{{-- FAQ Section --}}
@include('landing.sections.faq')

{{-- Contact Section --}}
@include('landing.sections.contact')

{{-- Footer - ENHANCED --}}
@endsection
