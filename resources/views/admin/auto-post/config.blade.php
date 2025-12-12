@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 py-6">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Konfigurasi Auto-Post</h1>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    Atur sistem posting artikel otomatis dengan AI
                </p>
            </div>
            <div class="flex items-center space-x-3">
                <!-- Status Toggle -->
                <button 
                    id="toggleAutoPost"
                    data-enabled="{{ $config->is_enabled ? 'true' : 'false' }}"
                    class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 {{ $config->is_enabled ? 'bg-blue-600' : 'bg-gray-200 dark:bg-gray-700' }}">
                    <span class="sr-only">Toggle auto-post</span>
                    <span class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform {{ $config->is_enabled ? 'translate-x-6' : 'translate-x-1' }}"></span>
                </button>
                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                    {{ $config->is_enabled ? 'Aktif' : 'Nonaktif' }}
                </span>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-4 rounded-lg bg-green-50 dark:bg-green-900/20 p-4 text-sm text-green-800 dark:text-green-300">
            {{ session('success') }}
        </div>
    @endif

    <!-- Configuration Form -->
    <form action="{{ route('auto-post.config.update') }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        <!-- Scheduling Settings -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Pengaturan Jadwal</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Jumlah Post Per Hari
                    </label>
                    <input 
                        type="number" 
                        name="posts_per_day" 
                        value="{{ old('posts_per_day', $config->posts_per_day) }}" 
                        min="1" 
                        max="10"
                        class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-blue-500 focus:ring-blue-500">
                    @error('posts_per_day')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Auto-Schedule Days
                    </label>
                    <input 
                        type="number" 
                        name="auto_schedule_days" 
                        value="{{ old('auto_schedule_days', $config->auto_schedule_days) }}" 
                        min="1" 
                        max="30"
                        class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-blue-500 focus:ring-blue-500">
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Berapa hari ke depan untuk auto-schedule</p>
                </div>
            </div>

            <div class="mt-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Waktu Posting (Format 24 jam: HH:MM)
                </label>
                <div id="postTimes" class="space-y-2">
                    @foreach($config->post_times as $index => $time)
                        <div class="flex items-center space-x-2">
                            <input 
                                type="time" 
                                name="post_times[]" 
                                value="{{ $time }}"
                                class="flex-1 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-blue-500 focus:ring-blue-500">
                            @if($index > 0)
                                <button type="button" onclick="this.parentElement.remove()" class="px-3 py-2 text-red-600 hover:text-red-700 dark:text-red-400">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            @endif
                        </div>
                    @endforeach
                </div>
                <button 
                    type="button" 
                    onclick="addTimeSlot()"
                    class="mt-2 text-sm text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300">
                    + Tambah Waktu
                </button>
            </div>
        </div>

        <!-- AI Settings -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Pengaturan AI</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        AI Model
                    </label>
                    <select 
                        name="ai_model" 
                        class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-blue-500 focus:ring-blue-500">
                        <option value="anthropic/claude-3.5-sonnet" {{ $config->ai_model === 'anthropic/claude-3.5-sonnet' ? 'selected' : '' }}>
                            Claude 3.5 Sonnet (Recommended)
                        </option>
                        <option value="anthropic/claude-3-haiku" {{ $config->ai_model === 'anthropic/claude-3-haiku' ? 'selected' : '' }}>
                            Claude 3 Haiku (Faster)
                        </option>
                        <option value="openai/gpt-4" {{ $config->ai_model === 'openai/gpt-4' ? 'selected' : '' }}>
                            GPT-4
                        </option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Temperature (0-2)
                    </label>
                    <input 
                        type="number" 
                        name="temperature" 
                        value="{{ old('temperature', $config->temperature) }}" 
                        min="0" 
                        max="2" 
                        step="0.1"
                        class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-blue-500 focus:ring-blue-500">
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">0 = Konsisten, 2 = Kreatif</p>
                </div>
            </div>
        </div>

        <!-- Content Quality Settings -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Kualitas Konten</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Min Word Count
                    </label>
                    <input 
                        type="number" 
                        name="min_word_count" 
                        value="{{ old('min_word_count', $config->min_word_count) }}" 
                        min="300"
                        class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-blue-500 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Max Word Count
                    </label>
                    <input 
                        type="number" 
                        name="max_word_count" 
                        value="{{ old('max_word_count', $config->max_word_count) }}" 
                        min="500"
                        class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-blue-500 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Quality Threshold (0-100)
                    </label>
                    <input 
                        type="number" 
                        name="quality_threshold" 
                        value="{{ old('quality_threshold', $config->quality_threshold) }}" 
                        min="0" 
                        max="100"
                        class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-blue-500 focus:ring-blue-500">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Similarity Threshold (0-1)
                    </label>
                    <input 
                        type="number" 
                        name="similarity_threshold" 
                        value="{{ old('similarity_threshold', $config->similarity_threshold) }}" 
                        min="0" 
                        max="1" 
                        step="0.01"
                        class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-blue-500 focus:ring-blue-500">
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Deteksi duplikat artikel</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Internal Links Count
                    </label>
                    <input 
                        type="number" 
                        name="internal_links_count" 
                        value="{{ old('internal_links_count', $config->internal_links_count) }}" 
                        min="0" 
                        max="10"
                        class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-blue-500 focus:ring-blue-500">
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Jumlah anchor link otomatis</p>
                </div>
            </div>
        </div>

        <!-- Publishing Options -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Opsi Publishing</h2>
            
            <div class="space-y-4">
                <label class="flex items-center">
                    <input 
                        type="checkbox" 
                        name="auto_publish" 
                        value="1"
                        {{ $config->auto_publish ? 'checked' : '' }}
                        class="rounded border-gray-300 dark:border-gray-600 text-blue-600 focus:ring-blue-500">
                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">
                        Auto-publish artikel (langsung published tanpa review)
                    </span>
                </label>

                <label class="flex items-center">
                    <input 
                        type="checkbox" 
                        name="auto_add_tags" 
                        value="1"
                        {{ $config->auto_add_tags ? 'checked' : '' }}
                        class="rounded border-gray-300 dark:border-gray-600 text-blue-600 focus:ring-blue-500">
                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">
                        Auto-generate tags dari konten
                    </span>
                </label>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="flex justify-end space-x-3">
            <a href="{{ route('articles.index') }}" class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600">
                Batal
            </a>
            <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                Simpan Konfigurasi
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
function addTimeSlot() {
    const container = document.getElementById('postTimes');
    const div = document.createElement('div');
    div.className = 'flex items-center space-x-2';
    div.innerHTML = `
        <input 
            type="time" 
            name="post_times[]" 
            class="flex-1 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-blue-500 focus:ring-blue-500">
        <button type="button" onclick="this.parentElement.remove()" class="px-3 py-2 text-red-600 hover:text-red-700 dark:text-red-400">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
    `;
    container.appendChild(div);
}

// Toggle auto-post
document.getElementById('toggleAutoPost').addEventListener('click', function() {
    const button = this;
    const isEnabled = button.dataset.enabled === 'true';
    
    fetch('{{ route('auto-post.config.toggle') }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            button.dataset.enabled = data.is_enabled ? 'true' : 'false';
            button.classList.toggle('bg-blue-600', data.is_enabled);
            button.classList.toggle('bg-gray-200', !data.is_enabled);
            button.classList.toggle('dark:bg-gray-700', !data.is_enabled);
            button.querySelector('span:last-child').classList.toggle('translate-x-6', data.is_enabled);
            button.querySelector('span:last-child').classList.toggle('translate-x-1', !data.is_enabled);
            button.nextElementSibling.nextElementSibling.textContent = data.is_enabled ? 'Aktif' : 'Nonaktif';
            
            // Show toast
            const toast = document.createElement('div');
            toast.className = 'fixed bottom-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50';
            toast.textContent = data.message;
            document.body.appendChild(toast);
            setTimeout(() => toast.remove(), 3000);
        }
    });
});
</script>
@endpush
@endsection
