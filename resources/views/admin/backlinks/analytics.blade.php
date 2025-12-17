@extends('layouts.app')

@section('title', 'Backlink Analytics')

@section('content')
<div class="container-custom">
    {{-- Hero Header --}}
    <section class="card-apple p-5 md:p-6 relative overflow-hidden mb-6">
        <!-- Background Gradient Effects -->
        <div class="absolute inset-0 pointer-events-none" aria-hidden="true">
            <div class="w-72 h-72 bg-apple-purple opacity-30 blur-3xl rounded-full absolute -top-16 -right-10"></div>
            <div class="w-48 h-48 bg-apple-blue opacity-20 blur-2xl rounded-full absolute bottom-0 left-10"></div>
        </div>

        <div class="relative space-y-4">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <div class="space-y-2">
                    <p class="text-xs uppercase tracking-[0.4em]" style="color: rgba(235,235,245,0.5);">Performance Tracking</p>
                    <h1 class="text-2xl md:text-3xl font-bold" style="color: #FFFFFF;">
                        <i class="fas fa-chart-line mr-2"></i>Backlink Analytics
                    </h1>
                    <p class="text-sm" style="color: rgba(235,235,245,0.75);">
                        Comprehensive backlink performance and campaign analytics
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

    {{-- Overview Stats --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="stat-card-apple">
            <div class="stat-icon-apple" style="background: rgba(10,132,255,0.15); color: var(--apple-blue);">
                <i class="fas fa-link"></i>
            </div>
            <div>
                <div class="stat-value-apple">{{ $analytics['total_backlinks'] }}</div>
                <div class="stat-label-apple">Total Backlinks</div>
            </div>
        </div>

        <div class="stat-card-apple">
            <div class="stat-icon-apple" style="background: rgba(48,209,88,0.15); color: var(--apple-green);">
                <i class="fas fa-check-circle"></i>
            </div>
            <div>
                <div class="stat-value-apple">{{ $analytics['indexed_backlinks'] }}</div>
                <div class="stat-label-apple">Indexed</div>
            </div>
        </div>

        <div class="stat-card-apple">
            <div class="stat-icon-apple" style="background: rgba(255,159,10,0.15); color: var(--apple-orange);">
                <i class="fas fa-chart-bar"></i>
            </div>
            <div>
                <div class="stat-value-apple">{{ $analytics['avg_da'] }}</div>
                <div class="stat-label-apple">Avg Domain Authority</div>
            </div>
        </div>

        <div class="stat-card-apple">
            <div class="stat-icon-apple" style="background: rgba(175,82,222,0.15); color: var(--apple-purple);">
                <i class="fas fa-percentage"></i>
            </div>
            <div>
                <div class="stat-value-apple">{{ $analytics['success_rate'] }}%</div>
                <div class="stat-label-apple">Success Rate</div>
            </div>
        </div>
    </div>

    {{-- Quality & Outreach Metrics --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <div class="card-apple p-6">
            <h3 class="text-lg font-semibold text-dark-text-primary mb-4">
                <i class="fas fa-chart-pie mr-2 text-apple-blue"></i>Quality Metrics
            </h3>
            <div class="space-y-4">
                <div class="flex justify-between items-center">
                    <span class="text-sm text-dark-text-secondary">Dofollow Links</span>
                    <div class="flex items-center gap-2">
                        <div class="w-32 h-2 bg-dark-bg-tertiary rounded-full overflow-hidden">
                            <div class="h-full bg-apple-green" style="width: {{ $analytics['total_backlinks'] > 0 ? ($analytics['dofollow_count'] / $analytics['total_backlinks'] * 100) : 0 }}%"></div>
                        </div>
                        <span class="text-sm font-semibold text-dark-text-primary">{{ $analytics['dofollow_count'] }}</span>
                    </div>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-dark-text-secondary">Nofollow Links</span>
                    <div class="flex items-center gap-2">
                        <div class="w-32 h-2 bg-dark-bg-tertiary rounded-full overflow-hidden">
                            <div class="h-full bg-dark-text-secondary" style="width: {{ $analytics['total_backlinks'] > 0 ? ($analytics['nofollow_count'] / $analytics['total_backlinks'] * 100) : 0 }}%"></div>
                        </div>
                        <span class="text-sm font-semibold text-dark-text-primary">{{ $analytics['nofollow_count'] }}</span>
                    </div>
                </div>
                <div class="pt-4 border-t" style="border-color: var(--dark-separator);">
                    <div class="flex justify-between">
                        <span class="text-sm text-dark-text-secondary">Total Domain Authority</span>
                        <span class="text-sm font-semibold text-apple-blue">{{ $analytics['total_da'] }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-apple p-6">
            <h3 class="text-lg font-semibold text-dark-text-primary mb-4">
                <i class="fas fa-envelope mr-2 text-apple-purple"></i>Outreach Metrics
            </h3>
            <div class="space-y-4">
                <div class="flex justify-between items-center">
                    <span class="text-sm text-dark-text-secondary">Total Targets</span>
                    <span class="text-sm font-semibold text-dark-text-primary">{{ $analytics['total_targets'] }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-dark-text-secondary">Contacted</span>
                    <span class="text-sm font-semibold text-apple-blue">{{ $analytics['contacted'] }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-dark-text-secondary">Responded</span>
                    <span class="text-sm font-semibold text-apple-orange">{{ $analytics['responded'] }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-dark-text-secondary">Acquired</span>
                    <span class="text-sm font-semibold text-apple-green">{{ $analytics['acquired'] }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Backlinks by Category --}}
    @if($byCategory->count() > 0)
    <div class="card-apple p-6 mb-6">
        <h3 class="text-lg font-semibold text-dark-text-primary mb-4">
            <i class="fas fa-layer-group mr-2 text-apple-green"></i>Backlinks by Category
        </h3>
        <div class="space-y-3">
            @foreach($byCategory as $category)
            <div class="flex justify-between items-center">
                <span class="text-sm text-dark-text-primary">{{ $category->category }}</span>
                <div class="flex items-center gap-2">
                    <div class="w-48 h-2 bg-dark-bg-tertiary rounded-full overflow-hidden">
                        <div class="h-full bg-apple-blue" style="width: {{ ($category->count / $byCategory->sum('count')) * 100 }}%"></div>
                    </div>
                    <span class="text-sm font-semibold text-dark-text-primary w-8 text-right">{{ $category->count }}</span>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Monthly Trend --}}
    @if($monthlyTrend->count() > 0)
    <div class="card-apple p-6">
        <h3 class="text-lg font-semibold text-dark-text-primary mb-4">
            <i class="fas fa-calendar-alt mr-2 text-apple-orange"></i>Monthly Acquisition Trend
        </h3>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="text-xs text-dark-text-tertiary uppercase">
                    <tr>
                        <th class="text-left pb-3">Month</th>
                        <th class="text-right pb-3">Backlinks</th>
                        <th class="text-right pb-3">Avg DA</th>
                    </tr>
                </thead>
                <tbody class="divide-y" style="border-color: var(--dark-separator);">
                    @foreach($monthlyTrend as $trend)
                    <tr>
                        <td class="py-3 text-sm text-dark-text-primary">{{ \Carbon\Carbon::parse($trend->month . '-01')->format('M Y') }}</td>
                        <td class="py-3 text-sm text-right font-semibold text-apple-blue">{{ $trend->count }}</td>
                        <td class="py-3 text-sm text-right text-dark-text-secondary">{{ round($trend->avg_da, 1) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>
@endsection
