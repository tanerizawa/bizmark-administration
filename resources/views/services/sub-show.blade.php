@extends('landing.layout')

@section('title', $title ?? ($subService['title'] . ' - Bizmark.ID'))
@section('meta_description', $meta_description ?? ($subService['short_description'] ?? ''))
@section('meta_keywords', $subService['meta_keywords'] ?? $parentService['meta_keywords'] ?? '')

@section('structured_data')
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@type": "BreadcrumbList",
    "itemListElement": [
        {"@@type": "ListItem", "position": 1, "name": "Beranda", "item": "{{ route('landing.id') }}"},
        {"@@type": "ListItem", "position": 2, "name": "Layanan", "item": "{{ route('services.index.id') }}"},
        {"@@type": "ListItem", "position": 3, "name": "{{ $parentService['title'] }}", "item": "{{ route('services.show.id', $parentSlug) }}"},
        {"@@type": "ListItem", "position": 4, "name": "{{ $subService['title'] }}"}
    ]
}
</script>
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@type": "Service",
    "name": "{{ $subService['title'] }}",
    "description": "{{ $subService['short_description'] ?? '' }}",
    "provider": {
        "@@type": "Organization",
        "name": "PT Cangah Pajaratan Mandiri (Bizmark.ID)",
        "url": "https://bizmark.id"
    },
    "areaServed": {"@@type": "Country", "name": "Indonesia"},
    "isRelatedTo": {
        "@@type": "Service",
        "name": "{{ $parentService['title'] }}",
        "url": "{{ route('services.show.id', $parentSlug) }}"
    }
}
</script>
@if(!empty($parentService['faq']))
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@type": "FAQPage",
    "mainEntity": [
        @foreach($parentService['faq'] as $faqItem)
        {
            "@@type": "Question",
            "name": "{{ $faqItem['q'] }}",
            "acceptedAnswer": {"@@type": "Answer", "text": "{{ $faqItem['a'] }}"}
        }@if(!$loop->last),@endif
        @endforeach
    ]
}
</script>
@endif
@endsection

@section('content')
@php
    $contact = (array) data_get(config('landing_metrics'), 'contact', []);
    $whatsapp = $contact['whatsapp_link'] ?? 'https://wa.me/6283879602855';
    $phoneRaw = $contact['phone'] ?? '+62 838 7960 2855';
    $phoneHref = 'tel:' . preg_replace('/\s+/', '', $phoneRaw);
    $supportEmail = $contact['email'] ?? 'info@bizmark.id';

    $waText = 'Halo, saya tertarik dengan layanan ' . ($subService['title'] ?? '') . ' (' . ($parentService['title'] ?? '') . ')';
    $waHref = $whatsapp . (str_contains($whatsapp, '?') ? '&' : '?') . 'text=' . rawurlencode($waText);

    $parentColor = $parentService['color'] ?? '#0f172a';
    $subIcon = $subService['icon'] ?? $parentService['icon'] ?? 'fa-concierge-bell';
@endphp

<section class="relative overflow-hidden pt-28 pb-16" style="background:linear-gradient(135deg,var(--surface-warm) 0%, var(--surface-cool) 100%);">
    <div class="container-wide">
        <a href="{{ route('services.show.id', $parentSlug) }}" class="link-primary text-sm inline-flex items-center mb-5">
            <i class="fas fa-arrow-left mr-2"></i>Kembali ke {{ $parentService['title'] }}
        </a>

        <div class="max-w-4xl">
            <div class="flex items-start gap-4 mb-5">
                <div class="w-14 h-14 rounded-xl flex items-center justify-center" style="background:{{ $parentColor }}20;">
                    <i class="fas {{ $subIcon }}" style="color:{{ $parentColor }};"></i>
                </div>
                <div>
                    <span class="section-badge">{{ $parentService['category'] ?? 'Layanan' }}</span>
                    <h1 class="section-title mb-0">{{ $subService['title'] }}</h1>
                </div>
            </div>

            @if(!empty($subService['short_description']))
                <p class="section-description mb-6" style="margin-left:0;">{{ $subService['short_description'] }}</p>
            @endif

            <div class="flex flex-wrap gap-2 mb-7">
                <a href="{{ route('services.show.id', $parentSlug) }}" class="chip"><i class="fas {{ $parentService['icon'] ?? 'fa-layer-group' }}"></i> {{ $parentService['title'] }}</a>
                @if(!empty($subService['duration']))
                    <span class="chip"><i class="fas fa-clock"></i> {{ $subService['duration'] }}</span>
                @endif
                @if(!empty($parentService['price']))
                    <span class="chip"><i class="fas fa-tag"></i> {{ $parentService['price'] }}</span>
                @endif
            </div>

            <div class="flex flex-wrap gap-3">
                <a href="{{ $waHref }}" target="_blank" rel="noopener" class="btn btn-success"><i class="fab fa-whatsapp"></i> Konsultasi via WhatsApp</a>
                <a href="{{ route('contact.index') }}" class="btn btn-primary"><i class="fas fa-envelope"></i> Hubungi Tim</a>
            </div>
        </div>
    </div>
</section>

@if(!empty($subService['long_description']))
<section class="section">
    <div class="container-wide">
        <div class="card">
            <div class="content-prose">
                <h2>Tentang {{ $subService['title'] }}</h2>
                <p>{{ $subService['long_description'] }}</p>
            </div>
        </div>
    </div>
</section>
@endif

@if(!empty($subService['process_steps']))
<section class="section" style="background:var(--surface-warm);">
    <div class="container-wide">
        <h2 class="section-title mb-3">Proses Pengurusan</h2>
        <p class="section-description mb-8" style="margin-left:0;">Ringkasan tahapan pengerjaan layanan ini.</p>
        <div class="grid md:grid-cols-2 gap-6">
            @foreach($subService['process_steps'] as $index => $step)
                <div class="card">
                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 rounded-xl flex items-center justify-center text-white font-bold" style="background:{{ $parentColor }};">
                            {{ $index + 1 }}
                        </div>
                        <div class="min-w-0">
                            <div class="text-base font-semibold mb-1" style="color:var(--text-primary);">{{ $step }}</div>
                            <div class="text-sm" style="color:var(--text-secondary);">Tahap {{ $index + 1 }} dari {{ count($subService['process_steps']) }}.</div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif

@if(!empty($subService['requirements']))
<section class="section">
    <div class="container-wide">
        <div class="card">
            <div class="content-prose">
                <h2>Persyaratan &amp; Dokumen</h2>
                <ul>
                    @foreach($subService['requirements'] as $req)
                        <li>{{ $req }}</li>
                    @endforeach
                </ul>
                <p>Persyaratan detail akan dibahas saat konsultasi awal bersama tim kami.</p>
            </div>
        </div>
    </div>
</section>
@endif

@if(!empty($parentService['faq']))
<section class="section">
    <div class="container-wide">
        <h2 class="section-title mb-3">Pertanyaan Umum</h2>
        <p class="section-description mb-8" style="margin-left:0;">Jawaban cepat untuk pertanyaan yang paling sering muncul.</p>
        <div class="max-w-3xl space-y-3">
            @foreach($parentService['faq'] as $faq)
                <details class="faq-item">
                    <summary class="faq-toggle">
                        <span>{{ $faq['q'] }}</span>
                        <i class="fas fa-chevron-down" aria-hidden="true"></i>
                    </summary>
                    <div class="faq-content">
                        <div class="faq-content-inner">{{ $faq['a'] }}</div>
                    </div>
                </details>
            @endforeach
        </div>
    </div>
</section>
@endif

@if(count($relatedSubs) > 0)
<section class="section" style="background:var(--surface-secondary);">
    <div class="container-wide">
        <h2 class="section-title mb-3">Sub-Layanan Lainnya</h2>
        <p class="section-description mb-8" style="margin-left:0;">Pilihan sub-layanan lain dalam {{ $parentService['title'] }}.</p>
        <div class="grid md:grid-cols-2 xl:grid-cols-3 gap-6">
            @foreach($relatedSubs as $sibSlug => $sibling)
                <a href="{{ route('services.sub.id', [$parentSlug, $sibSlug]) }}" class="card">
                    <h3 class="text-base font-semibold mb-2 card-title" style="color:var(--text-primary);">{{ $sibling['title'] }}</h3>
                    <p class="text-sm mb-0" style="color:var(--text-secondary);">{{ $sibling['short_description'] ?? '' }}</p>
                </a>
            @endforeach
        </div>
    </div>
</section>
@endif

@if(count($relatedServices) > 0)
<section class="section">
    <div class="container-wide">
        <h2 class="section-title mb-3">Layanan Lainnya</h2>
        <p class="section-description mb-8" style="margin-left:0;">Alternatif layanan lain yang relevan untuk kebutuhan Anda.</p>
        <div class="grid md:grid-cols-2 xl:grid-cols-3 gap-6">
            @foreach($relatedServices as $relSlug => $related)
                <a href="{{ route('services.show.id', $relSlug) }}" class="card">
                    <div class="flex items-start justify-between gap-4 mb-4">
                        <div class="w-12 h-12 rounded-lg flex items-center justify-center" style="background:{{ $related['color'] ?? '#0f172a' }}20;">
                            <i class="fas {{ $related['icon'] ?? 'fa-layer-group' }}" style="color:{{ $related['color'] ?? '#0f172a' }};"></i>
                        </div>
                    </div>
                    <h3 class="text-lg font-bold mb-2 card-title" style="color:var(--text-primary);">{{ $related['title'] }}</h3>
                    <p class="text-sm mb-0" style="color:var(--text-secondary);">{{ $related['short_description'] ?? '' }}</p>
                </a>
            @endforeach
        </div>
    </div>
</section>
@endif

<section class="section-sm" style="background:var(--surface-dark);">
    <div class="container-wide text-center">
        <h2 class="text-white mb-3" style="font-size:clamp(1.5rem,3vw,2.1rem);font-weight:750;">Siap Mulai Prosesnya?</h2>
        <p class="mb-7" style="color:rgba(255,255,255,.74);">Konsultasi awal gratis untuk menentukan langkah dan dokumen yang paling tepat.</p>
        <div class="flex flex-wrap justify-center gap-3">
            <a href="{{ $waHref }}" target="_blank" rel="noopener" class="btn btn-success"><i class="fab fa-whatsapp"></i> WhatsApp</a>
            <a href="mailto:{{ $supportEmail }}" class="btn btn-secondary"><i class="fas fa-envelope"></i> Email</a>
            <a href="{{ $phoneHref }}" class="btn btn-ghost"><i class="fas fa-phone"></i> Telepon</a>
        </div>
    </div>
</section>
@endsection
