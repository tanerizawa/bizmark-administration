@extends('layouts.app')

@section('title', 'Jenis Izin')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold" style="color: #FFFFFF;">Jenis Izin</h1>
            <p class="mt-1" style="color: rgba(235, 235, 245, 0.6);">Master data jenis izin dan perizinan</p>
        </div>
        <a href="{{ route('permit-types.create') }}" 
           class="px-4 py-2 rounded-lg font-medium transition-colors" 
           style="background: rgba(10, 132, 255, 0.9); color: #FFFFFF;">
            <i class="fas fa-plus mr-2"></i>Tambah Jenis Izin
        </a>
    </div>

    @if(session('success'))
    <div class="mb-6 p-4 rounded-lg" style="background: rgba(52, 199, 89, 0.1); border: 1px solid rgba(52, 199, 89, 0.3);">
        <p style="color: rgba(52, 199, 89, 1);">
            <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
        </p>
    </div>
    @endif

    @if(session('error'))
    <div class="mb-6 p-4 rounded-lg" style="background: rgba(255, 59, 48, 0.1); border: 1px solid rgba(255, 59, 48, 0.3);">
        <p style="color: rgba(255, 59, 48, 1);">
            <i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}
        </p>
    </div>
    @endif

    <!-- Filters -->
    <div class="card-elevated rounded-apple-lg p-6 mb-6">
        <form method="GET" action="{{ route('permit-types.index') }}" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <!-- Search -->
                <div>
                    <label class="block text-sm font-medium mb-2" style="color: rgba(235, 235, 245, 0.6);">Cari</label>
                    <input type="text" 
                           name="search" 
                           value="{{ request('search') }}"
                           placeholder="Nama atau kode izin..." 
                           class="input-dark w-full px-3 py-2 rounded-md">
                </div>

                <!-- Category Filter -->
                <div>
                    <label class="block text-sm font-medium mb-2" style="color: rgba(235, 235, 245, 0.6);">Kategori</label>
                    <select name="category" class="input-dark w-full px-3 py-2 rounded-md">
                        <option value="">Semua Kategori</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat }}" {{ request('category') == $cat ? 'selected' : '' }}>
                                {{ ucfirst($cat) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Institution Filter -->
                <div>
                    <label class="block text-sm font-medium mb-2" style="color: rgba(235, 235, 245, 0.6);">Institusi</label>
                    <select name="institution" class="input-dark w-full px-3 py-2 rounded-md">
                        <option value="">Semua Institusi</option>
                        @foreach($institutions as $inst)
                            <option value="{{ $inst->id }}" {{ request('institution') == $inst->id ? 'selected' : '' }}>
                                {{ $inst->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Status Filter -->
                <div>
                    <label class="block text-sm font-medium mb-2" style="color: rgba(235, 235, 245, 0.6);">Status</label>
                    <select name="status" class="input-dark w-full px-3 py-2 rounded-md">
                        <option value="">Semua Status</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Aktif</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Tidak Aktif</option>
                    </select>
                </div>
            </div>

            <div class="flex justify-end space-x-3">
                <a href="{{ route('permit-types.index') }}" 
                   class="px-4 py-2 rounded-md" 
                   style="background: rgba(142, 142, 147, 0.3); color: rgba(235, 235, 245, 0.8);">
                    Reset
                </a>
                <button type="submit" 
                        class="px-4 py-2 rounded-md" 
                        style="background: rgba(10, 132, 255, 0.9); color: #FFFFFF;">
                    <i class="fas fa-search mr-2"></i>Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="card-elevated rounded-apple-lg p-4">
            <p class="text-sm" style="color: rgba(235, 235, 245, 0.6);">Total Jenis Izin</p>
            <p class="text-2xl font-bold mt-1" style="color: #FFFFFF;">{{ $permitTypes->total() }}</p>
        </div>
        <div class="card-elevated rounded-apple-lg p-4">
            <p class="text-sm" style="color: rgba(235, 235, 245, 0.6);">Environmental</p>
            <p class="text-2xl font-bold mt-1" style="color: rgba(52, 199, 89, 1);">
                {{ \App\Models\PermitType::where('category', 'environmental')->count() }}
            </p>
        </div>
        <div class="card-elevated rounded-apple-lg p-4">
            <p class="text-sm" style="color: rgba(235, 235, 245, 0.6);">Building</p>
            <p class="text-2xl font-bold mt-1" style="color: rgba(10, 132, 255, 1);">
                {{ \App\Models\PermitType::where('category', 'building')->count() }}
            </p>
        </div>
        <div class="card-elevated rounded-apple-lg p-4">
            <p class="text-sm" style="color: rgba(235, 235, 245, 0.6);">Business</p>
            <p class="text-2xl font-bold mt-1" style="color: rgba(255, 149, 0, 1);">
                {{ \App\Models\PermitType::where('category', 'business')->count() }}
            </p>
        </div>
    </div>

    <!-- Permit Types List -->
    <div class="card-elevated rounded-apple-lg overflow-hidden">
        @if($permitTypes->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y" style="border-color: rgba(58, 58, 60, 0.8);">
                    <thead style="background: rgba(58, 58, 60, 0.6);">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider" style="color: rgba(235, 235, 245, 0.6);">
                                Nama Izin
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider" style="color: rgba(235, 235, 245, 0.6);">
                                Kategori
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider" style="color: rgba(235, 235, 245, 0.6);">
                                Institusi
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider" style="color: rgba(235, 235, 245, 0.6);">
                                Waktu & Biaya
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider" style="color: rgba(235, 235, 245, 0.6);">
                                Status
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider" style="color: rgba(235, 235, 245, 0.6);">
                                Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y" style="border-color: rgba(58, 58, 60, 0.8);">
                        @foreach($permitTypes as $permit)
                        <tr class="group hover:bg-opacity-70 transition-colors cursor-pointer" 
                            style="background: rgba(44, 44, 46, 0.3);"
                            onclick="toggleDescription('desc-{{ $permit->id }}')">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <!-- Category Icon -->
                                    <div class="flex-shrink-0 w-10 h-10 rounded-lg flex items-center justify-center"
                                         style="background: {{ 
                                            $permit->category === 'environmental' ? 'rgba(52, 199, 89, 0.2)' : 
                                            ($permit->category === 'building' ? 'rgba(10, 132, 255, 0.2)' : 
                                            ($permit->category === 'business' ? 'rgba(255, 149, 0, 0.2)' : 
                                            ($permit->category === 'land' ? 'rgba(175, 82, 222, 0.2)' : 
                                            'rgba(142, 142, 147, 0.2)'))) }};">
                                        <i class="fas {{ 
                                            $permit->category === 'environmental' ? 'fa-leaf' : 
                                            ($permit->category === 'building' ? 'fa-building' : 
                                            ($permit->category === 'business' ? 'fa-briefcase' : 
                                            ($permit->category === 'land' ? 'fa-map' : 
                                            ($permit->category === 'transportation' ? 'fa-truck' : 'fa-file-alt')))) }}" 
                                           style="color: {{ 
                                            $permit->category === 'environmental' ? 'rgba(52, 199, 89, 1)' : 
                                            ($permit->category === 'building' ? 'rgba(10, 132, 255, 1)' : 
                                            ($permit->category === 'business' ? 'rgba(255, 149, 0, 1)' : 
                                            ($permit->category === 'land' ? 'rgba(175, 82, 222, 1)' : 
                                            'rgba(142, 142, 147, 1)'))) }};"></i>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center gap-2">
                                            <div class="text-sm font-medium" style="color: #FFFFFF;">
                                                {{ $permit->name }}
                                            </div>
                                            @if($permit->description)
                                                <button type="button" 
                                                        class="text-xs opacity-0 group-hover:opacity-100 transition-opacity"
                                                        style="color: rgba(10, 132, 255, 1);"
                                                        title="Klik baris untuk melihat detail">
                                                    <i class="fas fa-info-circle"></i>
                                                </button>
                                            @endif
                                        </div>
                                        <div class="text-xs mt-0.5" style="color: rgba(235, 235, 245, 0.6);">
                                            {{ $permit->code }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full"
                                      style="background: {{ 
                                            $permit->category === 'environmental' ? 'rgba(52, 199, 89, 0.2)' : 
                                            ($permit->category === 'building' ? 'rgba(10, 132, 255, 0.2)' : 
                                            ($permit->category === 'business' ? 'rgba(255, 149, 0, 0.2)' : 
                                            ($permit->category === 'land' ? 'rgba(175, 82, 222, 0.2)' : 
                                            'rgba(142, 142, 147, 0.2)'))) }}; 
                                             color: {{ 
                                            $permit->category === 'environmental' ? 'rgba(52, 199, 89, 1)' : 
                                            ($permit->category === 'building' ? 'rgba(10, 132, 255, 1)' : 
                                            ($permit->category === 'business' ? 'rgba(255, 149, 0, 1)' : 
                                            ($permit->category === 'land' ? 'rgba(175, 82, 222, 1)' : 
                                            'rgba(142, 142, 147, 1)'))) }};">
                                    {{ ucfirst($permit->category) }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm" style="color: rgba(235, 235, 245, 0.9);">
                                    {{ $permit->institution?->name ?? '-' }}
                                </div>
                                @if($permit->institution)
                                    <div class="text-xs mt-0.5" style="color: rgba(235, 235, 245, 0.5);">
                                        {{ $permit->institution->type }}
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="space-y-1">
                                    @if($permit->avg_processing_days)
                                        <div class="flex items-center gap-1.5 text-sm" style="color: rgba(235, 235, 245, 0.9);">
                                            <i class="fas fa-clock text-xs" style="color: rgba(10, 132, 255, 1);"></i>
                                            <span>{{ $permit->avg_processing_days }} hari</span>
                                        </div>
                                    @else
                                        <div class="text-xs" style="color: rgba(142, 142, 147, 1);">
                                            <i class="fas fa-clock"></i> Belum ditentukan
                                        </div>
                                    @endif
                                    @if($permit->estimated_cost_min && $permit->estimated_cost_max)
                                        <div class="flex items-center gap-1.5 text-xs" style="color: rgba(235, 235, 245, 0.6);">
                                            <i class="fas fa-money-bill-wave text-xs" style="color: rgba(52, 199, 89, 1);"></i>
                                            <span>{{ $permit->estimated_cost_range }}</span>
                                        </div>
                                    @else
                                        <div class="text-xs" style="color: rgba(142, 142, 147, 1);">
                                            <i class="fas fa-money-bill-wave"></i> Belum ditentukan
                                        </div>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                @if($permit->is_active)
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full"
                                          style="background: rgba(52, 199, 89, 0.2); color: rgba(52, 199, 89, 1);">
                                        Aktif
                                    </span>
                                @else
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full"
                                          style="background: rgba(142, 142, 147, 0.2); color: rgba(142, 142, 147, 1);">
                                        Tidak Aktif
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right text-sm font-medium">
                                <div class="flex items-center justify-end space-x-2" onclick="event.stopPropagation();">
                                    <a href="{{ route('permit-types.show', $permit) }}" 
                                       class="p-2 rounded-lg transition-colors"
                                       style="color: rgba(10, 132, 255, 1);"
                                       onmouseover="this.style.background='rgba(10, 132, 255, 0.1)'"
                                       onmouseout="this.style.background='transparent'"
                                       title="Lihat Detail">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('permit-types.edit', $permit) }}" 
                                       class="p-2 rounded-lg transition-colors"
                                       style="color: rgba(255, 149, 0, 1);"
                                       onmouseover="this.style.background='rgba(255, 149, 0, 0.1)'"
                                       onmouseout="this.style.background='transparent'"
                                       title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('permit-types.destroy', $permit) }}" 
                                          method="POST" 
                                          class="inline"
                                          onsubmit="return confirm('Yakin ingin menghapus jenis izin ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="p-2 rounded-lg transition-colors"
                                                style="color: rgba(255, 59, 48, 1);"
                                                onmouseover="this.style.background='rgba(255, 59, 48, 0.1)'"
                                                onmouseout="this.style.background='transparent'"
                                                title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        
                        <!-- Description Row (Expandable) -->
                        @if($permit->description)
                        <tr id="desc-{{ $permit->id }}" class="hidden transition-all" style="background: rgba(58, 58, 60, 0.4);">
                            <td colspan="6" class="px-6 py-4">
                                <div class="flex gap-3">
                                    <div class="flex-shrink-0 pt-1">
                                        <i class="fas fa-info-circle" style="color: rgba(10, 132, 255, 1);"></i>
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-sm font-medium mb-2" style="color: rgba(10, 132, 255, 1);">
                                            Deskripsi & Informasi:
                                        </p>
                                        <p class="text-sm leading-relaxed" style="color: rgba(235, 235, 245, 0.8);">
                                            {{ $permit->description }}
                                        </p>
                                        @if($permit->required_documents && count($permit->required_documents) > 0)
                                            <div class="mt-3">
                                                <p class="text-xs font-semibold mb-2" style="color: rgba(235, 235, 245, 0.7);">
                                                    <i class="fas fa-paperclip mr-1"></i>Dokumen yang Diperlukan:
                                                </p>
                                                <ul class="list-disc list-inside space-y-1 text-xs" style="color: rgba(235, 235, 245, 0.6);">
                                                    @foreach($permit->required_documents as $doc)
                                                        <li>{{ $doc }}</li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @endif
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="px-6 py-4" style="background: rgba(58, 58, 60, 0.3); border-top: 1px solid rgba(58, 58, 60, 0.8);">
                {{ $permitTypes->links() }}
            </div>
        @else
            <div class="text-center py-12">
                <i class="fas fa-file-alt text-6xl mb-4" style="color: rgba(235, 235, 245, 0.3);"></i>
                <p class="text-lg" style="color: rgba(235, 235, 245, 0.6);">
                    Tidak ada data jenis izin
                </p>
                <a href="{{ route('permit-types.create') }}" 
                   class="inline-block mt-4 px-6 py-2 rounded-lg" 
                   style="background: rgba(10, 132, 255, 0.9); color: #FFFFFF;">
                    <i class="fas fa-plus mr-2"></i>Tambah Jenis Izin Pertama
                </a>
            </div>
        @endif
    </div>
</div>

<script>
function toggleDescription(id) {
    const descRow = document.getElementById(id);
    if (descRow) {
        descRow.classList.toggle('hidden');
    }
}
</script>
@endsection
