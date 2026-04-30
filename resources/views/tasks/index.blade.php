@extends('layouts.app')

@section('title', 'Tugas')
@section('page-title', 'Manajemen Tugas')

@section('content')
    {{-- Compact Hero Section --}}
    <section class="card-elevated rounded-apple-lg admin-hero relative overflow-hidden mb-4">
        <div class="absolute inset-0 pointer-events-none" aria-hidden="true">
            <div class="w-48 h-48 bg-apple-blue opacity-20 blur-3xl rounded-full absolute -top-10 -right-6"></div>
            <div class="w-32 h-32 bg-apple-green opacity-15 blur-2xl rounded-full absolute bottom-0 left-6"></div>
        </div>
        <div class="relative space-y-3">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3">
                <div class="space-y-1 max-w-3xl">
                    <p class="admin-label-compact">Manajemen Tugas</p>
                    <h1 class="admin-hero-title">Kelola dan Lacak Semua Tugas Proyek</h1>
                    <p class="admin-body text-dark-text-secondary">Organisir tugas dengan prioritas yang jelas, tetapkan penanggung jawab, dan pantau kemajuan setiap aktivitas.</p>
                </div>
                <div>
                    <a href="{{ route('tasks.create') }}" class="admin-btn inline-flex items-center">
                        <i class="fas fa-plus mr-1.5"></i>Tambah Tugas
                    </a>
                </div>
            </div>

            {{-- Compact Stats Cards --}}
            <div class="grid grid-cols-2 md:grid-cols-4 gap-2">
                @php
                    $totalTasks = $tasks->total();
                    $pendingCount = $tasks->whereIn('status', ['todo', 'in_progress'])->count();
                    $completedCount = $tasks->where('status', 'done')->count();
                    $overdueCount = $tasks->filter(function($task) {
                        return $task->isOverdue() && $task->status !== 'done';
                    })->count();
                @endphp
                <div class="admin-stat-card bg-apple-blue/12">
                    <div class="flex items-center gap-2">
                        <div class="w-7 h-7 rounded-lg flex items-center justify-center bg-apple-blue/25">
                            <i class="fas fa-tasks text-xs text-apple-blue"></i>
                        </div>
                        <div>
                            <p class="admin-stat text-white">{{ $totalTasks }}</p>
                            <p class="admin-label-compact">Total Tugas</p>
                        </div>
                    </div>
                </div>
                <div class="admin-stat-card bg-apple-orange/12">
                    <div class="flex items-center gap-2">
                        <div class="w-7 h-7 rounded-lg flex items-center justify-center bg-apple-orange/25">
                            <i class="fas fa-spinner text-xs text-apple-orange"></i>
                        </div>
                        <div>
                            <p class="admin-stat text-apple-orange">{{ $pendingCount }}</p>
                            <p class="admin-label-compact">Dalam Proses</p>
                        </div>
                    </div>
                </div>
                <div class="admin-stat-card bg-apple-green/12">
                    <div class="flex items-center gap-2">
                        <div class="w-7 h-7 rounded-lg flex items-center justify-center bg-apple-green/25">
                            <i class="fas fa-check text-xs text-apple-green"></i>
                        </div>
                        <div>
                            <p class="admin-stat text-apple-green">{{ $completedCount }}</p>
                            <p class="admin-label-compact">Selesai</p>
                        </div>
                    </div>
                </div>
                <div class="admin-stat-card bg-apple-red/12">
                    <div class="flex items-center gap-2">
                        <div class="w-7 h-7 rounded-lg flex items-center justify-center bg-apple-red/25">
                            <i class="fas fa-exclamation text-xs text-apple-red"></i>
                        </div>
                        <div>
                            <p class="admin-stat text-apple-red">{{ $overdueCount }}</p>
                            <p class="admin-label-compact">Terlambat</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Compact Search and Filter --}}
    <div class="card-elevated rounded-apple p-3 mb-3">
        <form method="GET" action="{{ route('tasks.index') }}" class="flex flex-wrap gap-2 items-end">
            <div class="flex-1 min-w-[150px]">
                <label class="admin-label-compact block">Cari</label>
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Judul tugas..." 
                           class="admin-input w-full pl-7 rounded bg-dark-bg-secondary border border-dark-separator text-white">
                    <i class="fas fa-search absolute left-2.5 top-1/2 transform -translate-y-1/2 text-dark-text-tertiary text-[10px]"></i>
                </div>
            </div>
            <div class="w-28">
                <label class="admin-label-compact block">Status</label>
                <select name="status" class="admin-input admin-select w-full rounded bg-dark-bg-secondary border border-dark-separator text-white">
                    <option value="">Semua</option>
                    <option value="todo" {{ request('status') == 'todo' ? 'selected' : '' }}>Belum</option>
                    <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>Proses</option>
                    <option value="done" {{ request('status') == 'done' ? 'selected' : '' }}>Selesai</option>
                    <option value="blocked" {{ request('status') == 'blocked' ? 'selected' : '' }}>Terblokir</option>
                </select>
            </div>
            <div class="w-28">
                <label class="admin-label-compact block">Prioritas</label>
                <select name="priority" class="admin-input admin-select w-full rounded bg-dark-bg-secondary border border-dark-separator text-white">
                    <option value="">Semua</option>
                    <option value="urgent" {{ request('priority') == 'urgent' ? 'selected' : '' }}>Mendesak</option>
                    <option value="high" {{ request('priority') == 'high' ? 'selected' : '' }}>Tinggi</option>
                    <option value="normal" {{ request('priority') == 'normal' ? 'selected' : '' }}>Normal</option>
                    <option value="low" {{ request('priority') == 'low' ? 'selected' : '' }}>Rendah</option>
                </select>
            </div>
            <div class="w-36">
                <label class="admin-label-compact block">Proyek</label>
                <select name="project_id" class="admin-input admin-select w-full rounded bg-dark-bg-secondary border border-dark-separator text-white">
                    <option value="">Semua</option>
                    @isset($projects)
                        @foreach($projects as $project)
                            <option value="{{ $project->id }}" {{ request('project_id') == $project->id ? 'selected' : '' }}>{{ $project->name }}</option>
                        @endforeach
                    @endisset
                </select>
            </div>
        </form>
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('form[action="{{ route('tasks.index') }}"]');
            if (!form) return;
            form.querySelectorAll('select[name]').forEach(el => el.addEventListener('change', () => form.submit()));
            const searchInput = form.querySelector('input[name="search"]');
            if (searchInput) searchInput.addEventListener('keydown', e => { if (e.key === 'Enter') { e.preventDefault(); form.submit(); }});
        });
        </script>
    </div>

    <!-- Tasks Table -->
    <div class="card-elevated rounded-apple-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-700">
                <thead class="bg-dark-bg-secondary">
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
                <tbody class="divide-y divide-gray-700 bg-dark-bg-secondary">
                    @forelse($tasks as $task)
                        @php
                            // Status configuration
                            $statusConfig = [
                                'todo' => ['label' => 'Belum Dikerjakan', 'icon' => 'fa-circle', 'cls' => 'bg-apple-orange/15 text-apple-orange'],
                                'in_progress' => ['label' => 'Sedang Dikerjakan', 'icon' => 'fa-spinner', 'cls' => 'bg-apple-blue/15 text-apple-blue'],
                                'done' => ['label' => 'Selesai', 'icon' => 'fa-check-circle', 'cls' => 'bg-apple-green/15 text-apple-green'],
                                'blocked' => ['label' => 'Terblokir', 'icon' => 'fa-ban', 'cls' => 'bg-apple-red/15 text-apple-red'],
                            ];
                            $status = $statusConfig[$task->status] ?? $statusConfig['todo'];

                            // Priority configuration
                            $priorityConfig = [
                                'urgent' => ['label' => 'Mendesak', 'icon' => 'fa-exclamation-circle', 'cls' => 'bg-apple-red/15 text-apple-red'],
                                'high' => ['label' => 'Tinggi', 'icon' => 'fa-arrow-up', 'cls' => 'bg-apple-orange/15 text-apple-orange'],
                                'normal' => ['label' => 'Normal', 'icon' => 'fa-minus', 'cls' => 'bg-apple-blue/15 text-apple-blue'],
                                'low' => ['label' => 'Rendah', 'icon' => 'fa-arrow-down', 'cls' => 'bg-dark-text-tertiary/15 text-dark-text-tertiary'],
                            ];
                            $priority = $priorityConfig[$task->priority] ?? $priorityConfig['normal'];

                            // Check if overdue
                            $isOverdue = $task->isOverdue() && $task->status !== 'done';
                            $rowStyle = $isOverdue ? 'border-left: 3px solid rgba(255, 59, 48, 1);' : '';
                        @endphp

                        <tr class="hover-lift transition-apple cursor-pointer" style="{{ $rowStyle }}" onclick="window.location='{{ route('tasks.show', $task) }}'">
                            <!-- Tugas Info -->
                            <td class="px-4 py-3">
                                <div>
                                    <div class="font-semibold text-sm text-dark-text-primary">{{ $task->title }}</div>
                                    @if($task->project)
                                        <div class="flex items-center mt-1">
                                            <i class="fas fa-folder text-xs mr-1.5 text-dark-text-secondary"></i>
                                            <a href="{{ route('projects.show', $task->project) }}" 
                                               onclick="event.stopPropagation()"
                                               class="text-xs hover:underline text-apple-blue">
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
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium {{ $status['cls'] }}">
                                    <i class="fas {{ $status['icon'] }} mr-1.5"></i>
                                    {{ $status['label'] }}
                                </span>
                            </td>

                            <!-- Prioritas -->
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium {{ $priority['cls'] }}">
                                    <i class="fas {{ $priority['icon'] }} mr-1.5"></i>
                                    {{ $priority['label'] }}
                                </span>
                            </td>

                            <!-- Assigned To -->
                            <td class="px-4 py-3 whitespace-nowrap">
                                @if($task->assignedUser)
                                    <div class="flex items-center">
                                        <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-semibold mr-2 bg-apple-blue/15 text-apple-blue">
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
                                            <div class="text-xs mt-0.5 text-apple-red">
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
                                        <div class="w-full h-1.5 rounded-full bg-white/8">
                                            <div class="h-full rounded-full transition-all" style="width: {{ $progress }}%; background-color: {{ $progressColor }};"></div>
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <!-- Aksi -->
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-center space-x-2" onclick="event.stopPropagation();">
                                    <a href="{{ route('tasks.show', $task) }}" 
                                       class="p-2 rounded-apple transition-apple text-apple-blue bg-apple-blue/10 border border-apple-blue/30 hover:bg-apple-blue hover:text-white">
                                        <i class="fas fa-eye text-sm"></i>
                                    </a>
                                    <a href="{{ route('tasks.edit', $task) }}" 
                                       class="p-2 rounded-apple transition-apple text-apple-orange bg-apple-orange/10 border border-apple-orange/30 hover:bg-apple-orange hover:text-white">
                                        <i class="fas fa-edit text-sm"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center">
                                <div class="flex flex-col items-center justify-center text-dark-text-secondary">
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
            <div class="px-4 py-3 border-t border-white/10 bg-dark-bg-secondary">
                {{ $tasks->links() }}
            </div>
        @endif
    </div>
@endsection
