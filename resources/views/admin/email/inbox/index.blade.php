@extends('layouts.app')

@section('title', 'Email Inbox')
@section('page-title', 'Email Inbox')

@section('content')
@php
    $categoryTabs = [
        'inbox' => ['label' => 'Inbox', 'icon' => 'inbox', 'count' => $stats['unread'] ?? 0, 'badge' => true],
        'sent' => ['label' => 'Sent', 'icon' => 'paper-plane'],
    ];
@endphp

<div class="max-w-7xl mx-auto space-y-10">
    {{-- Hero --}}
    <section class="card-elevated rounded-apple-xl p-5 md:p-6 relative overflow-hidden">
        <div class="absolute inset-0 pointer-events-none" aria-hidden="true">
            <div class="w-72 h-72 bg-apple-blue opacity-30 blur-3xl rounded-full absolute -top-16 -right-10"></div>
            <div class="w-48 h-48 bg-apple-green opacity-20 blur-2xl rounded-full absolute bottom-0 left-10"></div>
        </div>
        <div class="relative flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
            <div class="space-y-3">
                <p class="text-xs uppercase tracking-[0.4em]" style="color: rgba(235,235,245,0.5);">Communication Ops</p>
                <h1 class="text-2xl md:text-3xl font-bold text-white">Email Inbox Bizmark.id</h1>
                <p class="text-sm md:text-base" style="color: rgba(235,235,245,0.7);">
                    Pantau pesan masuk, balasan, dan prioritas tim dengan gaya mission control.
                </p>
                <div class="flex flex-wrap gap-3 text-xs" style="color: rgba(235,235,245,0.6);">
                    <span><i class="fas fa-envelope-open-text mr-2"></i>{{ $stats['total'] ?? 0 }} total email</span>
                    <span><i class="fas fa-circle mr-2"></i>{{ $stats['unread'] ?? 0 }} belum dibaca</span>
                    <span><i class="fas fa-star mr-2"></i>{{ $stats['starred'] ?? 0 }} tersimpan</span>
                </div>
            </div>
            <div class="flex flex-col items-start gap-3">
                <a href="{{ route('admin.inbox.compose') }}" class="btn-primary">
                    <i class="fas fa-plus mr-2"></i>Tulis Email
                </a>
                <a href="{{ route('admin.inbox.compose') }}" class="text-xs font-semibold" style="color: rgba(235,235,245,0.7);">
                    Gunakan template balasan →
                </a>
            </div>
        </div>
    </section>

    {{-- Stats --}}
    <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="card-elevated rounded-apple-lg p-4">
            <p class="text-xs uppercase tracking-widest" style="color: rgba(10,132,255,0.9);">Total Email</p>
            <p class="text-3xl font-bold text-white">{{ $stats['total'] ?? 0 }}</p>
            <p class="text-xs" style="color: rgba(235,235,245,0.6);">{{ $stats['inbox'] ?? 0 }} berada di inbox</p>
        </div>
        <div class="card-elevated rounded-apple-lg p-4">
            <p class="text-xs uppercase tracking-widest" style="color: rgba(255,59,48,0.9);">Belum Dibaca</p>
            <p class="text-3xl font-bold text-white">{{ $stats['unread'] ?? 0 }}</p>
            <p class="text-xs" style="color: rgba(235,235,245,0.6);">Butuh tindak lanjut</p>
        </div>
        <div class="card-elevated rounded-apple-lg p-4">
            <p class="text-xs uppercase tracking-widest" style="color: rgba(255,214,10,0.9);">Starred</p>
            <p class="text-3xl font-bold text-white">{{ $stats['starred'] ?? 0 }}</p>
            <p class="text-xs" style="color: rgba(235,235,245,0.6);">Pesan prioritas</p>
        </div>
        <div class="card-elevated rounded-apple-lg p-4">
            <p class="text-xs uppercase tracking-widest" style="color: rgba(52,199,89,0.9);">Email Terkirim</p>
            <p class="text-3xl font-bold text-white">{{ $stats['sent'] ?? 0 }}</p>
            <p class="text-xs" style="color: rgba(235,235,245,0.6);">Aktivitas komunikasi keluar</p>
        </div>
    </section>

    {{-- Category tabs --}}
    <section class="card-elevated rounded-apple-xl p-4">
        <div class="flex flex-wrap gap-2">
            @foreach($categoryTabs as $key => $tab)
                @php
                    $isActive = $category === $key;
                @endphp
                <a href="{{ route('admin.inbox.index', array_merge(['category' => $key], request()->except('category'))) }}"
                   class="category-pill {{ $isActive ? 'active' : '' }}">
                    <i class="fas fa-{{ $tab['icon'] }} mr-2"></i>{{ $tab['label'] }}
                    @if(!empty($tab['badge']) && ($tab['count'] ?? 0) > 0)
                        <span>{{ $tab['count'] }}</span>
                    @endif
                </a>
            @endforeach
            <a href="{{ route('admin.inbox.index', array_merge(['is_starred' => 1], request()->except(['is_starred', 'category']))) }}"
               class="category-pill {{ request('is_starred') ? 'active' : '' }}">
                <i class="fas fa-star mr-2"></i>Starred
            </a>
        </div>
    </section>

    {{-- Filters --}}
    <section class="card-elevated rounded-apple-xl p-5 md:p-6 space-y-4">
        <div class="flex items-center justify-between flex-wrap gap-3">
            <div>
                <p class="text-xs uppercase tracking-[0.35em]" style="color: rgba(235,235,245,0.5);">Filter</p>
                <h2 class="text-lg font-semibold text-white">Cari email</h2>
            </div>
            <p class="text-xs" style="color: rgba(235,235,245,0.6);">{{ $emails->total() }} hasil ditemukan</p>
        </div>
        <form method="GET" action="{{ route('admin.inbox.index', ['category' => $category]) }}">
            <div class="flex flex-col gap-3 md:flex-row md:items-end">
                <div class="flex-1">
                    <label class="text-xs uppercase tracking-widest mb-2 block" style="color: rgba(235,235,245,0.6);">Cari</label>
                    <div class="flex">
                        <span class="inline-flex items-center px-3 rounded-l-apple" style="background: rgba(28,28,30,0.6); border: 1px solid rgba(84,84,88,0.35); border-right: none; color: rgba(235,235,245,0.6);">
                            <i class="fas fa-search"></i>
                        </span>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Subject, pengirim, isi"
                               class="w-full px-4 py-2.5 rounded-r-apple text-sm text-white placeholder-gray-500"
                               style="background: rgba(28,28,30,0.6); border: 1px solid rgba(84,84,88,0.35); border-left: none;">
                    </div>
                </div>
                <div class="flex-1">
                    <label class="text-xs uppercase tracking-widest mb-2 block" style="color: rgba(235,235,245,0.6);">Status</label>
                    <select name="is_read" class="w-full px-4 py-2.5 rounded-apple text-sm text-white"
                            style="background: rgba(28,28,30,0.6); border: 1px solid rgba(84,84,88,0.35);">
                        <option value="">Semua status</option>
                        <option value="0" {{ request('is_read') === '0' ? 'selected' : '' }}>Unread</option>
                        <option value="1" {{ request('is_read') === '1' ? 'selected' : '' }}>Read</option>
                    </select>
                </div>
                <div class="flex-1">
                    <label class="text-xs uppercase tracking-widest mb-2 block" style="color: rgba(235,235,245,0.6);">Akun email</label>
                    <select name="to_email" class="w-full px-4 py-2.5 rounded-apple text-sm text-white"
                            style="background: rgba(28,28,30,0.6); border: 1px solid rgba(84,84,88,0.35);">
                        <option value="">Semua akun</option>
                        @foreach(\App\Models\EmailAccount::all() as $account)
                            <option value="{{ $account->email }}" {{ request('to_email') === $account->email ? 'selected' : '' }}>
                                {{ $account->email }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="flex gap-3">
                    <button type="submit" class="btn-primary-sm">
                        <i class="fas fa-search mr-2"></i>Cari
                    </button>
                    <a href="{{ route('admin.inbox.index', ['category' => $category]) }}" class="btn-secondary-sm text-center">
                        <i class="fas fa-redo mr-2"></i>Reset
                    </a>
                </div>
            </div>
        </form>
    </section>

    {{-- Email list --}}
    <section class="card-elevated rounded-apple-xl overflow-hidden">
        <div class="px-6 py-3 border-b border-white/5 flex items-center justify-between text-sm" style="color: rgba(235,235,245,0.6); background: rgba(28,28,30,0.4);">
            <span>{{ $emails->total() }} email ditemukan</span>
            <span>Menampilkan {{ $emails->firstItem() ?? 0 }} - {{ $emails->lastItem() ?? 0 }}</span>
        </div>
        <div class="divide-y divide-white/5">
            @forelse($emails as $email)
                <div class="email-item px-6 py-4 hover:bg-white/5 transition-colors cursor-pointer {{ !$email->is_read ? 'bg-white/3' : '' }}"
                     onclick="window.location='{{ route('admin.inbox.show', $email) }}'">
                    <div class="flex items-start gap-4">
                        <button type="button"
                                onclick="event.stopPropagation(); toggleStar({{ $email->id }}, this)"
                                class="mt-1 star-button {{ $email->is_starred ? 'active' : '' }}">
                            <i class="fas fa-star"></i>
                        </button>
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 rounded-full bg-gradient-to-br from-apple-blue to-purple-600 flex items-center justify-center text-white font-semibold text-sm">
                                {{ strtoupper(substr(($category === 'sent' ? $email->to_email : ($email->from_name ?? $email->from_email)), 0, 2)) }}
                            </div>
                        </div>
                        <div class="flex-grow min-w-0">
                            <div class="flex items-start justify-between gap-3">
                                <div class="flex items-center gap-2 min-w-0">
                                    <p class="text-sm font-{{ !$email->is_read ? 'semibold' : 'medium' }} text-white truncate">
                                        {{ $category === 'sent' ? $email->to_email : ($email->from_name ?? $email->from_email) }}
                                    </p>
                                    @if(!$email->is_read && $category !== 'sent')
                                        <span class="inline-flex items-center px-2 py-0.5 text-[10px] font-semibold rounded-full bg-apple-blue text-white">NEW</span>
                                    @endif
                                    @if($email->has_attachments)
                                        <i class="fas fa-paperclip text-dark-text-tertiary text-xs"></i>
                                    @endif
                                </div>
                                <span class="text-xs" style="color: rgba(235,235,245,0.6);">
                                    @if($email->received_at->isToday())
                                        {{ $email->received_at->format('H:i') }}
                                    @elseif($email->received_at->isYesterday())
                                        Yesterday
                                    @elseif($email->received_at->diffInDays() < 7)
                                        {{ $email->received_at->format('l') }}
                                    @else
                                        {{ $email->received_at->format('d M Y') }}
                                    @endif
                                </span>
                            </div>
                            <h3 class="text-sm font-{{ !$email->is_read ? 'semibold' : 'normal' }} text-dark-text-{{ !$email->is_read ? 'primary' : 'secondary' }} mb-1 truncate">
                                {{ $email->subject }}
                            </h3>
                            <p class="text-xs text-dark-text-tertiary line-clamp-2 mb-2">
                                {{ Str::limit(strip_tags($email->body_text ?? $email->body_html), 160) }}
                            </p>
                            @if($email->emailAccount)
                                <span class="inline-flex items-center px-2 py-1 text-xs rounded-apple bg-dark-bg-tertiary text-dark-text-secondary">
                                    <i class="fas fa-at mr-1"></i>{{ $email->emailAccount->email }}
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-16 space-y-2">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-dark-bg-tertiary mb-4">
                        <i class="fas fa-inbox text-3xl text-dark-text-tertiary"></i>
                    </div>
                    <p class="text-sm" style="color: rgba(235,235,245,0.6);">
                        @if(request('search'))
                            Tidak ada email sesuai pencarian Anda.
                        @else
                            Inbox sedang kosong.
                        @endif
                    </p>
                </div>
            @endforelse
        </div>
        @if($emails->hasPages())
            <div class="px-6 py-4 border-t border-white/5 flex items-center justify-between text-sm" style="color: rgba(235,235,245,0.6);">
                <div>Menampilkan {{ $emails->firstItem() ?? 0 }} - {{ $emails->lastItem() ?? 0 }} dari {{ $emails->total() }} email</div>
                <div class="space-x-2">
                    @if($emails->onFirstPage())
                        <span class="px-4 py-2 rounded-apple bg-dark-bg-tertiary text-dark-text-tertiary cursor-not-allowed">
                            <i class="fas fa-chevron-left mr-2"></i>Sebelumnya
                        </span>
                    @else
                        <a href="{{ $emails->previousPageUrl() }}" class="px-4 py-2 rounded-apple bg-dark-bg-tertiary text-dark-text-secondary hover:bg-dark-bg-quaternary transition-colors">
                            <i class="fas fa-chevron-left mr-2"></i>Sebelumnya
                        </a>
                    @endif
                    @if($emails->hasMorePages())
                        <a href="{{ $emails->nextPageUrl() }}" class="px-4 py-2 rounded-apple bg-dark-bg-tertiary text-dark-text-secondary hover:bg-dark-bg-quaternary transition-colors">
                            Berikutnya<i class="fas fa-chevron-right ml-2"></i>
                        </a>
                    @else
                        <span class="px-4 py-2 rounded-apple bg-dark-bg-tertiary text-dark-text-tertiary cursor-not-allowed">
                            Berikutnya<i class="fas fa-chevron-right ml-2"></i>
                        </span>
                    @endif
                </div>
            </div>
        @endif
    </section>
</div>

<style>
.category-pill {
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    padding: 0.45rem 0.9rem;
    border-radius: 999px;
    border: 1px solid rgba(84,84,88,0.35);
    background: rgba(28,28,30,0.4);
    color: rgba(235,235,245,0.7);
    font-size: 0.8rem;
    font-weight: 600;
    transition: all 0.2s;
}
.category-pill span {
    padding: 0.1rem 0.45rem;
    border-radius: 999px;
    background: rgba(255,255,255,0.1);
    font-size: 0.7rem;
}
.category-pill.active {
    border-color: rgba(10,132,255,0.5);
    background: rgba(10,132,255,0.15);
    color: rgba(10,132,255,0.95);
}
.star-button {
    color: rgba(235,235,245,0.45);
    transition: color 0.2s ease;
}
.star-button.active {
    color: rgba(255,214,10,1);
}
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
.email-item::before {
    content: '';
    position: absolute;
    left: 0;
    top: 0;
    bottom: 0;
    width: 3px;
    background: rgba(10,132,255,0.6);
    opacity: 0;
    transition: opacity 0.2s ease;
}
.email-item:hover::before {
    opacity: 1;
}
</style>

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
            button.classList.toggle('active');
        }
    })
    .catch(console.error);
}
</script>

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
