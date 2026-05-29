{{-- Service Cost Requests Tab --}}
@php
    $stats = $serviceCostRequestsStats ?? [
        'total' => 0, 'pending' => 0, 'reviewing' => 0, 'quoted' => 0,
        'accepted' => 0, 'rejected' => 0, 'this_week' => 0, 'this_month' => 0,
    ];
    $serviceCostRequests = $serviceCostRequests ?? collect();

    $statusMap = [
        'pending'   => ['variant' => 'warning', 'label' => 'Pending'],
        'reviewing' => ['variant' => 'info',    'label' => 'Reviewing'],
        'quoted'    => ['variant' => 'primary', 'label' => 'Quoted'],
        'accepted'  => ['variant' => 'success', 'label' => 'Accepted'],
        'rejected'  => ['variant' => 'danger',  'label' => 'Rejected'],
        'cancelled' => ['variant' => 'neutral', 'label' => 'Cancelled'],
    ];

    $cellRenderers = [
        'request_number' => function ($row) {
            return '<span style="font-family:monospace;font-size:0.8rem;font-weight:700;color:var(--dark-text-primary);letter-spacing:.02em">' . e($row->request_number) . '</span>';
        },
        'tanggal' => function ($row) {
            return '<span style="font-size:0.8rem;color:var(--dark-text-primary)">' . e($row->created_at->format('d M Y')) . '</span>'
                 . '<br><span style="font-size:0.7rem;color:var(--dark-text-secondary)">' . e($row->created_at->format('H:i')) . '</span>';
        },
        'pemohon' => function ($row) {
            $type = $row->applicant_type === 'badan' ? 'Badan Usaha' : 'Perorangan';
            $typeColor = $row->applicant_type === 'badan' ? 'var(--apple-purple)' : 'var(--apple-teal)';
            return '<span style="font-size:0.85rem;font-weight:600;color:var(--dark-text-primary);display:block">' . e($row->display_name) . '</span>'
                 . '<span style="font-size:0.68rem;font-weight:600;color:' . $typeColor . '">' . e($type) . '</span>';
        },
        'kontak' => function ($row) {
            return '<span style="font-size:0.8rem;color:var(--dark-text-primary);display:block">' . e($row->email) . '</span>'
                 . '<span style="font-size:0.7rem;color:var(--dark-text-secondary)">' . e($row->phone) . '</span>';
        },
        'kategori' => function ($row) {
            $cats = \App\Models\ServiceCostRequest::getServiceCategories();
            $label = $cats[$row->service_category] ?? $row->service_category;
            return '<span style="font-size:0.8rem;color:var(--dark-text-primary)">' . e($label) . '</span>';
        },
        'status' => function ($row) use ($statusMap) {
            $s = $statusMap[$row->status] ?? ['variant' => 'neutral', 'label' => ucfirst($row->status)];
            return \Illuminate\Support\Facades\Blade::render('<x-ui.badge :variant="$v">{{ $l }}</x-ui.badge>', ['v' => $s['variant'], 'l' => $s['label']]);
        },
        'actions' => function ($row) {
            $url = route('admin.service-cost-requests.show', $row->request_number);
            return '<a href="' . e($url) . '" style="display:inline-flex;align-items:center;gap:5px;font-size:0.75rem;font-weight:600;color:var(--apple-blue);text-decoration:none;transition:opacity .15s" onmouseover="this.style.opacity=.7" onmouseout="this.style.opacity=1"><i class="fas fa-eye"></i>Detail</a>';
        },
    ];
@endphp

{{-- Stats Strip --}}
<div style="display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin-bottom:16px">
    @php $statsData = [
        ['label'=>'Total',    'value'=>$stats['total'],    'sub'=>'semua permohonan',  'color'=>'var(--dark-text-primary)',  'bg'=>'transparent',          'icon'=>'fa-file-alt'],
        ['label'=>'Pending',  'value'=>$stats['pending'],  'sub'=>'perlu ditinjau',    'color'=>'var(--apple-orange)',       'bg'=>'var(--apple-orange)',   'icon'=>'fa-clock'],
        ['label'=>'Reviewing','value'=>$stats['reviewing'],'sub'=>'sedang diproses',   'color'=>'var(--apple-blue)',         'bg'=>'var(--apple-blue)',     'icon'=>'fa-search'],
        ['label'=>'Accepted', 'value'=>$stats['accepted'], 'sub'=>($stats['total']>0 ? round(($stats['accepted']/$stats['total'])*100).'% rate' : '—'), 'color'=>'var(--apple-green)', 'bg'=>'var(--apple-green)', 'icon'=>'fa-check-circle'],
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
        ['label'=>'Quoted',    'value'=>$stats['quoted'],    'color'=>'var(--apple-indigo)', 'bg'=>'var(--apple-indigo)', 'icon'=>'fa-file-invoice'],
        ['label'=>'Rejected',  'value'=>$stats['rejected'],  'color'=>'var(--apple-red)',    'bg'=>'var(--apple-red)',    'icon'=>'fa-times-circle'],
        ['label'=>'Minggu Ini','value'=>$stats['this_week'], 'color'=>'var(--dark-text-primary)', 'bg'=>'transparent',   'icon'=>'fa-calendar-week'],
        ['label'=>'Bulan Ini', 'value'=>$stats['this_month'],'color'=>'var(--dark-text-primary)', 'bg'=>'transparent',   'icon'=>'fa-calendar'],
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
    $scrActiveFilters = collect([
        'search'         => request('search'),
        'status'         => request('status'),
        'applicant_type' => request('applicant_type'),
    ])->filter()->count();
@endphp
<form method="GET" action="{{ route('admin.leads.index') }}" style="margin-bottom:16px">
    <input type="hidden" name="tab" value="service-cost-requests">
    <div style="background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:14px;padding:12px 14px;display:flex;align-items:center;gap:10px;flex-wrap:wrap">

        {{-- Search --}}
        <div style="position:relative;flex:1;min-width:220px">
            <i class="fas fa-magnifying-glass" style="position:absolute;left:12px;top:50%;transform:translateY(-50%);font-size:0.72rem;color:var(--dark-text-tertiary);pointer-events:none;z-index:1"></i>
            <input type="text" name="search" id="scr-search" value="{{ request('search') }}"
                   placeholder="Nomor request, email, nama pemohon…"
                   style="width:100%;padding:8px 36px 8px 34px;background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:10px;color:var(--dark-text-primary);font-size:0.82rem;line-height:1.4;outline:none;box-sizing:border-box;transition:border-color .18s"
                   onfocus="this.style.borderColor='var(--apple-orange)'"
                   onblur="this.style.borderColor='var(--dark-separator)'">
            <button type="button"
                    style="display:{{ request('search') ? 'flex' : 'none' }};position:absolute;right:9px;top:50%;transform:translateY(-50%);width:18px;height:18px;align-items:center;justify-content:center;background:var(--dark-text-tertiary);border:none;border-radius:50%;cursor:pointer;padding:0;color:var(--dark-bg-primary);font-size:0.55rem"
                    onclick="document.getElementById('scr-search').value='';this.style.display='none';this.closest('form').submit()">
                <i class="fas fa-xmark"></i>
            </button>
        </div>

        <div style="width:1px;height:26px;background:var(--dark-separator);flex-shrink:0"></div>

        <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap">

            {{-- Status pill --}}
            <div style="position:relative">
                <select name="status" onchange="this.closest('form').submit()"
                        style="padding:6px 28px 6px 10px;background:{{ request('status') ? 'color-mix(in srgb,var(--apple-orange) 18%,var(--dark-bg-tertiary))' : 'var(--dark-bg-tertiary)' }};border:1px solid {{ request('status') ? 'color-mix(in srgb,var(--apple-orange) 45%,var(--dark-separator))' : 'var(--dark-separator)' }};border-radius:20px;color:{{ request('status') ? 'var(--apple-orange)' : 'var(--dark-text-secondary)' }};font-size:0.75rem;font-weight:{{ request('status') ? '600' : '500' }};outline:none;appearance:none;-webkit-appearance:none;cursor:pointer;white-space:nowrap;transition:all .18s">
                    <option value="">Status</option>
                    <option value="pending"   {{ request('status')=='pending'   ? 'selected':'' }}>Pending</option>
                    <option value="reviewing" {{ request('status')=='reviewing' ? 'selected':'' }}>Reviewing</option>
                    <option value="quoted"    {{ request('status')=='quoted'    ? 'selected':'' }}>Quoted</option>
                    <option value="accepted"  {{ request('status')=='accepted'  ? 'selected':'' }}>Accepted</option>
                    <option value="rejected"  {{ request('status')=='rejected'  ? 'selected':'' }}>Rejected</option>
                    <option value="cancelled" {{ request('status')=='cancelled' ? 'selected':'' }}>Cancelled</option>
                </select>
                <i class="fas fa-chevron-down" style="position:absolute;right:9px;top:50%;transform:translateY(-50%);font-size:0.5rem;color:{{ request('status') ? 'var(--apple-orange)' : 'var(--dark-text-tertiary)' }};pointer-events:none"></i>
            </div>

            {{-- Pemohon pill --}}
            <div style="position:relative">
                <select name="applicant_type" onchange="this.closest('form').submit()"
                        style="padding:6px 28px 6px 10px;background:{{ request('applicant_type') ? 'color-mix(in srgb,var(--apple-purple) 18%,var(--dark-bg-tertiary))' : 'var(--dark-bg-tertiary)' }};border:1px solid {{ request('applicant_type') ? 'color-mix(in srgb,var(--apple-purple) 45%,var(--dark-separator))' : 'var(--dark-separator)' }};border-radius:20px;color:{{ request('applicant_type') ? 'var(--apple-purple)' : 'var(--dark-text-secondary)' }};font-size:0.75rem;font-weight:{{ request('applicant_type') ? '600' : '500' }};outline:none;appearance:none;-webkit-appearance:none;cursor:pointer;white-space:nowrap;transition:all .18s">
                    <option value="">Pemohon</option>
                    <option value="perorangan" {{ request('applicant_type')=='perorangan' ? 'selected':'' }}>Perorangan</option>
                    <option value="badan"      {{ request('applicant_type')=='badan'      ? 'selected':'' }}>Badan Usaha</option>
                </select>
                <i class="fas fa-chevron-down" style="position:absolute;right:9px;top:50%;transform:translateY(-50%);font-size:0.5rem;color:{{ request('applicant_type') ? 'var(--apple-purple)' : 'var(--dark-text-tertiary)' }};pointer-events:none"></i>
            </div>

            @if($scrActiveFilters > 0)
            <a href="{{ route('admin.leads.index', ['tab' => 'service-cost-requests']) }}"
               style="display:inline-flex;align-items:center;gap:5px;padding:5px 11px;background:color-mix(in srgb,var(--apple-red) 14%,var(--dark-bg-tertiary));border:1px solid color-mix(in srgb,var(--apple-red) 30%,var(--dark-separator));border-radius:20px;font-size:0.72rem;font-weight:600;color:var(--apple-red);text-decoration:none;white-space:nowrap"
               onmouseover="this.style.opacity=.75" onmouseout="this.style.opacity=1">
                <i class="fas fa-xmark"></i>Reset
                <span style="display:inline-flex;align-items:center;justify-content:center;width:16px;height:16px;background:var(--apple-red);color:#fff;border-radius:50%;font-size:0.6rem;font-weight:700">{{ $scrActiveFilters }}</span>
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
            <h3 style="font-size:0.95rem;font-weight:700;color:var(--dark-text-primary);margin:3px 0 0">Daftar Permohonan Biaya</h3>
        </div>
        <span style="font-size:0.75rem;color:var(--dark-text-secondary)">
            @if($serviceCostRequests instanceof \Illuminate\Pagination\LengthAwarePaginator)
                @if($serviceCostRequests->total() === 0)
                    0 permohonan
                @else
                    {{ $serviceCostRequests->firstItem() }}–{{ $serviceCostRequests->lastItem() }} dari {{ $serviceCostRequests->total() }}
                @endif
            @else
                {{ $serviceCostRequests->count() }} entri
            @endif
        </span>
    </div>
    <x-ui.table
        :columns="[
            ['key' => 'request_number', 'label' => 'Request #'],
            ['key' => 'tanggal',        'label' => 'Tanggal'],
            ['key' => 'pemohon',        'label' => 'Pemohon'],
            ['key' => 'kontak',         'label' => 'Kontak'],
            ['key' => 'kategori',       'label' => 'Kategori'],
            ['key' => 'status',         'label' => 'Status'],
            ['key' => 'actions',        'label' => 'Aksi', 'align' => 'right'],
        ]"
        :rows="$serviceCostRequests"
        :cellRenderers="$cellRenderers"
        :striped="true"
        :hoverable="true"
        variant="compact"
        empty-message="Tidak ada permohonan. Coba ubah filter."
    />
    @if($serviceCostRequests instanceof \Illuminate\Pagination\LengthAwarePaginator && $serviceCostRequests->hasPages())
        <div style="padding:14px 20px;border-top:1px solid var(--dark-separator)">
            <x-ui.pagination :paginator="$serviceCostRequests->appends(array_merge(request()->all(), ['tab'=>'service-cost-requests']))" variant="full" :show-info="true" />
        </div>
    @endif
</div>

<x-ui.alert variant="warning" :icon="true">
    <strong>Tentang Permohonan Biaya:</strong>
    Data dari formulir <code>/permohonan</code> masuk ke tab ini untuk review tim admin sebelum diteruskan ke proses quotation.
</x-ui.alert>
