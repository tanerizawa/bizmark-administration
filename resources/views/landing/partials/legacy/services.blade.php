    <!-- ============================================
         SERVICES SECTION - PROGRESSIVE DISCLOSURE
         Neuroscience Optimization:
         - Initial 3 cards (Miller's Law: 7±2 chunks)
         - Additional 2 cards with progressive disclosure
         - F-Pattern layout (3-column grid)
         - Clear visual hierarchy per service
         ============================================ -->
    <section id="services" class="py-20 px-4 bg-[var(--bg-raised)]" aria-labelledby="services-heading">
        <div class="container mx-auto max-w-6xl">
            <!-- Section Header -->
            <div class="text-center mb-16">
                <h2 id="services-heading" class="text-4xl md:text-5xl font-bold mb-4 text-gray-900">
                    Layanan Kami
                </h2>
                <p class="text-xl text-gray-600">
                    Solusi lengkap untuk kebutuhan perizinan dan pengembangan bisnis Anda
                </p>
            </div>
            
            <!-- Primary Services: Always Visible (Miller's Law: 3 core items) -->
            <div class="grid md:grid-cols-3 gap-8 mb-8" data-neural-group="primary-services">
                <!-- Service 1: Manajemen Perizinan (Highest Priority) -->
                <article class="feature-card" data-neural-priority="highest">
                    <div class="feature-icon" aria-hidden="true">
                        <i class="fas fa-file-signature"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-3 text-gray-900">Manajemen Perizinan</h3>
                    <p class="text-gray-600 mb-4">
                        Pengurusan lengkap perizinan usaha dengan proses cepat, transparan, dan terpercaya. Didampingi konsultan berpengalaman.
                    </p>
                    <ul class="space-y-2 text-sm mb-5 text-gray-600">
                        <li><i class="fas fa-check mr-2 text-amber-500" aria-hidden="true"></i>OSS (Online Single Submission) & NIB</li>
                        <li><i class="fas fa-check mr-2 text-amber-500" aria-hidden="true"></i>AMDAL & UKL-UPL (Izin Lingkungan)</li>
                        <li><i class="fas fa-check mr-2 text-amber-500" aria-hidden="true"></i>Izin Lingkungan & SPPL</li>
                        <li><i class="fas fa-check mr-2 text-amber-500" aria-hidden="true"></i>PBG (Persetujuan Bangunan Gedung) & SLF</li>
                        <li><i class="fas fa-check mr-2 text-amber-500" aria-hidden="true"></i>Andalalin (Analisis Dampak Lalu Lintas)</li>
                    </ul>
                    {{-- Price anchors --}}
                    <div class="pt-4 border-t border-gray-200">
                        <p class="text-xs mb-2 font-semibold text-gray-600">Estimasi biaya mulai dari:</p>
                        <div class="flex flex-wrap gap-2 mb-3">
                            <span class="text-xs px-2 py-1 rounded-full bg-amber-500/10 text-amber-700">
                                <i class="fas fa-tag mr-1"></i>OSS NIB mulai Rp 1,5 Jt
                            </span>
                            <span class="text-xs px-2 py-1 rounded-full bg-amber-500/10 text-amber-700">
                                <i class="fas fa-tag mr-1"></i>UKL-UPL mulai Rp 15 Jt
                            </span>
                            <span class="text-xs px-2 py-1 rounded-full bg-amber-500/10 text-amber-700">
                                <i class="fas fa-tag mr-1"></i>LB3 mulai Rp 25 Jt
                            </span>
                            <span class="text-xs px-2 py-1 rounded-full bg-amber-500/10 text-amber-700">
                                <i class="fas fa-tag mr-1"></i>AMDAL mulai Rp 150 Jt
                            </span>
                        </div>
                        <a href="{{ app()->getLocale() === 'en' ? route('services.index.en') : route('services.index.id') }}" class="text-xs font-semibold text-primary">
                            Lihat rincian harga semua layanan <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                    </div>
                </article>
                
                <!-- Service 2: Konsultasi Bisnis (High Priority) -->
                <article class="feature-card" data-neural-priority="high">
                    <div class="feature-icon" aria-hidden="true">
                        <i class="fas fa-briefcase"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-3 text-gray-900">Konsultasi Bisnis</h3>
                    <p class="text-gray-600 mb-4">
                        Pendampingan profesional untuk pengembangan dan pertumbuhan bisnis berkelanjutan dengan strategi yang tepat.
                    </p>
                    <ul class="space-y-2 text-sm text-gray-600">
                        <li><i class="fas fa-check mr-2 text-amber-500" aria-hidden="true"></i>Legalitas Perusahaan (PT, CV, Yayasan)</li>
                        <li><i class="fas fa-check mr-2 text-amber-500" aria-hidden="true"></i>Strategi Pengembangan Bisnis</li>
                        <li><i class="fas fa-check mr-2 text-amber-500" aria-hidden="true"></i>Perencanaan Pajak & Compliance</li>
                        <li><i class="fas fa-check mr-2 text-amber-500" aria-hidden="true"></i>Business Process Improvement</li>
                        <li><i class="fas fa-check mr-2 text-amber-500" aria-hidden="true"></i>Compliance & Risk Management</li>
                    </ul>
                </article>
                
                <!-- Service 3: Digitalisasi Administrasi (High Priority) -->
                <article class="feature-card" data-neural-priority="high">
                    <div class="feature-icon" aria-hidden="true">
                        <i class="fas fa-laptop-code"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-3 text-gray-900">Digitalisasi Administrasi</h3>
                    <p class="text-gray-600 mb-4">
                        Sistem digital modern untuk efisiensi dan transparansi operasional perusahaan dengan teknologi cloud.
                    </p>
                    <ul class="space-y-2 text-sm text-gray-600">
                        <li><i class="fas fa-check mr-2 text-amber-500" aria-hidden="true"></i>Document Management System</li>
                        <li><i class="fas fa-check mr-2 text-amber-500" aria-hidden="true"></i>Workflow Automation & Approval</li>
                        <li><i class="fas fa-check mr-2 text-amber-500" aria-hidden="true"></i>Project Tracking & Monitoring</li>
                        <li><i class="fas fa-check mr-2 text-amber-500" aria-hidden="true"></i>Real-time Progress Dashboard</li>
                        <li><i class="fas fa-check mr-2 text-amber-500" aria-hidden="true"></i>Reporting & Analytics Dashboard</li>
                    </ul>
                </article>
            </div>

            <!-- Secondary Services: Progressive Disclosure (Collapsible) -->
            <details class="mb-8" data-neural-group="secondary-services">
                <summary x-data @mouseenter="$el.style.borderColor='var(--color-primary)'" @mouseleave="$el.style.borderColor='var(--color-border)'" class="flex items-center justify-center gap-3 py-4 px-6 cursor-pointer section transition-all" style="border-color: var(--color-border);">
                    <span class="text-lg font-semibold text-gray-900">Lihat Layanan Tambahan</span>
                    <i class="fas fa-chevron-down transition-transform text-primary"></i>
                </summary>
                
                <div class="grid md:grid-cols-2 gap-8 mt-8">
                    <!-- Service 4: Legal & Compliance -->
                    <article class="feature-card" data-neural-priority="medium">
                        <div class="feature-icon" aria-hidden="true">
                            <i class="fas fa-balance-scale"></i>
                        </div>
                        <h3 class="text-xl font-bold mb-3 text-gray-900">Legal & Compliance</h3>
                        <p class="text-gray-600">
                            Layanan legal komprehensif untuk memastikan bisnis Anda sesuai regulasi dan perundangan yang berlaku. Konsultasi hukum bisnis, kontrak, dan compliance audit.
                        </p>
                    </article>
                    
                    <!-- Service 5: Partnership & Networking -->
                    <article class="feature-card" data-neural-priority="medium">
                        <div class="feature-icon" aria-hidden="true">
                            <i class="fas fa-handshake"></i>
                        </div>
                        <h3 class="text-xl font-bold mb-3 text-gray-900">Partnership & Networking</h3>
                        <p class="text-gray-600">
                            Fasilitasi kerjasama bisnis dan networking dengan instansi pemerintah (DPMPTSP, DLH, BPN, Notaris) maupun sektor swasta untuk kemudahan proses perizinan.
                        </p>
                    </article>
                </div>
            </details>
            
            <!-- CTA: View all services & cost estimation -->
            <div class="text-center flex flex-col sm:flex-row items-center justify-center gap-4">
                <a href="{{ app()->getLocale() === 'en' ? route('services.index.en') : route('services.index.id') }}"
                   class="btn-primary inline-flex items-center gap-2"
                   data-neural-priority="highest">
                    <i class="fas fa-th-list" aria-hidden="true"></i>
                    <span>{{ __('landing.services.show_more') }}</span>
                </a>
                <a href="/estimasi-biaya"
                   class="btn-secondary inline-flex items-center gap-2"
                   data-neural-priority="high">
                    <i class="fas fa-calculator" aria-hidden="true"></i>
                    <span>Dapatkan Estimasi Biaya</span>
                </a>
            </div>
        </div>
    </section>
