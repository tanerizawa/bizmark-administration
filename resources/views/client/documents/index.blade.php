@extends('client.layouts.app')

@section('title', 'Dokumen')
@section('page-title', 'Dokumen Perizinan')
@section('page-subtitle', 'Akses dan download semua dokumen proyek Anda')

@section('content')
<div class="space-y-6">
    <!-- Header with Upload Button -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Dokumen Perizinan</h2>
            <p class="text-gray-600 mt-1">Akses dan upload dokumen proyek Anda</p>
        </div>
        <button 
            onclick="openUploadModal()"
            class="px-6 py-3 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition flex items-center gap-2"
        >
            <i class="fas fa-upload"></i>Upload Dokumen
        </button>
    </div>

    <!-- Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Total Dokumen</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">{{ $stats['total'] }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-file-alt text-blue-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Dokumen Bulan Ini</p>
                    <p class="text-3xl font-bold text-purple-600 mt-2">{{ $stats['this_month'] }}</p>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-calendar text-purple-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters & Search -->
    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
        <form method="GET" action="{{ route('client.documents.index') }}" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <!-- Search -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-search mr-2"></i>Cari Dokumen
                    </label>
                    <input 
                        type="text" 
                        name="search" 
                        value="{{ request('search') }}"
                        placeholder="Nama dokumen..."
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                    >
                </div>

                <!-- Project Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-folder mr-2"></i>Proyek
                    </label>
                    <select 
                        name="project_id" 
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                    >
                        <option value="">Semua Proyek</option>
                        @foreach($projects as $project)
                            <option value="{{ $project->id }}" {{ request('project_id') == $project->id ? 'selected' : '' }}>
                                {{ $project->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Type Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-filter mr-2"></i>Kategori Dokumen
                    </label>
                    <select 
                        name="category" 
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                    >
                        <option value="">Semua Kategori</option>
                        @foreach($documentTypes as $type)
                            <option value="{{ $type }}" {{ request('category') == $type ? 'selected' : '' }}>
                                {{ ucfirst($type) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Sort -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-sort mr-2"></i>Urutkan
                    </label>
                    <select 
                        name="sort_by" 
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                    >
                        <option value="created_at" {{ request('sort_by') == 'created_at' ? 'selected' : '' }}>Terbaru</option>
                        <option value="name" {{ request('sort_by') == 'name' ? 'selected' : '' }}>Nama A-Z</option>
                    </select>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <button 
                    type="submit" 
                    class="px-6 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition"
                >
                    <i class="fas fa-search mr-2"></i>Filter
                </button>
                <a 
                    href="{{ route('client.documents.index') }}" 
                    class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition"
                >
                    <i class="fas fa-redo mr-2"></i>Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Documents List -->
    @if($documents->count() > 0)
        <div class="bg-white rounded-xl shadow-sm border border-gray-100">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                <i class="fas fa-file mr-2"></i>Nama Dokumen
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                <i class="fas fa-folder mr-2"></i>Proyek
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                <i class="fas fa-tag mr-2"></i>Tipe
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                <i class="fas fa-calendar mr-2"></i>Tanggal Upload
                            </th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                <i class="fas fa-cog mr-2"></i>Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($documents as $document)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                            @php
                                                $extension = pathinfo($document->file_path, PATHINFO_EXTENSION);
                                                $iconClass = 'fa-file';
                                                $iconColor = 'text-gray-600';
                                                
                                                if (in_array($extension, ['pdf'])) {
                                                    $iconClass = 'fa-file-pdf';
                                                    $iconColor = 'text-red-600';
                                                } elseif (in_array($extension, ['doc', 'docx'])) {
                                                    $iconClass = 'fa-file-word';
                                                    $iconColor = 'text-blue-600';
                                                } elseif (in_array($extension, ['xls', 'xlsx'])) {
                                                    $iconClass = 'fa-file-excel';
                                                    $iconColor = 'text-green-600';
                                                } elseif (in_array($extension, ['jpg', 'jpeg', 'png', 'gif'])) {
                                                    $iconClass = 'fa-file-image';
                                                    $iconColor = 'text-purple-600';
                                                }
                                            @endphp
                                            <i class="fas {{ $iconClass }} {{ $iconColor }}"></i>
                                        </div>
                                        <div>
                                            <p class="font-medium text-gray-900">{{ $document->title }}</p>
                                            @if($document->description)
                                                <p class="text-sm text-gray-500">{{ Str::limit($document->description, 50) }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <a href="{{ route('client.projects.show', $document->project_id) }}" 
                                       class="text-purple-600 hover:text-purple-800 font-medium">
                                        {{ $document->project->name }}
                                    </a>
                                </td>
                                <td class="px-6 py-4">
                                    @if($document->category)
                                        <span class="px-3 py-1 text-xs font-semibold bg-gray-100 text-gray-800 rounded-full">
                                            {{ ucfirst($document->category) }}
                                        </span>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    {{ $document->created_at->format('d M Y, H:i') }}
                                    <p class="text-xs text-gray-400">{{ $document->created_at->diffForHumans() }}</p>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <a 
                                        href="{{ route('client.documents.download', $document->id) }}" 
                                        class="inline-flex items-center px-4 py-2 bg-purple-600 text-white text-sm rounded-lg hover:bg-purple-700 transition"
                                    >
                                        <i class="fas fa-download mr-2"></i>Download
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination -->
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
            {{ $documents->links() }}
        </div>
    @else
        <!-- Empty State -->
        <div class="bg-white rounded-xl shadow-sm p-12 border border-gray-100 text-center">
            <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-file-alt text-gray-400 text-4xl"></i>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-2">Tidak Ada Dokumen</h3>
            <p class="text-gray-600 mb-6">
                @if(request('search') || request('project_id') || request('type'))
                    Tidak ada dokumen yang sesuai dengan filter Anda.
                @else
                    Belum ada dokumen yang tersedia untuk proyek Anda.
                @endif
            </p>
            @if(request('search') || request('project_id') || request('type'))
                <a 
                    href="{{ route('client.documents.index') }}" 
                    class="inline-block px-6 py-3 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition"
                >
                    <i class="fas fa-redo mr-2"></i>Reset Filter
                </a>
            @endif
        </div>
    @endif
</div>

<!-- Upload Modal -->
<div id="uploadModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
        <div class="p-6 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 class="text-xl font-bold text-gray-900">
                    <i class="fas fa-upload text-purple-600 mr-2"></i>Upload Dokumen Baru
                </h3>
                <button 
                    onclick="closeUploadModal()"
                    class="text-gray-400 hover:text-gray-600 transition"
                >
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
        </div>

        <form action="{{ route('client.documents.store') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-6">
            @csrf

            <!-- Project Selection -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-folder mr-2"></i>Pilih Proyek <span class="text-red-500">*</span>
                </label>
                <select 
                    name="project_id" 
                    required
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                >
                    <option value="">-- Pilih Proyek --</option>
                    @foreach($projects as $project)
                        <option value="{{ $project->id }}">{{ $project->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Document Title -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-heading mr-2"></i>Judul Dokumen <span class="text-red-500">*</span>
                </label>
                <input 
                    type="text" 
                    name="title" 
                    required
                    maxlength="255"
                    placeholder="Contoh: Akta Pendirian Perusahaan"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                >
            </div>

            <!-- Category -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-tag mr-2"></i>Kategori Dokumen <span class="text-red-500">*</span>
                </label>
                <select 
                    name="category" 
                    required
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                >
                    <option value="">-- Pilih Kategori --</option>
                    <option value="akta">Akta Perusahaan</option>
                    <option value="npwp">NPWP</option>
                    <option value="sertifikat">Sertifikat</option>
                    <option value="uji-lab">Hasil Uji Lab</option>
                    <option value="desain">Desain/Label</option>
                    <option value="surat">Surat Keterangan</option>
                    <option value="izin">Izin/Lisensi</option>
                    <option value="lainnya">Lainnya</option>
                </select>
            </div>

            <!-- Description -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-align-left mr-2"></i>Deskripsi (Opsional)
                </label>
                <textarea 
                    name="description" 
                    rows="3"
                    maxlength="500"
                    placeholder="Tambahkan catatan atau deskripsi dokumen..."
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                ></textarea>
                <p class="text-xs text-gray-500 mt-1">Maksimal 500 karakter</p>
            </div>

            <!-- File Upload -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-file mr-2"></i>Upload File <span class="text-red-500">*</span>
                </label>
                <input 
                    type="file" 
                    name="file" 
                    required
                    accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                >
                <p class="text-xs text-gray-500 mt-1">
                    Format: PDF, DOC, DOCX, XLS, XLSX, JPG, PNG. Maksimal 10MB
                </p>
            </div>

            <!-- Actions -->
            <div class="flex items-center gap-3 pt-4 border-t border-gray-200">
                <button 
                    type="button"
                    onclick="closeUploadModal()"
                    class="flex-1 px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition"
                >
                    Batal
                </button>
                <button 
                    type="submit"
                    class="flex-1 px-6 py-3 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition"
                >
                    <i class="fas fa-upload mr-2"></i>Upload Dokumen
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openUploadModal() {
    document.getElementById('uploadModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeUploadModal() {
    document.getElementById('uploadModal').classList.add('hidden');
    document.body.style.overflow = 'auto';
}

// Close modal when clicking outside
document.getElementById('uploadModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closeUploadModal();
    }
});
</script>

@endsection
