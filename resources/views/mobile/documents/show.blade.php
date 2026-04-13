@extends('mobile.layouts.app')

@section('title', $document->title ?? 'Detail Dokumen')

@section('content')
<div class="pb-20">

    {{-- Document Header --}}
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden mb-4">
        <div class="p-4">
            {{-- File Icon & Type --}}
            <div class="flex items-center gap-4 mb-4">
                <div class="w-14 h-14 rounded-xl flex items-center justify-center flex-shrink-0
                    @if(str_contains($document->file_type ?? '', 'pdf')) bg-red-50
                    @elseif(str_contains($document->file_type ?? '', 'word') || str_contains($document->file_type ?? '', 'doc')) bg-blue-50
                    @elseif(str_contains($document->file_type ?? '', 'sheet') || str_contains($document->file_type ?? '', 'xls')) bg-green-50
                    @elseif(str_contains($document->file_type ?? '', 'image') || str_contains($document->file_type ?? '', 'png') || str_contains($document->file_type ?? '', 'jpg')) bg-purple-50
                    @else bg-gray-50
                    @endif">
                    <i class="text-2xl
                        @if(str_contains($document->file_type ?? '', 'pdf')) fas fa-file-pdf text-red-500
                        @elseif(str_contains($document->file_type ?? '', 'word') || str_contains($document->file_type ?? '', 'doc')) fas fa-file-word text-blue-500
                        @elseif(str_contains($document->file_type ?? '', 'sheet') || str_contains($document->file_type ?? '', 'xls')) fas fa-file-excel text-green-500
                        @elseif(str_contains($document->file_type ?? '', 'image') || str_contains($document->file_type ?? '', 'png') || str_contains($document->file_type ?? '', 'jpg')) fas fa-file-image text-purple-500
                        @else fas fa-file text-gray-500
                        @endif"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <h2 class="text-lg font-bold text-gray-900 truncate">{{ $document->title }}</h2>
                    <p class="text-sm text-gray-500">{{ $document->file_name ?? 'Dokumen' }}</p>
                </div>
            </div>

            {{-- Status --}}
            <div class="flex items-center gap-2 mb-3">
                <span class="text-xs px-2.5 py-1 rounded-full font-medium
                    @if($document->status === 'active') bg-green-100 text-green-700
                    @elseif($document->status === 'review') bg-amber-100 text-amber-700
                    @elseif($document->status === 'rejected') bg-red-100 text-red-700
                    @else bg-gray-100 text-gray-700
                    @endif">
                    @if($document->status === 'active') <i class="fas fa-check-circle mr-1"></i>Aktif
                    @elseif($document->status === 'review') <i class="fas fa-clock mr-1"></i>Review
                    @elseif($document->status === 'rejected') <i class="fas fa-times-circle mr-1"></i>Ditolak
                    @else {{ ucfirst($document->status ?? 'Draft') }}
                    @endif
                </span>
                @if($document->category)
                <span class="text-xs px-2.5 py-1 rounded-full font-medium bg-[#E7F3F8] text-[#0A66C2]">
                    {{ ucfirst($document->category) }}
                </span>
                @endif
            </div>

            {{-- Meta Info --}}
            <div class="space-y-2 text-sm">
                @if($document->project)
                <div class="flex items-center gap-2 text-gray-600">
                    <i class="fas fa-folder text-gray-400 w-5 text-center"></i>
                    <a href="{{ route('mobile.projects.show', $document->project_id) }}" class="text-[#0A66C2] font-medium">
                        {{ $document->project->name }}
                    </a>
                </div>
                @endif

                @if($document->file_size)
                <div class="flex items-center gap-2 text-gray-600">
                    <i class="fas fa-hdd text-gray-400 w-5 text-center"></i>
                    <span>{{ number_format($document->file_size / 1024, 1) }} KB</span>
                </div>
                @endif

                @if($document->created_at)
                <div class="flex items-center gap-2 text-gray-600">
                    <i class="fas fa-calendar text-gray-400 w-5 text-center"></i>
                    <span>{{ $document->created_at->format('d M Y H:i') }}</span>
                </div>
                @endif

                @if($document->uploadedBy)
                <div class="flex items-center gap-2 text-gray-600">
                    <i class="fas fa-user text-gray-400 w-5 text-center"></i>
                    <span>{{ $document->uploadedBy->name }}</span>
                </div>
                @endif
            </div>

            {{-- Description --}}
            @if($document->description)
            <div class="mt-3 pt-3 border-t border-gray-100">
                <p class="text-sm text-gray-600 leading-relaxed">{{ $document->description }}</p>
            </div>
            @endif
        </div>

        {{-- Actions --}}
        <div class="border-t border-gray-100 p-3 flex gap-2">
            <a href="{{ route('mobile.documents.download', $document->id) }}"
               class="flex-1 py-2.5 bg-[#0A66C2] text-white rounded-lg text-sm font-medium text-center active:scale-95 transition-all">
                <i class="fas fa-download mr-1"></i> Download
            </a>
            @if($document->file_type && str_contains($document->file_type, 'pdf'))
            <a href="{{ asset('storage/' . $document->file_path) }}" target="_blank"
               class="flex-1 py-2.5 bg-gray-100 text-gray-700 rounded-lg text-sm font-medium text-center active:scale-95 transition-all">
                <i class="fas fa-eye mr-1"></i> Lihat
            </a>
            @endif
        </div>
    </div>
</div>
@endsection
