{{-- KPI Summary Cards --}}
@php
    $urgent   = $criticalAlerts['total_urgent'] ?? 0;
    $runway   = $cashFlowStatus['runway_months'] ?? 0;
    $pending  = $pendingApprovals['total_pending'] ?? 0;
    $upcoming = $thisWeek['total_items'] ?? 0;

    // Show "N/A" when there is no cash balance AND no burn-rate data (brand new account / no transactions)
    $hasFinancialData = ($cashFlowStatus['current_balance'] ?? 0) != 0
        || ($cashFlowStatus['monthly_burn_rate'] ?? 0) > 0;
    $runwayDisplay    = $hasFinancialData ? $runway.' bln' : 'N/A';
    $runwayColor      = ! $hasFinancialData
        ? 'var(--dark-text-secondary)'
        : ($runway < 2 ? 'var(--apple-red)' : ($runway < 6 ? 'var(--apple-orange)' : 'var(--apple-green)'));

    $kpis = [
        ['label'=>'Urgent',     'value'=>$urgent,         'sub'=>'perlu penanganan',  'color'=>$urgent > 0 ? 'var(--apple-red)'    : 'var(--apple-green)',           'bg'=>$urgent > 0 ? 'var(--apple-red)'    : 'var(--apple-green)',           'icon'=>'fa-exclamation-triangle'],
        ['label'=>'Runway Kas', 'value'=>$runwayDisplay,  'sub'=>'proyeksi arus kas', 'color'=>$runwayColor,                                                          'bg'=>$runwayColor,                                                          'icon'=>'fa-wallet'],
        ['label'=>'Pending',    'value'=>$pending,        'sub'=>'dokumen tertunda',  'color'=>$pending > 0 ? 'var(--apple-orange)' : 'var(--dark-text-secondary)', 'bg'=>$pending > 0 ? 'var(--apple-orange)' : 'transparent',                  'icon'=>'fa-clock'],
        ['label'=>'30 Hari',    'value'=>$upcoming,       'sub'=>'agenda mendatang',  'color'=>'var(--apple-blue)',                                                   'bg'=>'var(--apple-blue)',                                                   'icon'=>'fa-calendar-check'],
    ];
@endphp

<div style="display:grid;grid-template-columns:repeat(4,1fr);gap:12px">
    @foreach($kpis as $k)
    <div style="background:linear-gradient(135deg,color-mix(in srgb,{{ $k['bg'] }} 14%,var(--dark-bg-secondary)) 0%,var(--dark-bg-secondary) 100%);border:1px solid color-mix(in srgb,{{ $k['bg'] }} 28%,var(--dark-separator));border-radius:16px;padding:18px 20px;position:relative;overflow:hidden">
        <div style="position:absolute;top:12px;right:16px;font-size:1.1rem;opacity:.18;color:{{ $k['color'] }}">
            <i class="fas {{ $k['icon'] }}"></i>
        </div>
        <p style="font-size:0.6rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:{{ $k['color'] }};opacity:.85;margin:0">{{ $k['label'] }}</p>
        <p style="font-size:2rem;font-weight:800;color:{{ $k['color'] }};margin:5px 0 3px;line-height:1">{{ $k['value'] }}</p>
        <p style="font-size:0.68rem;color:var(--dark-text-secondary);margin:0">{{ $k['sub'] }}</p>
    </div>
    @endforeach
</div>
