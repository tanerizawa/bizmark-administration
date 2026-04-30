@php
    $locale = $locale ?? app()->getLocale();
    $isEn = $locale === 'en';
    $primaryCtaRoute = route('landing.service-inquiry.create');

    $primaryTools = [
        [
            'icon' => 'fa-robot',
            'color' => 'var(--accent)',
            'title' => $isEn ? 'AI Permit Checker' : 'Cek Kebutuhan Izin (AI)',
            'desc' => $isEn
                ? 'Tell us about your business and our AI instantly maps every permit you need — at no cost.'
                : 'Ceritakan jenis usaha Anda, dan AI kami langsung memetakan semua izin yang perlu Anda urus — tanpa biaya.',
            'stat' => $isEn ? '1,000+ KBLI · Free' : '1.000+ KBLI · Gratis',
            'cta' => $isEn ? 'Check now' : 'Cek sekarang',
            'href' => $primaryCtaRoute,
        ],
        [
            'icon' => 'fa-brain',
            'color' => 'var(--accent)',
            'title' => $isEn ? 'Permit Cost Estimator' : 'Estimasi Biaya Perizinan',
            'desc' => $isEn
                ? 'Select your KBLI business code and get an instant breakdown of permit costs and processing timelines.'
                : 'Pilih kode KBLI usaha Anda dan dapatkan perkiraan biaya serta estimasi durasi pengurusan izin secara langsung.',
            'stat' => $isEn ? 'Real-time · AI' : 'Real-time · AI',
            'cta' => $isEn ? 'Estimate now' : 'Hitung Biaya',
            'href' => route('consultation.index'),
        ],
        [
            'icon' => 'fa-draw-polygon',
            'color' => 'var(--accent)',
            'title' => 'Polygon SHP Maker',
            'desc' => $isEn
                ? 'Draw your business polygon on an interactive map and export an OSS-RBA ready SHP file in minutes.'
                : 'Gambar poligon lokasi usaha Anda di peta interaktif dan ekspor file SHP siap pakai untuk OSS-RBA dalam hitungan menit.',
            'stat' => $isEn ? 'OSS-RBA ready' : 'Siap OSS-RBA',
            'cta' => $isEn ? 'Create SHP' : 'Buat SHP',
            'href' => route('polygon.shp.index'),
        ],
        [
            'icon' => 'fa-calculator',
            'color' => 'var(--accent)',
            'title' => $isEn ? 'Permit Cost Calculator' : 'Kalkulator Biaya Perizinan',
            'desc' => $isEn
                ? 'A detailed cost breakdown by permit type, required documents, and expected processing time.'
                : 'Rincian biaya lengkap berdasarkan jenis izin, dokumen yang diperlukan, dan estimasi waktu pengurusan.',
            'stat' => $isEn ? '50+ permit types' : '50+ jenis izin',
            'cta' => $isEn ? 'Calculate now' : 'Hitung Biaya',
            'href' => route('calculator.index'),
        ],
    ];

    $blogCount = cache()->remember('blog_published_count', 3600, fn() => \App\Models\Article::where('status', 'published')->whereNotNull('published_at')->count());
    $blogSub   = $blogCount . ' ' . ($isEn ? 'articles' : 'artikel');

    $secondaryTools = [
        ['icon' => 'fa-newspaper', 'label' => $isEn ? 'Insights & Guides' : 'Artikel & Panduan', 'sub' => $blogSub, 'href' => $isEn ? route('blog.index.en') : route('blog.index.id')],
        ['icon' => 'fa-user-lock',  'label' => $isEn ? 'Client Portal' : 'Portal Klien',        'sub' => $isEn ? 'Track your projects' : 'Pantau proyek Anda', 'href' => route('login')],
        ['icon' => 'fa-envelope',   'label' => $isEn ? 'Newsletter' : 'Buletin Regulasi',        'sub' => $isEn ? 'Monthly regulatory digest' : 'Pembaruan regulasi bulanan', 'href' => '#newsletter'],
        ['icon' => 'fa-briefcase',  'label' => $isEn ? 'Careers' : 'Karir',                      'sub' => $isEn ? 'Join our team' : 'Bergabung bersama kami', 'href' => route('career.index')],
    ];
@endphp

{{-- ────────────────────────────────────────────────
     ECOSYSTEM HUB — Feature showcase (the highlight)
     Reciprocity: free tools build trust + qualified leads
──────────────────────────────────────────────── --}}
<section class="section-v2" aria-labelledby="ecosystem-heading">
    <div class="container-wide">
        <div class="max-w-3xl mb-8">
            <span class="eyebrow mb-4">{{ $isEn ? 'Digital Ecosystem' : 'Ekosistem Digital' }}</span>
            <h2 id="ecosystem-heading" class="display-lg mt-2 mb-4 text-gray-100">
                {{ $isEn ? 'Free tools built for Indonesia\'s permit ecosystem.' : 'Alat gratis untuk mempercepat perizinan usaha Anda.' }}
            </h2>
            <p class="text-lg leading-relaxed text-gray-400">
                {{ $isEn
                    ? 'Built on over a decade of hands-on permit consultancy. Use any tool for free — and when you need expert guidance, our team is ready.'
                    : 'Dikembangkan dari lebih dari satu dekade pengalaman langsung di bidang konsultansi perizinan. Gunakan semua alat secara gratis — dan saat Anda membutuhkan pendampingan ahli, tim kami siap membantu.' }}
            </p>
        </div>

        {{-- Primary tools: 4 cards in 2×2 grid, equal height --}}
        <div class="grid md:grid-cols-2 gap-5 mb-10 grid-equal">
            @foreach($primaryTools as $tool)
                <a href="{{ $tool['href'] }}" class="tool-card">
                    <div class="flex items-start justify-between gap-4">
                        <span class="editorial-icon-badge">
                            <i class="fas {{ $tool['icon'] }} icon-xl" aria-hidden="true"></i>
                        </span>
                        <span class="tool-stat">{{ $tool['stat'] }}</span>
                    </div>
                    <div>
                        <div class="tool-title mb-1.5">{{ $tool['title'] }}</div>
                        <div class="tool-desc">{{ $tool['desc'] }}</div>
                    </div>
                    <div class="tool-cta pt-2">
                        <span>{{ $tool['cta'] }}</span>
                        <i class="fas fa-arrow-right text-xs flex-shrink-0 leading-none" aria-hidden="true"></i>
                    </div>
                </a>
            @endforeach
        </div>

        {{-- Secondary ecosystem links --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3 pt-6 border-t border-white/10">
            @foreach($secondaryTools as $s)
                <a href="{{ $s['href'] }}" class="subtle-link-card">
                    <span class="editorial-icon-badge">
                        <i class="fas {{ $s['icon'] }} icon-md flex-shrink-0" aria-hidden="true"></i>
                    </span>
                    <div class="min-w-0">
                        <div class="text-sm font-semibold leading-tight text-gray-100">{{ $s['label'] }}</div>
                        <div class="text-xs leading-tight mt-0.5 text-gray-500">{{ $s['sub'] }}</div>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
</section>
