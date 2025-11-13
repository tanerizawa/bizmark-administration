@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard - Critical Overview')

@section('content')
    {{-- Alert Banner if Critical Issues --}}
    @if($criticalAlerts['total_urgent'] > 0 || $cashFlowStatus['status'] === 'critical')
    <div class="mb-4 p-2.5 rounded-apple" style="background: rgba(255, 59, 48, 0.1);">
        <div class="flex items-center">
            <div class="w-10 h-10 rounded-full flex items-center justify-center mr-2.5" style="background-color: rgba(142, 142, 147, 0.2);">
                <i class="fas fa-exclamation-triangle text-lg" style="color: rgba(142, 142, 147, 0.8);"></i>
            </div>
            <div>
                <p class="text-sm font-semibold text-white">Perhatian Diperlukan</p>
                <p class="text-xs" style="color: rgba(255,255,255,0.7);">
                    {{ $criticalAlerts['total_urgent'] }} item urgent
                    @if($cashFlowStatus['status'] === 'critical')
                        • Cash flow critical ({{ $cashFlowStatus['runway_months'] }} bulan)
                    @endif
                </p>
            </div>
        </div>
    </div>
    @endif

    {{-- PHASE 1: CRITICAL ALERTS DASHBOARD --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-3 mb-4">
        
        {{-- Card 1: Urgent Actions --}}
        <div class="card-elevated rounded-apple-lg overflow-hidden transition-all duration-300 hover:scale-[1.01] hover:shadow-2xl">
            <div class="p-4 border-b" style="border-color: rgba(255,255,255,0.1);">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-base font-semibold" style="color: #FFFFFF;"><i class="fas fa-exclamation-circle mr-2" style="color: rgba(142, 142, 147, 0.6);"></i>Urgent Actions</h3>
                        <p class="text-xs mt-1" style="color: rgba(255,255,255,0.5);">Perlu tindakan segera</p>
                    </div>
                    @if($criticalAlerts['total_urgent'] > 0)
                    <span class="px-3 py-1 rounded-full text-sm font-bold" style="background: rgba(255, 59, 48, 0.2); color: rgba(142, 142, 147, 0.6);">
                        {{ $criticalAlerts['total_urgent'] }}
                    </span>
                    @endif
                </div>
            </div>
            <div class="p-3 space-y-2 max-h-96 overflow-y-auto">
                {{-- Overdue Projects --}}
                @foreach($criticalAlerts['overdue_projects'] as $project)
                <a href="{{ route('projects.show', $project) }}" class="block p-2 rounded-apple hover:bg-dark-elevated-2 hover:scale-[1.01] transition-all duration-200" style="background: rgba(255, 59, 48, 0.08);">
                    <div class="flex items-start justify-between">
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center gap-2">
                                <i class="fas fa-project-diagram text-xs" style="color: rgba(142, 142, 147, 0.6);"></i>
                                <p class="text-sm font-semibold text-white truncate">{{ $project->name }}</p>
                            </div>
                            <p class="text-xs mt-1" style="color: rgba(142, 142, 147, 0.8);">
                                <i class="fas fa-exclamation-circle mr-1"></i>
                                {{ $project->days_overdue }} hari terlambat
                            </p>
                            @if($project->institution)
                            <p class="text-xs mt-0.5" style="color: rgba(255,255,255,0.5);">
                                {{ $project->institution->name }}
                            </p>
                            @endif
                        </div>
                        <i class="fas fa-chevron-right text-xs ml-2" style="color: rgba(255,255,255,0.3);"></i>
                    </div>
                </a>
                @endforeach

                {{-- Overdue Tasks --}}
                @foreach($criticalAlerts['overdue_tasks'] as $task)
                <a href="{{ route('tasks.show', $task) }}" class="block p-2.5 rounded-apple hover:bg-dark-elevated-2 transition-apple" style="background: rgba(255, 149, 0, 0.08);">
                    <div class="flex items-start justify-between">
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center gap-2">
                                <i class="fas fa-tasks text-xs" style="color: rgba(142, 142, 147, 0.6);"></i>
                                <p class="text-sm font-semibold text-white truncate">{{ $task->title }}</p>
                            </div>
                            <p class="text-xs mt-1" style="color: rgba(255, 149, 0, 1);">
                                <i class="fas fa-clock mr-1"></i>
                                {{ $task->days_overdue }} hari terlambat
                            </p>
                            <p class="text-xs mt-0.5" style="color: rgba(255,255,255,0.5);">
                                Assigned: {{ $task->assignedUser->name ?? 'Unassigned' }}
                            </p>
                        </div>
                        <i class="fas fa-chevron-right text-xs ml-2" style="color: rgba(255,255,255,0.3);"></i>
                    </div>
                </a>
                @endforeach

                {{-- Due Today --}}
                @foreach($criticalAlerts['due_today'] as $item)
                <a href="{{ $item->type === 'project' ? route('projects.show', $item) : route('tasks.show', $item) }}" class="block p-2.5 rounded-apple hover:bg-dark-elevated-2 transition-apple" style="background: rgba(255, 204, 0, 0.08);">
                    <div class="flex items-start justify-between">
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center gap-2">
                                <i class="fas fa-{{ $item->type === 'project' ? 'project-diagram' : 'tasks' }} text-xs" style="color: rgba(142, 142, 147, 0.6);"></i>
                                <p class="text-sm font-semibold text-white truncate">
                                    {{ $item->type === 'project' ? $item->name : $item->title }}
                                </p>
                            </div>
                            <p class="text-xs mt-1" style="color: rgba(255, 204, 0, 1);">
                                <i class="fas fa-bell mr-1"></i>
                                Due hari ini
                            </p>
                        </div>
                        <i class="fas fa-chevron-right text-xs ml-2" style="color: rgba(255,255,255,0.3);"></i>
                    </div>
                </a>
                @endforeach

                {{-- Empty State --}}
                @if($criticalAlerts['total_urgent'] === 0)
                <div class="text-center py-8">
                    <div class="w-16 h-16 rounded-full mx-auto mb-3 flex items-center justify-center" style="background: rgba(52, 199, 89, 0.1);">
                        <i class="fas fa-check-circle text-2xl" style="color: rgba(142, 142, 147, 0.6);"></i>
                    </div>
                    <p class="text-sm font-medium text-white">Semua Lancar!</p>
                    <p class="text-xs mt-1" style="color: rgba(255,255,255,0.5);">Tidak ada item urgent</p>
                </div>
                @endif
            </div>
            @if($criticalAlerts['total_urgent'] > 0)
            <div class="p-3 border-t" style="border-color: rgba(255,255,255,0.1);">
                <a href="{{ route('projects.index') }}?filter=overdue" class="text-xs font-medium" style="color: rgba(142, 142, 147, 0.6);">
                    Lihat semua urgent items →
                </a>
            </div>
            @endif
        </div>

        {{-- Card 2: Cash Flow Status --}}
        <div class="card-elevated rounded-apple-lg overflow-hidden transition-all duration-300 hover:scale-[1.01] hover:shadow-2xl">
            <div class="p-4 border-b" style="border-color: rgba(255,255,255,0.1);">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-base font-semibold" style="color: #FFFFFF;"><i class="fas fa-wallet mr-2" style="color: rgba(142, 142, 147, 0.6);"></i>Cash Flow Status</h3>
                        <p class="text-xs mt-1" style="color: rgba(255,255,255,0.5);">Kesehatan finansial</p>
                    </div>
                    <span class="px-3 py-1 rounded-full text-xs font-bold uppercase" style="background: {{ $cashFlowStatus['status_color'] }}20; color: {{ $cashFlowStatus['status_color'] }};">
                        {{ $cashFlowStatus['status'] }}
                    </span>
                </div>
            </div>
            <div class="p-4 space-y-4">
                {{-- Current Balance --}}
                <div>
                    <p class="text-xs font-medium mb-2" style="color: rgba(255,255,255,0.6);">Cash Balance</p>
                    <div class="flex items-baseline">
                        <h2 class="text-4xl font-bold text-white">{{ number_format($cashFlowStatus['current_balance'] / 1000000, 1) }}</h2>
                        <span class="ml-2 text-xl font-medium" style="color: rgba(255,255,255,0.6);">M</span>
                    </div>
                </div>

                {{-- Burn Rate & Runway --}}
                <div class="grid grid-cols-2 gap-3">
                    <div class="p-3 rounded-xl" style="background: rgba(84,84,88,0.3);">
                        <p class="text-xs mb-1" style="color: rgba(255,255,255,0.6);">Burn Rate</p>
                        <p class="text-lg font-bold text-white">{{ number_format($cashFlowStatus['monthly_burn_rate'] / 1000000, 1) }}M</p>
                        <p class="text-xs" style="color: rgba(255,255,255,0.5);">per bulan</p>
                    </div>
                    <div class="p-3 rounded-xl" style="background: rgba(84,84,88,0.3);">
                        <p class="text-xs mb-1" style="color: rgba(255,255,255,0.6);">Runway</p>
                        <p class="text-lg font-bold" style="color: {{ $cashFlowStatus['status_color'] }};">{{ $cashFlowStatus['runway_months'] }}</p>
                        <p class="text-xs" style="color: rgba(255,255,255,0.5);">bulan</p>
                    </div>
                </div>

                {{-- Overdue Invoices Alert --}}
                @if($cashFlowStatus['overdue_invoices'] > 0)
                <div class="p-3 rounded-xl" style="background: rgba(255, 59, 48, 0.1); border: 1px solid rgba(255, 59, 48, 0.3);">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-medium" style="color: rgba(255,255,255,0.7);">Overdue Invoices</p>
                            <p class="text-xl font-bold mt-1" style="color: rgba(142, 142, 147, 0.6);">
                                Rp {{ number_format($cashFlowStatus['overdue_invoices'] / 1000000, 1) }}M
                            </p>
                        </div>
                        <div class="w-12 h-12 rounded-full flex items-center justify-center" style="background-color: rgba(142, 142, 147, 0.2);">
                            <i class="fas fa-exclamation-triangle text-xl" style="color: rgba(142, 142, 147, 0.6);"></i>
                        </div>
                    </div>
                    <a href="{{ route('projects.index') }}" class="mt-2 inline-block px-4 py-2 rounded-apple text-xs font-medium text-white transition-apple" style="background: #FF3B30;">
                        <i class="fas fa-dollar-sign mr-1"></i>
                        Tagih Sekarang
                    </a>
                </div>
                @else
                <div class="p-3 rounded-xl" style="background: rgba(52, 199, 89, 0.1);">
                    <p class="text-sm font-medium" style="color: rgba(142, 142, 147, 0.6);">
                        <i class="fas fa-check-circle mr-2"></i>
                        Semua invoice up-to-date
                    </p>
                </div>
                @endif
            </div>
        </div>

        {{-- Card 3: Pending Approvals --}}
        <div class="card-elevated rounded-apple-lg overflow-hidden transition-all duration-300 hover:scale-[1.01] hover:shadow-2xl">
            <div class="p-4 border-b" style="border-color: rgba(255,255,255,0.1);">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-base font-semibold" style="color: #FFFFFF;"><i class="fas fa-file-alt mr-2" style="color: rgba(142, 142, 147, 0.6);"></i>Pending Approvals</h3>
                        <p class="text-xs mt-1" style="color: rgba(255,255,255,0.5);">Dokumen menunggu review</p>
                    </div>
                    @if($pendingApprovals['total_pending'] > 0)
                    <span class="px-3 py-1 rounded-full text-sm font-bold" style="background: rgba(191, 90, 242, 0.2); color: rgba(142, 142, 147, 0.6);">
                        {{ $pendingApprovals['total_pending'] }}
                    </span>
                    @endif
                </div>
            </div>
            <div class="p-4 space-y-2 max-h-96 overflow-y-auto">
                @foreach($pendingApprovals['pending_documents'] as $document)
                <div class="p-3 rounded-xl" style="background: rgba(191, 90, 242, 0.08);">
                    <div class="flex items-start justify-between mb-2">
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center gap-2">
                                <i class="fas fa-file-alt text-xs" style="color: rgba(142, 142, 147, 0.6);"></i>
                                <p class="text-sm font-semibold text-white truncate">{{ $document->name }}</p>
                            </div>
                            <p class="text-xs mt-1" style="color: rgba(191, 90, 242, 1);">
                                <i class="fas fa-clock mr-1"></i>
                                {{ $document->days_waiting }} hari menunggu
                            </p>
                            <p class="text-xs mt-0.5" style="color: rgba(255,255,255,0.5);">
                                Uploaded by: {{ $document->uploader->name ?? 'Unknown' }}
                            </p>
                            @if($document->project)
                            <p class="text-xs mt-0.5" style="color: rgba(255,255,255,0.5);">
                                Project: {{ $document->project->name }}
                            </p>
                            @endif
                        </div>
                    </div>
                    <div class="flex gap-2">
                        <a href="{{ route('documents.show', $document) }}" class="flex-1 px-3 py-2 rounded-apple text-xs font-medium text-center transition-apple" style="background: rgba(52, 199, 89, 0.2); color: rgba(142, 142, 147, 0.6);">
                            <i class="fas fa-check mr-1"></i>
                            Review
                        </a>
                        <a href="{{ route('documents.show', $document) }}" class="px-3 py-2 rounded-apple text-xs font-medium transition-apple" style="background: rgba(255,255,255,0.1); color: rgba(255,255,255,0.7);">
                            <i class="fas fa-eye mr-1"></i>
                            View
                        </a>
                    </div>
                </div>
                @endforeach

                {{-- Empty State --}}
                @if($pendingApprovals['total_pending'] === 0)
                <div class="text-center py-12">
                    <div class="w-16 h-16 rounded-full mx-auto mb-3 flex items-center justify-center" style="background: rgba(52, 199, 89, 0.1);">
                        <i class="fas fa-check-circle text-2xl" style="color: rgba(142, 142, 147, 0.6);"></i>
                    </div>
                    <p class="text-sm font-medium text-white">All Caught Up!</p>
                    <p class="text-xs mt-1" style="color: rgba(255,255,255,0.5);">Tidak ada pending approvals</p>
                </div>
                @endif
            </div>
            @if($pendingApprovals['total_pending'] > 0)
            <div class="p-3 border-t" style="border-color: rgba(255,255,255,0.1);">
                <a href="{{ route('documents.index') }}?status=review" class="text-xs font-medium" style="color: rgba(142, 142, 147, 0.6);">
                    Lihat semua pending documents →
                </a>
            </div>
            @endif
        </div>
    </div>

    {{-- PHASE 2: FINANCIAL DASHBOARD --}}
    <div class="mt-6 mb-4">
        <h2 class="text-lg font-bold" style="color: #FFFFFF;" mb-1"><i class="fas fa-chart-bar mr-2" style="color: rgba(142, 142, 147, 0.6);"></i>Financial Overview</h2>
        <p class="text-sm" style="color: rgba(255,255,255,0.5);">Ringkasan keuangan dan budget tracking</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-6">
        
        {{-- Card 4: Cash Flow Summary --}}
        <div class="card-elevated rounded-apple-lg overflow-hidden transition-all duration-300 hover:scale-[1.01] hover:shadow-2xl">
            <div class="p-4 border-b" style="border-color: rgba(255,255,255,0.1);">
                <h3 class="text-base font-semibold" style="color: #FFFFFF;"><i class="fas fa-dollar-sign mr-2" style="color: rgba(142, 142, 147, 0.6);"></i>Cash Flow Summary</h3>
                <p class="text-xs mt-1" style="color: rgba(255,255,255,0.5);">Total cash balance dan ringkasan keuangan</p>
            </div>
            <div class="p-3">
                {{-- Total Cash Balance Header --}}
                <div class="text-center p-3 rounded-xl mb-3" style="background: rgba(10, 132, 255, 0.1);">
                    <p class="text-xs mb-1" style="color: rgba(255,255,255,0.6);">Total Saldo Kas</p>
                    <p class="text-2xl font-bold" style="color: #0A84FF;">
                        Rp {{ number_format($cashFlowSummary['total_cash_balance']) }}
                    </p>
                    <p class="text-xs mt-0.5" style="color: rgba(255,255,255,0.5);">
                        <i class="fas fa-university mr-1" style="color: rgba(142, 142, 147, 0.6);"></i>
                        Semua rekening bank
                    </p>
                </div>

                {{-- Year-to-Date Summary --}}
                <div class="mb-3">
                    <div class="flex items-center justify-between mb-1.5">
                        <p class="text-xs font-medium" style="color: rgba(255,255,255,0.6);">Year-to-Date ({{ now()->year }})</p>
                    </div>
                    <div class="grid grid-cols-3 gap-2">
                        {{-- YTD Payments --}}
                        <div class="p-2 rounded-lg" style="background: rgba(255,255,255,0.02);">
                            <p class="text-xs mb-1" style="color: rgba(255,255,255,0.5);">Pemasukan</p>
                            <p class="text-sm font-bold" style="color: #34C759;">
                                {{ number_format($cashFlowSummary['payments_ytd'] / 1000000, 1) }}M
                            </p>
                        </div>

                        {{-- YTD Expenses --}}
                        <div class="p-2 rounded-lg" style="background: rgba(255,255,255,0.02);">
                            <p class="text-xs mb-1" style="color: rgba(255,255,255,0.5);">Pengeluaran</p>
                            <p class="text-sm font-bold" style="color: #FF3B30;">
                                {{ number_format($cashFlowSummary['expenses_ytd'] / 1000000, 1) }}M
                            </p>
                        </div>

                        {{-- YTD Net --}}
                        <div class="p-2 rounded-lg" style="background: rgba(255,255,255,0.02);">
                            <p class="text-xs mb-1" style="color: rgba(255,255,255,0.5);">Net YTD</p>
                            <p class="text-sm font-bold" style="color: {{ $cashFlowSummary['net_ytd'] >= 0 ? '#34C759' : '#FF3B30' }};">
                                {{ $cashFlowSummary['net_ytd'] >= 0 ? '+' : '' }}{{ number_format($cashFlowSummary['net_ytd'] / 1000000, 1) }}M
                            </p>
                        </div>
                    </div>
                </div>

                {{-- This Month Summary --}}
                <div class="mb-3">
                    <div class="flex items-center justify-between mb-1.5">
                        <p class="text-xs font-medium" style="color: rgba(255,255,255,0.6);">Bulan Ini ({{ now()->format('F Y') }})</p>
                    </div>
                    <div class="grid grid-cols-3 gap-2">
                        {{-- Payments In --}}
                        <div class="p-2 rounded-xl" style="background: rgba(52, 199, 89, 0.1);">
                            <p class="text-xs mb-0.5" style="color: rgba(255,255,255,0.6);">Pemasukan</p>
                            <p class="text-lg font-bold" style="color: rgba(142, 142, 147, 0.6);">
                                {{ number_format($cashFlowSummary['payments_this_month'] / 1000000, 1) }}M
                            </p>
                            @if($cashFlowSummary['payments_growth'] != 0)
                            <p class="text-xs mt-1" style="color: {{ $cashFlowSummary['payments_growth'] > 0 ? '#34C759' : '#FF3B30' }};">
                                <i class="fas fa-arrow-{{ $cashFlowSummary['payments_growth'] > 0 ? 'up' : 'down' }} mr-1"></i>
                                {{ abs($cashFlowSummary['payments_growth']) }}%
                            </p>
                            @endif
                        </div>

                        {{-- Expenses Out --}}
                        <div class="p-2 rounded-xl" style="background: rgba(255, 59, 48, 0.1);">
                            <p class="text-xs mb-0.5" style="color: rgba(255,255,255,0.6);">Pengeluaran</p>
                            <p class="text-lg font-bold" style="color: rgba(142, 142, 147, 0.6);">
                                {{ number_format($cashFlowSummary['expenses_this_month'] / 1000000, 1) }}M
                            </p>
                            @if($cashFlowSummary['expenses_growth'] != 0)
                            <p class="text-xs mt-1" style="color: {{ $cashFlowSummary['expenses_growth'] > 0 ? '#FF3B30' : '#34C759' }};">
                                <i class="fas fa-arrow-{{ $cashFlowSummary['expenses_growth'] > 0 ? 'up' : 'down' }} mr-1"></i>
                                {{ abs($cashFlowSummary['expenses_growth']) }}%
                            </p>
                            @endif
                        </div>

                        {{-- Net Profit/Loss --}}
                        <div class="p-2 rounded-xl" style="background: rgba(10, 132, 255, 0.1);">
                            <p class="text-xs mb-0.5" style="color: rgba(255,255,255,0.6);">Net</p>
                            <p class="text-lg font-bold" style="color: {{ $cashFlowSummary['is_profitable'] ? '#34C759' : '#FF3B30' }};">
                                {{ $cashFlowSummary['is_profitable'] ? '+' : '' }}{{ number_format($cashFlowSummary['net_this_month'] / 1000000, 1) }}M
                            </p>
                            <p class="text-xs mt-0.5" style="color: {{ $cashFlowSummary['is_profitable'] ? '#34C759' : '#FF3B30' }};">
                                {{ $cashFlowSummary['is_profitable'] ? 'Profit' : 'Loss' }}
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Visual Bar Chart --}}
                <div class="space-y-1.5">
                    <p class="text-xs font-medium mb-1" style="color: rgba(255,255,255,0.6);">Comparison Chart</p>
                    
                    {{-- Payments Bar --}}
                    <div>
                        <div class="flex items-center justify-between mb-0.5">
                            <span class="text-xs" style="color: rgba(255,255,255,0.7);">Pemasukan</span>
                            <span class="text-xs font-bold" style="color: rgba(142, 142, 147, 0.6);">Rp {{ number_format($cashFlowSummary['payments_this_month']) }}</span>
                        </div>
                        <div class="h-2.5 rounded-full overflow-hidden" style="background: rgba(52, 199, 89, 0.2);">
                            @php
                                $maxAmount = max($cashFlowSummary['payments_this_month'], $cashFlowSummary['expenses_this_month'], 1);
                                $paymentsWidth = ($cashFlowSummary['payments_this_month'] / $maxAmount) * 100;
                            @endphp
                            <div class="h-full rounded-full transition-all duration-500" style="width: {{ $paymentsWidth }}%; background: #34C759;"></div>
                        </div>
                    </div>

                    {{-- Expenses Bar --}}
                    <div>
                        <div class="flex items-center justify-between mb-0.5">
                            <span class="text-xs" style="color: rgba(255,255,255,0.7);">Pengeluaran</span>
                            <span class="text-xs font-bold" style="color: rgba(142, 142, 147, 0.6);">Rp {{ number_format($cashFlowSummary['expenses_this_month']) }}</span>
                        </div>
                        <div class="h-2.5 rounded-full overflow-hidden" style="background: rgba(255, 59, 48, 0.2);">
                            @php
                                $expensesWidth = ($cashFlowSummary['expenses_this_month'] / $maxAmount) * 100;
                            @endphp
                            <div class="h-full rounded-full transition-all duration-500" style="width: {{ $expensesWidth }}%; background: #FF3B30;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Card 5: Receivables Aging --}}
        <div class="card-elevated rounded-apple-lg overflow-hidden transition-all duration-300 hover:scale-[1.01] hover:shadow-2xl">
            <div class="p-4 border-b" style="border-color: rgba(255,255,255,0.1);">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-base font-semibold" style="color: #FFFFFF;"><i class="fas fa-receipt mr-2" style="color: rgba(142, 142, 147, 0.6);"></i>Receivables Aging</h3>
                        <p class="text-xs mt-1" style="color: rgba(255,255,255,0.5);">Piutang invoice & kasbon internal</p>
                    </div>
                    @if($receivablesAging['invoice_count'] > 0 || $receivablesAging['internal_count'] > 0)
                    <span class="px-3 py-1 rounded-full text-xs font-bold" style="background: rgba(10, 132, 255, 0.2); color: rgba(142, 142, 147, 0.6);">
                        {{ $receivablesAging['invoice_count'] + $receivablesAging['internal_count'] }} items
                    </span>
                    @endif
                </div>
            </div>
            <div class="p-3">
                {{-- Total Receivables --}}
                <div class="mb-3 p-3 rounded-xl" style="background: rgba(10, 132, 255, 0.1);">
                    <p class="text-xs mb-1" style="color: rgba(255,255,255,0.6);">Total Piutang Outstanding</p>
                    <div class="flex items-baseline">
                        <h2 class="text-2xl font-bold" style="color: rgba(142, 142, 147, 0.6);">{{ number_format($receivablesAging['total_receivables'] / 1000000, 1) }}</h2>
                        <span class="ml-2 text-base font-medium" style="color: rgba(255,255,255,0.6);">M</span>
                    </div>
                    {{-- Breakdown --}}
                    <div class="mt-1.5 pt-1.5 border-t" style="border-color: rgba(255,255,255,0.1);">
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-xs" style="color: rgba(255,255,255,0.5);">Invoice Receivables:</span>
                            <span class="text-xs font-semibold" style="color: rgba(142, 142, 147, 0.6);">Rp {{ number_format($receivablesAging['invoice_receivables']) }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-xs" style="color: rgba(255,255,255,0.5);">Kasbon Internal:</span>
                            <span class="text-xs font-semibold" style="color: rgba(142, 142, 147, 0.6);">Rp {{ number_format($receivablesAging['internal_receivables']) }}</span>
                        </div>
                    </div>
                </div>

                {{-- Internal Receivables List (if any) --}}
                @if($receivablesAging['internal_count'] > 0)
                <div class="mb-3">
                    <p class="text-xs font-medium mb-1.5" style="color: rgba(255,255,255,0.6);">Kasbon Internal</p>
                    <div class="space-y-1.5">
                        @foreach($receivablesAging['internal_list'] as $kasbon)
                        <div class="p-2 rounded-apple" style="background: rgba(255,255,255,0.03);">
                            <div class="flex items-center justify-between mb-0.5">
                                <span class="text-sm font-medium text-white">{{ $kasbon['from'] }}</span>
                                <span class="text-sm font-bold" style="color: rgba(142, 142, 147, 0.6);">Rp {{ number_format($kasbon['remaining']) }}</span>
                            </div>
                            <p class="text-xs" style="color: rgba(255,255,255,0.5);">{{ $kasbon['description'] }}</p>
                            <p class="text-xs mt-0.5" style="color: rgba(255,255,255,0.4);">{{ \Carbon\Carbon::parse($kasbon['date'])->format('d M Y') }}</p>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                {{-- Aging Buckets --}}
                <p class="text-xs font-medium mb-1.5" style="color: rgba(255,255,255,0.6);">Aging Analysis</p>
                <div class="space-y-1.5">
                    {{-- 0-30 days (Current) --}}
                    <div class="block p-2 rounded-apple" style="background: rgba(52, 199, 89, 0.08);">
                        <div class="flex items-center justify-between">
                            <div class="flex-1">
                                <div class="flex items-center gap-2">
                                    <div class="w-2 h-2 rounded-full" style="background: rgba(142, 142, 147, 0.6);"></div>
                                    <p class="text-sm font-medium text-white">0-30 hari</p>
                                </div>
                                <p class="text-xs mt-0.5" style="color: rgba(255,255,255,0.5);">Current</p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-bold" style="color: rgba(142, 142, 147, 0.6);">{{ number_format($receivablesAging['aging']['current'] / 1000000, 1) }}M</p>
                            </div>
                        </div>
                    </div>

                    {{-- 31-60 days --}}
                    @if($receivablesAging['aging']['31_60'] > 0)
                    <div class="block p-2.5 rounded-apple" style="background: rgba(255, 204, 0, 0.08);">
                        <div class="flex items-center justify-between">
                            <div class="flex-1">
                                <div class="flex items-center gap-2">
                                    <div class="w-2 h-2 rounded-full" style="background: rgba(142, 142, 147, 0.6);"></div>
                                    <p class="text-sm font-medium text-white">31-60 hari</p>
                                </div>
                                <p class="text-xs mt-0.5" style="color: rgba(255,255,255,0.5);">Follow up</p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-bold" style="color: rgba(142, 142, 147, 0.6);">{{ number_format($receivablesAging['aging']['31_60'] / 1000000, 1) }}M</p>
                            </div>
                        </div>
                    </div>
                    @endif

                    {{-- 61-90 days --}}
                    @if($receivablesAging['aging']['61_90'] > 0)
                    <div class="block p-2.5 rounded-apple" style="background: rgba(255, 149, 0, 0.08);">
                        <div class="flex items-center justify-between">
                            <div class="flex-1">
                                <div class="flex items-center gap-2">
                                    <div class="w-2 h-2 rounded-full" style="background: rgba(142, 142, 147, 0.6);"></div>
                                    <p class="text-sm font-medium text-white">61-90 hari</p>
                                </div>
                                <p class="text-xs mt-0.5" style="color: rgba(255,255,255,0.5);">Urgent</p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-bold" style="color: rgba(142, 142, 147, 0.6);">{{ number_format($receivablesAging['aging']['61_90'] / 1000000, 1) }}M</p>
                            </div>
                        </div>
                    </div>
                    @endif

                    {{-- 90+ days --}}
                    @if($receivablesAging['aging']['over_90'] > 0)
                    <div class="block p-2.5 rounded-apple" style="background: rgba(255, 59, 48, 0.08);">
                        <div class="flex items-center justify-between">
                            <div class="flex-1">
                                <div class="flex items-center gap-2">
                                    <div class="w-2 h-2 rounded-full" style="background: rgba(142, 142, 147, 0.6);"></div>
                                    <p class="text-sm font-medium text-white">90+ hari</p>
                                </div>
                                <p class="text-xs mt-0.5" style="color: rgba(255,255,255,0.5);">Critical</p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-bold" style="color: rgba(142, 142, 147, 0.6);">{{ number_format($receivablesAging['aging']['over_90'] / 1000000, 1) }}M</p>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>

                {{-- Empty State --}}
                @if($receivablesAging['total_receivables'] == 0)
                <div class="text-center py-8">
                    <div class="w-16 h-16 rounded-full mx-auto mb-3 flex items-center justify-center" style="background: rgba(52, 199, 89, 0.1);">
                        <i class="fas fa-check-circle text-2xl" style="color: rgba(142, 142, 147, 0.6);"></i>
                    </div>
                    <p class="text-sm font-medium text-white">No Outstanding!</p>
                    <p class="text-xs mt-1" style="color: rgba(255,255,255,0.5);">Semua piutang telah terbayar</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Card 6: Budget Status (Full Width) --}}
    <div class="card-elevated rounded-apple-lg overflow-hidden mb-6 transition-all duration-300 hover:scale-[1.01] hover:shadow-2xl">
        <div class="p-4 border-b" style="border-color: rgba(255,255,255,0.1);">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-base font-semibold" style="color: #FFFFFF;"><i class="fas fa-chart-line mr-2" style="color: rgba(142, 142, 147, 0.6);"></i>Budget Status</h3>
                    <p class="text-xs mt-1" style="color: rgba(255,255,255,0.5);">Top 5 proyek berdasarkan utilisasi budget</p>
                </div>
                <div class="text-right">
                    <p class="text-xs" style="color: rgba(255,255,255,0.5);">Overall Utilization</p>
                    <p class="text-xl font-bold text-white">{{ $budgetStatus['overall_utilization'] }}%</p>
                </div>
            </div>
        </div>
        <div class="p-4">
            @if($budgetStatus['top_projects']->count() > 0)
            <div class="space-y-3">
                @foreach($budgetStatus['top_projects'] as $project)
                <a href="{{ route('projects.show', $project) }}" class="block p-3 rounded-xl hover:bg-dark-elevated-2 transition-apple" style="background: {{ $project->status_color }}10;">
                    <div class="flex items-center justify-between mb-2">
                        <div class="flex-1 min-w-0 mr-4">
                            <p class="text-sm font-semibold text-white truncate">{{ $project->name }}</p>
                            <p class="text-xs mt-0.5" style="color: rgba(255,255,255,0.5);">
                                Budget: Rp {{ number_format($project->budget) }} • Spent: Rp {{ number_format($project->actual_cost) }}
                            </p>
                        </div>
                        <div class="text-right flex-shrink-0">
                            <p class="text-base font-bold" style="color: {{ $project->status_color }};">{{ $project->variance_percentage }}%</p>
                            @if($project->variance > 0)
                            <p class="text-xs" style="color: rgba(142, 142, 147, 0.6);">+Rp {{ number_format($project->variance / 1000000, 1) }}M</p>
                            @endif
                        </div>
                    </div>
                    {{-- Progress Bar --}}
                    <div class="h-2 rounded-full overflow-hidden" style="background: rgba(255,255,255,0.1);">
                        <div class="h-full rounded-full transition-all duration-500" style="width: {{ min($project->variance_percentage, 100) }}%; background: {{ $project->status_color }};"></div>
                    </div>
                </a>
                @endforeach
            </div>
            @else
            <div class="text-center py-8">
                <p class="text-sm" style="color: rgba(255,255,255,0.5);">Belum ada data budget tracking</p>
            </div>
            @endif
        </div>
    </div>

    {{-- PHASE 3: OPERATIONAL INSIGHTS --}}
    <div class="mt-6 mb-4">
        <h2 class="text-lg font-bold" style="color: #FFFFFF;" mb-1"><i class="fas fa-bolt mr-2" style="color: rgba(142, 142, 147, 0.6);"></i>Operational Insights</h2>
        <p class="text-sm" style="color: rgba(255,255,255,0.5);">Timeline mingguan, performa tim, dan aktivitas terbaru</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-6">
        
        {{-- Card 7: Next 30 Days Timeline --}}
        <div class="card-elevated rounded-apple-lg overflow-hidden transition-all duration-300 hover:scale-[1.01] hover:shadow-2xl">
            <div class="p-4 border-b" style="border-color: rgba(255,255,255,0.1);">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-base font-semibold" style="color: #FFFFFF;"><i class="fas fa-calendar-alt mr-2" style="color: rgba(142, 142, 147, 0.6);"></i>Next 30 Days</h3>
                        <p class="text-xs mt-1" style="color: rgba(255,255,255,0.5);">{{ $thisWeek['period_start'] }} - {{ $thisWeek['period_end'] }}</p>
                    </div>
                    <span class="px-3 py-1 rounded-full text-xs font-bold" style="background: rgba(10, 132, 255, 0.2); color: rgba(142, 142, 147, 0.6);">
                        {{ $thisWeek['total_items'] }} items
                    </span>
                </div>
            </div>
            <div class="p-4 max-h-96 overflow-y-auto">
                @if($thisWeek['total_items'] > 0)
                <div class="space-y-2">
                    {{-- Tasks --}}
                    @foreach($thisWeek['tasks'] as $task)
                    <a href="{{ route('projects.show', $task['project_id']) }}" class="block p-3 rounded-xl hover:bg-dark-elevated-2 transition-apple" style="background: rgba({{ $task['is_past'] ? '255, 59, 48' : ($task['is_today'] ? '255, 204, 0' : '52, 199, 89') }}, 0.08);">
                        <div class="flex items-start gap-2">
                            <div class="w-2 h-2 rounded-full mt-1.5 flex-shrink-0" style="background: {{ $task['priority_color'] }};"></div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold text-white truncate">{{ $task['title'] }}</p>
                                <p class="text-xs mt-0.5" style="color: rgba(255,255,255,0.5);">{{ $task['project'] }}</p>
                                <div class="flex items-center gap-2 mt-1">
                                    <span class="text-xs" style="color: {{ $task['priority_color'] }};">
                                        <i class="fas fa-clock mr-1"></i>
                                        {{ $task['deadline_formatted'] }}
                                    </span>
                                    @if($task['is_past'])
                                    <span class="text-xs px-2 py-0.5 rounded-full" style="background: rgba(255, 59, 48, 0.2); color: rgba(142, 142, 147, 0.6);">
                                        {{ $task['days_until'] }}d overdue
                                    </span>
                                    @elseif($task['is_today'])
                                    <span class="text-xs px-2 py-0.5 rounded-full" style="background: rgba(255, 204, 0, 0.2); color: rgba(142, 142, 147, 0.6);">
                                        Today!
                                    </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </a>
                    @endforeach

                    {{-- Projects --}}
                    @foreach($thisWeek['projects'] as $project)
                    <a href="{{ route('projects.show', $project['id']) }}" class="block p-3 rounded-xl hover:bg-dark-elevated-2 transition-apple" style="background: rgba(10, 132, 255, 0.08);">
                        <div class="flex items-start gap-2">
                            <div class="w-2 h-2 rounded-full mt-1.5 flex-shrink-0" style="background: {{ $project['status_color'] }};"></div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold text-white truncate">{{ $project['name'] }}</p>
                                <p class="text-xs mt-0.5" style="color: rgba(255,255,255,0.5);">Project Deadline</p>
                                <span class="text-xs mt-1 inline-block" style="color: rgba(142, 142, 147, 0.6);">
                                    <i class="fas fa-calendar mr-1"></i>
                                    {{ $project['deadline_formatted'] }}
                                </span>
                            </div>
                        </div>
                    </a>
                    @endforeach
                </div>
                @else
                <div class="text-center py-8">
                    <div class="w-16 h-16 rounded-full mx-auto mb-3 flex items-center justify-center" style="background: rgba(52, 199, 89, 0.1);">
                        <i class="fas fa-calendar-check text-2xl" style="color: rgba(142, 142, 147, 0.6);"></i>
                    </div>
                    <p class="text-sm font-medium text-white">All Clear!</p>
                    <p class="text-xs mt-1" style="color: rgba(255,255,255,0.5);">Tidak ada deadline minggu ini</p>
                </div>
                @endif
            </div>
        </div>

        {{-- Card 8: Project Status Distribution --}}
        <div class="card-elevated rounded-apple-lg overflow-hidden transition-all duration-300 hover:scale-[1.01] hover:shadow-2xl">
            <div class="p-4 border-b" style="border-color: rgba(255,255,255,0.1);">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-base font-semibold" style="color: #FFFFFF;"><i class="fas fa-chart-pie mr-2" style="color: rgba(142, 142, 147, 0.6);"></i>Project Status</h3>
                        <p class="text-xs mt-1" style="color: rgba(255,255,255,0.5);">Distribution by status</p>
                    </div>
                    <div class="text-right">
                        <p class="text-2xl font-bold" style="color: rgba(142, 142, 147, 0.6);">{{ $projectStatusDistribution['total_projects'] }}</p>
                        <p class="text-xs" style="color: rgba(255,255,255,0.5);">Projects</p>
                    </div>
                </div>
            </div>
            <div class="p-3 max-h-96 overflow-y-auto">
                @if($projectStatusDistribution['distribution']->count() > 0)
                <div class="space-y-2">
                    @foreach($projectStatusDistribution['distribution'] as $statusGroup)
                    <div class="p-2.5 rounded-apple" style="background: rgba(255,255,255,0.03);">
                        <div class="flex items-center justify-between mb-2">
                            <div class="flex items-center gap-2 flex-1 min-w-0">
                                <div class="w-2 h-2 rounded-full flex-shrink-0" style="background: rgba(255,255,255,0.3);"></div>
                                <p class="text-sm font-medium text-white truncate">{{ $statusGroup['status_name'] }}</p>
                            </div>
                            <span class="text-sm font-bold flex-shrink-0 ml-2" style="color: rgba(142, 142, 147, 0.6);">{{ $statusGroup['count'] }}</span>
                        </div>
                        @if($statusGroup['projects']->count() > 0)
                        <div class="ml-4 space-y-1">
                            @foreach($statusGroup['projects'] as $project)
                            <a href="{{ route('projects.show', $project->id) }}\" class="block text-xs hover:scale-[1.01] transition-all duration-200" style="color: rgba(255,255,255,0.5);">
                                • {{ $project->name }}
                            </a>
                            @endforeach
                        </div>
                        @endif
                    </div>
                    @endforeach
                </div>
                @else
                <div class="text-center py-8">
                    <p class="text-sm" style="color: rgba(255,255,255,0.5);">No project data</p>
                </div>
                @endif
            </div>
        </div>

        {{-- Card 9: Recent Activities --}}
        <div class="card-elevated rounded-apple-lg overflow-hidden transition-all duration-300 hover:scale-[1.01] hover:shadow-2xl">
            <div class="p-4 border-b" style="border-color: rgba(255,255,255,0.1);">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-base font-semibold" style="color: #FFFFFF;"><i class="fas fa-bell mr-2" style="color: rgba(142, 142, 147, 0.6);"></i>Recent Activities</h3>
                        <p class="text-xs mt-1" style="color: rgba(255,255,255,0.5);">Latest updates</p>
                    </div>
                    <span class="px-3 py-1 rounded-full text-xs font-bold" style="background: rgba(191, 90, 242, 0.2); color: rgba(142, 142, 147, 0.6);">
                        {{ $recentActivities['count'] }}
                    </span>
                </div>
            </div>
            <div class="p-3 max-h-96 overflow-y-auto">
                @if($recentActivities['count'] > 0)
                <div class="space-y-1">
                    @foreach($recentActivities['activities'] as $activity)
                    <a href="{{ $activity['link'] }}" class="block p-2 rounded-apple hover:bg-dark-elevated-2 hover:scale-[1.01] transition-all duration-200" style="background: rgba(255,255,255,0.02);">
                        <div class="flex items-start gap-2">
                            <div class="w-6 h-6 rounded-apple flex items-center justify-center flex-shrink-0" style="background: rgba(142, 142, 147, 0.2);">
                                <i class="fas fa-circle text-xs" style="color: rgba(142, 142, 147, 0.8);"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-xs font-medium text-white truncate">{{ $activity['title'] }}</p>
                                <p class="text-xs mt-0.5" style="color: rgba(255,255,255,0.4);">{{ $activity['description'] }}</p>
                                <p class="text-xs mt-0.5" style="color: rgba(142, 142, 147, 0.8);">
                                    {{ $activity['time_formatted'] }}
                                </p>
                            </div>
                        </div>
                    </a>
                    @endforeach
                </div>
                @else
                <div class="text-center py-8">
                    <p class="text-sm" style="color: rgba(255,255,255,0.5);">Belum ada aktivitas</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Compact Footer Info --}}
    <div class="mt-6 mb-4 text-center">
        <p class="text-xs" style="color: rgba(235, 235, 245, 0.3);">
            <i class="fas fa-clock mr-1"></i>
            Last updated: {{ now()->format('d M Y, H:i') }}
        </p>
    </div>
@endsection
