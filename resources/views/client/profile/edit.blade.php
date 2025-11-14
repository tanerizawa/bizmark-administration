@extends('client.layouts.app')

@section('title', 'Edit Profil')
@section('page-title', 'Profil Saya')
@section('page-subtitle', 'Kelola informasi akun Anda')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    
    <!-- Profile Information Card -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
        <div class="p-6 border-b border-gray-100">
            <h3 class="text-xl font-bold text-gray-900">
                <i class="fas fa-user text-purple-600 mr-2"></i>Informasi Profil
            </h3>
            <p class="text-sm text-gray-600 mt-1">Perbarui informasi akun dan alamat email Anda</p>
        </div>
        
        <form method="POST" action="{{ route('client.profile.update') }}" class="p-6">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Name -->
                <div class="md:col-span-2">
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                        Nama Lengkap <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="text" 
                        id="name" 
                        name="name" 
                        value="{{ old('name', $client->name) }}" 
                        required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent @error('name') border-red-500 @enderror"
                    >
                    @error('name')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Company Name -->
                <div class="md:col-span-2">
                    <label for="company_name" class="block text-sm font-medium text-gray-700 mb-2">
                        Nama Perusahaan <span class="text-gray-400 text-xs">(opsional)</span>
                    </label>
                    <input 
                        type="text" 
                        id="company_name" 
                        name="company_name" 
                        value="{{ old('company_name', $client->company_name) }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent @error('company_name') border-red-500 @enderror"
                    >
                    @error('company_name')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                        Email <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        value="{{ old('email', $client->email) }}" 
                        required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent @error('email') border-red-500 @enderror"
                    >
                    @error('email')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Phone -->
                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">
                        Telepon
                    </label>
                    <input 
                        type="tel" 
                        id="phone" 
                        name="phone" 
                        value="{{ old('phone', $client->phone) }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent @error('phone') border-red-500 @enderror"
                    >
                    @error('phone')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Mobile -->
                <div>
                    <label for="mobile" class="block text-sm font-medium text-gray-700 mb-2">
                        No. HP/WhatsApp
                    </label>
                    <input 
                        type="tel" 
                        id="mobile" 
                        name="mobile" 
                        value="{{ old('mobile', $client->mobile) }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent @error('mobile') border-red-500 @enderror"
                    >
                    @error('mobile')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Address -->
                <div class="md:col-span-2">
                    <label for="address" class="block text-sm font-medium text-gray-700 mb-2">
                        Alamat Lengkap
                    </label>
                    <textarea 
                        id="address" 
                        name="address" 
                        rows="3"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent @error('address') border-red-500 @enderror"
                    >{{ old('address', $client->address) }}</textarea>
                    @error('address')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- City -->
                <div>
                    <label for="city" class="block text-sm font-medium text-gray-700 mb-2">
                        Kota
                    </label>
                    <input 
                        type="text" 
                        id="city" 
                        name="city" 
                        value="{{ old('city', $client->city) }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent @error('city') border-red-500 @enderror"
                    >
                    @error('city')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Province -->
                <div>
                    <label for="province" class="block text-sm font-medium text-gray-700 mb-2">
                        Provinsi
                    </label>
                    <input 
                        type="text" 
                        id="province" 
                        name="province" 
                        value="{{ old('province', $client->province) }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent @error('province') border-red-500 @enderror"
                    >
                    @error('province')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Postal Code -->
                <div>
                    <label for="postal_code" class="block text-sm font-medium text-gray-700 mb-2">
                        Kode Pos
                    </label>
                    <input 
                        type="text" 
                        id="postal_code" 
                        name="postal_code" 
                        value="{{ old('postal_code', $client->postal_code) }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent @error('postal_code') border-red-500 @enderror"
                    >
                    @error('postal_code')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 mt-6 pt-6 border-t border-gray-100">
                <a href="{{ route('client.dashboard') }}" class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                    <i class="fas fa-times mr-2"></i>Batal
                </a>
                <button type="submit" class="px-6 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition">
                    <i class="fas fa-save mr-2"></i>Simpan Perubahan
                </button>
            </div>
        </form>
    </div>

    <!-- Change Password Card -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
        <div class="p-6 border-b border-gray-100">
            <h3 class="text-xl font-bold text-gray-900">
                <i class="fas fa-lock text-purple-600 mr-2"></i>Ubah Password
            </h3>
            <p class="text-sm text-gray-600 mt-1">Pastikan password Anda kuat dan aman</p>
        </div>
        
        <form method="POST" action="{{ route('client.profile.password.update') }}" class="p-6">
            @csrf
            @method('PUT')
            
            <div class="space-y-6">
                <!-- Current Password -->
                <div>
                    <label for="current_password" class="block text-sm font-medium text-gray-700 mb-2">
                        Password Saat Ini <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <input 
                            type="password" 
                            id="current_password" 
                            name="current_password" 
                            required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent @error('current_password') border-red-500 @enderror"
                        >
                        <button 
                            type="button" 
                            onclick="togglePassword('current_password')"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-700"
                        >
                            <i class="fas fa-eye" id="current_password-icon"></i>
                        </button>
                    </div>
                    @error('current_password')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- New Password -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                        Password Baru <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <input 
                            type="password" 
                            id="password" 
                            name="password" 
                            required
                            minlength="8"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent @error('password') border-red-500 @enderror"
                        >
                        <button 
                            type="button" 
                            onclick="togglePassword('password')"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-700"
                        >
                            <i class="fas fa-eye" id="password-icon"></i>
                        </button>
                    </div>
                    @error('password')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @else
                        <p class="text-xs text-gray-500 mt-1">Minimal 8 karakter, kombinasi huruf & angka</p>
                    @enderror
                </div>

                <!-- Password Confirmation -->
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                        Konfirmasi Password Baru <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <input 
                            type="password" 
                            id="password_confirmation" 
                            name="password_confirmation" 
                            required
                            minlength="8"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                        >
                        <button 
                            type="button" 
                            onclick="togglePassword('password_confirmation')"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-700"
                        >
                            <i class="fas fa-eye" id="password_confirmation-icon"></i>
                        </button>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 mt-6 pt-6 border-t border-gray-100">
                <button type="submit" class="px-6 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition">
                    <i class="fas fa-key mr-2"></i>Ubah Password
                </button>
            </div>
        </form>
    </div>

    <!-- Account Info -->
    <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl shadow-sm p-6">
        <div class="flex items-start gap-4">
            <div class="w-12 h-12 bg-purple-600 rounded-lg flex items-center justify-center flex-shrink-0">
                <i class="fas fa-info-circle text-white text-xl"></i>
            </div>
            <div class="flex-1">
                <h4 class="text-lg font-bold text-gray-900 mb-2">Informasi Akun</h4>
                <div class="space-y-2 text-sm text-gray-700">
                    <p><strong>Tipe Akun:</strong> {{ $client->client_type === 'company' ? 'Perusahaan' : 'Individu' }}</p>
                    <p><strong>Status:</strong> 
                        <span class="px-2 py-1 bg-green-100 text-green-800 rounded text-xs font-semibold">
                            {{ ucfirst($client->status) }}
                        </span>
                    </p>
                    <p><strong>Terdaftar sejak:</strong> {{ $client->created_at->format('d F Y') }}</p>
                    @if($client->email_verified_at)
                        <p><strong>Email terverifikasi:</strong> 
                            <i class="fas fa-check-circle text-green-600"></i>
                            {{ $client->email_verified_at->format('d F Y') }}
                        </p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function togglePassword(fieldId) {
        const field = document.getElementById(fieldId);
        const icon = document.getElementById(fieldId + '-icon');
        
        if (field.type === 'password') {
            field.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            field.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }
</script>
@endpush
@endsection
