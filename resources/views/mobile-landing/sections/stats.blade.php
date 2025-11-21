@php
    $metrics = config('landing_metrics');
@endphp

<!-- STATS INFOGRAPHIC: Magazine-Style Data Visualization -->
<section class="magazine-section bg-gradient-to-br from-blue-50 to-white fade-in-up">
    <!-- Section Title (Editorial Style) -->
    <div class="text-center mb-8">
        <h2 class="headline text-4xl text-gray-900 mb-3">
            Melayani <span class="text-gradient">Berbagai Industri</span>
        </h2>
        <div class="w-16 h-1 bg-yellow-500 mx-auto"></div>
    </div>
    
    <!-- Stats Grid - FOKUS KAPABILITAS PLATFORM -->
    <div class="grid grid-cols-2 gap-4 max-w-2xl mx-auto">
        <!-- Stat Card 1 -->
        <div class="magazine-card bg-white p-4">
            <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mb-3">
                <i class="fas fa-trophy text-2xl text-blue-600"></i>
            </div>
            <div class="text-4xl font-bold text-blue-600 mb-1">{{ $metrics['display']['experience_years'] }}</div>
            <div class="text-sm text-gray-600 font-medium mb-2">Tahun Pengalaman</div>
            <div class="text-xs text-gray-500">
                Sejak {{ $metrics['experience']['since_year'] }}, melayani berbagai industri
            </div>
        </div>
        
        <!-- Stat Card 2 -->
        <div class="magazine-card bg-white p-4">
            <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mb-3">
                <i class="fas fa-industry text-2xl text-green-600"></i>
            </div>
            <div class="text-4xl font-bold text-green-600 mb-1">{{ $metrics['clients']['industries'] }}+</div>
            <div class="text-sm text-gray-600 font-medium mb-2">Sektor Industri</div>
            <div class="text-xs text-gray-500">
                Manufaktur, properti, F&B, dan lainnya
            </div>
        </div>
        
        <!-- Stat Card 3 (Featured - Full Width) -->
        <div class="col-span-2 bg-gradient-to-r from-[#0077B5] to-[#005582] rounded-2xl p-4 text-white shadow-lg">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <div class="text-4xl font-bold mb-1">{{ $metrics['permits']['types_available'] }}+</div>
                    <div class="text-sm font-medium opacity-90 mb-2">Jenis Layanan Perizinan</div>
                    <div class="text-xs opacity-75">
                        OSS, AMDAL, PBG, SLF, dan lainnya
                    </div>
                </div>
                <div class="w-16 h-16 flex items-center justify-center">
                    <i class="fas fa-file-certificate text-5xl opacity-20"></i>
                </div>
            </div>
        </div>
        
        <!-- Stat Card 4 -->
        <div class="magazine-card bg-white p-4">
            <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center mb-3">
                <i class="fas fa-bolt text-2xl text-yellow-600"></i>
            </div>
            <div class="text-4xl font-bold text-yellow-600 mb-1">{{ $metrics['display']['process_time'] }}</div>
            <div class="text-sm text-gray-600 font-medium mb-2">Waktu Proses</div>
            <div class="text-xs text-gray-500">
                Rata-rata untuk OSS
            </div>
        </div>
        
        <!-- Stat Card 5 -->
        <div class="magazine-card bg-white p-4">
            <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mb-3">
                <i class="fas fa-desktop text-2xl text-blue-600"></i>
            </div>
            <div class="text-base font-bold text-blue-600 mb-1">{{ $metrics['display']['platform_uptime'] }}</div>
            <div class="text-sm text-gray-600 font-medium mb-2">Portal Aktif</div>
            <div class="text-xs text-gray-500">
                Monitoring real-time kapanpun
            </div>
        </div>
    </div>
</section>
