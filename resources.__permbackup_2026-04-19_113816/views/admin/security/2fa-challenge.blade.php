@extends('layouts.app')

@section('title', '2FA Challenge')

@section('content')
<div class="max-w-xl mx-auto px-4 py-10">
    <h1 class="text-2xl font-semibold mb-4">Verifikasi 2FA</h1>

    <div class="bg-white rounded-lg shadow p-6 space-y-4">
        <form method="POST" action="{{ route('admin.security.2fa.verify') }}" class="space-y-3">
            @csrf

            <div>
                <label class="block text-sm font-medium text-gray-700">Kode 6 digit (atau backup code)</label>
                <input name="code" type="text" autocomplete="one-time-code" class="mt-1 w-full border rounded px-3 py-2" required>
                @error('code')
                    <div class="text-sm text-red-600 mt-1">{{ $message }}</div>
                @enderror
            </div>

            <label class="inline-flex items-center gap-2 text-sm text-gray-700">
                <input type="checkbox" name="trust_device" value="1">
                Trust this device (30 hari)
            </label>

            <button type="submit" class="inline-flex items-center px-4 py-2 rounded bg-purple-600 text-white hover:bg-purple-700">
                Verifikasi
            </button>
        </form>
    </div>
</div>
@endsection

