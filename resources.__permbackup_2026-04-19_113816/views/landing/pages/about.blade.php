@extends('landing.layout')

@php
    $isEnglish = ($locale ?? app()->getLocale()) === 'en';
    $pageTitle = $isEnglish ? 'About Us' : 'Tentang Kami';
    $pageDescription = $isEnglish 
        ? 'Learn about Bizmark.ID - Your trusted partner for business permit and investment services across Indonesia. ISO certified, expert team, nationwide coverage.'
        : 'Pelajari tentang Bizmark.ID - Mitra terpercaya Anda untuk layanan perizinan dan investasi usaha di seluruh Indonesia. Bersertifikasi ISO, tim ahli, cakupan nasional.';
    
    $stats = config('landing_metrics.stats', [
        'years_experience' => 12,
        'clients_served' => 500,
        'success_rate' => 98,
        'permits_processed' => 2500
    ]);
@endphp

@section('title', $pageTitle . ' - Bizmark.ID')
@section('meta_description', $pageDescription)

@section('structured_data')
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@type": "Organization",
    "name": "Bizmark.ID",
    "description": "{{ $pageDescription }}",
    "url": "{{ url('/') }}",
    "logo": "{{ asset('logo.svg') }}",
    "foundingDate": "2012",
    "areaServed": "Indonesia",
    "numberOfEmployees": {
        "@@type": "QuantitativeValue",
        "minValue": 10,
        "maxValue": 50
    },
    "sameAs": [
        "https://www.instagram.com/bizmark.id",
        "https://www.linkedin.com/company/bizmark-id"
    ]
}
</script>
@endsection

@section('content')
<!-- Hero Section -->
<section class="relative overflow-hidden pt-24 pb-16" style="background: linear-gradient(135deg, var(--surface-warm) 0%, var(--surface-secondary) 100%);">
    <div class="container-wide">
        <div class="max-w-3xl mx-auto text-center">
            <span class="section-badge mb-4">{{ $isEnglish ? 'About Bizmark.ID' : 'Tentang Bizmark.ID' }}</span>
            <h1 class="text-4xl md:text-5xl font-bold mb-6" style="color: var(--text-primary);">
                {{ $isEnglish ? 'Your Trusted Partner for Business Permits' : 'Mitra Terpercaya untuk Perizinan Usaha' }}
            </h1>
            <p class="text-xl leading-relaxed mb-8" style="color: var(--text-secondary);">
                {{ $isEnglish 
                    ? 'Since 2012, we\'ve been helping businesses navigate the complex landscape of permits and regulations in Indonesia.' 
                    : 'Sejak 2012, kami telah membantu bisnis mengelola kompleksitas perizinan dan regulasi di Indonesia.' }}
            </p>
        </div>
    </div>
</section>

<!-- Stats Section -->
<section class="py-12" style="background: var(--color-primary);">
    <div class="container-wide">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
            <div class="text-center text-white">
                <div class="text-4xl md:text-5xl font-bold mb-2">{{ $stats['years_experience'] ?? 12 }}+</div>
                <div class="text-sm opacity-80">{{ $isEnglish ? 'Years Experience' : 'Tahun Pengalaman' }}</div>
            </div>
            <div class="text-center text-white">
                <div class="text-4xl md:text-5xl font-bold mb-2">{{ $stats['clients_served'] ?? 500 }}+</div>
                <div class="text-sm opacity-80">{{ $isEnglish ? 'Clients Served' : 'Klien Dilayani' }}</div>
            </div>
            <div class="text-center text-white">
                <div class="text-4xl md:text-5xl font-bold mb-2">{{ $stats['success_rate'] ?? 98 }}%</div>
                <div class="text-sm opacity-80">{{ $isEnglish ? 'Success Rate' : 'Tingkat Keberhasilan' }}</div>
            </div>
            <div class="text-center text-white">
                <div class="text-4xl md:text-5xl font-bold mb-2">{{ $stats['permits_processed'] ?? 2500 }}+</div>
                <div class="text-sm opacity-80">{{ $isEnglish ? 'Permits Processed' : 'Izin Diproses' }}</div>
            </div>
        </div>
    </div>
</section>

<!-- About Content Section -->
<section class="section" style="background: var(--surface-primary);">
    <div class="container-wide">
        <div class="grid lg:grid-cols-2 gap-12 items-center">
            <!-- Left: Story -->
            <div>
                <h2 class="text-3xl font-bold mb-6" style="color: var(--text-primary);">
                    {{ $isEnglish ? 'Our Story' : 'Cerita Kami' }}
                </h2>
                <div class="space-y-4 text-lg" style="color: var(--text-secondary);">
                    <p>
                        {{ $isEnglish 
                            ? 'Bizmark.ID was founded with a simple mission: to make business permits accessible and hassle-free for every entrepreneur in Indonesia.'
                            : 'Bizmark.ID didirikan dengan misi sederhana: menjadikan perizinan usaha mudah diakses dan tanpa hambatan bagi setiap pengusaha di Indonesia.' }}
                    </p>
                    <p>
                        {{ $isEnglish 
                            ? 'Over the years, we\'ve built a team of experienced professionals who understand the intricacies of Indonesian business regulations. Our deep knowledge spans various industries and permit types, from basic business licenses to complex investment permits.'
                            : 'Selama bertahun-tahun, kami telah membangun tim profesional berpengalaman yang memahami seluk-beluk regulasi bisnis Indonesia. Pengetahuan mendalam kami mencakup berbagai industri dan jenis izin, dari izin usaha dasar hingga izin investasi yang kompleks.' }}
                    </p>
                    <p>
                        {{ $isEnglish 
                            ? 'Today, we continue to innovate with AI-powered tools and digital solutions while maintaining the personal touch that our clients value.'
                            : 'Saat ini, kami terus berinovasi dengan alat berbasis AI dan solusi digital sambil mempertahankan sentuhan personal yang dihargai klien kami.' }}
                    </p>
                </div>
            </div>
            
            <!-- Right: Features -->
            <div class="grid sm:grid-cols-2 gap-6">
                <!-- Feature 1 -->
                <div class="magazine-card p-6">
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center mb-4" style="background: linear-gradient(135deg, #10b981, #059669);">
                        <i class="fas fa-certificate text-xl text-white"></i>
                    </div>
                    <h3 class="text-lg font-bold mb-2" style="color: var(--text-primary);">
                        {{ $isEnglish ? 'ISO Certified' : 'Bersertifikasi ISO' }}
                    </h3>
                    <p class="text-sm" style="color: var(--text-secondary);">
                        {{ $isEnglish 
                            ? 'Our quality management system meets international standards'
                            : 'Sistem manajemen kualitas kami memenuhi standar internasional' }}
                    </p>
                </div>
                
                <!-- Feature 2 -->
                <div class="magazine-card p-6">
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center mb-4" style="background: linear-gradient(135deg, #3b82f6, #1d4ed8);">
                        <i class="fas fa-user-tie text-xl text-white"></i>
                    </div>
                    <h3 class="text-lg font-bold mb-2" style="color: var(--text-primary);">
                        {{ $isEnglish ? 'Expert Team' : 'Tim Ahli' }}
                    </h3>
                    <p class="text-sm" style="color: var(--text-secondary);">
                        {{ $isEnglish 
                            ? 'Experienced professionals with deep regulatory knowledge'
                            : 'Profesional berpengalaman dengan pengetahuan regulasi mendalam' }}
                    </p>
                </div>
                
                <!-- Feature 3 -->
                <div class="magazine-card p-6">
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center mb-4" style="background: linear-gradient(135deg, #8b5cf6, #6366f1);">
                        <i class="fas fa-handshake text-xl text-white"></i>
                    </div>
                    <h3 class="text-lg font-bold mb-2" style="color: var(--text-primary);">
                        {{ $isEnglish ? 'Trusted Partners' : 'Mitra Terpercaya' }}
                    </h3>
                    <p class="text-sm" style="color: var(--text-secondary);">
                        {{ $isEnglish 
                            ? 'Strong relationships with government agencies and institutions'
                            : 'Hubungan kuat dengan instansi dan lembaga pemerintah' }}
                    </p>
                </div>
                
                <!-- Feature 4 -->
                <div class="magazine-card p-6">
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center mb-4" style="background: linear-gradient(135deg, #f59e0b, #d97706);">
                        <i class="fas fa-map-marked-alt text-xl text-white"></i>
                    </div>
                    <h3 class="text-lg font-bold mb-2" style="color: var(--text-primary);">
                        {{ $isEnglish ? 'Nationwide' : 'Se-Indonesia' }}
                    </h3>
                    <p class="text-sm" style="color: var(--text-secondary);">
                        {{ $isEnglish 
                            ? 'Coverage across all provinces in Indonesia'
                            : 'Cakupan di seluruh provinsi di Indonesia' }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Vision Mission Section -->
<section class="section" style="background: var(--surface-warm);">
    <div class="container-wide">
        <div class="grid md:grid-cols-2 gap-8">
            <!-- Vision -->
            <div class="magazine-card p-8">
                <div class="flex items-center gap-4 mb-6">
                    <div class="w-14 h-14 rounded-xl flex items-center justify-center" style="background: linear-gradient(135deg, var(--color-primary), var(--color-primary-dark));">
                        <i class="fas fa-eye text-2xl text-white"></i>
                    </div>
                    <h3 class="text-2xl font-bold" style="color: var(--text-primary);">
                        {{ $isEnglish ? 'Our Vision' : 'Visi Kami' }}
                    </h3>
                </div>
                <p class="text-lg leading-relaxed" style="color: var(--text-secondary);">
                    {{ $isEnglish 
                        ? 'To be Indonesia\'s leading business permit consultation service, known for excellence, integrity, and innovation in simplifying regulatory compliance for businesses of all sizes.'
                        : 'Menjadi layanan konsultasi perizinan usaha terkemuka di Indonesia, dikenal karena keunggulan, integritas, dan inovasi dalam menyederhanakan kepatuhan regulasi bagi bisnis dari semua ukuran.' }}
                </p>
            </div>
            
            <!-- Mission -->
            <div class="magazine-card p-8">
                <div class="flex items-center gap-4 mb-6">
                    <div class="w-14 h-14 rounded-xl flex items-center justify-center" style="background: linear-gradient(135deg, #10b981, #059669);">
                        <i class="fas fa-bullseye text-2xl text-white"></i>
                    </div>
                    <h3 class="text-2xl font-bold" style="color: var(--text-primary);">
                        {{ $isEnglish ? 'Our Mission' : 'Misi Kami' }}
                    </h3>
                </div>
                <ul class="space-y-3" style="color: var(--text-secondary);">
                    <li class="flex items-start gap-3">
                        <i class="fas fa-check-circle mt-1" style="color: var(--color-success);"></i>
                        <span>{{ $isEnglish ? 'Provide expert guidance and end-to-end support for all business permits' : 'Memberikan panduan ahli dan dukungan end-to-end untuk semua perizinan usaha' }}</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <i class="fas fa-check-circle mt-1" style="color: var(--color-success);"></i>
                        <span>{{ $isEnglish ? 'Leverage technology to streamline and accelerate permit processing' : 'Memanfaatkan teknologi untuk mempercepat proses perizinan' }}</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <i class="fas fa-check-circle mt-1" style="color: var(--color-success);"></i>
                        <span>{{ $isEnglish ? 'Maintain transparency and build trust with every client interaction' : 'Menjaga transparansi dan membangun kepercayaan di setiap interaksi' }}</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <i class="fas fa-check-circle mt-1" style="color: var(--color-success);"></i>
                        <span>{{ $isEnglish ? 'Continuously improve services based on client feedback' : 'Terus meningkatkan layanan berdasarkan umpan balik klien' }}</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</section>

<!-- Values Section -->
<section class="section" style="background: var(--surface-primary);">
    <div class="container-wide">
        <div class="text-center mb-12">
            <h2 class="section-title mb-4">{{ $isEnglish ? 'Our Core Values' : 'Nilai Inti Kami' }}</h2>
            <p class="section-description mx-auto">
                {{ $isEnglish 
                    ? 'The principles that guide everything we do'
                    : 'Prinsip-prinsip yang memandu semua yang kami lakukan' }}
            </p>
        </div>
        
        <div class="grid md:grid-cols-3 gap-6 max-w-4xl mx-auto">
            <!-- Value 1 -->
            <div class="text-center p-6">
                <div class="w-16 h-16 mx-auto rounded-full flex items-center justify-center mb-4" style="background: rgba(var(--color-primary-rgb), 0.1);">
                    <i class="fas fa-balance-scale text-2xl" style="color: var(--color-primary);"></i>
                </div>
                <h3 class="text-xl font-bold mb-2" style="color: var(--text-primary);">{{ $isEnglish ? 'Integrity' : 'Integritas' }}</h3>
                <p class="text-sm" style="color: var(--text-secondary);">
                    {{ $isEnglish 
                        ? 'Honest, ethical practices in all dealings'
                        : 'Praktik jujur dan etis dalam semua urusan' }}
                </p>
            </div>
            
            <!-- Value 2 -->
            <div class="text-center p-6">
                <div class="w-16 h-16 mx-auto rounded-full flex items-center justify-center mb-4" style="background: rgba(var(--color-primary-rgb), 0.1);">
                    <i class="fas fa-gem text-2xl" style="color: var(--color-primary);"></i>
                </div>
                <h3 class="text-xl font-bold mb-2" style="color: var(--text-primary);">{{ $isEnglish ? 'Excellence' : 'Keunggulan' }}</h3>
                <p class="text-sm" style="color: var(--text-secondary);">
                    {{ $isEnglish 
                        ? 'Commitment to highest quality standards'
                        : 'Komitmen terhadap standar kualitas tertinggi' }}
                </p>
            </div>
            
            <!-- Value 3 -->
            <div class="text-center p-6">
                <div class="w-16 h-16 mx-auto rounded-full flex items-center justify-center mb-4" style="background: rgba(var(--color-primary-rgb), 0.1);">
                    <i class="fas fa-heart text-2xl" style="color: var(--color-primary);"></i>
                </div>
                <h3 class="text-xl font-bold mb-2" style="color: var(--text-primary);">{{ $isEnglish ? 'Client-Centric' : 'Berpusat pada Klien' }}</h3>
                <p class="text-sm" style="color: var(--text-secondary);">
                    {{ $isEnglish 
                        ? 'Your success is our ultimate goal'
                        : 'Kesuksesan Anda adalah tujuan utama kami' }}
                </p>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="section" style="background: linear-gradient(135deg, var(--color-primary) 0%, var(--color-primary-dark) 100%);">
    <div class="container-wide">
        <div class="max-w-3xl mx-auto text-center text-white">
            <h2 class="text-3xl md:text-4xl font-bold mb-6">
                {{ $isEnglish ? 'Let\'s Work Together' : 'Mari Bekerja Sama' }}
            </h2>
            <p class="text-xl opacity-90 mb-8">
                {{ $isEnglish 
                    ? 'Ready to simplify your business permits? Get in touch with our team today.'
                    : 'Siap menyederhanakan perizinan usaha Anda? Hubungi tim kami hari ini.' }}
            </p>
            <div class="flex flex-wrap justify-center gap-4">
                <a href="{{ route('landing.service-inquiry.create') }}" class="btn bg-white text-[color:var(--color-primary)] hover:bg-gray-100">
                    <i class="fas fa-comments mr-2"></i>
                    {{ $isEnglish ? 'Contact Us' : 'Hubungi Kami' }}
                </a>
                <a href="{{ $isEnglish ? route('services.index.en') : route('services.index.id') }}" class="btn border-2 border-white text-white hover:bg-white/10">
                    <i class="fas fa-list mr-2"></i>
                    {{ $isEnglish ? 'Our Services' : 'Layanan Kami' }}
                </a>
            </div>
        </div>
    </div>
</section>
@endsection
