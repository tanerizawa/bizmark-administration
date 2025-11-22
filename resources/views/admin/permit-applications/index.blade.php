@extends('layouts.app')

@section('title', 'Manajemen Permohonan Izin')
@section('page-title', 'Manajemen Permohonan Izin')

@section('content')
@php
    $totalApplications = $stats['total'] ?? 0;
    $reviewBacklog = ($stats['submitted'] ?? 0) + ($stats['under_review'] ?? 0);
    $quoted = $stats['quoted'] ?? 0;
    $inProgress = $stats['in_progress'] ?? 0;
    $completed = $stats['completed'] ?? 0;
    $activePipeline = max(0, $totalApplications - $completed);
    $completionRate = $totalApplications > 0 ? round(($completed / $totalApplications) * 100) : 0;

    $statusStyles = [
        'draft' => ['bg' => 'rgba(142,142,147,0.15)', 'text' => 'rgba(142,142,147,1)', 'border' => 'rgba(142,142,147,0.3)'],
        'submitted' => ['bg' => 'rgba(10,132,255,0.15)', 'text' => 'rgba(10,132,255,1)', 'border' => 'rgba(10,132,255,0.3)'],
        'under_review' => ['bg' => 'rgba(255,204,0,0.15)', 'text' => 'rgba(255,204,0,1)', 'border' => 'rgba(255,204,0,0.3)'],
        'document_incomplete' => ['bg' => 'rgba(255,59,48,0.15)', 'text' => 'rgba(255,59,48,1)', 'border' => 'rgba(255,59,48,0.3)'],
        'quoted' => ['bg' => 'rgba(175,82,222,0.15)', 'text' => 'rgba(175,82,222,1)', 'border' => 'rgba(175,82,222,0.3)'],
        'quotation_accepted' => ['bg' => 'rgba(48,209,88,0.15)', 'text' => 'rgba(48,209,88,1)', 'border' => 'rgba(48,209,88,0.3)'],
        'payment_pending' => ['bg' => 'rgba(255,149,0,0.15)', 'text' => 'rgba(255,149,0,1)', 'border' => 'rgba(255,149,0,0.3)'],
        'payment_verified' => ['bg' => 'rgba(52,199,89,0.15)', 'text' => 'rgba(52,199,89,1)', 'border' => 'rgba(52,199,89,0.3)'],
        'in_progress' => ['bg' => 'rgba(10,132,255,0.15)', 'text' => 'rgba(10,132,255,1)', 'border' => 'rgba(10,132,255,0.3)'],
        'completed' => ['bg' => 'rgba(52,199,89,0.15)', 'text' => 'rgba(52,199,89,1)', 'border' => 'rgba(52,199,89,0.3)'],
        'cancelled' => ['bg' => 'rgba(142,142,147,0.15)', 'text' => 'rgba(142,142,147,1)', 'border' => 'rgba(142,142,147,0.3)'],
    ];

@endphp

@section('content')
    {{-- Hero / overview --}}
    <section class="card-elevated rounded-apple-xl p-5 md:p-6 relative overflow-hidden mb-6">
        <div class="absolute inset-0 pointer-events-none" aria-hidden="true">
            <div class="w-72 h-72 bg-apple-blue opacity-30 blur-3xl rounded-full absolute -top-16 -right-10"></div>
            <div class="w-48 h-48 bg-apple-green opacity-20 blur-2xl rounded-full absolute bottom-0 left-10"></div>
        </div>
        <div class="relative space-y-6">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-5">
                <div class="space-y-2.5 max-w-3xl">
                    <p class="text-xs uppercase tracking-[0.4em]" style="color: rgba(235,235,245,0.5);">Manajemen Permohonan</p>
                    <h1 class="text-2xl md:text-3xl font-bold" style="color: #FFFFFF;">
                        Database Lengkap Permohonan Izin
                    </h1>
                    <p class="text-sm md:text-base" style="color: rgba(235,235,245,0.75);">
                        Pantau permohonan baru, tindak lanjuti dokumen yang belum lengkap, dan lacak progres setiap pengajuan dalam satu tampilan.
                    </p>
                </div>
                <div class="text-sm space-y-2.5" style="color: rgba(235,235,245,0.65);">
                    <p><i class="fas fa-database mr-2"></i>{{ $totalApplications }} total permohonan</p>
                    <p><i class="fas fa-percentage mr-2"></i>Tingkat penyelesaian {{ $completionRate }}%</p>
                    <a href="{{ route('admin.permit-dashboard') }}" class="btn-secondary-sm">
                        <i class="fas fa-chart-network mr-2"></i>Kembali ke Dashboard
                    </a>
                </div>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3 md:gap-4">
                <div class="rounded-apple-lg p-4" style="background: rgba(255,59,48,0.08);">
                    <p class="text-xs uppercase tracking-widest" style="color: rgba(255,59,48,0.8);">Antrian Tinjauan</p>
                    <h2 class="text-2xl font-bold text-white mt-1.5">{{ $reviewBacklog }}</h2>
                    <p class="text-xs" style="color: rgba(235,235,245,0.65);">Menunggu verifikasi</p>
                </div>
                <div class="rounded-apple-lg p-4" style="background: rgba(191,90,242,0.12);">
                    <p class="text-xs uppercase tracking-widest" style="color: rgba(191,90,242,0.9);">Butuh Penawaran</p>
                    <h2 class="text-2xl font-bold text-white mt-1.5">{{ $quoted }}</h2>
                    <p class="text-xs" style="color: rgba(235,235,245,0.65);">Menunggu tindak lanjut</p>
                </div>
                <div class="rounded-apple-lg p-4" style="background: rgba(10,132,255,0.12);">
                    <p class="text-xs uppercase tracking-widest" style="color: rgba(10,132,255,0.9);">Dalam Proses</p>
                    <h2 class="text-2xl font-bold mt-1.5" style="color: rgba(10,132,255,1);">{{ $inProgress }}</h2>
                    <p class="text-xs" style="color: rgba(235,235,245,0.65);">{{ $activePipeline }} permohonan aktif</p>
                </div>
                <div class="rounded-apple-lg p-4" style="background: rgba(52,199,89,0.12);">
                    <p class="text-xs uppercase tracking-widest" style="color: rgba(52,199,89,0.9);">Selesai</p>
                    <h2 class="text-2xl font-bold mt-1.5" style="color: rgba(52,199,89,1);">{{ $completed }}</h2>
                    <p class="text-xs" style="color: rgba(235,235,245,0.65);">Tingkat penyelesaian {{ $completionRate }}%</p>
                </div>
            </div>
        </div>
    </section>

    @if(session('success') || session('error'))
        <div class="space-y-3 mb-5">
            @if(session('success'))
                <div class="rounded-apple-lg p-4" style="background: rgba(52,199,89,0.15); border: 1px solid rgba(52,199,89,0.3);">
                    <p class="text-sm font-medium" style="color: rgba(52,199,89,1);">{{ session('success') }}</p>
                </div>
            @endif
            @if(session('error'))
                <div class="rounded-apple-lg p-4" style="background: rgba(255,59,48,0.15); border: 1px solid rgba(255,59,48,0.3);">
                    <p class="text-sm font-medium" style="color: rgba(255,59,48,1);">{{ session('error') }}</p>
                </div>
            @endif
        </div>
    @endif

    {{-- Search --}}
    <section class="card-elevated rounded-apple-xl p-5 md:p-6 space-y-5 mb-5">
        <div class="flex items-center justify-between flex-wrap gap-3">
            <div>
                <p class="text-xs uppercase tracking-[0.35em]" style="color: rgba(235,235,245,0.5);">Pencarian</p>
                <h2 class="text-xl font-semibold text-white">Cari Permohonan</h2>
                <p class="text-sm" style="color: rgba(235,235,245,0.65);">Filter berdasarkan nomor permohonan, nama klien, atau status pengajuan.</p>
            </div>
            <div class="flex items-center gap-2 text-xs" style="color: rgba(235,235,245,0.6);">
                <i class="fas fa-info-circle"></i>
                Menampilkan {{ $applications->total() }} hasil
            </div>
        </div>
        <form method="GET" action="{{ route('admin.permit-applications.index') }}" class="space-y-4">
            <input type="hidden" name="sort_by" value="{{ request('sort_by', 'submitted_at') }}">
            <input type="hidden" name="sort_order" value="{{ request('sort_order', 'desc') }}">
            <div class="flex flex-col md:flex-row md:items-end md:gap-4 gap-3">
                <div class="flex-1">
                    <label class="text-xs uppercase tracking-widest mb-2 block" style="color: rgba(235,235,245,0.6);">Nomor permohonan atau nama klien</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Masukkan nomor permohonan atau nama klien"
                           class="w-full px-4 py-2.5 rounded-apple text-sm text-white placeholder-gray-500"
                           style="background: rgba(28,28,30,0.6); border: 1px solid rgba(84,84,88,0.35);">
                </div>
                <div class="w-full md:w-60">
                    <label class="text-xs uppercase tracking-widest mb-2 block" style="color: rgba(235,235,245,0.6);">Filter status</label>
                    <select name="status" class="w-full px-4 py-2.5 rounded-apple text-sm text-white"
                            style="background: rgba(28,28,30,0.6); border: 1px solid rgba(84,84,88,0.35);">
                        <option value="">Semua Status</option>
                        @foreach($statusOptions as $value => $label)
                            <option value="{{ $value }}" {{ request('status') === $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex gap-3 w-full md:w-auto">
                    <button type="submit" class="btn-primary-sm flex-1 md:flex-none">
                        <i class="fas fa-search mr-2"></i>Terapkan
                    </button>
                    <a href="{{ route('admin.permit-applications.index') }}" class="btn-secondary-sm flex-1 md:flex-none text-center">
                        <i class="fas fa-redo mr-2"></i>Reset
                    </a>
                </div>
            </div>
        </form>
    </section>

    {{-- Applications table --}}
    <section class="card-elevated rounded-apple-xl">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 p-5" style="border-bottom: 1px solid rgba(84,84,88,0.35);">
            <div>
                <p class="text-xs uppercase tracking-[0.35em]" style="color: rgba(235,235,245,0.5);">Data Table</p>
                <h3 class="text-lg font-semibold text-white">Daftar permohonan</h3>
                <p class="text-xs mt-1" style="color: rgba(235,235,245,0.6);">
                    {{ $applications->firstItem() ?? 0 }}-{{ $applications->lastItem() ?? 0 }} dari {{ $applications->total() }} entri
                </p>
            </div>
            <form method="GET" action="{{ route('admin.permit-applications.index') }}" class="flex flex-wrap items-center gap-2 text-xs">
                @foreach(request()->except(['sort_by','sort_order','page']) as $param => $value)
                    <input type="hidden" name="{{ $param }}" value="{{ $value }}">
                @endforeach
                <label style="color: rgba(235,235,245,0.6);">Urut:</label>
                <select name="sort_by" class="px-3 py-2 rounded-apple text-xs text-white"
                        style="background: rgba(28,28,30,0.6); border: 1px solid rgba(84,84,88,0.35);">
                    <option value="submitted_at" {{ request('sort_by','submitted_at') === 'submitted_at' ? 'selected' : '' }}>Submit</option>
                    <option value="created_at" {{ request('sort_by') === 'created_at' ? 'selected' : '' }}>Dibuat</option>
                    <option value="application_number" {{ request('sort_by') === 'application_number' ? 'selected' : '' }}>No Aplikasi</option>
                    <option value="status" {{ request('sort_by') === 'status' ? 'selected' : '' }}>Status</option>
                </select>
                <select name="sort_order" class="px-3 py-2 rounded-apple text-xs text-white"
                        style="background: rgba(28,28,30,0.6); border: 1px solid rgba(84,84,88,0.35);">
                    <option value="desc" {{ request('sort_order','desc') === 'desc' ? 'selected' : '' }}>↓ Terbaru</option>
                    <option value="asc" {{ request('sort_order') === 'asc' ? 'selected' : '' }}>↑ Terlama</option>
                </select>
                <button type="submit" class="btn-secondary-sm"><i class="fas fa-sort mr-2"></i>Terapkan</button>
            </form>
        </div>
        <div class="overflow-hidden rounded-b-apple-xl">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead style="background: rgba(28,28,30,0.4); border-bottom: 1px solid rgba(84,84,88,0.35);">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs uppercase tracking-widest" style="color: rgba(235,235,245,0.6);">Aplikasi</th>
                        <th class="px-6 py-4 text-left text-xs uppercase tracking-widest" style="color: rgba(235,235,245,0.6);">Client</th>
                        <th class="px-6 py-4 text-left text-xs uppercase tracking-widest" style="color: rgba(235,235,245,0.6);">Jenis Izin</th>
                        <th class="px-6 py-4 text-left text-xs uppercase tracking-widest" style="color: rgba(235,235,245,0.6);">Status</th>
                        <th class="px-6 py-4 text-left text-xs uppercase tracking-widest" style="color: rgba(235,235,245,0.6);">Timeline</th>
                        <th class="px-6 py-4 text-left text-xs uppercase tracking-widest" style="color: rgba(235,235,245,0.6);">Quotation / Nilai</th>
                        <th class="px-6 py-4 text-left text-xs uppercase tracking-widest text-right" style="color: rgba(235,235,245,0.6);">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($applications as $app)
                        @php $style = $statusStyles[$app->status] ?? $statusStyles['draft']; @endphp
                        <tr style="border-bottom: 1px solid rgba(84,84,88,0.25);" class="hover:bg-white/5 transition">
                            <td class="px-6 py-4 align-top">
                                <p class="text-sm font-semibold text-white">{{ $app->application_number }}</p>
                                <p class="text-xs mt-0.5" style="color: rgba(235,235,245,0.6);">
                                    ID internal: {{ $app->id }}
                                </p>
                            </td>
                            <td class="px-6 py-4 align-top">
                                @if($app->client)
                                    <p class="text-sm font-semibold text-white">{{ $app->client->name }}</p>
                                    <p class="text-xs" style="color: rgba(235,235,245,0.6);">{{ $app->client->email }}</p>
                                    @if(!empty($app->client->company_type))
                                        <p class="text-xs mt-0.5" style="color: rgba(235,235,245,0.6);">{{ strtoupper($app->client->company_type) }}</p>
                                    @endif
                                @else
                                    <p class="text-sm text-gray-400">-</p>
                                @endif
                            </td>
                            <td class="px-6 py-4 align-top">
                                <div class="text-sm font-medium text-white">
                                    {{ $app->permitType->name ?? ($app->form_data['permit_package'] ?? 'Tidak ada data') }}
                                </div>
                                @if(isset($app->business_context['primary_kbli']))
                                    <p class="text-xs mt-0.5" style="color: rgba(191,90,242,0.9);">
                                        KBLI {{ $app->business_context['primary_kbli'] }}
                                    </p>
                                @endif
                                <p class="text-xs mt-0.5" style="color: rgba(235,235,245,0.6);">
                                    Dokumen: {{ $app->documents->count() }} file
                                </p>
                            </td>
                            <td class="px-6 py-4 align-top">
                                <span class="px-3 py-1.5 inline-flex text-xs font-semibold rounded-apple"
                                      style="background: {{ $style['bg'] }}; color: {{ $style['text'] }}; border: 1px solid {{ $style['border'] }};">
                                    {{ $app->status_label ?? ucfirst(str_replace('_',' ',$app->status)) }}
                                </span>
                                @if($app->reviewedBy)
                                    <p class="text-xs mt-1" style="color: rgba(235,235,245,0.55);">
                                        PIC: {{ $app->reviewedBy->name }}
                                    </p>
                                @endif
                            </td>
                            <td class="px-6 py-4 align-top">
                                <p class="text-xs" style="color: rgba(235,235,245,0.7);">
                                    Dibuat: {{ optional($app->created_at)->format('d M Y') }}
                                </p>
                                <p class="text-xs" style="color: rgba(235,235,245,0.7);">
                                    Submit: {{ optional($app->submitted_at)->format('d M Y') ?? '—' }}
                                </p>
                                <p class="text-xs mt-1" style="color: rgba(235,235,245,0.6);">
                                    Update {{ optional($app->updated_at)->diffForHumans() }}
                                </p>
                            </td>
                            <td class="px-6 py-4 align-top">
                                @if($app->quotation)
                                    <p class="text-sm font-semibold text-white">
                                        Rp {{ number_format($app->quotation->total_price, 0, ',', '.') }}
                                    </p>
                                    <p class="text-xs" style="color: rgba(235,235,245,0.6);">
                                        {{ ucfirst($app->quotation->status ?? 'draft') }}
                                    </p>
                                @else
                                    <p class="text-xs" style="color: rgba(235,235,245,0.6);">Belum ada quotation</p>
                                @endif
                            </td>
                            <td class="px-6 py-4 align-top text-right">
                                <a href="{{ route('admin.permit-applications.show', $app->id) }}" class="btn-secondary-sm">
                                    <i class="fas fa-eye mr-2"></i>Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center">
                                <i class="fas fa-folder-open text-4xl mb-3" style="color: rgba(235,235,245,0.3);"></i>
                                <p class="text-sm" style="color: rgba(235,235,245,0.65);">Tidak ada permohonan sesuai filter.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        </div>
        @if($applications->hasPages())
            <div class="px-6 py-4" style="border-top: 1px solid rgba(84,84,88,0.35);">
                {{ $applications->withQueryString()->links() }}
            </div>
        @endif
    </section>
@endsection
