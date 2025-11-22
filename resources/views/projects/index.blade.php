@extends('layouts.app')

@section('title', 'Proyek')
@section('page-title', 'Manajemen Proyek')

@section('content')
    {{-- Hero Section --}}
    <section class="card-elevated rounded-apple-xl p-5 md:p-6 relative overflow-hidden mb-6">
        <div class="absolute inset-0 pointer-events-none" aria-hidden="true">
            <div class="w-72 h-72 bg-apple-blue opacity-30 blur-3xl rounded-full absolute -top-16 -right-10"></div>
            <div class="w-48 h-48 bg-apple-green opacity-20 blur-2xl rounded-full absolute bottom-0 left-10"></div>
        </div>
        <div class="relative space-y-5 md:space-y-6">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-5">
                <div class="space-y-2.5 max-w-3xl">
                    <p class="text-sm uppercase tracking-[0.4em]" style="color: rgba(235,235,245,0.5);">Manajemen Proyek</p>
                    <h1 class="text-2xl md:text-3xl font-bold" style="color: #FFFFFF;">
                        Pantau dan Kelola Semua Proyek Perizinan
                    </h1>
                    <p class="text-sm md:text-base" style="color: rgba(235,235,245,0.75);">
                        Kelola proyek perizinan dari awal hingga selesai dengan visibilitas penuh terhadap status, tenggat, dan kemajuan setiap proyek.
                    </p>
                </div>
                <div class="space-y-2.5">
                    <a href="{{ route('projects.create') }}" 
                       class="inline-flex items-center px-4 py-2 rounded-apple text-sm font-semibold" 
                       style="background: rgba(10,132,255,0.25); color: rgba(235,235,245,0.9);">
                        <i class="fas fa-plus mr-2"></i>
                        Tambah Proyek
                        <i class="fas fa-arrow-right ml-2 text-xs"></i>
                    </a>
                </div>
            </div>

            <!-- Summary Statistics -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3 md:gap-4">
                <!-- Total Projects -->
                <div class="rounded-apple-lg p-3.5 md:p-4" style="background: rgba(10,132,255,0.12);">
                    <p class="text-xs uppercase tracking-widest" style="color: rgba(10,132,255,0.9);">Total Proyek</p>
                    <h2 class="text-2xl font-bold mt-1.5" style="color: #FFFFFF;">{{ $totalProjects }}</h2>
                    <p class="text-xs" style="color: rgba(235,235,245,0.6);">Semua proyek</p>
                </div>

                <!-- In Progress -->
                <div class="rounded-apple-lg p-3.5 md:p-4" style="background: rgba(255,159,10,0.12);">
                    <p class="text-xs uppercase tracking-widest" style="color: rgba(255,159,10,0.9);">Sedang Berjalan</p>
                    <h2 class="text-2xl font-bold mt-1.5" style="color: rgba(255,159,10,1);">{{ $inProgressProjects }}</h2>
                    <p class="text-xs" style="color: rgba(235,235,245,0.6);">Aktif dikerjakan</p>
                </div>

                <!-- Completed -->
                <div class="rounded-apple-lg p-3.5 md:p-4" style="background: rgba(52,199,89,0.12);">
                    <p class="text-xs uppercase tracking-widest" style="color: rgba(52,199,89,0.9);">Selesai</p>
                    <h2 class="text-2xl font-bold mt-1.5" style="color: rgba(52,199,89,1);">{{ $completedProjects }}</h2>
                    <p class="text-xs" style="color: rgba(235,235,245,0.6);">Proyek tuntas</p>
                </div>

                <!-- Overdue -->
                <div class="rounded-apple-lg p-3.5 md:p-4" style="background: rgba(255,59,48,0.12);">
                    <p class="text-xs uppercase tracking-widest" style="color: rgba(255,59,48,0.9);">Terlambat</p>
                    <h2 class="text-2xl font-bold mt-1.5" style="color: rgba(255,59,48,1);">{{ $overdueProjects }}</h2>
                    <p class="text-xs" style="color: rgba(235,235,245,0.6);">Perlu perhatian</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Search and Filter Card -->
    <div class="card-elevated rounded-apple-lg mb-5 overflow-hidden">
        <div class="px-4 py-3" style="border-bottom: 1px solid rgba(84, 84, 88, 0.45);">
            <h3 class="text-sm font-semibold uppercase tracking-wide" style="color: rgba(235, 235, 245, 0.8);">Pencarian & Filter</h3>
        </div>
        <div class="p-4 md:p-5">
            <form method="GET" action="{{ route('projects.index') }}" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3">
                    <div>
                        <label class="block text-xs font-semibold mb-1.5" style="color: rgba(235, 235, 245, 0.65);">Pencarian</label>
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
                        <label class="block text-xs font-semibold mb-1.5" style="color: rgba(235, 235, 245, 0.65);">Status</label>
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
                        <label class="block text-xs font-semibold mb-1.5" style="color: rgba(235, 235, 245, 0.65);">Klien</label>
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
                        <label class="block text-xs font-semibold mb-1.5" style="color: rgba(235, 235, 245, 0.65);">Tahun</label>
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
                                    <div class="text-sm" style="color: {{ $project->deadline->isPast() ? '#FF453A' : '#FFFFFF' }};">
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
