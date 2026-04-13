@extends('landing.layout')

@section('title', $seo['title'])
@section('meta_title', $seo['title'])
@section('meta_description', $seo['description'])
@section('meta_keywords', $seo['keywords'])

@section('content')

{{-- Breadcrumbs --}}
<section class="pt-24 pb-4 px-4 bg-white border-b border-gray-100">
    <div class="container mx-auto max-w-6xl">
        <nav class="flex flex-wrap items-center gap-2 text-sm text-gray-500">
            <a href="/" class="hover:text-primary transition"><i class="fas fa-home"></i></a>
            <span class="text-gray-300">/</span>
            <a href="{{ url('/panduan') }}" class="hover:text-primary transition">Panduan</a>
            <span class="text-gray-300">/</span>
            <span class="text-gray-900 font-medium">{{ $cluster->pillar_title }}</span>
        </nav>
    </div>
</section>

{{-- Hero --}}
<section class="py-12 md:py-16 bg-gradient-to-br from-emerald-900 via-emerald-800 to-teal-900 text-white">
    <div class="container mx-auto max-w-4xl px-4 text-center">
        <h1 class="text-3xl md:text-4xl font-extrabold mb-4">{{ $cluster->pillar_title }}</h1>
        <p class="text-lg text-emerald-100 max-w-2xl mx-auto">{{ $cluster->pillar_description }}</p>
        @if($service)
        <div class="mt-6">
            <a href="{{ url('/layanan/' . $service['slug']) }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-white/10 hover:bg-white/20 rounded-lg text-sm font-medium transition">
                <i class="fas {{ $service['icon'] ?? 'fa-file-alt' }}"></i>
                Lihat Layanan {{ $service['title'] }}
            </a>
        </div>
        @endif
    </div>
</section>

{{-- Table of Contents --}}
<section class="py-8 bg-gray-50 border-b border-gray-200">
    <div class="container mx-auto max-w-4xl px-4">
        <h2 class="text-lg font-bold text-gray-900 mb-4"><i class="fas fa-list-ol mr-2 text-emerald-600"></i>Daftar Isi</h2>
        <div class="grid sm:grid-cols-2 gap-2">
            @foreach($subtopics as $i => $subtopic)
            <a href="#subtopic-{{ $i }}" class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-emerald-50 transition text-sm">
                <span class="w-6 h-6 rounded-full {{ $subtopic['has_article'] ? 'bg-emerald-500' : 'bg-gray-300' }} text-white text-xs flex items-center justify-center font-bold">{{ $i + 1 }}</span>
                <span class="{{ $subtopic['has_article'] ? 'text-gray-900' : 'text-gray-500' }}">{{ $subtopic['title'] }}</span>
                @if($subtopic['has_article'])
                <i class="fas fa-check-circle text-emerald-500 ml-auto text-xs"></i>
                @endif
            </a>
            @endforeach
        </div>
        <p class="mt-3 text-xs text-gray-500">
            <i class="fas fa-circle text-emerald-500 mr-1"></i> Artikel tersedia
            <i class="fas fa-circle text-gray-300 ml-3 mr-1"></i> Segera hadir
        </p>
    </div>
</section>

{{-- Subtopics Detail --}}
<section class="py-12 bg-white">
    <div class="container mx-auto max-w-4xl px-4 space-y-8">
        @foreach($subtopics as $i => $subtopic)
        <div id="subtopic-{{ $i }}" class="scroll-mt-24">
            <div class="flex items-start gap-4 p-6 rounded-xl {{ $subtopic['has_article'] ? 'bg-white border border-gray-200' : 'bg-gray-50 border border-dashed border-gray-300' }}">
                <div class="flex-shrink-0 w-10 h-10 rounded-full {{ $subtopic['has_article'] ? 'bg-emerald-500' : 'bg-gray-300' }} text-white flex items-center justify-center font-bold">
                    {{ $i + 1 }}
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 mb-1">
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                            {{ $subtopic['priority'] === 'high' ? 'bg-red-100 text-red-700' : ($subtopic['priority'] === 'medium' ? 'bg-yellow-100 text-yellow-700' : 'bg-gray-100 text-gray-600') }}">
                            {{ ucfirst($subtopic['priority']) }}
                        </span>
                        <span class="text-xs text-gray-400">{{ ucfirst($subtopic['type']) }}</span>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-2">{{ $subtopic['title'] }}</h3>

                    @if($subtopic['has_article'])
                        <p class="text-sm text-gray-600 mb-3">{{ $subtopic['article']->excerpt }}</p>
                        <a href="{{ $subtopic['article']->getUrl() }}" class="inline-flex items-center gap-1.5 text-sm font-medium text-emerald-600 hover:text-emerald-700 transition">
                            Baca selengkapnya <i class="fas fa-arrow-right text-xs"></i>
                        </a>
                    @else
                        <p class="text-sm text-gray-500 italic">Artikel untuk topik ini sedang dalam tahap persiapan.</p>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>
</section>

{{-- Related Articles --}}
@if($articles->count() > 0)
<section class="py-12 bg-gray-50">
    <div class="container mx-auto max-w-6xl px-4">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">Artikel Terkait</h2>
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($articles->take(6) as $article)
            <a href="{{ $article->getUrl() }}" class="group bg-white rounded-xl border border-gray-200 overflow-hidden hover:shadow-lg transition">
                @if($article->featured_image)
                <img src="{{ $article->featured_image }}" alt="{{ $article->title }}" class="w-full h-40 object-cover" loading="lazy">
                @endif
                <div class="p-4">
                    <h3 class="font-bold text-gray-900 group-hover:text-emerald-600 transition line-clamp-2 mb-2">{{ $article->title }}</h3>
                    <p class="text-sm text-gray-600 line-clamp-2">{{ $article->excerpt }}</p>
                </div>
            </a>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- Keywords Cloud --}}
@if($keywordClusters->count() > 0)
<section class="py-12 bg-white">
    <div class="container mx-auto max-w-4xl px-4">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">Kata Kunci Terkait</h2>
        <div class="flex flex-wrap gap-2">
            @foreach($keywordClusters->flatMap(fn($kc) => $kc->long_tail_keywords ?? $kc->keywords ?? [])->unique()->take(30) as $keyword)
            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-gray-100 text-gray-700 hover:bg-emerald-50 hover:text-emerald-700 transition">
                {{ $keyword }}
            </span>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- Related Pillars --}}
@if($relatedClusters->count() > 0)
<section class="py-12 bg-gray-50">
    <div class="container mx-auto max-w-6xl px-4">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">Panduan Lainnya</h2>
        <div class="grid sm:grid-cols-2 gap-4">
            @foreach($relatedClusters as $rc)
            <a href="{{ url('/panduan/' . $rc->pillar_slug) }}" class="flex items-center gap-4 p-4 bg-white rounded-xl border border-gray-200 hover:border-emerald-300 hover:shadow transition">
                <div class="w-10 h-10 rounded-lg bg-emerald-100 text-emerald-600 flex items-center justify-center">
                    <i class="fas fa-book"></i>
                </div>
                <div>
                    <h3 class="font-bold text-gray-900">{{ $rc->pillar_title }}</h3>
                    <p class="text-sm text-gray-500">{{ count($rc->subtopics ?? []) }} subtopik</p>
                </div>
            </a>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- CTA --}}
<section class="py-12 bg-gradient-to-r from-emerald-600 to-teal-600 text-white">
    <div class="container mx-auto max-w-4xl px-4 text-center">
        <h2 class="text-2xl md:text-3xl font-bold mb-4">Butuh Bantuan Pengurusan?</h2>
        <p class="text-emerald-100 mb-6 max-w-xl mx-auto">Tim ahli kami siap membantu Anda mengurus semua jenis perizinan lingkungan dan industri.</p>
        <a href="https://wa.me/6281573635143" target="_blank" rel="noopener" class="inline-flex items-center gap-2 px-6 py-3 bg-white text-emerald-700 font-bold rounded-lg hover:bg-emerald-50 transition">
            <i class="fab fa-whatsapp text-xl"></i> Konsultasi Gratis
        </a>
    </div>
</section>

{{-- Schemas --}}
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@type": "BreadcrumbList",
    "itemListElement": [
        {"@@type": "ListItem", "position": 1, "name": "Home", "item": "{{ url('/') }}"},
        {"@@type": "ListItem", "position": 2, "name": "Panduan", "item": "{{ url('/panduan') }}"},
        {"@@type": "ListItem", "position": 3, "name": "{{ $cluster->pillar_title }}"}
    ]
}
</script>

<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@type": "Article",
    "headline": "{{ $cluster->pillar_title }}",
    "description": "{{ $cluster->pillar_description }}",
    "url": "{{ url('/panduan/' . $cluster->pillar_slug) }}",
    "dateModified": "{{ $cluster->updated_at->toIso8601String() }}",
    "publisher": {
        "@@type": "Organization",
        "name": "Bizmark.ID",
        "url": "{{ url('/') }}"
    }
}
</script>

@if($faqs->count() > 0)
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@type": "FAQPage",
    "mainEntity": [
        @foreach($faqs as $i => $faq)
        {
            "@@type": "Question",
            "name": "{{ $faq['title'] }}",
            "acceptedAnswer": {
                "@@type": "Answer",
                "text": "{{ $faq['has_article'] ? $faq['article']->excerpt : 'Panduan lengkap segera tersedia di Bizmark.ID' }}"
            }
        }{{ $i < $faqs->count() - 1 ? ',' : '' }}
        @endforeach
    ]
}
</script>
@endif
@endsection
