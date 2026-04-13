@extends('mobile.layouts.app')

@section('title', 'Task Urgent')

@section('content')
<div class="pb-20">

    {{-- Header --}}
    <div class="bg-gradient-to-br from-red-500 to-red-600 rounded-2xl p-6 mb-4 text-white">
        <div class="flex items-center justify-between mb-2">
            <div>
                <h2 class="text-xl font-bold">Task Urgent</h2>
                <p class="text-sm opacity-90">{{ $tasks->count() }} task butuh perhatian</p>
            </div>
            <div class="w-12 h-12 bg-white/10 rounded-xl flex items-center justify-center">
                <i class="fas fa-exclamation-triangle text-2xl"></i>
            </div>
        </div>
    </div>

    {{-- Overdue Tasks --}}
    @php
        $overdue = $tasks->filter(fn($t) => $t->due_date < now()->startOfDay());
        $dueToday = $tasks->filter(fn($t) => \Carbon\Carbon::parse($t->due_date)->isToday());
    @endphp

    @if($overdue->count() > 0)
    <div class="mb-4">
        <h3 class="text-sm font-bold text-red-600 mb-2 px-1 flex items-center gap-2">
            <i class="fas fa-clock"></i>
            Terlambat ({{ $overdue->count() }})
        </h3>
        <div class="space-y-2">
            @foreach($overdue as $task)
            <a href="{{ route('mobile.tasks.show', $task->id) }}"
               class="block bg-white rounded-xl border border-red-200 p-4 active:scale-[0.98] transition-transform">
                <div class="flex items-start gap-3">
                    <div class="w-8 h-8 bg-red-50 rounded-lg flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-exclamation text-red-500 text-sm"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h4 class="text-sm font-semibold text-gray-900 truncate">{{ $task->title }}</h4>
                        @if($task->project)
                        <p class="text-xs text-gray-500 mt-0.5">
                            <i class="fas fa-folder text-gray-400 mr-1"></i>{{ $task->project->name }}
                        </p>
                        @endif
                        <p class="text-xs text-red-600 font-medium mt-1">
                            <i class="fas fa-calendar mr-1"></i>
                            {{ \Carbon\Carbon::parse($task->due_date)->format('d M Y') }}
                            — {{ \Carbon\Carbon::parse($task->due_date)->diffInDays(now()) }} hari terlambat
                        </p>
                    </div>
                    <button onclick="event.preventDefault(); completeTask({{ $task->id }})"
                            class="w-8 h-8 bg-green-50 rounded-lg flex items-center justify-center text-green-600 active:scale-90">
                        <i class="fas fa-check text-sm"></i>
                    </button>
                </div>
            </a>
            @endforeach
        </div>
    </div>
    @endif

    @if($dueToday->count() > 0)
    <div class="mb-4">
        <h3 class="text-sm font-bold text-amber-600 mb-2 px-1 flex items-center gap-2">
            <i class="fas fa-calendar-day"></i>
            Hari Ini ({{ $dueToday->count() }})
        </h3>
        <div class="space-y-2">
            @foreach($dueToday as $task)
            <a href="{{ route('mobile.tasks.show', $task->id) }}"
               class="block bg-white rounded-xl border border-amber-200 p-4 active:scale-[0.98] transition-transform">
                <div class="flex items-start gap-3">
                    <div class="w-8 h-8 bg-amber-50 rounded-lg flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-calendar-day text-amber-500 text-sm"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h4 class="text-sm font-semibold text-gray-900 truncate">{{ $task->title }}</h4>
                        @if($task->project)
                        <p class="text-xs text-gray-500 mt-0.5">
                            <i class="fas fa-folder text-gray-400 mr-1"></i>{{ $task->project->name }}
                        </p>
                        @endif
                        <p class="text-xs text-amber-600 font-medium mt-1">
                            <i class="fas fa-clock mr-1"></i>Deadline hari ini
                        </p>
                    </div>
                    <button onclick="event.preventDefault(); completeTask({{ $task->id }})"
                            class="w-8 h-8 bg-green-50 rounded-lg flex items-center justify-center text-green-600 active:scale-90">
                        <i class="fas fa-check text-sm"></i>
                    </button>
                </div>
            </a>
            @endforeach
        </div>
    </div>
    @endif

    @if($tasks->count() === 0)
    <div class="text-center py-16">
        <div class="w-16 h-16 bg-green-50 rounded-full flex items-center justify-center mx-auto mb-4">
            <i class="fas fa-check-circle text-green-400 text-2xl"></i>
        </div>
        <h3 class="text-gray-500 font-medium mb-1">Tidak Ada Task Urgent</h3>
        <p class="text-sm text-gray-400">Semua task on track!</p>
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
async function completeTask(taskId) {
    try {
        const res = await fetch(`{{ url('m/tasks') }}/${taskId}/complete`, {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        });
        const data = await res.json();
        if (data.success) {
            window.location.reload();
        }
    } catch (e) {
        alert('Gagal menyelesaikan task');
    }
}
</script>
@endpush
