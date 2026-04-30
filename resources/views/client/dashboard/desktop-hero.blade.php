    {{-- ============================================================ --}}
    {{-- DESKTOP HERO HEADER                                          --}}
    {{-- ============================================================ --}}
    <div class="hidden lg:block bg-[#0a66c2] border-y border-[#0a66c2] text-white" role="banner">
        <div class="max-w-6xl mx-auto px-6 lg:px-8 py-8">
            {{-- Title + CTA --}}
            <div class="flex items-start justify-between gap-8 mb-6">
                <div class="flex-1">
                    <h1 class="text-2xl lg:text-3xl font-bold leading-tight mb-2">
                        Hai, {{ $client->name }}
                    </h1>
                    <p class="text-base text-white/90 leading-normal">
                        Pantau progres izin usaha dan kelola proyek Anda
                    </p>
                </div>
                <div class="flex gap-3 flex-shrink-0">
                    <a href="{{ route('client.applications.create') }}"
                       class="inline-flex items-center gap-2 bg-white text-[#0a66c2] font-semibold px-5 py-3 hover:shadow-lg active:scale-95 transition-all">
                        <i class="fas fa-plus" aria-hidden="true"></i> Ajukan Permohonan
                    </a>
                    <a href="{{ route('client.services.index') }}"
                       class="inline-flex items-center gap-2 bg-white/10 backdrop-blur border border-white/30 px-5 py-3 font-semibold hover:bg-white/20 active:scale-95 transition-all">
                        <i class="fas fa-layer-group" aria-hidden="true"></i> Jelajahi Layanan
                    </a>
                </div>
            </div>

            {{-- Stats Grid 4-col --}}
            <div class="grid grid-cols-4 gap-4" role="list" aria-label="Ringkasan statistik">
                <div class="bg-white/10 backdrop-blur border border-white/20 px-5 py-4" role="listitem">
                    <div class="flex items-center justify-between mb-2">
                        <p class="text-xs uppercase tracking-wider text-white/70 leading-tight">Proyek Aktif</p>
                        <i class="fas fa-folder-open text-white/50" aria-hidden="true"></i>
                    </div>
                    <p class="text-3xl font-bold leading-tight">{{ $activeProjects }}</p>
                    <p class="text-xs text-white/60 mt-1">dari {{ $projects->count() }} total</p>
                </div>
                <div class="bg-white/10 backdrop-blur border border-white/20 px-5 py-4" role="listitem">
                    <div class="flex items-center justify-between mb-2">
                        <p class="text-xs uppercase tracking-wider text-white/70 leading-tight">Selesai</p>
                        <i class="fas fa-check-circle text-emerald-400" aria-hidden="true"></i>
                    </div>
                    <p class="text-3xl font-bold leading-tight">{{ $completedProjects }}</p>
                    <p class="text-xs text-white/60 mt-1">{{ $projects->count() > 0 ? round(($completedProjects / $projects->count()) * 100) : 0 }}% tingkat selesai</p>
                </div>
                <div class="bg-white/10 backdrop-blur border border-white/20 px-5 py-4" role="listitem">
                    <div class="flex items-center justify-between mb-2">
                        <p class="text-xs uppercase tracking-wider text-white/70 leading-tight">Deadline 7 Hari</p>
                        <i class="fas fa-clock text-amber-400" aria-hidden="true"></i>
                    </div>
                    <p class="text-3xl font-bold leading-tight">{{ $upcomingDeadlines->count() }}</p>
                    <p class="text-xs text-white/60 mt-1">{{ $pendingDocuments > 0 ? $pendingDocuments . ' dokumen pending' : 'Semua terkendali' }}</p>
                </div>
                <div class="bg-white/10 backdrop-blur border border-white/20 px-5 py-4" role="listitem">
                    <div class="flex items-center justify-between mb-2">
                        <p class="text-xs uppercase tracking-wider text-white/70 leading-tight">Total Investasi</p>
                        <i class="fas fa-wallet text-white/50" aria-hidden="true"></i>
                    </div>
                    <p class="text-2xl font-bold leading-tight">{{ $investDisplay }}</p>
                    <p class="text-xs text-white/60 mt-1">Nilai kontrak</p>
                </div>
            </div>

            {{-- Desktop Progress Bar --}}
            @if($totalDocuments > 0)
            <div class="mt-4 bg-white/10 backdrop-blur border border-white/20 px-5 py-3 flex items-center gap-4" role="progressbar" aria-valuenow="{{ $documentCompletion }}" aria-valuemin="0" aria-valuemax="100" aria-label="Progres kelengkapan dokumen">
                <span class="text-sm text-white/80 flex items-center gap-2 whitespace-nowrap">
                    <i class="fas fa-file-alt text-xs" aria-hidden="true"></i> Kelengkapan Dokumen
                </span>
                <div class="flex-1 h-2 bg-white/20 rounded-full overflow-hidden">
                    <div class="h-full rounded-full progress-bar-fill {{ $documentCompletion >= 100 ? 'bg-emerald-400' : 'bg-white' }}" style="width: {{ $documentCompletion }}%"></div>
                </div>
                <span class="text-sm font-semibold whitespace-nowrap">{{ $uploadedDocuments }}/{{ $totalDocuments }} ({{ $documentCompletion }}%)</span>
            </div>
            @endif
        </div>
    </div>

