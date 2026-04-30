    <!-- ============================================
         HERO SECTION - F-PATTERN ZONE 2
         Neuroscience Optimization:
         - F-Pattern horizontal scan zone
         - Clear visual hierarchy (headline -> CTA)
         - CTA ratio 3:1 (primary vs secondary)
         - Miller's Law: 6-8 word headline optimal
         ============================================ -->
    <section id="home" class="hero-gradient px-4" role="banner">
        <div class="container mx-auto max-w-6xl relative z-10">
            <!-- F-Pattern Zone 1: Brand Identity (Top-Left) -->
            <div class="flex flex-col md:flex-row items-center justify-between gap-12 md:gap-16">
                <!-- Left Column: Primary Content (F-Pattern Priority) -->
                <div class="md:w-1/2 text-center md:text-left space-y-8">
                    <!-- Logo positioned for F-Pattern top-left attention -->
                    <div class="logo-container" aria-label="Logo Bizmark.ID">
                        <img src="{{ asset('images/logo-bizmark.svg') }}" 
                             alt="BizMark Indonesia - Konsultan Perizinan" 
                             class="w-20 h-20 md:w-24 md:h-24 mx-auto md:mx-0 filter drop-shadow-lg"
                             loading="eager">
                    </div>
                    
                    <!-- Headline: Miller's Law (6-8 words optimal) -->
                    <h1 class="text-4xl sm:text-5xl md:text-6xl font-extrabold leading-tight">
                        Solusi <span style="background: var(--gradient-primary); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">Perizinan Digital</span><br class="hidden md:block">Terpercaya
                    </h1>
                    
                    <!-- Supporting text: Max 3 sentences (Miller's Law) -->
                    <p class="text-lg md:text-xl leading-relaxed" style="color: var(--dark-text-secondary);">
                        Platform manajemen perizinan OSS, AMDAL, UKL-UPL, PBG, SLF dengan teknologi AI. Proses cepat, transparan, dan terpercaya untuk bisnis modern Indonesia.
                    </p>
                    
                    <!-- CTA Buttons: Visual Hierarchy 3:1 ratio -->
                    <div class="flex flex-col sm:flex-row gap-4">
                        <!-- Primary CTA: Highest visual weight -->
                        <a href="/estimasi-biaya" 
                           class="btn-primary group"
                           data-neural-priority="highest" 
                           aria-label="Dapatkan estimasi biaya perizinan dengan AI">
                            <i class="fas fa-calculator mr-2 group-hover:scale-110 transition-transform" aria-hidden="true"></i>
                            <span>Estimasi Biaya AI</span>
                        </a>
                        
                        <!-- Secondary CTA: Lower visual weight -->
                        <a href="#services" 
                           class="btn-secondary"
                           data-neural-priority="medium" 
                           aria-label="Pelajari lebih lanjut tentang layanan kami">
                            <i class="fas fa-arrow-down mr-2" aria-hidden="true"></i>
                            <span>Lihat Layanan</span>
                        </a>
                    </div>
                    
                    <!-- Trust Indicators (Progressive Disclosure) -->
                    @php
                        $heroStats = config('landing_metrics.stats', [
                            'experience_label' => '10+ Tahun',
                            'clients_label'    => '500+ Klien',
                            'permits_label'    => '1.000+ Izin',
                            'success_label'    => '98%',
                        ]);
                    @endphp
                    <div class="flex flex-wrap items-center justify-center md:justify-start gap-6 text-sm" style="color: var(--dark-text-secondary);">
                        <div class="flex items-center gap-2">
                            <i class="fas fa-check-circle" style="color: var(--color-success);"></i>
                            <span>{{ $heroStats['experience_label'] }}</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <i class="fas fa-users" style="color: var(--color-primary);"></i>
                            <span>{{ $heroStats['clients_label'] }}</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <i class="fas fa-file-check" style="color: var(--color-warning);"></i>
                            <span>{{ $heroStats['permits_label'] }}</span>
                        </div>
                    </div>
                </div>
                
                <!-- Right Column: Visual Support (F-Pattern Secondary) -->
                <div class="md:w-1/2 hidden md:flex items-center justify-center">
                    <!-- Illustration or Dashboard Preview -->
                    <div class="relative w-full max-w-md">
                        <div class="section" style="background: var(--dark-bg-tertiary); padding: var(--spacing-xl);">
                            <div class="space-y-4">
                                <!-- Visual representation of value -->
                                <div class="flex items-center justify-between p-4 rounded-lg" style="background: rgba(48, 209, 88, 0.1); border: 1px solid rgba(48, 209, 88, 0.3);">
                                    <div>
                                        <div class="text-sm" style="color: var(--dark-text-secondary);">Waktu Proses</div>
                                        <div class="text-2xl font-bold" style="color: var(--color-success);">1-3 Hari</div>
                                    </div>
                                    <i class="fas fa-bolt text-3xl" style="color: var(--color-success);"></i>
                                </div>
                                
                                <div class="flex items-center justify-between p-4 rounded-lg" style="background: rgba(10, 102, 194, 0.1); border: 1px solid rgba(10, 102, 194, 0.3);">
                                    <div>
                                        <div class="text-sm" style="color: var(--dark-text-secondary);">Success Rate</div>
                                        <div class="text-2xl font-bold" style="color: var(--color-primary);">98%</div>
                                    </div>
                                    <i class="fas fa-chart-line text-3xl" style="color: var(--color-primary);"></i>
                                </div>
                                
                                <div class="flex items-center justify-between p-4 bg-purple-900/20 rounded-lg border border-purple-700/30">
                                    <div>
                                        <div class="text-sm" style="color: var(--dark-text-secondary);">Monitoring</div>
                                        <div class="text-2xl font-bold text-purple-400">Real-time</div>
                                    </div>
                                    <i class="fas fa-eye text-3xl text-purple-400"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="py-16 px-4" style="background: var(--dark-bg-secondary);" aria-label="Statistik dan Pencapaian Bizmark.ID">
        <div class="container mx-auto">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
                <div class="stat-card">
                    <div class="stat-number" aria-label="10 plus">10+</div>
                    <p class="text-lg" style="color: var(--dark-text-secondary);">Tahun Pengalaman</p>
                </div>
                <div class="stat-card">
                    <div class="stat-number" aria-label="500 plus">500+</div>
                    <p class="text-lg" style="color: var(--dark-text-secondary);">Klien Terlayani</p>
                </div>
                <div class="stat-card">
                    <div class="stat-number" aria-label="1000 plus">1000+</div>
                    <p class="text-lg" style="color: var(--dark-text-secondary);">Perizinan Selesai</p>
                </div>
                <div class="stat-card">
                    <div class="stat-number" aria-label="98 persen">98%</div>
                    <p class="text-lg" style="color: var(--dark-text-secondary);">Kepuasan Klien</p>
                </div>
            </div>
        </div>
    </section>
