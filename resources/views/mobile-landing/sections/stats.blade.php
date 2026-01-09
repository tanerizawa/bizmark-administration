@php
    $currentLocale = app()->getLocale();
    $isEnglish = $currentLocale === 'en';
@endphp

<!-- Stats Section - Desktop Style -->
<section class="py-16 px-4 bg-dark-bg-secondary" aria-label="{{ $isEnglish ? 'Bizmark.ID Statistics and Achievements' : 'Statistik dan Pencapaian Bizmark.ID' }}">
    <div class="container mx-auto">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6 md:gap-8">
            <!-- Stat 1 -->
            <div class="stat-card text-center">
                <div class="stat-number text-4xl md:text-5xl font-bold mb-2 text-white">10+</div>
                <p class="text-sm md:text-lg text-gray-400">
                    {{ $isEnglish ? 'Years Experience' : 'Tahun Pengalaman' }}
                </p>
            </div>
            
            <!-- Stat 2 -->
            <div class="stat-card text-center">
                <div class="stat-number text-4xl md:text-5xl font-bold mb-2 text-white">500+</div>
                <p class="text-sm md:text-lg text-gray-400">
                    {{ $isEnglish ? 'Clients Served' : 'Klien Terlayani' }}
                </p>
            </div>
            
            <!-- Stat 3 -->
            <div class="stat-card text-center">
                <div class="stat-number text-4xl md:text-5xl font-bold mb-2 text-white">1000+</div>
                <p class="text-sm md:text-lg text-gray-400">
                    {{ $isEnglish ? 'Permits Completed' : 'Perizinan Selesai' }}
                </p>
            </div>
            
            <!-- Stat 4 -->
            <div class="stat-card text-center">
                <div class="stat-number text-4xl md:text-5xl font-bold mb-2 text-white">98%</div>
                <p class="text-sm md:text-lg text-gray-400">
                    {{ $isEnglish ? 'Client Satisfaction' : 'Kepuasan Klien' }}
                </p>
            </div>
        </div>
    </div>
</section>

<style>
    .bg-dark-bg-secondary {
        background: #1C1C1E;
    }
    
    .stat-card {
        padding: 1.5rem;
        border-radius: 1rem;
        background: rgba(255, 255, 255, 0.05);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.1);
        transition: all 0.3s ease;
    }
    
    .stat-card:hover {
        transform: translateY(-5px);
        background: rgba(255, 255, 255, 0.08);
        box-shadow: 0 10px 30px rgba(0, 122, 255, 0.2);
    }
    
    .stat-number {
        background: linear-gradient(135deg, #007AFF 0%, #34C759 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }
</style>
