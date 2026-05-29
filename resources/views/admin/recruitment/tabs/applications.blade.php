@php
    $statusMeta = [
        'pending'  => ['label' => 'Pending',   'color' => 'var(--apple-yellow)'],
        'reviewed' => ['label' => 'Direview',  'color' => 'var(--apple-blue)'],
        'interview'=> ['label' => 'Interview', 'color' => 'var(--apple-purple)'],
        'accepted' => ['label' => 'Diterima',  'color' => 'var(--apple-green)'],
        'rejected' => ['label' => 'Ditolak',   'color' => 'var(--apple-red)'],
    ];
@endphp

<div style="display:flex;flex-direction:column;gap:16px">

    {{-- Stats Strip --}}
    <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:12px">
        @php $statsA = [
            ['label'=>'Pending',    'value'=>$pendingCount ?? 0,                                     'sub'=>'Perlu peninjauan',      'color'=>'var(--apple-yellow)', 'bg'=>'var(--apple-yellow)'],
            ['label'=>'Interview',  'value'=>$interviewCount ?? 0,                                   'sub'=>'Proses wawancara',      'color'=>'var(--apple-purple)', 'bg'=>'var(--apple-purple)'],
            ['label'=>'Diterima',   'value'=>$offeredCount ?? 0,                                     'sub'=>'Kandidat sukses',       'color'=>'var(--apple-green)',  'bg'=>'var(--apple-green)'],
            ['label'=>'Ditolak',    'value'=>$applications->where('status','rejected')->count(),     'sub'=>'Tidak sesuai kriteria', 'color'=>'var(--apple-red)',    'bg'=>'var(--apple-red)'],
        ]; @endphp
        @foreach($statsA as $s)
        <div style="background:linear-gradient(135deg,color-mix(in srgb,{{ $s['bg'] }} 12%,var(--dark-bg-tertiary)) 0%,var(--dark-bg-tertiary) 100%);border:1px solid color-mix(in srgb,{{ $s['bg'] }} 25%,var(--dark-separator));border-radius:14px;padding:16px 18px">
            <p style="font-size:0.6rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:{{ $s['color'] }};opacity:.85;margin:0">{{ $s['label'] }}</p>
            <p style="font-size:1.8rem;font-weight:800;color:{{ $s['color'] }};margin:4px 0 2px;line-height:1">{{ $s['value'] }}</p>
            <p style="font-size:0.68rem;color:var(--dark-text-secondary);margin:0">{{ $s['sub'] }}</p>
        </div>
        @endforeach
    </div>

    {{-- Status Filter Pills --}}
    <div style="background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:12px;padding:10px 14px;display:flex;flex-wrap:wrap;gap:6px">
        @php
        $allCount = $applications->total();
        $filterStatuses = ['' => ['label'=>'Semua', 'count'=>$allCount]] + collect($statusMeta)->mapWithKeys(fn($m,$k)=>[$k=>$m])->toArray();
        @endphp
        @foreach($statusMeta as $statusKey => $sMeta)
        @php $isActive = request('status') === $statusKey; @endphp
        <a href="{{ route('admin.recruitment.index', ['tab' => 'applications', 'status' => $statusKey, 'search' => request('search'), 'job_id' => request('job_id')]) }}"
           style="display:inline-flex;align-items:center;gap:5px;padding:5px 12px;border-radius:20px;font-size:0.75rem;font-weight:600;text-decoration:none;transition:all .15s;background:{{ $isActive ? 'color-mix(in srgb,'.$sMeta['color'].' 20%,transparent)' : 'transparent' }};color:{{ $isActive ? $sMeta['color'] : 'var(--dark-text-secondary)' }};border:1px solid {{ $isActive ? 'color-mix(in srgb,'.$sMeta['color'].' 40%,transparent)' : 'var(--dark-separator)' }}"
           onmouseover="this.style.color='{{ $sMeta['color'] }}'" onmouseout="if(!{{ $isActive ? 'true' : 'false' }})this.style.color='var(--dark-text-secondary)'">
            {{ $sMeta['label'] }}
        </a>
        @endforeach
        @if(!request('status'))
        <span style="display:inline-flex;align-items:center;gap:5px;padding:5px 12px;border-radius:20px;font-size:0.75rem;font-weight:600;background:color-mix(in srgb,var(--apple-blue) 20%,transparent);color:var(--apple-blue);border:1px solid color-mix(in srgb,var(--apple-blue) 40%,transparent)">Semua ({{ $allCount }})</span>
        @else
        <a href="{{ route('admin.recruitment.index', ['tab' => 'applications']) }}" style="display:inline-flex;align-items:center;gap:5px;padding:5px 12px;border-radius:20px;font-size:0.75rem;font-weight:600;text-decoration:none;color:var(--dark-text-secondary);border:1px solid var(--dark-separator)">Semua ({{ $allCount }})</a>
        @endif
    </div>

    {{-- Filter --}}
    <div style="background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:14px;padding:16px 20px">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:14px">
            <div>
                <p style="font-size:0.6rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--dark-text-secondary);margin:0">Pencarian & Filter</p>
                <h3 style="font-size:0.88rem;font-weight:700;color:var(--dark-text-primary);margin:3px 0 0">Temukan Kandidat</h3>
            </div>
            <span style="font-size:0.75rem;color:var(--dark-text-secondary)">{{ $applications->total() }} lamaran</span>
        </div>
        <form method="GET" action="{{ route('admin.recruitment.index') }}">
            <input type="hidden" name="tab" value="applications">
            @if(request('status'))<input type="hidden" name="status" value="{{ request('status') }}">@endif
            <div style="display:grid;grid-template-columns:2fr 1fr auto;gap:10px;align-items:flex-end">
                <div style="position:relative">
                    <i class="fas fa-search" style="position:absolute;left:11px;top:50%;transform:translateY(-50%);color:var(--dark-text-secondary);font-size:0.75rem;pointer-events:none"></i>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Nama atau email kandidat..."
                           style="width:100%;padding:9px 12px 9px 32px;background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:10px;color:var(--dark-text-primary);font-size:0.82rem;outline:none;box-sizing:border-box"
                           onfocus="this.style.borderColor='var(--apple-blue)'" onblur="this.style.borderColor='var(--dark-separator)'">
                </div>
                <div style="position:relative">
                    <select name="job_id"
                            style="width:100%;padding:9px 32px 9px 12px;background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:10px;color:var(--dark-text-primary);font-size:0.82rem;outline:none;appearance:none"
                            onfocus="this.style.borderColor='var(--apple-blue)'" onblur="this.style.borderColor='var(--dark-separator)'">
                        <option value="">Semua Lowongan</option>
                        @foreach($jobsForFilter ?? [] as $job)
                        <option value="{{ $job->id }}" {{ request('job_id') == $job->id ? 'selected' : '' }}>{{ $job->title }}</option>
                        @endforeach
                    </select>
                    <i class="fas fa-chevron-down" style="position:absolute;right:11px;top:50%;transform:translateY(-50%);color:var(--dark-text-secondary);font-size:0.65rem;pointer-events:none"></i>
                </div>
                <div style="display:flex;gap:6px">
                    <button type="submit" style="padding:9px 18px;background:var(--apple-blue);color:#fff;border:none;border-radius:10px;font-size:0.8rem;font-weight:600;cursor:pointer;transition:opacity .2s;white-space:nowrap" onmouseover="this.style.opacity=.85" onmouseout="this.style.opacity=1">
                        <i class="fas fa-search" style="margin-right:5px"></i>Cari
                    </button>
                    @if(request()->hasAny(['search','job_id','status']))
                    <a href="{{ route('admin.recruitment.index', ['tab' => 'applications']) }}" style="display:inline-flex;align-items:center;justify-content:center;width:36px;height:36px;border-radius:10px;background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);color:var(--dark-text-secondary);text-decoration:none" title="Reset">
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
                        @foreach(['Kandidat','Lowongan','Pendidikan','Status','Tanggal Lamar','Aksi'] as $col)
                        <th style="padding:10px 14px;font-size:0.6rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--dark-text-secondary);text-align:{{ $col === 'Aksi' ? 'right' : 'left' }};border-bottom:1px solid var(--dark-separator);white-space:nowrap">{{ $col }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @forelse($applications as $application)
                    @php $sMeta = $statusMeta[$application->status] ?? ['label' => ucfirst($application->status), 'color' => 'var(--dark-text-secondary)']; @endphp
                    <tr style="border-bottom:1px solid var(--dark-separator)" onmouseover="this.style.background='var(--dark-bg-secondary)'" onmouseout="this.style.background='transparent'">
                        <td style="padding:12px 14px">
                            <div style="display:flex;align-items:center;gap:10px">
                                <div style="width:36px;height:36px;border-radius:50%;flex-shrink:0;display:flex;align-items:center;justify-content:center;font-size:0.82rem;font-weight:700;background:color-mix(in srgb,var(--apple-blue) 20%,var(--dark-bg-secondary));color:var(--apple-blue)">
                                    {{ strtoupper(substr($application->full_name, 0, 1)) }}
                                </div>
                                <div>
                                    <span style="font-size:0.85rem;font-weight:600;color:var(--dark-text-primary);display:block">{{ $application->full_name }}</span>
                                    <span style="font-size:0.72rem;color:var(--dark-text-secondary);display:block"><i class="fas fa-envelope" style="margin-right:4px;opacity:.6"></i>{{ $application->email }}</span>
                                    @if($application->phone)
                                    <span style="font-size:0.72rem;color:var(--dark-text-secondary)"><i class="fas fa-phone" style="margin-right:4px;opacity:.6"></i>{{ $application->phone }}</span>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td style="padding:12px 14px">
                            <span style="font-size:0.85rem;font-weight:600;color:var(--dark-text-primary);display:block">{{ $application->jobVacancy->title ?? '—' }}</span>
                            @if($application->has_experience_ukl_upl)
                            <span style="display:inline-flex;padding:2px 8px;border-radius:20px;font-size:0.68rem;font-weight:600;background:color-mix(in srgb,var(--apple-green) 15%,transparent);color:var(--apple-green);margin-top:3px">UKL-UPL Exp</span>
                            @endif
                        </td>
                        <td style="padding:12px 14px">
                            <span style="font-size:0.82rem;color:var(--dark-text-primary);display:block">{{ $application->education_level }} {{ $application->major }}</span>
                            <span style="font-size:0.72rem;color:var(--dark-text-secondary);display:block">{{ $application->institution }}</span>
                            @if($application->gpa)
                            <span style="font-size:0.7rem;color:var(--dark-text-secondary)">IPK: {{ number_format($application->gpa, 2) }}</span>
                            @endif
                        </td>
                        <td style="padding:12px 14px">
                            <span style="display:inline-flex;align-items:center;padding:3px 10px;border-radius:20px;font-size:0.68rem;font-weight:600;background:color-mix(in srgb,{{ $sMeta['color'] }} 15%,transparent);color:{{ $sMeta['color'] }}">{{ $sMeta['label'] }}</span>
                        </td>
                        <td style="padding:12px 14px">
                            <span style="font-size:0.82rem;color:var(--dark-text-secondary);display:block">{{ $application->created_at->format('d M Y') }}</span>
                            <span style="font-size:0.7rem;color:var(--dark-text-secondary);opacity:.6">{{ $application->created_at->diffForHumans() }}</span>
                        </td>
                        <td style="padding:12px 14px;text-align:right">
                            <a href="{{ route('admin.applications.show', $application->id) }}"
                               style="display:inline-flex;align-items:center;gap:5px;font-size:0.75rem;font-weight:600;color:var(--apple-teal);background:color-mix(in srgb,var(--apple-teal) 12%,transparent);padding:5px 10px;border-radius:7px;border:1px solid color-mix(in srgb,var(--apple-teal) 25%,transparent);text-decoration:none"
                               onmouseover="this.style.opacity=.7" onmouseout="this.style.opacity=1">
                                <i class="fas fa-eye"></i>Detail
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" style="padding:48px;text-align:center">
                            <i class="fas fa-user-tie" style="font-size:2rem;color:var(--dark-text-secondary);opacity:.4;display:block;margin-bottom:12px"></i>
                            <p style="font-size:0.88rem;font-weight:600;color:var(--dark-text-primary);margin:0 0 4px">Belum Ada Lamaran</p>
                            <p style="font-size:0.78rem;color:var(--dark-text-secondary);margin:0">Lamaran yang sesuai filter tidak ditemukan</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($applications instanceof \Illuminate\Pagination\LengthAwarePaginator && $applications->hasPages())
        <div style="padding:14px 20px;border-top:1px solid var(--dark-separator)">
            <x-ui.pagination :paginator="$applications->appends(array_merge(request()->all(), ['tab'=>'applications']))" variant="full" :show-info="true" />
        </div>
        @endif
    </div>

</div>
