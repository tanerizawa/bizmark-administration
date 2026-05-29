{{-- KBLI Tab --}}
<div x-data="{ importModalOpen: false }" style="display:flex;flex-direction:column;gap:16px">

    {{-- Header + Actions --}}
    <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px">
        <div>
            <p style="font-size:0.6rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--dark-text-secondary);margin:0">Referensi</p>
            <h3 style="font-size:0.95rem;font-weight:700;color:var(--dark-text-primary);margin:3px 0 2px">Data KBLI</h3>
            <p style="font-size:0.78rem;color:var(--dark-text-secondary);margin:0">Klasifikasi Baku Lapangan Usaha Indonesia (KBLI 2020)</p>
        </div>
        <div style="display:flex;gap:8px;flex-wrap:wrap">
            <a href="{{ route('admin.settings.kbli.template') }}" style="display:inline-flex;align-items:center;gap:6px;padding:7px 14px;border-radius:8px;background:color-mix(in srgb,var(--apple-blue) 18%,var(--dark-bg-tertiary));color:var(--apple-blue);font-size:0.8rem;font-weight:600;text-decoration:none;border:1px solid color-mix(in srgb,var(--apple-blue) 30%,var(--dark-separator))">
                <i class="fas fa-download" style="font-size:0.7rem"></i>Template CSV
            </a>
            <button @click="importModalOpen = true" style="display:inline-flex;align-items:center;gap:6px;padding:7px 14px;border-radius:8px;background:color-mix(in srgb,var(--apple-green) 18%,var(--dark-bg-tertiary));color:var(--apple-green);font-size:0.8rem;font-weight:600;cursor:pointer;border:1px solid color-mix(in srgb,var(--apple-green) 30%,var(--dark-separator))">
                <i class="fas fa-file-import" style="font-size:0.7rem"></i>Import KBLI
            </button>
            <a href="{{ route('admin.settings.kbli.export') }}" style="display:inline-flex;align-items:center;gap:6px;padding:7px 14px;border-radius:8px;background:color-mix(in srgb,var(--apple-purple) 18%,var(--dark-bg-tertiary));color:var(--apple-purple);font-size:0.8rem;font-weight:600;text-decoration:none;border:1px solid color-mix(in srgb,var(--apple-purple) 30%,var(--dark-separator))">
                <i class="fas fa-file-csv" style="font-size:0.7rem"></i>Export CSV
            </a>
            <form action="{{ route('admin.settings.kbli.clear') }}" method="POST" x-data
                  @submit.prevent="if(confirm('Hapus semua data KBLI? Tindakan ini tidak dapat dibatalkan.')) $el.submit()">
                @csrf
                @method('DELETE')
                <button type="submit" style="display:inline-flex;align-items:center;gap:6px;padding:7px 14px;border-radius:8px;background:color-mix(in srgb,var(--apple-red) 18%,var(--dark-bg-tertiary));color:var(--apple-red);font-size:0.8rem;font-weight:600;cursor:pointer;border:1px solid color-mix(in srgb,var(--apple-red) 30%,var(--dark-separator))">
                    <i class="fas fa-trash-alt" style="font-size:0.7rem"></i>Hapus Semua
                </button>
            </form>
        </div>
    </div>

    {{-- Stats --}}
    <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:10px">
        @php
            $kbliStatCards = [
                ['label'=>'Total KBLI',  'value'=>$kbliStats['total'] ?? 0,               'color'=>'var(--apple-blue)',   'icon'=>'fa-database'],
                ['label'=>'Sektor',      'value'=>count($kbliStats['by_sector'] ?? []),   'color'=>'var(--apple-green)',  'icon'=>'fa-layer-group'],
                ['label'=>'Versi KBLI',  'value'=>'2020',                                  'color'=>'var(--apple-orange)', 'icon'=>'fa-sync-alt'],
                ['label'=>'Status',      'value'=>'Aktif',                                 'color'=>'var(--apple-purple)', 'icon'=>'fa-check-circle'],
            ];
        @endphp
        @foreach($kbliStatCards as $s)
        <div style="background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:12px;padding:14px 16px;display:flex;align-items:center;gap:12px">
            <div style="width:36px;height:36px;border-radius:10px;flex-shrink:0;display:flex;align-items:center;justify-content:center;background:color-mix(in srgb,{{ $s['color'] }} 20%,transparent)">
                <i class="fas {{ $s['icon'] }}" style="color:{{ $s['color'] }};font-size:0.9rem"></i>
            </div>
            <div>
                <p style="font-size:1.25rem;font-weight:700;color:var(--dark-text-primary);margin:0;line-height:1">{{ $s['value'] }}</p>
                <p style="font-size:0.68rem;color:var(--dark-text-secondary);margin:3px 0 0">{{ $s['label'] }}</p>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Sector Chips --}}
    @if(isset($kbliStats['by_sector']) && count($kbliStats['by_sector']) > 0)
    <div style="background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:12px;padding:12px 16px;display:flex;flex-wrap:wrap;gap:6px">
        @foreach($kbliStats['by_sector'] as $sector)
        <span style="display:inline-flex;align-items:center;gap:5px;padding:4px 10px;border-radius:20px;font-size:0.72rem;font-weight:600;background:color-mix(in srgb,var(--apple-blue) 15%,var(--dark-bg-secondary));color:var(--apple-blue)">
            <strong>{{ $sector->sector }}</strong>
            <span style="opacity:.7">{{ $sector->count }}</span>
        </span>
        @endforeach
    </div>
    @endif

    {{-- Search Form --}}
    <div style="background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:14px;padding:16px 20px">
        <form action="{{ route('admin.permits.index') }}" method="GET">
            <input type="hidden" name="tab" value="kbli">
            <div style="display:flex;flex-wrap:wrap;gap:10px;align-items:end">
                <div style="flex:1;min-width:200px">
                    <label style="font-size:0.68rem;font-weight:600;color:var(--dark-text-secondary);display:block;margin-bottom:4px">Cari KBLI <span style="font-size:0.65rem;opacity:.6">(smart search)</span></label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="real estate, konstruksi, retail..."
                           style="background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);color:var(--dark-text-primary);border-radius:8px;padding:8px 12px;font-size:0.85rem;width:100%;outline:none"
                           onfocus="this.style.borderColor='var(--apple-blue)'" onblur="this.style.borderColor='var(--dark-separator)'">
                </div>
                <div style="width:160px">
                    <label style="font-size:0.68rem;font-weight:600;color:var(--dark-text-secondary);display:block;margin-bottom:4px">Sektor</label>
                    <select name="category" style="background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);color:var(--dark-text-primary);border-radius:8px;padding:8px 12px;font-size:0.85rem;width:100%;outline:none">
                        <option value="">Semua</option>
                        @foreach($categories ?? [] as $cat)
                        <option value="{{ $cat }}" {{ request('category') == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" style="display:inline-flex;align-items:center;gap:5px;padding:8px 16px;border-radius:8px;background:var(--apple-blue);color:#fff;font-size:0.82rem;font-weight:600;border:none;cursor:pointer">
                    <i class="fas fa-search" style="font-size:0.7rem"></i>Cari
                </button>
                @if(request('search') || request('category'))
                <a href="{{ route('admin.permits.index', ['tab' => 'kbli']) }}" style="display:inline-flex;align-items:center;gap:5px;padding:8px 14px;border-radius:8px;background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);color:var(--dark-text-secondary);font-size:0.82rem;font-weight:600;text-decoration:none">
                    <i class="fas fa-times" style="font-size:0.7rem"></i>Reset
                </a>
                @endif
            </div>
        </form>
    </div>

    {{-- Results Info --}}
    @if((request('search') || request('category')) && isset($kbliData))
    <p style="font-size:0.78rem;color:var(--dark-text-secondary);margin:0">
        Menampilkan {{ $kbliData->total() }} hasil
        @if(request('search')) untuk "<strong style="color:var(--dark-text-primary)">{{ request('search') }}</strong>"@endif
        @if(request('category')) di sektor <strong style="color:var(--dark-text-primary)">{{ request('category') }}</strong>@endif
    </p>
    @endif

    {{-- KBLI Table --}}
    @if(isset($kbliData) && $kbliData->count() > 0)
    <div style="background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:14px;overflow:hidden">
        <div style="overflow-x:auto">
            <table style="width:100%;border-collapse:collapse">
                <thead style="background:var(--dark-bg-secondary)">
                    <tr>
                        @foreach(['Kode','Sektor','Deskripsi','Kegiatan'] as $col)
                        <th style="padding:10px 14px;font-size:0.6rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--dark-text-secondary);text-align:left;border-bottom:1px solid var(--dark-separator);white-space:nowrap">{{ $col }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach($kbliData as $kbli)
                    <tr style="border-bottom:1px solid var(--dark-separator)" onmouseover="this.style.background='var(--dark-bg-secondary)'" onmouseout="this.style.background='transparent'">
                        <td style="padding:11px 14px;white-space:nowrap">
                            <span style="font-family:monospace;font-size:0.8rem;font-weight:600;color:var(--apple-blue);background:color-mix(in srgb,var(--apple-blue) 15%,transparent);padding:2px 8px;border-radius:6px">{{ $kbli->code }}</span>
                        </td>
                        <td style="padding:11px 14px;white-space:nowrap">
                            <span style="font-size:0.78rem;font-weight:500;color:var(--apple-green);background:color-mix(in srgb,var(--apple-green) 15%,transparent);padding:2px 8px;border-radius:6px">{{ $kbli->sector }}</span>
                        </td>
                        <td style="padding:11px 14px;font-size:0.82rem;color:var(--dark-text-primary);max-width:300px">{{ $kbli->description }}</td>
                        <td style="padding:11px 14px;font-size:0.75rem;color:var(--dark-text-secondary);max-width:260px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap" title="{{ $kbli->activities }}">{{ Str::limit($kbli->activities, 100) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @if($kbliData->hasPages())
        <div style="padding:12px 16px;border-top:1px solid var(--dark-separator);background:var(--dark-bg-secondary)">
            {{ $kbliData->appends(['tab' => 'kbli'])->links() }}
        </div>
        @endif
    </div>
    @else
    <div style="background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:14px;padding:48px;text-align:center">
        <div style="width:48px;height:48px;border-radius:50%;background:color-mix(in srgb,var(--apple-blue) 15%,transparent);display:flex;align-items:center;justify-content:center;margin:0 auto 14px">
            <i class="fas fa-search" style="font-size:1.2rem;color:var(--apple-blue)"></i>
        </div>
        <p style="font-size:0.88rem;font-weight:600;color:var(--dark-text-primary);margin:0 0 6px">
            @if(request('search') || request('category'))
            Tidak Ada Hasil
            @else
            Belum Ada Data KBLI
            @endif
        </p>
        <p style="font-size:0.78rem;color:var(--dark-text-secondary);margin:0 0 16px">
            @if(request('search'))Tidak ditemukan KBLI untuk "{{ request('search') }}"@elseif(request('category'))Tidak ada KBLI di sektor {{ request('category') }}@else Import data KBLI terlebih dahulu.@endif
        </p>
        @if(request('search') || request('category'))
        <a href="{{ route('admin.permits.index', ['tab' => 'kbli']) }}" style="display:inline-flex;align-items:center;gap:6px;padding:8px 18px;border-radius:8px;background:color-mix(in srgb,var(--apple-blue) 20%,var(--dark-bg-secondary));color:var(--apple-blue);font-size:0.82rem;font-weight:600;text-decoration:none;border:1px solid color-mix(in srgb,var(--apple-blue) 30%,var(--dark-separator))">
            <i class="fas fa-undo" style="font-size:0.7rem"></i>Reset Pencarian
        </a>
        @else
        <button @click="importModalOpen = true" style="display:inline-flex;align-items:center;gap:6px;padding:8px 18px;border-radius:8px;background:color-mix(in srgb,var(--apple-green) 20%,var(--dark-bg-secondary));color:var(--apple-green);font-size:0.82rem;font-weight:600;cursor:pointer;border:1px solid color-mix(in srgb,var(--apple-green) 30%,var(--dark-separator))">
            <i class="fas fa-file-import" style="font-size:0.7rem"></i>Import KBLI
        </button>
        @endif
    </div>
    @endif

</div>

{{-- Import Modal --}}
<style>[x-cloak]{display:none!important}</style>
<div x-show="importModalOpen" x-cloak style="position:fixed;inset:0;z-index:50;display:flex;align-items:center;justify-content:center;padding:20px" aria-modal="true" role="dialog">
    <div style="position:fixed;inset:0;background:rgba(0,0,0,.7)" @click="importModalOpen = false"></div>
    <div style="position:relative;width:100%;max-width:480px;background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:18px;overflow:hidden;box-shadow:0 25px 60px rgba(0,0,0,.5)">
        <form action="{{ route('admin.settings.kbli.import') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div style="padding:20px 24px;border-bottom:1px solid var(--dark-separator)">
                <h3 style="font-size:0.95rem;font-weight:700;color:var(--dark-text-primary);margin:0">Import Data KBLI</h3>
            </div>
            <div style="padding:20px 24px;display:flex;flex-direction:column;gap:16px">
                <div>
                    <label style="font-size:0.75rem;font-weight:600;color:var(--dark-text-secondary);display:block;margin-bottom:6px">File CSV</label>
                    <input type="file" name="csv_file" accept=".csv,.txt" required
                           style="display:block;width:100%;font-size:0.82rem;color:var(--dark-text-primary)">
                    <p style="font-size:0.7rem;color:var(--dark-text-secondary);margin:5px 0 0">Format: CSV dengan kolom Kode, Judul, Kategori</p>
                </div>
                <div style="display:flex;align-items:center;gap:8px">
                    <input type="checkbox" name="clear_existing" id="clear_existing" value="1"
                           style="width:16px;height:16px;accent-color:var(--apple-blue);border-radius:4px;border:1px solid var(--dark-separator)">
                    <label for="clear_existing" style="font-size:0.82rem;color:var(--dark-text-secondary);cursor:pointer">Hapus data existing sebelum import</label>
                </div>
            </div>
            <div style="padding:14px 24px;background:var(--dark-bg-tertiary);display:flex;justify-content:flex-end;gap:8px;border-top:1px solid var(--dark-separator)">
                <button type="button" @click="importModalOpen = false" style="display:inline-flex;align-items:center;padding:7px 16px;border-radius:8px;background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);color:var(--dark-text-secondary);font-size:0.82rem;font-weight:600;cursor:pointer">Batal</button>
                <button type="submit" style="display:inline-flex;align-items:center;gap:6px;padding:7px 16px;border-radius:8px;background:var(--apple-green);color:#fff;font-size:0.82rem;font-weight:600;border:none;cursor:pointer"><i class="fas fa-upload" style="font-size:0.7rem"></i>Import</button>
            </div>
        </form>
    </div>
</div>
