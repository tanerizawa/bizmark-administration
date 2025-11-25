@extends('layouts.app')

@section('title', 'Interviews')

@php
    $completedCount = $metrics['completed'] ?? \App\Models\InterviewSchedule::where('status', 'completed')->count();
    $cancelledCount = $metrics['cancelled'] ?? \App\Models\InterviewSchedule::where('status', 'cancelled')->count();
@endphp

@section('content')
<div class="recruitment-shell max-w-7xl mx-auto space-y-5">
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
                    <span>Interviews</span>
                </div>
                <h1 class="text-2xl font-semibold text-white leading-tight">Manajemen Interview</h1>
                <p class="text-sm" style="color: rgba(235,235,245,0.7);">
                    Jadwalkan dan pantau interview kandidat dalam satu panel.
                </p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('admin.recruitment.interviews.create') }}" class="btn-primary">
                    <i class="fas fa-plus mr-2"></i>Jadwalkan Interview
                </a>
            </div>
        </div>
    </section>

    {{-- Stats --}}
    <section class="grid grid-cols-2 sm:grid-cols-4 gap-3">
        <div class="card-elevated rounded-apple-lg p-3.5 space-y-1">
            <p class="text-xs uppercase tracking-widest" style="color: rgba(10,132,255,0.9);">Interview Hari Ini</p>
            <p class="text-2xl font-bold text-white">{{ $todayInterviews->count() }}</p>
            <p class="text-xs" style="color: rgba(235,235,245,0.6);">Terlaksana hari ini</p>
        </div>
        <div class="card-elevated rounded-apple-lg p-3.5 space-y-1">
            <p class="text-xs uppercase tracking-widest" style="color: rgba(52,199,89,0.9);">Akan Datang</p>
            <p class="text-2xl font-bold text-white">{{ $upcomingInterviews->count() }}</p>
            <p class="text-xs" style="color: rgba(235,235,245,0.6);">Terjadwal</p>
        </div>
        <div class="card-elevated rounded-apple-lg p-3.5 space-y-1">
            <p class="text-xs uppercase tracking-widest" style="color: rgba(52,199,89,0.9);">Selesai</p>
            <p class="text-2xl font-bold text-white">{{ $completedCount }}</p>
            <p class="text-xs" style="color: rgba(235,235,245,0.6);">Status selesai</p>
        </div>
        <div class="card-elevated rounded-apple-lg p-3.5 space-y-1">
            <p class="text-xs uppercase tracking-widest" style="color: rgba(255,59,48,0.9);">Dibatalkan</p>
            <p class="text-2xl font-bold text-white">{{ $cancelledCount }}</p>
            <p class="text-xs" style="color: rgba(235,235,245,0.6);">Total batal</p>
        </div>
    </section>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        {{-- Calendar --}}
        <div class="lg:col-span-2 space-y-4">
            <div class="card-elevated rounded-apple-xl p-4">
                <div class="flex items-center justify-between mb-3">
                    <div>
                        <p class="text-xs uppercase tracking-[0.3em]" style="color: rgba(235,235,245,0.5);">Kalender</p>
                        <h3 class="text-base font-semibold text-white">Jadwal Interview</h3>
                    </div>
                </div>
                <div id="interviewCalendar"></div>
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="space-y-4">
            <div class="card-elevated rounded-apple-xl overflow-hidden">
                <div class="px-4 py-3 border-b" style="border-color: rgba(58,58,60,0.6);">
                    <h3 class="text-base font-semibold text-white">Jadwal Hari Ini</h3>
                </div>
                <div class="divide-y" style="border-color: rgba(58,58,60,0.6);">
                    @forelse($todayInterviews as $interview)
                        <div class="px-4 py-3 space-y-1">
                            <div class="flex items-start justify-between">
                                <div>
                                    <p class="text-sm font-semibold text-white">{{ $interview->jobApplication->full_name }}</p>
                                    <p class="text-xs" style="color: rgba(235,235,245,0.65);">{{ $interview->jobApplication?->jobVacancy?->title ?? 'Position Deleted' }}</p>
                                </div>
                                <span class="px-2 py-1 rounded-full text-xs font-semibold" style="background: {{ $interview->status === 'scheduled' ? 'rgba(10,132,255,0.2)' : 'rgba(142,142,147,0.2)' }};">
                                    {{ ucfirst($interview->status) }}
                                </span>
                            </div>
                            <p class="text-xs" style="color: rgba(235,235,245,0.65);">
                                <i class="fas fa-clock mr-1"></i>{{ $interview->scheduled_at->format('H:i') }} ({{ $interview->duration_minutes }} mnt)
                            </p>
                            <p class="text-xs" style="color: rgba(235,235,245,0.65);">
                                <i class="fas fa-{{ $interview->interview_type === 'video' ? 'video' : ($interview->interview_type === 'phone' ? 'phone-alt' : 'map-marker-alt') }} mr-1"></i>
                                {{ $interview->getMeetingTypeLabel() }}
                            </p>
                            <div class="pt-1">
                                <a href="{{ route('admin.recruitment.interviews.show', $interview) }}" class="btn-secondary-sm">
                                    Lihat Detail
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="p-4 text-center" style="color: rgba(235,235,245,0.6);">
                            <i class="fas fa-calendar-times text-2xl mb-2"></i>
                            <p class="text-sm mb-0">Tidak ada interview hari ini</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="card-elevated rounded-apple-xl overflow-hidden">
                <div class="px-4 py-3 border-b" style="border-color: rgba(58,58,60,0.6);">
                    <h3 class="text-base font-semibold text-white">Minggu Ini</h3>
                </div>
                <div class="divide-y" style="border-color: rgba(58,58,60,0.6);">
                    @forelse($upcomingInterviews->take(5) as $interview)
                        <div class="px-4 py-3">
                            <p class="text-xs" style="color: rgba(235,235,245,0.65);">{{ $interview->scheduled_at->format('D, d M H:i') }}</p>
                            <p class="text-sm font-semibold text-white">{{ $interview->jobApplication->full_name }}</p>
                            <p class="text-xs" style="color: rgba(235,235,245,0.6);">{{ $interview->jobApplication?->jobVacancy?->title ?? 'Position Deleted' }}</p>
                        </div>
                    @empty
                        <div class="p-4 text-center" style="color: rgba(235,235,245,0.6);">
                            <p class="text-sm mb-0">Tidak ada jadwal minggu ini</p>
                        </div>
                    @endforelse
                    @if($upcomingInterviews->count() > 5)
                        <div class="p-3 text-center" style="color: rgba(235,235,245,0.6);">
                            +{{ $upcomingInterviews->count() - 5 }} lainnya
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.css" rel="stylesheet">
<style>
    #interviewCalendar {
        max-width: 100%;
        min-height: 600px;
    }
    .fc-theme-standard td, .fc-theme-standard th {
        border-color: rgba(58,58,60,0.6);
    }
    .fc .fc-toolbar-title {
        color: #fff;
        font-size: 1.1rem;
        font-weight: 600;
    }
    .fc .fc-button-primary {
        background: rgba(255,255,255,0.08);
        border: 1px solid rgba(255,255,255,0.15);
        color: #fff;
    }
    .fc .fc-button-primary:hover {
        background: rgba(255,255,255,0.15);
    }
    .fc .fc-col-header-cell-cushion, .fc .fc-daygrid-day-number {
        color: rgba(235,235,245,0.8);
    }
    .fc-event {
        border: none;
        padding: 2px 6px;
        background: rgba(10,132,255,0.9);
    }
    .fc-event:hover {
        opacity: 0.9;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const calendarEl = document.getElementById('interviewCalendar');
    
    const calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'timeGridWeek',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        slotMinTime: '08:00:00',
        slotMaxTime: '18:00:00',
        allDaySlot: false,
        nowIndicator: true,
        selectable: true,
        selectMirror: true,
        
        events: {
            url: '{{ route("admin.recruitment.interviews.index") }}',
            method: 'GET',
            extraParams: () => ({ json: 1 }),
            failure: () => alert('Error loading interviews!')
        },
        
        eventClick: function(info) {
            window.location.href = '/admin/recruitment/interviews/' + info.event.id;
        },
        
        select: function(info) {
            let url = '{{ route("admin.recruitment.interviews.create") }}';
            url += '?date=' + info.startStr;
            window.location.href = url;
        }
    });
    
    calendar.render();
});
</script>
@endpush
