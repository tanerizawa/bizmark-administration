{{-- ══════════════════════════════════════════════════════════
     HERO
══════════════════════════════════════════════════════════ --}}
<section class="portal-hero relative overflow-hidden border-b border-[var(--border-subtle)]"
         style="background: linear-gradient(135deg, #064e3b 0%, color-mix(in oklab, #064e3b 55%, #001020) 100%); color:#fff;">
    <div class="portal-glow-orb portal-glow-orb--tr hidden lg:block" aria-hidden="true"
         style="background: radial-gradient(circle at 50% 50%, rgba(255,255,255,0.13) 0%, transparent 70%);"></div>
    <div class="max-w-[1400px] mx-auto px-4 lg:px-8 py-5 relative z-10">
        <nav class="flex items-center gap-1.5 text-[11px] mb-3 flex-wrap" aria-label="Breadcrumb">
            <a href="{{ route('client.compliance-reports.index') }}" class="opacity-70 hover:opacity-100 transition-opacity">Laporan Compliance</a>
            <i class="fas fa-chevron-right text-[9px] opacity-50"></i>
            <span class="opacity-90 font-semibold">Buat Laporan</span>
        </nav>
        <div class="flex items-start gap-4">
            <div class="hidden sm:flex w-12 h-12 rounded-xl items-center justify-center flex-shrink-0"
                 style="background: rgba(255,255,255,0.15); border: 1px solid rgba(255,255,255,0.2);">
                <i class="fas fa-wand-magic-sparkles text-lg"></i>
            </div>
            <div>
                <h1 class="text-xl sm:text-2xl font-bold leading-snug">Buat Laporan Compliance</h1>
                <p class="text-sm opacity-80 mt-0.5">AI akan generate laporan PDF secara otomatis dari parameter yang Anda isi.</p>
            </div>
        </div>
    </div>
</section>

{{-- ══════════════════════════════════════════════════════════
     FORM
══════════════════════════════════════════════════════════ --}}
<div class="max-w-[860px] mx-auto px-4 lg:px-8 py-6" x-data="reportForm()">

    @if($errors->any())
    <div class="flex items-start gap-2 mb-5 px-4 py-3 rounded-xl text-sm"
         style="background: rgba(239,68,68,0.08); border: 1px solid rgba(239,68,68,0.2); color: #b91c1c;">
        <i class="fas fa-circle-xmark mt-0.5 flex-shrink-0"></i>{{ $errors->first() }}
    </div>
    @endif

    <form method="POST" action="{{ route('client.compliance-reports.store') }}" @submit.prevent="submitForm" x-ref="form">
        @csrf
        <div class="space-y-4">

            {{-- Step 1: Template --}}
            <div class="rounded-2xl border overflow-hidden" style="background: var(--surface-elevated); border-color: var(--border-subtle);">
                <div class="px-5 py-4 border-b flex items-center gap-3" style="border-color: var(--border-subtle);">
                    <div class="w-6 h-6 rounded-full flex items-center justify-center text-[10px] font-bold text-white flex-shrink-0"
                         style="background: var(--client-primary);">1</div>
                    <h3 class="text-sm font-bold" style="color: var(--text-primary);">Pilih Template</h3>
                </div>
                <div class="px-5 py-4">
                    <label class="block text-xs font-semibold mb-1.5" style="color: var(--text-secondary);">
                        Template Laporan <span style="color: var(--apple-red)">*</span>
                    </label>
                    <select name="template_id" x-model="templateId" @change="loadFields" required
                            class="w-full px-3 py-2.5 text-sm rounded-lg border transition-all focus:outline-none appearance-none"
                            style="background: var(--surface-cool); border-color: var(--border-subtle); color: var(--text-primary);"
                            onfocus="this.style.borderColor='var(--client-primary)'; this.style.boxShadow='0 0 0 3px rgba(10,102,194,0.12)';"
                            onblur="this.style.borderColor=''; this.style.boxShadow='';">
                        <option value="">— Pilih Template —</option>
                        @foreach($templates as $tpl)
                            <option value="{{ $tpl->id }}" @selected(request('template') == $tpl->id)>
                                {{ $tpl->name }} ({{ \App\Models\ReportTemplate::$typeLabels[$tpl->type] ?? $tpl->type }})
                                @if($tpl->regulatory_basis) — {{ $tpl->regulatory_basis }}@endif
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Step 2: Proyek & Periode --}}
            <div class="rounded-2xl border overflow-hidden" style="background: var(--surface-elevated); border-color: var(--border-subtle);">
                <div class="px-5 py-4 border-b flex items-center gap-3" style="border-color: var(--border-subtle);">
                    <div class="w-6 h-6 rounded-full flex items-center justify-center text-[10px] font-bold text-white flex-shrink-0"
                         style="background: var(--client-primary);">2</div>
                    <h3 class="text-sm font-bold" style="color: var(--text-primary);">Proyek &amp; Periode</h3>
                </div>
                <div class="px-5 py-4 space-y-4">
                    <div>
                        <label class="block text-xs font-semibold mb-1.5" style="color: var(--text-secondary);">
                            Proyek <span style="color: var(--apple-red)">*</span>
                        </label>
                        <select name="project_id" required
                                class="w-full px-3 py-2.5 text-sm rounded-lg border transition-all focus:outline-none appearance-none"
                                style="background: var(--surface-cool); border-color: var(--border-subtle); color: var(--text-primary);"
                                onfocus="this.style.borderColor='var(--client-primary)'; this.style.boxShadow='0 0 0 3px rgba(10,102,194,0.12)';"
                                onblur="this.style.borderColor=''; this.style.boxShadow='';">
                            <option value="">— Pilih Proyek —</option>
                            @foreach($projects as $project)
                                <option value="{{ $project->id }}">{{ $project->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold mb-1.5" style="color: var(--text-secondary);">
                                Tanggal Mulai <span style="color: var(--apple-red)">*</span>
                            </label>
                            <input type="date" name="period_start" required
                                   class="w-full px-3 py-2.5 text-sm rounded-lg border transition-all focus:outline-none"
                                   style="background: var(--surface-cool); border-color: var(--border-subtle); color: var(--text-primary);"
                                   onfocus="this.style.borderColor='var(--client-primary)'; this.style.boxShadow='0 0 0 3px rgba(10,102,194,0.12)';"
                                   onblur="this.style.borderColor=''; this.style.boxShadow='';">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold mb-1.5" style="color: var(--text-secondary);">
                                Tanggal Akhir <span style="color: var(--apple-red)">*</span>
                            </label>
                            <input type="date" name="period_end" required
                                   class="w-full px-3 py-2.5 text-sm rounded-lg border transition-all focus:outline-none"
                                   style="background: var(--surface-cool); border-color: var(--border-subtle); color: var(--text-primary);"
                                   onfocus="this.style.borderColor='var(--client-primary)'; this.style.boxShadow='0 0 0 3px rgba(10,102,194,0.12)';"
                                   onblur="this.style.borderColor=''; this.style.boxShadow='';">
                        </div>
                    </div>
                </div>
            </div>

            {{-- Step 3: Dynamic Parameters --}}
            <div x-show="fields.length > 0" x-collapse
                 class="rounded-2xl border overflow-hidden" style="background: var(--surface-elevated); border-color: var(--border-subtle);">
                <div class="px-5 py-4 border-b flex items-center gap-3" style="border-color: var(--border-subtle);">
                    <div class="w-6 h-6 rounded-full flex items-center justify-center text-[10px] font-bold text-white flex-shrink-0"
                         style="background: var(--client-primary);">3</div>
                    <h3 class="text-sm font-bold" style="color: var(--text-primary);">Parameter Lapangan</h3>
                </div>
                <div class="px-5 py-4">
                    <p class="text-xs mb-4" style="color: var(--text-secondary);">
                        Isi data parameter sesuai kondisi lapangan untuk periode yang dipilih.
                    </p>
                    <div class="space-y-4">
                        <template x-for="field in fields" :key="field.key">
                            <div>
                                <label class="block text-xs font-semibold mb-1.5"
                                       style="color: var(--text-secondary);"
                                       x-text="field.label + (field.unit ? ' (' + field.unit + ')' : '') + (field.required ? ' *' : '')"></label>
                                <template x-if="field.type === 'textarea'">
                                    <textarea :name="`input_data[${field.key}]`" rows="3"
                                              :placeholder="field.placeholder || ''" :required="field.required"
                                              class="w-full px-3 py-2.5 text-sm rounded-lg border transition-all focus:outline-none resize-none"
                                              style="background: var(--surface-cool); border-color: var(--border-subtle); color: var(--text-primary);"
                                              @focus="$el.style.borderColor='var(--client-primary)'; $el.style.boxShadow='0 0 0 3px rgba(10,102,194,0.12)'"
                                              @blur="$el.style.borderColor=''; $el.style.boxShadow=''"></textarea>
                                </template>
                                <template x-if="field.type !== 'textarea'">
                                    <input :type="field.type || 'text'" :name="`input_data[${field.key}]`"
                                           :placeholder="field.placeholder || ''" :required="field.required"
                                           class="w-full px-3 py-2.5 text-sm rounded-lg border transition-all focus:outline-none"
                                           style="background: var(--surface-cool); border-color: var(--border-subtle); color: var(--text-primary);"
                                           @focus="$el.style.borderColor='var(--client-primary)'; $el.style.boxShadow='0 0 0 3px rgba(10,102,194,0.12)'"
                                           @blur="$el.style.borderColor=''; $el.style.boxShadow=''">
                                </template>
                                <p x-show="field.help" x-text="field.help" class="text-xs mt-1" style="color: var(--text-tertiary);"></p>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </div>

        {{-- Submit --}}
        <div class="flex justify-end mt-5">
            <button type="submit"
                    :disabled="!templateId || submitting"
                    :class="(!templateId || submitting) ? 'opacity-50 pointer-events-none' : 'hover:opacity-90'"
                    class="inline-flex items-center gap-2 px-6 py-3 rounded-xl text-sm font-semibold text-white transition-all"
                    style="background: var(--client-primary);">
                <i class="fas fa-wand-magic-sparkles text-xs"></i>
                <span x-text="submitting ? 'Generating…' : 'Generate Laporan dengan AI'"></span>
            </button>
        </div>
    </form>
</div>

{{-- Generating progress modal --}}
<div x-show="$store.reportProgress?.active" x-transition
     style="display:none"
     class="fixed inset-0 z-50 flex items-center justify-center p-6"
     style="background: rgba(0,0,0,0.7); backdrop-filter: blur(4px);">
    <div class="rounded-2xl shadow-2xl max-w-sm w-full px-6 py-7 text-center"
         style="background: var(--surface-elevated); border: 1px solid var(--border-subtle);">
        <div class="w-14 h-14 rounded-full flex items-center justify-center mx-auto mb-4"
             style="background: rgba(10,102,194,0.1);">
            <i class="fas fa-circle-notch fa-spin text-2xl" style="color: var(--client-primary);"></i>
        </div>
        <h3 class="text-base font-bold mb-2" style="color: var(--text-primary);">Generating Laporan…</h3>
        <p class="text-sm mb-4" style="color: var(--text-secondary);">AI sedang memproses data Anda. Ini mungkin membutuhkan beberapa menit.</p>
        <div class="w-full rounded-full h-1.5 mb-3 overflow-hidden" style="background: var(--surface-cool);">
            <div class="h-full rounded-full" style="background: var(--client-primary); animation: report-progress 1.8s ease-in-out infinite; width: 60%;"></div>
        </div>
        <p class="text-xs" style="color: var(--text-tertiary);">Halaman akan otomatis di-update saat selesai</p>
    </div>
</div>

@push('styles')
<style>
@keyframes report-progress {
    0%   { transform: translateX(-100%); width: 60%; }
    50%  { transform: translateX(60%); width: 80%; }
    100% { transform: translateX(200%); width: 60%; }
}
</style>
@endpush

@push('scripts')
<script>
function reportForm() {
    return {
        templateId: '{{ request("template") }}',
        fields: [],
        submitting: false,

        async loadFields() {
            if (!this.templateId) { this.fields = []; return; }
            try {
                const res = await fetch(`/client/compliance-reports/template-params/${this.templateId}`);
                this.fields = await res.json();
            } catch { this.fields = []; }
        },

        async submitForm() {
            if (!this.templateId || this.submitting) return;
            this.submitting = true;
            Alpine.store('reportProgress', { active: true });
            this.$refs.form.submit();
        },

        async init() {
            if (!Alpine.store('reportProgress')) Alpine.store('reportProgress', { active: false });
            if (this.templateId) await this.loadFields();
        },
    }
}
</script>
@endpush
