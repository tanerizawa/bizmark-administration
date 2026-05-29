@extends('client.layouts.app')

@section('title', 'Dashboard')

@section('content')
@php
    // Status label map for projects (label-based, colors from client.css)
    $statusMap = [
        'Selesai'       => ['status' => 'completed',   'icon' => 'fa-check-circle'],
        'Dalam Proses'  => ['status' => 'in_progress',  'icon' => 'fa-spinner'],
        'Sedang Diproses' => ['status' => 'in_progress','icon' => 'fa-spinner'],
        'Draft'         => ['status' => 'draft',        'icon' => 'fa-file-alt'],
        'Dokumen Kurang'=> ['status' => 'document_incomplete', 'icon' => 'fa-exclamation-triangle'],
    ];

    $documentCompletion = $totalDocuments > 0
        ? round(($uploadedDocuments / $totalDocuments) * 100)
        : 0;

    // Smart currency display
    $investDisplay = $totalInvested >= 1000000000
        ? 'Rp ' . number_format($totalInvested / 1000000000, 1) . 'M'
        : ($totalInvested >= 1000000
            ? 'Rp ' . number_format($totalInvested / 1000000, 0) . 'Jt'
            : ($totalInvested > 0
                ? 'Rp ' . number_format($totalInvested, 0, ',', '.')
                : 'Belum ada'));
@endphp

@php
    // Portal v2 feature flag (matches layout logic)
    $portalV2Master  = (bool) config('portal_redesign.enabled', false);
    $portalV2Routes  = (array) config('portal_redesign.enabled_routes', []);
    $portalV2Allowed = $portalV2Master || in_array(optional(request()->route())->getName(), $portalV2Routes, true);
    $portalLegacy    = config('portal_redesign.allow_legacy_query', true) && request()->boolean('legacy');
    $portalV2        = $portalV2Allowed && ! $portalLegacy;
@endphp

<div class="space-y-0" role="main" aria-label="Dashboard Client">

    @if($portalV2)
        @include('client.dashboard.v2-hero')
        @include('client.dashboard.v2-content')
    @else
        @include('client.dashboard.mobile-hero')
        @include('client.dashboard.desktop-hero')
        @include('client.dashboard.content-cards')
    @endif
</div>
@endsection
