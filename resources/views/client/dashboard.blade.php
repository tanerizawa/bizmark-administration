@extends('client.layouts.app')

@section('title', 'Dashboard')

@push('styles')
<style>
    /* Pull-to-refresh indicator */
    .pull-to-refresh {
        position: absolute;
        top: -60px;
        left: 0;
        right: 0;
        height: 60px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: white;
        transition: top 0.3s ease;
        z-index: 10;
    }
    @media (prefers-color-scheme: dark) {
        .pull-to-refresh { background: #1f2937; }
    }
    .pull-to-refresh.active { top: 0; }
    .pull-to-refresh i {
        font-size: 24px;
        color: #0A66C2;
        animation: ptr-spin 1s linear infinite;
    }
    @keyframes ptr-spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
    /* Progress bar animation */
    .progress-bar-fill {
        transition: width 0.8s cubic-bezier(0.4, 0, 0.2, 1);
    }
</style>
@endpush

@section('content')
@php
    $statusColors = [
        'Selesai' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400',
        'Dalam Proses' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400',
        'Sedang Diproses' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400',
        'Draft' => 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300',
        'Dokumen Kurang' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400',
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
                : '-'));
@endphp

<div class="space-y-0" role="main" aria-label="Dashboard Client">

    @include('client.dashboard.mobile-hero')
    @include('client.dashboard.desktop-hero')
    @include('client.dashboard.content-cards')
</div>
@endsection
