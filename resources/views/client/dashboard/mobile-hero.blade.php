    {{-- ============================================================ --}}
    {{-- MOBILE HERO HEADER                                           --}}
    {{-- ============================================================ --}}
    <div class="lg:hidden bg-[#0a66c2] text-white px-4 py-5 border-y border-[#0a66c2]" role="banner">
        {{-- Greeting + Notification bell --}}
        <div class="flex items-center justify-between mb-4">
            <div class="min-w-0 flex-1">
                <p class="text-xs text-white/70 leading-tight">Selamat datang kembali</p>
                <h1 class="text-lg font-bold leading-tight truncate mt-0.5">{{ $client->name }}</h1>
            </div>
            @if($upcomingDeadlines->count() > 0)
            <a href="#deadlines-section"
               class="flex items-center gap-1.5 bg-white/20 backdrop-blur px-3 py-2 rounded-full min-h-[44px] active:scale-95 transition-transform flex-shrink-0 ml-3"
               aria-label="{{ $upcomingDeadlines->count() }} deadline mendesak">
                <i class="fas fa-bell text-sm"></i>
                <span class="text-xs font-semibold">{{ $upcomingDeadlines->count() }}</span>
            </a>
            @endif
        </div>

        {{-- Stats 2×2 Grid --}}
        <div class="grid grid-cols-2 gap-2" role="list" aria-label="Ringkasan statistik">
            <div class="bg-white/10 backdrop-blur border border-white/20 px-3 py-2.5" role="listitem">
                <div class="flex items-center justify-between mb-0.5">
                    <p class="text-xs text-white/70 leading-tight">Proyek Aktif</p>
                    <i class="fas fa-folder-open text-white/40 text-xs" aria-hidden="true"></i>
                </div>
                <p class="text-2xl font-bold leading-tight">{{ $activeProjects }}</p>
            </div>
            <div class="bg-white/10 backdrop-blur border border-white/20 px-3 py-2.5" role="listitem">
                <div class="flex items-center justify-between mb-0.5">
                    <p class="text-xs text-white/70 leading-tight">Selesai</p>
                    <i class="fas fa-check-circle text-emerald-400 text-xs" aria-hidden="true"></i>
                </div>
                <p class="text-2xl font-bold leading-tight">{{ $completedProjects }}</p>
            </div>
            <div class="bg-white/10 backdrop-blur border border-white/20 px-3 py-2.5" role="listitem">
                <div class="flex items-center justify-between mb-0.5">
                    <p class="text-xs text-white/70 leading-tight">Deadline 7 Hari</p>
                    <i class="fas fa-clock text-amber-400 text-xs" aria-hidden="true"></i>
                </div>
                <p class="text-2xl font-bold leading-tight">{{ $upcomingDeadlines->count() }}</p>
            </div>
            <div class="bg-white/10 backdrop-blur border border-white/20 px-3 py-2.5" role="listitem">
                <div class="flex items-center justify-between mb-0.5">
                    <p class="text-xs text-white/70 leading-tight">Total Investasi</p>
                    <i class="fas fa-wallet text-white/40 text-xs" aria-hidden="true"></i>
                </div>
                <p class="text-xl font-bold leading-tight">{{ $investDisplay }}</p>
            </div>
        </div>

        {{-- Document Progress Bar --}}
        @if($totalDocuments > 0)
        <div class="mt-3 bg-white/10 backdrop-blur border border-white/20 px-3 py-2.5" role="progressbar" aria-valuenow="{{ $documentCompletion }}" aria-valuemin="0" aria-valuemax="100" aria-label="Progres kelengkapan dokumen">
            <div class="flex items-center justify-between mb-1.5">
                <span class="text-xs text-white/70 flex items-center gap-1.5">
                    <i class="fas fa-file-alt text-xs" aria-hidden="true"></i> Kelengkapan Dokumen
                </span>
                <span class="text-xs font-semibold">{{ $uploadedDocuments }}/{{ $totalDocuments }}</span>
            </div>
            <div class="w-full h-1.5 bg-white/20 rounded-full overflow-hidden">
                <div class="h-full rounded-full progress-bar-fill {{ $documentCompletion >= 100 ? 'bg-emerald-400' : 'bg-white' }}" style="width: {{ $documentCompletion }}%"></div>
            </div>
        </div>
        @endif

        {{-- Quick Actions --}}
        <div class="mt-3 grid grid-cols-3 gap-2">
            <a href="{{ route('client.applications.create') }}"
               class="flex flex-col items-center gap-1 px-2 py-2.5 bg-white/10 backdrop-blur border border-white/20 rounded-lg min-h-[44px] active:scale-95 transition-transform"
               aria-label="Ajukan permohonan baru">
                <i class="fas fa-plus text-base" aria-hidden="true"></i>
                <span class="text-[11px] font-medium leading-tight">Ajukan</span>
            </a>
            <a href="{{ route('client.documents.index') }}"
               class="flex flex-col items-center gap-1 px-2 py-2.5 bg-white/10 backdrop-blur border border-white/20 rounded-lg min-h-[44px] active:scale-95 transition-transform"
               aria-label="Kelola dokumen">
                <i class="fas fa-folder text-base" aria-hidden="true"></i>
                <span class="text-[11px] font-medium leading-tight">Dokumen</span>
            </a>
            <a href="{{ route('client.projects.index') }}"
               class="flex flex-col items-center gap-1 px-2 py-2.5 bg-white/10 backdrop-blur border border-white/20 rounded-lg min-h-[44px] active:scale-95 transition-transform"
               aria-label="Lihat semua proyek">
                <i class="fas fa-briefcase text-base" aria-hidden="true"></i>
                <span class="text-[11px] font-medium leading-tight">Proyek</span>
            </a>
        </div>
    </div>

