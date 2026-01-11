@extends('landing.layout')

@section('content')

{{-- Hero Section - Optimized (Phase 1) --}}
@include('landing.sections.hero-modern')

{{-- Trust Bar - Phase 2: Social Proof --}}
@include('landing.partials.trust-bar')

{{-- Services Section --}}
@include('landing.sections.services')

{{-- Process Section --}}
@include('landing.sections.process')

{{-- Enhanced Testimonials - Phase 2: Trust & Credibility --}}
@include('landing.partials.testimonials-enhanced')

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
