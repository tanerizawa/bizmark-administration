@extends('client.layouts.app')

@section('title', 'Proyek Saya')

@section('content')
@php
    $totalProjects = $stats['total'] ?? 0;
    $activeProjects = $stats['active'] ?? 0;
    $completedProjects = $stats['completed'] ?? 0;
    $totalValue = $stats['total_value'] ?? 0;
    $sortBy = request('sort_by', 'created_at');
    $sortOrder = request('sort_order', 'desc');
@endphp

<div class="space-y-1">
    <!-- Mobile Compact Hero (PWA only) -->
    <div class="lg:hidden border-y border-[#0a66c2] bg-[#0a66c2] text-white px-4 lg:px-6 py-4">
        <div class="flex items-center justify-between mb-3">
            <div>
                <p class="text-xs text-white/70">Proyek Anda</p>
                <h1 class="text-lg font-bold">{{ $totalProjects }} Proyek</h1>
            </div>
            @if($activeProjects > 0)
            <a href="#projects" class="flex items-center gap-1.5 bg-white/20 backdrop-blur px-3 py-1.5 rounded-full active:scale-95 transition-transform">
                <i class="fas fa-spinner text-sm"></i>
                <span class="text-xs font-semibold">{{ $activeProjects }} aktif</span>
            </a>
            @endif
        </div>
        
        <!-- Compact Stats Grid (2x2, no horizontal scroll) -->
        <div class="grid grid-cols-2 gap-2 text-sm">
            <div class="bg-white/10 backdrop-blur border border-white/20 p-2.5">
                <p class="text-xs text-white/70">Total</p>
                <p class="text-xl font-bold">{{ $totalProjects }}</p>
            </div>
            <div class="bg-white/10 backdrop-blur border border-white/20 p-2.5">
                <p class="text-xs text-white/70">Aktif</p>
                <p class="text-xl font-bold text-green-300">{{ $activeProjects }}</p>
            </div>
            <div class="bg-white/10 backdrop-blur border border-white/20 p-2.5">
                <p class="text-xs text-white/70">Selesai</p>
                <p class="text-xl font-bold">{{ $completedProjects }}</p>
            </div>
            <div class="bg-white/10 backdrop-blur border border-white/20 p-2.5">
                <p class="text-xs text-white/70">Nilai</p>
                <p class="text-base font-bold">{{ number_format($totalValue / 1000000, 0) }}Jt</p>
            </div>
        </div>
    </div>

    <!-- Desktop Hero -->
    <div class="hidden lg:block border-y border-[#0a66c2] bg-[#0a66c2] text-white overflow-hidden">
        <div class="px-6 lg:px-8 py-8 flex items-center justify-between gap-8">
            <div class="space-y-4 flex-1">
                <p class="text-xs uppercase tracking-[0.35em] text-white/70">Monitor Proyek</p>
                <h1 class="text-3xl font-bold leading-tight">Pantau progres seluruh proyek perizinan Anda secara real-time.</h1>
                <p class="text-white/80">Lihat status terkini, nilai kontrak, dan timeline pengerjaan yang ditangani tim Bizmark.</p>
                <div class="flex flex-wrap gap-3 pt-2">
                    <a href="{{ route('client.applications.index') }}" class="inline-flex items-center gap-2 bg-white text-[#0a66c2] font-semibold px-5 py-3 shadow hover:shadow-md active:scale-95 transition-all">
                        <i class="fas fa-file-signature"></i> Lihat Permohonan
                    </a>
                    <a href="{{ route('client.documents.index') }}" class="inline-flex items-center gap-2 bg-white/10 border border-white/30 px-5 py-3 font-semibold hover:bg-white/20 active:scale-95 transition-all">
                        <i class="fas fa-paperclip"></i> Kelola Dokumen
                    </a>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4 w-80">
                <div class="bg-white/15 border border-white/20 p-5 backdrop-blur">
                    <p class="text-xs uppercase tracking-wide text-white/70">Total Proyek</p>
                    <p class="text-3xl font-bold mt-2">{{ $totalProjects }}</p>
                    <p class="text-xs text-white/70 mt-1">{{ $completedProjects }} selesai</p>
                </div>
                <div class="bg-white/15 border border-white/20 p-5 backdrop-blur">
                    <p class="text-xs uppercase tracking-wide text-white/70">Proyek Aktif</p>
                    <p class="text-3xl font-bold mt-2 text-green-300">{{ $activeProjects }}</p>
                    <p class="text-xs text-white/70 mt-1">Sedang dikerjakan</p>
                </div>
                <div class="bg-white/15 border border-white/20 p-5 backdrop-blur col-span-2">
                    <p class="text-xs uppercase tracking-wide text-white/70">Total Nilai Kontrak</p>
                    <p class="text-2xl font-bold mt-2">Rp {{ number_format($totalValue, 0, ',', '.') }}</p>
                    <p class="text-xs text-white/70 mt-1">Termasuk DP yang telah diterima</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Projects -->
    @if($projects->count() > 0)
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
        @foreach($projects as $project)
        @php
            $status = $project->status;
            $progress = $project->progress_percentage ?? null;
            $documentsCount = $project->documents_count ?? 0;
            $tasksCount = $project->tasks_count ?? 0;
        @endphp
        <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-lg hover:border-[#0a66c2]/30 transition-all p-5 flex flex-col gap-4">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <a href="{{ route('client.projects.show', $project->id) }}" class="text-lg font-semibold text-gray-900 dark:text-white hover:text-[#0a66c2] active:scale-[0.98] inline-block transition-transform">
                        {{ $project->name }}
                    </a>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                        {{ $project->institution->name ?? 'Instansi belum ditetapkan' }}
                    </p>
                </div>
                <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $status && $status->is_final ? 'bg-emerald-100 text-emerald-700' : 'bg-sky-100 text-sky-700' }}">
                    {{ $status->name ?? 'Tanpa status' }}
                </span>
            </div>
            <div class="text-sm space-y-2 text-gray-600 dark:text-gray-400">
                <p><strong>Jenis Izin:</strong> {{ $project->permitApplication->permitType->name ?? 'N/A' }}</p>
                <p><strong>Nilai Kontrak:</strong> Rp {{ number_format($project->contract_value ?? 0, 0, ',', '.') }}</p>
            </div>
            <div class="space-y-3">
                @if(!is_null($progress))
                <div>
                    <div class="flex items-center justify-between text-xs text-gray-500 dark:text-gray-400">
                        <span>Progress Pengerjaan</span>
                        <span>{{ $progress }}%</span>
                    </div>
                    <div class="mt-1 h-2 bg-gray-100 dark:bg-gray-800 rounded-full overflow-hidden">
                        <div class="h-full bg-gradient-to-r bg-[#0a66c2]" style="width: {{ min(100, $progress) }}%"></div>
                    </div>
                </div>
                @endif
                <div class="flex flex-wrap gap-3 text-xs text-gray-500 dark:text-gray-400">
                    <span class="inline-flex items-center gap-1"><i class="fas fa-paperclip text-gray-400"></i>{{ $documentsCount }} Dokumen</span>
                    <span class="inline-flex items-center gap-1"><i class="fas fa-tasks text-gray-400"></i>{{ $tasksCount }} Task</span>
                    <span class="inline-flex items-center gap-1"><i class="fas fa-clock text-gray-400"></i>Update {{ optional($project->updated_at)->diffForHumans() }}</span>
                </div>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('client.projects.show', $project->id) }}" class="flex-1 px-4 py-2.5 text-center text-sm font-semibold bg-[#0a66c2] text-white hover:bg-[#004182] active:scale-95 transition-all">
                    Detail Proyek
                </a>
                <a href="{{ route('client.documents.index') }}" class="px-4 py-2.5 text-center text-sm font-semibold bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-700 active:scale-95 transition-all">
                    Dokumen
                </a>
            </div>
        </div>
        @endforeach
    </div>

    <div class="border-y border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 px-4 py-4">
        {{ $projects->withQueryString()->links() }}
    </div>
    @else
    <div class="border-y border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 px-4 lg:px-6 py-16 text-center">
        <i class="fas fa-road text-5xl text-gray-300 dark:text-gray-600 mb-4"></i>
        <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">Belum ada proyek tercatat</h3>
        <p class="text-gray-600 dark:text-gray-400 mb-6">Ajukan permohonan izin terlebih dahulu, dan proyek akan otomatis tercatat di sini.</p>
        <a href="{{ route('client.services.index') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-[#0a66c2] hover:bg-[#004182] text-white font-semibold active:scale-95 transition-all">
            <i class="fas fa-plus"></i> Ajukan Permohonan
        </a>
    </div>
    @endif
</div>
@endsection
