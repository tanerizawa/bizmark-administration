@extends('layouts.app')

@section('title', 'Rekrutmen')

@section('content')
@php
    $pendingCount = $notifications['applications'] ?? 0;
@endphp

<div class="recruitment-shell space-y-4">
{{-- Compact Hero Section --}}
<section class="card-elevated rounded-apple-lg admin-hero relative overflow-hidden">
    <div class="absolute inset-0 pointer-events-none" aria-hidden="true">
        <div class="w-48 h-48 bg-apple-blue opacity-20 blur-3xl rounded-full absolute -top-12 -right-8"></div>
    </div>
    <div class="relative flex flex-col md:flex-row md:items-center md:justify-between gap-3">
        <div class="flex-1 min-w-0">
            <p class="admin-hero-subtitle">Manajemen Talenta</p>
            <h1 class="admin-hero-title text-white">Rekrutmen & Lamaran</h1>
            <p class="admin-hero-desc">Kelola lowongan dan proses rekrutmen kandidat</p>
            <div class="admin-hero-meta flex flex-wrap gap-3">
                <span><i class="fas fa-briefcase mr-1.5"></i>{{ $totalJobs }} lowongan</span>
                <span><i class="fas fa-users mr-1.5"></i>{{ $totalApplications }} lamaran</span>
                @if($pendingCount > 0)
                    <span><i class="fas fa-clock mr-1.5"></i>{{ $pendingCount }} pending</span>
                @endif
            </div>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.jobs.create') }}" class="admin-btn admin-btn-sm rounded bg-apple-blue text-white">
                <i class="fas fa-plus mr-1"></i>Lowongan
            </a>
        </div>
    </div>
</section>

{{-- Compact Stats --}}
<div class="grid grid-cols-4 gap-2">
    <article class="admin-stat-card card-elevated rounded-apple flex items-center gap-2" 
             style="background: rgba(10,132,255,0.1); border: 1px solid rgba(10,132,255,0.2);">
        <div class="admin-stat-icon rounded flex items-center justify-center" style="background: rgba(10,132,255,0.2);">
            <i class="fas fa-briefcase text-apple-blue" style="font-size: 0.7rem;"></i>
        </div>
        <div>
            <p class="admin-small text-apple-blue uppercase tracking-wider">Total</p>
            <p class="admin-stat text-white">{{ $totalJobs }}</p>
        </div>
    </article>
    <article class="admin-stat-card card-elevated rounded-apple flex items-center gap-2" 
             style="background: rgba(52,199,89,0.1); border: 1px solid rgba(52,199,89,0.2);">
        <div class="admin-stat-icon rounded flex items-center justify-center" style="background: rgba(52,199,89,0.2);">
            <i class="fas fa-check-circle text-apple-green" style="font-size: 0.7rem;"></i>
        </div>
        <div>
            <p class="admin-small text-apple-green uppercase tracking-wider">Aktif</p>
            <p class="admin-stat text-apple-green">{{ $activeJobs }}</p>
        </div>
    </article>
    <article class="admin-stat-card card-elevated rounded-apple flex items-center gap-2" 
             style="background: rgba(175,82,222,0.1); border: 1px solid rgba(175,82,222,0.2);">
        <div class="admin-stat-icon rounded flex items-center justify-center" style="background: rgba(175,82,222,0.2);">
            <i class="fas fa-users" style="color: #AF52DE; font-size: 0.7rem;"></i>
        </div>
        <div>
            <p class="admin-small uppercase tracking-wider" style="color: #AF52DE;">Lamaran</p>
            <p class="admin-stat text-white">{{ $totalApplications }}</p>
        </div>
    </article>
    <article class="admin-stat-card card-elevated rounded-apple flex items-center gap-2" 
             style="background: rgba(255,214,10,0.1); border: 1px solid rgba(255,214,10,0.2);">
        <div class="admin-stat-icon rounded flex items-center justify-center" style="background: rgba(255,214,10,0.2);">
            <i class="fas fa-clock" style="color: #FFD60A; font-size: 0.7rem;"></i>
        </div>
        <div>
            <p class="admin-small uppercase tracking-wider" style="color: #FFD60A;">Pending</p>
            <p class="admin-stat text-white">{{ $pendingCount }}</p>
        </div>
    </article>
</div>

{{-- Compact Quick Actions Grid --}}
<section class="grid grid-cols-3 gap-2">
    <a href="{{ route('admin.recruitment.pipeline.index') }}" class="admin-module-card card-elevated rounded-apple">
        <div class="flex items-center gap-2">
            <div class="admin-stat-icon rounded-lg flex items-center justify-center" style="background: linear-gradient(135deg, #0A84FF, #AF52DE);">
                <i class="fas fa-stream text-white" style="font-size: 0.65rem;"></i>
            </div>
            <div class="flex-1 min-w-0">
                <h3 class="admin-body font-medium text-white">Pipeline</h3>
                <p class="admin-small text-dark-text-tertiary truncate">Pergerakan kandidat</p>
            </div>
            <i class="fas fa-chevron-right text-apple-blue" style="font-size: 10px;"></i>
        </div>
    </a>
    <a href="{{ route('admin.recruitment.interviews.index') }}" class="admin-module-card card-elevated rounded-apple">
        <div class="flex items-center gap-2">
            <div class="admin-stat-icon rounded-lg flex items-center justify-center" style="background: linear-gradient(135deg, #34C759, #14B8A6);">
                <i class="fas fa-calendar-alt text-white" style="font-size: 0.65rem;"></i>
            </div>
            <div class="flex-1 min-w-0">
                <h3 class="admin-body font-medium text-white">Interview</h3>
                <p class="admin-small text-dark-text-tertiary truncate">Jadwal interview</p>
            </div>
            <i class="fas fa-chevron-right text-apple-green" style="font-size: 10px;"></i>
        </div>
    </a>
    <a href="{{ route('admin.recruitment.tests.index') }}" class="admin-module-card card-elevated rounded-apple">
        <div class="flex items-center gap-2">
            <div class="admin-stat-icon rounded-lg flex items-center justify-center" style="background: linear-gradient(135deg, #F97316, #EF4444);">
                <i class="fas fa-clipboard-list text-white" style="font-size: 0.65rem;"></i>
            </div>
            <div class="flex-1 min-w-0">
                <h3 class="admin-body font-medium text-white">Testing</h3>
                <p class="admin-small text-dark-text-tertiary truncate">Template & sesi tes</p>
            </div>
            <i class="fas fa-chevron-right text-orange-400 text-xs"></i>
        </div>
    </a>
</section>

{{-- Tab Navigation --}}
<section class="card-elevated rounded-apple-xl overflow-hidden">
    <div class="border-b" style="border-color: var(--dark-separator);">
        <div class="flex space-x-1 p-2 overflow-x-auto" role="tablist">
            <button onclick="switchTab('jobs')" id="tab-jobs" 
                    class="tab-button {{ $activeTab == 'jobs' ? 'active' : '' }} text-sm transition-apple whitespace-nowrap">
                <i class="fas fa-briefcase mr-2"></i>Lowongan Kerja
            </button>
            <button onclick="switchTab('applications')" id="tab-applications"
                    class="tab-button {{ $activeTab == 'applications' ? 'active' : '' }} text-sm transition-apple whitespace-nowrap">
                <i class="fas fa-user-tie mr-2"></i>Lamaran Masuk
                @if($pendingCount > 0)
                    <span class="ml-2 px-2 py-0.5 text-xs font-semibold rounded-full {{ $activeTab == 'applications' ? 'bg-white text-apple-blue' : 'bg-yellow-500 text-white' }}">
                        {{ $pendingCount }}
                    </span>
                @endif
            </button>
        </div>
    </div>

    <div class="p-6">
        <!-- Jobs Tab Content -->
        <div id="content-jobs" class="tab-content {{ $activeTab != 'jobs' ? 'hidden' : '' }}">
            @include('admin.recruitment.tabs.jobs')
        </div>
        
        <!-- Applications Tab Content -->
        <div id="content-applications" class="tab-content {{ $activeTab != 'applications' ? 'hidden' : '' }}">
            @include('admin.recruitment.tabs.applications')
        </div>
    </div>
</section>
</div>

@push('styles')
<style>
    .recruitment-shell .tab-button {
        color: rgba(235, 235, 245, 0.6);
        background-color: transparent;
        padding: 0.55rem 0.85rem;
        border: 1px solid transparent;
        border-radius: 10px;
        font-weight: 600;
        min-height: 42px;
    }

    .recruitment-shell .tab-button:hover {
        color: rgba(235, 235, 245, 0.9);
        background-color: rgba(255, 255, 255, 0.05);
    }

    .recruitment-shell .tab-button.active {
        color: #FFFFFF;
        background-color: rgba(0, 122, 255, 0.15);
        border: 1px solid rgba(0, 122, 255, 0.3);
        box-shadow: inset 0 0 0 1px rgba(255,255,255,0.02);
    }

    .recruitment-shell .tab-content {
        animation: fadeIn 0.25s ease-in;
    }
    
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(6px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>
@endpush

@push('scripts')
<script>
function switchTab(tabName) {
    const url = new URL(window.location);
    url.searchParams.set('tab', tabName);
    window.history.pushState({}, '', url);
    
    document.querySelectorAll('.tab-content').forEach(content => content.classList.add('hidden'));
    document.querySelectorAll('.tab-button').forEach(button => button.classList.remove('active'));
    
    const targetContent = document.getElementById('content-' + tabName);
    const targetButton = document.getElementById('tab-' + tabName);
    targetContent?.classList.remove('hidden');
    targetButton?.classList.add('active');
}

window.addEventListener('popstate', function() {
    const tab = new URLSearchParams(window.location.search).get('tab') || 'jobs';
    switchTab(tab);
});

document.addEventListener('DOMContentLoaded', function() {
    const initialTab = new URLSearchParams(window.location.search).get('tab') || 'jobs';
    switchTab(initialTab);
});
</script>
@endpush
@endsection
