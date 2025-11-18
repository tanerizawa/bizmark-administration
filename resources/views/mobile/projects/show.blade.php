@extends('mobile.layouts.app')

@section('title', $project->name)

@section('content')
<div class="mobile-page pb-20" x-data="projectDetail({{ $project->id }})">
    <!-- Hero Section -->
    <div class="bg-gradient-to-br from-blue-500 to-blue-600 text-white p-6 safe-top">
        <div class="flex items-start justify-between mb-3">
            <div class="flex-1 min-w-0 pr-3">
                <h1 class="text-xl font-bold mb-1">{{ $project->name }}</h1>
                <p class="text-blue-100 text-sm">{{ $project->institution->name ?? '-' }}</p>
            </div>
            <span class="flex-shrink-0 px-3 py-1 bg-white/20 backdrop-blur-sm rounded-full text-sm font-medium">
                {{ $project->status->name ?? 'Unknown' }}
            </span>
        </div>

        <!-- Progress -->
        <div class="mb-4">
            <div class="flex justify-between text-sm mb-1.5">
                <span class="text-blue-100">Progress</span>
                <span class="font-semibold">{{ $project->progress ?? 0 }}%</span>
            </div>
            <div class="h-2 bg-white/20 rounded-full overflow-hidden">
                <div style="width: {{ $project->progress ?? 0 }}%" 
                     class="h-full bg-white rounded-full transition-all duration-500"></div>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="grid grid-cols-3 gap-3">
            <div class="bg-white/10 backdrop-blur-sm rounded-lg p-3">
                <div class="text-2xl font-bold">{{ $stats['totalTasks'] ?? 0 }}</div>
                <div class="text-xs text-blue-100 mt-0.5">Tasks</div>
            </div>
            <div class="bg-white/10 backdrop-blur-sm rounded-lg p-3">
                <div class="text-2xl font-bold">{{ $stats['daysLeft'] ?? 0 }}</div>
                <div class="text-xs text-blue-100 mt-0.5">Hari Lagi</div>
            </div>
            <div class="bg-white/10 backdrop-blur-sm rounded-lg p-3">
                <div class="text-2xl font-bold">{{ number_format(($stats['totalSpent'] / $stats['totalBudget']) * 100, 0) }}%</div>
                <div class="text-xs text-blue-100 mt-0.5">Budget Used</div>
            </div>
        </div>
    </div>

    <!-- Tabs -->
    <div class="sticky top-14 z-10 bg-white border-b border-gray-200">
        <div class="flex overflow-x-auto scrollbar-hide">
            <button @click="activeTab = 'overview'" 
                    :class="activeTab === 'overview' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500'"
                    class="flex-shrink-0 px-4 py-3 border-b-2 font-medium text-sm">
                Overview
            </button>
            <button @click="activeTab = 'tasks'" 
                    :class="activeTab === 'tasks' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500'"
                    class="flex-shrink-0 px-4 py-3 border-b-2 font-medium text-sm">
                Tasks ({{ $stats['totalTasks'] }})
            </button>
            <button @click="activeTab = 'timeline'" 
                    :class="activeTab === 'timeline' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500'"
                    class="flex-shrink-0 px-4 py-3 border-b-2 font-medium text-sm">
                Timeline
            </button>
            <button @click="activeTab = 'files'" 
                    :class="activeTab === 'files' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500'"
                    class="flex-shrink-0 px-4 py-3 border-b-2 font-medium text-sm">
                Files
            </button>
        </div>
    </div>

    <!-- Tab Content -->
    <div class="p-4">
        <!-- Overview Tab -->
        <div x-show="activeTab === 'overview'" x-transition>
            <!-- Project Info -->
            <div class="bg-white rounded-lg border border-gray-200 p-4 mb-4">
                <h3 class="font-semibold text-gray-900 mb-3">Informasi Project</h3>
                <div class="space-y-2.5">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Manager</span>
                        <span class="font-medium">{{ $project->manager->name ?? '-' }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Deadline</span>
                        <span class="font-medium">{{ \Carbon\Carbon::parse($project->deadline)->format('d M Y') }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Budget</span>
                        <span class="font-medium">{{ number_format($project->budget, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Total Spent</span>
                        <span class="font-medium text-red-600">{{ number_format($stats['totalSpent'], 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Remaining</span>
                        <span class="font-medium text-green-600">{{ number_format($stats['totalBudget'] - $stats['totalSpent'], 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            <!-- Tasks Summary -->
            <div class="bg-white rounded-lg border border-gray-200 p-4 mb-4">
                <h3 class="font-semibold text-gray-900 mb-3">Tasks Summary</h3>
                <div class="grid grid-cols-3 gap-3">
                    <div class="text-center p-3 bg-green-50 rounded-lg">
                        <div class="text-2xl font-bold text-green-600">{{ $stats['completedTasks'] }}</div>
                        <div class="text-xs text-gray-600 mt-1">Selesai</div>
                    </div>
                    <div class="text-center p-3 bg-blue-50 rounded-lg">
                        <div class="text-2xl font-bold text-blue-600">{{ $stats['totalTasks'] - $stats['completedTasks'] }}</div>
                        <div class="text-xs text-gray-600 mt-1">Aktif</div>
                    </div>
                    <div class="text-center p-3 bg-red-50 rounded-lg">
                        <div class="text-2xl font-bold text-red-600">{{ $stats['overdueTasks'] }}</div>
                        <div class="text-xs text-gray-600 mt-1">Overdue</div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white rounded-lg border border-gray-200 p-4">
                <h3 class="font-semibold text-gray-900 mb-3">Quick Actions</h3>
                <div class="grid grid-cols-2 gap-2">
                    <button @click="showAddNoteModal = true" 
                            class="p-3 border border-gray-200 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 active:bg-gray-100">
                        <i class="fas fa-sticky-note mr-2 text-yellow-500"></i>Add Note
                    </button>
                    <button @click="showStatusModal = true" 
                            class="p-3 border border-gray-200 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 active:bg-gray-100">
                        <i class="fas fa-exchange-alt mr-2 text-blue-500"></i>Update Status
                    </button>
                </div>
            </div>
        </div>

        <!-- Tasks Tab -->
        <div x-show="activeTab === 'tasks'" x-transition>
            <div class="space-y-2">
                @forelse($project->tasks as $task)
                <a href="/m/tasks/{{ $task->id }}" 
                   class="block bg-white rounded-lg border border-gray-200 p-4 hover:shadow-sm transition-shadow">
                    <div class="flex items-start justify-between mb-2">
                        <h4 class="font-medium text-gray-900 flex-1 pr-3">{{ $task->title }}</h4>
                        <span class="flex-shrink-0 px-2 py-1 text-xs rounded-full
                            {{ $task->status === 'done' ? 'bg-green-100 text-green-800' : 
                               ($task->status === 'in_progress' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800') }}">
                            {{ ucfirst($task->status) }}
                        </span>
                    </div>
                    <div class="flex items-center gap-3 text-xs text-gray-600">
                        <div class="flex items-center gap-1">
                            <i class="fas fa-calendar-alt"></i>
                            <span>{{ \Carbon\Carbon::parse($task->due_date)->format('d M') }}</span>
                        </div>
                        @if($task->assignee)
                        <div class="flex items-center gap-1">
                            <i class="fas fa-user"></i>
                            <span>{{ $task->assignee->name }}</span>
                        </div>
                        @endif
                    </div>
                </a>
                @empty
                <div class="text-center py-8 text-gray-500">
                    <i class="fas fa-tasks text-3xl text-gray-300 mb-2"></i>
                    <p>Tidak ada tasks</p>
                </div>
                @endforelse
            </div>
        </div>

        <!-- Timeline Tab -->
        <div x-show="activeTab === 'timeline'" x-transition>
            <template x-if="!timelineLoaded">
                <div class="text-center py-8">
                    <i class="fas fa-spinner fa-spin text-2xl text-gray-400"></i>
                </div>
            </template>

            <div class="relative">
                <div class="absolute left-4 top-0 bottom-0 w-0.5 bg-gray-200"></div>
                
                <template x-for="event in timeline" :key="event.date + event.title">
                    <div class="relative pl-10 pb-6">
                        <div :class="`absolute left-2.5 w-3 h-3 rounded-full border-2 border-white ${getEventColor(event.color)}`"></div>
                        <div class="bg-white rounded-lg border border-gray-200 p-3">
                            <div class="flex items-center gap-2 mb-1">
                                <i :class="`fas fa-${event.icon} text-${event.color}-500 text-sm`"></i>
                                <h4 class="font-medium text-gray-900 text-sm" x-text="event.title"></h4>
                            </div>
                            <p class="text-xs text-gray-600" x-text="formatDate(event.date)"></p>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <!-- Files Tab -->
        <div x-show="activeTab === 'files'" x-transition>
            <div class="space-y-2">
                @forelse($project->documents->take(10) as $doc)
                <a href="{{ Storage::url($doc->file_path) }}" 
                   target="_blank"
                   class="flex items-center gap-3 bg-white rounded-lg border border-gray-200 p-4 hover:shadow-sm transition-shadow">
                    <div class="flex-shrink-0 w-10 h-10 bg-blue-50 rounded-lg flex items-center justify-center">
                        <i class="fas fa-file-alt text-blue-500"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h4 class="font-medium text-gray-900 text-sm truncate">{{ $doc->title }}</h4>
                        <p class="text-xs text-gray-500 mt-0.5">{{ \Carbon\Carbon::parse($doc->created_at)->format('d M Y') }}</p>
                    </div>
                    <i class="fas fa-external-link-alt text-gray-400 text-sm"></i>
                </a>
                @empty
                <div class="text-center py-8 text-gray-500">
                    <i class="fas fa-folder-open text-3xl text-gray-300 mb-2"></i>
                    <p>Tidak ada file</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function projectDetail(projectId) {
    return {
        activeTab: 'overview',
        timeline: [],
        timelineLoaded: false,
        showAddNoteModal: false,
        showStatusModal: false,

        init() {
            this.$watch('activeTab', (value) => {
                if (value === 'timeline' && !this.timelineLoaded) {
                    this.loadTimeline();
                }
            });
        },

        async loadTimeline() {
            try {
                const response = await fetch(`/m/projects/${projectId}/timeline`);
                const data = await response.json();
                this.timeline = data.timeline || [];
                this.timelineLoaded = true;
            } catch (error) {
                console.error('Timeline load error:', error);
            }
        },

        getEventColor(color) {
            const colors = {
                'green': 'bg-green-500',
                'blue': 'bg-blue-500',
                'red': 'bg-red-500',
                'yellow': 'bg-yellow-500',
                'purple': 'bg-purple-500'
            };
            return colors[color] || 'bg-gray-500';
        },

        formatDate(date) {
            return new Date(date).toLocaleDateString('id-ID', { 
                day: 'numeric', 
                month: 'long', 
                year: 'numeric' 
            });
        }
    };
}
</script>
@endpush
