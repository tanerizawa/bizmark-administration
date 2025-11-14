@extends('client.layouts.app')

@section('title', 'Dokumen')

@section('content')
@php
    $totalDocs = $stats['total'] ?? 0;
    $monthlyDocs = $stats['this_month'] ?? 0;
    $sortBy = request('sort_by', 'created_at');
    $sortOrder = request('sort_order', 'desc');
    $hasFilters = request()->except('page');
@endphp

<div class="space-y-8">
    <!-- Hero -->
    <div class="bg-gradient-to-r from-indigo-700 via-indigo-600 to-blue-500 text-white rounded-3xl shadow-xl overflow-hidden relative">
        <div class="absolute inset-0 opacity-10 bg-[url('https://www.toptal.com/designers/subtlepatterns/uploads/dot-grid.png')]"></div>
        <div class="relative p-6 lg:p-8 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
            <div class="space-y-3 flex-1">
                <p class="text-xs uppercase tracking-[0.35em] text-white/70">Manajemen Dokumen</p>
                <h1 class="text-3xl font-bold leading-tight">Semua dokumen perizinan Anda tersimpan aman dan siap diunduh kapan saja.</h1>
                <p class="text-white/80">Upload bukti, surat resmi, atau sertifikat penting dan hubungkan langsung dengan proyek yang berjalan.</p>
                <div class="flex flex-wrap gap-3">
                    <button onclick="openUploadModal()" class="inline-flex items-center gap-2 bg-white text-indigo-700 font-semibold px-5 py-3 rounded-xl shadow">
                        <i class="fas fa-upload"></i> Upload Dokumen
                    </button>
                    <a href="{{ route('client.projects.index') }}" class="inline-flex items-center gap-2 bg-white/10 border border-white/30 px-5 py-3 rounded-xl font-semibold">
                        <i class="fas fa-folder"></i> Lihat Proyek
                    </a>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4 w-full lg:w-auto">
                <div class="bg-white/15 rounded-2xl p-4 backdrop-blur">
                    <p class="text-xs uppercase tracking-wide text-white/70">Total Dokumen</p>
                    <p class="text-3xl font-bold">{{ $totalDocs }}</p>
                    <p class="text-xs text-white/70 mt-1">tersimpan sepanjang proyek</p>
                </div>
                <div class="bg-white/15 rounded-2xl p-4 backdrop-blur">
                    <p class="text-xs uppercase tracking-wide text-white/70">Bulan Ini</p>
                    <p class="text-3xl font-bold">{{ $monthlyDocs }}</p>
                    <p class="text-xs text-white/70 mt-1">dokumen baru diunggah</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-800 p-6 space-y-4">
        <form method="GET" action="{{ route('client.documents.index') }}" class="grid grid-cols-1 lg:grid-cols-4 gap-4">
            <div>
                <label class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Cari Dokumen</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Contoh: Sertifikat Halal" class="mt-1 w-full px-4 py-2.5 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-indigo-500 dark:bg-gray-800 dark:text-white">
            </div>
            <div>
                <label class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Proyek</label>
                <select name="project_id" class="mt-1 w-full px-4 py-2.5 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-indigo-500 dark:bg-gray-800 dark:text-white">
                    <option value="">Semua Proyek</option>
                    @foreach($projects as $project)
                        <option value="{{ $project->id }}" {{ request('project_id') == $project->id ? 'selected' : '' }}>
                            {{ $project->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Kategori</label>
                <select name="category" class="mt-1 w-full px-4 py-2.5 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-indigo-500 dark:bg-gray-800 dark:text-white">
                    <option value="">Semua Kategori</option>
                    @foreach($documentTypes as $type)
                        <option value="{{ $type }}" {{ request('category') == $type ? 'selected' : '' }}>{{ ucfirst($type) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="grid grid-cols-2 gap-2">
                <div>
                    <label class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Urutkan</label>
                    <select name="sort_by" class="mt-1 w-full px-3 py-2 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-indigo-500 dark:bg-gray-800 dark:text-white">
                        <option value="created_at" {{ $sortBy === 'created_at' ? 'selected' : '' }}>Tanggal unggah</option>
                        <option value="title" {{ $sortBy === 'title' ? 'selected' : '' }}>Nama dokumen</option>
                    </select>
                </div>
                <div>
                    <label class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Urutan</label>
                    <select name="sort_order" class="mt-1 w-full px-3 py-2 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-indigo-500 dark:bg-gray-800 dark:text-white">
                        <option value="desc" {{ $sortOrder === 'desc' ? 'selected' : '' }}>Terbaru</option>
                        <option value="asc" {{ $sortOrder === 'asc' ? 'selected' : '' }}>Terlama</option>
                    </select>
                </div>
            </div>
            <div class="lg:col-span-4 flex flex-wrap gap-2 justify-end">
                <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-semibold">
                    <i class="fas fa-filter"></i> Terapkan
                </button>
                @if($hasFilters)
                <a href="{{ route('client.documents.index') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-200 rounded-xl font-semibold">
                    <i class="fas fa-redo"></i> Reset
                </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Documents -->
    @if($documents->count() > 0)
    <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm overflow-hidden">
        <div class="hidden lg:grid lg:grid-cols-12 bg-gray-50 dark:bg-gray-800 text-xs font-semibold text-gray-500 dark:text-gray-400 px-6 py-3">
            <div class="col-span-4">Nama Dokumen</div>
            <div class="col-span-3">Proyek</div>
            <div class="col-span-2">Kategori</div>
            <div class="col-span-2">Diunggah</div>
            <div class="col-span-1 text-center">Aksi</div>
        </div>
        <div class="divide-y divide-gray-100 dark:divide-gray-800">
            @foreach($documents as $document)
            <div class="px-6 py-4 flex flex-col lg:grid lg:grid-cols-12 gap-3">
                <div class="lg:col-span-4">
                    <p class="font-semibold text-gray-900 dark:text-white">{{ $document->title }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 flex items-center gap-2 mt-1">
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-indigo-50 text-indigo-600 text-[11px]">
                            <i class="fas fa-file"></i>{{ strtoupper($document->document_type ?? pathinfo($document->file_name, PATHINFO_EXTENSION)) }}
                        </span>
                        <span>{{ number_format(($document->file_size ?? 0) / 1024, 1) }} KB</span>
                    </p>
                    @if($document->description)
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ \Illuminate\Support\Str::limit($document->description, 80) }}</p>
                    @endif
                </div>
                <div class="lg:col-span-3 text-sm text-gray-700 dark:text-gray-300">
                    <p class="font-medium">{{ $document->project->name ?? 'Tanpa Proyek' }}</p>
                    <p class="text-xs text-gray-400">#{{ $document->project->id ?? '-' }}</p>
                </div>
                <div class="lg:col-span-2">
                    @if($document->category)
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-200">
                            {{ ucfirst($document->category) }}
                        </span>
                    @else
                        <span class="text-xs text-gray-400">-</span>
                    @endif
                </div>
                <div class="lg:col-span-2 text-sm text-gray-600 dark:text-gray-400">
                    {{ optional($document->created_at)->format('d M Y, H:i') }}
                    <p class="text-xs text-gray-400">{{ optional($document->created_at)->diffForHumans() }}</p>
                </div>
                <div class="lg:col-span-1 flex lg:justify-center">
                    <a href="{{ route('client.documents.download', $document->id) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white text-xs font-semibold rounded-full hover:bg-indigo-700">
                        <i class="fas fa-download"></i> Unduh
                    </a>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm p-4">
        {{ $documents->withQueryString()->links() }}
    </div>
    @else
    <div class="bg-white dark:bg-gray-900 rounded-2xl border border-dashed border-gray-200 dark:border-gray-700 p-12 text-center">
        <div class="w-20 h-20 rounded-full bg-gray-100 dark:bg-gray-800 flex items-center justify-center mx-auto mb-4">
            <i class="fas fa-file text-3xl text-gray-400"></i>
        </div>
        <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">Dokumen tidak ditemukan</h3>
        <p class="text-gray-600 dark:text-gray-400 mb-6">
            @if($hasFilters)
                Tidak ada dokumen yang sesuai dengan filter Anda.
            @else
                Mulai unggah dokumen pendukung untuk proyek Anda.
            @endif
        </p>
        @if($hasFilters)
        <a href="{{ route('client.documents.index') }}" class="inline-flex items-center gap-2 px-6 py-2 bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-200 rounded-xl font-semibold">
            <i class="fas fa-redo"></i> Reset Filter
        </a>
        @else
        <button onclick="openUploadModal()" class="inline-flex items-center gap-2 px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-semibold">
            <i class="fas fa-upload"></i> Upload Dokumen
        </button>
        @endif
    </div>
    @endif
</div>

<!-- Upload Modal -->
<div id="uploadModal" class="hidden fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4">
    <div class="bg-white dark:bg-gray-900 rounded-3xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto border border-gray-100 dark:border-gray-800">
        <div class="p-6 border-b border-gray-100 dark:border-gray-800 flex items-center justify-between">
            <div>
                <p class="text-xs uppercase tracking-[0.3em] text-gray-400 dark:text-gray-500">Unggah Dokumen</p>
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white">Tambahkan dokumen pendukung izin</h3>
            </div>
            <button onclick="closeUploadModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200"><i class="fas fa-times text-xl"></i></button>
        </div>
        <form action="{{ route('client.documents.store') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-5">
            @csrf
            <div>
                <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Pilih Proyek <span class="text-red-500">*</span></label>
                <select name="project_id" required class="mt-1 w-full px-4 py-3 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-indigo-500 dark:bg-gray-800 dark:text-white">
                    <option value="">-- Pilih Proyek --</option>
                    @foreach($projects as $project)
                        <option value="{{ $project->id }}">{{ $project->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Judul Dokumen <span class="text-red-500">*</span></label>
                <input type="text" name="title" required maxlength="255" placeholder="Contoh: Sertifikat Standar" class="mt-1 w-full px-4 py-3 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-indigo-500 dark:bg-gray-800 dark:text-white">
            </div>
            <div>
                <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Kategori <span class="text-red-500">*</span></label>
                <select name="category" required class="mt-1 w-full px-4 py-3 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-indigo-500 dark:bg-gray-800 dark:text-white">
                    <option value="">-- Pilih Kategori --</option>
                    @foreach(['akta','npwp','sertifikat','uji-lab','desain','surat','izin','lainnya'] as $category)
                        <option value="{{ $category }}">{{ ucfirst($category) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Deskripsi</label>
                <textarea name="description" rows="3" maxlength="500" placeholder="Tambahkan catatan penting atau detail dokumen" class="mt-1 w-full px-4 py-3 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-indigo-500 dark:bg-gray-800 dark:text-white"></textarea>
                <p class="text-xs text-gray-400 mt-1">Maksimal 500 karakter</p>
            </div>
            <div>
                <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Upload File <span class="text-red-500">*</span></label>
                <input type="file" name="file" required accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png" class="mt-1 w-full px-4 py-3 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-indigo-500 dark:bg-gray-800 dark:text-white">
                <p class="text-xs text-gray-400 mt-1">Format: PDF, Word, Excel, JPG, PNG. Maksimal 10MB.</p>
            </div>
            <div class="flex flex-col sm:flex-row gap-3 pt-4 border-t border-gray-100 dark:border-gray-800">
                <button type="button" onclick="closeUploadModal()" class="flex-1 px-6 py-3 border border-gray-300 dark:border-gray-700 text-gray-700 dark:text-gray-200 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-800">
                    Batal
                </button>
                <button type="submit" class="flex-1 px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-semibold">
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
