@extends('layouts.app')

@section('title', 'Dokumen')
@section('page-title', 'Manajemen Dokumen')

@section('content')
    {{-- Hero Section --}}
    <section class="card-elevated rounded-apple-xl p-5 md:p-6 relative overflow-hidden mb-6">
        <div class="absolute inset-0 pointer-events-none" aria-hidden="true">
            <div class="w-72 h-72 bg-apple-blue opacity-30 blur-3xl rounded-full absolute -top-16 -right-10"></div>
            <div class="w-48 h-48 bg-apple-green opacity-20 blur-2xl rounded-full absolute bottom-0 left-10"></div>
        </div>
        <div class="relative space-y-5 md:space-y-6">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-5">
                <div class="space-y-2.5 max-w-3xl">
                    <p class="text-sm uppercase tracking-[0.4em]" style="color: rgba(235,235,245,0.5);">Manajemen Dokumen</p>
                    <h1 class="text-2xl md:text-3xl font-bold" style="color: #FFFFFF;">
                        Arsip Digital Dokumen Perizinan
                    </h1>
                    <p class="text-sm md:text-base" style="color: rgba(235,235,245,0.75);">
                        Simpan, kelola, dan akses semua dokumen perizinan secara terpusat dengan sistem keamanan dan versioning yang terstruktur.
                    </p>
                </div>
                <div class="space-y-2.5">
                    <a href="{{ route('documents.create') }}" 
                       class="inline-flex items-center px-4 py-2 rounded-apple text-sm font-semibold" 
                       style="background: rgba(10,132,255,0.25); color: rgba(235,235,245,0.9);">
                        <i class="fas fa-upload mr-2"></i>
                        Upload Dokumen
                        <i class="fas fa-arrow-right ml-2 text-xs"></i>
                    </a>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3 md:gap-4">
                @php
                    $totalDocs = $documents->total();
                    $totalSize = $documents->sum('file_size');
                    $perizinanCount = $documents->where('category', 'perizinan')->count();
                    $confidentialCount = $documents->where('is_confidential', true)->count();
                    
                    // Format file size
                    $formattedSize = $totalSize >= 1073741824 ? 
                        number_format($totalSize / 1073741824, 2) . ' GB' : 
                        ($totalSize >= 1048576 ? 
                            number_format($totalSize / 1048576, 2) . ' MB' : 
                            number_format($totalSize / 1024, 2) . ' KB');
                @endphp

                <!-- Total Dokumen -->
                <div class="rounded-apple-lg p-3.5 md:p-4" style="background: rgba(10,132,255,0.12);">
                    <p class="text-xs uppercase tracking-widest" style="color: rgba(10,132,255,0.9);">Total Dokumen</p>
                    <h2 class="text-2xl font-bold mt-1.5" style="color: #FFFFFF;">{{ $totalDocs }}</h2>
                    <p class="text-xs" style="color: rgba(235,235,245,0.6);">Berkas tersimpan</p>
                </div>

                <!-- Total Size -->
                <div class="rounded-apple-lg p-3.5 md:p-4" style="background: rgba(175,82,222,0.12);">
                    <p class="text-xs uppercase tracking-widest" style="color: rgba(175,82,222,0.9);">Total Ukuran</p>
                    <h2 class="text-2xl font-bold mt-1.5" style="color: rgba(175,82,222,1);">{{ $formattedSize }}</h2>
                    <p class="text-xs" style="color: rgba(235,235,245,0.6);">Kapasitas terpakai</p>
                </div>

                <!-- Perizinan -->
                <div class="rounded-apple-lg p-3.5 md:p-4" style="background: rgba(255,159,10,0.12);">
                    <p class="text-xs uppercase tracking-widest" style="color: rgba(255,159,10,0.9);">Perizinan</p>
                    <h2 class="text-2xl font-bold mt-1.5" style="color: rgba(255,159,10,1);">{{ $perizinanCount }}</h2>
                    <p class="text-xs" style="color: rgba(235,235,245,0.6);">Dokumen izin</p>
                </div>

                <!-- Confidential -->
                <div class="rounded-apple-lg p-3.5 md:p-4" style="background: rgba(255,59,48,0.12);">
                    <p class="text-xs uppercase tracking-widest" style="color: rgba(255,59,48,0.9);">Rahasia</p>
                    <h2 class="text-2xl font-bold mt-1.5" style="color: rgba(255,59,48,1);">{{ $confidentialCount }}</h2>
                    <p class="text-xs" style="color: rgba(235,235,245,0.6);">Akses terbatas</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Search and Filter Card -->
    <div class="card-elevated rounded-apple-lg mb-4 overflow-hidden">
        <div class="px-4 py-3" style="border-bottom: 1px solid rgba(84, 84, 88, 0.65);">
            <h3 class="text-base font-semibold" style="color: #FFFFFF;">Pencarian & Filter</h3>
        </div>
        <div class="p-4">
            <form method="GET" action="{{ route('documents.index') }}" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3">
                    <!-- Search -->
                    <div>
                        <label class="block text-xs font-medium mb-1.5" style="color: rgba(235, 235, 245, 0.6);">Pencarian</label>
                        <div class="relative">
                            <input type="text" 
                                   name="search" 
                                   value="{{ request('search') }}" 
                                   placeholder="Judul, deskripsi, atau nama file..." 
                                   class="input-dark w-full pl-9 pr-3 py-2 rounded-apple text-sm">
                            <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-sm" style="color: rgba(235, 235, 245, 0.3);"></i>
                        </div>
                    </div>

                    <!-- Category -->
                    <div>
                        <label class="block text-xs font-medium mb-1.5" style="color: rgba(235, 235, 245, 0.6);">Kategori</label>
                        <select name="category" class="input-dark w-full px-3 py-2 rounded-apple text-sm">
                            <option value="">Semua Kategori</option>
                            @isset($categories)
                                @foreach($categories as $cat)
                                    <option value="{{ $cat }}" {{ request('category') == $cat ? 'selected' : '' }}>
                                        {{ ucfirst($cat) }}
                                    </option>
                                @endforeach
                            @endisset
                        </select>
                    </div>

                    <!-- Document Type -->
                    <div>
                        <label class="block text-xs font-medium mb-1.5" style="color: rgba(235, 235, 245, 0.6);">Tipe Dokumen</label>
                        <select name="document_type" class="input-dark w-full px-3 py-2 rounded-apple text-sm">
                            <option value="">Semua Tipe</option>
                            @isset($documentTypes)
                                @foreach($documentTypes as $type)
                                    <option value="{{ $type }}" {{ request('document_type') == $type ? 'selected' : '' }}>
                                        {{ ucfirst($type) }}
                                    </option>
                                @endforeach
                            @endisset
                        </select>
                    </div>

                    <!-- Project -->
                    <div>
                        <label class="block text-xs font-medium mb-1.5" style="color: rgba(235, 235, 245, 0.6);">Proyek</label>
                        <select name="project_id" class="input-dark w-full px-3 py-2 rounded-apple text-sm">
                            <option value="">Semua Proyek</option>
                            @isset($projects)
                                @foreach($projects as $project)
                                    <option value="{{ $project->id }}" {{ request('project_id') == $project->id ? 'selected' : '' }}>
                                        {{ $project->name }}
                                    </option>
                                @endforeach
                            @endisset
                        </select>
                    </div>
                </div>
                <script>
                // Auto submit form on filter change
                document.addEventListener('DOMContentLoaded', function() {
                    const form = document.querySelector('form[action="{{ route('documents.index') }}"]');
                    if (!form) return;
                    
                    const searchInput = form.querySelector('input[name="search"]');
                    
                    // Auto-submit for select dropdowns
                    form.querySelectorAll('select[name]').forEach(function(el) {
                        el.addEventListener('change', function() {
                            form.submit();
                        });
                    });
                    
                    // Submit search on Enter key only
                    if (searchInput) {
                        searchInput.addEventListener('keydown', function(e) {
                            if (e.key === 'Enter') {
                                e.preventDefault();
                                e.stopPropagation();
                                form.submit();
                            }
                        });
                    }
                });
                </script>
            </form>
        </div>
    </div>

    <!-- Documents Table -->
    <div class="card-elevated rounded-apple-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-700">
                <thead style="background-color: var(--dark-bg-secondary);">
                    <tr>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-dark-text-secondary">Dokumen</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-dark-text-secondary">Kategori</th>
                        <th scope="col" class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wider text-dark-text-secondary">Tipe Berkas</th>
                        <th scope="col" class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wider text-dark-text-secondary">Ukuran</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-dark-text-secondary">Proyek</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-dark-text-secondary">Info Unggahan</th>
                        <th scope="col" class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wider text-dark-text-secondary">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-700" style="background-color: var(--dark-bg-secondary);">
                    @forelse($documents as $document)
                        @php
                            // File type icon configuration
                            $extension = strtolower(pathinfo($document->file_name, PATHINFO_EXTENSION));
                            $fileTypeConfig = [
                                'pdf' => ['icon' => 'fa-file-pdf', 'color' => 'rgba(255, 59, 48, 1)', 'bg' => 'rgba(255, 59, 48, 0.15)'],
                                'doc' => ['icon' => 'fa-file-word', 'color' => 'rgba(0, 122, 255, 1)', 'bg' => 'rgba(0, 122, 255, 0.15)'],
                                'docx' => ['icon' => 'fa-file-word', 'color' => 'rgba(0, 122, 255, 1)', 'bg' => 'rgba(0, 122, 255, 0.15)'],
                                'xls' => ['icon' => 'fa-file-excel', 'color' => 'rgba(52, 199, 89, 1)', 'bg' => 'rgba(52, 199, 89, 0.15)'],
                                'xlsx' => ['icon' => 'fa-file-excel', 'color' => 'rgba(52, 199, 89, 1)', 'bg' => 'rgba(52, 199, 89, 0.15)'],
                                'jpg' => ['icon' => 'fa-file-image', 'color' => 'rgba(175, 82, 222, 1)', 'bg' => 'rgba(175, 82, 222, 0.15)'],
                                'jpeg' => ['icon' => 'fa-file-image', 'color' => 'rgba(175, 82, 222, 1)', 'bg' => 'rgba(175, 82, 222, 0.15)'],
                                'png' => ['icon' => 'fa-file-image', 'color' => 'rgba(175, 82, 222, 1)', 'bg' => 'rgba(175, 82, 222, 0.15)'],
                                'zip' => ['icon' => 'fa-file-archive', 'color' => 'rgba(255, 149, 0, 1)', 'bg' => 'rgba(255, 149, 0, 0.15)'],
                                'rar' => ['icon' => 'fa-file-archive', 'color' => 'rgba(255, 149, 0, 1)', 'bg' => 'rgba(255, 149, 0, 0.15)'],
                            ];
                            $fileType = $fileTypeConfig[$extension] ?? ['icon' => 'fa-file-alt', 'color' => 'rgba(142, 142, 147, 1)', 'bg' => 'rgba(142, 142, 147, 0.15)'];

                            // Category configuration
                            $categoryConfig = [
                                'perizinan' => ['icon' => 'fa-file-contract', 'color' => 'rgba(255, 159, 10, 1)', 'bg' => 'rgba(255, 159, 10, 0.15)'],
                                'kontrak' => ['icon' => 'fa-file-signature', 'color' => 'rgba(175, 82, 222, 1)', 'bg' => 'rgba(175, 82, 222, 0.15)'],
                                'laporan' => ['icon' => 'fa-file-chart-line', 'color' => 'rgba(52, 199, 89, 1)', 'bg' => 'rgba(52, 199, 89, 0.15)'],
                                'teknis' => ['icon' => 'fa-file-code', 'color' => 'rgba(0, 122, 255, 1)', 'bg' => 'rgba(0, 122, 255, 0.15)'],
                            ];
                            $category = $categoryConfig[$document->category] ?? ['icon' => 'fa-folder', 'color' => 'rgba(142, 142, 147, 1)', 'bg' => 'rgba(142, 142, 147, 0.15)'];

                            // Format file size
                            $fileSize = $document->file_size;
                            $formattedFileSize = $fileSize >= 1048576 ? 
                                number_format($fileSize / 1048576, 2) . ' MB' : 
                                number_format($fileSize / 1024, 2) . ' KB';
                        @endphp

                        <tr class="hover-lift transition-apple" style="cursor: pointer;" onclick="window.location='{{ route('documents.show', $document) }}'">
                            <!-- Dokumen Info -->
                            <td class="px-4 py-3">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 rounded-lg flex items-center justify-center mr-3" style="background-color: {{ $fileType['bg'] }};">
                                        <i class="fas {{ $fileType['icon'] }} text-lg" style="color: {{ $fileType['color'] }};"></i>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center">
                                            <div class="font-semibold text-sm text-dark-text-primary truncate">
                                                {{ $document->title }}
                                            </div>
                                            @if($document->is_confidential)
                                                <i class="fas fa-lock text-xs ml-2" style="color: rgba(255, 59, 48, 1);" title="Rahasia"></i>
                                            @endif
                                            @if($document->version > 1)
                                                <span class="text-xs ml-2 px-2 py-0.5 rounded-full" style="background-color: rgba(0, 122, 255, 0.15); color: rgba(0, 122, 255, 1);">
                                                    v{{ $document->version }}
                                                </span>
                                            @endif
                                        </div>
                                        <div class="text-xs text-dark-text-secondary mt-0.5 truncate">
                                            {{ $document->file_name }}
                                        </div>
                                        @if($document->download_count > 0)
                                            <div class="text-xs text-dark-text-secondary mt-0.5">
                                                <i class="fas fa-download mr-1"></i>{{ $document->download_count }} unduhan
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            <!-- Kategori -->
                            <td class="px-4 py-3 whitespace-nowrap">
                                @if($document->category)
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium" 
                                          style="background-color: {{ $category['bg'] }}; color: {{ $category['color'] }};">
                                        <i class="fas {{ $category['icon'] }} mr-1.5"></i>
                                        {{ ucfirst($document->category) }}
                                    </span>
                                @else
                                    <span class="text-sm text-dark-text-secondary">-</span>
                                @endif
                            </td>

                            <!-- Tipe File -->
                            <td class="px-4 py-3 text-center whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold" 
                                      style="background-color: {{ $fileType['bg'] }}; color: {{ $fileType['color'] }};">
                                    {{ strtoupper($extension) }}
                                </span>
                            </td>

                            <!-- Ukuran -->
                            <td class="px-4 py-3 text-center whitespace-nowrap">
                                <span class="text-sm text-dark-text-primary">{{ $formattedFileSize }}</span>
                            </td>

                            <!-- Proyek -->
                            <td class="px-4 py-3">
                                @if($document->project)
                                    <a href="{{ route('projects.show', $document->project) }}" 
                                       onclick="event.stopPropagation()"
                                       class="text-sm hover:underline" 
                                       style="color: rgba(0, 122, 255, 1);">
                                        {{ Str::limit($document->project->name, 30) }}
                                    </a>
                                @else
                                    <span class="text-sm text-dark-text-secondary">-</span>
                                @endif
                            </td>

                            <!-- Upload Info -->
                            <td class="px-4 py-3">
                                <div class="text-sm">
                                    @if($document->uploader)
                                        <div class="flex items-center mb-1">
                                            <div class="w-6 h-6 rounded-full flex items-center justify-center text-xs font-semibold mr-2" 
                                                 style="background-color: rgba(0, 122, 255, 0.15); color: rgba(0, 122, 255, 1);">
                                                {{ strtoupper(substr($document->uploader->name, 0, 1)) }}
                                            </div>
                                            <span class="text-dark-text-primary">{{ $document->uploader->name }}</span>
                                        </div>
                                    @endif
                                    <div class="text-xs text-dark-text-secondary">
                                        {{ $document->created_at->format('d M Y') }}
                                        <span class="mx-1">•</span>
                                        {{ $document->created_at->diffForHumans() }}
                                    </div>
                                </div>
                            </td>

                            <!-- Aksi -->
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-center space-x-2" onclick="event.stopPropagation();">
                                    <a href="{{ Storage::url($document->file_path) }}" 
                                       download
                                       class="p-2 rounded-apple transition-apple" 
                                       style="color: #34C759; background-color: rgba(52, 199, 89, 0.1); border: 1px solid rgba(52, 199, 89, 0.3);" 
                                       onmouseover="this.style.backgroundColor='#34C759'; this.style.color='#FFFFFF'" 
                                       onmouseout="this.style.backgroundColor='rgba(52, 199, 89, 0.1)'; this.style.color='#34C759'"
                                       title="Unduh">
                                        <i class="fas fa-download text-sm"></i>
                                    </a>
                                    <a href="{{ route('documents.show', $document) }}" 
                                       class="p-2 rounded-apple transition-apple" 
                                       style="color: #0A84FF; background-color: rgba(10, 132, 255, 0.1); border: 1px solid rgba(10, 132, 255, 0.3);" 
                                       onmouseover="this.style.backgroundColor='#0A84FF'; this.style.color='#FFFFFF'" 
                                       onmouseout="this.style.backgroundColor='rgba(10, 132, 255, 0.1)'; this.style.color='#0A84FF'"
                                       title="Lihat">
                                        <i class="fas fa-eye text-sm"></i>
                                    </a>
                                    <a href="{{ route('documents.edit', $document) }}" 
                                       class="p-2 rounded-apple transition-apple" 
                                       style="color: #FF9F0A; background-color: rgba(255, 159, 10, 0.1); border: 1px solid rgba(255, 159, 10, 0.3);" 
                                       onmouseover="this.style.backgroundColor='#FF9F0A'; this.style.color='#FFFFFF'" 
                                       onmouseout="this.style.backgroundColor='rgba(255, 159, 10, 0.1)'; this.style.color='#FF9F0A'"
                                       title="Ubah">
                                        <i class="fas fa-edit text-sm"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center">
                                <div class="flex flex-col items-center justify-center" style="color: rgba(235, 235, 245, 0.6);">
                                    <i class="fas fa-inbox text-4xl mb-3"></i>
                                    <p class="text-sm font-medium">Tidak ada dokumen ditemukan</p>
                                    <p class="text-xs mt-1">Coba ubah filter atau upload dokumen baru</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($documents->hasPages())
            <div class="px-4 py-3" style="border-top: 1px solid rgba(84, 84, 88, 0.65); background-color: var(--dark-bg-secondary);">
                {{ $documents->links() }}
            </div>
        @endif
    </div>
@endsection
