@php
    $metrics = config('landing_metrics');
@endphp

{{-- Hero Section - Neuroscience-Based Design --}}
<section id="home" class="relative overflow-hidden pt-32 pb-20 lg:pt-40 lg:pb-32">
    {{-- Background Gradient - Soft Cream --}}
    <div class="absolute inset-0 bg-gradient-to-b from-[#FDFBF8] via-[#FDFBF8] to-[#F5F3F8] pointer-events-none"></div>
    
    {{-- Subtle Background Elements --}}
    <div class="absolute inset-0 pointer-events-none overflow-hidden">
        {{-- Soft Blue Glow (Top Right) --}}
        <div class="absolute -top-32 right-0 w-96 h-96 bg-[#5B8DBE]/8 rounded-full blur-3xl"></div>
        {{-- Soft Coral Glow (Bottom Left) --}}
        <div class="absolute -bottom-32 -left-32 w-96 h-96 bg-[#E8956F]/8 rounded-full blur-3xl"></div>
        {{-- Soft Green Glow (Center) --}}
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[400px] bg-[#7CB342]/5 rounded-full blur-3xl"></div>
    </div>

    <div class="container-wide relative z-10">
        <div class="grid lg:grid-cols-2 gap-16 items-center">
            {{-- Left Column: Content --}}
            <div class="space-y-8" data-aos="fade-up" data-aos-duration="800">
                {{-- Pill Badge --}}
                <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-[#5B8DBE]/10 border border-[#5B8DBE]/20">
                    <span class="text-sm font-semibold text-[#5B8DBE]">Bizmark.ID</span>
                    <span class="w-1.5 h-1.5 rounded-full bg-[#7CB342]"></span>
                    <span class="text-sm font-semibold text-[#5B8DBE]">Permit Bureau</span>
                </div>

                {{-- Main Heading --}}
                <div class="space-y-6">
                    <h1 class="text-5xl lg:text-6xl font-black leading-tight text-[#1A1410]" style="letter-spacing: -0.02em;">
                        Konsultan Perizinan LB3, AMDAL & UKL-UPL Terpercaya di Karawang
                    </h1>
                    <p class="text-xl text-[#6B5D52] leading-relaxed max-w-2xl font-light">
                        Kami mengelola perizinan Limbah B3, AMDAL, UKL-UPL, OSS, dan legalitas industri lainnya dengan SOP terdokumentasi sehingga pimpinan selalu punya visibilitas penuh.
                    </p>
                </div>

                {{-- Metrics - Reduced from 3 to 2 for cognitive load --}}
                <div class="grid grid-cols-2 gap-8 pt-4" role="list" aria-label="Ringkasan capaian Bizmark.ID">
                    <div class="metric-card" role="listitem">
                        <p class="text-4xl font-black text-[#5B8DBE]">{{ $metrics['display']['experience_years'] ?? '10+' }}</p>
                        <p class="text-sm font-semibold text-[#6B5D52] mt-2">Tahun Pengalaman</p>
                    </div>
                    <div class="metric-card" role="listitem">
                        <p class="text-4xl font-black text-[#7CB342]">{{ $metrics['display']['service_types'] ?? '15+ Jenis' }}</p>
                        <p class="text-sm font-semibold text-[#6B5D52] mt-2">Layanan Perizinan</p>
                    </div>
                </div>

                {{-- CTA Buttons - Simplified --}}
                <div class="space-y-4 pt-4">
                    <div class="flex items-center gap-4 flex-wrap">
                        {{-- Primary CTA --}}
                        <a href="{{ route('landing.service-inquiry.create') }}"
                           class="inline-flex items-center gap-3 px-8 py-4 bg-gradient-to-r from-[#5B8DBE] to-[#3A5D82] text-white rounded-full font-semibold hover:shadow-lg transition-all duration-300 hover:translate-y-[-2px]"
                           data-cta="hero_ai_analysis"
                           onclick="trackEvent('CTA', 'click', 'hero_ai_analysis_primary')">
                            <i class="fas fa-robot text-lg"></i>
                            <span>Analisis Izin Anda</span>
                            <i class="fas fa-arrow-right text-sm opacity-0 group-hover:opacity-100 transition-opacity"></i>
                        </a>
                        {{-- Secondary CTA --}}
                        <a href="#services"
                           class="inline-flex items-center gap-3 px-8 py-4 bg-white text-[#5B8DBE] rounded-full font-semibold border-2 border-[#5B8DBE]/20 hover:border-[#5B8DBE]/50 hover:bg-[#5B8DBE]/5 transition-all duration-300"
                           data-cta="hero_services"
                           onclick="trackEvent('CTA', 'click', 'hero_services_secondary')">
                            <span>Lihat Layanan</span>
                            <i class="fas fa-chevron-down text-sm"></i>
                        </a>
                    </div>

                    {{-- Social Proof --}}
                    <div class="flex items-center gap-4 text-sm text-[#6B5D52] pt-4">
                        <div class="flex items-center gap-3">
                            <div class="flex -space-x-2">
                                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-[#5B8DBE] to-[#3A5D82] border-2 border-white flex items-center justify-center text-xs text-white font-semibold">A</div>
                                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-[#E8956F] to-[#C96535] border-2 border-white flex items-center justify-center text-xs text-white font-semibold">B</div>
                                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-[#7CB342] to-[#5A8328] border-2 border-white flex items-center justify-center text-xs text-white font-semibold">C</div>
                            </div>
                            <span class="font-medium text-[#1A1410]">{{ $metrics['display']['platform_uptime'] ?? '24/7' }} monitoring aktif</span>
                        </div>
                        <span class="text-[#9B8B7E]">•</span>
                        <a href="tel:{{ str_replace(' ', '', $metrics['contact']['phone']) }}" 
                           class="hover:text-[#5B8DBE] transition font-semibold"
                           onclick="trackEvent('Engagement', 'phone_click', 'hero_phone')">
                            {{ $metrics['contact']['phone_display'] }}
                        </a>
                    </div>
                </div>
            </div>

            {{-- Right Column: Image --}}
            <div class="relative" data-aos="fade-left" data-aos-delay="200" data-aos-duration="800">
                <div class="rounded-3xl border border-[#5B8DBE]/10 bg-white shadow-soft-xl overflow-hidden">
                    <picture>
                        <source srcset="/images/landing/hero-800.webp 800w,
                                        /images/landing/hero-1200.webp 1200w,
                                        /images/landing/hero-1400.webp 1400w"
                                type="image/webp"
                                sizes="(min-width: 1024px) 560px, 100vw">
                        <source srcset="/images/landing/hero-800.jpg 800w,
                                        /images/landing/hero-1200.jpg 1200w,
                                        /images/landing/hero-1400.jpg 1400w"
                                type="image/jpeg"
                                sizes="(min-width: 1024px) 560px, 100vw">
                        <img src="/images/landing/hero-1400.jpg"
                             alt="Tim konsultan Bizmark.ID sedang membahas kepatuhan regulasi perizinan lingkungan"
                             class="w-full h-full object-cover"
                             loading="eager"
                             fetchpriority="high"
                             decoding="async"
                             width="560"
                             height="560">
                    </picture>
                </div>

                {{-- Info Card - Floating --}}
                <div class="absolute -bottom-6 left-1/2 -translate-x-1/2 w-[95%] rounded-2xl bg-white shadow-soft-lg border border-[#5B8DBE]/10 px-6 py-5 flex flex-col sm:flex-row gap-4 items-start sm:items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-[#9B8B7E] uppercase tracking-wider">Izin Utama</p>
                        <p class="text-base font-bold text-[#1A1410] mt-1">AMDAL • UKL-UPL • OSS</p>
                    </div>
                    <div class="text-right">
                        <p class="text-xs font-semibold text-[#9B8B7E] uppercase tracking-wider">Progress</p>
                        <p class="text-lg font-bold text-[#7CB342] mt-1">89% selesai</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Trust Signals Bar - Neuroscience Soft Colors --}}
<section class="py-12 border-y border-[#5B8DBE]/10 bg-white">
    <div class="container-wide">
        <div class="grid grid-cols-2 md:grid-cols-5 gap-8 md:gap-6" data-aos="fade-up" data-aos-duration="800">
            {{-- Years of Experience --}}
            <div class="flex flex-col items-center text-center">
                <div class="w-12 h-12 rounded-full bg-gradient-to-br from-[#5B8DBE] to-[#3A5D82] flex items-center justify-center mb-3">
                    <i class="fas fa-briefcase text-white text-lg"></i>
                </div>
                <p class="font-bold text-[#1A1410] text-lg">15+ Tahun</p>
                <p class="text-xs text-[#9B8B7E] mt-1">Pengalaman</p>
            </div>
            
            {{-- Multi-Sector Expertise --}}
            <div class="flex flex-col items-center text-center">
                <div class="w-12 h-12 rounded-full bg-gradient-to-br from-[#E8956F] to-[#C96535] flex items-center justify-center mb-3">
                    <i class="fas fa-industry text-white text-lg"></i>
                </div>
                <p class="font-bold text-[#1A1410] text-lg">15+ Sektor</p>
                <p class="text-xs text-[#9B8B7E] mt-1">Industri</p>
            </div>
            
            {{-- SLA Guarantee --}}
            <div class="flex flex-col items-center text-center">
                <div class="w-12 h-12 rounded-full bg-gradient-to-br from-[#7CB342] to-[#5A8328] flex items-center justify-center mb-3">
                    <i class="fas fa-shield-check text-white text-lg"></i>
                </div>
                <p class="font-bold text-[#1A1410] text-lg">{{ $metrics['display']['sla_rate'] }} SLA</p>
                <p class="text-xs text-[#9B8B7E] mt-1">On-Time</p>
            </div>
            
            {{-- Real-time Activity --}}
            <div class="flex flex-col items-center text-center">
                <div class="relative w-12 h-12 mb-3">
                    <div class="w-12 h-12 rounded-full bg-gradient-to-br from-[#5B8DBE] to-[#3A5D82] flex items-center justify-center">
                        <i class="fas fa-headset text-white text-lg"></i>
                    </div>
                    <span class="absolute -top-1 -right-1 flex h-3 w-3">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-[#7CB342] opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-3 w-3 bg-[#7CB342]"></span>
                    </span>
                </div>
                <p class="font-bold text-[#1A1410] text-lg">24/7 Support</p>
                <p class="text-xs text-[#9B8B7E] mt-1">Fast Response</p>
            </div>
            
            {{-- Government Partnership --}}
            <div class="flex flex-col items-center text-center">
                <div class="w-12 h-12 rounded-full bg-gradient-to-br from-[#C97C7C] to-[#A85555] flex items-center justify-center mb-3">
                    <i class="fas fa-landmark text-white text-lg"></i>
                </div>
                <p class="font-bold text-[#1A1410] text-lg">Gov. Partner</p>
                <p class="text-xs text-[#9B8B7E] mt-1">Verified</p>
            </div>
        </div>
    </div>
</section>
