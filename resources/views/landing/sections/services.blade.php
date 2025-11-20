@php
    $services = collect(config('services_data'));
    $colorRgba = function ($hex, $alpha = 0.15) {
        $hex = ltrim($hex, '#');
        if (strlen($hex) === 3) {
            $hex = implode('', array_map(fn($chunk) => $chunk . $chunk, str_split($hex)));
        }
        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));
        return "rgba($r, $g, $b, $alpha)";
    };
@endphp

<!-- Services Section -->
<section id="services" class="py-12 lg:py-20 bg-slate-50">
    <div class="container-wide space-y-8">
        <div class="max-w-3xl text-center mx-auto space-y-4" data-aos="fade-up">
            <div class="pill pill-brand mx-auto justify-center">
                Layanan Bizmark.ID
            </div>
            <h2 class="text-3xl lg:text-4xl font-semibold text-slate-900 leading-tight">
                Layanan perizinan end-to-end agar operasional tetap jalan tanpa hambatan.
            </h2>
            <p class="text-lg text-slate-500">
                Mulai dari desk study sampai izin terbit, tiap layanan punya owner, dokumentasi, dan pelacakan jelas.
            </p>
        </div>
        
        <div class="grid md:grid-cols-2 xl:grid-cols-3 gap-6">
            @forelse ($services as $slug => $service)
                @php
                    $iconColor = $service['color'] ?? '#0077B5';
                @endphp
                <article class="card h-full flex flex-col justify-between" data-aos="fade-up" data-aos-delay="{{ $loop->index * 60 }}" style="--icon-color: {{ $iconColor }}; --icon-sheen: {{ $colorRgba($iconColor, 0.18) }}; --icon-border: {{ $colorRgba($iconColor, 0.35) }};">
                    <div class="space-y-4">
                        <div class="icon-ring">
                            <i class="fas {{ $service['icon'] }} text-xl"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-slate-900">{{ $service['title'] }}</h3>
                        <p class="text-sm text-slate-500 leading-relaxed">
                            {{ $service['short_description'] }}
                        </p>
                    </div>
                    <div class="flex items-center justify-between pt-6 mt-6 border-t border-slate-100 text-xs uppercase tracking-[0.35em] text-slate-400">
                        <span>Detail</span>
                        <a href="{{ route('services.show', $service['slug']) }}" class="inline-flex items-center gap-2 text-slate-900 font-semibold hover:gap-3 transition-all">
                            Buka
                            <i class="fas fa-arrow-right text-[0.65rem]"></i>
                        </a>
                    </div>
                </article>
            @empty
                <p class="text-center text-slate-500 col-span-full">Layanan sedang diperbarui.</p>
            @endforelse
        </div>

        <div class="rounded-3xl border border-slate-200 bg-white px-6 py-5 flex flex-wrap items-center justify-between gap-5">
            <div>
                <p class="text-sm uppercase tracking-[0.35em] text-slate-400">Directory</p>
                <p class="text-base font-semibold text-slate-900">Lihat daftar lengkap layanan beserta studi kasus.</p>
            </div>
            <a href="{{ route('services.index') }}" class="btn btn-secondary" data-cta="services_directory">
                Jelajahi
                <i class="fas fa-arrow-right text-xs"></i>
            </a>
        </div>
    </div>
</section>
