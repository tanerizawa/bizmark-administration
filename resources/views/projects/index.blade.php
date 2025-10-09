@extends('layouts.app')

@section('title', 'Proyek')
@section('page-title', 'Manajemen Proyek')

@section('content')
    <!-- Header Actions -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4 space-y-3 sm:space-y-0">
        <div>
            <p class="text-sm" style="color: rgba(235, 235, 245, 0.6);">Kelola semua proyek bisnis dan perizinan</p>
        </div>
        <div class="flex items-center space-x-3">
            <a href="{{ route('projects.create') }}" 
               class="btn-primary px-4 py-2 text-white rounded-apple text-sm font-medium">
                <i class="fas fa-plus mr-2"></i>
                Tambah Proyek
            </a>
        </div>
    </div>

    <!-- Summary Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
        <!-- Total Projects -->
        <div class="card-elevated rounded-apple-lg p-4">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-xs font-medium" style="color: rgba(235, 235, 245, 0.6);">Total Proyek</div>
                    <div class="text-2xl font-bold mt-1" style="color: #FFFFFF;">{{ $totalProjects }}</div>
                </div>
                <div class="w-12 h-12 rounded-full flex items-center justify-center" style="background-color: rgba(0, 122, 255, 0.15);">
                    <i class="fas fa-project-diagram text-xl" style="color: rgba(0, 122, 255, 1);"></i>
                </div>
            </div>
        </div>

        <!-- In Progress -->
        <div class="card-elevated rounded-apple-lg p-4">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-xs font-medium" style="color: rgba(235, 235, 245, 0.6);">Sedang Berjalan</div>
                    <div class="text-2xl font-bold mt-1" style="color: #FFFFFF;">{{ $inProgressProjects }}</div>
                </div>
                <div class="w-12 h-12 rounded-full flex items-center justify-center" style="background-color: rgba(255, 159, 10, 0.15);">
                    <i class="fas fa-spinner text-xl" style="color: rgba(255, 159, 10, 1);"></i>
                </div>
            </div>
        </div>

        <!-- Completed -->
        <div class="card-elevated rounded-apple-lg p-4">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-xs font-medium" style="color: rgba(235, 235, 245, 0.6);">Selesai</div>
                    <div class="text-2xl font-bold mt-1" style="color: #FFFFFF;">{{ $completedProjects }}</div>
                </div>
                <div class="w-12 h-12 rounded-full flex items-center justify-center" style="background-color: rgba(52, 199, 89, 0.15);">
                    <i class="fas fa-check-circle text-xl" style="color: rgba(52, 199, 89, 1);"></i>
                </div>
            </div>
        </div>

        <!-- Overdue -->
        <div class="card-elevated rounded-apple-lg p-4">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-xs font-medium" style="color: rgba(235, 235, 245, 0.6);">Terlambat</div>
                    <div class="text-2xl font-bold mt-1" style="color: #FFFFFF;">{{ $overdueProjects }}</div>
                </div>
                <div class="w-12 h-12 rounded-full flex items-center justify-center" style="background-color: rgba(255, 59, 48, 0.15);">
                    <i class="fas fa-exclamation-triangle text-xl" style="color: rgba(255, 59, 48, 1);"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Search and Filter Card -->
    <div class="card-elevated rounded-apple-lg mb-4 overflow-hidden">
        <div class="px-4 py-3" style="border-bottom: 1px solid rgba(84, 84, 88, 0.65);">
            <h3 class="text-base font-semibold" style="color: #FFFFFF;">Pencarian & Filter</h3>
        </div>
        <div class="p-4">
            <form method="GET" action="{{ route('projects.index') }}" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3">
                    <div>
                        <label class="block text-xs font-medium mb-1.5" style="color: rgba(235, 235, 245, 0.6);">Pencarian</label>
                        <div class="relative">
                            <input type="text" 
                                   name="search" 
                                   value="{{ request('search') }}" 
                                   placeholder="Nama proyek atau klien..." 
                                   class="input-dark w-full pl-9 pr-3 py-2 rounded-apple text-sm">
                            <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-sm" style="color: rgba(235, 235, 245, 0.3);"></i>
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-xs font-medium mb-1.5" style="color: rgba(235, 235, 245, 0.6);">Status</label>
                        <select name="status" class="input-dark w-full px-3 py-2 rounded-apple text-sm">
                            <option value="">Semua Status</option>
                            @foreach($statuses as $status)
                                <option value="{{ $status->id }}" {{ request('status') == $status->id ? 'selected' : '' }}>
                                    {{ $status->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-xs font-medium mb-1.5" style="color: rgba(235, 235, 245, 0.6);">Klien</label>
                        <select name="client" class="input-dark w-full px-3 py-2 rounded-apple text-sm">
                            <option value="">Semua Klien</option>
                            @foreach($clients as $client)
                                <option value="{{ $client->id }}" {{ request('client') == $client->id ? 'selected' : '' }}>
                                    {{ $client->company_name ?? $client->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-xs font-medium mb-1.5" style="color: rgba(235, 235, 245, 0.6);">Tahun</label>
                        <select name="year" class="input-dark w-full px-3 py-2 rounded-apple text-sm">
                            <option value="">Semua Tahun</option>
                            @for($year = date('Y'); $year >= 2020; $year--)
                                <option value="{{ $year }}" {{ request('year') == $year ? 'selected' : '' }}>
                                    {{ $year }}
                                </option>
                            @endfor
                        </select>
                    </div>
                </div>
                <script>
                // Auto submit form on filter change
                document.addEventListener('DOMContentLoaded', function() {
                    const form = document.querySelector('form[action="{{ route('projects.index') }}"]');
                    if (!form) return;
                    
                    const searchInput = form.querySelector('input[name="search"]');
                    
                    // Auto-submit for select dropdowns
                    form.querySelectorAll('select[name]').forEach(function(el) {
                        el.addEventListener('change', function() {
                            form.submit();
                        });
                    });
                    
                    // Submit search on Enter key only
                    if (searchInput) {
                        searchInput.addEventListener('keydown', function(e) {
                            if (e.key === 'Enter') {
                                e.preventDefault();
                                e.stopPropagation();
                                form.submit();
                            }
                        });
                    }
                });
                </script>
            </form>
        </div>
    </div>

    <!-- Projects Table Card -->
    <div class="card-elevated rounded-apple-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-700">
                <thead style="background-color: var(--dark-bg-secondary);">
                    <tr>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-dark-text-secondary">Proyek</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-dark-text-secondary">Klien</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-dark-text-secondary">Status</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-dark-text-secondary">Deadline</th>
                        <th scope="col" class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wider text-dark-text-secondary">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-700" style="background-color: var(--dark-bg-secondary);">
                    @forelse($projects as $project)
                        <tr class="hover-lift transition-apple" style="cursor: pointer;" onclick="window.location='{{ route('projects.show', $project) }}'">
                            <td class="px-4 py-3">
                                <div class="font-semibold text-sm text-dark-text-primary">{{ $project->name }}</div>
                                @if($project->description)
                                    <div class="text-xs text-dark-text-secondary mt-1 line-clamp-1">
                                        {{ Str::limit($project->description, 80) }}
                                    </div>
                                @endif
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
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
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs font-medium rounded-apple"
                                      style="background-color: {{ $project->status->color ?? '#6B7280' }}20; color: {{ $project->status->color ?? '#6B7280' }}">
                                    {{ $project->status->name ?? 'N/A' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                @if($project->deadline)
                                    <div class="text-sm" style="color: {{ $project->deadline->isPast() ? '#FF453A' : '#FFFFFF' }};">
                                        {{ $project->deadline->format('d M Y') }}
                                    </div>
                                @else
                                    <span class="text-sm text-dark-text-secondary">-</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-center" onclick="event.stopPropagation()">
                                <div class="flex items-center justify-center space-x-2">
                                    <a href="{{ route('projects.show', $project) }}" 
                                       class="inline-flex items-center px-3 py-1.5 rounded-apple text-xs font-medium transition-apple" 
                                       style="background-color: rgba(90, 200, 250, 0.15); color: var(--apple-teal); border: 1px solid rgba(90, 200, 250, 0.3);"
                                       onmouseover="this.style.backgroundColor='rgba(90, 200, 250, 0.25)'" 
                                       onmouseout="this.style.backgroundColor='rgba(90, 200, 250, 0.15)'">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('projects.edit', $project) }}" 
                                       class="inline-flex items-center px-3 py-1.5 rounded-apple text-xs font-medium transition-apple" 
                                       style="background-color: rgba(255, 149, 0, 0.15); color: var(--apple-orange); border: 1px solid rgba(255, 149, 0, 0.3);"
                                       onmouseover="this.style.backgroundColor='rgba(255, 149, 0, 0.25)'" 
                                       onmouseout="this.style.backgroundColor='rgba(255, 149, 0, 0.15)'">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button onclick="event.stopPropagation(); deleteProject({{ $project->id }})" 
                                            class="inline-flex items-center px-3 py-1.5 rounded-apple text-xs font-medium transition-apple" 
                                            style="background-color: rgba(255, 59, 48, 0.15); color: var(--apple-red); border: 1px solid rgba(255, 59, 48, 0.3);"
                                            onmouseover="this.style.backgroundColor='rgba(255, 59, 48, 0.25)'" 
                                            onmouseout="this.style.backgroundColor='rgba(255, 59, 48, 0.15)'">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-16 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <i class="fas fa-project-diagram text-6xl mb-6" style="color: rgba(235, 235, 245, 0.3);"></i>
                                    <h3 class="text-xl font-semibold mb-2" style="color: #FFFFFF;">Belum Ada Proyek</h3>
                                    <p class="mb-6" style="color: rgba(235, 235, 245, 0.6);">Mulai dengan membuat proyek pertama Anda</p>
                                    <a href="{{ route('projects.create') }}" 
                                       class="btn-primary inline-flex items-center px-6 py-3 rounded-apple font-medium">
                                        <i class="fas fa-plus mr-2"></i>
                                        Tambah Proyek Pertama
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