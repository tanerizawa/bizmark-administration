{{-- Hero --}}
<div class="client-hero px-4 sm:px-6 py-6">
    <div class="max-w-5xl mx-auto">
        <div class="flex items-center gap-3 mb-1">
            <i class="fas fa-file-chart-column text-xl opacity-90"></i>
            <h1 class="text-2xl font-bold tracking-tight">Laporan Compliance</h1>
        </div>
        <p class="text-white/80 text-sm">Generate laporan UKL-UPL dan dokumen lingkungan dengan AI secara otomatis.</p>
    </div>
</div>

<div class="max-w-5xl mx-auto px-4 sm:px-6 py-5 space-y-6"
     x-data="{
         statusFilter: 'all',
         emailModal: false,
         emailReportId: null,
         emailAddr: '',
         emailSending: false,
         openEmail(id) { this.emailReportId = id; this.emailModal = true; this.emailAddr = ''; },
         async sendEmail() {
             if (!this.emailAddr) return;
             this.emailSending = true;
             try {
                 await window.apiFetch('/client/compliance-reports/' + this.emailReportId + '/email', {
                     method: 'POST',
                     headers: { 'Content-Type': 'application/json' },
                     body: JSON.stringify({ email: this.emailAddr })
                 });
                 this.emailModal = false;
                 alert('Laporan berhasil dikirim ke ' + this.emailAddr);
             } catch(e) {
                 alert('Gagal mengirim. Coba lagi.');
             } finally { this.emailSending = false; }
         }
     }">

    {{-- Flash --}}
    @if(session('success'))
    <div class="flex items-center gap-2 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-xl px-4 py-3 text-sm text-green-800 dark:text-green-300">
        <i class="fas fa-circle-check text-green-500"></i> {{ session('success') }}
    </div>
    @endif

    {{-- Template gallery --}}
    @if($templates->isNotEmpty())
    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl overflow-hidden">
        <div class="flex items-center justify-between gap-3 px-4 sm:px-5 py-4 border-b border-gray-100 dark:border-gray-700">
            <h2 class="text-sm font-bold text-gray-900 dark:text-white flex items-center gap-2">
                <i class="fas fa-layer-group text-[#0a66c2] text-sm"></i> Pilih Template
            </h2>
            <a href="{{ route('client.compliance-reports.create') }}"
               class="inline-flex items-center gap-1.5 text-xs font-semibold px-3 py-1.5 bg-[#0a66c2] hover:bg-[#004182] text-white rounded-xl transition active:scale-95">
                <i class="fas fa-plus text-[9px]"></i> Buat Laporan
            </a>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 p-4 sm:p-5">
            @php
                $typeIcons = [
                    'ukl_upl'       => ['icon' => 'fa-leaf', 'color' => 'bg-green-50 dark:bg-green-900/20 text-green-600', 'border' => 'border-green-200 dark:border-green-800'],
                    'amdal'         => ['icon' => 'fa-mountain', 'color' => 'bg-blue-50 dark:bg-blue-900/20 text-blue-600', 'border' => 'border-blue-200 dark:border-blue-800'],
                    'sppl'          => ['icon' => 'fa-shield-halved', 'color' => 'bg-purple-50 dark:bg-purple-900/20 text-purple-600', 'border' => 'border-purple-200 dark:border-purple-800'],
                    'compliance'    => ['icon' => 'fa-clipboard-check', 'color' => 'bg-indigo-50 dark:bg-indigo-900/20 text-indigo-600', 'border' => 'border-indigo-200 dark:border-indigo-800'],
                ];
                $defaultIcon = ['icon' => 'fa-file-lines', 'color' => 'bg-gray-50 dark:bg-gray-700 text-gray-500', 'border' => 'border-gray-200 dark:border-gray-700'];
            @endphp
            @foreach($templates as $tpl)
            @php $ti = $typeIcons[$tpl->type] ?? $defaultIcon; @endphp
            <a href="{{ route('client.compliance-reports.create') }}?template={{ $tpl->id }}"
               class="group flex flex-col gap-3 p-4 bg-white dark:bg-gray-800 border {{ $ti['border'] }} rounded-xl hover:shadow-md transition active:scale-95">
                <div class="w-10 h-10 rounded-xl {{ $ti['color'] }} flex items-center justify-center flex-shrink-0">
                    <i class="fas {{ $ti['icon'] }} text-base"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="font-semibold text-sm text-gray-900 dark:text-white leading-snug group-hover:text-[#0a66c2] transition">{{ $tpl->name }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ \App\Models\ReportTemplate::$typeLabels[$tpl->type] ?? $tpl->type }}</p>
                    @if($tpl->regulatory_basis)
                    <p class="text-[10px] text-gray-400 dark:text-gray-500 mt-1 truncate">{{ $tpl->regulatory_basis }}</p>
                    @endif
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-[10px] font-semibold text-[#0a66c2] group-hover:underline">Buat Laporan →</span>
                </div>
            </a>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Reports history --}}
    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl overflow-hidden">
        <div class="flex items-center justify-between gap-3 px-4 sm:px-5 py-4 border-b border-gray-100 dark:border-gray-700">
            <h2 class="text-sm font-bold text-gray-900 dark:text-white flex items-center gap-2">
                <i class="fas fa-clock-rotate-left text-[#0a66c2] text-sm"></i> Riwayat Laporan
            </h2>
            {{-- Status filter chips --}}
            <div class="flex items-center gap-1.5 flex-wrap">
                @foreach(['all' => 'Semua', 'ready' => 'Siap', 'generating' => 'Proses', 'failed' => 'Gagal'] as $fKey => $fLabel)
                <button type="button"
                        @click="statusFilter='{{ $fKey }}'"
                        :class="statusFilter==='{{ $fKey }}' ? 'bg-[#0a66c2] text-white border-[#0a66c2]' : 'bg-white dark:bg-gray-800 text-gray-500 dark:text-gray-400 border-gray-200 dark:border-gray-700 hover:border-[#0a66c2]'"
                        class="text-[10px] font-semibold px-2.5 py-1 border rounded-full transition">
                    {{ $fLabel }}
                </button>
                @endforeach
            </div>
        </div>

        @if($reports->isEmpty())
        <div class="px-5 py-10">
            @include('client.components.empty-state', [
                'icon'     => 'fa-file-chart-column',
                'title'    => 'Belum Ada Laporan',
                'message'  => 'Klik "Buat Laporan" untuk mulai generate laporan compliance pertama Anda.',
                'ctaLabel' => 'Buat Laporan',
                'ctaHref'  => route('client.compliance-reports.create'),
                'ctaIcon'  => 'fa-plus',
                'size'     => 'sm',
                'color'    => 'blue',
            ])
        </div>
        @else
        <div class="divide-y divide-gray-100 dark:divide-gray-700">
            @foreach($reports as $report)
            @php
                $stIcon = match($report->status) {
                    'ready'      => ['icon' => 'fa-circle-check', 'cls' => 'text-green-500'],
                    'generating' => ['icon' => 'fa-rotate', 'cls' => 'text-blue-500 animate-spin'],
                    'failed'     => ['icon' => 'fa-circle-xmark', 'cls' => 'text-red-500'],
                    default      => ['icon' => 'fa-clock', 'cls' => 'text-gray-400'],
                };
            @endphp
            <div class="flex items-center gap-4 px-4 sm:px-5 py-3.5 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition"
                 x-show="statusFilter === 'all' || statusFilter === '{{ $report->status }}'">
                <i class="fas {{ $stIcon['icon'] }} {{ $stIcon['cls'] }} text-base flex-shrink-0"></i>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-gray-900 dark:text-white truncate">{{ $report->template->name ?? '—' }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                        {{ $report->project->name ?? '—' }} &middot;
                        {{ $report->period_start->format('d M Y') }} – {{ $report->period_end->format('d M Y') }}
                    </p>
                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">Dibuat {{ $report->created_at->format('d M Y') }}</p>
                </div>
                <div class="flex items-center gap-2 flex-shrink-0">
                    @if($report->status === 'ready')
                    <a href="{{ route('client.compliance-reports.download', $report) }}"
                       class="inline-flex items-center gap-1 text-xs font-semibold px-3 py-1.5 bg-[#0a66c2] hover:bg-[#004182] text-white rounded-xl transition active:scale-95">
                        <i class="fas fa-download text-[9px]"></i> PDF
                    </a>
                    <button type="button" @click="openEmail({{ $report->id }})"
                            class="inline-flex items-center gap-1 text-xs font-semibold px-3 py-1.5 border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-400 hover:border-[#0a66c2] hover:text-[#0a66c2] rounded-xl transition active:scale-95">
                        <i class="fas fa-envelope text-[9px]"></i> Kirim
                    </button>
                    @elseif($report->status === 'generating')
                    <span class="text-xs text-blue-500 font-medium flex items-center gap-1">
                        <i class="fas fa-rotate animate-spin text-[10px]"></i> Diproses…
                    </span>
                    @else
                    <span class="text-xs text-gray-400">—</span>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
        @if($reports->hasPages())
        <div class="px-5 py-3 border-t border-gray-100 dark:border-gray-700">
            {{ $reports->links() }}
        </div>
        @endif
        @endif
    </div>

    {{-- Email modal --}}
    <div x-show="emailModal" x-transition
         style="display:none"
         class="fixed inset-0 z-50 flex items-end sm:items-center justify-center p-4 sm:p-6 bg-black/60 backdrop-blur-sm"
         @click.self="emailModal=false" @keydown.escape.window="emailModal=false">
        <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-2xl w-full max-w-sm">
            <div class="flex items-center justify-between px-4 py-3 border-b border-gray-200 dark:border-gray-700">
                <p class="font-semibold text-sm text-gray-900 dark:text-white flex items-center gap-2">
                    <i class="fas fa-envelope text-[#0a66c2]"></i> Kirim Laporan via Email
                </p>
                <button @click="emailModal=false" class="w-7 h-7 flex items-center justify-center rounded-full hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-500">
                    <i class="fas fa-xmark text-sm"></i>
                </button>
            </div>
            <div class="px-4 py-4 space-y-3">
                <p class="text-xs text-gray-500 dark:text-gray-400">Kirim laporan PDF ke alamat email pihak ketiga (konsultan, auditor, dll).</p>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Alamat Email Tujuan</label>
                    <input type="email" x-model="emailAddr" placeholder="admin@perusahaan.com"
                           class="w-full px-3 py-2.5 text-sm border border-gray-300 dark:border-gray-600 rounded-xl dark:bg-gray-800 dark:text-white focus:ring-2 focus:ring-[#0a66c2] focus:border-[#0a66c2]">
                </div>
            </div>
            <div class="px-4 pb-4 flex gap-2 justify-end">
                <button type="button" @click="emailModal=false"
                        class="px-4 py-2 text-sm text-gray-600 dark:text-gray-400 border border-gray-200 dark:border-gray-700 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                    Batal
                </button>
                <button type="button" @click="sendEmail()"
                        :disabled="!emailAddr || emailSending"
                        :class="(!emailAddr || emailSending) ? 'opacity-50 pointer-events-none' : ''"
                        class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-semibold bg-[#0a66c2] hover:bg-[#004182] text-white rounded-xl transition active:scale-95">
                    <i class="fas fa-paper-plane text-xs" :class="emailSending ? 'animate-pulse' : ''"></i>
                    <span x-text="emailSending ? 'Mengirim…' : 'Kirim'"></span>
                </button>
            </div>
        </div>
    </div>

</div>
@endsection
    <h1 class="page-title">Laporan Compliance</h1>
    <p class="page-sub">Generate laporan UKL-UPL dan dokumen lingkungan dengan AI</p>
</div>

@if(session('success'))
    <div class="alert alert-success mb-3">{{ session('success') }}</div>
@endif

{{-- Quick Template Picker --}}
<div class="client-card">
    <div class="client-card-header">
        Buat Laporan Baru
        <a href="{{ route('client.compliance-reports.create') }}" class="btn-indigo">+ Buat Laporan</a>
    </div>
    <div class="client-card-body">
        <p style="font-size:.875rem;color:#64748b;margin-bottom:1rem">Pilih template laporan yang ingin Anda buat:</p>
        <div class="template-grid">
            @foreach($templates as $tpl)
                <a href="{{ route('client.compliance-reports.create') }}?template={{ $tpl->id }}" style="text-decoration:none">
                    <div class="template-card">
                        <div class="t-name">{{ $tpl->name }}</div>
                        <div class="t-type">{{ \App\Models\ReportTemplate::$typeLabels[$tpl->type] ?? $tpl->type }}</div>
                    </div>
                </a>
            @endforeach
            @if($templates->isEmpty())
                <div style="color:#94a3b8;font-size:.875rem">Belum ada template tersedia. Hubungi admin.</div>
            @endif
        </div>
    </div>
</div>

{{-- Reports History --}}
<div class="client-card">
    <div class="client-card-header">Riwayat Laporan</div>
    @if($reports->isEmpty())
        <div class="client-card-body" style="text-align:center;color:#64748b;padding:2rem">
            Belum ada laporan. Klik <strong>Buat Laporan</strong> untuk memulai.
        </div>
    @else
        <table>
            <thead>
                <tr>
                    <th>Template</th>
                    <th>Proyek</th>
                    <th>Periode</th>
                    <th>Status</th>
                    <th>Dibuat</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
            @foreach($reports as $report)
                <tr>
                    <td style="font-weight:600">{{ $report->template->name }}</td>
                    <td>{{ $report->project->name ?? '—' }}</td>
                    <td style="font-size:.8125rem;color:#64748b">
                        {{ $report->period_start->format('d M Y') }} –<br>
                        {{ $report->period_end->format('d M Y') }}
                    </td>
                    <td>
                        <span class="status-dot" style="background:{{ $report->status_color }}"></span>
                        {{ $report->status_label }}
                        @if($report->status === 'generating')
                            <span style="font-size:.7rem;color:#f59e0b">(dalam antrian)</span>
                        @endif
                    </td>
                    <td style="font-size:.8125rem;color:#64748b">{{ $report->created_at->format('d M Y') }}</td>
                    <td>
                        @if($report->status === 'ready')
                            <a href="{{ route('client.compliance-reports.download', $report) }}" class="btn-secondary">⬇ Unduh PDF</a>
                        @else
                            <span style="font-size:.8rem;color:#94a3b8">—</span>
                        @endif
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
        <div style="padding:1rem">{{ $reports->links() }}</div>
    @endif
</div>
