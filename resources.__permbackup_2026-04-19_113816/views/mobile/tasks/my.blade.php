@extends('mobile.layouts.app')

@section('title', 'Task Saya')

@section('content')
<div class="pb-20">

    {{-- Header --}}
    <div class="bg-gradient-to-br from-[#0A66C2] to-[#004182] rounded-2xl p-6 mb-4 text-white">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h2 class="text-xl font-bold">Task Saya</h2>
                <p class="text-sm opacity-90">{{ $stats['all'] ?? 0 }} task aktif</p>
            </div>
            <div class="w-12 h-12 bg-white/10 rounded-xl flex items-center justify-center">
                <i class="fas fa-tasks text-2xl"></i>
            </div>
        </div>

        <div class="grid grid-cols-3 gap-2">
            <div class="bg-white/10 rounded-lg p-3 border border-white/20">
                <div class="text-2xl font-bold">{{ $stats['today'] ?? 0 }}</div>
                <div class="text-xs opacity-90">Hari Ini</div>
            </div>
            <div class="bg-white/10 rounded-lg p-3 border border-white/20">
                <div class="text-2xl font-bold">{{ $stats['week'] ?? 0 }}</div>
                <div class="text-xs opacity-90">Minggu Ini</div>
            </div>
            <div class="bg-white/10 rounded-lg p-3 border border-white/20">
                <div class="text-2xl font-bold text-red-300">{{ $stats['overdue'] ?? 0 }}</div>
                <div class="text-xs opacity-90">Terlambat</div>
            </div>
        </div>
    </div>

    {{-- Task List --}}
    <div class="space-y-2">
        @forelse($tasks as $task)
        <a href="{{ route('mobile.tasks.show', $task->id) }}" 
           class="block bg-white rounded-xl border border-gray-200 p-4 active:scale-[0.98] transition-transform">
            <div class="flex items-start gap-3">
                {{-- Checkbox icon --}}
                <div class="w-6 h-6 rounded-md border-2 flex items-center justify-center flex-shrink-0 mt-0.5
                    @if($task->status === 'done') bg-green-500 border-green-500 @else border-gray-300 @endif">
                    @if($task->status === 'done')
                    <i class="fas fa-check text-white text-xs"></i>
                    @endif
                </div>

                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 mb-1">
                        <h3 class="text-sm font-semibold text-gray-900 truncate @if($task->status === 'done') line-through text-gray-500 @endif">
                            {{ $task->title }}
                        </h3>
                        @if($task->priority === 'urgent' || $task->priority === 'high')
                        <span class="text-xs px-2 py-0.5 rounded-full font-medium
                            @if($task->priority === 'urgent') bg-red-100 text-red-700 @else bg-orange-100 text-orange-700 @endif">
                            {{ ucfirst($task->priority) }}
                        </span>
                        @endif
                    </div>

                    @if($task->project)
                    <p class="text-xs text-gray-500 mb-1">
                        <i class="fas fa-folder text-gray-400 mr-1"></i>{{ $task->project->name }}
                    </p>
                    @endif

                    @if($task->due_date)
                    <p class="text-xs @if($task->due_date < now() && $task->status !== 'done') text-red-600 font-medium @else text-gray-500 @endif">
                        <i class="fas fa-calendar mr-1"></i>
                        {{ \Carbon\Carbon::parse($task->due_date)->format('d M Y') }}
                        @if($task->due_date < now() && $task->status !== 'done')
                            — Terlambat
                        @endif
                    </p>
                    @endif
                </div>

                <i class="fas fa-chevron-right text-gray-300 text-xs mt-1"></i>
            </div>
        </a>
        @empty
        <div class="text-center py-16">
            <div class="w-16 h-16 bg-green-50 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-check-circle text-green-400 text-2xl"></i>
            </div>
            <h3 class="text-gray-500 font-medium mb-1">Tidak Ada Task</h3>
            <p class="text-sm text-gray-400">Semua task sudah selesai!</p>
        </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    @if($tasks->hasPages())
    <div class="mt-4 px-2">
        {{ $tasks->links('pagination::simple-tailwind') }}
    </div>
    @endif
</div>
@endsection
