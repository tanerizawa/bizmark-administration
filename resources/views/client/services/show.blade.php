@extends('client.layouts.app')

@section('title', 'Rekomendasi Perizinan - BizMark')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Back Button -->
        <a href="{{ route('client.services.index') }}" class="inline-flex items-center text-blue-600 dark:text-blue-400 hover:underline mb-6">
            <i class="fas fa-arrow-left mr-2"></i>
            Kembali ke Katalog
        </a>

        @if(session('error'))
        <div class="mb-6 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4">
            <div class="flex items-start">
                <i class="fas fa-exclamation-circle text-red-600 dark:text-red-400 mt-0.5 mr-3"></i>
                <div>
                    <strong class="font-semibold text-red-800 dark:text-red-300">Error:</strong>
                    <p class="text-red-700 dark:text-red-300 mt-1">{{ session('error') }}</p>
                    <p class="text-sm text-red-600 dark:text-red-400 mt-2">
                        Silakan hubungi administrator atau coba lagi nanti.
                    </p>
                </div>
            </div>
        </div>
        @endif

        @if(!$recommendation)
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-12 text-center">
            <i class="fas fa-robot text-6xl text-gray-400 dark:text-gray-600 mb-4"></i>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">Menganalisis Perizinan...</h2>
            <p class="text-gray-600 dark:text-gray-400 mb-6">AI kami sedang mempelajari kebutuhan izin untuk usaha Anda. Harap tunggu sebentar.</p>
            <div class="inline-block animate-spin rounded-full h-12 w-12 border-4 border-gray-200 border-t-blue-600"></div>
        </div>
        @else
        <!-- KBLI Header -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6 mb-6">
            <div class="flex items-start justify-between">
                <div>
                    <span class="inline-block px-3 py-1 bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 text-xs font-mono font-semibold rounded mb-2">
                        {{ $kbli->code }}
                    </span>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-1">
                        {{ $kbli->description }}
                    </h1>
                    <div class="text-sm text-gray-600 dark:text-gray-400">
                        {{ $kbli->sector }} • {{ $kbli->division_desc }}
                    </div>
                </div>
                <div class="text-right">
                    <div class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium
                        {{ $recommendation->confidence_score >= 0.8 ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200' }}">
                        <i class="fas fa-check-circle mr-1"></i>
                        Confidence: {{ number_format($recommendation->confidence_score * 100, 0) }}%
                    </div>
                    <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                        AI Model: {{ $recommendation->ai_model }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <div class="bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-800/20 rounded-lg p-6 border border-blue-200 dark:border-blue-800">
                <div class="flex items-center justify-between mb-2">
                    <i class="fas fa-file-alt text-2xl text-blue-600 dark:text-blue-400"></i>
                    <span class="text-xs text-blue-600 dark:text-blue-400 font-medium">TOTAL IZIN</span>
                </div>
                <div class="text-3xl font-bold text-blue-900 dark:text-blue-100">
                    {{ $recommendation->mandatory_permits_count }}
                </div>
                <div class="text-sm text-blue-700 dark:text-blue-300 mt-1">Izin Wajib</div>
            </div>

            <div class="bg-gradient-to-br from-green-50 to-green-100 dark:from-green-900/20 dark:to-green-800/20 rounded-lg p-6 border border-green-200 dark:border-green-800">
                <div class="flex items-center justify-between mb-2">
                    <i class="fas fa-money-bill-wave text-2xl text-green-600 dark:text-green-400"></i>
                    <span class="text-xs text-green-600 dark:text-green-400 font-medium">ESTIMASI BIAYA</span>
                </div>
                <div class="text-2xl font-bold text-green-900 dark:text-green-100">
                    Rp {{ number_format($recommendation->total_cost_range['min'] ?? 0, 0, ',', '.') }}
                </div>
                <div class="text-sm text-green-700 dark:text-green-300 mt-1">
                    s/d Rp {{ number_format($recommendation->total_cost_range['max'] ?? 0, 0, ',', '.') }}
                </div>
            </div>

            <div class="bg-gradient-to-br from-purple-50 to-purple-100 dark:from-purple-900/20 dark:to-purple-800/20 rounded-lg p-6 border border-purple-200 dark:border-purple-800">
                <div class="flex items-center justify-between mb-2">
                    <i class="fas fa-clock text-2xl text-purple-600 dark:text-purple-400"></i>
                    <span class="text-xs text-purple-600 dark:text-purple-400 font-medium">WAKTU PROSES</span>
                </div>
                <div class="text-3xl font-bold text-purple-900 dark:text-purple-100">
                    {{ $recommendation->estimated_timeline['total_days'] ?? '?' }}
                </div>
                <div class="text-sm text-purple-700 dark:text-purple-300 mt-1">Hari Kerja</div>
            </div>
        </div>

        <!-- Risk Assessment -->
        @if(!empty($recommendation->risk_assessment))
        <div class="mb-6 bg-orange-50 dark:bg-orange-900/20 border border-orange-200 dark:border-orange-800 rounded-lg p-6">
            <div class="flex items-start">
                <i class="fas fa-exclamation-triangle text-orange-600 dark:text-orange-400 text-xl mr-3 mt-1"></i>
                <div class="flex-1">
                    <h3 class="font-semibold text-orange-900 dark:text-orange-200 mb-2">Penilaian Risiko</h3>
                    <div class="text-sm text-orange-800 dark:text-orange-300 space-y-1">
                        @if(isset($recommendation->risk_assessment['level']))
                        <p><strong>Level Risiko:</strong> {{ ucfirst($recommendation->risk_assessment['level']) }}</p>
                        @endif
                        @if(isset($recommendation->risk_assessment['notes']))
                        <p>{{ $recommendation->risk_assessment['notes'] }}</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Mandatory Permits -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 mb-6">
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white flex items-center">
                    <i class="fas fa-star text-yellow-500 mr-2"></i>
                    Izin Wajib
                </h2>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                    Perizinan yang harus dipenuhi untuk menjalankan usaha secara legal
                </p>
            </div>

            <div class="divide-y divide-gray-200 dark:divide-gray-700">
                @forelse($recommendation->recommended_permits as $index => $permit)
                @if(($permit['is_mandatory'] ?? false))
                <div class="p-6">
                    <div class="flex items-start">
                        <div class="flex-shrink-0 w-10 h-10 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center mr-4">
                            <span class="text-blue-600 dark:text-blue-400 font-bold">{{ $loop->iteration }}</span>
                        </div>
                        <div class="flex-1">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">
                                {{ $permit['name'] }}
                            </h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">
                                {{ $permit['description'] ?? 'Tidak ada deskripsi' }}
                            </p>
                            
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                                <div>
                                    <span class="text-gray-500 dark:text-gray-400">Penerbit:</span>
                                    <span class="ml-2 font-medium text-gray-900 dark:text-white">{{ $permit['issuing_authority'] ?? 'N/A' }}</span>
                                </div>
                                <div>
                                    <span class="text-gray-500 dark:text-gray-400">Biaya:</span>
                                    <span class="ml-2 font-medium text-gray-900 dark:text-white">
                                        @if(isset($permit['cost']))
                                            @if($permit['cost'] == 0)
                                                Gratis
                                            @else
                                                Rp {{ number_format($permit['cost'], 0, ',', '.') }}
                                            @endif
                                        @else
                                            N/A
                                        @endif
                                    </span>
                                </div>
                                <div>
                                    <span class="text-gray-500 dark:text-gray-400">Waktu Proses:</span>
                                    <span class="ml-2 font-medium text-gray-900 dark:text-white">{{ $permit['processing_time'] ?? 'N/A' }}</span>
                                </div>
                            </div>

                            @if(!empty($permit['requirements']))
                            <div class="mt-3">
                                <button 
                                    onclick="toggleRequirements('permit-{{ $index }}')" 
                                    class="text-sm text-blue-600 dark:text-blue-400 hover:underline"
                                >
                                    <i class="fas fa-chevron-down mr-1" id="icon-permit-{{ $index }}"></i>
                                    Lihat Persyaratan ({{ count($permit['requirements']) }})
                                </button>
                                <ul id="permit-{{ $index }}" class="hidden mt-2 ml-4 space-y-1 text-sm text-gray-700 dark:text-gray-300 list-disc list-inside">
                                    @foreach($permit['requirements'] as $req)
                                    <li>{{ $req }}</li>
                                    @endforeach
                                </ul>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                @endif
                @empty
                <div class="p-6 text-center text-gray-500 dark:text-gray-400">
                    Tidak ada izin wajib teridentifikasi
                </div>
                @endforelse
            </div>
        </div>

        <!-- Required Documents -->
        @if(!empty($recommendation->required_documents))
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 mb-6">
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white flex items-center">
                    <i class="fas fa-folder-open mr-2 text-blue-600"></i>
                    Dokumen yang Dibutuhkan
                </h2>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    @foreach($recommendation->required_documents as $doc)
                    <div class="flex items-center p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <i class="fas fa-file-alt text-gray-400 dark:text-gray-500 mr-3"></i>
                        <span class="text-sm text-gray-700 dark:text-gray-300">{{ $doc }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        <!-- Timeline -->
        @if(!empty($recommendation->estimated_timeline['phases']))
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 mb-6">
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white flex items-center">
                    <i class="fas fa-calendar-alt mr-2 text-purple-600"></i>
                    Timeline Proses
                </h2>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    @foreach($recommendation->estimated_timeline['phases'] as $phase)
                    <div class="flex items-start">
                        <div class="flex-shrink-0 w-8 h-8 bg-purple-100 dark:bg-purple-900 rounded-full flex items-center justify-center mr-4 mt-1">
                            <i class="fas fa-check text-purple-600 dark:text-purple-400 text-sm"></i>
                        </div>
                        <div class="flex-1">
                            <h4 class="font-semibold text-gray-900 dark:text-white">{{ $phase['name'] ?? 'Phase' }}</h4>
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                Durasi: {{ $phase['duration'] ?? 'N/A' }}
                            </p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        <!-- Action Buttons -->
        <div class="flex flex-wrap gap-4 justify-center">
            <button 
                onclick="window.print()" 
                class="px-6 py-3 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-colors inline-flex items-center"
            >
                <i class="fas fa-download mr-2"></i>
                Download PDF
            </button>
            
            <a 
                href="{{ route('client.contact') }}" 
                class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors inline-flex items-center"
            >
                <i class="fas fa-comments mr-2"></i>
                Konsultasi dengan Ahli
            </a>
            
            <a 
                href="{{ route('client.applications.create', ['kbli_code' => $kbli->code]) }}" 
                class="px-6 py-3 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors inline-flex items-center"
            >
                <i class="fas fa-paper-plane mr-2"></i>
                Ajukan Permohonan
            </a>
        </div>

        <!-- Footer Note -->
        <div class="mt-8 text-center text-sm text-gray-500 dark:text-gray-400">
            <i class="fas fa-info-circle mr-1"></i>
            Rekomendasi ini dihasilkan oleh AI berdasarkan data KBLI dan regulasi terkini.
            Untuk kepastian hukum, silakan konsultasikan dengan ahli perizinan kami.
        </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
function toggleRequirements(id) {
    const element = document.getElementById(id);
    const icon = document.getElementById('icon-' + id);
    
    if (element.classList.contains('hidden')) {
        element.classList.remove('hidden');
        icon.classList.remove('fa-chevron-down');
        icon.classList.add('fa-chevron-up');
    } else {
        element.classList.add('hidden');
        icon.classList.remove('fa-chevron-up');
        icon.classList.add('fa-chevron-down');
    }
}
</script>
@endpush
@endsection

