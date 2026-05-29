{{-- Consultation Leads Tab --}}
@php
    $stats = $consultationLeadsStats ?? [
        'total' => 0, 'new' => 0, 'contacted' => 0, 'converted' => 0,
        'pending_review' => 0, 'high_value' => 0, 'this_week' => 0, 'this_month' => 0,
    ];
    $consultations = $consultations ?? collect();

    $statusMap = [
        'auto_estimated' => ['variant' => 'info',    'label' => 'Auto Estimated'],
        'reviewed'       => ['variant' => 'warning', 'label' => 'Reviewed'],
        'approved'       => ['variant' => 'success', 'label' => 'Approved'],
        'quoted'         => ['variant' => 'primary', 'label' => 'Quoted'],
        'rejected'       => ['variant' => 'danger',  'label' => 'Rejected'],
    ];
    $sizeMap = [
        'large'  => ['variant' => 'danger',  'label' => 'Large'],
        'medium' => ['variant' => 'warning', 'label' => 'Medium'],
        'small'  => ['variant' => 'success', 'label' => 'Small'],
        'micro'  => ['variant' => 'neutral', 'label' => 'Micro'],
    ];

    $cellRenderers = [
        'lead_id' => function ($row) {
            return '<span style="font-family:monospace;font-size:0.8rem;font-weight:700;color:var(--dark-text-primary)">#' . e($row->id) . '</span>';
        },
        'tanggal' => function ($row) {
            return '<span style="font-size:0.8rem;color:var(--dark-text-primary)">' . e($row->created_at->format('d M Y')) . '</span>'
                 . '<br><span style="font-size:0.7rem;color:var(--dark-text-secondary)">' . e($row->created_at->format('H:i')) . '</span>';
        },
        'perusahaan' => function ($row) use ($sizeMap) {
            $html = '<span style="font-size:0.85rem;font-weight:600;color:var(--dark-text-primary);display:block">' . e($row->company_name ?: $row->name) . '</span>';
            if ($row->kbli) {
                $html .= '<span style="font-size:0.68rem;color:var(--dark-text-secondary);display:block;max-width:160px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">' . e(optional($row->kbli)->description) . '</span>';
            }
            if ($row->business_size && isset($sizeMap[$row->business_size])) {
                $s = $sizeMap[$row->business_size];
                $html .= '<span style="display:inline-block;margin-top:3px">' . \Illuminate\Support\Facades\Blade::render('<x-ui.badge :variant="$v" size="sm">{{ $l }}</x-ui.badge>', ['v' => $s['variant'], 'l' => $s['label']]) . '</span>';
            }
            return $html;
        },
        'kontak' => function ($row) {
            return '<span style="font-size:0.85rem;font-weight:500;color:var(--dark-text-primary);display:block">' . e($row->name) . '</span>'
                 . '<span style="font-size:0.7rem;color:var(--dark-text-secondary);display:block">' . e($row->email) . '</span>'
                 . '<span style="font-size:0.7rem;color:var(--dark-text-secondary)">' . e($row->phone) . '</span>';
        },
        'estimasi' => function ($row) {
            $formatted = $row->auto_estimate['cost_summary']['formatted']['grand_total'] ?? null;
            $total = $row->auto_estimate['cost_summary']['grand_total'] ?? null;
            $confidence = number_format(($row->confidence_score ?? 0.5) * 100, 0);
            $html = $formatted
                ? '<span style="font-size:0.9rem;font-weight:700;color:var(--apple-green)">' . e($formatted) . '</span>'
                : '<span style="font-size:0.75rem;color:var(--dark-text-secondary)">—</span>';
            $html .= '<br><span style="font-size:0.68rem;color:var(--dark-text-secondary)">Confidence: ' . $confidence . '%</span>';
            if ($total && $total >= 10000000) {
                $html .= '<br>' . \Illuminate\Support\Facades\Blade::render('<x-ui.badge variant="danger" size="sm">High Value</x-ui.badge>');
            }
            return $html;
        },
        'status' => function ($row) use ($statusMap) {
            $s = $statusMap[$row->estimate_status] ?? ['variant' => 'neutral', 'label' => ucfirst(str_replace('_', ' ', $row->estimate_status))];
            $html = \Illuminate\Support\Facades\Blade::render('<x-ui.badge :variant="$v">{{ $l }}</x-ui.badge>', ['v' => $s['variant'], 'l' => $s['label']]);
            if ($row->contacted) {
                $html .= '<span style="display:block;margin-top:3px">' . \Illuminate\Support\Facades\Blade::render('<x-ui.badge variant="success" size="sm">Contacted</x-ui.badge>') . '</span>';
            }
            if ($row->converted_to_client) {
                $html .= '<span style="display:block;margin-top:3px">' . \Illuminate\Support\Facades\Blade::render('<x-ui.badge variant="primary" size="sm">Converted</x-ui.badge>') . '</span>';
            }
            return $html;
        },
        'actions' => function ($row) {
            $showUrl    = route('admin.consultation-leads.show', $row);
            $convertUrl = route('admin.consultation-leads.convert-to-client', $row);
            $html = '<div style="display:flex;align-items:center;gap:8px;justify-content:flex-end">';
            $html .= '<a href="' . e($showUrl) . '" style="display:inline-flex;align-items:center;gap:5px;font-size:0.75rem;font-weight:600;color:var(--apple-blue);text-decoration:none" onmouseover="this.style.opacity=.7" onmouseout="this.style.opacity=1"><i class="fas fa-eye"></i>Detail</a>';
            if (!$row->converted_to_client) {
                $html .= '<button onclick="showConvertModal(\'' . e($convertUrl) . '\')" style="display:inline-flex;align-items:center;gap:5px;font-size:0.75rem;font-weight:600;color:var(--apple-purple);background:none;border:none;cursor:pointer;padding:0" onmouseover="this.style.opacity=.7" onmouseout="this.style.opacity=1"><i class="fas fa-user-plus"></i>Konversi</button>';
            }
            $html .= '</div>';
            return $html;
        },
    ];
@endphp

{{-- Stats Strip --}}
<div style="display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin-bottom:16px">
    @php $statsData = [
        ['label'=>'Total',      'value'=>$stats['total'],     'sub'=>'semua leads',       'color'=>'var(--dark-text-primary)',   'bg'=>'transparent',           'icon'=>'fa-users'],
        ['label'=>'Baru',       'value'=>$stats['new'],       'sub'=>'belum ditindak',    'color'=>'var(--apple-blue)',          'bg'=>'var(--apple-blue)',      'icon'=>'fa-user-plus'],
        ['label'=>'Dihubungi',  'value'=>$stats['contacted'], 'sub'=>'sudah follow-up',   'color'=>'var(--apple-green)',         'bg'=>'var(--apple-green)',     'icon'=>'fa-phone'],
        ['label'=>'Konversi',   'value'=>$stats['converted'], 'sub'=>($stats['total']>0 ? round(($stats['converted']/$stats['total'])*100).'% rate' : '—'), 'color'=>'var(--apple-purple)', 'bg'=>'var(--apple-purple)', 'icon'=>'fa-trophy'],
    ] @endphp
    @foreach($statsData as $s)
    <div style="background:linear-gradient(135deg,color-mix(in srgb,{{ $s['bg'] }} 12%,var(--dark-bg-secondary)) 0%,var(--dark-bg-secondary) 100%);border:1px solid color-mix(in srgb,{{ $s['bg'] }} 25%,var(--dark-separator));border-radius:14px;padding:16px 18px;position:relative;overflow:hidden">
        <div style="position:absolute;top:10px;right:14px;font-size:1rem;opacity:.2;color:{{ $s['color'] }}"><i class="fas {{ $s['icon'] }}"></i></div>
        <p style="font-size:0.6rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:{{ $s['color'] }};opacity:.8;margin:0">{{ $s['label'] }}</p>
        <p style="font-size:2rem;font-weight:800;color:{{ $s['color'] }};margin:4px 0 2px;line-height:1">{{ $s['value'] }}</p>
        <p style="font-size:0.68rem;color:var(--dark-text-secondary);margin:0">{{ $s['sub'] }}</p>
    </div>
    @endforeach
</div>
<div style="display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin-bottom:20px">
    @php $statsData2 = [
        ['label'=>'Perlu Review', 'value'=>$stats['pending_review'], 'color'=>'var(--apple-orange)', 'bg'=>'var(--apple-orange)', 'icon'=>'fa-exclamation-circle'],
        ['label'=>'High Value',   'value'=>$stats['high_value'],     'color'=>'var(--apple-red)',    'bg'=>'var(--apple-red)',    'icon'=>'fa-fire'],
        ['label'=>'Minggu Ini',   'value'=>$stats['this_week'],      'color'=>'var(--dark-text-primary)', 'bg'=>'transparent',   'icon'=>'fa-calendar-week'],
        ['label'=>'Bulan Ini',    'value'=>$stats['this_month'],     'color'=>'var(--dark-text-primary)', 'bg'=>'transparent',   'icon'=>'fa-calendar'],
    ] @endphp
    @foreach($statsData2 as $s)
    <div style="background:linear-gradient(135deg,color-mix(in srgb,{{ $s['bg'] }} 8%,var(--dark-bg-secondary)) 0%,var(--dark-bg-secondary) 100%);border:1px solid color-mix(in srgb,{{ $s['bg'] }} 20%,var(--dark-separator));border-radius:12px;padding:12px 16px;position:relative;overflow:hidden">
        <div style="position:absolute;top:8px;right:12px;font-size:.85rem;opacity:.18;color:{{ $s['color'] }}"><i class="fas {{ $s['icon'] }}"></i></div>
        <p style="font-size:0.6rem;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:{{ $s['color'] }};opacity:.8;margin:0">{{ $s['label'] }}</p>
        <p style="font-size:1.5rem;font-weight:700;color:{{ $s['color'] }};margin:2px 0 0;line-height:1.1">{{ $s['value'] }}</p>
    </div>
    @endforeach
</div>

{{-- Smart Search & Filter Toolbar --}}
@php
    $clActiveFilters = collect([
        'search'    => request('search'),
        'status'    => request('status'),
        'contacted' => request('contacted'),
        'date_from' => request('date_from'),
    ])->filter()->count();
@endphp
<form method="GET" action="{{ route('admin.leads.index') }}" style="margin-bottom:16px">
    <input type="hidden" name="tab" value="consultation-leads">
    <div style="background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:14px;padding:12px 14px;display:flex;align-items:center;gap:10px;flex-wrap:wrap">

        {{-- Search --}}
        <div style="position:relative;flex:1;min-width:220px">
            <i class="fas fa-magnifying-glass" style="position:absolute;left:12px;top:50%;transform:translateY(-50%);font-size:0.72rem;color:var(--dark-text-tertiary);pointer-events:none;z-index:1"></i>
            <input type="text" name="search" id="cl-search" value="{{ request('search') }}"
                   placeholder="ID, email, nama, perusahaan…"
                   style="width:100%;padding:8px 36px 8px 34px;background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:10px;color:var(--dark-text-primary);font-size:0.82rem;line-height:1.4;outline:none;box-sizing:border-box;transition:border-color .18s"
                   onfocus="this.style.borderColor='var(--apple-yellow)'"
                   onblur="this.style.borderColor='var(--dark-separator)'">
            <button type="button" id="cl-clear-search"
                    style="display:{{ request('search') ? 'flex' : 'none' }};position:absolute;right:9px;top:50%;transform:translateY(-50%);width:18px;height:18px;align-items:center;justify-content:center;background:var(--dark-text-tertiary);border:none;border-radius:50%;cursor:pointer;padding:0;color:var(--dark-bg-primary);font-size:0.55rem"
                    onclick="document.getElementById('cl-search').value='';this.style.display='none';this.closest('form').submit()">
                <i class="fas fa-xmark"></i>
            </button>
        </div>

        <div style="width:1px;height:26px;background:var(--dark-separator);flex-shrink:0"></div>

        <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap">

            {{-- Status pill --}}
            <div style="position:relative">
                <select name="status" onchange="this.closest('form').submit()"
                        style="padding:6px 28px 6px 10px;background:{{ request('status') ? 'color-mix(in srgb,var(--apple-yellow) 18%,var(--dark-bg-tertiary))' : 'var(--dark-bg-tertiary)' }};border:1px solid {{ request('status') ? 'color-mix(in srgb,var(--apple-yellow) 45%,var(--dark-separator))' : 'var(--dark-separator)' }};border-radius:20px;color:{{ request('status') ? 'var(--apple-yellow)' : 'var(--dark-text-secondary)' }};font-size:0.75rem;font-weight:{{ request('status') ? '600' : '500' }};outline:none;appearance:none;-webkit-appearance:none;cursor:pointer;white-space:nowrap;transition:all .18s">
                    <option value="">Status</option>
                    <option value="auto_estimated" {{ request('status')=='auto_estimated' ? 'selected':'' }}>Auto Estimated</option>
                    <option value="reviewed"       {{ request('status')=='reviewed'       ? 'selected':'' }}>Reviewed</option>
                    <option value="approved"       {{ request('status')=='approved'       ? 'selected':'' }}>Approved</option>
                    <option value="quoted"         {{ request('status')=='quoted'         ? 'selected':'' }}>Quoted</option>
                    <option value="rejected"       {{ request('status')=='rejected'       ? 'selected':'' }}>Rejected</option>
                </select>
                <i class="fas fa-chevron-down" style="position:absolute;right:9px;top:50%;transform:translateY(-50%);font-size:0.5rem;color:{{ request('status') ? 'var(--apple-yellow)' : 'var(--dark-text-tertiary)' }};pointer-events:none"></i>
            </div>

            {{-- Kontak pill --}}
            <div style="position:relative">
                <select name="contacted" onchange="this.closest('form').submit()"
                        style="padding:6px 28px 6px 10px;background:{{ request('contacted') ? 'color-mix(in srgb,var(--apple-green) 18%,var(--dark-bg-tertiary))' : 'var(--dark-bg-tertiary)' }};border:1px solid {{ request('contacted') ? 'color-mix(in srgb,var(--apple-green) 45%,var(--dark-separator))' : 'var(--dark-separator)' }};border-radius:20px;color:{{ request('contacted') ? 'var(--apple-green)' : 'var(--dark-text-secondary)' }};font-size:0.75rem;font-weight:{{ request('contacted') ? '600' : '500' }};outline:none;appearance:none;-webkit-appearance:none;cursor:pointer;white-space:nowrap;transition:all .18s">
                    <option value="">Kontak</option>
                    <option value="yes" {{ request('contacted')=='yes' ? 'selected':'' }}>Dihubungi</option>
                    <option value="no"  {{ request('contacted')=='no'  ? 'selected':'' }}>Belum</option>
                </select>
                <i class="fas fa-chevron-down" style="position:absolute;right:9px;top:50%;transform:translateY(-50%);font-size:0.5rem;color:{{ request('contacted') ? 'var(--apple-green)' : 'var(--dark-text-tertiary)' }};pointer-events:none"></i>
            </div>

            {{-- Date from --}}
            <div style="position:relative">
                <input type="date" name="date_from" value="{{ request('date_from') }}"
                       onchange="this.closest('form').submit()"
                       style="padding:6px 10px;background:{{ request('date_from') ? 'color-mix(in srgb,var(--apple-teal) 18%,var(--dark-bg-tertiary))' : 'var(--dark-bg-tertiary)' }};border:1px solid {{ request('date_from') ? 'color-mix(in srgb,var(--apple-teal) 45%,var(--dark-separator))' : 'var(--dark-separator)' }};border-radius:20px;color:{{ request('date_from') ? 'var(--apple-teal)' : 'var(--dark-text-secondary)' }};font-size:0.75rem;font-weight:{{ request('date_from') ? '600' : '500' }};outline:none;cursor:pointer;white-space:nowrap;transition:all .18s">
            </div>

            @if($clActiveFilters > 0)
            <a href="{{ route('admin.leads.index', ['tab' => 'consultation-leads']) }}"
               style="display:inline-flex;align-items:center;gap:5px;padding:5px 11px;background:color-mix(in srgb,var(--apple-red) 14%,var(--dark-bg-tertiary));border:1px solid color-mix(in srgb,var(--apple-red) 30%,var(--dark-separator));border-radius:20px;font-size:0.72rem;font-weight:600;color:var(--apple-red);text-decoration:none;white-space:nowrap"
               onmouseover="this.style.opacity=.75" onmouseout="this.style.opacity=1">
                <i class="fas fa-xmark"></i>Reset
                <span style="display:inline-flex;align-items:center;justify-content:center;width:16px;height:16px;background:var(--apple-red);color:#fff;border-radius:50%;font-size:0.6rem;font-weight:700">{{ $clActiveFilters }}</span>
            </a>
            @endif
        </div>
    </div>
</form>

{{-- Data Table --}}
<div style="background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:16px;overflow:hidden;margin-bottom:16px">
    <div style="display:flex;align-items:center;justify-content:space-between;padding:16px 20px;border-bottom:1px solid var(--dark-separator)">
        <div>
            <p style="font-size:0.6rem;font-weight:700;letter-spacing:.12em;text-transform:uppercase;color:rgba(235,235,245,0.85);margin:0">Data</p>
            <h3 style="font-size:0.95rem;font-weight:700;color:var(--dark-text-primary);margin:3px 0 0">Daftar Consultation Leads</h3>
        </div>
        <span style="font-size:0.75rem;color:var(--dark-text-secondary)">
            @if($consultations instanceof \Illuminate\Pagination\LengthAwarePaginator)
                @if($consultations->total() === 0)
                    0 leads
                @else
                    {{ $consultations->firstItem() }}–{{ $consultations->lastItem() }} dari {{ $consultations->total() }}
                @endif
            @else
                {{ $consultations->count() }} entri
            @endif
        </span>
    </div>
    <x-ui.table
        :columns="[
            ['key' => 'lead_id',    'label' => 'Lead #'],
            ['key' => 'tanggal',    'label' => 'Tanggal'],
            ['key' => 'perusahaan', 'label' => 'Perusahaan'],
            ['key' => 'kontak',     'label' => 'Kontak'],
            ['key' => 'estimasi',   'label' => 'Estimasi'],
            ['key' => 'status',     'label' => 'Status'],
            ['key' => 'actions',    'label' => 'Aksi', 'align' => 'right'],
        ]"
        :rows="$consultations"
        :cellRenderers="$cellRenderers"
        :striped="true"
        :hoverable="true"
        variant="compact"
        empty-message="Tidak ada consultation leads. Coba ubah filter."
    />
    @if($consultations instanceof \Illuminate\Pagination\LengthAwarePaginator && $consultations->hasPages())
        <div style="padding:14px 20px;border-top:1px solid var(--dark-separator)">
            <x-ui.pagination :paginator="$consultations->appends(array_merge(request()->all(), ['tab'=>'consultation-leads']))" variant="full" :show-info="true" />
        </div>
    @endif
</div>
