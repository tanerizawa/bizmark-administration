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
            <div class="flex gap-2">
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
            <div class="card stat-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon bg-primary">
                            <i class="fas fa-briefcase"></i>
                        </div>
                        <div class="ms-3">
                            <p class="text-sm text-dark-text-secondary mb-1">Total Proyek</p>
                            <h3 class="text-2xl font-semibold text-dark-text-primary mb-0">{{ $client->projects->count() }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card stat-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon bg-success">
                            <i class="fas fa-tasks"></i>
                        </div>
                        <div class="ms-3">
                            <p class="text-sm text-dark-text-secondary mb-1">Proyek Aktif</p>
                            <h3 class="text-2xl font-semibold text-dark-text-primary mb-0">{{ $client->activeProjectsCount() }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card stat-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon bg-warning">
                            <i class="fas fa-money-bill-wave"></i>
                        </div>
                        <div class="ms-3">
                            <p class="text-sm text-dark-text-secondary mb-1">Total Nilai</p>
                            <h3 class="text-lg font-semibold text-dark-text-primary mb-0">Rp {{ number_format($client->totalProjectValue ?? 0, 0, ',', '.') }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card stat-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon bg-info">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div class="ms-3">
                            <p class="text-sm text-dark-text-secondary mb-1">Total Dibayar</p>
                            <h3 class="text-lg font-semibold text-dark-text-primary mb-0">Rp {{ number_format($client->totalPaid ?? 0, 0, ',', '.') }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Client Information -->
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header bg-dark-bg-secondary">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-info-circle me-2 text-apple-blue"></i>Informasi Klien
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
                                <span class="badge bg-info">Individual</span>
                            @elseif($client->client_type == 'company')
                                <span class="badge bg-primary">Perusahaan</span>
                            @else
                                <span class="badge bg-secondary">Pemerintah</span>
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
                                <span class="badge bg-success">Aktif</span>
                            @elseif($client->status == 'inactive')
                                <span class="badge bg-danger">Tidak Aktif</span>
                            @else
                                <span class="badge bg-warning">Potensial</span>
                            @endif
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contact Information -->
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header bg-dark-bg-secondary">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-address-book me-2 text-apple-blue"></i>Informasi Kontak
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
                        <p><a href="mailto:{{ $client->email }}" class="text-apple-blue hover:underline">{{ $client->email }}</a></p>
                    </div>
                    @endif

                    @if($client->phone)
                    <div class="info-group">
                        <label>Telepon</label>
                        <p><a href="tel:{{ $client->phone }}" class="text-apple-blue hover:underline">{{ $client->phone }}</a></p>
                    </div>
                    @endif

                    @if($client->mobile)
                    <div class="info-group">
                        <label>Handphone / WhatsApp</label>
                        <p>
                            <a href="tel:{{ $client->mobile }}" class="text-apple-blue hover:underline">{{ $client->mobile }}</a>
                            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $client->mobile) }}" target="_blank" class="btn btn-sm btn-success ms-2">
                                <i class="fab fa-whatsapp me-1"></i>WhatsApp
                            </a>
                        </p>
                    </div>
                    @endif

                    @if(!$client->contact_person && !$client->email && !$client->phone && !$client->mobile)
                    <p class="text-dark-text-tertiary text-sm">Tidak ada informasi kontak tersedia</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Address Information -->
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header bg-dark-bg-secondary">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-map-marker-alt me-2 text-apple-blue"></i>Alamat
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
                    <p class="text-dark-text-tertiary text-sm">Tidak ada informasi alamat tersedia</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Tax Information -->
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header bg-dark-bg-secondary">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-file-invoice me-2 text-apple-blue"></i>Informasi Pajak
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
                    <p class="text-dark-text-tertiary text-sm">Tidak ada informasi pajak tersedia</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Notes -->
        @if($client->notes)
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-dark-bg-secondary">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-sticky-note me-2 text-apple-blue"></i>Catatan
                    </h5>
                </div>
                <div class="card-body">
                    <p class="mb-0 whitespace-pre-line">{{ $client->notes }}</p>
                </div>
            </div>
        </div>
        @endif

        <!-- Projects List -->
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-dark-bg-secondary d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-folder me-2 text-apple-blue"></i>Daftar Proyek ({{ $client->projects->count() }})
                    </h5>
                    <a href="{{ route('projects.create', ['client_id' => $client->id]) }}" class="btn btn-sm btn-primary">
                        <i class="fas fa-plus me-2"></i>Tambah Proyek
                    </a>
                </div>
                <div class="card-body p-0">
                    @if($client->projects->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="bg-dark-bg-tertiary">
                                <tr>
                                    <th>No Proyek</th>
                                    <th>Nama Proyek</th>
                                    <th>Status</th>
                                    <th>Nilai Proyek</th>
                                    <th>Tanggal</th>
                                    <th width="100">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($client->projects as $project)
                                <tr>
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
                                        <a href="{{ route('projects.show', $project) }}" class="btn btn-sm btn-info" title="Lihat Detail">
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
        transition: transform 0.2s ease;
    }

    .stat-card:hover {
        transform: translateY(-2px);
    }

    .stat-icon {
        width: 50px;
        height: 50px;
        border-radius: 12px;
        display: flex;
        align-items: center;
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

    .whitespace-pre-line {
        white-space: pre-line;
    }
</style>
@endsection
