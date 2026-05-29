{{-- Compliance Reports Index — Portal v2 --}}

{{-- ─── HERO ─── --}}
<section class="portal-hero relative overflow-hidden border-b border-[var(--border-subtle)]"
         style="background: linear-gradient(135deg, #0f4c2e 0%, #062015 100%); color:#fff;"
         aria-label="Laporan Compliance">
    <div class="portal-glow-orb portal-glow-orb--tr hidden lg:block" aria-hidden="true"
         style="background: radial-gradient(circle at 50% 50%, rgba(34,197,94,0.2) 0%, transparent 70%);"></div>

    <div class="relative max-w-[1400px] mx-auto px-4 lg:px-8 py-5 lg:py-7">
        <span class="portal-eyebrow" style="background: rgba(34,197,94,0.15); color: rgba(255,255,255,0.9); border-color: rgba(34,197,94,0.25);">
            <i class="fas fa-file-chart-column text-[9px]" aria-hidden="true"></i>
            AI-Generated Reports
        </span>
        <h1 class="mt-2 text-2xl font-bold text-white">Laporan Compliance</h1>
        <p class="mt-1 text-sm text-white/80">Generate laporan UKL-UPL dan dokumen lingkungan secara otomatis dengan AI.</p>

        <div class="flex items-center gap-3 mt-4">
            <a href="{{ route('client.compliance-reports.create') }}"
               class="inline-flex items-center gap-2 px-4 py-2.5 bg-white text-[#0f4c2e] text-sm font-semibold rounded-lg hover:shadow-md transition-shadow">
                <i class="fas fa-plus text-xs" aria-hidden="true"></i> Buat Laporan Baru
            </a>
        </div>
    </div>
</section>

{{-- ─── MAIN ─── --}}
<div class="max-w-[1400px] mx-auto px-4 lg:px-8 py-6 space-y-6"
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
             } catch(e) {
                 alert('Gagal mengirim. Coba lagi.');
             } finally { this.emailSending = false; }
         }
     }">

    {{-- Alerts --}}
    @if(session('success'))
    <div class="flex items-center gap-2 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-xl px-4 py-3 text-sm text-green-800 dark:text-green-300">
        <i class="fas fa-circle-check text-green-500" aria-hidden="true"></i> {{ session('success') }}
    </div>
    @endif

    {{-- Templates gallery --}}
    @if(isset($templates) && $templates->isNotEmpty())
    <section class="bg-[var(--surface-elevated)] border border-[var(--border-subtle)] rounded-xl overflow-hidden">
        <div class="px-5 py-4 border-b border-[var(--border-subtle)] flex items-center justify-between">
            <h2 class="text-sm font-bold text-[var(--text-primary)] flex items-center gap-2">
                <i class="fas fa-layer-group text-[var(--client-primary)] text-sm" aria-hidden="true"></i>
                Template Laporan
            </h2>
            <a href="{{ route('client.compliance-reports.create') }}"
               class="text-xs font-semibold text-[var(--client-primary)] hover:underline">Lihat semua</a>
        </div>
        <div class="p-4 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
            @foreach($templates->take(6) as $template)
            <a href="{{ route('client.compliance-reports.create', ['template' => $template->id]) }}"
               class="group flex items-start gap-3 p-3 bg-[var(--surface-cool)] border border-[var(--border-subtle)] rounded-lg hover:border-[var(--client-primary)]/40 hover:shadow-sm transition-all">
                <div class="w-8 h-8 rounded-lg bg-green-100 dark:bg-green-900/30 flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-file-lines text-green-600 dark:text-green-400 text-xs" aria-hidden="true"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-xs font-bold text-[var(--text-primary)] group-hover:text-[var(--client-primary)] transition-colors">{{ $template->name }}</p>
                    @if($template->description)
                    <p class="text-[10px] text-[var(--text-tertiary)] mt-0.5 line-clamp-1">{{ $template->description }}</p>
                    @endif
                </div>
            </a>
            @endforeach
        </div>
    </section>
    @endif

    {{-- Reports list --}}
    <section>
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-sm font-bold text-[var(--text-primary)]">Laporan Saya</h2>
            {{-- Status filter chips --}}
            <div class="flex items-center gap-1.5" role="group" aria-label="Filter status">
                @foreach([
                    ['key'=>'all',        'label'=>'Semua'],
                    ['key'=>'completed',  'label'=>'Selesai'],
                    ['key'=>'processing', 'label'=>'Diproses'],
                    ['key'=>'failed',     'label'=>'Gagal'],
                ] as $chip)
                <button type="button" @click="statusFilter = @js($chip['key'])"
                        :class="statusFilter === @js($chip['key']) ? 'bg-[var(--client-primary)] text-white border-[var(--client-primary)]' : 'bg-[var(--surface-cool)] text-[var(--text-secondary)] border-[var(--border-subtle)]'"
                        class="px-2.5 py-1 text-[10px] font-semibold border rounded-full transition-colors">
                    {{ $chip['label'] }}
                </button>
                @endforeach
            </div>
        </div>

        @if($reports->isEmpty())
        <x-ui.empty-state icon="fas fa-file-chart-column" title="Belum ada laporan"
            description="Generate laporan UKL-UPL, compliance check, atau dokumen lingkungan otomatis dengan AI."
            :action="['label' => 'Buat Laporan Pertama', 'url' => route('client.compliance-reports.create')]" />
        @else
        <div class="bg-[var(--surface-elevated)] border border-[var(--border-subtle)] rounded-xl overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-[var(--surface-cool)] border-b border-[var(--border-subtle)]">
                    <tr>
                        <th class="text-left px-4 py-3 text-[11px] font-semibold uppercase tracking-wider text-[var(--text-tertiary)]">Laporan</th>
                        <th class="text-left px-4 py-3 text-[11px] font-semibold uppercase tracking-wider text-[var(--text-tertiary)] hidden md:table-cell">Proyek</th>
                        <th class="text-left px-4 py-3 text-[11px] font-semibold uppercase tracking-wider text-[var(--text-tertiary)]">Status</th>
                        <th class="text-right px-4 py-3 text-[11px] font-semibold uppercase tracking-wider text-[var(--text-tertiary)] hidden sm:table-cell">Tanggal</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[var(--border-subtle)]">
                    @foreach($reports as $report)
                    @php
                        $rStatus = $report->status ?? 'processing';
                        $rBadge  = match($rStatus) {
                            'completed'  => 'text-green-600 dark:text-green-400',
                            'failed'     => 'text-red-600 dark:text-red-400',
                            default      => 'text-amber-600 dark:text-amber-400',
                        };
                        $rLabel  = match($rStatus) {
                            'completed'  => 'Selesai',
                            'failed'     => 'Gagal',
                            default      => 'Diproses',
                        };
                    @endphp
                    <tr class="hover:bg-[var(--surface-cool)] transition-colors"
                        x-show="statusFilter === 'all' || statusFilter === @js($rStatus)">
                        <td class="px-4 py-3">
                            <p class="text-sm font-semibold text-[var(--text-primary)]">
                                {{ $report->template?->name ?? 'Laporan Compliance' }}
                            </p>
                            @if($report->title)
                            <p class="text-xs text-[var(--text-tertiary)]">{{ $report->title }}</p>
                            @endif
                        </td>
                        <td class="px-4 py-3 hidden md:table-cell">
                            <p class="text-xs text-[var(--text-secondary)]">{{ $report->project?->name ?? '—' }}</p>
                        </td>
                        <td class="px-4 py-3">
                            <span class="text-xs font-semibold {{ $rBadge }}">
                                @if($rStatus === 'processing')<i class="fas fa-spinner animate-spin text-[9px]" aria-hidden="true"></i> @endif
                                {{ $rLabel }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right hidden sm:table-cell">
                            <time class="text-xs text-[var(--text-tertiary)]">{{ $report->created_at->format('d M Y') }}</time>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <div class="flex items-center justify-end gap-2">
                                @if($rStatus === 'completed')
                                <a href="{{ route('client.compliance-reports.download', $report->id) }}"
                                   class="text-xs font-semibold text-[var(--client-primary)] hover:underline">
                                    <i class="fas fa-download text-[9px]" aria-hidden="true"></i> Unduh
                                </a>
                                <button type="button" @click="openEmail({{ $report->id }})"
                                        class="text-xs font-semibold text-[var(--text-tertiary)] hover:text-[var(--client-primary)] transition-colors">
                                    <i class="fas fa-envelope text-[9px]" aria-hidden="true"></i> Email
                                </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @if(method_exists($reports, 'links')) {{ $reports->links() }} @endif
        @endif
    </section>
</div>

{{-- ─── EMAIL MODAL ─── --}}
<div x-show="emailModal" x-transition.opacity
     class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm"
     @keydown.escape.window="emailModal = false" style="display:none">
    <div class="w-full max-w-sm bg-[var(--surface-elevated)] rounded-2xl shadow-2xl overflow-hidden"
         @click.outside="emailModal = false">
        <div class="px-5 py-4 border-b border-[var(--border-subtle)] flex items-center justify-between">
            <h3 class="text-sm font-bold text-[var(--text-primary)]">Kirim Laporan via Email</h3>
            <button type="button" @click="emailModal = false" aria-label="Tutup modal" class="text-[var(--text-tertiary)] hover:text-[var(--text-primary)]">
                <i class="fas fa-xmark" aria-hidden="true"></i>
            </button>
        </div>
        <div class="px-5 py-5 space-y-4">
            <input type="email" x-model="emailAddr" placeholder="email@perusahaan.com"
                   class="w-full px-3 py-2.5 bg-[var(--surface-cool)] border border-[var(--border-subtle)] rounded-lg text-sm text-[var(--text-primary)] placeholder-[var(--text-tertiary)] focus:ring-2 focus:ring-[var(--client-primary)]">
            <button type="button" @click="sendEmail()" :disabled="emailSending"
                    class="w-full inline-flex items-center justify-center gap-2 px-4 py-3 bg-[var(--client-primary)] text-white text-sm font-semibold rounded-lg hover:brightness-110 transition-all disabled:opacity-60">
                <i class="fas" :class="emailSending ? 'fa-spinner animate-spin' : 'fa-paper-plane'" aria-hidden="true"></i>
                <span x-text="emailSending ? 'Mengirim…' : 'Kirim'"></span>
            </button>
        </div>
    </div>
</div>
