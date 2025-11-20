@extends('mobile.layouts.app')

@section('title', $project->name)

@section('content')
<div class="mobile-page pb-20" x-data="projectDetail({{ $project->id }})">
    <!-- Hero Section -->
    <div class="bg-gradient-to-br from-[#0077b5] to-[#004d6d] text-white p-6 safe-top">
        <div class="flex items-start justify-between mb-3">
            <div class="flex-1 min-w-0 pr-3">
                <h1 class="text-xl font-bold mb-1">{{ $project->name }}</h1>
                <p class="text-white/90 text-sm">{{ $project->institution->name ?? '-' }}</p>
                @if($project->client)
                <div class="flex items-center gap-1.5 mt-1.5">
                    <i class="fas fa-building text-white/80 text-xs"></i>
                    <p class="text-white/80 text-sm font-medium">{{ $project->client->company_name }}</p>
                </div>
                @endif
            </div>
            <span class="flex-shrink-0 px-3 py-1 bg-white/20 backdrop-blur-sm rounded-full text-sm font-medium">
                {{ $project->status->name ?? 'Unknown' }}
            </span>
        </div>

        <!-- Progress -->
        <div class="mb-4">
            <div class="flex justify-between text-sm mb-1.5">
                <span class="text-white">Progress</span>
                <span class="font-semibold">{{ $project->progress_percentage ?? 0 }}%</span>
            </div>
            <div class="h-2 bg-white/20 rounded-full overflow-hidden">
                <div style="width: {{ $project->progress_percentage ?? 0 }}%" 
                     class="h-full bg-white rounded-full transition-all duration-500"></div>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="grid grid-cols-3 gap-3">
            <div class="bg-white/10 backdrop-blur-sm rounded-lg p-3">
                <div class="text-2xl font-bold">{{ $stats['totalTasks'] ?? 0 }}</div>
                <div class="text-xs text-white mt-0.5">Tasks</div>
            </div>
            <div class="bg-white/10 backdrop-blur-sm rounded-lg p-3">
                <div class="text-2xl font-bold">{{ (int)($stats['daysLeft'] ?? 0) }}</div>
                <div class="text-xs text-white mt-0.5">Hari Lagi</div>
            </div>
            <div class="bg-white/10 backdrop-blur-sm rounded-lg p-3">
                <div class="text-2xl font-bold">
                    @if($stats['totalBudget'] > 0)
                        {{ number_format(($stats['totalSpent'] / $stats['totalBudget']) * 100, 0) }}%
                    @else
                        0%
                    @endif
                </div>
                <div class="text-xs text-white mt-0.5">Kontrak Used</div>
            </div>
        </div>
    </div>

    <!-- Tabs -->
    <div class="sticky top-14 z-10 bg-white border-b border-gray-200">
        <div class="flex justify-around">
            <button @click="activeTab = 'overview'" 
                    :class="activeTab === 'overview' ? 'border-[#0077b5] text-[#0077b5]' : 'border-transparent text-gray-500'"
                    class="flex-1 flex flex-col items-center gap-1 py-3 border-b-2 font-medium text-xs">
                <i class="fas fa-info-circle text-base"></i>
                <span>Info</span>
            </button>
            <button @click="activeTab = 'financial'" 
                    :class="activeTab === 'financial' ? 'border-[#0077b5] text-[#0077b5]' : 'border-transparent text-gray-500'"
                    class="flex-1 flex flex-col items-center gap-1 py-3 border-b-2 font-medium text-xs">
                <i class="fas fa-wallet text-base"></i>
                <span>Keuangan</span>
            </button>
            <button @click="activeTab = 'tasks'" 
                    :class="activeTab === 'tasks' ? 'border-[#0077b5] text-[#0077b5]' : 'border-transparent text-gray-500'"
                    class="flex-1 flex flex-col items-center gap-1 py-3 border-b-2 font-medium text-xs relative">
                <i class="fas fa-tasks text-base"></i>
                <span>Tasks</span>
                @if($stats['totalTasks'] > 0)
                <span class="absolute top-1 right-2 bg-red-500 text-white text-[10px] rounded-full w-4 h-4 flex items-center justify-center font-bold">
                    {{ $stats['totalTasks'] > 9 ? '9+' : $stats['totalTasks'] }}
                </span>
                @endif
            </button>
            <button @click="activeTab = 'timeline'" 
                    :class="activeTab === 'timeline' ? 'border-[#0077b5] text-[#0077b5]' : 'border-transparent text-gray-500'"
                    class="flex-1 flex flex-col items-center gap-1 py-3 border-b-2 font-medium text-xs">
                <i class="fas fa-stream text-base"></i>
                <span>Timeline</span>
            </button>
            <button @click="activeTab = 'files'" 
                    :class="activeTab === 'files' ? 'border-[#0077b5] text-[#0077b5]' : 'border-transparent text-gray-500'"
                    class="flex-1 flex flex-col items-center gap-1 py-3 border-b-2 font-medium text-xs">
                <i class="fas fa-folder text-base"></i>
                <span>Files</span>
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
                    @if($project->client)
                    <div class="flex justify-between text-sm pb-2.5 border-b border-gray-100">
                        <span class="text-gray-600">Klien</span>
                        <span class="font-medium text-[#0077b5]">{{ $project->client->company_name }}</span>
                    </div>
                    @if($project->client->contact_person)
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Contact Person</span>
                        <span class="font-medium">{{ $project->client->contact_person }}</span>
                    </div>
                    @endif
                    @if($project->client->phone)
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Telepon</span>
                        <a href="tel:{{ $project->client->phone }}" class="font-medium text-[#0077b5] hover:underline">
                            {{ $project->client->phone }}
                        </a>
                    </div>
                    @endif
                    @endif
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Deadline</span>
                        <span class="font-medium">{{ \Carbon\Carbon::parse($project->deadline)->format('d M Y') }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Nilai Kontrak</span>
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
                    <div class="text-center p-3 bg-[#f0f7fa] rounded-lg">
                        <div class="text-2xl font-bold text-[#0077b5]">{{ $stats['totalTasks'] - $stats['completedTasks'] }}</div>
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
                        <i class="fas fa-exchange-alt mr-2 text-[#0077b5]"></i>Update Status
                    </button>
                </div>
            </div>
        </div>

        <!-- Financial Tab -->
        <div x-show="activeTab === 'financial'" x-transition>
            <!-- Financial Summary -->
            <div class="bg-white rounded-lg border border-gray-200 p-4 mb-4">
                <h3 class="font-semibold text-gray-900 mb-3">Ringkasan Keuangan</h3>
                <div class="space-y-3">
                    <div class="flex justify-between items-center">
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 bg-green-50 rounded-lg flex items-center justify-center">
                                <i class="fas fa-arrow-down text-green-600 text-sm"></i>
                            </div>
                            <span class="text-sm text-gray-600">Total Pemasukan</span>
                        </div>
                        <span class="font-semibold text-green-600">Rp {{ number_format($stats['totalIncome'] ?? 0, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 bg-red-50 rounded-lg flex items-center justify-center">
                                <i class="fas fa-arrow-up text-red-600 text-sm"></i>
                            </div>
                            <span class="text-sm text-gray-600">Total Pengeluaran</span>
                        </div>
                        <span class="font-semibold text-red-600">Rp {{ number_format($stats['totalSpent'], 0, ',', '.') }}</span>
                    </div>
                    <div class="pt-2 border-t border-gray-200">
                        <div class="flex justify-between items-center">
                            <span class="text-sm font-medium text-gray-900">Saldo</span>
                            <span class="font-bold text-lg {{ ($stats['totalIncome'] - $stats['totalSpent']) >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                Rp {{ number_format(($stats['totalIncome'] ?? 0) - $stats['totalSpent'], 0, ',', '.') }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pemasukan Section -->
            <div class="mb-4">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="font-semibold text-gray-900 flex items-center gap-2">
                        <i class="fas fa-arrow-down text-green-600"></i>
                        Pemasukan
                    </h3>
                    <span class="text-xs text-gray-500">{{ $project->payments->count() }} transaksi</span>
                </div>
                <div class="space-y-2">
                    @forelse($project->payments as $payment)
                    <div class="bg-white rounded-lg border border-gray-200 p-3">
                        <div class="flex justify-between items-start mb-1">
                            <div class="flex-1">
                                <div class="font-medium text-gray-900">{{ $payment->description ?? 'Pembayaran' }}</div>
                                <div class="text-xs text-gray-500 mt-0.5">
                                    {{ \Carbon\Carbon::parse($payment->payment_date)->format('d M Y') }}
                                    @if($payment->payment_method)
                                    <span class="mx-1">•</span>
                                    <span class="capitalize">{{ str_replace('_', ' ', $payment->payment_method) }}</span>
                                    @endif
                                </div>
                            </div>
                            <div class="text-right ml-3">
                                <div class="font-semibold text-green-600">+Rp {{ number_format($payment->amount, 0, ',', '.') }}</div>
                                @if($payment->payment_type)
                                <div class="text-xs text-gray-500 mt-0.5">{{ ucfirst($payment->payment_type) }}</div>
                                @endif
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="bg-gray-50 rounded-lg p-6 text-center">
                        <i class="fas fa-inbox text-gray-300 text-3xl mb-2"></i>
                        <p class="text-sm text-gray-500">Belum ada pemasukan</p>
                    </div>
                    @endforelse
                </div>
            </div>

            <!-- Pengeluaran/Kasbon Section -->
            <div class="mb-4">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="font-semibold text-gray-900 flex items-center gap-2">
                        <i class="fas fa-arrow-up text-red-600"></i>
                        Pengeluaran/Kasbon
                    </h3>
                    <span class="text-xs text-gray-500">{{ $project->expenses->count() }} transaksi</span>
                </div>
                <div class="space-y-2">
                    @forelse($project->expenses as $expense)
                    <div class="bg-white rounded-lg border border-gray-200 p-3">
                        <div class="flex justify-between items-start mb-1">
                            <div class="flex-1">
                                <div class="font-medium text-gray-900">{{ $expense->description ?? 'Pengeluaran' }}</div>
                                <div class="text-xs text-gray-500 mt-0.5">
                                    {{ \Carbon\Carbon::parse($expense->expense_date)->format('d M Y') }}
                                    @if($expense->category)
                                    <span class="mx-1">•</span>
                                    <span class="capitalize">{{ $expense->category }}</span>
                                    @endif
                                </div>
                                @if($expense->notes)
                                <div class="text-xs text-gray-600 mt-1">{{ Str::limit($expense->notes, 50) }}</div>
                                @endif
                            </div>
                            <div class="text-right ml-3">
                                <div class="font-semibold text-red-600">-Rp {{ number_format($expense->amount, 0, ',', '.') }}</div>
                                @if($expense->status)
                                <div class="text-xs mt-0.5">
                                    <span class="px-2 py-0.5 rounded-full
                                        {{ $expense->status === 'approved' ? 'bg-green-100 text-green-700' : 
                                           ($expense->status === 'pending' ? 'bg-yellow-100 text-yellow-700' : 'bg-gray-100 text-gray-700') }}">
                                        {{ ucfirst($expense->status) }}
                                    </span>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="bg-gray-50 rounded-lg p-6 text-center">
                        <i class="fas fa-inbox text-gray-300 text-3xl mb-2"></i>
                        <p class="text-sm text-gray-500">Belum ada pengeluaran</p>
                    </div>
                    @endforelse
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
                               ($task->status === 'in_progress' ? 'bg-[#e7f3f8] text-[#0077b5]' : 'bg-gray-100 text-gray-800') }}">
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
                    <div class="flex-shrink-0 w-10 h-10 bg-[#f0f7fa] rounded-lg flex items-center justify-center">
                        <i class="fas fa-file-alt text-[#0077b5]"></i>
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

    <!-- Add Note Modal -->
    <div x-show="showAddNoteModal" 
         x-cloak
         @click.self="showAddNoteModal = false"
         class="fixed inset-0 z-50 flex items-end sm:items-center justify-center bg-black/50 backdrop-blur-sm">
        <div @click.stop 
             x-show="showAddNoteModal"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="translate-y-full sm:translate-y-0 sm:scale-95 opacity-0"
             x-transition:enter-end="translate-y-0 sm:scale-100 opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="translate-y-0 sm:scale-100 opacity-100"
             x-transition:leave-end="translate-y-full sm:translate-y-0 sm:scale-95 opacity-0"
             class="bg-white w-full sm:max-w-md sm:rounded-2xl rounded-t-2xl shadow-xl max-h-[90vh] overflow-y-auto">
            <div class="sticky top-0 bg-white border-b border-gray-200 p-4 flex items-center justify-between">
                <h3 class="font-bold text-lg">Add Note</h3>
                <button @click="showAddNoteModal = false" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <form @submit.prevent="submitNote" class="p-4">
                <textarea 
                    x-model="noteContent"
                    placeholder="Tulis catatan..."
                    rows="5"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0077b5] focus:border-transparent resize-none"
                    required></textarea>
                <div class="mt-4 flex gap-2">
                    <button type="button" 
                            @click="showAddNoteModal = false"
                            class="flex-1 px-4 py-2.5 border border-gray-300 rounded-lg font-medium text-gray-700 hover:bg-gray-50">
                        Cancel
                    </button>
                    <button type="submit" 
                            :disabled="submitting"
                            class="flex-1 px-4 py-2.5 bg-[#0077b5] text-white rounded-lg font-medium hover:bg-[#006399] disabled:opacity-50">
                        <span x-show="!submitting">Save Note</span>
                        <span x-show="submitting"><i class="fas fa-spinner fa-spin mr-1"></i>Saving...</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Update Status Modal -->
    <div x-show="showStatusModal" 
         x-cloak
         @click.self="showStatusModal = false"
         class="fixed inset-0 z-50 flex items-end sm:items-center justify-center bg-black/50 backdrop-blur-sm">
        <div @click.stop 
             x-show="showStatusModal"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="translate-y-full sm:translate-y-0 sm:scale-95 opacity-0"
             x-transition:enter-end="translate-y-0 sm:scale-100 opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="translate-y-0 sm:scale-100 opacity-100"
             x-transition:leave-end="translate-y-full sm:translate-y-0 sm:scale-95 opacity-0"
             class="bg-white w-full sm:max-w-md sm:rounded-2xl rounded-t-2xl shadow-xl max-h-[90vh] overflow-y-auto">
            <div class="sticky top-0 bg-white border-b border-gray-200 p-4 flex items-center justify-between">
                <h3 class="font-bold text-lg">Update Status</h3>
                <button @click="showStatusModal = false" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <form @submit.prevent="submitStatus" class="p-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Pilih Status Baru</label>
                <select x-model="selectedStatusId" 
                        class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0077b5] focus:border-transparent"
                        required>
                    <option value="">-- Pilih Status --</option>
                    @foreach(\App\Models\ProjectStatus::all() as $status)
                    <option value="{{ $status->id }}" {{ $project->status_id == $status->id ? 'selected' : '' }}>
                        {{ $status->name }}
                    </option>
                    @endforeach
                </select>
                <div class="mt-4 flex gap-2">
                    <button type="button" 
                            @click="showStatusModal = false"
                            class="flex-1 px-4 py-2.5 border border-gray-300 rounded-lg font-medium text-gray-700 hover:bg-gray-50">
                        Cancel
                    </button>
                    <button type="submit" 
                            :disabled="submitting || !selectedStatusId"
                            class="flex-1 px-4 py-2.5 bg-[#0077b5] text-white rounded-lg font-medium hover:bg-[#006399] disabled:opacity-50">
                        <span x-show="!submitting">Update Status</span>
                        <span x-show="submitting"><i class="fas fa-spinner fa-spin mr-1"></i>Updating...</span>
                    </button>
                </div>
            </form>
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
        noteContent: '',
        selectedStatusId: '{{ $project->status_id }}',
        submitting: false,

        init() {
            this.$watch('activeTab', (value) => {
                if (value === 'timeline' && !this.timelineLoaded) {
                    this.loadTimeline();
                }
            });
        },

        async submitNote() {
            if (!this.noteContent.trim()) return;
            
            this.submitting = true;
            try {
                const response = await fetch(`/m/projects/${projectId}/note`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        note: this.noteContent
                    })
                });

                const data = await response.json();
                
                if (data.success) {
                    // Show success message
                    this.showToast('Catatan berhasil ditambahkan', 'success');
                    this.noteContent = '';
                    this.showAddNoteModal = false;
                    
                    // Reload timeline if active
                    if (this.activeTab === 'timeline') {
                        this.timelineLoaded = false;
                        await this.loadTimeline();
                    }
                } else {
                    this.showToast(data.message || 'Gagal menambahkan catatan', 'error');
                }
            } catch (error) {
                console.error('Add note error:', error);
                this.showToast('Terjadi kesalahan saat menambahkan catatan', 'error');
            } finally {
                this.submitting = false;
            }
        },

        async submitStatus() {
            if (!this.selectedStatusId) return;
            
            this.submitting = true;
            try {
                const response = await fetch(`/m/projects/${projectId}/status`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        status_id: this.selectedStatusId
                    })
                });

                const data = await response.json();
                
                if (data.success) {
                    this.showToast(data.message || 'Status berhasil diupdate', 'success');
                    this.showStatusModal = false;
                    
                    // Reload page to show new status
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                } else {
                    this.showToast(data.message || 'Gagal update status', 'error');
                }
            } catch (error) {
                console.error('Update status error:', error);
                this.showToast('Terjadi kesalahan saat update status', 'error');
            } finally {
                this.submitting = false;
            }
        },

        showToast(message, type = 'success') {
            // Create toast element
            const toast = document.createElement('div');
            toast.className = `fixed bottom-20 left-1/2 -translate-x-1/2 px-6 py-3 rounded-lg shadow-lg z-50 animate-fade-in ${
                type === 'success' ? 'bg-green-600' : 'bg-red-600'
            } text-white font-medium`;
            toast.textContent = message;
            
            document.body.appendChild(toast);
            
            // Remove after 3 seconds
            setTimeout(() => {
                toast.classList.add('animate-fade-out');
                setTimeout(() => toast.remove(), 300);
            }, 3000);
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
                'blue': 'bg-[#0077b5]',
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
