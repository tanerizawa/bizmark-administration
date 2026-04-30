@extends('layouts.app')

@section('title', 'AI Settings Management')

@section('content')
<div class="space-y-4">
    {{-- Compact Header Section --}}
    <section class="card-elevated rounded-apple-lg admin-hero relative overflow-hidden" role="region" aria-labelledby="ai-settings-header">
        <div class="relative flex flex-col md:flex-row md:items-center md:justify-between gap-3">
            <div class="flex-1 min-w-0">
                <p class="admin-hero-subtitle">Konfigurasi AI</p>
                <h1 id="ai-settings-header" class="admin-hero-title text-white">AI Settings</h1>
                <p class="admin-hero-desc">Configure AI services, pricing multipliers, and system parameters</p>
            </div>
            
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.ai-settings.recent-changes') }}" 
                   class="admin-btn admin-btn-sm rounded bg-apple-blue/25 text-white">
                    <i class="fas fa-history mr-1"></i>Changes
                </a>
                <form action="{{ route('admin.ai-settings.clear-cache') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" 
                            class="admin-btn admin-btn-sm rounded text-apple-orange"
                            class="bg-apple-orange/20"
                            onclick="return confirm('Clear all AI settings cache?')">
                        <i class="fas fa-sync-alt"></i>
                    </button>
                </form>
            </div>
        </div>
    </section>

    {{-- Main Content --}}
    <section class="card-elevated rounded-apple-lg p-3">
        {{-- Category Tabs --}}
        <nav class="flex flex-wrap gap-1 mb-3" role="tablist" aria-label="AI Settings Categories">
            @foreach($categories as $cat)
            <a href="{{ route('admin.ai-settings.index', ['category' => $cat]) }}" 
               class="tab-button {{ $category === $cat ? 'active' : '' }}"
               role="tab"
               aria-selected="{{ $category === $cat ? 'true' : 'false' }}">
                <i class="fas fa-{{ $cat === 'pricing' ? 'dollar-sign' : ($cat === 'global' ? 'cog' : 'brain') }}"></i>
                <span>{{ ucfirst($cat) }}</span>
            </a>
            @endforeach
        </nav>

        {{-- Success Alert --}}
        @if(session('success'))
        <div class="admin-alert admin-alert-success mb-3">
            <i class="fas fa-check-circle"></i>
            <span>{{ session('success') }}</span>
        </div>
        @endif

        {{-- Error Alert --}}
        @if($errors->any())
        <div class="admin-alert admin-alert-error mb-3">
            <i class="fas fa-exclamation-triangle"></i>
            <ul class="space-y-0.5">
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        {{-- Settings Form --}}
        <form action="{{ route('admin.ai-settings.update') }}" method="POST" class="space-y-3">
        {{-- Settings Form --}}
        <form action="{{ route('admin.ai-settings.update') }}" method="POST" class="space-y-3">
            @csrf
            
            @foreach($settings as $groupName => $groupSettings)
            <div class="space-y-2">
                <h6 class="admin-small uppercase tracking-widest text-dark-text-tertiary font-semibold">
                    {{ $groupName }}
                </h6>
                
                <div class="grid grid-cols-3 gap-2">
                    @foreach($groupSettings as $setting)
                    <article class="card-subtle rounded-apple p-2 space-y-2">
                        <div class="flex items-start justify-between gap-2">
                            <div class="flex-1 space-y-0.5">
                                <label for="setting_{{ $setting->id }}" class="admin-body font-medium text-white">
                                    {{ ucwords(str_replace(['.', '_'], ' ', last(explode('.', $setting->key)))) }}
                                    @if($setting->is_encrypted)
                                    <span class="admin-badge ml-1 bg-apple-orange/20 text-apple-orange text-[10px]">
                                        <i class="fas fa-lock"></i>
                                    </span>
                                    @endif
                                </label>
                                <p class="admin-small text-dark-text-tertiary truncate">{{ $setting->description }}</p>
                            </div>
                            
                            <form action="{{ route('admin.ai-settings.reset', $setting->key) }}" 
                                  method="POST" class="inline">
                                @csrf
                                <button type="submit" 
                                        class="text-apple-red hover:text-opacity-80 transition-opacity"
                                        title="Reset to default: {{ $setting->default_value }}"
                                        onclick="return confirm('Reset to default value?')"
                                        aria-label="Reset to default">
                                    <i class="fas fa-undo" style="font-size: 10px;"></i>
                                </button>
                            </form>
                        </div>
                        
                        @if($setting->data_type === 'boolean')
                            <div class="flex items-center gap-2">
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" 
                                           id="setting_{{ $setting->id }}"
                                           name="settings[{{ $setting->key }}]" 
                                           value="1"
                                           class="sr-only peer"
                                           {{ $setting->value ? 'checked' : '' }}>
                                    <div class="w-9 h-5 bg-dark-surface-tertiary rounded-full peer peer-checked:bg-apple-blue transition-colors duration-200
                                                peer-focus:ring-2 peer-focus:ring-apple-blue peer-focus:ring-opacity-50"></div>
                                    <div class="absolute left-0.5 top-0.5 w-4 h-4 bg-white rounded-full transition-transform duration-200
                                                peer-checked:translate-x-4"></div>
                                </label>
                                <span class="admin-small text-dark-text-secondary">
                                    {{ $setting->value ? 'Enabled' : 'Disabled' }}
                                </span>
                            </div>
                        @elseif($setting->data_type === 'number')
                            <input type="number" 
                                   class="w-full px-3 py-2 rounded-apple text-sm bg-dark-surface-secondary text-white border border-dark-border
                                          focus:border-apple-blue focus:ring-2 focus:ring-apple-blue focus:ring-opacity-30 transition-all duration-200" 
                                   id="setting_{{ $setting->id }}"
                                   name="settings[{{ $setting->key }}]" 
                                   value="{{ $setting->value }}"
                                   step="any"
                                   @if(isset($setting->validation_rules['min'])) min="{{ $setting->validation_rules['min'] }}" @endif
                                   @if(isset($setting->validation_rules['max'])) max="{{ $setting->validation_rules['max'] }}" @endif>
                        @elseif($setting->data_type === 'json' || $setting->data_type === 'array')
                            <textarea class="w-full px-3 py-2 rounded-apple text-sm font-mono bg-dark-surface-secondary text-white border border-dark-border
                                             focus:border-apple-blue focus:ring-2 focus:ring-apple-blue focus:ring-opacity-30 transition-all duration-200" 
                                      id="setting_{{ $setting->id }}"
                                      name="settings[{{ $setting->key }}]" 
                                      rows="2">{{ is_array($setting->value) ? json_encode($setting->value, JSON_PRETTY_PRINT) : $setting->value }}</textarea>
                        @else
                            <input type="text" 
                                   class="admin-input w-full" 
                                   id="setting_{{ $setting->id }}"
                                   name="settings[{{ $setting->key }}]" 
                                   value="{{ $setting->display_value }}">
                        @endif
                        
                        <div class="flex items-center justify-between admin-small">
                            <span class="text-dark-text-tertiary">
                                Default: <code class="px-1 py-0.5 rounded bg-dark-surface-tertiary text-dark-text-secondary">{{ $setting->default_value }}</code>
                            </span>
                            @if($setting->requires_restart)
                            <span class="admin-badge bg-apple-blue/20 text-apple-blue">
                                <i class="fas fa-power-off"></i> Restart
                            </span>
                            @endif
                        </div>
                    </article>
                    @endforeach
                </div>
            </div>
            @endforeach

            <div class="flex justify-center pt-3">
                <button type="submit" class="admin-btn rounded-apple-lg bg-apple-blue text-white">
                    <i class="fas fa-save mr-1"></i>Save All Settings
                </button>
            </div>
        </form>
    </section>
</div>
@endsection

@push('scripts')
<script>
    // Auto-format JSON on blur
    document.querySelectorAll('textarea.font-mono').forEach(textarea => {
        textarea.addEventListener('blur', function() {
            try {
                const json = JSON.parse(this.value);
                this.value = JSON.stringify(json, null, 2);
            } catch (e) {
                // Invalid JSON, keep as is
            }
        });
    });
</script>
@endpush
