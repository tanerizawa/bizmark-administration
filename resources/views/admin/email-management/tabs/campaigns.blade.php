<div class="space-y-4">
    {{-- Header Section --}}
    <div class="email-panel-header">
        <div>
            <h2 class="text-base font-semibold text-white">Email Campaigns</h2>
            <p class="text-sm" style="color: rgba(235,235,245,0.6);">
                Buat dan kelola kampanye email marketing untuk pelanggan Anda
            </p>
        </div>
        <a href="{{ route('admin.campaigns.create') ?? '#' }}" class="btn-apple-primary-sm px-3 py-2">
            <i class="fas fa-plus mr-2"></i>New Campaign
        </a>
    </div>

    {{-- Filters --}}
    <form method="GET" action="{{ route('admin.email-management.index') }}" class="email-toolbar">
        <input type="hidden" name="tab" value="campaigns">
        <div class="email-toolbar-grid compact-4">
            <div class="email-filter">
                <label for="campaign-search">Pencarian</label>
                <input id="campaign-search" type="text" name="search" placeholder="Nama campaign atau subject..." 
                       class="input-apple w-full" value="{{ request('tab') === 'campaigns' ? request('search') : '' }}">
            </div>
            <div class="email-filter">
                <label for="campaign-status">Status</label>
                <select id="campaign-status" name="status" class="input-apple w-full">
                    <option value="">Semua Status</option>
                    @foreach(($statuses ?? []) as $status)
                        <option value="{{ $status }}" {{ request('tab') === 'campaigns' && request('status') === $status ? 'selected' : '' }}>{{ ucfirst($status) }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn-apple-primary-sm px-4 py-2">
                <i class="fas fa-filter mr-2"></i>Filter
            </button>
            <a href="{{ route('admin.email-management.index', ['tab' => 'campaigns']) }}" class="btn-apple-sm px-4 py-2">
                Reset
            </a>
        </div>
    </form>

    {{-- Campaign List --}}
    @if(isset($campaigns) && $campaigns->count() > 0)
        <div class="grid grid-cols-1 xl:grid-cols-2 gap-3">
            @foreach($campaigns as $campaign)
            <div class="card-elevated rounded-apple-lg p-4 hover:bg-opacity-80 transition-apple email-table-shell">
                    {{-- Status Badge --}}
                    <div class="flex items-center justify-between mb-3">
                        <span class="px-3 py-1 text-xs font-semibold rounded-full {{ 
                            $campaign->status == 'sent' ? 'bg-green-500/20 text-green-400' : 
                            ($campaign->status == 'scheduled' ? 'bg-blue-500/20 text-blue-400' : 
                            ($campaign->status == 'sending' ? 'bg-yellow-500/20 text-yellow-400' : 
                            'bg-gray-500/20 text-gray-400'))
                        }}">
                            {{ ucfirst($campaign->status) }}
                        </span>
                        <span class="text-xs" style="color: rgba(235,235,245,0.5);">
                            ID #{{ $campaign->id }}
                        </span>
                    </div>

                    {{-- Campaign Info --}}
                    <h3 class="text-sm font-semibold text-white mb-2">
                        {{ $campaign->name }}
                    </h3>
                    <p class="text-sm mb-3" style="color: rgba(235,235,245,0.7);">
                        {{ $campaign->subject }}
                    </p>

                    {{-- Statistics --}}
                    @if($campaign->status == 'sent')
                        <div class="grid grid-cols-4 gap-2 py-3 mb-3 border-t border-b" style="border-color: rgba(235,235,245,0.1);">
                            <div class="text-center">
                                <p class="text-xs" style="color: rgba(235,235,245,0.5);">Sent</p>
                                <p class="text-sm font-semibold text-white">{{ number_format($campaign->sent_count) }}</p>
                            </div>
                            <div class="text-center">
                                <p class="text-xs" style="color: rgba(235,235,245,0.5);">Opened</p>
                                <p class="text-sm font-semibold text-green-400">{{ $campaign->open_rate }}%</p>
                            </div>
                            <div class="text-center">
                                <p class="text-xs" style="color: rgba(235,235,245,0.5);">Clicked</p>
                                <p class="text-sm font-semibold text-blue-400">{{ $campaign->click_rate }}%</p>
                            </div>
                            <div class="text-center">
                                <p class="text-xs" style="color: rgba(235,235,245,0.5);">Bounced</p>
                                <p class="text-sm font-semibold text-red-400">{{ $campaign->bounce_rate }}%</p>
                            </div>
                        </div>
                    @endif

                    {{-- Actions --}}
                    <div class="flex items-center justify-between">
                        <p class="text-xs" style="color: rgba(235,235,245,0.5);">
                            <i class="far fa-calendar mr-1"></i>
                            {{ $campaign->scheduled_at ? $campaign->scheduled_at->format('d M Y H:i') : $campaign->created_at->format('d M Y') }}
                        </p>
                        <div class="flex gap-2">
                            @if($campaign->status == 'draft')
                                <a href="{{ route('admin.campaigns.edit', $campaign->id) ?? '#' }}" 
                                   class="btn-apple-sm text-xs px-3 py-1">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                            @endif
                            <a href="{{ route('admin.campaigns.show', $campaign->id) ?? '#' }}" 
                               class="btn-apple-primary-sm text-xs px-3 py-1">
                                <i class="fas fa-eye"></i> View
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Pagination --}}
        @if(method_exists($campaigns, 'hasPages') && $campaigns->hasPages())
            <div class="mt-6">
                {{ $campaigns->appends(request()->query())->links() }}
            </div>
        @endif
    @else
        <div class="email-empty-state">
            <i class="fas fa-bullhorn"></i>
            <p class="text-base font-medium text-white mb-2">No Campaigns Yet</p>
            <p class="text-sm mb-4" style="color: rgba(235,235,245,0.6);">
                Create your first email campaign to reach your subscribers
            </p>
            <a href="{{ route('admin.campaigns.create') ?? '#' }}" class="btn-apple-primary-sm px-4 py-2">
                <i class="fas fa-plus mr-2"></i>Create Campaign
            </a>
        </div>
    @endif
</div>
