@extends('mobile.layouts.app')

@section('title', 'Tasks')

@section('content')
<div class="mobile-page pb-20" x-data="tasksPage()">
    <!-- Filter Tabs -->
    <div class="sticky top-14 z-10 bg-white border-b border-gray-200 safe-top">
        <div class="flex overflow-x-auto scrollbar-hide">
            <button @click="filterTasks('all')" 
                    :class="currentFilter === 'all' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500'"
                    class="flex-shrink-0 px-4 py-3 border-b-2 font-medium text-sm">
                Semua ({{ $stats['all'] }})
            </button>
            <button @click="filterTasks('today')" 
                    :class="currentFilter === 'today' ? 'border-green-500 text-green-600' : 'border-transparent text-gray-500'"
                    class="flex-shrink-0 px-4 py-3 border-b-2 font-medium text-sm">
                Hari Ini ({{ $stats['today'] }})
            </button>
            <button @click="filterTasks('overdue')" 
                    :class="currentFilter === 'overdue' ? 'border-red-500 text-red-600' : 'border-transparent text-gray-500'"
                    class="flex-shrink-0 px-4 py-3 border-b-2 font-medium text-sm">
                Overdue ({{ $stats['overdue'] }})
            </button>
        </div>
    </div>

    <!-- Tasks List -->
    <div class="p-4">
        <template x-if="loading">
            <div class="py-12 text-center">
                <i class="fas fa-spinner fa-spin text-3xl text-gray-400"></i>
                <p class="mt-3 text-gray-500">Loading...</p>
            </div>
        </template>

        <template x-if="!loading && tasks.length === 0">
            <div class="py-12 text-center">
                <i class="fas fa-check-circle text-5xl text-green-300 mb-3"></i>
                <h3 class="text-lg font-semibold text-gray-900 mb-1">Semua Task Selesai!</h3>
                <p class="text-gray-500">Tidak ada task pending</p>
            </div>
        </template>

        <div class="space-y-3">
            <template x-for="task in tasks" :key="task.id">
                <div class="bg-white rounded-lg border border-gray-200 overflow-hidden"
                     :class="task.status === 'done' ? 'opacity-60' : ''">
                    
                    <a :href="`/m/tasks/${task.id}`" class="block p-4">
                        <!-- Header -->
                        <div class="flex items-start justify-between mb-2">
                            <div class="flex-1 min-w-0 pr-3">
                                <h3 class="font-semibold text-gray-900 mb-0.5" 
                                    :class="task.status === 'done' ? 'line-through' : ''"
                                    x-text="task.title"></h3>
                                <p class="text-sm text-gray-600 truncate" x-text="task.project?.name || '-'"></p>
                            </div>
                            
                            <!-- Priority Badge -->
                            <template x-if="task.priority">
                                <span :class="getPriorityColor(task.priority)" 
                                      class="flex-shrink-0 px-2 py-1 text-xs font-medium rounded-full uppercase">
                                    <span x-text="task.priority"></span>
                                </span>
                            </template>
                        </div>

                        <!-- Meta Info -->
                        <div class="flex items-center gap-4 text-xs text-gray-600 mb-3">
                            <div class="flex items-center gap-1" 
                                 :class="isOverdue(task.due_date, task.status) ? 'text-red-600 font-semibold' : ''">
                                <i class="fas fa-calendar-alt"></i>
                                <span x-text="formatDate(task.due_date)"></span>
                            </div>
                            <template x-if="task.assignee">
                                <div class="flex items-center gap-1">
                                    <i class="fas fa-user"></i>
                                    <span x-text="task.assignee.name"></span>
                                </div>
                            </template>
                        </div>

                        <!-- Status Badge -->
                        <span :class="getStatusColor(task.status)" 
                              class="inline-block px-2 py-1 text-xs font-medium rounded-full">
                            <span x-text="getStatusLabel(task.status)"></span>
                        </span>
                    </a>

                    <!-- Quick Actions -->
                    <template x-if="task.status !== 'done'">
                        <div class="border-t border-gray-100 px-4 py-2">
                            <button @click.prevent="completeTask(task)" 
                                    class="w-full py-2 bg-green-50 text-green-700 text-sm font-medium rounded-lg hover:bg-green-100 active:bg-green-200">
                                <i class="fas fa-check mr-1"></i>Mark as Complete
                            </button>
                        </div>
                    </template>
                </div>
            </template>
        </div>

        <!-- Load More -->
        <template x-if="hasMore && !loading">
            <div class="mt-4">
                <button @click="loadMore()" 
                        class="w-full py-3 bg-gray-100 text-gray-700 rounded-lg font-medium hover:bg-gray-200">
                    Load More
                </button>
            </div>
        </template>
    </div>
</div>
@endsection

@push('scripts')
<script>
function tasksPage() {
    return {
        tasks: @json($tasks->items()),
        currentFilter: '{{ $currentFilter }}',
        loading: false,
        hasMore: {{ $tasks->hasMorePages() ? 'true' : 'false' }},
        currentPage: {{ $tasks->currentPage() }},

        async filterTasks(filter) {
            if (this.currentFilter === filter) return;
            
            this.currentFilter = filter;
            this.loading = true;
            
            try {
                window.location.href = `/m/tasks?filter=${filter}`;
            } catch (error) {
                console.error('Filter error:', error);
                this.loading = false;
            }
        },

        async completeTask(task) {
            if (!confirm('Mark task sebagai complete?')) return;

            try {
                const response = await fetch(`/m/tasks/${task.id}/complete`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });

                const data = await response.json();
                
                if (data.success) {
                    // Update task in list
                    task.status = 'done';
                    this.showToast('Task completed!', 'success');
                }
            } catch (error) {
                console.error('Complete error:', error);
                this.showToast('Gagal complete task', 'error');
            }
        },

        async loadMore() {
            this.loading = true;
            
            try {
                const response = await fetch(`/m/tasks?filter=${this.currentFilter}&page=${this.currentPage + 1}`);
                const data = await response.json();
                
                this.tasks.push(...(data.tasks || []));
                this.hasMore = data.hasMore || false;
                this.currentPage++;
            } catch (error) {
                console.error('Load more error:', error);
            } finally {
                this.loading = false;
            }
        },

        getPriorityColor(priority) {
            const colors = {
                'high': 'bg-red-100 text-red-700',
                'medium': 'bg-yellow-100 text-yellow-700',
                'low': 'bg-blue-100 text-blue-700'
            };
            return colors[priority] || 'bg-gray-100 text-gray-700';
        },

        getStatusColor(status) {
            const colors = {
                'todo': 'bg-gray-100 text-gray-700',
                'in_progress': 'bg-blue-100 text-blue-700',
                'done': 'bg-green-100 text-green-700'
            };
            return colors[status] || 'bg-gray-100 text-gray-700';
        },

        getStatusLabel(status) {
            const labels = {
                'todo': 'To Do',
                'in_progress': 'In Progress',
                'done': 'Done'
            };
            return labels[status] || status;
        },

        isOverdue(dueDate, status) {
            if (status === 'done') return false;
            return new Date(dueDate) < new Date();
        },

        formatDate(date) {
            if (!date) return '-';
            const d = new Date(date);
            const today = new Date();
            const tomorrow = new Date(today);
            tomorrow.setDate(tomorrow.getDate() + 1);

            if (d.toDateString() === today.toDateString()) {
                return 'Hari Ini';
            } else if (d.toDateString() === tomorrow.toDateString()) {
                return 'Besok';
            } else {
                return d.toLocaleDateString('id-ID', { 
                    day: 'numeric', 
                    month: 'short',
                    year: d.getFullYear() !== today.getFullYear() ? 'numeric' : undefined
                });
            }
        },

        showToast(message, type = 'info') {
            const toast = document.createElement('div');
            toast.className = `fixed bottom-20 left-1/2 -translate-x-1/2 px-6 py-3 rounded-lg text-white font-medium z-50 ${
                type === 'success' ? 'bg-green-500' : 'bg-red-500'
            }`;
            toast.textContent = message;
            document.body.appendChild(toast);
            
            setTimeout(() => toast.remove(), 3000);
        }
    };
}
</script>
@endpush
