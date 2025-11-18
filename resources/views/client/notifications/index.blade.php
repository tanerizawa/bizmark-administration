@extends('client.layouts.app')

@section('title', 'Notifikasi')

@section('content')
<div class="max-w-4xl mx-auto space-y-1">
    <!-- Header -->
    <div class="border-y border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 px-4 lg:px-6 py-4">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Notifikasi</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Lihat semua notifikasi dan update terkait aplikasi Anda</p>
            </div>
            
            @if($unreadCount > 0)
            <form method="POST" action="{{ route('client.notifications.read-all') }}">
                @csrf
                <button 
                    type="submit"
                    class="px-4 py-2 bg-[#0a66c2] text-white text-sm font-medium hover:bg-[#004182] active:scale-95 transition-all focus:outline-none focus-visible:ring-2 focus-visible:ring-[#0a66c2]"
                >
                    <i class="fas fa-check-double mr-2"></i>
                    Tandai Semua Dibaca
                </button>
            </form>
            @endif
        </div>
    </div>

    <!-- Notifications List -->
    <div class="border-y border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 overflow-hidden">
        @forelse($notifications as $notification)
        <div class="flex items-start gap-4 px-4 lg:px-6 py-5 border-b border-gray-100 dark:border-gray-800 last:border-b-0 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors {{ $notification->is_read ? '' : 'bg-[#0a66c2]/5' }}">
            <!-- Icon -->
            <div class="flex-shrink-0">
                <div class="w-10 h-10 rounded-full {{ $notification->is_read ? 'bg-gray-100 dark:bg-gray-800 text-gray-500 dark:text-gray-400' : 'bg-[#0a66c2]/10 text-[#0a66c2]' }} flex items-center justify-center">
                    <i class="fas fa-{{ $notification->is_read ? 'envelope-open' : 'envelope' }} text-lg"></i>
                </div>
            </div>

            <!-- Content -->
            <div class="flex-1 min-w-0">
                <div class="flex items-start justify-between gap-3 mb-1">
                    <h3 class="text-sm font-semibold text-gray-900 dark:text-white">
                        Update: {{ $notification->application->service->name ?? 'Aplikasi Anda' }}
                    </h3>
                    @if(!$notification->is_read)
                    <span class="flex-shrink-0 px-2 py-0.5 bg-[#0a66c2] text-white text-xs font-semibold rounded-full">
                        Baru
                    </span>
                    @endif
                </div>
                
                <p class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed mb-2">
                    {{ Str::limit($notification->note_text, 150) }}
                </p>
                
                <div class="flex items-center gap-4 text-xs text-gray-500 dark:text-gray-400">
                    <span>
                        <i class="far fa-clock mr-1"></i>
                        {{ $notification->created_at->diffForHumans() }}
                    </span>
                    @if($notification->user)
                    <span>
                        <i class="fas fa-user mr-1"></i>
                        {{ $notification->user->name }}
                    </span>
                    @endif
                    <a 
                        href="{{ route('client.applications.show', $notification->application_id) }}" 
                        class="text-[#0a66c2] hover:text-[#004182] font-medium active:scale-95 inline-block transition-all"
                    >
                        Lihat Detail <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
            </div>
        </div>
        @empty
        <div class="px-4 lg:px-6 py-16 text-center">
            <div class="w-16 h-16 mx-auto mb-4 bg-gray-100 dark:bg-gray-800 rounded-full flex items-center justify-center">
                <i class="fas fa-bell-slash text-2xl text-gray-400"></i>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Belum Ada Notifikasi</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400">Notifikasi akan muncul di sini ketika ada update terkait aplikasi Anda.</p>
        </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($notifications->hasPages())
    <div class="border-y border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 px-4 py-4">
        {{ $notifications->links() }}
    </div>
    @endif
</div>
@endsection
