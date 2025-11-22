@extends('landing.layout')

@section('title', $title ?? 'Layanan Kami - Bizmark.ID')
@section('meta_description', $meta_description ?? 'Layanan lengkap perizinan industri dan konsultasi lingkungan')

@section('content')

<!-- Hero Section -->
<section class="section bg-gradient-to-br from-primary/5 via-white to-secondary/5 pt-32 pb-16">
    <div class="container">
        <div class="max-w-4xl mx-auto text-center" data-aos="fade-up">
            <span class="inline-block px-4 py-2 bg-primary/10 text-primary rounded-full text-sm font-semibold mb-4">
                <i class="fas fa-briefcase mr-2"></i>Layanan Profesional
            </span>
            <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold mb-6 text-gray-900">
                Layanan Perizinan & Konsultasi Lingkungan
            </h1>
            <p class="text-lg md:text-xl text-gray-600 leading-relaxed mb-8">
                Solusi lengkap untuk semua kebutuhan perizinan industri Anda. Tim berpengalaman siap membantu dari konsultasi hingga izin terbit.
            </p>
            
            <!-- Quick Stats -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-6 mt-12">
                <div class="text-center" data-aos="fade-up" data-aos-delay="100">
                    <div class="text-3xl md:text-4xl font-bold text-primary mb-2">10+</div>
                    <div class="text-sm text-gray-600">Tahun Pengalaman</div>
                </div>
                <div class="text-center" data-aos="fade-up" data-aos-delay="200">
                    <div class="text-3xl md:text-4xl font-bold text-secondary mb-2">500+</div>
                    <div class="text-sm text-gray-600">Klien Puas</div>
                </div>
                <div class="text-center" data-aos="fade-up" data-aos-delay="300">
                    <div class="text-3xl md:text-4xl font-bold text-accent mb-2">98%</div>
                    <div class="text-sm text-gray-600">Tingkat Sukses</div>
                </div>
                <div class="text-center" data-aos="fade-up" data-aos-delay="400">
                    <div class="text-3xl md:text-4xl font-bold text-purple-600 mb-2">8</div>
                    <div class="text-sm text-gray-600">Layanan Unggulan</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Services Grid Section -->
<section class="section bg-white">
    <div class="container">
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6 lg:gap-8">
            @foreach($services as $slug => $service)
            <div class="card group hover:shadow-2xl transition-all duration-300" data-aos="fade-up" data-aos-delay="{{ $loop->index * 100 }}">
                <!-- Icon Header -->
                <div class="flex items-start justify-between mb-4">
                    <div class="w-16 h-16 rounded-2xl flex items-center justify-center group-hover:scale-110 transition-transform duration-300" style="background: linear-gradient(135deg, {{ $service['color'] }}15 0%, {{ $service['color'] }}30 100%);">
                        <i class="fas {{ $service['icon'] }} text-3xl" style="color: {{ $service['color'] }};"></i>
                    </div>
                    <span class="px-3 py-1 bg-gray-100 text-gray-600 text-xs font-semibold rounded-full">
                        Profesional
                    </span>
                </div>
                
                <!-- Content -->
                <h3 class="text-xl font-bold mb-3 text-gray-900 group-hover:text-primary transition-colors">
                    {{ $service['title'] }}
                </h3>
                <p class="text-gray-600 mb-6 leading-relaxed">
                    {{ $service['short_description'] }}
                </p>
                
                <!-- CTA Button -->
                <a href="{{ route('services.show', $slug) }}" class="inline-flex items-center text-primary font-semibold hover:gap-3 gap-2 transition-all group">
                    <span>Pelajari Lebih Lanjut</span>
                    <i class="fas fa-arrow-right group-hover:translate-x-1 transition-transform"></i>
                </a>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- Why Choose Us Section -->
<section class="section bg-gray-50">
    <div class="container">
        <div class="text-center mb-12" data-aos="fade-up">
            <h2 class="section-title mb-6">Mengapa Memilih Bizmark.ID?</h2>
            <p class="section-description max-w-3xl mx-auto">
                Kami berkomitmen memberikan layanan terbaik dengan standar profesional tinggi
            </p>
        </div>
        
        <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="bg-white rounded-2xl p-6 text-center" data-aos="fade-up" data-aos-delay="0">
                <div class="w-14 h-14 rounded-full mx-auto mb-4 flex items-center justify-center" style="background: linear-gradient(135deg, #1E40AF 0%, #1E3A8A 100%);">
                    <i class="fas fa-certificate text-white text-xl"></i>
                </div>
                <h4 class="font-bold text-gray-900 mb-2">Tim Bersertifikat</h4>
                <p class="text-sm text-gray-600">Konsultan tersertifikasi dan berpengalaman</p>
            </div>
            
            <div class="bg-white rounded-2xl p-6 text-center" data-aos="fade-up" data-aos-delay="100">
                <div class="w-14 h-14 rounded-full mx-auto mb-4 flex items-center justify-center" style="background: linear-gradient(135deg, #DC2626 0%, #991B1B 100%);">
                    <i class="fas fa-clock text-white text-xl"></i>
                </div>
                <h4 class="font-bold text-gray-900 mb-2">Proses Cepat</h4>
                <p class="text-sm text-gray-600">Pengurusan efisien dan tepat waktu</p>
            </div>
            
            <div class="bg-white rounded-2xl p-6 text-center" data-aos="fade-up" data-aos-delay="200">
                <div class="w-14 h-14 rounded-full mx-auto mb-4 flex items-center justify-center" style="background: linear-gradient(135deg, #9333EA 0%, #6B21A8 100%);">
                    <i class="fas fa-handshake text-white text-xl"></i>
                </div>
                <h4 class="font-bold text-gray-900 mb-2">Transparansi</h4>
                <p class="text-sm text-gray-600">Update progress secara berkala</p>
            </div>
            
            <div class="bg-white rounded-2xl p-6 text-center" data-aos="fade-up" data-aos-delay="300">
                <div class="w-14 h-14 rounded-full mx-auto mb-4 flex items-center justify-center" style="background: linear-gradient(135deg, #F59E0B 0%, #D97706 100%);">
                    <i class="fas fa-shield-alt text-white text-xl"></i>
                </div>
                <h4 class="font-bold text-gray-900 mb-2">Garansi Kepuasan</h4>
                <p class="text-sm text-gray-600">Komitmen hasil terbaik untuk klien</p>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="section bg-gradient-to-br from-primary to-primary-dark text-white">
    <div class="container">
        <div class="max-w-4xl mx-auto text-center" data-aos="fade-up">
            <h2 class="text-3xl md:text-4xl font-bold mb-6">
                Siap Memulai Perizinan Anda?
            </h2>
            <p class="text-lg md:text-xl mb-8 text-white/90">
                Konsultasikan kebutuhan perizinan Anda dengan tim ahli kami. Gratis dan tanpa kewajiban.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="https://wa.me/6283879602855" target="_blank" class="btn bg-white text-primary hover:bg-gray-100 px-8 py-4 text-lg">
                    <i class="fab fa-whatsapp mr-2"></i>
                    Konsultasi via WhatsApp
                </a>
                <a href="tel:+6283879602855" class="btn bg-white/10 hover:bg-white/20 text-white border border-white/30 px-8 py-4 text-lg">
                    <i class="fas fa-phone mr-2"></i>
                    Telepon Kami
                </a>
            </div>
        </div>
    </div>
</section>

@include('landing.sections.footer')

@endsection
