@extends('layouts.app')

@section('title', 'Master Data')
@section('page-title', 'Master Data')

@section('content')
    {{-- Hero Section --}}
    <section class="card-elevated rounded-apple-xl p-5 md:p-6 relative overflow-hidden mb-6">
        <div class="absolute inset-0 pointer-events-none" aria-hidden="true">
            <div class="w-72 h-72 bg-apple-blue opacity-30 blur-3xl rounded-full absolute -top-16 -right-10"></div>
            <div class="w-48 h-48 bg-apple-purple opacity-20 blur-2xl rounded-full absolute bottom-0 left-10"></div>
        </div>
        <div class="relative space-y-5 md:space-y-6">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-5">
                <div class="space-y-2.5 max-w-3xl">
                    <p class="text-sm uppercase tracking-[0.4em]" style="color: rgba(235,235,245,0.5);">Pusat Data Master</p>
                    <h1 class="text-2xl md:text-3xl font-bold" style="color: #FFFFFF;">
                        Master Data Management
                    </h1>
                    <p class="text-sm md:text-base" style="color: rgba(235,235,245,0.75);">
                        Kelola akun kas, data KBLI, dan rekonsiliasi bank dalam satu platform terpadu.
                    </p>
                </div>
                <div class="space-y-2.5 text-sm" style="color: rgba(235,235,245,0.65);">
                    <p><i class="fas fa-sync-alt mr-2"></i>Diperbarui: {{ now()->locale('id')->isoFormat('D MMM Y, HH:mm') }}</p>
                    <p><i class="fas fa-database mr-2"></i>Sistem Data Terintegrasi</p>
                </div>
            </div>

            <!-- Summary Statistics -->
            <div class="grid grid-cols-2 md:grid-cols-3 gap-3 md:gap-4">
                <div class="rounded-apple-lg p-3.5 md:p-4" style="background: rgba(10,132,255,0.12);">
                    <p class="text-xs uppercase tracking-widest" style="color: rgba(10,132,255,0.9);">Akun Kas</p>
                    <h2 class="text-2xl font-bold mt-1.5" style="color: #FFFFFF;">
                        {{ number_format($totalCashAccounts) }}
                    </h2>
                    <p class="text-xs" style="color: rgba(235,235,245,0.6);">
                        <span class="text-apple-green font-medium">{{ number_format($activeCashAccounts) }} aktif</span>
                    </p>
                </div>

                <div class="rounded-apple-lg p-3.5 md:p-4" style="background: rgba(175,82,222,0.12);">
                    <p class="text-xs uppercase tracking-widest" style="color: rgba(175,82,222,0.9);">Data KBLI</p>
                    <h2 class="text-2xl font-bold mt-1.5" style="color: #FFFFFF;">
                        {{ number_format($totalKbli) }}
                    </h2>
                    <p class="text-xs" style="color: rgba(235,235,245,0.6);">Klasifikasi usaha</p>
                </div>

                <div class="rounded-apple-lg p-3.5 md:p-4" style="background: rgba(255,159,10,0.12);">
                    <p class="text-xs uppercase tracking-widest" style="color: rgba(255,159,10,0.9);">Rekonsiliasi</p>
                    <h2 class="text-2xl font-bold mt-1.5" style="color: {{ $pendingReconciliations > 0 ? 'rgba(255,159,10,1)' : '#FFFFFF' }};">
                        {{ number_format($totalReconciliations) }}
                    </h2>
                    <p class="text-xs" style="color: rgba(235,235,245,0.6);">
                        @if($pendingReconciliations > 0)
                            <span class="text-apple-orange font-medium">{{ number_format($pendingReconciliations) }} menunggu</span>
                        @else
                            <span class="text-apple-green font-medium">Semua selesai</span>
                        @endif
                    </p>
                </div>
            </div>
        </div>
    </section>

    {{-- Tab Navigation --}}
    <section class="card-elevated rounded-apple-xl overflow-hidden mb-5">
        <div class="border-b" style="border-color: var(--dark-separator);">
            <div class="flex space-x-1 p-2 overflow-x-auto" role="tablist">
                <button onclick="switchTab('cash-accounts')" id="tab-cash-accounts" 
                        class="tab-button {{ $activeTab == 'cash-accounts' ? 'active' : '' }} px-4 py-2.5 rounded-apple text-sm font-medium transition-apple whitespace-nowrap">
                    <i class="fas fa-wallet mr-2"></i>Akun Kas
                </button>
                <button onclick="switchTab('kbli')" id="tab-kbli"
                        class="tab-button {{ $activeTab == 'kbli' ? 'active' : '' }} px-4 py-2.5 rounded-apple text-sm font-medium transition-apple whitespace-nowrap">
                    <i class="fas fa-file-invoice mr-2"></i>Data KBLI
                </button>
                <button onclick="switchTab('reconciliations')" id="tab-reconciliations"
                        class="tab-button {{ $activeTab == 'reconciliations' ? 'active' : '' }} px-4 py-2.5 rounded-apple text-sm font-medium transition-apple whitespace-nowrap">
                    <i class="fas fa-balance-scale mr-2"></i>Rekonsiliasi Bank
                    @if($pendingReconciliations > 0)
                        <span class="ml-2 px-2 py-0.5 text-xs font-semibold rounded-full {{ $activeTab == 'reconciliations' ? 'bg-white text-apple-blue' : 'bg-orange-500 text-white' }}">
                            {{ $pendingReconciliations }}
                        </span>
                    @endif
                </button>
            </div>
        </div>

        <div class="p-6">
            <!-- Tab 1: Cash Accounts -->
            <div id="content-cash-accounts" class="tab-content {{ $activeTab !== 'cash-accounts' ? 'hidden' : '' }}">
                @include('admin.master-data.tabs.cash-accounts')
            </div>

            <!-- Tab 2: KBLI -->
            <div id="content-kbli" class="tab-content {{ $activeTab !== 'kbli' ? 'hidden' : '' }}">
                @include('admin.master-data.tabs.kbli')
            </div>

            <!-- Tab 3: Reconciliations -->
            <div id="content-reconciliations" class="tab-content {{ $activeTab !== 'reconciliations' ? 'hidden' : '' }}">
                @include('admin.master-data.tabs.reconciliations')
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
    const tab = urlParams.get('tab') || 'cash-accounts';
    switchTab(tab);
});
</script>
@endpush
