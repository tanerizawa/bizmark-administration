<div class="space-y-4">
    {{-- Header Section --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h2 class="text-xl font-semibold text-white">Email Accounts</h2>
            <p class="text-sm" style="color: rgba(235,235,245,0.6);">
                Kelola akun email tim untuk sistem multi-user email management
            </p>
        </div>
        <a href="{{ route('admin.email-accounts.create') ?? '#' }}" class="btn-apple-primary-sm px-4 py-2">
            <i class="fas fa-plus mr-2"></i>Add Account
        </a>
    </div>

    {{-- Filters --}}
    <div class="flex flex-wrap items-center gap-3">
        <div class="flex-1 min-w-[200px]">
            <input type="text" placeholder="Search accounts..." 
                   class="input-apple w-full" value="{{ request('search') }}">
        </div>
        <select class="input-apple min-w-[150px]">
            <option value="">All Types</option>
            <option value="shared" {{ request('type') == 'shared' ? 'selected' : '' }}>Shared</option>
            <option value="personal" {{ request('type') == 'personal' ? 'selected' : '' }}>Personal</option>
        </select>
        <select class="input-apple min-w-[150px]">
            <option value="">All Departments</option>
            <option value="general">General</option>
            <option value="cs">Customer Service</option>
            <option value="sales">Sales</option>
            <option value="support">Technical Support</option>
            <option value="finance">Finance</option>
        </select>
    </div>

    {{-- Account List --}}
    @if(isset($accounts) && $accounts->count() > 0)
        <div class="space-y-3">
            @foreach($accounts as $account)
                <div class="card-elevated rounded-apple-lg p-5 hover:bg-opacity-80 transition-apple">
                    <div class="flex flex-col md:flex-row md:items-center gap-4">
                        {{-- Account Icon --}}
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 rounded-full flex items-center justify-center text-white font-semibold text-lg"
                                 style="background: linear-gradient(135deg, {{ $account->type == 'shared' ? 'rgba(10,132,255,0.8), rgba(30,86,172,0.8)' : 'rgba(52,199,89,0.8), rgba(30,172,86,0.8)' }});">
                                {{ strtoupper(substr($account->email, 0, 1)) }}
                            </div>
                        </div>

                        {{-- Account Info --}}
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 mb-1">
                                <h3 class="text-base font-semibold text-white">
                                    {{ $account->name }}
                                </h3>
                                
                                {{-- Type Badge --}}
                                <span class="px-2 py-0.5 text-xs font-semibold rounded-full {{ 
                                    $account->type == 'shared' ? 'bg-blue-500/20 text-blue-400' : 'bg-green-500/20 text-green-400'
                                }}">
                                    {{ ucfirst($account->type) }}
                                </span>

                                {{-- Status Badge --}}
                                @if($account->is_active)
                                    <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-green-500/20 text-green-400">
                                        Active
                                    </span>
                                @else
                                    <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-red-500/20 text-red-400">
                                        Inactive
                                    </span>
                                @endif

                                {{-- Department Badge --}}
                                <span class="px-2 py-0.5 text-xs rounded-full" 
                                      style="background: rgba(175,82,222,0.15); color: rgba(175,82,222,1);">
                                    {{ $account->department_label }}
                                </span>
                            </div>

                            <p class="text-sm mb-1" style="color: rgba(235,235,245,0.7);">
                                <i class="fas fa-envelope mr-1"></i>{{ $account->email }}
                            </p>

                            @if($account->description)
                                <p class="text-xs" style="color: rgba(235,235,245,0.5);">
                                    {{ $account->description }}
                                </p>
                            @endif

                            {{-- Assigned Users --}}
                            @if($account->activeUsers && $account->activeUsers->count() > 0)
                                <div class="flex items-center gap-2 mt-2">
                                    <span class="text-xs" style="color: rgba(235,235,245,0.5);">Assigned to:</span>
                                    <div class="flex -space-x-2">
                                        @foreach($account->activeUsers->take(3) as $user)
                                            <div class="w-6 h-6 rounded-full flex items-center justify-center text-xs font-semibold text-white border-2"
                                                 style="background: linear-gradient(135deg, rgba(52,199,89,0.8), rgba(30,172,86,0.8)); border-color: var(--dark-elevated);"
                                                 title="{{ $user->name }}">
                                                {{ strtoupper(substr($user->name, 0, 1)) }}
                                            </div>
                                        @endforeach
                                        @if($account->activeUsers->count() > 3)
                                            <div class="w-6 h-6 rounded-full flex items-center justify-center text-xs font-semibold border-2"
                                                 style="background: rgba(255,255,255,0.1); border-color: var(--dark-elevated); color: rgba(235,235,245,0.7);">
                                                +{{ $account->activeUsers->count() - 3 }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        </div>

                        {{-- Statistics --}}
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-3 md:gap-4">
                            <div class="text-center">
                                <p class="text-xs mb-1" style="color: rgba(235,235,245,0.5);">Received</p>
                                <p class="text-base font-semibold text-white">{{ number_format($account->total_received ?? 0) }}</p>
                            </div>
                            <div class="text-center">
                                <p class="text-xs mb-1" style="color: rgba(235,235,245,0.5);">Sent</p>
                                <p class="text-base font-semibold text-white">{{ number_format($account->total_sent ?? 0) }}</p>
                            </div>
                            <div class="text-center">
                                <p class="text-xs mb-1" style="color: rgba(235,235,245,0.5);">Unread</p>
                                <p class="text-base font-semibold text-yellow-400">{{ number_format($account->getUnreadCount() ?? 0) }}</p>
                            </div>
                        </div>

                        {{-- Actions --}}
                        <div class="flex flex-shrink-0 items-center gap-2">
                            <a href="{{ route('admin.email-accounts.edit', $account->id) ?? '#' }}" 
                               class="btn-apple-sm text-sm px-3 py-1.5">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <a href="{{ route('admin.email-accounts.show', $account->id) ?? '#' }}" 
                               class="btn-apple-primary-sm text-sm px-3 py-1.5">
                                <i class="fas fa-eye"></i> View
                            </a>
                        </div>
                    </div>

                    {{-- Auto Reply Indicator --}}
                    @if($account->auto_reply_enabled)
                        <div class="mt-3 pt-3 border-t" style="border-color: rgba(235,235,245,0.1);">
                            <div class="flex items-center gap-2">
                                <i class="fas fa-reply-all text-xs" style="color: rgba(90,200,250,1);"></i>
                                <span class="text-xs" style="color: rgba(235,235,245,0.6);">
                                    Auto-reply enabled
                                </span>
                            </div>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>

        {{-- Pagination --}}
        @if(method_exists($accounts, 'hasPages') && $accounts->hasPages())
            <div class="mt-6">
                {{ $accounts->appends(request()->query())->links() }}
            </div>
        @endif
    @else
        <div class="text-center py-12">
            <i class="fas fa-at text-5xl mb-4" style="color: rgba(235,235,245,0.3);"></i>
            <p class="text-lg font-medium text-white mb-2">No Email Accounts Yet</p>
            <p class="text-sm mb-4" style="color: rgba(235,235,245,0.6);">
                Add email accounts to start managing team emails
            </p>
            <a href="{{ route('admin.email-accounts.create') ?? '#' }}" class="btn-apple-primary-sm px-4 py-2">
                <i class="fas fa-plus mr-2"></i>Add Account
            </a>
        </div>
    @endif
</div>
