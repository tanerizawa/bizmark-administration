<div class="card-apple">
    {{-- Header with Search and Filters --}}
    <div class="p-4 md:p-5 border-b" style="border-color: var(--dark-separator);">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 md:gap-4 mb-4">
            <h2 class="text-lg font-semibold text-dark-text-primary">Data KBLI</h2>
            
            <div class="flex flex-col sm:flex-row gap-2.5 md:gap-3">
                {{-- Search Form --}}
                <form method="GET" action="{{ route('admin.master-data.index') }}" class="flex-1 sm:w-64">
                    <input type="hidden" name="tab" value="kbli">
                    @if(request('category'))
                        <input type="hidden" name="category" value="{{ request('category') }}">
                    @endif
                    <div class="relative">
                        <input type="text" 
                               name="search" 
                               value="{{ request('search') }}"
                               placeholder="Cari kode atau nama KBLI..."
                               class="input-apple">
                        <svg class="absolute left-3 top-2.5 w-5 h-5 text-dark-text-tertiary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                </form>
            </div>
        </div>

        {{-- Category Filter Pills --}}
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('admin.master-data.index', ['tab' => 'kbli'] + request()->except(['category', 'page'])) }}" 
               class="pill-apple {{ !request('category') ? 'active' : '' }}">
                Semua
            </a>
            @foreach($categories as $cat)
                <a href="{{ route('admin.master-data.index', ['tab' => 'kbli', 'category' => $cat] + request()->except(['category', 'page'])) }}" 
                   class="pill-apple {{ request('category') === $cat ? 'active' : '' }}">
                    {{ $cat }}
                </a>
            @endforeach
        </div>
    </div>

    {{-- KBLI Table --}}
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-dark-bg-secondary border-b" style="border-color: var(--dark-separator);">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-dark-text-tertiary uppercase tracking-wider w-32">
                        Kode
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-dark-text-tertiary uppercase tracking-wider">
                        Deskripsi
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-dark-text-tertiary uppercase tracking-wider w-32">
                        Sektor
                    </th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-dark-text-tertiary uppercase tracking-wider w-24">
                        Kategori
                    </th>
                </tr>
            </thead>
            <tbody class="divide-y" style="border-color: var(--dark-separator);">
                @forelse($kbliData as $kbli)
                    <tr class="hover-apple">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-sm font-mono font-medium text-apple-blue">
                                {{ $kbli->code }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-dark-text-primary">
                                {{ $kbli->description }}
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-dark-text-secondary">
                                {{ $kbli->sector ?? '-' }}
                            </div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="badge-apple-purple">
                                {{ substr($kbli->code, 0, 1) }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-12 text-center">
                            <div class="icon-circle-apple bg-gray-500 bg-opacity-10 w-16 h-16 mx-auto mb-4">
                                <svg class="w-8 h-8 text-dark-text-tertiary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                            <h3 class="text-lg font-medium text-dark-text-primary mb-2">Data tidak ditemukan</h3>
                            <p class="text-dark-text-secondary">
                                @if(request('search') || request('category'))
                                    Tidak ada data KBLI yang sesuai dengan filter Anda
                                @else
                                    Belum ada data KBLI tersedia
                                @endif
                            </p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if($kbliData->hasPages())
        <div class="px-6 py-4 border-t" style="border-color: var(--dark-separator);">
            {{ $kbliData->links() }}
        </div>
    @endif
</div>
