@extends('layouts.app')

@section('title', 'Manajemen Perizinan')
@section('page-title', 'Manajemen Perizinan')

@section('content')
    {{-- Hero Section --}}
    <section class="card-elevated rounded-apple-xl p-5 md:p-6 relative overflow-hidden mb-6">
        <div class="absolute inset-0 pointer-events-none" aria-hidden="true">
            <div class="w-72 h-72 bg-apple-blue opacity-30 blur-3xl rounded-full absolute -top-16 -right-10"></div>
            <div class="w-48 h-48 bg-apple-green opacity-20 blur-2xl rounded-full absolute bottom-0 left-10"></div>
        </div>
        <div class="relative space-y-5 md:space-y-6">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-5">
                <div class="space-y-2.5 max-w-3xl">
                    <p class="text-sm uppercase tracking-[0.4em]" style="color: rgba(235,235,245,0.5);">Pusat Kendali Perizinan</p>
                    <h1 class="text-2xl md:text-3xl font-bold" style="color: #FFFFFF;">
                        Manajemen Perizinan Terpadu
                    </h1>
                    <p class="text-sm md:text-base" style="color: rgba(235,235,245,0.75);">
                        Kelola seluruh alur perizinan dari permohonan, verifikasi, hingga pembayaran dalam satu tampilan komprehensif.
                    </p>
                </div>
                <div class="space-y-2.5 text-sm" style="color: rgba(235,235,245,0.65);">
                    <p><i class="fas fa-sync-alt mr-2"></i>Diperbarui: {{ now()->locale('id')->isoFormat('D MMM Y, HH:mm') }}</p>
                    <p><i class="fas fa-shield-alt mr-2"></i>Akses Tim Operasional</p>
                </div>
            </div>

            <!-- Summary Statistics -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3 md:gap-4">
                <div class="rounded-apple-lg p-3.5 md:p-4" style="background: rgba(10,132,255,0.12);">
                    <p class="text-xs uppercase tracking-widest" style="color: rgba(10,132,255,0.9);">Total Permohonan</p>
                    <h2 class="text-2xl font-bold mt-1.5" style="color: #FFFFFF;">
                        {{ isset($totalApplications) ? $totalApplications : 0 }}
                    </h2>
                    <p class="text-xs" style="color: rgba(235,235,245,0.6);">Semua status</p>
                </div>

                <div class="rounded-apple-lg p-3.5 md:p-4" style="background: rgba(255,159,10,0.12);">
                    <p class="text-xs uppercase tracking-widest" style="color: rgba(255,159,10,0.9);">Perlu Tindakan</p>
                    <h2 class="text-2xl font-bold mt-1.5" style="color: rgba(255,159,10,1);">
                        {{ $notifications['applications'] ?? 0 }}
                    </h2>
                    <p class="text-xs" style="color: rgba(235,235,245,0.6);">Review & catatan baru</p>
                </div>

                <div class="rounded-apple-lg p-3.5 md:p-4" style="background: rgba(52,199,89,0.12);">
                    <p class="text-xs uppercase tracking-widest" style="color: rgba(52,199,89,0.9);">Pembayaran Pending</p>
                    <h2 class="text-2xl font-bold mt-1.5" style="color: rgba(52,199,89,1);">
                        {{ $notifications['payments'] ?? 0 }}
                    </h2>
                    <p class="text-xs" style="color: rgba(235,235,245,0.6);">Perlu verifikasi</p>
                </div>

                <div class="rounded-apple-lg p-3.5 md:p-4" style="background: rgba(175,82,222,0.12);">
                    <p class="text-xs uppercase tracking-widest" style="color: rgba(175,82,222,0.9);">Proyek Aktif</p>
                    <h2 class="text-2xl font-bold mt-1.5" style="color: #FFFFFF;">
                        {{ isset($activeProjects) ? $activeProjects : 0 }}
                    </h2>
                    <p class="text-xs" style="color: rgba(235,235,245,0.6);">Sedang berjalan</p>
                </div>
            </div>
        </div>
    </section>

    {{-- Tab Navigation --}}
    <section class="card-elevated rounded-apple-xl overflow-hidden mb-5">
        <div class="border-b" style="border-color: var(--dark-separator);">
            <div class="flex space-x-1 p-2 overflow-x-auto" role="tablist">
                <button onclick="switchTab('dashboard')" id="tab-dashboard" 
                        class="tab-button {{ $activeTab == 'dashboard' ? 'active' : '' }} px-4 py-2.5 rounded-apple text-sm font-medium transition-apple whitespace-nowrap">
                    <i class="fas fa-chart-pie mr-2"></i>Dashboard
                </button>
                <button onclick="switchTab('applications')" id="tab-applications"
                        class="tab-button {{ $activeTab == 'applications' ? 'active' : '' }} px-4 py-2.5 rounded-apple text-sm font-medium transition-apple whitespace-nowrap">
                    <i class="fas fa-file-signature mr-2"></i>Permohonan Izin
                    @if(($notifications['applications'] ?? 0) > 0)
                        <span class="ml-2 px-2 py-0.5 text-xs font-semibold rounded-full {{ $activeTab == 'applications' ? 'bg-white text-apple-blue' : 'bg-yellow-500 text-white' }}">
                            {{ $notifications['applications'] }}
                        </span>
                    @endif
                </button>
                <button onclick="switchTab('types')" id="tab-types"
                        class="tab-button {{ $activeTab == 'types' ? 'active' : '' }} px-4 py-2.5 rounded-apple text-sm font-medium transition-apple whitespace-nowrap">
                    <i class="fas fa-certificate mr-2"></i>Jenis Izin
                </button>
                <button onclick="switchTab('kbli')" id="tab-kbli"
                        class="tab-button {{ $activeTab == 'kbli' ? 'active' : '' }} px-4 py-2.5 rounded-apple text-sm font-medium transition-apple whitespace-nowrap">
                    <i class="fas fa-file-invoice mr-2"></i>Data KBLI
                </button>
                <button onclick="switchTab('payments')" id="tab-payments"
                        class="tab-button {{ $activeTab == 'payments' ? 'active' : '' }} px-4 py-2.5 rounded-apple text-sm font-medium transition-apple whitespace-nowrap">
                    <i class="fas fa-money-check-alt mr-2"></i>Pembayaran
                    @if(($notifications['payments'] ?? 0) > 0)
                        <span class="ml-2 px-2 py-0.5 text-xs font-semibold rounded-full {{ $activeTab == 'payments' ? 'bg-white text-apple-blue' : 'bg-green-500 text-white' }}">
                            {{ $notifications['payments'] }}
                        </span>
                    @endif
                </button>
            </div>
        </div>

        <div class="p-6">
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
                @include('admin.master-data.tabs.kbli')
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
