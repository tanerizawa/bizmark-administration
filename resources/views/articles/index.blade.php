@extends('layouts.app')

@section('title', 'Artikel & Berita')
@section('page-title', 'Artikel & Berita')

@section('content')
    {{-- Compact Hero Section --}}
    <section class="card-elevated rounded-apple-lg admin-hero relative overflow-hidden mb-4">
        <div class="absolute inset-0 pointer-events-none" aria-hidden="true">
            <div class="w-48 h-48 bg-apple-blue opacity-20 blur-3xl rounded-full absolute -top-10 -right-6"></div>
            <div class="w-32 h-32 bg-apple-purple opacity-15 blur-2xl rounded-full absolute bottom-0 left-6"></div>
        </div>
        <div class="relative space-y-3">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3">
                <div class="space-y-1 max-w-3xl">
                    <p class="admin-label-compact">Content Management</p>
                    <h1 class="admin-hero-title">Artikel & Berita</h1>
                    <p class="admin-body" style="color: rgba(235,235,245,0.75);">Kelola konten artikel manual dan auto-generated AI dengan mudah.</p>
                </div>
                <div class="space-y-1.5">
                    <a href="{{ route('articles.create') }}" class="admin-btn inline-flex items-center">
                        <i class="fas fa-plus mr-1.5"></i>Buat Artikel Baru
                    </a>
                    <p class="admin-label-compact">
                        <i class="fas fa-sync-alt mr-1.5"></i>{{ now()->locale('id')->isoFormat('D MMM Y, HH:mm') }}
                    </p>
                </div>
            </div>

            {{-- Compact Stats Cards --}}
            <div class="grid grid-cols-2 md:grid-cols-4 gap-2">
                <div class="admin-stat-card" style="background: rgba(10,132,255,0.12);">
                    <div class="flex items-center gap-2">
                        <div class="w-7 h-7 rounded-lg flex items-center justify-center" style="background: rgba(10,132,255,0.25);">
                            <i class="fas fa-newspaper text-xs" style="color: var(--apple-blue);"></i>
                        </div>
                        <div>
                            <p class="admin-stat" style="color: #FFFFFF;">{{ number_format($stats['all']) }}</p>
                            <p class="admin-label-compact">Total Artikel</p>
                        </div>
                    </div>
                </div>
                <div class="admin-stat-card" style="background: rgba(52,199,89,0.12);">
                    <div class="flex items-center gap-2">
                        <div class="w-7 h-7 rounded-lg flex items-center justify-center" style="background: rgba(52,199,89,0.25);">
                            <i class="fas fa-check text-xs" style="color: var(--apple-green);"></i>
                        </div>
                        <div>
                            <p class="admin-stat" style="color: rgba(52,199,89,1);">{{ number_format($stats['published']) }}</p>
                            <p class="admin-label-compact">Published</p>
                        </div>
                    </div>
                </div>
                <div class="admin-stat-card" style="background: rgba(255,159,10,0.12);">
                    <div class="flex items-center gap-2">
                        <div class="w-7 h-7 rounded-lg flex items-center justify-center" style="background: rgba(255,159,10,0.25);">
                            <i class="fas fa-pencil-alt text-xs" style="color: var(--apple-orange);"></i>
                        </div>
                        <div>
                            <p class="admin-stat" style="color: rgba(255,159,10,1);">{{ number_format($stats['draft']) }}</p>
                            <p class="admin-label-compact">Draft</p>
                        </div>
                    </div>
                </div>
                <div class="admin-stat-card" style="background: rgba(175,82,222,0.12);">
                    <div class="flex items-center gap-2">
                        <div class="w-7 h-7 rounded-lg flex items-center justify-center" style="background: rgba(175,82,222,0.25);">
                            <i class="fas fa-robot text-xs" style="color: rgba(175,82,222,1);"></i>
                        </div>
                        <div>
                            <p class="admin-stat" style="color: rgba(175,82,222,1);">{{ number_format($stats['auto_generated']) }}</p>
                            <p class="admin-label-compact">AI Generated</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Success Message --}}
    @if(session('success'))
    <div class="mb-3 p-3 rounded-apple" style="background: rgba(52,199,89,0.12); border: 1px solid rgba(52,199,89,0.3); color: rgba(52,199,89,1);">
        <i class="fas fa-check-circle mr-1.5"></i><span class="admin-body">{{ session('success') }}</span>
    </div>
    @endif

    {{-- Error Message --}}
    @if(session('error'))
    <div class="mb-3 p-3 rounded-apple" style="background: rgba(255,69,58,0.12); border: 1px solid rgba(255,69,58,0.3); color: rgba(255,69,58,1);">
        <i class="fas fa-exclamation-circle mr-1.5"></i><span class="admin-body">{{ session('error') }}</span>
    </div>
    @endif

    {{-- Tabs Navigation --}}
    <div class="mb-3">
        <div class="card-elevated rounded-apple-lg overflow-hidden">
            <div class="flex flex-wrap border-b" style="border-color: var(--dark-separator);">
                <a href="{{ route('articles.index', ['tab' => 'all'] + request()->except('tab', 'page')) }}" 
                   class="tab-link {{ $tab === 'all' ? 'active' : '' }}">
                    <i class="fas fa-list mr-1.5"></i>
                    Semua
                    <span class="ml-1.5 px-1.5 py-0.5 rounded text-xs" style="background: rgba(255,255,255,0.1);">{{ $stats['all'] }}</span>
                </a>
                
                <a href="{{ route('articles.index', ['tab' => 'manual'] + request()->except('tab', 'page')) }}" 
                   class="tab-link {{ $tab === 'manual' ? 'active' : '' }}">
                    <i class="fas fa-pen mr-1.5"></i>
                    Manual
                    <span class="ml-1.5 px-1.5 py-0.5 rounded text-xs" style="background: rgba(255,255,255,0.1);">{{ $stats['manual'] }}</span>
                </a>
                
                <a href="{{ route('articles.index', ['tab' => 'auto-generated'] + request()->except('tab', 'page')) }}" 
                   class="tab-link {{ $tab === 'auto-generated' ? 'active' : '' }}">
                    <i class="fas fa-robot mr-1.5"></i>
                    AI
                    <span class="ml-1.5 px-1.5 py-0.5 rounded text-xs" style="background: rgba(255,255,255,0.1);">{{ $stats['auto_generated'] }}</span>
                </a>
                
                <a href="{{ route('articles.index', ['tab' => 'auto-post-settings'] + request()->except('tab', 'page')) }}" 
                   class="tab-link {{ $tab === 'auto-post-settings' ? 'active' : '' }}">
                    <i class="fas fa-cog mr-1.5"></i>
                    Auto-Post
                </a>
            </div>

            @if($tab === 'auto-post-settings')
                {{-- Auto-Post Settings Tab --}}
                <div class="p-4">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                        {{-- Configuration Card --}}
                        <div>
                            <div class="flex items-center justify-between mb-3">
                                <h3 class="admin-section text-dark-text-primary">
                                    <i class="fas fa-cog mr-1.5 text-apple-blue"></i>Konfigurasi Auto-Post
                                </h3>
                                <button type="button" id="autoPostToggle" 
                                    class="toggle-switch {{ $autoPostConfig && $autoPostConfig->is_enabled ? 'active' : '' }}"
                                    data-url="{{ route('auto-post.config.toggle') }}"
                                    data-csrf="{{ csrf_token() }}">
                                    <span class="toggle-slider"></span>
                                </button>
                            </div>

                            @if($autoPostConfig)
                            <div class="space-y-3 text-sm">
                                <div class="flex justify-between py-2 border-b" style="border-color: var(--dark-separator);">
                                    <span class="text-dark-text-secondary">Status:</span>
                                    <span data-status-row class="font-medium {{ $autoPostConfig->is_enabled ? 'text-apple-green' : 'text-apple-red' }}">
                                        {{ $autoPostConfig->is_enabled ? '✓ Aktif' : '✗ Non-aktif' }}
                                    </span>
                                </div>
                                <div class="flex justify-between py-2 border-b" style="border-color: var(--dark-separator);">
                                    <span class="text-dark-text-secondary">Posts per Day:</span>
                                    <span class="font-medium text-dark-text-primary">{{ $autoPostConfig->posts_per_day }}x</span>
                                </div>
                                <div class="flex justify-between py-2 border-b" style="border-color: var(--dark-separator);">
                                    <span class="text-dark-text-secondary">Post Times:</span>
                                    <span class="font-medium text-dark-text-primary">{{ implode(', ', $autoPostConfig->post_times ?? []) }}</span>
                                </div>
                                <div class="flex justify-between py-2 border-b" style="border-color: var(--dark-separator);">
                                    <span class="text-dark-text-secondary">AI Model:</span>
                                    <span class="font-medium text-dark-text-primary">{{ $autoPostConfig->ai_model }}</span>
                                </div>
                                <div class="flex justify-between py-2">
                                    <span class="text-dark-text-secondary">Auto Publish:</span>
                                    <span class="font-medium {{ $autoPostConfig->auto_publish ? 'text-apple-green' : 'text-apple-red' }}">
                                        {{ $autoPostConfig->auto_publish ? 'Yes' : 'No' }}
                                    </span>
                                </div>
                            </div>

                            <div class="mt-6 space-y-2">
                                <a href="{{ route('auto-post.config') }}" class="block w-full px-4 py-2.5 bg-apple-blue text-white text-center rounded-apple text-sm font-medium hover:bg-apple-blue-dark transition-apple">
                                    <i class="fas fa-cog mr-2"></i>Edit Konfigurasi Lengkap
                                </a>
                                <a href="{{ route('auto-post.topics.index') }}" class="block w-full px-4 py-2.5 bg-dark-bg-tertiary text-dark-text-primary text-center rounded-apple text-sm font-medium hover:bg-dark-bg-secondary transition-apple">
                                    <i class="fas fa-lightbulb mr-2"></i>Kelola Topic Pool
                                </a>
                            </div>
                            @else
                            <p class="text-dark-text-secondary text-sm">Konfigurasi auto-post belum diatur.</p>
                            @endif
                        </div>

                        {{-- Upcoming Schedules Card --}}
                        <div>
                            <h3 class="text-lg font-semibold text-dark-text-primary mb-4">
                                <i class="fas fa-calendar-alt mr-2 text-apple-purple"></i>Jadwal Mendatang
                            </h3>

                            @if($upcomingSchedules->count() > 0)
                            <div class="space-y-3">
                                @foreach($upcomingSchedules as $schedule)
                                <div class="p-3 rounded-apple" style="background: rgba(255,255,255,0.05);">
                                    <div class="flex items-start justify-between">
                                        <div class="flex-1">
                                            <p class="text-sm font-medium text-dark-text-primary mb-1">
                                                {{ $schedule->topic ? Str::limit($schedule->topic->title, 50) : 'Topic tidak ditemukan' }}
                                            </p>
                                            <p class="text-xs text-dark-text-tertiary">
                                                <i class="far fa-clock mr-1"></i>{{ optional($schedule->scheduled_at ?? $schedule->scheduled_for)->format('d M Y, H:i') ?? '-' }}
                                            </p>
                                        </div>
                                        <span class="px-2 py-1 rounded-full text-xs font-medium" style="background: rgba(255,159,10,0.2); color: rgba(255,159,10,1);">
                                            Pending
                                        </span>
                                    </div>
                                </div>
                                @endforeach
                            </div>

                            <div class="mt-4">
                                <a href="{{ route('auto-post.schedules.index') }}" class="block w-full px-4 py-2.5 bg-dark-bg-tertiary text-dark-text-primary text-center rounded-apple text-sm font-medium hover:bg-dark-bg-secondary transition-apple">
                                    <i class="fas fa-calendar-check mr-2"></i>Lihat Semua Jadwal
                                </a>
                            </div>
                            @else
                            <div class="text-center py-8">
                                <i class="fas fa-calendar-times text-4xl text-dark-text-tertiary mb-3"></i>
                                <p class="text-dark-text-secondary text-sm">Belum ada jadwal yang akan datang</p>
                                <form action="{{ route('auto-post.schedules.generate-batch') }}" method="POST" class="mt-4">
                                    @csrf
                                    <input type="hidden" name="date" value="{{ now()->addDay()->format('Y-m-d') }}">
                                    <button type="submit" class="px-4 py-2 bg-apple-green text-white rounded-apple text-sm font-medium hover:opacity-90 transition-apple">
                                        <i class="fas fa-magic mr-2"></i>Generate Tomorrow's Posts
                                    </button>
                                </form>
                            </div>
                            @endif
                        </div>
                    </div>

                    {{-- Quick Links --}}
                    <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4">
                        <a href="{{ route('auto-post.analytics') }}" class="p-4 rounded-apple hover-apple" style="background: rgba(10,132,255,0.1); border: 1px solid rgba(10,132,255,0.3);">
                            <i class="fas fa-chart-line text-2xl text-apple-blue mb-2"></i>
                            <h4 class="text-sm font-semibold text-dark-text-primary mb-1">Analytics Dashboard</h4>
                            <p class="text-xs text-dark-text-tertiary">Monitor performa auto-posting</p>
                        </a>

                        <a href="{{ route('auto-post.logs.index') }}" class="p-4 rounded-apple hover-apple" style="background: rgba(175,82,222,0.1); border: 1px solid rgba(175,82,222,0.3);">
                            <i class="fas fa-file-alt text-2xl text-apple-purple mb-2"></i>
                            <h4 class="text-sm font-semibold text-dark-text-primary mb-1">Activity Logs</h4>
                            <p class="text-xs text-dark-text-tertiary">Lihat log aktivitas sistem</p>
                        </a>

                        <a href="{{ route('auto-post.topics.create') }}" class="p-4 rounded-apple hover-apple" style="background: rgba(52,199,89,0.1); border: 1px solid rgba(52,199,89,0.3);">
                            <i class="fas fa-plus-circle text-2xl text-apple-green mb-2"></i>
                            <h4 class="text-sm font-semibold text-dark-text-primary mb-1">Add New Topic</h4>
                            <p class="text-xs text-dark-text-tertiary">Tambah topic ke pool</p>
                        </a>
                    </div>
                </div>
            @else
                {{-- Articles List Tab --}}
                <div class="p-5">
                    {{-- Filters Section --}}
                    <form action="{{ route('articles.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-5">
                        <input type="hidden" name="tab" value="{{ $tab }}">
                        
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

                    {{-- Articles Table --}}
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
                                            @if($article->featured_image_url)
                                            <img src="{{ $article->featured_image_url }}" alt="{{ $article->title }}" class="w-16 h-16 object-cover rounded-apple mr-3">
                                            @else
                                            <div class="w-16 h-16 bg-dark-bg-tertiary rounded-apple mr-3 flex items-center justify-center">
                                                <i class="fas fa-image text-dark-text-tertiary text-xl"></i>
                                            </div>
                                            @endif
                                            <div>
                                                <div class="text-sm font-medium text-dark-text-primary">{{ Str::limit($article->title, 50) }}</div>
                                                <div class="flex items-center gap-2 mt-1">
                                                    @if($article->is_featured)
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-apple-orange/20 text-apple-orange">
                                                        <i class="fas fa-star mr-1"></i>Featured
                                                    </span>
                                                    @endif
                                                    @if($article->source_type === 'auto-generated')
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-apple-purple/20 text-apple-purple">
                                                        <i class="fas fa-robot mr-1"></i>AI
                                                    </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-dark-text-secondary">{{ $article->category_label }}</td>
                                    <td class="px-6 py-4">{!! $article->status_badge !!}</td>
                                    <td class="px-6 py-4 text-sm text-dark-text-secondary">{{ $article->author?->name ?? 'System AI' }}</td>
                                    <td class="px-6 py-4 text-sm text-dark-text-secondary">
                                        <i class="fas fa-eye mr-1"></i>{{ number_format($article->views_count) }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-dark-text-secondary">
                                        {{ $article->published_at ? $article->formatted_published_at : '-' }}
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <div class="flex items-center justify-end space-x-2">
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

                                            <a href="{{ route('articles.show', $article) }}" class="btn-icon-apple text-apple-blue" title="Lihat">
                                                <i class="fas fa-eye"></i>
                                            </a>

                                            <a href="{{ route('articles.edit', $article) }}" class="btn-icon-apple" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>

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
                                        <p class="text-dark-text-secondary mb-4">
                                            @if($tab === 'auto-generated')
                                                AI belum menghasilkan artikel. Pastikan auto-post aktif dan jadwal tersedia.
                                            @else
                                                Mulai dengan membuat artikel pertama Anda
                                            @endif
                                        </p>
                                        @if($tab !== 'auto-generated')
                                        <a href="{{ route('articles.create') }}" class="inline-flex items-center px-4 py-2 bg-apple-blue text-white rounded-apple text-sm font-medium hover:bg-apple-blue-dark transition-apple">
                                            <i class="fas fa-plus mr-2"></i>Buat Artikel Baru
                                        </a>
                                        @endif
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($articles->hasPages())
                    <div class="mt-5">
                        {{ $articles->appends(request()->query())->links() }}
                    </div>
                    @endif
                </div>
            @endif
        </div>
    </div>
@endsection

@push('styles')
<style>
    .tab-link {
        display: inline-flex;
        align-items: center;
        padding: 1rem 1.5rem;
        color: var(--dark-text-secondary);
        border-bottom: 2px solid transparent;
        transition: all 0.2s ease;
        font-size: 0.875rem;
        font-weight: 500;
    }

    .tab-link:hover {
        color: var(--dark-text-primary);
        background-color: rgba(255, 255, 255, 0.05);
    }

    .tab-link.active {
        color: #0A84FF;
        border-bottom-color: #0A84FF;
        background-color: rgba(10, 132, 255, 0.1);
    }

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

    .toggle-switch {
        position: relative;
        display: inline-block;
        width: 48px;
        height: 26px;
        background-color: rgba(255,255,255,0.2);
        border-radius: 26px;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    .toggle-switch.active {
        background-color: #34C759;
    }

    .toggle-slider {
        position: absolute;
        top: 3px;
        left: 3px;
        width: 20px;
        height: 20px;
        background-color: white;
        border-radius: 50%;
        transition: transform 0.3s ease;
    }

    .toggle-switch.active .toggle-slider {
        transform: translateX(22px);
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const toggleBtn = document.getElementById('autoPostToggle');
    if (!toggleBtn) return;

    toggleBtn.addEventListener('click', function () {
        const url = this.dataset.url;
        const csrf = this.dataset.csrf;
        const btn = this;

        btn.style.opacity = '0.5';
        btn.style.pointerEvents = 'none';

        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrf,
                'Accept': 'application/json',
            },
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                btn.classList.toggle('active', data.is_enabled);
                // Update the status text in the config card
                const statusRow = document.querySelector('[data-status-row]');
                if (statusRow) {
                    statusRow.className = 'font-medium ' + (data.is_enabled ? 'text-apple-green' : 'text-apple-red');
                    statusRow.textContent = data.is_enabled ? '✓ Aktif' : '✗ Non-aktif';
                }
            }
        })
        .catch(() => {
            // Fallback: reload page on error
            window.location.reload();
        })
        .finally(() => {
            btn.style.opacity = '1';
            btn.style.pointerEvents = 'auto';
        });
    });
});
</script>
@endpush

