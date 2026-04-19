@extends('layouts.app')

@section('title', 'Edit Campaign')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1 text-white">
                <i class="fas fa-edit me-2"></i>Edit Campaign
            </h1>
            <p class="text-muted">{{ $campaign->name }}</p>
        </div>
        <div>
            <a href="{{ route('admin.campaigns.show', $campaign->id) }}" class="btn btn-info me-2">
                <i class="fas fa-eye me-2"></i>View
            </a>
            <a href="{{ route('admin.campaigns.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back
            </a>
        </div>
    </div>

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($campaign->status !== 'draft')
        <div class="alert alert-warning">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <strong>Note:</strong> This campaign has status "{{ $campaign->status }}". Some fields may be locked.
        </div>
    @endif

    <form action="{{ route('admin.campaigns.update', $campaign->id) }}" method="POST">
        @csrf
        @method('PUT')
        
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
                                   value="{{ old('name', $campaign->name) }}"
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
                                   value="{{ old('subject', $campaign->subject) }}"
                                   {{ $campaign->status !== 'draft' ? 'readonly' : '' }}
                                   required>
                            @error('subject')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Template Selection -->
                        <div class="mb-3">
                            <label for="template_id" class="form-label text-white">
                                Email Template
                            </label>
                            <select class="form-select bg-dark text-white border-secondary @error('template_id') is-invalid @enderror" 
                                    id="template_id" 
                                    name="template_id"
                                    onchange="loadTemplate(this.value)"
                                    {{ $campaign->status !== 'draft' ? 'disabled' : '' }}>
                                <option value="">-- Select Template (Optional) --</option>
                                @foreach($templates as $template)
                                    <option value="{{ $template->id }}" 
                                            data-content="{{ $template->content }}"
                                            {{ old('template_id', $campaign->template_id) == $template->id ? 'selected' : '' }}>
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
                                      {{ $campaign->status !== 'draft' ? 'readonly' : '' }}
                                      required>{{ old('content', $campaign->content) }}</textarea>
                            @error('content')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
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
                <!-- Status Badge -->
                <div class="card bg-dark border-dark shadow-sm mb-3">
                    <div class="card-body text-center">
                        <h6 class="text-white mb-2">Current Status</h6>
                        <span class="badge 
                            @if($campaign->status === 'draft') bg-warning
                            @elseif($campaign->status === 'scheduled') bg-info
                            @elseif($campaign->status === 'sending') bg-primary
                            @elseif($campaign->status === 'sent') bg-success
                            @else bg-secondary
                            @endif
                            fs-6">
                            {{ ucfirst($campaign->status) }}
                        </span>
                    </div>
                </div>

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
                                    {{ $campaign->status !== 'draft' ? 'disabled' : '' }}
                                    required>
                                <option value="all" {{ old('recipient_type', $campaign->recipient_type) == 'all' ? 'selected' : '' }}>
                                    All Subscribers
                                </option>
                                <option value="active" {{ old('recipient_type', $campaign->recipient_type) == 'active' ? 'selected' : '' }}>
                                    Active Only
                                </option>
                                <option value="tags" {{ old('recipient_type', $campaign->recipient_type) == 'tags' ? 'selected' : '' }}>
                                    By Tags
                                </option>
                            </select>
                            @error('recipient_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Tags Selection -->
                        <div class="mb-3" id="tags_field" style="display: {{ $campaign->recipient_type === 'tags' ? 'block' : 'none' }};">
                            <label for="recipient_tags" class="form-label text-white">Select Tags</label>
                            <input type="text" 
                                   class="form-control bg-dark text-white border-secondary" 
                                   id="recipient_tags" 
                                   name="recipient_tags" 
                                   value="{{ old('recipient_tags', is_array($campaign->recipient_tags) ? implode(',', $campaign->recipient_tags) : '') }}"
                                   placeholder="e.g., customer, vip, prospect"
                                   {{ $campaign->status !== 'draft' ? 'readonly' : '' }}>
                            <div class="form-text text-muted">
                                Comma-separated tags
                            </div>
                        </div>

                        @if($campaign->status === 'sent')
                        <div class="alert alert-success">
                            <small>
                                <i class="fas fa-check-circle me-1"></i>
                                Sent to <strong>{{ $campaign->sent_count }}</strong> of <strong>{{ $campaign->total_recipients }}</strong> recipients
                            </small>
                        </div>
                        @endif
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
                        @if($campaign->status === 'draft')
                        <div class="mb-3">
                            <input type="datetime-local" 
                                   class="form-control bg-dark text-white border-secondary" 
                                   id="scheduled_at" 
                                   name="scheduled_at" 
                                   value="{{ old('scheduled_at', $campaign->scheduled_at ? $campaign->scheduled_at->format('Y-m-d\TH:i') : '') }}">
                            <div class="form-text text-muted">
                                Leave empty to send immediately
                            </div>
                        </div>
                        @else
                        <p class="text-muted mb-0">
                            @if($campaign->scheduled_at)
                                Scheduled: {{ $campaign->scheduled_at->format('d M Y, H:i') }}
                            @elseif($campaign->sent_at)
                                Sent: {{ $campaign->sent_at->format('d M Y, H:i') }}
                            @else
                                Not scheduled
                            @endif
                        </p>
                        @endif
                    </div>
                </div>

                <!-- Actions -->
                <div class="card bg-dark border-dark shadow-sm">
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            @if($campaign->status === 'draft')
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Update Campaign
                            </button>
                            @else
                            <button type="submit" class="btn btn-secondary">
                                <i class="fas fa-save me-2"></i>Update Details
                            </button>
                            @endif
                            <a href="{{ route('admin.campaigns.show', $campaign->id) }}" class="btn btn-outline-info">
                                <i class="fas fa-eye me-2"></i>View Campaign
                            </a>
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
}

function previewContent() {
    const subject = document.getElementById('subject').value;
    const content = document.getElementById('content').value;
    
    document.getElementById('preview_subject').textContent = subject || '(No subject)';
    document.getElementById('preview_content').innerHTML = content || '<p class="text-muted">No content</p>';
    
    const modal = new bootstrap.Modal(document.getElementById('previewModal'));
    modal.show();
}
</script>
@endsection
