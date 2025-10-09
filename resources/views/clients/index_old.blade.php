@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">
            <i class="fas fa-users me-2"></i>Manajemen Klien
        </h1>
        <a href="{{ route('clients.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Tambah Klien
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Filter & Search -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('clients.index') }}" class="row g-3">
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control" placeholder="Cari klien..." value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <select name="status" class="form-select">
                        <option value="">Semua Status</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Aktif</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Tidak Aktif</option>
                        <option value="potential" {{ request('status') == 'potential' ? 'selected' : '' }}>Potensial</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="client_type" class="form-select">
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

    <!-- Clients Table -->
    <div class="card" style="background-color: var(--dark-bg-secondary); border: 1px solid var(--dark-separator);">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Nama Klien</th>
                            <th>Perusahaan</th>
                            <th>Kontak</th>
                            <th>Tipe</th>
                            <th>Status</th>
                            <th>Jumlah Project</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($clients as $client)
                            <tr>
                                <td>
                                    <div class="fw-bold">{{ $client->name }}</div>
                                    @if($client->contact_person)
                                        <small class="text-muted">{{ $client->contact_person }}</small>
                                    @endif
                                </td>
                                <td>
                                    {{ $client->company_name ?? '-' }}
                                    @if($client->industry)
                                        <br><small class="text-muted">{{ $client->industry }}</small>
                                    @endif
                                </td>
                                <td>
                                    @if($client->email)
                                        <div><i class="fas fa-envelope me-1"></i>{{ $client->email }}</div>
                                    @endif
                                    @if($client->phone)
                                        <div><i class="fas fa-phone me-1"></i>{{ $client->phone }}</div>
                                    @endif
                                    @if($client->mobile)
                                        <div><i class="fab fa-whatsapp me-1"></i>{{ $client->mobile }}</div>
                                    @endif
                                </td>
                                <td>
                                    @if($client->client_type == 'individual')
                                        <span class="badge bg-info">Individual</span>
                                    @elseif($client->client_type == 'company')
                                        <span class="badge bg-primary">Perusahaan</span>
                                    @else
                                        <span class="badge bg-success">Pemerintah</span>
                                    @endif
                                </td>
                                <td>
                                    @if($client->status == 'active')
                                        <span class="badge bg-success">Aktif</span>
                                    @elseif($client->status == 'inactive')
                                        <span class="badge bg-secondary">Tidak Aktif</span>
                                    @else
                                        <span class="badge bg-warning">Potensial</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-primary rounded-pill">
                                        {{ $client->projects->count() }} Project
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a href="{{ route('clients.show', $client) }}" class="btn btn-info" title="Detail">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('clients.edit', $client) }}" class="btn btn-warning" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('clients.destroy', $client) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus klien ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger" title="Hapus">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <i class="fas fa-users fa-3x mb-3 d-block"></i>
                                    Belum ada data klien
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($clients->hasPages())
            <div class="card-footer">
                {{ $clients->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
