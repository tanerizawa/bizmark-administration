@extends('layouts.app')

@section('title', 'Edit Lowongan Kerja')

@section('content')
<div class="container-fluid">
    <div class="mb-4">
        <a href="{{ route('admin.jobs.index') }}" class="btn btn-sm btn-outline-secondary mb-3">
            <i class="fas fa-arrow-left me-2"></i>Kembali
        </a>
        <h1 class="h3 mb-1">Edit Lowongan Kerja</h1>
        <p class="text-muted">Perbarui informasi lowongan pekerjaan</p>
    </div>

    <form action="{{ route('admin.jobs.update', $vacancy->id) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="row">
            <div class="col-lg-8">
                <div class="card bg-dark border-dark shadow-sm mb-4">
                    <div class="card-body">
                        <h5 class="card-title mb-4 text-white">Informasi Dasar</h5>
                        
                        <div class="mb-3">
                            <label class="form-label text-white">Judul Lowongan <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control bg-dark text-white border-secondary @error('title') is-invalid @enderror" 
                                   value="{{ old('title', $vacancy->title) }}" required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-white">Posisi <span class="text-danger">*</span></label>
                            <input type="text" name="position" class="form-control bg-dark text-white border-secondary @error('position') is-invalid @enderror" 
                                   value="{{ old('position', $vacancy->position) }}" required>
                            @error('position')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-white">Deskripsi Pekerjaan <span class="text-danger">*</span></label>
                            <textarea name="description" rows="6" class="form-control bg-dark text-white border-secondary @error('description') is-invalid @enderror" required>{{ old('description', $vacancy->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3" id="responsibilities-container">
                            <label class="form-label text-white">Tanggung Jawab <span class="text-danger">*</span></label>
                            <div class="responsibilities-list">
                                @if(is_array($vacancy->responsibilities) && count($vacancy->responsibilities) > 0)
                                    @foreach($vacancy->responsibilities as $responsibility)
                                        <div class="input-group mb-2">
                                            <input type="text" name="responsibilities[]" class="form-control bg-dark text-white border-secondary" value="{{ $responsibility }}">
                                            <button type="button" class="btn btn-outline-danger btn-sm" onclick="this.parentElement.remove()">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="input-group mb-2">
                                        <input type="text" name="responsibilities[]" class="form-control bg-dark text-white border-secondary" placeholder="Tanggung jawab 1">
                                        <button type="button" class="btn btn-outline-danger btn-sm" onclick="this.parentElement.remove()">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                @endif
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="addField('responsibilities')">
                                <i class="fas fa-plus me-1"></i>Tambah
                            </button>
                        </div>

                        <div class="mb-3" id="qualifications-container">
                            <label class="form-label text-white">Kualifikasi <span class="text-danger">*</span></label>
                            <div class="qualifications-list">
                                @if(is_array($vacancy->qualifications) && count($vacancy->qualifications) > 0)
                                    @foreach($vacancy->qualifications as $qualification)
                                        <div class="input-group mb-2">
                                            <input type="text" name="qualifications[]" class="form-control bg-dark text-white border-secondary" value="{{ $qualification }}">
                                            <button type="button" class="btn btn-outline-danger btn-sm" onclick="this.parentElement.remove()">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="input-group mb-2">
                                        <input type="text" name="qualifications[]" class="form-control bg-dark text-white border-secondary" placeholder="Kualifikasi 1">
                                        <button type="button" class="btn btn-outline-danger btn-sm" onclick="this.parentElement.remove()">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                @endif
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="addField('qualifications')">
                                <i class="fas fa-plus me-1"></i>Tambah
                            </button>
                        </div>

                        <div class="mb-3" id="benefits-container">
                            <label class="form-label text-white">Benefit/Keuntungan</label>
                            <div class="benefits-list">
                                @if(is_array($vacancy->benefits) && count($vacancy->benefits) > 0)
                                    @foreach($vacancy->benefits as $benefit)
                                        <div class="input-group mb-2">
                                            <input type="text" name="benefits[]" class="form-control bg-dark text-white border-secondary" value="{{ $benefit }}">
                                            <button type="button" class="btn btn-outline-danger btn-sm" onclick="this.parentElement.remove()">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="input-group mb-2">
                                        <input type="text" name="benefits[]" class="form-control bg-dark text-white border-secondary" placeholder="Benefit 1">
                                        <button type="button" class="btn btn-outline-danger btn-sm" onclick="this.parentElement.remove()">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                @endif
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
                                <option value="full-time" {{ old('employment_type', $vacancy->employment_type) == 'full-time' ? 'selected' : '' }}>Full Time</option>
                                <option value="part-time" {{ old('employment_type', $vacancy->employment_type) == 'part-time' ? 'selected' : '' }}>Part Time</option>
                                <option value="contract" {{ old('employment_type', $vacancy->employment_type) == 'contract' ? 'selected' : '' }}>Kontrak</option>
                                <option value="internship" {{ old('employment_type', $vacancy->employment_type) == 'internship' ? 'selected' : '' }}>Magang</option>
                                <option value="remote" {{ old('employment_type', $vacancy->employment_type) == 'remote' ? 'selected' : '' }}>Remote</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-white">Lokasi <span class="text-danger">*</span></label>
                            <input type="text" name="location" class="form-control bg-dark text-white border-secondary" value="{{ old('location', $vacancy->location) }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-white">Gaji Minimum (Rp)</label>
                            <input type="number" name="salary_min" class="form-control bg-dark text-white border-secondary" value="{{ old('salary_min', $vacancy->salary_min) }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-white">Gaji Maksimum (Rp)</label>
                            <input type="number" name="salary_max" class="form-control bg-dark text-white border-secondary" value="{{ old('salary_max', $vacancy->salary_max) }}">
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input type="checkbox" name="salary_negotiable" value="1" class="form-check-input" {{ old('salary_negotiable', $vacancy->salary_negotiable) ? 'checked' : '' }}>
                                <label class="form-check-label text-white">Gaji bisa dinegosiasi</label>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-white">Deadline Lamaran</label>
                            <input type="date" name="deadline" class="form-control bg-dark text-white border-secondary" value="{{ old('deadline', $vacancy->deadline ? $vacancy->deadline->format('Y-m-d') : '') }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-white">Google Form URL (Backup)</label>
                            <input type="url" name="google_form_url" class="form-control bg-dark text-white border-secondary" value="{{ old('google_form_url', $vacancy->google_form_url) }}" placeholder="https://forms.gle/...">
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-white">Status <span class="text-danger">*</span></label>
                            <select name="status" class="form-select bg-dark text-white border-secondary" required>
                                <option value="draft" {{ old('status', $vacancy->status) == 'draft' ? 'selected' : '' }}>Draft</option>
                                <option value="open" {{ old('status', $vacancy->status) == 'open' ? 'selected' : '' }}>Aktif/Terbuka</option>
                                <option value="closed" {{ old('status', $vacancy->status) == 'closed' ? 'selected' : '' }}>Ditutup</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Update Lowongan
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
