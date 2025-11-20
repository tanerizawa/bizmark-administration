@extends('layouts.app')

@section('title', 'Detail Lamaran - ' . $application->full_name)

@section('content')
<div class="container-fluid">
    <div class="mb-4">
        <a href="{{ route('admin.applications.index') }}" class="btn btn-sm btn-outline-secondary mb-3">
            <i class="fas fa-arrow-left me-2"></i>Kembali ke Daftar
        </a>
        <h1 class="h3 mb-1">Detail Lamaran</h1>
        <p class="text-muted">{{ $application->jobVacancy->title ?? 'N/A' }}</p>
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

    <div class="row">
        <!-- Main Content -->
        <div class="col-lg-8">
            <!-- Personal Information -->
            <div class="card bg-dark border-dark shadow-sm mb-4">
                <div class="card-body">
                    <h5 class="card-title mb-4 text-white">Data Pribadi</h5>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">
                                <i class="fas fa-user me-1"></i>Nama Lengkap
                            </label>
                            <div class="fw-semibold text-white">{{ $application->full_name }}</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">
                                <i class="fas fa-envelope me-1"></i>Email
                            </label>
                            <div>
                                <a href="mailto:{{ $application->email }}" class="text-decoration-none text-info">
                                    {{ $application->email }}
                                </a>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">
                                <i class="fas fa-phone me-1"></i>Nomor Telepon
                            </label>
                            <div>
                                <a href="tel:{{ $application->phone }}" class="text-decoration-none text-info">
                                    {{ $application->phone }}
                                </a>
                                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $application->phone) }}" 
                                   class="btn btn-sm btn-success ms-2" target="_blank">
                                    <i class="fab fa-whatsapp me-1"></i> WhatsApp
                                </a>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">
                                <i class="fas fa-calendar-alt me-1"></i>Tanggal Lahir
                            </label>
                            <div class="text-white">{{ $application->birth_date ? $application->birth_date->format('d M Y') : '-' }}</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">
                                <i class="fas fa-venus-mars me-1"></i>Jenis Kelamin
                            </label>
                            <div class="text-white">{{ $application->gender ?? '-' }}</div>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="text-muted small">
                                <i class="fas fa-map-marker-alt me-1"></i>Alamat
                            </label>
                            <div class="text-white">{{ $application->address ?? '-' }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Education -->
            <div class="card bg-dark border-dark shadow-sm mb-4">
                <div class="card-body">
                    <h5 class="card-title mb-4 text-white">
                        <i class="fas fa-graduation-cap me-2"></i>Pendidikan
                    </h5>
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label class="text-muted small">
                                <i class="fas fa-layer-group me-1"></i>Jenjang
                            </label>
                            <div class="fw-semibold text-white">{{ $application->education_level }}</div>
                        </div>
                        <div class="col-md-5 mb-3">
                            <label class="text-muted small">
                                <i class="fas fa-book me-1"></i>Jurusan
                            </label>
                            <div class="fw-semibold text-white">{{ $application->major }}</div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="text-muted small">
                                <i class="fas fa-star me-1"></i>IPK
                            </label>
                            <div class="text-white">{{ $application->gpa ? number_format($application->gpa, 2) : '-' }}</div>
                        </div>
                        <div class="col-md-8 mb-3">
                            <label class="text-muted small">
                                <i class="fas fa-university me-1"></i>Institusi
                            </label>
                            <div class="text-white">{{ $application->institution }}</div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="text-muted small">
                                <i class="fas fa-calendar-check me-1"></i>Tahun Lulus
                            </label>
                            <div class="text-white">{{ $application->graduation_year ?? '-' }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Experience -->
            <div class="card bg-dark border-dark shadow-sm mb-4">
                <div class="card-body">
                    <h5 class="card-title mb-4 text-white">
                        <i class="fas fa-briefcase me-2"></i>Pengalaman & Keahlian
                    </h5>
                    
                    @if($application->has_experience_ukl_upl)
                        <div class="alert alert-success bg-success bg-opacity-25 border-success text-white mb-3">
                            <i class="fas fa-badge-check me-2"></i>
                            <strong>Memiliki pengalaman UKL-UPL/Kajian Teknis</strong>
                        </div>
                    @endif

                    @if($application->work_experience && count($application->work_experience) > 0)
                        <h6 class="mb-3 text-white">
                            <i class="fas fa-history me-2"></i>Riwayat Pekerjaan
                        </h6>
                        @foreach($application->work_experience as $exp)
                            <div class="mb-3 pb-3 border-bottom border-secondary">
                                <div class="fw-semibold text-white">{{ $exp['position'] ?? 'N/A' }}</div>
                                <div class="text-muted">{{ $exp['company'] ?? 'N/A' }}</div>
                                <div class="text-muted small">{{ $exp['duration'] ?? 'N/A' }}</div>
                                @if(!empty($exp['responsibilities']))
                                    <div class="mt-2 text-white">{{ $exp['responsibilities'] }}</div>
                                @endif
                            </div>
                        @endforeach
                    @else
                        <p class="text-muted">Belum ada pengalaman kerja tercatat.</p>
                    @endif

                    @if($application->skills && count($application->skills) > 0)
                        <h6 class="mt-4 mb-3 text-white">
                            <i class="fas fa-tools me-2"></i>Keahlian
                        </h6>
                        <div class="d-flex flex-wrap gap-2">
                            @foreach($application->skills as $skill)
                                <span class="badge bg-primary">
                                    <i class="fas fa-check-circle me-1"></i>{{ $skill }}
                                </span>
                            @endforeach
                        </div>
                    @endif

                    @if($application->expected_salary || $application->available_from)
                        <h6 class="mt-4 mb-3 text-white">
                            <i class="fas fa-info-circle me-2"></i>Informasi Tambahan
                        </h6>
                        <div class="row">
                            @if($application->expected_salary)
                                <div class="col-md-6 mb-2">
                                    <label class="text-muted small">
                                        <i class="fas fa-money-bill-wave me-1"></i>Ekspektasi Gaji
                                    </label>
                                    <div class="text-white">Rp {{ number_format($application->expected_salary, 0, ',', '.') }}</div>
                                </div>
                            @endif
                            @if($application->available_from)
                                <div class="col-md-6 mb-2">
                                    <label class="text-muted small">
                                        <i class="fas fa-calendar-day me-1"></i>Bisa Mulai Kerja
                                    </label>
                                    <div class="text-white">{{ $application->available_from->format('d M Y') }}</div>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            </div>

            <!-- Cover Letter -->
            @if($application->cover_letter)
                <div class="card bg-dark border-dark shadow-sm mb-4">
                    <div class="card-body">
                        <h5 class="card-title mb-3 text-white">
                            <i class="fas fa-file-alt me-2"></i>Surat Lamaran
                        </h5>
                        <p class="mb-0 text-white">{{ $application->cover_letter }}</p>
                    </div>
                </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Status Card -->
            <div class="card bg-dark border-dark shadow-sm mb-4">
                <div class="card-body">
                    <h5 class="card-title mb-4 text-white">Status Lamaran</h5>
                    
                    <div class="mb-3 text-center">
                        @php
                            $statusConfig = [
                                'pending' => ['badge' => 'bg-warning text-dark', 'icon' => 'fas fa-clock'],
                                'reviewed' => ['badge' => 'bg-info', 'icon' => 'fas fa-eye'],
                                'interview' => ['badge' => 'bg-purple', 'icon' => 'fas fa-users'],
                                'accepted' => ['badge' => 'bg-success', 'icon' => 'fas fa-check-circle'],
                                'rejected' => ['badge' => 'bg-danger', 'icon' => 'fas fa-times-circle'],
                            ];
                            $config = $statusConfig[$application->status] ?? ['badge' => 'bg-secondary', 'icon' => 'fas fa-question'];
                        @endphp
                        <span class="badge {{ $config['badge'] }} fs-6 px-4 py-2">
                            <i class="{{ $config['icon'] }} me-2"></i>{{ $application->status_label }}
                        </span>
                    </div>

                    <form action="{{ route('admin.applications.update-status', $application->id) }}" method="POST">
                        @csrf
                        @method('PATCH')

                        <div class="mb-3">
                            <label class="form-label text-white">Ubah Status</label>
                            <select name="status" class="form-select bg-dark text-white border-secondary" required>
                                <option value="pending" {{ $application->status === 'pending' ? 'selected' : '' }}>Pending - Menunggu Review</option>
                                <option value="reviewed" {{ $application->status === 'reviewed' ? 'selected' : '' }}>Direview - Sedang Ditinjau</option>
                                <option value="interview" {{ $application->status === 'interview' ? 'selected' : '' }}>Interview - Undangan Wawancara</option>
                                <option value="accepted" {{ $application->status === 'accepted' ? 'selected' : '' }}>Diterima - Lamaran Disetujui</option>
                                <option value="rejected" {{ $application->status === 'rejected' ? 'selected' : '' }}>Ditolak - Tidak Dilanjutkan</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-white">
                                <i class="fas fa-comment-alt me-2"></i>Catatan untuk Pelamar
                            </label>
                            <textarea name="notes" rows="4" class="form-control bg-dark text-white border-secondary" placeholder="Catatan akan dikirim ke pelamar via email...">{{ $application->notes }}</textarea>
                            <small class="text-muted">
                                <i class="fas fa-envelope me-1"></i>Email notifikasi akan dikirim otomatis ke pelamar
                            </small>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-paper-plane me-2"></i>Update Status & Kirim Email
                        </button>
                    </form>

                    @if($application->reviewed_at)
                        <div class="mt-3 pt-3 border-top border-secondary">
                            <small class="text-muted">
                                <div><i class="fas fa-calendar me-2"></i>Direview: {{ $application->reviewed_at->format('d M Y H:i') }}</div>
                                @if($application->reviewer)
                                    <div><i class="fas fa-user me-2"></i>Oleh: {{ $application->reviewer->name }}</div>
                                @endif
                            </small>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Documents -->
            <div class="card bg-dark border-dark shadow-sm mb-4">
                <div class="card-body">
                    <h5 class="card-title mb-4 text-white">
                        <i class="fas fa-folder-open me-2"></i>Dokumen
                    </h5>
                    
                    @if($application->cv_path)
                        <a href="{{ route('admin.applications.download-cv', $application->id) }}" 
                           class="btn btn-outline-primary w-100 mb-2">
                            <i class="fas fa-file-pdf me-2"></i>Download CV
                        </a>
                    @else
                        <div class="alert alert-warning bg-warning bg-opacity-25 border-warning text-dark mb-2">
                            <i class="fas fa-exclamation-triangle me-2"></i>CV tidak tersedia
                        </div>
                    @endif

                    @if($application->portfolio_path)
                        <a href="{{ route('admin.applications.download-portfolio', $application->id) }}" 
                           class="btn btn-outline-secondary w-100">
                            <i class="fas fa-folder me-2"></i>Download Portfolio
                        </a>
                    @endif
                </div>
            </div>

            <!-- Job Info -->
            <div class="card bg-dark border-dark shadow-sm mb-4">
                <div class="card-body">
                    <h5 class="card-title mb-3 text-white">
                        <i class="fas fa-briefcase me-2"></i>Info Lowongan
                    </h5>
                    <div class="mb-2">
                        <label class="text-muted small">
                            <i class="fas fa-tag me-1"></i>Posisi
                        </label>
                        <div class="fw-semibold text-white">{{ $application->jobVacancy->title ?? 'N/A' }}</div>
                    </div>
                    <div class="mb-2">
                        <label class="text-muted small">
                            <i class="fas fa-clock me-1"></i>Tanggal Lamar
                        </label>
                        <div class="text-white">{{ $application->created_at->format('d M Y H:i') }}</div>
                    </div>
                    <a href="{{ route('career.show', $application->jobVacancy->slug) }}" 
                       class="btn btn-sm btn-outline-secondary w-100 mt-2" target="_blank">
                        <i class="fas fa-external-link-alt me-2"></i>Lihat Lowongan
                    </a>
                </div>
            </div>

            <!-- Actions -->
            <div class="card bg-dark border-dark shadow-sm">
                <div class="card-body">
                    <h5 class="card-title mb-3 text-white">
                        <i class="fas fa-cog me-2"></i>Aksi
                    </h5>
                    <form action="{{ route('admin.applications.destroy', $application->id) }}" 
                          method="POST" 
                          onsubmit="return confirm('Yakin ingin menghapus lamaran ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger w-100">
                            <i class="fas fa-trash-alt me-2"></i>Hapus Lamaran
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
