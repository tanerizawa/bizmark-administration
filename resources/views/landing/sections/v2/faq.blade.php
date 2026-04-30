@php
    $locale = $locale ?? app()->getLocale();
    $isEn = $locale === 'en';
    $landingConfig = config('landing');
    $faqs = collect((array) data_get($landingConfig, 'faq', []))->take(6);
@endphp

@if($faqs->count() > 0)
{{-- ────────────────────────────────────────────────
     FAQ — top 6 objection-handlers
     Emits FAQPage schema for SEO rich results
──────────────────────────────────────────────── --}}
<section class="section-v2 section-premium" aria-labelledby="faq-heading">
    <div class="container-wide">
        <div class="grid lg:grid-cols-12 gap-10">
            <div class="lg:col-span-4" data-aos="fade-up">
                <span class="eyebrow mb-4">FAQ</span>
                <h2 id="faq-heading" class="display-lg mt-2 mb-4 text-gray-100">
                    {{ $isEn ? 'Common questions.' : 'Pertanyaan umum.' }}
                </h2>
                <p class="text-base leading-relaxed mb-6 text-gray-400">
                    {{ $isEn
                        ? 'Still not sure? Reach out to our team and we\'ll walk you through it.'
                        : 'Masih ragu? Hubungi tim kami dan kami akan bantu jelaskan.' }}
                </p>
                <a href="{{ route('contact.index') }}" class="link-primary text-sm font-semibold inline-flex items-center gap-2">
                    {{ $isEn ? 'Contact our team' : 'Hubungi tim kami' }}
                    <i class="fas fa-arrow-right text-xs" aria-hidden="true"></i>
                </a>

                <div class="mt-8 pt-6 border-t border-white/10 flex flex-col gap-3">
                    <div class="flex items-center gap-2.5 text-sm text-gray-400">
                        <i class="fas fa-certificate flex-shrink-0 text-blue-500"></i>
                        <span>ISO 9001:2015 {{ $isEn ? 'Certified' : 'Tersertifikasi' }}</span>
                    </div>
                    <div class="flex items-center gap-2.5 text-sm text-gray-400">
                        <i class="fas fa-file-contract flex-shrink-0 text-blue-400"></i>
                        <span>{{ $isEn ? 'NDA available on request' : 'NDA tersedia atas permintaan' }}</span>
                    </div>
                    <div class="flex items-center gap-2.5 text-sm text-gray-400">
                        <i class="fas fa-globe flex-shrink-0 text-green-500"></i>
                        <span>{{ $isEn ? 'Bilingual support ID / EN' : 'Dukungan bilingual ID / EN' }}</span>
                    </div>
                    <div class="flex items-center gap-2.5 text-sm text-gray-400">
                        <i class="fas fa-map-marked-alt flex-shrink-0 text-blue-500"></i>
                        <span>{{ $isEn ? 'Nationwide coverage' : 'Cakupan seluruh Indonesia' }}</span>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-8 space-y-3" data-aos="fade-up" data-aos-delay="100">
                @foreach($faqs as $idx => $faq)
                    @php
                        $q = is_array($faq) ? ($faq['question'] ?? $faq['q'] ?? '') : ($faq->question ?? '');
                        $a = is_array($faq) ? ($faq['answer'] ?? $faq['a'] ?? '') : ($faq->answer ?? '');
                        if (is_array($q)) $q = $q[$locale] ?? $q['id'] ?? reset($q);
                        if (is_array($a)) $a = $a[$locale] ?? $a['id'] ?? reset($a);
                    @endphp
                    <details class="faq-item" @if($idx === 0) open @endif>
                        <summary class="faq-toggle">
                            <span>{{ $q }}</span>
                            <i class="fas fa-chevron-down" aria-hidden="true"></i>
                        </summary>
                        <div class="faq-content">
                            {{-- SAFE: e() escapes HTML first, then nl2br adds safe <br> tags --}}
                            <div class="faq-content-inner">{!! nl2br(e($a)) !!}</div>
                        </div>
                    </details>
                @endforeach
            </div>
        </div>
    </div>

    {{-- FAQPage structured data --}}
    <script type="application/ld+json">
    {
        "@@context": "https://schema.org",
        "@@type": "FAQPage",
        "mainEntity": [
            @foreach($faqs as $faq)
                @php
                    $q = is_array($faq) ? ($faq['question'] ?? $faq['q'] ?? '') : ($faq->question ?? '');
                    $a = is_array($faq) ? ($faq['answer'] ?? $faq['a'] ?? '') : ($faq->answer ?? '');
                    if (is_array($q)) $q = $q[$locale] ?? $q['id'] ?? reset($q);
                    if (is_array($a)) $a = $a[$locale] ?? $a['id'] ?? reset($a);
                @endphp
                {
                    "@@type": "Question",
                    "name": @json($q),
                    "acceptedAnswer": {
                        "@@type": "Answer",
                        "text": @json($a)
                    }
                }@if(!$loop->last),@endif
            @endforeach
        ]
    }
    </script>
</section>
@endif

{{-- FAQ accordion styling handled by landing-theme.css --}}
