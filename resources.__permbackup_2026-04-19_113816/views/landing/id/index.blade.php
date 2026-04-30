@extends('landing.layout')

@section('title', __('landing.meta.title'))
@section('meta_title', __('landing.meta.title'))
@section('meta_description', __('landing.meta.description'))
@section('meta_keywords', __('landing.meta.keywords'))
@section('og_title', __('landing.meta.og_title'))
@section('og_description', __('landing.meta.og_description'))

@section('structured_data')
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@type": "LocalBusiness",
    "@id": "{{ url('/') }}/#organization",
    "name": "Bizmark.ID - PT Cangah Pajaratan Mandiri",
    "alternateName": "Bizmark Indonesia",
    "description": "{{ __('landing.meta.description') }}",
    "url": "{{ url('/') }}",
    "logo": "{{ asset('images/logo.png') }}",
    "image": "{{ asset('images/og-image-id.jpg') }}",
    "telephone": "{{ preg_replace('/\\s+/', '', data_get(config('landing_metrics'), 'contact.phone', '+62 838 7960 2855')) }}",
    "email": "{{ data_get(config('landing_metrics'), 'contact.email', 'info@bizmark.id') }}",
    "address": {
        "@@type": "PostalAddress",
        "addressLocality": "Karawang",
        "addressRegion": "Jawa Barat",
        "addressCountry": "ID"
    },
    "areaServed": {
        "@@type": "Country",
        "name": "Indonesia"
    },
    "sameAs": [
        "https://www.linkedin.com/company/bizmark-id",
        "https://www.facebook.com/bizmark.id",
        "https://www.instagram.com/bizmark.id"
    ]
}
</script>
@endsection

@section('content')
@include('landing.pages.home')
@endsection

