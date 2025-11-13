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
@include('landing.sections.footer')

@endsection
