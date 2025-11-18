@extends('mobile.layouts.app')

@section('title', 'Notifikasi')

@section('header-actions')
<button @click="markAllAsRead" class="text-gray-600 hover:text-blue-600 transition-colors text-sm font-medium">
    <i class="fas fa-check-double mr-1"></i>
    Tandai Semua
</button>
@endsection

@section('content')
<div x-data="notificationCenter()" class="pb-20">
    {{-- Filter Tabs --}}
    <div class="sticky top-16 bg-white z-10 border-b border-gray-200 px-3 pt-2">
        <div class="flex gap-2 overflow-x-auto scrollbar-hide pb-2">
            <button 
                @click="filter = 'all'" 
                :class="filter === 'all' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-600'"
                class="px-4 py-2 rounded-lg font-medium text-sm whitespace-nowrap transition-all 
                       active:scale-95 flex items-center gap-2">
                <span>Semua</span>
                <span x-show="filter === 'all' && stats.all > 0" 
                      class="bg-white text-blue-600 px-2 py-0.5 rounded-full text-xs font-bold"
                      x-text="stats.all"></span>
            </button>
            <button 
                @click="filter = 'tasks'" 
                :class="filter === 'tasks' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-600'"
                class="px-4 py-2 rounded-lg font-medium text-sm whitespace-nowrap transition-all 
                       active:scale-95 flex items-center gap-2">
                <i class="fas fa-circle-check text-xs"></i>
                <span>Task</span>
                <span x-show="stats.tasks > 0" 
                      :class="filter === 'tasks' ? 'bg-white text-blue-600' : 'bg-blue-100 text-blue-600'"
                      class="px-2 py-0.5 rounded-full text-xs font-bold"
                      x-text="stats.tasks"></span>
            </button>
            <button 
                @click="filter = 'approvals'" 
                :class="filter === 'approvals' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-600'"
                class="px-4 py-2 rounded-lg font-medium text-sm whitespace-nowrap transition-all 
                       active:scale-95 flex items-center gap-2">
                <i class="fas fa-clipboard-check text-xs"></i>
                <span>Approval</span>
                <span x-show="stats.approvals > 0" 
                      :class="filter === 'approvals' ? 'bg-white text-blue-600' : 'bg-orange-100 text-orange-600'"
                      class="px-2 py-0.5 rounded-full text-xs font-bold"
                      x-text="stats.approvals"></span>
            </button>
            <button 
                @click="filter = 'cash'" 
                :class="filter === 'cash' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-600'"
                class="px-4 py-2 rounded-lg font-medium text-sm whitespace-nowrap transition-all 
                       active:scale-95 flex items-center gap-2">
                <i class="fas fa-money-bill-wave text-xs"></i>
                <span>Keuangan</span>
                <span x-show="stats.cash > 0" 
                      :class="filter === 'cash' ? 'bg-white text-blue-600' : 'bg-green-100 text-green-600'"
                      class="px-2 py-0.5 rounded-full text-xs font-bold"
                      x-text="stats.cash"></span>
            </button>
        </div>
    </div>

    {{-- Notifications List --}}
    <div class="divide-y divide-gray-100">
        <template x-if="loading">
            <div class="p-8 text-center">
                <i class="fas fa-spinner fa-spin text-gray-400 text-2xl"></i>
                <p class="text-sm text-gray-500 mt-2">Memuat notifikasi...</p>
            </div>
        </template>

        <template x-if="!loading && filteredNotifications.length === 0">
            <div class="p-8 text-center">
                <i class="fas fa-bell-slash text-gray-300 text-3xl mb-2"></i>
                <p class="text-gray-500 font-medium">Tidak ada notifikasi</p>
                <p class="text-sm text-gray-400 mt-1">Semua sudah dibaca</p>
            </div>
        </template>

        <template x-for="notif in filteredNotifications" :key="notif.id">
            <div 
                :class="!notif.read_at ? 'bg-blue-50' : 'bg-white'"
                class="p-3 hover:bg-gray-50 transition-colors relative border-b border-gray-100"
                @click="handleNotification(notif)">
                
                {{-- Unread Indicator --}}
                <div x-show="!notif.read_at" 
                     class="absolute left-1 top-4 w-2 h-2 bg-[#0077b5] rounded-full"></div>

                <div class="flex gap-2 ml-3">
                    {{-- Icon --}}
                    <div 
                        :class="{
                            'bg-gray-100 text-gray-600': notif.type === 'task',
                            'bg-gray-100 text-gray-600': notif.type === 'approval',
                            'bg-gray-100 text-gray-600': notif.type === 'cash',
                            'bg-gray-100 text-gray-600': notif.type === 'document',
                            'bg-gray-100 text-gray-600': notif.type === 'other'
                        }"
                        class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0">
                        <i :class="{
                            'fa-circle-check': notif.type === 'task',
                            'fa-clipboard-check': notif.type === 'approval',
                            'fa-money-bill-wave': notif.type === 'cash',
                            'fa-file-alt': notif.type === 'document',
                            'fa-info-circle': notif.type === 'other'
                        }" class="fas text-sm"></i>
                    </div>

                    {{-- Content --}}
                    <div class="flex-1 min-w-0">
                        <p 
                            :class="!notif.read_at ? 'text-gray-900 font-medium' : 'text-gray-700'"
                            class="text-sm mb-0.5" 
                            x-text="notif.title"></p>
                        <p class="text-xs text-gray-600 mb-1" x-text="notif.message"></p>
                        
                        {{-- Meta Info --}}
                        <div class="flex items-center gap-2 text-xs text-gray-500">
                            <span x-text="notif.time_ago"></span>
                            <span x-show="notif.project_name" class="flex items-center gap-1">
                                <i class="fas fa-folder text-gray-400 text-[10px]"></i>
                                <span x-text="notif.project_name"></span>
                            </span>
                        </div>

                        {{-- Quick Actions --}}
                        <div x-show="notif.actions && notif.actions.length > 0" 
                             class="flex gap-1.5 mt-2">
                            <template x-for="action in notif.actions" :key="action.label">
                                <button 
                                    @click.stop="handleAction(notif, action)"
                                    :class="action.primary ? 'bg-[#0077b5] text-white' : 'bg-gray-100 text-gray-700'"
                                    class="px-3 py-1 rounded-lg text-xs font-medium 
                                           active:scale-95 transition-all">
                                    <span x-text="action.label"></span>
                                </button>
                            </template>
                        </div>
                    </div>

                    {{-- Mark as Read Button --}}
                    <button 
                        x-show="!notif.read_at"
                        @click.stop="markAsRead(notif.id)"
                        class="text-gray-400 hover:text-[#0077b5] transition-colors">
                        <i class="fas fa-check text-sm"></i>
                    </button>
                </div>
            </div>
        </template>
    </div>

    {{-- Load More --}}
    <div x-show="hasMore && !loading" class="p-3 text-center">
        <button 
            @click="loadMore"
            class="px-6 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg 
                   font-medium text-sm active:scale-95 transition-all">
            Muat Lebih Banyak
        </button>
    </div>
</div>

<script>
function notificationCenter() {
    return {
        filter: 'all',
        loading: true,
        hasMore: false,
        page: 1,
        notifications: [],
        stats: {
            all: 0,
            tasks: 0,
            approvals: 0,
            cash: 0
        },

        get filteredNotifications() {
            if (this.filter === 'all') {
                return this.notifications;
            }
            return this.notifications.filter(n => n.type === this.filter);
        },

        async init() {
            await this.loadNotifications();
        },

        async loadNotifications() {
            this.loading = true;

            try {
                const response = await fetch(`{{ mobile_route('notifications.index') }}?page=${this.page}`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });

                const data = await response.json();

                if (this.page === 1) {
                    this.notifications = data.notifications;
                } else {
                    this.notifications = [...this.notifications, ...data.notifications];
                }

                this.hasMore = data.has_more;
                this.stats = data.stats;

            } catch (error) {
                console.error('Error loading notifications:', error);
            } finally {
                this.loading = false;
            }
        },

        async loadMore() {
            this.page++;
            await this.loadNotifications();
        },

        async markAsRead(notifId) {
            try {
                const response = await fetch(`{{ url('m/notifications') }}/${notifId}/read`, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });

                if (response.ok) {
                    // Update UI
                    const notif = this.notifications.find(n => n.id === notifId);
                    if (notif) {
                        notif.read_at = new Date().toISOString();
                        this.stats.all = Math.max(0, this.stats.all - 1);
                        if (notif.type === 'tasks') this.stats.tasks = Math.max(0, this.stats.tasks - 1);
                        if (notif.type === 'approvals') this.stats.approvals = Math.max(0, this.stats.approvals - 1);
                        if (notif.type === 'cash') this.stats.cash = Math.max(0, this.stats.cash - 1);
                    }
                }
            } catch (error) {
                console.error('Error marking as read:', error);
            }
        },

        async markAllAsRead() {
            if (!confirm('Tandai semua notifikasi sebagai sudah dibaca?')) return;

            try {
                const response = await fetch('{{ mobile_route("notifications.read-all") }}', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });

                if (response.ok) {
                    // Mark all as read in UI
                    this.notifications = this.notifications.map(n => ({
                        ...n,
                        read_at: new Date().toISOString()
                    }));
                    
                    this.stats = {
                        all: 0,
                        tasks: 0,
                        approvals: 0,
                        cash: 0
                    };
                }
            } catch (error) {
                console.error('Error marking all as read:', error);
            }
        },

        handleNotification(notif) {
            // Mark as read
            if (!notif.read_at) {
                this.markAsRead(notif.id);
            }

            // Navigate to related page
            if (notif.url) {
                window.location.href = notif.url;
            }
        },

        async handleAction(notif, action) {
            if (action.url) {
                window.location.href = action.url;
            } else if (action.callback) {
                await action.callback(notif);
            }
        }
    }
}
</script>
@endsection
