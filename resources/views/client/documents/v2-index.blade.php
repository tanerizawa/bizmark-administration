{{-- Documents Index — Portal v2 --}}
@php
    $totalDocs    = $stats['total'] ?? 0;
    $monthlyDocs  = $stats['this_month'] ?? 0;
    $pendingDocs  = $stats['pending'] ?? collect($documents)->where('status','pending')->count();
    $categoryCounts = collect($documents)->groupBy('category')->map->count()->sortDesc();
    $topCategory  = $categoryCounts->keys()->first() ?? '—';
    $topCategoryCount = $categoryCounts->first() ?? 0;
    $currentSearch = request('search','');
    $currentProject = request('project_id','');
    $currentType = request('document_type','');
@endphp

{{-- ─── HERO ─── --}}
<section class="portal-hero relative overflow-hidden border-b border-[var(--border-subtle)]"
         style="background: linear-gradient(135deg, var(--client-primary) 0%, color-mix(in oklab, var(--client-primary) 65%, #001020) 100%); color:#fff;"
         aria-label="Dokumen perizinan">
    <div class="portal-glow-orb portal-glow-orb--tr hidden lg:block" aria-hidden="true"
         style="background: radial-gradient(circle at 50% 50%, rgba(255,255,255,0.18) 0%, transparent 70%);"></div>

    <div class="relative max-w-[1400px] mx-auto px-4 lg:px-8 py-5 lg:py-7">
        <div class="flex items-start justify-between gap-4 mb-5">
            <div>
                <span class="portal-eyebrow" style="background: rgba(255,255,255,0.15); color: rgba(255,255,255,0.9); border-color: rgba(255,255,255,0.2);">
                    <i class="fas fa-paperclip text-[9px]" aria-hidden="true"></i>
                    Dokumen Perizinan
                </span>
                <h1 class="mt-2 text-2xl font-bold text-white leading-tight">{{ $totalDocs }} Dokumen</h1>
                <p class="mt-1 text-sm text-white/80">Kelola semua berkas dan lampiran perizinan Anda.</p>
            </div>
            <button type="button" @click="$dispatch('drawer-open', { name: 'doc-upload' })"
                    class="hidden lg:inline-flex items-center gap-2 bg-white text-[var(--client-primary)] font-semibold text-sm px-4 py-2.5 rounded-lg shadow-sm hover:shadow-md transition-shadow flex-shrink-0">
                <i class="fas fa-upload text-xs" aria-hidden="true"></i> Upload Dokumen
            </button>
        </div>

        <div class="portal-stat-strip grid grid-cols-2 lg:grid-cols-4 gap-3">
            @foreach([
                ['label'=>'Total Dokumen',   'value'=>$totalDocs,        'sub'=>'Semua proyek',     'icon'=>'fa-file'],
                ['label'=>'Bulan Ini',       'value'=>$monthlyDocs,      'sub'=>'Baru diunggah',    'icon'=>'fa-calendar-plus'],
                ['label'=>'Menunggu Review', 'value'=>$pendingDocs,      'sub'=>'Perlu tindakan',   'icon'=>'fa-clock'],
                ['label'=>'Kategori Utama',  'value'=>$topCategoryCount, 'sub'=>$topCategory,       'icon'=>'fa-layer-group'],
            ] as $s)
            <div class="bg-white/10 backdrop-blur border border-white/15 rounded-lg px-4 py-3">
                <div class="flex items-center justify-between mb-1">
                    <p class="text-[11px] uppercase tracking-wider text-white/70 font-semibold">{{ $s['label'] }}</p>
                    <i class="fas {{ $s['icon'] }} text-white/40 text-xs" aria-hidden="true"></i>
                </div>
                <p class="text-2xl font-bold tabular-nums text-white leading-none">{{ $s['value'] }}</p>
                <p class="text-[11px] text-white/60 mt-0.5 capitalize">{{ $s['sub'] }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ─── FILTER BAR ─── --}}
<div class="sticky top-0 z-20 bg-[var(--surface-elevated)]/95 backdrop-blur border-b border-[var(--border-subtle)]"
     x-data="{ search: @js($currentSearch), project: @js($currentProject), type: @js($currentType),
         submit() {
             const u = new URL(window.location.href);
             this.search  ? u.searchParams.set('search',  this.search)  : u.searchParams.delete('search');
             this.project ? u.searchParams.set('project_id', this.project) : u.searchParams.delete('project_id');
             this.type    ? u.searchParams.set('document_type', this.type) : u.searchParams.delete('document_type');
             u.searchParams.delete('page');
             window.location.href = u.toString();
         }
     }">
    <div class="max-w-[1400px] mx-auto px-4 lg:px-8 py-3 flex flex-wrap items-center gap-2">
        <div class="relative flex-1 min-w-[180px]">
            <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-[var(--text-tertiary)] text-xs" aria-hidden="true"></i>
            <input type="text" x-model="search" @keydown.enter.prevent="submit()"
                   placeholder="Cari nama dokumen…"
                   class="w-full pl-9 pr-4 py-2 text-sm bg-[var(--surface-cool)] border border-[var(--border-subtle)] rounded-lg focus:ring-2 focus:ring-[var(--client-primary)] text-[var(--text-primary)] placeholder-[var(--text-tertiary)]">
        </div>
        @if($projects->count())
        <select x-model="project" @change="submit()"
                class="py-2 px-3 text-sm bg-[var(--surface-cool)] border border-[var(--border-subtle)] rounded-lg text-[var(--text-primary)]">
            <option value="">Semua Proyek</option>
            @foreach($projects as $proj)
            <option value="{{ $proj->id }}" {{ $currentProject == $proj->id ? 'selected' : '' }}>{{ $proj->name }}</option>
            @endforeach
        </select>
        @endif
        @if(count($documentTypes ?? []))
        <select x-model="type" @change="submit()"
                class="py-2 px-3 text-sm bg-[var(--surface-cool)] border border-[var(--border-subtle)] rounded-lg text-[var(--text-primary)]">
            <option value="">Semua Tipe</option>
            @foreach($documentTypes as $t)
            <option value="{{ $t }}" {{ $currentType === $t ? 'selected' : '' }}>{{ $t }}</option>
            @endforeach
        </select>
        @endif
        <button type="button" @click="$dispatch('drawer-open', { name: 'doc-upload' })"
                class="lg:hidden inline-flex items-center gap-1.5 px-3 py-2 bg-[var(--client-primary)] text-white text-sm font-semibold rounded-lg">
            <i class="fas fa-upload text-xs" aria-hidden="true"></i> Upload
        </button>
    </div>
</div>

{{-- ─── LIST ─── --}}
<div class="max-w-[1400px] mx-auto px-4 lg:px-8 py-6">
    @if(empty($documents) || count($documents) === 0)
    <x-ui.empty-state icon="fas fa-file-arrow-up" title="Belum ada dokumen"
        description="Upload berkas perizinan untuk melengkapi permohonan Anda."
        :action="['label' => 'Upload Sekarang', 'url' => '#']" />
    @else
    <div class="bg-[var(--surface-elevated)] border border-[var(--border-subtle)] rounded-xl overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-[var(--surface-cool)] border-b border-[var(--border-subtle)]">
                <tr>
                    <th class="text-left px-4 py-3 text-[11px] font-semibold uppercase tracking-wider text-[var(--text-tertiary)]">Dokumen</th>
                    <th class="text-left px-4 py-3 text-[11px] font-semibold uppercase tracking-wider text-[var(--text-tertiary)] hidden md:table-cell">Proyek</th>
                    <th class="text-left px-4 py-3 text-[11px] font-semibold uppercase tracking-wider text-[var(--text-tertiary)] hidden lg:table-cell">Tipe</th>
                    <th class="text-left px-4 py-3 text-[11px] font-semibold uppercase tracking-wider text-[var(--text-tertiary)]">Status</th>
                    <th class="text-right px-4 py-3 text-[11px] font-semibold uppercase tracking-wider text-[var(--text-tertiary)] hidden sm:table-cell">Tanggal</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[var(--border-subtle)]">
                @foreach($documents as $doc)
                @php
                    $fileIcon = str_contains($doc->mime_type ?? '', 'pdf') ? 'fa-file-pdf text-red-500'
                              : (str_contains($doc->mime_type ?? '', 'image') ? 'fa-file-image text-blue-500'
                              : (str_contains($doc->mime_type ?? '', 'word') ? 'fa-file-word text-blue-700' : 'fa-file text-[var(--text-tertiary)]'));
                    $docStatus = $doc->status ?? 'uploaded';
                @endphp
                <tr class="hover:bg-[var(--surface-cool)] transition-colors">
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-3">
                            <i class="fas {{ $fileIcon }} text-base flex-shrink-0" aria-hidden="true"></i>
                            <div class="min-w-0">
                                <p class="text-sm font-semibold text-[var(--text-primary)] truncate max-w-[200px]">
                                    {{ $doc->document_name ?? $doc->file_name ?? 'Dokumen' }}
                                </p>
                                @if($doc->description)
                                <p class="text-xs text-[var(--text-tertiary)] truncate max-w-[200px]">{{ $doc->description }}</p>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td class="px-4 py-3 hidden md:table-cell">
                        <p class="text-xs text-[var(--text-secondary)] truncate max-w-[120px]">
                            {{ $doc->project?->name ?? $doc->application?->application_number ?? '—' }}
                        </p>
                    </td>
                    <td class="px-4 py-3 hidden lg:table-cell">
                        <span class="text-xs text-[var(--text-tertiary)]">{{ $doc->document_type ?? $doc->category ?? '—' }}</span>
                    </td>
                    <td class="px-4 py-3">
                        <x-ui.status-pill :status="$docStatus" />
                    </td>
                    <td class="px-4 py-3 text-right hidden sm:table-cell">
                        <time class="text-xs text-[var(--text-tertiary)]">{{ $doc->created_at->format('d M Y') }}</time>
                    </td>
                    <td class="px-4 py-3 text-right">
                        @if($doc->file_path)
                        <a href="{{ Storage::url($doc->file_path) }}" target="_blank" rel="noopener"
                           class="inline-flex items-center gap-1 text-xs font-semibold text-[var(--client-primary)] hover:underline">
                            <i class="fas fa-download text-[10px]" aria-hidden="true"></i>
                            <span class="hidden sm:inline">Unduh</span>
                        </a>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
</div>

{{-- ─── UPLOAD DRAWER ─── --}}
<x-ui.drawer name="doc-upload" size="md" title="Upload Dokumen" subtitle="Tambahkan berkas perizinan baru">
    @if(request()->route())
    <form method="POST" action="{{ route('client.documents.store') }}" enctype="multipart/form-data" class="space-y-4">
        @csrf
        @if($projects->count())
        <div>
            <label class="block text-xs font-semibold text-[var(--text-primary)] mb-1.5">Proyek <span class="text-[var(--apple-red)]">*</span></label>
            <select name="project_id" required class="w-full px-3 py-2.5 bg-[var(--surface-cool)] border border-[var(--border-subtle)] rounded-lg text-sm text-[var(--text-primary)]">
                <option value="">Pilih proyek…</option>
                @foreach($projects as $proj)
                <option value="{{ $proj->id }}">{{ $proj->name }}</option>
                @endforeach
            </select>
        </div>
        @endif
        <div>
            <label class="block text-xs font-semibold text-[var(--text-primary)] mb-1.5">Tipe Dokumen</label>
            <select name="document_type" class="w-full px-3 py-2.5 bg-[var(--surface-cool)] border border-[var(--border-subtle)] rounded-lg text-sm text-[var(--text-primary)]">
                <option value="">Pilih tipe…</option>
                @foreach($documentTypes ?? [] as $t)
                <option value="{{ $t }}">{{ $t }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-semibold text-[var(--text-primary)] mb-1.5">File <span class="text-[var(--apple-red)]">*</span></label>
            <input type="file" name="file" required accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
                   class="w-full px-3 py-2 bg-[var(--surface-cool)] border border-[var(--border-subtle)] rounded-lg text-sm text-[var(--text-primary)]">
            <p class="text-[10px] text-[var(--text-tertiary)] mt-1">PDF, DOC, DOCX, JPG, PNG — Maks 10MB</p>
        </div>
        <div>
            <label class="block text-xs font-semibold text-[var(--text-primary)] mb-1.5">Catatan</label>
            <textarea name="description" rows="3" placeholder="Keterangan tambahan…"
                      class="w-full px-3 py-2.5 bg-[var(--surface-cool)] border border-[var(--border-subtle)] rounded-lg text-sm text-[var(--text-primary)] placeholder-[var(--text-tertiary)] resize-none"></textarea>
        </div>
        <button type="submit"
                class="w-full inline-flex items-center justify-center gap-2 px-4 py-3 bg-[var(--client-primary)] hover:brightness-110 text-white text-sm font-semibold rounded-lg transition-all">
            <i class="fas fa-upload text-xs" aria-hidden="true"></i> Upload Dokumen
        </button>
    </form>
    @endif
</x-ui.drawer>
