@extends('client.layouts.auth')

@section('title', 'Reset Password — Bizmark.ID')

@section('hero-title', 'Buat Password Baru')
@section('hero-subtitle', 'Masukkan password baru Anda dan konfirmasikan untuk melanjutkan.')

@section('form')
<div class="auth-card p-7 sm:p-8" x-data="{ showPwd: false, showConfirm: false }">
    <div class="mb-7">
        <h1 class="text-2xl font-bold" style="color: var(--text-primary);">Buat Password Baru</h1>
        <p class="text-sm mt-1" style="color: var(--text-secondary);">Pilih password yang kuat dan mudah Anda ingat.</p>
    </div>

    @if($errors->any())
    <div class="mb-5 px-4 py-3 rounded-xl text-sm text-red-700"
         style="background: rgba(239,68,68,0.08); border: 1px solid rgba(239,68,68,0.2);">
        <i class="fas fa-circle-xmark mr-2"></i>{{ $errors->first() }}
    </div>
    @endif

    <form method="POST" action="{{ route('client.password.update') }}" class="space-y-5" novalidate>
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">

        <div>
            <label for="email" class="auth-label">Email</label>
            <div class="relative">
                <span class="absolute left-3 inset-y-0 flex items-center pointer-events-none" style="color: var(--text-tertiary);"><i class="fas fa-envelope text-xs"></i></span>
                <input type="email" id="email" name="email" value="{{ old('email', $email ?? '') }}"
                       required autocomplete="email"
                       class="auth-input pl-9 @error('email') !border-red-400 @enderror">
            </div>
            @error('email')<p class="auth-error">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="password" class="auth-label">Password Baru <span style="color: var(--apple-red)">*</span></label>
            <div class="relative">
                <span class="absolute left-3 inset-y-0 flex items-center pointer-events-none" style="color: var(--text-tertiary);"><i class="fas fa-lock text-xs"></i></span>
                <input :type="showPwd ? 'text' : 'password'" id="password" name="password"
                       required autocomplete="new-password"
                       placeholder="Min. 8 karakter"
                       class="auth-input pl-9 pr-10 @error('password') !border-red-400 @enderror">
                <button type="button" @click="showPwd = !showPwd" class="absolute right-3 inset-y-0 flex items-center text-xs" style="color: var(--text-tertiary);">
                    <i :class="showPwd ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
                </button>
            </div>
            @error('password')<p class="auth-error">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="password_confirmation" class="auth-label">Konfirmasi Password <span style="color: var(--apple-red)">*</span></label>
            <div class="relative">
                <span class="absolute left-3 inset-y-0 flex items-center pointer-events-none" style="color: var(--text-tertiary);"><i class="fas fa-lock text-xs"></i></span>
                <input :type="showConfirm ? 'text' : 'password'" id="password_confirmation" name="password_confirmation"
                       required autocomplete="new-password"
                       placeholder="Ulangi password baru"
                       class="auth-input pl-9 pr-10">
                <button type="button" @click="showConfirm = !showConfirm" class="absolute right-3 inset-y-0 flex items-center text-xs" style="color: var(--text-tertiary);">
                    <i :class="showConfirm ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
                </button>
            </div>
        </div>

        <button type="submit" class="auth-btn-primary">
            <i class="fas fa-shield-check text-xs"></i>
            Simpan Password Baru
        </button>
    </form>
</div>
@endsection
