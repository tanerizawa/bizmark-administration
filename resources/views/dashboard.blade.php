@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard – Mission Control')

@section('content')
<div class="max-w-7xl mx-auto space-y-10">
    {{-- Hero inspired by Bizmark dashboard narrative --}}
    <section class="card-elevated rounded-apple-xl p-5 md:p-6 relative overflow-hidden">
        <div class="absolute inset-0 pointer-events-none" aria-hidden="true">
            <div class="w-72 h-72 bg-apple-blue opacity-30 blur-3xl rounded-full absolute -top-16 -right-10"></div>
            <div class="w-48 h-48 bg-apple-green opacity-20 blur-2xl rounded-full absolute bottom-0 left-10"></div>
        </div>
        <div class="relative space-y-5 md:space-y-6">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-5">
                <div class="space-y-2.5 max-w-3xl">
                    <p class="text-sm uppercase tracking-[0.4em]" style="color: rgba(235,235,245,0.5);">Bizmark Ops Center</p>
                    <h1 class="text-2xl md:text-3xl font-bold" style="color: #FFFFFF;">
                        Semua metrik krusial tersaji ala mission control Bizmark.id
                    </h1>
                    <p class="text-sm md:text-base" style="color: rgba(235,235,245,0.75);">
                        Pantau alert penting, kas, dan momentum proyek tanpa banyak tab. Tindak lanjuti kartu prioritas yang sudah kami kurasi.
                    </p>
                </div>
                <div class="space-y-2.5 text-sm" style="color: rgba(235,235,245,0.65);">
                    <p><i class="fas fa-clock mr-2"></i>Last sync: {{ now()->format('d M Y, H:i') }}</p>
                    <p><i class="fas fa-user-shield mr-2"></i>Visibility: Direksi &amp; Ops Lead</p>
                    <a href="{{ route('projects.index') }}" class="inline-flex items-center px-4 py-2 rounded-apple text-xs font-semibold" style="background: rgba(10,132,255,0.25); color: rgba(235,235,245,0.9);">
                        Lihat semua proyek
                        <i class="fas fa-arrow-up-right-from-square ml-2"></i>
                    </a>
                </div>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-3 md:gap-4">
                <div class="rounded-apple-lg p-3.5 md:p-4" style="background: rgba(255,59,48,0.08);">
                    <p class="text-xs uppercase tracking-widest" style="color: rgba(255,59,48,0.8);">Urgent Alerts</p>
                    <h2 class="text-2xl font-bold mt-1.5" style="color: #FFFFFF;">{{ $criticalAlerts['total_urgent'] }}</h2>
                    <p class="text-xs" style="color: rgba(235,235,245,0.6);">Item perlu tindakan cepat</p>
                </div>
                <div class="rounded-apple-lg p-3.5 md:p-4" style="background: rgba(10,132,255,0.12);">
                    <p class="text-xs uppercase tracking-widest" style="color: rgba(10,132,255,0.9);">Runway</p>
                    <h2 class="text-2xl font-bold mt-1.5" style="color: rgba(10,132,255,1);">{{ $cashFlowStatus['runway_months'] }} bln</h2>
                    <p class="text-xs" style="color: rgba(235,235,245,0.6);">Status: {{ ucfirst($cashFlowStatus['status']) }}</p>
                </div>
                <div class="rounded-apple-lg p-3.5 md:p-4" style="background: rgba(191,90,242,0.12);">
                    <p class="text-xs uppercase tracking-widest" style="color: rgba(191,90,242,0.9);">Pending Docs</p>
                    <h2 class="text-2xl font-bold mt-1.5" style="color: #FFFFFF;">{{ $pendingApprovals['total_pending'] }}</h2>
                    <p class="text-xs" style="color: rgba(235,235,245,0.6);">Menunggu peninjauan</p>
                </div>
                <div class="rounded-apple-lg p-3.5 md:p-4" style="background: rgba(52,199,89,0.12);">
                    <p class="text-xs uppercase tracking-widest" style="color: rgba(52,199,89,0.9);">Agenda 30 Hari</p>
                    <h2 class="text-2xl font-bold mt-1.5" style="color: rgba(52,199,89,1);">{{ $thisWeek['total_items'] }}</h2>
                    <p class="text-xs" style="color: rgba(235,235,245,0.6);">Tasks &amp; milestones</p>
                </div>
            </div>
        </div>
    </section>

    {{-- Critical focus deck --}}
    <section class="space-y-3 md:space-y-4">
        <div class="flex items-center justify-between flex-wrap gap-2.5">
            <div>
                <p class="text-xs uppercase tracking-[0.4em]" style="color: rgba(235,235,245,0.5);">Prioritas 01</p>
                <h2 class="text-2xl font-semibold" style="color:#FFFFFF;">Critical Focus</h2>
                <p class="text-sm" style="color: rgba(235,235,245,0.65);">Menggabungkan insight urgent, cash-flow pulse, dan antrean approval.</p>
            </div>
            @if($criticalAlerts['total_urgent'] > 0 || $cashFlowStatus['status'] === 'critical')
            <span class="px-3 py-1 rounded-full text-xs font-semibold" style="background: rgba(255,59,48,0.15); color: rgba(255,59,48,0.95);">
                <i class="fas fa-bolt mr-1"></i>Mode mitigasi aktif
            </span>
            @else
            <span class="px-3 py-1 rounded-full text-xs font-semibold" style="background: rgba(52,199,89,0.15); color: rgba(52,199,89,0.9);">
                <i class="fas fa-thumbs-up mr-1"></i>Semua metrik stabil
            </span>
            @endif
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-3 gap-3 md:gap-4">
            {{-- Urgent board --}}
            <div class="card-elevated rounded-apple-lg p-4 md:p-5 flex flex-col">
                <div class="flex items-center justify-between mb-3">
                    <div>
                        <p class="text-xs uppercase tracking-widest" style="color: rgba(235,235,245,0.5);">Urgent actions</p>
                        <h3 class="text-xl font-semibold text-white">Prioritas pemulihan</h3>
                    </div>
                    <span class="px-3 py-1 rounded-full text-xs font-semibold" style="background: rgba(255,59,48,0.12); color: rgba(255,59,48,0.9);">
                        {{ $criticalAlerts['total_urgent'] }} items
                    </span>
                </div>
                <div class="space-y-2.5 overflow-y-auto pr-1" style="max-height: 330px;">
                    @php $projectsCount = count($criticalAlerts['overdue_projects']); $tasksCount = count($criticalAlerts['overdue_tasks']); @endphp
                    @if($projectsCount)
                        <p class="text-xs font-semibold uppercase tracking-widest" style="color: rgba(255,255,255,0.5);">Proyek terlambat</p>
                        @foreach($criticalAlerts['overdue_projects'] as $project)
                        <a href="{{ route('projects.show', $project) }}" class="block p-3 rounded-apple hover:bg-dark-elevated-2 transition-apple" style="background: rgba(255,59,48,0.08);">
                            <div class="flex items-start justify-between">
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-semibold text-white truncate">{{ $project->name }}</p>
                                    <p class="text-xs mt-1" style="color: rgba(255,149,0,0.9);">
                                        <i class="fas fa-exclamation-circle mr-1"></i>
                                        {{ $project->days_overdue }} hari terlambat
                                    </p>
                                    @if($project->institution)
                                    <p class="text-xs mt-0.5" style="color: rgba(255,255,255,0.5);">{{ $project->institution->name }}</p>
                                    @endif
                                </div>
                                <i class="fas fa-chevron-right text-xs" style="color: rgba(255,255,255,0.3);"></i>
                            </div>
                        </a>
                        @endforeach
                    @endif

                    @if($tasksCount)
                        <p class="text-xs font-semibold uppercase tracking-widest mt-4" style="color: rgba(255,255,255,0.5);">Tasks overdue</p>
                        @foreach($criticalAlerts['overdue_tasks'] as $task)
                        <a href="{{ route('tasks.show', $task) }}" class="block p-3 rounded-apple hover:bg-dark-elevated-2 transition-apple" style="background: rgba(255,149,0,0.08);">
                            <div class="flex items-start justify-between">
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-semibold text-white truncate">{{ $task->title }}</p>
                                    <p class="text-xs mt-1" style="color: rgba(255,149,0,1);">
                                        <i class="fas fa-clock mr-1"></i>{{ $task->days_overdue }} hari terlambat
                                    </p>
                                    <p class="text-xs mt-0.5" style="color: rgba(255,255,255,0.5);">Assigned: {{ $task->assignedUser->name ?? 'Unassigned' }}</p>
                                </div>
                            </div>
                        </a>
                        @endforeach
                    @endif

                    @if(!$projectsCount && !$tasksCount)
                    <div class="text-center py-12">
                        <i class="fas fa-check-circle text-3xl" style="color: rgba(52,199,89,0.8);"></i>
                        <p class="mt-3 text-sm font-medium" style="color: rgba(255,255,255,0.8);">Tidak ada isu kritis aktif</p>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Cash flow pulse --}}
            <div class="card-elevated rounded-apple-lg p-4 md:p-5 space-y-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs uppercase tracking-widest" style="color: rgba(235,235,245,0.5);">Cash flow pulse</p>
                        <h3 class="text-xl font-semibold text-white">Kesehatan finansial</h3>
                    </div>
                    <span class="px-3 py-1 rounded-full text-xs font-bold uppercase" style="background: {{ $cashFlowStatus['status_color'] }}20; color: {{ $cashFlowStatus['status_color'] }};">
                        {{ $cashFlowStatus['status'] }}
                    </span>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <p class="text-xs" style="color: rgba(255,255,255,0.6);">Saldo saat ini</p>
                        <p class="text-3xl font-bold text-white">Rp {{ number_format($cashFlowStatus['current_balance']) }}</p>
                        <p class="text-xs mt-1" style="color: rgba(255,255,255,0.4);">Burn {{ number_format($cashFlowStatus['monthly_burn_rate']) }}/bln</p>
                    </div>
                    <div>
                        <p class="text-xs" style="color: rgba(255,255,255,0.6);">Runway</p>
                        <p class="text-3xl font-bold" style="color: {{ $cashFlowStatus['status_color'] }};">{{ $cashFlowStatus['runway_months'] }} bln</p>
                        <p class="text-xs mt-1" style="color: rgba(255,255,255,0.4);">{{ $cashFlowStatus['runway_label'] ?? 'Monitor per minggu' }}</p>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-2.5">
                    <div class="p-3 rounded-xl" style="background: rgba(84,84,88,0.35);">
                        <p class="text-xs" style="color: rgba(255,255,255,0.6);">Burn Rate</p>
                        <p class="text-xl font-bold text-white">{{ number_format($cashFlowStatus['monthly_burn_rate'] / 1000000, 1) }}M</p>
                        <p class="text-xs" style="color: rgba(255,255,255,0.4);">per bulan</p>
                    </div>
                    <div class="p-3 rounded-xl" style="background: rgba(84,84,88,0.35);">
                        <p class="text-xs" style="color: rgba(255,255,255,0.6);">Overdue invoices</p>
                        <p class="text-xl font-bold" style="color: {{ $cashFlowStatus['overdue_invoices'] > 0 ? '#FF3B30' : '#34C759' }};">
                            {{ $cashFlowStatus['overdue_invoices'] > 0 ? 'Rp '.number_format($cashFlowStatus['overdue_invoices']) : '0' }}
                        </p>
                        <p class="text-xs" style="color: rgba(255,255,255,0.4);">Tagih segera</p>
                    </div>
                </div>
                <div class="rounded-apple-lg p-3" style="background: rgba(10,132,255,0.12);">
                    <p class="text-xs" style="color: rgba(255,255,255,0.6);">Catatan</p>
                    <p class="text-sm" style="color: rgba(255,255,255,0.85);">
                        Prioritaskan tagihan {{ $cashFlowStatus['top_client'] ?? 'client utama' }} agar runway tetap > 4 bulan.
                    </p>
                </div>
            </div>

            {{-- Pending approvals --}}
            <div class="card-elevated rounded-apple-lg p-4 md:p-5 flex flex-col">
                <div class="flex items-center justify-between mb-3">
                    <div>
                        <p class="text-xs uppercase tracking-widest" style="color: rgba(235,235,245,0.5);">Approvals</p>
                        <h3 class="text-xl font-semibold text-white">Dokumen menunggu</h3>
                    </div>
                    <span class="px-3 py-1 rounded-full text-xs font-semibold" style="background: rgba(191,90,242,0.15); color: rgba(191,90,242,0.95);">
                        {{ $pendingApprovals['total_pending'] }}
                    </span>
                </div>
                <div class="space-y-2.5 overflow-y-auto pr-1" style="max-height: 320px;">
                    @forelse($pendingApprovals['pending_documents'] as $document)
                    <div class="p-2.5 rounded-apple" style="background: rgba(191,90,242,0.08);">
                        <div class="flex items-start justify-between">
                            <div class="min-w-0 flex-1">
                                <p class="text-sm font-semibold text-white truncate">{{ $document->name }}</p>
                                <p class="text-xs mt-0.5" style="color: rgba(191,90,242,1);">
                                    <i class="fas fa-clock mr-1"></i>{{ $document->days_waiting }} hari menunggu
                                </p>
                                <p class="text-xs" style="color: rgba(255,255,255,0.5);">Uploader: {{ $document->uploader->name ?? 'Unknown' }}</p>
                                @if($document->project)
                                <p class="text-xs" style="color: rgba(255,255,255,0.5);">Project: {{ $document->project->name }}</p>
                                @endif
                            </div>
                        </div>
                        <div class="flex gap-2 mt-2.5">
                            <a href="{{ route('documents.show', $document) }}" class="flex-1 px-3 py-2 rounded-apple text-xs font-semibold text-center" style="background: rgba(52,199,89,0.25); color: rgba(52,199,89,0.95);">
                                <i class="fas fa-check mr-1"></i>Approve
                            </a>
                            <a href="{{ route('documents.show', $document) }}" class="px-3 py-2 rounded-apple text-xs font-semibold" style="background: rgba(255,255,255,0.1); color: rgba(255,255,255,0.7);">
                                <i class="fas fa-eye mr-1"></i>Preview
                            </a>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-10">
                        <i class="fas fa-check-circle text-3xl" style="color: rgba(52,199,89,0.9);"></i>
                        <p class="mt-3 text-sm font-medium" style="color: rgba(255,255,255,0.8);">Tidak ada pending approvals</p>
                    </div>
                    @endforelse
                </div>
                @if($pendingApprovals['total_pending'] > 0)
                <a href="{{ route('documents.index') }}?status=review" class="mt-4 text-xs font-semibold" style="color: rgba(235,235,245,0.7);">
                    Lihat semua dokumen →
                </a>
                @endif
            </div>
        </div>
    </section>

    {{-- Financial intelligence --}}
    <section class="space-y-3 md:space-y-4">
        <div class="flex items-center justify-between flex-wrap gap-3">
            <div>
                <p class="text-xs uppercase tracking-[0.4em]" style="color: rgba(235,235,245,0.5);">Prioritas 02</p>
                <h2 class="text-2xl font-semibold" style="color:#FFFFFF;">Financial Intelligence</h2>
                <p class="text-sm" style="color: rgba(235,235,245,0.65);">Cash vs burn, aging piutang, dan pemakaian budget terpadat.</p>
            </div>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-3 gap-3 md:gap-4">
            {{-- Income vs Expense --}}
            <div class="card-elevated rounded-apple-lg p-4 md:p-5 space-y-4">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-white">Aliran Kas Bulan Ini</h3>
                    <span class="text-xs px-3 py-1 rounded-full" style="background: rgba(10,132,255,0.15); color: rgba(10,132,255,0.9);">Live</span>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-xs" style="color: rgba(255,255,255,0.6);">Payments</p>
                        <p class="text-2xl font-bold text-white">{{ number_format($cashFlowSummary['payments_this_month'] / 1000000, 1) }}M</p>
                        @if($cashFlowSummary['payments_growth'] != 0)
                        <p class="text-xs" style="color: {{ $cashFlowSummary['payments_growth'] > 0 ? '#34C759' : '#FF3B30' }};">
                            <i class="fas fa-arrow-{{ $cashFlowSummary['payments_growth'] > 0 ? 'up' : 'down' }} mr-1"></i>{{ abs($cashFlowSummary['payments_growth']) }}%
                        </p>
                        @endif
                    </div>
                    <div>
                        <p class="text-xs" style="color: rgba(255,255,255,0.6);">Expenses</p>
                        <p class="text-2xl font-bold text-white">{{ number_format($cashFlowSummary['expenses_this_month'] / 1000000, 1) }}M</p>
                        @if($cashFlowSummary['expenses_growth'] != 0)
                        <p class="text-xs" style="color: {{ $cashFlowSummary['expenses_growth'] > 0 ? '#FF3B30' : '#34C759' }};">
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
                <div class="space-y-3">
                    <div>
                        <div class="flex items-center justify-between text-xs" style="color: rgba(255,255,255,0.6);">
                            <span>Payments</span>
                            <span>Rp {{ number_format($cashFlowSummary['payments_this_month']) }}</span>
                        </div>
                        <div class="h-2 rounded-full overflow-hidden" style="background: rgba(52,199,89,0.15);">
                            <div class="h-full" style="width: {{ $paymentsWidth }}%; background: #34C759;"></div>
                        </div>
                    </div>
                    <div>
                        <div class="flex items-center justify-between text-xs" style="color: rgba(255,255,255,0.6);">
                            <span>Expenses</span>
                            <span>Rp {{ number_format($cashFlowSummary['expenses_this_month']) }}</span>
                        </div>
                        <div class="h-2 rounded-full overflow-hidden" style="background: rgba(255,59,48,0.15);">
                            <div class="h-full" style="width: {{ $expensesWidth }}%; background: #FF3B30;"></div>
                        </div>
                    </div>
                </div>
                <div class="text-sm font-semibold" style="color: {{ $cashFlowSummary['is_profitable'] ? '#34C759' : '#FF3B30' }};">
                    {{ $cashFlowSummary['is_profitable'] ? 'Surplus ' : 'Defisit ' }}{{ number_format($cashFlowSummary['net_this_month'] / 1000000, 1) }}M bulan ini
                </div>
                <div class="grid grid-cols-2 gap-3 text-xs" style="color: rgba(255,255,255,0.6);">
                    <div>
                        <p>Payments YTD</p>
                        <p class="text-lg font-semibold text-white">{{ number_format($cashFlowSummary['payments_ytd'] / 1000000, 1) }}M</p>
                    </div>
                    <div>
                        <p>Expenses YTD</p>
                        <p class="text-lg font-semibold text-white">{{ number_format($cashFlowSummary['expenses_ytd'] / 1000000, 1) }}M</p>
                    </div>
                </div>
            </div>

            {{-- Receivables --}}
            <div class="card-elevated rounded-apple-lg p-4 md:p-5 space-y-4">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-white">Receivables Aging</h3>
                    <span class="text-xs px-3 py-1 rounded-full" style="background: rgba(255,214,10,0.15); color: rgba(255,214,10,0.9);">
                        {{ $receivablesAging['invoice_count'] + $receivablesAging['internal_count'] }} items
                    </span>
                </div>
                <div class="rounded-apple-lg p-4" style="background: rgba(10,132,255,0.12);">
                    <p class="text-xs" style="color: rgba(255,255,255,0.6);">Outstanding</p>
                    <p class="text-3xl font-bold" style="color: rgba(10,132,255,1);">{{ number_format($receivablesAging['total_receivables'] / 1000000, 1) }}M</p>
                    <p class="text-xs" style="color: rgba(255,255,255,0.5);">Invoice: Rp {{ number_format($receivablesAging['invoice_receivables']) }} • Kasbon: Rp {{ number_format($receivablesAging['internal_receivables']) }}</p>
                </div>
                <div class="space-y-2 text-sm">
                    @foreach(['under_30' => '0-30 hari', 'days_30_60' => '30-60 hari', 'days_60_90' => '60-90 hari', 'over_90' => '90+ hari'] as $key => $label)
                        @if($receivablesAging['aging'][$key] > 0)
                        <div class="p-2 rounded-apple flex items-center justify-between" style="background: rgba(255,255,255,0.04);">
                            <span style="color: rgba(255,255,255,0.75);">{{ $label }}</span>
                            <span class="font-semibold" style="color: {{ $key === 'over_90' ? '#FF3B30' : ($key === 'days_60_90' ? '#FF9500' : '#FFFFFF') }};">
                                {{ number_format($receivablesAging['aging'][$key] / 1000000, 1) }}M
                            </span>
                        </div>
                        @endif
                    @endforeach
                </div>
                @if($receivablesAging['internal_count'] > 0)
                <div class="space-y-2">
                    <p class="text-xs uppercase tracking-widest" style="color: rgba(255,255,255,0.5);">Kasbon internal</p>
                    @foreach($receivablesAging['internal_list'] as $kasbon)
                    <div class="p-2 rounded-apple" style="background: rgba(255,255,255,0.03);">
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-white">{{ $kasbon['from'] }}</span>
                            <span class="text-sm font-bold" style="color: rgba(142,142,147,0.6);">Rp {{ number_format($kasbon['remaining']) }}</span>
                        </div>
                        <p class="text-xs" style="color: rgba(255,255,255,0.5);">{{ $kasbon['description'] }} • {{ \Carbon\Carbon::parse($kasbon['date'])->format('d M Y') }}</p>
                    </div>
                    @endforeach
                </div>
                @else
                <p class="text-xs text-center py-4" style="color: rgba(52,199,89,0.9);">Tidak ada kasbon outstanding</p>
                @endif
            </div>

            {{-- Budget status --}}
            <div class="card-elevated rounded-apple-lg p-4 md:p-5 space-y-4">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-white">Budget Utilization</h3>
                    <div class="text-right">
                        <p class="text-xs" style="color: rgba(255,255,255,0.5);">Overall</p>
                        <p class="text-2xl font-bold text-white">{{ $budgetStatus['overall_utilization'] }}%</p>
                    </div>
                </div>
                @if($budgetStatus['top_projects']->count() > 0)
                <div class="space-y-3">
                    @foreach($budgetStatus['top_projects'] as $project)
                    <a href="{{ route('projects.show', $project) }}" class="block p-3 rounded-apple hover:bg-dark-elevated-2 transition-apple" style="background: {{ $project->status_color }}15;">
                        <div class="flex items-center justify-between mb-2">
                            <div class="flex-1 min-w-0 mr-3">
                                <p class="text-sm font-semibold text-white truncate">{{ $project->name }}</p>
                                <p class="text-xs" style="color: rgba(255,255,255,0.5);">Budget Rp {{ number_format($project->budget) }} • Spent Rp {{ number_format($project->actual_cost) }}</p>
                            </div>
                            <span class="text-base font-bold" style="color: {{ $project->status_color }};">{{ $project->variance_percentage }}%</span>
                        </div>
                        <div class="h-2 rounded-full overflow-hidden" style="background: rgba(255,255,255,0.1);">
                            <div class="h-full" style="width: {{ min($project->variance_percentage, 100) }}%; background: {{ $project->status_color }};"></div>
                        </div>
                    </a>
                    @endforeach
                </div>
                @else
                <div class="text-center py-8">
                    <p class="text-sm" style="color: rgba(255,255,255,0.5);">Belum ada data budget</p>
                </div>
                @endif
            </div>
        </div>
    </section>

    {{-- Operational momentum --}}
    <section class="space-y-3 md:space-y-4">
        <div class="flex items-center justify-between flex-wrap gap-2.5">
            <div>
                <p class="text-xs uppercase tracking-[0.4em]" style="color: rgba(235,235,245,0.5);">Prioritas 03</p>
                <h2 class="text-2xl font-semibold" style="color:#FFFFFF;">Operational Momentum</h2>
                <p class="text-sm" style="color: rgba(235,235,245,0.65);">Timeline 30 hari, sebaran status proyek, dan aktivitas terbaru.</p>
            </div>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-3 gap-3 md:gap-4">
            {{-- Timeline --}}
            <div class="card-elevated rounded-apple-lg p-4 md:p-5 space-y-3">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-white">Next 30 Days</h3>
                    <span class="text-xs" style="color: rgba(255,255,255,0.5);">{{ $thisWeek['period_start'] }} – {{ $thisWeek['period_end'] }}</span>
                </div>
                <div class="space-y-1.5 overflow-y-auto pr-1" style="max-height: 340px;">
                    @if($thisWeek['total_items'] > 0)
                        @foreach($thisWeek['tasks'] as $task)
                        <a href="{{ route('projects.show', $task['project_id']) }}" class="block p-2.5 rounded-apple hover:bg-dark-elevated-2 transition-apple" style="background: rgba({{ $task['is_past'] ? '255,59,48' : ($task['is_today'] ? '255,204,0' : '52,199,89') }},0.08);">
                            <div class="flex items-start gap-2">
                                <div class="w-2 h-2 rounded-full mt-1.5" style="background: {{ $task['priority_color'] }};"></div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-semibold text-white truncate">{{ $task['title'] }}</p>
                                    <p class="text-xs" style="color: rgba(255,255,255,0.5);">{{ $task['project'] }}</p>
                                    <div class="flex items-center gap-2 mt-1 text-xs">
                                        <span style="color: {{ $task['priority_color'] }};"><i class="fas fa-clock mr-1"></i>{{ $task['deadline_formatted'] }}</span>
                                        @if($task['is_past'])
                                        <span class="px-2 py-0.5 rounded-full" style="background: rgba(255,59,48,0.2); color: rgba(255,59,48,0.9);">{{ $task['days_until'] }}d overdue</span>
                                        @elseif($task['is_today'])
                                        <span class="px-2 py-0.5 rounded-full" style="background: rgba(255,204,0,0.2); color: rgba(255,204,0,1);">Hari ini!</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </a>
                        @endforeach
                        @foreach($thisWeek['projects'] as $project)
                        <a href="{{ route('projects.show', $project['id']) }}" class="block p-2.5 rounded-apple hover:bg-dark-elevated-2 transition-apple" style="background: rgba(10,132,255,0.08);">
                            <p class="text-sm font-semibold text-white">{{ $project['name'] }}</p>
                            <p class="text-xs" style="color: rgba(255,255,255,0.5);">{{ $project['deadline_formatted'] ?? 'No deadline' }}</p>
                            <p class="text-xs mt-1" style="color: rgba(10,132,255,1);"><i class="fas fa-flag mr-1"></i>{{ $project['is_past'] ? $project['days_until'] . 'd overdue' : $project['days_until'] . ' hari lagi' }}</p>
                        </a>
                        @endforeach
                    @else
                    <div class="text-center py-10">
                        <p class="text-sm" style="color: rgba(255,255,255,0.5);">Tidak ada agenda</p>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Project status distribution --}}
            <div class="card-elevated rounded-apple-lg p-4 md:p-5 space-y-3">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-white">Project Distribution</h3>
                    <span class="text-xs" style="color: rgba(255,255,255,0.5);">{{ $projectStatusDistribution['total'] }} proyek</span>
                </div>
                    <div class="space-y-2.5">
                    @forelse($projectStatusDistribution['groups'] as $statusGroup)
                    <div>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full" style="background: {{ $statusGroup['color'] }};"></span>
                                <p class="text-sm font-semibold text-white">{{ $statusGroup['label'] }}</p>
                            </div>
                            <span class="text-sm font-bold" style="color: rgba(235,235,245,0.7);">{{ $statusGroup['count'] }}</span>
                        </div>
                        <div class="ml-4 mt-1 space-y-1">
                            @foreach($statusGroup['projects'] as $project)
                            <a href="{{ route('projects.show', $project['id']) }}" class="block text-xs" style="color: rgba(255,255,255,0.5);">• {{ $project['name'] }}</a>
                            @endforeach
                        </div>
                    </div>
                    @empty
                    <p class="text-xs" style="color: rgba(255,255,255,0.5);">Belum ada proyek aktif</p>
                    @endforelse
                </div>
            </div>

            {{-- Recent activity --}}
            <div class="card-elevated rounded-apple-lg p-4 md:p-5 space-y-3">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-white">Recent Activity</h3>
                    <span class="text-xs px-3 py-1 rounded-full" style="background: rgba(191,90,242,0.15); color: rgba(191,90,242,0.95);">{{ $recentActivities['count'] }}</span>
                </div>
                <div class="space-y-2 overflow-y-auto pr-1" style="max-height: 360px;">
                    @forelse($recentActivities['activities'] as $activity)
                    <a href="{{ $activity['link'] }}" class="block p-3 rounded-apple hover:bg-dark-elevated-2 transition-apple" style="background: rgba(255,255,255,0.03);">
                        <p class="text-sm font-semibold text-white">{{ $activity['title'] }}</p>
                        <p class="text-xs mt-0.5" style="color: rgba(255,255,255,0.5);">{{ $activity['description'] }}</p>
                        <p class="text-xs mt-0.5" style="color: rgba(255,255,255,0.4);"><i class="fas fa-clock mr-1"></i>{{ $activity['time_formatted'] }}</p>
                    </a>
                    @empty
                    <div class="text-center py-10">
                        <p class="text-sm" style="color: rgba(255,255,255,0.5);">Belum ada aktivitas</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
