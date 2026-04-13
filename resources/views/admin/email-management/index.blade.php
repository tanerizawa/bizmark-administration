@extends('layouts.app')

@section('title', 'Email Management')
@section('page-title', 'Email Management')

@section('content')
<div class="space-y-4 email-shell">
    {{-- Compact Hero Section --}}
    <section class="card-elevated rounded-apple-lg admin-hero relative overflow-hidden compact-email-hero">
        <div class="absolute inset-0 pointer-events-none" aria-hidden="true">
            <div class="w-48 h-48 bg-apple-blue opacity-20 blur-3xl rounded-full absolute -top-12 -right-8"></div>
            <div class="w-40 h-40 rounded-full absolute -bottom-16 left-12" style="background: rgba(175,82,222,0.12); filter: blur(52px);"></div>
        </div>
        <div class="relative flex flex-col lg:flex-row lg:items-end lg:justify-between gap-4">
            <div class="flex-1 min-w-0">
                <p class="admin-hero-subtitle">Pusat Kendali Email</p>
                <h1 class="admin-hero-title text-white">Email Management</h1>
                <p class="admin-hero-desc max-w-2xl">Mailbox, campaign, subscriber, template, account, dan pengaturan email dalam satu workspace operasional yang lebih rapi.</p>
                <div class="admin-hero-meta flex flex-wrap gap-3">
                    <span><i class="fas fa-sync-alt mr-1.5"></i>{{ now()->format('d M Y, H:i') }}</span>
                    <span><i class="fas fa-layer-group mr-1.5"></i>6 modul terintegrasi</span>
                    <span><i class="fas fa-columns mr-1.5"></i>Mailbox kini berjalan penuh di dalam tab</span>
                </div>
            </div>
        </div>
    </section>

    <!-- Compact Summary Statistics -->
    <div id="email-management-summary-stats" class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-6 gap-2.5">
        <article class="admin-stat-card card-elevated rounded-apple flex items-center gap-2" 
                 style="background: rgba(10,132,255,0.1); border: 1px solid rgba(10,132,255,0.2);">
            <div class="admin-stat-icon rounded flex items-center justify-center" style="background: rgba(10,132,255,0.2);">
                <i class="fas fa-envelope text-apple-blue" style="font-size: 0.7rem;"></i>
            </div>
            <div>
                <p class="admin-small text-apple-blue uppercase tracking-wider">Email</p>
                <p class="admin-stat text-white">{{ $totalEmails ?? 0 }}</p>
            </div>
        </article>

        <article class="admin-stat-card card-elevated rounded-apple flex items-center gap-2" 
                 style="background: rgba(255,159,10,0.1); border: 1px solid rgba(255,159,10,0.2);">
            <div class="admin-stat-icon rounded flex items-center justify-center" style="background: rgba(255,159,10,0.2);">
                <i class="fas fa-envelope-open text-apple-orange" style="font-size: 0.7rem;"></i>
            </div>
            <div>
                <p class="admin-small text-apple-orange uppercase tracking-wider">Unread</p>
                <p class="admin-stat text-apple-orange">{{ $unreadEmails ?? 0 }}</p>
            </div>
        </article>

        <article class="admin-stat-card card-elevated rounded-apple flex items-center gap-2" 
                 style="background: rgba(52,199,89,0.1); border: 1px solid rgba(52,199,89,0.2);">
            <div class="admin-stat-icon rounded flex items-center justify-center" style="background: rgba(52,199,89,0.2);">
                <i class="fas fa-bullhorn text-apple-green" style="font-size: 0.7rem;"></i>
            </div>
            <div>
                <p class="admin-small text-apple-green uppercase tracking-wider">Campaign</p>
                <p class="admin-stat text-apple-green">{{ $totalCampaigns ?? 0 }}</p>
            </div>
        </article>

        <article class="admin-stat-card card-elevated rounded-apple flex items-center gap-2" 
                 style="background: rgba(175,82,222,0.1); border: 1px solid rgba(175,82,222,0.2);">
            <div class="admin-stat-icon rounded flex items-center justify-center" style="background: rgba(175,82,222,0.2);">
                <i class="fas fa-users" style="color: #AF52DE; font-size: 0.7rem;"></i>
            </div>
            <div>
                <p class="admin-small uppercase tracking-wider" style="color: #AF52DE;">Subscriber</p>
                <p class="admin-stat text-white">{{ $totalSubscribers ?? 0 }}</p>
            </div>
        </article>

        <article class="admin-stat-card card-elevated rounded-apple flex items-center gap-2" 
                 style="background: rgba(255,55,95,0.1); border: 1px solid rgba(255,55,95,0.2);">
            <div class="admin-stat-icon rounded flex items-center justify-center" style="background: rgba(255,55,95,0.2);">
                <i class="fas fa-file-code" style="color: #FF375F; font-size: 0.7rem;"></i>
            </div>
            <div>
                <p class="admin-small uppercase tracking-wider" style="color: #FF375F;">Template</p>
                <p class="admin-stat text-white">{{ $totalTemplates ?? 0 }}</p>
            </div>
        </article>

        <article class="admin-stat-card card-elevated rounded-apple flex items-center gap-2" 
                 style="background: rgba(90,200,250,0.1); border: 1px solid rgba(90,200,250,0.2);">
            <div class="admin-stat-icon rounded flex items-center justify-center" style="background: rgba(90,200,250,0.2);">
                <i class="fas fa-at" style="color: #5AC8FA; font-size: 0.7rem;"></i>
            </div>
            <div>
                <p class="admin-small uppercase tracking-wider" style="color: #5AC8FA;">Account</p>
                <p class="admin-stat text-white">{{ $totalAccounts ?? 0 }}</p>
            </div>
        </article>
    </div>

    {{-- Tab Navigation --}}
    <section class="card-elevated rounded-apple-lg overflow-hidden compact-tabs-shell">
        <div class="border-b px-3 py-2" style="border-color: var(--dark-separator); background: rgba(255,255,255,0.02);">
            <div class="flex items-center justify-between gap-3 flex-wrap mb-2">
                <div>
                    <p class="admin-small uppercase tracking-[0.28em] text-dark-text-tertiary">Workspace</p>
                    <p class="text-sm text-white font-semibold">Operasional Email</p>
                </div>
                <div class="text-xs" style="color: rgba(235,235,245,0.6);">
                    Pindah tab tanpa reload penuh dan filter tetap terisolasi per modul.
                </div>
            </div>
            <div class="flex gap-1.5 overflow-x-auto pb-0.5" role="tablist">
                <button onclick="switchTab('inbox')" id="tab-inbox" 
                        class="tab-button whitespace-nowrap {{ $activeTab == 'inbox' ? 'active' : '' }}">
                    <i class="fas fa-inbox"></i>Mailbox
                    @if(($notifications['inbox'] ?? 0) > 0)
                        <span class="admin-badge ml-1" style="background: rgba(255,204,0,0.25); color: #FFD60A;">{{ $notifications['inbox'] }}</span>
                    @endif
                </button>
                <button onclick="switchTab('campaigns')" id="tab-campaigns"
                        class="tab-button {{ $activeTab == 'campaigns' ? 'active' : '' }}">
                    <i class="fas fa-bullhorn"></i>Campaigns
                    @if(($notifications['campaigns'] ?? 0) > 0)
                        <span class="admin-badge ml-1" style="background: rgba(10,132,255,0.25); color: #0A84FF;">{{ $notifications['campaigns'] }}</span>
                    @endif
                </button>
                <button onclick="switchTab('subscribers')" id="tab-subscribers"
                        class="tab-button {{ $activeTab == 'subscribers' ? 'active' : '' }}">
                    <i class="fas fa-users"></i>Subscribers
                    @if(($notifications['subscribers'] ?? 0) > 0)
                        <span class="admin-badge ml-1" style="background: rgba(52,199,89,0.25); color: #34C759;">{{ $notifications['subscribers'] }}</span>
                    @endif
                </button>
                <button onclick="switchTab('templates')" id="tab-templates"
                        class="tab-button {{ $activeTab == 'templates' ? 'active' : '' }}">
                    <i class="fas fa-file-code"></i>Templates
                </button>
                <button onclick="switchTab('settings')" id="tab-settings"
                        class="tab-button {{ $activeTab == 'settings' ? 'active' : '' }}">
                    <i class="fas fa-cog"></i>Settings
                </button>
                <button onclick="switchTab('accounts')" id="tab-accounts"
                        class="tab-button {{ $activeTab == 'accounts' ? 'active' : '' }}">
                    <i class="fas fa-at"></i>Accounts
                </button>
            </div>
        </div>

        <div class="p-3 md:p-4">
            <!-- Tab 1: Inbox -->
            <div id="content-inbox" class="tab-content {{ $activeTab !== 'inbox' ? 'hidden' : '' }}">
                @include('admin.email-management.tabs.inbox')
            </div>

            <!-- Tab 2: Campaigns -->
            <div id="content-campaigns" class="tab-content {{ $activeTab !== 'campaigns' ? 'hidden' : '' }}">
                @include('admin.email-management.tabs.campaigns')
            </div>

            <!-- Tab 3: Subscribers -->
            <div id="content-subscribers" class="tab-content {{ $activeTab !== 'subscribers' ? 'hidden' : '' }}">
                @include('admin.email-management.tabs.subscribers')
            </div>

            <!-- Tab 4: Templates -->
            <div id="content-templates" class="tab-content {{ $activeTab !== 'templates' ? 'hidden' : '' }}">
                @include('admin.email-management.tabs.templates')
            </div>

            <!-- Tab 5: Settings -->
            <div id="content-settings" class="tab-content {{ $activeTab !== 'settings' ? 'hidden' : '' }}">
                @include('admin.email-management.tabs.settings')
            </div>

            <!-- Tab 6: Accounts -->
            <div id="content-accounts" class="tab-content {{ $activeTab !== 'accounts' ? 'hidden' : '' }}">
                @include('admin.email-management.tabs.accounts')
            </div>
        </div>
    </section>
@endsection

@push('styles')
<style>
    .email-shell .compact-email-hero {
        padding: 1rem 1.1rem;
    }

    .email-shell .glance-chip {
        padding: 0.65rem 0.8rem;
        border-radius: 0.9rem;
        background: rgba(255,255,255,0.06);
        border: 1px solid rgba(255,255,255,0.08);
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.75rem;
        min-height: 44px;
    }

    .email-shell .glance-chip span {
        font-size: 0.71rem;
        letter-spacing: 0.04em;
        color: rgba(235,235,245,0.62);
    }

    .email-shell .glance-chip strong {
        font-size: 0.95rem;
        color: #fff;
        font-weight: 700;
    }

    .email-shell .admin-stat-card {
        min-height: 72px;
    }

    .email-shell .tab-button {
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
        padding: 0.55rem 0.8rem;
        border-radius: 0.85rem;
        font-size: 0.78rem;
        font-weight: 600;
        color: rgba(235, 235, 245, 0.6);
        background-color: transparent;
        border: 1px solid transparent;
        transition: all 0.2s ease;
    }

    .email-shell .tab-button:hover {
        color: rgba(235, 235, 245, 0.9);
        background-color: rgba(255, 255, 255, 0.05);
    }

    .email-shell .tab-button.active {
        color: #FFFFFF;
        background-color: rgba(0, 122, 255, 0.15);
        border: 1px solid rgba(0, 122, 255, 0.3);
        box-shadow: inset 0 0 0 1px rgba(255,255,255,0.03);
    }

    .email-shell .tab-content {
        animation: fadeIn 0.3s ease-in;
    }

    .email-shell .email-panel-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 0.75rem;
        flex-wrap: wrap;
    }

    .email-shell .email-toolbar {
        padding: 0.9rem;
        border-radius: 1rem;
        background: rgba(255,255,255,0.03);
        border: 1px solid rgba(255,255,255,0.06);
    }

    .email-shell .email-toolbar-grid {
        display: grid;
        grid-template-columns: minmax(0, 1.6fr) repeat(2, minmax(140px, 0.8fr)) auto auto;
        gap: 0.65rem;
        align-items: end;
    }

    .email-shell .email-toolbar-grid.compact-4 {
        grid-template-columns: minmax(0, 1.7fr) minmax(160px, 0.9fr) auto auto;
    }

    .email-shell .email-toolbar-grid.compact-3 {
        grid-template-columns: minmax(0, 1.7fr) auto auto;
    }

    .email-shell .email-filter label {
        display: block;
        margin-bottom: 0.35rem;
        font-size: 0.72rem;
        font-weight: 600;
        letter-spacing: 0.04em;
        color: rgba(235,235,245,0.62);
    }

    .email-shell .email-table-shell {
        border: 1px solid rgba(255,255,255,0.06);
        background: rgba(255,255,255,0.02);
    }

    .email-shell .email-empty-state {
        padding: 2.5rem 1rem;
        text-align: center;
    }

    .email-shell .email-empty-state i,
    .email-shell .email-empty-state .empty-icon {
        font-size: 2rem;
        margin-bottom: 0.75rem;
        color: rgba(235,235,245,0.28);
    }

    @media (max-width: 1024px) {
        .email-shell .email-toolbar-grid,
        .email-shell .email-toolbar-grid.compact-4,
        .email-shell .email-toolbar-grid.compact-3 {
            grid-template-columns: 1fr 1fr;
        }
    }

    @media (max-width: 640px) {
        .email-shell .email-toolbar-grid,
        .email-shell .email-toolbar-grid.compact-4,
        .email-shell .email-toolbar-grid.compact-3 {
            grid-template-columns: 1fr;
        }
    }
    
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>
@endpush

@push('scripts')
<script>
function switchTab(tabName, updateHistory = true) {
    if (updateHistory) {
        const url = new URL(window.location);
        url.searchParams.set('tab', tabName);
        window.history.pushState({}, '', url);
    }
    
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
    const tab = urlParams.get('tab') || 'inbox';
    switchTab(tab, false);
});
</script>
@endpush
