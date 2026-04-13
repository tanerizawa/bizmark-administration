@extends('landing.layout')

@section('title', ($article->meta_title ?? $article->title) . ' - Bizmark.ID Blog')
@section('meta_title', $article->meta_title ?? $article->title)
@section('meta_description', $article->meta_description ?? $article->excerpt ?? '')
@section('meta_keywords', $article->meta_keywords ?? '')
@section('og_type', 'article')
@section('og_title', $article->meta_title ?? $article->title)
@section('og_description', $article->meta_description ?? $article->excerpt ?? '')
@section('og_image', $article->featured_image ? asset('storage/' . $article->featured_image) : '')
@section('twitter_title', $article->meta_title ?? $article->title)
@section('twitter_description', $article->meta_description ?? $article->excerpt ?? '')
@section('article_published_time', ($article->published_at ?? $article->created_at)->toIso8601String())
@section('article_modified_time', $article->updated_at->toIso8601String())
@section('article_section', $article->category_label ?? $article->category)

@section('content')
@php
    // Generate rich structured data (Article + Breadcrumb + FAQ + HowTo)
    $schemaService = app(\App\Services\SchemaMarkupService::class);
    $schemas = $schemaService->allSchemas($article);

    $blogIndexRoute = app()->getLocale() === 'en' ? 'blog.index.en' : 'blog.index.id';
    $blogCategoryRoute = app()->getLocale() === 'en' ? 'blog.category.en' : 'blog.category.id';
    $blogArticleRoute = app()->getLocale() === 'en' ? 'blog.article.en' : 'blog.article.id';
    $blogTagRoute = app()->getLocale() === 'en' ? 'blog.tag.en' : 'blog.tag.id';
    $whatsappLink = data_get(config('landing_metrics'), 'contact.whatsapp_link', 'https://wa.me/6283879602855');

    // --- Content Sanitization ---
    $cleanContent = $article->content ?? '';

    // 0. Strip code fence wrappers (```html ... ```) left by AI content generation
    $cleanContent = preg_replace('/^```\w*\s*\n?/m', '', $cleanContent);
    $cleanContent = preg_replace('/```\s*$/m', '', $cleanContent);
    $cleanContent = trim($cleanContent);

    // 1. Remove duplicate title (first h2 matching article title)
    $escapedTitle = preg_quote(e($article->title), '/');
    $cleanContent = preg_replace('/<h2>\s*' . $escapedTitle . '\s*<\/h2>/i', '', $cleanContent, 1);

    // 2. Convert {#anchor-id} in headings to proper id attributes
    // Use [^<]*? to prevent matching across multiple HTML tags
    $cleanContent = preg_replace_callback(
        '/<(h[2-4])([^>]*)>([^<]*?)\s*\{#([\w-]+)\}\s*<\/\1>/i',
        function($m) { return '<' . $m[1] . $m[2] . ' id="' . $m[4] . '">' . trim($m[3]) . '</' . $m[1] . '>'; },
        $cleanContent
    );

    // 3. Convert <p>---</p> to <hr>
    $cleanContent = preg_replace('/<p>\s*-{3,}\s*<\/p>/', '<hr>', $cleanContent);

    // 4. Remove empty paragraphs (<p>&nbsp;</p>, <p><br>&nbsp;</p>, <p><br></p>)
    $cleanContent = preg_replace('/<p>\s*(?:&nbsp;|<br\s*\/?>)*\s*<\/p>/', '', $cleanContent);

    // 5. Merge consecutive <ul> lists separated by whitespace
    $cleanContent = preg_replace('/<\/ul>\s*<ul>/i', '', $cleanContent);
    // Same for <ol>
    $cleanContent = preg_replace('/<\/ol>\s*<ol>/i', '', $cleanContent);

    // ─── 5.5 Convert inline Markdown remnants inside HTML content ───
    // Some articles have mixed HTML + Markdown (e.g., <p>...**bold**...</p>)
    
    // Convert **text** → <strong>text</strong> (only outside existing HTML tags)
    $cleanContent = preg_replace('/\*\*([^*\n]+?)\*\*/', '<strong>$1</strong>', $cleanContent);
    
    // Convert *text* → <em>text</em> (single asterisk, not already part of list)
    $cleanContent = preg_replace('/(?<!\*)\*(?!\*)([^*\n]+?)(?<!\*)\*(?!\*)/', '<em>$1</em>', $cleanContent);
    
    // Convert Markdown links [text](url) → <a href="url">text</a>
    $cleanContent = preg_replace(
        '/\[([^\]]+?)\]\(((?:https?:\/\/|\/)[^\)]+?)\)/',
        '<a href="$2">$1</a>',
        $cleanContent
    );
    
    // Convert standalone Markdown headings (## / ###) not inside HTML tags
    $cleanContent = preg_replace('/^### (.+)$/m', '<h3>$1</h3>', $cleanContent);
    $cleanContent = preg_replace('/^## (.+)$/m', '<h2>$1</h2>', $cleanContent);
    
    // Convert Markdown horizontal rules (---)
    $cleanContent = preg_replace('/^-{3,}\s*$/m', '<hr>', $cleanContent);
    
    // Convert Markdown unordered list blocks (- item) to <ul><li>
    $cleanContent = preg_replace_callback('/(?:^- .+$\n?)+/m', function($match) {
        $items = preg_split('/^- /m', $match[0], -1, PREG_SPLIT_NO_EMPTY);
        $lis = '';
        foreach ($items as $item) {
            $text = trim($item);
            if ($text !== '') {
                $lis .= '<li>' . $text . '</li>';
            }
        }
        return $lis ? '<ul>' . $lis . '</ul>' : '';
    }, $cleanContent);
    
    // Clean up: remove leftover "**" artifacts (e.g., "**Artikel Terkait:**")
    $cleanContent = str_replace(['**:', ':**'], [':', ':'], $cleanContent);
    
    // Convert inline code fences (```...```) to <pre> blocks for ASCII art/diagrams
    $cleanContent = preg_replace_callback(
        '/```(?:\w+)?(?:<br\s*\/?>|\n)(.*?)```/s',
        function($match) {
            $code = $match[1];
            // Convert <br> to newlines for proper pre display
            $code = preg_replace('/<br\s*\/?>/', "\n", $code);
            $code = trim($code);
            return '<pre class="bg-gray-50 p-4 rounded-lg overflow-x-auto text-sm">' . $code . '</pre>';
        },
        $cleanContent
    );

    // 6. Auto-generate heading IDs for TOC
    $tocItems = [];
    $cleanContent = preg_replace_callback(
        '/<(h[2-3])(\s[^>]*)?>(.+?)<\/\1>/i',
        function($m) use (&$tocItems) {
            $tag = $m[1];
            $attrs = $m[2] ?? '';
            $text = strip_tags($m[3]);
            // If no id already, generate one
            if (!preg_match('/id\s*=/', $attrs)) {
                $id = Str::slug($text);
                $attrs .= ' id="' . $id . '"';
            } else {
                preg_match('/id\s*=\s*"([^"]+)"/', $attrs, $idMatch);
                $id = $idMatch[1] ?? Str::slug($text);
            }
            $tocItems[] = ['tag' => $tag, 'id' => $id, 'text' => $text];
            return '<' . $tag . $attrs . '>' . $m[3] . '</' . $tag . '>';
        },
        $cleanContent
    );

    // 7. Wrap tables in responsive container
    $cleanContent = preg_replace('/<table/', '<div class="table-responsive"><table', $cleanContent);
    $cleanContent = preg_replace('/<\/table>/', '</table></div>', $cleanContent);
@endphp

{{-- Rich Structured Data (Article + Breadcrumb + FAQ + HowTo) --}}
@foreach($schemas as $schema)
<script type="application/ld+json">{!! json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
@endforeach

<!-- Reading Progress Bar -->
<div id="reading-progress" style="position:fixed;top:0;left:0;width:0;height:3px;background:linear-gradient(90deg,#0f172a,#0ea5e9);z-index:9999;transition:width .15s ease;"></div>

<!-- Breadcrumbs -->
<section class="pt-24 pb-4 px-4 bg-white border-b border-gray-100">
    <div class="container mx-auto max-w-5xl">
        <nav class="flex items-center space-x-2 text-sm text-gray-500">
            <a href="/" class="text-gray-600 hover:text-primary transition">
                <i class="fas fa-home mr-1"></i> Beranda
            </a>
            <span class="text-gray-300">/</span>
            <a href="{{ route($blogIndexRoute) }}" class="text-gray-600 hover:text-primary transition">
                Blog
            </a>
            <span class="text-gray-300">/</span>
            <a href="{{ route($blogCategoryRoute, $article->category) }}" class="text-gray-600 hover:text-primary transition">
                {{ $article->category_label }}
            </a>
            <span class="text-gray-300">/</span>
            <span class="text-gray-900 truncate max-w-xs font-medium">{{ Str::limit($article->title, 40) }}</span>
        </nav>
    </div>
</section>

<!-- Article Header -->
<section class="py-10 px-4 bg-gradient-to-b from-gray-50 to-white">
    <div class="container mx-auto max-w-5xl">
        <div class="max-w-3xl">
            <!-- Back Button -->
            <div class="mb-6">
                <a href="{{ route($blogIndexRoute) }}" class="inline-flex items-center text-gray-500 hover:text-primary transition text-sm font-medium">
                    <i class="fas fa-arrow-left mr-2"></i>Kembali ke Blog
                </a>
            </div>

            <!-- Category & Meta -->
            <div class="flex items-center gap-3 mb-5 flex-wrap">
                <a href="{{ route($blogCategoryRoute, $article->category) }}" class="px-3 py-1 bg-primary text-white rounded-full text-xs font-bold uppercase tracking-wide hover:bg-primary-dark transition">
                    {{ $article->category_label }}
                </a>
                <span class="text-gray-400 text-sm"><i class="far fa-calendar mr-1"></i>{{ $article->published_at->format('d F Y') }}</span>
                <span class="text-gray-400 text-sm"><i class="far fa-clock mr-1"></i>{{ $article->reading_time }} menit</span>
                <span class="text-gray-400 text-sm"><i class="far fa-eye mr-1"></i>{{ number_format($article->views_count) }} views</span>
            </div>

            <!-- Title -->
            <h1 class="text-3xl md:text-4xl lg:text-[2.75rem] font-bold mb-5 leading-[1.15] text-gray-900 tracking-tight">
                {{ $article->title }}
            </h1>

            <!-- Excerpt -->
            <p class="text-lg text-gray-600 leading-relaxed">
                {{ $article->excerpt }}
            </p>
        </div>
    </div>
</section>

<!-- Featured Image -->
@if($article->featured_image)
<section class="px-4 pb-8 bg-white">
    <div class="container mx-auto max-w-5xl">
        <div class="rounded-2xl overflow-hidden shadow-lg">
            <img src="{{ Storage::url($article->featured_image) }}" alt="{{ $article->title }}" class="w-full h-auto" loading="eager">
        </div>
    </div>
</section>
@endif

<!-- Article Body: Content + Sidebar TOC -->
<section class="py-10 px-4 bg-white">
    <div class="container mx-auto max-w-5xl">
        <div class="flex flex-col lg:flex-row gap-10">
            <!-- Main Content -->
            <div class="flex-1 min-w-0">
                <article class="article-prose">
                    {!! $cleanContent !!}
                </article>

                <!-- Tags -->
                @if($article->tags && count($article->tags) > 0)
                <div class="mt-10 pt-8 border-t border-gray-200">
                    <h4 class="text-sm font-bold uppercase tracking-wide text-gray-500 mb-4">Tags</h4>
                    <div class="flex flex-wrap gap-2">
                        @foreach($article->tags as $tag)
                        <a href="{{ route($blogTagRoute, $tag) }}" class="px-4 py-1.5 bg-gray-100 hover:bg-primary/10 text-gray-600 hover:text-primary rounded-full text-sm font-medium transition">
                            #{{ $tag }}
                        </a>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Share Buttons -->
                <div class="mt-8 pt-8 border-t border-gray-200">
                    <h4 class="text-sm font-bold uppercase tracking-wide text-gray-500 mb-4">Bagikan Artikel</h4>
                    <div class="flex gap-3">
                        <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(route($blogArticleRoute, $article->slug)) }}"
                           target="_blank" rel="noopener"
                           class="w-10 h-10 rounded-full flex items-center justify-center transition text-white" style="background:#1877F2;">
                            <i class="fab fa-facebook-f text-sm"></i>
                        </a>
                        <a href="https://twitter.com/intent/tweet?url={{ urlencode(route($blogArticleRoute, $article->slug)) }}&text={{ urlencode($article->title) }}"
                           target="_blank" rel="noopener"
                           class="w-10 h-10 rounded-full flex items-center justify-center transition text-white" style="background:#1DA1F2;">
                            <i class="fab fa-twitter text-sm"></i>
                        </a>
                        <a href="https://www.linkedin.com/shareArticle?mini=true&url={{ urlencode(route($blogArticleRoute, $article->slug)) }}&title={{ urlencode($article->title) }}"
                           target="_blank" rel="noopener"
                           class="w-10 h-10 rounded-full flex items-center justify-center transition text-white" style="background:#0A66C2;">
                            <i class="fab fa-linkedin-in text-sm"></i>
                        </a>
                        <a href="https://wa.me/?text={{ urlencode($article->title . ' - ' . route($blogArticleRoute, $article->slug)) }}"
                           target="_blank" rel="noopener"
                           class="w-10 h-10 rounded-full flex items-center justify-center transition text-white" style="background:#25D366;">
                            <i class="fab fa-whatsapp text-sm"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Sticky Sidebar TOC (desktop) -->
            @if(count($tocItems) >= 3)
            <aside class="hidden lg:block w-64 flex-shrink-0">
                <nav class="sticky top-24" id="toc-sidebar">
                    <h4 class="text-xs font-bold uppercase tracking-widest text-gray-400 mb-3">Daftar Isi</h4>
                    <ul class="space-y-1 border-l-2 border-gray-100">
                        @foreach($tocItems as $toc)
                        <li>
                            <a href="#{{ $toc['id'] }}"
                               class="toc-link block py-1 text-sm text-gray-500 hover:text-primary transition border-l-2 border-transparent hover:border-primary -ml-[2px] {{ $toc['tag'] === 'h3' ? 'pl-6' : 'pl-4' }}"
                               data-target="{{ $toc['id'] }}">
                                {{ Str::limit($toc['text'], 50) }}
                            </a>
                        </li>
                        @endforeach
                    </ul>
                </nav>
            </aside>
            @endif
        </div>
    </div>
</section>

<!-- Related Articles -->
@if($relatedArticles->count() > 0)
<section class="py-16 px-4 bg-gray-50">
    <div class="container mx-auto max-w-5xl">
        <h2 class="text-2xl md:text-3xl font-bold mb-10 text-gray-900">
            Artikel Terkait
        </h2>

        <div class="grid md:grid-cols-3 gap-6">
            @foreach($relatedArticles as $related)
            <article class="group bg-white rounded-2xl overflow-hidden shadow-soft hover:shadow-lg transition-all duration-300">
                <div class="relative h-44 overflow-hidden bg-gray-100">
                    @if($related->featured_image)
                    <img src="{{ Storage::url($related->featured_image) }}" alt="{{ $related->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" loading="lazy">
                    @else
                    <div class="w-full h-full bg-gradient-to-br from-primary/10 to-secondary/10 flex items-center justify-center">
                        <i class="fas fa-newspaper text-5xl text-gray-200"></i>
                    </div>
                    @endif
                </div>

                <div class="p-5">
                    <div class="flex items-center gap-3 mb-2 text-xs text-gray-400">
                        <span><i class="far fa-calendar mr-1"></i>{{ $related->published_at->format('d M Y') }}</span>
                        <span><i class="far fa-clock mr-1"></i>{{ $related->reading_time }} min</span>
                    </div>

                    <h3 class="text-base font-bold mb-2 leading-snug text-gray-900 group-hover:text-primary transition">
                        <a href="{{ route($blogArticleRoute, $related->slug) }}">{{ $related->title }}</a>
                    </h3>

                    <p class="text-gray-500 text-sm mb-3 line-clamp-2">
                        {{ Str::limit($related->excerpt, 100) }}
                    </p>

                    <a href="{{ route($blogArticleRoute, $related->slug) }}" class="text-primary hover:text-primary-dark font-semibold text-sm inline-flex items-center group/link">
                        Baca Artikel
                        <i class="fas fa-arrow-right ml-2 group-hover/link:translate-x-1 transition-transform"></i>
                    </a>
                </div>
            </article>
            @endforeach
        </div>
    </div>
</section>
@endif

<!-- CTA Section -->
<section class="py-16 px-4 bg-gradient-to-br from-gray-50 to-white">
    <div class="container mx-auto max-w-3xl">
        <div class="bg-white rounded-2xl p-10 md:p-12 text-center shadow-lg border border-gray-100">
            <h2 class="text-2xl md:text-3xl font-bold mb-3 text-gray-900">
                Butuh Konsultasi Perizinan?
            </h2>
            <p class="text-lg text-gray-600 mb-8">
                Tim ahli kami siap membantu Anda mengurus perizinan industri dengan cepat dan transparan
            </p>

            <div class="flex flex-wrap justify-center gap-4">
                <a href="{{ $whatsappLink }}?text=Halo%20Bizmark.ID%2C%20saya%20ingin%20konsultasi"
                   target="_blank" rel="noopener"
                   class="px-7 py-3.5 bg-primary hover:bg-primary-dark text-white font-semibold rounded-xl transition shadow-soft inline-flex items-center">
                    <i class="fab fa-whatsapp mr-2 text-lg"></i>
                    Chat via WhatsApp
                </a>
                <a href="{{ route($blogIndexRoute) }}" class="px-7 py-3.5 bg-white hover:bg-gray-50 text-gray-900 font-semibold rounded-xl transition shadow-soft border border-gray-200 inline-flex items-center">
                    <i class="fas fa-newspaper mr-2"></i>
                    Baca Artikel Lainnya
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Article Prose Styles -->
<style>
/* =============================================
   ARTICLE PROSE — Magazine Editorial Typography
   ============================================= */
.article-prose {
    color: #334155;
    font-size: 1.0625rem;
    line-height: 1.8;
}

/* Headings */
.article-prose h2 {
    color: #0f172a;
    font-weight: 700;
    font-size: 1.75rem;
    margin-top: 2.5rem;
    margin-bottom: 0.75rem;
    padding-bottom: 0.5rem;
    border-bottom: 2px solid #f1f5f9;
    letter-spacing: -0.02em;
    line-height: 1.3;
    scroll-margin-top: 5rem;
}
.article-prose h3 {
    color: #0f172a;
    font-weight: 600;
    font-size: 1.375rem;
    margin-top: 2rem;
    margin-bottom: 0.5rem;
    letter-spacing: -0.01em;
    line-height: 1.35;
    scroll-margin-top: 5rem;
}
.article-prose h4 {
    color: #1e293b;
    font-weight: 600;
    font-size: 1.125rem;
    margin-top: 1.5rem;
    margin-bottom: 0.5rem;
    line-height: 1.4;
}

/* Paragraphs */
.article-prose p {
    color: #475569;
    margin-bottom: 1.25rem;
    line-height: 1.8;
}

/* Links */
.article-prose a {
    color: #0f172a;
    text-decoration: underline;
    text-decoration-color: #cbd5e1;
    text-underline-offset: 3px;
    font-weight: 500;
    transition: text-decoration-color .2s;
}
.article-prose a:hover {
    text-decoration-color: #0f172a;
}

/* Strong & Emphasis */
.article-prose strong {
    color: #0f172a;
    font-weight: 600;
}
.article-prose em {
    color: #475569;
}

/* Lists */
.article-prose ul {
    list-style-type: disc;
    padding-left: 1.5rem;
    margin-bottom: 1.25rem;
    color: #475569;
}
.article-prose ol {
    list-style-type: decimal;
    padding-left: 1.5rem;
    margin-bottom: 1.25rem;
    color: #475569;
}
.article-prose li {
    margin-bottom: 0.375rem;
    line-height: 1.7;
    padding-left: 0.25rem;
}
.article-prose li::marker {
    color: #94a3b8;
}
.article-prose ul ul, .article-prose ol ul {
    margin-top: 0.25rem;
    margin-bottom: 0.25rem;
}

/* Blockquote */
.article-prose blockquote {
    border-left: 3px solid #0f172a;
    padding: 1rem 1.25rem;
    margin: 1.5rem 0;
    background: #f8fafc;
    border-radius: 0 0.5rem 0.5rem 0;
    color: #475569;
    font-style: italic;
}
.article-prose blockquote p {
    color: inherit;
    margin-bottom: 0.5rem;
}
.article-prose blockquote p:last-child {
    margin-bottom: 0;
}

/* Horizontal rule */
.article-prose hr {
    border: none;
    height: 1px;
    background: #e2e8f0;
    margin: 2rem 0;
}

/* Images */
.article-prose img {
    border-radius: 0.75rem;
    margin: 1.5rem 0;
    max-width: 100%;
    height: auto;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06);
}

/* Code */
.article-prose code {
    background: #f1f5f9;
    color: #0f172a;
    padding: 0.15rem 0.4rem;
    border-radius: 0.25rem;
    font-size: 0.875em;
    font-weight: 500;
    font-family: 'SF Mono', 'Fira Code', 'Fira Mono', Menlo, monospace;
}
.article-prose pre {
    background: #0f172a;
    color: #e2e8f0;
    padding: 1.25rem;
    border-radius: 0.75rem;
    overflow-x: auto;
    margin: 1.5rem 0;
    font-size: 0.875rem;
    line-height: 1.6;
}
.article-prose pre code {
    background: transparent;
    color: inherit;
    padding: 0;
    font-size: inherit;
}

/* Tables - responsive */
.article-prose .table-responsive {
    overflow-x: auto;
    margin: 1.5rem 0;
    border-radius: 0.75rem;
    border: 1px solid #e2e8f0;
}
.article-prose table {
    width: 100%;
    border-collapse: collapse;
    margin: 0;
    font-size: 0.9375rem;
}
.article-prose th {
    background: #f8fafc;
    color: #0f172a;
    font-weight: 600;
    padding: 0.75rem 1rem;
    text-align: left;
    border-bottom: 2px solid #e2e8f0;
    white-space: nowrap;
    font-size: 0.8125rem;
    text-transform: uppercase;
    letter-spacing: 0.03em;
}
.article-prose td {
    padding: 0.625rem 1rem;
    border-bottom: 1px solid #f1f5f9;
    color: #475569;
    vertical-align: top;
}
.article-prose tr:last-child td {
    border-bottom: none;
}
.article-prose tr:hover td {
    background: #f8fafc;
}

/* First element spacing reset */
.article-prose > *:first-child {
    margin-top: 0;
}

/* TOC sidebar active state */
.toc-link.active {
    color: #0f172a;
    border-left-color: #0f172a;
    font-weight: 600;
}

/* Mobile TOC (inside content) */
@media (max-width: 1023px) {
    .article-prose h2:first-of-type {
        margin-top: 0;
    }
}
</style>

<!-- Reading Progress + TOC Highlight Script -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Reading progress bar
    var progressBar = document.getElementById('reading-progress');
    var articleEl = document.querySelector('.article-prose');
    if (progressBar && articleEl) {
        window.addEventListener('scroll', function() {
            var rect = articleEl.getBoundingClientRect();
            var articleTop = rect.top + window.scrollY;
            var articleHeight = articleEl.offsetHeight;
            var scrolled = window.scrollY - articleTop + window.innerHeight * 0.3;
            var progress = Math.max(0, Math.min(100, (scrolled / articleHeight) * 100));
            progressBar.style.width = progress + '%';
        }, { passive: true });
    }

    // TOC highlight on scroll
    var tocLinks = document.querySelectorAll('.toc-link');
    if (tocLinks.length > 0) {
        var headings = [];
        tocLinks.forEach(function(link) {
            var target = document.getElementById(link.getAttribute('data-target'));
            if (target) headings.push({ el: target, link: link });
        });

        window.addEventListener('scroll', function() {
            var scrollPos = window.scrollY + 120;
            var current = null;
            headings.forEach(function(h) {
                if (h.el.offsetTop <= scrollPos) current = h;
            });
            tocLinks.forEach(function(l) { l.classList.remove('active'); });
            if (current) current.link.classList.add('active');
        }, { passive: true });
    }
});
</script>

@endsection
