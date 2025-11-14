{{-- Hero Section --}}
<section id="home" class="relative overflow-hidden bg-gradient-to-b from-white via-white to-slate-50 pt-32 pb-28 lg:pt-40 lg:pb-32">
    <div class="absolute inset-0 pointer-events-none">
        <div class="absolute -top-24 right-6 w-64 h-64 bg-primary/10 rounded-full blur-3xl"></div>
        <div class="absolute bottom-4 left-1/2 -translate-x-1/2 w-[75%] h-56 bg-gradient-to-t from-primary/5 via-white to-transparent"></div>
        <div class="absolute -bottom-32 -right-16 w-72 h-72 bg-secondary/10 rounded-full blur-3xl"></div>
    </div>

    <div class="container-wide relative z-10">
        <div class="grid lg:grid-cols-2 gap-16 items-center">
            <div class="space-y-8" data-aos="fade-up">
                <span class="pill pill-brand">
                    Bizmark.ID
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    Permit Bureau
                </span>
                <div class="space-y-6">
                    <h1 class="text-4xl lg:text-[3.4rem] font-semibold leading-tight text-slate-900">
                        Arsip, regulasi, dan koordinasi perizinan berpadu dalam satu ekosistem.
                    </h1>
                    <p class="text-lg text-slate-500 leading-relaxed max-w-2xl">
                        Kami mengelola LB3, AMDAL, UKL-UPL, OSS, dan legalitas lain dengan SOP terdokumentasi sehingga pimpinan selalu punya visibilitas penuh.
                    </p>
                </div>
                <div class="grid grid-cols-3 gap-6" role="list" aria-label="Ringkasan capaian Bizmark.ID">
                    <div class="metric-card" role="listitem">
                        <p class="metric-value">138</p>
                        <p class="metric-label">Project</p>
                    </div>
                    <div class="metric-card" role="listitem">
                        <p class="metric-value">96%</p>
                        <p class="metric-label">SLA Tepat</p>
                    </div>
                    <div class="metric-card" role="listitem">
                        <p class="metric-value">18</p>
                        <p class="metric-label">Provinsi</p>
                    </div>
                </div>
                {{-- Primary & Secondary CTAs --}}
                <div class="space-y-5">
                    <div class="flex items-center gap-4 flex-wrap">
                        <a href="https://wa.me/6283879602855?text=Halo%20Bizmark.ID,%20saya%20ingin%20mendapatkan%20analisis%20perizinan"
                           class="btn btn-primary group"
                           data-cta="hero_whatsapp"
                           onclick="trackEvent('CTA', 'click', 'hero_whatsapp_primary')">
                            <i class="fab fa-whatsapp text-base"></i>
                            <span>Konsultasi Gratis</span>
                            <i class="fas fa-arrow-right text-xs opacity-0 group-hover:opacity-100 transition-opacity"></i>
                        </a>
                        <a href="#services"
                           class="btn btn-secondary"
                           data-cta="hero_services"
                           onclick="trackEvent('CTA', 'click', 'hero_services_secondary')">
                            <span>Lihat Layanan</span>
                            <i class="fas fa-chevron-down text-xs"></i>
                        </a>
                        <a href="{{ route('career.index') }}"
                           class="btn btn-outline"
                           data-cta="hero_career"
                           onclick="trackEvent('CTA', 'click', 'hero_career_tertiary')">
                            <i class="fas fa-briefcase text-xs"></i>
                            <span>Karir</span>
                        </a>
                    </div>
                    <div class="flex items-center gap-3 text-sm text-slate-500">
                        <div class="flex items-center gap-2">
                            <div class="flex -space-x-2">
                                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-blue-400 to-blue-600 border-2 border-white flex items-center justify-center text-xs text-white font-semibold">
                                    A
                                </div>
                                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-emerald-400 to-emerald-600 border-2 border-white flex items-center justify-center text-xs text-white font-semibold">
                                    B
                                </div>
                                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-purple-400 to-purple-600 border-2 border-white flex items-center justify-center text-xs text-white font-semibold">
                                    C
                                </div>
                            </div>
                            <span class="font-medium text-slate-700">23+ klien aktif bulan ini</span>
                        </div>
                        <span class="text-slate-300">•</span>
                        <a href="tel:+6283879602855" 
                           class="hover:text-primary transition font-medium"
                           onclick="trackEvent('Engagement', 'phone_click', 'hero_phone')">
                            +62 838 7960 2855
                        </a>
                    </div>
                </div>
            </div>

            <div class="relative" data-aos="fade-left" data-aos-delay="120">
                <div class="rounded-[2.5rem] border border-gray-100 bg-white shadow-[0_35px_120px_rgba(15,23,42,0.15)] overflow-hidden">
                    <picture>
                        <source srcset="https://images.pexels.com/photos/3184291/pexels-photo-3184291.jpeg?auto=compress&cs=tinysrgb&w=800&fm=webp 800w,
                                        https://images.pexels.com/photos/3184291/pexels-photo-3184291.jpeg?auto=compress&cs=tinysrgb&w=1200&fm=webp 1200w,
                                        https://images.pexels.com/photos/3184291/pexels-photo-3184291.jpeg?auto=compress&cs=tinysrgb&w=1400&fm=webp 1400w"
                                type="image/webp"
                                sizes="(min-width: 1024px) 560px, 100vw">
                        <source srcset="https://images.pexels.com/photos/3184291/pexels-photo-3184291.jpeg?auto=compress&cs=tinysrgb&w=800 800w,
                                        https://images.pexels.com/photos/3184291/pexels-photo-3184291.jpeg?auto=compress&cs=tinysrgb&w=1200 1200w,
                                        https://images.pexels.com/photos/3184291/pexels-photo-3184291.jpeg?auto=compress&cs=tinysrgb&w=1400 1400w"
                                type="image/jpeg"
                                sizes="(min-width: 1024px) 560px, 100vw">
                        <img src="https://images.pexels.com/photos/3184291/pexels-photo-3184291.jpeg?auto=compress&cs=tinysrgb&w=1400"
                             alt="Tim konsultan Bizmark.ID sedang membahas kepatuhan"
                             class="w-full h-full object-cover"
                             loading="eager"
                             decoding="async"
                             fetchpriority="high"
                             width="560"
                             height="560">
                    </picture>
                </div>
                <div class="absolute -bottom-8 left-1/2 -translate-x-1/2 w-[90%] rounded-3xl bg-white shadow-xl border border-gray-100 px-8 py-6 flex flex-wrap gap-4 items-center justify-between">
                    <div>
                        <p class="text-xs uppercase tracking-[0.35em] text-slate-400">Pipeline</p>
                        <p class="text-lg font-semibold text-slate-900">UKL-UPL • OSS • SLF</p>
                    </div>
                    <div class="text-right">
                        <p class="text-xs uppercase tracking-[0.35em] text-slate-400">Progress</p>
                        <p class="text-xl font-semibold text-green-600">89% selesai</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Trust Signals Bar --}}
<section class="py-8 border-y border-slate-100 bg-white">
    <div class="container-wide">
        <div class="flex flex-wrap items-center justify-center gap-x-12 gap-y-6 text-sm" data-aos="fade-up">
            {{-- Certification Badge --}}
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center">
                    <i class="fas fa-certificate text-white text-lg"></i>
                </div>
                <div>
                    <p class="font-semibold text-slate-900">ISO 9001:2015</p>
                    <p class="text-xs text-slate-500">Certified Quality</p>
                </div>
            </div>
            
            {{-- Award Badge --}}
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-amber-400 to-amber-600 flex items-center justify-center">
                    <i class="fas fa-award text-white text-lg"></i>
                </div>
                <div>
                    <p class="font-semibold text-slate-900">Top Rated 2024</p>
                    <p class="text-xs text-slate-500">Client Excellence</p>
                </div>
            </div>
            
            {{-- SLA Guarantee --}}
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-emerald-500 to-emerald-600 flex items-center justify-center">
                    <i class="fas fa-shield-check text-white text-lg"></i>
                </div>
                <div>
                    <p class="font-semibold text-slate-900">96% SLA</p>
                    <p class="text-xs text-slate-500">On-Time Delivery</p>
                </div>
            </div>
            
            {{-- Real-time Activity --}}
            <div class="flex items-center gap-3">
                <div class="relative">
                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-purple-500 to-purple-600 flex items-center justify-center">
                        <i class="fas fa-users text-white text-lg"></i>
                    </div>
                    <span class="absolute -top-1 -right-1 flex h-3 w-3">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-3 w-3 bg-emerald-500"></span>
                    </span>
                </div>
                <div>
                    <p class="font-semibold text-slate-900">23 Active Projects</p>
                    <p class="text-xs text-slate-500">This Month</p>
                </div>
            </div>
            
            {{-- Government Partnership --}}
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-red-500 to-red-600 flex items-center justify-center">
                    <i class="fas fa-landmark text-white text-lg"></i>
                </div>
                <div>
                    <p class="font-semibold text-slate-900">Gov. Partner</p>
                    <p class="text-xs text-slate-500">Verified Consultant</p>
                </div>
            </div>
        </div>
    </div>
</section>
