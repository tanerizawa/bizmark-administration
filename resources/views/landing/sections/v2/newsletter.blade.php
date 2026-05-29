@php
    $locale = $locale ?? app()->getLocale();
    $isEn = $locale === 'en';
@endphp

{{-- ────────────────────────────────────────────────
     NEWSLETTER — editorial horizontal card
     Balanced grid · unified dark surface · aligned form row
──────────────────────────────────────────────── --}}
<section class="section-v2" id="newsletter" aria-labelledby="newsletter-heading">
    <div class="container-wide">
        <div class="newsletter-shell relative">

            <div class="newsletter-grid relative grid lg:grid-cols-12 gap-8 p-8 md:p-10 lg:p-11 items-center">

                {{-- Copy (7 cols) --}}
                <div class="lg:col-span-7 newsletter-copy">
                    <span class="eyebrow mb-4">
                        {{ $isEn ? 'Newsletter' : 'Buletin' }}
                    </span>
                    <h2 id="newsletter-heading" class="display-md mt-3 mb-4">
                        {{ $isEn
                            ? 'Stay ahead of Indonesia\'s regulatory changes.'
                            : 'Pembaruan regulasi langsung ke email Anda.' }}
                    </h2>
                    <p class="text-base md:text-lg leading-relaxed max-w-xl text-gray-600">
                        {{ $isEn
                            ? 'A monthly digest of permit regulation updates, KBLI rule changes, and compliance briefings — curated by our regulatory specialists.'
                            : 'Ringkasan bulanan pembaruan regulasi perizinan, perubahan aturan KBLI, dan briefing kepatuhan — dikurasi langsung oleh spesialis regulasi kami.' }}
                    </p>
                </div>

                {{-- Form (5 cols) --}}
                <div class="lg:col-span-5 newsletter-form-panel">
                    <form action="{{ route('newsletter.subscribe') }}" method="POST" class="form-row-v2 newsletter-form-row">
                        @csrf
                        <label for="newsletter-email" class="sr-only">
                            {{ $isEn ? 'Email address' : 'Alamat email' }}
                        </label>
                        <input type="email"
                               name="email"
                               id="newsletter-email"
                               required
                               autocomplete="email"
                               placeholder="{{ $isEn ? 'your@company.com' : 'email@perusahaan.com' }}"
                               class="form-input-dark">
                        <button type="submit" class="btn btn-primary">
                            <span>{{ $isEn ? 'Subscribe' : 'Berlangganan' }}</span>
                            <i class="fas fa-arrow-right text-xs flex-shrink-0 leading-none" aria-hidden="true"></i>
                        </button>
                    </form>

                    <div class="newsletter-meta mt-4 flex flex-wrap items-center gap-x-4 gap-y-2 text-xs text-gray-500">
                        <span class="inline-flex items-center gap-1.5">
                            <i class="fas fa-calendar-check text-xs flex-shrink-0 leading-none text-amber-600" aria-hidden="true"></i>
                            {{ $isEn ? 'Monthly digest' : 'Ringkasan bulanan' }}
                        </span>
                        <span class="inline-flex items-center gap-1.5">
                            <i class="fas fa-ban text-xs flex-shrink-0 leading-none text-gray-500" aria-hidden="true"></i>
                            {{ $isEn ? 'No spam, ever' : 'Bebas spam' }}
                        </span>
                        <span class="inline-flex items-center gap-1.5">
                            <i class="fas fa-check-circle text-xs flex-shrink-0 leading-none text-green-500" aria-hidden="true"></i>
                            {{ $isEn ? 'One-click unsubscribe' : 'Berhenti kapan saja' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
