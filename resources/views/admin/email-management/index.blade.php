@extends('layouts.app')

@section('title', 'Email Management')
@section('page-title', 'Email Management')

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
                    <p class="text-sm uppercase tracking-[0.4em]" style="color: rgba(235,235,245,0.5);">Pusat Kendali Email</p>
                    <h1 class="text-2xl md:text-3xl font-bold" style="color: #FFFFFF;">
                        Email Management Terpadu
                    </h1>
                    <p class="text-sm md:text-base" style="color: rgba(235,235,245,0.75);">
                        Kelola inbox, campaigns, subscribers, dan email templates dalam satu platform terintegrasi.
                    </p>
                </div>
                <div class="space-y-2.5 text-sm" style="color: rgba(235,235,245,0.65);">
                    <p><i class="fas fa-sync-alt mr-2"></i>Diperbarui: {{ now()->locale('id')->isoFormat('D MMM Y, HH:mm') }}</p>
                    <p><i class="fas fa-envelope mr-2"></i>Sistem Email Terintegrasi</p>
                </div>
            </div>

            <!-- Summary Statistics -->
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3 md:gap-4">
                <div class="rounded-apple-lg p-3.5 md:p-4" style="background: rgba(10,132,255,0.12);">
                    <p class="text-xs uppercase tracking-widest" style="color: rgba(10,132,255,0.9);">Total Email</p>
                    <h2 class="text-2xl font-bold mt-1.5" style="color: #FFFFFF;">
                        {{ $totalEmails ?? 0 }}
                    </h2>
                    <p class="text-xs" style="color: rgba(235,235,245,0.6);">Semua pesan</p>
                </div>

                <div class="rounded-apple-lg p-3.5 md:p-4" style="background: rgba(255,159,10,0.12);">
                    <p class="text-xs uppercase tracking-widest" style="color: rgba(255,159,10,0.9);">Belum Dibaca</p>
                    <h2 class="text-2xl font-bold mt-1.5" style="color: rgba(255,159,10,1);">
                        {{ $unreadEmails ?? 0 }}
                    </h2>
                    <p class="text-xs" style="color: rgba(235,235,245,0.6);">Inbox baru</p>
                </div>

                <div class="rounded-apple-lg p-3.5 md:p-4" style="background: rgba(52,199,89,0.12);">
                    <p class="text-xs uppercase tracking-widest" style="color: rgba(52,199,89,0.9);">Campaigns</p>
                    <h2 class="text-2xl font-bold mt-1.5" style="color: rgba(52,199,89,1);">
                        {{ $totalCampaigns ?? 0 }}
                    </h2>
                    <p class="text-xs" style="color: rgba(235,235,245,0.6);">Total kampanye</p>
                </div>

                <div class="rounded-apple-lg p-3.5 md:p-4" style="background: rgba(175,82,222,0.12);">
                    <p class="text-xs uppercase tracking-widest" style="color: rgba(175,82,222,0.9);">Subscribers</p>
                    <h2 class="text-2xl font-bold mt-1.5" style="color: #FFFFFF;">
                        {{ $totalSubscribers ?? 0 }}
                    </h2>
                    <p class="text-xs" style="color: rgba(235,235,245,0.6);">Aktif</p>
                </div>

                <div class="rounded-apple-lg p-3.5 md:p-4" style="background: rgba(255,55,95,0.12);">
                    <p class="text-xs uppercase tracking-widest" style="color: rgba(255,55,95,0.9);">Templates</p>
                    <h2 class="text-2xl font-bold mt-1.5" style="color: #FFFFFF;">
                        {{ $totalTemplates ?? 0 }}
                    </h2>
                    <p class="text-xs" style="color: rgba(235,235,245,0.6);">Template siap</p>
                </div>

                <div class="rounded-apple-lg p-3.5 md:p-4" style="background: rgba(90,200,250,0.12);">
                    <p class="text-xs uppercase tracking-widest" style="color: rgba(90,200,250,0.9);">Accounts</p>
                    <h2 class="text-2xl font-bold mt-1.5" style="color: #FFFFFF;">
                        {{ $totalAccounts ?? 0 }}
                    </h2>
                    <p class="text-xs" style="color: rgba(235,235,245,0.6);">Email aktif</p>
                </div>
            </div>
        </div>
    </section>

    {{-- Tab Navigation --}}
    <section class="card-elevated rounded-apple-xl overflow-hidden mb-5">
        <div class="border-b" style="border-color: var(--dark-separator);">
            <div class="flex space-x-1 p-2 overflow-x-auto" role="tablist">
                <button onclick="switchTab('inbox')" id="tab-inbox" 
                        class="tab-button {{ $activeTab == 'inbox' ? 'active' : '' }} px-4 py-2.5 rounded-apple text-sm font-medium transition-apple whitespace-nowrap">
                    <i class="fas fa-inbox mr-2"></i>Inbox
                    @if(($notifications['inbox'] ?? 0) > 0)
                        <span class="ml-2 px-2 py-0.5 text-xs font-semibold rounded-full {{ $activeTab == 'inbox' ? 'bg-white text-apple-blue' : 'bg-yellow-500 text-white' }}">
                            {{ $notifications['inbox'] }}
                        </span>
                    @endif
                </button>
                <button onclick="switchTab('campaigns')" id="tab-campaigns"
                        class="tab-button {{ $activeTab == 'campaigns' ? 'active' : '' }} px-4 py-2.5 rounded-apple text-sm font-medium transition-apple whitespace-nowrap">
                    <i class="fas fa-bullhorn mr-2"></i>Campaigns
                    @if(($notifications['campaigns'] ?? 0) > 0)
                        <span class="ml-2 px-2 py-0.5 text-xs font-semibold rounded-full {{ $activeTab == 'campaigns' ? 'bg-white text-apple-blue' : 'bg-blue-500 text-white' }}">
                            {{ $notifications['campaigns'] }}
                        </span>
                    @endif
                </button>
                <button onclick="switchTab('subscribers')" id="tab-subscribers"
                        class="tab-button {{ $activeTab == 'subscribers' ? 'active' : '' }} px-4 py-2.5 rounded-apple text-sm font-medium transition-apple whitespace-nowrap">
                    <i class="fas fa-users mr-2"></i>Subscribers
                    @if(($notifications['subscribers'] ?? 0) > 0)
                        <span class="ml-2 px-2 py-0.5 text-xs font-semibold rounded-full {{ $activeTab == 'subscribers' ? 'bg-white text-apple-blue' : 'bg-green-500 text-white' }}">
                            {{ $notifications['subscribers'] }}
                        </span>
                    @endif
                </button>
                <button onclick="switchTab('templates')" id="tab-templates"
                        class="tab-button {{ $activeTab == 'templates' ? 'active' : '' }} px-4 py-2.5 rounded-apple text-sm font-medium transition-apple whitespace-nowrap">
                    <i class="fas fa-file-code mr-2"></i>Templates
                </button>
                <button onclick="switchTab('settings')" id="tab-settings"
                        class="tab-button {{ $activeTab == 'settings' ? 'active' : '' }} px-4 py-2.5 rounded-apple text-sm font-medium transition-apple whitespace-nowrap">
                    <i class="fas fa-cog mr-2"></i>Email Settings
                </button>
                <button onclick="switchTab('accounts')" id="tab-accounts"
                        class="tab-button {{ $activeTab == 'accounts' ? 'active' : '' }} px-4 py-2.5 rounded-apple text-sm font-medium transition-apple whitespace-nowrap">
                    <i class="fas fa-at mr-2"></i>Email Accounts
                </button>
            </div>
        </div>

        <div class="p-6">
            <!-- Tab 1: Inbox -->
            @if($activeTab === 'inbox')
                <div id="content-inbox" class="tab-content">
                    @include('admin.email-management.tabs.inbox')
                </div>
            @else
                <div id="content-inbox" class="tab-content hidden"></div>
            @endif

            <!-- Tab 2: Campaigns -->
            @if($activeTab === 'campaigns')
                <div id="content-campaigns" class="tab-content">
                    @include('admin.email-management.tabs.campaigns')
                </div>
            @else
                <div id="content-campaigns" class="tab-content hidden"></div>
            @endif

            <!-- Tab 3: Subscribers -->
            @if($activeTab === 'subscribers')
                <div id="content-subscribers" class="tab-content">
                    @include('admin.email-management.tabs.subscribers')
                </div>
            @else
                <div id="content-subscribers" class="tab-content hidden"></div>
            @endif

            <!-- Tab 4: Templates -->
            @if($activeTab === 'templates')
                <div id="content-templates" class="tab-content">
                    @include('admin.email-management.tabs.templates')
                </div>
            @else
                <div id="content-templates" class="tab-content hidden"></div>
            @endif

            <!-- Tab 5: Settings -->
            @if($activeTab === 'settings')
                <div id="content-settings" class="tab-content">
                    @include('admin.email-management.tabs.settings')
                </div>
            @else
                <div id="content-settings" class="tab-content hidden"></div>
            @endif

            <!-- Tab 6: Accounts -->
            @if($activeTab === 'accounts')
                <div id="content-accounts" class="tab-content">
                    @include('admin.email-management.tabs.accounts')
                </div>
            @else
                <div id="content-accounts" class="tab-content hidden"></div>
            @endif
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
    const tab = urlParams.get('tab') || 'inbox';
    switchTab(tab);
});
</script>
@endpush
