@extends('layouts.app')

@section('title', 'Edit Email Account')

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Header -->
        <div class="col-12 mb-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-2 text-white">
                        <i class="fas fa-edit me-2"></i>Edit Email Account
                    </h1>
                    <p class="text-dark-text-secondary mb-0">Update email account settings and assignments</p>
                </div>
                <div>
                    <a href="{{ route('admin.email-accounts.index') }}" class="btn btn-outline-light">
                        <i class="fas fa-arrow-left me-2"></i>Back to List
                    </a>
                </div>
            </div>
        </div>

        <!-- Form -->
        <div class="col-lg-8">
            <form action="{{ route('admin.email-accounts.update', $emailAccount) }}" method="POST">
                @csrf
                @method('PUT')
                @method('PUT')

                <!-- Basic Information Card -->
                <div class="card card-elevated rounded-apple mb-4">
                    <div class="card-header bg-transparent border-bottom border-secondary">
                        <h5 class="mb-0 text-white">
                            <i class="fas fa-info-circle me-2"></i>Basic Information
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label text-white">
                                    Email Address <span class="text-danger">*</span>
                                </label>
                                <input type="email" name="email" class="form-control bg-dark text-white border-secondary @error('email') is-invalid @enderror" 
                                       value="{{ old('email', $emailAccount->email) }}" placeholder="cs@bizmark.id" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-dark-text-secondary">
                                    <i class="fas fa-info-circle me-1"></i>Use @bizmark.id domain
                                </small>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label text-white">
                                    Display Name <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="name" class="form-control bg-dark text-white border-secondary @error('name') is-invalid @enderror" 
                                       value="{{ old('name', $emailAccount->name) }}" placeholder="Customer Service" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label text-white">
                                    Account Type <span class="text-danger">*</span>
                                </label>
                                <select name="type" class="form-select bg-dark text-white border-secondary @error('type') is-invalid @enderror" required id="typeSelect">
                                    <option value="">Select Type</option>
                                    <option value="shared" {{ old('type', $emailAccount->type) === 'shared' ? 'selected' : '' }}>Shared (Multiple Users)</option>
                                    <option value="personal" {{ old('type', $emailAccount->type) === 'personal' ? 'selected' : '' }}>Personal (Single User)</option>
                                </select>
                                @error('type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-dark-text-secondary" id="typeHelp">
                                    <i class="fas fa-info-circle me-1"></i>Choose shared for team emails (cs@, sales@)
                                </small>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label text-white">
                                    Department <span class="text-danger">*</span>
                                </label>
                                <select name="department" class="form-select bg-dark text-white border-secondary @error('department') is-invalid @enderror" required>
                                    <option value="">Select Department</option>
                                    <option value="cs" {{ old('department', $emailAccount->department) === 'cs' ? 'selected' : '' }}>Customer Service</option>
                                    <option value="sales" {{ old('department', $emailAccount->department) === 'sales' ? 'selected' : '' }}>Sales</option>
                                    <option value="support" {{ old('department', $emailAccount->department) === 'support' ? 'selected' : '' }}>Support</option>
                                    <option value="finance" {{ old('department', $emailAccount->department) === 'finance' ? 'selected' : '' }}>Finance</option>
                                    <option value="hr" {{ old('department', $emailAccount->department) === 'hr' ? 'selected' : '' }}>HR</option>
                                    <option value="it" {{ old('department', $emailAccount->department) === 'it' ? 'selected' : '' }}>IT</option>
                                    <option value="marketing" {{ old('department', $emailAccount->department) === 'marketing' ? 'selected' : '' }}>Marketing</option>
                                </select>
                                @error('department')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label class="form-label text-white">Description</label>
                                <textarea name="description" class="form-control bg-dark text-white border-secondary @error('description') is-invalid @enderror" 
                                          rows="3" placeholder="Enter account description (optional)">{{ old('description', $emailAccount->description) }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Email Settings Card -->
                <div class="card card-elevated rounded-apple mb-4">
                    <div class="card-header bg-transparent border-bottom border-secondary">
                        <h5 class="mb-0 text-white">
                            <i class="fas fa-cog me-2"></i>Email Settings
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label text-white">Forward To (Optional)</label>
                                <input type="email" name="forward_to" class="form-control bg-dark text-white border-secondary @error('forward_to') is-invalid @enderror" 
                                       value="{{ old('forward_to', $emailAccount->forward_to) }}" placeholder="forward@example.com">
                                @error('forward_to')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-dark-text-secondary">
                                    <i class="fas fa-info-circle me-1"></i>Auto-forward all emails to this address
                                </small>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label text-white">Max Daily Emails</label>
                                <input type="number" name="max_daily_emails" class="form-control bg-dark text-white border-secondary @error('max_daily_emails') is-invalid @enderror" 
                                       value="{{ old('max_daily_emails', $emailAccount->max_daily_emails ?? 100) }}" min="1">
                                @error('max_daily_emails')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-dark-text-secondary">
                                    <i class="fas fa-info-circle me-1"></i>Maximum emails that can be sent per day
                                </small>
                            </div>

                            <div class="col-12">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="auto_reply_enabled" 
                                           id="autoReplyEnabled" {{ old('auto_reply_enabled', $emailAccount->auto_reply_enabled) ? 'checked' : '' }}>
                                    <label class="form-check-label text-white" for="autoReplyEnabled">
                                        Enable Auto-Reply
                                    </label>
                                </div>
                            </div>

                            <div class="col-12" id="autoReplySettings" style="display: {{ old('auto_reply_enabled', $emailAccount->auto_reply_enabled) ? 'block' : 'none' }};">
                                <label class="form-label text-white">Auto-Reply Message</label>
                                <textarea name="auto_reply_message" class="form-control bg-dark text-white border-secondary @error('auto_reply_message') is-invalid @enderror" 
                                          rows="4" placeholder="Thank you for contacting us. We'll get back to you soon...">{{ old('auto_reply_message', $emailAccount->auto_reply_message) }}</textarea>
                                @error('auto_reply_message')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- User Assignment Note -->
                <div class="card card-elevated rounded-apple mb-4">
                    <div class="card-body">
                        <div class="alert alert-info bg-dark border-secondary text-white mb-0">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Note:</strong> To manage user assignments, please go to the 
                            <a href="{{ route('admin.email-accounts.show', $emailAccount) }}" class="text-apple-blue">detail page</a>
                            and use the "Assign User" button.
                        </div>
                    </div>
                </div>

                <!-- Status Card -->
                <div class="card card-elevated rounded-apple mb-4">
                    <div class="card-body">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_active" 
                                   id="isActive" {{ old('is_active', $emailAccount->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label text-white" for="isActive">
                                Active (Account can send/receive emails)
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="d-flex justify-content-end gap-2 mb-4">
                    <a href="{{ route('admin.email-accounts.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times me-2"></i>Cancel
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Update Email Account
                    </button>
                </div>
            </form>
        </div>

        <!-- Help Sidebar -->
        <div class="col-lg-4">
            <div class="card card-elevated rounded-apple">
                <div class="card-header bg-transparent border-bottom border-secondary">
                    <h5 class="mb-0 text-white">
                        <i class="fas fa-question-circle me-2"></i>Help
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <h6 class="text-white mb-2">
                            <i class="fas fa-users text-apple-green me-2"></i>Shared Account
                        </h6>
                        <p class="text-dark-text-secondary small mb-0">
                            Use for team emails like cs@, sales@, or support@. Multiple users can access and respond.
                        </p>
                    </div>

                    <div class="mb-4">
                        <h6 class="text-white mb-2">
                            <i class="fas fa-user text-apple-purple me-2"></i>Personal Account
                        </h6>
                        <p class="text-dark-text-secondary small mb-0">
                            Use for individual staff like john@bizmark.id. Only one user can be assigned.
                        </p>
                    </div>

                    <div class="mb-4">
                        <h6 class="text-white mb-2">
                            <i class="fas fa-shield-alt text-apple-blue me-2"></i>User Roles
                        </h6>
                        <ul class="text-dark-text-secondary small mb-0 ps-3">
                            <li><strong>Primary:</strong> Main handler, full access</li>
                            <li><strong>Backup:</strong> Can send/receive, limited delete</li>
                            <li><strong>Viewer:</strong> Read-only access</li>
                        </ul>
                    </div>

                    <div class="alert alert-info bg-dark border-secondary text-white">
                        <i class="fas fa-lightbulb me-2"></i>
                        <strong>Tip:</strong> Configure Cloudflare Email Routing to point to your webhook URL for incoming emails.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Toggle auto-reply settings
document.getElementById('autoReplyEnabled').addEventListener('change', function() {
    document.getElementById('autoReplySettings').style.display = this.checked ? 'block' : 'none';
});

// Update type help text
document.getElementById('typeSelect').addEventListener('change', function() {
    const help = document.getElementById('typeHelp');
    if (this.value === 'personal') {
        help.innerHTML = '<i class="fas fa-info-circle me-1"></i>Personal accounts can only have one user assigned';
    } else {
        help.innerHTML = '<i class="fas fa-info-circle me-1"></i>Choose shared for team emails (cs@, sales@)';
    }
});
</script>
@endpush
