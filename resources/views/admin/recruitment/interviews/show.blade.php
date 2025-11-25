@extends('layouts.app')

@section('title', 'Interview - ' . $interview->jobApplication->full_name)

@section('content')
@php
    $application = $interview->jobApplication;
    $vacancy = $application->jobVacancy;
    $statusText = ucfirst($interview->status);
    $statusStyle = $interview->status === 'completed'
        ? 'background: rgba(52,199,89,0.2); color: rgba(52,199,89,1);'
        : ($interview->status === 'scheduled'
            ? 'background: rgba(10,132,255,0.2); color: rgba(10,132,255,1);'
            : 'background: rgba(142,142,147,0.2); color: rgba(142,142,147,1);');
@endphp

<div class="recruitment-shell max-w-6xl mx-auto space-y-5">
    {{-- Header --}}
    <section class="card-elevated rounded-apple-xl p-5 md:p-6 relative overflow-hidden">
        <div class="absolute inset-0 pointer-events-none" aria-hidden="true">
            <div class="w-72 h-72 bg-apple-blue opacity-25 blur-3xl rounded-full absolute -top-14 -right-10"></div>
            <div class="w-48 h-48 bg-apple-green opacity-20 blur-2xl rounded-full absolute bottom-0 left-8"></div>
        </div>
        <div class="relative flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div class="space-y-2.5">
                <div class="flex items-center gap-2 text-xs uppercase tracking-[0.35em]" style="color: rgba(235,235,245,0.6);">
                    <a href="{{ route('admin.recruitment.interviews.index') }}" class="inline-flex items-center gap-2 hover:text-white transition-apple">
                        <i class="fas fa-arrow-left text-xs"></i> Interviews
                    </a>
                    <span class="text-dark-text-tertiary">/</span>
                    <span>Detail</span>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <h1 class="text-xl md:text-2xl font-semibold text-white leading-tight">{{ $application->full_name }}</h1>
                    <span class="px-3 py-1 text-xs font-semibold rounded-full" style="{{ $statusStyle }}">
                        {{ $statusText }}
                    </span>
                </div>
                <p class="text-sm" style="color: rgba(235,235,245,0.7);">
                    {{ $vacancy->title }} · {{ $vacancy->location }} · {{ $interview->scheduled_at->format('d M Y, H:i') }} WIB
                </p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('admin.recruitment.pipeline.show', $application) }}" class="btn-secondary-sm">
                    <i class="fas fa-diagram-project mr-2"></i>Pipeline
                </a>
                <a href="{{ route('admin.recruitment.interviews.edit', $interview) }}" class="btn-primary-sm">
                    <i class="fas fa-edit mr-2"></i>Edit
                </a>
            </div>
        </div>
    </section>

    {{-- Stats --}}
    <section class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3">
        <div class="card-elevated rounded-apple-lg p-3.5 space-y-1">
            <p class="text-xs uppercase tracking-widest" style="color: rgba(10,132,255,0.9);">Tanggal</p>
            <p class="text-2xl font-bold text-white">{{ $interview->scheduled_at->format('d M Y') }}</p>
            <p class="text-xs" style="color: rgba(235,235,245,0.6);">{{ $interview->scheduled_at->diffForHumans() }}</p>
        </div>
        <div class="card-elevated rounded-apple-lg p-3.5 space-y-1">
            <p class="text-xs uppercase tracking-widest" style="color: rgba(52,199,89,0.9);">Durasi</p>
            <p class="text-2xl font-bold text-white">{{ $interview->duration_minutes }} mnt</p>
            <p class="text-xs" style="color: rgba(235,235,245,0.6);">{{ ucfirst($interview->interview_type) }}</p>
        </div>
        <div class="card-elevated rounded-apple-lg p-3.5 space-y-1">
            <p class="text-xs uppercase tracking-widest" style="color: rgba(191,90,242,0.9);">Meeting</p>
            <p class="text-2xl font-bold text-white">{{ ucfirst(str_replace('-', ' ', $interview->meeting_type)) }}</p>
            <p class="text-xs" style="color: rgba(235,235,245,0.6);">Tipe pertemuan</p>
        </div>
        <div class="card-elevated rounded-apple-lg p-3.5 space-y-1">
            <p class="text-xs uppercase tracking-widest" style="color: rgba(255,159,10,0.9);">Status</p>
            <p class="text-2xl font-bold text-white">{{ $statusText }}</p>
            <p class="text-xs" style="color: rgba(235,235,245,0.6);">Update {{ $interview->updated_at->diffForHumans() }}</p>
        </div>
    </section>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        <div class="lg:col-span-2 space-y-4">
            {{-- Interview Details --}}
            <div class="card-elevated rounded-apple-xl p-4 space-y-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs uppercase tracking-[0.3em]" style="color: rgba(235,235,245,0.5);">Informasi</p>
                        <h3 class="text-base font-semibold text-white">Detail Interview</h3>
                    </div>
                    <span class="px-2 py-1 rounded-full text-xs font-semibold" style="{{ $statusStyle }}">{{ $statusText }}</span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm" style="color: rgba(235,235,245,0.85);">
                    <div class="space-y-1">
                        <p class="text-xs uppercase tracking-widest" style="color: rgba(235,235,245,0.55);">Kandidat</p>
                        <p class="font-semibold text-white">{{ $application->full_name }}</p>
                        <p class="text-xs" style="color: rgba(235,235,245,0.65);">{{ $application->email }}{{ $application->phone ? ' · ' . $application->phone : '' }}</p>
                    </div>
                    <div class="space-y-1">
                        <p class="text-xs uppercase tracking-widest" style="color: rgba(235,235,245,0.55);">Posisi</p>
                        <p class="font-semibold text-white">{{ $vacancy->title }}</p>
                        <p class="text-xs" style="color: rgba(235,235,245,0.65);">{{ $vacancy->location }}</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm" style="color: rgba(235,235,245,0.85);">
                    <div class="space-y-1">
                        <p class="text-xs uppercase tracking-widest" style="color: rgba(235,235,245,0.55);">Jadwal</p>
                        <p class="font-semibold text-white">{{ $interview->scheduled_at->format('d M Y, H:i') }} WIB</p>
                        <p class="text-xs" style="color: rgba(235,235,245,0.65);">{{ $interview->scheduled_at->diffForHumans() }}</p>
                    </div>
                    <div class="space-y-1">
                        <p class="text-xs uppercase tracking-widest" style="color: rgba(235,235,245,0.55);">Pertemuan</p>
                        <p class="font-semibold text-white">{{ $interview->getMeetingTypeLabel() }}</p>
                        @if($interview->meeting_link)
                            <a href="{{ $interview->meeting_link }}" target="_blank" class="text-apple-blue text-xs hover:underline break-words">
                                {{ $interview->meeting_link }}
                            </a>
                        @elseif($interview->location)
                            <p class="text-xs" style="color: rgba(235,235,245,0.65);">{{ $interview->location }}</p>
                        @endif
                    </div>
                </div>

                <div class="space-y-2">
                    <p class="text-xs uppercase tracking-[0.25em]" style="color: rgba(235,235,245,0.55);">Interviewer</p>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                        @php $interviewers = $interview->interviewers(); @endphp
                        @forelse($interviewers as $interviewer)
                            <div class="card-elevated rounded-apple-lg p-3 flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-apple-blue bg-opacity-15 text-apple-blue flex items-center justify-center font-semibold">
                                    {{ strtoupper(substr($interviewer->full_name ?? $interviewer->name, 0, 2)) }}
                                </div>
                                <div class="min-w-0">
                                    <p class="text-sm font-semibold text-white">{{ $interviewer->full_name ?? $interviewer->name }}</p>
                                    <p class="text-xs" style="color: rgba(235,235,245,0.65);">{{ $interviewer->email }}</p>
                                </div>
                            </div>
                        @empty
                            <p class="text-xs" style="color: rgba(235,235,245,0.6);">Belum ada interviewer ditetapkan.</p>
                        @endforelse
                    </div>
                </div>

                @if($interview->notes)
                    <div class="space-y-2">
                        <p class="text-xs uppercase tracking-[0.25em]" style="color: rgba(235,235,245,0.55);">Catatan Internal</p>
                        <div class="card-elevated rounded-apple-lg p-3" style="background: rgba(10,132,255,0.08); border-color: rgba(10,132,255,0.2);">
                            <p class="text-sm" style="color: rgba(235,235,245,0.8);">{{ $interview->notes }}</p>
                        </div>
                    </div>
                @endif

                <div class="flex flex-wrap gap-2 pt-2">
                    @if($interview->status === 'scheduled')
                        <form action="{{ route('admin.recruitment.interviews.update', $interview) }}" method="POST" onsubmit="return confirm('Tandai interview selesai?')">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="status" value="completed">
                            <button type="submit" class="btn-secondary-sm" style="background: rgba(52,199,89,0.18); color: rgba(52,199,89,0.95); border-color: rgba(52,199,89,0.35);">
                                <i class="fas fa-check mr-2"></i>Tandai Selesai
                            </button>
                        </form>
                    @endif
                    <form action="{{ route('admin.recruitment.interviews.destroy', $interview) }}" method="POST" onsubmit="return confirm('Hapus interview ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn-secondary-sm" style="background: rgba(255,59,48,0.15); color: rgba(255,59,48,0.95); border-color: rgba(255,59,48,0.4);">
                            <i class="fas fa-trash mr-2"></i>Hapus
                        </button>
                    </form>
                </div>
            </div>

            {{-- Feedback --}}
            @if($interview->relationLoaded('feedback') && $interview->feedback->count() > 0)
                <div class="card-elevated rounded-apple-xl p-4 space-y-3">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs uppercase tracking-[0.3em]" style="color: rgba(235,235,245,0.5);">Feedback</p>
                            <h3 class="text-base font-semibold text-white">Ringkasan Penilai</h3>
                        </div>
                        <a href="{{ route('admin.recruitment.interviews.feedback.show', $interview) }}" class="btn-secondary-sm">Lihat Semua</a>
                    </div>
                    <div class="space-y-3">
                        @foreach($interview->feedback as $feedback)
                            @php
                                $recLabel = $feedback->getRecommendationLabel();
                                $recColor = $feedback->recommendation === 'strong-hire' ? 'rgba(52,199,89,0.25)'
                                    : ($feedback->recommendation === 'hire' ? 'rgba(10,132,255,0.25)'
                                    : ($feedback->recommendation === 'maybe' ? 'rgba(255,149,0,0.25)' : 'rgba(255,59,48,0.25)'));
                            @endphp
                            <div class="card-elevated rounded-apple-lg p-3 space-y-2">
                                <div class="flex items-start justify-between">
                                    <div>
                                        <p class="text-sm font-semibold text-white">{{ $feedback->interviewer->name }}</p>
                                        <p class="text-xs" style="color: rgba(235,235,245,0.6);">{{ $feedback->created_at->format('d M Y H:i') }}</p>
                                    </div>
                                    <span class="px-2 py-1 rounded-full text-xs font-semibold" style="background: {{ $recColor }};">
                                        {{ $recLabel }}
                                    </span>
                                </div>
                                <div class="grid grid-cols-3 gap-2 text-xs" style="color: rgba(235,235,245,0.75);">
                                    <div>Teknis: <span class="font-semibold text-white">{{ $feedback->technical_score }}/10</span></div>
                                    <div>Komunikasi: <span class="font-semibold text-white">{{ $feedback->communication_score }}/10</span></div>
                                    <div>Overall: <span class="font-semibold text-white">{{ $feedback->calculateOverallRating() }}/10</span></div>
                                </div>
                                @if($feedback->notes)
                                    <p class="text-sm" style="color: rgba(235,235,245,0.75);">{{ $feedback->notes }}</p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        {{-- Sidebar --}}
        <div class="space-y-4">
            <div class="card-elevated rounded-apple-xl p-4 space-y-3">
                <h3 class="text-base font-semibold text-white">Aksi Cepat</h3>
                <div class="divide-y" style="border-color: rgba(58,58,60,0.6);">
                    <a href="{{ route('admin.recruitment.interviews.create', ['application_id' => $application->id]) }}" class="flex items-center gap-2 px-2 py-3 hover:bg-white/5 transition-apple">
                        <i class="fas fa-calendar-plus text-apple-blue"></i>
                        <span class="text-sm">Jadwalkan Interview Lanjutan</span>
                    </a>
                    <a href="{{ route('admin.recruitment.tests.create', ['application_id' => $application->id]) }}" class="flex items-center gap-2 px-2 py-3 hover:bg-white/5 transition-apple">
                        <i class="fas fa-clipboard-check text-apple-green"></i>
                        <span class="text-sm">Assign Test</span>
                    </a>
                    <a href="mailto:{{ $application->email }}" class="flex items-center gap-2 px-2 py-3 hover:bg-white/5 transition-apple">
                        <i class="fas fa-envelope text-apple-orange"></i>
                        <span class="text-sm">Kirim Email</span>
                    </a>
                </div>
            </div>

            <div class="card-elevated rounded-apple-xl p-4 space-y-3">
                <h3 class="text-base font-semibold text-white">Timeline</h3>
                <div class="space-y-2 text-sm" style="color: rgba(235,235,245,0.8);">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-clock text-apple-blue"></i>
                        <span>Scheduled {{ $interview->created_at->diffForHumans() }}</span>
                    </div>
                    @if($interview->candidate_joined_at)
                        <div class="flex items-center gap-2">
                            <i class="fas fa-sign-in-alt text-apple-green"></i>
                            <span>Candidate joined {{ $interview->candidate_joined_at->diffForHumans() }}</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
