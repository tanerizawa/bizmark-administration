@extends('layouts.app')

@section('title', 'Test Template Detail')

@section('content')
<div class="recruitment-shell max-w-7xl mx-auto space-y-5">
    {{-- Header --}}
    <section class="card-elevated rounded-apple-xl p-5 md:p-6 relative overflow-hidden">
        <div class="absolute inset-0 pointer-events-none" aria-hidden="true">
            <div class="w-72 h-72 bg-apple-blue opacity-25 blur-3xl rounded-full absolute -top-14 -right-10"></div>
            <div class="w-48 h-48 bg-apple-purple opacity-20 blur-2xl rounded-full absolute bottom-0 left-8"></div>
        </div>
        <div class="relative space-y-4">
            <div class="flex items-center gap-2 text-xs uppercase tracking-[0.35em]" style="color: rgba(235,235,245,0.6);">
                <a href="{{ route('admin.recruitment.index') }}" class="inline-flex items-center gap-2 hover:text-white transition-apple">
                    <i class="fas fa-arrow-left text-xs"></i> Rekrutmen
                </a>
                <span class="text-dark-text-tertiary">/</span>
                <a href="{{ route('admin.recruitment.tests.index') }}" class="hover:text-white transition-apple">
                    Test
                </a>
                <span class="text-dark-text-tertiary">/</span>
                <span>Detail</span>
            </div>
            
            <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4">
                <div class="space-y-2.5">
                    <h1 class="text-2xl font-semibold text-white leading-tight">{{ $test->title }}</h1>
                    <p class="text-sm" style="color: rgba(235,235,245,0.7);">
                        {{ $test->description ?? 'Template tes untuk proses rekrutmen' }}
                    </p>
                    <div class="flex flex-wrap gap-2 mt-3">
                        @php
                            $typeColors = [
                                'psychology' => ['bg' => 'rgba(10,132,255,0.2)', 'text' => 'rgba(10,132,255,1)'],
                                'psychometric' => ['bg' => 'rgba(255,214,10,0.2)', 'text' => 'rgba(255,214,10,1)'],
                                'technical' => ['bg' => 'rgba(255,69,58,0.2)', 'text' => 'rgba(255,69,58,1)'],
                                'aptitude' => ['bg' => 'rgba(52,199,89,0.2)', 'text' => 'rgba(52,199,89,1)'],
                                'personality' => ['bg' => 'rgba(191,90,242,0.2)', 'text' => 'rgba(191,90,242,1)'],
                            ];
                            $colors = $typeColors[$test->test_type] ?? ['bg' => 'rgba(255,255,255,0.1)', 'text' => 'rgba(255,255,255,0.8)'];
                        @endphp
                        <span class="px-3 py-1 rounded-full text-xs font-semibold" style="background: {{ $colors['bg'] }}; color: {{ $colors['text'] }};">
                            <i class="fas fa-tag mr-1"></i>{{ ucfirst($test->test_type) }}
                        </span>
                        <span class="px-3 py-1 rounded-full text-xs font-semibold" style="background: {{ $test->is_active ? 'rgba(52,199,89,0.2)' : 'rgba(142,142,147,0.2)' }}; color: {{ $test->is_active ? 'rgba(52,199,89,1)' : 'rgba(142,142,147,1)' }};">
                            <i class="fas fa-circle mr-1 text-[0.5rem]"></i>{{ $test->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </div>
                </div>
                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('admin.recruitment.tests.edit', $test) }}" class="btn-primary">
                        <i class="fas fa-edit mr-2"></i>Edit Template
                    </a>
                    <a href="{{ route('admin.recruitment.tests.index') }}" class="btn-secondary">
                        <i class="fas fa-arrow-left mr-2"></i>Kembali
                    </a>
                </div>
            </div>
        </div>
    </section>

    {{-- Statistics --}}
    <section class="grid grid-cols-2 lg:grid-cols-4 gap-3">
        <div class="card-elevated rounded-apple-lg p-4 space-y-2">
            <div class="flex items-center justify-between">
                <p class="text-xs uppercase tracking-widest" style="color: rgba(10,132,255,0.9);">Total Sesi</p>
                <i class="fas fa-users text-lg" style="color: rgba(10,132,255,0.5);"></i>
            </div>
            <p class="text-3xl font-bold text-white">{{ $test->test_sessions_count }}</p>
            <p class="text-xs" style="color: rgba(235,235,245,0.6);">Kandidat mengikuti</p>
        </div>
        
        <div class="card-elevated rounded-apple-lg p-4 space-y-2">
            <div class="flex items-center justify-between">
                <p class="text-xs uppercase tracking-widest" style="color: rgba(52,199,89,0.9);">Avg. Score</p>
                <i class="fas fa-chart-line text-lg" style="color: rgba(52,199,89,0.5);"></i>
            </div>
            <p class="text-3xl font-bold text-white">{{ number_format($statistics['average_score'] ?? 0, 1) }}</p>
            <p class="text-xs" style="color: rgba(235,235,245,0.6);">Skor rata-rata</p>
        </div>
        
        <div class="card-elevated rounded-apple-lg p-4 space-y-2">
            <div class="flex items-center justify-between">
                <p class="text-xs uppercase tracking-widest" style="color: rgba(255,149,0,0.9);">Pass Rate</p>
                <i class="fas fa-percentage text-lg" style="color: rgba(255,149,0,0.5);"></i>
            </div>
            <p class="text-3xl font-bold text-white">{{ number_format($statistics['pass_rate'] ?? 0, 1) }}%</p>
            <p class="text-xs" style="color: rgba(235,235,245,0.6);">Tingkat kelulusan</p>
        </div>
        
        <div class="card-elevated rounded-apple-lg p-4 space-y-2">
            <div class="flex items-center justify-between">
                <p class="text-xs uppercase tracking-widest" style="color: rgba(191,90,242,0.9);">Avg. Duration</p>
                <i class="fas fa-clock text-lg" style="color: rgba(191,90,242,0.5);"></i>
            </div>
            <p class="text-3xl font-bold text-white">{{ number_format($statistics['average_duration'] ?? 0, 0) }}</p>
            <p class="text-xs" style="color: rgba(235,235,245,0.6);">Menit rata-rata</p>
        </div>
    </section>

    {{-- Test Details --}}
    <section class="card-elevated rounded-apple-xl p-6 space-y-5">
        <div class="border-b border-white/10 pb-4">
            <h3 class="text-lg font-semibold text-white">Informasi Template</h3>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-3">
                <div class="flex justify-between py-3 border-b border-white/5">
                    <span class="text-sm" style="color: rgba(235,235,245,0.6);">Durasi Tes</span>
                    <span class="text-sm font-semibold text-white">{{ $test->duration_minutes }} menit</span>
                </div>
                <div class="flex justify-between py-3 border-b border-white/5">
                    <span class="text-sm" style="color: rgba(235,235,245,0.6);">Passing Score</span>
                    <span class="text-sm font-semibold text-white">{{ $test->passing_score }}%</span>
                </div>
                <div class="flex justify-between py-3 border-b border-white/5">
                    <span class="text-sm" style="color: rgba(235,235,245,0.6);">Total Pertanyaan</span>
                    <span class="text-sm font-semibold text-white">{{ $test->total_questions ?? 0 }} pertanyaan</span>
                </div>
            </div>
            
            <div class="space-y-3">
                <div class="flex justify-between py-3 border-b border-white/5">
                    <span class="text-sm" style="color: rgba(235,235,245,0.6);">Dibuat</span>
                    <span class="text-sm font-semibold text-white">{{ $test->created_at->format('d M Y H:i') }}</span>
                </div>
                <div class="flex justify-between py-3 border-b border-white/5">
                    <span class="text-sm" style="color: rgba(235,235,245,0.6);">Terakhir Update</span>
                    <span class="text-sm font-semibold text-white">{{ $test->updated_at->format('d M Y H:i') }}</span>
                </div>
                <div class="flex justify-between py-3 border-b border-white/5">
                    <span class="text-sm" style="color: rgba(235,235,245,0.6);">Sesi Selesai</span>
                    <span class="text-sm font-semibold text-white">{{ $test->completed_sessions_count }} sesi</span>
                </div>
            </div>
        </div>

        {{-- Template File (Document Editing) --}}
        @if($test->test_type === 'document-editing' && $test->template_file_path)
            <div class="mt-6 rounded-apple overflow-hidden" style="background: rgba(28,28,30,0.5); border: 1px solid rgba(84,84,88,0.35);">
                <div class="px-6 py-4 border-b border-white/10" style="background: rgba(28,28,30,0.4);">
                    <h4 class="text-sm font-semibold text-white">Template Dokumen</h4>
                </div>
                <div class="px-6 py-4">
                    <div class="flex items-center justify-between p-4 rounded-lg" style="background: rgba(28,28,30,0.4); border: 1px solid rgba(84,84,88,0.3);">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg flex items-center justify-center" style="background: rgba(10,132,255,0.15);">
                                <i class="fas fa-file-word text-lg" style="color: rgba(10,132,255,0.9);"></i>
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-white">{{ basename($test->template_file_path) }}</p>
                                <p class="text-xs mt-0.5" style="color: rgba(235,235,245,0.6);">
                                    Dokumen Template • 
                                    @if(Storage::disk('private')->exists($test->template_file_path))
                                        {{ number_format(Storage::disk('private')->size($test->template_file_path) / 1024, 2) }} KB
                                    @else
                                        File tidak ditemukan
                                    @endif
                                </p>
                            </div>
                        </div>
                        <a href="{{ route('admin.recruitment.tests.download-template', $test) }}" 
                           class="btn-secondary text-sm">
                            <i class="fas fa-download mr-2"></i>Download
                        </a>
                    </div>
                </div>
            </div>
        @endif

        @if($test->instructions)
            <div class="mt-6 rounded-apple overflow-hidden" style="background: rgba(28,28,30,0.5); border: 1px solid rgba(84,84,88,0.35);">
                <div class="px-6 py-4 border-b border-white/10" style="background: rgba(28,28,30,0.4);">
                    <h4 class="text-sm font-semibold text-white">Instruksi Tes</h4>
                </div>
                <div class="px-6 py-5">
                    <style>
                        .instructions-content {
                            line-height: 1.75;
                            font-size: 0.9375rem;
                        }
                        .instructions-content .section-header {
                            color: #fff;
                            font-size: 0.8125rem;
                            font-weight: 600;
                            letter-spacing: 0.05em;
                            text-transform: uppercase;
                            margin-top: 2rem;
                            margin-bottom: 1rem;
                            padding-bottom: 0.5rem;
                            border-bottom: 1px solid rgba(255,255,255,0.1);
                            display: flex;
                            align-items: center;
                            gap: 0.5rem;
                        }
                        .instructions-content .section-header:first-child {
                            margin-top: 0;
                        }
                        .instructions-content .section-header::before {
                            content: '';
                            width: 3px;
                            height: 14px;
                            background: linear-gradient(180deg, rgba(255,214,10,0.8) 0%, rgba(255,214,10,0.3) 100%);
                            border-radius: 2px;
                        }
                        .instructions-content .task-number {
                            display: inline-flex;
                            align-items: center;
                            justify-content: center;
                            width: 24px;
                            height: 24px;
                            background: rgba(255,214,10,0.15);
                            border: 1px solid rgba(255,214,10,0.3);
                            border-radius: 6px;
                            color: rgba(255,214,10,0.9);
                            font-size: 0.8125rem;
                            font-weight: 600;
                            margin-right: 0.5rem;
                        }
                        .instructions-content .task-title {
                            color: rgba(255,255,255,0.95);
                            font-weight: 600;
                            font-size: 0.9375rem;
                            margin-top: 1.25rem;
                            margin-bottom: 0.625rem;
                            display: flex;
                            align-items: center;
                        }
                        .instructions-content .task-points {
                            color: rgba(255,214,10,0.8);
                            font-size: 0.8125rem;
                            font-weight: 500;
                            margin-left: 0.375rem;
                        }
                        .instructions-content ul {
                            margin-top: 0.5rem;
                            margin-bottom: 1rem;
                            padding-left: 0;
                            list-style: none;
                        }
                        .instructions-content li {
                            margin-bottom: 0.5rem;
                            color: rgba(235,235,245,0.8);
                            padding-left: 1.5rem;
                            position: relative;
                        }
                        .instructions-content li::before {
                            content: '';
                            position: absolute;
                            left: 0.375rem;
                            top: 0.625rem;
                            width: 4px;
                            height: 4px;
                            background: rgba(255,214,10,0.5);
                            border-radius: 50%;
                        }
                        .instructions-content p {
                            margin-bottom: 0.875rem;
                            color: rgba(235,235,245,0.8);
                        }
                        .instructions-content .rule-group {
                            margin-top: 1rem;
                            margin-bottom: 1rem;
                        }
                        .instructions-content .rule-label {
                            display: inline-flex;
                            align-items: center;
                            gap: 0.375rem;
                            font-weight: 600;
                            font-size: 0.875rem;
                            margin-bottom: 0.5rem;
                            padding: 0.25rem 0.625rem;
                            border-radius: 6px;
                        }
                        .instructions-content .rule-label.allowed {
                            color: rgba(52,199,89,0.95);
                            background: rgba(52,199,89,0.1);
                            border: 1px solid rgba(52,199,89,0.2);
                        }
                        .instructions-content .rule-label.forbidden {
                            color: rgba(255,69,58,0.95);
                            background: rgba(255,69,58,0.1);
                            border: 1px solid rgba(255,69,58,0.2);
                        }
                        .instructions-content .rule-label.deadline {
                            color: rgba(191,90,242,0.95);
                            background: rgba(191,90,242,0.1);
                            border: 1px solid rgba(191,90,242,0.2);
                        }
                        .instructions-content .standard-item {
                            background: rgba(28,28,30,0.3);
                            padding: 0.5rem 0.875rem;
                            border-radius: 6px;
                            margin-bottom: 0.375rem;
                            border-left: 2px solid rgba(84,84,88,0.5);
                            font-size: 0.875rem;
                            color: rgba(235,235,245,0.85);
                        }
                        .instructions-content .highlight-box {
                            background: linear-gradient(135deg, rgba(255,214,10,0.08) 0%, rgba(255,214,10,0.03) 100%);
                            border: 1px solid rgba(255,214,10,0.2);
                            padding: 1rem 1.25rem;
                            border-radius: 8px;
                            margin-top: 1.5rem;
                            color: rgba(235,235,245,0.9);
                            font-size: 0.9375rem;
                            line-height: 1.6;
                        }
                    </style>
                    <div class="instructions-content">
                        {!! nl2br(e($test->instructions)) !!}
                    </div>
                </div>
            </div>
        @endif
    </section>

    {{-- Assign Test to Candidate --}}
    <section class="card-elevated rounded-apple-xl overflow-hidden">
        <div class="px-6 py-4 border-b border-white/10">
            <h3 class="text-lg font-semibold text-white flex items-center gap-2">
                <i class="fas fa-user-plus" style="color: rgba(52,199,89,1);"></i>
                Assign Test to Candidate
            </h3>
            <p class="text-xs mt-1" style="color: rgba(235,235,245,0.6);">
                Berikan tes ini kepada kandidat yang belum memiliki sesi aktif
            </p>
        </div>

        <div class="p-6">
            @if(isset($availableCandidates) && $availableCandidates->count() > 0)
                <form action="{{ route('admin.recruitment.tests.assign') }}" method="POST" class="space-y-4">
                    @csrf
                    <input type="hidden" name="test_template_id" value="{{ $test->id }}">
                    
                    <div>
                        <label class="block text-sm font-medium text-white mb-2">
                            Select Candidate <span class="text-red-500">*</span>
                        </label>
                        <select name="job_application_id" 
                                class="w-full px-4 py-3 rounded-lg text-white transition-all"
                                style="background: rgba(28,28,30,0.6); border: 1px solid rgba(84,84,88,0.35);"
                                required>
                            <option value="">-- Pilih Kandidat --</option>
                            @foreach($availableCandidates as $candidate)
                                <option value="{{ $candidate->id }}">
                                    {{ $candidate->full_name }} - {{ $candidate->jobVacancy->title ?? 'No Position' }} 
                                    ({{ $candidate->email }})
                                </option>
                            @endforeach
                        </select>
                        @error('job_application_id')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-white mb-2">
                            Expiration Date (Optional)
                        </label>
                        <input type="datetime-local" 
                               name="expires_at" 
                               class="w-full px-4 py-3 rounded-lg text-white transition-all"
                               style="background: rgba(28,28,30,0.6); border: 1px solid rgba(84,84,88,0.35);"
                               min="{{ now()->addHour()->format('Y-m-d\TH:i') }}"
                               value="{{ now()->addDays(7)->format('Y-m-d\TH:i') }}">
                        <p class="text-xs mt-1" style="color: rgba(235,235,245,0.5);">
                            Default: 7 days from now
                        </p>
                    </div>
                    
                    <div class="flex items-center justify-end gap-3 pt-2">
                        <button type="submit" class="btn-primary">
                            <i class="fas fa-paper-plane mr-2"></i>Assign Test & Send Email
                        </button>
                    </div>
                </form>
            @else
                <div class="text-center py-8">
                    <i class="fas fa-inbox text-4xl mb-3" style="color: rgba(142,142,147,0.5);"></i>
                    <p class="text-sm" style="color: rgba(235,235,245,0.6);">
                        Tidak ada kandidat yang tersedia untuk di-assign. 
                        Semua kandidat sudah memiliki sesi aktif untuk tes ini.
                    </p>
                </div>
            @endif
        </div>
    </section>

    {{-- Recent Sessions --}}
    <section class="card-elevated rounded-apple-xl overflow-hidden">
        <div class="px-6 py-4 border-b border-white/10">
            <h3 class="text-lg font-semibold text-white">All Test Sessions</h3>
            <p class="text-xs mt-1" style="color: rgba(235,235,245,0.6);">
                Semua sesi tes dengan template ini ({{ $recentSessions->count() }} total)
            </p>
        </div>
        
        @if($recentSessions->count())
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead style="background: rgba(28,28,30,0.45);">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs uppercase tracking-widest" style="color: rgba(235,235,245,0.6);">Kandidat</th>
                            <th class="px-6 py-4 text-left text-xs uppercase tracking-widest" style="color: rgba(235,235,245,0.6);">Status</th>
                            <th class="px-6 py-4 text-left text-xs uppercase tracking-widest" style="color: rgba(235,235,245,0.6);">Score</th>
                            <th class="px-6 py-4 text-left text-xs uppercase tracking-widest" style="color: rgba(235,235,245,0.6);">Durasi</th>
                            <th class="px-6 py-4 text-left text-xs uppercase tracking-widest" style="color: rgba(235,235,245,0.6);">Tanggal</th>
                            <th class="px-6 py-4 text-right text-xs uppercase tracking-widest" style="color: rgba(235,235,245,0.6);">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentSessions as $session)
                            @php
                                $statusColors = [
                                    'pending' => ['bg' => 'rgba(255,214,10,0.2)', 'text' => 'rgba(255,214,10,1)'],
                                    'in_progress' => ['bg' => 'rgba(10,132,255,0.2)', 'text' => 'rgba(10,132,255,1)'],
                                    'completed' => ['bg' => 'rgba(52,199,89,0.2)', 'text' => 'rgba(52,199,89,1)'],
                                    'expired' => ['bg' => 'rgba(142,142,147,0.2)', 'text' => 'rgba(142,142,147,1)'],
                                ];
                                $statusColor = $statusColors[$session->status] ?? ['bg' => 'rgba(255,255,255,0.1)', 'text' => 'rgba(255,255,255,0.8)'];
                                
                                $duration = null;
                                if ($session->started_at && $session->completed_at) {
                                    $duration = $session->started_at->diffInMinutes($session->completed_at);
                                }
                            @endphp
                            <tr class="border-b border-white/5 hover:bg-white/5 transition">
                                <td class="px-6 py-4">
                                    @if($session->jobApplication)
                                        <div class="flex items-center gap-3">
                                            <div class="w-9 h-9 rounded-full bg-gradient-to-br from-apple-blue to-apple-purple flex items-center justify-center text-white text-sm font-semibold">
                                                {{ strtoupper(substr($session->jobApplication->full_name, 0, 1)) }}
                                            </div>
                                            <div>
                                                <p class="text-sm font-semibold text-white">{{ $session->jobApplication->full_name }}</p>
                                                <p class="text-xs" style="color: rgba(235,235,245,0.6);">{{ $session->jobApplication->email }}</p>
                                            </div>
                                        </div>
                                    @else
                                        <div class="flex items-center gap-3">
                                            <div class="w-9 h-9 rounded-full bg-gradient-to-br from-gray-500 to-gray-600 flex items-center justify-center text-white text-sm font-semibold">
                                                <i class="fas fa-user"></i>
                                            </div>
                                            <div>
                                                <p class="text-sm font-semibold" style="color: rgba(235,235,245,0.5);">Data tidak tersedia</p>
                                                <p class="text-xs" style="color: rgba(235,235,245,0.4);">Kandidat telah dihapus</p>
                                            </div>
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-3 py-1 rounded-full text-xs font-semibold" style="background: {{ $statusColor['bg'] }}; color: {{ $statusColor['text'] }};">
                                        {{ ucfirst(str_replace('_', ' ', $session->status)) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    @if($session->status === 'completed' && $session->score !== null)
                                        <span class="text-sm font-semibold text-white">{{ number_format($session->score, 1) }}</span>
                                    @else
                                        <span class="text-sm" style="color: rgba(235,235,245,0.5);">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    @if($duration)
                                        <span class="text-sm text-white">{{ $duration }} menit</span>
                                    @else
                                        <span class="text-sm" style="color: rgba(235,235,245,0.5);">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm text-white">
                                    {{ $session->created_at->format('d M Y, H:i') }}
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        @if($session->status === 'completed')
                                            <a href="{{ route('admin.recruitment.tests.sessions.results', $session) }}" class="btn-secondary-sm">
                                                <i class="fas fa-chart-bar mr-1"></i>Hasil
                                            </a>
                                        @else
                                            <a href="{{ route('candidate.test.show', $session->session_token) }}" 
                                               target="_blank"
                                               class="btn-secondary-sm"
                                               title="Lihat Link Test">
                                                <i class="fas fa-external-link-alt"></i>
                                            </a>
                                        @endif
                                        
                                        @if(in_array($session->status, ['pending', 'not-started', 'expired']))
                                            <form action="{{ route('admin.recruitment.tests.sessions.cancel', $session) }}" 
                                                  method="POST" 
                                                  class="inline"
                                                  onsubmit="return confirm('Apakah Anda yakin ingin membatalkan test session ini? Email notifikasi tidak akan dikirim ulang.')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="px-3 py-1.5 rounded-lg text-xs font-semibold transition-all"
                                                        style="background: rgba(255,59,48,0.15); color: rgba(255,59,48,1);"
                                                        onmouseover="this.style.background='rgba(255,59,48,0.25)'"
                                                        onmouseout="this.style.background='rgba(255,59,48,0.15)'"
                                                        title="Batalkan Test">
                                                    <i class="fas fa-times mr-1"></i>Batalkan
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-12 space-y-3" style="color: rgba(235,235,245,0.7);">
                <i class="fas fa-clipboard-check text-4xl"></i>
                <p class="text-sm">Belum ada sesi tes dengan template ini.</p>
            </div>
        @endif
    </section>
</div>
@endsection
