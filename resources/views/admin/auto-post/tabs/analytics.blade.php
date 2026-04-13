{{-- Period Filter --}}
<div class="mb-6">
    <div class="inline-flex rounded-apple-lg overflow-hidden auto-soft-card" style="background: var(--dark-bg-tertiary);">
        @foreach(['24hours' => '24 Jam', '7days' => '7 Hari', '30days' => '30 Hari', '90days' => '90 Hari'] as $key => $label)
            <a href="{{ route('auto-post.index', ['tab' => 'analytics', 'period' => $key]) }}" 
               class="px-4 py-2 text-sm font-medium transition-apple {{ $period === $key ? 'bg-apple-blue text-white' : 'text-dark-text-secondary hover:bg-white/5' }}">
                {{ $label }}
            </a>
        @endforeach
    </div>
</div>

{{-- Overview Stats --}}
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="bg-dark-bg-tertiary rounded-apple-lg p-5 auto-soft-card">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-dark-text-secondary">Artikel Dibuat</p>
                <p class="text-xl font-bold text-white mt-2">{{ $stats['total_articles'] ?? 0 }}</p>
            </div>
            <div class="w-12 h-12 rounded-apple-lg flex items-center justify-center" style="background: rgba(10,132,255,0.15);">
                <i class="fas fa-file-alt text-2xl text-apple-blue"></i>
            </div>
        </div>
    </div>

    <div class="bg-dark-bg-tertiary rounded-apple-lg p-5 auto-soft-card">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-dark-text-secondary">Success Rate</p>
                <p class="text-xl font-bold text-apple-green mt-2">{{ $stats['success_rate'] ?? 0 }}%</p>
            </div>
            <div class="w-12 h-12 rounded-apple-lg flex items-center justify-center" style="background: rgba(52,199,89,0.15);">
                <i class="fas fa-check-circle text-2xl text-apple-green"></i>
            </div>
        </div>
    </div>

    <div class="bg-dark-bg-tertiary rounded-apple-lg p-5 auto-soft-card">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-dark-text-secondary">Topics Tersedia</p>
                <p class="text-xl font-bold text-white mt-2">{{ $stats['available_topics'] ?? 0 }}</p>
            </div>
            <div class="w-12 h-12 rounded-apple-lg flex items-center justify-center" style="background: rgba(175,82,222,0.15);">
                <i class="fas fa-lightbulb text-2xl text-apple-purple"></i>
            </div>
        </div>
    </div>

    <div class="bg-dark-bg-tertiary rounded-apple-lg p-5 auto-soft-card">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-dark-text-secondary">Pending</p>
                <p class="text-xl font-bold text-apple-yellow mt-2">{{ $stats['pending_schedules'] ?? 0 }}</p>
            </div>
            <div class="w-12 h-12 rounded-apple-lg flex items-center justify-center" style="background: rgba(255,214,10,0.15);">
                <i class="fas fa-clock text-2xl text-apple-yellow"></i>
            </div>
        </div>
    </div>
</div>

{{-- Performance Metrics --}}
<div class="bg-dark-bg-tertiary rounded-apple-lg p-5 auto-soft-card mb-6">
    <h2 class="text-sm font-semibold text-white mb-4">Performance Metrics</h2>
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
        <div class="text-center p-4 rounded-apple-lg" style="background: var(--dark-bg-secondary);">
            <p class="text-lg font-bold text-white">{{ $performanceMetrics['total_attempts'] ?? 0 }}</p>
            <p class="text-xs text-dark-text-secondary mt-1">Total Attempts</p>
        </div>
        <div class="text-center p-4 rounded-apple-lg" style="background: rgba(52,199,89,0.1);">
            <p class="text-2xl font-bold text-apple-green">{{ $performanceMetrics['successful'] ?? 0 }}</p>
            <p class="text-xs text-dark-text-secondary mt-1">Successful</p>
        </div>
        <div class="text-center p-4 rounded-apple-lg" style="background: rgba(255,69,58,0.1);">
            <p class="text-2xl font-bold text-apple-red">{{ $performanceMetrics['failed'] ?? 0 }}</p>
            <p class="text-xs text-dark-text-secondary mt-1">Failed</p>
        </div>
        <div class="text-center p-4 rounded-apple-lg" style="background: rgba(255,214,10,0.1);">
            <p class="text-2xl font-bold text-apple-yellow">{{ $performanceMetrics['quality_issues'] ?? 0 }}</p>
            <p class="text-xs text-dark-text-secondary mt-1">Quality Issues</p>
        </div>
        <div class="text-center p-4 rounded-apple-lg" style="background: rgba(255,149,0,0.1);">
            <p class="text-2xl font-bold text-apple-orange">{{ $performanceMetrics['duplicates'] ?? 0 }}</p>
            <p class="text-xs text-dark-text-secondary mt-1">Duplicates</p>
        </div>
    </div>
</div>

{{-- Daily Generation Chart --}}
@if(isset($dailyGeneration) && $dailyGeneration->count() > 0)
<div class="bg-dark-bg-tertiary rounded-apple-lg p-5 auto-soft-card mb-6">
    <h2 class="text-sm font-semibold text-white mb-4">Daily Generation</h2>
    <div class="h-64 flex items-end justify-between space-x-2">
        @php $maxCount = $dailyGeneration->max('count') ?: 1; @endphp
        @foreach($dailyGeneration as $day)
            <div class="flex-1 flex flex-col items-center">
                <div class="w-full rounded-t relative" style="height: {{ ($day->count / $maxCount) * 100 }}%; min-height: 4px; background: var(--dark-bg-secondary);">
                    @if($day->count > 0)
                        <div class="absolute bottom-0 left-0 right-0 bg-apple-green rounded-t" style="height: {{ ($day->success / $day->count) * 100 }}%;"></div>
                    @endif
                </div>
                <div class="text-xs text-dark-text-secondary mt-2">
                    {{ \Carbon\Carbon::parse($day->date)->format('d M') }}
                </div>
            </div>
        @endforeach
    </div>
    <div class="flex items-center justify-center space-x-4 mt-4 text-xs">
        <div class="flex items-center">
            <div class="w-3 h-3 bg-apple-green rounded mr-1"></div>
            <span class="text-dark-text-secondary">Success</span>
        </div>
        <div class="flex items-center">
            <div class="w-3 h-3 bg-apple-red rounded mr-1"></div>
            <span class="text-dark-text-secondary">Failed</span>
        </div>
    </div>
</div>
@endif

{{-- Category Distribution --}}
@if(isset($categoryDistribution) && $categoryDistribution->count() > 0)
<div class="bg-dark-bg-tertiary rounded-apple-lg p-5 auto-soft-card">
    <h2 class="text-sm font-semibold text-white mb-4">Category Distribution</h2>
    <div class="space-y-3">
        @foreach($categoryDistribution as $cat)
            @php
                $percentage = ($stats['total_articles'] ?? 0) > 0 ? ($cat->count / $stats['total_articles']) * 100 : 0;
            @endphp
            <div>
                <div class="flex justify-between text-sm mb-1">
                    <span class="text-dark-text-primary">{{ ucfirst($cat->category) }}</span>
                    <span class="text-dark-text-secondary">{{ $cat->count }} ({{ number_format($percentage, 1) }}%)</span>
                </div>
                <div class="w-full rounded-full h-2" style="background: var(--dark-bg-secondary);">
                    <div class="bg-apple-blue h-2 rounded-full" style="width: {{ $percentage }}%"></div>
                </div>
            </div>
        @endforeach
    </div>
</div>
@else
<div class="bg-dark-bg-tertiary rounded-apple-lg p-8 auto-soft-card text-center">
    <i class="fas fa-chart-bar text-4xl text-dark-text-tertiary mb-3"></i>
    <p class="text-dark-text-secondary">Belum ada data kategori</p>
</div>
@endif
