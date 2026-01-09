@php
    $currentLocale = app()->getLocale();
    $isEnglish = $currentLocale === 'en';
    $services = collect(config('services_data'));
    $featured = $services->where('featured', true)->first();
    $others = $services->where('featured', '!=', true)->take(5);
@endphp

<!-- Services Section - Dark Theme -->
<section id="services" class="py-20 px-4" style="background: #1C1C1E;">
    <div class="container mx-auto">
        <!-- Section Header -->
        <div class="text-center mb-12">
            <h2 class="text-3xl md:text-4xl font-bold text-white mb-4">
                {{ $isEnglish ? 'Our' : 'Layanan' }} 
                <span class="bg-clip-text text-transparent bg-gradient-to-r from-blue-400 to-green-400">
                    {{ $isEnglish ? 'Services' : 'Kami' }}
                </span>
            </h2>
            <p class="text-gray-400 max-w-2xl mx-auto">
                {{ $isEnglish ? 'Professional business licensing services with fast and transparent processes' : 'Layanan perizinan usaha profesional dengan proses cepat dan transparan' }}
            </p>
        </div>

        <!-- Services Grid -->
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
            @if($featured)
            <!-- Featured Service -->
            <div class="feature-card md:col-span-2">
                <div class="feature-icon" style="background: linear-gradient(135deg, {{ $featured['color'] }}, {{ $featured['color'] }}dd);">
                    <i class="fas {{ $featured['icon'] }}"></i>
                </div>
                <h3 class="text-xl font-bold text-white mb-2">{{ $featured['title'] }}</h3>
                <p class="text-gray-400 mb-4">{{ $featured['short_description'] }}</p>
                @if(isset($featured['price']))
                <p class="text-sm text-gray-500 mb-2">{{ $isEnglish ? 'Starting from' : 'Mulai dari' }}</p>
                <p class="text-2xl font-bold mb-4" style="color: {{ $featured['color'] }};">{{ $featured['price'] }}</p>
                @endif
                <a href="{{ route($isEnglish ? 'services.show.en' : 'services.show.id', $featured['slug']) }}" 
                   class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-lg hover:shadow-lg transition">
                    <span>{{ $isEnglish ? 'Learn More' : 'Lihat Detail' }}</span>
                    <i class="fas fa-arrow-right"></i>
                </a>
            </div>
            @endif

            @foreach($others as $service)
            <!-- Service Card -->
            <article class="feature-card">
                <div class="feature-icon" style="background: linear-gradient(135deg, {{ $service['color'] }}, {{ $service['color'] }}dd);">
                    <i class="fas {{ $service['icon'] }}"></i>
                </div>
                <h3 class="text-lg font-bold text-white mb-2">{{ $service['title'] }}</h3>
                <p class="text-sm text-gray-400 mb-4">{{ $service['short_description'] }}</p>
                <a href="{{ route($isEnglish ? 'services.show.en' : 'services.show.id', $service['slug']) }}" 
                   class="text-blue-400 hover:text-blue-300 text-sm font-semibold transition">
                    {{ $isEnglish ? 'Learn more' : 'Selengkapnya' }} →
                </a>
            </article>
            @endforeach
        </div>

        <!-- CTA Banner -->
        <div class="mt-12 p-8 rounded-2xl text-center" style="background: linear-gradient(135deg, #007AFF, #0051D5);">
            <i class="fas fa-robot text-5xl text-white mb-4 opacity-90"></i>
            <h3 class="text-2xl font-bold text-white mb-2">
                {{ $isEnglish ? "Not sure which permit you need?" : "Tidak Tahu Izin Apa yang Dibutuhkan?" }}
            </h3>
            <p class="text-white/90 mb-6">
                {{ $isEnglish ? "Try our FREE AI Analysis in 30 seconds!" : "Coba Analisis AI Gratis kami dalam 30 detik!" }}
            </p>
            <button @click="window.dispatchEvent(new CustomEvent('open-ai-estimator'))" 
                    class="inline-flex items-center gap-2 px-8 py-4 bg-white text-blue-600 font-bold rounded-xl hover:shadow-xl transition">
                <span>🤖</span>
                <span>{{ $isEnglish ? 'AI Analysis' : 'Analisis AI Gratis' }}</span>
            </button>
        </div>
    </div>
</section>

<style>
.feature-card {
    background: rgba(255,255,255,0.05);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255,255,255,0.1);
    border-radius: 1rem;
    padding: 2rem;
    transition: all 0.3s ease;
}

.feature-card:hover {
    transform: translateY(-8px);
    background: rgba(255,255,255,0.08);
    box-shadow: 0 15px 35px rgba(0,122,255,0.2);
    border-color: #007AFF;
}

.feature-icon {
    width: 70px;
    height: 70px;
    background: linear-gradient(135deg, #007AFF, #0051D5);
    color: #fff;
    border-radius: 1rem;
    display: flex;
    align-items: center;
    justify-center;
    font-size: 2rem;
    margin-bottom: 1.5rem;
}
</style>
