{{-- Hero --}}
<div class="client-hero px-4 sm:px-6 py-6">
    <div class="max-w-3xl mx-auto">
        <a href="{{ route('client.compliance-reports.index') }}"
           class="inline-flex items-center gap-2 text-white/80 hover:text-white text-sm mb-4 -ml-1 px-1 active:scale-95 transition">
            <i class="fas fa-arrow-left text-xs"></i> Kembali
        </a>
        <div class="flex items-center gap-3 mb-1">
            <i class="fas fa-wand-magic-sparkles text-xl opacity-90"></i>
            <h1 class="text-2xl font-bold tracking-tight">Buat Laporan Compliance</h1>
        </div>
        <p class="text-white/80 text-sm">Isi parameter di bawah ini — AI akan generate laporan PDF secara otomatis.</p>
    </div>
</div>

<div class="max-w-3xl mx-auto px-4 sm:px-6 py-5">

@if($errors->any())
<div class="flex items-center gap-2 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl px-4 py-3 text-sm text-red-700 dark:text-red-300 mb-4">
    <i class="fas fa-circle-xmark text-red-500 flex-shrink-0"></i> {{ $errors->first() }}
</div>
@endif

<form method="POST" action="{{ route('client.compliance-reports.store') }}"
      x-data="reportForm()"
      @submit.prevent="submitForm">
    @csrf

    <div class="space-y-4">
        {{-- Step 1: Template --}}
        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl overflow-hidden">
            <div class="px-4 sm:px-5 py-4 border-b border-gray-100 dark:border-gray-700 flex items-center gap-3">
                <div class="w-6 h-6 rounded-full bg-[#0a66c2] text-white flex items-center justify-center text-xs font-bold flex-shrink-0">1</div>
                <h3 class="text-sm font-bold text-gray-900 dark:text-white">Pilih Template</h3>
            </div>
            <div class="px-4 sm:px-5 py-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Template Laporan <span class="text-red-500">*</span></label>
                <select name="template_id" x-model="templateId" @change="loadFields" required
                        class="w-full px-3 py-2.5 text-sm border border-gray-300 dark:border-gray-600 rounded-xl dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-[#0a66c2] focus:border-[#0a66c2]">
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
        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl overflow-hidden">
            <div class="px-4 sm:px-5 py-4 border-b border-gray-100 dark:border-gray-700 flex items-center gap-3">
                <div class="w-6 h-6 rounded-full bg-[#0a66c2] text-white flex items-center justify-center text-xs font-bold flex-shrink-0">2</div>
                <h3 class="text-sm font-bold text-gray-900 dark:text-white">Proyek & Periode</h3>
            </div>
            <div class="px-4 sm:px-5 py-4 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Proyek <span class="text-red-500">*</span></label>
                    <select name="project_id" required
                            class="w-full px-3 py-2.5 text-sm border border-gray-300 dark:border-gray-600 rounded-xl dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-[#0a66c2] focus:border-[#0a66c2]">
                        <option value="">— Pilih Proyek —</option>
                        @foreach($projects as $project)
                            <option value="{{ $project->id }}">{{ $project->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Tanggal Mulai <span class="text-red-500">*</span></label>
                        <input type="date" name="period_start" required
                               class="w-full px-3 py-2.5 text-sm border border-gray-300 dark:border-gray-600 rounded-xl dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-[#0a66c2] focus:border-[#0a66c2]">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Tanggal Akhir <span class="text-red-500">*</span></label>
                        <input type="date" name="period_end" required
                               class="w-full px-3 py-2.5 text-sm border border-gray-300 dark:border-gray-600 rounded-xl dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-[#0a66c2] focus:border-[#0a66c2]">
                    </div>
                </div>
            </div>
        </div>

        {{-- Step 3: Dynamic Parameters --}}
        <div x-show="fields.length > 0" x-collapse
             class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl overflow-hidden">
            <div class="px-4 sm:px-5 py-4 border-b border-gray-100 dark:border-gray-700 flex items-center gap-3">
                <div class="w-6 h-6 rounded-full bg-[#0a66c2] text-white flex items-center justify-center text-xs font-bold flex-shrink-0">3</div>
                <h3 class="text-sm font-bold text-gray-900 dark:text-white">Parameter Lingkungan</h3>
            </div>
            <div class="px-4 sm:px-5 py-4">
                <p class="text-xs text-gray-500 dark:text-gray-400 mb-4">Isi data parameter sesuai kondisi lapangan untuk periode yang dipilih.</p>
                <div class="space-y-4">
                    <template x-for="field in fields" :key="field.key">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5"
                                   x-text="field.label + (field.unit ? ' (' + field.unit + ')' : '') + (field.required ? ' *' : '')"></label>
                            <template x-if="field.type === 'textarea'">
                                <textarea :name="`input_data[${field.key}]`" rows="3" :placeholder="field.placeholder || ''" :required="field.required"
                                          class="w-full px-3 py-2.5 text-sm border border-gray-300 dark:border-gray-600 rounded-xl dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-[#0a66c2] focus:border-[#0a66c2]"></textarea>
                            </template>
                            <template x-if="field.type !== 'textarea'">
                                <input :type="field.type || 'text'" :name="`input_data[${field.key}]`" :placeholder="field.placeholder || ''" :required="field.required"
                                       class="w-full px-3 py-2.5 text-sm border border-gray-300 dark:border-gray-600 rounded-xl dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-[#0a66c2] focus:border-[#0a66c2]">
                            </template>
                            <p x-show="field.help" x-text="field.help" class="text-xs text-gray-400 dark:text-gray-500 mt-1"></p>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>

    {{-- Submit --}}
    <div class="flex justify-end mt-4">
        <button type="submit"
                :disabled="!templateId"
                :class="!templateId ? 'opacity-50 pointer-events-none' : ''"
                class="inline-flex items-center gap-2 px-6 py-3 bg-[#0a66c2] hover:bg-[#004182] text-white font-semibold text-sm rounded-xl transition active:scale-95">
            <i class="fas fa-wand-magic-sparkles text-xs"></i> Generate Laporan dengan AI
        </button>
    </div>
</form>

{{-- Generating progress modal --}}
<div x-show="$store.reportProgress?.active" x-transition
     style="display:none"
     class="fixed inset-0 z-50 flex items-center justify-center p-6 bg-black/70 backdrop-blur-sm">
    <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-2xl max-w-sm w-full px-6 py-7 text-center">
        <div class="w-14 h-14 bg-[#0a66c2]/10 rounded-full flex items-center justify-center mx-auto mb-4">
            <i class="fas fa-rotate animate-spin text-[#0a66c2] text-2xl"></i>
        </div>
        <h3 class="text-base font-bold text-gray-900 dark:text-white mb-2">Generating Laporan…</h3>
        <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">AI sedang memproses data Anda. Ini mungkin membutuhkan beberapa menit.</p>
        {{-- Animated progress bar --}}
        <div class="w-full bg-gray-100 dark:bg-gray-700 rounded-full h-2 mb-3 overflow-hidden">
            <div class="h-2 rounded-full bg-[#0a66c2]"
                 style="animation: progress-indeterminate 1.8s ease-in-out infinite; width:60%"
                 x-bind:style="''"></div>
        </div>
        <p class="text-xs text-gray-400 dark:text-gray-500">Halaman akan otomatis di-update saat selesai</p>
    </div>
</div>

@push('styles')
<style>
@keyframes progress-indeterminate {
    0%   { transform: translateX(-100%); width: 60%; }
    50%  { transform: translateX(60%); width: 80%; }
    100% { transform: translateX(200%); width: 60%; }
}
</style>
@endpush

</div>
