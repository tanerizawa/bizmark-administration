@php
    $formData = is_array($application->form_data)
        ? $application->form_data
        : (json_decode($application->form_data, true) ?? []);
    $permitName = $application->permitType?->name ?? 'Permohonan';
@endphp

{{-- ══════════════════════════════════════════════════════════
     HERO
══════════════════════════════════════════════════════════ --}}
<section class="portal-hero relative overflow-hidden border-b border-[var(--border-subtle)]"
         style="background: linear-gradient(135deg, #92400e 0%, color-mix(in oklab, #92400e 55%, #001020) 100%); color:#fff;">
    <div class="portal-glow-orb portal-glow-orb--tr hidden lg:block" aria-hidden="true"
         style="background: radial-gradient(circle at 50% 50%, rgba(255,255,255,0.13) 0%, transparent 70%);"></div>
    <div class="max-w-[1400px] mx-auto px-4 lg:px-8 py-5 relative z-10">
        {{-- Breadcrumb --}}
        <nav class="flex items-center gap-1.5 text-[11px] mb-3 flex-wrap" aria-label="Breadcrumb">
            <a href="{{ route('client.applications.index') }}" class="opacity-70 hover:opacity-100 transition-opacity">Permohonan</a>
            <i class="fas fa-chevron-right text-[9px] opacity-50"></i>
            <a href="{{ route('client.applications.show', $application->id) }}" class="opacity-70 hover:opacity-100 transition-opacity">{{ $application->application_number }}</a>
            <i class="fas fa-chevron-right text-[9px] opacity-50"></i>
            <span class="opacity-90 font-semibold">Edit</span>
        </nav>

        <div class="flex items-start gap-4">
            <div class="hidden sm:flex w-12 h-12 rounded-xl items-center justify-center flex-shrink-0"
                 style="background: rgba(255,255,255,0.15); border: 1px solid rgba(255,255,255,0.2);">
                <i class="fas fa-file-pen text-lg" aria-hidden="true"></i>
            </div>
            <div>
                <div class="flex items-center gap-2 flex-wrap mb-1">
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 text-[10px] font-semibold rounded-full"
                          style="background: rgba(255,255,255,0.15); color: rgba(255,255,255,0.9); border: 1px solid rgba(255,255,255,0.2);">
                        <i class="fas fa-pencil text-[8px]"></i>
                        Draft — Belum Diajukan
                    </span>
                </div>
                <h1 class="text-xl sm:text-2xl font-bold leading-snug">Edit Permohonan</h1>
                <p class="text-sm opacity-80 mt-0.5">{{ $application->application_number }} &mdash; {{ $permitName }}</p>
            </div>
        </div>
    </div>
</section>

{{-- ══════════════════════════════════════════════════════════
     FORM BODY
══════════════════════════════════════════════════════════ --}}
<div class="max-w-[1400px] mx-auto px-4 lg:px-8 py-6"
     x-data="{
        step: 1,
        totalSteps: 3,
        nextStep() { if (this.step < this.totalSteps) this.step++; },
        prevStep() { if (this.step > 1) this.step--; },
     }">

    @if(session('error'))
    <div class="flex items-start gap-2 mb-5 px-4 py-3 rounded-xl text-sm"
         style="background: rgba(239,68,68,0.08); border: 1px solid rgba(239,68,68,0.2); color: #b91c1c;">
        <i class="fas fa-circle-xmark mt-0.5 flex-shrink-0"></i>
        <p>{{ session('error') }}</p>
    </div>
    @endif

    @if($errors->any())
    <div class="flex items-start gap-2 mb-5 px-4 py-3 rounded-xl text-sm"
         style="background: rgba(239,68,68,0.08); border: 1px solid rgba(239,68,68,0.2); color: #b91c1c;">
        <i class="fas fa-circle-xmark mt-0.5 flex-shrink-0"></i>
        <p>{{ $errors->first() }}</p>
    </div>
    @endif

    {{-- Info banner --}}
    <div class="mb-5 flex items-start gap-3 px-4 py-3 rounded-xl border"
         style="background: rgba(10,102,194,0.06); border-color: rgba(10,102,194,0.18);">
        <i class="fas fa-circle-info text-sm mt-0.5 flex-shrink-0" style="color: var(--client-primary);"></i>
        <p class="text-sm" style="color: var(--text-secondary);">
            Anda dapat mengubah data permohonan ini selama masih berstatus <strong style="color: var(--text-primary);">Draft</strong>.
            Simpan sebagai draft atau langsung ajukan setelah selesai.
        </p>
    </div>

    {{-- Step progress bar --}}
    <div class="mb-6">
        <div class="flex items-center gap-2 mb-2 text-xs font-semibold" style="color: var(--text-secondary);">
            <template x-for="i in totalSteps" :key="i">
                <div class="flex items-center gap-2">
                    <div class="flex items-center gap-1.5">
                        <div class="w-6 h-6 rounded-full flex items-center justify-center text-[10px] font-bold transition-all"
                             :class="step >= i ? 'text-white' : ''"
                             :style="step >= i
                                ? 'background: var(--client-primary); border: 2px solid var(--client-primary);'
                                : 'background: var(--surface-cool); border: 2px solid var(--border-subtle); color: var(--text-tertiary);'">
                            <template x-if="step > i"><i class="fas fa-check text-[8px]"></i></template>
                            <template x-if="step <= i"><span x-text="i"></span></template>
                        </div>
                        <span x-text="['Perusahaan', 'PIC', 'Konfirmasi'][i-1]"
                              :style="step === i ? 'color: var(--client-primary); font-weight: 600;' : ''"></span>
                    </div>
                    <template x-if="i < totalSteps">
                        <div class="flex-1 h-px w-8" style="background: var(--border-subtle);"></div>
                    </template>
                </div>
            </template>
        </div>
        <div class="h-1 rounded-full overflow-hidden" style="background: var(--surface-cool);">
            <div class="h-full rounded-full transition-all duration-500"
                 style="background: var(--client-primary);"
                 :style="`width: ${(step / totalSteps) * 100}%`"></div>
        </div>
    </div>

    <form method="POST" action="{{ route('client.applications.update', $application->id) }}" id="editForm">
        @csrf
        @method('PUT')

        {{-- ───────────────────── STEP 1: PERUSAHAAN ───────────────────── --}}
        <div x-show="step === 1" x-cloak>
            <div class="rounded-2xl border p-5 sm:p-6 mb-4"
                 style="background: var(--surface-elevated); border-color: var(--border-subtle);">
                <h2 class="text-base font-bold mb-5 flex items-center gap-2" style="color: var(--text-primary);">
                    <i class="fas fa-building text-sm" style="color: var(--client-primary);"></i>
                    Informasi Perusahaan
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    {{-- Company Name --}}
                    <div class="md:col-span-2">
                        <label class="block text-xs font-semibold mb-1.5" style="color: var(--text-secondary);">
                            Nama Perusahaan <span style="color: var(--apple-red)">*</span>
                        </label>
                        <input type="text" name="form_data[company_name]"
                               value="{{ old('form_data.company_name', $formData['company_name'] ?? auth('client')->user()->company_name) }}"
                               required autocomplete="organization"
                               class="w-full px-3 py-2.5 text-sm rounded-lg border transition-all focus:outline-none"
                               style="background: var(--surface-cool); border-color: var(--border-subtle); color: var(--text-primary);"
                               onfocus="this.style.borderColor='var(--client-primary)'; this.style.boxShadow='0 0 0 3px rgba(10,102,194,0.12)';"
                               onblur="this.style.borderColor=''; this.style.boxShadow='';">
                        @error('form_data.company_name')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                    </div>

                    {{-- Company Address --}}
                    <div class="md:col-span-2">
                        <label class="block text-xs font-semibold mb-1.5" style="color: var(--text-secondary);">
                            Alamat Perusahaan <span style="color: var(--apple-red)">*</span>
                        </label>
                        <textarea name="form_data[company_address]" rows="3" required autocomplete="street-address"
                                  class="w-full px-3 py-2.5 text-sm rounded-lg border transition-all focus:outline-none resize-none"
                                  style="background: var(--surface-cool); border-color: var(--border-subtle); color: var(--text-primary);"
                                  onfocus="this.style.borderColor='var(--client-primary)'; this.style.boxShadow='0 0 0 3px rgba(10,102,194,0.12)';"
                                  onblur="this.style.borderColor=''; this.style.boxShadow='';">{{ old('form_data.company_address', $formData['company_address'] ?? auth('client')->user()->address) }}</textarea>
                        @error('form_data.company_address')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                    </div>

                    {{-- NPWP --}}
                    <div>
                        <label class="block text-xs font-semibold mb-1.5" style="color: var(--text-secondary);">
                            NPWP Perusahaan <span style="color: var(--apple-red)">*</span>
                        </label>
                        <input type="text" name="form_data[company_npwp]"
                               value="{{ old('form_data.company_npwp', $formData['company_npwp'] ?? '') }}"
                               placeholder="00.000.000.0-000.000" required inputmode="numeric" autocomplete="off"
                               class="w-full px-3 py-2.5 text-sm rounded-lg border transition-all focus:outline-none"
                               style="background: var(--surface-cool); border-color: var(--border-subtle); color: var(--text-primary);"
                               onfocus="this.style.borderColor='var(--client-primary)'; this.style.boxShadow='0 0 0 3px rgba(10,102,194,0.12)';"
                               onblur="this.style.borderColor=''; this.style.boxShadow='';">
                    </div>

                    {{-- Company Phone --}}
                    <div>
                        <label class="block text-xs font-semibold mb-1.5" style="color: var(--text-secondary);">
                            No. Telepon Perusahaan <span style="color: var(--apple-red)">*</span>
                        </label>
                        <input type="tel" name="form_data[company_phone]"
                               value="{{ old('form_data.company_phone', $formData['company_phone'] ?? auth('client')->user()->phone) }}"
                               required inputmode="tel" autocomplete="tel"
                               class="w-full px-3 py-2.5 text-sm rounded-lg border transition-all focus:outline-none"
                               style="background: var(--surface-cool); border-color: var(--border-subtle); color: var(--text-primary);"
                               onfocus="this.style.borderColor='var(--client-primary)'; this.style.boxShadow='0 0 0 3px rgba(10,102,194,0.12)';"
                               onblur="this.style.borderColor=''; this.style.boxShadow='';">
                    </div>

                    {{-- KBLI Search --}}
                    <div class="md:col-span-2">
                        <label class="block text-xs font-semibold mb-1.5" style="color: var(--text-secondary);">
                            Kode KBLI
                            <span class="font-normal" style="color: var(--text-tertiary);">— Opsional</span>
                        </label>
                        <div class="relative">
                            <input type="text" id="kbli_search"
                                   placeholder="Ketik untuk mencari KBLI (min. 2 karakter)…"
                                   autocomplete="off"
                                   class="w-full px-3 py-2.5 text-sm rounded-lg border transition-all focus:outline-none"
                                   style="background: var(--surface-cool); border-color: var(--border-subtle); color: var(--text-primary);"
                                   onfocus="this.style.borderColor='var(--client-primary)'; this.style.boxShadow='0 0 0 3px rgba(10,102,194,0.12)';"
                                   onblur="this.style.borderColor=''; this.style.boxShadow='';">
                            <input type="hidden" name="kbli_code" id="kbli_code" value="{{ old('kbli_code', $application->kbli_code ?? '') }}">
                            <input type="hidden" name="kbli_description" id="kbli_description" value="{{ old('kbli_description', $application->kbli_description ?? '') }}">
                            <div id="kbli_loading" class="hidden absolute right-3 top-2.5">
                                <i class="fas fa-circle-notch fa-spin text-xs" style="color: var(--client-primary);"></i>
                            </div>
                            <div id="kbli_dropdown" class="hidden absolute z-20 w-full mt-1 rounded-xl shadow-xl overflow-hidden border"
                                 style="background: var(--surface-elevated); border-color: var(--border-subtle); max-height: 240px; overflow-y: auto;"></div>
                        </div>
                        <div id="kbli_selected" class="hidden mt-2 flex items-start justify-between gap-2 px-3 py-2.5 rounded-lg border"
                             style="background: rgba(10,102,194,0.06); border-color: rgba(10,102,194,0.18);">
                            <div class="flex-1 min-w-0">
                                <span class="text-xs font-bold" id="selected_code" style="color: var(--client-primary);"></span>
                                <p class="text-xs mt-0.5" id="selected_description" style="color: var(--text-secondary);"></p>
                            </div>
                            <button type="button" onclick="clearKBLI()" class="text-xs flex-shrink-0 hover:opacity-70" style="color: var(--apple-red);">
                                <i class="fas fa-xmark"></i>
                            </button>
                        </div>
                        <p class="mt-1 text-xs" style="color: var(--text-tertiary);">
                            <i class="fas fa-circle-info mr-1"></i>
                            KBLI membantu kami memahami bidang usaha Anda sesuai standar OSS
                        </p>
                    </div>
                </div>
            </div>
            <div class="flex justify-end">
                <button type="button" @click="nextStep()"
                        class="flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-semibold text-white transition-all hover:opacity-90"
                        style="background: var(--client-primary);">
                    Lanjut ke PIC <i class="fas fa-arrow-right text-xs"></i>
                </button>
            </div>
        </div>

        {{-- ───────────────────── STEP 2: PIC ───────────────────── --}}
        <div x-show="step === 2" x-cloak>
            <div class="rounded-2xl border p-5 sm:p-6 mb-4"
                 style="background: var(--surface-elevated); border-color: var(--border-subtle);">
                <h2 class="text-base font-bold mb-5 flex items-center gap-2" style="color: var(--text-primary);">
                    <i class="fas fa-user text-sm" style="color: var(--client-primary);"></i>
                    Penanggung Jawab (PIC)
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach([
                        ['form_data[pic_name]', 'pic_name', 'Nama Lengkap', 'text', 'name', auth('client')->user()->name],
                        ['form_data[pic_position]', 'pic_position', 'Jabatan', 'text', 'organization-title', ''],
                        ['form_data[pic_email]', 'pic_email', 'Email PIC', 'email', 'email', auth('client')->user()->email],
                        ['form_data[pic_phone]', 'pic_phone', 'No. HP PIC', 'tel', 'tel', auth('client')->user()->phone],
                    ] as [$name, $key, $label, $type, $auto, $default])
                    <div>
                        <label class="block text-xs font-semibold mb-1.5" style="color: var(--text-secondary);">
                            {{ $label }} <span style="color: var(--apple-red)">*</span>
                        </label>
                        <input type="{{ $type }}" name="{{ $name }}"
                               value="{{ old($name, $formData[$key] ?? $default) }}"
                               required autocomplete="{{ $auto }}"
                               class="w-full px-3 py-2.5 text-sm rounded-lg border transition-all focus:outline-none"
                               style="background: var(--surface-cool); border-color: var(--border-subtle); color: var(--text-primary);"
                               onfocus="this.style.borderColor='var(--client-primary)'; this.style.boxShadow='0 0 0 3px rgba(10,102,194,0.12)';"
                               onblur="this.style.borderColor=''; this.style.boxShadow='';">
                    </div>
                    @endforeach
                </div>
            </div>
            <div class="flex justify-between">
                <button type="button" @click="prevStep()"
                        class="flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-semibold transition-all"
                        style="background: var(--surface-cool); border: 1px solid var(--border-subtle); color: var(--text-secondary);">
                    <i class="fas fa-arrow-left text-xs"></i> Kembali
                </button>
                <button type="button" @click="nextStep()"
                        class="flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-semibold text-white transition-all hover:opacity-90"
                        style="background: var(--client-primary);">
                    Lanjut ke Konfirmasi <i class="fas fa-arrow-right text-xs"></i>
                </button>
            </div>
        </div>

        {{-- ───────────────────── STEP 3: KONFIRMASI ───────────────────── --}}
        <div x-show="step === 3" x-cloak>
            <div class="rounded-2xl border p-5 sm:p-6 mb-4"
                 style="background: var(--surface-elevated); border-color: var(--border-subtle);">
                <h2 class="text-base font-bold mb-5 flex items-center gap-2" style="color: var(--text-primary);">
                    <i class="fas fa-note-sticky text-sm" style="color: var(--client-primary);"></i>
                    Catatan Tambahan
                </h2>
                <textarea name="form_data[notes]" rows="4"
                          placeholder="Tambahkan catatan atau informasi khusus…"
                          class="w-full px-3 py-2.5 text-sm rounded-lg border transition-all focus:outline-none resize-none"
                          style="background: var(--surface-cool); border-color: var(--border-subtle); color: var(--text-primary);"
                          onfocus="this.style.borderColor='var(--client-primary)'; this.style.boxShadow='0 0 0 3px rgba(10,102,194,0.12)';"
                          onblur="this.style.borderColor=''; this.style.boxShadow='';">{{ old('form_data.notes', $formData['notes'] ?? '') }}</textarea>
            </div>

            <div class="flex flex-col sm:flex-row gap-3 justify-between">
                <button type="button" @click="prevStep()"
                        class="flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl text-sm font-semibold transition-all"
                        style="background: var(--surface-cool); border: 1px solid var(--border-subtle); color: var(--text-secondary);">
                    <i class="fas fa-arrow-left text-xs"></i> Kembali
                </button>
                <div class="flex flex-col sm:flex-row gap-3">
                    <a href="{{ route('client.applications.show', $application->id) }}"
                       class="flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl text-sm font-semibold transition-all"
                       style="background: var(--surface-cool); border: 1px solid var(--border-subtle); color: var(--text-secondary);">
                        <i class="fas fa-xmark text-xs"></i> Batal
                    </a>
                    <button type="submit" name="save_as_draft" value="1"
                            class="flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl text-sm font-semibold text-white transition-all hover:opacity-90"
                            style="background: #475569;">
                        <i class="fas fa-floppy-disk text-xs"></i> Simpan sebagai Draft
                    </button>
                    <button type="submit"
                            class="flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl text-sm font-semibold text-white transition-all hover:opacity-90"
                            style="background: var(--client-primary);">
                        <i class="fas fa-paper-plane text-xs"></i>
                        <span class="hidden sm:inline">Simpan &amp; Ajukan</span>
                        <span class="sm:hidden">Ajukan</span>
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
(function () {
    let _timer = null;

    // Restore KBLI if already set
    document.addEventListener('DOMContentLoaded', function () {
        const code = document.getElementById('kbli_code').value;
        if (code) {
            showSelectedKBLI({ code, description: document.getElementById('kbli_description').value });
        }
    });

    document.getElementById('kbli_search').addEventListener('input', function (e) {
        const q = e.target.value.trim();
        if (_timer) clearTimeout(_timer);
        document.getElementById('kbli_dropdown').classList.add('hidden');
        if (q.length < 2) { document.getElementById('kbli_loading').classList.add('hidden'); return; }
        document.getElementById('kbli_loading').classList.remove('hidden');
        _timer = setTimeout(() => searchKBLI(q), 300);
    });

    document.addEventListener('click', function (e) {
        if (!e.target.closest('#kbli_search') && !e.target.closest('#kbli_dropdown')) {
            document.getElementById('kbli_dropdown').classList.add('hidden');
        }
    });

    window.searchKBLI = function (q) {
        fetch('/api/kbli/search?q=' + encodeURIComponent(q))
            .then(r => r.json())
            .then(data => {
                document.getElementById('kbli_loading').classList.add('hidden');
                data.success && data.data.length > 0 ? renderKBLI(data.data) : renderEmpty();
            })
            .catch(() => { document.getElementById('kbli_loading').classList.add('hidden'); renderEmpty(); });
    };

    window.renderKBLI = function (items) {
        const dd = document.getElementById('kbli_dropdown');
        dd.innerHTML = items.map(item =>
            `<button type="button" onclick='selectKBLI(${JSON.stringify(item)})'
                class="w-full px-3 py-2.5 text-left text-xs border-b transition-colors hover:opacity-80"
                style="border-color: var(--border-subtle);">
                <span class="font-bold block" style="color: var(--client-primary);">${item.code}</span>
                <span class="block mt-0.5 line-clamp-2" style="color: var(--text-secondary);">${item.description}</span>
                <span class="block mt-0.5" style="color: var(--text-tertiary);">Sektor: ${item.sector}</span>
            </button>`
        ).join('');
        dd.classList.remove('hidden');
    };

    window.renderEmpty = function () {
        const dd = document.getElementById('kbli_dropdown');
        dd.innerHTML = '<div class="px-3 py-3 text-xs" style="color: var(--text-tertiary);">Tidak ditemukan hasil</div>';
        dd.classList.remove('hidden');
    };

    window.selectKBLI = function (item) {
        document.getElementById('kbli_code').value = item.code;
        document.getElementById('kbli_description').value = item.description;
        showSelectedKBLI(item);
        document.getElementById('kbli_dropdown').classList.add('hidden');
        document.getElementById('kbli_search').value = '';
    };

    window.showSelectedKBLI = function (item) {
        document.getElementById('selected_code').textContent = item.code;
        document.getElementById('selected_description').textContent = item.description;
        document.getElementById('kbli_selected').classList.remove('hidden');
    };

    window.clearKBLI = function () {
        document.getElementById('kbli_code').value = '';
        document.getElementById('kbli_description').value = '';
        document.getElementById('kbli_search').value = '';
        document.getElementById('kbli_selected').classList.add('hidden');
    };
})();
</script>
@endpush
