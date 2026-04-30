    <!-- Digital Tools Section -->
    <!-- ============================================
         DIGITAL TOOLS SECTION - FREE TOOLS SHOWCASE
         Neuroscience Optimization:
         - Value-first presentation (free tools)
         - Clear visual hierarchy with icons
         - "NEW" badge for attention capture
         - 2-column layout for easy scanning
         ============================================ -->
    <section id="digital-tools" class="py-20 px-4" style="background: linear-gradient(135deg, var(--dark-bg-secondary) 0%, var(--dark-bg-tertiary) 100%);" aria-labelledby="tools-heading">
        <div class="container mx-auto max-w-6xl">
            <!-- Section Header with "NEW" Badge -->
            <div class="text-center mb-16">
                <div class="inline-flex items-center gap-2 mb-4">
                    <span class="px-4 py-2 text-sm font-bold bg-gradient-to-r from-green-500 to-emerald-500 text-white rounded-full shadow-lg" style="animation: pulse 2s infinite;">
                        <i class="fas fa-star mr-1"></i>FITUR BARU
                    </span>
                </div>
                <h2 id="tools-heading" class="text-4xl md:text-5xl font-bold mb-4">
                    Alat Digital Gratis
                </h2>
                <p class="text-xl" style="color: var(--dark-text-secondary);">
                    Tools digital gratis untuk membantu persiapan perizinan Anda
                </p>
            </div>
            
            <!-- Tools Grid: 3 Cards -->
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8 mb-8" data-neural-group="digital-tools">
                <!-- Tool 1: Polygon SHP Maker -->
                <a href="{{ route('polygon.shp.index') }}" 
                   class="group block transform hover:scale-105 transition-all duration-300 section p-8" 
                   style="background: var(--dark-bg-primary); border: 2px solid var(--color-border);"
                   data-neural-priority="highest">
                    <div class="relative mb-6">
                        <!-- Tool Icon -->
                        <div class="w-16 h-16 rounded-2xl flex items-center justify-center" style="background: linear-gradient(135deg, #10b981, #059669);">
                            <i class="fas fa-draw-polygon text-3xl text-white"></i>
                        </div>
                        <!-- Free Badge -->
                        <span class="absolute -top-2 -right-2 px-3 py-1 text-xs font-bold bg-green-500 text-white rounded-full shadow-lg">
                            GRATIS
                        </span>
                    </div>
                    <h3 class="text-2xl font-bold mb-3 group-hover:text-green-400 transition-colors">
                        Polygon SHP Maker
                    </h3>
                    <p style="color: var(--dark-text-secondary);" class="mb-4 text-lg">
                        Buat file Shapefile (.shp) untuk upload OSS RBA. Gambar poligon di peta interaktif, proyeksi WGS84 standar, unduh langsung dalam ZIP.
                    </p>
                    <ul class="space-y-3 text-base mb-6" style="color: var(--dark-text-secondary);">
                        <li class="flex items-center"><i class="fas fa-check-circle mr-3 text-green-500"></i>Peta interaktif untuk gambar poligon</li>
                        <li class="flex items-center"><i class="fas fa-check-circle mr-3 text-green-500"></i>Proyeksi WGS84 standar OSS RBA</li>
                        <li class="flex items-center"><i class="fas fa-check-circle mr-3 text-green-500"></i>Format ESRI Shapefile siap upload</li>
                        <li class="flex items-center"><i class="fas fa-check-circle mr-3 text-green-500"></i>Gratis tanpa batas penggunaan</li>
                    </ul>
                    <div class="inline-flex items-center gap-2 px-6 py-3 rounded-lg font-bold text-white group-hover:gap-4 transition-all" style="background: linear-gradient(135deg, #10b981, #059669);">
                        <span>Buat File SHP Sekarang</span>
                        <i class="fas fa-arrow-right"></i>
                    </div>
                </a>
                
                <!-- Tool 2: Estimasi Biaya Perizinan -->
                <a href="{{ route('consultation.index') }}" 
                   class="group block transform hover:scale-105 transition-all duration-300 section p-8" 
                   style="background: var(--dark-bg-primary); border: 2px solid var(--color-border);"
                   data-neural-priority="high">
                    <div class="relative mb-6">
                        <!-- Tool Icon -->
                        <div class="w-16 h-16 rounded-2xl flex items-center justify-center" style="background: linear-gradient(135deg, #8b5cf6, #7c3aed);">
                            <i class="fas fa-calculator text-3xl text-white"></i>
                        </div>
                        <!-- AI Badge -->
                        <span class="absolute -top-2 -right-2 px-3 py-1 text-xs font-bold bg-purple-500 text-white rounded-full shadow-lg">
                            <i class="fas fa-robot mr-1"></i>AI
                        </span>
                    </div>
                    <h3 class="text-2xl font-bold mb-3 group-hover:text-purple-400 transition-colors">
                        Estimasi Biaya Perizinan
                    </h3>
                    <p style="color: var(--dark-text-secondary);" class="mb-4 text-lg">
                        Dapatkan estimasi biaya perizinan usaha dengan AI analysis. Pilih KBLI usaha, isi informasi bisnis, terima estimasi instan.
                    </p>
                    <ul class="space-y-3 text-base mb-6" style="color: var(--dark-text-secondary);">
                        <li class="flex items-center"><i class="fas fa-check-circle mr-3 text-purple-500"></i>AI-powered cost estimation</li>
                        <li class="flex items-center"><i class="fas fa-check-circle mr-3 text-purple-500"></i>Database KBLI terlengkap</li>
                        <li class="flex items-center"><i class="fas fa-check-circle mr-3 text-purple-500"></i>Hasil instan tanpa registrasi</li>
                        <li class="flex items-center"><i class="fas fa-check-circle mr-3 text-purple-500"></i>Rincian biaya transparan</li>
                    </ul>
                    <div class="inline-flex items-center gap-2 px-6 py-3 rounded-lg font-bold text-white group-hover:gap-4 transition-all" style="background: linear-gradient(135deg, #8b5cf6, #7c3aed);">
                        <span>Hitung Estimasi Biaya</span>
                        <i class="fas fa-arrow-right"></i>
                    </div>
                </a>
                
                <!-- Tool 3: Analisis Perizinan Usaha -->
                <a href="{{ route('client.services.index') }}" 
                   class="group block transform hover:scale-105 transition-all duration-300 section p-8" 
                   style="background: var(--dark-bg-primary); border: 2px solid var(--color-border);"
                   data-neural-priority="high">
                    <div class="relative mb-6">
                        <!-- Tool Icon -->
                        <div class="w-16 h-16 rounded-2xl flex items-center justify-center" style="background: linear-gradient(135deg, #0ea5e9, #0284c7);">
                            <i class="fas fa-search-dollar text-3xl text-white"></i>
                        </div>
                        <!-- AI Badge -->
                        <span class="absolute -top-2 -right-2 px-3 py-1 text-xs font-bold bg-sky-500 text-white rounded-full shadow-lg">
                            <i class="fas fa-robot mr-1"></i>AI
                        </span>
                    </div>
                    <h3 class="text-2xl font-bold mb-3 group-hover:text-sky-400 transition-colors">
                        Analisis Perizinan Usaha
                    </h3>
                    <p style="color: var(--dark-text-secondary);" class="mb-4 text-lg">
                        Sistem AI menganalisis kebutuhan bisnis Anda dan merekomendasikan izin yang wajib lengkap dengan estimasi biaya dan waktu proses.
                    </p>
                    <ul class="space-y-3 text-base mb-6" style="color: var(--dark-text-secondary);">
                        <li class="flex items-center"><i class="fas fa-check-circle mr-3 text-sky-500"></i>AI-powered recommendation</li>
                        <li class="flex items-center"><i class="fas fa-check-circle mr-3 text-sky-500"></i>Katalog 1000+ layanan KBLI</li>
                        <li class="flex items-center"><i class="fas fa-check-circle mr-3 text-sky-500"></i>Analisis kebutuhan izin lengkap</li>
                        <li class="flex items-center"><i class="fas fa-check-circle mr-3 text-sky-500"></i>Gratis tanpa registrasi</li>
                    </ul>
                    <div class="inline-flex items-center gap-2 px-6 py-3 rounded-lg font-bold text-white group-hover:gap-4 transition-all" style="background: linear-gradient(135deg, #0ea5e9, #0284c7);">
                        <span>Analisis Kebutuhan Izin</span>
                        <i class="fas fa-arrow-right"></i>
                    </div>
                </a>
            </div>
        </div>
    </section>
