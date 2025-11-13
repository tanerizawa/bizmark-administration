@extends('layouts.app')

@section('title', 'Klien')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 space-y-3 sm:space-y-0">
        <div>
            <h1 class="text-2xl font-semibold text-dark-text-primary mb-1">
                Daftar Klien
            </h1>
            <p class="text-sm text-dark-text-secondary">Kelola data klien dan tracking proyek</p>
        </div>
        <a href="{{ route('clients.create') }}" class="btn-primary px-4 py-2 text-white rounded-apple text-sm font-medium inline-flex items-center">
            <i class="fas fa-plus mr-2"></i>
            Tambah Klien
        </a>
    </div>

    <!-- Alert Messages -->
    @if(session('success'))
        <div class="rounded-apple-lg p-4 mb-4" style="background-color: rgba(52, 199, 89, 0.15); border: 1px solid var(--apple-green);">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <i class="fas fa-check-circle mr-3" style="color: var(--apple-green);"></i>
                    <span class="text-sm font-medium" style="color: var(--apple-green);">{{ session('success') }}</span>
                </div>
                <button onclick="this.parentElement.parentElement.remove()" class="text-sm" style="color: var(--apple-green); opacity: 0.6;">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="rounded-apple-lg p-4 mb-4" style="background-color: rgba(255, 59, 48, 0.15); border: 1px solid var(--apple-red);">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-circle mr-3" style="color: var(--apple-red);"></i>
                    <span class="text-sm font-medium" style="color: var(--apple-red);">{{ session('error') }}</span>
                </div>
                <button onclick="this.parentElement.parentElement.remove()" class="text-sm" style="color: var(--apple-red); opacity: 0.6;">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    @endif

    <!-- Filter & Search Card -->
    <div class="card-elevated rounded-apple-lg mb-4">
        <div class="px-4 py-3" style="border-bottom: 1px solid rgba(84, 84, 88, 0.65);">
            <h3 class="text-base font-semibold text-white">Pencarian & Filter</h3>
        </div>
        <div class="p-4">
            <form method="GET" action="{{ route('clients.index') }}" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3">
                <div class="md:col-span-2">
                    <input type="text" 
                           name="search" 
                           class="w-full px-3 py-2 rounded-apple text-sm"
                           placeholder="Cari nama, perusahaan, email, atau telepon..." 
                           value="{{ request('search') }}"
                           style="background-color: var(--dark-bg-tertiary); border: 1px solid var(--dark-separator); color: var(--dark-text-primary);">
                </div>
                <div>
                    <select name="status" 
                            class="w-full px-3 py-2 rounded-apple text-sm"
                            style="background-color: var(--dark-bg-tertiary); border: 1px solid var(--dark-separator); color: var(--dark-text-primary);">
                        <option value="">Semua Status</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Aktif</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Tidak Aktif</option>
                        <option value="potential" {{ request('status') == 'potential' ? 'selected' : '' }}>Potensial</option>
                    </select>
                </div>
                <div>
                    <select name="client_type" 
                            class="w-full px-3 py-2 rounded-apple text-sm"
                            style="background-color: var(--dark-bg-tertiary); border: 1px solid var(--dark-separator); color: var(--dark-text-primary);">
                        <option value="">Semua Tipe</option>
                        <option value="individual" {{ request('client_type') == 'individual' ? 'selected' : '' }}>Individual</option>
                        <option value="company" {{ request('client_type') == 'company' ? 'selected' : '' }}>Perusahaan</option>
                        <option value="government" {{ request('client_type') == 'government' ? 'selected' : '' }}>Pemerintah</option>
                    </select>
                </div>
                <div class="md:col-span-4">
                    <button type="submit" class="btn-primary px-4 py-2 text-white rounded-apple text-sm font-medium inline-flex items-center">
                        <i class="fas fa-search mr-2"></i>Filter
                    </button>
                </div>
            </form>
        </div>
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
