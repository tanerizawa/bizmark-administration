<div class="space-y-4">
    {{-- Header Section --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
        <div>
            <h2 class="text-xl font-semibold text-white">Email Settings</h2>
            <p class="text-sm" style="color: rgba(235,235,245,0.6);">
                Konfigurasi pengaturan email sistem Anda
            </p>
        </div>
    </div>

    {{-- Settings Form --}}
    <form action="{{ route('admin.email.settings.update') ?? '#' }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        {{-- SMTP Configuration --}}
        <div class="card-elevated rounded-apple-lg p-6">
            <h3 class="text-lg font-semibold text-white mb-4 flex items-center">
                <i class="fas fa-server mr-2" style="color: rgba(10,132,255,1);"></i>
                SMTP Configuration
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-2" style="color: rgba(235,235,245,0.7);">
                        SMTP Host
                    </label>
                    <input type="text" name="smtp_host" value="{{ $settings['smtp_host'] ?? '' }}"
                           class="input-apple w-full" placeholder="smtp.example.com">
                </div>

                <div>
                    <label class="block text-sm font-medium mb-2" style="color: rgba(235,235,245,0.7);">
                        SMTP Port
                    </label>
                    <input type="number" name="smtp_port" value="{{ $settings['smtp_port'] ?? '587' }}"
                           class="input-apple w-full" placeholder="587">
                </div>

                <div>
                    <label class="block text-sm font-medium mb-2" style="color: rgba(235,235,245,0.7);">
                        SMTP Username
                    </label>
                    <input type="text" name="smtp_username" value="{{ $settings['smtp_username'] ?? '' }}"
                           class="input-apple w-full">
                </div>

                <div>
                    <label class="block text-sm font-medium mb-2" style="color: rgba(235,235,245,0.7);">
                        SMTP Password
                    </label>
                    <input type="password" name="smtp_password" value="{{ $settings['smtp_password'] ?? '' }}"
                           class="input-apple w-full">
                </div>

                <div>
                    <label class="block text-sm font-medium mb-2" style="color: rgba(235,235,245,0.7);">
                        SMTP Encryption
                    </label>
                    <select name="smtp_encryption" class="input-apple w-full">
                        <option value="tls" {{ ($settings['smtp_encryption'] ?? 'tls') == 'tls' ? 'selected' : '' }}>TLS</option>
                        <option value="ssl" {{ ($settings['smtp_encryption'] ?? 'tls') == 'ssl' ? 'selected' : '' }}>SSL</option>
                        <option value="none" {{ ($settings['smtp_encryption'] ?? 'tls') == 'none' ? 'selected' : '' }}>None</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-2" style="color: rgba(235,235,245,0.7);">
                        From Email
                    </label>
                    <input type="email" name="from_email" value="{{ $settings['from_email'] ?? '' }}"
                           class="input-apple w-full" placeholder="noreply@example.com">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium mb-2" style="color: rgba(235,235,245,0.7);">
                        From Name
                    </label>
                    <input type="text" name="from_name" value="{{ $settings['from_name'] ?? '' }}"
                           class="input-apple w-full" placeholder="Your Company Name">
                </div>
            </div>
        </div>

        {{-- Email Sending --}}
        <div class="card-elevated rounded-apple-lg p-6">
            <h3 class="text-lg font-semibold text-white mb-4 flex items-center">
                <i class="fas fa-paper-plane mr-2" style="color: rgba(52,199,89,1);"></i>
                Email Sending
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-2" style="color: rgba(235,235,245,0.7);">
                        Rate Limit (emails per hour)
                    </label>
                    <input type="number" name="rate_limit" value="{{ $settings['rate_limit'] ?? '100' }}"
                           class="input-apple w-full" placeholder="100">
                </div>

                <div>
                    <label class="block text-sm font-medium mb-2" style="color: rgba(235,235,245,0.7);">
                        Batch Size
                    </label>
                    <input type="number" name="batch_size" value="{{ $settings['batch_size'] ?? '50' }}"
                           class="input-apple w-full" placeholder="50">
                </div>

                <div class="md:col-span-2">
                    <label class="flex items-center">
                        <input type="checkbox" name="queue_emails" value="1"
                               {{ ($settings['queue_emails'] ?? false) ? 'checked' : '' }}
                               class="rounded mr-2">
                        <span class="text-sm" style="color: rgba(235,235,245,0.7);">
                            Queue emails for background processing
                        </span>
                    </label>
                </div>
            </div>
        </div>

        {{-- Tracking & Analytics --}}
        <div class="card-elevated rounded-apple-lg p-6">
            <h3 class="text-lg font-semibold text-white mb-4 flex items-center">
                <i class="fas fa-chart-line mr-2" style="color: rgba(175,82,222,1);"></i>
                Tracking & Analytics
            </h3>
            
            <div class="space-y-3">
                <label class="flex items-center">
                    <input type="checkbox" name="track_opens" value="1"
                           {{ ($settings['track_opens'] ?? true) ? 'checked' : '' }}
                           class="rounded mr-2">
                    <span class="text-sm" style="color: rgba(235,235,245,0.7);">
                        Track email opens
                    </span>
                </label>

                <label class="flex items-center">
                    <input type="checkbox" name="track_clicks" value="1"
                           {{ ($settings['track_clicks'] ?? true) ? 'checked' : '' }}
                           class="rounded mr-2">
                    <span class="text-sm" style="color: rgba(235,235,245,0.7);">
                        Track link clicks
                    </span>
                </label>

                <label class="flex items-center">
                    <input type="checkbox" name="track_unsubscribes" value="1"
                           {{ ($settings['track_unsubscribes'] ?? true) ? 'checked' : '' }}
                           class="rounded mr-2">
                    <span class="text-sm" style="color: rgba(235,235,245,0.7);">
                        Track unsubscribes
                    </span>
                </label>
            </div>
        </div>

        {{-- Unsubscribe Settings --}}
        <div class="card-elevated rounded-apple-lg p-6">
            <h3 class="text-lg font-semibold text-white mb-4 flex items-center">
                <i class="fas fa-user-slash mr-2" style="color: rgba(255,159,10,1);"></i>
                Unsubscribe Settings
            </h3>
            
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium mb-2" style="color: rgba(235,235,245,0.7);">
                        Unsubscribe Page URL
                    </label>
                    <input type="url" name="unsubscribe_url" value="{{ $settings['unsubscribe_url'] ?? '' }}"
                           class="input-apple w-full" placeholder="https://example.com/unsubscribe">
                </div>

                <div>
                    <label class="flex items-center">
                        <input type="checkbox" name="add_unsubscribe_link" value="1"
                               {{ ($settings['add_unsubscribe_link'] ?? true) ? 'checked' : '' }}
                               class="rounded mr-2">
                        <span class="text-sm" style="color: rgba(235,235,245,0.7);">
                            Automatically add unsubscribe link to all campaigns
                        </span>
                    </label>
                </div>
            </div>
        </div>

        {{-- Test Email --}}
        <div class="card-elevated rounded-apple-lg p-6">
            <h3 class="text-lg font-semibold text-white mb-4 flex items-center">
                <i class="fas fa-vial mr-2" style="color: rgba(90,200,250,1);"></i>
                Test Email
            </h3>
            
            <div class="flex gap-3">
                <input type="email" placeholder="Enter email to send test"
                       class="input-apple flex-1" id="test-email">
                <button type="button" class="btn-apple-sm px-4 py-2">
                    <i class="fas fa-paper-plane mr-2"></i>Send Test
                </button>
            </div>
        </div>

        {{-- Submit Button --}}
        <div class="flex justify-end gap-3">
            <button type="button" class="btn-apple-sm px-6 py-2.5">
                Cancel
            </button>
            <button type="submit" class="btn-apple-primary-sm px-6 py-2.5">
                <i class="fas fa-save mr-2"></i>Save Settings
            </button>
        </div>
    </form>
</div>
