{{-- OPTIMIZED HERO SECTION - Phase 1: Quick Wins --}}
<section id="home" class="relative min-h-[80vh] md:min-h-[85vh] flex items-center overflow-hidden pt-32 md:pt-36 pb-24">
    {{-- Simplified Background - Less visual noise --}}
    <div class="absolute inset-0 gradient-mesh opacity-70"></div>
    <div class="absolute inset-0">
        <div class="absolute top-24 left-12 w-64 h-64 bg-primary/8 rounded-full blur-3xl"></div>
        <div class="absolute bottom-24 right-16 w-80 h-80 bg-secondary/8 rounded-full blur-3xl"></div>
    </div>

    <div class="container relative z-10">
        <div class="grid lg:grid-cols-2 gap-12 lg:gap-20 items-center">

            {{-- LEFT: Content - SIMPLIFIED (12 → 5 elements) --}}
            <div class="space-y-10" data-aos="fade-up" data-aos-duration="800">

                {{-- 1. POWER HEADLINE - Maximum Contrast & Impact --}}
                <div class="space-y-4">
                    <h1 class="space-y-3">
                        {{-- Primary headline - MAX attention --}}
                        <span class="block headline-hero">
                            Perizinan Industri
                        </span>
                        {{-- Power statement - Gradient accent --}}
                        <span class="block headline-hero-accent">
                            <span class="headline-power-word">100% Legal.</span> Zero Ribet.
                        </span>
                    </h1>

                    {{-- Sub-headline - Optimized for F-pattern scanning --}}
                    <p class="text-xl md:text-2xl leading-relaxed max-w-xl" style="color: #6B5D52;">
                        <strong class="text-attention-high font-semibold">Pendampingan penuh</strong> izin
                        <strong class="text-attention-max">AMDAL, UKL-UPL, dan LB3</strong>
                        dengan <strong class="text-attention-high">monitoring digital 24/7</strong>.
                    </p>
                </div>

                {{-- 2. PRIMARY CTA - IMPOSSIBLE TO MISS --}}
                <div class="flex flex-col sm:flex-row gap-4 items-start">
                    <a href="{{ route('consultation.index') }}"
                       class="btn-primary-enhanced pulse group">
                        <span class="flex items-center justify-center h-10 w-10 rounded-full bg-white/20">
                            <i class="fas fa-calculator text-lg"></i>
                        </span>
                        <span>Estimasi Biaya Gratis</span>
                        <i class="fas fa-arrow-right text-base transition-transform group-hover:translate-x-2"></i>
                    </a>
                </div>

                {{-- 3. TRUST SIGNALS - Animated Counters + Unified Colors --}}
                <div class="grid grid-cols-3 gap-4 max-w-2xl">
                    <div class="trust-metric">
                        <div class="trust-metric-value"
                             data-counter="247"
                             data-counter-suffix="+"
                             data-counter-duration="2000"
                             data-counter-delay="200">
                            0
                        </div>
                        <div class="trust-metric-label">Klien</div>
                    </div>

                    <div class="trust-metric">
                        <div class="trust-metric-value"
                             data-counter="12"
                             data-counter-suffix="+"
                             data-counter-duration="1800"
                             data-counter-delay="400">
                            0
                        </div>
                        <div class="trust-metric-label">Tahun</div>
                    </div>

                    <div class="trust-metric">
                        <div class="flex items-center justify-center gap-1.5">
                            <i class="fas fa-star text-yellow-400 text-xl"></i>
                            <span class="trust-metric-value"
                                  data-counter="4.9"
                                  data-counter-decimals="1"
                                  data-counter-duration="1600"
                                  data-counter-delay="600">
                                0
                            </span>
                        </div>
                        <div class="trust-metric-label">Rating</div>
                    </div>
                </div>

                {{-- 4. SECONDARY CTA - WhatsApp (subtle) --}}
                <div class="flex items-center gap-4 pt-2">
                    <span class="text-sm font-medium" style="color: #9B8B7E;">Atau hubungi via:</span>
                    <a href="https://wa.me/6283879602855?text=Halo%20PT%20Cangah%20Pajaratan%20Mandiri,%20saya%20ingin%20konsultasi"
                       class="inline-flex items-center gap-2 px-5 py-3 bg-green-50 hover:bg-green-100 rounded-full border border-green-200 hover:border-green-400 transition-all duration-250 group">
                        <i class="fab fa-whatsapp text-xl text-green-600"></i>
                        <span class="text-sm font-semibold text-green-700">WhatsApp</span>
                        <i class="fas fa-arrow-right text-xs text-green-600 transition-transform group-hover:translate-x-1"></i>
                    </a>
                </div>

            </div>
            
            {{-- RIGHT: Hero Visual - SIMPLIFIED for better focus --}}
            <div class="relative hidden lg:block" data-aos="fade-left" data-aos-duration="900" data-aos-delay="200">

                {{-- Main Visual - Cleaner design --}}
                <div class="relative">
                    {{-- Professional Business Visual --}}
                    <div class="aspect-[4/3] bg-white/80 backdrop-blur-xl border-2 border-primary/15 rounded-3xl relative overflow-hidden shadow-[0_30px_70px_-40px_rgba(26,45,70,0.5)]">

                        {{-- Hero Photo --}}
                        <div class="absolute inset-0 bg-cover bg-center" style="background-image: url('https://images.pexels.com/photos/3183286/pexels-photo-3183286.jpeg?auto=compress&cs=tinysrgb&w=1400');"></div>
                        <div class="absolute inset-0 bg-gradient-to-tr from-primary/25 via-white/20 to-transparent"></div>

                        {{-- Progress Indicator - Simplified --}}
                        <div class="absolute bottom-8 left-8 right-8 bg-white/95 backdrop-blur-lg border border-primary/10 rounded-2xl p-6 shadow-2xl">
                            <div class="flex items-center justify-between gap-4">
                                <div class="flex-1">
                                    <p class="text-xs uppercase tracking-wider text-gray-400 mb-2 font-semibold">Live Progress</p>
                                    <p class="text-base font-bold text-attention-max">Izin AMDAL - PT Mandiri Jaya</p>
                                </div>
                                <div class="flex flex-col items-end gap-2">
                                    <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-green-50 border border-green-200">
                                        <span class="flex h-2 w-2 rounded-full bg-green-500 animate-pulse"></span>
                                        <span class="text-sm font-bold text-green-700">90% Selesai</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Subtle decorative glow --}}
                    <div class="absolute -z-10 -top-8 -right-8 w-64 h-64 bg-primary/12 rounded-full blur-3xl"></div>
                    <div class="absolute -z-10 -bottom-8 -left-8 w-64 h-64 bg-secondary/10 rounded-full blur-3xl"></div>
                </div>

            </div>

        </div>
    </div>

    {{-- Scroll Indicator - Cleaner design --}}
    <div class="container relative z-10 mt-12 md:mt-16">
        <div class="flex flex-col items-center gap-4">
            <a href="#services"
               class="inline-flex flex-col items-center gap-2 text-sm font-semibold text-gray-500 hover:text-attention-high transition-all duration-300 group">
                <span class="uppercase tracking-widest">Lihat Layanan</span>
                <i class="fas fa-chevron-down text-base animate-bounce group-hover:translate-y-1 transition-transform"></i>
            </a>
        </div>
    </div>
</section>
