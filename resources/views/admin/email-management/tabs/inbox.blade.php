<div class="space-y-4">
    {{-- Header Section --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h2 class="text-xl font-semibold text-white">Email Inbox</h2>
            <p class="text-sm" style="color: rgba(235,235,245,0.6);">
                Kelola dan lihat semua email masuk dari berbagai akun
            </p>
        </div>
        <div class="flex items-center gap-2">
            <button id="deleteSelectedBtn" class="btn-apple-sm px-4 py-2 hidden" style="background: rgba(255,69,58,0.2); color: rgba(255,69,58,1);" onclick="deleteSelected()">
                <i class="fas fa-trash mr-2"></i>Delete Selected (<span id="selectedCount">0</span>)
            </button>
            <button class="btn-apple-sm px-4 py-2" onclick="location.reload()">
                <i class="fas fa-sync-alt mr-2"></i>Refresh
            </button>
            <a href="{{ route('admin.inbox.compose') ?? '#' }}" class="btn-apple-primary-sm px-4 py-2">
                <i class="fas fa-plus mr-2"></i>Compose Email
            </a>
        </div>
    </div>

    {{-- Filters --}}
    <div class="flex flex-wrap items-center gap-3">
        <label class="flex items-center gap-2 cursor-pointer">
            <input type="checkbox" id="selectAll" class="rounded text-apple-blue focus:ring-apple-blue" onchange="toggleSelectAll()">
            <span class="text-sm text-dark-text-secondary">Select All</span>
        </label>
        <div class="flex-1 min-w-[200px]">
            <input type="text" placeholder="Search emails..." 
                   class="input-apple w-full" value="{{ request('search') }}">
        </div>
        <select class="input-apple min-w-[150px]">
            <option value="">All Folders</option>
            <option value="inbox" {{ request('folder') == 'inbox' ? 'selected' : '' }}>Inbox</option>
            <option value="sent" {{ request('folder') == 'sent' ? 'selected' : '' }}>Sent</option>
            <option value="starred" {{ request('folder') == 'starred' ? 'selected' : '' }}>Starred</option>
            <option value="trash" {{ request('folder') == 'trash' ? 'selected' : '' }}>Trash</option>
        </select>
        <select class="input-apple min-w-[150px]">
            <option value="">All Status</option>
            <option value="unread">Unread</option>
            <option value="read">Read</option>
        </select>
    </div>

    <form id="batchDeleteForm" action="{{ route('admin.inbox.batch-delete') ?? '#' }}" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>

    {{-- Email List --}}
    @if(isset($emails) && $emails->count() > 0)
        <div class="space-y-2">
            @foreach($emails as $email)
                @php
                    // Clean email address from bounce/tracking prefixes
                    $cleanEmail = $email->from_email;
                    if (preg_match('/^[^@]+\+[^@]+=([^@]+@[^@]+)$/', $cleanEmail, $matches)) {
                        $cleanEmail = str_replace('=', '@', $matches[1]);
                    }
                    $displayName = $email->from_name ?: $cleanEmail;
                @endphp
                <div class="card-elevated rounded-apple-lg p-4 hover:bg-opacity-80 transition-apple" data-email-id="{{ $email->id }}">
                    <div class="flex items-start gap-4">
                        {{-- Checkbox --}}
                        <div class="flex-shrink-0 pt-1">
                            <input type="checkbox" class="email-checkbox rounded text-apple-blue focus:ring-apple-blue" 
                                   value="{{ $email->id }}" 
                                   onchange="updateSelectedCount()" 
                                   onclick="event.stopPropagation()">
                        </div>

                        {{-- Avatar --}}
                        <div class="flex-shrink-0 cursor-pointer" onclick="window.location='{{ route('admin.inbox.show', $email->id) ?? '#' }}'">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center text-white font-semibold"
                                 style="background: linear-gradient(135deg, rgba(10,132,255,0.8), rgba(30,86,172,0.8));">
                                {{ strtoupper(substr($displayName, 0, 1)) }}
                            </div>
                        </div>

                        {{-- Email Content --}}
                        <div class="flex-1 min-w-0 cursor-pointer" onclick="window.location='{{ route('admin.inbox.show', $email->id) ?? '#' }}'">
                            <div class="flex items-center justify-between gap-2 mb-1">
                                <div class="flex items-center gap-2 truncate">
                                    <h3 class="font-semibold text-white truncate">
                                        {{ $displayName }}
                                    </h3>
                                    @if($cleanEmail !== $displayName)
                                        <span class="text-xs text-dark-text-tertiary truncate">&lt;{{ $cleanEmail }}&gt;</span>
                                    @endif
                                    @if(!$email->is_read)
                                        <span class="w-2 h-2 rounded-full inline-block flex-shrink-0" style="background: var(--neuro-primary);"></span>
                                    @endif
                                </div>
                                <div class="flex items-center gap-2 flex-shrink-0">
                                    @if($email->is_starred)
                                        <i class="fas fa-star" style="color: var(--neuro-warning);"></i>
                                    @endif
                                    @if($email->attachments && count($email->attachments) > 0)
                                        <i class="fas fa-paperclip" style="color: rgba(235,235,245,0.6);"></i>
                                    @endif
                                    <span class="text-xs" style="color: rgba(235,235,245,0.5);">
                                        {{ $email->received_at?->diffForHumans() }}
                                    </span>
                                </div>
                            </div>
                            
                            <p class="text-sm font-medium mb-1" style="color: rgba(235,235,245,0.8);">
                                {{ $email->subject }}
                            </p>
                            
                            <p class="text-sm line-clamp-2" style="color: rgba(235,235,245,0.6);">
                                {{ $email->preview ?? Str::limit(strip_tags($email->body_text ?? ''), 100) }}
                            </p>

                            {{-- Tags --}}
                            @if($email->tags && count($email->tags) > 0)
                                <div class="flex flex-wrap gap-1 mt-2">
                                    @foreach($email->tags as $tag)
                                        <span class="px-2 py-0.5 text-xs rounded-full" 
                                              style="background: rgba(90,200,250,0.15); color: rgba(90,200,250,1);">
                                            {{ $tag }}
                                        </span>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Pagination --}}
        @if(method_exists($emails, 'hasPages') && $emails->hasPages())
            <div class="mt-6">
                {{ $emails->appends(request()->query())->links() }}
            </div>
        @endif
    @else
        <div class="text-center py-12">
            <i class="fas fa-inbox text-5xl mb-4" style="color: rgba(235,235,245,0.3);"></i>
            <p class="text-lg font-medium text-white mb-2">No Emails Found</p>
            <p class="text-sm" style="color: rgba(235,235,245,0.6);">
                Your inbox is empty or no emails match your filters
            </p>
        </div>
    @endif
</div>

@push('scripts')
<script>
function toggleSelectAll() {
    const selectAll = document.getElementById('selectAll');
    const checkboxes = document.querySelectorAll('.email-checkbox');
    checkboxes.forEach(cb => cb.checked = selectAll.checked);
    updateSelectedCount();
}

function updateSelectedCount() {
    const checkboxes = document.querySelectorAll('.email-checkbox:checked');
    const count = checkboxes.length;
    const deleteBtn = document.getElementById('deleteSelectedBtn');
    const countSpan = document.getElementById('selectedCount');
    
    countSpan.textContent = count;
    if (count > 0) {
        deleteBtn.classList.remove('hidden');
    } else {
        deleteBtn.classList.add('hidden');
    }
    
    // Update select all checkbox state
    const allCheckboxes = document.querySelectorAll('.email-checkbox');
    const selectAll = document.getElementById('selectAll');
    selectAll.checked = allCheckboxes.length > 0 && checkboxes.length === allCheckboxes.length;
}

function deleteSelected() {
    const checkboxes = document.querySelectorAll('.email-checkbox:checked');
    if (checkboxes.length === 0) return;
    
    if (!confirm(`Yakin ingin menghapus ${checkboxes.length} email yang dipilih?`)) {
        return;
    }
    
    const form = document.getElementById('batchDeleteForm');
    
    // Add selected email IDs to form
    checkboxes.forEach((cb, index) => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'email_ids[]';
        input.value = cb.value;
        form.appendChild(input);
    });
    
    form.submit();
}
</script>
@endpush
