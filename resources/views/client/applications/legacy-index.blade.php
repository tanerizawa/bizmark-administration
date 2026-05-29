@php
    // Status meta: only label & icon are used. Colors come from client.css status-badge--* classes.
    $statusMeta = [
        'draft'               => ['label' => 'Draft',                      'icon' => 'fa-file-alt'],
        'submitted'           => ['label' => 'Diajukan',                   'icon' => 'fa-paper-plane'],
        'under_review'        => ['label' => 'Dalam Review',               'icon' => 'fa-search'],
        'document_incomplete' => ['label' => 'Dokumen Kurang',             'icon' => 'fa-exclamation-triangle'],
        'quoted'              => ['label' => 'Menunggu Persetujuan',       'icon' => 'fa-file-invoice-dollar'],
        'quotation_accepted'  => ['label' => 'Quotation Diterima',        'icon' => 'fa-check-circle'],
        'payment_pending'     => ['label' => 'Menunggu Pembayaran',        'icon' => 'fa-credit-card'],
        'payment_verified'    => ['label' => 'Pembayaran Terverifikasi',   'icon' => 'fa-check-double'],
        'in_progress'         => ['label' => 'Sedang Diproses',            'icon' => 'fa-spinner'],
        'completed'           => ['label' => 'Selesai',                    'icon' => 'fa-check-circle'],
        'cancelled'           => ['label' => 'Dibatalkan',                 'icon' => 'fa-times-circle'],
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

<!-- Filter Bar + Search -->
<div class="bg-white dark:bg-gray-800 border-y border-gray-200 dark:border-gray-700 px-4 lg:px-6 py-3"
     x-data="{
         search: '{{ request('search', '') }}',
         status: '{{ request('status', '') }}',
         submit() {
             const url = new URL(window.location.href);
             url.searchParams.set('search', this.search);
             url.searchParams.set('status', this.status);
             url.searchParams.delete('page');
             window.location.href = url.toString();
         }
     }">
    <!-- Search input -->
    <div class="relative mb-3">
        <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm pointer-events-none" aria-hidden="true"></i>
        <input
            type="text"
            x-model="search"
            @input.debounce.400ms="submit()"
            placeholder="Cari nomor permohonan, jenis izin, KBLI..."
            class="w-full pl-9 pr-4 py-2.5 text-sm border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/50 rounded-lg focus:ring-2 focus:ring-[#0a66c2] focus:border-[#0a66c2] dark:text-white transition"
            aria-label="Cari permohonan"
        >
        <template x-if="search.length > 0">
            <button @click="search = ''; submit()" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 min-h-0" aria-label="Hapus pencarian">
                <i class="fas fa-times text-xs"></i>
            </button>
        </template>
    </div>
    <!-- Status Filter Chips -->
    <div class="flex gap-2 overflow-x-auto pb-0.5 scrollbar-hide -mx-1 px-1">
        @php
            $filterChips = [
                ''                    => ['label' => 'Semua',      'icon' => 'fa-layer-group'],
                'submitted'           => ['label' => 'Diajukan',   'icon' => 'fa-paper-plane'],
                'quoted'              => ['label' => 'Penawaran',   'icon' => 'fa-file-invoice-dollar'],
                'document_incomplete' => ['label' => 'Perlu Dok',  'icon' => 'fa-exclamation-triangle'],
                'payment_pending'     => ['label' => 'Bayar',      'icon' => 'fa-credit-card'],
                'in_progress'         => ['label' => 'Proses',     'icon' => 'fa-spinner'],
                'completed'           => ['label' => 'Selesai',    'icon' => 'fa-check-circle'],
            ];
        @endphp
        @foreach($filterChips as $chipStatus => $chip)
            @php $isActive = request('status', '') === $chipStatus; @endphp
            <button
                @click="status = '{{ $chipStatus }}'; submit()"
                class="flex-shrink-0 inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold transition min-h-0
                    {{ $isActive
                        ? 'bg-[#0a66c2] text-white'
                        : 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600' }}"
                aria-pressed="{{ $isActive ? 'true' : 'false' }}"
                aria-label="Filter {{ $chip['label'] }}"
            >
                <i class="fas {{ $chip['icon'] }} text-[10px]" aria-hidden="true"></i>
                {{ $chip['label'] }}
                @if($chipStatus && isset($statusCounts[$chipStatus]) && $statusCounts[$chipStatus] > 0)
                    <span class="inline-flex items-center justify-center w-4 h-4 {{ $isActive ? 'bg-white/30 text-white' : 'bg-gray-200 dark:bg-gray-600 text-gray-600 dark:text-gray-300' }} rounded-full text-[10px] font-bold min-h-0">
                        {{ $statusCounts[$chipStatus] }}
                    </span>
                @endif
            </button>
        @endforeach
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
                                @include('client.components.status-badge', [
                                    'status' => $application->status,
                                    'label'  => $meta['label'],
                                ])
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
    <div class="bg-white dark:bg-gray-800 border-y border-gray-200 dark:border-gray-700">
        @if(request('status') || request('search'))
            @include('client.components.empty-state', [
                'icon'           => 'fa-search',
                'title'          => 'Tidak Ada Permohonan Ditemukan',
                'message'        => 'Coba ubah filter atau kata kunci pencarian Anda.',
                'secondary'      => true,
                'secondaryLabel' => 'Reset Filter',
                'secondaryHref'  => route('client.applications.index'),
            ])
        @else
            @include('client.components.empty-state', [
                'icon'      => 'fa-inbox',
                'title'     => 'Belum Ada Permohonan',
                'message'   => 'Mulai ajukan permohonan izin pertama Anda sekarang.',
                'ctaLabel'  => 'Ajukan Permohonan Baru',
                'ctaHref'   => route('client.services.index'),
                'ctaIcon'   => 'fa-plus',
            ])
        @endif
    </div>
@endif
