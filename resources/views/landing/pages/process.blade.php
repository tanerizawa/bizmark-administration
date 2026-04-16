@extends('landing.layout')

@php
    $isEnglish = ($locale ?? app()->getLocale()) === 'en';
    $pageTitle = $isEnglish ? 'Our Work Process' : 'Proses Kerja Kami';
    $pageDescription = $isEnglish 
        ? 'Learn about our systematic 6-step business permit consultation process. From discovery to ongoing support, we ensure your permits are handled professionally.'
        : 'Pelajari proses konsultasi perizinan usaha kami yang sistematis dalam 6 langkah. Dari analisis kebutuhan hingga dukungan berkelanjutan, kami pastikan perizinan Anda ditangani secara profesional.';
@endphp

@section('title', $pageTitle . ' - Bizmark.ID')
@section('meta_description', $pageDescription)

@section('structured_data')
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@type": "HowTo",
    "name": "{{ $pageTitle }}",
    "description": "{{ $pageDescription }}",
    "step": [
        {
            "@@type": "HowToStep",
            "name": "{{ __('investment.process.discovery.title') }}",
            "text": "{{ __('investment.process.discovery.description') }}"
        },
        {
            "@@type": "HowToStep",
            "name": "{{ __('investment.process.roadmap.title') }}",
            "text": "{{ __('investment.process.roadmap.description') }}"
        },
        {
            "@@type": "HowToStep",
            "name": "{{ __('investment.process.preparation.title') }}",
            "text": "{{ __('investment.process.preparation.description') }}"
        },
        {
            "@@type": "HowToStep",
            "name": "{{ __('investment.process.liaison.title') }}",
            "text": "{{ __('investment.process.liaison.description') }}"
        },
        {
            "@@type": "HowToStep",
            "name": "{{ __('investment.process.monitoring.title') }}",
            "text": "{{ __('investment.process.monitoring.description') }}"
        },
        {
            "@@type": "HowToStep",
            "name": "{{ __('investment.process.support.title') }}",
            "text": "{{ __('investment.process.support.description') }}"
        }
    ]
}
</script>
@endsection

@section('content')
<!-- Hero Section -->
<section class="relative overflow-hidden pt-24 pb-16" style="background: linear-gradient(135deg, var(--surface-warm) 0%, var(--surface-secondary) 100%);">
    <div class="container-wide">
        <div class="max-w-3xl mx-auto text-center">
            <span class="section-badge mb-4">{{ __('landing.process.badge') }}</span>
            <h1 class="text-4xl md:text-5xl font-bold mb-6" style="color: var(--text-primary);">
                {{ $isEnglish ? 'How We Work' : 'Cara Kami Bekerja' }}
            </h1>
            <p class="text-xl leading-relaxed mb-8" style="color: var(--text-secondary);">
                {{ $isEnglish 
                    ? 'A systematic and transparent approach to ensure your business permits are processed efficiently and professionally.' 
                    : 'Pendekatan sistematis dan transparan untuk memastikan perizinan usaha Anda diproses secara efisien dan profesional.' }}
            </p>
            <div class="flex flex-wrap justify-center gap-4">
                <a href="{{ $isEnglish ? route('landing.service-inquiry.create') : route('landing.service-inquiry.create') }}" class="btn btn-primary">
                    <i class="fas fa-paper-plane mr-2"></i>
                    {{ $isEnglish ? 'Start Consultation' : 'Mulai Konsultasi' }}
                </a>
                <a href="{{ $isEnglish ? route('services.index.en') : route('services.index.id') }}" class="btn btn-outline-primary">
                    <i class="fas fa-list mr-2"></i>
                    {{ $isEnglish ? 'View Services' : 'Lihat Layanan' }}
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Process Steps Section -->
<section class="section" style="background: var(--surface-primary);">
    <div class="container-wide">
        <div class="max-w-4xl mx-auto">
            <!-- Timeline -->
            <div class="relative">
                <!-- Vertical Line -->
                <div class="hidden md:block absolute left-8 top-0 bottom-0 w-0.5" style="background: var(--border-light);"></div>
                
                @foreach(['discovery', 'roadmap', 'preparation', 'liaison', 'monitoring', 'support'] as $index => $step)
                <div class="flex gap-6 md:gap-8 mb-12 last:mb-0 animate-fade-in" style="animation-delay: {{ $index * 100 }}ms;">
                    <!-- Step Number -->
                    <div class="flex-shrink-0 relative z-10">
                        <div class="w-16 h-16 rounded-2xl flex items-center justify-center text-xl font-bold text-white shadow-lg" 
                             style="background: linear-gradient(135deg, var(--color-primary) 0%, var(--color-primary-dark) 100%);">
                            {{ $index + 1 }}
                        </div>
                    </div>
                    
                    <!-- Content -->
                    <div class="flex-1 pb-8 {{ $loop->last ? '' : 'border-b' }}" style="border-color: var(--border-light);">
                        <h3 class="text-2xl font-bold mb-3" style="color: var(--text-primary);">
                            {{ __("investment.process.{$step}.title") }}
                        </h3>
                        <p class="text-lg leading-relaxed mb-6" style="color: var(--text-secondary);">
                            {{ __("investment.process.{$step}.description") }}
                        </p>
                        
                        <!-- Deliverables -->
                        <div class="magazine-card p-5">
                            <h4 class="text-sm font-bold uppercase tracking-wider mb-4" style="color: var(--text-tertiary);">
                                {{ $isEnglish ? 'What You Get' : 'Yang Anda Dapatkan' }}
                            </h4>
                            <div class="grid sm:grid-cols-2 gap-3">
                                @foreach(__("investment.process.{$step}.deliverables") as $deliverable)
                                <div class="flex items-start gap-3">
                                    <div class="flex-shrink-0 w-6 h-6 rounded-full flex items-center justify-center" style="background: rgba(22, 163, 74, 0.1);">
                                        <i class="fas fa-check text-xs" style="color: var(--color-success);"></i>
                                    </div>
                                    <span class="text-sm" style="color: var(--text-secondary);">{{ $deliverable }}</span>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</section>

<!-- Why Our Process Section -->
<section class="section" style="background: var(--surface-warm);">
    <div class="container-wide">
        <div class="text-center mb-12">
            <h2 class="section-title mb-4">
                {{ $isEnglish ? 'Why Our Process Works' : 'Mengapa Proses Kami Efektif' }}
            </h2>
            <p class="section-description mx-auto">
                {{ $isEnglish 
                    ? 'Our methodology has been refined through hundreds of successful permit applications'
                    : 'Metodologi kami telah disempurnakan melalui ratusan aplikasi perizinan yang berhasil' }}
            </p>
        </div>
        
        <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Benefit 1 -->
            <div class="magazine-card p-6 text-center hover:shadow-lg transition-shadow">
                <div class="w-14 h-14 mx-auto rounded-xl flex items-center justify-center mb-4" style="background: linear-gradient(135deg, #10b981, #059669);">
                    <i class="fas fa-clock text-2xl text-white"></i>
                </div>
                <h3 class="text-lg font-bold mb-2" style="color: var(--text-primary);">
                    {{ $isEnglish ? 'Time Efficient' : 'Hemat Waktu' }}
                </h3>
                <p class="text-sm" style="color: var(--text-secondary);">
                    {{ $isEnglish 
                        ? 'Streamlined process reduces permit processing time by up to 40%'
                        : 'Proses yang efisien mengurangi waktu pengurusan izin hingga 40%' }}
                </p>
            </div>
            
            <!-- Benefit 2 -->
            <div class="magazine-card p-6 text-center hover:shadow-lg transition-shadow">
                <div class="w-14 h-14 mx-auto rounded-xl flex items-center justify-center mb-4" style="background: linear-gradient(135deg, #3b82f6, #1d4ed8);">
                    <i class="fas fa-eye text-2xl text-white"></i>
                </div>
                <h3 class="text-lg font-bold mb-2" style="color: var(--text-primary);">
                    {{ $isEnglish ? 'Full Transparency' : 'Transparansi Penuh' }}
                </h3>
                <p class="text-sm" style="color: var(--text-secondary);">
                    {{ $isEnglish 
                        ? 'Real-time updates and progress tracking at every stage'
                        : 'Update real-time dan pelacakan progres di setiap tahap' }}
                </p>
            </div>
            
            <!-- Benefit 3 -->
            <div class="magazine-card p-6 text-center hover:shadow-lg transition-shadow">
                <div class="w-14 h-14 mx-auto rounded-xl flex items-center justify-center mb-4" style="background: linear-gradient(135deg, #8b5cf6, #6366f1);">
                    <i class="fas fa-shield-alt text-2xl text-white"></i>
                </div>
                <h3 class="text-lg font-bold mb-2" style="color: var(--text-primary);">
                    {{ $isEnglish ? 'Risk Mitigation' : 'Mitigasi Risiko' }}
                </h3>
                <p class="text-sm" style="color: var(--text-secondary);">
                    {{ $isEnglish 
                        ? 'Proactive identification and resolution of potential issues'
                        : 'Identifikasi dan penyelesaian proaktif terhadap potensi masalah' }}
                </p>
            </div>
            
            <!-- Benefit 4 -->
            <div class="magazine-card p-6 text-center hover:shadow-lg transition-shadow">
                <div class="w-14 h-14 mx-auto rounded-xl flex items-center justify-center mb-4" style="background: linear-gradient(135deg, #f59e0b, #d97706);">
                    <i class="fas fa-headset text-2xl text-white"></i>
                </div>
                <h3 class="text-lg font-bold mb-2" style="color: var(--text-primary);">
                    {{ $isEnglish ? 'Dedicated Support' : 'Dukungan Khusus' }}
                </h3>
                <p class="text-sm" style="color: var(--text-secondary);">
                    {{ $isEnglish 
                        ? 'Personal consultant assigned to your project from start to finish'
                        : 'Konsultan personal ditugaskan untuk proyek Anda dari awal hingga selesai' }}
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
                {{ $isEnglish ? 'Ready to Start Your Permit Journey?' : 'Siap Memulai Perjalanan Perizinan Anda?' }}
            </h2>
            <p class="text-xl opacity-90 mb-8">
                {{ $isEnglish 
                    ? 'Get a free consultation and discover how we can help streamline your business permits'
                    : 'Dapatkan konsultasi gratis dan temukan bagaimana kami dapat membantu mempercepat perizinan usaha Anda' }}
            </p>
            <div class="flex flex-wrap justify-center gap-4">
                <a href="{{ route('landing.service-inquiry.create') }}" class="btn bg-white text-[color:var(--color-primary)] hover:bg-gray-100">
                    <i class="fas fa-comments mr-2"></i>
                    {{ $isEnglish ? 'Free Consultation' : 'Konsultasi Gratis' }}
                </a>
                @php
                    $contact = config('landing_metrics.contact', []);
                    $whatsappLink = $contact['whatsapp_link'] ?? 'https://wa.me/6283879602855';
                @endphp
                <a href="{{ $whatsappLink }}" target="_blank" class="btn bg-green-500 text-white hover:bg-green-600">
                    <i class="fab fa-whatsapp mr-2"></i>
                    WhatsApp
                </a>
            </div>
        </div>
    </div>
</section>
@endsection
