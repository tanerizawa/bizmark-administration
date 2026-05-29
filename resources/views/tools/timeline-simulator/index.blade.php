@extends('layouts.landing')

@section('title', 'Simulasi Timeline Perizinan — Bizmark')
@section('meta_description', 'Simulasikan total waktu yang dibutuhkan untuk mendapatkan semua izin bisnis Anda. Visualisasi Gantt chart interaktif dengan AI tips.')

@push('styles')
<style>
    .sim-hero { background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%); color: #fff; padding: 3rem 0 2rem; text-align: center; }
    .sim-hero h1 { font-size: clamp(1.5rem, 4vw, 2.5rem); font-weight: 900; margin-bottom: .5rem; }
    .sim-hero p  { opacity: .85; font-size: 1.0625rem; max-width: 560px; margin: 0 auto; }
    .sim-layout { display: grid; grid-template-columns: 340px 1fr; gap: 1.5rem; max-width: 1200px; margin: 2rem auto; padding: 0 1rem; }
    @media(max-width:860px) { .sim-layout { grid-template-columns: 1fr; } }

    .panel { background: #fff; border: 1px solid #e2e8f0; border-radius: 1rem; padding: 1.25rem; }
    .panel-title { font-size: 1rem; font-weight: 700; margin-bottom: 1rem; color: #1e293b; }

    /* Permit selection */
    .permit-search { width:100%; padding:.5rem .75rem; border:1px solid #d1d5db; border-radius:.5rem; margin-bottom:.75rem; font-size:.875rem; }
    .permit-list   { max-height: 380px; overflow-y: auto; }
    .permit-item   { display:flex; align-items:center; gap:.5rem; padding:.4rem .25rem; border-radius:.375rem; cursor:pointer; }
    .permit-item:hover { background: #f8fafc; }
    .permit-item input { accent-color: #6366f1; }
    .permit-item label { font-size:.8125rem; color:#374151; cursor:pointer; line-height:1.35; flex:1; }
    .permit-item .days { font-size:.7rem; color:#94a3b8; white-space:nowrap; }

    .cat-header { font-size:.7rem; font-weight:700; color:#6366f1; text-transform:uppercase; letter-spacing:.05em; padding:.5rem .25rem .25rem; }

    .btn-simulate { width:100%; background:#6366f1; color:#fff; border:none; padding:.75rem; border-radius:.75rem; font-weight:700; font-size:.9375rem; cursor:pointer; margin-top:1rem; transition:background .2s; }
    .btn-simulate:hover { background:#4f46e5; }
    .btn-simulate:disabled { background:#a5b4fc; cursor:not-allowed; }

    /* Results */
    .result-hidden { display: none; }
    .summary-grid  { display:grid; grid-template-columns:repeat(3,1fr); gap:1rem; margin-bottom:1.5rem; }
    .summary-card  { background:#f8fafc; border-radius:.75rem; padding:1rem; text-align:center; }
    .summary-card .num { font-size:1.875rem; font-weight:900; color:#6366f1; }
    .summary-card .lbl { font-size:.75rem; color:#64748b; margin-top:2px; }
    .summary-card.opt .num { color:#10b981; }
    .summary-card.pes .num { color:#f59e0b; }

    /* Gantt */
    .gantt-wrap { overflow-x: auto; }
    .gantt-container { min-width: 600px; }
    .gantt-row  { display:flex; align-items:center; gap:.5rem; margin-bottom:.375rem; }
    .gantt-label { width: 180px; flex-shrink:0; font-size:.8rem; color:#374151; text-align:right; padding-right:.5rem; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
    .gantt-track { flex:1; position:relative; height:28px; background:#f1f5f9; border-radius:4px; }
    .gantt-bar   { position:absolute; top:4px; bottom:4px; border-radius:4px; display:flex; align-items:center; justify-content:center; font-size:.7rem; color:#fff; font-weight:600; min-width:2px; transition:all .3s; }
    .gantt-day-markers { display:flex; margin-bottom:.25rem; }
    .gantt-day-markers .gantt-label { /* spacer */ }
    .gantt-day-labels { flex:1; display:flex; position:relative; height:18px; }
    .gantt-day-tick { position:absolute; font-size:.65rem; color:#94a3b8; transform:translateX(-50%); }

    /* AI Tips */
    .tips-list { list-style:none; padding:0; }
    .tip-item  { display:flex; gap:.75rem; padding:.625rem 0; border-bottom:1px solid #f1f5f9; align-items:flex-start; }
    .tip-item:last-child { border-bottom:none; }
    .tip-badge { flex-shrink:0; padding:2px 8px; border-radius:9999px; font-size:.7rem; font-weight:700; }
    .tip-tinggi  { background:#fee2e2; color:#991b1b; }
    .tip-sedang  { background:#fef9c3; color:#854d0e; }
    .tip-rendah  { background:#f1f5f9; color:#475569; }
    .tip-text  { font-size:.875rem; color:#374151; }

    .loading-spinner { text-align:center; padding:2rem; color:#6366f1; font-size:.875rem; }
    .spinner { width:32px; height:32px; border:3px solid #e0e7ff; border-top-color:#6366f1; border-radius:50%; animation:spin .7s linear infinite; margin:0 auto .5rem; }
    @keyframes spin { to { transform:rotate(360deg); } }

    .cta-box { background:linear-gradient(135deg,#6366f1,#8b5cf6); color:#fff; border-radius:1rem; padding:1.5rem; text-align:center; margin-top:1.5rem; }
    .cta-box h3 { font-weight:800; font-size:1.125rem; margin-bottom:.5rem; }
    .cta-box .btn { background:#fff; color:#6366f1; font-weight:700; padding:.6rem 1.5rem; border-radius:.625rem; text-decoration:none; display:inline-block; margin-top:.5rem; }
</style>
@endpush

@section('content')
<div class="sim-hero">
    <h1>Simulasi Timeline Perizinan</h1>
    <p>Pilih izin yang Anda butuhkan dan lihat estimasi total waktu + Gantt chart interaktif.</p>
</div>

<div class="sim-layout" x-data="simulator()">
    {{-- Left: Permit Picker --}}
    <div>
        <div class="panel">
            <div class="panel-title">Pilih Izin yang Dibutuhkan</div>
            <input type="text" class="permit-search" placeholder="Cari nama izin..." x-model="search" @input="filterPermits()">

            <div class="permit-list" id="permitList">
                @php
                    $grouped = $permitTypes->groupBy('category');
                @endphp
                @foreach($grouped as $category => $items)
                    <div class="cat-header" x-show="hasCategoryItems('{{ $category }}')">{{ $category ?: 'Umum' }}</div>
                    @foreach($items as $permit)
                    <div class="permit-item" data-name="{{ strtolower($permit->name) }}" data-cat="{{ strtolower($category) }}">
                        <input type="checkbox"
                            id="permit-{{ $permit->id }}"
                            value="{{ $permit->id }}"
                            x-model="selectedIds"
                            @change="onSelect">
                        <label for="permit-{{ $permit->id }}">{{ $permit->name }}</label>
                        @if($permit->typical_duration_days)
                            <span class="days">~{{ $permit->typical_duration_days }}h</span>
                        @endif
                    </div>
                    @endforeach
                @endforeach
            </div>

            <div style="font-size:.8rem;color:#64748b;margin-top:.75rem">
                <span x-text="selectedIds.length"></span> izin dipilih
            </div>

            <button class="btn-simulate"
                :disabled="selectedIds.length === 0 || loading"
                @click="runSimulation">
                <span x-show="!loading">▶ Simulasikan Timeline</span>
                <span x-show="loading">Menghitung...</span>
            </button>
        </div>
    </div>

    {{-- Right: Results --}}
    <div>
        {{-- Loading --}}
        <div x-show="loading" class="panel loading-spinner">
            <div class="spinner"></div>
            AI sedang menganalisis dependensi dan menghitung timeline...
        </div>

        {{-- Results --}}
        <template x-if="result && !loading">
            <div>
                {{-- Summary Cards --}}
                <div class="summary-grid">
                    <div class="summary-card">
                        <div class="num" x-text="result.total_days"></div>
                        <div class="lbl">Estimasi Normal (hari)</div>
                    </div>
                    <div class="summary-card opt">
                        <div class="num" x-text="result.optimistic_days"></div>
                        <div class="lbl">Optimistis (hari)</div>
                    </div>
                    <div class="summary-card pes">
                        <div class="num" x-text="result.pessimistic_days"></div>
                        <div class="lbl">Pesimistis (hari)</div>
                    </div>
                </div>

                {{-- Gantt --}}
                <div class="panel" style="margin-bottom:1rem">
                    <div class="panel-title">Gantt Chart Timeline</div>
                    <div class="gantt-wrap">
                        <div class="gantt-container" :style="`min-width:${Math.max(600, result.total_days * 3 + 200)}px`">
                            {{-- Day markers --}}
                            <div class="gantt-day-markers">
                                <div class="gantt-label"></div>
                                <div class="gantt-day-labels" :style="`flex:1;height:18px;position:relative`">
                                    <template x-for="tick in dayTicks" :key="tick">
                                        <span class="gantt-day-tick" :style="`left:${(tick/result.total_days)*100}%`" x-text="tick + 'h'"></span>
                                    </template>
                                </div>
                            </div>
                            {{-- Bars --}}
                            <template x-for="(bar, i) in result.gantt" :key="i">
                                <div class="gantt-row">
                                    <div class="gantt-label" :title="bar.label" x-text="bar.label"></div>
                                    <div class="gantt-track">
                                        <div class="gantt-bar"
                                            :style="`left:${(bar.start/result.total_days)*100}%;width:${Math.max(1,(bar.duration/result.total_days)*100)}%;background:${bar.color}`"
                                            :title="`${bar.label}: ${bar.start}–${bar.end} hari`">
                                            <span x-show="bar.duration > 5" x-text="bar.duration + 'h'"></span>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                    <div style="font-size:.75rem;color:#94a3b8;margin-top:.5rem">h = hari kerja estimasi</div>
                </div>

                {{-- AI Tips --}}
                <div class="panel" x-show="result.ai_tips && result.ai_tips.length > 0">
                    <div class="panel-title">💡 Tips AI untuk Mempercepat Proses</div>
                    <ul class="tips-list">
                        <template x-for="(tip, i) in result.ai_tips" :key="i">
                            <li class="tip-item">
                                <span class="tip-badge" :class="'tip-' + (tip.impact || 'rendah').toLowerCase()" x-text="tip.impact || 'info'"></span>
                                <span class="tip-text" x-text="tip.tip"></span>
                            </li>
                        </template>
                    </ul>
                </div>

                {{-- CTA --}}
                <div class="cta-box">
                    <h3>Siap memulai proses perizinan?</h3>
                    <p style="opacity:.85;font-size:.9375rem">Tim konsultan Bizmark siap membantu dari awal hingga izin terbit.</p>
                    <a href="{{ url('/konsultasi') }}" class="btn">Konsultasi Gratis Sekarang →</a>
                </div>
            </div>
        </template>

        {{-- Empty state --}}
        <template x-if="!result && !loading">
            <div class="panel" style="text-align:center;padding:3rem;color:#94a3b8">
                <div style="font-size:3rem;margin-bottom:.75rem">📋</div>
                <div style="font-weight:600;color:#475569;margin-bottom:.25rem">Pilih izin di kiri</div>
                <div style="font-size:.875rem">Pilih minimal 1 jenis izin lalu klik Simulasikan Timeline</div>
            </div>
        </template>
    </div>
</div>
@endsection

@push('scripts')
<script>
function simulator() {
    return {
        selectedIds: [],
        search: '',
        loading: false,
        result: null,

        get dayTicks() {
            if (!this.result || this.result.total_days <= 0) return [];
            const total = this.result.total_days;
            const step  = total <= 30 ? 5 : total <= 90 ? 15 : total <= 180 ? 30 : 60;
            const ticks = [];
            for (let d = step; d < total; d += step) ticks.push(d);
            ticks.push(total);
            return ticks;
        },

        filterPermits() {
            const q = this.search.toLowerCase();
            document.querySelectorAll('.permit-item').forEach(el => {
                const name = el.dataset.name || '';
                const cat  = el.dataset.cat || '';
                el.style.display = (!q || name.includes(q) || cat.includes(q)) ? '' : 'none';
            });
        },

        hasCategoryItems(cat) {
            const q = this.search.toLowerCase();
            if (!q) return true;
            const items = document.querySelectorAll(`.permit-item[data-cat="${cat.toLowerCase()}"]`);
            return [...items].some(el => el.style.display !== 'none');
        },

        onSelect() {
            // nothing extra needed
        },

        async runSimulation() {
            if (this.selectedIds.length === 0) return;
            this.loading = true;
            this.result  = null;

            try {
                const res = await fetch('{{ route("timeline-simulator.simulate") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept':       'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify({ permit_ids: this.selectedIds.map(Number) }),
                });
                this.result = await res.json();
            } catch (e) {
                alert('Gagal menghitung timeline. Coba lagi.');
            } finally {
                this.loading = false;
            }
        },
    }
}
</script>
@endpush
