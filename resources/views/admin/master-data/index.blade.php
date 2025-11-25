@extends('layouts.app')

@section('title', 'Master Data')
@section('page-title', 'Master Data')

@section('content')
<div class="master-shell space-y-5">
    {{-- Hero Section --}}
    <section class="card-elevated rounded-apple-xl p-5 md:p-6 relative overflow-hidden section-card">
        <div class="absolute inset-0 pointer-events-none" aria-hidden="true">
            <div class="w-72 h-72 bg-apple-blue opacity-30 blur-3xl rounded-full absolute -top-16 -right-10"></div>
            <div class="w-48 h-48 bg-apple-purple opacity-20 blur-2xl rounded-full absolute bottom-0 left-10"></div>
        </div>
        <div class="relative space-y-5 md:space-y-6">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-5">
                <div class="space-y-2.5 max-w-3xl">
                    <p class="text-sm uppercase tracking-[0.4em]" style="color: rgba(235,235,245,0.5);">Pusat Data Master</p>
                    <h1 class="text-2xl md:text-3xl font-bold" style="color: #FFFFFF;">
                        Klasifikasi Baku Lapangan Usaha Indonesia (KBLI)
                    </h1>
                    <p class="text-sm md:text-base" style="color: rgba(235,235,245,0.75);">
                        Kelola data KBLI untuk keperluan perizinan dan klasifikasi bidang usaha.
                    </p>
                </div>
                <div class="space-y-2.5 text-sm" style="color: rgba(235,235,245,0.65);">
                    <p><i class="fas fa-sync-alt mr-2"></i>Diperbarui: {{ now()->locale('id')->isoFormat('D MMM Y, HH:mm') }}</p>
                    <p><i class="fas fa-database mr-2"></i>Sistem Data Terintegrasi</p>
                </div>
            </div>

            <!-- Summary Statistics -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 md:gap-4">
                <div class="rounded-apple-lg p-3.5 md:p-4" style="background: rgba(175,82,222,0.12);">
                    <p class="text-xs uppercase tracking-widest" style="color: rgba(175,82,222,0.9);">Data KBLI</p>
                    <h2 class="text-2xl font-bold mt-1.5" style="color: #FFFFFF;">
                        {{ number_format($totalKbli) }}
                    </h2>
                    <p class="text-xs" style="color: rgba(235,235,245,0.6);">Klasifikasi usaha tersedia</p>
                </div>

                <div class="rounded-apple-lg p-3.5 md:p-4" style="background: rgba(10,132,255,0.12);">
                    <p class="text-xs uppercase tracking-widest" style="color: rgba(10,132,255,0.9);">Akses Cepat Keuangan</p>
                    <div class="mt-1.5">
                        <a href="{{ route('cash-accounts.index') }}" class="inline-flex items-center text-sm font-medium text-apple-blue hover:text-apple-blue-dark transition-colors">
                            <i class="fas fa-wallet mr-2"></i>Buka Akun Kas & Bank
                            <i class="fas fa-arrow-right ml-2"></i>
                        </a>
                    </div>
                    <p class="text-xs mt-1" style="color: rgba(235,235,245,0.6);">Manajemen keuangan dipindah ke menu terpisah</p>
                </div>
            </div>
        </div>
    </section>

    {{-- Tab Navigation - KBLI Only --}}
    <section class="card-elevated rounded-apple-xl overflow-hidden section-card">
        <div class="border-b" style="border-color: var(--dark-separator);">
            <div class="flex space-x-1 p-2 overflow-x-auto" role="tablist">
                <button onclick="switchTab('kbli')" id="tab-kbli"
                        class="tab-button active text-sm transition-apple whitespace-nowrap">
                    <i class="fas fa-file-invoice mr-2"></i>Data KBLI
                </button>
            </div>
        </div>

        <div class="p-6">
            <!-- Tab: KBLI -->
            <div id="content-kbli" class="tab-content">
                @include('admin.master-data.tabs.kbli')
            </div>
        </div>
    </section>
</div>
@endsection

@push('styles')
<style>
    .master-shell {
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        letter-spacing: -0.01em;
    }

    .master-shell .section-card {
        border: 1px solid var(--dark-separator);
    }

    .master-shell .tab-button {
        color: rgba(235, 235, 245, 0.6);
        background-color: transparent;
        padding: 0.55rem 0.85rem;
        border: 1px solid transparent;
        border-radius: 10px;
        font-weight: 600;
        min-height: 42px;
    }

    .master-shell .tab-button:hover {
        color: rgba(235, 235, 245, 0.9);
        background-color: rgba(255, 255, 255, 0.05);
    }

    .master-shell .tab-button.active {
        color: #FFFFFF;
        background-color: rgba(0, 122, 255, 0.14);
        border: 1px solid rgba(0, 122, 255, 0.3);
        box-shadow: inset 0 0 0 1px rgba(255,255,255,0.02);
    }

    .master-shell .tab-content {
        animation: fadeIn 0.25s ease-in;
    }
    
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(6px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .master-shell .card-apple {
        border: 1px solid var(--dark-separator);
        border-radius: 14px;
        background: var(--dark-bg-elevated);
        box-shadow: 0 6px 18px rgba(0,0,0,0.4);
    }

    .master-shell h1, .master-shell h2, .master-shell h3 {
        letter-spacing: -0.015em;
    }

    .master-shell h1 {
        font-size: 1.75rem;
    }

    .master-shell h2 {
        font-size: 1.05rem;
        font-weight: 700;
    }

    .master-shell h3 {
        font-size: 1rem;
        font-weight: 700;
    }

    .master-shell .btn-apple-primary,
    .master-shell .btn-apple-secondary,
    .master-shell .btn-apple,
    .master-shell .btn-icon-apple {
        border-radius: 10px;
    }

    .master-shell .btn-apple-primary,
    .master-shell .btn-apple-secondary,
    .master-shell .btn-apple {
        padding: 0.55rem 0.95rem;
        font-size: 0.92rem;
    }

    .master-shell .btn-icon-apple {
        padding: 0.4rem;
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
