@extends('landing.layout')

@section('content')

{{-- Hero Section - MODERN REDESIGN --}}
@include('landing.sections.hero-modern')

{{-- Services Section --}}
@include('landing.sections.services')

{{-- Trusted Clients Section - NEW --}}
@include('landing.sections.clients')

{{-- Process Section --}}
@include('landing.sections.process')

{{-- Blog/Articles Carousel - ENHANCED --}}
@include('landing.sections.blog')

{{-- Why Choose Section --}}
@include('landing.sections.why-choose')

{{-- Statistics Section - NEW --}}
@include('landing.sections.stats')

{{-- Testimonials Carousel - NEW --}}
@include('landing.sections.testimonials')

{{-- FAQ Section --}}
@include('landing.sections.faq')

{{-- Final CTA Section - NEW --}}
@include('landing.sections.final-cta')

{{-- Contact Section --}}
@include('landing.sections.contact')

{{-- Footer - ENHANCED --}}
@include('landing.sections.footer')

@endsection
