{{-- Permit Types Tab --}}
<div style="display:flex;flex-direction:column;gap:16px">

    {{-- Quick Stats --}}
    <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:10px">
        @php
            $typeStats = [
                ['label'=>'Total Jenis',        'value'=>$totalTypes,      'sub'=>'Izin terdaftar',         'color'=>'var(--dark-text-primary)'],
                ['label'=>'Aktif',              'value'=>$activeTypes,     'sub'=>'Bisa digunakan klien',   'color'=>'var(--apple-green)'],
                ['label'=>'Total Permohonan',   'value'=>$totalApplications,'sub'=>'Menggunakan jenis ini', 'color'=>'var(--apple-blue)'],
                ['label'=>'Harga Rata-rata',    'value'=>$avgPrice ? 'Rp '.number_format($avgPrice/1000,0).'K' : 'N/A', 'sub'=>'Base price estimasi', 'color'=>'var(--apple-orange)'],
            ];
        @endphp
        @foreach($typeStats as $s)
        <div style="background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:12px;padding:14px 16px">
            <p style="font-size:0.6rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:{{ $s['color'] }};margin:0;opacity:.85">{{ $s['label'] }}</p>
            <p style="font-size:1.5rem;font-weight:800;color:{{ $s['color'] }};margin:4px 0 2px;line-height:1">{{ $s['value'] }}</p>
            <p style="font-size:0.68rem;color:var(--dark-text-secondary);margin:0">{{ $s['sub'] }}</p>
        </div>
        @endforeach
    </div>

    {{-- Header + Search --}}
    <div style="background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:14px;padding:16px 20px">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:14px">
            <div>
                <p style="font-size:0.6rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--dark-text-secondary);margin:0">Manajemen</p>
                <h3 style="font-size:0.88rem;font-weight:700;color:var(--dark-text-primary);margin:3px 0 0">Katalog Jenis Izin</h3>
            </div>
            <a href="{{ route('permit-types.create') }}" style="display:inline-flex;align-items:center;gap:6px;padding:8px 16px;border-radius:8px;background:var(--apple-blue);color:#fff;font-size:0.82rem;font-weight:600;text-decoration:none">
                <i class="fas fa-plus" style="font-size:0.7rem"></i>Tambah Jenis Izin
            </a>
        </div>
        <form method="GET" action="{{ route('admin.permits.index') }}">
            <input type="hidden" name="tab" value="types">
            <div style="display:grid;grid-template-columns:2fr 1fr auto;gap:10px;align-items:end">
                <div>
                    <label style="font-size:0.68rem;font-weight:600;color:var(--dark-text-secondary);display:block;margin-bottom:4px">Nama Jenis Izin</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama jenis izin..."
                           style="background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);color:var(--dark-text-primary);border-radius:8px;padding:8px 12px;font-size:0.85rem;width:100%;outline:none"
                           onfocus="this.style.borderColor='var(--apple-blue)'" onblur="this.style.borderColor='var(--dark-separator)'">
                </div>
                <div>
                    <label style="font-size:0.68rem;font-weight:600;color:var(--dark-text-secondary);display:block;margin-bottom:4px">Status</label>
                    <select name="is_active" style="background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);color:var(--dark-text-primary);border-radius:8px;padding:8px 12px;font-size:0.85rem;width:100%;outline:none">
                        <option value="">Semua Status</option>
                        <option value="1" {{ request('is_active') === '1' ? 'selected' : '' }}>Aktif</option>
                        <option value="0" {{ request('is_active') === '0' ? 'selected' : '' }}>Nonaktif</option>
                    </select>
                </div>
                <div style="display:flex;gap:6px">
                    <button type="submit" style="display:inline-flex;align-items:center;justify-content:center;gap:5px;padding:8px 16px;border-radius:8px;background:var(--apple-blue);color:#fff;font-size:0.82rem;font-weight:600;border:none;cursor:pointer">
                        <i class="fas fa-filter" style="font-size:0.7rem"></i>Filter
                    </button>
                    <a href="{{ route('admin.permits.index', ['tab' => 'types']) }}" style="display:inline-flex;align-items:center;justify-content:center;width:36px;height:36px;border-radius:8px;background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);color:var(--dark-text-secondary);text-decoration:none">
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
                        @foreach(['Nama','Estimasi Biaya','Permohonan','Status','Aksi'] as $col)
                        <th style="padding:10px 14px;font-size:0.6rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--dark-text-secondary);text-align:{{ in_array($col,['Permohonan','Status','Aksi']) ? 'center' : 'left' }};border-bottom:1px solid var(--dark-separator);white-space:nowrap">{{ $col }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @forelse($permitTypes as $type)
                    <tr style="border-bottom:1px solid var(--dark-separator)" onmouseover="this.style.background='var(--dark-bg-secondary)'" onmouseout="this.style.background='transparent'">
                        <td style="padding:12px 14px">
                            <span style="font-size:0.85rem;font-weight:600;color:var(--dark-text-primary);display:block">{{ $type->name }}</span>
                            @if($type->description)
                            <span style="font-size:0.72rem;color:var(--dark-text-secondary);display:block;margin-top:2px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;max-width:280px">{{ Str::limit($type->description, 70) }}</span>
                            @endif
                        </td>
                        <td style="padding:12px 14px;font-size:0.82rem;color:var(--dark-text-secondary)">
                            @if($type->estimated_cost_min && $type->estimated_cost_max)
                                Rp {{ number_format($type->estimated_cost_min,0,',','.') }} – {{ number_format($type->estimated_cost_max,0,',','.') }}
                            @elseif($type->estimated_cost_min)
                                Rp {{ number_format($type->estimated_cost_min,0,',','.') }}
                            @else
                                <span style="color:var(--dark-text-secondary);opacity:.5">Tidak tersedia</span>
                            @endif
                        </td>
                        <td style="padding:12px 14px;text-align:center">
                            <span style="font-size:0.9rem;font-weight:700;color:var(--dark-text-primary)">{{ $type->applications_count }}</span>
                        </td>
                        <td style="padding:12px 14px;text-align:center;white-space:nowrap">
                            @if($type->is_active)
                            <span style="display:inline-flex;align-items:center;padding:3px 10px;border-radius:20px;font-size:0.68rem;font-weight:600;background:color-mix(in srgb,var(--apple-green) 15%,transparent);color:var(--apple-green)">Aktif</span>
                            @else
                            <span style="display:inline-flex;align-items:center;padding:3px 10px;border-radius:20px;font-size:0.68rem;font-weight:600;background:var(--dark-bg-secondary);color:var(--dark-text-secondary)">Nonaktif</span>
                            @endif
                        </td>
                        <td style="padding:12px 14px;text-align:center;white-space:nowrap">
                            <div style="display:flex;align-items:center;justify-content:center;gap:6px">
                                <a href="{{ route('permit-types.show', $type) }}" style="display:inline-flex;align-items:center;justify-content:center;width:30px;height:30px;border-radius:8px;background:color-mix(in srgb,var(--apple-teal) 15%,transparent);color:var(--apple-teal);text-decoration:none;border:1px solid color-mix(in srgb,var(--apple-teal) 30%,transparent)" title="Detail" onmouseover="this.style.opacity=.7" onmouseout="this.style.opacity=1"><i class="fas fa-eye" style="font-size:0.7rem"></i></a>
                                <a href="{{ route('permit-types.edit', $type) }}" style="display:inline-flex;align-items:center;justify-content:center;width:30px;height:30px;border-radius:8px;background:color-mix(in srgb,var(--apple-orange) 15%,transparent);color:var(--apple-orange);text-decoration:none;border:1px solid color-mix(in srgb,var(--apple-orange) 30%,transparent)" title="Edit" onmouseover="this.style.opacity=.7" onmouseout="this.style.opacity=1"><i class="fas fa-edit" style="font-size:0.7rem"></i></a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" style="padding:48px;text-align:center">
                            <i class="fas fa-certificate" style="font-size:2rem;color:var(--dark-text-secondary);opacity:.4;display:block;margin-bottom:12px"></i>
                            <p style="font-size:0.88rem;font-weight:600;color:var(--dark-text-primary);margin:0 0 4px">Belum Ada Jenis Izin</p>
                            <p style="font-size:0.78rem;color:var(--dark-text-secondary);margin:0 0 14px">Tambahkan jenis izin untuk memulai</p>
                            <a href="{{ route('permit-types.create') }}" style="display:inline-flex;align-items:center;gap:6px;padding:8px 18px;border-radius:8px;background:var(--apple-blue);color:#fff;font-size:0.82rem;font-weight:600;text-decoration:none"><i class="fas fa-plus" style="font-size:0.7rem"></i>Tambah Jenis Izin Pertama</a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Pagination --}}
    @if($permitTypes->hasPages())
    <div style="padding:14px 20px;background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:12px">
        <x-ui.pagination :paginator="$permitTypes->appends(array_merge(request()->all(), ['tab'=>'types']))" variant="full" :show-info="true" />
    </div>
    @endif

</div>
