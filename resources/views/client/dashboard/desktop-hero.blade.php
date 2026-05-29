    {{-- ============================================================ --}}
    {{-- DESKTOP HERO HEADER                                          --}}
    {{-- ============================================================ --}}
    <div class="hidden lg:block bg-[#0a66c2] text-white" role="banner">
        <div class="max-w-6xl mx-auto px-6 lg:px-8 py-6">

            {{-- Single greeting row + single primary CTA --}}
            <div class="flex items-center justify-between gap-6 mb-5">
                <div class="min-w-0">
                    <p class="text-xs text-white/60 uppercase tracking-widest mb-1 leading-none">Dashboard</p>
                    <h1 class="text-xl lg:text-2xl font-bold leading-tight truncate">
                        {{ $client->company_name ?? $client->name }}
                    </h1>
                </div>
                <a href="{{ route('client.applications.create') }}"
                   class="inline-flex items-center gap-2 bg-white text-[#0a66c2] font-semibold text-sm px-5 py-2.5 rounded-lg hover:bg-blue-50 active:scale-95 transition-all shadow-sm whitespace-nowrap flex-shrink-0">
                    <i class="fas fa-plus text-xs" aria-hidden="true"></i> Ajukan Permohonan
                </a>
            </div>

            {{-- Stats Grid 4-col --}}
            <div class="grid grid-cols-4 gap-3" role="list" aria-label="Ringkasan statistik">
                <div class="bg-white/10 backdrop-blur border border-white/20 px-5 py-4 rounded-lg" role="listitem">
                    <div class="flex items-center justify-between mb-2">
                        <p class="text-[11px] uppercase tracking-wider text-white/60 leading-tight">Proyek Aktif</p>
                        <i class="fas fa-folder-open text-white/40 text-sm" aria-hidden="true"></i>
                    </div>
                    <p class="text-3xl font-bold leading-none">{{ $activeProjects }}</p>
                    <p class="text-xs text-white/50 mt-1.5">dari {{ $projects->count() }} total proyek</p>
                </div>
                <div class="bg-white/10 backdrop-blur border border-white/20 px-5 py-4 rounded-lg" role="listitem">
                    <div class="flex items-center justify-between mb-2">
                        <p class="text-[11px] uppercase tracking-wider text-white/60 leading-tight">Selesai</p>
                        <i class="fas fa-check-circle text-emerald-400 text-sm" aria-hidden="true"></i>
                    </div>
                    <p class="text-3xl font-bold leading-none">{{ $completedProjects }}</p>
                    <p class="text-xs text-white/50 mt-1.5">{{ $projects->count() > 0 ? round(($completedProjects / $projects->count()) * 100) : 0 }}% tingkat keberhasilan</p>
                </div>
                <div class="bg-white/10 backdrop-blur border border-white/20 px-5 py-4 rounded-lg" role="listitem">
                    <div class="flex items-center justify-between mb-2">
                        <p class="text-[11px] uppercase tracking-wider text-white/60 leading-tight">Deadline 7 Hari</p>
                        <i class="fas fa-clock text-amber-400 text-sm" aria-hidden="true"></i>
                    </div>
                    <p class="text-3xl font-bold leading-none">{{ $upcomingDeadlines->count() }}</p>
                    <p class="text-xs mt-1.5 {{ $pendingDocuments > 0 ? 'text-amber-300 font-medium' : 'text-white/50' }}">
                        {{ $pendingDocuments > 0 ? $pendingDocuments . ' dok. belum diupload' : 'Semua terkendali' }}
                    </p>
                </div>
                <div class="bg-white/10 backdrop-blur border border-white/20 px-5 py-4 rounded-lg" role="listitem">
                    <div class="flex items-center justify-between mb-2">
                        <p class="text-[11px] uppercase tracking-wider text-white/60 leading-tight">Total Investasi</p>
                        <i class="fas fa-wallet text-white/40 text-sm" aria-hidden="true"></i>
                    </div>
                    <p class="text-2xl font-bold leading-none">{{ $investDisplay }}</p>
                    <p class="text-xs text-white/50 mt-1.5">{{ $totalInvested > 0 ? 'Total nilai kontrak' : 'Belum ada kontrak' }}</p>
                </div>
            </div>

            {{-- Document Progress Bar (only when docs exist) --}}
            @if($totalDocuments > 0)
            <div class="mt-3 bg-white/10 backdrop-blur border border-white/20 px-5 py-3 rounded-lg flex items-center gap-4"
                 role="progressbar" aria-valuenow="{{ $documentCompletion }}" aria-valuemin="0" aria-valuemax="100" aria-label="Progres kelengkapan dokumen">
                <span class="text-xs text-white/70 flex items-center gap-2 whitespace-nowrap">
                    <i class="fas fa-file-alt" aria-hidden="true"></i> Kelengkapan Dokumen
                </span>
                <div class="flex-1 h-1.5 bg-white/20 rounded-full overflow-hidden">
                    <div class="h-full rounded-full progress-bar-fill {{ $documentCompletion >= 100 ? 'bg-emerald-400' : 'bg-white' }}" style="width: {{ $documentCompletion }}%"></div>
                </div>
                <span class="text-xs font-semibold whitespace-nowrap">{{ $uploadedDocuments }}/{{ $totalDocuments }} ({{ $documentCompletion }}%)</span>
            </div>
            @endif

        </div>
    </div>

