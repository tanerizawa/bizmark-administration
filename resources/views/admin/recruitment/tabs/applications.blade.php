@php
    $statusLabels = [
        'pending' => 'Pending',
        'reviewed' => 'Direview',
        'interview' => 'Interview',
        'accepted' => 'Diterima',
        'rejected' => 'Ditolak',
    ];

    $statusColors = [
        'pending' => ['bg' => 'rgba(255,214,10,0.15)', 'text' => 'rgba(255,214,10,1)'],
        'reviewed' => ['bg' => 'rgba(10,132,255,0.15)', 'text' => 'rgba(10,132,255,1)'],
        'interview' => ['bg' => 'rgba(175,82,222,0.15)', 'text' => 'rgba(175,82,222,1)'],
        'accepted' => ['bg' => 'rgba(48,209,88,0.15)', 'text' => 'rgba(48,209,88,1)'],
        'rejected' => ['bg' => 'rgba(255,69,58,0.15)', 'text' => 'rgba(255,69,58,1)'],
    ];
@endphp

{{-- Stats --}}
<section class="grid grid-cols-1 sm:grid-cols-4 gap-4 mb-5">
    <div class="card-elevated rounded-apple-lg p-4 space-y-1">
        <p class="text-xs uppercase tracking-widest" style="color: rgba(255,214,10,0.9);">Pending</p>
        <p class="text-3xl font-bold text-white">{{ number_format($pendingCount ?? 0) }}</p>
        <p class="text-xs" style="color: rgba(235,235,245,0.6);">Perlu peninjauan</p>
    </div>
    <div class="card-elevated rounded-apple-lg p-4 space-y-1">
        <p class="text-xs uppercase tracking-widest" style="color: rgba(175,82,222,0.9);">Interview</p>
        <p class="text-3xl font-bold text-white">{{ number_format($interviewCount ?? 0) }}</p>
        <p class="text-xs" style="color: rgba(235,235,245,0.6);">Proses wawancara</p>
    </div>
    <div class="card-elevated rounded-apple-lg p-4 space-y-1">
        <p class="text-xs uppercase tracking-widest" style="color: rgba(48,209,88,0.9);">Diterima</p>
        <p class="text-3xl font-bold text-white">{{ number_format($offeredCount ?? 0) }}</p>
        <p class="text-xs" style="color: rgba(235,235,245,0.6);">Kandidat sukses</p>
    </div>
    <div class="card-elevated rounded-apple-lg p-4 space-y-1">
        <p class="text-xs uppercase tracking-widest" style="color: rgba(255,69,58,0.9);">Ditolak</p>
        <p class="text-3xl font-bold text-white">{{ number_format($applications->where('status', 'rejected')->count()) }}</p>
        <p class="text-xs" style="color: rgba(235,235,245,0.6);">Tidak sesuai kriteria</p>
    </div>
</section>

{{-- Status pills --}}
<section class="card-elevated rounded-apple-xl p-4 mb-5">
    <div class="flex flex-wrap gap-2">
        <a href="{{ route('admin.recruitment.index', ['tab' => 'applications']) }}"
           class="status-pill {{ !request('status') ? 'active' : '' }}">
            Semua <span>{{ $applications->total() }}</span>
        </a>
        @foreach($statusLabels as $status => $label)
            <a href="{{ route('admin.recruitment.index', ['tab' => 'applications', 'status' => $status]) }}"
               class="status-pill {{ request('status') === $status ? 'active' : '' }}">
                {{ $label }} <span>{{ $applications->where('status', $status)->count() }}</span>
            </a>
        @endforeach
    </div>
</section>

{{-- Filters --}}
<section class="card-elevated rounded-apple-xl p-5 md:p-6 space-y-4 mb-5">
    <div class="flex items-center justify-between flex-wrap gap-3">
        <div>
            <p class="text-xs uppercase tracking-[0.35em]" style="color: rgba(235,235,245,0.5);">Filter</p>
            <h2 class="text-lg font-semibold text-white">Temukan Kandidat</h2>
        </div>
        <p class="text-xs" style="color: rgba(235,235,245,0.6);">{{ $applications->total() }} lamaran ditemukan</p>
    </div>
    <form method="GET" action="{{ route('admin.recruitment.index') }}" class="flex flex-col gap-3 md:flex-row md:items-end">
        <input type="hidden" name="tab" value="applications">
        <div class="flex-1">
            <label class="text-xs uppercase tracking-widest mb-2 block" style="color: rgba(235,235,245,0.6);">Pencarian</label>
            <div class="flex">
                <span class="inline-flex items-center px-3 rounded-l-apple" style="background: rgba(28,28,30,0.6); border: 1px solid rgba(84,84,88,0.35); border-right: none; color: rgba(235,235,245,0.6);">
                    <i class="fas fa-search"></i>
                </span>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Nama atau email"
                       class="w-full px-4 py-2.5 rounded-r-apple text-sm text-white placeholder-gray-500"
                       style="background: rgba(28,28,30,0.6); border: 1px solid rgba(84,84,88,0.35); border-left: none;">
            </div>
        </div>
        <div class="flex-1">
            <label class="text-xs uppercase tracking-widest mb-2 block" style="color: rgba(235,235,245,0.6);">Lowongan</label>
            <select name="job_id" class="w-full px-4 py-2.5 rounded-apple text-sm text-white"
                    style="background: rgba(28,28,30,0.6); border: 1px solid rgba(84,84,88,0.35);">
                <option value="">Semua Lowongan</option>
                @foreach($jobsForFilter ?? [] as $job)
                    <option value="{{ $job->id }}" {{ request('job_id') == $job->id ? 'selected' : '' }}>
                        {{ $job->title }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="flex gap-3">
            <button type="submit" class="btn-primary-sm">
                <i class="fas fa-search mr-2"></i>Terapkan
            </button>
            <a href="{{ route('admin.recruitment.index', ['tab' => 'applications']) }}" class="btn-secondary-sm text-center">
                Reset
            </a>
        </div>
    </form>
</section>

{{-- Applications table --}}
<section class="card-elevated rounded-apple-xl overflow-hidden">
    @if($applications->count() > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead style="background: rgba(28,28,30,0.45);">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs uppercase tracking-widest" style="color: rgba(235,235,245,0.6);">Kandidat</th>
                        <th class="px-6 py-4 text-left text-xs uppercase tracking-widest" style="color: rgba(235,235,245,0.6);">Lowongan</th>
                        <th class="px-6 py-4 text-left text-xs uppercase tracking-widest" style="color: rgba(235,235,245,0.6);">Pendidikan</th>
                        <th class="px-6 py-4 text-left text-xs uppercase tracking-widest" style="color: rgba(235,235,245,0.6);">Status</th>
                        <th class="px-6 py-4 text-left text-xs uppercase tracking-widest" style="color: rgba(235,235,245,0.6);">Tanggal Lamar</th>
                        <th class="px-6 py-4 text-right text-xs uppercase tracking-widest" style="color: rgba(235,235,245,0.6);">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($applications as $application)
                        @php $color = $statusColors[$application->status] ?? $statusColors['pending']; @endphp
                        <tr class="border-b border-white/5 hover:bg-white/5 transition">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <span class="w-10 h-10 rounded-full flex items-center justify-center text-sm font-semibold" style="background: rgba(255,255,255,0.1); color:#FFFFFF;">
                                        {{ strtoupper(substr($application->full_name, 0, 1)) }}
                                    </span>
                                    <div>
                                        <p class="text-sm font-semibold text-white">{{ $application->full_name }}</p>
                                        <p class="text-xs" style="color: rgba(235,235,245,0.6);"><i class="fas fa-envelope mr-2"></i>{{ $application->email }}</p>
                                        <p class="text-xs" style="color: rgba(235,235,245,0.6);"><i class="fas fa-phone mr-2"></i>{{ $application->phone }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-sm font-semibold text-white">{{ $application->jobVacancy->title ?? '-' }}</p>
                                @if($application->has_experience_ukl_upl)
                                    <span class="inline-flex px-2 py-0.5 text-[10px] rounded-apple" style="background: rgba(52,199,89,0.18); color: rgba(52,199,89,1);">
                                        UKL-UPL Exp
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-sm" style="color:#FFFFFF;">{{ $application->education_level }} {{ $application->major }}</p>
                                <p class="text-xs" style="color: rgba(235,235,245,0.55);">{{ $application->institution }}</p>
                                @if($application->gpa)
                                    <p class="text-xs" style="color: rgba(235,235,245,0.55);">IPK: {{ number_format($application->gpa, 2) }}</p>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex px-3 py-1 text-xs font-semibold rounded-apple" style="background: {{ $color['bg'] }}; color: {{ $color['text'] }};">
                                    {{ $statusLabels[$application->status] ?? ucfirst($application->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-sm" style="color: rgba(235,235,245,0.85);">{{ $application->created_at->format('d M Y') }}</p>
                                <p class="text-xs" style="color: rgba(235,235,245,0.55);">{{ $application->created_at->diffForHumans() }}</p>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.applications.show', $application->id) }}" class="btn-secondary-sm">
                                        <i class="fas fa-eye mr-1"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @if($applications->hasPages())
            <div class="px-6 py-4 border-t border-white/5">
                {{ $applications->links() }}
            </div>
        @endif
    @else
        <div class="text-center py-12 space-y-4">
            <i class="fas fa-user-tie text-5xl" style="color: rgba(235,235,245,0.3);"></i>
            <p class="text-sm" style="color: rgba(235,235,245,0.65);">Belum ada lamaran yang sesuai filter Anda.</p>
        </div>
    @endif
</section>
