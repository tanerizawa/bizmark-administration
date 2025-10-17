{{-- Hero Section - Compact & Elegant --}}
<section id="home" class="hero-gradient section pt-28">
    <div class="container-wide">
        <div class="grid md:grid-cols-2 gap-10 items-center">
            {{-- Left Content --}}
            <div class="text-content" data-aos="fade-right">
                {{-- Category Badge --}}
                <div class="inline-flex items-center gap-2 px-3.5 py-1.5 bg-primary/10 rounded-full mb-5">
                    <span class="w-2 h-2 bg-primary rounded-full animate-pulse"></span>
                    <span class="text-xs font-bold text-primary uppercase tracking-wide">
                        {{ app()->getLocale() == 'id' ? 'Konsultan Terpercaya' : 'Trusted Consultant' }}
                    </span>
                </div>
                
                {{-- Main Headline --}}
                <h1 class="text-4xl md:text-5xl font-extrabold leading-tight mb-5">
                    @if(app()->getLocale() == 'id')
                        Perizinan Industri <span class="bg-gradient-to-r from-primary to-secondary bg-clip-text text-transparent">Lebih Mudah</span>
                    @else
                        Industrial Permits <span class="bg-gradient-to-r from-primary to-secondary bg-clip-text text-transparent">Made Easy</span>
                    @endif
                </h1>
                
                {{-- Lead Paragraph --}}
                <p class="text-lg text-text-secondary leading-relaxed mb-6">
                    @if(app()->getLocale() == 'id')
                        Kami membantu perusahaan Anda mendapatkan izin LB3, AMDAL, UKL-UPL dengan proses yang cepat, transparan, dan terpercaya.
                    @else
                        We help your company obtain B3 Waste, AMDAL, UKL-UPL permits with fast, transparent, and trusted processes.
                    @endif
                </p>
                
                {{-- CTA Buttons --}}
                <div class="flex flex-wrap gap-3 mb-6">
                    <a href="#contact" class="btn btn-primary">
                        <i class="fas fa-comments mr-2"></i>
                        {{ app()->getLocale() == 'id' ? 'Konsultasi Gratis' : 'Free Consultation' }}
                    </a>
                    <a href="#services" class="btn btn-secondary">
                        <i class="fas fa-th-large mr-2"></i>
                        {{ app()->getLocale() == 'id' ? 'Lihat Layanan' : 'View Services' }}
                    </a>
                </div>
                
                {{-- Trust Indicators --}}
                <div class="flex items-center gap-6 pt-3 border-t border-border-light">
                    <div>
                        <div class="flex items-center gap-0.5 text-accent mb-0.5">
                            <i class="fas fa-star text-sm"></i>
                            <i class="fas fa-star text-sm"></i>
                            <i class="fas fa-star text-sm"></i>
                            <i class="fas fa-star text-sm"></i>
                            <i class="fas fa-star text-sm"></i>
                        </div>
                        <p class="text-xs text-text-tertiary font-medium">
                            {{ app()->getLocale() == 'id' ? 'Rating 5.0' : '5.0 Rating' }}
                        </p>
                    </div>
                    <div>
                        <p class="text-xl font-bold text-primary">10+</p>
                        <p class="text-xs text-text-tertiary font-medium">
                            {{ app()->getLocale() == 'id' ? 'Tahun Pengalaman' : 'Years Experience' }}
                        </p>
                    </div>
                    <div>
                        <p class="text-xl font-bold text-secondary">100%</p>
                        <p class="text-xs text-text-tertiary font-medium">
                            {{ app()->getLocale() == 'id' ? 'Transparansi' : 'Transparency' }}
                        </p>
                    </div>
                </div>
            </div>
            
            {{-- Right Content - Hero Image --}}
            <div class="relative" data-aos="fade-left" data-aos-delay="200">
                {{-- Placeholder untuk Hero Image/Illustration --}}
                <div class="relative rounded-2xl overflow-hidden shadow-xl bg-gradient-to-br from-primary/10 to-secondary/10 aspect-[4/3]">
                    <div class="absolute inset-0 flex items-center justify-center">
                        <div class="text-center p-6">
                            <i class="fas fa-building text-7xl text-primary/20 mb-3"></i>
                            <p class="text-text-tertiary font-medium text-sm">
                                {{ app()->getLocale() == 'id' ? 'Ilustrasi Hero akan ditempatkan di sini' : 'Hero illustration will be placed here' }}
                            </p>
                        </div>
                    </div>
                </div>
                
                {{-- Floating Stats Card --}}
                <div class="absolute -bottom-6 -left-6 bg-white rounded-xl shadow-lg p-4 border border-border-light" data-aos="zoom-in" data-aos-delay="400">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-gradient-to-br from-secondary to-secondary-dark rounded-lg flex items-center justify-center">
                            <i class="fas fa-check text-white text-xl"></i>
                        </div>
                        <div>
                            <p class="text-2xl font-bold text-primary">{{ app()->getLocale() == 'id' ? '200+' : '200+' }}</p>
                            <p class="text-sm text-text-secondary">{{ app()->getLocale() == 'id' ? 'Izin Terbit' : 'Permits Issued' }}</p>
                        </div>
                    </div>
                </div>
                
                {{-- Floating Client Card --}}
                <div class="absolute -top-6 -right-6 bg-white rounded-xl shadow-lg p-4 border border-border-light" data-aos="zoom-in" data-aos-delay="600">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-gradient-to-br from-primary to-primary-dark rounded-lg flex items-center justify-center">
                            <i class="fas fa-users text-white text-xl"></i>
                        </div>
                        <div>
                            <p class="text-2xl font-bold text-primary">{{ app()->getLocale() == 'id' ? '150+' : '150+' }}</p>
                            <p class="text-sm text-text-secondary">{{ app()->getLocale() == 'id' ? 'Klien Puas' : 'Happy Clients' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
