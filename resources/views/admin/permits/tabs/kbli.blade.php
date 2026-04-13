{{-- KBLI Data Tab Content --}}
<div class="space-y-5">
    {{-- Header with Stats --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h3 class="text-base font-semibold text-white">Data KBLI</h3>
            <p class="text-sm" style="color: rgba(235,235,245,0.65);">
                Kelola Klasifikasi Baku Lapangan Usaha Indonesia (KBLI 2020)
            </p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('admin.settings.kbli.template') }}" 
               class="admin-btn admin-btn-sm rounded" style="background: rgba(10,132,255,0.25);">
                <i class="fas fa-download mr-1.5"></i>Template CSV
            </a>
            <button onclick="document.getElementById('import-modal').classList.remove('hidden')" 
                    class="admin-btn admin-btn-sm rounded" style="background: rgba(52,199,89,0.25);">
                <i class="fas fa-file-import mr-1.5"></i>Import KBLI
            </button>
            <form action="{{ route('admin.settings.kbli.clear') }}" method="POST" class="inline" 
                  onsubmit="return confirm('Hapus semua data KBLI? Tindakan ini tidak dapat dibatalkan.')">
                @csrf
                @method('DELETE')
                <button type="submit" class="admin-btn admin-btn-sm rounded" style="background: rgba(255,59,48,0.25);">
                    <i class="fas fa-trash-alt mr-1.5"></i>Hapus Semua
                </button>
            </form>
        </div>
    </div>

    {{-- Statistics Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
        <div class="card-elevated rounded-apple p-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg flex items-center justify-center" style="background: rgba(10,132,255,0.25);">
                    <i class="fas fa-database" style="color: var(--apple-blue);"></i>
                </div>
                <div>
                    <p class="text-lg font-bold text-white">{{ $kbliStats['total'] ?? 0 }}</p>
                    <p class="text-xs text-dark-text-secondary">Total KBLI</p>
                </div>
            </div>
        </div>
        <div class="card-elevated rounded-apple p-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg flex items-center justify-center" style="background: rgba(52,199,89,0.25);">
                    <i class="fas fa-layer-group" style="color: var(--apple-green);"></i>
                </div>
                <div>
                    <p class="text-lg font-bold text-white">{{ count($kbliStats['by_sector'] ?? []) }}</p>
                    <p class="text-xs text-dark-text-secondary">Sektor</p>
                </div>
            </div>
        </div>
        <div class="card-elevated rounded-apple p-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg flex items-center justify-center" style="background: rgba(255,149,0,0.25);">
                    <i class="fas fa-sync-alt" style="color: var(--apple-orange);"></i>
                </div>
                <div>
                    <p class="text-lg font-bold text-white">2020</p>
                    <p class="text-xs text-dark-text-secondary">Versi KBLI</p>
                </div>
            </div>
        </div>
        <div class="card-elevated rounded-apple p-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg flex items-center justify-center" style="background: rgba(175,82,222,0.25);">
                    <i class="fas fa-file-csv" style="color: var(--apple-purple);"></i>
                </div>
                <div>
                    <a href="{{ route('admin.settings.kbli.export') }}" class="text-lg font-bold text-white hover:underline">
                        Export <i class="fas fa-external-link-alt text-xs ml-1"></i>
                    </a>
                    <p class="text-xs text-dark-text-secondary">Download CSV</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Sector Distribution (Compact) --}}
    @if(isset($kbliStats['by_sector']) && count($kbliStats['by_sector']) > 0)
    <div class="card-elevated rounded-apple p-3">
        <div class="flex flex-wrap gap-2">
            @foreach($kbliStats['by_sector'] as $sector)
            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-apple text-xs font-medium" 
                  style="background: rgba(10,132,255,0.15); color: var(--apple-blue);">
                <span class="font-bold">{{ $sector->sector }}</span>
                <span class="opacity-70">{{ $sector->count }}</span>
            </span>
            @endforeach
        </div>
    </div>
    @endif
    
    {{-- Search Form --}}
    <div class="card-elevated rounded-apple p-4">
        <form action="{{ route('admin.permits.index') }}" method="GET" class="flex flex-wrap gap-3 items-end">
            <input type="hidden" name="tab" value="kbli">
            <div class="flex-1 min-w-[200px]">
                <label class="admin-label-compact block mb-1">Cari KBLI <span class="text-xs opacity-60">(smart search)</span></label>
                <input type="text" 
                       name="search"
                       value="{{ request('search') }}"
                       placeholder="real estate, konstruksi, retail..."
                       class="admin-input w-full rounded bg-dark-bg-secondary border border-dark-separator text-white">
            </div>
            <div class="w-32">
                <label class="admin-label-compact block mb-1">Sektor</label>
                <select name="category" class="admin-input admin-select w-full rounded bg-dark-bg-secondary border border-dark-separator text-white">
                    <option value="">Semua</option>
                    @foreach($categories ?? [] as $cat)
                        <option value="{{ $cat }}" {{ request('category') == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="admin-btn admin-btn-sm rounded" style="background: rgba(10,132,255,0.25);">
                <i class="fas fa-search mr-1.5"></i>Cari
            </button>
            @if(request('search') || request('category'))
            <a href="{{ route('admin.permits.index', ['tab' => 'kbli']) }}" class="admin-btn admin-btn-sm rounded" style="background: rgba(142,142,147,0.25);">
                <i class="fas fa-times"></i>
            </a>
            @endif
        </form>
    </div>
    
    {{-- KBLI Data Table --}}
    @if(isset($kbliData) && $kbliData->count() > 0)
    @if(request('search') || request('category'))
    <div class="text-sm text-dark-text-secondary">
        Menampilkan {{ $kbliData->total() }} hasil
        @if(request('search')) untuk "{{ request('search') }}"@endif
        @if(request('category')) di sektor {{ request('category') }}@endif
    </div>
    @endif
    <div class="card-elevated rounded-apple-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-700">
                <thead style="background-color: var(--dark-bg-secondary);">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-dark-text-secondary">Kode</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-dark-text-secondary">Sektor</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-dark-text-secondary">Deskripsi</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-dark-text-secondary">Kegiatan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-700" style="background-color: var(--dark-bg-secondary);">
                    @foreach($kbliData as $kbli)
                    <tr class="hover:bg-dark-bg-tertiary transition-apple">
                        <td class="px-4 py-3">
                            <span class="font-mono text-sm font-medium text-white bg-apple-blue/20 px-2 py-0.5 rounded">{{ $kbli->code }}</span>
                        </td>
                        <td class="px-4 py-3">
                            <span class="text-sm text-white px-2 py-0.5 rounded" style="background: rgba(52,199,89,0.2);">{{ $kbli->sector }}</span>
                        </td>
                        <td class="px-4 py-3">
                            <p class="text-sm text-white">{{ $kbli->description }}</p>
                        </td>
                        <td class="px-4 py-3">
                            <p class="text-xs text-dark-text-secondary line-clamp-2" title="{{ $kbli->activities }}">
                                {{ Str::limit($kbli->activities, 100) }}
                            </p>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        @if($kbliData->hasPages())
        <div class="px-4 py-3" style="background: var(--dark-bg-tertiary); border-top: 1px solid var(--dark-separator);">
            {{ $kbliData->appends(['tab' => 'kbli'])->links() }}
        </div>
        @endif
    </div>
    @else
    <div class="card-elevated rounded-apple-lg p-8 text-center">
        <div class="w-16 h-16 mx-auto mb-4 rounded-full flex items-center justify-center" style="background: rgba(10,132,255,0.15);">
            <i class="fas fa-search text-2xl" style="color: var(--apple-blue);"></i>
        </div>
        <h3 class="text-sm font-semibold text-white mb-2">Tidak Ada Hasil</h3>
        <p class="text-sm text-dark-text-secondary mb-4">
            @if(request('search'))
                Tidak ditemukan KBLI untuk "{{ request('search') }}"
                @if(request('category')) di sektor {{ request('category') }}@endif
            @elseif(request('category'))
                Tidak ada KBLI di sektor {{ request('category') }}
            @else
                Belum ada data KBLI. Import data terlebih dahulu.
            @endif
        </p>
        @if(request('search') || request('category'))
        <a href="{{ route('admin.permits.index', ['tab' => 'kbli']) }}" 
           class="admin-btn rounded px-4 py-2" style="background: rgba(10,132,255,0.25);">
            <i class="fas fa-undo mr-1.5"></i>Reset Pencarian
        </a>
        @else
        <button onclick="document.getElementById('import-modal').classList.remove('hidden')" 
                class="admin-btn rounded px-4 py-2" style="background: rgba(52,199,89,0.25);">
            <i class="fas fa-file-import mr-1.5"></i>Import KBLI
        </button>
        @endif
    </div>
    @endif
</div>

{{-- Import Modal --}}
<div id="import-modal" class="hidden fixed inset-0 z-50 overflow-y-auto" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
        <div class="fixed inset-0 bg-black/70 transition-opacity" onclick="document.getElementById('import-modal').classList.add('hidden')"></div>
        
        <div class="relative inline-block align-bottom rounded-apple-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full"
             style="background: var(--dark-bg-elevated);">
            <form action="{{ route('admin.settings.kbli.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="p-6">
                    <h3 class="text-sm font-semibold text-white mb-4">Import Data KBLI</h3>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm text-dark-text-secondary mb-2">File CSV</label>
                            <input type="file" name="csv_file" accept=".csv,.txt" required
                                   class="block w-full text-sm text-white file:mr-4 file:py-2 file:px-4 file:rounded-apple file:border-0 file:text-sm file:font-semibold file:bg-apple-blue file:text-white hover:file:bg-blue-600">
                            <p class="mt-1 text-xs text-dark-text-tertiary">Format: CSV dengan kolom Kode, Judul, Kategori</p>
                        </div>
                        
                        <div class="flex items-center gap-2">
                            <input type="checkbox" name="clear_existing" id="clear_existing" value="1"
                                   class="rounded border-gray-600 bg-dark-bg-tertiary text-apple-blue focus:ring-apple-blue">
                            <label for="clear_existing" class="text-sm text-dark-text-secondary">
                                Hapus data existing sebelum import
                            </label>
                        </div>
                    </div>
                </div>
                
                <div class="px-6 py-4 flex justify-end gap-2" style="background: var(--dark-bg-tertiary);">
                    <button type="button" onclick="document.getElementById('import-modal').classList.add('hidden')"
                            class="admin-btn admin-btn-sm rounded" style="background: rgba(142,142,147,0.25);">
                        Batal
                    </button>
                    <button type="submit" class="admin-btn admin-btn-sm rounded" style="background: rgba(52,199,89,0.25);">
                        <i class="fas fa-upload mr-1.5"></i>Import
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
