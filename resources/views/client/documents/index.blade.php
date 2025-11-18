@extends('client.layouts.app')

@section('title', 'Dokumen')

@section('content')
@php
    $totalDocs = $stats['total'] ?? 0;
    $monthlyDocs = $stats['this_month'] ?? 0;
    $sortBy = request('sort_by', 'created_at');
    $sortOrder = request('sort_order', 'desc');
    $hasFilters = request()->except('page');
    
    // Calculate stats by category
    $categoryCounts = collect($documents)->groupBy('category')->map->count()->sortDesc();
    $topCategory = $categoryCounts->keys()->first() ?? 'N/A';
    $topCategoryCount = $categoryCounts->first() ?? 0;
@endphp

<!-- Mobile Hero -->
<div class="lg:hidden bg-[#0a66c2] border-y border-gray-200 dark:border-gray-700 text-white p-6">
    <div class="space-y-4">
        <div>
            <p class="text-xs text-white/70 uppercase tracking-widest leading-tight">Manajemen Dokumen</p>
            <h1 class="text-xl font-bold mt-1 leading-tight">{{ $totalDocs }} Dokumen Perizinan</h1>
        </div>
        
        <div class="grid grid-cols-2 gap-3">
            <div class="bg-white/10 backdrop-blur px-4 py-3">
                <p class="text-xs text-white/70 leading-tight">Total</p>
                <p class="text-2xl font-bold leading-tight mt-1">{{ $totalDocs }}</p>
            </div>
            <div class="bg-white/10 backdrop-blur px-4 py-3">
                <p class="text-xs text-white/70 leading-tight">Bulan Ini</p>
                <p class="text-2xl font-bold leading-tight mt-1">{{ $monthlyDocs }}</p>
            </div>
        </div>
        
        <div class="flex gap-3">
            <button onclick="openUploadModal()" class="flex-1 inline-flex items-center justify-center gap-2 bg-white text-[#0a66c2] font-semibold px-4 py-3 text-sm min-h-[44px] active:scale-95 transition-transform">
                <i class="fas fa-upload"></i> Upload
            </button>
            <a href="{{ route('client.projects.index') }}" class="flex-1 inline-flex items-center justify-center gap-2 bg-white/10 backdrop-blur border border-white/30 px-4 py-3 font-semibold text-sm min-h-[44px] active:scale-95 transition-transform">
                <i class="fas fa-folder"></i> Proyek
            </a>
        </div>
    </div>
</div>

<!-- Desktop Hero -->
<div class="hidden lg:block bg-[#0a66c2] border-y border-gray-200 dark:border-gray-700 text-white">
    <div class="max-w-6xl mx-auto px-6 lg:px-8 py-8">
        <div class="flex items-start justify-between gap-8 mb-6">
            <div class="flex-1">
                <h1 class="text-2xl lg:text-3xl font-bold leading-tight mb-2">
                    Manajemen {{ $totalDocs }} Dokumen Perizinan
                </h1>
                <p class="text-base text-white/90 leading-normal">
                    Upload, kelola, dan akses dokumen penting untuk semua proyek izin usaha Anda
                </p>
            </div>
            <div class="flex gap-3">
                <button onclick="openUploadModal()" class="inline-flex items-center gap-2 bg-white text-[#0a66c2] font-semibold px-5 py-3 hover:shadow-lg active:scale-95 transition-all">
                    <i class="fas fa-upload"></i> Upload Dokumen
                </button>
                <a href="{{ route('client.projects.index') }}" class="inline-flex items-center gap-2 bg-white/10 backdrop-blur border border-white/30 px-5 py-3 hover:bg-white/20 active:scale-95 transition-all">
                    <i class="fas fa-folder"></i> Lihat Proyek
                </a>
            </div>
        </div>
        
        <div class="grid grid-cols-4 gap-4">
            <div class="bg-white/10 backdrop-blur px-5 py-4">
                <p class="text-xs uppercase tracking-wider text-white/70 leading-tight">Total Dokumen</p>
                <p class="text-3xl font-bold leading-tight mt-2">{{ $totalDocs }}</p>
                <p class="text-xs text-white/60 mt-1">Semua Proyek</p>
            </div>
            <div class="bg-white/10 backdrop-blur px-5 py-4">
                <p class="text-xs uppercase tracking-wider text-white/70 leading-tight">Bulan Ini</p>
                <p class="text-3xl font-bold leading-tight mt-2">{{ $monthlyDocs }}</p>
                <p class="text-xs text-white/60 mt-1">Baru Ditambahkan</p>
            </div>
            <div class="bg-white/10 backdrop-blur px-5 py-4">
                <p class="text-xs uppercase tracking-wider text-white/70 leading-tight">Kategori Utama</p>
                <p class="text-3xl font-bold leading-tight mt-2">{{ $topCategoryCount }}</p>
                <p class="text-xs text-white/60 mt-1 capitalize">{{ $topCategory }}</p>
            </div>
            <div class="bg-white/10 backdrop-blur px-5 py-4">
                <p class="text-xs uppercase tracking-wider text-white/70 leading-tight">Per Proyek</p>
                <p class="text-3xl font-bold leading-tight mt-2">{{ $projects->count() > 0 ? number_format($totalDocs / $projects->count(), 1) : 0 }}</p>
                <p class="text-xs text-white/60 mt-1">Rata-rata</p>
            </div>
        </div>
    </div>
</div>

<!-- Documents List -->
@if($documents->count() > 0)
    <div class="space-y-1">
        @foreach($documents as $document)
        @php
            $fileExt = strtoupper($document->document_type ?? pathinfo($document->file_name, PATHINFO_EXTENSION));
            $fileSize = number_format(($document->file_size ?? 0) / 1024, 1);
            $daysAgo = (int) $document->created_at->diffInDays(now());
        @endphp
        <div class="bg-white dark:bg-gray-800 border-y border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
            <div class="px-4 lg:px-6 py-5">
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-[#0a66c2]/10 flex items-center justify-center">
                            <i class="fas fa-file text-xl text-[#0a66c2]"></i>
                        </div>
                    </div>
                    
                    <div class="flex-1 min-w-0 space-y-3">
                        <!-- Title & File Info -->
                        <div>
                            <div class="flex flex-wrap items-center gap-2 mb-1">
                                <span class="px-2.5 py-0.5 text-xs font-semibold rounded-full bg-[#0a66c2]/10 text-[#0a66c2]">
                                    {{ $fileExt }}
                                </span>
                                <span class="text-xs text-gray-500 dark:text-gray-400">{{ $fileSize }} KB</span>
                                @if($document->category)
                                <span class="px-2.5 py-0.5 text-xs font-semibold rounded-full bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300">
                                    <i class="fas fa-tag text-xs"></i> {{ ucfirst($document->category) }}
                                </span>
                                @endif
                            </div>
                            <p class="text-base font-semibold text-gray-900 dark:text-white leading-tight">
                                {{ $document->title }}
                            </p>
                            @if($document->description)
                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1 leading-normal">
                                {{ \Illuminate\Support\Str::limit($document->description, 120) }}
                            </p>
                            @endif
                        </div>
                        
                        <!-- Project & Upload Info -->
                        <div class="flex flex-wrap gap-x-4 gap-y-1 text-sm">
                            @if($document->project)
                            <span class="inline-flex items-center gap-1.5 text-gray-600 dark:text-gray-400">
                                <i class="fas fa-folder text-xs"></i>
                                <span class="font-medium">{{ $document->project->name }}</span>
                                <span class="text-gray-400">#{!! $document->project->id !!}</span>
                            </span>
                            @else
                            <span class="inline-flex items-center gap-1.5 text-gray-400">
                                <i class="fas fa-folder text-xs"></i>Tanpa Proyek
                            </span>
                            @endif
                            
                            <span class="inline-flex items-center gap-1.5 text-gray-600 dark:text-gray-400">
                                <i class="fas fa-calendar text-xs"></i>
                                @if($daysAgo === 0)
                                Hari ini
                                @elseif($daysAgo === 1)
                                Kemarin
                                @else
                                {{ $daysAgo }} hari lalu
                                @endif
                            </span>
                            
                            <span class="inline-flex items-center gap-1.5 text-gray-500 dark:text-gray-400">
                                <i class="fas fa-clock text-xs"></i>{{ $document->created_at->format('d M Y, H:i') }}
                            </span>
                        </div>
                        
                        <!-- Actions -->
                        <div class="flex gap-2 pt-2">
                            <a href="{{ route('client.documents.download', $document->id) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-[#0a66c2] text-white text-sm font-semibold hover:bg-[#004182] active:scale-95 transition-all">
                                <i class="fas fa-download"></i> Unduh
                            </a>
                            @if($document->file_path && Storage::exists($document->file_path))
                            <a href="{{ Storage::url($document->file_path) }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 text-sm font-semibold hover:bg-gray-200 dark:hover:bg-gray-600 active:scale-95 transition-all">
                                <i class="fas fa-eye"></i> Lihat
                            </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Pagination -->
    <div class="bg-white dark:bg-gray-800 border-y border-gray-200 dark:border-gray-700 px-4 lg:px-6 py-4">
        {{ $documents->withQueryString()->links() }}
    </div>
@else
    <!-- Empty State -->
    <div class="bg-white dark:bg-gray-800 border-y border-gray-200 dark:border-gray-700 py-16 text-center">
        <i class="fas fa-file text-gray-300 dark:text-gray-600 text-6xl mb-4"></i>
        <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2 leading-tight">
            @if($hasFilters)
                Dokumen Tidak Ditemukan
            @else
                Belum Ada Dokumen
            @endif
        </h3>
        <p class="text-base text-gray-600 dark:text-gray-400 mb-6 leading-normal">
            @if($hasFilters)
                Tidak ada dokumen yang sesuai dengan filter Anda
            @else
                Mulai upload dokumen pendukung untuk proyek perizinan Anda
            @endif
        </p>
        @if($hasFilters)
        <a href="{{ route('client.documents.index') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-200 font-semibold hover:bg-gray-200 dark:hover:bg-gray-700 active:scale-95 transition-all">
            <i class="fas fa-redo"></i> Reset Filter
        </a>
        @else
        <button onclick="openUploadModal()" class="inline-flex items-center gap-2 px-6 py-3 bg-[#0a66c2] hover:bg-[#004182] text-white font-semibold active:scale-95 transition-all">
            <i class="fas fa-upload"></i> Upload Dokumen
        </button>
        @endif
    </div>
@endif

<!-- Upload Modal -->
<div id="uploadModal" class="hidden fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4">
    <div class="bg-white dark:bg-gray-900 shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto border border-gray-200 dark:border-gray-700">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
            <div>
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white">Upload Dokumen</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Tambahkan dokumen pendukung untuk proyek izin</p>
            </div>
            <button onclick="closeUploadModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <form action="{{ route('client.documents.store') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-5">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Pilih Proyek <span class="text-red-500">*</span>
                </label>
                <select name="project_id" required class="w-full px-4 py-3 border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-[#0a66c2] focus:border-transparent transition">
                    <option value="">-- Pilih Proyek --</option>
                    @foreach($projects as $project)
                        <option value="{{ $project->id }}">{{ $project->name }}</option>
                    @endforeach
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Judul Dokumen <span class="text-red-500">*</span>
                </label>
                <input type="text" name="title" required maxlength="255" placeholder="Contoh: Sertifikat Standar" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-[#0a66c2] focus:border-transparent transition">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Kategori <span class="text-red-500">*</span>
                </label>
                <select name="category" required class="w-full px-4 py-3 border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-[#0a66c2] focus:border-transparent transition">
                    <option value="">-- Pilih Kategori --</option>
                    @foreach(['akta','npwp','sertifikat','uji-lab','desain','surat','izin','lainnya'] as $category)
                        <option value="{{ $category }}">{{ ucfirst($category) }}</option>
                    @endforeach
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Deskripsi
                </label>
                <textarea name="description" rows="3" maxlength="500" placeholder="Tambahkan catatan penting atau detail dokumen" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-[#0a66c2] focus:border-transparent transition"></textarea>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Maksimal 500 karakter</p>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Upload File <span class="text-red-500">*</span>
                </label>
                <input type="file" name="file" required accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-[#0a66c2] focus:border-transparent transition">
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Format: PDF, Word, Excel, JPG, PNG. Maksimal 10MB</p>
            </div>
            
            <div class="flex flex-col sm:flex-row gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                <button type="button" onclick="closeUploadModal()" class="flex-1 px-6 py-3 border border-gray-300 dark:border-gray-700 text-gray-700 dark:text-gray-200 font-semibold hover:bg-gray-50 dark:hover:bg-gray-800 active:scale-95 transition-all">
                    Batal
                </button>
                <button type="submit" class="flex-1 px-6 py-3 bg-[#0a66c2] hover:bg-[#004182] text-white font-semibold active:scale-95 transition-all">
                    <i class="fas fa-upload mr-2"></i> Upload
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function openUploadModal() {
    const modal = document.getElementById('uploadModal');
    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}
function closeUploadModal() {
    const modal = document.getElementById('uploadModal');
    modal.classList.add('hidden');
    document.body.style.overflow = 'auto';
}
document.getElementById('uploadModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closeUploadModal();
    }
});
</script>
@endpush
@endsection
