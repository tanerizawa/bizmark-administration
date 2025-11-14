@extends('layouts.app')

@section('title', 'Compose Email')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1 text-white">
                <i class="fas fa-edit me-2"></i>Compose Email
            </h1>
            <p class="text-muted">Kirim email baru</p>
        </div>
        <a href="{{ route('admin.inbox.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Inbox
        </a>
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

    <!-- Compose Form -->
    <div class="card bg-dark border-dark shadow-sm">
        <div class="card-body">
            <form action="{{ route('admin.inbox.send') }}" method="POST">
                @csrf
                
                <!-- To Email -->
                <div class="mb-3">
                    <label for="to_email" class="form-label text-white">
                        <i class="fas fa-envelope me-2"></i>To
                    </label>
                    <input type="email" 
                           class="form-control bg-dark text-white border-secondary @error('to_email') is-invalid @enderror" 
                           id="to_email" 
                           name="to_email" 
                           value="{{ old('to_email') }}"
                           placeholder="recipient@example.com" 
                           required>
                    @error('to_email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Subject -->
                <div class="mb-3">
                    <label for="subject" class="form-label text-white">
                        <i class="fas fa-tag me-2"></i>Subject
                    </label>
                    <input type="text" 
                           class="form-control bg-dark text-white border-secondary @error('subject') is-invalid @enderror" 
                           id="subject" 
                           name="subject" 
                           value="{{ old('subject') }}"
                           placeholder="Email subject" 
                           required>
                    @error('subject')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Body -->
                <div class="mb-3">
                    <label for="body_html" class="form-label text-white">
                        <i class="fas fa-align-left me-2"></i>Message
                    </label>
                    <textarea class="form-control bg-dark text-white border-secondary @error('body_html') is-invalid @enderror" 
                              id="body_html" 
                              name="body_html" 
                              rows="15" 
                              placeholder="Write your message here..."
                              required>{{ old('body_html') }}</textarea>
                    @error('body_html')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div class="form-text text-muted">
                        <i class="fas fa-info-circle me-1"></i>
                        You can use HTML formatting if needed
                    </div>
                </div>

                <!-- Actions -->
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-paper-plane me-2"></i>Send Email
                        </button>
                        <button type="button" class="btn btn-secondary" onclick="saveDraft()">
                            <i class="fas fa-save me-2"></i>Save Draft
                        </button>
                    </div>
                    <a href="{{ route('admin.inbox.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times me-2"></i>Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Quick Tips -->
    <div class="card bg-dark border-dark shadow-sm mt-3">
        <div class="card-body">
            <h6 class="text-white mb-3">
                <i class="fas fa-lightbulb text-warning me-2"></i>Email Tips
            </h6>
            <ul class="text-muted mb-0 small">
                <li>Pastikan email penerima valid dan aktif</li>
                <li>Tulis subject yang jelas dan deskriptif</li>
                <li>Gunakan format HTML untuk tampilan yang lebih menarik</li>
                <li>Email akan tersimpan di folder "Sent" setelah terkirim</li>
            </ul>
        </div>
    </div>
</div>

<script>
function saveDraft() {
    const to = document.getElementById('to_email').value;
    const subject = document.getElementById('subject').value;
    const body = document.getElementById('body_html').value;
    
    // Save to localStorage
    localStorage.setItem('email_draft', JSON.stringify({
        to: to,
        subject: subject,
        body: body,
        saved_at: new Date().toISOString()
    }));
    
    // Show notification
    alert('Draft saved successfully!');
}

// Load draft on page load
document.addEventListener('DOMContentLoaded', function() {
    const draft = localStorage.getItem('email_draft');
    if (draft) {
        const data = JSON.parse(draft);
        
        // Ask user if they want to restore draft
        if (confirm('Found saved draft from ' + new Date(data.saved_at).toLocaleString() + '. Restore?')) {
            document.getElementById('to_email').value = data.to || '';
            document.getElementById('subject').value = data.subject || '';
            document.getElementById('body_html').value = data.body || '';
        }
    }
});

// Clear draft after successful send
@if(session('success'))
    localStorage.removeItem('email_draft');
@endif
</script>
@endsection
