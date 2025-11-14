@extends('layouts.app')

@section('title', 'Email Inbox')

@section('content')
<div class="container-fluid px-4 py-6">
    <!-- Header Section -->
    <div class="mb-6">
        <div class="flex items-center justify-between mb-2">
            <div>
                <h1 class="text-2xl font-semibold text-dark-text-primary mb-1">Email Inbox</h1>
                <p class="text-sm text-dark-text-secondary">Manage your incoming and outgoing emails</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('admin.inbox.compose') }}" 
                   class="btn-apple-blue inline-flex items-center px-4 py-2 rounded-apple text-sm font-medium transition-apple">
                    <i class="fas fa-plus mr-2"></i>
                    Compose Email
                </a>
            </div>
        </div>
    </div>

    <!-- Stats Dashboard -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <!-- Total Emails -->
        <div class="card-apple p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-dark-text-secondary uppercase tracking-wide mb-1">Total Emails</p>
                    <p class="text-2xl font-bold text-dark-text-primary">{{ $stats['total'] ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 rounded-full bg-apple-blue bg-opacity-10 flex items-center justify-center">
                    <i class="fas fa-envelope text-apple-blue text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Unread -->
        <div class="card-apple p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-dark-text-secondary uppercase tracking-wide mb-1">Unread</p>
                    <p class="text-2xl font-bold text-dark-text-primary">{{ $stats['unread'] ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 rounded-full bg-red-500 bg-opacity-10 flex items-center justify-center">
                    <i class="fas fa-circle text-red-500 text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Starred -->
        <div class="card-apple p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-dark-text-secondary uppercase tracking-wide mb-1">Starred</p>
                    <p class="text-2xl font-bold text-dark-text-primary">{{ $stats['starred'] ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 rounded-full bg-yellow-500 bg-opacity-10 flex items-center justify-center">
                    <i class="fas fa-star text-yellow-500 text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Sent -->
        <div class="card-apple p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-dark-text-secondary uppercase tracking-wide mb-1">Sent</p>
                    <p class="text-2xl font-bold text-dark-text-primary">{{ $stats['sent'] ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 rounded-full bg-green-500 bg-opacity-10 flex items-center justify-center">
                    <i class="fas fa-paper-plane text-green-500 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Tabs & Search -->
    <div class="card-apple p-4 mb-6">
        <!-- Category Tabs -->
        <div class="flex flex-wrap gap-2 mb-4 pb-4" style="border-bottom: 1px solid var(--dark-separator);">
            <a href="{{ route('admin.inbox.index', array_merge(['category' => 'inbox'], request()->except('category'))) }}" 
               class="inline-flex items-center px-4 py-2 rounded-apple text-sm font-medium transition-apple {{ $category === 'inbox' ? 'bg-apple-blue text-white' : 'text-dark-text-secondary hover:bg-dark-bg-tertiary hover:text-dark-text-primary' }}">
                <i class="fas fa-inbox mr-2"></i>
                Inbox
                @if(($stats['unread'] ?? 0) > 0)
                    <span class="ml-2 px-2 py-0.5 text-xs font-semibold rounded-full {{ $category === 'inbox' ? 'bg-white text-apple-blue' : 'bg-red-500 text-white' }}">
                        {{ $stats['unread'] }}
                    </span>
                @endif
            </a>
            
            <a href="{{ route('admin.inbox.index', array_merge(['category' => 'sent'], request()->except('category'))) }}" 
               class="inline-flex items-center px-4 py-2 rounded-apple text-sm font-medium transition-apple {{ $category === 'sent' ? 'bg-apple-blue text-white' : 'text-dark-text-secondary hover:bg-dark-bg-tertiary hover:text-dark-text-primary' }}">
                <i class="fas fa-paper-plane mr-2"></i>
                Sent
            </a>
            
            <a href="{{ route('admin.inbox.index', array_merge(['is_starred' => 1], request()->except(['category', 'is_starred']))) }}" 
               class="inline-flex items-center px-4 py-2 rounded-apple text-sm font-medium transition-apple {{ request('is_starred') ? 'bg-apple-blue text-white' : 'text-dark-text-secondary hover:bg-dark-bg-tertiary hover:text-dark-text-primary' }}">
                <i class="fas fa-star mr-2"></i>
                Starred
            </a>
            
            <a href="{{ route('admin.inbox.index', array_merge(['category' => 'trash'], request()->except('category'))) }}" 
               class="inline-flex items-center px-4 py-2 rounded-apple text-sm font-medium transition-apple {{ $category === 'trash' ? 'bg-apple-blue text-white' : 'text-dark-text-secondary hover:bg-dark-bg-tertiary hover:text-dark-text-primary' }}">
                <i class="fas fa-trash mr-2"></i>
                Trash
            </a>
        </div>

        <!-- Search & Filters -->
        <form action="{{ route('admin.inbox.index') }}" method="GET" class="space-y-4">
            <input type="hidden" name="category" value="{{ $category }}">
            @if(request('is_starred'))
                <input type="hidden" name="is_starred" value="1">
            @endif
            
            <div class="grid grid-cols-1 md:grid-cols-12 gap-3">
                <!-- Search Input -->
                <div class="md:col-span-5">
                    <div class="relative">
                        <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-dark-text-tertiary"></i>
                        <input type="text" 
                               name="search" 
                               value="{{ request('search') }}"
                               placeholder="Search emails by subject, sender, or content..." 
                               class="input-apple pl-10 w-full">
                    </div>
                </div>

                <!-- Read Status Filter -->
                <div class="md:col-span-2">
                    <select name="is_read" class="input-apple w-full">
                        <option value="">All Status</option>
                        <option value="0" {{ request('is_read') === '0' ? 'selected' : '' }}>Unread</option>
                        <option value="1" {{ request('is_read') === '1' ? 'selected' : '' }}>Read</option>
                    </select>
                </div>

                <!-- Email Account Filter -->
                <div class="md:col-span-3">
                    <select name="to_email" class="input-apple w-full">
                        <option value="">All Accounts</option>
                        @foreach(\App\Models\EmailAccount::all() as $account)
                            <option value="{{ $account->email }}" {{ request('to_email') === $account->email ? 'selected' : '' }}>
                                {{ $account->email }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Action Buttons -->
                <div class="md:col-span-2 flex gap-2">
                    <button type="submit" class="btn-apple-blue flex-1 inline-flex items-center justify-center px-4 py-2 rounded-apple text-sm font-medium transition-apple">
                        <i class="fas fa-search mr-2"></i>
                        Search
                    </button>
                    <a href="{{ route('admin.inbox.index', ['category' => $category]) }}" 
                       class="btn-apple-secondary flex-1 inline-flex items-center justify-center px-4 py-2 rounded-apple text-sm font-medium transition-apple">
                        <i class="fas fa-redo mr-2"></i>
                        Reset
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- Email List -->
    <div class="card-apple overflow-hidden">
        <!-- List Header -->
        <div class="px-6 py-3 bg-dark-bg-tertiary border-b border-dark-separator">
            <div class="flex items-center justify-between">
                <p class="text-sm font-medium text-dark-text-secondary">
                    {{ $emails->total() }} {{ Str::plural('email', $emails->total()) }} found
                </p>
                <div class="flex items-center gap-2">
                    <span class="text-xs text-dark-text-tertiary">
                        Showing {{ $emails->firstItem() ?? 0 }} - {{ $emails->lastItem() ?? 0 }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Email Items -->
        <div class="divide-y divide-dark-separator">
            @forelse($emails as $email)
                <div class="email-item px-6 py-4 hover:bg-dark-bg-tertiary transition-colors cursor-pointer {{ !$email->is_read ? 'bg-dark-bg-secondary' : '' }}"
                     onclick="window.location='{{ route('admin.inbox.show', $email) }}'">
                    <div class="flex items-start gap-4">
                        <!-- Star Button -->
                        <button type="button" 
                                onclick="event.stopPropagation(); toggleStar({{ $email->id }}, this)"
                                class="mt-1 text-{{ $email->is_starred ? 'yellow-500' : 'dark-text-tertiary' }} hover:text-yellow-500 transition-colors">
                            <i class="fas fa-star"></i>
                        </button>

                        <!-- Avatar -->
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 rounded-full bg-gradient-to-br from-apple-blue to-purple-600 flex items-center justify-center text-white font-semibold text-sm">
                                @if($category === 'sent')
                                    {{ strtoupper(substr($email->to_email, 0, 2)) }}
                                @else
                                    {{ strtoupper(substr($email->from_name ?? $email->from_email, 0, 2)) }}
                                @endif
                            </div>
                        </div>

                        <!-- Email Content -->
                        <div class="flex-grow min-w-0">
                            <div class="flex items-start justify-between mb-1">
                                <div class="flex items-center gap-2 min-w-0">
                                    <p class="text-sm font-{{ !$email->is_read ? 'semibold' : 'medium' }} text-dark-text-primary truncate">
                                        @if($category === 'sent')
                                            To: {{ $email->to_email }}
                                        @else
                                            {{ $email->from_name ?? $email->from_email }}
                                        @endif
                                    </p>
                                    
                                    @if(!$email->is_read && $category !== 'sent')
                                        <span class="flex-shrink-0 px-2 py-0.5 text-xs font-semibold rounded-full bg-apple-blue text-white">
                                            New
                                        </span>
                                    @endif

                                    @if($email->has_attachments)
                                        <i class="fas fa-paperclip text-dark-text-tertiary text-xs"></i>
                                    @endif
                                </div>

                                <span class="flex-shrink-0 text-xs text-dark-text-tertiary ml-4">
                                    @if($email->received_at->isToday())
                                        {{ $email->received_at->format('H:i') }}
                                    @elseif($email->received_at->isYesterday())
                                        Yesterday
                                    @elseif($email->received_at->diffInDays() < 7)
                                        {{ $email->received_at->format('l') }}
                                    @else
                                        {{ $email->received_at->format('M d') }}
                                    @endif
                                </span>
                            </div>

                            <h3 class="text-sm font-{{ !$email->is_read ? 'semibold' : 'normal' }} text-dark-text-{{ !$email->is_read ? 'primary' : 'secondary' }} mb-1 truncate">
                                {{ $email->subject }}
                            </h3>

                            <p class="text-xs text-dark-text-tertiary line-clamp-2">
                                {{ Str::limit(strip_tags($email->body_text ?? $email->body_html), 150) }}
                            </p>

                            <!-- Email Account Badge -->
                            @if($email->emailAccount)
                                <div class="mt-2">
                                    <span class="inline-flex items-center px-2 py-1 text-xs rounded-apple bg-dark-bg-tertiary text-dark-text-secondary">
                                        <i class="fas fa-at mr-1"></i>
                                        {{ $email->emailAccount->email }}
                                    </span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-16">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-dark-bg-tertiary mb-4">
                        <i class="fas fa-inbox text-3xl text-dark-text-tertiary"></i>
                    </div>
                    <h3 class="text-lg font-medium text-dark-text-primary mb-1">No emails found</h3>
                    <p class="text-sm text-dark-text-secondary">
                        @if(request('search'))
                            Try adjusting your search or filters
                        @else
                            Your inbox is empty
                        @endif
                    </p>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($emails->hasPages())
            <div class="px-6 py-4 border-t border-dark-separator">
                <div class="flex items-center justify-between">
                    <div class="text-sm text-dark-text-secondary">
                        Showing {{ $emails->firstItem() ?? 0 }} to {{ $emails->lastItem() ?? 0 }} of {{ $emails->total() }} results
                    </div>
                    <div class="flex gap-2">
                        @if($emails->onFirstPage())
                            <span class="px-4 py-2 rounded-apple text-sm font-medium text-dark-text-tertiary bg-dark-bg-tertiary cursor-not-allowed">
                                <i class="fas fa-chevron-left mr-2"></i>Previous
                            </span>
                        @else
                            <a href="{{ $emails->previousPageUrl() }}" 
                               class="px-4 py-2 rounded-apple text-sm font-medium text-dark-text-secondary bg-dark-bg-tertiary hover:bg-dark-bg-quaternary transition-colors">
                                <i class="fas fa-chevron-left mr-2"></i>Previous
                            </a>
                        @endif

                        @if($emails->hasMorePages())
                            <a href="{{ $emails->nextPageUrl() }}" 
                               class="px-4 py-2 rounded-apple text-sm font-medium text-dark-text-secondary bg-dark-bg-tertiary hover:bg-dark-bg-quaternary transition-colors">
                                Next<i class="fas fa-chevron-right ml-2"></i>
                            </a>
                        @else
                            <span class="px-4 py-2 rounded-apple text-sm font-medium text-dark-text-tertiary bg-dark-bg-tertiary cursor-not-allowed">
                                Next<i class="fas fa-chevron-right ml-2"></i>
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

<!-- Star Toggle Script -->
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
            // Toggle star color
            if (button.classList.contains('text-yellow-500')) {
                button.classList.remove('text-yellow-500');
                button.classList.add('text-dark-text-tertiary');
            } else {
                button.classList.remove('text-dark-text-tertiary');
                button.classList.add('text-yellow-500');
            }
        }
    })
    .catch(error => {
        console.error('Error toggling star:', error);
    });
}

// Optional: Mark as read when clicking email
document.querySelectorAll('.email-item').forEach(item => {
    item.addEventListener('click', function(e) {
        if (!e.target.closest('button')) {
            // Add visual feedback
            this.classList.remove('bg-dark-bg-secondary');
        }
    });
});
</script>

<style>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.email-item {
    position: relative;
}

.email-item::before {
    content: '';
    position: absolute;
    left: 0;
    top: 0;
    bottom: 0;
    width: 3px;
    background: var(--apple-blue);
    opacity: 0;
    transition: opacity 0.2s ease;
}

.email-item:hover::before {
    opacity: 1;
}
</style>
@endsection
