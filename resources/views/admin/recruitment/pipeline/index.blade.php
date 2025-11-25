@extends('layouts.app')

@section('title', 'Recruitment Pipeline')

@section('content')
<div class="recruitment-shell max-w-7xl mx-auto space-y-5">
    @php
        $pendingBadge = $stats['screening'] + $stats['testing'] + $stats['interview'] + $stats['offer'];
    @endphp

    {{-- Header --}}
    <section class="card-elevated rounded-apple-xl p-5 md:p-6 relative overflow-hidden">
        <div class="absolute inset-0 pointer-events-none" aria-hidden="true">
            <div class="w-72 h-72 bg-apple-blue opacity-25 blur-3xl rounded-full absolute -top-14 -right-10"></div>
            <div class="w-48 h-48 bg-apple-green opacity-20 blur-2xl rounded-full absolute bottom-0 left-8"></div>
        </div>
        <div class="relative flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div class="space-y-2.5">
                <div class="flex items-center gap-2 text-xs uppercase tracking-[0.35em]" style="color: rgba(235,235,245,0.6);">
                    <a href="{{ route('admin.recruitment.index') }}" class="inline-flex items-center gap-2 hover:text-white transition-apple">
                        <i class="fas fa-arrow-left text-xs"></i> Rekrutmen
                    </a>
                    <span class="text-dark-text-tertiary">/</span>
                    <span>Pipeline</span>
                </div>
                <h1 class="text-2xl font-semibold text-white leading-tight">Rekrutmen Pipeline</h1>
                <p class="text-sm" style="color: rgba(235,235,245,0.7);">
                    Pantau pergerakan kandidat di setiap tahap proses.
                </p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('admin.jobs.index') }}" class="btn-secondary-sm">
                    <i class="fas fa-briefcase mr-2"></i>Lowongan
                </a>
                @if($pendingBadge > 0)
                    <span class="px-3 py-1 rounded-full text-xs font-semibold" style="background: rgba(0,122,255,0.15); color: rgba(0,122,255,1);">
                        {{ $pendingBadge }} kandidat aktif
                    </span>
                @endif
            </div>
        </div>
    </section>

    {{-- Statistics --}}
    <section class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
        <div class="card-elevated rounded-apple-lg p-3.5 space-y-1">
            <p class="text-xs uppercase tracking-widest" style="color: rgba(10,132,255,0.9);">Total Aktif</p>
            <p class="text-2xl font-bold text-white">{{ $stats['total_in_pipeline'] }}</p>
            <p class="text-xs" style="color: rgba(235,235,245,0.6);">Dalam pipeline</p>
        </div>
        <div class="card-elevated rounded-apple-lg p-3.5 space-y-1">
            <p class="text-xs uppercase tracking-widest" style="color: rgba(10,132,255,0.9);">Screening</p>
            <p class="text-2xl font-bold text-white">{{ $stats['screening'] }}</p>
            <p class="text-xs" style="color: rgba(235,235,245,0.6);">Tahap awal</p>
        </div>
        <div class="card-elevated rounded-apple-lg p-3.5 space-y-1">
            <p class="text-xs uppercase tracking-widest" style="color: rgba(255,149,0,0.9);">Testing</p>
            <p class="text-2xl font-bold text-white">{{ $stats['testing'] }}</p>
            <p class="text-xs" style="color: rgba(235,235,245,0.6);">Tes berlangsung</p>
        </div>
        <div class="card-elevated rounded-apple-lg p-3.5 space-y-1">
            <p class="text-xs uppercase tracking-widest" style="color: rgba(191,90,242,0.9);">Interview</p>
            <p class="text-2xl font-bold text-white">{{ $stats['interview'] }}</p>
            <p class="text-xs" style="color: rgba(235,235,245,0.6);">Jadwal aktif</p>
        </div>
        <div class="card-elevated rounded-apple-lg p-3.5 space-y-1">
            <p class="text-xs uppercase tracking-widest" style="color: rgba(52,199,89,0.9);">Offer</p>
            <p class="text-2xl font-bold text-white">{{ $stats['offer'] }}</p>
            <p class="text-xs" style="color: rgba(235,235,245,0.6);">Tahap penawaran</p>
        </div>
        <div class="card-elevated rounded-apple-lg p-3.5 space-y-1">
            <p class="text-xs uppercase tracking-widest" style="color: rgba(52,199,89,0.9);">Minggu Ini</p>
            <p class="text-2xl font-bold text-white">{{ $stats['completed_this_week'] }}</p>
            <p class="text-xs" style="color: rgba(235,235,245,0.6);">Selesai minggu ini</p>
        </div>
    </section>

    {{-- Filters --}}
    <section class="card-elevated rounded-apple-xl p-4 space-y-3">
        <div>
            <p class="text-xs uppercase tracking-[0.3em]" style="color: rgba(235,235,245,0.5);">Filter</p>
            <h3 class="text-base font-semibold text-white">Sesuaikan Pipeline</h3>
        </div>
        <form method="GET" action="{{ route('admin.recruitment.pipeline.index') }}" class="grid grid-cols-1 md:grid-cols-3 gap-3">
            <div class="space-y-1">
                <label class="text-xs uppercase tracking-widest" style="color: rgba(235,235,245,0.55);">Lowongan</label>
                <select name="vacancy_id" class="w-full">
                    <option value="">Semua Lowongan</option>
                    @foreach(\App\Models\JobVacancy::where('status', 'open')->get() as $vacancy)
                        <option value="{{ $vacancy->id }}" {{ request('vacancy_id') == $vacancy->id ? 'selected' : '' }}>
                            {{ $vacancy->title }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="space-y-1">
                <label class="text-xs uppercase tracking-widest" style="color: rgba(235,235,245,0.55);">Tahap</label>
                <select name="stage" class="w-full">
                    <option value="">Semua Tahap</option>
                    <option value="screening" {{ request('stage') == 'screening' ? 'selected' : '' }}>Screening</option>
                    <option value="testing" {{ request('stage') == 'testing' ? 'selected' : '' }}>Testing</option>
                    <option value="interview" {{ request('stage') == 'interview' ? 'selected' : '' }}>Interview</option>
                    <option value="offer" {{ request('stage') == 'offer' ? 'selected' : '' }}>Offer</option>
                </select>
            </div>

            <div class="flex items-end gap-2">
                <button type="submit" class="btn-primary-sm flex-1">
                    <i class="fas fa-filter mr-2"></i>Terapkan
                </button>
                <a href="{{ route('admin.recruitment.pipeline.index') }}" class="btn-secondary-sm">
                    <i class="fas fa-undo mr-2"></i>Reset
                </a>
            </div>
        </form>
    </section>

    {{-- Pipeline Table --}}
    <section class="card-elevated rounded-apple-xl overflow-hidden">
        <div class="flex items-center justify-between px-4 py-3 border-b" style="border-color: rgba(58,58,60,0.6);">
            <div>
                <p class="text-xs uppercase tracking-[0.25em]" style="color: rgba(235,235,245,0.55);">Kandidat</p>
                <h3 class="text-base font-semibold text-white">Pipeline Kandidat</h3>
            </div>
        </div>
        <div class="overflow-x-auto">
            @if($applications->count() > 0)
                <table class="min-w-full text-sm">
                    <thead class="bg-dark-bg-secondary border-b" style="border-color: rgba(58,58,60,0.6); color: rgba(235,235,245,0.55);">
                        <tr class="text-left text-xs uppercase tracking-wider">
                            <th class="px-4 py-2">Kandidat</th>
                            <th class="px-4 py-2">Posisi</th>
                            <th class="px-4 py-2">Dilamar</th>
                            <th class="px-4 py-2">Tahap</th>
                            <th class="px-4 py-2">Progress</th>
                            <th class="px-4 py-2">Status</th>
                            <th class="px-4 py-2 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y" style="border-color: rgba(58,58,60,0.6); color: rgba(235,235,245,0.9);">
                        @foreach($applications as $application)
                            @php
                                $currentStage = $application->recruitmentStages->where('status', 'in-progress')->first();
                                $totalStages = $application->recruitmentStages->count();
                                $completedStages = $application->recruitmentStages->where('status', 'passed')->count();
                                $progressPercent = $totalStages > 0 ? round(($completedStages / $totalStages) * 100) : 0;
                            @endphp
                            <tr class="hover:bg-white/5 transition-colors">
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-2">
                                        <div class="w-9 h-9 rounded-full bg-apple-blue bg-opacity-15 text-apple-blue flex items-center justify-center font-semibold">
                                            {{ strtoupper(substr($application->full_name, 0, 2)) }}
                                        </div>
                                        <div class="min-w-0">
                                            <div class="font-semibold">{{ $application->full_name }}</div>
                                            <p class="text-xs" style="color: rgba(235,235,245,0.65);">{{ $application->email }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="font-medium">{{ $application->jobVacancy?->title ?? 'Position Deleted' }}</div>
                                    <p class="text-xs" style="color: rgba(235,235,245,0.65);">{{ $application->jobVacancy?->location ?? '-' }}</p>
                                </td>
                                <td class="px-4 py-3">
                                    <p class="text-xs" style="color: rgba(235,235,245,0.75);">{{ $application->created_at->format('d M Y') }}</p>
                                    <p class="text-[11px]" style="color: rgba(235,235,245,0.55);">{{ $application->created_at->diffForHumans() }}</p>
                                </td>
                                <td class="px-4 py-3">
                                    @if($currentStage)
                                        @php
                                            $stageColor = match($currentStage->stage_name) {
                                                'screening' => 'rgba(10,132,255,0.2)',
                                                'testing' => 'rgba(255,149,0,0.2)',
                                                'interview' => 'rgba(191,90,242,0.2)',
                                                'offer' => 'rgba(52,199,89,0.2)',
                                                default => 'rgba(142,142,147,0.25)'
                                            };
                                        @endphp
                                        <span class="px-2 py-1 rounded-full text-xs font-semibold" style="background: {{ $stageColor }};">
                                            {{ ucfirst($currentStage->stage_name) }}
                                        </span>
                                    @else
                                        <span class="px-2 py-1 rounded-full text-xs font-semibold" style="background: rgba(142,142,147,0.25);">Not Started</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-2">
                                        <div class="flex-1 h-1.5 rounded-full bg-white/10">
                                            <div class="h-full rounded-full bg-apple-blue" style="width: {{ $progressPercent }}%"></div>
                                        </div>
                                        <span class="text-xs" style="color: rgba(235,235,245,0.7);">{{ $progressPercent }}%</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    @php
                                        $statusColor = $application->status == 'hired' ? 'rgba(52,199,89,0.2)' : ($application->status == 'rejected' ? 'rgba(255,59,48,0.2)' : 'rgba(10,132,255,0.2)');
                                    @endphp
                                    <span class="px-2 py-1 rounded-full text-xs font-semibold" style="background: {{ $statusColor }};">
                                        {{ ucfirst($application->status) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <div class="flex justify-end gap-2">
                                        <a href="{{ route('admin.recruitment.pipeline.show', $application) }}" class="btn-secondary-sm">
                                            <i class="fas fa-stream mr-1"></i>Pipeline
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="p-8 text-center" style="color: rgba(235,235,245,0.6);">
                    <i class="fas fa-inbox text-4xl mb-2"></i>
                    <p class="text-sm">Belum ada kandidat di pipeline</p>
                    <a href="{{ route('admin.jobs.index') }}" class="btn-primary-sm mt-3">
                        <i class="fas fa-briefcase mr-2"></i>Lihat Lowongan
                    </a>
                </div>
            @endif
        </div>
    </section>
</div>
@endsection
