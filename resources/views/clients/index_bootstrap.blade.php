@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 py-6">
    <!-- Header -->
    <div class="mb-6">
        <div class="d-flex justify-content-between align-items-start">
            <div>
                <h1 class="text-dark-text-primary mb-2" style="font-size: 1.75rem; font-weight: 600;">
                    <i class="fas fa-users me-2" style="color: var(--apple-blue);"></i>Daftar Klien
                </h1>
                <p class="text-dark-text-secondary" style="font-size: 0.875rem;">Kelola data klien dan tracking proyek</p>
            </div>
            <a href="{{ route('clients.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Tambah Klien
            </a>
        </div>
    </div>

    <!-- Alert Messages -->
    @if(session('success'))
        <div class="alert alert-dismissible fade show mb-4" role="alert" style="background-color: rgba(52, 199, 89, 0.15); border: 1px solid var(--apple-green); color: var(--apple-green);">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close" style="filter: brightness(0) invert(1);"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-dismissible fade show mb-4" role="alert" style="background-color: rgba(255, 59, 48, 0.15); border: 1px solid var(--apple-red); color: var(--apple-red);">
            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close" style="filter: brightness(0) invert(1);"></button>
        </div>
    @endif

    <!-- Filter & Search Card -->
    <div class="card mb-4" style="background-color: var(--dark-bg-secondary); border: 1px solid var(--dark-separator);">
        <div class="card-body">
            <form method="GET" action="{{ route('clients.index') }}" class="row g-3">
                <div class="col-md-4">
                    <input type="text" 
                           name="search" 
                           class="form-control" 
                           placeholder="Cari nama, perusahaan, email, atau telepon..." 
                           value="{{ request('search') }}"
                           style="background-color: var(--dark-bg-tertiary); border-color: var(--dark-separator); color: var(--dark-text-primary);">
                </div>
                <div class="col-md-3">
                    <select name="status" 
                            class="form-select"
                            style="background-color: var(--dark-bg-tertiary); border-color: var(--dark-separator); color: var(--dark-text-primary);">
                        <option value="">Semua Status</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Aktif</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Tidak Aktif</option>
                        <option value="potential" {{ request('status') == 'potential' ? 'selected' : '' }}>Potensial</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="client_type" 
                            class="form-select"
                            style="background-color: var(--dark-bg-tertiary); border-color: var(--dark-separator); color: var(--dark-text-primary);">
                        <option value="">Semua Tipe</option>
                        <option value="individual" {{ request('client_type') == 'individual' ? 'selected' : '' }}>Individual</option>
                        <option value="company" {{ request('client_type') == 'company' ? 'selected' : '' }}>Perusahaan</option>
                        <option value="government" {{ request('client_type') == 'government' ? 'selected' : '' }}>Pemerintah</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search me-2"></i>Filter
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Clients Table Card -->
    <div class="card" style="background-color: var(--dark-bg-secondary); border: 1px solid var(--dark-separator);">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0" style="color: var(--dark-text-primary);">
                    <thead style="background-color: var(--dark-bg-secondary);">
                        <tr>
                            <th style="color: var(--dark-text-secondary); border-bottom: 1px solid var(--dark-separator); padding: 1rem; font-weight: 600; font-size: 0.875rem; text-transform: uppercase; letter-spacing: 0.5px;">Nama Klien</th>
                            <th style="color: var(--dark-text-secondary); border-bottom: 1px solid var(--dark-separator); padding: 1rem; font-weight: 600; font-size: 0.875rem; text-transform: uppercase; letter-spacing: 0.5px;">Perusahaan</th>
                            <th style="color: var(--dark-text-secondary); border-bottom: 1px solid var(--dark-separator); padding: 1rem; font-weight: 600; font-size: 0.875rem; text-transform: uppercase; letter-spacing: 0.5px;">Kontak</th>
                            <th style="color: var(--dark-text-secondary); border-bottom: 1px solid var(--dark-separator); padding: 1rem; font-weight: 600; font-size: 0.875rem; text-transform: uppercase; letter-spacing: 0.5px;">Tipe</th>
                            <th style="color: var(--dark-text-secondary); border-bottom: 1px solid var(--dark-separator); padding: 1rem; font-weight: 600; font-size: 0.875rem; text-transform: uppercase; letter-spacing: 0.5px;">Status</th>
                            <th style="color: var(--dark-text-secondary); border-bottom: 1px solid var(--dark-separator); padding: 1rem; font-weight: 600; font-size: 0.875rem; text-transform: uppercase; letter-spacing: 0.5px;">Proyek</th>
                            <th style="color: var(--dark-text-secondary); border-bottom: 1px solid var(--dark-separator); padding: 1rem; text-align: center; font-weight: 600; font-size: 0.875rem; text-transform: uppercase; letter-spacing: 0.5px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody style="background-color: var(--dark-bg-secondary);">
                        @forelse($clients as $client)
                            <tr style="border-bottom: 1px solid var(--dark-separator); background-color: var(--dark-bg-secondary);">
                                <td style="padding: 1rem;">
                                    <div class="fw-bold text-dark-text-primary">{{ $client->name }}</div>
                                    @if($client->contact_person)
                                        <small class="text-dark-text-secondary">{{ $client->contact_person }}</small>
                                    @endif
                                </td>
                                <td style="padding: 1rem;">
                                    <span class="text-dark-text-primary">{{ $client->company_name ?? '-' }}</span>
                                    @if($client->industry)
                                        <br><small class="text-dark-text-secondary">{{ $client->industry }}</small>
                                    @endif
                                </td>
                                <td style="padding: 1rem;">
                                    @if($client->email)
                                        <div class="text-dark-text-secondary" style="font-size: 0.875rem;">
                                            <i class="fas fa-envelope me-1" style="color: var(--apple-blue);"></i>{{ $client->email }}
                                        </div>
                                    @endif
                                    @if($client->phone)
                                        <div class="text-dark-text-secondary" style="font-size: 0.875rem;">
                                            <i class="fas fa-phone me-1" style="color: var(--apple-blue);"></i>{{ $client->phone }}
                                        </div>
                                    @endif
                                    @if($client->mobile)
                                        <div class="text-dark-text-secondary" style="font-size: 0.875rem;">
                                            <i class="fab fa-whatsapp me-1" style="color: var(--apple-green);"></i>{{ $client->mobile }}
                                        </div>
                                    @endif
                                </td>
                                <td style="padding: 1rem;">
                                    @if($client->client_type == 'individual')
                                        <span class="badge" style="background-color: var(--apple-teal);">Individual</span>
                                    @elseif($client->client_type == 'company')
                                        <span class="badge" style="background-color: var(--apple-blue);">Perusahaan</span>
                                    @else
                                        <span class="badge" style="background-color: var(--apple-purple);">Pemerintah</span>
                                    @endif
                                </td>
                                <td style="padding: 1rem;">
                                    @if($client->status == 'active')
                                        <span class="badge" style="background-color: var(--apple-green);">Aktif</span>
                                    @elseif($client->status == 'inactive')
                                        <span class="badge" style="background-color: var(--apple-red);">Tidak Aktif</span>
                                    @else
                                        <span class="badge" style="background-color: var(--apple-orange);">Potensial</span>
                                    @endif
                                </td>
                                <td style="padding: 1rem;">
                                    <span class="badge rounded-pill" style="background-color: var(--apple-blue);">
                                        {{ $client->projects->count() }} Proyek
                                    </span>
                                </td>
                                <td style="padding: 1rem; text-align: center;">
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a href="{{ route('clients.show', $client) }}" 
                                           class="btn btn-sm" 
                                           title="Detail"
                                           style="background-color: var(--apple-teal); color: white; border: none;">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('clients.edit', $client) }}" 
                                           class="btn btn-sm" 
                                           title="Edit"
                                           style="background-color: var(--apple-orange); color: white; border: none;">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('clients.destroy', $client) }}" 
                                              method="POST" 
                                              class="d-inline" 
                                              onsubmit="return confirm('Yakin ingin menghapus klien ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="btn btn-sm" 
                                                    title="Hapus"
                                                    style="background-color: var(--apple-red); color: white; border: none;">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <i class="fas fa-users fa-3x mb-3 d-block text-dark-text-tertiary"></i>
                                    <p class="text-dark-text-secondary mb-3">Belum ada data klien</p>
                                    <a href="{{ route('clients.create') }}" class="btn btn-primary">
                                        <i class="fas fa-plus me-2"></i>Tambah Klien Pertama
                                    </a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($clients->hasPages())
            <div class="card-footer" style="background-color: var(--dark-bg-tertiary); border-top: 1px solid var(--dark-separator);">
                <div class="d-flex justify-content-center">
                    {{ $clients->links() }}
                </div>
            </div>
        @endif
    </div>
</div>

<style>
    /* Table header background */
    .table thead th {
        background-color: var(--dark-bg-secondary) !important;
        color: var(--dark-text-secondary) !important;
        border-color: var(--dark-separator) !important;
    }

    /* Table body and rows background */
    .table tbody {
        background-color: var(--dark-bg-secondary) !important;
    }

    .table tbody tr {
        background-color: var(--dark-bg-secondary) !important;
    }

    /* Table hover effect */
    .table tbody tr:hover {
        background-color: var(--dark-bg-tertiary) !important;
    }

    /* Table cells */
    .table td {
        background-color: transparent !important;
        border-color: var(--dark-separator) !important;
    }

    /* Form focus states */
    .form-control:focus,
    .form-select:focus {
        background-color: var(--dark-bg-tertiary);
        border-color: var(--apple-blue);
        color: var(--dark-text-primary);
        box-shadow: 0 0 0 0.2rem rgba(0, 122, 255, 0.25);
    }

    /* Form placeholder */
    .form-control::placeholder {
        color: var(--dark-text-tertiary);
    }

    /* Select dropdown options */
    .form-select option {
        background-color: var(--dark-bg-tertiary);
        color: var(--dark-text-primary);
    }

    /* Button hover effects */
    .btn-group .btn:hover {
        opacity: 0.85;
        transform: translateY(-1px);
    }

    /* Badge styling */
    .badge {
        font-weight: 500;
        padding: 0.35em 0.65em;
        font-size: 0.875rem;
    }

    /* Pagination styling */
    .pagination {
        margin-bottom: 0;
    }

    .pagination .page-link {
        background-color: var(--dark-bg-secondary);
        border-color: var(--dark-separator);
        color: var(--dark-text-primary);
    }

    .pagination .page-link:hover {
        background-color: var(--dark-bg-tertiary);
        border-color: var(--apple-blue);
        color: var(--apple-blue);
    }

    .pagination .page-item.active .page-link {
        background-color: var(--apple-blue);
        border-color: var(--apple-blue);
    }

    .pagination .page-item.disabled .page-link {
        background-color: var(--dark-bg-secondary);
        border-color: var(--dark-separator);
        color: var(--dark-text-tertiary);
    }
</style>
@endsection
