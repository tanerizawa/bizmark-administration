@extends('layouts.app')

@section('title', 'Target Websites - Backlink Builder')

@section('content')
<div class="container-fluid px-4 py-6">
    {{-- Hero Header --}}
    <section class="card-elevated rounded-apple-xl p-5 md:p-6 relative overflow-hidden mb-6">
        <!-- Background Gradient Effects -->
        <div class="absolute inset-0 pointer-events-none" aria-hidden="true">
            <div class="w-72 h-72 bg-apple-blue opacity-30 blur-3xl rounded-full absolute -top-16 -right-10"></div>
            <div class="w-48 h-48 bg-apple-orange opacity-20 blur-2xl rounded-full absolute bottom-0 left-10"></div>
        </div>

        <div class="relative space-y-4">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <div class="space-y-2">
                    <p class="text-xs uppercase tracking-[0.4em]" style="color: rgba(235,235,245,0.5);">Backlink Management</p>
                    <h1 class="text-2xl md:text-3xl font-bold" style="color: #FFFFFF;">
                        <i class="fas fa-bullseye mr-2"></i>Target Websites
                    </h1>
                    <p class="text-sm" style="color: rgba(235,235,245,0.75);">
                        Manage and track {{ $targets->total() }} potential backlink sources
                    </p>
                </div>
                <div>
                    <a href="{{ route('backlinks.targets.create') }}" class="inline-flex items-center px-4 py-2.5 bg-apple-blue text-white rounded-apple text-sm font-medium hover:bg-apple-blue-dark transition-apple">
                        <i class="fas fa-plus mr-2"></i>Add New Target
                    </a>
                </div>
            </div>
        </div>
    </section>

    {{-- Filters --}}
    <div class="card-elevated rounded-apple-xl p-5 mb-6">
        <form method="GET" action="{{ route('backlinks.targets') }}">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-xs uppercase tracking-wider mb-2" style="color: rgba(235,235,245,0.6);">Status</label>
                    <select name="status" class="w-full px-4 py-2.5 rounded-apple text-sm font-medium transition-apple" 
                            style="background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); color: white;"
                            onchange="this.form.submit()">
                        <option value="">All Status</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="contacted" {{ request('status') == 'contacted' ? 'selected' : '' }}>Contacted</option>
                        <option value="responded" {{ request('status') == 'responded' ? 'selected' : '' }}>Responded</option>
                        <option value="accepted" {{ request('status') == 'accepted' ? 'selected' : '' }}>Accepted</option>
                        <option value="acquired" {{ request('status') == 'acquired' ? 'selected' : '' }}>Acquired</option>
                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs uppercase tracking-wider mb-2" style="color: rgba(235,235,245,0.6);">Priority</label>
                    <select name="priority" class="w-full px-4 py-2.5 rounded-apple text-sm font-medium transition-apple" 
                            style="background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); color: white;"
                            onchange="this.form.submit()">
                        <option value="">All Priority</option>
                        <option value="high" {{ request('priority') == 'high' ? 'selected' : '' }}>High</option>
                        <option value="medium" {{ request('priority') == 'medium' ? 'selected' : '' }}>Medium</option>
                        <option value="low" {{ request('priority') == 'low' ? 'selected' : '' }}>Low</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs uppercase tracking-wider mb-2" style="color: rgba(235,235,245,0.6);">Type</label>
                    <select name="type" class="w-full px-4 py-2.5 rounded-apple text-sm font-medium transition-apple" 
                            style="background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); color: white;"
                            onchange="this.form.submit()">
                        <option value="">All Types</option>
                        <option value="guest_post" {{ request('type') == 'guest_post' ? 'selected' : '' }}>Guest Post</option>
                        <option value="resource_link" {{ request('type') == 'resource_link' ? 'selected' : '' }}>Resource Link</option>
                        <option value="partnership" {{ request('type') == 'partnership' ? 'selected' : '' }}>Partnership</option>
                        <option value="directory" {{ request('type') == 'directory' ? 'selected' : '' }}>Directory</option>
                        <option value="syndication" {{ request('type') == 'syndication' ? 'selected' : '' }}>Syndication</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs uppercase tracking-wider mb-2" style="color: rgba(235,235,245,0.6);">Search</label>
                    <input type="text" name="search" placeholder="Website name..." value="{{ request('search') }}"
                           class="w-full px-4 py-2.5 rounded-apple text-sm font-medium transition-apple" 
                           style="background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); color: white;">
                </div>
            </div>
        </form>
    </div>

    {{-- Targets List --}}
    <div class="card-elevated rounded-apple-xl p-5">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b" style="border-color: rgba(255,255,255,0.08);">
                        <th class="text-left py-3 px-4 text-xs uppercase tracking-wider" style="color: rgba(235,235,245,0.6);">Website</th>
                        <th class="text-left py-3 px-4 text-xs uppercase tracking-wider" style="color: rgba(235,235,245,0.6);">DA</th>
                        <th class="text-left py-3 px-4 text-xs uppercase tracking-wider" style="color: rgba(235,235,245,0.6);">Category</th>
                        <th class="text-left py-3 px-4 text-xs uppercase tracking-wider" style="color: rgba(235,235,245,0.6);">Type</th>
                        <th class="text-left py-3 px-4 text-xs uppercase tracking-wider" style="color: rgba(235,235,245,0.6);">Priority</th>
                        <th class="text-left py-3 px-4 text-xs uppercase tracking-wider" style="color: rgba(235,235,245,0.6);">Status</th>
                        <th class="text-left py-3 px-4 text-xs uppercase tracking-wider" style="color: rgba(235,235,245,0.6);">Outreach</th>
                        <th class="text-left py-3 px-4 text-xs uppercase tracking-wider" style="color: rgba(235,235,245,0.6);">Backlinks</th>
                        <th class="text-left py-3 px-4 text-xs uppercase tracking-wider" style="color: rgba(235,235,245,0.6);">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($targets as $target)
                    <tr class="border-b transition-apple hover:bg-white hover:bg-opacity-5" style="border-color: rgba(255,255,255,0.05);">
                        <td class="py-3 px-4">
                            <div class="space-y-1">
                                <h3 class="text-sm font-semibold" style="color: white;">{{ $target->website_name }}</h3>
                                <a href="{{ $target->website_url }}" target="_blank" class="text-apple-blue text-xs hover:underline flex items-center">
                                    {{ Str::limit($target->website_url, 35) }}
                                    <i class="fas fa-external-link-alt fa-xs ml-1"></i>
                                </a>
                                @if($target->contact_email)
                                <p class="text-xs flex items-center" style="color: rgba(235,235,245,0.6);">
                                    <i class="fas fa-envelope fa-xs mr-1"></i> {{ $target->contact_email }}
                                </p>
                                @endif
                            </div>
                        </td>
                        <td class="py-3 px-4">
                            <span class="text-xs font-medium px-2 py-1 rounded-apple" 
                                  style="background: {{ $target->domain_authority >= 70 ? 'rgba(48,209,88,0.15)' : ($target->domain_authority >= 50 ? 'rgba(255,159,10,0.15)' : 'rgba(142,142,147,0.15)') }}; 
                                         color: {{ $target->domain_authority >= 70 ? 'rgba(48,209,88,1)' : ($target->domain_authority >= 50 ? 'rgba(255,159,10,1)' : 'rgba(142,142,147,1)') }};">
                                {{ $target->domain_authority ?? 'N/A' }}
                            </span>
                        </td>
                        <td class="py-3 px-4">
                            <span class="text-xs font-medium px-2 py-1 rounded-apple" 
                                  style="background: rgba(255,255,255,0.05); color: rgba(235,235,245,0.9);">
                                {{ $target->category }}
                            </span>
                        </td>
                        <td class="py-3 px-4">
                            <span class="text-xs font-medium px-2 py-1 rounded-apple" 
                                  style="background: rgba(10,132,255,0.15); color: rgba(10,132,255,1);">
                                {{ ucfirst(str_replace('_', ' ', $target->type)) }}
                            </span>
                        </td>
                        <td class="py-3 px-4">
                            <span class="text-xs font-medium px-2 py-1 rounded-apple"
                                  style="background: {{ $target->priority == 'high' ? 'rgba(255,69,58,0.15)' : ($target->priority == 'medium' ? 'rgba(255,159,10,0.15)' : 'rgba(142,142,147,0.15)') }};
                                         color: {{ $target->priority == 'high' ? 'rgba(255,69,58,1)' : ($target->priority == 'medium' ? 'rgba(255,159,10,1)' : 'rgba(142,142,147,1)') }};">
                                {{ ucfirst($target->priority) }}
                            </span>
                        </td>
                        <td class="py-3 px-4">
                            <span class="text-xs font-medium px-2 py-1 rounded-apple"
                                  style="background: {{ $target->status == 'acquired' ? 'rgba(48,209,88,0.15)' : ($target->status == 'pending' ? 'rgba(255,214,10,0.15)' : 'rgba(10,132,255,0.15)') }};
                                         color: {{ $target->status == 'acquired' ? 'rgba(48,209,88,1)' : ($target->status == 'pending' ? 'rgba(255,214,10,1)' : 'rgba(10,132,255,1)') }};">
                                {{ ucfirst($target->status) }}
                            </span>
                        </td>
                        <td class="py-3 px-4">
                            <span class="text-xs font-medium px-2 py-1 rounded-apple" 
                                  style="background: rgba(142,142,147,0.15); color: rgba(142,142,147,1);">
                                {{ $target->outreach_count }}
                            </span>
                        </td>
                        <td class="py-3 px-4">
                            <span class="text-xs font-medium px-2 py-1 rounded-apple" 
                                  style="background: rgba(48,209,88,0.15); color: rgba(48,209,88,1);">
                                {{ $target->backlinks_count }}
                            </span>
                        </td>
                        <td class="py-3 px-4">
                            <div class="flex items-center gap-2">
                                <a href="{{ route('backlinks.targets.edit', $target) }}" 
                                   class="inline-flex items-center px-3 py-1.5 bg-apple-blue text-white rounded-apple text-xs font-medium hover:bg-apple-blue-dark transition-apple">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('backlinks.targets.delete', $target) }}" method="POST" 
                                      onsubmit="return confirm('Delete {{ $target->website_name }}?')" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="inline-flex items-center px-3 py-1.5 rounded-apple text-xs font-medium transition-apple"
                                            style="background: rgba(255,69,58,0.15); color: rgba(255,69,58,1);"
                                            onmouseover="this.style.background='rgba(255,69,58,0.25)'"
                                            onmouseout="this.style.background='rgba(255,69,58,0.15)'">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="py-12 text-center">
                            <div style="color: rgba(235,235,245,0.6);">
                                <i class="fas fa-bullseye fa-3x mb-3 opacity-50"></i>
                                <p class="text-sm mb-2">No targets found</p>
                                <p class="text-xs mb-4">Try adjusting your filters or add a new target</p>
                                <a href="{{ route('backlinks.targets.create') }}" 
                                   class="inline-flex items-center px-4 py-2 bg-apple-blue text-white rounded-apple text-sm font-medium hover:bg-apple-blue-dark transition-apple">
                                    <i class="fas fa-plus mr-2"></i>Add First Target
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($targets->hasPages())
        <div class="mt-6 flex justify-center">
            {{ $targets->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
