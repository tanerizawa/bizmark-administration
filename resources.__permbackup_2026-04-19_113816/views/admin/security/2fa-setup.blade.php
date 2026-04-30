@extends('layouts.app')

@section('title', 'Setup 2FA')

@section('content')
<div class="max-w-3xl mx-auto px-4 py-6">
    <h1 class="text-2xl font-semibold mb-4">Setup Two-Factor Authentication (2FA)</h1>

    <div class="bg-white rounded-lg shadow p-6 space-y-4">
        <div>
            <div class="text-sm text-gray-600 mb-2">Secret</div>
            <div class="font-mono text-lg break-all">{{ $secret }}</div>
        </div>

        <div>
            <div class="text-sm text-gray-600 mb-2">OTPAuth URL</div>
            <div class="font-mono text-xs break-all">{{ $otpauthUrl }}</div>
        </div>

        <form method="POST" action="{{ route('admin.security.2fa.enable') }}" class="space-y-3">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700">Kode dari authenticator</label>
                <input name="code" type="text" inputmode="numeric" autocomplete="one-time-code" class="mt-1 w-full border rounded px-3 py-2" required>
                @error('code')
                    <div class="text-sm text-red-600 mt-1">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="inline-flex items-center px-4 py-2 rounded bg-purple-600 text-white hover:bg-purple-700">
                Aktifkan 2FA
            </button>
        </form>
    </div>
</div>
@endsection

