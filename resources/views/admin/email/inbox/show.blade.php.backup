@extends('layouts.app')

@section('title', 'Email Detail')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1 text-white">
                <i class="fas fa-envelope-open me-2"></i>Email Detail
            </h1>
        </div>
        <div>
            <a href="{{ route('admin.inbox.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back
            </a>
            <a href="{{ route('admin.inbox.reply', $email->id) }}" class="btn btn-primary">
                <i class="fas fa-reply me-2"></i>Reply
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Email Detail -->
    <div class="card bg-dark border-dark shadow-sm">
        <div class="card-header bg-dark border-secondary">
            <div class="d-flex justify-content-between align-items-start">
                <div class="flex-grow-1">
                    <h4 class="mb-3 text-white">{{ $email->subject }}</h4>
                    
                    <div class="row g-3 text-muted small">
                        <div class="col-md-6">
                            <div class="d-flex align-items-center mb-2">
                                <i class="fas fa-user-circle text-primary me-2"></i>
                                <div>
                                    <strong class="text-white">From:</strong>
                                    {{ $email->from_name ?? $email->from_email }}
                                    @if($email->from_name)
                                        <span class="text-muted">&lt;{{ $email->from_email }}&gt;</span>
                                    @endif
                                </div>
                            </div>
                            <div class="d-flex align-items-center">
                                <i class="fas fa-envelope text-success me-2"></i>
                                <div>
                                    <strong class="text-white">To:</strong>
                                    {{ $email->to_email }}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 text-md-end">
                            <div class="mb-2">
                                <i class="fas fa-clock text-info me-2"></i>
                                <strong class="text-white">Received:</strong>
                                {{ $email->received_at->format('d M Y, H:i') }}
                                <span class="text-muted">({{ $email->received_at->diffForHumans() }})</span>
                            </div>
                            <div>
                                <i class="fas fa-tag text-warning me-2"></i>
                                <strong class="text-white">Category:</strong>
                                <span class="badge bg-{{ $email->category === 'inbox' ? 'primary' : 'secondary' }}">
                                    {{ ucfirst($email->category) }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="ms-3">
                    <button type="button" 
                            class="btn btn-sm btn-outline-warning" 
                            onclick="toggleStar({{ $email->id }}, this)">
                        <i class="fas fa-star {{ $email->is_starred ? 'text-warning' : '' }}"></i>
                    </button>
                    <form action="{{ route('admin.inbox.trash', $email->id) }}" 
                          method="POST" 
                          class="d-inline"
                          onsubmit="return confirm('Move to trash?')">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-outline-danger">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="card-body">
            <!-- Labels -->
            @if($email->labels && count($email->labels) > 0)
                <div class="mb-3">
                    <strong class="text-white me-2">Labels:</strong>
                    @foreach($email->labels as $label)
                        <span class="badge bg-info me-1">{{ $label }}</span>
                    @endforeach
                </div>
            @endif

            <!-- Email Body -->
            <div class="email-content bg-dark-subtle p-4 rounded" style="background: rgba(255,255,255,0.05);">
                @if($email->body_html)
                    <div class="text-white">
                        {!! $email->body_html !!}
                    </div>
                @else
                    <div class="text-white" style="white-space: pre-wrap;">{{ $email->body_text }}</div>
                @endif
            </div>

            <!-- Attachments -->
            @if($email->attachments && count($email->attachments) > 0)
                <div class="mt-4">
                    <h6 class="text-white mb-3">
                        <i class="fas fa-paperclip me-2"></i>Attachments ({{ count($email->attachments) }})
                    </h6>
                    <div class="list-group">
                        @foreach($email->attachments as $attachment)
                            <div class="list-group-item bg-dark border-secondary d-flex justify-content-between align-items-center">
                                <div>
                                    <i class="fas fa-file text-primary me-2"></i>
                                    <span class="text-white">{{ $attachment['filename'] ?? 'Attachment' }}</span>
                                    <small class="text-muted ms-2">
                                        ({{ isset($attachment['size']) ? number_format($attachment['size'] / 1024, 2) . ' KB' : 'Unknown size' }})
                                    </small>
                                </div>
                                <a href="#" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-download"></i> Download
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Action Buttons -->
            <div class="mt-4 d-flex gap-2">
                <a href="{{ route('admin.inbox.reply', $email->id) }}" class="btn btn-primary">
                    <i class="fas fa-reply me-2"></i>Reply
                </a>
                <button type="button" class="btn btn-secondary" onclick="window.print()">
                    <i class="fas fa-print me-2"></i>Print
                </button>
                <form action="{{ route('admin.inbox.mark-unread', $email->id) }}" 
                      method="POST" 
                      class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-secondary">
                        <i class="fas fa-envelope me-2"></i>Mark as Unread
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Thread / Replies -->
    @if($email->replies && $email->replies->count() > 0)
        <div class="mt-3">
            <h5 class="text-white mb-3">
                <i class="fas fa-comments me-2"></i>Replies ({{ $email->replies->count() }})
            </h5>
            @foreach($email->replies as $reply)
                <div class="card bg-dark border-dark shadow-sm mb-2">
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-2">
                            <div>
                                <strong class="text-white">{{ $reply->from_name ?? $reply->from_email }}</strong>
                                <small class="text-muted ms-2">{{ $reply->received_at->diffForHumans() }}</small>
                            </div>
                            <a href="{{ route('admin.inbox.show', $reply->id) }}" class="btn btn-sm btn-outline-primary">
                                View
                            </a>
                        </div>
                        <div class="text-muted small">
                            {{ \Illuminate\Support\Str::limit(strip_tags($reply->body_html ?? $reply->body_text), 150) }}
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>

<script>
function toggleStar(emailId, button) {
    fetch(`/admin/inbox/${emailId}/star`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const icon = button.querySelector('i');
            if (data.starred) {
                icon.classList.add('text-warning');
            } else {
                icon.classList.remove('text-warning');
            }
        }
    });
}
</script>

<style>
@media print {
    .btn, .card-header button, .card-header form, nav, aside, footer {
        display: none !important;
    }
    .card {
        border: none !important;
        box-shadow: none !important;
    }
}
</style>
@endsection
