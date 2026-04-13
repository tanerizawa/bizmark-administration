@extends('layouts.app')

@section('title', 'Manajemen Perizinan')
@section('page-title', 'Manajemen Perizinan')

@section('content')
<div class="space-y-4">
    {{-- Compact Hero Section --}}
    <section class="card-elevated rounded-apple-lg admin-hero relative overflow-hidden">
        <div class="absolute inset-0 pointer-events-none" aria-hidden="true">
            <div class="w-48 h-48 bg-apple-blue opacity-20 blur-3xl rounded-full absolute -top-12 -right-8"></div>
        </div>
        <div class="relative flex flex-col md:flex-row md:items-center md:justify-between gap-3">
            <div class="flex-1 min-w-0">
                <p class="admin-hero-subtitle">Pusat Kendali Perizinan</p>
                <h1 class="admin-hero-title text-white">Manajemen Perizinan</h1>
                <p class="admin-hero-desc">Kelola permohonan, verifikasi, hingga pembayaran</p>
                <div class="admin-hero-meta flex flex-wrap gap-3">
                    <span><i class="fas fa-sync-alt mr-1.5"></i>{{ now()->format('d M Y, H:i') }}</span>
                    <span><i class="fas fa-shield-alt mr-1.5"></i>Tim Operasional</span>
                </div>
            </div>
        </div>
    </section>

    <!-- Compact Summary Statistics -->
    <div class="grid grid-cols-4 gap-2">
        <article class="admin-stat-card card-elevated rounded-apple flex items-center gap-2" 
                 style="background: rgba(10,132,255,0.1); border: 1px solid rgba(10,132,255,0.2);">
            <div class="admin-stat-icon rounded flex items-center justify-center" style="background: rgba(10,132,255,0.2);">
                <i class="fas fa-file-alt text-apple-blue" style="font-size: 0.7rem;"></i>
            </div>
            <div>
                <p class="admin-small text-apple-blue uppercase tracking-wider">Total</p>
                <p class="admin-stat text-white">{{ isset($totalApplications) ? $totalApplications : 0 }}</p>
            </div>
        </article>

        <article class="admin-stat-card card-elevated rounded-apple flex items-center gap-2" 
                 style="background: rgba(255,159,10,0.1); border: 1px solid rgba(255,159,10,0.2);">
            <div class="admin-stat-icon rounded flex items-center justify-center" style="background: rgba(255,159,10,0.2);">
                <i class="fas fa-exclamation text-apple-orange" style="font-size: 0.7rem;"></i>
            </div>
            <div>
                <p class="admin-small text-apple-orange uppercase tracking-wider">Tindakan</p>
                <p class="admin-stat text-apple-orange">{{ $notifications['applications'] ?? 0 }}</p>
            </div>
        </article>

        <article class="admin-stat-card card-elevated rounded-apple flex items-center gap-2" 
                 style="background: rgba(52,199,89,0.1); border: 1px solid rgba(52,199,89,0.2);">
            <div class="admin-stat-icon rounded flex items-center justify-center" style="background: rgba(52,199,89,0.2);">
                <i class="fas fa-credit-card text-apple-green" style="font-size: 0.7rem;"></i>
            </div>
            <div>
                <p class="admin-small text-apple-green uppercase tracking-wider">Pending</p>
                <p class="admin-stat text-apple-green">{{ $notifications['payments'] ?? 0 }}</p>
            </div>
        </article>

        <article class="admin-stat-card card-elevated rounded-apple flex items-center gap-2" 
                 style="background: rgba(175,82,222,0.1); border: 1px solid rgba(175,82,222,0.2);">
            <div class="admin-stat-icon rounded flex items-center justify-center" style="background: rgba(175,82,222,0.2);">
                <i class="fas fa-project-diagram" style="color: #AF52DE; font-size: 0.7rem;"></i>
            </div>
            <div>
                <p class="admin-small uppercase tracking-wider" style="color: #AF52DE;">Aktif</p>
                <p class="admin-stat text-white">{{ isset($activeProjects) ? $activeProjects : 0 }}</p>
            </div>
        </article>
    </div>

    {{-- Tab Navigation --}}
    <section class="card-elevated rounded-apple-lg overflow-hidden">
        <div class="border-b" style="border-color: var(--dark-separator);">
            <div class="flex space-x-1 p-2 overflow-x-auto" role="tablist">
                <button onclick="switchTab('dashboard')" id="tab-dashboard" 
                        class="tab-button {{ $activeTab == 'dashboard' ? 'active' : '' }}">
                    <i class="fas fa-chart-pie"></i>Dashboard
                </button>
                <button onclick="switchTab('applications')" id="tab-applications"
                        class="tab-button {{ $activeTab == 'applications' ? 'active' : '' }}">
                    <i class="fas fa-file-signature"></i>Permohonan
                    @if(($notifications['applications'] ?? 0) > 0)
                        <span class="admin-badge ml-1" style="background: rgba(255,204,0,0.25); color: #FFD60A;">{{ $notifications['applications'] }}</span>
                    @endif
                </button>
                <button onclick="switchTab('types')" id="tab-types"
                        class="tab-button {{ $activeTab == 'types' ? 'active' : '' }}">
                    <i class="fas fa-certificate"></i>Jenis Izin
                </button>
                <button onclick="switchTab('kbli')" id="tab-kbli"
                        class="tab-button {{ $activeTab == 'kbli' ? 'active' : '' }}">
                    <i class="fas fa-file-invoice"></i>KBLI
                </button>
                <button onclick="switchTab('payments')" id="tab-payments"
                        class="tab-button {{ $activeTab == 'payments' ? 'active' : '' }}">
                    <i class="fas fa-money-check-alt"></i>Pembayaran
                    @if(($notifications['payments'] ?? 0) > 0)
                        <span class="admin-badge ml-1" style="background: rgba(52,199,89,0.25); color: #34C759;">{{ $notifications['payments'] }}</span>
                    @endif
                </button>
            </div>
        </div>

        <div class="p-3">
            <!-- Tab 1: Dashboard -->
            <div id="content-dashboard" class="tab-content {{ $activeTab !== 'dashboard' ? 'hidden' : '' }}">
                @include('admin.permits.tabs.dashboard')
            </div>

            <!-- Tab 2: Applications -->
            <div id="content-applications" class="tab-content {{ $activeTab !== 'applications' ? 'hidden' : '' }}">
                @include('admin.permits.tabs.applications')
            </div>

            <!-- Tab 3: Permit Types -->
            <div id="content-types" class="tab-content {{ $activeTab !== 'types' ? 'hidden' : '' }}">
                @include('admin.permits.tabs.types')
            </div>

            <!-- Tab 4: KBLI Data -->
            <div id="content-kbli" class="tab-content {{ $activeTab !== 'kbli' ? 'hidden' : '' }}">
                @include('admin.permits.tabs.kbli')
            </div>

            <!-- Tab 5: Payments -->
            <div id="content-payments" class="tab-content {{ $activeTab !== 'payments' ? 'hidden' : '' }}">
                @include('admin.permits.tabs.payments')
            </div>
        </div>
    </section>
@endsection

@push('styles')
<style>
.tab-button {
    color: rgba(235, 235, 245, 0.6);
    background-color: transparent;
}

.tab-button:hover {
    color: rgba(235, 235, 245, 0.9);
    background-color: rgba(255, 255, 255, 0.05);
}

.tab-button.active {
    color: #FFFFFF;
    background-color: rgba(0, 122, 255, 0.15);
    border: 1px solid rgba(0, 122, 255, 0.3);
}

.tab-content {
    animation: fadeIn 0.3s ease-in;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>
@endpush

@push('scripts')
<script>
function switchTab(tabName) {
    // Update URL without reload
    const url = new URL(window.location);
    url.searchParams.set('tab', tabName);
    window.history.pushState({}, '', url);
    
    // Hide all tabs
    document.querySelectorAll('.tab-content').forEach(content => {
        content.classList.add('hidden');
    });
    
    // Remove active from all buttons
    document.querySelectorAll('.tab-button').forEach(button => {
        button.classList.remove('active');
    });
    
    // Show selected tab
    document.getElementById('content-' + tabName).classList.remove('hidden');
    document.getElementById('tab-' + tabName).classList.add('active');
}

// Handle browser back/forward
window.addEventListener('popstate', function() {
    const urlParams = new URLSearchParams(window.location.search);
    const tab = urlParams.get('tab') || 'dashboard';
    switchTab(tab);
});

// Auto submit forms on filter change (for filter dropdowns)
document.addEventListener('DOMContentLoaded', function() {
    const forms = document.querySelectorAll('form[data-auto-submit]');
    forms.forEach(form => {
        form.querySelectorAll('select[name]').forEach(function(el) {
            el.addEventListener('change', function() {
                form.submit();
            });
        });
    });
});
</script>
@endpush
