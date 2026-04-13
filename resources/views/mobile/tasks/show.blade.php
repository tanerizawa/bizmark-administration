@extends('mobile.layouts.app')

@section('title', 'Detail Task')

@section('content')
<div class="pb-20" x-data="taskDetail()">
    
    {{-- Task Header Card --}}
    <div class="bg-gradient-to-br from-white to-[#E7F3F8] rounded-xl border border-[#0A66C2]/15 shadow-sm overflow-hidden mb-4">
        <div class="p-4">
            {{-- Status & Priority --}}
            <div class="flex items-center gap-2 mb-3">
                <span class="text-xs px-2.5 py-1 rounded-full font-medium
                    @if($task->status === 'done') bg-green-100 text-green-700
                    @elseif($task->status === 'in_progress') bg-blue-100 text-blue-700
                    @else bg-gray-100 text-gray-700
                    @endif">
                    @if($task->status === 'done') <i class="fas fa-check-circle mr-1"></i>Selesai
                    @elseif($task->status === 'in_progress') <i class="fas fa-spinner mr-1"></i>Dikerjakan
                    @else <i class="fas fa-circle mr-1"></i>To Do
                    @endif
                </span>
                @if($task->priority && $task->priority !== 'medium')
                <span class="text-xs px-2.5 py-1 rounded-full font-medium
                    @if($task->priority === 'urgent') bg-red-100 text-red-700
                    @elseif($task->priority === 'high') bg-orange-100 text-orange-700
                    @else bg-blue-50 text-blue-600
                    @endif">
                    {{ ucfirst($task->priority) }}
                </span>
                @endif
            </div>

            {{-- Title --}}
            <h2 class="text-lg font-bold text-gray-900 mb-2">{{ $task->title }}</h2>

            {{-- Description --}}
            @if($task->description)
            <p class="text-sm text-gray-600 leading-relaxed mb-3">{{ $task->description }}</p>
            @endif

            {{-- Meta Info --}}
            <div class="space-y-2 text-sm">
                @if($task->project)
                <div class="flex items-center gap-2 text-gray-600">
                    <i class="fas fa-folder text-gray-400 w-5 text-center"></i>
                    <a href="{{ route('mobile.projects.show', $task->project_id) }}" class="text-[#0A66C2] font-medium">
                        {{ $task->project->name }}
                    </a>
                </div>
                @endif

                @if($task->assignedUser)
                <div class="flex items-center gap-2 text-gray-600">
                    <i class="fas fa-user text-gray-400 w-5 text-center"></i>
                    <span>{{ $task->assignedUser->name }}</span>
                </div>
                @endif

                @if($task->due_date)
                <div class="flex items-center gap-2 
                    @if($task->due_date < now() && $task->status !== 'done') text-red-600 font-medium @else text-gray-600 @endif">
                    <i class="fas fa-calendar w-5 text-center @if($task->due_date < now() && $task->status !== 'done') text-red-400 @else text-gray-400 @endif"></i>
                    <span>{{ $task->due_date->format('d M Y') }}</span>
                    @if($task->due_date < now() && $task->status !== 'done')
                        <span class="text-xs bg-red-100 text-red-700 px-2 py-0.5 rounded-full">
                            {{ $task->due_date->diffInDays(now()) }} hari terlambat
                        </span>
                    @endif
                </div>
                @endif

                @if($task->completed_at)
                <div class="flex items-center gap-2 text-green-600">
                    <i class="fas fa-check-circle text-green-400 w-5 text-center"></i>
                    <span>Selesai {{ $task->completed_at->format('d M Y H:i') }}</span>
                </div>
                @endif
            </div>
        </div>

        {{-- Action Buttons --}}
        @if($task->status !== 'done')
        <div class="border-t border-[#0A66C2]/10 bg-[#E7F3F8]/30 p-3 flex gap-2">
            @if($task->status === 'todo')
            <button @click="updateStatus('in_progress')"
                    class="flex-1 py-2.5 bg-[#0A66C2] text-white rounded-lg text-sm font-medium active:scale-95 transition-all">
                <i class="fas fa-play mr-1"></i> Mulai Kerjakan
            </button>
            @endif
            <button @click="updateStatus('done')"
                    class="flex-1 py-2.5 bg-green-600 text-white rounded-lg text-sm font-medium active:scale-95 transition-all">
                <i class="fas fa-check mr-1"></i> Selesai
            </button>
        </div>
        @endif
    </div>

    {{-- SOP Notes --}}
    @if($task->sop_notes)
    <div class="bg-[#E7F3F8] rounded-xl border border-[#0A66C2]/15 p-4 mb-4">
        <h3 class="text-sm font-bold text-[#004182] mb-2 flex items-center gap-2">
            <i class="fas fa-clipboard-list text-[#0A66C2]"></i>
            Catatan SOP
        </h3>
        <div class="text-sm text-[#004182]/80 leading-relaxed whitespace-pre-line">{{ $task->sop_notes }}</div>
    </div>
    @endif

    {{-- Related Tasks --}}
    @if($relatedTasks->count() > 0)
    <div class="bg-gradient-to-br from-white to-[#E7F3F8]/50 rounded-xl border border-[#0A66C2]/15 shadow-sm overflow-hidden mb-4">
        <div class="px-4 py-3 border-b border-[#0A66C2]/10 bg-[#E7F3F8]/40">
            <h3 class="text-sm font-bold text-[#004182] flex items-center gap-2">
                <i class="fas fa-link text-[#0A66C2]"></i>
                Task Terkait ({{ $relatedTasks->count() }})
            </h3>
        </div>
        <div class="divide-y divide-gray-100">
            @foreach($relatedTasks as $related)
            <a href="{{ route('mobile.tasks.show', $related->id) }}" class="block p-3 hover:bg-gray-50 transition-colors">
                <div class="flex items-center gap-3">
                    <div class="w-6 h-6 rounded-md border-2 flex items-center justify-center flex-shrink-0
                        @if($related->status === 'done') bg-green-500 border-green-500 @else border-gray-300 @endif">
                        @if($related->status === 'done')
                        <i class="fas fa-check text-white text-xs"></i>
                        @endif
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900 truncate @if($related->status === 'done') line-through text-gray-500 @endif">
                            {{ $related->title }}
                        </p>
                        @if($related->due_date)
                        <p class="text-xs text-gray-500 mt-0.5">{{ $related->due_date->format('d M Y') }}</p>
                        @endif
                    </div>
                    <i class="fas fa-chevron-right text-gray-300 text-xs"></i>
                </div>
            </a>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Add Comment --}}
    <div class="bg-gradient-to-br from-white to-[#E7F3F8]/50 rounded-xl border border-[#0A66C2]/15 shadow-sm p-4">
        <h3 class="text-sm font-bold text-[#004182] mb-3 flex items-center gap-2">
            <i class="fas fa-comment text-[#0A66C2]"></i>
            Tambah Komentar
        </h3>
        <div class="flex gap-2">
            <textarea x-model="comment" rows="2" placeholder="Tulis komentar..."
                      class="flex-1 px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#0A66C2] focus:border-transparent resize-none"></textarea>
            <button @click="addComment" :disabled="!comment.trim() || submitting"
                    class="self-end px-4 py-2 bg-[#0A66C2] text-white rounded-lg text-sm font-medium active:scale-95 transition-all disabled:opacity-50">
                <i class="fas fa-paper-plane"></i>
            </button>
        </div>
    </div>

    {{-- Toast --}}
    <div x-show="toast.show" x-transition
         class="fixed bottom-20 left-4 right-4 p-4 rounded-lg shadow-lg z-50 text-white"
         :class="toast.type === 'success' ? 'bg-green-500' : 'bg-red-500'">
        <div class="flex items-center gap-2">
            <i class="fas" :class="toast.type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'"></i>
            <span x-text="toast.message"></span>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function taskDetail() {
    return {
        comment: '',
        submitting: false,
        toast: { show: false, message: '', type: 'success' },

        async updateStatus(status) {
            try {
                const res = await fetch(`{{ url('m/tasks') }}/{{ $task->id }}/status`, {
                    method: 'PATCH',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ status })
                });
                const data = await res.json();
                if (data.success) {
                    this.showToast(data.message, 'success');
                    setTimeout(() => window.location.reload(), 800);
                }
            } catch (e) {
                this.showToast('Gagal mengubah status', 'error');
            }
        },

        async addComment() {
            if (!this.comment.trim() || this.submitting) return;
            this.submitting = true;
            try {
                const res = await fetch(`{{ url('m/tasks') }}/{{ $task->id }}/comment`, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ comment: this.comment })
                });
                const data = await res.json();
                if (data.success) {
                    this.comment = '';
                    this.showToast('Komentar ditambahkan', 'success');
                }
            } catch (e) {
                this.showToast('Gagal menambah komentar', 'error');
            } finally {
                this.submitting = false;
            }
        },

        showToast(message, type = 'success') {
            this.toast = { show: true, message, type };
            setTimeout(() => this.toast.show = false, 3000);
        }
    }
}
</script>
@endpush
