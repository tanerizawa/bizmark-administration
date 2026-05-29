@extends('landing.layout')

@php
    $locale = app()->getLocale();
    $isEn = $locale === 'en';
    $pageTitle = $isEn ? 'Insights & Expertise' : 'Wawasan & Pengetahuan';
    $pageDescription = $isEn
        ? 'In-depth articles on Indonesian permit regulations, real case studies, and compliance guides from the Bizmark.ID team.'
        : 'Artikel mendalam tentang regulasi perizinan Indonesia, studi kasus nyata, dan panduan kepatuhan dari tim Bizmark.ID.';

    $blogIndexRoute = $isEn ? 'blog.index.en' : 'blog.index.id';
    $blogArticleRoute = $isEn ? 'blog.article.en' : 'blog.article.id';
    $blogCategoryRoute = $isEn ? 'blog.category.en' : 'blog.category.id';

    $categoryLabels = \App\Helpers\BlogHelper::categoryLabels($isEn);
    $fmtCategory = function($cat) use ($categoryLabels) {
        return $categoryLabels[$cat] ?? \Illuminate\Support\Str::title(str_replace('-', ' ', (string)$cat));
    };

    // Collect unique categories from current page for filter chips
    $availableCategories = $articles->pluck('category')->unique()->filter()->values();

    $totalArticles = $articles->total();
    $featured = $articles->first();
    $rest = $articles->slice(1);

    // Pre-compute search data JSON to avoid Blade parser issue with multi-line @json + closures
    $searchDataJson = json_encode($articles->getCollection()->map(fn($a) => [
        'slug' => $a->slug,
        'title' => $a->title,
        'excerpt' => $a->excerpt,
        'category' => $a->category,
    ])->values());
@endphp

@section('title', $pageTitle . ' — Bizmark.ID')
@section('meta_description', $pageDescription)

@section('structured_data')
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@type": "Blog",
    "name": "{{ $pageTitle }} — Bizmark.ID",
    "description": @json($pageDescription),
    "url": "{{ url()->current() }}",
    "publisher": {
        "@@type": "Organization",
        "name": "Bizmark.ID",
        "logo": "{{ asset('images/logo.png') }}"
    }
}
</script>
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@type": "BreadcrumbList",
    "itemListElement": [
        { "@@type": "ListItem", "position": 1, "name": "{{ $isEn ? 'Home' : 'Beranda' }}", "item": "{{ url('/') }}" },
        { "@@type": "ListItem", "position": 2, "name": "{{ $pageTitle }}", "item": "{{ url()->current() }}" }
    ]
}
</script>
@endsection

@push('styles')
<style>
.blog-search-input {
    background: var(--bg-raised);
    color: var(--text-primary);
    border-color: var(--border-subtle);
}
.blog-search-input::placeholder { color: var(--text-tertiary); }
.blog-search-input:focus {
    border-color: rgba(var(--accent-rgb), .5);
    box-shadow: 0 0 0 3px rgba(var(--accent-rgb), .15);
}
</style>
@endpush

@section('content')
<section class="section-v2" style="background: var(--surface);">
    <div class="container-wide">
        <x-ui.breadcrumb
            :items="[
                ['label' => $isEn ? 'Home' : 'Beranda', 'url' => url('/')],
                ['label' => $isEn ? 'Blog' : 'Blog'],
            ]"
            separator="slash"
            class="mb-6"
        />

        <div class="max-w-4xl">
            <span class="eyebrow mb-6">{{ $isEn ? 'Insights & Expertise' : 'Wawasan & Pengetahuan' }}</span>
            <h1 class="display-xl mt-2 mb-6" style="color: var(--text-primary);">
                {{ $isEn
                    ? 'Regulatory insight, written by practitioners.'
                    : 'Wawasan regulasi dari para praktisi perizinan.' }}
            </h1>
            <p class="text-lg leading-relaxed max-w-3xl" style="color: var(--text-secondary);">
                {{ $isEn
                    ? 'In-depth articles on Indonesian permit regulations, real case studies, and step-by-step compliance guides from our consultant team.'
                    : 'Artikel mendalam tentang regulasi perizinan Indonesia, studi kasus nyata, dan panduan kepatuhan langkah demi langkah dari tim konsultan kami.' }}
            </p>

            @if($totalArticles > 0)
                <div class="mt-8 text-sm" style="color: var(--text-tertiary);">
                    <i class="fas fa-newspaper mr-2" style="color: var(--accent);"></i>
                    <strong class="font-semibold" style="color: var(--text-primary);">{{ number_format($totalArticles) }}</strong>
                    {{ $isEn ? 'published articles' : 'artikel terpublikasi' }}
                </div>
            @endif
        </div>
    </div>
</section>

{{-- CATEGORY CHIPS (filter) --}}
@if($availableCategories->count() > 1)
<section class="section-v2-sm" style="background: var(--surface); border-top: 1px solid var(--border-subtle); border-bottom: 1px solid var(--border-subtle);">
    <div class="container-wide">
        <div class="flex flex-wrap items-center gap-2">
            <span class="text-xs font-bold uppercase tracking-[.15em] mr-3" style="color: var(--text-tertiary);">
                {{ $isEn ? 'Browse by' : 'Jelajahi' }}:
            </span>
            <a href="{{ route($blogIndexRoute) }}" class="cert-badge" style="background: var(--text-primary); color: #fff; border-color: var(--text-primary);">
                {{ $isEn ? 'All' : 'Semua' }}
            </a>
            @foreach($availableCategories as $cat)
                <a href="{{ route($blogCategoryRoute, $cat) }}" class="cert-badge">
                    {{ $fmtCategory($cat) }}
                </a>
            @endforeach
        </div>
    </div>
</section>
@endif

@if($articles->isEmpty())
    {{-- EMPTY STATE --}}
    <section class="section-v2">
        <div class="container-wide">
            <x-ui.empty-state
                icon="fa-solid fa-newspaper"
                :title="$isEn ? 'No articles yet' : 'Belum ada artikel'"
                :description="$isEn ? 'Check back soon for new insights.' : 'Nantikan insight terbaru dari tim kami.'"
                size="lg"
            />
        </div>
    </section>
@else
    {{-- FEATURED + GRID --}}
    <section class="section-v2"
             x-data="blogSearch()"
             x-init="initSearch({{ $searchDataJson }})">
        <div class="container-wide">

            {{-- Search Bar --}}
            <div class="mb-10"
                 x-show="!searching || query.length > 0"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 -translate-y-2"
                 x-transition:enter-end="opacity-100 translate-y-0">
                <div class="relative max-w-xl">
                    <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-sm" style="color: var(--text-tertiary);"
                       :class="query.length > 0 ? '!text-[var(--accent)]' : ''"></i>
                    <input type="text"
                           x-ref="searchInput"
                           x-model="query"
                           @keydown.escape="clearSearch()"
                           @keydown.slash.prevent="$refs.searchInput.focus()"
                           placeholder="{{ $isEn ? 'Search articles by title, topic or category...' : 'Cari artikel berdasarkan judul, topik, atau kategori...' }}"
                           class="blog-search-input w-full pl-10 pr-10 py-3 rounded-xl border text-sm transition focus:outline-none">
                    <button x-show="query.length > 0"
                            @click="clearSearch()"
                            class="absolute right-3 top-1/2 -translate-y-1/2 w-5 h-5 flex items-center justify-center rounded-full text-xs transition"
                            style="background: var(--border-subtle); color: var(--text-tertiary);"
                            type="button"
                            aria-label="{{ $isEn ? 'Clear search' : 'Hapus pencarian' }}">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="flex items-center gap-2 mt-2 text-xs" style="color: var(--text-tertiary);">
                    <span>
                        <kbd class="rounded px-1.5 py-0.5 font-sans" style="background: var(--bg-raised); border: 1px solid var(--border-subtle); color: var(--text-secondary);">/</kbd>
                        {{ $isEn ? 'to search' : 'untuk mencari' }}
                    </span>
                    <span class="mx-1">·</span>
                    <span x-show="query.length > 0" x-text="`${filteredCount} ${'{{ $isEn ? 'results' : 'hasil' }}'}`"></span>
                    <span x-show="query.length > 0 && filteredCount === 0" style="color: var(--accent);">{{ $isEn ? 'No matching articles' : 'Tidak ada artikel yang cocok' }}</span>
                </div>
            </div>

            {{-- No results state --}}
            <div x-show="query.length > 0 && filteredCount === 0"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100">
                <x-ui.empty-state
                    icon="fa-solid fa-search"
                    :title="$isEn ? 'No results found' : 'Hasil tidak ditemukan'"
                    :description="$isEn ? 'Try different keywords or browse by category.' : 'Coba kata kunci lain atau jelajahi berdasarkan kategori.'"
                    size="lg"
                />
            </div>

            {{-- Featured (page 1 only) --}}
            @if($articles->onFirstPage() && $featured)
                <a href="{{ route($blogArticleRoute, $featured->slug) }}"
                   class="article-card featured block mb-12 grid grid-cols-[1.3fr_1fr] gap-10 items-center max-md:grid-cols-1 max-md:gap-5"
                   x-show="matches('{{ $featured->slug }}')">
                    <div class="article-image">
                        @if($featured->featured_image)
                            <img src="{{ \Illuminate\Support\Str::startsWith($featured->featured_image, ['http','/']) ? $featured->featured_image : asset('storage/' . $featured->featured_image) }}"
                                 alt="{{ $featured->title }}"
                                 loading="eager" fetchpriority="high" width="900" height="675">
                        @else
                            <div class="w-full h-full flex items-center justify-center bg-ink-gradient">
                                <i class="fas fa-newspaper text-5xl text-white/40"></i>
                            </div>
                        @endif
                    </div>
                    <div>
                        <div class="article-meta mb-3">
                            <span class="article-cat">{{ $fmtCategory($featured->category) }}</span>
                            <span>·</span>
                            <time datetime="{{ optional($featured->published_at)->toIso8601String() }}">
                                {{ optional($featured->published_at)->translatedFormat('d M Y') }}
                            </time>
                            @if($featured->reading_time)
                                <span>·</span>
                                <span>{{ $featured->reading_time }} {{ $isEn ? 'min read' : 'menit baca' }}</span>
                            @endif
                        </div>
                        <h2 class="article-title mb-4">{{ $featured->title }}</h2>
                        @if($featured->excerpt)
                            <p class="article-excerpt mb-6">{{ \Illuminate\Support\Str::limit($featured->excerpt, 220) }}</p>
                        @endif
                        <span class="link-primary font-semibold text-sm inline-flex items-center gap-2">
                            {{ $isEn ? 'Read article' : 'Baca artikel' }}
                            <i class="fas fa-arrow-right text-xs flex-shrink-0 leading-none" aria-hidden="true"></i>
                        </span>
                    </div>
                </a>
            @endif

            {{-- Grid: rest of articles --}}
            @php $gridArticles = $articles->onFirstPage() ? $rest : $articles->getCollection(); @endphp
            @if($gridArticles->count() > 0)
                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8 grid-equal">
                    @foreach($gridArticles as $article)
                        <a href="{{ route($blogArticleRoute, $article->slug) }}" class="article-card"
                           x-show="matches('{{ $article->slug }}')">
                            <div class="article-image">
                                @if($article->featured_image)
                                    <img src="{{ \Illuminate\Support\Str::startsWith($article->featured_image, ['http','/']) ? $article->featured_image : asset('storage/' . $article->featured_image) }}"
                                         alt="{{ $article->title }}"
                                         loading="lazy" width="600" height="375">
                                @else
                                    <div class="w-full h-full flex items-center justify-center bg-ink-gradient">
                                        <i class="fas fa-newspaper text-3xl text-white/40"></i>
                                    </div>
                                @endif
                            </div>
                            <div class="flex flex-col flex-1">
                                <div class="article-meta mb-2">
                                    <span class="article-cat">{{ $fmtCategory($article->category) }}</span>
                                    <span>·</span>
                                    <time datetime="{{ optional($article->published_at)->toIso8601String() }}">
                                        {{ optional($article->published_at)->translatedFormat('d M Y') }}
                                    </time>
                                </div>
                                <h3 class="article-title mb-3">{{ $article->title }}</h3>
                                @if($article->excerpt)
                                    <p class="article-excerpt flex-1">{{ \Illuminate\Support\Str::limit($article->excerpt, 140) }}</p>
                                @endif
                                @if($article->reading_time)
                                    <div class="text-xs mt-4 pt-4" style="color: var(--text-tertiary); border-top: 1px solid var(--border-subtle);">
                                        <i class="far fa-clock mr-1"></i>
                                        {{ $article->reading_time }} {{ $isEn ? 'min read' : 'menit baca' }}
                                    </div>
                                @endif
                            </div>
                        </a>
                    @endforeach
                </div>
            @endif

            {{-- Pagination --}}
            @if($articles->hasPages())
                <div class="mt-12" x-show="query.length === 0">
                    {{ $articles->links() }}
                </div>
            @endif
        </div>
    </section>

    {{-- Newsletter CTA at end --}}
    <div x-show="query.length === 0">
        @include('landing.sections.v2.newsletter', ['locale' => $locale])
    </div>
@endif

@endsection

@push('scripts')
<script>
/**
 * Alpine.js blog search component.
 * Provides live client-side filtering of articles by title, excerpt, or category.
 */
window.blogSearch = function() {
    return {
        query: '',
        searching: false,
        articles: [],

        /** Initialize with serialized article data from the server. */
        initSearch(articles) {
            this.articles = articles;
        },

        /** Number of articles matching the current query. */
        get filteredCount() {
            if (!this.query || !this.query.trim()) return this.articles.length;
            const q = this.query.toLowerCase().trim();
            return this.articles.filter(a =>
                a.title.toLowerCase().includes(q) ||
                (a.excerpt && a.excerpt.toLowerCase().includes(q)) ||
                (a.category && a.category.toLowerCase().includes(q))
            ).length;
        },

        /** Check if a specific article (by slug) matches the current query. */
        matches(slug) {
            if (!this.query || !this.query.trim()) return true;
            const q = this.query.toLowerCase().trim();
            return this.articles.some(a =>
                a.slug === slug && (
                    a.title.toLowerCase().includes(q) ||
                    (a.excerpt && a.excerpt.toLowerCase().includes(q)) ||
                    (a.category && a.category.toLowerCase().includes(q))
                )
            );
        },

        /** Clear search query and re-focus the search input. */
        clearSearch() {
            this.query = '';
            this.searching = false;
            this.$refs.searchInput?.focus();
        }
    };
};
</script>
@endpush
