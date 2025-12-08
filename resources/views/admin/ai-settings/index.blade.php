@extends('layouts.app')

@section('title', 'AI Settings Management')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header pb-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5>AI Settings Management</h5>
                            <p class="text-sm mb-0">Configure AI services, pricing multipliers, and system parameters</p>
                        </div>
                        <div>
                            <a href="{{ route('admin.ai-settings.recent-changes') }}" class="btn btn-sm btn-outline-info me-2">
                                <i class="fas fa-history me-1"></i> Recent Changes
                            </a>
                            <form action="{{ route('admin.ai-settings.clear-cache') }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-outline-warning" 
                                        onclick="return confirm('Clear all AI settings cache?')">
                                    <i class="fas fa-sync-alt me-1"></i> Clear Cache
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                
                <div class="card-body">
                    <!-- Category Tabs -->
                    <ul class="nav nav-pills mb-4" role="tablist">
                        @foreach($categories as $cat)
                        <li class="nav-item">
                            <a class="nav-link {{ $category === $cat ? 'active' : '' }}" 
                               href="{{ route('admin.ai-settings.index', ['category' => $cat]) }}">
                                <i class="fas fa-{{ $cat === 'pricing' ? 'dollar-sign' : ($cat === 'global' ? 'cog' : 'brain') }} me-1"></i>
                                {{ ucfirst($cat) }}
                            </a>
                        </li>
                        @endforeach
                    </ul>

                    @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-2"></i>
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    @endif

                    @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    @endif

                    <!-- Settings Form -->
                    <form action="{{ route('admin.ai-settings.update') }}" method="POST">
                        @csrf
                        
                        @foreach($settings as $groupName => $groupSettings)
                        <div class="mb-4">
                            <h6 class="text-uppercase text-xs font-weight-bolder opacity-7 mb-3">
                                {{ $groupName }}
                            </h6>
                            
                            <div class="row">
                                @foreach($groupSettings as $setting)
                                <div class="col-md-6 mb-3">
                                    <div class="card border">
                                        <div class="card-body p-3">
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <div class="flex-grow-1">
                                                    <label for="setting_{{ $setting->id }}" class="form-label mb-1">
                                                        <strong>{{ ucwords(str_replace(['.', '_'], ' ', last(explode('.', $setting->key)))) }}</strong>
                                                        @if($setting->is_encrypted)
                                                        <span class="badge badge-sm bg-gradient-warning ms-1">
                                                            <i class="fas fa-lock"></i> Encrypted
                                                        </span>
                                                        @endif
                                                    </label>
                                                    <p class="text-xs text-secondary mb-2">{{ $setting->description }}</p>
                                                </div>
                                                
                                                <form action="{{ route('admin.ai-settings.reset', $setting->key) }}" 
                                                      method="POST" class="d-inline ms-2">
                                                    @csrf
                                                    <button type="submit" class="btn btn-link btn-sm p-0 text-danger" 
                                                            title="Reset to default: {{ $setting->default_value }}"
                                                            onclick="return confirm('Reset to default value?')">
                                                        <i class="fas fa-undo"></i>
                                                    </button>
                                                </form>
                                            </div>
                                            
                                            @if($setting->data_type === 'boolean')
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" 
                                                           id="setting_{{ $setting->id }}"
                                                           name="settings[{{ $setting->key }}]" 
                                                           value="1"
                                                           {{ $setting->value ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="setting_{{ $setting->id }}">
                                                        {{ $setting->value ? 'Enabled' : 'Disabled' }}
                                                    </label>
                                                </div>
                                            @elseif($setting->data_type === 'number')
                                                <input type="number" 
                                                       class="form-control form-control-sm" 
                                                       id="setting_{{ $setting->id }}"
                                                       name="settings[{{ $setting->key }}]" 
                                                       value="{{ $setting->value }}"
                                                       step="any"
                                                       @if(isset($setting->validation_rules['min'])) min="{{ $setting->validation_rules['min'] }}" @endif
                                                       @if(isset($setting->validation_rules['max'])) max="{{ $setting->validation_rules['max'] }}" @endif>
                                            @elseif($setting->data_type === 'json' || $setting->data_type === 'array')
                                                <textarea class="form-control form-control-sm font-monospace" 
                                                          id="setting_{{ $setting->id }}"
                                                          name="settings[{{ $setting->key }}]" 
                                                          rows="3">{{ is_array($setting->value) ? json_encode($setting->value, JSON_PRETTY_PRINT) : $setting->value }}</textarea>
                                            @else
                                                <input type="text" 
                                                       class="form-control form-control-sm" 
                                                       id="setting_{{ $setting->id }}"
                                                       name="settings[{{ $setting->key }}]" 
                                                       value="{{ $setting->display_value }}">
                                            @endif
                                            
                                            <div class="d-flex justify-content-between align-items-center mt-2">
                                                <small class="text-muted">
                                                    Default: <code>{{ $setting->default_value }}</code>
                                                </small>
                                                @if($setting->requires_restart)
                                                <span class="badge badge-sm bg-gradient-info">
                                                    <i class="fas fa-power-off"></i> Requires restart
                                                </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endforeach

                        <div class="text-center mt-4">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-save me-2"></i> Save All Settings
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Auto-format JSON on blur
    document.querySelectorAll('textarea.font-monospace').forEach(textarea => {
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
