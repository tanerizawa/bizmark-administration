@extends('layouts.app')

@section('title', 'Email Inbox')

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Sidebar Menu -->
        <div class="col-md-2">
            <div class="card bg-dark border-dark">
                <div class="card-body">
                    <a href="{{ route('admin.inbox.compose') }}" class="btn btn-primary w-100 mb-3">
                        <i class="fas fa-plus me-2"></i>Compose
                    </a>

                    <div class="list-group list-group-flush">
                        <a href="{{ route('admin.inbox.index', ['category' => 'inbox']) }}" 
                           class="list-group-item list-group-item-action bg-dark text-white border-secondary {{ $category === 'inbox' ? 'active' : '' }}">
                            <i class="fas fa-inbox me-2"></i>Inbox
                            @if($stats['unread'] > 0)
                                <span class="badge bg-danger float-end">{{ $stats['unread'] }}</span>
                            @endif
                        </a>
                        <a href="{{ route('admin.inbox.index', ['category' => 'sent']) }}" 
                           class="list-group-item list-group-item-action bg-dark text-white border-secondary {{ $category === 'sent' ? 'active' : '' }}">
                            <i class="fas fa-paper-plane me-2"></i>Sent
                            <span class="badge bg-secondary float-end">{{ $stats['sent'] }}</span>
                        </a>
                        <a href="{{ route('admin.inbox.index', ['is_starred' => 1]) }}" 
                           class="list-group-item list-group-item-action bg-dark text-white border-secondary">
                            <i class="fas fa-star me-2 text-warning"></i>Starred
                            <span class="badge bg-secondary float-end">{{ $stats['starred'] }}</span>
                        </a>
                        <a href="{{ route('admin.inbox.index', ['category' => 'trash']) }}" 
                           class="list-group-item list-group-item-action bg-dark text-white border-secondary {{ $category === 'trash' ? 'active' : '' }}">
                            <i class="fas fa-trash me-2"></i>Trash
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Email List -->
        <div class="col-md-10">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-2 text-white">
                        @if($category === 'inbox')
                            <i class="fas fa-inbox me-2"></i>Inbox
                        @elseif($category === 'sent')
                            <i class="fas fa-paper-plane me-2"></i>Sent
                        @elseif($category === 'trash')
                            <i class="fas fa-trash me-2"></i>Trash
                        @endif
                    </h1>
                    <p class="text-muted">{{ $emails->total() }} emails</p>
                </div>
            </div>

            <!-- Search Bar -->
            <div class="card bg-dark border-dark mb-3">
                <div class="card-body">
                    <form action="{{ route('admin.inbox.index') }}" method="GET" class="row g-2">
                        <input type="hidden" name="category" value="{{ $category }}">
                        <div class="col-md-6">
                            <input type="text" name="search" class="form-control bg-dark text-white border-secondary" 
                                   placeholder="Cari email..." value="{{ request('search') }}">
                        </div>
                        <div class="col-md-2">
                            <select name="is_read" class="form-select bg-dark text-white border-secondary">
                                <option value="">Semua</option>
                                <option value="0" {{ request('is_read') === '0' ? 'selected' : '' }}>Unread</option>
                                <option value="1" {{ request('is_read') === '1' ? 'selected' : '' }}>Read</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                        <div class="col-md-2">
                            <a href="{{ route('admin.inbox.index', ['category' => $category]) }}" 
                               class="btn btn-secondary w-100">Reset</a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Email List -->
            <div class="card bg-dark border-dark">
                <div class="list-group list-group-flush">
                    @forelse($emails as $email)
                        <a href="{{ route('admin.inbox.show', $email) }}" 
                           class="list-group-item list-group-item-action bg-dark text-white border-secondary {{ !$email->is_read ? 'fw-bold' : '' }}">
                            <div class="d-flex w-100 justify-content-between align-items-center">
                                <div class="flex-grow-1">
                                    <div class="d-flex align-items-center mb-2">
                                        <!-- Star Icon -->
                                        <button type="button" 
                                                class="btn btn-link btn-sm text-{{ $email->is_starred ? 'warning' : 'muted' }} p-0 me-2"
                                                onclick="event.preventDefault(); toggleStar({{ $email->id }}, this)">
                                            <i class="fas fa-star"></i>
                                        </button>
                                        
                                        <!-- From/To -->
                                        <strong>
                                            @if($category === 'sent')
                                                To: {{ $email->to_email }}
                                            @else
                                                {{ $email->from_name ?? $email->from_email }}
                                            @endif
                                        </strong>
                                        
                                        @if(!$email->is_read && $category === 'inbox')
                                            <span class="badge bg-primary ms-2">New</span>
                                        @endif
                                    </div>
                                    
                                    <h6 class="mb-1 {{ !$email->is_read ? 'text-white' : 'text-muted' }}">
                                        {{ $email->subject }}
                                    </h6>
                                    
                                    <p class="mb-0 text-muted small">
                                        {{ Str::limit(strip_tags($email->body_text ?? $email->body_html), 100) }}
                                    </p>
                                </div>
                                
                                <div class="text-end">
                                    <small class="text-muted">
                                        {{ $email->received_at->format('d M') }}
                                    </small>
                                    <br>
                                    <small class="text-muted">
                                        {{ $email->received_at->format('H:i') }}
                                    </small>
                                </div>
                            </div>
                        </a>
                    @empty
                        <div class="list-group-item bg-dark text-center text-muted py-5">
                            <i class="fas fa-inbox fa-3x mb-3"></i>
                            <p>Tidak ada email</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Pagination -->
            @if($emails->hasPages())
                <div class="mt-4">
                    {{ $emails->appends(request()->query())->links() }}
                </div>
            @endif
        </div>
    </div>
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
            button.classList.toggle('text-warning');
            button.classList.toggle('text-muted');
        }
    });
}
</script>
@endsection
