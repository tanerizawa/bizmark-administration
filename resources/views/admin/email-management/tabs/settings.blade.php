<div class="space-y-4">
    {{-- Header Section --}}
    <div class="email-panel-header mb-1">
        <div>
            <h2 class="text-base font-semibold text-white">Email Settings</h2>
            <p class="text-sm" style="color: rgba(235,235,245,0.6);">
                Konfigurasi pengaturan email sistem Anda
            </p>
        </div>
        <div class="text-xs max-w-md" style="color: rgba(235,235,245,0.55);">
            Susun SMTP, pengiriman, tracking, dan pengujian email dalam panel yang lebih rapat dan mudah dipindai.
        </div>
    </div>

    {{-- Settings Form --}}
    <form action="{{ route('admin.email.settings.update') ?? '#' }}" method="POST" class="space-y-4" id="emailSettingsForm">
        @csrf
        @method('PUT')
        <input type="hidden" name="redirect_to" value="email-management">

        <div class="grid grid-cols-1 xl:grid-cols-2 gap-4">
        {{-- SMTP Configuration --}}
        <div class="card-elevated rounded-apple-lg p-4 email-table-shell">
            <h3 class="text-sm font-semibold text-white mb-3 flex items-center">
                <i class="fas fa-server mr-2" style="color: rgba(10,132,255,1);"></i>
                SMTP Configuration
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium mb-2" style="color: rgba(235,235,245,0.7);">
                        Mail Driver
                    </label>
                    <select name="mail_mailer" class="input-apple w-full" id="mail-mailer-select">
                        <option value="smtp" {{ ($settings['mail_mailer'] ?? 'smtp') === 'smtp' ? 'selected' : '' }}>SMTP</option>
                        <option value="mailgun" {{ ($settings['mail_mailer'] ?? 'smtp') === 'mailgun' ? 'selected' : '' }}>Mailgun</option>
                        <option value="log" {{ ($settings['mail_mailer'] ?? 'smtp') === 'log' ? 'selected' : '' }}>Log Only</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-2" style="color: rgba(235,235,245,0.7);">
                        SMTP Host
                    </label>
                    <input type="text" name="mail_host" value="{{ $settings['mail_host'] ?? '' }}"
                           class="input-apple w-full" placeholder="smtp.example.com">
                </div>

                <div>
                    <label class="block text-sm font-medium mb-2" style="color: rgba(235,235,245,0.7);">
                        SMTP Port
                    </label>
                    <input type="number" name="mail_port" value="{{ $settings['mail_port'] ?? '587' }}"
                           class="input-apple w-full" placeholder="587">
                </div>

                <div>
                    <label class="block text-sm font-medium mb-2" style="color: rgba(235,235,245,0.7);">
                        SMTP Username
                    </label>
                    <input type="text" name="mail_username" value="{{ $settings['mail_username'] ?? '' }}"
                           class="input-apple w-full">
                </div>

                <div>
                    <label class="block text-sm font-medium mb-2" style="color: rgba(235,235,245,0.7);">
                        SMTP Password
                    </label>
                    <input type="password" name="mail_password" value="{{ $settings['mail_password'] ?? '' }}"
                           class="input-apple w-full">
                </div>

                <div>
                    <label class="block text-sm font-medium mb-2" style="color: rgba(235,235,245,0.7);">
                        SMTP Encryption
                    </label>
                    <select name="mail_encryption" class="input-apple w-full">
                        <option value="tls" {{ ($settings['mail_encryption'] ?? 'tls') == 'tls' ? 'selected' : '' }}>TLS</option>
                        <option value="ssl" {{ ($settings['mail_encryption'] ?? 'tls') == 'ssl' ? 'selected' : '' }}>SSL</option>
                        <option value="null" {{ ($settings['mail_encryption'] ?? 'tls') == 'null' ? 'selected' : '' }}>None</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-2" style="color: rgba(235,235,245,0.7);">
                        From Email
                    </label>
                    <input type="email" name="mail_from_address" value="{{ $settings['mail_from_address'] ?? '' }}"
                           class="input-apple w-full" placeholder="noreply@example.com">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium mb-2" style="color: rgba(235,235,245,0.7);">
                        From Name
                    </label>
                    <input type="text" name="mail_from_name" value="{{ $settings['mail_from_name'] ?? '' }}"
                           class="input-apple w-full" placeholder="Your Company Name">
                </div>

                <div class="md:col-span-2 grid grid-cols-1 md:grid-cols-3 gap-3" id="mailgun-fields" style="display: none;">
                    <div>
                        <label class="block text-sm font-medium mb-2" style="color: rgba(235,235,245,0.7);">Mailgun Domain</label>
                        <input type="text" name="mailgun_domain" value="{{ $settings['mailgun_domain'] ?? '' }}" class="input-apple w-full">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-2" style="color: rgba(235,235,245,0.7);">Mailgun Secret</label>
                        <input type="password" name="mailgun_secret" value="{{ $settings['mailgun_secret'] ?? '' }}" class="input-apple w-full">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-2" style="color: rgba(235,235,245,0.7);">Mailgun Endpoint</label>
                        <input type="text" name="mailgun_endpoint" value="{{ $settings['mailgun_endpoint'] ?? '' }}" class="input-apple w-full">
                    </div>
                </div>
            </div>
        </div>

        {{-- Email Sending --}}
        <div class="card-elevated rounded-apple-lg p-4 email-table-shell">
            <h3 class="text-sm font-semibold text-white mb-3 flex items-center">
                <i class="fas fa-paper-plane mr-2" style="color: rgba(52,199,89,1);"></i>
                Email Sending
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
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
        <div class="card-elevated rounded-apple-lg p-4 email-table-shell">
            <h3 class="text-sm font-semibold text-white mb-3 flex items-center">
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
        <div class="card-elevated rounded-apple-lg p-4 email-table-shell">
            <h3 class="text-sm font-semibold text-white mb-3 flex items-center">
                <i class="fas fa-user-slash mr-2" style="color: rgba(255,159,10,1);"></i>
                Unsubscribe Settings
            </h3>
            
            <div class="space-y-3">
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
        <div class="card-elevated rounded-apple-lg p-4 email-table-shell">
            <h3 class="text-sm font-semibold text-white mb-3 flex items-center">
                <i class="fas fa-vial mr-2" style="color: rgba(90,200,250,1);"></i>
                Test Email
            </h3>
            
            <div class="flex gap-3 flex-col md:flex-row">
                <input type="email" placeholder="Enter email to send test"
                       class="input-apple flex-1" id="test-email" value="{{ $settings['mail_from_address'] ?? '' }}">
                <button type="button" class="btn-apple-sm px-4 py-2" onclick="sendEmailSettingsTest()">
                    <i class="fas fa-paper-plane mr-2"></i>Send Test
                </button>
            </div>
            <p class="text-xs mt-2" style="color: rgba(235,235,245,0.45);">
                Status berhasil hanya berarti email diterima SMTP/provider. Provider seperti Brevo tetap dapat memblokir delivery jika recipient berstatus unsubscribed atau masuk suppression list.
            </p>
            <p id="email-settings-test-result" class="text-xs mt-2" style="color: rgba(235,235,245,0.6);"></p>
        </div>
        </div>

        {{-- Submit Button --}}
        <div class="flex justify-end gap-3">
            <a href="{{ route('admin.email-management.index', ['tab' => 'settings']) }}" class="btn-apple-sm px-6 py-2.5 inline-flex items-center">
                Cancel
            </a>
            <button type="submit" class="btn-apple-primary-sm px-6 py-2.5">
                <i class="fas fa-save mr-2"></i>Save Settings
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
function toggleMailerFields() {
    const mailer = document.getElementById('mail-mailer-select');
    const mailgunFields = document.getElementById('mailgun-fields');

    if (!mailer || !mailgunFields) {
        return;
    }

    mailgunFields.style.display = mailer.value === 'mailgun' ? 'grid' : 'none';
}

async function sendEmailSettingsTest() {
    const emailInput = document.getElementById('test-email');
    const result = document.getElementById('email-settings-test-result');

    if (!emailInput || !result || !emailInput.value) {
        return;
    }

    result.textContent = 'Mengirim test email...';

    try {
        const response = await fetch('{{ route('admin.email.settings.test') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ test_email: emailInput.value })
        });

        const data = await response.json();
        result.textContent = data.message || 'Selesai.';
        result.style.color = response.ok ? 'rgba(52,199,89,1)' : 'rgba(255,69,58,1)';
    } catch (error) {
        result.textContent = 'Gagal mengirim test email.';
        result.style.color = 'rgba(255,69,58,1)';
    }
}

document.addEventListener('DOMContentLoaded', toggleMailerFields);
document.getElementById('mail-mailer-select')?.addEventListener('change', toggleMailerFields);
</script>
@endpush
