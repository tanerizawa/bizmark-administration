@extends('landing.layout')

@php
    $locale = $locale ?? app()->getLocale();
    $isEn = $locale === 'en';
    $pageTitle = $isEn ? 'About Us' : 'Tentang Kami';
    $pageDescription = $isEn
        ? 'Bizmark.ID — a decade of permit consultancy for Indonesian manufacturing, infrastructure, and foreign-investment businesses. ISO 9001:2015, 138+ clients, nationwide coverage.'
        : 'Bizmark.ID — satu dekade konsultansi perizinan untuk bisnis manufaktur, infrastruktur, dan PMA di Indonesia. ISO 9001:2015, 138+ klien, cakupan nasional.';

    $contact = (array) data_get(config('landing_metrics'), 'contact', []);
    $whatsappLink = $contact['whatsapp_link'] ?? 'https://wa.me/6283879602855';
    $primaryCtaRoute = route('landing.service-inquiry.create');
    $servicesIndexRoute = $isEn ? route('services.index.en') : route('services.index.id');

    $timeline = [
        ['year' => '2014', 'title' => $isEn ? 'Founded in Karawang' : 'Didirikan di Karawang', 'desc' => $isEn ? 'Started as a specialist for manufacturing environmental permits.' : 'Dimulai sebagai spesialis perizinan lingkungan manufaktur.'],
        ['year' => '2017', 'title' => $isEn ? 'AMDAL practice expanded' : 'Praktik AMDAL diperluas', 'desc' => $isEn ? 'Added full AMDAL, UKL-UPL studies across West Java.' : 'Menambahkan studi AMDAL, UKL-UPL lengkap di Jawa Barat.'],
        ['year' => '2020', 'title' => $isEn ? 'Digital transformation: OSS-RBA' : 'Transformasi digital: OSS-RBA', 'desc' => $isEn ? 'Among the first consultants to offer integrated digital permit tracking for clients.' : 'Menjadi salah satu konsultan pertama yang menawarkan pelacakan perizinan digital terintegrasi bagi klien.'],
        ['year' => '2022', 'title' => $isEn ? 'ISO 9001:2015 certified' : 'Sertifikasi ISO 9001:2015', 'desc' => $isEn ? 'Quality management formalized. Weekly SLA reports standardized.' : 'Manajemen mutu diformalkan. Laporan SLA mingguan distandardisasi.'],
        ['year' => '2024', 'title' => $isEn ? 'AI-powered tools launched' : 'Peluncuran alat berbasis AI', 'desc' => $isEn ? 'AI permit checker, cost estimator, and SHP maker — free for all users.' : 'Cek perizinan AI, estimasi biaya, dan pembuat SHP — tersedia gratis untuk semua pengguna.'],
        ['year' => '2026', 'title' => $isEn ? '138+ active clients' : '138+ klien aktif', 'desc' => $isEn ? 'Across manufacturing, logistics, energy, and PMA sectors.' : 'Lintas sektor manufaktur, logistik, energi, dan PMA.'],
    ];

    $values = [
        ['icon' => 'fa-shield-halved', 'title' => $isEn ? 'Transparent by default' : 'Transparan sejak awal', 'desc' => $isEn ? 'Every SLA, cost, and timeline documented. No surprises.' : 'Setiap SLA, biaya, dan timeline didokumentasikan. Tanpa kejutan.'],
        ['icon' => 'fa-compass',       'title' => $isEn ? 'Regulatory-first' : 'Regulasi adalah peta', 'desc' => $isEn ? 'Our team tracks every KBLI and policy change in-house.' : 'Tim kami mengikuti setiap perubahan KBLI dan kebijakan secara internal.'],
        ['icon' => 'fa-users',         'title' => $isEn ? 'One team, one owner' : 'Satu tim, satu penanggung jawab', 'desc' => $isEn ? 'Dedicated project manager per project — no handoff confusion.' : 'Manajer proyek khusus per klien — tanpa lempar-lemparan tanggung jawab.'],
        ['icon' => 'fa-certificate',   'title' => $isEn ? 'Quality certified' : 'Mutu tersertifikasi', 'desc' => $isEn ? 'ISO 9001:2015 operations. Audited processes.' : 'Operasional ISO 9001:2015. Proses teraudit.'],
    ];

    $certifications = [
        ['icon' => 'fa-certificate', 'label' => 'ISO 9001:2015'],
        ['icon' => 'fa-shield-halved', 'label' => $isEn ? 'OSS-RBA Partner' : 'Mitra OSS-RBA'],
        ['icon' => 'fa-globe', 'label' => 'Bilingual EN / ID'],
        ['icon' => 'fa-map-marked-alt', 'label' => $isEn ? 'Nationwide coverage' : 'Cakupan se-Indonesia'],
    ];
@endphp

@section('title', $pageTitle . ' — Bizmark.ID')
@section('meta_description', $pageDescription)

@section('content')

{{-- HERO --}}
<section class="section-v2 bg-[var(--bg-raised)] border-b border-white/10">
    <div class="container-wide">
        <div class="max-w-4xl">
            <span class="eyebrow mb-6">{{ $isEn ? 'About Bizmark' : 'Tentang Bizmark' }}</span>
            <h1 class="display-xl mt-2 mb-6 text-gray-100">
                {{ $isEn ? 'A decade of permit expertise, delivered transparently.' : 'Satu dekade keahlian di bidang perizinan, disampaikan secara transparan.' }}
            </h1>
            <p class="text-xl leading-relaxed max-w-3xl text-gray-400">
                {{ $isEn
                    ? 'Bizmark.ID (PT Cangah Pajaratan Mandiri) is a specialist permit consultancy serving manufacturing, infrastructure, energy, and foreign-investment clients across Indonesia.'
                    : 'Bizmark.ID (PT Cangah Pajaratan Mandiri) adalah konsultan perizinan spesialis yang melayani klien manufaktur, infrastruktur, energi, dan PMA di seluruh Indonesia.' }}
            </p>

            <div class="stat-cluster mt-12 pt-10 border-t border-white/10">
                <div class="stat-item"><div class="stat-value">138<span class="text-amber-400">+</span></div><div class="stat-label">{{ $isEn ? 'Corporate clients' : 'Klien Korporat' }}</div></div>
                <div class="stat-item"><div class="stat-value">10<span class="text-[.6em] text-amber-400">+</span></div><div class="stat-label">{{ $isEn ? 'Years of experience' : 'Tahun pengalaman' }}</div></div>
                <div class="stat-item"><div class="stat-value">96%</div><div class="stat-label">{{ $isEn ? 'On-time delivery' : 'Izin selesai tepat waktu' }}</div></div>
                <div class="stat-item"><div class="stat-value">500<span class="text-amber-400">+</span></div><div class="stat-label">{{ $isEn ? 'Permits issued' : 'Izin yang diterbitkan' }}</div></div>
            </div>
        </div>
    </div>
</section>

{{-- MISSION / STORY --}}
<section class="section-v2" aria-labelledby="story-heading">
    <div class="container-wide">
        <div class="grid lg:grid-cols-12 gap-12">
            <div class="lg:col-span-5">
                <span class="eyebrow mb-4">{{ $isEn ? 'Our Story' : 'Cerita Kami' }}</span>
                <h2 id="story-heading" class="display-lg mt-2 text-gray-100">
                    {{ $isEn ? 'Built to tackle regulatory complexity.' : 'Dibangun untuk mengatasi kerumitan regulasi perizinan.' }}
                </h2>
            </div>
            <div class="lg:col-span-7">
                <span class="gold-rule"></span>
                <div class="text-lg leading-relaxed space-y-5 text-gray-400">
                    <p>
                        {{ $isEn
                            ? 'We started in 2014 because Indonesian permit regulations change faster than most in-house teams can keep up with. Factory deadlines do not wait for anyone.'
                            : 'Bizmark hadir pada 2014 karena regulasi perizinan Indonesia berubah lebih cepat dari yang mampu diikuti oleh kebanyakan tim internal. Tenggat waktu di pabrik tidak bisa menunggu.' }}
                    </p>
                    <p>
                        {{ $isEn
                            ? 'Today, we serve 138+ corporations with a single promise: every permit, every SLA, every report — fully documented and transparently delivered.'
                            : 'Hari ini, kami melayani 138+ korporasi dengan satu janji: setiap izin, SLA, dan laporan — terdokumentasi lengkap dan disampaikan transparan.' }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- TIMELINE --}}
<section class="section-v2 section-premium" aria-labelledby="timeline-heading">
    <div class="container-wide">
        <div class="max-w-2xl mb-12">
            <span class="eyebrow mb-4">{{ $isEn ? 'Milestones' : 'Tonggak Sejarah' }}</span>
            <h2 id="timeline-heading" class="display-lg mt-2 text-gray-100">
                {{ $isEn ? 'Twelve years, forward.' : 'Dua belas tahun, melangkah maju.' }}
            </h2>
        </div>

        <ol class="timeline-v2 space-y-10 max-w-3xl">
            @foreach($timeline as $t)
                <li class="relative">
                    <span class="timeline-node" aria-hidden="true"></span>
                    <div class="font-display text-2xl font-bold mb-1 text-amber-400">{{ $t['year'] }}</div>
                    <h3 class="font-bold text-lg mb-1 text-gray-100">{{ $t['title'] }}</h3>
                    <p class="text-base leading-relaxed text-gray-400">{{ $t['desc'] }}</p>
                </li>
            @endforeach
        </ol>
    </div>
</section>

{{-- VALUES --}}
<section class="section-v2" aria-labelledby="values-heading">
    <div class="container-wide">
        <div class="max-w-2xl mb-12">
            <span class="eyebrow mb-4">{{ $isEn ? 'Our Values' : 'Nilai Kami' }}</span>
            <h2 id="values-heading" class="display-lg mt-2 text-gray-100">
                {{ $isEn ? 'How we work.' : 'Cara kami bekerja.' }}
            </h2>
        </div>

        <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-5">
            @foreach($values as $v)
                <div class="premium-card flex flex-col">
                    <div class="mb-5">
                        <i class="fas {{ $v['icon'] }} icon-xl text-blue-400" aria-hidden="true"></i>
                    </div>
                    <h3 class="font-display font-bold text-lg mb-2 text-gray-100">{{ $v['title'] }}</h3>
                    <p class="text-sm leading-relaxed text-gray-400">{{ $v['desc'] }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- CERTIFICATIONS --}}
<section class="section-v2-sm section-premium">
    <div class="container-wide text-center">
        <span class="eyebrow mb-6 justify-center">{{ $isEn ? 'Certifications & Partnerships' : 'Sertifikasi & Kemitraan' }}</span>
        <div class="flex flex-wrap justify-center gap-3 mt-6">
            @foreach($certifications as $c)
                <span class="cert-badge"><i class="fas {{ $c['icon'] }}"></i>{{ $c['label'] }}</span>
            @endforeach
        </div>
    </div>
</section>

{{-- CTA --}}
<section class="section-v2 section-ink">
    <div class="container-wide">
        <div class="max-w-3xl mx-auto text-center">
            <span class="gold-rule"></span>
            <h2 class="display-lg mb-6">
                {{ $isEn ? 'Work with a team that knows the full regulatory landscape.' : 'Bekerja dengan tim yang memahami seluruh peta regulasi perizinan Indonesia.' }}
            </h2>
            <p class="text-lg leading-relaxed mb-8 text-white/75">
                {{ $isEn
                    ? 'Start with a free AI permit check — or speak directly with our consultant team.'
                    : 'Mulai dengan cek perizinan AI gratis — atau bicara langsung dengan tim konsultan kami.' }}
            </p>
            <div class="flex flex-wrap items-center justify-center gap-3">
                <a href="{{ $primaryCtaRoute }}" class="btn btn-gold btn-lg">
                    <i class="fas fa-robot"></i>
                    <span>{{ $isEn ? 'Start Free Permit Check' : 'Cek Perizinan Gratis' }}</span>
                </a>
                <a href="{{ $servicesIndexRoute }}" class="btn btn-ghost-on-dark btn-lg">
                    <span>{{ $isEn ? 'Explore Services' : 'Lihat Semua Layanan' }}</span>
                    <i class="fas fa-arrow-right text-xs"></i>
                </a>
            </div>
        </div>
    </div>
</section>

@endsection
