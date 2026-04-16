@extends('layouts.app')

@section('title', 'Lead Management')

@section('content')
<div class="leads-shell space-y-4">
    {{-- Compact Hero Section --}}
    <section class="card-elevated rounded-apple-lg admin-hero relative overflow-hidden">
        <div class="absolute inset-0 pointer-events-none" aria-hidden="true">
            <div class="w-48 h-48 bg-apple-blue opacity-20 blur-3xl rounded-full absolute -top-12 -right-8"></div>
        </div>
        <div class="relative flex flex-col md:flex-row md:items-center md:justify-between gap-3">
            <div class="flex-1 min-w-0">
                <p class="admin-hero-subtitle">Lead Management</p>
                <h1 class="admin-hero-title text-white">Kelola Lead & Inquiry</h1>
                <p class="admin-hero-desc">Pantau leads dari konsultasi AI dan estimasi perizinan</p>
                <div class="admin-hero-meta flex flex-wrap gap-3">
                    <span><i class="fas fa-envelope mr-1.5"></i>{{ $serviceInquiriesCount }} inquiry</span>
                    <span><i class="fas fa-calculator mr-1.5"></i>{{ $consultationLeadsCount }} consultation</span>
                </div>
            </div>
            <div class="flex items-center gap-2">
                @if($activeTab === 'service-inquiries')
                    <a href="{{ route('admin.service-inquiries.export', request()->all()) }}" class="admin-btn admin-btn-sm rounded" style="background: rgba(255,255,255,0.08); color: rgba(235,235,245,0.8);">
                        <i class="fas fa-download mr-1"></i>Export
                    </a>
                @else
                    <a href="{{ route('admin.consultation-leads.export', request()->all()) }}" class="admin-btn admin-btn-sm rounded" style="background: rgba(255,255,255,0.08); color: rgba(235,235,245,0.8);">
                        <i class="fas fa-download mr-1"></i>Export
                    </a>
                @endif
            </div>
        </div>
    </section>

    {{-- Alert Messages --}}
    @if(session('success'))
        <div class="admin-alert admin-alert-success">
            <i class="fas fa-check-circle"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="admin-alert admin-alert-error">
            <i class="fas fa-exclamation-circle"></i>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    {{-- Tab Navigation --}}
    <section class="card-elevated rounded-apple-lg overflow-hidden">
        <div class="border-b" style="border-color: var(--dark-separator);">
            <div class="flex space-x-1 p-2 overflow-x-auto" role="tablist">
                <button onclick="switchTab('service-inquiries')" id="tab-service-inquiries" 
                        class="tab-button {{ $activeTab == 'service-inquiries' ? 'active' : '' }}">
                    <i class="fas fa-envelope"></i>Service Inquiries
                    @if($serviceInquiriesCount > 0)
                        <span class="admin-badge ml-1 {{ $activeTab == 'service-inquiries' ? 'bg-white text-apple-blue' : '' }}" style="{{ $activeTab != 'service-inquiries' ? 'background: rgba(10,132,255,0.25); color: #fff;' : '' }}">
                            {{ $serviceInquiriesCount }}
                        </span>
                    @endif
                </button>
                <button onclick="switchTab('consultation-leads')" id="tab-consultation-leads"
                        class="tab-button {{ $activeTab == 'consultation-leads' ? 'active' : '' }}">
                    <i class="fas fa-calculator"></i>Consultation Leads
                    @if($consultationLeadsCount > 0)
                        <span class="admin-badge ml-1 {{ $activeTab == 'consultation-leads' ? 'bg-white text-apple-blue' : '' }}" style="{{ $activeTab != 'consultation-leads' ? 'background: rgba(255,204,0,0.25); color: #FFD60A;' : '' }}">
                            {{ $consultationLeadsCount }}
                        </span>
                    @endif
                </button>
            </div>
        </div>

        <div class="p-3">
            <!-- Service Inquiries Tab Content -->
            <div id="content-service-inquiries" class="tab-content {{ $activeTab != 'service-inquiries' ? 'hidden' : '' }}">
                @include('admin.leads.tabs.service-inquiries')
            </div>
            
            <!-- Consultation Leads Tab Content -->
            <div id="content-consultation-leads" class="tab-content {{ $activeTab != 'consultation-leads' ? 'hidden' : '' }}">
                @include('admin.leads.tabs.consultation-leads')
            </div>
        </div>
    </section>
</div>

<!-- Convert to Client Modal -->
<div id="convertModal" class="hidden fixed inset-0 bg-black bg-opacity-75 z-50 flex items-center justify-center p-4">
    <div class="card-elevated rounded-apple-lg max-w-md w-full p-4">
        <h3 class="admin-section text-white mb-3">Konversi ke Klien</h3>
        <p class="admin-body text-dark-text-secondary mb-4">
            Konversi consultation lead ini menjadi akun klien terdaftar?
        </p>
        <form id="convertForm" method="POST" class="space-y-3">
            @csrf
            <div>
                <label class="inline-flex items-center">
                    <input type="checkbox" name="create_client_account" value="1" class="form-checkbox bg-dark-bg-tertiary border-dark-separator" checked>
                    <span class="ml-2 admin-body text-dark-text-primary">Buat akun klien</span>
                </label>
            </div>
            <div>
                <input type="password" name="password" placeholder="Password untuk klien" 
                       class="admin-input w-full" required>
            </div>
            <div>
                <input type="text" name="company_name" placeholder="Nama perusahaan (opsional)" 
                       class="admin-input w-full">
            </div>
            <div class="flex justify-end space-x-2 pt-2">
                <button type="button" onclick="hideConvertModal()" class="admin-btn admin-btn-sm rounded" style="background: rgba(255,255,255,0.08); color: rgba(235,235,245,0.8);">Batal</button>
                <button type="submit" class="admin-btn admin-btn-sm rounded bg-apple-blue text-white">Konversi</button>
            </div>
        </form>
    </div>
</div>

@push('styles')
<style>
    .leads-shell .tab-button {
        color: rgba(235, 235, 245, 0.6);
        background-color: transparent;
        padding: 0.55rem 0.85rem;
        border: 1px solid transparent;
        border-radius: 10px;
        font-weight: 600;
        min-height: 42px;
    }

    .leads-shell .tab-button:hover {
        color: rgba(235, 235, 245, 0.9);
        background-color: rgba(255, 255, 255, 0.05);
    }

    .leads-shell .tab-button.active {
        color: #FFFFFF;
        background-color: rgba(0, 122, 255, 0.15);
        border: 1px solid rgba(0, 122, 255, 0.3);
        box-shadow: inset 0 0 0 1px rgba(255,255,255,0.02);
    }

    .leads-shell .tab-content {
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
    // Clear other filters when switching tabs
    url.searchParams.delete('search');
    url.searchParams.delete('status');
    url.searchParams.delete('priority');
    url.searchParams.delete('contacted');
    url.searchParams.delete('date_from');
    url.searchParams.delete('date_to');
    url.searchParams.delete('page');
    window.location.href = url.toString();
}

function showConvertModal(consultationId) {
    document.getElementById('convertForm').action = `/admin/consultation-leads/${consultationId}/convert`;
    document.getElementById('convertModal').classList.remove('hidden');
}

function hideConvertModal() {
    document.getElementById('convertModal').classList.add('hidden');
}

// Close modal when clicking outside
document.getElementById('convertModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        hideConvertModal();
    }
});
</script>
@endpush
@endsection
