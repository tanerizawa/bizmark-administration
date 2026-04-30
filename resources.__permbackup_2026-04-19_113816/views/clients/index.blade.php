@extends('layouts.app')

@section('title', 'Klien')

@section('content')
<div class="space-y-4">
    {{-- Compact Hero Section --}}
    <section class="card-elevated rounded-apple-lg admin-hero relative overflow-hidden mb-4">
        <div class="absolute inset-0 pointer-events-none" aria-hidden="true">
            <div class="w-48 h-48 bg-apple-purple opacity-20 blur-3xl rounded-full absolute -top-10 -right-6"></div>
            <div class="w-32 h-32 bg-apple-orange opacity-15 blur-2xl rounded-full absolute bottom-0 left-6"></div>
        </div>
        <div class="relative space-y-3">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3">
                <div class="space-y-1 max-w-3xl">
                    <p class="admin-label-compact">Manajemen Klien</p>
                    <h1 class="admin-hero-title">Database Klien Aktif</h1>
                    <p class="admin-body" style="color: rgba(235,235,245,0.75);">Kelola hubungan klien, tracking proyek, dan riwayat kerja sama dalam satu platform.</p>
                </div>
                <div>
                    <a href="{{ route('clients.create') }}" class="admin-btn inline-flex items-center" style="background: rgba(175,82,222,0.25);">
                        <i class="fas fa-plus mr-1.5"></i>Tambah Klien
                    </a>
                </div>
            </div>

            {{-- Compact Stats Cards --}}
            <div class="grid grid-cols-2 md:grid-cols-4 gap-2">
                <div class="admin-stat-card" style="background: rgba(10,132,255,0.12);">
                    <div class="flex items-center gap-2">
                        <div class="w-7 h-7 rounded-lg flex items-center justify-center" style="background: rgba(10,132,255,0.25);">
                            <i class="fas fa-users text-xs" style="color: var(--apple-blue);"></i>
                        </div>
                        <div>
                            <p class="admin-stat" style="color: #FFFFFF;">{{ $clients->total() }}</p>
                            <p class="admin-label-compact">Total Klien</p>
                        </div>
                    </div>
                </div>
                <div class="admin-stat-card" style="background: rgba(52,199,89,0.12);">
                    <div class="flex items-center gap-2">
                        <div class="w-7 h-7 rounded-lg flex items-center justify-center" style="background: rgba(52,199,89,0.25);">
                            <i class="fas fa-check text-xs" style="color: var(--apple-green);"></i>
                        </div>
                        <div>
                            <p class="admin-stat" style="color: rgba(52,199,89,1);">{{ $clients->where('status', 'active')->count() }}</p>
                            <p class="admin-label-compact">Aktif</p>
                        </div>
                    </div>
                </div>
                <div class="admin-stat-card" style="background: rgba(175,82,222,0.12);">
                    <div class="flex items-center gap-2">
                        <div class="w-7 h-7 rounded-lg flex items-center justify-center" style="background: rgba(175,82,222,0.25);">
                            <i class="fas fa-building text-xs" style="color: rgba(175,82,222,1);"></i>
                        </div>
                        <div>
                            <p class="admin-stat" style="color: rgba(175,82,222,1);">{{ $clients->where('client_type', 'company')->count() }}</p>
                            <p class="admin-label-compact">Perusahaan</p>
                        </div>
                    </div>
                </div>
                <div class="admin-stat-card" style="background: rgba(255,159,10,0.12);">
                    <div class="flex items-center gap-2">
                        <div class="w-7 h-7 rounded-lg flex items-center justify-center" style="background: rgba(255,159,10,0.25);">
                            <i class="fas fa-star text-xs" style="color: var(--apple-orange);"></i>
                        </div>
                        <div>
                            <p class="admin-stat" style="color: rgba(255,159,10,1);">{{ $clients->where('status', 'potential')->count() }}</p>
                            <p class="admin-label-compact">Potensial</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Alert Messages -->
    @if(session('success'))
        <div class="rounded-apple p-3 mb-3" style="background-color: rgba(52, 199, 89, 0.15); border: 1px solid var(--apple-green);">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <i class="fas fa-check-circle mr-2" style="color: var(--apple-green);"></i>
                    <span class="admin-body" style="color: var(--apple-green);">{{ session('success') }}</span>
                </div>
                <button onclick="this.parentElement.parentElement.remove()" style="color: var(--apple-green); opacity: 0.6;"><i class="fas fa-times"></i></button>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="rounded-apple p-3 mb-3" style="background-color: rgba(255, 59, 48, 0.15); border: 1px solid var(--apple-red);">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-circle mr-2" style="color: var(--apple-red);"></i>
                    <span class="admin-body" style="color: var(--apple-red);">{{ session('error') }}</span>
                </div>
                <button onclick="this.parentElement.parentElement.remove()" style="color: var(--apple-red); opacity: 0.6;"><i class="fas fa-times"></i></button>
            </div>
        </div>
    @endif

    {{-- Compact Search and Filter --}}
    <div class="card-elevated rounded-apple p-3 mb-3">
        <form method="GET" action="{{ route('clients.index') }}" class="flex flex-wrap gap-2 items-end">
            <div class="flex-1 min-w-[180px]">
                <label class="admin-label-compact block">Cari</label>
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Nama, perusahaan, email..." 
                           class="admin-input w-full pl-7 rounded bg-dark-bg-secondary border border-dark-separator text-white">
                    <i class="fas fa-search absolute left-2.5 top-1/2 transform -translate-y-1/2" style="font-size: 0.625rem; color: rgba(235,235,245,0.3);"></i>
                </div>
            </div>
            <div class="w-28">
                <label class="admin-label-compact block">Status</label>
                <select name="status" class="admin-input admin-select w-full rounded bg-dark-bg-secondary border border-dark-separator text-white">
                    <option value="">Semua</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Aktif</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Tidak Aktif</option>
                    <option value="potential" {{ request('status') == 'potential' ? 'selected' : '' }}>Potensial</option>
                </select>
            </div>
            <div class="w-28">
                <label class="admin-label-compact block">Tipe</label>
                <select name="client_type" class="admin-input admin-select w-full rounded bg-dark-bg-secondary border border-dark-separator text-white">
                    <option value="">Semua</option>
                    <option value="individual" {{ request('client_type') == 'individual' ? 'selected' : '' }}>Individual</option>
                    <option value="company" {{ request('client_type') == 'company' ? 'selected' : '' }}>Perusahaan</option>
                    <option value="government" {{ request('client_type') == 'government' ? 'selected' : '' }}>Pemerintah</option>
                </select>
            </div>
        </form>
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('form[action="{{ route('clients.index') }}"]');
            if (!form) return;
            form.querySelectorAll('select[name]').forEach(el => el.addEventListener('change', () => form.submit()));
            const searchInput = form.querySelector('input[name="search"]');
            if (searchInput) searchInput.addEventListener('keydown', e => { if (e.key === 'Enter') { e.preventDefault(); form.submit(); }});
        });
        </script>
    </div>

    <!-- Clients Table Card -->
    <div class="card-elevated rounded-apple-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-700">
                <thead style="background-color: var(--dark-bg-secondary);">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-dark-text-secondary">Klien</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-dark-text-secondary">Kontak</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-dark-text-secondary">Tipe</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-dark-text-secondary">Status</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-dark-text-secondary">Proyek</th>
                        <th scope="col" class="px-6 py-3 text-center text-xs font-semibold uppercase tracking-wider text-dark-text-secondary">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-700" style="background-color: var(--dark-bg-secondary);">
                    @forelse($clients as $client)
                        <tr class="hover-lift transition-apple" style="cursor: pointer;" onclick="window.location='{{ route('clients.show', $client) }}'">
                            <td class="px-6 py-4">
                                <div class="font-semibold text-sm text-dark-text-primary">{{ $client->company_name ?? $client->name }}</div>
                                <div class="text-xs text-dark-text-secondary mt-1">
                                    @if($client->contact_person)
                                        <span>{{ $client->contact_person }}</span>
                                    @endif
                                    @if($client->industry)
                                        <span class="ml-2">• {{ $client->industry }}</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="space-y-1">
                                    @if($client->email)
                                        <div class="flex items-center text-xs text-dark-text-secondary">
                                            <i class="fas fa-envelope mr-2 text-apple-blue"></i>{{ $client->email }}
                                        </div>
                                    @endif
                                    @if($client->phone)
                                        <div class="flex items-center text-xs text-dark-text-secondary">
                                            <i class="fas fa-phone mr-2 text-apple-blue"></i>{{ $client->phone }}
                                        </div>
                                    @endif
                                    @if($client->mobile)
                                        <div class="flex items-center text-xs text-dark-text-secondary">
                                            <i class="fab fa-whatsapp mr-2 text-apple-green"></i>{{ $client->mobile }}
                                        </div>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($client->client_type == 'individual')
                                    <span class="px-2 py-1 text-xs font-medium rounded-apple" style="background-color: rgba(90, 200, 250, 0.15); color: var(--apple-teal);">Individual</span>
                                @elseif($client->client_type == 'company')
                                    <span class="px-2 py-1 text-xs font-medium rounded-apple" style="background-color: rgba(0, 122, 255, 0.15); color: var(--apple-blue);">Perusahaan</span>
                                @else
                                    <span class="px-2 py-1 text-xs font-medium rounded-apple" style="background-color: rgba(175, 82, 222, 0.15); color: var(--apple-purple);">Pemerintah</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($client->status == 'active')
                                    <span class="px-2 py-1 text-xs font-medium rounded-apple" style="background-color: rgba(52, 199, 89, 0.15); color: var(--apple-green);">Aktif</span>
                                @elseif($client->status == 'inactive')
                                    <span class="px-2 py-1 text-xs font-medium rounded-apple" style="background-color: rgba(255, 59, 48, 0.15); color: var(--apple-red);">Tidak Aktif</span>
                                @else
                                    <span class="px-2 py-1 text-xs font-medium rounded-apple" style="background-color: rgba(255, 149, 0, 0.15); color: var(--apple-orange);">Potensial</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs font-medium rounded-apple" style="background-color: rgba(0, 122, 255, 0.15); color: var(--apple-blue);">
                                    <i class="fas fa-folder mr-1"></i>{{ $client->projects->count() }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center" onclick="event.stopPropagation()">
                                <div class="flex items-center justify-center space-x-2">
                                    <a href="{{ route('clients.show', $client) }}" 
                                       class="inline-flex items-center px-3 py-1.5 rounded-apple text-xs font-medium transition-apple" 
                                       style="background-color: rgba(90, 200, 250, 0.15); color: var(--apple-teal); border: 1px solid rgba(90, 200, 250, 0.3);"
                                       onmouseover="this.style.backgroundColor='rgba(90, 200, 250, 0.25)'" 
                                       onmouseout="this.style.backgroundColor='rgba(90, 200, 250, 0.15)'">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('clients.edit', $client) }}" 
                                       class="inline-flex items-center px-3 py-1.5 rounded-apple text-xs font-medium transition-apple" 
                                       style="background-color: rgba(255, 149, 0, 0.15); color: var(--apple-orange); border: 1px solid rgba(255, 149, 0, 0.3);"
                                       onmouseover="this.style.backgroundColor='rgba(255, 149, 0, 0.25)'" 
                                       onmouseout="this.style.backgroundColor='rgba(255, 149, 0, 0.15)'">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('clients.destroy', $client) }}" 
                                          method="POST" 
                                          class="inline-block" 
                                          onsubmit="return confirm('Yakin ingin menghapus klien ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="inline-flex items-center px-3 py-1.5 rounded-apple text-xs font-medium transition-apple" 
                                                style="background-color: rgba(255, 59, 48, 0.15); color: var(--apple-red); border: 1px solid rgba(255, 59, 48, 0.3);"
                                                onmouseover="this.style.backgroundColor='rgba(255, 59, 48, 0.25)'" 
                                                onmouseout="this.style.backgroundColor='rgba(255, 59, 48, 0.15)'">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-16 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <i class="fas fa-users text-6xl mb-4 text-dark-text-tertiary"></i>
                                    <p class="text-sm text-dark-text-secondary mb-4">Belum ada data klien</p>
                                    <a href="{{ route('clients.create') }}" class="btn-primary px-4 py-2 text-white rounded-apple text-sm font-medium inline-flex items-center">
                                        <i class="fas fa-plus mr-2"></i>Tambah Klien Pertama
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($clients->hasPages())
            <div class="px-6 py-4" style="background-color: var(--dark-bg-tertiary); border-top: 1px solid var(--dark-separator);">
                <div class="flex justify-center">
                    {{ $clients->links() }}
                </div>
            </div>
        @endif
    </div>
</div>

<style>
    /* Form focus states */
    input:focus,
    select:focus {
        outline: none;
        border-color: var(--apple-blue) !important;
        box-shadow: 0 0 0 3px rgba(0, 122, 255, 0.25) !important;
    }

    /* Placeholder */
    input::placeholder {
        color: var(--dark-text-tertiary);
    }

    /* Select dropdown options */
    select option {
        background-color: var(--dark-bg-tertiary);
        color: var(--dark-text-primary);
    }

    /* Table row hover - override inline onclick */
    tbody tr:hover {
        background-color: var(--dark-bg-tertiary) !important;
    }

    /* Pagination styling */
    .pagination {
        display: flex;
        list-style: none;
        padding: 0;
        margin: 0;
        gap: 0.5rem;
    }

    .pagination .page-link {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0.5rem 0.75rem;
        font-size: 0.875rem;
        font-weight: 500;
        color: var(--dark-text-primary);
        background-color: var(--dark-bg-secondary);
        border: 1px solid var(--dark-separator);
        border-radius: 10px;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .pagination .page-link:hover {
        background-color: var(--dark-bg-tertiary);
        border-color: var(--apple-blue);
        color: var(--apple-blue);
    }

    .pagination .page-item.active .page-link {
        background-color: var(--apple-blue);
        border-color: var(--apple-blue);
        color: white;
    }

    .pagination .page-item.disabled .page-link {
        opacity: 0.5;
        cursor: not-allowed;
    }
</style>
@endsection
