@extends('layouts.app')

@section('title', 'Detail Klien - ' . $client->name)

@section('content')
<div class="container-fluid px-4 py-6">
    <!-- Header -->
    <div class="mb-6">
        <div class="d-flex justify-content-between align-items-start">
            <div>
                <h1 class="text-dark-text-primary mb-2" style="font-size: 1.75rem; font-weight: 600;">Detail Klien</h1>
                <div class="d-flex gap-3 text-dark-text-secondary" style="font-size: 0.875rem;">
                    <span><i class="fas fa-calendar-alt me-2"></i>Terdaftar: {{ $client->created_at->format('d M Y') }}</span>
                    <span><i class="fas fa-folder me-2"></i>{{ $client->projects->count() }} Proyek</span>
                </div>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('clients.edit', $client) }}" class="btn btn-warning">
                    <i class="fas fa-edit me-2"></i>Edit
                </a>
                <a href="{{ route('clients.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Kembali
                </a>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row g-4 mb-6">
        <div class="col-md-3">
            <div class="card stat-card" style="background-color: var(--dark-bg-secondary); border: 1px solid var(--dark-separator);">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon" style="background: linear-gradient(135deg, var(--apple-blue) 0%, var(--apple-blue-dark) 100%);">
                            <i class="fas fa-briefcase"></i>
                        </div>
                        <div class="ms-3">
                            <p class="text-dark-text-secondary mb-1" style="font-size: 0.875rem;">Total Proyek</p>
                            <h3 class="text-dark-text-primary mb-0" style="font-size: 1.5rem; font-weight: 600;">{{ $client->projects->count() }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card stat-card" style="background-color: var(--dark-bg-secondary); border: 1px solid var(--dark-separator);">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon" style="background: linear-gradient(135deg, var(--apple-green) 0%, #28a745 100%);">
                            <i class="fas fa-tasks"></i>
                        </div>
                        <div class="ms-3">
                            <p class="text-dark-text-secondary mb-1" style="font-size: 0.875rem;">Proyek Aktif</p>
                            <h3 class="text-dark-text-primary mb-0" style="font-size: 1.5rem; font-weight: 600;">{{ $client->activeProjectsCount() }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card stat-card" style="background-color: var(--dark-bg-secondary); border: 1px solid var(--dark-separator);">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon" style="background: linear-gradient(135deg, var(--apple-orange) 0%, #fd7e14 100%);">
                            <i class="fas fa-money-bill-wave"></i>
                        </div>
                        <div class="ms-3">
                            <p class="text-dark-text-secondary mb-1" style="font-size: 0.875rem;">Total Nilai</p>
                            <h3 class="text-dark-text-primary mb-0" style="font-size: 1.125rem; font-weight: 600;">Rp {{ number_format($client->totalProjectValue ?? 0, 0, ',', '.') }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card stat-card" style="background-color: var(--dark-bg-secondary); border: 1px solid var(--dark-separator);">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon" style="background: linear-gradient(135deg, var(--apple-teal) 0%, #17a2b8 100%);">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div class="ms-3">
                            <p class="text-dark-text-secondary mb-1" style="font-size: 0.875rem;">Total Dibayar</p>
                            <h3 class="text-dark-text-primary mb-0" style="font-size: 1.125rem; font-weight: 600;">Rp {{ number_format($client->totalPaid ?? 0, 0, ',', '.') }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Client Information -->
        <div class="col-md-6">
            <div class="card h-100" style="background-color: var(--dark-bg-secondary); border: 1px solid var(--dark-separator);">
                <div class="card-header" style="background-color: var(--dark-bg-tertiary); border-bottom: 1px solid var(--dark-separator);">
                    <h5 class="card-title mb-0 text-dark-text-primary" style="font-weight: 600;">
                        <i class="fas fa-info-circle me-2" style="color: var(--apple-blue);"></i>Informasi Klien
                    </h5>
                </div>
                <div class="card-body">
                    <div class="info-group">
                        <label>Nama Klien</label>
                        <p>{{ $client->name }}</p>
                    </div>

                    @if($client->company_name)
                    <div class="info-group">
                        <label>Nama Perusahaan</label>
                        <p>{{ $client->company_name }}</p>
                    </div>
                    @endif

                    <div class="info-group">
                        <label>Tipe Klien</label>
                        <p>
                            @if($client->client_type == 'individual')
                                <span class="badge" style="background-color: var(--apple-teal);">Individual</span>
                            @elseif($client->client_type == 'company')
                                <span class="badge" style="background-color: var(--apple-blue);">Perusahaan</span>
                            @else
                                <span class="badge" style="background-color: var(--dark-bg-tertiary);">Pemerintah</span>
                            @endif
                        </p>
                    </div>

                    @if($client->industry)
                    <div class="info-group">
                        <label>Industri</label>
                        <p>{{ $client->industry }}</p>
                    </div>
                    @endif

                    <div class="info-group">
                        <label>Status</label>
                        <p>
                            @if($client->status == 'active')
                                <span class="badge" style="background-color: var(--apple-green);">Aktif</span>
                            @elseif($client->status == 'inactive')
                                <span class="badge" style="background-color: var(--apple-red);">Tidak Aktif</span>
                            @else
                                <span class="badge" style="background-color: var(--apple-orange);">Potensial</span>
                            @endif
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contact Information -->
        <div class="col-md-6">
            <div class="card h-100" style="background-color: var(--dark-bg-secondary); border: 1px solid var(--dark-separator);">
                <div class="card-header" style="background-color: var(--dark-bg-tertiary); border-bottom: 1px solid var(--dark-separator);">
                    <h5 class="card-title mb-0 text-dark-text-primary" style="font-weight: 600;">
                        <i class="fas fa-address-book me-2" style="color: var(--apple-blue);"></i>Informasi Kontak
                    </h5>
                </div>
                <div class="card-body">
                    @if($client->contact_person)
                    <div class="info-group">
                        <label>Contact Person</label>
                        <p>{{ $client->contact_person }}</p>
                    </div>
                    @endif

                    @if($client->email)
                    <div class="info-group">
                        <label>Email</label>
                        <p><a href="mailto:{{ $client->email }}" style="color: var(--apple-blue); text-decoration: none;">{{ $client->email }}</a></p>
                    </div>
                    @endif

                    @if($client->phone)
                    <div class="info-group">
                        <label>Telepon</label>
                        <p><a href="tel:{{ $client->phone }}" style="color: var(--apple-blue); text-decoration: none;">{{ $client->phone }}</a></p>
                    </div>
                    @endif

                    @if($client->mobile)
                    <div class="info-group">
                        <label>Handphone / WhatsApp</label>
                        <p>
                            <a href="tel:{{ $client->mobile }}" style="color: var(--apple-blue); text-decoration: none;">{{ $client->mobile }}</a>
                            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $client->mobile) }}" target="_blank" class="btn btn-sm ms-2" style="background-color: var(--apple-green); color: white; border: none;">
                                <i class="fab fa-whatsapp me-1"></i>WhatsApp
                            </a>
                        </p>
                    </div>
                    @endif

                    @if(!$client->contact_person && !$client->email && !$client->phone && !$client->mobile)
                    <p class="text-dark-text-tertiary" style="font-size: 0.875rem;">Tidak ada informasi kontak tersedia</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Address Information -->
        <div class="col-md-6">
            <div class="card h-100" style="background-color: var(--dark-bg-secondary); border: 1px solid var(--dark-separator);">
                <div class="card-header" style="background-color: var(--dark-bg-tertiary); border-bottom: 1px solid var(--dark-separator);">
                    <h5 class="card-title mb-0 text-dark-text-primary" style="font-weight: 600;">
                        <i class="fas fa-map-marker-alt me-2" style="color: var(--apple-blue);"></i>Alamat
                    </h5>
                </div>
                <div class="card-body">
                    @if($client->address)
                    <div class="info-group">
                        <label>Alamat Lengkap</label>
                        <p>{{ $client->address }}</p>
                    </div>
                    @endif

                    @if($client->city || $client->province || $client->postal_code)
                    <div class="info-group">
                        <label>Kota / Provinsi</label>
                        <p>{{ $client->city }}{{ $client->city && $client->province ? ', ' : '' }}{{ $client->province }}{{ $client->postal_code ? ' - ' . $client->postal_code : '' }}</p>
                    </div>
                    @endif

                    @if(!$client->address && !$client->city && !$client->province)
                    <p class="text-dark-text-tertiary" style="font-size: 0.875rem;">Tidak ada informasi alamat tersedia</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Tax Information -->
        <div class="col-md-6">
            <div class="card h-100" style="background-color: var(--dark-bg-secondary); border: 1px solid var(--dark-separator);">
                <div class="card-header" style="background-color: var(--dark-bg-tertiary); border-bottom: 1px solid var(--dark-separator);">
                    <h5 class="card-title mb-0 text-dark-text-primary" style="font-weight: 600;">
                        <i class="fas fa-file-invoice me-2" style="color: var(--apple-blue);"></i>Informasi Pajak
                    </h5>
                </div>
                <div class="card-body">
                    @if($client->npwp)
                    <div class="info-group">
                        <label>NPWP</label>
                        <p>{{ $client->npwp }}</p>
                    </div>
                    @endif

                    @if($client->tax_name)
                    <div class="info-group">
                        <label>Nama di NPWP</label>
                        <p>{{ $client->tax_name }}</p>
                    </div>
                    @endif

                    @if($client->tax_address)
                    <div class="info-group">
                        <label>Alamat NPWP</label>
                        <p>{{ $client->tax_address }}</p>
                    </div>
                    @endif

                    @if(!$client->npwp && !$client->tax_name && !$client->tax_address)
                    <p class="text-dark-text-tertiary" style="font-size: 0.875rem;">Tidak ada informasi pajak tersedia</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Notes -->
        @if($client->notes)
        <div class="col-12">
            <div class="card" style="background-color: var(--dark-bg-secondary); border: 1px solid var(--dark-separator);">
                <div class="card-header" style="background-color: var(--dark-bg-tertiary); border-bottom: 1px solid var(--dark-separator);">
                    <h5 class="card-title mb-0 text-dark-text-primary" style="font-weight: 600;">
                        <i class="fas fa-sticky-note me-2" style="color: var(--apple-blue);"></i>Catatan
                    </h5>
                </div>
                <div class="card-body">
                    <p class="mb-0 text-dark-text-primary" style="white-space: pre-line;">{{ $client->notes }}</p>
                </div>
            </div>
        </div>
        @endif

        <!-- Projects List -->
        <div class="col-12">
            <div class="card" style="background-color: var(--dark-bg-secondary); border: 1px solid var(--dark-separator);">
                <div class="card-header d-flex justify-content-between align-items-center" style="background-color: var(--dark-bg-tertiary); border-bottom: 1px solid var(--dark-separator);">
                    <h5 class="card-title mb-0 text-dark-text-primary" style="font-weight: 600;">
                        <i class="fas fa-folder me-2" style="color: var(--apple-blue);"></i>Daftar Proyek ({{ $client->projects->count() }})
                    </h5>
                    <a href="{{ route('projects.create', ['client_id' => $client->id]) }}" class="btn btn-sm btn-primary">
                        <i class="fas fa-plus me-2"></i>Tambah Proyek
                    </a>
                </div>
                <div class="card-body p-0">
                    @if($client->projects->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover mb-0" style="color: var(--dark-text-primary);">
                            <thead style="background-color: var(--dark-bg-tertiary);">
                                <tr>
                                    <th style="color: var(--dark-text-primary);">No Proyek</th>
                                    <th style="color: var(--dark-text-primary);">Nama Proyek</th>
                                    <th style="color: var(--dark-text-primary);">Status</th>
                                    <th style="color: var(--dark-text-primary);">Nilai Proyek</th>
                                    <th style="color: var(--dark-text-primary);">Tanggal</th>
                                    <th width="100" style="color: var(--dark-text-primary);">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($client->projects as $project)
                                <tr style="border-bottom: 1px solid var(--dark-separator);">
                                    <td><strong>{{ $project->project_number }}</strong></td>
                                    <td>{{ $project->project_name }}</td>
                                    <td>
                                        <span class="badge" style="background-color: {{ $project->status->color ?? '#6b7280' }}">
                                            {{ $project->status->name ?? 'N/A' }}
                                        </span>
                                    </td>
                                    <td>Rp {{ number_format($project->project_value ?? 0, 0, ',', '.') }}</td>
                                    <td>{{ $project->created_at->format('d M Y') }}</td>
                                    <td>
                                        <a href="{{ route('projects.show', $project) }}" class="btn btn-sm" style="background-color: var(--apple-teal); color: white; border: none;" title="Lihat Detail">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="p-4 text-center">
                        <i class="fas fa-folder-open text-dark-text-tertiary mb-3" style="font-size: 3rem;"></i>
                        <p class="text-dark-text-secondary mb-3">Belum ada proyek untuk klien ini</p>
                        <a href="{{ route('projects.create', ['client_id' => $client->id]) }}" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>Tambah Proyek Pertama
                        </a>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .stat-card {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.4);
    }

    .stat-icon {
        width: 50px;
        height: 50px;
        border-radius: 12px;
        display: flex;
        align-items-center;
        justify-content: center;
        color: white;
        font-size: 1.5rem;
    }

    .info-group {
        margin-bottom: 1.5rem;
    }

    .info-group:last-child {
        margin-bottom: 0;
    }

    .info-group label {
        display: block;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: var(--dark-text-tertiary);
        margin-bottom: 0.375rem;
    }

    .info-group p {
        font-size: 0.95rem;
        color: var(--dark-text-primary);
        margin-bottom: 0;
    }

    .table tbody tr:hover {
        background-color: var(--dark-bg-tertiary) !important;
    }
</style>
@endsection
