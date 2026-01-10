@php
    $colorRgba = function ($hex, $alpha = 0.2) {
        $hex = ltrim($hex, '#');
        if (strlen($hex) === 3) {
            $hex = implode('', array_map(fn($chunk) => $chunk . $chunk, str_split($hex)));
        }
        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));
        return "rgba($r, $g, $b, $alpha)";
    };

    $steps = config('landing.process_steps', []);
    $isIndonesian = app()->getLocale() === 'id';
@endphp

<!-- Process Section - Neuroscience-Based Design -->
<section id="process" class="py-16 lg:py-24 bg-white">
    <div class="container-wide">
        {{-- Section Header --}}
        <div class="max-w-3xl mx-auto text-center mb-16 space-y-6" data-aos="fade-up" data-aos-duration="800">
            <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-[#5B8DBE]/10 border border-[#5B8DBE]/20">
                <span class="text-sm font-semibold text-[#5B8DBE]">{{ $isIndonesian ? 'Cara Kerja Kami' : 'How We Work' }}</span>
            </div>
            <h2 class="text-4xl lg:text-5xl font-black leading-tight text-[#1A1410]" style="letter-spacing: -0.02em;">
                {{ $isIndonesian ? 'Proses Perizinan Terukur & Transparan' : 'A Measurable, Transparent Permit Process' }}
            </h2>
            <p class="text-lg text-[#6B5D52] leading-relaxed font-light max-w-2xl mx-auto">
                {{ $isIndonesian ? 'Lima fase inti memastikan setiap izin ditangani oleh owner yang jelas, SLA terukur, dan komunikasi lintas instansi berjalan dalam satu jalur.' : 'Five structured phases ensure each permit has a clear owner, measurable SLA, and a single coordination lane across agencies.' }}
            </p>
        </div>

        {{-- Process Steps Grid --}}
        <div class="grid lg:grid-cols-[1.2fr_0.8fr] gap-12 items-start">
            {{-- Steps Column --}}
            <div class="space-y-6">
                @foreach($steps as $index => $step)
                    <article class="group relative bg-white rounded-2xl border border-[#5B8DBE]/10 p-8 transition-all duration-300 hover:shadow-soft-lg hover:border-[#5B8DBE]/30 hover:translate-y-[-4px]"
                             data-aos="fade-up" 
                             data-aos-delay="{{ $index * 100 }}"
                             data-aos-duration="800">
                        {{-- Top Border Accent --}}
                        <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-[#5B8DBE] to-[#E8956F] rounded-t-2xl opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                        
                        {{-- Step Number & Icon --}}
                        <div class="flex items-start gap-6 mb-4">
                            {{-- Step Number Circle --}}
                            <div class="flex-shrink-0 flex flex-col items-center">
                                <div class="w-12 h-12 rounded-full bg-gradient-to-br from-[#5B8DBE] to-[#3A5D82] flex items-center justify-center text-white font-bold text-lg mb-3 shadow-soft">
                                    {{ $index + 1 }}
                                </div>
                                {{-- Connector Line (except last) --}}
                                @if($index < count($steps) - 1)
                                    <div class="w-1 h-16 bg-gradient-to-b from-[#5B8DBE]/30 to-[#5B8DBE]/10"></div>
                                @endif
                            </div>
                            
                            {{-- Icon --}}
                            <div class="w-14 h-14 rounded-full bg-gradient-to-br from-[#5B8DBE]/15 to-[#5B8DBE]/5 border border-[#5B8DBE]/20 flex items-center justify-center flex-shrink-0 group-hover:shadow-soft transition-all duration-300">
                                <i class="{{ $step['icon'] }} text-2xl text-[#5B8DBE]"></i>
                            </div>
                        </div>

                        {{-- Content --}}
                        <div class="ml-[72px]">
                            <h3 class="text-xl font-bold text-[#1A1410] mb-3 group-hover:text-[#5B8DBE] transition-colors duration-300">
                                {{ $isIndonesian ? ($step['title']['id'] ?? '') : ($step['title']['en'] ?? '') }}
                            </h3>
                            <p class="text-sm text-[#6B5D52] leading-relaxed">
                                {{ $isIndonesian ? ($step['body']['id'] ?? '') : ($step['body']['en'] ?? '') }}
                            </p>
                        </div>
                    </article>
                @endforeach
            </div>

            {{-- Image Column --}}
            <div class="flex flex-col" data-aos="fade-left" data-aos-delay="300" data-aos-duration="800">
                <figure class="rounded-2xl border border-[#5B8DBE]/10 bg-[#FDFBF8] shadow-soft-lg overflow-hidden h-full flex flex-col sticky top-32">
                    <picture class="flex-1">
                        <source srcset="https://images.pexels.com/photos/3182834/pexels-photo-3182834.jpeg?auto=compress&cs=tinysrgb&w=600&fm=webp 600w,
                                        https://images.pexels.com/photos/3182834/pexels-photo-3182834.jpeg?auto=compress&cs=tinysrgb&w=1200&fm=webp 1200w"
                                type="image/webp"
                                sizes="(min-width: 1024px) 480px, 100vw">
                        <source srcset="https://images.pexels.com/photos/3182834/pexels-photo-3182834.jpeg?auto=compress&cs=tinysrgb&w=600 600w,
                                        https://images.pexels.com/photos/3182834/pexels-photo-3182834.jpeg?auto=compress&cs=tinysrgb&w=1200 1200w"
                                type="image/jpeg"
                                sizes="(min-width: 1024px) 480px, 100vw">
                        <img src="https://images.pexels.com/photos/3182834/pexels-photo-3182834.jpeg?auto=compress&cs=tinysrgb&w=1200"
                             alt="Tim konsultan Bizmark.ID sedang berdiskusi"
                             class="w-full h-full object-cover object-center"
                             loading="lazy"
                             decoding="async"
                             width="480"
                             height="640">
                    </picture>
                    <div class="flex-shrink-0 bg-white border-t border-[#5B8DBE]/10">
                        <figcaption class="text-xs text-[#9B8B7E] uppercase tracking-wider px-6 py-3 font-semibold">
                            Foto: Pexels / Fauxels
                        </figcaption>
                        <div class="px-6 pb-6 text-sm text-[#6B5D52] leading-relaxed">
                            {{ $isIndonesian ? 'Setiap proyek memiliki workspace khusus yang menyatukan dokumen, percakapan, dan milestone sehingga pimpinan selalu memiliki visibilitas real time.' : 'Each project runs in a dedicated workspace that unifies documents, conversations, and milestones for real-time executive visibility.' }}
                        </div>
                    </div>
                </figure>
            </div>
        </div>
    </div>
</section>
