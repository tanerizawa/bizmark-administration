@php
    $locale = $locale ?? app()->getLocale();
    $isEn = $locale === 'en';
    $servicesIndexRoute = $isEn ? route('services.index.en') : route('services.index.id');
    // Specific service page routes for each segment
    $segRoutes = [
        'umkm' => $isEn ? route('services.show.en', 'oss-nib')              : route('services.show.id', 'oss-nib'),
        'corp' => $isEn ? route('services.show.en', 'amdal')                 : route('services.show.id', 'amdal'),
        'pma'  => $isEn ? route('services.show.en', 'pma-investasi-asing')   : route('services.show.id', 'pma-investasi-asing'),
    ];
@endphp

{{-- ────────────────────────────────────────────────
     SEGMENTATION — Self-selection by business type
     Hick's Law: reduce cognitive load via explicit paths
──────────────────────────────────────────────── --}}
<section class="section-v2-sm section-premium" aria-labelledby="segment-heading">
    <div class="container-wide">
        <div class="text-center max-w-2xl mx-auto mb-7">
            <span class="eyebrow mb-4">{{ $isEn ? 'Solutions by business type' : 'Solusi per Jenis Usaha' }}</span>
            <h2 id="segment-heading" class="display-md mt-2 mb-3 text-gray-100">
                {{ $isEn ? 'Which type of business are you?' : 'Usaha Anda termasuk yang mana?' }}
            </h2>
            <p class="text-base text-gray-400">
                {{ $isEn
                    ? 'Every solution we offer is structured around your business scale, sector, and regulatory obligations.'
                    : 'Setiap solusi yang kami tawarkan dirancang sesuai skala usaha, bidang industri, dan kewajiban regulasi Anda.' }}
            </p>
        </div>

        <div class="grid md:grid-cols-3 gap-5 max-w-5xl mx-auto items-start">
            {{-- MSME --}}
            <a href="{{ $segRoutes['umkm'] }}" class="premium-card group flex flex-col text-center no-underline">
                <span class="editorial-icon-badge self-center mb-5">
                    <i class="fas fa-store icon-xl" aria-hidden="true"></i>
                </span>
                <h3 class="font-display font-bold text-xl mb-2 text-gray-100">
                    {{ $isEn ? 'MSME / Startup' : 'UMKM / Startup' }}
                </h3>
                <p class="text-sm leading-relaxed mb-5 flex-1 text-gray-400">
                    {{ $isEn
                        ? 'NIB registration, OSS-RBA setup, basic environmental documents, and initial business permits.'
                        : 'Registrasi NIB, pengaturan OSS-RBA, dokumen lingkungan dasar, serta izin usaha tahap awal.' }}
                </p>
                <span class="inline-flex items-center justify-center gap-2 text-sm font-semibold text-gray-400">
                    {{ $isEn ? 'Explore solutions' : 'Temukan solusinya' }}
                    <i class="fas fa-arrow-right text-xs flex-shrink-0 leading-none transition-transform group-hover:translate-x-1" aria-hidden="true"></i>
                </span>
            </a>

            {{-- Corporate (featured) — badge rendered in normal flow at top of card --}}
            <a href="{{ $segRoutes['corp'] }}" class="premium-card is-featured group flex flex-col text-center no-underline">
                <span class="inline-flex items-center justify-center self-center px-3 py-1 mb-4 text-[10px] font-bold uppercase tracking-wider rounded-full bg-blue-500/10 text-blue-400 border border-blue-500/20 tracking-[.15em]">
                    {{ $isEn ? 'Most Popular' : 'Paling Populer' }}
                </span>
                <span class="editorial-icon-badge self-center mb-5">
                    <i class="fas fa-industry icon-xl" aria-hidden="true"></i>
                </span>
                <h3 class="font-display font-bold text-xl mb-2 text-gray-100">
                    {{ $isEn ? 'Mid-to-Large Corporation' : 'Korporasi Menengah & Besar' }}
                </h3>
                <p class="text-sm leading-relaxed mb-5 flex-1 text-gray-400">
                    {{ $isEn
                        ? 'AMDAL, B3 waste management, UKL-UPL, PBG, SLF — full environmental and building compliance.'
                        : 'AMDAL, pengelolaan limbah B3, UKL-UPL, PBG, SLF — kepatuhan lingkungan dan bangunan secara menyeluruh.' }}
                </p>
                <span class="inline-flex items-center justify-center gap-2 text-sm font-semibold text-gray-400">
                    {{ $isEn ? 'Explore solutions' : 'Temukan solusinya' }}
                    <i class="fas fa-arrow-right text-xs flex-shrink-0 leading-none transition-transform group-hover:translate-x-1" aria-hidden="true"></i>
                </span>
            </a>

            {{-- PMA --}}
            <a href="{{ $segRoutes['pma'] }}" class="premium-card group flex flex-col text-center no-underline">
                <span class="editorial-icon-badge self-center mb-5">
                    <i class="fas fa-globe-asia icon-xl" aria-hidden="true"></i>
                </span>
                <h3 class="font-display font-bold text-xl mb-2 text-gray-100">
                    {{ $isEn ? 'Foreign-Invested Company (PMA)' : 'Perusahaan PMA / Investasi Asing' }}
                </h3>
                <p class="text-sm leading-relaxed mb-5 flex-1 text-gray-400">
                    {{ $isEn
                        ? 'BKPM registration, sector-specific permits, and complete bilingual guidance throughout the entire process.'
                        : 'Pendaftaran BKPM, pengurusan izin sektoral, dan pendampingan bilingual di setiap tahap proses.' }}
                </p>
                <span class="inline-flex items-center justify-center gap-2 text-sm font-semibold text-gray-400">
                    {{ $isEn ? 'Explore solutions' : 'Temukan solusinya' }}
                    <i class="fas fa-arrow-right text-xs flex-shrink-0 leading-none transition-transform group-hover:translate-x-1" aria-hidden="true"></i>
                </span>
            </a>
        </div>
    </div>
</section>
