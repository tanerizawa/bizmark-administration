    {{-- ============================================================ --}}
    {{-- CONTENT CARDS                                                --}}
    {{-- ============================================================ --}}
    <div class="space-y-1 lg:mt-1">

        {{-- ───── PROJECT OVERVIEW ───── --}}
        <section class="bg-white dark:bg-gray-800 border-y border-gray-200 dark:border-gray-700" aria-label="Ringkasan proyek">
            <div class="p-4 lg:p-5 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                <div>
                    <h2 class="text-base lg:text-lg font-semibold text-gray-900 dark:text-white leading-tight">Ringkasan Proyek</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5 leading-normal">
                        @if($projects->count() > 0)
                            {{ min($projects->count(), 4) }} proyek terbaru
                        @else
                            Belum ada proyek
                        @endif
                    </p>
                </div>
                @if($projects->count() > 0)
                <a href="{{ route('client.projects.index') }}"
                   class="text-sm font-medium text-[#0a66c2] hover:text-[#004182] dark:text-blue-400 dark:hover:text-blue-300 px-3 py-2 min-h-[44px] flex items-center active:scale-95 transition-transform">
                    Lihat Semua <i class="fas fa-arrow-right ml-2 text-xs" aria-hidden="true"></i>
                </a>
                @endif
            </div>
            <div>
                @forelse($projects->take(4) as $project)
                <a href="{{ route('client.projects.show', $project->id) }}"
                   class="flex items-center gap-3 px-4 lg:px-5 py-4 border-b border-gray-100 dark:border-gray-700 last:border-b-0 hover:bg-gray-50 dark:hover:bg-gray-700/50 active:bg-gray-100 dark:active:bg-gray-700 transition-colors group">
                    {{-- Project icon --}}
                    <span class="w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0
                        {{ ($project->status && $project->status->name === 'Selesai') ? 'bg-green-100 text-green-600 dark:bg-green-900/30 dark:text-green-400' : 'bg-blue-50 text-[#0a66c2] dark:bg-blue-900/30 dark:text-blue-400' }}">
                        <i class="fas {{ ($project->status && $project->status->name === 'Selesai') ? 'fa-check-circle' : 'fa-briefcase' }} text-sm" aria-hidden="true"></i>
                    </span>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-gray-900 dark:text-white leading-tight truncate group-hover:text-[#0a66c2] dark:group-hover:text-blue-400 transition-colors">{{ $project->name }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 leading-normal truncate">{{ $project->permitApplication->permitType->name ?? 'Jenis izin belum ditetapkan' }}</p>
                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5 leading-tight">{{ optional($project->updated_at)->diffForHumans() }}</p>
                    </div>
                    <span class="px-2.5 py-1 text-xs font-semibold rounded-full {{ $statusColors[$project->status->name ?? ''] ?? 'bg-blue-50 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400' }} whitespace-nowrap flex-shrink-0">
                        {{ $project->status->name ?? 'Belum ada status' }}
                    </span>
                    <i class="fas fa-chevron-right text-gray-300 dark:text-gray-600 text-xs hidden sm:block flex-shrink-0" aria-hidden="true"></i>
                </a>
                @empty
                <div class="px-4 lg:px-5 py-12 text-center">
                    <div class="w-16 h-16 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-folder-open text-gray-400 dark:text-gray-500 text-2xl" aria-hidden="true"></i>
                    </div>
                    <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Belum ada proyek</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 leading-normal mb-4">Ajukan permohonan izin untuk memulai proyek pertama Anda</p>
                    <a href="{{ route('client.applications.create') }}"
                       class="inline-flex items-center gap-2 bg-[#0a66c2] text-white font-semibold text-sm px-5 py-2.5 rounded-lg hover:bg-[#004182] active:scale-95 transition-all">
                        <i class="fas fa-plus" aria-hidden="true"></i> Ajukan Permohonan
                    </a>
                </div>
                @endforelse
            </div>
        </section>

        {{-- ───── RECENT DOCUMENTS ───── --}}
        <section class="bg-white dark:bg-gray-800 border-y border-gray-200 dark:border-gray-700" aria-label="Dokumen terbaru">
            <div class="p-4 lg:p-5 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                <div>
                    <h2 class="text-base lg:text-lg font-semibold text-gray-900 dark:text-white leading-tight">Dokumen Terbaru</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5 leading-normal">
                        {{ $recentDocuments->count() }} dokumen terbaru
                    </p>
                </div>
                <a href="{{ route('client.documents.index') }}"
                   class="text-sm font-medium text-[#0a66c2] hover:text-[#004182] dark:text-blue-400 dark:hover:text-blue-300 px-3 py-2 min-h-[44px] flex items-center active:scale-95 transition-transform">
                    Kelola <i class="fas fa-arrow-right ml-2 text-xs" aria-hidden="true"></i>
                </a>
            </div>
            <div>
                @forelse($recentDocuments as $document)
                @php
                    $hasFile = !empty($document->file_path);
                    $ext = $hasFile ? strtolower(pathinfo($document->file_path, PATHINFO_EXTENSION)) : '';
                    $iconMap = [
                        'pdf' => 'fa-file-pdf text-red-500',
                        'doc' => 'fa-file-word text-blue-600',
                        'docx' => 'fa-file-word text-blue-600',
                        'xls' => 'fa-file-excel text-green-600',
                        'xlsx' => 'fa-file-excel text-green-600',
                        'jpg' => 'fa-file-image text-purple-500',
                        'jpeg' => 'fa-file-image text-purple-500',
                        'png' => 'fa-file-image text-purple-500',
                    ];
                    $fileIcon = $iconMap[$ext] ?? 'fa-file-alt text-gray-500 dark:text-gray-400';
                @endphp
                @if($hasFile)
                <a href="{{ Storage::url($document->file_path) }}" target="_blank" rel="noopener noreferrer"
                   class="flex items-center gap-3 px-4 lg:px-5 py-3.5 border-b border-gray-100 dark:border-gray-700 last:border-b-0 hover:bg-gray-50 dark:hover:bg-gray-700/50 active:bg-gray-100 dark:active:bg-gray-700 transition-colors">
                @else
                <div class="flex items-center gap-3 px-4 lg:px-5 py-3.5 border-b border-gray-100 dark:border-gray-700 last:border-b-0">
                @endif
                    <span class="w-10 h-10 rounded-lg bg-gray-100 dark:bg-gray-700 flex items-center justify-center flex-shrink-0">
                        <i class="fas {{ $fileIcon }}" aria-hidden="true"></i>
                    </span>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-gray-900 dark:text-white leading-tight truncate">{{ $document->title ?? $document->file_name ?? 'Dokumen tanpa judul' }}</p>
                        <div class="flex items-center gap-2 mt-1">
                            @if($document->category)
                            <span class="text-xs text-gray-500 dark:text-gray-400 capitalize">{{ $document->category }}</span>
                            <span class="text-gray-300 dark:text-gray-600">&middot;</span>
                            @endif
                            <span class="text-xs text-gray-400 dark:text-gray-500">{{ optional($document->created_at)->diffForHumans() }}</span>
                        </div>
                    </div>
                    @if($hasFile)
                    <i class="fas fa-download text-[#0a66c2] dark:text-blue-400 text-sm flex-shrink-0" aria-hidden="true"></i>
                    @else
                    <span class="text-xs text-amber-600 dark:text-amber-400 font-medium flex-shrink-0">
                        <i class="fas fa-exclamation-circle mr-1" aria-hidden="true"></i>Belum diupload
                    </span>
                    @endif
                @if($hasFile)
                </a>
                @else
                </div>
                @endif
                @empty
                <div class="px-4 lg:px-5 py-12 text-center">
                    <div class="w-16 h-16 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-file text-gray-400 dark:text-gray-500 text-2xl" aria-hidden="true"></i>
                    </div>
                    <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Belum ada dokumen</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 leading-normal">Dokumen akan muncul saat proyek Anda berjalan</p>
                </div>
                @endforelse
            </div>
        </section>

        {{-- ───── DEADLINE TIMELINE ───── --}}
        <section id="deadlines-section" class="bg-white dark:bg-gray-800 border-y border-gray-200 dark:border-gray-700" aria-label="Timeline deadline">
            <div class="p-4 lg:p-5 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                <div>
                    <h2 class="text-base lg:text-lg font-semibold text-gray-900 dark:text-white leading-tight">
                        Deadline Mendatang
                        @if($upcomingDeadlines->count() > 0)
                        <span class="inline-flex items-center justify-center ml-2 w-6 h-6 text-xs font-bold bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400 rounded-full align-middle">{{ $upcomingDeadlines->count() }}</span>
                        @endif
                    </h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5 leading-normal">Task dalam 7 hari ke depan</p>
                </div>
                @if($projects->count() > 0)
                <a href="{{ route('client.projects.index') }}"
                   class="text-sm font-medium text-[#0a66c2] hover:text-[#004182] dark:text-blue-400 dark:hover:text-blue-300 px-3 py-2 min-h-[44px] flex items-center active:scale-95 transition-transform">
                    Lihat Proyek <i class="fas fa-arrow-right ml-2 text-xs" aria-hidden="true"></i>
                </a>
                @endif
            </div>
            <div>
                @forelse($upcomingDeadlines as $task)
                @php
                    $daysLeft = optional($task->due_date)->diffInDays(now());
                    $isUrgent = $daysLeft !== null && $daysLeft <= 2;
                @endphp
                <div class="flex items-start gap-3 px-4 lg:px-5 py-4 border-b border-gray-100 dark:border-gray-700 last:border-b-0 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                    {{-- Priority indicator --}}
                    <span class="flex-shrink-0 inline-flex items-center justify-center w-9 h-9 rounded-full text-sm font-bold
                        {{ $isUrgent ? 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400' : 'bg-amber-50 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400' }}"
                        aria-label="{{ $isUrgent ? 'Mendesak' : 'Segera' }}">
                        {{ $loop->iteration }}
                    </span>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-gray-900 dark:text-white leading-tight">{{ $task->title }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 leading-normal truncate">{{ $task->project->name ?? 'Tanpa nama proyek' }}</p>
                        <div class="mt-2 flex flex-wrap items-center gap-2">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                                {{ $isUrgent ? 'bg-red-50 dark:bg-red-900/30 text-red-700 dark:text-red-400' : 'bg-amber-50 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400' }}">
                                <i class="fas fa-clock mr-1 text-[10px]" aria-hidden="true"></i>
                                {{ optional($task->due_date)->translatedFormat('d M Y') }}
                            </span>
                            @if($isUrgent)
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-50 dark:bg-red-900/30 text-red-700 dark:text-red-400">
                                <i class="fas fa-exclamation-triangle mr-1 text-[10px]" aria-hidden="true"></i>
                                {{ $daysLeft == 0 ? 'Hari ini!' : $daysLeft . ' hari lagi' }}
                            </span>
                            @endif
                            @if($task->assignedUser)
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400">
                                <i class="fas fa-user mr-1 text-[10px]" aria-hidden="true"></i>
                                {{ $task->assignedUser->name }}
                            </span>
                            @endif
                        </div>
                    </div>
                    <div class="text-right text-xs text-gray-400 dark:text-gray-500 hidden lg:block flex-shrink-0 whitespace-nowrap">
                        {{ optional($task->due_date)->diffForHumans() }}
                    </div>
                </div>
                @empty
                <div class="px-4 lg:px-5 py-12 text-center">
                    <div class="w-16 h-16 rounded-full bg-green-50 dark:bg-green-900/20 flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-calendar-check text-green-500 dark:text-green-400 text-2xl" aria-hidden="true"></i>
                    </div>
                    <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tidak ada deadline mendesak</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 leading-normal">Semua task dalam 7 hari ke depan sudah diselesaikan</p>
                </div>
                @endforelse
            </div>
        </section>

        {{-- ───── PENDING DOCUMENTS ALERT ───── --}}
        @if($pendingDocuments > 0)
        <section class="bg-white dark:bg-gray-800 border-y border-gray-200 dark:border-gray-700" aria-label="Peringatan dokumen pending">
            <a href="{{ route('client.documents.index') }}"
               class="flex items-center gap-4 px-4 lg:px-5 py-4 hover:bg-amber-50/50 dark:hover:bg-amber-900/10 transition-colors group">
                <span class="w-10 h-10 rounded-full bg-amber-100 dark:bg-amber-900/30 flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-exclamation-triangle text-amber-600 dark:text-amber-400" aria-hidden="true"></i>
                </span>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-gray-900 dark:text-white leading-tight">{{ $pendingDocuments }} Dokumen Belum Diupload</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5 leading-normal">Upload dokumen yang diperlukan agar proses izin tidak tertunda</p>
                </div>
                <i class="fas fa-chevron-right text-gray-300 dark:text-gray-600 text-xs flex-shrink-0 group-hover:text-[#0a66c2] transition-colors" aria-hidden="true"></i>
            </a>
        </section>
        @endif

    </div>
</div>

{{-- Notification Prompt Component --}}
<div data-has-applications="{{ $projects->count() > 0 ? 'true' : 'false' }}">
    @include('client.components.notification-prompt')
</div>

