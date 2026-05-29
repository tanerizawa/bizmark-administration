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

<!-- Services Section - Neuroscience-Based Design -->
<section id="services" class="py-16 lg:py-24 bg-white">
    <div class="container-wide space-y-12">
        {{-- Section Header --}}
        <div class="max-w-3xl mx-auto space-y-6 text-center" data-aos="fade-up" data-aos-duration="800">
            <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-[#5B8DBE]/10 border border-[#5B8DBE]/20">
                <span class="text-sm font-semibold text-[#5B8DBE]">Layanan Bizmark.ID</span>
            </div>
            <h2 class="text-4xl lg:text-5xl font-black leading-tight text-[#1A1410]" style="letter-spacing: -0.02em;">
                Layanan perizinan end-to-end agar operasional tetap jalan tanpa hambatan.
            </h2>
            <p class="text-lg text-[#6B5D52] leading-relaxed font-light max-w-2xl mx-auto">
                Mulai dari desk study sampai izin terbit, tiap layanan punya owner, dokumentasi, dan pelacakan jelas.
            </p>
        </div>
        
        {{-- Services Grid - Reduced from 4 to 3 columns --}}
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
            @forelse ($services->take(6) as $slug => $service)
                @php
                    $iconColor = $service['color'] ?? '#5B8DBE';
                    // Map old colors to neuroscience palette
                    $colorMap = [
                        '#0077B5' => '#5B8DBE',
                        '#F97316' => '#E8956F',
                        '#10B981' => '#7CB342',
                    ];
                    $mappedColor = $colorMap[$iconColor] ?? $iconColor;
                @endphp
                <article class="group relative bg-white rounded-2xl border border-[#5B8DBE]/10 p-8 transition-all duration-300 hover:shadow-soft-lg hover:border-[#5B8DBE]/30 hover:translate-y-[-4px]" 
                         data-aos="fade-up" 
                         data-aos-delay="{{ $loop->index * 80 }}"
                         data-aos-duration="800">
                    {{-- Top Border Accent --}}
                    <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-[#5B8DBE] to-[#E8956F] rounded-t-2xl opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    
                    {{-- Icon --}}
                    <div class="w-14 h-14 rounded-full bg-gradient-to-br from-[{{ $mappedColor }}]/15 to-[{{ $mappedColor }}]/5 border border-[{{ $mappedColor }}]/20 flex items-center justify-center mb-6 group-hover:shadow-soft transition-all duration-300">
                        <i class="fas {{ $service['icon'] }} text-2xl text-[{{ $mappedColor }}]"></i>
                    </div>

                    {{-- Content --}}
                    <div class="space-y-4 mb-6">
                        <h3 class="text-xl font-bold text-[#1A1410] group-hover:text-[#5B8DBE] transition-colors duration-300">
                            {{ $service['title'] }}
                        </h3>
                        <p class="text-sm text-[#6B5D52] leading-relaxed">
                            {{ $service['short_description'] }}
                        </p>
                    </div>

                    {{-- Footer Link --}}
                    <div class="pt-6 border-t border-[#5B8DBE]/10">
                        <a href="{{ route('services.show.id', $slug) }}" 
                           class="inline-flex items-center gap-2 text-[#5B8DBE] font-semibold text-sm hover:gap-3 transition-all duration-300 group/link">
                            <span>Pelajari Lebih Lanjut</span>
                            <i class="fas fa-arrow-right text-xs group-hover/link:translate-x-1 transition-transform duration-300"></i>
                        </a>
                    </div>
                </article>
            @empty
                <p class="text-center text-[#6B5D52] col-span-full py-12">Layanan sedang diperbarui.</p>
            @endforelse
        </div>

        {{-- CTA Card - Directory --}}
        <div class="rounded-2xl border border-[#5B8DBE]/10 bg-gradient-to-r from-[#5B8DBE]/5 to-[#E8956F]/5 px-8 py-8 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-6 hover:border-[#5B8DBE]/30 transition-all duration-300">
            <div>
                <p class="text-xs font-semibold text-[#9B8B7E] uppercase tracking-wider mb-2">Directory Lengkap</p>
                <p class="text-lg font-bold text-[#1A1410]">Lihat daftar lengkap layanan beserta studi kasus.</p>
            </div>
            <a href="{{ route('services.index.id') }}" 
               class="inline-flex items-center gap-3 px-8 py-4 bg-gradient-to-r from-[#5B8DBE] to-[#3A5D82] text-white rounded-full font-semibold hover:shadow-soft-lg transition-all duration-300 hover:translate-y-[-2px] whitespace-nowrap"
               data-cta="services_directory">
                <span>Jelajahi Semua</span>
                <i class="fas fa-arrow-right text-sm"></i>
            </a>
        </div>
    </div>
</section>
