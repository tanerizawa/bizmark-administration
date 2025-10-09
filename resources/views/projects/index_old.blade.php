@extends('layouts.app')

@section('title', 'Manajemen Proyek')

@section('content')
<div class="flex justify-between items-center mb-6">
    <div>
        <h1 class="text-3xl font-bold text-gray-800">Manajemen Proyek</h1>
        <p class="text-gray-600 mt-1">Kelola semua proyek perizinan</p>
    </div>
    <a href="{{ route('projects.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium transition-colors">
        <i class="fas fa-plus mr-2"></i>Tambah Proyek
    </a>
</div>

<!-- Search and Filter -->
<div class="bg-white rounded-lg shadow-sm border mb-6 p-6">
    <form method="GET" action="{{ route('projects.index') }}" class="space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Pencarian</label>
                <input type="text" name="search" value="{{ request('search') }}" 
                       placeholder="Nama proyek atau klien..." 
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Semua Status</option>
                    @foreach($statuses as $status)
                    <option value="{{ $status->id }}" {{ request('status') == $status->id ? 'selected' : '' }}>
                        {{ $status->name }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Institusi</label>
                <select name="institution" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Semua Institusi</option>
                    @foreach($institutions as $institution)
                    <option value="{{ $institution->id }}" {{ request('institution') == $institution->id ? 'selected' : '' }}>
                        {{ $institution->name }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end space-x-2">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md transition-colors">
                    <i class="fas fa-search mr-1"></i>Cari
                </button>
                <a href="{{ route('projects.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-md transition-colors">
                    <i class="fas fa-undo mr-1"></i>Reset
                </a>
            </div>
        </div>
    </form>
</div>

<!-- Projects List -->
<div class="bg-white rounded-lg shadow-sm border">
    @if($projects->count() > 0)
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Proyek</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Klien</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Institusi</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Progress</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Deadline</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($projects as $project)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4">
                        <div>
                            <div class="text-sm font-medium text-gray-900">{{ $project->name }}</div>
                            <div class="text-sm text-gray-500">{{ Str::limit($project->description, 50) }}</div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-sm text-gray-900">{{ $project->client_name }}</div>
                        <div class="text-sm text-gray-500">{{ $project->client_contact }}</div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-sm text-gray-900">{{ $project->institution->name }}</div>
                    </td>
                    <td class="px-6 py-4">
                        @php
                            $statusColor = match($project->status->code ?? '') {
                                'PENAWARAN' => 'bg-yellow-100 text-yellow-800',
                                'KONTRAK' => 'bg-blue-100 text-blue-800',
                                'PENGUMPULAN_DOK' => 'bg-orange-100 text-orange-800',
                                'PROSES_DLH', 'PROSES_BPN', 'PROSES_OSS', 'PROSES_NOTARIS' => 'bg-purple-100 text-purple-800',
                                'MENUNGGU_PERSETUJUAN' => 'bg-yellow-100 text-yellow-800',
                                'SK_TERBIT' => 'bg-green-100 text-green-800',
                                'DIBATALKAN' => 'bg-red-100 text-red-800',
                                'DITUNDA' => 'bg-gray-100 text-gray-800',
                                default => 'bg-gray-100 text-gray-800'
                            };
                        @endphp
                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $statusColor }}">
                            {{ $project->status->name }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center">
                            <div class="w-16 bg-gray-200 rounded-full h-2 mr-2">
                                <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $project->progress_percentage }}%"></div>
                            </div>
                            <span class="text-sm text-gray-600">{{ $project->progress_percentage }}%</span>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        @if($project->deadline)
                            @php
                                $isOverdue = \Carbon\Carbon::parse($project->deadline)->isPast();
                                $isUrgent = \Carbon\Carbon::parse($project->deadline)->diffInDays(now()) <= 7;
                            @endphp
                            <div class="text-sm {{ $isOverdue ? 'text-red-600 font-semibold' : ($isUrgent ? 'text-yellow-600 font-medium' : 'text-gray-900') }}">
                                {{ \Carbon\Carbon::parse($project->deadline)->format('d M Y') }}
                                @if($isOverdue)
                                    <i class="fas fa-exclamation-triangle ml-1"></i>
                                @elseif($isUrgent)
                                    <i class="fas fa-clock ml-1"></i>
                                @endif
                            </div>
                        @else
                            <span class="text-gray-400 text-sm">Tidak ada</span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center space-x-2">
                            <a href="{{ route('projects.show', $project) }}" 
                               class="text-blue-600 hover:text-blue-800 transition-colors" title="Lihat Detail">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('projects.edit', $project) }}" 
                               class="text-yellow-600 hover:text-yellow-800 transition-colors" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('projects.destroy', $project) }}" method="POST" 
                                  class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus proyek ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="text-red-600 hover:text-red-800 transition-colors" title="Hapus">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($projects->hasPages())
    <div class="px-6 py-3 border-t border-gray-200">
        {{ $projects->appends(request()->query())->links() }}
    </div>
    @endif
    @else
    <div class="text-center py-12">
        <i class="fas fa-project-diagram text-gray-400 text-4xl mb-4"></i>
        <h3 class="text-lg font-medium text-gray-900 mb-2">Belum ada proyek</h3>
        <p class="text-gray-500 mb-4">Mulai dengan membuat proyek perizinan pertama Anda.</p>
        <a href="{{ route('projects.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium transition-colors">
            <i class="fas fa-plus mr-2"></i>Tambah Proyek Pertama
        </a>
    </div>
    @endif
</div>

<!-- Quick Stats -->
<div class="mt-6 grid grid-cols-1 md:grid-cols-4 gap-4">
    <div class="bg-white rounded-lg shadow-sm border p-4">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                <i class="fas fa-project-diagram"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">Total Proyek</p>
                <p class="text-2xl font-semibold text-gray-900">{{ $totalProjects }}</p>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-lg shadow-sm border p-4">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                <i class="fas fa-hourglass-half"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">Dalam Progress</p>
                <p class="text-2xl font-semibold text-gray-900">{{ $inProgressProjects }}</p>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-lg shadow-sm border p-4">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-green-100 text-green-600">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">Selesai</p>
                <p class="text-2xl font-semibold text-gray-900">{{ $completedProjects }}</p>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-lg shadow-sm border p-4">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-red-100 text-red-600">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">Terlambat</p>
                <p class="text-2xl font-semibold text-gray-900">{{ $overdueProjects }}</p>
            </div>
        </div>
    </div>
</div>
@endsection