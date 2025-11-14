@extends('layouts.app')

@section('title', 'Create Campaign')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1 text-white">
                <i class="fas fa-paper-plane me-2"></i>Create Email Campaign
            </h1>
            <p class="text-muted">Create a new email marketing campaign</p>
        </div>
        <a href="{{ route('admin.campaigns.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Campaigns
        </a>
    </div>

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <form action="{{ route('admin.campaigns.store') }}" method="POST">
        @csrf
        
        <div class="row">
            <!-- Left Column - Campaign Details -->
            <div class="col-lg-8">
                <!-- Basic Information -->
                <div class="card bg-dark border-dark shadow-sm mb-3">
                    <div class="card-header bg-dark border-secondary">
                        <h5 class="mb-0 text-white">
                            <i class="fas fa-info-circle me-2"></i>Basic Information
                        </h5>
                    </div>
                    <div class="card-body">
                        <!-- Campaign Name -->
                        <div class="mb-3">
                            <label for="name" class="form-label text-white">
                                Campaign Name <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control bg-dark text-white border-secondary @error('name') is-invalid @enderror" 
                                   id="name" 
                                   name="name" 
                                   value="{{ old('name') }}"
                                   placeholder="e.g., Monthly Newsletter - November 2025"
                                   required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Email Subject -->
                        <div class="mb-3">
                            <label for="subject" class="form-label text-white">
                                Email Subject <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control bg-dark text-white border-secondary @error('subject') is-invalid @enderror" 
                                   id="subject" 
                                   name="subject" 
                                   value="{{ old('subject') }}"
                                   placeholder="e.g., 🎉 Update Terbaru dari Bizmark.ID"
                                   required>
                            @error('subject')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text text-muted">
                                <i class="fas fa-lightbulb me-1"></i>
                                Use emoji and personalization tags like {{name}} for better engagement
                            </div>
                        </div>

                        <!-- Template Selection -->
                        <div class="mb-3">
                            <label for="template_id" class="form-label text-white">
                                Email Template
                            </label>
                            <select class="form-select bg-dark text-white border-secondary @error('template_id') is-invalid @enderror" 
                                    id="template_id" 
                                    name="template_id"
                                    onchange="loadTemplate(this.value)">
                                <option value="">-- Select Template (Optional) --</option>
                                @foreach($templates as $template)
                                    <option value="{{ $template->id }}" 
                                            data-content="{{ $template->content }}"
                                            {{ old('template_id') == $template->id ? 'selected' : '' }}>
                                        {{ $template->name }} ({{ ucfirst($template->category) }})
                                    </option>
                                @endforeach
                            </select>
                            @error('template_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Email Content -->
                        <div class="mb-3">
                            <label for="content" class="form-label text-white">
                                Email Content (HTML) <span class="text-danger">*</span>
                            </label>
                            <textarea class="form-control bg-dark text-white border-secondary @error('content') is-invalid @enderror" 
                                      id="content" 
                                      name="content" 
                                      rows="15" 
                                      required>{{ old('content') }}</textarea>
                            @error('content')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text text-muted">
                                <i class="fas fa-code me-1"></i>
                                Available variables: {{name}}, {{email}}, {{unsubscribe_url}}
                            </div>
                        </div>

                        <!-- Preview Button -->
                        <button type="button" class="btn btn-outline-info btn-sm" onclick="previewContent()">
                            <i class="fas fa-eye me-2"></i>Preview
                        </button>
                    </div>
                </div>
            </div>

            <!-- Right Column - Campaign Settings -->
            <div class="col-lg-4">
                <!-- Recipients -->
                <div class="card bg-dark border-dark shadow-sm mb-3">
                    <div class="card-header bg-dark border-secondary">
                        <h5 class="mb-0 text-white">
                            <i class="fas fa-users me-2"></i>Recipients
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label text-white">Send To</label>
                            <select class="form-select bg-dark text-white border-secondary @error('recipient_type') is-invalid @enderror" 
                                    id="recipient_type" 
                                    name="recipient_type"
                                    onchange="toggleTagsField()"
                                    required>
                                <option value="all" {{ old('recipient_type') == 'all' ? 'selected' : '' }}>
                                    All Subscribers
                                </option>
                                <option value="active" {{ old('recipient_type') == 'active' ? 'selected' : '' }}>
                                    Active Only
                                </option>
                                <option value="tags" {{ old('recipient_type') == 'tags' ? 'selected' : '' }}>
                                    By Tags
                                </option>
                            </select>
                            @error('recipient_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Tags Selection (Hidden by default) -->
                        <div class="mb-3" id="tags_field" style="display: none;">
                            <label for="recipient_tags" class="form-label text-white">Select Tags</label>
                            <input type="text" 
                                   class="form-control bg-dark text-white border-secondary" 
                                   id="recipient_tags" 
                                   name="recipient_tags" 
                                   value="{{ old('recipient_tags') }}"
                                   placeholder="e.g., customer, vip, prospect">
                            <div class="form-text text-muted">
                                Comma-separated tags
                            </div>
                        </div>

                        <!-- Estimated Recipients -->
                        <div class="alert alert-info">
                            <small>
                                <i class="fas fa-info-circle me-1"></i>
                                <strong id="estimated_recipients">0</strong> subscribers will receive this
                            </small>
                        </div>
                    </div>
                </div>

                <!-- Schedule -->
                <div class="card bg-dark border-dark shadow-sm mb-3">
                    <div class="card-header bg-dark border-secondary">
                        <h5 class="mb-0 text-white">
                            <i class="fas fa-clock me-2"></i>Schedule
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <div class="form-check mb-2">
                                <input class="form-check-input" 
                                       type="radio" 
                                       name="schedule_type" 
                                       id="send_now" 
                                       value="now"
                                       onchange="toggleScheduleField()"
                                       checked>
                                <label class="form-check-label text-white" for="send_now">
                                    Send Now
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" 
                                       type="radio" 
                                       name="schedule_type" 
                                       id="schedule_later" 
                                       value="later"
                                       onchange="toggleScheduleField()">
                                <label class="form-check-label text-white" for="schedule_later">
                                    Schedule for Later
                                </label>
                            </div>
                        </div>

                        <div id="schedule_field" style="display: none;">
                            <input type="datetime-local" 
                                   class="form-control bg-dark text-white border-secondary" 
                                   id="scheduled_at" 
                                   name="scheduled_at" 
                                   value="{{ old('scheduled_at') }}">
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="card bg-dark border-dark shadow-sm">
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <button type="submit" name="action" value="send" class="btn btn-primary">
                                <i class="fas fa-paper-plane me-2"></i>Create & Send
                            </button>
                            <button type="submit" name="action" value="draft" class="btn btn-secondary">
                                <i class="fas fa-save me-2"></i>Save as Draft
                            </button>
                            <a href="{{ route('admin.campaigns.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-2"></i>Cancel
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Preview Modal -->
<div class="modal fade" id="previewModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content bg-dark border-secondary">
            <div class="modal-header border-secondary">
                <h5 class="modal-title text-white">
                    <i class="fas fa-eye me-2"></i>Email Preview
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-2">
                    <strong class="text-white">Subject:</strong>
                    <span class="text-muted" id="preview_subject"></span>
                </div>
                <hr class="border-secondary">
                <div id="preview_content" class="text-white"></div>
            </div>
        </div>
    </div>
</div>

<script>
function loadTemplate(templateId) {
    if (!templateId) return;
    
    const option = document.querySelector(`#template_id option[value="${templateId}"]`);
    if (option) {
        const content = option.getAttribute('data-content');
        document.getElementById('content').value = content || '';
    }
}

function toggleTagsField() {
    const recipientType = document.getElementById('recipient_type').value;
    const tagsField = document.getElementById('tags_field');
    
    if (recipientType === 'tags') {
        tagsField.style.display = 'block';
    } else {
        tagsField.style.display = 'none';
    }
    
    updateEstimatedRecipients();
}

function toggleScheduleField() {
    const scheduleType = document.querySelector('input[name="schedule_type"]:checked').value;
    const scheduleField = document.getElementById('schedule_field');
    
    if (scheduleType === 'later') {
        scheduleField.style.display = 'block';
    } else {
        scheduleField.style.display = 'none';
    }
}

function updateEstimatedRecipients() {
    // This should be an AJAX call to get actual count
    // For now, just a placeholder
    document.getElementById('estimated_recipients').textContent = '{{ $activeSubscribers }}';
}

function previewContent() {
    const subject = document.getElementById('subject').value;
    const content = document.getElementById('content').value;
    
    document.getElementById('preview_subject').textContent = subject || '(No subject)';
    document.getElementById('preview_content').innerHTML = content || '<p class="text-muted">No content</p>';
    
    const modal = new bootstrap.Modal(document.getElementById('previewModal'));
    modal.show();
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    toggleTagsField();
    updateEstimatedRecipients();
});
</script>
@endsection
