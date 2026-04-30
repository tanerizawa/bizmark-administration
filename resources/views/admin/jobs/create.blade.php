@extends('layouts.app')

@section('title', 'Tambah Lowongan Kerja')

@section('content')
<div class="px-4 py-6 max-w-7xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('admin.jobs.index') }}" class="inline-flex items-center text-sm text-gray-400 hover:text-white mb-3">
            <i class="fas fa-arrow-left mr-2"></i>Kembali
        </a>
        <h1 class="text-2xl font-bold text-white">Tambah Lowongan Kerja</h1>
        <p class="text-gray-400 mt-1">Buat lowongan pekerjaan baru</p>
    </div>

    <form action="{{ route('admin.jobs.store') }}" method="POST">
        @csrf
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Left: Main Info --}}
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-gray-800 border border-gray-700 rounded-xl p-6 shadow">
                    <h5 class="text-white font-semibold text-lg mb-5">Informasi Dasar</h5>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-white mb-1">Judul Lowongan <span class="text-red-400">*</span></label>
                        <input type="text" name="title" value="{{ old('title') }}" required
                            class="w-full bg-gray-900 text-white border border-gray-600 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('title') border-red-500 @enderror">
                        @error('title')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-white mb-1">Posisi <span class="text-red-400">*</span></label>
                        <input type="text" name="position" value="{{ old('position') }}" required
                            class="w-full bg-gray-900 text-white border border-gray-600 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('position') border-red-500 @enderror">
                        @error('position')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-white mb-1">Deskripsi Pekerjaan <span class="text-red-400">*</span></label>
                        <textarea name="description" rows="6" required
                            class="w-full bg-gray-900 text-white border border-gray-600 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('description') border-red-500 @enderror">{{ old('description') }}</textarea>
                        @error('description')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    @foreach(['responsibilities' => 'Tanggung Jawab', 'qualifications' => 'Kualifikasi', 'benefits' => 'Benefit/Keuntungan'] as $field => $label)
                    <div class="mb-4" id="{{ $field }}-container">
                        <label class="block text-sm font-medium text-white mb-1">{{ $label }}{{ $field !== 'benefits' ? ' <span class="text-red-400">*</span>' : '' }}</label>
                        <div class="{{ $field }}-list space-y-2">
                            <div class="flex gap-2">
                                <input type="text" name="{{ $field }}[]" placeholder="{{ $label }} 1"
                                    class="flex-1 bg-gray-900 text-white border border-gray-600 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <button type="button" onclick="this.parentElement.remove()"
                                    class="px-3 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg text-sm">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                        <button type="button" onclick="addField('{{ $field }}')"
                            class="mt-2 inline-flex items-center text-sm text-blue-400 hover:text-blue-300">
                            <i class="fas fa-plus mr-1"></i>Tambah
                        </button>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Right: Details + Actions --}}
            <div class="space-y-6">
                <div class="bg-gray-800 border border-gray-700 rounded-xl p-6 shadow">
                    <h5 class="text-white font-semibold text-lg mb-5">Detail Pekerjaan</h5>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-white mb-1">Tipe Pekerjaan <span class="text-red-400">*</span></label>
                        <select name="employment_type" required class="w-full bg-gray-900 text-white border border-gray-600 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="full-time">Full Time</option>
                            <option value="part-time">Part Time</option>
                            <option value="contract">Kontrak</option>
                            <option value="internship">Magang</option>
                            <option value="remote">Remote</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-white mb-1">Lokasi <span class="text-red-400">*</span></label>
                        <input type="text" name="location" value="{{ old('location', 'Jakarta') }}" required
                            class="w-full bg-gray-900 text-white border border-gray-600 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-white mb-1">Gaji Minimum (Rp)</label>
                        <input type="number" name="salary_min" value="{{ old('salary_min') }}"
                            class="w-full bg-gray-900 text-white border border-gray-600 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-white mb-1">Gaji Maksimum (Rp)</label>
                        <input type="number" name="salary_max" value="{{ old('salary_max') }}"
                            class="w-full bg-gray-900 text-white border border-gray-600 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div class="mb-4 flex items-center gap-2">
                        <input type="checkbox" name="salary_negotiable" value="1" id="salary_negotiable" checked
                            class="w-4 h-4 accent-blue-500">
                        <label for="salary_negotiable" class="text-white text-sm">Gaji bisa dinegosiasi</label>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-white mb-1">Deadline Lamaran</label>
                        <input type="date" name="deadline" value="{{ old('deadline') }}"
                            class="w-full bg-gray-900 text-white border border-gray-600 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-white mb-1">Google Form URL (Backup)</label>
                        <input type="url" name="google_form_url" value="{{ old('google_form_url') }}" placeholder="https://forms.gle/..."
                            class="w-full bg-gray-900 text-white border border-gray-600 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-white mb-1">Status <span class="text-red-400">*</span></label>
                        <select name="status" required class="w-full bg-gray-900 text-white border border-gray-600 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="draft">Draft</option>
                            <option value="open">Aktif/Terbuka</option>
                            <option value="closed">Ditutup</option>
                        </select>
                    </div>
                </div>

                <div class="flex flex-col gap-3">
                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2.5 rounded-xl transition">
                        <i class="fas fa-save mr-2"></i>Simpan Lowongan
                    </button>
                    <a href="{{ route('admin.jobs.index') }}" class="w-full text-center border border-gray-600 text-gray-300 hover:text-white hover:border-gray-400 font-medium py-2.5 rounded-xl transition">
                        Batal
                    </a>
                </div>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
function addField(type) {
    const list = document.querySelector(`#${type}-container .${type}-list`);
    const div = document.createElement('div');
    div.className = 'flex gap-2';
    div.innerHTML = `
        <input type="text" name="${type}[]" placeholder="${type.charAt(0).toUpperCase() + type.slice(1)} baru"
            class="flex-1 bg-gray-900 text-white border border-gray-600 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
        <button type="button" onclick="this.parentElement.remove()"
            class="px-3 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg text-sm">
            <i class="fas fa-times"></i>
        </button>
    `;
    list.appendChild(div);
}
</script>
@endpush
@endsection
