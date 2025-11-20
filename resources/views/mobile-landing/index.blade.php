@extends('mobile-landing.layouts.magazine')

@section('content')

<!-- 1. COVER PAGE (Hero) -->
@include('mobile-landing.sections.cover')

<!-- 2. STATS INFOGRAPHIC -->
@include('mobile-landing.sections.stats')

<!-- 2.5 SOCIAL PROOF & LIVE ACTIVITY -->
@include('mobile-landing.sections.social-proof')

<!-- 3. FEATURED ARTICLES (Services) -->
@include('mobile-landing.sections.services')

<!-- 4. PROCESS TIMELINE -->
@include('mobile-landing.sections.process')

<!-- 5. PHOTO ESSAY (Why Us) -->
@include('mobile-landing.sections.why-us')

<!-- 6. BLOG/ARTICLES -->
@include('mobile-landing.sections.blog')

<!-- 7. FAQ ACCORDION -->
@include('mobile-landing.sections.faq')

<!-- 7. CONTACT SPREAD -->
@include('mobile-landing.sections.contact')

<!-- 8. FOOTER -->
@include('mobile-landing.sections.footer')

@endsection
