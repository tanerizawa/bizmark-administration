@extends('layouts.app')

@section('title', 'Daftar Beta Tester')
@section('page-title', 'Daftar Beta Tester')

@section('content')
@php
    $totalTesters = \App\Models\BetaTester::count();
    $todayRegistrations = \App\Models\BetaTester::whereDate('created_at', today())->count();
    $pendingDocs = \App\Models\BetaTester::where('status', 'documents_pending')->count();
@endphp

{{-- Hero Section --}}
<section class="card-elevated rounded-apple-xl p-5 md:p-6 relative overflow-hidden mb-6">
    <div class="absolute inset-0 pointer-events-none" aria-hidden="true">
        <div class="w-72 h-72 bg-apple-blue opacity-30 blur-3xl rounded-full absolute -top-16 -right-10"></div>
        <div class="w-48 h-48 bg-apple-green opacity-20 blur-2xl rounded-full absolute bottom-0 left-10"></div>
    </div>
    <div class="relative flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
        <div class="space-y-3 max-w-3xl">
            <p class="text-xs uppercase tracking-[0.4em]" style="color: rgba(235,235,245,0.5);">Program Beta Testing</p>
            <h1 class="text-2xl md:text-3xl font-bold text-white">Manajemen Beta Tester</h1>
            <p class="text-sm md:text-base" style="color: rgba(235,235,245,0.7);">
                Kelola seluruh peserta program beta testing, monitor status dokumen, dan pantau progres pengujian sistem.
            </p>
            <div class="text-xs flex flex-wrap gap-3" style="color: rgba(235,235,245,0.6);">
                <span><i class="fas fa-users mr-2"></i>{{ $totalTesters }} tester terdaftar</span>
                <span><i class="fas fa-user-plus mr-2"></i>{{ $todayRegistrations }} registrasi hari ini</span>
                @if($pendingDocs > 0)
                    <span><i class="fas fa-clock mr-2"></i>{{ $pendingDocs }} menunggu tanda tangan</span>
                @endif
            </div>
        </div>
        <div class="flex flex-col items-start gap-3">
            <a href="{{ route('admin.beta-tester.dashboard') }}" class="btn-secondary-sm">
                <i class="fas fa-chart-line mr-2"></i>Dashboard
            </a>
            <a href="{{ route('admin.beta-tester.export', request()->query()) }}" class="btn-primary-sm">
                <i class="fas fa-download mr-2"></i>Export CSV
            </a>
        </div>
    </div>
</section>

{{-- Flash Messages --}}
@if(session('success'))
    <div class="rounded-apple-lg px-4 py-3 flex items-center gap-3 mb-5" style="background: rgba(52,199,89,0.12); border: 1px solid rgba(52,199,89,0.3); color: rgba(52,199,89,1);">
        <i class="fas fa-check-circle"></i>
        <span class="text-sm">{{ session('success') }}</span>
    </div>
@endif

@if(session('error'))
    <div class="rounded-apple-lg px-4 py-3 flex items-center gap-3 mb-5" style="background: rgba(255,69,58,0.12); border: 1px solid rgba(255,69,58,0.3); color: rgba(255,69,58,1);">
        <i class="fas fa-exclamation-circle"></i>
        <span class="text-sm">{{ session('error') }}</span>
    </div>
@endif

{{-- Filters Card --}}
<section class="card-elevated rounded-apple-lg p-5 mb-5">
    <h2 class="text-base font-semibold mb-4 text-white flex items-center gap-2">
        <i class="fas fa-filter text-sm" style="color: var(--apple-blue);"></i>
        Filter & Pencarian
    </h2>
    <form method="GET" action="{{ route('admin.beta-tester.index') }}" id="filterForm">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Search -->
            <div>
                <label class="block text-xs font-medium mb-2 uppercase tracking-wider" style="color: rgba(235,235,245,0.6);">
                    Pencarian
                </label>
                <input type="text" 
                       name="search" 
                       value="{{ request('search') }}"
                       placeholder="Nama, email, universitas..."
                       class="w-full px-4 py-2.5 rounded-apple text-sm transition-all"
                       style="background: var(--dark-bg-secondary); border: 1px solid var(--dark-separator); color: var(--dark-text-primary);"
                       onfocus="this.style.borderColor='var(--apple-blue)'"
                       onblur="this.style.borderColor='var(--dark-separator)'">
            </div>

            <!-- Status Filter -->
            <div>
                <label class="block text-xs font-medium mb-2 uppercase tracking-wider" style="color: rgba(235,235,245,0.6);">
                    Status Tester
                </label>
                <select name="status" 
                        class="w-full px-4 py-2.5 rounded-apple text-sm transition-all"
                        style="background: var(--dark-bg-secondary); border: 1px solid var(--dark-separator); color: var(--dark-text-primary);"
                        onfocus="this.style.borderColor='var(--apple-blue)'"
                        onblur="this.style.borderColor='var(--dark-separator)'">
                        <option value="">Semua Status</option>
                        <option value="registered" {{ request('status') == 'registered' ? 'selected' : '' }}>
                            Terdaftar
                        </option>
                        <option value="documents_pending" {{ request('status') == 'documents_pending' ? 'selected' : '' }}>
                            Pending Dokumen
                        </option>
                        <option value="documents_signed" {{ request('status') == 'documents_signed' ? 'selected' : '' }}>
                            Dokumen Signed
                        </option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>
                            Aktif
                        </option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>
                            Nonaktif
                        </option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>
                            Selesai
                        </option>
                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>
                            Ditolak
                        </option>
                    </select>
                </div>

            <!-- Document Status Filter -->
            <div>
                <label class="block text-xs font-medium mb-2 uppercase tracking-wider" style="color: rgba(235,235,245,0.6);">
                    Status Dokumen
                </label>
                <select name="document_status" 
                        class="w-full px-4 py-2.5 rounded-apple text-sm transition-all"
                        style="background: var(--dark-bg-secondary); border: 1px solid var(--dark-separator); color: var(--dark-text-primary);"
                        onfocus="this.style.borderColor='var(--apple-blue)'"
                        onblur="this.style.borderColor='var(--dark-separator)'">
                        <option value="">Semua</option>
                        <option value="all_signed" {{ request('document_status') == 'all_signed' ? 'selected' : '' }}>
                            Semua Signed
                        </option>
                        <option value="partial_signed" {{ request('document_status') == 'partial_signed' ? 'selected' : '' }}>
                            Sebagian Signed
                        </option>
                        <option value="none_signed" {{ request('document_status') == 'none_signed' ? 'selected' : '' }}>
                            Belum Signed
                        </option>
                    </select>
                </div>

            <!-- University Filter -->
            <div>
                <label class="block text-xs font-medium mb-2 uppercase tracking-wider" style="color: rgba(235,235,245,0.6);">
                    Universitas
                </label>
                <input type="text" 
                       name="university" 
                       value="{{ request('university') }}"
                       placeholder="Filter universitas..."
                       class="w-full px-4 py-2.5 rounded-apple text-sm transition-all"
                       style="background: var(--dark-bg-secondary); border: 1px solid var(--dark-separator); color: var(--dark-text-primary);"
                       onfocus="this.style.borderColor='var(--apple-blue)'"
                       onblur="this.style.borderColor='var(--dark-separator)'">
            </div>
        </div>

        <!-- Filter Actions -->
        <div class="flex items-center gap-3 mt-5 pt-5" style="border-top: 1px solid var(--dark-separator);">
            <button type="submit" class="btn-primary-sm">
                <i class="fas fa-search mr-2"></i>
                Terapkan Filter
            </button>
            <a href="{{ route('admin.beta-tester.index') }}" class="btn-secondary-sm">
                <i class="fas fa-redo mr-2"></i>
                Reset
            </a>
            <div class="ml-auto text-xs" style="color: rgba(235,235,245,0.6);">
                <i class="fas fa-info-circle mr-1"></i>
                Menampilkan <strong style="color: var(--apple-blue);">{{ $betaTesters->count() }}</strong> dari <strong style="color: var(--dark-text-primary);">{{ $betaTesters->total() }}</strong> tester
            </div>
        </div>
    </form>
</section>

{{-- Table Card --}}
<section class="card-elevated rounded-apple-lg overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead style="background: var(--dark-bg-tertiary); border-bottom: 1px solid var(--dark-separator);">
                <tr>
                    <th class="text-left py-3 px-4 text-xs font-medium uppercase tracking-wider" style="color: rgba(235,235,245,0.5);">
                        <div class="flex items-center gap-2">
                            No. Registrasi
                            <a href="{{ route('admin.beta-tester.index', array_merge(request()->query(), ['sort' => 'registration_number', 'direction' => request('sort') == 'registration_number' && request('direction') == 'asc' ? 'desc' : 'asc'])) }}" 
                               class="opacity-50 hover:opacity-100 transition-opacity">
                                <i class="fas fa-sort text-xs"></i>
                            </a>
                        </div>
                    </th>
                    <th class="text-left py-3 px-4 text-xs font-medium uppercase tracking-wider" style="color: rgba(235,235,245,0.5);">
                        <div class="flex items-center gap-2">
                            Nama & Kontak
                            <a href="{{ route('admin.beta-tester.index', array_merge(request()->query(), ['sort' => 'full_name', 'direction' => request('sort') == 'full_name' && request('direction') == 'asc' ? 'desc' : 'asc'])) }}"
                               class="opacity-50 hover:opacity-100 transition-opacity">
                                <i class="fas fa-sort text-xs"></i>
                            </a>
                        </div>
                    </th>
                    <th class="text-left py-3 px-4 text-xs font-medium uppercase tracking-wider" style="color: rgba(235,235,245,0.5);">
                        Universitas
                    </th>
                    <th class="text-center py-3 px-4 text-xs font-medium uppercase tracking-wider" style="color: rgba(235,235,245,0.5);">
                        Status
                    </th>
                    <th class="text-center py-3 px-4 text-xs font-medium uppercase tracking-wider" style="color: rgba(235,235,245,0.5);">
                        Dokumen
                    </th>
                    <th class="text-center py-3 px-4 text-xs font-medium uppercase tracking-wider" style="color: rgba(235,235,245,0.5);">
                        <div class="flex items-center justify-center gap-2">
                            Tanggal
                            <a href="{{ route('admin.beta-tester.index', array_merge(request()->query(), ['sort' => 'created_at', 'direction' => request('sort') == 'created_at' && request('direction') == 'asc' ? 'desc' : 'asc'])) }}"
                               class="opacity-50 hover:opacity-100 transition-opacity">
                                <i class="fas fa-sort text-xs"></i>
                            </a>
                        </div>
                    </th>
                    <th class="text-center py-3 px-4 text-xs font-medium uppercase tracking-wider" style="color: rgba(235,235,245,0.5);">
                        Aksi
                    </th>
                </tr>
            </thead>
            <tbody>
                @forelse($betaTesters as $tester)
                <tr style="border-bottom: 1px solid var(--dark-separator);" 
                    class="transition-colors hover:bg-white hover:bg-opacity-5">
                    <!-- Registration Number -->
                    <td class="py-3 px-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center text-white text-sm font-bold" 
                                 style="background: linear-gradient(135deg, var(--apple-blue), var(--apple-purple));">
                                {{ strtoupper(substr($tester->full_name, 0, 1)) }}
                            </div>
                            <div>
                                <p class="font-mono text-xs font-semibold" style="color: var(--apple-blue);">
                                    {{ $tester->registration_number }}
                                </p>
                            </div>
                        </div>
                    </td>

                    <!-- Name & Contact -->
                    <td class="py-3 px-4">
                        <div>
                            <p class="font-medium text-sm mb-1 text-white">
                                {{ $tester->full_name }}
                            </p>
                            <p class="text-xs mb-0.5" style="color: rgba(235,235,245,0.6);">
                                <i class="fas fa-envelope mr-1"></i>
                                {{ $tester->email }}
                            </p>
                            <p class="text-xs" style="color: rgba(235,235,245,0.6);">
                                <i class="fas fa-phone mr-1"></i>
                                {{ $tester->phone }}
                            </p>
                        </div>
                    </td>

                    <!-- University -->
                    <td class="py-3 px-4">
                        <div>
                            <p class="text-sm font-medium text-white mb-0.5">
                                {{ Str::limit($tester->university, 30) }}
                            </p>
                            <p class="text-xs" style="color: rgba(235,235,245,0.6);">
                                {{ Str::limit($tester->faculty ?? '-', 30) }}
                            </p>
                        </div>
                    </td>

                    <!-- Status -->
                    <td class="py-3 px-4 text-center">
                        @php
                            $statusConfig = [
                                'registered' => ['color' => '#6B7280', 'bg' => 'rgba(107, 114, 128, 0.15)', 'label' => 'Terdaftar', 'icon' => 'user-plus'],
                                'documents_pending' => ['color' => 'rgba(255,214,10,1)', 'bg' => 'rgba(255,214,10,0.15)', 'label' => 'Pending', 'icon' => 'clock'],
                                'documents_signed' => ['color' => 'rgba(10,132,255,1)', 'bg' => 'rgba(10,132,255,0.15)', 'label' => 'Signed', 'icon' => 'check'],
                                'active' => ['color' => 'rgba(48,209,88,1)', 'bg' => 'rgba(48,209,88,0.15)', 'label' => 'Aktif', 'icon' => 'check-circle'],
                                'inactive' => ['color' => '#6B7280', 'bg' => 'rgba(107, 114, 128, 0.15)', 'label' => 'Nonaktif', 'icon' => 'ban'],
                                'completed' => ['color' => 'rgba(175,82,222,1)', 'bg' => 'rgba(175,82,222,0.15)', 'label' => 'Selesai', 'icon' => 'flag-checkered'],
                                'rejected' => ['color' => 'rgba(255,69,58,1)', 'bg' => 'rgba(255,69,58,0.15)', 'label' => 'Ditolak', 'icon' => 'times-circle'],
                            ];
                            $config = $statusConfig[$tester->status] ?? $statusConfig['registered'];
                        @endphp
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-medium"
                              style="background: {{ $config['bg'] }}; color: {{ $config['color'] }};">
                            <i class="fas fa-{{ $config['icon'] }}"></i>
                            {{ $config['label'] }}
                        </span>
                    </td>

                    <!-- Documents -->
                    <td class="py-3 px-4 text-center">
                        @php
                            $signedCount = $tester->documents->where('is_signed', true)->count();
                            $totalDocs = $tester->documents->count();
                            $docColor = $signedCount == $totalDocs ? 'rgba(48,209,88,1)' : ($signedCount > 0 ? 'rgba(10,132,255,1)' : 'rgba(255,214,10,1)');
                            $docBg = $signedCount == $totalDocs ? 'rgba(48,209,88,0.15)' : ($signedCount > 0 ? 'rgba(10,132,255,0.15)' : 'rgba(255,214,10,0.15)');
                        @endphp
                        <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-apple text-xs font-medium"
                             style="background: {{ $docBg }}; color: {{ $docColor }};">
                            <i class="fas fa-file-signature"></i>
                            <span class="font-semibold">{{ $signedCount }}/{{ $totalDocs }}</span>
                        </div>
                    </td>

                    <!-- Date -->
                    <td class="py-3 px-4 text-center">
                        <div>
                            <p class="text-sm font-medium text-white">
                                {{ $tester->created_at->format('d M Y') }}
                            </p>
                            <p class="text-xs" style="color: rgba(235,235,245,0.5);">
                                {{ $tester->created_at->format('H:i') }}
                            </p>
                        </div>
                    </td>

                    <!-- Actions -->
                    <td class="py-3 px-4 text-center">
                        <div class="flex items-center justify-center gap-2">
                            <a href="{{ route('admin.beta-tester.show', $tester) }}"
                               class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-apple text-xs font-medium transition-all hover:scale-105"
                               style="background: rgba(10,132,255,0.15); color: rgba(10,132,255,1);"
                               title="Detail">
                                <i class="fas fa-eye"></i>
                                <span>Detail</span>
                            </a>
                            
                            <form action="{{ route('admin.beta-tester.destroy', $tester) }}" 
                                  method="POST" 
                                  class="inline"
                                  onsubmit="return confirm('Yakin ingin menghapus beta tester {{ $tester->full_name }}? Semua data terkait akan dihapus.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-apple text-xs font-medium transition-all hover:scale-105"
                                        style="background: rgba(255,69,58,0.15); color: rgba(255,69,58,1);"
                                        title="Hapus">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="py-16 text-center">
                        <div style="color: rgba(235,235,245,0.5);">
                            <i class="fas fa-users-slash text-5xl mb-4 opacity-30"></i>
                            <p class="text-base font-medium mb-2 text-white">Tidak ada beta tester</p>
                            <p class="text-sm">
                                @if(request()->hasAny(['search', 'status', 'document_status', 'university']))
                                    Tidak ada hasil dengan filter yang dipilih.
                                    <a href="{{ route('admin.beta-tester.index') }}" class="font-medium hover:underline" style="color: var(--apple-blue);">
                                        Reset filter
                                    </a>
                                @else
                                    Belum ada beta tester yang terdaftar
                                @endif
                            </p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($betaTesters->hasPages())
    <div class="px-5 py-4" style="border-top: 1px solid var(--dark-separator); background: var(--dark-bg-tertiary);">
        <div class="flex items-center justify-between flex-wrap gap-4">
            <div class="text-xs" style="color: rgba(235,235,245,0.6);">
                Menampilkan <strong class="text-white">{{ $betaTesters->firstItem() }}</strong> - <strong class="text-white">{{ $betaTesters->lastItem() }}</strong> dari <strong class="text-white">{{ $betaTesters->total() }}</strong> tester
            </div>
            
            <div class="flex gap-2">
                {{-- Previous Button --}}
                @if ($betaTesters->onFirstPage())
                    <span class="px-3 py-2 rounded-apple text-xs font-medium opacity-40 cursor-not-allowed"
                          style="background: var(--dark-bg-secondary); color: rgba(235,235,245,0.6);">
                        <i class="fas fa-chevron-left mr-1"></i>
                        Previous
                    </span>
                @else
                    <a href="{{ $betaTesters->previousPageUrl() }}"
                       class="px-3 py-2 rounded-apple text-xs font-medium transition-all hover:scale-105"
                       style="background: var(--dark-bg-secondary); color: var(--dark-text-primary);">
                        <i class="fas fa-chevron-left mr-1"></i>
                        Previous
                    </a>
                @endif

                {{-- Page Numbers --}}
                @foreach(range(1, $betaTesters->lastPage()) as $page)
                    @if($page == $betaTesters->currentPage())
                        <span class="px-3 py-2 rounded-apple text-xs font-medium"
                              style="background: linear-gradient(135deg, var(--apple-blue), var(--apple-purple)); color: white;">
                            {{ $page }}
                        </span>
                    @elseif($page == 1 || $page == $betaTesters->lastPage() || abs($page - $betaTesters->currentPage()) <= 2)
                        <a href="{{ $betaTesters->url($page) }}"
                           class="px-3 py-2 rounded-apple text-xs font-medium transition-all hover:scale-105"
                           style="background: var(--dark-bg-secondary); color: var(--dark-text-primary);">
                            {{ $page }}
                        </a>
                    @elseif(abs($page - $betaTesters->currentPage()) == 3)
                        <span class="px-2 py-2 text-xs" style="color: rgba(235,235,245,0.5);">...</span>
                    @endif
                @endforeach

                {{-- Next Button --}}
                @if ($betaTesters->hasMorePages())
                    <a href="{{ $betaTesters->nextPageUrl() }}"
                       class="px-3 py-2 rounded-apple text-xs font-medium transition-all hover:scale-105"
                       style="background: var(--dark-bg-secondary); color: var(--dark-text-primary);">
                        Next
                        <i class="fas fa-chevron-right ml-1"></i>
                    </a>
                @else
                    <span class="px-3 py-2 rounded-apple text-xs font-medium opacity-40 cursor-not-allowed"
                          style="background: var(--dark-bg-secondary); color: rgba(235,235,245,0.6);">
                        Next
                        <i class="fas fa-chevron-right ml-1"></i>
                    </span>
                @endif
            </div>
        </div>
    </div>
    @endif
</section>
@endsection
