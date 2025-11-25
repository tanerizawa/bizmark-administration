@extends('layouts.app')

@section('content')
<div class="recruitment-shell max-w-6xl mx-auto space-y-5">
    {{-- Header --}}
    <section class="card-elevated rounded-apple-xl p-5 md:p-6 relative overflow-hidden">
        <div class="absolute inset-0 pointer-events-none" aria-hidden="true">
            <div class="w-60 h-60 bg-apple-blue opacity-20 blur-3xl rounded-full absolute -top-10 -right-6"></div>
            <div class="w-44 h-44 bg-apple-green opacity-15 blur-2xl rounded-full absolute bottom-0 left-6"></div>
        </div>
        <div class="relative flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div class="space-y-2">
                <div class="flex items-center gap-2 text-xs uppercase tracking-[0.35em]" style="color: rgba(235,235,245,0.6);">
                    <a href="{{ route('admin.recruitment.interviews.index') }}" class="inline-flex items-center gap-2 hover:text-white transition-apple">
                        <i class="fas fa-arrow-left text-xs"></i> Interviews
                    </a>
                    <span class="text-dark-text-tertiary">/</span>
                    <span>Edit</span>
                </div>
                <h1 class="text-xl md:text-2xl font-semibold text-white leading-tight">Edit Interview</h1>
                <p class="text-sm" style="color: rgba(235,235,245,0.7);">
                    Update jadwal, format, atau tim pewawancara.
                </p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('admin.recruitment.interviews.show', $interview) }}" class="btn-secondary-sm">
                    <i class="fas fa-eye mr-2"></i>Detail
                </a>
            </div>
        </div>
    </section>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        <div class="lg:col-span-2 lg:order-1 space-y-4">
            <div class="card-elevated rounded-apple-xl p-4 space-y-4">
                <div>
                    <p class="text-xs uppercase tracking-[0.3em]" style="color: rgba(235,235,245,0.5);">Formulir</p>
                    <h3 class="text-base font-semibold text-white">Detail Interview</h3>
                </div>
                <form action="{{ route('admin.recruitment.interviews.update', $interview) }}" method="POST" class="space-y-3">
                    @csrf
                    @method('PUT')

                    <!-- Candidate Info (Read-only) -->
                    <div class="space-y-1">
                        <label class="text-xs uppercase tracking-widest" style="color: rgba(235,235,245,0.55);">Kandidat</label>
                        <div class="card-elevated rounded-apple-lg p-3 space-y-1" style="background: rgba(10,132,255,0.08); border-color: rgba(10,132,255,0.2);">
                            <p class="text-sm font-semibold text-white">{{ $interview->jobApplication->full_name }}</p>
                            <p class="text-xs" style="color: rgba(235,235,245,0.65);">Posisi: {{ $interview->jobApplication?->jobVacancy?->title ?? 'Position Deleted' }}</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                        <div class="md:col-span-2 space-y-1">
                            <label for="scheduled_at" class="text-xs uppercase tracking-widest" style="color: rgba(235,235,245,0.55);">Tanggal & Waktu *</label>
                            <input type="datetime-local" 
                                   name="scheduled_at" 
                                   id="scheduled_at" 
                                   class="w-full @error('scheduled_at') is-invalid @enderror"
                                   value="{{ old('scheduled_at', $interview->scheduled_at->format('Y-m-d\TH:i')) }}"
                                   min="{{ now()->format('Y-m-d\TH:i') }}"
                                   required>
                            @error('scheduled_at')
                                <div class="text-xs text-apple-red">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="space-y-1">
                            <label for="duration_minutes" class="text-xs uppercase tracking-widest" style="color: rgba(235,235,245,0.55);">Durasi (menit) *</label>
                            <select name="duration_minutes" id="duration_minutes" 
                                    class="w-full @error('duration_minutes') is-invalid @enderror" required>
                                <option value="30" {{ old('duration_minutes', $interview->duration_minutes) == 30 ? 'selected' : '' }}>30 minutes</option>
                                <option value="45" {{ old('duration_minutes', $interview->duration_minutes) == 45 ? 'selected' : '' }}>45 minutes</option>
                                <option value="60" {{ old('duration_minutes', $interview->duration_minutes) == 60 ? 'selected' : '' }}>60 minutes</option>
                                <option value="90" {{ old('duration_minutes', $interview->duration_minutes) == 90 ? 'selected' : '' }}>90 minutes</option>
                                <option value="120" {{ old('duration_minutes', $interview->duration_minutes) == 120 ? 'selected' : '' }}>2 hours</option>
                            </select>
                            @error('duration_minutes')
                                <div class="text-xs text-apple-red">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="space-y-1">
                        <label for="meeting_type" class="text-xs uppercase tracking-widest" style="color: rgba(235,235,245,0.55);">Tipe Interview *</label>
                        <select name="meeting_type" id="meeting_type" 
                                class="w-full @error('meeting_type') is-invalid @enderror" required>
                            <option value="video-call" {{ old('meeting_type', $interview->meeting_type) == 'video-call' ? 'selected' : '' }}>Video Conference</option>
                            <option value="phone" {{ old('meeting_type', $interview->meeting_type) == 'phone' ? 'selected' : '' }}>Phone Call</option>
                            <option value="in-person" {{ old('meeting_type', $interview->meeting_type) == 'in-person' ? 'selected' : '' }}>In-Person</option>
                        </select>
                        @error('meeting_type')
                            <div class="text-xs text-apple-red">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="space-y-1">
                        <label for="interview_type" class="text-xs uppercase tracking-widest" style="color: rgba(235,235,245,0.55);">Tahap Interview *</label>
                        <select name="interview_type" id="interview_type" 
                                class="w-full @error('interview_type') is-invalid @enderror" required>
                            <option value="preliminary" {{ old('interview_type', $interview->interview_type) == 'preliminary' ? 'selected' : '' }}>Screening Awal</option>
                            <option value="technical" {{ old('interview_type', $interview->interview_type) == 'technical' ? 'selected' : '' }}>Teknis</option>
                            <option value="hr" {{ old('interview_type', $interview->interview_type) == 'hr' ? 'selected' : '' }}>HR</option>
                            <option value="final" {{ old('interview_type', $interview->interview_type) == 'final' ? 'selected' : '' }}>Final</option>
                        </select>
                        @error('interview_type')
                            <div class="text-xs text-apple-red">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="space-y-1" id="location-field" style="{{ $interview->meeting_type == 'in-person' ? '' : 'display: none;' }}">
                        <label for="location" class="text-xs uppercase tracking-widest" style="color: rgba(235,235,245,0.55);">Lokasi</label>
                        <input type="text" 
                               name="location" 
                               id="location" 
                               class="w-full @error('location') is-invalid @enderror"
                               placeholder="Alamat kantor atau ruang meeting"
                               value="{{ old('location', $interview->location) }}">
                        @error('location')
                            <div class="text-xs text-apple-red">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="space-y-1" id="meeting-link-field" style="{{ $interview->meeting_type == 'video-call' ? '' : 'display: none;' }}">
                        <label for="meeting_link" class="text-xs uppercase tracking-widest" style="color: rgba(235,235,245,0.55);">Meeting Link</label>
                        <input type="url" 
                               name="meeting_link" 
                               id="meeting_link" 
                               class="w-full @error('meeting_link') is-invalid @enderror"
                               placeholder="Link Zoom/Google Meet/Jitsi"
                               value="{{ old('meeting_link', $interview->meeting_link) }}">
                        <p class="text-xs" style="color: rgba(235,235,245,0.6);">
                            URL meeting video call.
                        </p>
                        @error('meeting_link')
                            <div class="text-xs text-apple-red">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="space-y-1">
                        <label for="interviewer_ids" class="text-xs uppercase tracking-widest" style="color: rgba(235,235,245,0.55);">Interviewer *</label>
                        <select name="interviewer_ids[]" id="interviewer_ids" 
                                class="w-full @error('interviewer_ids') is-invalid @enderror" 
                                multiple required style="height: 150px;">
                            @php
                                $currentInterviewerIds = is_array($interview->interviewer_ids) 
                                    ? $interview->interviewer_ids 
                                    : (is_string($interview->interviewer_ids) 
                                        ? json_decode($interview->interviewer_ids, true) ?? []
                                        : []);
                            @endphp
                            @foreach($interviewers as $interviewer)
                                <option value="{{ $interviewer->id }}" 
                                    {{ in_array($interviewer->id, old('interviewer_ids', $currentInterviewerIds)) ? 'selected' : '' }}>
                                    {{ $interviewer->full_name ?? $interviewer->name }} ({{ $interviewer->email }})
                                </option>
                            @endforeach
                        </select>
                        <p class="text-xs" style="color: rgba(235,235,245,0.6);">Gunakan Ctrl/Cmd untuk memilih beberapa.</p>
                        @error('interviewer_ids')
                            <div class="text-xs text-apple-red">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="space-y-1">
                        <label for="notes" class="text-xs uppercase tracking-widest" style="color: rgba(235,235,245,0.55);">Catatan Internal</label>
                        <textarea name="notes" 
                                  id="notes" 
                                  rows="3" 
                                  class="w-full @error('notes') is-invalid @enderror"
                                  placeholder="Catatan persiapan, fokus penilaian, dll.">{{ old('notes', $interview->notes) }}</textarea>
                        @error('notes')
                            <div class="text-xs text-apple-red">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="flex flex-wrap gap-2 pt-2">
                        <button type="submit" class="btn-primary-sm">
                            <i class="fas fa-save mr-2"></i>Simpan Perubahan
                        </button>
                        <a href="{{ route('admin.recruitment.interviews.show', $interview) }}" class="btn-secondary-sm">Batal</a>
                    </div>
                </form>
            </div>
        </div>

        <div class="lg:col-span-1 lg:order-2 space-y-4">
            <div class="card-elevated rounded-apple-xl p-4 space-y-3">
                <h3 class="text-base font-semibold text-white">Info Interview</h3>
                <dl class="space-y-2 text-sm" style="color: rgba(235,235,245,0.75);">
                    <div>
                        <dt class="text-xs uppercase tracking-widest" style="color: rgba(235,235,245,0.55);">Status</dt>
                        <dd class="mt-1">
                            <span class="px-2 py-1 rounded-full text-xs font-semibold" 
                                  style="background: {{ $interview->status == 'completed' ? 'rgba(52,199,89,0.2)' : ($interview->status == 'scheduled' ? 'rgba(10,132,255,0.2)' : 'rgba(142,142,147,0.25)') }};">
                                {{ ucfirst($interview->status) }}
                            </span>
                        </dd>
                    </div>
                    <div>
                        <dt class="text-xs uppercase tracking-widest" style="color: rgba(235,235,245,0.55);">Dijadwalkan</dt>
                        <dd class="mt-1">{{ $interview->created_at->format('d M Y, H:i') }}</dd>
                    </div>
                    @if($interview->completed_at)
                    <div>
                        <dt class="text-xs uppercase tracking-widest" style="color: rgba(235,235,245,0.55);">Diselesaikan</dt>
                        <dd class="mt-1">{{ $interview->completed_at->format('d M Y, H:i') }}</dd>
                    </div>
                    @endif
                </dl>
            </div>

            <div class="card-elevated rounded-apple-xl p-4 space-y-3">
                <h3 class="text-base font-semibold text-white">Tips Edit</h3>
                <ul class="list-disc list-inside text-sm space-y-1" style="color: rgba(235,235,245,0.72);">
                    <li>Perubahan jadwal otomatis mengirim notifikasi.</li>
                    <li>Kandidat tidak bisa diubah setelah dijadwalkan.</li>
                    <li>Update meeting link jika menggunakan platform baru.</li>
                    <li>Tambah/kurangi interviewer sesuai kebutuhan.</li>
                </ul>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const typeSelect = document.getElementById('meeting_type');
    const locationField = document.getElementById('location-field');
    const meetingLinkField = document.getElementById('meeting-link-field');
    
    function updateFields() {
        const type = typeSelect.value;
        
        if (type === 'in-person') {
            locationField.style.display = 'block';
            meetingLinkField.style.display = 'none';
        } else if (type === 'video-call') {
            locationField.style.display = 'none';
            meetingLinkField.style.display = 'block';
        } else if (type === 'phone') {
            locationField.style.display = 'none';
            meetingLinkField.style.display = 'none';
        } else {
            locationField.style.display = 'block';
            meetingLinkField.style.display = 'block';
        }
    }
    
    typeSelect.addEventListener('change', updateFields);
    updateFields(); // Initial state
});
</script>
@endpush
@endsection
