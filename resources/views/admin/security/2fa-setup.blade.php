@extends('layouts.app')

@section('title', 'Setup 2FA')

@section('content')
<div class="max-w-3xl mx-auto px-4 py-6">
    <h1 class="text-2xl font-semibold text-white mb-4">Setup Two-Factor Authentication (2FA)</h1>

    <div class="card-elevated rounded-apple-lg p-6 space-y-4">
        <div>
            <div class="text-sm text-dark-text-secondary mb-2">Secret</div>
            <div class="font-mono text-lg text-dark-text-primary break-all">{{ $secret }}</div>
        </div>

        <div>
            <div class="text-sm text-dark-text-secondary mb-2">OTPAuth URL</div>
            <div class="font-mono text-xs text-dark-text-primary break-all">{{ $otpauthUrl }}</div>
        </div>

        <form method="POST" action="{{ route('admin.security.2fa.enable') }}" class="space-y-3">
            @csrf
            <div>
                <label class="block text-sm font-medium text-dark-text-secondary">Kode dari authenticator</label>
                <input name="code" type="text" inputmode="numeric" autocomplete="one-time-code"
                       class="input-dark w-full px-3 py-2 rounded-apple text-sm mt-1" required>
                @error('code')
                    <div class="text-sm text-apple-red mt-1">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="inline-flex items-center px-4 py-2 rounded-apple bg-apple-blue text-white hover:bg-apple-blue-dark transition-colors">
                Aktifkan 2FA
            </button>
        </form>
    </div>
</div>
@endsection
