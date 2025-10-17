<!-- Services Section -->
<section id="services" class="section bg-[#F7F8FC]">
    <div class="container">
        <div class="text-center mb-12" data-aos="fade-up">
            <span class="section-label">
                {{ app()->getLocale() == 'id' ? 'Layanan Kami' : 'Our Services' }}
            </span>
            <h2 class="section-title mb-6">
                {{ app()->getLocale() == 'id' ? 'Layanan Perizinan yang Kami Tawarkan' : 'Permit Services We Offer' }}
            </h2>
            <p class="section-description max-w-3xl mx-auto">
                {{ app()->getLocale() == 'id' ? 'Solusi lengkap perizinan industri untuk berbagai kebutuhan bisnis Anda' : 'Complete industrial permit solutions for all your business needs' }}
            </p>
        </div>
        
        <div class="grid md:grid-cols-2 xl:grid-cols-4 gap-6 lg:gap-8 xl:gap-10">
            <!-- Service 1: LB3 -->
            <div class="service-card h-full flex flex-col" data-aos="fade-up" data-aos-delay="0">
                <div class="service-icon">
                    <i class="fas fa-biohazard"></i>
                </div>
                <h3 class="text-xl font-semibold mb-3 text-gray-900">
                    {{ app()->getLocale() == 'id' ? 'Perizinan LB3' : 'B3 Waste Permit' }}
                </h3>
                <p class="text-gray-600 mb-5 leading-relaxed">
                    {{ app()->getLocale() == 'id' ? 'Pengelolaan dokumen Limbah Bahan Berbahaya dan Beracun sesuai regulasi terbaru' : 'Hazardous waste document management according to latest regulations' }}
                </p>
                <a href="#contact" class="underline-link font-semibold text-primary text-sm">
                    {{ app()->getLocale() == 'id' ? 'Pelajari Lebih Lanjut' : 'Learn More' }}
                </a>
            </div>
            
            <!-- Service 2: AMDAL -->
            <div class="service-card h-full flex flex-col" data-aos="fade-up" data-aos-delay="100">
                <div class="service-icon" style="background: linear-gradient(135deg, rgba(20,184,166,0.15) 0%, rgba(16,185,129,0.15) 100%);">
                    <i class="fas fa-leaf" style="color: #0FBA7D;"></i>
                </div>
                <h3 class="text-xl font-semibold mb-3 text-gray-900">AMDAL</h3>
                <p class="text-gray-600 mb-5 leading-relaxed">
                    {{ app()->getLocale() == 'id' ? 'Analisis Mengenai Dampak Lingkungan untuk proyek strategis Anda' : 'Environmental Impact Analysis for your strategic projects' }}
                </p>
                <a href="#contact" class="underline-link font-semibold text-primary text-sm">
                    {{ app()->getLocale() == 'id' ? 'Pelajari Lebih Lanjut' : 'Learn More' }}
                </a>
            </div>
            
            <!-- Service 3: UKL-UPL -->
            <div class="service-card h-full flex flex-col" data-aos="fade-up" data-aos-delay="200">
                <div class="service-icon" style="background: linear-gradient(135deg, rgba(99,102,241,0.15) 0%, rgba(129,140,248,0.15) 100%);">
                    <i class="fas fa-file-alt" style="color: #6366F1;"></i>
                </div>
                <h3 class="text-xl font-semibold mb-3 text-gray-900">UKL-UPL</h3>
                <p class="text-gray-600 mb-5 leading-relaxed">
                    {{ app()->getLocale() == 'id' ? 'Upaya Pengelolaan Lingkungan dan Pemantauan Lingkungan Hidup' : 'Environmental Management and Monitoring Efforts' }}
                </p>
                <a href="#contact" class="underline-link font-semibold text-primary text-sm">
                    {{ app()->getLocale() == 'id' ? 'Pelajari Lebih Lanjut' : 'Learn More' }}
                </a>
            </div>
            
            <!-- Service 4: OSS -->
            <div class="service-card h-full flex flex-col" data-aos="fade-up" data-aos-delay="300">
                <div class="service-icon" style="background: linear-gradient(135deg, rgba(249,115,22,0.12) 0%, rgba(234,88,12,0.12) 100%);">
                    <i class="fas fa-globe" style="color: #EA580C;"></i>
                </div>
                <h3 class="text-xl font-semibold mb-3 text-gray-900">OSS (NIB)</h3>
                <p class="text-gray-600 mb-5 leading-relaxed">
                    {{ app()->getLocale() == 'id' ? 'Online Single Submission untuk Nomor Induk Berusaha perusahaan Anda' : 'Online Single Submission for your Business Identification Number' }}
                </p>
                <a href="#contact" class="underline-link font-semibold text-primary text-sm">
                    {{ app()->getLocale() == 'id' ? 'Pelajari Lebih Lanjut' : 'Learn More' }}
                </a>
            </div>
            
            <!-- Service 5: PBG/SLF -->
            <div class="service-card h-full flex flex-col" data-aos="fade-up" data-aos-delay="400">
                <div class="service-icon" style="background: linear-gradient(135deg, rgba(13,148,136,0.12) 0%, rgba(59,130,246,0.12) 100%);">
                    <i class="fas fa-building" style="color: #0EA5E9;"></i>
                </div>
                <h3 class="text-xl font-semibold mb-3 text-gray-900">PBG / SLF</h3>
                <p class="text-gray-600 mb-5 leading-relaxed">
                    {{ app()->getLocale() == 'id' ? 'Persetujuan Bangunan Gedung dan Sertifikat Laik Fungsi bangunan' : 'Building Permit and Functional Worthiness Certificate' }}
                </p>
                <a href="#contact" class="underline-link font-semibold text-primary text-sm">
                    {{ app()->getLocale() == 'id' ? 'Pelajari Lebih Lanjut' : 'Learn More' }}
                </a>
            </div>
            
            <!-- Service 6: Izin Operasional -->
            <div class="service-card h-full flex flex-col" data-aos="fade-up" data-aos-delay="500">
                <div class="service-icon" style="background: linear-gradient(135deg, rgba(236,72,153,0.12) 0%, rgba(244,114,182,0.12) 100%);">
                    <i class="fas fa-industry" style="color: #EC4899;"></i>
                </div>
                <h3 class="text-xl font-semibold mb-3 text-gray-900">
                    {{ app()->getLocale() == 'id' ? 'Izin Operasional' : 'Operational Permit' }}
                </h3>
                <p class="text-gray-600 mb-5 leading-relaxed">
                    {{ app()->getLocale() == 'id' ? 'Perizinan operasional pabrik dan industri manufaktur lengkap' : 'Complete operational permits for factory and manufacturing industry' }}
                </p>
                <a href="#contact" class="underline-link font-semibold text-primary text-sm">
                    {{ app()->getLocale() == 'id' ? 'Pelajari Lebih Lanjut' : 'Learn More' }}
                </a>
            </div>
            
            <!-- Service 7: Konsultan Lingkungan -->
            <div class="service-card h-full flex flex-col" data-aos="fade-up" data-aos-delay="600">
                <div class="service-icon" style="background: linear-gradient(135deg, rgba(20,184,166,0.12) 0%, rgba(52,211,153,0.12) 100%);">
                    <i class="fas fa-user-tie" style="color: #14B8A6;"></i>
                </div>
                <h3 class="text-xl font-semibold mb-3 text-gray-900">
                    {{ app()->getLocale() == 'id' ? 'Konsultan Lingkungan' : 'Environmental Consultant' }}
                </h3>
                <p class="text-gray-600 mb-5 leading-relaxed">
                    {{ app()->getLocale() == 'id' ? 'Konsultasi dan pendampingan lingkungan untuk berbagai industri' : 'Environmental consultation and support for various industries' }}
                </p>
                <a href="#contact" class="underline-link font-semibold text-primary text-sm">
                    {{ app()->getLocale() == 'id' ? 'Pelajari Lebih Lanjut' : 'Learn More' }}
                </a>
            </div>
            
            <!-- Service 8: Progress Updates -->
            <div class="service-card h-full flex flex-col" data-aos="fade-up" data-aos-delay="700">
                <div class="service-icon" style="background: linear-gradient(135deg, rgba(79,70,229,0.12) 0%, rgba(99,102,241,0.12) 100%);">
                    <i class="fas fa-chart-line" style="color: #4F46E5;"></i>
                </div>
                <h3 class="text-xl font-semibold mb-3 text-gray-900">
                    {{ app()->getLocale() == 'id' ? 'Update Berkala' : 'Regular Updates' }}
                </h3>
                <p class="text-gray-600 mb-5 leading-relaxed">
                    {{ app()->getLocale() == 'id' ? 'Dapatkan update progress perizinan Anda secara berkala via WhatsApp/Email' : 'Get regular permit progress updates via WhatsApp/Email' }}
                </p>
                <a href="#contact" class="underline-link font-semibold text-primary text-sm">
                    {{ app()->getLocale() == 'id' ? 'Pelajari Lebih Lanjut' : 'Learn More' }}
                </a>
            </div>
        </div>
    </div>
</section>
