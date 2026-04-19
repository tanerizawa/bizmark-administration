@extends('layouts.app')

@section('title', 'SEO Command Center')

@section('content')
<div class="space-y-4">
    {{-- Compact Hero Section --}}
    <section class="card-elevated rounded-apple-lg admin-hero relative overflow-hidden">
        <div class="absolute inset-0 pointer-events-none" aria-hidden="true">
            <div class="w-48 h-48 bg-apple-green opacity-20 blur-3xl rounded-full absolute -top-12 -right-8"></div>
        </div>
        <div class="relative flex flex-col md:flex-row md:items-center md:justify-between gap-3">
            <div class="flex-1 min-w-0">
                <p class="admin-hero-subtitle">SEO & Analytics</p>
                <h1 class="admin-hero-title text-white">🚀 SEO Command Center</h1>
                <p class="admin-hero-desc">Monitor performa SEO, scoring artikel, dan optimasi konten</p>
                <div class="admin-hero-meta flex flex-wrap gap-3">
                    <span><i class="fas fa-file-alt mr-1.5"></i>{{ number_format($stats['published_count']) }} artikel</span>
                    <span><i class="fas fa-chart-line mr-1.5"></i>{{ number_format($stats['total_views']) }} views</span>
                    <span><i class="fas fa-star mr-1.5"></i>{{ $stats['avg_seo_score'] }} avg</span>
                </div>
            </div>
            <div class="inline-flex rounded-apple overflow-hidden" style="background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.1);">
                @foreach(['7days' => '7D', '30days' => '30D', '90days' => '90D'] as $key => $label)
                    <a href="{{ route('admin.seo.command-center', ['period' => $key]) }}" 
                       class="px-2.5 py-1.5 admin-small font-medium transition-apple {{ $period === $key ? 'bg-apple-blue text-white' : 'text-white/60 hover:text-white hover:bg-white/5' }}">
                        {{ $label }}
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Alert Messages - Compact --}}
    @if(session('success'))
        <div class="admin-alert flex items-center justify-between" style="background-color: rgba(52, 199, 89, 0.15); border: 1px solid var(--apple-green);">
            <div class="flex items-center">
                <i class="fas fa-check-circle mr-2" style="color: var(--apple-green);"></i>
                <span class="admin-body" style="color: var(--apple-green);">{{ session('success') }}</span>
            </div>
            <button onclick="this.parentElement.remove()" class="admin-small opacity-60 hover:opacity-100" style="color: var(--apple-green);">
                <i class="fas fa-times"></i>
            </button>
        </div>
    @endif

    {{-- Compact Quick Stats Row --}}
    <div class="grid grid-cols-4 gap-2">
        <div class="admin-stat-card card-elevated rounded-apple flex items-center gap-2" style="background: rgba(52,199,89,0.1); border: 1px solid rgba(52,199,89,0.2);">
            <div class="admin-stat-icon rounded flex items-center justify-center" style="background: rgba(52,199,89,0.2);">
                <i class="fas fa-trophy text-apple-green" style="font-size: 0.7rem;"></i>
            </div>
            <div>
                <p class="admin-small text-apple-green uppercase tracking-wider">Excellent</p>
                <p class="admin-stat text-white">{{ $scoreDistribution['excellent'] ?? 0 }}</p>
            </div>
        </div>
        
        <div class="admin-stat-card card-elevated rounded-apple flex items-center gap-2" style="background: rgba(10,132,255,0.1); border: 1px solid rgba(10,132,255,0.2);">
            <div class="admin-stat-icon rounded flex items-center justify-center" style="background: rgba(10,132,255,0.2);">
                <i class="fas fa-thumbs-up text-apple-blue" style="font-size: 0.7rem;"></i>
            </div>
            <div>
                <p class="admin-small text-apple-blue uppercase tracking-wider">Good</p>
                <p class="admin-stat text-white">{{ $scoreDistribution['good'] ?? 0 }}</p>
            </div>
        </div>
        
        <div class="admin-stat-card card-elevated rounded-apple flex items-center gap-2" style="background: rgba(255,149,0,0.1); border: 1px solid rgba(255,149,0,0.2);">
            <div class="admin-stat-icon rounded flex items-center justify-center" style="background: rgba(255,149,0,0.2);">
                <i class="fas fa-minus text-apple-orange" style="font-size: 0.7rem;"></i>
            </div>
            <div>
                <p class="admin-small text-apple-orange uppercase tracking-wider">Average</p>
                <p class="admin-stat text-white">{{ $scoreDistribution['average'] ?? 0 }}</p>
            </div>
        </div>
        
        <div class="admin-stat-card card-elevated rounded-apple flex items-center gap-2" style="background: rgba(255,69,58,0.1); border: 1px solid rgba(255,69,58,0.2);">
            <div class="admin-stat-icon rounded flex items-center justify-center" style="background: rgba(255,69,58,0.2);">
                <i class="fas fa-exclamation-triangle text-apple-red" style="font-size: 0.7rem;"></i>
            </div>
            <div>
                <p class="admin-small text-apple-red uppercase tracking-wider">Needs Work</p>
                <p class="admin-stat text-white">{{ $scoreDistribution['poor'] ?? 0 }}</p>
            </div>
        </div>
    </div>

    {{-- Compact Module Navigation Cards --}}
    <section class="card-elevated rounded-apple-lg p-3">
        <h2 class="admin-section text-white mb-3 flex items-center gap-2">
            <i class="fas fa-compass text-apple-blue" style="font-size: 0.75rem;"></i>
            SEO Modules
        </h2>
        
        <div class="grid grid-cols-2 md:grid-cols-4 gap-2">
            {{-- SEO Scores --}}
            <a href="{{ route('admin.seo.scores') }}" 
               class="group admin-module-card relative rounded-apple transition-all hover:scale-[1.01]"
               style="background: linear-gradient(135deg, rgba(10,132,255,0.15) 0%, rgba(94,92,230,0.15) 100%); border: 1px solid rgba(10,132,255,0.3);">
                <div class="flex items-start justify-between">
                    <div class="space-y-1">
                        <div class="admin-module-icon rounded flex items-center justify-center" style="background: rgba(10,132,255,0.2);">
                            <i class="fas fa-chart-bar text-apple-blue"></i>
                        </div>
                        <h3 class="admin-module-title text-white">SEO Scores</h3>
                        <p class="admin-module-desc">Analisis skor SEO artikel</p>
                    </div>
                    @if(($moduleCounts['low_scores'] ?? 0) > 0)
                        <span class="admin-badge bg-apple-red text-white">{{ $moduleCounts['low_scores'] }} fix</span>
                    @endif
                </div>
                <div class="absolute bottom-2 right-2 text-apple-blue/50 group-hover:text-apple-blue transition-colors">
                    <i class="fas fa-arrow-right" style="font-size: 0.625rem;"></i>
                </div>
            </a>
            
            {{-- Competitors --}}
            <a href="{{ route('admin.seo.competitors') }}" 
               class="group admin-module-card relative rounded-apple transition-all hover:scale-[1.01]"
               style="background: linear-gradient(135deg, rgba(175,82,222,0.15) 0%, rgba(191,90,242,0.15) 100%); border: 1px solid rgba(175,82,222,0.3);">
                <div class="flex items-start justify-between">
                    <div class="space-y-1">
                        <div class="admin-module-icon rounded flex items-center justify-center" style="background: rgba(175,82,222,0.2);">
                            <i class="fas fa-search text-apple-purple"></i>
                        </div>
                        <h3 class="admin-module-title text-white">Competitor</h3>
                        <p class="admin-module-desc">Benchmark kompetitor</p>
                    </div>
                    @if(($moduleCounts['competitors'] ?? 0) > 0)
                        <span class="admin-badge" style="background: rgba(175,82,222,0.3); color: rgba(175,82,222,1);">{{ $moduleCounts['competitors'] }}</span>
                    @endif
                </div>
                <div class="absolute bottom-2 right-2 text-apple-purple/50 group-hover:text-apple-purple transition-colors">
                    <i class="fas fa-arrow-right" style="font-size: 0.625rem;"></i>
                </div>
            </a>
            
            {{-- A/B Tests --}}
            <a href="{{ route('admin.seo.ab-tests') }}" 
               class="group admin-module-card relative rounded-apple transition-all hover:scale-[1.01]"
               style="background: linear-gradient(135deg, rgba(52,199,89,0.15) 0%, rgba(48,209,88,0.15) 100%); border: 1px solid rgba(52,199,89,0.3);">
                <div class="flex items-start justify-between">
                    <div class="space-y-1">
                        <div class="admin-module-icon rounded flex items-center justify-center" style="background: rgba(52,199,89,0.2);">
                            <i class="fas fa-flask text-apple-green"></i>
                        </div>
                        <h3 class="admin-module-title text-white">A/B Tests</h3>
                        <p class="admin-module-desc">Title & meta testing</p>
                    </div>
                    @if(($moduleCounts['active_tests'] ?? 0) > 0)
                        <span class="admin-badge bg-apple-green text-white">{{ $moduleCounts['active_tests'] }}</span>
                    @endif
                </div>
                <div class="absolute bottom-2 right-2 text-apple-green/50 group-hover:text-apple-green transition-colors">
                    <i class="fas fa-arrow-right" style="font-size: 0.625rem;"></i>
                </div>
            </a>
            
            {{-- Search Console --}}
            <a href="{{ route('admin.seo.search-console') }}" 
               class="group admin-module-card relative rounded-apple transition-all hover:scale-[1.01]"
               style="background: linear-gradient(135deg, rgba(255,149,0,0.15) 0%, rgba(255,159,10,0.15) 100%); border: 1px solid rgba(255,149,0,0.3);">
                <div class="flex items-start justify-between">
                    <div class="space-y-1">
                        <div class="admin-module-icon rounded flex items-center justify-center" style="background: rgba(255,149,0,0.2);">
                            <i class="fab fa-google text-apple-orange"></i>
                        </div>
                        <h3 class="admin-module-title text-white">Search Console</h3>
                        <p class="admin-module-desc">Data GSC</p>
                    </div>
                    @if(($moduleCounts['search_console'] ?? 0) > 0)
                        <span class="admin-badge" style="background: rgba(255,149,0,0.3); color: rgba(255,149,0,1);">{{ number_format($moduleCounts['search_console']) }}</span>
                    @endif
                </div>
                <div class="absolute bottom-2 right-2 text-apple-orange/50 group-hover:text-apple-orange transition-colors">
                    <i class="fas fa-arrow-right" style="font-size: 0.625rem;"></i>
                </div>
            </a>
            
            {{-- Content Refresh --}}
            <a href="{{ route('admin.seo.refresh-logs') }}" 
               class="group admin-module-card relative rounded-apple transition-all hover:scale-[1.01]"
               style="background: linear-gradient(135deg, rgba(255,69,58,0.15) 0%, rgba(255,59,48,0.15) 100%); border: 1px solid rgba(255,69,58,0.3);">
                <div class="flex items-start justify-between">
                    <div class="space-y-1">
                        <div class="admin-module-icon rounded flex items-center justify-center" style="background: rgba(255,69,58,0.2);">
                            <i class="fas fa-sync-alt text-apple-red"></i>
                        </div>
                        <h3 class="admin-module-title text-white">Refresh</h3>
                        <p class="admin-module-desc">Update konten lama</p>
                    </div>
                    @if(($moduleCounts['pending_refresh'] ?? 0) > 0)
                        <span class="admin-badge bg-apple-yellow text-black">{{ $moduleCounts['pending_refresh'] }}</span>
                    @endif
                </div>
                <div class="absolute bottom-2 right-2 text-apple-red/50 group-hover:text-apple-red transition-colors">
                    <i class="fas fa-arrow-right" style="font-size: 0.625rem;"></i>
                </div>
            </a>
            
            {{-- Programmatic SEO --}}
            <a href="{{ route('admin.seo.programmatic') }}" 
               class="group admin-module-card relative rounded-apple transition-all hover:scale-[1.01]"
               style="background: linear-gradient(135deg, rgba(94,92,230,0.15) 0%, rgba(100,160,255,0.15) 100%); border: 1px solid rgba(94,92,230,0.3);">
                <div class="space-y-1">
                    <div class="admin-module-icon rounded flex items-center justify-center" style="background: rgba(94,92,230,0.2);">
                        <i class="fas fa-code text-indigo-400"></i>
                    </div>
                    <h3 class="admin-module-title text-white">Programmatic</h3>
                    <p class="admin-module-desc">Landing pages</p>
                </div>
                <div class="absolute bottom-2 right-2 text-indigo-400/50 group-hover:text-indigo-400 transition-colors">
                    <i class="fas fa-arrow-right" style="font-size: 0.625rem;"></i>
                </div>
            </a>
            
            {{-- Reports --}}
            <a href="{{ route('admin.seo.reports') }}" 
               class="group admin-module-card relative rounded-apple transition-all hover:scale-[1.01]"
               style="background: linear-gradient(135deg, rgba(255,255,255,0.05) 0%, rgba(255,255,255,0.02) 100%); border: 1px solid rgba(255,255,255,0.1);">
                <div class="flex items-start justify-between">
                    <div class="space-y-1">
                        <div class="admin-module-icon rounded flex items-center justify-center" style="background: rgba(255,255,255,0.1);">
                            <i class="fas fa-file-alt text-white/70"></i>
                        </div>
                        <h3 class="admin-module-title text-white">Reports</h3>
                        <p class="admin-module-desc">Laporan SEO</p>
                    </div>
                    @if(($moduleCounts['reports'] ?? 0) > 0)
                        <span class="admin-badge" style="background: rgba(255,255,255,0.1); color: rgba(255,255,255,0.8);">{{ $moduleCounts['reports'] }}</span>
                    @endif
                </div>
                <div class="absolute bottom-2 right-2 text-white/30 group-hover:text-white/60 transition-colors">
                    <i class="fas fa-arrow-right" style="font-size: 0.625rem;"></i>
                </div>
            </a>
            
            {{-- Full Dashboard --}}
            <a href="{{ route('admin.seo.dashboard') }}" 
               class="group admin-module-card relative rounded-apple transition-all hover:scale-[1.01]"
               style="background: linear-gradient(135deg, rgba(255,214,10,0.15) 0%, rgba(255,204,0,0.15) 100%); border: 1px solid rgba(255,214,10,0.3);">
                <div class="space-y-1">
                    <div class="admin-module-icon rounded flex items-center justify-center" style="background: rgba(255,214,10,0.2);">
                        <i class="fas fa-tachometer-alt text-apple-yellow"></i>
                    </div>
                    <h3 class="admin-module-title text-white">Dashboard</h3>
                    <p class="admin-module-desc">Charts & trends</p>
                </div>
                <div class="absolute bottom-2 right-2 text-apple-yellow/50 group-hover:text-apple-yellow transition-colors">
                    <i class="fas fa-arrow-right" style="font-size: 0.625rem;"></i>
                </div>
            </a>
        </div>
    </section>

    {{-- Low Score Articles - Compact --}}
    @if($lowScoreArticles->count() > 0)
    <section class="card-elevated rounded-apple-lg p-3">
        <div class="flex items-center justify-between mb-2">
            <h2 class="admin-section text-white flex items-center gap-2">
                <i class="fas fa-exclamation-circle text-apple-red" style="font-size: 0.75rem;"></i>
                Perlu Perhatian
            </h2>
            <a href="{{ route('admin.seo.scores', ['sort' => 'score_asc']) }}" class="admin-small text-apple-blue hover:underline">
                Lihat Semua <i class="fas fa-arrow-right ml-1"></i>
            </a>
        </div>
        
        <div class="space-y-1.5">
            @foreach($lowScoreArticles as $score)
                <div class="flex items-center justify-between p-2 rounded-apple" style="background: var(--dark-bg-tertiary); border: 1px solid var(--dark-separator);">
                    <div class="flex-1 min-w-0">
                        <a href="{{ route('admin.seo.score-detail', $score->article_id) }}" class="admin-body text-white font-medium hover:text-apple-blue truncate block">
                            {{ $score->article->title ?? 'Unknown Article' }}
                        </a>
                    </div>
                    <div class="flex items-center gap-2 ml-3">
                        <span class="admin-badge {{ $score->total_score < 40 ? 'bg-apple-red' : 'bg-apple-orange' }} text-white">
                            {{ round($score->total_score) }}
                        </span>
                        <form action="{{ route('admin.seo.fix-single', $score->article_id) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="admin-btn admin-btn-sm bg-apple-blue text-white hover:bg-blue-700 transition-apple">
                                <i class="fas fa-wrench mr-1"></i>Fix
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    </section>
    @endif
</div>
@endsection
