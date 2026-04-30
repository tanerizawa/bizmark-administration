@extends('layouts.app')

@section('title', 'Proyek')
@section('page-title', 'Manajemen Proyek')

@section('content')
<div class="space-y-3">
    {{-- Compact Hero Section --}}
    <section class="card-elevated rounded-apple-lg admin-hero relative overflow-hidden">
        <div class="absolute inset-0 pointer-events-none" aria-hidden="true">
            <div class="w-48 h-48 bg-apple-blue opacity-20 blur-3xl rounded-full absolute -top-12 -right-8"></div>
        </div>
        <div class="relative flex flex-col md:flex-row md:items-center md:justify-between gap-3">
            <div class="flex-1 min-w-0">
                <p class="admin-hero-subtitle">Manajemen Proyek</p>
                <h1 class="admin-hero-title text-white">Proyek Perizinan</h1>
                <p class="admin-hero-desc">Kelola proyek dari awal hingga selesai</p>
            </div>
            <a href="{{ route('projects.create') }}" class="admin-btn admin-btn-sm rounded" style="background: rgba(10,132,255,0.25); color: #fff;">
                <i class="fas fa-plus mr-1"></i>Tambah Proyek
            </a>
        </div>
    </section>

    {{-- Compact Stats Row --}}
    <div class="grid grid-cols-4 gap-2">
        <div class="admin-stat-card card-elevated rounded-apple flex items-center gap-2" style="background: rgba(10,132,255,0.1); border: 1px solid rgba(10,132,255,0.2);">
            <div class="admin-stat-icon rounded flex items-center justify-center" style="background: rgba(10,132,255,0.2);">
                <i class="fas fa-folder text-apple-blue" style="font-size: 0.7rem;"></i>
            </div>
            <div>
                <p class="admin-small text-apple-blue uppercase tracking-wider">Total</p>
                <p class="admin-stat text-white">{{ $totalProjects }}</p>
            </div>
        </div>
        <div class="admin-stat-card card-elevated rounded-apple flex items-center gap-2" style="background: rgba(255,149,0,0.1); border: 1px solid rgba(255,149,0,0.2);">
            <div class="admin-stat-icon rounded flex items-center justify-center" style="background: rgba(255,149,0,0.2);">
                <i class="fas fa-spinner text-apple-orange" style="font-size: 0.7rem;"></i>
            </div>
            <div>
                <p class="admin-small text-apple-orange uppercase tracking-wider">Berjalan</p>
                <p class="admin-stat text-apple-orange">{{ $inProgressProjects }}</p>
            </div>
        </div>
        <div class="admin-stat-card card-elevated rounded-apple flex items-center gap-2" style="background: rgba(52,199,89,0.1); border: 1px solid rgba(52,199,89,0.2);">
            <div class="admin-stat-icon rounded flex items-center justify-center" style="background: rgba(52,199,89,0.2);">
                <i class="fas fa-check text-apple-green" style="font-size: 0.7rem;"></i>
            </div>
            <div>
                <p class="admin-small text-apple-green uppercase tracking-wider">Selesai</p>
                <p class="admin-stat text-apple-green">{{ $completedProjects }}</p>
            </div>
        </div>
        <div class="admin-stat-card card-elevated rounded-apple flex items-center gap-2" style="background: rgba(255,69,58,0.1); border: 1px solid rgba(255,69,58,0.2);">
            <div class="admin-stat-icon rounded flex items-center justify-center" style="background: rgba(255,69,58,0.2);">
                <i class="fas fa-exclamation text-apple-red" style="font-size: 0.7rem;"></i>
            </div>
            <div>
                <p class="admin-small text-apple-red uppercase tracking-wider">Terlambat</p>
                <p class="admin-stat text-apple-red">{{ $overdueProjects }}</p>
            </div>
        </div>
    </div>

    {{-- Compact Search and Filter --}}
    <div class="card-elevated rounded-apple p-3">
        <form method="GET" action="{{ route('projects.index') }}" class="flex flex-wrap gap-2 items-end">
            <div class="flex-1 min-w-[150px]">
                <label class="admin-label-compact block">Cari</label>
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Nama proyek..." 
                           class="admin-input w-full pl-7 rounded bg-dark-bg-secondary border border-dark-separator text-white">
                    <i class="fas fa-search absolute left-2.5 top-1/2 transform -translate-y-1/2" style="font-size: 0.625rem; color: rgba(235,235,245,0.3);"></i>
                </div>
            </div>
            <div class="w-32">
                <label class="admin-label-compact block">Status</label>
                <select name="status" class="admin-input admin-select w-full rounded bg-dark-bg-secondary border border-dark-separator text-white">
                    <option value="">Semua</option>
                    @foreach($statuses as $status)
                        <option value="{{ $status->id }}" {{ request('status') == $status->id ? 'selected' : '' }}>{{ $status->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="w-36">
                <label class="admin-label-compact block">Klien</label>
                <select name="client" class="admin-input admin-select w-full rounded bg-dark-bg-secondary border border-dark-separator text-white">
                    <option value="">Semua</option>
                    @foreach($clients as $client)
                        <option value="{{ $client->id }}" {{ request('client') == $client->id ? 'selected' : '' }}>{{ $client->company_name ?? $client->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="w-24">
                <label class="admin-label-compact block">Tahun</label>
                <select name="year" class="admin-input admin-select w-full rounded bg-dark-bg-secondary border border-dark-separator text-white">
                    <option value="">Semua</option>
                    @for($year = date('Y'); $year >= 2020; $year--)
                        <option value="{{ $year }}" {{ request('year') == $year ? 'selected' : '' }}>{{ $year }}</option>
                    @endfor
                </select>
            </div>
        </form>
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('form[action="{{ route('projects.index') }}"]');
            if (!form) return;
            form.querySelectorAll('select[name]').forEach(el => el.addEventListener('change', () => form.submit()));
            const searchInput = form.querySelector('input[name="search"]');
            if (searchInput) searchInput.addEventListener('keydown', e => { if (e.key === 'Enter') { e.preventDefault(); form.submit(); }});
        });
        </script>
    </div>

    <!-- Projects Table Card -->
    <div class="card-elevated rounded-apple-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-700 text-sm">
                <thead style="background-color: var(--dark-bg-secondary);">
                    <tr>
                        <th scope="col" class="px-4 py-2.5 text-left text-[11px] font-semibold uppercase tracking-[0.2em] text-dark-text-secondary">Proyek</th>
                        <th scope="col" class="px-4 py-2.5 text-left text-[11px] font-semibold uppercase tracking-[0.2em] text-dark-text-secondary">Klien</th>
                        <th scope="col" class="px-4 py-2.5 text-left text-[11px] font-semibold uppercase tracking-[0.2em] text-dark-text-secondary">Status</th>
                        <th scope="col" class="px-4 py-2.5 text-left text-[11px] font-semibold uppercase tracking-[0.2em] text-dark-text-secondary">Deadline</th>
                        <th scope="col" class="px-4 py-2.5 text-center text-[11px] font-semibold uppercase tracking-[0.2em] text-dark-text-secondary">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-700" style="background-color: var(--dark-bg-secondary);">
                    @forelse($projects as $project)
                        <tr class="hover-lift transition-apple" style="cursor: pointer;" onclick="window.location='{{ route('projects.show', $project) }}'">
                            <td class="px-4 py-2.5">
                                <div class="font-semibold text-sm text-dark-text-primary">{{ $project->name }}</div>
                                @if($project->description)
                                    <div class="text-xs text-dark-text-secondary mt-1 line-clamp-1">
                                        {{ Str::limit($project->description, 80) }}
                                    </div>
                                @endif
                            </td>
                            <td class="px-4 py-2.5 whitespace-nowrap">
                                @if($project->client)
                                    <div class="flex items-center space-x-1.5">
                                        <span class="text-sm text-dark-text-primary">{{ $project->client->company_name ?? $project->client->name }}</span>
                                        <a href="{{ route('clients.show', $project->client) }}" 
                                           onclick="event.stopPropagation()"
                                           class="text-xs px-1.5 py-0.5 rounded transition-colors" 
                                           style="background: rgba(0, 122, 255, 0.1); color: var(--apple-blue);">
                                            <i class="fas fa-external-link-alt"></i>
                                        </a>
                                    </div>
                                @else
                                    <span class="text-sm text-dark-text-secondary">{{ $project->client_name ?? '-' }}</span>
                                @endif
                            </td>
                            <td class="px-4 py-2.5 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs font-medium rounded-apple"
                                      style="background-color: {{ $project->status->color ?? '#6B7280' }}20; color: {{ $project->status->color ?? '#6B7280' }}">
                                    {{ $project->status->name ?? 'N/A' }}
                                </span>
                            </td>
                            <td class="px-4 py-2.5 whitespace-nowrap">
                                @if($project->deadline)
                                    @php
                                        // Fix: Check if project is completed first
                                        if ($project->completed_at) {
                                            $completionStatus = $project->getCompletionStatus();
                                            $deadlineColor = match($completionStatus) {
                                                'early' => '#5AC8FA',      // Blue (completed early)
                                                'on-time' => '#34C759',    // Green (on-time)
                                                'late' => '#FF9F0A',       // Orange (late)
                                                default => '#FFFFFF'
                                            };
                                        } else {
                                            // Ongoing project - check if overdue
                                            $deadlineColor = $project->deadline->isPast() ? '#FF453A' : '#FFFFFF';
                                        }
                                    @endphp
                                    <div class="text-sm" style="color: {{ $deadlineColor }};">
                                        {{ $project->deadline->format('d M Y') }}
                                    </div>
                                @else
                                    <span class="text-sm text-dark-text-secondary">-</span>
                                @endif
                            </td>
                            <td class="px-4 py-2.5 whitespace-nowrap text-center" onclick="event.stopPropagation()">
                                <div class="flex items-center justify-center space-x-1.5">
                                    <a href="{{ route('projects.show', $project) }}" 
                                       class="inline-flex items-center px-2.5 py-1 rounded-apple text-xs font-semibold transition-apple" 
                                       style="background-color: rgba(90, 200, 250, 0.15); color: var(--apple-teal); border: 1px solid rgba(90, 200, 250, 0.25);">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('projects.edit', $project) }}" 
                                       class="inline-flex items-center px-2.5 py-1 rounded-apple text-xs font-semibold transition-apple" 
                                       style="background-color: rgba(255, 149, 0, 0.15); color: var(--apple-orange); border: 1px solid rgba(255, 149, 0, 0.25);">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button onclick="event.stopPropagation(); deleteProject({{ $project->id }})" 
                                            class="inline-flex items-center px-2.5 py-1 rounded-apple text-xs font-semibold transition-apple" 
                                            style="background-color: rgba(255, 59, 48, 0.15); color: var(--apple-red); border: 1px solid rgba(255, 59, 48, 0.25);">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <i class="fas fa-project-diagram text-3xl mb-3" style="color: rgba(235, 235, 245, 0.3);"></i>
                                    <h3 class="admin-section mb-1" style="color: #FFFFFF;">Belum Ada Proyek</h3>
                                    <p class="admin-body mb-3" style="color: rgba(235, 235, 245, 0.6);">Mulai dengan membuat proyek pertama</p>
                                    <a href="{{ route('projects.create') }}" class="admin-btn inline-flex items-center">
                                        <i class="fas fa-plus mr-1.5"></i>Tambah Proyek
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    @if($projects->hasPages())
        <div class="rounded-apple-lg px-4 py-3 mt-4" style="background-color: #2C2C2E; border: 1px solid rgba(84, 84, 88, 0.65); box-shadow: 0 2px 8px rgba(0, 0, 0, 0.48);">
            {{ $projects->withQueryString()->links('pagination::tailwind') }}
        </div>
    @endif

@endsection

@push('scripts')
<script>
// Delete Project Function
function deleteProject(id) {
    if (confirm('Apakah Anda yakin ingin menghapus proyek ini?')) {
        // Get CSRF token from meta tag
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/projects/${id}`;
        form.innerHTML = `
            <input type="hidden" name="_method" value="DELETE">
            <input type="hidden" name="_token" value="${csrfToken}">
        `;
        document.body.appendChild(form);
        form.submit();
    }
}
</script>
@endpush
