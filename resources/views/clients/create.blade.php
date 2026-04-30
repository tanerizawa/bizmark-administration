@extends('layouts.app')

@section('title', 'Tambah Klien Baru')

@section('content')
<div class="space-y-4">
    <!-- Header -->
    <section class="card-elevated rounded-apple-lg admin-hero relative overflow-hidden mb-4">
        <div class="absolute inset-0 pointer-events-none" aria-hidden="true">
            <div class="w-48 h-48 bg-apple-purple opacity-20 blur-3xl rounded-full absolute -top-10 -right-6"></div>
            <div class="w-32 h-32 bg-apple-orange opacity-15 blur-2xl rounded-full absolute bottom-0 left-6"></div>
        </div>
        <div class="relative">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3">
                <div class="space-y-1 max-w-3xl">
                    <p class="admin-label-compact">Manajemen Klien</p>
                    <h1 class="admin-hero-title">Tambah Klien Baru</h1>
                    <p class="admin-body text-dark-text-primary/75">Tambahkan informasi klien baru untuk sistem manajemen proyek.</p>
                </div>
                <a href="{{ route('clients.index') }}" class="admin-btn inline-flex items-center">
                    <i class="fas fa-arrow-left mr-1.5"></i>Kembali
                </a>
            </div>
        </div>
    </section>

    <!-- Form Card -->
    <div class="card-elevated rounded-apple-lg bg-dark-bg-secondary border border-white/10">
        <div class="p-5">
            <form action="{{ route('clients.store') }}" method="POST">
                @csrf

                <!-- Informasi Dasar -->
                <div class="mb-6">
                    <h5 class="text-dark-text-primary mb-4 pb-2 flex items-center gap-2 text-base font-semibold border-b border-dark-separator">
                        <i class="fas fa-info-circle text-apple-blue"></i>Informasi Dasar
                    </h5>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="name" class="admin-form-label required">Nama Klien</label>
                            <input type="text" class="admin-form-input @error('name') admin-input-error @enderror" 
                                   id="name" name="name" value="{{ old('name') }}" required>
                            @error('name')
                                <p class="admin-error-text mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="company_name" class="admin-form-label">Nama Perusahaan</label>
                            <input type="text" class="admin-form-input @error('company_name') admin-input-error @enderror" 
                                   id="company_name" name="company_name" value="{{ old('company_name') }}">
                            @error('company_name')
                                <p class="admin-error-text mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="client_type" class="admin-form-label required">Tipe Klien</label>
                            <select class="admin-form-select @error('client_type') admin-input-error @enderror" 
                                    id="client_type" name="client_type" required>
                                <option value="">Pilih Tipe Klien</option>
                                <option value="individual" {{ old('client_type') == 'individual' ? 'selected' : '' }}>Individual</option>
                                <option value="company" {{ old('client_type') == 'company' ? 'selected' : '' }}>Perusahaan</option>
                                <option value="government" {{ old('client_type') == 'government' ? 'selected' : '' }}>Pemerintah</option>
                            </select>
                            @error('client_type')
                                <p class="admin-error-text mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="industry" class="admin-form-label">Industri</label>
                            <input type="text" class="admin-form-input @error('industry') admin-input-error @enderror" 
                                   id="industry" name="industry" value="{{ old('industry') }}" 
                                   placeholder="Contoh: Konstruksi, Perdagangan, dll">
                            @error('industry')
                                <p class="admin-error-text mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="status" class="admin-form-label required">Status</label>
                            <select class="admin-form-select @error('status') admin-input-error @enderror" 
                                    id="status" name="status" required>
                                <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>Aktif</option>
                                <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Tidak Aktif</option>
                                <option value="potential" {{ old('status') == 'potential' ? 'selected' : '' }}>Potensial</option>
                            </select>
                            @error('status')
                                <p class="admin-error-text mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Informasi Kontak -->
                <div class="mb-6">
                    <h5 class="text-lg font-semibold text-dark-text-primary mb-4 pb-2 border-b border-dark-separator flex items-center gap-2">
                        <i class="fas fa-address-book text-apple-blue"></i>Informasi Kontak
                    </h5>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="contact_person" class="admin-form-label">Nama Contact Person</label>
                            <input type="text" class="admin-form-input @error('contact_person') admin-input-error @enderror" 
                                   id="contact_person" name="contact_person" value="{{ old('contact_person') }}">
                            @error('contact_person')
                                <p class="admin-error-text mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="email" class="admin-form-label">Email</label>
                            <input type="email" class="admin-form-input @error('email') admin-input-error @enderror" 
                                   id="email" name="email" value="{{ old('email') }}">
                            @error('email')
                                <p class="admin-error-text mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="phone" class="admin-form-label">Telepon</label>
                            <input type="text" class="admin-form-input @error('phone') admin-input-error @enderror" 
                                   id="phone" name="phone" value="{{ old('phone') }}" 
                                   placeholder="Contoh: 021-12345678">
                            @error('phone')
                                <p class="admin-error-text mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="mobile" class="admin-form-label">Handphone / WhatsApp</label>
                            <input type="text" class="admin-form-input @error('mobile') admin-input-error @enderror" 
                                   id="mobile" name="mobile" value="{{ old('mobile') }}" 
                                   placeholder="Contoh: 083879602855">
                            @error('mobile')
                                <p class="admin-error-text mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Alamat -->
                <div class="mb-6">
                    <h5 class="text-lg font-semibold text-dark-text-primary mb-4 pb-2 border-b border-dark-separator flex items-center gap-2">
                        <i class="fas fa-map-marker-alt text-apple-blue"></i>Alamat
                    </h5>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="md:col-span-2">
                            <label for="address" class="admin-form-label">Alamat Lengkap</label>
                            <textarea class="admin-form-textarea @error('address') admin-input-error @enderror" 
                                      id="address" name="address" rows="3">{{ old('address') }}</textarea>
                            @error('address')
                                <p class="admin-error-text mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="city" class="admin-form-label">Kota</label>
                            <input type="text" class="admin-form-input @error('city') admin-input-error @enderror" 
                                   id="city" name="city" value="{{ old('city') }}">
                            @error('city')
                                <p class="admin-error-text mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="province" class="admin-form-label">Provinsi</label>
                            <input type="text" class="admin-form-input @error('province') admin-input-error @enderror" 
                                   id="province" name="province" value="{{ old('province') }}">
                            @error('province')
                                <p class="admin-error-text mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="postal_code" class="admin-form-label">Kode Pos</label>
                            <input type="text" class="admin-form-input @error('postal_code') admin-input-error @enderror" 
                                   id="postal_code" name="postal_code" value="{{ old('postal_code') }}">
                            @error('postal_code')
                                <p class="admin-error-text mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Informasi Pajak -->
                <div class="mb-6">
                    <h5 class="text-lg font-semibold text-dark-text-primary mb-4 pb-2 border-b border-dark-separator flex items-center gap-2">
                        <i class="fas fa-file-invoice text-apple-blue"></i>Informasi Pajak
                    </h5>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="npwp" class="admin-form-label">NPWP</label>
                            <input type="text" class="admin-form-input @error('npwp') admin-input-error @enderror" 
                                   id="npwp" name="npwp" value="{{ old('npwp') }}" 
                                   placeholder="Contoh: 12.345.678.9-012.345">
                            @error('npwp')
                                <p class="admin-error-text mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="tax_name" class="admin-form-label">Nama di NPWP</label>
                            <input type="text" class="admin-form-input @error('tax_name') admin-input-error @enderror" 
                                   id="tax_name" name="tax_name" value="{{ old('tax_name') }}">
                            @error('tax_name')
                                <p class="admin-error-text mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="md:col-span-2">
                            <label for="tax_address" class="admin-form-label">Alamat NPWP</label>
                            <textarea class="admin-form-textarea @error('tax_address') admin-input-error @enderror" 
                                      id="tax_address" name="tax_address" rows="2">{{ old('tax_address') }}</textarea>
                            @error('tax_address')
                                <p class="admin-error-text mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Catatan -->
                <div class="mb-6">
                    <h5 class="text-lg font-semibold text-dark-text-primary mb-4 pb-2 border-b border-dark-separator flex items-center gap-2">
                        <i class="fas fa-sticky-note text-apple-blue"></i>Catatan
                    </h5>
                    
                    <div>
                        <label for="notes" class="admin-form-label">Catatan Tambahan</label>
                        <textarea class="admin-form-textarea @error('notes') admin-input-error @enderror" 
                                  id="notes" name="notes" rows="3" 
                                  placeholder="Catatan internal tentang klien ini...">{{ old('notes') }}</textarea>
                        @error('notes')
                            <p class="admin-error-text mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex justify-end gap-3 pt-4 border-t border-dark-separator">
                    <a href="{{ route('clients.index') }}" class="admin-btn-secondary inline-flex items-center">
                        <i class="fas fa-times mr-1.5"></i>Batal
                    </a>
                    <button type="submit" class="admin-btn-primary inline-flex items-center">
                        <i class="fas fa-save mr-1.5"></i>Simpan Klien
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

    /* Admin Form Components - Tailwind Style */
    .admin-form-label {
        display: block;
        color: var(--dark-text-secondary);
        font-weight: 500;
        font-size: 0.875rem;
        margin-bottom: 0.5rem;
    }

    .admin-form-input,
    .admin-form-select,
    .admin-form-textarea {
        width: 100%;
        padding: 0.625rem 0.875rem;
        font-size: 0.875rem;
        line-height: 1.5;
        color: var(--dark-text-primary);
        background-color: var(--dark-bg-tertiary);
        border: 1px solid var(--dark-separator);
        border-radius: 0.5rem;
        transition: border-color 0.2s ease, box-shadow 0.2s ease;
    }

    .admin-form-input:focus,
    .admin-form-select:focus,
    .admin-form-textarea:focus {
        outline: none;
        border-color: var(--apple-blue);
        box-shadow: 0 0 0 3px rgba(0, 122, 255, 0.15);
    }

    .admin-form-input::placeholder,
    .admin-form-textarea::placeholder {
        color: var(--dark-text-tertiary);
    }

    .admin-form-select option {
        background-color: var(--dark-bg-tertiary);
        color: var(--dark-text-primary);
    }

    .admin-input-error {
        border-color: var(--apple-red) !important;
    }

    .admin-input-error:focus {
        box-shadow: 0 0 0 3px rgba(255, 59, 48, 0.15) !important;
    }

    .admin-error-text {
        font-size: 0.75rem;
        color: var(--apple-red);
    }

    /* Buttons */
    .admin-btn-secondary {
        padding: 0.625rem 1rem;
        font-size: 0.875rem;
        font-weight: 500;
        color: var(--dark-text-secondary);
        background-color: var(--dark-bg-tertiary);
        border: 1px solid var(--dark-separator);
        border-radius: 0.5rem;
        transition: all 0.2s ease;
    }

    .admin-btn-secondary:hover {
        background-color: var(--dark-separator);
        color: var(--dark-text-primary);
    }

    .admin-btn-primary {
        padding: 0.625rem 1rem;
        font-size: 0.875rem;
        font-weight: 500;
        color: white;
        background: linear-gradient(135deg, rgba(175, 82, 222, 0.9) 0%, rgba(155, 62, 202, 1) 100%);
        border: none;
        border-radius: 0.5rem;
        transition: all 0.2s ease;
        box-shadow: 0 4px 12px rgba(175, 82, 222, 0.3);
    }

    .admin-btn-primary:hover {
        transform: translateY(-1px);
        box-shadow: 0 6px 16px rgba(175, 82, 222, 0.4);
    }
</style>
@endsection
