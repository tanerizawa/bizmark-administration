@extends('landing.layout')

@php
    $locale = $locale ?? app()->getLocale();
    $isEn = $locale === 'en';
    $pageTitle = $isEn ? 'Our Process' : 'Proses Kami';
    $pageDescription = $isEn
        ? 'How Bizmark.ID delivers permits: a 6-stage process with SLA-backed results, weekly progress reporting, and a dedicated project manager per engagement.'
        : 'Cara Bizmark.ID mengurus perizinan: proses 6 tahap dengan hasil kerja bergaransi SLA, laporan kemajuan mingguan, dan manajer proyek khusus untuk setiap proyek.';

    $contact = (array) data_get(config('landing_metrics'), 'contact', []);
    $whatsappLink = $contact['whatsapp_link'] ?? 'https://wa.me/6283879602855';
    $primaryCtaRoute = route('landing.service-inquiry.create');

    $stages = [
        [
            'num' => '01',
            'title' => $isEn ? 'Initial Consultation & Mapping' : 'Konsultasi Awal & Pemetaan',
            'duration' => $isEn ? '1-2 days' : '1-2 hari',
            'deliverable' => $isEn ? 'Permit gap analysis + KBLI mapping (free)' : 'Analisis celah perizinan + pemetaan KBLI (gratis)',
            'client_input' => $isEn ? 'Business description, KBLI if known, scale' : 'Deskripsi usaha, kode KBLI jika ada, skala operasional',
            'desc' => $isEn
                ? 'We audit your business context and identify every permit you legally need — or already hold — across environmental, operational, and sectoral categories.'
                : 'Kami menelaah konteks usaha Anda dan mengidentifikasi setiap izin yang wajib Anda miliki — maupun yang sudah ada — dari kategori lingkungan, operasional, hingga sektoral.',
            'icon' => 'fa-compass',
        ],
        [
            'num' => '02',
            'title' => $isEn ? 'Proposal & SLA Agreement' : 'Proposal & Kesepakatan SLA',
            'duration' => $isEn ? '2-3 days' : '2-3 hari',
            'deliverable' => $isEn ? 'Formal proposal with scope, cost, SLA' : 'Proposal resmi: lingkup pekerjaan, biaya, SLA',
            'client_input' => $isEn ? 'Internal approvals, company data sheet' : 'Persetujuan internal, lembar data perusahaan',
            'desc' => $isEn
                ? 'Clear scope, transparent cost, and written SLA terms — all documented before work begins. No surprises mid-project.'
                : 'Lingkup pekerjaan, biaya, dan ketentuan SLA yang jelas — semua terdokumentasi sebelum pekerjaan dimulai. Tidak ada kejutan di tengah proyek.',
            'icon' => 'fa-file-signature',
        ],
        [
            'num' => '03',
            'title' => $isEn ? 'Data & Document Collection' : 'Pengumpulan Data & Dokumen',
            'duration' => $isEn ? '3-7 days' : '3-7 hari',
            'deliverable' => $isEn ? 'Complete document checklist + validation' : 'Daftar dokumen lengkap + validasi',
            'client_input' => $isEn ? 'Legal docs, site data, technical drawings' : 'Dokumen hukum, data lokasi, gambar teknis',
            'desc' => $isEn
                ? 'We provide a clear document checklist. Every document is validated before submission — no rejection cycles.'
                : 'Kami menyediakan daftar dokumen yang jelas. Setiap dokumen divalidasi sebelum pengajuan — tanpa siklus penolakan berulang.',
            'icon' => 'fa-folder-open',
        ],
        [
            'num' => '04',
            'title' => $isEn ? 'Submission & Field Follow-through' : 'Pengajuan & Tindak Lanjut Lapangan',
            'duration' => $isEn ? 'Varies by permit' : 'Variatif per jenis izin',
            'deliverable' => $isEn ? 'Submission receipts + weekly SLA report' : 'Tanda terima pengajuan + laporan SLA mingguan',
            'client_input' => $isEn ? 'Site access for inspections (if required)' : 'Akses lokasi untuk inspeksi (jika diperlukan)',
            'desc' => $isEn
                ? 'Our project manager handles OSS submission, ministry coordination, and inspection scheduling. You receive weekly status reports.'
                : 'Manajer proyek kami menangani pengajuan OSS, koordinasi kementerian, dan penjadwalan inspeksi. Anda menerima laporan status setiap minggu.',
            'icon' => 'fa-paper-plane',
        ],
        [
            'num' => '05',
            'title' => $isEn ? 'Issuance & Quality Control' : 'Penerbitan & Kendali Mutu',
            'duration' => $isEn ? '1-3 days' : '1-3 hari',
            'deliverable' => $isEn ? 'Final permits + internal QC sign-off' : 'Izin final + persetujuan kendali mutu internal',
            'client_input' => $isEn ? 'Review & approval' : 'Peninjauan & persetujuan',
            'desc' => $isEn
                ? 'Every permit goes through internal quality control before handover. We verify dates, scope, and all attachments.'
                : 'Setiap izin melewati proses kendali mutu internal sebelum diserahkan. Kami memverifikasi tanggal, lingkup pekerjaan, dan semua lampiran.',
            'icon' => 'fa-clipboard-check',
        ],
        [
            'num' => '06',
            'title' => $isEn ? 'Handover & Compliance Roadmap' : 'Serah Terima & Peta Jalan Kepatuhan',
            'duration' => $isEn ? '1 week' : '1 minggu',
            'deliverable' => $isEn ? 'Digital archive + renewal calendar' : 'Arsip digital + kalender perpanjangan izin',
            'client_input' => $isEn ? 'Kick-off of ongoing monitoring (optional)' : 'Mulai pemantauan berkelanjutan (opsional)',
            'desc' => $isEn
                ? 'We hand over organized digital archives and a compliance roadmap. Optional ongoing monitoring keeps you renewal-ready.'
                : 'Kami menyerahkan arsip digital yang terorganisir beserta peta jalan kepatuhan. Pemantauan berkelanjutan opsional tersedia agar perpanjangan izin Anda selalu siap.',
            'icon' => 'fa-map',
        ],
    ];
@endphp

@section('title', $pageTitle . ' — Bizmark.ID')
@section('meta_description', $pageDescription)

@section('content')

{{-- HERO --}}
<section class="section-v2 bg-[var(--bg-raised)] border-b border-white/10">
    <div class="container-wide">
        <div class="max-w-4xl">
            <span class="eyebrow mb-6">{{ $isEn ? 'How We Work' : 'Cara Kerja Kami' }}</span>
            <h1 class="display-xl mt-2 mb-6 text-gray-100">
                {{ $isEn ? 'A process built for clarity and accountability.' : 'Proses yang mengutamakan kejelasan dan akuntabilitas.' }}
            </h1>
            <p class="text-xl leading-relaxed max-w-3xl text-gray-400">
                {{ $isEn
                    ? 'Six stages. One dedicated project manager per engagement. SLA-backed results at every step — documented in writing and reported weekly.'
                    : 'Enam tahap. Satu manajer proyek khusus per proyek. Hasil kerja bergaransi SLA di setiap langkah — terdokumentasi secara tertulis dan dilaporkan setiap minggu.' }}
            </p>
        </div>
    </div>
</section>

{{-- STAGES --}}
<section class="section-v2">
    <div class="container-wide">
        <div class="space-y-6">
            @foreach($stages as $s)
                <article class="premium-card grid lg:grid-cols-12 gap-6 items-start">
                    <div class="lg:col-span-3 flex items-center gap-4">
                        <span class="inline-flex items-center justify-center w-14 h-14 rounded-full flex-shrink-0 bg-blue-500/15 text-blue-400">
                            <i class="fas {{ $s['icon'] }} text-xl"></i>
                        </span>
                        <div>
                            <div class="font-display text-3xl font-bold leading-none text-amber-400">{{ $s['num'] }}</div>
                            <div class="text-xs font-semibold mt-1 uppercase tracking-wider text-gray-600">
                                {{ $s['duration'] }}
                            </div>
                        </div>
                    </div>

                    <div class="lg:col-span-5">
                        <h2 class="font-display font-bold text-2xl mb-2 text-gray-100">{{ $s['title'] }}</h2>
                        <p class="text-base leading-relaxed text-gray-400">{{ $s['desc'] }}</p>
                    </div>

                    <div class="lg:col-span-4 flex flex-col gap-3">
                        <div class="rounded-lg p-3 bg-blue-950/30">
                            <div class="text-[10px] font-bold uppercase tracking-[.15em] mb-1.5 text-blue-400">
                                <i class="fas fa-cube text-[10px] mr-1"></i>
                                {{ $isEn ? 'Deliverable' : 'Hasil Kerja' }}
                            </div>
                            <div class="text-sm font-medium text-gray-100">{{ $s['deliverable'] }}</div>
                        </div>
                        <div class="rounded-lg p-3 bg-white/[.04]">
                            <div class="text-[10px] font-bold uppercase tracking-[.15em] mb-1.5 text-gray-500">
                                <i class="fas fa-user text-[10px] mr-1"></i>
                                {{ $isEn ? 'From you' : 'Dari Anda' }}
                            </div>
                            <div class="text-sm font-medium text-gray-100">{{ $s['client_input'] }}</div>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
    </div>
</section>

{{-- TRUST BAND --}}
<section class="section-v2-sm section-premium">
    <div class="container-wide text-center">
        <span class="eyebrow mb-4 justify-center">{{ $isEn ? 'What we guarantee' : 'Apa yang kami jamin' }}</span>
        <div class="grid md:grid-cols-4 gap-6 mt-8 max-w-4xl mx-auto">
            <div>
                <div class="font-display text-3xl font-bold text-blue-400">96%</div>
                <div class="text-xs font-semibold mt-1 text-gray-400">{{ $isEn ? 'On-time delivery' : 'Izin selesai tepat waktu' }}</div>
            </div>
            <div>
                <div class="font-display text-3xl font-bold text-blue-400"><i class="fas fa-file-contract"></i></div>
                <div class="text-xs font-semibold mt-1 text-gray-400">{{ $isEn ? 'Written SLA' : 'SLA tertulis' }}</div>
            </div>
            <div>
                <div class="font-display text-3xl font-bold text-blue-400">7</div>
                <div class="text-xs font-semibold mt-1 text-gray-400">{{ $isEn ? 'Days weekly report' : 'Hari laporan mingguan' }}</div>
            </div>
            <div>
                <div class="font-display text-3xl font-bold text-blue-400">1</div>
                <div class="text-xs font-semibold mt-1 text-gray-400">{{ $isEn ? 'Dedicated PM' : 'Manajer proyek khusus' }}</div>
            </div>
        </div>
    </div>
</section>

{{-- CTA --}}
<section class="section-v2 section-ink">
    <div class="container-wide">
        <div class="max-w-3xl mx-auto text-center">
            <span class="gold-rule"></span>
            <h2 class="display-lg mb-6">
                {{ $isEn ? 'See how this process applies to your permits.' : 'Terapkan proses ini pada kebutuhan perizinan Anda.' }}
            </h2>
            <p class="text-lg leading-relaxed mb-8 text-white/75">
                {{ $isEn
                    ? 'Start with a free AI permit check to get your compliance roadmap — then speak with our team.'
                    : 'Mulai dengan cek perizinan AI gratis untuk mendapatkan peta jalan kepatuhan Anda — lalu bicara langsung dengan tim kami.' }}
            </p>
            <div class="flex flex-wrap items-center justify-center gap-3">
                <a href="{{ $primaryCtaRoute }}" class="btn btn-gold btn-lg">
                    <i class="fas fa-robot text-lg flex-shrink-0 leading-none" aria-hidden="true"></i>
                    <span>{{ $isEn ? 'Start Free Permit Check' : 'Cek Perizinan Gratis' }}</span>
                </a>
                <a href="{{ $whatsappLink }}" target="_blank" rel="noopener noreferrer" class="btn btn-ghost-on-dark btn-lg">
                    <i class="fab fa-whatsapp" aria-hidden="true"></i>
                    <span>{{ $isEn ? 'Chat on WhatsApp' : 'Hubungi via WhatsApp' }}</span>
                </a>
            </div>
        </div>
    </div>
</section>

@endsection
