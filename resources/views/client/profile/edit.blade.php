@extends('client.layouts.app')

@section('title', 'Profil Saya')

@section('content')
<div class="space-y-1">
    <!-- Hero -->
    <div class="border-y border-[#0a66c2] bg-[#0a66c2] text-white overflow-hidden">
        <div class="px-4 lg:px-6 py-6 lg:py-8">
            <div class="flex items-start justify-between gap-6">
                <div class="flex-1 space-y-2">
                    <h1 class="text-2xl lg:text-3xl font-bold">Profil Saya</h1>
                    <p class="text-white/80 text-sm">Kelola informasi akun dan keamanan Anda</p>
                </div>
                <div class="text-right space-y-1">
                    <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-white/10 border border-white/20 text-sm">
                        <i class="fas {{ $client->client_type === 'company' ? 'fa-building' : 'fa-user' }} text-base"></i>
                        <span>{{ $client->client_type === 'company' ? 'Perusahaan' : ($client->client_type === 'government' ? 'Pemerintah' : 'Perorangan') }}</span>
                    </div>
                    @if($client->email_verified_at)
                    <p class="text-xs text-white/70"><i class="fas fa-check-circle text-green-300"></i> Email terverifikasi</p>
                    @else
                    <form method="POST" action="{{ route('client.verification.send') }}" class="inline">
                        @csrf
                        <button class="text-xs text-white/70 hover:text-white underline active:scale-95 transition-transform">
                            <i class="fas fa-paper-plane"></i> Verifikasi Email
                        </button>
                    </form>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
    <div class="border-y border-green-200 bg-green-50 dark:bg-green-900/20 dark:border-green-800 px-4 lg:px-6 py-3">
        <div class="flex items-center gap-2 text-green-800 dark:text-green-200">
            <i class="fas fa-check-circle"></i>
            <span class="font-medium">{{ session('success') }}</span>
        </div>
    </div>
    @endif

    <div class="grid grid-cols-1 xl:grid-cols-2 gap-1">
        <!-- Profile Info -->
        <div class="bg-white dark:bg-gray-900 border-y border-gray-200 dark:border-gray-700">
            <div class="px-4 lg:px-6 py-6 border-b border-gray-200 dark:border-gray-700 flex items-center gap-3">
                <i class="fas fa-id-card text-[#0a66c2] text-xl"></i>
                <div>
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Informasi Profil</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Data dasar pemilik akun.</p>
                </div>
            </div>
            <form method="POST" action="{{ route('client.profile.update') }}" enctype="multipart/form-data" class="px-4 lg:px-6 py-6 space-y-5">
                @csrf
                @method('PUT')
                
                <!-- Profile Picture Upload -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Foto Profil</label>
                    <div class="flex items-center gap-4">
                        <!-- Preview -->
                        <div class="flex-shrink-0">
                            <div class="w-20 h-20 rounded-full overflow-hidden bg-gray-100 dark:bg-gray-800 border-2 border-gray-200 dark:border-gray-700">
                                @if($client->profile_picture)
                                <img id="profilePreview" src="{{ asset('storage/' . $client->profile_picture) }}" alt="Profile" class="w-full h-full object-cover">
                                @else
                                <div id="profilePreview" class="w-full h-full flex items-center justify-center text-gray-400">
                                    <i id="profileIcon" class="fas {{ $client->client_type === 'company' ? 'fa-building' : 'fa-user' }} text-3xl"></i>
                                </div>
                                @endif
                            </div>
                        </div>
                        <!-- Upload Input -->
                        <div class="flex-1">
                            <input type="file" name="profile_picture" id="profile_picture" accept="image/jpeg,image/jpg,image/png" class="block w-full text-sm text-gray-500 dark:text-gray-400 file:mr-4 file:py-2 file:px-4 file:border-0 file:text-sm file:font-semibold file:bg-[#0a66c2] file:text-white hover:file:bg-[#004182] file:cursor-pointer file:active:scale-95 file:transition-all" onchange="previewImage(event)">
                            @error('profile_picture')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                            <p class="text-xs text-gray-500 mt-1">JPG, PNG max 2MB. Upload baru akan mengganti foto lama.</p>
                        </div>
                    </div>
                    @if($client->profile_picture)
                    <div class="mt-2">
                        <a href="{{ route('client.profile.update') }}" onclick="event.preventDefault(); if(confirm('Yakin ingin menghapus foto profil?')) { document.getElementById('deletePhotoForm').submit(); }" class="text-sm text-red-600 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300">
                            <i class="fas fa-trash mr-1"></i> Hapus foto
                        </a>
                    </div>
                    @endif
                </div>
                
                <!-- Client Type Toggle -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Tipe Klien <span class="text-red-500">*</span></label>
                    <div class="grid grid-cols-2 gap-3">
                        <label class="relative flex items-center justify-center p-4 border-2 cursor-pointer transition-all {{ old('client_type', $client->client_type) === 'individual' ? 'border-[#0a66c2] bg-blue-50 dark:bg-blue-900/20' : 'border-gray-300 dark:border-gray-700 hover:border-gray-400' }}">
                            <input type="radio" name="client_type" value="individual" {{ old('client_type', $client->client_type) === 'individual' ? 'checked' : '' }} required class="sr-only" onchange="toggleClientTypeFields('individual')">
                            <div class="text-center">
                                <i class="fas fa-user text-2xl text-gray-600 dark:text-gray-400 mb-2 block"></i>
                                <span class="text-sm font-medium text-gray-900 dark:text-white">Perorangan</span>
                            </div>
                        </label>
                        <label class="relative flex items-center justify-center p-4 border-2 cursor-pointer transition-all {{ old('client_type', $client->client_type) === 'company' ? 'border-[#0a66c2] bg-blue-50 dark:bg-blue-900/20' : 'border-gray-300 dark:border-gray-700 hover:border-gray-400' }}">
                            <input type="radio" name="client_type" value="company" {{ old('client_type', $client->client_type) === 'company' ? 'checked' : '' }} required class="sr-only" onchange="toggleClientTypeFields('company')">
                            <div class="text-center">
                                <i class="fas fa-building text-2xl text-gray-600 dark:text-gray-400 mb-2 block"></i>
                                <span class="text-sm font-medium text-gray-900 dark:text-white">Perusahaan</span>
                            </div>
                        </label>
                    </div>
                    @error('client_type')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        <span id="nameLabel">{{ $client->client_type === 'company' ? 'Nama Pemilik/Direktur' : 'Nama Lengkap' }}</span> <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name" value="{{ old('name', $client->name) }}" required autocomplete="name" placeholder="Nama lengkap Anda" class="w-full px-4 py-2.5 text-base border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-[#0a66c2] focus:border-transparent transition @error('name') border-red-500 @enderror">
                    @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div id="companyNameField" style="display: {{ old('client_type', $client->client_type) === 'company' ? 'block' : 'none' }}">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nama Perusahaan <span class="text-red-500" id="companyNameRequired">*</span></label>
                    <input type="text" name="company_name" id="company_name" value="{{ old('company_name', $client->company_name) }}" autocomplete="organization" placeholder="Nama perusahaan" class="w-full px-4 py-2.5 text-base border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-[#0a66c2] focus:border-transparent transition">
                </div>
                <div id="companyFieldsGrid" class="grid grid-cols-1 md:grid-cols-2 gap-4" style="display: {{ old('client_type', $client->client_type) === 'company' ? 'grid' : 'none' }}">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Bidang Usaha/Industri</label>
                        <input type="text" name="industry" value="{{ old('industry', $client->industry) }}" placeholder="Misal: Perdagangan, Jasa, Manufaktur" class="w-full px-4 py-2.5 text-base border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-[#0a66c2] focus:border-transparent transition">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nama Contact Person</label>
                        <input type="text" name="contact_person" value="{{ old('contact_person', $client->contact_person) }}" placeholder="PIC/Contact person" class="w-full px-4 py-2.5 text-base border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-[#0a66c2] focus:border-transparent transition">
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Email</label>
                        <input type="email" name="email" value="{{ old('email', $client->email) }}" required inputmode="email" autocomplete="email" class="w-full px-4 py-2.5 text-base border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-[#0a66c2] focus:border-transparent transition @error('email') border-red-500 @enderror">
                        @error('email')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">No. HP/WhatsApp</label>
                        <input type="tel" name="mobile" value="{{ old('mobile', $client->mobile) }}" inputmode="tel" autocomplete="tel" pattern="[0-9+\s\-\(\)]+" class="w-full px-4 py-2.5 text-base border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-[#0a66c2] focus:border-transparent transition">
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            <span id="phoneLabel">{{ $client->client_type === 'company' ? 'Telepon Kantor' : 'Telepon Rumah' }}</span>
                        </label>
                        <input type="tel" name="phone" value="{{ old('phone', $client->phone) }}" inputmode="tel" autocomplete="tel" pattern="[0-9+\s\-\(\)]+" class="w-full px-4 py-2.5 text-base border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-[#0a66c2] focus:border-transparent transition">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Kode Pos</label>
                        <input type="text" name="postal_code" value="{{ old('postal_code', $client->postal_code) }}" inputmode="numeric" autocomplete="postal-code" class="w-full px-4 py-2.5 text-base border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-[#0a66c2] focus:border-transparent transition">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Alamat Lengkap</label>
                    <textarea name="address" rows="3" autocomplete="street-address" class="w-full px-4 py-2.5 text-base border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-[#0a66c2] focus:border-transparent transition">{{ old('address', $client->address) }}</textarea>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Kota</label>
                        <input type="text" name="city" value="{{ old('city', $client->city) }}" autocomplete="address-level2" class="w-full px-4 py-2.5 text-base border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-[#0a66c2] focus:border-transparent transition">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Provinsi</label>
                        <input type="text" name="province" value="{{ old('province', $client->province) }}" autocomplete="address-level1" class="w-full px-4 py-2.5 text-base border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-[#0a66c2] focus:border-transparent transition">
                    </div>
                </div>
                
                <!-- Tax Information -->
                <div class="pt-4 border-t border-gray-200 dark:border-gray-700">
                    <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">
                        <i class="fas fa-file-invoice text-[#0a66c2] mr-2"></i>Informasi Perpajakan (Opsional)
                    </h3>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">NPWP</label>
                            <input type="text" name="npwp" value="{{ old('npwp', $client->npwp) }}" placeholder="XX.XXX.XXX.X-XXX.XXX" inputmode="numeric" class="w-full px-4 py-2.5 text-base border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-[#0a66c2] focus:border-transparent transition">
                            <p class="text-xs text-gray-500 mt-1">Nomor Pokok Wajib Pajak</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nama Wajib Pajak</label>
                            <input type="text" name="tax_name" value="{{ old('tax_name', $client->tax_name) }}" placeholder="Nama sesuai NPWP" class="w-full px-4 py-2.5 text-base border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-[#0a66c2] focus:border-transparent transition">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Alamat Pajak</label>
                            <textarea name="tax_address" rows="2" placeholder="Alamat sesuai NPWP" class="w-full px-4 py-2.5 text-base border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-[#0a66c2] focus:border-transparent transition">{{ old('tax_address', $client->tax_address) }}</textarea>
                        </div>
                    </div>
                </div>
                
                <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                    <a href="{{ route('client.dashboard') }}" class="px-5 py-2 border border-gray-300 dark:border-gray-700 text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-800 active:scale-95 transition-all">Batal</a>
                    <button type="submit" class="px-6 py-2 bg-[#0a66c2] hover:bg-[#004182] text-white font-semibold active:scale-95 transition-all">
                        <i class="fas fa-save mr-2"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>

        <!-- Password Card -->
        <div class="bg-white dark:bg-gray-900 border-y border-gray-200 dark:border-gray-700">
            <div class="px-4 lg:px-6 py-6 border-b border-gray-200 dark:border-gray-700 flex items-center gap-3">
                <i class="fas fa-lock text-[#0a66c2] text-xl"></i>
                <div>
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Keamanan Akun</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Perbarui password secara berkala.</p>
                </div>
            </div>
            <form method="POST" action="{{ route('client.profile.password.update') }}" class="px-4 lg:px-6 py-6 space-y-4">
                @csrf
                @method('PUT')
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Password Saat Ini</label>
                    <div class="relative">
                        <input type="password" name="current_password" id="current_password" required autocomplete="current-password" class="w-full px-4 py-2.5 pr-12 text-base border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-[#0a66c2] focus:border-transparent transition @error('current_password') border-red-500 @enderror">
                        <button type="button" onclick="togglePassword('current_password')" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-700 dark:hover:text-gray-300"><i class="fas fa-eye" id="current_password-icon"></i></button>
                    </div>
                    @error('current_password')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Password Baru</label>
                    <div class="relative">
                        <input type="password" name="password" id="password" required minlength="8" autocomplete="new-password" class="w-full px-4 py-2.5 pr-12 text-base border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-[#0a66c2] focus:border-transparent transition @error('password') border-red-500 @enderror">
                        <button type="button" onclick="togglePassword('password')" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-700 dark:hover:text-gray-300"><i class="fas fa-eye" id="password-icon"></i></button>
                    </div>
                    @error('password')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    <p class="text-xs text-gray-400 mt-1">Minimal 8 karakter, kombinasikan huruf & angka.</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Konfirmasi Password</label>
                    <div class="relative">
                        <input type="password" name="password_confirmation" id="password_confirmation" required autocomplete="new-password" class="w-full px-4 py-2.5 pr-12 text-base border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-[#0a66c2] focus:border-transparent transition">
                        <button type="button" onclick="togglePassword('password_confirmation')" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-700 dark:hover:text-gray-300"><i class="fas fa-eye" id="password_confirmation-icon"></i></button>
                    </div>
                </div>
                <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                    <button type="submit" class="px-6 py-2 bg-[#0a66c2] hover:bg-[#004182] text-white font-semibold active:scale-95 transition-all">
                        <i class="fas fa-key mr-2"></i> Ubah Password
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Hidden form for delete photo -->
<form id="deletePhotoForm" method="POST" action="{{ route('client.profile.update') }}" style="display: none;">
    @csrf
    @method('PUT')
    <input type="hidden" name="delete_photo" value="1">
</form>

@push('scripts')
<script>
function togglePassword(fieldId) {
    const field = document.getElementById(fieldId);
    const icon = document.getElementById(fieldId + '-icon');
    const isPassword = field.type === 'password';
    field.type = isPassword ? 'text' : 'password';
    icon.classList.toggle('fa-eye', !isPassword);
    icon.classList.toggle('fa-eye-slash', isPassword);
}

function previewImage(event) {
    const file = event.target.files[0];
    const preview = document.getElementById('profilePreview');
    
    if (file && file.type.startsWith('image/')) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.innerHTML = '<img src="' + e.target.result + '" alt="Preview" class="w-full h-full object-cover">';
        };
        reader.readAsDataURL(file);
    }
}

function toggleClientTypeFields(type) {
    // Update radio button styling
    const form = event.target.form || event.target.closest('form');
    form.querySelectorAll('label[class*="border-2"]').forEach(l => {
        l.classList.remove('border-[#0a66c2]', 'bg-blue-50', 'dark:bg-blue-900/20');
        l.classList.add('border-gray-300', 'dark:border-gray-700');
    });
    event.target.closest('label').classList.remove('border-gray-300', 'dark:border-gray-700');
    event.target.closest('label').classList.add('border-[#0a66c2]', 'bg-blue-50', 'dark:bg-blue-900/20');
    
    // Update profile icon (only if no image uploaded)
    const profilePreview = document.getElementById('profilePreview');
    const profileIcon = document.getElementById('profileIcon');
    if (profileIcon && !profilePreview.querySelector('img')) {
        if (type === 'company') {
            profileIcon.classList.remove('fa-user');
            profileIcon.classList.add('fa-building');
        } else {
            profileIcon.classList.remove('fa-building');
            profileIcon.classList.add('fa-user');
        }
    }
    
    // Show/Hide company fields
    const companyNameField = document.getElementById('companyNameField');
    const companyFieldsGrid = document.getElementById('companyFieldsGrid');
    const companyNameInput = document.getElementById('company_name');
    const companyNameRequired = document.getElementById('companyNameRequired');
    
    if (type === 'company') {
        // Show company fields
        companyNameField.style.display = 'block';
        companyFieldsGrid.style.display = 'grid';
        companyNameInput.setAttribute('required', 'required');
        companyNameRequired.style.display = 'inline';
    } else {
        // Hide company fields
        companyNameField.style.display = 'none';
        companyFieldsGrid.style.display = 'none';
        companyNameInput.removeAttribute('required');
        companyNameRequired.style.display = 'none';
    }
    
    // Update dynamic labels
    const nameLabel = document.getElementById('nameLabel');
    const phoneLabel = document.getElementById('phoneLabel');
    
    if (type === 'company') {
        nameLabel.textContent = 'Nama Pemilik/Direktur';
        phoneLabel.textContent = 'Telepon Kantor';
    } else {
        nameLabel.textContent = 'Nama Lengkap';
        phoneLabel.textContent = 'Telepon Rumah';
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    const checkedType = document.querySelector('input[name="client_type"]:checked');
    if (checkedType) {
        // Set initial state without triggering full toggle
        const type = checkedType.value;
        const companyNameField = document.getElementById('companyNameField');
        const companyFieldsGrid = document.getElementById('companyFieldsGrid');
        const companyNameInput = document.getElementById('company_name');
        
        if (type === 'company') {
            companyNameField.style.display = 'block';
            companyFieldsGrid.style.display = 'grid';
            companyNameInput.setAttribute('required', 'required');
        } else {
            companyNameField.style.display = 'none';
            companyFieldsGrid.style.display = 'none';
            companyNameInput.removeAttribute('required');
        }
    }
});
</script>
@endpush
@endsection

