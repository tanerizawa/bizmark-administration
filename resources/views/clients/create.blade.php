@extends('layouts.app')

@section('title', 'Tambah Klien Baru')

@section('content')
<div class="container-fluid px-4 py-6">
    <!-- Header -->
    <div class="mb-6">
        <div class="d-flex justify-content-between align-items-start">
            <div>
                <h1 class="text-dark-text-primary mb-2" style="font-size: 1.75rem; font-weight: 600;">Tambah Klien Baru</h1>
                <p class="text-dark-text-secondary" style="font-size: 0.875rem;">Tambahkan informasi klien baru untuk sistem manajemen proyek</p>
            </div>
            <a href="{{ route('clients.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Kembali
            </a>
        </div>
    </div>

    <!-- Form Card -->
    <div class="card" style="background-color: var(--dark-bg-secondary); border: 1px solid var(--dark-separator);">
        <div class="card-body">
            <form action="{{ route('clients.store') }}" method="POST">
                @csrf

                <!-- Informasi Dasar -->
                <div class="mb-6">
                    <h5 class="text-dark-text-primary mb-4 pb-2" style="font-size: 1rem; font-weight: 600; border-bottom: 1px solid var(--dark-separator);">
                        <i class="fas fa-info-circle me-2" style="color: var(--apple-blue);"></i>Informasi Dasar
                    </h5>
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="name" class="form-label required text-dark-text-secondary">Nama Klien</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name') }}" required
                                   style="background-color: var(--dark-bg-tertiary); border-color: var(--dark-separator); color: var(--dark-text-primary);">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="company_name" class="form-label text-dark-text-secondary">Nama Perusahaan</label>
                            <input type="text" class="form-control @error('company_name') is-invalid @enderror" 
                                   id="company_name" name="company_name" value="{{ old('company_name') }}"
                                   style="background-color: var(--dark-bg-tertiary); border-color: var(--dark-separator); color: var(--dark-text-primary);">
                            @error('company_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="client_type" class="form-label required">Tipe Klien</label>
                            <select class="form-select @error('client_type') is-invalid @enderror" 
                                    id="client_type" name="client_type" required>
                                <option value="">Pilih Tipe Klien</option>
                                <option value="individual" {{ old('client_type') == 'individual' ? 'selected' : '' }}>Individual</option>
                                <option value="company" {{ old('client_type') == 'company' ? 'selected' : '' }}>Perusahaan</option>
                                <option value="government" {{ old('client_type') == 'government' ? 'selected' : '' }}>Pemerintah</option>
                            </select>
                            @error('client_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="industry" class="form-label">Industri</label>
                            <input type="text" class="form-control @error('industry') is-invalid @enderror" 
                                   id="industry" name="industry" value="{{ old('industry') }}" 
                                   placeholder="Contoh: Konstruksi, Perdagangan, dll">
                            @error('industry')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="status" class="form-label required">Status</label>
                            <select class="form-select @error('status') is-invalid @enderror" 
                                    id="status" name="status" required>
                                <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>Aktif</option>
                                <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Tidak Aktif</option>
                                <option value="potential" {{ old('status') == 'potential' ? 'selected' : '' }}>Potensial</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Informasi Kontak -->
                <div class="mb-6">
                    <h5 class="text-lg font-semibold text-dark-text-primary mb-4 pb-2 border-b border-dark-separator">
                        <i class="fas fa-address-book me-2 text-apple-blue"></i>Informasi Kontak
                    </h5>
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="contact_person" class="form-label">Nama Contact Person</label>
                            <input type="text" class="form-control @error('contact_person') is-invalid @enderror" 
                                   id="contact_person" name="contact_person" value="{{ old('contact_person') }}">
                            @error('contact_person')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                   id="email" name="email" value="{{ old('email') }}">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="phone" class="form-label">Telepon</label>
                            <input type="text" class="form-control @error('phone') is-invalid @enderror" 
                                   id="phone" name="phone" value="{{ old('phone') }}" 
                                   placeholder="Contoh: 021-12345678">
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="mobile" class="form-label">Handphone / WhatsApp</label>
                            <input type="text" class="form-control @error('mobile') is-invalid @enderror" 
                                   id="mobile" name="mobile" value="{{ old('mobile') }}" 
                                   placeholder="Contoh: 083879602855">
                            @error('mobile')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Alamat -->
                <div class="mb-6">
                    <h5 class="text-lg font-semibold text-dark-text-primary mb-4 pb-2 border-b border-dark-separator">
                        <i class="fas fa-map-marker-alt me-2 text-apple-blue"></i>Alamat
                    </h5>
                    
                    <div class="row g-3">
                        <div class="col-12">
                            <label for="address" class="form-label">Alamat Lengkap</label>
                            <textarea class="form-control @error('address') is-invalid @enderror" 
                                      id="address" name="address" rows="3">{{ old('address') }}</textarea>
                            @error('address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label for="city" class="form-label">Kota</label>
                            <input type="text" class="form-control @error('city') is-invalid @enderror" 
                                   id="city" name="city" value="{{ old('city') }}">
                            @error('city')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label for="province" class="form-label">Provinsi</label>
                            <input type="text" class="form-control @error('province') is-invalid @enderror" 
                                   id="province" name="province" value="{{ old('province') }}">
                            @error('province')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label for="postal_code" class="form-label">Kode Pos</label>
                            <input type="text" class="form-control @error('postal_code') is-invalid @enderror" 
                                   id="postal_code" name="postal_code" value="{{ old('postal_code') }}">
                            @error('postal_code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Informasi Pajak -->
                <div class="mb-6">
                    <h5 class="text-lg font-semibold text-dark-text-primary mb-4 pb-2 border-b border-dark-separator">
                        <i class="fas fa-file-invoice me-2 text-apple-blue"></i>Informasi Pajak
                    </h5>
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="npwp" class="form-label">NPWP</label>
                            <input type="text" class="form-control @error('npwp') is-invalid @enderror" 
                                   id="npwp" name="npwp" value="{{ old('npwp') }}" 
                                   placeholder="Contoh: 12.345.678.9-012.345">
                            @error('npwp')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="tax_name" class="form-label">Nama di NPWP</label>
                            <input type="text" class="form-control @error('tax_name') is-invalid @enderror" 
                                   id="tax_name" name="tax_name" value="{{ old('tax_name') }}">
                            @error('tax_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <label for="tax_address" class="form-label">Alamat NPWP</label>
                            <textarea class="form-control @error('tax_address') is-invalid @enderror" 
                                      id="tax_address" name="tax_address" rows="2">{{ old('tax_address') }}</textarea>
                            @error('tax_address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Catatan -->
                <div class="mb-6">
                    <h5 class="text-lg font-semibold text-dark-text-primary mb-4 pb-2 border-b border-dark-separator">
                        <i class="fas fa-sticky-note me-2 text-apple-blue"></i>Catatan
                    </h5>
                    
                    <div class="col-12">
                        <label for="notes" class="form-label">Catatan Tambahan</label>
                        <textarea class="form-control @error('notes') is-invalid @enderror" 
                                  id="notes" name="notes" rows="3" 
                                  placeholder="Catatan internal tentang klien ini...">{{ old('notes') }}</textarea>
                        @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('clients.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times me-2"></i>Batal
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Simpan Klien
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .required::after {
        content: " *";
        color: var(--apple-red);
    }

    /* Form Labels */
    .form-label {
        color: var(--dark-text-secondary);
        font-weight: 500;
        font-size: 0.875rem;
        margin-bottom: 0.5rem;
    }

    /* Form Inputs - Global Styling */
    .form-control,
    .form-select,
    textarea.form-control {
        background-color: var(--dark-bg-tertiary) !important;
        border-color: var(--dark-separator) !important;
        color: var(--dark-text-primary) !important;
    }

    .form-control:focus,
    .form-select:focus,
    textarea.form-control:focus {
        background-color: var(--dark-bg-tertiary) !important;
        border-color: var(--apple-blue) !important;
        color: var(--dark-text-primary) !important;
        box-shadow: 0 0 0 0.2rem rgba(0, 122, 255, 0.25) !important;
    }

    .form-control::placeholder,
    textarea.form-control::placeholder {
        color: var(--dark-text-tertiary);
    }

    /* Select dropdowns */
    .form-select option {
        background-color: var(--dark-bg-tertiary);
        color: var(--dark-text-primary);
    }

    /* Invalid feedback */
    .invalid-feedback {
        color: var(--apple-red);
    }
</style>
@endsection
