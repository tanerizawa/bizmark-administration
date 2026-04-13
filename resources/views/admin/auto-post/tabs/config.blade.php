{{-- Configuration Form - Compact --}}
@php
    // Ensure Blade @error checks do not fail in non-HTTP render contexts.
    if (!isset($errors)) {
        $errors = new \Illuminate\Support\ViewErrorBag();
    }
@endphp

<form action="{{ route('auto-post.config.update') }}" method="POST" class="space-y-3">
    @csrf
    @method('PUT')

    {{-- Scheduling Settings --}}
    <div class="bg-dark-bg-tertiary rounded-apple p-3 auto-soft-card">
        <h2 class="admin-section text-white mb-3 flex items-center gap-2">
            <i class="fas fa-clock text-apple-blue" style="font-size: 0.75rem;"></i>
            Pengaturan Jadwal
        </h2>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
            <div>
                <label class="admin-label-compact block">Post Per Hari</label>
                <input 
                    type="number" 
                    name="posts_per_day" 
                    value="{{ old('posts_per_day', $config->posts_per_day) }}" 
                    min="1" 
                    max="24"
                    class="admin-input w-full rounded bg-dark-bg-secondary auto-soft-field text-white focus:border-apple-blue focus:ring-1 focus:ring-apple-blue">
                <p class="mt-1 admin-small" style="color: rgba(235,235,245,0.6);">Target rekomendasi SEO: 15 slot/hari di jam peak.</p>
                @error('posts_per_day')
                    <p class="mt-0.5 admin-small text-apple-red">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="admin-label-compact block">Cooldown (hari)</label>
                <input 
                    type="number" 
                    name="cooldown_days" 
                    value="{{ old('cooldown_days', $config->cooldown_days) }}" 
                    min="1" 
                    max="365"
                    class="admin-input w-full rounded bg-dark-bg-secondary auto-soft-field text-white focus:border-apple-blue focus:ring-1 focus:ring-apple-blue">
            </div>

            <div>
                <label class="admin-label-compact block">Waktu Posting</label>
                <div id="postTimes" class="space-y-1">
                    @foreach($config->post_times ?? ['09:00'] as $index => $time)
                        <div class="flex items-center gap-1">
                            <input 
                                type="time" 
                                name="post_times[]" 
                                value="{{ $time }}"
                                class="admin-input flex-1 rounded bg-dark-bg-secondary auto-soft-field text-white focus:border-apple-blue">
                            @if($index > 0)
                                <button type="button" onclick="this.parentElement.remove()" class="px-2 py-1 text-apple-red hover:text-red-400">
                                    <i class="fas fa-times" style="font-size: 0.625rem;"></i>
                                </button>
                            @endif
                        </div>
                    @endforeach
                </div>
                <button type="button" onclick="addTimeSlot()" class="mt-1 admin-small text-apple-blue hover:text-blue-400">
                    + Tambah
                </button>
                <button type="button" onclick="applyPeakTimePreset()" class="mt-1 ml-2 admin-small text-apple-green hover:text-green-400">
                    Gunakan Preset 15 Jam Peak
                </button>
                @error('post_times')
                    <p class="mt-0.5 admin-small text-apple-red">{{ $message }}</p>
                @enderror
            </div>
        </div>
    </div>

    {{-- AI Settings --}}
    <div class="bg-dark-bg-tertiary rounded-apple p-3 auto-soft-card">
        <h2 class="admin-section text-white mb-3 flex items-center gap-2">
            <i class="fas fa-brain text-apple-purple" style="font-size: 0.75rem;"></i>
            Pengaturan AI
        </h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            <div>
                <label class="admin-label-compact block">AI Model</label>
                <select 
                    name="ai_model" 
                    class="admin-input admin-select w-full rounded bg-dark-bg-secondary auto-soft-field text-white focus:border-apple-blue">
                    <option value="anthropic/claude-3.5-sonnet" {{ $config->ai_model === 'anthropic/claude-3.5-sonnet' ? 'selected' : '' }}>
                        Claude 3.5 Sonnet
                    </option>
                    <option value="anthropic/claude-3-haiku" {{ $config->ai_model === 'anthropic/claude-3-haiku' ? 'selected' : '' }}>
                        Claude 3 Haiku (Faster)
                    </option>
                    <option value="openai/gpt-4" {{ $config->ai_model === 'openai/gpt-4' ? 'selected' : '' }}>
                        GPT-4
                    </option>
                </select>
            </div>
        </div>
    </div>

    {{-- Content Quality Settings --}}
    <div class="bg-dark-bg-tertiary rounded-apple p-3 auto-soft-card">
        <h2 class="admin-section text-white mb-3 flex items-center gap-2">
            <i class="fas fa-check-circle text-apple-green" style="font-size: 0.75rem;"></i>
            Kualitas Konten
        </h2>
        
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
            <div>
                <label class="admin-label-compact block">Min Words</label>
                <input 
                    type="number" 
                    name="min_word_count" 
                    value="{{ old('min_word_count', $config->min_word_count) }}" 
                    min="300"
                    class="admin-input w-full rounded bg-dark-bg-secondary auto-soft-field text-white focus:border-apple-blue">
            </div>

            <div>
                <label class="admin-label-compact block">Max Words</label>
                <input 
                    type="number" 
                    name="max_word_count" 
                    value="{{ old('max_word_count', $config->max_word_count) }}" 
                    min="500"
                    class="admin-input w-full rounded bg-dark-bg-secondary auto-soft-field text-white focus:border-apple-blue">
            </div>

            <div>
                <label class="admin-label-compact block">Dup Threshold</label>
                <input 
                    type="number" 
                    name="duplicate_threshold" 
                    value="{{ old('duplicate_threshold', $config->duplicate_threshold) }}" 
                    min="0" 
                    max="1" 
                    step="0.01"
                    class="admin-input w-full rounded bg-dark-bg-secondary auto-soft-field text-white focus:border-apple-blue">
            </div>

            <div>
                <label class="admin-label-compact block">Internal Links</label>
                <input 
                    type="number" 
                    name="internal_links_count" 
                    value="{{ old('internal_links_count', $config->internal_links_count) }}" 
                    min="0" 
                    max="10"
                    class="admin-input w-full rounded bg-dark-bg-secondary auto-soft-field text-white focus:border-apple-blue">
            </div>
        </div>
    </div>

    {{-- Language & Market Settings --}}
    <div class="bg-dark-bg-tertiary rounded-apple p-3 auto-soft-card">
        <h2 class="admin-section text-white mb-3 flex items-center gap-2">
            <i class="fas fa-globe text-apple-green" style="font-size: 0.75rem;"></i>
            Language & Market
        </h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            {{-- Language Distribution --}}
            <div>
                <label class="admin-label-compact block mb-2">Distribusi Bahasa (%)</label>
                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="admin-small block mb-0.5" style="color: rgba(235,235,245,0.6);">🇮🇩 Indonesia</label>
                        <input 
                            type="number" 
                            name="language_distribution[id]" 
                            value="{{ old('language_distribution.id', $config->language_distribution['id'] ?? 60) }}" 
                            min="0" max="100"
                            class="admin-input w-full rounded bg-dark-bg-secondary auto-soft-field text-white focus:border-apple-blue">
                    </div>
                    <div>
                        <label class="admin-small block mb-0.5" style="color: rgba(235,235,245,0.6);">🇬🇧 English</label>
                        <input 
                            type="number" 
                            name="language_distribution[en]" 
                            value="{{ old('language_distribution.en', $config->language_distribution['en'] ?? 40) }}" 
                            min="0" max="100"
                            class="admin-input w-full rounded bg-dark-bg-secondary auto-soft-field text-white focus:border-apple-blue">
                    </div>
                </div>
            </div>

            {{-- Market Focus --}}
            <div>
                <label class="admin-label-compact block mb-2">Target Market</label>
                <div class="space-y-1.5">
                    <label class="flex items-center cursor-pointer">
                        <input type="checkbox" name="market_focus[local]" value="1"
                            {{ ($config->market_focus['local'] ?? true) ? 'checked' : '' }}
                            class="rounded auto-soft-check bg-dark-bg-secondary text-apple-blue focus:ring-apple-blue" style="width: 1rem; height: 1rem;">
                        <span class="ml-2 admin-body">Local (UMKM, CV, PT)</span>
                    </label>
                    <label class="flex items-center cursor-pointer">
                        <input type="checkbox" name="market_focus[pma]" value="1"
                            {{ ($config->market_focus['pma'] ?? true) ? 'checked' : '' }}
                            class="rounded auto-soft-check bg-dark-bg-secondary text-apple-blue focus:ring-apple-blue" style="width: 1rem; height: 1rem;">
                        <span class="ml-2 admin-body">PMA / Foreign Investment</span>
                    </label>
                </div>
            </div>
        </div>
    </div>

    {{-- Publishing Options --}}
    <div class="bg-dark-bg-tertiary rounded-apple p-3 auto-soft-card">
        <div class="flex items-center justify-between">
            <h2 class="admin-section text-white flex items-center gap-2">
                <i class="fas fa-rocket text-apple-orange" style="font-size: 0.75rem;"></i>
                Publishing
            </h2>
            <label class="flex items-center cursor-pointer">
                <input type="checkbox" name="auto_publish" value="1"
                    {{ $config->auto_publish ? 'checked' : '' }}
                    class="rounded auto-soft-check bg-dark-bg-secondary text-apple-blue focus:ring-apple-blue" style="width: 1rem; height: 1rem;">
                <span class="ml-2 admin-body">Auto-publish tanpa review</span>
            </label>
        </div>
    </div>

    {{-- Submit Button - Compact --}}
    <div class="flex justify-end">
        <button type="submit" class="admin-btn admin-btn-primary rounded">
            <i class="fas fa-save mr-1.5"></i>Simpan Konfigurasi
        </button>
    </div>
</form>

@push('scripts')
<script>
function addTimeSlot() {
    const container = document.getElementById('postTimes');
    const div = document.createElement('div');
    div.className = 'flex items-center gap-1';
    div.innerHTML = `
        <input type="time" name="post_times[]" 
            class="admin-input flex-1 rounded bg-dark-bg-secondary auto-soft-field text-white focus:border-apple-blue">
        <button type="button" onclick="this.parentElement.remove()" class="px-2 py-1 text-apple-red hover:text-red-400">
            <i class="fas fa-times" style="font-size: 0.625rem;"></i>
        </button>
    `;
    container.appendChild(div);
}

function applyPeakTimePreset() {
    const peakTimes = [
        '06:30', '07:30', '08:30', '09:30',
        '11:00', '11:45', '12:30', '13:15', '14:00',
        '16:30', '17:15', '18:00', '19:00', '20:00', '21:00'
    ];

    const container = document.getElementById('postTimes');
    container.innerHTML = '';

    peakTimes.forEach((time, index) => {
        const div = document.createElement('div');
        div.className = 'flex items-center gap-1';
        div.innerHTML = `
            <input type="time" name="post_times[]" value="${time}"
                class="admin-input flex-1 rounded bg-dark-bg-secondary auto-soft-field text-white focus:border-apple-blue">
            ${index > 0 ? `
                <button type="button" onclick="this.parentElement.remove()" class="px-2 py-1 text-apple-red hover:text-red-400">
                    <i class="fas fa-times" style="font-size: 0.625rem;"></i>
                </button>
            ` : ''}
        `;
        container.appendChild(div);
    });

    const postsPerDayInput = document.querySelector('input[name="posts_per_day"]');
    if (postsPerDayInput) {
        postsPerDayInput.value = '15';
    }
}
</script>
@endpush

@push('styles')
<style>
    .auto-soft-card {
        border: 1px solid rgba(255, 255, 255, 0.06);
    }

    .auto-soft-field {
        border: 1px solid rgba(255, 255, 255, 0.08) !important;
    }

    .auto-soft-field:focus {
        border-color: rgba(10, 132, 255, 0.38) !important;
        box-shadow: 0 0 0 1px rgba(10, 132, 255, 0.16) !important;
    }

    .auto-soft-check {
        border: 1px solid rgba(255, 255, 255, 0.12) !important;
    }

    .auto-soft-divider-top {
        border-top: 1px solid rgba(255, 255, 255, 0.06);
    }

    .auto-soft-divider-bottom {
        border-bottom: 1px solid rgba(255, 255, 255, 0.06);
    }

    .auto-soft-divide-y > :not([hidden]) ~ :not([hidden]) {
        border-top: 1px solid rgba(255, 255, 255, 0.06);
    }
</style>
@endpush
