@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 py-6">
    <div class="max-w-3xl mx-auto">
        <!-- Header -->
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Tambah Topic Baru</h1>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                Buat topic untuk artikel yang akan di-generate otomatis
            </p>
        </div>

        <form action="{{ route('auto-post.topics.store') }}" method="POST" class="space-y-6">
            @csrf

            <!-- Title -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Judul Topic <span class="text-red-500">*</span>
                </label>
                <input 
                    type="text" 
                    name="title" 
                    value="{{ old('title') }}" 
                    required
                    placeholder="Contoh: 10 Tips Mengurus IMB Dengan Cepat"
                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-blue-500 focus:ring-blue-500">
                @error('title')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                    Judul akan digunakan sebagai konteks untuk AI dalam generate artikel
                </p>
            </div>

            <!-- Description -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Deskripsi
                </label>
                <textarea 
                    name="description" 
                    rows="3"
                    placeholder="Brief description atau angle yang ingin dibahas..."
                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-blue-500 focus:ring-blue-500">{{ old('description') }}</textarea>
                @error('description')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror>
            </div>

            <!-- Category & Priority -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Kategori <span class="text-red-500">*</span>
                        </label>
                        <select 
                            name="category" 
                            required
                            class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Pilih Kategori</option>
                            <option value="tips" {{ old('category') === 'tips' ? 'selected' : '' }}>Tips</option>
                            <option value="guide" {{ old('category') === 'guide' ? 'selected' : '' }}>Guide</option>
                            <option value="case-study" {{ old('category') === 'case-study' ? 'selected' : '' }}>Case Study</option>
                            <option value="news" {{ old('category') === 'news' ? 'selected' : '' }}>News</option>
                            <option value="regulation" {{ old('category') === 'regulation' ? 'selected' : '' }}>Regulation</option>
                            <option value="general" {{ old('category') === 'general' ? 'selected' : '' }}>General</option>
                        </select>
                        @error('category')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Priority (1-10) <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="number" 
                            name="priority" 
                            value="{{ old('priority', 5) }}" 
                            min="1" 
                            max="10"
                            required
                            class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-blue-500 focus:ring-blue-500">
                        @error('priority')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">10 = Highest priority</p>
                    </div>
                </div>
            </div>

            <!-- Keywords -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Keywords <span class="text-red-500">*</span>
                </label>
                <div id="keywords" class="space-y-2">
                    <div class="flex items-center space-x-2">
                        <input 
                            type="text" 
                            name="keywords[]" 
                            required
                            placeholder="Masukkan keyword..."
                            class="flex-1 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-blue-500 focus:ring-blue-500">
                    </div>
                </div>
                <button 
                    type="button" 
                    onclick="addKeyword()"
                    class="mt-2 text-sm text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300">
                    + Tambah Keyword
                </button>
                @error('keywords')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
                <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                    Keywords digunakan AI untuk memahami konteks dan menghasilkan konten yang relevan
                </p>
            </div>

            <!-- Advanced Options -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-4">Advanced Options</h3>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Target Audience
                        </label>
                        <input 
                            type="text" 
                            name="target_audience" 
                            value="{{ old('target_audience') }}" 
                            placeholder="Contoh: Pengusaha UMKM, Developer properti"
                            class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Content Angle
                        </label>
                        <input 
                            type="text" 
                            name="content_angle" 
                            value="{{ old('content_angle') }}" 
                            placeholder="Contoh: Panduan praktis, Analisis mendalam, Tips hemat biaya"
                            class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    <label class="flex items-center">
                        <input 
                            type="checkbox" 
                            name="is_evergreen" 
                            value="1"
                            {{ old('is_evergreen') ? 'checked' : '' }}
                            class="rounded border-gray-300 dark:border-gray-600 text-blue-600 focus:ring-blue-500">
                        <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">
                            Evergreen Content (dapat digunakan berkali-kali)
                        </span>
                    </label>
                </div>
            </div>

            <!-- Submit Buttons -->
            <div class="flex justify-end space-x-3">
                <a href="{{ route('auto-post.topics.index') }}" class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600">
                    Batal
                </a>
                <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    Simpan Topic
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function addKeyword() {
    const container = document.getElementById('keywords');
    const div = document.createElement('div');
    div.className = 'flex items-center space-x-2';
    div.innerHTML = `
        <input 
            type="text" 
            name="keywords[]" 
            placeholder="Masukkan keyword..."
            class="flex-1 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-blue-500 focus:ring-blue-500">
        <button type="button" onclick="this.parentElement.remove()" class="px-3 py-2 text-red-600 hover:text-red-700 dark:text-red-400">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
    `;
    container.appendChild(div);
}
</script>
@endpush
@endsection
