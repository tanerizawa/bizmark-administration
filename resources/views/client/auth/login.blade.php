@extends('client.layouts.auth')

@section('title', 'Masuk — Bizmark.ID Portal Klien')

@section('hero-title', 'Cockpit Perizinan Usaha Anda')
@section('hero-subtitle', 'Kelola permohonan izin, lacak progress, dan akses dokumen Anda kapan saja.')

@section('form')
<div class="auth-card p-7 sm:p-8" x-data="{ showPassword: false }">
    <div class="mb-7">
        <span class="inline-flex items-center gap-1.5 px-3 py-1 text-[10px] font-semibold rounded-full mb-3"
              style="background: rgba(10,102,194,0.08); color: var(--client-primary); border: 1px solid rgba(10,102,194,0.15);">
            <i class="fas fa-lock text-[8px]" aria-hidden="true"></i>
            Portal Klien
        </span>
        <h1 class="text-2xl font-bold" style="color: var(--text-primary);">Selamat Datang Kembali</h1>
        <p class="text-sm mt-1" style="color: var(--text-secondary);">Masuk ke portal perizinan Anda</p>
    </div>

    @if(session('success'))
    <div class="flex items-start gap-2 mb-5 px-4 py-3 rounded-xl text-sm text-green-800 dark:text-green-300"
         style="background: rgba(34,197,94,0.08); border: 1px solid rgba(34,197,94,0.2);">
        <i class="fas fa-circle-check text-green-500 mt-0.5 flex-shrink-0" aria-hidden="true"></i>
        {{ session('success') }}
    </div>
    @endif

    @if($errors->any())
    <div class="mb-5 px-4 py-3 rounded-xl text-sm text-red-700 dark:text-red-300"
         style="background: rgba(239,68,68,0.08); border: 1px solid rgba(239,68,68,0.2);">
        <i class="fas fa-circle-xmark mr-2" aria-hidden="true"></i>{{ $errors->first() }}
    </div>
    @endif

    <form method="POST" action="{{ route('client.login') }}" class="space-y-5" novalidate>
        @csrf

        <div>
            <label for="email" class="auth-label">Email</label>
            <div class="relative">
                <span class="absolute left-3 inset-y-0 flex items-center pointer-events-none" style="color: var(--text-tertiary);">
                    <i class="fas fa-envelope text-xs" aria-hidden="true"></i>
                </span>
                <input type="email" id="email" name="email" value="{{ old('email') }}"
                       required autofocus autocomplete="email"
                       placeholder="nama@perusahaan.com"
                       class="auth-input pl-9 @error('email') !border-red-400 @enderror">
            </div>
            @error('email')<p class="auth-error">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="password" class="auth-label">Password</label>
            <div class="relative">
                <span class="absolute left-3 inset-y-0 flex items-center pointer-events-none" style="color: var(--text-tertiary);">
                    <i class="fas fa-lock text-xs" aria-hidden="true"></i>
                </span>
                <input :type="showPassword ? 'text' : 'password'"
                       id="password" name="password" required autocomplete="current-password"
                       placeholder="Masukkan password"
                       class="auth-input pl-9 pr-10 @error('password') !border-red-400 @enderror">
                <button type="button" @click="showPassword = !showPassword"
                        class="absolute right-3 inset-y-0 flex items-center text-xs transition-colors"
                        style="color: var(--text-tertiary);">
                    <i :class="showPassword ? 'fas fa-eye-slash' : 'fas fa-eye'" aria-hidden="true"></i>
                </button>
            </div>
            @error('password')<p class="auth-error">{{ $message }}</p>@enderror
        </div>

        <div class="flex items-center justify-between">
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" name="remember" class="w-3.5 h-3.5 rounded"
                       style="accent-color: var(--client-primary);">
                <span class="text-xs" style="color: var(--text-secondary);">Ingat saya</span>
            </label>
            <a href="{{ route('client.password.request') }}"
               class="text-xs font-semibold hover:underline" style="color: var(--client-primary);">
                Lupa password?
            </a>
        </div>

        <button type="submit" class="auth-btn-primary">
            <i class="fas fa-right-to-bracket text-xs" aria-hidden="true"></i>
            Masuk ke Portal
        </button>
    </form>

    <div class="mt-6 text-center">
        <p class="text-sm" style="color: var(--text-secondary);">
            Belum punya akun?
            <a href="{{ route('client.register') }}" class="font-semibold hover:underline" style="color: var(--client-primary);">
                Daftar Sekarang
            </a>
        </p>
    </div>
</div>
@endsection
