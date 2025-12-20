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
    @if($syndications->isEmpty())
    {{-- Enhanced Empty State --}}
    <div class="card-apple p-12 text-center">
        <div class="max-w-2xl mx-auto">
            <!-- Icon -->
            <div class="mb-6">
                <div class="inline-flex items-center justify-center w-24 h-24 rounded-full mb-4" style="background: rgba(48,209,88,0.12);">
                    <i class="fas fa-share-alt text-4xl" style="color: rgba(48,209,88,1);"></i>
                </div>
            </div>
            
            <!-- Message -->
            <h3 class="text-2xl font-bold mb-3" style="color: #FFFFFF;">
                Belum Ada Content Syndication
            </h3>
            <p class="text-sm mb-8" style="color: rgba(235,235,245,0.6);">
                Content syndication membantu memperluas jangkauan artikel Anda dengan mempublikasikannya di platform eksternal seperti Medium, Dev.to, dan Hashnode.
            </p>
            
            <!-- Action Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-8">
                <div class="p-6 rounded-apple text-left" style="background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1);">
                    <div class="flex items-start mb-4">
                        <div class="w-10 h-10 rounded-lg flex items-center justify-center mr-3" style="background: rgba(10,132,255,0.15);">
                            <i class="fas fa-plus" style="color: rgba(10,132,255,1);"></i>
                        </div>
                        <div class="flex-1">
                            <h4 class="font-semibold mb-1" style="color: #FFFFFF;">Manual Syndication</h4>
                            <p class="text-xs mb-3" style="color: rgba(235,235,245,0.6);">
                                Track articles yang sudah Anda publikasikan secara manual di platform lain
                            </p>
                            <span class="text-xs px-2.5 py-1 rounded inline-block" style="background: rgba(10,132,255,0.15); color: rgba(10,132,255,1);">
                                Coming Soon
                            </span>
                        </div>
                    </div>
                </div>
                
                <div class="p-6 rounded-apple text-left" style="background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1);">
                    <div class="flex items-start mb-4">
                        <div class="w-10 h-10 rounded-lg flex items-center justify-center mr-3" style="background: rgba(48,209,88,0.15);">
                            <i class="fas fa-robot" style="color: rgba(48,209,88,1);"></i>
                        </div>
                        <div class="flex-1">
                            <h4 class="font-semibold mb-1" style="color: #FFFFFF;">Auto Syndication</h4>
                            <p class="text-xs mb-3" style="color: rgba(235,235,245,0.6);">
                                Otomatis publikasikan artikel ke multiple platform via API
                            </p>
                            <span class="text-xs px-2.5 py-1 rounded inline-block" style="background: rgba(255,159,10,0.15); color: rgba(255,159,10,1);">
                                In Development
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Supported Platforms -->
            <div class="p-6 rounded-apple text-left" style="background: rgba(48,209,88,0.12); border: 1px solid rgba(48,209,88,0.2);">
                <h4 class="font-semibold mb-3 flex items-center" style="color: rgba(48,209,88,1);">
                    <i class="fas fa-check-circle mr-2"></i>
                    Platform yang Akan Didukung:
                </h4>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-xs">
                    <div class="flex items-center" style="color: rgba(235,235,245,0.75);">
                        <i class="fab fa-medium text-lg mr-2" style="color: rgba(48,209,88,1);"></i>
                        <span><strong>Medium</strong> - 200M+ readers</span>
                    </div>
                    <div class="flex items-center" style="color: rgba(235,235,245,0.75);">
                        <i class="fab fa-dev text-lg mr-2" style="color: rgba(48,209,88,1);"></i>
                        <span><strong>Dev.to</strong> - Developer community</span>
                    </div>
                    <div class="flex items-center" style="color: rgba(235,235,245,0.75);">
                        <i class="fas fa-hashtag text-lg mr-2" style="color: rgba(48,209,88,1);"></i>
                        <span><strong>Hashnode</strong> - Tech blogging</span>
                    </div>
                </div>
            </div>
            
            <!-- Benefits -->
            <div class="mt-8 p-6 rounded-apple text-left" style="background: rgba(255,255,255,0.03);">
                <h4 class="font-semibold mb-3" style="color: #FFFFFF;">
                    <i class="fas fa-lightbulb mr-2" style="color: rgba(255,159,10,1);"></i>
                    Manfaat Content Syndication:
                </h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-xs" style="color: rgba(235,235,245,0.6);">
                    <div class="flex items-start">
                        <i class="fas fa-arrow-right mr-2 mt-0.5" style="color: rgba(48,209,88,1);"></i>
                        <span>Meningkatkan reach & traffic artikel</span>
                    </div>
                    <div class="flex items-start">
                        <i class="fas fa-arrow-right mr-2 mt-0.5" style="color: rgba(48,209,88,1);"></i>
                        <span>Mendapatkan backlinks berkualitas</span>
                    </div>
                    <div class="flex items-start">
                        <i class="fas fa-arrow-right mr-2 mt-0.5" style="color: rgba(48,209,88,1);"></i>
                        <span>Memperluas audience di platform baru</span>
                    </div>
                    <div class="flex items-start">
                        <i class="fas fa-arrow-right mr-2 mt-0.5" style="color: rgba(48,209,88,1);"></i>
                        <span>Membangun authority di niche Anda</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @else
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
                    @foreach($syndications as $syndication)
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
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($syndications->hasPages())
        <div class="p-5 border-t" style="border-color: var(--dark-separator);">
            {{ $syndications->links() }}
        </div>
        @endif
    </div>
    @endif
</div>
@endsection
