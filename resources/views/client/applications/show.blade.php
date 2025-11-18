@extends('client.layouts.app')

@section('title', 'Detail Permohonan - ' . $application->application_number)

@section('content')
@php
    $formData = is_string($application->form_data) 
        ? json_decode($application->form_data, true) 
        : $application->form_data;
    $isPackage = isset($formData['package_type']) && $formData['package_type'] === 'multi_permit';
    
    // Status colors
    $statusColors = [
        'draft' => ['bg' => 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300', 'icon' => 'fa-file-alt', 'color' => 'text-gray-600'],
        'submitted' => ['bg' => 'bg-[#0a66c2]/10 text-[#0a66c2]', 'icon' => 'fa-paper-plane', 'color' => 'text-[#0a66c2]'],
        'under_review' => ['bg' => 'bg-[#0a66c2]/10 text-[#0a66c2]', 'icon' => 'fa-search', 'color' => 'text-[#0a66c2]'],
        'document_incomplete' => ['bg' => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400', 'icon' => 'fa-exclamation-triangle', 'color' => 'text-yellow-600'],
        'quoted' => ['bg' => 'bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400', 'icon' => 'fa-file-invoice-dollar', 'color' => 'text-purple-600'],
        'quotation_accepted' => ['bg' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400', 'icon' => 'fa-check-circle', 'color' => 'text-emerald-600'],
        'payment_pending' => ['bg' => 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400', 'icon' => 'fa-credit-card', 'color' => 'text-amber-600'],
        'payment_verified' => ['bg' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400', 'icon' => 'fa-check-double', 'color' => 'text-emerald-600'],
        'in_progress' => ['bg' => 'bg-[#0a66c2]/10 text-[#0a66c2]', 'icon' => 'fa-spinner', 'color' => 'text-[#0a66c2]'],
        'completed' => ['bg' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400', 'icon' => 'fa-check-circle', 'color' => 'text-green-600'],
        'cancelled' => ['bg' => 'bg-gray-200 text-gray-700 dark:bg-gray-700 dark:text-gray-300', 'icon' => 'fa-times-circle', 'color' => 'text-gray-600'],
    ];
    $statusMeta = $statusColors[$application->status] ?? ['bg' => 'bg-gray-100 text-gray-700', 'icon' => 'fa-file-alt', 'color' => 'text-gray-600'];
    
    // Calculate stats
    $totalDocuments = $application->documents->count();
    $requiredDocs = $application->permitType && $application->permitType->required_documents 
        ? count($application->permitType->required_documents) 
        : 0;
    $documentProgress = $requiredDocs > 0 ? min(100, round(($totalDocuments / $requiredDocs) * 100)) : 0;
    $daysAgo = (int) $application->created_at->diffInDays(now());
@endphp

<!-- Mobile Hero - LinkedIn Style -->
<div class="lg:hidden bg-[#0a66c2] text-white border-b border-gray-200 dark:border-gray-700">
    <div class="p-6">
        <!-- Back Button -->
        <div class="mb-4">
            <a href="{{ route('client.applications.index') }}" 
               class="inline-flex items-center gap-2 text-white/90 hover:text-white text-sm active:scale-95 transition-all min-h-[44px] -ml-2 px-2">
                <i class="fas fa-arrow-left"></i>
                <span>Daftar Permohonan</span>
            </a>
        </div>

        <!-- Application Info -->
        <div class="mb-4">
            <p class="text-xs text-white/70 uppercase tracking-wider mb-1">Permohonan</p>
            <h1 class="text-xl font-bold mb-2">{{ $application->application_number }}</h1>
            <p class="text-sm text-white/90 mb-3">
                @if($isPackage)
                    <i class="fas fa-box mr-1"></i>{{ $formData['project_name'] ?? 'Paket Perizinan' }}
                @else
                    <i class="fas fa-file-alt mr-1"></i>{{ $application->permitType ? $application->permitType->name : ($formData['permit_name'] ?? 'Permohonan Izin') }}
                @endif
            </p>
            
            <!-- Status Badge -->
            <div class="inline-flex items-center gap-2 bg-white/20 backdrop-blur px-4 py-2 rounded-full">
                <i class="fas {{ $statusMeta['icon'] }} text-sm"></i>
                <span class="text-sm font-semibold">{{ $application->status_label }}</span>
            </div>
        </div>

        <!-- Compact Stats -->
        <div class="grid grid-cols-3 gap-2">
            <div class="bg-white/10 backdrop-blur px-3 py-2.5 text-center">
                <i class="fas fa-paperclip text-white/70 text-xs mb-1"></i>
                <p class="text-lg font-bold">{{ $totalDocuments }}</p>
                <p class="text-[10px] text-white/70">Dokumen</p>
            </div>
            <div class="bg-white/10 backdrop-blur px-3 py-2.5 text-center">
                <i class="fas fa-calendar text-white/70 text-xs mb-1"></i>
                <p class="text-lg font-bold">{{ $daysAgo }}</p>
                <p class="text-[10px] text-white/70">Hari lalu</p>
            </div>
            @if($application->quoted_price)
            <div class="bg-white/10 backdrop-blur px-3 py-2.5 text-center">
                <i class="fas fa-money-bill text-white/70 text-xs mb-1"></i>
                <p class="text-sm font-bold">{{ number_format($application->quoted_price / 1000000, 1) }}M</p>
                <p class="text-[10px] text-white/70">Investasi</p>
            </div>
            @else
            <div class="bg-white/10 backdrop-blur px-3 py-2.5 text-center">
                <i class="fas fa-hourglass-half text-white/70 text-xs mb-1"></i>
                <p class="text-lg font-bold">-</p>
                <p class="text-[10px] text-white/70">Pending</p>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Desktop Hero - LinkedIn Style -->
<div class="hidden lg:block bg-[#0a66c2] text-white border-b border-gray-200 dark:border-gray-700">
    <div class="px-6 lg:px-8 py-6">
        <!-- Back Button -->
        <div class="mb-4">
            <a href="{{ route('client.applications.index') }}" 
               class="inline-flex items-center gap-2 text-white/90 hover:text-white text-sm transition-colors">
                <i class="fas fa-arrow-left"></i>
                <span>Kembali ke Daftar Permohonan</span>
            </a>
        </div>

        <div class="flex items-start justify-between gap-6">
            <!-- Left: Info -->
            <div class="flex-1">
                <div class="flex items-center gap-3 mb-3">
                    <h1 class="text-2xl lg:text-3xl font-bold">{{ $application->application_number }}</h1>
                    <span class="inline-flex items-center gap-2 px-4 py-1.5 bg-white/20 backdrop-blur rounded-full text-sm font-semibold">
                        <i class="fas {{ $statusMeta['icon'] }}"></i>
                        {{ $application->status_label }}
                    </span>
                </div>
                
                <p class="text-base text-white/90 mb-2">
                    @if($isPackage)
                        <i class="fas fa-box mr-1"></i>{{ $formData['project_name'] ?? 'Paket Perizinan' }}
                    @else
                        <i class="fas fa-file-alt mr-1"></i>{{ $application->permitType ? $application->permitType->name : ($formData['permit_name'] ?? 'Permohonan Izin') }}
                    @endif
                </p>
                
                <div class="flex items-center gap-4 text-sm text-white/80">
                    <span><i class="fas fa-calendar mr-1"></i>Dibuat {{ $application->created_at->format('d M Y') }}</span>
                    @if($application->submitted_at)
                        <span><i class="fas fa-paper-plane mr-1"></i>Disubmit {{ $application->submitted_at->format('d M Y') }}</span>
                    @endif
                </div>
            </div>

            <!-- Right: Stats Grid -->
            <div class="grid grid-cols-3 gap-4">
                <div class="bg-white/10 backdrop-blur border border-white/20 px-5 py-4 min-w-[140px]">
                    <div class="flex items-center justify-between mb-2">
                        <p class="text-xs uppercase tracking-wider text-white/70">Dokumen</p>
                        <i class="fas fa-paperclip text-white/50"></i>
                    </div>
                    <p class="text-3xl font-bold">{{ $totalDocuments }}</p>
                    @if($requiredDocs > 0)
                        <p class="text-xs text-white/70 mt-1">dari {{ $requiredDocs }} wajib</p>
                    @endif
                </div>
                
                <div class="bg-white/10 backdrop-blur border border-white/20 px-5 py-4">
                    <div class="flex items-center justify-between mb-2">
                        <p class="text-xs uppercase tracking-wider text-white/70">Progress</p>
                        <i class="fas fa-tasks text-white/50"></i>
                    </div>
                    <p class="text-3xl font-bold">{{ $documentProgress }}%</p>
                    <p class="text-xs text-white/70 mt-1">Kelengkapan</p>
                </div>
                
                @if($application->quoted_price)
                <div class="bg-white/10 backdrop-blur border border-white/20 px-5 py-4">
                    <div class="flex items-center justify-between mb-2">
                        <p class="text-xs uppercase tracking-wider text-white/70">Nilai</p>
                        <i class="fas fa-money-bill text-white/50"></i>
                    </div>
                    <p class="text-2xl font-bold">{{ number_format($application->quoted_price / 1000000, 1) }}M</p>
                    <p class="text-xs text-white/70 mt-1">Investasi</p>
                </div>
                @else
                <div class="bg-white/10 backdrop-blur border border-white/20 px-5 py-4">
                    <div class="flex items-center justify-between mb-2">
                        <p class="text-xs uppercase tracking-wider text-white/70">Waktu</p>
                        <i class="fas fa-clock text-white/50"></i>
                    </div>
                    <p class="text-3xl font-bold">{{ $daysAgo }}</p>
                    <p class="text-xs text-white/70 mt-1">Hari lalu</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 lg:px-6 py-6">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-1">

            <!-- Application Info -->
    <div class="bg-white dark:bg-gray-800 border-y border-gray-200 dark:border-gray-700">
        <div class="px-4 lg:px-6 py-4 border-b border-gray-100 dark:border-gray-700">
            <h2 class="text-sm font-bold text-gray-900 dark:text-white flex items-center">
                <i class="fas fa-info-circle text-[#0a66c2] mr-2 text-base"></i>
                Informasi Permohonan
            </h2>
        </div>
        <div class="divide-y divide-gray-100 dark:divide-gray-700">
            <div class="px-4 lg:px-6 py-3 flex items-center justify-between hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                <span class="text-xs text-gray-500 dark:text-gray-400">Nomor Permohonan</span>
                <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ $application->application_number }}</span>
            </div>
            <div class="px-4 lg:px-6 py-3 flex items-center justify-between hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                <span class="text-xs text-gray-500 dark:text-gray-400">
                    @if($isPackage) Nama Proyek @else Jenis Izin @endif
                </span>
                <span class="text-sm font-semibold text-gray-900 dark:text-white text-right">
                    @if($isPackage)
                        {{ $formData['project_name'] ?? 'N/A' }}
                    @else
                        {{ $application->permitType ? $application->permitType->name : ($formData['permit_name'] ?? 'N/A') }}
                    @endif
                </span>
            </div>
            <div class="px-4 lg:px-6 py-3 flex items-center justify-between hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                <span class="text-xs text-gray-500 dark:text-gray-400">Tanggal Dibuat</span>
                <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ $application->created_at->format('d M Y, H:i') }}</span>
            </div>
            @if($application->submitted_at)
            <div class="px-4 lg:px-6 py-3 flex items-center justify-between hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                <span class="text-xs text-gray-500 dark:text-gray-400">Tanggal Diajukan</span>
                <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ $application->submitted_at->format('d M Y, H:i') }}</span>
            </div>
            @endif
            @if($application->quoted_price)
            <div class="px-4 lg:px-6 py-3 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                <div class="flex items-center justify-between">
                    <span class="text-xs text-gray-500 dark:text-gray-400">Harga Quotation</span>
                    <span class="text-lg font-bold text-[#0a66c2]">Rp {{ number_format($application->quoted_price, 0, ',', '.') }}</span>
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Package Information (if multi-permit) -->
    @if($isPackage)
    <div class="bg-white dark:bg-gray-800 border-y border-gray-200 dark:border-gray-700">
        <div class="px-4 lg:px-6 py-5">
            <h2 class="text-base font-bold text-gray-900 dark:text-white mb-4 flex items-center">
                <i class="fas fa-box-open text-[#0a66c2] mr-2"></i>
                Detail Paket Izin
            </h2>
            
            <!-- Summary Cards -->
            <div class="grid grid-cols-3 gap-2 mb-4">
                <div class="bg-[#0a66c2]/5 dark:bg-[#0a66c2]/10 border border-[#0a66c2]/20 rounded-lg p-3 text-center">
                    <i class="fas fa-handshake text-[#0a66c2] text-lg mb-1"></i>
                    <p class="text-xl font-bold text-gray-900 dark:text-white">{{ $formData['permits_by_service']['bizmark'] ?? 0 }}</p>
                    <p class="text-xs text-gray-600 dark:text-gray-400">BizMark.ID</p>
                </div>
                <div class="bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-700 rounded-lg p-3 text-center">
                    <i class="fas fa-check-circle text-emerald-600 dark:text-emerald-400 text-lg mb-1"></i>
                    <p class="text-xl font-bold text-gray-900 dark:text-white">{{ $formData['permits_by_service']['owned'] ?? 0 }}</p>
                    <p class="text-xs text-gray-600 dark:text-gray-400">Sudah Ada</p>
                </div>
                <div class="bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 rounded-lg p-3 text-center">
                    <i class="fas fa-user-check text-gray-600 dark:text-gray-400 text-lg mb-1"></i>
                    <p class="text-xl font-bold text-gray-900 dark:text-white">{{ $formData['permits_by_service']['self'] ?? 0 }}</p>
                    <p class="text-xs text-gray-600 dark:text-gray-400">Dikerjakan Sendiri</p>
                </div>
            </div>

                <!-- Project Details -->
                <div class="bg-white dark:bg-gray-800 rounded-lg p-4 mb-4">
                    <h3 class="font-semibold text-gray-800 dark:text-gray-200 text-sm mb-3">Informasi Proyek</h3>
                    <div class="grid grid-cols-2 gap-3 text-sm">
                        @if(isset($formData['project_location']))
                        <div class="col-span-2">
                            <p class="text-xs text-gray-600 dark:text-gray-400"><i class="fas fa-map-marker-alt mr-1"></i>Lokasi</p>
                            <p class="text-gray-900 dark:text-white">{{ $formData['project_location'] }}</p>
                        </div>
                        @endif
                        @if(isset($formData['land_area']))
                        <div>
                            <p class="text-xs text-gray-600 dark:text-gray-400"><i class="fas fa-ruler-combined mr-1"></i>Luas Tanah</p>
                            <p class="text-gray-900 dark:text-white">{{ number_format($formData['land_area'], 0, ',', '.') }} m²</p>
                        </div>
                        @endif
                        @if(isset($formData['building_area']))
                        <div>
                            <p class="text-xs text-gray-600 dark:text-gray-400"><i class="fas fa-building mr-1"></i>Luas Bangunan</p>
                            <p class="text-gray-900 dark:text-white">{{ number_format($formData['building_area'], 0, ',', '.') }} m²</p>
                        </div>
                        @endif
                        @if(isset($formData['investment_value']))
                        <div class="col-span-2">
                            <p class="text-xs text-gray-600 dark:text-gray-400"><i class="fas fa-money-bill-wave mr-1"></i>Nilai Investasi</p>
                            <p class="text-lg font-bold text-[#0a66c2] dark:text-blue-400">Rp {{ number_format($formData['investment_value'], 0, ',', '.') }}</p>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Permits List -->
                @if(isset($formData['selected_permits']) && count($formData['selected_permits']) > 0)
                <div>
                    <h3 class="font-semibold text-gray-800 dark:text-gray-200 text-sm mb-2">Daftar Izin ({{ count($formData['selected_permits']) }})</h3>
                    <div class="space-y-2">
                        @foreach($formData['selected_permits'] as $permit)
                        <div class="bg-white dark:bg-gray-800 border rounded-lg p-3 
                            @if($permit['service_type'] === 'bizmark') border-blue-200 dark:border-blue-800
                            @elseif($permit['service_type'] === 'owned') border-green-200 dark:border-green-800
                            @else border-purple-200 dark:border-purple-800
                            @endif">
                            <div class="flex items-center justify-between">
                                <span class="font-semibold text-gray-900 dark:text-white text-sm">{{ $permit['name'] }}</span>
                                <span class="text-xs px-2 py-1 rounded-full font-semibold
                                    @if($permit['service_type'] === 'bizmark') bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300
                                    @elseif($permit['service_type'] === 'owned') bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300
                                    @else bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-300
                                    @endif">
                                    @if($permit['service_type'] === 'bizmark') BizMark.ID
                                    @elseif($permit['service_type'] === 'owned') Sudah Ada
                                    @else Dikerjakan Sendiri
                                    @endif
                                </span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
        </div>
        @endif

        <!-- Company Information -->
        <div class="bg-white dark:bg-gray-800 border-y border-gray-200 dark:border-gray-700">
            <div class="px-4 lg:px-6 py-4 border-b border-gray-100 dark:border-gray-700">
                <h2 class="text-sm font-bold text-gray-900 dark:text-white flex items-center">
                    <i class="fas fa-building text-[#0a66c2] mr-2 text-base"></i>
                    Data Perusahaan
                </h2>
            </div>
            <div class="divide-y divide-gray-100 dark:divide-gray-700">
                <div class="px-4 lg:px-6 py-3">
                    <div class="flex items-start justify-between gap-4 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors py-2 -my-2 px-2 -mx-2 rounded">
                        <span class="text-xs text-gray-500 dark:text-gray-400">Nama Perusahaan</span>
                        <span class="text-sm font-semibold text-gray-900 dark:text-white text-right">{{ $formData['company_name'] ?? '-' }}</span>
                    </div>
                </div>
                <div class="px-4 lg:px-6 py-3">
                    <div class="flex items-start justify-between gap-4 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors py-2 -my-2 px-2 -mx-2 rounded">
                        <span class="text-xs text-gray-500 dark:text-gray-400">Alamat</span>
                        <span class="text-sm font-semibold text-gray-900 dark:text-white text-right max-w-xs">{{ $formData['company_address'] ?? '-' }}</span>
                    </div>
                </div>
                <div class="px-4 lg:px-6 py-3 grid grid-cols-2 gap-4">
                    <div class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors py-2 px-2 -ml-2 rounded">
                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-0.5">NPWP</p>
                        <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $formData['company_npwp'] ?? '-' }}</p>
                    </div>
                    <div class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors py-2 px-2 -mr-2 rounded">
                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-0.5">Telepon</p>
                        <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $formData['company_phone'] ?? '-' }}</p>
                    </div>
                </div>
            </div>
        </div>

            <!-- PIC Information -->
        <div class="bg-white dark:bg-gray-800 border-y border-gray-200 dark:border-gray-700">
            <div class="px-4 lg:px-6 py-4 border-b border-gray-100 dark:border-gray-700">
                <h2 class="text-sm font-bold text-gray-900 dark:text-white flex items-center">
                    <i class="fas fa-user text-[#0a66c2] mr-2 text-base"></i>
                    Penanggung Jawab
                </h2>
            </div>
            <div class="divide-y divide-gray-100 dark:divide-gray-700">
                <div class="px-4 lg:px-6 py-3 grid grid-cols-2 gap-4">
                    <div class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors py-2 px-2 -ml-2 rounded">
                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-0.5">Nama</p>
                        <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $formData['pic_name'] ?? '-' }}</p>
                    </div>
                    <div class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors py-2 px-2 -mr-2 rounded">
                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-0.5">Jabatan</p>
                        <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $formData['pic_position'] ?? '-' }}</p>
                    </div>
                </div>
                <div class="px-4 lg:px-6 py-3 grid grid-cols-2 gap-4">
                    <div class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors py-2 px-2 -ml-2 rounded">
                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-0.5">Email</p>
                        <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $formData['pic_email'] ?? '-' }}</p>
                    </div>
                    <div class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors py-2 px-2 -mr-2 rounded">
                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-0.5">No. HP</p>
                        <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $formData['pic_phone'] ?? '-' }}</p>
                    </div>
                </div>
            </div>
        </div>

        @if(!empty($formData['notes']))
        <!-- Notes -->
        <div class="bg-white dark:bg-gray-800 border-y border-gray-200 dark:border-gray-700">
            <div class="px-4 lg:px-6 py-4 border-b border-gray-100 dark:border-gray-700">
                <h2 class="text-sm font-bold text-gray-900 dark:text-white flex items-center">
                    <i class="fas fa-sticky-note text-[#0a66c2] mr-2 text-base"></i>
                    Catatan Tambahan
                </h2>
            </div>
            <div class="px-4 lg:px-6 py-4">
                <p class="text-sm text-gray-700 dark:text-gray-300 whitespace-pre-line">{{ $formData['notes'] }}</p>
            </div>
        </div>
        @endif

        <!-- Documents -->
        <div class="bg-white dark:bg-gray-800 border-y border-gray-200 dark:border-gray-700">
            <div class="px-4 lg:px-6 py-4 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                <h2 class="text-sm font-bold text-gray-900 dark:text-white flex items-center">
                    <i class="fas fa-paperclip text-[#0a66c2] mr-2 text-base"></i>
                    Dokumen Pendukung
                    <span class="ml-2 text-xs font-normal text-gray-600 dark:text-gray-400">
                        ({{ $application->documents->count() }} dokumen)
                    </span>
                </h2>
                @if($application->status === 'draft' || $application->status === 'document_incomplete')
                <button onclick="document.getElementById('uploadModal').classList.remove('hidden')" 
                        class="px-4 py-2 bg-[#0a66c2] hover:bg-[#004182] text-white rounded-lg transition text-sm font-semibold">
                    <i class="fas fa-upload mr-2"></i>Upload
                </button>
                @endif
            </div>
            <div class="px-4 lg:px-6 py-4">                @if($application->documents->count() > 0)
                    <div class="divide-y divide-gray-100 dark:divide-gray-700">
                        @foreach($application->documents as $document)
                        <div class="flex items-center justify-between py-3 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                            <div class="flex items-center gap-3 flex-1 min-w-0">
                                <div class="flex-shrink-0">
                                    @if(str_contains($document->mime_type, 'pdf'))
                                        <i class="fas fa-file-pdf text-red-500 text-xl"></i>
                                    @elseif(str_contains($document->mime_type, 'image'))
                                        <i class="fas fa-file-image text-blue-500 text-xl"></i>
                                    @else
                                        <i class="fas fa-file text-gray-500 text-xl"></i>
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2 mb-0.5">
                                        <p class="font-semibold text-gray-900 dark:text-white text-sm truncate">{{ $document->document_type }}</p>
                                        @if($document->status === 'approved')
                                            <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300">
                                                <i class="fas fa-check-circle"></i>
                                            </span>
                                        @elseif($document->status === 'rejected')
                                            <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-300">
                                                <i class="fas fa-times-circle"></i>
                                            </span>
                                        @else
                                            <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-700 dark:bg-yellow-900 dark:text-yellow-300">
                                                <i class="fas fa-clock"></i>
                                            </span>
                                        @endif
                                    </div>
                                    <p class="text-xs text-gray-600 dark:text-gray-400 truncate">{{ $document->file_name }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-500">
                                        {{ $document->file_size_formatted }} • {{ $document->created_at->format('d M Y') }}
                                    </p>
                                    @if($document->status === 'rejected' && $document->review_notes)
                                        <p class="text-xs text-red-600 dark:text-red-400 mt-1">
                                            <i class="fas fa-exclamation-circle mr-1"></i>{{ $document->review_notes }}
                                        </p>
                                    @endif
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                @if($document->status === 'rejected' || (!$document->is_verified && ($application->status === 'draft' || $application->status === 'document_incomplete')))
                                <form method="POST" 
                                      action="{{ route('client.applications.documents.delete', [$application->id, $document->id]) }}"
                                      onsubmit="return confirm('Yakin ingin menghapus dokumen ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="p-2 text-red-600 hover:bg-red-100 dark:hover:bg-red-900 rounded-lg transition"
                                            title="Hapus dokumen">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <i class="fas fa-file-upload text-4xl text-gray-300 dark:text-gray-600 mb-3"></i>
                        <p class="text-gray-600 dark:text-gray-400 mb-4">Belum ada dokumen yang diupload</p>
                        @if($application->status === 'draft' || $application->status === 'document_incomplete')
                        <button onclick="document.getElementById('uploadModal').classList.remove('hidden')" 
                                class="w-full px-6 py-3 bg-[#0a66c2] hover:bg-[#004182] text-white rounded-lg transition font-semibold">
                            <i class="fas fa-upload mr-2"></i>Upload Dokumen Pertama
                        </button>
                        @endif
                    </div>
                @endif
                </div>
            </div>

        <!-- Status History -->
        @if($application->statusLogs->count() > 0)
        <div class="bg-white dark:bg-gray-800 border-y border-gray-200 dark:border-gray-700">
            <div class="px-4 lg:px-6 py-4 border-b border-gray-100 dark:border-gray-700">
                <h2 class="text-sm font-bold text-gray-900 dark:text-white flex items-center">
                    <i class="fas fa-history text-[#0a66c2] mr-2 text-base"></i>
                    Riwayat Status
                </h2>
            </div>
                <div class="px-4 lg:px-6 py-4 divide-y divide-gray-100 dark:divide-gray-700">
                    @foreach($application->statusLogs()->latest()->get() as $log)
                    <div class="flex gap-3 py-3 first:pt-0 last:pb-0 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors px-2 -mx-2 rounded">
                        <div class="flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center"
                             style="background-color: {{ $application->status_color }}20">
                            <i class="fas fa-circle text-xs" style="color: {{ $application->status_color }}"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-gray-900 dark:text-white">
                                <span class="text-gray-600 dark:text-gray-400 font-normal">Status berubah dari</span> 
                                <span style="color: {{ $application->status_color }}">{{ ucfirst($log->from_status) }}</span>
                                <span class="text-gray-600 dark:text-gray-400 font-normal">ke</span> 
                                <span style="color: {{ $application->status_color }}">{{ ucfirst($log->to_status) }}</span>
                            </p>
                            @if($log->notes)
                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ $log->notes }}</p>
                            @endif
                            <p class="text-xs text-gray-500 dark:text-gray-500 mt-1">
                                {{ $log->created_at->format('d M Y H:i') }}
                            </p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

        <!-- Communication Section -->
        @if($application->status !== 'draft')
        <div class="bg-white dark:bg-gray-800 border-y border-gray-200 dark:border-gray-700">
            <div class="px-4 lg:px-6 py-4 border-b border-gray-100 dark:border-gray-700">
                <h2 class="text-sm font-bold text-gray-900 dark:text-white flex items-center">
                    <i class="fas fa-comments text-[#0a66c2] mr-2 text-base"></i>
                    Komunikasi dengan Admin
                </h2>
            </div>
            <div class="px-4 lg:px-6 py-4">
            @php
                $notes = $application->notes()
                    ->visibleToClient()
                    ->orderBy('created_at', 'asc')
                    ->get();
            @endphp

            @if($notes->count() > 0)
                <!-- Chat Container - WhatsApp Style -->
                <div id="chatContainer" class="mb-6 max-h-[500px] overflow-y-auto px-2 py-4 space-y-3 bg-gray-50 dark:bg-gray-900/30 rounded-lg scroll-smooth">
                    @foreach($notes as $note)
                        @if($note->author_type === 'admin')
                            <!-- Admin Message (Left side) -->
                            <div class="flex gap-2 items-end">
                                <div class="flex-shrink-0 mb-1">
                                    <div class="w-8 h-8 bg-gradient-to-br from-[#0a66c2] to-blue-600 rounded-full flex items-center justify-center shadow-sm">
                                        <i class="fas fa-user-shield text-white text-xs"></i>
                                    </div>
                                </div>
                                <div class="flex-1 max-w-[75%]">
                                    <div class="bg-white dark:bg-gray-800 rounded-2xl rounded-bl-sm shadow-sm border border-gray-200 dark:border-gray-700 px-4 py-2.5">
                                        <div class="flex items-center gap-2 mb-1">
                                            <span class="text-xs font-semibold text-[#0a66c2] dark:text-blue-400">
                                                {{ $note->author->name ?? 'Admin' }}
                                            </span>
                                            <span class="px-1.5 py-0.5 text-[10px] font-bold rounded bg-[#0a66c2]/10 text-[#0a66c2] dark:bg-blue-900/30 dark:text-blue-400">
                                                ADMIN
                                            </span>
                                        </div>
                                        <p class="text-sm text-gray-800 dark:text-gray-200 leading-relaxed whitespace-pre-wrap break-words">{{ $note->note }}</p>
                                        <div class="flex items-center justify-end gap-1 mt-2">
                                            <span class="text-[10px] text-gray-400 dark:text-gray-500">
                                                {{ $note->created_at->format('H:i') }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @else
                            <!-- Client Message (Right side) -->
                            <div class="flex gap-2 items-end justify-end">
                                <div class="flex-1 max-w-[75%] flex justify-end">
                                    <div class="bg-gradient-to-br from-[#DCF8C6] to-[#D1F2B8] dark:from-green-800 dark:to-green-900 rounded-2xl rounded-br-sm shadow-sm px-4 py-2.5">
                                        <div class="flex items-center gap-2 mb-1 justify-end">
                                            <span class="px-1.5 py-0.5 text-[10px] font-bold rounded bg-green-700/20 text-green-800 dark:bg-green-900/50 dark:text-green-300">
                                                ANDA
                                            </span>
                                            <span class="text-xs font-semibold text-green-800 dark:text-green-300">
                                                {{ $note->author->name ?? 'Anda' }}
                                            </span>
                                        </div>
                                        <p class="text-sm text-gray-900 dark:text-gray-100 leading-relaxed whitespace-pre-wrap break-words">{{ $note->note }}</p>
                                        <div class="flex items-center justify-end gap-1 mt-2">
                                            <span class="text-[10px] text-gray-600 dark:text-gray-400">
                                                {{ $note->created_at->format('H:i') }}
                                            </span>
                                            <i class="fas fa-check-double text-[10px] text-blue-600 dark:text-blue-400"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex-shrink-0 mb-1">
                                    <div class="w-8 h-8 bg-gradient-to-br from-green-500 to-green-600 rounded-full flex items-center justify-center shadow-sm">
                                        <i class="fas fa-user text-white text-xs"></i>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            @else
                <div class="text-center py-12 text-gray-500 dark:text-gray-400 mb-6 bg-gray-50 dark:bg-gray-900/30 rounded-lg">
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-gray-200 dark:bg-gray-800 rounded-full mb-4">
                        <i class="fas fa-comments text-2xl text-gray-400 dark:text-gray-600"></i>
                    </div>
                    <p class="text-sm font-medium">Belum ada pesan</p>
                    <p class="text-xs mt-1">Kirim pesan pertama Anda ke admin</p>
                </div>
            @endif

            <!-- Reply Form - WhatsApp Style -->
            <form action="{{ route('client.applications.notes.store', $application->id) }}" method="POST" class="border-t border-gray-100 dark:border-gray-700 pt-4">
                @csrf
                <div class="flex gap-2 items-end">
                    <div class="flex-1">
                        <textarea 
                            name="note" 
                            rows="1" 
                            required
                            placeholder="Ketik pesan..."
                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-3xl focus:ring-2 focus:ring-[#0a66c2] focus:border-[#0a66c2] dark:bg-gray-700 dark:text-white text-sm resize-none transition-all"
                            style="min-height: 44px; max-height: 120px;"
                            onInput="this.style.height = 'auto'; this.style.height = Math.min(this.scrollHeight, 120) + 'px'"
                        ></textarea>
                    </div>
                    <button 
                        type="submit"
                        class="flex-shrink-0 w-11 h-11 bg-gradient-to-br from-[#0a66c2] to-blue-600 hover:from-[#004182] hover:to-blue-700 text-white rounded-full transition active:scale-95 flex items-center justify-center shadow-lg hover:shadow-xl"
                        title="Kirim pesan"
                    >
                        <i class="fas fa-paper-plane text-sm"></i>
                    </button>
                </div>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-2 px-1">
                    <i class="fas fa-info-circle mr-1"></i>
                    Pesan akan dikirim langsung ke admin
                </p>
            </form>
            </div>
        </div>
        @endif
        </div>

        <!-- Sidebar -->
        <div class="lg:col-span-1">
            <!-- Quick Actions -->
            <div class="bg-white dark:bg-gray-800 border-y border-gray-200 dark:border-gray-700">
                <div class="px-4 lg:px-6 py-4 border-b border-gray-100 dark:border-gray-700">
                    <h3 class="text-sm font-bold text-gray-900 dark:text-white">Tindakan</h3>
                </div>
                <div class="px-4 lg:px-6 py-4 space-y-2">
                    {{-- Link to Project if converted --}}
                    @if($application->status === 'converted_to_project' && $application->project_id)
                    <a href="{{ route('client.projects.show', $application->project_id) }}" 
                       class="block w-full text-center px-4 py-3 bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white font-semibold rounded-lg transition shadow-sm active:scale-95">
                        <i class="fas fa-project-diagram mr-2"></i>Lihat Project
                    </a>
                    <p class="text-sm text-gray-600 dark:text-gray-400 text-center mt-2">
                        <i class="fas fa-check-circle text-green-600 mr-1"></i>
                        Permohonan telah dikonversi menjadi project
                    </p>
                    @endif
                    
                    @if($application->quotation && in_array($application->status, ['quoted', 'quotation_accepted', 'payment_pending', 'payment_verified']))
                    <a href="{{ route('client.quotations.show', $application->id) }}" 
                       class="block w-full text-center px-4 py-3 bg-purple-600 hover:bg-purple-700 text-white font-semibold rounded-lg transition active:scale-95">
                        <i class="fas fa-file-invoice mr-2"></i>Lihat Quotation
                    </a>
                    @endif

                    @if($application->canBeEdited())
                    <a href="{{ route('client.applications.edit', $application->id) }}" 
                       class="block w-full text-center px-4 py-3 bg-gray-600 hover:bg-gray-700 text-white font-semibold rounded-lg transition active:scale-95">
                        <i class="fas fa-edit mr-2"></i>Edit Permohonan
                    </a>
                    @endif

                    @if($application->status === 'draft' && $application->documents->count() > 0)
                    <a href="{{ route('client.applications.preview-submit', $application->id) }}" 
                       class="block w-full px-4 py-3 bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white font-semibold rounded-lg transition active:scale-95 text-center shadow-sm">
                        <i class="fas fa-file-contract mr-2"></i>Ajukan Permohonan
                    </a>
                    @endif

                    @if($application->canBeCancelled())
                    <form method="POST" action="{{ route('client.applications.cancel', $application->id) }}"
                          onsubmit="return confirm('Yakin ingin membatalkan permohonan ini?')">
                        @csrf
                        <button type="submit" 
                                class="w-full px-4 py-3 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-lg transition active:scale-95">
                            <i class="fas fa-times-circle mr-2"></i>Batalkan
                        </button>
                    </form>
                    @endif
                </div>
            </div>

            <!-- Required Documents Checklist -->
            <div class="bg-white dark:bg-gray-800 border-y border-gray-200 dark:border-gray-700 mt-1">
                <div class="px-4 lg:px-6 py-4 border-b border-gray-100 dark:border-gray-700">
                    <h3 class="text-sm font-bold text-gray-900 dark:text-white">Dokumen yang Diperlukan</h3>
                </div>
                <div class="px-4 lg:px-6 py-4 divide-y divide-gray-100 dark:divide-gray-700">
                    @if($application->permitType && $application->permitType->required_documents)
                        @foreach($application->permitType->required_documents as $index => $doc)
                            @php
                                $uploaded = $application->documents->where('document_type', $doc)->isNotEmpty();
                            @endphp
                            <div class="flex items-start gap-3 py-2 first:pt-0 last:pb-0">
                                <i class="fas fa-{{ $uploaded ? 'check-circle text-green-500' : 'circle text-gray-300' }} mt-0.5"></i>
                                <span class="text-sm {{ $uploaded ? 'text-gray-900 dark:text-white font-semibold' : 'text-gray-600 dark:text-gray-400' }}">
                                    {{ $doc }}
                                </span>
                            </div>
                        @endforeach
                    @else
                        <p class="text-sm text-gray-500 dark:text-gray-400">Tidak ada dokumen yang diperlukan untuk paket ini.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Upload Modal -->
<div id="uploadModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white dark:bg-gray-800 rounded-lg max-w-md w-full px-4 lg:px-6 py-5">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white">Upload Dokumen</h3>
            <button onclick="document.getElementById('uploadModal').classList.add('hidden')" 
                    class="text-gray-500 hover:text-gray-700 dark:hover:text-gray-300">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <form method="POST" 
              action="{{ route('client.applications.documents.upload', $application->id) }}" 
              enctype="multipart/form-data">
            @csrf
            
            <div class="space-y-4">
                <!-- Document Type -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Jenis Dokumen <span class="text-red-500">*</span>
                    </label>
                    <select name="document_type" 
                            required
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0a66c2] dark:bg-gray-700 dark:text-white">
                        <option value="">Pilih jenis dokumen...</option>
                        @if($application->permitType && $application->permitType->required_documents)
                            @foreach($application->permitType->required_documents as $doc)
                                <option value="{{ $doc }}">{{ $doc }}</option>
                            @endforeach
                        @endif
                        <option value="Lainnya">Lainnya</option>
                    </select>
                </div>

                <!-- File Upload -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        File <span class="text-red-500">*</span>
                    </label>
                    <input type="file" 
                           name="file" 
                           accept=".pdf,.jpg,.jpeg,.png"
                           required
                           class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0a66c2] dark:bg-gray-700 dark:text-white">
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                        Format: PDF, JPG, PNG. Maksimal 5MB
                    </p>
                </div>

                <!-- Notes -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Catatan (Opsional)
                    </label>
                    <textarea name="notes" 
                              rows="3"
                              placeholder="Tambahkan catatan jika diperlukan..."
                              class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#0a66c2] dark:bg-gray-700 dark:text-white"></textarea>
                </div>
            </div>

            <div class="flex gap-2 mt-6">
                <button type="button" 
                        onclick="document.getElementById('uploadModal').classList.add('hidden')"
                        class="w-full px-4 py-3 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 font-semibold rounded-lg transition active:scale-95">
                    Batal
                </button>
                <button type="submit" 
                        class="w-full px-4 py-3 bg-[#0a66c2] hover:bg-[#004182] text-white font-semibold rounded-lg transition active:scale-95">
                    <i class="fas fa-upload mr-2"></i>Upload
                </button>
            </div>
        </form>
    </div>
</div>
</div>
</div>

@endsection

@push('scripts')
<script>
    // Auto-scroll to latest message on page load
    document.addEventListener('DOMContentLoaded', function() {
        const chatContainer = document.getElementById('chatContainer');
        if (chatContainer) {
            chatContainer.scrollTop = chatContainer.scrollHeight;
        }
    });

    // Auto-expand textarea
    const messageInput = document.querySelector('textarea[name="note"]');
    if (messageInput) {
        messageInput.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = Math.min(this.scrollHeight, 120) + 'px';
        });
        
        // Focus on input when page loads
        messageInput.focus();
    }

    // Smooth scroll to bottom after sending message
    const chatForm = document.querySelector('form[action*="notes.store"]');
    if (chatForm) {
        chatForm.addEventListener('submit', function() {
            setTimeout(function() {
                const chatContainer = document.getElementById('chatContainer');
                if (chatContainer) {
                    chatContainer.scrollTo({
                        top: chatContainer.scrollHeight,
                        behavior: 'smooth'
                    });
                }
            }, 100);
        });
    }
</script>
@endpush
