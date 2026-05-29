@php
    $catLabels = [
        'izin_utama'        => 'Izin Utama',
        'dokumen_pendukung' => 'Dokumen Pendukung',
        'laporan'           => 'Laporan',
        'sertifikat'        => 'Sertifikat',
        'lainnya'           => 'Lainnya',
    ];
    $catIcons = [
        'izin_utama'        => 'fa-building-columns',
        'dokumen_pendukung' => 'fa-file-lines',
        'laporan'           => 'fa-chart-bar',
        'sertifikat'        => 'fa-award',
        'lainnya'           => 'fa-folder',
    ];
    $allDocs = $documents instanceof \Illuminate\Pagination\LengthAwarePaginator
        ? $documents->getCollection()
        : collect($documents);
    $byCat = $allDocs->groupBy(fn($d) => $d->vault_category ?? 'lainnya');
@endphp

{{-- Hero --}}
<div class="client-hero px-4 sm:px-6 py-6">
    <div class="max-w-5xl mx-auto">
        <div class="flex items-center gap-3 mb-2">
            <i class="fas fa-folder-open text-xl"></i>
            <h1 class="text-2xl font-bold tracking-tight">Vault Dokumen</h1>
        </div>
        <p class="text-blue-200 text-sm mb-5">Semua dokumen resmi proyek Anda tersimpan aman di sini.</p>
        <div class="grid grid-cols-3 gap-3">
            <div class="bg-white/10 backdrop-blur rounded-xl px-4 py-3 text-center">
                <p class="text-2xl font-bold">{{ $stats['total'] }}</p>
                <p class="text-xs text-blue-200 mt-0.5">Total Dokumen</p>
            </div>
            <div class="bg-amber-500/20 border border-amber-400/30 rounded-xl px-4 py-3 text-center">
                <p class="text-2xl font-bold text-amber-200">{{ $stats['expiring'] }}</p>
                <p class="text-xs text-amber-200 mt-0.5">Segera Expire</p>
            </div>
            <div class="bg-red-500/20 border border-red-400/30 rounded-xl px-4 py-3 text-center">
                <p class="text-2xl font-bold text-red-200">{{ $stats['expired'] }}</p>
                <p class="text-xs text-red-200 mt-0.5">Sudah Expired</p>
            </div>
        </div>
    </div>
</div>

<div class="max-w-5xl mx-auto px-4 sm:px-6 py-5"
     x-data="{
         selected: [],
         query: '{{ request('search') }}',
         catFilter: '{{ request('category', 'all') }}',
         showPreview: false,
         previewUrl: null,
         previewTitle: '',
         previewIsImage: false,
         toggleSelect(id) {
             if (this.selected.includes(id)) this.selected = this.selected.filter(i => i !== id);
             else this.selected.push(id);
         },
         openPreview(url, title, isImage) {
             this.previewUrl = url; this.previewTitle = title; this.previewIsImage = isImage;
             this.showPreview = true;
         },
         submitSearch() { window.location.href = '{{ route('client.vault.index') }}?search=' + encodeURIComponent(this.query) + '&category=' + this.catFilter; }
     }">

    {{-- Flash --}}
    @if(session('success'))
    <div class="flex items-center gap-2 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-xl px-4 py-3 text-sm text-green-800 dark:text-green-300 mb-4">
        <i class="fas fa-circle-check text-green-500"></i> {{ session('success') }}
    </div>
    @endif

    {{-- Filter bar --}}
    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-3 mb-4">
        <div class="flex items-center gap-2 flex-wrap">
            {{-- Debounce search --}}
            <div class="relative flex-1 min-w-[160px]">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs pointer-events-none"></i>
                <input type="text" x-model="query"
                       @input.debounce.400ms="submitSearch()"
                       placeholder="Cari nama / nomor dokumen…"
                       class="w-full pl-8 pr-3 py-2 text-sm border border-gray-200 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-[#0a66c2] focus:border-[#0a66c2]">
            </div>
            {{-- Project filter --}}
            @if($projects->count() > 0)
            <select name="project_id" onchange="window.location.href='{{ route('client.vault.index') }}?project_id=' + this.value + '&search={{ request('search') }}'"
                    class="text-sm border border-gray-200 dark:border-gray-600 rounded-lg px-3 py-2 bg-white dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-[#0a66c2]">
                <option value="">Semua Proyek</option>
                @foreach($projects as $project)
                    <option value="{{ $project->id }}" {{ request('project_id') == $project->id ? 'selected' : '' }}>{{ $project->name }}</option>
                @endforeach
            </select>
            @endif
            @if(request()->hasAny(['category','project_id','search']))
            <a href="{{ route('client.vault.index') }}" class="text-xs text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 px-2 py-2">
                <i class="fas fa-xmark"></i> Reset
            </a>
            @endif
        </div>
        {{-- Category chip filters --}}
        <div class="flex items-center gap-2 flex-wrap mt-2.5 pt-2.5 border-t border-gray-100 dark:border-gray-700">
            <a href="{{ route('client.vault.index') }}?search={{ request('search') }}&project_id={{ request('project_id') }}"
               class="inline-flex items-center gap-1 px-3 py-1 text-xs font-medium rounded-full border transition
               {{ !request('category') ? 'bg-[#0a66c2] text-white border-[#0a66c2]' : 'border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-400 hover:border-[#0a66c2]' }}">
                Semua
            </a>
            @foreach($catLabels as $catKey => $catName)
            <a href="{{ route('client.vault.index') }}?category={{ $catKey }}&search={{ request('search') }}&project_id={{ request('project_id') }}"
               class="inline-flex items-center gap-1 px-3 py-1 text-xs font-medium rounded-full border transition
               {{ request('category') === $catKey ? 'bg-[#0a66c2] text-white border-[#0a66c2]' : 'border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-400 hover:border-[#0a66c2]' }}">
                <i class="fas {{ $catIcons[$catKey] ?? 'fa-folder' }} text-[9px]"></i> {{ $catName }}
                @php $cnt = $byCat->get($catKey)?->count() ?? 0; @endphp
                @if($cnt)<span class="opacity-70">{{ $cnt }}</span>@endif
            </a>
            @endforeach
        </div>
    </div>

    {{-- Bulk action bar (sticky) --}}
    <div x-show="selected.length > 0" x-transition
         style="display:none"
         class="sticky top-16 z-20 mb-4 flex items-center gap-3 bg-[#0a66c2] text-white px-4 py-3 rounded-xl shadow-lg">
        <span class="text-sm font-semibold flex-1">
            <span x-text="selected.length"></span> dokumen dipilih
        </span>
        <form method="POST" action="{{ route('client.vault.bulk-download') }}"
              @submit.prevent="$el.querySelector('input[name=ids]').value = selected.join(','); $el.submit()">
            @csrf
            <input type="hidden" name="ids" value="">
            <button type="submit"
                    class="inline-flex items-center gap-1.5 text-xs font-semibold px-3 py-1.5 bg-white text-[#0a66c2] rounded-lg hover:bg-blue-50 transition active:scale-95">
                <i class="fas fa-download text-[10px]"></i> Download ZIP
            </button>
        </form>
        <button type="button" @click="selected = []"
                class="inline-flex items-center gap-1 text-xs font-medium px-3 py-1.5 bg-white/20 hover:bg-white/30 rounded-lg transition">
            <i class="fas fa-xmark text-[10px]"></i> Batal
        </button>
    </div>

    {{-- Empty State --}}
    @if($documents->isEmpty())
        @include('client.components.empty-state', [
            'icon'    => 'fa-folder-open',
            'title'   => 'Belum Ada Dokumen',
            'message' => request()->hasAny(['category','project_id','search'])
                ? 'Tidak ada dokumen sesuai filter.'
                : 'Tim Bizmark akan meng-upload dokumen proyek Anda ke sini.',
            'ctaLabel' => request()->hasAny(['category','project_id','search']) ? 'Reset Filter' : null,
            'ctaHref'  => request()->hasAny(['category','project_id','search']) ? route('client.vault.index') : null,
            'size'     => 'md',
            'color'    => 'gray',
        ])
    @else

    @foreach($catLabels as $catKey => $catName)
        @if($byCat->has($catKey))
        @php $catDocs = $byCat->get($catKey); @endphp
        <section class="mb-6">
            <div class="flex items-center gap-2 mb-3">
                <i class="fas {{ $catIcons[$catKey] ?? 'fa-folder' }} text-[#0a66c2] text-sm"></i>
                <h2 class="text-sm font-bold text-gray-800 dark:text-gray-200">{{ $catName }}</h2>
                <span class="ml-auto text-xs bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400 px-2 py-0.5 rounded-full">{{ $catDocs->count() }}</span>
            </div>

            <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                @foreach($catDocs as $doc)
                @php
                    $isExpiring = $doc->document_expires_at && $doc->document_expires_at->isFuture() && $doc->document_expires_at->lte(now()->addDays(90));
                    $isExpired  = $doc->document_expires_at && $doc->document_expires_at->isPast();
                    $ext        = strtolower(pathinfo($doc->file_path ?? '', PATHINFO_EXTENSION));
                    $isImage    = in_array($ext, ['jpg','jpeg','png','webp','gif']);
                    $isPdf      = $ext === 'pdf';
                    $canPreview = $isImage || $isPdf;
                    $downloadUrl = $doc->file_path ? route('client.vault.download', $doc) : null;
                @endphp
                <div class="bg-white dark:bg-gray-800 border {{ $isExpired ? 'border-red-300 dark:border-red-700' : ($isExpiring ? 'border-amber-300 dark:border-amber-700' : 'border-gray-200 dark:border-gray-700') }} rounded-xl p-4 flex flex-col gap-2 hover:shadow-md transition relative"
                     :class="selected.includes({{ $doc->id }}) ? 'ring-2 ring-[#0a66c2]' : ''">
                    {{-- Select checkbox --}}
                    @if($doc->file_path)
                    <label class="absolute top-2 right-2 cursor-pointer">
                        <input type="checkbox" class="sr-only peer" @click.stop="toggleSelect({{ $doc->id }})">
                        <div class="w-5 h-5 rounded border-2 border-gray-300 dark:border-gray-600 flex items-center justify-center
                            peer-checked:bg-[#0a66c2] peer-checked:border-[#0a66c2] transition"
                             :class="selected.includes({{ $doc->id }}) ? 'bg-[#0a66c2] border-[#0a66c2]' : 'border-gray-300 dark:border-gray-600'">
                            <i class="fas fa-check text-white text-[8px]"
                               x-show="selected.includes({{ $doc->id }})"></i>
                        </div>
                    </label>
                    @endif

                    {{-- Header --}}
                    <div class="flex items-start gap-2 pr-6">
                        <div class="flex-1 min-w-0">
                            <p class="font-semibold text-sm text-gray-800 dark:text-gray-100 truncate" title="{{ $doc->title }}">{{ $doc->title }}</p>
                            @if($doc->document_number)
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">No: {{ $doc->document_number }}</p>
                            @endif
                        </div>
                    </div>

                    {{-- Status badge --}}
                    <div>
                        @if($isExpired)
                            <span class="text-[10px] font-semibold bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300 px-2 py-0.5 rounded-full">Expired</span>
                        @elseif($isExpiring)
                            <span class="text-[10px] font-semibold bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-300 px-2 py-0.5 rounded-full">Segera Expire</span>
                        @else
                            <span class="text-[10px] font-semibold bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300 px-2 py-0.5 rounded-full">Aktif</span>
                        @endif
                    </div>

                    {{-- Meta --}}
                    <div class="text-xs text-gray-500 dark:text-gray-400 space-y-0.5">
                        <div>Proyek: <span class="text-gray-700 dark:text-gray-300">{{ $doc->project?->name ?? '-' }}</span></div>
                        @if($doc->document_issued_at)
                        <div>Terbit: <span class="text-gray-700 dark:text-gray-300">{{ $doc->document_issued_at->format('d/m/Y') }}</span></div>
                        @endif
                        @if($doc->document_expires_at)
                        <div>Hingga:
                            <span class="{{ $isExpired ? 'text-red-600 dark:text-red-400 font-semibold' : ($isExpiring ? 'text-amber-600 dark:text-amber-400 font-semibold' : 'text-gray-700 dark:text-gray-300') }}">
                                {{ $doc->document_expires_at->format('d/m/Y') }}
                                @if($isExpiring && !$isExpired)({{ $doc->document_expires_at->diffForHumans() }})@endif
                            </span>
                        </div>
                        @endif
                    </div>

                    {{-- Actions --}}
                    <div class="mt-auto pt-2 flex gap-2">
                        @if($doc->file_path)
                        @if($canPreview)
                        <button type="button"
                                @click="openPreview('{{ $downloadUrl }}', '{{ addslashes($doc->title) }}', {{ $isImage ? 'true' : 'false' }})"
                                class="flex-1 text-center text-xs font-medium border border-[#0a66c2] text-[#0a66c2] hover:bg-blue-50 dark:hover:bg-[#0a66c2]/10 py-2 px-3 rounded-lg transition">
                            <i class="fas fa-eye text-[10px] mr-1"></i> Preview
                        </button>
                        @endif
                        <a href="{{ $downloadUrl }}"
                           class="flex-1 text-center text-xs font-medium bg-[#0a66c2] hover:bg-[#004182] text-white py-2 px-3 rounded-lg transition">
                            <i class="fas fa-download text-[10px] mr-1"></i> Unduh
                        </a>
                        @else
                        <span class="flex-1 text-center text-xs text-gray-400 bg-gray-100 dark:bg-gray-700 py-2 px-3 rounded-lg">Belum tersedia</span>
                        @endif

                        @if($isExpired || $isExpiring)
                        @php
                            $waText = 'Halo Bizmark, saya ingin perpanjang dokumen: ' . $doc->title;
                            $waHref = 'https://wa.me/' . preg_replace('/\D/', '', config('landing_metrics.whatsapp_number', '6281234567890')) . '?text=' . rawurlencode($waText);
                        @endphp
                        <a href="{{ $waHref }}" target="_blank" rel="noopener"
                           class="text-xs font-medium border border-green-500 text-green-600 dark:text-green-400 hover:bg-green-50 dark:hover:bg-green-900/20 py-2 px-3 rounded-lg transition">
                            <i class="fab fa-whatsapp text-[10px]"></i>
                        </a>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </section>
        @endif
    @endforeach

    {{-- Pagination --}}
    @if($documents instanceof \Illuminate\Pagination\LengthAwarePaginator && $documents->hasPages())
    <div class="mt-4">{{ $documents->links() }}</div>
    @endif
    @endif

    {{-- Preview Modal --}}
    <div x-show="showPreview" x-transition
         style="display:none"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm"
         @click.self="showPreview = false"
         @keydown.escape.window="showPreview = false">
        <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-2xl max-w-3xl w-full max-h-[90vh] flex flex-col overflow-hidden">
            <div class="flex items-center justify-between px-4 py-3 border-b border-gray-200 dark:border-gray-700 flex-shrink-0">
                <p class="font-semibold text-sm text-gray-900 dark:text-white truncate" x-text="previewTitle"></p>
                <button type="button" @click="showPreview = false"
                        class="ml-3 w-7 h-7 flex items-center justify-center rounded-full hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-500 transition">
                    <i class="fas fa-xmark text-sm"></i>
                </button>
            </div>
            <div class="flex-1 overflow-auto flex items-center justify-center p-4 min-h-0">
                <template x-if="previewIsImage">
                    <img :src="previewUrl" class="max-w-full max-h-full object-contain rounded-lg" alt="Preview">
                </template>
                <template x-if="!previewIsImage && previewUrl">
                    <iframe :src="previewUrl + '?preview=1'" class="w-full h-full min-h-[60vh] rounded-lg border-0" title="Document Preview"></iframe>
                </template>
            </div>
            <div class="px-4 py-3 border-t border-gray-100 dark:border-gray-700 flex justify-end flex-shrink-0">
                <a :href="previewUrl" class="inline-flex items-center gap-2 px-4 py-2 bg-[#0a66c2] hover:bg-[#004182] text-white text-sm font-semibold rounded-xl transition active:scale-95">
                    <i class="fas fa-download text-xs"></i> Download
                </a>
            </div>
        </div>
    </div>

</div>
@endsection
        'izin_utama'        => 'Izin Utama',
        'dokumen_pendukung' => 'Dokumen Pendukung',
        'laporan'           => 'Laporan',
        'sertifikat'        => 'Sertifikat',
        'lainnya'           => 'Lainnya',
    ];
    $catIcons = [
        'izin_utama'        => '🏛️',
        'dokumen_pendukung' => '📄',
        'laporan'           => '📊',
        'sertifikat'        => '🎖️',
        'lainnya'           => '📁',
    ];
@endphp

{{-- Hero --}}
<div class="client-hero px-6 py-8">
    <div class="max-w-5xl mx-auto">
        <div class="flex items-center gap-3 mb-2">
            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 19a2 2 0 01-2-2V7a2 2 0 012-2h4l2 2h4a2 2 0 012 2v1M5 19h14a2 2 0 002-2v-5a2 2 0 00-2-2H9a2 2 0 00-2 2v5a2 2 0 01-2 2z"/>
            </svg>
            <h1 class="text-2xl font-bold tracking-tight">Vault Dokumen</h1>
        </div>
        <p class="text-blue-200 text-sm mb-6">Semua dokumen resmi proyek Anda tersimpan aman di sini.</p>

        {{-- Stats --}}
        <div class="grid grid-cols-3 gap-3">
            <div class="bg-white/10 rounded-xl p-4 text-center">
                <div class="text-2xl font-bold">{{ $stats['total'] }}</div>
                <div class="text-xs text-blue-200 mt-1">Total Dokumen</div>
            </div>
            <div class="bg-amber-500/20 border border-amber-400/30 rounded-xl p-4 text-center">
                <div class="text-2xl font-bold text-amber-200">{{ $stats['expiring'] }}</div>
                <div class="text-xs text-amber-200 mt-1">Segera Expire</div>
            </div>
            <div class="bg-red-500/20 border border-red-400/30 rounded-xl p-4 text-center">
                <div class="text-2xl font-bold text-red-200">{{ $stats['expired'] }}</div>
                <div class="text-xs text-red-200 mt-1">Sudah Expired</div>
            </div>
        </div>
    </div>
</div>

<div class="max-w-5xl mx-auto px-4 py-6 space-y-6">

    {{-- Flash --}}
    @if(session('success'))
    <div class="bg-green-50 border border-green-200 text-green-800 rounded-lg px-4 py-3 text-sm">
        {{ session('success') }}
    </div>
    @endif

    {{-- Filter --}}
    <form method="GET" class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-4 flex flex-wrap gap-3 items-end">
        <div class="flex-1 min-w-[150px]">
            <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Kategori</label>
            <select name="category" class="w-full text-sm border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200">
                <option value="">Semua Kategori</option>
                @foreach($categories as $val => $label)
                    <option value="{{ $val }}" {{ request('category') === $val ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex-1 min-w-[150px]">
            <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Proyek</label>
            <select name="project_id" class="w-full text-sm border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200">
                <option value="">Semua Proyek</option>
                @foreach($projects as $project)
                    <option value="{{ $project->id }}" {{ request('project_id') == $project->id ? 'selected' : '' }}>{{ $project->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex-1 min-w-[180px]">
            <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Cari</label>
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Nama / nomor dokumen…"
                   class="w-full text-sm border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200">
        </div>
        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-5 py-2 rounded-lg transition">
            Filter
        </button>
        @if(request()->hasAny(['category','project_id','search']))
        <a href="{{ route('client.vault.index') }}" class="text-sm text-gray-500 hover:text-gray-700 py-2">Reset</a>
        @endif
    </form>

    {{-- Empty State --}}
    @if($documents->isEmpty())
    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-12 text-center">
        <div class="text-5xl mb-4">📂</div>
        <h3 class="font-semibold text-gray-700 dark:text-gray-300 mb-1">Belum Ada Dokumen</h3>
        <p class="text-sm text-gray-500 dark:text-gray-400">
            @if(request()->hasAny(['category','project_id','search']))
                Tidak ada dokumen sesuai filter. <a href="{{ route('client.vault.index') }}" class="text-blue-600 underline">Reset filter</a>
            @else
                Tim Bizmark akan meng-upload dokumen proyek Anda ke sini.
            @endif
        </p>
    </div>

    @else
    {{-- Grouped by category --}}
    @php
        $byCat = $documents->getCollection()->groupBy(fn($d) => $d->vault_category ?? 'lainnya');
    @endphp

    @foreach($catLabels as $catKey => $catName)
        @if($byCat->has($catKey))
        @php $catDocs = $byCat->get($catKey); @endphp
        <section>
            <div class="flex items-center gap-2 mb-3">
                <span class="text-lg">{{ $catIcons[$catKey] ?? '📁' }}</span>
                <h2 class="text-base font-semibold text-gray-800 dark:text-gray-200">{{ $catName }}</h2>
                <span class="ml-auto text-xs bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400 px-2 py-0.5 rounded-full">{{ $catDocs->count() }} dokumen</span>
            </div>

            <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                @foreach($catDocs as $doc)
                @php
                    $isExpiring = $doc->document_expires_at && $doc->document_expires_at->isFuture() && $doc->document_expires_at->lte(now()->addDays(90));
                    $isExpired  = $doc->document_expires_at && $doc->document_expires_at->isPast();
                @endphp
                <div class="bg-white dark:bg-gray-800 border {{ $isExpired ? 'border-red-300 dark:border-red-700' : ($isExpiring ? 'border-amber-300 dark:border-amber-700' : 'border-gray-200 dark:border-gray-700') }} rounded-xl p-4 flex flex-col gap-2 hover:shadow-md transition">
                    {{-- Title & Status --}}
                    <div class="flex items-start gap-2">
                        <div class="flex-1 min-w-0">
                            <p class="font-medium text-sm text-gray-800 dark:text-gray-100 truncate" title="{{ $doc->title }}">{{ $doc->title }}</p>
                            @if($doc->document_number)
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">No: {{ $doc->document_number }}</p>
                            @endif
                        </div>
                        @if($isExpired)
                            <span class="shrink-0 text-xs bg-red-100 dark:bg-red-900/40 text-red-700 dark:text-red-300 px-2 py-0.5 rounded-full">Expired</span>
                        @elseif($isExpiring)
                            <span class="shrink-0 text-xs bg-amber-100 dark:bg-amber-900/40 text-amber-700 dark:text-amber-300 px-2 py-0.5 rounded-full">Segera Expire</span>
                        @else
                            <span class="shrink-0 text-xs bg-green-100 dark:bg-green-900/40 text-green-700 dark:text-green-300 px-2 py-0.5 rounded-full">Aktif</span>
                        @endif
                    </div>

                    {{-- Meta --}}
                    <div class="text-xs text-gray-500 dark:text-gray-400 space-y-0.5">
                        <div>Proyek: <span class="text-gray-700 dark:text-gray-300">{{ $doc->project?->name ?? '-' }}</span></div>
                        @if($doc->document_issued_at)
                        <div>Diterbitkan: <span class="text-gray-700 dark:text-gray-300">{{ $doc->document_issued_at->format('d/m/Y') }}</span></div>
                        @endif
                        @if($doc->document_expires_at)
                        <div>
                            Berlaku hingga:
                            <span class="{{ $isExpired ? 'text-red-600 dark:text-red-400 font-semibold' : ($isExpiring ? 'text-amber-600 dark:text-amber-400 font-semibold' : 'text-gray-700 dark:text-gray-300') }}">
                                {{ $doc->document_expires_at->format('d/m/Y') }}
                                @if($isExpiring && !$isExpired)
                                    ({{ $doc->document_expires_at->diffForHumans() }})
                                @endif
                            </span>
                        </div>
                        @endif
                    </div>

                    {{-- Actions --}}
                    <div class="mt-auto pt-2 flex gap-2">
                        @if($doc->file_path)
                        <a href="{{ route('client.vault.download', $doc) }}"
                           class="flex-1 text-center text-xs font-medium bg-blue-600 hover:bg-blue-700 text-white py-2 px-3 rounded-lg transition">
                            ⬇ Unduh
                        </a>
                        @else
                        <span class="flex-1 text-center text-xs text-gray-400 bg-gray-100 dark:bg-gray-700 py-2 px-3 rounded-lg">File belum tersedia</span>
                        @endif

                        @if($isExpired || $isExpiring)
                        <a href="https://wa.me/{{ preg_replace('/\D/', '', config('landing_metrics.whatsapp_number', '6281234567890')) }}?text={{ urlencode('Halo Bizmark, saya ingin perpanjang dokumen: ' . $doc->title) }}"
                           target="_blank" rel="noopener"
                           class="text-xs font-medium border border-green-500 text-green-600 dark:text-green-400 hover:bg-green-50 dark:hover:bg-green-900/20 py-2 px-3 rounded-lg transition">
                            WA
                        </a>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </section>
        @endif
    @endforeach

    {{-- Pagination --}}
    @if($documents->hasPages())
    <div class="mt-6">
        {{ $documents->links() }}
    </div>
    @endif
    @endif

</div>
