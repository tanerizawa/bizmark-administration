@extends('layouts.app')

@section('title', 'Lamaran Masuk')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Lamaran Masuk</h1>
            <p class="text-muted">Review dan kelola aplikasi kandidat</p>
        </div>
        <a href="{{ route('admin.jobs.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-briefcase me-2"></i>Kelola Lowongan
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Status Stats -->
    <div class="row mb-4">
        <div class="col-md-2">
            <a href="{{ route('admin.applications.index') }}" class="text-decoration-none">
                <div class="card bg-dark border-dark shadow-sm {{ !request('status') ? 'border-primary' : '' }}">
                    <div class="card-body text-center">
                        <h4 class="mb-1 text-white">{{ $applications->total() }}</h4>
                        <small class="text-muted">Semua</small>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-2">
            <a href="{{ route('admin.applications.index', ['status' => 'pending']) }}" class="text-decoration-none">
                <div class="card bg-dark border-dark shadow-sm {{ request('status') === 'pending' ? 'border-warning' : '' }}">
                    <div class="card-body text-center">
                        <h4 class="mb-1 text-warning">{{ $statusCounts['pending'] ?? 0 }}</h4>
                        <small class="text-muted">Pending</small>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-2">
            <a href="{{ route('admin.applications.index', ['status' => 'reviewed']) }}" class="text-decoration-none">
                <div class="card bg-dark border-dark shadow-sm {{ request('status') === 'reviewed' ? 'border-info' : '' }}">
                    <div class="card-body text-center">
                        <h4 class="mb-1 text-info">{{ $statusCounts['reviewed'] ?? 0 }}</h4>
                        <small class="text-muted">Direview</small>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-2">
            <a href="{{ route('admin.applications.index', ['status' => 'interview']) }}" class="text-decoration-none">
                <div class="card bg-dark border-dark shadow-sm {{ request('status') === 'interview' ? 'border-purple' : '' }}">
                    <div class="card-body text-center">
                        <h4 class="mb-1 text-purple">{{ $statusCounts['interview'] ?? 0 }}</h4>
                        <small class="text-muted">Interview</small>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-2">
            <a href="{{ route('admin.applications.index', ['status' => 'accepted']) }}" class="text-decoration-none">
                <div class="card bg-dark border-dark shadow-sm {{ request('status') === 'accepted' ? 'border-success' : '' }}">
                    <div class="card-body text-center">
                        <h4 class="mb-1 text-success">{{ $statusCounts['accepted'] ?? 0 }}</h4>
                        <small class="text-muted">Diterima</small>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-2">
            <a href="{{ route('admin.applications.index', ['status' => 'rejected']) }}" class="text-decoration-none">
                <div class="card bg-dark border-dark shadow-sm {{ request('status') === 'rejected' ? 'border-danger' : '' }}">
                    <div class="card-body text-center">
                        <h4 class="mb-1 text-danger">{{ $statusCounts['rejected'] ?? 0 }}</h4>
                        <small class="text-muted">Ditolak</small>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="card bg-dark border-dark shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.applications.index') }}">
                <div class="row g-3">
                    <div class="col-md-4">
                        <input type="text" name="search" class="form-control bg-dark text-white border-secondary" 
                               placeholder="Cari nama atau email..." 
                               value="{{ request('search') }}">
                    </div>
                    <div class="col-md-3">
                        <select name="job_vacancy_id" class="form-select bg-dark text-white border-secondary">
                            <option value="">Semua Lowongan</option>
                            @foreach($vacancies as $vacancy)
                                <option value="{{ $vacancy->id }}" {{ request('job_vacancy_id') == $vacancy->id ? 'selected' : '' }}>
                                    {{ $vacancy->title }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select name="status" class="form-select bg-dark text-white border-secondary">
                            <option value="">Semua Status</option>
                            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="reviewed" {{ request('status') === 'reviewed' ? 'selected' : '' }}>Direview</option>
                            <option value="interview" {{ request('status') === 'interview' ? 'selected' : '' }}>Interview</option>
                            <option value="accepted" {{ request('status') === 'accepted' ? 'selected' : '' }}>Diterima</option>
                            <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Ditolak</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-filter me-2"></i>Filter
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Applications Table -->
    <div class="card bg-dark border-dark shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-dark table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>Kandidat</th>
                            <th>Lowongan</th>
                            <th>Pendidikan</th>
                            <th>Status</th>
                            <th>Tanggal Lamar</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($applications as $application)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar avatar-sm bg-primary text-white rounded-circle me-2">
                                            {{ substr($application->full_name, 0, 1) }}
                                        </div>
                                        <div>
                                            <div class="fw-semibold">{{ $application->full_name }}</div>
                                            <div class="small text-muted">
                                                <i class="fas fa-envelope me-1"></i>{{ $application->email }}
                                            </div>
                                            <div class="small text-muted">
                                                <i class="fas fa-phone me-1"></i>{{ $application->phone }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-semibold text-white">{{ $application->jobVacancy->title ?? '-' }}</div>
                                    @if($application->has_experience_ukl_upl)
                                        <span class="badge bg-success badge-sm">UKL-UPL Exp ✓</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="text-white">{{ $application->education_level }} {{ $application->major }}</div>
                                    <div class="small text-muted">{{ $application->institution }}</div>
                                    @if($application->gpa)
                                        <div class="small text-muted">IPK: {{ number_format($application->gpa, 2) }}</div>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge {{ $application->status_badge }}">
                                        {{ $application->status_label }}
                                    </span>
                                </td>
                                <td>
                                    <span class="text-muted small">
                                        {{ $application->created_at->format('d M Y H:i') }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('admin.applications.show', $application->id) }}" 
                                       class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye me-1"></i>Detail
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <i class="fas fa-inbox text-muted fa-3x mb-3"></i>
                                    <p class="text-muted">Belum ada lamaran masuk.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($applications->hasPages())
                <div class="d-flex justify-content-center mt-4">
                    {{ $applications->appends(request()->query())->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<style>
.text-purple { color: #6f42c1 !important; }
.border-purple { border-color: #6f42c1 !important; }
.avatar { width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; }
.avatar-sm { width: 32px; height: 32px; font-size: 0.875rem; }
</style>
@endsection
