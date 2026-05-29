@extends('client.layouts.auth')

@section('title', 'Verifikasi Email — Bizmark.ID')

@section('hero-title', 'Satu Langkah Lagi')
@section('hero-subtitle', 'Verifikasi email Anda untuk mengaktifkan akses penuh ke portal perizinan.')

@section('form')
<div class="auth-card p-7 sm:p-8">
    <div class="mb-7 text-center">
        <div class="w-16 h-16 rounded-full mx-auto mb-4 flex items-center justify-center"
             style="background: rgba(10,102,194,0.1);">
            <i class="fas fa-envelope-open-text text-2xl" style="color: var(--client-primary);" aria-hidden="true"></i>
        </div>
        <h1 class="text-xl font-bold" style="color: var(--text-primary);">Verifikasi Email Anda</h1>
        <p class="text-sm mt-2" style="color: var(--text-secondary);">
            Kami mengirimkan link verifikasi ke email Anda. Cek inbox (atau folder spam) Anda.
        </p>
    </div>

    @if(session('status') === 'verification-link-sent')
    <div class="flex items-start gap-2 mb-5 px-4 py-3 rounded-xl text-sm text-green-800"
         style="background: rgba(34,197,94,0.08); border: 1px solid rgba(34,197,94,0.2);">
        <i class="fas fa-circle-check text-green-500 mt-0.5 flex-shrink-0"></i>
        Link verifikasi baru telah dikirim ke email Anda.
    </div>
    @endif

    <div class="space-y-3">
        <form method="POST" action="{{ route('client.verification.send') }}">
            @csrf
            <button type="submit" class="auth-btn-primary">
                <i class="fas fa-paper-plane text-xs"></i>
                Kirim Ulang Email Verifikasi
            </button>
        </form>

        <form method="POST" action="{{ route('client.logout') }}">
            @csrf
            <button type="submit" class="w-full py-2.5 text-sm font-semibold rounded-xl transition-colors"
                    style="color: var(--text-secondary); background: var(--surface-cool); border: 1px solid var(--border-subtle);">
                <i class="fas fa-right-from-bracket text-xs mr-1"></i>
                Keluar
            </button>
        </form>
    </div>
</div>
@endsection
