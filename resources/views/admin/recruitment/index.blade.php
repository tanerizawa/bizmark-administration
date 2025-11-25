@extends('layouts.app')

@section('title', 'Rekrutmen')

@section('content')
@php
    $pendingCount = $notifications['applications'] ?? 0;
@endphp

<div class="recruitment-shell max-w-7xl mx-auto space-y-5">
{{-- Hero Section --}}
<section class="card-elevated rounded-apple-xl p-5 md:p-6 relative overflow-hidden">
    <div class="absolute inset-0 pointer-events-none" aria-hidden="true">
        <div class="w-72 h-72 bg-apple-blue opacity-30 blur-3xl rounded-full absolute -top-16 -right-10"></div>
        <div class="w-48 h-48 bg-apple-green opacity-20 blur-2xl rounded-full absolute bottom-0 left-10"></div>
    </div>
    <div class="relative flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
        <div class="space-y-3 max-w-3xl">
            <p class="text-xs uppercase tracking-[0.4em]" style="color: rgba(235,235,245,0.5);">Manajemen Talenta</p>
            <h1 class="text-2xl md:text-3xl font-bold text-white">Rekrutmen & Lamaran</h1>
            <p class="text-sm md:text-base" style="color: rgba(235,235,245,0.7);">
                Kelola lowongan kerja dan proses rekrutmen kandidat dalam satu platform terpadu.
            </p>
            <div class="text-xs flex flex-wrap gap-3" style="color: rgba(235,235,245,0.6);">
                <span><i class="fas fa-briefcase mr-2"></i>{{ $totalJobs }} lowongan</span>
                <span><i class="fas fa-users mr-2"></i>{{ $totalApplications }} lamaran</span>
                @if($pendingCount > 0)
                    <span><i class="fas fa-clock mr-2"></i>{{ $pendingCount }} menunggu review</span>
                @endif
            </div>
        </div>
        <div class="flex flex-col items-start gap-3">
            <a href="{{ route('admin.recruitment.pipeline.index') }}" class="btn-secondary">
                <i class="fas fa-stream mr-2"></i>Pipeline Kandidat
            </a>
            <a href="{{ route('admin.jobs.create') }}" class="btn-primary">
                <i class="fas fa-plus mr-2"></i>Tambah Lowongan
            </a>
        </div>
    </div>
</section>

{{-- Stats --}}
<section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
    <div class="card-elevated rounded-apple-lg p-4 space-y-1">
        <p class="text-xs uppercase tracking-widest" style="color: rgba(10,132,255,0.9);">Total Lowongan</p>
        <p class="text-3xl font-bold text-white">{{ $totalJobs }}</p>
        <p class="text-xs" style="color: rgba(235,235,245,0.6);">{{ $activeJobs }} sedang aktif</p>
    </div>
    <div class="card-elevated rounded-apple-lg p-4 space-y-1">
        <p class="text-xs uppercase tracking-widest" style="color: rgba(52,199,89,0.9);">Lowongan Aktif</p>
        <p class="text-3xl font-bold text-white">{{ $activeJobs }}</p>
        <p class="text-xs" style="color: rgba(235,235,245,0.6);">Sedang tayang untuk publik</p>
    </div>
    <div class="card-elevated rounded-apple-lg p-4 space-y-1">
        <p class="text-xs uppercase tracking-widest" style="color: rgba(10,132,255,0.9);">Total Lamaran</p>
        <p class="text-3xl font-bold text-white">{{ $totalApplications }}</p>
        <p class="text-xs" style="color: rgba(235,235,245,0.6);">Akumulasi seluruh lowongan</p>
    </div>
    <div class="card-elevated rounded-apple-lg p-4 space-y-1">
        <p class="text-xs uppercase tracking-widest" style="color: rgba(255,214,10,0.9);">Pending Review</p>
        <p class="text-3xl font-bold text-white">{{ $pendingCount }}</p>
        <p class="text-xs" style="color: rgba(235,235,245,0.6);">Perlu peninjauan</p>
    </div>
</section>

{{-- Quick Actions --}}
<section class="mb-5">
    <a href="{{ route('admin.recruitment.pipeline.index') }}" class="card-elevated rounded-apple-xl p-5 block hover:scale-[1.01] transition-transform">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-apple-blue to-apple-purple flex items-center justify-center">
                    <i class="fas fa-stream text-2xl text-white"></i>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-white mb-1">Pipeline Rekrutmen</h3>
                    <p class="text-sm" style="color: rgba(235,235,245,0.7);">Pantau pergerakan kandidat di setiap tahap proses rekrutmen</p>
                </div>
            </div>
            <i class="fas fa-chevron-right text-apple-blue"></i>
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
