@extends('layouts.app')

@section('title', 'Tugas')
@section('page-title', 'Manajemen Tugas')

@section('content')
    {{-- Hero Section --}}
    <section class="card-elevated rounded-apple-xl p-5 md:p-6 relative overflow-hidden mb-6">
        <div class="absolute inset-0 pointer-events-none" aria-hidden="true">
            <div class="w-72 h-72 bg-apple-blue opacity-30 blur-3xl rounded-full absolute -top-16 -right-10"></div>
            <div class="w-48 h-48 bg-apple-green opacity-20 blur-2xl rounded-full absolute bottom-0 left-10"></div>
        </div>
        <div class="relative space-y-5 md:space-y-6">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-5">
                <div class="space-y-2.5 max-w-3xl">
                    <p class="text-sm uppercase tracking-[0.4em]" style="color: rgba(235,235,245,0.5);">Manajemen Tugas</p>
                    <h1 class="text-2xl md:text-3xl font-bold" style="color: #FFFFFF;">
                        Kelola dan Lacak Semua Tugas Proyek
                    </h1>
                    <p class="text-sm md:text-base" style="color: rgba(235,235,245,0.75);">
                        Organisir tugas dengan prioritas yang jelas, tetapkan penanggung jawab, dan pantau kemajuan setiap aktivitas dalam proyek Anda.
                    </p>
                </div>
                <div class="space-y-2.5">
                    <a href="{{ route('tasks.create') }}" 
                       class="inline-flex items-center px-4 py-2 rounded-apple text-sm font-semibold" 
                       style="background: rgba(10,132,255,0.25); color: rgba(235,235,245,0.9);">
                        <i class="fas fa-plus mr-2"></i>
                        Tambah Tugas
                        <i class="fas fa-arrow-right ml-2 text-xs"></i>
                    </a>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3 md:gap-4">
                @php
                    $totalTasks = $tasks->total();
                    $pendingCount = $tasks->whereIn('status', ['todo', 'in_progress'])->count();
                    $completedCount = $tasks->where('status', 'done')->count();
                    $overdueCount = $tasks->filter(function($task) {
                        return $task->isOverdue() && $task->status !== 'done';
                    })->count();
                @endphp

                <!-- Total Tugas -->
                <div class="rounded-apple-lg p-3.5 md:p-4" style="background: rgba(10,132,255,0.12);">
                    <p class="text-xs uppercase tracking-widest" style="color: rgba(10,132,255,0.9);">Total Tugas</p>
                    <h2 class="text-2xl font-bold mt-1.5" style="color: #FFFFFF;">{{ $totalTasks }}</h2>
                    <p class="text-xs" style="color: rgba(235,235,245,0.6);">Semua tugas</p>
                </div>

                <!-- Pending -->
                <div class="rounded-apple-lg p-3.5 md:p-4" style="background: rgba(255,159,10,0.12);">
                    <p class="text-xs uppercase tracking-widest" style="color: rgba(255,159,10,0.9);">Dalam Proses</p>
                    <h2 class="text-2xl font-bold mt-1.5" style="color: rgba(255,159,10,1);">{{ $pendingCount }}</h2>
                    <p class="text-xs" style="color: rgba(235,235,245,0.6);">Sedang dikerjakan</p>
                </div>

                <!-- Selesai -->
                <div class="rounded-apple-lg p-3.5 md:p-4" style="background: rgba(52,199,89,0.12);">
                    <p class="text-xs uppercase tracking-widest" style="color: rgba(52,199,89,0.9);">Selesai</p>
                    <h2 class="text-2xl font-bold mt-1.5" style="color: rgba(52,199,89,1);">{{ $completedCount }}</h2>
                    <p class="text-xs" style="color: rgba(235,235,245,0.6);">Tugas tuntas</p>
                </div>

                <!-- Terlambat -->
                <div class="rounded-apple-lg p-3.5 md:p-4" style="background: rgba(255,59,48,0.12);">
                    <p class="text-xs uppercase tracking-widest" style="color: rgba(255,59,48,0.9);">Terlambat</p>
                    <h2 class="text-2xl font-bold mt-1.5" style="color: rgba(255,59,48,1);">{{ $overdueCount }}</h2>
                    <p class="text-xs" style="color: rgba(235,235,245,0.6);">Perlu perhatian</p>
                </div>
            </div>
        </div>
    </section>

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
                            <option value="todo" {{ request('status') == 'todo' ? 'selected' : '' }}>Belum Dikerjakan</option>
                            <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>Sedang Dikerjakan</option>
                            <option value="done" {{ request('status') == 'done' ? 'selected' : '' }}>Selesai</option>
                            <option value="blocked" {{ request('status') == 'blocked' ? 'selected' : '' }}>Terblokir</option>
                        </select>
                    </div>

                    <!-- Priority -->
                    <div>
                        <label class="block text-xs font-medium mb-1.5" style="color: rgba(235, 235, 245, 0.6);">Prioritas</label>
                        <select name="priority" class="input-dark w-full px-3 py-2 rounded-apple text-sm">
                            <option value="">Semua Prioritas</option>
                            <option value="urgent" {{ request('priority') == 'urgent' ? 'selected' : '' }}>Mendesak</option>
                            <option value="high" {{ request('priority') == 'high' ? 'selected' : '' }}>Tinggi</option>
                            <option value="normal" {{ request('priority') == 'normal' ? 'selected' : '' }}>Normal</option>
                            <option value="low" {{ request('priority') == 'low' ? 'selected' : '' }}>Rendah</option>
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
                        <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-dark-text-secondary">Ditugaskan Kepada</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-dark-text-secondary">Tenggat</th>
                        <th scope="col" class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wider text-dark-text-secondary">Kemajuan</th>
                        <th scope="col" class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wider text-dark-text-secondary">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-700" style="background-color: var(--dark-bg-secondary);">
                    @forelse($tasks as $task)
                        @php
                            // Status configuration
                            $statusConfig = [
                                'todo' => ['label' => 'Belum Dikerjakan', 'icon' => 'fa-circle', 'color' => 'rgba(255, 159, 10, 1)', 'bg' => 'rgba(255, 159, 10, 0.15)'],
                                'in_progress' => ['label' => 'Sedang Dikerjakan', 'icon' => 'fa-spinner', 'color' => 'rgba(0, 122, 255, 1)', 'bg' => 'rgba(0, 122, 255, 0.15)'],
                                'done' => ['label' => 'Selesai', 'icon' => 'fa-check-circle', 'color' => 'rgba(52, 199, 89, 1)', 'bg' => 'rgba(52, 199, 89, 0.15)'],
                                'blocked' => ['label' => 'Terblokir', 'icon' => 'fa-ban', 'color' => 'rgba(255, 59, 48, 1)', 'bg' => 'rgba(255, 59, 48, 0.15)'],
                            ];
                            $status = $statusConfig[$task->status] ?? $statusConfig['todo'];

                            // Priority configuration
                            $priorityConfig = [
                                'urgent' => ['label' => 'Mendesak', 'icon' => 'fa-exclamation-circle', 'color' => 'rgba(255, 59, 48, 1)', 'bg' => 'rgba(255, 59, 48, 0.15)'],
                                'high' => ['label' => 'Tinggi', 'icon' => 'fa-arrow-up', 'color' => 'rgba(255, 149, 0, 1)', 'bg' => 'rgba(255, 149, 0, 0.15)'],
                                'normal' => ['label' => 'Normal', 'icon' => 'fa-minus', 'color' => 'rgba(0, 122, 255, 1)', 'bg' => 'rgba(0, 122, 255, 0.15)'],
                                'low' => ['label' => 'Rendah', 'icon' => 'fa-arrow-down', 'color' => 'rgba(142, 142, 147, 1)', 'bg' => 'rgba(142, 142, 147, 0.15)'],
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
                                    <span class="text-sm text-dark-text-secondary">Belum Ditugaskan</span>
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
