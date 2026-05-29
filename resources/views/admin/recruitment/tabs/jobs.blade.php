@php
    $statusMeta = [
        'open'   => ['label' => 'Aktif',    'color' => 'var(--apple-green)'],
        'draft'  => ['label' => 'Draft',    'color' => 'var(--apple-yellow)'],
        'closed' => ['label' => 'Ditutup',  'color' => 'var(--apple-red)'],
    ];
    $employmentOptions = $jobs->pluck('employment_type')->filter()->unique()->values();
    $locationOptions   = $jobs->pluck('location')->filter()->unique()->values();
@endphp

<div style="display:flex;flex-direction:column;gap:16px">

    {{-- Stats Strip --}}
    <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:12px">
        @php $statsJ = [
            ['label'=>'Lowongan Aktif', 'value'=>$activeCount ?? 0,  'sub'=>'Sedang tayang untuk publik',  'color'=>'var(--apple-green)',  'bg'=>'var(--apple-green)'],
            ['label'=>'Draft',          'value'=>$draftCount ?? 0,   'sub'=>'Belum dipublikasikan',        'color'=>'var(--apple-yellow)', 'bg'=>'var(--apple-yellow)'],
            ['label'=>'Ditutup',        'value'=>$closedCount ?? 0,  'sub'=>'Lowongan selesai',            'color'=>'var(--apple-red)',    'bg'=>'var(--apple-red)'],
        ]; @endphp
        @foreach($statsJ as $s)
        <div style="background:linear-gradient(135deg,color-mix(in srgb,{{ $s['bg'] }} 12%,var(--dark-bg-tertiary)) 0%,var(--dark-bg-tertiary) 100%);border:1px solid color-mix(in srgb,{{ $s['bg'] }} 25%,var(--dark-separator));border-radius:14px;padding:16px 18px">
            <p style="font-size:0.6rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:{{ $s['color'] }};opacity:.85;margin:0">{{ $s['label'] }}</p>
            <p style="font-size:1.8rem;font-weight:800;color:{{ $s['color'] }};margin:4px 0 2px;line-height:1">{{ $s['value'] }}</p>
            <p style="font-size:0.68rem;color:var(--dark-text-secondary);margin:0">{{ $s['sub'] }}</p>
        </div>
        @endforeach
    </div>

    {{-- Filter --}}
    <div style="background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:14px;padding:16px 20px">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:14px">
            <div>
                <p style="font-size:0.6rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--dark-text-secondary);margin:0">Pencarian & Filter</p>
                <h3 style="font-size:0.88rem;font-weight:700;color:var(--dark-text-primary);margin:3px 0 0">Susun Daftar Lowongan</h3>
            </div>
            <span style="font-size:0.75rem;color:var(--dark-text-secondary)">{{ $jobs->total() }} hasil</span>
        </div>
        <form method="GET" action="{{ route('admin.recruitment.index') }}">
            <input type="hidden" name="tab" value="jobs">
            <div style="display:grid;grid-template-columns:2fr 1fr 1fr auto;gap:10px;align-items:flex-end">
                <div style="position:relative">
                    <i class="fas fa-search" style="position:absolute;left:11px;top:50%;transform:translateY(-50%);color:var(--dark-text-secondary);font-size:0.75rem;pointer-events:none"></i>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Judul, posisi, lokasi..."
                           style="width:100%;padding:9px 12px 9px 32px;background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:10px;color:var(--dark-text-primary);font-size:0.82rem;outline:none;box-sizing:border-box"
                           onfocus="this.style.borderColor='var(--apple-blue)'" onblur="this.style.borderColor='var(--dark-separator)'">
                </div>
                <div style="position:relative">
                    <select name="status"
                            style="width:100%;padding:9px 32px 9px 12px;background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:10px;color:var(--dark-text-primary);font-size:0.82rem;outline:none;appearance:none"
                            onfocus="this.style.borderColor='var(--apple-blue)'" onblur="this.style.borderColor='var(--dark-separator)'">
                        <option value="">Semua Status</option>
                        @foreach($jobStatuses ?? [] as $status)
                        <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>{{ $statusMeta[$status]['label'] ?? ucfirst($status) }}</option>
                        @endforeach
                    </select>
                    <i class="fas fa-chevron-down" style="position:absolute;right:11px;top:50%;transform:translateY(-50%);color:var(--dark-text-secondary);font-size:0.65rem;pointer-events:none"></i>
                </div>
                <div style="position:relative">
                    <select name="employment_type"
                            style="width:100%;padding:9px 32px 9px 12px;background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:10px;color:var(--dark-text-primary);font-size:0.82rem;outline:none;appearance:none"
                            onfocus="this.style.borderColor='var(--apple-blue)'" onblur="this.style.borderColor='var(--dark-separator)'">
                        <option value="">Semua Tipe</option>
                        @foreach($employmentTypes ?? [] as $type)
                        <option value="{{ $type }}" {{ request('employment_type') == $type ? 'selected' : '' }}>{{ ucfirst(str_replace('-',' ',$type)) }}</option>
                        @endforeach
                    </select>
                    <i class="fas fa-chevron-down" style="position:absolute;right:11px;top:50%;transform:translateY(-50%);color:var(--dark-text-secondary);font-size:0.65rem;pointer-events:none"></i>
                </div>
                <div style="display:flex;gap:6px">
                    <button type="submit" style="padding:9px 18px;background:var(--apple-blue);color:#fff;border:none;border-radius:10px;font-size:0.8rem;font-weight:600;cursor:pointer;transition:opacity .2s;white-space:nowrap" onmouseover="this.style.opacity=.85" onmouseout="this.style.opacity=1">
                        <i class="fas fa-search" style="margin-right:5px"></i>Filter
                    </button>
                    @if(request()->hasAny(['search','status','employment_type']))
                    <a href="{{ route('admin.recruitment.index', ['tab' => 'jobs']) }}" style="display:inline-flex;align-items:center;justify-content:center;width:36px;height:36px;border-radius:10px;background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);color:var(--dark-text-secondary);text-decoration:none" title="Reset">
                        <i class="fas fa-times" style="font-size:0.75rem"></i>
                    </a>
                    @endif
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
                        @foreach(['Posisi','Tipe','Lokasi','Status','Deadline','Pelamar','Dibuat','Aksi'] as $col)
                        <th style="padding:10px 14px;font-size:0.6rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--dark-text-secondary);text-align:{{ $col === 'Aksi' ? 'right' : 'left' }};border-bottom:1px solid var(--dark-separator);white-space:nowrap">{{ $col }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @forelse($jobs as $job)
                    @php
                        $meta = $statusMeta[$job->status] ?? ['label' => ucfirst($job->status), 'color' => 'var(--dark-text-secondary)'];
                    @endphp
                    <tr style="border-bottom:1px solid var(--dark-separator)" onmouseover="this.style.background='var(--dark-bg-secondary)'" onmouseout="this.style.background='transparent'">
                        <td style="padding:12px 14px">
                            <span style="font-size:0.85rem;font-weight:600;color:var(--dark-text-primary);display:block">{{ $job->title }}</span>
                            <span style="font-size:0.72rem;color:var(--dark-text-secondary)">{{ $job->position ?? 'Posisi belum diisi' }}</span>
                        </td>
                        <td style="padding:12px 14px">
                            <span style="display:inline-flex;padding:3px 10px;border-radius:20px;font-size:0.72rem;font-weight:500;background:color-mix(in srgb,var(--dark-text-secondary) 12%,transparent);color:var(--dark-text-secondary)">
                                {{ $job->employment_type ? ucfirst(str_replace('-',' ',$job->employment_type)) : 'N/A' }}
                            </span>
                        </td>
                        <td style="padding:12px 14px;font-size:0.82rem;color:var(--dark-text-secondary)">
                            <i class="fas fa-map-marker-alt" style="margin-right:5px;opacity:.5"></i>{{ $job->location ?? '-' }}
                        </td>
                        <td style="padding:12px 14px">
                            <span style="display:inline-flex;align-items:center;padding:3px 10px;border-radius:20px;font-size:0.68rem;font-weight:600;background:color-mix(in srgb,{{ $meta['color'] }} 15%,transparent);color:{{ $meta['color'] }}">{{ $meta['label'] }}</span>
                        </td>
                        <td style="padding:12px 14px;font-size:0.82rem;color:var(--dark-text-secondary)">
                            {{ $job->deadline ? \Carbon\Carbon::parse($job->deadline)->format('d M Y') : '—' }}
                        </td>
                        <td style="padding:12px 14px">
                            <span style="font-size:0.88rem;font-weight:700;color:var(--dark-text-primary);display:block">{{ $job->applications_count ?? 0 }}</span>
                            <span style="font-size:0.68rem;color:var(--dark-text-secondary)">pelamar</span>
                        </td>
                        <td style="padding:12px 14px">
                            <span style="font-size:0.82rem;color:var(--dark-text-secondary);display:block">{{ $job->created_at->format('d M Y') }}</span>
                            <span style="font-size:0.7rem;color:var(--dark-text-secondary);opacity:.6">{{ $job->created_at->diffForHumans() }}</span>
                        </td>
                        <td style="padding:12px 14px;text-align:right;white-space:nowrap">
                            <div style="display:flex;align-items:center;justify-content:flex-end;gap:6px">
                                <a href="{{ route('admin.jobs.show', $job->id) }}"
                                   style="display:inline-flex;align-items:center;gap:5px;font-size:0.75rem;font-weight:600;color:var(--apple-teal);background:color-mix(in srgb,var(--apple-teal) 12%,transparent);padding:5px 10px;border-radius:7px;border:1px solid color-mix(in srgb,var(--apple-teal) 25%,transparent);text-decoration:none"
                                   onmouseover="this.style.opacity=.7" onmouseout="this.style.opacity=1">
                                    <i class="fas fa-eye"></i>Detail
                                </a>
                                <a href="{{ route('admin.jobs.edit', $job->id) }}"
                                   style="display:inline-flex;align-items:center;gap:5px;font-size:0.75rem;font-weight:600;color:var(--apple-orange);background:color-mix(in srgb,var(--apple-orange) 12%,transparent);padding:5px 10px;border-radius:7px;border:1px solid color-mix(in srgb,var(--apple-orange) 25%,transparent);text-decoration:none"
                                   onmouseover="this.style.opacity=.7" onmouseout="this.style.opacity=1">
                                    <i class="fas fa-edit"></i>Edit
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" style="padding:48px;text-align:center">
                            <i class="fas fa-briefcase" style="font-size:2rem;color:var(--dark-text-secondary);opacity:.4;display:block;margin-bottom:12px"></i>
                            <p style="font-size:0.88rem;font-weight:600;color:var(--dark-text-primary);margin:0 0 4px">Belum Ada Lowongan</p>
                            <p style="font-size:0.78rem;color:var(--dark-text-secondary);margin:0 0 14px">Lowongan yang sesuai filter tidak ditemukan</p>
                            <a href="{{ route('admin.jobs.create') }}" style="display:inline-flex;align-items:center;gap:6px;padding:8px 18px;border-radius:8px;background:var(--apple-blue);color:#fff;font-size:0.82rem;font-weight:600;text-decoration:none"><i class="fas fa-plus" style="font-size:0.7rem"></i>Tambah Lowongan Pertama</a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($jobs instanceof \Illuminate\Pagination\LengthAwarePaginator && $jobs->hasPages())
        <div style="padding:14px 20px;border-top:1px solid var(--dark-separator)">
            <x-ui.pagination :paginator="$jobs->appends(array_merge(request()->all(), ['tab'=>'jobs']))" variant="full" :show-info="true" />
        </div>
        @endif
    </div>

</div>

