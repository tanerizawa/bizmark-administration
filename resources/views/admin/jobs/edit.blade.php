@extends('layouts.app')

@section('title', 'Edit Lowongan Kerja')

@section('content')
<div class="px-4 py-6 max-w-7xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('admin.jobs.index') }}" class="inline-flex items-center text-sm text-gray-400 hover:text-white mb-3">
            <i class="fas fa-arrow-left mr-2"></i>Kembali
        </a>
        <h1 class="text-2xl font-bold text-white">Edit Lowongan Kerja</h1>
        <p class="text-gray-400 mt-1">Perbarui informasi lowongan pekerjaan</p>
    </div>

    <form action="{{ route('admin.jobs.update', $vacancy->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Left: Main Info --}}
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-gray-800 border border-gray-700 rounded-xl p-6 shadow">
                    <h5 class="text-white font-semibold text-lg mb-5">Informasi Dasar</h5>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-white mb-1">Judul Lowongan <span class="text-red-400">*</span></label>
                        <input type="text" name="title" value="{{ old('title', $vacancy->title) }}" required
                            class="w-full bg-gray-900 text-white border border-gray-600 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('title') border-red-500 @enderror">
                        @error('title')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-white mb-1">Posisi <span class="text-red-400">*</span></label>
                        <input type="text" name="position" value="{{ old('position', $vacancy->position) }}" required
                            class="w-full bg-gray-900 text-white border border-gray-600 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('position') border-red-500 @enderror">
                        @error('position')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-white mb-1">Deskripsi Pekerjaan <span class="text-red-400">*</span></label>
                        <textarea name="description" rows="6" required
                            class="w-full bg-gray-900 text-white border border-gray-600 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('description') border-red-500 @enderror">{{ old('description', $vacancy->description) }}</textarea>
                        @error('description')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    {{-- Responsibilities --}}
                    <div class="mb-4" id="responsibilities-container">
                        <label class="block text-sm font-medium text-white mb-1">Tanggung Jawab <span class="text-red-400">*</span></label>
                        <div class="responsibilities-list space-y-2">
                            @if(is_array($vacancy->responsibilities) && count($vacancy->responsibilities) > 0)
                                @foreach($vacancy->responsibilities as $item)
                                <div class="flex gap-2">
                                    <input type="text" name="responsibilities[]" value="{{ $item }}"
                                        class="flex-1 bg-gray-900 text-white border border-gray-600 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <button type="button" onclick="this.parentElement.remove()"
                                        class="px-3 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg text-sm"><i class="fas fa-times"></i></button>
                                </div>
                                @endforeach
                            @else
                                <div class="flex gap-2">
                                    <input type="text" name="responsibilities[]" placeholder="Tanggung jawab 1"
                                        class="flex-1 bg-gray-900 text-white border border-gray-600 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <button type="button" onclick="this.parentElement.remove()"
                                        class="px-3 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg text-sm"><i class="fas fa-times"></i></button>
                                </div>
                            @endif
                        </div>
                        <button type="button" onclick="addField('responsibilities')"
                            class="mt-2 inline-flex items-center text-sm text-blue-400 hover:text-blue-300">
                            <i class="fas fa-plus mr-1"></i>Tambah
                        </button>
                    </div>

                    {{-- Qualifications --}}
                    <div class="mb-4" id="qualifications-container">
                        <label class="block text-sm font-medium text-white mb-1">Kualifikasi <span class="text-red-400">*</span></label>
                        <div class="qualifications-list space-y-2">
                            @if(is_array($vacancy->qualifications) && count($vacancy->qualifications) > 0)
                                @foreach($vacancy->qualifications as $item)
                                <div class="flex gap-2">
                                    <input type="text" name="qualifications[]" value="{{ $item }}"
                                        class="flex-1 bg-gray-900 text-white border border-gray-600 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <button type="button" onclick="this.parentElement.remove()"
                                        class="px-3 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg text-sm"><i class="fas fa-times"></i></button>
                                </div>
                                @endforeach
                            @else
                                <div class="flex gap-2">
                                    <input type="text" name="qualifications[]" placeholder="Kualifikasi 1"
                                        class="flex-1 bg-gray-900 text-white border border-gray-600 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <button type="button" onclick="this.parentElement.remove()"
                                        class="px-3 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg text-sm"><i class="fas fa-times"></i></button>
                                </div>
                            @endif
                        </div>
                        <button type="button" onclick="addField('qualifications')"
                            class="mt-2 inline-flex items-center text-sm text-blue-400 hover:text-blue-300">
                            <i class="fas fa-plus mr-1"></i>Tambah
                        </button>
                    </div>

                    {{-- Benefits --}}
                    <div class="mb-4" id="benefits-container">
                        <label class="block text-sm font-medium text-white mb-1">Benefit/Keuntungan</label>
                        <div class="benefits-list space-y-2">
                            @if(is_array($vacancy->benefits) && count($vacancy->benefits) > 0)
                                @foreach($vacancy->benefits as $item)
                                <div class="flex gap-2">
                                    <input type="text" name="benefits[]" value="{{ $item }}"
                                        class="flex-1 bg-gray-900 text-white border border-gray-600 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <button type="button" onclick="this.parentElement.remove()"
                                        class="px-3 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg text-sm"><i class="fas fa-times"></i></button>
                                </div>
                                @endforeach
                            @else
                                <div class="flex gap-2">
                                    <input type="text" name="benefits[]" placeholder="Benefit 1"
                                        class="flex-1 bg-gray-900 text-white border border-gray-600 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <button type="button" onclick="this.parentElement.remove()"
                                        class="px-3 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg text-sm"><i class="fas fa-times"></i></button>
                                </div>
                            @endif
                        </div>
                        <button type="button" onclick="addField('benefits')"
                            class="mt-2 inline-flex items-center text-sm text-blue-400 hover:text-blue-300">
                            <i class="fas fa-plus mr-1"></i>Tambah
                        </button>
                    </div>
                </div>
            </div>

            {{-- Right: Details + Actions --}}
            <div class="space-y-6">
                <div class="bg-gray-800 border border-gray-700 rounded-xl p-6 shadow">
                    <h5 class="text-white font-semibold text-lg mb-5">Detail Pekerjaan</h5>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-white mb-1">Tipe Pekerjaan <span class="text-red-400">*</span></label>
                        <select name="employment_type" required class="w-full bg-gray-900 text-white border border-gray-600 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @foreach(['full-time' => 'Full Time', 'part-time' => 'Part Time', 'contract' => 'Kontrak', 'internship' => 'Magang', 'remote' => 'Remote'] as $val => $label)
                            <option value="{{ $val }}" {{ old('employment_type', $vacancy->employment_type) == $val ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-white mb-1">Lokasi <span class="text-red-400">*</span></label>
                        <input type="text" name="location" value="{{ old('location', $vacancy->location) }}" required
                            class="w-full bg-gray-900 text-white border border-gray-600 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-white mb-1">Gaji Minimum (Rp)</label>
                        <input type="number" name="salary_min" value="{{ old('salary_min', $vacancy->salary_min) }}"
                            class="w-full bg-gray-900 text-white border border-gray-600 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-white mb-1">Gaji Maksimum (Rp)</label>
                        <input type="number" name="salary_max" value="{{ old('salary_max', $vacancy->salary_max) }}"
                            class="w-full bg-gray-900 text-white border border-gray-600 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div class="mb-4 flex items-center gap-2">
                        <input type="checkbox" name="salary_negotiable" value="1" id="salary_negotiable"
                            {{ old('salary_negotiable', $vacancy->salary_negotiable) ? 'checked' : '' }}
                            class="w-4 h-4 accent-blue-500">
                        <label for="salary_negotiable" class="text-white text-sm">Gaji bisa dinegosiasi</label>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-white mb-1">Deadline Lamaran</label>
                        <input type="date" name="deadline" value="{{ old('deadline', $vacancy->deadline ? $vacancy->deadline->format('Y-m-d') : '') }}"
                            class="w-full bg-gray-900 text-white border border-gray-600 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-white mb-1">Google Form URL (Backup)</label>
                        <input type="url" name="google_form_url" value="{{ old('google_form_url', $vacancy->google_form_url) }}" placeholder="https://forms.gle/..."
                            class="w-full bg-gray-900 text-white border border-gray-600 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-white mb-1">Status <span class="text-red-400">*</span></label>
                        <select name="status" required class="w-full bg-gray-900 text-white border border-gray-600 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @foreach(['draft' => 'Draft', 'open' => 'Aktif/Terbuka', 'closed' => 'Ditutup'] as $val => $label)
                            <option value="{{ $val }}" {{ old('status', $vacancy->status) == $val ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="flex flex-col gap-3">
                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2.5 rounded-xl transition">
                        <i class="fas fa-save mr-2"></i>Update Lowongan
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
