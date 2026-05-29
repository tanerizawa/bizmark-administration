@extends('client.layouts.app')
@section('title', 'Dokumen')
@section('content')
@php
    $portalV2Master = (bool) config("portal_redesign.enabled", true);
    $portalV2Routes = (array) config("portal_redesign.enabled_routes", []);
    $portalV2Allowed = empty($portalV2Routes) || in_array(request()->route()?->getName(), $portalV2Routes, true);
    $portalLegacy = config("portal_redesign.allow_legacy_query", true) && request()->boolean("legacy");
    $portalV2 = $portalV2Master && $portalV2Allowed && ! $portalLegacy;
@endphp
@if($portalV2)
    @include('client.documents.v2-index')
@else
    @include('client.documents.legacy-index')
@endif
@endsection
