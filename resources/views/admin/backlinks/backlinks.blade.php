@extends('layouts.app')

@section('title', 'Backlinks Management')

@section('content')
<div class="container-custom">
    {{-- Hero Header --}}
    <section class="card-apple p-5 md:p-6 relative overflow-hidden mb-6">
        <!-- Background Gradient Effects -->
        <div class="absolute inset-0 pointer-events-none" aria-hidden="true">
            <div class="w-72 h-72 bg-apple-green opacity-30 blur-3xl rounded-full absolute -top-16 -right-10"></div>
            <div class="w-48 h-48 bg-apple-orange opacity-20 blur-2xl rounded-full absolute bottom-0 left-10"></div>
        </div>

        <div class="relative space-y-4">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <div class="space-y-2">
                    <p class="text-xs uppercase tracking-[0.4em]" style="color: rgba(235,235,245,0.5);">Link Management</p>
                    <h1 class="text-2xl md:text-3xl font-bold" style="color: #FFFFFF;">
                        <i class="fas fa-link mr-2"></i>Acquired Backlinks
                    </h1>
                    <p class="text-sm" style="color: rgba(235,235,245,0.75);">
                        Kelola dan monitor semua backlinks yang telah berhasil diperoleh
                    </p>
                </div>
                <div>
                    <a href="{{ route('admin.backlinks.create') }}" class="inline-flex items-center px-4 py-2.5 bg-apple-blue text-white rounded-apple text-sm font-medium hover:bg-apple-blue-dark transition-apple">
                        <i class="fas fa-plus mr-2"></i>Add Backlink
                    </a>
                </div>
            </div>
        </div>
    </section>

    {{-- Filters --}}
    <div class="card-apple p-5 mb-5">
        <form action="{{ route('admin.backlinks.list') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="label-apple">Search</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="URL, anchor text..." class="input-apple">
            </div>
            <div>
                <label class="label-apple">Status</label>
                <select name="status" class="input-apple">
                    <option value="">All Status</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="indexed" {{ request('status') == 'indexed' ? 'selected' : '' }}>Indexed</option>
                    <option value="broken" {{ request('status') == 'broken' ? 'selected' : '' }}>Broken</option>
                    <option value="removed" {{ request('status') == 'removed' ? 'selected' : '' }}>Removed</option>
                </select>
            </div>
            <div>
                <label class="label-apple">Type</label>
                <select name="type" class="input-apple">
                    <option value="">All Types</option>
                    <option value="dofollow" {{ request('type') == 'dofollow' ? 'selected' : '' }}>Dofollow</option>
                    <option value="nofollow" {{ request('type') == 'nofollow' ? 'selected' : '' }}>Nofollow</option>
                </select>
            </div>
            <div class="flex items-end">
                <button type="submit" class="w-full btn-primary-apple">
                    <i class="fas fa-filter mr-2"></i>Filter
                </button>
            </div>
        </form>
    </div>

    {{-- Backlinks Table --}}
    <div class="card-apple">
        <div class="table-responsive">
            <table class="table-apple">
                <thead>
                    <tr>
                        <th>Source URL</th>
                        <th>Target URL</th>
                        <th>Anchor Text</th>
                        <th>Type</th>
                        <th>DA</th>
                        <th>Status</th>
                        <th>Acquired</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($backlinks as $backlink)
                    <tr>
                        <td>
                            <a href="{{ $backlink->source_url }}" target="_blank" class="text-apple-blue hover:underline text-sm">
                                {{ Str::limit($backlink->source_url, 40) }}
                                <i class="fas fa-external-link-alt ml-1 text-xs"></i>
                            </a>
                        </td>
                        <td>
                            <a href="{{ $backlink->target_url }}" target="_blank" class="text-dark-text-secondary hover:text-apple-blue text-sm">
                                {{ Str::limit($backlink->target_url, 40) }}
                            </a>
                        </td>
                        <td class="text-sm text-dark-text-primary">{{ $backlink->anchor_text }}</td>
                        <td>
                            @if($backlink->type == 'dofollow')
                            <span class="badge-apple" style="background: rgba(48,209,88,0.15); color: var(--apple-green);">
                                Dofollow
                            </span>
                            @else
                            <span class="badge-apple" style="background: rgba(142,142,147,0.15); color: var(--dark-text-secondary);">
                                Nofollow
                            </span>
                            @endif
                        </td>
                        <td>
                            <span class="badge-apple" style="background: rgba(10,132,255,0.15); color: var(--apple-blue);">
                                {{ $backlink->domain_authority ?? 'N/A' }}
                            </span>
                        </td>
                        <td>
                            @if($backlink->status == 'indexed')
                            <span class="badge-apple" style="background: rgba(48,209,88,0.15); color: var(--apple-green);">
                                <i class="fas fa-check-circle mr-1"></i>Indexed
                            </span>
                            @elseif($backlink->status == 'active')
                            <span class="badge-apple" style="background: rgba(10,132,255,0.15); color: var(--apple-blue);">
                                Active
                            </span>
                            @elseif($backlink->status == 'broken')
                            <span class="badge-apple" style="background: rgba(255,69,58,0.15); color: var(--apple-red);">
                                <i class="fas fa-unlink mr-1"></i>Broken
                            </span>
                            @else
                            <span class="badge-apple" style="background: rgba(142,142,147,0.15); color: var(--dark-text-secondary);">
                                {{ ucfirst($backlink->status) }}
                            </span>
                            @endif
                        </td>
                        <td class="text-sm text-dark-text-secondary">
                            {{ $backlink->acquired_at ? $backlink->acquired_at->format('d M Y') : '-' }}
                        </td>
                        <td>
                            <div class="flex gap-2">
                                <a href="{{ route('admin.backlinks.edit', $backlink->id) }}" class="btn-icon-apple">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.backlinks.delete', $backlink->id) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-icon-apple text-apple-red" onclick="return confirm('Delete this backlink?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-12 text-dark-text-tertiary">
                            <i class="fas fa-link text-4xl mb-3 opacity-50"></i>
                            <p>No backlinks found</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($backlinks->hasPages())
        <div class="p-5 border-t" style="border-color: var(--dark-separator);">
            {{ $backlinks->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
