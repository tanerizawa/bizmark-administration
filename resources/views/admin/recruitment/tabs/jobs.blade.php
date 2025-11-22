@php
    $statusMeta = [
        'open' => ['label' => 'Aktif', 'bg' => 'rgba(52,199,89,0.15)', 'text' => 'rgba(52,199,89,1)'],
        'draft' => ['label' => 'Draft', 'bg' => 'rgba(255,214,10,0.15)', 'text' => 'rgba(255,214,10,1)'],
        'closed' => ['label' => 'Ditutup', 'bg' => 'rgba(255,69,58,0.15)', 'text' => 'rgba(255,69,58,1)'],
    ];
    
    $employmentOptions = $jobs->pluck('employment_type')->filter()->unique()->values();
    $locationOptions = $jobs->pluck('location')->filter()->unique()->values();
@endphp

{{-- Stats --}}
<section class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-5">
    <div class="card-elevated rounded-apple-lg p-4">
        <p class="text-xs uppercase tracking-widest" style="color: rgba(52,199,89,0.9);">Lowongan Aktif</p>
        <p class="text-3xl font-bold text-white">{{ number_format($activeCount ?? 0) }}</p>
        <p class="text-xs" style="color: rgba(235,235,245,0.6);">Sedang tayang untuk publik</p>
    </div>
    <div class="card-elevated rounded-apple-lg p-4">
        <p class="text-xs uppercase tracking-widest" style="color: rgba(255,214,10,0.9);">Draft</p>
        <p class="text-3xl font-bold text-white">{{ number_format($draftCount ?? 0) }}</p>
        <p class="text-xs" style="color: rgba(235,235,245,0.6);">Belum dipublikasikan</p>
    </div>
    <div class="card-elevated rounded-apple-lg p-4">
        <p class="text-xs uppercase tracking-widest" style="color: rgba(255,69,58,0.9);">Ditutup</p>
        <p class="text-3xl font-bold text-white">{{ number_format($closedCount ?? 0) }}</p>
        <p class="text-xs" style="color: rgba(235,235,245,0.6);">Lowongan selesai</p>
    </div>
</section>

{{-- Filters --}}
<section class="card-elevated rounded-apple-xl p-5 md:p-6 space-y-4 mb-5">
    <div class="flex items-center justify-between flex-wrap gap-3">
        <div>
            <p class="text-xs uppercase tracking-[0.35em]" style="color: rgba(235,235,245,0.5);">Filter</p>
            <h2 class="text-lg font-semibold text-white">Susun Daftar Lowongan</h2>
        </div>
        <p class="text-xs" style="color: rgba(235,235,245,0.6);">{{ $jobs->total() }} hasil ditemukan</p>
    </div>
    <form method="GET" action="{{ route('admin.recruitment.index') }}">
        <input type="hidden" name="tab" value="jobs">
        <div class="flex flex-col gap-3 md:flex-row md:items-end">
            <div class="flex-1">
                <label class="text-xs uppercase tracking-widest mb-2 block" style="color: rgba(235,235,245,0.6);">Pencarian</label>
                <div class="flex">
                    <span class="inline-flex items-center px-3 rounded-l-apple" style="background: rgba(28,28,30,0.6); border: 1px solid rgba(84,84,88,0.35); border-right: none; color: rgba(235,235,245,0.6);">
                        <i class="fas fa-search"></i>
                    </span>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Judul, posisi, lokasi"
                           class="w-full px-4 py-2.5 rounded-r-apple text-sm text-white placeholder-gray-500"
                           style="background: rgba(28,28,30,0.6); border: 1px solid rgba(84,84,88,0.35); border-left: none;">
                </div>
            </div>
            <div class="flex-1">
                <label class="text-xs uppercase tracking-widest mb-2 block" style="color: rgba(235,235,245,0.6);">Status</label>
                <select name="status" class="w-full px-4 py-2.5 rounded-apple text-sm text-white"
                        style="background: rgba(28,28,30,0.6); border: 1px solid rgba(84,84,88,0.35);">
                    <option value="">Semua Status</option>
                    @foreach($jobStatuses ?? [] as $status)
                        <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>
                            {{ ucfirst($statusMeta[$status]['label'] ?? $status) }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="flex-1">
                <label class="text-xs uppercase tracking-widest mb-2 block" style="color: rgba(235,235,245,0.6);">Tipe Kerja</label>
                <select name="employment_type" class="w-full px-4 py-2.5 rounded-apple text-sm text-white"
                        style="background: rgba(28,28,30,0.6); border: 1px solid rgba(84,84,88,0.35);">
                    <option value="">Semua Tipe</option>
                    @foreach($employmentTypes ?? [] as $type)
                        <option value="{{ $type }}" {{ request('employment_type') == $type ? 'selected' : '' }}>
                            {{ ucfirst(str_replace('-', ' ', $type)) }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="flex gap-3">
                <button type="submit" class="btn-primary-sm">
                    <i class="fas fa-search mr-2"></i>Terapkan
                </button>
                <a href="{{ route('admin.recruitment.index', ['tab' => 'jobs']) }}" class="btn-secondary-sm text-center">
                    Reset
                </a>
            </div>
        </div>
    </form>
</section>

{{-- Job list --}}
<section class="card-elevated rounded-apple-xl overflow-hidden">
    @if($jobs->count() > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead style="background: rgba(28,28,30,0.45);">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs uppercase tracking-widest" style="color: rgba(235,235,245,0.6);">Posisi</th>
                        <th class="px-6 py-4 text-left text-xs uppercase tracking-widest" style="color: rgba(235,235,245,0.6);">Tipe</th>
                        <th class="px-6 py-4 text-left text-xs uppercase tracking-widest" style="color: rgba(235,235,245,0.6);">Lokasi</th>
                        <th class="px-6 py-4 text-left text-xs uppercase tracking-widest" style="color: rgba(235,235,245,0.6);">Status</th>
                        <th class="px-6 py-4 text-left text-xs uppercase tracking-widest" style="color: rgba(235,235,245,0.6);">Deadline</th>
                        <th class="px-6 py-4 text-left text-xs uppercase tracking-widest" style="color: rgba(235,235,245,0.6);">Pelamar</th>
                        <th class="px-6 py-4 text-left text-xs uppercase tracking-widest" style="color: rgba(235,235,245,0.6);">Dibuat</th>
                        <th class="px-6 py-4 text-right text-xs uppercase tracking-widest" style="color: rgba(235,235,245,0.6);">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($jobs as $job)
                        @php
                            $meta = $statusMeta[$job->status] ?? ['label' => ucfirst($job->status), 'bg' => 'rgba(255,255,255,0.15)', 'text' => '#FFFFFF'];
                        @endphp
                        <tr class="border-b border-white/5 hover:bg-white/5 transition">
                            <td class="px-6 py-4">
                                <p class="text-sm font-semibold text-white">{{ $job->title }}</p>
                                <p class="text-xs" style="color: rgba(235,235,245,0.6);">{{ $job->position ?? 'Posisi belum diisi' }}</p>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex px-3 py-1 text-xs rounded-apple" style="background: rgba(255,255,255,0.08); color: rgba(235,235,245,0.9);">
                                    {{ $job->employment_type ? ucfirst(str_replace('-', ' ', $job->employment_type)) : 'N/A' }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-sm" style="color:#FFFFFF;"><i class="fas fa-map-marker-alt mr-2" style="color: rgba(235,235,245,0.5);"></i>{{ $job->location ?? '-' }}</p>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex px-3 py-1 text-xs font-semibold rounded-apple" style="background: {{ $meta['bg'] }}; color: {{ $meta['text'] }};">
                                    {{ $meta['label'] }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-sm" style="color: rgba(235,235,245,0.85);">{{ $job->deadline ? \Carbon\Carbon::parse($job->deadline)->format('d M Y') : 'Tidak ditentukan' }}</p>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-sm font-semibold text-white">{{ $job->applications_count ?? 0 }}</p>
                                <p class="text-xs" style="color: rgba(235,235,245,0.55);">Pelamar</p>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-sm" style="color: rgba(235,235,245,0.85);">{{ $job->created_at->format('d M Y') }}</p>
                                <p class="text-xs" style="color: rgba(235,235,245,0.55);">{{ $job->created_at->diffForHumans() }}</p>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.jobs.show', $job->id) }}" class="btn-secondary-sm">
                                        <i class="fas fa-eye mr-1"></i>Detail
                                    </a>
                                    <a href="{{ route('admin.jobs.edit', $job->id) }}" class="btn-primary-sm">
                                        <i class="fas fa-edit mr-1"></i>Edit
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @if($jobs->hasPages())
            <div class="px-6 py-4 border-t border-white/5">
                {{ $jobs->links() }}
            </div>
        @endif
    @else
        <div class="text-center py-12 space-y-4">
            <i class="fas fa-briefcase text-5xl" style="color: rgba(235,235,245,0.3);"></i>
            <p class="text-sm" style="color: rgba(235,235,245,0.65);">Belum ada lowongan yang sesuai filter Anda.</p>
            <a href="{{ route('admin.jobs.create') }}" class="btn-primary">
                <i class="fas fa-plus mr-2"></i>Tambah Lowongan Pertama
            </a>
        </div>
    @endif
</section>
