@extends('mobile.layouts.app')

@section('title', 'My Tasks')

@section('header-actions')
<a href="{{ mobile_route('tasks.create', fallback: '#') }}" class="text-blue-600 hover:text-blue-700 transition-colors">
    <i class="fas fa-plus text-xl"></i>
</a>
@endsection

@section('content')
<div x-data="taskManager()" class="pb-20">
    {{-- Filter Tabs --}}
    <div class="sticky top-16 bg-white z-10 border-b border-gray-200 px-3 pt-2">
        <div class="flex gap-2 overflow-x-auto scrollbar-hide pb-2">
            <button 
                @click="filter = 'today'" 
                :class="filter === 'today' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-600'"
                class="px-4 py-2 rounded-lg font-medium text-sm whitespace-nowrap transition-all 
                       active:scale-95 flex items-center gap-2">
                <i class="fas fa-calendar-day text-xs"></i>
                <span>Hari Ini</span>
                <span x-show="stats.today > 0" 
                      :class="filter === 'today' ? 'bg-white text-blue-600' : 'bg-blue-100 text-blue-600'"
                      class="px-2 py-0.5 rounded-full text-xs font-bold"
                      x-text="stats.today"></span>
            </button>
            <button 
                @click="filter = 'week'" 
                :class="filter === 'week' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-600'"
                class="px-4 py-2 rounded-lg font-medium text-sm whitespace-nowrap transition-all 
                       active:scale-95 flex items-center gap-2">
                <i class="fas fa-calendar-week text-xs"></i>
                <span>Minggu Ini</span>
                <span x-show="stats.week > 0" 
                      :class="filter === 'week' ? 'bg-white text-blue-600' : 'bg-gray-100 text-gray-600'"
                      class="px-2 py-0.5 rounded-full text-xs font-bold"
                      x-text="stats.week"></span>
            </button>
            <button 
                @click="filter = 'overdue'" 
                :class="filter === 'overdue' ? 'bg-red-600 text-white' : 'bg-gray-100 text-gray-600'"
                class="px-4 py-2 rounded-lg font-medium text-sm whitespace-nowrap transition-all 
                       active:scale-95 flex items-center gap-2">
                <i class="fas fa-exclamation-circle text-xs"></i>
                <span>Terlambat</span>
                <span x-show="stats.overdue > 0" 
                      :class="filter === 'overdue' ? 'bg-white text-red-600' : 'bg-red-100 text-red-600'"
                      class="px-2 py-0.5 rounded-full text-xs font-bold"
                      x-text="stats.overdue"></span>
            </button>
            <button 
                @click="filter = 'all'" 
                :class="filter === 'all' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-600'"
                class="px-4 py-2 rounded-lg font-medium text-sm whitespace-nowrap transition-all 
                       active:scale-95 flex items-center gap-2">
                <span>Semua</span>
                <span x-show="filter === 'all'" 
                      :class="filter === 'all' ? 'bg-white text-blue-600' : 'bg-gray-100 text-gray-600'"
                      class="px-2 py-0.5 rounded-full text-xs font-bold"
                      x-text="stats.all"></span>
            </button>
        </div>
    </div>

    {{-- Task List --}}
    <div class="p-3 space-y-2">
        <template x-if="loading">
            <div class="p-8 text-center">
                <i class="fas fa-spinner fa-spin text-gray-400 text-2xl"></i>
                <p class="text-sm text-gray-500 mt-2">Memuat tasks...</p>
            </div>
        </template>

        <template x-if="!loading && filteredTasks.length === 0">
            <div class="p-8 text-center">
                <i class="fas fa-check-circle text-gray-300 text-3xl mb-2"></i>
                <p class="text-gray-500 font-medium">Tidak ada task</p>
                <p class="text-sm text-gray-400 mt-1">Semua task sudah selesai</p>
            </div>
        </template>

        <template x-for="task in filteredTasks" :key="task.id">
            <div 
                class="bg-white rounded-lg border border-gray-200 overflow-hidden 
                       hover:border-gray-300 transition-all"
                :class="{
                    'border-red-200': task.is_overdue,
                    'opacity-60': task.status === 'completed'
                }"
                x-data="taskCard(task)">
                
                {{-- Swipeable Container --}}
                <div 
                    class="relative"
                    @touchstart="handleTouchStart($event)"
                    @touchmove="handleTouchMove($event)"
                    @touchend="handleTouchEnd($event)">
                    
                    {{-- Swipe Actions Background --}}
                    <div class="absolute inset-0 flex">
                        {{-- Left: Complete --}}
                        <div class="w-20 bg-green-500 flex items-center justify-center">
                            <i class="fas fa-check text-white text-xl"></i>
                        </div>
                        
                        {{-- Right: More Options --}}
                        <div class="flex-1"></div>
                        <div class="w-20 bg-blue-500 flex items-center justify-center">
                            <i class="fas fa-ellipsis-h text-white text-xl"></i>
                        </div>
                    </div>

                    {{-- Task Card Content --}}
                    <div 
                        class="relative bg-white"
                        :style="`transform: translateX(${swipeX}px); transition: ${swiping ? 'none' : 'transform 0.3s ease'}`"
                        @click="!swiping && viewTask()">
                        
                        <div class="p-3">
                            {{-- Header --}}
                            <div class="flex items-start gap-2 mb-2">
                                {{-- Checkbox --}}
                                <button 
                                    @click.stop="toggleComplete"
                                    class="flex-shrink-0 w-6 h-6 rounded-md border-2 flex items-center justify-center 
                                           active:scale-95 transition-all"
                                    :class="task.status === 'completed' 
                                        ? 'bg-green-500 border-green-500' 
                                        : 'border-gray-300 hover:border-blue-500'">
                                    <i x-show="task.status === 'completed'" 
                                       class="fas fa-check text-white text-xs"></i>
                                </button>

                                {{-- Task Info --}}
                                <div class="flex-1 min-w-0">
                                    <h3 
                                        class="font-medium text-gray-900 text-sm mb-1"
                                        :class="task.status === 'completed' && 'line-through text-gray-500'"
                                        x-text="task.title"></h3>
                                    
                                    {{-- Project --}}
                                    <p class="text-xs text-gray-600 flex items-center gap-1 mb-1.5" 
                                       x-show="task.project_name">
                                        <i class="fas fa-folder text-gray-400 text-[10px]"></i>
                                        <span x-text="task.project_name"></span>
                                    </p>

                                    {{-- Meta Info --}}
                                    <div class="flex items-center gap-2 text-xs text-gray-500">
                                        {{-- Due Date --}}
                                        <span 
                                            class="flex items-center gap-1"
                                            :class="{
                                                'text-red-600 font-medium': task.is_overdue,
                                                'text-orange-600 font-medium': task.is_due_soon
                                            }">
                                            <i class="fas fa-calendar"></i>
                                            <span x-text="task.due_date_formatted"></span>
                                        </span>

                                        {{-- Priority --}}
                                        <span 
                                            x-show="task.priority !== 'medium'"
                                            class="px-2 py-0.5 rounded-full text-xs font-medium"
                                            :class="{
                                                'bg-red-100 text-red-600': task.priority === 'urgent',
                                                'bg-orange-100 text-orange-600': task.priority === 'high',
                                                'bg-blue-100 text-blue-600': task.priority === 'low'
                                            }">
                                            <span x-text="task.priority_label"></span>
                                        </span>

                                        {{-- Assigned To --}}
                                        <span x-show="task.assigned_to_name" class="flex items-center gap-1">
                                            <i class="fas fa-user text-gray-400"></i>
                                            <span x-text="task.assigned_to_name"></span>
                                        </span>
                                    </div>
                                </div>

                                {{-- More Menu Trigger --}}
                                <button 
                                    @click.stop="showMenu = !showMenu"
                                    class="text-gray-400 hover:text-gray-600 transition-colors">
                                    <i class="fas fa-ellipsis-v"></i>
                                </button>
                            </div>

                            {{-- Description (if exists) --}}
                            <p x-show="task.description" 
                               class="text-sm text-gray-600 mt-2 line-clamp-2" 
                               x-text="task.description"></p>

                            {{-- Menu Dropdown --}}
                            <div x-show="showMenu" 
                                 @click.away="showMenu = false"
                                 class="absolute right-4 top-12 bg-white rounded-lg shadow-lg border border-gray-200 
                                        py-2 z-10 min-w-[150px]">
                                <button 
                                    @click.stop="viewTask()"
                                    class="w-full px-4 py-2 text-left text-sm hover:bg-gray-50 flex items-center gap-2">
                                    <i class="fas fa-eye text-gray-400 w-4"></i>
                                    <span>Lihat Detail</span>
                                </button>
                                <hr class="my-1">
                                <button 
                                    @click.stop="toggleComplete()"
                                    class="w-full px-4 py-2 text-left text-sm hover:bg-gray-50 flex items-center gap-2"
                                    :class="task.status === 'completed' ? 'text-orange-600' : 'text-green-600'">
                                    <i :class="task.status === 'completed' ? 'fa-rotate-left' : 'fa-check'" 
                                       class="fas w-4"></i>
                                    <span x-text="task.status === 'completed' ? 'Tandai Belum Selesai' : 'Tandai Selesai'"></span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </div>

    {{-- Load More --}}
    <div x-show="hasMore && !loading" class="p-4 text-center">
        <button 
            @click="loadMore"
            class="px-6 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg 
                   font-medium text-sm active:scale-95 transition-all">
            Muat Lebih Banyak
        </button>
    </div>
</div>

<script>
function taskManager() {
    return {
        filter: 'today',
        loading: true,
        hasMore: false,
        page: 1,
        tasks: [],
        stats: {
            today: 0,
            week: 0,
            overdue: 0,
            all: 0
        },

        get filteredTasks() {
            const now = new Date();
            const today = new Date(now.getFullYear(), now.getMonth(), now.getDate());
            const weekEnd = new Date(today);
            weekEnd.setDate(weekEnd.getDate() + 7);

            return this.tasks.filter(task => {
                if (this.filter === 'all') return true;
                
                const dueDate = new Date(task.due_date);
                
                if (this.filter === 'today') {
                    return dueDate.toDateString() === today.toDateString() && task.status !== 'completed';
                }
                
                if (this.filter === 'week') {
                    return dueDate >= today && dueDate <= weekEnd && task.status !== 'completed';
                }
                
                if (this.filter === 'overdue') {
                    return dueDate < today && task.status !== 'completed';
                }
                
                return true;
            });
        },

        async init() {
            await this.loadTasks();
        },

        async loadTasks() {
            this.loading = true;

            try {
                const response = await fetch(`{{ mobile_route('tasks.my') }}?page=${this.page}`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });

                const data = await response.json();

                if (this.page === 1) {
                    this.tasks = data.tasks;
                } else {
                    this.tasks = [...this.tasks, ...data.tasks];
                }

                this.hasMore = data.has_more;
                this.stats = data.stats;

            } catch (error) {
                console.error('Error loading tasks:', error);
            } finally {
                this.loading = false;
            }
        },

        async loadMore() {
            this.page++;
            await this.loadTasks();
        }
    }
}

function taskCard(task) {
    return {
        task: task,
        showMenu: false,
        swiping: false,
        swipeX: 0,
        startX: 0,
        
        handleTouchStart(e) {
            this.startX = e.touches[0].clientX;
            this.swiping = true;
        },
        
        handleTouchMove(e) {
            if (!this.swiping) return;
            
            const currentX = e.touches[0].clientX;
            const diff = currentX - this.startX;
            
            // Limit swipe distance
            if (diff > 80) {
                this.swipeX = 80; // Max left swipe (complete)
            } else if (diff < -80) {
                this.swipeX = -80; // Max right swipe (menu)
            } else {
                this.swipeX = diff;
            }
        },
        
        async handleTouchEnd() {
            this.swiping = false;
            
            // Execute action based on swipe distance
            if (this.swipeX > 60) {
                await this.toggleComplete();
            } else if (this.swipeX < -60) {
                this.showMenu = true;
            }
            
            // Reset swipe
            this.swipeX = 0;
        },
        
        async toggleComplete() {
            try {
                const response = await fetch(`{{ url('m/tasks') }}/${this.task.id}/complete`, {
                    method: 'PATCH',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });

                if (response.ok) {
                    this.task.status = this.task.status === 'completed' ? 'in_progress' : 'completed';
                    
                    // Haptic feedback
                    if ('vibrate' in navigator) {
                        navigator.vibrate(20);
                    }
                    
                    this.showMenu = false;
                }
            } catch (error) {
                console.error('Error toggling task:', error);
            }
        },
        
        viewTask() {
            window.location.href = `{{ url('m/tasks') }}/${this.task.id}`;
        }
    }
}
</script>
@endsection
