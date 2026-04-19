@extends('layouts.app')

@section('title', 'Tambah Lowongan Kerja')

@section('content')
<div class="container-fluid">
    <div class="mb-4">
        <a href="{{ route('admin.jobs.index') }}" class="btn btn-sm btn-outline-secondary mb-3">
            <i class="fas fa-arrow-left me-2"></i>Kembali
        </a>
        <h1 class="h3 mb-1">Tambah Lowongan Kerja</h1>
        <p class="text-muted">Buat lowongan pekerjaan baru</p>
    </div>

    <form action="{{ route('admin.jobs.store') }}" method="POST">
        @csrf
        
        <div class="row">
            <div class="col-lg-8">
                <div class="card bg-dark border-dark shadow-sm mb-4">
                    <div class="card-body">
                        <h5 class="card-title mb-4 text-white">Informasi Dasar</h5>
                        
                        <div class="mb-3">
                            <label class="form-label text-white">Judul Lowongan <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control bg-dark text-white border-secondary @error('title') is-invalid @enderror" 
                                   value="{{ old('title') }}" required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-white">Posisi <span class="text-danger">*</span></label>
                            <input type="text" name="position" class="form-control bg-dark text-white border-secondary @error('position') is-invalid @enderror" 
                                   value="{{ old('position') }}" required>
                            @error('position')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-white">Deskripsi Pekerjaan <span class="text-danger">*</span></label>
                            <textarea name="description" rows="6" class="form-control bg-dark text-white border-secondary @error('description') is-invalid @enderror" required>{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3" id="responsibilities-container">
                            <label class="form-label text-white">Tanggung Jawab <span class="text-danger">*</span></label>
                            <div class="responsibilities-list">
                                <div class="input-group mb-2">
                                    <input type="text" name="responsibilities[]" class="form-control bg-dark text-white border-secondary" placeholder="Tanggung jawab 1">
                                    <button type="button" class="btn btn-outline-danger btn-sm" onclick="this.parentElement.remove()">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="addField('responsibilities')">
                                <i class="fas fa-plus me-1"></i>Tambah
                            </button>
                        </div>

                        <div class="mb-3" id="qualifications-container">
                            <label class="form-label text-white">Kualifikasi <span class="text-danger">*</span></label>
                            <div class="qualifications-list">
                                <div class="input-group mb-2">
                                    <input type="text" name="qualifications[]" class="form-control bg-dark text-white border-secondary" placeholder="Kualifikasi 1">
                                    <button type="button" class="btn btn-outline-danger btn-sm" onclick="this.parentElement.remove()">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="addField('qualifications')">
                                <i class="fas fa-plus me-1"></i>Tambah
                            </button>
                        </div>

                        <div class="mb-3" id="benefits-container">
                            <label class="form-label text-white">Benefit/Keuntungan</label>
                            <div class="benefits-list">
                                <div class="input-group mb-2">
                                    <input type="text" name="benefits[]" class="form-control bg-dark text-white border-secondary" placeholder="Benefit 1">
                                    <button type="button" class="btn btn-outline-danger btn-sm" onclick="this.parentElement.remove()">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="addField('benefits')">
                                <i class="fas fa-plus me-1"></i>Tambah
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card bg-dark border-dark shadow-sm mb-4">
                    <div class="card-body">
                        <h5 class="card-title mb-4 text-white">Detail Pekerjaan</h5>

                        <div class="mb-3">
                            <label class="form-label text-white">Tipe Pekerjaan <span class="text-danger">*</span></label>
                            <select name="employment_type" class="form-select bg-dark text-white border-secondary" required>
                                <option value="full-time">Full Time</option>
                                <option value="part-time">Part Time</option>
                                <option value="contract">Kontrak</option>
                                <option value="internship">Magang</option>
                                <option value="remote">Remote</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-white">Lokasi <span class="text-danger">*</span></label>
                            <input type="text" name="location" class="form-control bg-dark text-white border-secondary" value="{{ old('location', 'Jakarta') }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-white">Gaji Minimum (Rp)</label>
                            <input type="number" name="salary_min" class="form-control bg-dark text-white border-secondary" value="{{ old('salary_min') }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-white">Gaji Maksimum (Rp)</label>
                            <input type="number" name="salary_max" class="form-control bg-dark text-white border-secondary" value="{{ old('salary_max') }}">
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input type="checkbox" name="salary_negotiable" value="1" class="form-check-input" checked>
                                <label class="form-check-label text-white">Gaji bisa dinegosiasi</label>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-white">Deadline Lamaran</label>
                            <input type="date" name="deadline" class="form-control bg-dark text-white border-secondary" value="{{ old('deadline') }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-white">Google Form URL (Backup)</label>
                            <input type="url" name="google_form_url" class="form-control bg-dark text-white border-secondary" value="{{ old('google_form_url') }}" placeholder="https://forms.gle/...">
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-white">Status <span class="text-danger">*</span></label>
                            <select name="status" class="form-select bg-dark text-white border-secondary" required>
                                <option value="draft">Draft</option>
                                <option value="open">Aktif/Terbuka</option>
                                <option value="closed">Ditutup</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Simpan Lowongan
                    </button>
                    <a href="{{ route('admin.jobs.index') }}" class="btn btn-outline-secondary">
                        Batal
                    </a>
                </div>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
function addField(type) {
    const container = document.querySelector(`#${type}-container .${type}-list`);
    const div = document.createElement('div');
    div.className = 'input-group mb-2';
    div.innerHTML = `
        <input type="text" name="${type}[]" class="form-control bg-dark text-white border-secondary" placeholder="${type.charAt(0).toUpperCase() + type.slice(1)} baru">
        <button type="button" class="btn btn-outline-danger btn-sm" onclick="this.parentElement.remove()">
            <i class="fas fa-times"></i>
        </button>
    `;
    container.appendChild(div);
}
</script>
@endpush
@endsection
