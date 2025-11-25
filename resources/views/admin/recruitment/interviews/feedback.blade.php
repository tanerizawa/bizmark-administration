@extends('layouts.app')

@section('title', 'Interview Feedback - ' . $interview->jobApplication->full_name)

@section('content')
@php
    $application = $interview->jobApplication;
    $vacancy = $application->jobVacancy;
    $statusText = ucfirst($interview->status);
    $statusStyle = $interview->status === 'completed'
        ? 'background: rgba(52,199,89,0.2); color: rgba(52,199,89,1);'
        : 'background: rgba(10,132,255,0.2); color: rgba(10,132,255,1);';
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
                    <a href="{{ route('admin.recruitment.pipeline.index', ['vacancy_id' => $vacancy->id]) }}" class="inline-flex items-center gap-2 hover:text-white transition-apple">
                        <i class="fas fa-arrow-left text-xs"></i> Pipeline
                    </a>
                    <span class="text-dark-text-tertiary">/</span>
                    <span>Feedback</span>
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
                <a href="{{ route('admin.recruitment.interviews.show', $interview) }}" class="btn-secondary-sm">
                    <i class="fas fa-calendar mr-2"></i>Detail Interview
                </a>
                <a href="{{ route('admin.jobs.applications.show', [$vacancy, $application]) }}" class="btn-secondary-sm">
                    <i class="fas fa-file-alt mr-2"></i>Lihat Lamaran
                </a>
            </div>
        </div>
    </section>

    <form action="{{ route('admin.recruitment.interviews.feedback.store', $interview) }}" method="POST" class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        @csrf

        <div class="lg:col-span-2 space-y-4">
            {{-- Interview Info --}}
            <div class="card-elevated rounded-apple-xl p-4 space-y-3">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs uppercase tracking-[0.3em]" style="color: rgba(235,235,245,0.5);">Rincian Interview</p>
                        <h3 class="text-base font-semibold text-white">Ringkasan Jadwal</h3>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-3 text-sm" style="color: rgba(235,235,245,0.85);">
                    <div class="space-y-1">
                        <p class="text-xs uppercase tracking-widest" style="color: rgba(235,235,245,0.55);">Tanggal</p>
                        <p class="font-semibold">{{ $interview->scheduled_at->format('d M Y') }}</p>
                        <p class="text-xs" style="color: rgba(235,235,245,0.65);">{{ $interview->scheduled_at->format('H:i') }} WIB</p>
                    </div>
                    <div class="space-y-1">
                        <p class="text-xs uppercase tracking-widest" style="color: rgba(235,235,245,0.55);">Tipe Interview</p>
                        <p class="font-semibold">{{ ucfirst($interview->interview_type) }}</p>
                        <p class="text-xs" style="color: rgba(235,235,245,0.65);">{{ ucfirst(str_replace('-', ' ', $interview->meeting_type)) }}</p>
                    </div>
                    <div class="space-y-1">
                        <p class="text-xs uppercase tracking-widest" style="color: rgba(235,235,245,0.55);">Durasi</p>
                        <p class="font-semibold">{{ $interview->duration_minutes }} menit</p>
                    </div>
                    <div class="space-y-1">
                        <p class="text-xs uppercase tracking-widest" style="color: rgba(235,235,245,0.55);">Status</p>
                        <span class="px-2 py-1 rounded-full text-xs font-semibold inline-block" style="{{ $statusStyle }}">
                            {{ $statusText }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- Ratings --}}
            <div class="card-elevated rounded-apple-xl p-4 space-y-5">
                <div>
                    <p class="text-xs uppercase tracking-[0.3em]" style="color: rgba(235,235,245,0.5);">Penilaian</p>
                    <h3 class="text-base font-semibold text-white">Skor Interview</h3>
                </div>

                @php
                    $ratingLabels = [
                        1 => 'Sangat Kurang',
                        2 => 'Kurang',
                        3 => 'Cukup',
                        4 => 'Baik',
                        5 => 'Sangat Baik',
                    ];
                @endphp

                @foreach([
                    ['field' => 'communication_rating', 'label' => '1. Komunikasi', 'helper' => 'Kejelasan, bahasa profesional, mendengarkan.'],
                    ['field' => 'technical_rating', 'label' => '2. Pengetahuan Teknis', 'helper' => 'Kompetensi teknis dan problem solving.'],
                    ['field' => 'teamwork_rating', 'label' => '3. Kolaborasi', 'helper' => 'Kerja tim, kooperatif, interpersonal.'],
                    ['field' => 'culture_fit_rating', 'label' => '4. Kesesuaian Budaya', 'helper' => 'Kesesuaian nilai, gaya kerja, adaptasi.'],
                    ['field' => 'overall_rating', 'label' => '5. Penilaian Akhir', 'helper' => 'Impresi keseluruhan kandidat.'],
                ] as $rating)
                    <div class="space-y-2">
                        <div class="flex items-start justify-between">
                            <div>
                                <label class="text-sm font-semibold text-white">{{ $rating['label'] }} <span class="text-apple-red">*</span></label>
                                <p class="text-xs" style="color: rgba(235,235,245,0.65);">{{ $rating['helper'] }}</p>
                            </div>
                        </div>
                        <div class="rating-group">
                            @foreach($ratingLabels as $value => $text)
                                <input type="radio" name="{{ $rating['field'] }}" id="{{ $rating['field'] }}_{{ $value }}" value="{{ $value }}" class="rating-input" {{ old($rating['field']) == $value ? 'checked' : '' }} required>
                                <label for="{{ $rating['field'] }}_{{ $value }}" class="rating-pill">
                                    <span class="rating-stars">
                                        @for($s=1; $s <= $value; $s++)
                                            <i class="fas fa-star"></i>
                                        @endfor
                                    </span>
                                    <span class="rating-text">{{ $text }}</span>
                                </label>
                            @endforeach
                        </div>
                        @error($rating['field'])
                            <div class="text-xs text-apple-red">{{ $message }}</div>
                        @enderror
                    </div>
                @endforeach
            </div>

            {{-- Comments --}}
            <div class="card-elevated rounded-apple-xl p-4 space-y-4">
                <div>
                    <p class="text-xs uppercase tracking-[0.3em]" style="color: rgba(235,235,245,0.5);">Umpan Balik</p>
                    <h3 class="text-base font-semibold text-white">Catatan Detail</h3>
                </div>
                <div class="space-y-3 text-sm" style="color: rgba(235,235,245,0.85);">
                    <div class="space-y-1">
                        <label for="strengths" class="text-xs uppercase tracking-widest" style="color: rgba(235,235,245,0.55);">Kekuatan</label>
                        <textarea name="strengths" id="strengths" class="w-full" rows="3" placeholder="Hal yang menonjol, kekuatan utama.">{{ old('strengths') }}</textarea>
                        @error('strengths')
                            <div class="text-xs text-apple-red">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="space-y-1">
                        <label for="weaknesses" class="text-xs uppercase tracking-widest" style="color: rgba(235,235,245,0.55);">Area Perbaikan</label>
                        <textarea name="weaknesses" id="weaknesses" class="w-full" rows="3" placeholder="Area yang perlu ditingkatkan.">{{ old('weaknesses') }}</textarea>
                        @error('weaknesses')
                            <div class="text-xs text-apple-red">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="space-y-1">
                        <label for="additional_notes" class="text-xs uppercase tracking-widest" style="color: rgba(235,235,245,0.55);">Catatan Tambahan</label>
                        <textarea name="additional_notes" id="additional_notes" class="w-full" rows="3" placeholder="Observasi lain, red flag, atau poin penting.">{{ old('additional_notes') }}</textarea>
                        @error('additional_notes')
                            <div class="text-xs text-apple-red">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Recommendation --}}
            <div class="card-elevated rounded-apple-xl p-4 space-y-3">
                <div>
                    <p class="text-xs uppercase tracking-[0.3em]" style="color: rgba(235,235,245,0.5);">Rekomendasi</p>
                    <h3 class="text-base font-semibold text-white">Keputusan Hiring</h3>
                    <p class="text-xs" style="color: rgba(235,235,245,0.65);">Pilih rekomendasi akhir berdasarkan penilaian.</p>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-2">
                    @foreach([
                        ['value' => 'highly-recommended', 'label' => 'Highly Recommended'],
                        ['value' => 'recommended', 'label' => 'Recommended'],
                        ['value' => 'neutral', 'label' => 'Neutral'],
                        ['value' => 'not-recommended', 'label' => 'Not Recommended'],
                    ] as $rec)
                        <label class="recommend-pill">
                            <input type="radio" name="recommendation" value="{{ $rec['value'] }}" class="hidden" {{ old('recommendation') == $rec['value'] ? 'checked' : '' }} required>
                            <span class="font-semibold text-sm">{{ $rec['label'] }}</span>
                        </label>
                    @endforeach
                </div>
                @error('recommendation')
                    <div class="text-xs text-apple-red">{{ $message }}</div>
                @enderror
            </div>

            <div class="flex flex-wrap gap-2 justify-end">
                <a href="{{ route('admin.recruitment.interviews.show', $interview) }}" class="btn-secondary-sm">
                    <i class="fas fa-times mr-2"></i>Batal
                </a>
                <button type="submit" class="btn-primary">
                    <i class="fas fa-check mr-2"></i>Kirim Feedback
                </button>
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="space-y-4">
            <div class="card-elevated rounded-apple-xl p-4 space-y-3">
                <h3 class="text-base font-semibold text-white">Detail Kandidat</h3>
                <div class="space-y-2 text-sm" style="color: rgba(235,235,245,0.8);">
                    <div class="flex justify-between">
                        <span>Nama</span>
                        <span class="font-semibold text-white">{{ $application->full_name }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Posisi</span>
                        <span class="font-semibold text-white">{{ $vacancy->title }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Lokasi</span>
                        <span>{{ $vacancy->location }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Tahap Terakhir</span>
                        <span>{{ ucfirst($application->status) }}</span>
                    </div>
                </div>
            </div>

            <div class="card-elevated rounded-apple-xl p-4 space-y-3">
                <h3 class="text-base font-semibold text-white">Agenda Interview</h3>
                <div class="space-y-2 text-sm" style="color: rgba(235,235,245,0.8);">
                    <div class="flex justify-between">
                        <span>Tanggal</span>
                        <span>{{ $interview->scheduled_at->format('d M Y') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Waktu</span>
                        <span>{{ $interview->scheduled_at->format('H:i') }} WIB</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Tipe</span>
                        <span>{{ ucfirst($interview->interview_type) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Meeting</span>
                        <span>{{ ucfirst(str_replace('-', ' ', $interview->meeting_type)) }}</span>
                    </div>
                    @if($interview->meeting_link)
                        <div class="pt-2 border-t" style="border-color: rgba(58,58,60,0.6);">
                            <p class="text-xs uppercase tracking-widest" style="color: rgba(235,235,245,0.55);">Link</p>
                            <a href="{{ $interview->meeting_link }}" target="_blank" class="text-apple-blue text-xs hover:underline break-words">
                                {{ $interview->meeting_link }}
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('styles')
<style>
.recruitment-shell .rating-group {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
    gap: 0.5rem;
}
.recruitment-shell .rating-input {
    display: none;
}
.recruitment-shell .rating-pill {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 0.75rem;
    border-radius: 12px;
    border: 1px solid rgba(255,255,255,0.08);
    background: rgba(255,255,255,0.03);
    color: rgba(235,235,245,0.85);
    cursor: pointer;
    transition: all 0.2s ease;
    text-align: center;
}
.recruitment-shell .rating-pill:hover {
    background: rgba(255,255,255,0.06);
}
.recruitment-shell .rating-input:checked + .rating-pill {
    background: rgba(0,122,255,0.2);
    border-color: rgba(0,122,255,0.4);
    color: #fff;
    box-shadow: 0 6px 18px rgba(0,0,0,0.35);
}
.recruitment-shell .rating-stars {
    color: #fbbf24;
    font-size: 0.9rem;
}
.recruitment-shell .rating-text {
    font-size: 0.75rem;
    margin-top: 0.35rem;
}
.recruitment-shell .recommend-pill {
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 0.85rem;
    border-radius: 12px;
    border: 1px solid rgba(255,255,255,0.08);
    background: rgba(255,255,255,0.03);
    color: rgba(235,235,245,0.85);
    cursor: pointer;
    gap: 0.4rem;
    transition: all 0.2s ease;
}
.recruitment-shell .recommend-pill:hover {
    background: rgba(255,255,255,0.06);
}
.recruitment-shell .recommend-pill input[type=radio] {
    display: none;
}
.recruitment-shell .recommend-pill input[type=radio]:checked + span,
.recruitment-shell .recommend-pill:has(input[type=radio]:checked) {
    background: rgba(0,122,255,0.2);
    border-color: rgba(0,122,255,0.4);
    color: #fff;
    box-shadow: 0 6px 18px rgba(0,0,0,0.35);
}
</style>
@endpush
