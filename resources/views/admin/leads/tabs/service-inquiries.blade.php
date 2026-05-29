{{-- Service Inquiries Tab --}}
@php
    $stats = $serviceInquiriesStats ?? [
        'total' => 0, 'new' => 0, 'analyzed' => 0, 'contacted' => 0,
        'converted' => 0, 'high_priority' => 0, 'this_week' => 0, 'this_month' => 0,
    ];
    $inquiries = $inquiries ?? collect();

    $statusMap = [
        'new'        => ['color' => 'var(--apple-blue)',   'label' => 'Baru'],
        'processing' => ['color' => 'var(--apple-teal)',   'label' => 'Diproses'],
        'analyzed'   => ['color' => 'var(--apple-yellow)', 'label' => 'Dianalisis'],
        'contacted'  => ['color' => 'var(--apple-green)',  'label' => 'Dihubungi'],
        'qualified'  => ['color' => 'var(--apple-purple)', 'label' => 'Qualified'],
        'converted'  => ['color' => 'var(--apple-green)',  'label' => 'Konversi'],
        'registered' => ['color' => 'var(--dark-text-secondary)', 'label' => 'Terdaftar'],
        'lost'       => ['color' => 'var(--apple-red)',    'label' => 'Lost'],
    ];
    $priorityColors = ['high' => 'var(--apple-red)', 'medium' => 'var(--apple-orange)', 'low' => 'var(--dark-text-secondary)'];

    $cellRenderers = [
        'inquiry_number' => function ($row) {
            return '<span style="font-family:monospace;font-size:0.8rem;font-weight:700;color:var(--dark-text-primary);letter-spacing:.02em">' . e($row->inquiry_number) . '</span>';
        },
        'tanggal' => function ($row) {
            return '<span style="font-size:0.8rem;color:var(--dark-text-primary)">' . e($row->created_at->format('d M Y')) . '</span>'
                 . '<br><span style="font-size:0.7rem;color:var(--dark-text-secondary)">' . e($row->created_at->format('H:i')) . '</span>';
        },
        'perusahaan' => function ($row) {
            return '<span style="font-size:0.85rem;font-weight:600;color:var(--dark-text-primary);display:block">' . e($row->company_name) . '</span>'
                 . '<span style="font-size:0.7rem;color:var(--dark-text-secondary)">' . e($row->company_type ?? '-') . '</span>';
        },
        'kontak' => function ($row) {
            return '<span style="font-size:0.8rem;font-weight:500;color:var(--dark-text-primary);display:block">' . e($row->contact_person) . '</span>'
                 . '<span style="font-size:0.7rem;color:var(--dark-text-secondary);display:block">' . e($row->email) . '</span>'
                 . '<span style="font-size:0.7rem;color:var(--dark-text-secondary)">' . e($row->phone ?? '-') . '</span>';
        },
        'status' => function ($row) use ($statusMap) {
            $s = $statusMap[$row->status] ?? ['color' => 'var(--dark-text-secondary)', 'label' => ucfirst($row->status)];
            $variantMap = ['var(--apple-blue)'=>'info','var(--apple-teal)'=>'info','var(--apple-yellow)'=>'warning','var(--apple-green)'=>'success','var(--apple-purple)'=>'primary','var(--apple-red)'=>'danger','var(--dark-text-secondary)'=>'neutral'];
            $v = $variantMap[$s['color']] ?? 'neutral';
            return \Illuminate\Support\Facades\Blade::render('<x-ui.badge :variant="$v">{{ $l }}</x-ui.badge>', ['v' => $v, 'l' => $s['label']]);
        },
        'priority' => function ($row) use ($priorityColors) {
            if (!$row->priority) return '<span style="color:var(--dark-text-secondary);font-size:0.75rem">—</span>';
            $c = $priorityColors[$row->priority] ?? 'var(--dark-text-secondary)';
            $labels = ['high' => 'High', 'medium' => 'Medium', 'low' => 'Low'];
            $v = ['high' => 'danger', 'medium' => 'warning', 'low' => 'neutral'][$row->priority] ?? 'neutral';
            return \Illuminate\Support\Facades\Blade::render('<x-ui.badge :variant="$v">{{ $l }}</x-ui.badge>', ['v' => $v, 'l' => $labels[$row->priority] ?? ucfirst($row->priority)]);
        },
        'est_value' => function ($row) {
            if ($row->estimated_value) {
                $val = (float) $row->estimated_value;
                if ($val >= 1_000_000_000) {
                    return '<span style="font-size:0.85rem;font-weight:700;color:var(--apple-green)">Rp ' . e(number_format($val / 1_000_000_000, 1)) . ' M</span>'
                         . '<span style="display:block;font-size:0.65rem;color:var(--dark-text-tertiary)">Miliar</span>';
                }
                return '<span style="font-size:0.85rem;font-weight:700;color:var(--apple-green)">Rp ' . e(number_format($val / 1_000_000, 0)) . ' Jt</span>'
                     . '<span style="display:block;font-size:0.65rem;color:var(--dark-text-tertiary)">Juta</span>';
            }
            return '<span style="font-size:0.75rem;color:var(--dark-text-secondary)">—</span>';
        },
        'actions' => function ($row) {
            $url = route('admin.service-inquiries.show', $row);
            return '<a href="' . e($url) . '" style="display:inline-flex;align-items:center;gap:5px;font-size:0.75rem;font-weight:600;color:var(--apple-blue);text-decoration:none;transition:opacity .15s" onmouseover="this.style.opacity=.7" onmouseout="this.style.opacity=1"><i class="fas fa-eye"></i>Detail</a>';
        },
    ];
@endphp

{{-- Stats Strip --}}
<div style="display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin-bottom:16px">
    @php $statsData = [
        ['label'=>'Total',         'value'=>$stats['total'],         'sub'=>'semua inquiry',     'color'=>'var(--dark-text-primary)',   'bg'=>'transparent',             'icon'=>'fa-inbox'],
        ['label'=>'Baru',          'value'=>$stats['new'],           'sub'=>'perlu ditindak',    'color'=>'var(--apple-blue)',          'bg'=>'var(--apple-blue)',        'icon'=>'fa-star'],
        ['label'=>'Dihubungi',     'value'=>$stats['contacted'],     'sub'=>'sudah follow-up',   'color'=>'var(--apple-green)',         'bg'=>'var(--apple-green)',       'icon'=>'fa-phone'],
        ['label'=>'Konversi',      'value'=>$stats['converted'],     'sub'=>($stats['total']>0 ? round(($stats['converted']/$stats['total'])*100).'% rate' : '—'), 'color'=>'var(--apple-purple)', 'bg'=>'var(--apple-purple)', 'icon'=>'fa-trophy'],
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
        ['label'=>'Dianalisis',    'value'=>$stats['analyzed'],      'color'=>'var(--apple-teal)',  'bg'=>'var(--apple-teal)',   'icon'=>'fa-search'],
        ['label'=>'High Priority', 'value'=>$stats['high_priority'], 'color'=>'var(--apple-red)',   'bg'=>'var(--apple-red)',    'icon'=>'fa-fire'],
        ['label'=>'Minggu Ini',    'value'=>$stats['this_week'],     'color'=>'var(--dark-text-primary)', 'bg'=>'transparent',  'icon'=>'fa-calendar-week'],
        ['label'=>'Bulan Ini',     'value'=>$stats['this_month'],    'color'=>'var(--dark-text-primary)', 'bg'=>'transparent',  'icon'=>'fa-calendar'],
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
    $siActiveFilters = collect([
        'search'    => request('search'),
        'status'    => request('status'),
        'priority'  => request('priority'),
        'date_from' => request('date_from'),
    ])->filter()->count();
@endphp
<form method="GET" action="{{ route('admin.leads.index') }}" style="margin-bottom:16px">
    <input type="hidden" name="tab" value="service-inquiries">
    <div style="background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:14px;padding:12px 14px;display:flex;align-items:center;gap:10px;flex-wrap:wrap">

        {{-- Search --}}
        <div style="position:relative;flex:1;min-width:220px">
            <i class="fas fa-magnifying-glass" style="position:absolute;left:12px;top:50%;transform:translateY(-50%);font-size:0.72rem;color:var(--dark-text-tertiary);pointer-events:none;z-index:1"></i>
            <input type="text" name="search" id="si-search" value="{{ request('search') }}"
                   placeholder="Nomor inquiry, email, perusahaan…"
                   style="width:100%;padding:8px 36px 8px 34px;background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:10px;color:var(--dark-text-primary);font-size:0.82rem;line-height:1.4;outline:none;box-sizing:border-box;transition:border-color .18s"
                   onfocus="this.style.borderColor='var(--apple-blue)'"
                   onblur="this.style.borderColor='var(--dark-separator)'">
            <button type="button" id="si-clear-search"
                    style="display:{{ request('search') ? 'flex' : 'none' }};position:absolute;right:9px;top:50%;transform:translateY(-50%);width:18px;height:18px;align-items:center;justify-content:center;background:var(--dark-text-tertiary);border:none;border-radius:50%;cursor:pointer;padding:0;color:var(--dark-bg-primary);font-size:0.55rem"
                    onclick="document.getElementById('si-search').value='';this.style.display='none';this.closest('form').submit()">
                <i class="fas fa-xmark"></i>
            </button>
        </div>

        <div style="width:1px;height:26px;background:var(--dark-separator);flex-shrink:0"></div>

        <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap">

            {{-- Status pill --}}
            <div style="position:relative">
                <select name="status" onchange="this.closest('form').submit()"
                        style="padding:6px 28px 6px 10px;background:{{ request('status') ? 'color-mix(in srgb,var(--apple-blue) 18%,var(--dark-bg-tertiary))' : 'var(--dark-bg-tertiary)' }};border:1px solid {{ request('status') ? 'color-mix(in srgb,var(--apple-blue) 45%,var(--dark-separator))' : 'var(--dark-separator)' }};border-radius:20px;color:{{ request('status') ? 'var(--apple-blue)' : 'var(--dark-text-secondary)' }};font-size:0.75rem;font-weight:{{ request('status') ? '600' : '500' }};outline:none;appearance:none;-webkit-appearance:none;cursor:pointer;white-space:nowrap;transition:all .18s">
                    <option value="">Status</option>
                    <option value="new"        {{ request('status')=='new'        ? 'selected':'' }}>Baru</option>
                    <option value="processing" {{ request('status')=='processing' ? 'selected':'' }}>Diproses</option>
                    <option value="analyzed"   {{ request('status')=='analyzed'   ? 'selected':'' }}>Dianalisis</option>
                    <option value="contacted"  {{ request('status')=='contacted'  ? 'selected':'' }}>Dihubungi</option>
                    <option value="qualified"  {{ request('status')=='qualified'  ? 'selected':'' }}>Qualified</option>
                    <option value="converted"  {{ request('status')=='converted'  ? 'selected':'' }}>Konversi</option>
                    <option value="lost"       {{ request('status')=='lost'       ? 'selected':'' }}>Lost</option>
                </select>
                <i class="fas fa-chevron-down" style="position:absolute;right:9px;top:50%;transform:translateY(-50%);font-size:0.5rem;color:{{ request('status') ? 'var(--apple-blue)' : 'var(--dark-text-tertiary)' }};pointer-events:none"></i>
            </div>

            {{-- Priority pill --}}
            <div style="position:relative">
                <select name="priority" onchange="this.closest('form').submit()"
                        style="padding:6px 28px 6px 10px;background:{{ request('priority') ? 'color-mix(in srgb,var(--apple-red) 18%,var(--dark-bg-tertiary))' : 'var(--dark-bg-tertiary)' }};border:1px solid {{ request('priority') ? 'color-mix(in srgb,var(--apple-red) 45%,var(--dark-separator))' : 'var(--dark-separator)' }};border-radius:20px;color:{{ request('priority') ? 'var(--apple-red)' : 'var(--dark-text-secondary)' }};font-size:0.75rem;font-weight:{{ request('priority') ? '600' : '500' }};outline:none;appearance:none;-webkit-appearance:none;cursor:pointer;white-space:nowrap;transition:all .18s">
                    <option value="">Prioritas</option>
                    <option value="high"   {{ request('priority')=='high'   ? 'selected':'' }}>High</option>
                    <option value="medium" {{ request('priority')=='medium' ? 'selected':'' }}>Medium</option>
                    <option value="low"    {{ request('priority')=='low'    ? 'selected':'' }}>Low</option>
                </select>
                <i class="fas fa-chevron-down" style="position:absolute;right:9px;top:50%;transform:translateY(-50%);font-size:0.5rem;color:{{ request('priority') ? 'var(--apple-red)' : 'var(--dark-text-tertiary)' }};pointer-events:none"></i>
            </div>

            {{-- Date from --}}
            <div style="position:relative">
                <input type="date" name="date_from" value="{{ request('date_from') }}"
                       onchange="this.closest('form').submit()"
                       style="padding:6px 10px;background:{{ request('date_from') ? 'color-mix(in srgb,var(--apple-teal) 18%,var(--dark-bg-tertiary))' : 'var(--dark-bg-tertiary)' }};border:1px solid {{ request('date_from') ? 'color-mix(in srgb,var(--apple-teal) 45%,var(--dark-separator))' : 'var(--dark-separator)' }};border-radius:20px;color:{{ request('date_from') ? 'var(--apple-teal)' : 'var(--dark-text-secondary)' }};font-size:0.75rem;font-weight:{{ request('date_from') ? '600' : '500' }};outline:none;cursor:pointer;white-space:nowrap;transition:all .18s">
            </div>

            @if($siActiveFilters > 0)
            <a href="{{ route('admin.leads.index', ['tab' => 'service-inquiries']) }}"
               style="display:inline-flex;align-items:center;gap:5px;padding:5px 11px;background:color-mix(in srgb,var(--apple-red) 14%,var(--dark-bg-tertiary));border:1px solid color-mix(in srgb,var(--apple-red) 30%,var(--dark-separator));border-radius:20px;font-size:0.72rem;font-weight:600;color:var(--apple-red);text-decoration:none;white-space:nowrap"
               onmouseover="this.style.opacity=.75" onmouseout="this.style.opacity=1">
                <i class="fas fa-xmark"></i>Reset
                <span style="display:inline-flex;align-items:center;justify-content:center;width:16px;height:16px;background:var(--apple-red);color:#fff;border-radius:50%;font-size:0.6rem;font-weight:700">{{ $siActiveFilters }}</span>
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
            <h3 style="font-size:0.95rem;font-weight:700;color:var(--dark-text-primary);margin:3px 0 0">Daftar Service Inquiry</h3>
        </div>
        <span style="font-size:0.75rem;color:var(--dark-text-secondary)">
            @if($inquiries instanceof \Illuminate\Pagination\LengthAwarePaginator)
                @if($inquiries->total() === 0)
                    0 inquiry
                @else
                    {{ $inquiries->firstItem() }}–{{ $inquiries->lastItem() }} dari {{ $inquiries->total() }}
                @endif
            @else
                {{ $inquiries->count() }} entri
            @endif
        </span>
    </div>
    <x-ui.table
        :columns="[
            ['key' => 'inquiry_number', 'label' => 'Inquiry #'],
            ['key' => 'tanggal',        'label' => 'Tanggal'],
            ['key' => 'perusahaan',     'label' => 'Perusahaan'],
            ['key' => 'kontak',         'label' => 'Kontak'],
            ['key' => 'status',         'label' => 'Status'],
            ['key' => 'priority',       'label' => 'Prioritas'],
            ['key' => 'est_value',      'label' => 'Est. Value'],
            ['key' => 'actions',        'label' => 'Aksi', 'align' => 'right'],
        ]"
        :rows="$inquiries"
        :cellRenderers="$cellRenderers"
        :striped="true"
        :hoverable="true"
        variant="compact"
        empty-message="Tidak ada inquiry. Coba ubah filter."
    />
    @if($inquiries instanceof \Illuminate\Pagination\LengthAwarePaginator && $inquiries->hasPages())
        <div style="padding:14px 20px;border-top:1px solid var(--dark-separator)">
            <x-ui.pagination :paginator="$inquiries->appends(array_merge(request()->all(), ['tab'=>'service-inquiries']))" variant="full" :show-info="true" />
        </div>
    @endif
</div>
