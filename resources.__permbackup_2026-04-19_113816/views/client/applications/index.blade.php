@extends('client.layouts.app')

@section('title', 'Permohonan Izin Saya')

@section('content')
@php
    $statusMeta = [
        'draft' => ['label' => 'Draft', 'desc' => 'Masih bisa diubah', 'color' => 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300'],
        'submitted' => ['label' => 'Diajukan', 'desc' => 'Menunggu verifikasi', 'color' => 'bg-[#0a66c2]/10 text-[#0a66c2] dark:bg-[#0a66c2]/20 dark:text-[#0a66c2]'],
        'under_review' => ['label' => 'Dalam Review', 'desc' => 'Sedang diperiksa', 'color' => 'bg-[#0a66c2]/10 text-[#0a66c2] dark:bg-[#0a66c2]/20 dark:text-[#0a66c2]'],
        'document_incomplete' => ['label' => 'Dokumen Kurang', 'desc' => 'Butuh dokumen', 'color' => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400'],
        'quoted' => ['label' => 'Menunggu Persetujuan', 'desc' => 'Review penawaran', 'color' => 'bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400'],
        'quotation_accepted' => ['label' => 'Quotation Diterima', 'desc' => 'Lanjut pembayaran', 'color' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400'],
        'payment_pending' => ['label' => 'Menunggu Pembayaran', 'desc' => 'Upload bukti bayar', 'color' => 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400'],
        'payment_verified' => ['label' => 'Pembayaran Terverifikasi', 'desc' => 'Menunggu progres', 'color' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400'],
        'in_progress' => ['label' => 'Sedang Diproses', 'desc' => 'Ditangani konsultan', 'color' => 'bg-[#0a66c2]/10 text-[#0a66c2] dark:bg-[#0a66c2]/20 dark:text-[#0a66c2]'],
        'completed' => ['label' => 'Selesai', 'desc' => 'Izin terbit', 'color' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400'],
        'cancelled' => ['label' => 'Dibatalkan', 'desc' => 'Tidak dilanjutkan', 'color' => 'bg-gray-200 text-gray-700 dark:bg-gray-700 dark:text-gray-300'],
    ];
    
    $totalApplications = $applications->total();
    $actionNeeded = ($statusCounts['document_incomplete'] ?? 0) + ($statusCounts['payment_pending'] ?? 0);
    $waitingResponse = ($statusCounts['quoted'] ?? 0);
    $completed = ($statusCounts['completed'] ?? 0);
    $activeProcessing = ($statusCounts['submitted'] ?? 0) + ($statusCounts['under_review'] ?? 0) + ($statusCounts['in_progress'] ?? 0) + ($statusCounts['payment_verified'] ?? 0);
@endphp

<!-- Mobile Hero - LinkedIn Style -->
<div class="lg:hidden bg-[#0a66c2] border-b border-gray-200 dark:border-gray-700 text-white p-6">
    <div class="space-y-4">
        <div>
            <p class="text-xs text-white/70 uppercase tracking-widest leading-tight">Manajemen Permohonan</p>
            <h1 class="text-xl font-bold mt-1 leading-tight">{{ $totalApplications }} Permohonan Izin</h1>
        </div>
        
        <div class="grid grid-cols-2 gap-3">
            <div class="bg-white/10 backdrop-blur px-4 py-3">
                <p class="text-xs text-white/70 leading-tight">Butuh Tindakan</p>
                <p class="text-2xl font-bold leading-tight mt-1">{{ $actionNeeded }}</p>
            </div>
            <div class="bg-white/10 backdrop-blur px-4 py-3">
                <p class="text-xs text-white/70 leading-tight">Aktif Diproses</p>
                <p class="text-2xl font-bold leading-tight mt-1">{{ $activeProcessing }}</p>
            </div>
        </div>
        
        <div class="flex gap-3">
            <a href="{{ route('client.services.index') }}" class="flex-1 px-4 py-3 bg-white text-[#0a66c2] font-semibold text-base text-center min-h-[44px] flex items-center justify-center active:scale-95 transition-transform">
                <i class="fas fa-plus mr-2"></i> Ajukan
            </a>
            <a href="{{ route('client.documents.index') }}" class="flex-1 px-4 py-3 bg-white/10 backdrop-blur border border-white/30 font-semibold text-base text-center min-h-[44px] flex items-center justify-center active:scale-95 transition-transform">
                <i class="fas fa-paperclip mr-2"></i> Dokumen
            </a>
        </div>
    </div>
</div>

<!-- Desktop Hero - LinkedIn Style -->
<div class="hidden lg:block bg-[#0a66c2] border-b border-gray-200 dark:border-gray-700 text-white">
    <div class="max-w-6xl mx-auto px-6 lg:px-8 py-8">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl lg:text-3xl font-bold leading-tight mb-2">
                    Manajemen {{ $totalApplications }} Permohonan Izin
                </h1>
                <p class="text-base text-white/90 leading-normal">
                    Pantau progres, lengkapi dokumen, dan kelola pembayaran dalam satu dashboard terpusat
                </p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('client.services.index') }}" class="inline-flex items-center gap-2 bg-white text-[#0a66c2] font-semibold px-5 py-3 hover:shadow-lg active:scale-95 transition-all">
                    <i class="fas fa-plus"></i> Ajukan Izin Baru
                </a>
                <a href="{{ route('client.documents.index') }}" class="inline-flex items-center gap-2 bg-white/10 backdrop-blur border border-white/30 px-5 py-3 hover:bg-white/20 active:scale-95 transition-all">
                    <i class="fas fa-paperclip"></i> Kelola Dokumen
                </a>
            </div>
        </div>
        
        <div class="grid grid-cols-4 gap-4">
            <div class="bg-white/10 backdrop-blur px-5 py-4">
                <p class="text-xs uppercase tracking-wider text-white/70 leading-tight">Butuh Tindakan</p>
                <p class="text-3xl font-bold leading-tight mt-2">{{ $actionNeeded }}</p>
                <p class="text-xs text-white/60 mt-1">Dokumen & Pembayaran</p>
            </div>
            <div class="bg-white/10 backdrop-blur px-5 py-4">
                <p class="text-xs uppercase tracking-wider text-white/70 leading-tight">Aktif Diproses</p>
                <p class="text-3xl font-bold leading-tight mt-2">{{ $activeProcessing }}</p>
                <p class="text-xs text-white/60 mt-1">Sedang Ditangani</p>
            </div>
            <div class="bg-white/10 backdrop-blur px-5 py-4">
                <p class="text-xs uppercase tracking-wider text-white/70 leading-tight">Menunggu Respon</p>
                <p class="text-3xl font-bold leading-tight mt-2">{{ $waitingResponse }}</p>
                <p class="text-xs text-white/60 mt-1">Review Penawaran</p>
            </div>
            <div class="bg-white/10 backdrop-blur px-5 py-4">
                <p class="text-xs uppercase tracking-wider text-white/70 leading-tight">Selesai</p>
                <p class="text-3xl font-bold leading-tight mt-2">{{ $completed }}</p>
                <p class="text-xs text-white/60 mt-1">Izin Terbit</p>
            </div>
        </div>
    </div>
</div>

<!-- Applications List - LinkedIn Style Full Width -->
@if($applications->count() > 0)
    <div class="space-y-1 mt-1 lg:mt-1">
        @foreach($applications as $application)
            @php
                $formData = is_string($application->form_data) ? json_decode($application->form_data, true) : $application->form_data;
                $isPackage = isset($formData['package_type']) && $formData['package_type'] === 'multi_permit';
                $meta = $statusMeta[$application->status] ?? ['label' => ucfirst(str_replace('_', ' ', $application->status)), 'desc' => '', 'color' => 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300'];
                
                // Smart permit name detection
                $permitName = 'Permohonan Izin';
                if ($application->permitType) {
                    $permitName = $application->permitType->name;
                } elseif ($isPackage) {
                    $totalPermits = ($formData['permits_by_service']['bizmark'] ?? 0) + ($formData['permits_by_service']['owned'] ?? 0);
                    $permitName = ($formData['project_name'] ?? 'Paket Izin') . ' (' . $totalPermits . ' Perizinan)';
                } elseif (!empty($formData['permit_name'])) {
                    $permitName = $formData['permit_name'];
                } elseif (!empty($formData['permit_type'])) {
                    $permitName = $formData['permit_type'];
                } elseif ($application->kbli_description) {
                    $permitName = 'Perizinan ' . $application->kbli_description;
                }
                
                // Extract additional details
                $location = null;
                if (!empty($formData['city']) && !empty($formData['province'])) {
                    $location = $formData['city'] . ', ' . $formData['province'];
                } elseif (!empty($formData['province'])) {
                    $location = $formData['province'];
                }
                
                $businessScale = !empty($formData['business_scale']) ? ucfirst($formData['business_scale']) : null;
                $landArea = !empty($formData['land_area']) ? number_format($formData['land_area'], 0, ',', '.') . ' m²' : null;
                
                $daysAgo = (int) $application->created_at->diffInDays(now());
            @endphp
            <a href="{{ route('client.applications.show', $application->id) }}" class="block bg-white dark:bg-gray-800 border-y border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                <div class="px-4 lg:px-6 py-5">
                    <div class="flex items-start gap-4">
                        <div class="flex-1 min-w-0 space-y-3">
                            <!-- Status & Number -->
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="px-2.5 py-1 text-xs font-semibold rounded-full {{ $meta['color'] }}">
                                    {{ $meta['label'] }}
                                </span>
                                <span class="text-xs font-medium text-gray-500 dark:text-gray-400">{{ $application->application_number }}</span>
                                @if($daysAgo === 0)
                                <span class="text-xs text-gray-500 dark:text-gray-400">• Hari ini</span>
                                @elseif($daysAgo === 1)
                                <span class="text-xs text-gray-500 dark:text-gray-400">• Kemarin</span>
                                @else
                                <span class="text-xs text-gray-500 dark:text-gray-400">• {{ $daysAgo }} hari lalu</span>
                                @endif
                            </div>
                            
                            <!-- Permit Name & KBLI -->
                            <div>
                                <p class="text-base font-semibold text-gray-900 dark:text-white leading-tight mb-1">
                                    {{ $permitName }}
                                </p>
                                @if($application->kbli_code && $application->kbli_description)
                                <p class="text-sm text-gray-600 dark:text-gray-400 leading-normal">
                                    <span class="font-medium">KBLI {{ $application->kbli_code }}</span> - {{ Str::limit($application->kbli_description, 80) }}
                                </p>
                                @endif
                            </div>
                            
                            <!-- Business Details -->
                            @if($location || $businessScale || $landArea)
                            <div class="flex flex-wrap gap-x-4 gap-y-1 text-sm">
                                @if($location)
                                <span class="inline-flex items-center gap-1.5 text-gray-600 dark:text-gray-400">
                                    <i class="fas fa-map-marker-alt text-xs"></i>{{ $location }}
                                </span>
                                @endif
                                @if($businessScale)
                                <span class="inline-flex items-center gap-1.5 text-gray-600 dark:text-gray-400">
                                    <i class="fas fa-building text-xs"></i>{{ $businessScale }}
                                </span>
                                @endif
                                @if($landArea)
                                <span class="inline-flex items-center gap-1.5 text-gray-600 dark:text-gray-400">
                                    <i class="fas fa-ruler-combined text-xs"></i>{{ $landArea }}
                                </span>
                                @endif
                            </div>
                            @endif
                            
                            <!-- Package Info -->
                            @if($isPackage)
                                <div class="p-3 bg-purple-50 dark:bg-purple-900/20 border border-purple-200 dark:border-purple-700 text-xs flex flex-wrap items-center gap-2">
                                    <span class="font-semibold text-sm text-purple-800 dark:text-purple-200 flex items-center gap-2">
                                        <i class="fas fa-box"></i>Paket Multi Perizinan
                                    </span>
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400">
                                        <i class="fas fa-handshake"></i>{{ $formData['permits_by_service']['bizmark'] ?? 0 }} oleh Bizmark
                                    </span>
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400">
                                        <i class="fas fa-check"></i>{{ $formData['permits_by_service']['owned'] ?? 0 }} sudah dimiliki
                                    </span>
                                </div>
                            @endif
                            
                            <!-- Action Indicator -->
                            @if(!empty($meta['desc']))
                            <div class="flex items-center gap-2 text-sm">
                                <i class="fas fa-info-circle text-[#0a66c2]"></i>
                                <span class="text-[#0a66c2] dark:text-[#0a66c2] font-medium">{{ $meta['desc'] }}</span>
                            </div>
                            @endif
                            
                            <!-- Metadata Footer -->
                            <div class="flex flex-wrap gap-x-4 gap-y-1 text-sm text-gray-500 dark:text-gray-400 pt-2 border-t border-gray-100 dark:border-gray-700">
                                @if($application->submitted_at)
                                <span class="inline-flex items-center gap-1.5">
                                    <i class="fas fa-paper-plane text-xs"></i>Diajukan {{ $application->submitted_at->format('d M Y') }}
                                </span>
                                @endif
                                <span class="inline-flex items-center gap-1.5">
                                    <i class="fas fa-paperclip text-xs"></i>{{ $application->documents->count() }} Dokumen
                                </span>
                                @if($application->quoted_price)
                                <span class="inline-flex items-center gap-1.5 text-gray-900 dark:text-white font-semibold">
                                    <i class="fas fa-money-bill text-xs"></i>Rp {{ number_format($application->quoted_price, 0, ',', '.') }}
                                </span>
                                @endif
                            </div>
                        </div>
                        <i class="fas fa-chevron-right text-gray-400 text-sm hidden sm:block mt-1"></i>
                    </div>
                </div>
            </a>
        @endforeach
    </div>

    <!-- Pagination - LinkedIn Style -->
    <div class="bg-white dark:bg-gray-800 border-y border-gray-200 dark:border-gray-700 px-4 lg:px-6 py-4">
        {{ $applications->links() }}
    </div>
@else
    <!-- Empty State - LinkedIn Style -->
    <div class="bg-white dark:bg-gray-800 border-y border-gray-200 dark:border-gray-700 py-16 text-center">
        <i class="fas fa-inbox text-gray-300 dark:text-gray-600 text-6xl mb-4"></i>
        <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2 leading-tight">
            @if(request('status') || request('search'))
                Tidak Ada Permohonan Ditemukan
            @else
                Belum Ada Permohonan
            @endif
        </h3>
        <p class="text-base text-gray-600 dark:text-gray-400 mb-6 leading-normal">
            @if(request('status') || request('search'))
                Coba ubah filter atau kata kunci pencarian Anda
            @else
                Mulai ajukan permohonan izin pertama Anda sekarang
            @endif
        </p>
        @if(request('status') || request('search'))
        <a href="{{ route('client.applications.index') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-200 font-semibold hover:bg-gray-200 dark:hover:bg-gray-700 active:scale-95 transition-all">
            <i class="fas fa-redo"></i> Reset Filter
        </a>
        @else
        <a href="{{ route('client.services.index') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-[#0a66c2] hover:bg-[#004182] text-white font-semibold active:scale-95 transition-all">
            <i class="fas fa-plus"></i> Ajukan Permohonan Baru
        </a>
        @endif
    </div>
@endif
@endsection
