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
        <div class="text-center mb-12" data-aos="fade-up">
            <span class="section-label">
                {{ $isIndonesian ? 'Mengapa Kami' : 'Why Us' }}
            </span>
            <h2 class="section-title mb-6">
                {{ $isIndonesian ? 'Mengapa Memilih Bizmark.ID?' : 'Why Choose Bizmark.ID?' }}
            </h2>
            <p class="section-description max-w-3xl mx-auto">
                {{ $isIndonesian ? 'Keunggulan yang membuat kami menjadi pilihan terbaik untuk perizinan industri Anda.' : 'Advantages that make us the most reliable partner for your permitting needs.' }}
            </p>
        </div>
        
        <div class="grid md:grid-cols-2 xl:grid-cols-3 gap-6 lg:gap-8">
            @foreach($whyChoose as $index => $item)
                <article class="card text-center" data-aos="zoom-in" data-aos-delay="{{ $index * 80 }}" style="--icon-color: {{ $item['color'] }}; --icon-sheen: {{ $colorRgba($item['color'], 0.15) }}; --icon-border: {{ $colorRgba($item['color'], 0.35) }};">
                    <div class="icon-ring mx-auto mb-4">
                        <i class="{{ $item['icon'] }} text-xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold mb-3 text-gray-900">
                        {{ $isIndonesian ? ($item['title']['id'] ?? '') : ($item['title']['en'] ?? '') }}
                    </h3>
                    <p class="text-gray-600 leading-relaxed">
                        {{ $isIndonesian ? ($item['body']['id'] ?? '') : ($item['body']['en'] ?? '') }}
                    </p>
                </article>
            @endforeach
        </div>
    </div>
</section>
