{{-- Financial Intelligence Section --}}
@php
    $paymentsThisMonth = $cashFlowSummary['payments_this_month'] ?? 0;
    $expensesThisMonth = $cashFlowSummary['expenses_this_month'] ?? 0;
    $netThisMonth      = $cashFlowSummary['net_this_month'] ?? 0;
    $isProfitable      = $cashFlowSummary['is_profitable'] ?? false;
    $maxAmount         = max($paymentsThisMonth, $expensesThisMonth, 1);
    $paymentsWidth     = min(($paymentsThisMonth / $maxAmount) * 100, 100);
    $expensesWidth     = min(($expensesThisMonth / $maxAmount) * 100, 100);
    $aging             = $receivablesAging['aging'] ?? [];

    // Flag: no financial transactions recorded yet this month
    $noFinancialData = $paymentsThisMonth == 0 && $expensesThisMonth == 0
        && ($cashFlowSummary['payments_ytd'] ?? 0) == 0
        && ($cashFlowSummary['expenses_ytd'] ?? 0) == 0;
@endphp

<div style="background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:18px;overflow:hidden">

    {{-- Section Header --}}
    <div style="padding:16px 20px;border-bottom:1px solid var(--dark-separator)">
        <p style="font-size:0.6rem;font-weight:700;letter-spacing:.12em;text-transform:uppercase;color:var(--dark-text-secondary);margin:0">Laporan Keuangan</p>
        <h2 style="font-size:0.95rem;font-weight:700;color:var(--dark-text-primary);margin:3px 0 0;display:flex;align-items:center;gap:8px">
            <i class="fas fa-chart-line" style="font-size:0.8rem;color:var(--apple-blue)"></i>
            Tinjauan Keuangan
        </h2>
    </div>

    {{-- 3-col grid --}}
    <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:0">

        {{-- Col 1: Cash Flow This Month --}}
        <div style="padding:16px 20px;border-right:1px solid var(--dark-separator)">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:14px">
                <h3 style="font-size:0.85rem;font-weight:700;color:var(--dark-text-primary);margin:0">Arus Kas Bulan Ini</h3>
                @if($noFinancialData)
                <span style="padding:3px 10px;border-radius:20px;font-size:0.7rem;font-weight:600;background:color-mix(in srgb,var(--dark-text-secondary) 15%,transparent);color:var(--dark-text-secondary)">Belum ada data</span>
                @else
                <span style="padding:3px 10px;border-radius:20px;font-size:0.7rem;font-weight:600;background:color-mix(in srgb,var(--apple-blue) 15%,transparent);color:var(--apple-blue)">Aktif</span>
                @endif
            </div>

            @if($noFinancialData)
            {{-- Empty state: no transactions recorded yet --}}
            <div style="text-align:center;padding:32px 16px">
                <div style="width:44px;height:44px;border-radius:50%;background:color-mix(in srgb,var(--dark-text-secondary) 12%,transparent);display:flex;align-items:center;justify-content:center;margin:0 auto 12px">
                    <i class="fas fa-chart-bar" style="color:var(--dark-text-secondary);font-size:1.1rem"></i>
                </div>
                <p style="font-size:0.85rem;font-weight:600;color:var(--dark-text-primary);margin:0">Belum Ada Transaksi</p>
                <p style="font-size:0.72rem;color:var(--dark-text-secondary);margin:6px 0 0;line-height:1.4">Tambahkan pembayaran atau pengeluaran proyek untuk melihat laporan arus kas bulan ini.</p>
            </div>
            @else
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-bottom:12px">
                <div>
                    <p style="font-size:0.65rem;color:var(--dark-text-secondary);margin:0">Pemasukan</p>
                    <p style="font-size:1.1rem;font-weight:700;color:var(--dark-text-primary);margin:3px 0 0">{{ number_format($paymentsThisMonth/1000000,1) }}M</p>
                    @if(($cashFlowSummary['payments_growth'] ?? 0) != 0)
                    <p style="font-size:0.68rem;color:{{ ($cashFlowSummary['payments_growth'] ?? 0) > 0 ? 'var(--apple-green)' : 'var(--apple-red)' }};margin:2px 0 0">
                        <i class="fas fa-arrow-{{ ($cashFlowSummary['payments_growth'] ?? 0) > 0 ? 'up' : 'down' }}" style="margin-right:3px"></i>{{ abs($cashFlowSummary['payments_growth'] ?? 0) }}%
                    </p>
                    @endif
                </div>
                <div>
                    <p style="font-size:0.65rem;color:var(--dark-text-secondary);margin:0">Pengeluaran</p>
                    <p style="font-size:1.1rem;font-weight:700;color:var(--dark-text-primary);margin:3px 0 0">{{ number_format($expensesThisMonth/1000000,1) }}M</p>
                    @if(($cashFlowSummary['expenses_growth'] ?? 0) != 0)
                    <p style="font-size:0.68rem;color:{{ ($cashFlowSummary['expenses_growth'] ?? 0) > 0 ? 'var(--apple-red)' : 'var(--apple-green)' }};margin:2px 0 0">
                        <i class="fas fa-arrow-{{ ($cashFlowSummary['expenses_growth'] ?? 0) > 0 ? 'up' : 'down' }}" style="margin-right:3px"></i>{{ abs($cashFlowSummary['expenses_growth'] ?? 0) }}%
                    </p>
                    @endif
                </div>
            </div>

            {{-- Progress Bars --}}
            <div style="display:flex;flex-direction:column;gap:8px;margin-bottom:12px">
                <div>
                    <div style="display:flex;justify-content:space-between;font-size:0.68rem;color:var(--dark-text-secondary);margin-bottom:3px">
                        <span>Pemasukan</span><span>Rp {{ number_format($paymentsThisMonth) }}</span>
                    </div>
                    <div style="height:5px;border-radius:3px;overflow:hidden;background:color-mix(in srgb,var(--apple-green) 15%,var(--dark-bg-tertiary))">
                        <div style="height:100%;width:{{ $paymentsWidth }}%;background:var(--apple-green);border-radius:3px"></div>
                    </div>
                </div>
                <div>
                    <div style="display:flex;justify-content:space-between;font-size:0.68rem;color:var(--dark-text-secondary);margin-bottom:3px">
                        <span>Pengeluaran</span><span>Rp {{ number_format($expensesThisMonth) }}</span>
                    </div>
                    <div style="height:5px;border-radius:3px;overflow:hidden;background:color-mix(in srgb,var(--apple-red) 15%,var(--dark-bg-tertiary))">
                        <div style="height:100%;width:{{ $expensesWidth }}%;background:var(--apple-red);border-radius:3px"></div>
                    </div>
                </div>
            </div>

            <div style="padding:10px 14px;border-radius:10px;background:color-mix(in srgb,{{ $isProfitable ? 'var(--apple-green)' : 'var(--apple-red)' }} 10%,var(--dark-bg-tertiary));border:1px solid color-mix(in srgb,{{ $isProfitable ? 'var(--apple-green)' : 'var(--apple-red)' }} 25%,var(--dark-separator));margin-bottom:10px">
                <p style="font-size:0.85rem;font-weight:700;color:{{ $isProfitable ? 'var(--apple-green)' : 'var(--apple-red)' }};margin:0">
                    {{ $isProfitable ? 'Surplus ' : 'Defisit ' }}{{ number_format($netThisMonth/1000000,1) }}M bulan ini
                </p>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px">
                <div>
                    <p style="font-size:0.65rem;color:var(--dark-text-secondary);margin:0">Pemasukan YTD</p>
                    <p style="font-size:0.9rem;font-weight:600;color:var(--dark-text-primary);margin:2px 0 0">{{ number_format(($cashFlowSummary['payments_ytd'] ?? 0)/1000000,1) }}M</p>
                </div>
                <div>
                    <p style="font-size:0.65rem;color:var(--dark-text-secondary);margin:0">Pengeluaran YTD</p>
                    <p style="font-size:0.9rem;font-weight:600;color:var(--dark-text-primary);margin:2px 0 0">{{ number_format(($cashFlowSummary['expenses_ytd'] ?? 0)/1000000,1) }}M</p>
                </div>
            </div>
            @endif
        </div>

        {{-- Col 2: Receivables Aging --}}
        <div style="padding:16px 20px;border-right:1px solid var(--dark-separator)">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px">
                <h3 style="font-size:0.85rem;font-weight:700;color:var(--dark-text-primary);margin:0">Umur Piutang</h3>
                <span style="padding:3px 10px;border-radius:20px;font-size:0.7rem;font-weight:600;background:color-mix(in srgb,var(--apple-yellow) 15%,transparent);color:var(--apple-yellow)">{{ ($receivablesAging['invoice_count'] ?? 0) + ($receivablesAging['internal_count'] ?? 0) }} invoice</span>
            </div>
            <div style="padding:12px 14px;border-radius:12px;background:color-mix(in srgb,var(--apple-blue) 10%,var(--dark-bg-tertiary));border:1px solid color-mix(in srgb,var(--apple-blue) 20%,var(--dark-separator));margin-bottom:10px">
                <p style="font-size:0.65rem;color:var(--dark-text-secondary);margin:0">Total Piutang</p>
                <p style="font-size:1.3rem;font-weight:800;color:var(--apple-blue);margin:3px 0 2px">{{ number_format(($receivablesAging['total_receivables'] ?? 0)/1000000,1) }}M</p>
                <p style="font-size:0.68rem;color:var(--dark-text-secondary);margin:0">Faktur {{ number_format(($receivablesAging['invoice_receivables'] ?? 0)/1000000,1) }}M · Kasbon {{ number_format(($receivablesAging['internal_receivables'] ?? 0)/1000000,1) }}M</p>
            </div>
            <div style="display:flex;flex-direction:column;gap:5px;margin-bottom:10px">
                @foreach(['under_30' => ['0–30 hari', 'var(--dark-text-primary)'], 'days_30_60' => ['30–60 hari', 'var(--apple-yellow)'], 'days_60_90' => ['60–90 hari', 'var(--apple-orange)'], 'over_90' => ['90+ hari', 'var(--apple-red)']] as $key => [$label, $color])
                    @if(($aging[$key] ?? 0) > 0)
                    <div style="display:flex;align-items:center;justify-content:space-between;padding:7px 10px;border-radius:8px;background:var(--dark-bg-tertiary)">
                        <span style="font-size:0.75rem;color:var(--dark-text-secondary)">{{ $label }}</span>
                        <span style="font-size:0.82rem;font-weight:600;color:{{ $color }}">{{ number_format(($aging[$key] ?? 0)/1000000,1) }}M</span>
                    </div>
                    @endif
                @endforeach
            </div>
            @if(($receivablesAging['internal_count'] ?? 0) > 0)
            <div>
                <p style="font-size:0.6rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--dark-text-secondary);margin:0 0 6px">Kasbon Internal</p>
                <div style="display:flex;flex-direction:column;gap:4px;max-height:100px;overflow-y:auto">
                    @foreach(($receivablesAging['internal_list'] ?? []) as $kasbon)
                    <div style="display:flex;align-items:center;justify-content:space-between;padding:6px 10px;border-radius:8px;background:var(--dark-bg-tertiary)">
                        <span style="font-size:0.78rem;font-weight:500;color:var(--dark-text-primary)">{{ $kasbon['from'] ?? '-' }}</span>
                        <span style="font-size:0.78rem;font-weight:700;color:var(--dark-text-secondary)">{{ number_format(($kasbon['remaining'] ?? 0)/1000000,1) }}M</span>
                    </div>
                    @endforeach
                </div>
            </div>
            @else
            <p style="font-size:0.75rem;color:var(--apple-green);text-align:center;margin:4px 0 0"><i class="fas fa-check-circle" style="margin-right:4px"></i>Tidak ada kasbon belum lunas</p>
            @endif
        </div>

        {{-- Col 3: Budget Utilization --}}
        <div style="padding:16px 20px">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px">
                <h3 style="font-size:0.85rem;font-weight:700;color:var(--dark-text-primary);margin:0">Pemanfaatan Anggaran</h3>
                @php
                    $util = $budgetStatus['overall_utilization'] ?? 0;
                    $utilColor = $util > 100 ? 'var(--apple-red)' : ($util > 80 ? 'var(--apple-orange)' : 'var(--apple-green)');
                @endphp
                <span style="font-size:1.1rem;font-weight:800;color:{{ $utilColor }}">{{ $util }}%</span>
            </div>
            @if(collect($budgetStatus['top_projects'] ?? [])->count() > 0)
            <div style="display:flex;flex-direction:column;gap:8px">
                @foreach(($budgetStatus['top_projects'] ?? []) as $project)
                @php $pColor = $project->status_color ?? 'var(--apple-red)'; @endphp
                <a href="{{ route('projects.show', $project) }}" style="display:block;padding:10px 12px;border-radius:10px;background:color-mix(in srgb,{{ $pColor }} 8%,var(--dark-bg-tertiary));border:1px solid color-mix(in srgb,{{ $pColor }} 20%,var(--dark-separator));text-decoration:none" onmouseover="this.style.opacity=.85" onmouseout="this.style.opacity=1">
                    <div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:6px">
                        <div style="flex:1;min-width:0;margin-right:8px">
                            <p style="font-size:0.82rem;font-weight:600;color:var(--dark-text-primary);margin:0;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">{{ $project->name ?? '-' }}</p>
                            <p style="font-size:0.68rem;color:var(--dark-text-secondary);margin:2px 0 0">{{ number_format(($project->budget ?? 0)/1000000,1) }}M → {{ number_format(($project->actual_cost ?? 0)/1000000,1) }}M</p>
                        </div>
                        <span style="font-size:0.85rem;font-weight:700;color:{{ $pColor }};flex-shrink:0">{{ $project->variance_percentage ?? 0 }}%</span>
                    </div>
                    <div style="height:4px;border-radius:2px;overflow:hidden;background:color-mix(in srgb,{{ $pColor }} 15%,var(--dark-bg-tertiary))">
                        <div style="height:100%;width:{{ min($project->variance_percentage ?? 0, 100) }}%;background:{{ $pColor }};border-radius:2px"></div>
                    </div>
                </a>
                @endforeach
            </div>
            @else
            <div style="text-align:center;padding:32px 0">
                <div style="width:40px;height:40px;border-radius:50%;background:color-mix(in srgb,var(--apple-yellow) 15%,transparent);display:flex;align-items:center;justify-content:center;margin:0 auto 10px">
                    <i class="fas fa-chart-pie" style="color:var(--apple-yellow);font-size:1rem"></i>
                </div>
                <p style="font-size:0.85rem;font-weight:600;color:var(--dark-text-primary);margin:0">Belum Ada Anggaran</p>
                <p style="font-size:0.72rem;color:var(--dark-text-secondary);margin:4px 0 0">Buat anggaran proyek untuk memantau</p>
            </div>
            @endif
        </div>

    </div>
</div>
