@extends('client.layouts.app')

@section('title', 'Dashboard')

@push('styles')
<style>
    /* Pull-to-refresh indicator */
    .pull-to-refresh {
        position: absolute;
        top: -60px;
        left: 0;
        right: 0;
        height: 60px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: white;
        transition: top 0.3s ease;
        z-index: 10;
    }
    @media (prefers-color-scheme: dark) {
        .pull-to-refresh { background: #1f2937; }
    }
    .pull-to-refresh.active { top: 0; }
    .pull-to-refresh i {
        font-size: 24px;
        color: #0A66C2;
        animation: ptr-spin 1s linear infinite;
    }
    @keyframes ptr-spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
    /* Progress bar animation */
    .progress-bar-fill {
        transition: width 0.8s cubic-bezier(0.4, 0, 0.2, 1);
    }
</style>
@endpush

@section('content')
@php
    $statusColors = [
        'Selesai' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400',
        'Dalam Proses' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400',
        'Sedang Diproses' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400',
        'Draft' => 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300',
        'Dokumen Kurang' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400',
    ];

    $documentCompletion = $totalDocuments > 0
        ? round(($uploadedDocuments / $totalDocuments) * 100)
        : 0;

    // Smart currency display
    $investDisplay = $totalInvested >= 1000000000
        ? 'Rp ' . number_format($totalInvested / 1000000000, 1) . 'M'
        : ($totalInvested >= 1000000
            ? 'Rp ' . number_format($totalInvested / 1000000, 0) . 'Jt'
            : ($totalInvested > 0
                ? 'Rp ' . number_format($totalInvested, 0, ',', '.')
                : '-'));
@endphp

<div class="space-y-0" role="main" aria-label="Dashboard Client">

    {{-- ============================================================ --}}
    {{-- MOBILE HERO HEADER                                           --}}
    {{-- ============================================================ --}}
    <div class="lg:hidden bg-[#0a66c2] text-white px-4 py-5 border-y border-[#0a66c2]" role="banner">
        {{-- Greeting + Notification bell --}}
        <div class="flex items-center justify-between mb-4">
            <div class="min-w-0 flex-1">
                <p class="text-xs text-white/70 leading-tight">Selamat datang kembali</p>
                <h1 class="text-lg font-bold leading-tight truncate mt-0.5">{{ $client->name }}</h1>
            </div>
            @if($upcomingDeadlines->count() > 0)
            <a href="#deadlines-section"
               class="flex items-center gap-1.5 bg-white/20 backdrop-blur px-3 py-2 rounded-full min-h-[44px] active:scale-95 transition-transform flex-shrink-0 ml-3"
               aria-label="{{ $upcomingDeadlines->count() }} deadline mendesak">
                <i class="fas fa-bell text-sm"></i>
                <span class="text-xs font-semibold">{{ $upcomingDeadlines->count() }}</span>
            </a>
            @endif
        </div>

        {{-- Stats 2×2 Grid --}}
        <div class="grid grid-cols-2 gap-2" role="list" aria-label="Ringkasan statistik">
            <div class="bg-white/10 backdrop-blur border border-white/20 px-3 py-2.5" role="listitem">
                <div class="flex items-center justify-between mb-0.5">
                    <p class="text-xs text-white/70 leading-tight">Proyek Aktif</p>
                    <i class="fas fa-folder-open text-white/40 text-xs" aria-hidden="true"></i>
                </div>
                <p class="text-2xl font-bold leading-tight">{{ $activeProjects }}</p>
            </div>
            <div class="bg-white/10 backdrop-blur border border-white/20 px-3 py-2.5" role="listitem">
                <div class="flex items-center justify-between mb-0.5">
                    <p class="text-xs text-white/70 leading-tight">Selesai</p>
                    <i class="fas fa-check-circle text-emerald-400 text-xs" aria-hidden="true"></i>
                </div>
                <p class="text-2xl font-bold leading-tight">{{ $completedProjects }}</p>
            </div>
            <div class="bg-white/10 backdrop-blur border border-white/20 px-3 py-2.5" role="listitem">
                <div class="flex items-center justify-between mb-0.5">
                    <p class="text-xs text-white/70 leading-tight">Deadline 7 Hari</p>
                    <i class="fas fa-clock text-amber-400 text-xs" aria-hidden="true"></i>
                </div>
                <p class="text-2xl font-bold leading-tight">{{ $upcomingDeadlines->count() }}</p>
            </div>
            <div class="bg-white/10 backdrop-blur border border-white/20 px-3 py-2.5" role="listitem">
                <div class="flex items-center justify-between mb-0.5">
                    <p class="text-xs text-white/70 leading-tight">Total Investasi</p>
                    <i class="fas fa-wallet text-white/40 text-xs" aria-hidden="true"></i>
                </div>
                <p class="text-xl font-bold leading-tight">{{ $investDisplay }}</p>
            </div>
        </div>

        {{-- Document Progress Bar --}}
        @if($totalDocuments > 0)
        <div class="mt-3 bg-white/10 backdrop-blur border border-white/20 px-3 py-2.5" role="progressbar" aria-valuenow="{{ $documentCompletion }}" aria-valuemin="0" aria-valuemax="100" aria-label="Progres kelengkapan dokumen">
            <div class="flex items-center justify-between mb-1.5">
                <span class="text-xs text-white/70 flex items-center gap-1.5">
                    <i class="fas fa-file-alt text-xs" aria-hidden="true"></i> Kelengkapan Dokumen
                </span>
                <span class="text-xs font-semibold">{{ $uploadedDocuments }}/{{ $totalDocuments }}</span>
            </div>
            <div class="w-full h-1.5 bg-white/20 rounded-full overflow-hidden">
                <div class="h-full rounded-full progress-bar-fill {{ $documentCompletion >= 100 ? 'bg-emerald-400' : 'bg-white' }}" style="width: {{ $documentCompletion }}%"></div>
            </div>
        </div>
        @endif

        {{-- Quick Actions --}}
        <div class="mt-3 grid grid-cols-3 gap-2">
            <a href="{{ route('client.applications.create') }}"
               class="flex flex-col items-center gap-1 px-2 py-2.5 bg-white/10 backdrop-blur border border-white/20 rounded-lg min-h-[44px] active:scale-95 transition-transform"
               aria-label="Ajukan permohonan baru">
                <i class="fas fa-plus text-base" aria-hidden="true"></i>
                <span class="text-[11px] font-medium leading-tight">Ajukan</span>
            </a>
            <a href="{{ route('client.documents.index') }}"
               class="flex flex-col items-center gap-1 px-2 py-2.5 bg-white/10 backdrop-blur border border-white/20 rounded-lg min-h-[44px] active:scale-95 transition-transform"
               aria-label="Kelola dokumen">
                <i class="fas fa-folder text-base" aria-hidden="true"></i>
                <span class="text-[11px] font-medium leading-tight">Dokumen</span>
            </a>
            <a href="{{ route('client.projects.index') }}"
               class="flex flex-col items-center gap-1 px-2 py-2.5 bg-white/10 backdrop-blur border border-white/20 rounded-lg min-h-[44px] active:scale-95 transition-transform"
               aria-label="Lihat semua proyek">
                <i class="fas fa-briefcase text-base" aria-hidden="true"></i>
                <span class="text-[11px] font-medium leading-tight">Proyek</span>
            </a>
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- DESKTOP HERO HEADER                                          --}}
    {{-- ============================================================ --}}
    <div class="hidden lg:block bg-[#0a66c2] border-y border-[#0a66c2] text-white" role="banner">
        <div class="max-w-6xl mx-auto px-6 lg:px-8 py-8">
            {{-- Title + CTA --}}
            <div class="flex items-start justify-between gap-8 mb-6">
                <div class="flex-1">
                    <h1 class="text-2xl lg:text-3xl font-bold leading-tight mb-2">
                        Hai, {{ $client->name }}
                    </h1>
                    <p class="text-base text-white/90 leading-normal">
                        Pantau progres izin usaha dan kelola proyek Anda
                    </p>
                </div>
                <div class="flex gap-3 flex-shrink-0">
                    <a href="{{ route('client.applications.create') }}"
                       class="inline-flex items-center gap-2 bg-white text-[#0a66c2] font-semibold px-5 py-3 hover:shadow-lg active:scale-95 transition-all">
                        <i class="fas fa-plus" aria-hidden="true"></i> Ajukan Permohonan
                    </a>
                    <a href="{{ route('client.services.index') }}"
                       class="inline-flex items-center gap-2 bg-white/10 backdrop-blur border border-white/30 px-5 py-3 font-semibold hover:bg-white/20 active:scale-95 transition-all">
                        <i class="fas fa-layer-group" aria-hidden="true"></i> Jelajahi Layanan
                    </a>
                </div>
            </div>

            {{-- Stats Grid 4-col --}}
            <div class="grid grid-cols-4 gap-4" role="list" aria-label="Ringkasan statistik">
                <div class="bg-white/10 backdrop-blur border border-white/20 px-5 py-4" role="listitem">
                    <div class="flex items-center justify-between mb-2">
                        <p class="text-xs uppercase tracking-wider text-white/70 leading-tight">Proyek Aktif</p>
                        <i class="fas fa-folder-open text-white/50" aria-hidden="true"></i>
                    </div>
                    <p class="text-3xl font-bold leading-tight">{{ $activeProjects }}</p>
                    <p class="text-xs text-white/60 mt-1">dari {{ $projects->count() }} total</p>
                </div>
                <div class="bg-white/10 backdrop-blur border border-white/20 px-5 py-4" role="listitem">
                    <div class="flex items-center justify-between mb-2">
                        <p class="text-xs uppercase tracking-wider text-white/70 leading-tight">Selesai</p>
                        <i class="fas fa-check-circle text-emerald-400" aria-hidden="true"></i>
                    </div>
                    <p class="text-3xl font-bold leading-tight">{{ $completedProjects }}</p>
                    <p class="text-xs text-white/60 mt-1">{{ $projects->count() > 0 ? round(($completedProjects / $projects->count()) * 100) : 0 }}% tingkat selesai</p>
                </div>
                <div class="bg-white/10 backdrop-blur border border-white/20 px-5 py-4" role="listitem">
                    <div class="flex items-center justify-between mb-2">
                        <p class="text-xs uppercase tracking-wider text-white/70 leading-tight">Deadline 7 Hari</p>
                        <i class="fas fa-clock text-amber-400" aria-hidden="true"></i>
                    </div>
                    <p class="text-3xl font-bold leading-tight">{{ $upcomingDeadlines->count() }}</p>
                    <p class="text-xs text-white/60 mt-1">{{ $pendingDocuments > 0 ? $pendingDocuments . ' dokumen pending' : 'Semua terkendali' }}</p>
                </div>
                <div class="bg-white/10 backdrop-blur border border-white/20 px-5 py-4" role="listitem">
                    <div class="flex items-center justify-between mb-2">
                        <p class="text-xs uppercase tracking-wider text-white/70 leading-tight">Total Investasi</p>
                        <i class="fas fa-wallet text-white/50" aria-hidden="true"></i>
                    </div>
                    <p class="text-2xl font-bold leading-tight">{{ $investDisplay }}</p>
                    <p class="text-xs text-white/60 mt-1">Nilai kontrak</p>
                </div>
            </div>

            {{-- Desktop Progress Bar --}}
            @if($totalDocuments > 0)
            <div class="mt-4 bg-white/10 backdrop-blur border border-white/20 px-5 py-3 flex items-center gap-4" role="progressbar" aria-valuenow="{{ $documentCompletion }}" aria-valuemin="0" aria-valuemax="100" aria-label="Progres kelengkapan dokumen">
                <span class="text-sm text-white/80 flex items-center gap-2 whitespace-nowrap">
                    <i class="fas fa-file-alt text-xs" aria-hidden="true"></i> Kelengkapan Dokumen
                </span>
                <div class="flex-1 h-2 bg-white/20 rounded-full overflow-hidden">
                    <div class="h-full rounded-full progress-bar-fill {{ $documentCompletion >= 100 ? 'bg-emerald-400' : 'bg-white' }}" style="width: {{ $documentCompletion }}%"></div>
                </div>
                <span class="text-sm font-semibold whitespace-nowrap">{{ $uploadedDocuments }}/{{ $totalDocuments }} ({{ $documentCompletion }}%)</span>
            </div>
            @endif
        </div>
    </div>

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

@endsection

@push('scripts')
<script>
    // Pull-to-refresh for mobile PWA
    if ('ontouchstart' in window && window.innerWidth < 1024) {
        let startY = 0, currentY = 0, pulling = false;
        const mainContent = document.querySelector('main');
        const threshold = 80;

        if (mainContent) {
            const indicator = document.createElement('div');
            indicator.className = 'pull-to-refresh';
            indicator.innerHTML = '<i class="fas fa-sync-alt"></i>';
            indicator.setAttribute('aria-hidden', 'true');
            mainContent.style.position = 'relative';
            mainContent.insertBefore(indicator, mainContent.firstChild);

            mainContent.addEventListener('touchstart', (e) => {
                if (window.scrollY === 0) {
                    startY = e.touches[0].clientY;
                    pulling = true;
                }
            }, { passive: true });

            mainContent.addEventListener('touchmove', (e) => {
                if (!pulling) return;
                currentY = e.touches[0].clientY;
                const diff = currentY - startY;
                if (diff > 0 && window.scrollY === 0) {
                    const pull = Math.min(diff, threshold * 1.5);
                    indicator.style.top = `${pull - 60}px`;
                    if (pull >= threshold) indicator.classList.add('active');
                }
            }, { passive: true });

            mainContent.addEventListener('touchend', () => {
                if (!pulling) return;
                const diff = currentY - startY;
                if (diff > threshold && window.scrollY === 0) {
                    indicator.style.top = '0';
                    indicator.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
                    setTimeout(() => window.location.reload(), 400);
                } else {
                    indicator.style.top = '-60px';
                    indicator.classList.remove('active');
                }
                pulling = false;
                startY = 0;
                currentY = 0;
            });
        }
    }

    // Animate progress bars on load
    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('.progress-bar-fill').forEach(bar => {
            const width = bar.style.width;
            bar.style.width = '0%';
            requestAnimationFrame(() => {
                requestAnimationFrame(() => { bar.style.width = width; });
            });
        });
    });
</script>
@endpush
