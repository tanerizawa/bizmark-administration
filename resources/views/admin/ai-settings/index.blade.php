@extends('layouts.app')

@section('title', 'AI Settings Management')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    {{-- Header Section --}}
    <section class="card-elevated rounded-apple-xl p-5 md:p-6" role="region" aria-labelledby="ai-settings-header">
        <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4">
            <div class="space-y-2">
                <p class="text-xs uppercase tracking-[0.4em] text-dark-text-tertiary">Konfigurasi AI</p>
                <h1 id="ai-settings-header" class="text-2xl md:text-3xl font-bold text-white">
                    AI Settings Management
                </h1>
                <p class="text-sm text-dark-text-secondary">
                    Configure AI services, pricing multipliers, and system parameters
                </p>
            </div>
            
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('admin.ai-settings.recent-changes') }}" 
                   class="inline-flex items-center gap-2 px-4 py-2 rounded-apple text-xs font-semibold bg-apple-blue bg-opacity-20 text-apple-blue hover:bg-opacity-30 transition-all duration-200">
                    <i class="fas fa-history" aria-hidden="true"></i>
                    <span>Recent Changes</span>
                </a>
                <form action="{{ route('admin.ai-settings.clear-cache') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" 
                            class="inline-flex items-center gap-2 px-4 py-2 rounded-apple text-xs font-semibold bg-apple-orange bg-opacity-20 text-apple-orange hover:bg-opacity-30 transition-all duration-200"
                            onclick="return confirm('Clear all AI settings cache?')">
                        <i class="fas fa-sync-alt" aria-hidden="true"></i>
                        <span>Clear Cache</span>
                    </button>
                </form>
            </div>
        </div>
    </section>

    {{-- Main Content --}}
    <section class="card-elevated rounded-apple-xl p-5 md:p-6">
        {{-- Category Tabs --}}
        <nav class="flex flex-wrap gap-2 mb-6" role="tablist" aria-label="AI Settings Categories">
            @foreach($categories as $cat)
            <a href="{{ route('admin.ai-settings.index', ['category' => $cat]) }}" 
               class="inline-flex items-center gap-2 px-4 py-2.5 rounded-apple text-sm font-medium transition-all duration-200
                      {{ $category === $cat 
                         ? 'bg-apple-blue bg-opacity-25 text-apple-blue' 
                         : 'bg-dark-surface-secondary text-dark-text-secondary hover:bg-opacity-80 hover:text-dark-text-primary' }}"
               role="tab"
               aria-selected="{{ $category === $cat ? 'true' : 'false' }}">
                <i class="fas fa-{{ $cat === 'pricing' ? 'dollar-sign' : ($cat === 'global' ? 'cog' : 'brain') }}" aria-hidden="true"></i>
                <span>{{ ucfirst($cat) }}</span>
            </a>
            @endforeach
        </nav>

        {{-- Success Alert --}}
        @if(session('success'))
        <div class="mb-4 p-4 rounded-apple-lg bg-apple-green bg-opacity-15 border border-apple-green border-opacity-30" role="alert">
            <div class="flex items-start gap-3">
                <i class="fas fa-check-circle text-apple-green mt-0.5" aria-hidden="true"></i>
                <div class="flex-1">
                    <p class="text-sm text-apple-green font-medium">{{ session('success') }}</p>
                </div>
                <button type="button" class="text-apple-green hover:text-opacity-70" data-bs-dismiss="alert" aria-label="Close">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
        @endif

        {{-- Error Alert --}}
        @if($errors->any())
        <div class="mb-4 p-4 rounded-apple-lg bg-apple-red bg-opacity-15 border border-apple-red border-opacity-30" role="alert">
            <div class="flex items-start gap-3">
                <i class="fas fa-exclamation-triangle text-apple-red mt-0.5" aria-hidden="true"></i>
                <div class="flex-1">
                    <ul class="text-sm text-apple-red space-y-1">
                        @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                <button type="button" class="text-apple-red hover:text-opacity-70" data-bs-dismiss="alert" aria-label="Close">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
        @endif

        {{-- Settings Form --}}
        <form action="{{ route('admin.ai-settings.update') }}" method="POST" class="space-y-6">
        {{-- Settings Form --}}
        <form action="{{ route('admin.ai-settings.update') }}" method="POST" class="space-y-6">
            @csrf
            
            @foreach($settings as $groupName => $groupSettings)
            <div class="space-y-4">
                <h6 class="text-xs uppercase tracking-widest text-dark-text-tertiary font-semibold">
                    {{ $groupName }}
                </h6>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($groupSettings as $setting)
                    <article class="card-subtle rounded-apple-lg p-4 space-y-3">
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex-1 space-y-1">
                                <label for="setting_{{ $setting->id }}" class="block text-sm font-semibold text-white">
                                    {{ ucwords(str_replace(['.', '_'], ' ', last(explode('.', $setting->key)))) }}
                                    @if($setting->is_encrypted)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-xs font-medium bg-apple-orange bg-opacity-20 text-apple-orange ml-2">
                                        <i class="fas fa-lock text-xs" aria-hidden="true"></i>
                                        Encrypted
                                    </span>
                                    @endif
                                </label>
                                <p class="text-xs text-dark-text-tertiary">{{ $setting->description }}</p>
                            </div>
                            
                            <form action="{{ route('admin.ai-settings.reset', $setting->key) }}" 
                                  method="POST" class="inline">
                                @csrf
                                <button type="submit" 
                                        class="text-apple-red hover:text-opacity-80 transition-opacity"
                                        title="Reset to default: {{ $setting->default_value }}"
                                        onclick="return confirm('Reset to default value?')"
                                        aria-label="Reset to default">
                                    <i class="fas fa-undo" aria-hidden="true"></i>
                                </button>
                            </form>
                        </div>
                        
                        @if($setting->data_type === 'boolean')
                            <div class="flex items-center gap-3">
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" 
                                           id="setting_{{ $setting->id }}"
                                           name="settings[{{ $setting->key }}]" 
                                           value="1"
                                           class="sr-only peer"
                                           {{ $setting->value ? 'checked' : '' }}>
                                    <div class="w-11 h-6 bg-dark-surface-tertiary rounded-full peer peer-checked:bg-apple-blue transition-colors duration-200
                                                peer-focus:ring-2 peer-focus:ring-apple-blue peer-focus:ring-opacity-50"></div>
                                    <div class="absolute left-1 top-1 w-4 h-4 bg-white rounded-full transition-transform duration-200
                                                peer-checked:translate-x-5"></div>
                                </label>
                                <span class="text-sm text-dark-text-secondary">
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
                                      rows="3">{{ is_array($setting->value) ? json_encode($setting->value, JSON_PRETTY_PRINT) : $setting->value }}</textarea>
                        @else
                            <input type="text" 
                                   class="w-full px-3 py-2 rounded-apple text-sm bg-dark-surface-secondary text-white border border-dark-border
                                          focus:border-apple-blue focus:ring-2 focus:ring-apple-blue focus:ring-opacity-30 transition-all duration-200" 
                                   id="setting_{{ $setting->id }}"
                                   name="settings[{{ $setting->key }}]" 
                                   value="{{ $setting->display_value }}">
                        @endif
                        
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-dark-text-tertiary">
                                Default: <code class="px-1.5 py-0.5 rounded bg-dark-surface-tertiary text-dark-text-secondary">{{ $setting->default_value }}</code>
                            </span>
                            @if($setting->requires_restart)
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-xs font-medium bg-apple-blue bg-opacity-20 text-apple-blue">
                                <i class="fas fa-power-off text-xs" aria-hidden="true"></i>
                                Requires restart
                            </span>
                            @endif
                        </div>
                    </article>
                    @endforeach
                </div>
            </div>
            @endforeach

            <div class="flex justify-center pt-6">
                <button type="submit" 
                        class="inline-flex items-center gap-3 px-8 py-3.5 rounded-apple-lg text-sm font-semibold
                               bg-apple-blue text-white hover:bg-opacity-90 transition-all duration-200
                               focus:ring-4 focus:ring-apple-blue focus:ring-opacity-30">
                    <i class="fas fa-save" aria-hidden="true"></i>
                    <span>Save All Settings</span>
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
