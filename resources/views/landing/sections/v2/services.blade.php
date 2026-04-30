@php
    $locale = $locale ?? app()->getLocale();
    $isEn = $locale === 'en';
    $servicesIndexRoute = $isEn ? route('services.index.en') : route('services.index.id');

    // Curated core services (consistent icons + real deliverables)
    $coreServices = [
        [
            'slug' => 'perizinan-lb3',
            'icon' => 'fa-recycle',
            'color' => 'var(--accent)',
            'title' => $isEn ? 'B3 Waste Permits' : 'Izin Limbah B3',
            'desc' => $isEn ? 'TPS LB3, handling permits, manifest systems.' : 'TPS LB3, izin pengelolaan, manifest.',
            'bullets' => $isEn
                ? ['TPS LB3 permit', 'Transport manifest', 'Recovery / disposal']
                : ['Izin TPS LB3', 'Manifest angkut', 'Pemulihan / pembuangan'],
        ],
        [
            'slug' => 'amdal',
            'icon' => 'fa-leaf',
            'color' => 'var(--accent)',
            'title' => $isEn ? 'Environmental Impact' : 'Lingkungan',
            'desc' => $isEn ? 'AMDAL, UKL-UPL, SPPL, environmental audits.' : 'AMDAL, UKL-UPL, SPPL, audit lingkungan.',
            'bullets' => $isEn
                ? ['AMDAL full study', 'UKL-UPL document', 'SPPL']
                : ['Studi AMDAL lengkap', 'Dokumen UKL-UPL', 'SPPL'],
        ],
        [
            'slug' => 'pbg-slf',
            'icon' => 'fa-building',
            'color' => 'var(--accent)',
            'title' => $isEn ? 'Building Permits' : 'Perizinan Gedung',
            'desc' => $isEn ? 'PBG, SLF, IMB conversions, technical drawings.' : 'PBG, SLF, konversi IMB, gambar teknis.',
            'bullets' => $isEn
                ? ['PBG issuance', 'SLF certification', 'IMB conversion']
                : ['Penerbitan PBG', 'Sertifikasi SLF', 'Konversi IMB'],
        ],
        [
            'slug' => 'oss-nib',
            'icon' => 'fa-id-card',
            'color' => 'var(--accent)',
            'title' => $isEn ? 'Business License' : 'Izin Usaha',
            'desc' => $isEn ? 'NIB, OSS-RBA setup, sectoral business permits.' : 'NIB, setup OSS-RBA, izin sektoral.',
            'bullets' => $isEn
                ? ['NIB registration', 'OSS-RBA setup', 'SIUP · API']
                : ['Registrasi NIB', 'Setup OSS-RBA', 'SIUP · API'],
        ],
        [
            'slug' => 'pma-investasi-asing',
            'icon' => 'fa-globe-asia',
            'color' => 'var(--accent)',
            'title' => $isEn ? 'PMA / Foreign Investment' : 'PMA / Investasi Asing',
            'desc' => $isEn ? 'BKPM setup, sectoral permits, bilingual full-service.' : 'Setup BKPM, izin sektoral, full-service bilingual.',
            'bullets' => $isEn
                ? ['BKPM registration', 'Sectoral permits', 'Bilingual support']
                : ['Registrasi BKPM', 'Izin sektoral', 'Dukungan bilingual'],
        ],
        [
            'slug' => 'izin-operasional',
            'icon' => 'fa-industry',
            'color' => 'var(--accent)',
            'title' => $isEn ? 'Operational Permits' : 'Operasional',
            'desc' => $isEn ? 'Sector-specific permits: industry, logistics, energy.' : 'Izin sektoral: industri, logistik, energi.',
            'bullets' => $isEn
                ? ['Industrial permits', 'Logistics · K3', 'Compliance audit']
                : ['Izin industri', 'Logistik · K3', 'Audit kepatuhan'],
        ],
    ];
@endphp

{{-- ────────────────────────────────────────────────
     SERVICES GRID — 6 core, symmetrical 3×2
──────────────────────────────────────────────── --}}
<section class="section-v2" aria-labelledby="services-heading" id="services">
    <div class="container-wide">
        <div class="max-w-3xl mb-12" data-aos="fade-up">
            <span class="eyebrow mb-4">{{ $isEn ? 'Core Services' : 'Layanan Utama' }}</span>
            <h2 id="services-heading" class="display-lg mt-2 mb-4 text-gray-100">
                {{ $isEn ? 'End-to-end permit consultancy.' : 'Konsultasi perizinan dari awal hingga terbit.' }}
            </h2>
            <p class="text-lg leading-relaxed text-gray-400">
                {{ $isEn
                    ? 'Six core practice areas. One accountable team per project. Transparent SLA — always.'
                    : 'Enam bidang layanan utama. Satu tim yang akuntabel per proyek. SLA transparan — selalu.' }}
            </p>
        </div>

        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-5 grid-equal">
            @foreach($coreServices as $idx => $svc)
                <a href="{{ $isEn ? route('services.show.en', $svc['slug']) : route('services.show.id', $svc['slug']) }}"
                   class="premium-card group flex flex-col no-underline"
                   data-aos="fade-up" data-aos-delay="{{ ($idx % 3) * 100 }}">
                    <div class="mb-5">
                        <span class="editorial-icon-badge">
                            <i class="fas {{ $svc['icon'] }} icon-xl" aria-hidden="true"></i>
                        </span>
                    </div>
                    <h3 class="font-display font-bold text-xl mb-2 text-gray-100">{{ $svc['title'] }}</h3>
                    <p class="text-sm leading-relaxed mb-4 text-gray-400">{{ $svc['desc'] }}</p>
                    <ul class="space-y-1.5 mb-5 flex-1">
                        @foreach($svc['bullets'] as $b)
                            <li class="flex items-start gap-2 text-sm text-gray-400">
                                <i class="fas fa-check text-[10px] mt-1.5 flex-shrink-0 text-blue-400"></i>
                                <span>{{ $b }}</span>
                            </li>
                        @endforeach
                    </ul>
                    <span class="text-sm font-semibold inline-flex items-center gap-1.5 text-gray-400">
                        {{ $isEn ? 'Learn more' : 'Pelajari' }}
                        <i class="fas fa-arrow-right text-xs transition-transform group-hover:translate-x-1" aria-hidden="true"></i>
                    </span>
                </a>
            @endforeach
        </div>

        <div class="text-center mt-10">
            <a href="{{ $servicesIndexRoute }}" class="btn btn-outline-primary">
                <span>{{ $isEn ? 'View all 20+ services' : 'Lihat semua 20+ layanan' }}</span>
                <i class="fas fa-arrow-right text-xs" aria-hidden="true"></i>
            </a>
        </div>
    </div>
</section>
