@extends('layouts.app')

@section('title', 'Manajemen Lowongan Kerja')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Manajemen Lowongan Kerja</h1>
            <p class="text-muted">Kelola lowongan pekerjaan dan track aplikasi masuk</p>
        </div>
        <a href="{{ route('admin.jobs.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Tambah Lowongan
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-dark border-dark shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avatar avatar-sm bg-success text-white rounded">
                                <i class="fas fa-briefcase"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <div class="small text-muted">Lowongan Aktif</div>
                            <h4 class="mb-0 text-white">{{ $vacancies->where('status', 'open')->count() }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-dark border-dark shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avatar avatar-sm bg-warning text-white rounded">
                                <i class="fas fa-clock"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <div class="small text-muted">Draft</div>
                            <h4 class="mb-0 text-white">{{ $vacancies->where('status', 'draft')->count() }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-dark border-dark shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avatar avatar-sm bg-danger text-white rounded">
                                <i class="fas fa-ban"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <div class="small text-muted">Ditutup</div>
                            <h4 class="mb-0 text-white">{{ $vacancies->where('status', 'closed')->count() }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-dark border-dark shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avatar avatar-sm bg-info text-white rounded">
                                <i class="fas fa-users"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <div class="small text-muted">Total Pelamar</div>
                            <h4 class="mb-0 text-white">{{ $vacancies->sum('applications_count') }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Job Vacancies Table -->
    <div class="card bg-dark border-dark shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-dark table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>Posisi</th>
                            <th>Tipe</th>
                            <th>Lokasi</th>
                            <th>Status</th>
                            <th>Deadline</th>
                            <th>Pelamar</th>
                            <th>Dibuat</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($vacancies as $vacancy)
                            <tr>
                                <td>
                                    <div class="fw-semibold text-white">{{ $vacancy->title }}</div>
                                    <div class="small text-muted">{{ $vacancy->position }}</div>
                                </td>
                                <td>
                                    <span class="badge bg-secondary">
                                        {{ ucfirst(str_replace('-', ' ', $vacancy->employment_type)) }}
                                    </span>
                                </td>
                                <td>
                                    <i class="fas fa-map-marker-alt text-muted me-1"></i>
                                    <span class="text-white">{{ $vacancy->location }}</span>
                                </td>
                                <td>
                                    @if($vacancy->status === 'open')
                                        <span class="badge bg-success">Aktif</span>
                                    @elseif($vacancy->status === 'draft')
                                        <span class="badge bg-warning">Draft</span>
                                    @else
                                        <span class="badge bg-danger">Ditutup</span>
                                    @endif
                                </td>
                                <td>
                                    @if($vacancy->deadline)
                                        <span class="text-white {{ $vacancy->deadline->isPast() ? 'text-danger' : '' }}">
                                            {{ $vacancy->deadline->format('d M Y') }}
                                        </span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('admin.applications.index', ['job_vacancy_id' => $vacancy->id]) }}" 
                                       class="badge bg-info text-decoration-none">
                                        {{ $vacancy->applications_count }} pelamar
                                    </a>
                                </td>
                                <td>
                                    <span class="text-muted small">{{ $vacancy->created_at->format('d M Y') }}</span>
                                </td>
                                <td class="text-end">
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('career.show', $vacancy->slug) }}" 
                                           class="btn btn-outline-secondary" 
                                           target="_blank"
                                           title="Lihat di Public">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.jobs.show', $vacancy->id) }}" 
                                           class="btn btn-outline-primary"
                                           title="Detail">
                                            <i class="fas fa-info-circle"></i>
                                        </a>
                                        <a href="{{ route('admin.jobs.edit', $vacancy->id) }}" 
                                           class="btn btn-outline-warning"
                                           title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" 
                                                class="btn btn-outline-danger" 
                                                onclick="confirmDelete({{ $vacancy->id }})"
                                                title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>

                                    <form id="delete-form-{{ $vacancy->id }}" 
                                          action="{{ route('admin.jobs.destroy', $vacancy->id) }}" 
                                          method="POST" 
                                          class="d-none">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5">
                                    <i class="fas fa-inbox text-muted fa-3x mb-3"></i>
                                    <p class="text-muted">Belum ada lowongan kerja dibuat.</p>
                                    <a href="{{ route('admin.jobs.create') }}" class="btn btn-primary">
                                        <i class="fas fa-plus me-2"></i>Buat Lowongan Pertama
                                    </a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($vacancies->hasPages())
                <div class="d-flex justify-content-center mt-4">
                    {{ $vacancies->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
function confirmDelete(id) {
    if (confirm('Yakin ingin menghapus lowongan ini? Semua aplikasi yang masuk juga akan terhapus!')) {
        document.getElementById('delete-form-' + id).submit();
    }
}
</script>
@endpush
@endsection
