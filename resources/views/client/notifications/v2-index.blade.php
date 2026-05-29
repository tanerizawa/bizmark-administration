{{-- Notifications Index — Portal v2 --}}

{{-- ─── HEADER BAR ─── --}}
<div class="sticky top-0 z-20 bg-[var(--surface-elevated)]/95 backdrop-blur border-b border-[var(--border-subtle)]">
    <div class="max-w-[1400px] mx-auto px-4 lg:px-8 py-3 flex items-center justify-between gap-4">
        <div class="flex items-center gap-2">
            <h1 class="text-base font-bold text-[var(--text-primary)]">Notifikasi</h1>
            @if($unreadCount > 0)
            <span class="text-[10px] font-bold px-2 py-0.5 bg-[var(--client-primary)] text-white rounded-full tabular-nums">
                {{ $unreadCount }}
            </span>
            @endif
        </div>
        @if($unreadCount > 0)
        <form method="POST" action="{{ route('client.notifications.mark-all-read') }}">
            @csrf @method('PATCH')
            <button type="submit"
                    class="inline-flex items-center gap-1.5 text-xs font-semibold text-[var(--client-primary)] hover:underline transition-colors">
                <i class="fas fa-check-double text-[10px]" aria-hidden="true"></i>
                Tandai semua dibaca
            </button>
        </form>
        @endif
    </div>
</div>

{{-- ─── LIST ─── --}}
<div class="max-w-[1400px] mx-auto px-4 lg:px-8 py-5">
    @if($notifications->isEmpty())
    <x-ui.empty-state icon="fas fa-bell-slash" title="Belum ada notifikasi"
        description="Notifikasi status permohonan dan pesan dari tim akan muncul di sini." />
    @else
    <div class="bg-[var(--surface-elevated)] border border-[var(--border-subtle)] rounded-xl overflow-hidden">
        <ul class="divide-y divide-[var(--border-subtle)]">
            @foreach($notifications as $notif)
            @php
                $isUnread = !$notif->read_at;
                $appNum   = $notif->application?->application_number ?? '';
                $appId    = $notif->application_id ?? null;
            @endphp
            <li class="relative {{ $isUnread ? 'bg-[var(--client-primary)]/3 dark:bg-[var(--client-primary)]/5' : '' }} hover:bg-[var(--surface-cool)] transition-colors">
                @if($isUnread)
                <span class="absolute left-4 top-1/2 -translate-y-1/2 w-1.5 h-1.5 rounded-full bg-[var(--client-primary)]" aria-label="Belum dibaca"></span>
                @endif
                <div class="px-5 py-4 {{ $isUnread ? 'pl-8' : '' }}">
                    <div class="flex items-start justify-between gap-4">
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center gap-2 flex-wrap mb-1">
                                @if($appNum)
                                <span class="portal-mono text-[10px] text-[var(--client-primary)]">{{ $appNum }}</span>
                                @endif
                                @if($notif->user)
                                <span class="text-[10px] text-[var(--text-tertiary)]">
                                    <i class="fas fa-user-tie text-[8px]" aria-hidden="true"></i>
                                    Tim Bizmark
                                </span>
                                @endif
                            </div>
                            <p class="text-sm text-[var(--text-primary)] leading-snug">{{ $notif->note }}</p>
                        </div>
                        <div class="flex items-center gap-3 flex-shrink-0">
                            <time class="text-[10px] text-[var(--text-tertiary)]">{{ $notif->created_at->diffForHumans() }}</time>
                            @if($appId)
                            <a href="{{ route('client.applications.show', $appId) }}"
                               class="text-xs text-[var(--client-primary)] hover:underline font-semibold">
                                Lihat →
                            </a>
                            @endif
                        </div>
                    </div>
                </div>
            </li>
            @endforeach
        </ul>
    </div>

    {{-- Pagination --}}
    @if(method_exists($notifications, 'links') && $notifications->hasPages())
    <div class="mt-4">{{ $notifications->links() }}</div>
    @endif
    @endif
</div>
