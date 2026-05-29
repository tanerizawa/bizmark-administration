{{-- Summary Statistics --}}
<div class="grid grid-cols-2 md:grid-cols-4 gap-3 md:gap-4 mb-6">
    <div class="rounded-apple-lg p-3.5 md:p-4" style="background: rgba(10,132,255,0.12);">
        <p class="text-xs uppercase tracking-widest" style="color: rgba(10,132,255,0.9);">Total Topics</p>
        <h2 class="text-lg font-bold mt-1.5 text-white">
            {{ number_format($topicStats['total'] ?? 0) }}
        </h2>
        <p class="text-xs" style="color: rgba(235,235,245,0.6);">Semua topic</p>
    </div>

    <div class="rounded-apple-lg p-3.5 md:p-4" style="background: rgba(52,199,89,0.12);">
        <p class="text-xs uppercase tracking-widest" style="color: rgba(52,199,89,0.9);">Available</p>
        <h2 class="text-lg font-bold mt-1.5 text-apple-green">
            {{ number_format($topicStats['available'] ?? 0) }}
        </h2>
        <p class="text-xs" style="color: rgba(235,235,245,0.6);">Siap digunakan</p>
    </div>

    <div class="rounded-apple-lg p-3.5 md:p-4" style="background: rgba(255,159,10,0.12);">
        <p class="text-xs uppercase tracking-widest" style="color: rgba(255,159,10,0.9);">Scheduled</p>
        <h2 class="text-lg font-bold mt-1.5 text-apple-orange">
            {{ number_format($topicStats['scheduled'] ?? 0) }}
        </h2>
        <p class="text-xs" style="color: rgba(235,235,245,0.6);">Terjadwal</p>
    </div>

    <div class="rounded-apple-lg p-3.5 md:p-4" style="background: rgba(175,82,222,0.12);">
        <p class="text-xs uppercase tracking-widest" style="color: rgba(175,82,222,0.9);">Used</p>
        <h2 class="text-lg font-bold mt-1.5 text-white">
            {{ number_format($topicStats['used'] ?? 0) }}
        </h2>
        <p class="text-xs" style="color: rgba(235,235,245,0.6);">Sudah dipakai</p>
    </div>
</div>

{{-- Ecosystem Snapshot --}}
<div class="grid grid-cols-1 md:grid-cols-3 gap-3 md:gap-4 mb-6">
    <div class="rounded-apple-lg p-3.5 md:p-4 auto-soft-card" style="background: rgba(52,199,89,0.08);">
        <p class="text-xs uppercase tracking-widest" style="color: rgba(52,199,89,0.9);">Local Focus</p>
        <h2 class="text-lg font-bold mt-1.5 text-apple-green">{{ number_format($topicStats['local'] ?? 0) }}</h2>
        <p class="text-xs" style="color: rgba(235,235,245,0.6);">UMKM & pasar domestik</p>
    </div>
    <div class="rounded-apple-lg p-3.5 md:p-4 auto-soft-card" style="background: rgba(10,132,255,0.08);">
        <p class="text-xs uppercase tracking-widest" style="color: rgba(10,132,255,0.9);">PMA Focus</p>
        <h2 class="text-lg font-bold mt-1.5 text-apple-blue">{{ number_format($topicStats['pma'] ?? 0) }}</h2>
        <p class="text-xs" style="color: rgba(235,235,245,0.6);">Investor & ekspansi asing</p>
    </div>
    <div class="rounded-apple-lg p-3.5 md:p-4 auto-soft-card" style="background: rgba(175,82,222,0.08);">
        <p class="text-xs uppercase tracking-widest" style="color: rgba(175,82,222,0.9);">Balanced</p>
        <h2 class="text-lg font-bold mt-1.5 text-white">{{ number_format($topicStats['both'] ?? 0) }}</h2>
        <p class="text-xs" style="color: rgba(235,235,245,0.6);">Bisa untuk local + PMA</p>
    </div>
</div>

{{-- Actions Bar --}}
<div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
    {{-- Filters --}}
    <form method="GET" class="flex flex-wrap items-center gap-3">
        <input type="hidden" name="tab" value="topics">
        <input 
            type="text" 
            name="search" 
            value="{{ request('search') }}" 
            placeholder="Cari topic..."
            class="w-full md:w-auto px-4 py-2.5 rounded-apple text-sm bg-dark-bg-tertiary auto-soft-field text-white placeholder-dark-text-tertiary focus:border-apple-blue focus:ring-1 focus:ring-apple-blue">
        
        <select name="status" class="px-4 py-2.5 rounded-apple text-sm bg-dark-bg-tertiary auto-soft-field text-white focus:border-apple-blue focus:ring-1 focus:ring-apple-blue">
            <option value="">Semua Status</option>
            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
            <option value="scheduled" {{ request('status') == 'scheduled' ? 'selected' : '' }}>Scheduled</option>
            <option value="published" {{ request('status') == 'published' ? 'selected' : '' }}>Published</option>
        </select>

        <select name="category" class="px-4 py-2.5 rounded-apple text-sm bg-dark-bg-tertiary auto-soft-field text-white focus:border-apple-blue focus:ring-1 focus:ring-apple-blue">
            <option value="">Semua Kategori</option>
            @foreach(($categories ?? collect()) as $cat)
                <option value="{{ $cat }}" {{ request('category') == $cat ? 'selected' : '' }}>{{ ucfirst($cat) }}</option>
            @endforeach
        </select>

        <select name="market" class="px-4 py-2.5 rounded-apple text-sm bg-dark-bg-tertiary auto-soft-field text-white focus:border-apple-blue focus:ring-1 focus:ring-apple-blue">
            <option value="">Semua Market</option>
            @foreach(($markets ?? collect()) as $market)
                <option value="{{ $market }}" {{ request('market') == $market ? 'selected' : '' }}>{{ strtoupper($market) }}</option>
            @endforeach
        </select>
        
        <button type="submit" class="px-4 py-2.5 rounded-apple text-sm font-medium bg-dark-bg-tertiary auto-soft-field text-white hover:bg-white/5 transition-apple">
            <i class="fas fa-filter mr-2"></i>Filter
        </button>
    </form>
    
    {{-- Add Topic Button --}}
    <a href="{{ route('auto-post.topics.create') }}" 
       class="inline-flex items-center px-4 py-2.5 bg-apple-blue text-white rounded-apple text-sm font-medium hover:bg-blue-700 transition-apple">
        <i class="fas fa-plus mr-2"></i>Tambah Topic
    </a>
        <form method="POST" action="{{ route('auto-post.topics.bulk-action') }}" x-data @submit.prevent="if(confirm('Normalisasi & lengkapi topic sesuai filter saat ini?')) $el.submit()">
            @csrf
            <input type="hidden" name="action" value="normalize_ecosystem">
            <input type="hidden" name="scope" value="filtered">
            <input type="hidden" name="status" value="{{ request('status') }}">
            <input type="hidden" name="category" value="{{ request('category') }}">
            <input type="hidden" name="market" value="{{ request('market') }}">
            <input type="hidden" name="search" value="{{ request('search') }}">
            <button type="submit" class="inline-flex items-center px-4 py-2.5 rounded-apple text-sm font-medium bg-dark-bg-tertiary auto-soft-field text-white hover:bg-white/5 transition-apple">
                <i class="fas fa-wand-magic-sparkles mr-2"></i>Auto Improve (Filtered)
            </button>
        </form>
</div>

{{-- Topics Table --}}
<div class="bg-dark-bg-tertiary rounded-apple-lg auto-soft-card overflow-hidden">
    @if(isset($topics) && $topics->count() > 0)
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="auto-soft-divider-bottom">
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-dark-text-secondary">Topic</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-dark-text-secondary">Kategori</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-dark-text-secondary">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-dark-text-secondary">Ecosystem</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-dark-text-secondary">Fit</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-dark-text-secondary">Scheduled</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-dark-text-secondary">Actions</th>
                    </tr>
                </thead>
                <tbody class="auto-soft-divide-y">
                    @foreach($topics as $topic)
                        <tr class="hover:bg-white/5 transition-colors">
                            <td class="px-4 py-3">
                                <div class="text-sm font-medium text-white">{{ Str::limit($topic->title, 50) }}</div>
                                @if($topic->keywords)
                                    <div class="text-xs text-dark-text-tertiary mt-1">
                                        {{ Str::limit(implode(', ', $topic->keywords ?? []), 40) }}
                                    </div>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-1 text-xs font-medium rounded-apple" style="background: rgba(94,92,230,0.15); color: rgba(94,92,230,1);">
                                    {{ ucfirst($topic->category ?? 'General') }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                @php
                                    $statusColors = [
                                        'pending' => 'background: rgba(255,214,10,0.15); color: rgba(255,214,10,1);',
                                        'scheduled' => 'background: rgba(10,132,255,0.15); color: rgba(10,132,255,1);',
                                        'published' => 'background: rgba(52,199,89,0.15); color: rgba(52,199,89,1);',
                                    ];
                                @endphp
                                <span class="px-2 py-1 text-xs font-medium rounded-apple" style="{{ $statusColors[$topic->status] ?? $statusColors['pending'] }}">
                                    {{ ucfirst($topic->status) }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                @php
                                    $marketMap = [
                                        'local' => ['label' => 'LOCAL', 'style' => 'background: rgba(52,199,89,0.15); color: rgba(52,199,89,1);'],
                                        'pma' => ['label' => 'PMA', 'style' => 'background: rgba(10,132,255,0.15); color: rgba(10,132,255,1);'],
                                        'both' => ['label' => 'BOTH', 'style' => 'background: rgba(175,82,222,0.15); color: rgba(175,82,222,1);'],
                                    ];
                                    $marketCfg = $marketMap[$topic->target_market ?? 'both'] ?? $marketMap['both'];
                                @endphp
                                <span class="px-2 py-1 text-xs font-medium rounded-apple" style="{{ $marketCfg['style'] }}">
                                    {{ $marketCfg['label'] }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                @php
                                    $title = Str::lower($topic->title ?? '');
                                    $detectedCategory = 'general';
                                    if (Str::contains($title, ['studi kasus', 'case study'])) {
                                        $detectedCategory = 'case-study';
                                    } elseif (Str::contains($title, ['breaking news', 'berita', 'tren '])) {
                                        $detectedCategory = 'news';
                                    } elseif (Str::contains($title, ['regulasi', 'peraturan', 'omnibus', 'pp ', 'permen'])) {
                                        $detectedCategory = 'regulation';
                                    } elseif (Str::contains($title, ['panduan', 'cara ', 'checklist', 'tips', 'strategi', 'optimalisasi', 'perbedaan'])) {
                                        $detectedCategory = 'tips';
                                    }

                                    $isFit = ($detectedCategory === $topic->category) || ($topic->category === 'general' && $detectedCategory === 'general');
                                @endphp
                                @if($isFit)
                                    <span class="px-2 py-1 text-xs font-medium rounded-apple" style="background: rgba(52,199,89,0.15); color: rgba(52,199,89,1);">
                                        Match
                                    </span>
                                @else
                                    <span class="px-2 py-1 text-xs font-medium rounded-apple" style="background: rgba(255,159,10,0.15); color: rgba(255,159,10,1);" title="Saran: {{ $detectedCategory }}">
                                        Review
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm text-dark-text-secondary">
                                @if($topic->scheduled_for)
                                    {{ $topic->scheduled_for->format('d M Y, H:i') }}
                                @else
                                    <span class="text-dark-text-tertiary">-</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('auto-post.topics.edit', $topic) }}" 
                                       class="p-2 rounded-apple text-dark-text-secondary hover:text-white hover:bg-white/10 transition-colors">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('auto-post.topics.destroy', $topic) }}" method="POST" class="inline" x-data @submit.prevent="if(confirm('Hapus topic ini?')) $el.submit()">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 rounded-apple text-dark-text-secondary hover:text-apple-red hover:bg-apple-red/10 transition-colors">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        {{-- Pagination --}}
        @if($topics->hasPages())
            <div class="px-4 py-3 auto-soft-divider-top">
                {{ $topics->appends(['tab' => 'topics'])->links() }}
            </div>
        @endif
    @else
        <div class="p-8 text-center">
            <i class="fas fa-lightbulb text-4xl text-dark-text-tertiary mb-3"></i>
            <p class="text-dark-text-secondary mb-4">Belum ada topic yang ditambahkan</p>
            <a href="{{ route('auto-post.topics.create') }}" 
               class="inline-flex items-center px-4 py-2 bg-apple-blue text-white rounded-apple text-sm font-medium hover:bg-blue-700 transition-apple">
                <i class="fas fa-plus mr-2"></i>Tambah Topic Pertama
            </a>
        </div>
    @endif
</div>
