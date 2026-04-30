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
                    <span class="admin-badge bg-apple-blue/15 text-apple-blue/90">Aktif</span>
                </div>
                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <p class="admin-small text-dark-text-tertiary">Pemasukan</p>
                        {{-- FIX (BUG-07): Null coalescing --}}
                        <p class="admin-stat text-white">{{ number_format(($cashFlowSummary['payments_this_month'] ?? 0) / 1000000, 1) }}M</p>
                        @if(($cashFlowSummary['payments_growth'] ?? 0) != 0)
                        <p class="admin-small" style="color: {{ ($cashFlowSummary['payments_growth'] ?? 0) > 0 ? '#34C759' : '#FF3B30' }};">
                            <i class="fas fa-arrow-{{ ($cashFlowSummary['payments_growth'] ?? 0) > 0 ? 'up' : 'down' }} mr-1"></i>{{ abs($cashFlowSummary['payments_growth'] ?? 0) }}%
                        </p>
                        @endif
                    </div>
                    <div>
                        <p class="admin-small text-dark-text-tertiary">Pengeluaran</p>
                        {{-- FIX (BUG-07): Null coalescing --}}
                        <p class="admin-stat text-white">{{ number_format(($cashFlowSummary['expenses_this_month'] ?? 0) / 1000000, 1) }}M</p>
                        @if(($cashFlowSummary['expenses_growth'] ?? 0) != 0)
                        <p class="admin-small" style="color: {{ ($cashFlowSummary['expenses_growth'] ?? 0) > 0 ? '#FF3B30' : '#34C759' }};">
                            <i class="fas fa-arrow-{{ ($cashFlowSummary['expenses_growth'] ?? 0) > 0 ? 'up' : 'down' }} mr-1"></i>{{ abs($cashFlowSummary['expenses_growth'] ?? 0) }}%
                        </p>
                        @endif
                    </div>
                </div>
                {{-- FIX (BUG-07): Null coalescing --}}
                @php
                    $paymentsThisMonth = $cashFlowSummary['payments_this_month'] ?? 0;
                    $expensesThisMonth = $cashFlowSummary['expenses_this_month'] ?? 0;
                    $maxAmount = max($paymentsThisMonth, $expensesThisMonth, 1);
                    $paymentsWidth = ($paymentsThisMonth / $maxAmount) * 100;
                    $expensesWidth = ($expensesThisMonth / $maxAmount) * 100;
                @endphp
                <div class="space-y-2">
                    <div>
                        <div class="flex items-center justify-between admin-small text-dark-text-tertiary">
                            <span>Pemasukan</span>
                            <span>Rp {{ number_format($paymentsThisMonth) }}</span>
                        </div>
                        <div class="h-1.5 rounded-full overflow-hidden bg-apple-green/15">
                            <div class="h-full" style="width: {{ $paymentsWidth }}%; background: #34C759;"></div>
                        </div>
                    </div>
                    <div>
                        <div class="flex items-center justify-between admin-small text-dark-text-tertiary">
                            <span>Pengeluaran</span>
                            <span>Rp {{ number_format($expensesThisMonth) }}</span>
                        </div>
                        <div class="h-1.5 rounded-full overflow-hidden bg-apple-red/15">
                            <div class="h-full" style="width: {{ $expensesWidth }}%; background: #FF3B30;"></div>
                        </div>
                    </div>
                </div>
                {{-- FIX (BUG-07): Null coalescing --}}
                @php $isProfitable = $cashFlowSummary['is_profitable'] ?? false; @endphp
                <div class="admin-body font-semibold" style="color: {{ $isProfitable ? '#34C759' : '#FF3B30' }};">
                    {{ $isProfitable ? 'Surplus ' : 'Defisit ' }}{{ number_format(($cashFlowSummary['net_this_month'] ?? 0) / 1000000, 1) }}M bulan ini
                </div>
                <div class="grid grid-cols-2 gap-2 admin-small text-dark-text-tertiary">
                    <div>
                        <p>Pemasukan YTD</p>
                        <p class="admin-body font-semibold text-white">{{ number_format(($cashFlowSummary['payments_ytd'] ?? 0) / 1000000, 1) }}M</p>
                    </div>
                    <div>
                        <p>Pengeluaran YTD</p>
                        <p class="admin-body font-semibold text-white">{{ number_format(($cashFlowSummary['expenses_ytd'] ?? 0) / 1000000, 1) }}M</p>
                    </div>
                </div>
            </div>

            {{-- Receivables --}}
            <div class="card-elevated rounded-apple p-3 space-y-2">
                <div class="flex items-center justify-between">
                    <h3 class="admin-section text-white">Umur Piutang</h3>
                    {{-- FIX (BUG-07): Null coalescing --}}
                    <span class="admin-badge bg-apple-yellow/15 text-apple-yellow/90">
                        {{ ($receivablesAging['invoice_count'] ?? 0) + ($receivablesAging['internal_count'] ?? 0) }}
                    </span>
                </div>
                <div class="rounded-apple p-2 bg-apple-blue/12">
                    <p class="admin-small text-dark-text-tertiary">Total Piutang</p>
                    <p class="admin-stat text-apple-blue">{{ number_format(($receivablesAging['total_receivables'] ?? 0) / 1000000, 1) }}M</p>
                    <p class="admin-small text-dark-text-tertiary">Faktur {{ number_format(($receivablesAging['invoice_receivables'] ?? 0)/1000000,1) }}M • Kasbon {{ number_format(($receivablesAging['internal_receivables'] ?? 0)/1000000,1) }}M</p>
                </div>
                {{-- FIX (BUG-07): Null coalescing for aging array --}}
                @php $aging = $receivablesAging['aging'] ?? []; @endphp
                <div class="space-y-1">
                    @foreach(['under_30' => '0-30 hari', 'days_30_60' => '30-60 hari', 'days_60_90' => '60-90 hari', 'over_90' => '90+ hari'] as $key => $label)
                        @if(($aging[$key] ?? 0) > 0)
                        <div class="p-1.5 rounded-apple flex items-center justify-between admin-small bg-white/4">
                            <span class="text-dark-text-secondary">{{ $label }}</span>
                            <span class="font-semibold" style="color: {{ $key === 'over_90' ? '#FF3B30' : ($key === 'days_60_90' ? '#FF9500' : '#FFFFFF') }};">
                                {{ number_format(($aging[$key] ?? 0) / 1000000, 1) }}M
                            </span>
                        </div>
                        @endif
                    @endforeach
                </div>
                @if(($receivablesAging['internal_count'] ?? 0) > 0)
                <div class="space-y-1 max-h-24 overflow-y-auto">
                    <p class="admin-small uppercase tracking-widest text-dark-text-tertiary">Kasbon Internal</p>
                    @foreach(($receivablesAging['internal_list'] ?? []) as $kasbon)
                    <div class="p-1.5 rounded-apple bg-white/3">
                        <div class="flex items-center justify-between admin-small">
                            <span class="font-medium text-white">{{ $kasbon['from'] ?? '-' }}</span>
                            <span class="font-bold text-dark-text-secondary">{{ number_format(($kasbon['remaining'] ?? 0)/1000000,1) }}M</span>
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
                        {{-- FIX (BUG-07): Null coalescing --}}
                        <p class="admin-stat text-white">{{ $budgetStatus['overall_utilization'] ?? 0 }}%</p>
                    </div>
                </div>
                {{-- FIX (BUG-07): Null coalescing for collection check --}}
                @if(collect($budgetStatus['top_projects'] ?? [])->count() > 0)
                <div class="space-y-2">
                    @foreach(($budgetStatus['top_projects'] ?? []) as $project)
                    <a href="{{ route('projects.show', $project) }}" class="block p-2 rounded-apple hover:bg-dark-elevated-2 transition-apple" style="background: {{ ($project->status_color ?? '#FF3B30') }}15;">
                        <div class="flex items-center justify-between mb-1">
                            <div class="flex-1 min-w-0 mr-2">
                                <p class="admin-body font-medium text-white truncate">{{ $project->name ?? '-' }}</p>
                                <p class="admin-small text-dark-text-tertiary">{{ number_format(($project->budget ?? 0)/1000000,1) }}M → {{ number_format(($project->actual_cost ?? 0)/1000000,1) }}M</p>
                            </div>
                            <span class="admin-body font-bold" style="color: {{ $project->status_color ?? '#FF3B30' }};">{{ $project->variance_percentage ?? 0 }}%</span>
                        </div>
                        <div class="h-1.5 rounded-full overflow-hidden bg-white/10">
                            <div class="h-full" style="width: {{ min($project->variance_percentage ?? 0, 100) }}%; background: {{ $project->status_color ?? '#FF3B30' }};"></div>
                        </div>
                    </a>
                    @endforeach
                </div>
                @else
                <div class="text-center py-4">
                    <div class="admin-stat-icon mx-auto mb-2 rounded-full flex items-center justify-center bg-apple-yellow/12">
                        <i class="fas fa-chart-pie text-apple-yellow" style="font-size: 0.7rem;"></i>
                    </div>
                    <p class="admin-body font-medium text-white">Belum Ada Anggaran</p>
                    <p class="admin-small text-dark-text-tertiary">Buat anggaran proyek untuk memantau</p>
                </div>
                @endif
            </div>
        </div>
    </section>

