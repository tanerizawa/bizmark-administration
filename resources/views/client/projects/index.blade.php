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

<div class="space-y-8">
    <!-- Hero -->
    <div class="bg-gradient-to-r from-slate-900 via-indigo-900 to-blue-800 text-white rounded-3xl shadow-xl overflow-hidden relative">
        <div class="absolute inset-0 opacity-20 bg-[url('https://www.toptal.com/designers/subtlepatterns/uploads/dot-grid.png')]"></div>
        <div class="relative p-6 lg:p-8 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
            <div class="space-y-3 flex-1">
                <p class="text-xs uppercase tracking-[0.35em] text-white/70">Monitor Proyek</p>
                <h1 class="text-3xl font-bold leading-tight">Pantau progres seluruh proyek perizinan Anda secara real-time.</h1>
                <p class="text-white/80">Lihat status terkini, nilai kontrak, dan timeline pengerjaan yang ditangani tim Bizmark.</p>
                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('client.applications.index') }}" class="inline-flex items-center gap-2 bg-white text-indigo-700 font-semibold px-5 py-3 rounded-xl shadow">
                        <i class="fas fa-file-signature"></i> Lihat Permohonan
                    </a>
                    <a href="{{ route('client.documents.index') }}" class="inline-flex items-center gap-2 bg-white/10 border border-white/30 px-5 py-3 rounded-xl font-semibold">
                        <i class="fas fa-paperclip"></i> Kelola Dokumen
                    </a>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4 w-full lg:w-auto">
                <div class="bg-white/15 rounded-2xl p-4 backdrop-blur">
                    <p class="text-xs uppercase tracking-wide text-white/70">Total Proyek</p>
                    <p class="text-3xl font-bold">{{ $totalProjects }}</p>
                    <p class="text-xs text-white/70 mt-1">{{ $completedProjects }} selesai</p>
                </div>
                <div class="bg-white/15 rounded-2xl p-4 backdrop-blur">
                    <p class="text-xs uppercase tracking-wide text-white/70">Proyek Aktif</p>
                    <p class="text-3xl font-bold">{{ $activeProjects }}</p>
                    <p class="text-xs text-white/70 mt-1">Sedang dikerjakan</p>
                </div>
                <div class="bg-white/15 rounded-2xl p-4 backdrop-blur col-span-2">
                    <p class="text-xs uppercase tracking-wide text-white/70">Total Nilai Kontrak</p>
                    <p class="text-2xl font-bold">Rp {{ number_format($totalValue, 0, ',', '.') }}</p>
                    <p class="text-xs text-white/70 mt-1">Termasuk DP yang telah diterima</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-800 p-5 space-y-4">
        <form method="GET" action="{{ route('client.projects.index') }}" class="grid grid-cols-1 lg:grid-cols-4 gap-4">
            <div class="lg:col-span-2">
                <label class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Cari Proyek</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Contoh: Proyek OSS Kawasan Industri" class="mt-1 w-full px-4 py-2.5 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-indigo-500 dark:bg-gray-800 dark:text-white">
            </div>
            <div>
                <label class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Status</label>
                <select name="status" class="mt-1 w-full px-4 py-2.5 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-indigo-500 dark:bg-gray-800 dark:text-white">
                    <option value="">Semua Status</option>
                    @foreach($statuses as $status)
                        <option value="{{ $status->id }}" {{ request('status') == $status->id ? 'selected' : '' }}>
                            {{ $status->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="grid grid-cols-2 gap-2">
                <div>
                    <label class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Urutkan</label>
                    <select name="sort_by" class="mt-1 w-full px-3 py-2 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-indigo-500 dark:bg-gray-800 dark:text-white">
                        <option value="created_at" {{ $sortBy === 'created_at' ? 'selected' : '' }}>Tanggal dibuat</option>
                        <option value="name" {{ $sortBy === 'name' ? 'selected' : '' }}>Nama proyek</option>
                        <option value="contract_value" {{ $sortBy === 'contract_value' ? 'selected' : '' }}>Nilai kontrak</option>
                    </select>
                </div>
                <div>
                    <label class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Urutan</label>
                    <select name="sort_order" class="mt-1 w-full px-3 py-2 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-indigo-500 dark:bg-gray-800 dark:text-white">
                        <option value="asc" {{ $sortOrder === 'asc' ? 'selected' : '' }}>Naik</option>
                        <option value="desc" {{ $sortOrder === 'desc' ? 'selected' : '' }}>Turun</option>
                    </select>
                </div>
            </div>
            <div class="lg:col-span-4 flex flex-wrap gap-2 justify-end">
                <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-semibold">
                    <i class="fas fa-filter"></i> Terapkan
                </button>
                @if(request()->except(['page']))
                <a href="{{ route('client.projects.index') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-200 rounded-xl font-semibold">
                    <i class="fas fa-redo"></i> Reset
                </a>
                @endif
            </div>
        </form>
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
        <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm hover:shadow-lg transition p-5 flex flex-col gap-4">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <a href="{{ route('client.projects.show', $project->id) }}" class="text-lg font-semibold text-gray-900 dark:text-white hover:text-indigo-600">
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
                        <div class="h-full bg-gradient-to-r from-indigo-500 to-blue-500" style="width: {{ min(100, $progress) }}%"></div>
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
                <a href="{{ route('client.projects.show', $project->id) }}" class="flex-1 px-4 py-2.5 rounded-xl text-center text-sm font-semibold bg-indigo-600 text-white hover:bg-indigo-700 transition">
                    Detail Proyek
                </a>
                <a href="{{ route('client.documents.index') }}" class="px-4 py-2.5 rounded-xl text-center text-sm font-semibold bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-700 transition">
                    Dokumen
                </a>
            </div>
        </div>
        @endforeach
    </div>

    <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm p-4">
        {{ $projects->withQueryString()->links() }}
    </div>
    @else
    <div class="bg-white dark:bg-gray-900 rounded-2xl border border-dashed border-gray-200 dark:border-gray-700 p-12 text-center">
        <i class="fas fa-road text-5xl text-gray-300 dark:text-gray-600 mb-4"></i>
        <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">Belum ada proyek tercatat</h3>
        <p class="text-gray-600 dark:text-gray-400 mb-6">Ajukan permohonan izin terlebih dahulu, dan proyek akan otomatis tercatat di sini.</p>
        <a href="{{ route('client.services.index') }}" class="inline-flex items-center gap-2 px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-semibold">
            <i class="fas fa-plus"></i> Ajukan Permohonan
        </a>
    </div>
    @endif
</div>
@endsection
