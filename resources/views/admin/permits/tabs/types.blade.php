{{-- Permit Types Tab Content --}}
<div class="space-y-5">
    {{-- Quick Stats --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
        <div class="card-elevated rounded-apple-lg p-4 space-y-2">
            <p class="text-xs uppercase tracking-widest" style="color: rgba(235,235,245,0.6);">Total Jenis</p>
            <p class="text-3xl font-bold text-white">{{ $totalTypes }}</p>
            <p class="text-xs" style="color: rgba(235,235,245,0.6);">Izin terdaftar</p>
        </div>
        <div class="card-elevated rounded-apple-lg p-4 space-y-2">
            <p class="text-xs uppercase tracking-widest" style="color: rgba(52,199,89,0.9);">Aktif</p>
            <p class="text-3xl font-bold" style="color: rgba(52,199,89,1);">{{ $activeTypes }}</p>
            <p class="text-xs" style="color: rgba(235,235,245,0.6);">Bisa digunakan klien</p>
        </div>
        <div class="card-elevated rounded-apple-lg p-4 space-y-2">
            <p class="text-xs uppercase tracking-widest" style="color: rgba(10,132,255,0.9);">Total Permohonan</p>
            <p class="text-3xl font-bold" style="color: rgba(10,132,255,1);">{{ $totalApplications }}</p>
            <p class="text-xs" style="color: rgba(235,235,245,0.6);">Menggunakan jenis ini</p>
        </div>
        <div class="card-elevated rounded-apple-lg p-4 space-y-2">
            <p class="text-xs uppercase tracking-widest" style="color: rgba(255,159,10,0.9);">Harga Rata-rata</p>
            <p class="text-3xl font-bold" style="color: rgba(255,159,10,1);">
                {{ $avgPrice ? 'Rp '.number_format($avgPrice/1000, 0).'K' : 'N/A' }}
            </p>
            <p class="text-xs" style="color: rgba(235,235,245,0.6);">Base price</p>
        </div>
    </div>

    {{-- Actions --}}
    <div class="flex items-center justify-between gap-3">
        <div>
            <p class="text-xs uppercase tracking-[0.35em]" style="color: rgba(235,235,245,0.5);">Manajemen</p>
            <h2 class="text-lg font-semibold text-white">Katalog Jenis Izin</h2>
        </div>
        <a href="{{ route('permit-types.create') }}" class="btn-primary-sm">
            <i class="fas fa-plus mr-2"></i>Tambah Jenis Izin
        </a>
    </div>

    {{-- Search & Filter --}}
    <div class="card-elevated rounded-apple-lg p-4">
        <form method="GET" action="{{ route('admin.permits.index') }}" class="space-y-3" data-auto-submit>
            <input type="hidden" name="tab" value="types">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                <div>
                    <label class="block text-xs font-semibold mb-1.5" style="color: rgba(235, 235, 245, 0.65);">Pencarian</label>
                    <input type="text" name="search" value="{{ request('search') }}" 
                           placeholder="Nama jenis izin..." 
                           class="input-dark w-full px-3 py-2 rounded-apple text-sm">
                </div>
                
                <div>
                    <label class="block text-xs font-semibold mb-1.5" style="color: rgba(235, 235, 245, 0.65);">Status</label>
                    <select name="is_active" class="input-dark w-full px-3 py-2 rounded-apple text-sm">
                        <option value="">Semua Status</option>
                        <option value="1" {{ request('is_active') === '1' ? 'selected' : '' }}>Aktif</option>
                        <option value="0" {{ request('is_active') === '0' ? 'selected' : '' }}>Nonaktif</option>
                    </select>
                </div>
                
                <div class="flex items-end gap-2">
                    <button type="submit" class="btn-primary-sm flex-1">
                        <i class="fas fa-filter"></i>
                    </button>
                    <a href="{{ route('admin.permits.index', ['tab' => 'types']) }}" class="btn-secondary-sm flex-1">
                        <i class="fas fa-times"></i>
                    </a>
                </div>
            </div>
        </form>
    </div>

    {{-- Types Table --}}
    <div class="card-elevated rounded-apple-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-700 text-sm">
                <thead style="background-color: rgba(28,28,30,0.45);">
                    <tr>
                        <th scope="col" class="px-4 py-2.5 text-left text-[11px] font-semibold uppercase tracking-[0.2em] text-dark-text-secondary">Nama</th>
                        <th scope="col" class="px-4 py-2.5 text-left text-[11px] font-semibold uppercase tracking-[0.2em] text-dark-text-secondary">Estimasi Biaya</th>
                        <th scope="col" class="px-4 py-2.5 text-center text-[11px] font-semibold uppercase tracking-[0.2em] text-dark-text-secondary">Permohonan</th>
                        <th scope="col" class="px-4 py-2.5 text-center text-[11px] font-semibold uppercase tracking-[0.2em] text-dark-text-secondary">Status</th>
                        <th scope="col" class="px-4 py-2.5 text-center text-[11px] font-semibold uppercase tracking-[0.2em] text-dark-text-secondary">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-700" style="background-color: var(--dark-bg-secondary);">
                    @forelse($permitTypes as $type)
                        <tr class="hover-lift transition-apple">
                            <td class="px-4 py-2.5">
                                <div class="text-sm font-semibold text-dark-text-primary">{{ $type->name }}</div>
                                @if($type->description)
                                    <div class="text-xs text-dark-text-secondary mt-1 line-clamp-1">
                                        {{ Str::limit($type->description, 60) }}
                                    </div>
                                @endif
                            </td>
                            <td class="px-4 py-2.5 text-sm text-dark-text-primary">
                                @if($type->estimated_cost_min && $type->estimated_cost_max)
                                    Rp {{ number_format($type->estimated_cost_min, 0, ',', '.') }} - Rp {{ number_format($type->estimated_cost_max, 0, ',', '.') }}
                                @elseif($type->estimated_cost_min)
                                    Rp {{ number_format($type->estimated_cost_min, 0, ',', '.') }}
                                @else
                                    <span class="text-dark-text-tertiary">Tidak tersedia</span>
                                @endif
                            </td>
                            <td class="px-4 py-2.5 text-center">
                                <span class="text-sm font-semibold text-white">{{ $type->applications_count }}</span>
                            </td>
                            <td class="px-4 py-2.5 text-center whitespace-nowrap">
                                @if($type->is_active)
                                    <span class="px-2 py-1 text-xs font-medium rounded-apple"
                                          style="background-color: rgba(52,199,89,0.15); color: rgba(52,199,89,1);">
                                        Aktif
                                    </span>
                                @else
                                    <span class="px-2 py-1 text-xs font-medium rounded-apple"
                                          style="background-color: rgba(142,142,147,0.15); color: rgba(142,142,147,1);">
                                        Nonaktif
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-2.5 text-center whitespace-nowrap">
                                <div class="flex items-center justify-center space-x-1.5">
                                    <a href="{{ route('permit-types.show', $type) }}" 
                                       class="inline-flex items-center px-2.5 py-1 rounded-apple text-xs font-semibold transition-apple" 
                                       style="background-color: rgba(90, 200, 250, 0.15); color: var(--apple-teal); border: 1px solid rgba(90, 200, 250, 0.25);">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('permit-types.edit', $type) }}" 
                                       class="inline-flex items-center px-2.5 py-1 rounded-apple text-xs font-semibold transition-apple" 
                                       style="background-color: rgba(255, 149, 0, 0.15); color: var(--apple-orange); border: 1px solid rgba(255, 149, 0, 0.25);">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-16 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <i class="fas fa-certificate text-6xl mb-6" style="color: rgba(235, 235, 245, 0.3);"></i>
                                    <h3 class="text-xl font-semibold mb-2" style="color: #FFFFFF;">Belum Ada Jenis Izin</h3>
                                    <p class="mb-6" style="color: rgba(235, 235, 245, 0.6);">
                                        Tambahkan jenis izin untuk memulai
                                    </p>
                                    <a href="{{ route('permit-types.create') }}" 
                                       class="btn-primary inline-flex items-center px-6 py-3 rounded-apple font-medium">
                                        <i class="fas fa-plus mr-2"></i>
                                        Tambah Jenis Izin Pertama
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Pagination --}}
    @if($permitTypes->hasPages())
        <div class="rounded-apple-lg px-4 py-3" style="background-color: #2C2C2E; border: 1px solid rgba(84, 84, 88, 0.65); box-shadow: 0 2px 8px rgba(0, 0, 0, 0.48);">
            {{ $permitTypes->appends(['tab' => 'types'])->links('pagination::tailwind') }}
        </div>
    @endif
</div>
