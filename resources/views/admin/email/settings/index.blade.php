@extends('layouts.app')

@section('title', 'Email Settings')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 text-white mb-1">
                <i class="fas fa-cog me-2"></i>Email Settings
            </h1>
            <p class="text-muted mb-0">Configure SMTP settings for sending emails</p>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="row">
        <!-- SMTP Configuration -->
        <div class="col-lg-8">
            <div class="card bg-dark border-secondary shadow-sm">
                <div class="card-header bg-gradient-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-server me-2"></i>SMTP Configuration
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.email.settings.update') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <!-- Mail Driver -->
                        <div class="mb-3">
                            <label for="mail_mailer" class="form-label text-white">
                                Mail Driver <span class="text-danger">*</span>
                            </label>
                            <select class="form-select bg-dark text-white border-secondary" 
                                    id="mail_mailer" 
                                    name="mail_mailer" 
                                    required>
                                <option value="mailgun" {{ old('mail_mailer', $settings['mail_mailer']) === 'mailgun' ? 'selected' : '' }}>
                                    Mailgun (Recommended)
                                </option>
                                <option value="smtp" {{ old('mail_mailer', $settings['mail_mailer']) === 'smtp' ? 'selected' : '' }}>
                                    SMTP (Custom Server)
                                </option>
                                <option value="sendmail" {{ old('mail_mailer', $settings['mail_mailer']) === 'sendmail' ? 'selected' : '' }}>
                                    Sendmail (Local Server)
                                </option>
                                <option value="log" {{ old('mail_mailer', $settings['mail_mailer']) === 'log' ? 'selected' : '' }}>
                                    Log (Development Only)
                                </option>
                            </select>
                            <small class="form-text text-muted">
                                Mailgun recommended for production. SMTP for custom servers. Log for testing only.
                            </small>
                        </div>

                        <!-- Mailgun Fields -->
                        <div id="mailgun-fields" style="display: none;">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                <strong>Mailgun Setup:</strong> Sign up at <a href="https://signup.mailgun.com/" target="_blank" class="text-white"><u>mailgun.com</u></a>, 
                                add domain <code>mg.bizmark.id</code>, then add DNS records provided by Mailgun.
                            </div>

                            <!-- Mailgun Domain -->
                            <div class="mb-3">
                                <label for="mailgun_domain" class="form-label text-white">
                                    Mailgun Domain <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       class="form-control bg-dark text-white border-secondary" 
                                       id="mailgun_domain" 
                                       name="mailgun_domain"
                                       value="{{ old('mailgun_domain', config('services.mailgun.domain')) }}"
                                       placeholder="mg.bizmark.id">
                                <small class="form-text text-muted">
                                    Your verified domain in Mailgun (e.g., mg.bizmark.id)
                                </small>
                            </div>

                            <!-- Mailgun API Key -->
                            <div class="mb-3">
                                <label for="mailgun_secret" class="form-label text-white">
                                    Mailgun API Key <span class="text-danger">*</span>
                                </label>
                                <input type="password" 
                                       class="form-control bg-dark text-white border-secondary" 
                                       id="mailgun_secret" 
                                       name="mailgun_secret"
                                       value="{{ old('mailgun_secret', config('services.mailgun.secret')) }}"
                                       placeholder="key-••••••••••••">
                                <small class="form-text text-muted">
                                    Get from Mailgun Dashboard → Settings → API Keys
                                </small>
                            </div>

                            <!-- Mailgun Endpoint -->
                            <div class="mb-3">
                                <label for="mailgun_endpoint" class="form-label text-white">
                                    Mailgun Endpoint
                                </label>
                                <select class="form-select bg-dark text-white border-secondary" 
                                        id="mailgun_endpoint" 
                                        name="mailgun_endpoint">
                                    <option value="api.eu.mailgun.net" {{ old('mailgun_endpoint', config('services.mailgun.endpoint')) === 'api.eu.mailgun.net' ? 'selected' : '' }}>
                                        EU (api.eu.mailgun.net) - GDPR Compliant
                                    </option>
                                    <option value="api.mailgun.net" {{ old('mailgun_endpoint', config('services.mailgun.endpoint')) === 'api.mailgun.net' ? 'selected' : '' }}>
                                        US (api.mailgun.net)
                                    </option>
                                </select>
                                <small class="form-text text-muted">
                                    Choose EU for GDPR compliance
                                </small>
                            </div>
                        </div>

                        <div id="smtp-fields">
                            <!-- SMTP Host -->
                            <div class="mb-3">
                                <label for="mail_host" class="form-label text-white">
                                    SMTP Host <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       class="form-control bg-dark text-white border-secondary" 
                                       id="mail_host" 
                                       name="mail_host"
                                       value="{{ old('mail_host', $settings['mail_host']) }}"
                                       placeholder="smtp.gmail.com">
                                <small class="form-text text-muted">
                                    Examples: smtp.gmail.com, smtp.office365.com, smtp.mailgun.org
                                </small>
                            </div>

                            <!-- SMTP Port -->
                            <div class="mb-3">
                                <label for="mail_port" class="form-label text-white">
                                    SMTP Port <span class="text-danger">*</span>
                                </label>
                                <input type="number" 
                                       class="form-control bg-dark text-white border-secondary" 
                                       id="mail_port" 
                                       name="mail_port"
                                       value="{{ old('mail_port', $settings['mail_port']) }}"
                                       placeholder="587">
                                <small class="form-text text-muted">
                                    Common ports: 587 (TLS), 465 (SSL), 25 (Unsecured)
                                </small>
                            </div>

                            <!-- Encryption -->
                            <div class="mb-3">
                                <label for="mail_encryption" class="form-label text-white">
                                    Encryption
                                </label>
                                <select class="form-select bg-dark text-white border-secondary" 
                                        id="mail_encryption" 
                                        name="mail_encryption">
                                    <option value="tls" {{ old('mail_encryption', $settings['mail_encryption']) === 'tls' ? 'selected' : '' }}>
                                        TLS (Recommended)
                                    </option>
                                    <option value="ssl" {{ old('mail_encryption', $settings['mail_encryption']) === 'ssl' ? 'selected' : '' }}>
                                        SSL
                                    </option>
                                    <option value="" {{ old('mail_encryption', $settings['mail_encryption']) === '' ? 'selected' : '' }}>
                                        None
                                    </option>
                                </select>
                            </div>

                            <!-- SMTP Username -->
                            <div class="mb-3">
                                <label for="mail_username" class="form-label text-white">
                                    SMTP Username
                                </label>
                                <input type="text" 
                                       class="form-control bg-dark text-white border-secondary" 
                                       id="mail_username" 
                                       name="mail_username"
                                       value="{{ old('mail_username', $settings['mail_username']) }}"
                                       placeholder="your-email@gmail.com">
                            </div>

                            <!-- SMTP Password -->
                            <div class="mb-3">
                                <label for="mail_password" class="form-label text-white">
                                    SMTP Password
                                </label>
                                <input type="password" 
                                       class="form-control bg-dark text-white border-secondary" 
                                       id="mail_password" 
                                       name="mail_password"
                                       placeholder="••••••••••••">
                                <small class="form-text text-muted">
                                    Leave empty to keep current password
                                </small>
                            </div>
                        </div>

                        <!-- From Email -->
                        <div class="mb-3">
                            <label for="mail_from_address" class="form-label text-white">
                                From Email Address <span class="text-danger">*</span>
                            </label>
                            <input type="email" 
                                   class="form-control bg-dark text-white border-secondary" 
                                   id="mail_from_address" 
                                   name="mail_from_address"
                                   value="{{ old('mail_from_address', $settings['mail_from_address']) }}"
                                   placeholder="noreply@bizmark.id"
                                   required>
                        </div>

                        <!-- From Name -->
                        <div class="mb-3">
                            <label for="mail_from_name" class="form-label text-white">
                                From Name <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control bg-dark text-white border-secondary" 
                                   id="mail_from_name" 
                                   name="mail_from_name"
                                   value="{{ old('mail_from_name', $settings['mail_from_name']) }}"
                                   placeholder="Bizmark.id"
                                   required>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Save Settings
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Test Email & Info -->
        <div class="col-lg-4">
            <!-- Test Email -->
            <div class="card bg-dark border-secondary shadow-sm mb-4">
                <div class="card-header bg-gradient-info text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-paper-plane me-2"></i>Test Email
                    </h5>
                </div>
                <div class="card-body">
                    <p class="text-muted small mb-3">
                        Send a test email to verify your SMTP configuration
                    </p>
                    
                    <div class="mb-3">
                        <label for="test_email" class="form-label text-white">Test Email Address</label>
                        <input type="email" 
                               class="form-control bg-dark text-white border-secondary" 
                               id="test_email" 
                               placeholder="test@example.com">
                    </div>

                    <button type="button" class="btn btn-info w-100" onclick="sendTestEmail()">
                        <i class="fas fa-paper-plane me-2"></i>Send Test Email
                    </button>

                    <div id="test-result" class="mt-3"></div>
                </div>
            </div>

            <!-- SMTP Providers Info -->
            <div class="card bg-dark border-secondary shadow-sm">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-info-circle me-2"></i>Popular SMTP Providers
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <strong class="text-white">Mailgun (Recommended)</strong>
                        <p class="text-muted small mb-1">Best for transactional emails</p>
                        <p class="text-muted small mb-0">$35/month for 50k emails</p>
                    </div>

                    <div class="mb-3">
                        <strong class="text-white">SendGrid</strong>
                        <p class="text-muted small mb-1">Host: smtp.sendgrid.net</p>
                        <p class="text-muted small mb-0">Port: 587 (TLS)</p>
                    </div>

                    <div class="mb-3">
                        <strong class="text-white">Gmail</strong>
                        <p class="text-muted small mb-1">Host: smtp.gmail.com</p>
                        <p class="text-muted small mb-0">Port: 587 (TLS)</p>
                    </div>

                    <div>
                        <strong class="text-white">Office 365</strong>
                        <p class="text-muted small mb-1">Host: smtp.office365.com</p>
                        <p class="text-muted small mb-0">Port: 587 (TLS)</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function sendTestEmail() {
    const email = document.getElementById('test_email').value;
    const resultDiv = document.getElementById('test-result');
    
    if (!email) {
        resultDiv.innerHTML = '<div class="alert alert-warning alert-dismissible fade show" role="alert"><small>Please enter an email address</small><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
        return;
    }

    resultDiv.innerHTML = '<div class="alert alert-info"><small><i class="fas fa-spinner fa-spin me-2"></i>Sending test email...</small></div>';

    fetch('{{ route('admin.email.settings.test') }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ test_email: email })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            resultDiv.innerHTML = '<div class="alert alert-success alert-dismissible fade show" role="alert"><small><i class="fas fa-check-circle me-2"></i>' + data.message + '</small><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
        } else {
            resultDiv.innerHTML = '<div class="alert alert-danger alert-dismissible fade show" role="alert"><small><i class="fas fa-exclamation-circle me-2"></i>' + data.message + '</small><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
        }
    })
    .catch(error => {
        resultDiv.innerHTML = '<div class="alert alert-danger alert-dismissible fade show" role="alert"><small><i class="fas fa-exclamation-circle me-2"></i>Error: ' + error.message + '</small><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
    });
}

// Toggle fields based on mail driver selection
function toggleMailFields() {
    const mailer = document.getElementById('mail_mailer').value;
    const smtpFields = document.getElementById('smtp-fields');
    const mailgunFields = document.getElementById('mailgun-fields');
    
    // Hide all first
    smtpFields.style.display = 'none';
    mailgunFields.style.display = 'none';
    
    // Show relevant fields
    if (mailer === 'mailgun') {
        mailgunFields.style.display = 'block';
    } else if (mailer === 'smtp') {
        smtpFields.style.display = 'block';
    }
    // 'log' and 'sendmail' show neither
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    toggleMailFields();
    document.getElementById('mail_mailer').addEventListener('change', toggleMailFields);
});
</script>
@endsection
