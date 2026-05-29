<div style="display:flex;flex-direction:column;gap:16px">

    {{-- Header --}}
    <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px">
        <div>
            <p style="font-size:0.6rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--dark-text-secondary);margin:0">Konfigurasi Sistem</p>
            <h3 style="font-size:0.95rem;font-weight:700;color:var(--dark-text-primary);margin:3px 0 2px">Email Settings</h3>
            <p style="font-size:0.78rem;color:var(--dark-text-secondary);margin:0">Susun SMTP, pengiriman, tracking, dan pengujian email</p>
        </div>
    </div>

    {{-- Settings Form --}}
    <form action="{{ route('admin.email.settings.update') ?? '#' }}" method="POST" id="emailSettingsForm">
        @csrf
        @method('PUT')
        <input type="hidden" name="redirect_to" value="email-management">

        <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:16px">

            {{-- SMTP Configuration --}}
            <div style="background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:14px;padding:20px 22px">
                <h3 style="font-size:0.85rem;font-weight:700;color:var(--dark-text-primary);margin:0 0 14px;display:flex;align-items:center;gap:8px">
                    <i class="fas fa-server" style="color:var(--apple-blue);font-size:0.78rem"></i>SMTP Configuration
                </h3>
                <div style="display:flex;flex-direction:column;gap:12px">
                    <div>
                        <label style="font-size:0.68rem;font-weight:600;color:var(--dark-text-secondary);display:block;margin-bottom:4px">Mail Driver</label>
                        <select name="mail_mailer" id="mail-mailer-select"
                                style="width:100%;padding:8px 12px;background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:8px;color:var(--dark-text-primary);font-size:0.85rem;outline:none"
                                onfocus="this.style.borderColor='var(--apple-blue)'" onblur="this.style.borderColor='var(--dark-separator)'"
                                onchange="toggleMailerFields()">
                            <option value="smtp" {{ ($settings['mail_mailer'] ?? 'smtp') === 'smtp' ? 'selected' : '' }}>SMTP</option>
                            <option value="mailgun" {{ ($settings['mail_mailer'] ?? 'smtp') === 'mailgun' ? 'selected' : '' }}>Mailgun</option>
                            <option value="log" {{ ($settings['mail_mailer'] ?? 'smtp') === 'log' ? 'selected' : '' }}>Log Only</option>
                        </select>
                    </div>
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
                        <div>
                            <label style="font-size:0.68rem;font-weight:600;color:var(--dark-text-secondary);display:block;margin-bottom:4px">SMTP Host</label>
                            <input type="text" name="mail_host" value="{{ $settings['mail_host'] ?? '' }}" placeholder="smtp.example.com"
                                   style="width:100%;padding:8px 12px;background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:8px;color:var(--dark-text-primary);font-size:0.85rem;outline:none;box-sizing:border-box"
                                   onfocus="this.style.borderColor='var(--apple-blue)'" onblur="this.style.borderColor='var(--dark-separator)'">
                        </div>
                        <div>
                            <label style="font-size:0.68rem;font-weight:600;color:var(--dark-text-secondary);display:block;margin-bottom:4px">SMTP Port</label>
                            <input type="number" name="mail_port" value="{{ $settings['mail_port'] ?? '587' }}" placeholder="587"
                                   style="width:100%;padding:8px 12px;background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:8px;color:var(--dark-text-primary);font-size:0.85rem;outline:none;box-sizing:border-box"
                                   onfocus="this.style.borderColor='var(--apple-blue)'" onblur="this.style.borderColor='var(--dark-separator)'">
                        </div>
                        <div>
                            <label style="font-size:0.68rem;font-weight:600;color:var(--dark-text-secondary);display:block;margin-bottom:4px">SMTP Username</label>
                            <input type="text" name="mail_username" value="{{ $settings['mail_username'] ?? '' }}"
                                   style="width:100%;padding:8px 12px;background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:8px;color:var(--dark-text-primary);font-size:0.85rem;outline:none;box-sizing:border-box"
                                   onfocus="this.style.borderColor='var(--apple-blue)'" onblur="this.style.borderColor='var(--dark-separator)'">
                        </div>
                        <div>
                            <label style="font-size:0.68rem;font-weight:600;color:var(--dark-text-secondary);display:block;margin-bottom:4px">SMTP Password</label>
                            <input type="password" name="mail_password" value="{{ $settings['mail_password'] ?? '' }}"
                                   style="width:100%;padding:8px 12px;background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:8px;color:var(--dark-text-primary);font-size:0.85rem;outline:none;box-sizing:border-box"
                                   onfocus="this.style.borderColor='var(--apple-blue)'" onblur="this.style.borderColor='var(--dark-separator)'">
                        </div>
                        <div>
                            <label style="font-size:0.68rem;font-weight:600;color:var(--dark-text-secondary);display:block;margin-bottom:4px">Encryption</label>
                            <select name="mail_encryption"
                                    style="width:100%;padding:8px 12px;background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:8px;color:var(--dark-text-primary);font-size:0.85rem;outline:none"
                                    onfocus="this.style.borderColor='var(--apple-blue)'" onblur="this.style.borderColor='var(--dark-separator)'">
                                <option value="tls" {{ ($settings['mail_encryption'] ?? 'tls') == 'tls' ? 'selected' : '' }}>TLS</option>
                                <option value="ssl" {{ ($settings['mail_encryption'] ?? 'tls') == 'ssl' ? 'selected' : '' }}>SSL</option>
                                <option value="null" {{ ($settings['mail_encryption'] ?? 'tls') == 'null' ? 'selected' : '' }}>None</option>
                            </select>
                        </div>
                        <div>
                            <label style="font-size:0.68rem;font-weight:600;color:var(--dark-text-secondary);display:block;margin-bottom:4px">From Email</label>
                            <input type="email" name="mail_from_address" value="{{ $settings['mail_from_address'] ?? '' }}" placeholder="noreply@example.com"
                                   style="width:100%;padding:8px 12px;background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:8px;color:var(--dark-text-primary);font-size:0.85rem;outline:none;box-sizing:border-box"
                                   onfocus="this.style.borderColor='var(--apple-blue)'" onblur="this.style.borderColor='var(--dark-separator)'">
                        </div>
                    </div>
                    <div>
                        <label style="font-size:0.68rem;font-weight:600;color:var(--dark-text-secondary);display:block;margin-bottom:4px">From Name</label>
                        <input type="text" name="mail_from_name" value="{{ $settings['mail_from_name'] ?? '' }}" placeholder="Your Company Name"
                               style="width:100%;padding:8px 12px;background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:8px;color:var(--dark-text-primary);font-size:0.85rem;outline:none;box-sizing:border-box"
                               onfocus="this.style.borderColor='var(--apple-blue)'" onblur="this.style.borderColor='var(--dark-separator)'">
                    </div>
                    {{-- Mailgun fields --}}
                    <div id="mailgun-fields" style="display:none;flex-direction:column;gap:12px">
                        <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:12px">
                            <div>
                                <label style="font-size:0.68rem;font-weight:600;color:var(--dark-text-secondary);display:block;margin-bottom:4px">Mailgun Domain</label>
                                <input type="text" name="mailgun_domain" value="{{ $settings['mailgun_domain'] ?? '' }}"
                                       style="width:100%;padding:8px 12px;background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:8px;color:var(--dark-text-primary);font-size:0.85rem;outline:none;box-sizing:border-box"
                                       onfocus="this.style.borderColor='var(--apple-blue)'" onblur="this.style.borderColor='var(--dark-separator)'">
                            </div>
                            <div>
                                <label style="font-size:0.68rem;font-weight:600;color:var(--dark-text-secondary);display:block;margin-bottom:4px">Mailgun Secret</label>
                                <input type="password" name="mailgun_secret" value="{{ $settings['mailgun_secret'] ?? '' }}"
                                       style="width:100%;padding:8px 12px;background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:8px;color:var(--dark-text-primary);font-size:0.85rem;outline:none;box-sizing:border-box"
                                       onfocus="this.style.borderColor='var(--apple-blue)'" onblur="this.style.borderColor='var(--dark-separator)'">
                            </div>
                            <div>
                                <label style="font-size:0.68rem;font-weight:600;color:var(--dark-text-secondary);display:block;margin-bottom:4px">Mailgun Endpoint</label>
                                <input type="text" name="mailgun_endpoint" value="{{ $settings['mailgun_endpoint'] ?? '' }}"
                                       style="width:100%;padding:8px 12px;background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:8px;color:var(--dark-text-primary);font-size:0.85rem;outline:none;box-sizing:border-box"
                                       onfocus="this.style.borderColor='var(--apple-blue)'" onblur="this.style.borderColor='var(--dark-separator)'">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Email Sending --}}
            <div style="background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:14px;padding:20px 22px">
                <h3 style="font-size:0.85rem;font-weight:700;color:var(--dark-text-primary);margin:0 0 14px;display:flex;align-items:center;gap:8px">
                    <i class="fas fa-paper-plane" style="color:var(--apple-green);font-size:0.78rem"></i>Email Sending
                </h3>
                <div style="display:flex;flex-direction:column;gap:12px">
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
                        <div>
                            <label style="font-size:0.68rem;font-weight:600;color:var(--dark-text-secondary);display:block;margin-bottom:4px">Rate Limit (per jam)</label>
                            <input type="number" name="rate_limit" value="{{ $settings['rate_limit'] ?? '100' }}" placeholder="100"
                                   style="width:100%;padding:8px 12px;background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:8px;color:var(--dark-text-primary);font-size:0.85rem;outline:none;box-sizing:border-box"
                                   onfocus="this.style.borderColor='var(--apple-green)'" onblur="this.style.borderColor='var(--dark-separator)'">
                        </div>
                        <div>
                            <label style="font-size:0.68rem;font-weight:600;color:var(--dark-text-secondary);display:block;margin-bottom:4px">Batch Size</label>
                            <input type="number" name="batch_size" value="{{ $settings['batch_size'] ?? '50' }}" placeholder="50"
                                   style="width:100%;padding:8px 12px;background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:8px;color:var(--dark-text-primary);font-size:0.85rem;outline:none;box-sizing:border-box"
                                   onfocus="this.style.borderColor='var(--apple-green)'" onblur="this.style.borderColor='var(--dark-separator)'">
                        </div>
                    </div>
                    <label style="display:flex;align-items:center;gap:8px;cursor:pointer">
                        <input type="checkbox" name="queue_emails" value="1" {{ ($settings['queue_emails'] ?? false) ? 'checked' : '' }}>
                        <span style="font-size:0.82rem;color:var(--dark-text-secondary)">Queue emails for background processing</span>
                    </label>
                </div>
            </div>

            {{-- Tracking & Analytics --}}
            <div style="background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:14px;padding:20px 22px">
                <h3 style="font-size:0.85rem;font-weight:700;color:var(--dark-text-primary);margin:0 0 14px;display:flex;align-items:center;gap:8px">
                    <i class="fas fa-chart-line" style="color:var(--apple-purple);font-size:0.78rem"></i>Tracking &amp; Analytics
                </h3>
                <div style="display:flex;flex-direction:column;gap:10px">
                    <label style="display:flex;align-items:center;gap:8px;cursor:pointer">
                        <input type="checkbox" name="track_opens" value="1" {{ ($settings['track_opens'] ?? true) ? 'checked' : '' }}>
                        <span style="font-size:0.82rem;color:var(--dark-text-secondary)">Track email opens</span>
                    </label>
                    <label style="display:flex;align-items:center;gap:8px;cursor:pointer">
                        <input type="checkbox" name="track_clicks" value="1" {{ ($settings['track_clicks'] ?? true) ? 'checked' : '' }}>
                        <span style="font-size:0.82rem;color:var(--dark-text-secondary)">Track link clicks</span>
                    </label>
                    <label style="display:flex;align-items:center;gap:8px;cursor:pointer">
                        <input type="checkbox" name="track_unsubscribes" value="1" {{ ($settings['track_unsubscribes'] ?? true) ? 'checked' : '' }}>
                        <span style="font-size:0.82rem;color:var(--dark-text-secondary)">Track unsubscribes</span>
                    </label>
                </div>
            </div>

            {{-- Unsubscribe Settings --}}
            <div style="background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:14px;padding:20px 22px">
                <h3 style="font-size:0.85rem;font-weight:700;color:var(--dark-text-primary);margin:0 0 14px;display:flex;align-items:center;gap:8px">
                    <i class="fas fa-user-slash" style="color:var(--apple-orange);font-size:0.78rem"></i>Unsubscribe Settings
                </h3>
                <div style="display:flex;flex-direction:column;gap:12px">
                    <div>
                        <label style="font-size:0.68rem;font-weight:600;color:var(--dark-text-secondary);display:block;margin-bottom:4px">Unsubscribe Page URL</label>
                        <input type="url" name="unsubscribe_url" value="{{ $settings['unsubscribe_url'] ?? '' }}" placeholder="https://example.com/unsubscribe"
                               style="width:100%;padding:8px 12px;background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:8px;color:var(--dark-text-primary);font-size:0.85rem;outline:none;box-sizing:border-box"
                               onfocus="this.style.borderColor='var(--apple-orange)'" onblur="this.style.borderColor='var(--dark-separator)'">
                    </div>
                    <label style="display:flex;align-items:center;gap:8px;cursor:pointer">
                        <input type="checkbox" name="add_unsubscribe_link" value="1" {{ ($settings['add_unsubscribe_link'] ?? true) ? 'checked' : '' }}>
                        <span style="font-size:0.82rem;color:var(--dark-text-secondary)">Automatically add unsubscribe link to all campaigns</span>
                    </label>
                </div>
            </div>

            {{-- Test Email --}}
            <div style="background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:14px;padding:20px 22px;grid-column:span 2">
                <h3 style="font-size:0.85rem;font-weight:700;color:var(--dark-text-primary);margin:0 0 14px;display:flex;align-items:center;gap:8px">
                    <i class="fas fa-vial" style="color:var(--apple-teal);font-size:0.78rem"></i>Test Email
                </h3>
                <div style="display:flex;gap:10px;align-items:flex-end;flex-wrap:wrap">
                    <div style="flex:1;min-width:200px">
                        <label style="font-size:0.68rem;font-weight:600;color:var(--dark-text-secondary);display:block;margin-bottom:4px">Email Tujuan</label>
                        <input type="email" id="test-email" value="{{ $settings['mail_from_address'] ?? '' }}" placeholder="test@example.com"
                               style="width:100%;padding:8px 12px;background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:8px;color:var(--dark-text-primary);font-size:0.85rem;outline:none;box-sizing:border-box"
                               onfocus="this.style.borderColor='var(--apple-teal)'" onblur="this.style.borderColor='var(--dark-separator)'">
                    </div>
                    <button type="button" onclick="sendEmailSettingsTest()"
                            style="padding:8px 18px;background:color-mix(in srgb,var(--apple-teal) 15%,transparent);border:1px solid color-mix(in srgb,var(--apple-teal) 30%,var(--dark-separator));border-radius:10px;color:var(--apple-teal);font-size:0.82rem;font-weight:600;cursor:pointer;white-space:nowrap"
                            onmouseover="this.style.opacity='.8'" onmouseout="this.style.opacity='1'">
                        <i class="fas fa-paper-plane" style="margin-right:6px"></i>Send Test
                    </button>
                </div>
                <p style="font-size:0.72rem;color:var(--dark-text-secondary);opacity:.6;margin:8px 0 0">
                    Status berhasil hanya berarti email diterima SMTP/provider. Provider seperti Brevo tetap dapat memblokir delivery jika recipient berstatus unsubscribed atau masuk suppression list.
                </p>
                <p id="email-settings-test-result" style="font-size:0.78rem;color:var(--dark-text-secondary);margin:6px 0 0"></p>
            </div>
        </div>

        {{-- Submit --}}
        <div style="display:flex;justify-content:flex-end;gap:10px;margin-top:16px">
            <a href="{{ route('admin.email-management.index', ['tab' => 'settings']) }}"
               style="padding:9px 22px;border:1px solid var(--dark-separator);border-radius:10px;color:var(--dark-text-secondary);font-size:0.82rem;font-weight:600;text-decoration:none;display:inline-flex;align-items:center"
               onmouseover="this.style.color='var(--dark-text-primary)'" onmouseout="this.style.color='var(--dark-text-secondary)'">Cancel</a>
            <button type="submit"
                    style="padding:9px 22px;background:var(--apple-blue);color:#fff;border:none;border-radius:10px;font-size:0.82rem;font-weight:600;cursor:pointer"
                    onmouseover="this.style.opacity='.85'" onmouseout="this.style.opacity='1'">
                <i class="fas fa-save" style="margin-right:6px"></i>Save Settings
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
function toggleMailerFields() {
    const mailer = document.getElementById('mail-mailer-select');
    const mailgunFields = document.getElementById('mailgun-fields');
    if (!mailer || !mailgunFields) return;
    mailgunFields.style.display = mailer.value === 'mailgun' ? 'flex' : 'none';
}

async function sendEmailSettingsTest() {
    const emailInput = document.getElementById('test-email');
    const result = document.getElementById('email-settings-test-result');
    if (!emailInput || !result || !emailInput.value) return;
    result.textContent = 'Mengirim test email...';
    result.style.color = 'var(--dark-text-secondary)';
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
        result.style.color = response.ok ? 'var(--apple-green)' : 'var(--apple-red)';
    } catch (error) {
        result.textContent = 'Gagal mengirim test email.';
        result.style.color = 'var(--apple-red)';
    }
}

document.addEventListener('DOMContentLoaded', toggleMailerFields);
</script>
@endpush
