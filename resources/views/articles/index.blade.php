@extends('layouts.app')

@section('title', 'Artikel & Berita')
@section('page-title', 'Artikel & Berita')

@section('content')
    {{-- Hero Section --}}
    <section class="card-elevated rounded-apple-xl p-5 md:p-6 relative overflow-hidden mb-6">
        <div class="absolute inset-0 pointer-events-none" aria-hidden="true">
            <div class="w-72 h-72 bg-apple-blue opacity-30 blur-3xl rounded-full absolute -top-16 -right-10"></div>
            <div class="w-48 h-48 bg-apple-purple opacity-20 blur-2xl rounded-full absolute bottom-0 left-10"></div>
        </div>
        <div class="relative space-y-5 md:space-y-6">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-5">
                <div class="space-y-2.5 max-w-3xl">
                    <p class="text-sm uppercase tracking-[0.4em]" style="color: rgba(235,235,245,0.5);">Content Management</p>
                    <h1 class="text-2xl md:text-3xl font-bold" style="color: #FFFFFF;">
                        Artikel & Berita
                    </h1>
                    <p class="text-sm md:text-base" style="color: rgba(235,235,245,0.75);">
                        Kelola konten artikel, berita, dan publikasi untuk landing page dengan mudah.
                    </p>
                </div>
                <div class="space-y-2.5">
                    <a href="{{ route('articles.create') }}" class="inline-flex items-center px-4 py-2.5 bg-apple-blue text-white rounded-apple text-sm font-medium hover:bg-apple-blue-dark transition-apple">
                        <i class="fas fa-plus mr-2"></i>Buat Artikel Baru
                    </a>
                    <p class="text-xs" style="color: rgba(235,235,245,0.65);">
                        <i class="fas fa-sync-alt mr-2"></i>Diperbarui: {{ now()->locale('id')->isoFormat('D MMM Y, HH:mm') }}
                    </p>
                </div>
            </div>

            <!-- Summary Statistics -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3 md:gap-4">
                <div class="rounded-apple-lg p-3.5 md:p-4" style="background: rgba(10,132,255,0.12);">
                    <p class="text-xs uppercase tracking-widest" style="color: rgba(10,132,255,0.9);">Total Artikel</p>
                    <h2 class="text-2xl font-bold mt-1.5" style="color: #FFFFFF;">
                        {{ number_format($articles->total()) }}
                    </h2>
                    <p class="text-xs" style="color: rgba(235,235,245,0.6);">Semua konten</p>
                </div>

                <div class="rounded-apple-lg p-3.5 md:p-4" style="background: rgba(52,199,89,0.12);">
                    <p class="text-xs uppercase tracking-widest" style="color: rgba(52,199,89,0.9);">Published</p>
                    <h2 class="text-2xl font-bold mt-1.5" style="color: rgba(52,199,89,1);">
                        {{ number_format($articles->where('status', 'published')->count()) }}
                    </h2>
                    <p class="text-xs" style="color: rgba(235,235,245,0.6);">Aktif online</p>
                </div>

                <div class="rounded-apple-lg p-3.5 md:p-4" style="background: rgba(255,159,10,0.12);">
                    <p class="text-xs uppercase tracking-widest" style="color: rgba(255,159,10,0.9);">Draft</p>
                    <h2 class="text-2xl font-bold mt-1.5" style="color: rgba(255,159,10,1);">
                        {{ number_format($articles->where('status', 'draft')->count()) }}
                    </h2>
                    <p class="text-xs" style="color: rgba(235,235,245,0.6);">Belum dipublikasi</p>
                </div>

                <div class="rounded-apple-lg p-3.5 md:p-4" style="background: rgba(175,82,222,0.12);">
                    <p class="text-xs uppercase tracking-widest" style="color: rgba(175,82,222,0.9);">Total Views</p>
                    <h2 class="text-2xl font-bold mt-1.5" style="color: #FFFFFF;">
                        {{ number_format($articles->sum('views_count')) }}
                    </h2>
                    <p class="text-xs" style="color: rgba(235,235,245,0.6);">Pembaca</p>
                </div>
            </div>
        </div>
    </section>

    {{-- Success Message --}}
    @if(session('success'))
    <div class="mb-5 p-4 rounded-apple-lg" style="background: rgba(52,199,89,0.12); border: 1px solid rgba(52,199,89,0.3); color: rgba(52,199,89,1);">
        <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
    </div>
    @endif

    {{-- Filters Section --}}
    <section class="card-elevated rounded-apple-xl p-5 mb-5">
        <form action="{{ route('articles.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <!-- Search -->
            <div class="md:col-span-2">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari artikel..." 
                       class="w-full px-4 py-2.5 bg-dark-bg-tertiary text-dark-text-primary rounded-apple text-sm border border-dark-separator focus:outline-none focus:border-apple-blue transition-apple">
            </div>

            <!-- Status Filter -->
            <div>
                <select name="status" class="w-full px-4 py-2.5 bg-dark-bg-tertiary text-dark-text-primary rounded-apple text-sm border border-dark-separator focus:outline-none focus:border-apple-blue transition-apple">
                    <option value="">Semua Status</option>
                    <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="published" {{ request('status') == 'published' ? 'selected' : '' }}>Published</option>
                    <option value="archived" {{ request('status') == 'archived' ? 'selected' : '' }}>Archived</option>
                </select>
            </div>

            <!-- Category Filter -->
            <div>
                <select name="category" class="w-full px-4 py-2.5 bg-dark-bg-tertiary text-dark-text-primary rounded-apple text-sm border border-dark-separator focus:outline-none focus:border-apple-blue transition-apple">
                    <option value="">Semua Kategori</option>
                    @foreach(App\Models\Article::getCategories() as $key => $label)
                    <option value="{{ $key }}" {{ request('category') == $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Submit -->
            <div>
                <button type="submit" class="w-full px-4 py-2.5 bg-apple-blue text-white rounded-apple text-sm font-medium hover:bg-apple-blue-dark transition-apple">
                    <i class="fas fa-search mr-2"></i>Filter
                </button>
            </div>
        </form>
    </section>

    {{-- Articles Table --}}
    <section class="card-elevated rounded-apple-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-dark-bg-secondary border-b" style="border-color: var(--dark-separator);">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-dark-text-tertiary uppercase tracking-wider">Artikel</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-dark-text-tertiary uppercase tracking-wider">Kategori</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-dark-text-tertiary uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-dark-text-tertiary uppercase tracking-wider">Penulis</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-dark-text-tertiary uppercase tracking-wider">Views</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-dark-text-tertiary uppercase tracking-wider">Tanggal</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-dark-text-tertiary uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y" style="border-color: var(--dark-separator);">
                    @forelse($articles as $article)
                    <tr class="hover-apple">
                        <td class="px-6 py-4">
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
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-apple-orange/20 text-apple-orange mt-1">
                                        <i class="fas fa-star mr-1"></i>Featured
                                    </span>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-dark-text-secondary">{{ $article->category_label }}</td>
                        <td class="px-6 py-4">{!! $article->status_badge !!}</td>
                        <td class="px-6 py-4 text-sm text-dark-text-secondary">{{ $article->author->name }}</td>
                        <td class="px-6 py-4 text-sm text-dark-text-secondary">
                            <i class="fas fa-eye mr-1"></i>{{ number_format($article->views_count) }}
                        </td>
                        <td class="px-6 py-4 text-sm text-dark-text-secondary">
                            {{ $article->published_at ? $article->formatted_published_at : '-' }}
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end space-x-2">
                                <!-- Quick Actions -->
                                @if($article->status == 'draft')
                                <form action="{{ route('articles.publish', $article) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="btn-icon-apple text-apple-green" title="Publikasikan">
                                        <i class="fas fa-check-circle"></i>
                                    </button>
                                </form>
                                @elseif($article->status == 'published')
                                <form action="{{ route('articles.unpublish', $article) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="btn-icon-apple text-apple-orange" title="Unpublish">
                                        <i class="fas fa-pause-circle"></i>
                                    </button>
                                </form>
                                @endif

                                <!-- View -->
                                <a href="{{ route('articles.show', $article) }}" class="btn-icon-apple text-apple-blue" title="Lihat">
                                    <i class="fas fa-eye"></i>
                                </a>

                                <!-- Edit -->
                                <a href="{{ route('articles.edit', $article) }}" class="btn-icon-apple" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>

                                <!-- Delete -->
                                <form action="{{ route('articles.destroy', $article) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus artikel ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-icon-apple text-apple-red" title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center">
                            <div class="icon-circle-apple bg-gray-500 bg-opacity-10 w-16 h-16 mx-auto mb-4">
                                <svg class="w-8 h-8 text-dark-text-tertiary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path>
                                </svg>
                            </div>
                            <h3 class="text-lg font-medium text-dark-text-primary mb-2">Belum ada artikel</h3>
                            <p class="text-dark-text-secondary mb-4">Mulai dengan membuat artikel pertama Anda</p>
                            <a href="{{ route('articles.create') }}" class="inline-flex items-center px-4 py-2 bg-apple-blue text-white rounded-apple text-sm font-medium hover:bg-apple-blue-dark transition-apple">
                                <i class="fas fa-plus mr-2"></i>Buat Artikel Baru
                            </a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($articles->hasPages())
        <div class="px-6 py-4 border-t" style="border-color: var(--dark-separator);">
            {{ $articles->links() }}
        </div>
        @endif
    </section>
@endsection

@push('styles')
<style>
    .btn-icon-apple {
        padding: 0.5rem;
        border-radius: 8px;
        transition: all 0.2s ease;
        color: var(--dark-text-secondary);
    }

    .btn-icon-apple:hover {
        background-color: var(--dark-bg-tertiary);
        color: var(--dark-text-primary);
    }

    .hover-apple {
        transition: background-color 0.2s ease;
    }

    .hover-apple:hover {
        background-color: rgba(255, 255, 255, 0.03);
    }
</style>
@endpush

