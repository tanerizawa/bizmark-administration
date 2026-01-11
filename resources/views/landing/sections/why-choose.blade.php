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

    $whyChoose = config('landing.why_choose', []);
    $isIndonesian = app()->getLocale() === 'id';
@endphp

<section id="about" class="section bg-white md:bg-[#F7F8FC]">
    <div class="container">
        {{-- Section Header - Enhanced Typography --}}
        <div class="text-center mb-12 space-y-6 max-w-3xl mx-auto" data-aos="fade-up">
            <div class="power-word-badge mx-auto">
                <i class="fas fa-star text-xs"></i>
                <span>{{ $isIndonesian ? 'Mengapa Kami' : 'Why Us' }}</span>
            </div>

            {{-- Heading with Power Words --}}
            <h2 class="heading-section space-y-2">
                <span class="block text-display-md">
                    Mengapa <span class="power-word-gradient">Bizmark.ID</span>?
                </span>
            </h2>

            {{-- Body with F-Pattern --}}
            <p class="text-body-lg paragraph-short mx-auto">
                @if($isIndonesian)
                    <strong class="emphasize">Keunggulan nyata</strong> yang membuat kami menjadi
                    <span class="power-word">pilihan #1</span> untuk
                    <span class="power-word">perizinan industri</span> Anda.
                @else
                    <strong class="emphasize">Real advantages</strong> that make us the
                    <span class="power-word">#1 choice</span> for your
                    <span class="power-word">industrial permits</span>.
                @endif
            </p>
        </div>
        
        <div class="grid md:grid-cols-2 xl:grid-cols-3 gap-6">
            @foreach($whyChoose as $index => $item)
                <article class="card text-center" data-aos="zoom-in" data-aos-delay="{{ $index * 80 }}" style="--icon-color: {{ $item['color'] }}; --icon-sheen: {{ $colorRgba($item['color'], 0.15) }}; --icon-border: {{ $colorRgba($item['color'], 0.35) }};">
                    <div class="icon-ring mx-auto mb-4">
                        <i class="{{ $item['icon'] }} text-xl"></i>
                    </div>
                    <h3 class="heading-card mb-3">
                        {{ $isIndonesian ? ($item['title']['id'] ?? '') : ($item['title']['en'] ?? '') }}
                    </h3>
                    <p class="text-body paragraph-short mx-auto">
                        {{ $isIndonesian ? ($item['body']['id'] ?? '') : ($item['body']['en'] ?? '') }}
                    </p>
                </article>
            @endforeach
        </div>
    </div>
</section>
