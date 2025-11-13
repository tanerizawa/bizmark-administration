@extends('layouts.app')

@section('title', $article->title)

@section('content')
<div class="container-fluid px-4 py-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-dark-text-primary">Preview Artikel</h1>
            <p class="text-sm text-dark-text-secondary mt-1">{{ $article->title }}</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('articles.edit', $article) }}" class="px-4 py-2 bg-apple-blue text-white rounded-apple text-sm font-medium hover:bg-apple-blue-dark transition-apple">
                <i class="fas fa-edit mr-2"></i>Edit
            </a>
            <a href="{{ route('articles.index') }}" class="px-4 py-2 bg-dark-bg-tertiary text-dark-text-primary rounded-apple text-sm font-medium hover:bg-dark-bg-secondary transition-apple">
                <i class="fas fa-arrow-left mr-2"></i>Kembali
            </a>
        </div>
    </div>

    <!-- Article Info -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Article Header -->
            <div class="bg-dark-bg-secondary rounded-apple p-6">
                @if($article->featured_image)
                <img src="{{ Storage::url($article->featured_image) }}" alt="{{ $article->title }}" class="w-full rounded-apple mb-6">
                @endif

                <div class="flex items-center gap-2 mb-4">
                    {!! $article->status_badge !!}
                    <span class="px-2 py-1 text-xs rounded-full bg-apple-purple/20 text-apple-purple">{{ $article->category_label }}</span>
                    @if($article->is_featured)
                    <span class="px-2 py-1 text-xs rounded-full bg-apple-orange/20 text-apple-orange">
                        <i class="fas fa-star mr-1"></i>Featured
                    </span>
                    @endif
                </div>

                <h1 class="text-3xl font-bold text-dark-text-primary mb-4">{{ $article->title }}</h1>

                <div class="flex items-center text-sm text-dark-text-secondary mb-6 pb-6 border-b border-dark-separator">
                    <div class="flex items-center mr-6">
                        <i class="fas fa-user mr-2"></i>{{ $article->author->name }}
                    </div>
                    <div class="flex items-center mr-6">
                        <i class="fas fa-calendar mr-2"></i>{{ $article->published_at ? $article->formatted_published_at : 'Belum dipublikasikan' }}
                    </div>
                    <div class="flex items-center mr-6">
                        <i class="fas fa-clock mr-2"></i>{{ $article->reading_time_text }}
                    </div>
                    <div class="flex items-center">
                        <i class="fas fa-eye mr-2"></i>{{ number_format($article->views_count) }} views
                    </div>
                </div>

                @if($article->excerpt)
                <div class="text-lg text-dark-text-secondary mb-6 italic border-l-4 border-apple-blue pl-4">
                    {{ $article->excerpt }}
                </div>
                @endif

                <div class="prose prose-invert max-w-none">
                    {!! $article->content !!}
                </div>

                @if($article->tags && count($article->tags) > 0)
                <div class="mt-8 pt-6 border-t border-dark-separator">
                    <h4 class="text-sm font-semibold text-dark-text-primary mb-3">Tags:</h4>
                    <div class="flex flex-wrap gap-2">
                        @foreach($article->tags as $tag)
                        <span class="px-3 py-1 bg-apple-blue/20 text-apple-blue rounded-full text-sm">
                            #{{ $tag }}
                        </span>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Article Stats -->
            <div class="bg-dark-bg-secondary rounded-apple p-6">
                <h3 class="text-lg font-semibold text-dark-text-primary mb-4">Statistik Artikel</h3>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-sm text-dark-text-secondary">Total Views</span>
                        <span class="text-sm font-semibold text-dark-text-primary">{{ number_format($article->views_count) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-dark-text-secondary">Waktu Baca</span>
                        <span class="text-sm font-semibold text-dark-text-primary">{{ $article->reading_time }} menit</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-dark-text-secondary">Dibuat</span>
                        <span class="text-sm font-semibold text-dark-text-primary">{{ $article->created_at->format('d M Y') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-dark-text-secondary">Terakhir Update</span>
                        <span class="text-sm font-semibold text-dark-text-primary">{{ $article->updated_at->format('d M Y') }}</span>
                    </div>
                </div>
            </div>

            <!-- SEO Info -->
            <div class="bg-dark-bg-secondary rounded-apple p-6">
                <h3 class="text-lg font-semibold text-dark-text-primary mb-4">SEO Information</h3>
                <div class="space-y-4">
                    <div>
                        <label class="text-xs text-dark-text-tertiary mb-1 block">Meta Title</label>
                        <p class="text-sm text-dark-text-primary">{{ $article->meta_title ?: $article->title }}</p>
                    </div>
                    <div>
                        <label class="text-xs text-dark-text-tertiary mb-1 block">Meta Description</label>
                        <p class="text-sm text-dark-text-primary">{{ $article->meta_description ?: $article->excerpt }}</p>
                    </div>
                    @if($article->meta_keywords)
                    <div>
                        <label class="text-xs text-dark-text-tertiary mb-1 block">Meta Keywords</label>
                        <p class="text-sm text-dark-text-primary">{{ $article->meta_keywords }}</p>
                    </div>
                    @endif
                    <div>
                        <label class="text-xs text-dark-text-tertiary mb-1 block">Slug</label>
                        <p class="text-sm text-apple-blue font-mono">{{ $article->slug }}</p>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-dark-bg-secondary rounded-apple p-6">
                <h3 class="text-lg font-semibold text-dark-text-primary mb-4">Quick Actions</h3>
                <div class="space-y-2">
                    @if($article->status == 'draft')
                    <form action="{{ route('articles.publish', $article) }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full px-4 py-2 bg-apple-green text-white rounded-apple text-sm font-medium hover:bg-apple-green/80 transition-apple">
                            <i class="fas fa-check-circle mr-2"></i>Publikasikan
                        </button>
                    </form>
                    @elseif($article->status == 'published')
                    <form action="{{ route('articles.unpublish', $article) }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full px-4 py-2 bg-apple-orange text-white rounded-apple text-sm font-medium hover:bg-apple-orange/80 transition-apple">
                            <i class="fas fa-pause-circle mr-2"></i>Unpublish
                        </button>
                    </form>
                    @endif

                    @if($article->status != 'archived')
                    <form action="{{ route('articles.archive', $article) }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full px-4 py-2 bg-apple-purple text-white rounded-apple text-sm font-medium hover:bg-apple-purple/80 transition-apple">
                            <i class="fas fa-archive mr-2"></i>Arsipkan
                        </button>
                    </form>
                    @endif

                    <a href="{{ route('articles.edit', $article) }}" class="w-full px-4 py-2 bg-apple-blue text-white rounded-apple text-sm font-medium hover:bg-apple-blue-dark transition-apple flex items-center justify-center">
                        <i class="fas fa-edit mr-2"></i>Edit Artikel
                    </a>

                    <form action="{{ route('articles.destroy', $article) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus artikel ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full px-4 py-2 bg-apple-red text-white rounded-apple text-sm font-medium hover:bg-apple-red/80 transition-apple">
                            <i class="fas fa-trash mr-2"></i>Hapus Artikel
                        </button>
                    </form>
                </div>
            </div>

            <!-- Author Info -->
            <div class="bg-dark-bg-secondary rounded-apple p-6">
                <h3 class="text-lg font-semibold text-dark-text-primary mb-4">Penulis</h3>
                <div class="flex items-center">
                    <div class="w-12 h-12 rounded-full bg-apple-blue flex items-center justify-center text-white font-semibold text-lg">
                        {{ strtoupper(substr($article->author->name, 0, 2)) }}
                    </div>
                    <div class="ml-3">
                        <div class="text-sm font-medium text-dark-text-primary">{{ $article->author->name }}</div>
                        <div class="text-xs text-dark-text-secondary">{{ $article->author->email }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Prose styles for article content */
    .prose {
        color: var(--dark-text-primary);
    }
    .prose h1, .prose h2, .prose h3, .prose h4, .prose h5, .prose h6 {
        color: var(--dark-text-primary);
        font-weight: 700;
        margin-top: 1.5em;
        margin-bottom: 0.5em;
    }
    .prose h2 {
        font-size: 1.5em;
    }
    .prose h3 {
        font-size: 1.25em;
    }
    .prose p {
        margin-bottom: 1em;
        line-height: 1.75;
    }
    .prose a {
        color: var(--apple-blue);
        text-decoration: underline;
    }
    .prose ul, .prose ol {
        margin-left: 1.5em;
        margin-bottom: 1em;
    }
    .prose li {
        margin-bottom: 0.5em;
    }
    .prose img {
        border-radius: 0.75rem;
        margin: 1.5em 0;
        max-width: 100%;
    }
    .prose blockquote {
        border-left: 4px solid var(--apple-blue);
        padding-left: 1em;
        margin: 1.5em 0;
        font-style: italic;
        color: var(--dark-text-secondary);
    }
    .prose code {
        background-color: var(--dark-bg-tertiary);
        padding: 0.2em 0.4em;
        border-radius: 0.25rem;
        font-family: 'Courier New', monospace;
        font-size: 0.9em;
    }
    .prose pre {
        background-color: var(--dark-bg-tertiary);
        padding: 1em;
        border-radius: 0.5rem;
        overflow-x: auto;
        margin: 1.5em 0;
    }
    .prose pre code {
        background-color: transparent;
        padding: 0;
    }
</style>
@endsection
