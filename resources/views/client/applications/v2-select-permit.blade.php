{{-- Select Permit — Portal v2 --}}

{{-- ─── HERO ─── --}}
<section class="portal-hero relative overflow-hidden border-b border-[var(--border-subtle)]"
         style="background: linear-gradient(135deg, var(--client-primary) 0%, color-mix(in oklab, var(--client-primary) 55%, #001020) 100%); color:#fff;"
         aria-label="Pilih Jenis Izin">
    <div class="portal-glow-orb portal-glow-orb--tr hidden lg:block" aria-hidden="true"
         style="background: radial-gradient(circle at 50% 50%, rgba(255,255,255,0.16) 0%, transparent 70%);"></div>
    <div class="relative max-w-[1400px] mx-auto px-4 lg:px-8 py-5 lg:py-7">
        {{-- Breadcrumb --}}
        <nav class="flex items-center gap-1.5 text-[11px] text-white/60 mb-3" aria-label="Breadcrumb">
            <a href="{{ route('client.services.index') }}" class="hover:text-white transition-colors">Katalog</a>
            <i class="fas fa-chevron-right text-[8px]" aria-hidden="true"></i>
            <a href="{{ route('client.services.show', $kbli->code) }}" class="hover:text-white transition-colors max-w-[180px] truncate">{{ $kbli->description }}</a>
            <i class="fas fa-chevron-right text-[8px]" aria-hidden="true"></i>
            <span class="text-white/90">Pilih Izin</span>
        </nav>

        <span class="portal-eyebrow" style="background: rgba(255,255,255,0.15); color: rgba(255,255,255,0.9); border-color: rgba(255,255,255,0.2);">
            <i class="fas fa-clipboard-list text-[9px]" aria-hidden="true"></i>
            KBLI {{ $kbli->code }}
        </span>
        <h1 class="mt-2 text-xl font-bold text-white">Pilih Izin yang Akan Diajukan</h1>
        <p class="mt-1 text-sm text-white/80 max-w-2xl">{{ $kbli->description }}</p>
    </div>
</section>

{{-- ─── MAIN ─── --}}
<div class="max-w-[1400px] mx-auto px-4 lg:px-8 py-6"
     x-data="permitSelection()">

    <form action="{{ route('client.applications.select-permits') }}" method="POST">
        @csrf
        <input type="hidden" name="kbli_code" value="{{ $kbli->code }}">
        <input type="hidden" name="kbli_description" value="{{ $kbli->description }}">

        @error('permits') <div class="mb-4 text-sm text-red-600 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-700 rounded-xl px-4 py-3">{{ $message }}</div> @enderror

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Left: Permit list --}}
            <div class="lg:col-span-2 space-y-5">

                @if($recommendation && !empty($recommendation->recommended_permits))
                <section>
                    <div class="flex items-center justify-between mb-3">
                        <h2 class="text-sm font-bold text-[var(--text-primary)] flex items-center gap-2">
                            <i class="fas fa-star text-amber-500 text-xs" aria-hidden="true"></i>
                            Izin Direkomendasikan
                            <span class="text-[10px] text-[var(--text-tertiary)] font-normal">({{ count($recommendation->recommended_permits) }})</span>
                        </h2>
                        <div class="flex items-center gap-3">
                            <button type="button" @click="selectAll()" class="text-[11px] text-[var(--client-primary)] hover:underline">Pilih Semua</button>
                            <button type="button" @click="deselectAll()" class="text-[11px] text-[var(--text-tertiary)] hover:underline">Hapus Semua</button>
                        </div>
                    </div>
                    <div class="space-y-2">
                        @foreach($recommendation->recommended_permits as $index => $permit)
                        @php $isMandatory = ($permit['type'] ?? '') === 'mandatory'; @endphp
                        <label class="flex items-start gap-3 p-4 bg-[var(--surface-elevated)] border-2 rounded-xl cursor-pointer transition-all portal-lift"
                               :class="selectedPermits.includes({{ $index }}) ? 'border-[var(--client-primary)] bg-[var(--client-primary)]/5' : 'border-[var(--border-subtle)]'">
                            <input type="checkbox" name="selected_permits[]" value="{{ $index }}"
                                   :checked="selectedPermits.includes({{ $index }})"
                                   @change="togglePermit({{ $index }})"
                                   class="mt-0.5 w-4 h-4 accent-[var(--client-primary)] flex-shrink-0">
                            <div class="flex-1 min-w-0">
                                <div class="flex items-start gap-2 flex-wrap">
                                    <span class="text-sm font-semibold text-[var(--text-primary)]">{{ $permit['name'] }}</span>
                                    @if($isMandatory)
                                    <span class="text-[9px] font-bold px-1.5 py-0.5 bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300 rounded uppercase tracking-wider">Wajib</span>
                                    @endif
                                </div>
                                <p class="text-xs text-[var(--text-tertiary)] mt-0.5">{{ $permit['description'] ?? '' }}</p>
                                <div class="flex flex-wrap gap-3 mt-2 text-[11px] text-[var(--text-secondary)]">
                                    @if(isset($permit['estimated_cost_range']['min']))
                                    <span><i class="fas fa-money-bill-wave text-[9px] mr-1" aria-hidden="true"></i>Rp {{ number_format($permit['estimated_cost_range']['min'], 0, ',', '.') }}</span>
                                    @endif
                                    <span><i class="fas fa-clock text-[9px] mr-1" aria-hidden="true"></i>{{ $permit['estimated_days'] ?? 'N/A' }} hari</span>
                                    @if(!empty($permit['issuing_authority']))
                                    <span><i class="fas fa-building text-[9px] mr-1" aria-hidden="true"></i>{{ $permit['issuing_authority'] }}</span>
                                    @endif
                                </div>
                            </div>
                        </label>
                        @endforeach
                    </div>
                </section>
                @endif

                @if(isset($permitTypes) && $permitTypes->isNotEmpty())
                <section>
                    <h2 class="text-sm font-bold text-[var(--text-primary)] mb-3">Semua Jenis Izin Tersedia</h2>
                    <div class="space-y-2">
                        @foreach($permitTypes as $pt)
                        <label class="flex items-start gap-3 p-4 bg-[var(--surface-elevated)] border-2 border-[var(--border-subtle)] rounded-xl cursor-pointer hover:border-[var(--client-primary)]/50 transition-all">
                            <input type="checkbox" name="permit_type_ids[]" value="{{ $pt->id }}"
                                   class="mt-0.5 w-4 h-4 accent-[var(--client-primary)] flex-shrink-0">
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold text-[var(--text-primary)]">{{ $pt->name }}</p>
                                @if($pt->description)
                                <p class="text-xs text-[var(--text-tertiary)] mt-0.5">{{ $pt->description }}</p>
                                @endif
                            </div>
                        </label>
                        @endforeach
                    </div>
                </section>
                @endif
            </div>

            {{-- Right: Summary sticky --}}
            <div class="space-y-4">
                <div class="sticky top-20 bg-[var(--surface-elevated)] border border-[var(--border-subtle)] rounded-xl p-5 space-y-4">
                    <h3 class="text-sm font-bold text-[var(--text-primary)]">Ringkasan Pilihan</h3>
                    <div class="text-center py-4">
                        <p class="text-3xl font-extrabold tabular-nums text-[var(--client-primary)]" x-text="selectedPermits.length"></p>
                        <p class="text-xs text-[var(--text-tertiary)] mt-1">Izin dipilih</p>
                    </div>
                    <p class="text-[11px] text-[var(--text-tertiary)] text-center">
                        Pilih minimal 1 izin untuk melanjutkan. Anda dapat memilih beberapa izin sekaligus.
                    </p>
                    <button type="submit"
                            :disabled="selectedPermits.length === 0"
                            class="w-full inline-flex items-center justify-center gap-2 px-4 py-3 bg-[var(--client-primary)] text-white text-sm font-semibold rounded-xl hover:brightness-110 disabled:opacity-40 disabled:cursor-not-allowed transition-all">
                        <i class="fas fa-arrow-right text-xs" aria-hidden="true"></i>
                        Lanjutkan
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
function permitSelection() {
    return {
        selectedPermits: [],
        togglePermit(idx) {
            const i = this.selectedPermits.indexOf(idx);
            if (i > -1) this.selectedPermits.splice(i, 1);
            else this.selectedPermits.push(idx);
        },
        selectAll() {
            @php
                $count = isset($recommendation) && !empty($recommendation->recommended_permits) ? count($recommendation->recommended_permits) : 0;
            @endphp
            this.selectedPermits = Array.from({length: {{ $count }}}, (_, i) => i);
        },
        deselectAll() { this.selectedPermits = []; }
    };
}
</script>
@endpush
