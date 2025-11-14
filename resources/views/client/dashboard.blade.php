@extends('client.layouts.app')

@section('title', 'Dashboard Client')

@section('content')

@if (session('success'))
                    <div class="mb-6 p-4 bg-green-100 border-l-4 border-green-500 text-green-700 rounded">
                        <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
                    </div>
                @endif

                <!-- Metrics Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <!-- Active Projects -->
                    <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-blue-500">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-600 mb-1">Proyek Aktif</p>
                                <p class="text-3xl font-bold text-gray-800">{{ $activeProjects }}</p>
                            </div>
                            <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-folder-open text-blue-600 text-xl"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Completed Projects -->
                    <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-green-500">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-600 mb-1">Proyek Selesai</p>
                                <p class="text-3xl font-bold text-gray-800">{{ $completedProjects }}</p>
                            </div>
                            <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-check-circle text-green-600 text-xl"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Total Investment -->
                    <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-purple-500">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-600 mb-1">Total Investasi</p>
                                <p class="text-3xl font-bold text-gray-800">Rp {{ number_format($totalInvested, 0, ',', '.') }}</p>
                            </div>
                            <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-wallet text-purple-600 text-xl"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Upcoming Deadlines -->
                    <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-orange-500">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-600 mb-1">Deadline Dekat</p>
                                <p class="text-3xl font-bold text-gray-800">{{ $upcomingDeadlines->count() }}</p>
                            </div>
                            <div class="w-12 h-12 bg-orange-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-clock text-orange-600 text-xl"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    
                    <!-- Active Projects -->
                    <div class="bg-white rounded-xl shadow-sm">
                        <div class="p-6 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-800">Proyek Aktif</h3>
                        </div>
                        <div class="p-6">
                            @forelse($projects->take(5) as $project)
                                <div class="flex items-center justify-between py-3 border-b border-gray-100 last:border-0">
                                    <div class="flex-1">
                                        <p class="font-medium text-gray-800">{{ $project->name }}</p>
                                        <p class="text-sm text-gray-500">{{ $project->permitApplication->permitType->name ?? 'N/A' }}</p>
                                    </div>
                                    <span class="px-3 py-1 text-xs font-semibold rounded-full {{ $project->status && $project->status->name === 'Selesai' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800' }}">
                                        {{ $project->status->name ?? 'N/A' }}
                                    </span>
                                </div>
                            @empty
                                <p class="text-gray-500 text-center py-8">Belum ada proyek</p>
                            @endforelse
                        </div>
                    </div>

                    <!-- Recent Documents -->
                    <div class="bg-white rounded-xl shadow-sm">
                        <div class="p-6 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-800">Dokumen Terbaru</h3>
                        </div>
                        <div class="p-6">
                            @forelse($recentDocuments as $document)
                                <div class="flex items-center justify-between py-3 border-b border-gray-100 last:border-0">
                                    <div class="flex items-center space-x-3 flex-1">
                                        <i class="fas fa-file-pdf text-red-500"></i>
                                        <div class="flex-1 min-w-0">
                                            <p class="font-medium text-gray-800 truncate">{{ $document->document_name }}</p>
                                            <p class="text-sm text-gray-500">{{ $document->created_at->diffForHumans() }}</p>
                                        </div>
                                    </div>
                                    <a href="{{ Storage::url($document->file_path) }}" target="_blank" class="text-purple-600 hover:text-purple-800">
                                        <i class="fas fa-download"></i>
                                    </a>
                                </div>
                            @empty
                                <p class="text-gray-500 text-center py-8">Belum ada dokumen</p>
                            @endforelse
                        </div>
                    </div>

                    <!-- Upcoming Deadlines -->
                    <div class="bg-white rounded-xl shadow-sm lg:col-span-2">
                        <div class="p-6 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-800">Deadline Mendatang (7 Hari)</h3>
                        </div>
                        <div class="p-6">
                            @forelse($upcomingDeadlines as $task)
                                <div class="flex items-center justify-between py-3 border-b border-gray-100 last:border-0">
                                    <div class="flex-1">
                                        <p class="font-medium text-gray-800">{{ $task->name }}</p>
                                        <p class="text-sm text-gray-500">{{ $task->project->name ?? 'N/A' }}</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-sm font-semibold text-orange-600">
                                            {{ $task->due_date->diffForHumans() }}
                                        </p>
                                        <p class="text-xs text-gray-500">{{ $task->due_date->format('d M Y') }}</p>
                                    </div>
                                </div>
                            @empty
                                <p class="text-gray-500 text-center py-8">Tidak ada deadline dalam 7 hari ke depan</p>
                            @endforelse
                        </div>
                    </div>

                </div>

@endsection
