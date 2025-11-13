{{-- Security Settings Tab --}}
<div>
    <h3 class="text-lg font-semibold mb-4" style="color: #FFFFFF;">Security Settings</h3>

    <form method="POST" action="{{ route('settings.security.update') }}" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="p-4 rounded-apple" style="background: rgba(255, 255, 255, 0.02);">
            <h4 class="text-base font-medium mb-3" style="color: #FFFFFF;">
                <i class="fas fa-lock mr-2" style="color: rgba(0, 122, 255, 0.6);"></i>
                Password Policies
            </h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="min_password_length" class="block text-sm font-medium mb-2" style="color: rgba(235, 235, 245, 0.9);">
                        Minimum Password Length
                    </label>
                    <input type="number" name="min_password_length" id="min_password_length" class="input-apple w-full"
                           value="{{ old('min_password_length', $securitySetting->min_password_length) }}" min="6" max="32" required>
                    @error('min_password_length')
                        <p class="text-xs mt-1" style="color: rgba(255, 69, 58, 0.9);">{{ $message }}</p>
                    @enderror
                </div>
                <div class="space-y-2">
                    <label class="flex items-center space-x-2">
                        <input type="checkbox" name="require_mixed_case" value="1" {{ old('require_mixed_case', $securitySetting->require_mixed_case) ? 'checked' : '' }}>
                        <span class="text-xs" style="color: rgba(235, 235, 245, 0.7);">Require mixed upper & lower case</span>
                    </label>
                    <label class="flex items-center space-x-2">
                        <input type="checkbox" name="require_number" value="1" {{ old('require_number', $securitySetting->require_number) ? 'checked' : '' }}>
                        <span class="text-xs" style="color: rgba(235, 235, 245, 0.7);">Require numbers</span>
                    </label>
                    <label class="flex items-center space-x-2">
                        <input type="checkbox" name="require_special_char" value="1" {{ old('require_special_char', $securitySetting->require_special_char) ? 'checked' : '' }}>
                        <span class="text-xs" style="color: rgba(235, 235, 245, 0.7);">Require special characters</span>
                    </label>
                </div>
            </div>
        </div>

        <div class="p-4 rounded-apple" style="background: rgba(255, 255, 255, 0.02);">
            <h4 class="text-base font-medium mb-3" style="color: #FFFFFF;">
                <i class="fas fa-history mr-2" style="color: rgba(0, 122, 255, 0.6);"></i>
                Password Rotation & Session
            </h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="password_expiration_days" class="block text-sm font-medium mb-2" style="color: rgba(235, 235, 245, 0.9);">
                        Password Expiration (days)
                    </label>
                    <input type="number" name="password_expiration_days" id="password_expiration_days" class="input-apple w-full"
                           value="{{ old('password_expiration_days', $securitySetting->password_expiration_days) }}" min="7" max="365" required>
                </div>
                <div>
                    <label for="session_timeout_minutes" class="block text-sm font-medium mb-2" style="color: rgba(235, 235, 245, 0.9);">
                        Session Timeout (minutes)
                    </label>
                    <input type="number" name="session_timeout_minutes" id="session_timeout_minutes" class="input-apple w-full"
                           value="{{ old('session_timeout_minutes', $securitySetting->session_timeout_minutes) }}" min="5" max="240" required>
                </div>
            </div>
            <div class="mt-3 grid grid-cols-1 md:grid-cols-2 gap-3">
                <label class="flex items-center space-x-2">
                    <input type="checkbox" name="enforce_password_expiration" value="1" {{ old('enforce_password_expiration', $securitySetting->enforce_password_expiration) ? 'checked' : '' }}>
                    <span class="text-xs" style="color: rgba(235, 235, 245, 0.7);">Enforce password expiration</span>
                </label>
                <label class="flex items-center space-x-2">
                    <input type="checkbox" name="allow_concurrent_sessions" value="1" {{ old('allow_concurrent_sessions', $securitySetting->allow_concurrent_sessions) ? 'checked' : '' }}>
                    <span class="text-xs" style="color: rgba(235, 235, 245, 0.7);">Allow concurrent sessions</span>
                </label>
            </div>
        </div>

        <div class="p-4 rounded-apple" style="background: rgba(255, 255, 255, 0.02);">
            <h4 class="text-base font-medium mb-3" style="color: #FFFFFF;">
                <i class="fas fa-shield-alt mr-2" style="color: rgba(0, 122, 255, 0.6);"></i>
                Advanced Controls
            </h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <label class="flex items-center space-x-2">
                    <input type="checkbox" name="two_factor_enabled" value="1" {{ old('two_factor_enabled', $securitySetting->two_factor_enabled) ? 'checked' : '' }}>
                    <span class="text-xs" style="color: rgba(235, 235, 245, 0.7);">Enable two-factor authentication</span>
                </label>
                <label class="flex items-center space-x-2">
                    <input type="checkbox" name="activity_log_enabled" value="1" {{ old('activity_log_enabled', $securitySetting->activity_log_enabled) ? 'checked' : '' }}>
                    <span class="text-xs" style="color: rgba(235, 235, 245, 0.7);">Track activity log</span>
                </label>
            </div>
        </div>

        <div class="flex justify-end">
            <button type="submit" class="px-4 py-2 rounded-apple text-sm font-medium transition-all duration-300"
                    style="background: rgba(0, 122, 255, 1); color: #FFFFFF;">
                <i class="fas fa-save mr-2"></i>Simpan Pengaturan Keamanan
            </button>
        </div>
    </form>
</div>
