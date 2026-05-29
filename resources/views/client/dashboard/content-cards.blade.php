    {{-- ============================================================ --}}
    {{-- CONTENT CARDS                                                --}}
    {{-- ============================================================ --}}
    @php
        $step1Done = true; // Akun sudah aktif
        $step2Done = $projects->count() > 0;
        $step3Done = $uploadedDocuments > 0;
        $stepsCompleted = (int)$step1Done + (int)$step2Done + (int)$step3Done;
    @endphp

    <div class="space-y-0">

        {{-- ───────────────────────────────────────────────────── --}}
        {{-- 1. ONBOARDING PROGRESS STEPPER (hidden when all done) --}}
        {{-- ───────────────────────────────────────────────────── --}}
        @if($stepsCompleted < 3)
        <section class="bg-white dark:bg-gray-800 border-y border-gray-200 dark:border-gray-700" aria-label="Panduan memulai">
            <div class="px-4 lg:px-5 py-3 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                <div>
                    <h2 class="text-sm font-semibold text-gray-900 dark:text-white leading-tight">Panduan Memulai</h2>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Lengkapi langkah ini untuk memulai perizinan</p>
                </div>
                <span class="text-xs bg-[#0a66c2]/10 text-[#0a66c2] dark:bg-blue-900/30 dark:text-blue-400 px-2.5 py-1 rounded-full font-semibold tabular-nums">
                    {{ $stepsCompleted }}/3 selesai
                </span>
            </div>
            <div class="px-4 lg:px-6 py-5 lg:py-6">
                <div class="flex items-start">

                    {{-- ── Step 1: Profil ── --}}
                    <div class="flex-1 flex flex-col items-center text-center">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center text-sm font-bold transition-all
                            {{ $step1Done ? 'bg-[#0a66c2] text-white shadow' : 'bg-gray-200 dark:bg-gray-600 text-gray-500 dark:text-gray-300' }}">
                            @if($step1Done)
                                <i class="fas fa-check text-xs" aria-hidden="true"></i>
                            @else
                                1
                            @endif
                        </div>
                        <p class="text-xs lg:text-sm font-semibold text-gray-900 dark:text-white mt-2.5 leading-tight">Profil Perusahaan</p>
                        <p class="text-[11px] text-gray-500 dark:text-gray-400 mt-0.5 leading-snug hidden lg:block">Data usaha &amp; kontak PIC</p>
                        <a href="{{ route('client.profile.edit') }}"
                           class="mt-2 text-xs font-medium min-h-[28px] flex items-center gap-1 {{ $step1Done ? 'text-emerald-600 dark:text-emerald-400' : 'text-[#0a66c2] dark:text-blue-400 hover:underline' }}">
                            @if($step1Done)
                                <i class="fas fa-check-circle text-[10px]" aria-hidden="true"></i> Selesai
                            @else
                                Lengkapi <i class="fas fa-arrow-right text-[10px]" aria-hidden="true"></i>
                            @endif
                        </a>
                    </div>

                    {{-- Connector 1→2 --}}
                    <div class="flex-shrink-0 pt-5 mx-1.5 lg:mx-3">
                        <div class="w-6 lg:w-14 h-0.5 transition-colors {{ $step2Done ? 'bg-[#0a66c2]' : 'bg-gray-200 dark:bg-gray-600' }}"></div>
                    </div>

                    {{-- ── Step 2: Permohonan ── --}}
                    <div class="flex-1 flex flex-col items-center text-center">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center text-sm font-bold transition-all
                            {{ $step2Done
                                ? 'bg-[#0a66c2] text-white shadow'
                                : ($step1Done
                                    ? 'bg-[#0a66c2]/15 text-[#0a66c2] dark:bg-blue-900/30 dark:text-blue-300 ring-2 ring-[#0a66c2]/40 ring-offset-1 dark:ring-offset-gray-800'
                                    : 'bg-gray-200 dark:bg-gray-600 text-gray-400 dark:text-gray-500') }}">
                            @if($step2Done)
                                <i class="fas fa-check text-xs" aria-hidden="true"></i>
                            @else
                                2
                            @endif
                        </div>
                        <p class="text-xs lg:text-sm font-semibold text-gray-900 dark:text-white mt-2.5 leading-tight">Ajukan Permohonan</p>
                        <p class="text-[11px] text-gray-500 dark:text-gray-400 mt-0.5 leading-snug hidden lg:block">Pilih layanan &amp; buat permohonan</p>
                        @if($step2Done)
                            <span class="mt-2 text-xs font-medium text-emerald-600 dark:text-emerald-400 flex items-center gap-1 min-h-[28px]">
                                <i class="fas fa-check-circle text-[10px]" aria-hidden="true"></i> Selesai
                            </span>
                        @elseif($step1Done)
                            <a href="{{ route('client.applications.create') }}"
                               class="mt-2 text-xs font-medium text-[#0a66c2] dark:text-blue-400 hover:underline flex items-center gap-1 min-h-[28px]">
                                Mulai <i class="fas fa-arrow-right text-[10px]" aria-hidden="true"></i>
                            </a>
                        @else
                            <span class="mt-2 text-xs text-gray-400 dark:text-gray-500 min-h-[28px] flex items-center">Menunggu</span>
                        @endif
                    </div>

                    {{-- Connector 2→3 --}}
                    <div class="flex-shrink-0 pt-5 mx-1.5 lg:mx-3">
                        <div class="w-6 lg:w-14 h-0.5 transition-colors {{ $step3Done ? 'bg-[#0a66c2]' : 'bg-gray-200 dark:bg-gray-600' }}"></div>
                    </div>

                    {{-- ── Step 3: Upload Dokumen ── --}}
                    <div class="flex-1 flex flex-col items-center text-center">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center text-sm font-bold transition-all
                            {{ $step3Done
                                ? 'bg-[#0a66c2] text-white shadow'
                                : ($step2Done
                                    ? 'bg-[#0a66c2]/15 text-[#0a66c2] dark:bg-blue-900/30 dark:text-blue-300 ring-2 ring-[#0a66c2]/40 ring-offset-1 dark:ring-offset-gray-800'
                                    : 'bg-gray-200 dark:bg-gray-600 text-gray-400 dark:text-gray-500') }}">
                            @if($step3Done)
                                <i class="fas fa-check text-xs" aria-hidden="true"></i>
                            @else
                                3
                            @endif
                        </div>
                        <p class="text-xs lg:text-sm font-semibold text-gray-900 dark:text-white mt-2.5 leading-tight">Upload Dokumen</p>
                        <p class="text-[11px] text-gray-500 dark:text-gray-400 mt-0.5 leading-snug hidden lg:block">Lengkapi dokumen persyaratan</p>
                        @if($step3Done)
                            <span class="mt-2 text-xs font-medium text-emerald-600 dark:text-emerald-400 flex items-center gap-1 min-h-[28px]">
                                <i class="fas fa-check-circle text-[10px]" aria-hidden="true"></i> Selesai
                            </span>
                        @elseif($step2Done)
                            <a href="{{ route('client.documents.index') }}"
                               class="mt-2 text-xs font-medium text-[#0a66c2] dark:text-blue-400 hover:underline flex items-center gap-1 min-h-[28px]">
                                Upload <i class="fas fa-arrow-right text-[10px]" aria-hidden="true"></i>
                            </a>
                        @else
                            <span class="mt-2 text-xs text-gray-400 dark:text-gray-500 min-h-[28px] flex items-center">Menunggu</span>
                        @endif
                    </div>

                </div>
            </div>
        </section>
        @endif

        {{-- ─────────────────────────────────────── --}}
        {{-- 2. QUICK ACTIONS — 2×2 descriptive grid --}}
        {{-- ─────────────────────────────────────── --}}
        <section class="bg-white dark:bg-gray-800 border-y border-gray-200 dark:border-gray-700" aria-label="Aksi cepat">
            <div class="px-4 lg:px-5 py-3 border-b border-gray-100 dark:border-gray-700">
                <h2 class="text-sm font-semibold text-gray-900 dark:text-white leading-tight">Aksi Cepat</h2>
            </div>
            <div class="grid grid-cols-2 divide-x divide-y divide-gray-100 dark:divide-gray-700">
                <a href="{{ route('client.applications.create') }}"
                   class="flex items-center gap-3 px-4 py-4 hover:bg-gray-50 dark:hover:bg-gray-700/50 active:bg-gray-100 dark:active:bg-gray-700 transition-colors group">
                    <span class="w-10 h-10 rounded-xl bg-[#0a66c2]/10 flex items-center justify-center flex-shrink-0 group-hover:bg-[#0a66c2] transition-colors">
                        <i class="fas fa-plus text-[#0a66c2] group-hover:text-white text-sm transition-colors" aria-hidden="true"></i>
                    </span>
                    <div class="min-w-0">
                        <p class="text-sm font-semibold text-gray-900 dark:text-white leading-tight">Ajukan Permohonan</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5 leading-tight hidden sm:block">Mulai permohonan izin baru</p>
                    </div>
                </a>
                <a href="{{ route('client.documents.index') }}"
                   class="flex items-center gap-3 px-4 py-4 hover:bg-gray-50 dark:hover:bg-gray-700/50 active:bg-gray-100 dark:active:bg-gray-700 transition-colors group">
                    <span class="w-10 h-10 rounded-xl bg-amber-50 flex items-center justify-center flex-shrink-0 group-hover:bg-amber-500 transition-colors">
                        <i class="fas fa-upload text-amber-600 group-hover:text-white text-sm transition-colors" aria-hidden="true"></i>
                    </span>
                    <div class="min-w-0">
                        <p class="text-sm font-semibold text-gray-900 dark:text-white leading-tight">Upload Dokumen</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5 leading-tight hidden sm:block">Tambah &amp; kelola dokumen izin</p>
                    </div>
                </a>
                <a href="{{ route('client.applications.index') }}"
                   class="flex items-center gap-3 px-4 py-4 hover:bg-gray-50 dark:hover:bg-gray-700/50 active:bg-gray-100 dark:active:bg-gray-700 transition-colors group">
                    <span class="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center flex-shrink-0 group-hover:bg-emerald-500 transition-colors">
                        <i class="fas fa-search text-emerald-600 group-hover:text-white text-sm transition-colors" aria-hidden="true"></i>
                    </span>
                    <div class="min-w-0">
                        <p class="text-sm font-semibold text-gray-900 dark:text-white leading-tight">Cek Status Izin</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5 leading-tight hidden sm:block">Pantau progres permohonan Anda</p>
                    </div>
                </a>
                <a href="{{ route('client.services.index') }}"
                   class="flex items-center gap-3 px-4 py-4 hover:bg-gray-50 dark:hover:bg-gray-700/50 active:bg-gray-100 dark:active:bg-gray-700 transition-colors group">
                    <span class="w-10 h-10 rounded-xl bg-purple-50 flex items-center justify-center flex-shrink-0 group-hover:bg-purple-500 transition-colors">
                        <i class="fas fa-layer-group text-purple-600 group-hover:text-white text-sm transition-colors" aria-hidden="true"></i>
                    </span>
                    <div class="min-w-0">
                        <p class="text-sm font-semibold text-gray-900 dark:text-white leading-tight">Katalog Layanan</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5 leading-tight hidden sm:block">Jelajahi semua jenis izin usaha</p>
                    </div>
                </a>
            </div>
        </section>

        {{-- ───────────────────────────────── --}}
        {{-- 3. PROJECT OVERVIEW               --}}
        {{-- ───────────────────────────────── --}}
        <section class="bg-white dark:bg-gray-800 border-y border-gray-200 dark:border-gray-700" aria-label="Ringkasan proyek">
            <div class="px-4 lg:px-5 py-3 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                <div>
                    <h2 class="text-base font-semibold text-gray-900 dark:text-white leading-tight">Ringkasan Proyek</h2>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                        @if($projects->count() > 0)
                            {{ min($projects->count(), 4) }} proyek terbaru · {{ $activeProjects }} aktif
                        @else
                            Belum ada proyek aktif
                        @endif
                    </p>
                </div>
                @if($projects->count() > 0)
                <a href="{{ route('client.projects.index') }}"
                   class="text-sm font-medium text-[#0a66c2] hover:text-[#004182] dark:text-blue-400 dark:hover:text-blue-300 px-3 py-2 min-h-[44px] flex items-center active:scale-95 transition-transform">
                    Lihat Semua <i class="fas fa-arrow-right ml-2 text-xs" aria-hidden="true"></i>
                </a>
                @endif
            </div>
            <div>
                @forelse($projects->take(4) as $project)
                <a href="{{ route('client.projects.show', $project->id) }}"
                   class="flex items-center gap-3 px-4 lg:px-5 py-4 border-b border-gray-100 dark:border-gray-700 last:border-b-0 hover:bg-gray-50 dark:hover:bg-gray-700/50 active:bg-gray-100 dark:active:bg-gray-700 transition-colors group">
                    <span class="w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0
                        {{ ($project->status && $project->status->name === 'Selesai') ? 'bg-green-100 text-green-600 dark:bg-green-900/30 dark:text-green-400' : 'bg-blue-50 text-[#0a66c2] dark:bg-blue-900/30 dark:text-blue-400' }}">
                        <i class="fas {{ ($project->status && $project->status->name === 'Selesai') ? 'fa-check-circle' : 'fa-briefcase' }} text-sm" aria-hidden="true"></i>
                    </span>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-gray-900 dark:text-white leading-tight truncate group-hover:text-[#0a66c2] dark:group-hover:text-blue-400 transition-colors">{{ $project->name }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5 leading-normal truncate">{{ $project->permitApplication->permitType->name ?? 'Jenis izin belum ditetapkan' }}</p>
                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">{{ optional($project->updated_at)->diffForHumans() }}</p>
                    </div>
                    @php
                        $pStatusName = $project->status->name ?? '';
                        $pStatusKey  = $statusMap[$pStatusName]['status'] ?? 'draft';
                    @endphp
                    @include('client.components.status-badge', [
                        'status' => $pStatusKey,
                        'label'  => $pStatusName ?: 'Belum ada status',
                        'size'   => 'xs',
                    ])
                    <i class="fas fa-chevron-right text-gray-300 dark:text-gray-600 text-xs hidden sm:block flex-shrink-0" aria-hidden="true"></i>
                </a>
                @empty
                <div class="px-4 lg:px-5 py-8 text-center">
                    <i class="fas fa-folder-open text-2xl text-gray-300 dark:text-gray-600 mb-3 block" aria-hidden="true"></i>
                    <p class="text-sm text-gray-600 dark:text-gray-400 font-medium mb-0.5">Belum ada proyek</p>
                    <p class="text-xs text-gray-400 dark:text-gray-500">Ajukan permohonan izin untuk memulai proyek pertama</p>
                </div>
                @endforelse
            </div>
        </section>

        {{-- ─────────────────────────────────────────────── --}}
        {{-- 4. FITUR LANJUTAN: OSS / Compliance / Vault    --}}
        {{-- ─────────────────────────────────────────────── --}}
        <section class="bg-white dark:bg-gray-800 border-y border-gray-200 dark:border-gray-700" aria-label="Fitur lanjutan">
            <div class="px-4 lg:px-5 py-3 border-b border-gray-100 dark:border-gray-700">
                <h2 class="text-sm font-semibold text-gray-900 dark:text-white leading-tight">Fitur Lanjutan</h2>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Monitor izin, kepatuhan, dan arsip dokumen Anda</p>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-3 divide-y sm:divide-y-0 sm:divide-x divide-gray-100 dark:divide-gray-700">
                <a href="{{ route('client.oss-tracker.index') }}"
                   class="flex items-center gap-3 px-4 lg:px-5 py-4 hover:bg-gray-50 dark:hover:bg-gray-700/50 active:bg-gray-100 dark:active:bg-gray-700 transition-colors group">
                    <span class="w-10 h-10 rounded-lg bg-blue-50 dark:bg-blue-900/20 flex items-center justify-center flex-shrink-0 group-hover:bg-[#0a66c2] transition-colors">
                        <i class="fas fa-building text-[#0a66c2] dark:text-blue-400 text-sm group-hover:text-white transition-colors" aria-hidden="true"></i>
                    </span>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-gray-900 dark:text-white leading-tight group-hover:text-[#0a66c2] dark:group-hover:text-blue-400 transition-colors">OSS Tracker</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Status izin OSS real-time</p>
                    </div>
                    <i class="fas fa-chevron-right text-gray-300 dark:text-gray-600 text-xs flex-shrink-0 group-hover:text-[#0a66c2] transition-colors" aria-hidden="true"></i>
                </a>
                <a href="{{ route('client.compliance.index') }}"
                   class="flex items-center gap-3 px-4 lg:px-5 py-4 hover:bg-gray-50 dark:hover:bg-gray-700/50 active:bg-gray-100 dark:active:bg-gray-700 transition-colors group">
                    <span class="w-10 h-10 rounded-lg bg-emerald-50 dark:bg-emerald-900/20 flex items-center justify-center flex-shrink-0 group-hover:bg-emerald-600 transition-colors">
                        <i class="fas fa-shield-halved text-emerald-600 dark:text-emerald-400 text-sm group-hover:text-white transition-colors" aria-hidden="true"></i>
                    </span>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-gray-900 dark:text-white leading-tight group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors">Compliance Monitor</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Pantau kepatuhan &amp; expiry izin</p>
                    </div>
                    <i class="fas fa-chevron-right text-gray-300 dark:text-gray-600 text-xs flex-shrink-0 group-hover:text-emerald-600 transition-colors" aria-hidden="true"></i>
                </a>
                <a href="{{ route('client.vault.index') }}"
                   class="flex items-center gap-3 px-4 lg:px-5 py-4 hover:bg-gray-50 dark:hover:bg-gray-700/50 active:bg-gray-100 dark:active:bg-gray-700 transition-colors group">
                    <span class="w-10 h-10 rounded-lg bg-amber-50 dark:bg-amber-900/20 flex items-center justify-center flex-shrink-0 group-hover:bg-amber-500 transition-colors">
                        <i class="fas fa-vault text-amber-600 dark:text-amber-400 text-sm group-hover:text-white transition-colors" aria-hidden="true"></i>
                    </span>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-gray-900 dark:text-white leading-tight group-hover:text-amber-600 dark:group-hover:text-amber-400 transition-colors">Document Vault</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Arsip &amp; kelola surat izin digital</p>
                    </div>
                    <i class="fas fa-chevron-right text-gray-300 dark:text-gray-600 text-xs flex-shrink-0 group-hover:text-amber-600 transition-colors" aria-hidden="true"></i>
                </a>
            </div>
        </section>

        {{-- ─────────────────────── --}}
        {{-- 5. DOKUMEN TERBARU      --}}
        {{-- ─────────────────────── --}}
        <section class="bg-white dark:bg-gray-800 border-y border-gray-200 dark:border-gray-700" aria-label="Dokumen terbaru">
            <div class="px-4 lg:px-5 py-3 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                <div>
                    <h2 class="text-base font-semibold text-gray-900 dark:text-white leading-tight">Dokumen Terbaru</h2>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ $recentDocuments->count() }} dokumen terbaru</p>
                </div>
                <a href="{{ route('client.documents.index') }}"
                   class="text-sm font-medium text-[#0a66c2] hover:text-[#004182] dark:text-blue-400 dark:hover:text-blue-300 px-3 py-2 min-h-[44px] flex items-center active:scale-95 transition-transform">
                    Kelola <i class="fas fa-arrow-right ml-2 text-xs" aria-hidden="true"></i>
                </a>
            </div>
            <div>
                @forelse($recentDocuments as $document)
                @php
                    $hasFile = !empty($document->file_path);
                    $ext = $hasFile ? strtolower(pathinfo($document->file_path, PATHINFO_EXTENSION)) : '';
                    $iconMap = [
                        'pdf'  => 'fa-file-pdf text-red-500',
                        'doc'  => 'fa-file-word text-blue-600',
                        'docx' => 'fa-file-word text-blue-600',
                        'xls'  => 'fa-file-excel text-green-600',
                        'xlsx' => 'fa-file-excel text-green-600',
                        'jpg'  => 'fa-file-image text-purple-500',
                        'jpeg' => 'fa-file-image text-purple-500',
                        'png'  => 'fa-file-image text-purple-500',
                    ];
                    $fileIcon = $iconMap[$ext] ?? 'fa-file-alt text-gray-500 dark:text-gray-400';
                @endphp
                @if($hasFile)
                <a href="{{ Storage::url($document->file_path) }}" target="_blank" rel="noopener noreferrer"
                   class="flex items-center gap-3 px-4 lg:px-5 py-3.5 border-b border-gray-100 dark:border-gray-700 last:border-b-0 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                @else
                <div class="flex items-center gap-3 px-4 lg:px-5 py-3.5 border-b border-gray-100 dark:border-gray-700 last:border-b-0">
                @endif
                    <span class="w-10 h-10 rounded-lg bg-gray-100 dark:bg-gray-700 flex items-center justify-center flex-shrink-0">
                        <i class="fas {{ $fileIcon }}" aria-hidden="true"></i>
                    </span>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-gray-900 dark:text-white leading-tight truncate">{{ $document->title ?? $document->file_name ?? 'Dokumen tanpa judul' }}</p>
                        <div class="flex items-center gap-2 mt-0.5">
                            @if($document->category)
                            <span class="text-xs text-gray-500 dark:text-gray-400 capitalize">{{ $document->category }}</span>
                            <span class="text-gray-300 dark:text-gray-600">&middot;</span>
                            @endif
                            <span class="text-xs text-gray-400 dark:text-gray-500">{{ optional($document->created_at)->diffForHumans() }}</span>
                        </div>
                    </div>
                    @if($hasFile)
                    <i class="fas fa-download text-[#0a66c2] dark:text-blue-400 text-sm flex-shrink-0" aria-hidden="true"></i>
                    @else
                    <span class="text-xs text-amber-600 dark:text-amber-400 font-medium flex-shrink-0">
                        <i class="fas fa-exclamation-circle mr-1" aria-hidden="true"></i>Belum diupload
                    </span>
                    @endif
                @if($hasFile)
                </a>
                @else
                </div>
                @endif
                @empty
                <div class="px-4 lg:px-5 py-8 text-center">
                    <i class="fas fa-file text-2xl text-gray-300 dark:text-gray-600 mb-3 block" aria-hidden="true"></i>
                    <p class="text-sm text-gray-600 dark:text-gray-400 font-medium mb-0.5">Belum ada dokumen</p>
                    <p class="text-xs text-gray-400 dark:text-gray-500">Dokumen akan muncul saat proyek Anda berjalan</p>
                </div>
                @endforelse
            </div>
        </section>

        {{-- ─────────────────────────── --}}
        {{-- 6. DEADLINE TIMELINE        --}}
        {{-- ─────────────────────────── --}}
        <section id="deadlines-section" class="bg-white dark:bg-gray-800 border-y border-gray-200 dark:border-gray-700" aria-label="Timeline deadline">
            <div class="px-4 lg:px-5 py-3 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                <div>
                    <h2 class="text-base font-semibold text-gray-900 dark:text-white leading-tight">
                        Deadline Mendatang
                        @if($upcomingDeadlines->count() > 0)
                        <span class="inline-flex items-center justify-center ml-2 w-5 h-5 text-xs font-bold bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400 rounded-full align-middle">{{ $upcomingDeadlines->count() }}</span>
                        @endif
                    </h2>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Task dalam 7 hari ke depan</p>
                </div>
                @if($projects->count() > 0)
                <a href="{{ route('client.projects.index') }}"
                   class="text-sm font-medium text-[#0a66c2] hover:text-[#004182] dark:text-blue-400 dark:hover:text-blue-300 px-3 py-2 min-h-[44px] flex items-center active:scale-95 transition-transform">
                    Lihat Proyek <i class="fas fa-arrow-right ml-2 text-xs" aria-hidden="true"></i>
                </a>
                @endif
            </div>
            <div>
                @forelse($upcomingDeadlines as $task)
                @php
                    $daysLeft = optional($task->due_date)->diffInDays(now());
                    $isUrgent = $daysLeft !== null && $daysLeft <= 2;
                @endphp
                <div class="flex items-start gap-3 px-4 lg:px-5 py-4 border-b border-gray-100 dark:border-gray-700 last:border-b-0 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                    <span class="flex-shrink-0 inline-flex items-center justify-center w-9 h-9 rounded-full text-sm font-bold
                        {{ $isUrgent ? 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400' : 'bg-amber-50 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400' }}"
                        aria-label="{{ $isUrgent ? 'Mendesak' : 'Segera' }}">
                        {{ $loop->iteration }}
                    </span>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-gray-900 dark:text-white leading-tight">{{ $task->title }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5 leading-normal truncate">{{ $task->project->name ?? 'Tanpa nama proyek' }}</p>
                        <div class="mt-1.5 flex flex-wrap items-center gap-2">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                                {{ $isUrgent ? 'bg-red-50 dark:bg-red-900/30 text-red-700 dark:text-red-400' : 'bg-amber-50 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400' }}">
                                <i class="fas fa-clock mr-1 text-[10px]" aria-hidden="true"></i>
                                {{ optional($task->due_date)->translatedFormat('d M Y') }}
                            </span>
                            @if($isUrgent)
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-50 dark:bg-red-900/30 text-red-700 dark:text-red-400">
                                <i class="fas fa-exclamation-triangle mr-1 text-[10px]" aria-hidden="true"></i>
                                {{ $daysLeft == 0 ? 'Hari ini!' : $daysLeft . ' hari lagi' }}
                            </span>
                            @endif
                            @if($task->assignedUser)
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400">
                                <i class="fas fa-user mr-1 text-[10px]" aria-hidden="true"></i>
                                {{ $task->assignedUser->name }}
                            </span>
                            @endif
                        </div>
                    </div>
                    <div class="text-right text-xs text-gray-400 dark:text-gray-500 hidden lg:block flex-shrink-0 whitespace-nowrap">
                        {{ optional($task->due_date)->diffForHumans() }}
                    </div>
                </div>
                @empty
                <div class="px-4 lg:px-5 py-8 text-center">
                    <i class="fas fa-calendar-check text-2xl text-green-400 dark:text-green-500 mb-3 block" aria-hidden="true"></i>
                    <p class="text-sm text-gray-600 dark:text-gray-400 font-medium mb-0.5">Tidak ada deadline mendesak</p>
                    <p class="text-xs text-gray-400 dark:text-gray-500">Semua task dalam 7 hari ke depan terkendali</p>
                </div>
                @endforelse
            </div>
        </section>

        {{-- ─────────────────────────────── --}}
        {{-- 7. PENDING DOCUMENTS ALERT      --}}
        {{-- ─────────────────────────────── --}}
        @if($pendingDocuments > 0)
        <section class="bg-white dark:bg-gray-800 border-y border-gray-200 dark:border-gray-700" aria-label="Peringatan dokumen pending">
            <a href="{{ route('client.documents.index') }}"
               class="flex items-center gap-4 px-4 lg:px-5 py-4 hover:bg-amber-50/50 dark:hover:bg-amber-900/10 transition-colors group">
                <span class="w-10 h-10 rounded-full bg-amber-100 dark:bg-amber-900/30 flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-exclamation-triangle text-amber-600 dark:text-amber-400" aria-hidden="true"></i>
                </span>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-gray-900 dark:text-white leading-tight">{{ $pendingDocuments }} Dokumen Belum Diupload</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Upload dokumen yang diperlukan agar proses izin tidak tertunda</p>
                </div>
                <i class="fas fa-chevron-right text-gray-300 dark:text-gray-600 text-xs flex-shrink-0 group-hover:text-[#0a66c2] transition-colors" aria-hidden="true"></i>
            </a>
        </section>
        @endif

    </div>
</div>

{{-- Notification Prompt Component --}}
<div data-has-applications="{{ $projects->count() > 0 ? 'true' : 'false' }}">
    @include('client.components.notification-prompt')
</div>
                <a href="{{ route('client.documents.index') }}"
                   class="flex flex-col items-center justify-center gap-1.5 py-4 px-3 hover:bg-gray-50 dark:hover:bg-gray-700/50 active:scale-95 transition-all group min-h-[72px]">
                    <span class="w-9 h-9 rounded-full bg-amber-50 flex items-center justify-center group-hover:bg-amber-500 transition-colors">
                        <i class="fas fa-upload text-amber-600 group-hover:text-white text-sm transition-colors" aria-hidden="true"></i>
                    </span>
                    <span class="text-xs font-semibold text-gray-700 dark:text-gray-300 text-center leading-tight">Upload Dokumen</span>
                </a>
                <a href="{{ route('client.applications.index') }}"
                   class="flex flex-col items-center justify-center gap-1.5 py-4 px-3 hover:bg-gray-50 dark:hover:bg-gray-700/50 active:scale-95 transition-all group min-h-[72px]">
                    <span class="w-9 h-9 rounded-full bg-emerald-50 flex items-center justify-center group-hover:bg-emerald-500 transition-colors">
                        <i class="fas fa-search text-emerald-600 group-hover:text-white text-sm transition-colors" aria-hidden="true"></i>
                    </span>
                    <span class="text-xs font-semibold text-gray-700 dark:text-gray-300 text-center leading-tight">Cek Status Izin</span>
                </a>
                <a href="{{ route('client.services.index') }}"
                   class="flex flex-col items-center justify-center gap-1.5 py-4 px-3 hover:bg-gray-50 dark:hover:bg-gray-700/50 active:scale-95 transition-all group min-h-[72px]">
                    <span class="w-9 h-9 rounded-full bg-purple-50 flex items-center justify-center group-hover:bg-purple-500 transition-colors">
                        <i class="fas fa-layer-group text-purple-600 group-hover:text-white text-sm transition-colors" aria-hidden="true"></i>
                    </span>
                    <span class="text-xs font-semibold text-gray-700 dark:text-gray-300 text-center leading-tight">Katalog Layanan</span>
                </a>
            </div>
        </section>

        {{-- ───── PROJECT OVERVIEW ───── --}}
        <section class="bg-white dark:bg-gray-800 border-y border-gray-200 dark:border-gray-700" aria-label="Ringkasan proyek">
            <div class="p-4 lg:p-5 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                <div>
                    <h2 class="text-base lg:text-lg font-semibold text-gray-900 dark:text-white leading-tight">Ringkasan Proyek</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5 leading-normal">
                        @if($projects->count() > 0)
                            {{ min($projects->count(), 4) }} proyek terbaru
                        @else
                            Belum ada proyek
                        @endif
                    </p>
                </div>
                @if($projects->count() > 0)
                <a href="{{ route('client.projects.index') }}"
                   class="text-sm font-medium text-[#0a66c2] hover:text-[#004182] dark:text-blue-400 dark:hover:text-blue-300 px-3 py-2 min-h-[44px] flex items-center active:scale-95 transition-transform">
                    Lihat Semua <i class="fas fa-arrow-right ml-2 text-xs" aria-hidden="true"></i>
                </a>
                @endif
            </div>
            <div>
                @forelse($projects->take(4) as $project)
                <a href="{{ route('client.projects.show', $project->id) }}"
                   class="flex items-center gap-3 px-4 lg:px-5 py-4 border-b border-gray-100 dark:border-gray-700 last:border-b-0 hover:bg-gray-50 dark:hover:bg-gray-700/50 active:bg-gray-100 dark:active:bg-gray-700 transition-colors group">
                    {{-- Project icon --}}
                    <span class="w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0
                        {{ ($project->status && $project->status->name === 'Selesai') ? 'bg-green-100 text-green-600 dark:bg-green-900/30 dark:text-green-400' : 'bg-blue-50 text-[#0a66c2] dark:bg-blue-900/30 dark:text-blue-400' }}">
                        <i class="fas {{ ($project->status && $project->status->name === 'Selesai') ? 'fa-check-circle' : 'fa-briefcase' }} text-sm" aria-hidden="true"></i>
                    </span>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-gray-900 dark:text-white leading-tight truncate group-hover:text-[#0a66c2] dark:group-hover:text-blue-400 transition-colors">{{ $project->name }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 leading-normal truncate">{{ $project->permitApplication->permitType->name ?? 'Jenis izin belum ditetapkan' }}</p>
                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5 leading-tight">{{ optional($project->updated_at)->diffForHumans() }}</p>
                    </div>
                    @php
                        $pStatusName = $project->status->name ?? '';
                        $pStatusKey  = $statusMap[$pStatusName]['status'] ?? 'draft';
                    @endphp
                    @include('client.components.status-badge', [
                        'status' => $pStatusKey,
                        'label'  => $pStatusName ?: 'Belum ada status',
                        'size'   => 'xs',
                    ])
                    <i class="fas fa-chevron-right text-gray-300 dark:text-gray-600 text-xs hidden sm:block flex-shrink-0" aria-hidden="true"></i>
                </a>
                @empty
                {{-- ─── Onboarding Stepper (no projects yet) ─── --}}
                <div class="px-4 lg:px-5 py-8">
                    <p class="text-sm font-semibold text-gray-900 dark:text-white text-center mb-1">Mulai Proses Perizinan Anda</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 text-center mb-6 leading-normal">Ikuti 3 langkah berikut untuk memulai permohonan pertama</p>
                    <ol class="space-y-4">
                        <li class="flex items-center gap-3">
                            <span class="flex-shrink-0 w-8 h-8 rounded-full bg-[#0a66c2] text-white text-xs font-bold flex items-center justify-center shadow-sm">1</span>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold text-gray-800 dark:text-gray-200 leading-tight">Lengkapi Profil Perusahaan</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5 leading-normal">NIB, alamat, data usaha, dan kontak PIC</p>
                            </div>
                            <a href="{{ route('client.profile.edit') }}"
                               class="flex-shrink-0 text-xs font-semibold text-[#0a66c2] dark:text-blue-400 hover:underline whitespace-nowrap min-h-[36px] flex items-center px-1 active:scale-95 transition-transform">
                                Lengkapi <i class="fas fa-arrow-right ml-1 text-[10px]" aria-hidden="true"></i>
                            </a>
                        </li>
                        <li class="flex items-center gap-3">
                            <span class="flex-shrink-0 w-8 h-8 rounded-full bg-gray-200 dark:bg-gray-600 text-gray-500 dark:text-gray-300 text-xs font-bold flex items-center justify-center">2</span>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold text-gray-800 dark:text-gray-200 leading-tight">Pilih Jenis Layanan Izin</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5 leading-normal">OSS, SIUP, TDP, izin lingkungan, dan lainnya</p>
                            </div>
                            <a href="{{ route('client.services.index') }}"
                               class="flex-shrink-0 text-xs font-semibold text-gray-500 dark:text-gray-400 hover:text-[#0a66c2] dark:hover:text-blue-400 hover:underline whitespace-nowrap min-h-[36px] flex items-center px-1 active:scale-95 transition-all">
                                Pilih <i class="fas fa-arrow-right ml-1 text-[10px]" aria-hidden="true"></i>
                            </a>
                        </li>
                        <li class="flex items-center gap-3">
                            <span class="flex-shrink-0 w-8 h-8 rounded-full bg-gray-200 dark:bg-gray-600 text-gray-500 dark:text-gray-300 text-xs font-bold flex items-center justify-center">3</span>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold text-gray-800 dark:text-gray-200 leading-tight">Ajukan Permohonan</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5 leading-normal">Upload dokumen dan mulai proses perizinan</p>
                            </div>
                            <a href="{{ route('client.applications.create') }}"
                               class="flex-shrink-0 text-xs font-semibold text-gray-500 dark:text-gray-400 hover:text-[#0a66c2] dark:hover:text-blue-400 hover:underline whitespace-nowrap min-h-[36px] flex items-center px-1 active:scale-95 transition-all">
                                Mulai <i class="fas fa-arrow-right ml-1 text-[10px]" aria-hidden="true"></i>
                            </a>
                        </li>
                    </ol>
                    <div class="mt-6 pt-5 border-t border-gray-100 dark:border-gray-700 text-center">
                        <a href="{{ route('client.applications.create') }}"
                           class="inline-flex items-center gap-2 bg-[#0a66c2] text-white font-semibold text-sm px-6 py-2.5 rounded-lg hover:bg-[#004182] active:scale-95 transition-all shadow-sm">
                            <i class="fas fa-plus" aria-hidden="true"></i> Ajukan Permohonan Sekarang
                        </a>
                    </div>
                </div>
                @endforelse
            </div>
        </section>

        {{-- ───── FEATURE SHORTCUTS (OSS / Compliance / Vault) ───── --}}
        <section class="bg-white dark:bg-gray-800 border-y border-gray-200 dark:border-gray-700" aria-label="Fitur lanjutan">
            <div class="p-4 lg:p-5 border-b border-gray-100 dark:border-gray-700">
                <h2 class="text-base lg:text-lg font-semibold text-gray-900 dark:text-white leading-tight">Fitur Lanjutan</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5 leading-normal">Monitor izin, dokumen, dan kepatuhan usaha Anda</p>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-3 divide-y sm:divide-y-0 sm:divide-x divide-gray-100 dark:divide-gray-700">
                {{-- OSS Tracker --}}
                <a href="{{ route('client.oss-tracker.index') }}"
                   class="flex items-center gap-3 px-4 lg:px-5 py-4 hover:bg-gray-50 dark:hover:bg-gray-700/50 active:bg-gray-100 dark:active:bg-gray-700 transition-colors group">
                    <span class="w-10 h-10 rounded-lg bg-blue-50 dark:bg-blue-900/20 flex items-center justify-center flex-shrink-0 group-hover:bg-[#0a66c2] transition-colors">
                        <i class="fas fa-building text-[#0a66c2] dark:text-blue-400 text-sm group-hover:text-white transition-colors" aria-hidden="true"></i>
                    </span>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-gray-900 dark:text-white leading-tight group-hover:text-[#0a66c2] dark:group-hover:text-blue-400 transition-colors">OSS Tracker</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5 leading-tight">Status izin OSS real-time</p>
                    </div>
                    <i class="fas fa-chevron-right text-gray-300 dark:text-gray-600 text-xs flex-shrink-0 group-hover:text-[#0a66c2] transition-colors" aria-hidden="true"></i>
                </a>
                {{-- Compliance Monitor --}}
                <a href="{{ route('client.compliance.index') }}"
                   class="flex items-center gap-3 px-4 lg:px-5 py-4 hover:bg-gray-50 dark:hover:bg-gray-700/50 active:bg-gray-100 dark:active:bg-gray-700 transition-colors group">
                    <span class="w-10 h-10 rounded-lg bg-emerald-50 dark:bg-emerald-900/20 flex items-center justify-center flex-shrink-0 group-hover:bg-emerald-600 transition-colors">
                        <i class="fas fa-shield-halved text-emerald-600 dark:text-emerald-400 text-sm group-hover:text-white transition-colors" aria-hidden="true"></i>
                    </span>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-gray-900 dark:text-white leading-tight group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors">Compliance Monitor</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5 leading-tight">Pantau kepatuhan izin usaha</p>
                    </div>
                    <i class="fas fa-chevron-right text-gray-300 dark:text-gray-600 text-xs flex-shrink-0 group-hover:text-emerald-600 transition-colors" aria-hidden="true"></i>
                </a>
                {{-- Document Vault --}}
                <a href="{{ route('client.vault.index') }}"
                   class="flex items-center gap-3 px-4 lg:px-5 py-4 hover:bg-gray-50 dark:hover:bg-gray-700/50 active:bg-gray-100 dark:active:bg-gray-700 transition-colors group">
                    <span class="w-10 h-10 rounded-lg bg-amber-50 dark:bg-amber-900/20 flex items-center justify-center flex-shrink-0 group-hover:bg-amber-500 transition-colors">
                        <i class="fas fa-vault text-amber-600 dark:text-amber-400 text-sm group-hover:text-white transition-colors" aria-hidden="true"></i>
                    </span>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-gray-900 dark:text-white leading-tight group-hover:text-amber-600 dark:group-hover:text-amber-400 transition-colors">Document Vault</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5 leading-tight">Arsip & kelola surat izin</p>
                    </div>
                    <i class="fas fa-chevron-right text-gray-300 dark:text-gray-600 text-xs flex-shrink-0 group-hover:text-amber-600 transition-colors" aria-hidden="true"></i>
                </a>
            </div>
        </section>

        {{-- ───── RECENT DOCUMENTS ───── --}}
        <section class="bg-white dark:bg-gray-800 border-y border-gray-200 dark:border-gray-700" aria-label="Dokumen terbaru">
            <div class="p-4 lg:p-5 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                <div>
                    <h2 class="text-base lg:text-lg font-semibold text-gray-900 dark:text-white leading-tight">Dokumen Terbaru</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5 leading-normal">
                        {{ $recentDocuments->count() }} dokumen terbaru
                    </p>
                </div>
                <a href="{{ route('client.documents.index') }}"
                   class="text-sm font-medium text-[#0a66c2] hover:text-[#004182] dark:text-blue-400 dark:hover:text-blue-300 px-3 py-2 min-h-[44px] flex items-center active:scale-95 transition-transform">
                    Kelola <i class="fas fa-arrow-right ml-2 text-xs" aria-hidden="true"></i>
                </a>
            </div>
            <div>
                @forelse($recentDocuments as $document)
                @php
                    $hasFile = !empty($document->file_path);
                    $ext = $hasFile ? strtolower(pathinfo($document->file_path, PATHINFO_EXTENSION)) : '';
                    $iconMap = [
                        'pdf' => 'fa-file-pdf text-red-500',
                        'doc' => 'fa-file-word text-blue-600',
                        'docx' => 'fa-file-word text-blue-600',
                        'xls' => 'fa-file-excel text-green-600',
                        'xlsx' => 'fa-file-excel text-green-600',
                        'jpg' => 'fa-file-image text-purple-500',
                        'jpeg' => 'fa-file-image text-purple-500',
                        'png' => 'fa-file-image text-purple-500',
                    ];
                    $fileIcon = $iconMap[$ext] ?? 'fa-file-alt text-gray-500 dark:text-gray-400';
                @endphp
                @if($hasFile)
                <a href="{{ Storage::url($document->file_path) }}" target="_blank" rel="noopener noreferrer"
                   class="flex items-center gap-3 px-4 lg:px-5 py-3.5 border-b border-gray-100 dark:border-gray-700 last:border-b-0 hover:bg-gray-50 dark:hover:bg-gray-700/50 active:bg-gray-100 dark:active:bg-gray-700 transition-colors">
                @else
                <div class="flex items-center gap-3 px-4 lg:px-5 py-3.5 border-b border-gray-100 dark:border-gray-700 last:border-b-0">
                @endif
                    <span class="w-10 h-10 rounded-lg bg-gray-100 dark:bg-gray-700 flex items-center justify-center flex-shrink-0">
                        <i class="fas {{ $fileIcon }}" aria-hidden="true"></i>
                    </span>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-gray-900 dark:text-white leading-tight truncate">{{ $document->title ?? $document->file_name ?? 'Dokumen tanpa judul' }}</p>
                        <div class="flex items-center gap-2 mt-1">
                            @if($document->category)
                            <span class="text-xs text-gray-500 dark:text-gray-400 capitalize">{{ $document->category }}</span>
                            <span class="text-gray-300 dark:text-gray-600">&middot;</span>
                            @endif
                            <span class="text-xs text-gray-400 dark:text-gray-500">{{ optional($document->created_at)->diffForHumans() }}</span>
                        </div>
                    </div>
                    @if($hasFile)
                    <i class="fas fa-download text-[#0a66c2] dark:text-blue-400 text-sm flex-shrink-0" aria-hidden="true"></i>
                    @else
                    <span class="text-xs text-amber-600 dark:text-amber-400 font-medium flex-shrink-0">
                        <i class="fas fa-exclamation-circle mr-1" aria-hidden="true"></i>Belum diupload
                    </span>
                    @endif
                @if($hasFile)
                </a>
                @else
                </div>
                @endif
                @empty
                <div class="px-4 lg:px-5 py-12 text-center">
                    <div class="w-16 h-16 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-file text-gray-400 dark:text-gray-500 text-2xl" aria-hidden="true"></i>
                    </div>
                    <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Belum ada dokumen</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 leading-normal">Dokumen akan muncul saat proyek Anda berjalan</p>
                </div>
                @endforelse
            </div>
        </section>

        {{-- ───── DEADLINE TIMELINE ───── --}}
        <section id="deadlines-section" class="bg-white dark:bg-gray-800 border-y border-gray-200 dark:border-gray-700" aria-label="Timeline deadline">
            <div class="p-4 lg:p-5 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                <div>
                    <h2 class="text-base lg:text-lg font-semibold text-gray-900 dark:text-white leading-tight">
                        Deadline Mendatang
                        @if($upcomingDeadlines->count() > 0)
                        <span class="inline-flex items-center justify-center ml-2 w-6 h-6 text-xs font-bold bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400 rounded-full align-middle">{{ $upcomingDeadlines->count() }}</span>
                        @endif
                    </h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5 leading-normal">Task dalam 7 hari ke depan</p>
                </div>
                @if($projects->count() > 0)
                <a href="{{ route('client.projects.index') }}"
                   class="text-sm font-medium text-[#0a66c2] hover:text-[#004182] dark:text-blue-400 dark:hover:text-blue-300 px-3 py-2 min-h-[44px] flex items-center active:scale-95 transition-transform">
                    Lihat Proyek <i class="fas fa-arrow-right ml-2 text-xs" aria-hidden="true"></i>
                </a>
                @endif
            </div>
            <div>
                @forelse($upcomingDeadlines as $task)
                @php
                    $daysLeft = optional($task->due_date)->diffInDays(now());
                    $isUrgent = $daysLeft !== null && $daysLeft <= 2;
                @endphp
                <div class="flex items-start gap-3 px-4 lg:px-5 py-4 border-b border-gray-100 dark:border-gray-700 last:border-b-0 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                    {{-- Priority indicator --}}
                    <span class="flex-shrink-0 inline-flex items-center justify-center w-9 h-9 rounded-full text-sm font-bold
                        {{ $isUrgent ? 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400' : 'bg-amber-50 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400' }}"
                        aria-label="{{ $isUrgent ? 'Mendesak' : 'Segera' }}">
                        {{ $loop->iteration }}
                    </span>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-gray-900 dark:text-white leading-tight">{{ $task->title }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 leading-normal truncate">{{ $task->project->name ?? 'Tanpa nama proyek' }}</p>
                        <div class="mt-2 flex flex-wrap items-center gap-2">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                                {{ $isUrgent ? 'bg-red-50 dark:bg-red-900/30 text-red-700 dark:text-red-400' : 'bg-amber-50 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400' }}">
                                <i class="fas fa-clock mr-1 text-[10px]" aria-hidden="true"></i>
                                {{ optional($task->due_date)->translatedFormat('d M Y') }}
                            </span>
                            @if($isUrgent)
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-50 dark:bg-red-900/30 text-red-700 dark:text-red-400">
                                <i class="fas fa-exclamation-triangle mr-1 text-[10px]" aria-hidden="true"></i>
                                {{ $daysLeft == 0 ? 'Hari ini!' : $daysLeft . ' hari lagi' }}
                            </span>
                            @endif
                            @if($task->assignedUser)
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400">
                                <i class="fas fa-user mr-1 text-[10px]" aria-hidden="true"></i>
                                {{ $task->assignedUser->name }}
                            </span>
                            @endif
                        </div>
                    </div>
                    <div class="text-right text-xs text-gray-400 dark:text-gray-500 hidden lg:block flex-shrink-0 whitespace-nowrap">
                        {{ optional($task->due_date)->diffForHumans() }}
                    </div>
                </div>
                @empty
                <div class="px-4 lg:px-5 py-12 text-center">
                    <div class="w-16 h-16 rounded-full bg-green-50 dark:bg-green-900/20 flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-calendar-check text-green-500 dark:text-green-400 text-2xl" aria-hidden="true"></i>
                    </div>
                    <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tidak ada deadline mendesak</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 leading-normal">Semua task dalam 7 hari ke depan sudah diselesaikan</p>
                </div>
                @endforelse
            </div>
        </section>

        {{-- ───── PENDING DOCUMENTS ALERT ───── --}}
        @if($pendingDocuments > 0)
        <section class="bg-white dark:bg-gray-800 border-y border-gray-200 dark:border-gray-700" aria-label="Peringatan dokumen pending">
            <a href="{{ route('client.documents.index') }}"
               class="flex items-center gap-4 px-4 lg:px-5 py-4 hover:bg-amber-50/50 dark:hover:bg-amber-900/10 transition-colors group">
                <span class="w-10 h-10 rounded-full bg-amber-100 dark:bg-amber-900/30 flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-exclamation-triangle text-amber-600 dark:text-amber-400" aria-hidden="true"></i>
                </span>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-gray-900 dark:text-white leading-tight">{{ $pendingDocuments }} Dokumen Belum Diupload</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5 leading-normal">Upload dokumen yang diperlukan agar proses izin tidak tertunda</p>
                </div>
                <i class="fas fa-chevron-right text-gray-300 dark:text-gray-600 text-xs flex-shrink-0 group-hover:text-[#0a66c2] transition-colors" aria-hidden="true"></i>
            </a>
        </section>
        @endif

    </div>
</div>

{{-- Notification Prompt Component --}}
<div data-has-applications="{{ $projects->count() > 0 ? 'true' : 'false' }}">
    @include('client.components.notification-prompt')
</div>

