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
                    <span>Jadwal Baru</span>
                </div>
                <h1 class="text-xl md:text-2xl font-semibold text-white leading-tight">Jadwalkan Interview</h1>
                <p class="text-sm" style="color: rgba(235,235,245,0.7);">
                    Atur jadwal, format, dan tim pewawancara dalam satu langkah.
                </p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('admin.recruitment.interviews.index') }}" class="btn-secondary-sm">
                    <i class="fas fa-list mr-2"></i>Daftar Interview
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
                <form action="{{ route('admin.recruitment.interviews.store') }}" method="POST" class="space-y-3">
                    @csrf

                        <!-- Application Selection -->
                        <div class="space-y-1">
                            <label for="job_application_id" class="text-xs uppercase tracking-widest" style="color: rgba(235,235,245,0.55);">Kandidat *</label>
                            @if($application)
                                <input type="hidden" name="job_application_id" value="{{ $application->id }}">
                                <div class="card-elevated rounded-apple-lg p-3 space-y-1" style="background: rgba(10,132,255,0.08); border-color: rgba(10,132,255,0.2);">
                                    <p class="text-sm font-semibold text-white">{{ $application->full_name }}</p>
                                    <p class="text-xs" style="color: rgba(235,235,245,0.65);">Posisi: {{ $application?->jobVacancy?->title ?? 'Position Deleted' }}</p>
                                    <p class="text-xs" style="color: rgba(235,235,245,0.65);">Email: {{ $application->email }}</p>
                                </div>
                            @else
                                <select name="job_application_id" id="job_application_id" 
                                        class="w-full @error('job_application_id') is-invalid @enderror" required>
                                    <option value="">Pilih kandidat</option>
                                    @php
                                        $applications = App\Models\JobApplication::with('jobVacancy')
                                            ->whereIn('status', ['reviewed', 'shortlisted', 'interview-scheduled'])
                                            ->orderBy('created_at', 'desc')
                                            ->get();
                                    @endphp
                                    @foreach($applications as $app)
                                        <option value="{{ $app->id }}" {{ old('job_application_id') == $app->id ? 'selected' : '' }}>
                                            {{ $app->full_name }} - {{ $app?->jobVacancy?->title ?? 'Position Deleted' }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('job_application_id')
                                    <div class="text-xs text-apple-red">{{ $message }}</div>
                                @enderror
                            @endif
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                            <div class="md:col-span-2 space-y-1">
                                <label for="scheduled_at" class="text-xs uppercase tracking-widest" style="color: rgba(235,235,245,0.55);">Tanggal & Waktu *</label>
                                @php
                                    $defaultDate = old('scheduled_at');
                                    if (!$defaultDate && request('date')) {
                                        // Convert ISO 8601 to datetime-local format
                                        try {
                                            $dateObj = new \DateTime(request('date'));
                                            $defaultDate = $dateObj->format('Y-m-d\TH:i');
                                        } catch (\Exception $e) {
                                            $defaultDate = '';
                                        }
                                    }
                                @endphp
                                <input type="datetime-local" 
                                       name="scheduled_at" 
                                       id="scheduled_at" 
                                       class="w-full @error('scheduled_at') is-invalid @enderror"
                                       value="{{ $defaultDate }}"
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
                                    <option value="30">30 minutes</option>
                                    <option value="45" selected>45 minutes</option>
                                    <option value="60">60 minutes</option>
                                    <option value="90">90 minutes</option>
                                    <option value="120">2 hours</option>
                                </select>
                                @error('duration_minutes')
                                    <div class="text-xs text-apple-red">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <div class="space-y-1">
                                <label for="interview_type" class="text-xs uppercase tracking-widest" style="color: rgba(235,235,245,0.55);">Tipe Interview *</label>
                                <select name="interview_type" id="interview_type" 
                                        class="w-full @error('interview_type') is-invalid @enderror" required>
                                    <option value="preliminary" {{ old('interview_type') == 'preliminary' ? 'selected' : '' }}>Preliminary</option>
                                    <option value="technical" {{ old('interview_type') == 'technical' ? 'selected' : '' }}>Technical</option>
                                    <option value="hr" {{ old('interview_type') == 'hr' ? 'selected' : '' }}>HR</option>
                                    <option value="final" {{ old('interview_type') == 'final' ? 'selected' : '' }}>Final</option>
                                </select>
                                @error('interview_type')
                                    <div class="text-xs text-apple-red">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="space-y-1">
                                <label for="meeting_type" class="text-xs uppercase tracking-widest" style="color: rgba(235,235,245,0.55);">Format Meeting *</label>
                                <select name="meeting_type" id="meeting_type" 
                                        class="w-full @error('meeting_type') is-invalid @enderror" required>
                                    <option value="video-call" {{ old('meeting_type') == 'video-call' ? 'selected' : '' }} selected>Video Conference</option>
                                    <option value="phone" {{ old('meeting_type') == 'phone' ? 'selected' : '' }}>Phone Call</option>
                                    <option value="in-person" {{ old('meeting_type') == 'in-person' ? 'selected' : '' }}>In-Person</option>
                                </select>
                                @error('meeting_type')
                                    <div class="text-xs text-apple-red">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="space-y-1" id="location-field" style="display: none;">
                            <label for="location" class="text-xs uppercase tracking-widest" style="color: rgba(235,235,245,0.55);">Lokasi</label>
                            <input type="text" 
                                   name="location" 
                                   id="location" 
                                   class="w-full @error('location') is-invalid @enderror"
                                   placeholder="Alamat kantor atau ruang meeting"
                                   value="{{ old('location') }}">
                            @error('location')
                                <div class="text-xs text-apple-red">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="space-y-1" id="meeting-link-field">
                            <label for="meeting_link" class="text-xs uppercase tracking-widest" style="color: rgba(235,235,245,0.55);">Meeting Link</label>
                            <input type="url" 
                                   name="meeting_link" 
                                   id="meeting_link" 
                                   class="w-full @error('meeting_link') is-invalid @enderror"
                                   placeholder="Kosongkan untuk auto-generate Jitsi"
                                   value="{{ old('meeting_link') }}">
                            <p class="text-xs" style="color: rgba(235,235,245,0.6);">
                                Kosongkan untuk auto-generate Jitsi atau tempel link Zoom/Google Meet.
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
                                @foreach($interviewers as $interviewer)
                                    <option value="{{ $interviewer->id }}">
                                        {{ $interviewer->name }} ({{ $interviewer->email }})
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
                                      placeholder="Catatan persiapan, fokus penilaian, dll.">{{ old('notes') }}</textarea>
                            @error('notes')
                                <div class="text-xs text-apple-red">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="flex flex-wrap gap-2 pt-2">
                            <button type="submit" class="btn-primary-sm">
                                <i class="fas fa-check mr-2"></i>Jadwalkan
                            </button>
                            <a href="{{ route('admin.recruitment.interviews.index') }}" class="btn-secondary-sm">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="lg:col-span-1 lg:order-2 space-y-4">
            <div class="card-elevated rounded-apple-xl p-4 space-y-3">
                <h3 class="text-base font-semibold text-white">Tips Penjadwalan</h3>
                <ul class="list-disc list-inside text-sm space-y-1" style="color: rgba(235,235,245,0.72);">
                    <li>Jadwalkan minimal 24 jam sebelumnya.</li>
                    <li>Pastikan ketersediaan pewawancara.</li>
                    <li>Video: Jitsi akan dibuat otomatis.</li>
                    <li>Pilih beberapa pewawancara (Ctrl/Cmd).</li>
                    <li>Tambahkan catatan fokus penilaian.</li>
                    <li>Notifikasi email dikirim ke kandidat.</li>
                </ul>
            </div>

            <div class="card-elevated rounded-apple-xl p-4 space-y-3">
                <h3 class="text-base font-semibold text-white">Jenis Interview</h3>
                <dl class="space-y-2 text-sm" style="color: rgba(235,235,245,0.75);">
                    <div>
                        <dt><i class="fas fa-clipboard-list text-apple-blue mr-2"></i>Preliminary</dt>
                        <dd class="ml-5 text-xs" style="color: rgba(235,235,245,0.6);">Screening awal kandidat.</dd>
                    </div>
                    <div>
                        <dt><i class="fas fa-code text-apple-green mr-2"></i>Technical</dt>
                        <dd class="ml-5 text-xs" style="color: rgba(235,235,245,0.6);">Tes kemampuan teknis.</dd>
                    </div>
                    <div>
                        <dt><i class="fas fa-user-tie text-apple-purple mr-2"></i>HR</dt>
                        <dd class="ml-5 text-xs" style="color: rgba(235,235,245,0.6);">Interview dengan HR.</dd>
                    </div>
                    <div>
                        <dt><i class="fas fa-users text-apple-orange mr-2"></i>Final</dt>
                        <dd class="ml-5 text-xs" style="color: rgba(235,235,245,0.6);">Interview akhir dengan pimpinan.</dd>
                    </div>
                </dl>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const meetingTypeSelect = document.getElementById('meeting_type');
    const locationField = document.getElementById('location-field');
    const meetingLinkField = document.getElementById('meeting-link-field');
    
    function updateFields() {
        const type = meetingTypeSelect.value;
        
        if (type === 'in-person') {
            locationField.style.display = 'block';
            meetingLinkField.style.display = 'none';
        } else if (type === 'video-call') {
            locationField.style.display = 'none';
            meetingLinkField.style.display = 'block';
        } else if (type === 'phone') {
            locationField.style.display = 'none';
            meetingLinkField.style.display = 'none';
        }
    }
    
    meetingTypeSelect.addEventListener('change', updateFields);
    updateFields(); // Initial state
});
</script>
@endpush
@endsection
