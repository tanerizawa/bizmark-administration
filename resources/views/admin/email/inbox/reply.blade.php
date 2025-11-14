@extends('layouts.app')

@section('title', 'Reply Email')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1 text-white">
                <i class="fas fa-reply me-2"></i>Reply Email
            </h1>
            <p class="text-muted">Reply to: {{ $email->from_name ?? $email->from_email }}</p>
        </div>
        <a href="{{ route('admin.inbox.show', $email->id) }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back
        </a>
    </div>

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Original Email (Collapsed) -->
    <div class="card bg-dark border-dark shadow-sm mb-3">
        <div class="card-header bg-dark border-secondary">
            <button class="btn btn-link text-white text-decoration-none p-0 w-100 text-start" 
                    type="button" 
                    data-bs-toggle="collapse" 
                    data-bs-target="#originalEmail">
                <i class="fas fa-chevron-down me-2"></i>
                <strong>Original Message</strong>
                <span class="text-muted ms-2">from {{ $email->from_email }}</span>
            </button>
        </div>
        <div class="collapse" id="originalEmail">
            <div class="card-body">
                <div class="mb-2">
                    <strong class="text-white">Subject:</strong>
                    <span class="text-muted">{{ $email->subject }}</span>
                </div>
                <div class="mb-2">
                    <strong class="text-white">Date:</strong>
                    <span class="text-muted">{{ $email->received_at->format('d M Y, H:i') }}</span>
                </div>
                <hr class="border-secondary">
                <div class="text-muted" style="max-height: 300px; overflow-y: auto;">
                    @if($email->body_html)
                        {!! $email->body_html !!}
                    @else
                        <div style="white-space: pre-wrap;">{{ $email->body_text }}</div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Reply Form -->
    <div class="card bg-dark border-dark shadow-sm">
        <div class="card-body">
            <form action="{{ route('admin.inbox.send-reply', $email->id) }}" method="POST">
                @csrf
                
                <!-- To (Readonly) -->
                <div class="mb-3">
                    <label class="form-label text-white">
                        <i class="fas fa-envelope me-2"></i>To
                    </label>
                    <input type="text" 
                           class="form-control bg-dark text-white border-secondary" 
                           value="{{ $email->from_name ?? $email->from_email }} <{{ $email->from_email }}>"
                           readonly>
                </div>

                <!-- Subject (Auto-filled with Re:) -->
                <div class="mb-3">
                    <label class="form-label text-white">
                        <i class="fas fa-tag me-2"></i>Subject
                    </label>
                    <input type="text" 
                           class="form-control bg-dark text-white border-secondary" 
                           value="Re: {{ $email->subject }}"
                           readonly>
                </div>

                <!-- Reply Body -->
                <div class="mb-3">
                    <label for="body_html" class="form-label text-white">
                        <i class="fas fa-align-left me-2"></i>Your Reply
                    </label>
                    <textarea class="form-control bg-dark text-white border-secondary @error('body_html') is-invalid @enderror" 
                              id="body_html" 
                              name="body_html" 
                              rows="12" 
                              placeholder="Write your reply here..."
                              required>{{ old('body_html') }}</textarea>
                    @error('body_html')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div class="form-text text-muted">
                        <i class="fas fa-info-circle me-1"></i>
                        HTML formatting is supported
                    </div>
                </div>

                <!-- Quick Responses (Optional) -->
                <div class="mb-3">
                    <label class="form-label text-white">
                        <i class="fas fa-bolt me-2"></i>Quick Responses
                    </label>
                    <div class="btn-group btn-group-sm" role="group">
                        <button type="button" class="btn btn-outline-secondary" onclick="insertQuickResponse('thanks')">
                            Thank You
                        </button>
                        <button type="button" class="btn btn-outline-secondary" onclick="insertQuickResponse('received')">
                            Received
                        </button>
                        <button type="button" class="btn btn-outline-secondary" onclick="insertQuickResponse('follow')">
                            Will Follow Up
                        </button>
                    </div>
                </div>

                <!-- Actions -->
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-paper-plane me-2"></i>Send Reply
                        </button>
                        <button type="button" class="btn btn-secondary" onclick="saveDraft()">
                            <i class="fas fa-save me-2"></i>Save Draft
                        </button>
                    </div>
                    <a href="{{ route('admin.inbox.show', $email->id) }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times me-2"></i>Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
const quickResponses = {
    thanks: `Terima kasih atas email Anda.

Kami sangat menghargai waktu Anda untuk menghubungi kami.

Salam,
Tim Bizmark.ID`,
    received: `Email Anda telah kami terima.

Kami akan meninjau pesan Anda dan segera memberikan respon.

Terima kasih,
Tim Bizmark.ID`,
    follow: `Terima kasih atas email Anda.

Kami akan menindaklanjuti hal ini dan segera menghubungi Anda kembali.

Hormat kami,
Tim Bizmark.ID`
};

function insertQuickResponse(type) {
    const textarea = document.getElementById('body_html');
    textarea.value = quickResponses[type];
    textarea.focus();
}

function saveDraft() {
    const body = document.getElementById('body_html').value;
    
    localStorage.setItem('reply_draft_{{ $email->id }}', JSON.stringify({
        body: body,
        saved_at: new Date().toISOString()
    }));
    
    alert('Draft saved successfully!');
}

// Load draft on page load
document.addEventListener('DOMContentLoaded', function() {
    const draft = localStorage.getItem('reply_draft_{{ $email->id }}');
    if (draft) {
        const data = JSON.parse(draft);
        
        if (confirm('Found saved draft from ' + new Date(data.saved_at).toLocaleString() + '. Restore?')) {
            document.getElementById('body_html').value = data.body || '';
        }
    }
});
</script>
@endsection
