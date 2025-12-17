@extends('layouts.app')

@section('title', 'Content Syndication')

@section('content')
<div class="container-custom">
    {{-- Hero Header --}}
    <section class="card-apple p-5 md:p-6 relative overflow-hidden mb-6">
        <!-- Background Gradient Effects -->
        <div class="absolute inset-0 pointer-events-none" aria-hidden="true">
            <div class="w-72 h-72 bg-apple-green opacity-30 blur-3xl rounded-full absolute -top-16 -right-10"></div>
            <div class="w-48 h-48 bg-apple-blue opacity-20 blur-2xl rounded-full absolute bottom-0 left-10"></div>
        </div>

        <div class="relative space-y-4">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <div class="space-y-2">
                    <p class="text-xs uppercase tracking-[0.4em]" style="color: rgba(235,235,245,0.5);">Content Distribution</p>
                    <h1 class="text-2xl md:text-3xl font-bold" style="color: #FFFFFF;">
                        <i class="fas fa-share-alt mr-2"></i>Content Syndication
                    </h1>
                    <p class="text-sm" style="color: rgba(235,235,245,0.75);">
                        Artikel yang disindikasikan ke platform eksternal untuk memperluas jangkauan
                    </p>
                </div>
                <div>
                    <a href="{{ route('admin.backlinks.index') }}" class="inline-flex items-center px-4 py-2.5 bg-gray-700 hover:bg-gray-600 text-white rounded-apple text-sm font-medium transition-apple">
                        <i class="fas fa-arrow-left mr-2"></i>Back to Dashboard
                    </a>
                </div>
            </div>
        </div>
    </section>

    {{-- Filters --}}
    <div class="card-apple p-5 mb-5">
        <form action="{{ route('admin.backlinks.syndication') }}" method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="label-apple">Platform</label>
                <select name="platform" class="input-apple">
                    <option value="">All Platforms</option>
                    <option value="medium" {{ request('platform') == 'medium' ? 'selected' : '' }}>Medium</option>
                    <option value="dev.to" {{ request('platform') == 'dev.to' ? 'selected' : '' }}>Dev.to</option>
                    <option value="hashnode" {{ request('platform') == 'hashnode' ? 'selected' : '' }}>Hashnode</option>
                </select>
            </div>
            <div>
                <label class="label-apple">Status</label>
                <select name="status" class="input-apple">
                    <option value="">All Status</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="published" {{ request('status') == 'published' ? 'selected' : '' }}>Published</option>
                    <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Failed</option>
                </select>
            </div>
            <div class="flex items-end">
                <button type="submit" class="w-full btn-primary-apple">
                    <i class="fas fa-filter mr-2"></i>Filter
                </button>
            </div>
        </form>
    </div>

    {{-- Syndication Table --}}
    <div class="card-apple">
        <div class="table-responsive">
            <table class="table-apple">
                <thead>
                    <tr>
                        <th>Article</th>
                        <th>Platform</th>
                        <th>Platform URL</th>
                        <th>Status</th>
                        <th>Published</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($syndications as $syndication)
                    <tr>
                        <td>
                            @if($syndication->article)
                            <a href="{{ route('articles.show', $syndication->article->id) }}" class="text-apple-blue hover:underline text-sm">
                                {{ Str::limit($syndication->article->title, 50) }}
                            </a>
                            @else
                            <span class="text-dark-text-tertiary">Article deleted</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge-apple" style="background: rgba(10,132,255,0.15); color: var(--apple-blue);">
                                {{ ucfirst($syndication->platform) }}
                            </span>
                        </td>
                        <td>
                            @if($syndication->platform_url)
                            <a href="{{ $syndication->platform_url }}" target="_blank" class="text-apple-blue hover:underline text-sm">
                                View <i class="fas fa-external-link-alt ml-1 text-xs"></i>
                            </a>
                            @else
                            <span class="text-dark-text-tertiary">-</span>
                            @endif
                        </td>
                        <td>
                            @if($syndication->status == 'published')
                            <span class="badge-apple" style="background: rgba(48,209,88,0.15); color: var(--apple-green);">
                                <i class="fas fa-check-circle mr-1"></i>Published
                            </span>
                            @elseif($syndication->status == 'pending')
                            <span class="badge-apple" style="background: rgba(255,159,10,0.15); color: var(--apple-orange);">
                                <i class="fas fa-clock mr-1"></i>Pending
                            </span>
                            @else
                            <span class="badge-apple" style="background: rgba(255,69,58,0.15); color: var(--apple-red);">
                                <i class="fas fa-times-circle mr-1"></i>Failed
                            </span>
                            @endif
                        </td>
                        <td class="text-sm text-dark-text-secondary">
                            {{ $syndication->published_at ? $syndication->published_at->format('d M Y H:i') : '-' }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-12 text-dark-text-tertiary">
                            <i class="fas fa-share-alt text-4xl mb-3 opacity-50"></i>
                            <p>No syndicated content found</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($syndications->hasPages())
        <div class="p-5 border-t" style="border-color: var(--dark-separator);">
            {{ $syndications->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
