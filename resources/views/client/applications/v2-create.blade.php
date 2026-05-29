{{-- Create Application — Portal v2 (3-step) --}}

{{-- ─── HERO ─── --}}
<section class="portal-hero relative overflow-hidden border-b border-[var(--border-subtle)]"
         style="background: linear-gradient(135deg, var(--client-primary) 0%, color-mix(in oklab, var(--client-primary) 55%, #001020) 100%); color:#fff;"
         aria-label="Ajukan Permohonan">
    <div class="portal-glow-orb portal-glow-orb--tr hidden lg:block" aria-hidden="true"
         style="background: radial-gradient(circle at 50% 50%, rgba(255,255,255,0.16) 0%, transparent 70%);"></div>
    <div class="relative max-w-[1400px] mx-auto px-4 lg:px-8 py-5 lg:py-7">
        <nav class="flex items-center gap-1.5 text-[11px] text-white/60 mb-3" aria-label="Breadcrumb">
            <a href="{{ route('client.services.index') }}" class="hover:text-white transition-colors">Katalog</a>
            <i class="fas fa-chevron-right text-[8px]" aria-hidden="true"></i>
            <a href="{{ route('client.services.show', $permitType->code) }}" class="hover:text-white transition-colors max-w-[200px] truncate">{{ $permitType->name }}</a>
            <i class="fas fa-chevron-right text-[8px]" aria-hidden="true"></i>
            <span class="text-white/90">Ajukan</span>
        </nav>
        <span class="portal-eyebrow" style="background: rgba(255,255,255,0.15); color: rgba(255,255,255,0.9); border-color: rgba(255,255,255,0.2);">
            <i class="fas fa-file-alt text-[9px]" aria-hidden="true"></i>
            Permohonan Baru
        </span>
        <h1 class="mt-2 text-xl font-bold text-white">{{ $permitType->name }}</h1>
        <div class="flex flex-wrap gap-4 mt-2 text-xs text-white/70">
            <span><i class="fas fa-money-bill-wave mr-1.5" aria-hidden="true"></i>
                Rp {{ number_format($permitType->estimated_cost_min, 0, ',', '.') }} – Rp {{ number_format($permitType->estimated_cost_max, 0, ',', '.') }}
            </span>
            <span><i class="fas fa-clock mr-1.5" aria-hidden="true"></i>{{ $permitType->avg_processing_days }} hari kerja</span>
        </div>
    </div>
</section>

{{-- ─── FORM ─── --}}
<div class="max-w-[860px] mx-auto px-4 lg:px-8 py-6"
     x-data="{
         step: 1,
         totalSteps: 3,
         saving: false,
         lastSaved: null,
         dirty: false,
         saveTimeout: null,
         init() {
             window.addEventListener('beforeunload', (e) => {
                 if (this.dirty) { e.preventDefault(); e.returnValue = ''; }
             });
         },
         triggerAutoSave() {
             this.dirty = true;
             clearTimeout(this.saveTimeout);
             this.saveTimeout = setTimeout(() => this.saveDraft(), 30000);
         },
         async saveDraft() {
             this.saving = true;
             try {
                 const form = document.getElementById('applicationForm');
                 const data = new FormData(form);
                 await window.apiFetch('{{ route('client.applications.store') }}', { method: 'POST', body: data });
                 this.lastSaved = Date.now();
                 this.dirty = false;
             } catch(e) {}
             this.saving = false;
         },
         advanceStep() {
             const form = document.getElementById('applicationForm');
             const invalid = form.querySelectorAll(':invalid');
             if (invalid.length) {
                 invalid[0].scrollIntoView({ behavior: 'smooth', block: 'center' });
                 invalid[0].focus();
                 form.reportValidity();
                 return;
             }
             this.step++;
             window.scrollTo({ top: 0, behavior: 'smooth' });
         }
     }"
     @input="triggerAutoSave()">

    {{-- Step indicator --}}
    <div class="mb-6">
        <div class="flex items-center justify-between mb-2">
            <span class="text-xs font-semibold text-[var(--text-tertiary)] uppercase tracking-wider">
                Langkah <span x-text="step"></span> dari 3
            </span>
            <span x-show="saving" class="text-xs text-[var(--client-primary)] flex items-center gap-1">
                <i class="fas fa-circle-notch fa-spin text-[10px]" aria-hidden="true"></i> Menyimpan…
            </span>
            <span x-show="!saving && lastSaved" class="text-xs text-[var(--text-tertiary)]" x-cloak>
                <i class="fas fa-check text-[10px] text-green-500" aria-hidden="true"></i> Tersimpan otomatis
            </span>
        </div>
        {{-- Progress bar --}}
        <div class="w-full h-1.5 bg-[var(--surface-cool)] rounded-full overflow-hidden">
            <div class="h-full bg-[var(--client-primary)] rounded-full transition-all duration-500"
                 :style="'width: ' + ((step / 3) * 100) + '%'"></div>
        </div>
        {{-- Step pills --}}
        <div class="flex items-center gap-2 mt-3">
            @foreach([['num'=>1,'label'=>'Perusahaan'],['num'=>2,'label'=>'PIC'],['num'=>3,'label'=>'Konfirmasi']] as $s)
            <div class="flex items-center gap-1.5" :class="{{ $s['num'] }} > step ? 'opacity-40' : ''">
                <div class="w-6 h-6 rounded-full flex items-center justify-center text-[10px] font-bold transition-colors"
                     :class="step >= {{ $s['num'] }} ? 'bg-[var(--client-primary)] text-white' : 'bg-[var(--surface-cool)] text-[var(--text-tertiary)] border border-[var(--border-subtle)]'">
                    <template x-if="step > {{ $s['num'] }}"><i class="fas fa-check text-[8px]"></i></template>
                    <template x-if="step <= {{ $s['num'] }}"><span>{{ $s['num'] }}</span></template>
                </div>
                <span class="text-[11px] font-semibold hidden sm:block"
                      :class="step >= {{ $s['num'] }} ? 'text-[var(--client-primary)]' : 'text-[var(--text-tertiary)]'">{{ $s['label'] }}</span>
                @if($s['num'] < 3)<div class="flex-1 h-px bg-[var(--border-subtle)] hidden sm:block" style="min-width:24px"></div>@endif
            </div>
            @endforeach
        </div>
    </div>

    <form method="POST" action="{{ route('client.applications.store') }}" id="applicationForm">
        @csrf
        <input type="hidden" name="permit_type_id" value="{{ $permitType->id }}">
        @if($draft)<input type="hidden" name="draft_id" value="{{ $draft->id }}">@endif

        {{-- Step 1: Company Information --}}
        <div x-show="step === 1" x-transition class="space-y-5">
            <div class="bg-[var(--surface-elevated)] border border-[var(--border-subtle)] rounded-xl p-5">
                <h2 class="text-sm font-bold text-[var(--text-primary)] mb-4">
                    <i class="fas fa-building text-[var(--client-primary)] mr-2" aria-hidden="true"></i>
                    Informasi Perusahaan
                </h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-semibold text-[var(--text-primary)] mb-1.5">Nama Perusahaan <span class="text-[var(--apple-red)]">*</span></label>
                        <input type="text" name="form_data[company_name]" required
                               value="{{ old('form_data.company_name', $draft->form_data['company_name'] ?? '') }}"
                               placeholder="PT. Nama Perusahaan"
                               class="w-full px-3 py-2.5 bg-[var(--surface-cool)] border border-[var(--border-subtle)] rounded-lg text-sm text-[var(--text-primary)] focus:ring-2 focus:ring-[var(--client-primary)] focus:border-[var(--client-primary)]">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-semibold text-[var(--text-primary)] mb-1.5">Alamat Perusahaan <span class="text-[var(--apple-red)]">*</span></label>
                        <textarea name="form_data[company_address]" required rows="2"
                                  placeholder="Jl. Contoh No. 1, Kota, Provinsi"
                                  class="w-full px-3 py-2.5 bg-[var(--surface-cool)] border border-[var(--border-subtle)] rounded-lg text-sm text-[var(--text-primary)] focus:ring-2 focus:ring-[var(--client-primary)] resize-none">{{ old('form_data.company_address', $draft->form_data['company_address'] ?? '') }}</textarea>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-[var(--text-primary)] mb-1.5">NPWP Perusahaan</label>
                        <input type="text" name="form_data[company_npwp]"
                               value="{{ old('form_data.company_npwp', $draft->form_data['company_npwp'] ?? '') }}"
                               placeholder="XX.XXX.XXX.X-XXX.XXX"
                               class="w-full px-3 py-2.5 bg-[var(--surface-cool)] border border-[var(--border-subtle)] rounded-lg text-sm text-[var(--text-primary)] focus:ring-2 focus:ring-[var(--client-primary)]">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-[var(--text-primary)] mb-1.5">No. Telepon Perusahaan</label>
                        <input type="tel" name="form_data[company_phone]"
                               value="{{ old('form_data.company_phone', $draft->form_data['company_phone'] ?? '') }}"
                               placeholder="+62 21 xxxxxx"
                               class="w-full px-3 py-2.5 bg-[var(--surface-cool)] border border-[var(--border-subtle)] rounded-lg text-sm text-[var(--text-primary)] focus:ring-2 focus:ring-[var(--client-primary)]">
                    </div>
                </div>
                <input type="hidden" name="kbli_code" value="{{ old('kbli_code', $draft->kbli_code ?? session('permit_selection.kbli_code', '')) }}">
                <input type="hidden" name="kbli_description" value="{{ old('kbli_description', $draft->kbli_description ?? session('permit_selection.kbli_description', '')) }}">
            </div>
        </div>

        {{-- Step 2: PIC --}}
        <div x-show="step === 2" x-transition class="space-y-5" x-cloak>
            <div class="bg-[var(--surface-elevated)] border border-[var(--border-subtle)] rounded-xl p-5">
                <h2 class="text-sm font-bold text-[var(--text-primary)] mb-4">
                    <i class="fas fa-user-tie text-[var(--client-primary)] mr-2" aria-hidden="true"></i>
                    Informasi PIC (Person in Charge)
                </h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-[var(--text-primary)] mb-1.5">Nama PIC <span class="text-[var(--apple-red)]">*</span></label>
                        <input type="text" name="form_data[pic_name]" required
                               value="{{ old('form_data.pic_name', $draft->form_data['pic_name'] ?? '') }}"
                               placeholder="Nama lengkap"
                               class="w-full px-3 py-2.5 bg-[var(--surface-cool)] border border-[var(--border-subtle)] rounded-lg text-sm text-[var(--text-primary)] focus:ring-2 focus:ring-[var(--client-primary)]">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-[var(--text-primary)] mb-1.5">Jabatan <span class="text-[var(--apple-red)]">*</span></label>
                        <input type="text" name="form_data[pic_position]" required
                               value="{{ old('form_data.pic_position', $draft->form_data['pic_position'] ?? '') }}"
                               placeholder="Direktur / Manager"
                               class="w-full px-3 py-2.5 bg-[var(--surface-cool)] border border-[var(--border-subtle)] rounded-lg text-sm text-[var(--text-primary)] focus:ring-2 focus:ring-[var(--client-primary)]">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-[var(--text-primary)] mb-1.5">Email PIC <span class="text-[var(--apple-red)]">*</span></label>
                        <input type="email" name="form_data[pic_email]" required
                               value="{{ old('form_data.pic_email', $draft->form_data['pic_email'] ?? '') }}"
                               placeholder="email@perusahaan.com"
                               class="w-full px-3 py-2.5 bg-[var(--surface-cool)] border border-[var(--border-subtle)] rounded-lg text-sm text-[var(--text-primary)] focus:ring-2 focus:ring-[var(--client-primary)]">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-[var(--text-primary)] mb-1.5">No. Telepon PIC</label>
                        <input type="tel" name="form_data[pic_phone]"
                               value="{{ old('form_data.pic_phone', $draft->form_data['pic_phone'] ?? '') }}"
                               placeholder="+62 8xx xxxx xxxx"
                               class="w-full px-3 py-2.5 bg-[var(--surface-cool)] border border-[var(--border-subtle)] rounded-lg text-sm text-[var(--text-primary)] focus:ring-2 focus:ring-[var(--client-primary)]">
                    </div>
                </div>
            </div>
        </div>

        {{-- Step 3: Catatan + Konfirmasi --}}
        <div x-show="step === 3" x-transition class="space-y-5" x-cloak>
            <div class="bg-[var(--surface-elevated)] border border-[var(--border-subtle)] rounded-xl p-5">
                <h2 class="text-sm font-bold text-[var(--text-primary)] mb-4">
                    <i class="fas fa-comment-alt text-[var(--client-primary)] mr-2" aria-hidden="true"></i>
                    Catatan Tambahan
                </h2>
                <textarea name="form_data[notes]" rows="4"
                          placeholder="Informasi tambahan yang perlu diketahui tim BizMark.ID (opsional)…"
                          class="w-full px-3 py-2.5 bg-[var(--surface-cool)] border border-[var(--border-subtle)] rounded-lg text-sm text-[var(--text-primary)] focus:ring-2 focus:ring-[var(--client-primary)] resize-none">{{ old('form_data.notes', $draft->form_data['notes'] ?? '') }}</textarea>
            </div>

            {{-- Confirmation card --}}
            <div class="bg-[var(--client-primary)]/5 border border-[var(--client-primary)]/20 rounded-xl p-5 space-y-3">
                <h3 class="text-sm font-bold text-[var(--text-primary)]">
                    <i class="fas fa-shield-check text-[var(--client-primary)] mr-2" aria-hidden="true"></i>
                    Konfirmasi Permohonan
                </h3>
                <p class="text-xs text-[var(--text-secondary)]">
                    Dengan mengajukan permohonan ini, Anda setuju dengan syarat dan ketentuan layanan BizMark.ID.
                    Tim kami akan meninjau permohonan dan menghubungi PIC dalam 1x24 jam kerja.
                </p>
                <label class="flex items-start gap-3 cursor-pointer">
                    <input type="checkbox" name="confirm_tos" value="1" required
                           class="mt-0.5 w-4 h-4 accent-[var(--client-primary)] flex-shrink-0">
                    <span class="text-xs text-[var(--text-secondary)]">
                        Saya telah membaca dan menyetujui
                        <a href="{{ route('client.applications.preview-submit', $permitType->id) }}" target="_blank"
                           class="text-[var(--client-primary)] hover:underline font-semibold">Syarat &amp; Ketentuan</a>
                        layanan BizMark.ID.
                    </span>
                </label>
            </div>
        </div>

        {{-- Navigation --}}
        <div class="flex items-center justify-between mt-6 gap-3">
            {{-- Back --}}
            <div>
                <template x-if="step === 1">
                    <a href="{{ route('client.services.show', $permitType->code) }}"
                       class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-semibold text-[var(--text-secondary)] bg-[var(--surface-cool)] border border-[var(--border-subtle)] rounded-xl hover:border-[var(--border-subtle)] transition-colors">
                        <i class="fas fa-arrow-left text-xs" aria-hidden="true"></i> Kembali
                    </a>
                </template>
                <template x-if="step > 1">
                    <button type="button" @click="step--"
                            class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-semibold text-[var(--text-secondary)] bg-[var(--surface-cool)] border border-[var(--border-subtle)] rounded-xl hover:border-[var(--client-primary)]/40 transition-colors">
                        <i class="fas fa-arrow-left text-xs" aria-hidden="true"></i> Kembali
                    </button>
                </template>
            </div>

            {{-- Next / Submit --}}
            <div class="flex items-center gap-3">
                <template x-if="step < 3">
                    <button type="button" @click="advanceStep()"
                            class="inline-flex items-center gap-2 px-5 py-2.5 bg-[var(--client-primary)] text-white text-sm font-semibold rounded-xl hover:brightness-110 transition-all">
                        Lanjut <i class="fas fa-arrow-right text-xs" aria-hidden="true"></i>
                    </button>
                </template>
                <template x-if="step === 3">
                    <div class="flex items-center gap-3">
                        <button type="submit" name="save_as_draft" value="1"
                                class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-semibold text-[var(--text-secondary)] bg-[var(--surface-cool)] border border-[var(--border-subtle)] rounded-xl hover:border-[var(--client-primary)]/40 transition-colors">
                            <i class="fas fa-save text-xs" aria-hidden="true"></i> Simpan Draft
                        </button>
                        <button type="submit"
                                class="inline-flex items-center gap-2 px-5 py-2.5 bg-[var(--client-primary)] text-white text-sm font-semibold rounded-xl hover:brightness-110 transition-all">
                            <i class="fas fa-paper-plane text-xs" aria-hidden="true"></i> Ajukan Permohonan
                        </button>
                    </div>
                </template>
            </div>
        </div>
    </form>
</div>
