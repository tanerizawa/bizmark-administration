@extends('layouts.app')

@section('title', 'Institusi')
@section('page-title', 'Manajemen Institusi')

@section('content')
    {{-- Hero Section --}}
    <section class="card-elevated rounded-apple-xl p-5 md:p-6 relative overflow-hidden mb-6">
        <div class="absolute inset-0 pointer-events-none" aria-hidden="true">
            <div class="w-72 h-72 bg-apple-blue opacity-30 blur-3xl rounded-full absolute -top-16 -right-10"></div>
            <div class="w-48 h-48 bg-apple-green opacity-20 blur-2xl rounded-full absolute bottom-0 left-10"></div>
        </div>
        <div class="relative space-y-5 md:space-y-6">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-5">
                <div class="space-y-2.5 max-w-3xl">
                    <p class="text-sm uppercase tracking-[0.4em]" style="color: rgba(235,235,245,0.5);">Manajemen Institusi</p>
                    <h1 class="text-2xl md:text-3xl font-bold" style="color: #FFFFFF;">
                        Database Institusi Penerbit Izin
                    </h1>
                    <p class="text-sm md:text-base" style="color: rgba(235,235,245,0.75);">
                        Kelola data lengkap institusi pemerintah, BUMN, dan swasta yang menjadi mitra dalam proses perizinan.
                    </p>
                </div>
                <div class="space-y-2.5">
                    <a href="{{ route('institutions.create') }}" 
                       class="inline-flex items-center px-4 py-2 rounded-apple text-sm font-semibold" 
                       style="background: rgba(10,132,255,0.25); color: rgba(235,235,245,0.9);">
                        <i class="fas fa-plus mr-2"></i>
                        Tambah Institusi
                        <i class="fas fa-arrow-right ml-2 text-xs"></i>
                    </a>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3 md:gap-4">
                <!-- Total -->
                <div class="rounded-apple-lg p-3.5 md:p-4" style="background: rgba(10,132,255,0.12);">
                    <p class="text-xs uppercase tracking-widest" style="color: rgba(10,132,255,0.9);">Total Institusi</p>
                    <h2 class="text-2xl font-bold mt-1.5" style="color: #FFFFFF;">{{ $institutions->total() }}</h2>
                    <p class="text-xs" style="color: rgba(235,235,245,0.6);">Lembaga terdaftar</p>
                </div>

                <!-- Pemerintah -->
                <div class="rounded-apple-lg p-3.5 md:p-4" style="background: rgba(255,59,48,0.12);">
                    <p class="text-xs uppercase tracking-widest" style="color: rgba(255,59,48,0.9);">Pemerintah</p>
                    <h2 class="text-2xl font-bold mt-1.5" style="color: rgba(255,59,48,1);">
                        {{ $institutions->where('type', 'Pemerintah')->count() }}
                    </h2>
                    <p class="text-xs" style="color: rgba(235,235,245,0.6);">Instansi negara</p>
                </div>

                <!-- BUMN -->
                <div class="rounded-apple-lg p-3.5 md:p-4" style="background: rgba(255,149,0,0.12);">
                    <p class="text-xs uppercase tracking-widest" style="color: rgba(255,149,0,0.9);">BUMN</p>
                    <h2 class="text-2xl font-bold mt-1.5" style="color: rgba(255,149,0,1);">
                        {{ $institutions->where('type', 'BUMN')->count() }}
                    </h2>
                    <p class="text-xs" style="color: rgba(235,235,245,0.6);">Perusahaan negara</p>
                </div>

                <!-- Swasta -->
                <div class="rounded-apple-lg p-3.5 md:p-4" style="background: rgba(52,199,89,0.12);">
                    <p class="text-xs uppercase tracking-widest" style="color: rgba(52,199,89,0.9);">Swasta</p>
                    <h2 class="text-2xl font-bold mt-1.5" style="color: rgba(52,199,89,1);">
                        {{ $institutions->where('type', 'Swasta')->count() }}
                    </h2>
                    <p class="text-xs" style="color: rgba(235,235,245,0.6);">Sektor privat</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Search and Filter Card -->
    <div class="card-elevated rounded-apple-lg mb-4 overflow-hidden">
        <div class="px-4 py-3" style="border-bottom: 1px solid rgba(84, 84, 88, 0.65);">
            <h3 class="text-base font-semibold" style="color: #FFFFFF;">Pencarian & Filter</h3>
        </div>
        <div class="p-4">
            <form method="GET" action="{{ route('institutions.index') }}" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                    <div>
                        <label class="block text-xs font-medium mb-1.5" style="color: rgba(235, 235, 245, 0.6);">Pencarian</label>
                        <div class="relative">
                            <input type="text" 
                                   name="search" 
                                   value="{{ request('search') }}" 
                                   placeholder="Nama institusi..." 
                                   class="input-dark w-full pl-9 pr-3 py-2 rounded-apple text-sm">
                            <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-sm" style="color: rgba(235, 235, 245, 0.3);"></i>
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-xs font-medium mb-1.5" style="color: rgba(235, 235, 245, 0.6);">Tipe</label>
                        <select name="type" class="input-dark w-full px-3 py-2 rounded-apple text-sm">
                            <option value="">Semua Tipe</option>
                            <option value="Pemerintah" {{ request('type') == 'Pemerintah' ? 'selected' : '' }}>Pemerintah</option>
                            <option value="Swasta" {{ request('type') == 'Swasta' ? 'selected' : '' }}>Swasta</option>
                            <option value="BUMN" {{ request('type') == 'BUMN' ? 'selected' : '' }}>BUMN</option>
                            <option value="Lainnya" {{ request('type') == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-xs font-medium mb-1.5" style="color: rgba(235, 235, 245, 0.6);">Status</label>
                        <select name="is_active" class="input-dark w-full px-3 py-2 rounded-apple text-sm">
                            <option value="">Semua Status</option>
                            <option value="1" {{ request('is_active') == '1' ? 'selected' : '' }}>Aktif</option>
                            <option value="0" {{ request('is_active') == '0' ? 'selected' : '' }}>Tidak Aktif</option>
                        </select>
                    </div>
                </div>
                <script>
                // Auto submit form on filter change
                document.addEventListener('DOMContentLoaded', function() {
                    const form = document.querySelector('form[action="{{ route('institutions.index') }}"]');
                    if (!form) return;
                    
                    const searchInput = form.querySelector('input[name="search"]');
                    
                    // Auto-submit for select dropdowns
                    form.querySelectorAll('select[name]').forEach(function(el) {
                        el.addEventListener('change', function() {
                            form.submit();
                        });
                    });
                    
                    // Submit search on Enter key only
                    if (searchInput) {
                        searchInput.addEventListener('keydown', function(e) {
                            if (e.key === 'Enter') {
                                e.preventDefault();
                                e.stopPropagation();
                                form.submit();
                            }
                        });
                    }
                });
                </script>
            </form>
        </div>
    </div>

    <!-- Institutions Table -->
    <div class="card-elevated rounded-apple-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-700">
                <thead style="background-color: var(--dark-bg-secondary);">
                    <tr>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-dark-text-secondary">Institusi</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-dark-text-secondary">Tipe</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-dark-text-secondary">Kontak</th>
                        <th scope="col" class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wider text-dark-text-secondary">Jenis Izin</th>
                        <th scope="col" class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wider text-dark-text-secondary">Status</th>
                        <th scope="col" class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wider text-dark-text-secondary">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-700" style="background-color: var(--dark-bg-secondary);">
                    @forelse($institutions as $institution)
                        <tr class="hover-lift transition-apple" style="cursor: pointer;" onclick="window.location='{{ route('institutions.show', $institution) }}'">
                            <!-- Institusi Info -->
                            <td class="px-4 py-3">
                                <div class="flex items-center">
                                    @php
                                        $typeConfig = [
                                            'Pemerintah' => ['icon' => 'fa-landmark', 'color' => 'rgba(255, 59, 48, 1)', 'bg' => 'rgba(255, 59, 48, 0.2)'],
                                            'BUMN' => ['icon' => 'fa-city', 'color' => 'rgba(255, 149, 0, 1)', 'bg' => 'rgba(255, 149, 0, 0.2)'],
                                            'Swasta' => ['icon' => 'fa-briefcase', 'color' => 'rgba(52, 199, 89, 1)', 'bg' => 'rgba(52, 199, 89, 0.2)'],
                                            'Lainnya' => ['icon' => 'fa-building', 'color' => 'rgba(142, 142, 147, 1)', 'bg' => 'rgba(142, 142, 147, 0.2)'],
                                        ];
                                        $config = $typeConfig[$institution->type] ?? $typeConfig['Lainnya'];
                                    @endphp
                                    <div class="w-10 h-10 rounded-full flex items-center justify-center mr-3" style="background-color: {{ $config['bg'] }};">
                                        <i class="fas {{ $config['icon'] }} text-base" style="color: {{ $config['color'] }};"></i>
                                    </div>
                                    <div>
                                        <div class="font-semibold text-sm text-dark-text-primary">{{ $institution->name }}</div>
                                        @if($institution->contact_person)
                                            <div class="text-xs text-dark-text-secondary mt-0.5">
                                                {{ $institution->contact_person }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            <!-- Tipe -->
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium" style="background-color: {{ $config['bg'] }}; color: {{ $config['color'] }};">
                                    <i class="fas {{ $config['icon'] }} mr-1.5"></i>
                                    {{ $institution->type }}
                                </span>
                            </td>

                            <!-- Kontak -->
                            <td class="px-4 py-3">
                                <div class="text-sm space-y-1">
                                    @if($institution->email)
                                        <div class="flex items-center text-dark-text-secondary">
                                            <i class="fas fa-envelope w-4 mr-2 text-xs"></i>
                                            <span class="truncate">{{ $institution->email }}</span>
                                        </div>
                                    @endif
                                    @if($institution->phone)
                                        <div class="flex items-center text-dark-text-secondary">
                                            <i class="fas fa-phone w-4 mr-2 text-xs"></i>
                                            <span>{{ $institution->phone }}</span>
                                        </div>
                                    @endif
                                </div>
                            </td>

                            <!-- Jenis Izin -->
                            <td class="px-4 py-3 text-center whitespace-nowrap">
                                <span class="inline-flex items-center justify-center px-3 py-1 rounded-full text-xs font-semibold" style="background-color: rgba(0, 122, 255, 0.15); color: rgba(0, 122, 255, 1);">
                                    {{ $institution->permit_types_count ?? 0 }} Izin
                                </span>
                            </td>

                            <!-- Status -->
                            <td class="px-4 py-3 text-center whitespace-nowrap">
                                @if($institution->is_active)
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium" style="background-color: rgba(52, 199, 89, 0.15); color: rgba(52, 199, 89, 1);">
                                        <i class="fas fa-check-circle mr-1.5"></i>
                                        Aktif
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium" style="background-color: rgba(142, 142, 147, 0.15); color: rgba(142, 142, 147, 1);">
                                        <i class="fas fa-times-circle mr-1.5"></i>
                                        Tidak Aktif
                                    </span>
                                @endif
                            </td>

                            <!-- Aksi -->
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-center space-x-2" onclick="event.stopPropagation();">
                                    <a href="{{ route('institutions.show', $institution) }}" 
                                       class="p-2 rounded-apple transition-apple" 
                                       style="color: #0A84FF; background-color: rgba(10, 132, 255, 0.1); border: 1px solid rgba(10, 132, 255, 0.3);" 
                                       onmouseover="this.style.backgroundColor='#0A84FF'; this.style.color='#FFFFFF'" 
                                       onmouseout="this.style.backgroundColor='rgba(10, 132, 255, 0.1)'; this.style.color='#0A84FF'">
                                        <i class="fas fa-eye text-sm"></i>
                                    </a>
                                    <a href="{{ route('institutions.edit', $institution) }}" 
                                       class="p-2 rounded-apple transition-apple" 
                                       style="color: #FF9F0A; background-color: rgba(255, 159, 10, 0.1); border: 1px solid rgba(255, 159, 10, 0.3);" 
                                       onmouseover="this.style.backgroundColor='#FF9F0A'; this.style.color='#FFFFFF'" 
                                       onmouseout="this.style.backgroundColor='rgba(255, 159, 10, 0.1)'; this.style.color='#FF9F0A'">
                                        <i class="fas fa-edit text-sm"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center">
                                <div class="flex flex-col items-center justify-center" style="color: rgba(235, 235, 245, 0.6);">
                                    <i class="fas fa-inbox text-4xl mb-3"></i>
                                    <p class="text-sm font-medium">Tidak ada institusi ditemukan</p>
                                    <p class="text-xs mt-1">Coba ubah filter atau tambahkan institusi baru</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($institutions->hasPages())
            <div class="px-4 py-3" style="border-top: 1px solid rgba(84, 84, 88, 0.65); background-color: var(--dark-bg-secondary);">
                {{ $institutions->links() }}
            </div>
        @endif
    </div>
@endsection
