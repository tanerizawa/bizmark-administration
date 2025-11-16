@extends('client.layouts.app')

@section('title', 'Permohonan Izin Saya')

@section('content')
@php
    $statusMeta = [
        'draft' => ['label' => 'Draft', 'desc' => 'Masih bisa Anda ubah', 'color' => 'bg-gray-100 text-gray-700'],
        'submitted' => ['label' => 'Diajukan', 'desc' => 'Menunggu verifikasi awal', 'color' => 'bg-blue-100 text-blue-700'],
        'under_review' => ['label' => 'Dalam Review', 'desc' => 'Sedang diperiksa tim', 'color' => 'bg-orange-100 text-orange-700'],
        'document_incomplete' => ['label' => 'Dokumen Kurang', 'desc' => 'Butuh aksi dokumen', 'color' => 'bg-yellow-100 text-yellow-700'],
        'quoted' => ['label' => 'Menunggu Persetujuan', 'desc' => 'Review detail penawaran', 'color' => 'bg-purple-100 text-purple-700'],
        'quotation_accepted' => ['label' => 'Quotation Diterima', 'desc' => 'Segera lanjutkan pembayaran', 'color' => 'bg-emerald-100 text-emerald-700'],
        'payment_pending' => ['label' => 'Menunggu Pembayaran', 'desc' => 'Upload bukti bayar', 'color' => 'bg-amber-100 text-amber-700'],
        'payment_verified' => ['label' => 'Pembayaran Terverifikasi', 'desc' => 'Menunggu progres tim', 'color' => 'bg-emerald-100 text-emerald-700'],
        'in_progress' => ['label' => 'Sedang Diproses', 'desc' => 'Ditangani konsultan', 'color' => 'bg-sky-100 text-sky-700'],
        'completed' => ['label' => 'Selesai', 'desc' => 'Izin sudah terbit', 'color' => 'bg-green-100 text-green-800'],
        'cancelled' => ['label' => 'Dibatalkan', 'desc' => 'Tidak dilanjutkan', 'color' => 'bg-gray-200 text-gray-700'],
    ];
    $totalApplications = $applications->total();
    $pendingCount = ($statusCounts['submitted'] ?? 0) + ($statusCounts['under_review'] ?? 0) + ($statusCounts['in_progress'] ?? 0);
    $actionNeeded = ($statusCounts['document_incomplete'] ?? 0) + ($statusCounts['payment_pending'] ?? 0);
@endphp

<div class="space-y-8">
    <div class="bg-gradient-to-r from-slate-900 via-indigo-900 to-sky-800 text-white rounded-3xl shadow-xl overflow-hidden relative">
        <div class="absolute inset-0 opacity-20 bg-[url('https://www.toptal.com/designers/subtlepatterns/uploads/dot-grid.png')]"></div>
        <div class="relative p-6 lg:p-8 flex flex-col md:flex-row md:items-center md:justify-between gap-6">
            <div class="space-y-3 flex-1">
                <p class="text-xs uppercase tracking-[0.35em] text-white/70">Dashboard Permohonan</p>
                <h1 class="text-3xl font-bold leading-tight">Kelola seluruh proses izin usaha Anda dalam satu tempat.</h1>
                <p class="text-white/80">Pantau status, lengkapi dokumen, dan lanjutkan pengajuan baru tanpa kehilangan konteks.</p>
                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('client.services.index') }}" class="inline-flex items-center gap-2 bg-white text-indigo-700 font-semibold px-5 py-3 rounded-xl shadow">
                        <i class="fas fa-plus"></i> Ajukan Izin Baru
                    </a>
                    <a href="{{ route('client.documents.index') }}" class="inline-flex items-center gap-2 bg-white/10 border border-white/30 px-5 py-3 rounded-xl font-semibold">
                        <i class="fas fa-paperclip"></i> Lengkapi Dokumen
                    </a>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4 w-full md:w-auto">
                <div class="bg-white/15 rounded-2xl p-4 backdrop-blur">
                    <p class="text-xs uppercase tracking-wide text-white/70">Total Permohonan</p>
                    <p class="text-3xl font-bold">{{ $totalApplications }}</p>
                    <p class="text-xs text-white/70 mt-1">{{ $statusCounts['completed'] ?? 0 }} selesai</p>
                </div>
                <div class="bg-white/15 rounded-2xl p-4 backdrop-blur">
                    <p class="text-xs uppercase tracking-wide text-white/70">Butuh Aksi</p>
                    <p class="text-3xl font-bold">{{ $actionNeeded }}</p>
                    <p class="text-xs text-white/70 mt-1">Dokumen/pembayaran</p>
                </div>
                <div class="bg-white/15 rounded-2xl p-4 backdrop-blur">
                    <p class="text-xs uppercase tracking-wide text-white/70">Diproses Konsultan</p>
                    <p class="text-3xl font-bold">{{ $pendingCount }}</p>
                    <p class="text-xs text-white/70 mt-1">Sedang ditangani</p>
                </div>
                <div class="bg-white/15 rounded-2xl p-4 backdrop-blur">
                    <p class="text-xs uppercase tracking-wide text-white/70">Draft</p>
                    <p class="text-3xl font-bold">{{ $statusCounts['draft'] ?? 0 }}</p>
                    <p class="text-xs text-white/70 mt-1">Perlu Anda lanjutkan</p>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-800 p-5 space-y-4">
        <form method="GET" action="{{ route('client.applications.index') }}" class="flex flex-col lg:flex-row gap-4">
            <div class="lg:w-1/3">
                <label class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Filter Status</label>
                <select name="status" class="mt-1 w-full px-4 py-2.5 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-indigo-500 dark:bg-gray-800 dark:text-white" onchange="this.form.submit()">
                    <option value="">Semua Status</option>
                    @foreach($statusMeta as $status => $meta)
                        <option value="{{ $status }}" {{ request('status') === $status ? 'selected' : '' }}>
                            {{ $meta['label'] }} ({{ $statusCounts[$status] ?? 0 }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="flex-1">
                <label class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Cari Nomor Permohonan</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Contoh: APP-2024-015" class="mt-1 w-full px-4 py-2.5 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-indigo-500 dark:bg-gray-800 dark:text-white">
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="h-12 px-6 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-semibold flex items-center gap-2">
                    <i class="fas fa-search"></i> Cari
                </button>
                @if(request('status') || request('search'))
                <a href="{{ route('client.applications.index') }}" class="h-12 px-6 bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-200 rounded-xl font-semibold flex items-center gap-2">
                    <i class="fas fa-redo"></i> Reset
                </a>
                @endif
            </div>
        </form>
        <div class="flex flex-wrap gap-2">
            @foreach($statusMeta as $status => $meta)
            <a href="{{ route('client.applications.index', array_merge(request()->except('page'), ['status' => $status])) }}" class="px-3 py-1.5 rounded-full text-xs font-semibold border {{ request('status') === $status ? 'bg-indigo-600 text-white border-indigo-600' : 'border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-300 hover:border-indigo-200 dark:hover:border-indigo-500' }}">
                {{ $meta['label'] }} • {{ $statusCounts[$status] ?? 0 }}
            </a>
            @endforeach
        </div>
    </div>

    @if($applications->count() > 0)
        <div class="space-y-4">
            @foreach($applications as $application)
                @php
                    $formData = is_string($application->form_data) ? json_decode($application->form_data, true) : $application->form_data;
                    $isPackage = isset($formData['package_type']) && $formData['package_type'] === 'multi_permit';
                    $meta = $statusMeta[$application->status] ?? ['label' => ucfirst(str_replace('_', ' ', $application->status)), 'desc' => '', 'color' => 'bg-gray-100 text-gray-700'];
                @endphp
                <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm hover:shadow-md transition">
                    <div class="p-6 flex flex-col xl:flex-row xl:items-start xl:justify-between gap-4">
                        <div class="flex-1 space-y-4">
                            <div class="flex flex-wrap items-center gap-3">
                                <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ $application->application_number }}</span>
                                <span class="px-3 py-1 text-xs font-semibold rounded-full {{ $meta['color'] }}">
                                    <i class="fas fa-circle text-[7px] mr-1"></i> {{ $meta['label'] }}
                                </span>
                            </div>
                            <div>
                                <p class="text-lg font-semibold text-gray-900 dark:text-white">
                                    {{ $application->permitType->name ?? ($formData['permit_name'] ?? 'Jenis Izin Tidak Diketahui') }}
                                </p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    KBLI {{ $application->kbli_code ?? 'N/A' }} • {{ $application->kbli_description ?? 'Deskripsi tidak tersedia' }}
                                </p>
                                @if(!empty($meta['desc']))
                                <p class="text-xs text-indigo-500 mt-1">{{ $meta['desc'] }}</p>
                                @endif
                                @if($isPackage)
                                    <div class="mt-3 p-3 bg-purple-50 dark:bg-purple-900/20 border border-purple-200 dark:border-purple-700 rounded-xl text-xs text-purple-800 dark:text-purple-200 flex flex-wrap gap-2">
                                        <span class="font-semibold text-sm flex items-center gap-2"><i class="fas fa-box"></i>{{ $formData['project_name'] ?? 'Paket Izin' }}</span>
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-blue-100 text-blue-700"><i class="fas fa-handshake"></i>{{ $formData['permits_by_service']['bizmark'] ?? 0 }} Bizmark</span>
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-green-100 text-green-700"><i class="fas fa-check"></i>{{ $formData['permits_by_service']['owned'] ?? 0 }} Sudah Ada</span>
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-amber-100 text-amber-700"><i class="fas fa-user"></i>{{ $formData['permits_by_service']['self'] ?? 0 }} Mandiri</span>
                                    </div>
                                @endif
                            </div>
                            <div class="grid grid-cols-2 md:flex md:flex-wrap gap-3 text-xs text-gray-500 dark:text-gray-400">
                                <span class="inline-flex items-center gap-1"><i class="fas fa-calendar-plus"></i>Dibuat {{ optional($application->created_at)->format('d M Y') }}</span>
                                @if($application->submitted_at)
                                <span class="inline-flex items-center gap-1"><i class="fas fa-paper-plane"></i>Diajukan {{ optional($application->submitted_at)->format('d M Y') }}</span>
                                @endif
                                <span class="inline-flex items-center gap-1"><i class="fas fa-paperclip"></i>{{ $application->documents->count() }} Dokumen</span>
                                @if($application->quoted_price)
                                <span class="inline-flex items-center gap-1 text-gray-900 dark:text-white font-semibold"><i class="fas fa-money-bill"></i>Rp {{ number_format($application->quoted_price, 0, ',', '.') }}</span>
                                @endif
                            </div>
                        </div>
                        <div class="w-full xl:w-64 flex flex-col gap-2">
                            <a href="{{ route('client.applications.show', $application->id) }}" class="w-full px-4 py-2.5 rounded-xl text-center text-sm font-semibold bg-indigo-600 text-white hover:bg-indigo-700 transition">
                                <i class="fas fa-eye mr-2"></i>Detail Permohonan
                            </a>
                            @if($application->canBeEdited())
                            <a href="{{ route('client.applications.edit', $application->id) }}" class="w-full px-4 py-2.5 rounded-xl text-center text-sm font-semibold bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-700 transition">
                                <i class="fas fa-edit mr-2"></i>Edit Draft
                            </a>
                            @endif
                            @if($application->status === 'draft' && $application->documents->count() > 0)
                            <form method="POST" action="{{ route('client.applications.submit', $application->id) }}" onsubmit="return confirm('Yakin ingin mengajukan permohonan ini?')">
                                @csrf
                                <button type="submit" class="w-full px-4 py-2.5 rounded-xl text-center text-sm font-semibold bg-emerald-600 text-white hover:bg-emerald-700 transition">
                                    <i class="fas fa-paper-plane mr-2"></i>Ajukan Sekarang
                                </button>
                            </form>
                            @endif
                            @if($application->canBeCancelled())
                            <form method="POST" action="{{ route('client.applications.cancel', $application->id) }}" onsubmit="return confirm('Yakin ingin membatalkan permohonan ini?')">
                                @csrf
                                <button type="submit" class="w-full px-4 py-2.5 rounded-xl text-center text-sm font-semibold bg-red-50 text-red-600 hover:bg-red-100 transition">
                                    <i class="fas fa-times-circle mr-2"></i>Batalkan
                                </button>
                            </form>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm p-4">
            {{ $applications->links() }}
        </div>
    @else
        <div class="bg-white dark:bg-gray-900 rounded-2xl border border-dashed border-gray-200 dark:border-gray-700 p-12 text-center">
            <i class="fas fa-inbox text-6xl text-gray-300 dark:text-gray-600 mb-4"></i>
            <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">
                @if(request('status') || request('search'))
                    Tidak Ada Permohonan Ditemukan
                @else
                    Belum Ada Permohonan
                @endif
            </h3>
            <p class="text-gray-600 dark:text-gray-400 mb-6">
                @if(request('status') || request('search'))
                    Coba ubah filter atau kata kunci pencarian Anda.
                @else
                    Mulai ajukan permohonan izin pertama Anda sekarang.
                @endif
            </p>
            @if(request('status') || request('search'))
            <a href="{{ route('client.applications.index') }}" class="inline-flex items-center gap-2 px-6 py-2 bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-200 rounded-xl font-semibold hover:bg-gray-200 dark:hover:bg-gray-700">
                <i class="fas fa-redo"></i> Reset Filter
            </a>
            @else
            <a href="{{ route('client.services.index') }}" class="inline-flex items-center gap-2 px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-semibold">
                <i class="fas fa-plus"></i> Ajukan Permohonan
            </a>
            @endif
        </div>
    @endif
</div>
@endsection
