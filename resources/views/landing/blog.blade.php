@extends('landing.layout')

@section('title', 'Artikel & Berita — Bizmark.ID')
@section('meta_description', 'Update terbaru seputar perizinan usaha, regulasi OSS-RBA, lingkungan hidup, dan tips legalitas bisnis Indonesia.')

@section('structured_data')
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@type": "BreadcrumbList",
    "itemListElement": [
        {
            "@@type": "ListItem",
            "position": 1,
            "name": "Beranda",
            "item": "{{ route('landing.id') }}"
        },
        {
            "@@type": "ListItem",
            "position": 2,
            "name": "Artikel",
            "item": "{{ route('blog.index.id') }}"
        }
    ]
}
</script>
@endsection

@section('content')
@php
    $contact = data_get(config('landing_metrics'), 'contact', []);
    $whatsappLink = $contact['whatsapp_link'] ?? 'https://wa.me/6283879602855';
    $featuredArticle = $articles->firstWhere('is_featured', true) ?? $articles->first();
    $listArticles = $featuredArticle ? $articles->filter(fn($a) => $a->id !== $featuredArticle->id) : $articles;
@endphp

{{-- Hero / Header --}}
<section class="relative overflow-hidden pt-28 pb-14 bg-gradient-to-br from-gray-900 via-gray-900 to-gray-800">
    <div class="container-wide">
        <span class="section-badge mb-4">Blog & Artikel</span>
        <h1 class="section-title mb-3 text-gray-100">Artikel & Berita</h1>
        <p class="section-description mb-0 text-gray-400">Update terbaru seputar perizinan, regulasi OSS-RBA, lingkungan hidup, dan tips legalitas bisnis Indonesia.</p>
    </div>
</section>

{{-- Featured Article --}}
@if($featuredArticle)
<section class="section-sm bg-[var(--bg-base)] border-b border-white/10">
    <div class="container-wide">
        <a href="{{ route('blog.article', $featuredArticle->slug) }}" class="group grid lg:grid-cols-2 gap-8 items-center magazine-card p-0 overflow-hidden hover:shadow-xl transition-all">
            <div class="relative h-72 lg:h-full min-h-[280px] overflow-hidden">
                @if($featuredArticle->featured_image)
                    <img src="{{ Storage::url($featuredArticle->featured_image) }}" alt="{{ $featuredArticle->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                @else
                    <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-blue-600 to-blue-900">
                        <i class="fas fa-newspaper text-white/30 text-5xl"></i>
                    </div>
                @endif
                <div class="absolute top-4 left-4">
                    <span class="px-3 py-1 rounded-full text-xs font-bold text-white bg-[var(--color-secondary)]">
                        <i class="fas fa-star mr-1"></i>Featured
                    </span>
                </div>
            </div>
            <div class="p-7 lg:py-10">
                <div class="mb-3">
                    <span class="section-badge">{{ $featuredArticle->category_label }}</span>
                </div>
                <h2 class="text-2xl md:text-3xl font-bold mb-3 leading-tight text-gray-100 group-hover:text-[var(--color-secondary)] transition-colors">
                    {{ $featuredArticle->title }}
                </h2>
                <p class="text-base mb-5 line-clamp-3 text-gray-400">{{ $featuredArticle->excerpt }}</p>
                <div class="flex items-center gap-5 text-sm mb-5 text-gray-500">
                    <span><i class="fas fa-calendar mr-1"></i>{{ $featuredArticle->published_at->format('d M Y') }}</span>
                    <span><i class="fas fa-clock mr-1"></i>{{ $featuredArticle->reading_time }} menit baca</span>
                    <span><i class="fas fa-eye mr-1"></i>{{ number_format($featuredArticle->views_count) }}</span>
                </div>
                <span class="text-blue-400 hover:text-blue-300 inline-flex items-center gap-2 font-semibold transition-colors">Baca Artikel <i class="fas fa-arrow-right transition-transform group-hover:translate-x-1"></i></span>
            </div>
        </a>
    </div>
</section>
@endif

{{-- Search & Filter --}}
<section class="section-sm bg-gray-900 border-b border-white/10">
    <div class="container-wide">
        <form action="{{ route('blog.index.id') }}" method="GET" class="flex flex-col sm:flex-row gap-3">
            <div class="flex-1 relative">
                <i class="fas fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-sm text-gray-500"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari artikel perizinan, regulasi..." class="w-full pl-10 pr-4 py-2.5 rounded-xl border text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition bg-gray-800 border-white/10 text-gray-100 placeholder-gray-500">
            </div>
            <select name="category" class="px-4 py-2.5 rounded-xl border text-sm focus:outline-none transition bg-gray-800 border-white/10 text-gray-100">
                <option value="">Semua Kategori</option>
                @foreach($categories as $key => $label)
                <option value="{{ $key }}" {{ request('category') == $key ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
            <select name="sort" class="px-4 py-2.5 rounded-xl border text-sm focus:outline-none transition bg-gray-800 border-white/10 text-gray-100">
                <option value="published_at" {{ request('sort', 'published_at') == 'published_at' ? 'selected' : '' }}>Terbaru</option>
                <option value="popular" {{ request('sort') == 'popular' ? 'selected' : '' }}>Terpopuler</option>
            </select>
            <button type="submit" class="btn btn-primary btn-sm shrink-0">
                <i class="fas fa-search"></i> Cari
            </button>
        </form>
    </div>
</section>

{{-- Articles Grid + Sidebar --}}
<section class="section">
    <div class="container-wide">
        <div class="grid lg:grid-cols-3 gap-10">

            {{-- Articles Grid --}}
            <div class="lg:col-span-2">
                @if($listArticles->count() > 0)
                <div class="grid md:grid-cols-2 gap-6">
                    @foreach($listArticles as $article)
                    <article class="card h-full flex flex-col group overflow-hidden p-0">
                        <a href="{{ route('blog.article', $article->slug) }}" class="block relative overflow-hidden h-[180px]">
                            @if($article->featured_image)
                                <img src="{{ Storage::url($article->featured_image) }}" alt="{{ $article->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                            @else
                                <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-gray-800 to-gray-700">
                                    <i class="fas fa-newspaper text-5xl text-gray-600"></i>
                                </div>
                            @endif
                            @if($article->is_featured)
                                <span class="absolute top-3 left-3 px-2 py-0.5 rounded text-[10px] font-bold text-white bg-[var(--color-secondary)]">
                                    <i class="fas fa-star mr-0.5"></i>Featured
                                </span>
                            @endif
                        </a>
                        <div class="p-5 flex-1 flex flex-col">
                            <div class="mb-2">
                                <a href="{{ route('blog.category', $article->category) }}" class="section-badge text-[10px] hover:opacity-80 transition">{{ $article->category_label }}</a>
                            </div>
                            <h3 class="text-base font-bold mb-2 line-clamp-2 leading-snug text-gray-100">
                                <a href="{{ route('blog.article', $article->slug) }}" class="hover:text-[var(--color-secondary)] transition-colors">
                                    {{ $article->title }}
                                </a>
                            </h3>
                            <p class="text-sm mb-4 line-clamp-2 flex-1 text-gray-400">{{ $article->excerpt }}</p>
                            <div class="flex items-center justify-between text-xs pt-3 border-t border-white/10 mt-auto text-gray-500">
                                <span><i class="fas fa-calendar mr-1"></i>{{ $article->published_at->format('d M Y') }}</span>
                                <span><i class="fas fa-clock mr-1"></i>{{ $article->reading_time }} min</span>
                                <span><i class="fas fa-eye mr-1"></i>{{ number_format($article->views_count) }}</span>
                            </div>
                        </div>
                    </article>
                    @endforeach
                </div>

                @if($articles->hasPages())
                <div class="mt-10">
                    {{ $articles->links() }}
                </div>
                @endif
                @else
                <div class="text-center py-20 card">
                    <i class="fas fa-search text-5xl mb-4 text-gray-500"></i>
                    <p class="text-lg font-semibold mb-1 text-gray-100">Tidak ada artikel ditemukan</p>
                    <p class="text-sm mb-5 text-gray-400">Coba kata kunci lain atau lihat semua kategori.</p>
                    <a href="{{ route('blog.index.id') }}" class="btn btn-primary btn-sm">Lihat Semua Artikel</a>
                </div>
                @endif
            </div>

            {{-- Sidebar --}}
            <aside class="lg:col-span-1 space-y-6">

                {{-- Kategori --}}
                <div class="card">
                    <h4 class="text-sm font-bold uppercase tracking-widest mb-4 text-gray-500">Kategori</h4>
                    <ul class="space-y-2">
                        <li>
                            <a href="{{ route('blog.index.id') }}" class="flex items-center justify-between text-sm py-1.5 transition {{ !request('category') ? 'font-semibold' : '' }} text-gray-400">
                                <span><i class="fas fa-layer-group mr-2 text-xs text-gray-500"></i>Semua Artikel</span>
                                <span class="text-xs px-2 py-0.5 rounded-full bg-gray-800 text-gray-500">{{ $articles->total() }}</span>
                            </a>
                        </li>
                        @foreach($categories as $key => $label)
                        <li>
                            <a href="{{ route('blog.index.id', ['category' => $key]) }}" class="flex items-center justify-between text-sm py-1.5 transition {{ request('category') == $key ? 'font-semibold text-[var(--color-secondary)]' : 'text-gray-400 hover:text-[var(--color-secondary)]' }}">
                                <span><i class="fas fa-tag mr-2 text-xs text-gray-500"></i>{{ $label }}</span>
                            </a>
                        </li>
                        @endforeach
                    </ul>
                </div>

                {{-- Artikel Populer --}}
                @if($articles->count() > 0)
                <div class="card">
                    <h4 class="text-sm font-bold uppercase tracking-widest mb-4 text-gray-500">Artikel Populer</h4>
                    <ul class="space-y-4">
                        @foreach($articles->sortByDesc('views_count')->take(4) as $pop)
                        <li>
                            <a href="{{ route('blog.article', $pop->slug) }}" class="flex items-start gap-3 group">
                                <div class="flex-shrink-0 w-16 h-12 rounded-lg overflow-hidden">
                                    @if($pop->featured_image)
                                        <img src="{{ Storage::url($pop->featured_image) }}" alt="{{ $pop->title }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center bg-gray-800">
                                            <i class="fas fa-newspaper text-lg text-gray-600"></i>
                                        </div>
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-semibold line-clamp-2 leading-snug text-gray-100 group-hover:text-[var(--color-secondary)] transition-colors">{{ $pop->title }}</p>
                                    <p class="text-xs mt-1 text-gray-500">{{ $pop->published_at->format('d M Y') }}</p>
                                </div>
                            </a>
                        </li>
                        @endforeach
                    </ul>
                </div>
                @endif

                {{-- CTA Konsultasi --}}
                <div class="card text-center bg-[var(--surface-dark)]">
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center mx-auto mb-4 bg-orange-500/20">
                        <i class="fas fa-comments-dollar text-xl leading-none text-[var(--color-secondary)]" aria-hidden="true"></i>
                    </div>
                    <h4 class="font-bold text-white mb-2">Butuh Bantuan Perizinan?</h4>
                    <p class="text-sm mb-4 text-white/65">Konsultasikan kebutuhan izin usaha Anda secara gratis bersama tim kami.</p>
                    <a href="{{ $whatsappLink }}" target="_blank" rel="noopener noreferrer" class="btn btn-success w-full btn-sm">
                        <i class="fab fa-whatsapp" aria-hidden="true"></i> Chat WhatsApp
                    </a>
                    <a href="{{ route('landing.service-inquiry.create') }}" class="btn btn-secondary w-full btn-sm mt-2">
                        <i class="fas fa-robot text-base flex-shrink-0 leading-none" aria-hidden="true"></i> Analisis AI Gratis
                    </a>
                </div>

            </aside>
        </div>
    </div>
</section>
@endsection
