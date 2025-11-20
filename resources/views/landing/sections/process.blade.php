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

<section id="process" class="section bg-white">
    <div class="container">
        <div class="text-center mb-8" data-aos="fade-up">
            <span class="section-label">
                {{ $isIndonesian ? 'Cara Kerja Kami' : 'How We Work' }}
            </span>
            <h2 class="section-title mb-4">
                {{ $isIndonesian ? 'Proses Perizinan Terukur & Transparan' : 'A Measurable, Transparent Permit Process' }}
            </h2>
            <p class="section-description max-w-3xl mx-auto">
                {{ $isIndonesian ? 'Lima fase inti memastikan setiap izin ditangani oleh owner yang jelas, SLA terukur, dan komunikasi lintas instansi berjalan dalam satu jalur.' : 'Five structured phases ensure each permit has a clear owner, measurable SLA, and a single coordination lane across agencies.' }}
            </p>
        </div>

        <div class="grid lg:grid-cols-[1.15fr_0.85fr] gap-8 items-stretch">
            <div class="space-y-4">
                @foreach($steps as $index => $step)
                    <article class="card flex items-start gap-5" data-aos="fade-up" data-aos-delay="{{ $index * 60 }}" style="--icon-color: {{ $step['color'] }}; --icon-sheen: {{ $colorRgba($step['color'], 0.18) }}; --icon-border: {{ $colorRgba($step['color'], 0.35) }};">
                        <div class="flex-shrink-0">
                            <div class="text-xs uppercase tracking-[0.35em] text-slate-400 mb-2">Step {{ $index + 1 }}</div>
                            <div class="icon-ring">
                                <i class="{{ $step['icon'] }} text-lg"></i>
                            </div>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-slate-900">
                                {{ $isIndonesian ? ($step['title']['id'] ?? '') : ($step['title']['en'] ?? '') }}
                            </h3>
                            <p class="text-sm text-slate-600 leading-relaxed">
                                {{ $isIndonesian ? ($step['body']['id'] ?? '') : ($step['body']['en'] ?? '') }}
                            </p>
                        </div>
                    </article>
                @endforeach
            </div>

            <div class="flex flex-col" data-aos="fade-left" data-aos-delay="180">
                <figure class="rounded-[1.75rem] border border-gray-100 bg-gray-50 shadow-soft-lg overflow-hidden h-full flex flex-col">
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
                    <div class="flex-shrink-0">
                        <figcaption class="text-xs text-gray-500 uppercase tracking-[0.3em] px-4 py-3 border-t border-gray-200">
                            Foto: Pexels / Fauxels
                        </figcaption>
                        <div class="px-6 pb-6 pt-2 text-sm text-gray-600 leading-relaxed">
                            {{ $isIndonesian ? 'Setiap proyek memiliki workspace khusus yang menyatukan dokumen, percakapan, dan milestone sehingga pimpinan selalu memiliki visibilitas real time.' : 'Each project runs in a dedicated workspace that unifies documents, conversations, and milestones for real-time executive visibility.' }}
                        </div>
                    </div>
                </figure>
            </div>
        </div>
    </div>
</section>
