@extends('layouts.app')

@section('title', 'Email Detail')

@section('content')
<div class="container-fluid px-4 py-6">
    <!-- Back Button & Actions -->
    <div class="mb-6">
        <div class="flex items-center justify-between mb-4">
            <a href="{{ route('admin.inbox.index') }}" 
               class="inline-flex items-center text-sm text-dark-text-secondary hover:text-dark-text-primary transition-colors">
                <i class="fas fa-arrow-left mr-2"></i>
                Back to Inbox
            </a>
            
            <div class="flex gap-2">
                <!-- Star Button -->
                <button type="button" 
                        onclick="toggleStar({{ $email->id }}, this)"
                        class="btn-apple-secondary inline-flex items-center px-4 py-2 rounded-apple text-sm font-medium transition-apple">
                    <i class="fas fa-star mr-2 {{ $email->is_starred ? 'text-yellow-500' : '' }}"></i>
                    {{ $email->is_starred ? 'Starred' : 'Star' }}
                </button>

                <!-- Reply Button -->
                <a href="{{ route('admin.inbox.reply', $email->id) }}" 
                   class="btn-apple-blue inline-flex items-center px-4 py-2 rounded-apple text-sm font-medium transition-apple">
                    <i class="fas fa-reply mr-2"></i>
                    Reply
                </a>

                <!-- More Actions Dropdown -->
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" 
                            class="btn-apple-secondary inline-flex items-center px-4 py-2 rounded-apple text-sm font-medium transition-apple">
                        <i class="fas fa-ellipsis-v"></i>
                    </button>
                    
                    <div x-show="open" 
                         @click.away="open = false"
                         class="absolute right-0 mt-2 w-48 card-apple py-2 z-50"
                         style="display: none;">
                        <button onclick="window.print()" 
                                class="w-full px-4 py-2 text-left text-sm text-dark-text-secondary hover:bg-dark-bg-tertiary hover:text-dark-text-primary transition-colors">
                            <i class="fas fa-print mr-2"></i>Print
                        </button>
                        <form action="{{ route('admin.inbox.mark-unread', $email->id) }}" method="POST">
                            @csrf
                            <button type="submit" 
                                    class="w-full px-4 py-2 text-left text-sm text-dark-text-secondary hover:bg-dark-bg-tertiary hover:text-dark-text-primary transition-colors">
                                <i class="fas fa-envelope mr-2"></i>Mark as Unread
                            </button>
                        </form>
                        <hr class="my-2" style="border-color: var(--dark-separator);">
                        <form action="{{ route('admin.inbox.trash', $email->id) }}" 
                              method="POST" 
                              onsubmit="return confirm('Move this email to trash?')">
                            @csrf
                            <button type="submit" 
                                    class="w-full px-4 py-2 text-left text-sm text-red-500 hover:bg-dark-bg-tertiary transition-colors">
                                <i class="fas fa-trash mr-2"></i>Move to Trash
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="card-apple p-4 mb-4 bg-green-500 bg-opacity-10 border-green-500">
                <div class="flex items-center">
                    <i class="fas fa-check-circle text-green-500 mr-3"></i>
                    <span class="text-green-500 font-medium">{{ session('success') }}</span>
                </div>
            </div>
        @endif
    </div>

    <!-- Email Detail Card -->
    <div class="card-apple overflow-hidden">
        <!-- Email Header -->
        <div class="px-6 py-5 border-b border-dark-separator">
            <div class="flex items-start justify-between mb-4">
                <div class="flex-grow">
                    <h1 class="text-2xl font-semibold text-dark-text-primary mb-3">
                        {{ $email->subject }}
                    </h1>
                    
                    <!-- Labels -->
                    @if($email->labels && count($email->labels) > 0)
                        <div class="flex flex-wrap gap-2 mb-4">
                            @foreach($email->labels as $label)
                                <span class="px-3 py-1 text-xs font-semibold rounded-full bg-blue-500 bg-opacity-20 text-blue-400">
                                    {{ $label }}
                                </span>
                            @endforeach
                        </div>
                    @endif
                </div>

                <!-- Category Badge -->
                <span class="px-3 py-1 text-xs font-semibold rounded-full {{ $email->category === 'inbox' ? 'bg-apple-blue text-white' : 'bg-dark-bg-tertiary text-dark-text-secondary' }}">
                    {{ ucfirst($email->category) }}
                </span>
            </div>

            <!-- Sender & Recipient Info -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- From -->
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 rounded-full bg-gradient-to-br from-apple-blue to-purple-600 flex items-center justify-center text-white font-semibold">
                            {{ strtoupper(substr($email->from_name ?? $email->from_email, 0, 2)) }}
                        </div>
                    </div>
                    <div class="flex-grow min-w-0">
                        <p class="text-xs font-medium text-dark-text-tertiary uppercase tracking-wide mb-1">From</p>
                        <p class="text-sm font-semibold text-dark-text-primary truncate">
                            {{ $email->from_name ?? $email->from_email }}
                        </p>
                        @if($email->from_name)
                            <p class="text-xs text-dark-text-secondary truncate">
                                {{ $email->from_email }}
                            </p>
                        @endif
                    </div>
                </div>

                <!-- To -->
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 rounded-full bg-gradient-to-br from-green-500 to-emerald-600 flex items-center justify-center text-white font-semibold">
                            {{ strtoupper(substr($email->to_email, 0, 2)) }}
                        </div>
                    </div>
                    <div class="flex-grow min-w-0">
                        <p class="text-xs font-medium text-dark-text-tertiary uppercase tracking-wide mb-1">To</p>
                        <p class="text-sm font-semibold text-dark-text-primary truncate">
                            {{ $email->to_email }}
                        </p>
                        @if($email->emailAccount)
                            <p class="text-xs text-dark-text-secondary">
                                via {{ $email->emailAccount->display_name }}
                            </p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Metadata -->
            <div class="mt-6 pt-4 border-t border-dark-separator">
                <div class="flex flex-wrap items-center gap-6 text-sm">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-clock text-dark-text-tertiary"></i>
                        <span class="text-dark-text-secondary">{{ $email->received_at->format('d M Y, H:i') }}</span>
                        <span class="text-dark-text-tertiary text-xs">({{ $email->received_at->diffForHumans() }})</span>
                    </div>

                    @if($email->has_attachments)
                        <div class="flex items-center gap-2">
                            <i class="fas fa-paperclip text-dark-text-tertiary"></i>
                            <span class="text-dark-text-secondary">{{ count($email->attachments ?? []) }} attachment(s)</span>
                        </div>
                    @endif

                    @if(!$email->is_read)
                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-apple-blue text-white">
                            Unread
                        </span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Email Body -->
        <div class="px-6 py-6">
            <div class="prose prose-invert max-w-none">
                <div class="email-content p-6 rounded-xl" style="background: rgba(255, 255, 255, 0.03); border: 1px solid rgba(255, 255, 255, 0.06);">
                    @if($email->body_html)
                        <div class="text-dark-text-primary" style="color: var(--dark-text-primary) !important;">
                            {!! $email->body_html !!}
                        </div>
                    @else
                        <pre class="text-dark-text-primary font-sans whitespace-pre-wrap" style="color: var(--dark-text-primary) !important;">{{ $email->body_text }}</pre>
                    @endif
                </div>
            </div>
        </div>

        <!-- Attachments -->
        @if($email->attachments && count($email->attachments) > 0)
            <div class="px-6 py-4 border-t border-dark-separator">
                <h3 class="text-sm font-semibold text-dark-text-primary mb-4">
                    <i class="fas fa-paperclip mr-2"></i>
                    Attachments ({{ count($email->attachments) }})
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                    @foreach($email->attachments as $attachment)
                        <div class="p-4 rounded-apple border border-dark-separator hover:bg-dark-bg-tertiary transition-colors">
                            <div class="flex items-start justify-between gap-3">
                                <div class="flex items-start gap-3 min-w-0 flex-grow">
                                    <div class="flex-shrink-0 w-10 h-10 rounded-lg bg-apple-blue bg-opacity-10 flex items-center justify-center">
                                        <i class="fas fa-file text-apple-blue"></i>
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-sm font-medium text-dark-text-primary truncate">
                                            {{ $attachment['filename'] ?? 'Attachment' }}
                                        </p>
                                        <p class="text-xs text-dark-text-tertiary">
                                            {{ isset($attachment['size']) ? number_format($attachment['size'] / 1024, 2) . ' KB' : 'Unknown size' }}
                                        </p>
                                    </div>
                                </div>
                                <a href="#" 
                                   class="flex-shrink-0 w-8 h-8 rounded-lg bg-apple-blue hover:bg-opacity-80 flex items-center justify-center transition-colors">
                                    <i class="fas fa-download text-white text-xs"></i>
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Action Footer -->
        <div class="px-6 py-4 bg-dark-bg-secondary border-t border-dark-separator">
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('admin.inbox.reply', $email->id) }}" 
                   class="btn-apple-blue inline-flex items-center px-4 py-2 rounded-apple text-sm font-medium transition-apple">
                    <i class="fas fa-reply mr-2"></i>
                    Reply
                </a>
                <button type="button" 
                        onclick="window.print()"
                        class="btn-apple-secondary inline-flex items-center px-4 py-2 rounded-apple text-sm font-medium transition-apple">
                    <i class="fas fa-print mr-2"></i>
                    Print
                </button>
                <form action="{{ route('admin.inbox.mark-unread', $email->id) }}" 
                      method="POST" 
                      class="inline-block">
                    @csrf
                    <button type="submit" 
                            class="btn-apple-secondary inline-flex items-center px-4 py-2 rounded-apple text-sm font-medium transition-apple">
                        <i class="fas fa-envelope mr-2"></i>
                        Mark as Unread
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Thread / Replies -->
    @if($email->replies && $email->replies->count() > 0)
        <div class="mt-6">
            <h2 class="text-lg font-semibold text-dark-text-primary mb-4">
                <i class="fas fa-comments mr-2"></i>
                Replies ({{ $email->replies->count() }})
            </h2>
            
            <div class="space-y-3">
                @foreach($email->replies as $reply)
                    <div class="card-apple p-5">
                        <div class="flex items-start justify-between mb-3">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-purple-500 to-pink-600 flex items-center justify-center text-white font-semibold text-sm">
                                    {{ strtoupper(substr($reply->from_name ?? $reply->from_email, 0, 2)) }}
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-dark-text-primary">
                                        {{ $reply->from_name ?? $reply->from_email }}
                                    </p>
                                    <p class="text-xs text-dark-text-tertiary">
                                        {{ $reply->received_at->diffForHumans() }}
                                    </p>
                                </div>
                            </div>
                            <a href="{{ route('admin.inbox.show', $reply->id) }}" 
                               class="btn-apple-secondary inline-flex items-center px-3 py-1.5 rounded-apple text-xs font-medium transition-apple">
                                View
                            </a>
                        </div>
                        <div class="text-sm text-dark-text-secondary line-clamp-3">
                            {{ Str::limit(strip_tags($reply->body_html ?? $reply->body_text), 200) }}
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

<script>
function toggleStar(emailId, button) {
    fetch(`/admin/inbox/${emailId}/star`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const icon = button.querySelector('i');
            const buttonText = button.querySelector('span') || button.childNodes[2];
            
            if (data.starred) {
                icon.classList.add('text-yellow-500');
                if (buttonText && buttonText.nodeType === Node.TEXT_NODE) {
                    button.innerHTML = '<i class="fas fa-star mr-2 text-yellow-500"></i>Starred';
                }
            } else {
                icon.classList.remove('text-yellow-500');
                if (buttonText && buttonText.nodeType === Node.TEXT_NODE) {
                    button.innerHTML = '<i class="fas fa-star mr-2"></i>Star';
                }
            }
        }
    })
    .catch(error => {
        console.error('Error toggling star:', error);
    });
}
</script>

<style>
/* Email content styling */
.email-content p,
.email-content div,
.email-content span,
.email-content td,
.email-content th {
    color: var(--dark-text-primary) !important;
}

.email-content a {
    color: var(--apple-blue) !important;
    text-decoration: underline;
}

.email-content a:hover {
    color: #4da3ff !important;
}

.email-content img {
    max-width: 100%;
    height: auto;
    border-radius: 8px;
    margin: 1rem 0;
}

.email-content table {
    border-collapse: collapse;
    width: 100%;
    margin: 1rem 0;
}

.email-content table td,
.email-content table th {
    border: 1px solid rgba(255, 255, 255, 0.1);
    padding: 0.5rem;
}

.line-clamp-3 {
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

/* Print styles */
@media print {
    .btn, button, nav, aside, footer, [x-data] {
        display: none !important;
    }
    
    .card-apple {
        border: none !important;
        box-shadow: none !important;
        background: white !important;
    }
    
    .email-content {
        background: white !important;
        color: black !important;
    }
    
    .email-content * {
        color: black !important;
    }
}
</style>
@endsection
