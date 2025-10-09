@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
    {{-- Hero Metrics Section --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        {{-- Active Projects --}}
        <div class="card-elevated rounded-apple-lg p-6 hover-lift cursor-pointer transition-apple" onclick="window.location='{{ route('projects.index') }}'">
            <div class="flex items-start justify-between mb-4">
                <div class="flex-1">
                    <p class="text-sm font-medium" style="color: rgba(255,255,255,0.6);">Proyek Aktif</p>
                    <div class="flex items-baseline mt-2">
                        <h2 class="text-4xl font-bold text-white">{{ $stats['active_projects'] ?? 0 }}</h2>
                        @php
                            $inProgressTasks = $stats['in_progress_tasks'] ?? 0;
                            $totalActiveTasks = ($stats['in_progress_tasks'] ?? 0) + ($stats['pending_tasks'] ?? 0);
                        @endphp
                        <span class="ml-3 text-sm font-medium" style="color: rgba(10, 132, 255, 1);">
                            {{ $inProgressTasks }} tasks
                        </span>
                    </div>
                    <p class="text-xs mt-2" style="color: rgba(255,255,255,0.4);">{{ $stats['completed_projects'] ?? 0 }} selesai, {{ $stats['overdue_projects'] ?? 0 }} terlambat</p>
                </div>
                <div class="p-3 rounded-2xl" style="background: linear-gradient(135deg, rgba(10, 132, 255, 0.2) 0%, rgba(10, 132, 255, 0.05) 100%);">
                    <i class="fas fa-project-diagram text-2xl" style="color: rgba(10, 132, 255, 1);"></i>
                </div>
            </div>
            <div class="flex items-center justify-between pt-3 border-t" style="border-color: rgba(255,255,255,0.1);">
                @php
                    $nearestDeadline = collect($upcoming_deadlines['projects'] ?? [])->first();
                @endphp
                @if($nearestDeadline)
                    <span class="text-xs" style="color: rgba(255,255,255,0.5);">
                        <i class="fas fa-clock mr-1"></i>
                        Terdekat: {{ \Carbon\Carbon::parse($nearestDeadline->deadline)->format('d M Y') }}
                    </span>
                @else
                    <span class="text-xs" style="color: rgba(52, 199, 89, 1);">
                        <i class="fas fa-check-circle mr-1"></i>
                        Semua on-track
                    </span>
                @endif
                <i class="fas fa-arrow-right text-xs" style="color: rgba(10, 132, 255, 0.6);"></i>
            </div>
        </div>

        {{-- Monthly Revenue --}}
        <div class="card-elevated rounded-apple-lg p-6 hover-lift cursor-pointer transition-apple" onclick="window.location='{{ route('cash-accounts.index') }}'">
            <div class="flex items-start justify-between mb-4">
                <div class="flex-1">
                    <p class="text-sm font-medium" style="color: rgba(255,255,255,0.6);">Pendapatan Bulan Ini</p>
                    <div class="flex items-baseline mt-2">
                        <h2 class="text-4xl font-bold text-white">{{ number_format(($stats['payments_this_month'] ?? 0) / 1000000, 1) }}</h2>
                        <span class="ml-1 text-xl font-medium" style="color: rgba(255,255,255,0.6);">M</span>
                    </div>
                    @php
                        $netProfit = ($stats['payments_this_month'] ?? 0) - ($stats['expenses_this_month'] ?? 0);
                        $isProfit = $netProfit >= 0;
                    @endphp
                    <p class="text-xs mt-2" style="color: {{ $isProfit ? 'rgba(52, 199, 89, 1)' : 'rgba(255, 59, 48, 1)' }};">
                        <i class="fas fa-{{ $isProfit ? 'arrow-up' : 'arrow-down' }} mr-1"></i>
                        Net: Rp {{ number_format(abs($netProfit) / 1000000, 1) }}M
                    </p>
                </div>
                <div class="p-3 rounded-2xl" style="background: linear-gradient(135deg, rgba(52, 199, 89, 0.2) 0%, rgba(52, 199, 89, 0.05) 100%);">
                    <i class="fas fa-sack-dollar text-2xl" style="color: rgba(52, 199, 89, 1);"></i>
                </div>
            </div>
            <div class="flex items-center justify-between pt-3 border-t" style="border-color: rgba(255,255,255,0.1);">
                <span class="text-xs" style="color: rgba(255,255,255,0.5);">
                    <i class="fas fa-chart-line mr-1"></i>
                    Pengeluaran: Rp {{ number_format(($stats['expenses_this_month'] ?? 0) / 1000000, 1) }}M
                </span>
                <i class="fas fa-arrow-right text-xs" style="color: rgba(52, 199, 89, 0.6);"></i>
            </div>
        </div>

        {{-- Project Health Score --}}
        <div class="card-elevated rounded-apple-lg p-6 hover-lift transition-apple">
            <div class="flex items-start justify-between mb-4">
                <div class="flex-1">
                    <p class="text-sm font-medium" style="color: rgba(255,255,255,0.6);">Skor Kesehatan</p>
                    @php
                        // Health score calculation based on multiple factors
                        $healthScore = 100;
                        $totalProjects = max(1, $stats['total_projects'] ?? 1);
                        $activeProjects = $stats['active_projects'] ?? 0;
                        
                        // Penalty for overdue items (max -40)
                        $overdueProjects = $stats['overdue_projects'] ?? 0;
                        $overdueTasks = $stats['overdue_tasks'] ?? 0;
                        $healthScore -= min(40, ($overdueProjects * 10) + ($overdueTasks * 2));
                        
                        // Penalty for low completion rate (max -30)
                        $completionRate = $stats['completion_rate'] ?? 0;
                        $healthScore -= max(0, (100 - $completionRate) * 0.3);
                        
                        // Penalty for pending reviews (max -10)
                        $pendingReviews = $stats['pending_reviews'] ?? 0;
                        $healthScore -= min(10, $pendingReviews * 2);
                        
                        // Penalty for cash flow issues (max -20)
                        $cashBalance = $stats['total_cash_balance'] ?? 0;
                        $expenses = $stats['expenses_this_month'] ?? 1;
                        $cashRatio = $expenses > 0 ? ($cashBalance / $expenses) : 10;
                        if ($cashRatio < 2) {
                            $healthScore -= min(20, (2 - $cashRatio) * 10);
                        }
                        
                        $healthScore = max(0, min(100, round($healthScore)));
                        $healthColor = $healthScore >= 80 ? 'rgba(52, 199, 89, 1)' : ($healthScore >= 60 ? 'rgba(255, 149, 0, 1)' : 'rgba(255, 59, 48, 1)');
                        $healthIcon = $healthScore >= 80 ? 'fa-check-circle' : ($healthScore >= 60 ? 'fa-exclamation-triangle' : 'fa-times-circle');
                        $healthLabel = $healthScore >= 80 ? 'Excellent' : ($healthScore >= 60 ? 'Good' : 'Critical');
                    @endphp
                    <div class="flex items-baseline mt-2">
                        <h2 class="text-4xl font-bold" style="color: {{ $healthColor }};">{{ $healthScore }}</h2>
                        <span class="ml-2 text-xl font-medium" style="color: rgba(255,255,255,0.6);">/100</span>
                    </div>
                    <p class="text-xs mt-2" style="color: rgba(255,255,255,0.4);">
                        {{ $completionRate }}% completion • {{ $activeProjects }} aktif
                    </p>
                </div>
                <div class="p-3 rounded-2xl" style="background: linear-gradient(135deg, {{ $healthColor }}20 0%, {{ $healthColor }}05 100%);">
                    <i class="fas {{ $healthIcon }} text-2xl" style="color: {{ $healthColor }};"></i>
                </div>
            </div>
            <div class="flex items-center justify-between pt-3 border-t" style="border-color: rgba(255,255,255,0.1);">
                <span class="text-xs" style="color: rgba(255,255,255,0.5);">
                    <i class="fas fa-tasks mr-1"></i>
                    {{ $overdueProjects + $overdueTasks }} item terlambat
                </span>
                <span class="px-2 py-1 rounded-full text-xs font-medium" style="background: {{ $healthColor }}20; color: {{ $healthColor }};">
                    {{ $healthLabel }}
                </span>
            </div>
        </div>
    </div>

    {{-- Analytics & Insights Section --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-6">
        {{-- Project Status Distribution --}}
        <div class="lg:col-span-1 card-elevated rounded-apple-lg overflow-hidden">
            <div class="p-4 border-b" style="border-color: rgba(255,255,255,0.1);">
                <h3 class="text-lg font-semibold text-white">Distribusi Status</h3>
                <p class="text-xs mt-1" style="color: rgba(255,255,255,0.5);">Status proyek saat ini</p>
            </div>
            <div class="p-4">
                <div class="relative" style="height: 200px;">
                    <canvas id="projectStatusChart"></canvas>
                </div>
            </div>
        </div>

        {{-- Monthly Progress Trend --}}
        <div class="lg:col-span-2 card-elevated rounded-apple-lg overflow-hidden">
            <div class="p-4 border-b" style="border-color: rgba(255,255,255,0.1);">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-white">Trend Progress</h3>
                        <p class="text-xs mt-1" style="color: rgba(255,255,255,0.5);">6 bulan terakhir</p>
                    </div>
                    <div class="flex items-center space-x-3">
                        <div class="flex items-center">
                            <div class="w-3 h-3 rounded-full mr-2" style="background: rgba(52, 199, 89, 1);"></div>
                            <span class="text-xs" style="color: rgba(255,255,255,0.6);">Selesai</span>
                        </div>
                        <div class="flex items-center">
                            <div class="w-3 h-3 rounded-full mr-2" style="background: rgba(10, 132, 255, 1);"></div>
                            <span class="text-xs" style="color: rgba(255,255,255,0.6);">Baru</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="p-4">
                <div class="relative" style="height: 200px;">
                    <canvas id="monthlyProgressChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- Action Required & Quick Stats --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-6">
        {{-- Needs Attention --}}
        <div class="card-elevated rounded-apple-lg overflow-hidden">
            <div class="p-4 border-b" style="border-color: rgba(255,255,255,0.1);">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-white">Perlu Perhatian</h3>
                        <p class="text-xs mt-1" style="color: rgba(255,255,255,0.5);">Items yang membutuhkan action</p>
                    </div>
                    @php
                        $alertCount = ($stats['overdue_projects'] ?? 0) + ($stats['overdue_tasks'] ?? 0) + ($stats['pending_reviews'] ?? 0);
                        $upcomingCount = count($upcoming_deadlines['projects'] ?? []) + count($upcoming_deadlines['tasks'] ?? []);
                    @endphp
                    @if($alertCount > 0)
                    <span class="px-2.5 py-1 rounded-full text-xs font-semibold" style="background: rgba(255, 59, 48, 0.2); color: rgba(255, 59, 48, 1);">
                        {{ $alertCount }} urgent
                    </span>
                    @endif
                </div>
            </div>
            <div class="p-4 space-y-2.5">
                {{-- Overdue Projects --}}
                @if(($stats['overdue_projects'] ?? 0) > 0)
                <a href="{{ route('projects.index') }}?filter=overdue" class="block p-3 rounded-xl hover:bg-dark-elevated-2 transition-apple" style="background: rgba(255, 59, 48, 0.08);">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center min-w-0 flex-1">
                            <div class="w-10 h-10 rounded-xl flex items-center justify-center mr-3 flex-shrink-0" style="background: rgba(255, 59, 48, 0.2);">
                                <i class="fas fa-exclamation-triangle" style="color: rgba(255, 59, 48, 1);"></i>
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="text-sm font-semibold text-white">{{ $stats['overdue_projects'] }} Proyek Terlambat</p>
                                <p class="text-xs truncate" style="color: rgba(255,255,255,0.5);">Melewati deadline, butuh tindakan segera</p>
                            </div>
                        </div>
                        <i class="fas fa-chevron-right text-xs ml-2 flex-shrink-0" style="color: rgba(255,255,255,0.3);"></i>
                    </div>
                </a>
                @endif

                {{-- Overdue Tasks --}}
                @if(($stats['overdue_tasks'] ?? 0) > 0)
                <a href="{{ route('tasks.index') }}?filter=overdue" class="block p-3 rounded-xl hover:bg-dark-elevated-2 transition-apple" style="background: rgba(255, 149, 0, 0.08);">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center min-w-0 flex-1">
                            <div class="w-10 h-10 rounded-xl flex items-center justify-center mr-3 flex-shrink-0" style="background: rgba(255, 149, 0, 0.2);">
                                <i class="fas fa-clock" style="color: rgba(255, 149, 0, 1);"></i>
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="text-sm font-semibold text-white">{{ $stats['overdue_tasks'] }} Tugas Terlambat</p>
                                <p class="text-xs truncate" style="color: rgba(255,255,255,0.5);">Perlu diselesaikan segera</p>
                            </div>
                        </div>
                        <i class="fas fa-chevron-right text-xs ml-2 flex-shrink-0" style="color: rgba(255,255,255,0.3);"></i>
                    </div>
                </a>
                @endif

                {{-- Pending Reviews --}}
                @if(($stats['pending_reviews'] ?? 0) > 0)
                <a href="{{ route('documents.index') }}?status=review" class="block p-3 rounded-xl hover:bg-dark-elevated-2 transition-apple" style="background: rgba(191, 90, 242, 0.08);">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center min-w-0 flex-1">
                            <div class="w-10 h-10 rounded-xl flex items-center justify-center mr-3 flex-shrink-0" style="background: rgba(191, 90, 242, 0.2);">
                                <i class="fas fa-file-alt" style="color: rgba(191, 90, 242, 1);"></i>
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="text-sm font-semibold text-white">{{ $stats['pending_reviews'] }} Dokumen Review</p>
                                <p class="text-xs truncate" style="color: rgba(255,255,255,0.5);">Menunggu persetujuan</p>
                            </div>
                        </div>
                        <i class="fas fa-chevron-right text-xs ml-2 flex-shrink-0" style="color: rgba(255,255,255,0.3);"></i>
                    </div>
                </a>
                @endif

                {{-- Upcoming Deadlines Warning --}}
                @if($alertCount == 0 && $upcomingCount > 0)
                <div class="p-3 rounded-xl" style="background: rgba(10, 132, 255, 0.08);">
                    <div class="flex items-center">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center mr-3 flex-shrink-0" style="background: rgba(10, 132, 255, 0.2);">
                            <i class="fas fa-calendar-alt" style="color: rgba(10, 132, 255, 1);"></i>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-semibold text-white">{{ $upcomingCount }} Deadline 30 Hari</p>
                            <p class="text-xs truncate" style="color: rgba(255,255,255,0.5);">Monitor progress untuk hindari keterlambatan</p>
                        </div>
                    </div>
                </div>
                @endif

                {{-- All Clear State --}}
                @if($alertCount == 0 && $upcomingCount == 0)
                <div class="text-center py-8">
                    <div class="w-16 h-16 rounded-full mx-auto mb-3 flex items-center justify-center" style="background: rgba(52, 199, 89, 0.1);">
                        <i class="fas fa-check-circle text-3xl" style="color: rgba(52, 199, 89, 1);"></i>
                    </div>
                    <p class="text-sm font-medium text-white">Semua Lancar!</p>
                    <p class="text-xs mt-1" style="color: rgba(255,255,255,0.5);">Tidak ada yang perlu perhatian khusus</p>
                </div>
                @endif
            </div>
        </div>

        {{-- Quick Stats & Actions --}}
        <div class="card-elevated rounded-apple-lg overflow-hidden">
            <div class="p-4 border-b" style="border-color: rgba(255,255,255,0.1);">
                <h3 class="text-lg font-semibold text-white">Statistik Cepat</h3>
                <p class="text-xs mt-1" style="color: rgba(255,255,255,0.5);">Ringkasan metrics penting</p>
            </div>
            <div class="p-4">
                {{-- Stats Grid --}}
                <div class="grid grid-cols-2 gap-3 mb-4">
                    {{-- Tasks Today --}}
                    <div class="p-3 rounded-xl" style="background: rgba(84,84,88,0.3);">
                        <div class="flex items-center justify-between mb-2">
                            <i class="fas fa-tasks" style="color: rgba(10, 132, 255, 0.8);"></i>
                            <span class="text-xs font-medium" style="color: rgba(255,255,255,0.5);">Tasks</span>
                        </div>
                        <p class="text-2xl font-bold text-white">{{ $stats['total_tasks'] ?? 0 }}</p>
                        <p class="text-xs mt-1" style="color: rgba(52, 199, 89, 1);">
                            {{ $stats['completed_tasks'] ?? 0 }} selesai
                        </p>
                    </div>

                    {{-- Documents --}}
                    <div class="p-3 rounded-xl" style="background: rgba(84,84,88,0.3);">
                        <div class="flex items-center justify-between mb-2">
                            <i class="fas fa-file-alt" style="color: rgba(191, 90, 242, 0.8);"></i>
                            <span class="text-xs font-medium" style="color: rgba(255,255,255,0.5);">Docs</span>
                        </div>
                        <p class="text-2xl font-bold text-white">{{ $stats['total_documents'] ?? 0 }}</p>
                        <p class="text-xs mt-1" style="color: rgba(191, 90, 242, 1);">
                            {{ $stats['documents_this_month'] ?? 0 }} bulan ini
                        </p>
                    </div>

                    {{-- Budget Utilization --}}
                    <div class="p-3 rounded-xl" style="background: rgba(84,84,88,0.3);">
                        <div class="flex items-center justify-between mb-2">
                            <i class="fas fa-chart-pie" style="color: rgba(255, 149, 0, 0.8);"></i>
                            <span class="text-xs font-medium" style="color: rgba(255,255,255,0.5);">Budget</span>
                        </div>
                        <p class="text-2xl font-bold text-white">{{ number_format($stats['budget_utilization'] ?? 0, 0) }}%</p>
                        <p class="text-xs mt-1" style="color: rgba(255, 149, 0, 1);">
                            Utilization
                        </p>
                    </div>

                    {{-- Institutions --}}
                    <div class="p-3 rounded-xl" style="background: rgba(84,84,88,0.3);">
                        <div class="flex items-center justify-between mb-2">
                            <i class="fas fa-building" style="color: rgba(52, 199, 89, 0.8);"></i>
                            <span class="text-xs font-medium" style="color: rgba(255,255,255,0.5);">Clients</span>
                        </div>
                        <p class="text-2xl font-bold text-white">{{ $stats['total_institutions'] ?? 0 }}</p>
                        <p class="text-xs mt-1" style="color: rgba(52, 199, 89, 1);">
                            Active
                        </p>
                    </div>
                </div>

                {{-- Quick Actions --}}
                <div class="space-y-2 pt-3 border-t" style="border-color: rgba(255,255,255,0.1);">
                    <a href="{{ route('projects.create') }}" class="block w-full px-4 py-3 rounded-xl text-center font-medium text-sm transition-apple" style="background: linear-gradient(135deg, rgba(10, 132, 255, 1) 0%, rgba(10, 132, 255, 0.8) 100%); color: white;">
                        <i class="fas fa-plus-circle mr-2"></i>
                        Proyek Baru
                    </a>
                    <div class="grid grid-cols-2 gap-2">
                        <a href="{{ route('tasks.index') }}" class="px-3 py-2 rounded-xl text-center text-xs font-medium transition-apple" style="background: rgba(84,84,88,0.4); color: rgba(255,255,255,0.8);">
                            <i class="fas fa-tasks mr-1"></i>
                            Lihat Tasks
                        </a>
                        <a href="{{ route('documents.index') }}" class="px-3 py-2 rounded-xl text-center text-xs font-medium transition-apple" style="background: rgba(84,84,88,0.4); color: rgba(255,255,255,0.8);">
                            <i class="fas fa-folder mr-1"></i>
                            Dokumen
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Color Palette
    const colors = {
        blue: '#0A84FF',
        green: '#34C759',
        red: '#FF3B30',
        orange: '#FF9500',
        yellow: '#FFD60A',
        purple: '#BF5AF2',
        teal: '#64D2FF'
    };

    // Chart.js Global Config
    Chart.defaults.font.family = 'Inter, -apple-system, BlinkMacSystemFont, sans-serif';
    Chart.defaults.color = 'rgba(255, 255, 255, 0.6)';
    Chart.defaults.borderColor = 'rgba(255, 255, 255, 0.1)';

    // Project Status Doughnut Chart
    const statusCtx = document.getElementById('projectStatusChart');
    if (statusCtx) {
        const statusData = @json($projects_by_status ?? []);
        
        if (Array.isArray(statusData) && statusData.length > 0) {
            const chartColors = [colors.blue, colors.green, colors.orange, colors.red, colors.purple, colors.teal];
            
            new Chart(statusCtx, {
                type: 'bar',
                data: {
                    labels: statusData.map(item => item.name || ''),
                    datasets: [{
                        label: 'Proyek',
                        data: statusData.map(item => item.projects_count ?? item.count ?? 0),
                        backgroundColor: chartColors.map((c, i) => c + '60'),
                        borderColor: chartColors,
                        borderWidth: 2,
                        borderRadius: 6
                    }]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    maintainAspectRatio: false,
                    layout: {
                        padding: {
                            top: 5,
                            right: 15,
                            bottom: 5,
                            left: 5
                        }
                    },
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: 'rgba(28, 28, 30, 0.95)',
                            padding: 12,
                            cornerRadius: 8,
                            titleFont: { size: 13, weight: 'bold' },
                            bodyFont: { size: 12 },
                            borderWidth: 0,
                            callbacks: {
                                label: function(context) {
                                    return context.parsed.x + ' proyek';
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(255, 255, 255, 0.05)',
                                borderColor: 'rgba(255, 255, 255, 0.1)'
                            },
                            ticks: {
                                font: { size: 10 },
                                color: 'rgba(255, 255, 255, 0.5)',
                                stepSize: 1,
                                padding: 5
                            }
                        },
                        y: {
                            grid: { display: false },
                            ticks: {
                                font: { size: 11, weight: '500' },
                                color: 'rgba(255, 255, 255, 0.8)',
                                padding: 10,
                                autoSkip: false
                            }
                        }
                    }
                }
            });
        }
    }

    // Monthly Progress Line Chart
    const progressCtx = document.getElementById('monthlyProgressChart');
    if (progressCtx) {
        const monthlyData = @json($monthly_progress ?? []);
        
        if (Array.isArray(monthlyData) && monthlyData.length > 0) {
            new Chart(progressCtx, {
                type: 'line',
                data: {
                    labels: monthlyData.map(item => item.month || ''),
                    datasets: [{
                        label: 'Selesai',
                        data: monthlyData.map(item => item.completed ?? 0),
                        borderColor: colors.green,
                        backgroundColor: colors.green + '20',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4,
                        pointBackgroundColor: colors.green,
                        pointBorderColor: '#1C1C1E',
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        pointHoverRadius: 6
                    }, {
                        label: 'Baru',
                        data: monthlyData.map(item => item.created ?? 0),
                        borderColor: colors.blue,
                        backgroundColor: colors.blue + '20',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4,
                        pointBackgroundColor: colors.blue,
                        pointBorderColor: '#1C1C1E',
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        pointHoverRadius: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: { intersect: false, mode: 'index' },
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: 'rgba(28, 28, 30, 0.95)',
                            padding: 12,
                            cornerRadius: 8,
                            titleFont: { size: 13, weight: 'bold' },
                            bodyFont: { size: 12 }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(255, 255, 255, 0.05)',
                                borderColor: 'rgba(255, 255, 255, 0.1)'
                            },
                            ticks: {
                                font: { size: 11 },
                                color: 'rgba(255, 255, 255, 0.5)'
                            }
                        },
                        x: {
                            grid: { display: false },
                            ticks: {
                                font: { size: 11 },
                                color: 'rgba(255, 255, 255, 0.5)'
                            }
                        }
                    }
                }
            });
        }
    }
});
</script>
@endpush
