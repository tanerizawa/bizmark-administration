{{-- Application Detail — Portal v2 (tabs: Ringkasan / Dokumen / Penawaran / Komunikasi / Riwayat) --}}
@php
    $formData = is_string($application->form_data) ? json_decode($application->form_data, true) : $application->form_data;
    $formData = $formData ?? [];
    $isPackage = ($formData['package_type'] ?? null) === 'multi_permit';

    $totalDocuments = $application->documents->count();
    $requiredDocs = $application->permitType && $application->permitType->required_documents
        ? count($application->permitType->required_documents)
        : 0;
    $documentProgress = $requiredDocs > 0 ? min(100, round(($totalDocuments / $requiredDocs) * 100)) : 100;
    $daysAgo = (int) $application->created_at->diffInDays(now());

    $permitName = $application->permitType
        ? $application->permitType->name
        : ($isPackage ? ($formData['project_name'] ?? 'Paket Perizinan') : ($formData['permit_name'] ?? 'Permohonan Izin'));

    // Build timeline items from status logs (chronological, oldest first)
    $statusLogs = $application->statusLogs->sortBy('created_at')->values();
    $timelineItems = [];
    $timelineItems[] = [
        'title' => 'Permohonan dibuat',
        'subtitle' => $application->created_at->format('d M Y, H:i'),
        'status' => 'completed',
        'icon' => 'fa-plus-circle',
    ];
    foreach ($statusLogs as $log) {
        $timelineItems[] = [
            'title' => 'Status: ' . ucfirst(str_replace('_', ' ', $log->to_status)),
            'subtitle' => ($log->notes ?: 'Diperbarui') . ' · ' . $log->created_at->format('d M Y, H:i'),
            'status' => 'completed',
            'icon' => 'fa-circle-check',
        ];
    }
    if (! in_array($application->status, ['completed', 'cancelled'])) {
        $timelineItems[] = [
            'title' => 'Sedang berjalan',
            'subtitle' => 'Status saat ini: ' . ucfirst(str_replace('_', ' ', $application->status)),
            'status' => 'current',
        ];
        $timelineItems[] = [
            'title' => 'Penerbitan izin',
            'subtitle' => 'Menunggu proses selesai',
            'status' => 'pending',
            'icon' => 'fa-flag-checkered',
        ];
    }

    // Notes
    $notes = $application->notes()->visibleToClient()->orderBy('created_at', 'asc')->get();
    $unreadAdminNotes = 0; // sudah di-mark read di controller

    // Tab badges
    $tabs = [
        ['id' => 'detail',   'label' => 'Ringkasan',   'icon' => 'fas fa-info-circle'],
        ['id' => 'docs',     'label' => 'Dokumen',     'icon' => 'fas fa-paperclip', 'badge' => $totalDocuments ?: null],
        ['id' => 'quote',    'label' => 'Penawaran',   'icon' => 'fas fa-file-invoice-dollar'],
        ['id' => 'notes',    'label' => 'Komunikasi',  'icon' => 'fas fa-comments',  'badge' => $notes->count() ?: null],
        ['id' => 'timeline', 'label' => 'Riwayat',     'icon' => 'fas fa-clock-rotate-left'],
    ];
@endphp

{{-- ─── HERO ─── --}}
<section class="portal-hero relative overflow-hidden border-b border-[var(--border-subtle)]"
         style="background: linear-gradient(135deg, var(--client-primary) 0%, color-mix(in oklab, var(--client-primary) 80%, #003a6b) 100%); color: #fff;">
    <div class="portal-glow-orb portal-glow-orb--tr hidden lg:block" aria-hidden="true"
         style="background: radial-gradient(circle at 50% 50%, rgba(255,255,255,0.2) 0%, transparent 70%); --tr-x: 80px; --tr-y: -80px;"></div>

    <div class="relative max-w-[1400px] mx-auto px-4 lg:px-8 py-5 lg:py-7">
        <a href="{{ route('client.applications.index') }}"
           class="inline-flex items-center gap-2 text-white/80 hover:text-white text-xs font-medium mb-4 transition-colors">
            <i class="fas fa-arrow-left text-[10px]" aria-hidden="true"></i>
            Daftar Permohonan
        </a>

        <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-5">
            <div class="min-w-0">
                <span class="portal-eyebrow" style="background: rgba(255,255,255,0.15); color: rgba(255,255,255,0.9); border-color: rgba(255,255,255,0.2);">
                    <i class="fas fa-folder-open text-[9px]" aria-hidden="true"></i>
                    Permohonan
                    @if($isPackage)
                    <span class="ml-1">· Paket Multi-Izin</span>
                    @endif
                </span>
                <h1 class="mt-2 portal-mono text-xl lg:text-2xl font-bold leading-tight">
                    {{ $application->application_number }}
                </h1>
                <p class="mt-1 text-sm lg:text-base text-white/90 leading-snug max-w-2xl">
                    {{ $permitName }}
                </p>
                <div class="mt-3 flex flex-wrap items-center gap-2">
                    <span class="inline-flex items-center gap-1.5 bg-white/15 backdrop-blur border border-white/20 px-3 py-1 rounded-full text-xs font-semibold">
                        <i class="fas fa-circle text-[8px]" aria-hidden="true"></i>
                        {{ $application->status_label ?? ucfirst(str_replace('_', ' ', $application->status)) }}
                    </span>
                    <span class="text-xs text-white/70">
                        <i class="fas fa-calendar text-[10px] mr-1" aria-hidden="true"></i>
                        Dibuat {{ $application->created_at->format('d M Y') }}
                    </span>
                    @if($application->submitted_at)
                    <span class="text-xs text-white/70">
                        <i class="fas fa-paper-plane text-[10px] mr-1" aria-hidden="true"></i>
                        Diajukan {{ $application->submitted_at->format('d M Y') }}
                    </span>
                    @endif
                </div>
            </div>

            {{-- Quick stats --}}
            <div class="grid grid-cols-3 gap-2 lg:gap-3 lg:flex-shrink-0">
                <div class="bg-white/10 backdrop-blur border border-white/15 rounded-lg px-3 py-2.5 lg:min-w-[110px]">
                    <p class="text-[10px] uppercase tracking-wider text-white/70 font-semibold">Dokumen</p>
                    <p class="text-xl lg:text-2xl font-bold tabular-nums">{{ $totalDocuments }}</p>
                    @if($requiredDocs > 0)
                    <p class="text-[10px] text-white/60">dari {{ $requiredDocs }} wajib</p>
                    @endif
                </div>
                <div class="bg-white/10 backdrop-blur border border-white/15 rounded-lg px-3 py-2.5 lg:min-w-[110px]">
                    <p class="text-[10px] uppercase tracking-wider text-white/70 font-semibold">Progress</p>
                    <p class="text-xl lg:text-2xl font-bold tabular-nums">{{ $documentProgress }}%</p>
                    <p class="text-[10px] text-white/60">Kelengkapan</p>
                </div>
                <div class="bg-white/10 backdrop-blur border border-white/15 rounded-lg px-3 py-2.5 lg:min-w-[110px]">
                    @if($application->quoted_price)
                    <p class="text-[10px] uppercase tracking-wider text-white/70 font-semibold">Nilai</p>
                    <p class="text-base lg:text-lg font-bold tabular-nums">Rp {{ number_format($application->quoted_price / 1000000, 1) }}M</p>
                    <p class="text-[10px] text-white/60">Investasi</p>
                    @else
                    <p class="text-[10px] uppercase tracking-wider text-white/70 font-semibold">Umur</p>
                    <p class="text-xl lg:text-2xl font-bold tabular-nums">{{ $daysAgo }}</p>
                    <p class="text-[10px] text-white/60">hari lalu</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ─── BODY: 3-col on desktop (2 main + 1 sidebar) ─── --}}
<div class="max-w-[1400px] mx-auto px-4 lg:px-8 py-6">
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- MAIN — TABS --}}
    <div class="lg:col-span-2">
        <div class="bg-[var(--surface-elevated)] border border-[var(--border-subtle)] rounded-xl">
            <div class="px-4 lg:px-6 pt-4">
                <x-ui.tabs :tabs="$tabs" defaultTab="detail">

                    {{-- TAB: DETAIL --}}
                    <div x-show="activeTab === 'detail'" x-transition.opacity class="space-y-5 pt-2 pb-5">

                        {{-- Permit info --}}
                        <section>
                            <span class="portal-eyebrow"><i class="fas fa-info text-[9px]" aria-hidden="true"></i> Informasi Permohonan</span>
                            <dl class="mt-3 grid grid-cols-2 gap-x-4 gap-y-3 text-sm">
                                <div>
                                    <dt class="text-[10px] uppercase tracking-wider text-[var(--text-tertiary)] font-semibold">Nomor</dt>
                                    <dd class="portal-mono text-[var(--text-primary)] font-semibold mt-0.5">{{ $application->application_number }}</dd>
                                </div>
                                <div>
                                    <dt class="text-[10px] uppercase tracking-wider text-[var(--text-tertiary)] font-semibold">{{ $isPackage ? 'Nama Proyek' : 'Jenis Izin' }}</dt>
                                    <dd class="text-[var(--text-primary)] mt-0.5">{{ $permitName }}</dd>
                                </div>
                                @if($application->kbli_code)
                                <div class="col-span-2">
                                    <dt class="text-[10px] uppercase tracking-wider text-[var(--text-tertiary)] font-semibold">KBLI</dt>
                                    <dd class="text-[var(--text-primary)] mt-0.5">
                                        <span class="portal-mono font-semibold">{{ $application->kbli_code }}</span>
                                        @if($application->kbli_description)
                                            <span class="text-[var(--text-secondary)]"> · {{ $application->kbli_description }}</span>
                                        @endif
                                    </dd>
                                </div>
                                @endif
                                <div>
                                    <dt class="text-[10px] uppercase tracking-wider text-[var(--text-tertiary)] font-semibold">Tanggal Dibuat</dt>
                                    <dd class="text-[var(--text-primary)] mt-0.5">{{ $application->created_at->format('d M Y, H:i') }}</dd>
                                </div>
                                @if($application->submitted_at)
                                <div>
                                    <dt class="text-[10px] uppercase tracking-wider text-[var(--text-tertiary)] font-semibold">Tanggal Diajukan</dt>
                                    <dd class="text-[var(--text-primary)] mt-0.5">{{ $application->submitted_at->format('d M Y, H:i') }}</dd>
                                </div>
                                @endif
                            </dl>
                        </section>

                        {{-- Package detail --}}
                        @if($isPackage)
                        <section class="border-t border-[var(--border-subtle)] pt-5">
                            <span class="portal-eyebrow"><i class="fas fa-box text-[9px]" aria-hidden="true"></i> Detail Paket</span>
                            <div class="grid grid-cols-3 gap-3 mt-3">
                                @foreach([
                                    ['label'=>'BizMark.ID',      'value'=>$formData['permits_by_service']['bizmark'] ?? 0, 'color'=>'var(--client-primary)', 'icon'=>'fa-handshake'],
                                    ['label'=>'Sudah Ada',       'value'=>$formData['permits_by_service']['owned']   ?? 0, 'color'=>'var(--apple-green)',    'icon'=>'fa-check-circle'],
                                    ['label'=>'Sendiri',         'value'=>$formData['permits_by_service']['self']    ?? 0, 'color'=>'var(--text-tertiary)',  'icon'=>'fa-user-check'],
                                ] as $p)
                                <div class="rounded-lg border border-[var(--border-subtle)] p-3 text-center"
                                     style="background: {{ $p['color'] }}0a;">
                                    <i class="fas {{ $p['icon'] }} text-base mb-1" style="color: {{ $p['color'] }};" aria-hidden="true"></i>
                                    <p class="text-2xl font-bold tabular-nums text-[var(--text-primary)]">{{ $p['value'] }}</p>
                                    <p class="text-[10px] text-[var(--text-secondary)]">{{ $p['label'] }}</p>
                                </div>
                                @endforeach
                            </div>

                            @if(!empty($formData['selected_permits']))
                            <div class="mt-4 space-y-1.5">
                                <p class="text-[10px] uppercase tracking-wider text-[var(--text-tertiary)] font-semibold">Daftar Izin ({{ count($formData['selected_permits']) }})</p>
                                @foreach($formData['selected_permits'] as $permit)
                                @php
                                    $svc = $permit['service_type'] ?? 'self';
                                    $svcLabel = ['bizmark'=>'BizMark.ID','owned'=>'Sudah Ada','self'=>'Sendiri'][$svc] ?? $svc;
                                    $svcVariant = ['bizmark'=>'info','owned'=>'success','self'=>'neutral'][$svc] ?? 'neutral';
                                @endphp
                                <div class="flex items-center justify-between gap-3 rounded-md border border-[var(--border-subtle)] bg-[var(--surface-cool)] px-3 py-2">
                                    <span class="text-sm text-[var(--text-primary)] truncate">{{ $permit['name'] }}</span>
                                    <span class="portal-pill portal-pill--{{ $svcVariant }} flex-shrink-0">{{ $svcLabel }}</span>
                                </div>
                                @endforeach
                            </div>
                            @endif
                        </section>
                        @endif

                        {{-- Company --}}
                        <section class="border-t border-[var(--border-subtle)] pt-5">
                            <span class="portal-eyebrow"><i class="fas fa-building text-[9px]" aria-hidden="true"></i> Data Perusahaan</span>
                            <dl class="mt-3 grid grid-cols-2 gap-x-4 gap-y-3 text-sm">
                                <div class="col-span-2">
                                    <dt class="text-[10px] uppercase tracking-wider text-[var(--text-tertiary)] font-semibold">Nama</dt>
                                    <dd class="text-[var(--text-primary)] font-medium mt-0.5">{{ $formData['company_name'] ?? '—' }}</dd>
                                </div>
                                <div class="col-span-2">
                                    <dt class="text-[10px] uppercase tracking-wider text-[var(--text-tertiary)] font-semibold">Alamat</dt>
                                    <dd class="text-[var(--text-secondary)] mt-0.5">{{ $formData['company_address'] ?? '—' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-[10px] uppercase tracking-wider text-[var(--text-tertiary)] font-semibold">NPWP</dt>
                                    <dd class="portal-mono text-[var(--text-primary)] mt-0.5">{{ $formData['company_npwp'] ?? '—' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-[10px] uppercase tracking-wider text-[var(--text-tertiary)] font-semibold">Telepon</dt>
                                    <dd class="portal-mono text-[var(--text-primary)] mt-0.5">{{ $formData['company_phone'] ?? '—' }}</dd>
                                </div>
                            </dl>
                        </section>

                        {{-- PIC --}}
                        <section class="border-t border-[var(--border-subtle)] pt-5">
                            <span class="portal-eyebrow"><i class="fas fa-user text-[9px]" aria-hidden="true"></i> Penanggung Jawab</span>
                            <dl class="mt-3 grid grid-cols-2 gap-x-4 gap-y-3 text-sm">
                                <div>
                                    <dt class="text-[10px] uppercase tracking-wider text-[var(--text-tertiary)] font-semibold">Nama</dt>
                                    <dd class="text-[var(--text-primary)] mt-0.5">{{ $formData['pic_name'] ?? '—' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-[10px] uppercase tracking-wider text-[var(--text-tertiary)] font-semibold">Jabatan</dt>
                                    <dd class="text-[var(--text-primary)] mt-0.5">{{ $formData['pic_position'] ?? '—' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-[10px] uppercase tracking-wider text-[var(--text-tertiary)] font-semibold">Email</dt>
                                    <dd class="portal-mono text-[var(--text-primary)] text-xs mt-0.5 break-all">{{ $formData['pic_email'] ?? '—' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-[10px] uppercase tracking-wider text-[var(--text-tertiary)] font-semibold">No. HP</dt>
                                    <dd class="portal-mono text-[var(--text-primary)] mt-0.5">{{ $formData['pic_phone'] ?? '—' }}</dd>
                                </div>
                            </dl>
                        </section>

                        @if(!empty($formData['notes']))
                        <section class="border-t border-[var(--border-subtle)] pt-5">
                            <span class="portal-eyebrow"><i class="fas fa-sticky-note text-[9px]" aria-hidden="true"></i> Catatan</span>
                            <p class="mt-2 text-sm text-[var(--text-secondary)] leading-relaxed whitespace-pre-line">{{ $formData['notes'] }}</p>
                        </section>
                        @endif
                    </div>

                    {{-- TAB: DOCS --}}
                    <div x-show="activeTab === 'docs'" x-transition.opacity class="pt-2 pb-5">
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <p class="text-sm font-semibold text-[var(--text-primary)]">{{ $totalDocuments }} Dokumen</p>
                                @if($requiredDocs > 0)
                                <p class="text-xs text-[var(--text-secondary)] mt-0.5">{{ $documentProgress }}% kelengkapan ({{ $totalDocuments }}/{{ $requiredDocs }} wajib)</p>
                                @endif
                            </div>
                            @if(in_array($application->status, ['draft', 'document_incomplete']))
                            <button type="button" @click="$dispatch('drawer-open', { name: 'doc-upload' })"
                                    class="inline-flex items-center gap-2 bg-[var(--client-primary)] hover:opacity-90 text-white text-sm font-semibold px-3 py-2 rounded-md transition-opacity">
                                <i class="fas fa-upload text-xs" aria-hidden="true"></i> Upload
                            </button>
                            @endif
                        </div>

                        @if($application->documents->count() > 0)
                        <ul class="divide-y divide-[var(--border-subtle)] border border-[var(--border-subtle)] rounded-lg overflow-hidden">
                            @foreach($application->documents as $document)
                            @php
                                $docVariant = $document->status === 'approved' ? 'success'
                                            : ($document->status === 'rejected' ? 'danger' : 'warning');
                                $docIcon = $document->status === 'approved' ? 'fa-check-circle'
                                         : ($document->status === 'rejected' ? 'fa-times-circle' : 'fa-clock');
                                $fileIcon = str_contains($document->mime_type ?? '', 'pdf') ? 'fa-file-pdf'
                                          : (str_contains($document->mime_type ?? '', 'image') ? 'fa-file-image' : 'fa-file');
                            @endphp
                            <li class="flex items-start gap-3 px-4 py-3 bg-[var(--surface-elevated)] hover:bg-[var(--surface-cool)] transition-colors">
                                <i class="fas {{ $fileIcon }} text-lg text-[var(--text-tertiary)] mt-0.5" aria-hidden="true"></i>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <p class="text-sm font-semibold text-[var(--text-primary)] truncate">{{ $document->document_type }}</p>
                                        <span class="portal-pill portal-pill--{{ $docVariant }} portal-pill--with-icon">
                                            <i class="fas {{ $docIcon }} text-[9px]" aria-hidden="true"></i>
                                            {{ ['approved'=>'Disetujui','rejected'=>'Ditolak'][$document->status] ?? 'Menunggu' }}
                                        </span>
                                    </div>
                                    <p class="text-xs text-[var(--text-secondary)] mt-0.5 truncate">{{ $document->file_name }}</p>
                                    <p class="text-[10px] text-[var(--text-tertiary)] mt-0.5">
                                        {{ $document->file_size_formatted ?? '' }} · {{ $document->created_at->format('d M Y') }}
                                    </p>
                                    @if($document->status === 'rejected' && $document->review_notes)
                                    <p class="text-xs text-[var(--apple-red)] mt-1.5">
                                        <i class="fas fa-exclamation-circle text-[10px] mr-1" aria-hidden="true"></i>
                                        {{ $document->review_notes }}
                                    </p>
                                    @endif
                                </div>
                                @if($document->status === 'rejected' || (!$document->is_verified && in_array($application->status, ['draft', 'document_incomplete'])))
                                <form method="POST" action="{{ route('client.applications.documents.delete', [$application->id, $document->id]) }}"
                                      x-data @submit.prevent="if(confirm('Hapus dokumen ini?')) $el.submit()" class="flex-shrink-0">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="p-1.5 text-[var(--apple-red)] hover:bg-[var(--apple-red)]/10 rounded transition-colors" title="Hapus">
                                        <i class="fas fa-trash text-xs" aria-hidden="true"></i>
                                    </button>
                                </form>
                                @endif
                            </li>
                            @endforeach
                        </ul>
                        @else
                        <x-ui.empty-state
                            icon="fas fa-file-arrow-up"
                            size="sm"
                            title="Belum ada dokumen"
                            description="Upload dokumen pendukung sesuai checklist di sidebar."
                        />
                        @endif

                        {{-- Required docs checklist (mobile-friendly inside tab) --}}
                        @if($application->permitType && $application->permitType->required_documents)
                        <div class="mt-5 border-t border-[var(--border-subtle)] pt-4">
                            <p class="text-[10px] uppercase tracking-wider text-[var(--text-tertiary)] font-semibold mb-2">Checklist Wajib</p>
                            <ul class="space-y-1.5">
                                @foreach($application->permitType->required_documents as $doc)
                                @php $uploaded = $application->documents->where('document_type', $doc)->isNotEmpty(); @endphp
                                <li class="flex items-center gap-2 text-sm">
                                    <i class="fas {{ $uploaded ? 'fa-check-circle text-[var(--apple-green)]' : 'fa-circle text-[var(--text-tertiary)]' }} text-xs" aria-hidden="true"></i>
                                    <span class="{{ $uploaded ? 'text-[var(--text-primary)] font-medium' : 'text-[var(--text-secondary)]' }}">{{ $doc }}</span>
                                </li>
                                @endforeach
                            </ul>
                        </div>
                        @endif
                    </div>

                    {{-- TAB: QUOTE --}}
                    <div x-show="activeTab === 'quote'" x-transition.opacity class="pt-2 pb-5">
                        @if($application->quotation || $application->quoted_price)
                        <div class="rounded-lg border border-[var(--border-subtle)] bg-gradient-to-br from-[var(--client-primary-light)] to-[var(--surface-elevated)] p-5">
                            <span class="portal-eyebrow"><i class="fas fa-file-invoice-dollar text-[9px]" aria-hidden="true"></i> Penawaran</span>
                            <p class="mt-3 text-3xl font-bold text-[var(--text-primary)] tabular-nums">
                                Rp {{ number_format($application->quoted_price ?? 0, 0, ',', '.') }}
                            </p>
                            @if($application->quotation && $application->quotation->valid_until ?? false)
                            <p class="text-xs text-[var(--text-secondary)] mt-1">
                                Berlaku hingga {{ \Carbon\Carbon::parse($application->quotation->valid_until)->format('d M Y') }}
                            </p>
                            @endif

                            <div class="mt-4 flex flex-wrap gap-2">
                                <a href="{{ route('client.quotations.show', $application->id) }}"
                                   class="inline-flex items-center gap-2 bg-[var(--client-primary)] hover:opacity-90 text-white text-sm font-semibold px-4 py-2 rounded-md transition-opacity">
                                    <i class="fas fa-eye text-xs" aria-hidden="true"></i> Lihat Detail
                                </a>
                                @if($pendingPayment)
                                <span class="portal-pill portal-pill--warning portal-pill--with-icon">
                                    <i class="fas fa-clock text-[9px]" aria-hidden="true"></i>
                                    Pembayaran sedang diproses
                                </span>
                                @endif
                            </div>
                        </div>
                        @else
                        <x-ui.empty-state
                            icon="fas fa-file-invoice-dollar"
                            size="sm"
                            title="Belum ada penawaran"
                            description="Tim kami akan mengirim penawaran setelah verifikasi dokumen selesai."
                        />
                        @endif
                    </div>

                    {{-- TAB: NOTES --}}
                    <div x-show="activeTab === 'notes'" x-transition.opacity class="pt-2 pb-5">
                        @if($application->status === 'draft')
                        <x-ui.empty-state
                            icon="fas fa-comments"
                            size="sm"
                            title="Komunikasi belum aktif"
                            description="Komunikasi dengan admin akan tersedia setelah permohonan diajukan."
                        />
                        @else
                            @if($notes->count() > 0)
                            <div id="chatContainer" class="max-h-[480px] overflow-y-auto space-y-3 mb-4 px-1 py-2 scroll-smooth">
                                @foreach($notes as $note)
                                @php $isAdmin = $note->author_type === 'admin'; @endphp
                                <div class="flex gap-2 items-end {{ $isAdmin ? '' : 'flex-row-reverse' }}">
                                    <div class="flex-shrink-0 w-7 h-7 rounded-full inline-flex items-center justify-center text-[10px] font-semibold"
                                         style="background: {{ $isAdmin ? 'var(--client-primary)' : 'var(--apple-green)' }}; color: white;">
                                        <i class="fas {{ $isAdmin ? 'fa-user-shield' : 'fa-user' }}" aria-hidden="true"></i>
                                    </div>
                                    <div class="max-w-[78%] rounded-2xl px-3.5 py-2.5
                                        {{ $isAdmin
                                            ? 'rounded-bl-sm bg-[var(--surface-cool)] border border-[var(--border-subtle)]'
                                            : 'rounded-br-sm bg-[var(--client-primary)] text-white' }}">
                                        <div class="flex items-center gap-1.5 mb-1 {{ $isAdmin ? '' : 'justify-end' }}">
                                            <span class="text-[10px] font-semibold {{ $isAdmin ? 'text-[var(--client-primary)]' : 'text-white/90' }}">
                                                {{ $note->author->name ?? ($isAdmin ? 'Admin' : 'Anda') }}
                                            </span>
                                        </div>
                                        <p class="text-sm leading-relaxed whitespace-pre-wrap break-words {{ $isAdmin ? 'text-[var(--text-primary)]' : 'text-white' }}">{{ $note->note }}</p>
                                        <p class="text-[10px] mt-1.5 {{ $isAdmin ? 'text-[var(--text-tertiary)]' : 'text-white/70' }} {{ $isAdmin ? '' : 'text-right' }}">
                                            {{ $note->created_at->format('H:i · d M') }}
                                        </p>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            @else
                            <p class="text-center py-8 text-sm text-[var(--text-secondary)]">
                                <i class="fas fa-comment-slash text-2xl text-[var(--text-tertiary)] block mb-2" aria-hidden="true"></i>
                                Belum ada pesan. Mulai percakapan dengan admin di bawah.
                            </p>
                            @endif

                            <form action="{{ route('client.applications.notes.store', $application->id) }}" method="POST" class="border-t border-[var(--border-subtle)] pt-3">
                                @csrf
                                <div class="flex gap-2 items-end">
                                    <textarea name="note" rows="1" required
                                              placeholder="Ketik pesan…"
                                              class="flex-1 px-3.5 py-2.5 bg-[var(--surface-cool)] border border-[var(--border-subtle)] rounded-2xl focus:ring-2 focus:ring-[var(--client-primary)] focus:border-[var(--client-primary)] text-sm text-[var(--text-primary)] placeholder-[var(--text-tertiary)] resize-none transition"
                                              style="min-height: 40px; max-height: 120px;"
                                              oninput="this.style.height='auto';this.style.height=Math.min(this.scrollHeight,120)+'px'"></textarea>
                                    <button type="submit"
                                            class="flex-shrink-0 w-10 h-10 rounded-full bg-[var(--client-primary)] hover:opacity-90 text-white inline-flex items-center justify-center transition-opacity"
                                            title="Kirim">
                                        <i class="fas fa-paper-plane text-xs" aria-hidden="true"></i>
                                    </button>
                                </div>
                            </form>
                        @endif
                    </div>

                    {{-- TAB: TIMELINE --}}
                    <div x-show="activeTab === 'timeline'" x-transition.opacity class="pt-2 pb-5">
                        @if(count($timelineItems) > 0)
                        <x-ui.timeline :items="$timelineItems" />
                        @else
                        <x-ui.empty-state icon="fas fa-clock-rotate-left" size="sm" title="Belum ada riwayat" />
                        @endif
                    </div>
                </x-ui.tabs>
            </div>
        </div>
    </div>

    {{-- SIDEBAR --}}
    <aside class="lg:col-span-1 space-y-4">

        {{-- Action card --}}
        <div class="bg-[var(--surface-elevated)] border border-[var(--border-subtle)] rounded-xl px-4 lg:px-5 py-4">
            <span class="portal-eyebrow"><i class="fas fa-bolt text-[9px]" aria-hidden="true"></i> Tindakan</span>
            <div class="mt-3 space-y-2">
                @if($application->status === 'converted_to_project' && $application->project_id)
                <a href="{{ route('client.projects.show', $application->project_id) }}"
                   class="flex items-center justify-center gap-2 w-full px-3 py-2.5 bg-[var(--apple-green)] hover:opacity-90 text-white text-sm font-semibold rounded-md transition-opacity">
                    <i class="fas fa-diagram-project text-xs" aria-hidden="true"></i> Lihat Project
                </a>
                @endif

                @if($application->quotation && in_array($application->status, ['quoted','quotation_accepted','payment_pending','payment_verified']))
                <a href="{{ route('client.quotations.show', $application->id) }}"
                   class="flex items-center justify-center gap-2 w-full px-3 py-2.5 bg-[var(--client-primary)] hover:opacity-90 text-white text-sm font-semibold rounded-md transition-opacity">
                    <i class="fas fa-file-invoice text-xs" aria-hidden="true"></i> Lihat Quotation
                </a>
                @endif

                @if(method_exists($application, 'canBeEdited') && $application->canBeEdited())
                <a href="{{ route('client.applications.edit', $application->id) }}"
                   class="flex items-center justify-center gap-2 w-full px-3 py-2.5 bg-[var(--surface-cool)] hover:bg-[var(--surface-sunken)] text-[var(--text-primary)] text-sm font-semibold rounded-md border border-[var(--border-subtle)] transition-colors">
                    <i class="fas fa-edit text-xs" aria-hidden="true"></i> Edit Permohonan
                </a>
                @endif

                @if($application->status === 'draft' && $application->documents->count() > 0)
                <a href="{{ route('client.applications.preview-submit', $application->id) }}"
                   class="flex items-center justify-center gap-2 w-full px-3 py-2.5 bg-[var(--apple-green)] hover:opacity-90 text-white text-sm font-semibold rounded-md transition-opacity">
                    <i class="fas fa-paper-plane text-xs" aria-hidden="true"></i> Ajukan Permohonan
                </a>
                @endif

                @if(method_exists($application, 'canBeCancelled') && $application->canBeCancelled())
                <form method="POST" action="{{ route('client.applications.cancel', $application->id) }}"
                      x-data @submit.prevent="if(confirm('Yakin ingin membatalkan permohonan ini?')) $el.submit()">
                    @csrf
                    <button type="submit" class="w-full px-3 py-2.5 bg-transparent hover:bg-[var(--apple-red)]/10 text-[var(--apple-red)] text-sm font-semibold rounded-md border border-[var(--apple-red)]/30 transition-colors">
                        <i class="fas fa-times-circle text-xs mr-1.5" aria-hidden="true"></i> Batalkan
                    </button>
                </form>
                @endif
            </div>
        </div>

        {{-- Mini timeline --}}
        @if(count($timelineItems) > 0)
        <div class="bg-[var(--surface-elevated)] border border-[var(--border-subtle)] rounded-xl px-4 lg:px-5 py-4 hidden lg:block">
            <span class="portal-eyebrow"><i class="fas fa-route text-[9px]" aria-hidden="true"></i> Progress</span>
            <div class="mt-3">
                <x-ui.timeline :items="array_slice($timelineItems, 0, 4)" />
            </div>
        </div>
        @endif
    </aside>
</div>
</div>

{{-- ─── UPLOAD DRAWER ─── --}}
<x-ui.drawer name="doc-upload" size="md" title="Upload Dokumen" subtitle="Tambah berkas pendukung permohonan">
    <form method="POST" action="{{ route('client.applications.documents.upload', $application->id) }}" enctype="multipart/form-data" class="space-y-4">
        @csrf
        <div>
            <label class="block text-xs font-semibold text-[var(--text-primary)] mb-1.5">
                Jenis Dokumen <span class="text-[var(--apple-red)]">*</span>
            </label>
            <select name="document_type" required
                    class="w-full px-3 py-2 bg-[var(--surface-cool)] border border-[var(--border-subtle)] rounded-md focus:ring-2 focus:ring-[var(--client-primary)] focus:border-[var(--client-primary)] text-sm text-[var(--text-primary)]">
                <option value="">Pilih jenis dokumen…</option>
                @if($application->permitType && $application->permitType->required_documents)
                    @foreach($application->permitType->required_documents as $doc)
                        <option value="{{ $doc }}">{{ $doc }}</option>
                    @endforeach
                @endif
                <option value="Lainnya">Lainnya</option>
            </select>
        </div>
        <div>
            <label class="block text-xs font-semibold text-[var(--text-primary)] mb-1.5">
                File <span class="text-[var(--apple-red)]">*</span>
            </label>
            <input type="file" name="file" accept=".pdf,.jpg,.jpeg,.png" required
                   class="w-full px-3 py-2 bg-[var(--surface-cool)] border border-[var(--border-subtle)] rounded-md text-sm text-[var(--text-primary)] file:mr-3 file:py-1 file:px-3 file:rounded file:border-0 file:bg-[var(--client-primary)] file:text-white file:font-semibold file:text-xs">
            <p class="text-[10px] text-[var(--text-tertiary)] mt-1">PDF, JPG, PNG · Maks 5MB</p>
        </div>
        <div>
            <label class="block text-xs font-semibold text-[var(--text-primary)] mb-1.5">Catatan (opsional)</label>
            <textarea name="notes" rows="3" placeholder="Tambahkan catatan jika perlu…"
                      class="w-full px-3 py-2 bg-[var(--surface-cool)] border border-[var(--border-subtle)] rounded-md focus:ring-2 focus:ring-[var(--client-primary)] focus:border-[var(--client-primary)] text-sm text-[var(--text-primary)] resize-none"></textarea>
        </div>

        <div class="flex gap-2 pt-2">
            <button type="button" @click="$dispatch('drawer-close', { name: 'doc-upload' })"
                    class="flex-1 px-3 py-2.5 bg-[var(--surface-cool)] hover:bg-[var(--surface-sunken)] text-[var(--text-primary)] text-sm font-semibold rounded-md border border-[var(--border-subtle)] transition-colors">
                Batal
            </button>
            <button type="submit"
                    class="flex-1 px-3 py-2.5 bg-[var(--client-primary)] hover:opacity-90 text-white text-sm font-semibold rounded-md transition-opacity">
                <i class="fas fa-upload text-xs mr-1.5" aria-hidden="true"></i> Upload
            </button>
        </div>
    </form>
</x-ui.drawer>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const chat = document.getElementById('chatContainer');
        if (chat) chat.scrollTop = chat.scrollHeight;
    });
</script>
@endpush
