@extends('client.layouts.app')

@section('title', 'Pilih Jenis Izin - BizMark')

@section('content')
<div class="max-w-6xl mx-auto">
    <!-- Breadcrumb -->
    <nav class="mb-6 text-sm">
        <ol class="flex items-center space-x-2 text-gray-600 dark:text-gray-400">
            <li><a href="{{ route('client.services.index') }}" class="hover:text-blue-600">Katalog Layanan</a></li>
            <li><i class="fas fa-chevron-right text-xs"></i></li>
            <li><a href="{{ route('client.services.show', $kbli->code) }}" class="hover:text-blue-600">{{ $kbli->description }}</a></li>
            <li><i class="fas fa-chevron-right text-xs"></i></li>
            <li class="text-gray-900 dark:text-white font-medium">Pilih Jenis Izin</li>
        </ol>
    </nav>

    <!-- Header -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4 mb-4">
        <div class="flex items-start gap-3">
            <div class="flex-shrink-0 w-10 h-10 bg-blue-100 dark:bg-blue-900 rounded-lg flex items-center justify-center">
                <i class="fas fa-clipboard-list text-blue-600 dark:text-blue-400 text-lg"></i>
            </div>
            <div class="flex-1">
                <h1 class="text-xl font-bold text-gray-900 dark:text-white mb-1">Pilih Jenis Izin yang Akan Diajukan</h1>
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    KBLI {{ $kbli->code }}: {{ $kbli->description }}
                </p>
            </div>
        </div>
    </div>

    @if($recommendation && !empty($recommendation->recommended_permits))
    <!-- Recommended Permits from AI Analysis -->
    <div class="mb-4">
        <div class="flex items-center gap-2 mb-3">
            <i class="fas fa-star text-yellow-500"></i>
            <h2 class="text-base font-bold text-gray-900 dark:text-white">Izin yang Direkomendasikan</h2>
            <span class="text-xs text-gray-500 dark:text-gray-400">(Berdasarkan analisis KBLI Anda)</span>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            @foreach($recommendation->recommended_permits as $permit)
                @php
                    // Try to match with actual permit types
                    $matchedPermitType = $permitTypes->first(function($pt) use ($permit) {
                        return stripos($pt->name, $permit['name']) !== false 
                            || stripos($permit['name'], $pt->name) !== false;
                    });
                @endphp
                
                <div class="bg-white dark:bg-gray-800 rounded-lg border-2 {{ $matchedPermitType ? 'border-blue-200 dark:border-blue-800 hover:border-blue-400 dark:hover:border-blue-600 cursor-pointer' : 'border-gray-200 dark:border-gray-700' }} p-4 transition-all">
                    @if($matchedPermitType)
                    <a href="{{ route('client.applications.create', ['permit_type' => $matchedPermitType->id, 'kbli_code' => $kbli->code]) }}" class="block">
                        <div class="flex items-start gap-3">
                            <div class="flex-shrink-0 w-8 h-8 bg-blue-100 dark:bg-blue-900 rounded-lg flex items-center justify-center">
                                <i class="fas fa-certificate text-blue-600 dark:text-blue-400"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-start justify-between gap-2 mb-1">
                                    <h3 class="font-semibold text-gray-900 dark:text-white text-sm">
                                        {{ $permit['name'] }}
                                    </h3>
                                    @if(($permit['type'] ?? '') === 'mandatory')
                                    <span class="inline-flex items-center px-2 py-0.5 bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200 text-xs font-semibold rounded flex-shrink-0">
                                        <i class="fas fa-star text-yellow-500 mr-1"></i>
                                        WAJIB
                                    </span>
                                    @endif
                                </div>
                                <p class="text-xs text-gray-600 dark:text-gray-400 mb-2">
                                    {{ $permit['description'] ?? 'Tidak ada deskripsi' }}
                                </p>
                                <div class="flex items-center gap-3 text-xs text-gray-500 dark:text-gray-400">
                                    <span><i class="fas fa-money-bill-wave mr-1"></i>
                                        @if(isset($permit['estimated_cost_range']))
                                            Rp {{ number_format($permit['estimated_cost_range']['min'] ?? 0, 0, ',', '.') }}
                                        @else
                                            N/A
                                        @endif
                                    </span>
                                    <span><i class="fas fa-clock mr-1"></i>{{ $permit['estimated_days'] ?? 'N/A' }} hari</span>
                                </div>
                                <div class="mt-2">
                                    <span class="inline-flex items-center text-xs text-blue-600 dark:text-blue-400 font-medium">
                                        Ajukan Sekarang <i class="fas fa-arrow-right ml-1"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </a>
                    @else
                    <!-- No matching permit type in system -->
                    <div class="flex items-start gap-3 opacity-60">
                        <div class="flex-shrink-0 w-8 h-8 bg-gray-100 dark:bg-gray-700 rounded-lg flex items-center justify-center">
                            <i class="fas fa-certificate text-gray-400"></i>
                        </div>
                        <div class="flex-1">
                            <h3 class="font-semibold text-gray-700 dark:text-gray-300 text-sm mb-1">
                                {{ $permit['name'] }}
                            </h3>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mb-2">
                                {{ $permit['description'] ?? 'Tidak ada deskripsi' }}
                            </p>
                            <span class="inline-flex items-center px-2 py-1 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400 text-xs rounded">
                                <i class="fas fa-info-circle mr-1"></i>
                                Hubungi admin untuk izin ini
                            </span>
                        </div>
                    </div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- All Available Permit Types -->
    <div>
        <div class="flex items-center gap-2 mb-3">
            <i class="fas fa-list text-gray-600 dark:text-gray-400"></i>
            <h2 class="text-base font-bold text-gray-900 dark:text-white">Semua Jenis Izin Tersedia</h2>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
            @foreach($permitTypes as $permitType)
            <a href="{{ route('client.applications.create', ['permit_type' => $permitType->id, 'kbli_code' => $kbli->code]) }}" 
               class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 hover:border-blue-400 dark:hover:border-blue-600 p-4 transition-all group">
                <div class="flex items-start gap-3">
                    <div class="flex-shrink-0 w-8 h-8 bg-gray-100 dark:bg-gray-700 group-hover:bg-blue-100 dark:group-hover:bg-blue-900 rounded-lg flex items-center justify-center transition-colors">
                        <i class="fas fa-file-alt text-gray-600 dark:text-gray-400 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h3 class="font-semibold text-gray-900 dark:text-white text-sm mb-1 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">
                            {{ $permitType->name }}
                        </h3>
                        <p class="text-xs text-gray-600 dark:text-gray-400 mb-2 line-clamp-2">
                            {{ $permitType->description }}
                        </p>
                        <div class="flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400">
                            <span><i class="fas fa-clock mr-1"></i>{{ $permitType->avg_processing_days }} hari</span>
                        </div>
                    </div>
                </div>
            </a>
            @endforeach
        </div>
    </div>

    <!-- Help Section -->
    <div class="mt-6 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
        <div class="flex items-start gap-3">
            <i class="fas fa-question-circle text-blue-600 dark:text-blue-400 text-lg mt-0.5"></i>
            <div class="flex-1">
                <h3 class="font-semibold text-blue-900 dark:text-blue-100 text-sm mb-1">Butuh Bantuan?</h3>
                <p class="text-xs text-blue-800 dark:text-blue-200 mb-2">
                    Tidak yakin izin mana yang harus diajukan? Tim ahli kami siap membantu Anda memilih izin yang tepat.
                </p>
                <a href="#" onclick="alert('Fitur konsultasi akan segera hadir. Silakan hubungi admin untuk bantuan.'); return false;" 
                   class="inline-flex items-center text-xs text-blue-600 dark:text-blue-400 font-semibold hover:underline">
                    <i class="fas fa-comments mr-1"></i>
                    Konsultasi Gratis
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
