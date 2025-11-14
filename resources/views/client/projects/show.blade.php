@extends('client.layouts.app')

@section('title', $project->name)
@section('page-title', $project->name)
@section('page-subtitle', 'Detail informasi proyek perizinan')

@section('content')
<div class="space-y-6">
    <!-- Back Button -->
    <a href="{{ route('client.projects.index') }}" class="inline-flex items-center text-purple-600 hover:text-purple-800">
        <i class="fas fa-arrow-left mr-2"></i>Kembali ke Daftar Proyek
    </a>

    <!-- Project Header -->
    <div class="bg-gradient-to-r from-purple-600 to-purple-800 rounded-xl shadow-lg p-8 text-white">
        <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4">
            <div class="flex-1">
                <h1 class="text-3xl font-bold mb-3">{{ $project->name }}</h1>
                @if($project->description)
                    <p class="text-purple-100 mb-4">{{ $project->description }}</p>
                @endif
                
                <div class="flex flex-wrap items-center gap-4 text-sm">
                    <span class="px-4 py-2 bg-white/20 backdrop-blur-sm rounded-lg">
                        <i class="fas fa-building mr-2"></i>{{ $project->institution->name ?? 'N/A' }}
                    </span>
                    <span class="px-4 py-2 bg-white/20 backdrop-blur-sm rounded-lg">
                        <i class="fas fa-calendar mr-2"></i>{{ $project->start_date ? $project->start_date->format('d M Y') : 'N/A' }}
                    </span>
                    <span class="px-4 py-2 bg-white/20 backdrop-blur-sm rounded-lg">
                        <i class="fas fa-flag mr-2"></i>{{ $project->deadline ? $project->deadline->format('d M Y') : 'N/A' }}
                    </span>
                </div>
            </div>

            <div class="flex-shrink-0">
                <span class="inline-block px-6 py-3 text-lg font-semibold rounded-lg" 
                      style="background-color: {{ $project->status->color }}; color: white;">
                    {{ $project->status->name }}
                </span>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Progress -->
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
            <div class="flex items-center justify-between mb-3">
                <p class="text-sm text-gray-600">Progress Proyek</p>
                <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-chart-line text-purple-600"></i>
                </div>
            </div>
            <p class="text-3xl font-bold text-purple-600">{{ $stats['progress'] }}%</p>
            <div class="w-full bg-gray-200 rounded-full h-2 mt-3">
                <div class="bg-purple-600 h-2 rounded-full transition-all" style="width: {{ $stats['progress'] }}%"></div>
            </div>
        </div>

        <!-- Tasks -->
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
            <div class="flex items-center justify-between mb-3">
                <p class="text-sm text-gray-600">Task Selesai</p>
                <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-tasks text-green-600"></i>
                </div>
            </div>
            <p class="text-3xl font-bold text-gray-900">
                {{ $stats['completed_tasks'] }}<span class="text-lg text-gray-500">/{{ $stats['total_tasks'] }}</span>
            </p>
            @if($stats['total_tasks'] > 0)
                <p class="text-sm text-gray-500 mt-2">
                    {{ round(($stats['completed_tasks'] / $stats['total_tasks']) * 100) }}% selesai
                </p>
            @endif
        </div>

        <!-- Documents -->
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
            <div class="flex items-center justify-between mb-3">
                <p class="text-sm text-gray-600">Total Dokumen</p>
                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-file-alt text-blue-600"></i>
                </div>
            </div>
            <p class="text-3xl font-bold text-gray-900">{{ $stats['total_documents'] }}</p>
            <p class="text-sm text-gray-500 mt-2">Dokumen tersedia</p>
        </div>

        <!-- Payment -->
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
            <div class="flex items-center justify-between mb-3">
                <p class="text-sm text-gray-600">Sisa Pembayaran</p>
                <div class="w-10 h-10 bg-yellow-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-dollar-sign text-yellow-600"></i>
                </div>
            </div>
            <p class="text-2xl font-bold text-gray-900">
                Rp {{ number_format($stats['pending_payments'], 0, ',', '.') }}
            </p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Column -->
        <div class="lg:col-span-2 space-y-6">
            
            <!-- Project Info -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                <div class="p-6 border-b border-gray-100">
                    <h3 class="text-xl font-bold text-gray-900">
                        <i class="fas fa-info-circle text-purple-600 mr-2"></i>Informasi Proyek
                    </h3>
                </div>
                <div class="p-6">
                    {{-- Link to source application --}}
                    @if($project->permitApplication)
                    <div class="mb-6 p-4 bg-purple-50 border border-purple-200 rounded-lg">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <p class="text-sm text-purple-700 font-medium mb-1">
                                    <i class="fas fa-file-alt mr-2"></i>Dibuat dari Permohonan Izin
                                </p>
                                <p class="text-xs text-purple-600">
                                    No. {{ $project->permitApplication->application_number }}
                                </p>
                                <p class="text-xs text-purple-600 mt-1">
                                    Jenis: {{ $project->permitApplication->permitType->name }}
                                </p>
                            </div>
                            <a href="{{ route('client.applications.show', $project->permitApplication->id) }}" 
                               class="px-3 py-1.5 bg-purple-600 hover:bg-purple-700 text-white text-xs font-medium rounded-lg transition">
                                <i class="fas fa-eye mr-1"></i>Lihat
                            </a>
                        </div>
                    </div>
                    @endif
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Institusi</p>
                            <p class="text-base font-medium text-gray-900">{{ $project->institution->name ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Status</p>
                            <span class="inline-block px-3 py-1 text-sm font-semibold rounded-lg" 
                                  style="background-color: {{ $project->status->color }}20; color: {{ $project->status->color }}">
                                {{ $project->status->name }}
                            </span>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Tanggal Mulai</p>
                            <p class="text-base font-medium text-gray-900">
                                {{ $project->start_date ? $project->start_date->format('d F Y') : '-' }}
                            </p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Deadline</p>
                            <p class="text-base font-medium text-gray-900">
                                {{ $project->deadline ? $project->deadline->format('d F Y') : '-' }}
                            </p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Nilai Kontrak</p>
                            <p class="text-base font-bold text-gray-900">
                                Rp {{ number_format($project->contract_value ?? 0, 0, ',', '.') }}
                            </p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Pembayaran Diterima</p>
                            <p class="text-base font-bold text-green-600">
                                Rp {{ number_format($project->payment_received ?? 0, 0, ',', '.') }}
                            </p>
                        </div>
                    </div>

                    @if($project->notes)
                        <div class="mt-6 pt-6 border-t border-gray-100">
                            <p class="text-sm text-gray-500 mb-2">Catatan</p>
                            <p class="text-gray-700">{{ $project->notes }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Tasks -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                <div class="p-6 border-b border-gray-100">
                    <h3 class="text-xl font-bold text-gray-900">
                        <i class="fas fa-tasks text-purple-600 mr-2"></i>Daftar Task
                    </h3>
                </div>
                <div class="p-6">
                    @if($project->tasks->count() > 0)
                        <div class="space-y-3">
                            @foreach($project->tasks as $task)
                                <div class="flex items-start gap-4 p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                                    <div class="flex-shrink-0 mt-1">
                                        @if($task->status === 'completed')
                                            <i class="fas fa-check-circle text-green-500 text-xl"></i>
                                        @else
                                            <i class="far fa-circle text-gray-400 text-xl"></i>
                                        @endif
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="font-medium text-gray-900 {{ $task->status === 'completed' ? 'line-through' : '' }}">
                                            {{ $task->name }}
                                        </p>
                                        @if($task->description)
                                            <p class="text-sm text-gray-600 mt-1">{{ $task->description }}</p>
                                        @endif
                                        @if($task->deadline)
                                            <p class="text-sm text-gray-500 mt-2">
                                                <i class="fas fa-calendar mr-1"></i>{{ $task->deadline->format('d M Y') }}
                                            </p>
                                        @endif
                                    </div>
                                    <span class="px-3 py-1 text-xs font-semibold rounded-full {{ $task->status === 'completed' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                        {{ $task->status }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <i class="fas fa-tasks text-gray-300 text-4xl mb-3"></i>
                            <p class="text-gray-500">Belum ada task untuk proyek ini</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Documents -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                <div class="p-6 border-b border-gray-100">
                    <h3 class="text-xl font-bold text-gray-900">
                        <i class="fas fa-file-alt text-purple-600 mr-2"></i>Dokumen
                    </h3>
                </div>
                <div class="p-6">
                    @if($project->documents->count() > 0)
                        <div class="space-y-3">
                            @foreach($project->documents as $document)
                                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                                    <div class="flex items-center gap-4">
                                        <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                            <i class="fas fa-file-pdf text-blue-600"></i>
                                        </div>
                                        <div>
                                            <p class="font-medium text-gray-900">{{ $document->name }}</p>
                                            <p class="text-sm text-gray-500">
                                                {{ $document->created_at->format('d M Y') }}
                                            </p>
                                        </div>
                                    </div>
                                    <a href="{{ route('client.documents.download', $document->id) }}" class="px-4 py-2 bg-purple-600 text-white text-sm rounded-lg hover:bg-purple-700 transition">
                                        <i class="fas fa-download mr-2"></i>Download
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <i class="fas fa-file-alt text-gray-300 text-4xl mb-3"></i>
                            <p class="text-gray-500">Belum ada dokumen untuk proyek ini</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Right Column -->
        <div class="space-y-6">
            
            <!-- Timeline/Activity -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                <div class="p-6 border-b border-gray-100">
                    <h3 class="text-xl font-bold text-gray-900">
                        <i class="fas fa-history text-purple-600 mr-2"></i>Aktivitas Terbaru
                    </h3>
                </div>
                <div class="p-6">
                    @if($recentActivities->count() > 0)
                        <div class="space-y-4">
                            @foreach($recentActivities as $log)
                                <div class="flex gap-3">
                                    <div class="flex-shrink-0 w-2 h-2 bg-purple-600 rounded-full mt-2"></div>
                                    <div class="flex-1">
                                        <p class="text-sm text-gray-900">{{ $log->description }}</p>
                                        <p class="text-xs text-gray-500 mt-1">{{ $log->created_at->diffForHumans() }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <i class="fas fa-history text-gray-300 text-4xl mb-3"></i>
                            <p class="text-gray-500">Belum ada aktivitas</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Contact Support -->
            <div class="bg-gradient-to-br from-purple-100 to-purple-200 rounded-xl shadow-sm p-6">
                <div class="text-center">
                    <div class="w-16 h-16 bg-white rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-headset text-purple-600 text-2xl"></i>
                    </div>
                    <h4 class="text-lg font-bold text-gray-900 mb-2">Butuh Bantuan?</h4>
                    <p class="text-sm text-gray-700 mb-4">
                        Hubungi tim kami untuk informasi lebih lanjut tentang proyek ini
                    </p>
                    <a href="https://wa.me/6281234567890" target="_blank" 
                       class="inline-block px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                        <i class="fab fa-whatsapp mr-2"></i>Hubungi via WhatsApp
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
