{{-- Applications Tab --}}
@php
    $statusMap = [
        'submitted'           => ['Diajukan',       'var(--apple-blue)'],
        'under_review'        => ['Ditinjau',        'var(--apple-orange)'],
        'document_incomplete' => ['Dok. Kurang',     'var(--apple-red)'],
        'quoted'              => ['Penawaran',       'var(--apple-yellow)'],
        'quotation_accepted'  => ['Penawaran OK',    'var(--apple-yellow)'],
        'payment_pending'     => ['Tunggu Bayar',    'var(--apple-orange)'],
        'payment_verified'    => ['Bayar OK',        'var(--apple-green)'],
        'in_progress'         => ['Proses',          'var(--apple-teal)'],
        'completed'           => ['Selesai',         'var(--apple-green)'],
        'cancelled'           => ['Dibatalkan',      'var(--apple-red)'],
        'draft'               => ['Draf',            'var(--dark-text-secondary)'],
    ];
@endphp

<div style="display:flex;flex-direction:column;gap:16px">

    {{-- Filter Form --}}
    <div style="background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:14px;padding:16px 20px">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:14px">
            <div>
                <p style="font-size:0.6rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--dark-text-secondary);margin:0">Pencarian</p>
                <h3 style="font-size:0.88rem;font-weight:700;color:var(--dark-text-primary);margin:3px 0 0">Cari Permohonan Izin</h3>
            </div>
            <span style="font-size:0.75rem;color:var(--dark-text-secondary)"><i class="fas fa-info-circle" style="margin-right:4px"></i>{{ $applications->total() ?? 0 }} hasil</span>
        </div>
        <form method="GET" action="{{ route('admin.permits.index') }}">
            <input type="hidden" name="tab" value="applications">
            <div style="display:grid;grid-template-columns:2fr 1fr 1fr auto;gap:10px;align-items:end">
                <div>
                    <label style="font-size:0.68rem;font-weight:600;color:var(--dark-text-secondary);display:block;margin-bottom:4px">Nomor / Nama Klien</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nomor atau nama klien..."
                           style="background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);color:var(--dark-text-primary);border-radius:8px;padding:8px 12px;font-size:0.85rem;width:100%;outline:none"
                           onfocus="this.style.borderColor='var(--apple-blue)'" onblur="this.style.borderColor='var(--dark-separator)'">
                </div>
                <div>
                    <label style="font-size:0.68rem;font-weight:600;color:var(--dark-text-secondary);display:block;margin-bottom:4px">Status</label>
                    <select name="status" style="background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);color:var(--dark-text-primary);border-radius:8px;padding:8px 12px;font-size:0.85rem;width:100%;outline:none">
                        <option value="">Semua Status</option>
                        @foreach($statuses as $status)
                        <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>{{ ucfirst(str_replace('_',' ',$status)) }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label style="font-size:0.68rem;font-weight:600;color:var(--dark-text-secondary);display:block;margin-bottom:4px">Jenis Izin</label>
                    <select name="permit_type" style="background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);color:var(--dark-text-primary);border-radius:8px;padding:8px 12px;font-size:0.85rem;width:100%;outline:none">
                        <option value="">Semua Jenis</option>
                        @foreach($permitTypes as $type)
                        <option value="{{ $type->id }}" {{ request('permit_type') == $type->id ? 'selected' : '' }}>{{ $type->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div style="display:flex;gap:6px">
                    <button type="submit" style="display:inline-flex;align-items:center;justify-content:center;gap:5px;padding:8px 16px;border-radius:8px;background:var(--apple-blue);color:#fff;font-size:0.82rem;font-weight:600;border:none;cursor:pointer">
                        <i class="fas fa-filter" style="font-size:0.7rem"></i>Filter
                    </button>
                    <a href="{{ route('admin.permits.index', ['tab' => 'applications']) }}" style="display:inline-flex;align-items:center;justify-content:center;width:36px;height:36px;border-radius:8px;background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);color:var(--dark-text-secondary);text-decoration:none">
                        <i class="fas fa-times" style="font-size:0.75rem"></i>
                    </a>
                </div>
            </div>
        </form>
    </div>

    {{-- Table --}}
    <div style="background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:14px;overflow:hidden">
        <div style="overflow-x:auto">
            <table style="width:100%;border-collapse:collapse">
                <thead style="background:var(--dark-bg-secondary)">
                    <tr>
                        @foreach(['Nomor','Klien','Jenis Izin','Status','Tanggal','Aksi'] as $col)
                        <th style="padding:10px 14px;font-size:0.6rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--dark-text-secondary);text-align:{{ $col === 'Aksi' ? 'center' : 'left' }};border-bottom:1px solid var(--dark-separator);white-space:nowrap">{{ $col }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @forelse($applications as $app)
                    @php
                        $sColor = $statusMap[$app->status][1] ?? 'var(--dark-text-secondary)';
                        $sLabel = $statusMap[$app->status][0] ?? ucfirst(str_replace('_',' ',$app->status));
                    @endphp
                    <tr style="border-bottom:1px solid var(--dark-separator)" onmouseover="this.style.background='var(--dark-bg-secondary)'" onmouseout="this.style.background='transparent'">
                        <td style="padding:12px 14px">
                            <span style="font-size:0.82rem;font-weight:600;color:var(--dark-text-primary)">{{ $app->application_number }}</span>
                        </td>
                        <td style="padding:12px 14px">
                            <span style="font-size:0.82rem;font-weight:500;color:var(--dark-text-primary);display:block">{{ $app->client->company_name ?? $app->client->name ?? 'N/A' }}</span>
                            @if($app->reviewer)
                            <span style="font-size:0.7rem;color:var(--dark-text-secondary);display:block;margin-top:2px"><i class="fas fa-user-tag" style="margin-right:3px"></i>{{ $app->reviewer->name }}</span>
                            @endif
                        </td>
                        <td style="padding:12px 14px;font-size:0.82rem;color:var(--dark-text-secondary)">{{ $app->permitType->name ?? 'N/A' }}</td>
                        <td style="padding:12px 14px;white-space:nowrap">
                            <span style="display:inline-flex;align-items:center;padding:3px 10px;border-radius:20px;font-size:0.68rem;font-weight:600;background:color-mix(in srgb,{{ $sColor }} 15%,transparent);color:{{ $sColor }}">{{ $sLabel }}</span>
                        </td>
                        <td style="padding:12px 14px">
                            <span style="font-size:0.82rem;color:var(--dark-text-secondary);display:block">{{ optional($app->created_at)->locale('id')->isoFormat('D MMM Y') }}</span>
                            <span style="font-size:0.7rem;color:var(--dark-text-secondary);opacity:.7;display:block;margin-top:1px">{{ optional($app->created_at)->locale('id')->diffForHumans() }}</span>
                        </td>
                        <td style="padding:12px 14px;text-align:center;white-space:nowrap">
                            <a href="{{ route('admin.permit-applications.show', $app->id) }}" style="display:inline-flex;align-items:center;justify-content:center;width:30px;height:30px;border-radius:8px;background:color-mix(in srgb,var(--apple-teal) 15%,transparent);color:var(--apple-teal);text-decoration:none;border:1px solid color-mix(in srgb,var(--apple-teal) 30%,transparent)" title="Detail" onmouseover="this.style.opacity=.7" onmouseout="this.style.opacity=1">
                                <i class="fas fa-eye" style="font-size:0.7rem"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" style="padding:48px;text-align:center">
                            <i class="fas fa-inbox" style="font-size:2rem;color:var(--dark-text-secondary);opacity:.4;display:block;margin-bottom:12px"></i>
                            <p style="font-size:0.88rem;font-weight:600;color:var(--dark-text-primary);margin:0 0 4px">Belum Ada Permohonan</p>
                            <p style="font-size:0.78rem;color:var(--dark-text-secondary);margin:0">Permohonan izin dari klien akan muncul di sini</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Pagination --}}
    @if($applications->hasPages())
    <div style="padding:14px 20px;background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:12px">
        <x-ui.pagination :paginator="$applications->appends(array_merge(request()->all(), ['tab'=>'applications']))" variant="full" :show-info="true" />
    </div>
    @endif

</div>
