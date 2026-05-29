@extends('client.layouts.auth')

@section('title', 'Lupa Password — Bizmark.ID')

@section('hero-title', 'Reset Password Anda')
@section('hero-subtitle', 'Masukkan email terdaftar dan kami akan kirim link reset password Anda.')

@section('form')
<div class="auth-card p-7 sm:p-8">
    <div class="mb-7">
        <span class="inline-flex items-center gap-1.5 px-3 py-1 text-[10px] font-semibold rounded-full mb-3"
              style="background: rgba(10,102,194,0.08); color: var(--client-primary); border: 1px solid rgba(10,102,194,0.15);">
            <i class="fas fa-key text-[8px]" aria-hidden="true"></i>
            Reset Password
        </span>
        <h1 class="text-2xl font-bold" style="color: var(--text-primary);">Lupa Password?</h1>
        <p class="text-sm mt-1" style="color: var(--text-secondary);">Masukkan email dan kami kirimkan link reset.</p>
    </div>

    @if(session('status'))
    <div class="flex items-start gap-2 mb-5 px-4 py-3 rounded-xl text-sm text-green-800 dark:text-green-300"
         style="background: rgba(34,197,94,0.08); border: 1px solid rgba(34,197,94,0.2);">
        <i class="fas fa-circle-check text-green-500 mt-0.5 flex-shrink-0"></i>
        {{ session('status') }}
    </div>
    @endif

    @if($errors->any())
    <div class="mb-5 px-4 py-3 rounded-xl text-sm text-red-700"
         style="background: rgba(239,68,68,0.08); border: 1px solid rgba(239,68,68,0.2);">
        <i class="fas fa-circle-xmark mr-2"></i>{{ $errors->first() }}
    </div>
    @endif

    <form method="POST" action="{{ route('client.password.email') }}" class="space-y-5">
        @csrf
        <div>
            <label for="email" class="auth-label">Alamat Email</label>
            <div class="relative">
                <span class="absolute left-3 inset-y-0 flex items-center pointer-events-none" style="color: var(--text-tertiary);">
                    <i class="fas fa-envelope text-xs"></i>
                </span>
                <input type="email" id="email" name="email" value="{{ old('email') }}"
                       required autofocus autocomplete="email"
                       placeholder="email@perusahaan.com"
                       class="auth-input pl-9 @error('email') !border-red-400 @enderror">
            </div>
            @error('email')<p class="auth-error">{{ $message }}</p>@enderror
        </div>
        <button type="submit" class="auth-btn-primary">
            <i class="fas fa-paper-plane text-xs"></i>
            Kirim Link Reset
        </button>
    </form>

    <div class="mt-6 text-center">
        <a href="{{ route('client.login') }}" class="text-sm font-semibold hover:underline flex items-center justify-center gap-1.5" style="color: var(--client-primary);">
            <i class="fas fa-arrow-left text-xs"></i> Kembali ke Login
        </a>
    </div>
</div>
@endsection
