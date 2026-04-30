@extends('layouts.app')

@section('title', 'Dokumen')
@section('page-title', 'Manajemen Dokumen')

@section('content')
    {{-- Compact Hero Section --}}
    <section class="card-elevated rounded-apple-lg admin-hero relative overflow-hidden mb-4">
        <div class="absolute inset-0 pointer-events-none" aria-hidden="true">
            <div class="w-48 h-48 bg-apple-blue opacity-20 blur-3xl rounded-full absolute -top-10 -right-6"></div>
            <div class="w-32 h-32 bg-apple-green opacity-15 blur-2xl rounded-full absolute bottom-0 left-6"></div>
        </div>
        <div class="relative space-y-3">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3">
                <div class="space-y-1 max-w-3xl">
                    <p class="admin-label-compact">Manajemen Dokumen</p>
                    <h1 class="admin-hero-title">Arsip Digital Dokumen Perizinan</h1>
                    <p class="admin-body text-dark-text-secondary">Simpan, kelola, dan akses semua dokumen perizinan secara terpusat dengan sistem keamanan terstruktur.</p>
                </div>
                <div>
                    <a href="{{ route('documents.create') }}" class="admin-btn inline-flex items-center">
                        <i class="fas fa-upload mr-1.5"></i>Upload Dokumen
                    </a>
                </div>
            </div>

            {{-- Compact Stats Cards --}}
            <div class="grid grid-cols-2 md:grid-cols-4 gap-2">
                @php
                    $totalDocs = $documents->total();
                    $totalSize = $documents->sum('file_size');
                    $perizinanCount = $documents->where('category', 'perizinan')->count();
                    $confidentialCount = $documents->where('is_confidential', true)->count();
                    $formattedSize = $totalSize >= 1073741824 ? 
                        number_format($totalSize / 1073741824, 2) . ' GB' : 
                        ($totalSize >= 1048576 ? 
                            number_format($totalSize / 1048576, 2) . ' MB' : 
                            number_format($totalSize / 1024, 2) . ' KB');
                @endphp
                <div class="admin-stat-card bg-apple-blue/12">
                    <div class="flex items-center gap-2">
                        <div class="w-7 h-7 rounded-lg flex items-center justify-center bg-apple-blue/25">
                            <i class="fas fa-file-alt text-xs text-apple-blue"></i>
                        </div>
                        <div>
                            <p class="admin-stat text-white">{{ $totalDocs }}</p>
                            <p class="admin-label-compact">Total Dokumen</p>
                        </div>
                    </div>
                </div>
                <div class="admin-stat-card bg-apple-purple/12">
                    <div class="flex items-center gap-2">
                        <div class="w-7 h-7 rounded-lg flex items-center justify-center bg-apple-purple/25">
                            <i class="fas fa-hdd text-xs text-apple-purple"></i>
                        </div>
                        <div>
                            <p class="admin-stat text-apple-purple">{{ $formattedSize }}</p>
                            <p class="admin-label-compact">Total Ukuran</p>
                        </div>
                    </div>
                </div>
                <div class="admin-stat-card bg-apple-orange/12">
                    <div class="flex items-center gap-2">
                        <div class="w-7 h-7 rounded-lg flex items-center justify-center bg-apple-orange/25">
                            <i class="fas fa-certificate text-xs text-apple-orange"></i>
                        </div>
                        <div>
                            <p class="admin-stat text-apple-orange">{{ $perizinanCount }}</p>
                            <p class="admin-label-compact">Perizinan</p>
                        </div>
                    </div>
                </div>
                <div class="admin-stat-card bg-apple-red/12">
                    <div class="flex items-center gap-2">
                        <div class="w-7 h-7 rounded-lg flex items-center justify-center bg-apple-red/25">
                            <i class="fas fa-lock text-xs text-apple-red"></i>
                        </div>
                        <div>
                            <p class="admin-stat text-apple-red">{{ $confidentialCount }}</p>
                            <p class="admin-label-compact">Rahasia</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Compact Search and Filter --}}
    <div class="card-elevated rounded-apple p-3 mb-3">
        <form method="GET" action="{{ route('documents.index') }}" class="flex flex-wrap gap-2 items-end">
            <div class="flex-1 min-w-[150px]">
                <label class="admin-label-compact block">Cari</label>
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Judul atau nama file..." 
                           class="admin-input w-full pl-7 rounded bg-dark-bg-secondary border border-dark-separator text-white">
                    <i class="fas fa-search absolute left-2.5 top-1/2 transform -translate-y-1/2 text-dark-text-tertiary text-[10px]"></i>
                </div>
            </div>
            <div class="w-28">
                <label class="admin-label-compact block">Kategori</label>
                <select name="category" class="admin-input admin-select w-full rounded bg-dark-bg-secondary border border-dark-separator text-white">
                    <option value="">Semua</option>
                    @isset($categories)
                        @foreach($categories as $cat)
                            <option value="{{ $cat }}" {{ request('category') == $cat ? 'selected' : '' }}>{{ ucfirst($cat) }}</option>
                        @endforeach
                    @endisset
                </select>
            </div>
            <div class="w-28">
                <label class="admin-label-compact block">Tipe</label>
                <select name="document_type" class="admin-input admin-select w-full rounded bg-dark-bg-secondary border border-dark-separator text-white">
                    <option value="">Semua</option>
                    @isset($documentTypes)
                        @foreach($documentTypes as $type)
                            <option value="{{ $type }}" {{ request('document_type') == $type ? 'selected' : '' }}>{{ ucfirst($type) }}</option>
                        @endforeach
                    @endisset
                </select>
            </div>
            <div class="w-36">
                <label class="admin-label-compact block">Proyek</label>
                <select name="project_id" class="admin-input admin-select w-full rounded bg-dark-bg-secondary border border-dark-separator text-white">
                    <option value="">Semua</option>
                    @isset($projects)
                        @foreach($projects as $project)
                            <option value="{{ $project->id }}" {{ request('project_id') == $project->id ? 'selected' : '' }}>{{ $project->name }}</option>
                        @endforeach
                    @endisset
                </select>
            </div>
        </form>
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('form[action="{{ route('documents.index') }}"]');
            if (!form) return;
            form.querySelectorAll('select[name]').forEach(el => el.addEventListener('change', () => form.submit()));
            const searchInput = form.querySelector('input[name="search"]');
            if (searchInput) searchInput.addEventListener('keydown', e => { if (e.key === 'Enter') { e.preventDefault(); form.submit(); }});
        });
        </script>
    </div>

    <!-- Documents Table -->
    <div class="card-elevated rounded-apple-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-700">
                <thead class="bg-dark-bg-secondary">
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
                <tbody class="divide-y divide-gray-700 bg-dark-bg-secondary">
                    @forelse($documents as $document)
                        @php
                            // File type icon configuration
                            $extension = strtolower(pathinfo($document->file_name, PATHINFO_EXTENSION));
                            $fileTypeConfig = [
                                'pdf' => ['icon' => 'fa-file-pdf', 'bgCls' => 'bg-apple-red/15', 'iconCls' => 'text-apple-red'],
                                'doc' => ['icon' => 'fa-file-word', 'bgCls' => 'bg-apple-blue/15', 'iconCls' => 'text-apple-blue'],
                                'docx' => ['icon' => 'fa-file-word', 'bgCls' => 'bg-apple-blue/15', 'iconCls' => 'text-apple-blue'],
                                'xls' => ['icon' => 'fa-file-excel', 'bgCls' => 'bg-apple-green/15', 'iconCls' => 'text-apple-green'],
                                'xlsx' => ['icon' => 'fa-file-excel', 'bgCls' => 'bg-apple-green/15', 'iconCls' => 'text-apple-green'],
                                'jpg' => ['icon' => 'fa-file-image', 'bgCls' => 'bg-apple-purple/15', 'iconCls' => 'text-apple-purple'],
                                'jpeg' => ['icon' => 'fa-file-image', 'bgCls' => 'bg-apple-purple/15', 'iconCls' => 'text-apple-purple'],
                                'png' => ['icon' => 'fa-file-image', 'bgCls' => 'bg-apple-purple/15', 'iconCls' => 'text-apple-purple'],
                                'zip' => ['icon' => 'fa-file-archive', 'bgCls' => 'bg-apple-orange/15', 'iconCls' => 'text-apple-orange'],
                                'rar' => ['icon' => 'fa-file-archive', 'bgCls' => 'bg-apple-orange/15', 'iconCls' => 'text-apple-orange'],
                            ];
                            $fileType = $fileTypeConfig[$extension] ?? ['icon' => 'fa-file-alt', 'bgCls' => 'bg-dark-text-tertiary/15', 'iconCls' => 'text-dark-text-tertiary'];

                            // Category configuration
                            $categoryConfig = [
                                'perizinan' => ['icon' => 'fa-file-contract', 'bgCls' => 'bg-apple-orange/15', 'iconCls' => 'text-apple-orange'],
                                'kontrak' => ['icon' => 'fa-file-signature', 'bgCls' => 'bg-apple-purple/15', 'iconCls' => 'text-apple-purple'],
                                'laporan' => ['icon' => 'fa-file-chart-line', 'bgCls' => 'bg-apple-green/15', 'iconCls' => 'text-apple-green'],
                                'teknis' => ['icon' => 'fa-file-code', 'bgCls' => 'bg-apple-blue/15', 'iconCls' => 'text-apple-blue'],
                            ];
                            $category = $categoryConfig[$document->category] ?? ['icon' => 'fa-folder', 'bgCls' => 'bg-dark-text-tertiary/15', 'iconCls' => 'text-dark-text-tertiary'];

                            // Format file size
                            $fileSize = $document->file_size;
                            $formattedFileSize = $fileSize >= 1048576 ? 
                                number_format($fileSize / 1048576, 2) . ' MB' : 
                                number_format($fileSize / 1024, 2) . ' KB';
                        @endphp

                        <tr class="hover-lift transition-apple cursor-pointer" onclick="window.location='{{ route('documents.show', $document) }}'">
                            <!-- Dokumen Info -->
                            <td class="px-4 py-3">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 rounded-lg flex items-center justify-center mr-3 {{ $fileType['bgCls'] }}">
                                        <i class="fas {{ $fileType['icon'] }} text-lg {{ $fileType['iconCls'] }}"></i>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center">
                                            <div class="font-semibold text-sm text-dark-text-primary truncate">
                                                {{ $document->title }}
                                            </div>
                                            @if($document->is_confidential)
                                                <i class="fas fa-lock text-xs ml-2 text-apple-red" title="Rahasia"></i>
                                            @endif
                                            @if($document->version > 1)
                                                <span class="text-xs ml-2 px-2 py-0.5 rounded-full bg-apple-blue/15 text-apple-blue">
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
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium {{ $category['bgCls'] }} {{ $category['iconCls'] }}">
                                        <i class="fas {{ $category['icon'] }} mr-1.5"></i>
                                        {{ ucfirst($document->category) }}
                                    </span>
                                @else
                                    <span class="text-sm text-dark-text-secondary">-</span>
                                @endif
                            </td>

                            <!-- Tipe File -->
                            <td class="px-4 py-3 text-center whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold {{ $fileType['bgCls'] }} {{ $fileType['iconCls'] }}">
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
                                       class="text-sm hover:underline text-apple-blue">
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
                                            <div class="w-6 h-6 rounded-full flex items-center justify-center text-xs font-semibold mr-2 bg-apple-blue/15 text-apple-blue">
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
                                       class="p-2 rounded-apple transition-apple text-apple-green bg-apple-green/10 border border-apple-green/30 hover:bg-apple-green hover:text-white"
                                       title="Unduh">
                                        <i class="fas fa-download text-sm"></i>
                                    </a>
                                    <a href="{{ route('documents.show', $document) }}" 
                                       class="p-2 rounded-apple transition-apple text-apple-blue bg-apple-blue/10 border border-apple-blue/30 hover:bg-apple-blue hover:text-white"
                                       title="Lihat">
                                        <i class="fas fa-eye text-sm"></i>
                                    </a>
                                    <a href="{{ route('documents.edit', $document) }}" 
                                       class="p-2 rounded-apple transition-apple text-apple-orange bg-apple-orange/10 border border-apple-orange/30 hover:bg-apple-orange hover:text-white"
                                       title="Ubah">
                                        <i class="fas fa-edit text-sm"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center">
                                <div class="flex flex-col items-center justify-center text-dark-text-secondary">
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
            <div class="px-4 py-3 border-t border-white/10 bg-dark-bg-secondary">
                {{ $documents->links() }}
            </div>
        @endif
    </div>
@endsection
