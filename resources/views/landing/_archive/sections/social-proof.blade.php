@php
    $clientList = config('landing.clients', []);
    $testimonials = collect(config('landing.testimonials', []));
    $metrics = config('landing_metrics');
    $industryCount = data_get($metrics, 'clients.industries', 6);
    $coverageLabel = trim((string) data_get($metrics, 'coverage.cities', ''));
    if ($coverageLabel === '' || \Illuminate\Support\Str::lower($coverageLabel) === 'berbagai') {
        $coverageLabel = 'berbagai kota';
    }
@endphp

{{-- Social Proof Section: Clients + Testimonials --}}
<section class="py-12 lg:py-20 bg-gradient-to-b from-white to-gray-50">
    <div class="container-wide space-y-8">
        
        {{-- Part 1: Trusted Clients --}}
        <div class="space-y-6">
            <div class="text-center max-w-3xl mx-auto space-y-4" data-aos="fade-up">
                <div class="pill pill-brand mx-auto justify-center">
                    Dipercaya Oleh
                </div>
                <h2 class="text-3xl lg:text-4xl font-bold text-gray-900 leading-tight">
                    Dipercaya perusahaan manufaktur, infrastruktur, dan energi.
                </h2>
                <p class="text-lg text-gray-600">
                    Perusahaan dari {{ $industryCount }} sektor industri di {{ $coverageLabel }} mempercayakan audit izin, AMDAL, dan pengelolaan legalitasnya kepada tim Bizmark.ID untuk menjaga tata kelola tetap rapi.
                </p>
            </div>

            {{-- Client Logos Grid --}}
            <div class="relative" data-aos="fade-up" data-aos-delay="80">
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-6" role="list" aria-label="Daftar klien Bizmark.ID">
                    @foreach($clientList as $index => $clientName)
                    <article class="client-logo-card group" role="listitem" data-aos="zoom-in" data-aos-delay="{{ 80 + ($index * 40) }}">
                        <div class="relative aspect-[3/2] flex items-center justify-center p-6 bg-white rounded-2xl border border-slate-100 transition-all duration-400 hover:-translate-y-1 hover:border-primary/30 hover:bg-primary/5">
                            <div class="text-center grayscale group-hover:grayscale-0 transition-all duration-500">
                                <div class="monogram mb-1">
                                    {{ strtoupper(substr($clientName, 0, 2)) }}
                                </div>
                                <div class="text-sm text-slate-500 group-hover:text-slate-700 font-medium leading-tight">
                                    {{ $clientName }}
                                </div>
                            </div>
                            <div class="absolute inset-0 bg-gradient-to-br from-primary/5 to-transparent rounded-2xl opacity-0 group-hover:opacity-100 transition-opacity duration-500 pointer-events-none"></div>
                        </div>
                    </article>
                    @endforeach
                </div>
                <div class="absolute -top-8 -left-8 w-32 h-32 bg-primary/10 rounded-full blur-3xl -z-10"></div>
                <div class="absolute -bottom-8 -right-8 w-32 h-32 bg-secondary/10 rounded-full blur-3xl -z-10"></div>
            </div>
        </div>

        {{-- Part 2: Client Testimonials --}}
        @php
        $testimonialData = collect(config('landing.testimonials', []));
        $placeholderTestimonials = [
            ['quote' => 'Tim Bizmark.ID sangat profesional. Proses AMDAL yang biasanya berbulan-bulan berhasil diselesaikan tepat waktu dengan dokumentasi lengkap. Highly recommended.', 'name' => 'Direktur Operasional', 'company' => 'Perusahaan Manufaktur, Jawa Barat', 'rating' => 5, 'icon' => 'fa-industry', 'color' => '#16a34a'],
            ['quote' => 'Pengurusan NIB dan OSS-RBA kami selesai dalam waktu yang sangat singkat. Bizmark.ID benar-benar tahu seluk-beluk regulasi dan prosesnya sangat transparan.', 'name' => 'General Manager', 'company' => 'Perusahaan Logistik, DKI Jakarta', 'rating' => 5, 'icon' => 'fa-truck', 'color' => '#0ea5e9'],
            ['quote' => 'Sebagai perusahaan PMA, kami sangat terbantu dengan dukungan bilingual Bizmark.ID. Semua izin sektoral terurus dengan baik dan laporan mingguan membuat kami tenang.', 'name' => 'Country Manager', 'company' => 'PMA Sektor Energi, Jakarta', 'rating' => 5, 'icon' => 'fa-globe-asia', 'color' => '#f97316'],
        ];
        $displayTestimonials = $testimonialData->count() > 0 ? $testimonialData->take(3)->values() : collect($placeholderTestimonials);
        @endphp

        <div class="space-y-6" data-aos="fade-up">
            <div class="text-center max-w-2xl mx-auto">
                <div class="pill pill-brand mx-auto justify-center mb-3">Testimoni Klien</div>
                <h3 class="text-2xl font-bold" style="color:var(--text-primary);">Apa Kata Klien Kami</h3>
            </div>
            <div class="grid md:grid-cols-3 gap-5">
                @foreach($displayTestimonials as $t)
                @php
                $quote   = is_array($t) ? ($t['quote']   ?? '') : ($t->quote   ?? '');
                $name    = is_array($t) ? ($t['name']    ?? '') : ($t->name    ?? '');
                $company = is_array($t) ? ($t['company'] ?? '') : ($t->company ?? '');
                $rating  = is_array($t) ? ($t['rating']  ?? 5)  : ($t->rating  ?? 5);
                $icon    = is_array($t) ? ($t['icon']    ?? 'fa-building') : 'fa-building';
                $color   = is_array($t) ? ($t['color']   ?? '#0f172a') : '#0f172a';
                @endphp
                <div class="magazine-card p-6 flex flex-col gap-4">
                    <div class="flex items-center gap-1 text-yellow-400">
                        @for($i = 0; $i < (int)$rating; $i++)
                        <i class="fas fa-star text-xs"></i>
                        @endfor
                    </div>
                    <p class="text-sm leading-relaxed flex-1 italic" style="color:var(--text-secondary);">"{{ $quote }}"</p>
                    <div class="flex items-center gap-3 pt-3 border-t" style="border-color:var(--border-light);">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center shrink-0" style="background:{{ $color }}14;border:1.5px solid {{ $color }}30;">
                            <i class="fas {{ $icon }} text-sm" style="color:{{ $color }};"></i>
                        </div>
                        <div>
                            <p class="text-sm font-bold" style="color:var(--text-primary);">{{ $name }}</p>
                            <p class="text-xs" style="color:var(--text-tertiary);">{{ $company }}</p>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            <p class="text-center text-xs" style="color:var(--text-tertiary);">* Studi kasus lengkap tersedia via NDA — hubungi tim kami untuk menjadwalkan review.</p>
        </div>

        {{-- Bottom Note --}}
        <div class="text-center space-y-3" data-aos="fade-up">
            <p class="text-sm uppercase tracking-[0.4em] text-gray-400">Reference Projects</p>
            <p class="text-base text-gray-600">Studi kasus lengkap tersedia via NDA - hubungi tim kami untuk menjadwalkan review.</p>
        </div>
    </div>
</section>

