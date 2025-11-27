@extends('landing.layout')

@section('content')
@php
    $topicFilters = [
        ['label' => 'Perizinan Industri', 'slug' => 'perizinan'],
        ['label' => 'Regulasi Lingkungan', 'slug' => 'regulasi'],
        ['label' => 'Kepatuhan & AMDAL', 'slug' => 'lingkungan'],
        ['label' => 'Tips Operasional', 'slug' => 'tips'],
    ];
@endphp

<!-- Breadcrumbs -->
<section class="pt-24 pb-4 px-4 bg-gray-50 border-b border-gray-100">
    <div class="container mx-auto">
        <nav class="flex items-center space-x-2 text-sm text-gray-500" data-aos="fade-right">
            <a href="/" class="text-gray-600 hover:text-primary transition">
                <i class="fas fa-home mr-1"></i>Beranda
            </a>
            <span class="text-gray-400">/</span>
            <span class="text-gray-900 font-medium">Blog & Artikel</span>
        </nav>
    </div>
</section>

<!-- Blog Header -->
<section class="relative py-16 px-4 overflow-hidden bg-white">
    <div class="absolute inset-y-0 left-1/2 -translate-x-1/2 w-[90%] max-w-5xl bg-gradient-to-br from-primary/5 via-white to-secondary/5 blur-3xl opacity-60 pointer-events-none"></div>
    <div class="container mx-auto relative text-center space-y-5">
        <p class="text-sm font-semibold tracking-[0.4em] uppercase text-primary" data-aos="fade-up">
            Insight Perizinan
        </p>
        <h1 class="text-4xl md:text-5xl font-bold leading-tight text-gray-900" data-aos="fade-up" data-aos-delay="100">
            Wawasan Terbaru untuk Tim Legal & Operasional
        </h1>
        <p class="text-lg text-gray-600 max-w-3xl mx-auto" data-aos="fade-up" data-aos-delay="200">
            Artikel singkat seputar regulasi, proses perizinan, dan praktik tata kelola agar perusahaan Anda selalu gesit menghadapi perubahan.
        </p>
        <div class="flex flex-wrap justify-center gap-2 text-sm" data-aos="fade-up" data-aos-delay="300">
            <a href="{{ route('blog.index') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-primary text-white font-semibold shadow-soft hover:-translate-y-0.5 transition">
                <i class="fas fa-fire"></i> Semua Artikel
            </a>
            @foreach($topicFilters as $filter)
                <a href="{{ route('blog.category', $filter['slug']) }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-gray-100 text-gray-700 hover:bg-primary/10 hover:text-primary transition font-medium">
                    <i class="fas fa-hashtag text-primary"></i>{{ $filter['label'] }}
                </a>
            @endforeach
        </div>
    </div>
</section>

<!-- Articles Grid -->
<section class="py-14 px-4 bg-gray-50">
    <div class="container mx-auto">
        @if($articles->count() > 0)
            <div class="flex flex-wrap items-center justify-between gap-3 mb-8 text-sm text-gray-500" data-aos="fade-up">
                <div class="flex items-center gap-3">
                    <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white text-xs font-semibold uppercase tracking-wide text-primary/80 border border-primary/20">
                        <i class="fas fa-rss text-primary"></i> Arsip Terbaru
                    </span>
                    <span class="text-gray-600">{{ $articles->total() }} artikel tersedia</span>
                </div>
                <div class="flex items-center gap-2">
                    <i class="far fa-calendar text-primary"></i>
                    <span class="text-gray-600">Update {{ now()->translatedFormat('F Y') }}</span>
                </div>
            </div>
            <div class="grid md:grid-cols-2 xl:grid-cols-3 gap-6 mb-10">
                @foreach($articles as $article)
                    <article class="group relative flex flex-col bg-white rounded-2xl overflow-hidden shadow-soft hover:shadow-soft-lg transition-all duration-300" data-aos="fade-up" data-aos-delay="{{ ($loop->index % 3) * 80 }}">
                        <div class="relative h-48 overflow-hidden">
                            @if($article->featured_image)
                                <img src="{{ Storage::url($article->featured_image) }}" alt="{{ $article->title }}" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105" loading="lazy">
                            @else
                                <div class="w-full h-full bg-gradient-to-br from-primary/10 to-secondary/10 flex items-center justify-center text-primary/60">
                                    <i class="fas fa-newspaper text-4xl"></i>
                                </div>
                            @endif
                            <div class="absolute inset-0 bg-gradient-to-t from-black/30 via-transparent to-transparent pointer-events-none"></div>
                            <div class="absolute top-3 left-3 flex items-center gap-2">
                                <a href="{{ route('blog.category', $article->category) }}" class="px-3 py-1 rounded-full bg-white/80 text-xs font-semibold tracking-wide uppercase text-gray-800">
                                    {{ $article->category_label }}
                                </a>
                                @if($loop->first)
                                    <span class="px-3 py-1 rounded-full bg-amber-200 text-xs font-semibold text-amber-900 uppercase tracking-wide">Terbaru</span>
                                @endif
                            </div>
                        </div>
                        <div class="flex-1 flex flex-col p-6">
                            <div class="flex items-center gap-4 text-xs text-gray-500 mb-3">
                                <span><i class="far fa-calendar mr-1 text-primary"></i>{{ $article->published_at->format('d M Y') }}</span>
                                <span><i class="far fa-clock mr-1 text-primary"></i>{{ $article->reading_time }} min</span>
                                <span><i class="far fa-eye mr-1 text-primary"></i>{{ number_format($article->views) }}</span>
                            </div>
                            <h3 class="text-xl font-semibold text-gray-900 mb-2 leading-snug">
                                <a href="{{ route('blog.article', $article->slug) }}" class="hover:text-primary transition">
                                    {{ $article->title }}
                                </a>
                            </h3>
                            <p class="text-sm text-gray-600 flex-1">
                                {{ Str::limit($article->excerpt, 130) }}
                            </p>
                            @php
                                $tagList = is_array($article->tags)
                                    ? $article->tags
                                    : (is_string($article->tags) ? array_filter(array_map('trim', explode(',', $article->tags))) : []);
                            @endphp
                            @if(!empty($tagList))
                                <div class="flex flex-wrap gap-2 mt-3">
                                    @foreach(array_slice($tagList, 0, 3) as $tag)
                                        <a href="{{ route('blog.tag', $tag) }}" class="text-xs px-3 py-1 rounded-full bg-gray-100 text-gray-600 hover:bg-primary/10 hover:text-primary transition">
                                            #{{ $tag }}
                                        </a>
                                    @endforeach
                                </div>
                            @endif
                            <div class="mt-5 pt-4 border-t border-gray-100 flex items-center justify-between text-sm">
                                <span class="text-gray-500">Oleh {{ $article->author_name ?? 'Tim Bizmark.ID' }}</span>
                                <a href="{{ route('blog.article', $article->slug) }}" class="inline-flex items-center gap-2 text-primary font-semibold" aria-label="Baca {{ $article->title }}">
                                    Selengkapnya
                                    <i class="fas fa-arrow-right text-xs transition-transform group-hover:translate-x-1"></i>
                                </a>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>

            @if($articles->hasPages())
                <div class="flex justify-center" data-aos="fade-up">
                    <div class="blog-pagination">
                        {{ $articles->withQueryString()->links('vendor.pagination.tailwind') }}
                    </div>
                </div>
            @endif
        @else
            <div class="text-center py-20 space-y-4" data-aos="fade-up">
                <div class="w-16 h-16 mx-auto rounded-2xl bg-white flex items-center justify-center text-primary shadow-soft">
                    <i class="fas fa-newspaper text-2xl"></i>
                </div>
                <h3 class="text-2xl font-semibold text-gray-900">Belum Ada Artikel</h3>
                <p class="text-gray-600 max-w-xl mx-auto">Tim konten kami sedang menyiapkan insight terbaru. Silakan kembali beberapa saat lagi atau hubungi kami untuk studi kasus privat.</p>
            </div>
        @endif
    </div>
</section>

<!-- Categories & CTA -->
<section class="py-14 px-4 bg-white border-t border-gray-100">
    <div class="container mx-auto">
        <div class="grid lg:grid-cols-2 gap-8">
            <div class="rounded-3xl p-8 bg-gray-50 border border-gray-100" data-aos="fade-right">
                <h3 class="text-2xl font-semibold mb-4 text-gray-900 flex items-center gap-3">
                    <i class="fas fa-folder-open text-primary"></i>Topik Populer
                </h3>
                <p class="text-sm text-gray-500 mb-4">Filter cepat untuk menemukan insight sesuai kebutuhan tim Anda.</p>
                <div class="flex flex-wrap gap-2">
                    @foreach($topicFilters as $filter)
                        <a href="{{ route('blog.category', $filter['slug']) }}" class="px-4 py-2 rounded-full bg-white hover:bg-primary/10 text-sm font-semibold text-gray-700 hover:text-primary transition border border-gray-200">
                            {{ $filter['label'] }}
                        </a>
                    @endforeach
                    <a href="{{ route('blog.index') }}" class="px-4 py-2 rounded-full border border-gray-300 text-sm font-semibold text-gray-600 hover:bg-gray-100 transition">
                        Semua Artikel
                    </a>
                </div>
            </div>
            <div class="rounded-3xl p-8 bg-gradient-to-r from-primary/10 via-white to-secondary/10 border border-gray-100" data-aos="fade-left">
                <h3 class="text-2xl font-semibold mb-2 text-gray-900">Butuh Konsultasi Perizinan Cepat?</h3>
                <p class="text-gray-600 mb-5">Hubungkan tim Anda dengan konsultan Bizmark.ID untuk audit izin, AMDAL, atau compliance harian.</p>
                <div class="flex flex-wrap gap-3">
                    <a href="https://wa.me/6283879602855?text=Halo%20Bizmark.ID%2C%20saya%20ingin%20konsultasi" target="_blank" class="inline-flex items-center gap-2 px-6 py-3 rounded-2xl bg-primary text-white font-semibold shadow-soft hover:-translate-y-0.5 transition">
                        <i class="fab fa-whatsapp text-lg"></i>Konsultasi WhatsApp
                    </a>
                    <a href="{{ route('landing.service-inquiry.create') }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-2xl border border-primary/30 text-primary font-semibold hover:bg-primary/5 transition">
                        <i class="fas fa-envelope-open-text"></i>Ajukan Proposal
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
    .blog-pagination nav {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        background-color: rgba(255, 255, 255, 0.9);
        border: 1px solid rgba(15, 23, 42, 0.08);
        border-radius: 9999px;
        padding: 0.5rem 1rem;
        box-shadow: 0 6px 18px rgba(15, 23, 42, 0.08);
    }
    .blog-pagination nav span,
    .blog-pagination nav a {
        color: #475569;
        background: transparent;
        border: none;
        border-radius: 9999px;
        padding: 0.4rem 0.85rem;
        font-weight: 500;
        transition: background-color 0.2s ease, color 0.2s ease;
    }
    .blog-pagination nav span[aria-current="page"] > span {
        background: rgba(0, 119, 181, 0.15);
        color: #005582;
    }
    .blog-pagination nav span[aria-disabled="true"] {
        color: rgba(148, 163, 184, 0.8);
        cursor: not-allowed;
    }
    .blog-pagination nav a:hover {
        background-color: rgba(15, 23, 42, 0.05);
    }
</style>

@endsection
