<div class="space-y-4">
    {{-- Header Section --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h2 class="text-xl font-semibold text-white">Email Campaigns</h2>
            <p class="text-sm" style="color: rgba(235,235,245,0.6);">
                Buat dan kelola kampanye email marketing untuk pelanggan Anda
            </p>
        </div>
        <a href="{{ route('admin.campaigns.create') ?? '#' }}" class="btn-apple-primary-sm px-4 py-2">
            <i class="fas fa-plus mr-2"></i>New Campaign
        </a>
    </div>

    {{-- Filters --}}
    <div class="flex flex-wrap items-center gap-3">
        <div class="flex-1 min-w-[200px]">
            <input type="text" placeholder="Search campaigns..." 
                   class="input-apple w-full" value="{{ request('search') }}">
        </div>
        <select class="input-apple min-w-[150px]">
            <option value="">All Status</option>
            <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
            <option value="scheduled" {{ request('status') == 'scheduled' ? 'selected' : '' }}>Scheduled</option>
            <option value="sending" {{ request('status') == 'sending' ? 'selected' : '' }}>Sending</option>
            <option value="sent" {{ request('status') == 'sent' ? 'selected' : '' }}>Sent</option>
        </select>
    </div>

    {{-- Campaign List --}}
    @if(isset($campaigns) && $campaigns->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach($campaigns as $campaign)
                <div class="card-elevated rounded-apple-lg p-5 hover:bg-opacity-80 transition-apple">
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
                        <button class="text-sm" style="color: rgba(235,235,245,0.6);">
                            <i class="fas fa-ellipsis-h"></i>
                        </button>
                    </div>

                    {{-- Campaign Info --}}
                    <h3 class="text-lg font-semibold text-white mb-2">
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
        <div class="text-center py-12">
            <i class="fas fa-bullhorn text-5xl mb-4" style="color: rgba(235,235,245,0.3);"></i>
            <p class="text-lg font-medium text-white mb-2">No Campaigns Yet</p>
            <p class="text-sm mb-4" style="color: rgba(235,235,245,0.6);">
                Create your first email campaign to reach your subscribers
            </p>
            <a href="{{ route('admin.campaigns.create') ?? '#' }}" class="btn-apple-primary-sm px-4 py-2">
                <i class="fas fa-plus mr-2"></i>Create Campaign
            </a>
        </div>
    @endif
</div>
