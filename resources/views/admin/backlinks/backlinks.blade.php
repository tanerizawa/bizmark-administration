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
                        <td colspan="8" class="py-0">
                            <div class="text-center py-16 px-6">
                                <!-- Icon -->
                                <div class="mb-6">
                                    <div class="inline-flex items-center justify-center w-20 h-20 rounded-full mb-4" style="background: rgba(10,132,255,0.12);">
                                        <i class="fas fa-link text-3xl" style="color: rgba(10,132,255,1);"></i>
                                    </div>
                                </div>
                                
                                <!-- Message -->
                                <h3 class="text-xl font-bold mb-2" style="color: #FFFFFF;">
                                    Belum Ada Backlinks
                                </h3>
                                <p class="text-sm mb-8 max-w-md mx-auto" style="color: rgba(235,235,245,0.6);">
                                    Anda belum memiliki backlinks yang tercatat. Mulai tambahkan backlinks dengan cara berikut:
                                </p>
                                
                                <!-- Action Cards -->
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 max-w-2xl mx-auto mb-8">
                                    <!-- Manual Add -->
                                    <div class="p-5 rounded-apple text-left" style="background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1);">
                                        <div class="flex items-start">
                                            <div class="flex-shrink-0 w-10 h-10 rounded-apple flex items-center justify-center mr-3" style="background: rgba(48,209,88,0.2);">
                                                <i class="fas fa-plus" style="color: rgba(48,209,88,1);"></i>
                                            </div>
                                            <div>
                                                <h4 class="font-semibold mb-1" style="color: #FFFFFF;">Manual Entry</h4>
                                                <p class="text-xs mb-3" style="color: rgba(235,235,245,0.6);">
                                                    Tambahkan backlink secara manual jika sudah berhasil diperoleh
                                                </p>
                                                <a href="{{ route('admin.backlinks.create') }}" class="inline-flex items-center text-xs font-medium" style="color: rgba(48,209,88,1);">
                                                    <i class="fas fa-plus mr-1"></i>Add Backlink
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Auto Crawl -->
                                    <div class="p-5 rounded-apple text-left" style="background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1);">
                                        <div class="flex items-start">
                                            <div class="flex-shrink-0 w-10 h-10 rounded-apple flex items-center justify-center mr-3" style="background: rgba(191,90,242,0.2);">
                                                <i class="fas fa-robot" style="color: rgba(191,90,242,1);"></i>
                                            </div>
                                            <div>
                                                <h4 class="font-semibold mb-1" style="color: #FFFFFF;">Auto Detection</h4>
                                                <p class="text-xs mb-3" style="color: rgba(235,235,245,0.6);">
                                                    Jalankan crawler untuk deteksi otomatis backlinks di target websites
                                                </p>
                                                <code class="text-xs px-2 py-1 rounded" style="background: rgba(0,0,0,0.3); color: rgba(191,90,242,1);">
                                                    backlink:crawl --all
                                                </code>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Info Box -->
                                <div class="max-w-2xl mx-auto p-4 rounded-apple text-left" style="background: rgba(255,159,10,0.12); border: 1px solid rgba(255,159,10,0.2);">
                                    <div class="flex items-start">
                                        <i class="fas fa-info-circle mr-3 mt-0.5" style="color: rgba(255,159,10,1);"></i>
                                        <div>
                                            <p class="text-sm font-medium mb-1" style="color: rgba(255,159,10,1);">
                                                Workflow untuk mendapatkan backlinks:
                                            </p>
                                            <ol class="text-xs space-y-1" style="color: rgba(235,235,245,0.75);">
                                                <li>1. Tambahkan target websites di <a href="{{ route('admin.backlinks.targets') }}" class="underline">Target Websites</a></li>
                                                <li>2. Kirim outreach email dengan <code class="bg-gray-900 px-1 rounded">backlink:outreach --ai</code></li>
                                                <li>3. Setelah dapat backlink, tambahkan manual atau jalankan crawler untuk auto-detect</li>
                                                <li>4. Monitor kesehatan backlinks dengan <code class="bg-gray-900 px-1 rounded">backlink:monitor</code></li>
                                            </ol>
                                        </div>
                                    </div>
                                </div>
                            </div>
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
