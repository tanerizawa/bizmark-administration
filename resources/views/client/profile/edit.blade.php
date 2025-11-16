@extends('client.layouts.app')

@section('title', 'Profil Saya')

@section('content')
<div class="space-y-8">
    <!-- Hero -->
    <div class="bg-gradient-to-r from-emerald-600 via-teal-600 to-indigo-600 text-white rounded-3xl shadow-xl overflow-hidden relative">
        <div class="absolute inset-0 opacity-20 bg-[url('https://www.toptal.com/designers/subtlepatterns/uploads/dot-grid.png')]"></div>
        <div class="relative p-6 lg:p-8 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
            <div class="flex-1 space-y-3">
                <p class="text-xs uppercase tracking-[0.35em] text-white/70">Profil Akun</p>
                <h1 class="text-3xl font-bold leading-tight">Kelola identitas bisnis dan kredensial Anda di Bizmark.</h1>
                <p class="text-white/80">Pastikan data perusahaan & kontak Anda selalu terbarui agar proses perizinan berjalan tanpa hambatan.</p>
                <div class="flex flex-wrap gap-3 text-sm">
                    <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/15 border border-white/30">
                        <i class="fas fa-user-circle"></i> {{ $client->client_type === 'company' ? 'Akun Perusahaan' : 'Akun Individu' }}
                    </span>
                    <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/15 border border-white/30">
                        <i class="fas fa-calendar-check"></i> Terdaftar {{ $client->created_at->format('d F Y') }}
                    </span>
                    @if($client->email_verified_at)
                    <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/15 border border-white/30">
                        <i class="fas fa-check-circle text-green-300"></i> Email terverifikasi
                    </span>
                    @else
                    <form method="POST" action="{{ route('client.verification.send') }}" class="inline">
                        @csrf
                        <button class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/15 border border-white/30 text-white/80 hover:text-white">
                            <i class="fas fa-paper-plane"></i> Verifikasi Email
                        </button>
                    </form>
                    @endif
                </div>
            </div>
            <div class="w-full lg:w-auto bg-white/10 rounded-2xl p-4 backdrop-blur border border-white/30 space-y-2 text-sm">
                <p class="flex items-center justify-between"><span>Status Akun</span><span class="px-3 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-700">{{ ucfirst($client->status) }}</span></p>
                <p class="flex items-center justify-between"><span>Email</span><span class="font-semibold">{{ $client->email }}</span></p>
                <p class="flex items-center justify-between"><span>Nomor Kontak</span><span class="font-semibold">{{ $client->mobile ?? '-' }}</span></p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
        <!-- Profile Info -->
        <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-800">
            <div class="p-6 border-b border-gray-100 dark:border-gray-800 flex items-center gap-3">
                <i class="fas fa-id-card text-indigo-600 text-xl"></i>
                <div>
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Informasi Profil</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Data dasar pemilik akun.</p>
                </div>
            </div>
            <form method="POST" action="{{ route('client.profile.update') }}" class="p-6 space-y-5">
                @csrf
                @method('PUT')
                <div>
                    <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Nama Lengkap</label>
                    <input type="text" name="name" value="{{ old('name', $client->name) }}" required class="mt-1 w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 focus:ring-2 focus:ring-indigo-500 dark:bg-gray-800 dark:text-white @error('name') border-red-500 @enderror">
                    @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Nama Perusahaan</label>
                    <input type="text" name="company_name" value="{{ old('company_name', $client->company_name) }}" class="mt-1 w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 focus:ring-2 focus:ring-indigo-500 dark:bg-gray-800 dark:text-white">
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Email</label>
                        <input type="email" name="email" value="{{ old('email', $client->email) }}" required inputmode="email" autocomplete="email" class="mt-1 w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 focus:ring-2 focus:ring-indigo-500 dark:bg-gray-800 dark:text-white @error('email') border-red-500 @enderror">
                        @error('email')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-700 dark:text-gray-300">No. HP/WhatsApp</label>
                        <input type="tel" name="mobile" value="{{ old('mobile', $client->mobile) }}" inputmode="tel" autocomplete="tel" pattern="[0-9+\s\-\(\)]+" class="mt-1 w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 focus:ring-2 focus:ring-indigo-500 dark:bg-gray-800 dark:text-white">
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Telepon Kantor</label>
                        <input type="tel" name="phone" value="{{ old('phone', $client->phone) }}" inputmode="tel" autocomplete="tel" pattern="[0-9+\s\-\(\)]+" class="mt-1 w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 focus:ring-2 focus:ring-indigo-500 dark:bg-gray-800 dark:text-white">
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Kode Pos</label>
                        <input type="text" name="postal_code" value="{{ old('postal_code', $client->postal_code) }}" class="mt-1 w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 focus:ring-2 focus:ring-indigo-500 dark:bg-gray-800 dark:text-white">
                    </div>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Alamat Lengkap</label>
                    <textarea name="address" rows="3" class="mt-1 w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 focus:ring-2 focus:ring-indigo-500 dark:bg-gray-800 dark:text-white">{{ old('address', $client->address) }}</textarea>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Kota</label>
                        <input type="text" name="city" value="{{ old('city', $client->city) }}" class="mt-1 w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 focus:ring-2 focus:ring-indigo-500 dark:bg-gray-800 dark:text-white">
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Provinsi</label>
                        <input type="text" name="province" value="{{ old('province', $client->province) }}" class="mt-1 w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 focus:ring-2 focus:ring-indigo-500 dark:bg-gray-800 dark:text-white">
                    </div>
                </div>
                <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100 dark:border-gray-800">
                    <a href="{{ route('client.dashboard') }}" class="px-5 py-2 rounded-xl border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-800">Batal</a>
                    <button type="submit" class="px-6 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-semibold">
                        <i class="fas fa-save mr-2"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>

        <!-- Password Card -->
        <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-800">
            <div class="p-6 border-b border-gray-100 dark:border-gray-800 flex items-center gap-3">
                <i class="fas fa-lock text-indigo-600 text-xl"></i>
                <div>
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Keamanan Akun</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Perbarui password secara berkala.</p>
                </div>
            </div>
            <form method="POST" action="{{ route('client.profile.password.update') }}" class="p-6 space-y-4">
                @csrf
                @method('PUT')
                <div>
                    <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Password Saat Ini</label>
                    <div class="relative">
                        <input type="password" name="current_password" id="current_password" required class="mt-1 w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 focus:ring-2 focus:ring-indigo-500 dark:bg-gray-800 dark:text-white @error('current_password') border-red-500 @enderror">
                        <button type="button" onclick="togglePassword('current_password')" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500"><i class="fas fa-eye" id="current_password-icon"></i></button>
                    </div>
                    @error('current_password')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Password Baru</label>
                    <div class="relative">
                        <input type="password" name="password" id="password" required minlength="8" class="mt-1 w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 focus:ring-2 focus:ring-indigo-500 dark:bg-gray-800 dark:text-white @error('password') border-red-500 @enderror">
                        <button type="button" onclick="togglePassword('password')" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500"><i class="fas fa-eye" id="password-icon"></i></button>
                    </div>
                    @error('password')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    <p class="text-xs text-gray-400 mt-1">Minimal 8 karakter, kombinasikan huruf & angka.</p>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Konfirmasi Password</label>
                    <div class="relative">
                        <input type="password" name="password_confirmation" id="password_confirmation" required class="mt-1 w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 focus:ring-2 focus:ring-indigo-500 dark:bg-gray-800 dark:text-white">
                        <button type="button" onclick="togglePassword('password_confirmation')" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500"><i class="fas fa-eye" id="password_confirmation-icon"></i></button>
                    </div>
                </div>
                <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100 dark:border-gray-800">
                    <button type="submit" class="px-6 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-semibold">
                        <i class="fas fa-key mr-2"></i> Ubah Password
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Account summary -->
    <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-800 p-6">
        <div class="flex items-start gap-4">
            <div class="w-12 h-12 rounded-2xl bg-indigo-100 text-indigo-600 flex items-center justify-center text-xl">
                <i class="fas fa-info-circle"></i>
            </div>
            <div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Ringkasan Akun</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm text-gray-600 dark:text-gray-400">
                    <p><strong>Tipe Akun:</strong> {{ $client->client_type === 'company' ? 'Perusahaan' : 'Individu' }}</p>
                    <p><strong>Status:</strong> <span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-700">{{ ucfirst($client->status) }}</span></p>
                    <p><strong>Email:</strong> {{ $client->email }}</p>
                    <p><strong>Nomor Telepon:</strong> {{ $client->phone ?? '-' }}</p>
                    <p><strong>Terakhir diperbarui:</strong> {{ optional($client->updated_at)->diffForHumans() }}</p>
                    <p><strong>Alamat:</strong> {{ $client->address ?? '-' }}</p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Notification Settings -->
    @include('client.components.notification-settings')
</div>

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
</script>
@endpush
@endsection
