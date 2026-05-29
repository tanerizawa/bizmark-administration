<!-- Tasks Tab Content -->
<div class="page-card space-y-4 tasks-tab-no-hover">
    <div class="flex items-center justify-between">
        <div>
            <h3 class="card-title text-white">
                <i class="fas fa-tasks mr-2 text-apple-blue-dark"></i>Task & Kegiatan Proyek
            </h3>
            <p class="text-sm mt-1 text-dark-text-secondary">
                Kelola task dan monitor progress kegiatan proyek
            </p>
        </div>
        
        <button @click.stop="showAddTaskModal()"
                class="btn-primary-sm bg-apple-blue/90 text-white">
            <i class="fas fa-plus mr-2"></i>Tambah Task
        </button>
    </div>

    @if($project->tasks->count() > 0)
        <!-- Statistics -->
        <div class="grid grid-cols-2 md:grid-cols-5 gap-3 mb-4">
            <div class="data-block">
                <p class="text-xs mb-1 text-dark-text-secondary">Total Task</p>
                <p class="text-lg font-semibold text-white">{{ $project->tasks->count() }}</p>
            </div>
            <div class="data-block bg-apple-green/8 border-apple-green/20">
                <p class="text-xs mb-1 text-apple-green/85">Selesai</p>
                <p class="text-lg font-semibold text-apple-green">
                    {{ $project->tasks->where('status', 'done')->count() }}
                </p>
            </div>
            <div class="data-block bg-apple-blue/8 border-apple-blue/20">
                <p class="text-xs mb-1 text-apple-blue/85">Dalam Proses</p>
                <p class="text-lg font-semibold text-apple-blue">
                    {{ $project->tasks->where('status', 'in_progress')->count() }}
                </p>
            </div>
            <div class="data-block bg-apple-red/8 border-apple-red/20">
                <p class="text-xs mb-1 text-apple-red/85">Terblokir</p>
                <p class="text-lg font-semibold text-apple-red">
                    {{ $project->tasks->where('status', 'blocked')->count() }}
                </p>
            </div>
            <div class="data-block bg-apple-orange/8 border-apple-orange/20">
                <p class="text-xs mb-1 text-apple-orange/85">Terlambat</p>
                <p class="text-lg font-semibold text-apple-orange">
                    {{ $project->tasks->filter->isOverdue()->count() }}
                </p>
            </div>
        </div>

        <!-- Task Flow Diagram -->
        <div id="tasks-sortable" class="space-y-4">
            <h4 class="text-sm font-semibold text-dark-text-primary/80">
                <i class="fas fa-list-ol mr-2"></i>Daftar Task
                <span class="ml-2 text-xs text-dark-text-tertiary">(Drag untuk mengubah urutan)</span>
            </h4>

            @foreach($project->tasks->sortBy('sort_order') as $task)
                <div class="relative"
                     data-task-id="{{ $task->id }}"
                     data-sort-order="{{ $task->sort_order }}"
                     data-task-title="{{ $task->title }}"
                     data-status="{{ $task->status }}"
                     data-priority="{{ $task->priority }}"
                     data-can-start="{{ $task->canStart() ? 'true' : 'false' }}"
                     data-blockers="{{ json_encode($task->getBlockers()) }}"
                     data-assigned-user-id="{{ $task->assigned_user_id ?? '' }}"
                     data-assigned-user-name="{{ $task->assignedUser->name ?? '' }}"
                     data-due-date="{{ $task->due_date?->format('Y-m-d') ?? '' }}"
                     data-description="{{ $task->description ?? '' }}"
                     data-sop-notes="{{ $task->sop_notes ?? '' }}"
                     data-estimated-hours="{{ $task->estimated_hours ?? '' }}">
                    
                    <!-- Task Card -->
            <div class="data-block task-card" style="transition: all 0.2s ease;">
                        
                        <div class="flex items-start gap-4">
                            <!-- Drag Handle -->
                            <div class="drag-handle flex-shrink-0 cursor-move opacity-50 hover:opacity-100 transition-opacity text-dark-text-secondary"
                                 title="Drag untuk mengubah urutan">
                                <i class="fas fa-grip-vertical text-xl"></i>
                            </div>
                            
                            <!-- Sort Order Badge -->
                            <div class="flex-shrink-0 w-10 h-10 rounded-full flex items-center justify-center font-bold bg-[rgba(10,132,255,0.3)] text-apple-blue">
                                {{ $task->sort_order }}
                            </div>

                            <div class="flex-1">
                                <!-- Task Header -->
                                <div class="flex items-start justify-between mb-2">
                                    <div class="flex-1">
                                        <h5 class="font-semibold text-white">
                                            {{ $task->title }}
                                        </h5>
                                        @if($task->description)
                                            <p class="text-sm mt-1 text-dark-text-secondary">
                                                {{ Str::limit($task->description, 100) }}
                                            </p>
                                        @endif
                                    </div>

                                    <div class="flex items-center gap-2 ml-4">
                                        <!-- Priority Badge (dynamic) -->
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full"
                                              style="background: {{ $task->getPriorityColor() }}20; color: {{ $task->getPriorityColor() }};">
                                            {{ $task->getPriorityLabel() }}
                                        </span>
                                        
                                        <!-- Status Badge (dynamic) -->
                                        <span class="inline-flex px-3 py-1 text-xs font-semibold rounded-full"
                                              style="background: {{ $task->getStatusColor() }}20; color: {{ $task->getStatusColor() }};">
                                            {{ $task->getStatusLabel() }}
                                        </span>
                                        
                                        <!-- Actions -->
                                        <div class="flex gap-1">
                                            <button @click.stop="editTask({{ $task->id }})"
                                                    class="p-2 rounded transition-colors text-apple-blue"
                                                    onmouseover="this.style.background='rgba(10, 132, 255, 0.1)'"
                                                    onmouseout="this.style.background='transparent'"
                                                    title="Edit Task">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button @click.stop="deleteTask({{ $task->id }})"
                                                    class="p-2 rounded transition-colors text-apple-red"
                                                    onmouseover="this.style.background='rgba(255, 59, 48, 0.1)'"
                                                    onmouseout="this.style.background='transparent'"
                                                    title="Hapus Task">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <!-- Task Details Grid -->
                                <div class="grid grid-cols-3 gap-4 mt-3">
                                    <!-- Assigned User -->
                                    <div>
                                        <p class="text-xs mb-1 text-dark-text-secondary">Ditugaskan Ke:</p>
                                        @if($task->assignedUser)
                                            <div class="flex items-center">
                                                <div class="w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold mr-2 bg-apple-blue/30 text-apple-blue">
                                                    {{ strtoupper(substr($task->assignedUser->name, 0, 1)) }}
                                                </div>
                                                <span class="text-sm text-dark-text-primary/80">
                                                    {{ $task->assignedUser->name }}
                                                </span>
                                            </div>
                                        @else
                                            <span class="text-sm text-dark-text-tertiary">
                                                Belum ditugaskan
                                            </span>
                                        @endif
                                    </div>

                                    <!-- Due Date -->
                                    <div>
                                        <p class="text-xs mb-1 text-dark-text-secondary">Deadline:</p>
                                        @if($task->due_date)
                                            <span class="text-sm {{ $task->isOverdue() ? 'font-bold' : '' }}"
                                                  style="color: {{ $task->isOverdue() ? 'rgba(255, 59, 48, 1)' : 'rgba(235, 235, 245, 0.8)' }};">
                                                {{ $task->due_date->format('d M Y') }}
                                                @if($task->isOverdue())
                                                    <i class="fas fa-exclamation-triangle ml-1"></i>
                                                @endif
                                            </span>
                                        @else
                                            <span class="text-sm text-dark-text-tertiary">
                                                Tidak ada deadline
                                            </span>
                                        @endif
                                    </div>

                                    <!-- Progress -->
                                    <div>
                                        <p class="text-xs mb-1 text-dark-text-secondary">Progress:</p>
                                        <div class="flex items-center">
                                            <div class="flex-1 h-2 rounded-full overflow-hidden bg-[rgba(58,58,60,0.5)]">
                                                <div class="h-full transition-all"
                                                     style="width: {{ $task->getProgress() }}%; background: {{ $task->getStatusColor() }};"></div>
                                            </div>
                                            <span class="ml-2 text-sm font-semibold" style="color: {{ $task->getStatusColor() }};">
                                                {{ $task->getProgress() }}%
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Can Start Check -->
                                @if($task->status === 'todo')
                                    @if($task->canStart())
                                        <div class="mt-3 p-2 rounded-lg text-sm bg-apple-green/10 text-apple-green">
                                            <i class="fas fa-check-circle mr-2"></i>
                                            Siap dikerjakan - Tidak ada blocker
                                        </div>
                                    @else
                                        <div class="mt-3 p-2 rounded-lg text-sm bg-apple-red/10 text-apple-red">
                                            <i class="fas fa-lock mr-2"></i>
                                            Menunggu prasyarat:
                                            @foreach($task->getBlockers() as $blocker)
                                                <strong>{{ $blocker }}</strong>{{ !$loop->last ? ', ' : '' }}
                                            @endforeach
                                        </div>
                                    @endif
                                @endif

                                <!-- Dependency Info -->
                                @if($task->dependsOnTask)
                                    <div class="mt-3 pt-3 border-t border-white/10">
                                        <p class="text-xs font-semibold mb-2 text-dark-text-secondary">
                                            <i class="fas fa-link mr-1"></i>PRASYARAT TASK:
                                        </p>
                                        <span class="inline-flex items-center px-2 py-1 text-xs rounded-full"
                                              style="background: {{ $task->dependsOnTask->getStatusColor() }}20; color: {{ $task->dependsOnTask->getStatusColor() }};">
                                            <i class="fas fa-{{ $task->dependsOnTask->status === 'done' ? 'check-circle' : 'clock' }} mr-1"></i>
                                            {{ $task->dependsOnTask->title }}
                                            ({{ $task->dependsOnTask->getStatusLabel() }})
                                        </span>
                                    </div>
                                @endif

                                <!-- Related Permit Info -->
                                @if($task->permit)
                                    <div class="mt-3 pt-3 border-t border-white/10">
                                        <p class="text-xs font-semibold mb-2 text-dark-text-secondary">
                                            <i class="fas fa-file-contract mr-1 text-blue-400"></i>TERKAIT IZIN:
                                        </p>
                                        <span class="inline-flex items-center px-3 py-1 text-xs rounded-full bg-apple-blue/15 text-apple-blue">
                                            <span class="w-5 h-5 rounded-full flex items-center justify-center text-xs font-bold mr-2 bg-apple-blue/30">
                                                {{ $task->permit->sequence_order }}
                                            </span>
                                            {{ $task->permit->permitType->name ?? $task->permit->custom_permit_name }}
                                            @if($task->permit->institution)
                                                <span class="mx-1">•</span>
                                                <span class="opacity-80">{{ $task->permit->institution->name }}</span>
                                            @endif
                                        </span>
                                    </div>
                                @endif

                                <!-- Time Tracking -->
                                @if($task->estimated_hours || $task->actual_hours)
                                    <div class="mt-3 grid grid-cols-2 gap-4 text-sm">
                                        @if($task->estimated_hours)
                                            <div>
                                                <p class="text-xs text-dark-text-secondary">Estimasi:</p>
                                                <p class="text-dark-text-primary/80">
                                                    {{ $task->estimated_hours }} jam
                                                </p>
                                            </div>
                                        @endif
                                        @if($task->actual_hours)
                                            <div>
                                                <p class="text-xs text-dark-text-secondary">Aktual:</p>
                                                <p class="text-dark-text-primary/80">
                                                    {{ $task->actual_hours }} jam
                                                </p>
                                            </div>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Arrow to next -->
                    @if(!$loop->last)
                        <div class="flex justify-center my-2">
                            <i class="fas fa-arrow-down text-2xl text-dark-text-tertiary/50"></i>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    @else
        <!-- Empty State -->
        <div class="text-center py-12">
            <i class="fas fa-tasks text-6xl mb-4 text-dark-text-tertiary/50"></i>
            <h4 class="text-xl font-semibold mb-2 text-white">Belum Ada Task</h4>
            <p class="mb-6 text-dark-text-secondary">
                Tambahkan task untuk mulai mengelola kegiatan proyek
            </p>
            <button @click.stop="showAddTaskModal()"
                    class="px-6 py-3 rounded-lg font-medium transition-colors bg-apple-blue/90 text-white">
                <i class="fas fa-plus mr-2"></i>Tambah Task Pertama
            </button>
        </div>
    @endif
</div>

<!-- Add Task Modal -->
<div id="add-task-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50" style="display: none;">
    <div class="bg-[#1e1e1e] rounded-lg shadow-xl max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto">
        <div class="sticky top-0 bg-[#1e1e1e] border-b border-gray-700 px-6 py-4">
            <div class="flex items-center justify-between">
                <h3 class="text-xl font-semibold text-white">Tambah Task Baru</h3>
                <button @click.stop="closeAddTaskModal()" class="text-gray-400 hover:text-white">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
        </div>

        <form action="{{ route('tasks.store') }}" method="POST" class="p-6">
            @csrf
            <input type="hidden" name="project_id" value="{{ $project->id }}">
            
            <div class="space-y-4">
                <!-- Title -->
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">
                        Judul Task <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="title" required 
                           class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-blue-500"
                           placeholder="Misal: Membuat proposal proyek">
                </div>

                <!-- Description -->
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">
                        Deskripsi
                    </label>
                    <textarea name="description" rows="3" 
                              class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-blue-500"
                              placeholder="Jelaskan detail task ini..."></textarea>
                </div>

                <!-- Priority & Status -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">
                            Prioritas <span class="text-red-500">*</span>
                        </label>
                        <select name="priority" required 
                                class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-blue-500">
                            <option value="normal">Normal</option>
                            <option value="low">Rendah</option>
                            <option value="high">Tinggi</option>
                            <option value="urgent">Mendesak</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">
                            Status <span class="text-red-500">*</span>
                        </label>
                        <select name="status" required 
                                class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-blue-500">
                            <option value="todo">Belum Dimulai</option>
                            <option value="in_progress">Dalam Proses</option>
                            <option value="done">Selesai</option>
                            <option value="blocked">Terblokir</option>
                        </select>
                    </div>
                </div>

                <!-- Assignment & Due Date -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">
                            Ditugaskan Ke
                        </label>
                        <select name="assigned_user_id" 
                                class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-blue-500">
                            <option value="">-- Pilih User --</option>
                            @php
                                $users = \App\Models\User::orderBy('name')->get();
                            @endphp
                            @foreach($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">
                            Deadline
                        </label>
                        <input type="date" name="due_date" 
                               class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-blue-500">
                    </div>
                </div>

                <!-- Dependency -->
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">
                        Task Prasyarat
                    </label>
                    <select name="depends_on_task_id" 
                            class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-blue-500">
                        <option value="">-- Tidak Ada --</option>
                        @foreach($project->tasks->sortBy('sort_order') as $existingTask)
                            <option value="{{ $existingTask->id }}">
                                {{ $existingTask->sort_order }}. {{ $existingTask->title }}
                            </option>
                        @endforeach
                    </select>
                    <p class="text-xs mt-1 text-dark-text-tertiary">
                        Task ini hanya bisa dimulai setelah task prasyarat selesai
                    </p>
                </div>

                <!-- Related Permit -->
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">
                        <i class="fas fa-file-contract mr-1 text-blue-400"></i>
                        Terkait Izin/Pra Syarat
                    </label>
                    <select name="project_permit_id" 
                            class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-blue-500">
                        <option value="">-- Tidak Terkait Izin --</option>
                        @foreach($project->permits->sortBy('sequence_order') as $permit)
                            <option value="{{ $permit->id }}">
                                {{ $permit->sequence_order }}. {{ $permit->permitType->name ?? 'Unknown' }}
                                @if($permit->institution)
                                    - {{ $permit->institution->name }}
                                @endif
                            </option>
                        @endforeach
                    </select>
                    <p class="text-xs mt-1 text-dark-text-tertiary">
                        Task ini merupakan bagian dari proses pengurusan izin tertentu
                    </p>
                </div>

                <!-- Estimated Hours -->
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">
                        Estimasi Waktu (Jam)
                    </label>
                    <input type="number" name="estimated_hours" min="1" 
                           class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-blue-500"
                           placeholder="Contoh: 8">
                </div>

                <!-- SOP Notes -->
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">
                        SOP / Checklist
                    </label>
                    <textarea name="sop_notes" rows="3" 
                              class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-blue-500"
                              placeholder="Tambahkan SOP atau checklist untuk task ini..."></textarea>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex gap-3 mt-6">
                <button type="button" @click.stop="closeAddTaskModal()"
                        class="flex-1 px-4 py-2 bg-gray-700 hover:bg-gray-600 text-white rounded-lg transition">
                    Batal
                </button>
                <button type="submit" 
                        class="flex-1 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">
                    <i class="fas fa-plus mr-2"></i>Tambah Task
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Update Status Modal -->
<!-- Edit Task Modal -->
<div id="edit-task-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 overflow-y-auto" style="display: none;">
    <div class="bg-[#1e1e1e] rounded-lg shadow-xl max-w-2xl w-full mx-4 my-8">
        <div class="sticky top-0 bg-[#1e1e1e] border-b border-gray-700 px-6 py-4">
            <div class="flex items-center justify-between">
                <h3 class="text-xl font-semibold text-white">Edit Task</h3>
                <button @click.stop="closeEditTaskModal()" class="text-gray-400 hover:text-white">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
        </div>

        <form id="edit-task-form" method="POST" class="p-6">
            @csrf
            @method('PUT')
            
            <div class="space-y-4">
                <!-- Title -->
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">
                        Judul Task <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="title" id="edit-title" required 
                           class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-blue-500"
                           placeholder="Misal: Membuat proposal proyek">
                </div>

                <!-- Description -->
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">
                        Deskripsi
                    </label>
                    <textarea name="description" id="edit-description" rows="3" 
                              class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-blue-500"
                              placeholder="Jelaskan detail task ini..."></textarea>
                </div>

                <!-- Priority & Status -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">
                            Prioritas <span class="text-red-500">*</span>
                        </label>
                        <select name="priority" id="edit-priority" required 
                                class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-blue-500">
                            <option value="normal">Normal</option>
                            <option value="low">Rendah</option>
                            <option value="high">Tinggi</option>
                            <option value="urgent">Mendesak</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">
                            Status <span class="text-red-500">*</span>
                        </label>
                        <select name="status" id="edit-status" required 
                                class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-blue-500">
                            <option value="todo">Belum Dimulai</option>
                            <option value="in_progress">Dalam Proses</option>
                            <option value="done">Selesai</option>
                            <option value="blocked">Terblokir</option>
                        </select>
                    </div>
                </div>

                <!-- Assignment & Due Date -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">
                            Ditugaskan Ke
                        </label>
                        <select name="assigned_user_id" id="edit-assigned-user" 
                                class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-blue-500">
                            <option value="">-- Pilih User --</option>
                            @php
                                $users = \App\Models\User::orderBy('name')->get();
                            @endphp
                            @foreach($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">
                            Deadline
                        </label>
                        <input type="date" name="due_date" id="edit-due-date" 
                               class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-blue-500">
                    </div>
                </div>

                <!-- Dependency -->
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">
                        Task Prasyarat
                    </label>
                    <select name="depends_on_task_id" id="edit-depends-on" 
                            class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-blue-500">
                        <option value="">-- Tidak Ada --</option>
                        @foreach($project->tasks->sortBy('sort_order') as $existingTask)
                            <option value="{{ $existingTask->id }}">
                                {{ $existingTask->sort_order }}. {{ $existingTask->title }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Related Permit -->
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">
                        <i class="fas fa-file-contract mr-1 text-blue-400"></i>
                        Terkait Izin/Pra Syarat
                    </label>
                    <select name="project_permit_id" id="edit-permit-id" 
                            class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-blue-500">
                        <option value="">-- Tidak Terkait Izin --</option>
                        @foreach($project->permits->sortBy('sequence_order') as $permit)
                            <option value="{{ $permit->id }}">
                                {{ $permit->sequence_order }}. {{ $permit->permitType->name ?? 'Unknown' }}
                                @if($permit->institution)
                                    - {{ $permit->institution->name }}
                                @endif
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Estimated Hours -->
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">
                        Estimasi Waktu (Jam)
                    </label>
                    <input type="number" name="estimated_hours" id="edit-estimated-hours" min="1" 
                           class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-blue-500"
                           placeholder="Contoh: 8">
                </div>

                <!-- SOP Notes -->
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">
                        SOP / Checklist
                    </label>
                    <textarea name="sop_notes" id="edit-sop-notes" rows="3" 
                              class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-blue-500"
                              placeholder="Tambahkan SOP atau checklist untuk task ini..."></textarea>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex gap-3 mt-6">
                <button type="button" @click.stop="closeEditTaskModal()"
                        class="flex-1 px-4 py-2 bg-gray-700 hover:bg-gray-600 text-white rounded-lg transition">
                    Batal
                </button>
                <button type="submit" 
                        class="flex-1 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">
                    <i class="fas fa-save mr-2"></i>Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<!-- SortableJS CDN -->
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>

<style>
/* OVERRIDE: Disable card-elevated hover effect for tasks tab container only */
.tasks-tab-no-hover.card-elevated:hover {
    background-color: #1C1C1E !important;
    border-color: rgba(84, 84, 88, 0.65) !important;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.48) !important;
    transform: none !important;
}

/* Sortable Styles */
.sortable-ghost {
    opacity: 0.4;
    background: rgba(10, 132, 255, 0.2);
}

.sortable-chosen {
    background: rgba(10, 132, 255, 0.1);
}

.sortable-drag {
    opacity: 1;
    cursor: grabbing !important;
}

/* Fix hover isolation */
#tasks-sortable > [data-task-id] {
    isolation: isolate;
}

#tasks-sortable button {
    position: relative;
    z-index: 1;
}

/* Hover effect for individual task cards */
.task-card:hover {
    background: rgba(58, 58, 60, 0.7) !important;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.4);
    transform: translateY(-2px);
}

/* Drag Handle Hover */
.drag-handle:hover {
    cursor: grab;
}

.drag-handle:active {
    cursor: grabbing;
}
</style>

<script>
console.log('🚀 Tasks Tab JavaScript Loading...');

// Store task data
let currentTaskData = null;
let allTasks = @json($project->tasks->sortBy('sort_order')->values());

console.log('📊 All Tasks Loaded:', allTasks.length);

// ===== MODAL FUNCTIONS =====

function showAddTaskModal() {
    console.log('Opening Add Task Modal');
    const modal = document.getElementById('add-task-modal');
    if (!modal) {
        console.error('Add Task modal not found!');
        return;
    }
    modal.classList.remove('hidden');
    modal.style.display = 'flex';
    modal.style.alignItems = 'center';
    modal.style.justifyContent = 'center';
}

function closeAddTaskModal() {
    console.log('Closing Add Task Modal');
    const modal = document.getElementById('add-task-modal');
    if (!modal) return;
    modal.classList.add('hidden');
    modal.style.display = 'none';
}

function editTask(taskId) {
    console.log('Opening Edit Task Modal for task:', taskId);
    
    // Fetch task data via AJAX
    fetch(`/tasks/${taskId}`, {
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
        .then(response => response.json())
        .then(task => {
            // Populate form fields
            document.getElementById('edit-title').value = task.title || '';
            document.getElementById('edit-description').value = task.description || '';
            document.getElementById('edit-priority').value = task.priority || 'normal';
            document.getElementById('edit-status').value = task.status || 'todo';
            document.getElementById('edit-assigned-user').value = task.assigned_user_id || '';
            document.getElementById('edit-due-date').value = task.due_date || '';
            document.getElementById('edit-depends-on').value = task.depends_on_task_id || '';
            document.getElementById('edit-permit-id').value = task.project_permit_id || '';
            document.getElementById('edit-estimated-hours').value = task.estimated_hours || '';
            document.getElementById('edit-sop-notes').value = task.sop_notes || '';
            
            // Set form action
            document.getElementById('edit-task-form').action = `/tasks/${taskId}`;
            
            // Show modal
            const modal = document.getElementById('edit-task-modal');
            modal.classList.remove('hidden');
            modal.style.display = 'flex';
            modal.style.alignItems = 'center';
            modal.style.justifyContent = 'center';
        })
        .catch(error => {
            console.error('Error fetching task:', error);
            alert('Gagal memuat data task');
        });
}

function closeEditTaskModal() {
    console.log('Closing Edit Task Modal');
    const modal = document.getElementById('edit-task-modal');
    if (!modal) return;
    modal.classList.add('hidden');
    modal.style.display = 'none';
    document.getElementById('edit-task-form').reset();
}

function updateTaskStatus(taskId) {
    // Get task data from DOM
    const taskCard = document.querySelector(`[data-task-id="${taskId}"]`);
    if (!taskCard) {
        alert('Task data not found');
        return;
    }
    
    // Extract task data
    currentTaskData = {
        id: taskId,
        sortOrder: taskCard.dataset.sortOrder || '',
        title: taskCard.dataset.taskTitle || '',
        currentStatus: taskCard.dataset.status || '',
        canStart: taskCard.dataset.canStart === 'true',
        blockers: taskCard.dataset.blockers ? JSON.parse(taskCard.dataset.blockers) : []
    };
    
    // Populate modal
    document.getElementById('task-sort-order').textContent = currentTaskData.sortOrder;
    document.getElementById('task-title').textContent = currentTaskData.title;
    document.getElementById('task-current-status').textContent = 'Status saat ini: ' + getStatusLabel(currentTaskData.currentStatus);
    
    // Set form action
    document.getElementById('update-status-form').action = `/tasks/${taskId}/status`;
    
    // Show/hide dependency warning
    checkDependencyWarning();
    
    // Show modal
    const modal = document.getElementById('update-status-modal');
    modal.classList.remove('hidden');
    modal.style.display = 'flex';
    modal.style.alignItems = 'center';
    modal.style.justifyContent = 'center';
}

function closeUpdateStatusModal() {
    const modal = document.getElementById('update-status-modal');
    modal.classList.add('hidden');
    modal.style.display = 'none';
    document.getElementById('update-status-form').reset();
    document.getElementById('dependency-warning').classList.add('hidden');
    document.getElementById('completion-notes-section').classList.add('hidden');
    currentTaskData = null;
}

function checkDependency() {
    const newStatus = document.getElementById('new-status').value;
    const completionSection = document.getElementById('completion-notes-section');
    
    // Show completion notes when status is done
    if (newStatus === 'done') {
        completionSection.classList.remove('hidden');
    } else {
        completionSection.classList.add('hidden');
    }
    
    checkDependencyWarning();
}

function checkDependencyWarning() {
    const newStatus = document.getElementById('new-status').value;
    const dependencyWarning = document.getElementById('dependency-warning');
    const blockerList = document.getElementById('blocker-list');
    
    if (!currentTaskData) return;
    
    // Show warning if trying to start task with unmet dependencies
    const needsWarning = !currentTaskData.canStart && 
                        (newStatus === 'in_progress' || newStatus === 'done') &&
                        currentTaskData.blockers.length > 0;
    
    if (needsWarning) {
        blockerList.innerHTML = '';
        currentTaskData.blockers.forEach(blocker => {
            const li = document.createElement('li');
            li.textContent = blocker;
            blockerList.appendChild(li);
        });
        dependencyWarning.classList.remove('hidden');
    } else {
        dependencyWarning.classList.add('hidden');
    }
}

function deleteTask(taskId) {
    if (!confirm('Yakin ingin menghapus task ini?\\n\\nPerhatian: Jika task ini menjadi prasyarat task lain, penghapusan akan gagal.')) {
        return;
    }
    
    // Create form and submit with redirect back to tasks tab
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = `/tasks/${taskId}`;
    
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
    
    form.innerHTML = `
        <input type="hidden" name="_token" value="${csrfToken}">
        <input type="hidden" name="_method" value="DELETE">
        <input type="hidden" name="redirect_to_tab" value="tasks">
    `;
    
    document.body.appendChild(form);
    form.submit();
}

function getStatusLabel(status) {
    const labels = {
        'todo': 'Belum Dimulai',
        'in_progress': 'Dalam Proses',
        'done': 'Selesai',
        'blocked': 'Terblokir'
    };
    return labels[status] || status;
}

// ===== DRAG AND DROP REORDERING =====

document.addEventListener('DOMContentLoaded', function() {
    console.log('🎯 Initializing Task Drag and Drop...');
    
    if (typeof Sortable === 'undefined') {
        console.error('❌ SortableJS not loaded!');
        return;
    }
    
    const sortableContainer = document.getElementById('tasks-sortable');
    if (!sortableContainer) {
        console.log('ℹ️ No sortable container found');
        return;
    }
    
    const taskItems = sortableContainer.querySelectorAll('[data-task-id]');
    console.log('📋 Task Items Found:', taskItems.length);
    
    if (taskItems.length > 0) {
        try {
            new Sortable(sortableContainer, {
                animation: 200,
                handle: '.drag-handle',
                draggable: '[data-task-id]',
                ghostClass: 'sortable-ghost',
                chosenClass: 'sortable-chosen',
                dragClass: 'sortable-drag',
                
                onEnd: function(evt) {
                    console.log('🔄 Drag ended, saving new order...');
                    const newOrder = [];
                    const items = sortableContainer.querySelectorAll('[data-task-id]');
                    
                    items.forEach((item, index) => {
                        const taskId = item.dataset.taskId;
                        newOrder.push({
                            id: parseInt(taskId),
                            sort_order: index + 1
                        });
                    });
                    
                    console.log('📊 New Order:', newOrder);
                    
                    // Update sort order badges visually
                    items.forEach((item, index) => {
                        const badge = item.querySelector('.flex-shrink-0.w-10.h-10');
                        if (badge) {
                            badge.textContent = index + 1;
                        }
                    });
                    
                    // Send to server
                    saveNewTaskOrder(newOrder);
                }
            });
            console.log('✅ Task Sortable initialized successfully!');
        } catch (error) {
            console.error('❌ Error initializing Sortable:', error);
        }
    }
});

function saveNewTaskOrder(newOrder) {
    console.log('💾 Saving new task order to server...');
    
    const csrfMeta = document.querySelector('meta[name="csrf-token"]');
    if (!csrfMeta) {
        console.error('❌ CSRF meta tag not found!');
        showNotification('Error: CSRF token tidak ditemukan', 'error');
        return;
    }
    
    const csrfToken = csrfMeta.content;
    const projectId = {{ $project->id }};
    
    console.log('🔑 CSRF Token:', csrfToken);
    console.log('📦 Project ID:', projectId);
    
    fetch(`/projects/${projectId}/tasks/reorder`, {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
        },
        body: JSON.stringify({ tasks: newOrder })
    })
    .then(response => {
        console.log('📡 Response received:', response.status);
        
        const contentType = response.headers.get('content-type');
        if (contentType && contentType.includes('application/json')) {
            return response.json();
        } else if (response.ok) {
            return { success: true };
        } else {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }
    })
    .then(data => {
        console.log('✅ Response data:', data);
        if (data.success) {
            showNotification('Urutan task berhasil diperbarui', 'success');
        } else {
            showNotification('Gagal memperbarui urutan: ' + (data.message || 'Unknown error'), 'error');
            setTimeout(() => location.reload(), 2000);
        }
    })
    .catch(error => {
        console.error('❌ Error:', error);
        showNotification('Gagal memperbarui urutan: ' + error.message, 'error');
        setTimeout(() => location.reload(), 2000);
    });
}

function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = 'fixed top-4 right-4 px-6 py-4 rounded-lg shadow-lg z-50';
    
    const colors = {
        success: 'bg-green-600',
        error: 'bg-red-600',
        info: 'bg-blue-600'
    };
    
    const icons = {
        success: 'fa-check-circle',
        error: 'fa-exclamation-circle',
        info: 'fa-info-circle'
    };
    
    notification.className += ` ${colors[type]}`;
    notification.innerHTML = `
        <div class="flex items-center text-white">
            <i class="fas ${icons[type]} mr-3"></i>
            <span>${message}</span>
        </div>
    `;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.remove();
    }, 3000);
}

</script>
@endpush
