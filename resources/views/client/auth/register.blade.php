@extends('client.layouts.auth')

@section('title', 'Daftar — Bizmark.ID Portal Klien')

@section('hero-title', 'Mulai Perjalanan Perizinan Anda')
@section('hero-subtitle', 'Daftar gratis dan konsultasi pertama Anda tidak dikenakan biaya.')

@section('form')
<div class="auth-card p-7 sm:p-8" x-data="{ showPwd: false, showPwdConfirm: false }">
    <div class="mb-6">
        <span class="inline-flex items-center gap-1.5 px-3 py-1 text-[10px] font-semibold rounded-full mb-3"
              style="background: rgba(10,102,194,0.08); color: var(--client-primary); border: 1px solid rgba(10,102,194,0.15);">
            <i class="fas fa-user-plus text-[8px]"></i>
            Daftar Akun Baru
        </span>
        <h1 class="text-2xl font-bold" style="color: var(--text-primary);">Buat Akun Bizmark.ID</h1>
        <p class="text-sm mt-1" style="color: var(--text-secondary);">Gratis. Tidak perlu kartu kredit.</p>
    </div>

    @if($errors->any())
    <div class="mb-5 px-4 py-3 rounded-xl text-sm text-red-700"
         style="background: rgba(239,68,68,0.08); border: 1px solid rgba(239,68,68,0.2);">
        <i class="fas fa-circle-xmark mr-2"></i>{{ $errors->first() }}
    </div>
    @endif

    <form method="POST" action="{{ route('client.register.store') }}" class="space-y-4" novalidate>
        @csrf

        <div class="grid grid-cols-2 gap-3">
            <div>
                <label for="name" class="auth-label">Nama Lengkap <span style="color: var(--apple-red)">*</span></label>
                <input type="text" id="name" name="name" value="{{ old('name') }}"
                       required autofocus autocomplete="name"
                       placeholder="Nama lengkap"
                       class="auth-input @error('name') !border-red-400 @enderror">
                @error('name')<p class="auth-error">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="company_name" class="auth-label">Nama Perusahaan</label>
                <input type="text" id="company_name" name="company_name" value="{{ old('company_name') }}"
                       autocomplete="organization"
                       placeholder="PT. Nama Perusahaan"
                       class="auth-input @error('company_name') !border-red-400 @enderror">
            </div>
        </div>

        <div>
            <label for="email" class="auth-label">Email <span style="color: var(--apple-red)">*</span></label>
            <div class="relative">
                <span class="absolute left-3 inset-y-0 flex items-center pointer-events-none" style="color: var(--text-tertiary);"><i class="fas fa-envelope text-xs"></i></span>
                <input type="email" id="email" name="email" value="{{ old('email') }}"
                       required autocomplete="email"
                       placeholder="email@perusahaan.com"
                       class="auth-input pl-9 @error('email') !border-red-400 @enderror">
            </div>
            @error('email')<p class="auth-error">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="phone" class="auth-label">No. Telepon</label>
            <div class="relative">
                <span class="absolute left-3 inset-y-0 flex items-center pointer-events-none" style="color: var(--text-tertiary);"><i class="fas fa-phone text-xs"></i></span>
                <input type="tel" id="phone" name="phone" value="{{ old('phone') }}"
                       autocomplete="tel"
                       placeholder="+62 8xx xxxx xxxx"
                       class="auth-input pl-9">
            </div>
        </div>

        <div>
            <label for="password" class="auth-label">Password <span style="color: var(--apple-red)">*</span></label>
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
                <input :type="showPwdConfirm ? 'text' : 'password'" id="password_confirmation" name="password_confirmation"
                       required autocomplete="new-password"
                       placeholder="Ulangi password"
                       class="auth-input pl-9 pr-10">
                <button type="button" @click="showPwdConfirm = !showPwdConfirm" class="absolute right-3 inset-y-0 flex items-center text-xs" style="color: var(--text-tertiary);">
                    <i :class="showPwdConfirm ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
                </button>
            </div>
        </div>

        <button type="submit" class="auth-btn-primary mt-2">
            <i class="fas fa-user-plus text-xs"></i>
            Buat Akun
        </button>
    </form>

    <div class="mt-5 text-center">
        <p class="text-sm" style="color: var(--text-secondary);">
            Sudah punya akun?
            <a href="{{ route('client.login') }}" class="font-semibold hover:underline" style="color: var(--client-primary);">Masuk</a>
        </p>
    </div>
</div>
@endsection
