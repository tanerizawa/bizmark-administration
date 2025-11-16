<!-- Notification Dropdown Component -->
<div 
    x-show="open"
    x-cloak
    x-transition:enter="transition ease-out duration-200"
    x-transition:enter-start="opacity-0 scale-95"
    x-transition:enter-end="opacity-100 scale-100"
    x-transition:leave="transition ease-in duration-150"
    x-transition:leave-start="opacity-100 scale-100"
    x-transition:leave-end="opacity-0 scale-95"
    role="menu"
    aria-label="Notifikasi terbaru"
    class="floating-panel floating-panel--notifications absolute right-0 mt-3 w-80 bg-white border border-gray-200 rounded-xl shadow-2xl z-50 max-h-96 overflow-hidden"
    style="top: 100%;"
>
    <!-- Header -->
    <div class="px-4 py-3 border-b border-gray-100 bg-gradient-to-r from-indigo-50 to-blue-50">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-semibold text-gray-800">Notifikasi</p>
                <p class="text-xs text-gray-500">{{ $notificationCount }} belum dibaca</p>
            </div>
            @if($notificationCount > 0)
            <form method="POST" action="{{ route('client.notifications.read-all') }}" class="inline">
                @csrf
                <button type="submit" class="text-xs text-indigo-600 hover:text-indigo-800 font-medium">
                    Tandai Semua
                </button>
            </form>
            @endif
        </div>
    </div>
    
    <!-- Notification List -->
    <div class="overflow-y-auto max-h-80">
        @forelse($recentNotifications as $notification)
        <a 
            href="{{ route('client.applications.show', $notification->application_id) }}" 
            class="block px-4 py-3 hover:bg-gray-50 border-b border-gray-100 transition-colors"
            @click="open = false"
        >
            <div class="flex gap-3">
                <div class="flex-shrink-0">
                    <div class="w-10 h-10 bg-indigo-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-file-alt text-indigo-600 text-sm"></i>
                    </div>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-medium text-gray-900 mb-0.5">
                        {{ $notification->application->application_number }}
                    </p>
                    <p class="text-xs text-gray-600 line-clamp-2">
                        {{ Str::limit($notification->notes ?? $notification->note, 60) }}
                    </p>
                    <p class="text-xs text-gray-400 mt-1">
                        {{ $notification->created_at->diffForHumans() }}
                    </p>
                </div>
                @if(!$notification->is_read)
                <div class="flex-shrink-0">
                    <span class="w-2 h-2 bg-indigo-600 rounded-full block"></span>
                </div>
                @endif
            </div>
        </a>
        @empty
        <div class="px-4 py-8 text-center">
            <i class="fas fa-bell-slash text-4xl text-gray-300 mb-2"></i>
            <p class="text-sm text-gray-500">Tidak ada notifikasi</p>
        </div>
        @endforelse
    </div>
    
    <!-- Footer -->
    @if($recentNotifications->count() > 0)
    <div class="px-4 py-2 bg-gray-50 border-t border-gray-100">
        <a href="{{ route('client.applications.index') }}" class="text-xs text-indigo-600 hover:text-indigo-800 font-medium block text-center">
            Lihat Semua Permohonan →
        </a>
    </div>
    @endif
</div>
