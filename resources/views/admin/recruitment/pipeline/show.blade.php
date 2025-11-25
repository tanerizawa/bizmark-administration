@extends('layouts.app')

@section('title', 'Pipeline Detail - ' . $application->full_name)

@push('styles')
<style>
    .avatar-circle-xl {
        width: 88px;
        height: 88px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 36px;
        background: linear-gradient(135deg, rgba(10,132,255,0.2), rgba(191,90,242,0.2));
        color: #0A84FF;
        border: 2px solid rgba(10,132,255,0.3);
    }

    .timeline-item {
        position: relative;
    }

    .timeline-item:not(:last-child)::before {
        content: '';
        position: absolute;
        left: 20px;
        top: 50px;
        bottom: -20px;
        width: 2px;
        background: rgba(58,58,60,0.6);
    }

    .stage-icon {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
    }
</style>
@endpush

@section('content')
<div class="max-w-screen-2xl mx-auto px-4 py-6 space-y-6">
    {{-- Breadcrumb --}}
    @if($application->jobVacancy)
        <x-breadcrumb :items="[
            ['label' => 'Jobs', 'url' => route('admin.jobs.index')],
            ['label' => $application->jobVacancy->title, 'url' => route('admin.jobs.show', $application->jobVacancy->id)],
            ['label' => 'Pipeline', 'url' => route('admin.jobs.pipeline', $application->jobVacancy->id)],
            ['label' => $application->full_name]
        ]" />
    @else
        <x-breadcrumb :items="[
            ['label' => 'Pipeline', 'url' => route('admin.recruitment.pipeline.index')],
            ['label' => $application->full_name]
        ]" />
    @endif

    {{-- Header --}}
    <section class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <nav class="text-xs mb-2 flex items-center gap-2" style="color: rgba(235,235,245,0.6);">
                <a href="{{ route('admin.recruitment.index') }}" class="hover:text-apple-blue transition-colors">Rekrutmen</a>
                <i class="fas fa-chevron-right text-[10px]"></i>
                <a href="{{ route('admin.recruitment.pipeline.index') }}" class="hover:text-apple-blue transition-colors">Pipeline</a>
                <i class="fas fa-chevron-right text-[10px]"></i>
                <span style="color: rgba(235,235,245,0.9);">Detail Kandidat</span>
            </nav>
            <h1 class="text-2xl md:text-3xl font-bold text-white mb-1">{{ $application->full_name }}</h1>
            @if($application->jobVacancy)
                <p class="text-sm" style="color: rgba(235,235,245,0.65);">
                    <i class="fas fa-briefcase mr-2"></i>{{ $application->jobVacancy->title }}
                </p>
            @else
                <p class="text-sm text-amber-400">
                    <i class="fas fa-exclamation-triangle mr-2"></i>Job Vacancy data not found
                </p>
            @endif
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.recruitment.pipeline.index') }}" class="btn-secondary">
                <i class="fas fa-arrow-left mr-2"></i>Kembali ke Pipeline
            </a>
        </div>
    </section>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Left Column: Candidate Info --}}
        <div class="space-y-6">
            {{-- Candidate Card --}}
            <section class="card-elevated rounded-apple-xl p-6 text-center">
                <div class="avatar-circle-xl mx-auto mb-4">
                    {{ strtoupper(substr($application->full_name, 0, 2)) }}
                </div>
                <h3 class="text-xl font-bold text-white mb-1">{{ $application->full_name }}</h3>
                <p class="text-sm mb-3" style="color: rgba(235,235,245,0.65);">{{ $application->email }}</p>
                
                <div class="flex justify-center gap-2 mb-4">
                    @php
                        $statusColors = [
                            'hired' => 'rgba(52,199,89,0.2)',
                            'rejected' => 'rgba(255,59,48,0.2)',
                            'pending' => 'rgba(10,132,255,0.2)',
                        ];
                        $statusColor = $statusColors[$application->status] ?? 'rgba(142,142,147,0.25)';
                    @endphp
                    <span class="px-3 py-1.5 rounded-full text-xs font-semibold" style="background: {{ $statusColor }};">
                        {{ ucfirst($application->status) }}
                    </span>
                </div>

                <div class="border-t pt-4 mt-4" style="border-color: rgba(58,58,60,0.6);">
                    <div class="grid grid-cols-2 gap-4 text-center">
                        <div>
                            <p class="text-xs mb-1" style="color: rgba(235,235,245,0.55);">Tanggal Melamar</p>
                            <p class="font-semibold text-white">{{ $application->created_at->format('d M Y') }}</p>
                        </div>
                        <div>
                            <p class="text-xs mb-1" style="color: rgba(235,235,245,0.55);">Telepon</p>
                            <p class="font-semibold text-white">{{ $application->phone ?? '-' }}</p>
                        </div>
                    </div>
                </div>
            </section>

            {{-- Pipeline Progress --}}
            <section class="card-elevated rounded-apple-xl overflow-hidden">
                <div class="px-4 py-3 border-b" style="border-color: rgba(58,58,60,0.6);">
                    <p class="text-xs uppercase tracking-[0.25em]" style="color: rgba(235,235,245,0.55);">Progress</p>
                    <h3 class="text-base font-semibold text-white">Tahap Rekrutmen</h3>
                </div>
                <div class="p-4">
                    @if($application->recruitmentStages->count() > 0)
                        <div class="space-y-4">
                            @foreach($application->recruitmentStages->sortBy('stage_order') as $stage)
                                <div class="border rounded-apple-lg p-4" style="border-color: rgba(58,58,60,0.6); background: rgba(255,255,255,0.02);">
                                    <div class="flex items-start gap-3 mb-3">
                                        <div class="stage-icon flex-shrink-0
                                            @if($stage->status == 'passed')
                                                bg-apple-green bg-opacity-20 text-apple-green
                                            @elseif($stage->status == 'in-progress')
                                                bg-apple-blue bg-opacity-20 text-apple-blue
                                            @elseif($stage->status == 'failed')
                                                bg-apple-red bg-opacity-20 text-apple-red
                                            @else
                                                bg-white bg-opacity-10 text-gray-400
                                            @endif">
                                            <i class="fas 
                                                @if($stage->status == 'passed') fa-check
                                                @elseif($stage->status == 'in-progress') fa-play
                                                @elseif($stage->status == 'failed') fa-times
                                                @else fa-circle
                                                @endif"></i>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="font-semibold text-white mb-1">{{ ucfirst($stage->stage_name) }}</p>
                                            @if($stage->started_at || $stage->completed_at)
                                                <p class="text-xs mb-2" style="color: rgba(235,235,245,0.55);">
                                                    @if($stage->started_at)
                                                        Dimulai: {{ $stage->started_at->format('d M Y') }}
                                                    @endif
                                                    @if($stage->completed_at)
                                                        <br>Selesai: {{ $stage->completed_at->format('d M Y') }}
                                                    @endif
                                                </p>
                                            @endif
                                            @php
                                                $badgeColors = [
                                                    'passed' => 'rgba(52,199,89,0.2)',
                                                    'in-progress' => 'rgba(10,132,255,0.2)',
                                                    'failed' => 'rgba(255,59,48,0.2)',
                                                    'pending' => 'rgba(255,214,10,0.2)',
                                                    'skipped' => 'rgba(142,142,147,0.25)',
                                                ];
                                                $badgeColor = $badgeColors[$stage->status] ?? 'rgba(142,142,147,0.25)';
                                            @endphp
                                            <span class="px-2 py-1 rounded-full text-xs font-semibold" style="background: {{ $badgeColor }};">
                                                {{ ucfirst($stage->status) }}
                                            </span>
                                        </div>
                                    </div>
                                    
                                    {{-- Stage Shortcuts --}}
                                    <div class="flex flex-wrap gap-2 mt-3 pt-3" style="border-top: 1px solid rgba(58,58,60,0.6);">
                                        @php
                                            $stageLinks = [
                                                'screening' => [
                                                    'icon' => 'fa-file-download',
                                                    'label' => 'Download CV/Resume',
                                                    'route' => route('admin.applications.download-cv', $application->id),
                                                    'color' => 'rgba(255,214,10,1)',
                                                    'download' => true,
                                                ],
                                                'testing' => [
                                                    'icon' => 'fa-clipboard-check',
                                                    'label' => 'Kelola Test',
                                                    'route' => route('admin.recruitment.tests.index'),
                                                    'color' => 'rgba(10,132,255,1)',
                                                ],
                                                'interview' => [
                                                    'icon' => 'fa-calendar-alt',
                                                    'label' => 'Jadwal Interview',
                                                    'route' => route('admin.recruitment.interviews.create', ['application_id' => $application->id]),
                                                    'color' => 'rgba(175,82,222,1)',
                                                ],
                                                'offer' => [
                                                    'icon' => 'fa-envelope',
                                                    'label' => 'Kirim Offer',
                                                    'route' => '#', // Bisa diganti dengan route kirim email offer
                                                    'color' => 'rgba(52,199,89,1)',
                                                ],
                                            ];
                                            
                                            $stageLink = $stageLinks[$stage->stage_name] ?? null;
                                        @endphp
                                        
                                        @if($stageLink)
                                            <a href="{{ $stageLink['route'] }}" 
                                               class="btn-secondary-sm" 
                                               style="background: rgba(255,255,255,0.05); color: {{ $stageLink['color'] }}; border-color: rgba(255,255,255,0.1);"
                                               @if($stageLink['route'] === '#') onclick="alert('Fitur kirim offer akan segera hadir!'); return false;" @endif
                                               @if(isset($stageLink['download']) && $stageLink['download']) target="_blank" @endif>
                                                <i class="fas {{ $stageLink['icon'] }} mr-1"></i>{{ $stageLink['label'] }}
                                            </a>
                                            
                                            {{-- Additional Portfolio Download for Screening Stage --}}
                                            @if($stage->stage_name === 'screening' && $application->portfolio_path)
                                                <a href="{{ route('admin.applications.download-portfolio', $application->id) }}" 
                                                   class="btn-secondary-sm" 
                                                   style="background: rgba(255,255,255,0.05); color: rgba(255,214,10,1); border-color: rgba(255,255,255,0.1);"
                                                   target="_blank">
                                                    <i class="fas fa-folder mr-1"></i>Download Portfolio
                                                </a>
                                            @endif
                                        @endif
                                        
                                        {{-- Status Action Buttons --}}
                                        @if($stage->status === 'pending' || $stage->status === 'in-progress')
                                            @if($stage->status === 'pending')
                                                <form action="{{ route('admin.recruitment.pipeline.stages.update', $stage->id) }}" method="POST" class="inline-block">
                                                    @csrf
                                                    @method('PATCH')
                                                    <input type="hidden" name="status" value="in-progress">
                                                    <button type="submit" class="btn-secondary-sm" style="background: rgba(10,132,255,0.15); color: rgba(10,132,255,1); border-color: rgba(10,132,255,0.4);">
                                                        <i class="fas fa-play mr-1"></i>Mulai
                                                    </button>
                                                </form>
                                            @endif
                                            
                                            @if($stage->status === 'in-progress')
                                                <form action="{{ route('admin.recruitment.pipeline.stages.update', $stage->id) }}" method="POST" class="inline-block">
                                                    @csrf
                                                    @method('PATCH')
                                                    <input type="hidden" name="status" value="passed">
                                                    <button type="submit" class="btn-secondary-sm" style="background: rgba(52,199,89,0.15); color: rgba(52,199,89,1); border-color: rgba(52,199,89,0.4);">
                                                        <i class="fas fa-check mr-1"></i>Lulus
                                                    </button>
                                                </form>
                                                
                                                <form action="{{ route('admin.recruitment.pipeline.stages.update', $stage->id) }}" method="POST" class="inline-block">
                                                    @csrf
                                                    @method('PATCH')
                                                    <input type="hidden" name="status" value="failed">
                                                    <button type="submit" class="btn-secondary-sm" style="background: rgba(255,59,48,0.15); color: rgba(255,59,48,1); border-color: rgba(255,59,48,0.4);">
                                                        <i class="fas fa-times mr-1"></i>Gagal
                                                    </button>
                                                </form>
                                            @endif
                                            
                                            <form action="{{ route('admin.recruitment.pipeline.stages.update', $stage->id) }}" method="POST" class="inline-block">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="status" value="skipped">
                                                <button type="submit" class="btn-secondary-sm" style="background: rgba(142,142,147,0.15); color: rgba(142,142,147,1); border-color: rgba(142,142,147,0.4);">
                                                    <i class="fas fa-forward mr-1"></i>Skip
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-6" style="color: rgba(235,235,245,0.6);">
                            <i class="fas fa-inbox text-3xl mb-3"></i>
                            <p class="text-sm mb-3">Belum ada tahap rekrutmen</p>
                            <form action="{{ route('admin.recruitment.pipeline.initialize', $application) }}" method="POST" class="inline-block">
                                @csrf
                                <input type="hidden" name="stages[0][stage_name]" value="screening">
                                <input type="hidden" name="stages[0][stage_order]" value="1">
                                <input type="hidden" name="stages[1][stage_name]" value="testing">
                                <input type="hidden" name="stages[1][stage_order]" value="2">
                                <input type="hidden" name="stages[2][stage_name]" value="interview">
                                <input type="hidden" name="stages[2][stage_order]" value="3">
                                <input type="hidden" name="stages[3][stage_name]" value="offer">
                                <input type="hidden" name="stages[3][stage_order]" value="4">
                                <button type="submit" class="btn-primary-sm">
                                    <i class="fas fa-play-circle mr-2"></i>Inisialisasi Tahap
                                </button>
                            </form>
                            <p class="text-xs mt-2" style="color: rgba(235,235,245,0.5);">
                                Membuat 4 tahap: Screening → Testing → Interview → Offer
                            </p>
                        </div>
                    @endif
                </div>
            </section>
        </div>

        {{-- Right Column: Timeline & Details --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Timeline --}}
            <section class="card-elevated rounded-apple-xl overflow-hidden">
                <div class="px-4 py-3 border-b" style="border-color: rgba(58,58,60,0.6);">
                    <p class="text-xs uppercase tracking-[0.25em]" style="color: rgba(235,235,245,0.55);">Aktivitas</p>
                    <h3 class="text-base font-semibold text-white">Timeline Kandidat</h3>
                </div>
                <div class="p-6">
                    @if(isset($timeline) && count($timeline) > 0)
                        <div class="space-y-6">
                            @foreach($timeline as $item)
                                <div class="timeline-item">
                                    <div class="flex gap-4">
                                        <div class="flex-shrink-0">
                                            @php
                                                $iconColors = [
                                                    'primary' => 'rgba(10,132,255,0.2)',
                                                    'success' => 'rgba(52,199,89,0.2)',
                                                    'danger' => 'rgba(255,59,48,0.2)',
                                                    'warning' => 'rgba(255,214,10,0.2)',
                                                    'info' => 'rgba(90,200,250,0.2)',
                                                    'secondary' => 'rgba(142,142,147,0.25)',
                                                ];
                                                $iconColor = $iconColors[$item['color'] ?? 'secondary'] ?? 'rgba(142,142,147,0.25)';
                                            @endphp
                                            <div class="w-10 h-10 rounded-full flex items-center justify-center" style="background: {{ $iconColor }};">
                                                <i class="fas fa-{{ $item['icon'] ?? 'circle' }} text-sm"></i>
                                            </div>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="flex justify-between items-start gap-4 mb-1">
                                                <h4 class="font-semibold text-white">{{ $item['title'] }}</h4>
                                                <span class="text-xs flex-shrink-0" style="color: rgba(235,235,245,0.55);">{{ $item['timestamp'] }}</span>
                                            </div>
                                            @if($item['description'])
                                                <p class="text-sm mb-2" style="color: rgba(235,235,245,0.7);">{{ $item['description'] }}</p>
                                            @endif
                                            @if(isset($item['score']))
                                                <span class="px-2 py-1 rounded-full text-xs font-semibold" style="background: rgba(10,132,255,0.2);">
                                                    Skor: {{ $item['score'] }}{{ is_numeric($item['score']) ? '%' : '' }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8" style="color: rgba(235,235,245,0.6);">
                            <i class="fas fa-clock text-4xl mb-3"></i>
                            <p class="text-sm">Belum ada aktivitas</p>
                        </div>
                    @endif
                </div>
            </section>

            {{-- Interviews --}}
            @if($application->interviewSchedules->count() > 0)
                <section class="card-elevated rounded-apple-xl overflow-hidden">
                    <div class="px-4 py-3 border-b flex justify-between items-center" style="border-color: rgba(58,58,60,0.6);">
                        <div>
                            <p class="text-xs uppercase tracking-[0.25em]" style="color: rgba(235,235,245,0.55);">Jadwal</p>
                            <h3 class="text-base font-semibold text-white">Interview</h3>
                        </div>
                        <a href="{{ route('admin.recruitment.interviews.create', ['application_id' => $application->id]) }}" class="btn-primary-sm">
                            <i class="fas fa-plus mr-1"></i>Jadwalkan
                        </a>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead class="bg-dark-bg-secondary border-b" style="border-color: rgba(58,58,60,0.6); color: rgba(235,235,245,0.55);">
                                <tr class="text-left text-xs uppercase tracking-wider">
                                    <th class="px-4 py-2">Tanggal</th>
                                    <th class="px-4 py-2">Tipe</th>
                                    <th class="px-4 py-2">Durasi</th>
                                    <th class="px-4 py-2">Status</th>
                                    <th class="px-4 py-2 text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y" style="border-color: rgba(58,58,60,0.6); color: rgba(235,235,245,0.9);">
                                @foreach($application->interviewSchedules as $interview)
                                    <tr class="hover:bg-white/5 transition-colors">
                                        <td class="px-4 py-3">
                                            <p class="text-sm">{{ $interview->scheduled_at->format('d M Y') }}</p>
                                            <p class="text-xs" style="color: rgba(235,235,245,0.55);">{{ $interview->scheduled_at->format('H:i') }}</p>
                                        </td>
                                        <td class="px-4 py-3">
                                            <span class="px-2 py-1 rounded-full text-xs font-semibold" style="background: rgba(90,200,250,0.2);">
                                                {{ $interview->getMeetingTypeLabel() }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3">{{ $interview->duration_minutes }} menit</td>
                                        <td class="px-4 py-3">
                                            @php
                                                $statusColors = [
                                                    'completed' => 'rgba(52,199,89,0.2)',
                                                    'scheduled' => 'rgba(255,214,10,0.2)',
                                                ];
                                                $statusColor = $statusColors[$interview->status] ?? 'rgba(142,142,147,0.25)';
                                            @endphp
                                            <span class="px-2 py-1 rounded-full text-xs font-semibold" style="background: {{ $statusColor }};">
                                                {{ ucfirst($interview->status) }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-right">
                                            <a href="{{ route('admin.recruitment.interviews.show', $interview) }}" class="btn-secondary-sm">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </section>
            @endif

            {{-- Test Sessions --}}
            @if($application->testSessions->count() > 0)
                <section class="card-elevated rounded-apple-xl overflow-hidden">
                    <div class="px-4 py-3 border-b" style="border-color: rgba(58,58,60,0.6);">
                        <p class="text-xs uppercase tracking-[0.25em]" style="color: rgba(235,235,245,0.55);">Hasil Tes</p>
                        <h3 class="text-base font-semibold text-white">Sesi Tes</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead class="bg-dark-bg-secondary border-b" style="border-color: rgba(58,58,60,0.6); color: rgba(235,235,245,0.55);">
                                <tr class="text-left text-xs uppercase tracking-wider">
                                    <th class="px-4 py-2">Nama Tes</th>
                                    <th class="px-4 py-2">Status</th>
                                    <th class="px-4 py-2">Skor</th>
                                    <th class="px-4 py-2">Selesai</th>
                                    <th class="px-4 py-2 text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y" style="border-color: rgba(58,58,60,0.6); color: rgba(235,235,245,0.9);">
                                @foreach($application->testSessions as $session)
                                    <tr class="hover:bg-white/5 transition-colors">
                                        <td class="px-4 py-3">
                                            <p class="font-medium">{{ $session->testTemplate->title ?? 'N/A' }}</p>
                                        </td>
                                        <td class="px-4 py-3">
                                            @php
                                                $statusColors = [
                                                    'completed' => 'rgba(52,199,89,0.2)',
                                                    'in-progress' => 'rgba(255,214,10,0.2)',
                                                ];
                                                $statusColor = $statusColors[$session->status] ?? 'rgba(142,142,147,0.25)';
                                            @endphp
                                            <span class="px-2 py-1 rounded-full text-xs font-semibold" style="background: {{ $statusColor }};">
                                                {{ ucfirst($session->status) }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3">
                                            @if($session->final_score)
                                                <span class="font-bold text-apple-blue">{{ $session->final_score }}%</span>
                                            @else
                                                <span style="color: rgba(235,235,245,0.55);">-</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3">
                                            @if($session->completed_at)
                                                {{ $session->completed_at->format('d M Y') }}
                                            @else
                                                <span style="color: rgba(235,235,245,0.55);">-</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 text-right">
                                            @if($session->status == 'completed')
                                                <a href="{{ route('admin.recruitment.tests.sessions.results', $session) }}" 
                                                   class="btn-secondary-sm"
                                                   title="Lihat Hasil">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            @elseif(($session->status == 'in-progress' || $session->status == 'pending') && $session->session_token)
                                                <a href="{{ route('candidate.test.show', $session->session_token) }}" 
                                                   class="btn-primary-sm"
                                                   target="_blank"
                                                   title="Buka Link Tes">
                                                    <i class="fas fa-external-link-alt"></i>
                                                </a>
                                            @elseif(($session->status == 'in-progress' || $session->status == 'pending') && !$session->session_token)
                                                <button class="btn-secondary-sm opacity-50 cursor-not-allowed"
                                                        disabled
                                                        title="Token tidak tersedia">
                                                    <i class="fas fa-exclamation-triangle"></i>
                                                </button>
                                            @else
                                                <span class="text-xs" style="color: rgba(235,235,245,0.55);">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </section>
            @endif
        </div>
    </div>
</div>
@endsection
