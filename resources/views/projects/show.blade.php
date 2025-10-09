@extends('layouts.app')

@section('title', $project->name)

@section('content')
<div class="max-w-6xl mx-auto">
    <!-- Header -->
    <div class="flex justify-between items-start mb-6">
        <div class="flex items-center">
            <a href="{{ route('projects.index') }}" class="text-apple-blue-dark hover:text-apple-blue mr-4">
                <i class="fas fa-arrow-left text-lg"></i>
            </a>
            <div>
                <h1 class="text-3xl font-bold" style="color: #FFFFFF;">{{ $project->name }}</h1>
                <p class="mt-1" style="color: rgba(235, 235, 245, 0.6);">Detail Proyek Perizinan</p>
            </div>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('projects.edit', $project) }}" 
               class="px-4 py-2 rounded-lg font-medium transition-colors" style="background: rgba(255, 149, 0, 0.9); color: #FFFFFF;">
                <i class="fas fa-edit mr-2"></i>Edit
            </a>
            <form action="{{ route('projects.destroy', $project) }}" method="POST" 
                  class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus proyek ini?')">
                @csrf
                @method('DELETE')
                <button type="submit" 
                        class="px-4 py-2 rounded-lg font-medium transition-colors" style="background: rgba(255, 59, 48, 0.9); color: #FFFFFF;">
                    <i class="fas fa-trash mr-2"></i>Hapus
                </button>
            </form>
        </div>
    </div>

    <!-- Quick Status Update -->
    <div class="card-elevated rounded-apple-lg p-4 mb-6">
        <form action="{{ route('projects.update-status', $project) }}" method="POST" class="flex items-center space-x-4">
            @csrf
            @method('PATCH')
            <div class="flex items-center space-x-2">
                <label class="text-sm font-medium" style="color: rgba(235, 235, 245, 0.8);">Quick Status Update:</label>
                <select name="status_id" class="input-dark px-3 py-2 rounded-md">
                    @foreach($statuses as $status)
                    <option value="{{ $status->id }}" {{ $project->status_id == $status->id ? 'selected' : '' }}>
                        {{ $status->name }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="flex-1">
                <input type="text" name="notes" placeholder="Catatan perubahan status (opsional)" 
                       class="input-dark w-full px-3 py-2 rounded-md">
            </div>
            <button type="submit" class="btn-primary px-4 py-2 rounded-md">
                <i class="fas fa-save mr-1"></i>Update
            </button>
        </form>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Main Information -->
        <div class="md:col-span-2 min-w-0 space-y-6">
            <!-- Tab Navigation -->
            <div class="card-elevated rounded-apple-lg p-1">
                <div class="flex space-x-1" role="tablist">
                    <button class="tab-button active flex-1 px-4 py-3 rounded-lg font-medium transition-colors" 
                            onclick="switchTab('overview')" id="tab-overview"
                            style="color: #FFFFFF;">
                        <i class="fas fa-info-circle mr-2"></i>Overview
                    </button>
                    <button class="tab-button flex-1 px-4 py-3 rounded-lg font-medium transition-colors" 
                            onclick="switchTab('permits')" id="tab-permits"
                            style="color: rgba(235, 235, 245, 0.6);">
                        <i class="fas fa-certificate mr-2"></i>Izin & Prasyarat
                    </button>
                    <button class="tab-button flex-1 px-4 py-3 rounded-lg font-medium transition-colors" 
                            onclick="switchTab('tasks')" id="tab-tasks"
                            style="color: rgba(235, 235, 245, 0.6);">
                        <i class="fas fa-tasks mr-2"></i>Tugas
                    </button>       
                    <button class="tab-button flex-1 px-4 py-3 rounded-lg font-medium transition-colors" 
                            onclick="switchTab('documents')" id="tab-documents"
                            style="color: rgba(235, 235, 245, 0.6);">
                        <i class="fas fa-file-alt mr-2"></i>Dokumen
                    </button>             
                    <button class="tab-button flex-1 px-4 py-3 rounded-lg font-medium transition-colors" 
                            onclick="switchTab('financial')" id="tab-financial"
                            style="color: rgba(235, 235, 245, 0.6);">
                        <i class="fas fa-file-invoice-dollar mr-2"></i>Financial
                    </button>

                </div>
            </div>

            <!-- Tab Content: Overview -->
            <div id="content-overview" class="tab-content">
            <!-- Project Overview -->
            <div class="card-elevated rounded-apple-lg p-4">
                <h3 class="text-base font-semibold mb-3" style="color: #FFFFFF;">
                    <i class="fas fa-info-circle mr-2 text-apple-blue-dark"></i>Informasi Proyek
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium mb-1" style="color: rgba(235, 235, 245, 0.6);">Nama Proyek</label>
                        <p class="font-medium break-words" style="color: rgba(235, 235, 245, 0.9);">{{ $project->name }}</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium mb-1" style="color: rgba(235, 235, 245, 0.6);">Institusi Tujuan</label>
                        <p class="break-words" style="color: rgba(235, 235, 245, 0.9);">{{ $project->institution->name }}</p>
                    </div>
                    
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium mb-1" style="color: rgba(235, 235, 245, 0.6);">Deskripsi</label>
                        <p class="break-words" style="color: rgba(235, 235, 245, 0.9);">{{ $project->description }}</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium mb-1" style="color: rgba(235, 235, 245, 0.6);">Status Saat Ini</label>
                        @php
                            $statusColor = match($project->status->code ?? '') {
                                'PENAWARAN' => 'background: rgba(255, 149, 0, 0.3); color: rgba(255, 149, 0, 1);',
                                'KONTRAK' => 'background: rgba(10, 132, 255, 0.3); color: rgba(10, 132, 255, 1);',
                                'PENGUMPULAN_DOK' => 'background: rgba(255, 149, 0, 0.3); color: rgba(255, 149, 0, 1);',
                                'PROSES_DLH', 'PROSES_BPN', 'PROSES_OSS', 'PROSES_NOTARIS' => 'background: rgba(191, 90, 242, 0.3); color: rgba(191, 90, 242, 1);',
                                'MENUNGGU_PERSETUJUAN' => 'background: rgba(255, 149, 0, 0.3); color: rgba(255, 149, 0, 1);',
                                'SK_TERBIT' => 'background: rgba(52, 199, 89, 0.3); color: rgba(52, 199, 89, 1);',
                                'DIBATALKAN' => 'background: rgba(255, 59, 48, 0.3); color: rgba(255, 59, 48, 1);',
                                'DITUNDA' => 'background: rgba(142, 142, 147, 0.3); color: rgba(142, 142, 147, 1);',
                                default => 'background: rgba(142, 142, 147, 0.3); color: rgba(142, 142, 147, 1);'
                            };
                        @endphp
                        <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full" style="{{ $statusColor }}">
                            {{ $project->status->name }}
                        </span>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium mb-1" style="color: rgba(235, 235, 245, 0.6);">Progress</label>
                        <div class="flex items-center">
                            <div class="flex-1 rounded-full h-3 mr-3" style="background: rgba(58, 58, 60, 0.8);">
                                <div class="h-3 rounded-full transition-all duration-300" 
                                     style="width: {{ $project->progress_percentage }}%; background: linear-gradient(90deg, rgba(10, 132, 255, 1), rgba(30, 150, 255, 1));"></div>
                            </div>
                            <span class="text-sm font-medium" style="color: rgba(235, 235, 245, 0.8);">{{ $project->progress_percentage }}%</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Client Information -->
            <div class="card-elevated rounded-apple-lg p-4">
                <h3 class="text-base font-semibold mb-3" style="color: #FFFFFF;">
                    <i class="fas fa-user mr-2 text-apple-blue-dark"></i>Informasi Klien
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium mb-1" style="color: rgba(235, 235, 245, 0.6);">Nama Klien</label>
                        @if($project->client)
                            <p class="font-medium break-words flex items-center" style="color: rgba(235, 235, 245, 0.9);">
                                {{ $project->client->company_name ?? $project->client->name }}
                                <a href="{{ route('clients.show', $project->client) }}" 
                                   class="ml-2 text-xs px-2 py-0.5 rounded transition-colors" 
                                   style="background: rgba(0, 122, 255, 0.15); color: var(--apple-blue);">
                                    <i class="fas fa-external-link-alt mr-1"></i>Lihat Detail
                                </a>
                            </p>
                            @if($project->client->company_name && $project->client->name != $project->client->company_name)
                                <p class="text-xs mt-0.5" style="color: rgba(235, 235, 245, 0.5);">Contact: {{ $project->client->name }}</p>
                            @endif
                        @else
                            <p class="font-medium" style="color: rgba(235, 235, 245, 0.9);">{{ $project->client_name ?? '-' }}</p>
                        @endif
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium mb-1" style="color: rgba(235, 235, 245, 0.6);">Kontak</label>
                        @if($project->client)
                            <div class="space-y-1">
                                @if($project->client->email)
                                    <p class="text-sm flex items-center" style="color: rgba(235, 235, 245, 0.9);">
                                        <i class="fas fa-envelope mr-2 text-apple-blue"></i>
                                        <a href="mailto:{{ $project->client->email }}" class="text-apple-blue hover:underline">{{ $project->client->email }}</a>
                                    </p>
                                @endif
                                @if($project->client->mobile)
                                    <p class="text-sm flex items-center" style="color: rgba(235, 235, 245, 0.9);">
                                        <i class="fab fa-whatsapp mr-2 text-apple-green"></i>
                                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $project->client->mobile) }}" target="_blank" class="text-apple-blue hover:underline">{{ $project->client->mobile }}</a>
                                    </p>
                                @elseif($project->client->phone)
                                    <p class="text-sm flex items-center" style="color: rgba(235, 235, 245, 0.9);">
                                        <i class="fas fa-phone mr-2 text-apple-blue"></i>
                                        <a href="tel:{{ $project->client->phone }}" class="text-apple-blue hover:underline">{{ $project->client->phone }}</a>
                                    </p>
                                @endif
                                @if(!$project->client->email && !$project->client->mobile && !$project->client->phone)
                                    <p class="text-sm" style="color: rgba(235, 235, 245, 0.5);">-</p>
                                @endif
                            </div>
                        @else
                            <p style="color: rgba(235, 235, 245, 0.9);">{{ $project->client_contact ?? '-' }}</p>
                        @endif
                    </div>
                    
                    @if(($project->client && $project->client->address) || $project->client_address)
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium mb-1" style="color: rgba(235, 235, 245, 0.6);">Alamat</label>
                        <p class="break-words" style="color: rgba(235, 235, 245, 0.9);">
                            {{ $project->client ? $project->client->address : $project->client_address }}
                            @if($project->client && ($project->client->city || $project->client->province))
                                <span class="text-sm" style="color: rgba(235, 235, 245, 0.6);">
                                    @if($project->client->city), {{ $project->client->city }}@endif
                                    @if($project->client->province), {{ $project->client->province }}@endif
                                    @if($project->client->postal_code) {{ $project->client->postal_code }}@endif
                                </span>
                            @endif
                        </p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Schedule Information -->
            <div class="card-elevated rounded-apple-lg p-4">
                <h3 class="text-base font-semibold mb-3" style="color: #FFFFFF;">
                    <i class="fas fa-calendar mr-2 text-apple-blue-dark"></i>Jadwal Proyek
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium mb-1" style="color: rgba(235, 235, 245, 0.6);">Tanggal Mulai</label>
                        <p class="font-medium" style="color: rgba(235, 235, 245, 0.9);">
                            {{ $project->start_date ? $project->start_date->format('d M Y') : 'Belum ditentukan' }}
                        </p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium mb-1" style="color: rgba(235, 235, 245, 0.6);">Target Selesai</label>
                        @if($project->deadline)
                            @php
                                $isOverdue = $project->deadline->isPast();
                                $isUrgent = $project->deadline->diffInDays(now()) <= 7;
                            @endphp
                            <p class="font-medium" style="color: {{ $isOverdue ? 'rgba(255, 59, 48, 1)' : ($isUrgent ? 'rgba(255, 149, 0, 1)' : 'rgba(235, 235, 245, 0.9)') }};">
                                {{ $project->deadline->format('d M Y') }}
                                @if($isOverdue)
                                    <span class="text-sm">(Terlambat)</span>
                                @elseif($isUrgent)
                                    <span class="text-sm">(Mendesak)</span>
                                @endif
                            </p>
                        @else
                            <p style="color: rgba(235, 235, 245, 0.4);">Belum ditentukan</p>
                        @endif
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium mb-1" style="color: rgba(235, 235, 245, 0.6);">Durasi</label>
                        @if($project->start_date && $project->deadline)
                            <p style="color: rgba(235, 235, 245, 0.9);">{{ $project->start_date->diffInDays($project->deadline) }} hari</p>
                        @else
                            <p style="color: rgba(235, 235, 245, 0.4);">-</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Quick Financial Summary -->
            <div class="card-elevated rounded-apple-lg p-4">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-base font-semibold" style="color: #FFFFFF;">
                        <i class="fas fa-wallet mr-2 text-apple-blue-dark"></i>Ringkasan Keuangan
                    </h3>
                    <button onclick="switchTab('financial')" class="text-xs px-2.5 py-1 rounded-md transition-colors" 
                            style="background: rgba(0, 122, 255, 0.15); color: rgba(0, 122, 255, 1);">
                        <i class="fas fa-arrow-right mr-1"></i>Lihat Detail
                    </button>
                </div>
                
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                    <div class="text-center p-2.5 rounded-lg" style="background: rgba(58, 58, 60, 0.5);">
                        <p class="text-xs mb-1" style="color: rgba(235, 235, 245, 0.6);">Nilai Kontrak</p>
                        <p class="text-sm font-bold" style="color: #FFFFFF;">Rp {{ number_format($totalBudget, 0, ',', '.') }}</p>
                    </div>
                    
                    <div class="text-center p-2.5 rounded-lg" style="background: rgba(52, 199, 89, 0.15);">
                        <p class="text-xs mb-1" style="color: rgba(235, 235, 245, 0.6);">Diterima</p>
                        <p class="text-sm font-bold" style="color: rgba(52, 199, 89, 1);">Rp {{ number_format($totalReceived, 0, ',', '.') }}</p>
                        <p class="text-xs" style="color: rgba(235, 235, 245, 0.5);">
                            {{ $totalBudget > 0 ? number_format(($totalReceived / $totalBudget) * 100, 1) : 0 }}%
                        </p>
                    </div>
                    
                    <div class="text-center p-2.5 rounded-lg" style="background: rgba(255, 59, 48, 0.15);">
                        <p class="text-xs mb-1" style="color: rgba(235, 235, 245, 0.6);">Pengeluaran</p>
                        <p class="text-sm font-bold" style="color: rgba(255, 59, 48, 1);">Rp {{ number_format($totalExpenses, 0, ',', '.') }}</p>
                        <p class="text-xs" style="color: rgba(235, 235, 245, 0.5);">
                            {{ $totalBudget > 0 ? number_format(($totalExpenses / $totalBudget) * 100, 1) : 0 }}%
                        </p>
                    </div>
                    
                    <div class="text-center p-2.5 rounded-lg" style="background: {{ $profitMargin >= 0 ? 'rgba(0, 122, 255, 0.15)' : 'rgba(255, 59, 48, 0.15)' }};">
                        <p class="text-xs mb-1" style="color: rgba(235, 235, 245, 0.6);">Profit</p>
                        <p class="text-sm font-bold" style="color: {{ $profitMargin >= 0 ? 'rgba(0, 122, 255, 1)' : 'rgba(255, 59, 48, 1)' }};">
                            {{ $profitMargin < 0 ? '-' : '' }}Rp {{ number_format(abs($profitMargin), 0, ',', '.') }}
                        </p>
                        <p class="text-xs" style="color: rgba(235, 235, 245, 0.5);">
                            {{ $totalReceived > 0 ? number_format(($profitMargin / $totalReceived) * 100, 1) : 0 }}% margin
                        </p>
                    </div>
                </div>
            </div>

            <!-- Notes -->
            @if($project->notes)
            <div class="card-elevated rounded-apple-lg p-4">
                <h3 class="text-base font-semibold mb-3" style="color: #FFFFFF;">
                    <i class="fas fa-sticky-note mr-2 text-apple-blue-dark"></i>Catatan
                </h3>
                <p class="text-sm whitespace-pre-line" style="color: rgba(235, 235, 245, 0.8);">{{ $project->notes }}</p>
            </div>
            @endif
            </div>
            <!-- End of Overview Tab Content -->

            <!-- Tab Content: Financial (Sprint 6) -->
            <div id="content-financial" class="tab-content hidden">
                <div class="space-y-6 min-w-0" data-scope="financial-tab">
                    @include('projects.partials.financial-tab')
                </div>
            </div>

            <!-- Tab Content: Permits -->
            <div id="content-permits" class="tab-content hidden">
                <div class="space-y-6 min-w-0" data-scope="permits-tab">
                    @include('projects.partials.permits-tab')
                </div>
            </div>

            <!-- Tab Content: Tasks -->
            <div id="content-tasks" class="tab-content hidden">
                <div class="space-y-6 min-w-0" data-scope="tasks-tab">
                    @include('projects.partials.tasks-tab')
                </div>
            </div>

            <!-- Tab Content: Documents -->
            <div id="content-documents" class="tab-content hidden">
                <div class="space-y-6 min-w-0" data-scope="documents-tab">
                <div class="card-elevated rounded-apple-lg p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold" style="color: #FFFFFF;">
                            <i class="fas fa-file-alt mr-2 text-apple-blue-dark"></i>Daftar Dokumen Proyek
                        </h3>
                        <a href="{{ route('documents.create', ['project_id' => $project->id]) }}" 
                           class="px-4 py-2 rounded-lg text-sm font-medium transition-colors" 
                           style="background: rgba(10, 132, 255, 0.9); color: #FFFFFF;">
                            <i class="fas fa-plus mr-2"></i>Tambah Dokumen
                        </a>
                    </div>

                    @if($project->documents->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach($project->documents as $document)
                                <div class="p-4 rounded-lg transition-colors hover:bg-opacity-80" 
                                     style="background: rgba(58, 58, 60, 0.6);">
                                    <div class="flex items-start space-x-3">
                                        <div class="flex-shrink-0">
                                            @php
                                                $iconColor = match($document->type) {
                                                    'KONTRAK' => 'rgba(10, 132, 255, 1)',
                                                    'IZIN' => 'rgba(52, 199, 89, 1)',
                                                    'LAPORAN' => 'rgba(191, 90, 242, 1)',
                                                    'SURAT' => 'rgba(255, 149, 0, 1)',
                                                    default => 'rgba(142, 142, 147, 1)'
                                                };
                                            @endphp
                                            <i class="fas fa-file-alt text-2xl" style="color: {{ $iconColor }};"></i>
                                        </div>
                                        
                                        <div class="flex-1 min-w-0">
                                            <a href="{{ route('documents.show', $document) }}" 
                                               class="text-sm font-semibold hover:text-apple-blue-dark transition-colors block mb-1" 
                                               style="color: #FFFFFF;">
                                                {{ $document->name }}
                                            </a>
                                            
                                            <div class="flex flex-wrap items-center gap-2 text-xs" style="color: rgba(235, 235, 245, 0.6);">
                                                <span class="inline-flex px-2 py-0.5 rounded" style="background: rgba(58, 58, 60, 0.8);">
                                                    {{ $document->type }}
                                                </span>
                                                
                                                @if($document->number)
                                                    <span>
                                                        <i class="fas fa-hashtag mr-1"></i>
                                                        {{ $document->number }}
                                                    </span>
                                                @endif
                                                
                                                @if($document->date)
                                                    <span>
                                                        <i class="fas fa-calendar mr-1"></i>
                                                        {{ $document->date->format('d M Y') }}
                                                    </span>
                                                @endif
                                            </div>
                                            
                                            @if($document->status)
                                                @php
                                                    $docStatusStyle = match($document->status) {
                                                        'DISETUJUI' => 'background: rgba(52, 199, 89, 0.3); color: rgba(52, 199, 89, 1);',
                                                        'MENUNGGU_PERSETUJUAN' => 'background: rgba(255, 149, 0, 0.3); color: rgba(255, 149, 0, 1);',
                                                        'DITOLAK' => 'background: rgba(255, 59, 48, 0.3); color: rgba(255, 59, 48, 1);',
                                                        default => 'background: rgba(142, 142, 147, 0.3); color: rgba(142, 142, 147, 1);'
                                                    };
                                                @endphp
                                                <div class="mt-2">
                                                    <span class="inline-flex px-2 py-0.5 text-xs font-semibold rounded-full" style="{{ $docStatusStyle }}">
                                                        {{ str_replace('_', ' ', $document->status) }}
                                                    </span>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <i class="fas fa-file-alt text-4xl mb-3" style="color: rgba(235, 235, 245, 0.3);"></i>
                            <p style="color: rgba(235, 235, 245, 0.6);">Belum ada dokumen untuk proyek ini</p>
                            <a href="{{ route('documents.create', ['project_id' => $project->id]) }}" 
                               class="inline-block mt-3 px-4 py-2 rounded-lg text-sm font-medium transition-colors" 
                               style="background: rgba(10, 132, 255, 0.9); color: #FFFFFF;">
                                <i class="fas fa-plus mr-2"></i>Upload Dokumen Pertama
                            </a>
                        </div>
                    @endif
                </div>
                </div>
            </div>
            <!-- End of Documents Tab Content -->

            <!-- Notes (commented out) -->
            @if(false && $project->notes)
            <div class="card-elevated rounded-apple-lg p-6">
                <h3 class="text-lg font-semibold mb-4" style="color: #FFFFFF;">
                    <i class="fas fa-sticky-note mr-2 text-apple-blue-dark"></i>Catatan
                </h3>
                <p class="whitespace-pre-line" style="color: rgba(235, 235, 245, 0.8);">{{ $project->notes }}</p>
            </div>
            @endif
        </div>
        <!-- End of Main Content Wrapper (md:col-span-2) -->

        <!-- Sidebar -->
        <div class="md:col-span-1 min-w-0">
            <!-- Combined Card: Recent Activity + Project Statistics -->
            <div class="card-elevated rounded-apple-lg">
                <!-- Recent Activity Section -->
                <div class="p-6 border-b" style="border-color: rgba(58, 58, 60, 0.6);">
                <h3 class="text-lg font-semibold mb-4" style="color: #FFFFFF;">
                    <i class="fas fa-history mr-2 text-apple-blue-dark"></i>Aktivitas Terbaru
                </h3>
                @if($project->logs && $project->logs->count() > 0)
                <div class="space-y-3 max-h-80 overflow-y-auto custom-scrollbar">
                    @foreach($project->logs->take(10) as $log)
                    <div class="flex items-start space-x-3 p-3 rounded-lg transition-colors hover:bg-opacity-80" style="background: rgba(58, 58, 60, 0.5);">
                        <div class="flex-shrink-0 mt-1">
                            @if(str_contains(strtolower($log->description), 'selesai') || str_contains(strtolower($log->description), 'completed'))
                                <i class="fas fa-check-circle text-sm" style="color: rgba(52, 199, 89, 0.9);"></i>
                            @elseif(str_contains(strtolower($log->description), 'tambah') || str_contains(strtolower($log->description), 'created'))
                                <i class="fas fa-plus-circle text-sm" style="color: rgba(0, 122, 255, 0.9);"></i>
                            @elseif(str_contains(strtolower($log->description), 'ubah') || str_contains(strtolower($log->description), 'updated'))
                                <i class="fas fa-edit text-sm" style="color: rgba(255, 204, 0, 0.9);"></i>
                            @elseif(str_contains(strtolower($log->description), 'hapus') || str_contains(strtolower($log->description), 'deleted'))
                                <i class="fas fa-trash text-sm" style="color: rgba(255, 59, 48, 0.9);"></i>
                            @else
                                <i class="fas fa-circle text-xs" style="color: rgba(0, 122, 255, 0.9);"></i>
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm break-words" style="color: rgba(235, 235, 245, 0.9);">{{ $log->description }}</p>
                            <div class="flex items-center mt-1 space-x-2">
                                <p class="text-xs" style="color: rgba(235, 235, 245, 0.6);">
                                    {{ $log->created_at->diffForHumans() }}
                                </p>
                                @if($log->user)
                                <span class="text-xs" style="color: rgba(235, 235, 245, 0.5);">•</span>
                                <p class="text-xs" style="color: rgba(235, 235, 245, 0.6);">
                                    {{ $log->user->name }}
                                </p>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="text-center py-8">
                    <i class="fas fa-inbox text-4xl mb-3" style="color: rgba(235, 235, 245, 0.3);"></i>
                    <p class="text-sm" style="color: rgba(235, 235, 245, 0.6);">Belum ada aktivitas</p>
                </div>
                @endif
                </div>

                <!-- Project Statistics Section -->
                <div class="p-6">
                <h3 class="text-lg font-semibold mb-4" style="color: #FFFFFF;">
                    <i class="fas fa-chart-pie mr-2 text-apple-blue-dark"></i>Statistik Proyek
                </h3>
                <div class="space-y-4">
                    <!-- Progress -->
                    <div>
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-sm" style="color: rgba(235, 235, 245, 0.6);">Progress Proyek</span>
                            <span class="font-semibold" style="color: rgba(235, 235, 245, 0.9);">{{ $project->progress_percentage ?? 0 }}%</span>
                        </div>
                        <div class="w-full h-2 rounded-full" style="background: rgba(58, 58, 60, 0.6);">
                            <div class="h-full rounded-full transition-all" 
                                 style="width: {{ $project->progress_percentage ?? 0 }}%; background: rgba(0, 122, 255, 0.9);"></div>
                        </div>
                    </div>
                    
                    <!-- Tasks -->
                    <div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm" style="color: rgba(235, 235, 245, 0.6);">Total Tugas</span>
                            <span class="font-semibold" style="color: rgba(235, 235, 245, 0.9);">{{ $project->tasks->count() }}</span>
                        </div>
                        @if($project->tasks->count() > 0)
                        <div class="mt-1 flex justify-between text-xs">
                            <span style="color: rgba(52, 199, 89, 0.9);">
                                <i class="fas fa-check-circle mr-1"></i>{{ $project->tasks->where('status', 'done')->count() }} selesai
                            </span>
                            <span style="color: rgba(255, 204, 0, 0.9);">
                                <i class="fas fa-clock mr-1"></i>{{ $project->tasks->where('status', 'in_progress')->count() }} berjalan
                            </span>
                            <span style="color: rgba(255, 149, 0, 0.9);">
                                <i class="fas fa-pause-circle mr-1"></i>{{ $project->tasks->whereIn('status', ['todo', 'blocked'])->count() }} pending
                            </span>
                        </div>
                        @endif
                    </div>
                    
                    <!-- Permits -->
                    <div class="flex justify-between items-center">
                        @php
                            $completedPermits = $project->permits->whereIn('status', [
                                \App\Models\ProjectPermit::STATUS_APPROVED,
                                \App\Models\ProjectPermit::STATUS_EXISTING,
                            ])->count();
                        @endphp
                        <span class="text-sm" style="color: rgba(235, 235, 245, 0.6);">Izin & Prasyarat</span>
                        <span class="font-semibold" style="color: rgba(235, 235, 245, 0.9);">
                            {{ $completedPermits }}/{{ $project->permits->count() }}
                        </span>
                    </div>
                    
                    <!-- Documents -->
                    <div class="flex justify-between items-center">
                        <span class="text-sm" style="color: rgba(235, 235, 245, 0.6);">Dokumen</span>
                        <span class="font-semibold" style="color: rgba(235, 235, 245, 0.9);">{{ $project->documents->count() }}</span>
                    </div>
                    
                    <!-- Financial Summary -->
                    @if($project->contract_value > 0)
                    <div class="pt-3 border-t" style="border-color: rgba(58, 58, 60, 0.6);">
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-sm font-medium" style="color: rgba(235, 235, 245, 0.8);">Financial</span>
                        </div>
                        <div class="space-y-2">
                            <div class="flex justify-between text-xs">
                                <span style="color: rgba(235, 235, 245, 0.6);">Nilai Kontrak</span>
                                <span style="color: rgba(235, 235, 245, 0.9);">Rp {{ number_format($project->contract_value, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between text-xs">
                                <span style="color: rgba(235, 235, 245, 0.6);">Pembayaran Diterima</span>
                                <span style="color: rgba(52, 199, 89, 0.9);">Rp {{ number_format($project->payment_received ?? 0, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between text-xs">
                                <span style="color: rgba(235, 235, 245, 0.6);">Total Pengeluaran</span>
                                <span style="color: rgba(255, 59, 48, 0.9);">Rp {{ number_format($project->expenses->sum('amount') ?? 0, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>
                    @endif
                    
                    <!-- Timeline -->
                    <div class="pt-3 border-t" style="border-color: rgba(58, 58, 60, 0.6);">
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-sm" style="color: rgba(235, 235, 245, 0.6);">Dibuat</span>
                            <span class="text-sm" style="color: rgba(235, 235, 245, 0.6);">{{ $project->created_at->format('d M Y') }}</span>
                        </div>
                        @if($project->deadline)
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-sm" style="color: rgba(235, 235, 245, 0.6);">Deadline</span>
                            <span class="text-sm font-medium {{ $project->deadline->isPast() ? 'text-red-400' : '' }}" 
                                  style="color: {{ $project->deadline->isPast() ? 'rgba(255, 59, 48, 0.9)' : 'rgba(235, 235, 245, 0.9)' }};">
                                {{ $project->deadline->format('d M Y') }}
                                @if($project->deadline->isFuture())
                                    <span class="text-xs" style="color: rgba(235, 235, 245, 0.6);">({{ $project->deadline->diffForHumans() }})</span>
                                @else
                                    <span class="text-xs" style="color: rgba(255, 59, 48, 0.9);">(Terlambat)</span>
                                @endif
                            </span>
                        </div>
                        @endif
                        <div class="flex justify-between items-center">
                            <span class="text-sm" style="color: rgba(235, 235, 245, 0.6);">Terakhir Update</span>
                            <span class="text-sm" style="color: rgba(235, 235, 245, 0.6);">{{ $project->updated_at->diffForHumans() }}</span>
                        </div>
                    </div>
                </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Payment Modal -->
<div id="paymentModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50" onclick="closePaymentModal()">
    <div class="card-elevated rounded-apple-lg p-6 w-full max-w-2xl mx-4 max-h-[90vh] overflow-y-auto" onclick="event.stopPropagation();">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-xl font-bold" style="color: #FFFFFF;">
                <i class="fas fa-money-bill-wave mr-2 text-apple-blue-dark"></i>Tambah Pembayaran
            </h3>
            <button onclick="closePaymentModal()" type="button"
                    class="text-2xl hover:opacity-75 transition-opacity" style="color: rgba(235, 235, 245, 0.6);">×</button>
        </div>

        <form action="{{ route('projects.payments.store', $project) }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium mb-2" style="color: rgba(235, 235, 245, 0.8);">
                        <i class="fas fa-file-invoice mr-1" style="color: rgba(0, 122, 255, 1);"></i>
                        Invoice (Opsional)
                    </label>
                    <select name="invoice_id" id="payment_invoice_id" class="input-dark w-full px-4 py-2.5 rounded-lg" onchange="updatePaymentAmount()">
                        <option value="">Tidak terkait invoice (pembayaran umum)</option>
                        @foreach($project->invoices()->whereIn('status', ['sent', 'partial', 'overdue'])->get() as $inv)
                        <option value="{{ $inv->id }}" data-remaining="{{ $inv->remaining_amount }}">
                            {{ $inv->invoice_number }} - Sisa: Rp {{ number_format($inv->remaining_amount, 0, ',', '.') }}
                        </option>
                        @endforeach
                    </select>
                    <p class="text-xs mt-1" style="color: rgba(235, 235, 245, 0.5);">
                        Pilih invoice jika pembayaran ini untuk melunasi invoice tertentu. Biarkan kosong jika pembayaran umum.
                    </p>
                </div>
                
                <div>
                    <label class="block text-sm font-medium mb-2" style="color: rgba(235, 235, 245, 0.8);">Tanggal Pembayaran *</label>
                    <input type="date" name="payment_date" required
                           class="input-dark w-full px-4 py-2.5 rounded-lg" value="{{ date('Y-m-d') }}">
                </div>

                <div>
                    <label class="block text-sm font-medium mb-2" style="color: rgba(235, 235, 245, 0.8);">Jumlah (Rp) *</label>
                    <input type="number" name="amount" id="payment_amount" required min="0" step="0.01"
                           class="input-dark w-full px-4 py-2.5 rounded-lg" placeholder="0">
                </div>

                <div>
                    <label class="block text-sm font-medium mb-2" style="color: rgba(235, 235, 245, 0.8);">Tipe Pembayaran *</label>
                    <select name="payment_type" required class="input-dark w-full px-4 py-2.5 rounded-lg">
                        <option value="">Pilih tipe...</option>
                        <option value="dp">Down Payment (DP)</option>
                        <option value="progress">Progress/Termin</option>
                        <option value="final">Pelunasan</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-2" style="color: rgba(235, 235, 245, 0.8);">Metode Pembayaran *</label>
                    <select name="payment_method" required class="input-dark w-full px-4 py-2.5 rounded-lg">
                        <option value="">Pilih metode...</option>
                        <option value="bank_transfer">Transfer Bank</option>
                        <option value="cash">Tunai</option>
                        <option value="check">Cek</option>
                        <option value="giro">Giro</option>
                        <option value="other">Lainnya</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-2" style="color: rgba(235, 235, 245, 0.8);">Akun Bank/Kas</label>
                    <select name="bank_account_id" class="input-dark w-full px-4 py-2.5 rounded-lg">
                        <option value="">Pilih akun...</option>
                        @foreach(\App\Models\CashAccount::active()->get() as $account)
                        <option value="{{ $account->id }}">{{ $account->account_name }} - {{ $account->formatted_balance }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-2" style="color: rgba(235, 235, 245, 0.8);">No. Referensi</label>
                    <input type="text" name="reference_number" maxlength="100"
                           class="input-dark w-full px-4 py-2.5 rounded-lg" placeholder="No. transfer/cek/giro">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium mb-2" style="color: rgba(235, 235, 245, 0.8);">Deskripsi/Catatan</label>
                    <textarea name="description" rows="2"
                              class="input-dark w-full px-4 py-2.5 rounded-lg" placeholder="Catatan pembayaran..."></textarea>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium mb-2" style="color: rgba(235, 235, 245, 0.8);">Upload Bukti (PDF/Gambar, max 5MB)</label>
                    <input type="file" name="receipt_file" accept=".pdf,.jpg,.jpeg,.png"
                           class="input-dark w-full px-4 py-2.5 rounded-lg">
                </div>
            </div>

            <div class="flex justify-end space-x-3 mt-6">
                <button type="button" onclick="closePaymentModal()"
                        class="px-6 py-2.5 rounded-lg font-medium transition-colors hover:opacity-80" 
                        style="background: rgba(58, 58, 60, 0.8); color: rgba(235, 235, 245, 0.8);">
                    Batal
                </button>
                <button type="submit" class="px-6 py-2.5 rounded-lg font-medium transition-colors hover:opacity-90" 
                        style="background: rgba(52, 199, 89, 0.9); color: #FFFFFF;">
                    <i class="fas fa-save mr-2"></i>Simpan Pembayaran
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Expense Modal -->
<div id="expenseModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50" onclick="closeExpenseModal()">
    <div class="card-elevated rounded-apple-lg p-6 w-full max-w-2xl mx-4 max-h-[90vh] overflow-y-auto" onclick="event.stopPropagation();">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-xl font-bold" style="color: #FFFFFF;">
                <i class="fas fa-shopping-cart mr-2 text-apple-blue-dark"></i>Tambah Pengeluaran
            </h3>
            <button onclick="closeExpenseModal()" type="button"
                    class="text-2xl hover:opacity-75 transition-opacity" style="color: rgba(235, 235, 245, 0.6);">×</button>
        </div>

        <form id="expenseForm" action="{{ route('projects.financial-expenses.store', $project) }}" method="POST" enctype="multipart/form-data" onsubmit="return handleExpenseSubmit(event)">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-2" style="color: rgba(235, 235, 245, 0.8);">Tanggal Pengeluaran *</label>
                    <input type="date" name="expense_date" required
                           class="input-dark w-full px-4 py-2.5 rounded-lg" value="{{ date('Y-m-d') }}">
                </div>

                <div>
                    <label class="block text-sm font-medium mb-2" style="color: rgba(235, 235, 245, 0.8);">Jumlah (Rp) *</label>
                    <input type="number" name="amount" required min="0" step="0.01"
                           class="input-dark w-full px-4 py-2.5 rounded-lg" placeholder="0">
                </div>

                <div>
                    <label class="block text-sm font-medium mb-2" style="color: rgba(235, 235, 245, 0.8);">Kategori *</label>
                    <select name="category" required class="input-dark w-full px-4 py-2.5 rounded-lg">
                        <option value="">Pilih kategori...</option>
                        <optgroup label="SDM & Personel">
                            <option value="personnel">💼 Gaji & Honor</option>
                            <option value="commission">🤝 Komisi</option>
                            <option value="allowance">💰 Tunjangan & Bonus</option>
                        </optgroup>
                        <optgroup label="Vendor & Subkontraktor">
                            <option value="subcontractor">🏗️ Subkontraktor</option>
                            <option value="consultant">👨‍💼 Konsultan Eksternal</option>
                            <option value="supplier">📦 Supplier/Vendor</option>
                        </optgroup>
                        <optgroup label="Layanan Teknis">
                            <option value="laboratory">🔬 Laboratorium</option>
                            <option value="survey">📐 Survey & Pengukuran</option>
                            <option value="testing">🧪 Testing & Inspeksi</option>
                            <option value="certification">📋 Sertifikasi</option>
                        </optgroup>
                        <optgroup label="Peralatan & Material">
                            <option value="equipment_rental">🚜 Sewa Alat</option>
                            <option value="equipment_purchase">🛠️ Pembelian Alat</option>
                            <option value="materials">📦 Bahan & Material</option>
                            <option value="maintenance">🔧 Maintenance & Perbaikan</option>
                        </optgroup>
                        <optgroup label="Operasional">
                            <option value="travel">✈️ Perjalanan Dinas</option>
                            <option value="accommodation">🏨 Akomodasi</option>
                            <option value="transportation">🚗 Transportasi</option>
                            <option value="communication">📞 Komunikasi & Internet</option>
                            <option value="office_supplies">📝 ATK & Supplies</option>
                            <option value="printing">🖨️ Printing & Dokumen</option>
                        </optgroup>
                        <optgroup label="Legal & Administrasi">
                            <option value="permit">📜 Perizinan</option>
                            <option value="insurance">🛡️ Asuransi</option>
                            <option value="tax">💵 Pajak & Retribusi</option>
                            <option value="legal">⚖️ Legal & Notaris</option>
                            <option value="administration">📋 Administrasi</option>
                        </optgroup>
                        <optgroup label="Marketing & Lainnya">
                            <option value="marketing">📢 Marketing & Promosi</option>
                            <option value="entertainment">🍽️ Entertainment & Jamuan</option>
                            <option value="donation">🎁 Donasi & CSR</option>
                            <option value="other">📌 Lainnya</option>
                        </optgroup>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-2" style="color: rgba(235, 235, 245, 0.8);">Nama Vendor</label>
                    <input type="text" name="vendor_name" maxlength="255"
                           class="input-dark w-full px-4 py-2.5 rounded-lg" placeholder="Nama vendor/penerima">
                </div>

                <div>
                    <label class="block text-sm font-medium mb-2" style="color: rgba(235, 235, 245, 0.8);">Metode Pembayaran *</label>
                    <select name="payment_method" required class="input-dark w-full px-4 py-2.5 rounded-lg">
                        <option value="">Pilih metode...</option>
                        <option value="bank_transfer">Transfer Bank</option>
                        <option value="cash">Tunai</option>
                        <option value="check">Cek</option>
                        <option value="giro">Giro</option>
                        <option value="other">Lainnya</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-2" style="color: rgba(235, 235, 245, 0.8);">Akun Bank/Kas</label>
                    <select name="bank_account_id" class="input-dark w-full px-4 py-2.5 rounded-lg">
                        <option value="">Pilih akun...</option>
                        @foreach(\App\Models\CashAccount::active()->get() as $account)
                        <option value="{{ $account->id }}">{{ $account->account_name }} - {{ $account->formatted_balance }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium mb-2" style="color: rgba(235, 235, 245, 0.8);">Deskripsi/Catatan</label>
                    <textarea name="description" rows="2"
                              class="input-dark w-full px-4 py-2.5 rounded-lg" placeholder="Detail pengeluaran..."></textarea>
                </div>

                <div class="md:col-span-2">
                    <label class="flex items-center space-x-2">
                        <input type="checkbox" name="is_receivable" value="1" class="rounded" style="background: rgba(58, 58, 60, 0.8);" onchange="toggleReceivableFields()">
                        <span class="text-sm" style="color: rgba(235, 235, 245, 0.8);">Kasbon/Piutang Internal (perlu dikembalikan oleh karyawan/pihak internal)</span>
                    </label>
                    <p class="text-xs mt-1" style="color: rgba(235, 235, 245, 0.5);">
                        Centang jika ini adalah kasbon atau uang yang dipinjamkan ke karyawan/tim internal yang harus dikembalikan
                    </p>
                </div>

                <div id="receivableFields" class="md:col-span-2 hidden">
                    <div class="p-4 rounded-lg" style="background: rgba(255, 204, 0, 0.1); border: 1px solid rgba(255, 204, 0, 0.3);">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium mb-2" style="color: rgba(235, 235, 245, 0.8);">Nama Penerima Kasbon *</label>
                                <input type="text" name="receivable_from" maxlength="255"
                                       class="input-dark w-full px-4 py-2.5 rounded-lg" placeholder="Nama karyawan/pihak internal">
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-2" style="color: rgba(235, 235, 245, 0.8);">Status</label>
                                <select name="receivable_status" class="input-dark w-full px-4 py-2.5 rounded-lg">
                                    <option value="pending">Belum Bayar</option>
                                    <option value="partial">Sebagian</option>
                                    <option value="paid">Lunas</option>
                                </select>
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium mb-2" style="color: rgba(235, 235, 245, 0.8);">Catatan Kasbon</label>
                                <textarea name="receivable_notes" rows="2"
                                          class="input-dark w-full px-4 py-2.5 rounded-lg" placeholder="Catatan tambahan untuk kasbon..."></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium mb-2" style="color: rgba(235, 235, 245, 0.8);">Upload Bukti (PDF/Gambar, max 5MB)</label>
                    <input type="file" name="receipt_file" accept=".pdf,.jpg,.jpeg,.png"
                           class="input-dark w-full px-4 py-2.5 rounded-lg">
                </div>
            </div>

            <div class="flex justify-end space-x-3 mt-6">
                <button type="button" onclick="closeExpenseModal()"
                        class="px-6 py-2.5 rounded-lg font-medium transition-colors hover:opacity-80" 
                        style="background: rgba(58, 58, 60, 0.8); color: rgba(235, 235, 245, 0.8);">
                    Batal
                </button>
                <button type="submit" class="px-6 py-2.5 rounded-lg font-medium transition-colors hover:opacity-90" 
                        style="background: rgba(255, 149, 0, 0.9); color: #FFFFFF;">
                    <i class="fas fa-save mr-2"></i>Simpan Pengeluaran
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function switchTab(tabName) {
    // Update URL hash (without triggering page scroll)
    if (window.location.hash !== '#' + tabName) {
        history.replaceState(null, null, '#' + tabName);
    }
    
    // Hide all tabs
    document.querySelectorAll('.tab-content').forEach(content => {
        content.classList.add('hidden');
    });
    
    // Remove active from all buttons
    document.querySelectorAll('.tab-button').forEach(btn => {
        btn.classList.remove('active');
        btn.style.background = 'transparent';
        btn.style.color = 'rgba(235, 235, 245, 0.6)';
    });
    
    // Show selected tab
    const tabContent = document.getElementById('content-' + tabName);
    if (tabContent) {
        tabContent.classList.remove('hidden');
    }
    
    // Activate button
    const activeBtn = document.getElementById('tab-' + tabName);
    if (activeBtn) {
        activeBtn.classList.add('active');
        activeBtn.style.background = 'rgba(58, 58, 60, 0.8)';
        activeBtn.style.color = '#FFFFFF';
    }
    
    // Initialize sortable when switching to permits or tasks tab
    if (tabName === 'permits' && typeof window.initializePermitsSortable === 'function') {
        setTimeout(() => window.initializePermitsSortable(), 100);
    } else if (tabName === 'tasks' && typeof window.initializeTasksSortable === 'function') {
        setTimeout(() => window.initializeTasksSortable(), 100);
    }
}

// Check URL parameter on page load to switch to correct tab
document.addEventListener('DOMContentLoaded', function() {
    // Check for URL hash first (for staying in tab after reload)
    const hash = window.location.hash.substring(1); // Remove the # symbol
    if (hash) {
        const validTabs = ['overview', 'financial', 'permits', 'tasks', 'documents'];
        if (validTabs.includes(hash)) {
            switchTab(hash);
            return;
        }
    }
    
    // Then check for URL query parameter
    const urlParams = new URLSearchParams(window.location.search);
    const tab = urlParams.get('tab');
    
    if (tab) {
        // Valid tab names
        const validTabs = ['overview', 'financial', 'permits', 'tasks', 'documents'];
        
        if (validTabs.includes(tab)) {
            switchTab(tab);
        }
    }
});

// ===== GLOBAL PERMITS TAB FUNCTIONS =====
// These functions need to be globally accessible for permits tab
function showTemplateModal() {
    const modal = document.getElementById('template-modal');
    if (!modal) {
        console.error('Template modal not found!');
        return;
    }
    modal.classList.remove('hidden');
    modal.style.display = 'flex';
    modal.style.alignItems = 'center';
    modal.style.justifyContent = 'center';
}

function closeTemplateModal() {
    const modal = document.getElementById('template-modal');
    if (!modal) return;
    modal.classList.add('hidden');
    modal.style.display = 'none';
}

function showAddPermitModal() {
    const modal = document.getElementById('add-permit-modal');
    if (!modal) {
        console.error('Add Permit modal not found!');
        return;
    }
    modal.classList.remove('hidden');
    modal.style.display = 'flex';
    modal.style.alignItems = 'center';
    modal.style.justifyContent = 'center';
}

function closeAddPermitModal() {
    const modal = document.getElementById('add-permit-modal');
    if (!modal) return;
    modal.classList.add('hidden');
    modal.style.display = 'none';
}

function selectTemplate(templateId) {
    document.querySelectorAll('[id^="preview-"]').forEach(el => {
        el.classList.add('hidden');
    });
    
    const preview = document.getElementById('preview-' + templateId);
    if (preview) {
        preview.classList.remove('hidden');
    }
}

function showUploadModal(permitId) {
    const permitCard = document.querySelector(`[data-permit-id="${permitId}"]`);
    if (!permitCard) {
        alert('Permit data not found');
        return;
    }
    
    window.currentUploadPermitId = permitId;
    
    document.getElementById('upload-permit-sequence').textContent = permitCard.dataset.sequence;
    document.getElementById('upload-permit-name').textContent = permitCard.dataset.permitName;
    
    const modal = document.getElementById('upload-document-modal');
    if (!modal) {
        console.error('Upload modal not found!');
        return;
    }
    modal.classList.remove('hidden');
    modal.style.display = 'flex';
}

function closeUploadModal() {
    const modal = document.getElementById('upload-document-modal');
    if (!modal) return;
    modal.classList.add('hidden');
    modal.style.display = 'none';
    document.getElementById('upload-document-form').reset();
    window.currentUploadPermitId = null;
}

function showManageDependenciesModal(permitId) {
    // This function will be called from permits tab
    if (typeof window.showManageDependenciesModalImpl === 'function') {
        window.showManageDependenciesModalImpl(permitId);
    }
}

function updatePermitStatus(permitId) {
    // This function will be called from permits tab
    if (typeof window.updatePermitStatusImpl === 'function') {
        window.updatePermitStatusImpl(permitId);
    }
}

function deletePermit(permitId) {
    if (!confirm('Yakin ingin menghapus izin ini?\n\nPerhatian: Jika izin ini menjadi prasyarat izin lain, penghapusan akan gagal.')) {
        return;
    }
    
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = `/permits/${permitId}`;
    
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
    
    form.innerHTML = `
        <input type="hidden" name="_token" value="${csrfToken}">
        <input type="hidden" name="_method" value="DELETE">
        <input type="hidden" name="redirect_to_tab" value="permits">
    `;
    
    document.body.appendChild(form);
    form.submit();
}

// Payment Modal: Auto-fill amount when invoice selected
function updatePaymentAmount() {
    const invoiceSelect = document.getElementById('payment_invoice_id');
    const amountInput = document.getElementById('payment_amount');
    
    if (invoiceSelect && amountInput) {
        const selectedOption = invoiceSelect.options[invoiceSelect.selectedIndex];
        const remaining = selectedOption.getAttribute('data-remaining');
        
        if (remaining && parseFloat(remaining) > 0) {
            amountInput.value = remaining;
        } else {
            amountInput.value = '';
        }
    }
}
</script>

<style>
.tab-button:hover {
    background: rgba(58, 58, 60, 0.6) !important;
    color: rgba(235, 235, 245, 0.9) !important;
}
.tab-button.active {
    background: rgba(58, 58, 60, 0.8) !important;
    color: #FFFFFF !important;
}

/* Protective scoping for tab content partials */
[data-scope="financial-tab"],
[data-scope="permits-tab"],
[data-scope="tasks-tab"],
[data-scope="documents-tab"] {
    min-width: 0;
    overflow: hidden; /* Prevent content from breaking out */
}

/* Ensure images and tables don't overflow */
[data-scope] img,
[data-scope] table {
    max-width: 100%;
}

/* Add horizontal scroll for wide content */
[data-scope] .overflow-x-auto {
    overflow-x: auto;
}

/* Custom scrollbar for activity feed */
.custom-scrollbar::-webkit-scrollbar {
    width: 6px;
}

.custom-scrollbar::-webkit-scrollbar-track {
    background: rgba(58, 58, 60, 0.3);
    border-radius: 3px;
}

.custom-scrollbar::-webkit-scrollbar-thumb {
    background: rgba(0, 122, 255, 0.5);
    border-radius: 3px;
}

.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: rgba(0, 122, 255, 0.7);
}
</style>

{{-- Include Financial Modals (Sprint 6) --}}
@include('projects.partials.financial-modals')

@endsection