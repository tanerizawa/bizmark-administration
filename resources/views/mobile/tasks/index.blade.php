@extends('mobile.layouts.app')

@section('title', 'My Tasks')

@section('header-actions')
<button 
    onclick="window.openQuickAddTask && window.openQuickAddTask()"
    class="text-white hover:bg-white/20 w-9 h-9 rounded-full flex items-center justify-center transition-all active:scale-95">
    <i class="fas fa-plus text-lg"></i>
</button>
@endsection

@section('content')
<div id="taskContainer" x-data="taskManager()" x-init="
    window.openQuickAddTask = () => { showQuickAdd = true; };
    // Auto-open modal if query parameter present
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('openQuickAdd') === '1') {
        showQuickAdd = true;
        // Clean URL
        window.history.replaceState({}, '', '{{ mobile_route("tasks.index") }}');
    }
" class="pb-20">
    {{-- Filter Tabs --}}
    <div class="sticky top-16 bg-white z-10 border-b border-gray-200 px-3 pt-2">
        <div class="flex gap-2 overflow-x-auto scrollbar-hide pb-2">
            <button 
                @click="filter = 'today'" 
                :class="filter === 'today' ? 'bg-[#0077b5] text-white' : 'bg-gray-100 text-gray-600'"
                class="px-4 py-2 rounded-lg font-medium text-sm whitespace-nowrap transition-all 
                       active:scale-95 flex items-center gap-2">
                <i class="fas fa-calendar-day text-xs"></i>
                <span>Hari Ini</span>
                <span x-show="stats.today > 0" 
                      :class="filter === 'today' ? 'bg-white text-[#0077b5]' : 'bg-[#e7f3f8] text-[#0077b5]'"
                      class="px-2 py-0.5 rounded-full text-xs font-bold"
                      x-text="stats.today"></span>
            </button>
            <button 
                @click="filter = 'week'" 
                :class="filter === 'week' ? 'bg-[#0077b5] text-white' : 'bg-gray-100 text-gray-600'"
                class="px-4 py-2 rounded-lg font-medium text-sm whitespace-nowrap transition-all 
                       active:scale-95 flex items-center gap-2">
                <i class="fas fa-calendar-week text-xs"></i>
                <span>Minggu Ini</span>
                <span x-show="stats.week > 0" 
                      :class="filter === 'week' ? 'bg-white text-[#0077b5]' : 'bg-gray-100 text-gray-600'"
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
                :class="filter === 'all' ? 'bg-[#0077b5] text-white' : 'bg-gray-100 text-gray-600'"
                class="px-4 py-2 rounded-lg font-medium text-sm whitespace-nowrap transition-all 
                       active:scale-95 flex items-center gap-2">
                <span>Semua</span>
                <span x-show="filter === 'all'" 
                      :class="filter === 'all' ? 'bg-white text-[#0077b5]' : 'bg-gray-100 text-gray-600'"
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
                        <div class="w-20 bg-[#0077b5] flex items-center justify-center">
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
                                        : 'border-gray-300 hover:border-[#0077b5]'">
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
                                                'bg-[#e7f3f8] text-[#0077b5]': task.priority === 'low'
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

    {{-- Quick Add Task Modal --}}
    <div 
        x-show="showQuickAdd" 
        x-cloak
        @click.self="showQuickAdd = false"
        class="fixed inset-0 bg-black/50 z-50 flex items-end sm:items-center sm:justify-center"
        style="display: none;">
        
        <div 
            x-show="showQuickAdd"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="translate-y-full sm:translate-y-0 sm:scale-95 sm:opacity-0"
            x-transition:enter-end="translate-y-0 sm:scale-100 sm:opacity-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="translate-y-0 sm:scale-100 sm:opacity-100"
            x-transition:leave-end="translate-y-full sm:translate-y-0 sm:scale-95 sm:opacity-0"
            class="bg-white w-full sm:max-w-md sm:rounded-2xl rounded-t-2xl max-h-[90vh] overflow-y-auto">
            
            {{-- Modal Header --}}
            <div class="sticky top-0 bg-white border-b border-gray-200 p-4 flex items-center justify-between rounded-t-2xl">
                <h3 class="text-lg font-semibold text-gray-900">Quick Add Task</h3>
                <button 
                    @click="showQuickAdd = false"
                    class="text-gray-400 hover:text-gray-600 w-8 h-8 rounded-full hover:bg-gray-100 flex items-center justify-center transition-all">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            {{-- Modal Body --}}
            <form @submit.prevent="submitQuickTask" class="p-4 space-y-4">
                {{-- Task Title --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Judul Task <span class="text-red-500">*</span>
                    </label>
                    <input 
                        x-model="quickTask.title"
                        type="text" 
                        required
                        placeholder="Misal: Review dokumen proposal"
                        class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0077b5] focus:border-transparent">
                </div>

                {{-- Project Selection --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Project <span class="text-red-500">*</span>
                    </label>
                    <select 
                        x-model="quickTask.project_id"
                        required
                        class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0077b5] focus:border-transparent">
                        <option value="">Pilih Project...</option>
                        @foreach(\App\Models\Project::where('status_id', '!=', 8)->orderBy('name')->get() as $project)
                            <option value="{{ $project->id }}">{{ $project->name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Due Date --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Deadline <span class="text-red-500">*</span>
                    </label>
                    <input 
                        x-model="quickTask.due_date"
                        type="date" 
                        required
                        :min="new Date().toISOString().split('T')[0]"
                        class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0077b5] focus:border-transparent">
                </div>

                {{-- Priority --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Prioritas</label>
                    <div class="grid grid-cols-3 gap-2">
                        <button 
                            type="button"
                            @click="quickTask.priority = 'low'"
                            :class="quickTask.priority === 'low' ? 'bg-[#0077b5] text-white border-[#0077b5]' : 'bg-white text-gray-700 border-gray-300'"
                            class="px-3 py-2 rounded-lg border font-medium text-sm transition-all active:scale-95">
                            Low
                        </button>
                        <button 
                            type="button"
                            @click="quickTask.priority = 'medium'"
                            :class="quickTask.priority === 'medium' ? 'bg-orange-500 text-white border-orange-500' : 'bg-white text-gray-700 border-gray-300'"
                            class="px-3 py-2 rounded-lg border font-medium text-sm transition-all active:scale-95">
                            Medium
                        </button>
                        <button 
                            type="button"
                            @click="quickTask.priority = 'high'"
                            :class="quickTask.priority === 'high' ? 'bg-red-500 text-white border-red-500' : 'bg-white text-gray-700 border-gray-300'"
                            class="px-3 py-2 rounded-lg border font-medium text-sm transition-all active:scale-95">
                            High
                        </button>
                    </div>
                </div>

                {{-- Assign To --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Assign To</label>
                    <select 
                        x-model="quickTask.assigned_user_id"
                        class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0077b5] focus:border-transparent">
                        <option value="">Diri Sendiri</option>
                        @foreach(\App\Models\User::where('is_active', true)->orderBy('name')->get() as $user)
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Submit Buttons --}}
                <div class="flex gap-2 pt-2">
                    <button 
                        type="button"
                        @click="showQuickAdd = false"
                        class="flex-1 px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg font-medium transition-all active:scale-95">
                        Batal
                    </button>
                    <button 
                        type="submit"
                        :disabled="submitting"
                        class="flex-1 px-4 py-2.5 bg-[#0077b5] hover:bg-[#005582] text-white rounded-lg font-medium transition-all active:scale-95 disabled:opacity-50 disabled:cursor-not-allowed">
                        <span x-show="!submitting">Buat Task</span>
                        <span x-show="submitting">
                            <i class="fas fa-spinner fa-spin"></i> Menyimpan...
                        </span>
                    </button>
                </div>
            </form>
        </div>
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
        showQuickAdd: false,
        submitting: false,
        quickTask: {
            title: '',
            project_id: '',
            due_date: '',
            priority: 'medium',
            assigned_user_id: ''
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
        },

        async submitQuickTask() {
            if (this.submitting) return;
            
            this.submitting = true;

            try {
                const response = await fetch('{{ route('mobile.quick.task') }}', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(this.quickTask)
                });

                const data = await response.json();

                if (response.ok && data.success) {
                    // Reset form
                    this.quickTask = {
                        title: '',
                        project_id: '',
                        due_date: '',
                        priority: 'medium',
                        assigned_user_id: ''
                    };
                    
                    // Close modal
                    this.showQuickAdd = false;
                    
                    // Show toast
                    this.showToast('Task berhasil dibuat!', 'success');
                    
                    // Reload tasks
                    this.page = 1;
                    await this.loadTasks();
                    
                    // Optional: redirect to task detail
                    if (data.redirect) {
                        setTimeout(() => {
                            window.location.href = data.redirect;
                        }, 1000);
                    }
                } else {
                    this.showToast(data.message || 'Gagal membuat task', 'error');
                }

            } catch (error) {
                console.error('Error creating task:', error);
                this.showToast('Terjadi kesalahan jaringan', 'error');
            } finally {
                this.submitting = false;
            }
        },

        showToast(message, type = 'info') {
            // Create toast element
            const toast = document.createElement('div');
            toast.className = `fixed bottom-20 left-4 right-4 p-4 rounded-lg shadow-lg z-50 transform transition-all duration-300 ${
                type === 'success' ? 'bg-green-500' : 
                type === 'error' ? 'bg-red-500' : 
                'bg-gray-800'
            } text-white`;
            toast.innerHTML = `
                <div class="flex items-center gap-2">
                    <i class="fas ${type === 'success' ? 'fa-check-circle' : type === 'error' ? 'fa-exclamation-circle' : 'fa-info-circle'}"></i>
                    <span>${message}</span>
                </div>
            `;
            
            document.body.appendChild(toast);
            
            // Animate in
            setTimeout(() => toast.style.transform = 'translateY(-10px)', 10);
            
            // Remove after 3s
            setTimeout(() => {
                toast.style.opacity = '0';
                toast.style.transform = 'translateY(10px)';
                setTimeout(() => toast.remove(), 300);
            }, 3000);
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
