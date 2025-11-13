@extends('layouts.app')

@section('title', 'Tugas')
@section('page-title', 'Manajemen Tugas')

@section('content')
    <!-- Header Actions -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4 space-y-3 sm:space-y-0">
        <div>
            <p class="text-sm" style="color: rgba(235, 235, 245, 0.6);">Kelola dan pantau semua tugas dalam proyek</p>
        </div>
        <div class="flex items-center space-x-3">
            <button type="button" class="px-4 py-2 rounded-apple text-sm font-medium transition-apple" 
                    style="background-color: #2C2C2E; color: rgba(235, 235, 245, 0.6); border: 1px solid rgba(84, 84, 88, 0.65); box-shadow: 0 2px 8px rgba(0, 0, 0, 0.48);" 
                    onmouseover="this.style.backgroundColor='#3A3A3C'; this.style.color='#FFFFFF'" 
                    onmouseout="this.style.backgroundColor='#2C2C2E'; this.style.color='rgba(235, 235, 245, 0.6)'">
                <i class="fas fa-filter mr-2"></i>
                Filter
            </button>
            <a href="{{ route('tasks.create') }}" 
               class="btn-primary px-4 py-2 text-white rounded-apple text-sm font-medium">
                <i class="fas fa-plus mr-2"></i>
                Tambah Tugas
            </a>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
        @php
            $totalTasks = $tasks->total();
            $pendingCount = $tasks->whereIn('status', ['todo', 'in_progress'])->count();
            $completedCount = $tasks->where('status', 'done')->count();
            $overdueCount = $tasks->filter(function($task) {
                return $task->isOverdue() && $task->status !== 'done';
            })->count();
        @endphp

        <!-- Total Tugas -->
        <div class="card-elevated rounded-apple-lg p-4">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-xs font-medium" style="color: rgba(235, 235, 245, 0.6);">Total Tugas</div>
                    <div class="text-2xl font-bold mt-1" style="color: #FFFFFF;">{{ $totalTasks }}</div>
                </div>
                <div class="w-12 h-12 rounded-full flex items-center justify-center" style="background-color: rgba(0, 122, 255, 0.15);">
                    <i class="fas fa-tasks text-xl" style="color: rgba(0, 122, 255, 1);"></i>
                </div>
            </div>
        </div>

        <!-- Pending -->
        <div class="card-elevated rounded-apple-lg p-4">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-xs font-medium" style="color: rgba(235, 235, 245, 0.6);">Pending</div>
                    <div class="text-2xl font-bold mt-1" style="color: #FFFFFF;">{{ $pendingCount }}</div>
                </div>
                <div class="w-12 h-12 rounded-full flex items-center justify-center" style="background-color: rgba(255, 159, 10, 0.15);">
                    <i class="fas fa-spinner text-xl" style="color: rgba(255, 159, 10, 1);"></i>
                </div>
            </div>
        </div>

        <!-- Selesai -->
        <div class="card-elevated rounded-apple-lg p-4">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-xs font-medium" style="color: rgba(235, 235, 245, 0.6);">Selesai</div>
                    <div class="text-2xl font-bold mt-1" style="color: #FFFFFF;">{{ $completedCount }}</div>
                </div>
                <div class="w-12 h-12 rounded-full flex items-center justify-center" style="background-color: rgba(52, 199, 89, 0.15);">
                    <i class="fas fa-check-circle text-xl" style="color: rgba(52, 199, 89, 1);"></i>
                </div>
            </div>
        </div>

        <!-- Terlambat -->
        <div class="card-elevated rounded-apple-lg p-4">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-xs font-medium" style="color: rgba(235, 235, 245, 0.6);">Terlambat</div>
                    <div class="text-2xl font-bold mt-1" style="color: #FFFFFF;">{{ $overdueCount }}</div>
                </div>
                <div class="w-12 h-12 rounded-full flex items-center justify-center" style="background-color: rgba(255, 59, 48, 0.15);">
                    <i class="fas fa-exclamation-triangle text-xl" style="color: rgba(255, 59, 48, 1);"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Search and Filter Card -->
    <div class="card-elevated rounded-apple-lg mb-4 overflow-hidden">
        <div class="px-4 py-3" style="border-bottom: 1px solid rgba(84, 84, 88, 0.65);">
            <h3 class="text-base font-semibold" style="color: #FFFFFF;">Pencarian & Filter</h3>
        </div>
        <div class="p-4">
            <form method="GET" action="{{ route('tasks.index') }}" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3">
                    <!-- Search -->
                    <div>
                        <label class="block text-xs font-medium mb-1.5" style="color: rgba(235, 235, 245, 0.6);">Pencarian</label>
                        <div class="relative">
                            <input type="text" 
                                   name="search" 
                                   value="{{ request('search') }}" 
                                   placeholder="Judul atau deskripsi tugas..." 
                                   class="input-dark w-full pl-9 pr-3 py-2 rounded-apple text-sm">
                            <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-sm" style="color: rgba(235, 235, 245, 0.3);"></i>
                        </div>
                    </div>

                    <!-- Status -->
                    <div>
                        <label class="block text-xs font-medium mb-1.5" style="color: rgba(235, 235, 245, 0.6);">Status</label>
                        <select name="status" class="input-dark w-full px-3 py-2 rounded-apple text-sm">
                            <option value="">Semua Status</option>
                            <option value="todo" {{ request('status') == 'todo' ? 'selected' : '' }}>To Do</option>
                            <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                            <option value="done" {{ request('status') == 'done' ? 'selected' : '' }}>Done</option>
                            <option value="blocked" {{ request('status') == 'blocked' ? 'selected' : '' }}>Blocked</option>
                        </select>
                    </div>

                    <!-- Priority -->
                    <div>
                        <label class="block text-xs font-medium mb-1.5" style="color: rgba(235, 235, 245, 0.6);">Prioritas</label>
                        <select name="priority" class="input-dark w-full px-3 py-2 rounded-apple text-sm">
                            <option value="">Semua Prioritas</option>
                            <option value="urgent" {{ request('priority') == 'urgent' ? 'selected' : '' }}>Urgent</option>
                            <option value="high" {{ request('priority') == 'high' ? 'selected' : '' }}>High</option>
                            <option value="normal" {{ request('priority') == 'normal' ? 'selected' : '' }}>Normal</option>
                            <option value="low" {{ request('priority') == 'low' ? 'selected' : '' }}>Low</option>
                        </select>
                    </div>

                    <!-- Project -->
                    <div>
                        <label class="block text-xs font-medium mb-1.5" style="color: rgba(235, 235, 245, 0.6);">Proyek</label>
                        <select name="project_id" class="input-dark w-full px-3 py-2 rounded-apple text-sm">
                            <option value="">Semua Proyek</option>
                            @isset($projects)
                                @foreach($projects as $project)
                                    <option value="{{ $project->id }}" {{ request('project_id') == $project->id ? 'selected' : '' }}>
                                        {{ $project->name }}
                                    </option>
                                @endforeach
                            @endisset
                        </select>
                    </div>
                </div>
                <script>
                // Auto submit form on filter change
                document.addEventListener('DOMContentLoaded', function() {
                    const form = document.querySelector('form[action="{{ route('tasks.index') }}"]');
                    if (!form) return;
                    
                    const searchInput = form.querySelector('input[name="search"]');
                    
                    // Auto-submit for select dropdowns
                    form.querySelectorAll('select[name]').forEach(function(el) {
                        el.addEventListener('change', function() {
                            form.submit();
                        });
                    });
                    
                    // Submit search on Enter key only
                    if (searchInput) {
                        searchInput.addEventListener('keydown', function(e) {
                            if (e.key === 'Enter') {
                                e.preventDefault();
                                e.stopPropagation();
                                form.submit();
                            }
                        });
                    }
                });
                </script>
            </form>
        </div>
    </div>

    <!-- Tasks Table -->
    <div class="card-elevated rounded-apple-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-700">
                <thead style="background-color: var(--dark-bg-secondary);">
                    <tr>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-dark-text-secondary">Tugas</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-dark-text-secondary">Status</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-dark-text-secondary">Prioritas</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-dark-text-secondary">Assigned To</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-dark-text-secondary">Deadline</th>
                        <th scope="col" class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wider text-dark-text-secondary">Progress</th>
                        <th scope="col" class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wider text-dark-text-secondary">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-700" style="background-color: var(--dark-bg-secondary);">
                    @forelse($tasks as $task)
                        @php
                            // Status configuration
                            $statusConfig = [
                                'todo' => ['label' => 'To Do', 'icon' => 'fa-circle', 'color' => 'rgba(255, 159, 10, 1)', 'bg' => 'rgba(255, 159, 10, 0.15)'],
                                'in_progress' => ['label' => 'In Progress', 'icon' => 'fa-spinner', 'color' => 'rgba(0, 122, 255, 1)', 'bg' => 'rgba(0, 122, 255, 0.15)'],
                                'done' => ['label' => 'Done', 'icon' => 'fa-check-circle', 'color' => 'rgba(52, 199, 89, 1)', 'bg' => 'rgba(52, 199, 89, 0.15)'],
                                'blocked' => ['label' => 'Blocked', 'icon' => 'fa-ban', 'color' => 'rgba(255, 59, 48, 1)', 'bg' => 'rgba(255, 59, 48, 0.15)'],
                            ];
                            $status = $statusConfig[$task->status] ?? $statusConfig['todo'];

                            // Priority configuration
                            $priorityConfig = [
                                'urgent' => ['label' => 'Urgent', 'icon' => 'fa-exclamation-circle', 'color' => 'rgba(255, 59, 48, 1)', 'bg' => 'rgba(255, 59, 48, 0.15)'],
                                'high' => ['label' => 'High', 'icon' => 'fa-arrow-up', 'color' => 'rgba(255, 149, 0, 1)', 'bg' => 'rgba(255, 149, 0, 0.15)'],
                                'normal' => ['label' => 'Normal', 'icon' => 'fa-minus', 'color' => 'rgba(0, 122, 255, 1)', 'bg' => 'rgba(0, 122, 255, 0.15)'],
                                'low' => ['label' => 'Low', 'icon' => 'fa-arrow-down', 'color' => 'rgba(142, 142, 147, 1)', 'bg' => 'rgba(142, 142, 147, 0.15)'],
                            ];
                            $priority = $priorityConfig[$task->priority] ?? $priorityConfig['normal'];

                            // Check if overdue
                            $isOverdue = $task->isOverdue() && $task->status !== 'done';
                            $rowStyle = $isOverdue ? 'border-left: 3px solid rgba(255, 59, 48, 1);' : '';
                        @endphp

                        <tr class="hover-lift transition-apple" style="cursor: pointer; {{ $rowStyle }}" onclick="window.location='{{ route('tasks.show', $task) }}'">
                            <!-- Tugas Info -->
                            <td class="px-4 py-3">
                                <div>
                                    <div class="font-semibold text-sm text-dark-text-primary">{{ $task->title }}</div>
                                    @if($task->project)
                                        <div class="flex items-center mt-1">
                                            <i class="fas fa-folder text-xs mr-1.5" style="color: rgba(235, 235, 245, 0.6);"></i>
                                            <a href="{{ route('projects.show', $task->project) }}" 
                                               onclick="event.stopPropagation()"
                                               class="text-xs hover:underline" 
                                               style="color: rgba(0, 122, 255, 1);">
                                                {{ $task->project->name }}
                                            </a>
                                        </div>
                                    @endif
                                    @if($task->description)
                                        <div class="text-xs text-dark-text-secondary mt-1 line-clamp-1">
                                            {{ Str::limit($task->description, 80) }}
                                        </div>
                                    @endif
                                </div>
                            </td>

                            <!-- Status -->
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium" 
                                      style="background-color: {{ $status['bg'] }}; color: {{ $status['color'] }};">
                                    <i class="fas {{ $status['icon'] }} mr-1.5"></i>
                                    {{ $status['label'] }}
                                </span>
                            </td>

                            <!-- Prioritas -->
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium" 
                                      style="background-color: {{ $priority['bg'] }}; color: {{ $priority['color'] }};">
                                    <i class="fas {{ $priority['icon'] }} mr-1.5"></i>
                                    {{ $priority['label'] }}
                                </span>
                            </td>

                            <!-- Assigned To -->
                            <td class="px-4 py-3 whitespace-nowrap">
                                @if($task->assignedUser)
                                    <div class="flex items-center">
                                        <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-semibold mr-2" 
                                             style="background-color: rgba(0, 122, 255, 0.15); color: rgba(0, 122, 255, 1);">
                                            {{ strtoupper(substr($task->assignedUser->name, 0, 2)) }}
                                        </div>
                                        <span class="text-sm text-dark-text-primary">{{ $task->assignedUser->name }}</span>
                                    </div>
                                @else
                                    <span class="text-sm text-dark-text-secondary">Unassigned</span>
                                @endif
                            </td>

                            <!-- Deadline -->
                            <td class="px-4 py-3 whitespace-nowrap">
                                @if($task->due_date)
                                    <div class="text-sm">
                                        <div class="text-dark-text-primary">
                                            {{ \Carbon\Carbon::parse($task->due_date)->format('d M Y') }}
                                        </div>
                                        @if($isOverdue)
                                            <div class="text-xs mt-0.5" style="color: rgba(255, 59, 48, 1);">
                                                <i class="fas fa-exclamation-circle mr-1"></i>
                                                Terlambat {{ \Carbon\Carbon::parse($task->due_date)->diffForHumans() }}
                                            </div>
                                        @else
                                            <div class="text-xs text-dark-text-secondary mt-0.5">
                                                {{ \Carbon\Carbon::parse($task->due_date)->diffForHumans() }}
                                            </div>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-sm text-dark-text-secondary">-</span>
                                @endif
                            </td>

                            <!-- Progress -->
                            <td class="px-4 py-3">
                                @php
                                    $progress = $task->getProgress();
                                    $progressColor = $progress >= 80 ? 'rgba(52, 199, 89, 1)' : ($progress >= 50 ? 'rgba(0, 122, 255, 1)' : 'rgba(255, 159, 10, 1)');
                                @endphp
                                <div class="flex items-center justify-center">
                                    <div class="w-full max-w-[80px]">
                                        <div class="flex items-center justify-between mb-1">
                                            <span class="text-xs font-medium" style="color: {{ $progressColor }};">{{ $progress }}%</span>
                                        </div>
                                        <div class="w-full h-1.5 rounded-full" style="background-color: rgba(142, 142, 147, 0.2);">
                                            <div class="h-full rounded-full transition-all" style="width: {{ $progress }}%; background-color: {{ $progressColor }};"></div>
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <!-- Aksi -->
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-center space-x-2" onclick="event.stopPropagation();">
                                    <a href="{{ route('tasks.show', $task) }}" 
                                       class="p-2 rounded-apple transition-apple" 
                                       style="color: #0A84FF; background-color: rgba(10, 132, 255, 0.1); border: 1px solid rgba(10, 132, 255, 0.3);" 
                                       onmouseover="this.style.backgroundColor='#0A84FF'; this.style.color='#FFFFFF'" 
                                       onmouseout="this.style.backgroundColor='rgba(10, 132, 255, 0.1)'; this.style.color='#0A84FF'">
                                        <i class="fas fa-eye text-sm"></i>
                                    </a>
                                    <a href="{{ route('tasks.edit', $task) }}" 
                                       class="p-2 rounded-apple transition-apple" 
                                       style="color: #FF9F0A; background-color: rgba(255, 159, 10, 0.1); border: 1px solid rgba(255, 159, 10, 0.3);" 
                                       onmouseover="this.style.backgroundColor='#FF9F0A'; this.style.color='#FFFFFF'" 
                                       onmouseout="this.style.backgroundColor='rgba(255, 159, 10, 0.1)'; this.style.color='#FF9F0A'">
                                        <i class="fas fa-edit text-sm"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center">
                                <div class="flex flex-col items-center justify-center" style="color: rgba(235, 235, 245, 0.6);">
                                    <i class="fas fa-inbox text-4xl mb-3"></i>
                                    <p class="text-sm font-medium">Tidak ada tugas ditemukan</p>
                                    <p class="text-xs mt-1">Coba ubah filter atau tambahkan tugas baru</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($tasks->hasPages())
            <div class="px-4 py-3" style="border-top: 1px solid rgba(84, 84, 88, 0.65); background-color: var(--dark-bg-secondary);">
                {{ $tasks->links() }}
            </div>
        @endif
    </div>
@endsection
