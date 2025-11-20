@extends('mobile.layouts.app')

@section('title', 'Dokumen')

@section('content')
<div class="pb-20">
    
    {{-- Header --}}
    <div class="bg-gradient-to-br from-[#0077b5] to-[#004d6d] rounded-2xl p-6 mb-4 text-white">
        <div class="flex items-center justify-between mb-3">
            <div>
                <h2 class="text-xl font-bold">Dokumen</h2>
                <p class="text-sm opacity-90">Kelola dokumen proyek</p>
            </div>
            <div class="w-12 h-12 bg-white/10 rounded-xl flex items-center justify-center">
                <i class="fas fa-file text-2xl"></i>
            </div>
        </div>
        
        {{-- Search --}}
        <div class="relative">
            <input type="text" 
                   placeholder="Cari dokumen..." 
                   class="w-full px-4 py-2.5 pl-10 bg-white/10 border border-white/20 rounded-lg text-white placeholder-white/60 focus:outline-none focus:ring-2 focus:ring-white/30">
            <i class="fas fa-search absolute left-3 top-3.5 text-white/60"></i>
        </div>
    </div>

    {{-- Filters --}}
    <div class="flex gap-2 mb-4 overflow-x-auto pb-2">
        <button class="px-4 py-2 bg-[#0077b5] text-white rounded-lg text-sm font-medium whitespace-nowrap">
            Semua
        </button>
        <button class="px-4 py-2 bg-white border border-gray-200 text-gray-700 rounded-lg text-sm font-medium whitespace-nowrap">
            Kontrak
        </button>
        <button class="px-4 py-2 bg-white border border-gray-200 text-gray-700 rounded-lg text-sm font-medium whitespace-nowrap">
            Proposal
        </button>
        <button class="px-4 py-2 bg-white border border-gray-200 text-gray-700 rounded-lg text-sm font-medium whitespace-nowrap">
            Invoice
        </button>
        <button class="px-4 py-2 bg-white border border-gray-200 text-gray-700 rounded-lg text-sm font-medium whitespace-nowrap">
            Lainnya
        </button>
    </div>

    {{-- Document List --}}
    <div class="space-y-2">
        @forelse($documents as $document)
        <div class="bg-white rounded-xl p-4 border border-gray-200">
            <div class="flex items-start gap-3">
                {{-- File Icon --}}
                <div class="w-12 h-12 rounded-lg flex items-center justify-center flex-shrink-0
                    {{ $document->mime_type === 'application/pdf' ? 'bg-red-50' : 
                       (str_contains($document->mime_type, 'word') ? 'bg-blue-50' : 
                       (str_contains($document->mime_type, 'excel') ? 'bg-green-50' : 'bg-gray-100')) }}">
                    <i class="fas {{ $document->mime_type === 'application/pdf' ? 'fa-file-pdf text-red-600' : 
                                    (str_contains($document->mime_type, 'word') ? 'fa-file-word text-blue-600' : 
                                    (str_contains($document->mime_type, 'excel') ? 'fa-file-excel text-green-600' : 'fa-file text-gray-600')) }} text-xl"></i>
                </div>
                
                <div class="flex-1 min-w-0">
                    <h3 class="font-medium text-gray-900 mb-1 truncate">{{ $document->title }}</h3>
                    <div class="flex items-center gap-2 text-xs text-gray-500 mb-2">
                        <span>{{ $document->category }}</span>
                        <span>•</span>
                        <span>{{ number_format($document->file_size / 1024, 0) }} KB</span>
                        <span>•</span>
                        <span>{{ $document->created_at->diffForHumans() }}</span>
                    </div>
                    
                    @if($document->project)
                    <div class="inline-flex items-center gap-1 px-2 py-1 bg-gray-100 rounded text-xs text-gray-600">
                        <i class="fas fa-folder text-xs"></i>
                        <span>{{ $document->project->name }}</span>
                    </div>
                    @endif
                </div>
                
                {{-- Status Badge --}}
                <span class="px-2 py-1 rounded-full text-xs font-medium whitespace-nowrap
                    {{ $document->status === 'approved' ? 'bg-green-100 text-green-800' : 
                       ($document->status === 'review' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-600') }}">
                    {{ ucfirst($document->status) }}
                </span>
            </div>
            
            <div class="mt-3 pt-3 border-t border-gray-100 flex items-center gap-2">
                <a href="{{ Storage::url($document->file_path) }}" 
                   target="_blank"
                   class="flex-1 text-center py-2 bg-[#0077b5] text-white rounded-lg text-sm font-medium">
                    <i class="fas fa-eye mr-1"></i> Lihat
                </a>
                <a href="{{ Storage::url($document->file_path) }}" 
                   download
                   class="flex-1 text-center py-2 bg-gray-100 text-gray-700 rounded-lg text-sm font-medium">
                    <i class="fas fa-download mr-1"></i> Download
                </a>
            </div>
        </div>
        @empty
        <div class="text-center py-12">
            <div class="w-20 h-20 bg-gray-100 rounded-full mx-auto mb-4 flex items-center justify-center">
                <i class="fas fa-file text-3xl text-gray-400"></i>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Belum Ada Dokumen</h3>
            <p class="text-sm text-gray-600 mb-4">Upload dokumen pertama Anda</p>
        </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    @if($documents->hasPages())
    <div class="mt-4">
        {{ $documents->links() }}
    </div>
    @endif

</div>
@endsection
