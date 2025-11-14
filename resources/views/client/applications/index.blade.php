@extends('client.layouts.app')

@section('title', 'Permohonan Izin Saya')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Permohonan Izin Saya</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-1">Kelola dan pantau status permohonan izin Anda</p>
        </div>
        <a href="{{ route('client.services.index') }}" 
           class="mt-4 md:mt-0 inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">
            <i class="fas fa-plus mr-2"></i>
            Ajukan Izin Baru
        </a>
    </div>

    <!-- Filters & Search -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4">
        <form method="GET" action="{{ route('client.applications.index') }}" class="flex flex-col md:flex-row gap-4">
            <!-- Status Filter -->
            <div class="flex-1">
                <select name="status" 
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                        onchange="this.form.submit()">
                    <option value="">Semua Status</option>
                    <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft ({{ $statusCounts['draft'] ?? 0 }})</option>
                    <option value="submitted" {{ request('status') == 'submitted' ? 'selected' : '' }}>Diajukan ({{ $statusCounts['submitted'] ?? 0 }})</option>
                    <option value="under_review" {{ request('status') == 'under_review' ? 'selected' : '' }}>Dalam Review ({{ $statusCounts['under_review'] ?? 0 }})</option>
                    <option value="document_incomplete" {{ request('status') == 'document_incomplete' ? 'selected' : '' }}>Dokumen Tidak Lengkap ({{ $statusCounts['document_incomplete'] ?? 0 }})</option>
                    <option value="quoted" {{ request('status') == 'quoted' ? 'selected' : '' }}>Menunggu Persetujuan ({{ $statusCounts['quoted'] ?? 0 }})</option>
                    <option value="quotation_accepted" {{ request('status') == 'quotation_accepted' ? 'selected' : '' }}>Quotation Diterima ({{ $statusCounts['quotation_accepted'] ?? 0 }})</option>
                    <option value="payment_pending" {{ request('status') == 'payment_pending' ? 'selected' : '' }}>Menunggu Pembayaran ({{ $statusCounts['payment_pending'] ?? 0 }})</option>
                    <option value="payment_verified" {{ request('status') == 'payment_verified' ? 'selected' : '' }}>Pembayaran Terverifikasi ({{ $statusCounts['payment_verified'] ?? 0 }})</option>
                    <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>Sedang Diproses ({{ $statusCounts['in_progress'] ?? 0 }})</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Selesai ({{ $statusCounts['completed'] ?? 0 }})</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Dibatalkan ({{ $statusCounts['cancelled'] ?? 0 }})</option>
                </select>
            </div>

            <!-- Search -->
            <div class="flex-1">
                <input type="text" 
                       name="search" 
                       value="{{ request('search') }}"
                       placeholder="Cari nomor permohonan..."
                       class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
            </div>

            <div class="flex gap-2">
                <button type="submit" 
                        class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">
                    <i class="fas fa-search mr-2"></i>Cari
                </button>
                @if(request('status') || request('search'))
                <a href="{{ route('client.applications.index') }}" 
                   class="px-6 py-2 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-lg transition">
                    <i class="fas fa-redo mr-2"></i>Reset
                </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Applications List -->
    @if($applications->count() > 0)
        <div class="space-y-4">
            @foreach($applications as $application)
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm hover:shadow-md transition overflow-hidden">
                    <div class="p-6">
                        <div class="flex flex-col md:flex-row md:items-start md:justify-between">
                            <div class="flex-1">
                                <!-- Application Number & Status -->
                                <div class="flex flex-wrap items-center gap-3 mb-3">
                                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">
                                        {{ $application->application_number }}
                                    </h3>
                                    <span class="inline-block px-3 py-1 text-xs font-semibold rounded-full"
                                          style="background-color: {{ $application->status_color }}20; color: {{ $application->status_color }}">
                                        {{ $application->status_label }}
                                    </span>
                                </div>

                                <!-- Permit Type -->
                                <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">
                                    <i class="fas fa-file-alt mr-2"></i>
                                    <strong>Jenis Izin:</strong> {{ $application->permitType->name }}
                                </p>

                                <!-- Dates -->
                                <div class="flex flex-wrap gap-4 text-sm text-gray-600 dark:text-gray-400">
                                    <span>
                                        <i class="fas fa-calendar-plus mr-1"></i>
                                        Dibuat: {{ $application->created_at->format('d M Y') }}
                                    </span>
                                    @if($application->submitted_at)
                                    <span>
                                        <i class="fas fa-paper-plane mr-1"></i>
                                        Diajukan: {{ $application->submitted_at->format('d M Y') }}
                                    </span>
                                    @endif
                                </div>

                                <!-- Documents Count -->
                                <div class="mt-3 flex items-center gap-4 text-sm">
                                    <span class="text-gray-600 dark:text-gray-400">
                                        <i class="fas fa-paperclip mr-1"></i>
                                        {{ $application->documents->count() }} dokumen
                                    </span>
                                    @if($application->quoted_price)
                                    <span class="text-gray-900 dark:text-white font-semibold">
                                        <i class="fas fa-money-bill-wave mr-1"></i>
                                        Rp {{ number_format($application->quoted_price, 0, ',', '.') }}
                                    </span>
                                    @endif
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="mt-4 md:mt-0 md:ml-6 flex flex-col gap-2 min-w-[160px]">
                                <a href="{{ route('client.applications.show', $application->id) }}" 
                                   class="text-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition text-sm">
                                    <i class="fas fa-eye mr-2"></i>Detail
                                </a>
                                
                                @if($application->canBeEdited())
                                <a href="{{ route('client.applications.edit', $application->id) }}" 
                                   class="text-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition text-sm">
                                    <i class="fas fa-edit mr-2"></i>Edit
                                </a>
                                @endif

                                @if($application->status === 'draft' && $application->documents->count() > 0)
                                <form method="POST" action="{{ route('client.applications.submit', $application->id) }}" 
                                      onsubmit="return confirm('Yakin ingin mengajukan permohonan ini?')">
                                    @csrf
                                    <button type="submit" 
                                            class="w-full px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition text-sm">
                                        <i class="fas fa-paper-plane mr-2"></i>Ajukan
                                    </button>
                                </form>
                                @endif

                                @if($application->canBeCancelled())
                                <form method="POST" action="{{ route('client.applications.cancel', $application->id) }}" 
                                      onsubmit="return confirm('Yakin ingin membatalkan permohonan ini?')">
                                    @csrf
                                    <button type="submit" 
                                            class="w-full px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition text-sm">
                                        <i class="fas fa-times-circle mr-2"></i>Batalkan
                                    </button>
                                </form>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4">
            {{ $applications->links() }}
        </div>
    @else
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-12 text-center">
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
                    Coba ubah filter atau kata kunci pencarian Anda
                @else
                    Mulai ajukan permohonan izin pertama Anda sekarang
                @endif
            </p>
            @if(request('status') || request('search'))
            <a href="{{ route('client.applications.index') }}" 
               class="inline-block px-6 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition">
                Reset Filter
            </a>
            @else
            <a href="{{ route('client.services.index') }}" 
               class="inline-block px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">
                <i class="fas fa-plus mr-2"></i>
                Ajukan Permohonan Izin
            </a>
            @endif
        </div>
    @endif
</div>
@endsection
