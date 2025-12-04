@extends('beta-tester.layouts.app')

@section('title', 'Dashboard Beta Tester')

@section('content')
@php
    $statusChip = [
        'gray' => ['bg' => 'rgba(148, 163, 184, 0.2)', 'color' => '#1E293B'],
        'yellow' => ['bg' => 'rgba(234, 179, 8, 0.18)', 'color' => '#92400E'],
        'blue' => ['bg' => 'rgba(37, 99, 235, 0.15)', 'color' => '#1D4ED8'],
        'green' => ['bg' => 'rgba(34, 197, 94, 0.18)', 'color' => '#065F46'],
        'red' => ['bg' => 'rgba(248, 113, 113, 0.18)', 'color' => '#B91C1C'],
    ][$betaTester->status_color] ?? ['bg' => 'rgba(148, 163, 184, 0.2)', 'color' => '#1E293B'];
@endphp

<div class="min-h-screen py-12" style="background: var(--light-bg-secondary);">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
        <!-- Header -->
        <div class="card p-6 flex flex-wrap gap-6 items-center justify-between">
            <div>
                <p class="uppercase text-xs font-semibold tracking-[0.2em]" style="color: var(--light-text-secondary);">
                    Program Beta Tester Bizmark.ID
                </p>
                <h1 class="text-3xl font-bold mt-2" style="color: var(--light-text-primary);">Dashboard Beta Tester</h1>
                <p style="color: var(--light-text-secondary);">{{ $betaTester->full_name }}</p>
            </div>
            <div class="text-right">
                <div class="text-xs uppercase tracking-widest mb-1" style="color: var(--light-text-secondary);">No. Registrasi</div>
                <div class="text-2xl font-bold" style="color: var(--apple-blue);">{{ $betaTester->registration_number }}</div>
            </div>
        </div>

        <!-- Alerts -->
        @foreach (['success' => ['icon' => 'check-circle', 'color' => 'rgba(52,199,89,0.15)', 'text' => '#166534'],
                   'info' => ['icon' => 'info-circle', 'color' => 'rgba(37,99,235,0.12)', 'text' => '#1D4ED8'],
                   'error' => ['icon' => 'exclamation-circle', 'color' => 'rgba(239,68,68,0.12)', 'text' => '#B91C1C']] as $type => $style)
            @if(session($type))
                <div class="card p-4 flex gap-3" style="background: {{ $style['color'] }}; border-color: transparent;">
                    <i class="fas fa-{{ $style['icon'] }} text-xl" style="color: {{ $style['text'] }};"></i>
                    <div>
                        <p style="color: {{ $style['text'] }};">{{ session($type) }}</p>
                    </div>
                </div>
            @endif
        @endforeach

        <!-- Layout -->
        <div class="grid lg:grid-cols-3 gap-6">
            <!-- Main -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Status -->
                <div class="card p-6 space-y-6">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-blue-500 to-blue-700 flex items-center justify-center text-white text-xl">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <h2 class="text-2xl font-bold" style="color: var(--light-text-primary);">Status Pendaftaran</h2>
                    </div>

                    <div class="flex flex-wrap gap-6 items-center justify-between">
                        <div>
                            <p class="text-sm mb-2" style="color: var(--light-text-secondary);">Status Saat Ini</p>
                            <span class="status-chip" style="background: {{ $statusChip['bg'] }}; color: {{ $statusChip['color'] }};">
                                <i class="fas fa-circle text-xs"></i>{{ $betaTester->status_label }}
                            </span>
                        </div>
                        <div class="text-center">
                            <p class="text-sm mb-1" style="color: var(--light-text-secondary);">Progress Dokumen</p>
                            <div class="text-4xl font-bold" style="color: var(--apple-blue);">{{ $betaTester->document_progress }}%</div>
                        </div>
                    </div>

                    <div>
                        <div class="h-3 rounded-full overflow-hidden" style="background: var(--light-bg-secondary); border: 1px solid var(--light-separator);">
                            <div class="h-full rounded-full transition-all duration-500"
                                 style="width: {{ $betaTester->document_progress }}%; background: linear-gradient(90deg, var(--apple-blue) 0%, var(--apple-green) 100%);"></div>
                        </div>
                    </div>

                    @if($betaTester->status === 'documents_pending')
                        <div class="p-4 rounded-xl" style="background: rgba(234,179,8,0.1); border: 1px solid rgba(234,179,8,0.3); color: #92400E;">
                            <div class="flex gap-3">
                                <i class="fas fa-exclamation-triangle mt-1"></i>
                                <div>
                                    <h3 class="font-semibold mb-1">Aksi Diperlukan</h3>
                                    <p class="text-sm">Silakan tanda tangani Pakta Integritas dan NDA untuk melanjutkan proses.</p>
                                </div>
                            </div>
                        </div>
                    @elseif($betaTester->status === 'documents_signed')
                        <div class="p-4 rounded-xl" style="background: rgba(37,99,235,0.08); border: 1px solid rgba(37,99,235,0.2); color: #1D4ED8;">
                            <div class="flex gap-3">
                                <i class="fas fa-check-circle mt-1"></i>
                                <div>
                                    <h3 class="font-semibold mb-1">Dokumen Lengkap</h3>
                                    <p class="text-sm">Terima kasih! Tim kami sedang memverifikasi data Anda.</p>
                                </div>
                            </div>
                        </div>
                    @elseif($betaTester->status === 'active')
                        <div class="p-4 rounded-xl" style="background: rgba(34,197,94,0.08); border: 1px solid rgba(34,197,94,0.2); color: #065F46;">
                            <div class="flex gap-3">
                                <i class="fas fa-trophy mt-1"></i>
                                <div>
                                    <h3 class="font-semibold mb-1">Selamat!</h3>
                                    <p class="text-sm">Anda resmi menjadi beta tester. Informasi akses GitLab segera dikirim.</p>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Documents -->
                <div class="card p-6 space-y-5">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-purple-500 to-purple-700 flex items-center justify-center text-white text-xl">
                            <i class="fas fa-file-contract"></i>
                        </div>
                        <h2 class="text-2xl font-bold" style="color: var(--light-text-primary);">Dokumen</h2>
                    </div>

                    <div class="space-y-4">
                        @foreach($documents as $document)
                        @php
                            $docStatus = [
                                'yellow' => ['bg' => 'rgba(234,179,8,0.1)', 'color' => '#92400E'],
                                'blue' => ['bg' => 'rgba(37,99,235,0.12)', 'color' => '#1D4ED8'],
                                'green' => ['bg' => 'rgba(34,197,94,0.12)', 'color' => '#065F46'],
                            ][$document->signed_status['color']] ?? ['bg' => 'rgba(148,163,184,0.15)', 'color' => '#334155'];
                        @endphp
                        <div class="card p-4">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <h3 class="font-semibold mb-1" style="color: var(--light-text-primary);">{{ $document->document_title }}</h3>
                                    <div class="flex items-center gap-2 flex-wrap text-xs">
                                        <span class="rounded-full px-3 py-1 font-semibold" style="background: {{ $docStatus['bg'] }}; color: {{ $docStatus['color'] }};">
                                            {{ $document->signed_status['label'] }}
                                        </span>
                                        @if($document->is_signed)
                                            <span style="color: var(--light-text-secondary);">
                                                <i class="fas fa-clock mr-1"></i>{{ $document->signed_at->isoFormat('DD MMM YYYY, HH:mm') }} WIB
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="mt-4 flex gap-3 flex-wrap">
                                <a href="{{ route('beta-tester.document.view', ['documentId' => $document->id, 'token' => $betaTester->registration_number]) }}"
                                   class="btn-secondary flex-1 justify-center text-sm">
                                    <i class="fas fa-eye mr-2"></i>Lihat Dokumen
                                </a>
                                @if($document->is_signed)
                                    <a href="{{ route('beta-tester.document.download', ['documentId' => $document->id, 'token' => $betaTester->registration_number]) }}"
                                       class="btn-primary text-sm">
                                        <i class="fas fa-download"></i>
                                        <span>Download PDF</span>
                                    </a>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                <!-- Recent Activities -->
                <div class="card p-6">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-green-500 to-green-700 flex items-center justify-center text-white text-xl">
                            <i class="fas fa-history"></i>
                        </div>
                        <h2 class="text-2xl font-bold" style="color: var(--light-text-primary);">Aktivitas Terakhir</h2>
                    </div>
                    @if($recentActivities->isEmpty())
                        <p class="text-center py-10" style="color: var(--light-text-secondary);">Belum ada aktivitas</p>
                    @else
                        <div class="space-y-4">
                            @foreach($recentActivities as $activity)
                                <div class="flex gap-3 pb-4 last:pb-0" style="border-bottom: 1px solid var(--light-separator);">
                                    <div class="w-2 h-2 rounded-full mt-2" style="background:
                                        @if($activity->activity_color == 'blue') #3B82F6
                                        @elseif($activity->activity_color == 'green') #10B981
                                        @elseif($activity->activity_color == 'yellow') #F59E0B
                                        @elseif($activity->activity_color == 'red') #EF4444
                                        @else #94A3B8 @endif;"></div>
                                    <div class="flex-1">
                                        <p class="text-sm mb-1" style="color: var(--light-text-primary);">{{ $activity->activity_description }}</p>
                                        <p class="text-xs" style="color: var(--light-text-secondary);">
                                            <i class="fas fa-clock mr-1"></i>{{ $activity->time_ago }} •
                                            <i class="fas fa-desktop mr-1"></i>{{ $activity->browser }}
                                        </p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <div class="card p-6">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-orange-500 to-orange-700 flex items-center justify-center text-white text-xl">
                            <i class="fas fa-user"></i>
                        </div>
                        <h2 class="text-xl font-bold" style="color: var(--light-text-primary);">Profil Saya</h2>
                    </div>
                    <dl class="space-y-4 text-sm">
                        <div>
                            <dt class="text-xs uppercase tracking-widest mb-1" style="color: var(--light-text-secondary);">Nama Lengkap</dt>
                            <dd class="font-semibold">{{ $betaTester->full_name }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs uppercase tracking-widest mb-1" style="color: var(--light-text-secondary);">Email</dt>
                            <dd class="font-semibold break-all">{{ $betaTester->email }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs uppercase tracking-widest mb-1" style="color: var(--light-text-secondary);">Universitas</dt>
                            <dd class="font-semibold">{{ $betaTester->university }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs uppercase tracking-widest mb-1" style="color: var(--light-text-secondary);">Program Studi</dt>
                            <dd class="font-semibold">{{ $betaTester->major }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs uppercase tracking-widest mb-1" style="color: var(--light-text-secondary);">Semester</dt>
                            <dd class="font-semibold">Semester {{ $betaTester->semester }}</dd>
                        </div>
                    </dl>
                </div>

                <div class="card p-6" style="background: rgba(37,99,235,0.08); border-color: transparent;">
                    <div class="flex items-center gap-3 mb-4" style="color: #1D4ED8;">
                        <i class="fas fa-question-circle text-2xl"></i>
                        <h3 class="text-lg font-bold">Butuh Bantuan?</h3>
                    </div>
                    <p class="text-sm mb-4" style="color: var(--light-text-secondary);">
                        Tim Bizmark.ID siap membantu jika Anda mengalami kendala selama program.
                    </p>
                    <a href="mailto:cs@bizmark.id" class="btn-primary w-full justify-center text-sm">
                        <i class="fas fa-envelope"></i>
                        <span>Hubungi Support</span>
                    </a>
                </div>

                @if($betaTester->status === 'active' || $betaTester->status === 'completed')
                <div class="card p-6">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-purple-500 to-purple-700 flex items-center justify-center text-white">
                            <i class="fas fa-calendar-check"></i>
                        </div>
                        <h3 class="text-lg font-bold" style="color: var(--light-text-primary);">Info Program</h3>
                    </div>
                    <dl class="space-y-4 text-sm">
                        @if($betaTester->program_start_date)
                        <div>
                            <dt class="text-xs uppercase tracking-widest mb-1" style="color: var(--light-text-secondary);">Mulai Program</dt>
                            <dd class="font-semibold">{{ $betaTester->program_start_date->isoFormat('DD MMMM YYYY') }}</dd>
                        </div>
                        @endif
                        @if($betaTester->program_end_date)
                        <div>
                            <dt class="text-xs uppercase tracking-widest mb-1" style="color: var(--light-text-secondary);">Akhir Program</dt>
                            <dd class="font-semibold">{{ $betaTester->program_end_date->isoFormat('DD MMMM YYYY') }}</dd>
                        </div>
                        @endif
                        @if($betaTester->gitlab_username)
                        <div>
                            <dt class="text-xs uppercase tracking-widest mb-1" style="color: var(--light-text-secondary);">GitLab Username</dt>
                            <dd class="font-semibold">@{{ $betaTester->gitlab_username }}</dd>
                        </div>
                        @endif
                    </dl>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
