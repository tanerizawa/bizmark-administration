@extends('client.layouts.app')

@section('title', 'Notifikasi')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Header -->
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Notifikasi</h1>
            <p class="text-sm text-gray-500 mt-1">Lihat semua notifikasi dan update terkait aplikasi Anda</p>
        </div>
        
        @if($unreadCount > 0)
        <form method="POST" action="{{ route('client.notifications.read-all') }}">
            @csrf
            <button 
                type="submit"
                class="px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500"
            >
                <i class="fas fa-check-double mr-2"></i>
                Tandai Semua Dibaca
            </button>
        </form>
        @endif
    </div>

    @if(session('success'))
    <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg flex items-start gap-3">
        <i class="fas fa-check-circle text-green-600 mt-0.5"></i>
        <p class="text-sm text-green-800">{{ session('success') }}</p>
    </div>
    @endif

    <!-- Notifications List -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        @forelse($notifications as $notification)
        <div class="flex items-start gap-4 p-5 border-b border-gray-100 hover:bg-gray-50 transition-colors {{ $notification->is_read ? '' : 'bg-indigo-50/30' }}">
            <!-- Icon -->
            <div class="flex-shrink-0">
                <div class="w-10 h-10 rounded-full {{ $notification->is_read ? 'bg-gray-100 text-gray-500' : 'bg-indigo-100 text-indigo-600' }} flex items-center justify-center">
                    <i class="fas fa-{{ $notification->is_read ? 'envelope-open' : 'envelope' }} text-lg"></i>
                </div>
            </div>

            <!-- Content -->
            <div class="flex-1 min-w-0">
                <div class="flex items-start justify-between gap-3 mb-1">
                    <h3 class="text-sm font-semibold text-gray-900">
                        Update: {{ $notification->application->service->name ?? 'Aplikasi Anda' }}
                    </h3>
                    @if(!$notification->is_read)
                    <span class="flex-shrink-0 px-2 py-0.5 bg-indigo-600 text-white text-xs font-semibold rounded-full">
                        Baru
                    </span>
                    @endif
                </div>
                
                <p class="text-sm text-gray-700 leading-relaxed mb-2">
                    {{ Str::limit($notification->note_text, 150) }}
                </p>
                
                <div class="flex items-center gap-4 text-xs text-gray-500">
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
                        class="text-indigo-600 hover:text-indigo-700 font-medium"
                    >
                        Lihat Detail <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
            </div>
        </div>
        @empty
        <div class="p-12 text-center">
            <div class="w-16 h-16 mx-auto mb-4 bg-gray-100 rounded-full flex items-center justify-center">
                <i class="fas fa-bell-slash text-2xl text-gray-400"></i>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Belum Ada Notifikasi</h3>
            <p class="text-sm text-gray-500">Notifikasi akan muncul di sini ketika ada update terkait aplikasi Anda.</p>
        </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($notifications->hasPages())
    <div class="mt-6">
        {{ $notifications->links() }}
    </div>
    @endif
</div>
@endsection
