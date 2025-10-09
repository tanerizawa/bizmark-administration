@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
    <!-- Quick Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-2.5 mb-3">
        <!-- Total Projects -->
        <div class="card-elevated rounded-apple-lg p-2.5 hover-lift">
            <div class="flex items-center">
                <div class="p-2 rounded-apple bg-gradient-to-br from-apple-blue to-blue-600 text-white">
                    <i class="fas fa-project-diagram text-sm"></i>
                </div>
                <div class="ml-3 flex-1">
                    <p class="text-xs font-medium text-dark-text-secondary">Total Proyek</p>
                    <p class="text-xl font-semibold text-dark-text-primary">{{ $stats['total_projects'] ?? 0 }}</p>
                    <div class="flex items-center mt-0.5">
                        <div class="flex items-center text-apple-green text-xs">
                            <i class="fas fa-arrow-up mr-1"></i>
                            <span>{{ $stats['completion_rate'] ?? 0 }}%</span>
                        </div>
                        <span class="text-xs text-dark-text-secondary ml-1">completion</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Active Projects -->
        <div class="card-elevated rounded-apple-lg p-2.5 hover-lift">
            <div class="flex items-center">
                <div class="p-2 rounded-apple bg-gradient-to-br from-apple-green to-green-600 text-white">
                    <i class="fas fa-play-circle text-sm"></i>
                </div>
                <div class="ml-3 flex-1">
                    <p class="text-xs font-medium text-dark-text-secondary">Proyek Aktif</p>
                    <p class="text-xl font-semibold text-dark-text-primary">{{ $stats['active_projects'] ?? 0 }}</p>
                    <p class="text-xs text-dark-text-secondary mt-0.5">sedang berjalan</p>
                </div>
            </div>
        </div>

        <!-- Total Tasks -->
        <div class="card-elevated rounded-apple-lg p-2.5 hover-lift">
            <div class="flex items-center">
                <div class="p-2 rounded-apple bg-gradient-to-br from-apple-orange to-orange-600 text-white">
                    <i class="fas fa-tasks text-sm"></i>
                </div>
                <div class="ml-3 flex-1">
                    <p class="text-xs font-medium text-dark-text-secondary">Total Tugas</p>
                    <p class="text-xl font-semibold text-dark-text-primary">{{ $stats['total_tasks'] ?? 0 }}</p>
                    <p class="text-xs text-dark-text-secondary mt-0.5">{{ $stats['completed_tasks'] ?? 0 }} selesai</p>
                </div>
            </div>
        </div>

        <!-- Total Documents -->
        <div class="card-elevated rounded-apple-lg p-2.5 hover-lift">
            <div class="flex items-center">
                <div class="p-2 rounded-apple bg-gradient-to-br from-apple-purple to-purple-600 text-white">
                    <i class="fas fa-file-alt text-sm"></i>
                </div>
                <div class="ml-3 flex-1">
                    <p class="text-xs font-medium text-dark-text-secondary">Total Dokumen</p>
                    <p class="text-xl font-semibold text-dark-text-primary">{{ $stats['total_documents'] ?? 0 }}</p>
                    <p class="text-xs text-dark-text-secondary mt-0.5">terdokumentasi</p>
                </div>
            </div>
        </div>

        <!-- Overdue Projects -->
        <div class="card-elevated rounded-apple-lg p-2.5 hover-lift">
            <div class="flex items-center">
                <div class="p-2 rounded-apple bg-gradient-to-br from-apple-red to-red-600 text-white">
                    <i class="fas fa-exclamation-triangle text-sm"></i>
                </div>
                <div class="ml-3 flex-1">
                    <p class="text-xs font-medium text-dark-text-secondary">Terlambat</p>
                    <p class="text-xl font-semibold text-dark-text-primary">{{ $stats['overdue_projects'] ?? 0 }}</p>
                    <p class="text-xs text-dark-text-secondary mt-0.5">perlu perhatian</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Financial Overview -->
    <div class="mb-3">
        <div class="flex items-center justify-between mb-2">
            <div>
                <h2 class="text-xl font-bold text-dark-text-primary">Ringkasan Keuangan</h2>
                <p class="text-sm text-dark-text-secondary">Real-time financial tracking</p>
            </div>
            <a href="{{ route('cash-accounts.index') }}" 
               class="text-sm text-apple-blue-dark hover:text-apple-blue font-medium">
                Lihat Detail <i class="fas fa-arrow-right ml-1"></i>
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-2.5">
            <!-- Total Cash Balance -->
            <div class="card-elevated rounded-apple-lg p-2.5 hover-lift">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-medium text-dark-text-secondary">Saldo Kas Total</span>
                    <i class="fas fa-wallet text-apple-blue-dark"></i>
                </div>
                <p class="text-xl font-bold text-dark-text-primary">
                    Rp {{ number_format($stats['total_cash_balance'] ?? 0, 0, ',', '.') }}
                </p>
                <div class="mt-2 text-xs text-dark-text-secondary">
                    <span>Bank: Rp {{ number_format($stats['bank_balance'] ?? 0, 0, ',', '.') }}</span><br>
                    <span>Cash: Rp {{ number_format($stats['cash_balance'] ?? 0, 0, ',', '.') }}</span>
                </div>
            </div>

            <!-- Payment This Month -->
            <div class="card-elevated rounded-apple-lg p-2.5 hover-lift">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-medium text-dark-text-secondary">Pembayaran Bulan Ini</span>
                    <i class="fas fa-arrow-down" style="color: rgba(52, 199, 89, 1);"></i>
                </div>
                <p class="text-xl font-bold" style="color: rgba(52, 199, 89, 1);">
                    Rp {{ number_format($stats['payments_this_month'] ?? 0, 0, ',', '.') }}
                </p>
                <p class="mt-2 text-xs text-dark-text-secondary">
                    Pemasukan {{ now()->format('F Y') }}
                </p>
            </div>

            <!-- Expenses This Month -->
            <div class="card-elevated rounded-apple-lg p-2.5 hover-lift">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-medium text-dark-text-secondary">Pengeluaran Bulan Ini</span>
                    <i class="fas fa-arrow-up" style="color: rgba(255, 59, 48, 1);"></i>
                </div>
                <p class="text-xl font-bold" style="color: rgba(255, 59, 48, 1);">
                    Rp {{ number_format($stats['expenses_this_month'] ?? 0, 0, ',', '.') }}
                </p>
                <p class="mt-2 text-xs text-dark-text-secondary">
                    Pengeluaran {{ now()->format('F Y') }}
                </p>
            </div>

            <!-- Total Payment Received -->
            <div class="card-elevated rounded-apple-lg p-2.5 hover-lift">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-medium text-dark-text-secondary">Total Pembayaran Diterima</span>
                    <i class="fas fa-hand-holding-usd" style="color: rgba(52, 199, 89, 1);"></i>
                </div>
                <p class="text-xl font-bold text-dark-text-primary">
                    Rp {{ number_format($stats['total_payment_received'] ?? 0, 0, ',', '.') }}
                </p>
                <p class="mt-2 text-xs text-dark-text-secondary">
                    Dari Rp {{ number_format($stats['total_contract_value'] ?? 0, 0, ',', '.') }}
                </p>
            </div>

            <!-- Outstanding Receivables -->
            <div class="card-elevated rounded-apple-lg p-2.5 hover-lift">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-medium text-dark-text-secondary">Piutang Outstanding</span>
                    <i class="fas fa-clock" style="color: rgba(255, 149, 0, 1);"></i>
                </div>
                <p class="text-xl font-bold" style="color: rgba(255, 149, 0, 1);">
                    Rp {{ number_format($stats['outstanding_receivables'] ?? 0, 0, ',', '.') }}
                </p>
                @php
                    $receivablePercentage = ($stats['total_contract_value'] ?? 0) > 0 
                        ? round((($stats['outstanding_receivables'] ?? 0) / $stats['total_contract_value']) * 100, 1) 
                        : 0;
                @endphp
                <p class="mt-2 text-xs text-dark-text-secondary">
                    {{ $receivablePercentage }}% dari kontrak
                </p>
            </div>
        </div>

        <!-- Monthly Financial Summary -->
        <div class="mt-2.5 card-elevated rounded-apple-lg p-2.5">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-sm font-medium text-dark-text-secondary mb-2">Profit Bulan Ini</p>
                    @php
                        $monthlyProfit = ($stats['payments_this_month'] ?? 0) - ($stats['expenses_this_month'] ?? 0);
                        $isProfitable = $monthlyProfit >= 0;
                    @endphp
                    <p class="text-2xl font-bold" style="color: {{ $isProfitable ? 'rgba(52, 199, 89, 1)' : 'rgba(255, 59, 48, 1)' }};">
                        {{ $isProfitable ? '+' : '-' }}Rp {{ number_format(abs($monthlyProfit), 0, ',', '.') }}
                    </p>
                </div>
                <div class="flex-1 text-right">
                    <p class="text-sm font-medium text-dark-text-secondary mb-2">Total Proyek Nilai Kontrak</p>
                    <p class="text-2xl font-bold text-dark-text-primary">
                        Rp {{ number_format($stats['total_contract_value'] ?? 0, 0, ',', '.') }}
                    </p>
                </div>
                <div class="flex-1 text-right">
                    <p class="text-sm font-medium text-dark-text-secondary mb-2">Total Pengeluaran</p>
                    <p class="text-2xl font-bold" style="color: rgba(255, 59, 48, 1);">
                        Rp {{ number_format($stats['total_expenses'] ?? 0, 0, ',', '.') }}
                    </p>
                </div>
                <div class="flex-1 text-right">
                    <p class="text-sm font-medium text-dark-text-secondary mb-2">Overall Profit Margin</p>
                    @php
                        $overallMargin = ($stats['total_payment_received'] ?? 0) > 0
                            ? round(((($stats['total_payment_received'] ?? 0) - ($stats['total_expenses'] ?? 0)) / $stats['total_payment_received']) * 100, 1)
                            : 0;
                    @endphp
                    <p class="text-2xl font-bold text-apple-blue-dark">
                        {{ $overallMargin }}%
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-2.5 mb-3">
        <!-- Project Status Chart -->
        <div class="xl:col-span-1">
            <div class="card-elevated rounded-apple-lg" style="overflow: visible;">
                <div class="px-2.5 py-2 border-b" style="border-color: rgba(84, 84, 88, 0.25);">
                    <h3 class="text-base font-medium text-dark-text-secondary mb-0.5">Status Proyek</h3>
                    <p class="text-xs text-dark-text-tertiary">Distribusi status saat ini</p>
                </div>
                <div class="p-2.5">
                    <div class="relative" style="height: 185px;">
                        <canvas id="projectStatusChart"></canvas>
                    </div>
                    <div id="statusLegend" class="flex flex-wrap justify-center gap-1.5 mt-1.5 text-xs"></div>
                </div>
            </div>
        </div>

        <!-- Monthly Progress Chart -->
        <div class="xl:col-span-2">
            <div class="card-elevated rounded-apple-lg" style="overflow: visible;">
                <div class="px-2.5 py-2 border-b" style="border-color: rgba(84, 84, 88, 0.25);">
                    <h3 class="text-base font-medium text-dark-text-secondary mb-0.5">Progress Bulanan</h3>
                    <p class="text-xs text-dark-text-tertiary">Trend proyek dalam 6 bulan terakhir</p>
                </div>
                <div class="p-2.5">
                    <div class="relative" style="height: 185px;">
                        <canvas id="monthlyProgressChart"></canvas>
                    </div>
                    <div class="mt-1.5" style="height: 23px;"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- New Dashboard Cards Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-2.5 mb-3">
        <!-- Task Overview & Progress -->
        <div class="card-elevated rounded-apple-lg overflow-hidden">
            <div class="px-2.5 py-2 border-b" style="border-color: rgba(84, 84, 88, 0.25);">
                <h3 class="text-base font-medium text-dark-text-secondary mb-0.5">Ringkasan Tugas</h3>
                <p class="text-xs text-dark-text-tertiary">Status dan progress tasks</p>
            </div>
            <div class="p-2.5">
                <!-- Task Stats Grid -->
                <div class="grid grid-cols-2 gap-2.5 mb-3">
                    <div class="p-2.5 rounded-apple" style="background-color: rgba(10, 132, 255, 0.08);">
                        <div class="flex items-center justify-between mb-1.5">
                            <span class="text-xs text-dark-text-tertiary">Todo</span>
                            <i class="fas fa-circle text-xs" style="color: rgba(10, 132, 255, 0.6);"></i>
                        </div>
                        <p class="text-2xl font-semibold" style="color: rgba(10, 132, 255, 1);">{{ $stats['pending_tasks'] ?? 0 }}</p>
                        <p class="text-xs text-dark-text-tertiary mt-0.5">Belum dimulai</p>
                    </div>
                    
                    <div class="p-2.5 rounded-apple" style="background-color: rgba(255, 149, 0, 0.08);">
                        <div class="flex items-center justify-between mb-1.5">
                            <span class="text-xs text-dark-text-tertiary">In Progress</span>
                            <i class="fas fa-circle text-xs" style="color: rgba(255, 149, 0, 0.6);"></i>
                        </div>
                        <p class="text-2xl font-semibold" style="color: rgba(255, 149, 0, 1);">{{ $stats['in_progress_tasks'] ?? 0 }}</p>
                        <p class="text-xs text-dark-text-tertiary mt-0.5">Sedang dikerjakan</p>
                    </div>
                    
                    <div class="p-2.5 rounded-apple" style="background-color: rgba(52, 199, 89, 0.08);">
                        <div class="flex items-center justify-between mb-1.5">
                            <span class="text-xs text-dark-text-tertiary">Completed</span>
                            <i class="fas fa-check-circle text-xs" style="color: rgba(52, 199, 89, 0.6);"></i>
                        </div>
                        <p class="text-2xl font-semibold" style="color: rgba(52, 199, 89, 1);">{{ $stats['completed_tasks'] ?? 0 }}</p>
                        <p class="text-xs text-dark-text-tertiary mt-0.5">Selesai</p>
                    </div>
                    
                    <div class="p-2.5 rounded-apple" style="background-color: rgba(255, 59, 48, 0.08);">
                        <div class="flex items-center justify-between mb-1.5">
                            <span class="text-xs text-dark-text-tertiary">Overdue</span>
                            <i class="fas fa-exclamation-circle text-xs" style="color: rgba(255, 59, 48, 0.6);"></i>
                        </div>
                        <p class="text-2xl font-semibold" style="color: rgba(255, 59, 48, 1);">{{ $stats['overdue_tasks'] ?? 0 }}</p>
                        <p class="text-xs text-dark-text-tertiary mt-0.5">Terlambat</p>
                    </div>
                </div>

                <!-- Task Progress Bar -->
                <div class="p-2.5 rounded-apple" style="background-color: rgba(84, 84, 88, 0.15);">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-xs font-medium text-dark-text-secondary">Overall Progress</span>
                        @php
                            $totalTasks = ($stats['total_tasks'] ?? 0);
                            $completedTasks = ($stats['completed_tasks'] ?? 0);
                            $taskProgress = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100, 1) : 0;
                        @endphp
                        <span class="text-xs font-semibold" style="color: rgba(52, 199, 89, 1);">{{ $taskProgress }}%</span>
                    </div>
                    <div class="w-full h-2 rounded-full" style="background-color: rgba(84, 84, 88, 0.3);">
                        <div class="h-2 rounded-full transition-all" style="background: linear-gradient(90deg, rgba(52, 199, 89, 1) 0%, rgba(52, 199, 89, 0.7) 100%); width: {{ $taskProgress }}%;"></div>
                    </div>
                    <p class="text-xs text-dark-text-tertiary mt-2">
                        {{ $completedTasks }} dari {{ $totalTasks }} tugas selesai
                    </p>
                </div>
            </div>
        </div>

        <!-- Financial Performance -->
        <div class="card-elevated rounded-apple-lg overflow-hidden">
            <div class="px-2.5 py-2 border-b" style="border-color: rgba(84, 84, 88, 0.25);">
                <h3 class="text-base font-medium text-dark-text-secondary mb-0.5">Kinerja Keuangan</h3>
                <p class="text-xs text-dark-text-tertiary">Budget utilization & cash flow</p>
            </div>
            <div class="p-2.5">
                <!-- Financial Metrics -->
                <div class="space-y-2.5">
                    <!-- Budget Utilization -->
                    <div class="p-2.5 rounded-apple" style="background-color: rgba(10, 132, 255, 0.08);">
                        <div class="flex items-center justify-between mb-2">
                            <div class="flex items-center">
                                <i class="fas fa-chart-pie mr-2 text-sm" style="color: rgba(10, 132, 255, 0.8);"></i>
                                <span class="text-xs font-medium text-dark-text-secondary">Budget Utilization</span>
                            </div>
                            @php
                                $budgetUtil = $stats['budget_utilization'] ?? 0;
                            @endphp
                            <span class="text-sm font-semibold" style="color: rgba(10, 132, 255, 1);">{{ number_format($budgetUtil, 1) }}%</span>
                        </div>
                        <div class="w-full h-1.5 rounded-full" style="background-color: rgba(84, 84, 88, 0.3);">
                            <div class="h-1.5 rounded-full" style="background-color: rgba(10, 132, 255, 1); width: {{ min($budgetUtil, 100) }}%;"></div>
                        </div>
                        <div class="flex items-center justify-between mt-2">
                            <span class="text-xs text-dark-text-tertiary">Spent: Rp {{ number_format($stats['total_spent'] ?? 0, 0, ',', '.') }}</span>
                            <span class="text-xs text-dark-text-tertiary">Budget: Rp {{ number_format($stats['total_budget'] ?? 0, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <!-- Monthly Cash Flow -->
                    <div class="p-2.5 rounded-apple" style="background-color: rgba(52, 199, 89, 0.08);">
                        <div class="flex items-center justify-between mb-2">
                            <div class="flex items-center">
                                <i class="fas fa-arrow-down mr-2 text-sm" style="color: rgba(52, 199, 89, 0.8);"></i>
                                <span class="text-xs font-medium text-dark-text-secondary">Pembayaran Bulan Ini</span>
                            </div>
                        </div>
                        <p class="text-xl font-semibold mb-1" style="color: rgba(52, 199, 89, 1);">
                            Rp {{ number_format($stats['payments_this_month'] ?? 0, 0, ',', '.') }}
                        </p>
                        <p class="text-xs text-dark-text-tertiary">Pemasukan {{ now()->format('F Y') }}</p>
                    </div>

                    <div class="p-2.5 rounded-apple" style="background-color: rgba(255, 59, 48, 0.08);">
                        <div class="flex items-center justify-between mb-2">
                            <div class="flex items-center">
                                <i class="fas fa-arrow-up mr-2 text-sm" style="color: rgba(255, 59, 48, 0.8);"></i>
                                <span class="text-xs font-medium text-dark-text-secondary">Pengeluaran Bulan Ini</span>
                            </div>
                        </div>
                        <p class="text-xl font-semibold mb-1" style="color: rgba(255, 59, 48, 1);">
                            Rp {{ number_format($stats['expenses_this_month'] ?? 0, 0, ',', '.') }}
                        </p>
                        <p class="text-xs text-dark-text-tertiary">Pengeluaran {{ now()->format('F Y') }}</p>
                    </div>

                    <!-- Net Cash Flow -->
                    <div class="p-2.5 rounded-apple" style="background-color: rgba(255, 149, 0, 0.08);">
                        @php
                            $netCashFlow = ($stats['payments_this_month'] ?? 0) - ($stats['expenses_this_month'] ?? 0);
                            $isPositive = $netCashFlow >= 0;
                        @endphp
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <i class="fas fa-wallet mr-2 text-sm" style="color: rgba(255, 149, 0, 0.8);"></i>
                                <span class="text-xs font-medium text-dark-text-secondary">Net Cash Flow</span>
                            </div>
                            <p class="text-lg font-semibold" style="color: {{ $isPositive ? 'rgba(52, 199, 89, 1)' : 'rgba(255, 59, 48, 1)' }};">
                                {{ $isPositive ? '+' : '' }}Rp {{ number_format(abs($netCashFlow), 0, ',', '.') }}
                            </p>
                        </div>
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
    const appleColors = {
        blue: '#0A84FF',
        green: '#30D158',
        red: '#FF453A',
        orange: '#FF9F0A',
        yellow: '#FFD60A',
        purple: '#BF5AF2',
        pink: '#FF375F',
        teal: '#64D2FF',
        indigo: '#5E5CE6',
        gray: '#8E8E93'
    };

    Chart.defaults.font.family = 'Inter, -apple-system, BlinkMacSystemFont, sans-serif';
    Chart.defaults.color = 'rgba(235, 235, 245, 0.6)';
    Chart.defaults.borderColor = 'rgba(84, 84, 88, 0.65)';

    const statusChartElement = document.getElementById('projectStatusChart');
    if (statusChartElement) {
        const statusCtx = statusChartElement.getContext('2d');
        const statusData = @json($projects_by_status ?? []);
        
        if (Array.isArray(statusData) && statusData.length > 0) {
            const chartColors = [appleColors.blue, appleColors.green, appleColors.orange, appleColors.red, appleColors.purple];
            
            new Chart(statusCtx, {
                type: 'doughnut',
                data: {
                    labels: statusData.map(item => (item && item.name) ? item.name : ''),
                    datasets: [{
                        data: statusData.map(item => (item && (item.projects_count ?? item.count ?? 0))),
                        backgroundColor: statusData.map((_, index) => (chartColors[index % chartColors.length] + '40')),
                        borderColor: statusData.map((_, index) => chartColors[index % chartColors.length]),
                        borderWidth: 3,
                        hoverBorderWidth: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '60%',
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            backgroundColor: 'rgba(28, 28, 30, 0.95)',
                            titleColor: '#FFFFFF',
                            bodyColor: 'rgba(235, 235, 245, 0.6)',
                            titleFont: { size: 12, weight: '600' },
                            bodyFont: { size: 11 },
                            cornerRadius: 8,
                            padding: 10,
                            borderColor: 'rgba(84, 84, 88, 0.3)',
                            borderWidth: 0
                        }
                    },
                    elements: { arc: { borderRadius: 4 } }
                }
            });
            
            // Custom compact legend
            const legendContainer = document.getElementById('statusLegend');
            if (legendContainer) {
                statusData.forEach((item, index) => {
                    const color = chartColors[index % chartColors.length];
                    const count = item.projects_count ?? item.count ?? 0;
                    const legendItem = document.createElement('div');
                    legendItem.className = 'flex items-center gap-1';
                    legendItem.innerHTML = `
                        <span class="w-2 h-2 rounded-full" style="background-color: ${color}"></span>
                        <span class="text-dark-text-secondary" style="font-size: 10px;">${item.name}</span>
                        <span class="text-dark-text-primary font-medium" style="font-size: 10px;">(${count})</span>
                    `;
                    legendContainer.appendChild(legendItem);
                });
            }
        } else {
            const chartContainer = statusChartElement.parentElement;
            chartContainer.innerHTML = '<div class="flex items-center justify-center h-full text-dark-text-secondary"><i class="fas fa-chart-pie mr-2"></i>Tidak ada data tersedia</div>';
        }
    }

    const progressChartElement = document.getElementById('monthlyProgressChart');
    if (progressChartElement) {
        const progressCtx = progressChartElement.getContext('2d');
        const monthlyData = @json($monthly_progress ?? []);
        
        if (Array.isArray(monthlyData) && monthlyData.length > 0) {
            new Chart(progressCtx, {
                type: 'line',
                data: {
                    labels: monthlyData.map(item => (item && item.month) ? item.month : ''),
                    datasets: [{
                        label: 'Proyek Selesai',
                        data: monthlyData.map(item => (item && item.completed) ? item.completed : 0),
                        borderColor: appleColors.green,
                        backgroundColor: appleColors.green + '20',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4,
                        pointBackgroundColor: appleColors.green,
                        pointBorderColor: '#1C1C1E',
                        pointBorderWidth: 3,
                        pointRadius: 5,
                        pointHoverRadius: 7
                    }, {
                        label: 'Proyek Baru',
                        data: monthlyData.map(item => (item && item.created) ? item.created : 0),
                        borderColor: appleColors.blue,
                        backgroundColor: appleColors.blue + '20',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4,
                        pointBackgroundColor: appleColors.blue,
                        pointBorderColor: '#1C1C1E',
                        pointBorderWidth: 3,
                        pointRadius: 5,
                        pointHoverRadius: 7
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: { intersect: false, mode: 'index' },
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                padding: 8,
                                usePointStyle: true,
                                pointStyle: 'circle',
                                font: { size: 10, weight: '500' },
                                boxWidth: 8,
                                boxHeight: 8
                            }
                        },
                        tooltip: {
                            backgroundColor: 'rgba(28, 28, 30, 0.95)',
                            titleFont: { size: 13, weight: '600' },
                            bodyFont: { size: 12 },
                            cornerRadius: 8,
                            padding: 12
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(84, 84, 88, 0.35)',
                                borderColor: 'rgba(84, 84, 88, 0.65)'
                            },
                            ticks: { font: { size: 10 }, color: 'rgba(235, 235, 245, 0.6)' }
                        },
                        x: {
                            grid: { display: false },
                            ticks: { font: { size: 10 }, color: 'rgba(235, 235, 245, 0.6)' }
                        }
                    },
                    elements: { point: { hoverBorderWidth: 4 } }
                }
            });
        } else {
            const chartContainer = progressChartElement.parentElement;
            chartContainer.innerHTML = '<div class="flex items-center justify-center h-full text-dark-text-secondary"><i class="fas fa-chart-line mr-2"></i>Tidak ada data tersedia</div>';
        }
    }
});
</script>
@endpush
