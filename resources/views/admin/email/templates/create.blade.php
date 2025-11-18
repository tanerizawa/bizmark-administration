@extends('layouts.app')

@section('title', 'Create Template')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1 text-white">
                <i class="fas fa-plus-circle me-2"></i>Create Email Template
            </h1>
            <p class="text-muted">Create a new reusable email template</p>
        </div>
        <a href="{{ route('admin.templates.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Templates
        </a>
    </div>

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <form action="{{ route('admin.templates.store') }}" method="POST">
        @csrf
        
        <div class="row">
            <!-- Left Column - Template Content -->
            <div class="col-lg-8">
                <!-- Basic Information -->
                <div class="card bg-dark border-dark shadow-sm mb-3">
                    <div class="card-header bg-dark border-secondary">
                        <h5 class="mb-0 text-white">
                            <i class="fas fa-info-circle me-2"></i>Template Information
                        </h5>
                    </div>
                    <div class="card-body">
                        <!-- Template Name -->
                        <div class="mb-3">
                            <label for="name" class="form-label text-white">
                                Template Name <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control bg-dark text-white border-secondary @error('name') is-invalid @enderror" 
                                   id="name" 
                                   name="name" 
                                   value="{{ old('name') }}"
                                   placeholder="e.g., Monthly Newsletter Template"
                                   required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Subject -->
                        <div class="mb-3">
                            <label for="subject" class="form-label text-white">
                                Default Subject <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control bg-dark text-white border-secondary @error('subject') is-invalid @enderror" 
                                   id="subject" 
                                   name="subject" 
                                   value="{{ old('subject') }}"
                                   placeholder="e.g., Newsletter Bulanan - {{month}} {{year}}"
                                   required>
                            @error('subject')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text text-muted">
                                <i class="fas fa-info-circle me-1"></i>
                                Use variables like {{month}}, {{year}}, {{name}} in subject
                            </div>
                        </div>

                        <!-- Category -->
                        <div class="mb-3">
                            <label for="category" class="form-label text-white">
                                Category <span class="text-danger">*</span>
                            </label>
                            <select class="form-select bg-dark text-white border-secondary @error('category') is-invalid @enderror" 
                                    id="category" 
                                    name="category"
                                    required>
                                <option value="">-- Select Category --</option>
                                <option value="newsletter" {{ old('category') === 'newsletter' ? 'selected' : '' }}>
                                    Newsletter
                                </option>
                                <option value="promotional" {{ old('category') === 'promotional' ? 'selected' : '' }}>
                                    Promotional
                                </option>
                                <option value="transactional" {{ old('category') === 'transactional' ? 'selected' : '' }}>
                                    Transactional
                                </option>
                                <option value="announcement" {{ old('category') === 'announcement' ? 'selected' : '' }}>
                                    Announcement
                                </option>
                            </select>
                            @error('category')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- HTML Content -->
                        <div class="mb-3">
                            <label for="content" class="form-label text-white">
                                HTML Content <span class="text-danger">*</span>
                            </label>
                            <textarea class="form-control bg-dark text-white border-secondary @error('content') is-invalid @enderror" 
                                      id="content" 
                                      name="content" 
                                      rows="20" 
                                      required>{{ old('content', '<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Hello {{name}}!</h1>
        <p>Your content here...</p>
    </div>
</body>
</html>') }}</textarea>
                            @error('content')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text text-muted">
                                <i class="fas fa-code me-1"></i>
                                Full HTML email template with inline CSS
                            </div>
                        </div>

                        <!-- Plain Content (Optional) -->
                        <div class="mb-3">
                            <label for="plain_content" class="form-label text-white">
                                Plain Text Version
                                <span class="text-muted">(Optional)</span>
                            </label>
                            <textarea class="form-control bg-dark text-white border-secondary" 
                                      id="plain_content" 
                                      name="plain_content" 
                                      rows="10">{{ old('plain_content') }}</textarea>
                            <div class="form-text text-muted">
                                Plain text fallback for email clients that don't support HTML
                            </div>
                        </div>

                        <!-- Preview Button -->
                        <button type="button" class="btn btn-outline-info" onclick="previewTemplate()">
                            <i class="fas fa-eye me-2"></i>Preview Template
                        </button>
                    </div>
                </div>
            </div>

            <!-- Right Column - Settings & Actions -->
            <div class="col-lg-4">
                <!-- Status -->
                <div class="card bg-dark border-dark shadow-sm mb-3">
                    <div class="card-header bg-dark border-secondary">
                        <h5 class="mb-0 text-white">
                            <i class="fas fa-toggle-on me-2"></i>Status
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="form-check form-switch">
                            <input class="form-check-input" 
                                   type="checkbox" 
                                   id="is_active" 
                                   name="is_active" 
                                   value="1"
                                   {{ old('is_active', true) ? 'checked' : '' }}>
                            <label class="form-check-label text-white" for="is_active">
                                Active Template
                            </label>
                        </div>
                        <small class="text-muted">
                            Only active templates can be used in campaigns
                        </small>
                    </div>
                </div>

                <!-- Variables -->
                <div class="card bg-dark border-dark shadow-sm mb-3">
                    <div class="card-header bg-dark border-secondary">
                        <h5 class="mb-0 text-white">
                            <i class="fas fa-code me-2"></i>Available Variables
                        </h5>
                    </div>
                    <div class="card-body">
                        <p class="text-muted mb-2">Use these variables in your template:</p>
                        <div class="d-flex flex-wrap gap-2">
                            <span class="badge bg-secondary" style="cursor: pointer;" onclick="insertVariable('name')">
                                {{name}}
                            </span>
                            <span class="badge bg-secondary" style="cursor: pointer;" onclick="insertVariable('email')">
                                {{email}}
                            </span>
                            <span class="badge bg-secondary" style="cursor: pointer;" onclick="insertVariable('phone')">
                                {{phone}}
                            </span>
                            <span class="badge bg-secondary" style="cursor: pointer;" onclick="insertVariable('unsubscribe_url')">
                                {{unsubscribe_url}}
                            </span>
                            <span class="badge bg-secondary" style="cursor: pointer;" onclick="insertVariable('month')">
                                {{month}}
                            </span>
                            <span class="badge bg-secondary" style="cursor: pointer;" onclick="insertVariable('year')">
                                {{year}}
                            </span>
                        </div>
                        <small class="text-muted mt-2 d-block">
                            <i class="fas fa-info-circle me-1"></i>
                            Click to insert into template
                        </small>
                    </div>
                </div>

                <!-- Quick Templates -->
                <div class="card bg-dark border-dark shadow-sm mb-3">
                    <div class="card-header bg-dark border-secondary">
                        <h5 class="mb-0 text-white">
                            <i class="fas fa-magic me-2"></i>Quick Insert
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="insertHeader()">
                                Insert Header
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="insertButton()">
                                Insert Button
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="insertFooter()">
                                Insert Footer
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="insertUnsubscribe()">
                                Insert Unsubscribe
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="card bg-dark border-dark shadow-sm">
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Create Template
                            </button>
                            <a href="{{ route('admin.templates.index') }}" class="btn btn-outline-secondary">
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
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-dark border-secondary">
                <h5 class="modal-title text-white">
                    <i class="fas fa-eye me-2"></i>Template Preview
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0">
                <div class="bg-light p-3 border-bottom">
                    <strong>Subject:</strong> <span id="preview_subject"></span>
                </div>
                <div class="p-4" style="background: white;">
                    <div id="preview_content"></div>
                </div>
            </div>
        </div>
    </div>
</div>

@verbatim
<script>
function insertVariable(varName) {
    const textarea = document.getElementById('content');
    const cursorPos = textarea.selectionStart;
    const textBefore = textarea.value.substring(0, cursorPos);
    const textAfter = textarea.value.substring(cursorPos);
    
    textarea.value = textBefore + '{{' + varName + '}}' + textAfter;
    textarea.focus();
    textarea.selectionStart = textarea.selectionEnd = cursorPos + varName.length + 4;
}

function insertHeader() {
    const header = `
<div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 40px 20px; text-align: center;">
    <h1 style="color: white; margin: 0; font-size: 32px;">Bizmark.ID</h1>
</div>
`;
    insertAtCursor(header);
}

function insertButton() {
    const button = `
<div style="text-align: center; margin: 30px 0;">
    <a href="https://bizmark.id" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 15px 40px; text-decoration: none; border-radius: 5px; display: inline-block; font-weight: bold;">
        Kunjungi Website
    </a>
</div>
`;
    insertAtCursor(button);
}

function insertFooter() {
    const footer = `
<div style="background: #f8f9fa; padding: 30px 20px; text-align: center; border-top: 1px solid #dee2e6;">
    <p style="color: #6c757d; margin: 0 0 10px 0;">© ${new Date().getFullYear()} Bizmark.ID. All rights reserved.</p>
    <p style="color: #6c757d; margin: 0;">
        <a href="https://bizmark.id" style="color: #667eea; text-decoration: none;">Visit Website</a> |
        <a href="https://wa.me/62838796028550" style="color: #667eea; text-decoration: none;">WhatsApp</a>
    </p>
</div>
`;
    insertAtCursor(footer);
}

function insertUnsubscribe() {
    const unsubscribe = `
<div style="text-align: center; margin-top: 20px; padding: 20px; background: #f8f9fa;">
    <p style="color: #6c757d; font-size: 12px; margin: 0;">
        Tidak ingin menerima email ini? 
        <a href="{{unsubscribe_url}}" style="color: #667eea; text-decoration: none;">Unsubscribe</a>
    </p>
</div>
`;
    insertAtCursor(unsubscribe);
}

function insertAtCursor(text) {
    const textarea = document.getElementById('content');
    const cursorPos = textarea.selectionStart;
    const textBefore = textarea.value.substring(0, cursorPos);
    const textAfter = textarea.value.substring(cursorPos);
    
    textarea.value = textBefore + text + textAfter;
    textarea.focus();
}

function previewTemplate() {
    const subject = document.getElementById('subject').value;
    const content = document.getElementById('content').value;
    
    // Replace variables with sample data
    let previewContent = content
        .replace(/\{\{name\}\}/g, 'John Doe')
        .replace(/\{\{email\}\}/g, 'john@example.com')
        .replace(/\{\{phone\}\}/g, '6283879602855')
        .replace(/\{\{month\}\}/g, 'November')
        .replace(/\{\{year\}\}/g, '2025')
        .replace(/\{\{unsubscribe_url\}\}/g, '#unsubscribe');
    
    let previewSubject = subject
        .replace(/\{\{name\}\}/g, 'John Doe')
        .replace(/\{\{month\}\}/g, 'November')
        .replace(/\{\{year\}\}/g, '2025');
    
    document.getElementById('preview_subject').textContent = previewSubject || '(No subject)';
    document.getElementById('preview_content').innerHTML = previewContent || '<p class="text-muted">No content</p>';
    
    const modal = new bootstrap.Modal(document.getElementById('previewModal'));
    modal.show();
}
</script>
@endverbatim
@endsection
