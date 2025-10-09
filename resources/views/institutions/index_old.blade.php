@extends('layouts.app')

@section('title', 'Manajemen Institusi')

@section('content')
<div class="container mx-auto px-6 py-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Manajemen Institusi</h1>
                <p class="text-gray-600 mt-2">Kelola data institusi mitra dan klien</p>
            </div>
            <a href="{{ route('institutions.create') }}" 
               class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium transition-colors duration-200 flex items-center">
                <i class="fas fa-plus mr-2"></i>
                Tambah Institusi
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
        <form method="GET" action="{{ route('institutions.index') }}" class="space-y-4 md:space-y-0 md:flex md:items-end md:space-x-4">
            <!-- Search -->
            <div class="flex-1">
                <label for="search" class="block text-sm font-medium text-gray-700 mb-2">Pencarian</label>
                <input type="text" 
                       id="search" 
                       name="search" 
                       value="{{ request('search') }}"
                       placeholder="Nama institusi, kontak, alamat..."
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>

            <!-- Type Filter -->
            <div class="w-full md:w-48">
                <label for="type" class="block text-sm font-medium text-gray-700 mb-2">Tipe</label>
                <select id="type" 
                        name="type" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Semua Tipe</option>
                    <option value="government" {{ request('type') === 'government' ? 'selected' : '' }}>Pemerintah</option>
                    <option value="private" {{ request('type') === 'private' ? 'selected' : '' }}>Swasta</option>
                    <option value="ngo" {{ request('type') === 'ngo' ? 'selected' : '' }}>NGO</option>
                    <option value="educational" {{ request('type') === 'educational' ? 'selected' : '' }}>Pendidikan</option>
                    <option value="healthcare" {{ request('type') === 'healthcare' ? 'selected' : '' }}>Kesehatan</option>
                    <option value="other" {{ request('type') === 'other' ? 'selected' : '' }}>Lainnya</option>
                </select>
            </div>

            <!-- Status Filter -->
            <div class="w-full md:w-48">
                <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                <select id="status" 
                        name="status" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Semua Status</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Aktif</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Tidak Aktif</option>
                </select>
            </div>

            <!-- Sort -->
            <div class="w-full md:w-48">
                <label for="sort" class="block text-sm font-medium text-gray-700 mb-2">Urutkan</label>
                <select id="sort" 
                        name="sort" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="name" {{ request('sort') === 'name' ? 'selected' : '' }}>Nama</option>
                    <option value="created_at" {{ request('sort') === 'created_at' ? 'selected' : '' }}>Tanggal Dibuat</option>
                    <option value="projects_count" {{ request('sort') === 'projects_count' ? 'selected' : '' }}>Jumlah Proyek</option>
                </select>
            </div>

            <!-- Actions -->
            <div class="flex space-x-2">
                <button type="submit" 
                        class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg transition-colors duration-200 flex items-center">
                    <i class="fas fa-search mr-2"></i>
                    Filter
                </button>
                <a href="{{ route('institutions.index') }}" 
                   class="bg-gray-400 hover:bg-gray-500 text-white px-4 py-2 rounded-lg transition-colors duration-200 flex items-center">
                    <i class="fas fa-times mr-2"></i>
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Institutions List -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        @if($institutions->count() > 0)
            <!-- Stats Summary -->
            <div class="p-6 border-b border-gray-200">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div class="text-center">
                        <div class="text-2xl font-bold text-blue-600">{{ $institutions->total() }}</div>
                        <div class="text-sm text-gray-600">Total Institusi</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-green-600">{{ $institutions->where('is_active', true)->count() }}</div>
                        <div class="text-sm text-gray-600">Aktif</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-red-600">{{ $institutions->where('is_active', false)->count() }}</div>
                        <div class="text-sm text-gray-600">Tidak Aktif</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-gray-600">{{ $institutions->sum('projects_count') }}</div>
                        <div class="text-sm text-gray-600">Total Proyek</div>
                    </div>
                </div>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Institusi
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Tipe & Status
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Kontak
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Proyek
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($institutions as $institution)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4">
                                    <div>
                                        <h3 class="text-sm font-medium text-gray-900">
                                            <a href="{{ route('institutions.show', $institution) }}" class="hover:text-blue-600">
                                                {{ $institution->name }}
                                            </a>
                                        </h3>
                                        @if($institution->address)
                                            <p class="text-sm text-gray-500 mt-1">{{ Str::limit($institution->address, 60) }}</p>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="space-y-1">
                                        <span class="inline-block px-2 py-1 bg-blue-100 text-blue-800 text-xs font-medium rounded">
                                            {{ ucfirst($institution->type) }}
                                        </span>
                                        <br>
                                        <span class="px-2 py-1 rounded-full text-xs font-medium
                                            @if($institution->is_active) bg-green-100 text-green-800
                                            @else bg-red-100 text-red-800
                                            @endif">
                                            {{ $institution->is_active ? 'Aktif' : 'Tidak Aktif' }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm">
                                        @if($institution->contact_person)
                                            <div class="font-medium text-gray-900">{{ $institution->contact_person }}</div>
                                            @if($institution->contact_position)
                                                <div class="text-gray-500 text-xs">{{ $institution->contact_position }}</div>
                                            @endif
                                        @endif
                                        @if($institution->phone)
                                            <div class="text-gray-500">{{ $institution->phone }}</div>
                                        @endif
                                        @if($institution->email)
                                            <div class="text-gray-500">{{ $institution->email }}</div>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-center">
                                        <div class="text-lg font-bold text-blue-600">{{ $institution->projects_count }}</div>
                                        <div class="text-xs text-gray-500">Proyek</div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <a href="{{ route('institutions.show', $institution) }}" 
                                           class="text-blue-600 hover:text-blue-900 transition-colors duration-200">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('institutions.edit', $institution) }}" 
                                           class="text-yellow-600 hover:text-yellow-900 transition-colors duration-200">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        @if($institution->projects_count === 0)
                                            <form action="{{ route('institutions.destroy', $institution) }}" 
                                                  method="POST" 
                                                  class="inline"
                                                  onsubmit="return confirm('Apakah Anda yakin ingin menghapus institusi ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="text-red-600 hover:text-red-900 transition-colors duration-200">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($institutions->hasPages())
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $institutions->links() }}
                </div>
            @endif
        @else
            <!-- Empty State -->
            <div class="p-12 text-center">
                <div class="w-24 h-24 mx-auto mb-4 text-gray-400">
                    <i class="fas fa-building text-6xl"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Belum ada institusi</h3>
                <p class="text-gray-500 mb-6">
                    @if(request()->hasAny(['search', 'type', 'status']))
                        Tidak ada institusi yang sesuai dengan filter yang dipilih.
                    @else
                        Mulai dengan menambahkan institusi pertama Anda.
                    @endif
                </p>
                @if(request()->hasAny(['search', 'type', 'status']))
                    <a href="{{ route('institutions.index') }}" 
                       class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition-colors duration-200 mr-3">
                        Reset Filter
                    </a>
                @endif
                <a href="{{ route('institutions.create') }}" 
                   class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition-colors duration-200">
                    Tambah Institusi Pertama
                </a>
            </div>
        @endif
    </div>
</div>
@endsection