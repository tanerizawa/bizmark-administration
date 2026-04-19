@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<style>
    /* Dashboard contrast overrides – fix low-opacity inline styles */
    .dashboard-root [style*="rgba(235,235,245,0.5)"] { color: rgba(235,235,245,0.72) !important; }
    .dashboard-root [style*="rgba(235,235,245,0.65)"] { color: rgba(235,235,245,0.82) !important; }
    .dashboard-root [style*="color: rgba(255,255,255,0.3)"] { color: rgba(255,255,255,0.52) !important; }
    .dashboard-root [style*="color: rgba(255,255,255,0.4)"] { color: rgba(255,255,255,0.62) !important; }
    .dashboard-root [style*="color: rgba(255,255,255,0.5)"] { color: rgba(255,255,255,0.72) !important; }
    .dashboard-root [style*="color: rgba(255,255,255,0.6)"] { color: rgba(255,255,255,0.8) !important; }
    .dashboard-root [style*="color: rgba(142,142,147,0.6)"] { color: rgba(210,210,215,0.9) !important; }
</style>
<div class="dashboard-root space-y-4">
    {{-- Compact Hero section --}}
    <section class="card-elevated rounded-apple-lg admin-hero relative overflow-hidden" role="region" aria-labelledby="dashboard-hero">
        {{-- Decorative Background --}}
        <div class="absolute inset-0 pointer-events-none" aria-hidden="true">
            <div class="w-48 h-48 bg-apple-blue opacity-20 blur-3xl rounded-full absolute -top-12 -right-8"></div>
        </div>
        
        <div class="relative flex flex-col md:flex-row md:items-center md:justify-between gap-3">
            <div class="flex-1 min-w-0">
                <p class="admin-hero-subtitle">Pusat Operasional</p>
                <h1 id="dashboard-hero" class="admin-hero-title text-white">Ringkasan Eksekutif</h1>
                <p class="admin-hero-desc">Pantau KPI, arus kas, dan perkembangan proyek</p>
                <div class="admin-hero-meta flex flex-wrap gap-3">
                    <span><i class="fas fa-clock mr-1.5"></i>{{ now()->format('d M Y, H:i') }}</span>
                    <span><i class="fas fa-user-shield mr-1.5"></i>Direksi & Kepala Ops</span>
                </div>
            </div>
            
            <div class="flex items-center gap-2">
                <a href="{{ route('projects.index') }}" 
                   class="admin-btn admin-btn-sm rounded" style="background: rgba(0,122,255,0.25); color: #fff;">
                    <i class="fas fa-project-diagram mr-1"></i>Proyek
                </a>
                <a href="{{ route('dashboard') }}" 
                   class="admin-btn admin-btn-sm rounded" style="background: rgba(255,255,255,0.08); color: rgba(235,235,245,0.8);">
                    <i class="fas fa-arrows-rotate"></i>
                </a>
            </div>
        </div>
    </section>

    {{-- Compact KPI Cards --}}
    <div class="grid grid-cols-4 gap-2">
        {{-- Urgent Actions --}}
        <article class="admin-stat-card card-elevated rounded-apple flex items-center gap-2" 
                 style="background: rgba(255,69,58,0.1); border: 1px solid rgba(255,69,58,0.2);">
            <div class="admin-stat-icon rounded flex items-center justify-center" style="background: rgba(255,69,58,0.2);">
                <i class="fas fa-exclamation-triangle text-apple-red" style="font-size: 0.7rem;"></i>
            </div>
            <div>
                <p class="admin-small text-apple-red uppercase tracking-wider">Urgent</p>
                <p class="admin-stat text-white">{{ $criticalAlerts['total_urgent'] }}</p>
            </div>
        </article>
        
        {{-- Cash Runway --}}
        <article class="admin-stat-card card-elevated rounded-apple flex items-center gap-2" 
                 style="background: rgba(10,132,255,0.1); border: 1px solid rgba(10,132,255,0.2);">
            <div class="admin-stat-icon rounded flex items-center justify-center" style="background: rgba(10,132,255,0.2);">
                <i class="fas fa-wallet text-apple-blue" style="font-size: 0.7rem;"></i>
            </div>
            <div>
                <p class="admin-small text-apple-blue uppercase tracking-wider">Kas</p>
                <p class="admin-stat text-apple-blue">{{ $cashFlowStatus['runway_months'] }} bln</p>
            </div>
        </article>
        
        {{-- Pending Approvals --}}
        <article class="admin-stat-card card-elevated rounded-apple flex items-center gap-2" 
                 style="background: rgba(255,149,0,0.1); border: 1px solid rgba(255,149,0,0.2);">
            <div class="admin-stat-icon rounded flex items-center justify-center" style="background: rgba(255,149,0,0.2);">
                <i class="fas fa-clock text-apple-orange" style="font-size: 0.7rem;"></i>
            </div>
            <div>
                <p class="admin-small text-apple-orange uppercase tracking-wider">Pending</p>
                <p class="admin-stat text-white">{{ $pendingApprovals['total_pending'] }}</p>
            </div>
        </article>
        
        {{-- Upcoming Tasks --}}
        <article class="admin-stat-card card-elevated rounded-apple flex items-center gap-2" 
                 style="background: rgba(52,199,89,0.1); border: 1px solid rgba(52,199,89,0.2);">
            <div class="admin-stat-icon rounded flex items-center justify-center" style="background: rgba(52,199,89,0.2);">
                <i class="fas fa-calendar-check text-apple-green" style="font-size: 0.7rem;"></i>
            </div>
            <div>
                <p class="admin-small text-apple-green uppercase tracking-wider">30 Hari</p>
                <p class="admin-stat text-apple-green">{{ $thisWeek['total_items'] }}</p>
            </div>
        </article>
    </div>

    {{-- Critical focus section --}}
    <section class="card-elevated rounded-apple-lg p-3" role="region" aria-labelledby="critical-focus">
        <div class="flex items-center justify-between mb-2">
            <div>
                <h2 id="critical-focus" class="admin-section text-white flex items-center gap-2">
                    <i class="fas fa-exclamation-circle text-apple-red" style="font-size: 0.75rem;"></i>
                    Fokus Kritis
                </h2>
                <p class="admin-body text-dark-text-secondary">Isu mendesak, arus kas, dan persetujuan dokumen</p>
            </div>
            
            {{-- Status Badge --}}
            @if($criticalAlerts['total_urgent'] > 0 || $cashFlowStatus['status'] === 'critical')
            <span class="px-2 py-1 rounded-full text-xs font-semibold flex items-center gap-1.5" 
                  style="background: rgba(255,59,48,0.15); color: #FF3B30;">
                <i class="fas fa-exclamation-triangle" aria-hidden="true"></i>
                Perhatian
            </span>
            @else
            <span class="px-2 py-1 rounded-full text-xs font-semibold flex items-center gap-1.5" style="background: rgba(52,199,89,0.15); color: #34C759;">
                <i class="fas fa-check-circle"></i>Stabil
            </span>
            @endif
        </div>

        <div class="grid grid-cols-3 gap-2">
            {{-- Urgent board --}}
            <div class="card-elevated rounded-apple p-3 flex flex-col">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="admin-section text-white">Memerlukan Penanganan</h3>
                    <span class="admin-badge" style="background: rgba(255,59,48,0.12); color: rgba(255,59,48,0.9);">
                        {{ $criticalAlerts['total_urgent'] }}
                    </span>
                </div>
                <div class="space-y-2 overflow-y-auto flex-1" style="max-height: 240px;">
                    @php $projectsCount = count($criticalAlerts['overdue_projects']); $tasksCount = count($criticalAlerts['overdue_tasks']); @endphp
                    @if($projectsCount)
                        <p class="admin-small uppercase tracking-widest text-dark-text-tertiary">Proyek Terlambat</p>
                        @foreach($criticalAlerts['overdue_projects'] as $project)
                        <a href="{{ route('projects.show', $project) }}" class="block p-2 rounded-apple hover:bg-dark-elevated-2 transition-apple" style="background: rgba(255,59,48,0.08);">
                            <p class="admin-body font-medium text-white truncate">{{ $project->name }}</p>
                            <p class="admin-small text-apple-orange">
                                <i class="fas fa-exclamation-circle mr-1"></i>Terlambat {{ $project->days_overdue }} hari
                            </p>
                            @if($project->institution)
                            <p class="admin-small text-dark-text-tertiary">{{ $project->institution->name }}</p>
                            @endif
                        </a>
                        @endforeach
                    @endif

                    @if($tasksCount)
                        <p class="admin-small uppercase tracking-widest text-dark-text-tertiary mt-2">Tugas Terlambat</p>
                        @foreach($criticalAlerts['overdue_tasks'] as $task)
                        <a href="{{ route('tasks.show', $task) }}" class="block p-2 rounded-apple hover:bg-dark-elevated-2 transition-apple" style="background: rgba(255,149,0,0.08);">
                            <p class="admin-body font-medium text-white truncate">{{ $task->title }}</p>
                            <p class="admin-small text-apple-orange"><i class="fas fa-clock mr-1"></i>Terlambat {{ $task->days_overdue }} hari</p>
                            <p class="admin-small text-dark-text-tertiary">{{ $task->assignedUser->name ?? 'Belum ditugaskan' }}</p>
                        </a>
                        @endforeach
                    @endif

                    @if(!$projectsCount && !$tasksCount)
                    <div class="text-center py-6">
                        <div class="admin-stat-icon mx-auto mb-2 rounded-full flex items-center justify-center" style="background: rgba(52,199,89,0.12);">
                            <i class="fas fa-check-circle text-apple-green"></i>
                        </div>
                        <p class="admin-body font-medium text-white">Semua Terkendali</p>
                        <p class="admin-small text-dark-text-tertiary">Tidak ada isu mendesak</p>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Cash flow status --}}
            <div class="card-elevated rounded-apple p-3 space-y-2">
                <div class="flex items-center justify-between">
                    <h3 class="admin-section text-white">Kondisi Keuangan</h3>
                    <span class="admin-badge uppercase" style="background: {{ $cashFlowStatus['status_color'] }}20; color: {{ $cashFlowStatus['status_color'] }};">
                        {{ $cashFlowStatus['status'] }}
                    </span>
                </div>
                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <p class="admin-small text-dark-text-tertiary">Saldo</p>
                        <p class="admin-stat text-white">Rp {{ number_format($cashFlowStatus['current_balance']/1000000, 1) }}M</p>
                    </div>
                    <div>
                        <p class="admin-small text-dark-text-tertiary">Proyeksi</p>
                        <p class="admin-stat" style="color: {{ $cashFlowStatus['status_color'] }};">{{ $cashFlowStatus['runway_months'] }} bln</p>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-2">
                    <div class="p-2 rounded-apple" style="background: rgba(84,84,88,0.35);">
                        <p class="admin-small text-dark-text-tertiary">Burn Rate</p>
                        <p class="admin-body font-semibold text-white">{{ number_format($cashFlowStatus['monthly_burn_rate'] / 1000000, 1) }}M/bln</p>
                    </div>
                    <div class="p-2 rounded-apple" style="background: rgba(84,84,88,0.35);">
                        <p class="admin-small text-dark-text-tertiary">Overdue</p>
                        <p class="admin-body font-semibold" style="color: {{ $cashFlowStatus['overdue_invoices'] > 0 ? '#FF3B30' : '#34C759' }};">
                            {{ $cashFlowStatus['overdue_invoices'] > 0 ? number_format($cashFlowStatus['overdue_invoices']/1000000,1).'M' : '0' }}
                        </p>
                    </div>
                </div>
                <div class="rounded-apple p-2" style="background: rgba(10,132,255,0.12);">
                    <p class="admin-small text-dark-text-secondary">
                        Prioritaskan penagihan {{ $cashFlowStatus['top_client'] ?? 'klien utama' }} untuk menjaga kas di atas 4 bulan.
                    </p>
                </div>
            </div>

            {{-- Pending approvals --}}
            <div class="card-elevated rounded-apple p-3 flex flex-col">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="admin-section text-white">Dokumen Tertunda</h3>
                    <span class="admin-badge" style="background: rgba(191,90,242,0.15); color: rgba(191,90,242,0.95);">
                        {{ $pendingApprovals['total_pending'] }}
                    </span>
                </div>
                <div class="space-y-2 overflow-y-auto flex-1" style="max-height: 200px;">
                    @forelse($pendingApprovals['pending_documents'] as $document)
                    <div class="p-2 rounded-apple" style="background: rgba(191,90,242,0.08);">
                        <p class="admin-body font-medium text-white truncate">{{ $document->name }}</p>
                        <p class="admin-small" style="color: rgba(191,90,242,1);">
                            <i class="fas fa-clock mr-1"></i>Menunggu {{ $document->days_waiting }} hari
                        </p>
                        <p class="admin-small text-dark-text-tertiary">{{ $document->uploader->name ?? '-' }}</p>
                        <div class="flex gap-1 mt-2">
                            <a href="{{ route('documents.show', $document) }}" class="admin-btn admin-btn-sm flex-1 text-center" style="background: rgba(52,199,89,0.25); color: rgba(52,199,89,0.95);">
                                <i class="fas fa-check mr-1"></i>Setujui
                            </a>
                            <a href="{{ route('documents.show', $document) }}" class="admin-btn admin-btn-sm" style="background: rgba(255,255,255,0.1); color: rgba(255,255,255,0.7);">
                                <i class="fas fa-eye"></i>
                            </a>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-6">
                        <div class="admin-stat-icon mx-auto mb-2 rounded-full flex items-center justify-center" style="background: rgba(52,199,89,0.12);">
                            <i class="fas fa-file-check text-apple-green"></i>
                        </div>
                        <p class="admin-body font-medium text-white">Semua Terselesaikan</p>
                        <p class="admin-small text-dark-text-tertiary">Tidak ada dokumen menunggu</p>
                    </div>
                    @endforelse
                </div>
                @if($pendingApprovals['total_pending'] > 0)
                <a href="{{ route('documents.index') }}?status=review" class="admin-small font-medium mt-2 text-dark-text-secondary hover:text-white">
                    Lihat semua dokumen →
                </a>
                @endif
            </div>
        </div>
    </section>

    {{-- Financial intelligence section --}}
    <section class="card-elevated rounded-apple-lg p-3 space-y-2">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="admin-section text-white flex items-center gap-2">
                    <i class="fas fa-chart-line text-apple-blue" style="font-size: 0.75rem;"></i>Tinjauan Keuangan
                </h2>
                <p class="admin-body text-dark-text-secondary">Pemasukan, pengeluaran, piutang, dan anggaran</p>
            </div>
        </div>

        <div class="grid grid-cols-3 gap-2">
            {{-- Income vs Expense --}}
            <div class="card-elevated rounded-apple p-3 space-y-2">
                <div class="flex items-center justify-between">
                    <h3 class="admin-section text-white">Arus Kas Bulan Ini</h3>
                    <span class="admin-badge" style="background: rgba(10,132,255,0.15); color: rgba(10,132,255,0.9);">Aktif</span>
                </div>
                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <p class="admin-small text-dark-text-tertiary">Pemasukan</p>
                        <p class="admin-stat text-white">{{ number_format($cashFlowSummary['payments_this_month'] / 1000000, 1) }}M</p>
                        @if($cashFlowSummary['payments_growth'] != 0)
                        <p class="admin-small" style="color: {{ $cashFlowSummary['payments_growth'] > 0 ? '#34C759' : '#FF3B30' }};">
                            <i class="fas fa-arrow-{{ $cashFlowSummary['payments_growth'] > 0 ? 'up' : 'down' }} mr-1"></i>{{ abs($cashFlowSummary['payments_growth']) }}%
                        </p>
                        @endif
                    </div>
                    <div>
                        <p class="admin-small text-dark-text-tertiary">Pengeluaran</p>
                        <p class="admin-stat text-white">{{ number_format($cashFlowSummary['expenses_this_month'] / 1000000, 1) }}M</p>
                        @if($cashFlowSummary['expenses_growth'] != 0)
                        <p class="admin-small" style="color: {{ $cashFlowSummary['expenses_growth'] > 0 ? '#FF3B30' : '#34C759' }};">
                            <i class="fas fa-arrow-{{ $cashFlowSummary['expenses_growth'] > 0 ? 'up' : 'down' }} mr-1"></i>{{ abs($cashFlowSummary['expenses_growth']) }}%
                        </p>
                        @endif
                    </div>
                </div>
                @php
                    $maxAmount = max($cashFlowSummary['payments_this_month'], $cashFlowSummary['expenses_this_month'], 1);
                    $paymentsWidth = ($cashFlowSummary['payments_this_month'] / $maxAmount) * 100;
                    $expensesWidth = ($cashFlowSummary['expenses_this_month'] / $maxAmount) * 100;
                @endphp
                <div class="space-y-2">
                    <div>
                        <div class="flex items-center justify-between admin-small text-dark-text-tertiary">
                            <span>Pemasukan</span>
                            <span>Rp {{ number_format($cashFlowSummary['payments_this_month']) }}</span>
                        </div>
                        <div class="h-1.5 rounded-full overflow-hidden" style="background: rgba(52,199,89,0.15);">
                            <div class="h-full" style="width: {{ $paymentsWidth }}%; background: #34C759;"></div>
                        </div>
                    </div>
                    <div>
                        <div class="flex items-center justify-between admin-small text-dark-text-tertiary">
                            <span>Pengeluaran</span>
                            <span>Rp {{ number_format($cashFlowSummary['expenses_this_month']) }}</span>
                        </div>
                        <div class="h-1.5 rounded-full overflow-hidden" style="background: rgba(255,59,48,0.15);">
                            <div class="h-full" style="width: {{ $expensesWidth }}%; background: #FF3B30;"></div>
                        </div>
                    </div>
                </div>
                <div class="admin-body font-semibold" style="color: {{ $cashFlowSummary['is_profitable'] ? '#34C759' : '#FF3B30' }};">
                    {{ $cashFlowSummary['is_profitable'] ? 'Surplus ' : 'Defisit ' }}{{ number_format($cashFlowSummary['net_this_month'] / 1000000, 1) }}M bulan ini
                </div>
                <div class="grid grid-cols-2 gap-2 admin-small text-dark-text-tertiary">
                    <div>
                        <p>Pemasukan YTD</p>
                        <p class="admin-body font-semibold text-white">{{ number_format($cashFlowSummary['payments_ytd'] / 1000000, 1) }}M</p>
                    </div>
                    <div>
                        <p>Pengeluaran YTD</p>
                        <p class="admin-body font-semibold text-white">{{ number_format($cashFlowSummary['expenses_ytd'] / 1000000, 1) }}M</p>
                    </div>
                </div>
            </div>

            {{-- Receivables --}}
            <div class="card-elevated rounded-apple p-3 space-y-2">
                <div class="flex items-center justify-between">
                    <h3 class="admin-section text-white">Umur Piutang</h3>
                    <span class="admin-badge" style="background: rgba(255,214,10,0.15); color: rgba(255,214,10,0.9);">
                        {{ $receivablesAging['invoice_count'] + $receivablesAging['internal_count'] }}
                    </span>
                </div>
                <div class="rounded-apple p-2" style="background: rgba(10,132,255,0.12);">
                    <p class="admin-small text-dark-text-tertiary">Total Piutang</p>
                    <p class="admin-stat text-apple-blue">{{ number_format($receivablesAging['total_receivables'] / 1000000, 1) }}M</p>
                    <p class="admin-small text-dark-text-tertiary">Faktur {{ number_format($receivablesAging['invoice_receivables']/1000000,1) }}M • Kasbon {{ number_format($receivablesAging['internal_receivables']/1000000,1) }}M</p>
                </div>
                <div class="space-y-1">
                    @foreach(['under_30' => '0-30 hari', 'days_30_60' => '30-60 hari', 'days_60_90' => '60-90 hari', 'over_90' => '90+ hari'] as $key => $label)
                        @if($receivablesAging['aging'][$key] > 0)
                        <div class="p-1.5 rounded-apple flex items-center justify-between admin-small" style="background: rgba(255,255,255,0.04);">
                            <span class="text-dark-text-secondary">{{ $label }}</span>
                            <span class="font-semibold" style="color: {{ $key === 'over_90' ? '#FF3B30' : ($key === 'days_60_90' ? '#FF9500' : '#FFFFFF') }};">
                                {{ number_format($receivablesAging['aging'][$key] / 1000000, 1) }}M
                            </span>
                        </div>
                        @endif
                    @endforeach
                </div>
                @if($receivablesAging['internal_count'] > 0)
                <div class="space-y-1 max-h-24 overflow-y-auto">
                    <p class="admin-small uppercase tracking-widest text-dark-text-tertiary">Kasbon Internal</p>
                    @foreach($receivablesAging['internal_list'] as $kasbon)
                    <div class="p-1.5 rounded-apple" style="background: rgba(255,255,255,0.03);">
                        <div class="flex items-center justify-between admin-small">
                            <span class="font-medium text-white">{{ $kasbon['from'] }}</span>
                            <span class="font-bold text-dark-text-secondary">{{ number_format($kasbon['remaining']/1000000,1) }}M</span>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <p class="admin-small text-center py-2 text-apple-green">Tidak ada kasbon belum lunas</p>
                @endif
            </div>

            {{-- Budget status --}}
            <div class="card-elevated rounded-apple p-3 space-y-2">
                <div class="flex items-center justify-between">
                    <h3 class="admin-section text-white">Pemanfaatan Anggaran</h3>
                    <div class="text-right">
                        <p class="admin-stat text-white">{{ $budgetStatus['overall_utilization'] }}%</p>
                    </div>
                </div>
                @if($budgetStatus['top_projects']->count() > 0)
                <div class="space-y-2">
                    @foreach($budgetStatus['top_projects'] as $project)
                    <a href="{{ route('projects.show', $project) }}" class="block p-2 rounded-apple hover:bg-dark-elevated-2 transition-apple" style="background: {{ $project->status_color }}15;">
                        <div class="flex items-center justify-between mb-1">
                            <div class="flex-1 min-w-0 mr-2">
                                <p class="admin-body font-medium text-white truncate">{{ $project->name }}</p>
                                <p class="admin-small text-dark-text-tertiary">{{ number_format($project->budget/1000000,1) }}M → {{ number_format($project->actual_cost/1000000,1) }}M</p>
                            </div>
                            <span class="admin-body font-bold" style="color: {{ $project->status_color }};">{{ $project->variance_percentage }}%</span>
                        </div>
                        <div class="h-1.5 rounded-full overflow-hidden" style="background: rgba(255,255,255,0.1);">
                            <div class="h-full" style="width: {{ min($project->variance_percentage, 100) }}%; background: {{ $project->status_color }};"></div>
                        </div>
                    </a>
                    @endforeach
                </div>
                @else
                <div class="text-center py-4">
                    <div class="admin-stat-icon mx-auto mb-2 rounded-full flex items-center justify-center" style="background: rgba(255,214,10,0.12);">
                        <i class="fas fa-chart-pie" style="color: #FFD60A; font-size: 0.7rem;"></i>
                    </div>
                    <p class="admin-body font-medium text-white">Belum Ada Anggaran</p>
                    <p class="admin-small text-dark-text-tertiary">Buat anggaran proyek untuk memantau</p>
                </div>
                @endif
            </div>
        </div>
    </section>

    {{-- Operational monitoring section --}}
    <section class="card-elevated rounded-apple-lg p-3 space-y-2">
        <div class="flex items-center justify-between">
            <h2 class="admin-section text-white flex items-center gap-2">
                <i class="fas fa-tasks text-apple-green" style="font-size: 0.75rem;"></i>Pemantauan Operasional
            </h2>
        </div>
        <p class="admin-body text-dark-text-secondary">Jadwal 30 hari, distribusi proyek, dan aktivitas terkini</p>

        {{-- RAG AI Quality Metrics --}}
        @if($ragMetrics['total_processed'] > 0)
        <div class="card-elevated rounded-apple p-3 space-y-2">
            <div class="flex items-center justify-between">
                <h3 class="admin-section text-white flex items-center gap-2">
                    <i class="fas fa-brain" style="color: rgba(191,90,242,0.95); font-size: 0.65rem;"></i>Kualitas RAG AI
                </h3>
                <span class="admin-badge" style="background: rgba(191,90,242,0.15); color: rgba(191,90,242,0.95);">{{ $ragMetrics['total_processed'] }} diproses</span>
            </div>
            <div class="grid grid-cols-3 gap-2">
                <div class="text-center p-2 rounded-apple" style="background: rgba(52,199,89,0.08);">
                    <p class="admin-stat text-white">{{ $ragMetrics['avg_confidence'] }}%</p>
                    <p class="admin-small text-dark-text-tertiary">Rata-rata Confidence</p>
                </div>
                <div class="text-center p-2 rounded-apple" style="background: rgba(52,199,89,0.08);">
                    <p class="admin-stat text-apple-green">{{ $ragMetrics['high_confidence'] }}</p>
                    <p class="admin-small text-dark-text-tertiary">Confidence Tinggi</p>
                </div>
                <div class="text-center p-2 rounded-apple" style="background: rgba(255,59,48,0.08);">
                    <p class="admin-stat" style="color: rgba(255,59,48,0.95);">{{ $ragMetrics['low_confidence'] }}</p>
                    <p class="admin-small text-dark-text-tertiary">Perlu Review</p>
                </div>
            </div>
            @if($ragMetrics['recent']->count() > 0)
            <div class="space-y-1 overflow-y-auto" style="max-height: 120px;">
                @foreach($ragMetrics['recent'] as $lead)
                <a href="{{ route('admin.consultation-leads.show', $lead->id) }}" class="block p-2 rounded-apple hover:bg-dark-elevated-2 transition-apple" style="background: rgba(255,255,255,0.03);">
                    <div class="flex items-center justify-between">
                        <p class="admin-body font-medium text-white truncate">{{ $lead->name }} — {{ $lead->company_name }}</p>
                        <span class="admin-small px-1.5 py-0.5 rounded-full" style="background: rgba({{ $lead->rag_confidence >= 0.7 ? '52,199,89' : ($lead->rag_confidence >= 0.4 ? '255,204,0' : '255,59,48') }},0.2); color: rgba({{ $lead->rag_confidence >= 0.7 ? '52,199,89' : ($lead->rag_confidence >= 0.4 ? '255,204,0' : '255,59,48') }},0.95);">{{ round($lead->rag_confidence * 100) }}%</span>
                    </div>
                </a>
                @endforeach
            </div>
            @endif
        </div>
        @endif

        <div class="grid grid-cols-3 gap-2">
            {{-- Timeline --}}
            <div class="card-elevated rounded-apple p-3 space-y-2">
                <div class="flex items-center justify-between">
                    <h3 class="admin-section text-white">30 Hari Mendatang</h3>
                    <span class="admin-small text-dark-text-tertiary">{{ $thisWeek['period_start'] }} – {{ $thisWeek['period_end'] }}</span>
                </div>
                <div class="space-y-1 overflow-y-auto" style="max-height: 220px;">
                    @if($thisWeek['total_items'] > 0)
                        @foreach($thisWeek['tasks'] as $task)
                        <a href="{{ route('projects.show', $task['project_id']) }}" class="block p-2 rounded-apple hover:bg-dark-elevated-2 transition-apple" style="background: rgba({{ $task['is_past'] ? '255,59,48' : ($task['is_today'] ? '255,204,0' : '52,199,89') }},0.08);">
                            <div class="flex items-start gap-2">
                                <div class="w-1.5 h-1.5 rounded-full mt-1.5" style="background: {{ $task['priority_color'] }};"></div>
                                <div class="flex-1 min-w-0">
                                    <p class="admin-body font-medium text-white truncate">{{ $task['title'] }}</p>
                                    <p class="admin-small text-dark-text-tertiary">{{ $task['project'] }}</p>
                                    <div class="flex items-center gap-2 mt-0.5 admin-small">
                                        <span style="color: {{ $task['priority_color'] }};"><i class="fas fa-clock mr-1"></i>{{ $task['deadline_formatted'] }}</span>
                                        @if($task['is_past'])
                                        <span class="px-1.5 py-0.5 rounded-full" style="background: rgba(255,59,48,0.2); color: rgba(255,59,48,0.9); font-size: 10px;">Terlambat {{ $task['days_until'] }}h</span>
                                        @elseif($task['is_today'])
                                        <span class="px-1.5 py-0.5 rounded-full" style="background: rgba(255,204,0,0.2); color: rgba(255,204,0,1); font-size: 10px;">Hari ini</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </a>
                        @endforeach
                        @foreach($thisWeek['projects'] as $project)
                        <a href="{{ route('projects.show', $project['id']) }}" class="block p-2 rounded-apple hover:bg-dark-elevated-2 transition-apple" style="background: rgba(10,132,255,0.08);">
                            <p class="admin-body font-medium text-white">{{ $project['name'] }}</p>
                            <p class="admin-small text-dark-text-tertiary">{{ $project['deadline_formatted'] ?? 'Belum ada tenggat' }}</p>
                            <p class="admin-small text-apple-blue"><i class="fas fa-flag mr-1"></i>{{ $project['is_past'] ? 'Terlambat ' . $project['days_until'] . ' hari' : $project['days_until'] . ' hari lagi' }}</p>
                        </a>
                        @endforeach
                    @else
                    <div class="text-center py-6">
                        <div class="admin-stat-icon mx-auto mb-2 rounded-full flex items-center justify-center" style="background: rgba(10,132,255,0.12);">
                            <i class="fas fa-calendar-check text-apple-blue" style="font-size: 0.7rem;"></i>
                        </div>
                        <p class="admin-body font-medium text-white">Jadwal Kosong</p>
                        <p class="admin-small text-dark-text-tertiary">Tidak ada agenda dalam 30 hari</p>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Project status distribution --}}
            <div class="card-elevated rounded-apple p-3 space-y-2">
                <div class="flex items-center justify-between">
                    <h3 class="admin-section text-white">Distribusi Proyek</h3>
                    <span class="admin-badge" style="background: rgba(255,255,255,0.08); color: rgba(255,255,255,0.7);">{{ $projectStatusDistribution['total'] }}</span>
                </div>
                <div class="space-y-2">
                    @forelse($projectStatusDistribution['groups'] as $statusGroup)
                    <div>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="w-1.5 h-1.5 rounded-full" style="background: {{ $statusGroup['color'] }};"></span>
                                <p class="admin-body font-medium text-white">{{ $statusGroup['label'] }}</p>
                            </div>
                            <span class="admin-body font-bold text-dark-text-secondary">{{ $statusGroup['count'] }}</span>
                        </div>
                        <div class="ml-4 mt-0.5 space-y-0.5 max-h-16 overflow-y-auto">
                            @foreach($statusGroup['projects'] as $project)
                            <a href="{{ route('projects.show', $project['id']) }}" class="block admin-small text-dark-text-tertiary hover:text-white truncate">• {{ $project['name'] }}</a>
                            @endforeach
                        </div>
                    </div>
                    @empty
                    <p class="admin-small text-dark-text-tertiary">Belum ada proyek aktif</p>
                    @endforelse
                </div>
            </div>

            {{-- Recent activity --}}
            <div class="card-elevated rounded-apple p-3 space-y-2">
                <div class="flex items-center justify-between">
                    <h3 class="admin-section text-white">Aktivitas Terkini</h3>
                    <span class="admin-badge" style="background: rgba(191,90,242,0.15); color: rgba(191,90,242,0.95);">{{ $recentActivities['count'] }}</span>
                </div>
                <div class="space-y-1 overflow-y-auto" style="max-height: 200px;">
                    @forelse($recentActivities['activities'] as $activity)
                    <a href="{{ $activity['link'] }}" class="block p-2 rounded-apple hover:bg-dark-elevated-2 transition-apple" style="background: rgba(255,255,255,0.03);">
                        <p class="admin-body font-medium text-white truncate">{{ $activity['title'] }}</p>
                        <p class="admin-small text-dark-text-tertiary truncate">{{ $activity['description'] }}</p>
                        <p class="admin-small text-dark-text-tertiary"><i class="fas fa-clock mr-1"></i>{{ $activity['time_formatted'] }}</p>
                    </a>
                    @empty
                    <div class="text-center py-6">
                        <div class="admin-stat-icon mx-auto mb-2 rounded-full flex items-center justify-center" style="background: rgba(191,90,242,0.12);">
                            <i class="fas fa-clock-rotate-left" style="color: rgba(191,90,242,0.95); font-size: 0.7rem;"></i>
                        </div>
                        <p class="admin-body font-medium text-white">Belum Ada Aktivitas</p>
                        <p class="admin-small text-dark-text-tertiary">Akan muncul saat ada perubahan</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
