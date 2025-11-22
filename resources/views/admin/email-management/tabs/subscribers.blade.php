<div class="space-y-4">
    {{-- Header Section --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h2 class="text-xl font-semibold text-white">Email Subscribers</h2>
            <p class="text-sm" style="color: rgba(235,235,245,0.6);">
                Kelola daftar subscriber untuk kampanye email marketing
            </p>
        </div>
        <div class="flex items-center gap-2">
            <button class="btn-apple-sm px-4 py-2">
                <i class="fas fa-file-import mr-2"></i>Import
            </button>
            <a href="{{ route('admin.subscribers.create') ?? '#' }}" class="btn-apple-primary-sm px-4 py-2">
                <i class="fas fa-plus mr-2"></i>Add Subscriber
            </a>
        </div>
    </div>

    {{-- Filters --}}
    <div class="flex flex-wrap items-center gap-3">
        <div class="flex-1 min-w-[200px]">
            <input type="text" placeholder="Search subscribers..." 
                   class="input-apple w-full" value="{{ request('search') }}">
        </div>
        <select class="input-apple min-w-[150px]">
            <option value="">All Status</option>
            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
            <option value="unsubscribed" {{ request('status') == 'unsubscribed' ? 'selected' : '' }}>Unsubscribed</option>
            <option value="bounced" {{ request('status') == 'bounced' ? 'selected' : '' }}>Bounced</option>
        </select>
    </div>

    {{-- Subscriber List --}}
    @if(isset($subscribers) && $subscribers->count() > 0)
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b" style="border-color: rgba(235,235,245,0.1);">
                        <th class="text-left py-3 px-4 text-sm font-semibold" style="color: rgba(235,235,245,0.7);">
                            <input type="checkbox" class="rounded">
                        </th>
                        <th class="text-left py-3 px-4 text-sm font-semibold" style="color: rgba(235,235,245,0.7);">Name</th>
                        <th class="text-left py-3 px-4 text-sm font-semibold" style="color: rgba(235,235,245,0.7);">Email</th>
                        <th class="text-left py-3 px-4 text-sm font-semibold" style="color: rgba(235,235,245,0.7);">Status</th>
                        <th class="text-left py-3 px-4 text-sm font-semibold" style="color: rgba(235,235,245,0.7);">Tags</th>
                        <th class="text-left py-3 px-4 text-sm font-semibold" style="color: rgba(235,235,245,0.7);">Subscribed</th>
                        <th class="text-right py-3 px-4 text-sm font-semibold" style="color: rgba(235,235,245,0.7);">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($subscribers as $subscriber)
                        <tr class="border-b hover:bg-white/5 transition-apple" style="border-color: rgba(235,235,245,0.05);">
                            <td class="py-3 px-4">
                                <input type="checkbox" class="rounded">
                            </td>
                            <td class="py-3 px-4">
                                <p class="text-sm font-medium text-white">{{ $subscriber->name }}</p>
                            </td>
                            <td class="py-3 px-4">
                                <p class="text-sm" style="color: rgba(235,235,245,0.7);">{{ $subscriber->email }}</p>
                            </td>
                            <td class="py-3 px-4">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full {{ 
                                    $subscriber->status == 'active' ? 'bg-green-500/20 text-green-400' : 
                                    ($subscriber->status == 'unsubscribed' ? 'bg-red-500/20 text-red-400' : 
                                    'bg-yellow-500/20 text-yellow-400')
                                }}">
                                    {{ ucfirst($subscriber->status) }}
                                </span>
                            </td>
                            <td class="py-3 px-4">
                                @if($subscriber->tags && count($subscriber->tags) > 0)
                                    <div class="flex flex-wrap gap-1">
                                        @foreach(array_slice($subscriber->tags, 0, 2) as $tag)
                                            <span class="px-2 py-0.5 text-xs rounded-full" 
                                                  style="background: rgba(90,200,250,0.15); color: rgba(90,200,250,1);">
                                                {{ $tag }}
                                            </span>
                                        @endforeach
                                        @if(count($subscriber->tags) > 2)
                                            <span class="text-xs" style="color: rgba(235,235,245,0.5);">
                                                +{{ count($subscriber->tags) - 2 }}
                                            </span>
                                        @endif
                                    </div>
                                @endif
                            </td>
                            <td class="py-3 px-4">
                                <p class="text-xs" style="color: rgba(235,235,245,0.5);">
                                    {{ $subscriber->subscribed_at?->format('d M Y') ?? $subscriber->created_at->format('d M Y') }}
                                </p>
                            </td>
                            <td class="py-3 px-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.subscribers.edit', $subscriber->id) ?? '#' }}" 
                                       class="text-sm" style="color: rgba(10,132,255,1);">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button class="text-sm" style="color: rgba(255,69,58,1);">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if(method_exists($subscribers, 'hasPages') && $subscribers->hasPages())
            <div class="mt-6">
                {{ $subscribers->appends(request()->query())->links() }}
            </div>
        @endif
    @else
        <div class="text-center py-12">
            <i class="fas fa-users text-5xl mb-4" style="color: rgba(235,235,245,0.3);"></i>
            <p class="text-lg font-medium text-white mb-2">No Subscribers Yet</p>
            <p class="text-sm mb-4" style="color: rgba(235,235,245,0.6);">
                Start building your email list to send campaigns
            </p>
            <a href="{{ route('admin.subscribers.create') ?? '#' }}" class="btn-apple-primary-sm px-4 py-2">
                <i class="fas fa-plus mr-2"></i>Add Subscriber
            </a>
        </div>
    @endif
</div>
