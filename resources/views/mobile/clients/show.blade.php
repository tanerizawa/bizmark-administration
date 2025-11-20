@extends('mobile.layouts.app')

@section('title', $client->name)

@section('content')
<div class="pb-20">

    {{-- Client Header Card --}}
    <div class="bg-gradient-to-br from-[#0077b5] to-[#004d6d] rounded-2xl p-6 mb-4 text-white">
        <div class="flex items-start gap-4">
            <div class="w-16 h-16 rounded-full bg-white/20 flex items-center justify-center text-2xl font-bold flex-shrink-0">
                {{ strtoupper(substr($client->name, 0, 1)) }}
            </div>
            <div class="flex-1">
                <h1 class="text-xl font-bold mb-1">{{ $client->name }}</h1>
                @if($client->company_name)
                <p class="text-sm opacity-90 mb-2">{{ $client->company_name }}</p>
                @endif
                <span class="inline-block px-3 py-1 bg-white/20 rounded-full text-xs font-medium">
                    {{ $client->status === 'active' ? 'Klien Aktif' : 'Klien Nonaktif' }}
                </span>
            </div>
        </div>
    </div>

    {{-- Quick Stats --}}
    <div class="grid grid-cols-3 gap-3 mb-4">
        <div class="bg-white rounded-xl p-4 border border-gray-200">
            <div class="text-2xl font-bold text-gray-900">{{ $client->projects->count() }}</div>
            <div class="text-xs text-gray-600">Total Proyek</div>
        </div>
        <div class="bg-white rounded-xl p-4 border border-gray-200">
            <div class="text-2xl font-bold text-green-600">
                {{ $client->projects->where('status_id', 9)->count() }}
            </div>
            <div class="text-xs text-gray-600">Selesai</div>
        </div>
        <div class="bg-white rounded-xl p-4 border border-gray-200">
            <div class="text-2xl font-bold text-blue-600">
                {{ $client->projects->whereIn('status_id', [2,3,4,5,6,7,8])->count() }}
            </div>
            <div class="text-xs text-gray-600">Aktif</div>
        </div>
    </div>

    {{-- Contact Information --}}
    <div class="bg-white rounded-xl p-4 border border-gray-200 mb-4">
        <h3 class="text-sm font-semibold text-gray-900 mb-3">INFORMASI KONTAK</h3>
        <div class="space-y-3">
            @if($client->contact_person)
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-user text-gray-600"></i>
                </div>
                <div class="flex-1">
                    <div class="text-xs text-gray-500">Contact Person</div>
                    <div class="text-sm font-medium text-gray-900">{{ $client->contact_person }}</div>
                </div>
            </div>
            @endif
            
            @if($client->email)
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-envelope text-gray-600"></i>
                </div>
                <div class="flex-1">
                    <div class="text-xs text-gray-500">Email</div>
                    <a href="mailto:{{ $client->email }}" class="text-sm font-medium text-[#0077b5]">
                        {{ $client->email }}
                    </a>
                </div>
            </div>
            @endif
            
            @if($client->phone)
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-phone text-gray-600"></i>
                </div>
                <div class="flex-1">
                    <div class="text-xs text-gray-500">Telepon</div>
                    <a href="tel:{{ $client->phone }}" class="text-sm font-medium text-[#0077b5]">
                        {{ $client->phone }}
                    </a>
                </div>
            </div>
            @endif
            
            @if($client->mobile)
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-mobile-alt text-gray-600"></i>
                </div>
                <div class="flex-1">
                    <div class="text-xs text-gray-500">Mobile</div>
                    <a href="tel:{{ $client->mobile }}" class="text-sm font-medium text-[#0077b5]">
                        {{ $client->mobile }}
                    </a>
                </div>
            </div>
            @endif
            
            @if($client->address)
            <div class="flex items-start gap-3">
                <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-map-marker-alt text-gray-600"></i>
                </div>
                <div class="flex-1">
                    <div class="text-xs text-gray-500">Alamat</div>
                    <div class="text-sm text-gray-900">{{ $client->address }}</div>
                    @if($client->city || $client->province)
                    <div class="text-xs text-gray-500 mt-1">
                        {{ $client->city }}{{ $client->city && $client->province ? ', ' : '' }}{{ $client->province }}
                        {{ $client->postal_code ? ' ' . $client->postal_code : '' }}
                    </div>
                    @endif
                </div>
            </div>
            @endif
        </div>
    </div>

    {{-- Company Information --}}
    @if($client->industry || $client->npwp || $client->client_type)
    <div class="bg-white rounded-xl p-4 border border-gray-200 mb-4">
        <h3 class="text-sm font-semibold text-gray-900 mb-3">INFORMASI PERUSAHAAN</h3>
        <div class="space-y-3">
            @if($client->industry)
            <div class="flex justify-between">
                <span class="text-sm text-gray-600">Industri</span>
                <span class="text-sm font-medium text-gray-900">{{ $client->industry }}</span>
            </div>
            @endif
            
            @if($client->client_type)
            <div class="flex justify-between">
                <span class="text-sm text-gray-600">Tipe Klien</span>
                <span class="text-sm font-medium text-gray-900">{{ ucfirst($client->client_type) }}</span>
            </div>
            @endif
            
            @if($client->npwp)
            <div class="flex justify-between">
                <span class="text-sm text-gray-600">NPWP</span>
                <span class="text-sm font-medium text-gray-900">{{ $client->npwp }}</span>
            </div>
            @endif
            
            @if($client->tax_name)
            <div class="flex justify-between">
                <span class="text-sm text-gray-600">Nama Pajak</span>
                <span class="text-sm font-medium text-gray-900">{{ $client->tax_name }}</span>
            </div>
            @endif
        </div>
    </div>
    @endif

    {{-- Projects List --}}
    <div class="bg-white rounded-xl p-4 border border-gray-200 mb-4">
        <div class="flex items-center justify-between mb-3">
            <h3 class="text-sm font-semibold text-gray-900">PROYEK</h3>
            <span class="text-xs text-gray-600">{{ $client->projects->count() }} proyek</span>
        </div>
        
        @if($client->projects->count() > 0)
        <div class="space-y-2">
            @foreach($client->projects->take(10) as $project)
            <a href="{{ mobile_route('projects.show', $project->id) }}" 
               class="block p-3 bg-gray-50 rounded-lg active:bg-gray-100">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-white rounded-lg flex items-center justify-center border border-gray-200">
                        <i class="fas fa-folder text-[#0077b5]"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h4 class="text-sm font-medium text-gray-900 truncate">{{ $project->name }}</h4>
                        <div class="flex items-center gap-2 mt-1">
                            <span class="text-xs text-gray-600">
                                {{ $project->status->name ?? 'N/A' }}
                            </span>
                            @if($project->progress_percentage)
                            <span class="text-xs text-gray-400">•</span>
                            <span class="text-xs text-gray-600">{{ $project->progress_percentage }}%</span>
                            @endif
                        </div>
                    </div>
                    <i class="fas fa-chevron-right text-gray-400 text-xs"></i>
                </div>
            </a>
            @endforeach
        </div>
        @else
        <div class="text-center py-6 text-gray-400">
            <i class="fas fa-folder-open text-2xl mb-2"></i>
            <p class="text-sm">Belum ada proyek</p>
        </div>
        @endif
    </div>

    {{-- Notes --}}
    @if($client->notes)
    <div class="bg-white rounded-xl p-4 border border-gray-200">
        <h3 class="text-sm font-semibold text-gray-900 mb-2">CATATAN</h3>
        <p class="text-sm text-gray-600 leading-relaxed">{{ $client->notes }}</p>
    </div>
    @endif

</div>
@endsection
