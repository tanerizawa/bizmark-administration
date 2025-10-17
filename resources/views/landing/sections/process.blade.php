<!-- Process Section -->
<section id="process" class="section bg-white">
    <div class="container">
        <div class="text-center mb-16" data-aos="fade-up">
            <span class="section-label">
                {{ app()->getLocale() == 'id' ? 'Cara Kerja Kami' : 'How We Work' }}
            </span>
            <h2 class="section-title mb-6">
                {{ app()->getLocale() == 'id' ? 'Proses Perizinan yang Mudah & Transparan' : 'Easy & Transparent Permit Process' }}
            </h2>
            <p class="section-description max-w-3xl mx-auto">
                {{ app()->getLocale() == 'id' ? 'Kami memastikan proses perizinan Anda berjalan lancar dari awal hingga akhir' : 'We ensure your permit process runs smoothly from start to finish' }}
            </p>
        </div>
        
        <div class="max-w-5xl mx-auto relative">
            <!-- Connection Line (desktop only) -->
            <div class="hidden md:block absolute inset-x-8 top-10 h-0.5 bg-gradient-to-r from-primary via-secondary to-accent" style="z-index: 0;"></div>

            <div class="grid md:grid-cols-5 gap-6 lg:gap-8 relative z-10">

            <!-- Step 1 -->
            <div class="text-center timeline-step relative z-10" data-aos="fade-up" data-aos-delay="0">
                <div class="w-16 h-16 bg-gradient-to-br from-primary to-primary-dark rounded-full flex items-center justify-center text-xl font-bold mx-auto mb-3 text-white">
                    1
                </div>
                <h4 class="text-xl font-bold mb-2 text-gray-900">
                    {{ app()->getLocale() == 'id' ? 'Konsultasi' : 'Consultation' }}
                </h4>
                <p class="text-gray-600 text-sm md:text-base leading-relaxed">
                    {{ app()->getLocale() == 'id' ? 'Diskusi kebutuhan dan analisis dokumen awal' : 'Discuss needs and initial document analysis' }}
                </p>
            </div>
            
            <!-- Step 2 -->
            <div class="text-center timeline-step relative z-10" data-aos="fade-up" data-aos-delay="100">
                <div class="w-16 h-16 bg-gradient-to-br from-secondary to-secondary-dark rounded-full flex items-center justify-center text-xl font-bold mx-auto mb-3 text-white">
                    2
                </div>
                <h4 class="text-xl font-bold mb-2 text-gray-900">
                    {{ app()->getLocale() == 'id' ? 'Penyiapan' : 'Preparation' }}
                </h4>
                <p class="text-gray-600 text-sm md:text-base leading-relaxed">
                    {{ app()->getLocale() == 'id' ? 'Pengumpulan data dan persiapan dokumen' : 'Data collection and document preparation' }}
                </p>
            </div>
            
            <!-- Step 3 -->
            <div class="text-center timeline-step relative z-10" data-aos="fade-up" data-aos-delay="200">
                <div class="w-16 h-16 bg-gradient-to-br from-purple-500 to-purple-700 rounded-full flex items-center justify-center text-xl font-bold mx-auto mb-3 text-white">
                    3
                </div>
                <h4 class="text-xl font-bold mb-2 text-gray-900">
                    {{ app()->getLocale() == 'id' ? 'Pengajuan' : 'Submission' }}
                </h4>
                <p class="text-gray-600 text-sm md:text-base leading-relaxed">
                    {{ app()->getLocale() == 'id' ? 'Submit dokumen ke instansi terkait' : 'Submit documents to relevant agencies' }}
                </p>
            </div>
            
            <!-- Step 4 -->
            <div class="text-center timeline-step relative z-10" data-aos="fade-up" data-aos-delay="300">
                <div class="w-16 h-16 bg-gradient-to-br from-accent to-accent-dark rounded-full flex items-center justify-center text-xl font-bold mx-auto mb-3 text-white">
                    4
                </div>
                <h4 class="text-xl font-bold mb-2 text-gray-900">
                    {{ app()->getLocale() == 'id' ? 'Monitoring' : 'Monitoring' }}
                </h4>
                <p class="text-gray-600 text-sm md:text-base leading-relaxed">
                    {{ app()->getLocale() == 'id' ? 'Update progress secara berkala' : 'Regular progress updates' }}
                </p>
            </div>
            
            <!-- Step 5 -->
            <div class="text-center timeline-step relative z-10" data-aos="fade-up" data-aos-delay="400">
                <div class="w-16 h-16 bg-gradient-to-br from-cyan-500 to-blue-600 rounded-full flex items-center justify-center text-xl font-bold mx-auto mb-3 text-white">
                    5
                </div>
                <h4 class="text-xl font-bold mb-2 text-gray-900">
                    {{ app()->getLocale() == 'id' ? 'Selesai' : 'Complete' }}
                </h4>
                <p class="text-gray-600 text-sm md:text-base leading-relaxed">
                    {{ app()->getLocale() == 'id' ? 'Izin terbit dan dokumentasi lengkap' : 'Permit issued with complete documentation' }}
                </p>
            </div>
            </div>
        </div>
        
        <!-- CTA Button -->
        <div class="text-center mt-12" data-aos="fade-up" data-aos-delay="500">
            <a href="#contact" class="btn btn-primary">
                <i class="fas fa-comments"></i>
                {{ app()->getLocale() == 'id' ? 'Mulai Konsultasi Sekarang' : 'Start Consultation Now' }}
            </a>
        </div>
    </div>
</section>
