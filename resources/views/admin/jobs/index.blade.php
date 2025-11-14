@extends('layouts.app')

@section('title', 'Manajemen Lowongan Kerja')

@section('content')
<div class="container-fluid">
    @php
        $openJobs = $vacancies->where('status', 'open')->count();
        $draftJobs = $vacancies->where('status', 'draft')->count();
        $closedJobs = $vacancies->where('status', 'closed')->count();
        $totalApplicants = $vacancies->sum('applications_count');
        $stats = [
            [
                'label' => 'Lowongan Aktif',
                'value' => $openJobs,
                'icon' => 'briefcase',
                'accent' => '#0A84FF',
                'tint' => 'rgba(10,132,255,0.18)',
            ],
            [
                'label' => 'Draft',
                'value' => $draftJobs,
                'icon' => 'clock',
                'accent' => '#FFD60A',
                'tint' => 'rgba(255,214,10,0.18)',
            ],
            [
                'label' => 'Ditutup',
                'value' => $closedJobs,
                'icon' => 'ban',
                'accent' => '#FF453A',
                'tint' => 'rgba(255,69,58,0.18)',
            ],
            [
                'label' => 'Total Pelamar',
                'value' => $totalApplicants,
                'icon' => 'users',
                'accent' => '#30D158',
                'tint' => 'rgba(48,209,88,0.18)',
            ],
        ];
    @endphp

    <!-- Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start gap-3 mb-4">
        <div>
            <p class="text-uppercase small mb-2" style="color: rgba(235,235,245,0.6);">Talent Hub</p>
            <h1 class="h4 mb-1 text-white">Manajemen Lowongan</h1>
            <p class="mb-0" style="color: rgba(235,235,245,0.65);">Monitor status rekrutmen dan tindak lanjuti pelamar aktif.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.jobs.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Tambah Lowongan
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Stats Cards -->
    <div class="row g-3 mb-4">
        @foreach ($stats as $stat)
            <div class="col-sm-6 col-lg-3">
                <div class="card h-100 border-0 text-white" style="background: rgba(255,255,255,0.02); border: 1px solid {{ $stat['tint'] }}; box-shadow: 0 10px 35px rgba(0,0,0,0.35);">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 46px; height: 46px; background: {{ $stat['tint'] }}; color: {{ $stat['accent'] }};">
                                <i class="fas fa-{{ $stat['icon'] }}"></i>
                            </div>
                            <div class="ms-3">
                                <p class="text-uppercase small mb-1" style="color: rgba(235,235,245,0.6);">{{ $stat['label'] }}</p>
                                <h4 class="mb-0 text-white">{{ $stat['value'] }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    @php
        $statusOptions = [
            '' => 'Semua Status',
            'open' => 'Aktif',
            'draft' => 'Draft',
            'closed' => 'Ditutup',
        ];
        $employmentOptions = $vacancies->pluck('employment_type')->filter()->unique()->values();
        $locationOptions = $vacancies->pluck('location')->filter()->unique()->values();
    @endphp

    <div class="card bg-dark border-dark shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.jobs.index') }}">
                <div class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label small text-uppercase" style="color: rgba(235,235,245,0.8);">Pencarian</label>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text" style="background: rgba(255,255,255,0.08); border-color: rgba(255,255,255,0.2); color: rgba(235,235,245,0.6);">
                                <i class="fas fa-search"></i>
                            </span>
                            <input type="text"
                                   name="search"
                                   value="{{ request('search') }}"
                                   class="form-control text-white"
                                   style="background: rgba(255,255,255,0.08); border-color: rgba(255,255,255,0.2);"
                                   placeholder="Judul, posisi, atau lokasi" />
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small text-uppercase" style="color: rgba(235,235,245,0.8);">Status</label>
                        <select name="status" class="form-select form-select-sm text-white" style="background: rgba(255,255,255,0.08); border-color: rgba(255,255,255,0.2);">
                            @foreach($statusOptions as $value => $label)
                                <option value="{{ $value }}" {{ request('status') === $value ? 'selected' : '' }} style="background: #1C1C1E; color: #FFFFFF;">
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small text-uppercase" style="color: rgba(235,235,245,0.8);">Tipe</label>
                        <select name="employment_type" class="form-select form-select-sm text-white" style="background: rgba(255,255,255,0.08); border-color: rgba(255,255,255,0.2);">
                            <option value="" style="background: #1C1C1E; color: #FFFFFF;">Semua Tipe</option>
                            @foreach($employmentOptions as $type)
                                <option value="{{ $type }}" {{ request('employment_type') === $type ? 'selected' : '' }} style="background: #1C1C1E; color: #FFFFFF;">
                                    {{ ucfirst(str_replace('-', ' ', $type)) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small text-uppercase" style="color: rgba(235,235,245,0.8);">Lokasi</label>
                        <select name="location" class="form-select form-select-sm text-white" style="background: rgba(255,255,255,0.08); border-color: rgba(255,255,255,0.2);">
                            <option value="" style="background: #1C1C1E; color: #FFFFFF;">Semua Lokasi</option>
                            @foreach($locationOptions as $location)
                                <option value="{{ $location }}" {{ request('location') === $location ? 'selected' : '' }} style="background: #1C1C1E; color: #FFFFFF;">
                                    {{ $location }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Job Vacancies Table -->
    @php
        $statusMeta = [
            'open' => ['label' => 'Aktif', 'bg' => 'rgba(48,209,88,0.18)', 'color' => '#30D158'],
            'draft' => ['label' => 'Draft', 'bg' => 'rgba(255,214,10,0.2)', 'color' => '#FFD60A'],
            'closed' => ['label' => 'Ditutup', 'bg' => 'rgba(255,69,58,0.18)', 'color' => '#FF453A'],
        ];
    @endphp

    <div class="card bg-dark border-dark shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-dark table-hover table-striped table-borderless align-middle mb-0" style="--bs-table-bg: rgba(255,255,255,0.02); --bs-table-striped-bg: rgba(255,255,255,0.05); --bs-table-striped-color: #fff;">
                    <thead class="text-uppercase small" style="color: rgba(235,235,245,0.7);">
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
                                    <div class="small" style="color: rgba(235,235,245,0.6);">{{ $vacancy->position ?? 'Posisi belum diisi' }}</div>
                                </td>
                                <td class="text-nowrap">
                                    <span class="badge rounded-pill px-3" style="background: rgba(255,255,255,0.15); color: rgba(235,235,245,0.9);">
                                        {{ $vacancy->employment_type ? ucfirst(str_replace('-', ' ', $vacancy->employment_type)) : 'N/A' }}
                                    </span>
                                </td>
                                <td class="text-nowrap">
                                    <i class="fas fa-map-marker-alt me-1" style="color: rgba(235,235,245,0.5);"></i>
                                    <span class="text-white">{{ $vacancy->location ?? '-' }}</span>
                                </td>
                                <td class="text-nowrap">
                                    @php
                                        $meta = $statusMeta[$vacancy->status] ?? ['label' => ucfirst($vacancy->status), 'bg' => 'rgba(255,255,255,0.15)', 'color' => '#FFFFFF'];
                                    @endphp
                                    <span class="badge rounded-pill px-3" style="background: {{ $meta['bg'] }}; color: {{ $meta['color'] }};">
                                        {{ $meta['label'] }}
                                    </span>
                                </td>
                                <td class="text-nowrap">
                                    @if($vacancy->deadline)
                                        @php
                                            $isOverdue = $vacancy->deadline->isPast();
                                            $isSoon = !$isOverdue && $vacancy->deadline->diffInDays(now()) <= 7;
                                        @endphp
                                        <span class="{{ $isOverdue ? 'text-danger' : ($isSoon ? 'text-warning' : 'text-white') }}">
                                            {{ $vacancy->deadline->format('d M Y') }}
                                        </span>
                                    @else
                                        <span style="color: rgba(235,235,245,0.5);">-</span>
                                    @endif
                                </td>
                                <td class="text-nowrap">
                                    <a href="{{ route('admin.applications.index', ['job_vacancy_id' => $vacancy->id]) }}" 
                                       class="badge bg-info text-decoration-none text-white">
                                        {{ $vacancy->applications_count }} pelamar
                                    </a>
                                </td>
                                <td class="text-nowrap">
                                    <span class="small" style="color: rgba(235,235,245,0.6);">{{ $vacancy->created_at->format('d M Y') }}</span>
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
                                    <i class="fas fa-inbox fa-3x mb-3" style="color: rgba(235,235,245,0.3);"></i>
                                    <p class="mb-3" style="color: rgba(235,235,245,0.6);">Belum ada lowongan yang dipublikasikan.</p>
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

document.addEventListener('DOMContentLoaded', function () {
    const filterForm = document.querySelector('form[action="{{ route('admin.jobs.index') }}"]');
    if (!filterForm) return;
    filterForm.querySelectorAll('select').forEach(select => {
        select.addEventListener('change', () => filterForm.submit());
    });
    const searchInput = filterForm.querySelector('input[name="search"]');
    if (searchInput) {
        searchInput.addEventListener('keydown', function (e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                filterForm.submit();
            }
        });
    }
});
</script>
@endpush
@endsection
