@extends('layouts.app')

@section('title', 'Institusi')
@section('page-title', 'Manajemen Institusi')

@section('content')
    {{-- Compact Hero Section --}}
    <section class="card-elevated rounded-apple-lg admin-hero relative overflow-hidden mb-4">
        <div class="absolute inset-0 pointer-events-none" aria-hidden="true">
            <div class="w-48 h-48 bg-apple-blue opacity-20 blur-3xl rounded-full absolute -top-10 -right-6"></div>
            <div class="w-32 h-32 bg-apple-green opacity-15 blur-2xl rounded-full absolute bottom-0 left-6"></div>
        </div>
        <div class="relative space-y-3">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3">
                <div class="space-y-1 max-w-3xl">
                    <p class="admin-label-compact">Manajemen Institusi</p>
                    <h1 class="admin-hero-title">Database Institusi Penerbit Izin</h1>
                    <p class="admin-body" style="color: rgba(235,235,245,0.75);">Kelola data institusi pemerintah, BUMN, dan swasta yang menjadi mitra proses perizinan.</p>
                </div>
                <div>
                    <a href="{{ route('institutions.create') }}" class="admin-btn inline-flex items-center">
                        <i class="fas fa-plus mr-1.5"></i>Tambah Institusi
                    </a>
                </div>
            </div>

            {{-- Compact Stats Cards --}}
            <div class="grid grid-cols-2 md:grid-cols-4 gap-2">
                <div class="admin-stat-card" style="background: rgba(10,132,255,0.12);">
                    <div class="flex items-center gap-2">
                        <div class="w-7 h-7 rounded-lg flex items-center justify-center" style="background: rgba(10,132,255,0.25);">
                            <i class="fas fa-building text-xs" style="color: var(--apple-blue);"></i>
                        </div>
                        <div>
                            <p class="admin-stat" style="color: #FFFFFF;">{{ $institutions->total() }}</p>
                            <p class="admin-label-compact">Total Institusi</p>
                        </div>
                    </div>
                </div>
                <div class="admin-stat-card" style="background: rgba(255,59,48,0.12);">
                    <div class="flex items-center gap-2">
                        <div class="w-7 h-7 rounded-lg flex items-center justify-center" style="background: rgba(255,59,48,0.25);">
                            <i class="fas fa-landmark text-xs" style="color: var(--apple-red);"></i>
                        </div>
                        <div>
                            <p class="admin-stat" style="color: rgba(255,59,48,1);">{{ $institutions->where('type', 'Pemerintah')->count() }}</p>
                            <p class="admin-label-compact">Pemerintah</p>
                        </div>
                    </div>
                </div>
                <div class="admin-stat-card" style="background: rgba(255,149,0,0.12);">
                    <div class="flex items-center gap-2">
                        <div class="w-7 h-7 rounded-lg flex items-center justify-center" style="background: rgba(255,149,0,0.25);">
                            <i class="fas fa-city text-xs" style="color: var(--apple-orange);"></i>
                        </div>
                        <div>
                            <p class="admin-stat" style="color: rgba(255,149,0,1);">{{ $institutions->where('type', 'BUMN')->count() }}</p>
                            <p class="admin-label-compact">BUMN</p>
                        </div>
                    </div>
                </div>
                <div class="admin-stat-card" style="background: rgba(52,199,89,0.12);">
                    <div class="flex items-center gap-2">
                        <div class="w-7 h-7 rounded-lg flex items-center justify-center" style="background: rgba(52,199,89,0.25);">
                            <i class="fas fa-briefcase text-xs" style="color: var(--apple-green);"></i>
                        </div>
                        <div>
                            <p class="admin-stat" style="color: rgba(52,199,89,1);">{{ $institutions->where('type', 'Swasta')->count() }}</p>
                            <p class="admin-label-compact">Swasta</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Compact Search and Filter --}}
    <div class="card-elevated rounded-apple p-3 mb-3">
        <form method="GET" action="{{ route('institutions.index') }}" class="flex flex-wrap gap-2 items-end">
            <div class="flex-1 min-w-[150px]">
                <label class="admin-label-compact block">Cari</label>
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Nama institusi..." 
                           class="admin-input w-full pl-7 rounded bg-dark-bg-secondary border border-dark-separator text-white">
                    <i class="fas fa-search absolute left-2.5 top-1/2 transform -translate-y-1/2" style="font-size: 0.625rem; color: rgba(235,235,245,0.3);"></i>
                </div>
            </div>
            <div class="w-28">
                <label class="admin-label-compact block">Tipe</label>
                <select name="type" class="admin-input admin-select w-full rounded bg-dark-bg-secondary border border-dark-separator text-white">
                    <option value="">Semua</option>
                    <option value="Pemerintah" {{ request('type') == 'Pemerintah' ? 'selected' : '' }}>Pemerintah</option>
                    <option value="Swasta" {{ request('type') == 'Swasta' ? 'selected' : '' }}>Swasta</option>
                    <option value="BUMN" {{ request('type') == 'BUMN' ? 'selected' : '' }}>BUMN</option>
                    <option value="Lainnya" {{ request('type') == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                </select>
            </div>
            <div class="w-28">
                <label class="admin-label-compact block">Status</label>
                <select name="is_active" class="admin-input admin-select w-full rounded bg-dark-bg-secondary border border-dark-separator text-white">
                    <option value="">Semua</option>
                    <option value="1" {{ request('is_active') == '1' ? 'selected' : '' }}>Aktif</option>
                    <option value="0" {{ request('is_active') == '0' ? 'selected' : '' }}>Tidak Aktif</option>
                </select>
            </div>
        </form>
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('form[action="{{ route('institutions.index') }}"]');
            if (!form) return;
            form.querySelectorAll('select[name]').forEach(el => el.addEventListener('change', () => form.submit()));
            const searchInput = form.querySelector('input[name="search"]');
            if (searchInput) searchInput.addEventListener('keydown', e => { if (e.key === 'Enter') { e.preventDefault(); form.submit(); }});
        });
        </script>
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
