@php
    $metrics = config('landing_metrics');
@endphp

<!-- SOCIAL PROOF: Live Activity & Client Logos -->
<section class="magazine-section bg-gradient-to-br from-blue-50 via-purple-50 to-pink-50 fade-in-up">
    
    <!-- Live Activity Counter -->
    <div class="bg-white rounded-2xl shadow-lg p-6 mb-6">
        <div class="text-center mb-4">
            <div class="inline-flex items-center gap-2 bg-green-100 text-green-800 px-4 py-2 rounded-full mb-3">
                <span class="relative flex h-3 w-3">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-3 w-3 bg-green-500"></span>
                </span>
                <span class="text-sm font-bold">{{ $metrics['contact']['hours'] }}</span>
            </div>
            <h3 class="text-2xl font-bold text-gray-900 mb-2">
                Layanan <span class="text-gradient">Profesional</span>
            </h3>
        </div>
        
        <!-- Stats Grid -->
        <div class="grid grid-cols-3 gap-4">
            <div class="text-center">
                <div class="text-3xl font-bold text-blue-600 mb-1">{{ $metrics['display']['clients_total'] }}</div>
                <div class="text-xs text-gray-600">Klien Aktif</div>
            </div>
            <div class="text-center">
                <div class="text-3xl font-bold text-green-600 mb-1">{{ $metrics['clients']['industries'] }}</div>
                <div class="text-xs text-gray-600">Sektor Industri</div>
            </div>
            <div class="text-center">
                <div class="text-3xl font-bold text-purple-600 mb-1">{{ $metrics['permits']['types_available'] }}+</div>
                <div class="text-xs text-gray-600">Jenis Perizinan</div>
            </div>
        </div>
        
        <!-- Info -->
        <div class="mt-6 pt-4 border-t border-gray-200">
            <div class="flex items-start gap-3 text-sm">
                <div class="w-2 h-2 bg-blue-500 rounded-full mt-1.5"></div>
                <div class="flex-1">
                    <p class="text-gray-700 font-medium">Konsultasi Gratis & Responsif</p>
                    <p class="text-gray-500 text-xs">Tim kami siap membantu kebutuhan perizinan bisnis Anda</p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Trusted By Section -->
    <div class="text-center mb-6">
        <h3 class="text-xl font-bold text-gray-900 mb-2">
            Dipercaya Oleh
        </h3>
        <p class="text-sm text-gray-600 mb-6">
            {{ $metrics['display']['clients_total'] }} perusahaan dari {{ $metrics['clients']['industries'] }} sektor industri
        </p>
        
        <!-- Client Industries Grid -->
        <div class="grid grid-cols-3 gap-4 mb-4">
            <div class="bg-white rounded-xl p-4 shadow-sm flex items-center justify-center h-20">
                <div class="text-center">
                    <i class="fas fa-building text-3xl text-blue-500 mb-1"></i>
                    <p class="text-xs text-gray-700 font-semibold">Property</p>
                </div>
            </div>
            <div class="bg-white rounded-xl p-4 shadow-sm flex items-center justify-center h-20">
                <div class="text-center">
                    <i class="fas fa-industry text-3xl text-orange-500 mb-1"></i>
                    <p class="text-xs text-gray-700 font-semibold">Industry</p>
                </div>
            </div>
            <div class="bg-white rounded-xl p-4 shadow-sm flex items-center justify-center h-20">
                <div class="text-center">
                    <i class="fas fa-broadcast-tower text-3xl text-purple-500 mb-1"></i>
                    <p class="text-xs text-gray-700 font-semibold">Telekomunikasi</p>
                </div>
            </div>
            <div class="bg-white rounded-xl p-4 shadow-sm flex items-center justify-center h-20">
                <div class="text-center">
                    <i class="fas fa-laptop-code text-3xl text-cyan-500 mb-1"></i>
                    <p class="text-xs text-gray-700 font-semibold">Teknologi</p>
                </div>
            </div>
            <div class="bg-white rounded-xl p-4 shadow-sm flex items-center justify-center h-20">
                <div class="text-center">
                    <i class="fas fa-hard-hat text-3xl text-yellow-600 mb-1"></i>
                    <p class="text-xs text-gray-700 font-semibold">Pertambangan</p>
                </div>
            </div>
            <div class="bg-white rounded-xl p-4 shadow-sm flex items-center justify-center h-20">
                <div class="text-center">
                    <i class="fas fa-heartbeat text-3xl text-red-500 mb-1"></i>
                    <p class="text-xs text-gray-700 font-semibold">Kesehatan</p>
                </div>
            </div>
        </div>
        
        <p class="text-xs text-gray-500 italic">
            Klien dari berbagai sektor industri telah mempercayai layanan perizinan kami
        </p>
    </div>
    
    <!-- Quick Success Story -->
    <div class="bg-white rounded-2xl shadow-lg p-6">
        <div class="flex items-start gap-4">
            <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-emerald-600 rounded-full flex items-center justify-center flex-shrink-0">
                <span class="text-white font-bold text-sm">AC</span>
            </div>
            <div class="flex-1">
                <p class="text-sm text-gray-700 italic leading-relaxed mb-3">
                    "UKL-UPL untuk pabrik paving block kami diurus dengan sangat detail dan teliti. 
                    Tim Bizmark.ID membantu lengkapi semua persyaratan dengan cepat."
                </p>
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-bold text-gray-900">Operational Manager</p>
                        <p class="text-xs text-gray-500">PT Asiacon Cipta Prima</p>
                    </div>
                    <div class="flex gap-0.5">
                        <i class="fas fa-star text-yellow-400 text-sm"></i>
                        <i class="fas fa-star text-yellow-400 text-sm"></i>
                        <i class="fas fa-star text-yellow-400 text-sm"></i>
                        <i class="fas fa-star text-yellow-400 text-sm"></i>
                        <i class="fas fa-star text-yellow-400 text-sm"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
</section>
