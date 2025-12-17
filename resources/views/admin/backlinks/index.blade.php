@extends('layouts.app')

@section('title', 'Backlink Builder Dashboard')

@section('content')
<div class="container-custom">
    {{-- Hero Header --}}
    <section class="card-apple p-5 md:p-6 relative overflow-hidden mb-6">
        <!-- Background Gradient Effects -->
        <div class="absolute inset-0 pointer-events-none" aria-hidden="true">
            <div class="w-72 h-72 bg-apple-blue opacity-30 blur-3xl rounded-full absolute -top-16 -right-10"></div>
            <div class="w-48 h-48 bg-apple-purple opacity-20 blur-2xl rounded-full absolute bottom-0 left-10"></div>
        </div>

        <div class="relative space-y-4">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <div class="space-y-2">
                    <p class="text-xs uppercase tracking-[0.4em]" style="color: rgba(235,235,245,0.5);">SEO & Marketing</p>
                    <h1 class="text-2xl md:text-3xl font-bold" style="color: #FFFFFF;">
                        <i class="fas fa-link mr-2"></i>Backlink Builder Dashboard
                    </h1>
                    <p class="text-sm" style="color: rgba(235,235,245,0.75);">
                        Automated backlink acquisition and content syndication system
                    </p>
                </div>
                <div>
                    <a href="{{ route('admin.backlinks.targets.create') }}" class="inline-flex items-center px-4 py-2.5 bg-apple-blue text-white rounded-apple text-sm font-medium hover:bg-apple-blue-dark transition-apple">
                        <i class="fas fa-plus mr-2"></i>Add Target Website
                    </a>
                </div>
            </div>
        </div>
    </section>

    {{-- Success Message --}}
    @if(session('success'))
    <div class="mb-5 p-4 rounded-apple-lg" style="background: rgba(52,199,89,0.12); border: 1px solid rgba(52,199,89,0.3); color: rgba(52,199,89,1);">
        <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
    </div>
    @endif

    {{-- Statistics Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
        <!-- Target Websites -->
        <div class="card-apple p-5 hover-lift">
            <div class="flex items-start justify-between mb-4">
                <div class="w-12 h-12 rounded-apple flex items-center justify-center"
                     style="background: linear-gradient(135deg, rgba(10,132,255,1) 0%, rgba(37,99,235,1) 100%);">
                    <i class="fas fa-bullseye text-white text-xl"></i>
                </div>
                <span class="text-sm font-semibold px-3 py-1 rounded-apple"
                      style="background: rgba(10,132,255,0.15); color: rgba(10,132,255,1);">
                    Total
                </span>
            </div>
            <h3 class="text-3xl font-bold mb-1" style="color: white;">
                {{ $stats['total_targets'] }}
            </h3>
            <p class="text-xs uppercase tracking-wider mb-3" style="color: rgba(235,235,245,0.6);">
                Target Websites
            </p>
            <span class="inline-block text-xs font-medium px-2 py-1 rounded-apple" 
                  style="background: rgba(255,214,10,0.15); color: rgba(255,214,10,1);">
                {{ $stats['pending_targets'] }} Pending
            </span>
        </div>

        <!-- Acquired Backlinks -->
        <div class="card-apple p-5 hover-lift">
            <div class="flex items-start justify-between mb-4">
                <div class="w-12 h-12 rounded-apple flex items-center justify-center"
                     style="background: linear-gradient(135deg, rgba(48,209,88,1) 0%, rgba(5,150,105,1) 100%);">
                    <i class="fas fa-external-link-alt text-white text-xl"></i>
                </div>
                <span class="text-sm font-semibold px-3 py-1 rounded-apple"
                      style="background: rgba(48,209,88,0.15); color: rgba(48,209,88,1);">
                    Acquired
                </span>
            </div>
            <h3 class="text-3xl font-bold mb-1" style="color: white;">
                {{ $stats['acquired_backlinks'] }}
            </h3>
            <p class="text-xs uppercase tracking-wider mb-3" style="color: rgba(235,235,245,0.6);">
                Total Backlinks
            </p>
            <div class="flex gap-2">
                <span class="inline-block text-xs font-medium px-2 py-1 rounded-apple" 
                      style="background: rgba(48,209,88,0.15); color: rgba(48,209,88,1);">
                    {{ $stats['indexed_backlinks'] }} Indexed
                </span>
                <span class="inline-block text-xs font-medium px-2 py-1 rounded-apple" 
                      style="background: rgba(10,132,255,0.15); color: rgba(10,132,255,1);">
                    {{ $stats['dofollow_backlinks'] }} Dofollow
                </span>
            </div>
        </div>

        <!-- Average DA -->
        <div class="card-apple p-5 hover-lift">
            <div class="flex items-start justify-between mb-4">
                <div class="w-12 h-12 rounded-apple flex items-center justify-center"
                     style="background: linear-gradient(135deg, rgba(255,159,10,1) 0%, rgba(217,119,6,1) 100%);">
                    <i class="fas fa-chart-line text-white text-xl"></i>
                </div>
                <span class="text-sm font-semibold px-3 py-1 rounded-apple"
                      style="background: rgba(255,159,10,0.15); color: rgba(255,159,10,1);">
                    DA Score
                </span>
            </div>
            <h3 class="text-3xl font-bold mb-1" style="color: white;">
                {{ number_format($stats['avg_da'] ?? 0, 1) }}
            </h3>
            <p class="text-xs uppercase tracking-wider mb-3" style="color: rgba(235,235,245,0.6);">
                Average Domain Authority
            </p>
            <span class="inline-block text-xs font-medium px-2 py-1 rounded-apple" 
                  style="background: rgba(142,142,147,0.15); color: rgba(142,142,147,1);">
                Total: {{ $stats['total_da'] ?? 0 }}
            </span>
        </div>

        <!-- Response Rate -->
        <div class="card-apple p-5 hover-lift">
            <div class="flex items-start justify-between mb-4">
                <div class="w-12 h-12 rounded-apple flex items-center justify-center"
                     style="background: linear-gradient(135deg, rgba(175,82,222,1) 0%, rgba(124,58,237,1) 100%);">
                    <i class="fas fa-percentage text-white text-xl"></i>
                </div>
                <span class="text-sm font-semibold px-3 py-1 rounded-apple"
                      style="background: rgba(175,82,222,0.15); color: rgba(175,82,222,1);">
                    Success
                </span>
            </div>
            <h3 class="text-3xl font-bold mb-1" style="color: white;">
                {{ $stats['response_rate'] }}%
            </h3>
            <p class="text-xs uppercase tracking-wider mb-3" style="color: rgba(235,235,245,0.6);">
                Response Rate
            </p>
            <span class="inline-block text-xs font-medium px-2 py-1 rounded-apple" 
                  style="background: rgba(175,82,222,0.15); color: rgba(175,82,222,1);">
                {{ $stats['outreach_responded'] }}/{{ $stats['outreach_sent'] }} Responded
            </span>
        </div>
    </div>

    {{-- Content Grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        <!-- Quick Actions -->
        <div class="card-apple p-5">
            <h2 class="text-lg font-bold mb-4 flex items-center" style="color: white;">
                <i class="fas fa-rocket text-apple-blue mr-2"></i>
                Quick Actions
            </h2>
            <div class="space-y-3">
                <a href="{{ route('admin.backlinks.settings') }}" class="w-full flex items-center justify-between p-3 rounded-apple transition-apple" 
                   style="background: rgba(191,90,242,0.12); border: 1px solid rgba(191,90,242,0.3);"
                   onmouseover="this.style.background='rgba(191,90,242,0.20)'"
                   onmouseout="this.style.background='rgba(191,90,242,0.12)'">
                    <span class="flex items-center text-sm font-medium" style="color: rgba(191,90,242,1);">
                        <i class="fas fa-brain mr-2"></i>
                        AI Automation Settings
                    </span>
                    <i class="fas fa-arrow-right" style="color: rgba(191,90,242,1);"></i>
                </a>

                <button onclick="alert('Run via SSH:\nphp artisan backlink:outreach --ai --limit=5 --dry-run')" class="w-full flex items-center justify-between p-3 rounded-apple transition-apple" 
                        style="background: rgba(10,132,255,0.12); border: 1px solid rgba(10,132,255,0.3);"
                        onmouseover="this.style.background='rgba(10,132,255,0.20)'"
                        onmouseout="this.style.background='rgba(10,132,255,0.12)'">
                    <span class="flex items-center text-sm font-medium" style="color: rgba(10,132,255,1);">
                        <i class="fas fa-envelope mr-2"></i>
                        AI Email Outreach
                    </span>
                    <i class="fas fa-arrow-right" style="color: rgba(10,132,255,1);"></i>
                </button>

                <button onclick="alert('Run via SSH:\nphp artisan backlink:crawl --all --limit=10')" class="w-full flex items-center justify-between p-3 rounded-apple transition-apple" 
                        style="background: rgba(48,209,88,0.12); border: 1px solid rgba(48,209,88,0.3);"
                        onmouseover="this.style.background='rgba(48,209,88,0.20)'"
                        onmouseout="this.style.background='rgba(48,209,88,0.12)'">
                    <span class="flex items-center text-sm font-medium" style="color: rgba(48,209,88,1);">
                        <i class="fas fa-spider mr-2"></i>
                        Crawl Backlinks
                    </span>
                    <i class="fas fa-arrow-right" style="color: rgba(48,209,88,1);"></i>
                </button>

                <button onclick="alert('Run via SSH:\nphp artisan backlink:monitor --limit=20')" class="w-full flex items-center justify-between p-3 rounded-apple transition-apple" 
                        style="background: rgba(255,159,10,0.12); border: 1px solid rgba(255,159,10,0.3);"
                        onmouseover="this.style.background='rgba(255,159,10,0.20)'"
                        onmouseout="this.style.background='rgba(255,159,10,0.12)'">
                    <span class="flex items-center text-sm font-medium" style="color: rgba(255,159,10,1);">
                        <i class="fas fa-sync mr-2"></i>
                        Monitor Health
                    </span>
                    <i class="fas fa-arrow-right" style="color: rgba(255,159,10,1);"></i>
                </button>
            </div>
        </div>

        <!-- High Priority Targets -->
        <div class="lg:col-span-2 card-apple p-5">
            <h2 class="text-lg font-bold mb-4 flex items-center" style="color: white;">
                <i class="fas fa-fire text-apple-orange mr-2"></i>
                High Priority Targets
            </h2>

            @if($highPriorityTargets->count() > 0)
                <div class="space-y-3">
                    @foreach($highPriorityTargets->take(5) as $target)
                    <div class="flex items-center justify-between p-3 rounded-apple" 
                         style="background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.08);">
                        <div class="flex-1">
                            <h3 class="text-sm font-semibold mb-1" style="color: white;">
                                {{ $target->website_name }}
                            </h3>
                            <p class="text-xs" style="color: rgba(235,235,245,0.6);">
                                {{ $target->category }}
                            </p>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-medium px-2 py-1 rounded-apple" 
                                  style="background: {{ $target->domain_authority >= 70 ? 'rgba(48,209,88,0.15)' : ($target->domain_authority >= 50 ? 'rgba(255,159,10,0.15)' : 'rgba(142,142,147,0.15)') }}; 
                                         color: {{ $target->domain_authority >= 70 ? 'rgba(48,209,88,1)' : ($target->domain_authority >= 50 ? 'rgba(255,159,10,1)' : 'rgba(142,142,147,1)') }};">
                                DA {{ $target->domain_authority ?? 'N/A' }}
                            </span>
                            <span class="text-xs font-medium px-2 py-1 rounded-apple" 
                                  style="background: rgba(10,132,255,0.15); color: rgba(10,132,255,1);">
                                {{ ucfirst(str_replace('_', ' ', $target->type)) }}
                            </span>
                            <a href="{{ route('admin.backlinks.targets.edit', $target) }}" 
                               class="inline-flex items-center px-3 py-1.5 bg-apple-blue text-white rounded-apple text-xs font-medium hover:bg-apple-blue-dark transition-apple">
                                Contact
                            </a>
                        </div>
                    </div>
                    @endforeach
                </div>
                <div class="mt-4">
                    <a href="{{ route('admin.backlinks.targets') }}" class="text-apple-blue text-sm font-medium hover:underline">
                        View all {{ $stats['total_targets'] }} targets <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
            @else
                <div class="text-center py-8" style="color: rgba(235,235,245,0.6);">
                    <i class="fas fa-bullseye fa-3x mb-3 opacity-50"></i>
                    <p class="text-sm mb-4">No high priority targets available.</p>
                    <a href="{{ route('admin.backlinks.targets.create') }}" class="inline-flex items-center px-4 py-2 bg-apple-blue text-white rounded-apple text-sm font-medium hover:bg-apple-blue-dark transition-apple">
                        Add Target Website
                    </a>
                </div>
            @endif
        </div>
    </div>

    {{-- Recent Backlinks --}}
    <div class="card-apple p-5">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-bold flex items-center" style="color: white;">
                <i class="fas fa-link text-apple-green mr-2"></i>
                Recent Backlinks
            </h2>
            <a href="{{ route('admin.backlinks.list') }}" class="text-apple-blue text-sm font-medium hover:underline">
                View All <i class="fas fa-arrow-right ml-1"></i>
            </a>
        </div>

        @if($recentBacklinks->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b" style="border-color: rgba(255,255,255,0.08);">
                            <th class="text-left py-3 px-4 text-xs uppercase tracking-wider" style="color: rgba(235,235,245,0.6);">Source</th>
                            <th class="text-left py-3 px-4 text-xs uppercase tracking-wider" style="color: rgba(235,235,245,0.6);">Anchor Text</th>
                            <th class="text-left py-3 px-4 text-xs uppercase tracking-wider" style="color: rgba(235,235,245,0.6);">Type</th>
                            <th class="text-left py-3 px-4 text-xs uppercase tracking-wider" style="color: rgba(235,235,245,0.6);">DA</th>
                            <th class="text-left py-3 px-4 text-xs uppercase tracking-wider" style="color: rgba(235,235,245,0.6);">Status</th>
                            <th class="text-left py-3 px-4 text-xs uppercase tracking-wider" style="color: rgba(235,235,245,0.6);">Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentBacklinks as $backlink)
                        <tr class="border-b transition-apple hover:bg-white hover:bg-opacity-5" style="border-color: rgba(255,255,255,0.05);">
                            <td class="py-3 px-4">
                                <a href="{{ $backlink->source_url }}" target="_blank" class="text-apple-blue text-sm hover:underline flex items-center">
                                    {{ Str::limit($backlink->source_url, 40) }}
                                    <i class="fas fa-external-link-alt fa-xs ml-1"></i>
                                </a>
                            </td>
                            <td class="py-3 px-4 text-sm" style="color: white;">{{ $backlink->anchor_text }}</td>
                            <td class="py-3 px-4">
                                <span class="text-xs font-medium px-2 py-1 rounded-apple"
                                      style="background: {{ $backlink->type == 'dofollow' ? 'rgba(48,209,88,0.15)' : 'rgba(142,142,147,0.15)' }};
                                             color: {{ $backlink->type == 'dofollow' ? 'rgba(48,209,88,1)' : 'rgba(142,142,147,1)' }};">
                                    {{ ucfirst($backlink->type) }}
                                </span>
                            </td>
                            <td class="py-3 px-4">
                                <span class="text-xs font-medium px-2 py-1 rounded-apple" 
                                      style="background: rgba(10,132,255,0.15); color: rgba(10,132,255,1);">
                                    {{ $backlink->domain_authority ?? 'N/A' }}
                                </span>
                            </td>
                            <td class="py-3 px-4">
                                <span class="text-xs font-medium px-2 py-1 rounded-apple"
                                      style="background: {{ $backlink->status == 'indexed' ? 'rgba(48,209,88,0.15)' : 'rgba(255,214,10,0.15)' }};
                                             color: {{ $backlink->status == 'indexed' ? 'rgba(48,209,88,1)' : 'rgba(255,214,10,1)' }};">
                                    {{ ucfirst($backlink->status) }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-xs" style="color: rgba(235,235,245,0.6);">
                                {{ $backlink->acquired_at?->diffForHumans() ?? 'N/A' }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-8" style="color: rgba(235,235,245,0.6);">
                <i class="fas fa-link fa-3x mb-3 opacity-50"></i>
                <p class="text-sm mb-2">No backlinks acquired yet.</p>
                <p class="text-xs">Start by adding target websites and running outreach campaigns.</p>
            </div>
        @endif
    </div>

    {{-- Automation Commands Info --}}
    <div class="mt-6 card-apple p-5">
        <h3 class="text-sm font-bold mb-4 flex items-center" style="color: rgba(235,235,245,0.9);">
            <i class="fas fa-terminal text-apple-purple mr-2"></i>
            Available Automation Commands
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <code class="block p-3 rounded-apple text-xs font-mono" 
                      style="background: rgba(255,255,255,0.05); color: rgba(48,209,88,1);">
                    php artisan backlink:outreach
                </code>
                <p class="text-xs mt-2" style="color: rgba(235,235,245,0.6);">Send automated outreach emails to targets</p>
            </div>
            <div>
                <code class="block p-3 rounded-apple text-xs font-mono" 
                      style="background: rgba(255,255,255,0.05); color: rgba(10,132,255,1);">
                    php artisan content:syndicate
                </code>
                <p class="text-xs mt-2" style="color: rgba(235,235,245,0.6);">Syndicate articles to Medium/LinkedIn</p>
            </div>
            <div>
                <code class="block p-3 rounded-apple text-xs font-mono" 
                      style="background: rgba(255,255,255,0.05); color: rgba(255,159,10,1);">
                    php artisan backlink:monitor
                </code>
                <p class="text-xs mt-2" style="color: rgba(235,235,245,0.6);">Check backlink status and health</p>
            </div>
        </div>
    </div>
</div>
@endsection
