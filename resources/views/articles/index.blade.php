@extends('layouts.app')

@section('title', 'Artikel & Berita')

@section('content')
<div class="container-fluid px-4 py-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-dark-text-primary">Artikel & Berita</h1>
            <p class="text-sm text-dark-text-secondary mt-1">Kelola konten artikel untuk landing page</p>
        </div>
        <a href="{{ route('articles.create') }}" class="px-4 py-2 bg-apple-blue text-white rounded-apple text-sm font-medium hover:bg-apple-blue-dark transition-apple">
            <i class="fas fa-plus mr-2"></i>Buat Artikel Baru
        </a>
    </div>

    <!-- Success Message -->
    @if(session('success'))
    <div class="mb-4 p-4 bg-apple-green/10 border border-apple-green/20 text-apple-green rounded-apple">
        <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
    </div>
    @endif

    <!-- Filters -->
    <div class="bg-dark-bg-secondary rounded-apple p-4 mb-6">
        <form action="{{ route('articles.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <!-- Search -->
            <div class="md:col-span-2">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari artikel..." class="w-full px-3 py-2 bg-dark-bg-tertiary text-dark-text-primary rounded-apple text-sm border border-dark-separator focus:outline-none focus:border-apple-blue">
            </div>

            <!-- Status Filter -->
            <div>
                <select name="status" class="w-full px-3 py-2 bg-dark-bg-tertiary text-dark-text-primary rounded-apple text-sm border border-dark-separator focus:outline-none focus:border-apple-blue">
                    <option value="">Semua Status</option>
                    <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="published" {{ request('status') == 'published' ? 'selected' : '' }}>Published</option>
                    <option value="archived" {{ request('status') == 'archived' ? 'selected' : '' }}>Archived</option>
                </select>
            </div>

            <!-- Category Filter -->
            <div>
                <select name="category" class="w-full px-3 py-2 bg-dark-bg-tertiary text-dark-text-primary rounded-apple text-sm border border-dark-separator focus:outline-none focus:border-apple-blue">
                    <option value="">Semua Kategori</option>
                    @foreach(App\Models\Article::getCategories() as $key => $label)
                    <option value="{{ $key }}" {{ request('category') == $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Submit -->
            <div>
                <button type="submit" class="w-full px-4 py-2 bg-apple-blue text-white rounded-apple text-sm font-medium hover:bg-apple-blue-dark transition-apple">
                    <i class="fas fa-search mr-2"></i>Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Articles Table -->
    <div class="bg-dark-bg-secondary rounded-apple overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-dark-bg-tertiary">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-dark-text-secondary uppercase tracking-wider">Artikel</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-dark-text-secondary uppercase tracking-wider">Kategori</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-dark-text-secondary uppercase tracking-wider">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-dark-text-secondary uppercase tracking-wider">Penulis</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-dark-text-secondary uppercase tracking-wider">Views</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-dark-text-secondary uppercase tracking-wider">Tanggal Publikasi</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-dark-text-secondary uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-dark-separator">
                    @forelse($articles as $article)
                    <tr class="hover:bg-dark-bg-tertiary transition-colors">
                        <td class="px-4 py-4">
                            <div class="flex items-center">
                                @if($article->featured_image)
                                <img src="{{ Storage::url($article->featured_image) }}" alt="{{ $article->title }}" class="w-16 h-16 object-cover rounded-apple mr-3">
                                @else
                                <div class="w-16 h-16 bg-dark-bg-tertiary rounded-apple mr-3 flex items-center justify-center">
                                    <i class="fas fa-image text-dark-text-tertiary text-xl"></i>
                                </div>
                                @endif
                                <div>
                                    <div class="text-sm font-medium text-dark-text-primary">{{ Str::limit($article->title, 50) }}</div>
                                    @if($article->is_featured)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-apple-orange/20 text-apple-orange mt-1">
                                        <i class="fas fa-star mr-1"></i>Featured
                                    </span>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-4 text-sm text-dark-text-secondary">{{ $article->category_label }}</td>
                        <td class="px-4 py-4">{!! $article->status_badge !!}</td>
                        <td class="px-4 py-4 text-sm text-dark-text-secondary">{{ $article->author->name }}</td>
                        <td class="px-4 py-4 text-sm text-dark-text-secondary">
                            <i class="fas fa-eye mr-1"></i>{{ number_format($article->views_count) }}
                        </td>
                        <td class="px-4 py-4 text-sm text-dark-text-secondary">
                            {{ $article->published_at ? $article->formatted_published_at : '-' }}
                        </td>
                        <td class="px-4 py-4 text-right">
                            <div class="flex items-center justify-end space-x-2">
                                <!-- Quick Actions -->
                                @if($article->status == 'draft')
                                <form action="{{ route('articles.publish', $article) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="text-apple-green hover:text-apple-green/80" title="Publikasikan">
                                        <i class="fas fa-check-circle"></i>
                                    </button>
                                </form>
                                @elseif($article->status == 'published')
                                <form action="{{ route('articles.unpublish', $article) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="text-apple-orange hover:text-apple-orange/80" title="Unpublish">
                                        <i class="fas fa-pause-circle"></i>
                                    </button>
                                </form>
                                @endif

                                <!-- View -->
                                <a href="{{ route('articles.show', $article) }}" class="text-apple-blue hover:text-apple-blue-dark" title="Lihat">
                                    <i class="fas fa-eye"></i>
                                </a>

                                <!-- Edit -->
                                <a href="{{ route('articles.edit', $article) }}" class="text-dark-text-secondary hover:text-dark-text-primary" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>

                                <!-- Delete -->
                                <form action="{{ route('articles.destroy', $article) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus artikel ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-apple-red hover:text-apple-red/80" title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-4 py-8 text-center text-dark-text-secondary">
                            <i class="fas fa-newspaper text-4xl mb-2"></i>
                            <p>Belum ada artikel. <a href="{{ route('articles.create') }}" class="text-apple-blue hover:underline">Buat artikel pertama Anda</a></p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    @if($articles->hasPages())
    <div class="mt-6">
        {{ $articles->links() }}
    </div>
    @endif
</div>
@endsection
