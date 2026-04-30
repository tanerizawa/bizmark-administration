@php
    $locale = $locale ?? app()->getLocale();
    $isEn = $locale === 'en';
    $contact = (array) data_get(config('landing_metrics'), 'contact', []);
    $whatsappLink = $contact['whatsapp_link'] ?? 'https://wa.me/6283879602855';
    $primaryCtaRoute = route('landing.service-inquiry.create');
@endphp

{{-- ────────────────────────────────────────────────
     FINAL CTA — editorial closing block
──────────────────────────────────────────────── --}}
<section class="section-v2-sm section-ink" aria-labelledby="final-cta-heading">
    <div class="container-wide">
        <div class="max-w-4xl mx-auto text-center" data-aos="fade-up">
            <span class="blue-rule mx-auto mb-6"></span>
            <h2 id="final-cta-heading" class="display-xl mb-6">
                {{ $isEn
                    ? 'Ready to move your permits forward?'
                    : 'Siap mengurus perizinan usaha Anda?' }}
            </h2>
            <p class="text-lg leading-relaxed max-w-2xl mx-auto mb-8 text-gray-400">
                {{ $isEn
                    ? 'Start with a free AI permit check — or speak directly with our consultant team. No commitment required.'
                    : 'Mulai dengan cek kebutuhan izin AI secara gratis — atau langsung bicara dengan konsultan kami. Tidak ada tekanan, tidak ada kewajiban apa pun.' }}
            </p>

            <div class="flex flex-wrap items-center justify-center gap-3">
                <a href="{{ $primaryCtaRoute }}" class="btn btn-primary btn-lg"
                   @click="if(window.trackEvent) trackEvent('CTA','click','final_cta_primary')">
                    <i class="fas fa-robot" aria-hidden="true"></i>
                    <span>{{ $isEn ? 'Start Free Permit Check' : 'Cek Perizinan AI — Gratis' }}</span>
                </a>
                <a href="{{ $whatsappLink }}" target="_blank" rel="noopener noreferrer"
                   class="btn btn-ghost-on-dark btn-lg"
                   @click="if(window.trackEvent) trackEvent('CTA','click','final_cta_whatsapp')">
                    <i class="fab fa-whatsapp" aria-hidden="true"></i>
                    <span>{{ $isEn ? 'Chat on WhatsApp' : 'Hubungi via WhatsApp' }}</span>
                </a>
            </div>

            <p class="text-xs mt-6 text-gray-500">
                <i class="fas fa-shield-halved mr-1.5 text-blue-400"></i>
                {{ $isEn
                    ? 'ISO 9001:2015 · NDA available · 96% on-time delivery · Bilingual EN/ID'
                    : 'ISO 9001:2015 · NDA tersedia · 96% tepat waktu · Bilingual EN/ID' }}
            </p>
        </div>
    </div>
</section>
