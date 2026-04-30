@extends('layouts.app')

@section('title', 'Recovery Codes')

@section('content')
<div class="max-w-3xl mx-auto px-4 py-6">
    <h1 class="text-2xl font-semibold mb-4">Backup Codes</h1>

    <div class="bg-white rounded-lg shadow p-6 space-y-4">
        <div class="text-sm text-gray-700">
            Simpan kode berikut di tempat aman. Setiap kode hanya bisa dipakai sekali.
        </div>

        <div class="grid grid-cols-2 gap-2">
            @foreach($codes as $c)
                <div class="font-mono bg-gray-50 border rounded px-3 py-2">{{ $c }}</div>
            @endforeach
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ url('/admin/dashboard') }}" class="inline-flex items-center px-4 py-2 rounded bg-purple-600 text-white hover:bg-purple-700">
                Selesai
            </a>

            <form method="POST" action="{{ route('admin.security.2fa.recovery.regenerate') }}">
                @csrf
                <button type="submit" class="inline-flex items-center px-4 py-2 rounded border hover:bg-gray-50">
                    Generate ulang
                </button>
            </form>
        </div>
    </div>
</div>
@endsection

