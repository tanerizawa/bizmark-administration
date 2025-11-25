@extends('layouts.app')

@section('title', $vacancy->title . ' - Detail Lowongan')

@section('content')
@php
    $statusColor = $vacancy->status === 'open' ? 'rgba(52,199,89,0.18)' : ($vacancy->status === 'draft' ? 'rgba(255,204,0,0.18)' : 'rgba(142,142,147,0.2)');
    $statusText = $vacancy->status === 'open' ? 'Aktif' : ($vacancy->status === 'draft' ? 'Draft' : 'Ditutup');
    $statusTextColor = $vacancy->status === 'open' ? 'rgba(52,199,89,1)' : ($vacancy->status === 'draft' ? 'rgba(255,204,0,0.95)' : 'rgba(142,142,147,1)');
@endphp

<div class="max-w-7xl mx-auto space-y-5">
    {{-- Breadcrumb --}}
    <x-breadcrumb :items="[
        ['label' => 'Jobs', 'url' => route('admin.jobs.index')],
        ['label' => $vacancy->title]
    ]" />

    {{-- Header with Tabs --}}
    <section class="card-elevated rounded-apple-xl p-5 md:p-6 relative overflow-hidden">
        <div class="absolute inset-0 pointer-events-none" aria-hidden="true">
            <div class="w-60 h-60 bg-apple-blue opacity-20 blur-3xl rounded-full absolute -top-10 -right-6"></div>
            <div class="w-44 h-44 bg-apple-green opacity-15 blur-2xl rounded-full absolute bottom-0 left-6"></div>
        </div>
        <div class="relative">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-4">
                <div class="space-y-2">
                    <div class="flex flex-wrap items-center gap-2">
                        <h1 class="text-xl md:text-2xl font-semibold text-white leading-tight">{{ $vacancy->title }}</h1>
                        <span class="px-3 py-1 text-xs font-semibold rounded-full" style="background: {{ $statusColor }}; color: {{ $statusTextColor }};">
                            {{ $statusText }}
                        </span>
                    </div>
                    <p class="text-sm" style="color: rgba(235,235,245,0.7);">
                        Dipublikasikan {{ $vacancy->created_at->format('d M Y') }} · Lokasi {{ $vacancy->location }}
                    </p>
                </div>
                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('career.show', $vacancy->slug) }}" target="_blank" class="btn-secondary-sm">
                        <i class="fas fa-external-link-alt mr-2"></i>View Public
                    </a>
                </div>
            </div>

            {{-- Tab Navigation --}}
            <x-job-tabs :vacancy="$vacancy" active-tab="overview" />
        </div>
    </section>

    {{-- Stats --}}
    <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
        <div class="card-elevated rounded-apple-lg p-4 space-y-1">
            <p class="text-xs uppercase tracking-widest" style="color: rgba(10,132,255,0.9);">Total Pelamar</p>
            <p class="text-2xl font-bold text-white">{{ $vacancy->applications_count }}</p>
            <p class="text-xs" style="color: rgba(235,235,245,0.6);">Akumulasi lamaran</p>
        </div>
        <div class="card-elevated rounded-apple-lg p-4 space-y-1">
            <p class="text-xs uppercase tracking-widest" style="color: {{ $statusTextColor }};">Status Lowongan</p>
            <p class="text-2xl font-bold text-white">{{ $statusText }}</p>
            <p class="text-xs" style="color: rgba(235,235,245,0.6);">Update: {{ $vacancy->updated_at->diffForHumans() }}</p>
        </div>
        <div class="card-elevated rounded-apple-lg p-4 space-y-1">
            <p class="text-xs uppercase tracking-widest" style="color: rgba(52,199,89,0.9);">Dipublikasikan</p>
            <p class="text-2xl font-bold text-white">{{ $vacancy->created_at->format('d M Y') }}</p>
            <p class="text-xs" style="color: rgba(235,235,245,0.6);">Tanggal tayang</p>
        </div>
        <div class="card-elevated rounded-apple-lg p-4 space-y-1">
            <p class="text-xs uppercase tracking-widest" style="color: rgba(255,214,10,0.9);">Batas Waktu</p>
            <p class="text-2xl font-bold text-white">{{ $vacancy->deadline ? $vacancy->deadline->format('d M Y') : 'Tidak ada' }}</p>
            <p class="text-xs" style="color: rgba(235,235,245,0.6);">Deadline lamaran</p>
        </div>
    </section>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        <div class="lg:col-span-2 space-y-4">
            {{-- Detail Lowongan --}}
            <div class="card-elevated rounded-apple-xl p-4 space-y-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs uppercase tracking-[0.3em]" style="color: rgba(235,235,245,0.5);">Informasi Posisi</p>
                        <h3 class="text-base font-semibold text-white">Detail Lowongan</h3>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm" style="color: rgba(235,235,245,0.8);">
                    <div class="space-y-1">
                        <p class="text-xs uppercase tracking-widest" style="color: rgba(235,235,245,0.55);">Tipe Pekerjaan</p>
                        <p class="font-semibold"><i class="fas fa-briefcase mr-2"></i>{{ ucfirst($vacancy->employment_type) }}</p>
                    </div>
                    <div class="space-y-1">
                        <p class="text-xs uppercase tracking-widest" style="color: rgba(235,235,245,0.55);">Lokasi</p>
                        <p class="font-semibold"><i class="fas fa-map-marker-alt mr-2"></i>{{ $vacancy->location }}</p>
                    </div>
                    <div class="space-y-1">
                        <p class="text-xs uppercase tracking-widest" style="color: rgba(235,235,245,0.55);">Rentang Gaji</p>
                        <p class="font-semibold"><i class="fas fa-money-bill-wave mr-2"></i>
                            @if($vacancy->salary_min && $vacancy->salary_max)
                                Rp {{ number_format($vacancy->salary_min, 0, ',', '.') }} - Rp {{ number_format($vacancy->salary_max, 0, ',', '.') }}
                            @else
                                Negosiasi
                            @endif
                        </p>
                    </div>
                    <div class="space-y-1">
                        <p class="text-xs uppercase tracking-widest" style="color: rgba(235,235,245,0.55);">Pengalaman</p>
                        <p class="font-semibold"><i class="fas fa-graduation-cap mr-2"></i>{{ $vacancy->experience_years }} tahun</p>
                    </div>
                </div>
                <div class="border-t pt-4" style="border-color: rgba(58,58,60,0.6);">
                    <p class="text-xs uppercase tracking-[0.25em]" style="color: rgba(235,235,245,0.55);">Deskripsi Pekerjaan</p>
                    <div class="text-sm leading-relaxed" style="color: rgba(235,235,245,0.75);">
                        {!! nl2br(e($vacancy->description)) !!}
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <p class="text-xs uppercase tracking-[0.25em]" style="color: rgba(235,235,245,0.55);">Tanggung Jawab</p>
                        <ul class="list-disc list-inside space-y-1 text-sm" style="color: rgba(235,235,245,0.75);">
                            @foreach((is_array($vacancy->responsibilities) ? $vacancy->responsibilities : (json_decode($vacancy->responsibilities, true) ?? [])) as $responsibility)
                                <li>{{ $responsibility }}</li>
                            @endforeach
                        </ul>
                    </div>
                    <div class="space-y-2">
                        <p class="text-xs uppercase tracking-[0.25em]" style="color: rgba(235,235,245,0.55);">Kualifikasi</p>
                        <ul class="list-disc list-inside space-y-1 text-sm" style="color: rgba(235,235,245,0.75);">
                            @foreach((is_array($vacancy->qualifications) ? $vacancy->qualifications : (json_decode($vacancy->qualifications, true) ?? [])) as $qualification)
                                <li>{{ $qualification }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>

            {{-- Recent Applications --}}
            <div class="card-elevated rounded-apple-xl overflow-hidden">
                <div class="flex items-center justify-between px-4 py-3 border-b" style="border-color: rgba(58,58,60,0.6);">
                    <div>
                        <p class="text-xs uppercase tracking-[0.25em]" style="color: rgba(235,235,245,0.55);">Pelamar</p>
                        <h3 class="text-base font-semibold text-white">Pelamar Terbaru</h3>
                    </div>
                    <a href="{{ route('admin.jobs.applications', $vacancy->id) }}" class="btn-secondary-sm">
                        Lihat Semua
                    </a>
                </div>
                @if($recentApplications->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead class="bg-dark-bg-secondary border-b" style="border-color: rgba(58,58,60,0.6);">
                                <tr class="text-left text-xs uppercase tracking-wider" style="color: rgba(235,235,245,0.55);">
                                    <th class="px-4 py-2">Nama</th>
                                    <th class="px-4 py-2">Email</th>
                                    <th class="px-4 py-2">Status</th>
                                    <th class="px-4 py-2">Tanggal</th>
                                    <th class="px-4 py-2 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y" style="border-color: rgba(58,58,60,0.6); color: rgba(235,235,245,0.9);">
                                @foreach($recentApplications as $application)
                                    @php
                                        $appStatusColor = $application->status === 'pending' ? 'rgba(255,204,0,0.2)' : ($application->status === 'accepted' ? 'rgba(52,199,89,0.2)' : 'rgba(255,59,48,0.2)');
                                        $appStatusText = ucfirst($application->status);
                                    @endphp
                                    <tr class="hover:bg-white/5 transition-colors">
                                        <td class="px-4 py-2 font-semibold">{{ $application->full_name }}</td>
                                        <td class="px-4 py-2">{{ $application->email }}</td>
                                        <td class="px-4 py-2">
                                            <span class="px-2 py-1 rounded-full text-xs font-semibold" style="background: {{ $appStatusColor }};">
                                                {{ $appStatusText }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-2">{{ $application->created_at->format('d M Y') }}</td>
                                        <td class="px-4 py-2 text-center">
                                            <a href="{{ route('admin.recruitment.pipeline.show', $application->id) }}" class="btn-secondary-sm">
                                                <i class="fas fa-stream mr-1"></i>Pipeline
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="p-6 text-center" style="color: rgba(235,235,245,0.6);">
                        <i class="fas fa-inbox text-3xl mb-2"></i>
                        <p class="mb-0">Belum ada pelamar untuk lowongan ini</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="space-y-4">
            <div class="card-elevated rounded-apple-xl overflow-hidden">
                <div class="px-4 py-3 border-b" style="border-color: rgba(58,58,60,0.6);">
                    <h3 class="text-base font-semibold text-white">Aksi Cepat</h3>
                </div>
                <div class="divide-y" style="border-color: rgba(58,58,60,0.6);">
                    <a href="{{ route('admin.jobs.applications', $vacancy->id) }}" class="flex items-center gap-2 px-4 py-3 hover:bg-white/5 transition-apple">
                        <i class="fas fa-users text-apple-blue"></i>
                        <span class="text-sm">View All Applications</span>
                    </a>
                    <a href="{{ route('admin.jobs.pipeline', $vacancy->id) }}" class="flex items-center gap-2 px-4 py-3 hover:bg-white/5 transition-apple">
                        <i class="fas fa-stream text-apple-purple"></i>
                        <span class="text-sm">Recruitment Pipeline</span>
                    </a>
                    <a href="{{ route('admin.jobs.tests', $vacancy->id) }}" class="flex items-center gap-2 px-4 py-3 hover:bg-white/5 transition-apple">
                        <i class="fas fa-clipboard-check text-yellow-500"></i>
                        <span class="text-sm">Assign & Track Tests</span>
                    </a>
                    <a href="{{ route('admin.jobs.interviews', $vacancy->id) }}" class="flex items-center gap-2 px-4 py-3 hover:bg-white/5 transition-apple">
                        <i class="fas fa-calendar-alt text-apple-green"></i>
                        <span class="text-sm">View Interviews</span>
                    </a>
                    <a href="{{ route('admin.recruitment.interviews.create', ['vacancy_id' => $vacancy->id]) }}" class="flex items-center gap-2 px-4 py-3 hover:bg-white/5 transition-apple">
                        <i class="fas fa-calendar-plus text-apple-orange"></i>
                        <span class="text-sm">Schedule New Interview</span>
                    </a>
                </div>
            </div>

            <div class="card-elevated rounded-apple-xl p-4 space-y-3">
                <div class="flex items-center justify-between">
                    <h3 class="text-base font-semibold text-white">Status Lowongan</h3>
                    <span class="px-2 py-1 rounded-full text-xs font-semibold" style="background: {{ $statusColor }}; color: {{ $statusTextColor }};">{{ $statusText }}</span>
                </div>
                <form action="{{ route('admin.jobs.update', $vacancy->id) }}" method="POST" class="space-y-2">
                    @csrf
                    @method('PUT')
                    <label class="text-xs uppercase tracking-widest" style="color: rgba(235,235,245,0.55);">Ubah Status</label>
                    <select name="status" class="w-full" onchange="this.form.submit()">
                        <option value="draft" {{ $vacancy->status === 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="open" {{ $vacancy->status === 'open' ? 'selected' : '' }}>Aktif (Buka)</option>
                        <option value="closed" {{ $vacancy->status === 'closed' ? 'selected' : '' }}>Ditutup</option>
                    </select>
                    <p class="text-xs" style="color: rgba(235,235,245,0.6);">Perubahan disimpan otomatis.</p>
                    <input type="hidden" name="title" value="{{ $vacancy->title }}">
                    <input type="hidden" name="description" value="{{ $vacancy->description }}">
                    <input type="hidden" name="employment_type" value="{{ $vacancy->employment_type }}">
                    <input type="hidden" name="location" value="{{ $vacancy->location }}">
                </form>
                <div class="pt-2 border-t" style="border-color: rgba(58,58,60,0.6);">
                    <div class="flex justify-between text-xs" style="color: rgba(235,235,245,0.6);">
                        <span>Dibuat</span>
                        <span>{{ $vacancy->created_at->format('d M Y H:i') }}</span>
                    </div>
                    <div class="flex justify-between text-xs" style="color: rgba(235,235,245,0.6);">
                        <span>Terakhir Update</span>
                        <span>{{ $vacancy->updated_at->format('d M Y H:i') }}</span>
                    </div>
                </div>
            </div>

            <div class="card-elevated rounded-apple-xl p-4 space-y-3">
                <h3 class="text-base font-semibold text-white">Statistik</h3>
                @php
                    $pendingCount = $vacancy->applications()->where('status', 'pending')->count();
                    $reviewedCount = $vacancy->applications()->where('status', 'reviewed')->count();
                    $acceptedCount = $vacancy->applications()->where('status', 'accepted')->count();
                    $rejectedCount = $vacancy->applications()->where('status', 'rejected')->count();
                @endphp
                <div class="space-y-3">
                    <div>
                        <div class="flex justify-between text-sm" style="color: rgba(235,235,245,0.75);">
                            <span>Total Pelamar</span>
                            <span class="font-semibold">{{ $vacancy->applications_count }}</span>
                        </div>
                        <div class="h-1.5 rounded-full bg-white/10 mt-1">
                            <div class="h-full rounded-full bg-apple-blue" style="width: 100%"></div>
                        </div>
                    </div>
                    <div>
                        <div class="flex justify-between text-sm" style="color: rgba(235,235,245,0.75);">
                            <span>Pending</span>
                            <span class="font-semibold">{{ $pendingCount }}</span>
                        </div>
                        <div class="h-1.5 rounded-full bg-white/10 mt-1">
                            <div class="h-full rounded-full bg-apple-orange" style="width: {{ $vacancy->applications_count ? ($pendingCount / $vacancy->applications_count * 100) : 0 }}%"></div>
                        </div>
                    </div>
                    <div>
                        <div class="flex justify-between text-sm" style="color: rgba(235,235,245,0.75);">
                            <span>Reviewed</span>
                            <span class="font-semibold">{{ $reviewedCount }}</span>
                        </div>
                        <div class="h-1.5 rounded-full bg-white/10 mt-1">
                            <div class="h-full rounded-full bg-apple-blue" style="width: {{ $vacancy->applications_count ? ($reviewedCount / $vacancy->applications_count * 100) : 0 }}%"></div>
                        </div>
                    </div>
                    <div>
                        <div class="flex justify-between text-sm" style="color: rgba(235,235,245,0.75);">
                            <span>Diterima</span>
                            <span class="font-semibold">{{ $acceptedCount }}</span>
                        </div>
                        <div class="h-1.5 rounded-full bg-white/10 mt-1">
                            <div class="h-full rounded-full bg-apple-green" style="width: {{ $vacancy->applications_count ? ($acceptedCount / $vacancy->applications_count * 100) : 0 }}%"></div>
                        </div>
                    </div>
                    <div>
                        <div class="flex justify-between text-sm" style="color: rgba(235,235,245,0.75);">
                            <span>Ditolak</span>
                            <span class="font-semibold">{{ $rejectedCount }}</span>
                        </div>
                        <div class="h-1.5 rounded-full bg-white/10 mt-1">
                            <div class="h-full rounded-full bg-apple-red" style="width: {{ $vacancy->applications_count ? ($rejectedCount / $vacancy->applications_count * 100) : 0 }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
